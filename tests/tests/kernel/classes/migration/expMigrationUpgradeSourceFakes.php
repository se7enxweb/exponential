<?php
/**
 * The fakes of the exp:upgrade bridge tests (expMigrationUpgradeSourceTest and the engine checks): upgrade steps whose
 * detection and work a test sets.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/**
 * An upgrade step set up by options: id, stage, after, engines, optional, effect (what detect() answers, or a closure
 * taking the context), apply (a closure taking the context, returning the lines; default: the effect becomes present).
 * calls lists what was called.
 */
class expMigrationUpgradeFakeStep extends expUpgradeBaseStep
{
    public $options;

    public $calls = array();

    public function __construct( array $options = array() )
    {
        $this->options = $options + array( 'id' => '6.0.15-fake', 'stage' => 'database', 'after' => array(), 'engines' => array( '*' ),
                                           'optional' => false, 'effect' => self::MISSING, 'apply' => null, 'version' => '6.0.15' );
    }

    public function id()
    {
        return $this->options['id'];
    }

    public function stage()
    {
        return $this->options['stage'];
    }

    public function version()
    {
        return $this->options['version'];
    }

    public function description()
    {
        return 'fake step ' . $this->options['id'];
    }

    public function engines()
    {
        return $this->options['engines'];
    }

    public function after()
    {
        return $this->options['after'];
    }

    public function optional()
    {
        return $this->options['optional'];
    }

    public function detect( expUpgradeContext $context )
    {
        $this->calls[] = 'detect:' . $context->option( 'mode' );
        $effect = $this->options['effect'];
        if ( $effect instanceof Closure )
            return $effect( $context );
        return self::effect( $effect, "fake: $effect" );
    }

    public function plan( expUpgradeContext $context )
    {
        $this->calls[] = 'plan';
        return array( 'would do ' . $this->options['id'] );
    }

    public function apply( expUpgradeContext $context )
    {
        $this->calls[] = 'apply';
        if ( $this->options['apply'] instanceof Closure )
            return call_user_func( $this->options['apply'], $context );
        $this->options['effect'] = self::PRESENT;
        return array( 'did ' . $this->options['id'] );
    }
}
