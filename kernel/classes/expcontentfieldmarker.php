<?php
/**
 * File containing the expContentFieldMarker class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Markers around every rendered attribute ({attribute_view_gui}), for a tool that edits the page in place (the
 * website sidebar expwt): where an attribute's output starts and ends, which field, object, version, attribute and
 * language it is.
 *
 *   <!--exp-field data-exp-field="title" data-exp-object="61" data-exp-version="7" data-exp-attribute="402" data-exp-language="eng-US"-->
 *   ... the attribute's template's output ...
 *   <!--/exp-field-->
 *
 * HTML comments: they change neither the layout nor what a CSS selector matches, so a page with markers looks the same
 * as one without.
 *
 * Only when a handler says so for this request: site.ini [ContentFieldMarkerSettings] Handlers[] names classes with a
 * static active() (an extension adds its own; expwt's says yes for a signed-in user with the sidebar). Without a
 * handler, or when none says yes, nothing is emitted: a visitor's page and every cache of it are byte for byte as
 * before. What the handlers decide on (the siteaccess, the user's roles) is what the view cache and the role-aware
 * page cache key on already, so a cached page with markers is only ever served to those who get them.
 *
 * The answer is kept per user and siteaccess, so a persistent worker (Velocity) asks again for the next user.
 */
class expContentFieldMarker
{
    const NAME = 'exp-field';

    /** @var array "<user id>/<siteaccess>" => bool */
    protected static $active = array();

    /**
     * Whether the attributes rendered in this request carry markers.
     *
     * @return bool
     */
    public static function active()
    {
        $access = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : '';
        $key = ( class_exists( 'eZUser', false ) ? eZUser::currentUserID() : 0 ) . '/' . $access;
        if ( !isset( self::$active[$key] ) )
            self::$active[$key] = self::ask();
        return self::$active[$key];
    }

    /**
     * Forget the answers (a test, or a user that changed within the request), or set one for this request.
     *
     * @param bool|null $active
     */
    public static function reset( $active = null )
    {
        self::$active = array();
        if ( $active !== null )
        {
            $access = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : '';
            self::$active[( class_exists( 'eZUser', false ) ? eZUser::currentUserID() : 0 ) . '/' . $access] = (bool)$active;
        }
    }

    protected static function ask()
    {
        try
        {
            $ini = eZINI::instance();
            if ( !$ini->hasVariable( 'ContentFieldMarkerSettings', 'Handlers' ) )
                return false;
            foreach ( (array)$ini->variable( 'ContentFieldMarkerSettings', 'Handlers' ) as $handler )
            {
                $handler = trim( (string)$handler );
                if ( $handler !== '' && class_exists( $handler ) && is_callable( array( $handler, 'active' ) ) && call_user_func( array( $handler, 'active' ) ) )
                    return true;
            }
        }
        catch ( Exception $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
        }
        return false;
    }

    /**
     * The opening and the closing marker of an attribute about to be rendered, or two empty strings.
     *
     * @param mixed $attribute the {attribute_view_gui} input
     * @return string[] array( open, close )
     */
    public static function pair( $attribute )
    {
        if ( !$attribute instanceof eZContentObjectAttribute || !self::active() )
            return array( '', '' );
        return array( self::open( array(
            'field' => (string)$attribute->attribute( 'contentclass_attribute_identifier' ),
            'object' => (int)$attribute->attribute( 'contentobject_id' ),
            'version' => (int)$attribute->attribute( 'version' ),
            'attribute' => (int)$attribute->attribute( 'id' ),
            'language' => (string)$attribute->attribute( 'language_code' ),
        ) ), self::close() );
    }

    /**
     * The opening marker. Pure: only safe characters reach the comment (an identifier, numbers, a locale), so it can
     * never end early.
     *
     * @param array $d field, object, version, attribute, language
     * @return string
     */
    public static function open( array $d )
    {
        $out = '<!--' . self::NAME;
        foreach ( array( 'field', 'object', 'version', 'attribute', 'language' ) as $key )
        {
            if ( !isset( $d[$key] ) )
                continue;
            $value = in_array( $key, array( 'object', 'version', 'attribute' ), true )
                   ? (string)(int)$d[$key]
                   : preg_replace( '/[^A-Za-z0-9_-]/', '', (string)$d[$key] );
            $out .= ' data-exp-' . $key . '="' . $value . '"';
        }
        return $out . '-->';
    }

    /** @return string the closing marker */
    public static function close()
    {
        return '<!--/' . self::NAME . '-->';
    }
}
