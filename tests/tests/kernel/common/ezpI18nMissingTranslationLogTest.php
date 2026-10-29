<?php
/**
 * ezpI18n's "Missing translation" debug line (no database; a debug instance of its own that keeps its messages):
 *   - with the eng-US locale a text its file does not reword is shown as written and nothing is logged (the source
 *     texts are American English; the line filled debug.log once per text per page)
 *   - with another locale (ger-DE) a missing text is still logged, as before
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class ezpI18nMissingTranslationLogTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZLocaleStringDefault', 'eZDebugLogFileEnabled' );

    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( self::GLOBAL_KEYS as $key )
            $this->saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $debug = new eZDebug();
        $debug->ShowTypes = eZDebug::SHOW_ALL;
        $GLOBALS['eZDebugGlobalInstance'] = $debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;
        // no log file is written by this test
        eZDebug::setLogFileEnabled( false );
    }

    protected function tearDown(): void
    {
        foreach ( $this->saved as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
    }

    private function missingLines()
    {
        $lines = array();
        foreach ( eZDebug::instance()->DebugStrings as $entry )
            if ( strpos( (string)$entry['String'], 'Missing translation' ) !== false )
                $lines[] = $entry['String'];
        return $lines;
    }

    private function translateIn( $locale, $source )
    {
        $GLOBALS['eZLocaleStringDefault'] = $locale;
        $method = new ReflectionMethod( 'ezpI18n', 'translateText' );
        return $method->invoke( null, 'tests/w555', $source, null, array( '%n' => 3 ) );
    }

    public function testEngUSShowsTheSourceWithoutALogLine()
    {
        $this->assertSame( 'A text no file has 3', $this->translateIn( 'eng-US', 'A text no file has %n' ) );
        $this->assertSame( array(), $this->missingLines() );
    }

    public function testAnotherLocaleStillLogsAMissingText()
    {
        $this->assertSame( 'Another text no file has 3', $this->translateIn( 'ger-DE', 'Another text no file has %n' ) );
        $this->assertCount( 1, $this->missingLines() );
        $this->assertStringContainsString( "context: 'tests/w555'", $this->missingLines()[0] );
    }
}
