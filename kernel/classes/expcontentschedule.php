<?php
/**
 * File containing the expContentSchedule class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Publishing a draft at a date and taking a page down at a date, the kernel's way, for every place that schedules:
 * the administration (content/edit of the date fields), the website sidebar (expwt) and anything else.
 *
 *   publish at    a date field (ezdatetime or ezdate) of the class that a "Wait until date" event (ezwaituntildate)
 *                 lists, in a workflow connected to content/publish before: the draft carries the date, publishing it
 *                 starts the workflow, the event defers it until the date and the workflow cronjob (workflow.php,
 *                 the frequent part) publishes it then. Every other event of the workflow (an approval) applies.
 *   take down at  the date field content.ini [HideSettings] HideDateAttributeList[<class>] names, for a page below a
 *                 node of RootNodeList[]: the hide cronjob (hide.php) hides it once the date has come.
 *
 * A class without such a field cannot be scheduled that way; forClass() tells which of the two a class has and
 * why not. setDates() writes the dates into a draft (every language of it); the caller then publishes the draft
 * through the content/publish operation as for "Publish now".
 *
 * The planning functions (publishAttribute(), takeDownAttribute(), dayStart(), plan()) work on plain arrays and are
 * tested without a database (tests/tests/kernel/classes/expContentScheduleTest.php).
 *
 * @since 6.0.15
 * @package kernel
 */
class expContentSchedule
{
    /** The datatypes the wait-until-date event and the hide cronjob read a date from */
    const DATATYPES = array( 'ezdatetime', 'ezdate' );

    const WAIT_EVENT = 'ezwaituntildate';

    /**
     * The date field of a class the wait-until-date event waits for.
     *
     * @param array $classAttributes each: id, identifier, datatype
     * @param int[] $waitIds the class attribute ids that wait-until-date events list (waitAttributeIds())
     * @return array|null identifier, datatype, precision (datetime or date); null when the class has none
     */
    public static function publishAttribute( array $classAttributes, array $waitIds )
    {
        $waitIds = array_map( 'intval', $waitIds );
        foreach ( $classAttributes as $a )
        {
            if ( in_array( (int)$a['id'], $waitIds, true ) && in_array( $a['datatype'], self::DATATYPES, true ) )
                return self::describe( $a );
        }
        return null;
    }

    /**
     * The date field of a class the hide cronjob reads, for a page whose parent has the path $parentPath.
     *
     * @param array $classAttributes each: id, identifier, datatype
     * @param string $classIdentifier
     * @param array $hideMap content.ini [HideSettings] HideDateAttributeList: class identifier => attribute identifier
     * @param int[] $rootNodeIds content.ini [HideSettings] RootNodeList
     * @param string $parentPath the path string of the page's parent (/1/2/59/), the page is below a root when a root
     *                           is in it
     * @return array|null identifier, datatype, precision; null when the class has none or the page is not below a root
     */
    public static function takeDownAttribute( array $classAttributes, $classIdentifier, array $hideMap, array $rootNodeIds, $parentPath )
    {
        if ( !isset( $hideMap[$classIdentifier] ) || $hideMap[$classIdentifier] === '' )
            return null;
        $below = false;
        foreach ( $rootNodeIds as $root )
            $below = $below || ( (int)$root > 0 && strpos( (string)$parentPath, '/' . (int)$root . '/' ) !== false );
        if ( !$below )
            return null;
        foreach ( $classAttributes as $a )
        {
            if ( $a['identifier'] === $hideMap[$classIdentifier] && in_array( $a['datatype'], self::DATATYPES, true ) )
                return self::describe( $a );
        }
        return null;
    }

    /** @return array identifier, datatype, precision */
    private static function describe( array $a )
    {
        return array( 'identifier' => (string)$a['identifier'], 'datatype' => (string)$a['datatype'],
                      'precision' => $a['datatype'] === 'ezdate' ? 'date' : 'datetime' );
    }

    /**
     * @param int $timestamp
     * @return int the start of its day (server time), as an ezdate field stores a date
     */
    public static function dayStart( $timestamp )
    {
        return (int)mktime( 0, 0, 0, (int)date( 'n', $timestamp ), (int)date( 'j', $timestamp ), (int)date( 'Y', $timestamp ) );
    }

    /**
     * What to write into a draft for "publish at $at" and "take down at $until".
     *
     *   $at       null: publish now. A publish date the draft carries in the future (copied from an earlier version or
     *             set in the administration) is set to now, so that "now" is now; any other is left as it is.
     *             A time: the publish date field gets it (in the past: published at once, as the event does).
     *   $until    null: the take-down date is left as it is. 0: it is cleared. A time: the field gets it.
     *
     * @param array|null $publish publishAttribute() of the class
     * @param array|null $takeDown takeDownAttribute() of the class
     * @param int|null $at
     * @param int|null $until
     * @param int $now
     * @param int $currentPublish the publish date the draft carries now (0: none)
     * @return array writes (identifier => timestamp), deferred (bool: the publish waits for the date),
     *               error (null, or no_publish_date, no_take_down_date, take_down_before_publish)
     */
    public static function plan( $publish, $takeDown, $at, $until, $now, $currentPublish = 0 )
    {
        $out = array( 'writes' => array(), 'deferred' => false, 'error' => null );
        if ( $at !== null && $at > $now && $publish === null )
            return array( 'error' => 'no_publish_date' ) + $out;
        if ( $until !== null && $until > 0 && $takeDown === null )
            return array( 'error' => 'no_take_down_date' ) + $out;
        if ( $until !== null && $until > 0 && $until <= max( $now, (int)$at ) )
            return array( 'error' => 'take_down_before_publish' ) + $out;
        if ( $publish !== null )
        {
            if ( $at !== null )
                $out['writes'][$publish['identifier']] = $publish['precision'] === 'date' ? self::dayStart( $at ) : (int)$at;
            else if ( (int)$currentPublish > $now )
                $out['writes'][$publish['identifier']] = $publish['precision'] === 'date' ? self::dayStart( $now ) : (int)$now;
            $out['deferred'] = isset( $out['writes'][$publish['identifier']] ) && $out['writes'][$publish['identifier']] > $now;
        }
        if ( $takeDown !== null && $until !== null )
            $out['writes'][$takeDown['identifier']] = $until > 0 && $takeDown['precision'] === 'date' ? self::dayStart( $until ) : (int)$until;
        return $out;
    }

    // ---------------------------------------------------------------------------------------------- installation

    /**
     * The class attribute ids the wait-until-date events of the workflows connected to content/publish before list.
     * Only events directly in such a workflow count: one in a workflow run by a multiplexer may not run for every
     * object, and a date that is not waited for would publish at once.
     *
     * @return int[]
     */
    public static function waitAttributeIds()
    {
        $ids = array();
        foreach ( (array)eZTrigger::fetchList( array( 'module' => 'content', 'function' => 'publish', 'connectType' => 'b' ) ) as $trigger )
        {
            foreach ( (array)eZWorkflow::fetchEventsByWorkflowID( (int)$trigger->attribute( 'workflow_id' ) ) as $event )
            {
                $type = preg_replace( '/^event_/', '', (string)$event->attribute( 'workflow_type_string' ) );
                if ( $type !== self::WAIT_EVENT )
                    continue;
                foreach ( (array)eZWaitUntilDateValue::fetchAllElements( (int)$event->attribute( 'id' ), (int)$event->attribute( 'version' ) ) as $entry )
                    $ids[] = (int)$entry->attribute( 'contentclass_attribute_id' );
            }
        }
        return array_values( array_unique( $ids ) );
    }

    /** @return array each: id, identifier, datatype */
    public static function classAttributes( eZContentClass $class )
    {
        $out = array();
        foreach ( (array)$class->fetchAttributes() as $a )
            $out[] = array( 'id' => (int)$a->attribute( 'id' ), 'identifier' => (string)$a->attribute( 'identifier' ),
                            'datatype' => (string)$a->attribute( 'data_type_string' ) );
        return $out;
    }

    /**
     * Which of the two an object can be scheduled with.
     *
     * @param eZContentObject $object
     * @param string|null $parentPath the path string of the parent (null: of the object's main node, or of the parent
     *                                its draft is to be placed below)
     * @return array publish, takeDown (each publishAttribute() / takeDownAttribute(), or null), why (why not, for each
     *               that is null: no_wait_event, no_hide_setting)
     */
    public static function forObject( eZContentObject $object, $parentPath = null )
    {
        $class = $object->contentClass();
        $attributes = $class instanceof eZContentClass ? self::classAttributes( $class ) : array();
        if ( $parentPath === null )
            $parentPath = self::parentPathOf( $object );
        $ini = eZINI::instance( 'content.ini' );
        $publish = self::publishAttribute( $attributes, self::waitAttributeIds() );
        $takeDown = self::takeDownAttribute( $attributes, (string)$object->attribute( 'class_identifier' ),
                                             (array)$ini->variable( 'HideSettings', 'HideDateAttributeList' ),
                                             (array)$ini->variable( 'HideSettings', 'RootNodeList' ), (string)$parentPath );
        $why = array();
        if ( $publish === null )
            $why['publish'] = 'no_wait_event';
        if ( $takeDown === null )
            $why['takeDown'] = 'no_hide_setting';
        return array( 'publish' => $publish, 'takeDown' => $takeDown, 'why' => $why );
    }

    /** @return string the path string of the object's main node's parent, or of the parent of its first placement */
    public static function parentPathOf( eZContentObject $object )
    {
        $node = $object->attribute( 'main_node' );
        if ( $node instanceof eZContentObjectTreeNode )
        {
            $parent = $node->attribute( 'parent' );
            return $parent instanceof eZContentObjectTreeNode ? (string)$parent->attribute( 'path_string' ) : '';
        }
        foreach ( (array)eZNodeAssignment::fetchForObject( (int)$object->attribute( 'id' ), (int)$object->attribute( 'current_version' ) ) as $assignment )
        {
            $parent = eZContentObjectTreeNode::fetch( (int)$assignment->attribute( 'parent_node' ) );
            if ( $parent instanceof eZContentObjectTreeNode )
                return (string)$parent->attribute( 'path_string' );
        }
        return '';
    }

    /**
     * @param eZContentObjectVersion $version
     * @param string $identifier
     * @return int the date the field carries in the version (0: none)
     */
    public static function dateOf( eZContentObjectVersion $version, $identifier )
    {
        foreach ( (array)$version->contentObjectAttributes() as $attribute )
        {
            if ( $attribute->attribute( 'contentclass_attribute_identifier' ) === $identifier )
                return (int)$attribute->attribute( 'data_int' );
        }
        return 0;
    }

    /**
     * Writes dates into the fields of a version, in every language it has (date fields are rarely translated, and
     * the event reads the language it finds first).
     *
     * @param eZContentObjectVersion $version
     * @param array $writes identifier => timestamp (0: cleared)
     * @return int fields written
     */
    public static function setDates( eZContentObjectVersion $version, array $writes )
    {
        if ( !$writes )
            return 0;
        $count = 0;
        $db = eZDB::instance();
        $db->begin();
        foreach ( (array)$version->translationList( false, false ) as $language )
        {
            foreach ( (array)$version->contentObjectAttributes( $language ) as $attribute )
            {
                $identifier = (string)$attribute->attribute( 'contentclass_attribute_identifier' );
                if ( !array_key_exists( $identifier, $writes ) || !in_array( $attribute->attribute( 'data_type_string' ), self::DATATYPES, true ) )
                    continue;
                $attribute->setAttribute( 'data_int', (int)$writes[$identifier] > 0 ? (int)$writes[$identifier] : null );
                $attribute->store();
                $count++;
            }
        }
        $db->commit();
        return $count;
    }
}
