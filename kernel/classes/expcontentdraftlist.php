<?php
/**
 * File containing the expContentDraftList class.
 *
 * What the drafts page (content/draft, "My drafts") lists, worked out from plain arrays: the filters of the address
 * (translation, class, age, search, order), the rows that match them, the overview figures, the choices of the
 * filters, the address part that keeps them, the age of a draft, which drafts are older than a number of days, and
 * whether a version may be removed from this page. Nothing here reads the database, the session or the current
 * user, so every rule can be tested without an installation (tests/tests/kernel/classes/expContentDraftListTest.php).
 * The view (kernel/private/classes/views/content/draft.php) reads the drafts of the current user and hands them
 * over; it removes only what removable() allows, checked again on each version at the moment it is removed.
 *
 * Guide: doc/guides/drafts-and-pending.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentDraftList
{
    /// The orders, the first is the default: last modified first, oldest modified first, by name, by class
    const SORTS = array( 'modified', 'oldest', 'name', 'class' );

    /// The ages a draft can be filtered and removed by, in days; the first is the default of "old"
    const AGES = array( 30, 7, 90, 365 );

    /// The longest search text taken, in characters
    const MAX_SEARCH_LENGTH = 100;

    /// The statuses of a draft this page removes: a draft and an untouched draft
    const DRAFT_STATUSES = array( 0, 5 );

    /**
     * The filters and order of the address: (language)/<locale>, (class)/<identifier>, (age)/<days>, (sort)/<order>,
     * and the search text (?q=). A value that is not one of them is left out.
     *
     * @param array $userParameters the view's user parameters
     * @param mixed $search the search text of the request
     * @return array( 'language' => string, 'class' => string, 'age' => int, 'sort' => string, 'search' => string )
     */
    static function filters( array $userParameters, $search = '' )
    {
        $language = isset( $userParameters['language'] ) ? (string)$userParameters['language'] : '';
        $class = isset( $userParameters['class'] ) ? (string)$userParameters['class'] : '';
        $age = isset( $userParameters['age'] ) && is_scalar( $userParameters['age'] ) && ctype_digit( (string)$userParameters['age'] )
               ? (int)$userParameters['age'] : 0;
        $sort = isset( $userParameters['sort'] ) ? (string)$userParameters['sort'] : '';
        return array( 'language' => preg_match( '/^[a-z]{2,3}-[A-Z]{2}(@[A-Za-z0-9]+)?$/', $language ) ? $language : '',
                      'class' => preg_match( '/^[a-z0-9_]{1,64}$/i', $class ) ? $class : '',
                      'age' => in_array( $age, self::AGES, true ) ? $age : 0,
                      'sort' => in_array( $sort, self::SORTS, true ) ? $sort : self::SORTS[0],
                      'search' => self::searchText( $search ) );
    }

    /**
     * @param mixed $raw
     * @return string the search text, trimmed, at most MAX_SEARCH_LENGTH characters, without control characters
     */
    static function searchText( $raw )
    {
        if ( !is_string( $raw ) )
            return '';
        $text = trim( preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', $raw ) );
        return function_exists( 'mb_substr' ) ? mb_substr( $text, 0, self::MAX_SEARCH_LENGTH ) : substr( $text, 0, self::MAX_SEARCH_LENGTH );
    }

    /**
     * The address part that keeps the filters and order (not the search, which is a query parameter).
     *
     * @param array $filters from filters()
     * @param array $change filters to set differently in this address
     * @return string such as "/(class)/folder/(sort)/name"
     */
    static function suffix( array $filters, array $change = array() )
    {
        $filters = array_merge( $filters, $change );
        $suffix = '';
        if ( !empty( $filters['language'] ) )
            $suffix .= '/(language)/' . $filters['language'];
        if ( !empty( $filters['class'] ) )
            $suffix .= '/(class)/' . $filters['class'];
        if ( !empty( $filters['age'] ) )
            $suffix .= '/(age)/' . (int)$filters['age'];
        if ( !empty( $filters['sort'] ) && $filters['sort'] !== self::SORTS[0] )
            $suffix .= '/(sort)/' . $filters['sort'];
        return $suffix;
    }

    /**
     * The address parts of the filter links, each filter set to each value it offers, the others kept; "all" is the
     * filter left out.
     *
     * @param array $filters
     * @param array $choices from choices()
     * @return array
     */
    static function links( array $filters, array $choices )
    {
        $links = array( 'language' => array( 'all' => self::suffix( $filters, array( 'language' => '' ) ) ),
                        'class' => array( 'all' => self::suffix( $filters, array( 'class' => '' ) ) ),
                        'age' => array( 'all' => self::suffix( $filters, array( 'age' => 0 ) ) ),
                        'sort' => array() );
        foreach ( array_keys( $choices['languages'] ) as $locale )
            $links['language'][$locale] = self::suffix( $filters, array( 'language' => $locale ) );
        foreach ( array_keys( $choices['classes'] ) as $identifier )
            $links['class'][$identifier] = self::suffix( $filters, array( 'class' => $identifier ) );
        foreach ( self::AGES as $days )
            $links['age'][$days] = self::suffix( $filters, array( 'age' => $days ) );
        foreach ( self::SORTS as $sort )
            $links['sort'][$sort] = self::suffix( $filters, array( 'sort' => $sort ) );
        return $links;
    }

    /**
     * One row of the list from the fields of a draft.
     *
     * @param array $draft id, version, object_id, name, class_identifier, class_name, language, language_name,
     *                     created, modified, status, is_new (the object was never published), node_id (its main
     *                     node, 0 for none), location (the name of the parent it is or will be under)
     * @param int $now the time the page is drawn
     * @return array the same fields as plain values, with age_days
     */
    static function row( array $draft, $now )
    {
        $int = function ( $key ) use ( $draft ) { return isset( $draft[$key] ) ? (int)$draft[$key] : 0; };
        $str = function ( $key ) use ( $draft ) { return isset( $draft[$key] ) ? (string)$draft[$key] : ''; };
        $modified = $int( 'modified' );
        return array( 'id' => $int( 'id' ), 'version' => $int( 'version' ), 'object_id' => $int( 'object_id' ),
                      'name' => $str( 'name' ), 'class_identifier' => $str( 'class_identifier' ), 'class_name' => $str( 'class_name' ),
                      'language' => $str( 'language' ), 'language_name' => $str( 'language_name' ),
                      'created' => $int( 'created' ), 'modified' => $modified, 'status' => $int( 'status' ),
                      'is_new' => !empty( $draft['is_new'] ), 'node_id' => $int( 'node_id' ), 'location' => $str( 'location' ),
                      'age_days' => self::ageDays( $modified, $now ) );
    }

    /**
     * @param int $time
     * @param int $now
     * @return int whole days from $time to $now, 0 for a time in the future
     */
    static function ageDays( $time, $now )
    {
        return max( 0, intdiv( (int)$now - (int)$time, 86400 ) );
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
        $search = isset( $filters['search'] ) ? $filters['search'] : '';
        $needle = function_exists( 'mb_strtolower' ) ? mb_strtolower( $search ) : strtolower( $search );
        $out = array();
        foreach ( $rows as $row )
        {
            if ( !empty( $filters['language'] ) && $row['language'] !== $filters['language'] )
                continue;
            if ( !empty( $filters['class'] ) && $row['class_identifier'] !== $filters['class'] )
                continue;
            if ( !empty( $filters['age'] ) && $row['age_days'] < (int)$filters['age'] )
                continue;
            if ( $needle !== '' )
            {
                $hay = $row['name'] . ' ' . $row['location'] . ' ' . $row['class_name'];
                $hay = function_exists( 'mb_strtolower' ) ? mb_strtolower( $hay ) : strtolower( $hay );
                if ( strpos( $hay, $needle ) === false )
                    continue;
            }
            $out[] = $row;
        }
        $sort = isset( $filters['sort'] ) ? $filters['sort'] : self::SORTS[0];
        usort( $out, function ( $a, $b ) use ( $sort )
        {
            if ( $sort === 'name' || $sort === 'class' )
            {
                $first = $sort === 'class' ? strnatcasecmp( $a['class_name'], $b['class_name'] ) : 0;
                if ( $first === 0 )
                    $first = strnatcasecmp( $a['name'], $b['name'] );
                return $first !== 0 ? $first : $b['modified'] <=> $a['modified'];
            }
            $order = $sort === 'oldest' ? $a['modified'] <=> $b['modified'] : $b['modified'] <=> $a['modified'];
            return $order !== 0 ? $order : $b['id'] <=> $a['id'];
        } );
        return $out;
    }

    /**
     * The figures above the list.
     *
     * @param array $rows every draft of the user, from row()
     * @param int $oldDays
     * @return array( 'total' => int, 'new_objects' => int, 'old' => int, 'old_days' => int,
     *                'languages' => array( locale => count ), 'classes' => array( identifier => count ) )
     */
    static function overview( array $rows, $oldDays )
    {
        $languages = array();
        $classes = array();
        $old = 0;
        $new = 0;
        foreach ( $rows as $row )
        {
            $languages[$row['language']] = isset( $languages[$row['language']] ) ? $languages[$row['language']] + 1 : 1;
            $classes[$row['class_identifier']] = isset( $classes[$row['class_identifier']] ) ? $classes[$row['class_identifier']] + 1 : 1;
            if ( $row['age_days'] >= (int)$oldDays )
                $old++;
            if ( $row['is_new'] )
                $new++;
        }
        arsort( $languages );
        arsort( $classes );
        return array( 'total' => count( $rows ), 'new_objects' => $new, 'old' => $old, 'old_days' => (int)$oldDays,
                      'languages' => $languages, 'classes' => $classes );
    }

    /**
     * What the filters offer: the translations (locale => name) and classes (identifier => name) of the drafts, by name.
     *
     * @param array $rows
     * @return array( 'languages' => array, 'classes' => array )
     */
    static function choices( array $rows )
    {
        $languages = array();
        $classes = array();
        foreach ( $rows as $row )
        {
            if ( $row['language'] !== '' )
                $languages[$row['language']] = $row['language_name'] !== '' ? $row['language_name'] : $row['language'];
            if ( $row['class_identifier'] !== '' )
                $classes[$row['class_identifier']] = $row['class_name'] !== '' ? $row['class_name'] : $row['class_identifier'];
        }
        asort( $languages, SORT_NATURAL | SORT_FLAG_CASE );
        asort( $classes, SORT_NATURAL | SORT_FLAG_CASE );
        return array( 'languages' => $languages, 'classes' => $classes );
    }

    /**
     * @param array $rows
     * @param int $days
     * @return int[] the version ids of the drafts not modified for $days days or more
     */
    static function olderThan( array $rows, $days )
    {
        $ids = array();
        foreach ( $rows as $row )
        {
            if ( (int)$days > 0 && $row['age_days'] >= (int)$days )
                $ids[] = $row['id'];
        }
        return $ids;
    }

    /**
     * @param mixed $raw the age a removal of old drafts was asked for
     * @return int one of AGES, or 0
     */
    static function age( $raw )
    {
        return is_scalar( $raw ) && ctype_digit( (string)$raw ) && in_array( (int)$raw, self::AGES, true ) ? (int)$raw : 0;
    }

    /**
     * Whether this page may remove a version for the user: their own draft (or untouched draft), nothing else. The
     * view asks it for every version at the moment it removes it, with the version's fields as they are then.
     *
     * @param array $version 'creator_id', 'status'
     * @param int $userID the current user, not the anonymous user
     * @param int $anonymousID
     * @return bool
     */
    static function removable( array $version, $userID, $anonymousID )
    {
        return (int)$userID > 0 && (int)$userID !== (int)$anonymousID &&
               isset( $version['creator_id'] ) && (int)$version['creator_id'] === (int)$userID &&
               isset( $version['status'] ) && in_array( (int)$version['status'], self::DRAFT_STATUSES, true );
    }
}

?>
