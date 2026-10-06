<?php
/**
 * File containing the expContentHistoryList class.
 *
 * What the versions page of an object (content/history) lists, worked out from plain arrays: the filters of the
 * address (status, translation, creator, order), the rows that match them in that order, the overview figures, the
 * creators and translations the filters offer, the address part that keeps the filters while paging, and which
 * actions the user has on a version and why the others are not offered. Nothing here reads the database, the
 * session or the current user, so every rule can be tested without an installation
 * (tests/tests/kernel/classes/expContentHistoryListTest.php). The view
 * (kernel/private/classes/views/content/history.php) reads the versions and the user's rights and hands them over;
 * who may open the page and whose content they see stays decided there (History::canOpen(),
 * History::canSeeVersionContent()).
 *
 * Guide: doc/bc/6.0/draft-edit-access.md ("The versions of such an object")
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentHistoryList
{
    /// The version statuses by their number, as the address and the templates name them
    const STATUSES = array( 0 => 'draft', 1 => 'published', 2 => 'pending', 3 => 'archived', 4 => 'rejected',
                            5 => 'untouched', 6 => 'repeat', 7 => 'queued' );

    /// The orders, the first is the default: the newest version first, the oldest first, the last modified first
    const SORTS = array( 'newest', 'oldest', 'modified' );

    /// The statuses whose versions the page offers to remove (as History::isRemovableStatus())
    const REMOVABLE = array( 0, 3, 4, 5 );

    /// The statuses a user edits in place when the version is theirs; others are copied to a new draft first
    const EDITABLE = array( 0, 5 );

    /**
     * The filters and order of the address: (status)/<name>, (language)/<locale>, (creator)/<id>, (sort)/<order>.
     * A value that is not one of them is left out, so a made-up address shows the whole list.
     *
     * @param array $userParameters the view's user parameters
     * @return array( 'status' => string, 'language' => string, 'creator' => int, 'sort' => string )
     */
    static function filters( array $userParameters )
    {
        $status = isset( $userParameters['status'] ) ? (string)$userParameters['status'] : '';
        $language = isset( $userParameters['language'] ) ? (string)$userParameters['language'] : '';
        $creator = isset( $userParameters['creator'] ) && is_scalar( $userParameters['creator'] ) && ctype_digit( (string)$userParameters['creator'] )
                   ? (int)$userParameters['creator'] : 0;
        $sort = isset( $userParameters['sort'] ) ? (string)$userParameters['sort'] : '';
        return array( 'status' => in_array( $status, self::STATUSES, true ) ? $status : '',
                      'language' => preg_match( '/^[a-z]{2,3}-[A-Z]{2}(@[A-Za-z0-9]+)?$/', $language ) ? $language : '',
                      'creator' => $creator,
                      'sort' => in_array( $sort, self::SORTS, true ) ? $sort : self::SORTS[0] );
    }

    /**
     * The address part that keeps the filters and order, for the pager, the forms and the links of the page.
     *
     * @param array $filters from filters()
     * @param array $change filters to set differently in this address, such as array( 'status' => '' )
     * @return string such as "/(status)/archived/(sort)/oldest", or "" for the whole list in the default order
     */
    static function suffix( array $filters, array $change = array() )
    {
        $filters = array_merge( $filters, $change );
        $suffix = '';
        if ( !empty( $filters['status'] ) )
            $suffix .= '/(status)/' . $filters['status'];
        if ( !empty( $filters['language'] ) )
            $suffix .= '/(language)/' . $filters['language'];
        if ( !empty( $filters['creator'] ) )
            $suffix .= '/(creator)/' . (int)$filters['creator'];
        if ( !empty( $filters['sort'] ) && $filters['sort'] !== self::SORTS[0] )
            $suffix .= '/(sort)/' . $filters['sort'];
        return $suffix;
    }

    /**
     * The address parts of the filter links: for each filter, the part with that filter set to each value it
     * offers, the other filters kept. The value "all" is the filter left out.
     *
     * @param array $filters from filters()
     * @param array $statuses status name => count, from overview()
     * @param array $choices from choices()
     * @return array( 'status' => array( 'all' => ..., name => ... ), 'language' => ..., 'creator' => ..., 'sort' => ... )
     */
    static function links( array $filters, array $statuses, array $choices )
    {
        $links = array( 'status' => array( 'all' => self::suffix( $filters, array( 'status' => '' ) ) ),
                        'language' => array( 'all' => self::suffix( $filters, array( 'language' => '' ) ) ),
                        'creator' => array( 'all' => self::suffix( $filters, array( 'creator' => 0 ) ) ),
                        'sort' => array() );
        foreach ( array_keys( $statuses ) as $name )
            $links['status'][$name] = self::suffix( $filters, array( 'status' => $name ) );
        foreach ( array_keys( isset( $choices['languages'] ) ? $choices['languages'] : array() ) as $locale )
            $links['language'][$locale] = self::suffix( $filters, array( 'language' => $locale ) );
        foreach ( array_keys( isset( $choices['creators'] ) ? $choices['creators'] : array() ) as $creatorID )
            $links['creator'][$creatorID] = self::suffix( $filters, array( 'creator' => (int)$creatorID ) );
        foreach ( self::SORTS as $sort )
            $links['sort'][$sort] = self::suffix( $filters, array( 'sort' => $sort ) );
        return $links;
    }

    /**
     * One row of the list from the fields of a version.
     *
     * @param array $version version, id, status, language (locale of the initial language), language_name,
     *                       creator_id, creator_name, created, modified
     * @return array the same fields with status_name, as plain values
     */
    static function row( array $version )
    {
        $status = isset( $version['status'] ) ? (int)$version['status'] : 0;
        return array( 'id' => isset( $version['id'] ) ? (int)$version['id'] : 0,
                      'version' => isset( $version['version'] ) ? (int)$version['version'] : 0,
                      'status' => $status,
                      'status_name' => isset( self::STATUSES[$status] ) ? self::STATUSES[$status] : 'draft',
                      'language' => isset( $version['language'] ) ? (string)$version['language'] : '',
                      'language_name' => isset( $version['language_name'] ) ? (string)$version['language_name'] : '',
                      'creator_id' => isset( $version['creator_id'] ) ? (int)$version['creator_id'] : 0,
                      'creator_name' => isset( $version['creator_name'] ) ? (string)$version['creator_name'] : '',
                      'created' => isset( $version['created'] ) ? (int)$version['created'] : 0,
                      'modified' => isset( $version['modified'] ) ? (int)$version['modified'] : 0 );
    }

    /**
     * The rows that match the filters, in the order asked for.
     *
     * @param array $rows from row()
     * @param array $filters from filters()
     * @return array
     */
    static function select( array $rows, array $filters )
    {
        $out = array();
        foreach ( $rows as $row )
        {
            if ( !empty( $filters['status'] ) && $row['status_name'] !== $filters['status'] )
                continue;
            if ( !empty( $filters['language'] ) && $row['language'] !== $filters['language'] )
                continue;
            if ( !empty( $filters['creator'] ) && $row['creator_id'] !== (int)$filters['creator'] )
                continue;
            $out[] = $row;
        }
        $sort = isset( $filters['sort'] ) ? $filters['sort'] : self::SORTS[0];
        usort( $out, function ( $a, $b ) use ( $sort )
        {
            if ( $sort === 'modified' && $a['modified'] !== $b['modified'] )
                return $b['modified'] <=> $a['modified'];
            return $sort === 'oldest' ? $a['version'] <=> $b['version'] : $b['version'] <=> $a['version'];
        } );
        return $out;
    }

    /**
     * The figures above the list: every version, the number in each status that has any, the translations the
     * versions were made in, and the drafts of the user (to edit or to remove).
     *
     * @param array $rows every row of the object, from row()
     * @param int $userID
     * @return array( 'total' => int, 'statuses' => array( name => count ), 'translations' => int, 'own_drafts' => int )
     */
    static function overview( array $rows, $userID )
    {
        $statuses = array();
        $languages = array();
        $own = 0;
        foreach ( $rows as $row )
        {
            $statuses[$row['status_name']] = isset( $statuses[$row['status_name']] ) ? $statuses[$row['status_name']] + 1 : 1;
            if ( $row['language'] !== '' )
                $languages[$row['language']] = true;
            if ( in_array( $row['status'], self::EDITABLE, true ) && $row['creator_id'] === (int)$userID )
                $own++;
        }
        // in the order of the statuses, so the figures do not move between objects
        $ordered = array();
        foreach ( self::STATUSES as $name )
        {
            if ( isset( $statuses[$name] ) )
                $ordered[$name] = $statuses[$name];
        }
        return array( 'total' => count( $rows ), 'statuses' => $ordered, 'translations' => count( $languages ), 'own_drafts' => $own );
    }

    /**
     * What the filters offer: the creators (id => name, by name) and the translations (locale => name, by name) of
     * the versions.
     *
     * @param array $rows every row of the object
     * @return array( 'creators' => array, 'languages' => array )
     */
    static function choices( array $rows )
    {
        $creators = array();
        $languages = array();
        foreach ( $rows as $row )
        {
            if ( $row['creator_id'] > 0 )
                $creators[$row['creator_id']] = $row['creator_name'] !== '' ? $row['creator_name'] : '#' . $row['creator_id'];
            if ( $row['language'] !== '' )
                $languages[$row['language']] = $row['language_name'] !== '' ? $row['language_name'] : $row['language'];
        }
        asort( $creators, SORT_NATURAL | SORT_FLAG_CASE );
        asort( $languages, SORT_NATURAL | SORT_FLAG_CASE );
        return array( 'creators' => $creators, 'languages' => $languages );
    }

    /**
     * The actions the user has on one version, and for each one not offered the reason, so the page can say it.
     *
     * @param array $row from row(), with 'can_versionread' (content/versionread allows the version), 'can_remove'
     *                   (content/versionremove allows it) and 'languages' (its translations, locale => name)
     * @param array $context 'can_edit' (the user may edit the object), 'content_versions' (the numbers whose
     *                       content the user may see), 'edit_languages' (locales the user may edit), 'user_id'
     * @return array name => array( 'allowed' => bool, 'reason' => string ) for view, edit, copy, compare and remove,
     *               and 'copy_languages' => the translations the copy may start in (locale => name)
     */
    static function actions( array $row, array $context )
    {
        $canEdit = !empty( $context['can_edit'] );
        $seen = in_array( $row['version'], isset( $context['content_versions'] ) ? array_map( 'intval', $context['content_versions'] ) : array(), true );
        $editLanguages = isset( $context['edit_languages'] ) ? $context['edit_languages'] : array();
        $own = $row['creator_id'] === (int)( isset( $context['user_id'] ) ? $context['user_id'] : 0 );
        $languages = isset( $row['languages'] ) && $row['languages'] ? $row['languages'] : array( $row['language'] => $row['language_name'] );
        $copyLanguages = array();
        foreach ( $languages as $locale => $name )
        {
            if ( in_array( $locale, $editLanguages, true ) )
                $copyLanguages[$locale] = $name;
        }

        $out = array();
        $out['view'] = !empty( $row['can_versionread'] ) ? self::yes() : self::no( 'versionread' );
        if ( !$canEdit )
            $out['edit'] = self::no( 'no_edit' );
        elseif ( !in_array( $row['status'], self::EDITABLE, true ) )
            $out['edit'] = self::no( 'not_draft' );
        elseif ( !$own )
            $out['edit'] = self::no( 'not_own' );
        else
            $out['edit'] = self::yes();

        if ( !$canEdit )
            $out['copy'] = self::no( 'no_edit' );
        elseif ( $row['status'] === 5 )
            $out['copy'] = self::no( 'untouched' );
        elseif ( !$seen )
            $out['copy'] = self::no( 'not_readable' );
        elseif ( !$copyLanguages )
            $out['copy'] = self::no( 'no_language' );
        else
            $out['copy'] = self::yes();

        $out['compare'] = $seen ? self::yes() : self::no( 'not_readable' );

        if ( $row['status'] === 1 )
            $out['remove'] = self::no( 'published' );
        elseif ( !in_array( $row['status'], self::REMOVABLE, true ) )
            $out['remove'] = self::no( 'workflow' );
        elseif ( !$canEdit || empty( $row['can_remove'] ) )
            $out['remove'] = self::no( 'no_remove' );
        else
            $out['remove'] = self::yes();

        $out['copy_languages'] = $copyLanguages;
        return $out;
    }

    private static function yes()
    {
        return array( 'allowed' => true, 'reason' => '' );
    }

    private static function no( $reason )
    {
        return array( 'allowed' => false, 'reason' => $reason );
    }
}

?>
