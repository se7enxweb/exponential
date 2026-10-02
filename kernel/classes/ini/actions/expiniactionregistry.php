<?php
/**
 * File containing the expIniActionRegistry class.
 *
 * The actions of the exp:ini command and the scope providers of the settings editor, read from
 * ini.ini [IniCommandSettings]:
 *
 *   Actions[<name>]=<class implementing expIniAction>
 *   ActionAliases[<alias>]=<name>
 *   ScopeProviders[]=<class implementing expIniScopeProvider>
 *
 * The kernel's settings/ini.ini registers the built-in actions; an extension adds its own by an
 * extension/<ext>/settings/ini.ini.append.php. When ini.ini cannot be read the built-in actions
 * are used, so the command keeps working on a broken settings tree.
 * Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionRegistry
{
    /** The built-in actions, used when ini.ini has none. */
    const BUILT_IN = array(
        'get'     => 'expIniActionGet',
        'set'     => 'expIniActionSet',
        'add'     => 'expIniActionAdd',
        'rem'     => 'expIniActionRem',
        'clear'   => 'expIniActionClear',
        'toggle'  => 'expIniActionToggle',
        'copy'    => 'expIniActionCopy',
        'move'    => 'expIniActionMove',
        'move-all' => 'expIniActionMoveAll',
        'where'   => 'expIniActionWhere',
        'list'    => 'expIniActionList',
        'scopes'  => 'expIniActionScopes',
        'actions' => 'expIniActionActions',
    );

    /** The built-in aliases. */
    const BUILT_IN_ALIASES = array( 'remove' => 'rem' );

    /** @var array name => class */
    private $actions;

    /** @var array alias => name */
    private $aliases;

    /** @var array class names */
    private $scopeProviders;

    /**
     * @param array $actions name => class
     * @param array $aliases alias => name
     * @param array $scopeProviders class names
     */
    public function __construct( array $actions, array $aliases = array(), array $scopeProviders = array() )
    {
        $this->actions = array();
        foreach ( $actions as $name => $class )
        {
            $name = strtolower( trim( (string)$name ) );
            $class = ltrim( trim( (string)$class ), '\\' );
            if ( $name !== '' && $class !== '' )
                $this->actions[$name] = $class;
        }

        $this->aliases = array();
        foreach ( $aliases as $alias => $name )
        {
            $alias = strtolower( trim( (string)$alias ) );
            $name = strtolower( trim( (string)$name ) );
            if ( $alias !== '' && isset( $this->actions[$name] ) && !isset( $this->actions[$alias] ) )
                $this->aliases[$alias] = $name;
        }
        ksort( $this->aliases );

        $this->scopeProviders = array_values( array_unique( array_filter( array_map( function ( $c ) {
            return ltrim( trim( (string)$c ), '\\' );
        }, $scopeProviders ) ) ) );
    }

    /**
     * The registry of this installation: ini.ini [IniCommandSettings], merged over the built-ins.
     *
     * @param eZINI|null $ini ini.ini, eZINI::instance( 'ini.ini' ) when null
     * @return expIniActionRegistry
     */
    public static function fromSettings( $ini = null )
    {
        $actions = self::BUILT_IN;
        $aliases = self::BUILT_IN_ALIASES;
        $providers = array();

        if ( $ini === null && class_exists( 'eZINI' ) && eZINI::exists( 'ini.ini' ) )
            $ini = eZINI::instance( 'ini.ini' );

        if ( $ini !== null )
        {
            $actions = self::merge( $actions, $ini, 'Actions' );
            $aliases = self::merge( $aliases, $ini, 'ActionAliases' );

            if ( $ini->hasVariable( 'IniCommandSettings', 'ScopeProviders' ) )
            {
                $value = $ini->variable( 'IniCommandSettings', 'ScopeProviders' );
                if ( is_array( $value ) )
                    $providers = array_values( $value );
            }
        }

        return new self( $actions, $aliases, $providers );
    }

    /**
     * A hash setting of [IniCommandSettings] laid over what is there; an empty value switches an
     * entry off (Actions[copy]= removes the copy action).
     *
     * @return array
     */
    private static function merge( array $into, $ini, $variable )
    {
        if ( !$ini->hasVariable( 'IniCommandSettings', $variable ) )
            return $into;
        $value = $ini->variable( 'IniCommandSettings', $variable );
        if ( !is_array( $value ) )
            return $into;
        foreach ( $value as $name => $class )
        {
            if ( !is_string( $name ) || $name === '' )
                continue;
            if ( trim( (string)$class ) === '' )
                unset( $into[strtolower( $name )] );
            else
                $into[strtolower( $name )] = $class;
        }
        return $into;
    }

    /** @return array name => class, sorted by name */
    public function actions()
    {
        return $this->actions;
    }

    /** @return array alias => name */
    public function aliases()
    {
        return $this->aliases;
    }

    /** @return array the scope provider classes ini.ini registers */
    public function scopeProviders()
    {
        return $this->scopeProviders;
    }

    /** @return array the action names, sorted */
    public function names()
    {
        return array_keys( $this->actions );
    }

    /**
     * The action a name or an alias stands for.
     *
     * @param string $name
     * @return string|null the action's name, null when there is none
     */
    public function resolve( $name )
    {
        $name = strtolower( (string)$name );
        if ( isset( $this->actions[$name] ) )
            return $name;
        if ( isset( $this->aliases[$name] ) )
            return $this->aliases[$name];
        return null;
    }

    /** @return bool whether a name or alias is registered */
    public function has( $name )
    {
        return $this->resolve( $name ) !== null;
    }

    /** @return bool whether the action is one of the kernel's own */
    public function isBuiltIn( $name )
    {
        $name = $this->resolve( $name );
        return $name !== null && isset( self::BUILT_IN[$name] ) && self::BUILT_IN[$name] === $this->actions[$name];
    }

    /**
     * Makes the action.
     *
     * @param string $name a name or an alias
     * @return expIniAction
     * @throws RuntimeException when nothing is registered by that name, the class does not exist or
     *                          does not implement expIniAction
     */
    public function create( $name )
    {
        $resolved = $this->resolve( $name );
        if ( $resolved === null )
            throw new RuntimeException( "no action \"$name\"" );
        $class = $this->actions[$resolved];
        $problem = self::classProblem( $class, 'expIniAction' );
        if ( $problem === self::MISSING )
            throw new RuntimeException( "action \"$resolved\": class $class does not exist (regenerate the autoloads?)" );
        if ( $problem === self::WRONG_INTERFACE )
            throw new RuntimeException( "action \"$resolved\": class $class does not implement expIniAction" );
        return new $class();
    }

    /**
     * An action as the lists show it: its description and first usage line, or why it cannot be made.
     *
     * @param string $name
     * @return array name, class, builtin, description, usage, ok (description is the error when ok is false)
     */
    public function describe( $name )
    {
        $row = array( 'name' => $name, 'class' => isset( $this->actions[$name] ) ? $this->actions[$name] : null,
                      'builtin' => $this->isBuiltIn( $name ), 'description' => '', 'usage' => '', 'ok' => true );
        try
        {
            $action = $this->create( $name );
            $row['description'] = $action->description();
            $row['usage'] = strtok( (string)$action->usage(), "\n" );
        }
        catch ( Exception $e )
        {
            $row['ok'] = false;
            $row['description'] = $e->getMessage();
        }
        return $row;
    }

    /** classProblem(): the class does not exist. */
    const MISSING = 'missing';

    /** classProblem(): the class does not implement the interface. */
    const WRONG_INTERFACE = 'interface';

    /**
     * What keeps a registered class from working.
     *
     * @param string $class
     * @param string $interface expIniAction or expIniScopeProvider
     * @return string|null MISSING, WRONG_INTERFACE, or null when it can be used
     */
    public static function classProblem( $class, $interface )
    {
        if ( !class_exists( $class ) )
            return self::MISSING;
        if ( interface_exists( $interface ) && !is_subclass_of( $class, $interface ) )
            return self::WRONG_INTERFACE;
        return null;
    }

    /**
     * Registrations that cannot work: a class that is missing or does not implement the interface it must.
     *
     * @return array of array( kind: action|provider, name, class, why )
     */
    public function problems()
    {
        $problems = array();
        foreach ( $this->actions as $name => $class )
            self::addProblem( $problems, 'action', $name, $class, 'expIniAction' );
        foreach ( $this->scopeProviders as $class )
            self::addProblem( $problems, 'provider', $class, $class, 'expIniScopeProvider' );
        return $problems;
    }

    /** Adds a registration to problems() when its class cannot work. */
    private static function addProblem( array &$problems, $kind, $name, $class, $interface )
    {
        $problem = self::classProblem( $class, $interface );
        if ( $problem === null )
            return;
        $why = $problem === self::MISSING ? 'the class does not exist' : "the class does not implement $interface";
        $problems[] = array( 'kind' => $kind, 'name' => $name, 'class' => $class, 'why' => $why );
    }
}
