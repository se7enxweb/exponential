<?php
/**
 * File containing the expMigrationStepRegistry class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step types YAML migrations can use: migration.ini [StepSettings] StepTypeList[<name>]=<class>, the kernel's and
 * those an extension adds in its own migration.ini.append.php. A class that does not exist, does not implement
 * expMigrationStepType, answers another name than it is registered with or has a broken schema is not registered;
 * the problem is listed in errors() (exp:migration:check reports it).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepRegistry
{
    /** A step type name. */
    const NAME_PATTERN = '/^[a-z][a-z0-9_]*$/';

    /** @var expMigrationStepType[] name => type */
    protected $types = array();

    /** @var string[] */
    protected $errors = array();

    /** @var expMigrationStepRegistry|null the registry of the settings, made once per process */
    private static $instance = null;

    /**
     * @param array $types name => expMigrationStepType or class name
     */
    public function __construct( array $types = array() )
    {
        foreach ( $types as $name => $type )
            $this->register( (string)$name, $type );
    }

    /**
     * The registry of migration.ini [StepSettings] StepTypeList, made once per process (forget() makes it again).
     *
     * @param eZINI|null $ini
     * @return expMigrationStepRegistry
     */
    public static function fromSettings( $ini = null )
    {
        if ( $ini === null && self::$instance !== null )
            return self::$instance;
        $shared = $ini === null;
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        $list = $ini->hasVariable( 'StepSettings', 'StepTypeList' ) ? (array)$ini->variable( 'StepSettings', 'StepTypeList' ) : array();
        $registry = new self();
        foreach ( $list as $name => $class )
        {
            if ( is_int( $name ) )
            {
                $registry->errors[] = "[StepSettings] StepTypeList[]=$class: name the step type, StepTypeList[<name>]=<class>";
                continue;
            }
            if ( trim( (string)$class ) === '' )
                continue;
            $registry->register( (string)$name, trim( (string)$class ) );
        }
        if ( $shared )
            self::$instance = $registry;
        return $registry;
    }

    /** Forgets the registry of the settings. */
    public static function forget()
    {
        self::$instance = null;
    }

    /**
     * Registers a step type; a problem goes to errors().
     *
     * @param string $name
     * @param expMigrationStepType|string $type an object or a class name
     * @return bool
     */
    public function register( $name, $type )
    {
        $label = is_string( $type ) ? $type : get_class( $type );
        if ( !preg_match( self::NAME_PATTERN, $name ) )
        {
            $this->errors[] = "the step type name $name: lower case letters, digits and _, starting with a letter";
            return false;
        }
        if ( is_string( $type ) )
        {
            if ( !class_exists( $type ) )
            {
                $this->errors[] = "the step type $name: the class $type does not exist";
                return false;
            }
            if ( !is_subclass_of( $type, 'expMigrationStepType' ) )
            {
                $this->errors[] = "the step type $name: $type does not implement expMigrationStepType";
                return false;
            }
            try
            {
                $type = new $type();
            }
            catch ( Throwable $e )
            {
                $this->errors[] = "the step type $name: $label cannot be made: " . $e->getMessage();
                return false;
            }
        }
        if ( !$type instanceof expMigrationStepType )
        {
            $this->errors[] = "the step type $name: $label does not implement expMigrationStepType";
            return false;
        }
        if ( $type->name() !== $name )
        {
            $this->errors[] = "the step type $name: $label calls itself " . $type->name();
            return false;
        }
        $problems = expMigrationStepSchema::problems( $type->schema() );
        if ( $problems )
        {
            $this->errors[] = "the step type $name ($label): " . implode( '; ', $problems );
            return false;
        }
        $this->types[$name] = $type;
        return true;
    }

    /**
     * @param string $name
     * @return expMigrationStepType|null
     */
    public function get( $name )
    {
        return is_string( $name ) && isset( $this->types[$name] ) ? $this->types[$name] : null;
    }

    /** @return string[] the names, sorted */
    public function names()
    {
        $names = array_keys( $this->types );
        sort( $names );
        return $names;
    }

    /** @return expMigrationStepType[] name => type */
    public function types()
    {
        return $this->types;
    }

    /** @return string[] */
    public function errors()
    {
        return $this->errors;
    }
}
