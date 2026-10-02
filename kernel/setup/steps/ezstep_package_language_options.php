<?php
/**
 * File containing the eZStepPackageLanguageOptions class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZStepPackageLanguageOptions ezstep_package_language_options.php
  \brief The class eZStepPackageLanguageOptions does

*/

class eZStepPackageLanguageOptions extends eZStepInstaller
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
        parent::__construct( $tpl, $http, $ini, $persistenceList, 'package_language_options', 'Package language options' );
    }

    function processPostData()
    {
        $languageMap = array();
        if( $this->Http->hasPostVariable( 'eZSetupPackageLanguageMap' ) )
        {
            $languageMap = $this->Http->postVariable( 'eZSetupPackageLanguageMap' );
        }

        // Add site languages.
        $siteLanguageLocaleList = (array) $this->PersistenceList['regional_info']['languages'];
        foreach( $siteLanguageLocaleList as $siteLanguage )
            $languageMap[$siteLanguage] = $siteLanguage;

        $this->PersistenceList['package_info']['language_map'] = $languageMap;

        return true;
    }

    function init()
    {
        /*
        if( $this->hasKickstartData() )
        {
            $data = $this->kickstartData();

            return $this->kickstartContinueNextStep();
        }
        */

        //
        // Get all available languages
        //
        $languages = false;
        $defaultLanguage = false;
        $defaultExtraLanguages = false;

        eZSetupLanguageList( $languages, $defaultLanguage, $defaultExtraLanguages );


        //
        // Get info about package and site languages
        //
        $siteLanguageLocaleList = (array) $this->PersistenceList['regional_info']['languages'];

        $packageNameList = array();
        $packageLanguageLocaleList = array();

        $sitePackageName = $this->chosenSitePackage();
        $sitePackage = eZPackage::fetch( $sitePackageName, false, false, false );
        if( is_object( $sitePackage ) )
        {
            $dependencies = $sitePackage->attribute( 'dependencies' );
            $requirements = $dependencies['requires'] ?? [];

            foreach( $requirements as $req )
            {
                $packageNameList[] = $req['name'];
            }

            $packageLanguageLocaleList = eZPackage::languageInfoFromPackageList( $packageNameList, false );
        }

        //
        // Exclude languages which exist both in packages and site, and the
        // clean data's language, which is never offered for mapping: the base
        // data (share/db_data.dba) stays in it whatever is chosen here, so
        // mapping the packages' copy of it onto the primary language split
        // one site across two languages. With eng-GB chosen, the package
        // objects came in as eng-GB while the base objects and their ids
        // stayed eng-US, and the package post-install aborted on an object
        // without attributes in the language it looked in. It is kept as a
        // content language and becomes the fallback in SiteLanguageList.
        //
        $packageLanguageLocaleList = array_diff( $packageLanguageLocaleList, $siteLanguageLocaleList,
                                                 array( eZStepInstaller::CLEAN_DATA_LANGUAGE ) );
        // A mapping chosen on an earlier visit no longer applies once there
        // is nothing to map: the packages then install in their own languages
        if( count( $packageLanguageLocaleList ) == 0 )
            unset( $this->PersistenceList['package_info']['language_map'] );

        if( count( $packageLanguageLocaleList ) > 0 )
        {
            //
            // Get language names
            //
            $siteLanguageList = array();
            $packageLanguageList = array();
            foreach( $languages as $language )
            {
                $locale = $language->attribute( 'locale_code' );
                $name = $language->attribute( 'intl_language_name' );

                if( in_array( $locale, $siteLanguageLocaleList ) )
                {
                    $siteLanguageList[] = array( 'locale' => $locale,
                                                 'name' => $name );
                }

                if( in_array( $locale, $packageLanguageLocaleList ) )
                {
                    $packageLanguageList[] = array( 'locale' => $locale,
                                                    'name' => $name );
                }
            }

            $this->MissedPackageLanguageList = $packageLanguageList;
            $this->SiteLanguageList = $siteLanguageList;

            return false;
        }

        // There are no language conflicts => proceed with next step
        return true;
    }

    function display()
    {
        $packageLanguageList = $this->MissedPackageLanguageList;
        $siteLanguageList = $this->SiteLanguageList;

        $this->Tpl->setVariable( 'package_language_list', $packageLanguageList );
        $this->Tpl->setVariable( 'site_language_list', $siteLanguageList );

        $result = array();
        $result['content'] = $this->Tpl->fetch( "design:setup/init/package_language_options.tpl" );
        $result['path'] = array( array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                          'Package language options' ),
                                        'url' => false ) );
        return $result;
    }

    public $MissedPackageLanguageList;
    public $SiteLanguageList;
}
?>
