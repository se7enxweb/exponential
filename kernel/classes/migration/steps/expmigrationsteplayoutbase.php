<?php
/**
 * File containing the expMigrationStepLayoutBase class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the step types of Exponential Layouts (layout, layout_block, layout_rule) share: whether the extensions
 * explayouts and explayouts_core are installed, their services, and the draft a change is made in.
 *
 * Every change goes through the services of explayouts_core (expLayoutsCoreLayoutService, ...ZoneService,
 * ...BlockService, ...RuleService), the way the layout editor makes it: a layout is changed in its draft (made from
 * the published layout when there is none), and the draft is published at the end of the step (publish: false leaves
 * it a draft for an editor to look at). A layout is named by its identifier, a block by its name in a zone of the
 * layout, a rule by its layout and its targets. Without the extensions a step fails with a message that says so
 * (missing: skip lets it pass).
 *
 * @since 6.0.15
 * @package kernel
 */
abstract class expMigrationStepLayoutBase extends expMigrationStepStructureBase
{
    /** The status of a draft and of a published layout, zone and block. */
    const DRAFT = 1;
    const PUBLISHED = 2;

    /** @return bool whether Exponential Layouts (explayouts and explayouts_core) is installed */
    public static function installed()
    {
        return class_exists( 'expLayoutsLayout' ) && class_exists( 'expLayoutsCoreLayoutService' );
    }

    /**
     * The result of a step without Exponential Layouts: a skip, or an exception.
     *
     * @param array $step
     * @param string $type
     * @param array $values
     * @return array
     * @throws RuntimeException unless missing: skip
     */
    protected static function notInstalled( array $step, $type, array $values )
    {
        if ( isset( $step['missing'] ) && $step['missing'] === 'skip' )
            return self::result( array( 'Exponential Layouts (explayouts) is not installed: skipped (missing: skip)' ), $values );
        throw new RuntimeException( "the step type $type needs Exponential Layouts (the extensions explayouts and explayouts_core), which is not installed here" );
    }

    /** @return string a note for a plan when the extensions are missing */
    protected static function planNote()
    {
        return self::installed() ? '' : ' (Exponential Layouts is not installed here)';
    }

    /** @return expLayoutsCoreLayoutService */
    protected static function layouts()
    {
        return new expLayoutsCoreLayoutService();
    }

    /** @return expLayoutsCoreZoneService */
    protected static function zones()
    {
        return new expLayoutsCoreZoneService();
    }

    /** @return expLayoutsCoreBlockService */
    protected static function blocks()
    {
        return new expLayoutsCoreBlockService();
    }

    /** @return expLayoutsCoreRuleService */
    protected static function rules()
    {
        return new expLayoutsCoreRuleService();
    }

    /**
     * The published layout of an identifier, or null.
     *
     * @param string $identifier
     * @return expLayoutsLayout|null
     */
    protected static function published( $identifier )
    {
        $layout = expLayoutsLayout::fetchByIdentifier( trim( (string)$identifier ), self::PUBLISHED );
        return $layout instanceof expLayoutsLayout ? $layout : null;
    }

    /**
     * The draft of an identifier, or null.
     *
     * @param string $identifier
     * @return expLayoutsLayout|null
     */
    protected static function draft( $identifier )
    {
        $layout = expLayoutsLayout::fetchByIdentifier( trim( (string)$identifier ), self::DRAFT );
        return $layout instanceof expLayoutsLayout ? $layout : null;
    }

    /**
     * The draft to change a layout in: its draft, or a new one made from the published layout.
     *
     * @param string $identifier
     * @return expLayoutsLayout|null null when the layout does not exist
     */
    protected static function draftToChange( $identifier )
    {
        $draft = self::draft( $identifier );
        if ( $draft )
            return $draft;
        $published = self::published( $identifier );
        if ( !$published )
            return null;
        $draft = self::layouts()->createDraft( (int)$published->attribute( 'id' ) );
        return $draft instanceof expLayoutsLayout ? $draft : null;
    }

    /**
     * Publishes a draft, or leaves it (publish: false).
     *
     * @param expLayoutsLayout $draft
     * @param bool $publish
     * @return expLayoutsLayout the published layout, or the draft
     */
    protected static function finish( expLayoutsLayout $draft, $publish )
    {
        if ( !$publish )
            return $draft;
        $published = self::layouts()->publish( (int)$draft->attribute( 'id' ) );
        if ( !$published instanceof expLayoutsLayout )
            throw new RuntimeException( 'the layout ' . $draft->attribute( 'identifier' ) . ' could not be published' );
        return $published;
    }

    /**
     * The zone of a layout row (draft or published), or null.
     *
     * @param expLayoutsLayout $layout
     * @param string $zone
     * @return expLayoutsZone|null
     */
    protected static function zoneOf( expLayoutsLayout $layout, $zone )
    {
        $row = expLayoutsZone::fetchByLayoutAndIdentifier( (int)$layout->attribute( 'id' ), trim( (string)$zone ), (int)$layout->attribute( 'status' ) );
        return $row instanceof expLayoutsZone ? $row : null;
    }

    /**
     * The top-level blocks of a zone with a name, in their order.
     *
     * @param expLayoutsZone $zone
     * @param string $name
     * @return expLayoutsBlock[]
     */
    protected static function blocksNamed( expLayoutsZone $zone, $name )
    {
        $found = array();
        foreach ( (array)expLayoutsBlock::fetchByZone( (int)$zone->attribute( 'id' ), (int)$zone->attribute( 'status' ) ) as $block )
            if ( $block instanceof expLayoutsBlock && (int)$block->attribute( 'parent_id' ) === 0 && (string)$block->attribute( 'name' ) === (string)$name )
                $found[] = $block;
        return $found;
    }

    /** @return bool whether an identifier of a layout is well formed: letters, digits, _ and - */
    protected static function validIdentifier( $identifier )
    {
        return is_scalar( $identifier ) && preg_match( '/^[A-Za-z0-9_][A-Za-z0-9_-]*$/', (string)$identifier ) === 1;
    }

    /**
     * The caches of every layout step: the content view cache (pages show layouts) and template-block (the layout
     * blocks of the pagelayout). The resolver's own cache is cleared by the services.
     *
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        return array( 'content', 'template-block' );
    }
}
