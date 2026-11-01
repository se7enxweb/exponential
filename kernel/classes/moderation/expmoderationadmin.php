<?php
/**
 * File containing the expModerationAdmin class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the pages of Setup > Recent content (the module moderation) need besides the list and the actions: the policies
 * of the current user, the choices of the filter and action forms, the dashboard block's counts (the fetch function
 * moderation/summary).
 *
 * @since 6.0.15
 * @package kernel
 */
class expModerationAdmin
{
    /**
     * Whether a user has a function of the module moderation (read, act, personaldata).
     *
     * @param string $function
     * @param eZUser|null $user
     * @return bool
     */
    public static function canDo( $function, $user = null )
    {
        $user = $user instanceof eZUser ? $user : eZUser::currentUser();
        $access = $user->hasAccessTo( 'moderation', (string)$function );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    /**
     * The fetch function moderation/summary, for the dashboard block "moderation": objects held for review, new objects
     * of today. Empty for a user without moderation/read; never throws.
     *
     * @return array array( 'result' => array( held, new_today, today, ready, problem ) )
     */
    public static function fetchSummary()
    {
        if ( !self::canDo( 'read' ) )
            return array( 'result' => array() );
        $summary = array( 'held' => 0, 'new_today' => 0, 'today' => date( 'Y-m-d' ), 'ready' => false, 'problem' => '' );
        try
        {
            $list = new expModerationList( null, false );
            $summary = $list->counts() + $summary;
            $summary['ready'] = class_exists( 'expReviewHold' ) && expReviewHold::group() !== null;
        }
        catch ( Throwable $e )
        {
            $summary['problem'] = $e->getMessage();
        }
        return array( 'result' => $summary );
    }

    /** @return array the classes for the filter: list of array( identifier, name ) */
    public static function classChoices()
    {
        $out = array();
        foreach ( expModerationList::classNames() as $class )
            $out[] = $class;
        return $out;
    }

    /** @return array the sections: list of array( id, name ) */
    public static function sectionChoices()
    {
        $out = array();
        foreach ( expModerationList::sectionNames() as $id => $name )
            $out[] = array( 'id' => $id, 'name' => $name );
        usort( $out, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ); } );
        return $out;
    }

    /**
     * The object states an action may assign: every state of the groups an editor sees (not the internal ez_lock),
     * by group. The review hold has its own actions and is left out.
     *
     * @return array list of array( group (name), states => list of array( id, name ) )
     */
    public static function stateChoices()
    {
        $out = array();
        foreach ( (array)eZContentObjectStateGroup::fetchByOffset( false, false ) as $group )
        {
            $identifier = (string)$group->attribute( 'identifier' );
            if ( strpos( $identifier, 'ez' ) === 0 || $identifier === ( class_exists( 'expReviewHold' ) ? expReviewHold::GROUP : 'review_hold' ) )
                continue;
            $states = array();
            foreach ( (array)$group->states() as $state )
                $states[] = array( 'id' => (int)$state->attribute( 'id' ), 'name' => (string)$state->attribute( 'current_translation' )->attribute( 'name' ) );
            if ( $states )
                $out[] = array( 'group' => (string)$group->attribute( 'current_translation' )->attribute( 'name' ), 'states' => $states );
        }
        return $out;
    }

    /** @return string[] the siteaccesses of the installation */
    public static function siteaccessChoices()
    {
        $list = (array)eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        sort( $list );
        return array_values( array_unique( array_filter( array_map( 'strval', $list ) ) ) );
    }

    /**
     * The creator of the filter, typed as a login or an id.
     *
     * @param string $typed
     * @return int the user's id, 0 when nobody
     */
    public static function creatorID( $typed )
    {
        $typed = trim( (string)$typed );
        if ( $typed === '' )
            return 0;
        if ( ctype_digit( $typed ) )
            return (int)$typed;
        $user = eZUser::fetchByName( $typed );
        return $user instanceof eZUser ? (int)$user->attribute( 'contentobject_id' ) : 0;
    }
}
