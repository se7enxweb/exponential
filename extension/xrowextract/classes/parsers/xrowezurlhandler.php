<?php

class XroweZURLHandler extends XrowBaseHandler
{
	public function exportAttribute( &$attribute )
	{
		$csvINI = eZINI::instance( 'csv.ini' );
		$ini_option = $csvINI->variable( 'General', 'StripURLText' );
		if ($ini_option == "true")
		{
			$tempstring = $attribute->DataText;
		}
		else
		{
			$tempstring = $attribute->content() . ' ' . $attribute->DataText;
		}
		return $this->escape( $tempstring );
	}
}

?>