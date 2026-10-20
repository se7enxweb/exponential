<?php
/**
 * File containing the expSysCardBaseProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The usual provider: set the properties, write collect(), and use fact() and the helpers.
 *
 *   class myExtSysCardProvider extends expSysCardBaseProvider
 *   {
 *       protected $id = 'myext';
 *       protected $section = 'site';
 *       public function collect( expSysCardContext $context )
 *       {
 *           return array( $this->fact( 'items', 'My items', 42 ) );
 *       }
 *   }
 */
abstract class expSysCardBaseProvider implements expSysCardProvider
{
    /** @var string */
    protected $id = '';
    /** @var string the section of fact() */
    protected $section = 'system';
    /** @var string[] */
    protected $modes = array( expSysCardContext::MODE_CARD );
    /** @var int */
    protected $priority = 50;
    /** @var float */
    protected $budget = 1.0;
    /** @var bool */
    protected $watched = false;

    public function id()
    {
        if ( $this->id === '' )
            $this->id = strtolower( (string)preg_replace( '/^expSysCard|Provider$/', '', get_class( $this ) ) );
        return $this->id;
    }

    public function modes()
    {
        return $this->modes;
    }

    public function priority()
    {
        return (int)$this->priority;
    }

    public function budget()
    {
        return (float)$this->budget;
    }

    public function watched()
    {
        return (bool)$this->watched;
    }

    /**
     * @return string the section its facts go to (unless a fact names another)
     */
    public function section()
    {
        return $this->section;
    }

    /**
     * A fact of this provider: its key is "<id>.<name>", its section the provider's unless given.
     *
     * @param string $name
     * @param string $label
     * @param mixed $value
     * @param string $severity
     * @param string $hint
     * @param string|null $section
     * @return expSysCardFact
     */
    protected function fact( $name, $label, $value, $severity = expSysCardFact::INFO, $hint = '', $section = null )
    {
        $fact = new expSysCardFact( $this->id() . '.' . $name, $label, $value, $severity, $section === null ? $this->section : $section, $hint );
        $fact->provider = $this->id();
        return $fact;
    }

    /**
     * A paragraph of this provider.
     *
     * @param string $name
     * @param string $text
     * @param string $label
     * @param string|null $section
     * @return expSysCardFact
     */
    protected function text( $name, $text, $label = '', $section = null )
    {
        $fact = expSysCardFact::text( $this->id() . '.' . $name, $text, $section === null ? $this->section : $section, $label );
        $fact->provider = $this->id();
        return $fact;
    }

    /**
     * A size in bytes for people: 1.5 GiB, 640 MiB, 12 KiB.
     *
     * @param int|float|null $bytes
     * @return string
     */
    public static function size( $bytes )
    {
        if ( $bytes === null || $bytes < 0 )
            return 'n/a';
        $units = array( 'B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB' );
        $i = 0;
        $bytes = (float)$bytes;
        while ( $bytes >= 1024 && $i < count( $units ) - 1 )
        {
            $bytes /= 1024;
            $i++;
        }
        return ( $i === 0 ? (string)(int)$bytes : number_format( $bytes, $bytes >= 100 ? 0 : 1, '.', '' ) ) . ' ' . $units[$i];
    }

    /**
     * A php.ini size ("128M", "1G", "-1") in bytes; -1 stays -1.
     *
     * @param string $value
     * @return int
     */
    public static function bytes( $value )
    {
        $value = trim( (string)$value );
        if ( $value === '' )
            return 0;
        if ( $value === '-1' )
            return -1;
        $number = (float)$value;
        switch ( strtolower( substr( $value, -1 ) ) )
        {
            case 'g':
                $number *= 1073741824;
                break;
            case 'm':
                $number *= 1048576;
                break;
            case 'k':
                $number *= 1024;
                break;
        }
        return (int)$number;
    }

    /**
     * A duration for people: 3d 4h, 5h 12m, 7m.
     *
     * @param int|float $seconds
     * @return string
     */
    public static function duration( $seconds )
    {
        $seconds = max( 0, (int)$seconds );
        $days = intdiv( $seconds, 86400 );
        $hours = intdiv( $seconds % 86400, 3600 );
        $minutes = intdiv( $seconds % 3600, 60 );
        if ( $days > 0 )
            return $days . 'd ' . $hours . 'h';
        if ( $hours > 0 )
            return $hours . 'h ' . $minutes . 'm';
        return $minutes . 'm';
    }

    /**
     * How long ago a time was: "50 s ago", "5 min ago", "3 h ago", "2 days ago".
     *
     * @param int $time
     * @param int $now
     * @return string
     */
    public static function ago( $time, $now )
    {
        $diff = max( 0, (int)$now - (int)$time );
        if ( $diff < 120 )
            return $diff . ' s ago';
        if ( $diff < 7200 )
            return intdiv( $diff, 60 ) . ' min ago';
        if ( $diff < 172800 )
            return intdiv( $diff, 3600 ) . ' h ago';
        return intdiv( $diff, 86400 ) . ' days ago';
    }

    /**
     * The first match of a pattern in a text, or ''.
     *
     * @param string $pattern
     * @param string|null $text
     * @param int $group
     * @return string
     */
    protected static function match( $pattern, $text, $group = 1 )
    {
        return is_string( $text ) && preg_match( $pattern, $text, $m ) && isset( $m[$group] ) ? trim( $m[$group] ) : '';
    }
}
