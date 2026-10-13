<?php
/**
 * @description Check an installation coming from LoveStack (read-only), and record its database as Exponential's (migrate)
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
                                 "check    lists what this installation and its database bring along from LoveStack:\n" .
                                 "         the database version row, LoveStack's db-schema-version row, a unique\n" .
                                 "         index on ezuser.login, data_text NOT NULL, the legacy_2018 extension,\n" .
                                 "         LoveStack-only settings in settings/override and the siteaccesses, and\n" .
                                 "         the LoveStack package in composer.json. Reads only, writes nothing.\n" .
                                 "migrate  records a LoveStack database as Exponential's: the version row\n" .
                                 "         'lovestack' becomes this release's version, and LoveStack's schema\n" .
                                 "         version is kept as ezsite_data lovestack-schema-version. Then it\n" .
                                 "         prints the settings to rename or remove. Never changes content or\n" .
                                 "         settings. A dry run unless --apply is given; running it again\n" .
                                 "         changes nothing. Refuses a database that does not come from LoveStack.\n" .
                                 "\n" .
                                 "./console exp:lovestack check\n" .
                                 "./console exp:lovestack check --json\n" .
                                 "./console exp:lovestack migrate              (dry run: prints what it would write)\n" .
                                 "./console exp:lovestack migrate --apply\n" .
                                 "\n" .
                                 "Exit code: check 0 nothing to do, 1 something needs attention (status found);\n" .
                                 "migrate 0 done or nothing to do, 1 refused; 2 usage error.\n" .
                                 "Guide: doc/guides/upgrading.md (Coming from LoveStack)",
                'use-session'    => false,
                'use-modules'    => false,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[json][dry-run][apply]', '[ACTION]',
                                   array( 'json' => 'One JSON object instead of the text',
                                          'dry-run' => 'migrate: print what would be written, write nothing (the default)',
                                          'apply' => 'migrate: write the two ezsite_data rows' ) );

        $action = isset( $options['arguments'][0] ) ? (string)$options['arguments'][0] : 'check';
        if ( $action !== 'check' && $action !== 'migrate' )
        {
            $cli->error( "FAIL: unknown action '$action'; the actions are: check, migrate" );
            $this->shutdown( 2 );
            return;
        }
        if ( $action === 'migrate' )
        {
            $this->migrate( !empty( $options['apply'] ), !empty( $options['dry-run'] ), !empty( $options['json'] ) );
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

    /**
     * exp:lovestack migrate
     *
     * @param bool $apply
     * @param bool $dryRun
     * @param bool $json
     */
    protected function migrate( $apply, $dryRun, $json )
    {
        $cli = $this->cli();
        if ( $apply && $dryRun )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other' );
            $this->shutdown( 2 );
            return;
        }

        $migration = new \expLoveStack();
        $result = $migration->migrate( $apply );
        $refused = $result['refused'] !== '';

        if ( $json )
        {
            $cli->output( json_encode( $result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
            $this->shutdown( $refused ? 1 : 0 );
            return;
        }

        if ( $refused )
        {
            $cli->error( 'REFUSED: ' . $result['refused'] );
            $this->shutdown( 1 );
            return;
        }

        $cli->output( $apply ? 'exp:lovestack migrate --apply' : 'exp:lovestack migrate (dry run: nothing is written; --apply writes)' );
        $cli->output( '' );
        $cli->output( 'Database (ezsite_data):' );
        if ( !$result['writes'] )
            $cli->output( '  nothing to change: the database is already recorded as Exponential ' . $result['expected'] );
        foreach ( $result['writes'] as $write )
        {
            $verb = $apply ? 'written ' : 'would write';
            $from = $write['from'] === null ? 'new row' : "'" . $write['from'] . "'";
            $cli->output( "  $verb  {$write['name']} = '{$write['to']}' (was: $from)" );
        }
        foreach ( $result['notes'] as $note )
            $cli->output( '  note: ' . $note );

        $cli->output( '' );
        $cli->output( 'Settings and extensions to change by hand (migrate does not edit settings):' );
        if ( !$result['settings'] )
            $cli->output( '  none' );
        foreach ( $result['settings'] as $setting )
        {
            $cli->output( '  ' . $setting['label'] . ': ' . $setting['detail'] );
            $cli->output( '    -> ' . $setting['action'] );
        }

        $cli->output( '' );
        if ( $apply )
            $cli->output( 'OK: ' . $result['written'] . ' row(s) written; content and settings were not changed' );
        else
            $cli->output( 'OK (dry run): ' . count( $result['writes'] ) . ' row(s) would be written; run with --apply to write them' );
        $this->shutdown( 0 );
    }
}

}
