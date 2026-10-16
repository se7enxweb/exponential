<?php
/**
 * @description Fill a translation of objects in a test language (Lorem Ipsum, qlo-ZZ) with lorem ipsum text
 * @alias exp:loremipsum
 *
 * The command bin/php/exploremipsumfill.php: adds or fills the translation of an object, or of every object of a
 * subtree, in a test language (a locale marked [LanguageSettings] TestLanguage=true, the shipped one being Lorem
 * Ipsum, qlo-ZZ) with lorem ipsum text per datatype (expTestLanguage::valueFor()): text lines, text blocks, rich
 * text and keywords. Every other attribute keeps what the translation was copied from. Publishes one new version
 * per object. Refuses a language that is not a test language or not installed: it never adds a language.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Exploremipsumfill extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script( array( 'description' => "Fills the translation of objects in a test language with lorem ipsum text.\n\n" .
                                               "Text lines, text blocks, rich text and keywords get lorem ipsum; every other attribute keeps\n" .
                                               "the value of the language it is copied from. One published version per object. The language\n" .
                                               "must be a test language (TestLanguage=true in its share/locale file) and already added on\n" .
                                               "Setup > Languages; the command never adds one.\n\n" .
                                               "  php bin/php/exploremipsumfill.php <object id>[,...] [--dry-run]\n" .
                                               "  php bin/php/exploremipsumfill.php --subtree=<node id> [--limit=50] [--dry-run]\n" .
                                               "  ./console exp:loremipsum <object id> --language=qlo-ZZ --from=eng-GB\n\n" .
                                               "Exit code: 0 done (or a dry run), 1 refused or failed, 2 usage error.\n" .
                                               "Guide: doc/guides/test-language-lorem-ipsum.md",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true,
                              'user' => true ) );
        $options = $this->startup( '[language:][from:][subtree:][limit:][dry-run]', '[OBJECTS]',
                                   array( 'language' => 'the test language to fill (default: ' . \expTestLanguage::LOREM_IPSUM . ')',
                                          'from' => 'the language to copy the other attributes from (default: the object\'s main language)',
                                          'subtree' => 'every object below this node, the node\'s own object included',
                                          'limit' => 'with --subtree: at most this many objects (default 50)',
                                          'dry-run' => 'print what would be written, change nothing' ),
                                   false, array( 'user' => true ) );
        \eZContentLanguage::setCronjobMode( true );

        $language = isset( $options['language'] ) && $options['language'] !== null ? trim( (string)$options['language'] ) : \expTestLanguage::LOREM_IPSUM;
        $from = isset( $options['from'] ) && $options['from'] !== null ? trim( (string)$options['from'] ) : '';
        $dryRun = !empty( $options['dry-run'] );

        if ( !\expTestLanguage::isTestLocale( $language ) )
        {
            $cli->error( "FAIL: $language is not a test language (share/locale/$language.ini with [LanguageSettings] TestLanguage=true)" );
            $this->shutdown( 1 );
            return;
        }
        if ( !\eZContentLanguage::fetchByLocale( $language ) )
        {
            $cli->error( "FAIL: $language is not a content language of this installation. Add it on Setup > Languages first;" );
            $cli->error( "      this command never adds one. Guide: doc/guides/test-language-lorem-ipsum.md" );
            $this->shutdown( 1 );
            return;
        }
        if ( $from !== '' && !\eZContentLanguage::fetchByLocale( $from ) )
        {
            $cli->error( "FAIL: --from=$from is not a content language of this installation" );
            $this->shutdown( 2 );
            return;
        }

        $objects = $this->objects( $options );
        if ( $objects === null )
        {
            $this->shutdown( 2 );
            return;
        }
        if ( !$objects )
        {
            $cli->error( 'FAIL: no object found' );
            $this->shutdown( 1 );
            return;
        }

        if ( !$dryRun && !\eZUser::currentUser()->isLoggedIn() )
        {
            $creator = \eZUser::fetch( (int)\eZINI::instance()->variable( 'UserSettings', 'UserCreatorID' ) );
            if ( $creator )
                \eZUser::setCurrentlyLoggedInUser( $creator, $creator->attribute( 'contentobject_id' ) );
        }

        $failed = 0;
        foreach ( $objects as $object )
        {
            if ( !$this->fill( $object, $language, $from, $dryRun ) )
                $failed++;
        }
        $cli->output( sprintf( '%s: %d object(s)%s, %d failed', $failed ? 'FAIL' : 'PASS', count( $objects ), $dryRun ? ' (dry run, nothing written)' : ' filled', $failed ) );
        $this->shutdown( $failed ? 1 : 0 );
    }

    /**
     * The objects of the arguments or of --subtree, null after a usage error.
     *
     * @return \eZContentObject[]|null
     */
    protected function objects( array $options )
    {
        $cli = $this->cli();
        $objects = array();
        if ( isset( $options['subtree'] ) && $options['subtree'] !== null )
        {
            $node = \eZContentObjectTreeNode::fetch( (int)$options['subtree'] );
            if ( !$node instanceof \eZContentObjectTreeNode )
            {
                $cli->error( 'FAIL: no node ' . (int)$options['subtree'] );
                return null;
            }
            $limit = isset( $options['limit'] ) && $options['limit'] !== null ? max( 1, (int)$options['limit'] ) : 50;
            $objects[(int)$node->attribute( 'contentobject_id' )] = $node->object();
            $children = $node->subTree( array( 'Limit' => $limit, 'Limitation' => array(), 'IgnoreVisibility' => true,
                                               'SortBy' => array( 'path', true ), 'MainNodeOnly' => true ) );
            foreach ( (array)$children as $child )
            {
                if ( count( $objects ) >= $limit )
                    break;
                $objects[(int)$child->attribute( 'contentobject_id' )] = $child->object();
            }
            return array_values( array_filter( $objects ) );
        }
        $ids = array();
        foreach ( (array)( $options['arguments'] ?? array() ) as $argument )
        {
            foreach ( explode( ',', (string)$argument ) as $id )
            {
                if ( trim( $id ) !== '' )
                    $ids[] = (int)$id;
            }
        }
        if ( !$ids )
        {
            $cli->error( 'Give one or more object ids, or --subtree=<node id>. --help shows the usage.' );
            return null;
        }
        foreach ( array_unique( $ids ) as $id )
        {
            $object = \eZContentObject::fetch( $id );
            if ( !$object instanceof \eZContentObject )
            {
                $cli->error( "FAIL: no object $id" );
                return null;
            }
            $objects[] = $object;
        }
        return $objects;
    }

    /**
     * Fills one object's translation and publishes it.
     *
     * @return bool
     */
    protected function fill( \eZContentObject $object, $language, $from, $dryRun )
    {
        $cli = $this->cli();
        $id = (int)$object->attribute( 'id' );
        $source = $from !== '' ? $from : (string)$object->attribute( 'initial_language_code' );
        if ( !in_array( $source, (array)$object->availableLanguages(), true ) )
            $source = (string)$object->attribute( 'initial_language_code' );
        $exists = in_array( $language, (array)$object->availableLanguages(), true );
        $cli->output( sprintf( 'Object %d "%s": %s the %s translation%s', $id, $object->attribute( 'name' ), $exists ? 'fills' : 'adds',
                               $language, $exists ? '' : ", copied from $source" ) );

        $plan = array();
        foreach ( (array)$object->contentObjectAttributes( true, false, $source ) as $attribute )
        {
            $classAttribute = $attribute->contentClassAttribute();
            if ( $classAttribute && !$classAttribute->attribute( 'can_translate' ) )
                continue;
            $type = (string)$attribute->attribute( 'data_type_string' );
            $maxLength = $type === 'ezstring' && $classAttribute ? (int)$classAttribute->attribute( 'data_int1' ) : 0;
            $value = \expTestLanguage::valueFor( $type, $id . ':' . $attribute->attribute( 'contentclass_attribute_identifier' ), $maxLength );
            if ( $value !== null )
                $plan[(string)$attribute->attribute( 'contentclass_attribute_identifier' )] = array( $type, $value );
        }
        foreach ( $plan as $identifier => $item )
            $cli->output( sprintf( '  %-24s %-10s %s', $identifier, $item[0], self::preview( $item[1] ) ) );
        if ( !$plan )
            $cli->output( '  no text attribute to fill; the translation is a copy' );
        if ( $dryRun )
            return true;

        $version = $object->createNewVersionIn( $language, $exists ? $language : $source );
        if ( !$version )
        {
            $cli->error( "  FAIL: object $id: the draft in $language could not be created" );
            return false;
        }
        $db = \eZDB::instance();
        $db->begin();
        foreach ( (array)$version->contentObjectAttributes( $language ) as $attribute )
        {
            $identifier = (string)$attribute->attribute( 'contentclass_attribute_identifier' );
            if ( !isset( $plan[$identifier] ) )
                continue;
            $attribute->fromString( $plan[$identifier][1] );
            $attribute->store();
        }
        $db->commit();
        $result = \eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $id, 'version' => (int)$version->attribute( 'version' ) ) );
        if ( !is_array( $result ) || ( $result['status'] ?? null ) != \eZModuleOperationInfo::STATUS_CONTINUE )
        {
            $cli->error( "  FAIL: object $id: version " . $version->attribute( 'version' ) . ' was not published' );
            return false;
        }
        $cli->output( '  published version ' . $version->attribute( 'version' ) );
        \eZContentObject::clearCache( array( $id ) );
        return true;
    }

    /** The first 70 characters of a value on one line, tags removed. */
    protected static function preview( $value )
    {
        // a space for every tag, so a heading and its paragraph do not run together
        $text = trim( preg_replace( '/\s+/', ' ', strip_tags( str_replace( '<', ' <', preg_replace( '/^<\?xml[^>]*>/', '', (string)$value ) ) ) ) );
        return mb_strlen( $text ) > 70 ? mb_substr( $text, 0, 69 ) . '…' : $text;
    }
}

}
