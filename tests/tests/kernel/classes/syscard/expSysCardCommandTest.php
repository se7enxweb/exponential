<?php
/**
 * The command of the system card and its names: exp:expfetch, with the aliases exp:info and exp:about that show more,
 * and the naming rule that keeps "fetch" out of everything but the command's name. No database.
 *
 *  SCC-01 - The alias set: the entry point and the command class declare exp:info and exp:about, the console reads both
 *  SCC-02 - The card follows the name the console was called by (EXP_CONSOLE_COMMAND), and --mode wins
 *  SCC-03 - The console passes the name it was called by to the command it runs
 *  SCC-04 - --help documents every option with examples; the shell completion knows the three commands and the options
 *  SCC-05 - The naming rule: "fetch" appears in the new code, settings, logos and tests only as the command's name
 *  SCC-06 - --list-providers shows every provider of syscard.ini
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expSysCardCommandTest extends PHPUnit\Framework\TestCase
{
    private function root()
    {
        return dirname( __DIR__, 5 );
    }

    /**
     * The aliases of a file as bin/php/console reads them: "@alias" tags of the first docblock.
     */
    private function aliases( $file )
    {
        $aliases = array();
        if ( preg_match( '#/\*\*(.*?)\*/#s', (string)file_get_contents( $file ), $block ) && preg_match_all( '/\*\s*@alias\s+(.+)$/m', $block[1], $m ) )
            foreach ( $m[1] as $line )
                foreach ( preg_split( '/[\s,]+/', trim( $line ), -1, PREG_SPLIT_NO_EMPTY ) as $alias )
                    $aliases[] = strpos( $alias, ':' ) === false ? 'exp:' . $alias : $alias;
        return $aliases;
    }

    public function testAliasSet()
    {
        $this->assertSame( array( 'exp:expfetch' => 'card', 'exp:info' => 'info', 'exp:about' => 'about' ), \Exponential\Command\Kernel\Syscard::NAMES );
        $this->assertFileExists( $this->root() . '/bin/php/expfetch.php', 'bin/php/expfetch.php is exp:expfetch' );
        $this->assertSame( array( 'exp:info', 'exp:about' ), $this->aliases( $this->root() . '/bin/php/expfetch.php' ) );
        $this->assertSame( array( 'exp:info', 'exp:about' ), $this->aliases( $this->root() . '/kernel/private/classes/commands/syscard.php' ) );
        foreach ( array( 'info', 'about' ) as $name )
            $this->assertFileDoesNotExist( $this->root() . '/bin/php/' . $name . '.php', "a real exp:$name would shadow the alias" );
        $entry = (string)file_get_contents( $this->root() . '/bin/php/expfetch.php' );
        $this->assertStringContainsString( 'The code is in kernel/private/classes/commands/syscard.php (#207)', $entry );
        $this->assertStringContainsString( '\\Exponential\\Command\\Kernel\\Syscard::main( __FILE__ );', $entry );
        $this->assertMatchesRegularExpression( '/^ \* @description .+/m', $entry );
    }

    public function testModeFollowsTheName()
    {
        $class = '\\Exponential\\Command\\Kernel\\Syscard';
        $this->assertSame( 'card', $class::modeFor( null, 'exp:expfetch' ) );
        $this->assertSame( 'info', $class::modeFor( null, 'exp:info' ) );
        $this->assertSame( 'about', $class::modeFor( null, 'EXP:ABOUT' ) );
        $this->assertSame( 'card', $class::modeFor( null, false ), 'run as php bin/php/expfetch.php' );
        $this->assertSame( 'card', $class::modeFor( null, 'exp:something' ) );
        $this->assertSame( 'about', $class::modeFor( 'about', 'exp:info' ), '--mode wins' );
        $this->assertNull( $class::modeFor( 'everything', 'exp:info' ) );
    }

    public function testConsolePassesTheNameItWasCalledBy()
    {
        $console = (string)file_get_contents( $this->root() . '/bin/php/console' );
        $this->assertStringContainsString( "putenv( 'EXP_CONSOLE_COMMAND=' . ( \$invokedAs !== null ? \$invokedAs : \$cmd['name'] ) );", $console );
        $this->assertStringContainsString( '$runCommand( $allCommands[ $aliasMap[ $commandInput ] ], $forwardArgs, $commandInput );', $console );
        $this->assertStringContainsString( '$runCommand( $allCommands[ $aliasMap[ $candidate ] ], $forwardArgs, $candidate );', $console );
        $this->assertSame( 'EXP_CONSOLE_COMMAND', \Exponential\Command\Kernel\Syscard::INVOKED_AS );
    }

    public function testHelpAndCompletion()
    {
        $code = (string)file_get_contents( $this->root() . '/kernel/private/classes/commands/syscard.php' );
        preg_match( "/startup\\( '([^']+)'/", $code, $m );
        $this->assertNotEmpty( $m );
        preg_match_all( '/\[([a-z-]+)[:;]?\]/', $m[1], $declared );
        $this->assertSame( array_keys( \Exponential\Command\Kernel\Syscard::OPTIONS ), $declared[1], 'every option has a help text, in the same order' );
        foreach ( array( './console exp:expfetch', './console exp:info', './console exp:about', '--json', '--plain', '--watch', '--theme=light', '--completion=bash' ) as $example )
            $this->assertStringContainsString( $example, $code, "the --help text shows $example" );

        $bash = \Exponential\Command\Kernel\Syscard::completion( 'bash', array( 'rainbow', 'light' ) );
        $this->assertStringContainsString( 'exp:expfetch|exp:info|exp:about)', $bash );
        $this->assertStringContainsString( '--no-logo', $bash );
        $this->assertStringContainsString( '--theme=', $bash );
        $this->assertStringContainsString( '"rainbow light"', $bash );
        $this->assertStringContainsString( 'complete -o default -F _exp_syscard_complete console ./console bin/php/console', $bash );
        $this->assertStringContainsString( 'bashcompinit', \Exponential\Command\Kernel\Syscard::completion( 'zsh', array() ) );
        $this->assertNull( \Exponential\Command\Kernel\Syscard::completion( 'fish', array() ) );
    }

    public function testProviderTable()
    {
        $registry = new expSysCardRegistry( array( 'ProviderClasses' => array( 'expSysCardHostProvider', 'expSysCardAboutProvider', 'noSuchProvider' ),
                                                   'Enabled' => array( 'about' => 'disabled' ) ) );
        $table = \Exponential\Command\Kernel\Syscard::providerTable( $registry );
        $this->assertMatchesRegularExpression( '/^host\s+expSysCardHostProvider\s+card\s+90\s+1 s\s+on$/m', $table );
        $this->assertMatchesRegularExpression( '/^about\s+expSysCardAboutProvider\s+about\s+95\s+0.5 s\s+off$/m', $table );
        $this->assertMatchesRegularExpression( '/^\?\s+noSuchProvider\s+missing$/m', $table );
    }

    /**
     * The files of the system card: "fetch" is the name of template fetch functions, module fetch definitions,
     * function collections and the fetch methods of persistent objects, so the card's own code, settings and
     * drawings never use the word, except in the command's name (exp:expfetch, bin/php/expfetch.php).
     */
    public function testNoFetchOutsideTheCommandName()
    {
        $root = $this->root();
        $files = array( 'kernel/private/classes/commands/syscard.php', 'settings/syscard.ini', 'bin/php/expfetch.php' );
        foreach ( array( 'kernel/classes/syscard', 'share/syscard', 'tests/tests/kernel/classes/syscard' ) as $dir )
        {
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $iterator as $file )
                if ( $file->isFile() )
                    $files[] = substr( $file->getPathname(), strlen( $root ) + 1 );
        }
        sort( $files );
        $this->assertGreaterThan( 25, count( $files ) );
        $found = array();
        foreach ( $files as $file )
        {
            $name = str_replace( 'expfetch', '', basename( $file ) );
            if ( stripos( $name, 'fetch' ) !== false )
                $found[] = $file . ' (file name)';
            // this test names the word to state the rule
            if ( $file === 'tests/tests/kernel/classes/syscard/expSysCardCommandTest.php' )
                continue;
            $text = (string)file_get_contents( $root . '/' . $file );
            foreach ( preg_split( '/\R/', $text ) as $n => $line )
            {
                $line = str_ireplace( array( 'exp:expfetch', 'expfetch.php', 'expfetch' ), '', $line );
                if ( stripos( $line, 'fetch' ) !== false )
                    $found[] = $file . ':' . ( $n + 1 ) . ': ' . trim( $line );
            }
        }
        $this->assertSame( array(), $found, 'use "system card" (expSysCard*), never "fetch", outside the command name' );
    }
}
