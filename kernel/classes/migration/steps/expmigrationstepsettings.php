<?php
/**
 * File containing the expMigrationStepSettings class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "settings": changes one INI file of settings/override or of settings/siteaccess/<name>, and nothing
 * else (never the shipped settings/*.ini, never an extension's settings). It is also what an INI migration file
 * (YYYYMMDDHHMMSS_name.ini, expMigrationIni) runs.
 *
 *   - type: settings
 *     file: site.ini
 *     siteaccess: admin               the siteaccess (settings/siteaccess/admin); without: settings/override
 *     set:
 *       SiteSettings:
 *         SiteName: Example           a value (true and false are written enabled and disabled)
 *         IndexPage: /content/view/full/2
 *       RegionalSettings:
 *         SiteLanguageList: [ eng-GB, ger-DE ]   a list: the array is these values (Var[] first, then Var[]=...)
 *       MailSettings:
 *         TransportAlias: { smtp: eZSMTPTransport }   a map: these keys of a hash are set (Var[key]=value)
 *     add:
 *       ExtensionSettings:
 *         ActiveExtensions: [ expnews ]   values added to an array that are not in it yet
 *     remove:
 *       DebugSettings: [ DebugOutput, "DebugRedirection[ip]", "DebugByIP[]=127.0.0.1" ]
 *
 * The file is written by the kernel's INI editor (expIniEditor), as ./console exp:ini writes it: only the lines that
 * change, a copy of the file as it was first under var/backup/ini/<time>/ (readable by its owner only), the write
 * atomic with the file's owner, group and mode kept, and each changed variable recorded in the audit log
 * (system.setting.write, a secret as [secret]). What the file says already is not written again. A dry run writes
 * nothing and lists the changes, a secret's value masked. The INI cache is cleared after the run.
 *
 * The step cannot undo itself: a migration with it needs a down: (a settings step that writes the old values) or a
 * rollback to its caption, which restores the settings folder.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepSettings extends expMigrationStepTypeBase
{
    /** @return string */
    public function name()
    {
        return 'settings';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Changes one INI file of settings/override or settings/siteaccess/<name> through the INI editor, with a backup',
            'keys' => array(
                'file' => array( 'type' => 'string', 'required' => true, 'description' => 'the INI file: site.ini, content.ini ...' ),
                'siteaccess' => array( 'type' => 'string', 'description' => 'the siteaccess (settings/siteaccess/<name>); without: settings/override' ),
                'set' => array( 'type' => 'map', 'description' => 'block => variable => value (a list: the array; a map: hash keys)' ),
                'add' => array( 'type' => 'map', 'description' => 'block => variable => values added to the array' ),
                'remove' => array( 'type' => 'map', 'description' => 'block => list of Var, Var[key] or Var[]=value' ),
            ),
            'values' => array(
                'file' => 'the file written, relative to the installation',
                'changed' => 'true when the file changed',
            ),
            'example' => "- type: settings\n  file: site.ini\n  siteaccess: admin\n  set:\n    SiteSettings:\n      DefaultPage: content/dashboard\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        if ( isset( $step['file'] ) && !expMigrationReferences::contains( $step['file'] ) && !preg_match( '/^[A-Za-z0-9_.-]+\.ini$/', (string)$step['file'] ) )
            $errors[] = 'file: the name of an INI file (site.ini)';
        if ( isset( $step['siteaccess'] ) && !expMigrationReferences::contains( $step['siteaccess'] ) && !expIniEditor::isValidName( $step['siteaccess'] ) )
            $errors[] = 'siteaccess: the name of a siteaccess';
        if ( empty( $step['set'] ) && empty( $step['add'] ) && empty( $step['remove'] ) )
            $errors[] = 'set, add or remove: what to change';
        foreach ( array( 'set', 'add', 'remove' ) as $key )
        {
            if ( !isset( $step[$key] ) || !is_array( $step[$key] ) )
                continue;
            foreach ( $step[$key] as $block => $variables )
            {
                if ( !preg_match( '/^[A-Za-z0-9_\-. ]+$/', (string)$block ) )
                    $errors[] = "$key.$block: the name of a block";
                if ( $key === 'remove' )
                {
                    if ( !is_array( $variables ) || !expMigrationYaml::isList( $variables ) )
                        $errors[] = "remove.$block: a list of Var, Var[key] or Var[]=value";
                    else
                        foreach ( $variables as $item )
                            if ( !is_string( $item ) || self::removal( $item ) === null )
                                $errors[] = "remove.$block: $item is not Var, Var[key] or Var[]=value";
                    continue;
                }
                if ( !is_array( $variables ) || expMigrationYaml::isList( $variables ) )
                {
                    $errors[] = "$key.$block: variable => value";
                    continue;
                }
                foreach ( $variables as $variable => $value )
                {
                    if ( !preg_match( '/^[A-Za-z0-9_\-.]+$/', (string)$variable ) )
                        $errors[] = "$key.$block.$variable: the name of a variable";
                    $values = is_array( $value ) ? $value : array( $value );
                    if ( $key === 'add' && !( is_array( $value ) && expMigrationYaml::isList( $value ) ) && !is_scalar( $value ) )
                        $errors[] = "add.$block.$variable: a value or a list of values";
                    foreach ( $values as $one )
                        if ( $one !== null && !is_scalar( $one ) )
                            $errors[] = "$key.$block.$variable: values, a list of values or a map of keys to values";
                }
            }
        }
        return $errors;
    }

    /** @return string[] */
    public function caches( array $step )
    {
        return array( 'tag:ini' );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $changes = array();
        $editor = $this->editor( $step, $changes );
        $where = $editor->relativePath();
        if ( !$editor->hasChanges() )
            return array( "$where: says so already, nothing to write" );
        $lines = array( "write $where (a backup first):" );
        foreach ( $changes as $change )
            $lines[] = "  $change";
        return $lines;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $changes = array();
        $editor = $this->editor( $step, $changes );
        $where = $editor->relativePath();
        if ( !$editor->hasChanges() )
            return self::result( array( "$where: says so already" ), array( 'file' => $where, 'changed' => false ) );
        try
        {
            $result = $editor->save( array( 'backup' => true, 'create' => true ) );
        }
        catch ( expIniException $e )
        {
            throw new RuntimeException( $e->getMessage(), 0, $e );
        }
        $lines = array( "wrote $where" . ( $result->backup() ? ', the old file kept as ' . self::relative( (string)$result->backup() ) : ( $result->created() ? ' (a new file)' : '' ) ) );
        foreach ( $changes as $change )
            $lines[] = "  $change";
        foreach ( (array)$result->warnings() as $warning )
            $lines[] = "  warning: $warning";
        return self::result( $lines, array( 'file' => $where, 'changed' => true ) );
    }

    /**
     * The editor of the step's file with the step's changes made (not written).
     *
     * @param array $step
     * @param string[] $changes what changed, one line each, secrets masked
     * @return expIniEditor
     * @throws RuntimeException
     */
    protected function editor( array $step, array &$changes )
    {
        try
        {
            $scope = expIniEditor::scope( isset( $step['siteaccess'] ) && $step['siteaccess'] !== '' ? 'siteaccess:' . $step['siteaccess'] : 'global' );
        }
        catch ( expIniException $e )
        {
            throw new RuntimeException( $e->getMessage(), 0, $e );
        }
        if ( !in_array( $scope->kind(), array( expIniScope::KIND_GLOBAL, expIniScope::KIND_SITEACCESS ), true ) )
            throw new RuntimeException( 'settings steps write only settings/override and settings/siteaccess/<name>, not ' . $scope->name() );
        $editor = new expIniEditor( $scope, (string)$step['file'] );
        $relative = $editor->relativePath();
        if ( !preg_match( '#^settings/(override|siteaccess/[^/]+)/[^/]+$#', $relative ) )
            throw new RuntimeException( "settings steps write only settings/override and settings/siteaccess/<name>, not $relative" );
        try
        {
            foreach ( isset( $step['remove'] ) ? (array)$step['remove'] : array() as $block => $items )
            {
                foreach ( (array)$items as $item )
                {
                    list( $variable, $key, $value ) = self::removal( $item );
                    try
                    {
                        $editor->remove( (string)$block, $variable, $value, $key );
                        $changes[] = "remove [$block] $item";
                    }
                    catch ( expIniException $e )
                    {
                        if ( $e->getCode() !== expIniException::NOT_FOUND )
                            throw $e;
                    }
                }
            }
            foreach ( isset( $step['set'] ) ? (array)$step['set'] : array() as $block => $variables )
            {
                foreach ( (array)$variables as $variable => $value )
                {
                    $block = (string)$block;
                    $variable = (string)$variable;
                    if ( is_array( $value ) && expMigrationYaml::isList( $value ) )
                    {
                        $values = array_map( array( __CLASS__, 'text' ), $value );
                        $now = $editor->get( $block, $variable );
                        if ( !is_array( $now ) || array_values( $now ) !== $values || !self::resetsHere( $editor, $block, $variable ) )
                        {
                            $editor->clearArray( $block, $variable );
                            foreach ( $values as $one )
                                $editor->add( $block, $variable, $one );
                            $changes[] = "set [$block] {$variable}[] = " . self::shown( $variable, implode( ', ', $values ) );
                        }
                    }
                    else if ( is_array( $value ) )
                    {
                        foreach ( $value as $key => $one )
                            if ( $editor->set( $block, $variable, self::text( $one ), (string)$key ) )
                                $changes[] = "set [$block] {$variable}[$key] = " . self::shown( $variable, self::text( $one ) );
                    }
                    else if ( $editor->set( $block, $variable, self::text( $value ) ) )
                    {
                        $changes[] = "set [$block] $variable = " . self::shown( $variable, self::text( $value ) );
                    }
                }
            }
            foreach ( isset( $step['add'] ) ? (array)$step['add'] : array() as $block => $variables )
                foreach ( (array)$variables as $variable => $values )
                    foreach ( (array)$values as $one )
                        if ( $editor->add( (string)$block, (string)$variable, self::text( $one ) ) )
                            $changes[] = "add [$block] {$variable}[] = " . self::shown( (string)$variable, self::text( $one ) );
        }
        catch ( expIniException $e )
        {
            throw new RuntimeException( "$relative: " . $e->getMessage(), 0, $e );
        }
        return $editor;
    }

    /**
     * Whether the file resets the array itself (a line Var[] before its values): a list in set: replaces what the
     * files read before say only then.
     *
     * @param expIniEditor $editor
     * @param string $block
     * @param string $variable
     * @return bool
     */
    protected static function resetsHere( expIniEditor $editor, $block, $variable )
    {
        foreach ( (array)$editor->blockLines( $block ) as $line )
            if ( preg_match( '/^\s*' . preg_quote( $variable, '/' ) . '\[\]\s*$/', (string)$line ) )
                return true;
        return false;
    }

    /**
     * An item of remove: array( variable, key, value ), or null when it is none. Pure.
     *
     * @param string $item Var, Var[key] or Var[]=value
     * @return array|null
     */
    public static function removal( $item )
    {
        if ( !is_string( $item ) )
            return null;
        if ( preg_match( '/^([A-Za-z0-9_\-.]+)\[\]=(.*)$/s', $item, $m ) )
            return array( $m[1], null, $m[2] );
        if ( preg_match( '/^([A-Za-z0-9_\-.]+)\[([^\]]+)\]$/', $item, $m ) )
            return array( $m[1], $m[2], null );
        if ( preg_match( '/^[A-Za-z0-9_\-.]+$/', $item ) )
            return array( $item, null, null );
        return null;
    }

    /**
     * A value as the INI file writes it. Pure.
     *
     * @param mixed $value
     * @return string
     */
    public static function text( $value )
    {
        if ( is_bool( $value ) )
            return $value ? 'enabled' : 'disabled';
        return $value === null ? '' : (string)$value;
    }

    /** @return string a value for the lines, masked for a secret */
    protected static function shown( $variable, $value )
    {
        return expIniEditor::isSecret( $variable ) ? expIniEditor::maskValue( $value ) : $value;
    }

    /** @return string a path relative to the INI editor's root */
    protected static function relative( $path )
    {
        $root = rtrim( expIniEditor::root(), '/' ) . '/';
        return strpos( $path, $root ) === 0 ? substr( $path, strlen( $root ) ) : $path;
    }
}
