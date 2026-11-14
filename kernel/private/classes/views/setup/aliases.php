<?php
/**
 * The code of kernel/setup/aliases.php: Setup > Image aliases (setup/aliases).
 *
 * One page in five parts, each a view parameter (tab): the dashboard, bulk runs, test tools, the run history and the
 * settings. Reading needs setup/aliases; every action is a POST with the form token and needs setup/managealiases
 * ("verify it's you" asks first, [ReauthGroup_system] setup/*). The logic is expImageAliasAdmin's; this class reads the
 * request and draws the page. Filters and the image looked at are view parameters
 * (/(tab)/history/(event)/fail, /(tab)/tools/(node)/42); a filter form sent without the page's script is a POST answered
 * with 303 to that address. A query string is never read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Setup
{

class Aliases extends \Exponential\Runnable\ModuleView
{
    /** The translation context of the page */
    const CONTEXT = 'design/admin/setup/aliases';

    /** The actions that change something (each needs setup/managealiases) */
    const ACTIONS = array( 'SaveSettings', 'PreviewSettings', 'StartRun', 'PauseBulk', 'ResumeBulk', 'CancelJob', 'MakeAlias',
                           'TestSource', 'ResetSource', 'RotateKey', 'NodeAction' );

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $t = function ( $text, array $values = array() ) {
            return \ezpI18n::tr( self::CONTEXT, $text, null, $values );
        };

        // The filter and lookup forms without the page's script: a POST answered with the address of what was asked
        if ( \expViewParameters::redirectsPost( $_POST, array( 'HistoryFilterButton' ) ) )
            \expViewParameters::redirect( 'setup/aliases' . \expViewParameters::suffix( array(
                'tab' => 'history', 'event' => \expViewParameters::posted( $_POST, 'HistoryEvent' ),
                'run' => \expViewParameters::posted( $_POST, 'HistoryRun' ), 'alias' => \expViewParameters::posted( $_POST, 'HistoryAlias' ),
                'text' => \expViewParameters::posted( $_POST, 'HistoryText' ), 'lines' => \expViewParameters::posted( $_POST, 'HistoryLines' ) ) ) );
        if ( \expViewParameters::redirectsPost( $_POST, array( 'TestImageButton' ) ) )
        {
            $lookup = trim( \expViewParameters::posted( $_POST, 'TestImageID' ) );
            $kind = \expViewParameters::posted( $_POST, 'TestImageKind' ) === 'object' ? 'object' : 'node';
            \expViewParameters::redirect( 'setup/aliases' . \expViewParameters::suffix( array( 'tab' => 'tools',
                $kind => preg_match( '/^\d{1,10}$/', $lookup ) ? $lookup : null ) ) );
        }

        $tab = \expImageAliasAdmin::tab( \expViewParameters::text( $Params, 'tab', 'Tab' ) );
        $canManage = \expImageAliasAdmin::can( 'managealiases' );
        $user = \eZUser::currentUser();
        $login = (string)$user->attribute( 'login' );
        $feedback = array();
        $jobID = (int)\expViewParameters::text( $Params, 'job', 'Job' );
        $made = null;
        $sourceTest = null;
        $settingsResult = null;
        $settingsErrors = array();
        $settingsInput = null;
        $runResult = null;
        $runErrors = array();
        $runInput = array( 'action' => 'generate', 'target' => 'all', 'node' => '', 'class' => '', 'attribute' => '', 'alias' => array(),
                           'since' => '', 'limit' => '', 'dry_run' => false, 'overwrite' => false );

        $asked = null;
        foreach ( self::ACTIONS as $action )
        {
            if ( $module->isCurrentAction( $action ) )
                $asked = $action;
        }
        if ( $asked !== null && !$canManage )
        {
            $feedback[] = array( 'bad', $t( 'Changing image aliases needs the policy setup/managealiases. Nothing was changed.' ) );
            $asked = null;
        }

        switch ( $asked )
        {
            case 'SaveSettings':
            case 'PreviewSettings':
                $tab = 'settings';
                $settingsInput = $http->hasPostVariable( 'Setting' ) && is_array( $http->postVariable( 'Setting' ) ) ? $http->postVariable( 'Setting' ) : array();
                $inherit = $http->hasPostVariable( 'Inherit' ) && is_array( $http->postVariable( 'Inherit' ) ) ? $http->postVariable( 'Inherit' ) : array();
                $planned = \expImageAliasAdmin::settingsChanges( $settingsInput, $inherit, \expImageAliasAdmin::settingsState() );
                $settingsErrors = $planned['errors'];
                if ( $settingsErrors )
                    $feedback[] = array( 'bad', $t( 'Nothing was saved: %count settings have a value that is not allowed. They are marked below.', array( '%count' => count( $settingsErrors ) ) ) );
                else if ( !$planned['changes'] )
                {
                    $feedback[] = array( 'info', $t( 'Nothing to save: every setting already has the value in the form.' ) );
                    $settingsInput = null;
                }
                else
                {
                    $settingsResult = \expImageAliasAdmin::applySettings( $planned['changes'], $asked === 'PreviewSettings' );
                    $settingsResult['changed'] = $planned['changed'];
                    $settingsResult['preview'] = $asked === 'PreviewSettings';
                    if ( !$settingsResult['ok'] )
                        $feedback[] = array( 'bad', $t( 'The settings could not be written: %error', array( '%error' => $settingsResult['error'] ) ) );
                    else if ( $settingsResult['preview'] )
                        $feedback[] = array( 'info', $t( 'Preview: %count settings would change in %file. Nothing was written yet.', array( '%count' => count( $planned['changed'] ), '%file' => $settingsResult['path'] ) ) );
                    else
                    {
                        $feedback[] = array( 'ok', $t( 'Saved: %count settings changed in %file. The settings cache was cleared.', array( '%count' => count( $planned['changed'] ), '%file' => $settingsResult['path'] ) ) );
                        \eZINI::resetInstance( 'image.ini' );
                        $settingsInput = null;
                    }
                }
                break;

            case 'StartRun':
            case 'NodeAction':
                $tab = 'bulk';
                if ( $asked === 'NodeAction' )
                {
                    $runInput = array( 'action' => (string)$http->postVariable( 'NodeActionButton', '' ),
                                       'target' => (string)$http->postVariable( 'NodeScope', 'node' ) === 'subtree' ? 'subtree' : 'node',
                                       'node' => (string)$http->postVariable( 'NodeID', '' ) ) + $runInput;
                }
                else
                {
                    $posted = $http->hasPostVariable( 'Run' ) && is_array( $http->postVariable( 'Run' ) ) ? $http->postVariable( 'Run' ) : array();
                    foreach ( array( 'action', 'target', 'node', 'class', 'attribute', 'since', 'limit' ) as $key )
                        $runInput[$key] = isset( $posted[$key] ) && is_scalar( $posted[$key] ) ? (string)$posted[$key] : '';
                    $runInput['alias'] = isset( $posted['alias'] ) && is_array( $posted['alias'] ) ? array_values( array_filter( $posted['alias'], 'is_scalar' ) ) : array();
                    $runInput['dry_run'] = !empty( $posted['dry_run'] );
                    $runInput['overwrite'] = !empty( $posted['overwrite'] );
                }
                if ( $runInput['action'] === 'remove' )
                    $runInput['action'] = 'purge';
                $runScope = \expImageAliasAdmin::scopeFromInput( $runInput, \expImageAliasAdmin::knownAliases() );
                $runErrors = $runScope['errors'];
                if ( $runErrors )
                {
                    $feedback[] = array( 'bad', $t( 'The run was not started: check the marked fields.' ) );
                    break;
                }
                $runResult = \expImageAliasAdmin::startRun( $runScope ) + array( 'action' => $runScope['action'], 'notes' => $runScope['notes'],
                                                                                'scope' => \expImageAliasStager::describeScope( $runScope['options'] ) );
                if ( !$runResult['ok'] )
                {
                    $feedback[] = array( 'bad', $runResult['message'] === 'not-staging'
                        ? $t( 'A resync needs a staging installation: [AliasStaging] Environment other than pro, SyncFrom and the source\'s BaseURL.' )
                        : $t( 'Without the job queue a run may handle at most %count images in this request. Narrow the scope or set up expqueue.', array( '%count' => \expImageAliasAdmin::SYNC_LIMIT ) ) );
                }
                else if ( $runResult['mode'] === 'preview' )
                    $feedback[] = array( 'info', $t( 'Dry run: %images images in the scope, %aliases aliases each. Nothing was changed.', array( '%images' => (int)$runResult['count'], '%aliases' => count( $runResult['aliases'] ) ) ) );
                else if ( $runResult['mode'] === 'queue' )
                {
                    $jobID = (int)$runResult['job'];
                    $feedback[] = array( 'ok', $t( 'The run was queued as job %job (run %run). Its progress is shown below.', array( '%job' => $jobID, '%run' => $runResult['run'] ) ) );
                }
                else
                    $feedback[] = array( 'ok', $t( 'The run was done in this request (no job queue): run %run.', array( '%run' => $runResult['run'] ) ) );
                if ( $runResult['ok'] && in_array( 'overwrite-means-regenerate', $runResult['notes'], true ) )
                    $feedback[] = array( 'info', $t( 'Generate makes only what is missing; to make existing aliases again, use Regenerate. Overwrite applies to a resync.' ) );
                break;

            case 'PauseBulk':
                $tab = 'bulk';
                \expImageAliasThrottle::pauseBulk( trim( mb_substr( (string)$http->postVariable( 'PauseReason', '' ), 0, 200 ) ), $login );
                $feedback[] = array( 'ok', $t( 'Bulk runs are paused on every server. They stop after their current image and go on when resumed.' ) );
                break;

            case 'ResumeBulk':
                $tab = 'bulk';
                $was = \expImageAliasThrottle::resumeBulk( $login );
                $feedback[] = $was ? array( 'ok', $t( 'Bulk runs go on. A deferred job continues at its next attempt.' ) )
                                   : array( 'info', $t( 'Bulk runs were not paused.' ) );
                break;

            case 'CancelJob':
                $tab = 'bulk';
                $cancel = (int)$http->postVariable( 'CancelJobButton', 0 );
                $state = \expImageAliasAdmin::cancelJob( $cancel );
                $jobID = $cancel;
                if ( $state === 'cancelled' )
                    $feedback[] = array( 'ok', $t( 'Job %job was cancelled before it started.', array( '%job' => $cancel ) ) );
                else if ( $state === 'running' )
                    $feedback[] = array( 'ok', $t( 'Job %job stops after the image it is working on.', array( '%job' => $cancel ) ) );
                else
                    $feedback[] = array( 'info', $t( 'Job %job has already ended or is not a job of image aliases.', array( '%job' => $cancel ) ) );
                break;

            case 'MakeAlias':
                $tab = 'tools';
                list( $attribute, $version, $language, $alias, $siteAccess ) = array_pad( explode( '|', (string)$http->postVariable( 'MakeAliasButton', '' ) ), 5, '' );
                $made = \expImageAliasAdmin::makeOne( (int)$attribute, (int)$version, $language, $alias, $siteAccess );
                $feedback[] = $made['ok'] ? array( 'ok', $t( 'Alias %alias: %result in %ms ms.', array( '%alias' => $made['alias'], '%result' => $made['result'], '%ms' => $made['ms'] ) ) )
                                          : array( 'bad', $t( 'Alias %alias was not made: %result.', array( '%alias' => $made['alias'], '%result' => $made['result'] ) ) );
                break;

            case 'TestSource':
                $tab = 'tools';
                $sourceTest = \expImageAliasAdmin::testSource( (string)$http->postVariable( 'SourceEnvironment', '' ), (string)$http->postVariable( 'SourcePath', '' ) );
                break;

            case 'ResetSource':
                $tab = 'dashboard';
                $staging = \expImageAliasSettings::staging();
                if ( $staging['sync_from'] !== '' )
                {
                    $stager = new \expImageAliasStager( $staging );
                    $stager->resetBreaker();
                    $feedback[] = array( 'ok', $t( 'The source %source may be asked again: its failures and pause were reset.', array( '%source' => $staging['sync_from'] ) ) );
                }
                break;

            case 'RotateKey':
                $tab = 'dashboard';
                $feedback[] = \expImageAliasUrl::rotateKey()
                    ? array( 'ok', $t( 'A new signing key is in use. Addresses signed with the old one stay valid for the grace period.' ) )
                    : array( 'bad', $t( 'The signing key could not be changed: the secret store is not available.' ) );
                break;
        }

        // What the parts of the page need
        $tpl = \eZTemplate::factory();
        $plan = \expImageAliasSettings::siteAccessAliasPlan();
        $tpl->setVariable( 'tab', $tab );
        $tpl->setVariable( 'tabs', \expImageAliasAdmin::TABS );
        $tpl->setVariable( 'can_manage', $canManage );
        $tpl->setVariable( 'feedback', $feedback );
        $tpl->setVariable( 'job_id', $jobID );
        $tpl->setVariable( 'job', $jobID > 0 ? \expImageAliasAdmin::job( $jobID ) : null );
        $tpl->setVariable( 'environments', \expImageAliasStager::ENVIRONMENTS );
        $tpl->setVariable( 'log_path', \expImageAliasLog::path() );
        if ( $tab === 'dashboard' )
            $tpl->setVariable( 'dashboard', \expImageAliasAdmin::dashboard() );
        if ( $tab === 'bulk' )
        {
            $tpl->setVariable( 'jobs', \expImageAliasAdmin::jobs( 10 ) );
            $tpl->setVariable( 'bulk_pause', \expImageAliasThrottle::bulkPause() );
            $tpl->setVariable( 'queue', \expImageAliasAdmin::queueState() );
            $tpl->setVariable( 'alias_plan', $plan );
            $tpl->setVariable( 'staging_active', \expImageAliasStager::forRequest() !== null );
            $tpl->setVariable( 'run_input', $runInput );
            $tpl->setVariable( 'run_errors', $runErrors );
            $tpl->setVariable( 'run_result', $runResult );
            $tpl->setVariable( 'sync_limit', \expImageAliasAdmin::SYNC_LIMIT );
        }
        if ( $tab === 'tools' )
        {
            $node = (int)\expViewParameters::text( $Params, 'node', 'Node' );
            $object = (int)\expViewParameters::text( $Params, 'object', 'Object' );
            if ( $asked === 'MakeAlias' )
            {
                $node = (int)$http->postVariable( 'TestNodeID', 0 );
                $object = (int)$http->postVariable( 'TestObjectID', 0 );
            }
            $tpl->setVariable( 'test_node', $node );
            $tpl->setVariable( 'test_object', $object );
            $tpl->setVariable( 'test_image', $node > 0 || $object > 0 ? \expImageAliasAdmin::testImage( $node, $object ) : null );
            $tpl->setVariable( 'made', $made );
            $tpl->setVariable( 'source_test', $sourceTest );
            $sources = array();
            foreach ( \expImageAliasStager::ENVIRONMENTS as $environment )
            {
                $source = \expImageAliasSettings::source( $environment );
                if ( $source['base_url'] !== '' )
                    $sources[] = array( 'environment' => $environment, 'base_url' => $source['base_url'], 'credential' => $source['credential'] !== '' );
            }
            $tpl->setVariable( 'sources', $sources );
        }
        if ( $tab === 'history' )
        {
            $filter = \expImageAliasAdmin::logFilter( array(
                'event' => \expViewParameters::text( $Params, 'event', 'Event' ), 'run' => \expViewParameters::text( $Params, 'run', 'Run' ),
                'alias' => \expViewParameters::text( $Params, 'alias', 'Alias' ), 'text' => \expViewParameters::text( $Params, 'text', 'Text' ),
                'lines' => \expViewParameters::text( $Params, 'lines', 'Lines' ) ?: 100 ) );
            $tpl->setVariable( 'filter', $filter );
            $tpl->setVariable( 'entries', \expImageAliasAdmin::readLog( $filter ) );
            $tpl->setVariable( 'events', array_merge( array( 'runs' ), \expImageAliasLog::EVENTS ) );
            $tpl->setVariable( 'feed_suffix', \expViewParameters::suffix( array( 'log' => 1, 'event' => $filter['event'], 'run' => $filter['run'],
                                                                                'alias' => $filter['alias'], 'text' => $filter['text'], 'lines' => $filter['lines'] ) ) );
        }
        if ( $tab === 'settings' )
        {
            $state = \expImageAliasAdmin::settingsState();
            $tpl->setVariable( 'settings', self::settingsRows( $state, $settingsInput, $settingsErrors ) );
            $tpl->setVariable( 'secret_names', \expImageAliasAdmin::secretNames() );
            $tpl->setVariable( 'settings_result', $settingsResult );
            $tpl->setVariable( 'settings_errors', $settingsErrors );
        }

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/aliases.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'kernel/setup', 'Setup' ) ),
                                 array( 'url' => false, 'text' => $t( 'Image aliases' ) ) );
        return $this->viewResult( $Result, null );
    }

    /**
     * The settings as rows for the form: per block its variables with the definition, the value in force (or the one
     * sent back when the form had an error), whether settings/override sets it, the error.
     *
     * @param array $state expImageAliasAdmin::settingsState()
     * @param array|null $input the submitted values to show again (a failed save), or null
     * @param array $errors block.variable => reason
     * @return array block => array( name, variables => list of array( name, field, type, values, min, max, value, text,
     *               overridden, error, default ) )
     */
    public static function settingsRows( array $state, $input, array $errors )
    {
        $out = array();
        foreach ( \expImageAliasAdmin::allDefinitions() as $block => $variables )
        {
            $rows = array();
            foreach ( $variables as $variable => $def )
            {
                $current = $state[$block][$variable];
                $value = $current['value'];
                if ( is_array( $input ) && isset( $input[$block] ) && is_array( $input[$block] ) && array_key_exists( $variable, $input[$block] ) && is_scalar( $input[$block][$variable] ) )
                    $value = $def['type'] === 'list' ? preg_split( '/\r\n|\r|\n/', (string)$input[$block][$variable] ) : (string)$input[$block][$variable];
                $rows[] = array( 'name' => $variable, 'block' => $block, 'type' => $def['type'],
                                 'values' => isset( $def['values'] ) ? $def['values'] : array(),
                                 'min' => isset( $def['min'] ) ? $def['min'] : null, 'max' => isset( $def['max'] ) ? $def['max'] : null,
                                 'value' => is_array( $value ) ? '' : (string)$value,
                                 'text' => is_array( $value ) ? implode( "\n", array_map( 'strval', $value ) ) : (string)$value,
                                 'overridden' => (bool)$current['overridden'],
                                 'default' => is_array( $def['default'] ) ? implode( ', ', $def['default'] ) : (string)$def['default'],
                                 'error' => isset( $errors["$block.$variable"] ) ? $errors["$block.$variable"] : '' );
            }
            $out[$block] = array( 'name' => $block, 'variables' => $rows );
        }
        return $out;
    }
}

}
