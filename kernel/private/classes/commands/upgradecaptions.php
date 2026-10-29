<?php
/**
 * @description Upgrade captions: the safe points to roll back to (git_manager's and exp:upgrade's), checked with --verify, pruned with --prune
 * @alias exp:upgrade:captions
 *
 * File containing the exp:upgrade:captions command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgradecaptions extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade:captions';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade captions\n" .
                             "Every caption, newest first: id, kind (pre, post, safety, manual; git_manager for its own), time,\n" .
                             "size, engine, label, and the command that rolls back to it. A caption is git_manager's full backup\n" .
                             "(database, var/ storage) with what exp:upgrade adds: settings, composer.lock, the git commits and\n" .
                             "checksums. --verify=<id> checks the checksums and restores the dump into a throwaway database.\n" .
                             "--prune lists the captions of exp:upgrade that [UpgradeSettings] CaptionKeep, CaptionKeepDays and\n" .
                             "CaptionKeepUpgrades let go, and removes them with --apply.\n" .
                             "\n" .
                             "./console exp:upgrade:captions\n" .
                             "./console exp:upgrade:captions --verify=2026-10-29_10-15-00\n" .
                             "./console exp:upgrade:captions --prune --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[format:][verify:][prune][apply][dry-run][backup-dir:]', '', array(
            'format' => 'text (default) or json (the machine output exp-upgrade/1)',
            'verify' => 'Check this caption: its checksums and a restore of its dump into a throwaway database',
            'prune' => 'Remove the captions of exp:upgrade beyond [UpgradeSettings] CaptionKeep, CaptionKeepDays, CaptionKeepUpgrades',
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'backup-dir' => 'Where the backup goes (default var/backup/upgrade); a directory <time> is made in it',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = self::applyRequested( $options );
        if ( $apply === null )
            $this->usage( '--apply and --dry-run exclude each other; without --apply nothing is changed' );
        $dir = !empty( $options['backup-dir'] ) ? (string)$options['backup-dir'] : null;
        $report = \expUpgrade::report( 'captions', array( 'command' => $this->name() ) );
        if ( !empty( $options['verify'] ) )
        {
            $result = \expUpgradeCaption::verify( (string)$options['verify'], \eZDB::instance(), $dir );
            $report['mode'] = 'caption-verify';
            $report['caption_verify'] = $result;
            $report['exit'] = $result['ok'] === false ? ( isset( $result['checks']['found'] ) ? \expUpgrade::EXIT_USAGE : \expUpgrade::EXIT_FAILED ) : \expUpgrade::EXIT_OK;
            if ( $format === 'json' )
                $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            else
            {
                $this->output( 'Caption ' . $result['id'] );
                foreach ( $result['checks'] as $name => $check )
                    $this->output( sprintf( '  %-5s %-10s %s', $check['ok'] === true ? 'PASS' : ( $check['ok'] === false ? 'FAIL' : 'SKIP' ), $name, $check['detail'] ) );
                $this->output( $report['exit'] === 0 ? 'PASS' : 'FAIL (exit ' . $report['exit'] . ')' );
            }
            $this->shutdown( $report['exit'] );
            return;
        }
        if ( !empty( $options['prune'] ) )
        {
            $result = \expUpgradeCaption::prune( (bool)$apply, $dir );
            $report['mode'] = $apply ? 'caption-prune' : 'caption-prune-dry-run';
            $report['prune'] = $result;
            $report['exit'] = $result['failed'] ? \expUpgrade::EXIT_FAILED : \expUpgrade::EXIT_OK;
            if ( $format === 'json' )
                $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            else
            {
                $s = $result['settings'];
                $this->output( sprintf( 'Keep the newest %d, captions younger than %d days, and the pre and post captions of the last %d upgrades',
                                        $s['keep'], $s['keep-days'], $s['keep-upgrades'] ) );
                foreach ( $result['removed'] as $id )
                    $this->output( ( $apply ? '  removed ' : '  would remove ' ) . $id . ( in_array( $id, $result['failed'], true ) ? ' (FAILED)' : '' ) );
                $this->output( count( $result['removed'] ) . ' caption(s)' . ( $apply ? '' : ' (dry run, nothing removed; --apply removes)' ) );
                $this->output( $report['exit'] === 0 ? 'PASS' : 'FAIL (exit 1)' );
            }
            $this->shutdown( $report['exit'] );
            return;
        }
        $report['captions'] = \expUpgradeCaption::all( $dir );
        if ( $format === 'json' )
            $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        else
        {
            $this->printCaptions( $report['captions'] );
            $this->output( count( $report['captions'] ) . ' caption(s)' );
        }
        $this->shutdown( 0 );
    }
}

}
