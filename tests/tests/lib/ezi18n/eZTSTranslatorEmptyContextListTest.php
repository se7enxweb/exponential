<?php
/**
 * A locale without a translation catalogue caches an empty list of contexts ('cachecontexts' => array()). The
 * translator stores that list only when none is cached yet: with a loose comparison (array() == null is true) it
 * stored it again for every untranslated message, and eZTranslationCache::setContextCache() wrote the warning
 * "Translation cache for context 'cachecontexts' already exists" each time, about a hundred per admin page.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezi18n/eZTSTranslatorEmptyContextListTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZTSTranslatorEmptyContextListTest extends PHPUnit\Framework\TestCase
{
    public function testTheContextListIsStoredOnlyWhenNoneIsCached()
    {
        $code = file_get_contents( dirname( __DIR__, 4 ) . '/lib/ezi18n/classes/eztstranslator.php' );
        $this->assertStringContainsString( "if ( eZTranslationCache::contextCache( 'cachecontexts' ) === null )", $code );
        $this->assertStringNotContainsString( "eZTranslationCache::contextCache( 'cachecontexts' ) == null", $code );
    }

    public function testAnEmptyCachedListIsNotNull()
    {
        $saved = isset( $GLOBALS['eZTranslationCacheTable'] ) ? $GLOBALS['eZTranslationCacheTable'] : null;
        try
        {
            $GLOBALS['eZTranslationCacheTable'] = array();
            eZTranslationCache::setContextCache( 'cachecontexts', array() );
            $cached = eZTranslationCache::contextCache( 'cachecontexts' );
            $this->assertSame( array(), $cached, 'an empty list is cached as an empty list' );
            $this->assertFalse( $cached === null, 'so the translator does not store it again' );
        }
        finally
        {
            if ( $saved === null )
                unset( $GLOBALS['eZTranslationCacheTable'] );
            else
                $GLOBALS['eZTranslationCacheTable'] = $saved;
        }
    }
}
