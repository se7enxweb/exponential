<?php
/**
 * File containing shared stub classes for security hardening tests.
 *
 * These minimal stubs allow kernel/classes security-patched files to be
 * loaded in isolation without bootstrapping the full eZ Publish application
 * stack.  They are intentionally incomplete — only the methods exercised by
 * the security test suite are implemented.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group security
 */
if ( !class_exists( 'eZPersistentObject', false ) )
{
    class eZPersistentObject
    {
        public function __construct( $row = [] ) {}
        public function store() {}
        public function attribute( $name ) { return $this->_data[$name] ?? null; }
        protected $_data = [];
        public static function fetchObjectList( $def, $fields = null, $conds = null,
                                                $sorts = null, $limit = null,
                                                $asObjects = true, $grouping = null,
                                                $customFields = null ) { return []; }
    }
}

if ( !class_exists( 'eZDB', false ) )
{
    class eZDB
    {
        private static $_inst;
        public static function instance()
        {
            if ( !self::$_inst ) self::$_inst = new self();
            return self::$_inst;
        }
        /** Minimal escapeString matching production semantics (addslashes-level). */
        public function escapeString( $str ) { return addslashes( (string)$str ); }
        public function query( $sql ) { return true; }
        public function arrayQuery( $sql, $params = [] ) { return []; }
        public function begin() {}
        public function commit() {}
        public function rollback() {}
    }
}

if ( !class_exists( 'eZDebug', false ) )
{
    class eZDebug
    {
        public static $lastWarning  = null;
        public static $lastError    = null;
        public static function writeWarning( $msg, $ctx = '' ) { self::$lastWarning = $msg; }
        public static function writeError( $msg, $ctx = '' )   { self::$lastError   = $msg; }
        public static function reset() { self::$lastWarning = self::$lastError = null; }
    }
}

if ( !class_exists( 'eZContentObjectTreeNode', false ) )
{
    class eZContentObjectTreeNode extends eZPersistentObject
    {
        public function attribute( $name )
        {
            return $this->_data[$name] ?? null;
        }
    }
}

if ( !class_exists( 'eZAudit', false ) )
{
    class eZAudit
    {
        public static function isAuditEnabled() { return false; }
    }
}

if ( !class_exists( 'eZRole', false ) )
{
    // Minimal role for referencing constants — real class loaded by the test
}
