<?php
/**
 * The views added in 6.0.15 that read their filters from the query string ($_GET) take view parameters instead:
 * user/audit, user/hygiene, user/security, setup/reauth, and the JSON endpoint the module code generator writes
 * (expModuleTableCode::jsonView()). A filter form is posted (FilterButton) and answered with a 303 to /(name)/value.
 * Reads the files; no database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class ViewsTakeViewParametersTest extends PHPUnit\Framework\TestCase
{
    private static function read( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 5 ) . '/' . $path );
    }

    public static function views()
    {
        return array(
            array( 'kernel/private/classes/views/user/audit.php' ),
            array( 'kernel/private/classes/views/user/hygiene.php' ),
            array( 'kernel/private/classes/views/user/security.php' ),
            array( 'kernel/setup/reauth.php' ),
        );
    }

    /**
     * @dataProvider views
     */
    #[\PHPUnit\Framework\Attributes\DataProvider( 'views' )]
    public function testNoQueryString( $path )
    {
        $code = self::read( $path );
        $this->assertDoesNotMatchRegularExpression( '/\$_(GET|REQUEST)\b/', $code, $path );
    }

    public function testFilterFormsArePosted()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            foreach ( array( 'user/audit.tpl', 'user/hygiene.tpl', 'user/security.tpl', 'setup/reauth.tpl' ) as $file )
            {
                $tpl = self::read( "design/$design/templates/$file" );
                $this->assertStringNotContainsString( 'method="get"', $tpl, "$design $file" );
                $this->assertDoesNotMatchRegularExpression( "/'\\?[a-z]+=/", $tpl, "$design $file: a link with a query string" );
            }
        }
        $this->assertStringNotContainsString( "'?filter=", self::read( 'design/standard/templates/user/security.tpl' ) );
        foreach ( array( 'kernel/private/classes/views/user/audit.php', 'kernel/private/classes/views/user/hygiene.php' ) as $view )
            $this->assertStringContainsString( "\\expViewParameters::redirect( 'user/", self::read( $view ), $view );
    }

    public function testGeneratedJsonEndpoint()
    {
        $code = expModuleTableCode::jsonView( array( 'class' => 'demoRow', 'module' => 'demo', 'table' => 'demo_row' ) );
        $this->assertDoesNotMatchRegularExpression( '/\$_(GET|REQUEST)\b/', $code );
        $this->assertStringContainsString( "expViewParameters::get( \$Params, 'offset' )", $code );
        $this->assertStringContainsString( '/(offset)/0/(limit)/50', $code );
    }
}
