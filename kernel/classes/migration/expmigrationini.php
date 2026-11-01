<?php
/**
 * File containing the expMigrationIni class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * INI migrations: a file YYYYMMDDHHMMSS_name.ini that changes settings/override and settings/siteaccess/<name>. Each
 * block names its file, and its siteaccess when it is not for settings/override:
 *
 *   #?ini charset="utf-8"?
 *   # description: News settings and the admin's start page
 *   # environments: staging, production
 *
 *   [news.ini/NewsSettings]                 settings/override/news.ini.append.php
 *   ListLimit=20                            a value
 *   ShownClasses[]                          the array is the values that follow (Var[] first)
 *   ShownClasses[]=article
 *   ShownClasses[]=blog_post
 *
 *   [siteaccess:admin site.ini/SiteSettings]   settings/siteaccess/admin/site.ini.append.php
 *   DefaultPage=content/dashboard
 *
 *   [site.ini/ExtensionSettings]
 *   ActiveExtensions[]=expnews              a value added to an array (no Var[] before it)
 *   TransportAlias[smtp]=eZSMTPTransport    a key of a hash
 *   -DebugByIP                              a line starting with "-" removes: Var, Var[key], Var[]=value
 *
 * Every file is written the way the step type settings writes it (expMigrationStepSettings): the INI editor, a
 * backup first, owner, group and mode kept, each change in the audit log. A migration of this type is a YAML
 * migration of settings steps, one per file, in the order the files first appear. It cannot undo itself: a rollback
 * restores its caption (which keeps the settings folder).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationIni
{
    /**
     * The migration of an INI file.
     *
     * @param expMigrationDefinition $definition a definition of type ini
     * @param expMigrationStepRegistry|null $registry
     * @return expMigrationYaml
     * @throws RuntimeException with every problem of the file
     */
    public static function load( expMigrationDefinition $definition, $registry = null )
    {
        $errors = array();
        $warnings = array();
        $migration = self::read( $definition, $registry, $errors, $warnings );
        if ( $migration === null )
            throw new RuntimeException( implode( '; ', $errors ) );
        return $migration;
    }

    /**
     * Reads and checks an INI migration (the lint of exp:migration:check).
     *
     * @param expMigrationDefinition $definition
     * @param expMigrationStepRegistry|null $registry
     * @param string[] $errors
     * @param string[] $warnings
     * @return expMigrationYaml|null null when there is an error
     */
    public static function read( expMigrationDefinition $definition, $registry, array &$errors, array &$warnings )
    {
        if ( $definition->type() !== 'ini' )
        {
            $errors[] = $definition->id() . ' is not an INI migration (.' . $definition->type() . ')';
            return null;
        }
        $file = $definition->file();
        $text = $file !== null && is_file( $file ) ? @file_get_contents( $file ) : false;
        if ( $text === false )
        {
            $errors[] = $definition->id() . ': the file is missing or cannot be read';
            return null;
        }
        $steps = self::steps( (string)$text, $errors );
        if ( $steps === array() && !$errors )
            $warnings[] = 'no settings';
        if ( $errors )
            return null;
        $document = array( 'steps' => $steps, 'down' => null, 'transactional' => $definition->transactional() );
        $registry = $registry ?: expMigrationStepRegistry::fromSettings();
        foreach ( expMigrationYaml::validate( $document, $registry, $warnings ) as $error )
            $errors[] = $error;
        return $errors ? null : new expMigrationYaml( $definition, $document, $registry );
    }

    /**
     * The settings steps of an INI migration's text, one per file. Pure.
     *
     * @param string $text
     * @param string[] $errors with the line numbers
     * @return array[]
     */
    public static function steps( $text, array &$errors = array() )
    {
        $steps = array();
        $changes = array();
        $target = null;
        $block = null;
        $resets = array();
        $sawBlock = false;
        foreach ( preg_split( '/\r\n|\r|\n/', (string)$text ) as $n => $raw )
        {
            $at = 'line ' . ( $n + 1 );
            $line = trim( $raw );
            if ( $line === '' || $line[0] === '#' || $line[0] === ';' )
                continue;
            if ( $line[0] === '[' )
            {
                $sawBlock = true;
                if ( !preg_match( '/^\[\s*(?:(override|global|siteaccess:([A-Za-z0-9_-]+))\s+)?([A-Za-z0-9_.-]+?)(?:\.ini)?\s*\/\s*([A-Za-z0-9_\-. ]+?)\s*\]$/', $line, $m ) )
                {
                    $errors[] = "$at: $line does not name its file: [<file>.ini/<Block>], or [siteaccess:<name> <file>.ini/<Block>]";
                    $target = null;
                    continue;
                }
                $siteAccess = isset( $m[2] ) ? $m[2] : '';
                $target = ( $siteAccess !== '' ? "siteaccess:$siteAccess " : '' ) . $m[3] . '.ini';
                $block = $m[4];
                if ( !isset( $steps[$target] ) )
                {
                    $steps[$target] = array( 'type' => 'settings', 'file' => $m[3] . '.ini' );
                    if ( $siteAccess !== '' )
                        $steps[$target]['siteaccess'] = $siteAccess;
                    $changes[$target] = self::noChanges();
                }
                continue;
            }
            if ( $target === null )
            {
                if ( !$sawBlock )
                    $errors[] = "$at: a setting before the first block";
                continue;
            }
            $step = &$changes[$target];
            if ( $line[0] === '-' )
            {
                $item = trim( substr( $line, 1 ) );
                if ( expMigrationStepSettings::removal( $item ) === null )
                    $errors[] = "$at: -$item is not -Var, -Var[key] or -Var[]=value";
                else
                    $step['remove'][$block][] = $item;
            }
            else if ( preg_match( '/^([A-Za-z0-9_\-.]+)\[\]$/', $line, $m ) )
            {
                $resets[$target][$block][$m[1]] = true;
                if ( isset( $step['set'][$block][$m[1]] ) && !is_array( $step['set'][$block][$m[1]] ) )
                    $errors[] = "$at: {$m[1]} is a value and an array";
                $step['set'][$block][$m[1]] = array();
            }
            else if ( preg_match( '/^([A-Za-z0-9_\-.]+)\[\]=(.*)$/', $line, $m ) )
            {
                if ( isset( $resets[$target][$block][$m[1]] ) )
                    $step['set'][$block][$m[1]][] = $m[2];
                else
                    $step['add'][$block][$m[1]][] = $m[2];
            }
            else if ( preg_match( '/^([A-Za-z0-9_\-.]+)\[([^\]]+)\]=(.*)$/', $line, $m ) )
            {
                if ( isset( $step['set'][$block][$m[1]] ) && !is_array( $step['set'][$block][$m[1]] ) )
                    $errors[] = "$at: {$m[1]} is a value and a hash";
                else
                    $step['set'][$block][$m[1]][$m[2]] = $m[3];
            }
            else if ( preg_match( '/^([A-Za-z0-9_\-.]+)=(.*)$/', $line, $m ) )
            {
                if ( isset( $step['set'][$block][$m[1]] ) && is_array( $step['set'][$block][$m[1]] ) )
                    $errors[] = "$at: {$m[1]} is an array and a value";
                else
                    $step['set'][$block][$m[1]] = $m[2];
            }
            else
            {
                $errors[] = "$at: $line is not Var=value, Var[], Var[]=value, Var[key]=value or -Var";
            }
            unset( $step );
        }
        foreach ( $changes as $target => $change )
            foreach ( $change as $key => $blocks )
                if ( $blocks )
                    $steps[$target][$key] = $blocks;
        return array_values( $steps );
    }

    /**
     * The changes of one file before any line is read: set, add and remove, each block => what changes. Pure.
     *
     * @return array<string, array<string, array<int|string, mixed>>>
     */
    protected static function noChanges()
    {
        return array( 'set' => array(), 'add' => array(), 'remove' => array() );
    }
}
