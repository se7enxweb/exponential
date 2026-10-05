<?php
/**
 * The Exponential sniff against type declarations (bin/phpcs/Exponential), run
 * through PHP_CodeSniffer on fixtures/typedeclarations.inc.
 *
 * The fixture holds every case once: strict_types, a typed constant, typed
 * properties, scalar, union and promoted parameter types, return types, and the
 * PHP 5 hints that stay allowed (array, class names, nullable). It is checked once
 * as a kernel file and once as a test file, where ": void" is allowed.
 *
 * Needs the development tools (make devtools); skipped without them.
 *
 * Run: php vendor/bin/phpunit tests/tests/bin/phpcs/NoTypeDeclarationsSniffTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class NoTypeDeclarationsSniffTest extends PHPUnit\Framework\TestCase
{
    const SNIFF = 'Exponential.TypeDeclarations.NoTypeDeclarations';

    private static $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 4 );
    }

    protected function setUp(): void
    {
        if ( !is_file( self::$root . '/.devtools/vendor/bin/phpcs' ) )
        {
            $this->markTestSkipped( 'needs the development tools (make devtools)' );
        }
    }

    /**
     * Runs the sniff on the fixture as $path and returns the number of reports per code
     *
     * @param string $path
     * @return array code => count
     */
    private function countsAs( $path )
    {
        $command = 'cd ' . escapeshellarg( self::$root ) . ' && php .devtools/vendor/bin/phpcs -q --no-colors --report=json' .
                   ' --sniffs=' . self::SNIFF . ' --stdin-path=' . escapeshellarg( $path ) . ' - < ' .
                   escapeshellarg( __DIR__ . '/fixtures/typedeclarations.inc' );
        $data = json_decode( (string)shell_exec( $command ), true );
        $this->assertIsArray( $data, 'phpcs answers with a JSON report' );

        $counts = array();
        foreach ( $data['files'] as $file )
        {
            foreach ( $file['messages'] as $message )
            {
                $code = substr( $message['source'], strlen( self::SNIFF ) + 1 );
                $counts[$code] = isset( $counts[$code] ) ? $counts[$code] + 1 : 1;
            }
        }
        ksort( $counts );
        return $counts;
    }

    /**
     * In a kernel file every PHP 7 and later type declaration is reported, the PHP 5
     * hints (array, class names, nullable) are not.
     */
    public function testKernelFile()
    {
        $this->assertSame( array( 'ConstantType' => 1,
                                  'ParameterType' => 5,
                                  'PropertyType' => 2,
                                  'ReturnType' => 4,
                                  'StrictTypes' => 1 ),
                           $this->countsAs( 'kernel/classes/typedeclarationsfixture.php' ) );
    }

    /**
     * In a test file ": void", which PHPUnit requires, is allowed; everything else
     * is reported as in a kernel file.
     */
    public function testTestFileAllowsVoid()
    {
        $this->assertSame( array( 'ConstantType' => 1,
                                  'ParameterType' => 5,
                                  'PropertyType' => 2,
                                  'ReturnType' => 3,
                                  'StrictTypes' => 1 ),
                           $this->countsAs( 'tests/tests/kernel/classes/typedeclarationsfixtureTest.php' ) );
    }
}
