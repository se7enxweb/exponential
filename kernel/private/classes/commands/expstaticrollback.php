<?php
/**
 * @description Roll a static publishing target back to an earlier publish: a new commit with the files of that tag, tagged and pushed; the history stays
 * @alias exp:static:rollback
 *
 * File containing the exp:static:rollback command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expstaticrollback extends ExpstaticBase
{
    protected function name()
    {
        return 'exp:static:rollback';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Roll a static site back to an earlier publish\n" .
                             "Names an earlier publish by its number or its tag static/<target>/<n> (exp:static:status lists\n" .
                             "them) and lists what would change. With --apply it makes a new commit that holds the files of that\n" .
                             "publish, tags it with the next number and pushes it: nothing is deleted from the history, no tag is\n" .
                             "moved and the push is never forced, so a rollback can itself be rolled back.\n" .
                             "\n" .
                             "./console exp:static:rollback github-public 4                 what would change\n" .
                             "./console exp:static:rollback github-public 4 --apply\n" .
                             "./console exp:static:rollback github-public static/github-public/4 --apply --no-push",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][message:][no-push][format:][login:][run-id:][progress:]', '[target][to]', array(
            'apply' => 'Make the rollback commit; without it the command only lists what would change',
            'message' => 'The commit message (default: what was published, or which publish is restored)',
            'no-push' => 'Commit and tag, but do not push (exp:static:push pushes later)',
            'format' => 'text (default) or json (the machine output exp-static/1)',
            'login' => 'Run as this user (the administration starts its jobs so); recorded as who ran it',
            'run-id' => 'The id of the run (date-time-6 hex digits) instead of a new one (the administration passes its job\'s id)',
            'progress' => 'Also append the progress, one JSON object per line, to this file (the administration follows it)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $to = isset( $options['arguments'][1] ) ? trim( (string)$options['arguments'][1] ) : '';
        $job = $this->jobOptions( $options );
        $target = $this->target( $options, $format );
        if ( $to === '' )
            $this->usage( 'name the publish to go back to: exp:static:rollback ' . $target->id() . ' <n|static/' . $target->id() . '/<n>>' );
        if ( \expStaticRepository::tagNumber( $to, $target->id() ) < 1 )
            $this->usage( "$to is not a publish of " . $target->id() . ': a number or static/' . $target->id() . '/<n>' );
        $this->requireEnabled( $target, 'rollback', $format );
        $db = $this->database( $format );
        $publisher = $this->publisher( $target, $db, array( 'push' => !empty( $options['no-push'] ) ? false : null, 'emit' => $job['progress'],
                                                            'by' => $job['by'], 'run_id' => $job['run_id'] ) );
        $message = isset( $options['message'] ) && $options['message'] !== false ? (string)$options['message'] : '';
        $this->finish( $publisher->rollback( $to, array( 'apply' => !empty( $options['apply'] ), 'message' => $message ) ), $format );
    }

    protected function lines( array $report )
    {
        return self::gitLines( $report );
    }
}

}
