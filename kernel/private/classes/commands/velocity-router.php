<?php
/**
 * The code of bin/php/velocity-router.php, moved into a class (#207). The file bin/php/velocity-router.php calls it.
 * @description Router of PHP's built-in web server, as the php engine of exp:velocity runs it
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 * Not a Runnable: the built-in server runs the router before any autoloader exists, for every request, and needs
 * the front controller required at the router file's own top level (the kernel's globals live there). So route()
 * only decides, and the entry point acts on its answer:
 *   true    the request is answered (health check, refused file)
 *   false   the server sends the file itself
 *   string  the front controller to run; $_SERVER is already set up for it
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */
/*
 * The original header of bin/php/velocity-router.php:
 *
 * Router for PHP's built-in web server, as the php engine of exp:velocity
 * runs it (php -S host:port -t <root> bin/php/velocity-router.php).
 *
 * The built-in server reads no .htaccess, so this does what .htaccess_root
 * does, in the same order: the paths expVelocity::STATIC_PATHS lists are
 * served as files, api/ goes to index_rest.php, the tree menu to
 * index_treemenu.php, and everything else to index.php. Nothing outside that
 * list is ever handed out as a file -- the server's own default would serve
 * settings/site.ini, the SQLite database and every kernel source, and run any
 * .php file it finds.
 *
 * Velocity passes the root and the list of static paths in the environment
 * (EXP_VELOCITY_ROOT, EXP_VELOCITY_STATIC_PATHS), so the list is kept in one
 * place for both engines that route requests themselves, and
 * EXP_VELOCITY_NEVER_STATIC (expVelocity::NEVER_STATIC) and
 * EXP_VELOCITY_FOLLOW_SYMLINKS ([ServerSettings] FollowSymlinks).
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

namespace Exponential\Command\Kernel;

class VelocityRouter
{
    /**
     * Decides what the built-in server does with the current request.
     *
     * @param string $defaultRoot the installation root, used when EXP_VELOCITY_ROOT is not set
     * @return bool|string true: answered; false: the server sends the file; string: the front controller to require
     */
    public static function route( $defaultRoot )
    {
        $velocityRoot = getenv( 'EXP_VELOCITY_ROOT' );
        if ( !is_string( $velocityRoot ) || $velocityRoot === '' )
            $velocityRoot = $defaultRoot;
        $velocityStatic = getenv( 'EXP_VELOCITY_STATIC_PATHS' );
        $velocityFollow = getenv( 'EXP_VELOCITY_FOLLOW_SYMLINKS' ) === '1';

        $velocityPath = rawurldecode( (string)parse_url( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH ) );

        // The same health check the other engines answer, for exp:velocity and load
        // balancers.
        if ( $velocityPath === '/Q/health' )
        {
            http_response_code( 200 );
            header( 'Content-Type: text/plain' );
            return true;
        }

        // A file from the list: let the server send it. Judged on the path as it
        // resolves, not as it was written: /design/standard/stylesheets/../../../
        // settings/site.ini starts like a stylesheet, and the server itself resolves
        // the dots and sends the file. Never a script or a dot path (the built-in
        // server runs any .php file it is handed, wherever it lies): those go to
        // index.php like every other request, as the frankenphp engine sends them.
        $velocityNever = getenv( 'EXP_VELOCITY_NEVER_STATIC' );
        if ( is_string( $velocityStatic ) && $velocityStatic !== ''
             && preg_match( '~' . $velocityStatic . '~', $velocityPath )
             && !( is_string( $velocityNever ) && $velocityNever !== '' && preg_match( '~' . $velocityNever . '~', $velocityPath ) ) )
        {
            $velocityRealRoot = realpath( $velocityRoot );
            $velocityReal = realpath( $velocityRoot . $velocityPath );
            $velocityRelative = ( $velocityReal !== false && $velocityRealRoot !== false
                                  && strpos( $velocityReal, $velocityRealRoot . '/' ) === 0 )
                              ? substr( $velocityReal, strlen( $velocityRealRoot ) ) : false;

            // Links followed on purpose: judged on the path as written instead, as
            // long as it has no dot segments -- those would climb out without any
            // link, and the check above is the only one that catches them then.
            if ( $velocityRelative === false && $velocityFollow && $velocityReal !== false
                 && !preg_match( '~(^|/)\.\.?(/|$)~', $velocityPath ) )
                $velocityRelative = $velocityPath;

            if ( $velocityRelative === false || !is_file( $velocityReal )
                 || !preg_match( '~' . $velocityStatic . '~', $velocityRelative )
                 || preg_match( '/\.(php\d?|phtml|phar)$/i', $velocityRelative )
                 || ( is_string( $velocityNever ) && $velocityNever !== '' && preg_match( '~' . $velocityNever . '~', $velocityRelative ) ) )
            {
                http_response_code( 404 );
                return true;
            }
            return false;
        }

        if ( preg_match( '~^/(api/|index_rest\.php)~', $velocityPath ) )
            $velocityScript = 'index_rest.php';
        elseif ( preg_match( '~^/([^/]+/)?content/treemenu~', $velocityPath ) )
            $velocityScript = 'index_treemenu.php';
        else
            $velocityScript = 'index.php';

        // As a web server that rewrote the request to the front controller would set
        // them: the kernel reads SCRIPT_NAME to tell its own path from the URL's.
        $_SERVER['SCRIPT_NAME'] = '/' . $velocityScript;
        $_SERVER['PHP_SELF'] = '/' . $velocityScript;
        $_SERVER['SCRIPT_FILENAME'] = $velocityRoot . '/' . $velocityScript;

        chdir( $velocityRoot );
        return $velocityRoot . '/' . $velocityScript;
    }
}
