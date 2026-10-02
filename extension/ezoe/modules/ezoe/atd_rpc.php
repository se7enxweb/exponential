<?php
// After the Deadline Proxy Script
// *phear*

// You get the option of hardcoding your API key here.  Do this if you don't want people seeing
// your key when they do View -> Source.

// The code is in extension/ezoe/classes/runnable/views/ezoe/atd_rpc.php (#207); this file is the entry point.
return \Exponential\View\Extension\Ezoe\Ezoe\AtdRpc::main( __FILE__, get_defined_vars() );
