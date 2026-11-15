<?php
/**
 * rss/feed of a feed that does not exist (removed, or a crawler's guess) answers 404 and writes no error.log line:
 * the view notes it as a notice. No database.
 *
 *  RF-01  neither the missing feed nor a missing feed name is written with eZDebug::writeError(); a feed that
 *         exists but cannot be produced still is
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expRssFeedMissingNotAnErrorTest extends PHPUnit\Framework\TestCase
{
    /** RF-01 */
    public function testAMissingFeedIsANoticeNotAnError()
    {
        $code = (string)file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/views/rss/feed.php' );
        $this->assertStringContainsString( "\\eZDebug::writeNotice( 'Could not find RSSExport : ' . \$Params['RSSFeed'] );", $code );
        $this->assertStringContainsString( "\\eZDebug::writeNotice( 'No RSS feed specified' );", $code );
        $this->assertStringNotContainsString( "writeError( 'Could not find RSSExport", $code );
        $this->assertStringNotContainsString( "writeError( 'No RSS feed specified'", $code );
        $this->assertStringContainsString( "writeError( 'Nothing to serve for RSS feed '", $code, 'a feed that exists but cannot be made stays an error' );
        $this->assertStringContainsString( 'KERNEL_NOT_AVAILABLE', $code, 'still the not-found answer' );
    }
}
