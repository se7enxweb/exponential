<?php
/**
 * @description Upgrade verify: re-detect every step and report drift (a step recorded as done whose effect is missing)
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
                             "\n" .
                             "./console exp:upgrade:verify\n" .
                             "./console exp:upgrade:verify --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[stages:][format:]', '', array(
            'stages' => 'Only these stages, a comma list of code, libraries, database, scripts, finish (in that order)',
            'format' => 'text (default) or json (the machine output exp-upgrade/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $stages = $this->stageOption( $options );
        $report = \expUpgrade::verify( $stages, array( 'command' => $this->name() ) );
        if ( $format === 'text' && $report['drift'] )
            foreach ( $report['drift'] as $id )
                $report['errors'][] = "drift: $id is recorded as done, but its effect is missing";
        $this->finish( $report, $format );
    }
}

}
