<?php
/**
 * @package GitManager
 * @author  Serhey Dolgushev <dolgushev.serhey@gmail.com>
 * @date    26 Sep 2013
 **/

$module  = $Params['Module'];
$git     = GitManager::getInstance();
$hash    = $Params['Hash'];
$output  = null;

if( strlen( $hash ) == 0  ) {
	return $module->redirectTo( 'git_manager/dashboard' );
}

$output = $git->commitInfo( $hash );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'output',  $output );
$tpl->setVariable( 'hash',  $hash );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:git_manager/commit_details.tpl' );
$Result['path']    = array(
	array(
		'text' => ezpI18n::tr( 'extension/git_manager', 'GIT Manager' ),
		'url'  => 'git_manager/dashboard'
	),
	array(
		'text' => ezpI18n::tr( 'extension/git_manager', 'Commit details' ),
		'url'  => false
	)
);