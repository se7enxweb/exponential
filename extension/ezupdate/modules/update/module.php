<?php
/**
 * @package GitManager
 * @author  Serhey Dolgushev <dolgushev.serhey@gmail.com>
 * @date    26 Sep 2013
 **/

$Module = array(
	'name'      => 'Update',
	'functions' => array()
);

$ViewList = array(
	'dashboard' => array(
		'script'                  => 'dashboard.php',
		'functions'               => array( 'ezupdate' ),
		'params'                  => array(),
		'default_navigation_part' => 'ezsetupnavigationpart',
		'single_post_actions'     => array(
			'CheckoutLocalBranch'  => 'CheckoutLocalBranch',
			'CheckoutRemoteBranch' => 'CheckoutRemoteBranch',
			'SetCommitsFilter'     => 'SetCommitsFilter',
			'CheckoutCommit'       => 'CheckoutCommit',
                        'CheckoutUpdateComposerPackage' => 'CheckoutUpdateComposerPackage'
		)
	),
	'show' => array(
		'script'                  => 'show.php',
		'functions'               => array( 'ezupdate' ),
		'params'                  => array( 'Package' ),
		'default_navigation_part' => 'ezsetupnavigationpart'
	)
);

$FunctionList = array(
	'ezupdate' => array(),
	'dump' => array()
);