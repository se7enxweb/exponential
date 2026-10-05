<?php
/**
 * ezjscore/call/explanguage::<service> - content languages, locales and translation files: the languages of the
 * installation, the priority list of this siteaccess, language masks, locale details (formats, currency) and the
 * progress of the interface translations (share/translations).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expLanguageServices extends expServiceBase
{
    public static $services = array(
        'languages' => array( 'summary' => 'The content languages of the installation with their object counts', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'list of id, locale, name, disabled, object_count' ),
        'language' => array( 'summary' => 'One content language by locale', 'access' => 'public', 'write' => false,
            'args' => array( 'locale' => 'string' ), 'returns' => 'language' ),
        'prioritized' => array( 'summary' => 'The languages in the priority order of this siteaccess', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'list of languages' ),
        'top' => array( 'summary' => 'The top priority language of this siteaccess', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'language' ),
        'decode' => array( 'summary' => 'The languages in a language mask', 'access' => 'public', 'write' => false,
            'args' => array( 'mask' => 'int' ), 'returns' => 'list of locales, always_available' ),
        'maskfor' => array( 'summary' => 'The language mask of some locales', 'access' => 'public', 'write' => false,
            'args' => array( 'locales' => 'list', 'always_available' => 'bool' ), 'returns' => 'mask' ),
        'locales' => array( 'summary' => 'The locales the system knows (share/locale)', 'access' => 'public', 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of locale codes' ),
        'localeinfo' => array( 'summary' => 'Names, formats and currency of a locale', 'access' => 'public', 'write' => false,
            'args' => array( 'locale' => 'string' ), 'returns' => 'locale details' ),
        'countries' => array( 'summary' => 'The country codes known to the locales', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'list of country codes' ),
        'translations' => array( 'summary' => 'The interface translations present in share/translations and their size', 'access' => 'user', 'write' => false,
            'args' => array(), 'returns' => 'list of locale, files, bytes' ),
        'translationstats' => array( 'summary' => 'Messages, finished, unfinished and obsolete of one interface translation', 'access' => 'user', 'write' => false,
            'args' => array( 'locale' => 'string' ), 'returns' => 'messages, finished, unfinished, obsolete, percent' ),
        'translationcontexts' => array( 'summary' => 'The translation contexts (files) of one interface translation with message counts', 'access' => 'user', 'write' => false,
            'args' => array( 'locale' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of context, messages' ),
        'siteaccess' => array( 'summary' => 'The language settings of the current siteaccess', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'locale, content_languages, translation_extensions' ),
        'objectcounts' => array( 'summary' => 'Published object names per language', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of locale, count' ),
    );

    protected static function exportLanguage( eZContentLanguage $l )
    {
        return array( 'id' => (int)$l->attribute( 'id' ), 'locale' => $l->attribute( 'locale' ), 'name' => $l->attribute( 'name' ),
                      'disabled' => (bool)$l->attribute( 'disabled' ) );
    }

    protected static function locale( $code )
    {
        if ( !preg_match( '/^[a-z]{3}-[A-Z]{2}(@[A-Za-z0-9_]+)?$/', $code ) )
            throw new expServiceException( 'A locale looks like eng-GB', 400 );
        $locale = eZLocale::instance( $code );
        if ( !$locale || !$locale->isValid() )
            throw new expServiceException( "Unknown locale '$code'", 404 );
        return $locale;
    }

    public static function languages( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( eZContentLanguage::fetchList() as $l )
            $list[] = array_merge( self::exportLanguage( $l ), array( 'object_count' => (int)$l->attribute( 'object_count' ) ) );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function language( $args )
    {
        self::guard( __FUNCTION__ );
        $l = eZContentLanguage::fetchByLocale( self::arg( $args, 0, 'string' ) );
        if ( !$l instanceof eZContentLanguage )
            throw new expServiceException( 'No such content language', 404 );
        return self::ok( array_merge( self::exportLanguage( $l ), array( 'object_count' => (int)$l->attribute( 'object_count' ), 'class_count' => (int)$l->attribute( 'class_count' ) ) ) );
    }

    public static function prioritized( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( eZContentLanguage::prioritizedLanguages() as $l )
            $list[] = self::exportLanguage( $l );
        return self::ok( $list );
    }

    public static function top( $args )
    {
        self::guard( __FUNCTION__ );
        $l = eZContentLanguage::topPriorityLanguage();
        return self::ok( $l ? self::exportLanguage( $l ) : null );
    }

    public static function decode( $args )
    {
        self::guard( __FUNCTION__ );
        $mask = self::arg( $args, 0, 'int' );
        if ( $mask < 0 )
            throw new expServiceException( 'A language mask is not negative', 400 );
        $d = eZContentLanguage::decodeLanguageMask( $mask, true );
        return self::ok( array( 'mask' => $mask, 'locales' => array_values( $d['language_list'] ), 'always_available' => (bool)$d['always_available'] ) );
    }

    public static function maskfor( $args )
    {
        self::guard( __FUNCTION__ );
        $locales = self::arg( $args, 0, 'list' );
        if ( !$locales || count( $locales ) > 30 )
            throw new expServiceException( 'Give 1 to 30 locales', 400 );
        foreach ( $locales as $l )
            if ( !eZContentLanguage::fetchByLocale( $l ) instanceof eZContentLanguage )
                throw new expServiceException( "'$l' is not a content language", 404 );
        return self::ok( array( 'mask' => (int)eZContentLanguage::maskByLocale( $locales, self::arg( $args, 1, 'bool', false ) ) ) );
    }

    public static function locales( $args )
    {
        self::guard( __FUNCTION__ );
        return self::pageOf( eZLocale::localeList( false, false ), $args, 0, 1 );
    }

    public static function localeinfo( $args )
    {
        self::guard( __FUNCTION__ );
        $l = self::locale( self::arg( $args, 0, 'string' ) );
        return self::ok( array( 'locale' => $l->localeCode(), 'language_code' => $l->languageCode(), 'language_name' => $l->languageName(),
                                'country_name' => $l->countryName(), 'charset' => $l->charset(), 'http_locale' => $l->httpLocaleCode(),
                                'currency_symbol' => $l->currencySymbol(), 'currency_name' => $l->currencyName(), 'currency_short_name' => $l->currencyShortName(),
                                'decimal_symbol' => $l->currencyDecimalSymbol(), 'thousands_separator' => $l->currencyThousandsSeparator(),
                                'decimal_count' => (int)$l->currencyDecimalCount(), 'short_date' => $l->attribute( 'short_date_format' ),
                                'date' => $l->attribute( 'date_format' ), 'time' => $l->attribute( 'time_format' ),
                                'datetime' => $l->attribute( 'datetime_format' ) ) );
    }

    public static function countries( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array_values( eZLocale::countryList( false ) );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    protected static function translationFile( $locale )
    {
        if ( !preg_match( '/^[a-z]{3}-[A-Z]{2}$/', $locale ) )
            throw new expServiceException( 'A locale looks like ger-DE', 400 );
        $file = 'share/translations/' . $locale . '/translation.ts';
        if ( !is_file( $file ) )
            throw new expServiceException( "No translation for '$locale'", 404 );
        return $file;
    }

    public static function translations( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( glob( 'share/translations/*', GLOB_ONLYDIR ) ?: array() as $d )
        {
            $files = glob( $d . '/*' ) ?: array();
            $bytes = 0;
            foreach ( $files as $f )
                $bytes += is_file( $f ) ? filesize( $f ) : 0;
            $list[] = array( 'locale' => basename( $d ), 'files' => count( $files ), 'bytes' => $bytes, 'has_translation' => is_file( $d . '/translation.ts' ) );
        }
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    protected static function counts( $file )
    {
        $xml = file_get_contents( $file );
        $messages = preg_match_all( '#<message[ >]#', $xml );
        $unfinished = preg_match_all( '#<translation type="unfinished"#', $xml );
        $obsolete = preg_match_all( '#<translation type="obsolete"#', $xml ) + preg_match_all( '#<translation type="vanished"#', $xml );
        $finished = max( 0, $messages - $unfinished - $obsolete );
        return array( 'messages' => $messages, 'finished' => $finished, 'unfinished' => $unfinished, 'obsolete' => $obsolete,
                      'percent' => $messages - $obsolete > 0 ? round( 100 * $finished / ( $messages - $obsolete ), 1 ) : 0 );
    }

    public static function translationstats( $args )
    {
        self::guard( __FUNCTION__ );
        $locale = self::arg( $args, 0, 'string' );
        return self::ok( array_merge( array( 'locale' => $locale ), self::counts( self::translationFile( $locale ) ) ) );
    }

    public static function translationcontexts( $args )
    {
        self::guard( __FUNCTION__ );
        $file = self::translationFile( self::arg( $args, 0, 'string' ) );
        $xml = file_get_contents( $file );
        $list = array();
        if ( preg_match_all( '#<context>\s*<name>([^<]*)</name>(.*?)</context>#s', $xml, $m, PREG_SET_ORDER ) )
            foreach ( $m as $c )
                $list[] = array( 'context' => html_entity_decode( $c[1], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ), 'messages' => preg_match_all( '#<message[ >]#', $c[2] ) );
        return self::pageOf( $list, $args, 1, 2 );
    }

    public static function siteaccess( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = eZINI::instance( 'site.ini' );
        return self::ok( array( 'locale' => $ini->variable( 'RegionalSettings', 'Locale' ),
                                'content_languages' => array_values( (array)$ini->variable( 'RegionalSettings', 'SiteLanguageList' ) ),
                                'translation_extensions' => $ini->hasVariable( 'RegionalSettings', 'TranslationExtensions' ) ? array_values( (array)$ini->variable( 'RegionalSettings', 'TranslationExtensions' ) ) : array(),
                                'text_translation' => $ini->variable( 'RegionalSettings', 'TextTranslation' ) ) );
    }

    public static function objectcounts( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( eZDB::instance()->arrayQuery( 'SELECT real_translation AS l, COUNT(*) AS n FROM ezcontentobject_name GROUP BY real_translation ORDER BY n DESC' ) as $r )
            $list[] = array( 'locale' => $r['l'], 'count' => (int)$r['n'] );
        return self::ok( $list );
    }
}
