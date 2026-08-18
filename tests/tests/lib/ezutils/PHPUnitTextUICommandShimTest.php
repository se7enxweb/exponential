<?php

class PHPUnitTextUICommandShimTest extends \PHPUnit\Framework\TestCase
{
    public function testRunConsumesSpaceSeparatedLongOptionValue(): void
    {
        [ $exitCode, $output ] = $this->runShimProcess(
            [ 'phpunit', '--help', '--dsn', 'mysql://root@127.0.0.1/testdb' ]
        );

        $this->assertSame( 0, $exitCode, $output );
        $this->assertStringNotContainsString( 'Unknown option "--dsn"', $output );
    }

    public function testRunConsumesEqualsSeparatedLongOptionValue(): void
    {
        [ $exitCode, $output ] = $this->runShimProcess(
            [ 'phpunit', '--help', '--dsn=mysql://root@127.0.0.1/testdb' ]
        );

        $this->assertSame( 0, $exitCode, $output );
        $this->assertStringNotContainsString( 'Unknown option "--dsn"', $output );
    }

    /**
     * @return array{int,string}
     */
    private function runShimProcess( array $argv ): array
    {
        $script = tempnam( sys_get_temp_dir(), 'phpunit-shim-' );
        if ( $script === false )
            $this->fail( 'Unable to create temporary script.' );

        $repoRoot = realpath( __DIR__ . '/../../../../..' );
        if ( $repoRoot === false )
            $this->fail( 'Unable to resolve repository root.' );

        $payload = var_export( $argv, true );
        $code = <<<PHP
<?php
require_once '{$repoRoot}/tests/bootstrap.php';
class ShimCommandForCli extends PHPUnit_TextUI_Command
{
    public function __construct()
    {
        \$this->longOptions['dsn='] = 'handleDsn';
        \$this->arguments['dsn'] = '';
    }
    public function handleDsn( \$value ): void
    {
        \$this->arguments['dsn'] = \$value;
    }
}
\$cmd = new ShimCommandForCli();
\$cmd->run( {$payload} );
PHP;

        $written = file_put_contents( $script, $code );
        if ( $written === false )
        {
            @unlink( $script );
            $this->fail( 'Unable to write temporary shim script.' );
        }

        $lines = [];
        $exitCode = 1;
        try
        {
            exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $script ) . ' 2>&1', $lines, $exitCode );
        }
        finally
        {
            if ( file_exists( $script ) )
                unlink( $script );
        }

        return [ $exitCode, implode( "\n", $lines ) ];
    }
}
