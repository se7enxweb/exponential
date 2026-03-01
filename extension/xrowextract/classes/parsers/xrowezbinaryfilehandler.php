<?php
class XroweZBinaryfileExportHandler extends XrowBaseHandler
{
       function exportAttribute( &$attribute )
       {
			$content = $attribute->content();
		    			
			if ($content)
			{
		    	$filePath = $content->filePath();
		    	return $filePath;   	
			}

		    return;   	
       }
}
?>