<?php
/**
 * @description Publish a target's static build (static publishing): commit it in the target's git repository, tag it static/<target>/<n> and push it, never forced
 * @alias exp:static:publish
 *
 * File containing the exp:static:publish command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expstaticpublish extends ExpstaticBase
{
    protected function name()
    {
        return 'exp:static:publish';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Publish a static build to its git remote\n" .
                             "Commits the build in the target's folder (var/<site>/static-publish/<target>/, a git repository of\n" .
                             "its own: only the files of the build), tags the commit static/<target>/<n> and pushes the branch and\n" .
                             "the tag to the target's Remote, signed in with its Credential (a token from Setup > Secrets, a deploy\n" .
                             "key, or none). A push is never forced; a push that fails stays local and exp:static:push or the\n" .
                             "cronjob part staticpublish tries again. Every publish is a row of exp_static_run.\n" .
                             "\n" .
                             "./console exp:static:publish github-public --build\n" .
                             "./console exp:static:publish github-public --nodes=2,57             build these nodes' pages, then publish\n" .
                             "./console exp:static:publish github-public --message=\"New opening hours\"\n" .
                             "./console exp:static:publish github-public --no-push                commit and tag only",
        ) + self::SCRIPT );
        return (array)$this->startup( '[build][nodes:*][message:][no-push][format:][login:][run-id:][progress:]', '[target]', array(
            'build' => 'Build the target first (as exp:static:build); nothing is committed when the build does not finish',
            'nodes' => 'Build only the pages of these nodes (node ids; a comma list or repeated), in each siteaccess of the target; the rest of the last build stays',
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
        $message = isset( $options['message'] ) && $options['message'] !== false ? (string)$options['message'] : '';
        $nodes = isset( $options['nodes'] ) && $options['nodes'] !== false ? self::listOption( $options['nodes'] ) : array();
        foreach ( $nodes as $node )
            if ( !ctype_digit( $node ) || (int)$node < 1 )
                $this->usage( "--nodes takes node ids, not $node" );
        $job = $this->jobOptions( $options );
        $target = $this->target( $options, $format );
        $this->requireEnabled( $target, 'publish', $format );
        $db = $this->database( $format );
        $command = $this;
        $publisher = $this->publisher( $target, $db, array(
            'emit' => self::bothEmit( $format === 'json' ? null : function ( $type, $text ) use ( $command ) { $command->progress( $type, $text ); }, $job['progress'] ),
            'push' => !empty( $options['no-push'] ) ? false : null,
            'by' => $job['by'],
            'run_id' => $job['run_id'],
            // --nodes: an incremental build of these nodes' pages; the rest of the last build stays
            'build' => $nodes ? array( 'nodes' => array_map( 'intval', $nodes ) ) : array(),
        ) );
        $this->finish( $publisher->publish( array( 'build' => !empty( $options['build'] ) || $nodes, 'message' => $message ) ), $format );
    }

    /**
     * A line of progress (text output). Public for the closure.
     *
     * @param string $type
     * @param string $message
     */
    public function progress( $type, $message )
    {
        if ( $type === 'warn' || $type === 'error' || $type === 'report' )
            return;
        $this->output( ( $type === 'phase' ? '' : '  ' ) . $message );
    }

    protected function lines( array $report )
    {
        return self::gitLines( $report );
    }
}

}
