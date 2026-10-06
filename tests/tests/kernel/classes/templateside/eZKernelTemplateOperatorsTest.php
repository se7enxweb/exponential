<?php
/**
 * The kernel's template operators of kernel/common that need neither a database nor a request, rendered by
 * eZTemplate from string templates, each case four ways: by the interpreter and by the compiler, and with its
 * input written as a literal (the compiler can work it out when compiling) and passed in as a variable (the
 * compiled template works it out at run time). All four must give the expected text:
 *
 *   - eZURLOperator: ezurl, ezroot (relative and absolute addresses, quotes), ezsys, ezdesign and ezimage of files
 *     that exist and do not, ezini and ezini_hasvariable, ezhttp and ezhttp_hasvariable for post, get and cookie
 *   - eZi18nOperator: i18n, x18n and d18n with arguments by position and by name
 *   - eZModuleParamsOperator, eZModuleOperator (unknown modules), expEnvironmentOperator, ezpSiteAccessURLOperator
 *
 * Template files and compiled templates go to a private directory under var/tmp, removed after the class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group eztemplate
 */

require_once dirname( __DIR__ ) . '/datatypes/eZDatatypeTestFixtures.php';

class eZKernelTemplateOperatorsTest extends eZDatatypeTestCase
{
    private static $dir;
    private $savedGlobals = array();
    private $savedGet;
    private $savedCookie;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        chdir( dirname( __DIR__, 5 ) );
        self::$dir = 'var/tmp/phpunit-k1d-kernel-operators-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
    }

    protected function setUp(): void
    {
        parent::setUp();
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics', 'eZRequestedModuleParams' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $this->savedGet = $_GET;
        $this->savedCookie = $_COOKIE;
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
        $_GET = $this->savedGet;
        $_COOKIE = $this->savedCookie;
        parent::tearDown();
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
            rmdir( $path );
        }
        else
            unlink( $path );
    }

    /**
     * @return array( output, errors, warnings )
     */
    private function render( $source, array $variables, $compiled )
    {
        if ( $compiled )
        {
            unset( $GLOBALS['eZSiteBasics'] );
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = true;
            $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = self::$dir . '/compiled';
        }
        else
        {
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
            $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        }
        $file = self::$dir . '/' . md5( $source ) . '.tpl';
        if ( !file_exists( $file ) )
            file_put_contents( $file, $source );

        $tpl = new eZTemplate();
        $tpl->setAutoloadPathList( array( 'lib/eztemplate/classes/', 'kernel/common/' ) );
        $tpl->autoload();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $file );
        return array( $output, $tpl->errorLog(), $tpl->warningLog() );
    }

    /**
     * Runs $callback and returns the PHP warnings and notices it raised.
     */
    private function phpWarnings( $callback )
    {
        $warnings = array();
        set_error_handler( function ( $level, $message ) use ( &$warnings ) {
            if ( $level & ( E_WARNING | E_NOTICE | E_DEPRECATED ) )
                $warnings[] = $message;
            return true;
        } );
        try
        {
            $callback();
        }
        finally
        {
            restore_error_handler();
        }
        return $warnings;
    }

    private function assertFourWays( $literal, $variable, array $variables, $expected, $message = '' )
    {
        foreach ( array( false, true ) as $compiled )
        {
            foreach ( array( 'literal' => $literal, 'variable' => $variable ) as $form => $source )
            {
                if ( $source === null )
                    continue;
                list( $output ) = $this->render( $source, $form === 'variable' ? $variables : array(), $compiled );
                $this->assertSame( $expected, $output, ( $compiled ? 'compiled' : 'interpreted' ) . ", $form: $source $message" );
            }
        }
    }

    private static function url( $url, $root = false, $server = 'relative' )
    {
        eZURI::transformURI( $url, $root, $server );
        return $url;
    }

    // ---------------------------------------------------------------- ezurl, ezroot

    public function testEzurlQuotesAndTransforms()
    {
        $this->assertFourWays( '{"content/view/full/2"|ezurl}', '{$u|ezurl}', array( 'u' => 'content/view/full/2' ), '"' . self::url( 'content/view/full/2' ) . '"' );
        $this->assertFourWays( "{'a/b'|ezurl(single)}", '{$u|ezurl(single)}', array( 'u' => 'a/b' ), "'" . self::url( 'a/b' ) . "'" );
        $this->assertFourWays( "{'a/b'|ezurl(no)}", '{$u|ezurl(no)}', array( 'u' => 'a/b' ), self::url( 'a/b' ) );
        $this->assertFourWays( "{'a/b'|ezurl(no, full)}", '{$u|ezurl(no, full)}', array( 'u' => 'a/b' ), self::url( 'a/b', false, 'full' ) );
        $this->assertFourWays( "{'a/b'|ezurl('no')}", '{$u|ezurl($q)}', array( 'u' => 'a/b', 'q' => 'no' ), self::url( 'a/b' ) );
        $this->assertFourWays( "{'a/b'|ezurl('single')}", '{$u|ezurl($q)}', array( 'u' => 'a/b', 'q' => 'single' ), "'" . self::url( 'a/b' ) . "'" );
    }

    public function testEzurlOfAbsoluteAddressesIsLeftAlone()
    {
        $this->assertFourWays( "{'https://k1d.example.invalid/x'|ezurl(no)}", '{$u|ezurl(no)}', array( 'u' => 'https://k1d.example.invalid/x' ), 'https://k1d.example.invalid/x' );
        $this->assertFourWays( "{'mailto:a@k1d.example.invalid'|ezurl(no)}", '{$u|ezurl(no)}', array( 'u' => 'mailto:a@k1d.example.invalid' ), 'mailto:a@k1d.example.invalid' );
    }

    public function testEzrootAddsTheSlash()
    {
        $this->assertFourWays( "{'design/x.css'|ezroot(no)}", '{$u|ezroot(no)}', array( 'u' => 'design/x.css' ), self::url( '/design/x.css', true ) );
        $this->assertFourWays( "{'/var/a.png'|ezroot}", '{$u|ezroot}', array( 'u' => '/var/a.png' ), '"' . self::url( '/var/a.png', true ) . '"' );
        $this->assertFourWays( "{''|ezroot(no)}", '{$u|ezroot(no)}', array( 'u' => '' ), self::url( '', true ) );
    }

    // ---------------------------------------------------------------- ezsys, ezdesign, ezimage

    public function testEzsys()
    {
        $sys = eZSys::instance();
        foreach ( array( 'wwwdir', 'indexfile', 'sitedir' ) as $attribute )
            $this->assertFourWays( "{ezsys('$attribute')}", '{ezsys($a)}', array( 'a' => $attribute ), (string)$sys->attribute( $attribute ), $attribute );
        list( $output, , $warnings ) = $this->render( "{ezsys('nosuchattribute')}", array(), false );
        $this->assertSame( '', $output );
        $this->assertNotEmpty( $warnings );
    }

    public function testEzdesignAndEzimageOfMissingFilesPointIntoTheSiteDesign()
    {
        $site = eZTemplateDesignResource::designSetting( 'site' );
        $www = eZSys::instance()->wwwDir();
        $this->assertFourWays( "{'k1d/no-such.css'|ezdesign(no)}", '{$f|ezdesign(no)}', array( 'f' => 'k1d/no-such.css' ), "$www/design/$site/k1d/no-such.css" );
        $this->assertFourWays( "{'k1d-no-such.png'|ezimage(no)}", '{$f|ezimage(no)}', array( 'f' => 'k1d-no-such.png' ), "$www/design/$site/images/k1d-no-such.png" );
        list( , , $warnings ) = $this->render( "{'k1d/no-such.css'|ezdesign}", array(), false );
        $this->assertNotEmpty( $warnings );
    }

    public function testDesignFilesOutsideASiteAccess()
    {
        // a script without a siteaccess (a cronjob rendering a mail, for example), with the design location cache
        $saved = array_key_exists( 'eZCurrentAccess', $GLOBALS ) ? array( $GLOBALS['eZCurrentAccess'] ) : null;
        $savedBases = array_key_exists( 'eZTemplateDesignResourceBases', $GLOBALS ) ? array( $GLOBALS['eZTemplateDesignResourceBases'] ) : null;
        $ini = eZINI::instance();
        $savedCache = $ini->variable( 'DesignSettings', 'DesignLocationCache' );
        unset( $GLOBALS['eZCurrentAccess'], $GLOBALS['eZTemplateDesignResourceBases'] );
        $ini->setVariable( 'DesignSettings', 'DesignLocationCache', 'enabled' );
        $cacheFile = eZSys::cacheDirectory() . '/' . eZTemplateDesignResource::DESIGN_BASE_CACHE_NAME . md5( '' ) . '.php';
        $cacheExisted = file_exists( $cacheFile );
        try
        {
            $warnings = $this->phpWarnings( function () use ( &$bases ) {
                $bases = eZTemplateDesignResource::allDesignBases();
            } );
        }
        finally
        {
            $ini->setVariable( 'DesignSettings', 'DesignLocationCache', $savedCache );
            if ( !$cacheExisted && file_exists( $cacheFile ) )
                eZClusterFileHandler::instance( $cacheFile )->delete();
            if ( $saved !== null )
                $GLOBALS['eZCurrentAccess'] = $saved[0];
            if ( $savedBases !== null )
                $GLOBALS['eZTemplateDesignResourceBases'] = $savedBases[0];
            else
                unset( $GLOBALS['eZTemplateDesignResourceBases'] );
        }
        $this->assertSame( array(), $warnings );
        $this->assertNotEmpty( $bases );
    }

    public function testEzdesignOfAnExistingFile()
    {
        $bases = eZTemplateDesignResource::allDesignBases();
        $tried = array();
        $match = eZTemplateDesignResource::fileMatch( $bases, false, 'templates/pagelayout.tpl', $tried );
        $this->assertNotEmpty( $match, 'the standard design has a page layout' );
        $expected = htmlspecialchars( eZSys::instance()->wwwDir() . '/' . $match['path'] );
        $this->assertFourWays( "{'templates/pagelayout.tpl'|ezdesign(no)}", '{$f|ezdesign(no)}', array( 'f' => 'templates/pagelayout.tpl' ), $expected );
        $this->assertFourWays( "{'templates/pagelayout.tpl'|ezdesign}", '{$f|ezdesign}', array( 'f' => 'templates/pagelayout.tpl' ), '"' . $expected . '"' );
    }

    // ---------------------------------------------------------------- ezini

    public function testEzini()
    {
        $value = eZINI::instance()->variable( 'RegionalSettings', 'Locale' );
        $this->assertFourWays( "{ezini('RegionalSettings','Locale')}", '{ezini($g,$v)}', array( 'g' => 'RegionalSettings', 'v' => 'Locale' ), $value );
        $value = eZINI::instance( 'content.ini' )->variable( 'header', 'UseStrictHeaderRule' );
        $this->assertFourWays( "{ezini('header','UseStrictHeaderRule','content.ini')}", '{ezini($g,$v,$f)}', array( 'g' => 'header', 'v' => 'UseStrictHeaderRule', 'f' => 'content.ini' ), $value );
    }

    public function testEziniHasVariable()
    {
        $this->assertFourWays( "{if ezini_hasvariable('RegionalSettings','Locale')}y{else}n{/if}", '{if ezini_hasvariable($g,$v)}y{else}n{/if}', array( 'g' => 'RegionalSettings', 'v' => 'Locale' ), 'y' );
        $this->assertFourWays( "{if ezini_hasvariable('RegionalSettings','K1dNoSuch')}y{else}n{/if}", '{if ezini_hasvariable($g,$v)}y{else}n{/if}', array( 'g' => 'RegionalSettings', 'v' => 'K1dNoSuch' ), 'n' );
        $this->assertFourWays( "{if ezini('RegionalSettings','K1dNoSuch','site.ini',false(),false(),true())}y{else}n{/if}", null, array(), 'n' );
        $this->assertFourWays( "{if ezini('RegionalSettings','Locale','site.ini',false(),false(),'hasVariable')}y{else}n{/if}", null, array(), 'y' );
    }

    public function testEziniOfAMissingVariableIsAnError()
    {
        list( $output, $errors ) = $this->render( "{ezini('RegionalSettings','K1dNoSuch')}", array(), false );
        $this->assertSame( '', $output );
        $this->assertNotEmpty( $errors );
        list( , $errors ) = $this->render( "{ezini('RegionalSettings')}", array(), false );
        $this->assertNotEmpty( $errors );
    }

    public function testEziniDynamicReadsAtRunTime()
    {
        $value = eZINI::instance()->variable( 'RegionalSettings', 'Locale' );
        list( $output ) = $this->render( "{ezini('RegionalSettings','Locale','site.ini',false(),true())}", array(), true );
        $this->assertSame( $value, $output );
        list( $output ) = $this->render( "{if ezini('RegionalSettings','K1dNoSuch','site.ini',false(),true(),true())}y{else}n{/if}", array(), true );
        $this->assertSame( 'n', $output );
    }

    // ---------------------------------------------------------------- ezhttp

    public function testEzhttpReadsPostGetAndCookie()
    {
        $_POST = array( 'k1d_post' => 'from post' );
        $_GET = array( 'k1d_get' => 'from get' );
        $_COOKIE = array( 'k1d_cookie' => 'from cookie' );
        $this->assertFourWays( "{ezhttp('k1d_post')}", '{ezhttp($n)}', array( 'n' => 'k1d_post' ), 'from post' );
        $this->assertFourWays( "{ezhttp('k1d_post','post')}", '{ezhttp($n,$t)}', array( 'n' => 'k1d_post', 't' => 'post' ), 'from post' );
        $this->assertFourWays( "{ezhttp('k1d_get','GET')}", '{ezhttp($n,$t)}', array( 'n' => 'k1d_get', 't' => 'get' ), 'from get' );
        $this->assertFourWays( "{ezhttp('k1d_cookie','cookie')}", '{ezhttp($n,$t)}', array( 'n' => 'k1d_cookie', 't' => 'cookie' ), 'from cookie' );
        $this->assertFourWays( "{if ezhttp_hasvariable('k1d_get','get')}y{else}n{/if}", '{if ezhttp_hasvariable($n,$t)}y{else}n{/if}', array( 'n' => 'k1d_get', 't' => 'get' ), 'y' );
        $this->assertFourWays( "{if ezhttp_hasvariable('k1d_none','get')}y{else}n{/if}", '{if ezhttp_hasvariable($n,$t)}y{else}n{/if}', array( 'n' => 'k1d_none', 't' => 'get' ), 'n' );
        $this->assertFourWays( "{if ezhttp_hasvariable('k1d_none','cookie')}y{else}n{/if}", null, array(), 'n' );
        $this->assertFourWays( "{if ezhttp_hasvariable('k1d_none')}y{else}n{/if}", null, array(), 'n' );
        $this->assertFourWays( "{if ezhttp('k1d_post','post',true())}y{else}n{/if}", null, array(), 'y' );
        $this->assertFourWays( "{if ezhttp('k1d_none','post','hasVariable')}y{else}n{/if}", null, array(), 'n' );
    }

    public function testEzhttpErrorsAndUnknownTypes()
    {
        $_POST = array( 'k1d_post' => 'p' );
        foreach ( array( "{ezhttp('k1d_none')}", "{ezhttp('k1d_none','get')}", "{ezhttp('k1d_none','cookie')}" ) as $source )
        {
            list( $output, $errors ) = $this->render( $source, array(), false );
            $this->assertSame( '', $output, $source );
            $this->assertNotEmpty( $errors, $source );
        }
        list( $output, , $warnings ) = $this->render( "{ezhttp('k1d_post','header')}", array(), false );
        $this->assertSame( 'p', $output, 'an unknown type falls back to post' );
        $this->assertNotEmpty( $warnings );
        list( $output ) = $this->render( "{ezhttp()|get_class}", array(), false );
        $this->assertSame( 'ezhttptool', strtolower( $output ) );
    }

    // ---------------------------------------------------------------- i18n

    public function testI18n()
    {
        $this->assertFourWays( "{'Hello'|i18n('k1d/test')}", '{$t|d18n($c)}', array( 't' => 'Hello', 'c' => 'k1d/test' ), 'Hello' );
        $this->assertFourWays( "{'Hello %1 and %2'|i18n('k1d/test',,array('Ada','Bob'))}", "{'Hello %1 and %2'|i18n('k1d/test',,\$a)}", array( 'a' => array( 'Ada', 'Bob' ) ), 'Hello Ada and Bob' );
        $this->assertFourWays( "{'Hi %name'|i18n('k1d/test','',hash('%name','Ada'))}", "{'Hi %name'|i18n('k1d/test','',\$a)}", array( 'a' => array( '%name' => 'Ada' ) ), 'Hi Ada' );
        $this->assertFourWays( "{'Hello'|x18n('k1dext','k1d/test')}", '{$t|x18n($e,$c)}', array( 't' => 'Hello', 'e' => 'k1dext', 'c' => 'k1d/test' ), 'Hello' );
    }

    // ---------------------------------------------------------------- module_params, ezmodule, environment, siteaccess_url

    public function testModuleParams()
    {
        $GLOBALS['eZRequestedModuleParams'] = array( 'module_name' => 'content', 'function_name' => 'view' );
        $this->assertFourWays( '{module_params().module_name}/{module_params().function_name}', null, array(), 'content/view' );
    }

    public function testModuleParamsOutsideAModuleView()
    {
        unset( $GLOBALS['eZRequestedModuleParams'] );
        $warnings = $this->phpWarnings( function () use ( &$output, &$errors ) {
            list( $output, $errors ) = $this->render( '{if module_params()}y{else}n{/if}', array(), false );
        } );
        $this->assertSame( 'n', $output );
        $this->assertSame( array(), $errors );
        $this->assertSame( array(), $warnings );
    }

    public function testEzmoduleOfAnUnknownModuleIsFalse()
    {
        $this->assertFourWays( "{if 'k1dnosuchmodule/view'|ezmodule}y{else}n{/if}", '{if $u|ezmodule}y{else}n{/if}', array( 'u' => 'k1dnosuchmodule/view' ), 'n' );
        $this->assertFourWays( "{if ''|ezmodule}y{else}n{/if}", null, array(), 'n' );
    }

    public function testEnvironment()
    {
        $environment = eZINI::environment();
        $this->assertFourWays( '{exp_environment()}', null, array(), $environment === false ? '' : (string)$environment );
    }

    public function testSiteAccessUrlIsWorkedOutAtEveryRender()
    {
        $expected = (string)ezpSiteAccessURL::root();
        $this->assertFourWays( '{siteaccess_url()}', null, array(), $expected );
        $compiledSource = '';
        $this->render( '{siteaccess_url()}', array(), true );
        foreach ( glob( self::$dir . '/compiled/*.php' ) as $file )
            $compiledSource .= file_get_contents( $file );
        $this->assertStringContainsString( 'ezpSiteAccessURL::root()', $compiledSource );
    }
}
