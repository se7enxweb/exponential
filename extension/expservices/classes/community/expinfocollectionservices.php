<?php
/**
 * ezjscore/call/expinfocollection::<method>: collected forms (information collection): the forms that have
 * collections, the collections and their values, export as CSV, removal, and the submit of a form the way
 * content/collectinformation does it. Reading needs the infocollector/read policy; submitting follows the
 * settings of collect.ini (anonymous collection, unique or overwrite handling).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expInfoCollectionServices extends expServiceBase
{
    public static $services = array(
        'forms' => array( 'summary' => 'The content objects that have collected data, with the number of collections', 'access' => array( 'infocollector', 'read' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of forms' ),
        'collections' => array( 'summary' => 'The collections of one form, newest first', 'access' => array( 'infocollector', 'read' ), 'write' => false,
            'args' => array( 'object_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of collections' ),
        'view' => array( 'summary' => 'One collection with its values', 'access' => array( 'infocollector', 'read' ), 'write' => false, 'args' => array( 'collection_id' => 'int' ), 'returns' => 'collection with values' ),
        'count' => array( 'summary' => 'How many collections a form (or all forms) has', 'access' => array( 'infocollector', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'count' ),
        'fields' => array( 'summary' => 'The fields of a form: the attributes that collect information, with type and requirement', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int' ), 'returns' => 'list of fields' ),
        'summary' => array( 'summary' => 'Per field of a form: how many values and, for choice and number fields, the distribution', 'access' => array( 'infocollector', 'read' ), 'write' => false,
            'args' => array( 'object_id' => 'int' ), 'returns' => 'list of fields with counts' ),
        'mine' => array( 'summary' => 'The collections the logged-in user submitted', 'access' => 'user', 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of collections' ),
        'export' => array( 'summary' => 'All collections of a form as CSV text (audited as a data export by the kernel when enabled)', 'access' => array( 'infocollector', 'read' ), 'write' => false,
            'args' => array( 'object_id' => 'int' ), 'returns' => 'filename, csv' ),
        'submit' => array( 'summary' => 'Submits a form: POST object_id and fields as JSON identifier => value (text, number, boolean, choice id)', 'access' => array( 'content', 'read' ), 'write' => true,
            'args' => array( 'object_id' => 'int POST', 'fields' => 'json POST' ), 'returns' => 'collection id' ),
        'remove' => array( 'summary' => 'Removes one collection (audited as data.infocollection.remove)', 'access' => array( 'infocollector', 'read' ), 'write' => true,
            'args' => array( 'collection_id' => 'int POST' ), 'returns' => 'removed id' ),
        'removeAll' => array( 'summary' => 'Removes every collection of a form', 'access' => array( 'infocollector', 'read' ), 'write' => true,
            'args' => array( 'object_id' => 'int POST' ), 'returns' => 'removed count' ),
        'settings' => array( 'summary' => 'How collection is handled for a form: anonymous allowed, unique/overwrite/multiple, template type', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int' ), 'returns' => 'anonymous, handling, type' ),
    );

    /** Datatype => the POST variable the datatype reads while collecting. */
    protected static $postNames = array( 'ezstring' => '_ezstring_data_text_', 'eztext' => '_data_text_', 'ezemail' => '_data_text_', 'ezinteger' => '_data_integer_',
        'ezfloat' => '_data_float_', 'ezboolean' => '_data_boolean_', 'ezoption' => '_data_option_value_' );

    protected static function formObject( $id )
    {
        $o = eZContentObject::fetch( (int)$id );
        if ( !$o instanceof eZContentObject )
            throw new expServiceException( "No object $id", 404 );
        return $o;
    }

    /** The attributes of the current version that collect information. */
    protected static function collectors( eZContentObject $o )
    {
        $list = array();
        foreach ( $o->currentVersion()->contentObjectAttributes() as $a )
            if ( $a->contentClassAttributeIsInformationCollector() )
                $list[$a->contentClassAttribute()->attribute( 'identifier' )] = $a;
        return $list;
    }

    protected static function fetchCollection( $id )
    {
        $c = eZInformationCollection::fetch( (int)$id );
        if ( !$c instanceof eZInformationCollection )
            throw new expServiceException( "No collection $id", 404 );
        return $c;
    }

    public static function forms( array $args )
    {
        self::guard( 'forms' );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $db = eZDB::instance();
        $total = (int)$db->arrayQuery( 'SELECT COUNT(DISTINCT contentobject_id) AS c FROM ezinfocollection' )[0]['c'];
        $rows = $db->arrayQuery( 'SELECT contentobject_id, COUNT(*) AS c, MAX(created) AS last FROM ezinfocollection GROUP BY contentobject_id ORDER BY MAX(created) DESC', array( 'limit' => $limit, 'offset' => $offset ) );
        $items = array();
        foreach ( $rows as $r )
        {
            $o = eZContentObject::fetch( (int)$r['contentobject_id'] );
            $items[] = array( 'object_id' => (int)$r['contentobject_id'], 'name' => $o ? $o->attribute( 'name' ) : null, 'class' => $o ? $o->attribute( 'class_identifier' ) : null,
                'node_id' => $o ? (int)$o->attribute( 'main_node_id' ) : null, 'collections' => (int)$r['c'], 'last' => self::iso( $r['last'] ) );
        }
        return self::page( $items, $total, $offset, $limit );
    }

    public static function collections( array $args )
    {
        self::guard( 'collections' );
        $o = self::formObject( self::arg( $args, 0, 'int' ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $id = (int)$o->attribute( 'id' );
        $list = eZInformationCollection::fetchCollectionsList( $id, false, false, array( 'limit' => $limit, 'offset' => $offset ), array( 'created', false ) );
        $items = array();
        foreach ( (array)$list as $c )
            $items[] = expCommerceExport::collection( $c, true );
        return self::page( $items, (int)eZInformationCollection::fetchCollectionsCount( $id ), $offset, $limit );
    }

    public static function view( array $args )
    {
        self::guard( 'view' );
        return self::ok( expCommerceExport::collection( self::fetchCollection( self::arg( $args, 0, 'int' ) ), true ) );
    }

    public static function count( array $args )
    {
        self::guard( 'count' );
        $id = self::arg( $args, 0, 'int', 0 );
        return self::ok( array( 'count' => (int)eZInformationCollection::fetchCollectionsCount( $id ?: false ), 'object_id' => $id ?: null ) );
    }

    public static function fields( array $args )
    {
        self::guard( 'fields' );
        $node = self::node( self::arg( $args, 0, 'int' ), 'read' );
        $out = array();
        foreach ( self::collectors( $node->attribute( 'object' ) ) as $id => $a )
        {
            $ca = $a->contentClassAttribute();
            $f = array( 'identifier' => $id, 'name' => $ca->attribute( 'name' ), 'type' => $ca->attribute( 'data_type_string' ), 'required' => (bool)$ca->attribute( 'is_required' ),
                'supported' => isset( self::$postNames[$ca->attribute( 'data_type_string' )] ) );
            if ( $ca->attribute( 'data_type_string' ) === 'ezoption' )
            {
                $f['choices'] = array();
                foreach ( (array)$a->content()->attribute( 'option_list' ) as $c )
                    $f['choices'][] = array( 'id' => (int)$c['id'], 'value' => $c['value'] );
            }
            $out[] = $f;
        }
        return self::ok( $out );
    }

    public static function summary( array $args )
    {
        self::guard( 'summary' );
        $o = self::formObject( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( self::collectors( $o ) as $id => $a )
        {
            $counts = eZInformationCollection::fetchCountList( $a->attribute( 'id' ) );
            $dist = array();
            foreach ( $counts as $v => $n )
                $dist[] = array( 'value' => (int)$v, 'count' => (int)$n );
            $out[] = array( 'identifier' => $id, 'type' => $a->attribute( 'data_type_string' ), 'values' => array_sum( array_map( 'intval', $counts ) ), 'int_distribution' => $dist );
        }
        return self::ok( $out, array( 'collections' => (int)eZInformationCollection::fetchCollectionsCount( $o->attribute( 'id' ) ) ) );
    }

    public static function mine( array $args )
    {
        self::guard( 'mine' );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $uid = (int)eZUser::currentUser()->attribute( 'contentobject_id' );
        $items = array();
        foreach ( (array)eZInformationCollection::fetchCollectionsList( false, $uid, false, array( 'limit' => $limit, 'offset' => $offset ), array( 'created', false ) ) as $c )
            $items[] = expCommerceExport::collection( $c, true );
        return self::page( $items, (int)eZInformationCollection::fetchCollectionsCount( false, $uid ), $offset, $limit );
    }

    /** One CSV line, RFC 4180, with a leading quote guard against spreadsheet formulas. */
    protected static function csvLine( array $cells )
    {
        $out = array();
        foreach ( $cells as $c )
        {
            $c = (string)$c;
            if ( $c !== '' && strpbrk( $c[0], "=+-@\t\r" ) !== false )
                $c = "'" . $c;
            $out[] = '"' . str_replace( '"', '""', $c ) . '"';
        }
        return implode( ',', $out );
    }

    public static function export( array $args )
    {
        self::guard( 'export' );
        $o = self::formObject( self::arg( $args, 0, 'int' ) );
        $id = (int)$o->attribute( 'id' );
        $fields = array();
        foreach ( self::collectors( $o ) as $ident => $a )
            $fields[$ident] = $a->contentClassAttribute()->attribute( 'name' );
        $lines = array( self::csvLine( array_merge( array( 'collection', 'created', 'user' ), array_values( $fields ) ) ) );
        foreach ( (array)eZInformationCollection::fetchCollectionsList( $id, false, false, false, array( 'created', true ) ) as $c )
        {
            $e = expCommerceExport::collection( $c, true );
            $by = array();
            foreach ( $e['values'] as $v )
                $by[$v['attribute']] = $v['text'] !== '' && $v['text'] !== null ? $v['text'] : ( $v['int'] ?: $v['float'] );
            $row = array( $e['id'], $e['created'], $e['creator_id'] );
            foreach ( array_keys( $fields ) as $ident )
                $row[] = isset( $by[$ident] ) ? $by[$ident] : '';
            $lines[] = self::csvLine( $row );
        }
        self::audit( 'data.export.csv', array( 'object' => array( 'type' => 'infocollection', 'id' => $id ), 'verb' => 'export', 'x' => array( 'rows' => count( $lines ) - 1 ) ) );
        return self::ok( array( 'filename' => 'collections-' . $id . '.csv', 'csv' => implode( "\r\n", $lines ) . "\r\n", 'rows' => count( $lines ) - 1 ) );
    }

    public static function settings( array $args )
    {
        self::guard( 'settings' );
        $node = self::node( self::arg( $args, 0, 'int' ), 'read' );
        $o = $node->attribute( 'object' );
        return self::ok( array( 'anonymous' => (bool)eZInformationCollection::allowAnonymous( $o ), 'handling' => eZInformationCollection::userDataHandling( $o ),
            'type' => eZInformationCollection::typeForObject( $o ), 'collects' => count( self::collectors( $o ) ) > 0 ) );
    }

    public static function submit( array $args )
    {
        self::guard( 'submit' );
        $o = self::formObject( self::post( 'object_id', 'int' ) );
        $values = self::post( 'fields', 'json' );
        if ( !is_array( $values ) )
            throw new expServiceException( 'fields is a JSON object: attribute identifier => value', 400 );
        if ( !$o->canRead() )
            throw new expServiceException( 'No read access to the form', 403 );
        $collectors = self::collectors( $o );
        if ( !$collectors )
            throw new expServiceException( 'The object does not collect information', 404 );
        $user = eZUser::currentUser();
        $logged = $user->attribute( 'is_logged_in' );
        if ( !$logged && !eZInformationCollection::allowAnonymous( $o ) )
            throw new expServiceException( 'Anonymous users cannot submit this form: log in', 401 );
        foreach ( $values as $k => $v )
            if ( !isset( $collectors[$k] ) )
                throw new expServiceException( "'$k' is not a field of this form", 422 );

        $handling = eZInformationCollection::userDataHandling( $o );
        $collection = false;
        if ( $handling === 'unique' || $handling === 'overwrite' )
            $collection = eZInformationCollection::fetchByUserIdentifier( eZInformationCollection::currentUserIdentifier(), $o->attribute( 'id' ) );
        if ( $handling === 'unique' && $collection )
            throw new expServiceException( 'You have already submitted this form', 409 );

        // the datatypes read their input from the HTTP variables: set them, for the time of the submit
        $http = eZHTTPTool::instance();
        $base = 'ContentObjectAttribute';
        $set = array();
        $errors = array();
        foreach ( $collectors as $ident => $a )
        {
            $type = $a->attribute( 'data_type_string' );
            if ( !isset( self::$postNames[$type] ) )
            {
                if ( isset( $values[$ident] ) )
                    throw new expServiceException( "The field '$ident' has a type ($type) that the service cannot submit", 422 );
                continue;
            }
            if ( array_key_exists( $ident, $values ) )
            {
                if ( !is_scalar( $values[$ident] ) )
                    throw new expServiceException( "The value of '$ident' must be a single value", 422 );
                $name = $base . self::$postNames[$type] . $a->attribute( 'id' );
                $http->setPostVariable( $name, $type === 'ezboolean' ? ( $values[$ident] ? 1 : 0 ) : (string)$values[$ident] );
                $set[] = $name;
            }
        }
        try
        {
            $canCollect = true;
            foreach ( $collectors as $ident => $a )
            {
                $input = null;
                $status = $a->validateInformation( $http, $base, $input );
                if ( $status === eZInputValidator::STATE_INVALID )
                {
                    $canCollect = false;
                    $errors[$ident] = (string)( $a->attribute( 'validation_error' ) ?: 'invalid' );
                }
            }
            if ( !$canCollect )
                throw new expServiceException( 'The form has errors: ' . json_encode( $errors ), 422 );

            $db = eZDB::instance();
            $db->begin();
            $new = false;
            if ( !$collection )
            {
                $collection = eZInformationCollection::create( $o->attribute( 'id' ), eZInformationCollection::currentUserIdentifier() );
                $collection->store();
                $new = true;
            }
            else
                $collection->setAttribute( 'modified', time() );
            foreach ( $collectors as $a )
            {
                $ca = $new ? eZInformationCollectionAttribute::create( $collection->attribute( 'id' ) )
                           : eZInformationCollectionAttribute::fetchByObjectAttributeID( $collection->attribute( 'id' ), $a->attribute( 'id' ) );
                if ( $ca && $a->collectInformation( $collection, $ca, $http, $base ) )
                    $ca->store();
            }
            $db->commit();
            $collection->sync();
        }
        finally
        {
            foreach ( $set as $name )
                unset( $_POST[$name] );
        }
        return self::ok( array( 'collection_id' => (int)$collection->attribute( 'id' ), 'object_id' => (int)$o->attribute( 'id' ), 'handling' => $handling ) );
    }

    public static function remove( array $args )
    {
        self::guard( 'remove' );
        $c = self::fetchCollection( self::post( 'collection_id', 'int' ) );
        $id = (int)$c->attribute( 'id' );
        $oid = (int)$c->attribute( 'contentobject_id' );
        eZInformationCollection::removeCollection( $id );
        self::audit( 'data.infocollection.remove', array( 'object' => array( 'type' => 'infocollection', 'id' => $id ), 'target' => array( 'type' => 'object', 'id' => $oid ) ) );
        return self::ok( array( 'removed' => $id ) );
    }

    public static function removeAll( array $args )
    {
        self::guard( 'removeAll' );
        $o = self::formObject( self::post( 'object_id', 'int' ) );
        $n = (int)eZInformationCollection::fetchCollectionsCount( $o->attribute( 'id' ) );
        eZInformationCollection::removeContentObject( $o->attribute( 'id' ) );
        self::audit( 'data.infocollection.remove', array( 'object' => array( 'type' => 'object', 'id' => (int)$o->attribute( 'id' ) ), 'verb' => 'remove_all', 'x' => array( 'collections' => $n ) ) );
        return self::ok( array( 'removed' => $n ) );
    }
}
