<?php
/**
 * File containing the expUpgradeScriptStep class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A step of the scripts stage: a PHP upgrade step (update/common/scripts/6.0/*.php, a setup command) run through the
 * class that command uses. The command stays as it is; the step asks the same class what is missing and applies the
 * same plan, with the command's default options.
 *
 * A subclass implements changes() (what is missing, one line each; empty when in place; null when it cannot be told)
 * and write(). detect() and plan() follow from changes().
 *
 * @since 6.0.15
 * @package kernel
 */
abstract class expUpgradeScriptStep extends expUpgradeBaseStep
{
    /** @var string why detect() could not tell, or why the step does not apply */
    protected $reason = '';

    /** @var bool set by changes() when the step does not apply to this installation */
    protected $notApplicable = false;

    public function stage()
    {
        return 'scripts';
    }

    /**
     * What is missing, one line per change: empty when everything is in place, null when it cannot be told (set
     * $this->reason) or when the step does not apply (set $this->notApplicable and $this->reason).
     *
     * @param expUpgradeContext $context
     * @return string[]|null
     */
    abstract protected function changes( expUpgradeContext $context );

    /**
     * Makes the changes; returns what was done.
     *
     * @param expUpgradeContext $context
     * @return string[]
     */
    abstract protected function write( expUpgradeContext $context );

    public function detect( expUpgradeContext $context )
    {
        $this->reason = '';
        $this->notApplicable = false;
        $changes = $this->changes( $context );
        if ( $this->notApplicable )
            return self::effect( self::NOT_APPLICABLE, $this->reason );
        if ( $changes === null )
            return self::effect( self::UNKNOWN, $this->reason !== '' ? $this->reason : 'cannot tell' );
        if ( $changes )
            return self::effect( self::MISSING, count( $changes ) . ' change(s) to make' );
        return self::effect( self::PRESENT, $this->presentDetail() );
    }

    /** @return string */
    protected function presentDetail()
    {
        return 'in place';
    }

    public function plan( expUpgradeContext $context )
    {
        return (array)$this->changes( $context );
    }

    public function apply( expUpgradeContext $context )
    {
        $done = $this->write( $context );
        $this->reason = '';
        $this->notApplicable = false;
        $left = $this->changes( $context );
        if ( $left )
            throw new RuntimeException( 'still missing after applying: ' . implode( '; ', $left ) );
        return $done;
    }

    /**
     * A role by name, or null.
     *
     * @param string $name
     * @return eZRole|null
     */
    protected static function role( $name )
    {
        $role = eZRole::fetchByName( $name );
        return is_object( $role ) ? $role : null;
    }
}
