<?php
/**
 * @description Upgrade status: every upgrade step with its state (done, pending, not-needed, failed, unknown)
 * @alias exp:upgrade:status
 *
 * File containing the exp:upgrade:status command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgradestatus extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade:status';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade status\n" .
                             "Every upgrade step, stage by stage, with its state as the installation shows it: done (applied\n" .
                             "by exp:upgrade), not-needed (in place without it, or not for this installation), pending, failed\n" .
                             "or unknown, and how it was told. Changes nothing, takes no lock.\n" .
                             "\n" .
                             "./console exp:upgrade:status\n" .
                             "./console exp:upgrade:status --stages=database --format=json\n" .
                             "./console exp:upgrade:status --fail-on-pending     exit 1 when something is pending, for CI",
        ) + self::SCRIPT );
        return (array)$this->startup( '[stages:][only:*][format:][fail-on-pending]', '', array(
            'stages' => 'Only these stages, a comma list of code, libraries, database, scripts, finish (in that order)',
            'only' => 'Only these steps (step ids, a comma list or repeated); an optional step runs only when named here',
            'format' => 'text (default) or json (the machine output exp-upgrade/1)',
            'fail-on-pending' => 'Exit 1 when steps are still pending (for CI)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $stages = $this->stageOption( $options );
        $report = \expUpgrade::status( $stages, array(
            'only' => isset( $options['only'] ) ? \expUpgrade::listOption( $options['only'] ) : array(),
            'command' => $this->name(),
        ) );
        if ( !empty( $options['fail-on-pending'] ) && $report['exit'] === \expUpgrade::EXIT_OK && $report['summary']['pending'] > 0 )
            $report['exit'] = \expUpgrade::EXIT_FAILED;
        $this->finish( $report, $format );
    }
}

}
