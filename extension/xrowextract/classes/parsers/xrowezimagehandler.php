<?php
class XroweZImageExportHandler extends XrowBaseHandler
{
       function exportAttribute( &$attribute )
       {
		    $imageHandler=$attribute->content();
		    
		    $imageAlias = $imageHandler->imageAlias( 'original' );
		   	
		    //Return full url?  
		    //$url = eZSys::hostname() . eZSys::wwwDir() .'/'. $imageAlias['url'];
		    //$url = preg_replace( "#^(//)#", "/", $url );
		
		    return $imageAlias['url'];   	
       }
}
?>