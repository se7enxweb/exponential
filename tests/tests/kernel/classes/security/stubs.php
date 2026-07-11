<?php
/**
 * File containing shared stub classes for security hardening tests.
 *
 * These stubs allow the security tests to run without bootstrapping the full
 * eZ Publish kernel.  Only the classes/methods the security suite actually
 * exercises are stubbed; the rest of the eZ stack is loaded from the real
 * source files so that later test suites are not shadowed by incomplete stubs.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
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

if ( !class_exists( 'eZDebug', false ) )
{
    class eZDebug
    {
        const LEVEL_NOTICE = 1;
        const LEVEL_WARNING = 2;
        const LEVEL_ERROR = 3;
        const LEVEL_TIMING_POINT = 4;
        const LEVEL_DEBUG = 5;
        const LEVEL_STRICT = 6;

        const SHOW_NOTICE = 1;
        const SHOW_WARNING = 2;
        const SHOW_ERROR = 4;
        const SHOW_TIMING_POINT = 8;
        const SHOW_DEBUG = 16;
        const SHOW_STRICT = 32;
        const SHOW_ALL = 63;

        const HANDLE_NONE = 0;
        const HANDLE_FROM_PHP = 1;
        const HANDLE_TO_PHP = 2;
        const HANDLE_EXCEPTION = 3;

        const OUTPUT_MESSAGE_SCREEN = 1;
        const OUTPUT_MESSAGE_FILE = 2;
        const OUTPUT_MESSAGE_LOG = 4;

        public static $lastWarning  = null;
        public static $lastError    = null;
        private static $instance     = null;

        public static function instance()
        {
            if ( self::$instance === null )
                self::$instance = new self();
            return self::$instance;
        }

        public function messageName( $messageType ) { return $messageType; }

        public static function isDebugEnabled() { return false; }

        public static function writeWarning( $msg, $ctx = '' ) { self::$lastWarning = $msg; }
        public static function writeError( $msg, $ctx = '' )   { self::$lastError   = $msg; }
        public static function writeNotice( $msg, $ctx = '' )  {}
        public static function writeStrict( $msg, $ctx = '' )  {}
        public static function setHandleType( $type )           {}
        public static function accumulatorStart( $key, $inGroup = false, $name = false, $recursive = false ) {}
        public static function accumulatorStop( $key, $recursive = false ) {}
        public static function reset() { self::$lastWarning = self::$lastError = null; }

        public function __call( $name, $arguments ) { return null; }
        public static function __callStatic( $name, $arguments ) { return null; }
    }
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
