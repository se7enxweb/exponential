<?php
/**
 * File containing the expMigrationRecordingSource interface.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the recording of changes reads from the installation while it turns an audit event into a recorded event
 * (expMigrationRecordingEvent::normalise()): the stable names of what an event is about (remote ids, identifiers,
 * names) and the state of it after the change, the values a migration step needs to make the same change on another
 * installation. expMigrationRecordingResolver reads them from the database; the tests give a fake.
 *
 * Every method returns null when the thing does not exist (any more). A reference ("ref") is an identifier in the form
 * of expMigrationIdentifier: remote:<remote id> for a node or an object.
 *
 * @since 6.0.15
 * @package kernel
 */
interface expMigrationRecordingSource
{
    /**
     * @param int $nodeId
     * @return string|null remote:<the node's remote id>
     */
    public function nodeRef( $nodeId );

    /**
     * @param int $objectId
     * @return string|null remote:<the object's remote id>
     */
    public function objectRef( $objectId );

    /**
     * A location after the change.
     *
     * @param int $nodeId
     * @return array|null remote_id, object (ref), parent (ref), hidden, priority, sort_field, sort_order (names), main
     */
    public function node( $nodeId );

    /**
     * A content object after the change, its published version: what a content step creates or updates.
     *
     * @param int $objectId
     * @return array|null remote_id, class, section (identifier), always_available, initial_language, main_language,
     *                    is_user (an object of a user class), parent (ref of the main location's parent), locations
     *                    (node() of each), states (group => state identifier), languages (locale => attribute
     *                    identifier => array( datatype, value )), user_groups (refs, for users)
     */
    public function object( $objectId );

    /**
     * A content class as defined after the change.
     *
     * @param int|string $class id or identifier
     * @return array|null identifier, names, descriptions, contentobject_name, url_alias_name, is_container,
     *                    always_available, sort_field, sort_order, groups (names), attributes (identifier => definition)
     */
    public function contentClass( $class );

    /**
     * A role with its policies, the limitations in stable forms.
     *
     * @param int|string $role id or name
     * @return array|null name, policies (list of module, function, limitations), notes (values left as they were)
     */
    public function role( $role );

    /**
     * @param int $sectionId
     * @return array|null identifier, name, navigation_part, component
     */
    public function section( $sectionId );

    /**
     * An object state group with its states.
     *
     * @param int|string $group id or identifier
     * @return array|null identifier, names, states (identifier => names), default_language
     */
    public function stateGroup( $group );

    /**
     * The identifier of a section.
     *
     * @param int $sectionId
     * @return string|null
     */
    public function sectionIdentifier( $sectionId );

    /**
     * Whether a content object is a user (an object of a class with an ezuser attribute).
     *
     * @param int $objectId
     * @return bool
     */
    public function isUser( $objectId );
}
