<?php
/**
 * Who may see a topic, manual or chapter of help maintained as content, decided from the plain data the repository
 * keeps of each object (expHelpContentRepository::accessData(): class, section, owner, states, locations), without
 * loading the object.
 *
 * It answers what eZContentObject::canRead() and canEdit() would for the current user, for the limitations the help
 * roles use (Class, ParentClass, ParentDepth, Section, User_Section, Language, Owner, ParentOwner, State,
 * StateGroup_*, Node, Subtree, User_Subtree), the way eZContentObject::checkAccess() compares them. Where it cannot
 * be sure (a Group limitation, an extension's limitation, the anonymous "own session" owner, an object that is not
 * published, the member management guard on the object's class), it answers null and the caller asks the object.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentAccess
{
    /** @var array function name => hasAccessTo() result (accessWord, policies) */
    protected $access;

    /** @var int */
    protected $userId;

    /** @var bool */
    protected $anonymous;

    /** @var callable function( int $nodeId ): int|null, the main node id of a node (Node limitation) */
    protected $mainNodeOf;

    /** @var callable function( array $locales ): int, the language mask of locales (Language limitation) */
    protected $languageMask;

    /** @var callable function( string $functionName, int $classId ): bool, whether the member guard may apply */
    protected $guarded;

    /** @var array memo of the Node limitation's main nodes */
    protected $mainNodes = array();

    /**
     * @param array $access array( 'read' => hasAccessTo( 'content', 'read' ), 'edit' => hasAccessTo( 'content', 'edit' ) )
     * @param int $userId the user's content object id
     * @param bool $anonymous
     * @param callable|null $mainNodeOf
     * @param callable|null $languageMask
     * @param callable|null $guarded
     */
    public function __construct( array $access, $userId, $anonymous = false, $mainNodeOf = null, $languageMask = null, $guarded = null )
    {
        $this->access = $access;
        $this->userId = (int)$userId;
        $this->anonymous = (bool)$anonymous;
        $this->mainNodeOf = $mainNodeOf;
        $this->languageMask = $languageMask;
        $this->guarded = $guarded;
    }

    /**
     * The access of the current user
     *
     * @return expHelpContentAccess
     */
    public static function forCurrentUser()
    {
        $user = eZUser::currentUser();
        $access = array( 'read' => $user->hasAccessTo( 'content', 'read' ), 'edit' => $user->hasAccessTo( 'content', 'edit' ) );
        $mainNodeOf = function ( $nodeId ) {
            $node = eZContentObjectTreeNode::fetch( (int)$nodeId, false, false );
            return is_array( $node ) ? (int)$node['main_node_id'] : null;
        };
        $languageMask = function ( array $locales ) {
            return (int)eZContentLanguage::maskByLocale( $locales );
        };
        $guarded = function ( $functionName, $classId ) {
            if ( !class_exists( 'expMemberManagement' ) || !expMemberManagement::guards( $functionName ) )
                return false;
            if ( in_array( (int)$classId, array_map( 'intval', (array)eZUser::contentClassIDs() ), true ) )
                return true;
            $settings = expMemberManagement::guardSettings();
            foreach ( (array)$settings['classes'] as $identifier )
            {
                if ( (int)eZContentClass::classIDByIdentifier( $identifier ) === (int)$classId )
                    return true;
            }
            return false;
        };
        return new self( $access, (int)$user->attribute( 'contentobject_id' ), (bool)$user->isAnonymous(), $mainNodeOf, $languageMask, $guarded );
    }

    /**
     * What the user may do with a help object: 'read' (Released and readable), 'preview' (not readable as Released,
     * but editable), 'none', or null when only the object can tell (expHelpContentProvider::visible()'s rule)
     *
     * @param array $data expHelpContentRepository::accessData() of the object
     * @param bool $released whether the object is in the Released state
     * @return string|null
     */
    public function decide( array $data, $released )
    {
        if ( $released )
        {
            $read = $this->check( 'read', $data );
            if ( $read === null )
                return null;
            if ( $read === 1 )
                return 'read';
        }
        $edit = $this->check( 'edit', $data );
        if ( $edit === null )
            return null;
        return $edit === 1 ? 'preview' : 'none';
    }

    /**
     * eZContentObject::checkAccess( $functionName ) for the object of $data: 1, 0, or null when it cannot be decided
     * here
     *
     * @param string $functionName read or edit
     * @param array $data
     * @return int|null
     */
    public function check( $functionName, array $data )
    {
        if ( !isset( $this->access[$functionName]['accessWord'] ) )
            return null;
        // canEdit() lets a user edit their own account (user/selfedit), and an object that was never published is
        // decided by the create access of its parent: neither is a help object, but only the object can tell
        if ( (int)$data['id'] === $this->userId || (int)$data['status'] !== 1 /* eZContentObject::STATUS_PUBLISHED */ )
            return null;
        $word = $this->access[$functionName]['accessWord'];
        if ( $word === 'yes' )
            return 1;
        if ( $word === 'no' )
            return 0;
        // the member management guard checks the object's every location on its own
        if ( $this->guarded !== null && call_user_func( $this->guarded, $functionName, (int)$data['class_id'] ) )
            return null;
        $policies = isset( $this->access[$functionName]['policies'] ) ? (array)$this->access[$functionName]['policies'] : array();
        $access = 'denied';
        foreach ( $policies as $limitations )
        {
            if ( $access === 'allowed' )
                break;
            $access = $this->policy( $functionName, (array)$limitations, $data );
            if ( $access === null )
                return null;
        }
        return $access === 'allowed' ? 1 : 0;
    }

    /**
     * One policy of eZContentObject::checkAccess(): 'allowed', 'denied', or null when it cannot be decided here
     *
     * @param string $functionName
     * @param array $limitations identifier => values
     * @param array $data
     * @return string|null
     */
    protected function policy( $functionName, array $limitations, array $data )
    {
        $access = 'denied';
        $checkedSubtree = !isset( $limitations['Subtree'] );
        $accessSubtree = false;
        $checkedNode = !isset( $limitations['Node'] );
        $accessNode = false;
        $paths = isset( $data['locations'] ) ? (array)$data['locations'] : array();
        if ( !$paths )
            return null;
        foreach ( $limitations as $key => $values )
        {
            $values = (array)$values;
            $access = 'denied';
            switch ( $key )
            {
                case 'Class':
                case 'ParentClass':
                    // checkAccess() compares ParentClass with the object's own class, for every function but create
                    $access = in_array( $data['class_id'], $values ) ? 'allowed' : 'denied';
                    break;

                case 'ParentDepth':
                    foreach ( $paths as $location )
                    {
                        if ( in_array( $location['depth'], $values ) )
                        {
                            $access = 'allowed';
                            break;
                        }
                    }
                    break;

                case 'Section':
                case 'User_Section':
                    $access = in_array( $data['section_id'], $values ) ? 'allowed' : 'denied';
                    break;

                case 'Language':
                    // no language asked: every language of the object counts (the mask -1)
                    if ( $this->languageMask === null )
                        return null;
                    $access = ( -1 & (int)call_user_func( $this->languageMask, $values ) ) != 0 ? 'allowed' : 'denied';
                    break;

                case 'Owner':
                case 'ParentOwner':
                    if ( in_array( 2, $values ) && $this->anonymous )
                        return null;
                    $access = ( (int)$data['owner_id'] === $this->userId || (int)$data['id'] === $this->userId ) ? 'allowed' : 'denied';
                    break;

                case 'State':
                    $access = array_intersect( $values, (array)$data['state_ids'] ) ? 'allowed' : 'denied';
                    break;

                case 'Node':
                    if ( $this->mainNodeOf === null )
                        return null;
                    $accessNode = false;
                    foreach ( $values as $nodeId )
                    {
                        if ( !array_key_exists( $nodeId, $this->mainNodes ) )
                            $this->mainNodes[$nodeId] = call_user_func( $this->mainNodeOf, (int)$nodeId );
                        if ( $this->mainNodes[$nodeId] !== null && (int)$data['main_node_id'] == $this->mainNodes[$nodeId] )
                        {
                            $access = 'allowed';
                            $accessNode = true;
                            break;
                        }
                    }
                    $access = ( $access !== 'allowed' && $checkedSubtree && !$accessSubtree ) ? 'denied' : 'allowed';
                    $checkedNode = true;
                    break;

                case 'Subtree':
                    $accessSubtree = false;
                    foreach ( $paths as $location )
                    {
                        foreach ( $values as $subtree )
                        {
                            if ( strpos( (string)$location['path'], (string)$subtree ) !== false )
                            {
                                $access = 'allowed';
                                $accessSubtree = true;
                                break;
                            }
                        }
                    }
                    $access = ( $access !== 'allowed' && $checkedNode && !$accessNode ) ? 'denied' : 'allowed';
                    $checkedSubtree = true;
                    break;

                case 'User_Subtree':
                    foreach ( $paths as $location )
                    {
                        foreach ( $values as $subtree )
                        {
                            if ( strpos( (string)$location['path'], (string)$subtree ) !== false )
                                $access = 'allowed';
                        }
                    }
                    break;

                default:
                    if ( strncmp( $key, 'StateGroup_', 11 ) !== 0 )
                        return null; // Group, ParentGroup, an extension's limitation: the object decides
                    $access = array_intersect( $values, (array)$data['state_ids'] ) ? 'allowed' : 'denied';
            }
            if ( $access === 'denied' )
                break;
        }
        return $access;
    }
}

?>
