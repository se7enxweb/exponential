<?php
/**
 * File containing the expHelpExamplesGenerator class.
 *
 * Writes the generated reference examples below doc/examples/ (doc/examples/README.md, "How the generated pages are
 * made"): one Markdown page per template operator, template function, fetch function, module view, module policy
 * list, INI block, datatype, workflow event type, cronjob script and console command, plus the extension points
 * (INI-driven handler lists), each area with an index page. Every page is derived from the git-tracked sources of the
 * installation: the template autoload files, function_definition.php, module.php, operation_definition.php, the INI
 * files below settings/ and extension/<name>/settings/, the datatype and workflow event classes, cronjob.ini and the
 * command classes. Nothing is invented: parameters, defaults, policies and settings are read from the code; values
 * that only illustrate (placeholders in a call) and classifications that are guesses (extension points) say so on the
 * page.
 *
 * Generation is deterministic: the same sources give byte-identical pages (sorted input, no dates, no host names, no
 * paths outside the installation), so "./console exp:help:examples --check" can tell whether the committed pages are
 * current. A generated page carries "generated: exp:help:examples" in its front matter; only such pages are ever
 * removed when their source is gone. Hand-written pages (the web server examples, the cookbook) are never touched.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpExamplesGenerator
{
    /** The front matter marker of a generated page */
    const MARKER = 'exp:help:examples';

    /** The page type of the generated pages (exphelp.ini [TypeSettings] TypeNames[generated]) */
    const PAGE_TYPE = 'generated';

    /** The areas, in the order of the index: directory => title */
    const AREAS = array(
        'template-operators' => 'Template operators',
        'template-functions' => 'Template functions',
        'fetch-functions'    => 'Fetch functions',
        'module-views'       => 'Module views',
        'policies'           => 'Policy functions and limitations',
        'ini-settings'       => 'INI settings',
        'datatypes'          => 'Datatypes',
        'workflow-events'    => 'Workflow event types and triggers',
        'cronjobs'           => 'Cronjob scripts and parts',
        'commands'           => 'Console commands',
        'extension-points'   => 'Extension points',
    );

    /** Settings whose name says they name classes, handlers or directories others can add to (a heuristic) */
    const EXTENSION_POINT_PATTERN = '/(Handlers?|HandlerList|HandlerClass(es)?|Class(es|List|Name)?|Filters?|FilterList|Providers?|Listeners?|Plugins?|ExtensionDirector(y|ies)|Repositories|Implementations?|Callbacks?|Hooks?|Connectors?|Factor(y|ies)|Converters?|Transports?|Sinks?|Checks?|Formatters?|Resolvers?|Loaders?)$/';

    /** @var string the installation root, without a trailing slash */
    protected $root;

    /** @var string the output directory relative to the root */
    protected $out;

    /** @var string[] the tracked files, relative to the root, sorted */
    protected $files;

    /** @var array path => content of the pages written in this run */
    protected $pages = array();

    /** @var array area => count */
    protected $counts = array();

    /** @var array name => list( path, line, text ) first use in a template, built on demand */
    protected $templateUses = null;

    /** @var array class name => file, from the kernel autoload array and a scan of the tracked PHP files */
    protected $classFiles = null;

    /** @var array|null modules(), read once */
    protected $modulesCache = null;

    /** @var string[] notes about sources that could not be read */
    protected $warnings = array();

    /**
     * @param string $root the installation root
     * @param string $out the output directory, relative to the root
     * @param string[]|null $files the tracked files (null: asks git, else scans the source directories)
     */
    public function __construct( $root, $out = 'doc/examples', ?array $files = null )
    {
        $this->root = rtrim( $root, '/' );
        $this->out = trim( $out, '/' );
        $this->files = $files !== null ? $files : self::trackedFiles( $this->root );
        sort( $this->files, SORT_STRING );
    }

    /**
     * The git-tracked files of $root, or (without git) the files of the source directories the generator reads.
     *
     * @param string $root
     * @return string[]
     */
    public static function trackedFiles( $root )
    {
        $list = array();
        if ( is_dir( $root . '/.git' ) || is_file( $root . '/.git' ) )
        {
            $pipes = array();
            $process = @proc_open( array( 'git', '-C', $root, 'ls-files', '-z' ), array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
            if ( is_resource( $process ) )
            {
                $output = stream_get_contents( $pipes[1] );
                fclose( $pipes[1] );
                fclose( $pipes[2] );
                if ( proc_close( $process ) === 0 )
                    $list = array_values( array_filter( explode( "\0", $output ), 'strlen' ) );
            }
        }
        if ( !$list )
        {
            foreach ( array( 'bin', 'cronjobs', 'design', 'extension', 'kernel', 'lib', 'settings' ) as $dir )
            {
                if ( !is_dir( "$root/$dir" ) )
                    continue;
                $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( "$root/$dir", FilesystemIterator::SKIP_DOTS ) );
                foreach ( $iterator as $file )
                    if ( $file->isFile() )
                        $list[] = substr( $file->getPathname(), strlen( $root ) + 1 );
            }
        }
        sort( $list, SORT_STRING );
        return $list;
    }

    /**
     * Generates every area into memory.
     *
     * @param string[]|null $areas only these areas (keys of AREAS); null: all
     * @return array path (relative to the root) => content
     */
    public function build( ?array $areas = null )
    {
        $this->pages = array();
        $this->counts = array();
        foreach ( array_keys( self::AREAS ) as $area )
        {
            if ( $areas !== null && !in_array( $area, $areas, true ) )
                continue;
            $method = 'area' . str_replace( ' ', '', ucwords( str_replace( '-', ' ', $area ) ) );
            $this->counts[$area] = 0;
            $this->$method();
        }
        ksort( $this->pages, SORT_STRING );
        return $this->pages;
    }

    /** @return array area => number of item pages (index pages not counted) */
    public function counts()
    {
        return $this->counts;
    }

    /** @return string[] */
    public function warnings()
    {
        return $this->warnings;
    }

    /**
     * Writes the pages of build() below the root, and removes generated pages of the built areas that were not
     * written again (their source is gone). Files that are not generated pages are never touched.
     *
     * @param array $pages path => content
     * @param string[]|null $areas the areas that were built (null: all)
     * @return array array( 'written' => n, 'unchanged' => n, 'removed' => n )
     */
    public function write( array $pages, ?array $areas = null )
    {
        $stats = array( 'written' => 0, 'unchanged' => 0, 'removed' => 0 );
        foreach ( $pages as $path => $content )
        {
            $file = $this->root . '/' . $path;
            if ( is_file( $file ) && file_get_contents( $file ) === $content )
            {
                ++$stats['unchanged'];
                continue;
            }
            if ( !is_dir( dirname( $file ) ) )
                eZDir::mkdir( dirname( $file ), false, true );
            file_put_contents( $file, $content );
            ++$stats['written'];
        }
        foreach ( $this->existingGeneratedPages( $areas ) as $path )
        {
            if ( !isset( $pages[$path] ) )
            {
                unlink( $this->root . '/' . $path );
                ++$stats['removed'];
            }
        }
        return $stats;
    }

    /**
     * Compares the pages of build() with the files on disk.
     *
     * @param array $pages
     * @param string[]|null $areas
     * @return array array( 'missing' => paths, 'different' => paths, 'stale' => paths )
     */
    public function compare( array $pages, ?array $areas = null )
    {
        $result = array( 'missing' => array(), 'different' => array(), 'stale' => array() );
        foreach ( $pages as $path => $content )
        {
            $file = $this->root . '/' . $path;
            if ( !is_file( $file ) )
                $result['missing'][] = $path;
            else if ( file_get_contents( $file ) !== $content )
                $result['different'][] = $path;
        }
        foreach ( $this->existingGeneratedPages( $areas ) as $path )
            if ( !isset( $pages[$path] ) )
                $result['stale'][] = $path;
        return $result;
    }

    /**
     * The generated pages on disk in the areas' directories (front matter marker).
     *
     * @param string[]|null $areas
     * @return string[]
     */
    protected function existingGeneratedPages( ?array $areas = null )
    {
        $found = array();
        foreach ( array_keys( self::AREAS ) as $area )
        {
            if ( $areas !== null && !in_array( $area, $areas, true ) )
                continue;
            $dir = $this->root . '/' . $this->out . '/' . $area;
            if ( !is_dir( $dir ) )
                continue;
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $iterator as $file )
            {
                if ( !$file->isFile() || substr( $file->getFilename(), -3 ) !== '.md' )
                    continue;
                $head = (string)file_get_contents( $file->getPathname(), false, null, 0, 2048 );
                if ( strpos( $head, "\ngenerated: " . self::MARKER . "\n" ) !== false )
                    $found[] = substr( $file->getPathname(), strlen( $this->root ) + 1 );
            }
        }
        sort( $found, SORT_STRING );
        return $found;
    }

    // ------------------------------------------------------------------------------------------------------------
    // Template operators

    protected function areaTemplateOperators()
    {
        $area = 'template-operators';
        $operators = array();
        foreach ( $this->templateAutoloadFiles() as $autoload )
        {
            list( $operatorArray, ) = $this->readTemplateAutoload( $autoload );
            foreach ( $operatorArray as $entry )
            {
                $names = array();
                $class = isset( $entry['class'] ) ? $entry['class'] : null;
                $object = null;
                if ( isset( $entry['operator_names'] ) )
                    $names = $entry['operator_names'];
                else if ( isset( $entry['operator_names_function'] ) && is_callable( $entry['operator_names_function'] ) )
                    $names = call_user_func( $entry['operator_names_function'] );
                if ( $class === null && isset( $entry['function'] ) && is_callable( $entry['function'] ) )
                {
                    try
                    {
                        $object = call_user_func( $entry['function'] );
                        $class = is_object( $object ) ? get_class( $object ) : null;
                    }
                    catch ( Throwable $e )
                    {
                        $this->warnings[] = "$autoload: {$entry['function']}(): " . $e->getMessage();
                    }
                }
                if ( $object === null && $class !== null )
                    $object = $this->instantiate( $class );
                foreach ( (array)$names as $name )
                {
                    if ( !is_string( $name ) || isset( $operators[$name] ) )
                        continue;
                    $operators[$name] = array( 'class' => $class, 'object' => $object, 'autoload' => $autoload,
                                               'siblings' => array_values( array_diff( (array)$names, array( $name ) ) ) );
                }
            }
        }
        $phpOperators = $this->iniValues( 'settings/template.ini', 'PHP', 'PHPOperatorList' );
        ksort( $operators, SORT_STRING );

        $rows = array();
        foreach ( $operators as $name => $info )
        {
            $params = $this->operatorParameters( $info['object'], $name );
            $hints = $this->operatorHints( $info['object'], $name );
            $file = $info['class'] !== null ? $this->classFile( $info['class'] ) : null;
            $piped = isset( $hints['input'] ) ? (bool)$hints['input'] : null;

            $md = array();
            $md[] = "# The $name template operator";
            $md[] = '';
            $md[] = 'Registered in `' . $info['autoload'] . '`' . ( $info['class'] !== null ? ' by the class `' . $info['class'] . '`' . ( $file ? ' (`' . $file . '`)' : '' ) : '' ) . '.';
            if ( isset( $phpOperators[$name] ) )
                $md[] = 'It calls the PHP function `' . $phpOperators[$name] . '()`, listed in `settings/template.ini` `[PHP] PHPOperatorList[]`.';
            if ( $info['siblings'] )
                $md[] = 'The same class also registers: ' . implode( ', ', array_map( function ( $n ) { return '[' . $n . '](' . self::fileName( $n ) . '.md)'; }, $info['siblings'] ) ) . '.';
            $md[] = '';
            $md[] = '## Parameters';
            $md[] = '';
            if ( $params === null )
                $md[] = 'The class does not describe its parameters (no `namedParameterList()`); see its source for what it reads.';
            else if ( !$params )
                $md[] = 'None.';
            else
            {
                $md[] = '| Parameter | Type | Required | Default |';
                $md[] = '|---|---|---|---|';
                foreach ( $params as $pname => $p )
                    $md[] = '| `' . $pname . '` | ' . self::cell( isset( $p['type'] ) ? $p['type'] : '' ) . ' | ' . ( !empty( $p['required'] ) ? 'yes' : 'no' ) . ' | ' . ( array_key_exists( 'default', $p ) ? self::code( self::export( $p['default'] ) ) : '' ) . ' |';
            }
            $md[] = '';
            if ( $piped !== null )
            {
                $md[] = $piped ? 'The operator works on its input: the value before `|`.' : 'The operator takes no input: it is called on its own, not after `|`.';
                $md[] = '';
            }
            $md[] = '## Example';
            $md[] = '';
            $md[] = 'Built from the parameter list above; the values are placeholders (the operator\'s template hints decide whether it takes an input).';
            $md[] = '';
            $md[] = '```tpl';
            $md[] = self::operatorCall( $name, $params, $piped );
            $md[] = '```';
            $md[] = '';
            $use = $this->templateUse( 'op:' . $name );
            $this->appendUse( $md, $use );
            $md[] = '## Related';
            $md[] = '';
            $md[] = '- [All template operators](README.md)';
            $md[] = '- [Templates and design](../../guides/templates-and-design.md)';
            $md[] = '- [A custom template operator](../cookbook/custom-template-operator.md)';
            $sources = array( $info['autoload'] );
            if ( $file )
                $sources[] = $file;
            $this->page( "$area/" . self::fileName( $name ) . '.md', "$name template operator",
                         "The $name template operator: " . ( $params ? count( $params ) . ' parameter(s)' : 'no named parameters' ) . ', registered by ' . ( $info['class'] ?: 'a function' ) . '.',
                         array( $name, 'template operator', 'operator', 'tpl', (string)$info['class'] ), $sources, $md );
            $rows[] = '| [' . $name . '](' . self::fileName( $name ) . '.md) | ' . self::cell( (string)$info['class'] ) . ' | ' . ( $params === null ? '?' : count( $params ) ) . ' | ' . ( $use ? 'yes' : '' ) . ' |';
            ++$this->counts[$area];
        }
        $md = array( '# Template operators', '',
                     'Every operator the kernel, its libraries and the bundled extensions register (the `$eZTemplateOperatorArray` of each `eztemplateautoload.php`, and `settings/template.ini` `[PHP] PHPOperatorList[]`), with its parameters and an example. "Used in" says whether a shipped template uses it; its page quotes the first use.', '',
                     count( $operators ) . ' operators.', '',
                     '| Operator | Class | Parameters | Used in a shipped template |', '|---|---|---|---|' );
        $md = array_merge( $md, $rows );
        $this->index( $area, 'Template operators', 'Every registered template operator with its parameters, an example call and its first use in a shipped template.', $md, $this->templateAutoloadFiles() );
    }

    /**
     * @return string[] the tracked eztemplateautoload.php files
     */
    protected function templateAutoloadFiles()
    {
        return array_values( array_filter( $this->files, function ( $f ) { return basename( $f ) === 'eztemplateautoload.php'; } ) );
    }

    /**
     * @param string $file
     * @return array array( operator array, function array )
     */
    protected function readTemplateAutoload( $file )
    {
        $read = function ( $path )
        {
            $eZTemplateOperatorArray = array();
            $eZTemplateFunctionArray = array();
            include $path;
            return array( $eZTemplateOperatorArray, $eZTemplateFunctionArray );
        };
        try
        {
            return $read( $this->root . '/' . $file );
        }
        catch ( Throwable $e )
        {
            $this->warnings[] = "$file: " . $e->getMessage();
            return array( array(), array() );
        }
    }

    /**
     * @param string $class
     * @return object|null
     */
    protected function instantiate( $class )
    {
        if ( !class_exists( $class ) )
        {
            $file = $this->classFile( $class );
            if ( $file !== null )
            {
                try
                {
                    require_once $this->root . '/' . $file;
                }
                catch ( Throwable $e )
                {
                    $this->warnings[] = "$class: " . $e->getMessage();
                }
            }
        }
        if ( !class_exists( $class ) )
        {
            $this->warnings[] = "$class: class not found";
            return null;
        }
        try
        {
            $reflection = new ReflectionClass( $class );
            $constructor = $reflection->getConstructor();
            if ( $constructor !== null && $constructor->getNumberOfRequiredParameters() > 0 )
                return null;
            return new $class();
        }
        catch ( Throwable $e )
        {
            $this->warnings[] = "$class: " . $e->getMessage();
            return null;
        }
    }

    /**
     * @param object|null $object
     * @param string $name
     * @return array|null parameter => definition, null when the class does not describe them
     */
    protected function operatorParameters( $object, $name )
    {
        if ( !is_object( $object ) || !method_exists( $object, 'namedParameterList' ) )
            return null;
        try
        {
            $list = $object->namedParameterList();
            $perOperator = method_exists( $object, 'namedParameterPerOperator' ) ? $object->namedParameterPerOperator() : false;
        }
        catch ( Throwable $e )
        {
            return null;
        }
        if ( !is_array( $list ) )
            return null;
        if ( $perOperator )
            $list = isset( $list[$name] ) && is_array( $list[$name] ) ? $list[$name] : array();
        $params = array();
        foreach ( $list as $pname => $definition )
            if ( is_string( $pname ) && is_array( $definition ) )
                $params[$pname] = $definition;
        return $params;
    }

    /**
     * @param object|null $object
     * @param string $name
     * @return array the operator's template hints (input, output, parameters ...), empty when unknown
     */
    protected function operatorHints( $object, $name )
    {
        if ( !is_object( $object ) || !method_exists( $object, 'operatorTemplateHints' ) )
            return array();
        try
        {
            $hints = $object->operatorTemplateHints();
        }
        catch ( Throwable $e )
        {
            return array();
        }
        return isset( $hints[$name] ) && is_array( $hints[$name] ) ? $hints[$name] : array();
    }

    /**
     * A template call of an operator, with placeholder values of the parameters' types.
     *
     * @param string $name
     * @param array|null $params
     * @param bool|null $piped
     * @return string
     */
    protected static function operatorCall( $name, $params, $piped )
    {
        $args = array();
        foreach ( (array)$params as $pname => $p )
        {
            $args[] = self::placeholder( $pname, isset( $p['type'] ) ? $p['type'] : '', array_key_exists( 'default', $p ) ? $p['default'] : null );
        }
        $call = $name . ( $args ? '( ' . implode( ', ', $args ) . ' )' : '' );
        if ( $piped === false )
            return '{' . $call . '}';
        return '{$value|' . $call . '}';
    }

    /**
     * A placeholder value in template syntax for a parameter.
     *
     * @param string $name
     * @param string $type
     * @param mixed $default
     * @return string
     */
    protected static function placeholder( $name, $type, $default = null )
    {
        if ( is_string( $default ) && $default !== '' )
            return "'" . str_replace( "'", "\\'", $default ) . "'";
        if ( is_int( $default ) || is_float( $default ) )
            return (string)$default;
        if ( $default === true )
            return 'true()';
        switch ( strtolower( (string)$type ) )
        {
            case 'integer':
            case 'int':
                return '1';
            case 'float':
            case 'number':
            case 'numeric':
                return '1.5';
            case 'bool':
            case 'boolean':
                return 'true()';
            case 'array':
                return 'array()';
            case 'hash':
                return 'hash()';
            case 'string':
            case 'text':
                return "'" . $name . "'";
            default:
                return '$' . preg_replace( '/[^A-Za-z0-9_]/', '_', $name );
        }
    }

    // ------------------------------------------------------------------------------------------------------------
    // Template functions

    protected function areaTemplateFunctions()
    {
        $area = 'template-functions';
        $functions = array();
        foreach ( $this->templateAutoloadFiles() as $autoload )
        {
            list( , $functionArray ) = $this->readTemplateAutoload( $autoload );
            foreach ( $functionArray as $entry )
            {
                $class = isset( $entry['class'] ) ? $entry['class'] : null;
                $rules = array();
                if ( $class === null && isset( $entry['function'] ) && is_callable( $entry['function'] ) )
                {
                    try
                    {
                        $object = call_user_func( $entry['function'] );
                        if ( is_object( $object ) )
                        {
                            $class = get_class( $object );
                            $rules = self::objectProperty( $object, array( 'Rules', 'rules' ) );
                        }
                    }
                    catch ( Throwable $e )
                    {
                        $this->warnings[] = "$autoload: {$entry['function']}(): " . $e->getMessage();
                    }
                }
                $names = isset( $entry['function_names'] ) ? (array)$entry['function_names'] : array();
                foreach ( $names as $name )
                {
                    if ( !is_string( $name ) || isset( $functions[$name] ) )
                        continue;
                    $functions[$name] = array( 'class' => $class, 'autoload' => $autoload,
                                               'attributes' => isset( $entry['function_attributes'] ) ? (array)$entry['function_attributes'] : array(),
                                               'rule' => is_array( $rules ) && isset( $rules[$name] ) && is_array( $rules[$name] ) ? $rules[$name] : array(),
                                               'siblings' => array_values( array_diff( $names, array( $name ) ) ) );
                }
            }
        }
        ksort( $functions, SORT_STRING );
        $rows = array();
        foreach ( $functions as $name => $info )
        {
            $file = $info['class'] !== null ? $this->classFile( $info['class'] ) : null;
            $md = array( "# The $name template function", '' );
            $md[] = 'Registered in `' . $info['autoload'] . '`' . ( $info['class'] !== null ? ' by the class `' . $info['class'] . '`' . ( $file ? ' (`' . $file . '`)' : '' ) : '' ) . '.';
            if ( $info['siblings'] )
                $md[] = 'The same class also handles: ' . implode( ', ', array_map( function ( $n ) { return '[' . $n . '](' . self::fileName( $n ) . '.md)'; }, $info['siblings'] ) ) . '.';
            $md[] = '';
            if ( $info['attributes'] )
            {
                $md[] = '## Inner functions';
                $md[] = '';
                $md[] = 'Used inside the block: ' . implode( ', ', array_map( function ( $a ) { return '`{' . $a . '}`'; }, $info['attributes'] ) ) . '.';
                $md[] = '';
            }
            if ( $info['rule'] )
            {
                $md[] = '## How it finds its template';
                $md[] = '';
                $md[] = 'From the forward rule in `eZObjectForwardInit()` (`kernel/common/eztemplateautoload.php`):';
                $md[] = '';
                $md[] = '| Key | Value |';
                $md[] = '|---|---|';
                foreach ( array( 'template_root', 'input_name', 'output_name', 'namespace', 'use_views' ) as $key )
                    if ( isset( $info['rule'][$key] ) )
                        $md[] = '| `' . $key . '` | ' . self::code( self::export( $info['rule'][$key] ) ) . ' |';
                if ( isset( $info['rule']['attribute_keys'] ) && is_array( $info['rule']['attribute_keys'] ) )
                    $md[] = '| override match keys | ' . implode( ', ', array_map( function ( $k ) { return '`' . $k . '`'; }, array_keys( $info['rule']['attribute_keys'] ) ) ) . ' |';
                $md[] = '';
                if ( isset( $info['rule']['input_name'] ) )
                {
                    $md[] = '```tpl';
                    $md[] = '{' . $name . ' ' . $info['rule']['input_name'] . '=$' . $info['rule']['input_name'] . ( !empty( $info['rule']['use_views'] ) ? ' view=' . ( is_string( $info['rule']['use_views'] ) ? $info['rule']['use_views'] : 'view' ) : '' ) . '}';
                    $md[] = '```';
                    $md[] = '';
                    $md[] = 'The variable name is the rule\'s `input_name`; the value is a placeholder.';
                    $md[] = '';
                }
            }
            $use = $this->templateUse( 'fn:' . $name );
            $this->appendUse( $md, $use );
            $md[] = '## Related';
            $md[] = '';
            $md[] = '- [All template functions](README.md)';
            $md[] = '- [Template operators](../template-operators/README.md)';
            $md[] = '- [Templates and design](../../guides/templates-and-design.md)';
            $sources = array( $info['autoload'] );
            if ( $file )
                $sources[] = $file;
            $this->page( "$area/" . self::fileName( $name ) . '.md', "$name template function",
                         "The {$name} template function, registered by " . ( $info['class'] ?: 'a function' ) . '.',
                         array( $name, 'template function', 'tpl', (string)$info['class'] ), $sources, $md );
            $rows[] = '| [' . $name . '](' . self::fileName( $name ) . '.md) | ' . self::cell( (string)$info['class'] ) . ' | ' . ( $use ? 'yes' : '' ) . ' |';
            ++$this->counts[$area];
        }
        $md = array_merge( array( '# Template functions', '',
                     'Every template function (`{name ...}` and `{name}...{/name}`) the kernel and the bundled extensions register (`$eZTemplateFunctionArray` in each `eztemplateautoload.php`), with its first use in a shipped template.', '',
                     count( $functions ) . ' functions.', '', '| Function | Class | Used in a shipped template |', '|---|---|---|' ), $rows );
        $this->index( $area, 'Template functions', 'Every registered template function with its class and its first use in a shipped template.', $md, $this->templateAutoloadFiles() );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Fetch functions

    protected function areaFetchFunctions()
    {
        $area = 'fetch-functions';
        $byModule = array();
        foreach ( $this->files as $file )
        {
            if ( basename( $file ) !== 'function_definition.php' )
                continue;
            $module = basename( dirname( $file ) );
            $list = $this->readDefinition( $file, 'FunctionList' );
            if ( !$list )
                continue;
            if ( isset( $byModule[$module] ) )
                $module .= '-' . basename( dirname( dirname( $file ) ) );
            $byModule[$module] = array( 'file' => $file, 'list' => $list );
        }
        ksort( $byModule, SORT_STRING );
        $moduleRows = array();
        foreach ( $byModule as $module => $data )
        {
            $list = $data['list'];
            ksort( $list, SORT_STRING );
            $rows = array();
            foreach ( $list as $fname => $def )
            {
                if ( !is_array( $def ) )
                    continue;
                $params = isset( $def['parameters'] ) && is_array( $def['parameters'] ) ? $def['parameters'] : array();
                $call = isset( $def['call_method'] ) && is_array( $def['call_method'] ) ? $def['call_method'] : array();
                $md = array( "# fetch( '$module', '$fname' )", '' );
                $md[] = 'Defined in `' . $data['file'] . '`' . ( isset( $call['class'], $call['method'] ) ? '; it calls `' . $call['class'] . '::' . $call['method'] . '()`' . ( ( $cf = $this->classFile( $call['class'] ) ) ? ' (`' . $cf . '`)' : '' ) : '' ) . '.';
                if ( !empty( $def['operation_types'] ) )
                    $md[] = 'Operation type: ' . implode( ', ', array_map( 'strval', (array)$def['operation_types'] ) ) . '.';
                $md[] = '';
                $md[] = '## Parameters';
                $md[] = '';
                if ( !$params )
                    $md[] = 'None.';
                else
                {
                    $md[] = '| Parameter | Type | Required | Default |';
                    $md[] = '|---|---|---|---|';
                    foreach ( $params as $p )
                        if ( isset( $p['name'] ) )
                            $md[] = '| `' . $p['name'] . '` | ' . self::cell( isset( $p['type'] ) ? $p['type'] : '' ) . ' | ' . ( !empty( $p['required'] ) ? 'yes' : 'no' ) . ' | ' . ( array_key_exists( 'default', $p ) ? self::code( self::export( $p['default'] ) ) : '' ) . ' |';
                }
                $md[] = '';
                $md[] = '## Example';
                $md[] = '';
                $md[] = 'In a template (the values are placeholders of each parameter\'s type):';
                $md[] = '';
                $required = array_values( array_filter( $params, function ( $p ) { return !empty( $p['required'] ); } ) );
                // with no required parameter the short example names the first one
                $minimal = $required ? $required : array_slice( array_values( $params ), 0, 1 );
                $md[] = '```tpl';
                $md[] = '{def $result = ' . self::fetchCall( $module, $fname, $minimal ) . '}';
                if ( count( $minimal ) < count( $params ) )
                {
                    $md[] = '{* with every parameter *}';
                    $md[] = '{def $all = ' . self::fetchCall( $module, $fname, $params ) . '}';
                }
                $md[] = '```';
                $md[] = '';
                $md[] = 'In PHP, the same function through the function handler (`lib/ezutils/classes/ezfunctionhandler.php`):';
                $md[] = '';
                $md[] = '```php';
                $phpArgs = array();
                foreach ( $minimal as $p )
                    $phpArgs[] = "'" . $p['name'] . "' => " . self::phpPlaceholder( $p['name'], isset( $p['type'] ) ? $p['type'] : '' );
                $md[] = "\$result = eZFunctionHandler::execute( '$module', '$fname', " . ( $phpArgs ? "array( " . implode( ", ", $phpArgs ) . " )" : "array()" ) . " );";
                $md[] = '```';
                $md[] = '';
                $this->appendUse( $md, $this->templateUse( "fetch:$module/$fname" ) );
                $md[] = '## Related';
                $md[] = '';
                $md[] = "- [All fetch functions of $module](README.md)";
                $md[] = "- [The views of the $module module](../../module-views/" . self::fileName( $module ) . '/README.md)';
                $md[] = '- [The fetch template operator](../../template-operators/fetch.md)';
                $sources = array( $data['file'] );
                if ( isset( $call['class'] ) && ( $cf = $this->classFile( $call['class'] ) ) )
                    $sources[] = $cf;
                $this->page( "$area/" . self::fileName( $module ) . '/' . self::fileName( $fname ) . '.md', "fetch( '$module', '$fname' )",
                             "The fetch function $fname of the $module module: " . count( $params ) . ' parameter(s), ' . count( $required ) . ' required.',
                             array( 'fetch', $module, $fname, 'fetch function', 'template' ), $sources, $md );
                $rows[] = '| [' . $fname . '](' . self::fileName( $fname ) . '.md) | ' . implode( ', ', array_map( function ( $p ) { return '`' . $p['name'] . '`' . ( !empty( $p['required'] ) ? '*' : '' ); }, array_filter( $params, function ( $p ) { return isset( $p['name'] ); } ) ) ) . ' |';
                ++$this->counts[$area];
            }
            $md = array_merge( array( "# Fetch functions of the $module module", '', 'Defined in `' . $data['file'] . '`. A parameter marked * is required.', '',
                                      '| Function | Parameters |', '|---|---|' ), $rows );
            $this->index( "$area/" . self::fileName( $module ), "Fetch functions of $module", "The fetch functions of the $module module with their parameters.", $md, array( $data['file'] ), 10 );
            $moduleRows[] = '| [' . $module . '](' . self::fileName( $module ) . '/README.md) | ' . count( $rows ) . ' | `' . $data['file'] . '` |';
        }
        $md = array_merge( array( '# Fetch functions', '',
                                  'Every `fetch( \'module\', \'function\', hash( ... ) )` the kernel and the bundled extensions offer, from their `function_definition.php`: the parameters, an example in a template and in PHP, and the first use in a shipped template.', '',
                                  '| Module | Functions | Defined in |', '|---|---|---|' ), $moduleRows );
        $this->index( $area, 'Fetch functions', 'Every fetch function by module, with parameters and examples in templates and PHP.', $md, array() );
    }

    /**
     * @param string $module
     * @param string $function
     * @param array $params
     * @return string
     */
    protected static function fetchCall( $module, $function, array $params )
    {
        $pairs = array();
        foreach ( $params as $p )
            if ( isset( $p['name'] ) )
                $pairs[] = "'" . $p['name'] . "', " . self::placeholder( $p['name'], isset( $p['type'] ) ? $p['type'] : '', array_key_exists( 'default', $p ) && empty( $p['required'] ) ? $p['default'] : null );
        return "fetch( '$module', '$function'" . ( $pairs ? ', hash( ' . implode( ",\n" . str_repeat( ' ', 28 + strlen( $module ) + strlen( $function ) ), $pairs ) . ' )' : '' ) . ' )';
    }

    /**
     * @param string $name
     * @param string $type
     * @return string a PHP placeholder value
     */
    protected static function phpPlaceholder( $name, $type )
    {
        switch ( strtolower( (string)$type ) )
        {
            case 'integer':
            case 'int':
                return '1';
            case 'bool':
            case 'boolean':
                return 'true';
            case 'array':
            case 'hash':
                return 'array()';
            case 'string':
            case 'text':
                return "'" . $name . "'";
            default:
                return '$' . preg_replace( '/[^A-Za-z0-9_]/', '_', $name );
        }
    }

    // ------------------------------------------------------------------------------------------------------------
    // Module views and policies

    /**
     * @return array module name => array( 'file', 'module', 'views', 'functions' )
     */
    protected function modules()
    {
        if ( $this->modulesCache !== null )
            return $this->modulesCache;
        $modules = array();
        foreach ( $this->files as $file )
        {
            if ( basename( $file ) !== 'module.php' )
                continue;
            if ( !preg_match( '#^(kernel/[^/]+|kernel/private/modules/[^/]+|extension/[^/]+/modules/[^/]+)/module\.php$#', $file ) )
                continue;
            $data = $this->readModule( $file );
            if ( $data === null )
                continue;
            $name = basename( dirname( $file ) );
            if ( isset( $modules[$name] ) )
                continue;
            $data['file'] = $file;
            $modules[$name] = $data;
        }
        ksort( $modules, SORT_STRING );
        $this->modulesCache = $modules;
        return $modules;
    }

    /**
     * @param string $file
     * @return array|null array( 'module', 'views', 'functions' )
     */
    protected function readModule( $file )
    {
        $read = function ( $path )
        {
            $Module = array();
            $ViewList = array();
            $FunctionList = array();
            include $path;
            return array( 'module' => $Module, 'views' => $ViewList, 'functions' => $FunctionList );
        };
        // The object state limitations (content, state) are read from the database at include time: one per state
        // group of the site. Generated pages must not depend on a database, so they are read as none and the page
        // says where they come from.
        $stateCache = null;
        if ( class_exists( 'eZContentObjectStateGroup' ) && property_exists( 'eZContentObjectStateGroup', 'limitationsCache' ) )
        {
            $stateCache = new ReflectionProperty( 'eZContentObjectStateGroup', 'limitationsCache' );
            if ( PHP_VERSION_ID < 80100 )
                $stateCache->setAccessible( true );
            $saved = $stateCache->getValue();
            $stateCache->setValue( null, array() );
        }
        try
        {
            $data = $read( $this->root . '/' . $file );
        }
        catch ( Throwable $e )
        {
            $this->warnings[] = "$file: " . $e->getMessage();
            $data = null;
        }
        if ( $stateCache !== null )
            $stateCache->setValue( null, $saved );
        if ( $data === null )
            return null;
        $data['stateLimitations'] = strpos( (string)file_get_contents( $this->root . '/' . $file ), 'eZContentObjectStateGroup::limitations()' ) !== false;
        return is_array( $data['views'] ) ? $data : null;
    }

    protected function areaModuleViews()
    {
        $area = 'module-views';
        $omit = array_map( 'strval', $this->iniList( 'settings/site.ini', 'RoleSettings', 'PolicyOmitList' ) );
        $moduleRows = array();
        foreach ( $this->modules() as $module => $data )
        {
            $views = $data['views'];
            ksort( $views, SORT_STRING );
            $dir = dirname( $data['file'] );
            $rows = array();
            foreach ( $views as $view => $def )
            {
                if ( !is_array( $def ) )
                    continue;
                $params = isset( $def['params'] ) && is_array( $def['params'] ) ? array_values( $def['params'] ) : array();
                $unordered = isset( $def['unordered_params'] ) && is_array( $def['unordered_params'] ) ? $def['unordered_params'] : array();
                $functions = isset( $def['functions'] ) ? $def['functions'] : null;
                $url = '/' . $module . '/' . $view . implode( '', array_map( function ( $p ) { return '/<' . $p . '>'; }, $params ) );
                $md = array( "# /$module/$view", '' );
                $md[] = 'A view of the `' . $module . '` module, defined in `' . $data['file'] . '`' . ( isset( $def['script'] ) ? ' and run by `' . $dir . '/' . $def['script'] . '`' : '' ) . '.';
                $md[] = '';
                $md[] = '## Address';
                $md[] = '';
                $md[] = '```text';
                $md[] = $url . implode( '', array_map( function ( $k, $v ) { return '/(' . $k . ')/<' . $v . '>'; }, array_keys( $unordered ), $unordered ) );
                $md[] = '```';
                $md[] = '';
                if ( $params )
                {
                    $md[] = 'Ordered parameters, in this order after the view name: ' . implode( ', ', array_map( function ( $p ) { return '`' . $p . '`'; }, $params ) ) . '.';
                    $md[] = '';
                }
                if ( $unordered )
                {
                    $md[] = 'Unordered parameters, in any order as `/(name)/value`: ' . implode( ', ', array_map( function ( $k, $v ) { return '`(' . $k . ')` sets `' . $v . '`'; }, array_keys( $unordered ), $unordered ) ) . '.';
                    $md[] = '';
                }
                $md[] = '## Who may open it';
                $md[] = '';
                $md = array_merge( $md, self::viewAccess( $module, $view, $functions, $data['functions'], in_array( "$module/$view", $omit, true ) || in_array( $module, $omit, true ) ) );
                $md[] = '';
                $details = array();
                foreach ( array( 'default_navigation_part' => 'Navigation part', 'ui_context' => 'UI context', 'ui_component' => 'UI component' ) as $key => $label )
                    if ( isset( $def[$key] ) && is_scalar( $def[$key] ) )
                        $details[] = '| ' . $label . ' | `' . $def[$key] . '` |';
                foreach ( array( 'single_post_actions' => 'Single POST actions', 'post_actions' => 'POST actions', 'post_action_parameters' => 'POST action parameters', 'post_value_action_parameters' => 'POST value action parameters' ) as $key => $label )
                    if ( !empty( $def[$key] ) && is_array( $def[$key] ) )
                        $details[] = '| ' . $label . ' | ' . self::cell( self::flatten( $def[$key] ) ) . ' |';
                if ( $details )
                {
                    $md[] = '## Details';
                    $md[] = '';
                    $md[] = '| Key | Value |';
                    $md[] = '|---|---|';
                    $md = array_merge( $md, $details );
                    $md[] = '';
                }
                $md[] = '## Example';
                $md[] = '';
                $md[] = 'A link in a template (`ezurl` adds the siteaccess and quotes the address); the parameter values are placeholders:';
                $md[] = '';
                $md[] = '```tpl';
                $sample = '/' . $module . '/' . $view;
                foreach ( $params as $p )
                    $sample .= '/' . self::paramPlaceholder( $p );
                $md[] = '<a href={\'' . $sample . '\'|ezurl}>' . htmlspecialchars( $module . '/' . $view, ENT_QUOTES ) . '</a>';
                if ( $unordered )
                {
                    $k = array_keys( $unordered );
                    $md[] = '<a href={concat( \'' . $sample . '/(' . $k[0] . ')/\', ' . self::placeholder( $unordered[$k[0]], 'integer' ) . ' )|ezurl}>' . htmlspecialchars( $module . '/' . $view, ENT_QUOTES ) . '</a>';
                }
                $md[] = '```';
                $md[] = '';
                $md[] = 'From PHP, the redirect a view gives after it has done its work:';
                $md[] = '';
                $md[] = '```php';
                $md[] = "return \$Module->redirectTo( '$sample' );";
                $md[] = '```';
                $md[] = '';
                $md[] = '## Related';
                $md[] = '';
                $md[] = "- [All views of $module](README.md)";
                $md[] = "- [The policies of $module](../../policies/" . self::fileName( $module ) . '.md)';
                $md[] = '- [A custom module and view](../../cookbook/custom-module-view.md)';
                $sources = array( $data['file'] );
                if ( isset( $def['script'] ) && in_array( $dir . '/' . $def['script'], $this->files, true ) )
                    $sources[] = $dir . '/' . $def['script'];
                $this->page( "$area/" . self::fileName( $module ) . '/' . self::fileName( $view ) . '.md', "/$module/$view",
                             "The view $module/$view: its address, parameters and the policy functions it needs.",
                             array( $module, $view, "$module/$view", 'module', 'view', 'URL' ), $sources, $md );
                $rows[] = '| [' . $view . '](' . self::fileName( $view ) . '.md) | `' . self::cell( $url ) . '` | ' . self::cell( self::functionsText( $functions ) ) . ' |';
                ++$this->counts[$area];
            }
            $name = isset( $data['module']['name'] ) && is_string( $data['module']['name'] ) ? $data['module']['name'] : $module;
            $md = array_merge( array( "# The $module module", '', 'Name `' . $name . '`, defined in `' . $data['file'] . '`. ' . count( $rows ) . ' views.', '',
                                      '| View | Address | Policy functions |', '|---|---|---|' ), $rows );
            $md[] = '';
            $md[] = '- [The policies of this module](../../policies/' . self::fileName( $module ) . '.md)';
            $this->index( "$area/" . self::fileName( $module ), "The $module module", "The views of the $module module: addresses, parameters and policy functions.", $md, array( $data['file'] ), 10 );
            $moduleRows[] = '| [' . $module . '](' . self::fileName( $module ) . '/README.md) | ' . count( $rows ) . ' | `' . $data['file'] . '` |';
        }
        $md = array_merge( array( '# Module views', '',
                                  'Every module of the kernel and the bundled extensions, from their `module.php`: each view\'s address with its ordered and unordered parameters, the policy functions it needs, and a link to it from a template.', '',
                                  '| Module | Views | Defined in |', '|---|---|---|' ), $moduleRows );
        $this->index( $area, 'Module views', 'Every module view: address pattern, parameters, unordered parameters and required policies.', $md, array() );
    }

    /**
     * What a view needs, in words, following eZUser::hasAccessToView().
     *
     * @return string[]
     */
    protected static function viewAccess( $module, $view, $functions, $moduleFunctions, $omitted )
    {
        $md = array();
        if ( $omitted )
            $md[] = 'Listed in `settings/site.ini` `[RoleSettings] PolicyOmitList[]`: no policy is checked, everyone may open it.';
        if ( $functions === null || $functions === array() )
        {
            $md[] = 'The view names no policy function, so the user needs a policy for the whole module: `' . $module . '/*` (every function of the module) in one of the roles assigned to them.';
            return $md;
        }
        $list = is_array( $functions ) ? $functions : array( $functions );
        $md[] = 'The user needs ' . ( count( $list ) > 1 ? 'all of these (the entries are combined with "and"); an entry with "or" needs one of its functions' : 'this policy function' ) . ':';
        $md[] = '';
        foreach ( $list as $expression )
        {
            if ( !is_string( $expression ) )
                continue;
            $words = preg_split( '/\s+(or|and)\s+/', trim( $expression ), -1, PREG_SPLIT_DELIM_CAPTURE );
            $out = array();
            foreach ( $words as $word )
                $out[] = ( $word === 'or' || $word === 'and' ) ? $word : '[`' . $module . '/' . trim( $word, '() ' ) . '`](../../policies/' . self::fileName( $module ) . '.md#' . self::anchor( trim( $word, '() ' ) ) . ')';
            $md[] = '- ' . implode( ' ', $out );
        }
        $md[] = '';
        $md[] = 'A policy on `' . $module . '/*` (every function) also grants it; limitations of the policy (section, subtree, class ...) still apply where the view checks them.';
        return $md;
    }

    /**
     * @param mixed $functions
     * @return string
     */
    protected static function functionsText( $functions )
    {
        if ( $functions === null || $functions === array() )
            return '(module)';
        return implode( '; ', array_filter( (array)$functions, 'is_string' ) );
    }

    /**
     * @param string $param
     * @return string
     */
    protected static function paramPlaceholder( $param )
    {
        return preg_match( '/(ID|Id|Number|Offset|Limit|Version|Count)$/', $param ) ? '1' : strtolower( preg_replace( '/[^A-Za-z0-9]+/', '-', $param ) );
    }

    protected function areaPolicies()
    {
        $area = 'policies';
        $rows = array();
        foreach ( $this->modules() as $module => $data )
        {
            $functions = is_array( $data['functions'] ) ? $data['functions'] : array();
            $usedBy = array();
            foreach ( $data['views'] as $view => $def )
                if ( is_array( $def ) && isset( $def['functions'] ) )
                    foreach ( (array)$def['functions'] as $expression )
                        if ( is_string( $expression ) )
                            foreach ( preg_split( '/\s+(?:or|and)\s+/', $expression ) as $word )
                                $usedBy[trim( $word, '() ' )][] = $view;
            $md = array( "# Policies of the $module module", '' );
            $md[] = 'The policy functions of `' . $module . '` and their limitations, from `$FunctionList` in `' . $data['file'] . '`. In the administration they are chosen under Roles and policies; a policy on `' . $module . '/*` grants every function.';
            $md[] = '';
            if ( !empty( $data['stateLimitations'] ) )
            {
                $md[] = 'Some functions also take one limitation per object state group of the site, named `StateGroup_<group identifier>` (`eZContentObjectStateGroup::limitations()`, read from the database, so not listed below).';
                $md[] = '';
            }
            if ( !$functions )
            {
                $md[] = 'The module defines no policy functions: a role needs `' . $module . '/*` for its views (unless they are in `PolicyOmitList[]`).';
                $md[] = '';
            }
            ksort( $functions, SORT_STRING );
            foreach ( $functions as $fname => $limitations )
            {
                $md[] = '## ' . $fname;
                $md[] = '';
                if ( isset( $usedBy[$fname] ) )
                {
                    $views = array_values( array_unique( $usedBy[$fname] ) );
                    sort( $views, SORT_STRING );
                    $md[] = 'Checked by the views: ' . implode( ', ', array_map( function ( $v ) use ( $module ) { return '[' . $v . '](../module-views/' . self::fileName( $module ) . '/' . self::fileName( $v ) . '.md)'; }, $views ) ) . '.';
                }
                else
                    $md[] = 'No view of the module names it; code checks it (`eZUser::hasAccessTo( \'' . $module . '\', \'' . $fname . '\' )` or the `user/has_access_to` fetch).';
                $md[] = '';
                if ( is_array( $limitations ) && $limitations )
                {
                    $md[] = '| Limitation | Values come from |';
                    $md[] = '|---|---|';
                    ksort( $limitations, SORT_STRING );
                    foreach ( $limitations as $lname => $lim )
                    {
                        $from = '';
                        if ( is_array( $lim ) )
                        {
                            if ( isset( $lim['class'], $lim['function'] ) && is_string( $lim['class'] ) && is_string( $lim['function'] ) )
                                $from = '`' . $lim['class'] . '::' . $lim['function'] . '()`';
                            else if ( isset( $lim['extension'] ) )
                                $from = 'extension ' . self::cell( self::flatten( $lim['extension'] ) );
                            else if ( !empty( $lim['values'] ) && is_array( $lim['values'] ) )
                                $from = 'fixed: ' . self::cell( implode( ', ', array_map( function ( $v ) { return is_array( $v ) && isset( $v['Name'] ) ? $v['Name'] . ( isset( $v['value'] ) ? ' (' . $v['value'] . ')' : '' ) : self::export( $v ); }, $lim['values'] ) ) );
                        }
                        $md[] = '| ' . self::cell( (string)$lname ) . ' | ' . $from . ' |';
                    }
                    $md[] = '';
                }
                else
                {
                    $md[] = 'No limitations: the policy is all or nothing.';
                    $md[] = '';
                }
                $md[] = '```php';
                $md[] = '// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))';
                $md[] = "\$role = eZRole::fetchByName( 'Editor' );";
                $md[] = "\$role->appendPolicy( '$module', '$fname'" . ( is_array( $limitations ) && $limitations ? ', array( ' . "'" . key( $limitations ) . "' => array( /* values */ ) )" : '' ) . ' );';
                $md[] = '```';
                $md[] = '';
                $md[] = '```tpl';
                $md[] = "{if fetch( 'user', 'has_access_to', hash( 'module', '$module', 'function', '$fname' ) )}";
                $md[] = '    ...';
                $md[] = '{/if}';
                $md[] = '```';
                $md[] = '';
            }
            $md[] = '## Related';
            $md[] = '';
            $md[] = '- [All modules\' policies](README.md)';
            $md[] = '- [The views of ' . $module . '](../module-views/' . self::fileName( $module ) . '/README.md)';
            $md[] = '- [Roles and policies](../../guides/roles-and-policies.md)';
            $this->page( "$area/" . self::fileName( $module ) . '.md', "Policies of the $module module",
                         "The policy functions of the $module module (" . count( $functions ) . ') and their limitations.',
                         array( $module, 'policy', 'policies', 'role', 'limitation', 'permission' ), array( $data['file'], 'kernel/classes/ezrole.php' ), $md );
            $rows[] = '| [' . $module . '](' . self::fileName( $module ) . '.md) | ' . ( $functions ? implode( ', ', array_map( function ( $f ) { return '`' . $f . '`'; }, array_keys( $functions ) ) ) : '(none: `' . $module . '/*`)' ) . ' |';
            ++$this->counts[$area];
        }
        $md = array_merge( array( '# Policy functions and limitations', '',
                                  'Every module\'s policy functions and the limitations each can carry, from `$FunctionList` in its `module.php`, with the views that check them and examples in PHP and templates.', '',
                                  '| Module | Policy functions |', '|---|---|' ), $rows );
        $this->index( $area, 'Policy functions and limitations', 'Every module\'s policy functions and limitations, with the views that check them.', $md, array( 'kernel/classes/ezrole.php' ) );
    }

    // ------------------------------------------------------------------------------------------------------------
    // INI settings

    /**
     * @return string[] the base INI files: settings/*.ini and extension/<name>/settings/*.ini
     */
    protected function iniFiles()
    {
        return array_values( array_filter( $this->files, function ( $f )
        {
            return (bool)preg_match( '#^(settings|extension/[^/]+/settings)/[^/]+\.ini$#', $f );
        } ) );
    }

    protected function areaIniSettings()
    {
        $area = 'ini-settings';
        $fileRows = array();
        foreach ( $this->iniFiles() as $file )
        {
            $parsed = $this->parseIni( $file );
            $ini = basename( $file );
            $ext = preg_match( '#^extension/([^/]+)/#', $file, $m ) ? $m[1] : null;
            $folder = $ext !== null ? $ext . '-' . substr( $ini, 0, -4 ) : substr( $ini, 0, -4 );
            $rows = array();
            $used = array();
            foreach ( $parsed['blocks'] as $block => $info )
            {
                $slug = self::fileName( $block );
                $base = $slug;
                for ( $i = 2; isset( $used[strtolower( $slug )] ); ++$i )
                    $slug = $base . '-' . $i;
                $used[strtolower( $slug )] = true;
                $md = array( "# $ini [$block]", '' );
                $md[] = 'The block `[' . $block . ']` of `' . $file . '`' . ( $ext !== null ? ' (extension ' . $ext . ')' : '' ) . ', with ' . count( $info['settings'] ) . ' setting(s).';
                $md[] = '';
                if ( $info['comment'] )
                {
                    $md = array_merge( $md, self::quote( $info['comment'] ) );
                    $md[] = '';
                }
                $md[] = 'Never edit `' . $file . '` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:';
                $md[] = '';
                $md[] = '- `settings/override/' . $ini . '.append.php` for every siteaccess,';
                $md[] = '- `settings/siteaccess/<siteaccess>/' . $ini . '.append.php` for one siteaccess,';
                $md[] = '- `extension/<extension>/settings/' . $ini . '.append.php` in an extension (read when the extension is active).';
                $md[] = '';
                foreach ( $info['settings'] as $name => $setting )
                {
                    $md[] = '## ' . $name;
                    $md[] = '';
                    if ( $setting['comment'] )
                    {
                        $md = array_merge( $md, self::quote( $setting['comment'] ) );
                        $md[] = '';
                    }
                    $md[] = 'Default: ' . self::iniDefaultText( $setting ) . '.';
                    $md[] = '';
                    $md[] = '```ini';
                    $md[] = '<?php /* #?ini charset="utf-8"?';
                    $md[] = '# settings/override/' . $ini . '.append.php';
                    $md[] = '[' . $block . ']';
                    $md = array_merge( $md, self::iniOverrideLines( $name, $setting ) );
                    $md[] = '*/ ?>';
                    $md[] = '```';
                    $md[] = '';
                }
                $md[] = '## Related';
                $md[] = '';
                $md[] = '- [All blocks of ' . $ini . '](README.md)';
                $md[] = '- [All INI files](../README.md)';
                $md[] = '- [The settings page](../../../guides/settings-page.md)';
                $this->page( "$area/$folder/$slug.md", "$ini [$block]",
                             "The settings of [$block] in $ini" . ( $ext !== null ? " ($ext)" : '' ) . ': ' . implode( ', ', array_slice( array_keys( $info['settings'] ), 0, 8 ) ) . ( count( $info['settings'] ) > 8 ? ' ...' : '' ),
                             array_merge( array( $ini, $block, 'INI', 'setting' ), array_slice( array_keys( $info['settings'] ), 0, 30 ) ), array( $file ), $md );
                $rows[] = '| [' . self::cell( $block ) . '](' . $slug . '.md) | ' . count( $info['settings'] ) . ' |';
                ++$this->counts[$area];
            }
            $md = array( "# $ini", '' );
            $md[] = 'The shipped settings of `' . $file . '`' . ( $ext !== null ? ' (extension ' . $ext . ')' : '' ) . ': ' . count( $parsed['blocks'] ) . ' blocks, ' . array_sum( array_map( function ( $b ) { return count( $b['settings'] ); }, $parsed['blocks'] ) ) . ' settings.';
            $md[] = '';
            if ( $parsed['comment'] )
            {
                $md = array_merge( $md, self::quote( $parsed['comment'] ) );
                $md[] = '';
            }
            $md[] = '| Block | Settings |';
            $md[] = '|---|---|';
            $md = array_merge( $md, $rows );
            $this->index( "$area/$folder", $ini . ( $ext !== null ? " ($ext)" : '' ), "The blocks and settings of $ini" . ( $ext !== null ? " of the extension $ext" : '' ) . '.', $md, array( $file ), 10 );
            $fileRows[] = '| [' . $ini . '](' . $folder . '/README.md) | ' . ( $ext !== null ? $ext : 'kernel' ) . ' | ' . count( $parsed['blocks'] ) . ' |';
        }
        $md = array_merge( array( '# INI settings', '',
                                  'Every shipped INI file of the kernel and the bundled extensions, one page per block: each setting with the comment of the shipped file, its default and an override to copy. Settings are changed in overrides, never in the shipped files (`settings/override/<file>.append.php`, `settings/siteaccess/<siteaccess>/<file>.append.php` or an extension\'s `settings/`).', '',
                                  '| File | From | Blocks |', '|---|---|---|' ), $fileRows );
        $this->index( $area, 'INI settings', 'Every shipped INI setting by file and block: comment, default and an override example.', $md, array() );
    }

    /**
     * Reads an INI file with its comments, in the order of the file.
     *
     * @param string $file
     * @return array array( 'comment' => file header, 'blocks' => block => array( 'comment', 'settings' => name =>
     *               array( 'kind' => scalar|list|hash, 'value', 'items' => list( array( key|null, value ) ), 'reset',
     *               'comment' ) ) )
     */
    public function parseIni( $file )
    {
        $text = (string)@file_get_contents( $this->root . '/' . $file );
        return self::parseIniText( $text );
    }

    /**
     * @param string $text
     * @return array see parseIni()
     */
    public static function parseIniText( $text )
    {
        $result = array( 'comment' => '', 'blocks' => array() );
        $comment = array();
        $block = null;
        $lastSetting = null;
        $headerDone = false;
        foreach ( preg_split( '/\r?\n/', $text ) as $line )
        {
            $trim = trim( $line );
            if ( $trim === '' )
            {
                if ( !$headerDone && $block === null && $comment )
                {
                    $result['comment'] = trim( $result['comment'] . "\n" . implode( "\n", $comment ) );
                    $comment = array();
                }
                continue;
            }
            if ( strncmp( $trim, '#?ini', 5 ) === 0 || strncmp( $trim, '<?php', 5 ) === 0 || $trim === '*/ ?>' || $trim === '*/' )
                continue;
            if ( $trim[0] === '#' )
            {
                $comment[] = rtrim( preg_replace( '/^#\s?/', '', $trim ) );
                continue;
            }
            if ( preg_match( '/^\[([^\]]+)\]\s*$/', $trim, $m ) )
            {
                if ( $block === null && !$headerDone && $result['comment'] === '' && $comment && !$result['blocks'] )
                {
                    // the file's own header ran straight into the first block: keep it as the block's comment
                }
                $headerDone = true;
                $block = $m[1];
                if ( !isset( $result['blocks'][$block] ) )
                    $result['blocks'][$block] = array( 'comment' => '', 'settings' => array() );
                if ( $comment )
                    $result['blocks'][$block]['comment'] = trim( $result['blocks'][$block]['comment'] . "\n" . implode( "\n", $comment ) );
                $comment = array();
                $lastSetting = null;
                continue;
            }
            if ( $block === null )
                continue;
            if ( !preg_match( '/^([A-Za-z0-9_.\-]+)(\[([^\]]*)\])?(=(.*))?$/', $trim, $m ) )
                continue;
            $name = $m[1];
            $isArray = isset( $m[2] ) && $m[2] !== '';
            $hasValue = isset( $m[4] ) && $m[4] !== '';
            $value = $hasValue ? $m[5] : null;
            $settings =& $result['blocks'][$block]['settings'];
            if ( !isset( $settings[$name] ) )
                $settings[$name] = array( 'kind' => $isArray ? 'list' : 'scalar', 'value' => null, 'items' => array(), 'reset' => false, 'comment' => '' );
            if ( $comment )
            {
                $settings[$name]['comment'] = trim( $settings[$name]['comment'] . "\n" . implode( "\n", $comment ) );
                $comment = array();
            }
            if ( $isArray )
            {
                if ( $settings[$name]['kind'] === 'scalar' )
                    $settings[$name]['kind'] = 'list';
                $key = $m[3];
                if ( !$hasValue )
                    $settings[$name]['reset'] = true;
                else
                {
                    if ( $key !== '' )
                        $settings[$name]['kind'] = 'hash';
                    $settings[$name]['items'][] = array( $key === '' ? null : $key, $value );
                }
            }
            else if ( $hasValue || substr( $trim, -1 ) === '=' )
                $settings[$name]['value'] = (string)$value;
            unset( $settings );
            $lastSetting = $name;
        }
        return $result;
    }

    /**
     * @param array $setting
     * @return string
     */
    protected static function iniDefaultText( array $setting )
    {
        if ( $setting['kind'] === 'scalar' )
            return $setting['value'] === null ? 'not set' : ( $setting['value'] === '' ? 'empty' : '`' . str_replace( '`', "'", $setting['value'] ) . '`' );
        if ( !$setting['items'] )
            return 'an empty list';
        $parts = array();
        foreach ( array_slice( $setting['items'], 0, 12 ) as $item )
            $parts[] = '`' . str_replace( '`', "'", ( $item[0] !== null ? $item[0] . '=' : '' ) . $item[1] ) . '`';
        return ( $setting['kind'] === 'hash' ? 'a hash of ' : 'a list of ' ) . count( $setting['items'] ) . ': ' . implode( ', ', $parts ) . ( count( $setting['items'] ) > 12 ? ' ...' : '' );
    }

    /**
     * @param string $name
     * @param array $setting
     * @return string[]
     */
    protected static function iniOverrideLines( $name, array $setting )
    {
        if ( $setting['kind'] === 'scalar' )
            return array( $name . '=' . (string)$setting['value'] );
        $lines = array( '# an empty "' . $name . '[]" first replaces the shipped list; without it the items are added' );
        $lines[] = $name . '[]';
        foreach ( array_slice( $setting['items'], 0, 20 ) as $item )
            $lines[] = $name . '[' . ( $item[0] !== null ? $item[0] : '' ) . ']=' . $item[1];
        if ( count( $setting['items'] ) > 20 )
            $lines[] = '# ... ' . ( count( $setting['items'] ) - 20 ) . ' more in the shipped file';
        return $lines;
    }

    /**
     * The values of a hash setting of a tracked INI file.
     *
     * @return array key => value
     */
    protected function iniValues( $file, $block, $name )
    {
        $parsed = $this->parseIni( $file );
        $values = array();
        if ( isset( $parsed['blocks'][$block]['settings'][$name] ) )
            foreach ( $parsed['blocks'][$block]['settings'][$name]['items'] as $item )
                if ( $item[0] !== null )
                    $values[$item[0]] = $item[1];
        return $values;
    }

    /**
     * @return string[] the values of a list setting of a tracked INI file
     */
    protected function iniList( $file, $block, $name )
    {
        $parsed = $this->parseIni( $file );
        $values = array();
        if ( isset( $parsed['blocks'][$block]['settings'][$name] ) )
            foreach ( $parsed['blocks'][$block]['settings'][$name]['items'] as $item )
                $values[] = $item[1];
        return $values;
    }

    // ------------------------------------------------------------------------------------------------------------
    // Datatypes

    protected function areaDatatypes()
    {
        $area = 'datatypes';
        $available = $this->iniList( 'settings/content.ini', 'DataTypeSettings', 'AvailableDataTypes' );
        $found = array();
        foreach ( $this->files as $file )
        {
            if ( !preg_match( '#^(kernel/classes/datatypes|extension/[^/]+/datatypes)/([^/]+)/\2type\.php$#', $file, $m ) )
                continue;
            $source = (string)file_get_contents( $this->root . '/' . $file );
            if ( !preg_match( "/const\s+DATA_TYPE_STRING\s*=\s*['\"]([a-z0-9_]+)['\"]/i", $source, $s ) )
                continue;
            $info = array( 'file' => $file, 'string' => $s[1], 'class' => '', 'name' => '', 'fields' => array(), 'flags' => array(), 'sort' => '' );
            if ( preg_match( '/class\s+(\w+)\s+extends\s+(\w+)/', $source, $c ) )
                $info['class'] = $c[1];
            if ( preg_match( "/ezpI18n::tr\(\s*'kernel\/classes\/datatypes'\s*,\s*(['\"])(.*?)\\1/", $source, $n ) )
                $info['name'] = $n[2];
            preg_match_all( "/['\"](data_(?:text|int|float)\d?|sort_key_(?:int|string))['\"]/", $source, $f );
            $fields = array_values( array_unique( $f[1] ) );
            sort( $fields, SORT_STRING );
            $info['fields'] = $fields;
            foreach ( array( 'isIndexable' => 'indexed by the search', 'isInformationCollector' => 'collects information (forms)', 'isTranslatable' => null, 'isSimpleStringInsertionSupported' => 'can be filled from a plain string', 'isHTTPFileInsertionSupported' => 'can be filled from an uploaded file', 'supportsBatchInitializeObjectAttribute' => null ) as $method => $label )
                if ( $label !== null && preg_match( '/function\s+' . $method . '\s*\([^)]*\)\s*(?::\s*\w+\s*)?\{\s*return\s+true\s*;/', $source ) )
                    $info['flags'][] = $label;
            if ( preg_match( '/function\s+isTranslatable\s*\([^)]*\)\s*(?::\s*\w+\s*)?\{\s*return\s+false\s*;/', $source ) )
                $info['flags'][] = 'not translatable (the same value in every language)';
            if ( preg_match( "/function\s+sortKeyType\s*\([^)]*\)\s*(?::\s*\w+\s*)?\{\s*return\s+'(\w+)'/", $source, $k ) )
                $info['sort'] = $k[1];
            $found[$s[1]] = $info;
        }
        ksort( $found, SORT_STRING );
        $rows = array();
        foreach ( $found as $string => $info )
        {
            $md = array( "# The $string datatype" . ( $info['name'] !== '' ? ' (' . $info['name'] . ')' : '' ), '' );
            $md[] = 'Class `' . $info['class'] . '` in `' . $info['file'] . '`; identifier `' . $string . '`' . ( in_array( $string, $available, true ) ? ', listed in `settings/content.ini` `[DataTypeSettings] AvailableDataTypes[]`' : ', not in the shipped `AvailableDataTypes[]` (an extension or override adds it)' ) . '.';
            $md[] = '';
            $md[] = '## What it stores';
            $md[] = '';
            if ( $info['fields'] )
                $md[] = 'The columns of the attribute tables its class reads or writes: ' . implode( ', ', array_map( function ( $f ) { return '`' . $f . '`'; }, $info['fields'] ) ) . ' (`data_text`, `data_int`, `data_float` and `sort_key_*` are the object attribute\'s; `data_text1` ... `data_int4` the class attribute\'s settings). Found by reading the class: a heuristic.';
            else
                $md[] = 'Its class names none of the standard columns; it stores its value elsewhere (a table of its own or another object).';
            if ( $info['sort'] !== '' )
                $md[] = 'Sorting: `sortKeyType()` returns `' . $info['sort'] . '`.';
            if ( $info['flags'] )
                $md[] = 'It is ' . implode( '; ', $info['flags'] ) . '.';
            $md[] = '';
            $md[] = '## In templates';
            $md[] = '';
            $md[] = '`my_attribute` stands for the identifier of an attribute of this type in your class:';
            $md[] = '';
            $md[] = '```tpl';
            $md[] = '{def $attribute = $node.data_map.my_attribute}';
            $md[] = '{if $attribute.has_content}';
            $md[] = '    {attribute_view_gui attribute=$attribute}';
            $md[] = '{/if}';
            $md[] = '{* the value as the datatype returns it (objectAttributeContent()) *}';
            $md[] = '{$attribute.content|attribute( show, 1 )}';
            $md[] = '```';
            $md[] = '';
            $views = array();
            foreach ( array( 'view' => 'shows it (`attribute_view_gui`)', 'edit' => 'edits it (`attribute_edit_gui`)' ) as $kind => $label )
            {
                $tpl = 'design/standard/templates/content/datatype/' . $kind . '/' . $string . '.tpl';
                if ( in_array( $tpl, $this->files, true ) )
                    $views[$tpl] = $label;
            }
            foreach ( $views as $tpl => $label )
            {
                $lines = preg_split( '/\r?\n/', rtrim( (string)file_get_contents( $this->root . '/' . $tpl ) ) );
                $md[] = 'The shipped template that ' . $label . ', `' . $tpl . '`' . ( count( $lines ) > 25 ? ' (first 25 lines)' : '' ) . ':';
                $md[] = '';
                $md[] = '```tpl';
                foreach ( array_slice( $lines, 0, 25 ) as $l )
                    $md[] = str_replace( '```', '` ` `', $l );
                $md[] = '```';
                $md[] = '';
            }
            $md[] = 'Override them for one class attribute in an `override.ini.append.php` with `Source=content/datatype/view/' . $string . '.tpl` and a match on `attribute_identifier` (see [A template override](../cookbook/template-override.md)).';
            $md[] = '';
            $md[] = '## Related';
            $md[] = '';
            $md[] = '- [All datatypes](README.md)';
            $md[] = '- [A custom datatype](../cookbook/custom-datatype.md)';
            $md[] = '- [A content class](../cookbook/content-class.md)';
            $this->page( "$area/" . self::fileName( $string ) . '.md', "$string datatype" . ( $info['name'] !== '' ? ' (' . $info['name'] . ')' : '' ),
                         "The $string datatype" . ( $info['name'] !== '' ? ' (' . $info['name'] . ')' : '' ) . ': what it stores and how templates use it.',
                         array( $string, $info['name'], 'datatype', 'attribute', $info['class'] ), array_merge( array( $info['file'], 'settings/content.ini' ), array_keys( $views ) ), $md );
            $rows[] = '| [' . $string . '](' . self::fileName( $string ) . '.md) | ' . self::cell( $info['name'] ) . ' | `' . $info['class'] . '` | ' . ( in_array( $string, $available, true ) ? 'yes' : 'no' ) . ' |';
            ++$this->counts[$area];
        }
        $md = array_merge( array( '# Datatypes', '', 'Every datatype of the kernel and the bundled extensions: its identifier, what it stores and how a template shows it, with the shipped view and edit templates.', '',
                                  '| Identifier | Name | Class | Available by default |', '|---|---|---|---|' ), $rows );
        $this->index( $area, 'Datatypes', 'Every datatype: identifier, storage columns and template examples.', $md, array( 'settings/content.ini' ) );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Workflow events and triggers

    protected function areaWorkflowEvents()
    {
        $area = 'workflow-events';
        $available = $this->iniList( 'settings/workflow.ini', 'EventSettings', 'AvailableEventTypes' );
        $events = array();
        foreach ( $this->files as $file )
        {
            if ( !preg_match( '#workflowtypes/event/([^/]+)/\1type\.php$#', $file, $m ) )
                continue;
            $source = (string)file_get_contents( $this->root . '/' . $file );
            $string = preg_match( "/const\s+WORKFLOW_TYPE_STRING\s*=\s*['\"]([a-z0-9_]+)['\"]/i", $source, $s ) ? $s[1] : $m[1];
            $class = preg_match( '/class\s+(\w+)\s+extends\s+(\w+)/', $source, $c ) ? $c[1] : '';
            $name = preg_match( "/ezpI18n::tr\(\s*'kernel\/workflow\/event'\s*,\s*(['\"])(.*?)\\1/", $source, $n ) ? $n[2] : '';
            $triggers = array();
            if ( preg_match( "/setTriggerTypes\(\s*array\((.*?)\)\s*\)\s*;/s", $source, $t ) )
            {
                preg_match_all( "/'([a-z_*]+)'\s*=>\s*array\(\s*'([a-z_*]+)'\s*=>\s*array\(([^)]*)\)/", $t[1], $tt, PREG_SET_ORDER );
                foreach ( $tt as $one )
                {
                    preg_match_all( "/'([a-z_*]+)'/", $one[3], $when );
                    $triggers[] = $one[1] . '/' . $one[2] . ( $when[1] ? ' (' . implode( ', ', $when[1] ) . ')' : '' );
                }
            }
            $events[$string] = array( 'file' => $file, 'class' => $class, 'name' => $name, 'triggers' => $triggers, 'dir' => dirname( $file ) );
        }
        ksort( $events, SORT_STRING );
        $rows = array();
        foreach ( $events as $string => $info )
        {
            $md = array( "# The $string workflow event" . ( $info['name'] !== '' ? ' (' . $info['name'] . ')' : '' ), '' );
            $md[] = 'Class `' . $info['class'] . '` in `' . $info['file'] . '`; in a workflow it is the event type `event_' . $string . '`' . ( in_array( 'event_' . $string, $available, true ) ? ', listed in `settings/workflow.ini` `[EventSettings] AvailableEventTypes[]`' : ' (add it to `[EventSettings] AvailableEventTypes[]` to offer it)' ) . '.';
            $md[] = '';
            if ( $info['triggers'] )
            {
                $md[] = 'It may be used on these triggers only (`setTriggerTypes()`): ' . implode( ', ', array_map( function ( $t ) { return '`' . $t . '`'; }, $info['triggers'] ) ) . '.';
                $md[] = '';
            }
            $md[] = '## Use it';
            $md[] = '';
            $md[] = '1. Setup > Workflows: create a workflow in a group and add the event "' . ( $info['name'] !== '' ? $info['name'] : $string ) . '"; fill in its settings.';
            $md[] = '2. Setup > Triggers: connect the workflow to a trigger, for example `content / publish / before` (see the [triggers](triggers.md)).';
            $md[] = '3. Workflows that wait (approval, a date) continue when the cronjob part `frequent` runs `workflow.php` (see the [cronjobs](../cronjobs/README.md)).';
            $md[] = '';
            $md[] = 'To offer it (it is shipped in the list unless noted above):';
            $md[] = '';
            $md[] = '```ini';
            $md[] = '<?php /* #?ini charset="utf-8"?';
            $md[] = '# settings/override/workflow.ini.append.php';
            $md[] = '[EventSettings]';
            $md[] = 'AvailableEventTypes[]=event_' . $string;
            $md[] = '*/ ?>';
            $md[] = '```';
            $md[] = '';
            $tpls = array();
            foreach ( $this->files as $f )
                if ( preg_match( '#^design/[^/]+/templates/workflow/eventtype/(view|edit|result)/event_' . preg_quote( $string, '#' ) . '\.tpl$#', $f ) )
                    $tpls[] = $f;
            if ( $tpls )
            {
                $md[] = 'Its templates: ' . implode( ', ', array_map( function ( $t ) { return '`' . $t . '`'; }, $tpls ) ) . '.';
                $md[] = '';
            }
            $md[] = '## Related';
            $md[] = '';
            $md[] = '- [All workflow event types](README.md)';
            $md[] = '- [Triggers](triggers.md)';
            $md[] = '- [A workflow event of your own](../cookbook/workflow-event.md)';
            $md[] = '- [Workflows](../../guides/workflows.md)';
            $this->page( "$area/" . self::fileName( $string ) . '.md', "$string workflow event" . ( $info['name'] !== '' ? ' (' . $info['name'] . ')' : '' ),
                         "The $string workflow event type: its class, the triggers it accepts and how to use it.",
                         array( $string, 'event_' . $string, 'workflow', 'event', 'trigger', $info['class'] ), array_merge( array( $info['file'], 'settings/workflow.ini' ), $tpls ), $md );
            $rows[] = '| [' . $string . '](' . self::fileName( $string ) . '.md) | ' . self::cell( $info['name'] ) . ' | `' . $info['class'] . '` | ' . ( $info['triggers'] ? self::cell( implode( ', ', $info['triggers'] ) ) : 'any' ) . ' |';
            ++$this->counts[$area];
        }

        // triggers from the operation definitions
        $triggerRows = array();
        $sources = array();
        foreach ( $this->files as $file )
        {
            if ( basename( $file ) !== 'operation_definition.php' )
                continue;
            $module = basename( dirname( $file ) );
            $operations = $this->readDefinition( $file, 'OperationList' );
            if ( !$operations )
                continue;
            $sources[] = $file;
            ksort( $operations, SORT_STRING );
            foreach ( $operations as $operation => $def )
            {
                $body = isset( $def['body'] ) && is_array( $def['body'] ) ? $def['body'] : array();
                $params = isset( $def['parameters'] ) && is_array( $def['parameters'] ) ? $def['parameters'] : array();
                foreach ( $body as $step )
                    if ( is_array( $step ) && isset( $step['type'], $step['name'] ) && $step['type'] === 'trigger' )
                        $triggerRows[] = '| `' . $module . '` | `' . $operation . '` | `' . $step['name'] . '` | ' . ( strpos( $step['name'], 'pre_' ) === 0 ? 'before' : 'after' ) . ' | ' . self::cell( implode( ', ', array_map( function ( $p ) { return isset( $p['name'] ) ? $p['name'] : ''; }, $params ) ) ) . ' |';
            }
        }
        $md = array_merge( array( '# Workflow triggers', '',
                                  'The points where a workflow can run: each operation of `' . implode( '`, `', $sources ) . '` and its `pre_` (before) and `post_` (after) triggers. Connect a workflow at Setup > Triggers; the operation\'s parameters are what its events receive.', '',
                                  '| Module | Operation | Trigger | When | Parameters |', '|---|---|---|---|---|' ), $triggerRows );
        $md[] = '';
        $md[] = '## Related';
        $md[] = '';
        $md[] = '- [Workflow event types](README.md)';
        $md[] = '- [Workflows](../../guides/workflows.md)';
        $this->page( "$area/triggers.md", 'Workflow triggers', 'Every workflow trigger point: the content, shop and user operations and their before and after triggers.',
                     array( 'trigger', 'workflow', 'operation', 'pre_publish', 'post_publish' ), $sources, $md, 5 );
        ++$this->counts[$area];

        $md = array_merge( array( '# Workflow event types and triggers', '',
                                  'Every workflow event type of the kernel and the bundled extensions, and every [trigger](triggers.md) a workflow can be connected to.', '',
                                  '| Event | Name | Class | Triggers it accepts |', '|---|---|---|---|' ), $rows );
        $this->index( $area, 'Workflow event types and triggers', 'Every workflow event type and every trigger point.', $md, array( 'settings/workflow.ini' ) );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Cronjobs

    protected function areaCronjobs()
    {
        $area = 'cronjobs';
        $parsed = $this->parseIni( 'settings/cronjob.ini' );
        $parts = array();
        $inPart = array();
        foreach ( $parsed['blocks'] as $block => $info )
        {
            if ( $block !== 'CronjobSettings' && strpos( $block, 'CronjobPart-' ) !== 0 )
                continue;
            $part = $block === 'CronjobSettings' ? '(default)' : substr( $block, strlen( 'CronjobPart-' ) );
            $scripts = isset( $info['settings']['Scripts'] ) ? array_map( function ( $i ) { return $i[1]; }, $info['settings']['Scripts']['items'] ) : array();
            $parts[$part] = array( 'scripts' => $scripts, 'comment' => $info['comment'] );
            foreach ( $scripts as $s )
                $inPart[$s][] = $part;
        }
        $rows = array();
        foreach ( $this->files as $file )
        {
            if ( !preg_match( '#^(cronjobs|extension/([^/]+)/cronjobs)/([^/]+)\.php$#', $file, $m ) )
                continue;
            $script = $m[3] . '.php';
            $source = (string)file_get_contents( $this->root . '/' . $file );
            $description = preg_match( '/@description\s+(.+)/', $source, $d ) ? rtrim( trim( $d[1] ), '.' ) : '';
            $classFile = null;
            $header = '';
            if ( preg_match( '/\\\\Exponential\\\\Cronjob\\\\Kernel\\\\(\w+)::main/', $source, $c ) )
            {
                $candidate = 'kernel/private/classes/cronjobs/' . strtolower( $m[3] ) . '.php';
                if ( in_array( $candidate, $this->files, true ) )
                {
                    $classFile = $candidate;
                    $header = self::docblockText( (string)file_get_contents( $this->root . '/' . $candidate ) );
                }
            }
            if ( $header === '' )
                $header = self::docblockText( $source );
            $partsOf = isset( $inPart[$script] ) ? $inPart[$script] : array();
            $md = array( "# The $script cronjob", '' );
            if ( $description !== '' )
                $md[] = rtrim( $description, '.' ) . '.';
            $md[] = '';
            $md[] = 'Entry point `' . $file . '`' . ( $classFile ? ', code in `' . $classFile . '`' : '' ) . '.';
            $md[] = '';
            if ( $header !== '' )
            {
                $md = array_merge( $md, self::quote( $header ) );
                $md[] = '';
            }
            $md[] = '## When it runs';
            $md[] = '';
            if ( $partsOf )
                $md[] = 'In the shipped `settings/cronjob.ini` it belongs to: ' . implode( ', ', array_map( function ( $p ) { return $p === '(default)' ? 'the default part (`[CronjobSettings]`, `runcronjobs.php` without a part)' : 'the part `' . $p . '`'; }, $partsOf ) ) . '.';
            else
                $md[] = 'It is in no shipped part: add it to one in an override (below) or run it on its own.';
            $md[] = '';
            $md[] = '```bash';
            if ( $partsOf )
            {
                $p = $partsOf[0];
                $md[] = '# run its part (as the user the web server runs as, from the installation root)';
                $md[] = 'php runcronjobs.php' . ( $p === '(default)' ? '' : ' ' . $p );
                $md[] = './console exp:runcronjobs' . ( $p === '(default)' ? '' : ' ' . $p );
            }
            $md[] = '# run only this script';
            $md[] = 'php runcronjobs.php ' . $m[3];
            $md[] = '```';
            $md[] = '';
            $md[] = 'Add it to a part in an override:';
            $md[] = '';
            $md[] = '```ini';
            $md[] = '<?php /* #?ini charset="utf-8"?';
            $md[] = '# settings/override/cronjob.ini.append.php';
            $md[] = '[CronjobPart-' . ( $partsOf && $partsOf[0] !== '(default)' ? $partsOf[0] : 'frequent' ) . ']';
            $md[] = 'Scripts[]=' . $script;
            $md[] = '*/ ?>';
            $md[] = '```';
            $md[] = '';
            $md[] = '## Related';
            $md[] = '';
            $md[] = '- [All cronjobs and parts](README.md)';
            $md[] = '- [Cronjobs](../../guides/cronjobs.md)';
            $md[] = '- [A cronjob of your own](../cookbook/cronjob.md)';
            $this->page( "$area/" . self::fileName( $m[3] ) . '.md', "$script cronjob", ( $description !== '' ? $description : "The cronjob script $script" ) . '.',
                         array( $script, $m[3], 'cronjob', 'runcronjobs', 'part' ), array_values( array_filter( array( $file, $classFile, 'settings/cronjob.ini' ) ) ), $md );
            $rows[] = '| [' . $script . '](' . self::fileName( $m[3] ) . '.md) | ' . self::cell( $description ) . ' | ' . self::cell( implode( ', ', $partsOf ) ) . ' |';
            ++$this->counts[$area];
        }
        $partRows = array();
        foreach ( $parts as $part => $info )
            $partRows[] = '| ' . ( $part === '(default)' ? '(default)' : '`' . $part . '`' ) . ' | ' . implode( ', ', array_map( function ( $s ) { return '[' . $s . '](' . self::fileName( substr( $s, 0, -4 ) ) . '.md)'; }, $info['scripts'] ) ) . ' | `php runcronjobs.php' . ( $part === '(default)' ? '' : ' ' . $part ) . '` |';
        $md = array_merge( array( '# Cronjob scripts and parts', '',
                                  'Every cronjob script of the kernel and the bundled extensions and the parts of the shipped `settings/cronjob.ini` that run them.', '',
                                  '## Parts', '', '| Part | Scripts | Run it |', '|---|---|---|' ), $partRows,
                           array( '', '## Scripts', '', '| Script | What it does | Parts |', '|---|---|---|' ), $rows );
        $this->index( $area, 'Cronjob scripts and parts', 'Every cronjob script and cronjob part, with how to run and schedule them.', $md, array( 'settings/cronjob.ini' ) );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Commands

    protected function areaCommands()
    {
        $area = 'commands';
        $rows = array();
        foreach ( $this->files as $file )
        {
            if ( !preg_match( '#^bin/php/([a-z0-9_\-]+)\.php$#', $file, $m ) )
                continue;
            $name = $m[1];
            $source = (string)file_get_contents( $this->root . '/' . $file );
            $classFile = null;
            if ( preg_match( '/\\\\Exponential\\\\Command\\\\Kernel\\\\(\w+)::main/', $source, $c ) )
            {
                $candidate = 'kernel/private/classes/commands/' . strtolower( $c[1] ) . '.php';
                if ( in_array( $candidate, $this->files, true ) )
                    $classFile = $candidate;
            }
            $code = $classFile !== null ? (string)file_get_contents( $this->root . '/' . $classFile ) : $source;
            $summary = '';
            foreach ( array( $source, $code ) as $text )
                if ( $summary === '' && preg_match( '/@description\s+(.+)/', $text, $d ) )
                    $summary = rtrim( trim( $d[1] ), "." );
            $aliases = array();
            foreach ( array( $source, $code ) as $text )
                if ( preg_match_all( '/@alias\s+([A-Za-z0-9:_\-]+)/', $text, $a ) )
                    foreach ( $a[1] as $alias )
                        $aliases[] = strpos( $alias, ':' ) === false ? 'exp:' . $alias : $alias;
            $aliases = array_values( array_unique( $aliases ) );
            $parsed = self::scriptHelp( $code );
            $md = array( "# exp:$name", '' );
            if ( $summary !== '' )
                $md[] = rtrim( $summary, '.' ) . '.';
            $md[] = '';
            $md[] = 'Script `' . $file . '`' . ( $classFile ? ', code in `' . $classFile . '`' : '' ) . '. Run it from the installation root, as the user the web server runs as:';
            $md[] = '';
            $md[] = '```bash';
            $md[] = './console exp:' . $name . ' --help';
            foreach ( array_diff( $aliases, array( 'exp:' . $name ) ) as $alias )
                $md[] = './console ' . $alias . ' --help';
            $md[] = 'php ' . $file . ' --help';
            $md[] = '```';
            $md[] = '';
            if ( $parsed['description'] !== '' )
            {
                $md[] = '## Help text';
                $md[] = '';
                $md[] = '```text';
                foreach ( preg_split( '/\r?\n/', rtrim( $parsed['description'] ) ) as $l )
                    $md[] = str_replace( '```', "'''", $l );
                $md[] = '```';
                $md[] = '';
            }
            if ( $parsed['options'] )
            {
                $md[] = '## Options';
                $md[] = '';
                $md[] = '| Option | Value | Help |';
                $md[] = '|---|---|---|';
                foreach ( $parsed['options'] as $opt => $o )
                    $md[] = '| `' . ( strlen( $opt ) === 1 ? '-' : '--' ) . $opt . '` | ' . $o['value'] . ' | ' . self::cell( $o['help'] ) . ' |';
                $md[] = '';
                $md[] = 'Read from the option definitions in the code (`startup()`, `getOptions()`, `options()` or `ezcConsoleOption`). Every script also takes the standard options of the script framework (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).';
                $md[] = '';
            }
            if ( $parsed['arguments'] !== '' )
            {
                $md[] = 'Arguments: `' . $parsed['arguments'] . '`.';
                $md[] = '';
            }
            $md[] = '## Related';
            $md[] = '';
            $md[] = '- [All console commands](README.md)';
            $this->page( "$area/" . self::fileName( $name ) . '.md', "exp:$name", ( $summary !== '' ? $summary : "The command exp:$name ($file)" ) . '.',
                         array_merge( array( 'exp:' . $name, $name, 'console', 'command', 'CLI', basename( $file ) ), $aliases ), array_values( array_filter( array( $file, $classFile ) ) ), $md );
            $rows[] = '| [exp:' . $name . '](' . self::fileName( $name ) . '.md) | ' . self::cell( implode( ', ', array_diff( $aliases, array( 'exp:' . $name ) ) ) ) . ' | ' . self::cell( $summary ) . ' |';
            ++$this->counts[$area];
        }
        $shell = array();
        foreach ( $this->files as $file )
            if ( preg_match( '#^bin/shell/([a-z0-9_\-]+)\.sh$#', $file, $m ) )
            {
                $text = (string)file_get_contents( $this->root . '/' . $file );
                $shell[] = '| `shell:' . $m[1] . '` | `' . $file . '` | ' . self::cell( preg_match( '/#\s*@description\s+(.+)/', $text, $d ) ? trim( $d[1] ) : '' ) . ' |';
            }
        $md = array_merge( array( '# Console commands', '',
                                  'Every command of `./console` (`bin/php/console`): each `bin/php/<name>.php` is `exp:<name>`, a script may add aliases with `@alias`. The help text and the options are read from the code, so they match `--help`. `./console list` shows them on a machine; `./console help <command>` prints one command\'s help.', '',
                                  '| Command | Aliases | What it does |', '|---|---|---|' ), $rows );
        if ( $shell )
            $md = array_merge( $md, array( '', '## Shell scripts', '', '`bin/shell/<name>.sh` runs as `shell:<name>`:', '', '| Command | File | What it does |', '|---|---|---|' ), $shell );
        $this->index( $area, 'Console commands', 'Every ./console command with its help text, options and aliases.', $md, array( 'bin/php/console' ) );
    }

    /**
     * The description, options and argument configuration of a script, read from its code without running it:
     * the 'description' of eZScript::instance() / $this->script(), and the first call of startup() or getOptions().
     *
     * @param string $code
     * @return array array( 'description' => string, 'options' => name => array( 'value', 'help' ), 'arguments' => string )
     */
    public static function scriptHelp( $code )
    {
        $result = array( 'description' => '', 'options' => array(), 'arguments' => '' );
        $tokens = array_values( array_filter( token_get_all( $code ), function ( $t ) { return !is_array( $t ) || !in_array( $t[0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ); } ) );
        $count = count( $tokens );
        for ( $i = 0; $i < $count; ++$i )
        {
            $t = $tokens[$i];
            if ( $result['description'] === '' && is_array( $t ) && $t[0] === T_CONSTANT_ENCAPSED_STRING && self::stringValue( $t[1] ) === 'description'
                 && isset( $tokens[$i + 1] ) && is_array( $tokens[$i + 1] ) && $tokens[$i + 1][0] === T_DOUBLE_ARROW )
            {
                list( $value, ) = self::readStringExpression( $tokens, $i + 2 );
                if ( $value !== null )
                    $result['description'] = $value;
            }
            if ( is_array( $t ) && $t[0] === T_STRING && in_array( $t[1], array( 'startup', 'getOptions', 'options' ), true )
                 && isset( $tokens[$i + 1] ) && $tokens[$i + 1] === '(' && !$result['options'] )
            {
                list( $config, $next ) = self::readStringExpression( $tokens, $i + 2 );
                if ( $config === null || strpos( $config, '[' ) === false )
                    continue;
                $arguments = '';
                $help = array();
                if ( isset( $tokens[$next] ) && $tokens[$next] === ',' )
                {
                    list( $arguments, $next ) = self::readStringExpression( $tokens, $next + 1 );
                    if ( isset( $tokens[$next] ) && $tokens[$next] === ',' )
                        $help = self::readStringArray( $tokens, $next + 1 );
                }
                $result['arguments'] = (string)$arguments;
                preg_match_all( '/\[([^\]]+)\]/', $config, $opts );
                foreach ( $opts[1] as $spec )
                    foreach ( explode( '|', $spec ) as $one )
                    {
                        $one = trim( $one );
                        if ( $one === '' )
                            continue;
                        $kind = '';
                        if ( preg_match( '/^(.*?)([:;])$/', $one, $mm ) )
                        {
                            $one = $mm[1];
                            $kind = $mm[2] === ':' ? 'required' : 'optional';
                        }
                        $result['options'][$one] = array( 'value' => $kind, 'help' => isset( $help[$one] ) ? $help[$one] : '' );
                    }
            }
        }
        // Scripts built on ezcConsoleInput: new ezcConsoleOption( 'h', 'help', <type> ) and its ->shorthelp
        if ( !$result['options'] && preg_match_all( '/\$(\w+)\s*=\s*new\s+\\\\?ezcConsoleOption\(\s*\'([^\']*)\'\s*,\s*\'([^\']+)\'(?:\s*,\s*\\\\?ezcConsoleInput::TYPE_(\w+))?/', $code, $matches, PREG_SET_ORDER ) )
        {
            foreach ( $matches as $m )
            {
                $help = preg_match( '/\$' . preg_quote( $m[1], '/' ) . '->shorthelp\s*=\s*([\'"])(.*?)\1\s*;/s', $code, $h ) ? stripcslashes( $h[2] ) : '';
                $result['options'][$m[3]] = array( 'value' => isset( $m[4] ) && $m[4] !== '' && $m[4] !== 'NONE' ? 'required' : '', 'help' => $help . ( $m[2] !== '' ? ' (short: -' . $m[2] . ')' : '' ) );
            }
        }
        return $result;
    }

    /**
     * Reads string literals joined with "." from $tokens[$i]; stops at anything else.
     *
     * @return array array( string|null, index after the expression )
     */
    protected static function readStringExpression( array $tokens, $i )
    {
        $value = null;
        $count = count( $tokens );
        while ( $i < $count )
        {
            $t = $tokens[$i];
            if ( is_array( $t ) && $t[0] === T_CONSTANT_ENCAPSED_STRING )
            {
                $value = (string)$value . self::stringValue( $t[1] );
                ++$i;
                if ( isset( $tokens[$i] ) && $tokens[$i] === '.' )
                {
                    ++$i;
                    continue;
                }
                break;
            }
            break;
        }
        return array( $value, $i );
    }

    /**
     * Reads array( 'key' => 'value' [. 'more'], ... ) or [ ... ] of string pairs.
     *
     * @return array
     */
    protected static function readStringArray( array $tokens, $i )
    {
        $out = array();
        $count = count( $tokens );
        if ( isset( $tokens[$i] ) && is_array( $tokens[$i] ) && $tokens[$i][0] === T_ARRAY )
            $i += 2;
        else if ( isset( $tokens[$i] ) && $tokens[$i] === '[' )
            ++$i;
        else
            return $out;
        while ( $i < $count )
        {
            list( $key, $i ) = self::readStringExpression( $tokens, $i );
            if ( $key === null || !isset( $tokens[$i] ) || !is_array( $tokens[$i] ) || $tokens[$i][0] !== T_DOUBLE_ARROW )
                break;
            list( $value, $i ) = self::readStringExpression( $tokens, $i + 1 );
            if ( $value === null )
                break;
            $out[$key] = $value;
            if ( isset( $tokens[$i] ) && $tokens[$i] === ',' )
                ++$i;
            else
                break;
        }
        return $out;
    }

    /**
     * The value of a PHP string literal (single or double quoted, without variables).
     *
     * @param string $literal
     * @return string
     */
    protected static function stringValue( $literal )
    {
        $quote = $literal[0];
        $inner = substr( $literal, 1, -1 );
        if ( $quote === "'" )
            return str_replace( array( "\\\\", "\\'" ), array( "\\", "'" ), $inner );
        return stripcslashes( $inner );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Extension points

    protected function areaExtensionPoints()
    {
        $area = 'extension-points';
        $fileRows = array();
        foreach ( $this->iniFiles() as $file )
        {
            $parsed = $this->parseIni( $file );
            $ini = basename( $file );
            $points = array();
            foreach ( $parsed['blocks'] as $block => $info )
                foreach ( $info['settings'] as $name => $setting )
                {
                    if ( !preg_match( self::EXTENSION_POINT_PATTERN, $name ) )
                        continue;
                    $values = $setting['kind'] === 'scalar' ? array( (string)$setting['value'] ) : array_map( function ( $i ) { return $i[1]; }, $setting['items'] );
                    $points[] = array( 'block' => $block, 'name' => $name, 'setting' => $setting, 'values' => $values );
                }
            if ( !$points )
                continue;
            $ext = preg_match( '#^extension/([^/]+)/#', $file, $m ) ? $m[1] : null;
            $slug = ( $ext !== null ? $ext . '-' : '' ) . substr( $ini, 0, -4 );
            $iniFolder = $slug;
            $md = array( "# Extension points in $ini", '' );
            $md[] = 'Settings of `' . $file . '` whose name says they hold handlers, classes, filters, providers or directories that an extension can add to or replace. **Found by the setting\'s name: a heuristic.** Read the setting\'s comment (linked) before relying on it.';
            $md[] = '';
            $md[] = 'An extension adds to a list in its own `settings/' . $ini . '.append.php` (active extensions\' settings are read after the kernel\'s and before `settings/override`):';
            $md[] = '';
            foreach ( $points as $p )
            {
                $blockSlug = self::fileName( $p['block'] );
                $md[] = '## ' . $p['block'] . ' ' . $p['name'];
                $md[] = '';
                $md[] = 'Shipped: ' . self::iniDefaultText( $p['setting'] ) . '. [The setting](../ini-settings/' . $iniFolder . '/' . $blockSlug . '.md#' . self::anchor( $p['name'] ) . ').';
                $md[] = '';
                $md[] = '```ini';
                $md[] = '<?php /* #?ini charset="utf-8"?';
                $md[] = '# extension/expexample/settings/' . $ini . '.append.php';
                $md[] = '[' . $p['block'] . ']';
                if ( $p['setting']['kind'] === 'scalar' )
                    $md[] = $p['name'] . '=' . ( $p['values'] && $p['values'][0] !== '' ? $p['values'][0] : '' ) . '    # replace with your class or value';
                else if ( $p['setting']['kind'] === 'hash' )
                    $md[] = $p['name'] . '[expexample]=expExample' . preg_replace( '/s$/', '', $p['name'] ) . '    # key and value are yours';
                else
                    $md[] = $p['name'] . '[]=expExample' . preg_replace( '/(s|List)$/', '', $p['name'] ) . '    # your class or value, added to the list';
                $md[] = '*/ ?>';
                $md[] = '```';
                $md[] = '';
            }
            $md[] = '## Related';
            $md[] = '';
            $md[] = '- [All extension points](README.md)';
            $md[] = '- [An extension skeleton](../cookbook/extension-skeleton.md)';
            $md[] = '- [The blocks of ' . $ini . '](../ini-settings/' . $iniFolder . '/README.md)';
            $this->page( "$area/$slug.md", "Extension points in $ini", "Settings of $ini that list handlers, classes or directories an extension can add to (" . count( $points ) . ', found by name).',
                         array( $ini, 'extension point', 'handler', 'extension', 'hook' ), array( $file ), $md );
            $fileRows[] = '| [' . $ini . '](' . $slug . '.md) | ' . ( $ext !== null ? $ext : 'kernel' ) . ' | ' . count( $points ) . ' | ' . self::cell( implode( ', ', array_slice( array_map( function ( $p ) { return $p['name']; }, $points ), 0, 6 ) ) . ( count( $points ) > 6 ? ' ...' : '' ) ) . ' |';
            $this->counts[$area] += count( $points );
        }
        $md = array_merge( array( '# Extension points', '',
                                  'Where an extension plugs into the kernel through settings: INI settings that name handler classes, filters, providers, listeners, repositories or extension directories. They are **found by the setting\'s name (a heuristic)**, one page per INI file, each with an override an extension can ship. Other ways to extend: [template operators](../template-operators/README.md), [modules](../module-views/README.md), [datatypes](../datatypes/README.md), [workflow events](../workflow-events/README.md), [cronjobs](../cronjobs/README.md) and [design overrides](../cookbook/template-override.md).', '',
                                  '| INI file | From | Settings | Examples |', '|---|---|---|---|' ), $fileRows );
        $this->index( $area, 'Extension points', 'INI-driven handler, class and directory lists an extension can add to (found by name, a heuristic).', $md, array() );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Template uses

    /**
     * The first use of an operator ("op:name"), a function ("fn:name") or a fetch ("fetch:module/function") in a
     * tracked template, by path order.
     *
     * @param string $key
     * @return array|null array( path, line, text )
     */
    protected function templateUse( $key )
    {
        if ( $this->templateUses === null )
        {
            $this->templateUses = array();
            foreach ( $this->files as $file )
            {
                if ( substr( $file, -4 ) !== '.tpl' )
                    continue;
                $lines = preg_split( '/\r?\n/', (string)file_get_contents( $this->root . '/' . $file ) );
                foreach ( $lines as $n => $line )
                {
                    if ( strpos( $line, '{' ) === false || strlen( $line ) > 400 )
                        continue;
                    if ( !preg_match_all( '/\{[^{}]*\}/', $line, $tags ) )
                        continue;
                    foreach ( $tags[0] as $tag )
                    {
                        if ( preg_match( '/^\{\/?([a-z_][a-z0-9_\-]*)/', $tag, $f ) )
                            $this->noteUse( 'fn:' . $f[1], $file, $n + 1, $line );
                        if ( preg_match_all( '/\|\s*([a-z_][a-z0-9_]*)/', $tag, $o ) )
                            foreach ( $o[1] as $op )
                                $this->noteUse( 'op:' . $op, $file, $n + 1, $line );
                        if ( preg_match_all( '/(?<![\w.$|])([a-z_][a-z0-9_]*)\s*\(/', $tag, $o ) )
                            foreach ( $o[1] as $op )
                                $this->noteUse( 'op:' . $op, $file, $n + 1, $line );
                        if ( preg_match_all( "/fetch\(\s*['\"]([a-z0-9_]+)['\"]\s*,\s*['\"]([a-z0-9_]+)['\"]/", $tag, $fe, PREG_SET_ORDER ) )
                            foreach ( $fe as $one )
                                $this->noteUse( 'fetch:' . $one[1] . '/' . $one[2], $file, $n + 1, $line );
                    }
                }
            }
        }
        return isset( $this->templateUses[$key] ) ? $this->templateUses[$key] : null;
    }

    protected function noteUse( $key, $file, $line, $text )
    {
        if ( !isset( $this->templateUses[$key] ) )
            $this->templateUses[$key] = array( $file, $line, trim( $text ) );
    }

    /**
     * @param string[] $md
     * @param array|null $use
     */
    protected function appendUse( array &$md, $use )
    {
        if ( $use === null )
            return;
        $md[] = '## In a shipped template';
        $md[] = '';
        $md[] = 'The first use in the templates of this installation, `' . $use[0] . '` line ' . $use[1] . ':';
        $md[] = '';
        $md[] = '```tpl';
        $md[] = str_replace( '```', '` ` `', $use[2] );
        $md[] = '```';
        $md[] = '';
    }

    // ------------------------------------------------------------------------------------------------------------
    // Helpers

    /**
     * Includes a definition file (function_definition.php, operation_definition.php) and returns one of its arrays.
     *
     * @param string $file
     * @param string $variable FunctionList or OperationList
     * @return array
     */
    protected function readDefinition( $file, $variable )
    {
        $read = function ( $path, $variable )
        {
            $FunctionList = array();
            $OperationList = array();
            include $path;
            return $variable === 'FunctionList' ? $FunctionList : $OperationList;
        };
        try
        {
            $list = $read( $this->root . '/' . $file, $variable );
        }
        catch ( Throwable $e )
        {
            $this->warnings[] = "$file: " . $e->getMessage();
            return array();
        }
        return is_array( $list ) ? $list : array();
    }

    /**
     * The tracked file that declares a class: the kernel autoload array, else a scan of the tracked PHP files.
     *
     * @param string $class
     * @return string|null
     */
    protected function classFile( $class )
    {
        if ( $this->classFiles === null )
        {
            $this->classFiles = array();
            $tracked = array_flip( $this->files );
            if ( is_file( $this->root . '/autoload/ezp_kernel.php' ) )
            {
                $map = include $this->root . '/autoload/ezp_kernel.php';
                if ( is_array( $map ) )
                    foreach ( $map as $name => $path )
                        if ( isset( $tracked[$path] ) )
                            $this->classFiles[strtolower( $name )] = $path;
            }
            foreach ( $this->files as $file )
            {
                if ( substr( $file, -4 ) !== '.php' || strpos( $file, 'tests/' ) === 0 || strpos( $file, 'doc/' ) === 0 )
                    continue;
                if ( strpos( $file, 'extension/' ) !== 0 && strpos( $file, 'lib/' ) !== 0 && strpos( $file, 'kernel/' ) !== 0 )
                    continue;
                $text = (string)file_get_contents( $this->root . '/' . $file, false, null, 0, 65536 );
                if ( preg_match_all( '/^\s*(?:abstract\s+|final\s+)?class\s+(\w+)/m', $text, $m ) )
                    foreach ( $m[1] as $name )
                        if ( !isset( $this->classFiles[strtolower( $name )] ) )
                            $this->classFiles[strtolower( $name )] = $file;
            }
        }
        $key = strtolower( (string)$class );
        return isset( $this->classFiles[$key] ) ? $this->classFiles[$key] : null;
    }

    /**
     * The first paragraphs of the first docblock of a PHP file, without tags and copyright lines.
     *
     * @param string $source
     * @return string
     */
    protected static function docblockText( $source )
    {
        if ( !preg_match( '#/\*\*(.*?)\*/#s', $source, $m ) )
            return '';
        $lines = array();
        foreach ( preg_split( '/\r?\n/', $m[1] ) as $line )
        {
            $line = preg_replace( '/^\s*\*\s?/', '', $line );
            if ( preg_match( '/^@/', trim( $line ) ) || preg_match( '/^File containing/i', trim( $line ) ) )
                continue;
            $lines[] = rtrim( $line );
        }
        return trim( implode( "\n", $lines ) );
    }

    /**
     * Reads a protected property of an object by one of its names.
     *
     * @return mixed
     */
    protected static function objectProperty( $object, array $names )
    {
        foreach ( $names as $name )
        {
            try
            {
                $reflection = new ReflectionObject( $object );
                if ( $reflection->hasProperty( $name ) )
                {
                    $property = $reflection->getProperty( $name );
                    if ( PHP_VERSION_ID < 80100 )
                        $property->setAccessible( true );
                    return $property->getValue( $object );
                }
            }
            catch ( Throwable $e )
            {
            }
        }
        return null;
    }

    /**
     * Adds a generated page.
     *
     * @param string $relative path below the output directory
     * @param string $title
     * @param string $summary
     * @param string[] $keywords
     * @param string[] $sources tracked files it was derived from
     * @param string[] $body Markdown lines, starting with the "# " title
     * @param int|null $order
     */
    protected function page( $relative, $title, $summary, array $keywords, array $sources, array $body, $order = null )
    {
        $keywords = array_values( array_unique( array_filter( array_map( function ( $k ) { return trim( str_replace( array( ',', "\n" ), ' ', (string)$k ) ); }, $keywords ), 'strlen' ) ) );
        $lines = array( '---' );
        $lines[] = 'title: ' . self::frontValue( $title );
        $lines[] = 'audience: developer, admin';
        $lines[] = 'category: examples';
        if ( $order !== null )
            $lines[] = 'order: ' . (int)$order;
        $lines[] = 'summary: ' . self::frontValue( self::truncateText( $summary, 280 ) );
        $lines[] = 'keywords: ' . self::frontValue( implode( ', ', $keywords ) );
        $lines[] = 'type: ' . self::PAGE_TYPE;
        $lines[] = 'generated: ' . self::MARKER;
        $lines[] = '---';
        $body[] = '';
        $body[] = '---';
        $body[] = '';
        $body[] = 'Generated by `./console exp:help:examples` from ' . implode( ', ', array_map( function ( $s ) { return '`' . $s . '`'; }, array_values( array_unique( $sources ) ) ) ) . '. Do not edit this page: change the source and generate again ([how](' . str_repeat( '../', substr_count( $relative, '/' ) ) . 'README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](' . str_repeat( '../', substr_count( $relative, '/' ) + 1 ) . 'help/free-documentation.md).';
        $this->pages[$this->out . '/' . $relative] = implode( "\n", $lines ) . "\n" . implode( "\n", $body ) . "\n";
    }

    /**
     * Adds the index page (README.md) of an area or of a folder in it.
     */
    protected function index( $relativeDir, $title, $summary, array $body, array $sources, $order = 0 )
    {
        $body[] = '';
        $up = str_repeat( '../', substr_count( $relativeDir, '/' ) + 1 );
        $body[] = '- [All examples](' . $up . 'README.md)';
        $this->page( "$relativeDir/README.md", $title, $summary, array( $title, 'examples', 'reference' ), $sources ?: array( 'the sources named on each page' ), $body, $order );
        // the sources fallback reads as prose
        $path = $this->out . "/$relativeDir/README.md";
        $this->pages[$path] = str_replace( 'from `the sources named on each page`', 'from the sources named on each page', $this->pages[$path] );
    }

    /**
     * A front matter value on one line; quoted when it could be read as something else.
     *
     * @param string $value
     * @return string
     */
    protected static function frontValue( $value )
    {
        $value = trim( preg_replace( '/\s+/', ' ', (string)$value ) );
        if ( $value === '' || preg_match( '/^[\[\]{}"\'>|*&!%@`#-]|: | #/', $value ) || in_array( strtolower( $value ), array( 'true', 'false', 'null', 'yes', 'no' ), true ) )
            return '"' . str_replace( '"', "'", $value ) . '"';
        return $value;
    }

    /**
     * @param string $text
     * @param int $length
     * @return string
     */
    protected static function truncateText( $text, $length )
    {
        $text = trim( preg_replace( '/\s+/', ' ', (string)$text ) );
        if ( strlen( $text ) <= $length )
            return $text;
        $cut = substr( $text, 0, $length );
        $space = strrpos( $cut, ' ' );
        return rtrim( $space !== false ? substr( $cut, 0, $space ) : $cut, ' ,.;:' ) . ' ...';
    }

    /**
     * A comment of a shipped file as a block quote.
     *
     * @param string $text
     * @return string[]
     */
    protected static function quote( $text )
    {
        $out = array();
        foreach ( preg_split( '/\r?\n/', trim( $text ) ) as $line )
            // the comment is plain text: Markdown characters in it are escaped (a "[x](y)" in a comment is no link)
            $out[] = rtrim( '> ' . addcslashes( $line, '\\*_[]`' ) );
        // line breaks of the file are kept as separate lines of one quote
        return array_map( function ( $l ) { return $l === '>' ? '>' : $l . '  '; }, $out );
    }

    /**
     * A safe file name for a page.
     *
     * @param string $name
     * @return string
     */
    public static function fileName( $name )
    {
        $name = preg_replace( '/[^A-Za-z0-9_.\-]+/', '-', (string)$name );
        $name = trim( $name, '-.' );
        return $name === '' ? 'item' : $name;
    }

    /**
     * The heading anchor the help gives a heading of only this text (expHelpDocumentIndex::slug()).
     *
     * @param string $text
     * @return string
     */
    protected static function anchor( $text )
    {
        if ( class_exists( 'expHelpDocumentIndex' ) && method_exists( 'expHelpDocumentIndex', 'slug' ) )
            return expHelpDocumentIndex::slug( $text );
        return trim( preg_replace( '/[^a-z0-9_]+/', '-', strtolower( $text ) ), '-' );
    }

    /**
     * A table cell: no pipes or line breaks.
     *
     * @param string $text
     * @return string
     */
    protected static function cell( $text )
    {
        return str_replace( array( '|', "\r", "\n" ), array( '\\|', ' ', ' ' ), (string)$text );
    }

    /**
     * @param string $text
     * @return string inline code, or nothing for an empty text
     */
    protected static function code( $text )
    {
        $text = self::cell( $text );
        return $text === '' ? '' : '`' . str_replace( '`', "'", $text ) . '`';
    }

    /**
     * A PHP value in a short, stable form.
     *
     * @param mixed $value
     * @return string
     */
    protected static function export( $value )
    {
        if ( $value === null )
            return 'null';
        if ( is_bool( $value ) )
            return $value ? 'true' : 'false';
        if ( is_string( $value ) )
            return "'" . $value . "'";
        if ( is_scalar( $value ) )
            return (string)$value;
        if ( is_array( $value ) )
        {
            if ( !$value )
                return 'array()';
            $parts = array();
            $list = array_keys( $value ) === range( 0, count( $value ) - 1 );
            foreach ( $value as $k => $v )
                $parts[] = ( $list ? '' : self::export( $k ) . ' => ' ) . self::export( $v );
            return 'array( ' . implode( ', ', $parts ) . ' )';
        }
        return is_object( $value ) ? get_class( $value ) : gettype( $value );
    }

    /**
     * A nested array as "a, b (c, d)".
     *
     * @param mixed $value
     * @return string
     */
    protected static function flatten( $value )
    {
        if ( !is_array( $value ) )
            return is_scalar( $value ) ? (string)$value : '';
        $parts = array();
        foreach ( $value as $k => $v )
            $parts[] = is_array( $v ) ? ( is_string( $k ) ? $k . ' ' : '' ) . '(' . self::flatten( $v ) . ')' : ( is_string( $k ) ? $k . ': ' : '' ) . ( is_bool( $v ) ? ( $v ? 'true' : 'false' ) : (string)$v );
        return implode( ', ', $parts );
    }
}
