<?php
/**
 * File containing the expMigrationUpgradeStep class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One upgrade step of exp:upgrade (expUpgradeStep) as a migration of the source "kernel": the adapter
 * expMigrationUpgradeSource makes one for every step, so exp:migration:status lists the kernel's steps beside the
 * site's and the extensions' migrations and exp:migration:migrate runs the pending ones first.
 *
 * Nothing of the step changes: its id is the migration id, its after() the migration's requires(), its engines()
 * the migration's engines, and the state is the one exp:upgrade gives it (expUpgrade::stateOf()): detect-based, from
 * what detect() finds in the installation, the record only telling "done" (applied by exp:upgrade or a migration run)
 * from "not-needed" (in place without either). up() is the step's apply(), checked with detect() afterwards, exactly
 * as exp:upgrade does it.
 *
 * Differences from a site migration, on purpose:
 *
 *   - no checksum guard (guarded() is false): the class of a step changes with the kernel, and its state comes from
 *     detect(), not from the file; the checksum of the class file is still recorded, as exp:upgrade records it
 *   - no transaction around up() (transactional() is false): a step runs as it runs under exp:upgrade
 *   - only the steps of the stages database and scripts are run by a migration run (runnable()). The steps of the
 *     stages code and libraries move the code and the libraries the process itself runs on, and the finish stage
 *     makes a run of exp:upgrade live; they are listed with their state and left to ./console exp:upgrade
 *   - not reversible: a step is undone only by restoring a caption
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationUpgradeStep implements expMigration
{
    /** The source of every upgrade step. */
    const SOURCE = 'kernel';

    /** The stages whose steps a migration run applies. */
    const RUNNABLE_STAGES = array( 'database', 'scripts' );

    /** @var expUpgradeStep */
    protected $step;

    /** @var string what the last state() found (the detail of detect()) */
    protected $detail = '';

    /**
     * @param expUpgradeStep $step
     */
    public function __construct( expUpgradeStep $step )
    {
        $this->step = $step;
    }

    /** @return expUpgradeStep the step this migration presents */
    public function step()
    {
        return $this->step;
    }

    public function id()
    {
        return (string)$this->step->id();
    }

    public function source()
    {
        return self::SOURCE;
    }

    public function description()
    {
        return (string)$this->step->description();
    }

    /** Every environment: the kernel's steps are the same everywhere. */
    public function environments()
    {
        return array();
    }

    /** The steps it runs after (after()). */
    public function requires()
    {
        return array_values( array_map( 'strval', (array)$this->step->after() ) );
    }

    public function maintenance()
    {
        return false;
    }

    public function engines()
    {
        return array_values( (array)$this->step->engines() );
    }

    /** @return string the stage of the step: code, libraries, database, scripts, finish */
    public function stage()
    {
        return (string)$this->step->stage();
    }

    /** @return string the kernel version of the step */
    public function version()
    {
        return (string)$this->step->version();
    }

    /** @return bool whether the step is optional (exp:upgrade runs it only when named with --only) */
    public function optional()
    {
        return (bool)$this->step->optional();
    }

    /**
     * Whether a migration run applies the step: a step of the stages database or scripts that is not optional.
     *
     * @return bool
     */
    public function runnable()
    {
        return in_array( $this->stage(), self::RUNNABLE_STAGES, true ) && !$this->optional();
    }

    /**
     * Why a migration run leaves a pending step to exp:upgrade, with the command that runs it ('' when it runs it).
     *
     * @return string
     */
    public function notRunReason()
    {
        if ( $this->runnable() )
            return '';
        if ( $this->optional() )
            return 'an optional upgrade step: ./console exp:upgrade --apply --only=' . $this->id();
        return 'an upgrade step of the stage ' . $this->stage() . ': ./console exp:upgrade:' . $this->stage() . ' --apply';
    }

    /** @return string the file of the step's class */
    public function file()
    {
        $file = ( new ReflectionClass( $this->step ) )->getFileName();
        return $file && is_readable( $file ) ? $file : '';
    }

    /** @return string|null SHA-256 of the step's class file (expUpgradeRecord::checksum()) */
    public function checksum()
    {
        $checksum = expUpgradeRecord::checksum( $this->step );
        return $checksum !== '' ? $checksum : null;
    }

    /** No checksum guard: the state comes from detect(), and the class changes with the kernel. */
    public function guarded()
    {
        return false;
    }

    /** No transaction around up(): the step runs as under exp:upgrade. */
    public function transactional()
    {
        return false;
    }

    /** @return string what detect() said the last time state() asked */
    public function detail()
    {
        return $this->detail;
    }

    /**
     * The state as exp:upgrade gives it: detect() and the record (expUpgrade::stateOf()).
     *
     * @param expUpgradeContext $context
     * @return string
     */
    public function state( $context )
    {
        $effect = $this->detect( $context );
        $this->detail = (string)$effect['detail'];
        return expUpgrade::stateOf( $effect, $this->record( $context ), true );
    }

    /**
     * What detect() finds; a detection that throws is unknown.
     *
     * @param expUpgradeContext $context
     * @return array effect, detail
     */
    public function detect( $context )
    {
        $restore = $this->prepare( $context );
        try
        {
            $effect = $this->step->detect( $context );
        }
        catch ( Throwable $e )
        {
            $effect = array( 'effect' => expUpgradeStep::UNKNOWN, 'detail' => 'detection failed: ' . $e->getMessage() );
        }
        finally
        {
            $restore();
        }
        return (array)$effect + array( 'effect' => expUpgradeStep::UNKNOWN, 'detail' => '' );
    }

    public function plan( $context )
    {
        $restore = $this->prepare( $context );
        try
        {
            return array_values( array_map( 'strval', (array)$this->step->plan( $context ) ) );
        }
        finally
        {
            $restore();
        }
    }

    /**
     * The step's apply(), then detect() again: a step whose change is still missing afterwards failed (as under
     * exp:upgrade).
     *
     * @param expUpgradeContext $context
     * @return string[]
     * @throws RuntimeException when the step fails, or is not one a migration run applies
     */
    public function up( $context )
    {
        if ( !$this->runnable() )
            throw new RuntimeException( $this->id() . ' is ' . $this->notRunReason() );
        $restore = $this->prepare( $context );
        try
        {
            $done = array_values( array_map( 'strval', (array)$this->step->apply( $context ) ) );
            expKernelTableSchema::reset();
            $after = (array)$this->step->detect( $context ) + array( 'effect' => expUpgradeStep::UNKNOWN, 'detail' => '' );
        }
        finally
        {
            $restore();
        }
        if ( $after['effect'] !== expUpgradeStep::PRESENT && $after['effect'] !== expUpgradeStep::NOT_APPLICABLE )
            throw new RuntimeException( 'applied, but detection still says ' . $after['effect'] . ': ' . $after['detail'] );
        $this->detail = (string)$after['detail'];
        return $done;
    }

    public function reversible()
    {
        return false;
    }

    public function down( $context )
    {
        throw new RuntimeException( $this->id() . ' is an upgrade step: it is undone only by restoring a caption' );
    }

    /**
     * The record of the step in exp:upgrade's shape (expUpgradeRecord::all()): the row of exp_migration the run's
     * context has, else the row of exp_migration read once per run.
     *
     * @param expUpgradeContext $context
     * @return array|null
     */
    protected function record( $context )
    {
        if ( $context instanceof expMigrationContext )
        {
            $row = $context->record( self::SOURCE, $this->id() );
            if ( $row )
                return expMigrationRecord::toLegacy( $row );
        }
        $db = $context->db();
        if ( !$db instanceof eZDBInterface )
            return null;
        $records = $context->option( 'upgrade-records' );
        if ( !is_array( $records ) )
        {
            $records = expUpgradeRecord::all( $db );
            $context->setOption( 'upgrade-records', $records );
        }
        return isset( $records[$this->id()] ) ? $records[$this->id()] : null;
    }

    /**
     * Gives a migration run's context what the steps read from exp:upgrade's: the root of the installation, and the
     * mode "status" while a step of a stage that is not run is asked (the code stage fetches from the remotes in any
     * other mode). Returns what puts the options back.
     *
     * @param expUpgradeContext $context
     * @return callable
     */
    protected function prepare( $context )
    {
        $saved = array();
        $set = array();
        if ( $context->option( 'root' ) === null && method_exists( $context, 'root' ) )
            $set['root'] = $context->root();
        if ( !$this->runnable() )
            $set['mode'] = 'status';
        else if ( $context->option( 'mode' ) === null )
            $set['mode'] = $context->isApply() ? 'apply' : 'dry-run';
        foreach ( $set as $name => $value )
        {
            $saved[$name] = $context->option( $name );
            $context->setOption( $name, $value );
        }
        return function () use ( $context, $saved ) {
            foreach ( $saved as $name => $value )
                $context->setOption( $name, $value );
        };
    }
}
