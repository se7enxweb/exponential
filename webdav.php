<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*
 * DEPRECATED: the WebDAV entry script is index_webdav.php. This file only forwards to it, so that virtual
 * hosts and rewrite rules written for webdav.php keep working until they are changed, and it will be removed
 * in a later major version. A request through it writes one warning a day to warning.log.
 * See doc/eng-us/bc/6.0/webdav-entry-script-index-webdav.md.
 */
define( 'EXP_WEBDAV_ENTRY_SCRIPT', 'webdav.php' );

require __DIR__ . '/index_webdav.php';
