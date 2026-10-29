<?php
/**
 * File containing the expDataTableMirrorType class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The workflow event "Mirror to data tables" (doc/eng-us/features/6.0/data-tables.md, "Live mirror"): in a workflow on
 * content/publish (after), the published object is written to the tables of the chosen data table synchronisations at
 * once, whatever their mode; with none chosen, to those of the object's class whose subtree holds it. The site.ini
 * listeners do the same for the live synchronisations without a workflow; this event is for a site that wants the
 * mirror as a step of its own workflow (after an approval, before a notification). It never stops the workflow: a
 * failure is logged.
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataTableMirrorType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'expdatatablemirror';

    public function __construct()
    {
        parent::__construct( self::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'kernel/workflow/event', 'Mirror to data tables' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    public function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $objectID = isset( $parameters['object_id'] ) ? (int)$parameters['object_id'] : 0;
        if ( !$objectID || !class_exists( 'expDataSync' ) || !expDataSync::installed() )
            return eZWorkflowType::STATUS_ACCEPTED;
        try
        {
            $object = eZContentObject::fetch( $objectID );
            $chosen = self::chosenIDs( $event );
            $syncs = array();
            foreach ( expDataSyncDefinition::fetchList( array( 'state' => 'enabled' ) ) as $sync )
            {
                if ( $sync->attribute( 'direction' ) === 'table_to_content' )
                    continue;
                if ( $chosen ? in_array( (int)$sync->attribute( 'id' ), $chosen, true )
                             : ( $object && $sync->attribute( 'content_class' ) === $object->attribute( 'class_identifier' ) ) )
                    $syncs[] = $sync;
            }
            if ( $syncs )
                expDataSync::mirror( $objectID, 'publish', $syncs );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( "Mirror to data tables, object $objectID: " . $e->getMessage(), __METHOD__ );
        }
        return eZWorkflowType::STATUS_ACCEPTED;
    }

    public function attributeDecoder( $event, $attr )
    {
        switch ( $attr )
        {
            case 'selected_syncs':
                return self::chosenIDs( $event );
            case 'syncs':
                return class_exists( 'expDataSync' ) && expDataSync::installed() ? expDataSyncDefinition::fetchList() : array();
        }
        return null;
    }

    public function typeFunctionalAttributes()
    {
        return array( 'selected_syncs', 'syncs' );
    }

    public function fetchHTTPInput( $http, $base, $event )
    {
        $name = $base . '_event_expdatatablemirror_syncs_' . $event->attribute( 'id' );
        $ids = $http->hasPostVariable( $name ) ? array_values( array_filter( array_map( 'intval', (array)$http->postVariable( $name ) ) ) ) : array();
        $event->setAttribute( 'data_text1', implode( ',', $ids ) );
    }

    /**
     * @param eZWorkflowEvent $event
     * @return int[] the chosen synchronisations (empty: those of the object's class)
     */
    protected static function chosenIDs( $event )
    {
        return array_values( array_filter( array_map( 'intval', explode( ',', (string)$event->attribute( 'data_text1' ) ) ) ) );
    }
}

eZWorkflowEventType::registerEventType( expDataTableMirrorType::WORKFLOW_TYPE_STRING, 'expDataTableMirrorType' );
?>
