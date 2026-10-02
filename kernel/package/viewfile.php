<?php
/**
 * Entry point of kernel/package/viewfile.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * package/viewfile/<PackageName>/<FileIndex>: streams one raw file out of a package's own
 * directory - an image the contents browser shows inline (package/view/full.tpl's <img>), or
 * anything else offered as a plain download. <FileIndex> is the file's position in
 * eZPackageFileBrowser::allFiles()'s own sorted list, not its path (a relative path can carry
 * slashes of its own, which a bare URL segment cannot hold safely); eZPackageFileBrowser::
 * filePath() is the one gate that actually reads it off disk: no path traversal, no absolute
 * path, no following a symlink out of the package's own directory. Kernel-only, no dependency on
 * any extension.
 *
 * IMPORTANT: eZExecution::cleanExit() ends the request by throwing under some web server
 * integrations, so the send-and-exit below must never sit inside a try/catch(Exception) - that
 * would swallow the exit and the page gets appended to the file.
 */

// The code is in kernel/private/classes/views/package/viewfile.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Package\Viewfile::main( __FILE__, get_defined_vars() );
