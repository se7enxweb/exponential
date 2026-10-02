<?php
/**
 * File containing the expIniActionRem class.
 *
 * exp:ini rem (alias remove): removes a variable from one scope's file, or one value of an array, or one
 * entry of a hash. The variable must be in that file (exit 2 otherwise); a missing file is never created.
 * Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionRem extends expIniActionBase
{
    const NAME = 'rem';
    const DESCRIPTION = 'Remove a variable, one array value or one hash entry from one scope (alias: remove)';
    const USAGE = "<file>/<Block>/<Variable> <scope>\n<file>/<Block>/<Variable>[] <value> <scope>\n<file>/<Block>/<Variable>[<key>] <scope>\n\n" .
                  "  exp:ini rem site.ini/SiteSettings/SiteName global\n" .
                  "  exp:ini rem site.ini/ExtensionSettings/ActiveAccessExtensions[] myext siteaccess:admin\n" .
                  "  exp:ini rem site.ini/SiteAccessSettings/RelatedSiteAccessList[admin] siteaccess:site\n" .
                  "Only that scope's file changes: the value other files give stays (see where).\n" .
                  "<file>/<Block> <scope> removes a block with no settings left (--force: with them).\n" .
                  "Exit 2 when the scope's file does not set it.";

    public function run( expIniCommandContext $c )
    {
        $first = $c->arguments() ? expIniEditor::parseSetting( (string)$c->arguments()[0] ) : null;
        if ( $first !== null && $first['block'] !== null && $first['variable'] === null )
            return $this->removeBlock( $c );

        $setting = $c->setting();
        $value = null;
        if ( $setting['kind'] === 'array' )
            $value = $c->shift( 'value' );
        else if ( $c->remaining() >= 2 && $setting['kind'] === 'plain' )
        {
            // rem <file>/<Block>/<Variable> <value> <scope>: one value of an array, [] left out
            $value = $c->shift();
            $setting['kind'] = 'array';
        }
        $scope = $c->writeScope( $c->shift( 'scope' ) );
        $c->noMoreArguments();

        $text = expIniCommandContext::settingText( $setting );
        $c->data( 'setting', $text );
        if ( !is_file( $scope->path( $setting['file'] ) ) )
            return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, "Not found: $text, " . self::noFile( $scope, $setting['file'] ) );

        $editor = $c->editor( $scope, $setting['file'] );
        if ( !self::setsWhatIsRemoved( $editor->get( $setting['block'], $setting['variable'] ), $setting, $value ) )
            return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, "Not found: $text"
                                                                    . ( $value !== null ? ' = ' . $c->display( $setting['variable'], $value ) : '' )
                                                                    . ' is not set in ' . $scope->name() );

        $editor->remove( $setting['block'], $setting['variable'], $value,
                         $setting['kind'] === 'hash' ? $setting['key'] : null );
        return $c->commit( $editor, $scope, $setting['file'], 'remove ' . $text, false );
    }

    /**
     * Whether the scope's file sets what rem is asked to remove: the variable, its hash entry, or the value of
     * its array.
     *
     * @param mixed $current the variable's value in the scope's file (null: not there)
     * @param array $setting parseSetting(), kind as rem reads it
     * @param string|null $value the array value to remove
     * @return bool
     */
    private static function setsWhatIsRemoved( $current, array $setting, $value )
    {
        if ( $current === null )
            return false;
        if ( $setting['kind'] === 'hash' )
            return is_array( $current ) && array_key_exists( $setting['key'], $current );
        if ( $setting['kind'] === 'array' )
            return self::hasValue( $current, $value );
        return true;
    }

    /**
     * rem <file>/<Block> <scope>: removes a block that holds no settings any more (its header, comments and
     * blank lines); one that still has settings only with --force.
     */
    private function removeBlock( expIniCommandContext $c )
    {
        $target = $c->fileAndBlock();
        $scope = $c->writeScope( $c->shift( 'scope' ) );
        $c->noMoreArguments();
        $text = $target['file'] . '.ini/' . $target['block'];
        $c->data( 'block', $target['block'] );
        if ( !is_file( $scope->path( $target['file'] ) ) )
            return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, 'Not found: ' . self::noFile( $scope, $target['file'] ) );
        $editor = $c->editor( $scope, $target['file'] );
        if ( $editor->blockLines( $target['block'] ) === null )
            return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, "Not found: no block [{$target['block']}] in " . $scope->name() );
        if ( $editor->variables( $target['block'] ) && !$c->option( 'force' ) )
            throw expIniException::refused( "[{$target['block']}] still has settings: remove them first, or add --force to remove the block with them" );
        $editor->removeBlock( $target['block'] );
        return $c->commit( $editor, $scope, $target['file'], 'remove block ' . $text, false );
    }
}
