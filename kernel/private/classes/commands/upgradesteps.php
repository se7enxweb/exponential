<?php
/**
 * @description Upgrade steps: the registered steps with id, stage, version, engines and description
 * @alias exp:upgrade:steps
 *
 * File containing the exp:upgrade:steps command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgradesteps extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade:steps';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade steps\n" .
                             "The registered upgrade steps in the order they run: id, stage, version, engines, owner (kernel\n" .
                             "or the extension that registers it), whether it runs only when named (optional) and what it\n" .
                             "does. Reads the settings only, never the database.\n" .
                             "\n" .
                             "./console exp:upgrade:steps\n" .
                             "./console exp:upgrade:steps --stage=database --engine=postgresql --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[stage:][engine:][format:]', '', array(
            'stage' => 'Only the steps of this stage',
            'engine' => 'Only the steps for this engine: mysql, postgresql, sqlite, oracle, mongodb',
            'format' => 'text (default) or json (the machine output exp-upgrade/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $stage = isset( $options['stage'] ) && $options['stage'] ? (string)$options['stage'] : null;
        if ( $stage !== null && !in_array( $stage, \expUpgradeRegistry::STAGES, true ) )
            $this->usage( "unknown stage $stage; stages: " . implode( ', ', \expUpgradeRegistry::STAGES ) );
        $engine = isset( $options['engine'] ) && $options['engine'] ? (string)$options['engine'] : null;
        $registry = \expUpgradeRegistry::fromSettings();
        $kernel = (array)\eZINI::instance( 'upgrade.ini' )->variable( 'UpgradeSettings', 'StepList' );
        $steps = array();
        foreach ( $registry->steps() as $step )
        {
            if ( $stage !== null && $step->stage() !== $stage )
                continue;
            if ( $engine !== null && !in_array( '*', $step->engines(), true ) && !in_array( $engine, $step->engines(), true ) )
                continue;
            $steps[] = array(
                'id' => $step->id(),
                'stage' => $step->stage(),
                'version' => $step->version(),
                'engines' => array_values( $step->engines() ),
                'after' => array_values( $step->after() ),
                'optional' => (bool)$step->optional(),
                'owner' => in_array( get_class( $step ), $kernel, true ) ? 'kernel' : 'extension',
                'class' => get_class( $step ),
                'reversible' => false,
                'description' => $step->description(),
            );
        }
        $report = \expUpgrade::report( 'steps', array( 'command' => $this->name() ) );
        $report['steps'] = $steps;
        $report['warnings'] = $registry->errors();
        if ( $format === 'json' )
        {
            $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            $this->shutdown( 0 );
            return;
        }
        $current = '';
        foreach ( $steps as $s )
        {
            if ( $s['stage'] !== $current )
            {
                $current = $s['stage'];
                $this->output();
                $this->output( "Stage $current" );
            }
            $this->output( sprintf( '  %-36s %-8s %-30s %s%s%s', $s['id'], $s['version'], implode( ',', $s['engines'] ), $s['owner'],
                                    $s['optional'] ? ', optional' : '', $s['after'] ? ', after ' . implode( ', ', $s['after'] ) : '' ) );
            $this->output( '    ' . $s['description'] );
        }
        foreach ( $registry->errors() as $e )
            $this->output( 'WARN ' . $e );
        $this->output();
        $this->output( count( $steps ) . ' step(s)' );
        $this->shutdown( 0 );
    }
}

}
