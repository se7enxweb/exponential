<?php
/**
 * The grouped country list (expCountryGroups, settings/countrygroups.ini, design:country/options.tpl) without a
 * database: the ranked groups keep their order, "All countries" is A-Z in the reader's collation (with and without
 * Intl), repeats are marked so a stored value is selected once, the shipped ranking only names countries of
 * country.ini, and the option values the templates post stay what the stored data expects.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/datatypes/expCountryGroupsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 5 ) . '/kernel/classes/datatypes/ezcountry/expcountrygroups.php';

/** The two calls of eZINI that expCountryGroups::definitionsFromINI() makes */
class expCountryGroupsTestINI
{
    /** @var array group => name => value */
    public $groups;

    public function __construct( array $groups )
    {
        $this->groups = $groups;
    }

    public function hasVariable( $group, $name )
    {
        return isset( $this->groups[$group] ) && array_key_exists( $name, $this->groups[$group] );
    }

    public function variable( $group, $name )
    {
        return $this->hasVariable( $group, $name ) ? $this->groups[$group][$name] : false;
    }
}

class expCountryGroupsTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected static $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    /**
     * Reads an INI file of the kernel's format (Name=value, Name[]=value, Name[] to empty a list).
     *
     * @param string $relative
     * @return array group => name => value
     */
    protected static function readINI( $relative )
    {
        $groups = array();
        $group = null;
        foreach ( file( self::$root . '/' . $relative, FILE_IGNORE_NEW_LINES ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' || $line[0] === '#' )
                continue;
            if ( preg_match( '/^\[(.+)\]$/', $line, $m ) )
            {
                $group = $m[1];
                $groups[$group] = array();
                continue;
            }
            if ( $group === null )
                continue;
            if ( preg_match( '/^([^=\[]+)\[\]$/', $line, $m ) )
            {
                $groups[$group][$m[1]] = array();
                continue;
            }
            if ( preg_match( '/^([^=\[]+)\[\]=(.*)$/', $line, $m ) )
            {
                $groups[$group][$m[1]][] = $m[2];
                continue;
            }
            if ( preg_match( '/^([^=]+)=(.*)$/', $line, $m ) )
                $groups[$group][$m[1]] = $m[2];
        }
        return $groups;
    }

    /**
     * The countries of settings/country.ini as eZCountryType::fetchCountryList() returns them (untranslated).
     *
     * @return array[]
     */
    protected static function shippedCountries()
    {
        return array_values( self::readINI( 'settings/country.ini' ) );
    }

    protected static function codesOf( array $group )
    {
        return array_map( function ( $country ) { return $country['Alpha2']; }, $group['countries'] );
    }

    protected static function sample()
    {
        return array(
            array( 'Name' => 'United States Minor Outlying Islands', 'Alpha2' => 'UM' ),
            array( 'Name' => 'Germany', 'Alpha2' => 'DE' ),
            array( 'Name' => 'United States of America', 'Alpha2' => 'US' ),
            array( 'Name' => 'Åland', 'Alpha2' => 'AX' ),
            array( 'Name' => 'Albania', 'Alpha2' => 'AL' ),
            array( 'Name' => 'Canada', 'Alpha2' => 'CA' ),
            array( 'Name' => 'Austria', 'Alpha2' => 'AT' ),
        );
    }

    public function testRankedGroupKeepsItsOrderAndSkipsUnknownAndRepeatedCodes()
    {
        $groups = expCountryGroups::build( self::sample(), array(
            array( 'identifier' => 'common', 'label' => 'Most used countries', 'codes' => array( 'US', 'ca', 'ZZ', 'US', 'DE' ) ),
            array( 'identifier' => 'second', 'label' => 'Second', 'codes' => array( 'DE', 'CA' ) ),
        ), 'en-US', true, false );

        $this->assertSame( array( 'common', expCountryGroups::ALL ), array_column( $groups, 'identifier' ),
                           'a group whose countries are all shown above is dropped' );
        $this->assertSame( array( 'US', 'CA', 'DE' ), self::codesOf( $groups[0] ) );
        $this->assertSame( 'All countries', $groups[1]['label'] );
    }

    public function testAllCountriesIsSortedAndMarksRepeats()
    {
        $groups = expCountryGroups::build( self::sample(), array(
            array( 'identifier' => 'common', 'label' => 'x', 'codes' => array( 'US', 'CA' ) ),
        ), 'en-US', true, false );

        $all = $groups[1];
        $this->assertSame( array( 'AX', 'AL', 'AT', 'CA', 'DE', 'UM', 'US' ), self::codesOf( $all ),
                           'Åland sorts with A, before Albania' );
        foreach ( $all['countries'] as $country )
            $this->assertSame( in_array( $country['Alpha2'], array( 'US', 'CA' ), true ), !empty( $country['Repeated'] ),
                               $country['Alpha2'] . ' is marked as a repeat exactly when it is ranked' );
        $this->assertArrayNotHasKey( 'Repeated', $groups[0]['countries'][0] );
    }

    public function testWithoutRepeatAllCountriesLeavesTheRankedOnesOut()
    {
        $groups = expCountryGroups::build( self::sample(), array(
            array( 'identifier' => 'common', 'label' => 'x', 'codes' => array( 'US', 'CA' ) ),
        ), 'en-US', false, false );

        $this->assertSame( array( 'AX', 'AL', 'AT', 'DE', 'UM' ), self::codesOf( $groups[1] ) );
        $codes = array_merge( self::codesOf( $groups[0] ), self::codesOf( $groups[1] ) );
        $this->assertSame( count( $codes ), count( array_unique( $codes ) ), 'a multiple select lists each country once' );
    }

    public function testNoGroupsLeavesOnlyAllCountries()
    {
        $groups = expCountryGroups::build( self::sample(), array(), 'en-US' );
        $this->assertCount( 1, $groups );
        $this->assertSame( expCountryGroups::ALL, $groups[0]['identifier'] );
        $this->assertCount( 7, $groups[0]['countries'] );
    }

    public function testFallbackSortFoldsAccentsAndCase()
    {
        $labels = array( 'AT' => 'Österreich', 'OM' => 'Oman', 'PK' => 'Pakistan', 'AX' => 'Ålandinseln',
                         'AF' => 'afghanistan' );
        $this->assertSame( array( 'AF', 'AX', 'OM', 'AT', 'PK' ), expCountryGroups::sortCodesByName( $labels, 'de-DE', false ) );
    }

    public function testIntlCollationOfTheReadersLocale()
    {
        if ( !class_exists( 'Collator' ) )
            $this->markTestSkipped( 'the intl extension is not loaded' );
        $labels = array( 'AT' => 'Österreich', 'OM' => 'Oman', 'PK' => 'Pakistan', 'TR' => 'Türkei', 'TG' => 'Togo',
                         'TN' => 'Tunesien' );
        $this->assertSame( array( 'OM', 'AT', 'PK', 'TG', 'TN', 'TR' ), expCountryGroups::sortCodesByName( $labels, 'de-DE' ),
                           'German sorts Ö with O and Ü with U' );
        $labels = array( 'AT' => 'Österreich', 'OM' => 'Oman', 'PK' => 'Pakistan' );
        $this->assertSame( array( 'OM', 'PK', 'AT' ), expCountryGroups::sortCodesByName( $labels, 'sv-SE' ),
                           'Swedish sorts Ö after Z' );
    }

    public function testDefinitionsFromSettings()
    {
        $ini = new expCountryGroupsTestINI( array( 'CountrySettings' => array(
            'GroupList' => array( 'common', '', 'all', 'nolabel' ),
            'GroupName_common' => 'Most used countries',
            'GroupCountries_common' => array( 'US' ),
            'GroupCountries_nolabel' => array( 'DE' ),
        ) ) );
        $this->assertSame( array(
            array( 'identifier' => 'common', 'label' => 'Most used countries', 'codes' => array( 'US' ) ),
            array( 'identifier' => 'nolabel', 'label' => 'nolabel', 'codes' => array( 'DE' ) ),
        ), expCountryGroups::definitionsFromINI( $ini ), 'empty names and the reserved "all" are skipped' );
        $this->assertSame( array(), expCountryGroups::definitionsFromINI( new expCountryGroupsTestINI( array() ) ) );
    }

    public function testShippedRankingNamesCountriesOfCountryIniInTheOwnersOrder()
    {
        $settings = self::readINI( 'settings/countrygroups.ini' );
        $this->assertArrayHasKey( 'CountrySettings', $settings );
        $definitions = expCountryGroups::definitionsFromINI( new expCountryGroupsTestINI( $settings ) );
        $this->assertSame( array( 'common' ), array_column( $definitions, 'identifier' ) );
        $codes = $definitions[0]['codes'];

        $this->assertGreaterThanOrEqual( 25, count( $codes ) );
        $this->assertLessThanOrEqual( 40, count( $codes ) );
        $this->assertSame( count( $codes ), count( array_unique( $codes ) ), 'no country is ranked twice' );
        $known = array_column( self::shippedCountries(), 'Alpha2' );
        $this->assertSame( array(), array_values( array_diff( $codes, $known ) ), 'every ranked code is in country.ini' );

        $this->assertSame( array( 'US', 'CA', 'MX' ), array_slice( $codes, 0, 3 ), 'North America first' );
        $position = array_flip( $codes );
        foreach ( array( array( 'BR', 'DE' ), array( 'DE', 'GB' ), array( 'GB', 'JP' ), array( 'JP', 'NZ' ),
                         array( 'NZ', 'AU' ), array( 'AU', 'ZA' ) ) as $pair )
            $this->assertLessThan( $position[$pair[1]], $position[$pair[0]], $pair[0] . ' is ranked before ' . $pair[1] );
    }

    /**
     * What a browser's autofill does when it falls back to the text: the first option whose text or value starts
     * with what the address profile holds.
     */
    public function testFirstOptionMatchingAUnitedStatesProfileIsTheUnitedStates()
    {
        $settings = self::readINI( 'settings/countrygroups.ini' );
        $groups = expCountryGroups::build( self::shippedCountries(),
                                           expCountryGroups::definitionsFromINI( new expCountryGroupsTestINI( $settings ) ),
                                           'en-US', true );
        $options = array();
        foreach ( $groups as $group )
            foreach ( $group['countries'] as $country )
                $options[] = $country;

        foreach ( array( 'United States', 'United States of America', 'US', 'USA' ) as $profile )
        {
            $match = null;
            foreach ( $options as $country )
            {
                if ( stripos( $country['Name'], $profile ) === 0 || strcasecmp( $country['Alpha2'], $profile ) === 0
                     || ( isset( $country['Alpha3'] ) && strcasecmp( $country['Alpha3'], $profile ) === 0 ) )
                {
                    $match = $country['Alpha2'];
                    break;
                }
            }
            $this->assertSame( 'US', $match, '"' . $profile . '" matches the United States first' );
        }
        $this->assertSame( 'US', $options[0]['Alpha2'], 'the United States is the first country' );
    }

    public function testTemplatesDrawTheGroupsWithTheValuesTheStoredDataExpects()
    {
        $options = file_get_contents( self::$root . '/design/standard/templates/country/options.tpl' );
        $this->assertStringContainsString( '<optgroup label="{$country_option_group.label|wash}">', $options );
        $this->assertStringContainsString( "{default \$country_value_field = 'Alpha2'", $options,
                                           'the country attribute posts codes' );
        $this->assertStringContainsString( 'is_set( $country_option.Repeated )|not', $options,
                                           'a repeated country is never selected twice' );
        $this->assertStringContainsString( "hash( 'repeat', \$country_multiple|not )", $options );

        foreach ( array( 'design/standard/templates/shop/country/edit.tpl',
                         'extension/sevenx_themes_media/design/media/templates/shop/country/edit.tpl' ) as $file )
        {
            if ( !is_file( self::$root . '/' . $file ) )
                continue;
            $text = file_get_contents( self::$root . '/' . $file );
            $this->assertStringContainsString( "design:country/options.tpl", $text, $file );
            $this->assertStringContainsString( "country_value_field=cond( \$use_country_code, 'Alpha2', 'Name' )", $text,
                                               $file . ' posts the name, as orders store it, unless codes are asked for' );
            $this->assertStringContainsString( "'country', 'country-name'", $text, $file . ' names the autofill field' );
        }

        foreach ( array( 'design/standard/templates/content/datatype/edit/ezcountry.tpl',
                         'design/standard/templates/content/datatype/collect/ezcountry.tpl',
                         'design/standard/templates/class/datatype/edit/ezcountry.tpl' ) as $file )
        {
            $text = file_get_contents( self::$root . '/' . $file );
            $this->assertStringContainsString( "design:country/options.tpl", $text, $file );
            $this->assertStringNotContainsString( "country_value_field", $text, $file . ' keeps posting codes' );
            $this->assertDoesNotMatchRegularExpression( '/<style|style="/', $text, $file );
        }
    }
}
