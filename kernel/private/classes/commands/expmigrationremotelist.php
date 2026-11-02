<?php
/**
 * @description The remotes migrations can be deployed to (migration.ini [RemoteSettings]), with their groups and any problem of their settings; connects to none
 * @alias exp:migration:remote:list
 *
 * File containing the exp:migration:remote:list command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationremotelist extends Expmigrationdeploy
{
    protected function name()
    {
        return 'exp:migration:remote:list';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "The remotes of migration deploys\n" .
                             "Every remote of migration.ini ([RemoteSettings] RemoteList[], one [Remote_<id>] each): environment,\n" .
                             "transport, address, credential (by name, never its value), groups, and what is wrong with its\n" .
                             "settings. Connects to no remote and reads no secret.\n" .
                             "\n" .
                             "./console exp:migration:remote:list\n" .
                             "./console exp:migration:remote:list --remotes=production --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[remotes:*][format:]', '', array(
            'remotes' => 'The remotes: ids or group names of [RemoteSettings] RemoteList[] in migration.ini, or all (a comma list or repeated)',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $remotes = array();
        $errors = array();
        foreach ( $this->remotes( $options, false ) as $id => $remote )
        {
            $problems = $remote->validate();
            $remotes[$id] = $remote->toArray() + array( 'problems' => $problems );
            foreach ( $problems as $problem )
                $errors[] = "$id: $problem";
        }
        $this->finish( array( 'mode' => 'remotes', 'remotes' => $remotes, 'errors' => $errors, 'warnings' => $remotes ? array() : array( 'no remote in [RemoteSettings] RemoteList[] of migration.ini' ),
                              'exit' => $errors ? \expUpgrade::EXIT_FAILED : \expUpgrade::EXIT_OK ), $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'Exponential migrations: the remotes' );
        foreach ( $report['remotes'] as $id => $remote )
            $lines[] = sprintf( '  %-14s %-12s %-6s %-40s %s%s', $id, $remote['environment'], $remote['transport'], $remote['address'], $remote['credential'],
                                $remote['groups'] ? '  groups: ' . implode( ', ', $remote['groups'] ) : '' );
        $lines[] = count( $report['remotes'] ) . ' remote(s)';
        return $lines;
    }
}

}
