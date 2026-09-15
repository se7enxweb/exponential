<?php
/**
 * Editing the full attribute set of several objects in one form.
 *
 * The one thing that makes this possible without touching a single datatype:
 * every datatype edit template names its inputs after the content object
 * attribute id -
 *
 *     name="{$attribute_base}_ezstring_data_text_{$attribute.id}"
 *
 * - and that id is unique per object, per version, per language. So one form
 * can carry the attributes of any number of objects with no name collisions,
 * and fetchInput()/validateInput()/storeInput() are called per object exactly
 * as content/edit calls them for one. A datatype that works in the normal
 * editor works here, including datatypes that do not exist yet.
 *
 * What this view adds is the assembly around that: resolving a selection,
 * opening a draft per object, grouping by class so the form reads as something
 * rather than a wall, and - the part that actually needs care - reporting what
 * happened to each object when publishing several of them is not one atomic
 * act.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$Module = $Params['Module'];
$http   = eZHTTPTool::instance();
$tpl    = eZTemplate::factory();

require_once 'kernel/content/multiedit_functions.php';

$attributeBase = 'ContentObjectAttribute';

// The language comes off the url, so it is only believed if it names one this
// installation actually has. Anything else is treated as "not asked for", and
// each object falls back to its own initial language - rather than being
// carried into createNewVersionIn(), or printed, as typed.
$language = false;

if ( isset( $Params['Language'] ) && $Params['Language'] !== false )
{
    $requested = (string) $Params['Language'];

    foreach ( eZContentLanguage::fetchList() as $known )
    {
        if ( $known->attribute( 'locale' ) === $requested )
        {
            $language = $requested;
            break;
        }
    }
}

// ── Leaving ──────────────────────────────────────────────────────────────────
if ( $Module->isCurrentAction( 'MultiDiscard' ) )
{
    eZMultiEdit::discardDrafts( eZMultiEdit::draftsFromRequest( $http ) );
    return $Module->redirectTo( eZMultiEdit::returnURI( $http ) );
}

// ── Making several new ones ──────────────────────────────────────────────────
//
// "Create multiple new" arrives here with a parent and nothing else; the
// chooser below asks what and how many, and once they exist they are ordinary
// drafts that the same form edits. A new object and an existing one are the
// same thing once a draft is open.
$createParent = $http->hasPostVariable( 'MultiEditCreateParent' )
                ? (int) $http->postVariable( 'MultiEditCreateParent' ) : 0;

if ( $createParent > 0 && !$http->hasPostVariable( 'MultiEditDraft' ) )
{
    if ( $http->hasPostVariable( 'MultiEditCreateButton' ) )
    {
        $made = eZMultiEdit::createDrafts(
            $http->hasPostVariable( 'MultiEditCreateClass' ) ? $http->postVariable( 'MultiEditCreateClass' ) : '',
            $http->hasPostVariable( 'MultiEditCreateCount' ) ? $http->postVariable( 'MultiEditCreateCount' ) : 0,
            $createParent,
            $language );

        if ( $made['error'] !== false )
        {
            $tpl->setVariable( 'multiedit_error', 'create-' . $made['error'] );
            $tpl->setVariable( 'multiedit_create_parent', $createParent );
            $tpl->setVariable( 'multiedit_create_classes', eZMultiEdit::creatableClasses( $createParent ) );
            $tpl->setVariable( 'multiedit_return_uri', eZMultiEdit::returnURI( $http ) );
            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:content/multiedit.tpl' );
            $Result['path'] = eZMultiEdit::path();
            return $Result;
        }

        // Fall through with the new objects as the selection, and as drafts
        // this form already owns: their version carries the node assignment,
        // and opening another one would publish an object with no location.
        //
        // Both are forced to arrays first. They come from the request, and a
        // request that sends MultiEditObjectIDArray as a plain string turns
        // the append below into "[] operator not supported for strings" - a
        // fatal, from one crafted field.
        if ( !isset( $_POST['MultiEditObjectIDArray'] ) || !is_array( $_POST['MultiEditObjectIDArray'] ) )
            $_POST['MultiEditObjectIDArray'] = array();

        if ( !isset( $_POST['MultiEditDraft'] ) || !is_array( $_POST['MultiEditDraft'] ) )
            $_POST['MultiEditDraft'] = array();

        foreach ( $made['created'] as $newID => $newVersion )
        {
            $_POST['MultiEditObjectIDArray'][] = $newID;
            $_POST['MultiEditDraft'][$newID]   = $newVersion;
        }
    }
    else
    {
        // First time through: ask what and how many.
        $tpl->setVariable( 'multiedit_create_parent', $createParent );
        $tpl->setVariable( 'multiedit_create_classes', eZMultiEdit::creatableClasses( $createParent ) );
        $tpl->setVariable( 'multiedit_return_uri', eZMultiEdit::returnURI( $http ) );
        $tpl->setVariable( 'multiedit_max', eZMultiEdit::MAX_OBJECTS );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/multiedit.tpl' );
        $Result['path'] = eZMultiEdit::path();
        return $Result;
    }
}

// ── What was selected ────────────────────────────────────────────────────────
//
// Three callers, three habits: the sub items list posts node ids in
// DeleteIDArray (a name it reuses for every bulk action), the search results
// post node ids of their own, and anything else may post object ids. All three
// are accepted and reduced to object ids here, so no caller has to know what
// the others do.
$objectIDs = eZMultiEdit::selectionFromRequest( $http );

if ( count( $objectIDs ) === 0 )
{
    $tpl->setVariable( 'multiedit_error', 'nothing-selected' );
    $Result = array();
    $Result['content'] = $tpl->fetch( 'design:content/multiedit.tpl' );
    $Result['path'] = eZMultiEdit::path();
    return $Result;
}

// ── Open a draft for each, and say plainly what could not be opened ──────────
$session = eZMultiEdit::draftsFromRequest( $http );
$opened  = eZMultiEdit::openDrafts( $objectIDs, $language, $session );

$editable  = $opened['editable'];     // objectID => array( object, version, class, attributes )
$refused   = $opened['refused'];      // objectID => reason

if ( count( $editable ) === 0 )
{
    $tpl->setVariable( 'multiedit_error', 'nothing-editable' );
    $tpl->setVariable( 'multiedit_refused', $refused );
    $Result = array();
    $Result['content'] = $tpl->fetch( 'design:content/multiedit.tpl' );
    $Result['path'] = eZMultiEdit::path();
    return $Result;
}

// ── Storing what was typed ───────────────────────────────────────────────────
$publishing = $Module->isCurrentAction( 'MultiPublish' );
$storing    = $Module->isCurrentAction( 'MultiStore' ) || $publishing;
$outcome    = array();

if ( $storing )
{
    // Required fields are only insisted on when publishing. Saving drafts half
    // filled is the whole point of a draft.
    $outcome = eZMultiEdit::storeAll( $Module, $http, $editable, $attributeBase,
                                      array( 'skip-isRequired' => !$publishing ) );

    if ( $publishing && $outcome['all-valid'] )
    {
        $published = eZMultiEdit::publishAll( $editable );

        // Everything through, and nothing left to look at: go back where the
        // selection came from. Anything less and the reader needs the report.
        if ( $published['failed'] === 0 && $published['pending'] === 0 )
            return $Module->redirectTo( eZMultiEdit::returnURI( $http ) );

        $tpl->setVariable( 'multiedit_published', $published['results'] );
    }
}

// ── Draw ─────────────────────────────────────────────────────────────────────
$tpl->setVariable( 'multiedit_groups', eZMultiEdit::groupByClass( $editable ) );
$tpl->setVariable( 'multiedit_refused', $refused );
$tpl->setVariable( 'multiedit_count', count( $editable ) );
$tpl->setVariable( 'multiedit_validation', isset( $outcome['validation'] ) ? $outcome['validation'] : array() );
$tpl->setVariable( 'multiedit_all_valid', isset( $outcome['all-valid'] ) ? $outcome['all-valid'] : true );
$tpl->setVariable( 'multiedit_drafts', eZMultiEdit::draftMap( $editable ) );
$tpl->setVariable( 'multiedit_return_uri', eZMultiEdit::returnURI( $http ) );
$tpl->setVariable( 'attribute_base', $attributeBase );

// Autosave, on the same settings the ordinary editor uses.
//
// ezautosave itself cannot be reused as it stands: its AutoSubmit is bound to
// one form and posts to an endpoint naming one object and one version, and
// this form holds many. So the interval and the track-input preference are
// taken from autosave.ini - an installation that has turned autosave off gets
// it off here too - and the saving is done by posting this form back to its
// own Save drafts action, which already stores every object in one request.
$autosaveInterval = 0;
$autosaveTrack    = false;

if ( in_array( 'ezautosave', (array) eZExtension::activeExtensions(), true ) )
{
    $autosaveINI      = eZINI::instance( 'autosave.ini' );
    $autosaveInterval = (int) $autosaveINI->variable( 'AutosaveSettings', 'Interval' );
    $autosaveTrack    = $autosaveINI->variable( 'AutosaveSettings', 'TrackUserInput' ) === 'enabled';
}

$tpl->setVariable( 'multiedit_autosave_interval', $autosaveInterval );
$tpl->setVariable( 'multiedit_autosave_track', $autosaveTrack );
$tpl->setVariable( 'multiedit_language', eZMultiEdit::languageOf( $editable, $language ) );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:content/multiedit.tpl' );
$Result['path'] = eZMultiEdit::path();
