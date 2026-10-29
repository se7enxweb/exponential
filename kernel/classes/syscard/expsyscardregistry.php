<?php
/**
 * File containing the expSysCardRegistry class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The providers of the system card and how they run (settings/syscard.ini [Providers] and [Runtime]):
 *
 *   [Providers]
 *   ProviderClasses[]=expSysCardHostProvider      the order of the facts on the card
 *   Enabled[velocity]=disabled                    switch one off by its id
 *   Priority[help]=10                             the higher, the earlier it runs
 *   Budget[database]=2                            the seconds it may take
 *
 * An extension adds a provider in its own settings/syscard.ini.append.php (ProviderClasses[]=myExtSysCardProvider).
 *
 * The providers run by priority, each within its budget and all of them within [Runtime] TotalBudget. With
 * [Runtime] Isolation=enabled (and pcntl and posix), each provider runs in a child process that is killed when its
 * time is up, so a hanging provider (a database that does not answer, a stalled disk) never hangs the card: its
 * facts show "n/a (timeout)". Without isolation the budget is kept by the providers themselves (the context's
 * deadline for programs and directory walks), and a provider that went over it is reported as slow.
 *
 * Every fact is checked before it is shown (safe()): a fact named like a password, key, token or secret is dropped,
 * and a value that carries a password ("mysql://user:pw@host") is masked. The card never shows a secret.
 */
class expSysCardRegistry
{
    /** @var string[] class names in card order */
    protected $classes = array();
    /** @var array id => bool */
    protected $enabled = array();
    /** @var array id => int */
    protected $priorities = array();
    /** @var array id => float */
    protected $budgets = array();
    /** @var float */
    protected $totalBudget = 5.0;
    /** @var bool */
    protected $isolation = true;
    /** @var array class => expSysCardProvider (instances given directly, tests) */
    protected $instances = array();
    /** @var string[] the classes that could not be used, and why */
    protected $problems = array();

    /**
     * @param array $settings ProviderClasses (list), Enabled, Priority, Budget (id => value), TotalBudget, Isolation
     */
    public function __construct( array $settings = array() )
    {
        $classes = isset( $settings['ProviderClasses'] ) ? (array)$settings['ProviderClasses'] : array();
        foreach ( $classes as $class )
        {
            $class = trim( (string)$class );
            if ( $class !== '' && !in_array( $class, $this->classes, true ) )
                $this->classes[] = $class;
        }
        foreach ( isset( $settings['Enabled'] ) ? (array)$settings['Enabled'] : array() as $id => $value )
            $this->enabled[strtolower( (string)$id )] = expSysCardContext::isOn( $value );
        foreach ( isset( $settings['Priority'] ) ? (array)$settings['Priority'] : array() as $id => $value )
            if ( is_numeric( $value ) )
                $this->priorities[strtolower( (string)$id )] = (int)$value;
        foreach ( isset( $settings['Budget'] ) ? (array)$settings['Budget'] : array() as $id => $value )
            if ( is_numeric( $value ) && (float)$value > 0 )
                $this->budgets[strtolower( (string)$id )] = (float)$value;
        if ( isset( $settings['TotalBudget'] ) && is_numeric( $settings['TotalBudget'] ) && (float)$settings['TotalBudget'] > 0 )
            $this->totalBudget = (float)$settings['TotalBudget'];
        if ( isset( $settings['Isolation'] ) )
            $this->isolation = expSysCardContext::isOn( $settings['Isolation'] );
    }

    /**
     * The registry of syscard.ini (with the extensions' syscard.ini.append.php).
     *
     * @return expSysCardRegistry
     */
    public static function fromIni()
    {
        $settings = array();
        if ( class_exists( 'eZINI' ) && eZINI::exists( 'syscard.ini' ) )
        {
            $ini = eZINI::instance( 'syscard.ini' );
            foreach ( array( 'ProviderClasses', 'Enabled', 'Priority', 'Budget' ) as $name )
                if ( $ini->hasVariable( 'Providers', $name ) )
                    $settings[$name] = $ini->variable( 'Providers', $name );
            foreach ( array( 'TotalBudget', 'Isolation' ) as $name )
                if ( $ini->hasVariable( 'Runtime', $name ) )
                    $settings[$name] = $ini->variable( 'Runtime', $name );
        }
        return new self( $settings );
    }

    /**
     * Adds a provider object (tests, or code that builds a card of its own) at the end of the order.
     *
     * @param expSysCardProvider $provider
     * @return expSysCardRegistry
     */
    public function add( expSysCardProvider $provider )
    {
        $class = get_class( $provider ) . '#' . spl_object_id( $provider );
        $this->instances[$class] = $provider;
        $this->classes[] = $class;
        return $this;
    }

    /**
     * @return string[] the class names, in card order
     */
    public function classes()
    {
        return array_values( array_map( function ( $class ) { $at = strpos( $class, '#' ); return $at === false ? $class : substr( $class, 0, $at ); }, $this->classes ) );
    }

    /**
     * @return string[] the classes left out: missing, or not a provider
     */
    public function problems()
    {
        return $this->problems;
    }

    /**
     * @param bool $isolation
     * @return expSysCardRegistry
     */
    public function setIsolation( $isolation )
    {
        $this->isolation = (bool)$isolation;
        return $this;
    }

    /**
     * @return bool whether providers run in child processes now (setting on, pcntl and posix there)
     */
    public function isolates()
    {
        return $this->isolation && function_exists( 'pcntl_fork' ) && function_exists( 'pcntl_waitpid' ) && function_exists( 'posix_kill' )
            && function_exists( 'stream_socket_pair' ) && PHP_SAPI === 'cli';
    }

    /**
     * @return float
     */
    public function totalBudget()
    {
        return $this->totalBudget;
    }

    /**
     * Whether a provider id is switched on (on unless Enabled[<id>] says otherwise).
     *
     * @param string $id
     * @return bool
     */
    public function isEnabled( $id )
    {
        $id = strtolower( (string)$id );
        return !isset( $this->enabled[$id] ) || $this->enabled[$id];
    }

    /**
     * The providers of a card, in card order: enabled, and made for the card (a "card" provider is on all three).
     *
     * @param string $mode card, info or about
     * @return expSysCardProvider[]
     */
    public function providers( $mode = expSysCardContext::MODE_CARD )
    {
        $this->problems = array();
        $list = array();
        foreach ( $this->classes as $class )
        {
            $provider = $this->instance( $class );
            if ( $provider === null )
                continue;
            if ( !$this->isEnabled( $provider->id() ) )
                continue;
            $modes = $provider->modes();
            if ( !in_array( $mode, $modes, true ) && !in_array( expSysCardContext::MODE_CARD, $modes, true ) )
                continue;
            $list[] = $provider;
        }
        return $list;
    }

    /**
     * @param string $class
     * @return expSysCardProvider|null
     */
    protected function instance( $class )
    {
        if ( isset( $this->instances[$class] ) )
            return $this->instances[$class];
        if ( !class_exists( $class ) )
        {
            $this->problems[] = $class . ': no such class';
            return null;
        }
        if ( !is_subclass_of( $class, 'expSysCardProvider' ) )
        {
            $this->problems[] = $class . ': not an expSysCardProvider';
            return null;
        }
        try
        {
            return $this->instances[$class] = new $class();
        }
        catch ( Throwable $e )
        {
            $this->problems[] = $class . ': ' . $e->getMessage();
            return null;
        }
    }

    /**
     * @param expSysCardProvider $provider
     * @return int
     */
    public function priorityOf( expSysCardProvider $provider )
    {
        $id = strtolower( $provider->id() );
        return isset( $this->priorities[$id] ) ? $this->priorities[$id] : (int)$provider->priority();
    }

    /**
     * @param expSysCardProvider $provider
     * @return float
     */
    public function budgetOf( expSysCardProvider $provider )
    {
        $id = strtolower( $provider->id() );
        $budget = isset( $this->budgets[$id] ) ? $this->budgets[$id] : (float)$provider->budget();
        return $budget > 0 ? $budget : 1.0;
    }

    /**
     * Runs the providers of the card and gathers their facts.
     *
     * @param expSysCardContext $context
     * @param string[]|null $only run only these provider ids (--watch); null for all
     * @return array( 'facts' => expSysCardFact[] in card order, 'runs' => list of id, class, status, seconds, priority, budget )
     */
    public function collect( expSysCardContext $context, $only = null )
    {
        $providers = $this->providers( $context->mode );
        $order = array();
        foreach ( $providers as $position => $provider )
            $order[] = array( $position, $this->priorityOf( $provider ) );
        usort( $order, function ( $a, $b ) { return $b[1] <=> $a[1] ?: $a[0] <=> $b[0]; } );

        $start = microtime( true );
        $totalEnd = $start + $this->totalBudget;
        $byPosition = array();
        $runs = array();
        foreach ( $order as $entry )
        {
            $provider = $providers[$entry[0]];
            $id = $provider->id();
            if ( is_array( $only ) && !in_array( $id, $only, true ) )
                continue;
            $budget = min( $this->budgetOf( $provider ), max( 0.0, $totalEnd - microtime( true ) ) );
            $run = array( 'id' => $id, 'class' => get_class( $provider ), 'status' => 'ok', 'seconds' => 0.0,
                          'priority' => $entry[1], 'budget' => round( $this->budgetOf( $provider ), 3 ) );
            if ( $budget <= 0.0 )
            {
                $run['status'] = 'skipped';
                $byPosition[$entry[0]] = array( expSysCardFact::unavailable( $id, $this->sectionOf( $provider ), 'time' ) );
                $runs[$entry[0]] = $run;
                continue;
            }
            $began = microtime( true );
            $providerContext = clone $context;
            $providerContext->deadline = $began + $budget;
            $result = $this->isolates() && !$context->isFake() ? $this->runIsolated( $provider, $providerContext, $budget )
                                                                : $this->runInline( $provider, $providerContext );
            $run['seconds'] = round( microtime( true ) - $began, 3 );
            $run['status'] = $result['status'];
            if ( $result['status'] === 'ok' && $run['seconds'] > $budget + 0.05 )
                $run['status'] = 'slow';
            $facts = $result['facts'];
            if ( $result['status'] === 'timeout' || $result['status'] === 'error' )
                $facts = array( expSysCardFact::unavailable( $id, $this->sectionOf( $provider ), $result['status'] ) );
            $clean = array();
            foreach ( $facts as $fact )
            {
                if ( !$fact instanceof expSysCardFact )
                    continue;
                if ( $fact->provider === '' )
                    $fact->provider = $id;
                $fact = self::safe( $fact, $context->root );
                if ( $fact !== null )
                    $clean[] = $fact;
            }
            $byPosition[$entry[0]] = $clean;
            $runs[$entry[0]] = $run;
        }
        ksort( $byPosition );
        ksort( $runs );
        $facts = array();
        foreach ( $byPosition as $list )
            foreach ( $list as $fact )
                $facts[] = $fact;
        return array( 'facts' => $facts, 'runs' => array_values( $runs ) );
    }

    /**
     * The section a provider's "n/a" goes to: the section of the base provider, else system.
     *
     * @param expSysCardProvider $provider
     * @return string
     */
    protected function sectionOf( expSysCardProvider $provider )
    {
        return $provider instanceof expSysCardBaseProvider ? $provider->section() : 'system';
    }

    /**
     * @param expSysCardProvider $provider
     * @param expSysCardContext $context
     * @return array( 'status' => ok|error, 'facts' => list )
     */
    protected function runInline( expSysCardProvider $provider, expSysCardContext $context )
    {
        try
        {
            $facts = $provider->collect( $context );
            return array( 'status' => 'ok', 'facts' => is_array( $facts ) ? $facts : array() );
        }
        catch ( Throwable $e )
        {
            return array( 'status' => 'error', 'facts' => array() );
        }
    }

    /**
     * Runs a provider in a child process that is killed when its time is up.
     *
     * @param expSysCardProvider $provider
     * @param expSysCardContext $context
     * @param float $budget
     * @return array( 'status' => ok|timeout|error, 'facts' => list )
     */
    protected function runIsolated( expSysCardProvider $provider, expSysCardContext $context, $budget )
    {
        $pair = @stream_socket_pair( STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP );
        if ( !is_array( $pair ) )
            return $this->runInline( $provider, $context );
        $pid = @pcntl_fork();
        if ( $pid === -1 )
        {
            fclose( $pair[0] );
            fclose( $pair[1] );
            return $this->runInline( $provider, $context );
        }
        if ( $pid === 0 )
        {
            // The child: collect, send the facts as JSON, and end at once. SIGKILL skips every shutdown function and
            // destructor, so the child never closes a connection or removes a file that belongs to the parent.
            fclose( $pair[0] );
            $result = $this->runInline( $provider, $context );
            $rows = array();
            foreach ( $result['facts'] as $fact )
                if ( $fact instanceof expSysCardFact )
                    $rows[] = $fact->toArray();
            $payload = json_encode( array( 'status' => $result['status'], 'facts' => $rows ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
            $payload = $payload === false ? '{"status":"error","facts":[]}' : $payload;
            for ( $written = 0, $length = strlen( $payload ); $written < $length; )
            {
                $n = @fwrite( $pair[1], substr( $payload, $written ) );
                if ( $n === false || $n === 0 )
                    break;
                $written += $n;
            }
            @fclose( $pair[1] );
            posix_kill( getmypid(), 9 );
            exit( 0 );
        }
        fclose( $pair[1] );
        stream_set_blocking( $pair[0], false );
        $data = '';
        $end = microtime( true ) + $budget;
        $status = 'ok';
        while ( true )
        {
            $left = $end - microtime( true );
            if ( $left <= 0 )
            {
                $status = 'timeout';
                break;
            }
            $read = array( $pair[0] );
            $write = null;
            $except = null;
            $ready = @stream_select( $read, $write, $except, 0, (int)min( 100000, $left * 1000000 ) );
            if ( $ready === false )
            {
                $status = 'error';
                break;
            }
            if ( $ready === 0 )
                continue;
            $chunk = fread( $pair[0], 65536 );
            if ( $chunk === false || ( $chunk === '' && feof( $pair[0] ) ) )
                break;
            $data .= $chunk;
        }
        fclose( $pair[0] );
        if ( $status === 'timeout' )
            @posix_kill( $pid, 9 );
        $state = 0;
        pcntl_waitpid( $pid, $state );
        if ( $status !== 'ok' )
            return array( 'status' => $status, 'facts' => array() );
        $decoded = json_decode( $data, true );
        if ( !is_array( $decoded ) || !isset( $decoded['facts'] ) || !is_array( $decoded['facts'] ) )
            return array( 'status' => 'error', 'facts' => array() );
        $facts = array();
        foreach ( $decoded['facts'] as $row )
            if ( is_array( $row ) )
                $facts[] = expSysCardFact::fromArray( $row );
        return array( 'status' => isset( $decoded['status'] ) && $decoded['status'] === 'error' ? 'error' : 'ok', 'facts' => $facts );
    }

    /** Names of facts that are never shown, whatever their value */
    const SECRET_NAME = '/(pass(word|wd|phrase)?|secret|token|api[_-]?key|private[_-]?key|credential|salt|cookie|session[_-]?id|auth(orization)?[_-]?(key|header)|dsn)$/i';

    /**
     * A fact as it may be shown, or null: a fact named like a secret is dropped; a password inside a value (an
     * address with user:password@, a "password=..." pair) is masked; the installation root is left as it is.
     *
     * @param expSysCardFact $fact
     * @param string $root
     * @return expSysCardFact|null
     */
    public static function safe( expSysCardFact $fact, $root = '' )
    {
        $name = preg_replace( '/^.*\./', '', $fact->key );
        if ( preg_match( self::SECRET_NAME, (string)$name ) || preg_match( self::SECRET_NAME, str_replace( ' ', '', $fact->label ) ) )
            return null;
        $fact->value = self::mask( $fact->value );
        $fact->hint = self::mask( $fact->hint );
        return $fact;
    }

    /**
     * A text with the secrets in it replaced by ***.
     *
     * @param string $text
     * @return string
     */
    public static function mask( $text )
    {
        $text = (string)$text;
        if ( $text === '' )
            return $text;
        // scheme://user:password@host -> scheme://user:***@host
        $text = (string)preg_replace( '#([a-z][a-z0-9+.-]*://[^:/@\s]*):[^@\s/]+@#i', '$1:***@', $text );
        // password=..., token: ..., api_key=...
        $text = (string)preg_replace( '/\b((?:pass(?:word|wd)?|secret|token|api[_-]?key|key|pwd)\s*[=:]\s*)("[^"]*"|\'[^\']*\'|[^\s,;&]+)/i', '$1***', $text );
        // Authorization: Bearer xyz
        $text = (string)preg_replace( '/\b(Bearer|Basic)\s+[A-Za-z0-9._~+\/=-]{8,}/', '$1 ***', $text );
        if ( class_exists( 'expSystemReportMask' ) && method_exists( 'expSystemReportMask', 'text' ) )
            $text = (string)expSystemReportMask::text( $text );
        return $text;
    }
}
