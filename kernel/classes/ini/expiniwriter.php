<?php
/**
 * File containing the expIniWriter class: the line-preserving model of one INI file that expIniEditor edits.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One INI file as a list of lines, read exactly the way eZINI::parseFile() reads it, and changed one line at a
 * time: comments, blank lines, the order of everything, the PHP wrapper of a *.ini.append.php file and the line
 * ends (LF or CRLF, per line) stay as they are; only the lines an operation touches change. With no operation,
 * content() is byte for byte the input.
 *
 * The reading rules, taken from eZINI::parseFile():
 *  - the file is split on LF; every CR is ignored when reading a line
 *  - a line that is empty or starts with '#' is a comment; a trailing '##...' is a comment ('/^(.+)##.*'/, the
 *    last '##' on the line)
 *  - '[Name]' (anything up to the last ']', trailing white space allowed) starts a block, the name trimmed
 *  - 'Var[]' resets an array, 'Var=value' sets a plain value, 'Var[]=value' appends, 'Var[key]=value' sets a hash
 *    entry; a key that PHP finds false ('' or '0') appends instead, exactly as eZINI does
 *  - the variable name is [\w_*@-]+ and must start the line; the value is everything after '=', untrimmed
 *  - anything else (the '<?php /*' and '*' . '/ ?>' lines, indented lines) is ignored
 */
class expIniWriter
{
    /** @var string[] Lines as split on LF; a CRLF line keeps its trailing CR */
    protected $lines = array();

    /** @var bool The input used CRLF line ends (most of its lines) */
    protected $crlf = false;

    /** @var bool The file is a PHP-wrapped INI file (starts with '<?php') */
    protected $phpWrapped = false;

    /** @var array|null Cached analysis */
    protected $entries = null;

    /**
     * @param string $content The file's bytes ('' for a new file)
     */
    public function __construct( $content = '' )
    {
        $content = (string)$content;
        $this->lines = $content === '' ? array() : explode( "\n", $content );
        // '<?php' or a short open tag ('<?/*', a few old extension files)
        $this->phpWrapped = strncmp( ltrim( $content ), '<?', 2 ) === 0;
        $withCr = 0;
        $total = 0;
        foreach ( $this->lines as $i => $line )
        {
            if ( $i === count( $this->lines ) - 1 )
                break;
            ++$total;
            if ( substr( $line, -1 ) === "\r" )
                ++$withCr;
        }
        $this->crlf = $total > 0 && $withCr * 2 > $total;
    }

    /**
     * The model of an existing file.
     *
     * @param string $path
     * @return expIniWriter
     * @throws expIniException WRITE_FAILED when it cannot be read
     */
    public static function fromFile( $path )
    {
        $content = @file_get_contents( $path );
        if ( $content === false )
            throw expIniException::writeFailed( "Cannot read $path" );
        return new self( $content );
    }

    /**
     * The skeleton of a new *.ini.append.php file, the way the files in settings/override are written.
     *
     * @return expIniWriter
     */
    public static function newPhpWrapped()
    {
        return new self( "<?php /* #?ini charset=\"utf-8\"?\n\n*/ ?>" );
    }

    /** @return string The file's bytes */
    public function content()
    {
        return implode( "\n", $this->lines );
    }

    /** @return string[] Lines without line ends */
    public function displayLines()
    {
        return array_map( function ( $l ) { return rtrim( $l, "\r" ); }, $this->lines );
    }

    /** @return bool */
    public function isPhpWrapped()
    {
        return $this->phpWrapped;
    }

    /** @return bool */
    public function isCrlf()
    {
        return $this->crlf;
    }

    /**
     * Analyses every line.
     *
     * @return array index => array( type, block, var, key, value, core, comment ); type is one of comment,
     *               block, reset, plain, append, hash, other
     */
    public function entries()
    {
        if ( $this->entries !== null )
            return $this->entries;
        $entries = array();
        $block = '';
        foreach ( $this->lines as $i => $raw )
        {
            $line = str_replace( "\r", '', $raw );
            $entry = array( 'type' => 'comment', 'block' => $block, 'var' => null, 'key' => null,
                            'value' => null, 'core' => $line, 'comment' => '' );
            if ( $line === '' || $line[0] === '#' )
            {
                $entries[$i] = $entry;
                continue;
            }
            $core = $line;
            if ( preg_match( "/^(.+)##.*/", $line, $regs ) )
            {
                $core = $regs[1];
                $entry['comment'] = (string)substr( $line, strlen( $core ) );
                $entry['core'] = $core;
            }
            if ( trim( $core ) === '' )
            {
                $entries[$i] = $entry;
                continue;
            }
            if ( preg_match( "#^\[(.+)\]\s*$#", $core, $m ) )
            {
                $block = trim( $m[1] );
                $entry['type'] = 'block';
                $entry['block'] = $block;
            }
            else if ( preg_match( "#^([\w_*@-]+)\\[\\]$#", $core, $m ) )
            {
                $entry['type'] = 'reset';
                $entry['var'] = trim( $m[1] );
            }
            else if ( preg_match( "#^([\w_*@-]+)(\\[([^\\]]*)\\])?=(.*)$#", $core, $m ) )
            {
                $entry['var'] = trim( $m[1] );
                $entry['value'] = $m[4];
                if ( $m[2] )
                {
                    if ( $m[3] )
                    {
                        $entry['type'] = 'hash';
                        $entry['key'] = $m[3];
                    }
                    else
                    {
                        $entry['type'] = 'append';
                    }
                }
                else
                {
                    $entry['type'] = 'plain';
                }
            }
            else
            {
                $entry['type'] = 'other';
            }
            $entries[$i] = $entry;
        }
        return $this->entries = $entries;
    }

    /**
     * Every block and variable as eZINI would read them from this one file (its BlockValues after parseFile()).
     *
     * @return array block => array( variable => string|array )
     */
    public function values()
    {
        $values = array();
        foreach ( $this->entries() as $e )
        {
            $b = $e['block'];
            $v = $e['var'];
            switch ( $e['type'] )
            {
                case 'block':
                    if ( !isset( $values[$b] ) )
                        $values[$b] = array();
                    break;
                case 'reset':
                    $values[$b][$v] = array();
                    break;
                case 'plain':
                    $values[$b][$v] = $e['value'];
                    break;
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
        return $values;
    }

    /**
     * Block names, in the order they first appear.
     *
     * @return string[]
     */
    public function blocks()
    {
        $blocks = array();
        foreach ( $this->entries() as $e )
        {
            if ( $e['type'] === 'block' && !in_array( $e['block'], $blocks, true ) )
                $blocks[] = $e['block'];
        }
        return $blocks;
    }

    /**
     * @param string $block
     * @return bool
     */
    public function hasBlock( $block )
    {
        return in_array( $block, $this->blocks(), true );
    }

    /**
     * Line indexes of a variable in a block (every occurrence of the block), in file order.
     *
     * @param string $block
     * @param string $var
     * @param string|null $type Only lines of this type
     * @return int[]
     */
    public function variableLines( $block, $var, $type = null )
    {
        $found = array();
        foreach ( $this->entries() as $i => $e )
        {
            if ( $e['var'] === $var && $e['block'] === $block && $e['type'] !== 'block'
                 && ( $type === null || $e['type'] === $type ) )
                $found[] = $i;
        }
        return $found;
    }

    /**
     * The entry of one line.
     *
     * @param int $index
     * @return array
     */
    public function entry( $index )
    {
        $entries = $this->entries();
        return $entries[$index];
    }

    /**
     * Replaces the value of an assignment line, keeping its name, key, '##' comment and line end.
     *
     * @param int $index
     * @param string $value
     */
    public function replaceValue( $index, $value )
    {
        $e = $this->entry( $index );
        $core = $e['core'];
        $eq = strpos( $core, '=' );
        $head = substr( $core, 0, $eq + 1 );
        $old = (string)substr( $core, $eq + 1 );
        $tail = '';
        if ( $e['comment'] !== '' )
        {
            // keep the white space that separated the value from its '##' comment
            $tail = substr( $old, strlen( rtrim( $old ) ) ) . $e['comment'];
        }
        $this->setLine( $index, $head . $value . $tail );
    }

    /**
     * Sets the text of one line, keeping its line end.
     *
     * @param int $index
     * @param string $text Without line end
     */
    public function setLine( $index, $text )
    {
        $cr = substr( $this->lines[$index], -1 ) === "\r" ? "\r" : '';
        $this->lines[$index] = $text . $cr;
        $this->entries = null;
    }

    /**
     * Removes lines.
     *
     * @param int[] $indexes
     */
    public function removeLines( array $indexes )
    {
        rsort( $indexes );
        $last = count( $this->lines ) - 1;
        foreach ( $indexes as $i )
        {
            if ( $i === $last && $i > 0 && substr( $this->lines[$i], -1 ) !== "\r" )
            {
                // the last line had no line end: neither has the line that becomes last
                $this->lines[$i - 1] = rtrim( $this->lines[$i - 1], "\r" );
            }
            array_splice( $this->lines, $i, 1 );
            $last = count( $this->lines ) - 1;
        }
        $this->entries = null;
    }

    /**
     * Inserts lines before line $index (count() = at the end), with the file's line ends.
     *
     * @param int $index
     * @param string[] $texts Without line ends
     */
    public function insertLines( $index, array $texts )
    {
        $cr = $this->crlf ? "\r" : '';
        $count = count( $this->lines );
        $new = array();
        foreach ( $texts as $t )
            $new[] = $t . $cr;
        if ( $index >= $count )
        {
            // appended after the last line, which had no line end: it gets one, the new last line has none
            if ( $count > 0 )
                $this->lines[$count - 1] = rtrim( $this->lines[$count - 1], "\r" ) . $cr;
            $new[count( $new ) - 1] = rtrim( $new[count( $new ) - 1], "\r" );
            $index = $count;
        }
        array_splice( $this->lines, $index, 0, $new );
        $this->entries = null;
    }

    /**
     * Where new blocks go: before the closing lines of the PHP wrapper ('*' . '/ ?>', '*' . '/', '?>') and the blank
     * lines and the empty final line around them; at the end of a plain file (before its final empty line).
     *
     * @return int Line index to insert before
     */
    public function endOfSettings()
    {
        $i = count( $this->lines );
        while ( $i > 0 )
        {
            $text = str_replace( "\r", '', $this->lines[$i - 1] );
            $isTrailer = trim( $text ) === ''
                || ( $this->phpWrapped && preg_match( '#^\s*(\*\s*)?\*/\s*(\?>)?\s*$#', $text ) )
                || ( $this->phpWrapped && preg_match( '#^\s*\?>\s*$#', $text ) );
            if ( !$isTrailer )
                break;
            --$i;
        }
        return $i;
    }

    /**
     * Where a new variable of a block goes: after the last setting line (or the header) of the block's last
     * occurrence. False when the block is not in the file.
     *
     * @param string $block
     * @return int|false Line index to insert before
     */
    public function endOfBlock( $block )
    {
        $pos = false;
        $in = false;
        foreach ( $this->entries() as $i => $e )
        {
            if ( $e['type'] === 'block' )
            {
                $in = $e['block'] === $block;
                if ( $in )
                    $pos = $i + 1;
                continue;
            }
            if ( $in && in_array( $e['type'], array( 'reset', 'plain', 'append', 'hash' ), true ) )
                $pos = $i + 1;
        }
        return $pos;
    }

    /**
     * Every occurrence of a block as array( header line, last line of its region ). The region runs to the line
     * before the next block's header, without the comment lines directly above that header (they describe the
     * next block), or to the line before endOfSettings().
     *
     * @param string $block
     * @return array[]
     */
    public function blockRegions( $block )
    {
        $entries = $this->entries();
        $endOfSettings = $this->endOfSettings();
        $headers = array();
        foreach ( $entries as $i => $e )
        {
            if ( $e['type'] === 'block' )
                $headers[] = $i;
        }
        $regions = array();
        foreach ( $headers as $n => $h )
        {
            if ( $entries[$h]['block'] !== $block )
                continue;
            if ( isset( $headers[$n + 1] ) )
            {
                $end = $headers[$n + 1] - 1;
                while ( $end > $h && $this->isCommentLine( $end ) )
                    --$end;
            }
            else
            {
                $end = max( $h, $endOfSettings - 1 );
            }
            $regions[] = array( $h, $end );
        }
        return $regions;
    }

    /** A '#' comment line (not a blank one). */
    protected function isCommentLine( $index )
    {
        $text = str_replace( "\r", '', $this->lines[$index] );
        return $text !== '' && $text[0] === '#';
    }

    /**
     * Inserts setting lines at the end of a block, adding the block at the end of the settings when it is
     * missing.
     *
     * @param string $block
     * @param string[] $texts
     */
    public function insertInBlock( $block, array $texts )
    {
        $pos = $this->endOfBlock( $block );
        if ( $pos !== false )
        {
            $this->insertLines( $pos, $texts );
            return;
        }
        $pos = $this->endOfSettings();
        $head = array();
        if ( $pos > 0 )
        {
            $prev = str_replace( "\r", '', $this->lines[$pos - 1] );
            if ( trim( $prev ) !== '' )
                $head[] = '';
        }
        $head[] = '[' . $block . ']';
        $this->insertLines( $pos, array_merge( $head, $texts ) );
    }
}
