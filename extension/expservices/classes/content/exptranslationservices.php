<?php
/**
 * Translation services: ezjscore/call/exptranslation::<method>[::arg...]
 *
 * The languages of the site and of content objects: which languages exist and are used, which translations an
 * object has or misses, and writes that translate an object, copy a translation, remove one, and add or remove
 * a site language.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expTranslationServices extends expContentServiceBase
{
    public static $services = array(
        'languages' => array( 'summary' => 'The languages of the site', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'languages' ),
        'language' => array( 'summary' => 'One site language by locale code', 'access' => 'user', 'write' => false, 'args' => array( 'locale' => 'string' ), 'returns' => 'language' ),
        'prioritized' => array( 'summary' => 'The language codes of the siteaccess in priority order', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'codes' ),
        'topPriority' => array( 'summary' => 'The first language of the siteaccess', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'language' ),
        'stats' => array( 'summary' => 'Objects and classes per site language', 'access' => array( 'content', 'translations' ), 'write' => false, 'args' => array(), 'returns' => 'languages with counts' ),
        'knownLocales' => array( 'summary' => 'Locales that can be added as site languages', 'access' => array( 'content', 'translations' ), 'write' => false, 'args' => array( 'text' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of locales' ),
        'ofObject' => array( 'summary' => 'The translations of an object with names and initial flag', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'translations' ),
        'missing' => array( 'summary' => 'Site languages an object has no translation in', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'languages' ),
        'content' => array( 'summary' => 'The attributes of an object in one language', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'language' => 'string' ), 'returns' => 'identifier => attribute' ),
        'names' => array( 'summary' => 'The object name in every language', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'language => name' ),
        'nodeNames' => array( 'summary' => 'The names of a node in every language', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'language => name' ),
        'classNames' => array( 'summary' => 'The name of a class in every language', 'access' => 'user', 'write' => false, 'args' => array( 'class' => 'string' ), 'returns' => 'language => name' ),
        'canTranslate' => array( 'summary' => 'Whether the current user can translate an object, and into which languages', 'access' => 'user', 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => '{can_translate, languages}' ),
        'status' => array( 'summary' => 'Translation completeness of a node subtree: objects per language', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'language => count' ),
        'translate' => array( 'summary' => 'Creates or changes a translation and publishes it. POST: attributes (json), copy_from_language', 'access' => array( 'content', 'translate' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'language' => 'string' ), 'returns' => 'object' ),
        'copyLanguage' => array( 'summary' => 'Copies the content of one language into another and publishes', 'access' => array( 'content', 'translate' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'from' => 'string', 'to' => 'string' ), 'returns' => 'object' ),
        'remove' => array( 'summary' => 'Removes one translation of an object', 'access' => array( 'content', 'translate' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'language' => 'string' ), 'returns' => 'object' ),
        'addLanguage' => array( 'summary' => 'Adds a site language (locale code). POST: name', 'access' => array( 'content', 'translations' ), 'write' => true, 'args' => array( 'locale' => 'string' ), 'returns' => 'language' ),
        'removeLanguage' => array( 'summary' => 'Removes a site language that no object or class uses', 'access' => array( 'content', 'translations' ), 'write' => true, 'args' => array( 'locale' => 'string' ), 'returns' => '{removed}' ),
    );

    protected static function exportLanguage( eZContentLanguage $l, $counts = false )
    {
        $row = array( 'id' => (int)$l->attribute( 'id' ), 'locale' => $l->attribute( 'locale' ), 'name' => $l->attribute( 'name' ), 'disabled' => (bool)$l->attribute( 'disabled' ) );
        if ( $counts )
        {
            $row['objects'] = (int)$l->objectCount();
            $row['initial_objects'] = (int)$l->objectInitialCount();
            $row['classes'] = (int)$l->classCount();
        }
        return $row;
    }

    protected static function siteLanguage( $locale )
    {
        $l = eZContentLanguage::fetchByLocale( (string)$locale );
        if ( !$l )
            throw new expServiceException( "Language $locale is not a site language", 404 );
        return $l;
    }

    public static function languages( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( (array)eZContentLanguage::fetchList( true ) as $l )
            $out[] = self::exportLanguage( $l );
        return self::ok( $out );
    }

    public static function language( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( self::exportLanguage( self::siteLanguage( self::arg( $args, 0, 'string' ) ) ) );
    }

    public static function prioritized( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array_values( (array)eZContentLanguage::prioritizedLanguageCodes() ) );
    }

    public static function topPriority( $args )
    {
        static::guard( __FUNCTION__ );
        $l = eZContentLanguage::topPriorityLanguage();
        return self::ok( $l ? self::exportLanguage( $l ) : null );
    }

    public static function stats( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( (array)eZContentLanguage::fetchList( true ) as $l )
            $out[] = self::exportLanguage( $l, true );
        return self::ok( $out );
    }

    public static function knownLocales( $args )
    {
        static::guard( __FUNCTION__ );
        $text = mb_strtolower( self::arg( $args, 0, 'string', '' ) );
        $used = array();
        foreach ( (array)eZContentLanguage::fetchList( true ) as $l )
            $used[$l->attribute( 'locale' )] = true;
        $items = array();
        foreach ( (array)eZLocale::localeList( false ) as $code )
        {
            if ( $text !== '' && mb_strpos( mb_strtolower( $code ), $text ) === false )
                continue;
            $items[] = array( 'locale' => $code, 'in_use' => isset( $used[$code] ) );
        }
        return self::pageOf( $items, $args, 1, 2 );
    }

    public static function ofObject( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( (array)$o->availableLanguages() as $code )
            $out[] = array( 'language' => $code, 'name' => $o->name( false, $code ), 'is_initial' => $code === $o->attribute( 'initial_language_code' ) );
        return self::ok( $out );
    }

    public static function missing( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $have = (array)$o->availableLanguages();
        $out = array();
        foreach ( (array)eZContentLanguage::fetchList() as $l )
            if ( !in_array( $l->attribute( 'locale' ), $have, true ) && !$l->attribute( 'disabled' ) )
                $out[] = self::exportLanguage( $l );
        return self::ok( $out );
    }

    public static function content( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $lang = self::arg( $args, 1, 'string' );
        if ( !in_array( $lang, (array)$o->availableLanguages(), true ) )
            throw new expServiceException( "The object has no translation in $lang", 404 );
        return self::ok( self::exportDataMap( $o, false, $lang ) );
    }

    public static function names( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( (object)self::object( self::arg( $args, 0, 'int' ) )->names() );
    }

    public static function nodeNames( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( (object)self::node( self::arg( $args, 0, 'int' ) )->object()->names() );
    }

    public static function classNames( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( (object)self::contentClass( self::arg( $args, 0, 'string' ) )->nameList() );
    }

    public static function canTranslate( $args )
    {
        static::guard( __FUNCTION__ );
        $o = eZContentObject::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$o )
            throw new expServiceException( 'Object does not exist', 404 );
        $langs = array();
        foreach ( (array)$o->canCreateLanguages() as $l )
            $langs[] = is_object( $l ) ? $l->attribute( 'locale' ) : (string)$l;
        return self::ok( array( 'can_translate' => (bool)$o->canTranslate(), 'languages' => $langs ) );
    }

    public static function status( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $db = eZDB::instance();
        $path = $db->escapeString( $node->attribute( 'path_string' ) );
        $total = (int)$db->arrayQuery( "SELECT COUNT(*) AS cnt FROM ezcontentobject_tree WHERE path_string LIKE '$path%' AND node_id = main_node_id" )[0]['cnt'];
        $out = array();
        foreach ( (array)eZContentLanguage::fetchList() as $l )
        {
            $mask = (int)$l->attribute( 'id' );
            $c = (int)$db->arrayQuery( "SELECT COUNT(*) AS cnt FROM ezcontentobject_tree t, ezcontentobject o WHERE t.path_string LIKE '$path%' AND t.node_id = t.main_node_id AND o.id = t.contentobject_id AND ( o.language_mask & $mask ) > 0" )[0]['cnt'];
            $out[$l->attribute( 'locale' )] = $c;
        }
        return self::ok( $out, array( 'objects' => $total ) );
    }

    public static function translate( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'translate' );
        $lang = self::languageCode( self::arg( $args, 1, 'string' ), $o );
        $attrs = (array)self::post( 'attributes', 'json' );
        if ( !$attrs )
            throw new expServiceException( 'No attributes given', 400 );
        self::checkInput( $o->contentClass(), $attrs );
        $copy = self::post( 'copy_from_language', 'string', '' );
        if ( $copy !== '' )
        {
            $v = $o->createNewVersionIn( $lang, self::languageCode( $copy, $o ) );
            if ( !$v )
                throw new expServiceException( 'The translation draft could not be created', 422 );
            self::storeInput( $v->contentObjectAttributes( $lang ), $attrs );
            if ( !self::publishVersion( $o, $v->attribute( 'version' ) ) )
                throw new expServiceException( 'The translation was not published', 422 );
            eZContentObject::clearCache();
            return self::ok( self::exportObject( eZContentObject::fetch( $o->attribute( 'id' ) ), true ) );
        }
        return self::ok( self::exportObject( self::updateObject( $o, $attrs, $lang ), true ) );
    }

    public static function copyLanguage( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'translate' );
        $from = self::languageCode( self::arg( $args, 1, 'string' ), $o );
        $to = self::languageCode( self::arg( $args, 2, 'string' ), $o );
        if ( $from === $to )
            throw new expServiceException( 'The languages are the same', 422 );
        if ( !in_array( $from, (array)$o->availableLanguages(), true ) )
            throw new expServiceException( "The object has no translation in $from", 404 );
        $v = $o->createNewVersionIn( $to, $from );
        if ( !$v || !self::publishVersion( $o, $v->attribute( 'version' ) ) )
            throw new expServiceException( 'The copy was not published', 422 );
        eZContentObject::clearCache();
        return self::ok( self::exportObject( eZContentObject::fetch( $o->attribute( 'id' ) ), true ) );
    }

    public static function remove( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'translate' );
        $lang = self::languageCode( self::arg( $args, 1, 'string' ), $o );
        $have = (array)$o->availableLanguages();
        if ( !in_array( $lang, $have, true ) )
            throw new expServiceException( "The object has no translation in $lang", 404 );
        if ( count( $have ) < 2 )
            throw new expServiceException( 'The only translation cannot be removed', 409 );
        if ( $lang === $o->attribute( 'initial_language_code' ) )
            throw new expServiceException( 'The initial language cannot be removed; change it first', 409 );
        $id = (int)$o->attribute( 'id' );
        $langId = (int)eZContentLanguage::idByLocale( $lang );
        self::operation( 'removetranslation', array( 'object_id' => $id, 'language_id_list' => array( $langId ), 'node_id' => (int)$o->attribute( 'main_node_id' ) ),
                         function () use ( $id, $langId ) { return eZContentOperationCollection::removeTranslation( $id, array( $langId ) ); } );
        eZContentObject::clearCache();
        return self::ok( self::exportObject( eZContentObject::fetch( $id ), true ) );
    }

    public static function addLanguage( $args )
    {
        static::guard( __FUNCTION__ );
        $locale = self::arg( $args, 0, 'string' );
        if ( !preg_match( '/^[a-z]{3}-[A-Z]{2}$/', $locale ) )
            throw new expServiceException( 'A locale code looks like eng-GB', 400 );
        if ( eZContentLanguage::fetchByLocale( $locale ) )
            throw new expServiceException( 'The language exists already', 409 );
        $name = self::post( 'name', 'string', null );
        $l = eZContentLanguage::addLanguage( $locale, $name );
        if ( !$l )
            throw new expServiceException( 'The language could not be added', 422 );
        eZContentLanguage::expireCache();
        return self::ok( self::exportLanguage( $l ) );
    }

    public static function removeLanguage( $args )
    {
        static::guard( __FUNCTION__ );
        $l = self::siteLanguage( self::arg( $args, 0, 'string' ) );
        if ( $l->objectCount() > 0 || $l->classCount() > 0 )
            throw new expServiceException( 'Objects or classes use the language', 409 );
        $locale = $l->attribute( 'locale' );
        if ( !$l->removeThis() )
            throw new expServiceException( 'The language could not be removed', 422 );
        eZContentLanguage::expireCache();
        return self::ok( array( 'removed' => $locale ) );
    }
}
