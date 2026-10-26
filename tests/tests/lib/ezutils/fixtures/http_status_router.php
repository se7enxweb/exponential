<?php
/**
 * Router for PHP's built-in server in eZHTTPToolGetDataByURLTest: /ok answers 200 with a body, /missing 404 and
 * /broken 500, both with an error page as the body, /moved redirects to /ok, /moved-missing to /missing and
 * /moved-file to file:///etc/hosts. Listens on 127.0.0.1 only; nothing is read or written on disk.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
switch ( $path )
{
    case '/ok':
        echo 'the feed';
        break;
    case '/missing':
        http_response_code( 404 );
        echo 'error page 404';
        break;
    case '/broken':
        http_response_code( 500 );
        echo 'error page 500';
        break;
    case '/moved':
        header( 'Location: /ok', true, 302 );
        break;
    case '/moved-missing':
        header( 'Location: /missing', true, 302 );
        break;
    case '/moved-file':
        header( 'Location: file:///etc/hosts', true, 302 );
        break;
    default:
        http_response_code( 404 );
        echo 'unknown';
}
return true;
