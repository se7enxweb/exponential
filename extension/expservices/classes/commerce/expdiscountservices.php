<?php
/**
 * ezjscore/call/expdiscount::<method>: discount groups (rules), their rules (percent with class, section or
 * product limits) and the users and user groups that get them. The kernel writes the commerce.discount.change
 * audit event of every change.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expDiscountServices extends expServiceBase
{
    public static $services = array(
        'groups' => array( 'summary' => 'The discount groups with their rule and member counts', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of groups' ),
        'group' => array( 'summary' => 'One discount group with its rules and members', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'group, rules, members' ),
        'createGroup' => array( 'summary' => 'Creates a discount group', 'access' => array( 'shop', 'administrate' ), 'write' => true, 'args' => array( 'name' => 'string POST' ), 'returns' => 'the group' ),
        'renameGroup' => array( 'summary' => 'Renames a discount group', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'id' => 'int POST', 'name' => 'string POST' ), 'returns' => 'the group' ),
        'removeGroup' => array( 'summary' => 'Removes a discount group with its rules and memberships', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'id' => 'int POST' ), 'returns' => 'removed id' ),
        'rules' => array( 'summary' => 'The rules of a discount group', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array( 'group_id' => 'int' ), 'returns' => 'list of rules' ),
        'rule' => array( 'summary' => 'One rule with its limitations', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'rule with classes, sections, products' ),
        'createRule' => array( 'summary' => 'Creates a rule in a group: percent and optional class/section/product limits', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'group_id' => 'int POST', 'name' => 'string POST', 'percent' => 'string POST', 'classes' => 'list POST', 'sections' => 'list POST', 'products' => 'list POST' ), 'returns' => 'the rule' ),
        'updateRule' => array( 'summary' => 'Changes name, percent or limits of a rule', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'id' => 'int POST', 'name' => 'string POST', 'percent' => 'string POST', 'classes' => 'list POST', 'sections' => 'list POST', 'products' => 'list POST' ), 'returns' => 'the rule' ),
        'removeRule' => array( 'summary' => 'Removes a rule', 'access' => array( 'shop', 'administrate' ), 'write' => true, 'args' => array( 'id' => 'int POST' ), 'returns' => 'removed id' ),
        'members' => array( 'summary' => 'The users and user groups of a discount group', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array( 'group_id' => 'int' ), 'returns' => 'list of members' ),
        'addMember' => array( 'summary' => 'Adds a user or user group (object id) to a discount group', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'group_id' => 'int POST', 'object_id' => 'int POST' ), 'returns' => 'members' ),
        'removeMember' => array( 'summary' => 'Removes a user or user group from a discount group', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'group_id' => 'int POST', 'object_id' => 'int POST' ), 'returns' => 'members' ),
        'groupsOfUser' => array( 'summary' => 'The discount groups a user or user group belongs to', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'list of groups' ),
        'forUser' => array( 'summary' => 'The best discount percent of a user for a product (class, section, object)', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'user_id' => 'int', 'node_id' => 'int' ), 'returns' => 'percent' ),
        'mine' => array( 'summary' => 'The discount percent the logged-in user gets for a product', 'access' => 'user', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'percent' ),
    );

    protected static function fetchGroup( $id )
    {
        $g = eZDiscountRule::fetch( (int)$id );
        if ( !$g instanceof eZDiscountRule )
            throw new expServiceException( "No discount group $id", 404 );
        return $g;
    }

    protected static function fetchRule( $id )
    {
        $r = eZDiscountSubRule::fetch( (int)$id );
        if ( !$r instanceof eZDiscountSubRule )
            throw new expServiceException( "No discount rule $id", 404 );
        return $r;
    }

    protected static function name( $v )
    {
        $v = trim( $v );
        if ( $v === '' || mb_strlen( $v ) > 255 )
            throw new expServiceException( 'The name is required, up to 255 characters', 422 );
        return $v;
    }

    protected static function percent( $v )
    {
        $v = str_replace( ',', '.', trim( $v ) );
        if ( !is_numeric( $v ) || (float)$v < 0 || (float)$v > 100 )
            throw new expServiceException( 'The percent is a number between 0 and 100', 422 );
        return (float)$v;
    }

    protected static function encodeRule( eZDiscountSubRule $r )
    {
        $out = expCommerceExport::subRule( $r );
        $id = (int)$r->attribute( 'id' );
        $vals = function ( $kind ) use ( $id ) {
            $l = array();
            foreach ( eZDiscountSubRuleValue::fetchBySubRuleID( $id, $kind ) as $v )
                $l[] = (int)$v->attribute( 'value' );
            return $l;
        };
        $out['classes'] = $vals( 0 );
        $out['sections'] = $vals( 1 );
        $out['products'] = $vals( 2 );
        return $out;
    }

    /** Stores the limits given as POST lists; null leaves a kind alone. */
    protected static function applyLimits( eZDiscountSubRule $r )
    {
        $kinds = array( 'classes' => 0, 'sections' => 1, 'products' => 2 );
        $given = array();
        foreach ( $kinds as $field => $kind )
        {
            $v = self::post( $field, 'list', null );
            if ( $v === null )
                continue;
            foreach ( $v as $x )
                if ( !is_numeric( $x ) )
                    throw new expServiceException( "$field is a list of ids", 422 );
            $given[$kind] = array_map( 'intval', $v );
        }
        if ( !$given )
            return;
        $id = (int)$r->attribute( 'id' );
        $db = eZDB::instance();
        $db->begin();
        foreach ( $given as $kind => $ids )
        {
            $db->query( "DELETE FROM ezdiscountsubrule_value WHERE discountsubrule_id=$id AND issection=" . (int)$kind );
            foreach ( $ids as $v )
                eZDiscountSubRuleValue::create( $id, $v, $kind )->store();
        }
        $any = $db->arrayQuery( "SELECT COUNT(*) AS c FROM ezdiscountsubrule_value WHERE discountsubrule_id=$id" );
        $r->setAttribute( 'limitation', (int)$any[0]['c'] > 0 ? '' : '*' );
        $r->store();
        $db->commit();
    }

    public static function groups( array $args )
    {
        self::guard( 'groups' );
        $out = array();
        foreach ( (array)eZDiscountRule::fetchList() as $g )
        {
            $id = (int)$g->attribute( 'id' );
            $o = expCommerceExport::discountRule( $g );
            $o['rules'] = count( (array)eZDiscountSubRule::fetchByRuleID( $id ) );
            $o['members'] = count( (array)eZUserDiscountRule::fetchByRuleID( $id ) );
            $out[] = $o;
        }
        return self::pageOf( $out, $args, 0, 1 );
    }

    public static function group( array $args )
    {
        self::guard( 'group' );
        $g = self::fetchGroup( self::arg( $args, 0, 'int' ) );
        $o = expCommerceExport::discountRule( $g );
        $o['rules'] = array();
        foreach ( (array)eZDiscountSubRule::fetchByRuleID( $g->attribute( 'id' ) ) as $r )
            $o['rules'][] = self::encodeRule( $r );
        $o['members'] = self::members( array( $g->attribute( 'id' ) ) )['data'];
        return self::ok( $o );
    }

    public static function createGroup( array $args )
    {
        self::guard( 'createGroup' );
        $g = eZDiscountRule::create();
        $g->setAttribute( 'name', self::name( self::post( 'name', 'string' ) ) );
        $g->store();
        return self::ok( expCommerceExport::discountRule( eZDiscountRule::fetch( $g->attribute( 'id' ) ) ) );
    }

    public static function renameGroup( array $args )
    {
        self::guard( 'renameGroup' );
        $g = self::fetchGroup( self::post( 'id', 'int' ) );
        $g->setAttribute( 'name', self::name( self::post( 'name', 'string' ) ) );
        $g->store();
        return self::ok( expCommerceExport::discountRule( eZDiscountRule::fetch( $g->attribute( 'id' ) ) ) );
    }

    public static function removeGroup( array $args )
    {
        self::guard( 'removeGroup' );
        $g = self::fetchGroup( self::post( 'id', 'int' ) );
        $id = (int)$g->attribute( 'id' );
        $db = eZDB::instance();
        $db->begin();
        foreach ( (array)eZDiscountSubRule::fetchByRuleID( $id ) as $r )
        {
            eZDiscountSubRuleValue::removeBySubRuleID( $r->attribute( 'id' ) );
            $r->remove( $r->attribute( 'id' ) );
        }
        $db->query( "DELETE FROM ezuser_discountrule WHERE discountrule_id=$id" );
        eZDiscountRule::removeByID( $id );
        $db->commit();
        return self::ok( array( 'removed' => $id ) );
    }

    public static function rules( array $args )
    {
        self::guard( 'rules' );
        $g = self::fetchGroup( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( (array)eZDiscountSubRule::fetchByRuleID( $g->attribute( 'id' ) ) as $r )
            $out[] = self::encodeRule( $r );
        return self::ok( $out );
    }

    public static function rule( array $args )
    {
        self::guard( 'rule' );
        return self::ok( self::encodeRule( self::fetchRule( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function createRule( array $args )
    {
        self::guard( 'createRule' );
        $g = self::fetchGroup( self::post( 'group_id', 'int' ) );
        $r = eZDiscountSubRule::create( $g->attribute( 'id' ) );
        $r->setAttribute( 'name', self::name( self::post( 'name', 'string' ) ) );
        $r->setAttribute( 'discount_percent', self::percent( self::post( 'percent', 'string' ) ) );
        $r->setAttribute( 'limitation', '*' );
        $r->store();
        self::applyLimits( $r );
        return self::ok( self::encodeRule( eZDiscountSubRule::fetch( $r->attribute( 'id' ) ) ) );
    }

    public static function updateRule( array $args )
    {
        self::guard( 'updateRule' );
        $r = self::fetchRule( self::post( 'id', 'int' ) );
        if ( ( $v = self::post( 'name', 'string', null ) ) !== null )
            $r->setAttribute( 'name', self::name( $v ) );
        if ( ( $v = self::post( 'percent', 'string', null ) ) !== null )
            $r->setAttribute( 'discount_percent', self::percent( $v ) );
        $r->store();
        self::applyLimits( $r );
        return self::ok( self::encodeRule( eZDiscountSubRule::fetch( $r->attribute( 'id' ) ) ) );
    }

    public static function removeRule( array $args )
    {
        self::guard( 'removeRule' );
        $r = self::fetchRule( self::post( 'id', 'int' ) );
        $id = (int)$r->attribute( 'id' );
        $db = eZDB::instance();
        $db->begin();
        eZDiscountSubRuleValue::removeBySubRuleID( $id );
        $r->remove( $r->attribute( 'id' ) );
        $db->commit();
        return self::ok( array( 'removed' => $id ) );
    }

    public static function members( array $args )
    {
        self::guard( 'members' );
        $g = self::fetchGroup( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( (array)eZUserDiscountRule::fetchByRuleID( $g->attribute( 'id' ) ) as $m )
        {
            $o = eZContentObject::fetch( (int)$m->attribute( 'contentobject_id' ) );
            $out[] = array( 'object_id' => (int)$m->attribute( 'contentobject_id' ), 'name' => $o ? $o->attribute( 'name' ) : null, 'class' => $o ? $o->attribute( 'class_identifier' ) : null );
        }
        return self::ok( $out );
    }

    protected static function memberObject( $id )
    {
        $o = eZContentObject::fetch( (int)$id );
        if ( !$o instanceof eZContentObject )
            throw new expServiceException( "No object $id", 404 );
        if ( !in_array( $o->attribute( 'class_identifier' ), array( 'user', 'user_group' ), true ) )
            throw new expServiceException( 'Only users and user groups can be members', 422 );
        return $o;
    }

    public static function addMember( array $args )
    {
        self::guard( 'addMember' );
        $g = self::fetchGroup( self::post( 'group_id', 'int' ) );
        $o = self::memberObject( self::post( 'object_id', 'int' ) );
        $gid = (int)$g->attribute( 'id' );
        foreach ( (array)eZUserDiscountRule::fetchByRuleID( $gid ) as $m )
            if ( (int)$m->attribute( 'contentobject_id' ) === (int)$o->attribute( 'id' ) )
                throw new expServiceException( 'Already a member', 409 );
        eZUserDiscountRule::create( $gid, $o->attribute( 'id' ) )->store();
        return self::members( array( $gid ) );
    }

    public static function removeMember( array $args )
    {
        self::guard( 'removeMember' );
        $g = self::fetchGroup( self::post( 'group_id', 'int' ) );
        $oid = self::post( 'object_id', 'int' );
        $gid = (int)$g->attribute( 'id' );
        $found = false;
        foreach ( (array)eZUserDiscountRule::fetchByRuleID( $gid ) as $m )
            if ( (int)$m->attribute( 'contentobject_id' ) === $oid )
            {
                $m->removeByID( $m->attribute( 'id' ) );
                $found = true;
            }
        if ( !$found )
            throw new expServiceException( 'Not a member', 404 );
        return self::members( array( $gid ) );
    }

    public static function groupsOfUser( array $args )
    {
        self::guard( 'groupsOfUser' );
        $oid = self::arg( $args, 0, 'int' );
        $out = array();
        foreach ( (array)eZUserDiscountRule::fetchByUserID( $oid ) as $m )
        {
            $g = eZDiscountRule::fetch( (int)$m->attribute( 'discountrule_id' ) );
            if ( $g )
                $out[] = expCommerceExport::discountRule( $g );
        }
        return self::ok( $out );
    }

    protected static function percentFor( $user, eZContentObjectTreeNode $node )
    {
        $o = $node->attribute( 'object' );
        return eZDiscount::discountPercent( $user, array( 'contentclass_id' => $o->attribute( 'contentclass_id' ), 'contentobject_id' => $o->attribute( 'id' ),
            'section_id' => $o->attribute( 'section_id' ) ) );
    }

    public static function forUser( array $args )
    {
        self::guard( 'forUser' );
        $user = eZUser::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$user instanceof eZUser )
            throw new expServiceException( 'No such user', 404 );
        $node = self::node( self::arg( $args, 1, 'int' ), 'read' );
        return self::ok( array( 'percent' => expCommerceExport::num( self::percentFor( $user, $node ) ) ) );
    }

    public static function mine( array $args )
    {
        self::guard( 'mine' );
        $node = self::node( self::arg( $args, 0, 'int' ), 'read' );
        return self::ok( array( 'percent' => expCommerceExport::num( self::percentFor( eZUser::currentUser(), $node ) ) ) );
    }
}
