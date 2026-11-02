<?php
/**
 * File containing the expMigrationStepLayoutBlock class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "layout_block": a block in a zone of a layout of Exponential Layouts, with its parameters.
 *
 *   - type: layout_block
 *     mode: ensure                     create, update, ensure, delete
 *     layout: landing_page             the identifier of the layout
 *     zone: main                       the zone (a zone linked to a shared layout has no blocks of its own)
 *     name: Welcome                    the block's name: how the step finds it in the zone
 *     definition: title                a block definition of explayouts.ini ([BlockDefinition_<identifier>])
 *     view_type: title                 how the block renders
 *     item_view_type: line             how it renders its items
 *     position: 0                      its place in the zone (default: after the last)
 *     parameters: { title: Welcome, tag: h1 }
 *     publish: true                    false: the change stays in the layout's draft
 *
 * The change is made in the layout's draft and published, as the layout editor does. A block of a published shared
 * layout is changed the same way: through its own layout. A create undoes itself (the block is removed again); the
 * other modes do not.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepLayoutBlock extends expMigrationStepLayoutBase implements expMigrationPartlyRevertibleStepType
{
    /** @return string */
    public function name()
    {
        return 'layout_block';
    }

    /** @return array */
    public function schema()
    {
        $writing = array( 'create', 'update', 'ensure' );
        return array(
            'description' => 'Creates, updates, ensures or deletes a block in a zone of a layout of Exponential Layouts, with its parameters',
            'modes' => array( 'create', 'update', 'ensure', 'delete' ),
            'keys' => array(
                'layout' => array( 'type' => 'string', 'required' => true, 'description' => 'the identifier of the layout' ),
                'zone' => array( 'type' => 'string', 'required' => true, 'description' => 'the identifier of the zone' ),
                'name' => array( 'type' => 'string', 'required' => true, 'description' => 'the name of the block, by which the step finds it in the zone' ),
                'definition' => array( 'type' => 'string', 'modes' => $writing,
                                       'description' => 'the block definition (explayouts.ini [BlockDefinition_<identifier>]): title, text, list ...' ),
                'view_type' => array( 'type' => 'string', 'modes' => $writing, 'description' => 'how the block renders (a new block: default)' ),
                'item_view_type' => array( 'type' => 'string', 'modes' => $writing, 'description' => 'how the block renders its items' ),
                'position' => array( 'type' => 'int', 'modes' => $writing, 'description' => 'the place in the zone, from 0 (a new block: after the last)' ),
                'parameters' => array( 'type' => 'map', 'modes' => $writing, 'description' => 'parameter => value; the others are kept' ),
                'publish' => array( 'type' => 'bool', 'default' => true, 'modes' => array( 'create', 'update', 'ensure', 'delete' ),
                                    'description' => 'true: the layout\'s draft is published; false: the change stays in the draft' ),
                'missing' => array( 'type' => 'string', 'default' => 'fail', 'values' => array( 'fail', 'skip' ),
                                    'description' => 'what happens when the layout, the zone, the block or Exponential Layouts is not there: fail (the default) or skip' ),
            ),
            'values' => array(
                'block_id' => 'the id of the block (in the published layout, or in the draft with publish: false)',
                'layout_id' => 'the id of the layout it is in',
                'created' => 'true when the step created the block',
                'changed' => 'true when the step changed something',
            ),
            'example' => "- type: layout_block\n  mode: ensure\n  layout: landing_page\n  zone: main\n  name: Welcome\n  definition: title\n"
                       . "  view_type: title\n  parameters: { title: Welcome, tag: h1 }\n",
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
        foreach ( array( 'layout', 'zone' ) as $key )
            if ( isset( $step[$key] ) && !self::validIdentifier( $step[$key] ) && !expMigrationReferences::contains( $step[$key] ) )
                $errors[] = "$key: letters, digits, _ and -";
        if ( isset( $step['name'] ) && trim( (string)$step['name'] ) === '' )
            $errors[] = 'name: the name of the block';
        if ( in_array( $mode, array( 'create', 'ensure' ), true ) && !isset( $step['definition'] ) )
            $errors[] = "definition is missing (mode $mode)";
        if ( $mode === 'update' && !array_intersect( array( 'definition', 'view_type', 'item_view_type', 'position', 'parameters' ), array_keys( $step ) ) )
            $errors[] = 'nothing to update: definition:, view_type:, item_view_type:, position: or parameters: is missing';
        if ( isset( $step['position'] ) && is_int( $step['position'] ) && $step['position'] < 0 )
            $errors[] = 'position: 0 or more';
        if ( isset( $step['parameters'] ) && is_array( $step['parameters'] ) )
            foreach ( $step['parameters'] as $name => $value )
                if ( !preg_match( '/^[A-Za-z0-9_]+$/', (string)$name ) )
                    $errors[] = "parameters.$name: the name of a parameter";
        return $errors;
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
        $where = 'in the zone ' . $step['zone'] . ' of the layout ' . $step['layout'];
        $what = 'the block "' . $step['name'] . '"' . ( isset( $step['definition'] ) ? ' (' . $step['definition'] . ')' : '' );
        $params = !empty( $step['parameters'] ) && is_array( $step['parameters'] ) ? ', parameters ' . implode( ', ', array_keys( $step['parameters'] ) ) : '';
        $draft = isset( $step['publish'] ) && !$step['publish'] ? ', left in the draft' : '';
        $note = self::planNote();
        switch ( $step['mode'] )
        {
            case 'create':
                return array( "create $what $where$params$draft$note" );
            case 'ensure':
                return array( "ensure $what $where: create it when missing, change what differs$params$draft$note" );
            case 'update':
                return array( "update $what $where$params$draft$note" );
            case 'delete':
                return array( "delete $what $where$draft$note" );
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
        $empty = array( 'block_id' => null, 'layout_id' => null, 'created' => false, 'changed' => false );
        if ( !self::installed() )
            return self::notInstalled( $step, 'layout_block', $empty );
        $layoutIdentifier = trim( (string)$step['layout'] );
        if ( !self::published( $layoutIdentifier ) && !self::draft( $layoutIdentifier ) )
            return self::missingResult( $step, "the layout $layoutIdentifier", $empty );
        $hadDraft = self::draft( $layoutIdentifier ) !== null;
        $draft = self::draftToChange( $layoutIdentifier );
        $zone = $draft ? self::zoneOf( $draft, $step['zone'] ) : null;
        if ( !$zone )
        {
            self::discardUnchanged( $draft, $hadDraft );
            return self::missingResult( $step, 'the zone ' . $step['zone'] . " of the layout $layoutIdentifier", $empty );
        }
        if ( $zone->isLinked() )
        {
            self::discardUnchanged( $draft, $hadDraft );
            throw new RuntimeException( 'the zone ' . $step['zone'] . " of the layout $layoutIdentifier takes its blocks from a shared layout: change them there" );
        }
        $blocks = self::blocksNamed( $zone, $step['name'] );
        if ( count( $blocks ) > 1 )
        {
            self::discardUnchanged( $draft, $hadDraft );
            throw new RuntimeException( 'the zone ' . $step['zone'] . ' has ' . count( $blocks ) . ' blocks named "' . $step['name'] . '": give them names of their own' );
        }
        $block = $blocks ? $blocks[0] : null;
        $mode = $step['mode'];
        if ( $mode === 'create' && $block )
        {
            self::discardUnchanged( $draft, $hadDraft );
            throw new RuntimeException( 'the block "' . $step['name'] . '" is in the zone already (mode ensure changes it)' );
        }
        if ( !$block && ( $mode === 'update' || $mode === 'delete' ) )
        {
            self::discardUnchanged( $draft, $hadDraft );
            return self::missingResult( $step, 'the block "' . $step['name'] . '"', $empty );
        }
        $created = false;
        if ( $mode === 'delete' )
        {
            self::blocks()->delete( (int)$block->attribute( 'id' ) );
            self::renumber( $zone );
            $lines = array( 'deleted the block "' . $step['name'] . '" from the zone ' . $step['zone'] );
        }
        elseif ( !$block )
        {
            $after = 0;
            foreach ( (array)expLayoutsBlock::fetchByZone( (int)$zone->attribute( 'id' ), self::DRAFT ) as $row )
                if ( (int)$row->attribute( 'parent_id' ) === 0 )
                    $after = max( $after, (int)$row->attribute( 'position' ) + 1 );
            $block = self::blocks()->create( (int)$zone->attribute( 'id' ), (int)$draft->attribute( 'id' ), (string)$step['definition'], (string)$step['name'],
                                             isset( $step['view_type'] ) ? (string)$step['view_type'] : 'default' );
            $block->setAttribute( 'status', self::DRAFT );
            $block->setAttribute( 'position', $after );
            $block->store();
            $created = true;
            $lines = array( 'created the block "' . $step['name'] . '" (' . $step['definition'] . ') in the zone ' . $step['zone'] );
            $lines = array_merge( $lines, $this->change( $block, $zone, $step, true ) );
        }
        else
            $lines = $this->change( $block, $zone, $step, false );
        if ( !$lines )
        {
            self::discardUnchanged( $draft, $hadDraft );
            $published = self::published( $layoutIdentifier );
            return self::result( array( 'the block "' . $step['name'] . '" is as the step says: nothing changed' ),
                                 array( 'block_id' => (int)$block->attribute( 'id' ), 'layout_id' => $published ? (int)$published->attribute( 'id' ) : (int)$draft->attribute( 'id' ),
                                        'created' => false, 'changed' => false ) );
        }
        $publish = !isset( $step['publish'] ) || $step['publish'];
        $layout = self::finish( $draft, $publish );
        $lines[] = $publish ? "published the layout $layoutIdentifier" : "the layout $layoutIdentifier stays a draft";
        $blockId = null;
        if ( $mode !== 'delete' )
        {
            $final = self::zoneOf( $layout, $step['zone'] );
            $found = $final ? self::blocksNamed( $final, $step['name'] ) : array();
            $blockId = $found ? (int)$found[0]->attribute( 'id' ) : null;
        }
        return self::result( $lines, array( 'block_id' => $blockId, 'layout_id' => (int)$layout->attribute( 'id' ), 'created' => $created, 'changed' => true ) );
    }

    /**
     * Removes the block a create made, and publishes the layout.
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
     * Sets what a step says on a block of the draft.
     *
     * @param expLayoutsBlock $block
     * @param expLayoutsZone $zone
     * @param array $step
     * @param bool $new
     * @return string[] what changed
     */
    protected function change( expLayoutsBlock $block, expLayoutsZone $zone, array $step, $new )
    {
        $lines = array();
        $changed = false;
        foreach ( array( 'definition' => 'definition_identifier', 'view_type' => 'view_type', 'item_view_type' => 'item_view_type' ) as $key => $column )
            if ( isset( $step[$key] ) && (string)$block->attribute( $column ) !== (string)$step[$key] )
            {
                if ( !$new )
                    $lines[] = "$key: " . $block->attribute( $column ) . ' -> ' . $step[$key];
                $block->setAttribute( $column, (string)$step[$key] );
                $changed = true;
            }
        if ( $changed )
            $block->store();
        if ( isset( $step['position'] ) && (int)$block->attribute( 'position' ) !== (int)$step['position'] )
        {
            self::move( $block, $zone, (int)$step['position'] );
            $lines[] = 'position ' . (int)$step['position'];
        }
        if ( !empty( $step['parameters'] ) && is_array( $step['parameters'] ) )
        {
            $current = array();
            foreach ( (array)expLayoutsBlockParameter::fetchByBlock( (int)$block->attribute( 'id' ) ) as $parameter )
                $current[(string)$parameter->attribute( 'name' )] = $parameter->attribute( 'value' );
            $set = array();
            foreach ( $step['parameters'] as $name => $value )
            {
                $value = is_array( $value ) ? json_encode( $value ) : ( is_bool( $value ) ? ( $value ? '1' : '0' ) : (string)$value );
                if ( !array_key_exists( (string)$name, $current ) || (string)$current[(string)$name] !== $value )
                    $set[(string)$name] = $value;
            }
            if ( $set )
            {
                self::blocks()->setParameters( (int)$block->attribute( 'id' ), $set );
                $lines[] = 'parameters ' . implode( ', ', array_keys( $set ) );
            }
        }
        return $lines;
    }

    /**
     * Moves a block to a place in its zone, the others after it.
     *
     * @param expLayoutsBlock $block
     * @param expLayoutsZone $zone
     * @param int $position
     */
    protected static function move( expLayoutsBlock $block, expLayoutsZone $zone, $position )
    {
        $others = array();
        foreach ( (array)expLayoutsBlock::fetchByZone( (int)$zone->attribute( 'id' ), (int)$zone->attribute( 'status' ) ) as $row )
            if ( (int)$row->attribute( 'parent_id' ) === 0 && (int)$row->attribute( 'id' ) !== (int)$block->attribute( 'id' ) )
                $others[] = $row;
        usort( $others, function ( $a, $b ) { return (int)$a->attribute( 'position' ) - (int)$b->attribute( 'position' ); } );
        array_splice( $others, max( 0, min( $position, count( $others ) ) ), 0, array( $block ) );
        foreach ( $others as $i => $row )
            if ( (int)$row->attribute( 'position' ) !== $i )
            {
                $row->setAttribute( 'position', $i );
                $row->store();
            }
    }

    /**
     * Numbers the top-level blocks of a zone 0, 1, 2 ... in their order.
     *
     * @param expLayoutsZone $zone
     */
    protected static function renumber( expLayoutsZone $zone )
    {
        $rows = array();
        foreach ( (array)expLayoutsBlock::fetchByZone( (int)$zone->attribute( 'id' ), (int)$zone->attribute( 'status' ) ) as $row )
            if ( (int)$row->attribute( 'parent_id' ) === 0 )
                $rows[] = $row;
        usort( $rows, function ( $a, $b ) { return (int)$a->attribute( 'position' ) - (int)$b->attribute( 'position' ); } );
        foreach ( $rows as $i => $row )
            if ( (int)$row->attribute( 'position' ) !== $i )
            {
                $row->setAttribute( 'position', $i );
                $row->store();
            }
    }

    /**
     * Removes a draft the step made when it changed nothing.
     *
     * @param expLayoutsLayout|null $draft
     * @param bool $hadDraft
     */
    protected static function discardUnchanged( $draft, $hadDraft )
    {
        if ( $draft && !$hadDraft )
            self::layouts()->discard( (int)$draft->attribute( 'id' ) );
    }
}
