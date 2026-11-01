<?php
/**
 * @description Migration status: every migration with its state (done, pending, not-needed, failed, unknown), the latest runs and what waits
 * @alias exp:migration:status
 *
 * File containing the exp:migration:status command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationstatus extends ExpmigrationBase
{
    protected function name()
    {
        return 'exp:migration:status';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Migration status\n" .
                             "Every migration of the site, the active extensions and the installed packages for the environment,\n" .
                             "in the order they run, with its state (done, pending, not-needed, failed, unknown) and its record;\n" .
                             "the records whose file is gone, the latest runs, the runs whose push waits and who holds the lock.\n" .
                             "Changes nothing, takes no lock.\n" .
                             "\n" .
                             "./console exp:migration:status\n" .
                             "./console exp:migration:status --environment=production --format=json\n" .
                             "./console exp:migration:status --fail-on-pending     exit 1 when something is pending, for CI",
        ) + self::SCRIPT );
        return (array)$this->startup( '[only:*][environment:][format:][fail-on-pending][runs:]', '', array(
            'only' => 'Only these migrations (migration ids, a comma list or repeated)',
            'environment' => 'The environment (default the installation\'s: [AdminEnvironment] Level, or EXP_ENV with Level=auto; check: every environment); only the migrations for it count',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'fail-on-pending' => 'Exit 1 when migrations are pending or failed (for CI)',
            'runs' => 'How many of the latest runs to list (default 10, 0 for none)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $selection = $this->selection( $options );
        if ( isset( $options['runs'] ) && $options['runs'] !== false && !ctype_digit( (string)$options['runs'] ) )
            $this->usage( '--runs must be a number' );
        $db = $this->database( $format );
        $report = \expMigrationStatus::report( $selection + array( 'db' => $db, 'runs' => isset( $options['runs'] ) && $options['runs'] !== false ? (int)$options['runs'] : 10 ) );
        if ( !empty( $options['fail-on-pending'] ) && $report['exit'] === \expUpgrade::EXIT_OK && $report['summary']['pending'] + $report['summary']['failed'] > 0 )
            $report['exit'] = \expUpgrade::EXIT_FAILED;
        $this->finish( $report, $format );
    }
}

}
