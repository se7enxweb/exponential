<?php
/**
 * The code of bin/php/configphp.php: the define() settings of config.php on the command line.
 * @description List, read, set and unset the define() settings of config.php, typed and checked with php -l, documented by config.php-RECOMMENDED (set and unset: dry run unless --apply)
 * @alias exp:config:list
 * @alias exp:config:get
 * @alias exp:config:set
 * @alias exp:config:unset
 * Guide: doc/eng-us/guides/setup-files-from-the-console.md
 *
 * config.php is read as PHP tokens and never run (expConfigPhpEditor), so these commands work when it does not load;
 * bin/php/configphp.php keeps autoload.php from reading a config.php that does not compile.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Configphp extends \Exponential\Runnable\Command
{
    const VERBS = array( 'list', 'get', 'set', 'unset' );

    /** @var string */
    protected $root;

    /** @var string the config.php path */
    protected $file;

    /** @var string config.php as written in messages */
    protected $label;

    /** @var \expConfigPhpCatalogue */
    protected $catalogue;

    /** @var array */
    protected $opts;

    /** @var bool the write sets a password, secret, key or token */
    protected $secretWritten = false;

    public function run()
    {
        $cli = $this->cli();
        $script = $this->script( array(
            'description' => "The define() settings of config.php\n\n"
                           . "  ./console exp:config:list [--only-set] [--format=text|json]     every setting config.php-RECOMMENDED documents, with\n"
                           . "                                                                 the value config.php has, the proposed one and the description\n"
                           . "  ./console exp:config:get <NAME> [--format=text|json]           the value (exit 1 when it is not set)\n"
                           . "  ./console exp:config:set <NAME> <value> [--type=<t>] [--apply] set it: in place, else where the documentation has it, else added\n"
                           . "  ./console exp:config:unset <NAME> [--apply]                    comment it out again\n\n"
                           . "config.php is read as PHP tokens, never run; only the value of the one define() changes, comments and\n"
                           . "order stay. set and unset show the diff and write nothing without --apply (--dry-run is the default;\n"
                           . "both together: exit 2). A write backs config.php up to var/backup/config/<time>/ (mode and owner\n"
                           . "kept), checks the new file with php -l and renames it into place. A config.php that does not compile is\n"
                           . "refused. Values of names with PASS, SECRET, KEY, TOKEN are shown as ******** unless --show-secrets.\n"
                           . "Types (--type): bool, int, octal, float, string, null; without --type the current or the documented\n"
                           . "value decides, true/false/null are always the PHP values. A value starting with \"-\": --value=<value>.",
            'use-session' => false,
            'use-modules' => false,
            'use-extensions' => true,
        ) );
        $options = $this->startup(
            '[apply][dry-run][type:][value:][allow-unknown][show-secrets][format:][only-set][root:][file:][recommended:]',
            '[verb][name][value]',
            array(
                'verb' => 'list, get, set or unset (default: the one the command was called as, else list)',
                'name' => 'get, set, unset: the name of the setting, such as EZP_INI_FILEMTIME_CHECK',
                'apply' => 'set, unset: write config.php (the default shows the diff)',
                'dry-run' => 'The default, kept for scripts that pass it: show the diff, change nothing',
                'type' => 'set: the type of the value: bool, int, octal, float, string, null',
                'value' => 'set: the value (instead of the argument after the name), for one that starts with "-"',
                'allow-unknown' => 'set: add a setting config.php-RECOMMENDED does not document',
                'show-secrets' => 'Show the values of passwords, secrets, keys and tokens',
                'format' => 'list, get: text (default) or json',
                'only-set' => 'list: only the settings config.php sets',
                'root' => 'The installation root (default: the installation this command belongs to)',
                'file' => 'The config file (default: <root>/config.php)',
                'recommended' => 'The documented settings (default: <root>/config.php-RECOMMENDED)',
            ) );
        $this->opts = $options;

        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply nothing is written' );
            $script->shutdown( 2 );
        }
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( !in_array( $format, array( 'text', 'json' ), true ) )
        {
            $cli->error( "FAIL: --format=$format: give text or json" );
            $script->shutdown( 2 );
        }

        $invoked = (string)getenv( 'EXP_CONSOLE_COMMAND' );
        $args = isset( $options['arguments'] ) ? array_values( $options['arguments'] ) : array();
        $verb = 'list';
        if ( preg_match( '/^exp:config:(\w+)$/', $invoked, $m ) && in_array( $m[1], self::VERBS, true ) )
            $verb = $m[1];
        if ( isset( $args[0] ) && in_array( $args[0], self::VERBS, true ) && ( $verb === 'list' || $args[0] === $verb ) )
            $verb = array_shift( $args );

        $this->root = \expSetupFileCopy::root( isset( $options['root'] ) ? $options['root'] : null );
        $this->file = !empty( $options['file'] ) ? (string)$options['file'] : $this->root . '/config.php';
        $this->label = \expSetupFileCopy::relative( $this->root, $this->file );
        $recommended = !empty( $options['recommended'] ) ? (string)$options['recommended'] : $this->root . '/config.php-RECOMMENDED';
        $this->catalogue = \expConfigPhpCatalogue::fromFile( $recommended );

        $name = isset( $args[0] ) ? (string)$args[0] : '';
        if ( $verb !== 'list' )
        {
            if ( $name === '' )
            {
                $cli->error( "FAIL: exp:config:$verb needs the name of a setting, such as EZP_INI_FILEMTIME_CHECK (./console exp:config:list shows them)" );
                $script->shutdown( 2 );
            }
            if ( !preg_match( '/^[A-Za-z_][A-Za-z0-9_]*$/', $name ) )
            {
                $cli->error( "FAIL: \"$name\" is not the name of a constant" );
                $script->shutdown( 2 );
            }
        }

        // a config.php that does not compile is refused: nothing in it can be trusted to be what it looks like
        $code = is_file( $this->file ) ? (string)file_get_contents( $this->file ) : null;
        if ( $code !== null )
        {
            $error = \expConfigPhpEditor::syntaxError( $code );
            if ( $error !== null )
            {
                $cli->error( "FAIL: {$this->label} does not compile ($error); nothing was read or changed. Fix it by hand,"
                           . " restore it from var/backup/config/ or var/backup/setup/, or start again from the template with"
                           . " ./console exp:setup:config --force --apply" );
                $script->shutdown( 1 );
            }
        }

        switch ( $verb )
        {
            case 'list':
                $exit = $this->listSettings( $code, $format );
                break;
            case 'get':
                $exit = $this->getSetting( $code, $name, $format );
                break;
            case 'set':
                $value = isset( $options['value'] ) && $options['value'] !== false && $options['value'] !== null ? (string)$options['value'] : ( isset( $args[1] ) ? (string)$args[1] : null );
                if ( $value === null )
                {
                    $cli->error( "FAIL: exp:config:set $name needs a value (--value=<value> for one that starts with \"-\")" );
                    $script->shutdown( 2 );
                }
                $exit = $this->setSetting( $code, $name, $value, (bool)$apply );
                break;
            case 'unset':
                $exit = $this->unsetSetting( $code, $name, (bool)$apply );
                break;
            default:
                $cli->error( "FAIL: unknown action \"$verb\": give list, get, set or unset" );
                $exit = 2;
        }
        $script->shutdown( $exit );
    }

    /** @return bool the value of this name is shown */
    protected function shows( $name )
    {
        return !empty( $this->opts['show-secrets'] ) || !\expConfigPhpEditor::isSecret( $name );
    }

    /** @return string the value as PHP writes it, or ******** for a secret */
    protected function shown( $name, $raw )
    {
        return $this->shows( $name ) ? (string)$raw : "'********'";
    }

    /**
     * exp:config:list
     *
     * @return int
     */
    protected function listSettings( $code, $format )
    {
        $cli = $this->cli();
        $editor = new \expConfigPhpEditor( (string)$code );
        $onlySet = !empty( $this->opts['only-set'] );
        $rows = array();
        foreach ( $this->catalogue->settings() as $name => $setting )
        {
            $entry = $editor->find( $name );
            if ( $onlySet && $entry === null )
                continue;
            $rows[] = $this->row( $name, $entry, $setting );
        }
        foreach ( $editor->definedNames() as $name )
        {
            if ( $this->catalogue->setting( $name ) === null )
                $rows[] = $this->row( $name, $editor->find( $name ), null );
        }

        if ( $format === 'json' )
        {
            $cli->output( json_encode( array( 'file' => $this->file, 'exists' => $code !== null, 'settings' => $rows ),
                                       JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            return 0;
        }

        $set = count( array_filter( $rows, function ( $r ) { return $r['state'] !== 'not set'; } ) );
        $cli->output( $this->label . ': ' . ( $code === null ? 'missing (./console exp:setup:config --apply copies config.php-RECOMMENDED)'
                                                            : "$set setting(s) set" ) . ', ' . count( $this->catalogue->settings() ) . ' documented in config.php-RECOMMENDED' );
        $section = null;
        foreach ( $rows as $row )
        {
            $heading = $row['documented'] ? ( $row['section'] !== '' ? $row['section'] : 'OTHER' ) : 'NOT DOCUMENTED IN config.php-RECOMMENDED';
            if ( $heading !== $section )
            {
                $cli->output();
                $cli->output( $heading );
                $section = $heading;
            }
            $value = $row['state'] === 'not set' ? '(not set)' : $row['raw'] . ( $row['state'] === 'conditional' ? ' (inside a condition, line ' . $row['line'] . ')' : ' (line ' . $row['line'] . ')' );
            $cli->output( sprintf( '  %-36s %s', $row['name'], $value ) );
            if ( $row['documented'] )
                $cli->output( '      RECOMMENDED ' . $row['recommended'] . ' - ' . $row['summary'] );
        }
        $cli->output();
        $cli->output( '(not set): the built-in default applies. RECOMMENDED: the value config.php-RECOMMENDED proposes, an'
                    . ' optimized value or an example. ./console exp:config:get <NAME> --format=json shows the whole description.' );
        return 0;
    }

    /** @return array a row of the listing */
    protected function row( $name, $entry, $setting )
    {
        $shows = $this->shows( $name );
        return array(
            'name' => $name,
            'state' => $entry === null ? 'not set' : $entry['state'],
            'type' => $entry !== null ? $entry['type'] : null,
            'value' => $entry !== null ? ( $shows ? $entry['value'] : '********' ) : null,
            'raw' => $entry !== null ? $this->shown( $name, $entry['raw'] ) : null,
            'text' => $entry !== null ? ( $shows ? \expConfigPhpEditor::plainValue( $entry ) : '********' ) : null,
            'line' => $entry !== null ? $entry['line'] : null,
            'secret' => \expConfigPhpEditor::isSecret( $name ),
            'documented' => $setting !== null,
            'recommended' => $setting !== null ? $this->shown( $name, $setting['raw'] ) : null,
            'recommended_type' => $setting !== null ? $setting['type'] : null,
            'section' => $setting !== null ? $setting['section'] : '',
            'summary' => $setting !== null ? \expConfigPhpCatalogue::summary( $setting['description'], 90 ) : '',
            'description' => $setting !== null ? $setting['description'] : '',
        );
    }

    /**
     * exp:config:get
     *
     * @return int
     */
    protected function getSetting( $code, $name, $format )
    {
        $cli = $this->cli();
        $editor = new \expConfigPhpEditor( (string)$code );
        $entry = $editor->find( $name );
        $setting = $this->catalogue->setting( $name );
        if ( $format === 'json' )
        {
            $row = $this->row( $name, $entry, $setting );
            $row['file'] = $this->file;
            $cli->output( json_encode( $row, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            return $entry === null ? 1 : 0;
        }
        if ( $entry === null )
        {
            $cli->error( "$name is not set in {$this->label}" . ( $setting !== null ? ' (config.php-RECOMMENDED proposes ' . $this->shown( $name, $setting['raw'] ) . ')' : ( $code === null ? ' (the file does not exist)' : '' ) ) );
            return 1;
        }
        if ( $entry['state'] === 'conditional' )
            $cli->warning( "$name is defined inside a condition on line {$entry['line']}: whether it applies depends on that condition" );
        $cli->output( $this->shows( $name ) ? \expConfigPhpEditor::plainValue( $entry ) : '********' );
        return 0;
    }

    /**
     * exp:config:set
     *
     * @return int
     */
    protected function setSetting( $code, $name, $text, $apply )
    {
        $cli = $this->cli();
        if ( $code === null )
        {
            $cli->error( "FAIL: {$this->label} does not exist; ./console exp:setup:config --apply copies config.php-RECOMMENDED to it first" );
            return 1;
        }
        $editor = new \expConfigPhpEditor( $code );
        $setting = $this->catalogue->setting( $name );
        $current = $editor->find( $name );
        if ( $setting === null && $current === null && empty( $this->opts['allow-unknown'] ) )
        {
            $cli->error( "FAIL: $name is not documented in config.php-RECOMMENDED and not set in {$this->label}; check the name"
                       . ' (./console exp:config:list), or give --allow-unknown to add it anyway' );
            return 1;
        }

        $type = isset( $this->opts['type'] ) && $this->opts['type'] ? strtolower( (string)$this->opts['type'] ) : null;
        if ( $type !== null && !in_array( $type, \expConfigPhpEditor::TYPES, true ) )
        {
            $cli->error( "FAIL: --type=$type: give one of " . implode( ', ', \expConfigPhpEditor::TYPES ) );
            return 2;
        }
        // the type: --type, else the one of the value config.php has, else the documented one
        $hint = null;
        if ( $current !== null && $current['type'] !== 'expression' && $current['type'] !== 'null' )
            $hint = $current['type'];
        else if ( $setting !== null )
            $hint = $setting['type'];
        $typed = \expConfigPhpEditor::typedValue( $text, $type, $hint );
        if ( isset( $typed['error'] ) )
        {
            $cli->error( "FAIL: $name: " . $typed['error'] );
            return 1;
        }
        $literal = \expConfigPhpEditor::literal( $typed['value'], $typed['type'] );
        $result = $editor->withValue( $name, $literal );
        if ( isset( $result['error'] ) )
        {
            $cli->error( 'FAIL: ' . $result['error'] . '; nothing was changed' );
            return 1;
        }
        if ( $result['how'] === 'unchanged' )
        {
            $cli->output( "PASS: $name is already " . $this->shown( $name, $literal ) . " in {$this->label} (line {$result['line']}); nothing to do" );
            return 0;
        }
        $this->secretWritten = \expConfigPhpEditor::isSecret( $name );
        $hows = array( 'changed' => 'change the value on line', 'uncommented' => 'take the comment off the documented line', 'added' => 'add it to the exp:config:set block at line' );
        $summary = "$name = " . $this->shown( $name, $literal ) . " ({$typed['type']}): " . $hows[$result['how']] . ' ' . $result['line'];
        return $this->write( $code, $result['code'], $summary, $apply, function ( \expConfigPhpEditor $written ) use ( $name, $typed )
        {
            $entry = $written->find( $name );
            if ( $entry === null || $entry['state'] !== 'set' || $entry['type'] !== $typed['type'] || $entry['value'] !== $typed['value'] )
                return "the new file does not read back $name as the value written";
            return null;
        } );
    }

    /**
     * exp:config:unset
     *
     * @return int
     */
    protected function unsetSetting( $code, $name, $apply )
    {
        $cli = $this->cli();
        if ( $code === null )
        {
            $cli->output( "PASS: {$this->label} does not exist, so $name is not set; nothing to do" );
            return 0;
        }
        $editor = new \expConfigPhpEditor( $code );
        $result = $editor->without( $name );
        if ( isset( $result['error'] ) )
        {
            $cli->error( 'FAIL: ' . $result['error'] . '; nothing was changed' );
            return 1;
        }
        if ( $result['how'] === 'absent' )
        {
            $cli->output( "PASS: $name is not set in {$this->label}; nothing to do" );
            return 0;
        }
        $summary = "$name: " . ( $result['how'] === 'commented' ? 'comment out the define() on line ' : 'remove the define() on line ' ) . $result['line']
                 . ' (the built-in default applies again)';
        return $this->write( $code, $result['code'], $summary, $apply, function ( \expConfigPhpEditor $written ) use ( $name )
        {
            return $written->find( $name ) !== null ? "the new file still defines $name" : null;
        } );
    }

    /**
     * Shows the diff; with --apply backs config.php up, writes the new code to a temporary file, checks it (php -l
     * and the expected value read back) and renames it into place.
     *
     * @return int
     */
    protected function write( $old, $new, $summary, $apply, $verify )
    {
        $cli = $this->cli();
        $diff = \expIniEditor::unifiedDiff( $old, $new, $this->label, $this->label );
        if ( empty( $this->opts['show-secrets'] ) )
            $diff = \expConfigPhpEditor::maskSecrets( $diff );
        $cli->output( $summary );
        foreach ( explode( "\n", rtrim( str_replace( "\0", '', $diff ), "\n" ) ) as $line )
            $cli->output( '  ' . $line );

        // the result must compile and read back as intended, in the dry run too
        $error = \expConfigPhpEditor::syntaxError( $new );
        $problem = $error !== null ? "the result would not compile ($error)" : $verify( new \expConfigPhpEditor( $new ) );
        if ( $problem !== null )
        {
            $cli->error( "FAIL: $problem; nothing was changed" );
            return 1;
        }
        if ( !$apply )
        {
            $cli->output( "PASS: dry run, {$this->label} not changed; --apply writes it (backup to var/backup/config/<time>/ first)" );
            return 0;
        }
        $result = \expSetupFileCopy::write( $this->root, $this->file, $new, 'config', array(
            'check' => function ( $tmp ) use ( $verify )
            {
                $lint = \expSetupFileCopy::lint( $tmp );
                if ( $lint !== null )
                    return $lint;
                return $verify( \expConfigPhpEditor::fromFile( $tmp ) );
            },
        ) );
        if ( !$result['ok'] )
        {
            $cli->error( 'FAIL: ' . $result['message'] );
            return 1;
        }
        if ( $this->script()->verboseOutputLevel() && $result['backup'] !== '' )
            $cli->output( "'{$this->label}' -> '" . \expSetupFileCopy::relative( $this->root, $result['backup'] ) . "'" );
        if ( $result['backup'] !== '' )
            $cli->output( 'Backup: ' . \expSetupFileCopy::relative( $this->root, $result['backup'] ) );
        $cli->output( "PASS: {$this->label} written and checked with php -l"
                    . sprintf( ' (mode %04o, owner %s:%s)', $result['mode'], $result['owner'], $result['group'] ) );
        if ( $this->secretWritten && ( $result['mode'] & 0004 ) )
            $cli->warning( sprintf( "WARN: %s holds a password or secret and every user of the server can read it (mode %04o):"
                                  . " chmod 0640 %s, with the web server's group", $this->label, $result['mode'], $this->label ) );
        $cli->output( 'A persistent server (Velocity, FrankenPHP worker) reads config.php again after a restart; PHP-FPM once'
                    . ' OPcache sees the change (a reload of PHP-FPM when opcache.validate_timestamps=0).' );
        return 0;
    }
}

}
