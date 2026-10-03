<?php
/**
 * ezjscore/call/explayout::<service> - the layouts of the explayouts extension: layouts, zones, blocks, rules,
 * the layout resolved for a node, and drafts (create, publish, discard). Policies explayouts/read and
 * explayouts/edit. Answers 404 "not available" when the extension is inactive.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expLayoutServices extends expServiceBase
{
    public static $services = array(
        'available' => array( 'summary' => 'Whether the explayouts extension is active, and the number of published layouts', 'access' => 'public',
            'write' => false, 'args' => array(), 'returns' => 'available, layouts' ),
        'layouts' => array( 'summary' => 'The published layouts', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int', 'status' => 'int' ), 'returns' => 'paged list of layouts (status 2 published, 1 draft)' ),
        'shared' => array( 'summary' => 'The shared layouts (header, footer, ...)', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of layouts' ),
        'drafts' => array( 'summary' => 'The draft layouts', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of layouts' ),
        'view' => array( 'summary' => 'One layout with its zones and block counts', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'layout, zones' ),
        'byidentifier' => array( 'summary' => 'A layout by identifier (published unless status is 1)', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'identifier' => 'string', 'status' => 'int' ), 'returns' => 'layout' ),
        'types' => array( 'summary' => 'The layout types (explayouts.ini LayoutType_*) with their zones', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of identifier, name, zones' ),
        'blocktypes' => array( 'summary' => 'The block definitions (explayouts.ini BlockDefinition_*)', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'category' => 'string' ), 'returns' => 'list of identifier, name, handler, view types, category' ),
        'zones' => array( 'summary' => 'The zones of a layout, with their link to a shared layout', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'list of zones' ),
        'zone' => array( 'summary' => 'One zone of a layout', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int', 'zone' => 'string' ), 'returns' => 'zone' ),
        'blocks' => array( 'summary' => 'The blocks of a zone (a linked zone answers the blocks of the shared layout)', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int', 'zone' => 'string' ), 'returns' => 'list of blocks' ),
        'block' => array( 'summary' => 'One block with its parameters', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'block, parameters' ),
        'blockchildren' => array( 'summary' => 'The child blocks of a container block', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'list of blocks' ),
        'blockparameters' => array( 'summary' => 'The parameters of a block as name => value', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'map' ),
        'linkedzones' => array( 'summary' => 'Every zone that inherits its blocks from a shared layout', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of layout, zone, linked layout, linked zone' ),
        'rules' => array( 'summary' => 'The layout rules in priority order', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of rules with targets and conditions' ),
        'rule' => array( 'summary' => 'One rule with its targets and conditions', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'rule' ),
        'rulesfor' => array( 'summary' => 'The rules that show a layout', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'list of rules' ),
        'resolve' => array( 'summary' => 'The layout a content node gets (first matching enabled rule, else the default); changes nothing', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int' ), 'returns' => 'node, layout, rule' ),
        'resolvepath' => array( 'summary' => 'The layout a URL path gets', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array( 'path' => 'string' ), 'returns' => 'path, layout, rule' ),
        'stats' => array( 'summary' => 'Counts of layouts, zones, blocks, rules and block types in use', 'access' => array( 'explayouts', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'counts, per block definition' ),
        'createdraft' => array( 'summary' => 'Creates (or returns) the draft of a published layout', 'access' => array( 'explayouts', 'edit' ), 'write' => true,
            'args' => array( 'id' => 'int' ), 'returns' => 'the draft layout' ),
        'publish' => array( 'summary' => 'Publishes the draft of a layout', 'access' => array( 'explayouts', 'edit' ), 'write' => true,
            'args' => array( 'id' => 'int' ), 'returns' => 'the published layout' ),
        'discard' => array( 'summary' => 'Discards the draft of a layout', 'access' => array( 'explayouts', 'edit' ), 'write' => true,
            'args' => array( 'id' => 'int' ), 'returns' => 'discarded' ),
        'enablerule' => array( 'summary' => 'Enables a rule', 'access' => array( 'explayouts', 'edit' ), 'write' => true,
            'args' => array( 'id' => 'int' ), 'returns' => 'the rule' ),
        'disablerule' => array( 'summary' => 'Disables a rule', 'access' => array( 'explayouts', 'edit' ), 'write' => true,
            'args' => array( 'id' => 'int' ), 'returns' => 'the rule' ),
        'clearcache' => array( 'summary' => 'Clears the layout resolver cache', 'access' => array( 'explayouts', 'edit' ), 'write' => true,
            'args' => array(), 'returns' => 'cleared' ),
    );

    protected static function need()
    {
        if ( !class_exists( 'expLayoutsLayout' ) )
            throw new expServiceException( 'The explayouts extension is not available', 404 );
    }

    protected static function layoutOf( $id, $status = null )
    {
        self::need();
        $layout = expLayoutsLayout::fetch( (int)$id );
        if ( !$layout instanceof expLayoutsLayout )
            throw new expServiceException( "Layout $id does not exist", 404 );
        return $layout;
    }

    protected static function exportLayout( $layout )
    {
        return array( 'id' => (int)$layout->attribute( 'id' ), 'identifier' => $layout->attribute( 'identifier' ),
                      'name' => $layout->attribute( 'name' ), 'layout_type' => $layout->attribute( 'layout_type' ),
                      'shared' => (bool)$layout->attribute( 'shared' ), 'status' => (int)$layout->attribute( 'status' ),
                      'status_name' => (int)$layout->attribute( 'status' ) === 2 ? 'published' : 'draft',
                      'created' => self::iso( $layout->attribute( 'created' ) ), 'modified' => self::iso( $layout->attribute( 'modified' ) ) );
    }

    protected static function exportZone( $zone, $withBlocks = true )
    {
        $out = array( 'id' => (int)$zone->attribute( 'id' ), 'layout_id' => (int)$zone->attribute( 'layout_id' ),
                      'identifier' => $zone->attribute( 'identifier' ), 'position' => (int)$zone->attribute( 'position' ),
                      'linked' => (bool)$zone->isLinked(), 'linked_layout_id' => (int)$zone->attribute( 'linked_layout_id' ),
                      'linked_zone_identifier' => (string)$zone->attribute( 'linked_zone_identifier' ) );
        if ( $withBlocks )
        {
            $source = expLayoutsZone::resolveSource( $zone );
            $out['block_count'] = $zone->isLinked() ? 0 : count( expLayoutsBlock::fetchByZone( $source->attribute( 'id' ), (int)$source->attribute( 'status' ) ) );
            $out['linked_block_count'] = $zone->isLinked() ? count( expLayoutsBlock::fetchByZone( $source->attribute( 'id' ), (int)$source->attribute( 'status' ) ) ) : 0;
        }
        return $out;
    }

    protected static function exportBlock( $block )
    {
        return array( 'id' => (int)$block->attribute( 'id' ), 'zone_id' => (int)$block->attribute( 'zone_id' ), 'layout_id' => (int)$block->attribute( 'layout_id' ),
                      'position' => (int)$block->attribute( 'position' ), 'definition' => $block->attribute( 'definition_identifier' ),
                      'view_type' => $block->attribute( 'view_type' ), 'item_view_type' => $block->attribute( 'item_view_type' ),
                      'name' => $block->attribute( 'name' ), 'parent_id' => (int)$block->attribute( 'parent_id' ),
                      'placeholder' => $block->attribute( 'placeholder' ), 'status' => (int)$block->attribute( 'status' ) );
    }

    protected static function exportRule( $rule )
    {
        $targets = array();
        foreach ( $rule->targets() as $t )
            $targets[] = array( 'id' => (int)$t->attribute( 'id' ), 'type' => $t->attribute( 'target_type' ), 'value' => $t->attribute( 'target_value' ) );
        $conditions = array();
        foreach ( $rule->conditions() as $c )
            $conditions[] = array( 'id' => (int)$c->attribute( 'id' ), 'type' => $c->attribute( 'condition_type' ), 'value' => $c->attribute( 'condition_value' ) );
        return array( 'id' => (int)$rule->attribute( 'id' ), 'layout_id' => (int)$rule->attribute( 'layout_id' ),
                      'priority' => (int)$rule->attribute( 'priority' ), 'enabled' => (bool)$rule->attribute( 'enabled' ),
                      'targets' => $targets, 'conditions' => $conditions );
    }

    protected static function allRules()
    {
        $rules = eZPersistentObject::fetchObjectList( expLayoutsRule::definition(), null, null, array( 'priority' => 'desc', 'id' => 'asc' ), null, true );
        return is_array( $rules ) ? $rules : array();
    }

    public static function available( $args )
    {
        self::guard( __FUNCTION__ );
        $has = class_exists( 'expLayoutsLayout' );
        return self::ok( array( 'available' => $has, 'layouts' => $has ? self::scalar( 'SELECT COUNT(*) AS n FROM explayouts_layout WHERE status=2' ) : 0 ) );
    }

    protected static function scalar( $sql )
    {
        $rows = eZDB::instance()->arrayQuery( $sql );
        return $rows ? (int)$rows[0]['n'] : 0;
    }

    public static function layouts( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $status = self::arg( $args, 2, 'int', 2 );
        $all = array();
        foreach ( expLayoutsLayout::fetchList( $status ) as $layout )
            $all[] = self::exportLayout( $layout );
        return self::pageOf( $all, $args, 0, 1 );
    }

    public static function shared( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $list = array();
        foreach ( expLayoutsLayout::fetchShared() as $layout )
            $list[] = self::exportLayout( $layout );
        return self::ok( $list );
    }

    public static function drafts( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $list = array();
        foreach ( expLayoutsLayout::fetchList( 1 ) as $layout )
            $list[] = self::exportLayout( $layout );
        return self::ok( $list );
    }

    public static function view( $args )
    {
        self::guard( __FUNCTION__ );
        $layout = self::layoutOf( self::arg( $args, 0, 'int' ) );
        $zones = array();
        foreach ( expLayoutsZone::fetchByLayout( $layout->attribute( 'id' ), (int)$layout->attribute( 'status' ) ) as $zone )
            $zones[] = self::exportZone( $zone );
        return self::ok( array_merge( self::exportLayout( $layout ), array( 'zones' => $zones ) ) );
    }

    public static function byidentifier( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $layout = expLayoutsLayout::fetchByIdentifier( self::arg( $args, 0, 'string' ), self::arg( $args, 1, 'int', 2 ) );
        if ( !$layout instanceof expLayoutsLayout )
            throw new expServiceException( 'No such layout', 404 );
        return self::ok( self::exportLayout( $layout ) );
    }

    public static function types( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $ini = eZINI::instance( 'explayouts.ini' );
        $list = array();
        foreach ( $ini->groups() as $group => $values )
            if ( strpos( $group, 'LayoutType_' ) === 0 )
                $list[] = array( 'identifier' => substr( $group, 11 ), 'name' => isset( $values['Name'] ) ? $values['Name'] : substr( $group, 11 ),
                                 'zones' => isset( $values['Zones'] ) ? array_values( (array)$values['Zones'] ) : array() );
        return self::ok( $list );
    }

    public static function blocktypes( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $category = self::arg( $args, 0, 'string', '' );
        $list = array();
        foreach ( eZINI::instance( 'explayouts.ini' )->groups() as $group => $v )
        {
            if ( strpos( $group, 'BlockDefinition_' ) !== 0 )
                continue;
            if ( $category !== '' && ( !isset( $v['Category'] ) || $v['Category'] !== $category ) )
                continue;
            $list[] = array( 'identifier' => substr( $group, 16 ), 'name' => isset( $v['Name'] ) ? $v['Name'] : '',
                             'handler' => isset( $v['Handler'] ) ? $v['Handler'] : '', 'view_types' => isset( $v['ViewTypes'] ) ? array_values( (array)$v['ViewTypes'] ) : array(),
                             'category' => isset( $v['Category'] ) ? $v['Category'] : '', 'container' => !empty( $v['IsContainer'] ) );
        }
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function zones( $args )
    {
        self::guard( __FUNCTION__ );
        $layout = self::layoutOf( self::arg( $args, 0, 'int' ) );
        $list = array();
        foreach ( expLayoutsZone::fetchByLayout( $layout->attribute( 'id' ), (int)$layout->attribute( 'status' ) ) as $zone )
            $list[] = self::exportZone( $zone );
        return self::ok( $list );
    }

    protected static function zoneOf( $args )
    {
        $layout = self::layoutOf( self::arg( $args, 0, 'int' ) );
        $zone = expLayoutsZone::fetchByLayoutAndIdentifier( $layout->attribute( 'id' ), self::arg( $args, 1, 'string' ), (int)$layout->attribute( 'status' ) );
        if ( !$zone instanceof expLayoutsZone )
            throw new expServiceException( 'No such zone', 404 );
        return $zone;
    }

    public static function zone( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( self::exportZone( self::zoneOf( $args ) ) );
    }

    public static function blocks( $args )
    {
        self::guard( __FUNCTION__ );
        $zone = self::zoneOf( $args );
        $source = expLayoutsZone::resolveSource( $zone );
        $list = array();
        foreach ( expLayoutsBlock::fetchByZone( $source->attribute( 'id' ), (int)$source->attribute( 'status' ) ) as $block )
            $list[] = self::exportBlock( $block );
        return self::ok( $list, array( 'linked' => (bool)$zone->isLinked(), 'source_layout_id' => (int)$source->attribute( 'layout_id' ) ) );
    }

    protected static function blockOf( $id )
    {
        self::need();
        $block = expLayoutsBlock::fetch( (int)$id );
        if ( !$block instanceof expLayoutsBlock )
            throw new expServiceException( "Block $id does not exist", 404 );
        return $block;
    }

    protected static function parametersOf( $block )
    {
        $map = array();
        foreach ( $block->parameters() as $p )
            $map[$p->attribute( 'name' )] = $p->attribute( 'value' );
        return $map;
    }

    public static function block( $args )
    {
        self::guard( __FUNCTION__ );
        $block = self::blockOf( self::arg( $args, 0, 'int' ) );
        return self::ok( array_merge( self::exportBlock( $block ), array( 'parameters' => (object)self::parametersOf( $block ) ) ) );
    }

    public static function blockchildren( $args )
    {
        self::guard( __FUNCTION__ );
        $block = self::blockOf( self::arg( $args, 0, 'int' ) );
        $list = array();
        foreach ( $block->children() as $child )
            $list[] = self::exportBlock( $child );
        return self::ok( $list );
    }

    public static function blockparameters( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( (object)self::parametersOf( self::blockOf( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function linkedzones( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $rows = eZDB::instance()->arrayQuery( 'SELECT id, layout_id, identifier, linked_layout_id, linked_zone_identifier FROM explayouts_zone WHERE status=2 AND linked_layout_id>0 ORDER BY layout_id, position' );
        $list = array();
        foreach ( is_array( $rows ) ? $rows : array() as $r )
            $list[] = array( 'zone_id' => (int)$r['id'], 'layout_id' => (int)$r['layout_id'], 'zone' => $r['identifier'],
                             'linked_layout_id' => (int)$r['linked_layout_id'], 'linked_zone' => $r['linked_zone_identifier'] );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function rules( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $all = array();
        foreach ( self::allRules() as $rule )
            $all[] = self::exportRule( $rule );
        return self::pageOf( $all, $args, 0, 1 );
    }

    protected static function ruleOf( $id )
    {
        self::need();
        $rule = expLayoutsRule::fetch( (int)$id );
        if ( !$rule instanceof expLayoutsRule )
            throw new expServiceException( "Rule $id does not exist", 404 );
        return $rule;
    }

    public static function rule( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( self::exportRule( self::ruleOf( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function rulesfor( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $id = self::arg( $args, 0, 'int' );
        $list = array();
        foreach ( self::allRules() as $rule )
            if ( (int)$rule->attribute( 'layout_id' ) === $id )
                $list[] = self::exportRule( $rule );
        return self::ok( $list );
    }

    /** The layout and rule a path gets, without touching the resolver cache. */
    protected static function resolveFor( $path )
    {
        $path = ltrim( (string)$path, '/' );
        if ( $path === '' )
            $path = 'home';
        foreach ( expLayoutsRule::fetchEnabled() as $rule )
        {
            if ( !expLayoutsResolver::ruleMatches( $rule, $path ) )
                continue;
            $layout = expLayoutsLayout::fetch( $rule->attribute( 'layout_id' ) );
            if ( $layout && (int)$layout->attribute( 'status' ) === 2 )
                return array( 'path' => $path, 'layout' => self::exportLayout( $layout ), 'rule' => (int)$rule->attribute( 'id' ), 'default' => false );
        }
        $default = eZINI::instance( 'explayouts.ini' )->variable( 'ResolverSettings', 'DefaultLayout' );
        $layout = $default ? expLayoutsLayout::fetchByIdentifier( $default, 2 ) : null;
        return array( 'path' => $path, 'layout' => $layout ? self::exportLayout( $layout ) : null, 'rule' => 0, 'default' => (bool)$layout );
    }

    public static function resolve( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $result = self::resolveFor( $node->attribute( 'url_alias' ) );
        $result['node'] = (int)$node->attribute( 'node_id' );
        return self::ok( $result );
    }

    public static function resolvepath( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $path = self::arg( $args, 0, 'string' );
        if ( strlen( $path ) > 500 || preg_match( '/[\x00-\x1f]/', $path ) )
            throw new expServiceException( 'The path is not valid', 400 );
        return self::ok( self::resolveFor( $path ) );
    }

    public static function stats( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $per = array();
        $rows = eZDB::instance()->arrayQuery( 'SELECT definition_identifier AS d, COUNT(*) AS n FROM explayouts_block WHERE status=2 GROUP BY definition_identifier ORDER BY n DESC' );
        foreach ( is_array( $rows ) ? $rows : array() as $r )
            $per[$r['d']] = (int)$r['n'];
        return self::ok( array( 'layouts' => self::scalar( 'SELECT COUNT(*) AS n FROM explayouts_layout WHERE status=2' ),
                                'drafts' => self::scalar( 'SELECT COUNT(*) AS n FROM explayouts_layout WHERE status=1' ),
                                'shared' => self::scalar( 'SELECT COUNT(*) AS n FROM explayouts_layout WHERE status=2 AND shared=1' ),
                                'zones' => self::scalar( 'SELECT COUNT(*) AS n FROM explayouts_zone WHERE status=2' ),
                                'blocks' => self::scalar( 'SELECT COUNT(*) AS n FROM explayouts_block WHERE status=2' ),
                                'rules' => self::scalar( 'SELECT COUNT(*) AS n FROM explayouts_rule' ),
                                'blocks_per_definition' => $per ) );
    }

    // ------------------------------------------------------------------ writes

    protected static function service()
    {
        if ( !class_exists( 'expLayoutsCoreLayoutService' ) )
            throw new expServiceException( 'The explayouts_core extension is not available', 404 );
        return new expLayoutsCoreLayoutService();
    }

    public static function createdraft( $args )
    {
        self::guard( __FUNCTION__ );
        $layout = self::layoutOf( self::arg( $args, 0, 'int' ) );
        $draft = self::service()->createDraft( (int)$layout->attribute( 'id' ) );
        if ( !$draft )
            throw new expServiceException( 'The draft could not be created', 422 );
        return self::ok( self::exportLayout( $draft ) );
    }

    public static function publish( $args )
    {
        self::guard( __FUNCTION__ );
        $layout = self::layoutOf( self::arg( $args, 0, 'int' ) );
        $service = self::service();
        if ( !$service->loadDraft( (int)$layout->attribute( 'id' ) ) )
            throw new expServiceException( 'The layout has no draft to publish', 409 );
        $published = $service->publish( (int)$layout->attribute( 'id' ) );
        if ( !$published )
            throw new expServiceException( 'The layout could not be published', 422 );
        expLayoutsResolver::clearCache();
        return self::ok( self::exportLayout( $published ) );
    }

    public static function discard( $args )
    {
        self::guard( __FUNCTION__ );
        $layout = self::layoutOf( self::arg( $args, 0, 'int' ) );
        $service = self::service();
        $draft = $service->loadDraft( (int)$layout->attribute( 'id' ) );
        if ( !$draft || (int)$draft->attribute( 'status' ) !== 1 )
            throw new expServiceException( 'The layout has no draft', 409 );
        $service->discard( (int)$draft->attribute( 'id' ) );
        return self::ok( array( 'discarded' => (int)$draft->attribute( 'id' ) ) );
    }

    protected static function setEnabled( $args, $enabled )
    {
        $rule = self::ruleOf( self::arg( $args, 0, 'int' ) );
        $rule->setAttribute( 'enabled', $enabled ? 1 : 0 );
        $rule->store();
        expLayoutsResolver::clearCache();
        return self::ok( self::exportRule( $rule ) );
    }

    public static function enablerule( $args )
    {
        self::guard( __FUNCTION__ );
        return self::setEnabled( $args, true );
    }

    public static function disablerule( $args )
    {
        self::guard( __FUNCTION__ );
        return self::setEnabled( $args, false );
    }

    public static function clearcache( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        expLayoutsResolver::clearCache();
        return self::ok( array( 'cleared' => true ) );
    }
}
