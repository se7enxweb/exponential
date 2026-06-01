<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = $Params['Module'];


$tpl = eZTemplate::factory();

$tpl->setVariable( 'md5_result', false );
$tpl->setVariable( 'upgrade_sql', false );

if ( $Module->isCurrentAction( 'MD5Check' ) )
{
    if ( !file_exists( eZMD5::CHECK_SUM_LIST_FILE ) )
    {
        $tpl->setVariable( 'md5_result', 'failed' );
        $tpl->setVariable( 'failure_reason',
                           ezpI18n::tr( 'kernel/setup', 'File %1 does not exist. '.
                                    'You should copy it from the recent eZ Publish distribution.',
                                    null, array( eZMD5::CHECK_SUM_LIST_FILE ) ) );
    }
    else
    {
        $checkResult = eZMD5::checkMD5Sums( eZMD5::CHECK_SUM_LIST_FILE );

        $extensionsDir = eZExtension::baseDirectory();
        foreach( eZextension::activeExtensions() as $activeExtension )
        {
            $extensionPath = "$extensionsDir/$activeExtension/";
            if ( file_exists( $extensionPath . eZMD5::CHECK_SUM_LIST_FILE ) )
            {
                $checkResult = array_merge( $checkResult, eZMD5::checkMD5Sums( $extensionPath . eZMD5::CHECK_SUM_LIST_FILE, $extensionPath ) );
            }
        }

        if ( count( $checkResult ) == 0 )
        {
            $tpl->setVariable( 'md5_result', 'ok' );
        }
        else
        {
            $tpl->setVariable( 'md5_result', $checkResult );
        }
    }
}

if ( $Module->isCurrentAction( 'DBCheck' ) )
{
    $db = eZDB::instance();
    $dbSchema = eZDbSchema::instance();
    // read original schema from dba file
    $originalSchema = eZDbSchema::read( 'share/db_schema.dba' );

    // merge schemas from all active extensions that declare some db schema
    $extensionsdir = eZExtension::baseDirectory();
    foreach( eZExtension::activeExtensions() as $activeextension )
    {
        if ( file_exists( $extensionsdir . '/' . $activeextension . '/share/db_schema.dba' ) )
        {
            if ( $extensionschema = eZDbSchema::read( $extensionsdir . '/' . $activeextension . '/share/db_schema.dba' ) )
            {
                $originalSchema = eZDbSchema::merge( $originalSchema, $extensionschema );
            }
        }
    }

    // transform schema to 'localized' version for current db
    // (we might as well convert $dbSchema to generic format and diff in generic format,
    // but eZDbSchemaChecker::diff does not know how to re-localize the generated sql
    if ( !is_object( $dbSchema ) )
    {
        // MongoDB has no relational schema adapter — validate collections instead.
        // Build the expected table list from the schema DBA files (already loaded above).
        $expectedTables = is_array( $originalSchema ) ? array_keys( $originalSchema ) : [];
        // Remove any non-table metadata keys (schema arrays sometimes have a '_info' key etc.)
        $expectedTables = array_filter( $expectedTables, function( $k ) { return strpos( $k, 'ez' ) === 0; } );

        $existingCollections = method_exists( $db, 'listCollectionNames' ) ? $db->listCollectionNames() : [];

        $missing = array_values( array_diff( $expectedTables, $existingCollections ) );
        $extra   = array_values( array_diff( $existingCollections, $expectedTables ) );

        // Feature-group labels for missing collection categorisation
        $featureGroups = [
            'Collaboration'     => [ 'ezcollab_group', 'ezcollab_item', 'ezcollab_item_group_link', 'ezcollab_item_message_link', 'ezcollab_item_participant_link', 'ezcollab_item_status', 'ezcollab_notification_rule', 'ezcollab_profile', 'ezcollab_simple_message' ],
            'Shop / Commerce'   => [ 'ezcurrencydata', 'ezdiscountrule', 'ezdiscountsubrule', 'ezdiscountsubrule_value', 'ezmultipricedata', 'ezorder', 'ezorder_nr_incr', 'ezorder_item', 'ezorder_status_history', 'ezpaymentobject', 'ezproductcategory', 'ezproductcollection_item_opt', 'ezvatrule', 'ezvatrule_product_category', 'ezuser_discountrule', 'ezwishlist' ],
            'Workflow'          => [ 'ezapprove_items', 'ezmodule_run', 'ezoperation_memento', 'ezpending_actions', 'ezpublishingqueueprocesses', 'ezscheduled_script', 'eztrigger', 'ezwaituntildatevalue', 'ezworkflow', 'ezworkflow_assign', 'ezworkflow_event', 'ezworkflow_group_link', 'ezworkflow_process' ],
            'Notifications'     => [ 'eznotificationcollection', 'eznotificationcollection_item', 'ezmessage', 'ezsubtree_notification_rule' ],
            'Media / Binary'    => [ 'ezbinaryfile', 'ezmedia' ],
            'Sessions / Auth'   => [ 'ezsession', 'ezforgot_password', 'ezuser_accountkey' ],
            'REST / OAuth'      => [ 'ezprest_authcode', 'ezprest_authorized_clients', 'ezprest_clients', 'ezprest_token' ],
            'Content'           => [ 'ezcontentobject_trash', 'ezenumobjectvalue', 'ezenumvalue', 'ezview_counter' ],
            'Search'            => [ 'ezsearch_search_phrase' ],
            'RSS'               => [ 'ezrss_import' ],
            'PDF Export'        => [ 'ezpdf_export' ],
            'Tip-a-Friend'      => [ 'eztipafriend_counter', 'eztipafriend_request' ],
            'Geo / Map'         => [ 'ezgmaplocation' ],
        ];

        $tpl->setVariable( 'mongo_check', true );

        if ( empty( $missing ) )
        {
            $tpl->setVariable( 'upgrade_sql', 'ok' );
        }
        else
        {
            // Assign each missing collection to its feature group
            $grouped  = [];
            $assigned = [];
            foreach ( $featureGroups as $groupName => $groupCols )
            {
                $inGroup = array_intersect( $missing, $groupCols );
                if ( !empty( $inGroup ) )
                {
                    $grouped[$groupName] = array_values( $inGroup );
                    $assigned = array_merge( $assigned, $inGroup );
                }
            }
            $other = array_values( array_diff( $missing, $assigned ) );
            if ( !empty( $other ) )
                $grouped['Other'] = $other;

            // Convert to indexed array for eZ template iteration (name / count / collections)
            $groupedList = [];
            foreach ( $grouped as $groupName => $cols )
                $groupedList[] = [ 'name' => $groupName, 'count' => count( $cols ), 'collections' => $cols ];

            // Build the mongosh JS snippet to pre-create all missing non-adapter collections
            $adapterOnly    = [ 'ezsequence', 'nxc_datalist_filters', 'init' ];
            $createList     = array_values( array_diff( $missing, $adapterOnly ) );
            $mongoCreateCmd = "[\n  '" . implode( "',\n  '", $createList ) . "'\n].forEach(function(c) {\n  db.createCollection(c);\n  print('Created: ' + c);\n});";

            $tpl->setVariable( 'mongo_missing_count', count( $missing ) );
            $tpl->setVariable( 'mongo_grouped_list', $groupedList );
            $tpl->setVariable( 'mongo_extra', $extra );
            $tpl->setVariable( 'mongo_create_cmd', $mongoCreateCmd );
            // Set upgrade_sql to a non-'ok' non-false string so the warning block renders; content comes from structured vars above
            $tpl->setVariable( 'upgrade_sql', 'mongo' );
        }
    }
    else
    {
    $dbSchema->transformSchema( $originalSchema, true );
    $differences = eZDbSchemaChecker::diff( $dbSchema->schema( array( 'format' => 'local', 'force_autoincrement_rebuild' => true ) ), $originalSchema );
    $sqlDiff = $dbSchema->generateUpgradeFile( $differences );

    if ( strlen( $sqlDiff ) == 0 )
    {
        $tpl->setVariable( 'upgrade_sql', 'ok' );
    }
    else
    {
        $tpl->setVariable( 'upgrade_sql', $sqlDiff );
    }
    }
}

$Result = array();
$Result['content'] = $tpl->fetch( "design:setup/systemupgrade.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'System Upgrade' ) ) );
?>
