<?php


class BCUsingOWMultisiteIni
{
	
	/**
	 * Get eZIni instance from a given siteaccess
	 */
	public function getInstance ( $siteaccess=false, $file='site.ini' ) {
		
		if ( !$siteaccess ) {
			$siteaccess = $this->getCurrentSiteAccessName();
		}
		return eZSiteAccess::getIni( $siteaccess, $file );
	}
	
	/**
	 * Test an ini value from a given siteaccess
	 */
	public function hasVariable ( $section, $variable, $siteaccess=false, $file='site.ini' ) {
		
		$ini = $this->getInstance( $siteaccess, $file );
		return $ini->hasVariable( $section, $variable );
		
	}
	
	/**
	 * Get an ini value from a given siteaccess
	 */
	public function variable ( $section, $variable, $siteaccess=false, $file='site.ini' ) {
		
		$ini = $this->getInstance( $siteaccess, $file );
		return $ini->variable( $section, $variable );
		
	}
	
	/**
	 * Get name of current siteaccess
	 */
	public static function getCurrentSiteAccessName() {
		$siteaccess = eZSiteAccess::current();
		return $siteaccess['name'];
	}
	
}
?>
