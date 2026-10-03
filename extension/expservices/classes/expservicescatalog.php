<?php
/**
 * ezjscore/call/expservices::catalog | schema::<domain> | version | domains | service::<domain>::<method>
 * The discovery of the API: built from the $services of every class registered as [ezjscServer_exp<domain>] in
 * ezjscore.ini whose Class extends expServiceBase.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expServicesCatalog extends expServiceBase
{
    const VERSION = '0.1.0';

    public static $services = array(
        'catalog' => array( 'summary' => 'Every service of every domain: domain, method, summary, access, write, args, returns',
            'access' => 'public', 'write' => false, 'args' => array( 'domain' => 'string' ),
            'returns' => 'list of service descriptors (optionally one domain)' ),
        'schema' => array( 'summary' => 'The services of one domain',
            'access' => 'public', 'write' => false, 'args' => array( 'domain' => 'string' ),
            'returns' => 'domain, class, list of service descriptors' ),
        'service' => array( 'summary' => 'One service descriptor',
            'access' => 'public', 'write' => false, 'args' => array( 'domain' => 'string', 'method' => 'string' ),
            'returns' => 'service descriptor with its call URL' ),
        'domains' => array( 'summary' => 'The domains with their class and service count',
            'access' => 'public', 'write' => false, 'args' => array(),
            'returns' => 'list of domain, class, services' ),
        'version' => array( 'summary' => 'The version of expservices, the envelope version and the Exponential version',
            'access' => 'public', 'write' => false, 'args' => array(),
            'returns' => 'expservices, envelope, exponential' ),
    );

    /** @return array domain => class, from the ezjscore.ini [ezjscServer_exp<domain>] blocks that name a service class */
    public static function domainClasses()
    {
        $ini = eZINI::instance( 'ezjscore.ini' );
        $domains = array();
        foreach ( $ini->groups() as $group => $values )
        {
            if ( strpos( $group, 'ezjscServer_exp' ) !== 0 )
                continue;
            $domain = substr( $group, strlen( 'ezjscServer_exp' ) );
            $class = isset( $values['Class'] ) ? $values['Class'] : 'exp' . $domain;
            if ( $domain === '' || !class_exists( $class ) || !is_subclass_of( $class, 'expServiceBase' ) )
                continue;
            $domains[$domain] = $class;
        }
        ksort( $domains );
        return $domains;
    }

    /** The descriptors of one domain class. */
    public static function describe( $domain, $class )
    {
        $list = array();
        foreach ( $class::$services as $method => $decl )
        {
            $access = isset( $decl['access'] ) ? $decl['access'] : null;
            $list[] = array(
                'domain' => $domain,
                'class' => $class,
                'method' => $method,
                'summary' => isset( $decl['summary'] ) ? $decl['summary'] : '',
                'access' => $access,
                'write' => !empty( $decl['write'] ),
                'args' => isset( $decl['args'] ) ? $decl['args'] : array(),
                'returns' => isset( $decl['returns'] ) ? $decl['returns'] : '',
                'call' => 'ezjscore/call/exp' . $domain . '::' . $method . ( !empty( $decl['args'] ) ? '::<' . implode( '>::<', array_keys( $decl['args'] ) ) . '>' : '' ),
            );
        }
        return $list;
    }

    /** Every descriptor of every domain, or of one. */
    public static function all( $domain = null )
    {
        $all = array();
        foreach ( self::domainClasses() as $d => $class )
            if ( $domain === null || $domain === $d )
                $all = array_merge( $all, self::describe( $d, $class ) );
        return $all;
    }

    public static function catalog( $args )
    {
        self::guard( __FUNCTION__ );
        $domain = self::arg( $args, 0, 'string', null );
        if ( $domain !== null && !isset( self::domainClasses()[$domain] ) )
            throw new expServiceException( "No such domain '$domain'", 404 );
        $all = self::all( $domain );
        return self::ok( $all, array( 'total' => count( $all ), 'domains' => count( $domain === null ? self::domainClasses() : array( 1 ) ) ) );
    }

    public static function schema( $args )
    {
        self::guard( __FUNCTION__ );
        $domain = self::arg( $args, 0, 'string' );
        $classes = self::domainClasses();
        if ( !isset( $classes[$domain] ) )
            throw new expServiceException( "No such domain '$domain'", 404 );
        return self::ok( array( 'domain' => $domain, 'class' => $classes[$domain], 'services' => self::describe( $domain, $classes[$domain] ) ) );
    }

    public static function service( $args )
    {
        self::guard( __FUNCTION__ );
        $domain = self::arg( $args, 0, 'string' );
        $method = self::arg( $args, 1, 'string' );
        $classes = self::domainClasses();
        if ( !isset( $classes[$domain] ) || !isset( $classes[$domain]::$services[$method] ) )
            throw new expServiceException( "No such service exp$domain::$method", 404 );
        foreach ( self::describe( $domain, $classes[$domain] ) as $d )
            if ( $d['method'] === $method )
                return self::ok( $d );
        throw new expServiceException( 'Not found', 404 );
    }

    public static function domains( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( self::domainClasses() as $domain => $class )
            $list[] = array( 'domain' => $domain, 'class' => $class, 'services' => count( $class::$services ) );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function version( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'expservices' => self::VERSION, 'envelope' => 1, 'exponential' => eZPublishSDK::version() ) );
    }
}
