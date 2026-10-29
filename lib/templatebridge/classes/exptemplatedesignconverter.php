<?php
/**
 * Converts the templates of a design from one template engine to another, file by file:
 *
 *   tpl    Exponential templates (.tpl)
 *   stpl   Smarty templates (.stpl)
 *
 * The work of the console commands exp:template:convert, exp:template:convert:tpl-to-stpl and
 * exp:template:convert:stpl-to-tpl (kernel/private/classes/commands/exptemplateconvert*.php): the commands parse the
 * options and call run(), so everything they do can be called, and tested, without a command line.
 *
 * The converter of one template text comes from a registry, site.ini [TemplateBridgeSettings]
 * ConverterList[<from>-<to>]=<class> (tpl-stpl expSmartyConverter, stpl-tpl expSmartyReverseConverter), so an
 * extension can add a direction without touching the commands. A direction without a converter is reported as not
 * available (exit code 3).
 *
 * Every file it writes ends with a marker comment, in the comment syntax of the target engine, that names the
 * direction, the checksum of what was written and the source:
 *
 *   {* exp:template:convert tpl->stpl md5:<checksum> from templates/a.tpl -- generated; ... *}
 *
 * A target that exists is only replaced when it still carries a marker whose checksum matches its content, that is
 * when nobody changed it after it was generated; a target without a marker (written by hand) or with a content that
 * no longer matches its marker (changed by hand) is skipped and reported, unless force is given. The marker is a
 * comment at the very end of the file, so it changes nothing in the output, and it is removed from a source before
 * the source is converted.
 *
 * Guide: doc/eng-us/guides/template-conversion.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTemplateDesignConverter
{
    /** Exit codes of run(). */
    const EXIT_OK = 0;
    const EXIT_ERRORS = 1;
    const EXIT_USAGE = 2;
    const EXIT_UNAVAILABLE = 3;

    /** The marker at the end of a generated file: direction, checksum of the content before it, source. */
    const MARKER_PATTERN = '/(\{\*|\{#) exp:template:convert ([a-z0-9]+)->([a-z0-9]+) md5:([0-9a-f]{32}) from (\S+)[^*#}]*(\*\}|#\})\z/';

    /** @var array engine => name, file ending and comment syntax */
    private static $engines = array(
        'tpl' => array( 'name' => 'Exponential templates', 'suffix' => '.tpl', 'comment' => array( '{*', '*}' ) ),
        'stpl' => array( 'name' => 'Smarty templates', 'suffix' => '.stpl', 'comment' => array( '{*', '*}' ) ),
        'twig' => array( 'name' => 'Twig templates', 'suffix' => '.twig', 'comment' => array( '{#', '#}' ) ),
    );

    /** @var array the other names an engine is accepted by */
    private static $engineAliases = array( 'exp' => 'tpl', 'exponential' => 'tpl', 'smarty' => 'stpl' );

    /** @var string the source engine */
    private $from;

    /** @var string the target engine */
    private $to;

    /** @var eZTemplate */
    private $tpl;

    /** @var object|null the converter of one template text */
    private $converter;

    /** @var array counts of the last run() */
    private $counts = array();

    /** @var array relative source => relative target, of the templates converted (or to convert, with dry-run), and of the
     * targets skipped because they were changed by hand: every template of the target design, for the override settings */
    private $converted = array();

    /**
     * @param string $from the source engine (tpl, stpl, or one of their other names)
     * @param string $to the target engine
     * @param eZTemplate|null $tpl the template system the operators and functions are read from
     */
    public function __construct( $from, $to, ?eZTemplate $tpl = null )
    {
        $fromEngine = self::engine( $from );
        $toEngine = self::engine( $to );
        if ( $fromEngine === null )
            throw new InvalidArgumentException( "Unknown template engine '$from'" );
        if ( $toEngine === null )
            throw new InvalidArgumentException( "Unknown template engine '$to'" );
        if ( $fromEngine === $toEngine )
            throw new InvalidArgumentException( "The source and the target engine are the same: '$fromEngine'" );
        $this->from = $fromEngine;
        $this->to = $toEngine;
        $this->tpl = $tpl;
        $this->resetCounts();
    }

    /**
     * The engine a --from-engine or --to-engine value names.
     *
     * @param string $value tpl, stpl, twig, or exp, exponential, smarty
     * @return string|null tpl, stpl or twig; null for an unknown value
     */
    public static function engine( $value )
    {
        $value = strtolower( trim( (string)$value ) );
        if ( isset( self::$engineAliases[$value] ) )
            $value = self::$engineAliases[$value];
        return isset( self::$engines[$value] ) ? $value : null;
    }

    /** @return string[] the engines, tpl, stpl, twig */
    public static function engines()
    {
        return array_keys( self::$engines );
    }

    /**
     * The engines that have a converter to or from another engine in the registry.
     *
     * @return string[]
     */
    public static function availableEngines()
    {
        $available = array();
        foreach ( self::engines() as $from )
            foreach ( self::engines() as $to )
                if ( $from !== $to && self::hasConversion( $from, $to ) )
                    $available[$from] = $available[$to] = true;
        return array_values( array_intersect( self::engines(), array_keys( $available ) ) );
    }

    /**
     * @param string $engine
     * @return string the name of an engine in the output (Exponential templates, Smarty templates ...)
     */
    public static function engineName( $engine )
    {
        return self::$engines[$engine]['name'];
    }

    /**
     * The converter class of a direction, from site.ini [TemplateBridgeSettings] ConverterList[<from>-<to>].
     *
     * @param string $from
     * @param string $to
     * @return string|null the class, null when the direction has no converter (or its class does not exist)
     */
    public static function converterClass( $from, $to )
    {
        $ini = eZINI::instance( 'site.ini' );
        if ( !$ini->hasVariable( 'TemplateBridgeSettings', 'ConverterList' ) )
            return null;
        $list = $ini->variable( 'TemplateBridgeSettings', 'ConverterList' );
        $key = $from . '-' . $to;
        if ( !is_array( $list ) || !isset( $list[$key] ) || !is_string( $list[$key] ) || $list[$key] === '' )
            return null;
        return class_exists( $list[$key] ) ? $list[$key] : null;
    }

    /** @return bool whether this direction has a converter */
    public function available()
    {
        return self::hasConversion( $this->from, $this->to );
    }

    /**
     * Whether a direction can be converted: by its own converter (ConverterList[<from>-<to>]), or through Exponential
     * templates in one run (<from>-tpl, then tpl-<to>: stpl -> twig, twig -> stpl).
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public static function hasConversion( $from, $to )
    {
        if ( $from === $to )
            return false;
        if ( self::converterClass( $from, $to ) !== null )
            return true;
        return $from !== 'tpl' && $to !== 'tpl' && self::converterClass( $from, 'tpl' ) !== null && self::converterClass( 'tpl', $to ) !== null;
    }

    /** @return string the source engine */
    public function fromEngine()
    {
        return $this->from;
    }

    /** @return string the target engine */
    public function toEngine()
    {
        return $this->to;
    }

    /** @return string the direction as the marker and the output name it: tpl->stpl */
    public function direction()
    {
        return $this->from . '->' . $this->to;
    }

    /** @return string the named console command of this direction */
    public function commandName()
    {
        return 'exp:template:convert:' . $this->from . '-to-' . $this->to;
    }

    /** @return string '.tpl', '.stpl' ... */
    public function sourceSuffix()
    {
        return self::$engines[$this->from]['suffix'];
    }

    /** @return string '.stpl', '.tpl', '.twig' or '.html.twig' (--twig-suffix) ... */
    public function targetSuffix()
    {
        return $this->to === 'twig' ? $this->twigSuffix : self::$engines[$this->to]['suffix'];
    }

    /** The ending a Twig template is written with: .twig (the default) or .html.twig (--twig-suffix). */
    private $twigSuffix = '.twig';

    /** The endings --twig-suffix accepts. */
    const TWIG_SUFFIXES = array( '.twig', '.html.twig' );

    /**
     * The ending of one source file: x.html.twig is the Twig template x (never x.html), any other file has the
     * source engine's ending.
     *
     * @param string $path
     * @return string
     */
    public function sourceSuffixOf( $path )
    {
        if ( $this->from === 'twig' && substr( $path, -10 ) === '.html.twig' && strlen( $path ) > 10 )
            return '.html.twig';
        return $this->sourceSuffix();
    }

    /**
     * @return array templates, written, unchanged, skipped, errors, notes, withNotes of the last run()
     */
    public function counts()
    {
        return $this->counts;
    }

    /** @return array relative source => relative target of the last run() */
    public function converted()
    {
        return $this->converted;
    }

    /**
     * The work of a console command: the engines from the command (a named command) or from --from-engine and
     * --to-engine (the general command), then run().
     *
     * @param array $options as eZScript::startup() gives them
     * @param eZCLI $cli
     * @param string|null $from the source engine of a named command; null: --from-engine
     * @param string|null $to the target engine of a named command; null: --to-engine
     * @return int the exit code
     */
    public static function runCommand( array $options, $cli, $from = null, $to = null )
    {
        $out = function ( $line ) use ( $cli ) { $cli->output( $line ); };
        $err = function ( $line ) use ( $cli ) { $cli->error( $line ); };
        return self::runWithEngines( $options, $out, $err, $from, $to );
    }

    /**
     * runCommand() without a command line: the engines are checked, then run().
     *
     * @return int the exit code
     */
    public static function runWithEngines( array $options, $out, $err, $from = null, $to = null )
    {
        $values = array( 'from-engine' => $from, 'to-engine' => $to );
        $engines = array();
        foreach ( $values as $option => $value )
        {
            if ( $value === null )
                $value = isset( $options[$option] ) && is_string( $options[$option] ) ? $options[$option] : '';
            if ( $value === '' )
            {
                $err( "FAIL --$option is needed: " . implode( ', ', self::availableEngines() ) );
                return self::EXIT_USAGE;
            }
            $engine = self::engine( $value );
            if ( $engine === null )
            {
                $err( "FAIL --$option=$value is not a template engine: " . implode( ', ', self::availableEngines() ) );
                return self::EXIT_USAGE;
            }
            $engines[] = $engine;
        }
        if ( $engines[0] === $engines[1] )
        {
            $err( "FAIL --from-engine and --to-engine are the same ({$engines[0]}): nothing to convert" );
            return self::EXIT_USAGE;
        }
        $converter = new self( $engines[0], $engines[1] );
        if ( !$converter->available() )
        {
            $err( 'FAIL ' . $converter->direction() . ': converting ' . self::engineName( $engines[0] ) . ' to ' .
                  self::engineName( $engines[1] ) . ' is not available yet' );
            return self::EXIT_UNAVAILABLE;
        }
        return $converter->run( $options, $out, $err );
    }

    /**
     * The converter of one template text.
     *
     * @return object with convert( $text, $name ) and warnings()
     */
    public function converter()
    {
        if ( $this->converter === null )
        {
            $class = self::converterClass( $this->from, $this->to );
            if ( $class !== null )
                $this->converter = new $class( $this->template() );
            else if ( self::hasConversion( $this->from, $this->to ) )
            {
                // through Exponential templates, in memory
                $first = self::converterClass( $this->from, 'tpl' );
                $second = self::converterClass( 'tpl', $this->to );
                $this->converter = new expTemplateChainConverter( new $first( $this->template() ), new $second( $this->template() ) );
            }
            else
                throw new RuntimeException( 'No converter for ' . $this->direction() . ' (site.ini [TemplateBridgeSettings] ConverterList)' );
        }
        return $this->converter;
    }

    /**
     * Converts one template text (a marker it carries is removed first).
     *
     * @param string $text
     * @param string $name name used in the notes
     * @return array( string converted text without a marker, string[] notes )
     */
    public function convertText( $text, $name = 'template' )
    {
        list( $content ) = self::splitMarker( $text );
        $converter = $this->converter();
        $tpl = $this->template();
        $tpl->resetErrorLog();
        $converted = $converter->convert( $content, $name );
        $notes = $converter->warnings();
        // the template system's parser reports what it could not parse in the source to the template's error log
        foreach ( $tpl->errorLog() as $error )
            $notes[] = 'the source has an error: ' . self::oneLine( $error['text'] );
        $tpl->resetErrorLog();
        return array( $converted, array_values( array_unique( $notes ) ) );
    }

    /**
     * Checks that a converted text is valid in its engine: a .stpl compiles with Smarty (when Smarty 5 is
     * installed), a .twig compiles with Twig 3 (when installed), a .tpl parses without an error in the template system's parser.
     *
     * @param string $text the converted text
     * @param string $name name used in the message
     * @return string|null what is wrong, null when it is valid (or cannot be checked)
     */
    public function verify( $text, $name = 'template' )
    {
        if ( $this->to === 'stpl' )
        {
            if ( !class_exists( 'expSmartyRenderer' ) || !expSmartyRenderer::loadLibrary() || !( $smarty = expSmartyRenderer::smarty() ) )
                return null;
            try
            {
                // eval: compiles in memory, nothing is written to the compile directory
                $smarty->createTemplate( 'eval:' . $text )->compileTemplateSource();
            }
            catch ( Throwable $e )
            {
                return 'Smarty cannot compile the result: ' . self::oneLine( $e->getMessage() );
            }
            return null;
        }
        if ( $this->to === 'twig' )
        {
            if ( !class_exists( 'expTwigRenderer' ) || !expTwigRenderer::loadLibrary() )
                return null;
            // compiles in memory, nothing is written to the cache directory
            $error = expTwigRenderer::compileCheck( $text, $name );
            return $error === null ? null : 'Twig cannot compile the result: ' . self::oneLine( $error );
        }
        if ( $this->to === 'tpl' )
        {
            $tpl = $this->template();
            $tpl->resetErrorLog();
            $root = array( eZTemplate::NODE_ROOT, false );
            $resourceData = array( 'resource' => 'file', 'template-name' => $name, 'template-filename' => $name, 'text' => $text );
            eZTemplateMultiPassParser::instance()->parse( $tpl, $text, $root, '', $resourceData );
            $errors = $tpl->errorLog();
            $tpl->resetErrorLog();
            if ( $errors )
                return 'the result does not parse: ' . self::oneLine( $errors[0]['text'] );
        }
        return null;
    }

    /** @return eZTemplate */
    private function template()
    {
        if ( $this->tpl === null )
            $this->tpl = eZTemplate::factory();
        return $this->tpl;
    }

    private static function oneLine( $text )
    {
        return trim( preg_replace( '/\s+/', ' ', (string)$text ) );
    }

    /**
     * The text with the marker of this direction appended, in the target engine's comment syntax.
     *
     * @param string $text the converted text
     * @param string $relativeSource the source, as the marker names it
     * @return string
     */
    public function withMarker( $text, $relativeSource )
    {
        $relativeSource = preg_replace( '/\s/', '_', (string)$relativeSource );
        list( $open, $close ) = self::$engines[$this->to]['comment'];
        return $text . $open . ' exp:template:convert ' . $this->direction() . ' md5:' . md5( $text ) . ' from ' . $relativeSource .
               ' -- generated; a file changed by hand is not replaced without --force ' . $close;
    }

    /**
     * Splits a file's text into its content and its marker (of any direction, in any engine's comment syntax).
     *
     * @param string $text
     * @return array( string content, array|null marker: from, to, md5, source, valid (the content matches) )
     */
    public static function splitMarker( $text )
    {
        $text = (string)$text;
        if ( !preg_match( self::MARKER_PATTERN, $text, $m, PREG_OFFSET_CAPTURE ) )
            return array( $text, null );
        $content = substr( $text, 0, $m[0][1] );
        return array( $content, array( 'from' => $m[2][0], 'to' => $m[3][0], 'md5' => $m[4][0], 'source' => $m[5][0],
                                       'valid' => md5( $content ) === $m[4][0] ) );
    }

    /**
     * What a target file is: 'new' (does not exist), 'generated' (its marker matches its content), 'edited' (it
     * has a marker that no longer matches) or 'foreign' (no marker: written by hand or by something else).
     *
     * @param string $file
     * @return string
     */
    public static function targetState( $file )
    {
        if ( !file_exists( $file ) )
            return 'new';
        list( , $marker ) = self::splitMarker( (string)file_get_contents( $file ) );
        if ( $marker === null )
            return 'foreign';
        return $marker['valid'] ? 'generated' : 'edited';
    }

    /**
     * Converts the templates the options name.
     *
     * @param array $options from, to, file, override-ini, dry-run, force (as eZScript::getOptions() gives them)
     * @param callable $out function( string $line ), the normal output
     * @param callable $err function( string $line ), the errors
     * @return int self::EXIT_OK, self::EXIT_ERRORS (a template could not be read, converted or written),
     *             self::EXIT_USAGE (the options are wrong) or self::EXIT_UNAVAILABLE (no converter for the direction)
     */
    public function run( array $options, $out, $err )
    {
        $this->resetCounts();
        $this->converted = array();
        if ( !$this->available() )
        {
            $err( 'FAIL ' . $this->direction() . ': converting ' . self::engineName( $this->from ) . ' to ' .
                  self::engineName( $this->to ) . ' is not available yet' );
            return self::EXIT_UNAVAILABLE;
        }
        $from = isset( $options['from'] ) && $options['from'] !== false && $options['from'] !== null ? rtrim( (string)$options['from'], '/' ) : '';
        $to = isset( $options['to'] ) && $options['to'] !== false && $options['to'] !== null ? rtrim( (string)$options['to'], '/' ) : '';
        $file = isset( $options['file'] ) && $options['file'] !== false && $options['file'] !== null ? (string)$options['file'] : '';
        $overrideIni = isset( $options['override-ini'] ) && $options['override-ini'] ? (string)$options['override-ini'] : '';
        $dryRun = !empty( $options['dry-run'] );
        $force = !empty( $options['force'] );
        if ( isset( $options['twig-suffix'] ) && $options['twig-suffix'] !== false && $options['twig-suffix'] !== null && $options['twig-suffix'] !== '' )
        {
            if ( !in_array( (string)$options['twig-suffix'], self::TWIG_SUFFIXES, true ) )
            {
                $err( 'FAIL --twig-suffix must be ' . implode( ' or ', self::TWIG_SUFFIXES ) . ': ' . $options['twig-suffix'] );
                return self::EXIT_USAGE;
            }
            $this->twigSuffix = (string)$options['twig-suffix'];
        }
        $sourceSuffix = $this->sourceSuffix();
        $targetSuffix = $this->targetSuffix();

        // The list of (source file, target file, relative name) to convert.
        $jobs = array();
        if ( $file !== '' && $from === '' )
        {
            if ( !is_file( $file ) || substr( $file, -strlen( $sourceSuffix ) ) !== $sourceSuffix )
            {
                $err( "FAIL --file must be an existing $sourceSuffix file: $file" );
                return self::EXIT_USAGE;
            }
            $target = $to !== '' ? $to : substr( $file, 0, -strlen( $this->sourceSuffixOf( $file ) ) ) . $targetSuffix;
            if ( substr( $target, -strlen( $targetSuffix ) ) !== $targetSuffix )
            {
                $err( "FAIL --to must name the $targetSuffix file to write when --file is given without --from: $target" );
                return self::EXIT_USAGE;
            }
            if ( $overrideIni !== '' )
            {
                $err( 'FAIL --override-ini needs --from (the design directory)' );
                return self::EXIT_USAGE;
            }
            $jobs[] = array( $file, $target, basename( $file ) );
        }
        else
        {
            if ( $from === '' || !is_dir( $from ) )
            {
                $err( 'FAIL --from must be a design directory' . ( $from !== '' ? ": $from" : '' ) . ( $file === '' ? ' (or give --file)' : '' ) );
                return self::EXIT_USAGE;
            }
            if ( $to === '' )
                $to = $from;
            $files = array();
            if ( $file !== '' )
            {
                $path = is_file( $file ) ? $file : $from . '/' . ltrim( $file, '/' );
                $real = realpath( $path );
                $realFrom = realpath( $from );
                if ( $real === false || !is_file( $real ) || strpos( $real, $realFrom . '/' ) !== 0 || substr( $real, -strlen( $sourceSuffix ) ) !== $sourceSuffix )
                {
                    $err( "FAIL --file must be a $sourceSuffix file below --from: $file" );
                    return self::EXIT_USAGE;
                }
                $files[] = $from . '/' . substr( $real, strlen( $realFrom ) + 1 );
            }
            else
            {
                $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $from, FilesystemIterator::SKIP_DOTS ) );
                foreach ( $iterator as $entry )
                {
                    if ( $entry->isFile() && substr( $entry->getFilename(), -strlen( $sourceSuffix ) ) === $sourceSuffix )
                        $files[] = $entry->getPathname();
                }
                sort( $files );
            }
            $byTarget = array();
            foreach ( $files as $source )
            {
                $relative = substr( $source, strlen( $from ) + 1 );
                $target = $to . '/' . substr( $relative, 0, -strlen( $this->sourceSuffixOf( $relative ) ) ) . $targetSuffix;
                // x.twig and x.html.twig are the same template: x.twig wins, as when the design resource finds both
                if ( isset( $byTarget[$target] ) )
                {
                    $kept = $jobs[$byTarget[$target]][2];
                    if ( substr( $relative, -10 ) === '.html.twig' )
                    {
                        $out( "NOTE $relative: $kept is the same template and is converted instead" );
                        continue;
                    }
                    $out( "NOTE $kept: $relative is the same template and is converted instead" );
                    $jobs[$byTarget[$target]] = array( $source, $target, $relative );
                    continue;
                }
                $byTarget[$target] = count( $jobs );
                $jobs[] = array( $source, $target, $relative );
            }
        }

        if ( $dryRun )
            $out( 'Dry run: nothing is written.' );
        foreach ( $jobs as $job )
        {
            list( $source, $target, $relative ) = $job;
            $this->convertOne( $source, $target, $relative, $dryRun, $force, $out, $err );
        }

        $c = $this->counts;
        $summary = sprintf( '%s %d templates: %d %s, %d unchanged, %d skipped, %d errors; %d notes in %d templates',
                            $this->direction(), $c['templates'], $c['written'], $dryRun ? 'to write' : 'written', $c['unchanged'],
                            $c['skipped'], $c['errors'], $c['notes'], $c['withNotes'] );
        if ( $c['errors'] )
        {
            $err( 'FAIL ' . $summary );
            return self::EXIT_ERRORS;
        }

        if ( $overrideIni !== '' )
        {
            if ( $dryRun )
                $out( "Dry run: would write $overrideIni" );
            else if ( !$this->writeOverrideIni( $overrideIni, $out, $err ) )
            {
                $err( 'FAIL ' . $summary );
                return self::EXIT_ERRORS;
            }
        }
        $out( 'PASS ' . $summary . ( $c['skipped'] ? ' (a skipped file was changed or written by hand: --force replaces it)' : '' ) );
        return self::EXIT_OK;
    }

    /**
     * Converts one template file and writes the result (unless $dryRun), keeping a target changed by hand.
     *
     * @return string written, unchanged, skipped or error
     */
    public function convertOne( $source, $target, $relative, $dryRun, $force, $out, $err )
    {
        ++$this->counts['templates'];
        $text = @file_get_contents( $source );
        if ( $text === false )
        {
            ++$this->counts['errors'];
            $err( "ERROR $relative: cannot read $source" );
            return 'error';
        }
        try
        {
            list( $converted, $notes ) = $this->convertText( $text, $relative );
        }
        catch ( Throwable $e )
        {
            ++$this->counts['errors'];
            $err( "ERROR $relative: " . $e->getMessage() );
            return 'error';
        }
        foreach ( $notes as $note )
            $out( "NOTE $relative: $note" );
        $this->counts['notes'] += count( $notes );
        if ( $notes )
            ++$this->counts['withNotes'];
        $problem = $this->verify( $converted, $relative );
        if ( $problem !== null )
        {
            ++$this->counts['errors'];
            $err( "ERROR $relative: $problem; nothing written" );
            return 'error';
        }

        $result = $this->withMarker( $converted, $relative );
        $state = self::targetState( $target );
        if ( $state === 'generated' && file_get_contents( $target ) === $result )
        {
            ++$this->counts['unchanged'];
            $this->converted[$relative] = $target;
            return 'unchanged';
        }
        if ( ( $state === 'edited' || $state === 'foreign' ) && !$force )
        {
            ++$this->counts['skipped'];
            // the target stays the design's template (changed by hand): the override settings must still point at it
            $this->converted[$relative] = $target;
            $out( "SKIP $relative: $target " . ( $state === 'edited' ? 'was changed after it was generated' : 'has no conversion marker (written by hand)' ) .
                  '; --force replaces it' );
            return 'skipped';
        }
        $this->converted[$relative] = $target;
        if ( $dryRun )
        {
            ++$this->counts['written'];
            $out( ( $state === 'new' ? 'WRITE ' : 'REPLACE ' ) . $target );
            return 'written';
        }
        if ( !is_dir( dirname( $target ) ) && !eZDir::mkdir( dirname( $target ), false, true ) )
        {
            unset( $this->converted[$relative] );
            ++$this->counts['errors'];
            $err( "ERROR $relative: cannot create the directory of $target" );
            return 'error';
        }
        if ( @file_put_contents( $target, $result ) === false )
        {
            unset( $this->converted[$relative] );
            ++$this->counts['errors'];
            $err( "ERROR $relative: cannot write $target" );
            return 'error';
        }
        ++$this->counts['written'];
        $out( ( $state === 'new' ? 'WRITE ' : 'REPLACE ' ) . $target );
        return 'written';
    }

    /**
     * Writes an override.ini.append.php for a siteaccess that uses the converted design: every override rule of the
     * current siteaccess (-s), with MatchFile pointing at the converted template where one was converted; when the
     * source engine is tpl, also one rule per converted templates/*.tpl that puts the converted file in its place
     * (the template system asks for pagelayout.tpl, content/search.tpl ...).
     *
     * @return bool
     */
    public function writeOverrideIni( $file, $out, $err )
    {
        $sourceSuffix = $this->sourceSuffix();
        $targetSuffix = $this->targetSuffix();
        $ownRules = $this->from === 'tpl';
        $overrideIni = eZINI::instance( 'override.ini' );
        $lines = array( '<?php /* #?ini charset="utf-8"?', '',
                        '; Generated by exp:template:convert (' . $this->direction() . '): the override rules of the siteaccess the',
                        '; design was converted for, with MatchFile pointing at the converted ' . $targetSuffix .
                        ( $ownRules ? ', then one rule per' : '.' ) );
        if ( $ownRules )
            $lines[] = '; template of the design that puts its ' . $targetSuffix . ' in the place of the .tpl. Lower Priority is tried first.';
        $lines[] = '';

        // The rules that put each converted templates/*.tpl in its place, written after the siteaccess's own rules.
        $ownGroups = array();
        if ( $ownRules )
        {
            foreach ( array_keys( $this->converted ) as $relative )
            {
                if ( strncmp( $relative, 'templates/', 10 ) !== 0 )
                    continue;
                $source = substr( $relative, 10 );
                $base = substr( $source, 0, -strlen( $this->sourceSuffixOf( $source ) ) );
                $group = $this->to . '_' . preg_replace( '/[^a-z0-9_]/i', '_', $base );
                // MatchFile is looked up below override/templates of each design: ../../templates is the
                // design's own templates directory, where the converted file sits next to where the .tpl was.
                $ownGroups[$group] = array( '[' . $group . ']', 'Source=' . $source, 'MatchFile=../../templates/' . $base . $targetSuffix,
                                            'Subdir=templates', '' );
            }
        }

        $mapped = 0;
        $suffixPattern = $this->from === 'twig' ? '/(\.html)?\.twig$/' : '/' . preg_quote( $sourceSuffix, '/' ) . '$/';
        foreach ( $overrideIni->groups() as $group => $settings )
        {
            if ( !isset( $settings['Source'] ) || !isset( $settings['MatchFile'] ) )
                continue;
            // a rule this conversion writes itself (the siteaccess already uses the converted design): written once, below
            if ( isset( $ownGroups[$group] ) )
                continue;
            $matchFile = $settings['MatchFile'];
            if ( isset( $this->converted['override/templates/' . $matchFile] ) )
            {
                $matchFile = preg_replace( $suffixPattern, $targetSuffix, $matchFile );
                ++$mapped;
            }
            $lines[] = '[' . $group . ']';
            $lines[] = 'Source=' . $settings['Source'];
            $lines[] = 'MatchFile=' . $matchFile;
            $lines[] = 'Subdir=' . ( isset( $settings['Subdir'] ) ? $settings['Subdir'] : 'templates' );
            if ( isset( $settings['Match'] ) && is_array( $settings['Match'] ) )
            {
                foreach ( $settings['Match'] as $key => $value )
                    $lines[] = 'Match[' . $key . ']=' . $value;
            }
            if ( isset( $settings['Priority'] ) && $settings['Priority'] !== '' )
                $lines[] = 'Priority=' . $settings['Priority'];
            $lines[] = '';
        }
        foreach ( $ownGroups as $groupLines )
            $lines = array_merge( $lines, $groupLines );
        $own = count( $ownGroups );
        $lines[] = '*/ ?>';
        if ( @file_put_contents( $file, implode( "\n", $lines ) . "\n" ) === false )
        {
            $err( "ERROR cannot write $file" );
            return false;
        }
        $out( "PASS wrote $file: $mapped rules pointed at $targetSuffix" . ( $ownRules ? ", $own rules for the design's templates" : '' ) );
        return true;
    }

    private function resetCounts()
    {
        $this->counts = array( 'templates' => 0, 'written' => 0, 'unchanged' => 0, 'skipped' => 0, 'errors' => 0,
                               'notes' => 0, 'withNotes' => 0 );
    }
}
