<?php
/**
 * File containing the expSysCardTheme class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A color theme of the system card (syscard.ini [Theme-<name>]): a #RRGGBB color for each role, the stops of the
 * gradient and its direction. Roles: Logo, LogoShade, Accent, Label, Value, Dim, Ok, Info, Warn, Error.
 * An empty color is the terminal's own. <Role>16= names the basic color to use on a 16-color terminal when the
 * nearest one would look wrong (orange becomes yellow, not red): black, red, green, yellow, blue, magenta, cyan,
 * white, or bright-<name>.
 *
 * The shipped themes: rainbow (the default), orange, mono, high-contrast and light (for a light background).
 * An extension adds a theme with a [Theme-<name>] block in its syscard.ini.append.php and Themes[]=<name>.
 */
class expSysCardTheme
{
    const ROLES = array( 'Logo', 'LogoShade', 'Accent', 'Label', 'Value', 'Dim', 'Ok', 'Info', 'Warn', 'Error' );

    const BASIC_NAMES = array( 'black', 'red', 'green', 'yellow', 'blue', 'magenta', 'cyan', 'white' );

    /** The colors of a theme that has no block (and of the roles a block leaves out) */
    const DEFAULTS = array(
        'Logo' => '#F28C28', 'LogoShade' => '#C2410C', 'Accent' => '#F28C28', 'Label' => '#F28C28', 'Value' => '',
        'Dim' => '#8A8A8A', 'Ok' => '#22C55E', 'Info' => '#38BDF8', 'Warn' => '#EAB308', 'Error' => '#EF4444',
    );

    /** @var string */
    public $name = 'rainbow';
    /** @var array role => #RRGGBB or '' */
    protected $colors = array();
    /** @var array role => basic color index (16 colors) */
    protected $basic = array();
    /** @var string[] */
    protected $gradient = array();
    /** @var string columns or lines */
    protected $direction = 'columns';
    /** @var bool */
    protected $bold = true;

    /**
     * @param string $name
     * @param array $settings role => color, <Role>16 => basic name, Gradient => list, GradientDirection, Bold
     */
    public function __construct( $name, array $settings = array() )
    {
        $this->name = (string)$name;
        foreach ( self::ROLES as $role )
        {
            $this->colors[$role] = array_key_exists( $role, $settings ) ? trim( (string)$settings[$role] ) : self::DEFAULTS[$role];
            if ( isset( $settings[$role . '16'] ) )
            {
                $index = self::basicIndex( $settings[$role . '16'] );
                if ( $index !== null )
                    $this->basic[$role] = $index;
            }
        }
        if ( isset( $settings['Gradient'] ) )
            $this->gradient = array_values( array_filter( array_map( 'trim', (array)$settings['Gradient'] ), function ( $c ) { return expSysCardColor::rgb( $c ) !== null; } ) );
        if ( isset( $settings['GradientDirection'] ) && strtolower( (string)$settings['GradientDirection'] ) === 'lines' )
            $this->direction = 'lines';
        if ( isset( $settings['Bold'] ) )
            $this->bold = expSysCardContext::isOn( $settings['Bold'] );
    }

    /**
     * A theme from syscard.ini, or the built-in rainbow colors when it has no block.
     *
     * @param string|null $name null for [Theme] Default
     * @return expSysCardTheme
     */
    public static function fromIni( $name = null )
    {
        $ini = class_exists( 'eZINI' ) && eZINI::exists( 'syscard.ini' ) ? eZINI::instance( 'syscard.ini' ) : null;
        if ( $name === null || $name === '' )
            $name = $ini && $ini->hasVariable( 'Theme', 'Default' ) ? (string)$ini->variable( 'Theme', 'Default' ) : 'rainbow';
        $name = strtolower( trim( $name ) );
        $settings = array();
        if ( $ini && $ini->hasGroup( 'Theme-' . $name ) )
            $settings = $ini->group( 'Theme-' . $name );
        return new self( $name, $settings );
    }

    /**
     * @return string[] the names of syscard.ini [Theme] Themes[]
     */
    public static function names()
    {
        $ini = class_exists( 'eZINI' ) && eZINI::exists( 'syscard.ini' ) ? eZINI::instance( 'syscard.ini' ) : null;
        $names = $ini && $ini->hasVariable( 'Theme', 'Themes' ) ? (array)$ini->variable( 'Theme', 'Themes' ) : array();
        return array_values( array_unique( array_filter( array_map( 'strtolower', array_map( 'trim', $names ) ), 'strlen' ) ) );
    }

    /**
     * @param string $name red, bright-yellow ...
     * @return int|null 0-15
     */
    public static function basicIndex( $name )
    {
        $name = strtolower( trim( (string)$name ) );
        $bright = strpos( $name, 'bright-' ) === 0;
        $index = array_search( $bright ? substr( $name, 7 ) : $name, self::BASIC_NAMES, true );
        return $index === false ? null : $index + ( $bright ? 8 : 0 );
    }

    /**
     * @param string $role
     * @return string #RRGGBB or ''
     */
    public function color( $role )
    {
        return isset( $this->colors[$role] ) ? $this->colors[$role] : '';
    }

    /**
     * @return string[] the gradient's stops (empty: no gradient)
     */
    public function gradient()
    {
        return $this->gradient;
    }

    /**
     * @return string columns or lines
     */
    public function direction()
    {
        return $this->direction;
    }

    /**
     * @return bool
     */
    public function bold()
    {
        return $this->bold;
    }

    /**
     * The SGR parameters of a role at a depth ('' for the terminal's own color).
     *
     * @param string $role
     * @param string $depth
     * @return string
     */
    public function code( $role, $depth )
    {
        if ( $depth === expSysCardColor::C16 && isset( $this->basic[$role] ) )
        {
            $index = $this->basic[$role];
            return (string)( $index < 8 ? 30 + $index : 90 + $index - 8 );
        }
        return expSysCardColor::code( $this->color( $role ), $depth );
    }

    /**
     * Text in a role's color.
     *
     * @param string $text
     * @param string $role
     * @param string $depth
     * @param bool $bold (only when the theme allows bold)
     * @return string
     */
    public function paint( $text, $role, $depth, $bold = false )
    {
        if ( $depth === expSysCardColor::NONE || $text === '' )
            return $text;
        $codes = array();
        if ( $bold && $this->bold )
            $codes[] = '1';
        $color = $this->code( $role, $depth );
        if ( $color !== '' )
            $codes[] = $color;
        elseif ( $role === 'Dim' )
            $codes[] = '2';
        return empty( $codes ) ? $text : "\033[" . implode( ';', $codes ) . 'm' . $text . "\033[0m";
    }

    /**
     * Lines with the gradient (across the columns, or one color per line), or in the Accent color when the theme
     * has no gradient.
     *
     * @param string[] $lines
     * @param string $depth
     * @param float $offset
     * @return string[]
     */
    public function paintBlock( array $lines, $depth, $offset = 0.0 )
    {
        if ( $depth === expSysCardColor::NONE )
            return $lines;
        if ( empty( $this->gradient ) )
            return array_map( function ( $line ) use ( $depth ) { return $this->paint( $line, 'Accent', $depth, true ); }, $lines );
        $width = 0;
        foreach ( $lines as $line )
            $width = max( $width, expSysCardColor::width( $line ) );
        $out = array();
        $count = count( $lines );
        foreach ( $lines as $i => $line )
        {
            if ( $this->direction === 'lines' )
            {
                $t = $count > 1 ? $i / ( $count - 1 ) : 0.0;
                $t += $offset;
                if ( $t > 1.0 )
                    $t -= floor( $t );
                $code = expSysCardColor::code( expSysCardColor::gradient( $this->gradient, $t ), $depth );
                $out[] = $code === '' ? $line : "\033[" . $code . 'm' . $line . "\033[0m";
            }
            else
                $out[] = expSysCardColor::paintGradient( $line, $this->gradient, $depth, $width, $offset );
        }
        return $out;
    }
}
