<?php
/**
 * The code of kernel/content/translations.php, moved into a class (#207 stage 1). The file kernel/content/translations.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 * The content languages page: an overview of the languages content can be written in, with how much content and
 * which classes use each, which siteaccesses show it (site.ini [RegionalSettings] SiteLanguageList) and what keeps
 * it from being removed; the page of one language; and the form that adds one. The helpers that shape what the
 * pages show take plain arrays and touch no database, so they are tested on their own
 * (tests/tests/kernel/classes/expContentLanguagesOverviewTest.php). User guide: doc/guides/content-languages.md.
 */
/*
 * The original header of kernel/content/translations.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class Translations extends \Exponential\Runnable\ModuleView
{
    /** Fewer objects than this in a language, and the page points it out. */
    const FEW_OBJECTS = 10;

    /** How many objects and classes the page of one language lists. */
    const LIST_LIMIT = 25;

    const I18N = 'design/admin/content/translations';

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $tpl = \eZTemplate::factory();
        $http = \eZHTTPTool::instance();
        $Module = $Params['Module'];

        $tpl->setVariable( 'module', $Module );

        // What the last action did, shown above the list: array( array( 'ok' => bool, 'message' => string ), ... ).
        $feedback = array();
        $siteLists = self::siteLanguageLists();

        if ( $Module->isCurrentAction( 'New' ) /*or
             $Module->isCurrentAction( 'Edit' )*/ )
        {
            return $this->newForm( $tpl, $Module, '', '', '', '' );
        }

        if ( $Module->isCurrentAction( 'StoreNew' ) /* || $http->hasPostVariable( 'StoreButton' ) */ )
        {
            $localeID = $Module->actionParameter( 'LocaleID' );
            $translationName = '';
            $translationLocale = '';
            $customName = (string) $Module->actionParameter( 'TranslationName' );
            $customLocale = (string) $Module->actionParameter( 'TranslationLocale' );
            \eZDebug::writeDebug( $localeID, 'localeID' );
            if ( $localeID != '' and
                 $localeID != -1 )
            {
                $translationLocale = $localeID;
                $localeInstance = \eZLocale::instance( $translationLocale );
                $translationName = $localeInstance->internationalLanguageName();
            }
            else
            {
                $translationName = $customName;
                $translationLocale = $customLocale;
                \eZDebug::writeDebug( $translationName, 'translationName' );
                \eZDebug::writeDebug( $translationLocale, 'translationLocale' );
            }

            // Make sure the locale string is valid, if not we try to extract a valid part of it
            if ( !preg_match( "/^" . \eZLocale::localeRegexp( false, false ) . "$/", $translationLocale ) )
            {
                if ( preg_match( "/(" . \eZLocale::localeRegexp( false, false ) . ")/", $translationLocale, $matches ) )
                {
                    $translationLocale = $matches[1];
                }
                else
                {
                    // The locale cannot be used so we show the edit page again.
                    $error = trim( $translationLocale ) === ''
                        ? \ezpI18n::tr( self::I18N, 'Choose a language from the list, or give the locale code of a custom one.' )
                        : \ezpI18n::tr( self::I18N, '"%locale" is not a locale code. A code is three letters for the language, a dash and two letters for the country, such as ger-DE.', null, array( '%locale' => $translationLocale ) );
                    return $this->newForm( $tpl, $Module, $error, $localeID, $customName, $customLocale );
                }
            }

            $existing = \eZContentLanguage::fetchByLocale( $translationLocale );
            if ( $existing )
            {
                $feedback[] = array( 'ok' => false,
                                     'message' => \ezpI18n::tr( self::I18N, '%name (%locale) is already a content language. Nothing was added.', null,
                                                                array( '%name' => $existing->attribute( 'name' ), '%locale' => $existing->attribute( 'locale' ) ) ) );
            }
            else
            {
                $locale = \eZLocale::instance( $translationLocale );
                if ( $locale->isValid() )
                {
                    $translation = \eZContentLanguage::addLanguage( $locale->localeCode(), $translationName );
                    if ( $translation )
                    {
                        \ezpEvent::getInstance()->notify( 'content/translations/cache', array( $translation->attribute( 'id' ) ) );
                        $feedback[] = array( 'ok' => true,
                                             'message' => \ezpI18n::tr( self::I18N, '%name (%locale) was added. Content can now be translated into it; add %locale to the SiteLanguageList of each siteaccess that should show it.', null,
                                                                        array( '%name' => $translation->attribute( 'name' ), '%locale' => $translation->attribute( 'locale' ) ) ) );
                    }
                    else
                    {
                        // addLanguage() refuses when every bit of the language mask is taken.
                        $feedback[] = array( 'ok' => false,
                                             'message' => \ezpI18n::tr( self::I18N, 'No language was added: this installation already has the most languages it can hold (%max).', null,
                                                                        array( '%max' => \eZContentLanguage::maxCount() ) ) );
                    }
                }
                else
                {
                    // The locale cannot be used so we show the edit page again.
                    $error = \ezpI18n::tr( self::I18N, 'There is no locale %locale in share/locale. Choose one from the list, or add its .ini file to share/locale first.', null,
                                           array( '%locale' => $translationLocale ) );
                    return $this->newForm( $tpl, $Module, $error, $localeID, $customName, $customLocale );
                }
            }
        }

        if ( $Module->isCurrentAction( 'Remove' ) )
        {
            $seletedIDList = $Module->actionParameter( 'SelectedTranslationList' );
            $seletedIDList = array_filter( (array) $seletedIDList, 'strlen' );

            if ( !$seletedIDList )
            {
                $feedback[] = array( 'ok' => false, 'message' => \ezpI18n::tr( self::I18N, 'No language was selected, so nothing was removed.' ) );
            }

            $db = \eZDB::instance();

            $db->begin();
            foreach ( $seletedIDList as $translationID )
            {
                $language = \eZContentLanguage::fetch( (int) $translationID );
                if ( !$language )
                    continue;
                $name = $language->attribute( 'name' );
                $localeCode = $language->attribute( 'locale' );
                $objects = (int) $language->objectCount();
                $classes = (int) $language->classCount();
                // The kernel refuses a language that any object or class still has; the page says why.
                if ( \eZContentLanguage::removeLanguage( $translationID ) )
                {
                    $message = \ezpI18n::tr( self::I18N, '%name (%locale) was removed.', null, array( '%name' => $name, '%locale' => $localeCode ) );
                    $sites = self::siteAccessLanguages( $siteLists );
                    if ( !empty( $sites[$localeCode]['sites'] ) )
                    {
                        $message .= ' ' . \ezpI18n::tr( self::I18N, 'These siteaccesses still list %locale in SiteLanguageList; take it out there: %sites', null,
                                                        array( '%locale' => $localeCode, '%sites' => implode( ', ', $sites[$localeCode]['sites'] ) ) );
                    }
                    $feedback[] = array( 'ok' => true, 'message' => $message );
                }
                else
                {
                    $feedback[] = array( 'ok' => false,
                                         'message' => \ezpI18n::tr( self::I18N, '%name (%locale) was not removed: %objects objects and %classes classes still have a translation in it.', null,
                                                                    array( '%name' => $name, '%locale' => $localeCode, '%objects' => $objects, '%classes' => $classes ) ) );
                }
            }
            $db->commit();
            \ezpEvent::getInstance()->notify( 'content/translations/cache', array( $seletedIDList ) );
        }

        $defaultLocale = (string) \eZINI::instance()->variable( 'RegionalSettings', 'ContentObjectLocale' );
        $interfaceLocale = (string) \eZLocale::instance()->localeCode();
        $siteMap = self::siteAccessLanguages( $siteLists );

        if ( $Params['TranslationID'] )
        {
            $translation = \eZContentLanguage::fetch( $Params['TranslationID'] );

            if( !$translation )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
            }

            $row = self::overviewRow( self::languageArray( $translation ), self::languageCounts( $translation->attribute( 'id' ) ),
                                      $siteMap, $defaultLocale, $interfaceLocale );

            $tpl->setVariable( 'translation',  $translation );
            $tpl->setVariable( 'language_row', $row );
            $tpl->setVariable( 'language_objects', self::objectsInLanguage( $translation->attribute( 'id' ), self::LIST_LIMIT ) );
            $tpl->setVariable( 'language_classes', self::classesInLanguage( $translation->attribute( 'id' ) ) );
            $tpl->setVariable( 'list_limit', self::LIST_LIMIT );

            $Result['content'] = $tpl->fetch( 'design:content/translationview.tpl' );
            $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Content translations' ),
                                            'url' => 'content/translations' ),
                                     array( 'text' => $translation->attribute( 'name' ),
                                            'url' => false ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $availableTranslations = \eZContentLanguage::fetchList( true );

        // Every language is counted for the overview; only the page's own are drawn.
        $rows = array();
        $locales = array();
        foreach ( $availableTranslations as $language )
        {
            $rows[] = self::overviewRow( self::languageArray( $language ), self::languageCounts( $language->attribute( 'id' ) ),
                                         $siteMap, $defaultLocale, $interfaceLocale );
            $locales[] = $language->attribute( 'locale' );
        }

        // Paged. The whole list was read and every row of it drawn.
        $pageCount  = count( $availableTranslations );
        $pageLimit  = \expAdminPagination::limit( 'content/translations' );
        $pageOffset = \expAdminPagination::offset( $Params );
        $availableTranslations = \expAdminPagination::page( $availableTranslations, $pageOffset, $pageLimit );
        $pageRows = \expAdminPagination::page( $rows, $pageOffset, $pageLimit );

        $tpl->setVariable( 'available_translations', $availableTranslations );
        $tpl->setVariable( 'language_rows', $pageRows );
        $tpl->setVariable( 'language_summary', self::summary( $rows, \eZContentLanguage::maxCount(), $defaultLocale ) );
        $tpl->setVariable( 'site_languages', self::siteLanguageTable( $siteLists, $locales ) );
        $tpl->setVariable( 'language_feedback', $feedback );
        $tpl->setVariable( 'few_objects', self::FEW_OBJECTS );
        $tpl->setVariable( 'translation_count', $pageCount );
        $tpl->setVariable( 'limit', $pageLimit );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $pageOffset ) );

        $Result['content'] = $tpl->fetch( 'design:content/translations.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Languages' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The add language form, again with what was entered and why it was not taken when $error is set.
     */
    private function newForm( $tpl, $Module, $error, $localeID, $customName, $customLocale )
    {
        $existing = array();
        foreach ( \eZContentLanguage::fetchList() as $language )
            $existing[] = $language->attribute( 'locale' );

        $tpl->setVariable( 'is_edit', $Module->isCurrentAction( 'Edit' ) );
        $tpl->setVariable( 'error', $error );
        $tpl->setVariable( 'selected_locale', (string) $localeID );
        $tpl->setVariable( 'custom_name', $customName );
        $tpl->setVariable( 'custom_locale', $customLocale );
        $tpl->setVariable( 'locale_choices', self::localeChoices( self::installedLocales(), $existing ) );
        $tpl->setVariable( 'existing_locales', $existing );
        $tpl->setVariable( 'language_slots', array( 'used' => count( $existing ), 'max' => \eZContentLanguage::maxCount() ) );
        $Result['content'] = $tpl->fetch( 'design:content/translationnew.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Translation' ),
                                        'url' => false ),
                                 array( 'text' => 'New',
                                        'url' => false ) );
        return $this->viewResult( $Result, null );
    }

    // ---- Helpers on plain arrays: no database, no settings ----------------------------------------------------

    /**
     * Which siteaccesses show each language: array( siteaccess => SiteLanguageList ) in, array( locale =>
     * array( 'sites' => siteaccesses listing it, 'main' => siteaccesses listing it first ) ) out. The first
     * language of a SiteLanguageList is the one a siteaccess shows first and creates content in by default.
     */
    public static function siteAccessLanguages( array $siteLists )
    {
        $map = array();
        foreach ( $siteLists as $siteAccess => $list )
        {
            $position = 0;
            foreach ( (array) $list as $locale )
            {
                $locale = trim( (string) $locale );
                if ( $locale === '' )
                    continue;
                if ( !isset( $map[$locale] ) )
                    $map[$locale] = array( 'sites' => array(), 'main' => array() );
                if ( !in_array( (string) $siteAccess, $map[$locale]['sites'], true ) )
                    $map[$locale]['sites'][] = (string) $siteAccess;
                if ( $position === 0 )
                    $map[$locale]['main'][] = (string) $siteAccess;
                $position++;
            }
        }
        return $map;
    }

    /**
     * The bit a language id stands for in the language masks: id 2 is bit 1, id 4 bit 2 ... (bit 0 is the
     * always-available flag). 0 for an id that is not a single bit.
     */
    public static function bitPosition( $id )
    {
        $id = (int) $id;
        if ( $id < 2 || ( $id & ( $id - 1 ) ) !== 0 )
            return 0;
        $bit = 0;
        while ( $id > 1 )
        {
            $id >>= 1;
            $bit++;
        }
        return $bit;
    }

    /**
     * One language as the overview shows it.
     *
     * @param array $language array( 'id', 'name', 'locale', 'country', 'intl_name', 'native_name' )
     * @param array $counts   array( 'objects', 'objects_main', 'objects_only', 'classes' )
     * @param array $siteMap  from siteAccessLanguages()
     * @param string $defaultLocale   ContentObjectLocale of the siteaccess the page is shown in
     * @param string $interfaceLocale the Locale of the administration interface
     */
    public static function overviewRow( array $language, array $counts, array $siteMap, $defaultLocale, $interfaceLocale )
    {
        $locale = (string) $language['locale'];
        $objects = (int) ( $counts['objects'] ?? 0 );
        $classes = (int) ( $counts['classes'] ?? 0 );
        $sites = $siteMap[$locale]['sites'] ?? array();
        $main = $siteMap[$locale]['main'] ?? array();

        $blockers = array();
        if ( $objects > 0 )
            $blockers[] = 'objects';
        if ( $classes > 0 )
            $blockers[] = 'classes';

        // Allowed by the kernel, but worth a second thought.
        $warnings = array();
        if ( $sites )
            $warnings[] = 'sites';
        if ( $locale === (string) $defaultLocale )
            $warnings[] = 'default';
        if ( $locale === (string) $interfaceLocale )
            $warnings[] = 'interface';

        $few = $objects > 0 && $objects < self::FEW_OBJECTS;
        $unlisted = !$sites;

        // What the language's card says about it, most pressing first.
        if ( $unlisted && $objects > 0 )
            $hint = 'unlisted_content';
        else if ( $unlisted && $classes > 0 )
            $hint = 'unlisted_classes';
        else if ( $unlisted )
            $hint = 'unlisted_empty';
        else if ( $few )
            $hint = 'few';
        else if ( $objects === 0 && $classes === 0 )
            $hint = 'empty';
        else
            $hint = '';

        $name = trim( (string) ( $language['name'] ?? '' ) );
        if ( $name === '' )
            $name = trim( (string) ( $language['intl_name'] ?? '' ) );
        if ( $name === '' )
            $name = $locale;

        return array(
            'id' => (int) $language['id'],
            'bit' => self::bitPosition( $language['id'] ),
            'name' => $name,
            'locale' => $locale,
            'country' => (string) ( $language['country'] ?? '' ),
            'native_name' => (string) ( $language['native_name'] ?? '' ),
            'objects' => $objects,
            'objects_main' => (int) ( $counts['objects_main'] ?? 0 ),
            'objects_only' => (int) ( $counts['objects_only'] ?? 0 ),
            'classes' => $classes,
            'sites' => $sites,
            'main_sites' => $main,
            'is_default' => $locale === (string) $defaultLocale,
            'is_interface' => $locale === (string) $interfaceLocale,
            'removable' => !$blockers,
            'blockers' => $blockers,
            'warnings' => $warnings,
            'few' => $few,
            'unlisted' => $unlisted,
            'attention' => $few || $unlisted,
            'hint' => $hint,
            'search' => mb_strtolower( implode( ' ', array( $name, $locale, $language['country'] ?? '', $language['native_name'] ?? '', implode( ' ', $sites ) ) ) ),
        );
    }

    /**
     * The overview's figures: how many languages, how many more fit, how many need a look, how many can go.
     */
    public static function summary( array $rows, $maxCount, $defaultLocale )
    {
        $summary = array( 'languages' => count( $rows ), 'max' => (int) $maxCount, 'free' => max( 0, (int) $maxCount - count( $rows ) ),
                          'attention' => 0, 'removable' => 0, 'unlisted' => 0, 'default_locale' => (string) $defaultLocale, 'default_name' => '' );
        foreach ( $rows as $row )
        {
            if ( $row['attention'] )
                $summary['attention']++;
            if ( $row['removable'] )
                $summary['removable']++;
            if ( $row['unlisted'] )
                $summary['unlisted']++;
            if ( $row['locale'] === (string) $defaultLocale )
                $summary['default_name'] = $row['name'];
        }
        return $summary;
    }

    /**
     * Each siteaccess with its languages in order, and the ones it lists that are not content languages here (a
     * siteaccess that lists a locale nobody added shows nothing in it).
     *
     * @return array array( array( 'siteaccess', 'languages' => array( array( 'locale', 'known', 'main' ) ), 'unknown' => int ) )
     */
    public static function siteLanguageTable( array $siteLists, array $contentLocales )
    {
        $table = array();
        foreach ( $siteLists as $siteAccess => $list )
        {
            $languages = array();
            $unknown = 0;
            foreach ( array_values( array_filter( array_map( 'trim', array_map( 'strval', (array) $list ) ), 'strlen' ) ) as $i => $locale )
            {
                $known = in_array( $locale, $contentLocales, true );
                if ( !$known )
                    $unknown++;
                $languages[] = array( 'locale' => $locale, 'known' => $known, 'main' => $i === 0 );
            }
            $table[] = array( 'siteaccess' => (string) $siteAccess, 'languages' => $languages, 'unknown' => $unknown );
        }
        return $table;
    }

    /**
     * The locales the add form offers, sorted by name, each with the text its search looks through and whether it is
     * already a content language.
     *
     * @param array $locales array( array( 'code', 'intl_name', 'native_name', 'country', 'comment' ) )
     * @param array $existing locale codes already added
     */
    public static function localeChoices( array $locales, array $existing )
    {
        $choices = array();
        foreach ( $locales as $locale )
        {
            $code = (string) $locale['code'];
            if ( $code === '' )
                continue;
            $label = trim( (string) ( $locale['intl_name'] ?? '' ) );
            if ( $label === '' )
                $label = $code;
            if ( trim( (string) ( $locale['comment'] ?? '' ) ) !== '' )
                $label .= ' [' . trim( $locale['comment'] ) . ']';
            $choices[] = array(
                'code' => $code,
                'label' => $label,
                'native_name' => (string) ( $locale['native_name'] ?? '' ),
                'country' => (string) ( $locale['country'] ?? '' ),
                'exists' => in_array( $code, $existing, true ),
                'search' => mb_strtolower( implode( ' ', array( $label, $code, $locale['native_name'] ?? '', $locale['country'] ?? '' ) ) ),
            );
        }
        usort( $choices, function ( $a, $b ) {
            return strcasecmp( $a['label'], $b['label'] ) ?: strcmp( $a['code'], $b['code'] );
        } );
        return $choices;
    }

    // ---- What the helpers are fed: the database and the settings ----------------------------------------------

    /**
     * SiteLanguageList of every siteaccess in site.ini [SiteAccessSettings] AvailableSiteAccessList, as each one
     * reads it with its own overrides.
     */
    public static function siteLanguageLists()
    {
        $lists = array();
        $available = \eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        foreach ( array_unique( (array) $available ) as $siteAccess )
        {
            if ( trim( (string) $siteAccess ) === '' )
                continue;
            $ini = \eZSiteAccess::getIni( $siteAccess, 'site.ini' );
            $list = $ini ? $ini->variable( 'RegionalSettings', 'SiteLanguageList' ) : array();
            $lists[$siteAccess] = is_array( $list ) ? array_values( $list ) : array();
        }
        return $lists;
    }

    /** One eZContentLanguage as the plain array overviewRow() takes. */
    public static function languageArray( \eZContentLanguage $language )
    {
        $locale = $language->localeObject();
        $valid = $locale && $locale->isValid();
        return array( 'id' => (int) $language->attribute( 'id' ),
                      'name' => (string) $language->attribute( 'name' ),
                      'locale' => (string) $language->attribute( 'locale' ),
                      'country' => $valid ? (string) $locale->attribute( 'country_name' ) : '',
                      'intl_name' => $valid ? (string) $locale->attribute( 'intl_language_name' ) : '',
                      'native_name' => $valid ? (string) $locale->attribute( 'language_name' ) : '' );
    }

    /**
     * How many objects have a translation in the language, have it as their main language, or have nothing else;
     * and how many classes have it.
     */
    public static function languageCounts( $languageID )
    {
        $language = \eZContentLanguage::fetch( $languageID );
        if ( !$language )
            return array( 'objects' => 0, 'objects_main' => 0, 'objects_only' => 0, 'classes' => 0 );

        $languageID = (int) $languageID;
        $db = \eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
        {
            $count = function ( array $match ) use ( $db ) {
                $rows = $db->aggregate( 'ezcontentobject', array( array( '$match' => $match ), array( '$count' => 'count' ) ) );
                return !empty( $rows ) ? (int) $rows[0]['count'] : 0;
            };
            $main = $count( array( 'initial_language_id' => $languageID ) );
            $only = $count( array( 'language_mask' => array( '$in' => array( $languageID, $languageID + 1 ) ) ) );
        }
        else
        {
            $rows = $db->arrayQuery( "SELECT COUNT(*) AS count FROM ezcontentobject WHERE initial_language_id = $languageID" );
            $main = (int) $rows[0]['count'];
            // Bit 0 is the always-available flag, so "only this language" is the id with or without it.
            $rows = $db->arrayQuery( "SELECT COUNT(*) AS count FROM ezcontentobject WHERE language_mask IN ( $languageID, " . ( $languageID + 1 ) . " )" );
            $only = (int) $rows[0]['count'];
        }

        return array( 'objects' => (int) $language->objectCount(),
                      'objects_main' => $main,
                      'objects_only' => $only,
                      'classes' => (int) $language->classCount() );
    }

    /**
     * The objects that have a translation in the language, newest change first, at most $limit, each with its main
     * node so the page can link to it and to its translations.
     */
    public static function objectsInLanguage( $languageID, $limit )
    {
        $languageID = (int) $languageID;
        $limit = max( 1, (int) $limit );
        $db = \eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
        {
            $rows = $db->aggregate( 'ezcontentobject', array(
                array( '$match' => array( '$expr' => array( '$gt' => array( array( '$bitAnd' => array( '$language_mask', $languageID ) ), 0 ) ) ) ),
                array( '$sort' => array( 'modified' => -1, 'id' => -1 ) ),
                array( '$limit' => $limit ),
                array( '$project' => array( 'id' => 1 ) ),
            ) );
        }
        else
        {
            $where = $db->databaseName() == 'oracle' ? "bitand( language_mask, $languageID ) > 0" : "language_mask & $languageID > 0";
            $rows = $db->arrayQuery( "SELECT id FROM ezcontentobject WHERE $where ORDER BY modified DESC, id DESC", array( 'limit' => $limit ) );
        }

        $objects = array();
        foreach ( (array) $rows as $row )
        {
            $object = \eZContentObject::fetch( (int) $row['id'] );
            if ( !$object instanceof \eZContentObject )
                continue;
            $initial = \eZContentLanguage::fetch( $object->attribute( 'initial_language_id' ) );
            $languages = array();
            foreach ( \eZContentLanguage::decodeLanguageMask( (int) $object->attribute( 'language_mask' ), true )['language_list'] ?? array() as $locale )
                $languages[] = $locale;
            $objects[] = array( 'id' => (int) $object->attribute( 'id' ),
                                'name' => (string) $object->attribute( 'name' ),
                                'class_name' => (string) $object->attribute( 'class_name' ),
                                'node_id' => (int) $object->attribute( 'main_node_id' ),
                                'main_locale' => $initial ? (string) $initial->attribute( 'locale' ) : '',
                                'is_main' => (int) $object->attribute( 'initial_language_id' ) === $languageID,
                                'languages' => $languages,
                                'always_available' => ( (int) $object->attribute( 'language_mask' ) & 1 ) === 1,
                                'status' => (int) $object->attribute( 'status' ),
                                'modified' => (int) $object->attribute( 'modified' ) );
        }
        return $objects;
    }

    /**
     * The classes whose names and descriptions have a translation in the language. Like
     * eZContentLanguage::classCount(), every version of a class counts: a class someone started to edit and never
     * stored (a temporary version, 'draft' here) keeps the language from being removed as much as a stored one.
     */
    public static function classesInLanguage( $languageID )
    {
        $languageID = (int) $languageID;
        $classes = array();
        foreach ( array( \eZContentClass::VERSION_STATUS_DEFINED, \eZContentClass::VERSION_STATUS_TEMPORARY, \eZContentClass::VERSION_STATUS_MODIFIED ) as $status )
        {
            foreach ( (array) \eZContentClass::fetchList( $status, true ) as $class )
            {
                if ( ( (int) $class->attribute( 'language_mask' ) & $languageID ) === 0 )
                    continue;
                $initial = \eZContentLanguage::fetch( $class->attribute( 'initial_language_id' ) );
                $classes[] = array( 'id' => (int) $class->attribute( 'id' ),
                                    'identifier' => (string) $class->attribute( 'identifier' ),
                                    'name' => (string) $class->attribute( 'name' ),
                                    'is_draft' => $status !== \eZContentClass::VERSION_STATUS_DEFINED,
                                    'is_main' => (int) $class->attribute( 'initial_language_id' ) === $languageID,
                                    'main_locale' => $initial ? (string) $initial->attribute( 'locale' ) : '' );
            }
        }
        usort( $classes, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ) ?: ( (int) $a['is_draft'] - (int) $b['is_draft'] ); } );
        return $classes;
    }

    /** The locales in share/locale (without variations), as the plain arrays localeChoices() takes. */
    public static function installedLocales()
    {
        $locales = array();
        foreach ( (array) \eZLocale::localeList( true, false ) as $locale )
        {
            if ( !$locale instanceof \eZLocale )
                continue;
            $locales[] = array( 'code' => (string) $locale->attribute( 'locale_full_code' ),
                                'intl_name' => (string) $locale->attribute( 'intl_language_name' ),
                                'native_name' => (string) $locale->attribute( 'language_name' ),
                                'country' => (string) $locale->attribute( 'country_name' ),
                                'comment' => $locale->attribute( 'country_variation' ) ? (string) $locale->attribute( 'language_comment' ) : '' );
        }
        return $locales;
    }
}

}
