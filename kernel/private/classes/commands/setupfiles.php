<?php
/**
 * The code of bin/php/setupfiles.php: put .htaccess and config.php into place from the templates the installation
 * ships, as administrators do with "cp -va".
 * @description Copy .htaccess_root (or .htaccess_root_static) to .htaccess and config.php-RECOMMENDED to config.php, with a diff, a backup and --force for an existing file (dry run unless --apply)
 * @alias exp:setup:htaccess
 * @alias exp:setup:config
 * @alias exp:setup:status
 * Guide: doc/eng-us/guides/setup-files-from-the-console.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Setupfiles extends \Exponential\Runnable\Command
{
    const VERBS = array( 'htaccess', 'config', 'status' );

    /** Printed by exp:setup:htaccess: the faster alternative to a .htaccess. */
    const VHOST_NOTICE = 'Faster: Apache reads .htaccess on every request; put these rules in the virtual host with AllowOverride None instead (doc/eng-us/examples/web-servers/apache-virtual-host.md).';

    /** Lines of a diff shown without -v. */
    const DIFF_LINES = 40;

    public function run()
    {
        $cli = $this->cli();
        $script = $this->script( array(
            'description' => "Put .htaccess and config.php into place from the templates of the installation\n\n"
                           . "  ./console exp:setup:htaccess [--static] [--force] [--apply]   .htaccess_root (--static: .htaccess_root_static) -> .htaccess\n"
                           . "  ./console exp:setup:config [--force] [--apply]               config.php-RECOMMENDED -> config.php\n"
                           . "  ./console exp:setup:status                                  which template .htaccess and config.php come from\n\n"
                           . "Nothing is written without --apply: the run shows what would be copied. --dry-run is the default\n"
                           . "and may be given; --apply with --dry-run is a usage error (exit 2). An existing file that\n"
                           . "differs from the template is shown as a diff (40 lines, -v all) and left alone unless --force; with --force --apply\n"
                           . "it is backed up to var/backup/setup/<time>/ (mode and owner kept) and replaced.\n"
                           . "A new file gets the template's mode and, run as root, the template's owner and group (as cp -a),\n"
                           . "an existing one keeps its own; --mode and --owner set them. -v prints each copy as cp -v does.\n"
                           . "Prints PASS or FAIL at the end; exit 1 when a file was refused or could not be written.",
            'use-session' => false,
            'use-modules' => false,
            'use-extensions' => true,
        ) );
        $options = $this->startup(
            '[apply][dry-run][force][static][root:][owner:][mode:][show-secrets]',
            '[verb]',
            array(
                'verb' => "htaccess, config or status (default: the one the command was called as, else status)",
                'apply' => 'Write: copy the file (the default only shows what would be copied)',
                'dry-run' => 'The default, kept for scripts that pass it: show what would be copied, change nothing',
                'force' => 'Replace an existing file that differs from the template (it is backed up first)',
                'static' => 'htaccess: copy .htaccess_root_static, the rules that serve the static cache, instead of .htaccess_root',
                'root' => 'The installation root (default: the installation this command belongs to)',
                'owner' => 'Owner of the written file, user or user:group (only root can give it to another user)',
                'mode' => 'Mode of the written file, such as 0644 (default: an existing file keeps its mode, a new one gets the template\'s)',
                'show-secrets' => 'config: show the values of passwords, secrets, keys and tokens in the diff',
            ) );

        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply nothing is written' );
            $script->shutdown( 2 );
        }

        $invoked = (string)getenv( 'EXP_CONSOLE_COMMAND' );
        $args = isset( $options['arguments'] ) ? array_values( $options['arguments'] ) : array();
        $verb = 'status';
        if ( preg_match( '/^exp:setup:(\w+)$/', $invoked, $m ) && in_array( $m[1], self::VERBS, true ) )
            $verb = $m[1];
        if ( isset( $args[0] ) && in_array( $args[0], self::VERBS, true ) )
            $verb = array_shift( $args );
        if ( $args )
        {
            $cli->error( 'FAIL: unexpected argument "' . $args[0] . '": give htaccess, config or status' );
            $script->shutdown( 2 );
        }
        if ( !empty( $options['static'] ) && $verb !== 'htaccess' )
        {
            $cli->error( 'FAIL: --static goes with exp:setup:htaccess only' );
            $script->shutdown( 2 );
        }

        $root = \expSetupFileCopy::root( isset( $options['root'] ) ? $options['root'] : null );
        if ( !is_dir( $root ) )
        {
            $cli->error( "FAIL: --root=$root is not a directory" );
            $script->shutdown( 2 );
        }
        if ( $verb === 'status' )
            $script->shutdown( $this->status( $root ) );

        $file = \expSetupFileCopy::FILES[$verb];
        $source = !empty( $options['static'] ) ? $file['static'] : $file['source'];
        $code = $this->copy( $root, $source, $file['target'], $verb, $options, (bool)$apply );
        $script->shutdown( $code );
    }

    /**
     * Copies one template into place (or shows what would be copied).
     *
     * @return int the exit code
     */
    protected function copy( $root, $source, $target, $verb, array $options, $apply )
    {
        $cli = $this->cli();
        $verbose = $this->script()->verboseOutputLevel();
        $force = !empty( $options['force'] );
        $plan = \expSetupFileCopy::plan( $root, $source, $target );
        $settings = array( 'owner' => isset( $options['owner'] ) ? $options['owner'] : null,
                           'mode' => isset( $options['mode'] ) ? $options['mode'] : null );
        $installation = 'Installation: ' . $root;
        $cli->output( $installation );
        if ( $verb === 'htaccess' )
            $cli->output( self::VHOST_NOTICE );

        switch ( $plan['state'] )
        {
            case 'missing-source':
                $cli->error( "FAIL: $source is missing in $root; it ships with every installation (get it from the release or the repository)" );
                return 1;
            case 'not-a-file':
                $cli->error( "FAIL: $target is a link or not a regular file; change it by hand" );
                return 1;
            case 'same':
                $cli->output( "PASS: $target is already a copy of $source; nothing to do" );
                return 0;
        }

        if ( $plan['state'] === 'differs' )
        {
            $cli->output( "$target differs from $source (- what $target has, + what $source has):" );
            $diff = rtrim( $plan['diff'], "\n" );
            if ( $verb === 'config' && empty( $options['show-secrets'] ) )
                $diff = \expConfigPhpEditor::maskSecrets( $diff );
            $lines = explode( "\n", str_replace( "\0", '', $diff ) );
            // a long diff (a file from an older release, a broken file) is cut; -v shows all of it
            $limit = $verbose ? count( $lines ) : self::DIFF_LINES;
            foreach ( array_slice( $lines, 0, $limit ) as $line )
                $cli->output( '  ' . $line );
            if ( count( $lines ) > $limit )
            {
                $removed = count( preg_grep( '/^-(?!--)/', $lines ) );
                $added = count( preg_grep( '/^\+(?!\+\+)/', $lines ) );
                $cli->output( sprintf( '  ... %d more lines of the diff (%d lines removed, %d added in all); -v shows the whole diff',
                                       count( $lines ) - $limit, $removed, $added ) );
            }
            if ( $verb === 'config' && $this->setSettings( $plan['target'] ) )
                $cli->output( "  ($target sets " . $this->setSettings( $plan['target'] ) . " setting(s); ./console exp:config:list --only-set shows them)" );
            if ( !$force )
            {
                $cli->error( "FAIL: $target exists and differs from $source; nothing was changed. --force --apply replaces it"
                           . " (the current file is backed up to " . \expSetupFileCopy::BACKUP_DIR . "/setup/<time>/ first)" );
                return 1;
            }
        }

        $what = $plan['state'] === 'new' ? "copy $source to $target" : "back up $target to " . \expSetupFileCopy::BACKUP_DIR . "/setup/<time>/ and replace it with $source";
        if ( !$apply )
        {
            $cli->output( "Would $what" . $this->attributes( $root, $plan, $settings ) );
            $cli->output( 'PASS: dry run, nothing changed; --apply ' . ( $plan['state'] === 'new' ? 'copies' : 'replaces' ) );
            return 0;
        }

        $result = \expSetupFileCopy::apply( $root, $plan, 'setup', $settings );
        if ( !$result['ok'] )
        {
            $cli->error( 'FAIL: ' . $result['message'] );
            return 1;
        }
        if ( $result['backup'] !== '' && $verbose )
            $cli->output( "'$target' -> '" . \expSetupFileCopy::relative( $root, $result['backup'] ) . "'" );
        if ( $verbose )
            $cli->output( "'$source' -> '$target'" );
        if ( $result['backup'] !== '' )
            $cli->output( 'Backup: ' . \expSetupFileCopy::relative( $root, $result['backup'] ) );
        $cli->output( 'PASS: ' . ( $result['created'] ? "$source copied to $target" : "$target replaced with $source" )
                    . sprintf( ' (mode %04o, owner %s:%s)', $result['mode'], $result['owner'], $result['group'] ) );
        if ( $verb === 'htaccess' )
            $cli->output( 'Apache and LiteSpeed read .htaccess on the next request; nginx, Caddy and IIS never read it, and Velocity has the rules of .htaccess_root built in.' );
        if ( $verb === 'config' )
            $cli->output( 'Next: ./console exp:config:list shows its settings, ./console exp:config:set <NAME> <value> sets one. A persistent server (Velocity, FrankenPHP worker) reads config.php again after a restart.' );
        return 0;
    }

    /** @return string " (mode 0644, owner user:group)" as the write would make it */
    protected function attributes( $root, array $plan, array $settings )
    {
        $target = $plan['target'];
        $existed = $plan['state'] !== 'new';
        if ( !empty( $settings['mode'] ) && preg_match( '/^0?[0-7]{3,4}$/', (string)$settings['mode'] ) )
            $mode = octdec( (string)$settings['mode'] );
        else
            $mode = fileperms( $existed ? $target : $plan['source'] ) & 07777;
        if ( !empty( $settings['owner'] ) )
            $owner = (string)$settings['owner'];
        else if ( !\expSetupFileCopy::isRoot() )
            $owner = \expSetupFileCopy::userName( function_exists( 'posix_geteuid' ) ? posix_geteuid() : getmyuid() );
        else
        {
            $from = $existed ? $target : $plan['source'];
            $stat = stat( $from );
            if ( !$existed && $stat['uid'] === 0 )
                $stat = stat( $root );
            $owner = \expSetupFileCopy::userName( $stat['uid'] ) . ':' . \expSetupFileCopy::groupName( $stat['gid'] );
        }
        return sprintf( ' (mode %04o, owner %s)', $mode, $owner );
    }

    /** @return int how many settings a config.php sets (0 when it does not compile) */
    protected function setSettings( $file )
    {
        $code = (string)@file_get_contents( $file );
        if ( \expConfigPhpEditor::syntaxError( $code ) !== null )
            return 0;
        $editor = new \expConfigPhpEditor( $code );
        return count( array_filter( $editor->entries(), function ( $e ) { return $e['state'] !== 'commented'; } ) );
    }

    /**
     * Which template .htaccess and config.php come from.
     *
     * @return int the exit code
     */
    protected function status( $root )
    {
        $cli = $this->cli();
        $cli->output( 'Installation: ' . $root );
        $htaccess = \expSetupFileCopy::htaccessState( $root );
        $texts = array(
            'missing' => 'missing: ./console exp:setup:htaccess --apply copies .htaccess_root (with --static: .htaccess_root_static)',
            'root' => 'a copy of .htaccess_root',
            'static' => 'a copy of .htaccess_root_static',
            'changed' => 'differs from .htaccess_root and .htaccess_root_static (changed by hand, or from an older release: ./console exp:setup:htaccess shows the diff)',
        );
        $cli->output( sprintf( '  %-11s %s', '.htaccess', $texts[$htaccess] ) );

        $config = $root . '/config.php';
        if ( !is_file( $config ) )
            $text = 'missing: ./console exp:setup:config --apply copies config.php-RECOMMENDED';
        else
        {
            $code = (string)file_get_contents( $config );
            $error = \expConfigPhpEditor::syntaxError( $code );
            if ( $error !== null )
                $text = 'does not compile: ' . $error . ' (restore it from var/backup/config/ or var/backup/setup/, or ./console exp:setup:config --force --apply)';
            else if ( is_file( $root . '/config.php-RECOMMENDED' ) && $code === (string)file_get_contents( $root . '/config.php-RECOMMENDED' ) )
                $text = 'a copy of config.php-RECOMMENDED (no setting set)';
            else
            {
                $editor = new \expConfigPhpEditor( $code );
                $set = count( array_filter( $editor->entries(), function ( $e ) { return $e['state'] !== 'commented'; } ) );
                $text = "present, $set setting(s) set: ./console exp:config:list --only-set shows them";
            }
        }
        $cli->output( sprintf( '  %-11s %s', 'config.php', $text ) );
        $cli->output( 'PASS: shown' );
        return 0;
    }
}

}
