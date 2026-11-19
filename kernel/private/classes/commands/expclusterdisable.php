<?php
/**
 * @description Take a one-node DFS cluster made by exp:cluster:enable back to a single file system: the files moved back, the settings out; a dry run unless --apply
 * @alias exp:cluster:disable
 *
 * File containing the exp:cluster:disable command (bin/php/expclusterdisable.php); guide: doc/eng-us/features/6.0/cluster-commands.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expclusterdisable extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_ERRORS = 1;
    const EXIT_USAGE = 2;

    public function run()
    {
        $this->script( array( 'description' => "Take a one-node DFS cluster back to a single file system\n" .
                                               "Moves the stored files back with clusterize.php -u (once per var directory; --no-move leaves that\n" .
                                               "for later), then takes the block of exp:cluster:enable out of settings/override/file.ini.append.php,\n" .
                                               "removes its config.cluster.php and clears the INI caches. The share folder and the cluster tables\n" .
                                               "are kept. Cluster settings that exp:cluster:enable did not write are left alone. Take the gateway\n" .
                                               "rule out of the web server's configuration first.\n" .
                                               "\n" .
                                               "./console exp:cluster:disable              what would be done\n" .
                                               "./console exp:cluster:disable --apply\n" .
                                               "\n" .
                                               "Exit code: 0 done, shown, or off already; 1 a step failed; 2 the cluster settings are not ours.",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $options = $this->startup( '[no-move][apply]', '',
                                   array( 'no-move' => 'do not move the stored files back now (run bin/php/clusterize.php -u yourself first)',
                                          'apply' => 'write; without it nothing is changed' ) );
        $out = $this->cli();
        $r = \expClusterSetup::disable( \eZSys::rootDir(), array( 'move' => empty( $options['no-move'] ), 'apply' => !empty( $options['apply'] ) ),
                                        function ( $line ) use ( $out ) { $out->output( $line ); } );
        if ( !$r['ok'] )
        {
            $out->error( 'FAIL ' . $r['error'] );
            $this->shutdown( strpos( $r['error'], 'left alone' ) !== false ? self::EXIT_USAGE : self::EXIT_ERRORS );
        }
        if ( !empty( $r['unchanged'] ) )
            $out->output( 'PASS the installation is on a single file system already' );
        else if ( !empty( $r['dry_run'] ) )
            $out->output( 'DRY RUN nothing written; run again with --apply' );
        else
            $out->output( 'PASS one-node cluster off' );
        $this->shutdown( self::EXIT_OK );
    }
}

}
?>
