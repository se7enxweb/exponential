<?php
/**
 * Stubs for eZSessionHandlerDB unit tests.
 *
 * These are loaded only inside the test process via
 * EzpSessionHandlerDBPhp8BugfixesTest::setUpBeforeClass(), so they do not leak
 * into the main PHPUnit runner process and do not shadow the real eZ kernel
 * classes for other test suites.
 */

// Loading eZDBInterface will autoload the real eZDB class first, because the
// eZDBInterface class uses eZDB::ERROR_HANDLING_STANDARD as a property default.
if ( !class_exists( 'eZDBInterface', false ) )
    class_exists( 'eZDBInterface', true );

/**
 * Stub eZDB instance returned by eZDB::instance().
 * Tests configure $connected, $queryResult, and record executed queries.
 */
class StubSessionEZDB extends eZDBInterface
{
    public bool $connected;
    /** @var array|false */
    public $queryResult;
    /** @var string[] recorded SELECT queries */
    public array $selectQueries = [];
    /** @var string[] recorded DELETE / other queries */
    public array $deleteQueries = [];

    public function __construct( bool $connected = true, $queryResult = false )
    {
        $this->connected = $connected;
        $this->queryResult = $queryResult;
    }

    public function isConnected() { return $this->connected; }

    public function escapeString( $s ) { return addslashes( $s ); }

    /** @return array|false */
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

if ( !class_exists( 'eZINI', false ) )
{
    /** Minimal eZINI stub. */
    class eZINI
    {
        public static function instance(): self { return new self(); }

        public function variable( string $section, string $key ): mixed
        {
            return 3600;
        }

        public function setVariable( string $section, string $key, mixed $value ): void {}
    }
}

if ( !class_exists( 'eZSession', false ) )
{
    /** Minimal eZSession stub. */
    class eZSession
    {
        public static ?int $userId = null;

        public static function setUserID( int $id ): void { self::$userId = $id; }
        public static function userID(): int { return self::$userId ?? 0; }
        public static function triggerCallback( string $event, array $args = [] ): void {}
    }
}

if ( !class_exists( 'ezpEvent', false ) )
{
    /** Minimal ezpEvent stub. */
    class ezpEvent
    {
        public static self $instance;

        public static function getInstance(): self
        {
            if ( !isset( self::$instance ) )
                self::$instance = new self();
            return self::$instance;
        }

        public function notify( string $event, array $args = [] ): void {}
    }
}
