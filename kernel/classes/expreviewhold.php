<?php
/**
 * File containing the expReviewHold class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The review hold (doc/eng-us/features/6.0/review-hold.md): an object state that stops editing and publishing of an
 * object while a reviewer (legal, compliance, a client) looks at it, and lets them release it again.
 *
 * - The object state group review_hold with the states not_held (the first, so every object and every new one has
 *   it) and held.
 * - Editing: every content/edit and content/publish policy carries the limitation StateGroup_review_hold = not_held,
 *   so the roles' own policies refuse to edit a held object. A policy that already names the group is left as it is
 *   (a role that may edit held objects says so with both states). Module-wide policies (content/*, * / *) cannot carry
 *   a limitation and are not changed: administrators keep editing.
 * - Publishing: while an object is held, publishing a version of it needs content/publish allowing the held state
 *   (refusesPublish(), the step check-review-hold of the publish operation); without it the publication is cancelled
 *   and the version stays a draft. This covers every way a version gets published: the edit view, a scheduled or
 *   approved publication resumed by the workflow, an extension calling the operation.
 * - Placing and releasing a hold is state/assign of the group; the role "Review hold" gives both.
 * - Audited by the kernel as content.object.state (who assigned which state); a refused publication is
 *   content.object.hold.refused.
 *
 * The upgrade step 6.0.15-review-hold (expUpgradeStepReviewHold) creates the group, its states and the role and adds
 * the limitation to the policies; nothing changes until it runs.
 *
 * @since 6.0.15
 * @package kernel
 */
class expReviewHold
{
    const GROUP = 'review_hold';
    const STATE_FREE = 'not_held';
    const STATE_HELD = 'held';
    const LIMITATION = 'StateGroup_review_hold';
    const ROLE = 'Review hold';

    /** The functions whose policies carry the limitation */
    const FUNCTIONS = array( 'edit', 'publish' );

    /**
     * The states, in priority order (the first is the default of every object).
     *
     * @return array identifier => array( name, description )
     */
    public static function states()
    {
        return array(
            self::STATE_FREE => array( 'Not held', 'Can be edited and published as the roles allow' ),
            self::STATE_HELD => array( 'Held for review', 'Editing and publishing wait until a reviewer releases it' ),
        );
    }

    /**
     * The policies of the role "Review hold": place and release a hold anywhere. Symbolic state identifiers.
     *
     * @return array list of array( module, function, limitations )
     */
    public static function rolePolicies()
    {
        $all = array_keys( self::states() );
        return array( array( 'state', 'assign', array( self::LIMITATION => $all, 'NewState' => $all ) ) );
    }

    /**
     * The state group, or null while the upgrade step has not created it.
     *
     * @return eZContentObjectStateGroup|null
     */
    public static function group()
    {
        $group = eZContentObjectStateGroup::fetchByIdentifier( self::GROUP );
        return $group instanceof eZContentObjectStateGroup ? $group : null;
    }

    /**
     * A state of the group by identifier, or null.
     *
     * @param string $identifier
     * @return eZContentObjectState|null
     */
    public static function state( $identifier )
    {
        $group = self::group();
        $state = $group ? $group->stateByIdentifier( (string)$identifier ) : null;
        return $state instanceof eZContentObjectState ? $state : null;
    }

    /**
     * Whether an object is held (false while there is no group).
     *
     * @param eZContentObject $object
     * @return bool
     */
    public static function isHeld( eZContentObject $object )
    {
        $held = self::state( self::STATE_HELD );
        return $held !== null && in_array( (int)$held->attribute( 'id' ), array_map( 'intval', (array)$object->attribute( 'state_id_array' ) ), true );
    }

    /**
     * Whether the current user may not publish a version of this object: it is held and the user's content/publish
     * policies do not allow it (administrators with a module-wide policy may).
     *
     * @param eZContentObject $object
     * @return bool
     */
    public static function refusesPublish( eZContentObject $object )
    {
        return self::isHeld( $object ) && !$object->checkAccess( 'publish' );
    }

    /**
     * The step check-review-hold of the publish operation (kernel/content/operation_definition.php): cancels the
     * publication of a held object for a user without content/publish for it. The version goes back to a draft so
     * it can be published once the hold is released.
     *
     * @param int $objectID
     * @param int $versionNum
     * @return array|null array( 'status' => cancelled ), or null to go on
     */
    public static function checkPublish( $objectID, $versionNum )
    {
        $object = eZContentObject::fetch( (int)$objectID );
        if ( !$object instanceof eZContentObject || !self::refusesPublish( $object ) )
            return null;
        $version = $object->version( (int)$versionNum );
        if ( $version instanceof eZContentObjectVersion && (int)$version->attribute( 'status' ) === eZContentObjectVersion::STATUS_PENDING )
        {
            $version->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );
            $version->store();
        }
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( 'content.object.hold.refused', array( 'object' => expAuditHook::object( $object ),
                'target' => array( 'type' => 'version', 'id' => (int)$versionNum ), 'verb' => 'refuse' ) );
        eZDebug::writeNotice( "Object $objectID is held for review: version $versionNum is not published", __METHOD__ );
        return array( 'status' => eZModuleOperationInfo::STATUS_CANCELLED );
    }

    /**
     * The policies the limitation is still missing from. Pure: roles as data.
     *
     * @param array $roles list of array( 'id' => int, 'name' => string, 'policies' => list of array( 'id' => int,
     *                     'module' => string, 'function' => string, 'limitations' => array( identifier => values ) ) )
     * @return array array( 'limit' => list of array( role id, role name, policy id, function ), 'unlimited' => list of
     *               array( role id, role name, module/function ) the limitation cannot be added to )
     */
    public static function policyPlan( array $roles )
    {
        $limit = array();
        $unlimited = array();
        foreach ( $roles as $role )
        {
            foreach ( isset( $role['policies'] ) ? (array)$role['policies'] : array() as $policy )
            {
                $module = (string)$policy['module'];
                $function = (string)$policy['function'];
                if ( $module === '*' || ( $module === 'content' && $function === '*' ) )
                {
                    $unlimited[] = array( (int)$role['id'], (string)$role['name'], $module . '/' . $function );
                    continue;
                }
                if ( $module !== 'content' || !in_array( $function, self::FUNCTIONS, true ) )
                    continue;
                $limitations = isset( $policy['limitations'] ) ? (array)$policy['limitations'] : array();
                if ( array_key_exists( self::LIMITATION, $limitations ) )
                    continue;
                $limit[] = array( (int)$role['id'], (string)$role['name'], (int)$policy['id'], $function );
            }
        }
        return array( 'limit' => $limit, 'unlimited' => $unlimited );
    }

    /**
     * The roles of the installation as policyPlan() reads them.
     *
     * @return array
     */
    public static function roleData()
    {
        $roles = array();
        foreach ( (array)eZRole::fetchList() as $role )
        {
            $policies = array();
            foreach ( (array)$role->policyList() as $policy )
            {
                $limitations = array();
                foreach ( (array)$policy->limitationList( false ) as $limitation )
                    $limitations[(string)$limitation->attribute( 'identifier' )] = (array)$limitation->attribute( 'values_as_array' );
                $policies[] = array( 'id' => (int)$policy->attribute( 'id' ), 'module' => (string)$policy->attribute( 'module_name' ),
                                     'function' => (string)$policy->attribute( 'function_name' ), 'limitations' => $limitations );
            }
            $roles[] = array( 'id' => (int)$role->attribute( 'id' ), 'name' => (string)$role->attribute( 'name' ), 'policies' => $policies );
        }
        return $roles;
    }

    /**
     * What the upgrade step still has to do, one line each (empty: in place).
     *
     * @return string[]
     */
    public static function changes()
    {
        $lines = array();
        $group = self::group();
        if ( !$group )
            $lines[] = 'create the object state group ' . self::GROUP . ' (its first state, ' . self::STATE_FREE . ', goes to every object)';
        foreach ( array_keys( self::states() ) as $identifier )
            if ( !$group || !self::state( $identifier ) )
                $lines[] = 'create the state ' . self::GROUP . '/' . $identifier;
        $plan = self::policyPlan( self::roleData() );
        foreach ( $plan['limit'] as $entry )
            $lines[] = sprintf( 'limit content/%s of the role %s (%d, policy %d) to %s/%s', $entry[3], $entry[1], $entry[0], $entry[2], self::GROUP, self::STATE_FREE );
        if ( !eZRole::fetchByName( self::ROLE ) )
            $lines[] = 'create the role "' . self::ROLE . '" (state/assign of ' . self::GROUP . '; assigned to nobody)';
        $unlinked = $group ? self::unlinkedPlan() : null;
        if ( $unlinked !== null && $unlinked['missing'] > 0 )
            $lines[] = sprintf( 'give %d object(s) without a state of %s the state %s/%s', $unlinked['missing'], self::GROUP, self::GROUP, self::STATE_FREE );
        return $lines;
    }

    /**
     * The objects that have no state of the group: the entry of the group in the plan of bin/php/adddefaultstates.php
     * (every database engine), or null when the group or its default state is missing. A state group links every
     * object that exists when its first state is stored; an object created afterwards by a process that had read the
     * default states before the group existed (an installer that ran the step in a process of its own) got none.
     *
     * @return array|null group_id, group_identifier, state_id, state_identifier, missing (count) and, on MongoDB, missing_ids
     */
    protected static function unlinkedPlan()
    {
        if ( !class_exists( '\\Exponential\\Command\\Kernel\\Adddefaultstates' ) )
            return null;
        foreach ( \Exponential\Command\Kernel\Adddefaultstates::plan( eZDB::instance() ) as $entry )
            if ( $entry['group_identifier'] === self::GROUP && $entry['state_identifier'] === self::STATE_FREE )
                return $entry;
        return null;
    }

    /**
     * Notes for the step's output: the module-wide policies a hold does not stop.
     *
     * @return string[]
     */
    public static function notes()
    {
        $notes = array();
        foreach ( self::policyPlan( self::roleData() )['unlimited'] as $entry )
            $notes[] = sprintf( 'the role %s (%d) has %s: a hold does not stop it', $entry[1], $entry[0], $entry[2] );
        return $notes;
    }

    /**
     * Limits every content/edit and content/publish policy that lacks it to review_hold/not_held. For code that adds
     * such policies to a role after the review hold is in place (member management): without it the new policy edits
     * held objects, and the review hold step reports drift. Nothing to do when the review hold is not installed.
     * Idempotent.
     *
     * @return string[] what was done
     */
    public static function limitPolicies()
    {
        $free = self::group() ? self::state( self::STATE_FREE ) : null;
        if ( !$free )
            return array();
        $done = array();
        foreach ( self::policyPlan( self::roleData() )['limit'] as $entry )
        {
            $policy = eZPolicy::fetch( $entry[2] );
            if ( !$policy instanceof eZPolicy )
                continue;
            $policy->appendLimitation( self::LIMITATION, array( (int)$free->attribute( 'id' ) ) );
            $done[] = sprintf( 'limited content/%s of the role %s (%d, policy %d) to %s/%s', $entry[3], $entry[1], $entry[0], $entry[2], self::GROUP, self::STATE_FREE );
        }
        if ( $done )
            eZRole::expireCache();
        return $done;
    }

    /**
     * Makes the changes; idempotent.
     *
     * @param string|null $locale the language of the names (default: the top priority language)
     * @return string[] what was done
     */
    public static function apply( $locale = null )
    {
        $done = array();
        if ( $locale === null )
        {
            $top = eZContentLanguage::topPriorityLanguage();
            $locale = $top instanceof eZContentLanguage ? (string)$top->attribute( 'locale' ) : 'eng-GB';
        }
        $group = self::group();
        if ( !$group )
        {
            $group = new eZContentObjectStateGroup();
            $group->setAttribute( 'identifier', self::GROUP );
            $translation = $group->translationByLocale( $locale );
            if ( !$translation )
                throw new RuntimeException( "the language $locale is not available for the state group" );
            $group->setAttribute( 'default_language_id', (int)$translation->realLanguageID() );
            $translation->setAttribute( 'name', 'Review hold' );
            $translation->setAttribute( 'description', 'A held object is not edited or published until a reviewer releases it' );
            $group->store();
            $group = self::group();
            if ( !$group )
                throw new RuntimeException( 'the state group ' . self::GROUP . ' was not stored' );
            $done[] = 'created the object state group ' . self::GROUP;
        }
        foreach ( self::states() as $identifier => $texts )
        {
            if ( $group->stateByIdentifier( $identifier ) instanceof eZContentObjectState )
                continue;
            $state = $group->newState( $identifier );
            $translation = $state->translationByLocale( $locale );
            $state->setAttribute( 'default_language_id', (int)$translation->realLanguageID() );
            $translation->setAttribute( 'name', $texts[0] );
            $translation->setAttribute( 'description', $texts[1] );
            $state->store();
            $group = self::group();
            $done[] = 'created the state ' . self::GROUP . '/' . $identifier;
        }
        eZContentObjectState::cleanDefaultsCache();
        eZExpiryHandler::instance()->setTimestamp( 'state-limitations', time() );
        eZExpiryHandler::instance()->store();

        $free = self::state( self::STATE_FREE );
        $held = self::state( self::STATE_HELD );
        if ( !$free || !$held )
            throw new RuntimeException( 'the states of ' . self::GROUP . ' are missing' );
        $ids = array( self::STATE_FREE => (int)$free->attribute( 'id' ), self::STATE_HELD => (int)$held->attribute( 'id' ) );

        $done = array_merge( $done, self::limitPolicies() );

        if ( !eZRole::fetchByName( self::ROLE ) )
        {
            $role = eZRole::create( self::ROLE );
            $role->store();
            foreach ( self::rolePolicies() as $policy )
            {
                $limitations = array();
                foreach ( $policy[2] as $identifier => $symbols )
                    $limitations[$identifier] = array_map( function ( $symbol ) use ( $ids ) { return $ids[$symbol]; }, $symbols );
                $role->appendPolicy( $policy[0], $policy[1], $limitations );
            }
            $role->store();
            $done[] = 'created the role "' . self::ROLE . '" (assigned to nobody)';
        }
        eZRole::expireCache();

        $unlinked = self::unlinkedPlan();
        if ( $unlinked !== null && $unlinked['missing'] > 0 )
        {
            self::linkUnlinked( eZDB::instance(), $unlinked );
            $done[] = sprintf( 'gave %d object(s) without a state of %s the state %s/%s', $unlinked['missing'], self::GROUP, self::GROUP, self::STATE_FREE );
        }
        return $done;
    }

    /**
     * Links every object of unlinkedPlan() to the group's default state, in one transaction; idempotent (an object
     * with a state of the group is never touched).
     *
     * @param eZDBInterface $db
     * @param array $entry unlinkedPlan()
     */
    protected static function linkUnlinked( $db, array $entry )
    {
        $stateID = (int)$entry['state_id'];
        $groupID = (int)$entry['group_id'];
        $db->begin();
        try
        {
            if ( $db->databaseName() === 'mongo' )
            {
                foreach ( isset( $entry['missing_ids'] ) ? $entry['missing_ids'] : array() as $objectID )
                    $db->insert( 'ezcobj_state_link', array( 'contentobject_id' => (int)$objectID, 'contentobject_state_id' => $stateID ) );
            }
            else
            {
                $db->query( "INSERT INTO ezcobj_state_link (contentobject_id, contentobject_state_id)
                             SELECT o.id, $stateID FROM ezcontentobject o
                             WHERE NOT EXISTS ( SELECT l.contentobject_id FROM ezcobj_state_link l, ezcobj_state s
                                                WHERE l.contentobject_state_id = s.id AND l.contentobject_id = o.id
                                                  AND s.group_id = $groupID )" );
            }
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
    }
}
