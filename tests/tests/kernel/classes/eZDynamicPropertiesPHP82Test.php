<?php
/**
 * The properties LoveStack (mugoweb/ezpublish-legacy) declared on kernel classes so that PHP 8.2 stops reporting
 * "Creation of dynamic property ... is deprecated" (commits 3b6a11496f, 1bd250d4c6, 72009a7f46, f8fcc09a57,
 * 10e40c35dd, d7f1480c76, 6bf0faa5f2). Exponential solves the same with #[AllowDynamicProperties] on
 * eZPersistentObject and the database and session handlers. Each case sets the property LoveStack declared on an
 * instance made without its constructor (no database) and expects no deprecation.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group php8
 */

class eZDynamicPropertiesPHP82Test extends PHPUnit\Framework\TestCase
{
    /** class => the properties LoveStack declared on it, which Exponential does not declare */
    private static $cases = array(
        'eZURL' => array( 'Modified' ),
        'eZCollaborationGroup' => array( 'PathString', 'Depth' ),
        'eZCollaborationItemStatus' => array( 'UserID' ),
        'eZContentClass' => array( 'RemoteID', 'URLAliasName' ),
        'eZContentObject' => array( 'CurrentVersion', 'OwnerID', 'ClassID', 'InitialLanguageID', 'LanguageMask', 'SectionID' ),
        'eZContentObjectAttribute' => array( 'ContentObjectID' ),
        'eZContentObjectTreeNode' => array( 'NodeID', 'MainNodeID', 'Depth', 'PathIdentificationString', 'ParentNodeID',
                                            'ContentObjectID', 'ContentObjectVersion', 'IsHidden', 'IsInvisible' ),
        'eZContentObjectVersion' => array( 'ContentObjectID', 'Status', 'InitialLanguageID' ),
        'eZInformationCollection' => array( 'ID', 'ContentObjectID' ),
        'eZNodeAssignment' => array( 'Name' ),
        'eZOrder' => array( 'ID', 'UserID' ),
        'eZOrderStatus' => array( 'ID' ),
        'eZPolicy' => array( 'NodeID', 'ID' ),
        'eZPolicyLimitation' => array( 'NodeID' ),
        'eZProductCollection' => array( 'ID' ),
        'eZProductCollectionItem' => array( 'ContentObjectID' ),
        'eZRSSExport' => array( 'ID', 'Status' ),
        'eZRSSExportItem' => array( 'ClassID' ),
        'eZRSSImport' => array( 'ClassID' ),
        'eZSection' => array( 'ID' ),
        'eZVatRule' => array( 'ID' ),
        'eZVatType' => array( 'ID' ),
        'eZGeneralDigestUserSettings' => array( 'UserID' ),
        'eZContentObjectState' => array( 'ID', 'LanguageMask', 'Identifier' ),
        'eZContentObjectStateGroup' => array( 'ID', 'LanguageMask', 'Identifier' ),
        'eZContentObjectStateGroupLanguage' => array( 'Name' ),
        'eZContentObjectStateLanguage' => array( 'Name' ),
        'eZMySQLiDB' => array( 'QueryAnalysisOutput', 'SlowSQLTimeout' ),
        'eZPostgreSQLDB' => array( 'SlowSQLTimeout' ),
        'ezpSessionHandlerDB' => array( 'userHasCookie' ),
        // eZPersistentObject subclasses with the generic properties LoveStack put on eZPersistentObject itself
        'eZWorkflowProcess' => array( 'MementoKey', 'WorkflowEventPos' ),
        'eZOperationMemento' => array( 'MainKey' ),
    );

    public function testLoveStackDeclaredPropertiesRaiseNoDeprecation()
    {
        $notices = array();
        set_error_handler( function ( $no, $str ) use ( &$notices ) { $notices[] = $str; return true; } );
        try
        {
            foreach ( self::$cases as $class => $properties )
            {
                $this->assertTrue( class_exists( $class ), "$class exists" );
                $object = ( new ReflectionClass( $class ) )->newInstanceWithoutConstructor();
                foreach ( $properties as $property )
                {
                    $object->$property = 1;
                    $this->assertSame( 1, $object->$property, "$class::\$$property" );
                }
            }
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( array(), $notices );
    }
}
