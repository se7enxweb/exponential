<?php
/**
 * The small page scripts of admin4 that run on plain JavaScript: none of them uses jQuery again, so they keep working
 * on a page that loads no jQuery, and each one still binds what its template renders.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminPageScriptsWithoutJQueryTest.php
 *
 * @copyright Copyright (C) 2026 CJW Network and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminPageScriptsWithoutJQueryTest extends PHPUnit\Framework\TestCase
{
    /** Page script => an id or class it binds, which must still be in the code */
    const SCRIPTS = array(
        'node_details.js' => 'tab-details-set-states',
        'node_ordering.js' => 'ezasi-sort-set',
        'node_locations.js' => 'tab-locations-list-set-main',
        'translations_main.js' => 'tab-translations-list-set-main',
        'translations_always_available.js' => 'tab-translations-alwaysavailable-btn',
        'content_edit_attribute.js' => 'ezcca-collapsed',
        'content_edit_languages.js' => 'ezcoeditlanguages-sourcets',
        'user_register.js' => 'PublishButton',
        'content_urlalias.js' => 'aliasform',
        'template_look_edit.js' => 'editform',
    );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** The source without its comments, so a comment cannot satisfy or fail an assertion on code */
    private static function code( $name )
    {
        $source = AdminFiles::read( self::root() . '/design/admin4/javascript/page/' . $name );
        return preg_replace( array( '#/\*.*?\*/#s', '#(?<![:\'"\\\\])//[^\n]*#' ), '', $source );
    }

    public static function scripts()
    {
        $cases = array();
        foreach ( self::SCRIPTS as $name => $bound )
            $cases[$name] = array( $name, $bound );
        return $cases;
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'scripts' )]
    public function testThePageScriptUsesNoJQuery( $name, $bound )
    {
        $code = self::code( $name );
        $this->assertDoesNotMatchRegularExpression( '/\bjQuery\b/', $code, "$name names jQuery" );
        $this->assertDoesNotMatchRegularExpression( '/(?<![\w.$])\$\s*[(.]/', $code, "$name calls \$() or \$.something" );
        $this->assertDoesNotMatchRegularExpression( '/\b(?:window|Exp)\.\$/', $code, "$name reaches jQuery through window.\$ or Exp.\$" );
        $this->assertStringContainsString( $bound, $code, "$name no longer binds $bound" );
    }

    public function testTheClassicAdministrationLoadsTheSameRegistrationScript()
    {
        $this->assertFileEquals( self::root() . '/design/admin4/javascript/page/user_register.js',
                                 self::root() . '/design/admin/javascript/page/user_register.js' );
    }
}
