<?php
/**
 * The messages of the GraphQL API's errors, translated (doc/eng-us/features/6.0/graphql.md, "Errors"): in the
 * language the request asks for with Accept-Language when the installation has a catalogue for it, else in the
 * language of the siteaccess (site.ini [RegionalSettings] Locale), as the administration translates. The texts are
 * in the context kernel/graphql of share/translations/<language>/translation.ts.
 *
 * extensions.code never changes with the language: a client decides by the code, a person reads the message. The
 * messages of the GraphQL specification's validation (an unknown field, a syntax error) come from the library and
 * stay in English; they quote the query.
 *
 * The language is set at the start of every request (useLanguage()), so a persistent worker (Velocity) never answers
 * in the language of an earlier request.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLMessage
{
    /** The context of the messages in the catalogues. */
    const CONTEXT = 'kernel/graphql';

    /** @var string|null the catalogue of this request (ger-DE), null: the siteaccess's */
    protected static $locale = null;

    /** @var eZTSTranslator[] locale => translator, loaded when first needed (the catalogues do not change while a process runs) */
    protected static $translators = array();

    /** @var array|null HTTP language code (de-de) => catalogue (ger-DE) */
    protected static $httpCodes = null;

    /**
     * Chooses the language of this request's messages from an Accept-Language header ('' or null: the siteaccess's).
     *
     * @param string|null $acceptLanguage
     */
    public static function useLanguage( $acceptLanguage )
    {
        self::$locale = ( $acceptLanguage === null || trim( (string)$acceptLanguage ) === '' ) ? null : self::localeFor( (string)$acceptLanguage );
    }

    /**
     * @return string|null the catalogue the messages of this request are read from (null: the siteaccess's)
     */
    public static function locale()
    {
        return self::$locale;
    }

    /**
     * A message in the language of the request.
     *
     * @param string $source the English text
     * @param array $params %name => value
     * @return string
     */
    public static function tr( $source, array $params = array() )
    {
        if ( self::$locale === null )
            return class_exists( 'ezpI18n' ) ? (string)ezpI18n::tr( self::CONTEXT, $source, null, $params ) : strtr( $source, $params );
        $translator = self::translator( self::$locale );
        $text = $translator !== null ? $translator->translate( self::CONTEXT, $source ) : null;
        return strtr( is_string( $text ) && $text !== '' ? $text : $source, $params );
    }

    /**
     * @param string $locale
     * @return eZTSTranslator|null
     */
    protected static function translator( $locale )
    {
        if ( !array_key_exists( $locale, self::$translators ) )
        {
            $translator = null;
            if ( class_exists( 'eZTSTranslator' ) )
            {
                // Not registered with the translator manager: the rest of the request keeps the siteaccess's language
                $translator = new eZTSTranslator( $locale, 'translation.ts' );
                if ( !$translator->load( self::CONTEXT ) )
                    $translator = null;
            }
            self::$translators[$locale] = $translator;
        }
        return self::$translators[$locale];
    }

    /**
     * The catalogue an Accept-Language header asks for: the first language (by its q value) that has a catalogue,
     * by its full code (de-AT) or its language (de: the first catalogue of German).
     *
     * @param string $acceptLanguage
     * @param array|null $httpCodes HTTP code => catalogue (null: the installation's)
     * @return string|null null: none of them
     */
    public static function localeFor( $acceptLanguage, $httpCodes = null )
    {
        $httpCodes = is_array( $httpCodes ) ? $httpCodes : self::httpCodes();
        $wanted = array();
        foreach ( explode( ',', (string)$acceptLanguage ) as $position => $part )
        {
            $pieces = explode( ';', trim( $part ) );
            $tag = strtolower( trim( $pieces[0] ) );
            if ( $tag === '' || $tag === '*' || !preg_match( '/^[a-z]{1,8}(-[a-z0-9]{1,8})*$/', $tag ) )
                continue;
            $q = 1.0;
            foreach ( array_slice( $pieces, 1 ) as $parameter )
            {
                if ( preg_match( '/^\s*q\s*=\s*([0-9.]+)\s*$/i', $parameter, $m ) )
                    $q = (float)$m[1];
            }
            if ( $q > 0 )
                $wanted[] = array( $tag, $q, $position );
        }
        usort( $wanted, function ( $a, $b ) { return $a[1] === $b[1] ? $a[2] - $b[2] : ( $a[1] < $b[1] ? 1 : -1 ); } );
        foreach ( $wanted as $item )
        {
            $tag = $item[0];
            if ( isset( $httpCodes[$tag] ) )
                return $httpCodes[$tag];
            $language = explode( '-', $tag )[0];
            if ( isset( $httpCodes[$language . '-' . $language] ) ) // de: de-DE before de-AT
                return $httpCodes[$language . '-' . $language];
            foreach ( $httpCodes as $code => $locale )
            {
                if ( $code === $language || strpos( $code, $language . '-' ) === 0 )
                    return $locale;
            }
        }
        return null;
    }

    /**
     * @return array HTTP code (lower case) => catalogue, for every catalogue of share/translations
     */
    protected static function httpCodes()
    {
        if ( self::$httpCodes === null )
        {
            self::$httpCodes = array();
            $root = expGraphQL::rootDir() . '/share/translations';
            foreach ( (array)glob( $root . '/*/translation.ts' ) as $file )
            {
                $locale = basename( dirname( $file ) );
                if ( $locale === 'untranslated' || !class_exists( 'eZLocale' ) )
                    continue;
                $http = strtolower( (string)eZLocale::instance( $locale )->httpLocaleCode() );
                if ( $http !== '' && !isset( self::$httpCodes[$http] ) )
                    self::$httpCodes[$http] = $locale;
            }
            ksort( self::$httpCodes );
        }
        return self::$httpCodes;
    }
}

?>
