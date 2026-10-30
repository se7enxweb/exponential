<?php
/**
 * File containing the expModuleTableCode class: the code the module table wizard writes.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

if ( !class_exists( 'expModuleTableCode', false ) ) {
/**
 * The files of a module table (expModuleTableWizard): the persistent class, the module with its list, edit, remove and
 * JSON views and its policies, the admin templates, the fetch functions and the settings. Pure: from the wizard's
 * settings and the resolved fields to text.
 *
 * The settings it reads: name (extension), module, class, table, title, connection ('' for the site's database),
 * source_label, and the fields: name, label, type, length, required, unique.
 *
 * @since 6.0.15
 * @package kernel
 */
class expModuleTableCode
{
    /** The translation context of the texts every generated module shares; titles and field labels keep
        extension/<name> */
    const CONTEXT = 'kernel/datatable/module';

    /**
     * The persistent class's definition() for the fields (also what the wizard shows and the schema is made from).
     *
     * @param array $s settings
     * @param array $fields
     * @return array
     */
    public static function definition( array $s, array $fields )
    {
        $defs = array( 'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ) );
        $relations = array();
        $unique = array();
        foreach ( $fields as $f )
        {
            if ( $f['name'] === 'id' )
                continue;
            $defs[$f['name']] = array( 'name' => self::camel( $f['name'] ), 'datatype' => self::datatype( $f['type'] ),
                                       'default' => self::defaultOf( $f['type'] ), 'required' => (bool)$f['required'] );
            $target = self::relationTarget( $f['type'] );
            if ( $target )
                $relations[$f['name']] = $target;
            if ( $f['unique'] )
                $unique[substr( $s['table'] . '_' . $f['name'], 0, 30 )] = array( $f['name'] );
        }
        $definition = array( 'fields' => $defs, 'keys' => array( 'id' ), 'increment_key' => 'id', 'class_name' => $s['class'],
                             'sort' => array( 'id' => 'desc' ), 'name' => $s['table'] );
        if ( $s['connection'] !== '' )
            $definition['connection'] = $s['connection'];
        if ( $relations )
            $definition['relations'] = $relations;
        if ( $unique )
            $definition['unique'] = $unique;
        return $definition;
    }

    /**
     * The lengths of the string fields, for the schema.
     *
     * @param array $fields
     * @return array name => length
     */
    public static function lengths( array $fields )
    {
        $lengths = array();
        foreach ( $fields as $f )
            if ( $f['type'] === 'string' )
                $lengths[$f['name']] = $f['length'] ?: 255;
        return $lengths;
    }

    /** @return string the kernel datatype of a field type */
    public static function datatype( $type )
    {
        switch ( $type )
        {
            case 'float':
                return 'float';
            case 'text':
            case 'json':
                return 'text';
            case 'string':
                return 'string';
        }
        return 'integer';
    }

    /** @return mixed the default of a field type */
    public static function defaultOf( $type )
    {
        switch ( self::datatype( $type ) )
        {
            case 'integer':
                return 0;
            case 'float':
                return 0.0;
        }
        return '';
    }

    /** @return array|null the relation a field type declares */
    public static function relationTarget( $type )
    {
        switch ( $type )
        {
            case 'object':
                return array( 'class' => 'eZContentObject', 'field' => 'id' );
            case 'node':
                return array( 'class' => 'eZContentObjectTreeNode', 'field' => 'node_id' );
            case 'user':
                return array( 'class' => 'eZUser', 'field' => 'contentobject_id' );
        }
        return null;
    }

    /** @return string FieldName from field_name */
    public static function camel( $name )
    {
        return str_replace( ' ', '', ucwords( str_replace( '_', ' ', (string)$name ) ) );
    }

    /**
     * The persistent class.
     *
     * @param array $s
     * @param array $fields
     * @param string $header the file header (licence and origin)
     * @return string
     */
    public static function persistentClass( array $s, array $fields, $header )
    {
        $definition = self::definition( $s, $fields );
        $meta = array();
        foreach ( $fields as $f )
            $meta[$f['name']] = array( 'label' => $f['label'], 'type' => $f['type'], 'length' => (int)$f['length'], 'required' => (bool)$f['required'], 'unique' => (bool)$f['unique'] );
        $functionAttributes = array();
        foreach ( isset( $definition['relations'] ) ? array_keys( $definition['relations'] ) : array() as $field )
            $functionAttributes[$field . '_target'] = lcfirst( self::camel( $field ) ) . 'Target';
        $definition['function_attributes'] = $functionAttributes + array( 'as_array' => 'toArray' );
        $relationMethods = '';
        foreach ( $functionAttributes as $attribute => $method )
        {
            $field = substr( $attribute, 0, -7 );
            $relationMethods .= "    /** @return object|null what $field points at */\n"
                              . "    public function $method()\n    {\n        return \$this->target( '$field' );\n    }\n\n";
        }
        $c = $s['class'];
        return "<?php\n/**\n * $c: one row of the table {$s['table']}" . ( $s['connection'] !== '' ? " on the data table connection {$s['connection']}" : '' ) . ".\n"
             . " * Made by the module table wizard from {$s['source_label']}.\n *\n" . $header . " */\n\n"
             . "class $c extends eZPersistentObject\n{\n"
             . "    const TABLE = '" . $s['table'] . "';\n\n"
             . "    public static function definition()\n    {\n        return " . self::export( $definition, 2 ) . ";\n    }\n\n"
             . "    /**\n     * The fields as the wizard made them: label, type (string, text, integer, float, boolean, date, datetime,\n"
             . "     * json, object, node, user), length, required, unique. The forms and the JSON endpoint read them.\n     *\n     * @return array\n     */\n"
             . "    public static function fields()\n    {\n        return " . self::export( $meta, 2 ) . ";\n    }\n\n"
             . "    /**\n     * @param array \$row\n     * @return $c\n     */\n"
             . "    public static function create( array \$row = array() )\n    {\n"
             . "        \$defaults = array();\n        foreach ( self::definition()['fields'] as \$name => \$field )\n            \$defaults[\$name] = \$field['default'];\n"
             . "        \$defaults['id'] = null;\n        return new self( \$row + \$defaults );\n    }\n\n"
             . "    /**\n     * @param int \$id\n     * @param bool \$asObject\n     * @return $c|array|null\n     */\n"
             . "    public static function fetch( \$id, \$asObject = true )\n    {\n"
             . "        return eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)\$id ), \$asObject );\n    }\n\n"
             . "    /**\n     * @param array|null \$conditions\n     * @param int \$offset\n     * @param int \$limit\n     * @param array|null \$sorts\n     * @return {$c}[]\n     */\n"
             . "    public static function fetchList( \$conditions = null, \$offset = 0, \$limit = 50, \$sorts = null )\n    {\n"
             . "        \$list = eZPersistentObject::fetchObjectList( self::definition(), null, \$conditions, \$sorts, array( 'offset' => (int)\$offset, 'length' => (int)\$limit ) );\n"
             . "        return is_array( \$list ) ? \$list : array();\n    }\n\n"
             . "    /**\n     * @param array|null \$conditions\n     * @return int\n     */\n"
             . "    public static function fetchListCount( \$conditions = null )\n    {\n"
             . "        return (int)eZPersistentObject::count( self::definition(), \$conditions );\n    }\n\n"
             . "    /**\n     * What a relation field points at (a content object, a node, a user), or null.\n     *\n     * @param string \$field\n     * @return object|null\n     */\n"
             . "    public function target( \$field )\n    {\n"
             . "        \$relations = isset( self::definition()['relations'] ) ? self::definition()['relations'] : array();\n"
             . "        if ( !isset( \$relations[\$field] ) || !class_exists( 'expDataTableRelation' ) )\n            return null;\n"
             . "        \$relation = array( 'column' => \$field, 'kind' => expDataTableCatalogue::kindOfClass( \$relations[\$field]['class'] ),\n"
             . "                           'class' => \$relations[\$field]['class'], 'field' => \$relations[\$field]['field'] );\n"
             . "        return expDataTableRelation::target( \$relation, \$this->attribute( \$field ) );\n    }\n\n"
             . $relationMethods
             . "    /**\n     * The row as plain values, typed by the fields (for the JSON endpoint and templates).\n     *\n     * @return array\n     */\n"
             . "    public function toArray()\n    {\n"
             . "        \$row = array( 'id' => (int)\$this->attribute( 'id' ) );\n"
             . "        foreach ( self::fields() as \$name => \$field )\n        {\n"
             . "            \$value = \$this->attribute( \$name );\n"
             . "            switch ( \$field['type'] )\n            {\n"
             . "                case 'integer': case 'date': case 'datetime': case 'object': case 'node': case 'user':\n                    \$value = (int)\$value;\n                    break;\n"
             . "                case 'float':\n                    \$value = (float)\$value;\n                    break;\n"
             . "                case 'boolean':\n                    \$value = (bool)\$value;\n                    break;\n"
             . "                case 'json':\n                    \$decoded = json_decode( (string)\$value, true );\n                    \$value = \$decoded === null && (string)\$value !== 'null' ? (string)\$value : \$decoded;\n                    break;\n"
             . "                default:\n                    \$value = (string)\$value;\n            }\n"
             . "            \$row[\$name] = \$value;\n        }\n        return \$row;\n    }\n\n"
             . "    /**\n     * Sets the fields from a form or a JSON body, typed, and says what is wrong.\n     *\n     * @param array \$input field => value (only the fields given are set)\n     * @return string[] the problems, empty when the row can be stored\n     */\n"
             . "    public function fromInput( array \$input )\n    {\n"
             . "        \$problems = array();\n"
             . "        foreach ( self::fields() as \$name => \$field )\n        {\n"
             . "            if ( !array_key_exists( \$name, \$input ) )\n                continue;\n"
             . "            \$value = \$input[\$name];\n"
             . "            switch ( \$field['type'] )\n            {\n"
             . "                case 'integer': case 'object': case 'node': case 'user':\n"
             . "                    if ( \$value !== '' && \$value !== null && !is_numeric( \$value ) )\n                        \$problems[] = \$field['label'] . ': a whole number';\n"
             . "                    \$value = (int)\$value;\n                    break;\n"
             . "                case 'float':\n"
             . "                    if ( \$value !== '' && \$value !== null && !is_numeric( \$value ) )\n                        \$problems[] = \$field['label'] . ': a number';\n"
             . "                    \$value = (float)\$value;\n                    break;\n"
             . "                case 'boolean':\n                    \$value = !empty( \$value ) && \$value !== 'false' ? 1 : 0;\n                    break;\n"
             . "                case 'date': case 'datetime':\n"
             . "                    \$value = is_numeric( \$value ) ? (int)\$value : ( trim( (string)\$value ) === '' ? 0 : (int)strtotime( (string)\$value ) );\n                    break;\n"
             . "                case 'json':\n"
             . "                    if ( !is_string( \$value ) )\n                        \$value = json_encode( \$value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );\n"
             . "                    elseif ( trim( \$value ) !== '' && json_decode( \$value ) === null && trim( \$value ) !== 'null' )\n                        \$problems[] = \$field['label'] . ': JSON';\n"
             . "                    break;\n"
             . "                default:\n                    \$value = is_scalar( \$value ) ? (string)\$value : '';\n"
             . "                    if ( \$field['length'] && mb_strlen( \$value ) > \$field['length'] )\n                        \$problems[] = \$field['label'] . ': at most ' . \$field['length'] . ' characters';\n"
             . "            }\n"
             . "            \$this->setAttribute( \$name, \$value );\n        }\n"
             . "        foreach ( self::fields() as \$name => \$field )\n        {\n"
             . "            \$value = \$this->attribute( \$name );\n"
             . "            if ( \$field['required'] && ( \$value === '' || \$value === null || ( in_array( \$field['type'], array( 'object', 'node', 'user' ), true ) && !\$value ) ) )\n"
             . "                \$problems[] = \$field['label'] . ': required';\n"
             . "            if ( \$field['unique'] && \$value !== '' && \$value !== null )\n            {\n"
             . "                \$other = eZPersistentObject::fetchObject( self::definition(), null, array( \$name => \$value ) );\n"
             . "                if ( \$other && (int)\$other->attribute( 'id' ) !== (int)\$this->attribute( 'id' ) )\n                    \$problems[] = \$field['label'] . ': another row has this value';\n"
             . "            }\n        }\n"
             . "        return \$problems;\n    }\n}\n";
    }

    /**
     * module.php.
     *
     * @param array $s
     * @return string
     */
    public static function moduleDefinition( array $s )
    {
        $nav = 'ezsetupnavigationpart';
        $view = function ( $name, $functions, $params, $unordered = array() ) use ( $nav ) {
            return "\$ViewList['$name'] = array(\n    'script' => '$name.php',\n    'functions' => array( " . implode( ', ', array_map( function ( $f ) { return "'$f'"; }, $functions ) ) . " ),\n"
                 . "    'default_navigation_part' => '$nav',\n    'params' => array( " . implode( ', ', array_map( function ( $p ) { return "'$p'"; }, $params ) ) . " )"
                 . ( $unordered ? ",\n    'unordered_params' => array( " . implode( ', ', array_map( function ( $k, $v ) { return "'$k' => '$v'"; }, array_keys( $unordered ), $unordered ) ) . " )" : '' )
                 . " );\n\n";
        };
        return "<?php\n/**\n * The module {$s['module']}: the rows of the table {$s['table']} ({$s['class']}).\n *\n"
             . " *   list     the rows, 25 a page, searched and sorted (policy read)\n"
             . " *   edit     a new row (/new) or a change of one (policy edit)\n"
             . " *   remove   a row, after a confirmation (policy edit)\n"
             . " *   json     the REST/JSON endpoint: GET the list or one row (read), POST, PUT, DELETE (edit)\n */\n\n"
             . "\$Module = array( 'name' => '" . self::php( $s['title'] ) . "', 'variable_params' => false );\n\n\$ViewList = array();\n\n"
             . $view( 'list', array( 'read' ), array(), array( 'offset' => 'Offset', 'q' => 'Query', 'sort' => 'Sort' ) )
             . $view( 'edit', array( 'edit' ), array( 'ID' ) )
             . $view( 'remove', array( 'edit' ), array( 'ID' ) )
             . $view( 'json', array( 'read' ), array( 'ID' ) )
             . "\$FunctionList = array();\n// See the rows (list, the JSON reads, the fetch functions)\n\$FunctionList['read'] = array();\n"
             . "// Make, change and remove rows\n\$FunctionList['edit'] = array();\n";
    }

    /** @return string list.php */
    public static function listView( array $s, array $fields )
    {
        $search = array();
        foreach ( $fields as $f )
            if ( $f['type'] === 'string' && count( $search ) < 1 )
                $search[] = $f['name'];
        $c = $s['class'];
        return "<?php\n/**\n * {$s['module']}/list: the rows of {$s['table']}, 25 a page; /(q)/<words> searches " . ( $search ? $search[0] : 'nothing (no text field)' ) . ",\n"
             . " * /(sort)/<field>-asc|desc sorts.\n */\n\n"
             . "/** @var eZModule \$Module */\n\$Module = \$Params['Module'];\n\$http = eZHTTPTool::instance();\n"
             . "\$limit = 25;\n\$offset = isset( \$Params['Offset'] ) ? max( 0, (int)\$Params['Offset'] ) : 0;\n"
             . "if ( \$http->hasPostVariable( 'SearchButton' ) )\n{\n"
             . "    \$q = trim( (string)\$http->postVariable( 'q', '' ) );\n"
             . "    return \$Module->redirectTo( '{$s['module']}/list' . ( \$q !== '' ? '/(q)/' . rawurlencode( \$q ) : '' ) );\n}\n"
             . "if ( \$http->hasPostVariable( 'NewButton' ) )\n    return \$Module->redirectTo( '{$s['module']}/edit/new' );\n"
             . "\$q = isset( \$Params['Query'] ) ? mb_substr( trim( (string)\$Params['Query'] ), 0, 100 ) : '';\n"
             . "\$conditions = null;\n"
             . ( $search ? "if ( \$q !== '' )\n    \$conditions = array( '{$search[0]}' => array( 'like', '%' . \$q . '%' ) );\n" : '' )
             . "\$sorts = array( 'id' => 'desc' );\n"
             . "if ( isset( \$Params['Sort'] ) && preg_match( '/^([a-z0-9_]+)-(asc|desc)\$/', (string)\$Params['Sort'], \$m ) && ( \$m[1] === 'id' || isset( $c::fields()[\$m[1]] ) ) )\n"
             . "    \$sorts = array( \$m[1] => \$m[2] );\n"
             . "\$rows = $c::fetchList( \$conditions, \$offset, \$limit, \$sorts );\n"
             . "\$count = $c::fetchListCount( \$conditions );\n"
             . "\$access = eZUser::currentUser()->hasAccessTo( '{$s['module']}', 'edit' );\n\n"
             . "\$tpl = eZTemplate::factory();\n"
             . "\$tpl->setVariable( 'rows', \$rows );\n\$tpl->setVariable( 'fields', $c::fields() );\n"
             . "\$tpl->setVariable( 'count', \$count );\n\$tpl->setVariable( 'offset', \$offset );\n\$tpl->setVariable( 'limit', \$limit );\n"
             . "\$tpl->setVariable( 'q', \$q );\n\$tpl->setVariable( 'can_edit', \$access['accessWord'] !== 'no' );\n"
             . "\$tpl->setVariable( 'saved', isset( \$Params['UserParameters']['saved'] ) );\n"
             . "\$tpl->setVariable( 'page_uri', '{$s['module']}/list' . ( \$q !== '' ? '/(q)/' . rawurlencode( \$q ) : '' ) );\n"
             . "\$Result = array();\n\$Result['content'] = \$tpl->fetch( 'design:{$s['module']}/list.tpl' );\n"
             . "\$Result['path'] = array( array( 'url' => 'setup/menu', 'text' => ezpI18n::tr( '" . self::CONTEXT . "', 'Setup' ) ),\n"
             . "                         array( 'url' => false, 'text' => ezpI18n::tr( 'extension/{$s['name']}', '" . self::php( $s['title'] ) . "' ) ) );\n";
    }

    /** @return string edit.php */
    public static function editView( array $s )
    {
        $c = $s['class'];
        return "<?php\n/**\n * {$s['module']}/edit: a new row (/new) or a change of one (/<id>). Policy {$s['module']}/edit.\n */\n\n"
             . "/** @var eZModule \$Module */\n\$Module = \$Params['Module'];\n\$http = eZHTTPTool::instance();\n"
             . "\$id = isset( \$Params['ID'] ) ? (string)\$Params['ID'] : 'new';\n"
             . "\$row = \$id === 'new' ? $c::create() : $c::fetch( (int)\$id );\n"
             . "if ( !\$row instanceof $c )\n    return \$Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );\n"
             . "if ( \$http->hasPostVariable( 'CancelButton' ) )\n    return \$Module->redirectTo( '{$s['module']}/list' );\n"
             . "\$problems = array();\n"
             . "if ( \$http->hasPostVariable( 'StoreButton' ) )\n{\n"
             . "    \$input = array();\n"
             . "    foreach ( $c::fields() as \$name => \$field )\n    {\n"
             . "        if ( \$field['type'] === 'boolean' )\n            \$input[\$name] = \$http->hasPostVariable( 'Field_' . \$name ) ? 1 : 0;\n"
             . "        elseif ( \$http->hasPostVariable( 'Field_' . \$name ) )\n            \$input[\$name] = (string)\$http->postVariable( 'Field_' . \$name );\n    }\n"
             . "    \$problems = \$row->fromInput( \$input );\n"
             . "    if ( !\$problems )\n    {\n        \$row->store();\n"
             . "        return \$Module->redirectTo( '{$s['module']}/list/(saved)/' . (int)\$row->attribute( 'id' ) );\n    }\n}\n"
             . "\$tpl = eZTemplate::factory();\n"
             . "\$tpl->setVariable( 'row', \$row );\n\$tpl->setVariable( 'fields', $c::fields() );\n"
             . "\$tpl->setVariable( 'is_new', \$id === 'new' );\n\$tpl->setVariable( 'problems', \$problems );\n"
             . "\$Result = array();\n\$Result['content'] = \$tpl->fetch( 'design:{$s['module']}/edit.tpl' );\n"
             . "\$Result['path'] = array( array( 'url' => '{$s['module']}/list', 'text' => ezpI18n::tr( 'extension/{$s['name']}', '" . self::php( $s['title'] ) . "' ) ),\n"
             . "                         array( 'url' => false, 'text' => \$id === 'new' ? ezpI18n::tr( '" . self::CONTEXT . "', 'New' ) : '#' . (int)\$id ) );\n";
    }

    /** @return string remove.php */
    public static function removeView( array $s )
    {
        $c = $s['class'];
        return "<?php\n/**\n * {$s['module']}/remove: removes a row after a confirmation. Policy {$s['module']}/edit.\n */\n\n"
             . "/** @var eZModule \$Module */\n\$Module = \$Params['Module'];\n\$http = eZHTTPTool::instance();\n"
             . "\$row = isset( \$Params['ID'] ) ? $c::fetch( (int)\$Params['ID'] ) : null;\n"
             . "if ( !\$row instanceof $c )\n    return \$Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );\n"
             . "if ( \$http->hasPostVariable( 'CancelButton' ) )\n    return \$Module->redirectTo( '{$s['module']}/list' );\n"
             . "if ( \$http->hasPostVariable( 'ConfirmButton' ) )\n{\n    \$row->remove();\n    return \$Module->redirectTo( '{$s['module']}/list' );\n}\n"
             . "\$tpl = eZTemplate::factory();\n\$tpl->setVariable( 'row', \$row );\n\$tpl->setVariable( 'fields', $c::fields() );\n"
             . "\$Result = array();\n\$Result['content'] = \$tpl->fetch( 'design:{$s['module']}/remove.tpl' );\n"
             . "\$Result['path'] = array( array( 'url' => '{$s['module']}/list', 'text' => ezpI18n::tr( 'extension/{$s['name']}', '" . self::php( $s['title'] ) . "' ) ),\n"
             . "                         array( 'url' => false, 'text' => ezpI18n::tr( '" . self::CONTEXT . "', 'Remove' ) ) );\n";
    }

    /** @return string json.php */
    public static function jsonView( array $s )
    {
        $c = $s['class'];
        return "<?php\n/**\n * {$s['module']}/json: the REST/JSON endpoint of {$s['table']}.\n *\n"
             . " *   GET    {$s['module']}/json                    the rows: ?offset=0&limit=50 (at most 500), ?<field>=<value> filters\n"
             . " *   GET    {$s['module']}/json/<id>               one row\n"
             . " *   POST   {$s['module']}/json                    a new row from a JSON object (or form fields)\n"
             . " *   PUT    {$s['module']}/json/<id>               a change (also POST with _method=PUT)\n"
             . " *   DELETE {$s['module']}/json/<id>               a removal (also POST with _method=DELETE)\n *\n"
             . " * Reading needs the policy {$s['module']}/read, writing {$s['module']}/edit; a write from a page of the site sends the form token\n"
             . " * (header X-CSRF-Token) like every POST. Answers: {\"data\": ...} or {\"error\": ..., \"problems\": [...]} with the HTTP status.\n */\n\n"
             . "/** @var eZModule \$Module */\n\$Module = \$Params['Module'];\n"
             . "\$method = strtoupper( (string)eZSys::serverVariable( 'REQUEST_METHOD', true ) );\n"
             . "\$body = array();\n"
             . "\$raw = in_array( \$method, array( 'POST', 'PUT', 'DELETE' ), true ) ? (string)file_get_contents( 'php://input' ) : '';\n"
             . "if ( \$raw !== '' && strpos( (string)eZSys::serverVariable( 'CONTENT_TYPE', true ), 'json' ) !== false )\n    \$body = (array)json_decode( \$raw, true );\n"
             . "else\n    \$body = \$_POST;\n"
             . "if ( \$method === 'POST' && isset( \$body['_method'] ) && in_array( strtoupper( (string)\$body['_method'] ), array( 'PUT', 'DELETE' ), true ) )\n    \$method = strtoupper( (string)\$body['_method'] );\n"
             . "unset( \$body['_method'], \$body['ezxform_token'] );\n"
             . "\$answer = function ( \$status, array \$payload ) {\n"
             . "    while ( @ob_end_clean() );\n"
             . "    http_response_code( \$status );\n    header( 'Content-Type: application/json; charset=utf-8' );\n    header( 'X-Content-Type-Options: nosniff' );\n"
             . "    echo json_encode( \$payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );\n    eZExecution::cleanExit();\n};\n"
             . "\$id = isset( \$Params['ID'] ) && ctype_digit( (string)\$Params['ID'] ) ? (int)\$Params['ID'] : 0;\n"
             . "if ( \$method !== 'GET' )\n{\n"
             . "    \$access = eZUser::currentUser()->hasAccessTo( '{$s['module']}', 'edit' );\n"
             . "    if ( \$access['accessWord'] === 'no' )\n        \$answer( 403, array( 'error' => 'The policy {$s['module']}/edit is needed.' ) );\n}\n"
             . "if ( \$method === 'GET' && \$id )\n{\n    \$row = $c::fetch( \$id );\n"
             . "    \$row ? \$answer( 200, array( 'data' => \$row->toArray() ) ) : \$answer( 404, array( 'error' => 'There is no such row.' ) );\n}\n"
             . "if ( \$method === 'GET' )\n{\n"
             . "    \$conditions = array();\n"
             . "    foreach ( $c::fields() as \$name => \$field )\n        if ( isset( \$_GET[\$name] ) && is_scalar( \$_GET[\$name] ) )\n            \$conditions[\$name] = (string)\$_GET[\$name];\n"
             . "    \$offset = isset( \$_GET['offset'] ) ? max( 0, (int)\$_GET['offset'] ) : 0;\n"
             . "    \$limit = isset( \$_GET['limit'] ) ? max( 1, min( 500, (int)\$_GET['limit'] ) ) : 50;\n"
             . "    \$rows = array();\n    foreach ( $c::fetchList( \$conditions ?: null, \$offset, \$limit ) as \$row )\n        \$rows[] = \$row->toArray();\n"
             . "    \$answer( 200, array( 'data' => \$rows, 'total' => $c::fetchListCount( \$conditions ?: null ), 'offset' => \$offset, 'limit' => \$limit ) );\n}\n"
             . "if ( \$method === 'DELETE' )\n{\n    \$row = \$id ? $c::fetch( \$id ) : null;\n"
             . "    if ( !\$row )\n        \$answer( 404, array( 'error' => 'There is no such row.' ) );\n"
             . "    \$row->remove();\n    \$answer( 200, array( 'data' => array( 'id' => \$id, 'removed' => true ) ) );\n}\n"
             . "\$row = \$method === 'PUT' ? ( \$id ? $c::fetch( \$id ) : null ) : $c::create();\n"
             . "if ( !\$row )\n    \$answer( 404, array( 'error' => 'There is no such row.' ) );\n"
             . "\$problems = \$row->fromInput( \$body );\n"
             . "if ( \$problems )\n    \$answer( 422, array( 'error' => 'The row was not stored.', 'problems' => \$problems ) );\n"
             . "\$row->store();\n\$answer( \$method === 'PUT' ? 200 : 201, array( 'data' => \$row->toArray() ) );\n";
    }

    /** @return string function_definition.php */
    public static function functionDefinition( array $s )
    {
        $collection = $s['class'] . 'FunctionCollection';
        return "<?php\n/**\n * The fetch functions of {$s['module']}:\n *\n"
             . " *   {def \$rows = fetch( '{$s['module']}', 'list', hash( 'conditions', hash( '<field>', <value> ), 'offset', 0, 'limit', 20 ) )}\n"
             . " *   {fetch( '{$s['module']}', 'count', hash() )}\n *   {fetch( '{$s['module']}', 'row', hash( 'id', 42 ) )}\n */\n\n"
             . "\$FunctionList = array();\n"
             . "\$FunctionList['list'] = array( 'name' => 'list', 'call_method' => array( 'class' => '$collection', 'method' => 'fetchList' ),\n"
             . "    'parameter_type' => 'standard', 'parameters' => array(\n"
             . "        array( 'name' => 'conditions', 'type' => 'array', 'required' => false, 'default' => false ),\n"
             . "        array( 'name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => 0 ),\n"
             . "        array( 'name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => 50 ) ) );\n"
             . "\$FunctionList['count'] = array( 'name' => 'count', 'call_method' => array( 'class' => '$collection', 'method' => 'fetchCount' ),\n"
             . "    'parameter_type' => 'standard', 'parameters' => array(\n"
             . "        array( 'name' => 'conditions', 'type' => 'array', 'required' => false, 'default' => false ) ) );\n"
             . "\$FunctionList['row'] = array( 'name' => 'row', 'call_method' => array( 'class' => '$collection', 'method' => 'fetchRow' ),\n"
             . "    'parameter_type' => 'standard', 'parameters' => array(\n"
             . "        array( 'name' => 'id', 'type' => 'integer', 'required' => true ) ) );\n";
    }

    /** @return string the function collection class */
    public static function functionCollection( array $s )
    {
        $c = $s['class'];
        $collection = $c . 'FunctionCollection';
        return "<?php\n/**\n * The fetch functions of the module {$s['module']} (modules/{$s['module']}/function_definition.php).\n */\n\n"
             . "class $collection\n{\n"
             . "    public static function fetchList( \$conditions = false, \$offset = 0, \$limit = 50 )\n    {\n"
             . "        return array( 'result' => $c::fetchList( self::conditions( \$conditions ), max( 0, (int)\$offset ), max( 1, min( 500, (int)\$limit ) ) ) );\n    }\n\n"
             . "    public static function fetchCount( \$conditions = false )\n    {\n"
             . "        return array( 'result' => $c::fetchListCount( self::conditions( \$conditions ) ) );\n    }\n\n"
             . "    public static function fetchRow( \$id )\n    {\n        return array( 'result' => $c::fetch( (int)\$id ) ?: false );\n    }\n\n"
             . "    /** Equality on the fields of the table only */\n"
             . "    protected static function conditions( \$conditions )\n    {\n"
             . "        if ( !is_array( \$conditions ) )\n            return null;\n"
             . "        \$clean = array();\n"
             . "        foreach ( \$conditions as \$name => \$value )\n            if ( ( \$name === 'id' || isset( $c::fields()[\$name] ) ) && is_scalar( \$value ) )\n                \$clean[\$name] = \$value;\n"
             . "        return \$clean ?: null;\n    }\n}\n";
    }

    /** @return string list.tpl (admin4 components; identical for design/admin) */
    public static function listTemplate( array $s, array $fields )
    {
        $ctx = 'extension/' . $s['name'];
        $k = self::CONTEXT;
        $m = $s['module'];
        $shown = array_slice( $fields, 0, 6 );
        $heads = '';
        $cells = '';
        foreach ( $shown as $f )
        {
            $heads .= "            <th scope=\"col\">{'" . self::tpl( $f['label'] ) . "'|i18n( '$ctx' )}</th>\n";
            if ( in_array( $f['type'], array( 'object', 'node', 'user' ), true ) )
                $cells .= "            <td>{def \$t = \$row.{$f['name']}_target}{if \$t}{if is_set( \$t.main_node_id )}<a href={concat( 'content/view/full/', \$t.main_node_id )|ezurl}>{\$t.name|wash}</a>{elseif is_set( \$t.node_id )}<a href={concat( 'content/view/full/', \$t.node_id )|ezurl}>{\$t.name|wash}</a>{else}{\$t.login|wash}{/if}{else}<span class=\"a4-muted\">{\$row.{$f['name']}|wash}</span>{/if}{undef \$t}</td>\n";
            elseif ( $f['type'] === 'boolean' )
                $cells .= "            <td>{if \$row.{$f['name']}}{'yes'|i18n( '$k' )}{else}<span class=\"a4-muted\">{'no'|i18n( '$k' )}</span>{/if}</td>\n";
            elseif ( in_array( $f['type'], array( 'date', 'datetime' ), true ) )
                $cells .= "            <td>{if \$row.{$f['name']}}{\$row.{$f['name']}|l10n( '" . ( $f['type'] === 'date' ? 'shortdate' : 'shortdatetime' ) . "' )}{/if}</td>\n";
            else
                $cells .= "            <td>{\$row.{$f['name']}|wash|shorten( 80 )}</td>\n";
        }
        return "{* {$m}/list: the rows of {$s['table']}. Made by the module table wizard. No inline CSS: admin4 components only. *}\n"
             . "<div class=\"a4-page\" id=\"{$m}-list\">\n\n<div class=\"a4-pagehead\">\n    <div class=\"a4-pagehead-main\">\n"
             . "        <h1 class=\"a4-pagehead-title context-title\">{'" . self::tpl( $s['title'] ) . "'|i18n( '$ctx' )}</h1>\n"
             . "        <p class=\"a4-pagehead-sub\">{'%count rows'|i18n( '$k',, hash( '%count', \$count ) )|wash}</p>\n    </div>\n"
             . "    {if \$can_edit}<div class=\"a4-pagehead-actions a4-actions\"><a class=\"a4-btn a4-btn-primary\" href={'{$m}/edit/new'|ezurl}>{'New row'|i18n( '$k' )}</a></div>{/if}\n</div>\n\n"
             . "{if \$saved}<div class=\"a4-notice is-ok\" role=\"status\"><p>{'The row was saved.'|i18n( '$k' )}</p></div>{/if}\n\n"
             . "<form method=\"post\" action={'{$m}/list'|ezurl} class=\"a4-filterbar\" role=\"search\">\n"
             . "    <div class=\"a4-field\"><label class=\"a4-label\" for=\"{$m}-q\">{'Search'|i18n( '$k' )}</label><input class=\"a4-input\" type=\"search\" id=\"{$m}-q\" name=\"q\" value=\"{\$q|wash}\" /></div>\n"
             . "    <div class=\"a4-field\"><span class=\"a4-label\">&nbsp;</span><button type=\"submit\" class=\"a4-btn\" name=\"SearchButton\" value=\"1\">{'Search'|i18n( '$k' )}</button></div>\n</form>\n\n"
             . "{if \$rows|count}\n<div class=\"a4-card is-flush a4-section\">\n"
             . "<div class=\"a4-table-wrap\" role=\"region\" aria-label=\"{'" . self::tpl( $s['title'] ) . "'|i18n( '$ctx' )|wash}\" tabindex=\"0\">\n<table class=\"a4-table\">\n"
             . "    <thead><tr>\n            <th scope=\"col\">#</th>\n" . $heads
             . "            <th scope=\"col\"><span class=\"a4-sr-only\">{'Actions'|i18n( '$k' )}</span></th>\n    </tr></thead>\n    <tbody>\n"
             . "    {foreach \$rows as \$row}\n        <tr>\n            <td>{\$row.id}</td>\n" . $cells
             . "            <td class=\"a4-nowrap\">{if \$can_edit}<a class=\"a4-btn a4-btn-small\" href={concat( '{$m}/edit/', \$row.id )|ezurl}>{'Edit'|i18n( '$k' )}</a> <a class=\"a4-btn a4-btn-small a4-btn-outline-danger\" href={concat( '{$m}/remove/', \$row.id )|ezurl}>{'Remove'|i18n( '$k' )}</a>{/if}</td>\n"
             . "        </tr>\n    {/foreach}\n    </tbody>\n</table>\n</div>\n</div>\n"
             . "{include name=navigator uri='design:navigator/google.tpl' page_uri=\$page_uri item_count=\$count view_parameters=hash( 'offset', \$offset ) item_limit=\$limit}\n"
             . "{else}\n<p class=\"a4-empty\">{'No rows yet.'|i18n( '$k' )}</p>\n{/if}\n\n"
             . "<p class=\"a4-meta\">{'JSON: %url'|i18n( '$k',, hash( '%url', '{$m}/json'|ezurl( 'no', 'full' ) ) )|wash}</p>\n</div>\n";
    }

    /** @return string edit.tpl */
    public static function editTemplate( array $s, array $fields )
    {
        $ctx = 'extension/' . $s['name'];
        $k = self::CONTEXT;
        $m = $s['module'];
        $inputs = '';
        foreach ( $fields as $f )
        {
            $id = "{$m}-field-{$f['name']}";
            $name = "Field_{$f['name']}";
            $label = "<label class=\"a4-label\" for=\"$id\">{'" . self::tpl( $f['label'] ) . "'|i18n( '$ctx' )}" . ( $f['required'] ? " <span class=\"a4-required-mark\" aria-hidden=\"true\">*</span>" : '' ) . "</label>";
            $required = $f['required'] && $f['type'] !== 'boolean' ? ' required="required"' : '';
            switch ( $f['type'] )
            {
                case 'text':
                case 'json':
                    $control = "<textarea class=\"a4-input\" id=\"$id\" name=\"$name\" rows=\"5\"$required>{\$row.{$f['name']}|wash}</textarea>";
                    $wide = ' a4-field-wide';
                    break;
                case 'boolean':
                    $control = "<label class=\"a4-check\"><input type=\"checkbox\" id=\"$id\" name=\"$name\" value=\"1\"{if \$row.{$f['name']}} checked=\"checked\"{/if} /> {'" . self::tpl( $f['label'] ) . "'|i18n( '$ctx' )}</label>";
                    $label = '';
                    $wide = '';
                    break;
                case 'integer':
                case 'object':
                case 'node':
                case 'user':
                    $control = "<input class=\"a4-input is-auto\" type=\"number\" step=\"1\" id=\"$id\" name=\"$name\" value=\"{\$row.{$f['name']}|wash}\"$required />";
                    if ( $f['type'] !== 'integer' )
                        $control .= "\n        {def \$t = \$row.{$f['name']}_target}{if \$t}<p class=\"a4-help\">{\$t.name|wash}</p>{/if}{undef \$t}"
                                  . "\n        <p class=\"a4-help\">{'" . ( $f['type'] === 'node' ? 'A node id' : ( $f['type'] === 'user' ? 'The object id of a user' : 'A content object id' ) ) . "'|i18n( '$k' )}</p>";
                    $wide = '';
                    break;
                case 'float':
                    $control = "<input class=\"a4-input is-auto\" type=\"number\" step=\"any\" id=\"$id\" name=\"$name\" value=\"{\$row.{$f['name']}|wash}\"$required />";
                    $wide = '';
                    break;
                case 'date':
                    $control = "<input class=\"a4-input is-auto\" type=\"date\" id=\"$id\" name=\"$name\" value=\"{if \$row.{$f['name']}}{\$row.{$f['name']}|datetime( 'custom', '%Y-%m-%d' )}{/if}\"$required />";
                    $wide = '';
                    break;
                case 'datetime':
                    $control = "<input class=\"a4-input is-auto\" type=\"datetime-local\" id=\"$id\" name=\"$name\" value=\"{if \$row.{$f['name']}}{\$row.{$f['name']}|datetime( 'custom', '%Y-%m-%dT%H:%i' )}{/if}\"$required />";
                    $wide = '';
                    break;
                default:
                    $control = "<input class=\"a4-input\" id=\"$id\" name=\"$name\" maxlength=\"" . ( $f['length'] ?: 255 ) . "\" value=\"{\$row.{$f['name']}|wash}\"$required />";
                    $wide = ( $f['length'] ?: 255 ) > 255 ? ' a4-field-wide' : '';
            }
            $inputs .= "    <div class=\"a4-field$wide\">\n        " . ( $label !== '' ? $label . "\n        " : '' ) . $control . "\n    </div>\n";
        }
        return "{* {$m}/edit: a new row or a change of one. Made by the module table wizard. *}\n"
             . "<div class=\"a4-page\" id=\"{$m}-edit\">\n\n<div class=\"a4-pagehead\"><div class=\"a4-pagehead-main\">\n"
             . "    <h1 class=\"a4-pagehead-title context-title\">{if \$is_new}{'New row'|i18n( '$k' )}{else}{'Row %id'|i18n( '$k',, hash( '%id', \$row.id ) )|wash}{/if}</h1>\n</div></div>\n\n"
             . "{if \$problems|count}\n<div class=\"a4-error-summary a4-notice is-bad\" role=\"alert\"><p class=\"a4-notice-title\">{'The row was not saved'|i18n( '$k' )}</p>\n"
             . "    <ul>{foreach \$problems as \$p}<li>{\$p|wash}</li>{/foreach}</ul></div>\n{/if}\n\n"
             . "<form method=\"post\" action={concat( '{$m}/edit/', cond( \$is_new, 'new', \$row.id ) )|ezurl} class=\"a4-form\">\n"
             . "<div class=\"a4-card a4-section\">\n<div class=\"a4-card-body a4-grid\">\n" . $inputs . "</div>\n"
             . "<div class=\"a4-card-foot a4-actions\">\n"
             . "    <button type=\"submit\" class=\"a4-btn a4-btn-primary\" name=\"StoreButton\" value=\"1\">{'Save'|i18n( '$k' )}</button>\n"
             . "    <button type=\"submit\" class=\"a4-btn a4-btn-ghost\" name=\"CancelButton\" value=\"1\" formnovalidate=\"formnovalidate\">{'Cancel'|i18n( '$k' )}</button>\n"
             . "</div>\n</div>\n</form>\n</div>\n";
    }

    /** @return string remove.tpl */
    public static function removeTemplate( array $s )
    {
        $k = self::CONTEXT;
        $m = $s['module'];
        return "{* {$m}/remove: the confirmation of a removal. Made by the module table wizard. *}\n"
             . "<div class=\"a4-page\" id=\"{$m}-remove\">\n<div class=\"a4-pagehead\"><div class=\"a4-pagehead-main\">\n"
             . "    <h1 class=\"a4-pagehead-title context-title\">{'Remove row %id?'|i18n( '$k',, hash( '%id', \$row.id ) )|wash}</h1>\n</div></div>\n"
             . "<form method=\"post\" action={concat( '{$m}/remove/', \$row.id )|ezurl}>\n"
             . "<div class=\"a4-notice is-warn\" role=\"status\"><p>{'The row is removed from the table and cannot be brought back.'|i18n( '$k' )}</p></div>\n"
             . "<div class=\"a4-actions\">\n"
             . "    <button type=\"submit\" class=\"a4-btn a4-btn-danger\" name=\"ConfirmButton\" value=\"1\">{'Remove'|i18n( '$k' )}</button>\n"
             . "    <button type=\"submit\" class=\"a4-btn a4-btn-ghost\" name=\"CancelButton\" value=\"1\">{'Cancel'|i18n( '$k' )}</button>\n"
             . "</div>\n</form>\n</div>\n";
    }

    /**
     * A PHP value as code, indented.
     *
     * @param mixed $value
     * @param int $depth
     * @return string
     */
    public static function export( $value, $depth = 0 )
    {
        $pad = str_repeat( '    ', $depth );
        if ( is_array( $value ) )
        {
            if ( !$value )
                return 'array()';
            $list = array_keys( $value ) === range( 0, count( $value ) - 1 );
            $items = array();
            foreach ( $value as $k => $v )
                $items[] = $pad . '    ' . ( $list ? '' : var_export( $k, true ) . ' => ' ) . self::export( $v, $depth + 1 );
            return "array(\n" . implode( ",\n", $items ) . "\n" . $pad . ')';
        }
        if ( is_float( $value ) )
            return $value == (int)$value ? number_format( $value, 1, '.', '' ) : var_export( $value, true );
        return var_export( $value, true );
    }

    /** @return string a text for a single-quoted PHP string */
    public static function php( $text )
    {
        return str_replace( array( '\\', "'" ), array( '\\\\', "\\'" ), (string)$text );
    }

    /** @return string a text for a single-quoted template string */
    public static function tpl( $text )
    {
        return str_replace( array( '\\', "'", '{', '}' ), array( '', "\\'", '', '' ), (string)$text );
    }
}
}
?>
