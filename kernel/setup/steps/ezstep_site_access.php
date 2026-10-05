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

    /**
     * The editor siteaccess's default match value for a site whose public and
     * admin values are known, never one of theirs: two siteaccesses on one
     * port or host would make one of them unreachable.
     *
     *  - port: the public port + 2 (8080, 8081 -> 8082), the next free one
     *    when that is taken
     *  - hostname: edit.<public host without www.> (www.example.com ->
     *    edit.example.com), not edit.<the host the setup runs on>, which is
     *    localhost on the command line
     *  - url: editor, or <public>_editor when editor is taken
     *
     * @param string $accessType url, port or hostname
     * @param string|int $siteValue the public siteaccess's value
     * @param string|int $adminValue the admin siteaccess's value
     * @return string|int
     */
    static function distinctEditorAccessValue( $accessType, $siteValue, $adminValue )
    {
        $taken = array( (string)$siteValue, (string)$adminValue );
        if ( $accessType == 'port' )
        {
            $port = ctype_digit( (string)$siteValue ) ? (int)$siteValue + 2 : 8082;
            while ( in_array( (string)$port, $taken, true ) )
                ++$port;
            return $port;
        }
        if ( $accessType == 'hostname' )
        {
            $host = preg_replace( '#^[a-zA-Z0-9]+://#', '', trim( (string)$siteValue ) );
            $host = preg_replace( '#[:/].*$#', '', $host );
            if ( $host === '' )
                return self::defaultEditorAccessValue( 'hostname' );
            $host = preg_replace( '#^www\.#i', '', $host );
            $value = 'edit.' . $host;
            if ( in_array( $value, $taken, true ) )
                $value = 'editor.' . $host;
            return $value;
        }
        $value = (string)self::defaultEditorAccessValue( $accessType );
        if ( in_array( $value, $taken, true ) )
            $value = $siteValue . '_editor';
        return $value;
    }

    /**
     * The editor value of a chosen site type: the one it names, else the
     * distinct default for its public and admin values.
     *
     * @param array $siteType
     * @return string|int
     */
    static function editorAccessValueFor( array $siteType )
    {
        if ( isset( $siteType['editor_access_type_value'] ) && trim( (string)$siteType['editor_access_type_value'] ) !== '' )
            return $siteType['editor_access_type_value'];
        $accessType = isset( $siteType['access_type'] ) ? $siteType['access_type'] : 'url';
        if ( !isset( $siteType['access_type_value'] ) )
            return self::defaultEditorAccessValue( $accessType );
        return self::distinctEditorAccessValue( $accessType, $siteType['access_type_value'],
                                                isset( $siteType['admin_access_type_value'] ) ? $siteType['admin_access_type_value'] : '' );
    }
}

?>
