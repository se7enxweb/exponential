<?php
/**
 * File containing the expCountryGroups class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The country list as people pick from it: first the groups of settings/countrygroups.ini (by default one group of the
 * most used countries, in ranked order), then "All countries", A-Z by the translated name in the collation of the
 * reader's locale (Intl's Collator, or accents and case folded when Intl is missing).
 *
 * Every country select draws its options from here through fetch( 'content', 'country_groups' ) and the shared
 * template design:country/options.tpl, so the order, the labels and the option values are the same everywhere.
 *
 * The ranked group is placed first on purpose: a browser filling a country from an address profile picks the first
 * option whose value or text matches, and "United States" used to match "United States Minor Outlying Islands"
 * because it sorts before "United States of America".
 *
 * Guide: doc/features/6.0/country-groups.md
 */
class expCountryGroups
{
    /** identifier of the A-Z group */
    const ALL = 'all';

    /** @var array cache key => groups, for the life of the process */
    private static $memory = array();

    /**
     * Forgets the cached groups (tests, or after the country list changed in the same process).
     */
    public static function resetMemory()
    {
        self::$memory = array();
    }

    /**
     * The groups for the current request: the installation's countries, its settings and the reader's locale.
     *
     * @param bool|null $repeat whether "All countries" repeats the countries of the groups above; null: the setting
     * @return array[] see build()
     */
    public static function fetch( $repeat = null )
    {
        $ini = eZINI::instance( 'countrygroups.ini' );
        if ( $repeat === null )
            $repeat = $ini->variable( 'CountrySettings', 'RepeatInAllCountries' ) !== 'disabled';
        $locale = eZLocale::instance();
        $httpLocale = (string)$locale->httpLocaleCode();
        $key = $locale->localeCode() . '|' . ( $repeat ? 1 : 0 );
        if ( isset( self::$memory[$key] ) )
            return self::$memory[$key];

        $definitions = self::definitionsFromINI( $ini );
        $groups = self::build( eZCountryType::fetchCountryList(), $definitions, $httpLocale, (bool)$repeat );
        foreach ( $groups as $index => $group )
            $groups[$index]['label'] = ezpI18n::tr( 'kernel/country', $group['label'] );
        return self::$memory[$key] = $groups;
    }

    /**
     * The groups the settings define, in their order.
     *
     * @param eZINI $ini countrygroups.ini
     * @return array[] list of array( 'identifier' => string, 'label' => string, 'codes' => string[] )
     */
    public static function definitionsFromINI( $ini )
    {
        $definitions = array();
        if ( !$ini->hasVariable( 'CountrySettings', 'GroupList' ) )
            return $definitions;
        foreach ( (array)$ini->variable( 'CountrySettings', 'GroupList' ) as $identifier )
        {
            $identifier = trim( (string)$identifier );
            if ( $identifier === '' || $identifier === self::ALL )
                continue;
            $label = $ini->hasVariable( 'CountrySettings', 'GroupName_' . $identifier )
                ? (string)$ini->variable( 'CountrySettings', 'GroupName_' . $identifier )
                : $identifier;
            $codes = $ini->hasVariable( 'CountrySettings', 'GroupCountries_' . $identifier )
                ? (array)$ini->variable( 'CountrySettings', 'GroupCountries_' . $identifier )
                : array();
            $definitions[] = array( 'identifier' => $identifier, 'label' => $label, 'codes' => $codes );
        }
        return $definitions;
    }

    /**
     * Builds the groups.
     *
     * A group keeps the order of its codes; codes that are no country of the list, and codes already shown in an
     * earlier group, are left out, and a group left empty is dropped. The last group, identifier "all", holds every
     * country A-Z. When $repeat is false it leaves out the countries of the groups above (for a multiple select,
     * where one country must have one option); when true it repeats them and marks each repeat with 'Repeated' =>
     * true, so a template selects a stored country once, in the first group.
     *
     * @param array $countries country arrays as eZCountryType::fetchCountryList() returns them (Name, Alpha2, ...)
     * @param array $definitions see definitionsFromINI()
     * @param string $httpLocale the reader's locale as an HTTP code (en-US), for the collation
     * @param bool $repeat
     * @param bool $useIntl
     * @return array[] list of array( 'identifier' => string, 'label' => string, 'countries' => array[] )
     */
    public static function build( array $countries, array $definitions, $httpLocale, $repeat = true, $useIntl = true )
    {
        $byCode = array();
        foreach ( $countries as $country )
        {
            if ( !is_array( $country ) || !isset( $country['Alpha2'] ) )
                continue;
            $code = strtoupper( trim( (string)$country['Alpha2'] ) );
            if ( $code === '' || isset( $byCode[$code] ) )
                continue;
            $country['Alpha2'] = $code;
            if ( !isset( $country['Name'] ) || (string)$country['Name'] === '' )
                $country['Name'] = $code;
            $byCode[$code] = $country;
        }

        $groups = array();
        $shown = array();
        foreach ( $definitions as $definition )
        {
            $members = array();
            foreach ( (array)$definition['codes'] as $code )
            {
                $code = strtoupper( trim( (string)$code ) );
                if ( !isset( $byCode[$code] ) || isset( $shown[$code] ) )
                    continue;
                $shown[$code] = true;
                $members[] = $byCode[$code];
            }
            if ( $members )
                $groups[] = array( 'identifier' => (string)$definition['identifier'],
                                   'label' => (string)$definition['label'],
                                   'countries' => $members );
        }

        $labels = array();
        foreach ( $byCode as $code => $country )
            $labels[$code] = (string)$country['Name'];
        $all = array();
        foreach ( self::sortCodesByName( $labels, $httpLocale, $useIntl ) as $code )
        {
            $country = $byCode[$code];
            if ( isset( $shown[$code] ) )
            {
                if ( !$repeat )
                    continue;
                $country['Repeated'] = true;
            }
            $all[] = $country;
        }
        if ( $all )
            $groups[] = array( 'identifier' => self::ALL, 'label' => 'All countries', 'countries' => $all );
        return $groups;
    }

    /**
     * Country codes ordered by their names for a reader of $httpLocale: Intl's Collator when it is there and
     * $useIntl, else the names with accents and case folded ("Åland" with A, "Côte d'Ivoire" with C).
     *
     * @param array $labels code => name
     * @param string $httpLocale
     * @param bool $useIntl
     * @return string[]
     */
    public static function sortCodesByName( array $labels, $httpLocale, $useIntl = true )
    {
        if ( $useIntl && class_exists( 'Collator' ) )
        {
            $name = str_replace( '-', '_', (string)$httpLocale );
            try
            {
                $collator = new Collator( $name !== '' ? $name : 'root' );
            }
            catch ( Throwable $e )
            {
                $collator = null;
            }
            if ( $collator instanceof Collator )
            {
                $collator->setStrength( Collator::SECONDARY );
                uksort( $labels, function ( $a, $b ) use ( $labels, $collator ) {
                    $order = $collator->compare( (string)$labels[$a], (string)$labels[$b] );
                    return is_int( $order ) && $order !== 0 ? $order : strcmp( (string)$a, (string)$b );
                } );
                return array_map( 'strval', array_keys( $labels ) );
            }
        }
        $folded = array();
        foreach ( $labels as $code => $label )
            $folded[$code] = self::fold( (string)$label );
        uksort( $labels, function ( $a, $b ) use ( $folded ) {
            $order = strcmp( $folded[$a], $folded[$b] );
            return $order !== 0 ? $order : strcmp( (string)$a, (string)$b );
        } );
        return array_map( 'strval', array_keys( $labels ) );
    }

    /**
     * A name with Latin accents removed and the case folded: the sort key when Intl is missing.
     *
     * @param string $label
     * @return string
     */
    public static function fold( $label )
    {
        $text = (string)$label;
        if ( class_exists( 'Normalizer' ) )
        {
            $decomposed = Normalizer::normalize( $text, Normalizer::FORM_D );
            if ( is_string( $decomposed ) )
                $text = preg_replace( '/\p{Mn}+/u', '', $decomposed );
        }
        else
        {
            $text = strtr( $text, array( 'Å' => 'A', 'å' => 'a', 'Ä' => 'A', 'ä' => 'a', 'á' => 'a', 'à' => 'a',
                                         'â' => 'a', 'ã' => 'a', 'ç' => 'c', 'Č' => 'C', 'č' => 'c', 'é' => 'e',
                                         'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
                                         'ñ' => 'n', 'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'Ö' => 'O', 'ö' => 'o',
                                         'õ' => 'o', 'ø' => 'o', 'š' => 's', 'ú' => 'u', 'ù' => 'u', 'û' => 'u',
                                         'Ü' => 'U', 'ü' => 'u', 'ý' => 'y', 'ž' => 'z', 'ß' => 'ss', 'Ł' => 'L',
                                         'ł' => 'l' ) );
        }
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( (string)$text, 'UTF-8' ) : strtolower( (string)$text );
    }
}
