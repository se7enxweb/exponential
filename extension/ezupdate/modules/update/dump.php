<?php

print_r("Processing...\r\n\r\n");

flush();

$cache_file = 'var/dump.lock';
$cache_life = 60*60*24*7; // Caching time, in seconds

$filemtime = @filemtime( $cache_file ); // Returns FALSE if file does not exist

if ( !$filemtime or (time() - $filemtime >= $cache_life ) )
{
	$assetDumpCommand = eZINI::instance( 'git_manager.ini' )->variable( 'GitManagerSettings', 'AssetDumpCommand' );

	if ( exec( $assetDumpCommand, $out ) )
        {
	    print_r( "Asset dump ready for collection. Dump file (.tgz contains db assets inside var directory.)\r\n\r\n" );
	    file_put_contents( "var/dump.lock", 'LOCKED' );
	}
        else
        {
            echo "Error occured!\r\n";
            var_dump( $out );
	}

}else{
   print_r( "Assets have already been dumped this week!" );
}

eZExecution::cleanExit();

?>