<?php
/**
 * File containing the expImageAliasAdmin class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What Setup > Image aliases (setup/aliases) shows and does: the dashboard, the settings of [AliasSettings],
 * [AliasStaging] and [AliasSource_<environment>] (written to settings/override through expIniEditor), the scope of a
 * bulk run (generate, regenerate, purge, resync) and its start as a job, the test tools (one image, the connection to
 * a source) and the run history read from imagealias.log.
 *
 * The parts that decide (the settings definition and its validation, the scope of a run, the log filter and counts,
 * the dashboard's verdicts) take their input as arrays and read nothing, so they are tested without a database
 * (expImageAliasAdminTest). The view classes (kernel/private/classes/views/setup/aliases.php, aliasesfeed.php) only
 * read the request and draw the page.
 *
 * Seeing the page needs the policy setup/aliases, every change setup/managealiases. A credential is named here by
 * its secret (expSecrets), never shown or written as a value.
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasAdmin
{
    /** The settings file the editor writes, and where (settings/override) */
    const FILE = 'image';
    const SCOPE = 'global';

    /** The parts of the page, each a view parameter (tab) */
    const TABS = array( 'dashboard', 'bulk', 'tools', 'history', 'settings' );

    /** The actions of a run */
    const ACTIONS = array( 'generate', 'regenerate', 'purge', 'resync' );

    /** The scopes of a run */
    const TARGETS = array( 'all', 'node', 'subtree' );

    /** The most images a run may handle in the request when no job queue takes it */
    const SYNC_LIMIT = 50;

    /** The most log lines the history shows, and how many it reads for the dashboard's counts */
    const MAX_LINES = 500;
    const COUNT_LINES = 5000;

    /** The queue states that wait, and the one that runs */
    const WAITING_STATES = array( 'queued', 'ready', 'retry', 'held' );

    /** Days after which the dashboard suggests rotating the signing key */
    const KEY_OLD_DAYS = 365;

    // ------------------------------------------------------------------------------------------------ access

    /**
     * @param string $function aliases or managealiases
     * @param eZUser|null $user
     * @return bool the user has the function (with or without limitation)
     */
    public static function can( $function, $user = null )
    {
        if ( !class_exists( 'eZUser' ) )
            return false;
        $user = $user ?: eZUser::currentUser();
        $access = $user->hasAccessTo( 'setup', (string)$function );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    /**
     * @param string $tab
     * @return string a known tab, dashboard for anything else
     */
    public static function tab( $tab )
    {
        return in_array( (string)$tab, self::TABS, true ) ? (string)$tab : 'dashboard';
    }

    // ------------------------------------------------------------------------------------------------ settings

    /**
     * The settings the editor offers: block => variable => definition. A definition: type (enum, int, number,
     * switch, list, url, secret, text), values (enum), min/max (int, number), default (as settings/image.ini ships
     * it). [AliasSource_<environment>] is added per environment by sourceDefinition().
     *
     * @return array
     */
    public static function settingsDefinition()
    {
        $switch = array( 'type' => 'switch', 'values' => array( 'enabled', 'disabled' ) );
        return array(
            'AliasSettings' => array(
                'AliasGeneration' => array( 'type' => 'enum', 'values' => array( 'on-demand', 'render' ), 'default' => 'on-demand' ),
                'OnDemandURL' => array( 'type' => 'enum', 'values' => array( 'token', 'ids-token', 'ids', 'node', 'storage' ), 'default' => 'token' ),
                'Delivery' => array( 'type' => 'enum', 'values' => array( 'auto', 'redirect', 'stream' ), 'default' => 'auto' ),
                'MaxWait' => array( 'type' => 'number', 'min' => 0, 'max' => 300, 'default' => '2' ),
                'PreferredConverter' => array( 'type' => 'enum', 'values' => array( 'auto', 'imagick', 'imagemagick', 'gd' ), 'default' => 'auto' ),
                'Negotiate' => $switch + array( 'default' => 'enabled' ),
                'Placeholder' => $switch + array( 'default' => 'enabled' ),
                'SwapScript' => $switch + array( 'default' => 'enabled' ),
                'PreGenerate' => $switch + array( 'default' => 'enabled' ),
                'MaxParallel' => array( 'type' => 'int', 'min' => 1, 'max' => 64, 'default' => '2' ),
                'BulkNice' => array( 'type' => 'int', 'min' => 0, 'max' => 19, 'default' => '10' ),
                'CacheMaxAge' => array( 'type' => 'int', 'min' => 0, 'max' => 31536000, 'default' => '31536000' ),
                'DraftTokenLifetime' => array( 'type' => 'int', 'min' => 60, 'max' => 31536000, 'default' => '3600' ),
                'SigningKeyGrace' => array( 'type' => 'int', 'min' => 0, 'max' => 31536000, 'default' => '604800' ),
            ),
            'AliasStaging' => array(
                'Environment' => array( 'type' => 'enum', 'values' => expImageAliasStager::ENVIRONMENTS, 'default' => 'pro' ),
                'SyncFrom' => array( 'type' => 'enum', 'values' => array_merge( array( '' ), expImageAliasStager::ENVIRONMENTS ), 'default' => '' ),
                'PathMap' => array( 'type' => 'list', 'item' => 'pathmap', 'default' => array() ),
                'Timeout' => array( 'type' => 'number', 'min' => 1, 'max' => 120, 'default' => '5' ),
                'MaxFileSize' => array( 'type' => 'int', 'min' => 1024, 'max' => 2147483647, 'default' => '67108864' ),
                'FailuresBeforePause' => array( 'type' => 'int', 'min' => 1, 'max' => 1000, 'default' => '5' ),
                'PauseSeconds' => array( 'type' => 'int', 'min' => 0, 'max' => 86400, 'default' => '300' ),
                'AlertEmail' => $switch + array( 'default' => 'enabled' ),
                'AlertRecipients' => array( 'type' => 'list', 'item' => 'email', 'default' => array() ),
            ),
        );
    }

    /**
     * The settings of one source block.
     *
     * @return array variable => definition
     */
    public static function sourceDefinition()
    {
        return array( 'BaseURL' => array( 'type' => 'url', 'default' => '' ),
                      'Credential' => array( 'type' => 'secret', 'default' => '' ) );
    }

    /**
     * Every block the editor writes, with its variables: [AliasSettings], [AliasStaging] and a source block per
     * environment.
     *
     * @return array block => variable => definition
     */
    public static function allDefinitions()
    {
        $all = self::settingsDefinition();
        foreach ( expImageAliasStager::ENVIRONMENTS as $environment )
            $all['AliasSource_' . $environment] = self::sourceDefinition();
        return $all;
    }

    /**
     * Checks one value against its definition.
     *
     * @param array $def
     * @param mixed $value a string, or for a list a string with one entry per line (or an array)
     * @return array ok (bool), value (normalized: a string, an array for a list), error (a short reason or '')
     */
    public static function validateValue( array $def, $value )
    {
        if ( $def['type'] === 'list' )
        {
            $items = is_array( $value ) ? $value : preg_split( '/\r\n|\r|\n/', (string)$value );
            $out = array();
            foreach ( $items as $item )
            {
                $item = trim( (string)$item );
                if ( $item === '' || in_array( $item, $out, true ) )
                    continue;
                if ( preg_match( '/[\x00-\x1F\x7F]/', $item ) || strlen( $item ) > 500 )
                    return array( 'ok' => false, 'value' => $out, 'error' => 'an entry is too long or has control characters' );
                if ( $def['item'] === 'email' && !filter_var( $item, FILTER_VALIDATE_EMAIL ) )
                    return array( 'ok' => false, 'value' => $out, 'error' => 'not an e-mail address: ' . $item );
                if ( $def['item'] === 'pathmap' && !self::validPathMap( $item ) )
                    return array( 'ok' => false, 'value' => $out, 'error' => 'not "<local prefix>;<source prefix or URL>": ' . $item );
                $out[] = $item;
            }
            if ( count( $out ) > 100 )
                return array( 'ok' => false, 'value' => $out, 'error' => 'at most 100 entries' );
            return array( 'ok' => true, 'value' => $out, 'error' => '' );
        }
        if ( is_array( $value ) )
            return array( 'ok' => false, 'value' => '', 'error' => 'one value expected' );
        $value = trim( (string)$value );
        if ( preg_match( '/[\x00-\x1F\x7F]/', $value ) )
            return array( 'ok' => false, 'value' => '', 'error' => 'control characters' );
        switch ( $def['type'] )
        {
            case 'enum':
            case 'switch':
                $value = strtolower( $value );
                if ( !in_array( $value, $def['values'], true ) )
                    return array( 'ok' => false, 'value' => $value, 'error' => 'one of ' . implode( ', ', array_map( function ( $v ) {
                        return $v === '' ? '(none)' : $v;
                    }, $def['values'] ) ) );
                return array( 'ok' => true, 'value' => $value, 'error' => '' );
            case 'int':
                if ( !preg_match( '/^\d{1,10}$/', $value ) || (int)$value < $def['min'] || (int)$value > $def['max'] )
                    return array( 'ok' => false, 'value' => $value, 'error' => 'a whole number from ' . $def['min'] . ' to ' . $def['max'] );
                return array( 'ok' => true, 'value' => (string)(int)$value, 'error' => '' );
            case 'number':
                if ( !preg_match( '/^\d{1,6}(\.\d{1,3})?$/', $value ) || (float)$value < $def['min'] || (float)$value > $def['max'] )
                    return array( 'ok' => false, 'value' => $value, 'error' => 'a number from ' . $def['min'] . ' to ' . $def['max'] );
                return array( 'ok' => true, 'value' => self::scalarText( $value ), 'error' => '' );
            case 'url':
                $value = rtrim( $value, '/' );
                if ( $value !== '' && !expImageAliasStager::validBaseUrl( $value ) )
                    return array( 'ok' => false, 'value' => $value, 'error' => 'an http or https address without user, password, query or fragment' );
                return array( 'ok' => true, 'value' => $value, 'error' => '' );
            case 'secret':
                if ( $value !== '' && !self::validSecretName( $value ) )
                    return array( 'ok' => false, 'value' => $value, 'error' => 'the name of a secret (letters, digits, _ . - :) or env:<VARIABLE>, never the value' );
                return array( 'ok' => true, 'value' => $value, 'error' => '' );
        }
        if ( strlen( $value ) > 500 )
            return array( 'ok' => false, 'value' => $value, 'error' => 'too long' );
        return array( 'ok' => true, 'value' => $value, 'error' => '' );
    }

    /**
     * A name of the secret store or of an environment variable (env:NAME). A value with a ":" between two words
     * other than env: ("user:password") is refused: it would be a credential typed in instead of a name.
     *
     * @param string $name
     * @return bool
     */
    public static function validSecretName( $name )
    {
        $name = (string)$name;
        if ( preg_match( '/^env:[A-Za-z_][A-Za-z0-9_]{0,99}$/', $name ) )
            return true;
        if ( strpos( $name, ':' ) !== false || preg_match( '/\s/', $name ) )
            return false;
        if ( class_exists( 'expSecrets' ) )
            return expSecrets::validName( $name );
        return (bool)preg_match( '/^[A-Za-z0-9_.-]{1,100}$/', $name );
    }

    /**
     * @param string $entry a PathMap[] entry: "<local prefix>;<source prefix or URL>"
     * @return bool
     */
    public static function validPathMap( $entry )
    {
        $parts = explode( ';', (string)$entry );
        if ( count( $parts ) !== 2 || trim( $parts[0] ) === '' || trim( $parts[1] ) === '' )
            return false;
        if ( strpos( $parts[0], '..' ) !== false || strpos( $parts[1], '..' ) !== false )
            return false;
        $source = trim( $parts[1] );
        return preg_match( '#^https?://#i', $source ) ? expImageAliasStager::validBaseUrl( rtrim( $source, '/' ) ) : (bool)preg_match( '#^/?[A-Za-z0-9_./~-]+$#', $source );
    }

    /**
     * The changes a submitted settings form asks for, compared with what is in force.
     *
     * @param array $input block => variable => submitted value (a list as text, one entry per line)
     * @param array $inherit block => variable => truthy: remove the variable from settings/override
     * @param array $state block => variable => array( value (in force), overridden (set in settings/override) )
     * @return array changes (array( op set|add|clear|remove, block, variable, value )), errors (block.variable =>
     *               reason), changed (block.variable names)
     */
    public static function settingsChanges( array $input, array $inherit, array $state )
    {
        $changes = array();
        $errors = array();
        $changed = array();
        foreach ( self::allDefinitions() as $block => $variables )
        {
            foreach ( $variables as $variable => $def )
            {
                $current = isset( $state[$block][$variable] ) ? $state[$block][$variable] : array( 'value' => $def['default'], 'overridden' => false );
                if ( !empty( $inherit[$block][$variable] ) )
                {
                    if ( !empty( $current['overridden'] ) )
                    {
                        $changes[] = array( 'op' => 'remove', 'block' => $block, 'variable' => $variable, 'value' => '' );
                        $changed[] = "$block.$variable";
                    }
                    continue;
                }
                if ( !isset( $input[$block] ) || !is_array( $input[$block] ) || !array_key_exists( $variable, $input[$block] ) )
                    continue;
                $checked = self::validateValue( $def, $input[$block][$variable] );
                if ( !$checked['ok'] )
                {
                    $errors["$block.$variable"] = $checked['error'];
                    continue;
                }
                if ( $def['type'] === 'list' )
                {
                    $now = array_values( array_filter( array_map( 'strval', (array)$current['value'] ), 'strlen' ) );
                    if ( $checked['value'] === $now )
                        continue;
                    $changes[] = array( 'op' => 'clear', 'block' => $block, 'variable' => $variable, 'value' => '' );
                    foreach ( $checked['value'] as $item )
                        $changes[] = array( 'op' => 'add', 'block' => $block, 'variable' => $variable, 'value' => $item );
                }
                else
                {
                    if ( $checked['value'] === self::scalarText( $current['value'] ) )
                        continue;
                    $changes[] = array( 'op' => 'set', 'block' => $block, 'variable' => $variable, 'value' => $checked['value'] );
                }
                $changed[] = "$block.$variable";
            }
        }
        // The source a staging site fetches from needs an address
        $sync = isset( $input['AliasStaging']['SyncFrom'] ) ? strtolower( trim( (string)$input['AliasStaging']['SyncFrom'] ) ) : '';
        $environment = isset( $input['AliasStaging']['Environment'] ) ? strtolower( trim( (string)$input['AliasStaging']['Environment'] ) ) : '';
        if ( $sync !== '' && $sync === $environment )
            $errors['AliasStaging.SyncFrom'] = 'an installation cannot fetch from its own environment';
        return array( 'changes' => $changes, 'errors' => $errors, 'changed' => $changed );
    }

    /**
     * @param mixed $value
     * @return string a scalar value as text (normalized like validateValue() writes it)
     */
    protected static function scalarText( $value )
    {
        if ( is_array( $value ) )
            return '';
        $value = trim( (string)$value );
        // numbers as the editor writes them: 2.50 -> 2.5, 2.0 -> 2, 007 -> 7
        if ( preg_match( '/^\d+\.\d+$/', $value ) )
            $value = rtrim( rtrim( $value, '0' ), '.' );
        if ( preg_match( '/^\d+$/', $value ) )
            $value = ltrim( $value, '0' ) === '' ? '0' : ltrim( $value, '0' );
        return $value;
    }

    /**
     * What is in force now for every setting of the editor, read through eZINI (the current siteaccess) and the file
     * settings/override/image.ini.append.php (whether the editor's own scope sets it).
     *
     * @param eZINI|null $ini
     * @param expIniEditor|null $editor
     * @return array block => variable => array( value, overridden )
     */
    public static function settingsState( $ini = null, $editor = null )
    {
        $ini = $ini ?: eZINI::instance( self::FILE . '.ini' );
        if ( $editor === null )
        {
            try
            {
                $editor = new expIniEditor( expIniEditor::scope( self::SCOPE ), self::FILE );
            }
            catch ( Throwable $e )
            {
                $editor = null;
            }
        }
        $state = array();
        foreach ( self::allDefinitions() as $block => $variables )
        {
            foreach ( $variables as $variable => $def )
            {
                $value = $ini->hasVariable( $block, $variable ) ? $ini->variable( $block, $variable ) : $def['default'];
                if ( $def['type'] === 'list' )
                    $value = array_values( array_filter( array_map( 'strval', (array)$value ), 'strlen' ) );
                $overridden = false;
                if ( $editor !== null )
                {
                    try
                    {
                        $overridden = $editor->get( $block, $variable ) !== null;
                    }
                    catch ( Throwable $e )
                    {
                        $overridden = false;
                    }
                }
                $state[$block][$variable] = array( 'value' => $value, 'overridden' => $overridden );
            }
        }
        return $state;
    }

    /**
     * Writes (or only shows, $dryRun) the changes into settings/override/image.ini.append.php: line by line, a backup
     * first, the audit's system.setting.write (expIniEditor::save()); then the INI caches are cleared.
     *
     * @param array[] $changes see settingsChanges()
     * @param bool $dryRun
     * @return array ok, path, diff, written (bool), error
     */
    public static function applySettings( array $changes, $dryRun )
    {
        $out = array( 'ok' => true, 'path' => '', 'diff' => '', 'written' => false, 'error' => '' );
        try
        {
            $editor = new expIniEditor( expIniEditor::scope( self::SCOPE ), self::FILE );
            $emptied = array();
            foreach ( $changes as $change )
            {
                if ( $change['op'] === 'remove' )
                {
                    $editor->remove( $change['block'], $change['variable'] );
                    $emptied[$change['block']] = true;
                }
                else if ( $change['op'] === 'clear' )
                    $editor->clearArray( $change['block'], $change['variable'] );
                else if ( $change['op'] === 'add' )
                    $editor->add( $change['block'], $change['variable'], $change['value'] );
                else
                    $editor->set( $change['block'], $change['variable'], $change['value'] );
            }
            // A block whose last setting went goes with it (the undo of a save that made it), unless the file says
            // something there of its own: a comment (a commented setting to switch back) keeps the block
            foreach ( array_keys( $emptied ) as $block )
            {
                if ( !$editor->variables( $block ) && trim( implode( '', (array)$editor->blockLines( $block ) ) ) === '' )
                {
                    try
                    {
                        $editor->removeBlock( $block, true );
                    }
                    catch ( Exception $e )
                    {
                    }
                }
            }
            $out['path'] = $editor->relativePath();
            $out['diff'] = $editor->diff();
            if ( !$dryRun && $editor->hasChanges() )
            {
                $editor->save();
                $out['written'] = true;
                if ( expIniEditor::isRealRoot() && class_exists( 'expSettingsPage' ) )
                    expSettingsPage::clearIniCache();
            }
        }
        catch ( Exception $e )
        {
            $out['ok'] = false;
            $out['error'] = $e->getMessage();
        }
        return $out;
    }

    /**
     * The secrets a credential can name (names and where they are kept, never a value).
     *
     * @return array name => array( name, source, scope, readable, modified )
     */
    public static function secretNames()
    {
        if ( !class_exists( 'expSecrets' ) )
            return array();
        try
        {
            $out = array();
            foreach ( expSecrets::names() as $name => $row )
            {
                // the signing keys of image URLs are never a source's credential
                if ( strpos( (string)$name, expImageAliasUrl::SECRET ) === 0 )
                    continue;
                $out[$name] = array( 'name' => (string)$name, 'source' => (string)$row['source'], 'scope' => (string)$row['scope'],
                                     'readable' => !empty( $row['readable'] ), 'modified' => (int)$row['modified'] );
            }
            return $out;
        }
        catch ( Throwable $e )
        {
            return array();
        }
    }

    // ------------------------------------------------------------------------------------------------ the scope of a run

    /**
     * The run a bulk form asks for, checked.
     *
     * @param array $input action, target (all|node|subtree), node, class (text: names separated by commas or spaces),
     *                     attribute (text), alias (list), since (text), limit, dry_run, overwrite
     * @param string[] $knownAliases the aliases a run may name (the related siteaccesses' AliasList[])
     * @param int|null $now
     * @return array action, options (for expImageAliasJob::startBulk() or startResync()), preview (a dry run of
     *               generate/regenerate/purge: count only), errors (field => reason), notes (what was ignored)
     */
    public static function scopeFromInput( array $input, array $knownAliases, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $errors = array();
        $notes = array();
        $action = isset( $input['action'] ) ? strtolower( trim( (string)$input['action'] ) ) : '';
        if ( !in_array( $action, self::ACTIONS, true ) )
        {
            $errors['action'] = 'one of ' . implode( ', ', self::ACTIONS );
            $action = 'generate';
        }
        $options = array();
        $target = isset( $input['target'] ) ? strtolower( trim( (string)$input['target'] ) ) : 'all';
        if ( !in_array( $target, self::TARGETS, true ) )
        {
            $errors['target'] = 'one of ' . implode( ', ', self::TARGETS );
            $target = 'all';
        }
        if ( $target !== 'all' )
        {
            $node = isset( $input['node'] ) ? trim( (string)$input['node'] ) : '';
            if ( !preg_match( '/^\d{1,10}$/', $node ) || (int)$node < 1 )
                $errors['node'] = 'a node id';
            else
                $options[$target] = (int)$node;
        }
        foreach ( array( 'class', 'attribute' ) as $key )
        {
            $values = self::identifierList( isset( $input[$key] ) ? $input[$key] : '' );
            if ( $values === false )
                $errors[$key] = 'identifiers (letters, digits, _ . -) separated by commas or spaces';
            else if ( $values )
                $options[$key] = $values;
        }
        $aliases = array();
        foreach ( isset( $input['alias'] ) ? (array)$input['alias'] : array() as $alias )
        {
            $alias = trim( (string)$alias );
            if ( $alias === '' )
                continue;
            $allowed = in_array( $alias, $knownAliases, true ) || ( $alias === 'original' && $action === 'resync' );
            if ( !$allowed )
            {
                $errors['alias'] = 'not an alias of this site: ' . $alias;
                continue;
            }
            if ( !in_array( $alias, $aliases, true ) )
                $aliases[] = $alias;
        }
        if ( $aliases )
            $options['alias'] = $aliases;
        $since = self::parseSince( isset( $input['since'] ) ? $input['since'] : '', $now );
        if ( $since === false )
            $errors['since'] = 'a date (2026-11-01, 2026-11-01 14:30) or a time ago (2 days, 12 hours, 30 minutes)';
        else if ( $since > 0 )
            $options['since'] = $since;
        $limit = isset( $input['limit'] ) ? trim( (string)$input['limit'] ) : '';
        if ( $limit !== '' )
        {
            if ( !preg_match( '/^\d{1,7}$/', $limit ) || (int)$limit < 1 )
                $errors['limit'] = 'a whole number from 1';
            else
                $options['limit'] = (int)$limit;
        }
        $dryRun = !empty( $input['dry_run'] );
        $overwrite = !empty( $input['overwrite'] );
        $preview = false;
        if ( $action === 'resync' )
        {
            if ( $overwrite )
                $options['overwrite'] = true;
            if ( $dryRun )
                $options['dry_run'] = true;
        }
        else
        {
            $options['action'] = $action;
            $preview = $dryRun;
            if ( $overwrite )
                $notes[] = $action === 'generate' ? 'overwrite-means-regenerate' : 'overwrite-ignored';
        }
        return array( 'action' => $action, 'target' => $target, 'options' => $options, 'preview' => $preview,
                      'errors' => $errors, 'notes' => $notes );
    }

    /**
     * @param mixed $text names separated by commas or white space (or a list)
     * @return string[]|false the names, false when one is not an identifier
     */
    public static function identifierList( $text )
    {
        $items = is_array( $text ) ? $text : preg_split( '/[\s,;]+/', (string)$text, -1, PREG_SPLIT_NO_EMPTY );
        $out = array();
        foreach ( $items as $item )
        {
            $item = trim( (string)$item );
            if ( $item === '' )
                continue;
            if ( !preg_match( '/^[A-Za-z0-9_.-]{1,100}$/', $item ) )
                return false;
            if ( !in_array( $item, $out, true ) )
                $out[] = $item;
        }
        return count( $out ) > 50 ? false : $out;
    }

    /**
     * "Changed since" as a time: a date or date and time, or a time ago ("2 days", "12 hours", "30 minutes", "1 week",
     * also "2d", "12h", "30m", "1w"). Never in the future.
     *
     * @param string $text
     * @param int $now
     * @return int|false 0 for an empty text, false for one that is none of these
     */
    public static function parseSince( $text, $now )
    {
        $text = strtolower( trim( (string)$text ) );
        if ( $text === '' )
            return 0;
        if ( preg_match( '/^(\d{1,4})\s*(m|min|mins|minute|minutes|h|hour|hours|d|day|days|w|week|weeks)(\s+ago)?$/', $text, $match ) )
        {
            $unit = $match[2][0];
            if ( strpos( $match[2], 'mi' ) === 0 || $match[2] === 'm' )
                $unit = 'm';
            $seconds = array( 'm' => 60, 'h' => 3600, 'd' => 86400, 'w' => 604800 );
            return max( 1, (int)$now - (int)$match[1] * $seconds[$unit] );
        }
        if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})(?:[ t](\d{2}):(\d{2}))?$/', $text, $match ) )
        {
            if ( !checkdate( (int)$match[2], (int)$match[3], (int)$match[1] ) || ( isset( $match[4] ) && ( (int)$match[4] > 23 || (int)$match[5] > 59 ) ) )
                return false;
            $time = mktime( isset( $match[4] ) ? (int)$match[4] : 0, isset( $match[5] ) ? (int)$match[5] : 0, 0, (int)$match[2], (int)$match[3], (int)$match[1] );
            return $time === false || $time > $now ? false : $time;
        }
        return false;
    }

    /**
     * The aliases a run may name: the union of AliasList[] over the related siteaccesses.
     *
     * @param array|null $plan siteaccess => names (null: expImageAliasSettings::siteAccessAliasPlan())
     * @return string[]
     */
    public static function knownAliases( $plan = null )
    {
        $plan = $plan === null ? expImageAliasSettings::siteAccessAliasPlan() : $plan;
        $out = array();
        foreach ( $plan as $names )
        {
            foreach ( (array)$names as $name )
            {
                if ( !in_array( (string)$name, $out, true ) )
                    $out[] = (string)$name;
            }
        }
        return $out;
    }

    /**
     * Starts a run that passed scopeFromInput(): queued when expqueue takes it, else in this request when it is
     * small (SYNC_LIMIT images at most). A dry run of generate, regenerate or purge only counts.
     *
     * @param array $scope scopeFromInput()
     * @return array ok, mode (queue, sync, preview, refused), job, run, summary, count, aliases, message (an untranslated
     *               reason when refused)
     */
    public static function startRun( array $scope )
    {
        $options = $scope['options'];
        $scopeOptions = array_intersect_key( $options, array_flip( array( 'node', 'subtree', 'since', 'limit', 'class', 'attribute' ) ) );
        $out = array( 'ok' => false, 'mode' => 'refused', 'job' => null, 'run' => null, 'summary' => null, 'count' => null,
                      'aliases' => array(), 'message' => '' );
        if ( $scope['action'] === 'resync' )
        {
            if ( expImageAliasStager::forRequest() === null )
            {
                $out['message'] = 'not-staging';
                return $out;
            }
            if ( !expImageAliasJob::queueAvailable( expImageAliasJob::TYPE_RESYNC ) && expImageAliasStager::countImages( $scopeOptions ) > self::SYNC_LIMIT )
            {
                $out['message'] = 'too-large';
                return $out;
            }
            $started = expImageAliasJob::startResync( $options );
            return array( 'ok' => true, 'mode' => $started['mode'], 'job' => $started['job'], 'run' => $started['run'],
                          'summary' => $started['summary'], 'count' => null, 'aliases' => array(), 'message' => '' );
        }
        $aliases = isset( $options['alias'] ) ? $options['alias'] : self::knownAliases();
        if ( $scope['preview'] )
        {
            $out = array( 'ok' => true, 'mode' => 'preview', 'count' => expImageAliasStager::countImages( $scopeOptions ), 'aliases' => $aliases ) + $out;
            return $out;
        }
        if ( !expImageAliasJob::queueAvailable( expImageAliasJob::TYPE_BULK ) && expImageAliasStager::countImages( $scopeOptions ) > self::SYNC_LIMIT )
        {
            $out['message'] = 'too-large';
            return $out;
        }
        $started = expImageAliasJob::startBulk( $options );
        return array( 'ok' => true, 'mode' => $started['mode'], 'job' => $started['job'], 'run' => $started['run'],
                      'summary' => $started['summary'], 'count' => null, 'aliases' => $aliases, 'message' => '' );
    }

    // ------------------------------------------------------------------------------------------------ jobs

    /**
     * @return string[] the job types of image aliases
     */
    public static function jobTypes()
    {
        return array( expImageAliasJob::TYPE_BULK, expImageAliasJob::TYPE_RESYNC, expImageAliasJob::TYPE_GENERATE );
    }

    /**
     * @return bool expqueue is there to ask
     */
    public static function queueReadable()
    {
        if ( !class_exists( 'expQueue' ) || !class_exists( 'expQueueJob' ) )
            return false;
        try
        {
            return expQueue::installed();
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * The jobs per image alias type that wait or run.
     *
     * @return array available (bool), types => type => array( waiting, running, dead, registered )
     */
    public static function queueState()
    {
        $out = array( 'available' => self::queueReadable(), 'types' => array() );
        foreach ( self::jobTypes() as $type )
        {
            $row = array( 'waiting' => 0, 'running' => 0, 'dead' => 0, 'registered' => expImageAliasJob::queueAvailable( $type ) );
            if ( $out['available'] )
            {
                try
                {
                    foreach ( expQueue::countBy( 'state', array( 'type' => $type ) ) as $state => $count )
                    {
                        if ( in_array( $state, self::WAITING_STATES, true ) )
                            $row['waiting'] += (int)$count;
                        else if ( $state === 'running' )
                            $row['running'] += (int)$count;
                        else if ( $state === 'dead' )
                            $row['dead'] += (int)$count;
                    }
                }
                catch ( Throwable $e )
                {
                    $out['available'] = false;
                }
            }
            $out['types'][$type] = $row;
        }
        return $out;
    }

    /**
     * The latest bulk and resync jobs (not the per-image generate jobs, which are many), newest first.
     *
     * @param int $limit
     * @return array[] see jobRow()
     */
    public static function jobs( $limit = 10 )
    {
        if ( !self::queueReadable() )
            return array();
        $out = array();
        try
        {
            foreach ( expQueue::listJobs( array( 'type' => array( expImageAliasJob::TYPE_BULK, expImageAliasJob::TYPE_RESYNC ) ), 0, max( 1, (int)$limit ) ) as $job )
                $out[] = self::jobRow( $job );
        }
        catch ( Throwable $e )
        {
            return array();
        }
        return $out;
    }

    /**
     * One job of image aliases, or null for an unknown id or a job of another type.
     *
     * @param int $id
     * @return array|null see jobRow()
     */
    public static function job( $id )
    {
        if ( (int)$id < 1 || !self::queueReadable() )
            return null;
        try
        {
            $job = expQueueJob::fetchByID( (int)$id );
        }
        catch ( Throwable $e )
        {
            return null;
        }
        if ( !$job || !in_array( (string)$job->attribute( 'type' ), self::jobTypes(), true ) )
            return null;
        return self::jobRow( $job );
    }

    /**
     * @param expQueueJob $job
     * @return array id, type, action, state, final, can_cancel, done, total, percent, message, run, scope, created,
     *               started, finished, error, counts (of the result)
     */
    public static function jobRow( $job )
    {
        $payload = $job->payloadArray();
        $result = $job->isFinal() ? $job->resultArray() : array();
        $type = (string)$job->attribute( 'type' );
        return array(
            'id' => (int)$job->attribute( 'id' ), 'type' => $type,
            'action' => $type === expImageAliasJob::TYPE_RESYNC ? 'resync' : ( isset( $payload['action'] ) ? (string)$payload['action'] : 'generate' ),
            'state' => (string)$job->attribute( 'state' ), 'final' => $job->isFinal(), 'can_cancel' => $job->canCancel(),
            'done' => (int)$job->attribute( 'progress_done' ), 'total' => (int)$job->attribute( 'progress_total' ),
            'percent' => $job->progressPercent(), 'message' => self::shortText( (string)$job->attribute( 'progress_message' ), 200 ),
            'run' => isset( $payload['run'] ) ? (string)$payload['run'] : '',
            'scope' => expImageAliasStager::describeScope( $payload ),
            'created' => (int)$job->attribute( 'created' ), 'started' => (int)$job->attribute( 'started' ),
            'finished' => (int)$job->attribute( 'finished' ),
            'error' => self::shortText( (string)$job->attribute( 'last_error' ), 255 ),
            'counts' => is_array( $result ) && isset( $result['counts'] ) && is_array( $result['counts'] ) ? array_map( 'intval', $result['counts'] ) : array(),
        );
    }

    /**
     * Cancels a job of image aliases (one that waits ends now; one that runs stops after its current image).
     *
     * @param int $id
     * @return string|null the new state, or null (not a job of image aliases, or it had ended)
     */
    public static function cancelJob( $id )
    {
        if ( self::job( $id ) === null )
            return null;
        try
        {
            return expQueue::cancel( (int)$id, 'from Setup > Image aliases' );
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    /**
     * @param string $text
     * @param int $length
     * @return string
     */
    protected static function shortText( $text, $length )
    {
        $text = (string)$text;
        return function_exists( 'mb_substr' ) ? mb_substr( $text, 0, $length ) : substr( $text, 0, $length );
    }

    // ------------------------------------------------------------------------------------------------ the log

    /**
     * A filter of the run history from view parameters: event (one of expImageAliasLog::EVENTS, or '' for all),
     * run (a run id), alias, text (in the path), lines.
     *
     * @param array $raw
     * @return array event, run, alias, text, lines
     */
    public static function logFilter( array $raw )
    {
        $event = isset( $raw['event'] ) ? strtolower( trim( (string)$raw['event'] ) ) : '';
        $run = isset( $raw['run'] ) ? trim( (string)$raw['run'] ) : '';
        $alias = isset( $raw['alias'] ) ? trim( (string)$raw['alias'] ) : '';
        $text = isset( $raw['text'] ) ? trim( (string)$raw['text'] ) : '';
        $lines = isset( $raw['lines'] ) ? (int)$raw['lines'] : 100;
        return array(
            'event' => in_array( $event, expImageAliasLog::EVENTS, true ) || $event === 'runs' ? $event : '',
            'run' => preg_match( '/^[A-Za-z0-9_.-]{1,64}$/', $run ) ? $run : '',
            'alias' => preg_match( '/^[A-Za-z0-9_.,-]{1,100}$/', $alias ) ? $alias : '',
            'text' => self::shortText( preg_replace( '/[\x00-\x1F\x7F]/', '', $text ), 200 ),
            'lines' => min( self::MAX_LINES, max( 10, $lines ) ),
        );
    }

    /**
     * The entries of the log that pass a filter, in their order (newest first as read()).
     *
     * @param array[] $entries expImageAliasLog::parse() results
     * @param array $filter logFilter(); event 'runs' keeps run-start and run-end
     * @return array[]
     */
    public static function filterLog( array $entries, array $filter )
    {
        $out = array();
        foreach ( $entries as $entry )
        {
            if ( !is_array( $entry ) || !isset( $entry['event'] ) )
                continue;
            if ( $filter['event'] === 'runs' )
            {
                if ( $entry['event'] !== 'run-start' && $entry['event'] !== 'run-end' )
                    continue;
            }
            else if ( $filter['event'] !== '' && $entry['event'] !== $filter['event'] )
                continue;
            if ( $filter['run'] !== '' && ( isset( $entry['run'] ) ? $entry['run'] : '' ) !== $filter['run'] )
                continue;
            if ( $filter['alias'] !== '' && strpos( (string)$entry['alias'], $filter['alias'] ) !== 0 )
                continue;
            if ( $filter['text'] !== '' && stripos( (string)$entry['path'], $filter['text'] ) === false )
                continue;
            $out[] = $entry;
            if ( count( $out ) >= $filter['lines'] )
                break;
        }
        return $out;
    }

    /**
     * Reads the log and filters it.
     *
     * @param array $filter logFilter()
     * @return array[] entries for the page and the feed (time, event, result, ms, alias, run, path, siteaccess, extra)
     */
    public static function readLog( array $filter )
    {
        $any = $filter['event'] === '' && $filter['run'] === '' && $filter['alias'] === '' && $filter['text'] === '';
        $entries = expImageAliasLog::read( $any ? $filter['lines'] : self::COUNT_LINES );
        $out = array();
        foreach ( self::filterLog( $entries, $filter ) as $entry )
            $out[] = self::publicEntry( $entry );
        return $out;
    }

    /**
     * A log entry for the page: the parsed fields, the other fields as one short text.
     *
     * @param array $entry
     * @return array
     */
    public static function publicEntry( array $entry )
    {
        $extra = array();
        foreach ( isset( $entry['fields'] ) ? (array)$entry['fields'] : array() as $key => $value )
        {
            if ( in_array( $key, array( 'event', 'result', 'ms', 'alias', 'run' ), true ) )
                continue;
            $extra[] = $key . '=' . $value;
        }
        return array( 'time' => (int)$entry['time'], 'event' => (string)$entry['event'], 'result' => (string)$entry['result'],
                      'ms' => $entry['ms'] === null ? null : (int)$entry['ms'], 'alias' => (string)$entry['alias'],
                      'run' => (string)$entry['run'] === '-' ? '' : (string)$entry['run'], 'path' => (string)$entry['path'],
                      'siteaccess' => (string)$entry['siteaccess'], 'extra' => self::shortText( implode( ' ', $extra ), 400 ) );
    }

    /**
     * What the log says since a time: aliases generated, placeholders answered, failures, files staged, runs.
     *
     * @param array[] $entries expImageAliasLog::parse() results, newest first
     * @param int $since
     * @return array generated, placeholders, failures, staged, runs, lines (read), oldest (time of the oldest line
     *               read, 0 when the log is empty), complete (the lines read reach back to $since)
     */
    public static function logCounts( array $entries, $since )
    {
        $out = array( 'generated' => 0, 'placeholders' => 0, 'failures' => 0, 'staged' => 0, 'runs' => 0, 'lines' => 0,
                      'oldest' => 0, 'complete' => true );
        $reached = false;
        foreach ( $entries as $entry )
        {
            if ( !is_array( $entry ) || !isset( $entry['event'] ) )
                continue;
            $out['lines']++;
            $out['oldest'] = (int)$entry['time'];
            if ( (int)$entry['time'] < (int)$since )
            {
                $reached = true;
                continue;
            }
            switch ( $entry['event'] )
            {
                case 'generate':
                    if ( $entry['result'] === 'ok' )
                        $out['generated']++;
                    break;
                case 'placeholder':
                    $out['placeholders']++;
                    break;
                case 'fail':
                    $out['failures']++;
                    break;
                case 'stage':
                    if ( in_array( $entry['result'], array( 'fetched', 'replaced', 'adopted' ), true ) )
                        $out['staged']++;
                    break;
                case 'run-start':
                    $out['runs']++;
                    break;
            }
        }
        // Fewer lines than asked for: the whole log was read
        $out['complete'] = $reached || $out['lines'] < self::COUNT_LINES;
        return $out;
    }

    // ------------------------------------------------------------------------------------------------ dashboard

    /**
     * Everything the dashboard shows, read now.
     *
     * @param int|null $now
     * @return array see buildDashboard()
     */
    public static function dashboard( $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $staging = expImageAliasSettings::staging();
        $source = null;
        if ( $staging['sync_from'] !== '' )
        {
            $stager = new expImageAliasStager( $staging );
            $source = $stager->status() + array( 'credential' => expImageAliasSettings::source( $staging['sync_from'] )['credential'] );
        }
        $converters = array();
        try
        {
            $converters = expImageFormatConverter::capabilities();
        }
        catch ( Throwable $e )
        {
            $converters = array();
        }
        $key = array( 'exists' => false, 'modified' => 0, 'previous_until' => 0 );
        try
        {
            if ( class_exists( 'expSecrets' ) && expSecrets::installed() )
            {
                $names = expSecrets::names( 'image' );
                if ( isset( $names[expImageAliasUrl::SECRET] ) )
                    $key = array( 'exists' => true, 'modified' => (int)$names[expImageAliasUrl::SECRET]['modified'], 'previous_until' => 0 );
                if ( isset( $names[expImageAliasUrl::SECRET_PREVIOUS] ) )
                {
                    $keys = expImageAliasUrl::keys();
                    $key['previous_until'] = (int)$keys['previous_until'];
                }
            }
        }
        catch ( Throwable $e )
        {
        }
        $raw = array(
            'mode' => expImageAliasSettings::configuredMode(),
            'url_form' => expImageAliasSettings::urlForm(),
            'delivery' => expImageAliasSettings::delivery(),
            'max_wait' => expImageAliasSettings::maxWait(),
            'max_parallel' => expImageAliasSettings::maxParallel(),
            'busy' => expImageAliasThrottle::busy(),
            'negotiate' => expImageAliasSettings::negotiateEnabled(),
            'pre_generate' => expImageAliasSettings::preGenerateEnabled(),
            'placeholder' => expImageAliasSettings::placeholderEnabled(),
            'swap_script' => expImageAliasSettings::swapScriptEnabled(),
            'preferred' => expImageAliasSettings::preferredConverter(),
            'order' => expImageFormatConverter::order(),
            'converters' => $converters,
            'staging' => $staging,
            'source' => $source,
            'queue' => self::queueState(),
            'bulk_pause' => expImageAliasThrottle::bulkPause(),
            'counts' => self::logCounts( expImageAliasLog::read( self::COUNT_LINES ), $now - 86400 ),
            'key' => $key,
            'log_path' => expImageAliasLog::path(),
            'aliases' => count( self::knownAliases() ),
        );
        return self::buildDashboard( $raw, $now );
    }

    /**
     * The dashboard from what was read: the values as they are, and the verdicts the page shows as badges.
     *
     * @param array $raw see dashboard()
     * @param int $now
     * @return array the raw values plus checks (list of array( key, level ok|warn|bad|info )), source_state
     *               (none|ok|paused|warn), key_age_days, converter_rows (converter, formats, rank, preferred), queue_total
     */
    public static function buildDashboard( array $raw, $now )
    {
        $checks = array();
        if ( $raw['mode'] === expImageAliasSettings::MODE_RENDER )
            $checks[] = array( 'key' => 'render-mode', 'level' => 'info' );
        // converters: who writes which variant format
        $rows = array();
        $order = isset( $raw['order'] ) ? array_values( (array)$raw['order'] ) : array();
        foreach ( (array)$raw['converters'] as $converter => $formats )
        {
            $rank = array_search( $converter, $order, true );
            $rows[] = array( 'converter' => (string)$converter, 'formats' => array_values( (array)$formats ),
                             'rank' => $rank === false ? 99 : (int)$rank + 1, 'preferred' => $raw['preferred'] === $converter );
        }
        usort( $rows, function ( $a, $b ) {
            return $a['rank'] - $b['rank'];
        } );
        $writable = array();
        foreach ( $rows as $row )
            $writable = array_merge( $writable, $row['formats'] );
        if ( $raw['negotiate'] && !in_array( 'webp', $writable, true ) )
            $checks[] = array( 'key' => 'no-webp', 'level' => 'warn' );
        // staging
        $sourceState = 'none';
        $source = $raw['source'];
        if ( $raw['staging']['environment'] !== 'pro' && $raw['staging']['sync_from'] !== '' )
        {
            if ( !is_array( $source ) || (string)$source['base_url'] === '' )
            {
                $sourceState = 'warn';
                $checks[] = array( 'key' => 'source-no-url', 'level' => 'bad' );
            }
            else if ( !empty( $source['paused'] ) )
            {
                $sourceState = 'paused';
                $checks[] = array( 'key' => 'source-paused', 'level' => 'bad' );
            }
            else if ( !empty( $source['warning'] ) || (int)$source['failures'] > 0 )
            {
                $sourceState = 'warn';
                $checks[] = array( 'key' => 'source-failing', 'level' => 'warn' );
            }
            else
                $sourceState = 'ok';
        }
        else if ( $raw['staging']['environment'] !== 'pro' && $raw['staging']['sync_from'] === '' )
            $checks[] = array( 'key' => 'staging-no-source', 'level' => 'info' );
        // queue
        $total = array( 'waiting' => 0, 'running' => 0, 'dead' => 0 );
        foreach ( (array)$raw['queue']['types'] as $row )
        {
            foreach ( $total as $state => $count )
                $total[$state] = $count + (int)$row[$state];
        }
        if ( !$raw['queue']['available'] )
            $checks[] = array( 'key' => 'no-queue', 'level' => $raw['pre_generate'] ? 'warn' : 'info' );
        if ( $total['dead'] > 0 )
            $checks[] = array( 'key' => 'dead-jobs', 'level' => 'warn' );
        if ( $raw['bulk_pause'] !== null )
            $checks[] = array( 'key' => 'bulk-paused', 'level' => 'warn' );
        // the log
        if ( (int)$raw['counts']['failures'] > 0 )
            $checks[] = array( 'key' => 'failures', 'level' => 'warn' );
        // the signing key
        $keyAge = null;
        if ( !empty( $raw['key']['exists'] ) && (int)$raw['key']['modified'] > 0 )
        {
            $keyAge = (int)floor( max( 0, (int)$now - (int)$raw['key']['modified'] ) / 86400 );
            if ( $keyAge >= self::KEY_OLD_DAYS )
                $checks[] = array( 'key' => 'key-old', 'level' => 'info' );
        }
        else if ( $raw['mode'] === expImageAliasSettings::MODE_ON_DEMAND )
            $checks[] = array( 'key' => 'no-key', 'level' => 'info' );
        $worst = 'ok';
        foreach ( $checks as $check )
        {
            if ( $check['level'] === 'bad' || ( $check['level'] === 'warn' && $worst !== 'bad' ) )
                $worst = $check['level'];
        }
        return $raw + array( 'checks' => $checks, 'worst' => $worst, 'source_state' => $sourceState, 'key_age_days' => $keyAge,
                             'converter_rows' => $rows, 'queue_total' => $total,
                             'previous_key_valid' => (int)$raw['key']['previous_until'] > (int)$now );
    }

    // ------------------------------------------------------------------------------------------------ test tools

    /**
     * Checks the connection to a source with a HEAD request, with its credential when one is named. The credential is
     * sent, never shown: the answer has only the status, the type, the size and the time.
     *
     * @param string $environment the source's environment
     * @param string $path a storage path to ask for ('' for the source's base address)
     * @return array ok, url, status, type, length, ms, credential (none, sent, missing, unusable), result (ok,
     *               unauthorized, missing, down, timeout, http, no-url, bad-path, bad-environment)
     */
    public static function testSource( $environment, $path = '' )
    {
        $environment = strtolower( trim( (string)$environment ) );
        $out = array( 'ok' => false, 'environment' => $environment, 'url' => '', 'status' => 0, 'type' => '', 'length' => null,
                      'ms' => 0, 'credential' => 'none', 'result' => 'bad-environment' );
        if ( !in_array( $environment, expImageAliasStager::ENVIRONMENTS, true ) )
            return $out;
        $source = expImageAliasSettings::source( $environment );
        if ( $source['base_url'] === '' || !expImageAliasStager::validBaseUrl( $source['base_url'] ) )
        {
            $out['result'] = 'no-url';
            return $out;
        }
        $path = ltrim( trim( (string)$path ), '/' );
        if ( $path === '' )
            $url = $source['base_url'] . '/';
        else
        {
            if ( !expImageAliasUrl::validStoragePath( $path ) )
            {
                $out['result'] = 'bad-path';
                return $out;
            }
            $staging = array( 'environment' => $environment === 'dev' ? 'stage' : 'dev', 'sync_from' => $environment ) + expImageAliasSettings::staging();
            $stager = new expImageAliasStager( $staging, $source );
            $url = (string)$stager->sourceUrl( $path );
            if ( $url === '' )
            {
                $out['result'] = 'bad-path';
                return $out;
            }
        }
        $out['url'] = $url;
        $headers = array( 'User-Agent' => 'Exponential image staging (connection test)' );
        if ( $source['credential'] !== '' )
        {
            $value = class_exists( 'expSecrets' ) ? expSecrets::get( $source['credential'] ) : null;
            if ( $value === null || $value === '' )
                $out['credential'] = 'missing';
            else
            {
                $authorization = expImageAliasStager::authorizationFor( $value );
                if ( $authorization === false )
                    $out['credential'] = 'unusable';
                else
                {
                    $headers['Authorization'] = $authorization;
                    $out['credential'] = 'sent';
                }
            }
            unset( $value );
        }
        $started = microtime( true );
        $response = expImageAliasStager::request( 'HEAD', $url, $headers, (float)expImageAliasSettings::staging()['timeout'], 0 );
        unset( $headers );
        $out['ms'] = (int)round( ( microtime( true ) - $started ) * 1000 );
        $out['status'] = (int)$response['status'];
        $out['type'] = substr( (string)$response['type'], 0, 100 );
        $out['length'] = $response['length'] === null ? null : (int)$response['length'];
        $out['result'] = self::sourceVerdict( $out['status'], (string)$response['error'] );
        $out['ok'] = $out['result'] === 'ok';
        return $out;
    }

    /**
     * @param int $status
     * @param string $error timeout, down, too-large or ''
     * @return string ok, unauthorized, missing, down, timeout, redirect, http
     */
    public static function sourceVerdict( $status, $error )
    {
        if ( $error === 'timeout' )
            return 'timeout';
        if ( (int)$status === 0 )
            return 'down';
        if ( (int)$status >= 200 && (int)$status < 300 )
            return 'ok';
        if ( (int)$status === 401 || (int)$status === 403 )
            return 'unauthorized';
        if ( (int)$status === 404 || (int)$status === 410 )
            return 'missing';
        if ( (int)$status >= 300 && (int)$status < 400 )
            return 'redirect';
        return 'http';
    }

    /**
     * The images of one object (by node or object id): per image field and language its original and, for every
     * alias of the related siteaccesses, the URL a template gets now, the size and type, whether the file is there,
     * and the format variants.
     *
     * @param int $nodeID
     * @param int $objectID used when $nodeID is 0
     * @return array ok, error (no-object, no-access, no-image), object (id, name, node), images (list of array(
     *               attribute, version, language, identifier, name, original (url, width, height, mime, exists),
     *               aliases (list of array( alias, siteaccess, url, path, width, height, mime, exists, formats
     *               (format => present|missing|not-applicable) ) ) ) )
     */
    public static function testImage( $nodeID, $objectID = 0 )
    {
        $out = array( 'ok' => false, 'error' => 'no-object', 'object' => null, 'images' => array() );
        $object = null;
        $node = null;
        if ( (int)$nodeID > 0 )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$nodeID );
            $object = $node instanceof eZContentObjectTreeNode ? $node->object() : null;
        }
        else if ( (int)$objectID > 0 )
            $object = eZContentObject::fetch( (int)$objectID );
        if ( !$object instanceof eZContentObject )
            return $out;
        if ( !$object->canRead() )
        {
            $out['error'] = 'no-access';
            return $out;
        }
        $out['object'] = array( 'id' => (int)$object->attribute( 'id' ), 'name' => (string)$object->attribute( 'name' ),
                                'node' => $node ? (int)$node->attribute( 'node_id' ) : (int)$object->attribute( 'main_node_id' ),
                                'version' => (int)$object->attribute( 'current_version' ) );
        $plan = expImageAliasSettings::siteAccessAliasPlan();
        foreach ( $object->availableLanguages() as $language )
        {
            $dataMap = $object->fetchDataMap( false, $language );
            foreach ( is_array( $dataMap ) ? $dataMap : array() as $identifier => $attribute )
            {
                if ( !$attribute instanceof eZContentObjectAttribute || $attribute->attribute( 'data_type_string' ) !== 'ezimage' )
                    continue;
                $out['images'][] = self::imageReport( $attribute, (string)$identifier, $plan );
                if ( count( $out['images'] ) >= 6 )
                    break 2;
            }
        }
        $out['ok'] = (bool)$out['images'];
        $out['error'] = $out['ok'] ? '' : 'no-image';
        return $out;
    }

    /**
     * @param eZContentObjectAttribute $attribute
     * @param string $identifier
     * @param array $plan siteaccess => alias names
     * @return array see testImage()
     */
    protected static function imageReport( $attribute, $identifier, array $plan )
    {
        $handler = $attribute->attribute( 'content' );
        $report = array( 'attribute' => (int)$attribute->attribute( 'id' ), 'version' => (int)$attribute->attribute( 'version' ),
                         'language' => (string)$attribute->attribute( 'language_code' ), 'identifier' => $identifier,
                         'name' => (string)$attribute->attribute( 'contentclass_attribute_name' ),
                         'original' => null, 'aliases' => array() );
        if ( !$handler instanceof eZImageAliasHandler )
            return $report;
        $list = $handler->aliasList( false );
        if ( isset( $list['original']['url'] ) && (string)$list['original']['url'] !== '' )
        {
            $original = $list['original'];
            $report['original'] = array( 'url' => (string)$original['url'], 'width' => (int)$original['width'], 'height' => (int)$original['height'],
                                         'mime' => (string)$original['mime_type'], 'exists' => expImageAliasServer::exists( (string)$original['url'] ) );
        }
        if ( $report['original'] === null )
            return $report;
        foreach ( $plan as $siteAccess => $names )
        {
            foreach ( (array)$names as $name )
            {
                $entry = expImageAliasSettings::withSiteAccess( (string)$siteAccess, function () use ( $handler, $name ) {
                    return $handler->imageAlias( (string)$name );
                } );
                if ( !is_array( $entry ) )
                    continue;
                $path = isset( $entry['full_path'] ) && (string)$entry['full_path'] !== '' ? (string)$entry['full_path'] : (string)$entry['url'];
                $mime = isset( $entry['mime_type'] ) ? (string)$entry['mime_type'] : '';
                $formats = array();
                foreach ( expImageAliasSettings::aliasFormats( (string)$name ) as $format )
                {
                    if ( !expImageFormatConverter::variantApplies( $mime, $format ) )
                        $formats[$format] = 'not-applicable';
                    else
                        $formats[$format] = expImageAliasServer::exists( expImageFormatConverter::variantPath( $path, $format ) ) ? 'present' : 'missing';
                }
                $report['aliases'][] = array( 'alias' => (string)$name, 'siteaccess' => (string)$siteAccess, 'url' => (string)$entry['url'],
                                              'path' => $path, 'width' => (int)( $entry['width'] ?? 0 ), 'height' => (int)( $entry['height'] ?? 0 ),
                                              'mime' => $mime, 'exists' => $path !== '' && expImageAliasServer::exists( $path ),
                                              'formats' => $formats );
            }
        }
        return $report;
    }

    /**
     * Makes one alias of one image now (as a job would: the same lock, slot and stored data), with its formats.
     *
     * @param int $attributeID
     * @param int $version
     * @param string $language
     * @param string $alias
     * @param string $siteAccess the siteaccess whose image.ini defines the alias ('' this one)
     * @return array ok, result (present, ok, timeout, failed, unknown, no-image, gone, bad-alias), variants, ms
     */
    public static function makeOne( $attributeID, $version, $language, $alias, $siteAccess = '' )
    {
        $out = array( 'ok' => false, 'result' => 'bad-alias', 'variants' => array(), 'ms' => 0, 'alias' => (string)$alias );
        $plan = expImageAliasSettings::siteAccessAliasPlan();
        $siteAccess = (string)$siteAccess;
        if ( !isset( $plan[$siteAccess] ) || !in_array( (string)$alias, (array)$plan[$siteAccess], true ) )
        {
            // an alias of this siteaccess's own image.ini, not listed in its AliasList[]
            if ( $siteAccess !== '' || !expImageAliasUrl::validAliasName( (string)$alias ) )
                return $out;
        }
        if ( !expImageAliasUrl::validLanguage( (string)$language ) )
            return $out;
        $started = microtime( true );
        $server = expImageAliasServer::forJob();
        $attribute = $server->publishedImageAttribute( (int)$attributeID, (int)$version, (string)$language );
        if ( $attribute === null )
        {
            $out['result'] = 'gone';
            return $out;
        }
        $done = expImageAliasSettings::withSiteAccess( $siteAccess, function () use ( $server, $attribute, $alias ) {
            return $server->complete( $attribute, (string)$alias, array( 'webp', 'avif' ) );
        } );
        $out['ms'] = (int)round( ( microtime( true ) - $started ) * 1000 );
        $out['result'] = (string)$done['alias'];
        $out['variants'] = (array)$done['variants'];
        $out['ok'] = in_array( $out['result'], array( 'present', 'ok' ), true );
        return $out;
    }
}
