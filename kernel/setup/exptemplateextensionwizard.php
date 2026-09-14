<?php
/**
 * The template extension wizard: everything a template can be taught to call.
 *
 * Four things can be added to the template language from an extension, and they
 * are easy to confuse:
 *
 *   an operator       {$value|myoperator( 3 )}     takes a value, gives one back
 *   a function        {myfunction arg=3}           writes output, may have a body
 *   a fetch function  {fetch( mymodule, thing )}   reads something and returns it
 *   a fetch alias     {fetch_alias( news_list )}   a fetch with its arguments fixed
 *
 * This writes any of them, in one extension, correctly registered. Registration
 * is where this usually goes wrong: an operator lives in an autoload array and
 * not in an ini, a fetch function lives in a module directory and not in a
 * class, and neither is found unless the extension is named in the right place.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

require_once 'kernel/setup/expextensionwizard.php';

class expTemplateExtensionWizard extends expExtensionWizard
{
    /**
     * What wrote the files, for the head of each one.
     *
     * @return string
     */
    public static function wizardName()
    {
        return 'template extension wizard';
    }

    // ── What a parameter can be ──────────────────────────────────────────────

    /**
     * The parameter types the template engine knows.
     *
     * These are what namedParameterList() declares, as the plain strings every
     * operator in the kernel uses - not the eZTemplate::TYPE_ constants, which
     * are a different set for a different purpose and would be an undefined
     * constant here. The engine checks nothing: the type is a note to whoever
     * reads the code.
     *
     * The default is what arrives when a template leaves the parameter out. It
     * is false throughout the kernel rather than an empty value of the type,
     * because false is the one answer that cannot be confused with something
     * somebody actually passed.
     *
     * @return array
     */
    public static function parameterTypes()
    {
        return array(
            'string'  => array( 'label' => 'string',  'declared' => 'string',  'default' => 'false' ),
            'integer' => array( 'label' => 'integer', 'declared' => 'integer', 'default' => 'false' ),
            'float'   => array( 'label' => 'float',   'declared' => 'float',   'default' => 'false' ),
            'boolean' => array( 'label' => 'boolean', 'declared' => 'boolean', 'default' => 'false' ),
            'array'   => array( 'label' => 'array',   'declared' => 'array',   'default' => 'array()' ),
            'none'    => array( 'label' => 'any',     'declared' => 'none',    'default' => 'false' ),
        );
    }

    /**
     * What an operator can promise the compiler about itself.
     *
     * A template is compiled once and run many times. An operator that promises
     * enough here is run at compile time and costs nothing afterwards; one that
     * promises nothing is called on every request, for ever.
     *
     * @return array
     */
    public static function hints()
    {
        return array(
            'static' => array(
                'label'   => 'Always the same answer',
                'default' => false,
                'what'    => 'Given the same input this always gives the same output, with nothing read from the request, the session, the database or the clock. The compiler may then run it once, at compile time, and put the answer straight in the template. The fastest an operator can be, and a lie here is a value frozen for the life of the cache.' ),
            'transform-parameters' => array(
                'label'   => 'Parameters may be worked out first',
                'default' => true,
                'what'    => 'The parameters can be evaluated before the operator is reached, rather than handed over as unevaluated element trees. True for almost every operator; false only for one that has to see the expression rather than its value.' ),
            'input-as-parameter' => array(
                'label'   => 'Input may be passed as a parameter',
                'default' => false,
                'what'    => 'The value on the left of the pipe can be handed over as an ordinary parameter. Lets the compiler rewrite the operator into a plain function call.' ),
        );
    }

    // ── What this wizard can put in ──────────────────────────────────────────

    /**
     * @return array
     */
    public static function parts()
    {
        return array(
            'operator' => array(
                'label' => 'Operator class',
                'description' => 'The class behind the operators named below, with its parameter list, its compile-time hints, and a modify() that dispatches on the operator name.',
                'default' => true ),
            'function' => array(
                'label' => 'Function class',
                'description' => 'A template function - {myfunction} rather than |myoperator - with its attribute list and a process() that writes output. Written only when function names are given.',
                'default' => true ),
            'fetch' => array(
                'label' => 'Fetch functions',
                'description' => 'A module directory carrying only a function_definition.php, which is all a fetch function is. Written only when fetch names are given.',
                'default' => true ),
            'fetch_alias' => array(
                'label' => 'Fetch aliases',
                'description' => 'fetchalias.ini, so a long fetch with fixed arguments can be called by one short name from any template.',
                'default' => true ),
            'autoload' => array(
                'label' => 'Autoload registration',
                'description' => 'autoloads/eztemplateautoload.php, which is how operators and functions are really registered. Not an ini: this is the file the engine reads, and without it nothing here is ever loaded.',
                'default' => true ),
            'settings' => array(
                'label' => 'Registration',
                'description' => 'site.ini naming this extension as one to look in for that autoload file, and module.ini if there are fetch functions.',
                'default' => true ),
            'examples' => array(
                'label' => 'API examples',
                'description' => 'What each thing looks like in a template, what it is handed, and how to try it from a script without a page anywhere near it.',
                'default' => true ),
            'ezinfo' => array(
                'label' => 'ezinfo.php',
                'description' => 'What the admin interface reads to show the extension name, version and licence.',
                'default' => true ),
            'extension_xml' => array(
                'label' => 'extension.xml',
                'description' => 'The packaged description of the extension.',
                'default' => true ),
            'composer' => array(
                'label' => 'composer.json',
                'description' => 'So the extension can be required by name rather than copied in.',
                'default' => true ),
            'readme' => array(
                'label' => 'README.md',
                'description' => 'What it adds to the template language, how to switch it on, and what each name does.',
                'default' => true ),
            'gitignore' => array(
                'label' => '.gitignore',
                'description' => 'Keeps editor leftovers and build output out of the repository.',
                'default' => true ),
            'licence' => array(
                'label' => 'LICENSE',
                'description' => 'The licence text named below. On by default: an extension with no licence file says nothing about how it may be used.',
                'default' => true ),
        );
    }

    // ── Settings ─────────────────────────────────────────────────────────────

    /**
     * Everything the wizard was asked for.
     *
     * @param array $input
     * @return array
     */
    public static function settings( array $input )
    {
        $settings = array(
            'name'     => self::safeName( isset( $input['name'] ) ? $input['name'] : '' ),
            'class'    => self::safeClass( isset( $input['class'] ) ? $input['class'] : '' ),
            'title'    => self::text( isset( $input['title'] ) ? $input['title'] : '', 120 ),
            'summary'  => self::text( isset( $input['summary'] ) ? $input['summary'] : '', 250 ),
            'author'   => self::text( isset( $input['author'] ) ? $input['author'] : '', 120 ),
            'vendor'   => self::safeName( isset( $input['vendor'] ) ? $input['vendor'] : '', true ),
            'version'  => self::text( isset( $input['version'] ) ? $input['version'] : '', 20 ),
            'licence'  => self::licence_id( isset( $input['licence'] ) ? $input['licence'] : '' ),
            'module'   => self::safeIdentifier( isset( $input['module'] ) ? $input['module'] : '' ),
        );

        $settings['operators']     = self::nameList( isset( $input['operators'] ) ? $input['operators'] : '' );
        $settings['functions']     = self::nameList( isset( $input['functions'] ) ? $input['functions'] : '' );
        $settings['fetches']       = self::nameList( isset( $input['fetches'] ) ? $input['fetches'] : '' );
        $settings['aliases']       = self::nameList( isset( $input['aliases'] ) ? $input['aliases'] : '' );
        $settings['parameters']    = self::parameterList( isset( $input['parameters'] ) ? $input['parameters'] : '' );

        $settings['input']  = !empty( $input['input'] );
        $settings['output'] = !empty( $input['output'] );
        $settings['children'] = !empty( $input['children'] );

        // On a first visit nothing has been posted at all, and the defaults
        // should stand rather than everything arriving switched off.
        $first = !isset( $input['submitted'] );
        if ( $first )
        {
            $settings['input']  = true;
            $settings['output'] = true;
        }

        $chosenHints = isset( $input['hints'] ) && is_array( $input['hints'] ) ? $input['hints'] : null;
        foreach ( self::hints() as $key => $hint )
            $settings['hints'][$key] = $chosenHints === null ? $hint['default'] : in_array( $key, $chosenHints, true );

        if ( $settings['class'] === '' && $settings['name'] !== '' )
            $settings['class'] = self::safeClass( str_replace( '_', '', $settings['name'] ) . 'Operators' );
        if ( $settings['title'] === '' && $settings['name'] !== '' )
            $settings['title'] = ucwords( str_replace( '_', ' ', $settings['name'] ) );
        if ( $settings['version'] === '' )
            $settings['version'] = '1.0.0';
        if ( $settings['vendor'] === '' )
            $settings['vendor'] = 'exponential';
        if ( $settings['module'] === '' && $settings['name'] !== '' )
            $settings['module'] = self::safeIdentifier( $settings['name'] );
        if ( $settings['summary'] === '' )
            $settings['summary'] = 'Template operators and functions for Exponential.';

        $settings['function_class'] = $settings['class'] !== ''
                                      ? self::safeClass( $settings['class'] . 'Functions' ) : '';

        $chosen = isset( $input['parts'] ) && is_array( $input['parts'] ) ? $input['parts'] : null;
        foreach ( self::parts() as $key => $part )
            $settings['parts'][$key] = $chosen === null ? $part['default'] : in_array( $key, $chosen, true );

        return $settings;
    }

    /**
     * A list of template names, out of one text box.
     *
     * Commas, spaces or newlines, in any mixture: whoever types four names
     * should not have to think about which separator this wants.
     *
     * @param mixed $value
     * @return array of string
     */
    public static function nameList( $value )
    {
        if ( is_array( $value ) )
            $value = implode( ',', array_filter( $value, 'is_scalar' ) );

        if ( !is_scalar( $value ) )
            return array();

        $names = array();
        foreach ( preg_split( '/[\s,;]+/', (string) $value ) as $name )
        {
            $name = self::safeIdentifier( $name );

            if ( $name !== '' && !in_array( $name, $names, true ) )
                $names[] = $name;

            // A template extension declaring hundreds of names is a mistake, not
            // an ambition, and the page has to draw all of them.
            if ( count( $names ) >= 40 )
                break;
        }

        return $names;
    }

    /**
     * The named parameters, out of one text box: one per line, name then type.
     *
     * @param mixed $value
     * @return array of array( name, type, required )
     */
    public static function parameterList( $value )
    {
        if ( !is_scalar( $value ) )
            return array();

        $types = self::parameterTypes();
        $parameters = array();

        foreach ( preg_split( '/[\r\n]+/', (string) $value ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' )
                continue;

            $bits = preg_split( '/[\s,:]+/', $line );
            $name = self::safeIdentifier( isset( $bits[0] ) ? $bits[0] : '' );

            if ( $name === '' )
                continue;

            $type = isset( $bits[1] ) && isset( $types[strtolower( $bits[1] )] )
                    ? strtolower( $bits[1] ) : 'string';

            $required = isset( $bits[2] ) && in_array( strtolower( $bits[2] ), array( 'required', 'yes', 'true' ), true );

            $parameters[] = array( 'name' => $name, 'type' => $type, 'required' => $required );

            if ( count( $parameters ) >= 20 )
                break;
        }

        return $parameters;
    }

    /**
     * A class name: letters and digits, starting with a letter.
     *
     * @param string $value
     * @return string
     */
    public static function safeClass( $value )
    {
        if ( !is_string( $value ) )
            return '';

        $value = preg_replace( '/[^A-Za-z0-9_]+/', '', $value );

        return $value !== null && preg_match( '/^[A-Za-z][A-Za-z0-9_]{2,60}$/', $value ) ? $value : '';
    }

    /**
     * A name a template will type: lower case, letters, digits, underscores.
     *
     * @param mixed $value
     * @return string
     */
    public static function safeIdentifier( $value )
    {
        if ( !is_scalar( $value ) )
            return '';

        $value = strtolower( preg_replace( '/[^A-Za-z0-9_]+/', '_', (string) $value ) );
        $value = trim( (string) $value, '_' );

        return preg_match( '/^[a-z][a-z0-9_]{0,60}$/', $value ) ? $value : '';
    }

    /**
     * What is wrong with these settings.
     *
     * @param array $settings
     * @return array of string
     */
    public static function problems( array $settings )
    {
        $problems = array();

        if ( $settings['name'] === '' )
            $problems[] = 'The extension needs a name: lower case letters, digits and underscores, three to forty one characters, starting with a letter.';

        if ( $settings['name'] !== '' && is_dir( self::extensionPath( $settings['name'] ) ) )
            $problems[] = 'extension/' . $settings['name'] . ' already exists. Choose another name, or remove it first.';

        if ( $settings['class'] === '' )
            $problems[] = 'The class needs a name: letters and digits, starting with a letter.';

        if ( $settings['class'] !== '' && class_exists( $settings['class'] ) )
            $problems[] = 'A class called ' . $settings['class'] . ' already exists on this installation. Choose another name.';

        if ( count( $settings['operators'] ) === 0
             && count( $settings['functions'] ) === 0
             && count( $settings['fetches'] ) === 0
             && count( $settings['aliases'] ) === 0 )
            $problems[] = 'Name at least one operator, function, fetch function or fetch alias, or there is nothing for this extension to add.';

        // A name the engine already answers to would either be shadowed or
        // shadow something, depending on load order - which is not a thing to
        // leave to chance.
        foreach ( self::taken( $settings['operators'] ) as $name )
            $problems[] = 'A template operator called ' . $name . ' already exists. Two operators of the same name cannot both work; choose another.';

        if ( count( $settings['fetches'] ) && $settings['module'] === '' )
            $problems[] = 'Fetch functions live in a module, and the module has no name.';

        if ( count( $settings['fetches'] ) && $settings['module'] !== ''
             && eZModule::exists( $settings['module'] ) !== null )
            $problems[] = 'A module called ' . $settings['module'] . ' already exists. Fetch functions would be added to it rather than to this extension; choose another name.';

        return $problems;
    }

    /**
     * Which of these operator names the engine already answers to.
     *
     * @param array $names
     * @return array of string
     */
    public static function taken( array $names )
    {
        $taken = array();

        // eZTemplate has no hasOperator(); the registry is the public Operators
        // property, and an entry there may be the object or the autoload record
        // that would build it. Either means the name is spoken for.
        $template = eZTemplate::factory();
        $registered = is_array( $template->Operators ) ? $template->Operators : array();

        foreach ( $names as $name )
            if ( isset( $registered[$name] ) )
                $taken[] = $name;

        return $taken;
    }

    // ── What it writes ───────────────────────────────────────────────────────

    /**
     * Every file the extension is made of.
     *
     * @param array $settings
     * @return array
     */
    public static function files( array $settings )
    {
        if ( $settings['name'] === '' || $settings['class'] === '' )
            return array();

        if ( count( $settings['operators'] ) === 0 && count( $settings['functions'] ) === 0
             && count( $settings['fetches'] ) === 0 && count( $settings['aliases'] ) === 0 )
            return array();

        $parts = $settings['parts'];
        $files = array();

        $hasOperators = $parts['operator'] && count( $settings['operators'] );
        $hasFunctions = $parts['function'] && count( $settings['functions'] );
        $hasFetches   = $parts['fetch'] && count( $settings['fetches'] );

        if ( $hasOperators )
            $files['autoloads/' . strtolower( $settings['class'] ) . '.php'] = self::operatorClass( $settings );

        if ( $hasFunctions )
            $files['autoloads/' . strtolower( $settings['function_class'] ) . '.php'] = self::functionClass( $settings );

        if ( $parts['autoload'] && ( $hasOperators || $hasFunctions ) )
            $files['autoloads/eztemplateautoload.php'] = self::autoload( $settings, $hasOperators, $hasFunctions );

        if ( $hasFetches )
        {
            $files['modules/' . $settings['module'] . '/module.php'] = self::moduleStub( $settings );
            $files['modules/' . $settings['module'] . '/function_definition.php'] = self::functionDefinition( $settings );
            $files['classes/' . strtolower( $settings['class'] ) . 'functioncollection.php'] = self::functionCollection( $settings );
        }

        if ( $parts['fetch_alias'] && count( $settings['aliases'] ) )
            $files['settings/fetchalias.ini.append.php'] = self::fetchAliasIni( $settings );

        if ( $parts['settings'] )
        {
            if ( $hasOperators || $hasFunctions )
                $files['settings/site.ini.append.php'] = self::siteIni( $settings );

            if ( $hasFetches )
                $files['settings/module.ini.append.php'] = self::moduleIni( $settings );
        }

        if ( $parts['examples'] )
            $files['doc/examples.php'] = self::examples( $settings );

        if ( $parts['ezinfo'] )
            $files['ezinfo.php'] = self::ezinfo( $settings );

        if ( $parts['extension_xml'] )
            $files['extension.xml'] = self::extensionXml( $settings );

        if ( $parts['composer'] )
            $files['composer.json'] = self::composerJson( $settings );

        if ( $parts['gitignore'] )
            $files['.gitignore'] = self::gitignore( $settings );

        if ( $parts['licence'] )
            $files['LICENSE'] = self::licence( $settings );

        ksort( $files );

        if ( $parts['readme'] )
        {
            $files['README.md'] = self::readme( $settings, array_keys( $files ) );
            ksort( $files );
        }

        return $files;
    }

    /**
     * The lines that switch the extension on.
     *
     * @param array $settings
     * @return string
     */
    public static function activation( array $settings )
    {
        return "[ExtensionSettings]\nActiveExtensions[]=" . $settings['name'];
    }

    /**
     * What a template would write to reach each thing this extension adds.
     *
     * @param array $settings
     * @return array of array( kind, name, example, what )
     */
    public static function usage( array $settings )
    {
        $usage = array();

        $arguments = array();
        foreach ( $settings['parameters'] as $parameter )
            $arguments[] = $parameter['name'] . '=' . self::sampleFor( $parameter['type'] );

        $call = count( $arguments ) ? '( ' . implode( ', ', $arguments ) . ' )' : '';

        foreach ( $settings['operators'] as $name )
        {
            $example = '{';
            $example .= $settings['input'] ? '$value|' : '';
            $example .= $name . $call;
            $example .= $settings['output'] ? '|wash' : '';
            $example .= '}';

            $usage[] = array( 'kind' => 'operator', 'name' => $name, 'example' => $example,
                              'what' => $settings['input']
                                        ? 'Takes the value on the left and gives one back.'
                                        : 'Takes no input; everything it needs is a parameter.' );
        }

        foreach ( $settings['functions'] as $name )
        {
            $example = '{' . $name . ( count( $arguments ) ? ' ' . implode( ' ', $arguments ) : '' ) . '}';
            if ( $settings['children'] )
                $example .= "\n    ...\n{/" . $name . '}';

            $usage[] = array( 'kind' => 'function', 'name' => $name, 'example' => $example,
                              'what' => $settings['children']
                                        ? 'Has a body, which it may draw none, one or many times.'
                                        : 'Writes output where it stands.' );
        }

        foreach ( $settings['fetches'] as $name )
            $usage[] = array( 'kind' => 'fetch', 'name' => $name,
                              'example' => '{def $thing=fetch( ' . $settings['module'] . ', ' . $name . ', hash( id, 42 ) )}',
                              'what' => 'Reads something and gives it back. Runs a policy check first, unlike an operator.' );

        foreach ( $settings['aliases'] as $name )
            $usage[] = array( 'kind' => 'alias', 'name' => $name,
                              'example' => '{def $thing=fetch_alias( ' . $name . ' )}',
                              'what' => 'A fetch with its arguments already decided, called by one short name.' );

        return $usage;
    }

    /**
     * A plausible value of a type, for an example.
     *
     * @param string $type
     * @return string
     */
    protected static function sampleFor( $type )
    {
        switch ( $type )
        {
            case 'integer': return '3';
            case 'float':   return '1.5';
            case 'boolean': return 'true()';
            case 'array':   return 'array( 1, 2 )';
        }

        return "'text'";
    }

    // ── The operator class ───────────────────────────────────────────────────

    /**
     * The class behind the operators.
     *
     * @param array $settings
     * @return string
     */
    protected static function operatorClass( array $settings )
    {
        $class = $settings['class'];
        $types = self::parameterTypes();

        $php  = "<?php\n/**\n * " . $class . " - template operators.\n *\n";
        $php .= " * " . wordwrap( $settings['summary'], 74, "\n * " ) . "\n *\n";
        $php .= " * Adds " . self::listOf( $settings['operators'] ) . " to the template language.\n *\n";
        $php .= " * Registered in autoloads/eztemplateautoload.php, which is the file the engine\n";
        $php .= " * reads. There is no ini that names this class.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "class " . $class . "\n{\n";

        $php .= "    /**\n";
        $php .= "     * The operator names this class answers to.\n";
        $php .= "     */\n";
        $php .= "    protected \$Operators;\n\n";

        $php .= "    public function __construct()\n    {\n";
        $php .= "        \$this->Operators = array( ";
        $names = array();
        foreach ( $settings['operators'] as $name )
            $names[] = "'" . self::phpString( $name ) . "'";
        $php .= implode( ",\n                                  ", $names ) . " );\n";
        $php .= "    }\n\n";

        $php .= "    /**\n";
        $php .= "     * What this class provides. The engine asks once, at registration.\n";
        $php .= "     *\n";
        $php .= "     * @return array\n";
        $php .= "     */\n";
        $php .= "    public function operatorList()\n    {\n";
        $php .= "        return \$this->Operators;\n";
        $php .= "    }\n\n";

        $php .= "    /**\n";
        $php .= "     * Whether the parameters are named rather than positional.\n";
        $php .= "     *\n";
        $php .= "     * True means a template writes " . ( count( $settings['parameters'] )
                ? '{$value|' . $settings['operators'][0] . '( ' . $settings['parameters'][0]['name'] . '=1 )}'
                : '{$value|' . ( $settings['operators'] ? $settings['operators'][0] : 'op' ) . '()}' ) . " and\n";
        $php .= "     * this class is handed a hash. False means it is handed a list, in order,\n";
        $php .= "     * and has to know what each position meant.\n";
        $php .= "     *\n";
        $php .= "     * @return bool\n";
        $php .= "     */\n";
        $php .= "    public function namedParameterPerOperator()\n    {\n";
        $php .= "        return true;\n";
        $php .= "    }\n\n";

        $php .= "    /**\n";
        $php .= "     * Every parameter each operator takes, with its type and its default.\n";
        $php .= "     *\n";
        $php .= "     * A parameter left out of a template arrives here as the default below, so\n";
        $php .= "     * nothing has to check whether it was given.\n";
        $php .= "     *\n";
        $php .= "     * @return array\n";
        $php .= "     */\n";
        $php .= "    public function namedParameterList()\n    {\n";

        $parameterBlock = "        \$parameters = array(\n";
        if ( count( $settings['parameters'] ) === 0 )
        {
            $parameterBlock .= "            // No parameters were asked for. Add them here as\n";
            $parameterBlock .= "            //     'name' => array( 'type' => 'string',\n";
            $parameterBlock .= "            //                      'required' => false,\n";
            $parameterBlock .= "            //                      'default' => false )\n";
        }
        foreach ( $settings['parameters'] as $parameter )
        {
            $type = $types[$parameter['type']];
            $parameterBlock .= "            '" . self::phpString( $parameter['name'] ) . "' => array(\n";
            $parameterBlock .= "                'type'     => '" . $type['declared'] . "',\n";
            $parameterBlock .= "                'required' => " . ( $parameter['required'] ? 'true' : 'false' ) . ",\n";
            $parameterBlock .= "                'default'  => " . $type['default'] . " ),\n";
        }
        $parameterBlock .= "        );\n\n";

        $php .= $parameterBlock;
        $php .= "        // The same list for every operator this class provides. Give one of\n";
        $php .= "        // them a list of its own by keying it differently here.\n";
        $php .= "        \$list = array();\n";
        $php .= "        foreach ( \$this->Operators as \$operator )\n";
        $php .= "            \$list[\$operator] = \$parameters;\n\n";
        $php .= "        return \$list;\n";
        $php .= "    }\n\n";

        $php .= self::hintMethod( $settings );

        $php .= "\n    /**\n";
        $php .= "     * The operator itself.\n";
        $php .= "     *\n";
        $php .= "     * \$operatorValue is what was on the left of the pipe on the way in, and\n";
        $php .= "     * what the template will print on the way out. It is passed by reference:\n";
        $php .= "     * nothing is returned, the value is changed in place.\n";
        $php .= "     *\n";
        $php .= "     * Whatever is left in it goes on the page. If it can contain anything an\n";
        $php .= "     * editor typed, either leave it for the template to wash or wash it here -\n";
        $php .= "     * but be sure which, because doing neither is how an operator becomes the\n";
        $php .= "     * way scripts get onto a page.\n";
        $php .= "     */\n";
        $php .= "    public function modify( \$tpl, \$operatorName, \$operatorParameters, \$rootNamespace,\n";
        $php .= "                            \$currentNamespace, &\$operatorValue, \$namedParameters,\n";
        $php .= "                            \$placement )\n    {\n";
        $php .= "        switch ( \$operatorName )\n        {\n";

        foreach ( $settings['operators'] as $name )
        {
            $php .= "            case '" . self::phpString( $name ) . "':\n";
            $php .= "            {\n";

            foreach ( $settings['parameters'] as $parameter )
                $php .= "                \$" . $parameter['name'] . " = \$namedParameters['"
                      . self::phpString( $parameter['name'] ) . "'];\n";

            if ( count( $settings['parameters'] ) )
                $php .= "\n";

            $php .= "                // Not written yet. Leaving the value alone means a template\n";
            $php .= "                // using this operator today prints what it printed before.\n";

            if ( !$settings['input'] )
            {
                $php .= "                //\n";
                $php .= "                // This operator takes no input, so set the value rather than\n";
                $php .= "                // change it:\n";
                $php .= "                //     \$operatorValue = ...;\n";
            }

            $php .= "            } break;\n";
        }

        $php .= "        }\n";
        $php .= "    }\n";
        $php .= "}\n";

        return $php;
    }

    /**
     * operatorTemplateHints(), or nothing when nothing is promised.
     *
     * @param array $settings
     * @return string
     */
    protected static function hintMethod( array $settings )
    {
        $php  = "    /**\n";
        $php .= "     * What this promises the compiler.\n";
        $php .= "     *\n";
        $php .= "     * A template is compiled once and run many times. What is promised here\n";
        $php .= "     * decides how much of the work happens at compile time and how much on\n";
        $php .= "     * every request, for ever.\n";
        $php .= "     *\n";
        $php .= "     * @return array\n";
        $php .= "     */\n";
        $php .= "    public function operatorTemplateHints()\n    {\n";
        $php .= "        \$hints = array(\n";

        foreach ( self::hints() as $key => $hint )
        {
            $php .= "            // " . wordwrap( $hint['what'], 66, "\n            // " ) . "\n";
            $php .= "            '" . $key . "' => " . ( !empty( $settings['hints'][$key] ) ? 'true' : 'false' ) . ",\n";
        }

        $php .= "            'input-as-parameter' => " . ( !empty( $settings['hints']['input-as-parameter'] ) ? 'true' : 'false' ) . ",\n";
        $php .= "        );\n\n";
        $php .= "        \$list = array();\n";
        $php .= "        foreach ( \$this->Operators as \$operator )\n";
        $php .= "            \$list[\$operator] = \$hints;\n\n";
        $php .= "        return \$list;\n";
        $php .= "    }\n";

        return $php;
    }

    // ── The function class ───────────────────────────────────────────────────

    /**
     * A template function, which is a different thing from an operator.
     *
     * @param array $settings
     * @return string
     */
    protected static function functionClass( array $settings )
    {
        $class = $settings['function_class'];
        $types = self::parameterTypes();

        $php  = "<?php\n/**\n * " . $class . " - template functions.\n *\n";
        $php .= " * Adds " . self::listOf( $settings['functions'] ) . " to the template language.\n *\n";
        $php .= " * A function is not an operator: it is written {name arg=1} rather than\n";
        $php .= " * {\$value|name}, it takes no input from the left, and it writes output rather\n";
        $php .= " * than giving a value back";
        $php .= $settings['children'] ? ". It also has a body, which it may draw\n * none, one or many times.\n" : ".\n";
        $php .= " *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "class " . $class . "\n{\n";

        $php .= "    protected \$Functions;\n\n";

        $php .= "    public function __construct()\n    {\n";
        $php .= "        \$this->Functions = array( ";
        $names = array();
        foreach ( $settings['functions'] as $name )
            $names[] = "'" . self::phpString( $name ) . "'";
        $php .= implode( ",\n                                  ", $names ) . " );\n";
        $php .= "    }\n\n";

        $php .= "    /**\n     * The function names this class answers to.\n     */\n";
        $php .= "    public function functionList()\n    {\n";
        $php .= "        return \$this->Functions;\n";
        $php .= "    }\n\n";

        $php .= "    /**\n";
        $php .= "     * The attributes each function takes.\n";
        $php .= "     *\n";
        $php .= "     * Unlike an operator's parameters these are not typed and have no\n";
        $php .= "     * defaults: the list simply says which names are allowed, and anything\n";
        $php .= "     * not in it is a template error rather than a silent nothing.\n";
        $php .= "     */\n";
        $php .= "    public function attributeList()\n    {\n";
        $php .= "        return array(\n";

        if ( count( $settings['parameters'] ) === 0 )
            $php .= "            // No attributes were asked for. Add them as 'name' => true.\n";

        foreach ( $settings['parameters'] as $parameter )
            $php .= "            '" . self::phpString( $parameter['name'] ) . "' => true,\n";

        $php .= "        );\n";
        $php .= "    }\n\n";

        $php .= "    /**\n";
        $php .= "     * Whether these functions have a body.\n";
        $php .= "     *\n";
        $php .= "     * True means a template writes {name}...{/name} and the body arrives as\n";
        $php .= "     * \$functionChildren, unprocessed, for this class to draw as often as it\n";
        $php .= "     * likes - which is how {section} and {foreach} work.\n";
        $php .= "     */\n";
        $php .= "    public function hasChildren()\n    {\n";
        $php .= "        return " . ( $settings['children'] ? 'true' : 'false' ) . ";\n";
        $php .= "    }\n\n";

        $php .= "    /**\n";
        $php .= "     * The function itself.\n";
        $php .= "     *\n";
        $php .= "     * Output is appended to \$textElements rather than returned. Everything\n";
        $php .= "     * appended goes on the page exactly as it is, so anything that came from\n";
        $php .= "     * content has to be escaped here - there is no template filter after this.\n";
        $php .= "     */\n";
        $php .= "    public function process( \$tpl, &\$textElements, \$functionName, \$functionChildren,\n";
        $php .= "                             \$functionParameters, \$functionPlacement, \$rootNamespace,\n";
        $php .= "                             \$currentNamespace )\n    {\n";

        foreach ( $settings['parameters'] as $parameter )
        {
            $php .= "        \$" . $parameter['name'] . " = isset( \$functionParameters['"
                  . self::phpString( $parameter['name'] ) . "'] )\n";
            $php .= "                  ? \$tpl->elementValue( \$functionParameters['"
                  . self::phpString( $parameter['name'] ) . "'], \$rootNamespace, \$currentNamespace, \$functionPlacement )\n";
            $php .= "                  : " . $types[$parameter['type']]['default'] . ";\n";
        }

        if ( count( $settings['parameters'] ) )
            $php .= "\n";

        $php .= "        switch ( \$functionName )\n        {\n";

        foreach ( $settings['functions'] as $name )
        {
            $php .= "            case '" . self::phpString( $name ) . "':\n";
            $php .= "            {\n";
            $php .= "                // Not written yet. Appending nothing means a template using\n";
            $php .= "                // this function today prints what it printed before.\n";
            $php .= "                //\n";
            $php .= "                //     \$textElements[] = htmlspecialchars( \$something, ENT_QUOTES, 'UTF-8' );\n";

            if ( $settings['children'] )
            {
                $php .= "                //\n";
                $php .= "                // To draw the body once:\n";
                $php .= "                //     \$tpl->processChildren( \$functionChildren, \$textElements,\n";
                $php .= "                //                           \$rootNamespace, \$currentNamespace );\n";
            }

            $php .= "            } break;\n";
        }

        $php .= "        }\n\n";
        $php .= "        return true;\n";
        $php .= "    }\n";
        $php .= "}\n";

        return $php;
    }

    /**
     * The file the template engine actually reads.
     *
     * @param array $settings
     * @param bool $hasOperators
     * @param bool $hasFunctions
     * @return string
     */
    protected static function autoload( array $settings, $hasOperators, $hasFunctions )
    {
        $base = 'extension/' . $settings['name'] . '/autoloads/';

        $php  = "<?php\n/**\n * What this extension adds to the template language.\n *\n";
        $php .= " * The engine reads this file - not an ini - when it builds its list of\n";
        $php .= " * operators and functions. The paths below are from the installation root and\n";
        $php .= " * are used verbatim, so they have to be right even though nothing checks them\n";
        $php .= " * until a template asks for one of these names.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";

        if ( $hasOperators )
        {
            $php .= "\$eZTemplateOperatorArray = array();\n\n";
            $php .= "\$eZTemplateOperatorArray[] = array(\n";
            $php .= "    'script' => '" . self::phpString( $base . strtolower( $settings['class'] ) . '.php' ) . "',\n";
            $php .= "    'class'  => '" . self::phpString( $settings['class'] ) . "',\n";
            $php .= "    'operator_names' => array( ";

            $names = array();
            foreach ( $settings['operators'] as $name )
                $names[] = "'" . self::phpString( $name ) . "'";
            $php .= implode( ", ", $names ) . " ) );\n\n";
        }

        if ( $hasFunctions )
        {
            $php .= "\$eZTemplateFunctionArray = array();\n\n";
            $php .= "\$eZTemplateFunctionArray[] = array(\n";
            $php .= "    'script' => '" . self::phpString( $base . strtolower( $settings['function_class'] ) . '.php' ) . "',\n";
            $php .= "    'class'  => '" . self::phpString( $settings['function_class'] ) . "',\n";
            $php .= "    'function_names' => array( ";

            $names = array();
            foreach ( $settings['functions'] as $name )
                $names[] = "'" . self::phpString( $name ) . "'";
            $php .= implode( ", ", $names ) . " ) );\n\n";
        }

        $php .= "?>\n";

        return $php;
    }

    // ── Fetch functions ──────────────────────────────────────────────────────

    /**
     * A module that exists only to carry fetch functions.
     *
     * @param array $settings
     * @return string
     */
    protected static function moduleStub( array $settings )
    {
        $php  = "<?php\n/**\n * The " . self::commentText( $settings['module'] ) . " module.\n *\n";
        $php .= " * It has no views: it exists so that the fetch functions beside it have a\n";
        $php .= " * module to belong to. A fetch is always fetch( <module>, <function> ), and\n";
        $php .= " * the module is where its policy check comes from.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "\$Module = array( 'name' => '" . self::phpString( $settings['module'] ) . "' );\n\n";
        $php .= "// No views. Add one here if this module grows a page of its own:\n";
        $php .= "//     \$ViewList['mypage'] = array( 'script' => 'mypage.php',\n";
        $php .= "//                                  'functions' => array( 'read' ) );\n";
        $php .= "\$ViewList = array();\n\n";
        $php .= "// The policies a role can grant on this module. A fetch function names one\n";
        $php .= "// of these in its operation_types, and anybody without it gets nothing back.\n";
        $php .= "\$FunctionList = array( 'read' => array() );\n";

        return $php;
    }

    /**
     * The fetch functions themselves.
     *
     * @param array $settings
     * @return string
     */
    protected static function functionDefinition( array $settings )
    {
        $collection = $settings['class'] . 'FunctionCollection';

        $php  = "<?php\n/**\n * Fetch functions for the " . self::commentText( $settings['module'] ) . " module.\n *\n";
        $php .= " * This file is read, not called: the kernel includes it and looks at\n";
        $php .= " * \$FunctionList. Every entry says what a template may ask for, which class\n";
        $php .= " * and method answer, and what may be passed.\n *\n";
        $php .= " * A parameter not declared here cannot be passed at all, which is the first\n";
        $php .= " * line of defence a fetch function has.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "\$FunctionList = array();\n\n";

        foreach ( $settings['fetches'] as $name )
        {
            $method = 'fetch' . str_replace( ' ', '', ucwords( str_replace( '_', ' ', $name ) ) );

            $php .= "\$FunctionList['" . self::phpString( $name ) . "'] = array(\n";
            $php .= "    'name' => '" . self::phpString( $name ) . "',\n\n";
            $php .= "    // The policy somebody needs before this answers at all. Every fetch\n";
            $php .= "    // is checked against the current user, which is the difference between\n";
            $php .= "    // a fetch function and an operator.\n";
            $php .= "    'operation_types' => array( 'read' ),\n\n";
            $php .= "    'call_method' => array( 'class'  => '" . self::phpString( $collection ) . "',\n";
            $php .= "                            'method' => '" . self::phpString( $method ) . "' ),\n\n";
            $php .= "    'parameter_type' => 'standard',\n";
            $php .= "    'parameters' => array(\n";

            if ( count( $settings['parameters'] ) === 0 )
            {
                $php .= "        // No parameters were asked for. Add them as\n";
                $php .= "        //     array( 'name' => 'id', 'type' => 'integer',\n";
                $php .= "        //            'required' => true, 'default' => false )\n";
            }

            foreach ( $settings['parameters'] as $parameter )
            {
                $php .= "        array( 'name'     => '" . self::phpString( $parameter['name'] ) . "',\n";
                $php .= "               'type'     => '" . self::phpString( $parameter['type'] ) . "',\n";
                $php .= "               'required' => " . ( $parameter['required'] ? 'true' : 'false' ) . ",\n";
                $php .= "               'default'  => false ),\n";
            }

            $php .= "    ) );\n\n";
        }

        $php .= "?>\n";

        return $php;
    }

    /**
     * The class the fetch functions call into.
     *
     * @param array $settings
     * @return string
     */
    protected static function functionCollection( array $settings )
    {
        $class = $settings['class'] . 'FunctionCollection';

        $php  = "<?php\n/**\n * " . $class . " - what the fetch functions call.\n *\n";
        $php .= " * Each method answers one fetch. The shape of the answer is fixed: an array\n";
        $php .= " * with a 'result' key, and nothing else is read. Returning the value straight\n";
        $php .= " * gives the template nothing, silently, which is a hard afternoon.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "class " . $class . "\n{\n";

        $first = true;
        foreach ( $settings['fetches'] as $name )
        {
            $method = 'fetch' . str_replace( ' ', '', ucwords( str_replace( '_', ' ', $name ) ) );

            $arguments = array();
            foreach ( $settings['parameters'] as $parameter )
                $arguments[] = '$' . $parameter['name'];

            if ( !$first )
                $php .= "\n";
            $first = false;

            $php .= "    /**\n";
            $php .= "     * fetch( " . self::commentText( $settings['module'] ) . ", " . self::commentText( $name ) . " )\n";
            $php .= "     *\n";
            $php .= "     * Every argument arrives exactly as a template wrote it, which means it\n";
            $php .= "     * may have come from a url. The declared type is a note, not a check:\n";
            $php .= "     * cast anything that reaches a query or a path.\n";
            $php .= "     *\n";
            $php .= "     * @return array with one 'result' key.\n";
            $php .= "     */\n";
            $php .= "    public static function " . $method . "( " . implode( ', ', $arguments ) . " )\n    {\n";
            $php .= "        // Not written yet. An empty result is what a fetch that found\n";
            $php .= "        // nothing gives back, so a template using it already behaves.\n";
            $php .= "        return array( 'result' => array() );\n";
            $php .= "    }\n";
        }

        $php .= "}\n";

        return $php;
    }

    // ── Registration ─────────────────────────────────────────────────────────

    /**
     * site.ini: where to look for the autoload file.
     *
     * @param array $settings
     * @return string
     */
    protected static function siteIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Template autoload registration' );
        $ini .= "[TemplateSettings]\n";
        $ini .= "# The extension whose autoloads/eztemplateautoload.php is read. Without this\n";
        $ini .= "# line the classes are never loaded and every name below is an unknown\n";
        $ini .= "# operator, reported in the debug output and nowhere else.\n";
        $ini .= "ExtensionAutoloadPath[]=" . $settings['name'] . "\n";
        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    /**
     * module.ini: where to look for the module carrying the fetch functions.
     *
     * @param array $settings
     * @return string
     */
    protected static function moduleIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Module registration' );
        $ini .= "[ModuleSettings]\n";
        $ini .= "# The extension whose modules/ directory is searched, and the module inside\n";
        $ini .= "# it. Both lines are needed: the first says where to look, the second what\n";
        $ini .= "# to look for.\n";
        $ini .= "ExtensionRepositories[]=" . $settings['name'] . "\n";
        $ini .= "ModuleList[]=" . $settings['module'] . "\n";
        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    /**
     * fetchalias.ini: a long fetch under a short name.
     *
     * @param array $settings
     * @return string
     */
    protected static function fetchAliasIni( array $settings )
    {
        $module   = $settings['module'] !== '' ? $settings['module'] : 'content';
        $function = count( $settings['fetches'] ) ? $settings['fetches'][0] : 'list';

        $ini  = self::iniHeader( $settings, 'Fetch aliases' );
        $ini .= "# A fetch alias is a fetch with its arguments already decided. Templates then\n";
        $ini .= "# call it by one short name, and the arguments are changed here rather than\n";
        $ini .= "# in every template that uses it.\n";
        $ini .= "#\n";
        $ini .= "# Parameter[<name>] takes its value from the hash the template passes.\n";
        $ini .= "# Constant[<name>] fixes it here. Use ; to separate the parts of an array.\n";

        foreach ( $settings['aliases'] as $alias )
        {
            $ini .= "\n[" . $alias . "]\n";
            $ini .= "Module=" . $module . "\n";
            $ini .= "FunctionName=" . $function . "\n";

            foreach ( $settings['parameters'] as $parameter )
                $ini .= "Parameter[" . $parameter['name'] . "]=" . $parameter['name'] . "\n";

            if ( count( $settings['parameters'] ) === 0 )
            {
                $ini .= "# Nothing is passed and nothing is fixed yet. For example:\n";
                $ini .= "# Parameter[id]=id\n";
                $ini .= "# Constant[limit]=10\n";
            }
        }

        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    // ── Documentation ────────────────────────────────────────────────────────

    /**
     * Worked examples, in templates and out of them.
     *
     * @param array $settings
     * @return string
     */
    protected static function examples( array $settings )
    {
        $php  = "<?php\n/**\n * " . $settings['title'] . " - worked examples.\n *\n";
        $php .= " * Not part of the extension: a file to read, and to copy lines out of. It is\n";
        $php .= " * under doc/ rather than autoloads/ so nothing loads it by accident.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "// Nothing below runs on its own.\nreturn;\n\n";

        $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 In a template " . str_repeat( "\xe2\x94\x80", 49 ) . "\n//\n";
        foreach ( self::usage( $settings ) as $use )
        {
            $php .= "// " . $use['what'] . "\n//\n";
            foreach ( explode( "\n", $use['example'] ) as $line )
                $php .= "//     " . $line . "\n";
            $php .= "//\n";
        }
        $php .= "\n\n";

        if ( count( $settings['operators'] ) )
        {
            $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 Trying an operator without a page " . str_repeat( "\xe2\x94\x80", 29 ) . "\n//\n";
            $php .= "// The engine can be driven from a script, which is much faster than\n";
            $php .= "// editing a template and reloading to find out what an operator did.\n\n";
            $php .= "\$tpl = eZTemplate::factory();\n";
            $php .= "\$tpl->setVariable( 'value', 'something' );\n\n";
            $php .= "echo \$tpl->fetch( 'string:{\$value|" . $settings['operators'][0] . "}' ), PHP_EOL;\n\n";
            $php .= "// Anything the engine complained about:\n";
            $php .= "print_r( \$tpl->errorList() );\n\n\n";

            $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 Where the value comes from and goes " . str_repeat( "\xe2\x94\x80", 27 ) . "\n//\n";
            $php .= "// modify() is handed \$operatorValue by reference. It arrives holding\n";
            $php .= "// whatever was on the left of the pipe and leaves holding whatever the\n";
            $php .= "// template will print. There is no return value.\n";
            $php .= "//\n";
            $php .= "// {\$a|" . $settings['operators'][0] . "|" . $settings['operators'][0] . "} calls it twice, the second time\n";
            $php .= "// with what the first left behind.\n\n\n";
        }

        if ( count( $settings['fetches'] ) )
        {
            $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 Calling a fetch from php " . str_repeat( "\xe2\x94\x80", 39 ) . "\n//\n";
            $php .= "// The same policy check a template gets, so this is also how to find out\n";
            $php .= "// whether a fetch returns nothing because it found nothing or because the\n";
            $php .= "// current user may not see it.\n\n";
            $php .= "\$result = eZFunctionHandler::execute( '" . self::phpString( $settings['module'] ) . "',\n";
            $php .= "                                     '" . self::phpString( $settings['fetches'][0] ) . "',\n";
            $php .= "                                     array() );\n";
            $php .= "print_r( \$result );\n\n";
            $php .= "// And the collection method directly, with no policy check at all:\n";
            $php .= "print_r( " . $settings['class'] . "FunctionCollection::fetch"
                  . str_replace( ' ', '', ucwords( str_replace( '_', ' ', $settings['fetches'][0] ) ) ) . "() );\n\n\n";
        }

        $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 When a name is not found " . str_repeat( "\xe2\x94\x80", 38 ) . "\n//\n";
        $php .= "// An unknown operator is reported in the debug output and nowhere else: the\n";
        $php .= "// page renders, without it. Three things to check, in this order:\n";
        $php .= "//\n";
        $php .= "//   1. The extension is in ActiveExtensions[].\n";
        $php .= "//   2. It is in [TemplateSettings] ExtensionAutoloadPath[] as well - being\n";
        $php .= "//      active is not enough, and this is the line that is forgotten.\n";
        $php .= "//   3. The caches are cleared. The operator list is itself cached.\n";
        $php .= "//\n";
        $php .= "//      php bin/php/ezcache.php --clear-all\n";

        return $php;
    }

    /**
     * A readable list of names.
     *
     * @param array $names
     * @return string
     */
    protected static function listOf( array $names )
    {
        $names = array_map( array( __CLASS__, 'commentText' ), $names );

        if ( count( $names ) === 0 )
            return 'nothing';

        if ( count( $names ) === 1 )
            return $names[0];

        $last = array_pop( $names );

        return implode( ', ', $names ) . ' and ' . $last;
    }

    /**
     * What it adds, how to switch it on, and what each name does.
     *
     * @param array $settings
     * @param array $paths
     * @return string
     */
    protected static function readme( array $settings, array $paths = array() )
    {
        $readme  = "# " . $settings['title'] . "\n\n" . $settings['summary'] . "\n\n";

        $readme .= "## What it adds\n\n";
        foreach ( self::usage( $settings ) as $use )
        {
            $readme .= "### `" . $use['name'] . "` (" . $use['kind'] . ")\n\n";
            $readme .= $use['what'] . "\n\n";
            $readme .= "```\n" . $use['example'] . "\n```\n\n";
        }

        $readme .= "## Switching it on\n\n";
        $readme .= "1. Put this directory in `extension/" . $settings['name'] . "`.\n";
        $readme .= "2. Add it to `settings/override/site.ini.append.php`:\n\n";
        $readme .= "```\n" . self::activation( $settings ) . "\n```\n\n";
        $readme .= "3. Regenerate the autoloads and clear the caches:\n\n";
        $readme .= "```\nphp bin/php/ezpgenerateautoloads.php --extension=" . $settings['name'] . "\nphp bin/php/ezcache.php --clear-all\n```\n\n";

        if ( count( $settings['operators'] ) || count( $settings['functions'] ) )
        {
            $readme .= "The extension brings its own `site.ini` adding itself to\n";
            $readme .= "`[TemplateSettings] ExtensionAutoloadPath[]`. That line, and not being\n";
            $readme .= "active, is what makes the names below work. Forgetting it is the usual\n";
            $readme .= "reason an operator is reported unknown.\n\n";
        }

        if ( count( $settings['parameters'] ) )
        {
            $readme .= "## Parameters\n\n| Name | Type | Required |\n| --- | --- | --- |\n";
            foreach ( $settings['parameters'] as $parameter )
                $readme .= "| `" . $parameter['name'] . "` | " . $parameter['type'] . " | "
                         . ( $parameter['required'] ? 'yes' : 'no' ) . " |\n";
            $readme .= "\n";
        }

        if ( count( $paths ) )
        {
            $readme .= "## What is in here\n\n```\n";
            foreach ( $paths as $path )
                $readme .= $path . "\n";
            $readme .= "```\n\n";
        }

        $readme .= "## Before it is used for real\n\n";
        $readme .= "Nothing here does anything yet. An operator leaves the value it was given\n";
        $readme .= "alone, a function writes nothing, a fetch returns an empty result. A\n";
        $readme .= "template using any of them today prints what it printed before, which is\n";
        $readme .= "what makes this safe to install before it is written.\n";

        return $readme;
    }
}

?>
