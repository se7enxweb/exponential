<?php
/**
 * @package eZUpdate
 * @author  7x <info@se7enx.com>
 * @date    2 Nov 2024
 **/

$http    = eZHTTPTool::instance();
$module  = $Params['Module'];
$composer     = eZUpdateManager::getInstance();
$error   = null;
$message = null;
$output  = null;

if( $module->isCurrentAction( 'CheckoutUpdateComposerPackage' ) ) {
        $output = $composer->updatePackages();
}

$tpl = eZTemplate::factory();
$tpl->setVariable( 'composer_manager', $composer );
$tpl->setVariable( 'error', $error );
$tpl->setVariable( 'message', $message );
$tpl->setVariable( 'output',  $output );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:ezupdate/dashboard.tpl' );
$Result['path']    = array(
	array(
		'text' => ezpI18n::tr( 'extension/ezupdate', 'Update' ),
		'url'  => false
	)
);