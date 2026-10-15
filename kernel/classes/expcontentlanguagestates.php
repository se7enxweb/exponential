<?php
/**
 * File containing the expContentLanguageStates class.
 *
 * The state of each language of one content object, for the language badges of admin4 (design:parts/
 * language_state_badge.tpl): the main language (the object's initial language), a translation that exists, one that
 * is missing (a language of the site the object does not have) and, on top of an existing one, whether it is
 * outdated (last edited before the main language was).
 *
 * "Last edited" is the newest published or archived version that was edited in that language (the version's
 * initial_language_id): content/edit always edits one language and records it there. A translation counts as
 * outdated only when both it and the main language have such a version, so an object whose languages were all
 * written in one version (an import, a copy) or whose older versions were removed by the history limit is never
 * marked: no mark rather than a wrong one. All objects of a page are read with one query (lastEdited()); the badges
 * use it for the one object of the node view and its Translations tab only, the sub-items table shows main, present
 * and missing from the row it already has.
 *
 * The rules (states(), isOutdated(), showMissingInList()) work on plain arrays, so they are tested without an
 * installation: tests/tests/design/AdminLanguageStatesTest.php. Guide: doc/features/6.0/admin4-design.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentLanguageStates
{
    const MAIN = 'main';
    const PRESENT = 'present';
    const MISSING = 'missing';

    /// Up to this many languages of the site, a list (the sub-items table) shows the missing ones as well
    const LIST_MISSING_LIMIT = 6;

    /**
     * The state of every language of the site and of the object, in the site's order (the object's languages that
     * are not among the site's follow).
     *
     * @param array $siteLanguages locale => name, in priority order
     * @param array $objectLanguages locale => name, the object's translations
     * @param string $mainLocale the object's main (initial) language
     * @param array $edited locale => timestamp of the newest version edited in that language (may be incomplete)
     * @param array $creatable the locales the user may create a translation in
     * @return array list of array( 'locale', 'name', 'state', 'outdated', 'can_translate' )
     */
    public static function states( array $siteLanguages, array $objectLanguages, $mainLocale, array $edited = array(), array $creatable = array() )
    {
        $states = array();
        foreach ( $siteLanguages + $objectLanguages as $locale => $name )
        {
            $locale = (string)$locale;
            if ( $locale === (string)$mainLocale )
                $state = self::MAIN;
            else if ( array_key_exists( $locale, $objectLanguages ) )
                $state = self::PRESENT;
            else
                $state = self::MISSING;
            $states[] = array( 'locale' => $locale,
                               'name' => (string)$name,
                               'state' => $state,
                               'outdated' => $state === self::PRESENT && self::isOutdated( $locale, $mainLocale, $edited ),
                               'can_translate' => $state === self::MISSING && in_array( $locale, $creatable, true ) );
        }
        return $states;
    }

    /**
     * Whether the translation $locale was last edited before the main language: only when both times are known.
     */
    public static function isOutdated( $locale, $mainLocale, array $edited )
    {
        if ( $locale === $mainLocale || !isset( $edited[$locale], $edited[$mainLocale] ) )
            return false;
        return (int)$edited[$locale] < (int)$edited[$mainLocale];
    }

    /**
     * Whether a list shows the missing languages: only for a site with few languages, so a row stays one short line.
     */
    public static function showMissingInList( $languageCount )
    {
        return (int)$languageCount <= self::LIST_MISSING_LIMIT;
    }

    /**
     * The newest published or archived version edited in each language, for all the objects at once: one query.
     *
     * @param int[] $objectIDs
     * @param eZDBInterface|null $db
     * @return array object id => array( language id => timestamp )
     */
    public static function lastEdited( array $objectIDs, $db = null )
    {
        $objectIDs = array_values( array_unique( array_filter( array_map( 'intval', $objectIDs ) ) ) );
        if ( !$objectIDs )
            return array();
        if ( $db === null )
            $db = eZDB::instance();
        $statuses = array( eZContentObjectVersion::STATUS_PUBLISHED, eZContentObjectVersion::STATUS_ARCHIVED );

        if ( $db->databaseName() === 'mongo' )
        {
            $rows = $db->find( 'ezcontentobject_version',
                               array( 'contentobject_id' => array( $objectIDs ), 'status' => array( $statuses ) ),
                               array( 'contentobject_id' => 1, 'initial_language_id' => 1, 'modified' => 1, '_id' => 0 ) );
        }
        else
        {
            $rows = $db->arrayQuery( 'SELECT contentobject_id, initial_language_id, MAX(modified) AS modified'
                                   . ' FROM ezcontentobject_version'
                                   . ' WHERE ' . $db->generateSQLINStatement( $objectIDs, 'contentobject_id', false, true, 'int' )
                                   . ' AND ' . $db->generateSQLINStatement( $statuses, 'status', false, true, 'int' )
                                   . ' GROUP BY contentobject_id, initial_language_id' );
        }

        $edited = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $objectID = (int)$row['contentobject_id'];
            $languageID = (int)$row['initial_language_id'];
            $modified = (int)$row['modified'];
            if ( !isset( $edited[$objectID][$languageID] ) || $edited[$objectID][$languageID] < $modified )
                $edited[$objectID][$languageID] = $modified;
        }
        return $edited;
    }

    /**
     * The states of one object's languages for the current user (who may create which translation).
     *
     * @param eZContentObject $object
     * @return array see states()
     */
    public static function forObject( eZContentObject $object )
    {
        $site = array();
        foreach ( eZContentLanguage::prioritizedLanguages() as $language )
            $site[$language->attribute( 'locale' )] = $language->attribute( 'name' );

        $own = array();
        foreach ( eZContentLanguage::languagesByMask( $object->attribute( 'language_mask' ) ) as $language )
            $own[$language->attribute( 'locale' )] = $language->attribute( 'name' );

        $edited = array();
        $lastEdited = self::lastEdited( array( $object->attribute( 'id' ) ) );
        foreach ( isset( $lastEdited[$object->attribute( 'id' )] ) ? $lastEdited[$object->attribute( 'id' )] : array() as $languageID => $time )
        {
            $language = eZContentLanguage::fetch( $languageID );
            if ( $language instanceof eZContentLanguage )
                $edited[$language->attribute( 'locale' )] = $time;
        }

        $creatable = array();
        foreach ( $object->attribute( 'can_create_languages' ) as $language )
            $creatable[] = $language->attribute( 'locale' );

        return self::states( $site, $own, (string)$object->attribute( 'initial_language_code' ), $edited, $creatable );
    }

    /**
     * fetch( 'content', 'language_states', hash( 'object', $object ) ) (kernel/content/function_definition.php)
     *
     * @param eZContentObject|mixed $object
     * @return array array( 'result' => states, or an empty list without an object )
     */
    public static function fetchForObject( $object )
    {
        return array( 'result' => $object instanceof eZContentObject ? self::forObject( $object ) : array() );
    }
}
