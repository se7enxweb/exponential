<?php
/**
 * @description Take a caption of the installation now (database, storage, settings, the commits of the code), or list the captions; a dry run unless --apply
 * @alias exp:migration:caption
 *
 * File containing the exp:migration:caption command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationcaption extends ExpmigrationBase
{
    protected function name()
    {
        return 'exp:migration:caption';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Take a caption now\n" .
                             "A caption of the kind manual, under the installation lock: git_manager's full backup (database and\n" .
                             "var/ storage) where it is installed, else exp:upgrade's own dump, with settings/override and the\n" .
                             "commits of the code. It is a point exp:migration:rollback --expert and exp:upgrade:rollback can go\n" .
                             "back to. --list lists the captions, newest first, with the command that restores each.\n" .
                             "\n" .
                             "./console exp:migration:caption --list\n" .
                             "./console exp:migration:caption --apply\n" .
                             "./console exp:migration:caption --label=\"before the news import\" --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][format:][list][label:][backup-dir:]', '', array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'list' => 'List the captions instead of taking one',
            'label' => 'The label of the caption or of the recording (a caption without one: "manual caption" with the time)',
            'backup-dir' => 'Where the captions made without git_manager are (default var/backup/upgrade)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $dir = isset( $options['backup-dir'] ) && $options['backup-dir'] !== false && $options['backup-dir'] !== '' ? (string)$options['backup-dir'] : null;
        if ( !empty( $options['list'] ) )
        {
            if ( $apply )
                $this->usage( '--list changes nothing; leave --apply out' );
            $this->finish( array( 'mode' => 'captions', 'captions' => \expUpgradeCaption::all( $dir ), 'errors' => array(), 'warnings' => array(),
                                  'exit' => \expUpgrade::EXIT_OK ), $format );
            return;
        }
        $label = isset( $options['label'] ) && $options['label'] !== false ? trim( preg_replace( '/\s+/', ' ', (string)$options['label'] ) ) : '';
        if ( $label === '' )
            $label = 'manual caption ' . date( 'Y-m-d H:i:s' );
        $label = mb_substr( $label, 0, 250 );
        $db = $this->database( $format );
        $report = array( 'mode' => $apply ? 'caption' : 'caption-dry-run', 'label' => $label, 'caption' => null, 'errors' => array(), 'warnings' => array(),
                         'exit' => \expUpgrade::EXIT_OK );
        if ( !$apply )
        {
            $report['warnings'][] = 'dry run: no caption taken (--apply takes it)';
            $this->finish( $report, $format );
            return;
        }
        $context = new \expMigrationContext( $db, true, array( 'no-mirror' => true, 'backup-dir' => $dir ) );
        $lock = $context->lock();
        if ( !$lock->acquire( array( 'purpose' => 'migration caption', 'user' => 'cli' ) ) )
        {
            $report['errors'][] = 'another run holds the lock: ' . \expMigrationLock::describe( $lock->holder() );
            $report['exit'] = \expUpgrade::EXIT_FAILED;
            $this->finish( $report, $format );
            return;
        }
        try
        {
            $caption = \expUpgradeCaption::take( 'manual', $label, $context );
        }
        catch ( \Throwable $e )
        {
            $caption = array( 'ok' => false, 'id' => '', 'source' => '', 'messages' => array( $e->getMessage() ) );
        }
        $lock->release();
        $report['caption'] = array( 'id' => (string)$caption['id'], 'ok' => !empty( $caption['ok'] ), 'source' => (string)$caption['source'],
                                    'messages' => array_values( (array)$caption['messages'] ) );
        $context->audit( 'system.migration.caption', array( 'object' => array( 'type' => 'caption', 'id' => (string)$caption['id'] ), 'verb' => 'take',
                                                            'result' => !empty( $caption['ok'] ) ? 'success' : 'failure',
                                                            'x' => array( 'kind' => 'manual', 'source' => (string)$caption['source'] ) ) );
        if ( empty( $caption['ok'] ) )
        {
            $report['errors'][] = 'the caption failed: ' . implode( '; ', (array)$caption['messages'] );
            $report['exit'] = \expUpgrade::EXIT_FAILED;
        }
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        if ( $report['mode'] === 'captions' )
        {
            $lines = array( 'Exponential migrations: the captions, newest first' );
            foreach ( $report['captions'] as $c )
            {
                $lines[] = sprintf( '  %-24s %-17s %s  %-10s %s', $c['id'], $c['kind'], date( 'Y-m-d H:i', $c['created'] ), $c['engine'] ?: '-', $c['label'] );
                $lines[] = '      ' . $c['rollback'];
            }
            $lines[] = count( $report['captions'] ) . ' caption(s)';
            return $lines;
        }
        $lines = array( 'Exponential migrations: ' . ( $report['mode'] === 'caption' ? 'caption' : 'caption, dry run (--apply takes it)' ),
                        'Label: ' . $report['label'] );
        if ( $report['caption'] )
        {
            $lines[] = 'Caption: ' . ( $report['caption']['id'] !== '' ? $report['caption']['id'] . ' (' . $report['caption']['source'] . ')' : '-' );
            foreach ( $report['caption']['messages'] as $message )
                $lines[] = '  ' . $message;
        }
        return $lines;
    }
}

}
