<?php
/**
 * @description The system card: the Exponential "e" next to the facts of this installation, its engine and its machine, with health hints (exp:info adds an inventory, exp:about the project)
 * @alias exp:info
 * @alias exp:about
 *
 * File containing the command of exp:expfetch, exp:info and exp:about (doc/eng-us/features/6.0/system-card.md).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Syscard extends \Exponential\Runnable\Command
{
    /** The command and its aliases: alias => the card it shows */
    const NAMES = array( 'exp:expfetch' => 'card', 'exp:info' => 'info', 'exp:about' => 'about' );

    /** The environment variable the console sets to the name the command was called by */
    const INVOKED_AS = 'EXP_CONSOLE_COMMAND';

    /** The options, for --help and the shell completion */
    const OPTIONS = array(
        'json'           => 'One JSON document: the sections, the hints and how each provider ran (no logo, no colors)',
        'plain'          => 'Plain text: no logo, no colors, ASCII marks, one "Label: value" per line (for pasting)',
        'no-logo'        => 'The facts without the logo',
        'section'        => 'Only these sections, comma separated: system, php, exponential, engine, database, site (exp:info also extensions, siteaccesses, settings, cron, queue, caches, help; exp:about about, project, links, contribute, credits)',
        'no-color'       => 'No colors (as NO_COLOR=1); colors are only used on a terminal anyway',
        'color'          => 'The color depth: auto (default), truecolor, 256, 16 or none',
        'theme'          => 'The theme: rainbow (default), orange, mono, high-contrast, light, or one of an extension',
        'width'          => 'The width in columns (default: the terminal\'s, else syscard.ini [Display] DefaultWidth)',
        'ascii'          => 'ASCII art and marks instead of Unicode block art',
        'no-hints'       => 'Leave out the Hints block',
        'no-bars'        => 'Leave out the color bars',
        'watch'          => 'Refresh in place every N seconds (default 2) like top: load, memory and Velocity workers; Ctrl-C stops',
        'count'          => '--watch: stop after this many refreshes',
        'animate'        => 'A short reveal of the logo and its gradient (under a second; only on a color terminal)',
        'mode'           => 'The card: card (exp:expfetch), info (exp:info) or about (exp:about); the alias sets it',
        'list-providers' => 'List the providers of the card: id, class, priority, budget, on or off',
        'list-themes'    => 'List the themes',
        'completion'     => 'Print a shell completion script: bash or zsh',
    );

    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "The system card of this installation, drawn next to the Exponential \"e\"\n" .
                                 "\n" .
                                 "./console exp:expfetch                     Exponential, the machine, PHP, the engine, the database and the site\n" .
                                 "./console exp:info                         the same and an inventory: extensions and versions, siteaccesses,\n" .
                                 "                                           settings, cronjobs, the queue, cache sizes, help coverage\n" .
                                 "./console exp:about                        the same and the project: what Exponential is, license, links\n" .
                                 "./console exp:expfetch --plain             plain text to paste (no logo, no colors)\n" .
                                 "./console exp:info --json --no-logo        one JSON document, e.g. for a support request or monitoring\n" .
                                 "./console exp:expfetch --section=php,engine  only these sections\n" .
                                 "./console exp:expfetch --watch=2           refresh every 2 seconds (Ctrl-C stops)\n" .
                                 "./console exp:expfetch --theme=light       darker colors for a light terminal (also orange, mono, high-contrast)\n" .
                                 "./console exp:expfetch --color=256 --width=100 --animate\n" .
                                 "./console exp:expfetch --completion=bash >> ~/.bashrc\n" .
                                 "\n" .
                                 "Colors only on a terminal, never with NO_COLOR set; the depth (truecolor, 256, 16) is taken from COLORTERM\n" .
                                 "and TERM. The facts come from the providers of settings/syscard.ini, each with a time budget: one that takes\n" .
                                 "too long shows n/a. Nothing is changed, and no password, key, token or full database address is printed.\n" .
                                 "Exit code: 0, or 2 for a wrong option.",
                'use-session'    => false,
                'use-modules'    => false,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[json][plain][no-logo][section:][no-color][color:][theme:][width:][ascii][no-hints][no-bars][watch;][count:][animate][mode:][list-providers][list-themes][completion:]', '', self::OPTIONS );

        $mode = self::modeFor( isset( $options['mode'] ) ? $options['mode'] : null, getenv( self::INVOKED_AS ) );
        if ( $mode === null )
        {
            $cli->error( 'Unknown --mode: card, info or about' );
            $this->shutdown( 2 );
        }
        if ( !empty( $options['completion'] ) )
        {
            $script = self::completion( (string)$options['completion'], \expSysCardTheme::names() );
            if ( $script === null )
            {
                $cli->error( 'Unknown shell for --completion: bash or zsh' );
                $this->shutdown( 2 );
            }
            fwrite( STDOUT, $script );
            $this->shutdown( 0 );
        }
        $registry = \expSysCardRegistry::fromIni();
        if ( !empty( $options['list-themes'] ) )
        {
            foreach ( \expSysCardTheme::names() as $name )
                fwrite( STDOUT, $name . "\n" );
            $this->shutdown( 0 );
        }
        if ( !empty( $options['list-providers'] ) )
        {
            fwrite( STDOUT, self::providerTable( $registry ) );
            $this->shutdown( 0 );
        }

        $env = self::environment();
        $tty = self::isTerminal( STDOUT );
        $display = function ( $name, $default ) {
            $ini = \eZINI::instance( 'syscard.ini' );
            return $ini->hasVariable( 'Display', $name ) ? $ini->variable( 'Display', $name ) : $default;
        };
        $json = !empty( $options['json'] );
        $plain = !empty( $options['plain'] );

        $depth = \expSysCardColor::detect( $env, $tty );
        if ( !empty( $options['color'] ) && strtolower( (string)$options['color'] ) !== 'auto' )
        {
            $forced = \expSysCardColor::parse( $options['color'] );
            if ( $forced === null )
            {
                $cli->error( 'Unknown --color: auto, truecolor, 256, 16 or none' );
                $this->shutdown( 2 );
            }
            // NO_COLOR always wins (https://no-color.org)
            $depth = isset( $env['NO_COLOR'] ) && $env['NO_COLOR'] !== '' ? \expSysCardColor::NONE : $forced;
        }
        if ( !empty( $options['no-color'] ) || $json || $plain )
            $depth = \expSysCardColor::NONE;

        $theme = \expSysCardTheme::fromIni( isset( $options['theme'] ) ? (string)$options['theme'] : null );
        if ( !empty( $options['theme'] ) && !in_array( $theme->name, \expSysCardTheme::names(), true ) )
        {
            $cli->error( 'Unknown --theme "' . $options['theme'] . '": ' . implode( ', ', \expSysCardTheme::names() ) );
            $this->shutdown( 2 );
        }
        $charset = strtolower( (string)$display( 'Charset', 'auto' ) );
        $unicode = !empty( $options['ascii'] ) ? false : ( $charset === 'unicode' ? true : ( $charset === 'ascii' ? false : \expSysCardLogo::unicodeTerminal( $env ) ) );
        $width = !empty( $options['width'] ) && ctype_digit( (string)$options['width'] ) ? (int)$options['width'] : self::terminalWidth( $env, $tty, (int)$display( 'DefaultWidth', 80 ) );
        $width = max( 20, min( 400, $width ) );

        $root = rtrim( (string)\eZSys::rootDir(), '/' );
        $renderer = new \expSysCardRenderer( $theme );
        $renderer->width = $width;
        $renderer->depth = $depth;
        $renderer->unicode = $unicode;
        $renderer->logo = !empty( $options['no-logo'] ) ? null : \expSysCardLogo::fromIni( $root !== '' ? $root : getcwd(), $unicode );
        $renderer->wordmark = \expSysCardContext::isOn( $display( 'Wordmark', 'enabled' ) );
        $renderer->colorBars = empty( $options['no-bars'] ) && \expSysCardContext::isOn( $display( 'ColorBars', 'enabled' ) );
        $renderer->hints = empty( $options['no-hints'] ) && \expSysCardContext::isOn( $display( 'Hints', 'enabled' ) );
        $ini = \eZINI::instance( 'syscard.ini' );
        $renderer->titles = $ini->hasVariable( 'Sections', 'Titles' ) ? (array)$ini->variable( 'Sections', 'Titles' ) : array();

        $sections = null;
        if ( !empty( $options['section'] ) )
            $sections = array_values( array_filter( array_map( 'strtolower', array_map( 'trim', explode( ',', (string)$options['section'] ) ) ), 'strlen' ) );

        $makeContext = function () use ( $mode, $root, $width, $env, $tty ) {
            $context = \expSysCardContext::real( $mode, $root !== '' ? $root : null );
            $rows = $tty ? self::terminalRows( $env ) : 0;
            if ( $tty )
                $context->set( 'host.terminal_size', $width . 'x' . ( $rows > 0 ? $rows : '?' ) );
            return $context;
        };

        $watch = isset( $options['watch'] ) && $options['watch'] !== null && $options['watch'] !== false;
        if ( $watch && !$json )
        {
            $interval = $options['watch'] === true || !is_numeric( $options['watch'] ) ? 2.0 : max( 0.5, (float)$options['watch'] );
            $count = isset( $options['count'] ) && ctype_digit( (string)$options['count'] ) ? (int)$options['count'] : 0;
            $this->watch( $registry, $renderer, $makeContext, $mode, $sections, $plain, $interval, $count, $tty );
            $this->shutdown( 0 );
        }

        $context = $makeContext();
        $result = $registry->collect( $context );
        $facts = self::filterSections( $result['facts'], $sections );
        $meta = self::meta( $mode, $facts, $context );
        if ( $json )
            fwrite( STDOUT, $renderer->json( $facts, $result['runs'], $meta ) );
        elseif ( $plain )
            fwrite( STDOUT, $renderer->plain( $facts, $meta ) );
        else
        {
            if ( !empty( $options['animate'] ) && $tty && $depth !== \expSysCardColor::NONE && $renderer->logo !== null )
                self::animate( $renderer, $facts, $meta );
            fwrite( STDOUT, $renderer->text( $facts, $meta ) );
        }
        $this->shutdown( 0 );
    }

    /**
     * The card for an alias or --mode.
     *
     * @param string|null $option --mode
     * @param string|false|null $invokedAs the name the console was given (EXP_CONSOLE_COMMAND)
     * @return string|null card, info or about; null for a wrong --mode
     */
    public static function modeFor( $option, $invokedAs )
    {
        if ( $option !== null && $option !== '' && $option !== true )
        {
            $option = strtolower( trim( (string)$option ) );
            return in_array( $option, array( 'card', 'info', 'about' ), true ) ? $option : null;
        }
        $invokedAs = strtolower( trim( (string)$invokedAs ) );
        return isset( self::NAMES[$invokedAs] ) ? self::NAMES[$invokedAs] : 'card';
    }

    /**
     * The title, the host and the card of the output.
     *
     * @param string $mode
     * @param \expSysCardFact[] $facts
     * @param \expSysCardContext $context
     * @return array
     */
    public static function meta( $mode, array $facts, \expSysCardContext $context )
    {
        $version = class_exists( 'ExponentialSDK' ) ? (string)\ExponentialSDK::version( true, false, false ) : '';
        $host = '';
        foreach ( $facts as $fact )
            if ( $fact->key === 'host.name' )
                $host = $fact->value;
        if ( $host === '' )
            $host = (string)php_uname( 'n' );
        $names = array_flip( self::NAMES );
        return array( 'card' => $mode, 'command' => $names[$mode], 'generated' => date( 'c', $context->now ),
                      'title' => 'Exponential' . ( $version !== '' ? ' ' . $version : '' ), 'subtitle' => $host );
    }

    /**
     * @param \expSysCardFact[] $facts
     * @param string[]|null $sections
     * @return \expSysCardFact[]
     */
    public static function filterSections( array $facts, $sections )
    {
        if ( !is_array( $sections ) || empty( $sections ) )
            return $facts;
        return array_values( array_filter( $facts, function ( $fact ) use ( $sections ) { return in_array( $fact->section, $sections, true ); } ) );
    }

    /**
     * Refreshes the card in place until Ctrl-C (or --count refreshes): the watched providers (load, memory,
     * workers) run again each time, the others keep their first facts.
     */
    protected function watch( \expSysCardRegistry $registry, \expSysCardRenderer $renderer, callable $makeContext, $mode, $sections, $plain, $interval, $count, $tty )
    {
        $stop = false;
        if ( function_exists( 'pcntl_signal' ) && function_exists( 'pcntl_async_signals' ) )
        {
            pcntl_async_signals( true );
            pcntl_signal( SIGINT, function () use ( &$stop ) { $stop = true; } );
            pcntl_signal( SIGTERM, function () use ( &$stop ) { $stop = true; } );
        }
        $watched = array();
        foreach ( $registry->providers( $mode ) as $provider )
            if ( $provider->watched() )
                $watched[] = $provider->id();
        $byProvider = array();
        $order = array();
        $frame = 0;
        if ( $tty )
            fwrite( STDOUT, "\033[?25l" );
        while ( !$stop )
        {
            $context = $makeContext();
            $result = $registry->collect( $context, $frame === 0 ? null : $watched );
            $fresh = array();
            foreach ( $result['facts'] as $fact )
                $fresh[$fact->provider][] = $fact;
            if ( $frame === 0 )
                foreach ( $result['runs'] as $run )
                    $order[] = $run['id'];
            foreach ( $result['runs'] as $run )
                $byProvider[$run['id']] = isset( $fresh[$run['id']] ) ? $fresh[$run['id']] : array();
            $facts = array();
            foreach ( $order as $id )
                foreach ( isset( $byProvider[$id] ) ? $byProvider[$id] : array() as $fact )
                    $facts[] = $fact;
            $facts = self::filterSections( $facts, $sections );
            $meta = self::meta( $mode, $facts, $context );
            $card = $plain ? $renderer->plain( $facts, $meta ) : $renderer->text( $facts, $meta );
            $footer = 'Every ' . rtrim( rtrim( number_format( $interval, 1, '.', '' ), '0' ), '.' ) . ' s, ' . date( 'H:i:s' ) . '  (Ctrl-C stops)';
            fwrite( STDOUT, ( $tty ? "\033[H\033[2J" : ( $frame > 0 ? "\n" : '' ) ) . $card . "\n" . $renderer->theme->paint( $footer, 'Dim', $renderer->depth ) . "\n" );
            $frame++;
            if ( $count > 0 && $frame >= $count )
                break;
            $until = microtime( true ) + $interval;
            while ( !$stop && microtime( true ) < $until )
                usleep( 100000 );
        }
        if ( $tty )
            fwrite( STDOUT, "\033[?25h" );
    }

    /**
     * The reveal of --animate: the logo appears line by line while its gradient moves, in under a second; then the
     * card is drawn over it.
     */
    protected static function animate( \expSysCardRenderer $renderer, array $facts, array $meta )
    {
        $layout = $renderer->layout();
        if ( empty( $layout['logo'] ) )
            return;
        $frames = 10;
        $lines = count( $layout['logo'] );
        for ( $f = 1; $f <= $frames; $f++ )
        {
            $shown = (int)ceil( $lines * $f / $frames );
            $painted = $renderer->paintLogo( $layout, ( $frames - $f ) / $frames );
            $out = '';
            for ( $i = 0; $i < $lines; $i++ )
                $out .= ( $i < $shown && isset( $painted[$i] ) ? $painted[$i] : '' ) . "\033[K\n";
            fwrite( STDOUT, $out . "\033[" . $lines . 'A' );
            usleep( 60000 );
        }
    }

    /**
     * The environment the card reads (NO_COLOR, COLORTERM, TERM, the locale, COLUMNS).
     *
     * @return array
     */
    public static function environment()
    {
        $env = array();
        foreach ( array( 'NO_COLOR', 'COLORTERM', 'TERM', 'LC_ALL', 'LC_CTYPE', 'LANG', 'COLUMNS', 'LINES' ) as $name )
        {
            $value = getenv( $name );
            if ( $value !== false )
                $env[$name] = $value;
        }
        return $env;
    }

    /**
     * @param resource $stream
     * @return bool
     */
    public static function isTerminal( $stream )
    {
        if ( function_exists( 'stream_isatty' ) )
            return @stream_isatty( $stream );
        return function_exists( 'posix_isatty' ) && @posix_isatty( $stream );
    }

    /**
     * The terminal's width: COLUMNS, else stty, else the default.
     *
     * @param array $env
     * @param bool $tty
     * @param int $default
     * @return int
     */
    public static function terminalWidth( array $env, $tty, $default = 80 )
    {
        if ( isset( $env['COLUMNS'] ) && ctype_digit( (string)$env['COLUMNS'] ) && (int)$env['COLUMNS'] > 0 )
            return (int)$env['COLUMNS'];
        $size = $tty ? self::stty() : null;
        return $size !== null ? $size[1] : max( 20, (int)$default );
    }

    /**
     * @param array $env
     * @return int the terminal's rows, 0 when unknown
     */
    public static function terminalRows( array $env )
    {
        if ( isset( $env['LINES'] ) && ctype_digit( (string)$env['LINES'] ) )
            return (int)$env['LINES'];
        $size = self::stty();
        return $size !== null ? $size[0] : 0;
    }

    /**
     * stty size, with the terminal as its input.
     *
     * @return int[]|null array( rows, columns )
     */
    protected static function stty()
    {
        static $size = false;
        if ( $size !== false )
            return $size;
        $size = null;
        if ( !function_exists( 'proc_open' ) || !self::isTerminal( STDIN ) )
            return $size;
        $pipes = array();
        $process = @proc_open( array( 'stty', 'size' ), array( 0 => STDIN, 1 => array( 'pipe', 'w' ), 2 => array( 'file', '/dev/null', 'w' ) ), $pipes );
        if ( !is_resource( $process ) )
            return $size;
        $out = (string)stream_get_contents( $pipes[1] );
        fclose( $pipes[1] );
        proc_close( $process );
        if ( preg_match( '/^(\d+)\s+(\d+)/', trim( $out ), $m ) && (int)$m[2] > 0 )
            $size = array( (int)$m[1], (int)$m[2] );
        return $size;
    }

    /**
     * The table of --list-providers.
     *
     * @param \expSysCardRegistry $registry
     * @return string
     */
    public static function providerTable( \expSysCardRegistry $registry )
    {
        $rows = array( array( 'id', 'class', 'cards', 'priority', 'budget', 'state' ) );
        $listed = array();
        foreach ( array( 'card', 'info', 'about' ) as $mode )
            foreach ( $registry->providers( $mode ) as $provider )
                $listed[get_class( $provider )] = $provider;
        foreach ( $registry->classes() as $class )
        {
            if ( isset( $listed[$class] ) )
            {
                $p = $listed[$class];
                $rows[] = array( $p->id(), $class, implode( ',', $p->modes() ), (string)$registry->priorityOf( $p ), $registry->budgetOf( $p ) . ' s', 'on' );
            }
            elseif ( class_exists( $class ) && is_subclass_of( $class, 'expSysCardProvider' ) )
            {
                $p = new $class();
                $rows[] = array( $p->id(), $class, implode( ',', $p->modes() ), (string)$registry->priorityOf( $p ), $registry->budgetOf( $p ) . ' s', 'off' );
            }
            else
                $rows[] = array( '?', $class, '', '', '', 'missing' );
        }
        $widths = array();
        foreach ( $rows as $row )
            foreach ( $row as $i => $cell )
                $widths[$i] = max( isset( $widths[$i] ) ? $widths[$i] : 0, strlen( $cell ) );
        $out = '';
        foreach ( $rows as $row )
        {
            $line = '';
            foreach ( $row as $i => $cell )
                $line .= str_pad( $cell, $widths[$i] + 2 );
            $out .= rtrim( $line ) . "\n";
        }
        return $out;
    }

    /**
     * A shell completion script for the three commands.
     *
     * @param string $shell bash or zsh
     * @param string[] $themes
     * @return string|null
     */
    public static function completion( $shell, array $themes )
    {
        $shell = strtolower( trim( $shell ) );
        if ( !in_array( $shell, array( 'bash', 'zsh' ), true ) )
            return null;
        $options = array();
        foreach ( self::OPTIONS as $name => $help )
            $options[] = '--' . $name . ( in_array( $name, array( 'section', 'color', 'theme', 'width', 'count', 'mode', 'completion' ), true ) ? '=' : '' );
        $commands = implode( ' ', array_keys( self::NAMES ) );
        $themeList = implode( ' ', $themes ? $themes : array( 'rainbow', 'orange', 'mono', 'high-contrast', 'light' ) );
        $script = "# Completion of ./console " . implode( ', ', array_keys( self::NAMES ) ) . " (" . $shell . ")\n"
                . ( $shell === 'zsh' ? "# Add to ~/.zshrc: eval \"\$(./console exp:expfetch --completion=zsh)\"\nautoload -Uz bashcompinit && bashcompinit\n"
                                     : "# Add to ~/.bashrc: eval \"\$(./console exp:expfetch --completion=bash)\"\n" )
                . "_exp_syscard_complete() {\n"
                . "    local cur=\"\${COMP_WORDS[COMP_CWORD]}\"\n"
                . "    case \"\${COMP_WORDS[1]}\" in\n"
                . "        " . str_replace( ' ', '|', $commands ) . ")\n"
                . "            case \"\$cur\" in\n"
                . "                --theme=*) COMPREPLY=( \$(compgen -P --theme= -W \"" . $themeList . "\" -- \"\${cur#--theme=}\") ); return ;;\n"
                . "                --color=*) COMPREPLY=( \$(compgen -P --color= -W \"auto truecolor 256 16 none\" -- \"\${cur#--color=}\") ); return ;;\n"
                . "                --mode=*) COMPREPLY=( \$(compgen -P --mode= -W \"card info about\" -- \"\${cur#--mode=}\") ); return ;;\n"
                . "                --completion=*) COMPREPLY=( \$(compgen -P --completion= -W \"bash zsh\" -- \"\${cur#--completion=}\") ); return ;;\n"
                . "                --section=*) COMPREPLY=( \$(compgen -P --section= -W \"system php exponential engine database site extensions siteaccesses settings cron queue caches help about project links contribute credits\" -- \"\${cur#--section=}\") ); return ;;\n"
                . "            esac\n"
                . "            COMPREPLY=( \$(compgen -W \"" . implode( ' ', $options ) . "\" -- \"\$cur\") ); return ;;\n"
                . "    esac\n"
                . "    [ \"\$COMP_CWORD\" -eq 1 ] && COMPREPLY=( \$(compgen -W \"" . $commands . "\" -- \"\$cur\") )\n"
                . "}\n"
                . "complete -o default -F _exp_syscard_complete console ./console bin/php/console\n";
        return $script;
    }
}

}
