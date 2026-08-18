<?php
#[\PHPUnit\Framework\Attributes\Group('database')]
/**
 * File containing the eZCountryTypeTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 * @group database
 */

class eZCountryTypeTest extends ezpDatabaseTestCase
{

    /**
     * Test for the sort feature of country list
     */
    public function testFetchTranslatedNamesSort()
    {
        $translatedCountriesList = array(
            'FR' => 'France',
            'GB' => 'Royaume-uni',
            'DE' => 'Allemagne',
            'NO' => 'Norvège' );

        ezpINIHelper::setINISetting( array( 'fre-FR.ini', 'share/locale' ), 'CountryNames', 'Countries', $translatedCountriesList );
        ezpINIHelper::setINISetting( 'site.ini', 'RegionalSettings', 'Locale', 'fre-FR' );

        $countries = eZCountryType::fetchCountryList();
        $this->assertIsArray( $countries, "eZCountryType::fetchCountryList() didn't return an array" );

        $countryListIsSorted = true;
        foreach( $countries as $country )
        {
            if ( !isset( $previousCountry ) )
            {
                $previousCountry = $country;
                continue;
            }

            if ( strcoll( $previousCountry['Name'], $country['Name'] ) > 0 )
            {
                $countryListIsSorted = false;
                break;
            }
        }

        ezpINIHelper::restoreINISettings();
        $this->assertTrue( $countryListIsSorted, "Country list isn't sorted" );
    }
}

?>
