<?php
/**
 * File containing the expMigrationUpgradeSource class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The bridge between exp:upgrade and the migration engine: every upgrade step of expUpgradeRegistry::fromSettings()
 * (upgrade.ini StepList[] of the kernel, ExtensionStepList[] of extensions) as a migration of the source "kernel"
 * (expMigrationUpgradeStep), in exp:upgrade's order (stage, version, after()).
 *
 *   $source = expMigrationUpgradeSource::fromSettings();
 *   $migrations = array_merge( $source->migrations(), $siteMigrations );   // the kernel's steps first
 *
 * One engine, one record, one lock, one caption list:
 *
 *   - exp:migration:status and the administration (Setup > Migrations, the dashboard block) list the kernel's steps
 *     beside the site's and the extensions' migrations, with exp:upgrade's states
 *   - exp:migration:migrate applies the pending steps of the stages database and scripts first, then the site's
 *     migrations, in one run under one caption; a site migration may require a step by its id
 *   - both commands record in exp_migration (source "kernel" for the steps, expUpgradeRecord), take the installation
 *     lock (expMigrationLock, expUpgradeLock) and their captions through expUpgradeCaption
 *   - exp:upgrade keeps its commands, stages and options; a run of it records exactly as before
 *
 * The steps of the stages code, libraries and finish are listed but never run by a migration run
 * (expMigrationUpgradeStep::runnable()): ./console exp:upgrade runs them.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationUpgradeSource
{
    /** The source of the steps. */
    const SOURCE = expMigrationUpgradeStep::SOURCE;

    /** @var expUpgradeRegistry */
    protected $registry;

    /**
     * @param expUpgradeRegistry $registry
     */
    public function __construct( expUpgradeRegistry $registry )
    {
        $this->registry = $registry;
    }

    /**
     * The steps of the installation's settings (upgrade.ini).
     *
     * @param eZINI|null $ini
     * @return expMigrationUpgradeSource
     */
    public static function fromSettings( $ini = null )
    {
        return new self( expUpgradeRegistry::fromSettings( $ini ) );
    }

    /** @return expUpgradeRegistry */
    public function registry()
    {
        return $this->registry;
    }

    /**
     * Every step as a migration, in exp:upgrade's order.
     *
     * @return expMigrationUpgradeStep[]
     */
    public function migrations()
    {
        $migrations = array();
        foreach ( $this->registry->steps() as $step )
            $migrations[] = new expMigrationUpgradeStep( $step );
        return $migrations;
    }

    /** @return string[] the ids of the steps, in order */
    public function ids()
    {
        $ids = array();
        foreach ( $this->registry->steps() as $step )
            $ids[] = (string)$step->id();
        return $ids;
    }

    /**
     * The problems of the registry (a class that does not exist, two steps with one id, a cycle in after()).
     *
     * @return string[]
     */
    public function errors()
    {
        return $this->registry->errors();
    }

    /**
     * The kernel's steps followed by other migrations (the site's, the extensions'): a migration with the id of a step
     * is left out with an error, as a migration defined twice is.
     *
     * @param expMigrationUpgradeStep[] $kernel
     * @param expMigration[] $migrations in their order
     * @param string[] $errors problems are added here
     * @return expMigration[]
     */
    public static function merge( array $kernel, array $migrations, array &$errors = array() )
    {
        $ids = array();
        foreach ( $kernel as $migration )
            $ids[(string)$migration->id()] = true;
        $all = array_values( $kernel );
        foreach ( $migrations as $migration )
        {
            if ( isset( $ids[(string)$migration->id()] ) )
            {
                $errors[] = 'the migration ' . $migration->id() . ' (' . $migration->source() . ') has the id of a kernel upgrade step and is left out';
                continue;
            }
            $all[] = $migration;
        }
        return $all;
    }
}
