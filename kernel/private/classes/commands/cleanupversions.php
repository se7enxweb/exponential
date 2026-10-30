<?php
/**
 * The code of bin/php/cleanupversions.php, moved into a class (#207 stage 1). The file bin/php/cleanupversions.php is one call to it.
 * @description Remove archived content versions beyond the VersionManagement limits (dry run unless --apply; -n: do not wait)
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/cleanupversions.php:
 *
 *
 * File containing the script to cleanup versions according content.ini/[VersionManagement]/DefaultVersionHistoryLimit
 * and VersionHistoryClass settings
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package
 *
 */

namespace Exponential\Command\Kernel
{

class Cleanupversions extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'batchVersionsToRemove', 'cli', 'db', 'node', 'nodes', 'object', 'options', 'removedVersions', 'script', 'subTreeParams', 'total', 'version', 'versionCount', 'versionLimit', 'versions', 'versionsToRemove' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = $this->cli();

        $script = $this->script(
            array(
                'description' => "Remove archived content object versions according to "
                    . "[VersionManagement]/DefaultVersionHistoryLimit and "
                    . "[VersionManagement]/VersionHistoryClass settings\n"
                    . "Without --apply nothing is removed: the script lists per object how many archived versions it would\n"
                    . "remove. --dry-run means the same as no flag; --apply together with --dry-run is a usage error (exit 2).\n\n"
                    . "Example: ./bin/php/cleanupversions.php\n"
                    . "         ./bin/php/cleanupversions.php --apply -n",
                'use-session' => false,
                'use-modules' => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( "[apply][dry-run][n]", "",
                                   array( 'apply' => 'Remove the versions (the default is a dry run that lists them and removes nothing)',
                                          'dry-run' => 'The default: list how many archived versions of each object would be removed',
                                          'n' => 'With --apply: do not wait 10 seconds' ) );

        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $script->shutdown( 2 );
        }
        $dryRun = !$apply;

        if ( !$dryRun && empty( $options['n'] ) )
        {
            $cli->warning( "This cleanup script is going to remove archived versions according to the settings" );
            $cli->warning( "content.ini/[VersionManagement]/DefaultVersionHistoryLimit and content.ini/[VersionManagement]/VersionHistoryClass" );
            $cli->warning();
            $cli->warning( "You have 10 seconds to break the script (press Ctrl-C)" );
            sleep( 10 );
            $cli->output();
        }

        $subTreeParams = array(
            'Limitation' => array(),
            'MainNodeOnly' => true,
            'LoadDataMap' => false,
            'IgnoreVisibility' => true,
        );
        $total = \eZContentObjectTreeNode::subTreeCountByNodeID( $subTreeParams, 1 );
        $cli->output( "{$total} objects to check..." . ( $dryRun ? '' : " (In the progess bar, 'R' means that at least a version was removed)" ) );

        if ( !$dryRun )
        {
            $script->setIterationData( 'R', '.' );
            $script->resetIteration( $total );
        }
        $planned = 0;
        $removedTotal = 0;

        $subTreeParams['Offset'] = 0;
        $subTreeParams['Limit'] = 100;
        $db = \eZDB::instance();

        while ( true )
        {
            $nodes = \eZContentObjectTreeNode::subTreeByNodeID( $subTreeParams, 1 );
            if ( empty( $nodes ) )
            {
                break;
            }
            foreach( $nodes as $node )
            {
                $object = $node->attribute( 'object' );
                $versionCount = $object->getVersionCount();
                $versionLimit = \eZContentClass::versionHistoryLimit( $object->attribute( 'content_class' ) );
                if ( $versionCount <= $versionLimit )
                {
                    if ( !$dryRun )
                        $script->iterate( $cli, false, "Nothing to do on object #{$object->attribute( 'id' )}" );
                    continue;
                }

                $versionsToRemove = $versionCount - $versionLimit;
                if ( $dryRun )
                {
                    $archived = count( $object->versions( false, array( 'conditions' => array( 'status' => \eZContentObjectVersion::STATUS_ARCHIVED ) ) ) );
                    $would = min( $versionsToRemove, $archived );
                    if ( $would > 0 )
                    {
                        $cli->output( sprintf( 'would remove %d archived version(s) of object #%d %s (%d versions, limit %d)',
                                               $would, $object->attribute( 'id' ), $object->attribute( 'name' ), $versionCount, $versionLimit ) );
                        $planned += $would;
                    }
                    continue;
                }
                $removedVersions = 0;
                $batchVersionsToRemove = 20;
                while ( $removedVersions < $versionsToRemove )
                {
                    // the oldest archived versions first; the removed ones are gone, so always from the start
                    $versions = $object->versions( true, array(
                        'conditions' => array( 'status' => \eZContentObjectVersion::STATUS_ARCHIVED ),
                        'sort' => array( 'modified' => 'asc' ),
                        'limit' => array( 'limit' => min( $batchVersionsToRemove, $versionsToRemove - $removedVersions ), 'offset' => 0 ),
                    ) );
                    if ( !$versions )
                        break;

                    $db->begin();
                    foreach( $versions as $version )
                    {
                        if ( !$dryRun )
                            $version->removeThis();
                        $removedVersions++;
                    }
                    $db->commit();
                }
                $removedTotal += $removedVersions;

                if ( $removedVersions > 0 )
                {
                    $script->iterate( $cli, true, "Removed {$removedVersions} archived versions of object #{$object->attribute( 'id' )}" );
                }
                else
                {
                    $script->iterate( $cli, false, "No archived version of object #{$object->attribute( 'id' )} found" );
                }
            }
            $subTreeParams['Offset'] += $subTreeParams['Limit'];
            \eZContentObject::clearCache();
        }

        $cli->output();
        if ( $dryRun )
            $cli->output( "PASS: $planned archived version(s) would be removed (dry run, nothing changed; --apply writes)" );
        else
            $cli->output( "PASS: $removedTotal archived version(s) removed" );
        $script->shutdown();
    }
}

}
