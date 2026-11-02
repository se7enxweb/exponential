<?php
/**
 * The moderation module: Dashboard > Recent content (doc/eng-us/features/6.0/moderation.md). The most recently created and
 * modified content, newest first, with its status, the review hold, the draft or pending version and its scheduled
 * publication, the creator and (with moderation/personaldata) the address and the browser the submission came from;
 * filters, sorting, page sizes remembered per user, and actions on a selection: release the hold, hold (with a reason),
 * hide, reveal, move to the trash, delete, assign a section, assign an object state. Each action runs the kernel's own
 * operation per object after that object's own policy, reports per object and is audited; a large selection runs as a
 * content job in the background. The work is in kernel/classes/moderation.
 *
 * Policies: moderation/read (the list), moderation/act (the actions; each object's content, section and state policies
 * still decide), moderation/personaldata (the address and browser columns: personal data). Moving to the trash and
 * deleting ask "Verify it's you" (site.ini [ReauthGroup_delete]).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'Moderation',
                 'variable_params' => false );

$ViewList = array();

// The list. The filters and the page are view parameters; the filter form is sent with GET and answered with a
// redirect to them.
$ViewList['recent'] = array(
    'default_navigation_part' => 'ezmynavigationpart',
    'script' => 'recent.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array( 'from' => 'From', 'to' => 'To', 'date' => 'Date', 'class' => 'Class', 'status' => 'Status',
                                 'hidden' => 'Hidden', 'held' => 'Held', 'section' => 'Section', 'siteaccess' => 'Siteaccess',
                                 'creator' => 'Creator', 'sort' => 'Sort', 'order' => 'Order', 'offset' => 'Offset' ) );

// An action on the selection: POST only. ApplyButton from the list; a destructive action (trash, delete) first shows
// its confirmation, whose ConfirmDestructiveButton asks "Verify it's you".
$ViewList['action'] = array(
    'default_navigation_part' => 'ezmynavigationpart',
    'script' => 'action.php',
    'functions' => array( 'act' ),
    'single_post_actions' => array( 'ApplyButton' => 'Apply', 'ConfirmDestructiveButton' => 'Confirm', 'CancelButton' => 'Cancel' ),
    'params' => array() );

// The result per object of an action run as a content job
$ViewList['report'] = array(
    'default_navigation_part' => 'ezmynavigationpart',
    'script' => 'report.php',
    'functions' => array( 'act' ),
    'params' => array( 'JobID' ),
    'unordered_params' => array() );

$FunctionList = array();
// See Dashboard > Recent content: every object's name, class, status, hold, section and creator
$FunctionList['read'] = array();
// Run its actions on a selection; each object's own policies still decide
$FunctionList['act'] = array();
// See the address and the browser a submission came from (personal data, from the audit)
$FunctionList['personaldata'] = array();

?>
