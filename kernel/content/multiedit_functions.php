<?php
/**
 * The working parts of content/multiedit.
 *
 * Kept beside the view rather than in kernel/classes because none of it is
 * useful to anything else: it is the assembly around the single object editing
 * machinery, not a new piece of machinery.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

class eZMultiEdit
{
    /// How many objects one form will carry. A selection larger than this is
    /// refused rather than served: every object is a draft, a form section and
    /// a publish, and a page of five hundred of them helps nobody.
    const MAX_OBJECTS = 50;

    /**
     * The objects a caller selected, as object ids.
     *
     * Three callers post three different things - the sub items list sends node
     * ids under DeleteIDArray, which is the name it uses for every bulk action;
     * the search results send node ids of their own; anything else may send
     * object ids. All of them are accepted so that no caller has to know what
     * the others do.
     *
     * @param eZHTTPTool $http
     * @return array of object id, in the order selected, without duplicates.
     */
    static function selectionFromRequest( $http )
    {
        $objectIDs = array();

        foreach ( array( 'ContentObjectIDArray', 'MultiEditObjectIDArray' ) as $name )
            foreach ( self::idsIn( $http, $name ) as $id )
                $objectIDs[] = (int) $id;

        $nodeIDs = array();
        foreach ( array( 'ContentNodeIDArray', 'MultiEditNodeIDArray', 'DeleteIDArray' ) as $name )
            foreach ( self::idsIn( $http, $name ) as $id )
                $nodeIDs[] = (int) $id;

        foreach ( array_unique( $nodeIDs ) as $nodeID )
        {
            $node = eZContentObjectTreeNode::fetch( $nodeID );
            if ( $node instanceof eZContentObjectTreeNode )
                $objectIDs[] = (int) $node->attribute( 'contentobject_id' );
        }

        $objectIDs = array_values( array_unique( array_filter( $objectIDs ) ) );

        return array_slice( $objectIDs, 0, self::MAX_OBJECTS );
    }

    /**
     * One post variable as a list of ids, whatever shape it arrived in.
     *
     * @param eZHTTPTool $http
     * @param string $name
     * @return array
     */
    static function idsIn( $http, $name )
    {
        if ( !$http->hasPostVariable( $name ) )
            return array();

        $value = $http->postVariable( $name );

        return is_array( $value ) ? $value : array( $value );
    }

    /**
     * The drafts this form is already working on, objectID => version.
     *
     * Carried in the form rather than in the session: a reader with two tabs
     * open is editing two selections, and a session would give them one.
     *
     * @param eZHTTPTool $http
     * @return array
     */
    static function draftsFromRequest( $http )
    {
        if ( !$http->hasPostVariable( 'MultiEditDraft' ) )
            return array();

        $drafts = $http->postVariable( 'MultiEditDraft' );

        if ( !is_array( $drafts ) )
            return array();

        $map = array();
        foreach ( $drafts as $objectID => $version )
            $map[(int) $objectID] = (int) $version;

        return $map;
    }

    /**
     * Opens a draft for each object, or says why it could not.
     *
     * An object the reader may not edit, or that somebody else is already
     * editing, is left out with a reason rather than silently dropped: a
     * selection of twelve that quietly becomes a form of nine is worse than one
     * that says which three are missing and why.
     *
     * @param array $objectIDs
     * @param string|false $language
     * @param array $existing objectID => version already opened by this form.
     * @return array( 'editable' => ..., 'refused' => ... )
     */
    static function openDrafts( $objectIDs, $language, $existing = array() )
    {
        $editable = array();
        $refused  = array();

        foreach ( $objectIDs as $objectID )
        {
            $object = eZContentObject::fetch( $objectID );

            if ( !$object instanceof eZContentObject )
            {
                $refused[$objectID] = array( 'name' => '#' . $objectID, 'reason' => 'gone' );
                continue;
            }

            $name = $object->attribute( 'name' );

            if ( !$object->attribute( 'can_edit' ) )
            {
                $refused[$objectID] = array( 'name' => $name, 'reason' => 'no-permission' );
                continue;
            }

            $editLanguage = $language !== false ? $language : $object->attribute( 'initial_language_code' );

            // Reuse the draft this form already opened rather than stacking a
            // new version on every submit.
            $version = false;
            if ( isset( $existing[$objectID] ) )
            {
                // The version number came from the request, so it is checked
                // the same way a discard is: a draft, on an object this reader
                // may edit, and theirs. Otherwise a posted number could hand
                // this form somebody else's unfinished work to edit and
                // publish under their name.
                $candidate = $object->version( $existing[$objectID] );

                if ( self::mayUseDraft( $object, $candidate ) )
                    $version = $candidate;
                else
                {
                    $refused[$objectID] = array( 'name' => $name, 'reason' => 'no-draft' );
                    continue;
                }
            }

            if ( !$version )
            {
                $version = $object->createNewVersionIn( $editLanguage, false, false, true,
                                                        eZContentObjectVersion::STATUS_INTERNAL_DRAFT );
            }

            if ( !$version instanceof eZContentObjectVersion )
            {
                $refused[$objectID] = array( 'name' => $name, 'reason' => 'no-draft' );
                continue;
            }

            $attributes = $version->contentObjectAttributes( $editLanguage );

            // A language the object does not carry gives an empty set; fall
            // back to what it does have rather than drawing nothing.
            if ( !is_array( $attributes ) || count( $attributes ) === 0 )
            {
                $attributes   = $version->contentObjectAttributes();
                $editLanguage = $version->initialLanguageCode();
            }

            $class = eZContentClass::fetch( $object->attribute( 'contentclass_id' ) );

            if ( !$class instanceof eZContentClass )
            {
                $refused[$objectID] = array( 'name' => $name, 'reason' => 'no-class' );
                continue;
            }

            $editable[$objectID] = array(
                'object'     => $object,
                'version'    => $version,
                'class'      => $class,
                'language'   => $editLanguage,
                'attributes' => $attributes,
                // The admin's edit_attribute.tpl draws from the grouped map,
                // not from the flat list - it is what puts attributes into
                // their content.ini categories - so it is built here, once per
                // object, exactly as content/attribute_edit builds it for one.
                'grouped'    => eZContentObject::createGroupedDataMap( $attributes ) );
        }

        return array( 'editable' => $editable, 'refused' => $refused );
    }

    /**
     * Creates several new objects of one class under one parent, as drafts.
     *
     * The same form then edits them: a new object and an existing one are both
     * a draft with a set of attributes, and once they are open there is nothing
     * left to tell apart. Only the getting there differs, which is all this
     * does - instantiate, place, and leave in draft for the form to fill in.
     *
     * Nothing is published here. A row created and left unfilled is a draft
     * nobody sees, which is the right thing for a form the reader may abandon.
     *
     * @param int|string $class the class id, or its identifier.
     * @param int $count how many to make.
     * @param int $parentNodeID where they go.
     * @param string|false $language
     * @return array( 'created' => array of object id, 'error' => string|false )
     */
    static function createDrafts( $class, $count, $parentNodeID, $language = false )
    {
        $count = (int) $count;

        if ( $count < 1 )
            return array( 'created' => array(), 'error' => 'count' );

        if ( $count > self::MAX_OBJECTS )
            $count = self::MAX_OBJECTS;

        // The chooser sends an id, because canCreateClassList() - which is what
        // decides who may create what here - only gives out ids and names. An
        // identifier is accepted too, for anything calling this directly.
        $class = is_numeric( $class )
                 ? eZContentClass::fetch( (int) $class )
                 : eZContentClass::fetchByIdentifier( $class );

        if ( !$class instanceof eZContentClass )
            return array( 'created' => array(), 'error' => 'class' );

        $parent = eZContentObjectTreeNode::fetch( (int) $parentNodeID );

        if ( !$parent instanceof eZContentObjectTreeNode )
            return array( 'created' => array(), 'error' => 'parent' );

        // The same check the ordinary Create here menu makes: may this reader
        // put this class in this place.
        if ( !$parent->checkAccess( 'create', $class->attribute( 'id' ) ) )
            return array( 'created' => array(), 'error' => 'permission' );

        $user      = eZUser::currentUser();
        $creatorID = (int) $user->attribute( 'contentobject_id' );
        $sectionID = (int) $parent->attribute( 'object' )->attribute( 'section_id' );
        $created   = array();

        $db = eZDB::instance();
        $db->begin();

        for ( $i = 0; $i < $count; $i++ )
        {
            $object = $class->instantiate( $creatorID, $sectionID, false, $language ?: false );

            if ( !$object instanceof eZContentObject )
                continue;

            $object->store();

            $assignment = eZNodeAssignment::create(
                array( 'contentobject_id'      => $object->attribute( 'id' ),
                       'contentobject_version' => $object->attribute( 'current_version' ),
                       'parent_node'           => (int) $parentNodeID,
                       'is_main'               => 1,
                       'sort_field'            => $class->attribute( 'sort_field' ),
                       'sort_order'            => $class->attribute( 'sort_order' ) ) );
            $assignment->store();

            // Internal, like the drafts openDrafts() makes: it keeps a form
            // that is abandoned out of the author's draft list.
            $version = $object->version( 1 );
            $version->setAttribute( 'modified', eZDateTime::currentTimeStamp() );
            $version->setAttribute( 'status', eZContentObjectVersion::STATUS_INTERNAL_DRAFT );
            $version->store();

            // objectID => version, not just the id. The caller has to hand
            // these to openDrafts() as drafts it already owns, or that method
            // opens a *second* version - and the node assignment lives on the
            // first one, so the second publishes with nowhere to go and the
            // object ends up with no location at all.
            $created[(int) $object->attribute( 'id' )] = (int) $version->attribute( 'version' );
        }

        $db->commit();

        return array( 'created' => $created, 'error' => false );
    }

    /**
     * The classes this reader may create under a node, for the chooser.
     *
     * @param int $parentNodeID
     * @return array of array( 'identifier' => ..., 'name' => ... )
     */
    static function creatableClasses( $parentNodeID )
    {
        $parent = eZContentObjectTreeNode::fetch( (int) $parentNodeID );

        if ( !$parent instanceof eZContentObjectTreeNode )
            return array();

        $classes = array();

        // id and name is all this gives, and all that is needed: it is the
        // same list the Create here menu is built from, so it already answers
        // "may this reader put this class in this place".
        foreach ( (array) $parent->canCreateClassList() as $class )
            $classes[] = array( 'id'   => (int) $class['id'],
                                'name' => $class['name'] );

        usort( $classes, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ); } );

        return $classes;
    }

    /**
     * The editable set arranged by content class.
     *
     * Objects of one class share a set of fields, so grouping them is what
     * turns the form from a wall into something with a shape. A selection
     * spanning several classes is not refused - it is drawn as one section per
     * class, in the order the classes were first met.
     *
     * @param array $editable
     * @return array of array( 'class' => ..., 'identifier' => ..., 'objects' => ... )
     */
    static function groupByClass( $editable )
    {
        $groups = array();

        foreach ( $editable as $objectID => $entry )
        {
            $classID = (int) $entry['class']->attribute( 'id' );

            if ( !isset( $groups[$classID] ) )
                $groups[$classID] = array( 'class'      => $entry['class'],
                                           'identifier' => $entry['class']->attribute( 'identifier' ),
                                           'name'       => $entry['class']->attribute( 'name' ),
                                           'objects'    => array() );

            $groups[$classID]['objects'][] = array( 'object_id'  => $objectID,
                                                    'object'     => $entry['object'],
                                                    'version'    => $entry['version'],
                                                    'language'   => $entry['language'],
                                                    'attributes' => $entry['attributes'],
                                                    'grouped'    => $entry['grouped'] );
        }

        return array_values( $groups );
    }

    /**
     * objectID => version, for the hidden fields that carry the drafts back.
     *
     * @param array $editable
     * @return array
     */
    static function draftMap( $editable )
    {
        $map = array();

        foreach ( $editable as $objectID => $entry )
            $map[] = array( 'object_id' => $objectID,
                            'version'   => $entry['version']->attribute( 'version' ) );

        return $map;
    }

    /**
     * Validates and stores what was typed, for every object.
     *
     * The sequence per object is the one content/edit uses for its single
     * object - validate, fix up, fetch, store - so a datatype behaves here
     * exactly as it does there. What differs is that a failure is recorded
     * against its object and the others carry on, rather than the whole
     * request stopping at the first one.
     *
     * @param eZModule $module
     * @param eZHTTPTool $http
     * @param array $editable
     * @param string $base
     * @param array $validationParameters
     * @return array( 'all-valid' => bool, 'validation' => objectID => array )
     */
    static function storeAll( $module, $http, $editable, $base, $validationParameters )
    {
        $allValid   = true;
        $validation = array();

        // Extensions hook editing through these, and a form that does not call
        // them silently bypasses whatever they enforce or fill in. The single
        // object editor calls them; so does this.
        eZContentObjectEditHandler::initialize();

        // A custom action - "Find object" on a relation, an upload on a file -
        // names the attribute it belongs to, and attribute ids are unique, so
        // one parse serves every object in the form and each only sees its own.
        $customActions = self::customActions( $http );

        foreach ( $editable as $objectID => $entry )
        {
            $object     = $entry['object'];
            $version    = $entry['version'];
            $attributes = $entry['attributes'];
            $class      = $entry['class'];
            $language   = $entry['language'];
            $versionNo  = $version->attribute( 'version' );

            $result = $object->validateInput( $attributes, $base, false, $validationParameters );

            if ( $result['require-fixup'] )
                $object->fixupInput( $attributes, $base );

            $objectValid = $result['input-validated'];

            // What the extensions make of it, on the same terms.
            $custom = eZContentObjectEditHandler::validateInputHandlers(
                $module, $class, $object, $version, $attributes,
                $versionNo, $language, false, $validationParameters );

            if ( !$custom['validated'] )
                $objectValid = false;

            if ( !$objectValid )
            {
                $allValid = false;

                $unvalidated = $result['unvalidated-attributes'];

                // An extension's complaint has no attribute to hang on, so it
                // is shown as a line of its own rather than dropped. The shape
                // of a warning is whatever the handler returned, so a plain
                // string is read as readily as the usual name/text pair.
                foreach ( (array) $custom['warnings'] as $warning )
                {
                    if ( is_string( $warning ) )
                        $warning = array( 'text' => $warning );

                    if ( !is_array( $warning ) )
                        continue;

                    $unvalidated[] = array(
                        'id'          => 0,
                        'identifier'  => '',
                        'name'        => isset( $warning['name'] ) ? $warning['name'] : '',
                        'description' => isset( $warning['text'] ) ? $warning['text'] : '' );
                }

                $validation[$objectID] = array(
                    'name'       => $object->attribute( 'name' ),
                    'attributes' => $unvalidated );
            }

            eZContentObjectEditHandler::executeInputHandlers(
                $module, $class, $object, $version, $attributes,
                $versionNo, $language, false );

            $fetched = $object->fetchInput( $attributes, $base, $customActions,
                                            array( 'module' => $module,
                                                   'current-redirection-uri' =>
                                                       $module->redirectionURI( 'content', 'multiedit' ) ) );

            $inputMap = $fetched['attribute-input-map'];

            if ( empty( $inputMap ) )
                continue;

            $db = eZDB::instance();
            $db->begin();

            // Only a version that validated becomes a real draft; the rest stay
            // internal, which is what keeps a half filled form out of the
            // author's draft list.
            if ( $objectValid )
                $version->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );

            $version->setAttribute( 'modified', time() );
            $version->store();

            $object->storeInput( $attributes, $inputMap );

            $db->commit();

            ezpEvent::getInstance()->notify( 'content/cache/version',
                                             array( $objectID, $version->attribute( 'version' ) ) );
        }

        return array( 'all-valid' => $allValid, 'validation' => $validation );
    }

    /**
     * The custom actions this request is carrying, keyed by attribute id.
     *
     * A datatype asks for one of these when it needs a round trip of its own -
     * "Find object" on a relation, an upload on a file field - by posting
     * CustomActionButton[<attributeID>_<action>]. The attribute id in the name
     * is what makes this safe for a form holding many objects: each object's
     * fetchInput() is handed the whole map and takes only the entries whose
     * ids belong to it.
     *
     * @param eZHTTPTool $http
     * @return array attributeID => array( 'id' => ..., 'value' => ... )
     */
    static function customActions( $http )
    {
        $actions = array();

        if ( !$http->hasPostVariable( 'CustomActionButton' ) )
            return $actions;

        $posted = $http->postVariable( 'CustomActionButton' );

        if ( !is_array( $posted ) )
            return $actions;

        foreach ( array_keys( $posted ) as $key )
        {
            if ( !preg_match( '#^([0-9]+)_(.*)$#', $key, $match ) )
                continue;

            $actions[$match[1]] = array( 'id' => $match[1], 'value' => $match[2] );
        }

        return $actions;
    }

    /**
     * Publishes every draft, and reports what became of each.
     *
     * Publishing several objects is not one act and must not be shown as one.
     * Each is its own operation, and any of them can fail its own validation or
     * be suspended by a workflow - an approve event, for instance - so the
     * answer is a list, not a yes.
     *
     * @param array $editable
     * @return array( 'results' => ..., 'published' => int, 'failed' => int, 'pending' => int )
     */
    static function publishAll( $editable )
    {
        $results   = array();
        $published = $failed = $pending = 0;

        foreach ( $editable as $objectID => $entry )
        {
            $version = (int) $entry['version']->attribute( 'version' );
            $name    = $entry['object']->attribute( 'name' );

            $operation = eZOperationHandler::execute( 'content', 'publish',
                                                     array( 'object_id' => $objectID,
                                                            'version'   => $version ) );

            $status = isset( $operation['status'] ) ? $operation['status'] : false;

            if ( $status == eZModuleOperationInfo::STATUS_CONTINUE )
            {
                $published++;
                $state = 'published';
            }
            else if ( $status == eZModuleOperationInfo::STATUS_HALTED ||
                      $status == eZModuleOperationInfo::STATUS_CANCELLED )
            {
                // A workflow took it: it is not published and not failed, and
                // saying either would be a lie.
                $pending++;
                $state = 'pending';
            }
            else
            {
                $failed++;
                $state = 'failed';
            }

            $results[] = array( 'object_id' => $objectID,
                                'name'      => $name,
                                'version'   => $version,
                                'state'     => $state );
        }

        return array( 'results' => $results, 'published' => $published,
                      'failed'  => $failed,  'pending'   => $pending );
    }

    /**
     * Throws away the drafts this form opened.
     *
     * @param array $drafts objectID => version.
     */
    static function discardDrafts( $drafts )
    {
        foreach ( $drafts as $objectID => $versionNumber )
        {
            $object = eZContentObject::fetch( (int) $objectID );

            if ( !$object instanceof eZContentObject )
                continue;

            $version = $object->version( (int) $versionNumber );

            // Every one of these has to hold, and the object/version pair comes
            // from the request: without the checks this deletes any draft on
            // the site by naming its id, whether or not the reader may touch
            // the object and whether or not the draft is theirs.
            if ( !self::mayUseDraft( $object, $version ) )
                continue;

            $version->removeThis();
        }
    }

    /**
     * Whether this reader may work on this draft.
     *
     * Three things, and the reason for each:
     *
     *  - it is a draft. Removing or editing a published version here would be
     *    a way to lose content by pressing a button meant to be harmless.
     *  - the reader may edit the object. The module's own policy check only
     *    says they may edit *something*.
     *  - the draft is their own. Somebody else's unfinished work is not for
     *    this form to adopt, discard, or publish under their name; the single
     *    object editor offers a version chooser for that, and this one refuses.
     *
     * The object and version both arrive as ids in the request, so nothing here
     * can be taken on trust.
     *
     * @param eZContentObject $object
     * @param eZContentObjectVersion|null $version
     * @return bool
     */
    static function mayUseDraft( $object, $version )
    {
        if ( !$version instanceof eZContentObjectVersion )
            return false;

        if ( !in_array( (int) $version->attribute( 'status' ),
                        array( eZContentObjectVersion::STATUS_DRAFT,
                               eZContentObjectVersion::STATUS_INTERNAL_DRAFT ), true ) )
            return false;

        if ( !$object->attribute( 'can_edit' ) )
            return false;

        return (int) $version->attribute( 'creator_id' ) === (int) eZUser::currentUserID();
    }

    /**
     * Where to go when the form is finished with.
     *
     * The caller says where it came from; anything else would land the reader
     * somewhere they did not start.
     *
     * @param eZHTTPTool $http
     * @return string
     */
    static function returnURI( $http )
    {
        if ( $http->hasPostVariable( 'MultiEditReturnURI' ) )
        {
            $uri = trim( (string) $http->postVariable( 'MultiEditReturnURI' ) );

            // Somewhere on this site, not wherever a posted field says.
            //
            // One leading slash and no second one is not enough on its own:
            // browsers read "/\evil.example" as protocol relative too, and a
            // newline or a tab inside the value can carry past a naive check
            // and into a header. So control characters go, backslashes are
            // refused outright, and what is left must begin with a single
            // slash followed by something that is not another separator.
            $uri = preg_replace( '/[\x00-\x1F\x7F]/', '', $uri );

            if ( $uri !== '' && strpos( $uri, '\\' ) === false &&
                 preg_match( '#^/(?![/\\\\])#', $uri ) )
                return $uri;
        }

        return '/content/dashboard';
    }

    /**
     * The language the form is editing in.
     *
     * @param array $editable
     * @param string|false $requested
     * @return string
     */
    static function languageOf( $editable, $requested )
    {
        if ( $requested !== false )
            return $requested;

        foreach ( $editable as $entry )
            return $entry['language'];

        return eZContentObject::defaultLanguage();
    }

    /**
     * The breadcrumb for every exit from this view.
     *
     * @return array
     */
    static function path()
    {
        return array( array( 'url'  => false,
                             'text' => ezpI18n::tr( 'kernel/content', 'Edit several items' ) ) );
    }
}
