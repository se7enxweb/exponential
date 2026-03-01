<?php
/**
 * @package eZUpdate
 * @class   ezupdateInfo
 **/

class ezupdateInfo
{
	public static function info() {
		return array(
			'Name'      => '<a href="https://github.com/se7enxweb/ezupdate">eZ Update : Composer Updater</a>',
			'Version'   => '1.0.1',
			'Author'    => '7x',
			'Copyright' => 'Copyright &copy; 1999 - ' . date( 'Y' ) . ' <a href="https://se7enx.com" target="blank">7x</a>',
                        'License' => "GNU General Public License v2",
                        'info_url'  => 'https://github.com/se7enxweb/ezupdate'
                        
		);
	}
}