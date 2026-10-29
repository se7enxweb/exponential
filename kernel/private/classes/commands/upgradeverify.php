<?php
/**
 * @description Upgrade verify: re-detect every step and report drift; --after: the full check after an upgrade (steps, smoke addresses, tests, error log, autoloads)
 * @alias exp:upgrade:verify
 *
 * File containing the exp:upgrade:verify command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgradeverify extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade:verify';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade verify\n" .
                             "Looks at every step again and lists drift: a step exp:upgrade recorded as done whose effect is\n" .
                             "missing now (a table dropped, a policy removed). Exit 1 when there is drift. Changes nothing.\n" .
                             "With --after, the check exp:upgrade --apply runs at its end: the steps (no drift, none failed),\n" .
                             "the smoke addresses ([UpgradeSettings] SmokeURL[], else velocity.ini WarmUpURL[], else the front\n" .
                             "page and the sign-in page: Apache and Velocity), the test suite upgrade-smoke, no new fatal line in\n" .
                             "the error logs since --since, and the autoloads. Exit 1 when a check fails.\n" .
                             "\n" .
                             "./console exp:upgrade:verify\n" .
                             "./console exp:upgrade:verify --after\n" .
                             "./console exp:upgrade:verify --after --url=https://www.example.com/ --no-tests --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[stages:][format:][after][url:*][no-tests][since:]', '', array(
            'stages' => 'Only these stages, a comma list of code, libraries, database, scripts, finish (in that order)',
            'format' => 'text (default) or json (the machine output exp-upgrade/1)',
            'after' => 'The verification after an upgrade: steps, smoke addresses, tests, error log, autoloads',
            'url' => 'The addresses the smoke check requests after the deploy (a comma list or repeated); default the front page of SiteURL',
            'no-tests' => 'Leave the test suite upgrade-smoke out of the verification',
            'since' => 'The error log is read from this moment on: a Unix time (default an hour ago)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $stages = $this->stageOption( $options );
        if ( !empty( $options['after'] ) )
        {
            $report = \expUpgrade::report( 'verify-after', array( 'command' => $this->name() ) );
            $report['after'] = \expUpgradeVerifyAfter::run( array(
                'url' => isset( $options['url'] ) ? \expUpgrade::listOption( $options['url'] ) : array(),
                'no-tests' => !empty( $options['no-tests'] ),
                'since' => !empty( $options['since'] ) ? (int)$options['since'] : null,
            ) );
            $report['exit'] = $report['after']['ok'] ? \expUpgrade::EXIT_OK : \expUpgrade::EXIT_FAILED;
            if ( $format === 'json' )
                $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            else
            {
                $this->output( 'Exponential upgrade: verification after the upgrade' );
                $this->printVerify( $report['after'] );
                $this->output( $report['exit'] === 0 ? 'PASS' : 'FAIL (exit 1)' );
            }
            $this->shutdown( $report['exit'] );
            return;
        }
        $report = \expUpgrade::verify( $stages, array( 'command' => $this->name() ) );
        if ( $format === 'text' && $report['drift'] )
            foreach ( $report['drift'] as $id )
                $report['errors'][] = "drift: $id is recorded as done, but its effect is missing";
        $this->finish( $report, $format );
    }
}

}
