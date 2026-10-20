<?php
/**
 * Overdue reviews of help maintained as content (doc/en/us/specifications/6.0/help-system.md, phase 7 "a list of
 * outdated topics (review date passed) in the admin dashboard"; doc/en/us/features/6.0/help-content.md, "Overdue
 * reviews"). A topic is overdue when
 *
 *   - its review date has passed (the validator's rule review-date.past, style guide section 8), whatever its state;
 *   - or it has been in Review longer than exphelp.ini [ContentSettings] ReviewMaxDays (default 14) since it last
 *     changed: the kernel keeps no date of a state change, so the last publication of the topic counts, and a topic
 *     an editor is still changing in Review is not overdue.
 *
 * The help's dashboard lists the overdue topics to those who can act on them: who may edit the topic (help editors:
 * Draft and Review) or assign it a state (help release: back to Review, Released). The command lists them all:
 * ./console exp:help:content review.
 *
 * overdue() is the rule alone, without a database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentReview
{
    const REASON_DATE = 'review-date';
    const REASON_IN_REVIEW = 'in-review';

    const DEFAULT_MAX_DAYS = 14;
    const DEFAULT_LIMIT = 10;

    /**
     * The overdue topics of a list
     *
     * @param array $topics the topics (expHelpContentRepository::load()['topics'])
     * @param int $now
     * @param int $maxDays days a topic may stay in Review without a change
     * @return array list of array( help_id, title, type, state, object_id, node_id, reason, since, days ), the
     *               longest overdue first; a topic with both reasons is listed once, with the older one
     */
    public static function overdue( array $topics, $now, $maxDays = self::DEFAULT_MAX_DAYS )
    {
        $now = (int)$now;
        $maxDays = max( 1, (int)$maxDays );
        $list = array();
        foreach ( $topics as $topic )
        {
            $reasons = array();
            $review = isset( $topic['review_date'] ) ? (int)$topic['review_date'] : 0;
            if ( $review > 0 && $review < $now )
                $reasons[self::REASON_DATE] = $review;
            $modified = isset( $topic['modified'] ) ? (int)$topic['modified'] : 0;
            if ( isset( $topic['state'] ) && $topic['state'] === expHelpContentSchema::STATE_REVIEW && $modified > 0
                 && $modified + $maxDays * 86400 < $now )
                $reasons[self::REASON_IN_REVIEW] = $modified;
            if ( !$reasons )
                continue;
            asort( $reasons );
            $reason = key( $reasons );
            $since = (int)current( $reasons );
            $list[] = array(
                'help_id' => (string)$topic['help_id'],
                'title' => (string)( $topic['title'] !== '' ? $topic['title'] : $topic['help_id'] ),
                'type' => isset( $topic['type'] ) ? (string)$topic['type'] : '',
                'state' => isset( $topic['state'] ) ? (string)$topic['state'] : '',
                'object_id' => (int)$topic['object_id'],
                'node_id' => isset( $topic['node_id'] ) ? (int)$topic['node_id'] : 0,
                'reason' => $reason,
                'since' => $since,
                'days' => (int)floor( ( $now - $since ) / 86400 ),
            );
        }
        usort( $list, function ( $a, $b ) {
            return $a['since'] <=> $b['since'] ?: strcmp( $a['help_id'], $b['help_id'] );
        } );
        return $list;
    }

    /** @return int exphelp.ini [ContentSettings] ReviewMaxDays */
    public static function maxDays()
    {
        $days = (int)expHelpContentSchema::setting( 'ReviewMaxDays', self::DEFAULT_MAX_DAYS );
        return $days > 0 ? $days : self::DEFAULT_MAX_DAYS;
    }

    /**
     * Every overdue topic of the installation (the command)
     *
     * @param int|null $now
     * @return array
     */
    public static function all( $now = null )
    {
        return self::overdue( expHelpContentRepository::instance()->load()['topics'], $now === null ? time() : $now, self::maxDays() );
    }

    /**
     * The overdue topics the current user can act on: edit the topic, or assign it a state. False when help as content
     * is not installed or the user can act on no topic at all (the dashboard then shows nothing).
     *
     * @param int $limit
     * @return array|false array( 'items' => list (at most $limit), 'count' => int, 'max_days' => int )
     */
    public static function forCurrentUser( $limit = self::DEFAULT_LIMIT )
    {
        if ( !expHelpContentProvider::isEnabled() )
            return false;
        $repository = expHelpContentRepository::instance();
        // the states of help_status: only those count as "may assign a state" here
        $helpStates = array();
        $stateNames = array();
        $group = eZContentObjectStateGroup::fetchByIdentifier( expHelpContentSchema::STATE_GROUP );
        if ( $group instanceof eZContentObjectStateGroup )
        {
            foreach ( $group->states() as $state )
            {
                $helpStates[] = (int)$state->attribute( 'id' );
                // the state's name as the site has written it, in the language of the interface where there is one
                $translation = $state->currentTranslation();
                $name = $translation ? trim( (string)$translation->attribute( 'name' ) ) : '';
                $stateNames[(string)$state->attribute( 'identifier' )] = $name !== '' ? $name : (string)$state->attribute( 'identifier' );
            }
        }
        $items = array();
        foreach ( self::all() as $item )
        {
            $object = $repository->object( $item['object_id'] );
            if ( !$object instanceof eZContentObject )
                continue;
            // the list always holds the object's current states too: only another state of help_status counts
            $assignable = array_diff( array_intersect( array_map( 'intval', (array)$object->attribute( 'allowed_assign_state_id_list' ) ), $helpStates ),
                                      array_map( 'intval', (array)$object->attribute( 'state_id_array' ) ) );
            if ( !$object->canEdit() && !$assignable )
                continue;
            $item['can_edit'] = (bool)$object->canEdit();
            $item['state_name'] = isset( $stateNames[$item['state']] ) ? $stateNames[$item['state']] : $item['state'];
            $items[] = $item;
        }
        return array( 'items' => array_slice( $items, 0, max( 1, (int)$limit ) ), 'count' => count( $items ), 'max_days' => self::maxDays() );
    }
}

?>
