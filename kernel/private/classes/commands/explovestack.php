<?php
/**
 * @description Check an installation and its database for what a site coming from LoveStack brings along (read-only)
 * @alias exp:lovestack
 *
 * File containing the exp:lovestack command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Explovestack extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "Sites coming from LoveStack (mugoweb/ezpublish-legacy)\n" .
                                 "\n" .
                                 "check  lists what this installation and its database bring along from LoveStack:\n" .
                                 "       the database version row, LoveStack's db-schema-version row, a unique\n" .
                                 "       index on ezuser.login, data_text NOT NULL, the legacy_2018 extension,\n" .
                                 "       LoveStack-only settings in settings/override and the siteaccesses, and\n" .
                                 "       the LoveStack package in composer.json. Reads only, writes nothing.\n" .
                                 "\n" .
                                 "./console exp:lovestack check\n" .
                                 "./console exp:lovestack check --json\n" .
                                 "\n" .
                                 "Exit code: 0 nothing to do, 1 something needs attention (status found), 2 usage error.\n" .
                                 "Guide: doc/bc/6.0/CONVERTING_LoveStack_to_Exponential.md",
                'use-session'    => false,
                'use-modules'    => false,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[json]', '[ACTION]', array( 'json' => 'One JSON object instead of the text' ) );

        $action = isset( $options['arguments'][0] ) ? (string)$options['arguments'][0] : 'check';
        if ( $action !== 'check' )
        {
            $cli->error( "FAIL: unknown action '$action'; the action is: check" );
            $this->shutdown( 2 );
            return;
        }

        $check = new \expLoveStack();
        $findings = $check->findings();
        $attention = \expLoveStack::needsAttention( $findings );

        if ( !empty( $options['json'] ) )
        {
            $cli->output( json_encode( array( 'needs_attention' => $attention, 'findings' => $findings ),
                                       JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
            $this->shutdown( $attention ? 1 : 0 );
            return;
        }

        $marks = array( \expLoveStack::STATUS_FOUND => 'FOUND  ', \expLoveStack::STATUS_INFO => 'info   ',
                        \expLoveStack::STATUS_CLEAR => 'clear  ', \expLoveStack::STATUS_UNKNOWN => 'unknown' );
        foreach ( $findings as $finding )
        {
            $cli->output( $marks[$finding['status']] . '  ' . $finding['label'] . ': ' . $finding['detail'] );
            if ( $finding['action'] !== '' && $finding['status'] !== \expLoveStack::STATUS_CLEAR )
                $cli->output( '         -> ' . $finding['action'] );
        }
        $cli->output( $attention ? 'NEEDS ATTENTION: LoveStack markers found (see FOUND above)'
                                 : 'OK: no LoveStack marker that needs attention' );
        $this->shutdown( $attention ? 1 : 0 );
    }
}

}
