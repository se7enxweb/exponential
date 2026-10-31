<?php
/**
 * File containing the expMigrationPhp class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The base of a PHP migration: a file YYYYMMDDHHMMSS_name.php with its header (expMigrationDefinition) and one
 * namespaced class that extends this one. load() reads the file and makes the object; the class name is free, the
 * namespace is the author's (the site's or the extension's), so two migrations never clash on a name.
 *
 *   <?php
 *   // description: A table for news notes and the News folder
 *   // requires: 20261030090000_news_class
 *   namespace MySite\Migrations;
 *
 *   final class AddNewsNotes extends \expMigrationPhp
 *   {
 *       public function plan( $context )
 *       {
 *           return array( 'create the table news_note', 'create the folder News below the root' );
 *       }
 *
 *       public function up( $context )
 *       {
 *           ...
 *           return array( 'created news_note', 'created the folder News' );
 *       }
 *   }
 *
 * The shape is the one of the kernel's own migrations (expWebsiteSidebarMigration): plan() lists what up() would do
 * without changing anything (the dry run; build it from pure functions where possible, so it can be tested without a
 * database), up() does it and lists what it did, down() undoes it when the class has one (reversible() is true then),
 * detect() may ask the installation whether the change is there already. Everything goes through the context
 * ($context->db(), $context->reference() ...), never through a connection of its own, so the runner's transaction and
 * a test's database are the ones used.
 *
 * The state is record-based: done once a run recorded it, failed when the last run failed, pending otherwise. A class
 * whose detect() answers (not null) is detect-based instead.
 *
 * @since 6.0.15
 * @package kernel
 */
abstract class expMigrationPhp implements expMigration
{
    /** @var expMigrationDefinition|null */
    protected $definition;

    /**
     * Reads a PHP migration file and makes its migration.
     *
     * @param expMigrationDefinition $definition a definition of type php
     * @return expMigrationPhp
     * @throws RuntimeException when the file has no class that extends expMigrationPhp, or more than one
     */
    public static function load( expMigrationDefinition $definition )
    {
        if ( $definition->type() !== 'php' )
            throw new RuntimeException( $definition->id() . ' is not a PHP migration (.' . $definition->type() . ')' );
        $file = $definition->file();
        if ( $file === null || !is_file( $file ) )
            throw new RuntimeException( $definition->id() . ': the file is missing' );
        $classes = self::classesIn( (string)file_get_contents( $file ) );
        foreach ( $classes as $class )
            if ( class_exists( $class, false ) && ( new ReflectionClass( $class ) )->getFileName() !== realpath( $file ) )
                throw new RuntimeException( "$file: the class $class is defined in another file already ("
                                            . ( new ReflectionClass( $class ) )->getFileName() . ')' );
        self::requireFile( $file );
        $found = array();
        foreach ( $classes as $class )
            if ( class_exists( $class, false ) && is_subclass_of( $class, __CLASS__ ) && !( new ReflectionClass( $class ) )->isAbstract() )
                $found[] = $class;
        if ( count( $found ) !== 1 )
            throw new RuntimeException( "$file: " . ( $found ? 'more than one class extends expMigrationPhp (' . implode( ', ', $found ) . ')'
                                                             : 'no class extends expMigrationPhp' ) );
        $class = $found[0];
        $migration = new $class();
        $migration->setDefinition( $definition );
        return $migration;
    }

    /**
     * The classes a PHP file declares, with their namespace. Pure.
     *
     * @param string $code
     * @return string[] fully qualified, without the leading backslash
     */
    public static function classesIn( $code )
    {
        $tokens = token_get_all( (string)$code );
        $nameTokens = array( T_STRING, T_NS_SEPARATOR );
        foreach ( array( 'T_NAME_QUALIFIED', 'T_NAME_FULLY_QUALIFIED' ) as $name )
            if ( defined( $name ) )
                $nameTokens[] = constant( $name );
        $namespace = '';
        $classes = array();
        $count = count( $tokens );
        for ( $i = 0; $i < $count; $i++ )
        {
            if ( !is_array( $tokens[$i] ) )
                continue;
            if ( $tokens[$i][0] === T_NAMESPACE )
            {
                $name = '';
                for ( $j = $i + 1; $j < $count; $j++ )
                {
                    if ( is_array( $tokens[$j] ) && in_array( $tokens[$j][0], $nameTokens, true ) )
                        $name .= $tokens[$j][1];
                    else if ( is_array( $tokens[$j] ) && $tokens[$j][0] === T_WHITESPACE )
                        continue;
                    else
                        break;
                }
                $namespace = trim( $name, '\\' );
                $i = $j;
            }
            else if ( $tokens[$i][0] === T_CLASS )
            {
                // not ::class, not new class (an anonymous class)
                $prev = $i - 1;
                while ( $prev >= 0 && is_array( $tokens[$prev] ) && $tokens[$prev][0] === T_WHITESPACE )
                    $prev--;
                if ( $prev >= 0 && ( ( is_array( $tokens[$prev] ) && in_array( $tokens[$prev][0], array( T_DOUBLE_COLON, T_NEW ), true ) ) ) )
                    continue;
                for ( $j = $i + 1; $j < $count; $j++ )
                {
                    if ( is_array( $tokens[$j] ) && $tokens[$j][0] === T_WHITESPACE )
                        continue;
                    if ( is_array( $tokens[$j] ) && $tokens[$j][0] === T_STRING )
                        $classes[] = ( $namespace !== '' ? $namespace . '\\' : '' ) . $tokens[$j][1];
                    break;
                }
            }
        }
        return $classes;
    }

    /**
     * Loads a migration file with nothing of the caller in its scope.
     *
     * @param string $file
     */
    protected static function requireFile( $file )
    {
        require_once $file;
    }

    /**
     * @param expMigrationDefinition $definition
     */
    public function setDefinition( expMigrationDefinition $definition )
    {
        $this->definition = $definition;
    }

    /** @return expMigrationDefinition|null */
    public function definition()
    {
        return $this->definition;
    }

    /** @return string */
    public function id()
    {
        return $this->definition ? $this->definition->id() : '';
    }

    /** @return string */
    public function source()
    {
        return $this->definition ? $this->definition->source() : 'site';
    }

    /** @return string */
    public function description()
    {
        return $this->definition ? $this->definition->description() : '';
    }

    /** @return string[] */
    public function environments()
    {
        return $this->definition ? $this->definition->environments() : array();
    }

    /** @return string[] */
    public function requires()
    {
        return $this->definition ? $this->definition->requires() : array();
    }

    /** @return bool */
    public function maintenance()
    {
        return $this->definition ? $this->definition->maintenance() : false;
    }

    /** @return string[] */
    public function engines()
    {
        return $this->definition ? $this->definition->engines() : array( '*' );
    }

    /** @return string|null the checksum of the file (expMigrationDefinition::checksumFor()) */
    public function checksum()
    {
        return $this->definition ? $this->definition->checksumFor() : null;
    }

    /** @return string|null the file */
    public function file()
    {
        return $this->definition ? $this->definition->file() : null;
    }

    /**
     * Whether up() runs in a transaction where the engine has one. A migration that commits itself (a long batch)
     * returns false.
     *
     * @return bool
     */
    public function transactional()
    {
        return true;
    }

    /**
     * Whether the change is in place without asking the record: expMigration::DONE, PENDING or NOT_NEEDED, or null
     * (the default) to go by the record.
     *
     * @param expMigrationContext $context
     * @return string|null
     */
    public function detect( $context )
    {
        return null;
    }

    /**
     * @param expMigrationContext $context
     * @return string
     */
    public function state( $context )
    {
        $detected = $this->detect( $context );
        if ( $detected !== null )
            return (string)$detected;
        $record = $context->record( $this->source(), $this->id() );
        if ( !$record )
            return expMigration::PENDING;
        if ( $record['state'] === expMigration::DONE )
            return expMigration::DONE;
        if ( $record['state'] === expMigration::FAILED )
            return expMigration::FAILED;
        return expMigration::PENDING;
    }

    /**
     * What up() would do. The default names the migration; a class lists its changes.
     *
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( $context )
    {
        return array( $this->description() !== '' ? $this->description() : 'run ' . $this->id() );
    }

    /**
     * @param expMigrationContext $context
     * @return string[] what was done
     */
    abstract public function up( $context );

    /**
     * Whether the class has a down() of its own.
     *
     * @return bool
     */
    public function reversible()
    {
        return ( new ReflectionMethod( $this, 'down' ) )->getDeclaringClass()->getName() !== __CLASS__;
    }

    /**
     * @param expMigrationContext $context
     * @return string[]
     * @throws RuntimeException always: the migration has no down()
     */
    public function down( $context )
    {
        throw new RuntimeException( $this->id() . ' cannot be undone by itself (no down()); restore the caption taken before it' );
    }
}
