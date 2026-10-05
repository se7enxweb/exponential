<?php
/**
 * eZCLI (lib/ezutils), the console helper of every bin/php script, without a terminal:
 *   - parseOptionString(): short and long options, values (":"), optional values (";"), quantifiers (? * +),
 *     aliases in brackets ([h|help]), the store name of an alias group, an unterminated bracket
 *   - getOptions(): -o, -ovalue, -o value, --opt, --opt=value, repeated options collected in a list, plain
 *     arguments, options that are absent (null), an unknown option and a missing value (false)
 *   - styles: terminal and web styles by name, an unknown name, stylize() with styles on and off, gotoColumn(),
 *     the position sequences, quiet output, the end of line string, the shared instance
 *
 * Error messages of getOptions() go to STDERR; nothing else is written.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZCLITest extends PHPUnit\Framework\TestCase
{
    const OPTIONS = '[h|help][q|quiet][s:|siteaccess:][l;|level;][v*][f:+|file:+][dry-run]';

    public function testParseOptionString()
    {
        $config = null;
        eZCLI::parseOptionString( self::OPTIONS, $config );
        $this->assertSame( array( 'h', 'q', 's', 'l', 'v', 'f' ), array_keys( $config['short'] ) );
        $this->assertSame( array( 'help', 'quiet', 'siteaccess', 'level', 'file', 'dry-run' ), array_keys( $config['long'] ) );
        $this->assertCount( 12, $config['list'] );

        $this->assertFalse( $config['short']['h']['has-value'] );
        $this->assertSame( 'help', $config['short']['h']['store-name'], 'an alias group is stored under its last name' );
        $this->assertTrue( $config['short']['s']['has-value'] );
        $this->assertSame( 'optional', $config['long']['level']['has-value'] );
        $this->assertSame( array( 'min' => 0, 'max' => false ), $config['short']['v']['quantifier'] );
        $this->assertSame( array( 'min' => 1, 'max' => false ), $config['long']['file']['quantifier'] );
        $this->assertTrue( $config['long']['dry-run']['is-long-option'] );
        $this->assertFalse( $config['short']['q']['is-long-option'] );
    }

    public function testParseOptionStringOutsideBrackets()
    {
        $config = null;
        eZCLI::parseOptionString( 'ab:c;?d*', $config );
        $this->assertSame( array( 'a', 'b', 'c', 'd' ), array_keys( $config['short'] ) );
        $this->assertTrue( $config['short']['b']['has-value'] );
        $this->assertSame( 'optional', $config['short']['c']['has-value'] );
        $this->assertSame( array( 'min' => 0, 'max' => 1 ), $config['short']['c']['quantifier'] );
        $this->assertSame( array( 'min' => 0, 'max' => false ), $config['short']['d']['quantifier'] );
    }

    public function testParseOptionStringAddsToAnExistingConfig()
    {
        $config = null;
        eZCLI::parseOptionString( '[a]', $config );
        eZCLI::parseOptionString( '[bee]', $config );
        $this->assertArrayHasKey( 'a', $config['short'] );
        $this->assertArrayHasKey( 'bee', $config['long'] );
    }

    public function testUnterminatedBracketStopsParsing()
    {
        $config = null;
        @eZCLI::parseOptionString( '[a][b', $config );
        $this->assertSame( array( 'a' ), array_keys( $config['short'] ) );
    }

    private function options( $arguments )
    {
        return ( new eZCLI() )->getOptions( self::OPTIONS, '', $arguments );
    }

    public function testGetOptions()
    {
        $options = $this->options( array( '-q', '--siteaccess=admin', 'first', '-v', '-v', '--file=a.txt', '-fb.txt', '-f', 'c.txt', 'second', '--dry-run' ) );
        $this->assertTrue( $options['quiet'] );
        $this->assertSame( 'admin', $options['siteaccess'] );
        $this->assertSame( array( true, true ), $options['v'] );
        $this->assertSame( array( 'a.txt', 'b.txt', 'c.txt' ), $options['file'] );
        $this->assertTrue( $options['dry-run'] );
        $this->assertSame( array( 'first', 'second' ), $options['arguments'] );
        $this->assertNull( $options['help'], 'absent options are null' );
        $this->assertNull( $options['level'] );
    }

    public function testShortOptionValues()
    {
        $this->assertSame( 'admin', $this->options( array( '-sadmin' ) )['siteaccess'] );
        $this->assertSame( 'admin', $this->options( array( '-s', 'admin' ) )['siteaccess'] );
        $this->assertTrue( $this->options( array( '-l' ) )['level'], 'an optional value may be left out' );
        $this->assertSame( '3', $this->options( array( '-l3' ) )['level'] );
        $this->assertSame( '3', $this->options( array( '--level=3' ) )['level'] );
        $this->assertSame( array( 'x' ), $this->options( array( '-l', 'x' ) )['arguments'], 'an optional value is not taken from the next argument' );
    }

    public function testSingleDashIsAnArgument()
    {
        $this->assertSame( array( '-' ), $this->options( array( '-' ) )['arguments'] );
    }

    public function testUnknownOptionAndMissingValueFail()
    {
        $this->assertFalse( @$this->options( array( '--no-such-option' ) ) );
        $this->assertFalse( @$this->options( array( '-x' ) ) );
        $this->assertFalse( @$this->options( array( '-s' ) ), 'no value after the last argument' );
        $this->assertFalse( @$this->options( array( '--siteaccess' ) ) );
    }

    public function testStyles()
    {
        $cli = new eZCLI();
        $this->assertSame( "\033[1;31m", $cli->terminalStyle( 'error' ) );
        $this->assertFalse( $cli->terminalStyle( 'no-such-style' ) );
        $this->assertSame( '<strong>', $cli->webStyle( 'strong' ) );
        $this->assertFalse( $cli->webStyle( 'no-such-style' ), 'as terminalStyle()' );
        $this->assertFalse( $cli->webStyle( 'red' ), 'a terminal colour has no web style' );
        $this->assertArrayHasKey( 'warning-end', $cli->terminalStyles() );
        $this->assertArrayHasKey( 'paragraph', $cli->webStyles() );
        $this->assertFalse( $cli->emptyStyles()['error'] );

        $this->assertFalse( $cli->useStyles() );
        $this->assertFalse( $cli->style( 'error' ) );
        $this->assertSame( 'text', $cli->stylize( 'error', 'text' ) );
        $this->assertSame( "\t\t", $cli->gotoColumn( 10 ) );

        $cli->UseStyles = true;
        $this->assertSame( "\033[1;31mtext\033[0;39m", $cli->stylize( 'error', 'text' ) );
        $this->assertSame( "\033[10G", $cli->gotoColumn( 10 ) );
        $cli->WebOutput = true;
        $this->assertSame( '<strong>text</strong>', $cli->stylize( 'strong', 'text' ) );

        $this->assertSame( "\033[s", eZCLI::storePosition() );
        $this->assertSame( "\033[u", eZCLI::restorePosition() );
    }

    public function testOutputAndQuiet()
    {
        $cli = new eZCLI();
        $this->assertFalse( $cli->isWebOutput() );
        $this->assertSame( "\n", $cli->endlineString() );
        $this->assertTrue( $cli->isLoud() );

        ob_start();
        $cli->output( 'line' );
        $cli->output( 'same', false );
        $cli->setIsQuiet( true );
        $cli->output( 'hidden' );
        $out = ob_get_clean();
        $this->assertSame( "line\nsame", $out );
        $this->assertTrue( $cli->isQuiet() );
        $this->assertFalse( $cli->isLoud() );
    }

    public function testSharedInstance()
    {
        $saved = $GLOBALS['eZCLIInstance'] ?? null;
        unset( $GLOBALS['eZCLIInstance'] );
        $this->assertFalse( eZCLI::hasInstance() );
        $cli = eZCLI::instance();
        $this->assertTrue( eZCLI::hasInstance() );
        $this->assertSame( $cli, eZCLI::instance() );
        if ( $saved !== null )
            $GLOBALS['eZCLIInstance'] = $saved;
        else
            unset( $GLOBALS['eZCLIInstance'] );
    }
}
