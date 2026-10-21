<?php
/**
 * Every literal 'design:<path>.tpl' in the module view scripts and view classes of the kernel exists in one of the
 * designs an admin siteaccess falls back to: admin4, admin3, admin or standard. A name that resolves nowhere is a page
 * that renders a template error (kernel/class/delete.php, kernel/content/error.php and kernel/workflow/list.php
 * fetched templates no design had, and no module defined a view for them; copying several items at once named
 * content/browse_copy_subtrees.tpl, which no design had either).
 * Read: the module scripts kernel/<module>/<view>.php and, since those are mostly one call to a view class now,
 * kernel/private/classes/views/<module>/<view>.php and kernel/private/modules/<module>/<file>.php (one directory
 * level each). Not read: kernel/classes, kernel/common, the rest of kernel/private, and the extensions. Only strings
 * that are exactly 'design:<path>.tpl' count: names put together at run time (concat, a variable in the string) and
 * templates a wizard writes into a new extension (inside a larger string) are not checked.
 * No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminKernelDesignTemplatesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminKernelDesignTemplatesTest extends PHPUnit\Framework\TestCase
{
    /** The designs an admin siteaccess resolves a template through, most specific first */
    const DESIGNS = array( 'admin4', 'admin3', 'admin', 'standard' );

    /**
     * Templates that may resolve in none of DESIGNS, each with its reason: a template that is meant to come from the
     * site's own design (an e-mail, a notification or a shop page a site styles itself) and that no admin design
     * ships on purpose. 'kernel/<file>.php:<template path>' => reason. Empty: every name resolves today.
     */
    const EXCEPTIONS = array();

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /**
     * @return array of array( relative file, line, template path )
     */
    private static function references()
    {
        $root = self::root();
        $files = array_merge( AdminFiles::files( $root . '/kernel/*/*.php' ),
                              AdminFiles::files( $root . '/kernel/private/classes/views/*/*.php' ),
                              AdminFiles::files( $root . '/kernel/private/modules/*/*.php' ) );
        $references = array();
        foreach ( $files as $file )
        {
            foreach ( token_get_all( (string)file_get_contents( $file ) ) as $token )
            {
                if ( !is_array( $token ) || $token[0] !== T_CONSTANT_ENCAPSED_STRING )
                    continue;
                if ( preg_match( '#^(["\'])design:([^"\'$]+\.tpl)\1$#', $token[1], $match ) )
                    $references[] = array( substr( $file, strlen( $root ) + 1 ), $token[2], $match[2] );
            }
        }
        return $references;
    }

    private static function resolves( $template )
    {
        foreach ( self::DESIGNS as $design )
        {
            if ( is_file( self::root() . "/design/$design/templates/$template" ) )
                return true;
        }
        return false;
    }

    public function testEveryKernelTemplateNameResolves()
    {
        $references = self::references();
        $this->assertGreaterThan( 200, count( $references ), 'the scan found too few design: names to have read the kernel' );

        $missing = array();
        foreach ( $references as $reference )
        {
            list( $file, $line, $template ) = $reference;
            if ( !isset( self::EXCEPTIONS["$file:$template"] ) && !self::resolves( $template ) )
                $missing[] = "$file:$line design:$template";
        }
        $this->assertSame( array(), $missing, 'templates no admin design and no standard template has' );
    }

    public function testEveryExceptionIsStillNeeded()
    {
        $named = array();
        foreach ( self::references() as $reference )
            $named[$reference[0] . ':' . $reference[2]] = true;

        $stale = array();
        foreach ( array_keys( self::EXCEPTIONS ) as $key )
        {
            $template = substr( $key, strpos( $key, ':' ) + 1 );
            if ( !isset( $named[$key] ) || self::resolves( $template ) )
                $stale[] = $key;
        }
        $this->assertSame( array(), $stale, 'exceptions for names the kernel no longer uses or that resolve now' );
    }
}
