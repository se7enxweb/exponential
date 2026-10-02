<?php
/**
 * One of the 15 built-in subitems columns (thumbnail, name, visibility, type, modifier, modified,
 * published, translations, section, nodeid, noderemoteid, objectid, objectremoteid, objectstate,
 * priority).
 *
 * The list in the browser renders these itself from the node JSON of ezjscnode::subtree, so the
 * rows server function never computes them. value() exists for the CSV export, which has no
 * browser to do it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsBuiltinColumn extends expSubitemsColumn
{
    /**
     * The built-in keys with their default settings (the INI block, when there is one, is merged
     * over these). SortField is the subTree() sort; LegacySort is the sort name the list used
     * before (the ezjscnode JSON field), still accepted as a sort key.
     *
     * @return array key => settings
     */
    public static function definitions()
    {
        return array(
            'thumbnail'      => array( 'Name' => 'Thumbnail', 'Group' => 'Basic', 'Type' => 'image', 'Order' => 10 ),
            'name'           => array( 'Name' => 'Name', 'Group' => 'Basic', 'Type' => 'text', 'SortField' => 'name', 'LegacySort' => 'name', 'Order' => 20 ),
            'visibility'     => array( 'Name' => 'Visibility', 'Group' => 'Basic', 'Type' => 'text', 'SortField' => 'visibility', 'LegacySort' => 'hidden_status_string', 'Order' => 30 ),
            'type'           => array( 'Name' => 'Type', 'Group' => 'Basic', 'Type' => 'text', 'SortField' => 'class_name', 'LegacySort' => 'class_name', 'Order' => 40 ),
            'modifier'       => array( 'Name' => 'Modifier', 'Group' => 'People', 'Type' => 'text', 'Order' => 50 ),
            'modified'       => array( 'Name' => 'Modified', 'Group' => 'Dates', 'Type' => 'datetime', 'SortField' => 'modified', 'LegacySort' => 'modified_date', 'Order' => 60 ),
            'published'      => array( 'Name' => 'Published', 'Group' => 'Dates', 'Type' => 'datetime', 'SortField' => 'published', 'LegacySort' => 'published_date', 'Order' => 70 ),
            'translations'   => array( 'Name' => 'Translations', 'Group' => 'Translations', 'Type' => 'list', 'Order' => 80 ),
            'section'        => array( 'Name' => 'Section', 'Group' => 'Object', 'Type' => 'text', 'SortField' => 'section', 'LegacySort' => 'section', 'Order' => 90 ),
            'nodeid'         => array( 'Name' => 'Node ID', 'Group' => 'Node', 'Type' => 'number', 'SortField' => 'node_id', 'LegacySort' => 'node_id', 'Align' => 'right', 'Copy' => 'true', 'Order' => 100 ),
            'noderemoteid'   => array( 'Name' => 'Node remote ID', 'Group' => 'Node', 'Type' => 'code', 'Copy' => 'true', 'Order' => 110 ),
            'objectid'       => array( 'Name' => 'Object ID', 'Group' => 'Object', 'Type' => 'number', 'SortField' => 'contentobject_id', 'LegacySort' => 'contentobject_id', 'Align' => 'right', 'Copy' => 'true', 'Order' => 120 ),
            'objectremoteid' => array( 'Name' => 'Object remote ID', 'Group' => 'Object', 'Type' => 'code', 'Copy' => 'true', 'Order' => 130 ),
            'objectstate'    => array( 'Name' => 'Object state', 'Group' => 'Workflow', 'Type' => 'list', 'Order' => 140 ),
            'priority'       => array( 'Name' => 'Priority', 'Group' => 'Basic', 'Type' => 'number', 'SortField' => 'priority', 'LegacySort' => 'priority', 'Align' => 'right', 'Order' => 150 ),
        );
    }

    /**
     * @return array the 15 built-in keys
     */
    public static function keys()
    {
        return array_keys( self::definitions() );
    }

    public function value( eZContentObjectTreeNode $node )
    {
        $object = $node->object();
        switch ( $this->key )
        {
            case 'name':
                return $object instanceof eZContentObject ? (string)$object->attribute( 'name' ) : (string)$node->attribute( 'name' );
            case 'visibility':
                return (string)$node->attribute( 'hidden_status_string' );
            case 'type':
                return $object instanceof eZContentObject ? (string)$object->attribute( 'class_name' ) : null;
            case 'modifier':
                if ( !$object instanceof eZContentObject )
                    return null;
                $version = $object->attribute( 'current' );
                $creator = $version ? $version->attribute( 'creator' ) : null;
                return $creator instanceof eZContentObject ? (string)$creator->attribute( 'name' ) : null;
            case 'modified':
                return $object instanceof eZContentObject ? (int)$object->attribute( 'modified' ) : null;
            case 'published':
                return $object instanceof eZContentObject ? (int)$object->attribute( 'published' ) : null;
            case 'translations':
                return $object instanceof eZContentObject
                    ? array_values( eZContentLanguage::decodeLanguageMask( $object->attribute( 'language_mask' ), true )['language_list'] )
                    : array();
            case 'section':
                if ( !$object instanceof eZContentObject )
                    return null;
                $section = eZSection::fetch( $object->attribute( 'section_id' ) );
                return $section instanceof eZSection ? (string)$section->attribute( 'name' ) : null;
            case 'nodeid':
                return (int)$node->attribute( 'node_id' );
            case 'noderemoteid':
                return (string)$node->attribute( 'remote_id' );
            case 'objectid':
                return (int)$node->attribute( 'contentobject_id' );
            case 'objectremoteid':
                return $object instanceof eZContentObject ? (string)$object->attribute( 'remote_id' ) : null;
            case 'objectstate':
                return $object instanceof eZContentObject ? array_values( $object->attribute( 'state_identifier_array' ) ) : array();
            case 'priority':
                return (int)$node->attribute( 'priority' );
            case 'thumbnail':
            default:
                return null;
        }
    }
}
