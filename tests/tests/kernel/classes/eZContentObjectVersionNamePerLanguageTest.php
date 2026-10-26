<?php
/**
 * eZContentObjectVersion::name() per language. content/copy names each translation of the copy with
 * name( false, $languageCode ); the method had a single parameter and cached its first answer for every language,
 * so every translation got the name of the main language. No database: the content object is a stand-in.
 *
 *  VN-01 - name( false, $code ) returns the name of that language
 *  VN-02 - name( $code ) (the other callers) still works
 *  VN-03 - The answer is cached per language, not once for all
 *  VN-04 - name() without language is not overwritten by a call with language, and vice versa
 *  VN-05 - Without a name of this version in the language, the object's name in that language is used
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
/** Content object stand-in: names per version and language from an array, every lookup counted */
class expVersionNamePerLanguageObject extends eZContentObject
{
    public $names = array();
    public $objectNames = array();
    public $lookups = 0;

    function versionLanguageName( $version, $lang = false )
    {
        $this->lookups++;
        $key = $lang === false ? 'initial' : $lang;
        return $this->names[$version][$key] ?? false;
    }

    function name( $version = false, $lang = false )
    {
        return $this->objectNames[$lang === false ? 'initial' : $lang] ?? false;
    }
}

class eZContentObjectVersionNamePerLanguageTest extends PHPUnit\Framework\TestCase
{
    private function version( expVersionNamePerLanguageObject $object )
    {
        $version = new eZContentObjectVersion( array( 'id' => 1, 'contentobject_id' => 4711, 'version' => 3 ) );
        $version->ContentObject = $object;
        return $version;
    }

    private function object()
    {
        $object = new expVersionNamePerLanguageObject( array( 'id' => 4711 ) );
        $object->names = array( 3 => array( 'initial' => 'Haus', 'ger-DE' => 'Haus', 'eng-GB' => 'House', 'fre-FR' => 'Maison' ) );
        return $object;
    }

    public function testSecondParameterSelectsTheLanguage()
    {
        $version = $this->version( $this->object() );
        $this->assertSame( 'Haus', $version->name() );
        $this->assertSame( 'House', $version->name( false, 'eng-GB' ) );
        $this->assertSame( 'Maison', $version->name( false, 'fre-FR' ) );
    }

    public function testFirstParameterStillSelectsTheLanguage()
    {
        $version = $this->version( $this->object() );
        $this->assertSame( 'House', $version->name( 'eng-GB' ) );
        $this->assertSame( 'Maison', $version->name( 'fre-FR' ) );
    }

    public function testCachedPerLanguage()
    {
        $object = $this->object();
        $version = $this->version( $object );
        $version->name( false, 'eng-GB' );
        $version->name( false, 'eng-GB' );
        $version->name( 'eng-GB' );
        $this->assertSame( 1, $object->lookups );
        $version->name( false, 'fre-FR' );
        $this->assertSame( 2, $object->lookups );
    }

    public function testNameWithoutLanguageIsKeptApart()
    {
        $version = $this->version( $this->object() );
        $this->assertSame( 'House', $version->name( false, 'eng-GB' ) );
        $this->assertSame( 'Haus', $version->name() );
        $this->assertSame( 'Maison', $version->name( false, 'fre-FR' ) );
        $this->assertSame( 'Haus', $version->name() );
    }

    public function testFallsBackToTheObjectNameInThatLanguage()
    {
        $object = $this->object();
        $object->objectNames = array( 'nor-NO' => 'Hus' );
        $version = $this->version( $object );
        $this->assertSame( 'Hus', $version->name( false, 'nor-NO' ) );
    }
}
