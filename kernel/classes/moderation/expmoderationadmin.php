<?php
/**
 * File containing the expModerationAdmin class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the pages of Dashboard > Recent content (the module moderation) need besides the list and the actions: the policies
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

    /**
     * The rows of the list as HTML (the tbody of moderation/recent.tpl). Drawn here, not in the template: a page of
     * 1000 rows with a dozen template operators each took seconds to render (more with the debug output on), this
     * takes milliseconds. The markup is the template's (shared components only, no CSS of its own); every text is
     * translated once per page, every value escaped.
     *
     * @param array $rows expModerationList::decorate()
     * @param bool $canAct a checkbox per row (moderation/act)
     * @param bool $personal the "Submitted from" column (moderation/personaldata)
     * @param callable|null $tr function( text, args ): string; default ezpI18n in design/admin/moderation
     * @param callable|null $url function( path ): string, the address of a path; default eZURI::transformURI
     * @param callable|null $date function( timestamp ): string; default the locale's short date and time
     * @return string
     */
    public static function rowsHtml( array $rows, $canAct, $personal, $tr = null, $url = null, $date = null )
    {
        $cache = array();
        $tr = $tr ?: function ( $text, array $args = array() ) use ( &$cache ) {
            if ( !$args )
                return isset( $cache[$text] ) ? $cache[$text] : ( $cache[$text] = ezpI18n::tr( 'design/admin/moderation', $text ) );
            return ezpI18n::tr( 'design/admin/moderation', $text, null, $args );
        };
        $url = $url ?: function ( $path ) { eZURI::transformURI( $path ); return $path; };
        $locale = $date ? null : eZLocale::instance();
        $date = $date ?: function ( $ts ) use ( $locale ) { return $locale->formatShortDateTime( (int)$ts ); };
        $e = function ( $s ) { return htmlspecialchars( (string)$s, ENT_QUOTES, 'UTF-8' ); };
        $muted = function ( $s ) use ( $e ) { return '<span class="a4-muted">' . $e( $s ) . '</span>'; };
        $out = array();
        foreach ( $rows as $row )
        {
            $h = '<tr>';
            if ( $canAct )
                $h .= '<td><input type="checkbox" name="ObjectIDList[]" value="' . (int)$row['id'] . '" aria-label="' . $e( $tr( 'Select %name', array( '%name' => $row['name'] ) ) ) . '" /></td>';
            $h .= '<td class="a4-nowrap">' . ( $row['modified'] ? $e( $date( $row['modified'] ) ) : $muted( '-' ) ) . '</td>';
            $h .= '<td class="a4-nowrap">' . ( $row['published'] ? $e( $date( $row['published'] ) ) : $muted( $tr( 'not yet' ) ) ) . '</td>';
            $name = $row['name'] !== '' ? $e( $row['name'] ) : $muted( $tr( '(no name)' ) );
            $h .= '<td>' . ( $row['node_id'] ? '<a href="' . $e( $url( '/content/view/full/' . (int)$row['node_id'] ) ) . '">' . $name . '</a>' : $name )
                . '<br /><span class="a4-meta">' . $e( $tr( 'ID %id', array( '%id' => $row['id'] ) ) ) . '</span></td>';
            $h .= '<td>' . $e( $row['class_name'] ) . '</td>';
            $badges = array();
            if ( $row['status'] === 'published' )
                $badges[] = array( 'is-ok', $tr( 'Published' ) );
            elseif ( $row['status'] === 'archived' )
                $badges[] = array( 'is-bad', $tr( 'In the trash' ) );
            else
                $badges[] = array( 'is-info', $tr( 'Draft' ) );
            if ( $row['pending'] )
                $badges[] = array( 'is-warn', $tr( 'Pending' ) );
            if ( $row['hidden'] )
                $badges[] = array( '', $tr( 'Hidden' ) );
            elseif ( $row['invisible'] )
                $badges[] = array( '', $tr( 'Hidden by its parent' ) );
            if ( $row['held'] )
                $badges[] = array( 'is-warn', $row['auto'] ? $tr( 'Held: new submission' ) : $tr( 'Held for review' ), $row['hold_reason'] );
            $h .= '<td><ul class="a4-badges">';
            foreach ( $badges as $b )
                $h .= '<li class="a4-badge' . ( $b[0] !== '' ? ' ' . $b[0] : '' ) . '"' . ( !empty( $b[2] ) ? ' title="' . $e( $b[2] ) . '"' : '' ) . '>' . $e( $b[1] ) . '</li>';
            $h .= '</ul>';
            if ( $row['held'] && $row['hold_reason'] !== '' )
            {
                $reason = function_exists( 'mb_strlen' ) && mb_strlen( $row['hold_reason'], 'UTF-8' ) > 80 ? mb_substr( $row['hold_reason'], 0, 77, 'UTF-8' ) . '...' : $row['hold_reason'];
                $h .= '<span class="a4-meta">' . $e( $reason ) . ( $row['hold_by'] !== '' ? ' (' . $e( $row['hold_by'] ) . ')' : '' ) . '</span>';
            }
            $h .= '</td><td>';
            if ( $row['version_status'] === 'pending' )
                $h .= $e( $tr( 'Version %version pending', array( '%version' => $row['version'] ) ) );
            elseif ( $row['version_status'] === 'draft' )
                $h .= $e( $row['status'] === 'published' ? $tr( 'Version %version in progress', array( '%version' => $row['version'] ) )
                                                         : $tr( 'Draft, version %version', array( '%version' => $row['version'] ) ) );
            else
                $h .= $e( $tr( 'Version %version published', array( '%version' => $row['current_version'] ) ) );
            if ( $row['scheduled'] )
                $h .= '<br /><span class="a4-meta">' . $e( $tr( 'Scheduled for %date', array( '%date' => $date( $row['scheduled'] ) ) ) ) . '</span>';
            $h .= '</td><td>' . $e( $row['section_name'] ) . '</td><td>';
            if ( $row['owner_node_id'] )
                $h .= '<a href="' . $e( $url( '/content/view/full/' . (int)$row['owner_node_id'] ) ) . '">' . $e( $row['owner_name'] ) . '</a>';
            else
                $h .= $row['owner_name'] !== '' ? $e( $row['owner_name'] ) : $muted( $row['owner_id'] );
            $h .= '</td>';
            if ( $personal )
            {
                $h .= '<td>';
                if ( $row['recorded'] )
                {
                    $h .= $row['ip'] !== '' ? '<code class="a4-code">' . $e( $row['ip'] ) . '</code>' : $muted( $tr( 'no address' ) );
                    if ( $row['ua'] !== '' )
                        $h .= '<br /><span class="a4-meta" title="' . $e( $row['ua'] ) . '">' . $e( function_exists( 'mb_substr' ) && mb_strlen( $row['ua'], 'UTF-8' ) > 60 ? mb_substr( $row['ua'], 0, 57, 'UTF-8' ) . '...' : $row['ua'] ) . '</span>';
                    if ( $row['siteaccess'] !== '' )
                        $h .= '<br /><span class="a4-meta">' . $e( $row['siteaccess'] ) . '</span>';
                }
                else
                    $h .= $muted( $tr( 'not recorded' ) );
                $h .= '</td>';
            }
            $out[] = $h . '</tr>';
        }
        return implode( "\n", $out );
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
