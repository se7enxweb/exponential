<?php
/**
 * The spell checker of the online editor on current PHP (checked on PHP 8.5), the ezoe part of LoveStack commit
 * cb8a3a1c58 (mugoweb/ezpublish-legacy, "PHP 5.3, 5.5, 5.6 and 7.0 deprecation fixes", Copyright (C) Mugo Web):
 *
 *  SR-01 - ezoe/spellcheck_rpc reads the raw request body from php://input when no json_data is posted; it never
 *          reads $HTTP_RAW_POST_DATA, which PHP 7.0 removed
 *  SR-02 - GoogleSpell decodes numeric character references without the /e modifier PHP 7.0 removed
 *  SR-03 - SpellChecker and the Moxiecode JSON classes are constructed through __construct(), so PHP 8 calls a
 *          constructor; the old method names still work
 *
 * Exponential already had all three; this test keeps them. Thanks to Mugo Web for the review in LoveStack.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezoeSpellcheckRpcRawInputTest extends PHPUnit\Framework\TestCase
{
    protected function classes()
    {
        $dir = dirname( __DIR__, 4 ) . '/extension/ezoe/modules/ezoe/classes';
        require_once $dir . '/SpellChecker.php';
        require_once $dir . '/GoogleSpell.php';
        require_once $dir . '/utils/mcejson.php';
    }

    /** SR-01 */
    public function testTheRawBodyComesFromPhpInput()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/spellcheck_rpc.php' );
        $this->assertDoesNotMatchRegularExpression( '/\$HTTP_RAW_POST_DATA\b/', $source, 'the removed global variable is not read' );
        $this->assertMatchesRegularExpression( '/else if \(\s*!\$raw\s*\)\s*\$raw = file_get_contents\(\s*"php:\/\/input"\s*\);/', $source );
    }

    /** SR-02 */
    public function testGoogleSpellDecodesCharacterReferences()
    {
        $this->classes();
        $config = array();
        $spell = new GoogleSpell( $config );
        $this->assertSame( 'AB & C', $spell->_unhtmlentities( '&#x41;&#66; &amp; C' ) );
    }

    /** SR-03 */
    public function testTheClassesHaveConstructors()
    {
        $this->classes();
        foreach ( array( 'SpellChecker', 'GoogleSpell', 'Moxiecode_JSONReader', 'Moxiecode_JSON' ) as $class )
        {
            $this->assertNotNull( ( new ReflectionClass( $class ) )->getConstructor(), $class );
            $this->assertSame( '__construct', ( new ReflectionClass( $class ) )->getConstructor()->getName(), $class );
        }
        $json = new Moxiecode_JSON();
        $this->assertSame( array( 'id' => 'c0', 'params' => array( 'en', array( 'wrod' ) ) ), $json->decode( '{"id":"c0","params":["en",["wrod"]]}' ) );
    }
}
