<?php
/**
 * The template operators rawurlencode and rawurldecode (template.ini [PHP] PHPOperatorList): RFC 3986
 * encoding, where a space is %20 and not + as urlencode gives. Checked in the shipped settings, through the
 * PHP operator class and in a template rendered by eZTemplate. No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/common/eZTemplateRawURLEncodeOperatorTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group templateoperators
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 627004e5c4), Copyright (C) Mugo Web, GPL v2
 */

class eZTemplateRawURLEncodeOperatorTest extends PHPUnit\Framework\TestCase
{
    const VALUE = 'a b+c/ä?x=1&y=~';

    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/rawurlencode_operator_' . uniqid( '', true );
        mkdir( $this->dir, 0700, true );
    }

    protected function tearDown(): void
    {
        foreach ( glob( $this->dir . '/*' ) ?: array() as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    public function testTheShippedSettingsListBothOperators()
    {
        $ini = new eZINI( 'template.ini', 'settings', null, false, null, true );
        $list = $ini->variable( 'PHP', 'PHPOperatorList' );
        $this->assertSame( 'rawurlencode', $list['rawurlencode'] );
        $this->assertSame( 'rawurldecode', $list['rawurldecode'] );
        $this->assertSame( 'urlencode', $list['urlencode'], 'the existing entries stay' );
        $this->assertSame( 'urldecode', $list['urldecode'] );
    }

    public function testThePHPOperatorEncodesAndDecodes()
    {
        $operator = new eZTemplatePHPOperator( array( 'rawurlencode' => 'rawurlencode', 'rawurldecode' => 'rawurldecode' ) );
        $this->assertContains( 'rawurlencode', $operator->operatorList() );

        $value = self::VALUE;
        $operator->modify( null, 'rawurlencode', array(), '', '', $value, array(), array() );
        $this->assertSame( 'a%20b%2Bc%2F%C3%A4%3Fx%3D1%26y%3D~', $value );

        $operator->modify( null, 'rawurldecode', array(), '', '', $value, array(), array() );
        $this->assertSame( self::VALUE, $value );
    }

    public function testATemplateRendersBothOperators()
    {
        $file = $this->dir . '/raw.tpl';
        file_put_contents( $file, '{$value|rawurlencode}|{$value|urlencode}|{$encoded|rawurldecode}' );

        // The process may hold template.ini from var/cache/ini, built before the entries were added (config.php
        // turns the INI file time check off), and the list is read only (site.ini [eZINISettings]
        // ReadonlySettingList), so it can not be set in-process: a new template instance registers the list of
        // the files themselves, as eZPHPOperatorInit() does with the cached one.
        $fresh = new eZINI( 'template.ini', 'settings', null, false );
        $list = $fresh->variable( 'PHP', 'PHPOperatorList' );
        eZTemplate::resetInstance();
        try
        {
            $tpl = eZTemplate::factory();
            $tpl->registerAutoloadOperators( array( 'class' => 'eZTemplatePHPOperator',
                                                    'class_parameter' => $list,
                                                    'operator_names' => array_keys( $list ) ) );
            $tpl->setVariable( 'value', 'a b' );
            $tpl->setVariable( 'encoded', 'x%20y%2Bz' );
            $this->assertSame( 'a%20b|a+b|x y+z', trim( $tpl->fetch( $file ) ) );
        }
        finally
        {
            eZTemplate::resetInstance();
        }
    }
}
