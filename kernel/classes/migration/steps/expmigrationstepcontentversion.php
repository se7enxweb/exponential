<?php
/**
 * File containing the expMigrationStepContentVersion class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "content_version": the versions of a content object.
 *
 *   - type: content_version
 *     mode: restore                    restore (publish a copy of an earlier version), cleanup (remove old versions)
 *     object: remote:news-welcome
 *     version: 3
 *
 * restore does what "Copy and edit" plus publish does in the version history of the administration: a new version
 * copied from the earlier one, published through the publish operation. cleanup removes archived versions, the
 * oldest first, so that "keep" of them are left, and with "drafts: true" every draft as well.
 *
 * Neither can undo itself: a migration with them needs a "down:".
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepContentVersion extends expMigrationStepContentBase
{
    /** @return string */
    public function name()
    {
        return 'content_version';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Restores an earlier version of a content object or removes its old versions',
            'modes' => array( 'restore', 'cleanup' ),
            'keys' => array(
                'object' => array( 'type' => 'identifier', 'kind' => 'object', 'description' => 'the object (or node:)' ),
                'node' => array( 'type' => 'identifier', 'kind' => 'node', 'description' => 'a node of the object (or object:)' ),
                'version' => array( 'type' => 'int', 'modes' => array( 'restore' ), 'description' => 'the number of the version to restore (restore)' ),
                'language' => array( 'type' => 'string', 'modes' => array( 'restore' ),
                                     'description' => 'restore only this language of the version (default: every language it has)' ),
                'keep' => array( 'type' => 'int', 'default' => 0, 'modes' => array( 'cleanup' ),
                                 'description' => 'how many archived versions are kept, the newest (cleanup)' ),
                'drafts' => array( 'type' => 'bool', 'default' => false, 'modes' => array( 'cleanup' ),
                                   'description' => 'true: the drafts of the object are removed too (cleanup)' ),
            ) + self::operationKeys( array( 'restore' ) ),
            'values' => array(
                'object_id' => 'the id of the object',
                'version' => 'its current version after the step',
                'removed' => 'how many versions cleanup removed',
            ),
            'example' => "- type: content_version\n  mode: cleanup\n  object: remote:news-welcome\n  keep: 5\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        if ( isset( $step['object'] ) === isset( $step['node'] ) )
            $errors[] = 'name the object by object: or by node: (one of them)';
        $mode = isset( $step['mode'] ) ? $step['mode'] : null;
        if ( $mode === 'restore' && !isset( $step['version'] ) )
            $errors[] = 'version is missing (mode restore)';
        if ( isset( $step['version'] ) && is_int( $step['version'] ) && $step['version'] < 1 )
            $errors[] = 'version: a version number is 1 or more';
        if ( isset( $step['keep'] ) && is_int( $step['keep'] ) && $step['keep'] < 0 )
            $errors[] = 'keep: 0 or more';
        if ( isset( $step['language'] ) && !expMigrationReferences::contains( $step['language'] ) && expMigrationIdentifier::problem( 'language', $step['language'] ) !== null )
            $errors[] = 'language: ' . expMigrationIdentifier::problem( 'language', $step['language'] );
        return $errors;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $target = isset( $step['object'] ) ? 'the object ' . self::named( $step['object'] ) : 'the object of node ' . self::named( $step['node'] );
        if ( $step['mode'] === 'restore' )
            return array( 'restore version ' . self::named( $step['version'] ) . " of $target" . ( isset( $step['language'] ) ? ' in ' . $step['language'] : '' ) . ' as a new published version' );
        return array( "remove the archived versions of $target but the newest " . (int)$step['keep'] . ( !empty( $step['drafts'] ) ? ', and its drafts' : '' ) );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $object = self::findObject( $step );
        if ( $object === null )
            throw new RuntimeException( ( isset( $step['object'] ) ? 'the object ' . self::named( $step['object'] ) : 'the object of node ' . self::named( $step['node'] ) ) . ' is not on this installation' );
        $objectId = (int)$object->attribute( 'id' );
        if ( $step['mode'] === 'restore' )
        {
            $number = (int)$step['version'];
            $old = $object->version( $number );
            if ( !$old instanceof eZContentObjectVersion )
                throw new RuntimeException( "the object $objectId has no version $number" );
            $language = isset( $step['language'] ) ? self::locale( $step['language'] ) : false;
            $db = eZDB::instance();
            $db->begin();
            try
            {
                $new = $object->copyRevertTo( $number, $language );
            }
            catch ( Throwable $e )
            {
                // nothing of a transaction that failed half way is committed by a later commit()
                if ( $db->transactionCounter() > 0 )
                    $db->rollback();
                throw $e;
            }
            $db->commit();
            $object = self::publish( $objectId, $new, $step );
            return self::result( array( "restored version $number of the object $objectId as version $new" ),
                                 array( 'object_id' => $objectId, 'version' => (int)$object->attribute( 'current_version' ), 'removed' => 0 ) );
        }
        $archived = array();
        $drafts = array();
        foreach ( (array)$object->versions( true ) as $version )
        {
            $status = (int)$version->attribute( 'status' );
            if ( $status === eZContentObjectVersion::STATUS_ARCHIVED )
                $archived[(int)$version->attribute( 'version' )] = $version;
            else if ( !empty( $step['drafts'] ) && in_array( $status, array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_INTERNAL_DRAFT ), true ) )
                $drafts[(int)$version->attribute( 'version' )] = $version;
        }
        krsort( $archived );
        $remove = array_slice( $archived, max( 0, (int)$step['keep'] ), null, true ) + $drafts;
        ksort( $remove );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            foreach ( $remove as $version )
                $version->removeThis();
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        eZContentObject::clearCache( array( $objectId ) );
        return self::result( array( 'removed ' . count( $remove ) . " version(s) of the object $objectId" . ( $remove ? ': ' . implode( ', ', array_keys( $remove ) ) : '' ) ),
                             array( 'object_id' => $objectId, 'version' => (int)$object->attribute( 'current_version' ), 'removed' => count( $remove ) ) );
    }
}
