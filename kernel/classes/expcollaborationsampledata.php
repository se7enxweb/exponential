<?php
/**
 * File containing the expCollaborationSampleData class.
 *
 * Builds, reports and removes a self-contained set of sample content for the collaboration tool: a user group
 * with three editors, an approval workflow that only reaches one sample section, a folder in that section and
 * articles that wait for approval, were approved or were denied, with comment threads. Everything is made
 * through the kernel's own APIs (content publishing, the approval workflow event, the approval collaboration
 * handler) so the collaboration items are real, and everything is marked so that remove() deletes exactly it:
 * names start with "Sample: " (or are "Editors (sample)" / "Approval (sample)"), content remote ids start with
 * "sample-collab-", the section identifier is "sample_collaboration" and the e-mail addresses end in
 * "@example.invalid".
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expCollaborationSampleData
{
    const NAME_PREFIX = 'Sample: ';
    const REMOTE_PREFIX = 'sample-collab-';
    const SECTION_IDENTIFIER = 'sample_collaboration';
    const SECTION_NAME = 'Sample: collaboration';
    const FOLDER_NAME = 'Sample: collaboration';
    const USER_GROUP_NAME = 'Editors (sample)';
    const WORKFLOW_GROUP_NAME = 'Sample: collaboration';
    const WORKFLOW_NAME = 'Approval (sample)';
    const MAIL_DOMAIN = 'example.invalid';

    /** @var callable|null receives ( $message ) for every step */
    private $logger;

    /** @var array the person records, by key */
    private static $people = array(
        'anna'  => array( 'first' => 'Anna',  'last' => 'Sample-Editor' ),
        'ben'   => array( 'first' => 'Ben',   'last' => 'Sample-Editor' ),
        'carla' => array( 'first' => 'Carla', 'last' => 'Sample-Editor' ) );

    /**
     * The scenarios. "state" is waiting, approved or denied. "thread" lists messages in order; "who" is an editor key
     * or "admin". "read" says how much of the thread the administrator has read: all, none or a number of messages.
     * "age" is how many hours ago the article was sent for approval.
     */
    private static function scenarios()
    {
        return array(
            array( 'key' => 'spring', 'title' => 'Sample: Spring campaign announcement', 'author' => 'anna', 'state' => 'waiting', 'age' => 30,
                   'group' => 'queue',
                   'text' => 'The spring campaign starts on the first of April. Tickets, opening hours and the programme are listed below.',
                   'thread' => array( array( 'anna', 'Please check the opening hours in the second paragraph before this goes out.' ),
                                      array( 'anna', 'I also added the programme list. Tell me if the order is wrong.' ) ),
                   'read' => 1 ),
            array( 'key' => 'curator', 'title' => 'Sample: Interview with the new curator', 'author' => 'ben', 'state' => 'waiting', 'age' => 9,
                   'group' => 'queue',
                   'text' => 'Our new curator talks about the first exhibition, the plans for the autumn and the ideas for younger visitors.',
                   'thread' => array( array( 'ben', 'The curator has read the text and agrees with it.' ) ),
                   'read' => 'none' ),
            array( 'key' => 'report', 'title' => 'Sample: Quarterly report summary', 'author' => 'carla', 'state' => 'waiting', 'age' => 3,
                   'group' => 'queue',
                   'text' => 'Visitor numbers rose by eight percent compared with the previous quarter. The summary lists the main figures.',
                   'thread' => array(), 'read' => 'all' ),
            array( 'key' => 'checklist', 'title' => 'Sample: Newsletter launch checklist', 'author' => 'anna', 'state' => 'approved', 'age' => 96,
                   'group' => 'approved',
                   'text' => 'A short checklist for the launch of the newsletter: list, template, test mail, schedule.',
                   'thread' => array( array( 'anna', 'Here is the checklist for the newsletter launch.' ),
                                      array( 'admin', 'Looks complete. Approved.' ) ),
                   'comment' => 'Looks complete. Approved.', 'read' => 'all' ),
            array( 'key' => 'schedule', 'title' => 'Sample: Event schedule', 'author' => 'carla', 'state' => 'approved', 'age' => 52,
                   'group' => 'approved',
                   'text' => 'The schedule of the events in May with times and rooms.',
                   'thread' => array(), 'read' => 'all' ),
            array( 'key' => 'quote', 'title' => 'Sample: Press quote without a source', 'author' => 'ben', 'state' => 'denied', 'age' => 72,
                   'group' => 'denied',
                   'text' => 'A quote from a visitor survey is used in the headline. The survey itself is not named.',
                   'thread' => array( array( 'ben', 'Is the quote fine for the headline?' ),
                                      array( 'admin', 'Not without a source. Please name the survey and send it again.' ) ),
                   'comment' => 'Not without a source. Please name the survey and send it again.', 'read' => 'all' ) );
    }

    /** The article written by the administrator that the editors review. */
    private static function adminScenario()
    {
        return array( 'key' => 'guidelines', 'title' => 'Sample: Editorial guidelines', 'age' => 20,
                      'text' => 'Rules for headlines, sources and images. The editors are asked to read the draft before it is published.',
                      'thread' => array( array( 'admin', 'Please read the draft and tell me what is missing.' ),
                                         array( 'carla', 'The section about image sources needs an example.' ),
                                         array( 'ben', 'I would add a rule about quotes, too.' ) ),
                      'group' => 'inbox' );
    }

    public function __construct( $logger = null )
    {
        $this->logger = $logger;
    }

    private function log( $message )
    {
        if ( $this->logger )
            call_user_func( $this->logger, $message );
    }

    // ----------------------------------------------------------------------------------------------------------
    // What exists
    // ----------------------------------------------------------------------------------------------------------

    /**
     * What a run would do now, one row per step: array( 'what' => ..., 'name' => ..., 'state' => 'exists'|'missing' ).
     * Reads only; nothing is created.
     *
     * @return array
     */
    public function plan()
    {
        $rows = array();
        $section = eZSection::fetchByIdentifier( self::SECTION_IDENTIFIER );
        $rows[] = $this->row( 'Section', self::SECTION_NAME . ' (' . self::SECTION_IDENTIFIER . ')', $section );
        $rows[] = $this->row( 'Folder', self::FOLDER_NAME, eZContentObject::fetchByRemoteID( self::REMOTE_PREFIX . 'folder' ) );
        $rows[] = $this->row( 'User group', self::USER_GROUP_NAME, eZContentObject::fetchByRemoteID( self::REMOTE_PREFIX . 'group' ) );
        foreach ( self::$people as $key => $person )
            $rows[] = $this->row( 'Editor', $person['first'] . ' ' . $person['last'] . ' <' . $key . '.sample@' . self::MAIL_DOMAIN . '>',
                                  eZContentObject::fetchByRemoteID( self::REMOTE_PREFIX . 'user-' . $key ) );
        $workflow = $this->findWorkflow();
        $rows[] = $this->row( 'Workflow', self::WORKFLOW_NAME . ' (approve event for the section only)', $workflow );
        $trigger = $this->contentPublishTrigger();
        if ( $trigger === null )
            $rows[] = array( 'what' => 'Trigger', 'name' => 'content / publish / before', 'state' => 'missing' );
        else
            $rows[] = array( 'what' => 'Trigger', 'name' => 'content / publish / before',
                             'state' => $workflow && $trigger->attribute( 'workflow_id' ) == $workflow->attribute( 'id' )
                                        ? 'exists' : 'taken by another workflow, not changed' );
        foreach ( self::scenarios() as $s )
            $rows[] = $this->row( 'Article (' . $s['state'] . ')', $s['title'], eZContentObject::fetchByRemoteID( self::REMOTE_PREFIX . 'article-' . $s['key'] ) );
        $a = self::adminScenario();
        $rows[] = $this->row( 'Article (waits for the editors)', $a['title'], eZContentObject::fetchByRemoteID( self::REMOTE_PREFIX . 'article-' . $a['key'] ) );
        return $rows;
    }

    private function row( $what, $name, $found )
    {
        return array( 'what' => $what, 'name' => $name, 'state' => $found ? 'exists' : 'missing' );
    }

    /** @return int the number of sample objects that exist (0 means a clean installation) */
    public function existingCount()
    {
        $n = 0;
        foreach ( $this->plan() as $row )
            if ( $row['state'] === 'exists' )
                ++$n;
        return $n + count( $this->sampleItemIDs() ) + count( $this->sampleCollaborationGroupIDs() );
    }

    // ----------------------------------------------------------------------------------------------------------
    // Build
    // ----------------------------------------------------------------------------------------------------------

    /**
     * Creates what is missing; what exists is left as it is.
     *
     * @return array counts: created, skipped
     */
    public function apply()
    {
        $counts = array( 'created' => 0, 'skipped' => 0, 'warnings' => 0 );
        $admin = $this->adminUser();
        if ( !$admin )
            throw new RuntimeException( 'The administrator account was not found.' );
        $adminID = (int)$admin->attribute( 'contentobject_id' );
        $previous = eZUser::currentUser();
        eZUser::setCurrentlyLoggedInUser( $admin, $adminID );

        try
        {
            // an administrator who never opened the collaboration tool has no profile (and no Inbox group) yet: the
            // run makes it and marks it, so --remove can take it away again
            if ( eZCollaborationProfile::fetchByUser( $adminID ) === null )
            {
                $profile = eZCollaborationProfile::instance( $adminID );
                $profile->setAttribute( 'data_text1', self::REMOTE_PREFIX . 'profile' );
                $profile->store();
            }
            $section = $this->ensureSection( $counts );
            $folder = $this->ensureFolder( $section, $adminID, $counts );
            $group = $this->ensureGroup( $adminID, $counts );
            $editors = $this->ensureEditors( $group, $adminID, $counts );
            $workflow = $this->ensureWorkflow( $section, $adminID, $counts );
            $triggered = $this->ensureTrigger( $workflow, $counts );

            $queueGroup = $this->ensureCollaborationGroup( $adminID, 'Sample: Waiting for my approval', 0, $counts );
            $approvedGroup = $this->ensureCollaborationGroup( $adminID, 'Sample: Approved', $queueGroup->attribute( 'id' ), $counts );
            $deniedGroup = $this->ensureCollaborationGroup( $adminID, 'Sample: Denied', $queueGroup->attribute( 'id' ), $counts );
            $groups = array( 'queue' => $queueGroup, 'approved' => $approvedGroup, 'denied' => $deniedGroup );

            foreach ( self::scenarios() as $s )
                $this->ensureArticle( $s, $folder, $section, $editors, $adminID, $triggered, $groups, $counts );
            $this->ensureAdminArticle( self::adminScenario(), $folder, $section, $editors, $adminID, $counts );
        }
        catch ( Exception $e )
        {
            eZUser::setCurrentlyLoggedInUser( $previous, $previous->attribute( 'contentobject_id' ) );
            throw $e;
        }
        eZUser::setCurrentlyLoggedInUser( $previous, $previous->attribute( 'contentobject_id' ) );
        eZContentCacheManager::clearAllContentCache();
        return $counts;
    }

    private function adminUser()
    {
        $admin = eZUser::fetchByName( 'admin' );
        if ( !$admin )
            $admin = eZUser::fetch( 14 );
        return $admin ? $admin : null;
    }

    private function ensureSection( &$counts )
    {
        $section = eZSection::fetchByIdentifier( self::SECTION_IDENTIFIER );
        if ( $section )
        {
            ++$counts['skipped'];
            return $section;
        }
        $section = new eZSection( array( 'name' => self::SECTION_NAME, 'identifier' => self::SECTION_IDENTIFIER,
                                         'locale' => '', 'navigation_part_identifier' => 'ezcontentnavigationpart' ) );
        $section->store();
        $this->log( 'Created the section ' . self::SECTION_NAME . ' (id ' . $section->attribute( 'id' ) . ')' );
        ++$counts['created'];
        return $section;
    }

    private function create( $class, $parentNodeID, $remote, $attributes, $creatorID, $sectionID )
    {
        $object = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => $parentNodeID, 'class_identifier' => $class, 'creator_id' => $creatorID,
            'section_id' => $sectionID, 'remote_id' => self::REMOTE_PREFIX . $remote, 'attributes' => $attributes ) );
        if ( !$object )
            throw new RuntimeException( "The $class '$remote' could not be created." );
        return $object;
    }

    private function ensureFolder( $section, $adminID, &$counts )
    {
        $folder = eZContentObject::fetchByRemoteID( self::REMOTE_PREFIX . 'folder' );
        if ( $folder )
        {
            ++$counts['skipped'];
            return $folder;
        }
        $parent = eZContentObjectTreeNode::fetch( 43 );
        $parentID = $parent && $parent->attribute( 'class_identifier' ) === 'folder' ? 43 : 2;
        $folder = $this->create( 'folder', $parentID, 'folder',
                                 array( 'name' => self::FOLDER_NAME,
                                        'short_description' => $this->xml( 'Sample content of the collaboration tool: articles that wait for approval, were approved or were denied.' ) ),
                                 $adminID, $section->attribute( 'id' ) );
        $this->log( 'Created the folder ' . self::FOLDER_NAME . ' under node ' . $parentID );
        ++$counts['created'];
        return $folder;
    }

    private function ensureGroup( $adminID, &$counts )
    {
        $group = eZContentObject::fetchByRemoteID( self::REMOTE_PREFIX . 'group' );
        if ( $group )
        {
            ++$counts['skipped'];
            return $group;
        }
        $group = $this->create( 'user_group', 5, 'group', array( 'name' => self::USER_GROUP_NAME,
                                                                  'description' => 'Sample editors of the collaboration tool.' ),
                                $adminID, 0 );
        $this->log( 'Created the user group ' . self::USER_GROUP_NAME );
        ++$counts['created'];
        return $group;
    }

    private function ensureEditors( $group, $adminID, &$counts )
    {
        $users = array();
        foreach ( self::$people as $key => $person )
        {
            $object = eZContentObject::fetchByRemoteID( self::REMOTE_PREFIX . 'user-' . $key );
            if ( $object )
            {
                ++$counts['skipped'];
                $users[$key] = $object;
                continue;
            }
            $login = 'sample-' . $key;
            // a random password nobody sees: these accounts are only ever participants
            $password = bin2hex( random_bytes( 16 ) );
            $type = eZUser::hashType();
            $hash = eZUser::createHash( $login, $password, eZUser::site(), $type );
            $account = $login . '|' . $key . '.sample@' . self::MAIL_DOMAIN . '|' . $hash . '|' . eZUser::passwordHashTypeName( $type ) . '|1';
            $object = $this->create( 'user', (int)$group->attribute( 'main_node_id' ), 'user-' . $key,
                                     array( 'first_name' => $person['first'], 'last_name' => $person['last'], 'user_account' => $account ),
                                     $adminID, 0 );
            $this->log( 'Created the editor ' . $person['first'] . ' (' . $login . ')' );
            ++$counts['created'];
            $users[$key] = $object;
        }
        return $users;
    }

    private function findWorkflow()
    {
        $list = eZWorkflow::fetchList();
        foreach ( $list as $workflow )
            if ( $workflow->attribute( 'name' ) === self::WORKFLOW_NAME )
                return $workflow;
        return null;
    }

    private function ensureWorkflow( $section, $adminID, &$counts )
    {
        $workflow = $this->findWorkflow();
        if ( $workflow )
        {
            ++$counts['skipped'];
            return $workflow;
        }
        $db = eZDB::instance();
        $db->begin();
        $groupID = false;
        foreach ( eZWorkflowGroup::fetchList() as $g )
            if ( $g->attribute( 'name' ) === self::WORKFLOW_GROUP_NAME )
                $groupID = $g->attribute( 'id' );
        if ( !$groupID )
        {
            $wg = eZWorkflowGroup::create( $adminID );
            $wg->setAttribute( 'name', self::WORKFLOW_GROUP_NAME );
            $wg->store();
            $groupID = $wg->attribute( 'id' );
        }
        $workflow = eZWorkflow::create( $adminID );
        $workflow->setAttribute( 'name', self::WORKFLOW_NAME );
        $workflow->setAttribute( 'version', 0 );
        $workflow->store();
        $workflowID = $workflow->attribute( 'id' );
        $link = eZWorkflowGroupLink::create( $workflowID, 0, $groupID, self::WORKFLOW_GROUP_NAME );
        $link->store();
        $event = eZWorkflowEvent::create( $workflowID, 'event_ezapprove' );
        $event->setAttribute( 'version', 0 );
        $event->setAttribute( 'data_text1', (string)$section->attribute( 'id' ) ); // only this section
        $event->setAttribute( 'data_text3', (string)$adminID );                    // the administrator approves
        $event->setAttribute( 'data_int3', eZApproveType::VERSION_OPTION_ALL );
        $event->store();
        $db->commit();
        $this->log( 'Created the workflow ' . self::WORKFLOW_NAME . ' (approval for section ' . $section->attribute( 'id' ) . ' only)' );
        ++$counts['created'];
        return eZWorkflow::fetch( $workflowID );
    }

    private function contentPublishTrigger()
    {
        $list = eZTrigger::fetchList( array( 'module' => 'content', 'function' => 'publish', 'connectType' => 'b' ) );
        return $list ? $list[0] : null;
    }

    /** @return bool true when the sample workflow runs on content/publish */
    private function ensureTrigger( $workflow, &$counts )
    {
        $trigger = $this->contentPublishTrigger();
        if ( $trigger === null )
        {
            eZTrigger::createNew( 'content', 'publish', 'b', $workflow->attribute( 'id' ) );
            $this->log( 'Attached the workflow to content / publish / before' );
            ++$counts['created'];
            return true;
        }
        if ( $trigger->attribute( 'workflow_id' ) == $workflow->attribute( 'id' ) )
        {
            ++$counts['skipped'];
            return true;
        }
        $this->log( 'WARNING: content / publish / before already runs another workflow (id ' . $trigger->attribute( 'workflow_id' ) .
                    '); it is left alone and the approval items are created through the approval handler instead' );
        ++$counts['warnings'];
        return false;
    }

    private function ensureCollaborationGroup( $userID, $title, $parentID, &$counts )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT id FROM ezcollab_group WHERE user_id=" . (int)$userID . " AND title='" . $db->escapeString( $title ) . "'" );
        if ( $rows )
        {
            ++$counts['skipped'];
            return eZCollaborationGroup::fetch( (int)$rows[0]['id'], $userID );
        }
        $group = eZCollaborationGroup::instantiate( $userID, $title, $parentID );
        $this->log( 'Created the collaboration group ' . $title );
        ++$counts['created'];
        return $group;
    }

    private function xml( $text )
    {
        return '<?xml version="1.0" encoding="utf-8"?>' .
               '<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">' .
               '<paragraph>' . htmlspecialchars( $text, ENT_XML1 | ENT_QUOTES, 'UTF-8' ) . '</paragraph></section>';
    }

    private function articleAttributes( $s )
    {
        return array( 'title' => $s['title'], 'short_title' => $s['title'], 'intro' => $this->xml( $s['text'] ), 'body' => $this->xml( $s['text'] ) );
    }

    private function loginAs( $user )
    {
        eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
    }

    /** The article of an editor and its approval item, taken to the scenario's state. */
    private function ensureArticle( $s, $folder, $section, $editors, $adminID, $triggered, $groups, &$counts )
    {
        $remote = self::REMOTE_PREFIX . 'article-' . $s['key'];
        if ( eZContentObject::fetchByRemoteID( $remote ) )
        {
            ++$counts['skipped'];
            return;
        }
        $editorObject = $editors[$s['author']];
        $editorID = (int)$editorObject->attribute( 'id' );
        $this->loginAs( eZUser::fetch( $editorID ) );
        $object = $this->create( 'article', (int)$folder->attribute( 'main_node_id' ), 'article-' . $s['key'], $this->articleAttributes( $s ), $editorID, $section->attribute( 'id' ) );
        $objectID = (int)$object->attribute( 'id' );
        $this->nameVersion( $object, 1 );
        $this->loginAs( eZUser::fetch( $adminID ) );

        $itemID = $this->itemForObject( $objectID );
        if ( !$itemID )
        {
            // the workflow did not take it: make the approval item through the handler
            $itemID = (int)eZApproveCollaborationHandler::createApproval( $objectID, 1, $editorID, array( $adminID ) )->attribute( 'id' );
        }
        $item = eZCollaborationItem::fetch( $itemID );
        $this->silenceNotifications( $itemID );
        $processID = $this->processForItem( $itemID );

        // the thread: each message written by its own author, the decision by the administrator
        $http = eZHTTPTool::instance();
        $handler = eZCollaborationItemHandler::instantiate( 'ezapprove' );
        $module = new expCollaborationSampleModule();
        $thread = $s['thread'];
        $lastIndex = count( $thread ) - 1;
        foreach ( $thread as $i => $message )
        {
            $who = $message[0];
            $isDecision = $s['state'] !== 'waiting' && $i === $lastIndex && $who === 'admin';
            if ( $isDecision )
            {
                $this->loginAs( eZUser::fetch( $adminID ) );
                $http->setPostVariable( 'Collaboration_ApproveComment', $message[1] );
                $http->setPostVariable( $s['state'] === 'approved' ? 'CollaborationAction_Accept' : 'CollaborationAction_Deny', '1' );
                $handler->handleCustomAction( $module, eZCollaborationItem::fetch( $itemID ) );
                unset( $_POST['Collaboration_ApproveComment'] );
                unset( $_POST['CollaborationAction_Accept'] );
                unset( $_POST['CollaborationAction_Deny'] );
                continue;
            }
            $speaker = $who === 'admin' ? eZUser::fetch( $adminID ) : eZUser::fetch( (int)$editors[$who]->attribute( 'id' ) );
            $this->loginAs( $speaker );
            $this->addComment( $itemID, $message[1], (int)$speaker->attribute( 'contentobject_id' ) );
        }
        $this->loginAs( eZUser::fetch( $adminID ) );
        if ( $s['state'] !== 'waiting' && ( empty( $thread ) || $thread[$lastIndex][0] !== 'admin' ) )
        {
            $http->setPostVariable( $s['state'] === 'approved' ? 'CollaborationAction_Accept' : 'CollaborationAction_Deny', '1' );
            $handler->handleCustomAction( $module, eZCollaborationItem::fetch( $itemID ) );
            unset( $_POST['CollaborationAction_Accept'] );
            unset( $_POST['CollaborationAction_Deny'] );
        }
        if ( $s['state'] !== 'waiting' && $processID )
            $this->advanceProcess( $processID );

        $this->retime( $itemID, $s, $adminID );
        $this->moveToGroup( $itemID, $adminID, $groups[$s['group']] );
        $this->log( 'Created the article "' . $s['title'] . '" (' . $s['state'] . ', approval item ' . $itemID . ')' );
        ++$counts['created'];
    }

    /** The administrator's own article, published at once, with an approval item the sample editors are asked to handle. */
    private function ensureAdminArticle( $s, $folder, $section, $editors, $adminID, &$counts )
    {
        $remote = self::REMOTE_PREFIX . 'article-' . $s['key'];
        if ( eZContentObject::fetchByRemoteID( $remote ) )
        {
            ++$counts['skipped'];
            return;
        }
        $this->loginAs( eZUser::fetch( $adminID ) );
        $object = $this->create( 'article', (int)$folder->attribute( 'main_node_id' ), 'article-' . $s['key'], $this->articleAttributes( $s ), $adminID, $section->attribute( 'id' ) );
        $editorIDs = array();
        foreach ( $editors as $e )
            $editorIDs[] = (int)$e->attribute( 'id' );
        $itemID = (int)eZApproveCollaborationHandler::createApproval( (int)$object->attribute( 'id' ), 1, $adminID, $editorIDs )->attribute( 'id' );
        $this->silenceNotifications( $itemID );
        foreach ( $s['thread'] as $message )
        {
            $speaker = $message[0] === 'admin' ? eZUser::fetch( $adminID ) : eZUser::fetch( (int)$editors[$message[0]]->attribute( 'id' ) );
            $this->loginAs( $speaker );
            $this->addComment( $itemID, $message[1], (int)$speaker->attribute( 'contentobject_id' ) );
        }
        $this->loginAs( eZUser::fetch( $adminID ) );
        $this->retime( $itemID, $s + array( 'state' => 'waiting', 'read' => 'all' ), $adminID );
        $this->log( 'Created the article "' . $s['title'] . '" (waits for the editors, approval item ' . $itemID . ')' );
        ++$counts['created'];
    }

    /** An editor's save stores the version's name; publishing through the API does not, so a draft would be called "New Article". */
    private function nameVersion( $object, $versionNumber )
    {
        $object = eZContentObject::fetch( $object->attribute( 'id' ) );
        $version = $object->version( $versionNumber );
        if ( !$version )
            return;
        $language = $version->initialLanguageCode();
        $name = $object->attribute( 'content_class' )->contentObjectName( $object, $versionNumber, $language );
        if ( $name !== '' )
            $object->setName( $name, $versionNumber, $language );
    }

    private function addComment( $itemID, $text, $userID )
    {
        $item = eZCollaborationItem::fetch( $itemID );
        $message = eZCollaborationSimpleMessage::create( 'ezapprove_comment', $text, $userID );
        $message->store();
        eZCollaborationItemMessageLink::addMessage( $item, $message, eZApproveCollaborationHandler::MESSAGE_TYPE_APPROVE, $userID );
    }

    private function itemForObject( $objectID )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT id FROM ezcollab_item WHERE type_identifier='ezapprove' AND data_int1=" . (int)$objectID . " ORDER BY id DESC", array( 'limit' => 1 ) );
        return $rows ? (int)$rows[0]['id'] : 0;
    }

    private function processForItem( $itemID )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT workflow_process_id FROM ezapprove_items WHERE collaboration_id=" . (int)$itemID );
        return $rows ? (int)$rows[0]['workflow_process_id'] : 0;
    }

    /** The notification events of an item are marked handled: sample data must never send a mail. */
    private function silenceNotifications( $itemID )
    {
        $db = eZDB::instance();
        $db->query( "UPDATE eznotificationevent SET status=" . eZNotificationEvent::STATUS_HANDLED .
                    " WHERE event_type_string='ezcollaboration' AND data_int1=" . (int)$itemID );
    }

    /**
     * Runs one deferred approval workflow process the way the workflow cronjob does (decision taken: the object is
     * published after an approval and goes back to a draft after a denial).
     */
    private function advanceProcess( $processID )
    {
        $process = eZWorkflowProcess::fetch( $processID );
        if ( !$process )
            return;
        $db = eZDB::instance();
        $db->begin();
        $workflow = eZWorkflow::fetch( $process->attribute( 'workflow_id' ) );
        $workflowEvent = $process->attribute( 'event_id' ) != 0 ? eZWorkflowEvent::fetch( $process->attribute( 'event_id' ) ) : null;
        $eventLog = array();
        $process->run( $workflow, $workflowEvent, $eventLog );
        $status = $process->attribute( 'status' );
        if ( $status != eZWorkflow::STATUS_DONE )
        {
            if ( in_array( $status, array( eZWorkflow::STATUS_RESET, eZWorkflow::STATUS_FAILED, eZWorkflow::STATUS_NONE,
                                           eZWorkflow::STATUS_CANCELLED, eZWorkflow::STATUS_BUSY ) ) )
            {
                if ( $bodyMemento = eZOperationMemento::fetchMain( $process->attribute( 'memento_key' ) ) )
                    $bodyMemento->remove();
                foreach ( eZOperationMemento::fetchList( $process->attribute( 'memento_key' ) ) as $memento )
                    $memento->remove();
            }
            if ( $status == eZWorkflow::STATUS_CANCELLED )
                $process->removeThis();
            else
                $process->store();
        }
        else
        {
            $bodyMemento = eZOperationMemento::fetchChild( $process->attribute( 'memento_key' ) );
            $mainMemento = $bodyMemento ? $bodyMemento->attribute( 'main_memento' ) : null;
            if ( $bodyMemento && $mainMemento )
            {
                $mementoData = $bodyMemento->data();
                $mementoData['main_memento'] = $mainMemento;
                $mementoData['skip_trigger'] = true;
                $mementoData['memento_key'] = $process->attribute( 'memento_key' );
                $bodyMemento->remove();
                $parameters = isset( $mementoData['parameters'] ) ? $mementoData['parameters'] : array();
                eZOperationHandler::execute( $mementoData['module_name'], $mementoData['operation_name'], $parameters, $mementoData );
                $process->removeThis();
            }
        }
        $db->commit();
    }

    /** Spreads the item's timeline over the past, so the lists show different ages, and sets what the administrator has read. */
    private function retime( $itemID, $s, $adminID )
    {
        $db = eZDB::instance();
        $itemID = (int)$itemID;
        $start = time() - (int)$s['age'] * 3600;
        $messages = $db->arrayQuery( "SELECT id FROM ezcollab_item_message_link WHERE collaboration_id=$itemID ORDER BY id ASC" );
        $times = array();
        $t = $start;
        foreach ( $messages as $m )
        {
            $t += 1800 + 600 * count( $times );
            $times[] = $t;
            $db->query( "UPDATE ezcollab_item_message_link SET created=$t, modified=$t WHERE id=" . (int)$m['id'] );
            $db->query( "UPDATE ezcollab_simple_message SET created=$t, modified=$t WHERE id=(SELECT message_id FROM ezcollab_item_message_link WHERE id=" . (int)$m['id'] . ")" );
        }
        $last = $times ? end( $times ) : $start;
        $db->query( "UPDATE ezcollab_item SET created=$start, modified=$last WHERE id=$itemID" );
        $db->query( "UPDATE ezcollab_item_group_link SET created=$start, modified=$last WHERE collaboration_id=$itemID" );
        $db->query( "UPDATE ezcollab_item_participant_link SET created=$start, modified=$last, last_read=0 WHERE collaboration_id=$itemID" );
        $db->query( "UPDATE ezcollab_item_status SET last_read=0, is_read=0 WHERE collaboration_id=$itemID" );

        // what the administrator has read
        $read = isset( $s['read'] ) ? $s['read'] : 'all';
        $readUntil = 0;
        if ( $read === 'all' )
            $readUntil = $last + 60;
        else if ( is_int( $read ) && $read > 0 && isset( $times[$read - 1] ) )
            $readUntil = $times[$read - 1] + 60;
        if ( $readUntil )
        {
            $db->query( "UPDATE ezcollab_item_participant_link SET last_read=$readUntil, is_read=1 WHERE collaboration_id=$itemID AND participant_id=" . (int)$adminID );
            $db->query( "UPDATE ezcollab_item_status SET last_read=$readUntil, is_read=1 WHERE collaboration_id=$itemID AND user_id=" . (int)$adminID );
        }
        // the authors have read their own item
        $db->query( "UPDATE ezcollab_item_participant_link SET last_read=" . ( $last + 60 ) . ", is_read=1 WHERE collaboration_id=$itemID AND participant_id<>" . (int)$adminID );
        $db->query( "UPDATE ezcollab_item_status SET last_read=" . ( $last + 60 ) . ", is_read=1 WHERE collaboration_id=$itemID AND user_id<>" . (int)$adminID );
        unset( $GLOBALS['eZCollaborationItemStatusCache'][$itemID] );
    }

    private function moveToGroup( $itemID, $adminID, $group )
    {
        $db = eZDB::instance();
        $db->query( "UPDATE ezcollab_item_group_link SET group_id=" . (int)$group->attribute( 'id' ) .
                    " WHERE collaboration_id=" . (int)$itemID . " AND user_id=" . (int)$adminID );
    }

    // ----------------------------------------------------------------------------------------------------------
    // Remove
    // ----------------------------------------------------------------------------------------------------------

    /** @return int[] the ids of the sample content objects */
    private function sampleObjectIDs()
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT id FROM ezcontentobject WHERE remote_id LIKE '" . $db->escapeString( self::REMOTE_PREFIX ) . "%'" );
        $ids = array();
        foreach ( $rows as $r )
            $ids[] = (int)$r['id'];
        return $ids;
    }

    /** @return int[] the ids of the sample editors */
    private function sampleUserIDs()
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT id FROM ezcontentobject WHERE remote_id LIKE '" . $db->escapeString( self::REMOTE_PREFIX . 'user-' ) . "%'" );
        $ids = array();
        foreach ( $rows as $r )
            $ids[] = (int)$r['id'];
        return $ids;
    }

    /** @return int[] the collaboration items that belong to the sample content or have a sample editor as participant */
    public function sampleItemIDs()
    {
        $db = eZDB::instance();
        $ids = array();
        $objects = $this->sampleObjectIDs();
        if ( $objects )
        {
            $rows = $db->arrayQuery( "SELECT id FROM ezcollab_item WHERE type_identifier='ezapprove' AND data_int1 IN (" . implode( ',', $objects ) . ")" );
            foreach ( $rows as $r )
                $ids[(int)$r['id']] = (int)$r['id'];
        }
        $users = $this->sampleUserIDs();
        if ( $users )
        {
            $rows = $db->arrayQuery( "SELECT DISTINCT collaboration_id FROM ezcollab_item_participant_link WHERE participant_id IN (" . implode( ',', $users ) . ")" );
            foreach ( $rows as $r )
                $ids[(int)$r['collaboration_id']] = (int)$r['collaboration_id'];
        }
        return array_values( $ids );
    }

    /** @return int[] the collaboration groups made for the sample (named "Sample: ...") and the sample editors' own groups */
    public function sampleCollaborationGroupIDs()
    {
        $db = eZDB::instance();
        $ids = array();
        $rows = $db->arrayQuery( "SELECT id FROM ezcollab_group WHERE title LIKE '" . $db->escapeString( self::NAME_PREFIX ) . "%'" );
        foreach ( $rows as $r )
            $ids[(int)$r['id']] = (int)$r['id'];
        $users = $this->sampleUserIDs();
        if ( $users )
        {
            $rows = $db->arrayQuery( "SELECT id FROM ezcollab_group WHERE user_id IN (" . implode( ',', $users ) . ")" );
            foreach ( $rows as $r )
                $ids[(int)$r['id']] = (int)$r['id'];
        }
        return array_values( $ids );
    }

    /**
     * Deletes everything apply() made and nothing else.
     *
     * @return array counts of what was removed
     */
    public function remove()
    {
        $db = eZDB::instance();
        $counts = array( 'items' => 0, 'objects' => 0, 'groups' => 0, 'workflows' => 0, 'other' => 0 );
        $admin = $this->adminUser();
        $previous = eZUser::currentUser();
        if ( $admin )
            eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ) );

        // the collaboration items first: they point at the content
        $items = $this->sampleItemIDs();
        if ( $items )
        {
            $in = implode( ',', $items );
            $db->begin();
            $db->query( "DELETE FROM ezcollab_simple_message WHERE id IN (SELECT message_id FROM ezcollab_item_message_link WHERE collaboration_id IN ($in))" );
            $db->query( "DELETE FROM ezcollab_item_message_link WHERE collaboration_id IN ($in)" );
            $db->query( "DELETE FROM ezcollab_item_participant_link WHERE collaboration_id IN ($in)" );
            $db->query( "DELETE FROM ezcollab_item_group_link WHERE collaboration_id IN ($in)" );
            $db->query( "DELETE FROM ezcollab_item_status WHERE collaboration_id IN ($in)" );
            $db->query( "DELETE FROM eznotificationevent WHERE event_type_string='ezcollaboration' AND data_int1 IN ($in)" );
            $db->query( "DELETE FROM ezapprove_items WHERE collaboration_id IN ($in)" );
            $db->query( "DELETE FROM ezcollab_item WHERE id IN ($in)" );
            $db->commit();
            $counts['items'] = count( $items );
            $this->log( 'Removed ' . count( $items ) . ' collaboration items with their messages' );
        }
        $GLOBALS['eZCollaborationItemStatusCache'] = array();

        // the collaboration groups
        $groups = $this->sampleCollaborationGroupIDs();
        if ( $groups )
        {
            $db->query( "DELETE FROM ezcollab_item_group_link WHERE group_id IN (" . implode( ',', $groups ) . ")" );
            $db->query( "DELETE FROM ezcollab_group WHERE id IN (" . implode( ',', $groups ) . ")" );
            $counts['groups'] = count( $groups );
        }
        // the administrator's profile, when the sample run made it and nothing else is in it
        if ( $admin )
        {
            $adminID = (int)$admin->attribute( 'contentobject_id' );
            $made = $db->arrayQuery( "SELECT main_group FROM ezcollab_profile WHERE user_id=$adminID AND data_text1='" . $db->escapeString( self::REMOTE_PREFIX . 'profile' ) . "'" );
            $items = $db->arrayQuery( "SELECT COUNT(*) AS n FROM ezcollab_item_group_link WHERE user_id=$adminID" );
            $groupsLeft = $db->arrayQuery( "SELECT COUNT(*) AS n FROM ezcollab_group WHERE user_id=$adminID" );
            if ( $made && (int)$items[0]['n'] === 0 && (int)$groupsLeft[0]['n'] <= 1 )
            {
                $db->query( "DELETE FROM ezcollab_group WHERE user_id=$adminID" );
                $db->query( "DELETE FROM ezcollab_profile WHERE user_id=$adminID" );
                unset( $GLOBALS["eZCollaborationProfile-$adminID"] );
                ++$counts['groups'];
            }
        }
        $users = $this->sampleUserIDs();
        if ( $users )
        {
            $db->query( "DELETE FROM ezcollab_profile WHERE user_id IN (" . implode( ',', $users ) . ")" );
            $db->query( "DELETE FROM ezcollab_notification_rule WHERE user_id IN ('" . implode( "','", $users ) . "')" );
        }

        // the workflow: its processes, trigger, events, group link and group
        $workflow = $this->findWorkflow();
        if ( $workflow )
        {
            $workflowID = (int)$workflow->attribute( 'id' );
            $processes = $db->arrayQuery( "SELECT id, memento_key FROM ezworkflow_process WHERE workflow_id=$workflowID" );
            foreach ( $processes as $p )
            {
                if ( $p['memento_key'] )
                    $db->query( "DELETE FROM ezoperation_memento WHERE memento_key='" . $db->escapeString( $p['memento_key'] ) . "'" );
                $db->query( "DELETE FROM ezworkflow_process WHERE id=" . (int)$p['id'] );
            }
            $db->begin();
            eZTrigger::removeTriggerForWorkflow( $workflowID );
            eZPersistentObject::removeObject( eZWorkflowEvent::definition(), array( 'workflow_id' => $workflowID ) );
            eZWorkflowGroupLink::removeWorkflowMembers( $workflowID, 0 );
            eZWorkflow::removeWorkflow( $workflowID, 0 );
            $db->commit();
            $counts['workflows'] = 1;
            $this->log( 'Removed the workflow ' . self::WORKFLOW_NAME . ' and its trigger' );
        }
        foreach ( eZWorkflowGroup::fetchList() as $wg )
        {
            if ( $wg->attribute( 'name' ) === self::WORKFLOW_GROUP_NAME && !eZWorkflowGroupLink::fetchWorkflowList( 0, $wg->attribute( 'id' ) ) )
            {
                eZWorkflowGroup::removeSelected( $wg->attribute( 'id' ) );
                ++$counts['other'];
            }
        }

        // the content, newest first: articles, users, group, folder; permanently (no trash)
        $objects = $this->sampleObjectIDs();
        rsort( $objects );
        foreach ( $objects as $objectID )
        {
            $object = eZContentObject::fetch( $objectID );
            if ( !$object )
                continue;
            if ( strpos( (string)$object->attribute( 'remote_id' ), self::REMOTE_PREFIX ) !== 0 )
                continue; // never anything without the marker
            eZContentObjectOperations::remove( $objectID, true );
            // a draft that was never published has no node: purge what is left
            $left = eZContentObject::fetch( $objectID );
            if ( $left )
                $left->purge();
            ++$counts['objects'];
        }
        foreach ( $objects as $objectID )
            eZContentObjectTrashNode::purgeForObject( $objectID );
        if ( $counts['objects'] )
            $this->log( 'Removed ' . $counts['objects'] . ' content objects (folder, group, editors, articles)' );

        // the section, when nothing is left in it
        $section = eZSection::fetchByIdentifier( self::SECTION_IDENTIFIER );
        if ( $section )
        {
            $left = $db->arrayQuery( "SELECT COUNT(*) AS n FROM ezcontentobject WHERE section_id=" . (int)$section->attribute( 'id' ) );
            if ( (int)$left[0]['n'] === 0 )
            {
                $section->removeThis();
                ++$counts['other'];
                $this->log( 'Removed the section ' . self::SECTION_NAME );
            }
            else
                $this->log( 'WARNING: the section ' . self::SECTION_NAME . ' still holds content that is not sample content; it was kept' );
        }

        eZUser::setCurrentlyLoggedInUser( $previous, $previous->attribute( 'contentobject_id' ) );
        eZContentCacheManager::clearAllContentCache();
        return $counts;
    }
}

/** The module the approval handler redirects with; here nothing is redirected. */
class expCollaborationSampleModule
{
    public function redirectToView( $view = '', $parameters = array() )
    {
        return true;
    }
}

?>
