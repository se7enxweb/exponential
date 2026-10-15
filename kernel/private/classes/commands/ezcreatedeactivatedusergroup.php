<?php
/**
 * The code of bin/php/ezcreatedeactivatedusergroup.php. The file bin/php/ezcreatedeactivatedusergroup.php is one call to it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

/**
 * Creates the user group for deactivated accounts of "Deactivate and move" (expUserDeactivation) ahead of first use,
 * for the siteaccess given with -s (required: the group belongs to that siteaccess's database). Without --commit it
 * only says what it would do. The group is created below content.ini [NodeSettings] UserRootNode with the remote id
 * expUserDeactivation::REMOTE_ID, hidden, without roles, published by [UserSettings] UserCreatorID, optionally in
 * the section --section-id. Prints the node id and the line for [UserSettings] DeactivatedUserGroupNodeID (needed
 * only to pin a different group; the remote id finds this one); no INI file is written. Exit 0 when the group
 * exists or was created, 1 on an error.
 */
class Ezcreatedeactivatedusergroup extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $script = $this->script( array(
            'description' => "Create the user group for deactivated accounts (Deactivate and move) ahead of first use.\n" .
                             "A dry run unless --commit is given. Prints the node id; never writes an INI file.\n" .
                             "\n" .
                             "  php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess>\n" .
                             "  php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess> --commit [--name=...] [--section-id=N]",
            'use-session' => false,
            'use-modules' => true,
            'use-extensions' => true,
        ) );
        // startup(), getOptions() (which applies -s), then initialize(): the order that binds the right database
        $options = $this->startup( '[commit][name:][section-id:]', '', array(
            'commit' => 'Create the group (without it, only say what would be done)',
            'name' => 'The name of the group (default: "Deactivated users" in the siteaccess language)',
            'section-id' => 'Put the group in this section (default: the section of the user root node)',
        ) );

        if ( empty( $options['siteaccess'] ) )
        {
            $cli->error( 'Give the siteaccess with -s <name> (or --siteaccess=<name>): the group is created in its database.' );
            $script->shutdown( 1 );
            return 1;
        }
        $siteaccess = (string)$options['siteaccess'];
        $commit = !empty( $options['commit'] );
        $name = isset( $options['name'] ) && is_string( $options['name'] ) ? trim( $options['name'] ) : '';
        $sectionID = isset( $options['section-id'] ) ? (int)$options['section-id'] : 0;
        if ( isset( $options['section-id'] ) && ( !ctype_digit( (string)$options['section-id'] ) || !\eZSection::fetch( $sectionID ) ) )
        {
            $cli->error( "No section with the id {$options['section-id']}." );
            $script->shutdown( 1 );
            return 1;
        }

        $cli->output( "Siteaccess $siteaccess (database " . \eZDB::instance()->DB . '); no other siteaccess was looked at.' );
        if ( !\expUserDeactivation::isEnabled() )
            $cli->output( 'Note: [UserSettings] DeactivateAndMove is not enabled for this siteaccess; the group is only used once it is.' );

        $group = \expUserDeactivation::groupNode();
        if ( $group )
        {
            $cli->output( 'The group exists: "' . $group->attribute( 'name' ) . '", node ' . (int)$group->attribute( 'node_id' ) . '.' );
            $this->printSettings( (int)$group->attribute( 'node_id' ) );
            $script->shutdown( 0 );
            return 0;
        }
        $configured = \expUserDeactivation::configuredNodeID();
        if ( $configured > 0 )
        {
            $cli->error( "[UserSettings] DeactivatedUserGroupNodeID=$configured, but there is no node $configured. Correct or empty the setting; this script does not replace a configured group." );
            $script->shutdown( 1 );
            return 1;
        }
        if ( !\expUserDeactivation::groupMayBeCreated() )
        {
            $cli->error( 'An object with the remote id ' . \expUserDeactivation::REMOTE_ID . ' exists but has no published location. Publish or remove it first.' );
            $script->shutdown( 1 );
            return 1;
        }
        $parent = \expUserDeactivation::groupParentNode();
        if ( !$parent )
        {
            $cli->error( 'content.ini [NodeSettings] UserRootNode names no node.' );
            $script->shutdown( 1 );
            return 1;
        }

        $what = 'a hidden user group "' . ( $name !== '' ? $name : \ezpI18n::tr( \expUserDeactivation::CONTEXT, 'Deactivated users' ) ) . '" below "'
              . $parent->attribute( 'name' ) . '" (node ' . (int)$parent->attribute( 'node_id' ) . '), remote id ' . \expUserDeactivation::REMOTE_ID
              . ', without roles' . ( $sectionID > 0 ? ", in section $sectionID" : '' );
        if ( !$commit )
        {
            $cli->output( "Dry run: would create $what." );
            $cli->output( 'Run again with --commit to create it.' );
            $script->shutdown( 0 );
            return 0;
        }

        // published by the user who creates the users of the site
        $creatorID = (int)\eZINI::instance()->variable( 'UserSettings', 'UserCreatorID' );
        $creator = \eZUser::fetch( $creatorID );
        if ( !$creator instanceof \eZUser )
        {
            $cli->error( "[UserSettings] UserCreatorID=$creatorID names no user." );
            $script->shutdown( 1 );
            return 1;
        }
        \eZUser::setCurrentlyLoggedInUser( $creator, $creatorID, \eZUser::NO_SESSION_REGENERATE );

        $created = \expUserDeactivation::createGroup( $name !== '' ? $name : null, $sectionID );
        if ( !$created['ok'] )
        {
            $cli->error( 'The group could not be created (' . $created['error'] . '). Nothing was changed.' );
            $script->shutdown( 1 );
            return 1;
        }
        $nodeID = (int)$created['node']->attribute( 'node_id' );
        $cli->output( "Created $what: node $nodeID." );
        $this->printSettings( $nodeID );
        $script->shutdown( 0 );
        return 0;
    }

    /**
     * The settings to put in the siteaccess's site.ini.append.php (or settings/override/), printed, never written.
     *
     * @param int $nodeID
     */
    protected function printSettings( $nodeID )
    {
        $cli = $this->cli();
        $cli->output( '' );
        $cli->output( 'To use it, set in site.ini.append.php ([UserSettings]); the node id is optional, the remote id finds the group:' );
        $cli->output( '  [UserSettings]' );
        $cli->output( '  DeactivateAndMove=enabled' );
        $cli->output( "  DeactivatedUserGroupNodeID=$nodeID" );
    }
}

}
