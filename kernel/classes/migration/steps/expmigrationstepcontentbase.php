<?php
/**
 * File containing the expMigrationStepContentBase class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the step types of content and locations (content, location, content_version) share: the keys "workflows" and
 * "notify", the kernel's content operations run the way the administration runs them (eZOperationHandler, with the
 * triggers of workflow.ini only when a step asks for them), attribute values written through each datatype's
 * fromString(), and the values a step gives to "references:".
 *
 * Every change goes through the kernel's own API: eZContentClass::instantiate() and the publish operation for new
 * objects and versions, the operations move, addlocation, removelocation, delete, hide, sort, updatepriority and
 * updatemainassignment for locations. The engine's transaction is around the migration; the operations open their
 * own inner transactions, as in the administration.
 *
 * @since 6.0.15
 * @package kernel
 */
abstract class expMigrationStepContentBase extends expMigrationStepTypeBase
{
    /** The caches a content change touches. */
    const CACHES = array( 'content' );

    /**
     * The keys every content step has: workflows and notify.
     *
     * @param string[] $modes the modes the keys belong to; empty: all
     * @param bool $notify whether the step publishes (notify is a key then)
     * @return array
     */
    protected static function operationKeys( array $modes = array(), $notify = true )
    {
        $keys = array(
            'workflows' => array( 'type' => 'bool', 'default' => false,
                                  'description' => 'true: the workflows of the operation\'s triggers run, as in the administration (default: they do not)' ),
        );
        if ( $notify )
            $keys['notify'] = array( 'type' => 'bool', 'default' => false,
                                     'description' => 'true: the publish makes a notification event (default: published without notification)' );
        if ( $modes )
            foreach ( $keys as $name => $definition )
                $keys[$name]['modes'] = $modes;
        return $keys;
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        return self::CACHES;
    }

    /**
     * Runs an operation of the content module the way a view of the administration does.
     *
     * @param string $operation publish, move, addlocation ...
     * @param array $parameters
     * @param array $step the step (workflows)
     * @return array the operation's result
     * @throws RuntimeException when the operation did not run to its end (a workflow stopped or deferred it)
     */
    protected static function operation( $operation, array $parameters, array $step )
    {
        $triggers = !empty( $step['workflows'] ) && eZOperationHandler::operationIsAvailable( 'content_' . $operation );
        $result = eZOperationHandler::execute( 'content', $operation, $parameters, null, $triggers );
        if ( !is_array( $result ) )
            throw new RuntimeException( "the content operation $operation could not run" );
        if ( isset( $result['status'] ) && $result['status'] != eZModuleOperationInfo::STATUS_CONTINUE )
            throw new RuntimeException( "the content operation $operation stopped (status " . $result['status'] . '): a workflow of its trigger cancelled or deferred it' );
        return $result;
    }

    /**
     * Publishes a version through the publish operation.
     *
     * @param int $objectId
     * @param int $version
     * @param array $step the step (workflows, notify)
     * @return eZContentObject the object as it is now
     * @throws RuntimeException
     */
    protected static function publish( $objectId, $version, array $step )
    {
        self::operation( 'publish', array( 'object_id' => (int)$objectId, 'version' => (int)$version,
                                           'notify' => eZContentOperationCollection::notifyRequested( !empty( $step['notify'] ) ) ), $step );
        eZContentObject::clearCache( array( (int)$objectId ) );
        $object = eZContentObject::fetch( (int)$objectId );
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'current_version' ) !== (int)$version )
            throw new RuntimeException( "version $version of object $objectId was not published" );
        return $object;
    }

    /**
     * Writes values into attributes through their datatype's fromString().
     *
     * @param eZContentObjectAttribute[] $attributes the attributes of a version in one language
     * @param array $fields attribute identifier => value (null: left as it is)
     * @param string $where for messages
     * @return string[] the identifiers written
     * @throws RuntimeException for an identifier the class does not have
     */
    protected static function writeFields( array $attributes, array $fields, $where )
    {
        $byIdentifier = array();
        foreach ( $attributes as $attribute )
            $byIdentifier[$attribute->attribute( 'contentclass_attribute_identifier' )] = $attribute;
        $unknown = array_diff( array_map( 'strval', array_keys( $fields ) ), array_keys( $byIdentifier ) );
        if ( $unknown )
            throw new RuntimeException( "$where: the class has no attribute " . implode( ', ', $unknown ) . ' (' . implode( ', ', array_keys( $byIdentifier ) ) . ')' );
        $written = array();
        foreach ( $fields as $identifier => $value )
        {
            if ( $value === null )
                continue;
            $attribute = $byIdentifier[(string)$identifier];
            $attribute->fromString( self::fieldText( $value ) );
            $attribute->store();
            $written[] = (string)$identifier;
        }
        return $written;
    }

    /**
     * The text fromString() gets for a value of "fields:". Pure.
     *
     * @param mixed $value
     * @return string
     */
    protected static function fieldText( $value )
    {
        if ( is_bool( $value ) )
            return $value ? '1' : '0';
        return (string)$value;
    }

    /**
     * The problems of a "fields:" map. Pure.
     *
     * @param mixed $fields
     * @param string $where
     * @return string[]
     */
    protected static function fieldProblems( $fields, $where )
    {
        if ( !is_array( $fields ) )
            return expMigrationReferences::contains( $fields ) ? array() : array( "$where: a map of attribute identifier => value" );
        $errors = array();
        foreach ( $fields as $identifier => $value )
        {
            if ( !is_string( $identifier ) || !preg_match( '/^[a-z0-9_]+$/', $identifier ) )
                $errors[] = "$where: $identifier is not an attribute identifier (lower case letters, digits and _)";
            else if ( is_array( $value ) )
                $errors[] = "$where.$identifier: a value is a text, a number, true/false or null, not a list or a map";
        }
        return $errors;
    }

    /**
     * The locale of a language key, checked to be a language of the installation.
     *
     * @param mixed $language a locale, a language id, or null/empty for the default language of content
     *                        (ContentObjectLocale, or the first of the prioritized languages when that is none of the
     *                        installation's)
     * @return string
     * @throws RuntimeException
     */
    protected static function locale( $language )
    {
        if ( $language === null || $language === '' || $language === false )
        {
            $language = eZContentObject::defaultLanguage();
            if ( !eZContentLanguage::fetchByLocale( $language ) )
            {
                $top = eZContentLanguage::topPriorityLanguage();
                if ( $top instanceof eZContentLanguage )
                    $language = $top->attribute( 'locale' );
            }
        }
        $id = expMigrationIdentifier::find( 'language', $language );
        $found = $id !== null ? eZContentLanguage::fetch( $id ) : null;
        if ( !$found instanceof eZContentLanguage )
            throw new RuntimeException( 'the language ' . expMigrationStepSchema::show( $language ) . ' is not one of the languages of this installation' );
        return (string)$found->attribute( 'locale' );
    }

    /**
     * locale() for the other step types: the locale of a language key, checked to be a language of the installation.
     *
     * @param mixed $language
     * @return string
     * @throws RuntimeException
     */
    public static function localeOf( $language )
    {
        return self::locale( $language );
    }

    /**
     * Every language an object has, whether or not the siteaccess shows it (availableLanguages() lists only the
     * prioritized ones of the siteaccess).
     *
     * @param eZContentObject $object
     * @return string[] locales
     */
    protected static function locales( eZContentObject $object )
    {
        return array_map( 'strval', array_keys( $object->allLanguages() ) );
    }

    /**
     * The object a step names by "object:" or "node:".
     *
     * @param array $step
     * @return eZContentObject|null null when the identifier names nothing here
     */
    protected static function findObject( array $step )
    {
        $id = null;
        if ( isset( $step['object'] ) )
        {
            $id = expMigrationIdentifier::find( 'object', $step['object'] );
        }
        else if ( isset( $step['node'] ) )
        {
            $nodeId = expMigrationIdentifier::find( 'node', $step['node'] );
            $node = $nodeId !== null ? eZContentObjectTreeNode::fetch( $nodeId ) : null;
            $id = $node instanceof eZContentObjectTreeNode ? (int)$node->attribute( 'contentobject_id' ) : null;
        }
        if ( $id === null )
            return null;
        eZContentObject::clearCache( array( $id ) );
        $object = eZContentObject::fetch( $id );
        return $object instanceof eZContentObject ? $object : null;
    }

    /**
     * A node, fresh from the database.
     *
     * @param int $nodeId
     * @return eZContentObjectTreeNode
     * @throws RuntimeException
     */
    protected static function node( $nodeId )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$nodeId );
        if ( !$node instanceof eZContentObjectTreeNode )
            throw new RuntimeException( "no node $nodeId on this installation" );
        return $node;
    }

    /**
     * What "references:" can take of an object.
     *
     * @param eZContentObject $object
     * @return array
     */
    protected static function objectValues( eZContentObject $object )
    {
        $main = $object->attribute( 'main_node' );
        return array(
            'object_id' => (int)$object->attribute( 'id' ),
            'node_id' => $main instanceof eZContentObjectTreeNode ? (int)$main->attribute( 'node_id' ) : null,
            'remote_id' => (string)$object->attribute( 'remote_id' ),
            'version' => (int)$object->attribute( 'current_version' ),
            'name' => (string)$object->attribute( 'name' ),
            'path' => $main instanceof eZContentObjectTreeNode ? (string)$main->attribute( 'path_identification_string' ) : null,
        );
    }

    /**
     * What "references:" can take of a node.
     *
     * @param eZContentObjectTreeNode $node
     * @return array
     */
    protected static function nodeValues( eZContentObjectTreeNode $node )
    {
        return array(
            'node_id' => (int)$node->attribute( 'node_id' ),
            'object_id' => (int)$node->attribute( 'contentobject_id' ),
            'parent_node_id' => (int)$node->attribute( 'parent_node_id' ),
            'remote_id' => (string)$node->attribute( 'remote_id' ),
            'path' => (string)$node->attribute( 'path_identification_string' ),
            'hidden' => (bool)$node->attribute( 'is_hidden' ),
        );
    }

    /**
     * The id of a node of an object below a parent, or null.
     *
     * @param int $objectId
     * @param int $parentId
     * @return int|null
     */
    protected static function nodeBelow( $objectId, $parentId )
    {
        foreach ( (array)eZContentObjectTreeNode::fetchByContentObjectID( (int)$objectId ) as $node )
            if ( (int)$node->attribute( 'parent_node_id' ) === (int)$parentId )
                return (int)$node->attribute( 'node_id' );
        return null;
    }

    /**
     * An identifier for a plan line: as written. Pure.
     *
     * @param mixed $value
     * @return string
     */
    protected static function named( $value )
    {
        return is_int( $value ) || ( is_string( $value ) && ctype_digit( $value ) ) ? (string)$value : expMigrationStepSchema::show( $value );
    }
}
