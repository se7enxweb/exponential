<?php
/**
 * ezoe/engine: the editor engine the current user edits with. A select over the registered engines
 * (ezoe.ini [EditorSettings] Engines[]), stored as the user preference ezoe_engine. The form is a POST
 * (checked by the form token); an empty choice goes back to the siteaccess default.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package ezoe
 */

$Module = $Params['Module'];
$http   = eZHTTPTool::instance();
$saved  = false;
$error  = false;

if ( $Module->isCurrentAction( 'Save' ) )
{
    $choice = $Module->actionParameter( 'Engine' );
    $choice = is_string( $choice ) ? $choice : '';
    if ( expOEEditor::setUserEngine( $choice ) )
        $saved = true;
    else
        $error = true;
}

$preference = eZPreferences::value( expOEEditor::PREFERENCE );
$tpl = eZTemplate::factory();
$tpl->setVariable( 'engines', expOEEditor::labels() );
$tpl->setVariable( 'preference', is_string( $preference ) ? $preference : '' );
$tpl->setVariable( 'resolved', expOEEditor::resolve() );
$tpl->setVariable( 'configured', expOEEditor::configuredEngine() );
$tpl->setVariable( 'saved', $saved );
$tpl->setVariable( 'error', $error );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:ezoe/engine.tpl' );
$Result['path'] = array( array( 'url' => false, 'text' => ezpI18n::tr( 'design/standard/ezoe', 'Online editor' ) ) );
