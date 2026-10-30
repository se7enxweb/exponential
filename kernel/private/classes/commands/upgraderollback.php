<?php
/**
 * @description Upgrade rollback: put the database, storage, settings, libraries and code back to a caption (dry run unless --apply)
 * @alias exp:upgrade:rollback
 *
 * File containing the exp:upgrade:rollback command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgraderollback extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade:rollback';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade rollback\n" .
                             "Without a caption id: the captions, newest first (as exp:upgrade:captions). With one: what a\n" .
                             "rollback to it restores, and with --apply: a safety caption of the current state first (so the\n" .
                             "rollback can be undone), then the parts: the database (tables made later are dropped), the\n" .
                             "storage (var/), settings/override, composer.lock with composer install, the git checkouts\n" .
                             "(git reset --keep), then the verification of exp:upgrade:verify --after. Never automatic.\n" .
                             "Afterwards run ./console exp:upgrade:finish --apply.\n" .
                             "\n" .
                             "./console exp:upgrade:rollback\n" .
                             "./console exp:upgrade:rollback 2026-10-29_10-15-00\n" .
                             "./console exp:upgrade:rollback 2026-10-29_10-15-00 --apply --parts=database,settings",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][parts:][backup-dir:][format:][no-caption][force-no-caption][no-verify][url:*][no-tests][allow-vendor-changes]', '[CAPTION]', array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'parts' => 'Only these parts, a comma list of database, storage, settings, libraries, code (default: what the caption has)',
            'backup-dir' => 'Where the backup goes (default var/backup/upgrade); a directory <time> is made in it',
            'format' => 'text (default) or json (the machine output exp-upgrade/1)',
            'no-caption' => 'Write without taking a caption first (the same as --no-backup); refused on production without --force-no-caption',
            'force-no-caption' => 'With --no-caption on a production installation: write without a caption all the same',
            'no-verify' => 'Skip the verification after the upgrade (exp:upgrade:verify --after)',
            'url' => 'The addresses the smoke check requests after the deploy (a comma list or repeated); default the front page of SiteURL',
            'no-tests' => 'Leave the test suite upgrade-smoke out of the verification',
            'allow-vendor-changes' => 'Install the libraries although Composer reports local changes in vendor/',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = self::applyRequested( $options );
        if ( $apply === null )
            $this->usage( '--apply and --dry-run exclude each other; without --apply nothing is changed' );
        $arguments = isset( $options['arguments'] ) ? (array)$options['arguments'] : array();
        $dir = !empty( $options['backup-dir'] ) ? (string)$options['backup-dir'] : null;
        if ( !$arguments )
        {
            $report = \expUpgrade::report( 'captions', array( 'command' => $this->name() ) );
            $report['captions'] = \expUpgradeCaption::all( $dir );
            if ( $format === 'json' )
                $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            else
            {
                $this->printCaptions( $report['captions'] );
                $this->output( count( $report['captions'] ) . ' caption(s)' );
            }
            $this->shutdown( 0 );
            return;
        }
        $report = \expUpgradeRollback::run( (string)$arguments[0], array(
            'apply' => $apply,
            'parts' => !empty( $options['parts'] ) ? (string)$options['parts'] : array(),
            'backup-dir' => $dir,
            'no-caption' => !empty( $options['no-caption'] ),
            'force-no-caption' => !empty( $options['force-no-caption'] ),
            'no-verify' => !empty( $options['no-verify'] ),
            'url' => isset( $options['url'] ) ? \expUpgrade::listOption( $options['url'] ) : array(),
            'no-tests' => !empty( $options['no-tests'] ),
            'allow-vendor-changes' => !empty( $options['allow-vendor-changes'] ),
            'command' => $this->name(),
        ) );
        if ( $format === 'json' )
        {
            $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            $this->shutdown( $report['exit'] );
            return;
        }
        $this->output( 'Exponential upgrade rollback to ' . $report['rollback']['caption'] . ( $apply ? '' : ': dry run, nothing is changed (--apply restores)' ) );
        if ( $report['rollback']['safety_caption'] )
            $this->output( 'Safety caption of the state before the rollback: ' . $report['rollback']['safety_caption']['id']
                           . ' (./console exp:upgrade:rollback ' . $report['rollback']['safety_caption']['id'] . ' --apply undoes the rollback)' );
        foreach ( $report['rollback']['parts'] as $part => $lines )
        {
            $this->output();
            $this->output( "Part $part" );
            foreach ( $lines as $line )
                $this->output( ( $apply ? '  ' : '  would ' ) . $line );
        }
        $this->output();
        if ( $report['verify'] )
        {
            $this->output( 'Verification after the rollback: ' . ( $report['verify']['ok'] ? 'PASS' : 'FAIL' ) );
            $this->printVerify( $report['verify'] );
            $this->output();
        }
        foreach ( $report['warnings'] as $w )
            $this->output( 'WARN ' . $w );
        foreach ( $report['errors'] as $e )
            $this->error( 'FAIL: ' . $e );
        $this->output( $report['exit'] === 0 ? 'PASS' : 'FAIL (exit ' . $report['exit'] . ')' );
        $this->shutdown( $report['exit'] );
    }
}

}
