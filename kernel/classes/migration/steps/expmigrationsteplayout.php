<?php
/**
 * File containing the expMigrationStepLayout class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "layout": a layout of Exponential Layouts with its zones, shared or not, and the zones it takes from a
 * shared layout.
 *
 *   - type: layout
 *     mode: ensure                         create, update, ensure, delete
 *     identifier: landing_page             the layout's identifier
 *     name: Landing page
 *     layout_type: layout_2                a layout type of explayouts.ini ([LayoutType_*]); its zones are added
 *     shared: false                        true: a shared layout whose zones others link to (Header / Footer)
 *     zones:                               zones the layout has, and what each takes from a shared layout
 *       main: {}                           a zone of its own
 *       header: { link: "header_footer/header" }   the blocks of zone header of the shared layout header_footer
 *       footer: { link: none }             a zone of its own again
 *     publish: true                        false: the change stays in the layout's draft
 *
 * A layout is named by its identifier. A create or update is made in the layout's draft and published, as the layout
 * editor does; a link names the shared layout by its identifier and must name one of its zones. A delete removes the
 * published layout and its draft (its rules are switched off, zones linked to it are unlinked, as in the editor).
 * A create undoes itself (the layout is deleted again); the other modes do not.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepLayout extends expMigrationStepLayoutBase implements expMigrationPartlyRevertibleStepType
{
    /** @return string */
    public function name()
    {
        return 'layout';
    }

    /** @return array */
    public function schema()
    {
        $writing = array( 'create', 'update', 'ensure' );
        return array(
            'description' => 'Creates, updates, ensures or deletes a layout of Exponential Layouts with its zones and their links to shared layouts',
            'modes' => array( 'create', 'update', 'ensure', 'delete' ),
            'keys' => array(
                'identifier' => array( 'type' => 'string', 'required' => true, 'description' => 'the identifier of the layout' ),
                'name' => array( 'type' => 'string', 'modes' => $writing, 'description' => 'the name of the layout' ),
                'layout_type' => array( 'type' => 'string', 'modes' => $writing,
                                        'description' => 'a layout type of explayouts.ini ([LayoutType_<type>]); the zones it defines are added' ),
                'shared' => array( 'type' => 'bool', 'modes' => $writing,
                                   'description' => 'whether other layouts can take the blocks of its zones (a new layout: false)' ),
                'zones' => array( 'type' => 'map', 'modes' => $writing,
                                  'description' => 'zone => {} (a zone of its own), { link: "<shared layout>/<zone>" } or { link: none }' ),
                'publish' => array( 'type' => 'bool', 'default' => true, 'modes' => $writing,
                                    'description' => 'true: the draft is published; false: the change stays in the draft' ),
                'missing' => array( 'type' => 'string', 'default' => 'fail', 'values' => array( 'fail', 'skip' ),
                                    'description' => 'what happens when the layout, or Exponential Layouts, is not there: fail (the default) or skip' ),
            ),
            'values' => array(
                'layout_id' => 'the id of the published layout (of the draft with publish: false)',
                'identifier' => 'its identifier',
                'created' => 'true when the step created the layout',
                'changed' => 'true when the step changed something',
            ),
            'example' => "- type: layout\n  mode: ensure\n  identifier: landing_page\n  name: Landing page\n  layout_type: layout_2\n"
                       . "  zones:\n    main: {}\n    header: { link: \"header_footer/header\" }\n  references: { landing: layout_id }\n",
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
        if ( isset( $step['identifier'] ) && !self::validIdentifier( $step['identifier'] ) && !expMigrationReferences::contains( $step['identifier'] ) )
            $errors[] = 'identifier: letters, digits, _ and -';
        if ( $mode === 'update' && !array_intersect( array( 'name', 'layout_type', 'shared', 'zones' ), array_keys( $step ) ) )
            $errors[] = 'nothing to update: name:, layout_type:, shared: or zones: is missing';
        if ( isset( $step['zones'] ) && is_array( $step['zones'] ) )
            foreach ( $step['zones'] as $zone => $definition )
            {
                if ( !self::validIdentifier( $zone ) )
                    $errors[] = "zones.$zone: the identifier of a zone";
                if ( $definition === null || $definition === array() )
                    continue;
                if ( !is_array( $definition ) || array_diff( array_keys( $definition ), array( 'link' ) ) )
                    $errors[] = "zones.$zone: {} or { link: \"<shared layout>/<zone>\" } or { link: none }";
                elseif ( isset( $definition['link'] ) && !self::linkProblemFree( $definition['link'] ) )
                    $errors[] = "zones.$zone.link: \"<shared layout>/<zone>\" or none";
            }
        return $errors;
    }

    /**
     * @param mixed $link
     * @return bool
     */
    protected static function linkProblemFree( $link )
    {
        if ( !is_scalar( $link ) )
            return false;
        $link = (string)$link;
        return $link === 'none' || expMigrationReferences::contains( $link ) || preg_match( '#^[A-Za-z0-9_][A-Za-z0-9_-]*/[A-Za-z0-9_][A-Za-z0-9_-]*$#', $link ) === 1;
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
        $identifier = $step['identifier'];
        $note = self::planNote();
        $zones = '';
        if ( !empty( $step['zones'] ) && is_array( $step['zones'] ) )
        {
            $parts = array();
            foreach ( $step['zones'] as $zone => $definition )
                $parts[] = $zone . ( is_array( $definition ) && isset( $definition['link'] ) ? ' (' . ( $definition['link'] === 'none' ? 'unlinked' : 'from ' . $definition['link'] ) . ')' : '' );
            $zones = ', zones ' . implode( ', ', $parts );
        }
        $draft = empty( $step['publish'] ) ? ', left as a draft' : '';
        switch ( $step['mode'] )
        {
            case 'create':
                return array( "create the layout $identifier" . ( isset( $step['layout_type'] ) ? ' of the type ' . $step['layout_type'] : '' ) . "$zones$draft$note" );
            case 'ensure':
                return array( "ensure the layout $identifier: create it when missing, change what differs$zones$draft$note" );
            case 'update':
                return array( "update the layout $identifier$zones$draft$note" );
            case 'delete':
                return array( "delete the layout $identifier and its draft$note" );
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
        $empty = array( 'layout_id' => null, 'identifier' => $step['identifier'], 'created' => false, 'changed' => false );
        if ( !self::installed() )
            return self::notInstalled( $step, 'layout', $empty );
        $identifier = trim( (string)$step['identifier'] );
        $exists = self::published( $identifier ) || self::draft( $identifier );
        switch ( $step['mode'] )
        {
            case 'create':
                if ( $exists )
                    throw new RuntimeException( "the layout $identifier is there already (mode ensure changes it)" );
                return $this->create( $step );
            case 'ensure':
                return $exists ? $this->update( $step ) : $this->create( $step );
            case 'update':
                return $exists ? $this->update( $step ) : self::missingResult( $step, "the layout $identifier", $empty );
            case 'delete':
                return $exists ? $this->delete( $identifier ) : self::missingResult( $step, "the layout $identifier", $empty );
        }
        throw new RuntimeException( 'the mode ' . $step['mode'] . ' is not one of create, update, ensure, delete' );
    }

    /**
     * Deletes the layout a create made.
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
        $identifier = trim( (string)$step['identifier'] );
        if ( !self::published( $identifier ) && !self::draft( $identifier ) )
            return array( "the layout $identifier is not there: nothing to undo" );
        $result = $this->delete( $identifier );
        return $result['lines'];
    }

    // ---- the modes ----------------------------------------------------------------------------------------------

    /**
     * @param array $step
     * @return array
     */
    protected function create( array $step )
    {
        $identifier = trim( (string)$step['identifier'] );
        $draft = self::layouts()->create( $identifier, isset( $step['name'] ) ? (string)$step['name'] : $identifier,
                                          isset( $step['layout_type'] ) ? (string)$step['layout_type'] : '', !empty( $step['shared'] ) );
        if ( !$draft instanceof expLayoutsLayout || (string)$draft->attribute( 'identifier' ) !== $identifier )
            throw new RuntimeException( "the layout $identifier could not be created" );
        $lines = array( "created the layout $identifier" . ( isset( $step['layout_type'] ) ? ' of the type ' . $step['layout_type'] : '' ) );
        foreach ( $this->applyZones( $draft, $step ) as $line )
            $lines[] = $line;
        $layout = self::finish( $draft, $step['publish'] );
        $lines[] = $step['publish'] ? "published the layout $identifier" : "the layout $identifier stays a draft";
        return self::result( $lines, array( 'layout_id' => (int)$layout->attribute( 'id' ), 'identifier' => $identifier, 'created' => true, 'changed' => true ) );
    }

    /**
     * @param array $step
     * @return array
     */
    protected function update( array $step )
    {
        $identifier = trim( (string)$step['identifier'] );
        $hadDraft = self::draft( $identifier ) !== null;
        $draft = self::draftToChange( $identifier );
        if ( !$draft )
            throw new RuntimeException( "the layout $identifier could not be opened in a draft" );
        $lines = array();
        $attributes = array();
        foreach ( array( 'name', 'layout_type' ) as $key )
            if ( isset( $step[$key] ) && (string)$draft->attribute( $key ) !== (string)$step[$key] )
            {
                $attributes[$key] = (string)$step[$key];
                $lines[] = "$key: " . $draft->attribute( $key ) . ' -> ' . $step[$key];
            }
        if ( isset( $step['shared'] ) && (int)$draft->attribute( 'shared' ) !== self::flag( $step['shared'] ) )
        {
            $attributes['shared'] = self::flag( $step['shared'] );
            $lines[] = 'shared: ' . ( $step['shared'] ? 'yes' : 'no' );
        }
        if ( $attributes )
            self::layouts()->update( (int)$draft->attribute( 'id' ), $attributes );
        if ( isset( $attributes['layout_type'] ) )
            foreach ( expLayoutsLayoutType::getZones( $attributes['layout_type'] ) as $zone )
                if ( !self::zoneOf( $draft, $zone ) )
                {
                    self::zones()->create( (int)$draft->attribute( 'id' ), $zone, self::DRAFT );
                    $lines[] = "added the zone $zone of the layout type";
                }
        foreach ( $this->applyZones( $draft, $step ) as $line )
            $lines[] = $line;
        $changed = (bool)$lines;
        if ( !$changed && !$hadDraft )
        {
            // nothing differs: the draft made for the step goes again, the published layout stays as it is
            self::layouts()->discard( (int)$draft->attribute( 'id' ) );
            $published = self::published( $identifier );
            return self::result( array( "the layout $identifier is as the step says: nothing changed" ),
                                 array( 'layout_id' => (int)$published->attribute( 'id' ), 'identifier' => $identifier, 'created' => false, 'changed' => false ) );
        }
        $layout = self::finish( $draft, $step['publish'] );
        array_unshift( $lines, "updated the layout $identifier" );
        $lines[] = $step['publish'] ? "published the layout $identifier" : "the layout $identifier stays a draft";
        return self::result( $lines, array( 'layout_id' => (int)$layout->attribute( 'id' ), 'identifier' => $identifier, 'created' => false, 'changed' => $changed ) );
    }

    /**
     * The zones of a step in a draft: missing ones added, links set or removed.
     *
     * @param expLayoutsLayout $draft
     * @param array $step
     * @return string[]
     */
    protected function applyZones( expLayoutsLayout $draft, array $step )
    {
        $lines = array();
        if ( empty( $step['zones'] ) || !is_array( $step['zones'] ) )
            return $lines;
        $draftId = (int)$draft->attribute( 'id' );
        foreach ( $step['zones'] as $identifier => $definition )
        {
            $zone = self::zoneOf( $draft, $identifier );
            if ( !$zone )
            {
                $zone = self::zones()->create( $draftId, (string)$identifier, self::DRAFT );
                $lines[] = "added the zone $identifier";
            }
            if ( !is_array( $definition ) || !isset( $definition['link'] ) )
                continue;
            $link = (string)$definition['link'];
            if ( $link === 'none' )
            {
                if ( $zone->isLinked() )
                {
                    self::zones()->unlink( (int)$zone->attribute( 'id' ) );
                    $lines[] = "the zone $identifier has blocks of its own again";
                }
                continue;
            }
            list( $sharedIdentifier, $sharedZone ) = explode( '/', $link, 2 );
            $shared = self::published( $sharedIdentifier );
            if ( !$shared )
                throw new RuntimeException( "zones.$identifier.link: the layout $sharedIdentifier is not published" );
            if ( !$shared->isShared() )
                throw new RuntimeException( "zones.$identifier.link: the layout $sharedIdentifier is not shared" );
            if ( !self::zoneOf( $shared, $sharedZone ) )
                throw new RuntimeException( "zones.$identifier.link: the layout $sharedIdentifier has no zone $sharedZone" );
            if ( $zone->isLinked() && (int)$zone->attribute( 'linked_layout_id' ) === (int)$shared->attribute( 'id' )
                 && (string)$zone->attribute( 'linked_zone_identifier' ) === $sharedZone )
                continue;
            if ( !self::zones()->link( (int)$zone->attribute( 'id' ), (int)$shared->attribute( 'id' ), $sharedZone ) )
                throw new RuntimeException( "zones.$identifier.link: the zone could not be linked to $link" );
            $lines[] = "the zone $identifier takes the blocks of $link";
        }
        return $lines;
    }

    /**
     * @param string $identifier
     * @return array
     */
    protected function delete( $identifier )
    {
        $lines = array();
        foreach ( array( self::draft( $identifier ), self::published( $identifier ) ) as $layout )
        {
            if ( !$layout )
                continue;
            $draft = (int)$layout->attribute( 'status' ) === self::DRAFT;
            if ( !self::layouts()->delete( (int)$layout->attribute( 'id' ) ) )
                throw new RuntimeException( "the layout $identifier could not be deleted" );
            $lines[] = $draft ? "deleted the draft of the layout $identifier" : "deleted the layout $identifier";
        }
        return self::result( $lines, array( 'layout_id' => null, 'identifier' => $identifier, 'created' => false, 'changed' => true ) );
    }
}
