<?php
/**
 * eZGZIPCompressionHandler forwards the compression level to the handler it
 * wraps. It called $this->handler(), which does not exist (the accessor is
 * forwardHandler()), so setCompressionLevel() and compressionLevel() stopped
 * PHP with an Error.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$lib = __DIR__ . '/../../../../lib/ezfile/classes';
foreach ( array( 'ezfilehandler', 'ezcompressionhandler', 'ezforwardcompressionhandler', 'ezgzipzlibcompressionhandler',
                 'ezgzipshellcompressionhandler', 'eznocompressionhandler', 'ezgzipcompressionhandler' ) as $file )
    require_once $lib . '/' . $file . '.php';

class eZGZIPCompressionHandlerLevelTest extends PHPUnit\Framework\TestCase
{
    public function testCompressionLevelIsForwarded()
    {
        if ( !eZGZIPZLIBCompressionHandler::isAvailable() )
            $this->markTestSkipped( 'zlib is not available' );

        $handler = new eZGZIPCompressionHandler();
        $handler->setCompressionLevel( 7 );
        $this->assertSame( 7, $handler->compressionLevel() );
        $this->assertSame( 7, $handler->forwardHandler()->compressionLevel() );
    }
}
