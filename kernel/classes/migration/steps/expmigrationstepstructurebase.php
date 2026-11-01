<?php
/**
 * File containing the expMigrationStepStructureBase class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the step types of an installation's structure share (content_class, content_class_group, role, section,
 * language, object_state_group): names in several languages ("name:" in the language of "language:", "names:" a map
 * of locale => name), the key "missing", the eZSiteInstaller helpers the setup wizard and the site packages use, and
 * the caches a change of the structure touches.
 *
 * @since 6.0.15
 * @package kernel
 */
abstract class expMigrationStepStructureBase extends expMigrationStepTypeBase
{
    /**
     * The keys "language", "name" and "names" (and "description"/"descriptions" with $descriptions).
     *
     * @param string[] $modes
     * @param string $what what is named (a class, a state group ...)
     * @param bool $descriptions
     * @return array
     */
    protected static function nameKeys( array $modes, $what, $descriptions = false )
    {
        $keys = array(
            'language' => array( 'type' => 'string', 'modes' => $modes,
                                 'description' => 'the language of name: (default: the default language of content, ContentObjectLocale)' ),
            'name' => array( 'type' => 'string', 'modes' => $modes, 'description' => "the name of $what in the language of language:" ),
            'names' => array( 'type' => 'map', 'modes' => $modes, 'description' => "language => the name of $what in it: its translations" ),
        );
        if ( $descriptions )
        {
            $keys['description'] = array( 'type' => 'string', 'modes' => $modes, 'description' => "the description of $what in the language of language:" );
            $keys['descriptions'] = array( 'type' => 'map', 'modes' => $modes, 'description' => "language => the description of $what in it" );
        }
        return $keys;
    }

    /**
     * The key "missing".
     *
     * @param string[] $modes
     * @param string $what
     * @return array
     */
    protected static function missingKey( array $modes, $what )
    {
        return array( 'missing' => array( 'type' => 'string', 'default' => 'fail', 'values' => array( 'fail', 'skip' ), 'modes' => $modes,
                                          'description' => "what happens when $what is not there: fail (the default) or skip" ) );
    }

    /**
     * The problems of name:/names: (and description:/descriptions:) of a step. Pure.
     *
     * @param array $step
     * @param string $single name or description
     * @param string $map names or descriptions
     * @return string[]
     */
    protected static function translationProblems( array $step, $single = 'name', $map = 'names' )
    {
        $errors = array();
        if ( isset( $step['language'] ) && !expMigrationReferences::contains( $step['language'] ) )
        {
            $problem = expMigrationIdentifier::problem( 'language', $step['language'] );
            if ( $problem !== null )
                $errors[] = "language: $problem";
        }
        if ( isset( $step[$map] ) && is_array( $step[$map] ) )
        {
            foreach ( $step[$map] as $language => $text )
            {
                if ( is_int( $language ) || expMigrationIdentifier::problem( 'language', (string)$language ) !== null )
                    $errors[] = "$map: $language is not a language code (ger-DE)";
                else if ( is_array( $text ) )
                    $errors[] = "$map.$language: a text, not a list or a map";
            }
        }
        return $errors;
    }

    /**
     * The texts of name:/names: as locale => text, the language of language: first. The locales are checked to be
     * languages of the installation.
     *
     * @param array $step
     * @param string $single
     * @param string $map
     * @return array locale => text (empty when the step has neither key)
     * @throws RuntimeException for a language the installation does not have
     */
    protected static function translations( array $step, $single = 'name', $map = 'names' )
    {
        $texts = array();
        if ( isset( $step[$single] ) )
            $texts[expMigrationStepContentBase::localeOf( isset( $step['language'] ) ? $step['language'] : null )] = (string)$step[$single];
        if ( isset( $step[$map] ) && is_array( $step[$map] ) )
            foreach ( $step[$map] as $language => $text )
                $texts[expMigrationStepContentBase::localeOf( (string)$language )] = (string)$text;
        return $texts;
    }

    /**
     * The languages of name:/names: for a plan line, as written. Pure.
     *
     * @param array $step
     * @param string $single
     * @param string $map
     * @return string '' or ' named "..." (ger-DE: "...")'
     */
    protected static function namesText( array $step, $single = 'name', $map = 'names' )
    {
        $parts = array();
        if ( isset( $step[$single] ) )
            $parts[] = '"' . $step[$single] . '"';
        if ( isset( $step[$map] ) && is_array( $step[$map] ) )
            foreach ( $step[$map] as $language => $text )
                $parts[] = "$language: \"" . ( is_scalar( $text ) ? $text : '?' ) . '"';
        return $parts ? ' named ' . implode( ', ', $parts ) : '';
    }

    /**
     * The helpers of the setup wizard and the site packages (eZSiteInstaller), without the settings of a site.
     *
     * @return eZSiteInstaller
     */
    protected static function installer()
    {
        return new expMigrationSiteInstaller();
    }

    /**
     * Throws when an eZSiteInstaller helper reported an error.
     *
     * @param eZSiteInstaller $installer
     * @param string $what
     * @throws RuntimeException
     */
    protected static function checkInstaller( eZSiteInstaller $installer, $what )
    {
        if ( $installer->lastErrorCode() !== eZSiteInstaller::ERR_OK )
            throw new RuntimeException( "$what failed (" . implode( '; ', $installer->errors ) . ')' );
    }

    /**
     * A step of a missing target: an exception, or with missing: skip a result that says so.
     *
     * @param array $step
     * @param string $what
     * @param array $values the values, all null
     * @return array
     * @throws RuntimeException unless missing: skip
     */
    protected static function missingResult( array $step, $what, array $values )
    {
        if ( !isset( $step['missing'] ) || $step['missing'] !== 'skip' )
            throw new RuntimeException( "$what is not on this installation (missing: skip lets the step pass)" );
        return self::result( array( "$what is not there: skipped (missing: skip)" ), $values + array( 'created' => false, 'changed' => false ) );
    }

    /**
     * A bool of a step as the 0/1 of a column. Pure.
     *
     * @param mixed $value
     * @return int
     */
    protected static function flag( $value )
    {
        return $value ? 1 : 0;
    }
}
