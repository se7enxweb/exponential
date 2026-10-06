<?php
/**
 * The code of kernel/settings/view.php, moved into a class (#207 stage 1). The file kernel/settings/view.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 * What the page shows is worked out by expSettingsChain (which file sets a value and what it overrides),
 * expSettingsSecretRule (what is masked) and expSettingsPage (rows, search, comparison, the notice of a write);
 * this view reads the request, checks it against the installation's own lists (expSettingsTarget) and hands the
 * results to the template. User guide: doc/guides/settings-page.md
 */
/*
 * The original header of kernel/settings/view.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Settings
{

class View extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $tpl = \eZTemplate::factory();
        $http = \eZHTTPTool::instance();
        $siteIni = \eZINI::instance();
        $siteAccessList = array_values( array_map( 'strval', (array)$siteIni->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' ) ) );
        $iniFiles = \expSettingsPage::iniFileList();
        $extensions = \expSettingsPage::activeExtensions();
        $rule = \expSettingsSecretRule::fromIni( $siteIni );

        // The file and the siteaccess: only names the installation itself lists (no path can be smuggled in)
        $askedFile = $http->hasPostVariable( 'selectedINIFile' ) ? $http->postVariable( 'selectedINIFile' ) : $Params['INIFile'];
        $settingFile = \expSettingsTarget::iniFile( $askedFile, $iniFiles );
        $askedSiteAccess = $http->hasPostVariable( 'CurrentSiteAccess' ) ? $http->postVariable( 'CurrentSiteAccess' ) : $Params['SiteAccess'];
        $currentSiteAccess = \expSettingsTarget::siteAccess( $askedSiteAccess, $siteAccessList );
        if ( $currentSiteAccess === null )
            $currentSiteAccess = isset( $siteAccessList[0] ) ? $siteAccessList[0] : '';
        $unknownFile = $askedFile && $settingFile === null;

        // "Select": the file and siteaccess picked become the address, so the page can be bookmarked and reloaded
        if ( $http->hasPostVariable( 'ChangeINIFile' ) && $settingFile !== null && !$http->hasPostVariable( 'RemoveButton' ) )
            return $this->viewResult( null, $Module->redirectTo( '/settings/view/' . $currentSiteAccess . '/' . $settingFile ) );

        if ( $http->hasPostVariable( 'RemoveButton' ) && $settingFile !== null && $http->hasPostVariable( 'RemoveSettingsArray' ) )
        {
            $removed = self::remove( $settingFile, $currentSiteAccess, (array)$http->postVariable( 'RemoveSettingsArray' ), $extensions );
            \expSettingsPage::afterWrite( $removed ? 'removed' : 'unchanged', $settingFile, $currentSiteAccess, $removed );
            return $this->viewResult( null, $Module->redirectTo( '/settings/view/' . $currentSiteAccess . '/' . $settingFile ) );
        }

        // What to show: a search, the settings changed from the default, a comparison with another siteaccess
        $query = $http->hasGetVariable( 'q' ) && is_string( $http->getVariable( 'q' ) ) ? trim( $http->getVariable( 'q' ) ) : '';
        $query = mb_substr( (string)preg_replace( '/[\x00-\x1F\x7F]/u', '', $query ), 0, 100 );
        $searchAll = $http->hasGetVariable( 'scope' ) && $http->getVariable( 'scope' ) === 'all' && $query !== '';
        $changedOnly = $http->hasGetVariable( 'changed' ) && $http->getVariable( 'changed' ) === '1';
        $compareWith = $http->hasGetVariable( 'compare' ) ? \expSettingsTarget::siteAccess( $http->getVariable( 'compare' ), $siteAccessList ) : null;
        if ( $compareWith === $currentSiteAccess )
            $compareWith = null;

        $current = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : null;
        $page = false;
        $settings = false;
        $summary = false;
        $compareRows = false;
        $searchHits = false;
        if ( $settingFile !== null )
        {
            $loaded = \expSettingsPage::chainFor( $settingFile, $currentSiteAccess, false );
            $chain = $loaded['chain'];
            $ini = $loaded['ini'];
            $readOnly = function ( $block, $name ) use ( $ini, $settingFile ) {
                // eZINI::isSettingReadOnly() answers true when a setting is NOT read only
                return !$ini->isSettingReadOnly( $settingFile, $block, $name === false ? false : $name );
            };
            // What this server runs with: for the siteaccess of this page only (other siteaccesses are not loaded)
            $runtime = $currentSiteAccess === $current ? \eZINI::instance( $settingFile )->groups() : null;
            $page = \expSettingsPage::rows( $chain, $rule, array(
                'query' => $searchAll ? '' : $query, 'changed' => $changedOnly, 'runtime' => $runtime,
                'file' => $settingFile, 'siteaccess' => $currentSiteAccess, 'extensions' => $extensions,
                'restart' => \expSettingsPage::restartList(), 'readOnly' => $readOnly ) );
            $settings = \expSettingsPage::legacySettings( $chain, $rule, $readOnly );
            $summary = $chain->summary();
            $summary['secrets'] = $page['secrets'];
            $summary['pending'] = $runtime === null ? null
                : ( $query === '' && !$changedOnly ? $page['pending'] : \expSettingsPage::rows( $chain, $rule, array( 'runtime' => $runtime ) )['pending'] );
            $summary['paths'] = $chain->paths();
            $summary['layers'] = array();
            foreach ( $chain->layers() as $layer )
                $summary['layers'][] = array( 'path' => $layer['path'], 'placement' => $layer['placement'], 'unreadable' => $layer['unreadable'],
                                              'used' => $summary['files_used'][$layer['path']] );
            if ( $compareWith !== null )
            {
                $other = \expSettingsPage::chainFor( $settingFile, $compareWith, false );
                $compareRows = \expSettingsPage::compareRows( \expSettingsChain::compare( $chain, $other['chain'] ), $rule );
            }
        }
        if ( $searchAll )
        {
            $searchHits = array();
            foreach ( $iniFiles as $file )
            {
                $loaded = \expSettingsPage::chainFor( $file, $currentSiteAccess, false );
                $left = \expSettingsPage::SEARCH_LIMIT - count( $searchHits );
                if ( $left <= 0 )
                    break;
                $searchHits = array_merge( $searchHits, \expSettingsPage::search( $file, $loaded['chain'], $rule, $query, $left ) );
            }
        }

        // The files read most often first, as site.ini [SettingsViewSettings] CommonINIFileList says
        $commonINIFiles = array();
        foreach ( (array)$siteIni->variable( 'SettingsViewSettings', 'CommonINIFileList' ) as $candidate )
        {
            $candidate = trim( $candidate );
            if ( $candidate !== '' && in_array( $candidate, $iniFiles, true ) && !in_array( $candidate, $commonINIFiles, true ) )
                $commonINIFiles[] = $candidate;
        }

        $tpl->setVariable( 'settings', $settings );
        $tpl->setVariable( 'block_count', $summary ? $summary['blocks'] : false );
        $tpl->setVariable( 'setting_count', $summary ? $summary['settings'] : false );
        $tpl->setVariable( 'ini_file', $settingFile !== null ? $settingFile : false );
        $tpl->setVariable( 'ini_files', $iniFiles );
        $tpl->setVariable( 'ini_files_common', $commonINIFiles );
        $tpl->setVariable( 'ini_files_other', array_values( array_diff( $iniFiles, $commonINIFiles ) ) );
        $tpl->setVariable( 'siteaccess_list', $siteAccessList );
        $tpl->setVariable( 'current_siteaccess', $currentSiteAccess );
        // added with the redesign
        $tpl->setVariable( 'page', $page );
        $tpl->setVariable( 'summary', $summary );
        $tpl->setVariable( 'query', $query );
        $tpl->setVariable( 'search_all', $searchAll );
        $tpl->setVariable( 'search_hits', $searchHits );
        $tpl->setVariable( 'changed_only', $changedOnly );
        $tpl->setVariable( 'compare_with', $compareWith !== null ? $compareWith : false );
        $tpl->setVariable( 'compare_rows', $compareRows );
        $tpl->setVariable( 'unknown_file', $unknownFile );
        $tpl->setVariable( 'runtime_known', $settingFile !== null && $currentSiteAccess === $current );
        $tpl->setVariable( 'this_siteaccess', $current );
        $tpl->setVariable( 'served_by_velocity', \expSettingsPage::servedByVelocity() );
        $tpl->setVariable( 'notice', \expSettingsPage::takeNotice() );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:settings/view.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'settings/view', 'Settings' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'settings/view', 'View' ),
                                        'url' => false ) );

        return $this->viewResult( $Result, null );
    }

    /**
     * Takes the selected settings out of the file of the installation's own settings that sets them last
     * (expSettingsPage::removeFrom()), after checking that file is one the page may write.
     *
     * @param string $settingFile Checked
     * @param string $siteAccess Checked
     * @param array $selected "Block:Setting" values of RemoveSettingsArray[]
     * @param string[] $extensions
     * @return array[] block, name, path of what was removed
     */
    public static function remove( $settingFile, $siteAccess, array $selected, array $extensions )
    {
        $loaded = \expSettingsPage::chainFor( $settingFile, $siteAccess, false );
        $chain = $loaded['chain'];
        $ini = $loaded['ini'];
        $byPath = array();
        foreach ( $selected as $item )
        {
            if ( !is_string( $item ) || ( $pos = strrpos( $item, ':' ) ) === false )
                continue;
            $block = substr( $item, 0, $pos );
            $name = substr( $item, $pos + 1 );
            $setting = $chain->setting( $block, $name );
            if ( $setting === null || !$ini->isSettingReadOnly( $settingFile, $block, $name ) )
                continue;
            $path = \expSettingsPage::removeFrom( $setting );
            if ( $path === null || !\expSettingsTarget::isWritableChainFile( $path, $settingFile, $siteAccess, $extensions ) )
                continue;
            $byPath[$path][] = array( 'block' => $block, 'name' => $name, 'path' => $path );
        }
        $removed = array();
        foreach ( $byPath as $path => $items )
        {
            // direct access: this one file, read and written as it is (comments kept by eZINI's round trip)
            $file = new \eZINI( basename( $path ), dirname( $path ), null, false, null, true, true );
            foreach ( $items as $item )
                $file->removeSetting( $item['block'], $item['name'] );
            if ( $file->save() )
                $removed = array_merge( $removed, $items );
            else
                \eZDebug::writeError( 'Could not write ' . $path . ' to remove ' . count( $items ) . ' setting(s)', __METHOD__ );
        }
        return $removed;
    }
}

}
