<?php
/**
 * File containing the eZStepFinal class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZStepFinal ezstep_final.php
  \brief The class eZStepFinal does

*/

class eZStepFinal extends eZStepInstaller
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
        parent::__construct( $tpl, $http, $ini, $persistenceList, 'final', 'Final' );
    }

    function processPostData()
    {
        return true; // Last step, but always proceede
    }

    function init()
    {
        eZCache::clearByID( 'global_ini' );
        $this->setUpCrontab();
        return false; // Always show
    }

    /**
     * The cron job of the new installation (doc/eng-us/bc/6.0/installer-crontab.md): the crontab lines that run its
     * cronjob parts, in the crontab of the user that owns the installation, or the lines to add by hand when no
     * crontab can be written. Never fails the installation; a second call finds the lines there already.
     *
     * @return array|null the result of expCrontabSetup::forNewInstallation(), null without the class
     */
    function setUpCrontab()
    {
        if ( $this->CrontabResult !== null || !class_exists( 'expCrontabSetup' ) )
            return $this->CrontabResult;
        $this->CrontabResult = expCrontabSetup::forNewInstallation( expCrontabSetup::root() );
        foreach ( $this->CrontabResult['lines'] as $line )
            eZLog::write( 'eZStepFinal: ' . $line, 'setup.log' );
        return $this->CrontabResult;
    }

    /** @var array|null what setUpCrontab() did */
    public $CrontabResult = null;

    function display()
    {
        $siteType = $this->chosenSiteType();

        $siteaccessURLs = $this->siteaccessURLs();

        $siteType['url'] = $siteaccessURLs['url'];
        $siteType['admin_url'] = $siteaccessURLs['admin_url'];
        $siteType['editor_url'] = $siteaccessURLs['editor_url'];

        $customText = isset( $this->PersistenceList['final_text'] ) ? $this->PersistenceList['final_text'] : '';

        $this->Tpl->setVariable( 'site_type', $siteType );

        $this->Tpl->setVariable( 'custom_text', $customText );

        $crontab = $this->setUpCrontab();
        if ( $crontab !== null )
            $this->Tpl->setVariable( 'crontab', $crontab );

        $this->Tpl->setVariable( 'setup_previous_step', 'Final' );
        $this->Tpl->setVariable( 'setup_next_step', 'Final' );

        $result = array();
        // Display template
        $result['content'] = $this->Tpl->fetch( 'design:setup/init/final.tpl' );
        $result['path'] = array( array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                          'Finished' ),
                                        'url' => false ) );
        return $result;

    }
}

?>
