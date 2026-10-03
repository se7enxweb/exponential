<?php
/**
 * exppolicy: policies of roles and their limitations, and the modules and functions a policy can name.
 * Changes apply to the published role directly; a full edit session uses the draft services of exprole.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expPolicyServices extends expUsersBase
{
    public static $services = array();

    protected static function policy( $id )
    {
        $policy = eZPolicy::fetch( (int)$id );
        if ( !$policy instanceof eZPolicy )
            throw new expServiceException( "Policy $id does not exist", 404 );
        return $policy;
    }

    public static function fetch( array $a = array() )
    {
        self::guard( 'fetch' );
        return self::ok( self::exportPolicy( self::policy( self::arg( $a, 0, 'int' ) ) ) );
    }

    public static function listOfRole( array $a = array() )
    {
        self::guard( 'listOfRole' );
        return expRoleServices::policies( $a );
    }

    public static function limitations( array $a = array() )
    {
        self::guard( 'limitations' );
        $p = self::exportPolicy( self::policy( self::arg( $a, 0, 'int' ) ) );
        return self::ok( $p['limitations'] );
    }

    public static function summary( array $a = array() )
    {
        self::guard( 'summary' );
        $p = self::exportPolicy( self::policy( self::arg( $a, 0, 'int' ) ) );
        $parts = array();
        foreach ( (array)$p['limitations'] as $k => $v )
            $parts[] = $k . ' in (' . implode( ', ', $v ) . ')';
        return self::ok( array( 'id' => $p['id'], 'text' => $p['module'] . '/' . $p['function'] . ( $parts ? ' where ' . implode( ' and ', $parts ) : '' ) ) );
    }

    public static function add( array $a = array() )
    {
        self::guard( 'add' );
        $role = self::role( self::post( 'role', 'int' ) );
        if ( (int)$role->attribute( 'version' ) !== 0 )
            throw new expServiceException( 'Policies of a draft are added with exprole::draftAddPolicy', 409 );
        $module = self::post( 'module', 'string' );
        $function = self::post( 'function', 'string' );
        $limits = self::checkedLimits( $module, $function, self::post( 'limitations', 'json', array() ) );
        $policy = $role->appendPolicy( $module, $function, $limits );
        eZUser::cleanupCache();
        return self::ok( self::exportPolicy( $policy ) );
    }

    public static function remove( array $a = array() )
    {
        self::guard( 'remove' );
        $policy = self::policy( self::post( 'id', 'int' ) );
        $id = (int)$policy->attribute( 'id' );
        $policy->removeThis();
        eZUser::cleanupCache();
        return self::ok( array( 'id' => $id, 'removed' => true ) );
    }

    public static function setLimitations( array $a = array() )
    {
        self::guard( 'setLimitations' );
        $policy = self::policy( self::post( 'id', 'int' ) );
        $limits = self::checkedLimits( $policy->attribute( 'module_name' ), $policy->attribute( 'function_name' ), self::post( 'limitations', 'json' ) );
        foreach ( $policy->limitationList( false ) as $l )
            $l->removeThis();
        $fresh = self::policy( (int)$policy->attribute( 'id' ) );
        foreach ( $limits as $identifier => $values )
            $fresh->appendLimitation( $identifier, $values );
        eZUser::cleanupCache();
        return self::ok( self::exportPolicy( self::policy( (int)$policy->attribute( 'id' ) ) ) );
    }

    public static function addLimitation( array $a = array() )
    {
        self::guard( 'addLimitation' );
        $policy = self::policy( self::post( 'id', 'int' ) );
        $identifier = self::post( 'identifier', 'string' );
        $limits = self::checkedLimits( $policy->attribute( 'module_name' ), $policy->attribute( 'function_name' ), array( $identifier => self::post( 'values', 'list' ) ) );
        foreach ( $policy->limitationList( false ) as $l )
            if ( $l->attribute( 'identifier' ) === $identifier )
                throw new expServiceException( "The policy already has the limitation '$identifier'; use setLimitations", 409 );
        $policy->appendLimitation( $identifier, $limits[$identifier] );
        eZUser::cleanupCache();
        return self::ok( self::exportPolicy( self::policy( (int)$policy->attribute( 'id' ) ) ) );
    }

    public static function removeLimitation( array $a = array() )
    {
        self::guard( 'removeLimitation' );
        $policy = self::policy( self::post( 'id', 'int' ) );
        $identifier = self::post( 'identifier', 'string' );
        $found = false;
        foreach ( $policy->limitationList( false ) as $l )
            if ( $l->attribute( 'identifier' ) === $identifier )
            {
                $l->removeThis();
                $found = true;
            }
        if ( !$found )
            throw new expServiceException( "The policy has no limitation '$identifier'", 404 );
        eZUser::cleanupCache();
        return self::ok( self::exportPolicy( self::policy( (int)$policy->attribute( 'id' ) ) ) );
    }

    public static function copy( array $a = array() )
    {
        self::guard( 'copy' );
        $policy = self::policy( self::post( 'id', 'int' ) );
        $role = self::role( self::post( 'role', 'int' ) );
        $new = $policy->copy( (int)$role->attribute( 'id' ) );
        eZUser::cleanupCache();
        return self::ok( self::exportPolicy( $new ) );
    }

    public static function modules( array $a = array() )
    {
        self::guard( 'modules' );
        $list = (array)eZINI::instance( 'module.ini' )->variable( 'ModuleSettings', 'ModuleList' );
        sort( $list );
        return self::ok( $list );
    }

    public static function functions( array $a = array() )
    {
        self::guard( 'functions' );
        $module = self::arg( $a, 0, 'string' );
        return self::ok( array_keys( self::moduleFunctions( $module ) ) );
    }

    public static function availableLimitations( array $a = array() )
    {
        self::guard( 'availableLimitations' );
        $module = self::arg( $a, 0, 'string' );
        $function = self::arg( $a, 1, 'string' );
        $functions = self::moduleFunctions( $module );
        if ( !isset( $functions[$function] ) )
            throw new expServiceException( "Module '$module' has no function '$function'", 404 );
        return self::ok( array_keys( (array)$functions[$function] ) );
    }

    public static function limitationValues( array $a = array() )
    {
        self::guard( 'limitationValues' );
        $module = self::arg( $a, 0, 'string' );
        $function = self::arg( $a, 1, 'string' );
        $identifier = self::arg( $a, 2, 'string' );
        $functions = self::moduleFunctions( $module );
        if ( !isset( $functions[$function][$identifier] ) )
            throw new expServiceException( "No limitation '$identifier' on $module/$function", 404 );
        $def = $functions[$function][$identifier];
        $values = array();
        if ( isset( $def['values'] ) && is_array( $def['values'] ) )
            foreach ( $def['values'] as $v )
                $values[] = array( 'value' => (string)$v['value'], 'name' => isset( $v['name'] ) ? (string)$v['name'] : (string)$v['value'] );
        else if ( isset( $def['class'] ) && isset( $def['function'] ) && is_callable( array( $def['class'], $def['function'] ) ) )
            foreach ( (array)call_user_func( array( $def['class'], $def['function'] ) ) as $v )
                if ( is_array( $v ) && isset( $v['value'] ) )
                    $values[] = array( 'value' => (string)$v['value'], 'name' => isset( $v['name'] ) ? (string)$v['name'] : (string)$v['value'] );
        return self::pageOf( $values, $a, 3, 4 );
    }

    public static function ofModule( array $a = array() )
    {
        self::guard( 'ofModule' );
        $module = self::arg( $a, 0, 'string' );
        $rows = eZDB::instance()->arrayQuery( "SELECT p.id FROM ezpolicy p INNER JOIN ezrole r ON r.id = p.role_id WHERE r.version = 0 AND p.module_name = '" . eZDB::instance()->escapeString( $module ) . "' ORDER BY p.id" );
        $out = array();
        foreach ( $rows as $r )
            $out[] = self::exportPolicy( self::policy( (int)$r['id'] ) );
        return self::pageOf( $out, $a, 1, 2 );
    }

    public static function findByLimitation( array $a = array() )
    {
        self::guard( 'findByLimitation' );
        $found = eZPolicyLimitation::findByType( self::arg( $a, 0, 'string' ), self::arg( $a, 1, 'string' ), true, false );
        $out = array();
        foreach ( (array)$found as $l )
        {
            $p = eZPolicy::fetch( (int)$l->attribute( 'policy_id' ) );
            if ( $p )
                $out[] = self::exportPolicy( $p );
        }
        return self::pageOf( $out, $a, 2, 3 );
    }
}

expPolicyServices::$services = expUsersBase::specs( array(
    'fetch' => array( 'A policy with its limitations', 'role/view', 'r', 'id:int', 'policy' ),
    'listOfRole' => array( 'The policies of a role, paged', 'role/view', 'r', 'id:int,limit:int,offset:int', 'paged policies' ),
    'limitations' => array( 'The limitations of a policy, identifier => values', 'role/view', 'r', 'id:int', 'limitations' ),
    'summary' => array( 'A policy as one line of text', 'role/view', 'r', 'id:int', 'text' ),
    'add' => array( 'Add a policy to a role, validated against the module (POST role, module, function, limitations)', 'role/edit', 'w', 'role:int,module:string,function:string,limitations:json', 'policy' ),
    'remove' => array( 'Remove a policy (POST id)', 'role/edit', 'w', 'id:int', 'removed' ),
    'setLimitations' => array( 'Replace all limitations of a policy (POST id, limitations)', 'role/edit', 'w', 'id:int,limitations:json', 'policy' ),
    'addLimitation' => array( 'Add one limitation (POST id, identifier, values)', 'role/edit', 'w', 'id:int,identifier:string,values:list', 'policy' ),
    'removeLimitation' => array( 'Remove one limitation (POST id, identifier)', 'role/edit', 'w', 'id:int,identifier:string', 'policy' ),
    'copy' => array( 'Copy a policy into another role (POST id, role)', 'role/edit', 'w', 'id:int,role:int', 'policy' ),
    'modules' => array( 'The modules a policy can name', 'role/view', 'r', '', 'module names' ),
    'functions' => array( 'The functions of a module', 'role/view', 'r', 'module:string', 'function names' ),
    'availableLimitations' => array( 'The limitation identifiers of a module function', 'role/view', 'r', 'module:string,function:string', 'identifiers' ),
    'limitationValues' => array( 'The values a limitation can take, paged', 'role/view', 'r', 'module:string,function:string,identifier:string,limit:int,offset:int', 'paged value, name' ),
    'ofModule' => array( 'The policies naming a module, over all roles', 'role/view', 'r', 'module:string,limit:int,offset:int', 'paged policies' ),
    'findByLimitation' => array( 'The policies with a limitation identifier and value', 'role/view', 'r', 'identifier:string,value:string,limit:int,offset:int', 'paged policies' ),
) );
