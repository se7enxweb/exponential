<?php
/**
 * File containing the expMigrationStepLayoutRule class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "layout_rule": a rule of Exponential Layouts that maps pages to a layout.
 *
 *   - type: layout_rule
 *     mode: ensure                                 create, update, ensure, delete
 *     layout: landing_page                         the identifier of the published layout
 *     targets:                                     where the rule applies: type and value, as the rule editor has them
 *       - { type: node, value: "remote:landing" }  node and subtree take an identifier of a node
 *       - { type: path_prefix, value: /campaign }
 *     conditions:                                  when it applies (siteaccess, class, query_parameter ...)
 *       - { type: siteaccess, value: site }
 *     priority: 50                                 higher first (a new rule: above every other)
 *     enabled: true
 *
 * A rule is named by its layout and its targets: ensure and update find the rule of the layout with the same
 * targets, delete removes it (or with which: all every rule of the layout). The target types are those of the layout
 * resolver (expLayoutsResolver::TARGET_TYPES). A create undoes itself (the rule is removed again); the other modes do
 * not.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepLayoutRule extends expMigrationStepLayoutBase implements expMigrationPartlyRevertibleStepType
{
    /** The target types whose value is a node, which a step may name by any identifier of a node. */
    const NODE_TARGETS = array( 'node', 'subtree' );

    /** @return string */
    public function name()
    {
        return 'layout_rule';
    }

    /** @return array */
    public function schema()
    {
        $writing = array( 'create', 'update', 'ensure' );
        return array(
            'description' => 'Creates, updates, ensures or deletes a rule of Exponential Layouts that maps pages to a layout',
            'modes' => array( 'create', 'update', 'ensure', 'delete' ),
            'keys' => array(
                'layout' => array( 'type' => 'string', 'required' => true, 'description' => 'the identifier of the published layout' ),
                'targets' => array( 'type' => 'list',
                                    'description' => 'where the rule applies: a list of { type, value } (path, path_prefix, path_info_prefix, path_regex, node, subtree, route, module, module_view); the rule is found by them' ),
                'conditions' => array( 'type' => 'list', 'modes' => $writing,
                                       'description' => 'when it applies: a list of { type, value } (siteaccess, class, query_parameter, route_parameter, time ...)' ),
                'priority' => array( 'type' => 'int', 'modes' => $writing, 'description' => 'the higher, the earlier it is tried (a new rule: above every other)' ),
                'enabled' => array( 'type' => 'bool', 'modes' => $writing, 'description' => 'whether the rule is used (a new rule: true)' ),
                'which' => array( 'type' => 'string', 'modes' => array( 'delete' ), 'values' => array( 'all' ),
                                  'description' => 'all: every rule of the layout, whatever its targets' ),
                'missing' => array( 'type' => 'string', 'default' => 'fail', 'values' => array( 'fail', 'skip' ),
                                    'description' => 'what happens when the layout, the rule or Exponential Layouts is not there: fail (the default) or skip' ),
            ),
            'values' => array(
                'rule_id' => 'the id of the rule (of the first with which: all)',
                'layout_id' => 'the id of the layout',
                'created' => 'true when the step created the rule',
                'changed' => 'true when the step changed something',
            ),
            'example' => "- type: layout_rule\n  mode: ensure\n  layout: landing_page\n  targets:\n    - { type: node, value: \"remote:landing\" }\n"
                       . "  conditions:\n    - { type: siteaccess, value: site }\n  priority: 50\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        $mode = isset( $step['mode'] ) ? $step['mode'] : null;
        if ( isset( $step['layout'] ) && !self::validIdentifier( $step['layout'] ) && !expMigrationReferences::contains( $step['layout'] ) )
            $errors[] = 'layout: letters, digits, _ and -';
        $all = isset( $step['which'] ) && $step['which'] === 'all';
        if ( !$all && empty( $step['targets'] ) )
            $errors[] = $mode === 'delete' ? 'name the rule by targets: or delete every rule of the layout with which: all' : 'targets is missing: where the rule applies';
        if ( $all && !empty( $step['targets'] ) )
            $errors[] = 'which: all deletes every rule of the layout; targets: does not go with it';
        if ( $mode === 'update' && !array_intersect( array( 'conditions', 'priority', 'enabled' ), array_keys( $step ) ) )
            $errors[] = 'nothing to update: conditions:, priority: or enabled: is missing';
        foreach ( array( 'targets', 'conditions' ) as $key )
            if ( isset( $step[$key] ) && is_array( $step[$key] ) )
                foreach ( $step[$key] as $i => $item )
                {
                    if ( !is_array( $item ) || !isset( $item['type'] ) || !is_scalar( $item['type'] ) || trim( (string)$item['type'] ) === ''
                         || array_diff( array_keys( $item ), array( 'type', 'value' ) ) || ( isset( $item['value'] ) && !is_scalar( $item['value'] ) ) )
                        $errors[] = "{$key}[$i]: { type: <type>, value: <value> }";
                    elseif ( $key === 'targets' && !in_array( (string)$item['type'], self::targetTypes(), true ) )
                        $errors[] = "targets[$i]: " . $item['type'] . ' is no target type (' . implode( ', ', self::targetTypes() ) . ')';
                    elseif ( $key === 'targets' && in_array( (string)$item['type'], self::NODE_TARGETS, true ) && isset( $item['value'] )
                             && !expMigrationReferences::contains( $item['value'] ) )
                    {
                        $problem = expMigrationIdentifier::problem( 'node', $item['value'] );
                        if ( $problem !== null )
                            $errors[] = "targets[$i].value: $problem";
                    }
                }
        return $errors;
    }

    /** @return string[] the target types of the resolver */
    protected static function targetTypes()
    {
        return class_exists( 'expLayoutsResolver' ) && defined( 'expLayoutsResolver::TARGET_TYPES' )
             ? expLayoutsResolver::TARGET_TYPES
             : array( 'path', 'path_prefix', 'path_info_prefix', 'path_regex', 'node', 'subtree', 'route', 'module', 'module_view' );
    }

    /**
     * @param array $step
     * @return bool
     */
    public function revertible( array $step )
    {
        return isset( $step['mode'] ) && $step['mode'] === 'create';
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $targets = !empty( $step['targets'] ) ? ' for ' . self::describe( (array)$step['targets'] ) : '';
        $note = self::planNote();
        $layout = $step['layout'];
        switch ( $step['mode'] )
        {
            case 'create':
                return array( "create a rule of the layout $layout$targets$note" );
            case 'ensure':
                return array( "ensure the rule of the layout $layout$targets: create it when missing, change what differs$note" );
            case 'update':
                return array( "update the rule of the layout $layout$targets$note" );
            case 'delete':
                return array( isset( $step['which'] ) && $step['which'] === 'all' ? "delete every rule of the layout $layout$note"
                                                                                   : "delete the rule of the layout $layout$targets$note" );
        }
        return parent::plan( $step, $context );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $empty = array( 'rule_id' => null, 'layout_id' => null, 'created' => false, 'changed' => false );
        if ( !self::installed() )
            return self::notInstalled( $step, 'layout_rule', $empty );
        $identifier = trim( (string)$step['layout'] );
        $layout = self::published( $identifier );
        if ( !$layout )
            return self::missingResult( $step, "the published layout $identifier", $empty );
        $layoutId = (int)$layout->attribute( 'id' );
        if ( $step['mode'] === 'delete' && isset( $step['which'] ) && $step['which'] === 'all' )
        {
            $lines = array();
            $first = null;
            foreach ( (array)self::rules()->listByLayout( $layoutId ) as $rule )
            {
                $first = $first === null ? (int)$rule->attribute( 'id' ) : $first;
                self::rules()->delete( (int)$rule->attribute( 'id' ) );
                $lines[] = 'deleted the rule ' . $rule->attribute( 'id' ) . " of the layout $identifier";
            }
            if ( !$lines )
                $lines[] = "the layout $identifier has no rule: nothing deleted";
            return self::result( $lines, array( 'rule_id' => $first, 'layout_id' => $layoutId, 'created' => false, 'changed' => $first !== null ) );
        }
        $targets = self::resolveTargets( (array)$step['targets'] );
        $rule = self::find( $layoutId, $targets );
        $what = 'the rule of the layout ' . $identifier . ' for ' . self::describe( $targets );
        switch ( $step['mode'] )
        {
            case 'create':
                if ( $rule )
                    throw new RuntimeException( "$what is there already (mode ensure changes it)" );
                return $this->create( $layoutId, $targets, $step, $what );
            case 'ensure':
                return $rule ? $this->update( $rule, $step, $what, $layoutId ) : $this->create( $layoutId, $targets, $step, $what );
            case 'update':
                return $rule ? $this->update( $rule, $step, $what, $layoutId ) : self::missingResult( $step, $what, $empty );
            case 'delete':
                if ( !$rule )
                    return self::missingResult( $step, $what, $empty );
                self::rules()->delete( (int)$rule->attribute( 'id' ) );
                return self::result( array( "deleted $what" ), array( 'rule_id' => (int)$rule->attribute( 'id' ), 'layout_id' => $layoutId, 'created' => false, 'changed' => true ) );
        }
        throw new RuntimeException( 'the mode ' . $step['mode'] . ' is not one of create, update, ensure, delete' );
    }

    /**
     * Removes the rule a create made.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        if ( !$this->revertible( $step ) )
            throw new RuntimeException( 'only a create can be undone by itself; give the migration a down:' );
        if ( !self::installed() )
            return array( 'Exponential Layouts is not installed: nothing to undo' );
        $undo = $step;
        $undo['mode'] = 'delete';
        $undo['missing'] = 'skip';
        $result = $this->apply( $undo, $context );
        return $result['lines'];
    }

    /**
     * @param int $layoutId
     * @param array $targets
     * @param array $step
     * @param string $what
     * @return array
     */
    protected function create( $layoutId, array $targets, array $step, $what )
    {
        $rule = self::rules()->create( $layoutId, isset( $step['priority'] ) ? (int)$step['priority'] : null,
                                       !isset( $step['enabled'] ) || $step['enabled'] ? 1 : 0 );
        if ( !$rule instanceof expLayoutsRule )
            throw new RuntimeException( "$what could not be created" );
        $ruleId = (int)$rule->attribute( 'id' );
        self::rules()->setTargets( $ruleId, $targets );
        if ( !empty( $step['conditions'] ) )
            self::rules()->setConditions( $ruleId, self::pairs( (array)$step['conditions'] ) );
        return self::result( array( "created $what (priority " . (int)$rule->attribute( 'priority' ) . ')' ),
                             array( 'rule_id' => $ruleId, 'layout_id' => $layoutId, 'created' => true, 'changed' => true ) );
    }

    /**
     * @param expLayoutsRule $rule
     * @param array $step
     * @param string $what
     * @param int $layoutId
     * @return array
     */
    protected function update( expLayoutsRule $rule, array $step, $what, $layoutId )
    {
        $ruleId = (int)$rule->attribute( 'id' );
        $lines = array();
        $attributes = array();
        if ( isset( $step['priority'] ) && (int)$rule->attribute( 'priority' ) !== (int)$step['priority'] )
        {
            $attributes['priority'] = (int)$step['priority'];
            $lines[] = 'priority ' . $rule->attribute( 'priority' ) . ' -> ' . (int)$step['priority'];
        }
        if ( isset( $step['enabled'] ) && (int)$rule->attribute( 'enabled' ) !== self::flag( $step['enabled'] ) )
        {
            $attributes['enabled'] = self::flag( $step['enabled'] );
            $lines[] = $step['enabled'] ? 'enabled' : 'disabled';
        }
        if ( $attributes )
            self::rules()->update( $ruleId, $attributes );
        if ( isset( $step['conditions'] ) )
        {
            $wanted = self::pairs( (array)$step['conditions'] );
            $current = array();
            foreach ( (array)$rule->conditions() as $condition )
                $current[] = array( 'type' => (string)$condition->attribute( 'condition_type' ), 'value' => (string)$condition->attribute( 'condition_value' ) );
            if ( self::key( $wanted ) !== self::key( $current ) )
            {
                self::rules()->setConditions( $ruleId, $wanted );
                $lines[] = 'conditions ' . ( $wanted ? self::describe( $wanted ) : 'none' );
            }
        }
        if ( !$lines )
            return self::result( array( "$what is as the step says: nothing changed" ), array( 'rule_id' => $ruleId, 'layout_id' => $layoutId, 'created' => false, 'changed' => false ) );
        array_unshift( $lines, "updated $what" );
        return self::result( $lines, array( 'rule_id' => $ruleId, 'layout_id' => $layoutId, 'created' => false, 'changed' => true ) );
    }

    /**
     * The rule of a layout with exactly these targets, or null.
     *
     * @param int $layoutId
     * @param array $targets
     * @return expLayoutsRule|null
     */
    protected static function find( $layoutId, array $targets )
    {
        $wanted = self::key( $targets );
        foreach ( (array)self::rules()->listByLayout( $layoutId ) as $rule )
        {
            $have = array();
            foreach ( (array)$rule->targets() as $target )
                $have[] = array( 'type' => (string)$target->attribute( 'target_type' ), 'value' => (string)$target->attribute( 'target_value' ) );
            if ( self::key( $have ) === $wanted )
                return $rule;
        }
        return null;
    }

    /**
     * The targets with a node named by its id (node, subtree).
     *
     * @param array $targets
     * @return array list of array( type, value )
     */
    protected static function resolveTargets( array $targets )
    {
        $pairs = self::pairs( $targets );
        foreach ( $pairs as $i => $pair )
            if ( in_array( $pair['type'], self::NODE_TARGETS, true ) && !ctype_digit( $pair['value'] ) )
            {
                $id = expMigrationIdentifier::find( 'node', $pair['value'] );
                if ( $id === null )
                    throw new RuntimeException( "targets[$i]: the node " . $pair['value'] . ' does not exist' );
                $pairs[$i]['value'] = (string)$id;
            }
        return $pairs;
    }

    /**
     * Pure.
     *
     * @param array $items
     * @return array list of array( 'type' => string, 'value' => string )
     */
    protected static function pairs( array $items )
    {
        $pairs = array();
        foreach ( $items as $item )
            $pairs[] = array( 'type' => trim( (string)$item['type'] ), 'value' => isset( $item['value'] ) ? trim( (string)$item['value'] ) : '' );
        return $pairs;
    }

    /**
     * A list of pairs as one text, the order left out. Pure.
     *
     * @param array $pairs
     * @return string
     */
    protected static function key( array $pairs )
    {
        $texts = array();
        foreach ( $pairs as $pair )
            $texts[] = $pair['type'] . '=' . $pair['value'];
        sort( $texts );
        return implode( "\n", $texts );
    }

    /**
     * @param array $pairs
     * @return string
     */
    protected static function describe( array $pairs )
    {
        $texts = array();
        foreach ( $pairs as $pair )
            $texts[] = ( is_array( $pair ) && isset( $pair['type'] ) ? $pair['type'] : '?' ) . ' ' . ( is_array( $pair ) && isset( $pair['value'] ) ? expMigrationStepSchema::show( $pair['value'] ) : '' );
        return implode( ', ', $texts );
    }
}
