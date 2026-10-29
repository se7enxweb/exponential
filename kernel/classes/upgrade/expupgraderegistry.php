<?php
/**
 * File containing the expUpgradeRegistry class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The registered upgrade steps, in the order they run.
 *
 *   upgrade.ini [UpgradeSettings] StepList[]           the kernel's steps (class names)
 *   upgrade.ini [UpgradeSettings] ExtensionStepList[]  steps of extensions, added by their settings/upgrade.ini.append.php
 *
 * Order: by stage (code, libraries, database, scripts, finish), then by version (version_compare), then as declared;
 * a step that names another in after() comes after it. A class that does not exist or does not implement
 * expUpgradeStep, a duplicate id and an after() that names no step or makes a cycle are reported in errors().
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeRegistry
{
    /** The stages in the order they run (permanent). */
    const STAGES = array( 'code', 'libraries', 'database', 'scripts', 'finish' );

    /** @var expUpgradeStep[] */
    protected $steps = array();

    /** @var string[] */
    protected $errors = array();

    /**
     * @param expUpgradeStep[] $steps in declared order
     * @param string[] $errors problems found while loading
     */
    public function __construct( array $steps, array $errors = array() )
    {
        $this->errors = $errors;
        $this->steps = self::order( $steps, $this->errors );
    }

    /**
     * The registry of the installation's settings.
     *
     * @param eZINI|null $ini upgrade.ini
     * @return expUpgradeRegistry
     */
    public static function fromSettings( $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'upgrade.ini' );
        $classes = array();
        foreach ( array( 'StepList', 'ExtensionStepList' ) as $list )
            if ( $ini->hasVariable( 'UpgradeSettings', $list ) )
                foreach ( (array)$ini->variable( 'UpgradeSettings', $list ) as $class )
                    if ( trim( (string)$class ) !== '' )
                        $classes[] = trim( (string)$class );
        return self::fromClasses( $classes );
    }

    /**
     * @param string[] $classes
     * @return expUpgradeRegistry
     */
    public static function fromClasses( array $classes )
    {
        $steps = array();
        $errors = array();
        foreach ( array_unique( $classes ) as $class )
        {
            if ( !class_exists( $class ) )
            {
                $errors[] = "the step class $class does not exist (autoloads not regenerated, or the extension is not active)";
                continue;
            }
            $step = new $class();
            if ( !$step instanceof expUpgradeStep )
            {
                $errors[] = "the class $class is not an upgrade step (expUpgradeStep)";
                continue;
            }
            $steps[] = $step;
        }
        return new self( $steps, $errors );
    }

    /** @return expUpgradeStep[] every step, in order */
    public function steps()
    {
        return $this->steps;
    }

    /**
     * The steps of some stages, in order.
     *
     * @param string[] $stages
     * @return expUpgradeStep[]
     */
    public function stageSteps( array $stages )
    {
        $out = array();
        foreach ( $this->steps as $step )
            if ( in_array( $step->stage(), $stages, true ) )
                $out[] = $step;
        return $out;
    }

    /**
     * @param string $id
     * @return expUpgradeStep|null
     */
    public function get( $id )
    {
        foreach ( $this->steps as $step )
            if ( $step->id() === $id )
                return $step;
        return null;
    }

    /** @return string[] */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * The steps in the order they run. Pure: no settings, no database.
     *
     * @param expUpgradeStep[] $steps in declared order
     * @param string[] $errors problems are added here
     * @return expUpgradeStep[]
     */
    public static function order( array $steps, array &$errors = array() )
    {
        $byId = array();
        $declared = array();
        foreach ( array_values( $steps ) as $i => $step )
        {
            $id = $step->id();
            if ( isset( $byId[$id] ) )
            {
                $errors[] = "two steps have the id $id (" . get_class( $byId[$id] ) . ', ' . get_class( $step ) . ')';
                continue;
            }
            if ( !in_array( $step->stage(), self::STAGES, true ) )
            {
                $errors[] = "the step $id names the unknown stage " . $step->stage();
                continue;
            }
            $byId[$id] = $step;
            $declared[$id] = $i;
        }
        $stageRank = array_flip( self::STAGES );
        $ids = array_keys( $byId );
        usort( $ids, function ( $a, $b ) use ( $byId, $declared, $stageRank ) {
            $sa = $stageRank[$byId[$a]->stage()];
            $sb = $stageRank[$byId[$b]->stage()];
            if ( $sa !== $sb )
                return $sa - $sb;
            $v = version_compare( $byId[$a]->version(), $byId[$b]->version() );
            if ( $v !== 0 )
                return $v;
            return $declared[$a] - $declared[$b];
        } );

        // dependencies: a step waits for what it names in after(); stable otherwise
        foreach ( $byId as $id => $step )
            foreach ( $step->after() as $dep )
                if ( !isset( $byId[$dep] ) )
                    $errors[] = "the step $id runs after $dep, which is not registered";
        $ordered = array();
        $placed = array();
        $remaining = $ids;
        while ( $remaining )
        {
            $progress = false;
            foreach ( $remaining as $k => $id )
            {
                $ready = true;
                foreach ( $byId[$id]->after() as $dep )
                    if ( isset( $byId[$dep] ) && !isset( $placed[$dep] ) )
                        $ready = false;
                if ( !$ready )
                    continue;
                $ordered[] = $byId[$id];
                $placed[$id] = true;
                unset( $remaining[$k] );
                $progress = true;
                break;
            }
            if ( !$progress )
            {
                $errors[] = 'the steps ' . implode( ', ', $remaining ) . ' wait for each other (a cycle in after())';
                foreach ( $remaining as $id )
                    $ordered[] = $byId[$id];
                break;
            }
        }
        // a dependency may not sit in a later stage
        foreach ( $ordered as $step )
            foreach ( $step->after() as $dep )
                if ( isset( $byId[$dep] ) && $stageRank[$byId[$dep]->stage()] > $stageRank[$step->stage()] )
                    $errors[] = 'the step ' . $step->id() . " runs after $dep, which is in a later stage";
        return $ordered;
    }
}
