<?php
/**
 * eZContentObject::defaultLanguage() without a prioritized language. A CLI script without siteaccess has
 * no SiteLanguageList whose languages exist; the method then took ContentObjectLocale and created it as a new
 * content language, even in an installation that already had others. No database: the language list is
 * preset in $GLOBALS, where eZContentLanguage::fetchList() keeps it.
 *
 *  DL-01 - ContentObjectLocale is used when that language exists
 *  DL-02 - Otherwise the existing language with the lowest ID, nothing is created
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class eZContentObjectDefaultLanguageTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();
    private $savedLocale;

    private const GLOBALS_KEYS = array( 'eZContentObjectDefaultLanguage', 'eZContentLanguageList', 'eZContentLanguageMask',
                                        'eZContentLanguagePrioritizedLanguages', 'eZContentLanguagePrioritizedLanguagesSet' );

    protected function setUp(): void
    {
        foreach ( self::GLOBALS_KEYS as $key )
        {
            $this->saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
            unset( $GLOBALS[$key] );
        }
        $ini = eZINI::instance();
        $this->savedLocale = $ini->hasVariable( 'RegionalSettings', 'ContentObjectLocale' )
            ? $ini->variable( 'RegionalSettings', 'ContentObjectLocale' ) : null;
        // No prioritized language known: topPriorityLanguage() is false
        $GLOBALS['eZContentLanguagePrioritizedLanguages'] = array();
        $GLOBALS['eZContentLanguageList'] = array(
            8 => new eZContentLanguage( array( 'id' => 8, 'locale' => 'fre-FR', 'name' => 'French' ) ),
            4 => new eZContentLanguage( array( 'id' => 4, 'locale' => 'eng-GB', 'name' => 'English' ) ),
            16 => new eZContentLanguage( array( 'id' => 16, 'locale' => 'esl-ES', 'name' => 'Spanish' ) ),
        );
        $GLOBALS['eZContentLanguageMask'] = 29;
    }

    protected function tearDown(): void
    {
        foreach ( self::GLOBALS_KEYS as $key )
        {
            unset( $GLOBALS[$key] );
            if ( $this->saved[$key] !== null )
            {
                $GLOBALS[$key] = $this->saved[$key][0];
            }
        }
        if ( $this->savedLocale !== null )
        {
            eZINI::instance()->setVariable( 'RegionalSettings', 'ContentObjectLocale', $this->savedLocale );
        }
    }

    public function testExistingContentObjectLocaleIsUsed()
    {
        eZINI::instance()->setVariable( 'RegionalSettings', 'ContentObjectLocale', 'esl-ES' );
        $this->assertSame( 'esl-ES', eZContentObject::defaultLanguage() );
    }

    public function testMissingContentObjectLocaleFallsBackToLowestExistingId()
    {
        eZINI::instance()->setVariable( 'RegionalSettings', 'ContentObjectLocale', 'nor-NO' );
        $this->assertSame( 'eng-GB', eZContentObject::defaultLanguage() );
        $this->assertSame( array( 4, 8, 16 ), $this->sortedIds(), 'no language may be added' );
    }

    private function sortedIds()
    {
        $ids = array_map( 'intval', array_keys( $GLOBALS['eZContentLanguageList'] ) );
        sort( $ids );
        return $ids;
    }
}
