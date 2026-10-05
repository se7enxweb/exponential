<?php
/**
 * File containing shared stub classes for security hardening tests.
 *
 * These stubs allow the security tests to run without bootstrapping the full
 * eZ Publish kernel.  Only the classes/methods the security suite actually
 * exercises are stubbed; the rest of the eZ stack is loaded from the real
 * source files so that later test suites are not shadowed by incomplete stubs.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group security
 */

// Load the real eZDBInterface so StubEZDB is a genuine eZDBInterface instance.
// This allows the real eZDB class to be loaded alongside the security stubs.
if ( !class_exists( 'eZDBInterface', false ) )
{
    class_exists( 'eZDBInterface', true );
}

// The real eZDebug, not a stub: a class declared here stays for the whole PHPUnit process, and the security
// suite runs first, so a stub shadowed eZDebug for every later suite. Without debug enabled the real class only
// writes errors to the log, which is what the tested code does on a site.
if ( !class_exists( 'eZDebug', false ) )
{
    require_once __DIR__ . '/../../../../../lib/ezutils/classes/ezdebug.php';
}

if ( !class_exists( 'StubEZDB', false ) )
{
    class StubEZDB extends eZDBInterface
    {
        public bool $connected;
        /** @var array|false */
        public $queryResult;
        /** @var string[] recorded SELECT queries */
        public array $selectQueries = [];
        /** @var string[] recorded DELETE / other queries */
        public array $deleteQueries = [];

        public function __construct( $parameters = [] )
        {
            if ( is_array( $parameters ) )
            {
                $this->connected   = $parameters['connected']   ?? true;
                $this->queryResult = $parameters['queryResult'] ?? false;
            }
            else
            {
                $this->connected   = (bool) $parameters;
                $this->queryResult = false;
            }
        }

        public function isConnected() { return $this->connected; }

        public function escapeString( $s ) { return addslashes( $s ); }

        public function arrayQuery( $sql, $params = [], $server = false )
        {
            $this->selectQueries[] = $sql;
            return $this->queryResult;
        }

        public function query( $sql, $server = false )
        {
            $this->deleteQueries[] = $sql;
            return true;
        }
    }
}
