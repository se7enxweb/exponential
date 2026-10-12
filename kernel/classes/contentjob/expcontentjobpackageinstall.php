<?php
/**
 * File containing the expContentJobPackageInstall class.
 *
 * The content job 'packageinstall': installs a package of the package repository in the background, as the user
 * who asked for it, with the choices made in the admin's install wizard (Setup > Packages > Install, "Install in
 * the background"): the siteaccess and top node maps, the dates, and whether the content objects are installed as
 * drafts. A large package (thousands of content objects) then never fails from the web request's time limit; the
 * job page (content/job/<id>) and the package page show its progress, object by object.
 *
 * Parameters: package (name, in the local repository), items (indexes into the package's install items, all when
 * empty), install (the install parameters: as_drafts, drafts_existing, site_access_map, top_nodes_map, design_map,
 * language_map, use_dates_from_package, restore_dates).
 *
 * One batch: the install itself (the content handler commits every 50 objects, so a very large package does not
 * hold one transaction). A job resumed after a crash installs the package again: as drafts with drafts_existing
 * "skip", objects already written are left alone; with "update" they are given one more draft.
 * The result: drafts, waiting, skipped (as eZPackage::install() reports them), objects. An install as drafts fires
 * package/install/drafts when it is finished (eZPackage::notifyDraftsInstalled(), context via "background").
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobPackageInstall implements expContentJobType
{
    /** the install parameters a job keeps (everything else of the wizard's session data is the wizard's own) */
    const INSTALL_KEYS = array( 'as_drafts', 'drafts_existing', 'site_access_map', 'top_nodes_map', 'design_map', 'language_map',
                                'use_dates_from_package', 'restore_dates' );

    /**
     * The install parameters of the wizard's session data, as a job keeps them.
     *
     * @param array $persistentData
     * @return array
     */
    public static function installParameters( array $persistentData )
    {
        $install = array();
        foreach ( self::INSTALL_KEYS as $key )
            if ( array_key_exists( $key, $persistentData ) )
                $install[$key] = $persistentData[$key];
        return $install;
    }

    /** @return bool whether background installs are possible here (the job type is registered) */
    public static function available()
    {
        return class_exists( 'expContentJob' ) && isset( expContentJob::types()['packageinstall'] );
    }

    /** @return string */
    protected static function tr( $text, array $arguments = array() )
    {
        return ezpI18n::tr( 'kernel/package', $text, null, $arguments );
    }

    /** @return eZPackage|false */
    protected static function package( $name )
    {
        return eZPackageRequestGuard::isSafeName( (string)$name ) ? eZPackage::fetch( (string)$name ) : false;
    }

    public function validate( array $params, eZUser $user )
    {
        $name = isset( $params['package'] ) ? (string)$params['package'] : '';
        $package = self::package( $name );
        if ( !$package instanceof eZPackage )
            throw new expContentJobException( self::tr( 'The package %1 does not exist.', array( '%1' => $name ) ) );
        $access = $user->hasAccessTo( 'package', 'install' );
        if ( $access['accessWord'] === 'no' )
            throw new expContentJobException( self::tr( 'You may not install packages (package/install).' ) );
        $install = isset( $params['install'] ) && is_array( $params['install'] ) ? self::installParameters( $params['install'] ) : array();
        $install['as_drafts'] = !empty( $install['as_drafts'] );
        $install['drafts_existing'] = isset( $install['drafts_existing'] ) && $install['drafts_existing'] === 'skip' ? 'skip' : 'update';
        foreach ( isset( $install['top_nodes_map'] ) ? (array)$install['top_nodes_map'] : array() as $top )
        {
            $nodeID = is_array( $top ) && isset( $top['new_node_id'] ) ? (int)$top['new_node_id'] : 0;
            if ( $nodeID > 0 && !eZContentObjectTreeNode::fetch( $nodeID ) )
                throw new expContentJobException( self::tr( 'The node %1 chosen for the package\'s content does not exist.', array( '%1' => $nodeID ) ) );
        }
        $count = count( $package->installItemsList( false, eZSys::osType() ) );
        $items = array();
        foreach ( isset( $params['items'] ) ? (array)$params['items'] : array() as $index )
            if ( (int)$index >= 0 && (int)$index < $count )
                $items[] = (int)$index;
        if ( !$items )
            $items = $count ? range( 0, $count - 1 ) : array();
        return array( 'package' => (string)$package->attribute( 'name' ), 'items' => array_values( array_unique( $items ) ), 'install' => $install,
                      'objects' => (int)expPackageConflictReport::build( $package, 0 )['object_total'] );
    }

    public function countNodes( array $params )
    {
        if ( isset( $params['objects'] ) )
            return (int)$params['objects'];
        $package = self::package( isset( $params['package'] ) ? $params['package'] : '' );
        return $package ? (int)expPackageConflictReport::build( $package, 0 )['object_total'] : 0;
    }

    public function locks( array $params )
    {
        // an install creates content: it takes no subtree lock (a remove or move of the top node is refused by its
        // own job's lock, not by this one)
        return array();
    }

    public function describe( array $params )
    {
        $objects = isset( $params['objects'] ) ? (int)$params['objects'] : 0;
        return !empty( $params['install']['as_drafts'] )
            ? self::tr( 'Install the package %1 as drafts (%2 content objects)', array( '%1' => $params['package'], '%2' => $objects ) )
            : self::tr( 'Install the package %1 (%2 content objects)', array( '%1' => $params['package'], '%2' => $objects ) );
    }

    public function prepare( expContentJob $job )
    {
        $cp =& $job->checkpoint();
        if ( !isset( $cp['installed'] ) )
            $cp['installed'] = false;
        if ( !self::package( $job->params()['package'] ) )
            throw new expContentJobException( self::tr( 'The package %1 does not exist (any more).', array( '%1' => $job->params()['package'] ) ) );
    }

    public function runBatch( expContentJob $job, $batchSize )
    {
        $cp =& $job->checkpoint();
        $params = $job->params();
        if ( !empty( $cp['installed'] ) )
            return array( 'done' => 0, 'finished' => true, 'message' => '', 'done_total' => (int)$params['objects'] );
        $package = self::package( $params['package'] );
        $install = $params['install'];
        $asDrafts = !empty( $install['as_drafts'] );
        $last = 0;
        $installParameters = $install + array(
            'user_id' => $job->userID(), 'non-interactive' => true,
            'drafts' => array(), 'drafts-waiting' => array(), 'skipped-items' => array(),
            // the job's progress, object by object, at most once a second (the job file is shared with the pages)
            'progress' => function ( $done, $total ) use ( $job, &$last ) {
                if ( $done < $total && time() - $last < 1 )
                    return;
                $last = time();
                $job->setProgress( array( 'done' => (int)$done, 'total' => (int)$total, 'phase' => 'objects',
                                          'message' => self::tr( '%1 of %2 content objects', array( '%1' => $done, '%2' => $total ) ) ) );
                $job->set( 'heartbeat', time() );
                $job->save();
            } );
        if ( !isset( $installParameters['language_map'] ) || !is_array( $installParameters['language_map'] ) )
            $installParameters['language_map'] = $package->defaultLanguageMap();
        $items = $package->installItemsList( false, eZSys::osType() );
        foreach ( $params['items'] as $index )
        {
            if ( !isset( $items[$index] ) )
                continue;
            $item = $items[$index];
            $type = isset( $item['type'] ) ? $item['type'] : '';
            if ( $asDrafts && $type !== 'ezcontentobject' )
            {
                $installParameters['skipped-items'][] = array( 'type' => $type, 'name' => !empty( $item['filename'] ) ? $item['filename'] : ( isset( $item['name'] ) ? $item['name'] : '' ) );
                continue;
            }
            $job->appendLog( 'installing item ' . $index . ' (' . $type . ( !empty( $item['filename'] ) ? ' ' . $item['filename'] : '' ) . ')' );
            if ( !$package->installItem( $item, $installParameters ) )
            {
                $description = isset( $installParameters['error']['description'] ) ? (string)$installParameters['error']['description'] : '';
                throw new expContentJobException( self::tr( 'The install of item %1 (%2) failed: %3', array( '%1' => $index, '%2' => $type,
                                                                                                             '%3' => $description !== '' ? $description : 'see the debug log' ) ) );
            }
        }
        $job->setResult( 'package', (string)$params['package'] );
        $job->setResult( 'as_drafts', $asDrafts );
        $job->setResult( 'drafts', array_values( $installParameters['drafts'] ) );
        $job->setResult( 'waiting', array_values( $installParameters['drafts-waiting'] ) );
        $job->setResult( 'skipped', array_values( $installParameters['skipped-items'] ) );
        $cp['installed'] = true;
        return array( 'done' => (int)$params['objects'], 'finished' => true, 'done_total' => (int)$params['objects'],
                      'message' => $asDrafts ? self::tr( '%1 drafts written', array( '%1' => count( $installParameters['drafts'] ) ) ) : self::tr( 'Installed' ) );
    }

    public function afterBatch( expContentJob $job )
    {
    }

    public function finish( expContentJob $job )
    {
        $params = $job->params();
        $package = self::package( $params['package'] );
        if ( !$package )
            return;
        $result = $job->result();
        if ( empty( $params['install']['as_drafts'] ) )
        {
            $package->setInstalled();
            return;
        }
        eZPackage::notifyDraftsInstalled( $package, array( 'drafts' => isset( $result['drafts'] ) ? $result['drafts'] : array(),
                                                            'waiting' => isset( $result['waiting'] ) ? $result['waiting'] : array(),
                                                            'skipped' => isset( $result['skipped'] ) ? $result['skipped'] : array(), 'ok' => true ),
                                          array( 'user_id' => $job->userID(), 'via' => 'background', 'job' => $job->id() ) );
    }

    /**
     * The background installs of a package, newest first (for the package page).
     *
     * @param string $name
     * @param int $limit
     * @return expContentJob[]
     */
    public static function jobsOfPackage( $name, $limit = 5 )
    {
        if ( !self::available() )
            return array();
        $jobs = array();
        foreach ( expContentJob::listFor( null ) as $job )
        {
            if ( $job->type() === 'packageinstall' && isset( $job->params()['package'] ) && $job->params()['package'] === (string)$name )
                $jobs[] = $job;
        }
        usort( $jobs, function ( $a, $b ) { return $b->created() <=> $a->created(); } );
        return array_slice( $jobs, 0, max( 1, (int)$limit ) );
    }
}
