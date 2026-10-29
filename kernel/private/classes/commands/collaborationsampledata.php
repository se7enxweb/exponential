<?php
/**
 * @description Build sample content for the collaboration tool (approvals, comments, groups), or remove it again (dry run unless --apply)
 * @alias exp:collaboration:sample-data
 *
 * File containing the exp:collaboration:sample-data command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Collaborationsampledata extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $script = $this->script(
            array(
                'description' => "Collaboration sample data\n" .
                                 "Builds a realistic set for the collaboration tool: the user group \"Editors (sample)\" with three\n" .
                                 "editors (e-mail addresses on example.invalid, random passwords that are not shown), the workflow\n" .
                                 "\"Approval (sample)\" that only reaches the section \"Sample: collaboration\", a folder in that\n" .
                                 "section and articles that wait for approval, were approved or were denied, with comment threads.\n" .
                                 "Everything is made through the kernel's own approval workflow and handler, and is marked\n" .
                                 "(names start with \"Sample: \", remote ids with \"sample-collab-\") so --remove deletes exactly it.\n" .
                                 "The command is idempotent: what exists is left as it is.\n" .
                                 "Without --apply nothing is changed: the command lists what it would create (or, with --remove,\n" .
                                 "delete). --dry-run means the same as no flag; --apply together with --dry-run is a usage error\n" .
                                 "(exit 2).\n" .
                                 "\n" .
                                 "./console exp:collaboration:sample-data\n" .
                                 "./console exp:collaboration:sample-data --apply\n" .
                                 "./console exp:collaboration:sample-data --remove --apply",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );

        $options = $this->startup(
            "[apply][dry-run][remove]",
            "",
            array( 'apply'   => 'Make the changes (the default is a dry run that shows them and changes nothing)',
                   'dry-run' => 'The default, kept for scripts that pass it: show what would be done and change nothing',
                   'remove'  => 'Delete the sample data (and only that)' ) );

        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $script->shutdown( 2 );
        }
        $dryRun = !$apply;

        $data = new \expCollaborationSampleData( function ( $message ) use ( $cli ) {
            $cli->output( '  ' . $message );
        } );

        $remove = (bool)$options['remove'];

        try
        {
            if ( $dryRun )
            {
                $cli->output( $remove ? 'Dry run: --remove would delete these sample items (nothing is changed).'
                                      : 'Dry run: this is what the command would do now (nothing is changed).' );
                $todo = 0;
                foreach ( $data->plan() as $row )
                {
                    $present = $row['state'] === 'exists';
                    if ( $remove )
                        $line = $present ? 'would remove' : 'nothing to remove';
                    else
                        $line = $present ? 'exists, kept' : ( $row['state'] === 'missing' ? 'would create' : $row['state'] );
                    if ( $remove ? $present : $row['state'] === 'missing' )
                        ++$todo;
                    $cli->output( sprintf( '  %-32s %-60s %s', $row['what'], $row['name'], $line ) );
                }
                if ( $remove )
                    $cli->output( sprintf( '  %d collaboration item(s) and %d collaboration group(s) would also be removed.',
                                           count( $data->sampleItemIDs() ), count( $data->sampleCollaborationGroupIDs() ) ) );
                $cli->output( 'PASS: ' . $todo . ' step(s) pending (dry run, nothing changed; --apply writes)' );
                $script->shutdown( 0 );
                return;
            }

            if ( $remove )
            {
                if ( !$dryRun )
                    $counts = $data->remove();
                $cli->output( sprintf( 'PASS: removed %d item(s), %d object(s), %d group(s), %d workflow(s), %d other.',
                                       $counts['items'], $counts['objects'], $counts['groups'], $counts['workflows'], $counts['other'] ) );
                $script->shutdown( 0 );
                return;
            }

            if ( !$dryRun )
                $counts = $data->apply();
            $cli->output( sprintf( 'PASS: %d created, %d already there%s.', $counts['created'], $counts['skipped'],
                                   $counts['warnings'] ? ', ' . $counts['warnings'] . ' warning(s)' : '' ) );
            $script->shutdown( 0 );
        }
        catch ( \Exception $e )
        {
            $cli->error( 'FAIL: ' . $e->getMessage() );
            $script->shutdown( 1 );
        }
    }
}

}
