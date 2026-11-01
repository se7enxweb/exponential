<?php
/**
 * @description The state of a static publishing target: its git repository, the credential (never its value), the last runs, waiting pushes and stale pages
 * @alias exp:static:status
 *
 * File containing the exp:static:status command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expstaticstatus extends ExpstaticBase
{
    protected function name()
    {
        return 'exp:static:status';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "The state of a static publishing target\n" .
                             "Shows the target's git repository (branch, last commit, last tag static/<target>/<n>, changes not\n" .
                             "published, commits not pushed), whether its credential can be read (never its value), the last\n" .
                             "publishes and rollbacks of exp_static_run with their push state, and the pages of exp_static_page\n" .
                             "with how many are stale.\n" .
                             "\n" .
                             "./console exp:static:status github-public\n" .
                             "./console exp:static:status github-public --limit=30 --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[limit:][format:]', '[target]', array(
            'limit' => 'How many of the last runs to list (default 10)',
            'format' => 'text (default) or json (the machine output exp-static/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $limit = isset( $options['limit'] ) && $options['limit'] !== false ? (string)$options['limit'] : '10';
        if ( !ctype_digit( $limit ) || (int)$limit < 1 )
            $this->usage( "--limit must be a number, not $limit" );
        $target = $this->target( $options, $format );
        $db = $this->database( $format );
        $publisher = $this->publisher( $target, $db );
        $credentials = $publisher->credentials();
        $problem = $credentials->problem( $target );
        $credentials->cleanup();
        $tables = \expStaticStatus::exists( $db ) === true;
        $pages = \expStaticStatus::pages( $db, $target->id() );
        $report = array( 'mode' => 'status', 'target' => $target->id(), 'enabled' => \expStaticTarget::enabled(), 'directory' => $publisher->checkoutDirectory(),
                         'remote' => (string)$target->setting( 'Remote' ), 'branch' => (string)$target->setting( 'Branch' ),
                         'folder' => $target->folder(), 'credential' => $credentials->describe(), 'credential_problem' => $problem,
                         'repository' => $publisher->repository()->state(), 'tables' => $tables,
                         'runs' => \expStaticStatus::runs( $db, $target->id(), (int)$limit ),
                         'pending' => count( \expStaticStatus::pending( $db, $target->id() ) ),
                         'pages' => count( $pages ), 'stale' => count( array_filter( array_column( $pages, 'stale' ) ) ),
                         'errors' => array(), 'warnings' => array() );
        if ( !$tables )
            $report['warnings'][] = 'the tables exp_static_run and exp_static_page do not exist yet: ./console exp:upgrade --apply (step 6.0.15-static-publish-tables)';
        if ( $problem !== '' && (string)$target->setting( 'Remote' ) !== '' )
            $report['warnings'][] = 'the credential cannot be used: ' . $problem;
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $r = $report['repository'];
        $lines = array( 'Static publishing: ' . $report['target'] . ( $report['enabled'] ? '' : ' (switched off)' ) );
        $lines[] = '  Folder:     ' . $report['directory'] . ( $report['folder'] !== '' ? ' (the site in ' . $report['folder'] . '/)' : '' );
        $lines[] = '  Remote:     ' . ( $report['remote'] !== '' ? $report['remote'] : '(none)' ) . ', branch ' . ( $r['branch'] !== '' ? $r['branch'] : $report['branch'] );
        $lines[] = '  Credential: ' . $report['credential'] . ( $report['credential_problem'] === '' ? ' (readable)' : '' );
        if ( !$r['repository'] )
            $lines[] = '  Repository: none yet (the first exp:static:publish makes it)';
        else
            $lines[] = sprintf( '  Repository: %s%s, %d tag(s), last %s; %d change(s) not published%s', $r['head'] !== '' ? substr( $r['head'], 0, 12 ) : 'no commit',
                                $r['subject'] !== '' ? ' "' . $r['subject'] . '"' : '', $r['tags'], $r['last_tag'] !== '' ? $r['last_tag'] : '(none)', $r['uncommitted'],
                                $r['unpushed'] !== null ? ', ' . $r['unpushed'] . ' commit(s) not pushed' : '' );
        $lines[] = sprintf( '  Pages:      %d published, %d stale; %d run(s) wait for a push', $report['pages'], $report['stale'], $report['pending'] );
        if ( $report['runs'] )
        {
            $lines[] = '';
            $lines[] = '  Run                     Kind      State      Tag                          Push       By';
            foreach ( $report['runs'] as $run )
                $lines[] = sprintf( '  %-23s %-9s %-10s %-28s %-10s %s', $run['run_id'], $run['kind'], $run['state'], $run['tag_name'] !== '' ? $run['tag_name'] : '-',
                                    $run['push_state'], $run['run_by'] );
        }
        return $lines;
    }
}

}
