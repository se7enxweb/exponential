<?php
/**
 * File containing the expKernelServiceContainer class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The service container of the kernel: shared objects by name, made on first use, in their scope.
 *
 * It holds definitions (from the compiled container of the siteaccess, or read from settings/kernelservices.ini
 * when nothing is compiled) and makes each service when it is first asked for: worker-scoped ones once per worker
 * and siteaccess (expKernelServiceScope), request-scoped ones once per request, prototypes every time. No
 * reflection and no autowiring at runtime: a definition says the class, the factory and the arguments.
 *
 * A definition (one [Service_<id>] block of kernelservices.ini):
 *
 *   class      the class (or interface) the service is
 *   factory    'Class::method' to call, or '' for "new class( arguments )"
 *   arguments  each '@<id>' (another service), '%ini:<file>/<Section>/<Variable>%' (a setting) or a literal
 *   scope      worker, request or prototype (default request)
 *   lazy       made only when first asked for (every service is, in this container)
 *   tags       names a consumer can collect services by (tagged())
 *
 * The ids "ini.<name>" need no definition: they are eZINI::instance( '<name>.ini' ), which hands out the worker's
 * copy when the container has attached its worker scope to eZINI.
 *
 * Code reaches the container through the expKernelServices facade.
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelServiceContainer
{
    /** @var array id => definition */
    protected $definitions;

    /** @var array tag => list of ids */
    protected $tags;

    /** @var string the siteaccess the definitions are for ('' when unknown) */
    protected $siteAccess;

    /** @var bool whether the definitions come from a compiled file */
    protected $compiled;

    /** @var array ids being made right now (cycle guard) */
    protected $making = array();

    /**
     * @param array $definitions id => definition, as expKernelServiceCompiler::definitionsFromINI() gives them
     * @param array $tags tag => list of ids
     * @param string $siteAccess
     * @param bool $compiled
     */
    public function __construct( array $definitions, array $tags = array(), $siteAccess = '', $compiled = false )
    {
        $this->definitions = $definitions;
        $this->tags = $tags;
        $this->siteAccess = (string)$siteAccess;
        $this->compiled = (bool)$compiled;
    }

    /**
     * Whether a service of this id exists (a definition, or an ini.<name> id).
     *
     * @param string $id
     * @return bool
     */
    public function has( $id )
    {
        return isset( $this->definitions[$id] ) || self::iniFileName( $id ) !== null;
    }

    /**
     * The service of this id, made if needed.
     *
     * @param string $id
     * @return object
     * @throws InvalidArgumentException for an unknown id
     * @throws RuntimeException for a definition that cannot be made (cycle, missing class or factory)
     */
    public function get( $id )
    {
        // ini.<name> is always eZINI::instance(): eZINI keeps its own instances (and the worker's copies), a
        // definition of it only documents it
        $iniFile = self::iniFileName( $id );
        if ( $iniFile !== null )
            return eZINI::instance( $iniFile );

        if ( !isset( $this->definitions[$id] ) )
            throw new InvalidArgumentException( "No kernel service '$id'" . ( $this->siteAccess !== '' ? " in siteaccess '{$this->siteAccess}'" : '' ) );

        $definition = $this->definitions[$id];
        $scope = isset( $definition['scope'] ) ? $definition['scope'] : expKernelServiceScope::REQUEST;

        if ( $scope === expKernelServiceScope::WORKER )
        {
            $worker = expKernelServiceScope::worker();
            if ( isset( $worker->services[$this->siteAccess][$id] ) )
                return $worker->services[$this->siteAccess][$id];
            $instance = $this->make( $id, $definition );
            $worker->services[$this->siteAccess][$id] = $instance;
            return $instance;
        }

        if ( $scope === expKernelServiceScope::REQUEST )
        {
            $instance = expKernelServiceScope::requestInstance( $id );
            if ( $instance !== null )
                return $instance;
            $instance = $this->make( $id, $definition );
            expKernelServiceScope::setRequestInstance( $id, $instance );
            return $instance;
        }

        return $this->make( $id, $definition );
    }

    /**
     * The services that carry a tag, made if needed, id => object, in the order they were defined.
     *
     * @param string $tag
     * @return array
     */
    public function tagged( $tag )
    {
        $services = array();
        foreach ( $this->taggedIds( $tag ) as $id )
            $services[$id] = $this->get( $id );
        return $services;
    }

    /**
     * The ids of the services that carry a tag, without making them.
     *
     * @param string $tag
     * @return array
     */
    public function taggedIds( $tag )
    {
        return isset( $this->tags[$tag] ) ? $this->tags[$tag] : array();
    }

    /**
     * The definition of a service, or null.
     *
     * @param string $id
     * @return array|null
     */
    public function definition( $id )
    {
        return isset( $this->definitions[$id] ) ? $this->definitions[$id] : null;
    }

    /**
     * Every definition, id => definition.
     *
     * @return array
     */
    public function definitions()
    {
        return $this->definitions;
    }

    /**
     * Every tag, tag => ids.
     *
     * @return array
     */
    public function tags()
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function siteAccess()
    {
        return $this->siteAccess;
    }

    /**
     * Whether the definitions come from the compiled container.
     *
     * @return bool
     */
    public function isCompiled()
    {
        return $this->compiled;
    }

    /**
     * The INI file an "ini.<name>" id stands for ('ini.site' is site.ini), or null for any other id.
     *
     * @param string $id
     * @return string|null
     */
    public static function iniFileName( $id )
    {
        if ( !is_string( $id ) || strncmp( $id, 'ini.', 4 ) !== 0 )
            return null;
        $name = substr( $id, 4 );
        if ( !preg_match( '/^[A-Za-z0-9_][A-Za-z0-9_.-]*$/', $name ) )
            return null;
        return substr( $name, -4 ) === '.ini' ? $name : $name . '.ini';
    }

    /**
     * Makes one service from its definition.
     *
     * @param string $id
     * @param array $definition
     * @return object
     */
    protected function make( $id, array $definition )
    {
        if ( isset( $this->making[$id] ) )
            throw new RuntimeException( "Kernel service '$id' depends on itself: " . implode( ' -> ', array_keys( $this->making ) ) . " -> $id" );
        $this->making[$id] = true;
        try
        {
            $arguments = array();
            foreach ( isset( $definition['arguments'] ) ? $definition['arguments'] : array() as $argument )
                $arguments[] = $this->resolveArgument( $argument );

            $factory = isset( $definition['factory'] ) ? (string)$definition['factory'] : '';
            if ( $factory !== '' )
            {
                $callable = strpos( $factory, '::' ) !== false ? explode( '::', $factory, 2 ) : $factory;
                if ( !is_callable( $callable ) )
                    throw new RuntimeException( "Kernel service '$id': factory $factory cannot be called" );
                $instance = call_user_func_array( $callable, $arguments );
            }
            else
            {
                $class = isset( $definition['class'] ) ? (string)$definition['class'] : '';
                if ( $class === '' || !class_exists( $class ) )
                    throw new RuntimeException( "Kernel service '$id': class '$class' not found" );
                $instance = new $class( ...$arguments );
            }

            if ( !is_object( $instance ) )
                throw new RuntimeException( "Kernel service '$id': the factory returned no object" );
            return $instance;
        }
        finally
        {
            unset( $this->making[$id] );
        }
    }

    /**
     * One argument of a definition: '@id' is that service, '%ini:file/Section/Variable%' that setting (null when it
     * is not set), '@@...' and '%%...' the literal text with one character less, anything else itself.
     *
     * @param mixed $argument
     * @return mixed
     */
    protected function resolveArgument( $argument )
    {
        if ( !is_string( $argument ) || $argument === '' )
            return $argument;
        if ( strncmp( $argument, '@@', 2 ) === 0 || strncmp( $argument, '%%', 2 ) === 0 )
            return substr( $argument, 1 );
        if ( $argument[0] === '@' )
            return $this->get( substr( $argument, 1 ) );
        $setting = self::parseIniArgument( $argument );
        if ( $setting !== null )
        {
            $ini = eZINI::instance( $setting[0] );
            return $ini->hasVariable( $setting[1], $setting[2] ) ? $ini->variable( $setting[1], $setting[2] ) : null;
        }
        return $argument;
    }

    /**
     * array( file, section, variable ) for '%ini:file/Section/Variable%', else null.
     *
     * @param string $argument
     * @return array|null
     */
    public static function parseIniArgument( $argument )
    {
        if ( is_string( $argument ) && preg_match( '#^%ini:([^/%]+)/([^/%]+)/([^/%]+)%$#', $argument, $m ) )
            return array( $m[1], $m[2], $m[3] );
        return null;
    }
}
