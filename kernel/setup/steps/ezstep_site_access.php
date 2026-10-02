<?php
/**
 * File containing the eZStepSiteAccess class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZStepSiteAccess ezstep_site_access.php
  \brief The class eZStepSiteAccess does

*/

class eZStepSiteAccess extends eZStepInstaller
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
        parent::__construct( $tpl, $http, $ini, $persistenceList, 'site_access', 'Site access' );
    }

    function processPostData()
    {
        $accessType = null;
        if( $this->Http->hasPostVariable( 'eZSetup_site_access' ) )
        {
            $accessType = $this->Http->postVariable( 'eZSetup_site_access' );
        }
        else
        {
            return false; // unknown error
        }

        $siteType = $this->chosenSiteType();

        $siteType['access_type'] = $accessType;

        $this->setAccessValues( $siteType );

        $this->storeSiteType( $siteType );
        return true;
    }

    function init()
    {
        if ( $this->hasKickstartData() )
        {
            $data = $this->kickstartData();

            $siteType = $this->chosenSiteType();

            $accessType = $data['Access'];
            if ( in_array( $accessType,
                           array( 'url', 'port', 'hostname' ) ) )
            $siteType['access_type'] = $accessType;

            $this->setAccessValues( $siteType );
            $this->storeSiteType( $siteType );
            return $this->kickstartContinueNextStep();
        }

        $siteType = $this->chosenSiteType();

        // If windows installer, install using url site access
        if ( eZSetupTestInstaller() == 'windows' )
        {
            $siteType['access_type'] = 'url';
            $siteType['access_type_value'] = $siteType['identifier'];
            $siteType['admin_access_type_value'] = $siteType['identifier'] . '_admin';
            $siteType['editor_access_type_value'] = self::defaultEditorAccessValue( 'url' );

            $this->storeSiteType( $siteType );

            return true;
        }

        if ( !isset( $siteType['access_type'] ) )
            $siteType['access_type'] = 'url';

        $this->storeSiteType( $siteType );
        return false; // Always show site access
    }

    function display()
    {
        $siteType = $this->chosenSiteType();
        $this->Tpl->setVariable( 'site_type', $siteType );
//         $this->Tpl->setVariable( 'error', $this->Error );

        // Return template and data to be shown
        $result = array();
        // Display template
        $result['content'] = $this->Tpl->fetch( 'design:setup/init/site_access.tpl' );
        $result['path'] = array( array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                          'Site access' ),
                                        'url' => false ) );
        return $result;
    }

    function setAccessValues( &$siteType )
    {
        $accessType = $siteType['access_type'];
        if ( $accessType == 'url' )
        {
            $siteType['access_type_value'] = $siteType['identifier'];
            $siteType['admin_access_type_value'] = $siteType['identifier'] . '_admin';
        }
        else if ( $accessType == 'port' )
        {
            $siteType['access_type_value'] = 8080;        // default port values
            $siteType['admin_access_type_value'] = 8081;
        }
        else if ( $accessType == 'hostname' )
        {
            $siteType['access_type_value'] = $siteType['identifier'] . '.' . eZSys::hostName();
            $siteType['admin_access_type_value'] = $siteType['identifier'] . '-admin.' . eZSys::hostName();
        }
        else
        {
            $siteType['access_type_value'] = $accessType;
            $siteType['admin_access_type_value'] = $accessType . '_admin';
        }
        $siteType['editor_access_type_value'] = self::defaultEditorAccessValue( $accessType );
    }

    /**
     * The editor siteaccess's default match value: the admin for content
     * editing only, on edit.<host> for hostname matching (edit.yourdomain.com).
     *
     * @param string $accessType url, port or hostname
     * @return string|int
     */
    static function defaultEditorAccessValue( $accessType )
    {
        if ( $accessType == 'port' )
            return 8082;
        if ( $accessType == 'hostname' )
            return 'edit.' . eZSys::hostName();
        if ( $accessType == 'url' )
            return 'editor';
        return $accessType . '_editor';
    }
}

?>
