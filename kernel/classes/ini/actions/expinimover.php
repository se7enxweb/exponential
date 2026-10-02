<?php
/**
 * File containing the expIniMover class.
 *
 * Moves settings blocks from one scope's file to another's, for exp:ini move and move-all: a block with its
 * comments and blank lines in order, every block of a file, or only some variables of a block. Variables the
 * target block already has are merged: the source's value wins, or the target's with --keep-target.
 *
 * Every file pair is its own transaction: the target is written first, then the block leaves the source (a
 * source that holds nothing more keeps its file and wrapper); then the value in effect of every moved variable
 * is read again for every siteaccess, and when one differs from before the move, both files are put back from
 * what was read before the write (a file the move created is moved aside to var/backup/ini/), unless --force.
 * The message names the file whose value now wins. Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniMover
{
    /** @var expIniCommandContext */
    private $c;

    /** @var array totals: blocks, variables, files, conflicts */
    private $totals = array( 'blocks' => 0, 'variables' => 0, 'files' => 0, 'conflicts' => 0 );

    /** @var array per file: file, source, target, blocks, variables, conflicts, written */
    private $report = array();

    public function __construct( expIniCommandContext $c )
    {
        $this->c = $c;
    }

    /** @return array the totals */
    public function totals()
    {
        return $this->totals;
    }

    /** @return array what happened per file */
    public function report()
    {
        return $this->report;
    }

    // ── The scopes ──────────────────────────────────────────────────────────

    /**
     * The source scope: any scope but the kernel's defaults.
     *
     * @param string $spec
     * @return expIniScope
     * @throws expIniException
     */
    public function sourceScope( $spec )
    {
        $scope = $this->c->scope( $spec );
        if ( $scope->kind() === expIniScope::KIND_DEFAULT )
            throw expIniException::refused( 'the default scope (settings/<file>.ini) is the kernel\'s own and is not moved' );
        if ( !$scope->exists() )
            throw expIniException::notFound( 'scope ' . $scope->name() . ' has no directory (' . $scope->dir() . ')' );
        return $scope;
    }

    /**
     * The target scope; an extension that does not exist is created with --create-extension (asked for
     * otherwise), and activated with --activate (the command that does it is printed otherwise).
     *
     * @param string $spec
     * @param expIniScope $from
     * @return expIniScope
     * @throws expIniException
     */
    public function targetScope( $spec, expIniScope $from )
    {
        $scope = $this->c->writeScope( $spec );
        if ( $scope->name() === $from->name() )
            throw expIniException::usage( 'move needs two different scopes' );

        $ext = $scope->extension();
        if ( !$scope->isExtension() || !$ext )
            return $scope;

        if ( !is_dir( expIniEditor::root() . 'extension/' . $ext ) )
        {
            if ( !$this->c->option( 'create-extension' ) )
                throw expIniException::refused( "the extension $ext does not exist (no extension/$ext): add --create-extension to create it "
                                                . '(extension.xml, ezinfo.php, settings/), and --activate to activate it' );
            $this->createExtension( $ext );
            $scope = $this->rescope( $spec );
        }

        if ( !$this->isActive( $ext, $scope ) )
        {
            $activation = $this->activationCommand( $ext, $scope );
            if ( $this->c->option( 'activate' ) )
            {
                $this->activate( $ext, $scope );
                $scope = $this->rescope( $spec );
            }
            else if ( $this->c->isDryRun() )
                $this->c->warn( "the extension $ext is not active: its settings are not read. Activate it with: $activation (or --activate)" );
            else if ( !$this->c->option( 'force' ) )
                throw expIniException::refused( "the extension $ext is not active, so nothing moved into it would be read. "
                                                . "Activate it with: $activation (or add --activate)" );
        }
        return $scope;
    }

    /** The scope again, after the scope list changed. */
    private function rescope( $spec )
    {
        expIniEditor::resetScopes();
        return $this->c->scope( $spec );
    }

    /** @return bool whether the extension's settings are read for the target */
    private function isActive( $ext, expIniScope $scope )
    {
        if ( in_array( $ext, expIniEditor::activeExtensions(), true ) )
            return true;
        if ( self::isSiteAccessTarget( $scope ) )
        {
            $sa = expIniEditor::scope( 'siteaccess:' . $scope->siteAccess() );
            $value = ( new expIniEditor( $sa, 'site' ) )->get( 'ExtensionSettings', 'ActiveAccessExtensions' );
            return is_array( $value ) && in_array( $ext, $value, true );
        }
        return false;
    }

    /**
     * Whether a target is an extension's directory for one siteaccess: activated there by the siteaccess's
     * ActiveAccessExtensions[], not by ActiveExtensions[] in global.
     *
     * @param expIniScope $scope
     * @return bool
     */
    private static function isSiteAccessTarget( expIniScope $scope )
    {
        return $scope->kind() === expIniScope::KIND_EXTENSION_SITEACCESS && $scope->siteAccess();
    }

    /** @return string the exp:ini command that activates the extension for the target */
    public function activationCommand( $ext, expIniScope $scope )
    {
        if ( self::isSiteAccessTarget( $scope ) )
            return "exp:ini add site.ini/ExtensionSettings/ActiveAccessExtensions[] $ext siteaccess:" . $scope->siteAccess();
        return "exp:ini add site.ini/ExtensionSettings/ActiveExtensions[] $ext global";
    }

    /**
     * Activates the extension through the editor: ActiveExtensions[] in global, ActiveAccessExtensions[] in
     * the siteaccess of an extension siteaccess target.
     */
    private function activate( $ext, expIniScope $scope )
    {
        $siteAccessTarget = self::isSiteAccessTarget( $scope );
        $where = expIniEditor::scope( $siteAccessTarget ? 'siteaccess:' . $scope->siteAccess() : 'global' );
        $variable = $siteAccessTarget ? 'ActiveAccessExtensions' : 'ActiveExtensions';
        $editor = new expIniEditor( $where, 'site' );
        $editor->add( 'ExtensionSettings', $variable, $ext );
        if ( $this->c->isDryRun() )
        {
            foreach ( explode( "\n", rtrim( $editor->diff(), "\n" ) ) as $l )
                $this->c->line( $l );
            $this->c->line( "Dry run: would activate $ext (" . $this->activationCommand( $ext, $scope ) . ')' );
            return;
        }
        $result = $editor->save( array( 'backup' => (bool)$this->c->option( 'backup' ) ) );
        $this->c->line( "Activated $ext: " . $variable . "[] in " . $result->relativePath() );
        $this->c->data( 'activated', $ext );
        if ( !expIniEditor::isRealRoot() )
            expIniCommand::useRoot( rtrim( expIniEditor::root(), '/' ) );
        else
        {
            // eZINI and eZExtension of this process have the old list: config.php may switch the INI mtime
            // checks off, so the ini cache goes too, and the comparison of the values in effect sees the
            // extension's settings
            list( , $text ) = $this->c->clearIniCache();
            $this->c->line( $text );
            eZINI::resetInstance( 'site.ini' );
            eZExtension::clearActiveExtensionsMemoryCache();
            expIniEditor::resetScopes();
        }
    }

    /**
     * Creates extension/<ext>/ with settings/, an extension.xml and an ezinfo.php, owned like extension/.
     *
     * @param string $ext
     * @throws expIniException
     */
    public function createExtension( $ext )
    {
        if ( !preg_match( '/^[a-z0-9_]+$/', $ext ) )
            throw expIniException::usage( "an extension name is made of a-z, 0-9 and _: \"$ext\"" );
        $base = expIniEditor::root() . 'extension';
        $dir = "$base/$ext";
        if ( $this->c->isDryRun() )
        {
            $this->c->line( "Dry run: would create extension/$ext/ (extension.xml, ezinfo.php, settings/)" );
            $this->c->data( 'created_extension', $ext );
            return;
        }
        if ( !is_dir( $base ) && !mkdir( $base, 0755, true ) )
            throw expIniException::writeFailed( "cannot create $base" );

        $owner = fileowner( $base );
        $group = filegroup( $base );
        $own = function ( $path ) use ( $owner, $group ) {
            if ( expIniEditor::isRoot() )
            {
                @chown( $path, $owner );
                @chgrp( $path, $group );
            }
        };
        foreach ( array( $dir, "$dir/settings" ) as $d )
        {
            if ( !is_dir( $d ) && !mkdir( $d, 0755 ) )
                throw expIniException::writeFailed( "cannot create $d" );
            $own( $d );
        }
        foreach ( array( 'extension.xml' => self::extensionXml( $ext ), 'ezinfo.php' => self::ezinfo( $ext ) ) as $name => $content )
        {
            if ( file_put_contents( "$dir/$name", $content ) === false )
                throw expIniException::writeFailed( "cannot write $dir/$name" );
            chmod( "$dir/$name", 0644 );
            $own( "$dir/$name" );
        }
        $this->c->line( "Created extension/$ext/ (extension.xml, ezinfo.php, settings/); it has no classes, so no autoloads to generate" );
        $this->c->data( 'created_extension', $ext );
    }

    /** @return string the extension.xml of a new extension */
    public static function extensionXml( $ext )
    {
        return "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n<software>\n    <metadata>\n        <name>$ext</name>\n"
             . "        <version>1.0.0</version>\n"
             . "        <copyright>Copyright (C) 1998 - 2026 7x &amp; Exponential Foundation. All rights reserved.</copyright>\n"
             . "        <license>GNU General Public License v2.0 (or any later version)</license>\n"
             . "    </metadata>\n</software>\n";
    }

    /** @return string the ezinfo.php of a new extension */
    public static function ezinfo( $ext )
    {
        return "<?php\n/**\n * File containing the {$ext}Info class.\n *\n"
             . " * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.\n"
             . " * @license GNU General Public License v2.0 (or any later version)\n * @package $ext\n */\n\n"
             . "class {$ext}Info\n{\n    public static function info()\n    {\n        return array(\n"
             . "            'Name' => \"$ext\",\n            'Version' => \"1.0.0\",\n"
             . "            'Copyright' => \"Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.\",\n"
             . "            'License' => \"GNU General Public License v2.0 (or any later version)\"\n        );\n    }\n}\n";
    }

    // ── The files of a scope ────────────────────────────────────────────────

    /**
     * The INI files a scope's directory holds (*.ini.append.php, *.ini.append), by base name.
     *
     * @param expIniScope $scope
     * @return string[]
     */
    public static function filesOf( expIniScope $scope )
    {
        $files = array();
        foreach ( (array)glob( $scope->absoluteDir() . '/*.ini.append*' ) as $path )
            if ( is_file( $path ) && preg_match( '#/([^/]+)\.ini\.append(\.php)?$#', $path, $m ) )
                $files[$m[1]] = true;
        $files = array_keys( $files );
        sort( $files );
        return $files;
    }

    // ── Moving ──────────────────────────────────────────────────────────────

    /**
     * Moves one file's blocks (all, or one) from one scope to another, as one transaction.
     *
     * @param expIniScope $from
     * @param expIniScope $to
     * @param string $file
     * @param string|null $block null: every block
     * @return int exit code
     */
    public function moveFile( expIniScope $from, expIniScope $to, $file, $block = null )
    {
        $c = $this->c;
        $source = $c->editor( $from, $file );
        $target = $c->editor( $to, $file );
        if ( !$source->fileExists() )
            return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, 'Not found: ' . $source->relativePath() . ' does not exist' );

        $blocks = $block === null ? $source->blocks() : array( $block );
        if ( $block !== null && $source->blockLines( $block ) === null )
            return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, "Not found: no block [$block] in " . $source->relativePath() );
        if ( !$blocks )
        {
            $c->line( 'Nothing to move: ' . $source->relativePath() . ' has no blocks' );
            return expIniCommandContext::EXIT_OK;
        }

        $only = $this->onlyOption();
        $moved = array();
        $conflicts = array();
        foreach ( $blocks as $b )
        {
            $names = $this->moveBlock( $source, $target, $to, $file, $b, $only, $conflicts );
            if ( is_int( $names ) )
                return $names;
            if ( $names !== null )
                $moved[$b] = $names;
        }

        $this->printConflicts( $conflicts );
        $entry = array( 'file' => $file, 'source' => $source->relativePath(), 'target' => $target->relativePath(),
                        'blocks' => array_keys( $moved ), 'variables' => self::variableCount( $moved ), 'conflicts' => $conflicts,
                        'written' => false, 'source_empty' => $source->isEmpty() );

        if ( $c->isDryRun() )
        {
            $c->printDiff( $target->diff(), true );
            $c->printDiff( $source->diff(), true );
            $entry['diff'] = $c->maskText( $target->diff() . $source->diff() );
            $this->count( $entry );
            return expIniCommandContext::EXIT_OK;
        }

        return $this->commitPair( $from, $to, $file, $source, $target, $moved, $entry );
    }

    /** @return string[]|null the variables of --only, null when it is not given */
    private function onlyOption()
    {
        $only = $this->c->option( 'only' );
        return $only !== null ? array_values( array_filter( array_map( 'trim', explode( ',', $only ) ), 'strlen' ) ) : null;
    }

    /**
     * Moves one block (pending in both editors): merges what the target block already has, writes the source's
     * lines to the target, and takes them out of the source.
     *
     * @param expIniEditor $source
     * @param expIniEditor $target
     * @param expIniScope $to
     * @param string $file
     * @param string $block
     * @param string[]|null $only --only
     * @param array $conflicts collects the merge conflicts
     * @return string[]|int|null the moved variables; null when nothing of the block moves; an exit code when
     *                           a variable of --only is not there
     */
    private function moveBlock( $source, $target, expIniScope $to, $file, $block, $only, array &$conflicts )
    {
        $sourceVars = (array)$source->variables( $block );
        $names = array_keys( $sourceVars );
        if ( $only !== null )
        {
            $missing = array_diff( $only, $names );
            if ( $missing )
                return $this->c->finish( expIniCommandContext::EXIT_NOT_FOUND, 'Not found: [' . $block . '] of ' . $source->relativePath()
                                                                              . ' has no ' . implode( ', ', $missing ) );
            $names = $only;
        }
        $blockOnly = $only;
        $stay = self::activationLists( $file, $block, $to, $names );
        if ( $stay )
        {
            $this->c->warn( implode( ', ', $stay ) . ' of [ExtensionSettings] stay in ' . $source->relativePath()
                            . ': an extension cannot activate itself' );
            $names = array_values( array_diff( $names, $stay ) );
            if ( !$names )
                return null;
            $blockOnly = $names;
        }

        $drop = $this->merge( $target, $block, $names, $sourceVars, $conflicts );
        $lines = self::selectLines( (array)$source->blockLines( $block ), $blockOnly === null ? null : array_diff( $names, $drop ), $drop );
        if ( self::hasSetting( $lines ) || $target->blockLines( $block ) === null )
            $target->insertBlockLines( $block, $lines );

        if ( $blockOnly === null )
            $source->removeBlock( $block );
        else
        {
            foreach ( $names as $name )
                $source->remove( $block, $name );
            if ( !$source->variables( $block ) )
                $source->removeBlock( $block, true );
        }
        return $names;
    }

    /**
     * The lists that activate extensions among the variables of a block moving into an extension: they are
     * never read from inside one, so they stay where they are.
     *
     * @return string[] ActiveExtensions and/or ActiveAccessExtensions, when they are among $names
     */
    private static function activationLists( $file, $block, expIniScope $to, array $names )
    {
        if ( $file !== 'site' || $block !== 'ExtensionSettings' || !$to->isExtension() )
            return array();
        return array_values( array_intersect( $names, array( 'ActiveExtensions', 'ActiveAccessExtensions' ) ) );
    }

    /**
     * The variables both blocks have: an equal value needs no line, a different one is a conflict. The source
     * wins (the target's line is removed), or the target with --keep-target.
     *
     * @return string[] the variables whose source lines are not written to the target
     */
    private function merge( $target, $block, array $names, array $sourceVars, array &$conflicts )
    {
        $keepTarget = (bool)$this->c->option( 'keep-target' );
        $targetVars = $target->blockLines( $block ) !== null ? (array)$target->variables( $block ) : array();
        $drop = array();
        foreach ( $names as $name )
        {
            if ( !array_key_exists( $name, $targetVars ) )
                continue;
            if ( $targetVars[$name] === $sourceVars[$name] )
            {
                $drop[] = $name;
                continue;
            }
            $conflicts[] = array( 'block' => $block, 'variable' => $name,
                                  'source' => $this->c->display( $name, $sourceVars[$name] ),
                                  'target' => $this->c->display( $name, $targetVars[$name] ),
                                  'kept' => $keepTarget ? 'target' : 'source' );
            if ( $keepTarget )
                $drop[] = $name;
            else
                $target->remove( $block, $name );
        }
        return $drop;
    }

    /** One line per merge conflict: the two values and which one was kept. */
    private function printConflicts( array $conflicts )
    {
        foreach ( $conflicts as $conflict )
            $this->c->line( sprintf( 'Conflict: [%s] %s: source %s, target %s -> the %s\'s kept', $conflict['block'], $conflict['variable'],
                                     self::shown( $conflict['source'] ), self::shown( $conflict['target'] ), $conflict['kept'] ) );
    }

    /** @return int the number of variables moved: block => names */
    private static function variableCount( array $moved )
    {
        $variables = 0;
        foreach ( $moved as $names )
            $variables += count( $names );
        return $variables;
    }

    /** @return string a value as the messages quote it (JSON, slashes and Unicode as they are) */
    private static function shown( $value )
    {
        return json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    }

    /** Adds a file's numbers to the totals and its entry to the report. */
    private function count( array $entry )
    {
        $this->report[] = $entry;
        $this->totals['blocks'] += count( $entry['blocks'] );
        $this->totals['variables'] += $entry['variables'];
        $this->totals['files'] += 1;
        $this->totals['conflicts'] += count( $entry['conflicts'] );
    }

    /**
     * Writes the target, then the source, then compares the values in effect; puts both back when the
     * second write fails or a value changed (unless --force).
     */
    private function commitPair( $from, $to, $file, $source, $target, array $moved, array $entry )
    {
        $c = $this->c;
        $siteAccesses = self::siteAccesses();
        $before = $this->effective( $file, $moved, $siteAccesses );
        $options = array( 'backup' => (bool)$c->option( 'backup' ), 'create' => true, 'createScope' => true );
        $targetExisted = $target->fileExists();
        $targetOriginal = $target->originalContent();
        $sourceOriginal = $source->originalContent();

        try
        {
            $targetResult = $target->save( $options );
        }
        catch ( expIniException $e )
        {
            return $c->finish( $e->getCode() ?: expIniCommandContext::EXIT_WRITE_FAILED, 'Not moved: ' . $e->getMessage() );
        }
        try
        {
            $sourceResult = $source->save( $options );
        }
        catch ( expIniException $e )
        {
            $this->restore( $target->path(), $targetExisted, $targetOriginal, $target->relativePath() );
            return $c->finish( expIniCommandContext::EXIT_WRITE_FAILED, 'Not moved: ' . $e->getMessage()
                                                                        . '; ' . $target->relativePath() . ' was put back' );
        }

        // a directory the write created (an extension siteaccess directory) is a scope now
        expIniEditor::resetScopes();
        $after = $this->effective( $file, $moved, $siteAccesses );
        $changed = array_keys( array_filter( $before, function ( $value, $key ) use ( $after ) {
            return $after[$key] !== $value;
        }, ARRAY_FILTER_USE_BOTH ) );

        if ( $changed )
        {
            $why = $this->whyChanged( $file, $changed[0], $before, $after, $target );
            $more = count( $changed ) > 1 ? ' and ' . ( count( $changed ) - 1 ) . ' other value(s) in effect' : '';
            $c->data( 'changed_values', array_map( function ( $k ) { return str_replace( "\x1f", ' ', $k ); }, $changed ) );
            if ( !$c->option( 'force' ) )
            {
                $this->restore( $source->path(), true, $sourceOriginal, $source->relativePath() );
                $this->restore( $target->path(), $targetExisted, $targetOriginal, $target->relativePath() );
                return $c->finish( expIniCommandContext::EXIT_REFUSED, "Refused and rolled back: $why$more. "
                                                                      . 'Both files are as they were; --force keeps such a move' );
            }
            $c->warn( "kept with --force: $why$more" );
        }

        $entry['written'] = true;
        $entry['backups'] = array_values( array_filter( array( $targetResult->backup(), $sourceResult->backup() ) ) );
        foreach ( array_merge( (array)$targetResult->warnings(), (array)$sourceResult->warnings() ) as $w )
            $c->warn( $w );
        $c->line( sprintf( 'Moved %s, %s: %s -> %s%s', expIniCommandContext::counted( count( $entry['blocks'] ), 'block' ),
                           expIniCommandContext::counted( $entry['variables'], 'variable' ), $entry['source'], $entry['target'],
                           $entry['source_empty'] ? ' (the source holds nothing more; its file stays)' : '' ) );
        $this->count( $entry );
        return expIniCommandContext::EXIT_OK;
    }

    /**
     * Why a move is refused: which value in effect would change, from what to what, and the file that now wins.
     *
     * @param string $file
     * @param string $key "block\x1fvariable\x1fsiteaccess", see effective()
     * @param array $before effective() before the write
     * @param array $after effective() after the write
     * @param expIniEditor $target
     * @return string
     */
    private function whyChanged( $file, $key, array $before, array $after, $target )
    {
        list( $b, $v, $sa ) = explode( "\x1f", $key );
        $winner = self::winningFile( $file, $b, $v, $sa === '' ? null : $sa );
        return sprintf( '%s.ini [%s] %s in effect%s would change from %s to %s%s', $file, $b, $v,
                        $sa === '' ? '' : " for siteaccess $sa",
                        self::shown( $this->c->display( $v, $before[$key] ) ),
                        self::shown( $this->c->display( $v, $after[$key] ) ),
                        $winner !== null ? " ($winner wins: it loads after " . $target->relativePath() . ')' : '' );
    }

    /**
     * Puts a file back as it was read: the bytes written over the same file (owner and mode stay), or a file the
     * move created moved aside to var/backup/ini/<stamp>-rollback/ (never deleted).
     */
    private function restore( $path, $existed, $original, $relative )
    {
        if ( $existed )
        {
            if ( file_put_contents( $path, $original, LOCK_EX ) === false )
                $this->c->warn( "could not put $relative back: restore it from its backup in var/backup/ini/" );
            return;
        }
        if ( !is_file( $path ) )
            return;
        $aside = expIniEditor::root() . 'var/backup/ini/' . date( 'Ymd-His' ) . '-rollback/' . $relative;
        if ( !is_dir( dirname( $aside ) ) )
            mkdir( dirname( $aside ), 0700, true );
        if ( !rename( $path, $aside ) )
            $this->c->warn( "could not move the new $relative aside" );
        else
            $this->c->line( "Rolled back: the new $relative is moved aside to " . substr( $aside, strlen( expIniEditor::root() ) ) );
    }

    /** @return array the siteaccesses whose values in effect are compared: every one ('' only when there is none) */
    public static function siteAccesses()
    {
        $list = array();
        foreach ( expIniEditor::scopes() as $scope )
            if ( $scope->kind() === expIniScope::KIND_SITEACCESS && $scope->siteAccess() )
                $list[] = $scope->siteAccess();
        return $list ? array_values( array_unique( $list ) ) : array( '' );
    }

    /**
     * The values in effect of the moved variables, for every siteaccess.
     *
     * @return array "block\x1fvariable\x1fsiteaccess" => value
     */
    private function effective( $file, array $moved, array $siteAccesses )
    {
        $values = array();
        foreach ( $siteAccesses as $sa )
        {
            $ini = null;
            if ( expIniEditor::isRealRoot() )
            {
                try
                {
                    $ini = expIniLocator::iniFor( $file . '.ini', $sa === '' ? null : $sa );
                }
                catch ( Exception $e )
                {
                    $ini = null;
                }
            }
            foreach ( $moved as $b => $names )
                foreach ( $names as $v )
                {
                    if ( $ini !== null )
                        $value = $ini->hasVariable( $b, $v ) ? $ini->variable( $b, $v ) : null;
                    else
                        $value = expIniEditor::effectiveValue( $file, $b, $v, $sa === '' ? null : $sa );
                    $values["$b\x1f$v\x1f$sa"] = $value;
                }
        }
        return $values;
    }

    /**
     * The file whose value of a variable is in effect: the last one in load order that sets it.
     *
     * @return string|null relative path
     */
    public static function winningFile( $file, $block, $variable, $siteAccess )
    {
        try
        {
            if ( expIniEditor::isRealRoot() )
            {
                $where = expIniLocator::where( $file, $block, $variable, $siteAccess );
                $last = end( $where['files'] );
                return $last ? $last['path'] : null;
            }
            // the editor's chain under another root (expIniEditor::effectiveValue())
            $order = array( 'default' );
            foreach ( array_reverse( expIniEditor::activeExtensions() ) as $ext )
                $order[] = "extension:$ext";
            if ( $siteAccess !== null )
            {
                $order[] = "siteaccess:$siteAccess";
                foreach ( expIniEditor::activeExtensions() as $ext )
                    $order[] = "extension:$ext:siteaccess:$siteAccess";
            }
            $order[] = 'global';
            $byName = array();
            foreach ( expIniEditor::scopes() as $s )
                $byName[$s->name()] = $s;
            $winner = null;
            foreach ( $order as $name )
                if ( isset( $byName[$name] ) && ( new expIniEditor( $byName[$name], $file ) )->get( $block, $variable ) !== null )
                    $winner = $byName[$name]->relativePath( $file );
            return $winner;
        }
        catch ( Exception $e )
        {
            return null;
        }
    }

    // ── Lines ───────────────────────────────────────────────────────────────

    /**
     * The variable a block line sets, or null for a comment or a blank line.
     *
     * @param string $line
     * @return string|null
     */
    public static function variableOf( $line )
    {
        $t = trim( $line );
        if ( $t === '' || $t[0] === '#' )
            return null;
        return preg_match( '/^([A-Za-z0-9_*@.-]+)\s*(\[[^\]]*\])?\s*(=|$)/', $t, $m ) ? $m[1] : null;
    }

    /** @return bool whether any of the lines sets a variable */
    public static function hasSetting( array $lines )
    {
        foreach ( $lines as $l )
            if ( self::variableOf( $l ) !== null )
                return true;
        return false;
    }

    /**
     * The lines of a block to write to the target.
     *
     * @param array $lines the source block's lines
     * @param array|null $only null: every line (comments and blank lines in order); else only the lines of
     *                         these variables (no comments)
     * @param array $drop variables whose lines are left out (the target keeps its own)
     * @return array
     */
    public static function selectLines( array $lines, $only, array $drop )
    {
        $out = array();
        foreach ( $lines as $l )
        {
            $v = self::variableOf( $l );
            if ( $v === null )
            {
                if ( $only === null )
                    $out[] = $l;
                continue;
            }
            if ( in_array( $v, $drop, true ) || ( $only !== null && !in_array( $v, $only, true ) ) )
                continue;
            $out[] = $l;
        }
        return $out;
    }

    /**
     * The summary line: blocks, variables, files (and conflicts), and the ini cache clear after real writes.
     *
     * @return int exit code
     */
    public function finish()
    {
        $t = $this->totals;
        $this->c->data( 'totals', $t );
        $this->c->data( 'files', $this->report );
        $summary = ( $this->c->isDryRun() ? 'Dry run: would move ' : 'Moved ' )
                 . expIniCommandContext::counted( $t['blocks'], 'block' ) . ', '
                 . expIniCommandContext::counted( $t['variables'], 'variable' ) . ', '
                 . expIniCommandContext::counted( $t['files'], 'file' )
                 . ( $t['conflicts'] ? ', ' . expIniCommandContext::counted( $t['conflicts'], 'conflict' ) . ' merged' : '' );
        if ( !$this->c->isDryRun() && $t['files'] > 0 )
            $this->c->afterWrite();
        return $this->c->finish( expIniCommandContext::EXIT_OK, $summary );
    }
}
