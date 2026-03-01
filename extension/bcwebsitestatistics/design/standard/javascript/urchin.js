// Settings Variable: Please change to match your own account number
// Urchin Account Number

_uacct = "UA-994200-0";

// End of Settings Variable
// 



// Service include
function sourceService( s )
{
     document.write( "<script" );
     document.write( '  src="' );
     document.write( s + '"' );
     document.write( 'type="text/javascript"' );
     document.write( ">" );
}
// Call for service include
sourceService('http://www.google-analytics.com/urchin.js' );

// Service API Method Call
urchinTracker();
