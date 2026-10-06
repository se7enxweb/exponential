<?php
/**
 * @description The facts and health checks of Setup > System information, as text or JSON (--json, --sizes)
 * @alias exp:system:info
 *
 * File containing the exp:system:info command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Systeminfo extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "System information\n" .
                                 "The report of Setup > System information for this command-line PHP: Exponential, PHP, OPcache,\n" .
                                 "the database, storage, caches, cronjobs, mail, locale, the extensions and the health checks.\n" .
                                 "The web server's own PHP (PHP-FPM, Velocity) can differ: its report is on the page.\n" .
                                 "Nothing is changed, and no password, key, token or session id is printed.\n" .
                                 "\n" .
                                 "./console exp:system:info\n" .
                                 "./console exp:system:info --json\n" .
                                 "./console exp:system:info --sizes      also measure var/, the cache directories and the database\n" .
                                 "\n" .
                                 "Exit 0 when no check fails, 1 when one does.",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[json][sizes]', '',
                                   array( 'json' => 'One JSON object instead of the text',
                                          'sizes' => 'Also measure the size of var/, of each cache directory and of the database' ) );

        $report = \expSystemReport::gather( array( 'sizes' => !empty( $options['sizes'] ) ) );
        if ( !empty( $options['json'] ) )
        {
            $data = $report->toArray();
            unset( $data['cards'] );
            $cli->output( json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        }
        else
        {
            $cli->output( rtrim( $report->toText() ) );
        }
        $summary = $report->summary();
        $this->shutdown( $summary['fail'] > 0 ? 1 : 0 );
    }
}

}
