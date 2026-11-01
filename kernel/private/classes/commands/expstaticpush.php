<?php
/**
 * @description Push the static publishing targets whose push failed again (runs with push state pending), never forced
 * @alias exp:static:push
 *
 * File containing the exp:static:push command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expstaticpush extends ExpstaticBase
{
    protected function name()
    {
        return 'exp:static:push';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Push waiting static publishes again\n" .
                             "A publish whose push failed (the remote was not reachable, the credential was missing) keeps its\n" .
                             "commit and tag in the target's folder with the push state pending. This command pushes those\n" .
                             "targets again, the branch with every commit and the tags, never forced. The cronjob part\n" .
                             "staticpublish does the same.\n" .
                             "\n" .
                             "./console exp:static:push                      every target with a waiting push\n" .
                             "./console exp:static:push github-public",
        ) + self::SCRIPT );
        return (array)$this->startup( '[format:]', '[target]', array(
            'format' => 'text (default) or json (the machine output exp-static/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $targets = array();
        if ( isset( $options['arguments'][0] ) && trim( (string)$options['arguments'][0] ) !== '' )
        {
            $target = $this->target( $options, $format );
            $targets[$target->id()] = $target;
        }
        else
            $targets = \expStaticTarget::all();
        $report = array( 'mode' => 'push', 'targets' => array(), 'errors' => array(), 'warnings' => array() );
        if ( !\expStaticTarget::enabled() )
        {
            $report['errors'][] = 'Static publishing is switched off: staticcache.ini [StaticPublishSettings] Enabled=disabled';
            $this->finish( $report, $format );
        }
        $db = $this->database( $format );
        if ( \expStaticStatus::exists( $db ) !== true )
            $report['warnings'][] = 'the tables exp_static_run and exp_static_page do not exist yet (./console exp:upgrade --apply, step 6.0.15-static-publish-tables): no run is known to wait';
        foreach ( $targets as $id => $target )
        {
            $one = $this->publisher( $target, $db )->retry();
            $report['targets'][] = $one;
            foreach ( $one['errors'] as $error )
                $report['errors'][] = "$id: $error";
        }
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $lines = array();
        foreach ( $report['targets'] as $one )
        {
            $lines[] = sprintf( '%s: %s', $one['target'], $one['runs'] ? count( $one['runs'] ) . ' run(s) waited, push ' . $one['push_state'] : 'nothing waits' );
            foreach ( $one['messages'] as $message )
                $lines[] = '  ' . $message;
        }
        return $lines;
    }
}

}
