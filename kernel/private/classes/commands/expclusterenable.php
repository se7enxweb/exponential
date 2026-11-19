<?php
/**
 * @description Make the installation a one-node DFS cluster: the share, the cluster tables, the settings, the files moved in; a dry run unless --apply
 * @alias exp:cluster:enable
 *
 * File containing the exp:cluster:enable command (bin/php/expclusterenable.php); guide: doc/eng-us/features/6.0/cluster-commands.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expclusterenable extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_ERRORS = 1;
    const EXIT_USAGE = 2;

    public function run()
    {
        $this->script( array( 'description' => "Make the installation a one-node DFS cluster\n" .
                                               "Makes the share folder (default: dfs next to the installation, --share another one outside it), the\n" .
                                               "kernel's cluster tables in the site's own database (kernel/sql/<engine>/cluster_dfs_schema.sql), the\n" .
                                               "cluster settings as a marked block of settings/override/file.ini.append.php and config.cluster.php\n" .
                                               "for the cluster gateway, clears the INI caches, and moves the stored files in with clusterize.php\n" .
                                               "(once per var directory; --no-move leaves that for later). MySQL/MariaDB, PostgreSQL, or SQLite for\n" .
                                               "one node only. The rewrite rule of the gateway is printed for the web server: add it\n" .
                                               "in front of the site's own rules. Files that change are backed up to var/backup/cluster/. Without\n" .
                                               "--apply nothing is written.\n" .
                                               "\n" .
                                               "./console exp:cluster:enable                                  what would be done\n" .
                                               "./console exp:cluster:enable --apply\n" .
                                               "./console exp:cluster:enable --share=/mnt/e1/dfs/example.com --apply\n" .
                                               "\n" .
                                               "Exit code: 0 done, shown, or on already; 1 a step failed (what was written is put back); 2 wrong\n" .
                                               "options or the installation cannot be a cluster (its database, its own cluster settings).",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $options = $this->startup( '[share:][backend:][no-move][apply]', '',
                                   array( 'share' => 'the share folder, an absolute path outside the installation (default: dfs next to it)',
                                          'backend' => 'auto (the default: the site\'s database), mysqli, postgresql or sqlite',
                                          'no-move' => 'do not move the stored files in now (run bin/php/clusterize.php yourself)',
                                          'apply' => 'write; without it nothing is changed' ) );
        $backend = isset( $options['backend'] ) && $options['backend'] ? (string)$options['backend'] : 'auto';
        if ( !in_array( $backend, array_merge( array( 'auto' ), array_keys( \expClusterSetup::backends() ) ), true ) )
            $this->usage( '--backend must be auto or one of: ' . implode( ', ', array_keys( \expClusterSetup::backends() ) ) );
        $out = $this->cli();
        $root = \eZSys::rootDir();
        $r = \expClusterSetup::enable( $root, array( 'share' => isset( $options['share'] ) && $options['share'] ? (string)$options['share'] : '', 'backend' => $backend,
                                                     'move' => empty( $options['no-move'] ), 'apply' => !empty( $options['apply'] ) ),
                                       function ( $line ) use ( $out ) { $out->output( $line ); } );
        if ( !$r['ok'] )
        {
            $out->error( 'FAIL ' . $r['error'] );
            $this->shutdown( isset( $r['plan'] ) || strpos( $r['error'], 'stopped' ) !== false || strpos( $r['error'], 'cannot' ) !== false ? self::EXIT_ERRORS : self::EXIT_USAGE );
        }
        if ( !empty( $r['unchanged'] ) )
        {
            $out->output( 'PASS the installation is a one-node cluster already (share ' . $r['share'] . ')' );
            $this->shutdown( self::EXIT_OK );
        }
        $out->output( '' );
        $out->output( 'The gateway rule for the web server, in front of the site\'s own rules:' );
        foreach ( \expClusterSetup::webServerRules() as $where => $rule )
            $out->output( '  ' . $where . ":\n    " . str_replace( "\n", "\n    ", $rule ) );
        if ( !empty( $r['dry_run'] ) )
            $out->output( 'DRY RUN nothing written; run again with --apply' );
        else
            $out->output( 'PASS one-node cluster on: ' . (int)$r['files'] . ' files in the cluster, share ' . $r['share'] );
        $this->shutdown( self::EXIT_OK );
    }

    /**
     * @param string $message
     */
    protected function usage( $message )
    {
        $this->cli()->error( "FAIL $message" );
        $this->shutdown( self::EXIT_USAGE );
    }
}

}
?>
