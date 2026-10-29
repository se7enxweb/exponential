<?php
/**
 * E-books of help manuals stored as content (doc/eng-us/specifications/6.0/help-system.md, delivery path 3: "stored as a
 * file object under E-books (section according to audience)"; doc/eng-us/features/6.0/help-content.md, "Stored e-books").
 *
 * Each format of each Released manual is one object of the class "file" below the folder E-books (remote_id
 * help-ebooks), found by its remote_id help-ebook-<manual remote_id>-<format>:
 *
 * - versions: a new build is published as a new version of the same object, so the content history keeps the earlier
 *   editions; a build whose file is the same as the stored one changes nothing ("No change.");
 * - state: Released (help_status), like everything the help delivers;
 * - section: help_public for a public manual (its audience is a public audience), else help_internal, set on the
 *   object only. So the policies of the plan decide who may download it: Anonymous reads help_public and Released.
 *   A public manual's book carries only public objects (expHelpContentEditionProvider);
 * - rebuilt on release: the listener onStateAssign() (site.ini [Event] Listeners[]=content/state/assign@...) builds the
 *   books of every manual that contains an object set to Released, when exphelp.ini [ContentSettings]
 *   EbooksOnRelease=enabled (the default). The command builds them too: ./console exp:help:content ebooks.
 *
 * The manual's page in the help lists its stored books (stored()).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentEbooks
{
    /** The prefix of the remote_id of a stored e-book: help-ebook-<manual remote_id>-<format> */
    const REMOTE_PREFIX = 'help-ebook-';

    /** The class of a stored e-book and its attributes */
    const FILE_CLASS = 'file';

    /** @var bool */
    protected $dryRun;

    /** @var int */
    protected $userId;

    /** @var string */
    protected $language;

    /** @var array list of array( step, action, what ) */
    protected $changes = array();

    /** @var string[]|null the formats to build (default: expHelpContentExport::formats()) */
    protected $formats;

    /** @var string the language the file objects are stored in */
    protected $storeLanguage;

    public function __construct( array $options = array() )
    {
        $this->dryRun = !empty( $options['dry_run'] );
        $this->userId = isset( $options['user_id'] ) ? (int)$options['user_id'] : (int)eZUser::currentUserID();
        // the file objects are stored in the default language; the language of the edition (the topics' text, the
        // remote_id and name of its books) is the option language, read as expHelpContentEbooks::useLanguage() set it
        $this->storeLanguage = class_exists( 'eZContentObject' ) ? (string)eZContentObject::defaultLanguage() : 'eng-US';
        $this->language = isset( $options['language'] ) ? (string)$options['language'] : $this->storeLanguage;
        $this->formats = isset( $options['formats'] ) ? (array)$options['formats'] : null;
    }

    /** @return array list of array( step, action, what ) */
    public function changes()
    {
        return $this->changes;
    }

    /**
     * The remote_id of the stored e-book of a manual in a format
     *
     * @param string $manualRemoteId
     * @param string $format
     * @return string
     */
    public static function remoteId( $manualRemoteId, $format, $language = '' )
    {
        $suffix = '';
        if ( (string)$language !== '' && (string)$language !== (string)eZContentObject::defaultLanguage() )
            $suffix = strtolower( (string)$language ) . '-';
        return self::REMOTE_PREFIX . $manualRemoteId . '-' . $suffix . $format;
    }

    /**
     * Reads the help in a language from now on (for the edition of a manual in that language): its translation, else
     * the default language. Objects already read are forgotten.
     *
     * @param string $locale
     * @return bool false when the installation has no such content language
     */
    public static function useLanguage( $locale )
    {
        $language = eZContentLanguage::fetchByLocale( (string)$locale );
        if ( !$language instanceof eZContentLanguage )
            return false;
        $default = (string)eZContentObject::defaultLanguage();
        eZContentLanguage::setPrioritizedLanguages( array_values( array_unique( array( (string)$locale, $default ) ) ) );
        eZContentObject::clearCache();
        expHelpContentRepository::resetInstance();
        expHelpContentProvider::resetInstance();
        return true;
    }

    /**
     * Whether a manual is public: its audience contains a public audience (the section the validator gives it)
     *
     * @param array $manual
     * @return bool
     */
    public static function isPublicManual( array $manual )
    {
        return expHelpContentSchema::isPublicAudience( isset( $manual['audience'] ) ? (array)$manual['audience'] : array() );
    }

    /**
     * Builds and stores the e-books of the Released manuals (or of one)
     *
     * @param string|null $manualRemoteId
     * @return int the number of books created or updated (in a dry run: that would be)
     */
    public function build( $manualRemoteId = null )
    {
        $repository = expHelpContentRepository::instance();
        $folder = eZContentObject::fetchByRemoteID( expHelpContentSchema::EBOOKS );
        $folderNode = $folder instanceof eZContentObject ? $folder->attribute( 'main_node' ) : null;
        if ( !$folderNode instanceof eZContentObjectTreeNode )
        {
            $this->change( 'error', 'the folder E-books (remote_id ' . expHelpContentSchema::EBOOKS . ') does not exist (run exp:help:content install --apply)' );
            return 0;
        }
        if ( !eZContentClass::fetchByIdentifier( self::FILE_CLASS, false ) )
        {
            $this->change( 'error', 'there is no class "' . self::FILE_CLASS . '" to store an e-book in' );
            return 0;
        }
        $count = 0;
        $found = false;
        foreach ( $repository->load()['maps'] as $manual )
        {
            if ( $manual['class'] !== 'help_manual' || ( $manualRemoteId !== null && $manual['remote_id'] !== $manualRemoteId ) )
                continue;
            $found = true;
            if ( $manual['state'] !== expHelpContentSchema::STATE_RELEASED )
            {
                $this->change( 'skip', $manual['remote_id'] . ': the manual is not Released' );
                continue;
            }
            foreach ( $this->formats === null ? expHelpContentExport::formats() : $this->formats as $format )
            {
                if ( $this->buildOne( $repository, $manual, $format, $folderNode ) )
                    $count++;
            }
        }
        if ( $manualRemoteId !== null && !$found )
            $this->change( 'error', "no manual with the remote_id $manualRemoteId" );
        return $count;
    }

    /**
     * One book: build it into the cache, compare it with the stored one, store a new version when it differs
     *
     * @return bool whether it was (or would be) stored
     */
    protected function buildOne( expHelpContentRepository $repository, array $manual, $format, eZContentObjectTreeNode $folderNode )
    {
        $remoteId = self::remoteId( $manual['remote_id'], $format, $this->language );
        $manualObject = eZContentObject::fetch( (int)$manual['object_id'] );
        $available = $manualObject instanceof eZContentObject ? (array)$manualObject->availableLanguages() : array();
        if ( !self::hasEdition( $available, $this->language, $this->storeLanguage ) )
        {
            // the help would fall back to the default language: the "translation" would be a copy of the default book
            $this->change( 'skip', "$remoteId: the manual has no translation in {$this->language}" );
            $this->removeStale( $remoteId );
            return false;
        }
        $public = self::isPublicManual( $manual );
        $export = new expHelpContentExport( new expHelpContentEditionProvider( $repository, $public ), $manual );
        if ( !$export->topics() )
        {
            $this->change( 'skip', "$remoteId: the manual has no Released " . ( $public ? 'public ' : '' ) . 'topic' );
            return false;
        }
        $dir = rtrim( expHelp::cacheDirectory(), '/' ) . '/ebooks/build-' . getmypid();
        $file = $dir . '/' . preg_replace( '/[^A-Za-z0-9._-]/', '-', $manual['remote_id'] ) . '.' . $format;
        try
        {
            $result = $format === 'pdf' ? $export->writePdf( $file ) : $export->writeEpub( $file );
        }
        catch ( RuntimeException $e )
        {
            $this->change( 'error', "$remoteId: " . $e->getMessage() );
            return false;
        }
        // epubcheck and Ace by DAISY, where the site has them (expHelpContentBookCheck); BookChecks=required keeps a
        // book that fails them from being stored
        if ( $format === 'epub' )
        {
            $checker = new expHelpContentBookCheck();
            $checks = $checker->check( $result['file'] );
            foreach ( $checks as $check )
            {
                if ( $check['status'] !== expHelpContentBookCheck::SKIPPED )
                    $this->change( 'check', "$remoteId: {$check['tool']} " . strtoupper( $check['status'] ) . ": {$check['message']}" );
            }
            if ( $checker->refuses( $checks ) )
            {
                $this->change( 'error', "$remoteId: not stored, a check failed (BookChecks=required)" );
                $this->cleanUp( $result['file'], $dir );
                return false;
            }
        }
        $object = eZContentObject::fetchByRemoteID( $remoteId );
        $stored = $object instanceof eZContentObject ? self::storedFile( $object ) : null;
        $same = $stored !== null && is_file( $stored ) && sha1_file( $stored ) === sha1_file( $result['file'] );
        $sectionId = self::sectionId( $public ? expHelpContentSchema::SECTION_PUBLIC : expHelpContentSchema::SECTION_INTERNAL );
        $title = $manual['title'] . ' (' . strtoupper( $format ) . ( $this->language !== $this->storeLanguage ? ', ' . $this->language : '' ) . ')';
        $stores = false;
        if ( $object instanceof eZContentObject && $same )
        {
            // the same book: only the section and state are made right
            $this->ensureSectionAndState( $object, $sectionId, $remoteId );
        }
        elseif ( $this->dryRun )
        {
            $this->change( $object instanceof eZContentObject ? 'update' : 'create', "$remoteId ({$result['topics']} topics)" );
            $stores = true;
        }
        else
        {
            $attributes = array( 'name' => $title, 'file' => $result['file'] . '|' . basename( $result['file'] ) );
            if ( $object instanceof eZContentObject )
            {
                eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => $attributes, 'language' => $this->storeLanguage ) );
                $object = eZContentObject::fetch( (int)$object->attribute( 'id' ) );
                $this->change( 'update', "$remoteId: version " . (int)$object->attribute( 'current_version' ) . " ({$result['topics']} topics)" );
            }
            else
            {
                $object = eZContentFunctions::createAndPublishObject( array(
                    'class_identifier' => self::FILE_CLASS,
                    'parent_node_id' => (int)$folderNode->attribute( 'node_id' ),
                    'creator_id' => $this->userId,
                    'remote_id' => $remoteId,
                    'language' => $this->storeLanguage,
                    'attributes' => $attributes,
                ) );
                if ( !$object instanceof eZContentObject )
                {
                    $this->change( 'error', "$remoteId could not be created" );
                    $this->cleanUp( $result['file'], $dir );
                    return false;
                }
                $object = eZContentObject::fetch( (int)$object->attribute( 'id' ) );
                $this->change( 'create', "$remoteId ({$result['topics']} topics)" );
            }
            $this->ensureSectionAndState( $object, $sectionId, $remoteId );
            eZContentCacheManager::clearContentCacheIfNeeded( (int)$object->attribute( 'id' ) );
            $stores = true;
        }
        $this->cleanUp( $result['file'], $dir );
        return $stores;
    }

    /**
     * Whether a manual has an edition in a language: the default language always, any other only when the manual is
     * translated into it. Without the translation the help reads the default language, and the book would be a copy of
     * the default edition under a translated remote_id.
     *
     * @param string[] $availableLanguages the manual object's languages
     * @param string $language the language of the edition
     * @param string $defaultLanguage the default content language
     * @return bool
     */
    public static function hasEdition( array $availableLanguages, $language, $defaultLanguage )
    {
        $language = strtolower( (string)$language );
        return $language === strtolower( (string)$defaultLanguage ) || in_array( $language, array_map( 'strtolower', array_map( 'strval', $availableLanguages ) ), true );
    }

    /**
     * A book stored for an edition the manual does not have (from a build before hasEdition()) goes to the trash
     */
    protected function removeStale( $remoteId )
    {
        $object = eZContentObject::fetchByRemoteID( $remoteId );
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'main_node_id' ) <= 0 )
            return;
        $this->change( 'remove', "$remoteId: stored for a translation the manual does not have (to the trash)" );
        if ( !$this->dryRun )
            eZContentObjectTreeNode::removeSubtrees( array( (int)$object->attribute( 'main_node_id' ) ), true );
    }

    protected function cleanUp( $file, $dir )
    {
        if ( is_file( $file ) )
            @unlink( $file );
        if ( is_dir( $dir ) )
            @rmdir( $dir );
    }

    /**
     * The section from the manual's audience and the state Released, on the object only
     */
    protected function ensureSectionAndState( eZContentObject $object, $sectionId, $label )
    {
        if ( $sectionId > 0 && (int)$object->attribute( 'section_id' ) !== $sectionId )
        {
            $this->change( 'section', "$label to section $sectionId" );
            if ( !$this->dryRun )
            {
                $installer = new expHelpContentInstaller( array( 'dry_run' => false, 'user_id' => $this->userId ) );
                $installer->setObjectSection( $object, $sectionId );
            }
        }
        $released = self::releasedState();
        if ( $released instanceof eZContentObjectState && !in_array( (int)$released->attribute( 'id' ), array_map( 'intval', (array)$object->attribute( 'state_id_array' ) ), true ) )
        {
            $this->change( 'release', $label );
            if ( !$this->dryRun )
                $object->assignState( $released );
        }
    }

    /** @return eZContentObjectState|null the state Released of help_status */
    public static function releasedState()
    {
        $group = eZContentObjectStateGroup::fetchByIdentifier( expHelpContentSchema::STATE_GROUP );
        $state = $group instanceof eZContentObjectStateGroup ? $group->stateByIdentifier( expHelpContentSchema::STATE_RELEASED ) : null;
        return $state instanceof eZContentObjectState ? $state : null;
    }

    protected static function sectionId( $identifier )
    {
        $section = eZSection::fetchByIdentifier( $identifier );
        return $section instanceof eZSection ? (int)$section->attribute( 'id' ) : 0;
    }

    /**
     * The local file of a stored e-book's current version, null without one
     *
     * @param eZContentObject $object
     * @return string|null
     */
    public static function storedFile( eZContentObject $object )
    {
        $dataMap = $object->attribute( 'data_map' );
        if ( !isset( $dataMap['file'] ) || !$dataMap['file']->attribute( 'has_content' ) )
            return null;
        $binary = $dataMap['file']->content();
        if ( !$binary instanceof eZBinaryFile )
            return null;
        $path = (string)$binary->attribute( 'filepath' );
        $handler = eZClusterFileHandler::instance( $path );
        if ( !is_file( $path ) && $handler->exists() )
            $handler->fetch();
        return is_file( $path ) ? $path : null;
    }

    /**
     * The stored e-books of a manual the current user may read: what the manual's page links to
     *
     * @param string $manualRemoteId
     * @return array list of array( format, name, url (content/download/...), size, modified, version, object_id, node_url )
     */
    public static function stored( $manualRemoteId )
    {
        $list = array();
        // the edition of the default language, then those of the manuals' translations ([ManualSettings] Languages[])
        $languages = array_values( array_unique( array_merge( array( (string)eZContentObject::defaultLanguage() ),
                                                              (array)expHelpManualBuilder::setting( 'Languages', array() ) ) ) );
        $editions = array();
        foreach ( $languages as $language )
            foreach ( array( 'epub', 'pdf' ) as $format )
                $editions[] = array( $format, $language );
        foreach ( $editions as $edition )
        {
            list( $format, $language ) = $edition;
            $object = eZContentObject::fetchByRemoteID( self::remoteId( $manualRemoteId, $format, $language ) );
            if ( !$object instanceof eZContentObject || !$object->canRead() )
                continue;
            $dataMap = $object->attribute( 'data_map' );
            if ( !isset( $dataMap['file'] ) || !$dataMap['file']->attribute( 'has_content' ) )
                continue;
            $binary = $dataMap['file']->content();
            if ( !$binary instanceof eZBinaryFile )
                continue;
            $node = $object->attribute( 'main_node' );
            $list[] = array(
                'format' => $format,
                'language' => $language,
                'name' => (string)$object->attribute( 'name' ),
                'url' => 'content/download/' . (int)$object->attribute( 'id' ) . '/' . (int)$dataMap['file']->attribute( 'id' ) . '/file/'
                       . rawurlencode( (string)$binary->attribute( 'original_filename' ) ),
                'size' => (int)$binary->attribute( 'filesize' ),
                'modified' => (int)$object->attribute( 'modified' ),
                'version' => (int)$object->attribute( 'current_version' ),
                'object_id' => (int)$object->attribute( 'id' ),
                'node_url' => $node instanceof eZContentObjectTreeNode ? 'content/view/full/' . (int)$node->attribute( 'node_id' ) : '',
            );
        }
        return $list;
    }

    // ---------------------------------------------------------------- rebuilt on release

    /**
     * The manuals that contain an object: itself, through its chapters, its topics, or the figures of its topics
     *
     * @param array $set expHelpContentRepository::load()
     * @param int $objectId
     * @return string[] remote_ids of manuals
     */
    public static function manualsContaining( array $set, $objectId )
    {
        $byId = array();
        foreach ( array( 'topics', 'maps', 'figures' ) as $kind )
        {
            foreach ( $set[$kind] as $item )
                $byId[(int)$item['object_id']] = $item;
        }
        $manuals = array();
        foreach ( $set['maps'] as $map )
        {
            if ( $map['class'] !== 'help_manual' )
                continue;
            $seen = array();
            $queue = array( (int)$map['object_id'] );
            while ( $queue )
            {
                $id = array_shift( $queue );
                if ( isset( $seen[$id] ) || !isset( $byId[$id] ) )
                    continue;
                $seen[$id] = true;
                $item = $byId[$id];
                foreach ( isset( $item['topicrefs'] ) ? $item['topicrefs'] : array() as $ref )
                    $queue[] = (int)$ref;
                foreach ( isset( $item['figures'] ) ? $item['figures'] : array() as $ref )
                    $seen[(int)$ref] = true;
            }
            if ( isset( $seen[(int)$objectId] ) )
                $manuals[] = $map['remote_id'];
        }
        return $manuals;
    }

    /**
     * Listener of content/state/assign (site.ini [Event] Listeners[]): when an object of the help is set to Released,
     * the e-books of every Released manual that contains it are built again and stored. Never throws: a book that
     * cannot be made is logged and the release itself stands.
     *
     * @param int $objectId
     * @param int[] $stateIds the states assigned
     */
    public static function onStateAssign( $objectId, $stateIds = array() )
    {
        try
        {
            // the cheap questions first: this runs for every assignment of states of every object
            if ( (string)expHelpContentSchema::setting( 'EbooksOnRelease', 'enabled' ) !== 'enabled' )
                return;
            $released = self::releasedState();
            if ( !$released instanceof eZContentObjectState || !in_array( (int)$released->attribute( 'id' ), array_map( 'intval', (array)$stateIds ), true ) )
                return;
            $object = eZContentObject::fetch( (int)$objectId );
            if ( !$object instanceof eZContentObject || !in_array( (string)$object->attribute( 'class_identifier' ), expHelpContentSchema::classIdentifiers(), true )
                 || !expHelpContentProvider::isEnabled() )
                return;
            eZContentObject::clearCache( array( (int)$objectId ) );
            expHelpContentProvider::resetInstance();
            $manuals = self::manualsContaining( expHelpContentRepository::instance()->load(), (int)$objectId );
            $builder = new self();
            foreach ( $manuals as $remoteId )
                $builder->build( $remoteId );
            foreach ( $builder->changes() as $change )
            {
                if ( $change['action'] === 'error' )
                    eZDebug::writeWarning( 'E-book of the help: ' . $change['what'], __METHOD__ );
            }
            expHelpContentProvider::resetInstance();
        }
        catch ( Exception $e )
        {
            eZDebug::writeError( 'The e-books of the help could not be built: ' . $e->getMessage(), __METHOD__ );
        }
    }

    protected function change( $action, $what )
    {
        $this->changes[] = array( 'step' => 'ebooks', 'action' => $action, 'what' => $what );
    }
}

?>
