<?php
/**
 * The attributes a person may see and edit of their own user account. A member who edits their own account
 * (user/edit, content/edit of their user object) and a visitor who signs up (user/register) never get the attributes
 * of the class's "meta" category (Meta Data and the other editorial settings of a content object): they are for
 * editors, not for the person the account belongs to. kernel/content/attribute_edit.php takes them out of the edit
 * list before anything uses it, so no design shows them and no posted value for them is read, validated or stored.
 * An editor who edits another person's account still gets them.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */
class expUserOwnAccountAttributes
{
    /** The class attribute category these edits never offer */
    const HIDDEN_CATEGORY = 'meta';

    /**
     * Whether an attribute of this category is left out (pure, for the tests).
     *
     * @param string $category the class attribute's category ('' is the default category)
     * @param bool $isUserAccount the edited object is a user account (its class has an ezuser attribute)
     * @param bool $byOwnerOrVisitor the account's own user edits it, or a visitor (anonymous) signs up
     * @return bool
     */
    public static function hidden( $category, $isUserAccount, $byOwnerOrVisitor )
    {
        return $isUserAccount && $byOwnerOrVisitor && (string)$category === self::HIDDEN_CATEGORY;
    }

    /**
     * Whether the person editing is the account's own user or a visitor signing up (pure, for the tests).
     *
     * @param int $objectID the edited object
     * @param int $currentUserID the signed-in user's object id
     * @param int $anonymousID the anonymous user's object id
     * @return bool
     */
    public static function byOwnerOrVisitor( $objectID, $currentUserID, $anonymousID )
    {
        return (int)$objectID === (int)$currentUserID || (int)$currentUserID === (int)$anonymousID;
    }

    /**
     * The edit list without the attributes the person may not see.
     *
     * @param eZContentObjectAttribute[] $attributes
     * @param eZContentObject $object
     * @param int|null $currentUserID default: the current user
     * @return eZContentObjectAttribute[]
     */
    public static function filter( $attributes, $object, $currentUserID = null )
    {
        if ( !is_array( $attributes ) || !$object instanceof eZContentObject || !eZUser::isUserObject( $object ) )
            return $attributes;
        if ( $currentUserID === null )
            $currentUserID = eZUser::currentUserID();
        if ( !self::byOwnerOrVisitor( $object->attribute( 'id' ), $currentUserID, eZUser::anonymousId() ) )
            return $attributes;
        $kept = array();
        foreach ( $attributes as $key => $attribute )
        {
            $classAttribute = $attribute instanceof eZContentObjectAttribute ? $attribute->attribute( 'contentclass_attribute' ) : null;
            if ( $classAttribute && self::hidden( $classAttribute->attribute( 'category' ), true, true ) )
                continue;
            $kept[$key] = $attribute;
        }
        return $kept;
    }
}
