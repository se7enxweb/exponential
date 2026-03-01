<?php
/**
 * @package eZUpdateManager
 * @class   eZUpdateManager
 * @author  Serhey Dolgushev <dolgushev.serhey@gmail.com>
 * @date    26 Sep 2013
 **/

class eZUpdateManager
{
	private static $path = './';
	private static $instance = null;

	private $callbackAttributes = array(
/*		'current_branch'  => 'getCurrentBranch',
		'current_commit'  => 'getCurrentCommit',
		'local_branches'  => 'getLocalBranches',
		'remote_branches' => 'getRemoteBranches'
*/
	);

	private function __construct() {}

	public static function getInstance() {
		if( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}

	public function attributes() {
		return array_keys( $this->callbackAttributes );
	}

	public function hasAttribute( $attr ) {
		return isset( $this->callbackAttributes[ $attr ] );
	}

	public function attribute( $attr ) {
		if( isset( $this->callbackAttributes[ $attr ] ) === false ) {
			throw new Exception( 'Undefined "' . $attr . '" attribute' );
		}

		$callback = array(
			$this,
			$this->callbackAttributes[ $attr ]
		);
		return call_user_func( $callback );
	}

	public function require( $packageName ) {
		return $this->cli( 'require ' . $packageName );
	}

	public function packageInfo( $packageName ) {
		return $this->cli( 'show ' . escapeshellcmd( $packageName ) );
	}

	public function updatePackages() {
                $result = $this->cli( 'update', false );

		return $result;
	}

	private function cli( $command, $path = false, $explodeLines = false ) {
		$ini = eZINI::instance( 'ezupdate.ini' );
		$composerPath = $ini->variable( "ComposerSettings", "Path" );

                if( $path === false )
                {
                    $cdCmd = 'cd ' . self::$path;
                }
                else
                {
                    $cdCmd = 'cd ' . $path;
                }

		$cmd    = $cdCmd . ' && php ' . $composerPath . 'composer ' . $command . ' 2>&1';
		$result = trim( shell_exec( $cmd ) );

		if( $explodeLines ) {
			$result = explode( "\n", $result );
		}

		return $result;
	}
}