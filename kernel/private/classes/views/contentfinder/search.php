<?php
/**
 * The contentfinder/search view (doc/eng-us/features/6.0/content-finder.md): one search field and a type selector, read
 * from the view parameters (text) and (type) only (contentfinder/search/(type)/object/(text)/58; a query string is not
 * read), and one result block per object found, with what the user may
 * see of it. Needs the policy contentfinder/search; answers on administration siteaccesses only. Every search is
 * recorded in the audit (content.finder.search). The search form is posted (SearchText, SearchType) and answered with a
 * 303 to that address. The text is written as every view parameter is (expViewParameters::encode(): "~2F" for "/"),
 * so that a path or a URL survives the address unchanged; the "~" and base64url form of before is still read
 * (textFromParameter()).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Contentfinder
{

class Search extends \Exponential\Runnable\ModuleView
{
    /** The session variable the action view leaves its outcome in */
    const FEEDBACK = 'ContentFinderFeedback';

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $module = $Params['Module'];
        if ( !\expContentFinder::isAvailableHere() )
            return $module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $http = \eZHTTPTool::instance();
        if ( \expViewParameters::redirectsPost( $_POST, array( 'SearchText', 'SearchType' ) ) )
            \expViewParameters::redirect( self::searchURI( \expViewParameters::posted( $_POST, 'SearchText' ),
                                                           \expViewParameters::posted( $_POST, 'SearchType' ) ) );
        $text = self::textFromParameter( \expViewParameters::raw( $Params, 'text', 'Text' ) );
        $type = \expContentFinderInput::type( \expViewParameters::get( $Params, 'type', 'Type' ) ?: 'auto' );

        $feedback = null;
        if ( $http->hasSessionVariable( self::FEEDBACK ) )
        {
            $feedback = $http->sessionVariable( self::FEEDBACK );
            $http->removeSessionVariable( self::FEEDBACK );
            if ( !is_array( $feedback ) )
                $feedback = null;
        }

        $found = null;
        if ( trim( $text ) !== '' )
        {
            $finder = new \expContentFinder();
            $found = $finder->search( $text, $type );
            self::audit( $found );
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'search_text', $found !== null ? $found['input']['text'] : '' );
        $tpl->setVariable( 'search_type', $type );
        $tpl->setVariable( 'types', \expContentFinderInput::types() );
        $tpl->setVariable( 'found', $found );
        $tpl->setVariable( 'feedback', $feedback );
        $tpl->setVariable( 'form_token', \expContentFinderAction::formToken() );
        $tpl->setVariable( 'token_field', \expContentFinderAction::TOKEN_FIELD );
        $tpl->setVariable( 'can_act', \expContentFinder::hasPolicy( 'contentfinder', 'delete' ) );

        return array( 'content' => $tpl->fetch( 'design:contentfinder/search.tpl' ),
                      'path' => array( array( 'text' => \ezpI18n::tr( 'kernel/contentfinder', 'Setup' ), 'url' => 'setup/menu' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentfinder', 'Content finder' ), 'url' => false ) ) );
    }

    /**
     * The address of a search: contentfinder/search/(type)/<type>/(text)/<text> (no type for "auto", nothing for an
     * empty text).
     *
     * @param string $text
     * @param string $type
     * @return string
     */
    public static function searchURI( $text, $type = 'auto' )
    {
        $type = \expContentFinderInput::type( $type );
        $uri = '/contentfinder/search';
        if ( $type !== 'auto' )
            $uri .= '/(type)/' . rawurlencode( $type );
        $text = trim( (string)$text );
        if ( $text !== '' )
            $uri .= '/(text)/' . self::textParameter( $text );
        return $uri;
    }

    /**
     * A search text as the view parameter (text): expViewParameters::encode() ("~2F" for "/", "~28" for "(" ...).
     *
     * @param string $text
     * @return string
     */
    public static function textParameter( $text )
    {
        return \expViewParameters::encode( (string)$text );
    }

    /**
     * The search text of the view parameter (text) as the kernel hands it (expViewParameters::raw()): the encoding
     * of every view parameter (expViewParameters::decode()), or the form of before, "~" and the base64url of the
     * text, so that the links written then keep working. A value is read as the old form only when it is not one
     * the encoding writes (expViewParameters::escape() of its decoding gives it back) and its base64url is valid
     * UTF-8.
     *
     * @param string|false $value
     * @return string
     */
    public static function textFromParameter( $value )
    {
        if ( !is_string( $value ) || $value === '' )
            return '';
        $decoded = \expViewParameters::decode( $value );
        if ( $value[0] !== '~' || \expViewParameters::escape( $decoded ) === $value )
            return $decoded;
        $legacy = self::legacyTextFromParameter( $value );
        return $legacy !== null ? $legacy : $decoded;
    }

    /**
     * The text of the form of before, "~" and a base64url, null when $value is not one.
     *
     * @param string $value
     * @return string|null
     */
    public static function legacyTextFromParameter( $value )
    {
        if ( !preg_match( '/^~([A-Za-z0-9_-]+)$/', (string)$value, $m ) )
            return null;
        $text = base64_decode( strtr( $m[1], '-_', '+/' ), true );
        return is_string( $text ) && $text !== '' && preg_match( '//u', $text ) ? $text : null;
    }

    /**
     * Records a search: the type asked and recognised, the text (at most 128 characters), how many objects were
     * shown and how many were found but not shown.
     *
     * @param array $found from expContentFinder::search()
     */
    public static function audit( array $found )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        \expAuditHook::emit( 'content.finder.search', function () use ( $found ) {
            $value = (string)$found['input']['text'];
            if ( function_exists( 'mb_strlen' ) ? mb_strlen( $value ) > 128 : strlen( $value ) > 128 )
                $value = ( function_exists( 'mb_substr' ) ? mb_substr( $value, 0, 128 ) : substr( $value, 0, 128 ) ) . '…';
            $types = array();
            foreach ( $found['input']['queries'] as $query )
                $types[] = $query['type'];
            return array( 'object' => array( 'type' => 'view', 'id' => 'contentfinder/search' ),
                          'result' => $found['input']['error'] === '' ? 'success' : 'refused',
                          'reason' => $found['input']['error'] === '' ? null : $found['input']['error'],
                          'after' => array( 'type' => $found['input']['type'], 'as' => array_values( array_unique( $types ) ),
                                            'value' => $value, 'results' => (int)$found['count'], 'hidden' => (int)$found['hidden'],
                                            'truncated' => (bool)$found['truncated'] ) );
        } );
    }
}

}
