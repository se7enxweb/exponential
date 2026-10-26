<?php
/**
 * "Was this helpful?" on each help topic (doc/eng-us/specifications/6.0/help-system.md, phase 7; ISO/IEC/IEEE 26514,
 * usability of the information): the answers, kept in the table exphelp_feedback.
 *
 * The privacy decision, made once for every site: an answer is **yes or no, and for a no one reason from a fixed list**
 * (REASONS), with the topic, its language and the **day**. Nothing about who answered: no user id, no address, no
 * session id, no free text (free text invites personal data and needs moderation). So the table holds no personal
 * data, and the answers are counted, never traced back. One answer per topic and session is counted (the session
 * remembers which topics it answered, not the other way round). Answers older than FeedbackKeepDays (730) are removed
 * by prune() (the help's cronjob part and "exp:help:content feedback --prune").
 *
 * Only Released topics a reader may see can be answered (the view exphelp/feedback asks the provider). Off with
 * exphelp.ini [ContentSettings] Feedback=disabled, and off without the table (expHelpContentTables).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentFeedback
{
    /** The reasons a reader can give for a no; stored as these keys, shown translated */
    const REASONS = array( 'outdated', 'unclear', 'incomplete', 'wrong', 'other' );

    const DEFAULT_KEEP_DAYS = 730;

    /** The session variable with the topics this session answered (object ids) */
    const SESSION_KEY = 'exphelp_feedback_answered';

    /** At most this many answers per session (a script that posts in a loop counts once per topic anyway) */
    const SESSION_LIMIT = 50;

    /**
     * Whether answers are taken: Feedback=enabled and the table exists
     *
     * @return bool
     */
    public static function isEnabled()
    {
        return (string)expHelpContentSchema::setting( 'Feedback', 'enabled' ) === 'enabled'
               && class_exists( 'expHelpContentTables' ) && expHelpContentTables::available( expHelpContentTables::FEEDBACK );
    }

    /** @return int the days answers are kept (FeedbackKeepDays, at least 1) */
    public static function keepDays()
    {
        return max( 1, (int)expHelpContentSchema::setting( 'FeedbackKeepDays', self::DEFAULT_KEEP_DAYS ) );
    }

    /**
     * The day of a time, as stored (00:00 UTC)
     *
     * @param int $time
     * @return int
     */
    public static function day( $time )
    {
        return (int)( floor( (int)$time / 86400 ) * 86400 );
    }

    /**
     * An answer as it is stored, or null when it is not one (pure, for the view and the tests)
     *
     * @param mixed $helpful 'yes', 'no', 1, 0
     * @param mixed $reason one of REASONS for a no; ignored for a yes
     * @return array|null array( helpful (1|0), reason ('' or a key of REASONS) )
     */
    public static function normalise( $helpful, $reason )
    {
        if ( $helpful === 'yes' || $helpful === '1' || $helpful === 1 || $helpful === true )
            return array( 'helpful' => 1, 'reason' => '' );
        if ( $helpful === 'no' || $helpful === '0' || $helpful === 0 || $helpful === false )
        {
            $reason = is_string( $reason ) ? strtolower( trim( $reason ) ) : '';
            return array( 'helpful' => 0, 'reason' => in_array( $reason, self::REASONS, true ) ? $reason : '' );
        }
        return null;
    }

    /**
     * Stores one answer
     *
     * @param array $topic a topic of expHelpContentProvider (object_id, help_id, language)
     * @param array $answer normalise()
     * @param int|null $now
     * @return bool
     */
    public static function record( array $topic, array $answer, $now = null )
    {
        if ( !self::isEnabled() || empty( $topic['object_id'] ) )
            return false;
        $db = eZDB::instance();
        $table = expHelpContentTables::FEEDBACK;
        $now = $now === null ? time() : (int)$now;
        $db->query( "INSERT INTO $table ( object_id, help_id, language, helpful, reason, created ) VALUES ( " .
                    (int)$topic['object_id'] . ", '" . $db->escapeString( substr( (string)( isset( $topic['help_id'] ) ? $topic['help_id'] : '' ), 0, 80 ) ) . "', '" .
                    $db->escapeString( substr( (string)( isset( $topic['language'] ) ? $topic['language'] : '' ), 0, 20 ) ) . "', " .
                    (int)$answer['helpful'] . ", '" . $db->escapeString( (string)$answer['reason'] ) . "', " . self::day( $now ) . ' )' );
        // now and then, the old answers go (the cronjob does it too)
        if ( mt_rand( 1, 200 ) === 1 )
            self::prune( $now );
        return true;
    }

    /**
     * Whether this session answered a topic already, or answered too many
     *
     * @param int $objectId
     * @return bool
     */
    public static function answeredInSession( $objectId )
    {
        $http = eZHTTPTool::instance();
        $answered = $http->hasSessionVariable( self::SESSION_KEY, false ) ? (array)$http->sessionVariable( self::SESSION_KEY ) : array();
        return in_array( (int)$objectId, array_map( 'intval', $answered ), true ) || count( $answered ) >= self::SESSION_LIMIT;
    }

    /**
     * Notes in the session that it answered a topic
     *
     * @param int $objectId
     */
    public static function rememberInSession( $objectId )
    {
        $http = eZHTTPTool::instance();
        $answered = $http->hasSessionVariable( self::SESSION_KEY, false ) ? (array)$http->sessionVariable( self::SESSION_KEY ) : array();
        $answered[] = (int)$objectId;
        $http->setSessionVariable( self::SESSION_KEY, array_values( array_unique( array_map( 'intval', $answered ) ) ) );
    }

    /**
     * The answers counted per topic
     *
     * @param int[]|null $objectIds null: every topic
     * @param int $since only answers from this day on (0: all kept)
     * @return array object id => array( yes, no, reasons (reason => count), help_id )
     */
    public static function summary( ?array $objectIds = null, $since = 0 )
    {
        if ( !self::isEnabled() )
            return array();
        $db = eZDB::instance();
        $table = expHelpContentTables::FEEDBACK;
        $where = array();
        if ( $objectIds !== null )
        {
            if ( !$objectIds )
                return array();
            $where[] = $db->generateSQLINStatement( array_map( 'intval', $objectIds ), 'object_id', false, true, 'int' );
        }
        if ( (int)$since > 0 )
            $where[] = 'created >= ' . self::day( (int)$since );
        $condition = $where ? ' WHERE ' . implode( ' AND ', $where ) : '';
        $rows = $db->arrayQuery( "SELECT object_id, helpful, reason, MAX(help_id) AS help_id, COUNT(*) AS c FROM $table$condition GROUP BY object_id, helpful, reason" );
        return self::summarise( is_array( $rows ) ? $rows : array() );
    }

    /**
     * The grouped rows as the summary (pure, for the tests)
     *
     * @param array $rows array( object_id, helpful, reason, help_id, c )
     * @return array
     */
    public static function summarise( array $rows )
    {
        $summary = array();
        foreach ( $rows as $row )
        {
            $id = (int)$row['object_id'];
            if ( !isset( $summary[$id] ) )
                $summary[$id] = array( 'yes' => 0, 'no' => 0, 'reasons' => array(), 'help_id' => (string)$row['help_id'] );
            $count = (int)$row['c'];
            if ( (int)$row['helpful'] === 1 )
                $summary[$id]['yes'] += $count;
            else
            {
                $summary[$id]['no'] += $count;
                $reason = (string)$row['reason'] !== '' ? (string)$row['reason'] : 'none';
                $summary[$id]['reasons'][$reason] = ( isset( $summary[$id]['reasons'][$reason] ) ? $summary[$id]['reasons'][$reason] : 0 ) + $count;
            }
        }
        foreach ( $summary as &$item )
            arsort( $item['reasons'] );
        unset( $item );
        return $summary;
    }

    /**
     * The topics readers found least helpful: at least $minimum answers and more no than yes, the worst share first
     *
     * @param array $summary summary()
     * @param int $minimum
     * @return array object id => summary entry with 'share' (no / all, 0..1)
     */
    public static function unhelpful( array $summary, $minimum = 3 )
    {
        $list = array();
        foreach ( $summary as $id => $item )
        {
            $all = $item['yes'] + $item['no'];
            if ( $all < max( 1, (int)$minimum ) || $item['no'] <= $item['yes'] )
                continue;
            $list[$id] = $item + array( 'share' => $item['no'] / $all );
        }
        uasort( $list, function ( $a, $b ) {
            return array( $b['share'], $b['no'] ) <=> array( $a['share'], $a['no'] );
        } );
        return $list;
    }

    /**
     * Removes the answers older than keepDays()
     *
     * @param int|null $now
     * @param bool $dryRun only count them
     * @return int how many (or would be)
     */
    public static function prune( $now = null, $dryRun = false )
    {
        if ( !class_exists( 'expHelpContentTables' ) || !expHelpContentTables::available( expHelpContentTables::FEEDBACK ) )
            return 0;
        $db = eZDB::instance();
        $table = expHelpContentTables::FEEDBACK;
        $limit = self::day( ( $now === null ? time() : (int)$now ) - self::keepDays() * 86400 );
        $rows = $db->arrayQuery( "SELECT COUNT(*) AS c FROM $table WHERE created < $limit" );
        $count = is_array( $rows ) && isset( $rows[0]['c'] ) ? (int)$rows[0]['c'] : 0;
        if ( $count > 0 && !$dryRun )
            $db->query( "DELETE FROM $table WHERE created < $limit" );
        return $count;
    }
}

?>
