<?php
/**
 * exp:template:check: checks templates of every engine (Exponential templates .tpl, Smarty .stpl, Twig .twig and
 * .html.twig) for valid syntax: single files, directories, designs (in the kernel and in extensions), the designs of
 * extensions, whole siteaccesses (their design chain and override rules) or stdin. Levels syntax, compile (the
 * default) and strict; output as text or machine formats (json, jsonl, junit, checkstyle, github, sarif); baseline,
 * ignore lists and comments, stable rule ids, permanent exit codes. Read-only: compiled output goes to a private
 * directory below var/cache/template-check that is removed at the end.
 *
 * The command-line contract is frozen (doc/eng-us/guides/template-syntax-check.md, "Compatibility promise"):
 * expTemplateCommandContractTest holds the names, options, exit codes, rule ids and the JSON schema.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTemplateChecker
{
    const EXIT_OK = 0;
    const EXIT_FINDINGS = 1;
    const EXIT_USAGE = 2;
    const EXIT_UNAVAILABLE = 3;
    const EXIT_INTERNAL = 4;

    /** The machine output schema. */
    const SCHEMA = 'exp-template-check/1';

    const LEVELS = array( 'syntax', 'compile', 'strict' );
    const FORMATS = array( 'text', 'json', 'jsonl', 'junit', 'checkstyle', 'github', 'sarif' );
    const SEVERITIES = array( 'NOTE' => 1, 'WARN' => 2, 'ERROR' => 3 );

    /** Default checkers; site.ini [TemplateBridgeSettings] CheckerList[<engine>]=<class> adds or replaces them. */
    const CHECKERS = array( 'tpl' => 'expTplTemplateChecker', 'stpl' => 'expSmartyTemplateChecker', 'twig' => 'expTwigTemplateChecker' );

    /**
     * Every rule id: severity, explanation. Ids are permanent: a rule may be retired (it never fires again) but its id
     * is never reused. doc/eng-us/reference/template-check-rules.md explains each with examples.
     */
    const RULES = array(
        'TPL-SYNTAX-001' => array( 'ERROR', 'The template parser cannot read the .tpl: an unbalanced or unknown tag, a bad quote, a missing parameter.' ),
        'TPL-SYNTAX-002' => array( 'WARN', 'The template parser read the .tpl with a warning: something it guessed or left out.' ),
        'TPL-COMPILE-001' => array( 'ERROR', 'The template compiler cannot compile the .tpl, or the compiled PHP is not valid.' ),
        'TPL-STRICT-INCLUDE-MISSING' => array( 'ERROR', 'An {include uri=\'design:...\'} names a template that no design of the chain has.' ),
        'STPL-SYNTAX-001' => array( 'ERROR', 'Smarty 5 cannot compile the .stpl with the bridge (unknown tag, unclosed block, bad expression).' ),
        'STPL-COMPILE-001' => array( 'ERROR', 'The PHP Smarty compiled from the .stpl is not valid.' ),
        'STPL-SECURITY-001' => array( 'ERROR', 'The Smarty security policy (smarty.ini Security) refuses a construct of the .stpl.' ),
        'STPL-STRICT-INCLUDE-MISSING' => array( 'ERROR', 'An {expinclude uri=\'design:...\'} names a template that no design of the chain has.' ),
        'TPL-STRICT-UNKNOWN-OPERATOR' => array( 'WARN', 'An operator of the .tpl is not registered: the template system passes its input through and writes a warning (strict).' ),
        'TWIG-SYNTAX-001' => array( 'ERROR', 'Twig 3 cannot parse the .twig with the bridge (unknown tag, filter or function, unclosed block, bad expression).' ),
        'TWIG-COMPILE-001' => array( 'ERROR', 'Twig cannot compile the .twig, or the compiled PHP is not valid.' ),
        'TWIG-SECURITY-001' => array( 'ERROR', 'The Twig sandbox (twig.ini Security) would refuse a tag, filter or function of the .twig.' ),
        'TWIG-STRICT-UNKNOWN-OPERATOR' => array( 'WARN', 'exp_<name> names no template operator: it passes its input through, as an unknown operator of a .tpl does.' ),
        'TWIG-STRICT-INCLUDE-MISSING' => array( 'ERROR', 'An {% expinclude uri=\'design:...\' %} names a template that no design of the chain has.' ),
        'TWIG-DEPRECATION-001' => array( 'WARN', 'Twig reported a deprecation while compiling the .twig (--deprecations).' ),
        'ENCODING-UTF8-001' => array( 'ERROR', 'The file is not valid UTF-8 (--encoding).' ),
        'ENCODING-BOM-002' => array( 'WARN', 'The file starts with a byte order mark, which the engines print (--encoding).' ),
        'ENCODING-CRLF-003' => array( 'NOTE', 'The file mixes line endings (CRLF and LF) (--encoding).' ),
        'OVERRIDE-MISSING-FILE' => array( 'ERROR', 'An override rule of the siteaccess points to a file that no design of the chain has.' ),
        'OVERRIDE-ENGINE-MISMATCH' => array( 'WARN', 'An override rule replaces a template of one engine with a file whose ending is not a template engine\'s.' ),
        'PARITY-PAIR' => array( 'NOTE', 'The template exists in two engines (e.g. x.tpl and x.twig) side by side (--parity).' ),
        'ENGINE-UNAVAILABLE' => array( 'ERROR', 'The engine of the file is not available (its library is not installed): the file is skipped.' ),
        'CHECK-READ' => array( 'ERROR', 'The file cannot be read.' ),
        'CHECK-INTERNAL-ERROR' => array( 'ERROR', 'The checker itself failed on the file.' ),
    );

    /** @var array options */
    private $options;

    /** @var callable */
    private $out;

    /** @var callable */
    private $err;

    /** @var array path => array( engine, belongs[] ) */
    private $files = array();

    /** @var array[] findings */
    private $findings = array();

    /** @var array path => array( engine, ms, status ) */
    private $results = array();

    /** @var string[] engines found unavailable */
    private $unavailable = array();

    /** @var string private directory for compiled output */
    private $compiledDir;

    /** @var array ending => engine */
    private $suffixes;

    /** @var string|null text of stdin */
    private $stdin = null;

    /**
     * @param array $options as the command parses them
     * @param callable $out function( string $line ) normal output
     * @param callable $err function( string $line ) errors
     */
    public function __construct( array $options, $out, $err )
    {
        $this->options = $options;
        $this->out = $out;
        $this->err = $err;
    }

    private function option( $name, $default = null )
    {
        return isset( $this->options[$name] ) && $this->options[$name] !== false && $this->options[$name] !== null ? $this->options[$name] : $default;
    }

    /** A repeatable option as a list. */
    private function optionList( $name )
    {
        $value = $this->option( $name, array() );
        return is_array( $value ) ? $value : array( $value );
    }

    // ----------------------------------------------------------------------------------------
    // Running

    /**
     * @param string[] $targets the arguments: files, directories, '-'
     * @return int exit code
     */
    public function run( array $targets )
    {
        try
        {
            return $this->runChecked( $targets );
        }
        catch ( Throwable $e )
        {
            call_user_func( $this->err, 'FAIL internal error: ' . $e->getMessage() . ( $this->verbosity() > 1 ? "\n" . $e->getTraceAsString() : '' ) );
            return self::EXIT_INTERNAL;
        }
        finally
        {
            if ( $this->compiledDir !== null && !$this->option( 'keep-compiled' ) )
                self::removeTree( $this->compiledDir );
        }
    }

    private function verbosity()
    {
        if ( $this->option( 'quiet' ) )
            return -1;
        $verbose = $this->option( 'verbose', array() );
        return is_array( $verbose ) ? count( $verbose ) : ( $verbose ? 1 : 0 );
    }

    private function runChecked( array $targets )
    {
        if ( $this->option( 'rules' ) )
        {
            foreach ( self::RULES as $id => $rule )
                call_user_func( $this->out, sprintf( '%-30s %-5s %s', $id, $rule[0], $rule[1] ) );
            return self::EXIT_OK;
        }
        if ( ( $explain = $this->option( 'explain' ) ) !== null )
        {
            if ( !isset( self::RULES[$explain] ) )
            {
                call_user_func( $this->err, "FAIL unknown rule id: $explain (--rules lists them)" );
                return self::EXIT_USAGE;
            }
            call_user_func( $this->out, $explain . ' (' . self::RULES[$explain][0] . '): ' . self::RULES[$explain][1] );
            call_user_func( $this->out, 'Examples: doc/eng-us/reference/template-check-rules.md#' . strtolower( $explain ) );
            return self::EXIT_OK;
        }

        $level = $this->option( 'level', 'compile' );
        $format = $this->option( 'format', 'text' );
        $failOn = strtoupper( $this->option( 'fail-on', 'error' ) );
        if ( $failOn === 'WARNING' )
            $failOn = 'WARN';
        $usage = null;
        if ( !in_array( $level, self::LEVELS, true ) )
            $usage = "--level must be one of " . implode( ', ', self::LEVELS ) . ": $level";
        else if ( !in_array( $format, self::FORMATS, true ) )
            $usage = "--format must be one of " . implode( ', ', self::FORMATS ) . ": $format";
        else if ( !isset( self::SEVERITIES[$failOn] ) )
            $usage = "--fail-on must be error, warn or note: " . $this->option( 'fail-on' );
        else if ( ( $schema = $this->option( 'schema', '1' ) ) !== '1' )
            $usage = "--schema must be 1: $schema";
        if ( $usage === null )
            $usage = $this->prepareSuffixes();
        $engines = $this->selectedEngines( $usage );
        if ( $usage === null )
            $usage = $this->resolveTargets( $targets );
        if ( $usage !== null )
        {
            call_user_func( $this->err, 'FAIL ' . $usage );
            return self::EXIT_USAGE;
        }

        $this->compiledDir = eZSys::rootDir() . '/var/cache/template-check/' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 6 );
        eZDir::mkdir( $this->compiledDir, false, true );

        $context = array(
            'compiledDir' => $this->compiledDir,
            'security' => $this->option( 'security' ) || $level === 'strict',
            'includes' => $this->option( 'includes' ) || $level === 'strict',
            'deprecations' => (bool)$this->option( 'deprecations' ),
            'resolve' => array( $this, 'resolves' ),
        );
        $started = microtime( true );
        $paths = array();
        foreach ( $this->files as $path => $info )
        {
            if ( in_array( $info['engine'], $engines, true ) && !$this->ignored( $path ) )
                $paths[] = $path;
        }
        $jobs = max( 1, (int)$this->option( 'jobs', 1 ) );
        if ( $jobs > 1 && count( $paths ) > 20 && function_exists( 'pcntl_fork' ) )
            $this->checkInParallel( $paths, $level, $context, $jobs );
        else
        {
            foreach ( $paths as $path )
                $this->checkFile( $path, $level, $context );
        }
        if ( $this->option( 'overrides' ) || $this->option( 'siteaccess' ) || $this->option( 'all-siteaccesses' ) )
            $this->checkOverrides();
        if ( $this->option( 'parity' ) )
            $this->checkParity();
        $seconds = microtime( true ) - $started;

        // baseline
        if ( ( $generate = $this->option( 'generate-baseline' ) ) !== null )
        {
            $entries = array();
            foreach ( $this->findings as $finding )
                $entries[] = self::fingerprint( $finding );
            file_put_contents( $generate, json_encode( array( 'schema' => 'exp-template-check-baseline/1', 'findings' => array_values( array_unique( $entries ) ) ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
        }
        if ( ( $baseline = $this->option( 'baseline' ) ) !== null )
        {
            $data = is_file( $baseline ) ? json_decode( (string)file_get_contents( $baseline ), true ) : null;
            if ( !is_array( $data ) || !isset( $data['findings'] ) )
            {
                call_user_func( $this->err, "FAIL --baseline is not a baseline file: $baseline" );
                return self::EXIT_USAGE;
            }
            $known = array_fill_keys( $data['findings'], true );
            $this->findings = array_values( array_filter( $this->findings, function ( $f ) use ( $known ) { return !isset( $known[self::fingerprint( $f )] ); } ) );
            // a file whose findings the baseline holds is OK again (or as bad as what is left)
            foreach ( $this->results as $path => $result )
            {
                if ( $result['status'] !== 'SKIP' )
                    $this->results[$path]['status'] = 'OK';
            }
            foreach ( $this->findings as $finding )
            {
                $path = $finding['file'] === $this->stdinName() && isset( $this->results['-'] ) ? '-' : $finding['file'];
                if ( isset( $this->results[$path] ) && $this->results[$path]['status'] !== 'SKIP' &&
                     ( $this->results[$path]['status'] === 'OK' || self::SEVERITIES[$finding['severity']] > self::SEVERITIES[$this->results[$path]['status']] ) )
                    $this->results[$path]['status'] = $finding['severity'];
            }
        }
        // stdin is named by --stdin-name in the output
        if ( isset( $this->results['-'] ) )
        {
            $results = array();
            foreach ( $this->results as $path => $result )
                $results[$path === '-' ? $this->stdinName() : $path] = $result;
            $this->results = $results;
        }

        $exit = self::EXIT_OK;
        foreach ( $this->findings as $finding )
        {
            if ( self::SEVERITIES[$finding['severity']] >= self::SEVERITIES[$failOn] && $finding['rule'] !== 'ENGINE-UNAVAILABLE' )
                $exit = self::EXIT_FINDINGS;
        }
        if ( $exit === self::EXIT_OK && $this->unavailable && !$this->option( 'allow-missing-engine' ) )
            $exit = self::EXIT_UNAVAILABLE;

        $summary = $this->summary( $seconds, $exit );
        $formatter = new expTemplateCheckFormatter( $this->findings, $this->results, $summary );
        $output = $this->option( 'output' );
        if ( $output !== null )
        {
            file_put_contents( $output, $formatter->format( $format === 'text' ? 'json' : $format ) );
            $this->printText( $formatter, $summary );
        }
        else if ( $format === 'text' )
            $this->printText( $formatter, $summary );
        else
            call_user_func( $this->out, rtrim( $formatter->format( $format ), "\n" ) );
        return $exit;
    }

    // ----------------------------------------------------------------------------------------
    // Targets

    private function prepareSuffixes()
    {
        $this->suffixes = array( '.tpl' => 'tpl' ) + expTemplateEngines::suffixes();
        foreach ( $this->optionList( 'suffix-map' ) as $map )
        {
            if ( !preg_match( '/^(\.[^:]+):(tpl|stpl|twig)$/', (string)$map, $m ) )
                return "--suffix-map must be <ending>:<engine> (tpl, stpl or twig): $map";
            $this->suffixes[$m[1]] = $m[2];
        }
        uksort( $this->suffixes, function ( $a, $b ) { return strlen( $b ) - strlen( $a ) ?: strcmp( $a, $b ); } );
        return null;
    }

    private function selectedEngines( &$usage )
    {
        $value = (string)$this->option( 'engine', 'all' );
        if ( $value === 'all' )
            return array( 'tpl', 'stpl', 'twig' );
        $engines = array();
        foreach ( explode( ',', $value ) as $engine )
        {
            $engine = expTemplateDesignConverter::engine( trim( $engine ) );
            if ( $engine === null && $usage === null )
                $usage = "--engine must be all or a list of tpl, stpl, twig: $value";
            $engines[] = $engine;
        }
        return $engines;
    }

    /** The engine of a file by its ending, or null. */
    public function engineOf( $path )
    {
        foreach ( $this->suffixes as $ending => $engine )
        {
            if ( strlen( $path ) > strlen( $ending ) && substr( $path, -strlen( $ending ) ) === $ending )
                return $engine;
        }
        return null;
    }

    private function relative( $path )
    {
        $root = rtrim( eZSys::rootDir(), '/' ) . '/';
        $real = realpath( $path );
        if ( $real !== false && strncmp( $real, $root, strlen( $root ) ) === 0 )
            return substr( $real, strlen( $root ) );
        return $real !== false ? $real : $path;
    }

    private function addFile( $path, $belongs = null )
    {
        $engine = $this->engineOf( $path );
        if ( $engine === null )
            return;
        $relative = $this->relative( $path );
        if ( !isset( $this->files[$relative] ) )
            $this->files[$relative] = array( 'engine' => $engine, 'belongs' => array() );
        if ( $belongs !== null && !in_array( $belongs, $this->files[$relative]['belongs'], true ) )
            $this->files[$relative]['belongs'][] = $belongs;
    }

    private function addDirectory( $dir, $belongs = null )
    {
        if ( !is_dir( $dir ) )
            return;
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
        $files = array();
        foreach ( $iterator as $entry )
        {
            $name = $entry->getFilename();
            if ( $entry->isFile() && !preg_match( '/~$|^#.*#$|^\./', $name ) )
                $files[] = $entry->getPathname();
        }
        sort( $files );
        foreach ( $files as $file )
            $this->addFile( $file, $belongs );
    }

    /** design/<name> and extension/<ext>/design/<name> of the active extensions (or all with --all-extensions). */
    private function designDirectories( $design )
    {
        $dirs = array();
        if ( is_dir( 'design/' . $design ) )
            $dirs['design/' . $design] = 'design ' . $design;
        foreach ( $this->extensions() as $extension )
        {
            $dir = eZExtension::baseDirectory() . '/' . $extension . '/design/' . $design;
            if ( is_dir( $dir ) )
                $dirs[$dir] = 'design ' . $design . ' (extension ' . $extension . ')';
        }
        return $dirs;
    }

    private function extensions()
    {
        if ( $this->option( 'all-extensions' ) )
        {
            $list = array();
            foreach ( glob( eZExtension::baseDirectory() . '/*', GLOB_ONLYDIR ) as $dir )
                $list[] = basename( $dir );
            return $list;
        }
        return eZExtension::activeExtensions();
    }

    private function resolveTargets( array $targets )
    {
        foreach ( $targets as $target )
        {
            if ( $target === '-' )
            {
                $this->stdin = (string)stream_get_contents( STDIN );
                $name = $this->option( 'stdin-name', 'stdin' );
                $engine = expTemplateDesignConverter::engine( (string)$this->option( 'stdin-engine', 'tpl' ) );
                if ( $engine === null )
                    return '--stdin-engine must be tpl, stpl or twig';
                $this->files['-'] = array( 'engine' => $engine, 'belongs' => array( $name ) );
                continue;
            }
            if ( is_dir( $target ) )
                $this->addDirectory( $target );
            else if ( is_file( $target ) )
            {
                if ( $this->engineOf( $target ) === null )
                    return "not a template of a known engine: $target (--suffix-map adds an ending)";
                $this->addFile( $target );
            }
            else
                return "no such file or directory: $target";
        }
        foreach ( $this->optionList( 'design' ) as $design )
        {
            $dirs = $this->designDirectories( (string)$design );
            if ( !$dirs )
                return "unknown design: $design";
            foreach ( $dirs as $dir => $belongs )
                $this->addDirectory( $dir, $belongs );
        }
        foreach ( $this->optionList( 'design-dir' ) as $dir )
        {
            if ( !is_dir( $dir ) )
                return "--design-dir is not a directory: $dir";
            $this->addDirectory( $dir, 'design directory ' . $dir );
        }
        foreach ( $this->optionList( 'extension' ) as $extension )
        {
            $dir = eZExtension::baseDirectory() . '/' . $extension;
            if ( !is_dir( $dir ) )
                return "unknown extension: $extension";
            foreach ( glob( $dir . '/design/*', GLOB_ONLYDIR ) as $designDir )
                $this->addDirectory( $designDir, 'extension ' . $extension );
        }
        if ( $this->option( 'extensions' ) )
        {
            foreach ( $this->extensions() as $extension )
            {
                foreach ( glob( eZExtension::baseDirectory() . '/' . $extension . '/design/*', GLOB_ONLYDIR ) ?: array() as $designDir )
                    $this->addDirectory( $designDir, 'extension ' . $extension );
            }
        }
        $siteaccesses = $this->optionList( 'siteaccess' );
        if ( $this->option( 'all-siteaccesses' ) )
            $siteaccesses = array_merge( $siteaccesses, (array)eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) );
        $known = (array)eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        foreach ( array_unique( $siteaccesses ) as $siteaccess )
        {
            if ( !in_array( $siteaccess, $known, true ) )
                return "unknown siteaccess: $siteaccess";
            foreach ( $this->siteaccessDesigns( $siteaccess ) as $dir => $belongs )
                $this->addDirectory( $dir, 'siteaccess ' . $siteaccess . ': ' . $belongs );
        }
        if ( ( $list = $this->option( 'files-from' ) ) !== null )
        {
            $lines = $list === '-' ? stream_get_contents( STDIN ) : ( is_file( $list ) ? file_get_contents( $list ) : false );
            if ( $lines === false )
                return "--files-from cannot be read: $list";
            foreach ( preg_split( '/\R/', $lines ) as $line )
            {
                if ( trim( $line ) !== '' && is_file( trim( $line ) ) )
                    $this->addFile( trim( $line ) );
            }
        }
        if ( ( $ref = $this->option( 'changed-since' ) ) !== null )
        {
            if ( !preg_match( '/^[A-Za-z0-9_.\/~^@{}-]+$/', $ref ) )
                return "--changed-since is not a git reference: $ref";
            $changed = array();
            exec( 'git diff --name-only ' . escapeshellarg( $ref ) . ' -- 2>/dev/null', $changed, $status );
            if ( $status !== 0 )
                return "--changed-since: git cannot compare with $ref";
            exec( 'git ls-files --others --exclude-standard 2>/dev/null', $changed );
            $changedFiles = array();
            foreach ( $changed as $file )
            {
                if ( is_file( $file ) && $this->engineOf( $file ) !== null )
                    $changedFiles[$this->relative( $file )] = true;
            }
            if ( $this->files )
                $this->files = array_intersect_key( $this->files, $changedFiles );
            else
            {
                foreach ( array_keys( $changedFiles ) as $file )
                    $this->addFile( $file );
            }
            return null;
        }
        if ( !$targets && !$this->files && !$this->optionList( 'design' ) && !$this->optionList( 'extension' ) && !$this->option( 'extensions' ) &&
             !$siteaccesses && $this->option( 'files-from' ) === null && !$this->optionList( 'design-dir' ) )
        {
            // nothing named: the current siteaccess's design chain, as eztemplatecheck does
            $current = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : eZINI::instance()->variable( 'SiteSettings', 'DefaultAccess' );
            foreach ( $this->siteaccessDesigns( $current ) as $dir => $belongs )
                $this->addDirectory( $dir, 'siteaccess ' . $current . ': ' . $belongs );
        }
        return null;
    }

    /**
     * The design directories of a siteaccess in the order the template system resolves them: SiteDesign,
     * AdditionalSiteDesignList, StandardDesign, each in the kernel and in the extensions of design.ini DesignExtensions.
     */
    public function siteaccessDesigns( $siteaccess )
    {
        $site = eZSiteAccess::getIni( $siteaccess, 'site.ini' );
        $design = eZSiteAccess::getIni( $siteaccess, 'design.ini' );
        $designs = array_merge( array( $site->variable( 'DesignSettings', 'SiteDesign' ) ),
                                (array)$site->variable( 'DesignSettings', 'AdditionalSiteDesignList' ),
                                array( $site->variable( 'DesignSettings', 'StandardDesign' ) ) );
        $extensions = $design->hasVariable( 'ExtensionSettings', 'DesignExtensions' ) ? (array)$design->variable( 'ExtensionSettings', 'DesignExtensions' ) : array();
        $dirs = array();
        foreach ( array_unique( array_filter( $designs ) ) as $name )
        {
            foreach ( $extensions as $extension )
            {
                $dir = eZExtension::baseDirectory() . '/' . $extension . '/design/' . $name;
                if ( is_dir( $dir ) )
                    $dirs[$dir] = 'design ' . $name . ' (extension ' . $extension . ')';
            }
            if ( is_dir( 'design/' . $name ) )
                $dirs['design/' . $name] = 'design ' . $name;
        }
        return $dirs;
    }

    /**
     * Whether a design: URI resolves in the current siteaccess's design chain.
     */
    public function resolves( $uri )
    {
        return expTemplateBridge::resolveURI( eZTemplate::factory(), $uri ) !== null;
    }

    private function ignored( $path )
    {
        $globs = $this->optionList( 'ignore' );
        $ini = eZINI::instance();
        if ( $ini->hasVariable( 'TemplateCheckSettings', 'IgnoreList' ) )
            $globs = array_merge( $globs, array_filter( (array)$ini->variable( 'TemplateCheckSettings', 'IgnoreList' ), 'strlen' ) );
        foreach ( $globs as $glob )
        {
            if ( fnmatch( $glob, $path ) || fnmatch( $glob, basename( $path ) ) )
                return true;
        }
        return false;
    }

    // ----------------------------------------------------------------------------------------
    // Checking

    /** @var expTemplateEngineChecker[] */
    private $checkers = array();

    private function checker( $engine )
    {
        if ( !isset( $this->checkers[$engine] ) )
        {
            $class = isset( self::CHECKERS[$engine] ) ? self::CHECKERS[$engine] : null;
            $ini = eZINI::instance();
            if ( $ini->hasVariable( 'TemplateBridgeSettings', 'CheckerList' ) )
            {
                $list = (array)$ini->variable( 'TemplateBridgeSettings', 'CheckerList' );
                if ( isset( $list[$engine] ) && $list[$engine] !== '' )
                    $class = $list[$engine];
            }
            $this->checkers[$engine] = $class !== null && class_exists( $class ) ? new $class() : null;
        }
        return $this->checkers[$engine];
    }

    private function checkFile( $path, $level, array $context )
    {
        $engine = $this->files[$path]['engine'];
        $checker = $this->checker( $engine );
        $started = microtime( true );
        if ( $checker === null || !$checker->available() )
        {
            if ( !in_array( $engine, $this->unavailable, true ) )
            {
                $this->unavailable[] = $engine;
                $this->addFinding( $path, $engine, expTplTemplateChecker::finding( 'ENGINE-UNAVAILABLE', 'ERROR', 0, 0, "UNAVAILABLE $engine: the engine's library is not installed; its files are skipped" ) );
            }
            $this->results[$path] = array( 'engine' => $engine, 'ms' => 0, 'status' => 'SKIP', 'belongs' => $this->files[$path]['belongs'] );
            return;
        }
        $text = $path === '-' ? $this->stdin : @file_get_contents( $path );
        if ( $text === false )
        {
            $this->addFinding( $path, $engine, expTplTemplateChecker::finding( 'CHECK-READ', 'ERROR', 0, 0, 'the file cannot be read' ) );
            $this->results[$path] = array( 'engine' => $engine, 'ms' => 0, 'status' => 'ERROR', 'belongs' => $this->files[$path]['belongs'] );
            return;
        }
        $findings = array();
        if ( $this->option( 'encoding' ) )
            $findings = array_merge( $findings, self::encodingFindings( $text ) );
        try
        {
            $findings = array_merge( $findings, $checker->check( $path, $text, $level, $context ) );
        }
        catch ( Throwable $e )
        {
            expTemplateBridge::rethrowExit( $e );
            $findings[] = expTplTemplateChecker::finding( 'CHECK-INTERNAL-ERROR', 'ERROR', 0, 0, get_class( $e ) . ': ' . $e->getMessage() );
        }
        $findings = self::withoutIgnoredLines( $findings, $text );
        $worst = 'OK';
        foreach ( $findings as $finding )
        {
            $this->addFinding( $path, $engine, $finding, $text );
            if ( $worst === 'OK' || self::SEVERITIES[$finding['severity']] > self::SEVERITIES[$worst] )
                $worst = $finding['severity'];
        }
        $this->results[$path] = array( 'engine' => $engine, 'ms' => (int)round( ( microtime( true ) - $started ) * 1000 ), 'status' => $worst,
                                       'belongs' => $this->files[$path]['belongs'] );
    }

    /**
     * --jobs: the files in parallel worker processes; each writes its results to a file of the private directory.
     */
    private function checkInParallel( array $paths, $level, array $context, $jobs )
    {
        $chunks = array_chunk( $paths, (int)ceil( count( $paths ) / $jobs ) );
        $children = array();
        foreach ( $chunks as $index => $chunk )
        {
            $pid = pcntl_fork();
            if ( $pid === 0 )
            {
                if ( function_exists( 'proc_nice' ) )
                    @proc_nice( 10 );
                foreach ( $chunk as $path )
                    $this->checkFile( $path, $level, $context );
                file_put_contents( $this->compiledDir . "/job-$index.json", serialize( array( $this->findings, $this->results, $this->unavailable ) ) );
                // the worker ends here: nothing of the parent's shutdown may run twice
                if ( function_exists( 'posix_kill' ) )
                    posix_kill( getmypid(), 9 );
                exit( 0 );
            }
            if ( $pid < 0 )
            {
                foreach ( $chunk as $path )
                    $this->checkFile( $path, $level, $context );
                continue;
            }
            $children[$index] = $pid;
        }
        foreach ( $children as $index => $pid )
        {
            pcntl_waitpid( $pid, $status );
            $file = $this->compiledDir . "/job-$index.json";
            $data = is_file( $file ) ? unserialize( (string)file_get_contents( $file ), array( 'allowed_classes' => false ) ) : false;
            if ( !is_array( $data ) )
            {
                foreach ( $chunks[$index] as $path )
                    $this->checkFile( $path, $level, $context );
                continue;
            }
            $this->findings = array_merge( $this->findings, $data[0] );
            $this->results = array_merge( $this->results, $data[1] );
            $this->unavailable = array_values( array_unique( array_merge( $this->unavailable, $data[2] ) ) );
        }
        ksort( $this->results );
    }

    /**
     * "did you mean ..." for an unknown operator name: the closest registered operator (at most two edits away).
     *
     * @return string|null
     */
    public static function operatorSuggestion( $name )
    {
        $best = null;
        $bestDistance = 3;
        foreach ( array_keys( eZTemplate::factory()->Operators ) as $operator )
        {
            $distance = self::editDistance( (string)$name, (string)$operator );
            if ( $distance > 0 && $distance < $bestDistance )
            {
                $best = $operator;
                $bestDistance = $distance;
            }
        }
        return $best !== null ? "did you mean \"$best\"?" : null;
    }

    /** The edit distance with two swapped neighbors as one edit (wahs -> wash is 1, not 2). */
    private static function editDistance( $a, $b )
    {
        $la = strlen( $a );
        $lb = strlen( $b );
        if ( abs( $la - $lb ) > 2 )
            return 3;
        $d = array();
        for ( $i = 0; $i <= $la; ++$i )
            $d[$i][0] = $i;
        for ( $j = 0; $j <= $lb; ++$j )
            $d[0][$j] = $j;
        for ( $i = 1; $i <= $la; ++$i )
        {
            for ( $j = 1; $j <= $lb; ++$j )
            {
                $cost = $a[$i - 1] === $b[$j - 1] ? 0 : 1;
                $d[$i][$j] = min( $d[$i - 1][$j] + 1, $d[$i][$j - 1] + 1, $d[$i - 1][$j - 1] + $cost );
                if ( $i > 1 && $j > 1 && $a[$i - 1] === $b[$j - 2] && $a[$i - 2] === $b[$j - 1] )
                    $d[$i][$j] = min( $d[$i][$j], $d[$i - 2][$j - 2] + 1 );
            }
        }
        return $d[$la][$lb];
    }

    /** The name stdin ('-') has in the output: --stdin-name, or stdin. */
    private function stdinName()
    {
        return (string)$this->option( 'stdin-name', 'stdin' );
    }

    private function addFinding( $path, $engine, array $finding, $text = null )
    {
        $finding['engine'] = $engine;
        $finding['file'] = $path === '-' ? $this->stdinName() : $path;
        $finding['source'] = null;
        if ( $text !== null && $finding['line'] > 0 )
        {
            $lines = explode( "\n", $text );
            if ( isset( $lines[$finding['line'] - 1] ) )
                $finding['source'] = rtrim( $lines[$finding['line'] - 1], "\r" );
        }
        $finding['belongs'] = isset( $this->files[$path] ) ? $this->files[$path]['belongs'] : array();
        $this->findings[] = $finding;
    }

    /**
     * Drops findings a comment on the line before them ignores:
     * {* exp:template:check ignore-next-line [RULE-ID] *} or {# exp:template:check ignore-next-line [RULE-ID] #}.
     */
    private static function withoutIgnoredLines( array $findings, $text )
    {
        if ( strpos( $text, 'exp:template:check ignore-next-line' ) === false )
            return $findings;
        $ignore = array();
        foreach ( explode( "\n", $text ) as $index => $line )
        {
            if ( preg_match( '/\{[*#]\s*exp:template:check ignore-next-line(?:\s+([A-Z0-9-]+))?\s*[*#]\}/', $line, $m ) )
                $ignore[$index + 2] = isset( $m[1] ) ? $m[1] : '*';
        }
        return array_values( array_filter( $findings, function ( $f ) use ( $ignore ) {
            return !( isset( $ignore[$f['line']] ) && ( $ignore[$f['line']] === '*' || $ignore[$f['line']] === $f['rule'] ) );
        } ) );
    }

    private static function encodingFindings( $text )
    {
        $findings = array();
        if ( !mb_check_encoding( $text, 'UTF-8' ) )
            $findings[] = expTplTemplateChecker::finding( 'ENCODING-UTF8-001', 'ERROR', 0, 0, 'the file is not valid UTF-8' );
        if ( strncmp( $text, "\xEF\xBB\xBF", 3 ) === 0 )
            $findings[] = expTplTemplateChecker::finding( 'ENCODING-BOM-002', 'WARN', 1, 1, 'the file starts with a byte order mark, which is printed' );
        if ( strpos( $text, "\r\n" ) !== false && preg_match( '/(?<!\r)\n/', $text ) )
            $findings[] = expTplTemplateChecker::finding( 'ENCODING-CRLF-003', 'NOTE', 0, 0, 'the file mixes CRLF and LF line endings' );
        return $findings;
    }

    /**
     * --overrides: every override rule of the siteaccesses checked points to an existing file of a known engine.
     */
    private function checkOverrides()
    {
        $siteaccesses = $this->optionList( 'siteaccess' );
        if ( $this->option( 'all-siteaccesses' ) )
            $siteaccesses = array_merge( $siteaccesses, (array)eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) );
        if ( !$siteaccesses )
            $siteaccesses = array( isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : eZINI::instance()->variable( 'SiteSettings', 'DefaultAccess' ) );
        foreach ( array_unique( $siteaccesses ) as $siteaccess )
        {
            $override = eZSiteAccess::getIni( $siteaccess, 'override.ini' );
            $dirs = array_keys( $this->siteaccessDesigns( $siteaccess ) );
            $file = 'settings/siteaccess/' . $siteaccess . '/override.ini.append.php';
            foreach ( $override->groups() as $group => $settings )
            {
                if ( !isset( $settings['MatchFile'] ) || !isset( $settings['Source'] ) )
                    continue;
                $subdir = isset( $settings['Subdir'] ) ? $settings['Subdir'] : 'templates';
                $found = false;
                foreach ( $dirs as $dir )
                {
                    if ( is_file( $dir . '/override/' . $subdir . '/' . $settings['MatchFile'] ) )
                    {
                        $found = true;
                        break;
                    }
                }
                $finding = null;
                if ( !$found )
                    $finding = expTplTemplateChecker::finding( 'OVERRIDE-MISSING-FILE', 'ERROR', 0, 0, "[$group] MatchFile={$settings['MatchFile']}: no design of the siteaccess $siteaccess has it" );
                else if ( $this->engineOf( $settings['MatchFile'] ) === null )
                    $finding = expTplTemplateChecker::finding( 'OVERRIDE-ENGINE-MISMATCH', 'WARN', 0, 0, "[$group] MatchFile={$settings['MatchFile']}: not a template of a known engine" );
                if ( $finding !== null )
                {
                    $finding['engine'] = 'tpl';
                    $finding['file'] = $file;
                    $finding['source'] = null;
                    $finding['belongs'] = array( 'siteaccess ' . $siteaccess );
                    $this->findings[] = $finding;
                }
            }
        }
    }

    /**
     * --parity: templates that exist in two engines side by side (x.tpl and x.twig ...).
     */
    private function checkParity()
    {
        $byBase = array();
        foreach ( $this->files as $path => $info )
        {
            $base = $path;
            foreach ( $this->suffixes as $ending => $engine )
            {
                if ( substr( $path, -strlen( $ending ) ) === $ending )
                {
                    $base = substr( $path, 0, -strlen( $ending ) );
                    break;
                }
            }
            $byBase[$base][] = $path;
        }
        foreach ( $byBase as $base => $paths )
        {
            if ( count( $paths ) > 1 )
            {
                $finding = expTplTemplateChecker::finding( 'PARITY-PAIR', 'NOTE', 0, 0, 'the same template in ' . count( $paths ) . ' engines: ' . implode( ', ', $paths ) .
                    ( $this->option( 'parity' ) === 'render' ? ' (--parity=render is experimental and compares nothing yet)' : '' ) );
                $this->addFinding( $paths[0], $this->files[$paths[0]]['engine'], $finding );
            }
        }
    }

    // ----------------------------------------------------------------------------------------
    // Output

    private function summary( $seconds, $exit )
    {
        $counts = array( 'files' => count( $this->results ), 'OK' => 0, 'ERROR' => 0, 'WARN' => 0, 'NOTE' => 0, 'SKIP' => 0 );
        $failed = 0;
        $byEngine = array();
        foreach ( $this->results as $result )
        {
            $counts[$result['status']] = ( isset( $counts[$result['status']] ) ? $counts[$result['status']] : 0 ) + 1;
            $byEngine[$result['engine']] = ( isset( $byEngine[$result['engine']] ) ? $byEngine[$result['engine']] : 0 ) + 1;
            if ( $result['status'] !== 'OK' && $result['status'] !== 'SKIP' )
                ++$failed;
        }
        $findingCounts = array( 'ERROR' => 0, 'WARN' => 0, 'NOTE' => 0 );
        foreach ( $this->findings as $finding )
            ++$findingCounts[$finding['severity']];
        return array( 'files' => $counts['files'], 'ok' => $counts['OK'], 'failed' => $failed, 'skipped' => $counts['SKIP'], 'errors' => $findingCounts['ERROR'],
                      'warnings' => $findingCounts['WARN'], 'notes' => $findingCounts['NOTE'], 'engines' => $byEngine,
                      'unavailable' => $this->unavailable, 'seconds' => round( $seconds, 3 ), 'level' => $this->option( 'level', 'compile' ),
                      'exit' => $exit, 'result' => $exit === self::EXIT_OK ? 'PASS' : 'FAIL' );
    }

    private function printText( expTemplateCheckFormatter $formatter, array $summary )
    {
        foreach ( explode( "\n", rtrim( $formatter->text( $this->verbosity(), (string)$this->option( 'summary-by', 'engine' ) ), "\n" ) ) as $line )
            call_user_func( $this->out, $line );
    }

    private static function fingerprint( array $finding )
    {
        return $finding['rule'] . '|' . $finding['file'] . '|' . $finding['message'];
    }

    /**
     * Lints PHP code in memory (the compiled template), without running it.
     *
     * @return string|null the parse error, null when the code is valid
     */
    public static function lintPhp( $code )
    {
        try
        {
            PhpToken::tokenize( (string)$code, TOKEN_PARSE );
        }
        catch ( ParseError $e )
        {
            return $e->getMessage() . ' (compiled line ' . $e->getLine() . ')';
        }
        return null;
    }

    private static function removeTree( $path )
    {
        if ( !$path || !file_exists( $path ) )
            return;
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            foreach ( scandir( $path ) as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    self::removeTree( $path . '/' . $entry );
            }
            @rmdir( $path );
        }
        else
            @unlink( $path );
    }
}
