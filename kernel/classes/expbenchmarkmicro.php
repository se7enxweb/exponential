<?php
/**
 * File containing the expBenchmarkMicro class.
 *
 * The micro mode of exp:benchmark: pure-PHP hot paths of the kernel, timed in this process without HTTP, a web
 * server or a database, so it runs on a CI runner with nothing installed but PHP and the Composer packages.
 *
 *   template_compile   shipped templates parsed and compiled to PHP, every time anew
 *   template_render    the same templates' compiled code run with fixed variables
 *   ini_parse          INI files of settings/ parsed from disk (eZINI::parseFile()), no INI cache, no overrides
 *   autoload_map       the kernel autoload array (autoload/ezp_kernel.php) read, as the first lookup of a request does
 *   autoload_miss      class_exists() of names nobody defines, through every registered autoloader
 *   uri_parse          eZURI parsing of module URLs with ordered and named view parameters
 *   i18n_load          the German translation file (share/translations/ger-DE) parsed, no translation cache and
 *                      no extension translations
 *   i18n_lookup        translations looked up in it by context and source
 *   datatype_validate  posted input validated by the integer, float, text line, email and date datatypes,
 *                      against content class attributes built in memory, and class attribute input of the integer
 *
 * Each probe runs a fixed number of operations per iteration, the same in every run, so ops/s and the median
 * time of an iteration compare from one run to the next. Before and after the probes a calibration loop of
 * fixed pure-PHP work is timed; every probe's median divided by the calibration median (its "normalized" value)
 * is what is left after the speed of the machine is divided out, and what the CI check compares.
 *
 * Writes nothing but its compiled templates, into a directory of its own under the cache directory, which it
 * removes again.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expBenchmarkMicro
{
    /** Shipped templates that are compiled; the first is also rendered. */
    const COMPILE_TEMPLATES = 'design/standard/templates/navigator/google.tpl,design/standard/templates/pagelayout.tpl,design/admin/templates/pagelayout.tpl,design/standard/templates/content/edit.tpl,design/standard/templates/node/view/full.tpl';

    /** The settings files ini_parse reads, in this order. */
    const INI_FILES = 'site.ini,content.ini,template.ini,i18n.ini,image.ini,design.ini,menu.ini';

    /** The translation ini18n_load and i18n_lookup use, and how many of its messages are looked up. */
    const TRANSLATION_LOCALE = 'ger-DE';
    const TRANSLATION_LOOKUPS = 300;

    /**
     * @return array the options and their defaults
     */
    public static function defaults()
    {
        return array(
            'repeat' => 30,       // measured iterations of every probe
            'warmup' => 3,        // unmeasured iterations first
            'probes' => array(),  // empty for all
        );
    }

    /**
     * @return array the names of every probe, in the order they run
     */
    public static function probeNames()
    {
        return array( 'template_compile', 'template_render', 'ini_parse', 'autoload_map', 'autoload_miss',
                      'uri_parse', 'i18n_load', 'i18n_lookup', 'datatype_validate' );
    }

    /**
     * Fixed pure-PHP work: integer arithmetic, string building, sorting, a regular expression, hashing and an
     * associative array, the operations the kernel's hot paths consist of. It does the same work on every machine
     * and every run; its time is the yardstick the probes are divided by.
     *
     * @return int a checksum of the work, the same every time (so the loop cannot be optimised away unnoticed)
     */
    public static function calibrationLoop()
    {
        $acc = 0;
        for ( $i = 0; $i < 120000; $i++ )
            $acc = ( $acc * 31 + $i ) % 1000003;

        $words = array();
        for ( $i = 0; $i < 12000; $i++ )
            $words[] = 'w' . ( ( $i * 7919 ) % 20011 );
        sort( $words, SORT_STRING );
        $text = implode( ' ', $words );
        $acc += preg_match_all( '/\bw1\d+\b/', $text );

        $map = array();
        foreach ( $words as $word )
            $map[$word] = strlen( $word ) + ( isset( $map[$word] ) ? $map[$word] : 0 );
        $acc += count( $map ) + array_sum( $map );
        $acc += crc32( $text ) % 9973;
        $acc += hexdec( substr( md5( $text ), 0, 6 ) ) % 9973;
        $acc += strlen( str_replace( 'w1', 'W-1', $text ) );
        return $acc;
    }

    /**
     * Times the calibration loop.
     *
     * @param int $repeat
     * @param int $warmup
     * @return array milliseconds per run
     */
    public static function calibrate( $repeat, $warmup = 0 )
    {
        for ( $i = 0; $i < $warmup; $i++ )
            self::calibrationLoop();
        $samples = array();
        for ( $i = 0; $i < max( 1, (int)$repeat ); $i++ )
        {
            $start = hrtime( true );
            self::calibrationLoop();
            $samples[] = ( hrtime( true ) - $start ) / 1e6;
        }
        return $samples;
    }

    /**
     * The calibration of a run from the samples taken before and after the probes.
     *
     * @param array $before milliseconds
     * @param array $after milliseconds
     * @return array the summary of both together, plus before_median, after_median and drift_pct (how much the
     *               machine slowed down, or sped up, while the probes ran; large values mean a noisy run)
     */
    public static function calibrationSummary( $before, $after )
    {
        $summary = expBenchmark::summarize( array_merge( (array)$before, (array)$after ) );
        $b = expBenchmark::summarize( $before );
        $a = expBenchmark::summarize( $after );
        $summary['before_median'] = $b['median'];
        $summary['after_median'] = $a['median'];
        $summary['drift_pct'] = expBenchmark::changePercent( $b['median'], $a['median'] );
        $summary['checksum'] = self::calibrationLoop();
        return $summary;
    }

    /**
     * Runs the probes.
     *
     * @param array $options see defaults()
     * @param callable|null $progress called with a line of text after every probe
     * @return array( 'rows' => list of rows (expBenchmark::microRow()), 'calibration' => calibrationSummary() )
     */
    public static function run( $options = array(), $progress = null )
    {
        $options = array_merge( self::defaults(), (array)$options );
        $repeat = max( 1, (int)$options['repeat'] );
        $warmup = max( 0, (int)$options['warmup'] );
        $wanted = empty( $options['probes'] ) ? self::probeNames() : (array)$options['probes'];

        $before = self::calibrate( $repeat, $warmup );
        $calibrationMedian = expBenchmark::percentile( self::sorted( $before ), 50 );
        if ( $progress )
            call_user_func( $progress, sprintf( '  %-18s %3d runs  median %.3f ms', 'calibration', count( $before ), $calibrationMedian ) );

        $results = array();
        foreach ( self::probeNames() as $name )
        {
            if ( !in_array( $name, $wanted, true ) )
                continue;
            $results[$name] = self::runProbe( $name, $repeat, $warmup );
            if ( $progress )
            {
                $median = expBenchmark::percentile( self::sorted( $results[$name]['samples'] ), 50 );
                call_user_func( $progress, sprintf( '  %-18s %3d runs  median %s ms', $name, count( $results[$name]['samples'] ),
                                                    $median === null ? '-' : sprintf( '%.3f', $median ) ) );
            }
        }

        $after = self::calibrate( $repeat );
        $calibration = self::calibrationSummary( $before, $after );

        $rows = array();
        foreach ( $results as $name => $result )
            $rows[] = expBenchmark::microRow( $name, $result['samples'], $result['ops'], $calibration['median'], $result['errors'], $result['note'] );
        return array( 'rows' => $rows, 'calibration' => $calibration );
    }

    /**
     * Prepares one probe, runs it $warmup times unmeasured and $repeat times measured, and cleans up.
     *
     * @return array( 'samples' => ms list, 'ops' => int, 'errors' => int, 'note' => string )
     */
    public static function runProbe( $name, $repeat, $warmup = 0 )
    {
        $cleanup = null;
        try
        {
            $method = 'probe' . str_replace( ' ', '', ucwords( str_replace( '_', ' ', $name ) ) );
            if ( !method_exists( __CLASS__, $method ) )
                throw new InvalidArgumentException( "Unknown micro probe \"$name\"" );
            // array( callable that does one iteration and returns false on failure, ops per iteration, note, cleanup )
            list( $callback, $ops, $note, $cleanup ) = array_pad( call_user_func( array( __CLASS__, $method ) ), 4, null );
        }
        catch ( Throwable $e )
        {
            return array( 'samples' => array(), 'ops' => 1, 'errors' => $repeat, 'note' => 'failed: ' . $e->getMessage() );
        }

        $samples = array();
        $errors = 0;
        $lastError = '';
        for ( $i = 0; $i < $warmup + $repeat; $i++ )
        {
            try
            {
                $start = hrtime( true );
                $ok = call_user_func( $callback );
                $elapsed = ( hrtime( true ) - $start ) / 1e6;
                if ( $i < $warmup )
                    continue;
                if ( $ok === false )
                {
                    $errors++;
                    continue;
                }
                $samples[] = $elapsed;
            }
            catch ( Throwable $e )
            {
                if ( $i >= $warmup )
                    $errors++;
                $lastError = $e->getMessage();
            }
        }
        if ( $cleanup )
            call_user_func( $cleanup );
        if ( $errors > 0 )
            $note = 'failed: ' . ( $lastError !== '' ? $lastError : 'an iteration returned false' );
        return array( 'samples' => $samples, 'ops' => $ops, 'errors' => $errors, 'note' => $note );
    }

    // The probes. Each returns array( callable, ops per iteration, note[, cleanup callable] ).

    protected static function probeTemplateCompile()
    {
        $templates = self::compileTemplates();
        $restore = self::useCompileDirectory( true );
        $tpl = eZTemplate::factory();
        $callback = function () use ( $tpl, $templates )
        {
            foreach ( $templates as $file )
            {
                if ( !$tpl->compileTemplateFile( $file ) )
                    return false;
            }
            return true;
        };
        return array( $callback, count( $templates ), count( $templates ) . ' shipped templates, parsed and compiled each time', $restore );
    }

    protected static function probeTemplateRender()
    {
        $restore = self::useCompileDirectory( false );
        $shipped = self::compileTemplates();
        $templates = array( $shipped[0] => array(
            'page_uri' => '/content/view/full/2', 'item_count' => 250, 'item_limit' => 10,
            'view_parameters' => array( 'offset' => 40, 'sort' => 'name' ),
        ) );
        $fixture = self::fixtureTemplate();
        $templates[$fixture] = array(
            'title' => 'Exponential <benchmark> & "quotes"',
            'items' => self::fixtureItems(),
        );
        $compiled = eZTemplateCompiler::isCompilationEnabled();
        $callback = function () use ( $templates )
        {
            $length = 0;
            foreach ( $templates as $file => $variables )
            {
                $tpl = eZTemplate::factory();
                foreach ( $variables as $name => $value )
                    $tpl->setVariable( $name, $value );
                $length += strlen( (string)$tpl->fetch( 'file:' . $file ) );
                foreach ( array_keys( $variables ) as $name )
                    $tpl->unsetVariable( $name );
            }
            return $length > 0;
        };
        $note = count( $templates ) . ' templates rendered, ' . ( $compiled ? 'compiled' : 'interpreted (template compilation is off)' );
        return array( $callback, count( $templates ), $note, function () use ( $restore, $fixture )
        {
            @unlink( $fixture );
            call_user_func( $restore );
        } );
    }

    protected static function probeIniParse()
    {
        $files = array();
        foreach ( explode( ',', self::INI_FILES ) as $file )
        {
            if ( is_file( 'settings/' . $file ) )
                $files[] = $file;
        }
        $callback = function () use ( $files )
        {
            foreach ( $files as $file )
            {
                // no INI cache and none of the override directories (which differ from one installation to
                // the next): only the file settings/ ships, so every installation parses the same text
                $ini = new eZINI( $file, 'settings', null, false, false, false, false, false );
                $ini->parseFile( 'settings/' . $file );
                if ( count( $ini->groups() ) === 0 )
                    return false;
            }
            return true;
        };
        return array( $callback, count( $files ), count( $files ) . ' files of settings/ (' . implode( ', ', $files ) . ')' );
    }

    protected static function probeAutoloadMap()
    {
        $file = 'autoload/ezp_kernel.php';
        $count = count( include $file );
        $callback = function () use ( $file )
        {
            $map = include $file;
            return is_array( $map ) && isset( $map['eZINI'] );
        };
        return array( $callback, 1, "$file, $count classes" );
    }

    protected static function probeAutoloadMiss()
    {
        $names = array();
        for ( $i = 0; $i < 2000; $i++ )
            $names[] = 'expBenchmarkMicroMissingClass' . $i;
        $callback = function () use ( $names )
        {
            foreach ( $names as $name )
            {
                if ( class_exists( $name ) )
                    return false;
            }
            return true;
        };
        return array( $callback, count( $names ), count( spl_autoload_functions() ) . ' registered autoloaders' );
    }

    protected static function probeUriParse()
    {
        $uris = array(
            '/', 'content/view/full/2', '/content/view/full/144/(offset)/20/(sort)/name',
            'content/edit/57/3/eng-GB', 'user/login', 'Products/Shoes/Running-shoe-42',
            '/content/search/(SearchText)/benchmark/(SubTreeArray)/2', 'layout/set/print/News/Article',
            'ezjscore/call/ezjsc::time', '/content/view/full/2/(year)/2026/(month)/10/(day)/5',
        );
        $times = 50;
        $callback = function () use ( $uris, $times )
        {
            $count = 0;
            for ( $i = 0; $i < $times; $i++ )
            {
                foreach ( $uris as $string )
                {
                    $uri = new eZURI( $string );
                    $count += count( $uri->elements( false ) ) + count( $uri->userParameters() );
                }
            }
            return $count > 0;
        };
        return array( $callback, count( $uris ) * $times, count( $uris ) . ' module URLs, elements and view parameters' );
    }

    protected static function probeI18nLoad()
    {
        list( $file, $pairs ) = self::translationPairs( 1 );
        $context = $pairs[0][0];
        $callback = function () use ( $context )
        {
            return self::translator()->load( $context );
        };
        return array( $callback, 1, sprintf( '%s, %.0f KB, no translation cache', $file, filesize( $file ) / 1024 ) );
    }

    protected static function probeI18nLookup()
    {
        list( $file, $pairs ) = self::translationPairs( self::TRANSLATION_LOOKUPS );
        $translator = self::translator();
        $translator->load( $pairs[0][0] );
        $times = 10;
        $callback = function () use ( $translator, $pairs, $times )
        {
            $found = 0;
            for ( $i = 0; $i < $times; $i++ )
            {
                foreach ( $pairs as $pair )
                {
                    if ( $translator->translate( $pair[0], $pair[1] ) !== null )
                        $found++;
                }
            }
            return $found > 0;
        };
        return array( $callback, count( $pairs ) * $times, count( $pairs ) . ' messages of ' . self::TRANSLATION_LOCALE . ", $times times" );
    }

    protected static function probeDatatypeValidate()
    {
        $cases = self::validationCases();
        $http = eZHTTPTool::instance();
        $saved = $_POST;
        $times = 20;
        $callback = function () use ( $cases, $http, $times )
        {
            for ( $i = 0; $i < $times; $i++ )
            foreach ( $cases as $case )
            {
                $_POST = $case['post'];
                if ( $case['class'] )
                    $state = $case['datatype']->validateClassAttributeHTTPInput( $http, 'ContentClass', $case['attribute'] );
                else
                    $state = $case['datatype']->validateObjectAttributeHTTPInput( $http, 'ContentObjectAttribute', $case['attribute'] );
                if ( $state !== $case['expect'] )
                    throw new RuntimeException( sprintf( '%s gave state %s for %s, expected %s', $case['name'],
                                                         var_export( $state, true ), json_encode( $case['post'] ), $case['expect'] ) );
            }
            return true;
        };
        $cleanup = function () use ( $saved, $cases )
        {
            $_POST = $saved;
            foreach ( $cases as $case )
                unset( $GLOBALS['eZContentClassAttributeCache'][$case['class_id']] );
        };
        return array( $callback, count( $cases ) * $times, count( $cases ) . " inputs $times times: integer, float, text line, email, date, integer class settings", $cleanup );
    }

    // Helpers

    /**
     * @return array paths of the shipped templates the template probes use (those that exist)
     */
    public static function compileTemplates()
    {
        $files = array();
        foreach ( explode( ',', self::COMPILE_TEMPLATES ) as $file )
        {
            if ( is_file( $file ) )
                $files[] = $file;
        }
        if ( !$files )
            throw new RuntimeException( 'None of the shipped templates was found; run from the installation root' );
        return $files;
    }

    /**
     * Points the template compiler at a directory of the benchmark's own.
     *
     * @param bool $alwaysGenerate compile anew every time (template_compile) or use what is compiled (template_render)
     * @return callable that restores the compiler settings and removes the directory
     */
    protected static function useCompileDirectory( $alwaysGenerate )
    {
        $previous = isset( $GLOBALS['eZTemplateCompilerSettings'] ) ? $GLOBALS['eZTemplateCompilerSettings'] : null;
        $directory = eZDir::path( array( eZSys::cacheDirectory(), 'benchmark-micro', 'compiled-' . getmypid() ) );
        eZTemplateCompiler::setSettings( array( 'compile' => true, 'generate' => (bool)$alwaysGenerate,
                                                'comments' => false, 'compilation-directory' => $directory ) );
        return function () use ( $previous, $directory )
        {
            if ( $previous === null )
                unset( $GLOBALS['eZTemplateCompilerSettings'] );
            else
                $GLOBALS['eZTemplateCompilerSettings'] = $previous;
            if ( is_dir( $directory ) )
                eZDir::recursiveDelete( $directory );
            $parent = dirname( $directory );
            if ( is_dir( $parent ) and count( (array)@scandir( $parent ) ) <= 2 )
                @rmdir( $parent );
        };
    }

    /**
     * Writes the template_render fixture: loops, conditions, operators, escaping, a translation and an
     * include-free layout, the constructs a full view consists of. Its text is fixed, so every run renders
     * the same thing.
     *
     * @return string the file
     */
    public static function fixtureTemplate()
    {
        $directory = eZDir::path( array( eZSys::cacheDirectory(), 'benchmark-micro' ) );
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        $file = $directory . '/fixture-' . getmypid() . '.tpl';
        $text = <<<'TPL'
<h1>{$title|wash}</h1>
{def $total=0 $odd=array()}
<ul class="items">
{foreach $items as $index => $item}
    {set $total=$total|sum( $item.price )}
    {if $index|mod( 2 )}{set $odd=$odd|append( $item.name )}{/if}
    <li class="{cond( $item.featured, 'featured', 'plain' )}" data-id="{$item.id}">
        <a href={concat( '/content/view/full/', $item.id )|ezurl}>{$item.name|wash|shorten( 30 )}</a>
        <span>{$item.price|l10n( 'number' )}</span> {$item.tags|implode( ', ' )|upcase}
    </li>
{/foreach}
</ul>
<p>{'Search'|i18n( 'design/standard/content/search' )}: {$items|count} / {$total} / {$odd|implode( ' ' )|wash}</p>
{undef}
TPL;
        eZFile::create( basename( $file ), $directory, $text );
        return $file;
    }

    /**
     * @return array the items the fixture template renders: 40 of them, always the same
     */
    public static function fixtureItems()
    {
        $items = array();
        for ( $i = 1; $i <= 40; $i++ )
        {
            $items[] = array( 'id' => 100 + $i, 'name' => "Benchmark item number $i with a longer name",
                              'price' => $i * 3.25, 'featured' => $i % 7 === 0, 'tags' => array( 'tag' . ( $i % 5 ), 'all' ) );
        }
        return $items;
    }

    /**
     * A translator of the shipped translation only: no translation cache, and none of the translations of the
     * extensions ([RegionalSettings] TranslationExtensions), which differ from one installation to the next.
     *
     * @return eZTSTranslator
     */
    protected static function translator()
    {
        $translator = new eZTSTranslator( self::TRANSLATION_LOCALE, 'translation.ts', false );
        $translator->RootCache = array( 'roots' => array( 'share/translations' ) );
        return $translator;
    }

    /**
     * The first $count (context, source) pairs of the translation file, in file order.
     *
     * @return array( file, list of array( context, source ) )
     */
    protected static function translationPairs( $count )
    {
        $file = 'share/translations/' . self::TRANSLATION_LOCALE . '/translation.ts';
        if ( !is_file( $file ) )
            throw new RuntimeException( "$file not found" );
        $dom = new DOMDocument();
        if ( !@$dom->load( $file ) )
            throw new RuntimeException( "$file is not readable XML" );
        $pairs = array();
        foreach ( $dom->getElementsByTagName( 'context' ) as $context )
        {
            $name = $context->getElementsByTagName( 'name' )->item( 0 );
            foreach ( $context->getElementsByTagName( 'message' ) as $message )
            {
                $source = $message->getElementsByTagName( 'source' )->item( 0 );
                if ( $name and $source )
                    $pairs[] = array( $name->textContent, $source->textContent );
                if ( count( $pairs ) >= $count )
                    break 2;
            }
        }
        if ( !$pairs )
            throw new RuntimeException( "$file has no messages" );
        return array( $file, $pairs );
    }

    /**
     * Content class attributes and object attributes built in memory (the class attributes are put in the
     * cache eZContentClassAttribute::fetch() reads first, so nothing asks the database), with posted input.
     *
     * @return array list of array( name, datatype, attribute, class, class_id, post, expect )
     */
    protected static function validationCases()
    {
        $valid = eZInputValidator::STATE_ACCEPTED;
        $invalid = eZInputValidator::STATE_INVALID;
        $names = serialize( array( 'eng-GB' => 'Benchmark', 'always-available' => 'eng-GB' ) );
        $definitions = array(
            // datatype, class attribute fields, posted field, values => expected state
            array( 'ezinteger', array( 'data_int1' => 1, 'data_int2' => 1000, 'data_int4' => 3 ), '_data_integer_',
                   array( '42' => $valid, '1000' => $valid, '1001' => $invalid, 'x1' => $invalid, '' => $invalid ) ),
            array( 'ezfloat', array( 'data_float1' => 0.5, 'data_float2' => 99.5, 'data_float4' => 3 ), '_data_float_',
                   array( '3.25' => $valid, '99.6' => $invalid, 'abc' => $invalid ) ),
            array( 'ezstring', array( 'data_int1' => 20 ), '_ezstring_data_text_',
                   array( 'A short text line' => $valid, 'A text line that is far too long for it' => $invalid, '' => $invalid ) ),
            array( 'ezemail', array(), '_data_text_',
                   array( 'someone@example.com' => $valid, 'not an address' => $invalid ) ),
            array( 'ezdate', array( 'data_int1' => 0 ), '', array() ),
        );

        $cases = array();
        $id = 990000;
        // a date, posted as year, month and day
        $dates = array( array( '2026', '10', '5', $valid ), array( '2026', '2', '30', $invalid ), array( '2026', '13', '1', $invalid ) );
        foreach ( $definitions as $definition )
        {
            list( $type, $fields, $postField, $inputs ) = $definition;
            $id++;
            $classAttribute = new eZContentClassAttribute( array(
                'id' => $id, 'version' => eZContentClass::VERSION_STATUS_DEFINED, 'contentclass_id' => 1,
                'identifier' => 'benchmark_' . $type, 'data_type_string' => $type, 'is_required' => 1,
                'is_information_collector' => 0, 'can_translate' => 1, 'serialized_name_list' => $names,
                'serialized_description_list' => $names, 'serialized_data_text' => $names,
            ) + $fields );
            $GLOBALS['eZContentClassAttributeCache'][$id][eZContentClass::VERSION_STATUS_DEFINED] = $classAttribute;
            $objectAttribute = new eZContentObjectAttribute( array(
                'id' => $id, 'version' => 1, 'contentobject_id' => 1, 'contentclassattribute_id' => $id,
                'data_type_string' => $type, 'language_code' => 'eng-GB',
            ) );
            $datatype = eZDataType::create( $type );
            if ( !$datatype )
                throw new RuntimeException( "Datatype $type is not registered" );
            foreach ( $inputs as $value => $expect )
            {
                $cases[] = array( 'name' => $type, 'datatype' => $datatype, 'attribute' => $objectAttribute,
                                  'class' => false, 'class_id' => $id, 'expect' => $expect,
                                  'post' => array( 'ContentObjectAttribute' . $postField . $id => (string)$value ) );
            }
            if ( $type === 'ezdate' )
            {
                foreach ( $dates as $date )
                {
                    $cases[] = array( 'name' => 'ezdate', 'datatype' => $datatype, 'attribute' => $objectAttribute,
                                      'class' => false, 'class_id' => $id, 'expect' => $date[3],
                                      'post' => array( 'ContentObjectAttribute_date_year_' . $id => $date[0],
                                                       'ContentObjectAttribute_date_month_' . $id => $date[1],
                                                       'ContentObjectAttribute_date_day_' . $id => $date[2] ) );
                }
            }
            if ( $type === 'ezinteger' )
            {
                foreach ( array( array( '1', '100', '50', $valid ), array( '1', '100', 'x', $invalid ) ) as $class )
                {
                    $cases[] = array( 'name' => 'ezinteger class', 'datatype' => $datatype, 'attribute' => $classAttribute,
                                      'class' => true, 'class_id' => $id, 'expect' => $class[3],
                                      'post' => array( 'ContentClass_ezinteger_min_integer_value_' . $id => $class[0],
                                                       'ContentClass_ezinteger_max_integer_value_' . $id => $class[1],
                                                       'ContentClass_ezinteger_default_value_' . $id => $class[2] ) );
                }
            }
        }
        return $cases;
    }

    protected static function sorted( $samples )
    {
        $samples = array_map( 'floatval', (array)$samples );
        sort( $samples, SORT_NUMERIC );
        return $samples;
    }
}
