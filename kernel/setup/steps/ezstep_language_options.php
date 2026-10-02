<?php
/**
 * File containing the eZStepLanguageOptions class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZStepLanguageOptions ezstep_language_options.php
  \brief The class eZStepLanguageOptions does

*/

class eZStepLanguageOptions extends eZStepInstaller
{
    /**
     * Constructor
     *
     * @param eZTemplate $tpl
     * @param eZHTTPTool $http
     * @param eZINI $ini
     * @param array $persistenceList
     */
    public function __construct( $tpl, $http, $ini, &$persistenceList )
    {
        parent::__construct( $tpl, $http, $ini, $persistenceList, 'language_options', 'Language options' );
    }

    function processPostData()
    {
        $primaryLanguage = $this->Http->hasPostVariable( 'eZSetupDefaultLanguage' ) ? $this->Http->postVariable( 'eZSetupDefaultLanguage' ) : '';
        $languages       = $this->Http->hasPostVariable( 'eZSetupLanguages' ) ? (array)$this->Http->postVariable( 'eZSetupLanguages' ) : array();

        // The primary language, then the additional ones: only languages
        // there is a locale for, none twice, and the primary not also listed
        // as additional (the page shows both a radio button and a checkbox
        // per language, and nothing stopped a person ticking both)
        $choice = eZSetupValidateLanguageChoice( $primaryLanguage, $languages );
        if ( $choice['errors'] )
        {
            $this->LanguageErrors = $choice['errors'];
            $this->PostedChoice = array( 'primary_language' => is_string( $primaryLanguage ) ? $primaryLanguage : '',
                                         'extra_languages' => array_values( array_filter( $languages, 'is_string' ) ) );
            return false;
        }

        $regionalInfo = eZStepLanguageOptions::regionalInfoFromChoice( $choice );

        $this->PersistenceList['regional_info'] = $regionalInfo;
        $charset = false;

//SP experimental code 26.04.2007 commented "if"
//        if ( !isset( $this->PersistenceList['database_info']['use_unicode'] ) ||
//             $this->PersistenceList['database_info']['use_unicode'] == false )
//        {
            // If we have already figured out charset and it is utf-8
            // we don't have to check the new languages
            if ( isset( $this->PersistenceList['regional_info']['site_charset'] ) and
                 $this->PersistenceList['regional_info']['site_charset'] == 'utf-8' )
            {
                $charset = 'utf-8';
            }
            else
            {
                $primaryLanguage     = null;
                $allLanguages        = array();
                $allLanguageCodes    = array();
                $variationsLanguages = array();
                $primaryLanguageCode = $this->PersistenceList['regional_info']['primary_language'];
                $extraLanguageCodes  = isset( $this->PersistenceList['regional_info']['languages'] ) ? $this->PersistenceList['regional_info']['languages'] : array();
                $extraLanguageCodes  = array_diff( $extraLanguageCodes, array( $primaryLanguageCode ) );

                /*
                if ( isset( $this->PersistenceList['regional_info']['variations'] ) )
                {
                    $variations = $this->PersistenceList['regional_info']['variations'];
                    foreach ( $variations as $variation )
                    {
                        $locale = eZLocale::create( $variation );
                        if ( $locale->localeCode() == $primaryLanguageCode )
                        {
                            $primaryLanguage = $locale;
                        }
                        else
                        {
                            $variationsLanguages[] = $locale;
                        }
                    }
                }
                */

                if ( $primaryLanguage === null )
                    $primaryLanguage = eZLocale::create( $primaryLanguageCode );

                $allLanguages[] = $primaryLanguage;

                foreach ( $extraLanguageCodes as $extraLanguageCode )
                {
                    $allLanguages[] = eZLocale::create( $extraLanguageCode );
                    $allLanguageCodes[] = $extraLanguageCode;
                }

                $canUseUnicode = isset( $this->PersistenceList['database_info']['use_unicode'] ) ? $this->PersistenceList['database_info']['use_unicode'] : false;
                $charset = $this->findAppropriateCharset( $primaryLanguage, $allLanguages, $canUseUnicode );
                if ( !$charset )
                {
                    $this->Error = 1;
                    return false;
                }
            }
            // Store the charset for later handling
            $this->PersistenceList['regional_info']['site_charset'] = $charset;
//SP experimental code 26.04.2007 commented "if"
//      }


        if ( $this->PersistenceList['regional_info']['site_charset'] )
        {
            $i18nINI = eZINI::create( 'i18n.ini' );
            // Set ReadOnlySettingsCheck to false: towards
            // Ignore site.ini[eZINISettings].ReadonlySettingList[] settings when saving ini variables.
            $i18nINI->setReadOnlySettingsCheck( false );

            $i18nINI->setVariable( 'CharacterSettings', 'Charset', $this->PersistenceList['regional_info']['site_charset'] );
            $i18nINI->save( false, '.php', 'append', true );
        }

        return true;
    }

    function init()
    {
        if ( $this->hasKickstartData() )
        {
            $data = $this->kickstartData();

            // kickstart.ini lists the primary among Languages[] or not, as it
            // likes: here only the additional ones count, so it is dropped
            // from them instead of being refused
            $primary = isset( $data['Primary'] ) ? $data['Primary'] : '';
            $extras = isset( $data['Languages'] ) ? (array)$data['Languages'] : array();
            $extras = array_values( array_diff( $extras, array( $primary ) ) );
            $choice = eZSetupValidateLanguageChoice( $primary, $extras );
            if ( $choice['errors'] )
            {
                // An unattended install with a language this installation has
                // no locale for: stop here and show the page with the reason,
                // instead of installing a site with a broken language setup
                foreach ( $choice['errors'] as $error )
                    eZDebug::writeError( 'kickstart.ini [language_options]: ' . $error, __METHOD__ );
                $this->LanguageErrors = $choice['errors'];
                $this->PostedChoice = array( 'primary_language' => $primary, 'extra_languages' => $extras );
                $this->setAllowKickstart( false );
                return false;
            }

            // The interactive path settles on utf-8 and says so; the kickstart
            // path left site_charset unset, so CreateSites fell back to
            // findAppropriateCharset() with use_unicode still at its default
            // false and picked iso-8859-1. Every unattended install therefore
            // came out latin-1 while a manual one came out utf-8, and
            // transliteration turned "Uber uns" into a-ber-uns.
            $regionalInfo = eZStepLanguageOptions::regionalInfoFromChoice( $choice );

            $this->PersistenceList['regional_info'] = $regionalInfo;
            $this->storePersistenceData();

            return $this->kickstartContinueNextStep();
        }

        return false;
    }

    function display()
    {
        $languages = false;
        $defaultLanguage = false;
        $defaultExtraLanguages = false;

        eZSetupLanguageList( $languages, $defaultLanguage, $defaultExtraLanguages );

        $this->Tpl->setVariable( 'language_list', $languages );

        $showUnicodeError = false;
        if ( isset( $this->Error ) )
        {
            $showUnicodeError = !$this->PersistenceList['database_info']['use_unicode'];
            $this->PersistenceList['database_info']['use_unicode'] = false;
        }
        $this->Tpl->setVariable( 'show_unicode_error', $showUnicodeError );

        // Preselection, first visit: the browser's most preferred language
        // the installation has a locale for as the primary, its other
        // accepted languages as additional ones. The language the bundled
        // content is in is not added: the installer keeps that content in
        // its own language and appends it to SiteLanguageList as a fallback,
        // and as a chosen language it would get a translation siteaccess of
        // its own.
        $dataLanguage = eZSetupBundledDataLanguage();
        $regionalInfo = array( 'primary_language' => $defaultLanguage,
                               'languages' => array_merge( array( $defaultLanguage ), $defaultExtraLanguages ) );
        if ( isset( $this->PersistenceList['regional_info'] ) )
            $regionalInfo = $this->PersistenceList['regional_info'];
        if ( !isset( $regionalInfo['enable_unicode'] ) )
            $regionalInfo['enable_unicode'] = true;
        $primaryLanguage = isset( $regionalInfo['primary_language'] ) ? $regionalInfo['primary_language'] : $defaultLanguage;
        $extraLanguages = array_values( array_diff( isset( $regionalInfo['languages'] ) ? (array)$regionalInfo['languages'] : array(),
                                                    array( $primaryLanguage ) ) );

        // A refused answer is shown as it was given, so the person sees what
        // to correct
        $languageErrors = $this->LanguageErrors ? $this->LanguageErrors : array();
        if ( $languageErrors && is_array( $this->PostedChoice ) )
        {
            $primaryLanguage = $this->PostedChoice['primary_language'];
            $extraLanguages = $this->PostedChoice['extra_languages'];
        }
        $regionalInfo['primary_language'] = $primaryLanguage;

        $this->Tpl->setVariable( 'regional_info', $regionalInfo );
        $this->Tpl->setVariable( 'extra_languages', $extraLanguages );
        $this->Tpl->setVariable( 'language_errors', $languageErrors );
        $this->Tpl->setVariable( 'data_language', $dataLanguage );
        $dataLocale = eZLocale::instance( $dataLanguage );
        $this->Tpl->setVariable( 'data_language_name', $dataLocale ? $dataLocale->attribute( 'intl_language_name' ) : $dataLanguage );

        // The default is to not use unicode if it has not been detected by
        // database driver to be OK.
        $databaseInfo = array( 'use_unicode' => false );
        if ( isset( $this->PersistenceList['database_info'] ) )
        {
            $databaseInfo = $this->PersistenceList['database_info'];
        }

        $this->Tpl->setVariable( 'database_info', $databaseInfo );

        $result = array();
        // Display template

        $result['content'] = $this->Tpl->fetch( "design:setup/init/language_options.tpl" );
        $result['path'] = array( array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                          'Language options' ),
                                        'url' => false ) );
        return $result;
    }


    /*!
     \static
     The regional_info the rest of the wizard reads, from a choice checked by
     eZSetupValidateLanguageChoice():

       primary_language  locale code of the primary language (eng-US)
       languages         every site language, the primary first, then the
                         additional ones in the order chosen
       extra_languages   the additional languages only (languages without
                         the primary)
       site_charset      always utf-8
    */
    static function regionalInfoFromChoice( $choice )
    {
        return array( 'language_type' => 1,
                      'primary_language' => $choice['primary_language'],
                      'languages' => $choice['languages'],
                      'extra_languages' => $choice['extra_languages'],
                      'enable_unicode' => true,
                      'site_charset' => 'utf-8' );
    }

    public $Error;
    public $LanguageErrors = array();
    public $PostedChoice = null;
}

?>
