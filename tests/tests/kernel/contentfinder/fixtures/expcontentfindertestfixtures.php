<?php
/**
 * The test content, roles and users of the content finder's database tests (expContentFinderSandboxTest). It WRITES
 * to the database it runs against: use it only in a throwaway copy of an installation. Idempotent: what exists is
 * found again by its remote id; what an earlier run moved to the trash or took versions of is made again.
 *
 *   CF Finder (folder)
 *     CF Open (folder)               the readers' subtree
 *       CF Article                   versions 1 (archived), 2 (published), 3 (a draft)
 *       CF Image                     a real image file (ezimagefile rows), if the installation has an image class
 *       CF File                      a real binary file (ezbinaryfile row), if it has a file class
 *       CF Trash me, CF Delete me, CF Version me (three versions), CF Purge me (in the trash)
 *     CF Closed (folder)
 *       CF Secret                    outside the readers' subtree
 *
 * Users, in a group of their own (CF Users, so the roles of the default group do not reach them), each with
 * user/login and a new random password every run (in the seed, never printed):
 *   cf_editor      content/* : makes, owns and trashes the test content
 *   cf_reader      contentfinder/search; content/read and content/versionread in CF Open only
 *   cf_noversions  contentfinder/search; content/read everywhere, no versionread
 *   cf_nofinder    content/read only
 *   cf_remover     contentfinder/search and delete; content/read, versionread, restore, cleantrash everywhere;
 *                  content/remove, edit and versionremove in CF Open
 *   cf_searcher    contentfinder/search; content/read, versionread and restore everywhere (no delete)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expContentFinderTestFixtures
{
    /**
     * Whether the current directory is a throwaway installation the tests may write to: EXP_CONTENTFINDER_SANDBOX
     * names it, and its database is an SQLite file inside it.
     *
     * @return string '' when it is, else why not
     */
    public static function refusal()
    {
        $sandbox = (string)getenv( 'EXP_CONTENTFINDER_SANDBOX' );
        $cwd = getcwd();
        if ( $sandbox === '' || realpath( $sandbox ) !== $cwd )
            return 'EXP_CONTENTFINDER_SANDBOX does not name the current directory (a throwaway copy of an installation)';
        $db = eZDB::instance();
        if ( !$db || !$db->isConnected() || $db->databaseName() !== 'sqlite' )
            return 'the database is not an SQLite copy';
        $file = realpath( eZSQLite3DB::filePath( eZINI::instance()->variable( 'DatabaseSettings', 'Database' ) ) );
        if ( $file === false || strpos( $file, $cwd . '/' ) !== 0 )
            return 'the database file is not inside the throwaway installation';
        return '';
    }

    /** the first attribute identifier of a datatype in a class */
    public static function attribute( $classIdentifier, $datatypes )
    {
        $class = eZContentClass::fetchByIdentifier( $classIdentifier );
        if ( !$class )
            return null;
        foreach ( $class->fetchAttributes() as $attribute )
            if ( in_array( $attribute->attribute( 'data_type_string' ), (array)$datatypes, true ) )
                return $attribute->attribute( 'identifier' );
        return null;
    }

    /** the attributes that name an object of a class */
    public static function name( $class, $name )
    {
        $id = self::attribute( $class, array( 'ezstring' ) );
        return $id ? array( $id => $name ) : array();
    }

    /** @return string the class of the test articles */
    public static function articleClass()
    {
        return eZContentClass::fetchByIdentifier( 'article' ) ? 'article' : 'folder';
    }

    protected static function object( $remoteID, $parentNodeID, $class, array $attributes, $storageDir = '' )
    {
        $object = eZContentObject::fetchByRemoteID( $remoteID );
        if ( $object instanceof eZContentObject )
            return $object;
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => $parentNodeID, 'class_identifier' => $class,
                                                                     'remote_id' => $remoteID, 'attributes' => $attributes,
                                                                     'storage_dir' => $storageDir ) );
        if ( !$object instanceof eZContentObject )
            throw new RuntimeException( "could not create $remoteID" );
        eZContentObject::clearCache();
        return eZContentObject::fetch( $object->attribute( 'id' ) );
    }

    /** a test user in the group (made, or found and moved there), with a new random password in $seed; its id */
    protected static function user( $login, $groupNode, array &$seed )
    {
        $user = eZUser::fetchByName( $login );
        if ( $user instanceof eZUser )
        {
            $userID = (int)$user->attribute( 'contentobject_id' );
            $userNode = eZContentObject::fetch( $userID )->attribute( 'main_node' );
            if ( $userNode && (int)$userNode->attribute( 'parent_node_id' ) !== $groupNode )
                eZContentObjectTreeNodeOperations::move( (int)$userNode->attribute( 'node_id' ), $groupNode );
        }
        else
        {
            $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => $groupNode, 'class_identifier' => 'user',
                                                                         'attributes' => array( 'first_name' => 'CF', 'last_name' => substr( $login, 3 ) ) ) );
            if ( !$object instanceof eZContentObject )
                throw new RuntimeException( "could not create the user $login" );
            $userID = (int)$object->attribute( 'id' );
            $user = eZUser::create( $userID );
        }
        $password = 'Cf-' . bin2hex( random_bytes( 8 ) ) . '!';
        $user->setInformation( $userID, $login, $login . '@example.invalid', $password, $password );
        $user->store();
        eZUser::purgeUserCacheByUserId( $userID );
        $seed['users'][$login] = array( 'id' => $userID, 'password' => $password );
        return $userID;
    }

    /** a role made again with these policies and assigned to the user */
    protected static function role( $name, array $policies, $userID )
    {
        $role = eZRole::fetchByName( $name );
        if ( $role instanceof eZRole )
            $role->removeThis();
        $role = eZRole::create( $name );
        $role->store();
        foreach ( $policies as $p )
            $role->appendPolicy( $p[0], $p[1], isset( $p[2] ) ? $p[2] : array() );
        $role->assignToUser( $userID );
        eZUser::purgeUserCacheByUserId( $userID );
    }

    /**
     * @param string $filesDir a writable directory for the image and the binary file
     * @return array objects, nodes (by short name), users (login => id, password), files (image, binary: path,
     *               attribute_id), article_versions and version_versions (version => id, status), urlalias
     */
    public static function seed( $filesDir )
    {
        $db = eZDB::instance();
        $seed = array( 'objects' => array(), 'nodes' => array(), 'users' => array(), 'files' => array() );
        $admin = eZUser::fetchByName( 'admin' );
        if ( !$admin instanceof eZUser )
            throw new RuntimeException( 'no admin user' );
        $admin->loginCurrent();
        $rootNodeID = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' );
        $userPlacement = (int)eZINI::instance()->variable( 'UserSettings', 'DefaultUserPlacement' );

        $usersRoot = (int)eZContentObjectTreeNode::fetch( $userPlacement )->attribute( 'parent_node_id' );
        $groupClass = eZContentClass::fetchByIdentifier( 'user_group' ) ? 'user_group' : 'folder';
        $groupNode = (int)self::object( 'cf-users', $usersRoot, $groupClass, self::name( $groupClass, 'CF Users' ) )->attribute( 'main_node_id' );
        $editorID = self::user( 'cf_editor', $groupNode, $seed );
        self::role( 'cf_editor role', array( array( 'content', '*' ), array( 'user', 'login' ) ), $editorID );
        eZRole::expireCache();
        eZContentObject::clearCache();
        eZUser::fetch( $editorID )->loginCurrent();

        $finder = self::object( 'cf-finder', $rootNodeID, 'folder', self::name( 'folder', 'CF Finder' ) );
        $finderNode = (int)$finder->attribute( 'main_node_id' );
        $open = self::object( 'cf-open', $finderNode, 'folder', self::name( 'folder', 'CF Open' ) );
        $closed = self::object( 'cf-closed', $finderNode, 'folder', self::name( 'folder', 'CF Closed' ) );
        $openNode = (int)$open->attribute( 'main_node_id' );
        $closedNode = (int)$closed->attribute( 'main_node_id' );
        $class = self::articleClass();

        $article = self::object( 'cf-article', $openNode, $class, self::name( $class, 'CF Article' ) );
        if ( (int)$article->attribute( 'current_version' ) === 1 )
        {
            eZContentFunctions::updateAndPublishObject( $article, array( 'attributes' => self::name( $class, 'CF Article' ) ) );
            eZContentObject::clearCache();
            $article = eZContentObject::fetch( $article->attribute( 'id' ) );
            $draft = $article->createNewVersion();
            $draft->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );
            $draft->store();
        }
        foreach ( array( 'trash' => 'CF Trash me', 'delete' => 'CF Delete me', 'version' => 'CF Version me',
                         'purge' => 'CF Purge me', 'secret' => 'CF Secret' ) as $key => $name )
        {
            $old = eZContentObject::fetchByRemoteID( 'cf-' . $key );
            if ( $old instanceof eZContentObject && ( (int)$old->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED
                                                      || ( $key === 'version' && count( $old->versions( false ) ) < 3 ) ) )
            {
                if ( (int)$old->attribute( 'status' ) === eZContentObject::STATUS_PUBLISHED )
                    eZContentObjectTreeNode::removeSubtrees( array( (int)$old->attribute( 'main_node_id' ) ), false );
                else
                    $old->purge();
                eZContentObject::clearCache();
            }
            $o = self::object( 'cf-' . $key, $key === 'secret' ? $closedNode : $openNode, $class, self::name( $class, $name ) );
            $seed['objects'][$key] = (int)$o->attribute( 'id' );
            $seed['nodes'][$key] = (int)$o->attribute( 'main_node_id' );
        }
        $versioned = eZContentObject::fetch( $seed['objects']['version'] );
        for ( $i = 0; $i < 3 && (int)$versioned->attribute( 'current_version' ) < 3; $i++ )
        {
            eZContentFunctions::updateAndPublishObject( $versioned, array( 'attributes' => self::name( $class, 'CF Version me' ) ) );
            eZContentObject::clearCache();
            $versioned = eZContentObject::fetch( $seed['objects']['version'] );
        }

        // a real image and a real binary file
        if ( !is_dir( $filesDir ) )
            mkdir( $filesDir, 0755, true );
        $filesDir = rtrim( $filesDir, '/' ) . '/';
        copy( 'design/admin/images/trash-icon-16x16.gif', $filesDir . 'cf-finder-image.gif' );
        file_put_contents( $filesDir . 'cf-finder-file.txt', "A content finder test file\n" );
        $imageAttribute = self::attribute( 'image', array( 'ezimage' ) );
        if ( $imageAttribute )
        {
            $image = self::object( 'cf-image', $openNode, 'image', self::name( 'image', 'CF Image' ) + array( $imageAttribute => 'cf-finder-image.gif|CF' ), $filesDir );
            $seed['objects']['image'] = (int)$image->attribute( 'id' );
            $rows = $db->arrayQuery( 'SELECT f.filepath, f.contentobject_attribute_id FROM ezimagefile f, ezcontentobject_attribute a'
                                   . ' WHERE a.id = f.contentobject_attribute_id AND a.contentobject_id = ' . (int)$image->attribute( 'id' ) );
            foreach ( $rows as $row )
                $seed['files']['image'][] = array( 'path' => $row['filepath'], 'attribute_id' => (int)$row['contentobject_attribute_id'] );
        }
        $fileAttribute = self::attribute( 'file', array( 'ezbinaryfile' ) );
        if ( $fileAttribute )
        {
            $file = self::object( 'cf-file', $openNode, 'file', self::name( 'file', 'CF File' ) + array( $fileAttribute => 'cf-finder-file.txt' ), $filesDir );
            $seed['objects']['file'] = (int)$file->attribute( 'id' );
            $rows = $db->arrayQuery( 'SELECT b.filename, b.mime_type, b.contentobject_attribute_id FROM ezbinaryfile b, ezcontentobject_attribute a'
                                   . ' WHERE a.id = b.contentobject_attribute_id AND a.version = b.version AND a.contentobject_id = ' . (int)$file->attribute( 'id' ) );
            foreach ( $rows as $row )
                $seed['files']['binary'][] = array( 'path' => eZSys::storageDirectory() . '/original/' . eZBinaryFile::mimeGroup( $row['mime_type'] ) . '/' . $row['filename'],
                                                    'attribute_id' => (int)$row['contentobject_attribute_id'] );
        }

        // the purge candidate goes to the trash, by cf_editor
        $purge = eZContentObject::fetch( $seed['objects']['purge'] );
        if ( (int)$purge->attribute( 'status' ) === eZContentObject::STATUS_PUBLISHED )
            eZContentObjectTreeNode::removeSubtrees( array( (int)$purge->attribute( 'main_node_id' ) ), true );

        $seed['objects'] += array( 'finder' => (int)$finder->attribute( 'id' ), 'open' => (int)$open->attribute( 'id' ),
                                   'closed' => (int)$closed->attribute( 'id' ), 'article' => (int)$article->attribute( 'id' ) );
        $seed['nodes'] += array( 'finder' => $finderNode, 'open' => $openNode, 'closed' => $closedNode, 'article' => (int)$article->attribute( 'main_node_id' ) );
        foreach ( array( 'article' => 'article_versions', 'version' => 'version_versions' ) as $key => $field )
        {
            $seed[$field] = array();
            foreach ( eZContentObject::fetch( $seed['objects'][$key] )->versions( false ) as $v )
                $seed[$field][(int)$v['version']] = array( 'id' => (int)$v['id'], 'status' => (int)$v['status'] );
        }
        $seed['urlalias']['article'] = (string)eZContentObjectTreeNode::fetch( $seed['nodes']['article'] )->urlAlias();

        // the roles name the folders of this run
        $admin->loginCurrent();
        $openPath = (string)eZContentObjectTreeNode::fetch( $openNode )->attribute( 'path_string' );
        $inOpen = array( 'Subtree' => array( $openPath ) );
        $roles = array(
            'cf_reader' => array( array( 'contentfinder', 'search' ), array( 'content', 'read', $inOpen ), array( 'content', 'versionread', $inOpen ),
                                  array( 'user', 'login' ) ),
            'cf_noversions' => array( array( 'contentfinder', 'search' ), array( 'content', 'read' ), array( 'user', 'login' ) ),
            'cf_nofinder' => array( array( 'content', 'read' ), array( 'user', 'login' ) ),
            'cf_remover' => array( array( 'contentfinder', 'search' ), array( 'contentfinder', 'delete' ), array( 'content', 'read' ),
                                   array( 'content', 'versionread' ), array( 'content', 'remove', $inOpen ), array( 'content', 'edit', $inOpen ),
                                   array( 'content', 'versionremove', $inOpen ), array( 'content', 'restore' ), array( 'content', 'cleantrash' ),
                                   array( 'user', 'login' ) ),
            'cf_searcher' => array( array( 'contentfinder', 'search' ), array( 'content', 'read' ), array( 'content', 'versionread' ),
                                    array( 'content', 'restore' ), array( 'user', 'login' ) ),
        );
        foreach ( $roles as $login => $policies )
            self::role( $login . ' role', $policies, self::user( $login, $groupNode, $seed ) );
        eZRole::expireCache();
        eZContentObject::clearCache();
        return $seed;
    }
}
