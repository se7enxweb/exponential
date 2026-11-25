<?php
/**
 * Guard: the bounce outputs name the delayed count next to hard and soft.
 */
class expMailBounceOutputsDelayedTest extends PHPUnit\Framework\TestCase
{
    public static function outputs()
    {
        return array(
            'cronjob' => array( 'kernel/private/classes/cronjobs/mailbounces.php' ),
            'command' => array( 'kernel/private/classes/commands/mailbounces.php' ),
            'status'  => array( 'kernel/private/classes/commands/mailstatus.php' ),
        );
    }

    /** @dataProvider outputs */
    #[\PHPUnit\Framework\Attributes\DataProvider( 'outputs' )]
    public function testOutputPrintsTheDelayedCount( $file )
    {
        $src = file_get_contents( dirname( __DIR__, 6 ) . '/' . $file );
        $this->assertNotFalse( $src, $file );
        $this->assertStringContainsString( 'delayed', $src, $file );
        $this->assertMatchesRegularExpression( "/\\\$[a-z]+(\\['last'\\])?\\['delayed'\\]/", $src, $file );
    }

    public function testTheCommandHelpNamesDelayNotices()
    {
        $src = file_get_contents( dirname( __DIR__, 6 ) . '/kernel/private/classes/commands/mailbounces.php' );
        $this->assertStringContainsString( 'delay notices (delayed)', $src );
    }
}
