<?php
/**
 * The per-user choice of the admin subitems list: visible columns in order and the chosen preset
 * per navigation part, the page size, and the user's own presets.
 *
 * Stored as compact JSON in one eZPreferences value, name "admin_subitems_table". The
 * ezpreferences.value column is longtext (MySQL) / text (PostgreSQL), but eZPreferences writes
 * it as a SQL string literal, and Oracle refuses literals over 4000 bytes, so the JSON is kept
 * within MAX_BYTES = 4000 on every engine; a choice that does not fit is refused, not cut.
 *
 * Stored shape:
 *   {"v":1,"parts":{"<navigation part>|*":{"visible":[keys],"preset":id|null}},
 *    "page_size":n|null,"presets":{"<id>":{"name":"...","columns":[keys]}}}
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsPreference
{
    const NAME = 'admin_subitems_table';
    const MAX_BYTES = 4000;
    const MAX_PRESETS = 20;
    const MAX_PRESET_NAME = 60;
    const MAX_PAGE_SIZE = 500;
    /** The part key used when a choice was saved without a parent node. */
    const ANY_PART = '*';

    /**
     * An empty preference.
     *
     * @return array
     */
    public static function blank()
    {
        return array( 'v' => 1, 'parts' => array(), 'page_size' => null, 'presets' => array() );
    }

    /**
     * Decodes a stored value; anything broken gives blank().
     *
     * @param mixed $raw
     * @return array
     */
    public static function decode( $raw )
    {
        $data = is_string( $raw ) && $raw !== '' ? json_decode( $raw, true ) : null;
        if ( !is_array( $data ) )
            return self::blank();
        $pref = self::blank();
        if ( isset( $data['parts'] ) && is_array( $data['parts'] ) )
            $pref['parts'] = $data['parts'];
        if ( isset( $data['page_size'] ) && is_int( $data['page_size'] ) )
            $pref['page_size'] = $data['page_size'];
        if ( isset( $data['presets'] ) && is_array( $data['presets'] ) )
            $pref['presets'] = $data['presets'];
        return $pref;
    }

    /**
     * The current user's stored preference.
     *
     * @return array
     */
    public static function load()
    {
        return self::decode( eZPreferences::value( self::NAME ) );
    }

    /**
     * Stores a preference for the current user.
     *
     * @param array $pref a validated preference
     * @return bool
     * @throws InvalidArgumentException when it is over MAX_BYTES
     */
    public static function store( array $pref )
    {
        $json = self::encode( $pref );
        if ( strlen( $json ) > self::MAX_BYTES )
            throw new InvalidArgumentException( 'The table options are too large to save (' . strlen( $json ) . ' of ' . self::MAX_BYTES . ' bytes); remove a preset' );
        return eZPreferences::setValue( self::NAME, $json );
    }

    /**
     * @param array $pref
     * @return string compact JSON
     */
    public static function encode( array $pref )
    {
        $out = array(
            'v' => 1,
            'parts' => (object)$pref['parts'],
            'page_size' => $pref['page_size'],
            'presets' => (object)$pref['presets'],
        );
        return json_encode( $out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
    }

    /**
     * The navigation part a parent's subitems list belongs to (its section's), as the list's
     * template finds it.
     *
     * @param eZContentObjectTreeNode|null $parent
     * @return string
     */
    public static function navigationPart( ?eZContentObjectTreeNode $parent = null )
    {
        if ( $parent === null )
            return self::ANY_PART;
        $object = $parent->object();
        if ( $object instanceof eZContentObject )
        {
            $section = eZSection::fetch( $object->attribute( 'section_id' ) );
            if ( $section instanceof eZSection && $section->attribute( 'navigation_part_identifier' ) )
                return (string)$section->attribute( 'navigation_part_identifier' );
        }
        return self::ANY_PART;
    }

    /**
     * What the columns server function returns as "preference" for a parent.
     *
     * @param array $pref the stored preference
     * @param string $part the parent's navigation part
     * @param string[] $defaults the parent's default columns (used when nothing is saved)
     * @return array array( 'visible', 'preset', 'page_size', 'saved' )
     */
    public static function forPart( array $pref, $part, array $defaults )
    {
        $entry = null;
        if ( isset( $pref['parts'][$part] ) && is_array( $pref['parts'][$part] ) )
            $entry = $pref['parts'][$part];
        else if ( isset( $pref['parts'][self::ANY_PART] ) && is_array( $pref['parts'][self::ANY_PART] ) )
            $entry = $pref['parts'][self::ANY_PART];

        return array(
            'visible' => $entry !== null && isset( $entry['visible'] ) && is_array( $entry['visible'] ) ? array_values( $entry['visible'] ) : $defaults,
            'preset' => $entry !== null && isset( $entry['preset'] ) && is_string( $entry['preset'] ) ? $entry['preset'] : null,
            'page_size' => isset( $pref['page_size'] ) && is_int( $pref['page_size'] ) ? $pref['page_size'] : null,
            'saved' => $entry !== null,
        );
    }

    /**
     * The user's own presets as the columns server function lists them.
     *
     * @param array $pref
     * @return array list of array( id, name, columns, source => 'user' )
     */
    public static function userPresets( array $pref )
    {
        $list = array();
        foreach ( $pref['presets'] as $id => $preset )
        {
            if ( !is_array( $preset ) )
                continue;
            $list[] = array( 'id' => (string)$id, 'name' => (string)( $preset['name'] ?? $id ),
                             'columns' => array_values( (array)( $preset['columns'] ?? array() ) ), 'source' => 'user' );
        }
        return $list;
    }

    /**
     * Merges a submitted choice into the stored preference, validated against the registry.
     *
     * Input (decoded JSON): visible (keys in order), preset (id or null), page_size (int),
     * presets ({id: {name, columns}} — the user's full set of own presets; when the key is absent
     * the stored presets are kept). Unknown column keys are dropped, a preset must exist, preset
     * ids must not be INI ones, names are plain text.
     *
     * @param array $stored the stored preference
     * @param array $input the submitted choice
     * @param expSubitemsColumnRegistry $registry
     * @param string $part the navigation part the visible columns are saved for
     * @return array the new preference
     * @throws InvalidArgumentException on input that cannot be saved at all
     */
    public static function merge( array $stored, array $input, expSubitemsColumnRegistry $registry, $part )
    {
        if ( !is_string( $part ) || !preg_match( '/^([a-z0-9_]{1,64}|\*)$/', $part ) )
            throw new InvalidArgumentException( 'Not a navigation part' );

        $pref = $stored + self::blank();
        $iniPresets = $registry->presets();

        if ( array_key_exists( 'presets', $input ) )
        {
            if ( !is_array( $input['presets'] ) )
                throw new InvalidArgumentException( 'presets must be an object' );
            $presets = array();
            foreach ( $input['presets'] as $id => $preset )
            {
                $id = (string)$id;
                if ( !expSubitemsColumnRegistry::isValidPresetID( $id ) || isset( $iniPresets[$id] ) || !is_array( $preset ) )
                    continue;
                $name = isset( $preset['name'] ) && is_scalar( $preset['name'] ) ? trim( strip_tags( (string)$preset['name'] ) ) : '';
                if ( $name === '' )
                    $name = $id;
                if ( function_exists( 'mb_substr' ) )
                    $name = mb_substr( $name, 0, self::MAX_PRESET_NAME, 'UTF-8' );
                $presets[$id] = array( 'name' => $name, 'columns' => self::validKeys( $preset['columns'] ?? array(), $registry ) );
                if ( count( $presets ) >= self::MAX_PRESETS )
                    break;
            }
            $pref['presets'] = $presets;
        }

        if ( array_key_exists( 'visible', $input ) || array_key_exists( 'preset', $input ) )
        {
            $entry = isset( $pref['parts'][$part] ) && is_array( $pref['parts'][$part] ) ? $pref['parts'][$part] : array( 'visible' => array(), 'preset' => null );
            if ( array_key_exists( 'visible', $input ) )
            {
                if ( !is_array( $input['visible'] ) )
                    throw new InvalidArgumentException( 'visible must be a list of column keys' );
                $entry['visible'] = self::validKeys( $input['visible'], $registry );
            }
            if ( array_key_exists( 'preset', $input ) )
            {
                $preset = $input['preset'];
                $entry['preset'] = is_string( $preset ) && ( isset( $iniPresets[$preset] ) || isset( $pref['presets'][$preset] ) ) ? $preset : null;
            }
            $pref['parts'][$part] = array( 'visible' => array_values( $entry['visible'] ?? array() ), 'preset' => $entry['preset'] ?? null );
        }

        if ( array_key_exists( 'page_size', $input ) )
        {
            $size = $input['page_size'];
            if ( $size === null )
                $pref['page_size'] = null;
            else if ( is_numeric( $size ) && (int)$size >= 1 && (int)$size <= self::MAX_PAGE_SIZE )
                $pref['page_size'] = (int)$size;
            else
                throw new InvalidArgumentException( 'page_size must be 1 - ' . self::MAX_PAGE_SIZE );
        }

        return $pref;
    }

    /**
     * The known column keys of a list, in order, without duplicates, at most MAX_KEYS.
     */
    protected static function validKeys( $keys, expSubitemsColumnRegistry $registry )
    {
        $valid = array();
        foreach ( (array)$keys as $key )
        {
            if ( is_string( $key ) && !in_array( $key, $valid, true ) && $registry->isKnownKey( $key ) )
                $valid[] = $key;
            if ( count( $valid ) >= expSubitemsColumnRegistry::MAX_KEYS )
                break;
        }
        return $valid;
    }
}
