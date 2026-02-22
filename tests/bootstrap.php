<?php
/**
 * PHPUnit 10 bootstrap for Exponential / eZ Publish Legacy 6.0.x.
 *
 * Loaded via the bootstrap="" attribute in phpunit.xml before any test file
 * is parsed.  Provides:
 *
 *  1. A PHP-namespace-to-underscore compat shim so the legacy toolkit classes
 *     (written for PHPUnit 3.7) can still extend PHPUnit_TextUI_Command,
 *     PHPUnit_Framework_TestRunner, etc. without a full rewrite.
 *
 *  2. require_once of every toolkit class so ezpTestCase, ezpDatabaseTestCase,
 *     ezpTestRunner and friends are autoloaded for every test file.
 *
 * IMPORTANT: Do NOT bootstrap the full eZ Publish kernel here.  Tests that
 * need it use ezpTestSuite / ezpDatabaseTestCase which do so via eZScript.
 *
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */

// ── 1. Composer autoloader ────────────────────────────────────────────────────
require_once __DIR__ . '/../vendor/autoload.php';

// ── 2. PHPUnit 3.7 → 10 compatibility shims ──────────────────────────────────
// These lightweight shim classes let the legacy toolkit load without a full
// rewrite.  They expose the $arguments / $longOptions array API that
// ezpTestRunner's constructor fills in, and delegate the actual test run to
// PHPUnit\TextUI\Application.

// PHPUnit_Framework_TestSuite — composition shim.
//
// PHPUnit 10 made TestSuite::__construct() "final private", so no subclass
// can define a constructor.  The legacy toolkit classes (ezpTestSuite,
// ezpTestRegressionSuite) extend this class and override __construct().
// We therefore cannot alias __construct directly to PHPUnit\Framework\TestSuite.
// Instead we provide a standalone shim class that:
//  - Has an overridable constructor (not final)
//  - Wraps a real PHPUnit\Framework\TestSuite via composition
//  - Implements PHPUnit\Framework\Test so PHPUnit can run it
//  - Delegates addTest / tests / run / count / getName to the inner suite
//
// Toolkit files KEEP their "extends PHPUnit_Framework_TestSuite" declarations.
if ( !class_exists( 'PHPUnit_Framework_TestSuite', false ) )
{
    class PHPUnit_Framework_TestSuite
        implements PHPUnit\Framework\Test, PHPUnit\Framework\SelfDescribing, Countable, IteratorAggregate
    {
        protected PHPUnit\Framework\TestSuite $inner;

        public function __construct( $theClass = '', string $name = '' )
        {
            if ( is_object( $theClass ) && $theClass instanceof ReflectionClass )
            {
                $suiteName = $name !== '' ? $name : $theClass->getName();
                $this->inner = PHPUnit\Framework\TestSuite::fromClassReflector( $theClass );
            }
            elseif ( is_string( $theClass ) && $theClass !== '' )
            {
                $suiteName = $name !== '' ? $name : $theClass;
                $this->inner = PHPUnit\Framework\TestSuite::empty( $suiteName );
            }
            else
            {
                $suiteName = $name;
                $this->inner = PHPUnit\Framework\TestSuite::empty( $suiteName );
            }
        }

        public function addTest( PHPUnit\Framework\Test $test, array $groups = [] ): void
        {
            $this->inner->addTest( $test, $groups );
        }

        public function addTestFile( string $filename ): void
        {
            $this->inner->addTestFile( $filename );
        }

        public function tests(): array { return $this->inner->tests(); }
        public function count(): int   { return $this->inner->count(); }
        public function getName(): string { return $this->inner->getName(); }
        public function toString(): string { return $this->inner->getName(); }
        public function run(): void    { $this->inner->run(); }
        public function getIterator(): Iterator { return $this->inner->getIterator(); }
    }
}

if ( !class_exists( 'PHPUnit_TextUI_Command', false ) )
{
    /**
     * Shim for PHPUnit_TextUI_Command (removed in PHPUnit 9).
     * Preserves the $arguments/$longOptions API used by ezpTestRunner.
     * Delegates test execution to PHPUnit\TextUI\Application (PHPUnit 10).
     */
    class PHPUnit_TextUI_Command
    {
        /** @var array<string,mixed> */
        public array $arguments = [];

        /** @var array<string,string> long-option → handler method name */
        public array $longOptions = [];

        /** Parse custom long-opts then hand off to PHPUnit\TextUI\Application. */
        public function run( array $argv, bool $exit = true ): int
        {
            $filtered = [ $argv[0] ?? 'phpunit' ];
            foreach ( array_slice( $argv, 1 ) as $arg )
            {
                foreach ( $this->longOptions as $opt => $handler )
                {
                    $key = rtrim( $opt, '=' );
                    if ( str_ends_with( $opt, '=' ) && str_starts_with( $arg, "--{$key}=" ) )
                    {
                        $value = substr( $arg, strlen( "--{$key}=" ) );
                        if ( method_exists( $this, $handler ) )
                            $this->$handler( $value );
                        continue 2;
                    }
                    if ( !str_ends_with( $opt, '=' ) && $arg === "--{$key}" )
                    {
                        if ( method_exists( $this, $handler ) )
                            $this->$handler();
                        continue 2;
                    }
                }
                $filtered[] = $arg;
            }

            $app = new PHPUnit\TextUI\Application();
            $app->run( $filtered );
            return 0;
        }
    }
}

if ( !class_exists( 'PHPUnit_TextUI_TestRunner', false ) )
{
    /** Minimal shim — phpunit 10 has no direct equivalent; unused in PHPUnit 10 path. */
    class PHPUnit_TextUI_TestRunner {}
}

if ( !class_exists( 'PHP_CodeCoverage_Filter', false ) )
{
    /** Minimal shim — code coverage filter API changed; stub prevents fatal. */
    class PHP_CodeCoverage_Filter
    {
        public function addFilesToWhitelist( array $files ): void {}
    }
}

// ── 3. Test toolkit classes (order matters) ───────────────────────────────────
$toolkit = __DIR__ . '/toolkit/';

$toolkitFiles = [
    'ezptestcase.php',
    'ezptestsuite.php',
    'ezptestregressionsuite.php',
    'ezpdsn.php',
    'ezpdatabasetestcase.php',
    'ezptestrunner.php',
];

foreach ( $toolkitFiles as $file )
{
    $path = $toolkit . $file;
    if ( file_exists( $path ) )
        require_once $path;
}

// Additional toolkit helpers loaded if present
$optional = [
    'ezpinihelper.php',
    'ezpextensionhelper.php',
    'ezptestdatabasehelper.php',
    'ezpdatabaseregressiontest.php',
    'ezpdatabasesuite.php',
];
foreach ( $optional as $file )
{
    $path = $toolkit . $file;
    if ( file_exists( $path ) )
        require_once $path;
}
