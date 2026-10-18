<?php
/**
 * eZTSTranslator with the translation fallbacks of expI18nFallbackResolver, in a private repository with an extension
 * root, cache off:
 *   - a locale without a file takes the first translation of its chain (tst-AR -> tst-MX -> tst-ES), for each root on
 *     its own: the kernel root has tst-MX, the extension root only tst-ES
 *   - FallbackMode=merge loads every file of the chain, so a message tst-MX lacks (or leaves empty) comes from tst-ES
 *   - the automatic same-language fallback, and AutomaticLanguageFallback=disabled
 *   - eng-GB as a fallback still means the source texts
 *
 * The settings are switched with eZINI::setVariable() and restored in tearDown(); expI18nFallbackResolver's shared
 * instances are reset around each test.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezi18n
 */

class eZTSTranslatorFallbackChainTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $saved = array();
    private $savedGlobals;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/phpunit-tstranslator-chain-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir . '/kernel', 0777, true );
        mkdir( $this->dir . '/extension/tstext/translations', 0777, true );

        $this->set( 'site.ini', 'RegionalSettings', 'TranslationRepository', $this->dir . '/kernel/' );
        $this->set( 'site.ini', 'RegionalSettings', 'TranslationExtensions', array() );
        $this->set( 'i18n.ini', 'TranslationSettings', 'FallbackLanguages', array( 'tst-AR' => 'tst-MX', 'tst-MX' => 'tst-ES', 'tst-GB' => 'eng-GB' ) );
        $this->set( 'i18n.ini', 'TranslationSettings', 'FallbackRules', array() );
        $this->set( 'i18n.ini', 'TranslationSettings', 'FallbackMode', 'first' );
        $this->set( 'i18n.ini', 'TranslationSettings', 'AutomaticLanguageFallback', 'enabled' );
        $this->set( 'i18n.ini', 'TranslationSettings', 'AutomaticFallbackExclude', array( 'eng' ) );
        $this->set( 'i18n.ini', 'TranslationSettings', 'FallbackMaxDepth', '4' );

        $this->savedGlobals = array();
        foreach ( array( 'eZTSTranslationTables', 'eZTranslatorManagerInstance' ) as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        unset( $GLOBALS['eZTSTranslationTables'], $GLOBALS['eZTranslatorManagerInstance'] );
        expI18nFallbackResolver::resetInstances();

        $this->ts( 'kernel/tst-ES', array( 'Hello' => 'Hola', 'Save' => 'Guardar', 'Close' => 'Cerrar' ) );
        $this->ts( 'kernel/tst-MX', array( 'Hello' => 'Qué onda', 'Save' => '' ) );
        $this->ts( 'extension/tstext/translations/tst-ES', array( 'Extension' => 'Extensión' ) );
        $this->ts( 'kernel/tst-DE', array( 'Hello' => 'Hallo' ) );
        $this->ts( 'kernel/untranslated', array( 'Hello' => '' ) );
    }

    protected function tearDown(): void
    {
        foreach ( array_reverse( $this->saved ) as $saved )
        {
            list( $file, $block, $name, $existed, $value ) = $saved;
            $ini = eZINI::instance( $file );
            if ( $existed )
                $ini->setVariable( $block, $name, $value );
            else
                $ini->removeSetting( $block, $name );
        }
        foreach ( $this->savedGlobals as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        expI18nFallbackResolver::resetInstances();
        $this->remove( $this->dir );
    }

    private function set( $file, $block, $name, $value )
    {
        $ini = eZINI::instance( $file );
        $existed = $ini->hasVariable( $block, $name );
        $this->saved[] = array( $file, $block, $name, $existed, $existed ? $ini->variable( $block, $name ) : null );
        $ini->setVariable( $block, $name, $value );
    }

    private function remove( $path )
    {
        if ( is_dir( $path ) )
        {
            foreach ( scandir( $path ) as $name )
            {
                if ( $name !== '.' && $name !== '..' )
                    $this->remove( $path . '/' . $name );
            }
            rmdir( $path );
        }
        elseif ( is_file( $path ) )
            unlink( $path );
    }

    private function ts( $dir, array $messages )
    {
        $xml = '';
        foreach ( $messages as $source => $translation )
            $xml .= '<message><source>' . $source . '</source><translation>' . $translation . "</translation></message>\n";
        mkdir( $this->dir . '/' . $dir, 0777, true );
        file_put_contents( $this->dir . '/' . $dir . '/translation.ts',
                           "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<!DOCTYPE TS>\n<TS version=\"2.0\">\n<context><name>test</name>\n$xml</context>\n</TS>\n" );
    }

    private function translator( $locale, $withExtension = false )
    {
        $translator = new eZTSTranslator( $locale, 'translation.ts', false );
        // the roots as loadTranslationFile() builds them: the repository, then each extension's translations
        $roots = array( $this->dir . '/kernel/' );
        if ( $withExtension )
            $roots[] = $this->dir . '/extension/tstext/translations';
        $translator->RootCache = array( 'roots' => $roots );
        $translator->load( 'test' );
        return $translator;
    }

    public function testALocaleWithoutAFileTakesTheFirstTranslationOfItsChain()
    {
        $this->assertSame( array( 'tst-MX', 'tst-ES' ), eZTSTranslator::fallbackChain( 'tst-AR' ) );
        $t = $this->translator( 'tst-AR' );
        $this->assertSame( 'Qué onda', $t->translate( 'test', 'Hello' ), 'tst-MX, the first of the chain' );
        $this->assertSame( 'Save', $t->translate( 'test', 'Save' ), 'first: what tst-MX leaves empty is the source text' );
        $this->assertNull( $t->translate( 'test', 'Close' ), 'first: tst-ES is not loaded' );
    }

    public function testEachRootTakesTheFirstFileOfTheChainItHas()
    {
        $t = $this->translator( 'tst-AR', true );
        $this->assertSame( 'Qué onda', $t->translate( 'test', 'Hello' ) );
        $this->assertSame( 'Extensión', $t->translate( 'test', 'Extension' ), 'the extension has only tst-ES' );
    }

    public function testMergeFillsTheGapsFromTheNextTranslation()
    {
        $this->set( 'i18n.ini', 'TranslationSettings', 'FallbackMode', 'merge' );
        expI18nFallbackResolver::resetInstances();
        $this->assertSame( 'merge', eZTSTranslator::fallbackMode() );
        $t = $this->translator( 'tst-AR' );
        $this->assertSame( 'Qué onda', $t->translate( 'test', 'Hello' ), 'the most specific translation wins' );
        $this->assertSame( 'Guardar', $t->translate( 'test', 'Save' ), 'an empty translation keeps the one of tst-ES' );
        $this->assertSame( 'Cerrar', $t->translate( 'test', 'Close' ), 'a message tst-MX lacks comes from tst-ES' );
    }

    public function testTheAutomaticFallbackTakesTheMostCompleteTranslationOfTheLanguage()
    {
        $this->assertSame( array( 'tst-ES' ), eZTSTranslator::fallbackChain( 'tst-CL' ), 'tst-ES translates the most' );
        $this->assertSame( 'Hola', $this->translator( 'tst-CL' )->translate( 'test', 'Hello' ) );
        $this->set( 'i18n.ini', 'TranslationSettings', 'AutomaticLanguageFallback', 'disabled' );
        expI18nFallbackResolver::resetInstances();
        unset( $GLOBALS['eZTSTranslationTables'] );
        $this->assertSame( array(), eZTSTranslator::fallbackChain( 'tst-CL' ) );
        $this->assertNull( $this->translator( 'tst-CL' )->translate( 'test', 'Hello' ) );
    }

    public function testEngGbMeansTheSourceTexts()
    {
        $this->assertSame( array( 'untranslated' ), eZTSTranslator::fallbackChain( 'tst-GB' ) );
        $this->assertSame( 'Hello', $this->translator( 'tst-GB' )->translate( 'test', 'Hello' ) );
    }
}
