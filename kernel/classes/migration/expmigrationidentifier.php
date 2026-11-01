<?php
/**
 * File containing the expMigrationIdentifier class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * How a YAML migration names a node or a content object, so the same file works on installations whose ids differ:
 *
 *   1234                       the id itself (a number, or a text of digits)
 *   remote:<remote id>         the remote id (of the node for a node, of the object for an object)
 *   object-remote:<remote id>  a node: the main node of the object with that remote id
 *   path:<url path>            the node at that path (path_identification_string, e.g. path:media/images), or its
 *                              object
 *
 * A placeholder is replaced first, so ${idmap.node.news} (the id map of the environment, expMigrationIdMap) and
 * ${ref.folder} work wherever an identifier does. Step types call resolve() for the keys their schema declares as
 * identifiers; the parse is pure, resolving reads the database.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationIdentifier
{
    /** The kinds resolve() knows. */
    const KINDS = array( 'node', 'object' );

    /** The prefixes of an identifier text. */
    const FORMS = array( 'remote', 'object-remote', 'path' );

    /**
     * The form and value of an identifier. Pure.
     *
     * @param mixed $value
     * @param string|null $error set to the problem when it is not one
     * @return array|null array( 'form' => id|remote|object-remote|path, 'value' => int|string )
     */
    public static function parse( $value, &$error = null )
    {
        $error = null;
        if ( is_int( $value ) )
        {
            if ( $value > 0 )
                return array( 'form' => 'id', 'value' => $value );
            $error = 'an id is a number above 0';
            return null;
        }
        if ( !is_string( $value ) || trim( $value ) === '' )
        {
            $error = 'an identifier is a number, remote:<remote id>, object-remote:<remote id> or path:<url path>';
            return null;
        }
        $value = trim( $value );
        if ( ctype_digit( $value ) )
            return self::parse( (int)$value, $error );
        $colon = strpos( $value, ':' );
        $form = $colon === false ? '' : substr( $value, 0, $colon );
        if ( !in_array( $form, self::FORMS, true ) )
        {
            $error = "\"$value\" is no identifier: a number, remote:<remote id>, object-remote:<remote id> or path:<url path>";
            return null;
        }
        $rest = trim( (string)substr( $value, $colon + 1 ) );
        if ( $form === 'path' )
            $rest = trim( $rest, '/' );
        if ( $rest === '' )
        {
            $error = "\"$value\" names nothing after $form:";
            return null;
        }
        return array( 'form' => $form, 'value' => $rest );
    }

    /**
     * The problem of an identifier for a kind, or null; a text with a placeholder is not checked. Pure.
     *
     * @param string $kind node or object
     * @param mixed $value
     * @return string|null
     */
    public static function problem( $kind, $value )
    {
        if ( expMigrationReferences::contains( $value ) )
            return null;
        $error = null;
        $parsed = self::parse( $value, $error );
        if ( $parsed === null )
            return $error;
        if ( $kind === 'object' && $parsed['form'] === 'object-remote' )
            return 'object-remote: names a node; an object is named by remote:';
        return null;
    }

    /**
     * The id an identifier names on this installation.
     *
     * @param string $kind node or object
     * @param mixed $value
     * @return int
     * @throws RuntimeException when it is no identifier or names nothing here
     */
    public static function resolve( $kind, $value )
    {
        if ( !in_array( $kind, self::KINDS, true ) )
            throw new RuntimeException( "no identifiers of the kind $kind (" . implode( ', ', self::KINDS ) . ')' );
        $problem = self::problem( $kind, $value );
        if ( $problem !== null )
            throw new RuntimeException( $problem );
        $parsed = self::parse( $value );
        if ( $parsed === null )
            throw new RuntimeException( 'not an identifier: ' . json_encode( $value ) );
        $node = null;
        $object = null;
        switch ( $parsed['form'] )
        {
            case 'id':
                if ( $kind === 'node' )
                    $node = eZContentObjectTreeNode::fetch( $parsed['value'], false, false );
                else
                    $object = eZContentObject::fetch( $parsed['value'], false );
                break;
            case 'remote':
                if ( $kind === 'node' )
                    $node = eZContentObjectTreeNode::fetchByRemoteID( $parsed['value'], false );
                else
                    $object = eZContentObject::fetchByRemoteID( $parsed['value'], false );
                break;
            case 'object-remote':
                $found = eZContentObject::fetchByRemoteID( $parsed['value'], false );
                $main = is_array( $found ) && isset( $found['id'] ) ? eZContentObjectTreeNode::findMainNode( (int)$found['id'], false ) : null;
                if ( $main )
                    return (int)$main;
                break;
            case 'path':
                $node = eZContentObjectTreeNode::fetchByURLPath( $parsed['value'], false );
                break;
        }
        if ( $kind === 'node' && is_array( $node ) && isset( $node['node_id'] ) )
            return (int)$node['node_id'];
        if ( $kind === 'object' && is_array( $object ) && isset( $object['id'] ) )
            return (int)$object['id'];
        // a node row of eZContentObjectTreeNode::fetch() carries its object's id as id (contentobject_id elsewhere)
        if ( $kind === 'object' && is_array( $node ) && ( isset( $node['contentobject_id'] ) || isset( $node['id'] ) ) )
            return (int)( isset( $node['contentobject_id'] ) ? $node['contentobject_id'] : $node['id'] );
        throw new RuntimeException( "no $kind " . ( $parsed['form'] === 'id' ? $parsed['value'] : $parsed['form'] . ':' . $parsed['value'] ) . ' on this installation' );
    }
}
