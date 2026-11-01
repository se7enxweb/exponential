<?php
/**
 * File containing the expMigrationIdentifier class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * How a YAML migration names a node, a content object, a class or another part of an installation, so the same file
 * works on installations whose ids differ:
 *
 *   1234                       the id itself (a number, or a text of digits)
 *   remote:<remote id>         the remote id (of the node for a node, of the object for an object, of the class for
 *                              a class)
 *   object-remote:<remote id>  a node: the main node of the object with that remote id
 *   path:<url path>            the node at that path (path_identification_string, e.g. path:media/images), or its
 *                              object
 *   <name>                     the kinds that have a name of their own: a class by its identifier (folder), a
 *                              section by its identifier (standard), a language by its locale (ger-DE), a user by
 *                              the login, a role by its name, an object state as <group>/<state> (ez_lock/locked),
 *                              a class group by its name (Content), an object state group by its identifier (ez_lock),
 *                              a URL alias by its address from the root (about-us/team)
 *   tags (eztags)              a tag by its id, remote:<remote id> or path:<keyword path> (path:Topics/Sport, the
 *                              keywords of the tag and its parents); without the extension eztags a tag names nothing
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
    const KINDS = array( 'node', 'object', 'class', 'section', 'language', 'user', 'role', 'state', 'class_group', 'state_group',
                        'tag', 'url_alias' );

    /** The prefixes of an identifier text. */
    const FORMS = array( 'remote', 'object-remote', 'path' );

    /** The kinds named by a name of their own, and what the name is. */
    const NAMES = array( 'class' => 'identifier', 'section' => 'identifier', 'language' => 'locale', 'user' => 'login',
                         'role' => 'name', 'state' => '<group>/<state>', 'class_group' => 'name', 'state_group' => 'identifier',
                         'url_alias' => 'address' );

    /** The prefixed forms each kind takes (besides the id and, for the kinds of NAMES, the name). */
    const KIND_FORMS = array( 'node' => array( 'remote', 'object-remote', 'path' ), 'object' => array( 'remote', 'path' ),
                              'class' => array( 'remote' ), 'user' => array( 'remote' ),
                              'tag' => array( 'remote', 'path' ) );

    /**
     * The form and value of an identifier. Pure.
     *
     * @param mixed $value
     * @param string|null $error set to the problem when it is not one
     * @param string|null $kind the kind: a kind of NAMES also takes its name (form "name"); null: node or object
     * @return array|null array( 'form' => id|remote|object-remote|path|name, 'value' => int|string )
     */
    public static function parse( $value, &$error = null, $kind = null )
    {
        $error = null;
        if ( is_int( $value ) )
        {
            if ( $value > 0 )
                return array( 'form' => 'id', 'value' => $value );
            $error = 'an id is a number above 0';
            return null;
        }
        $named = $kind !== null && isset( self::NAMES[$kind] );
        $forms = $kind !== null && isset( self::KIND_FORMS[$kind] ) ? self::KIND_FORMS[$kind] : ( $kind === null ? self::FORMS : array() );
        if ( !is_string( $value ) || trim( $value ) === '' )
        {
            $error = 'an identifier is ' . self::formsText( $kind );
            return null;
        }
        $value = trim( $value );
        if ( ctype_digit( $value ) )
            return self::parse( (int)$value, $error, $kind );
        $colon = strpos( $value, ':' );
        $form = $colon === false ? '' : substr( $value, 0, $colon );
        if ( !in_array( $form, $forms, true ) )
        {
            if ( $named && self::validName( $kind, $value ) )
                return array( 'form' => 'name', 'value' => $value );
            $error = "\"$value\" is no identifier" . ( $kind !== null ? " of a $kind" : '' ) . ': ' . self::formsText( $kind );
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
     * Whether a text can be the name of a kind of NAMES. Pure.
     *
     * @param string $kind
     * @param string $value
     * @return bool
     */
    protected static function validName( $kind, $value )
    {
        switch ( $kind )
        {
            case 'class':
            case 'section':
                return (bool)preg_match( '/^[A-Za-z_][A-Za-z0-9_\-]*$/', $value );
            case 'language':
                return (bool)preg_match( '/^[a-z]{2,3}-[A-Za-z]{2,4}(@[a-z]+)?$/', $value );
            case 'state':
                return (bool)preg_match( '#^[A-Za-z0-9_\-]+/[A-Za-z0-9_\-]+$#', $value );
            case 'state_group':
                return (bool)preg_match( '/^[A-Za-z0-9_\-]+$/', $value );
            case 'user':
            case 'role':
            case 'class_group':
                return strpos( $value, ':' ) === false;
            case 'url_alias':
                return strpos( $value, ':' ) === false && !preg_match( '/\s/', $value );
        }
        return false;
    }

    /**
     * What an identifier of a kind can be, for messages. Pure.
     *
     * @param string|null $kind
     * @return string
     */
    protected static function formsText( $kind )
    {
        $forms = $kind !== null && isset( self::KIND_FORMS[$kind] ) ? self::KIND_FORMS[$kind] : ( $kind === null ? self::FORMS : array() );
        $texts = array( 'a number' );
        foreach ( $forms as $form )
            $texts[] = $form === 'path' ? 'path:<url path>' : "$form:<remote id>";
        if ( $kind !== null && isset( self::NAMES[$kind] ) )
            $texts[] = 'the ' . self::NAMES[$kind];
        return implode( ', ', $texts );
    }

    /**
     * The problem of an identifier for a kind, or null; a text with a placeholder is not checked. Pure.
     *
     * @param string $kind one of KINDS
     * @param mixed $value
     * @return string|null
     */
    public static function problem( $kind, $value )
    {
        if ( expMigrationReferences::contains( $value ) )
            return null;
        if ( $kind === 'object' && is_string( $value ) && strpos( trim( $value ), 'object-remote:' ) === 0 )
            return 'object-remote: names a node; an object is named by remote:';
        $error = null;
        $parsed = self::parse( $value, $error, in_array( $kind, self::KINDS, true ) ? $kind : null );
        return $parsed === null ? $error : null;
    }

    /**
     * The id an identifier names on this installation.
     *
     * @param string $kind one of KINDS
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
        $parsed = self::parse( $value, $problem, $kind );
        if ( $parsed === null )
            throw new RuntimeException( 'not an identifier: ' . json_encode( $value ) );
        if ( $kind === 'tag' && !self::tagsInstalled() )
            throw new RuntimeException( 'tags are of the extension eztags, which is not installed here' );
        $id = in_array( $kind, array( 'node', 'object' ), true ) ? self::resolveContent( $kind, $parsed ) : self::resolveNamed( $kind, $parsed );
        if ( $id !== null && $id > 0 )
            return $id;
        throw new RuntimeException( "no $kind " . ( $parsed['form'] === 'id' || $parsed['form'] === 'name' ? $parsed['value'] : $parsed['form'] . ':' . $parsed['value'] ) . ' on this installation' );
    }

    /**
     * Whether an identifier names something on this installation (resolve() without the exception).
     *
     * @param string $kind
     * @param mixed $value
     * @return int|null the id, or null
     */
    public static function find( $kind, $value )
    {
        try
        {
            return self::resolve( $kind, $value );
        }
        catch ( RuntimeException $e )
        {
            return null;
        }
    }

    /**
     * @param string $kind node or object
     * @param array $parsed
     * @return int|null
     */
    protected static function resolveContent( $kind, array $parsed )
    {
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
        return null;
    }

    /**
     * @param string $kind a kind of NAMES
     * @param array $parsed
     * @return int|null
     */
    protected static function resolveNamed( $kind, array $parsed )
    {
        $form = $parsed['form'];
        $value = $parsed['value'];
        $found = null;
        switch ( $kind )
        {
            case 'class':
                if ( $form === 'id' )
                    $found = eZContentClass::fetch( $value, false );
                else if ( $form === 'remote' )
                    $found = eZContentClass::fetchByRemoteID( $value, false );
                else
                    $found = eZContentClass::fetchByIdentifier( $value, false );
                return is_array( $found ) && isset( $found['id'] ) ? (int)$found['id'] : null;
            case 'section':
                $found = $form === 'id' ? eZSection::fetch( $value, false ) : eZSection::fetchByIdentifier( $value, false );
                return is_array( $found ) && isset( $found['id'] ) ? (int)$found['id'] : null;
            case 'language':
                $found = $form === 'id' ? eZContentLanguage::fetch( $value ) : eZContentLanguage::fetchByLocale( $value );
                return $found instanceof eZContentLanguage ? (int)$found->attribute( 'id' ) : null;
            case 'user':
                if ( $form === 'remote' )
                {
                    $object = eZContentObject::fetchByRemoteID( $value, false );
                    $found = is_array( $object ) && isset( $object['id'] ) ? eZUser::fetch( (int)$object['id'] ) : null;
                }
                else
                {
                    $found = $form === 'id' ? eZUser::fetch( $value ) : eZUser::fetchByName( $value );
                }
                return $found instanceof eZUser ? (int)$found->attribute( 'contentobject_id' ) : null;
            case 'role':
                $found = $form === 'id' ? eZRole::fetch( $value ) : eZRole::fetchByName( $value );
                return $found instanceof eZRole ? (int)$found->attribute( 'id' ) : null;
            case 'state':
                if ( $form === 'id' )
                {
                    $found = eZContentObjectState::fetchById( $value );
                }
                else
                {
                    list( $groupName, $stateName ) = explode( '/', $value, 2 );
                    $group = eZContentObjectStateGroup::fetchByIdentifier( $groupName );
                    $found = $group instanceof eZContentObjectStateGroup ? eZContentObjectState::fetchByIdentifier( $stateName, $group->attribute( 'id' ) ) : null;
                }
                return $found instanceof eZContentObjectState ? (int)$found->attribute( 'id' ) : null;
            case 'class_group':
                $found = $form === 'id' ? eZContentClassGroup::fetch( $value ) : eZContentClassGroup::fetchByName( $value );
                return $found instanceof eZContentClassGroup ? (int)$found->attribute( 'id' ) : null;
            case 'state_group':
                $found = $form === 'id' ? eZContentObjectStateGroup::fetchById( $value ) : eZContentObjectStateGroup::fetchByIdentifier( $value );
                return $found instanceof eZContentObjectStateGroup ? (int)$found->attribute( 'id' ) : null;
            case 'tag':
                if ( $form === 'id' )
                    $found = eZTagsObject::fetchWithMainTranslation( $value );
                else if ( $form === 'remote' )
                    $found = eZTagsObject::fetchByRemoteID( $value, true );
                else
                    $found = eZTagsObject::fetchByUrl( $value, true );
                return $found instanceof eZTagsObject ? (int)$found->attribute( 'id' ) : null;
            case 'url_alias':
                if ( $form === 'id' )
                {
                    $rows = eZPersistentObject::fetchObjectList( eZURLAliasML::definition(), null, array( 'id' => (int)$value ), null, array( 'limit' => 1, 'offset' => 0 ), false );
                    return is_array( $rows ) && $rows ? (int)$rows[0]['id'] : null;
                }
                foreach ( (array)eZURLAliasML::fetchByPath( $value ) as $element )
                    if ( $element instanceof eZURLAliasML && (string)$element->attribute( 'action' ) !== 'nop:' )
                        return (int)$element->attribute( 'id' );
                return null;
        }
        return null;
    }

    /**
     * Whether the extension eztags is installed: its classes load. Its tables are its own; the steps of the type tag
     * and the identifiers of the kind tag need both.
     *
     * @return bool
     */
    public static function tagsInstalled()
    {
        return class_exists( 'eZTagsObject' ) && class_exists( 'eZTagsKeyword' );
    }
}
