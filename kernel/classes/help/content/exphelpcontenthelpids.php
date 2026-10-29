<?php
/**
 * The help_ids that were Released (doc/eng-us/specifications/6.0/help-style-guide.md, section 7: "A help_id is never
 * renamed and never reused"): the table exphelp_help_id keeps each with the object it was Released for and when.
 * Rows are only ever added; the validator (expHelpContentValidator, released_ids) reports a topic whose help_id
 * differs from the one it was Released with (help-id.renamed), a help_id now used by another object
 * (help-id.reused), and a released help_id without a topic (help-id.gone).
 *
 * Recorded when a topic is set to Released (eZContentOperationCollection::updateObjectState() through
 * expHelpContentWorkflow::afterStateAssign()), by seed-topics --release and by "exp:help:content check --fix" (which
 * also records the topics Released before the table existed). Topics made from the Markdown help (remote_id
 * help-gen-...) are not recorded: their help_id follows the page's path, and the Markdown is their source.
 *
 * Without the table (expHelpContentTables) nothing is recorded and the rules are not checked.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentHelpIds
{
    /**
     * Whether the table can be used
     *
     * @return bool
     */
    public static function available()
    {
        return class_exists( 'expHelpContentTables' ) && expHelpContentTables::available( expHelpContentTables::HELP_ID );
    }

    /**
     * Every help_id that was Released
     *
     * @return array help_id => object id
     */
    public static function registry()
    {
        if ( !self::available() )
            return array();
        $rows = eZDB::instance()->arrayQuery( 'SELECT help_id, object_id FROM ' . expHelpContentTables::HELP_ID . ' ORDER BY help_id' );
        $registry = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
            $registry[(string)$row['help_id']] = (int)$row['object_id'];
        return $registry;
    }

    /**
     * Whether a topic is one whose help_id is recorded: a valid help_id, not made from the Markdown help
     *
     * @param array $topic a row of expHelpContentRepository
     * @return bool
     */
    public static function isRecorded( array $topic )
    {
        $helpId = isset( $topic['help_id'] ) ? (string)$topic['help_id'] : '';
        if ( !expHelpContentValidator::isValidHelpId( $helpId ) )
            return false;
        return !( isset( $topic['remote_id'] ) && strpos( (string)$topic['remote_id'], expHelpContentValidator::GENERATED_PREFIX ) === 0 );
    }

    /**
     * Records a help_id as Released for an object, unless it is recorded already (for this object or, which the
     * validator reports, for another one)
     *
     * @param string $helpId
     * @param int $objectId
     * @param int|null $now
     * @return bool whether a row was added
     */
    public static function record( $helpId, $objectId, $now = null )
    {
        $helpId = (string)$helpId;
        if ( !self::available() || !expHelpContentValidator::isValidHelpId( $helpId ) || (int)$objectId <= 0 )
            return false;
        $db = eZDB::instance();
        $table = expHelpContentTables::HELP_ID;
        $escaped = $db->escapeString( $helpId );
        $rows = $db->arrayQuery( "SELECT object_id FROM $table WHERE help_id = '$escaped'" );
        if ( is_array( $rows ) && $rows )
            return false;
        $db->query( "INSERT INTO $table ( help_id, object_id, released ) VALUES ( '$escaped', " . (int)$objectId . ', ' .
                    (int)( $now === null ? time() : $now ) . ' )' );
        return true;
    }

    /**
     * Records the Released topics that are not recorded yet
     *
     * @param array $topics rows of expHelpContentRepository (help_id, object_id, state, remote_id)
     * @param bool $dryRun
     * @return string[] the help_ids added (or that would be)
     */
    public static function recordReleased( array $topics, $dryRun = false )
    {
        if ( !self::available() )
            return array();
        $registry = self::registry();
        $added = array();
        foreach ( $topics as $topic )
        {
            if ( !isset( $topic['state'] ) || $topic['state'] !== expHelpContentSchema::STATE_RELEASED || !self::isRecorded( $topic ) )
                continue;
            if ( isset( $registry[(string)$topic['help_id']] ) )
                continue;
            // a topic Released under another help_id before keeps that one: the validator reports the rename
            if ( in_array( (int)$topic['object_id'], $registry, true ) )
                continue;
            if ( $dryRun || self::record( $topic['help_id'], $topic['object_id'] ) )
            {
                $added[] = (string)$topic['help_id'];
                $registry[(string)$topic['help_id']] = (int)$topic['object_id'];
            }
        }
        return $added;
    }
}

?>
