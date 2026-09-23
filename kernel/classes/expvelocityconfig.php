<?php
/**
 * File containing the expVelocityConfig class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Read and write the settings Exponential Velocity actually runs on.
 *
 * There are three files that look like the server's configuration and only one
 * of them is. It is worth being exact about which, because two of the three are
 * places where a change is silently lost:
 *
 * - `/etc/qbix/qbix.json` is read by nothing. Not the server, not this
 *   installation. A value put there has no effect whatever.
 * - `var/tmp/velocity-server.json` is what the server is handed on its command
 *   line, and it is what most people would edit on finding it. It is generated
 *   from the settings below by expVelocity::writeServerConfig() on *every*
 *   start, so an edit survives exactly until the next restart.
 * - `settings/velocity.ini`, overridden per installation by
 *   `settings/override/velocity.ini.append.php`, is the source of truth. It is
 *   the only one of the three worth writing to, and it is the one this class
 *   writes.
 *
 * Reads come from the merged view, so what you are told is what the server
 * would use. Writes go to the override, so the packaged defaults stay intact
 * and an installation's own choices remain visible as a short list rather than
 * being buried in a copy of the whole file.
 *
 * @package kernel
 */
class expVelocityConfig
{
    const INI_FILE = 'velocity.ini';
    const OVERRIDE_DIR = 'settings/override';
    const OVERRIDE_FILE = 'velocity.ini.append.php';

    /**
     * The blocks and variables the packaged velocity.ini defines.
     *
     * Used to refuse a setting the server would never read. A typo in a
     * variable name is otherwise completely silent: the file is written, the
     * value is there, and nothing uses it.
     *
     * @var array|null
     */
    protected $known = null;

    /**
     * The merged settings, as the server would see them.
     *
     * @return eZINI
     */
    protected function ini()
    {
        return eZINI::instance( self::INI_FILE );
    }

    /**
     * The override file alone, opened for writing.
     *
     * @return eZINI
     */
    protected function override()
    {
        return eZINI::instance(
            self::OVERRIDE_FILE, self::OVERRIDE_DIR, null, false, null, true
        );
    }

    /**
     * Every block and variable the packaged file defines.
     *
     * @return array block => array of variable names
     */
    public function known()
    {
        if ( $this->known !== null )
            return $this->known;

        $this->known = array();
        $path = 'settings/' . self::INI_FILE;
        if ( !is_file( $path ) )
            return $this->known;

        $block = null;
        foreach ( file( $path ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' || $line[0] === '#' )
                continue;
            if ( $line[0] === '[' && substr( $line, -1 ) === ']' )
            {
                $block = substr( $line, 1, -1 );
                if ( !isset( $this->known[$block] ) )
                    $this->known[$block] = array();
                continue;
            }
            if ( $block !== null && strpos( $line, '=' ) !== false )
            {
                $name = trim( substr( $line, 0, strpos( $line, '=' ) ) );
                if ( $name !== '' && !in_array( $name, $this->known[$block], true ) )
                    $this->known[$block][] = $name;
            }
        }
        return $this->known;
    }

    /**
     * Whether the packaged file defines this setting.
     *
     * @param string $block
     * @param string $variable
     * @return bool
     */
    public function isKnown( $block, $variable )
    {
        $known = $this->known();
        return isset( $known[$block] ) && in_array( $variable, $known[$block], true );
    }

    /**
     * Forget every cached copy of the merged settings.
     *
     * eZINI caches the parsed result to disk and memoises the instance, so a
     * write is not visible to the next read -- not even in the next process --
     * until both are dropped. Left alone, `config set` reported success and
     * `config list` immediately afterwards showed the old value, which is a
     * worse outcome than refusing to report one at all: it tells the operator
     * their change did not take when it did.
     */
    protected function forgetCaches()
    {
        $ini = eZINI::instance( self::INI_FILE );
        if ( method_exists( $ini, 'resetCache' ) )
            $ini->resetCache();

        $override = $this->override();
        if ( method_exists( $override, 'resetCache' ) )
            $override->resetCache();

        // Keep the override directories as they are; only the parsed copies
        // are stale.
        eZINI::resetAllInstances( false );
    }

    /**
     * The value the server would use.
     *
     * @param string $block
     * @param string $variable
     * @return array ok, value, overridden
     */
    public function get( $block, $variable )
    {
        $ini = $this->ini();
        if ( !$ini->hasVariable( $block, $variable ) )
        {
            return array( 'ok' => false,
                'message' => "[$block] $variable is not set" );
        }

        $override = $this->override();
        return array(
            'ok' => true,
            'block' => $block,
            'variable' => $variable,
            'value' => $ini->variable( $block, $variable ),
            'overridden' => $override->hasVariable( $block, $variable ),
        );
    }

    /**
     * Every setting the server would use, with where each came from.
     *
     * @param string|null $onlyBlock
     * @return array
     */
    public function listAll( $onlyBlock = null )
    {
        $ini = $this->ini();
        $override = $this->override();
        $out = array();

        foreach ( $this->known() as $block => $variables )
        {
            if ( $onlyBlock !== null && strcasecmp( $block, $onlyBlock ) !== 0 )
                continue;
            foreach ( $variables as $variable )
            {
                if ( !$ini->hasVariable( $block, $variable ) )
                    continue;
                $out[] = array(
                    'block' => $block,
                    'variable' => $variable,
                    'value' => $ini->variable( $block, $variable ),
                    'overridden' => $override->hasVariable( $block, $variable ),
                );
            }
        }
        return $out;
    }

    /**
     * Write a setting to the installation's override.
     *
     * Refuses a variable the packaged file does not define. A name that is
     * almost right is the failure worth catching here, because nothing later
     * complains: the file is written, the value sits in it, and the server goes
     * on using the default while the operator believes otherwise.
     *
     * @param string $block
     * @param string $variable
     * @param string $value
     * @return array
     */
    public function set( $block, $variable, $value )
    {
        if ( !$this->isKnown( $block, $variable ) )
        {
            return array( 'ok' => false, 'message' =>
                "[$block] $variable is not a setting velocity.ini defines; " .
                "run 'config list' to see what is" );
        }

        $before = $this->get( $block, $variable );

        $override = $this->override();
        $override->setVariable( $block, $variable, $value );
        if ( !$override->save() )
        {
            return array( 'ok' => false, 'message' =>
                'could not write ' . self::OVERRIDE_DIR . '/' . self::OVERRIDE_FILE );
        }

        // The override holds a private key path among other things, so it is
        // not for every account on the machine to read.
        @chmod( self::OVERRIDE_DIR . '/' . self::OVERRIDE_FILE, 0600 );

        $this->forgetCaches();

        return array( 'ok' => true, 'message' =>
            "[$block] $variable set to '$value'" .
            ( ( !empty( $before['ok'] ) && (string)$before['value'] !== (string)$value )
                ? " (was '" . $before['value'] . "')" : '' ) .
            "\n  the server reads this at start; run 'graceful' or 'restart' to apply it" );
    }

    /**
     * Remove a setting from the override, returning it to the packaged value.
     *
     * @param string $block
     * @param string $variable
     * @return array
     */
    public function unsetVariable( $block, $variable )
    {
        $override = $this->override();
        if ( !$override->hasVariable( $block, $variable ) )
        {
            return array( 'ok' => false, 'message' =>
                "[$block] $variable is not overridden here; nothing to remove" );
        }

        $override->removeSetting( $block, $variable );
        if ( !$override->save() )
        {
            return array( 'ok' => false, 'message' =>
                'could not write ' . self::OVERRIDE_DIR . '/' . self::OVERRIDE_FILE );
        }
        @chmod( self::OVERRIDE_DIR . '/' . self::OVERRIDE_FILE, 0600 );

        $this->forgetCaches();

        $now = $this->get( $block, $variable );
        return array( 'ok' => true, 'message' =>
            "[$block] $variable removed from the override" .
            ( !empty( $now['ok'] ) ? "; the packaged value '" . $now['value'] . "' applies again" : '' ) .
            "\n  run 'graceful' or 'restart' to apply it" );
    }

    /**
     * Where the settings actually live, for anyone about to edit the wrong file.
     *
     * @return array
     */
    public function paths()
    {
        return array(
            'source of truth' => 'settings/' . self::INI_FILE,
            'this installation' => self::OVERRIDE_DIR . '/' . self::OVERRIDE_FILE,
            'generated on every start, do not edit' =>
                'var/tmp/velocity-server.json',
            'read by nothing' => '/etc/qbix/qbix.json',
        );
    }
}
