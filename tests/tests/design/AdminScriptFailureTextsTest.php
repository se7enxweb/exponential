<?php
/**
 * The texts the admin scripts show when a request fails are in the templates of both designs and translated:
 *
 *  SF-01 - ezajaxrelations_jquery.js reads #ezobjectrelation-search-failed-text (else it shows "HTTP <status>"),
 *          ezajaxsubitems_expdatatable.js reads TABLE_OPTIONS.save_failed and fills in %error
 *  SF-02 - ger-DE carries both texts
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminScriptFailureTextsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminScriptFailureTextsTest extends PHPUnit\Framework\TestCase
{
    private static function file( $path )
    {
        $file = dirname( __DIR__, 3 ) . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    /** The translation of $source in $context, null where the catalogue lacks it, '' where it is unfinished */
    private static function translation( $path, $context, $source )
    {
        $xml = simplexml_load_string( self::file( $path ) );
        self::assertNotFalse( $xml, "$path is valid XML" );
        foreach ( $xml->context as $ctx )
        {
            if ( (string)$ctx->name !== $context )
                continue;
            foreach ( $ctx->message as $message )
                if ( (string)$message->source === $source )
                    return (string)$message->translation['type'] === 'unfinished' ? '' : (string)$message->translation;
        }
        return null;
    }

    /** SF-01 */
    public function testFailureTextsTheAdminScriptsReadAreInTheTemplates()
    {
        foreach ( array( 'design/admin', 'design/admin4' ) as $design )
        {
            $this->assertStringContainsString( 'ezobjectrelation-search-failed-text', self::file( "$design/javascript/ezajaxrelations_jquery.js" ), $design );
            $this->assertMatchesRegularExpression(
                "#<p id=\"ezobjectrelation-search-failed-text\" class=\"hide\">\{'The search failed\. Try again\.'\|i18n\( 'design/standard/content/search' \)\|wash\}</p>#",
                self::file( "$design/templates/content/datatype/edit/ezobjectrelation_ajax_search.tpl" ), $design );
            $this->assertStringContainsString( 'O3.save_failed', self::file( "$design/javascript/ezajaxsubitems_expdatatable.js" ), $design );
            // admin: the inline label script; admin4: the data-labels hash (no inline script, Content Security Policy)
            $this->assertMatchesRegularExpression( $design === 'design/admin4'
                ? "#'save_failed', 'Not saved: %error'\|i18n\( 'design/admin/node/view/full' \)#"
                : "#save_failed: \"\{'Not saved: %error'\|i18n\( 'design/admin/node/view/full' \)\|wash\('javascript'\)\}\"#",
                self::file( "$design/templates/children_detailed.tpl" ), $design );
        }
    }

    /** SF-02 */
    public function testFailureTextsAreTranslated()
    {
        $ger = 'share/translations/ger-DE/translation.ts';
        $this->assertSame( 'Die Suche ist fehlgeschlagen. Bitte erneut versuchen.', self::translation( $ger, 'design/standard/content/search', 'The search failed. Try again.' ) );
        $this->assertSame( 'Nicht gespeichert: %error', self::translation( $ger, 'design/admin/node/view/full', 'Not saved: %error' ) );
    }
}
