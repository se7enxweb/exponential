<?php
/**
 * Fixes of defects that forks of eZ Publish Legacy (Opencontent, Mugo Web) found and that this tree still had,
 * each written anew here. DB-less: the URL alias depth limit is shown by a lookup that returns before it asks the
 * database.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expLegacyForkFixesTest extends PHPUnit\Framework\TestCase
{
    private $iniSaved = null;

    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    protected function tearDown(): void
    {
        if ( $this->iniSaved !== null )
        {
            $ini = eZINI::instance();
            if ( $this->iniSaved === false )
                $ini->removeSetting( 'URLTranslator', 'MaxPathDepth' );
            else
                $ini->setVariable( 'URLTranslator', 'MaxPathDepth', $this->iniSaved );
            $this->iniSaved = null;
        }
    }

    private function setMaxPathDepth( $value )
    {
        $ini = eZINI::instance();
        if ( $this->iniSaved === null )
            $this->iniSaved = $ini->hasVariable( 'URLTranslator', 'MaxPathDepth' ) ? $ini->variable( 'URLTranslator', 'MaxPathDepth' ) : false;
        $ini->setVariable( 'URLTranslator', 'MaxPathDepth', $value );
    }

    /**
     * Runs PHP code in a child process from the root of the tree, returns its output (stdout and stderr).
     */
    private static function runPhp( $code, $cwd = null )
    {
        $process = proc_open( array( PHP_BINARY, '-d', 'display_errors=stderr', '-r', $code ),
                              array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $cwd === null ? self::root() : $cwd );
        $out = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        proc_close( $process );
        return $out;
    }

    public function testMaxPathDepthDefaultsTo30AndReadsSiteIni()
    {
        $this->assertMatchesRegularExpression( '/^MaxPathDepth=30$/m', file_get_contents( self::root() . '/settings/site.ini' ) );
        $this->setMaxPathDepth( '12' );
        $this->assertSame( 12, eZURLAliasML::maxPathDepth() );
        $this->setMaxPathDepth( '0' );
        $this->assertSame( 30, eZURLAliasML::maxPathDepth(), 'a value below 1 falls back to the default' );
    }

    public function testTranslateOfAPathDeeperThanTheLimitIsNotFound()
    {
        $this->setMaxPathDepth( '5' );
        $uri = 'a/b/c/d/e/f';
        $this->assertFalse( eZURLAliasML::translate( $uri ) );
        $this->assertSame( 'a/b/c/d/e/f', $uri, 'the URI is left as it was' );
    }

    public function testFetchByPathDeeperThanTheLimitFindsNothing()
    {
        $this->setMaxPathDepth( '5' );
        $this->assertSame( array(), eZURLAliasML::fetchByPath( 'a/b/c/d/e/f' ) );
        // the glob adds one more join
        $this->assertSame( array(), eZURLAliasML::fetchByPath( 'a/b/c/d/e', 'x*' ) );
    }

    public function testLogEntryStaysOnOneLine()
    {
        $dir = sys_get_temp_dir() . '/exp-log-' . getmypid() . '-' . mt_rand();
        mkdir( $dir );
        try
        {
            eZLog::write( "login failed for admin\r\n[ Jan 01 2026 00:00:00 ] [ forged ]\nentry", 'oneline.log', $dir );
            $lines = file( $dir . '/oneline.log', FILE_IGNORE_NEW_LINES );
            $this->assertCount( 1, $lines );
            $this->assertStringContainsString( 'login failed for admin [ Jan 01 2026 00:00:00 ] [ forged ] entry', $lines[0] );
        }
        finally
        {
            @unlink( $dir . '/oneline.log' );
            @rmdir( $dir );
        }
    }

    public function testStorageLogEntryStaysOnOneLine()
    {
        $dir = sys_get_temp_dir() . '/exp-storagelog-' . getmypid() . '-' . mt_rand();
        mkdir( $dir );
        $ini = eZINI::instance();
        $saved = $ini->hasVariable( 'FileSettings', 'LogDir' ) ? $ini->variable( 'FileSettings', 'LogDir' ) : false;
        $ini->setVariable( 'FileSettings', 'LogDir', $dir );
        try
        {
            eZLog::writeStorageLog( "upload\n[ Jan 01 2026 00:00:00 ] [forged].txt", "var/storage\r\n" );
            $lines = file( $dir . '/storage.log', FILE_IGNORE_NEW_LINES );
            $this->assertCount( 1, $lines );
            $this->assertStringContainsString( '[var/storage /upload [ Jan 01 2026 00:00:00 ] [forged].txt]', $lines[0] );
        }
        finally
        {
            if ( $saved === false )
                $ini->removeSetting( 'FileSettings', 'LogDir' );
            else
                $ini->setVariable( 'FileSettings', 'LogDir', $saved );
            @unlink( $dir . '/storage.log' );
            @rmdir( $dir );
        }
    }

    /**
     * "if ( $x );" ends the if at the semicolon, so the block after it always runs. publishNode() had one: the node
     * found by the assignment's remote ID was always replaced by findNode()'s.
     */
    public function testNoIfEndsInAnEmptyStatement()
    {
        $skip = array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT );
        $found = array();
        foreach ( array( 'kernel', 'lib' ) as $dir )
        {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $f )
            {
                if ( substr( $f->getFilename(), -4 ) !== '.php' )
                    continue;
                $tokens = @token_get_all( file_get_contents( $f->getPathname() ) );
                $n = count( $tokens );
                for ( $i = 0; $i < $n; ++$i )
                {
                    if ( !is_array( $tokens[$i] ) || ( $tokens[$i][0] !== T_IF && $tokens[$i][0] !== T_ELSEIF ) )
                        continue;
                    $j = $i + 1;
                    while ( $j < $n && is_array( $tokens[$j] ) && in_array( $tokens[$j][0], $skip, true ) )
                        ++$j;
                    if ( $j >= $n || $tokens[$j] !== '(' )
                        continue;
                    for ( $depth = 0; $j < $n; ++$j )
                    {
                        if ( $tokens[$j] === '(' )
                            ++$depth;
                        elseif ( $tokens[$j] === ')' && --$depth === 0 )
                            break;
                    }
                    ++$j;
                    while ( $j < $n && is_array( $tokens[$j] ) && in_array( $tokens[$j][0], $skip, true ) )
                        ++$j;
                    if ( $j < $n && $tokens[$j] === ';' )
                        $found[] = substr( $f->getPathname(), strlen( self::root() ) + 1 ) . ':' . $tokens[$i][2];
                }
            }
        }
        $this->assertSame( array(), $found, 'an if or elseif whose body is an empty statement' );
    }

    public function testPublishNodeTakesTheRemoteIdNodeOnlyWhenItIsThisObjectsNodeBelowTheParent()
    {
        $code = file_get_contents( self::root() . '/kernel/content/ezcontentoperationcollection.php' );
        $this->assertStringContainsString( "(int)\$existingNode->attribute( 'contentobject_id' ) !== (int)\$object->attribute( 'id' )", $code );
        $this->assertStringContainsString( "(int)\$existingNode->attribute( 'parent_node_id' ) !== (int)\$nodeID", $code );
    }

    public function testStorePathStoresTheAbsoluteURLsPathOnly()
    {
        $this->assertSame( '/news/item', eZURLAliasML::stripURLOrigin( 'https://www.example.com:8443/news/item?x=1#top' ) );
        $this->assertSame( '/news', eZURLAliasML::stripURLOrigin( 'HTTP://example.com/news' ) );
        $this->assertSame( '', eZURLAliasML::stripURLOrigin( 'https://www.example.com' ) );
        $this->assertSame( '', eZURLAliasML::stripURLOrigin( 'https://www.example.com?x=1' ) );
        // relative paths are what they were
        $this->assertSame( 'news/item', eZURLAliasML::stripURLOrigin( 'news/item' ) );
        $this->assertSame( '/news/item?x', eZURLAliasML::stripURLOrigin( '/news/item?x' ) );
        $this->assertSame( 'a:b/c', eZURLAliasML::stripURLOrigin( 'a:b/c' ) );
        $this->assertSame( '//cdn.example.com/x', eZURLAliasML::stripURLOrigin( '//cdn.example.com/x' ) );

        $code = file_get_contents( self::root() . '/kernel/classes/ezurlaliasml.php' );
        $this->assertMatchesRegularExpression( '/static function storePath\(.*?\{\s*(\/\/[^\n]*\s*)*\$pathOfURL = eZURLAliasML::cleanURL\( eZURLAliasML::stripURLOrigin\( \$path \) \);/s', $code );
    }

    public function testEzjscoreSubtreeHasAHardLimitThatCoversTheAdminPageSizes()
    {
        $ini = file_get_contents( self::root() . '/extension/ezjscore/settings/ezjscore.ini' );
        $this->assertSame( 1, preg_match( '/^\[ezjscServer_ezjscnode\]\n(?:[^\[].*\n|\n)*?HardLimit=(.*)$/m', $ini, $match ) );
        $this->assertMatchesRegularExpression( '/^[1-9][0-9]*$/', trim( $match[1] ), 'a positive number, not false' );
        $hardLimit = (int)$match[1];

        foreach ( array( 'design/admin', 'design/admin4' ) as $design )
        {
            $js = file_get_contents( self::root() . '/' . $design . '/javascript/ezajaxsubitems_expdatatable.js' );
            preg_match_all( '/\{ id: \d+, count: (\d+) \}/', $js, $counts );
            $this->assertNotEmpty( $counts[1], $design . ': the page sizes of the sub-items list' );
            $this->assertLessThanOrEqual( $hardLimit, max( array_map( 'intval', $counts[1] ) ), $design . ': a page the list offers is cut' );
        }
    }

    public function testShippedFileExtensionBlackListRefusesWhatAWebServerMayRun()
    {
        $ini = file_get_contents( self::root() . '/settings/file.ini' );
        preg_match_all( '/^FileExtensionBlackList\[\]=(.+)$/m', $ini, $match );
        $shipped = array_map( 'trim', $match[1] );
        foreach ( array( 'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps', 'phar', 'phpt', 'pht', 'phtml', 'pgif',
                         'shtml', 'cgi', 'pl', 'asp', 'aspx', 'jsp', 'htaccess' ) as $extension )
        {
            $this->assertContains( $extension, $shipped );
        }
        foreach ( $shipped as $extension )
        {
            $this->assertSame( strtolower( $extension ), $extension, 'the validator compares in lower case' );
        }

        $validator = new eZFileExtensionBlackListValidator();
        foreach ( array( 'shell.php5', 'shell.PHP7', 'page.shtml', '.htaccess', 'run.cgi' ) as $name )
        {
            $this->assertSame( eZInputValidator::STATE_INVALID, $validator->validate( $name ), $name );
        }
        foreach ( array( 'report.pdf', 'photo.jpg', 'notes.txt' ) as $name )
        {
            $this->assertSame( eZInputValidator::STATE_ACCEPTED, $validator->validate( $name ), $name );
        }
    }

    /**
     * Flash ended in 2020 and no browser runs it. The Online Editor's TinyMCE media plugin carried moxieplayer.swf as
     * the Flash fallback of an MP4 or FLV video; the file is gone and the plugin adds no Flash player unless a site
     * names one in flash_video_player_url.
     */
    public function testOnlineEditorShipsNoFlashPlayer()
    {
        $media = self::root() . '/extension/ezoe/design/standard/javascript/plugins/media';
        $this->assertFileDoesNotExist( $media . '/moxieplayer.swf' );
        foreach ( array( 'editor_plugin.js', 'editor_plugin_src.js' ) as $file )
        {
            $code = file_get_contents( $media . '/' . $file );
            $this->assertStringNotContainsString( 'moxieplayer.swf', $code, $file );
            $this->assertMatchesRegularExpression( '/getParam\(\s*["\']flash_video_player_url["\']\s*,\s*false\s*\)/', $code, $file );
        }

        $swf = array();
        foreach ( array( 'design', 'extension/ezoe' ) as $dir )
        {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $f )
                if ( strtolower( $f->getExtension() ) === 'swf' )
                    $swf[] = substr( $f->getPathname(), strlen( self::root() ) + 1 );
        }
        $this->assertSame( array(), $swf );
    }

    public function testRestKernelRegistersTheEventListeners()
    {
        $kernel = file_get_contents( self::root() . '/kernel/private/rest/classes/ezpkernelrest.php' );
        $this->assertStringContainsString( 'ezpEvent::getInstance()->registerEventListeners();', $kernel );
    }

    public function testFieldSetOfALanguageWithoutTranslationIsEmpty()
    {
        $set = new ezpContentFieldSet();
        $children = new ReflectionProperty( 'ezpContentFieldSet', 'childrenFieldSets' );
        $children->setValue( $set, array( 'ger-DE' => ezpContentFieldSet::fromDataMap( array() ) ) );

        $problems = array();
        set_error_handler( function ( $no, $message ) use ( &$problems ) { $problems[] = $message; return true; } );
        try
        {
            $set->setActiveLanguage( 'eng-GB' );
            $fields = iterator_to_array( $set );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( array(), $problems );
        $this->assertSame( array(), $fields );
    }

    public function testParameterSetDefaultsTheRootDirectory()
    {
        $section = 'URLTranslator';
        $parameter = 'MaxPathDepth';
        $this->assertTrue( eZINI::parameterSet( 'site.ini', null, $section, $parameter ) );
    }

    public function testClusterIndexWithoutClusterConfigurationAnswersWithoutError()
    {
        $dir = sys_get_temp_dir() . '/exp-cluster-' . getmypid() . '-' . mt_rand();
        mkdir( $dir );
        try
        {
            $out = self::runPhp( 'include ' . var_export( self::root() . '/index_cluster.php', true ) . ';', $dir );
        }
        finally
        {
            @rmdir( $dir );
        }
        // the answer meant for this case (an error page and eZFatalUserError), not PHP's Error about the constant
        $this->assertStringNotContainsString( 'Undefined constant', $out );
        $this->assertStringContainsString( 'An error has occured', $out );
    }

    public function testPdfDeniedObjectDoesNotNameTheObject()
    {
        $template = file_get_contents( self::root() . '/design/standard/templates/content/datatype/pdf/ezxmltags/object_denied.tpl' );
        $this->assertStringNotContainsString( '$object.name', $template );
        $this->assertStringContainsString( 'You do not have permission to view this object', $template );
    }

    public function testBenchmarkCliRunnerLoads()
    {
        $out = self::runPhp( 'require "autoload.php"; require "benchmarks/classes/ezbenchmarkrunner.php";'
                           . ' require "benchmarks/classes/ezbenchmarkclirunner.php"; echo "loaded";' );
        $this->assertSame( 'loaded', trim( $out ) );
    }
}
