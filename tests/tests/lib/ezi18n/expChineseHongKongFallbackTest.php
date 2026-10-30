<?php
/**
 * Hong Kong (chi-HK) has no catalogue of its own and is translated through the kernel's normal translation fallback
 * (i18n.ini [TranslationSettings] FallbackLanguages[chi-HK]=chi-TW), with the shipped settings and catalogues:
 *   - the fallback chain of chi-HK starts with chi-TW, the one of chi-SG with chi-CN
 *   - eZTSTranslator for chi-HK loads the Traditional (Taiwan) text, not the Simplified one, from the kernel's
 *     catalogues and from those of the bundled extensions
 *
 * DB-less; the translation cache is off and the shared translation tables are restored in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezi18n
 */

class expChineseHongKongFallbackTest extends PHPUnit\Framework\TestCase
{
    private $savedGlobals;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->savedGlobals = array();
        foreach ( array( 'eZTSTranslationTables', 'eZTranslatorManagerInstance' ) as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        unset( $GLOBALS['eZTSTranslationTables'], $GLOBALS['eZTranslatorManagerInstance'] );
        expI18nFallbackResolver::resetInstances();
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        expI18nFallbackResolver::resetInstances();
    }

    /**
     * The translation of a message as the catalogue file has it.
     */
    private static function fromFile( $file, $context, $source )
    {
        $doc = new DOMDocument();
        $doc->load( $file, LIBXML_NONET );
        $xpath = new DOMXPath( $doc );
        $query = '/TS/context[name="' . $context . '"]/message[source="' . $source . '"]/translation';
        $node = $xpath->query( $query )->item( 0 );
        return $node ? $node->textContent : null;
    }

    private static function translator( $locale, array $roots )
    {
        unset( $GLOBALS['eZTSTranslationTables'] );
        $translator = new eZTSTranslator( $locale, 'translation.ts', false );
        $translator->RootCache = array( 'roots' => $roots );
        return $translator;
    }

    public function testHongKongHasNoCatalogueOfItsOwn()
    {
        $this->assertFileDoesNotExist( 'share/translations/chi-HK/translation.ts' );
        $this->assertFileExists( 'share/translations/chi-TW/translation.ts' );
        $this->assertFileExists( 'share/locale/chi-HK.ini', 'the locale (dates, numbers, currency) stays' );
    }

    public function testTheChainOfHongKongStartsWithTaiwanAndSingaporeWithTheMainland()
    {
        $this->assertSame( 'chi-TW', eZTSTranslator::fallbackChain( 'chi-HK' )[0] );
        $this->assertSame( 'chi-CN', eZTSTranslator::fallbackChain( 'chi-SG' )[0] );
    }

    public function testHongKongLoadsTheTraditionalTextOfTheKernelCatalogue()
    {
        $context = 'design/admin/pagelayout';
        $source = 'Content structure';
        $taiwan = self::fromFile( 'share/translations/chi-TW/translation.ts', $context, $source );
        $mainland = self::fromFile( 'share/translations/chi-CN/translation.ts', $context, $source );
        $this->assertNotEmpty( $taiwan );
        $this->assertNotSame( $mainland, $taiwan, 'the two scripts differ for this message' );

        $translator = self::translator( 'chi-HK', array( 'share/translations' ) );
        $translator->load( $context );
        $this->assertSame( $taiwan, $translator->translate( $context, $source ) );
    }

    public function testHongKongLoadsTheTraditionalTextOfABundledExtension()
    {
        $file = 'extension/ezoe/translations/chi-TW/translation.ts';
        $this->assertFileDoesNotExist( 'extension/ezoe/translations/chi-HK/translation.ts' );
        $doc = new DOMDocument();
        $doc->load( $file, LIBXML_NONET );
        $xpath = new DOMXPath( $doc );
        $message = $xpath->query( '/TS/context/message[translation != "" and not(translation/@type)]' )->item( 0 );
        $context = $xpath->query( '../name', $message )->item( 0 )->textContent;
        $source = $xpath->query( 'source', $message )->item( 0 )->textContent;
        $comment = $xpath->query( 'comment', $message )->item( 0 );
        $expected = $xpath->query( 'translation', $message )->item( 0 )->textContent;

        $translator = self::translator( 'chi-HK', array( 'extension/ezoe/translations' ) );
        $translator->load( $context );
        $this->assertSame( $expected, $translator->translate( $context, $source, $comment ? $comment->textContent : null ) );
    }
}
