<?php
/**
 * The spell checker's getRequestParam() reads json_data through formatParam(),
 * which was never defined, so a spell check posted as json_data stopped PHP
 * with "Call to undefined function formatParam()".
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../../../../kernel/private/classes/runnable/runnable.php';
require_once __DIR__ . '/../../../../kernel/private/classes/runnable/moduleview.php';
require_once __DIR__ . '/../../../../extension/ezoe/classes/runnable/views/ezoe/spellcheck_rpc.php';

class ezoeSpellcheckRpcRequestParamTest extends PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        unset( $_REQUEST['json_data'], $_REQUEST['lang'] );
    }

    public function testRequestParamIsReturned()
    {
        $_REQUEST['json_data'] = '{"method":"checkWords"}';
        $this->assertSame( '{"method":"checkWords"}', getRequestParam( 'json_data' ) );
        $this->assertSame( 'x', getRequestParam( 'missing', 'x' ) );
    }

    public function testSanitizedRequestParam()
    {
        $_REQUEST['lang'] = 'en<script>';
        $this->assertSame( 'enscript', getRequestParam( 'lang', false, true ) );
    }
}
