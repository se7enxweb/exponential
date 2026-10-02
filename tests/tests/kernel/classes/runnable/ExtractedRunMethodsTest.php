<?php
/**
 * The run() methods split into named protected methods (#207 stage 6), guide doc/bc/6.0/cli_cronjob_view_abstractions.md.
 *
 *  ER-01 — Every extracted method of the split runnables is protected, takes run()'s variables by reference and is
 *          called from run() exactly once
 *  ER-02 — The split run() methods stay short (the longest were 500 to 1100 lines)
 *  ER-03 — A method that can return from run() ends with "return $this;", and its call returns anything else
 *  ER-04 — A command's method binds the script's global variables as run() does, and only names run() binds
 *  ER-05 — The pattern itself: variables changed, created and read through the references, an early return
 *          (null included) passed on, $this meaning "run() goes on"
 *
 * No database.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group runnable
 */

class ezpTestExtractedRun
{
    public $log = array();

    public function run( $flag )
    {
        $count = 1;
        if ( ( $__return = $this->step( $flag, $count, $created ) ) !== $this )
            return $__return;
        $this->log[] = "count $count created $created";
        return 'end';
    }

    protected function step( &$flag, &$count, &$created )
    {
        $count++;
        $created = 'yes';
        if ( $flag === 'early' )
            return 'early';
        if ( $flag === 'null' )
            return null;
        return $this;
    }
}

class ExtractedRunMethodsTest extends PHPUnit\Framework\TestCase
{
    /** run() of each split file and the most lines it may have now */
    const FILES = array(
        'views/setup/info.php' => 160,
        'views/shop/dashboard.php' => 140,
        'views/class/edit.php' => 290,
        'views/role/edit.php' => 250,
        'views/content/edit.php' => 170,
        'views/rss/edit_export.php' => 150,
        'commands/ezpm.php' => 300,
        'commands/updateniceurls.php' => 400,
        'commands/preload.php' => 320,
        'commands/cache.php' => 240,
    );

    const MARKER = 'Part of run(), moved here unchanged (#207 stage 6)';

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function source( $file )
    {
        return (string) file_get_contents( self::root() . '/kernel/private/classes/' . $file );
    }

    /** @return array name => array( signature parameters, body ) of the extracted methods */
    private static function extracted( $code )
    {
        $out = array();
        preg_match_all( '/' . preg_quote( self::MARKER, '/' ) . '.*?\*\/\n    (\w+(?: \w+)*) function (\w+)\((.*?)\)\n    \{\n(.*?)\n    \}\n/s', $code, $ms, PREG_SET_ORDER );
        foreach ( $ms as $m )
            $out[$m[2]] = array( 'modifiers' => $m[1], 'params' => trim( $m[3] ), 'body' => $m[4] );
        return $out;
    }

    private static function runLength( $code )
    {
        $tokens = token_get_all( $code );
        $depth = 0; $start = 0; $inRun = false; $line = 1;
        foreach ( $tokens as $i => $t )
        {
            $text = is_array( $t ) ? $t[1] : $t;
            if ( is_array( $t ) && $t[0] === T_STRING && $t[1] === 'run' && !$inRun && $start === 0 )
            {
                $start = $line;
                $inRun = true;
            }
            if ( $inRun && ( $text === '{' || ( is_array( $t ) && in_array( $t[0], array( T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES ) ) ) ) )
                $depth++;
            if ( $inRun && $text === '}' )
            {
                $depth--;
                if ( $depth === 0 )
                    return $line - $start + 1;
            }
            $line += substr_count( $text, "\n" );
        }
        return -1;
    }

    /** ER-01 */
    public function testExtractedMethods()
    {
        $total = 0;
        foreach ( array_keys( self::FILES ) as $file )
        {
            $code = self::source( $file );
            $methods = self::extracted( $code );
            $this->assertNotEmpty( $methods, "$file has extracted methods" );
            foreach ( $methods as $name => $m )
            {
                $this->assertSame( 'protected', $m['modifiers'], "$file $name() is protected" );
                if ( $m['params'] !== '' )
                    foreach ( explode( ',', $m['params'] ) as $p )
                        $this->assertMatchesRegularExpression( '/^&\$\w+$/', trim( $p ), "$file $name(): run()'s variable by reference" );
                $this->assertSame( 1, substr_count( $code, '$this->' . $name . '(' ), "$file $name() is called once" );
                $total++;
            }
        }
        $this->assertGreaterThanOrEqual( 50, $total );
    }

    /** ER-02 */
    public function testRunIsShort()
    {
        foreach ( self::FILES as $file => $limit )
        {
            $length = self::runLength( self::source( $file ) );
            $this->assertGreaterThan( 0, $length, "$file has run()" );
            $this->assertLessThanOrEqual( $limit, $length, "$file run() is $length lines" );
        }
    }

    /** ER-03 */
    public function testEarlyReturns()
    {
        $returning = 0;
        foreach ( array_keys( self::FILES ) as $file )
        {
            $code = self::source( $file );
            foreach ( self::extracted( $code ) as $name => $m )
            {
                $calledWithReturn = (bool) preg_match( '/if \( \( \$__return = \$this->' . $name . '\(.*?\) \) !== \$this \)\n +return \$__return;\n/', $code );
                $endsWithThis = substr( rtrim( $m['body'] ), -strlen( 'return $this;' ) ) === 'return $this;';
                $this->assertSame( $calledWithReturn, $endsWithThis, "$file $name(): an early return has its call pass it on" );
                if ( $calledWithReturn )
                    $returning++;
            }
        }
        $this->assertGreaterThan( 0, $returning );
    }

    /** ER-04 */
    public function testCommandsBindTheirGlobals()
    {
        foreach ( array_keys( self::FILES ) as $file )
        {
            if ( strpos( $file, 'commands/' ) !== 0 )
                continue;
            $code = self::source( $file );
            $this->assertMatchesRegularExpression( '/public function run\(\)\n    \{\n.*?foreach \( array\(([^)]*)\) as \$__name \)/s', $code );
            preg_match( '/public function run\(\)\n    \{\n.*?foreach \( array\(([^)]*)\) as \$__name \)/s', $code, $r );
            $runNames = array_map( function ( $s ) { return trim( $s, " '" ); }, explode( ',', $r[1] ) );
            foreach ( self::extracted( $code ) as $name => $m )
            {
                if ( !preg_match( '/^        foreach \( array\(([^)]*)\) as \$__name \)\n            \$\{\$__name\} = &\$GLOBALS\[\$__name\];/', $m['body'], $b ) )
                    continue;
                foreach ( explode( ',', $b[1] ) as $s )
                    $this->assertContains( trim( $s, " '" ), $runNames, "$file $name() binds only what run() binds" );
            }
        }
    }

    /** ER-05 */
    public function testThePattern()
    {
        $r = new ezpTestExtractedRun();
        $this->assertSame( 'end', $r->run( 'go on' ) );
        $this->assertSame( array( 'count 2 created yes' ), $r->log, 'changed and created through the references' );
        $this->assertSame( 'early', $r->run( 'early' ) );
        $this->assertNull( $r->run( 'null' ), 'a "return null" of the range is still a return from run()' );
        $this->assertCount( 1, $r->log, 'nothing after an early return ran' );
    }
}
