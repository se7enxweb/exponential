<?php
/**
 * File containing the expDebugBarSettings class: the values of the Exp Debug bar's settings, where they come from,
 * and the changes the bar makes to them.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * For a siteaccess (the current one by default):
 *
 * - describe(): the value in effect (eZINI's own, an uncached instance with that siteaccess's override
 *   directories, see expIniLocator::iniFor()), every file of the load order that sets it, the origin (the last of
 *   them), the default and the value each scope's file gives;
 * - write(): one change in one scope's file through expIniEditor (line by line, backup, owner kept), refused when
 *   it would lock the current user out of debug output or open it to every address unless confirmed, then logged
 *   (expDebugBarLog);
 * - undo(): the inverse of a logged write, refused when the file changed since;
 * - applyPreset(): the values of a preset, each a logged write of one group.
 *
 * Guide: doc/bc/6.0/debug-bar.md
 */
class expDebugBarSettings
{
    /** @var expDebugBarRegistry */
    protected $registry;
    /** @var expDebugBarLog */
    protected $log;
    /** @var string|null */
    protected $siteAccess;
    /** @var array file => chain */
    protected $chains = array();

    /** @var bool Clear the ini cache after a write (tests switch it off) */
    public $clearCache = true;
    /** @var string|null|false The address the lock-out check uses (null: the request's; false: none) */
    public $clientIP = null;
    /** @var int|null The user id the lock-out check uses (null: the current user) */
    public $userID = null;
    /** @var int|null Time used for expiries (null: now) */
    public $now = null;

    /** The ops a write can be. */
    public static $Ops = array( 'set', 'unset', 'toggle', 'add', 'remove', 'replace' );

    /**
     * @param expDebugBarRegistry|null $registry
     * @param string|null $siteAccess Context of the values in effect (null: the current siteaccess)
     * @param expDebugBarLog|null $log
     */
    public function __construct( ?expDebugBarRegistry $registry = null, $siteAccess = null, ?expDebugBarLog $log = null )
    {
        $this->registry = $registry !== null ? $registry : new expDebugBarRegistry();
        $this->log = $log !== null ? $log : new expDebugBarLog();
        if ( $siteAccess !== null && $siteAccess !== '' )
        {
            // refuses an unknown siteaccess (expIniException)
            expIniEditor::scope( 'siteaccess:' . $siteAccess );
            $this->siteAccess = $siteAccess;
        }
        else
            $this->siteAccess = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : null;
    }

    /** @return expDebugBarRegistry */
    public function registry() { return $this->registry; }
    /** @return expDebugBarLog */
    public function log() { return $this->log; }
    /** @return string|null */
    public function siteAccess() { return $this->siteAccess; }

    /** Forgets the files read, so the next describe() reads them again. */
    public function reset()
    {
        $this->chains = array();
    }

    // ------------------------------------------------------------------ scopes

    /**
     * The scopes the bar offers for a change: global, every siteaccess, every active extension (and its directory
     * for the context siteaccess when it has one).
     *
     * @return array[] name, kind, label, dir, writable, active
     */
    public function scopes()
    {
        $out = array();
        foreach ( expIniEditor::scopes() as $scope )
        {
            $kind = $scope->kind();
            if ( !$scope->policyWritable() )
                continue;
            if ( $scope->isExtension() && !$scope->isActive() )
                continue;
            if ( $kind === expIniScope::KIND_EXTENSION_SITEACCESS && ( $scope->siteAccess() !== $this->siteAccess || !$scope->exists() ) )
                continue;
            $out[] = array( 'name' => $scope->name(), 'kind' => $kind, 'label' => $scope->label(), 'dir' => $scope->dir(),
                            'writable' => $scope->exists() ? $scope->writable() : true, 'active' => $scope->isExtension() ? $scope->isActive() : true );
        }
        return $out;
    }

    /** The scope a change goes to unless one is chosen: the context siteaccess, else global. */
    public function defaultScope()
    {
        return $this->siteAccess !== null ? 'siteaccess:' . $this->siteAccess : 'global';
    }

    // ------------------------------------------------------------------ reading

    /**
     * The load order of one INI file for the context siteaccess: the eZINI instance and each file with its scope and
     * parsed lines.
     *
     * @param string $file 'site.ini'
     * @return array ini, files (rel, abs, scope, writer)
     */
    protected function chain( $file )
    {
        if ( isset( $this->chains[$file] ) )
            return $this->chains[$file];
        $ini = expIniLocator::iniFor( $file, $this->siteAccess );
        $inputFiles = array();
        $iniFile = null;
        $ini->findInputFiles( $inputFiles, $iniFile );
        $root = expIniEditor::root();
        $files = array();
        foreach ( $inputFiles as $path )
        {
            $abs = $path !== '' && $path[0] === '/' ? $path : $root . $path;
            if ( !is_file( $abs ) )
                continue;
            $rel = self::relative( $abs, $root );
            try
            {
                $writer = expIniWriter::fromFile( $abs );
            }
            catch ( Exception $e )
            {
                continue;
            }
            $files[] = array( 'rel' => $rel, 'abs' => realpath( $abs ) ?: $abs, 'scope' => expIniLocator::scopeOf( $rel ), 'writer' => $writer );
        }
        return $this->chains[$file] = array( 'ini' => $ini, 'files' => $files );
    }

    protected static function relative( $abs, $root )
    {
        $real = realpath( $abs );
        $realRoot = realpath( $root );
        $realRoot = ( $realRoot !== false ? $realRoot : rtrim( $root, '/' ) ) . '/';
        if ( $real !== false && strpos( $real, $realRoot ) === 0 )
            return substr( $real, strlen( $realRoot ) );
        return ltrim( preg_replace( '#^\./#', '', $abs ), '/' );
    }

    /**
     * Every setting described.
     *
     * @return array[]
     */
    public function describeAll()
    {
        $out = array();
        foreach ( $this->registry->settings() as $id => $def )
        {
            try
            {
                $out[] = $this->describe( $id );
            }
            catch ( Exception $e )
            {
                $out[] = self::publicDefinition( $def ) + array( 'error' => $e->getMessage() );
            }
        }
        return $out;
    }

    /**
     * One setting as the bar shows it (see the settings answer in the guide).
     *
     * @param string $id
     * @return array
     * @throws InvalidArgumentException for an unknown id
     */
    public function describe( $id )
    {
        $def = $this->definition( $id );
        $chain = $this->chain( $def['file'] );
        $secret = $def['type'] !== 'bool' && expIniEditor::isSecret( $def['variable'] );
        $files = array();
        $scopes = array();
        $default = null;
        foreach ( $chain['files'] as $f )
        {
            $values = $f['writer']->values();
            if ( !isset( $values[$def['block']] ) || !array_key_exists( $def['variable'], $values[$def['block']] ) )
                continue;
            $value = $values[$def['block']][$def['variable']];
            $files[] = array( 'scope' => $f['scope'], 'path' => $f['rel'], 'value' => $secret ? expIniEditor::maskValue( $value ) : $value );
            if ( $f['scope'] !== null )
                $scopes[$f['scope']] = $secret ? expIniEditor::maskValue( $value ) : $value;
            if ( $f['scope'] === 'default' )
                $default = $value;
        }
        foreach ( array( 'global', $this->defaultScope() ) as $name )
        {
            if ( !array_key_exists( $name, $scopes ) )
                $scopes[$name] = null;
        }
        $ini = $chain['ini'];
        $effective = $ini->hasVariable( $def['block'], $def['variable'] ) ? $ini->variable( $def['block'], $def['variable'] ) : null;
        $last = $files ? end( $files ) : null;
        $out = self::publicDefinition( $def ) + array(
            'secret' => $secret,
            'effective' => $secret ? expIniEditor::maskValue( $effective ) : $effective,
            'origin' => $last ? array( 'scope' => $last['scope'], 'path' => $last['path'],
                                       'kind' => self::kindOf( $last['scope'] ) ) : null,
            'default' => $secret ? expIniEditor::maskValue( $default ) : $default,
            'files' => $files,
            'scopes' => $scopes,
        );
        if ( $def['type'] === 'bool' )
        {
            list( $on, $off ) = expDebugBarRegistry::boolWords( $def, $effective !== null ? $effective : $default );
            $out['on'] = $on;
            $out['off'] = $off;
            $out['values'] = array( $off, $on );
            $out['is_on'] = expDebugBarRegistry::isOn( $effective );
        }
        if ( $def['type'] === 'iplist' && class_exists( 'expDebugBarIPList' ) )
            $out['entries'] = expDebugBarIPList::analyse( (array)$effective, $this->clientIP(), $this->now )['entries'];
        return $out;
    }

    protected static function publicDefinition( array $def )
    {
        return array( 'id' => $def['id'], 'file' => $def['file'], 'block' => $def['block'], 'variable' => $def['variable'],
                      'type' => $def['type'], 'label' => $def['label'], 'group' => $def['group'], 'help' => $def['help'],
                      'values' => $def['values'], 'on' => $def['on'], 'off' => $def['off'], 'extension' => $def['extension'],
                      'source' => $def['source'] );
    }

    protected static function kindOf( $scopeName )
    {
        if ( $scopeName === null )
            return null;
        foreach ( expIniEditor::scopes() as $s )
        {
            if ( $s->name() === $scopeName )
                return $s->kind();
        }
        return null;
    }

    /**
     * @param string $id
     * @return array
     * @throws InvalidArgumentException
     */
    public function definition( $id )
    {
        $def = $this->registry->setting( (string)$id );
        if ( $def === null )
            throw new InvalidArgumentException( "Unknown debug bar setting '$id'" );
        return $def;
    }

    /** The value in effect of a setting (unmasked), null when nothing sets it. */
    public function effective( $id )
    {
        $def = $this->definition( $id );
        $ini = $this->chain( $def['file'] )['ini'];
        return $ini->hasVariable( $def['block'], $def['variable'] ) ? $ini->variable( $def['block'], $def['variable'] ) : null;
    }

    /** The address of the request (or the one set for tests). */
    public function clientIP()
    {
        if ( $this->clientIP === false )
            return null;
        if ( $this->clientIP !== null )
            return $this->clientIP;
        $ip = class_exists( 'eZSys' ) ? eZSys::clientIP() : null;
        return $ip ? (string)$ip : null;
    }

    /** The user id of the request (or the one set for tests). */
    protected function currentUserID()
    {
        if ( $this->userID !== null )
            return (int)$this->userID;
        return class_exists( 'eZUser' ) ? (int)eZUser::currentUserID() : 0;
    }

    // ------------------------------------------------------------------ writing

    /**
     * Changes one setting in one scope.
     *
     * @param string $id Registry id
     * @param string $op set | unset | toggle | add | remove | replace
     * @param mixed $value string; array (replace); for an iplist add also array( address, label, expires )
     * @param string $scopeName
     * @param array $options confirm (false), dry_run (false), group (null), log (true)
     * @return array ok, dry_run, changed, entry, setting, diff, backup, warnings, needs_confirm, message
     * @throws InvalidArgumentException for a bad request, expIniException from the editor
     */
    public function write( $id, $op, $value, $scopeName, array $options = array() )
    {
        $options += array( 'confirm' => false, 'dry_run' => false, 'group' => null, 'log' => true );
        $def = $this->definition( $id );
        if ( !in_array( $op, self::$Ops, true ) )
            throw new InvalidArgumentException( "Unknown op '$op' (" . implode( ', ', self::$Ops ) . ')' );
        $scope = expIniEditor::scope( $scopeName );
        if ( !$scope->policyWritable() )
            throw new InvalidArgumentException( "The scope {$scope->name()} holds the shipped defaults; choose global, a siteaccess or an extension" );
        if ( $scope->isExtension() && !is_dir( $scope->root() . 'extension/' . $scope->extension() ) )
            throw new InvalidArgumentException( "The extension {$scope->extension()} does not exist" );

        $editor = new expIniEditor( $scope, $def['file'] );
        $before = $editor->originalContent();
        $existed = $editor->fileExists();
        $hadBlock = in_array( $def['block'], $editor->blocks(), true );
        $old = $editor->get( $def['block'], $def['variable'] );
        $oldReset = (bool)( new expIniWriter( $before ) )->variableLines( $def['block'], $def['variable'], 'reset' );
        $effectiveOld = $this->effective( $id );
        $warnings = array();
        $written = $this->applyOp( $editor, $def, $op, $value, $effectiveOld, $warnings );

        $answer = array( 'ok' => true, 'dry_run' => (bool)$options['dry_run'], 'changed' => $editor->hasChanges(),
                         'entry' => null, 'setting' => null, 'diff' => $editor->diff(), 'backup' => null,
                         'warnings' => $warnings, 'needs_confirm' => null, 'message' => '' );
        if ( !$editor->hasChanges() )
        {
            $answer['message'] = "Nothing to change: {$def['file']} [{$def['block']}] {$def['variable']} in {$scope->name()} already says so";
            $answer['setting'] = $this->describe( $id );
            return $answer;
        }

        $after = $this->simulate( $editor );
        $effectiveNew = self::valueIn( $after, $def['block'], $def['variable'] );
        $check = $this->accessCheck( $def, $after );
        if ( $check !== null )
        {
            $answer['warnings'][] = $check['message'];
            if ( !$options['confirm'] )
            {
                $answer['ok'] = false;
                $answer['needs_confirm'] = $check;
                $answer['message'] = $check['message'] . ' Nothing was written; confirm to go ahead.';
                return $answer;
            }
        }
        if ( $options['dry_run'] )
        {
            $answer['message'] = "Dry run: {$op} {$def['file']} [{$def['block']}] {$def['variable']} in {$scope->name()}, nothing written";
            return $answer;
        }

        $result = $editor->save();
        foreach ( $result->warnings() as $w )
            $answer['warnings'][] = $w;
        $answer['backup'] = $result->backup() ? self::relative( $result->backup(), expIniEditor::root() ) : null;
        $answer['warnings'] = array_merge( $answer['warnings'], $this->afterWrite() );
        $this->reset();

        $entry = array(
            'op' => $op, 'setting' => $id, 'file' => $def['file'], 'block' => $def['block'], 'variable' => $def['variable'],
            'type' => $def['type'], 'scope' => $scope->name(), 'siteaccess' => $this->siteAccess,
            'path' => $editor->relativePath(),
            'value' => $written,
            'old' => self::loggable( $def, $old ), 'old_reset' => $oldReset,
            'new' => self::loggable( $def, $editor->get( $def['block'], $def['variable'] ) ),
            'effective_old' => self::loggable( $def, $effectiveOld ), 'effective_new' => self::loggable( $def, $effectiveNew ),
            'backup' => $answer['backup'],
            'created_file' => !$existed, 'created_block' => !$hadBlock, 'created_variable' => $old === null,
            'sha_before' => sha1( $before ), 'sha_after' => sha1( $editor->content() ),
            'group' => $options['group'], 'undoes' => null,
        );
        if ( $options['log'] )
            $entry = $this->log->append( $entry );
        $answer['entry'] = $entry;
        $answer['setting'] = $this->describe( $id );
        $answer['message'] = ucfirst( $op ) . " {$def['file']} [{$def['block']}] {$def['variable']} in {$scope->name()}: {$editor->relativePath()}";
        return $answer;
    }

    /**
     * Applies an op to the editor.
     *
     * @return mixed The value written (the line added or removed, the list, the bool word)
     */
    protected function applyOp( expIniEditor $editor, array $def, $op, $value, $effective, array &$warnings )
    {
        $b = $def['block'];
        $v = $def['variable'];
        $isList = in_array( $def['type'], array( 'list', 'iplist', 'userlist' ), true );
        if ( $op === 'unset' )
        {
            if ( $editor->get( $b, $v ) !== null )
                $editor->remove( $b, $v );
            return null;
        }
        if ( $isList )
        {
            if ( $op === 'set' )
                $op = 'replace';
            if ( $op === 'toggle' )
                throw new InvalidArgumentException( "{$def['id']} is a list: use add, remove or replace" );
            if ( $op === 'replace' )
            {
                $list = is_array( $value ) ? $value : ( is_string( $value ) && $value !== '' ? json_decode( $value, true ) : array() );
                if ( !is_array( $list ) )
                    throw new InvalidArgumentException( 'replace needs a JSON array of values' );
                $lines = array();
                foreach ( $list as $item )
                    $lines[] = $this->listValue( $def, $item, $warnings );
                if ( $editor->get( $b, $v ) !== null )
                    $editor->remove( $b, $v );
                $editor->clearArray( $b, $v );
                foreach ( $lines as $line )
                    $editor->add( $b, $v, $line );
                return $lines;
            }
            if ( $op === 'add' )
            {
                $line = $this->listValue( $def, $value, $warnings );
                $editor->add( $b, $v, $line );
                return $line;
            }
            // remove: the exact line, or for an iplist the entry whose address is the one given
            $line = is_array( $value ) ? ( isset( $value['line'] ) ? $value['line'] : ( isset( $value['address'] ) ? $value['address'] : '' ) ) : trim( (string)$value );
            $current = (array)$editor->get( $b, $v );
            if ( !in_array( $line, $current, true ) && $def['type'] === 'iplist' )
            {
                foreach ( $current as $c )
                {
                    if ( is_string( $c ) && expDebugBarIPList::parseEntry( $c )['address'] === $line )
                    {
                        $line = $c;
                        break;
                    }
                }
            }
            $editor->remove( $b, $v, $line );
            return $line;
        }
        if ( in_array( $op, array( 'add', 'remove', 'replace' ), true ) )
            throw new InvalidArgumentException( "{$def['id']} is not a list: use set, toggle or unset" );
        if ( $def['type'] === 'bool' )
        {
            $reference = $effective !== null ? $effective : $editor->get( $b, $v );
            list( $on, $off ) = expDebugBarRegistry::boolWords( $def, $reference );
            if ( $op === 'toggle' )
                $word = expDebugBarRegistry::isOn( $effective ) ? $off : $on;
            else
            {
                $text = strtolower( trim( (string)$value ) );
                if ( in_array( $text, array( 'on', '1', 'true', 'enabled', 'yes' ), true ) )
                    $word = $on;
                else if ( in_array( $text, array( 'off', '0', 'false', 'disabled', 'no' ), true ) )
                    $word = $off;
                else
                    throw new InvalidArgumentException( "'$value' is not on or off" );
            }
            $editor->set( $b, $v, $word );
            return $word;
        }
        if ( $op === 'toggle' )
            throw new InvalidArgumentException( "{$def['id']} is not a bool: use set" );
        $value = is_scalar( $value ) ? (string)$value : '';
        if ( $def['type'] === 'enum' && $def['values'] && !in_array( $value, $def['values'], true ) )
            throw new InvalidArgumentException( "'$value' is not one of " . implode( ', ', $def['values'] ) );
        $editor->set( $b, $v, $value );
        return $value;
    }

    /**
     * One value of a list, validated: an IP list entry (address/CIDR with label and expiry), a user id, or one of
     * the allowed values.
     */
    protected function listValue( array $def, $item, array &$warnings )
    {
        if ( $def['type'] === 'iplist' )
        {
            if ( is_string( $item ) && strlen( $item ) && $item[0] === '{' )
                $item = json_decode( $item, true );
            if ( is_array( $item ) )
                return expDebugBarIPList::formatEntry( isset( $item['address'] ) ? $item['address'] : '',
                                                       isset( $item['label'] ) ? $item['label'] : '',
                                                       isset( $item['expires'] ) ? $item['expires'] : null, $this->now );
            $entry = expDebugBarIPList::parseEntry( (string)$item, $this->now );
            if ( !$entry['valid'] )
                throw new InvalidArgumentException( (string)$entry['error'] );
            if ( $entry['expired'] )
                $warnings[] = "'{$entry['line']}' has already expired";
            return $entry['line'];
        }
        $item = trim( is_scalar( $item ) ? (string)$item : '' );
        if ( $item === '' )
            throw new InvalidArgumentException( 'An empty value cannot be added' );
        if ( $def['type'] === 'userlist' )
        {
            if ( !ctype_digit( $item ) )
                throw new InvalidArgumentException( "'$item' is not a user id" );
            if ( class_exists( 'eZUser' ) && !eZUser::fetch( (int)$item ) )
                $warnings[] = "There is no user with id $item";
            return $item;
        }
        if ( $def['values'] && !in_array( $item, $def['values'], true ) )
            throw new InvalidArgumentException( "'$item' is not one of " . implode( ', ', $def['values'] ) );
        return $item;
    }

    /**
     * The values in effect after the pending change of $editor: the load order replayed line by line (as eZINI
     * reads it) with that file's new content. A file the load order does not have yet is placed where its scope
     * loads: an extension's after the defaults, a siteaccess's before the override, the override last.
     *
     * @return array block => variable => value
     */
    protected function simulate( expIniEditor $editor )
    {
        $chain = $this->chain( $editor->file() . '.ini' );
        $target = realpath( $editor->path() ) ?: $editor->path();
        $new = new expIniWriter( $editor->content() );
        $writers = array();
        $placed = false;
        foreach ( $chain['files'] as $f )
        {
            if ( $f['abs'] === $target )
            {
                $writers[] = $new;
                $placed = true;
            }
            else
                $writers[] = $f['writer'];
        }
        if ( !$placed )
        {
            $kind = $editor->getScope()->kind();
            $pos = count( $writers );
            foreach ( $chain['files'] as $i => $f )
            {
                $k = self::kindOf( $f['scope'] );
                if ( $kind === expIniScope::KIND_EXTENSION && $k !== 'default' ) { $pos = $i; break; }
                if ( $kind !== expIniScope::KIND_GLOBAL && $kind !== expIniScope::KIND_EXTENSION && $k === expIniScope::KIND_GLOBAL ) { $pos = $i; break; }
            }
            array_splice( $writers, $pos, 0, array( $new ) );
        }
        $values = array();
        foreach ( $writers as $w )
        {
            foreach ( $w->entries() as $e )
            {
                $b = $e['block'];
                $v = $e['var'];
                switch ( $e['type'] )
                {
                    case 'reset': $values[$b][$v] = array(); break;
                    case 'plain': $values[$b][$v] = $e['value']; break;
                    case 'append':
                        if ( isset( $values[$b][$v] ) && !is_array( $values[$b][$v] ) )
                            $values[$b][$v] = (array)$values[$b][$v];
                        $values[$b][$v][] = $e['value'];
                        break;
                    case 'hash':
                        if ( isset( $values[$b][$v] ) && !is_array( $values[$b][$v] ) )
                            $values[$b][$v] = (array)$values[$b][$v];
                        $values[$b][$v][$e['key']] = $e['value'];
                        break;
                }
            }
        }
        return $values;
    }

    protected static function valueIn( array $values, $block, $variable )
    {
        return isset( $values[$block] ) && array_key_exists( $variable, $values[$block] ) ? $values[$block][$variable] : null;
    }

    /**
     * Whether the values after a change lock the current request out of debug output (DebugByIP with a list that
     * no longer has its address, DebugByUser with a list that no longer has its user), or open it to every address.
     *
     * @param array $def
     * @param array $after block => variable => value
     * @return array|null reason lockout|open, message
     */
    protected function accessCheck( array $def, array $after )
    {
        if ( $def['file'] !== 'site.ini' || $def['block'] !== 'DebugSettings'
             || !in_array( $def['variable'], array( 'DebugByIP', 'DebugIPList', 'DebugByUser', 'DebugUserIDList', 'DebugOutput' ), true ) )
            return null;
        $chain = $this->chain( 'site.ini' );
        $ini = $chain['ini'];
        $beforeOf = function ( $var ) use ( $ini ) {
            return $ini->hasVariable( 'DebugSettings', $var ) ? $ini->variable( 'DebugSettings', $var ) : null;
        };
        $byIP = expDebugBarRegistry::isOn( self::valueIn( $after, 'DebugSettings', 'DebugByIP' ) );
        $list = (array)self::valueIn( $after, 'DebugSettings', 'DebugIPList' );
        $ip = $this->clientIP();
        if ( $byIP && $ip !== null )
        {
            $wasAllowed = !expDebugBarRegistry::isOn( $beforeOf( 'DebugByIP' ) ) || expDebugBarIPList::match( $ip, (array)$beforeOf( 'DebugIPList' ), $this->now ) !== null;
            if ( $wasAllowed && expDebugBarIPList::match( $ip, $list, $this->now ) === null )
                return array( 'reason' => 'lockout', 'message' => "With this change your address $ip would no longer get debug output (DebugByIP is enabled and the IP list does not include it)." );
        }
        $byUser = expDebugBarRegistry::isOn( self::valueIn( $after, 'DebugSettings', 'DebugByUser' ) );
        $userID = $this->currentUserID();
        if ( $byUser && $userID )
        {
            $users = array_map( 'strval', (array)self::valueIn( $after, 'DebugSettings', 'DebugUserIDList' ) );
            $wasAllowed = !expDebugBarRegistry::isOn( $beforeOf( 'DebugByUser' ) ) || in_array( (string)$userID, array_map( 'strval', (array)$beforeOf( 'DebugUserIDList' ) ), true );
            if ( $wasAllowed && !in_array( (string)$userID, $users, true ) )
                return array( 'reason' => 'lockout', 'message' => "With this change you (user $userID) would no longer get debug output (DebugByUser is enabled and the user list does not include you)." );
        }
        if ( $byIP && expDebugBarIPList::isOpen( $list, $this->now ) && !( expDebugBarRegistry::isOn( $beforeOf( 'DebugByIP' ) ) && expDebugBarIPList::isOpen( (array)$beforeOf( 'DebugIPList' ), $this->now ) ) )
            return array( 'reason' => 'open', 'message' => 'With this change every address would get debug output (the IP list has an entry with prefix /0).' );
        if ( !$byIP && !$byUser && $def['variable'] === 'DebugByIP' && expDebugBarRegistry::isOn( self::valueIn( $after, 'DebugSettings', 'DebugOutput' ) )
             && expDebugBarRegistry::isOn( $beforeOf( 'DebugByIP' ) ) )
            return array( 'reason' => 'open', 'message' => 'With this change every visitor would get debug output (DebugOutput is enabled and neither DebugByIP nor DebugByUser limits it).' );
        return null;
    }

    /** A value for the log: secrets masked. */
    protected static function loggable( array $def, $value )
    {
        return expIniEditor::isSecret( $def['variable'] ) ? expIniEditor::maskValue( $value ) : $value;
    }

    /**
     * After a write: clear the ini cache (unless switched off) and say where the change takes effect.
     *
     * @return string[] warnings
     */
    protected function afterWrite()
    {
        $warnings = array();
        if ( $this->clearCache )
        {
            try
            {
                if ( class_exists( 'expCacheManager' ) )
                {
                    $manager = new expCacheManager();
                    $r = $manager->clear( 'tag', array( 'ini' ) );
                    if ( !$r['ok'] )
                        $warnings[] = 'INI cache not cleared: ' . $r['message'];
                }
                else
                    eZCache::clearByTag( 'ini' );
            }
            catch ( Exception $e )
            {
                $warnings[] = 'INI cache not cleared: ' . $e->getMessage();
            }
        }
        $warnings[] = 'Velocity workers keep the settings of their warm-up: the change reaches Velocity after it restarts.';
        return $warnings;
    }

    // ------------------------------------------------------------------ undo

    /**
     * Reverts a logged write.
     *
     * @param string $entryId
     * @param bool $force Revert even though the file changed since that write
     * @param array $options group (null)
     * @return array ok, entry (the undo entry), byte_identical (null when the write created the file), setting, message
     * @throws InvalidArgumentException when the entry cannot be undone
     */
    public function undo( $entryId, $force = false, array $options = array() )
    {
        $this->log->reset();
        $entry = $this->log->find( (string)$entryId );
        if ( $entry === null )
            throw new InvalidArgumentException( "No debug bar log entry '$entryId'" );
        if ( !in_array( $entry['op'], self::$Ops, true ) || !empty( $entry['undoes'] ) )
            throw new InvalidArgumentException( "The entry $entryId ({$entry['op']}) cannot be undone" );
        if ( !empty( $entry['undone_by'] ) )
            throw new InvalidArgumentException( "The entry $entryId was already undone by {$entry['undone_by']}" );
        if ( is_string( $entry['old'] ) && $entry['old'] === expIniEditor::MASK || is_array( $entry['old'] ) && in_array( expIniEditor::MASK, $entry['old'], true ) )
            throw new InvalidArgumentException( "The entry $entryId changed a secret; its old value is not in the log" );
        $scope = expIniEditor::scope( $entry['scope'] );
        $editor = new expIniEditor( $scope, $entry['file'] );
        $before = $editor->originalContent();
        // "changed since": the setting in that file is no longer what the write left (other settings of the file
        // may have changed: they are not touched)
        if ( $editor->get( $entry['block'], $entry['variable'] ) !== $entry['new'] && !$force )
            throw new InvalidArgumentException( "{$entry['file']} [{$entry['block']}] {$entry['variable']} in {$entry['path']} changed since the entry $entryId was written; undo with force to revert it anyway" );
        $b = $entry['block'];
        $v = $entry['variable'];
        $old = $entry['old'];
        $has = $editor->get( $b, $v ) !== null;
        switch ( $entry['op'] )
        {
            case 'add':
                if ( in_array( $entry['value'], (array)$editor->get( $b, $v ), true ) )
                    $editor->remove( $b, $v, $entry['value'] );
                if ( $old === null && $editor->get( $b, $v ) === array() )
                    $editor->remove( $b, $v );
                break;
            case 'remove':
                // the list as it was, in its order
                $editor->remove( $b, $v );
                if ( !empty( $entry['old_reset'] ) )
                    $editor->clearArray( $b, $v );
                foreach ( (array)$old as $item )
                    $editor->add( $b, $v, (string)$item );
                break;
            default:
                // set, toggle, unset, replace: the file's old value again
                if ( !is_array( $old ) && $old !== null && !( $has && is_array( $editor->get( $b, $v ) ) ) )
                    $editor->set( $b, $v, $old );
                else
                {
                    if ( $has )
                        $editor->remove( $b, $v );
                    if ( is_array( $old ) )
                    {
                        if ( !empty( $entry['old_reset'] ) )
                            $editor->clearArray( $b, $v );
                        foreach ( $old as $item )
                            $editor->add( $b, $v, (string)$item );
                    }
                }
        }
        if ( !empty( $entry['created_block'] ) && in_array( $b, $editor->blocks(), true ) && !$editor->variables( $b ) )
        {
            try
            {
                $editor->removeBlock( $b, true );
            }
            catch ( expIniException $e )
            {
            }
        }
        $answer = array( 'ok' => true, 'entry' => null, 'byte_identical' => null, 'setting' => null, 'warnings' => array(), 'message' => '' );
        if ( !$editor->hasChanges() )
            $answer['message'] = "Nothing to undo: {$entry['path']} already has the old value";
        else
        {
            $result = $editor->save();
            $answer['warnings'] = array_merge( $result->warnings(), $this->afterWrite() );
            $answer['backup'] = $result->backup() ? self::relative( $result->backup(), expIniEditor::root() ) : null;
            $answer['message'] = "Undid {$entry['op']} of {$entry['file']} [$b] $v in {$entry['scope']}";
        }
        $this->reset();
        if ( !empty( $entry['backup'] ) && is_file( expIniEditor::root() . $entry['backup'] ) )
            $answer['byte_identical'] = @file_get_contents( expIniEditor::root() . $entry['backup'] ) === $editor->content();
        else if ( empty( $entry['created_file'] ) )
            $answer['byte_identical'] = sha1( $editor->content() ) === $entry['sha_before'];
        $undo = array( 'op' => 'undo', 'setting' => $entry['setting'], 'file' => $entry['file'], 'block' => $b, 'variable' => $v,
                       'type' => isset( $entry['type'] ) ? $entry['type'] : null, 'scope' => $entry['scope'],
                       'siteaccess' => $this->siteAccess, 'path' => $entry['path'], 'value' => null,
                       'old' => $entry['new'], 'new' => $editor->get( $b, $v ),
                       'backup' => isset( $answer['backup'] ) ? $answer['backup'] : null,
                       'sha_before' => sha1( $before ), 'sha_after' => sha1( $editor->content() ),
                       'byte_identical' => $answer['byte_identical'], 'forced' => (bool)$force,
                       'group' => isset( $options['group'] ) ? $options['group'] : null, 'undoes' => $entry['id'] );
        $answer['entry'] = $this->log->append( $undo );
        try
        {
            $answer['setting'] = $this->describe( $entry['setting'] );
        }
        catch ( InvalidArgumentException $e )
        {
        }
        return $answer;
    }

    /**
     * Reverts every write of a group (one preset), newest first.
     *
     * @param string $group
     * @param bool $force
     * @return array ok, entries, errors
     */
    public function undoGroup( $group, $force = false )
    {
        $this->log->reset();
        $out = array( 'ok' => true, 'entries' => array(), 'errors' => array() );
        $undoGroup = 'undo-' . $group;
        foreach ( $this->log->group( (string)$group ) as $entry )
        {
            if ( !empty( $entry['undone_by'] ) )
                continue;
            try
            {
                $r = $this->undo( $entry['id'], $force, array( 'group' => $undoGroup ) );
                $out['entries'][] = $r['entry'];
            }
            catch ( Exception $e )
            {
                $out['ok'] = false;
                $out['errors'][] = array( 'entry' => $entry['id'], 'message' => $e->getMessage() );
            }
        }
        return $out;
    }

    // ------------------------------------------------------------------ presets

    /**
     * The presets: the registry's and the user's own.
     *
     * @return array[]
     */
    public function presets()
    {
        return array_values( array_merge( $this->registry->presets(), self::userPresets() ) );
    }

    /** The current user's saved presets (preference expdebugbar_presets). */
    public static function userPresets()
    {
        if ( !class_exists( 'eZPreferences' ) )
            return array();
        $raw = eZPreferences::value( 'expdebugbar_presets' );
        $list = $raw ? json_decode( $raw, true ) : array();
        $out = array();
        foreach ( is_array( $list ) ? $list : array() as $p )
        {
            if ( is_array( $p ) && isset( $p['id'], $p['name'], $p['values'] ) && is_array( $p['values'] ) )
                $out[$p['id']] = array( 'id' => (string)$p['id'], 'name' => (string)$p['name'],
                                        'description' => isset( $p['description'] ) ? (string)$p['description'] : '',
                                        'values' => $p['values'], 'source' => 'user' );
        }
        return $out;
    }

    /**
     * Saves a user preset: the values given, or with $values null the bool and enum values in effect now.
     *
     * @param string $name
     * @param array|null $values setting id => value
     * @return array the preset
     */
    public function saveUserPreset( $name, ?array $values = null )
    {
        $name = trim( (string)$name );
        if ( $name === '' || strlen( $name ) > 80 )
            throw new InvalidArgumentException( 'A preset needs a name of at most 80 characters' );
        if ( $values === null )
        {
            $values = array();
            foreach ( $this->registry->settings() as $id => $def )
            {
                if ( !in_array( $def['type'], array( 'bool', 'enum' ), true ) || in_array( $def['group'], array( 'access' ), true ) )
                    continue;
                $e = $this->effective( $id );
                if ( is_string( $e ) )
                    $values[$id] = $def['type'] === 'bool' ? ( expDebugBarRegistry::isOn( $e ) ? 'on' : 'off' ) : $e;
            }
        }
        foreach ( $values as $id => $value )
        {
            $this->definition( $id );
            if ( !is_scalar( $value ) )
                throw new InvalidArgumentException( "The value of $id must be a string" );
        }
        $presets = self::userPresets();
        $id = 'user_' . substr( sha1( $name ), 0, 8 );
        $presets[$id] = array( 'id' => $id, 'name' => $name, 'description' => '', 'values' => $values, 'source' => 'user' );
        eZPreferences::setValue( 'expdebugbar_presets', json_encode( array_values( $presets ) ) );
        return $presets[$id];
    }

    /** Deletes a user preset. @return bool whether it existed */
    public function deleteUserPreset( $id )
    {
        $presets = self::userPresets();
        if ( !isset( $presets[$id] ) )
            return false;
        unset( $presets[$id] );
        eZPreferences::setValue( 'expdebugbar_presets', json_encode( array_values( $presets ) ) );
        return true;
    }

    /**
     * Applies a preset to a scope: each value a write of its own, all of one group. Values already in effect are
     * skipped. Nothing is written when one of the writes needs a confirmation that was not given.
     *
     * @param string $presetId
     * @param string $scopeName
     * @param array $options confirm, dry_run
     * @return array ok, group, entries, skipped (setting, why), needs_confirm, message
     */
    public function applyPreset( $presetId, $scopeName, array $options = array() )
    {
        $options += array( 'confirm' => false, 'dry_run' => false );
        $presets = array_merge( $this->registry->presets(), self::userPresets() );
        if ( !isset( $presets[$presetId] ) )
            throw new InvalidArgumentException( "Unknown preset '$presetId'" );
        $preset = $presets[$presetId];
        $group = 'preset-' . $presetId . '-' . expDebugBarLog::newId();
        $out = array( 'ok' => true, 'group' => $group, 'preset' => $preset, 'entries' => array(), 'skipped' => array(),
                      'needs_confirm' => null, 'message' => '' );
        $plan = array();
        foreach ( $preset['values'] as $id => $value )
        {
            $def = $this->registry->setting( $id );
            if ( $def === null )
            {
                $out['skipped'][] = array( 'setting' => $id, 'why' => 'unknown setting' );
                continue;
            }
            $effective = $this->effective( $id );
            if ( $def['type'] === 'bool' && is_string( $effective ) && expDebugBarRegistry::isOn( $effective ) === expDebugBarRegistry::isOn( (string)$value ) )
            {
                $out['skipped'][] = array( 'setting' => $id, 'why' => 'already in effect' );
                continue;
            }
            if ( $def['type'] !== 'bool' && $effective === $value )
            {
                $out['skipped'][] = array( 'setting' => $id, 'why' => 'already in effect' );
                continue;
            }
            $op = in_array( $def['type'], array( 'list', 'iplist', 'userlist' ), true ) ? 'replace' : 'set';
            $plan[] = array( $id, $op, $value );
        }
        // the confirmations first, so a preset is applied whole or not at all
        foreach ( $plan as $p )
        {
            $r = $this->write( $p[0], $p[1], $p[2], $scopeName, array( 'dry_run' => true, 'confirm' => $options['confirm'] ) );
            if ( $r['needs_confirm'] )
            {
                $out['ok'] = false;
                $out['needs_confirm'] = $r['needs_confirm'];
                $out['message'] = $r['message'];
                return $out;
            }
        }
        if ( $options['dry_run'] )
        {
            $out['message'] = 'Dry run: ' . count( $plan ) . ' settings would change';
            $out['plan'] = $plan;
            return $out;
        }
        foreach ( $plan as $p )
        {
            $r = $this->write( $p[0], $p[1], $p[2], $scopeName, array( 'confirm' => $options['confirm'], 'group' => $group ) );
            if ( $r['entry'] )
                $out['entries'][] = $r['entry'];
        }
        $out['message'] = "Preset {$preset['name']}: " . count( $out['entries'] ) . ' settings changed in ' . $scopeName;
        return $out;
    }
}
