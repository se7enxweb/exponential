<?php
/**
 * File containing the expHandlerWizard class.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

require_once 'kernel/setup/expextensionwizard.php';

/**
 * Builds a handler: a class named by an ini setting that the kernel loads in
 * place of its own.
 *
 * Several of this system's parts are swapped out the same way. An ini setting
 * gives an alias, eZExtension::getHandlerClass turns the alias into a class,
 * and that class has to satisfy a contract nothing states except the base class
 * it is expected to extend. The three moving parts - which ini, which alias,
 * which methods - are different for every one of them and written down nowhere
 * together.
 *
 * Each kind is a recipe here: what it replaces, what it has to implement, what
 * registers it, and what a working skeleton of it looks like. The recipes are
 * separate tools on the RAD page, so a session handler and a mail transport are
 * each their own thing rather than two settings of one form - but they are
 * built by the same engine, so a fix to one is a fix to all of them.
 */
class expHandlerWizard extends expExtensionWizard
{
    /**
     * What wrote the files, for the head of each one.
     *
     * @return string
     */
    public static function wizardName()
    {
        return 'handler extension wizard';
    }
    /**
     * The kinds of handler this can build.
     *
     * @return array key => recipe
     */
    public static function kinds()
    {
        return array(

        'session' => array(
            'title'    => 'Session handler',
            'what'     => 'Where sessions are kept, how they are cleaned up, and what happens when a user logs in or out.',
            'why'      => 'The default lets php keep sessions wherever php.ini says. A handler of your own can put them in a database, in a cache, or anywhere shared between servers.',
            'base'     => 'ezpSessionHandler',
            'source'   => 'lib/ezsession/classes/ezpsessionhandler.php',
            'ini'      => 'site.ini',
            'section'  => 'Session',
            'variable' => 'Handler',
            'aliased'  => false,
            'suffix'   => 'sessionhandler',
            'note'     => 'Also set ForceStart=enabled in the same section if the handler needs a session on every request.',
            'methods'  => array(
                array( 'name' => 'read', 'signature' => 'read( $sessionId )',
                       'returns' => "''",
                       'what' => 'The session data for this id, or an empty string when there is none. Never null: php reads the return value as the whole session.' ),
                array( 'name' => 'write', 'signature' => 'write( $sessionId, $sessionData )',
                       'returns' => 'true',
                       'what' => 'Stores the data against the id. Called at the end of the request, after output has been sent, so it cannot report to the user.' ),
                array( 'name' => 'destroy', 'signature' => 'destroy( $sessionId )',
                       'returns' => 'true',
                       'what' => 'Forgets one session. Called on logout.' ),
                array( 'name' => 'regenerate', 'signature' => 'regenerate( $updateBackendData = true )',
                       'returns' => 'true',
                       'what' => 'Gives the session a new id, keeping its data. Called on login, so that a session id seen before logging in is not the one that is logged in.' ),
                array( 'name' => 'gc', 'signature' => 'gc( $maxLifeTime )',
                       'returns' => 'true',
                       'what' => 'Removes sessions older than the lifetime. Called by php at random, and by the session cronjob.' ),
                array( 'name' => 'cleanup', 'signature' => 'cleanup()',
                       'returns' => 'true',
                       'what' => 'Removes every session. Called when the caches are cleared.' ),
                array( 'name' => 'deleteByUserIDs', 'signature' => 'deleteByUserIDs( array $userIDArray )',
                       'returns' => 'true',
                       'what' => 'Removes the sessions of these users. Called when a user is disabled or removed, and this is the method that makes that take effect immediately.' ) ) ),

        'mail' => array(
            'title'    => 'Mail transport',
            'what'     => 'How mail leaves the system.',
            'why'      => 'The default hands mail to php, which hands it to the machine. A transport of your own can send it through an api, queue it, or write it to disk on a machine that must not send anything.',
            'base'     => 'eZMailTransport',
            'source'   => 'lib/ezutils/classes/ezmailtransport.php',
            'ini'      => 'site.ini',
            'section'  => 'MailSettings',
            'variable' => 'TransportAlias',
            'aliased'  => true,
            'suffix'   => 'transport',
            'note'     => 'Set Transport in the same section to the alias, or the default is still used.',
            'methods'  => array(
                array( 'name' => 'sendMail', 'signature' => 'sendMail( eZMail $mail )',
                       'returns' => 'true',
                       'what' => 'Sends one mail and says whether it went. Returning false is how a failure is reported; nothing else is read.' ) ) ),


        'search' => array(
            'title'    => 'Search engine',
            'what'     => 'What indexes content as it is published, and what answers when somebody searches.',
            'why'      => 'The engine that ships keeps its index in the database, which is fine until the content or the queries outgrow it. An engine of your own can hand indexing and searching to something built for it.',
            'base'     => 'ezpSearchEngine',
            'source'   => 'kernel/private/interfaces/ezpsearchengine.php',
            'ini'      => 'site.ini',
            'section'  => 'SearchSettings',
            'variable' => 'SearchEngine',
            'aliased'  => false,
            'suffix'   => 'searchengine',
            'interface' => true,
            'note'     => 'After switching engines the index has to be rebuilt: php bin/php/updatesearchindex.php. Until then a search answers from an index the new engine never wrote.',
            'methods'  => array(
                array( 'name' => 'needCommit', 'signature' => 'needCommit()',
                       'returns' => 'false',
                       'what' => 'Whether this engine has to be told when a batch of changes is finished. Say false and commit() is never called.' ),
                array( 'name' => 'needRemoveWithUpdate', 'signature' => 'needRemoveWithUpdate()',
                       'returns' => 'true',
                       'what' => 'Whether an object has to be removed from the index before it is added again. Say false only if adding replaces rather than duplicates.' ),
                array( 'name' => 'addObject', 'signature' => 'addObject( $contentObject, $commit = true )',
                       'returns' => 'true',
                       'what' => 'Indexes one object, with every attribute of every version that should be searchable. Called on publish, and by the reindex script for everything at once.' ),
                array( 'name' => 'removeObject', 'signature' => 'removeObject( $contentObject, $commit = null )',
                       'returns' => 'true',
                       'what' => 'Takes one object out of the index. Called when it is removed, and before it is added again when needRemoveWithUpdate says so.' ),
                array( 'name' => 'removeObjectById', 'signature' => 'removeObjectById( $contentObjectId, $commit = null )',
                       'returns' => 'true',
                       'what' => 'The same, by id alone - used when the object itself has already gone and cannot be fetched.' ),
                array( 'name' => 'search', 'signature' => 'search( $searchText, $params = array(), $searchTypes = array() )',
                       'returns' => "array( 'SearchResult' => array(), 'SearchCount' => 0, 'StopWordArray' => array() )",
                       'what' => 'Answers a search. The shape of what comes back is fixed: SearchResult holds the rows, SearchCount the total before paging, StopWordArray the words that were ignored.' ),
                array( 'name' => 'supportedSearchTypes', 'signature' => 'supportedSearchTypes()',
                       'returns' => 'array()',
                       'what' => 'Which kinds of narrowing this engine can do - by class, by section, by date. The advanced search form is built from this.' ),
                array( 'name' => 'commit', 'signature' => 'commit()',
                       'returns' => 'true',
                       'what' => 'Makes pending changes visible. Only called when needCommit() says it is wanted.' ) ) ),

        'staticcache' => array(
            'title'    => 'Static cache handler',
            'what'     => 'What writes pages to disk so the web server can serve them without php.',
            'why'      => 'The handler that ships writes files under a directory. One of your own could write somewhere else, push to a cache in front of the site, or record what it would have done without doing it.',
            'base'     => 'ezpStaticCache',
            'source'   => 'kernel/private/interfaces/ezpstaticcache.php',
            'ini'      => 'site.ini',
            'section'  => 'ContentSettings',
            'variable' => 'StaticCacheHandler',
            'aliased'  => false,
            'interface' => true,
            'suffix'   => 'staticcache',
            'note'     => 'StaticCache=enabled in the same section is what switches static caching on at all; this setting only decides which handler does it.',
            'methods'  => array(
                array( 'name' => 'generateAlwaysUpdatedCache', 'signature' => 'generateAlwaysUpdatedCache( $quiet = false, $cli = false, $delay = true )',
                       'returns' => 'true',
                       'what' => 'Rewrites the pages that are marked as always needing rewriting - the front page and anything else listed in staticcache.ini.' ),
                array( 'name' => 'generateNodeListCache', 'signature' => 'generateNodeListCache( $nodeList )',
                       'returns' => 'true',
                       'what' => 'Rewrites the pages of these nodes. Called when content changes, with the nodes the change affected.' ),
                array( 'name' => 'generateCache', 'signature' => 'generateCache( $force = false, $quiet = false, $cli = false, $delay = true )',
                       'returns' => 'true',
                       'what' => 'Writes the whole cache. This is what the Create new button on the cache page calls.' ),
                array( 'name' => 'cacheURL', 'signature' => 'cacheURL( $url, $nodeID = false, $skipExisting = false, $delay = true )',
                       'returns' => 'true',
                       'what' => 'Writes one address. Everything above ends up here.' ),
                array( 'name' => 'removeURL', 'signature' => 'removeURL( $url )',
                       'returns' => 'true',
                       'what' => 'Forgets one address, so the next visitor gets it from php again.' ),
                array( 'name' => 'executeActions', 'signature' => 'executeActions()',
                       'returns' => 'true',
                       'what' => 'Carries out the writes that were put off while the request was still running. Static: called at the end of the request, and by the cronjob.',
                       'static' => true ) ) ),

        'binaryfile' => array(
            'title'    => 'Binary file handler',
            'what'     => 'How an uploaded file is stored, and how it is handed back to somebody downloading it.',
            'why'      => 'The default stores files under var/ and sends them with php. A handler of your own can put them somewhere else, hand the download to the web server, or check who is asking before it answers.',
            'base'     => 'eZBinaryFileHandler',
            'source'   => 'kernel/classes/ezbinaryfilehandler.php',
            'ini'      => 'file.ini',
            'section'  => 'BinaryFileSettings',
            'variable' => 'Handler',
            'aliased'  => false,
            'suffix'   => 'binaryfilehandler',
            'note'     => 'The handler is constructed with an identifier, a name and a handle type; the generated constructor passes them up to the base.',
            'methods'  => array(
                array( 'name' => 'handleUpload', 'signature' => 'handleUpload()',
                       'returns' => 'false',
                       'what' => 'Takes an uploaded file wherever this handler keeps files. Returning false leaves the default storage to deal with it.' ),
                array( 'name' => 'handleDownload', 'signature' => 'handleDownload( $contentObject, $contentObjectAttribute, $type )',
                       'returns' => 'eZBinaryFileHandler::RESULT_UNAVAILABLE',
                       'what' => 'Decides what happens when somebody asks for the file. Answer RESULT_UNAVAILABLE to leave it to the default, or send it yourself and answer that it is done.' ),
                array( 'name' => 'handleFileDownload', 'signature' => 'handleFileDownload( $contentObject, $contentObjectAttribute, $type, $mimeData )',
                       'returns' => 'eZBinaryFileHandler::RESULT_UNAVAILABLE',
                       'what' => 'The same, once the file has been found and its type worked out. This is where a handler that hands off to the web server does so.' ) ) ),

        'cluster' => array(
            'title'    => 'Cluster file handler',
            'what'     => 'Where files live when more than one server serves the same site: every image, every binary, and every cache file the kernel writes.',
            'why'      => 'With one server, files on disk are fine. With several, each would write its own copy and serve stale ones. A cluster handler puts them somewhere all the servers share.',
            'base'     => 'eZFSFileHandler',
            'source'   => 'kernel/classes/clusterfilehandlers/ezfsfilehandler.php',
            'ini'      => 'file.ini',
            'section'  => 'ClusteringSettings',
            'variable' => 'FileHandler',
            'aliased'  => false,
            'suffix'   => 'filehandler',
            'override' => true,
            'note'     => 'eZClusterFileHandlerInterface has forty two methods. Extending eZFSFileHandler means only the ones below have to be thought about; the rest keep doing what they do on a filesystem. Every method generated calls up to it, so the site behaves exactly as before until one is changed.',
            'methods'  => array(
                array( 'name' => 'fileStore', 'signature' => 'fileStore( $filePath, $scope = false, $delete = false, $datatype = false )',
                       'returns' => 'null',
                       'what' => 'Takes a file that is already on disk into wherever this handler keeps things. $scope says what it is for - image, binaryfile, viewcache - and is worth keeping, because purging works on it.' ),
                array( 'name' => 'fileStoreContents', 'signature' => 'fileStoreContents( $filePath, $contents, $scope = false, $datatype = false )',
                       'returns' => 'null',
                       'what' => 'The same, from a string rather than a file. This is what cache writing goes through, so it is called far more often than the one above.' ),
                array( 'name' => 'fileFetch', 'signature' => 'fileFetch( $filePath )',
                       'returns' => 'null',
                       'what' => 'Brings a file back to local disk so php can read it. Called before anything that needs a real path.' ),
                array( 'name' => 'fetchContents', 'signature' => 'fetchContents()',
                       'returns' => 'false',
                       'what' => 'The contents of this handler\'s file, without putting it on disk first. The quick path, and the one caches use.' ),
                array( 'name' => 'fileExists', 'signature' => 'fileExists( $path )',
                       'returns' => 'false',
                       'what' => 'Whether a file is there. Called constantly; whatever this does, it has to be cheap.' ),
                array( 'name' => 'stat', 'signature' => 'stat()',
                       'returns' => 'false',
                       'what' => 'Size, modification time and the rest, in the shape php\'s own stat() returns. The kernel reads mtime from this to decide what is stale.' ),
                array( 'name' => 'fileDelete', 'signature' => 'fileDelete( $path, $fnamePart = false )',
                       'returns' => 'null',
                       'what' => 'Removes a file, or everything whose name starts with $fnamePart when it is given.' ),
                array( 'name' => 'purge', 'signature' => 'purge( $printCallback = false, $microsleep = false, $max = false, $expiry = false )',
                       'returns' => 'null',
                       'what' => 'Really removes what was only marked as deleted. Called by the cluster purge cronjob rather than during a request.' ),
                array( 'name' => 'requiresClusterizing', 'signature' => 'requiresClusterizing()',
                       'returns' => 'true',
                       'what' => 'Whether a file written locally has to be handed to this handler afterwards. False on a filesystem; true for anything shared.' ),
                array( 'name' => 'requiresPurge', 'signature' => 'requiresPurge()',
                       'returns' => 'true',
                       'what' => 'Whether deleting only marks, so that purging is needed later. Say true and the purge cronjob has to be scheduled.' ),
                array( 'name' => 'hasStaleCacheSupport', 'signature' => 'hasStaleCacheSupport()',
                       'returns' => 'false',
                       'what' => 'Whether an expired cache file can still be served while a new one is being made. Says whether a slow regeneration blocks visitors or not.' ),
                array( 'name' => 'startCacheGeneration', 'signature' => 'startCacheGeneration()',
                       'returns' => 'true',
                       'what' => 'Claims the right to build this cache entry, so that ten requests arriving together build it once. Returning something other than true means somebody else is already building it.' ),
                array( 'name' => 'endCacheGeneration', 'signature' => 'endCacheGeneration( $rename = true )',
                       'returns' => 'true',
                       'what' => 'Puts the finished cache entry in place and gives up the claim.' ),
                array( 'name' => 'abortCacheGeneration', 'signature' => 'abortCacheGeneration()',
                       'returns' => 'true',
                       'what' => 'Gives up the claim without putting anything in place. Called when generating threw, and forgetting it is how a cache entry stays locked for ever.' ) ) ),

        'dfsbackend' => array(
            'title'    => 'DFS backend',
            'what'     => 'Where the DFS cluster handler puts the bytes, once the database has been told the file exists.',
            'why'      => 'The DFS handler keeps an index of files in the database and the files themselves somewhere else. That somewhere else is this: a mounted filesystem by default, but it could be object storage or anything reachable.',
            'base'     => 'eZDFSFileHandlerDFSBackendInterface',
            'source'   => 'kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendinterface.php',
            'ini'      => 'file.ini',
            'section'  => 'eZDFSClusteringSettings',
            'variable' => 'DFSBackend',
            'aliased'  => false,
            'interface' => true,
            'suffix'   => 'dfsbackend',
            'note'     => 'This is only reached when FileHandler is eZDFSFileHandler. The database side is separate: DBBackend in the same section.',
            'methods'  => array(
                array( 'name' => 'copyFromDFSToDFS', 'signature' => 'copyFromDFSToDFS( $srcFilePath, $dstFilePath )',
                       'returns' => 'false',
                       'what' => 'Copies one stored file to another name without bringing it back through php. On storage that can copy server-side, this is the one worth doing properly.' ),
                array( 'name' => 'copyFromDFS', 'signature' => 'copyFromDFS( $srcFilePath, $dstFilePath = false )',
                       'returns' => 'false',
                       'what' => 'Brings a stored file down to local disk.' ),
                array( 'name' => 'copyToDFS', 'signature' => 'copyToDFS( $srcFilePath, $dstFilePath = false )',
                       'returns' => 'false',
                       'what' => 'Puts a local file into storage.' ),
                array( 'name' => 'delete', 'signature' => 'delete( $filePath )',
                       'returns' => 'false',
                       'what' => 'Removes a stored file. May be given one path or a list of them.' ),
                array( 'name' => 'passthrough', 'signature' => 'passthrough( $filePath, $startOffset = 0, $length = false )',
                       'returns' => 'false',
                       'what' => 'Sends a stored file straight to the browser, honouring a byte range. This is what makes a large download work without loading it into memory.' ),
                array( 'name' => 'getContents', 'signature' => 'getContents( $filePath )',
                       'returns' => 'false',
                       'what' => 'The whole file as a string. Fine for a cache entry, wrong for a video.' ),
                array( 'name' => 'createFileOnDFS', 'signature' => 'createFileOnDFS( $filePath, $contents )',
                       'returns' => 'false',
                       'what' => 'Writes a file from a string.' ),
                array( 'name' => 'renameOnDFS', 'signature' => 'renameOnDFS( $oldPath, $newPath )',
                       'returns' => 'false',
                       'what' => 'Moves a stored file. Wants to be atomic: the cache handler renames a finished entry into place and expects nobody to see it half done.' ),
                array( 'name' => 'existsOnDFS', 'signature' => 'existsOnDFS( $filePath )',
                       'returns' => 'false',
                       'what' => 'Whether a file is there.' ),
                array( 'name' => 'getDfsFileSize', 'signature' => 'getDfsFileSize( $filePath )',
                       'returns' => 'false',
                       'what' => 'How big it is, without fetching it.' ),
                array( 'name' => 'getFilesList', 'signature' => 'getFilesList( $basePath )',
                       'returns' => 'array()',
                       'what' => 'Everything stored under a path. Used when a whole directory has to go.' ),
                array( 'name' => 'applyServerUri', 'signature' => 'applyServerUri( $filePath )',
                       'returns' => '$filePath',
                       'what' => 'Turns a stored path into an address a browser can be sent to, when the storage can serve directly. Return the path unchanged to keep serving through php.' ) ) ),

        'dfsdbbackend' => array(
            'title'    => 'DFS database backend',
            'what'     => 'The other half of DFS: the index of which files exist, how big they are, and which are being generated right now.',
            'why'      => 'This is what stops two servers building the same cache entry at the same time, and what makes a delete on one server take effect on all of them. Extending the backend that ships means only the parts that need to differ have to be written; every method below already works.',
            'base'     => 'eZDFSFileHandlerMySQLiBackend',
            'source'   => 'kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php',
            'ini'      => 'file.ini',
            'section'  => 'eZDFSClusteringSettings',
            'variable' => 'DBBackend',
            'aliased'  => false,
            'suffix'   => 'dfsdbbackend',
            'override' => true,
            'note'     => 'The DFS backend and the database backend are separate settings. Changing this one does not change where the bytes go.',
            'methods'  => array(
                array( 'name' => '_connect', 'signature' => '_connect()',
                       'returns' => 'true',
                       'what' => 'Opens the connection the rest of the methods use. Called once, lazily, on the first thing that needs the index.' ),
                array( 'name' => '_exists', 'signature' => '_exists( $filePath, $fname = false, $ignoreExpiredFiles = true, $checkOnDFS = false )',
                       'returns' => 'false',
                       'what' => 'Whether the index knows this file. The expiry argument is what makes a stale entry look absent without being deleted.' ),
                array( 'name' => '_delete', 'signature' => '_delete( $filePath, $insideOfTransaction = false, $fname = false )',
                       'returns' => 'true',
                       'what' => 'Marks one file gone. It is a mark, not a removal: the row stays until the purge cronjob takes it, so that every server sees the deletion.' ),
                array( 'name' => '_deleteByLike', 'signature' => '_deleteByLike( $like, $fname = false )',
                       'returns' => 'true',
                       'what' => 'Marks everything matching a pattern gone. This is what a cache clear turns into, so it is worth it being fast.' ),
                array( 'name' => '_purge', 'signature' => '_purge( $filePath, $onlyExpired = false, $expiry = false, $fname = false )',
                       'returns' => 'true',
                       'what' => 'Really removes what was marked gone, index row and stored bytes together. Run by the clusterpurge cronjob.' ),
                array( 'name' => '_startCacheGeneration', 'signature' => '_startCacheGeneration( $filePath, $generatingFilePath )',
                       'returns' => 'true',
                       'what' => 'Claims the right to build one cache entry. Must be atomic across servers: two of them asking at once, only one may be told yes. Everything else here is bookkeeping; this is the part that is hard.' ),
                array( 'name' => '_endCacheGeneration', 'signature' => '_endCacheGeneration( $filePath, $generatingFilePath, $rename )',
                       'returns' => 'true',
                       'what' => 'Puts the finished entry in place and gives up the claim.' ),
                array( 'name' => '_abortCacheGeneration', 'signature' => '_abortCacheGeneration( $generatingFilePath )',
                       'returns' => 'true',
                       'what' => 'Gives up the claim without an entry. If this is ever missed, that entry stays claimed and nothing rebuilds it.' ),
                array( 'name' => '_fetch', 'signature' => '_fetch( $filePath, $uniqueName = false )',
                       'returns' => 'true',
                       'what' => 'Brings a file to the local disk so php can read it as a file.' ),
                array( 'name' => '_store', 'signature' => '_store( $filePath, $datatype, $scope, $fname = false )',
                       'returns' => 'true',
                       'what' => 'Indexes a local file and sends the bytes to the DFS backend.' ),
                array( 'name' => '_storeContents', 'signature' => '_storeContents( $filePath, $contents, $scope = false, $datatype = false, $mtime = false, $fname = false )',
                       'returns' => 'true',
                       'what' => 'The same from a string, without a local file in the middle.' ) ) ),

        'dbhandler' => array(
            'title'    => 'Database handler',
            'what'     => 'The layer every query in the system goes through on its way to the server.',
            'why'      => 'Extending the handler that ships is how a read goes to a replica, a query gets logged or timed, or a table name gets rewritten - without touching a single caller. Writing one from eZDBInterface instead means ninety methods, and is only worth it for a database nothing supports yet.',
            'base'     => 'eZMySQLiDB',
            'source'   => 'lib/ezdb/classes/ezmysqlidb.php',
            'ini'      => 'site.ini',
            'section'  => 'DatabaseSettings',
            'variable' => 'ImplementationAlias',
            'aliased'  => true,
            'suffix'   => 'db',
            'override' => true,
            'note'     => 'Set Implementation in the same section to the alias as well, or the default handler is still the one that is built.',
            'methods'  => array(
                array( 'name' => 'query', 'signature' => 'query( $sql, $server = false )',
                       'returns' => 'false',
                       'what' => 'Runs one statement and gives back whatever the driver gives back. Every write in the system arrives here. The server argument is how a caller asks for the replica rather than the master.' ),
                array( 'name' => 'arrayQuery', 'signature' => 'arrayQuery( $sql, $params = array(), $server = false )',
                       'returns' => 'array()',
                       'what' => 'Runs one statement and gives back rows as arrays. Every read in the system arrives here, so this is where a query log or a slow query timer belongs.' ),
                array( 'name' => 'escapeString', 'signature' => 'escapeString( $str )',
                       'returns' => "''",
                       'what' => 'Makes a value safe to put inside a statement. Never weaken this: it is the one thing standing between user input and the database.' ),
                array( 'name' => 'begin', 'signature' => 'begin()',
                       'returns' => 'true',
                       'what' => 'Starts a transaction. eZ nests these by counting, so only the outermost one really starts anything.' ),
                array( 'name' => 'commit', 'signature' => 'commit()',
                       'returns' => 'true',
                       'what' => 'Ends the outermost transaction and keeps the work.' ),
                array( 'name' => 'rollback', 'signature' => 'rollback()',
                       'returns' => 'true',
                       'what' => 'Ends the outermost transaction and throws the work away.' ),
                array( 'name' => 'lastSerialID', 'signature' => 'lastSerialID( $table = false, $column = false )',
                       'returns' => 'false',
                       'what' => 'The id the last insert was given. eZPersistentObject reads this straight after every insert, so getting it wrong breaks everything quietly.' ),
                array( 'name' => 'close', 'signature' => 'close()',
                       'returns' => 'true',
                       'what' => 'Lets the connection go, at the end of the request.' ) ) ),

        'notificationtype' => array(
            'title'    => 'Notification event type',
            'what'     => 'A new kind of thing the system can notify people about.',
            'why'      => 'The four that ship cover publishing and collaboration. A type of your own is how anything else - an order placed, a form filled in, a job finished - becomes something a user can subscribe to and be told about, through the machinery that already exists for digests, transports and subscriptions.',
            'base'     => 'eZNotificationEventType',
            'source'   => 'kernel/classes/notification/eznotificationeventtype.php',
            'ini'      => 'notification.ini',
            'section'  => 'NotificationEventTypeSettings',
            'variable' => 'AvailableNotificationEventTypes',
            'aliased'  => false,
            'appended' => true,
            'path'     => 'notificationtypes/%alias%/%alias%type.php',
            'extra'    => array(
                array( 'variable' => 'ExtensionDirectories[]',
                       'value'    => '%extension%',
                       'what'     => 'The extension whose notificationtypes/ directory is searched. Without this line the class above is never looked for, however correctly it is named.' ) ),
            'suffix'   => 'type',
            'note'     => 'The file is found by its path, not by the autoloader: it must be at exactly the place named above or the type is reported missing.',
            'constructor' => array(
                'what' => 'The kernel builds this with no arguments and the type has to name itself. The string must be the one listed in notification.ini.',
                'arguments' => 'self::TYPE' ),
            'constants' => array(
                array( 'name' => 'TYPE', 'value' => '%alias%',
                       'what' => 'The name in notification.ini, in the directory path, and in every stored event row. Changing it after events exist orphans them.' ) ),
            'methods'  => array(
                array( 'name' => 'eventDescription', 'signature' => 'eventDescription()',
                       'returns' => "'Something happened'",
                       'what' => 'One line saying what this type is, shown wherever a user picks what to be notified about.' ),
                array( 'name' => 'initializeEvent', 'signature' => 'initializeEvent( $event, $params )',
                       'returns' => 'null',
                       'what' => 'Puts what is known at the moment the event is raised onto the event row, with $event->setAttribute(). Whatever is not stored here is not available later: execute() runs from the cronjob, long after, in another request.' ),
                array( 'name' => 'execute', 'signature' => 'execute( $event )',
                       'returns' => 'eZNotificationEventType::STATUS_ACCEPTED',
                       'what' => 'Works out who should be told and adds them to the event with $event->addCollectionItem(). Return STATUS_ACCEPTED when done, or STATUS_REJECTED to have the event dropped. Runs from the notification cronjob.' ),
                array( 'name' => 'eventContent', 'signature' => 'eventContent( $event )',
                       'returns' => 'array()',
                       'what' => 'What the notification template is given. Keys become template variables, so this is where a subject line, a link and a name come from.' ) ) ),

        'notificationhandler' => array(
            'title'    => 'Notification handler',
            'what'     => 'What decides who gets told about an event, and turns it into something sent.',
            'why'      => 'A type says an event happened; a handler says who cares. The three that ship do subtree subscriptions, digests and collaboration. A handler of your own is how a rule of any other shape - everyone in a role, everyone who bought something, everyone on a list held elsewhere - gets its own settings tab in the user profile and its own place in the digest.',
            'base'     => 'eZNotificationEventHandler',
            'source'   => 'kernel/classes/notification/eznotificationeventhandler.php',
            'ini'      => 'notification.ini',
            'section'  => 'NotificationEventHandlerSettings',
            'variable' => 'AvailableNotificationEventTypes',
            'aliased'  => false,
            'appended' => true,
            'path'     => 'notification/handler/%alias%/%alias%handler.php',
            'extra'    => array(
                array( 'variable' => 'ExtensionDirectories[]',
                       'value'    => '%extension%',
                       'what'     => 'The extension whose notification/handler/ directory is searched. Without this line the class above is never looked for, however correctly it is named.' ) ),
            'suffix'   => 'handler',
            'note'     => 'The variable really is AvailableNotificationEventTypes in this section too, not AvailableNotificationEventHandlers. It is a quirk of the kernel, not a mistake here.',
            'constructor' => array(
                'what' => 'The kernel builds this with no arguments. The first string is the name in notification.ini and in the path; the second is what a user sees.',
                'arguments' => "self::HANDLER, 'Notification handler'" ),
            'constants' => array(
                array( 'name' => 'HANDLER', 'value' => '%alias%',
                       'what' => 'The name in notification.ini and in the directory path. The two must agree or the handler is never found.' ) ),
            'methods'  => array(
                array( 'name' => 'handle', 'signature' => 'handle( $event )',
                       'returns' => 'eZNotificationEventHandler::STATUS_ACCEPTED',
                       'what' => 'Looks at one event and adds whoever should hear about it, with $event->addCollectionItem(). Called once per event by the notification cronjob. Return STATUS_ACCEPTED when done.' ),
                array( 'name' => 'fetchHttpInput', 'signature' => 'fetchHttpInput( $http, $module )',
                       'returns' => 'null',
                       'what' => 'Reads this handler\'s own part of the notification settings form the user just sent. Only read what belongs to this handler, and prefix the field names, or two handlers will fight over the same field.' ),
                array( 'name' => 'storeSettings', 'signature' => 'storeSettings( $http, $module )',
                       'returns' => 'null',
                       'what' => 'Saves what fetchHttpInput() read. The two are separate so that a page with several handlers on it validates everything before storing anything.' ),
                array( 'name' => 'cleanup', 'signature' => 'cleanup()',
                       'returns' => 'null',
                       'what' => 'Forgets everything this handler holds. Called when notification data is being wiped, so anything stored in a table of your own has to be dealt with here.' ) ) ),

        'packagehandler' => array(
            'title'    => 'Package handler',
            'what'     => 'A new kind of thing a package can carry, install and uninstall.',
            'why'      => 'Packages already carry classes, objects, files and ini settings. A handler of your own is how anything else an extension owns - rows in its own tables, a set of roles, a configured workflow - travels between installations in the same package rather than in a document telling somebody what to click.',
            'base'     => 'eZPackageHandler',
            'source'   => 'kernel/classes/ezpackagehandler.php',
            'ini'      => 'package.ini',
            'section'  => 'PackageSettings',
            'variable' => 'HandlerAlias',
            'aliased'  => true,
            'suffix'   => 'packagehandler',
            'note'     => 'The alias is what goes in the type attribute of an <install> element inside package.xml, so it is part of the package format and cannot be changed once packages exist.',
            'constructor' => array(
                'what' => 'The kernel builds this with no arguments. The string is the alias in package.ini, and the array says which steps the handler takes part in.',
                'arguments' => "self::HANDLER, array( 'extract-install-content' => true )" ),
            'constants' => array(
                array( 'name' => 'HANDLER', 'value' => '%alias%',
                       'what' => 'The alias in package.ini and the type in package.xml.' ) ),
            'methods'  => array(
                array( 'name' => 'install', 'signature' => 'install( $package, $installType, $parameters, $name, $os, $filename, $subdirectory, $content, &$installParameters, &$installData )',
                       'returns' => 'true',
                       'what' => 'Puts one item into this installation. $content is the DOM element from package.xml; $package->path() is where the files are. Return false to stop the install and report a failure.' ),
                array( 'name' => 'uninstall', 'signature' => 'uninstall( $package, $installType, $parameters, $name, $os, $filename, $subdirectory, $content, &$installParameters, &$installData )',
                       'returns' => 'true',
                       'what' => 'Takes it out again. Has to cope with the item already being gone, or changed since: an uninstall that fails halfway is worse than one that does nothing.' ),
                array( 'name' => 'explainInstallItem', 'signature' => 'explainInstallItem( $package, $installItem, $requestedInfo = array() )',
                       'returns' => 'array()',
                       'what' => 'What the admin shows about this item before installing it. Return at least name and description keys, so somebody can see what they are about to accept.' ),
                array( 'name' => 'add', 'signature' => 'add( $packageType, $package, $cli, $parameters )',
                       'returns' => 'true',
                       'what' => 'Puts one item into a package being built. The other direction from install().' ),
                array( 'name' => 'createInstallNode', 'signature' => 'createInstallNode( $package, $installNode, $installItem, $installType )',
                       'returns' => 'true',
                       'what' => 'Writes this item into package.xml, as children of $installNode. Whatever is written here is all install() will get back.' ),
                array( 'name' => 'parseInstallNode', 'signature' => 'parseInstallNode( $package, $installNode, &$installParameters, $isInstall )',
                       'returns' => 'true',
                       'what' => 'Reads it back out of package.xml. Must survive a file written by a newer version of the handler, or by somebody by hand.' ) ) ),

        'restprovider' => array(
            'title'    => 'REST provider',
            'what'     => 'A set of REST routes and the controller behind them.',
            'why'      => 'The REST layer that ships answers about content. A provider of your own puts routes of any shape under the same api, with the same authentication, the same output formats and the same error handling, rather than a module view pretending to be an api.',
            'base'     => 'ezpRestProviderInterface',
            'source'   => 'kernel/private/rest/classes/interfaces/rest_provider.php',
            'ini'      => 'rest.ini',
            'section'  => 'ApiProvider',
            'variable' => 'ProviderClass',
            'aliased'  => true,
            'interface' => true,
            'suffix'   => 'restprovider',
            'note'     => 'The alias is the first part of the path: a provider registered as "shop" answers under /api/shop/. Routes are matched in the order the provider returns them, so put the specific ones first.',
            'methods'  => array(
                array( 'name' => 'getRoutes', 'signature' => 'getRoutes()',
                       'returns' => 'array()',
                       'what' => 'The routes this provider answers, as ezpRestVersionedRoute objects wrapping ezcMvcRailsRoute. Each names a path pattern, the controller class, and the method on it. An empty array means the provider answers nothing, which is what it does until this is written.' ),
                array( 'name' => 'getViewController', 'signature' => 'getViewController()',
                       'returns' => "'ezpRestViewController'",
                       'what' => 'What turns a result into a response body. Returning the default gives json and xml through content negotiation; a controller of your own is how any other format is served.' ) ) ),

        'xmlinput' => array(
            'title'    => 'XML text input handler',
            'what'     => 'What turns what an editor typed into the stored XML of an ezxmltext attribute.',
            'why'      => 'This is the only place the editing format and the stored format meet. A handler of your own is how a different editor, a different markup, or a stricter set of rules about what may be stored gets in - without changing the datatype or anything that reads it.',
            'base'     => 'eZSimplifiedXMLInput',
            'source'   => 'kernel/classes/datatypes/ezxmltext/handlers/input/ezsimplifiedxmlinput.php',
            'ini'      => 'ezxml.ini',
            'section'  => 'InputSettings',
            'variable' => 'HandlerClass',
            'aliased'  => false,
            'suffix'   => 'xmlinput',
            'override' => true,
            'note'     => 'Input and output are separate settings and are free to disagree, but XML written by one handler has to be readable by the other or existing content stops rendering.',
            'methods'  => array(
                array( 'name' => 'validateInput', 'signature' => 'validateInput( $http, $base, $contentObjectAttribute )',
                       'returns' => 'true',
                       'what' => 'Checks what was submitted before anything is stored. Return false and the editor is sent back to the form, so every rule about what may be published belongs here.' ),
                array( 'name' => 'convertInput', 'signature' => 'convertInput( $text )',
                       'returns' => 'true',
                       'what' => 'Turns the submitted text into stored XML. Whatever this writes is what every renderer will be handed for the life of the content, so it is the decision hardest to undo.' ),
                array( 'name' => 'editTemplateName', 'signature' => 'editTemplateName()',
                       'returns' => "''",
                       'what' => 'Which template draws the editing field. Change this and the editor changes; leave it and the field looks as it did.' ),
                array( 'name' => 'customObjectAttributeHTTPAction', 'signature' => 'customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute )',
                       'returns' => 'null',
                       'what' => 'Handles a button of your own inside the editing field, without leaving the edit form.' ) ) ),

        'xmloutput' => array(
            'title'    => 'XML text output handler',
            'what'     => 'What turns the stored XML of an ezxmltext attribute into what a visitor sees.',
            'why'      => 'The handler that ships renders to XHTML through a template per tag. A handler of your own is how the same stored content is rendered to something else entirely - plain text for a digest, a feed format, a print layout - without a second copy of the content existing anywhere.',
            'base'     => 'eZXHTMLXMLOutput',
            'source'   => 'kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php',
            'ini'      => 'ezxml.ini',
            'section'  => 'OutputSettings',
            'variable' => 'HandlerClass',
            'aliased'  => false,
            'suffix'   => 'xmloutput',
            'override' => true,
            'note'     => 'Whatever this returns is put on the page. Anything that came from an editor has to leave here escaped, or the stored content becomes a way to run script in a visitor\'s browser.',
            'methods'  => array(
                array( 'name' => 'outputText', 'signature' => 'outputText()',
                       'byref' => true,
                       'returns' => "''",
                       'what' => 'The whole rendered attribute. Everything below is in service of this one.' ),
                array( 'name' => 'renderTag', 'signature' => 'renderTag( $element, $content, $vars )',
                       'returns' => "''",
                       'what' => 'One tag, with its children already rendered into $content. The place to change how a paragraph, a link or a heading comes out.' ),
                array( 'name' => 'renderAll', 'signature' => 'renderAll( $element, $childrenOutput, $vars )',
                       'returns' => "''",
                       'what' => 'A tag whose children are still separate, for when they have to be joined some way other than end to end - a list, a table, anything numbered.' ),
                array( 'name' => 'viewTemplateName', 'signature' => 'viewTemplateName()',
                       'byref' => true,
                       'returns' => "''",
                       'what' => 'Which template draws the attribute as a whole.' ) ) ),
        );
    }

    /**
     * One recipe, or false.
     *
     * @param mixed $kind
     * @return array|false
     */
    public static function kind( $kind )
    {
        $kinds = self::kinds();

        return is_string( $kind ) && isset( $kinds[$kind] ) ? $kinds[$kind] : false;
    }

    /**
     * What this wizard can put in.
     *
     * @return array
     */
    public static function parts()
    {
        return array(
            'handler' => array(
                'label' => 'The handler',
                'description' => 'The class itself, with a method for every one the contract requires and a note on each saying what it is for and when it is called.',
                'default' => true ),
            'settings' => array(
                'label' => 'Registration',
                'description' => 'The ini that names this class in place of the default.',
                'default' => true ),
            'ezinfo' => array(
                'label' => 'ezinfo.php',
                'description' => 'What the admin interface reads to show the extension name, version and licence.',
                'default' => true ),
            'extension_xml' => array(
                'label' => 'extension.xml',
                'description' => 'The packaged description of the extension.',
                'default' => true ),
            'composer' => array(
                'label' => 'composer.json',
                'description' => 'So the extension can be required by name rather than copied in.',
                'default' => true ),
            'examples' => array(
                'label' => 'API examples',
                'description' => 'A file of worked examples: how the kernel reaches this handler, what it passes, and how to call it yourself from a script or a cronjob.',
                'default' => true ),
            'readme' => array(
                'label' => 'README.md',
                'description' => 'What it replaces, how to switch it on, and what each method has to do.',
                'default' => true ),
            'gitignore' => array(
                'label' => '.gitignore',
                'description' => 'Keeps editor leftovers and build output out of the repository.',
                'default' => true ),
            'licence' => array(
                'label' => 'LICENSE',
                'description' => 'The licence text named below. On by default: an extension with no licence file says nothing about how it may be used.',
                'default' => true ),
        );
    }

    /**
     * Everything the wizard was asked for.
     *
     * @param array $input
     * @return array
     */
    public static function settings( array $input )
    {
        $kinds = self::kinds();
        $keys  = array_keys( $kinds );

        $kind = isset( $input['kind'] ) && isset( $kinds[$input['kind']] ) ? $input['kind'] : $keys[0];
        $name = self::safeName( isset( $input['name'] ) ? $input['name'] : '' );

        $settings = array(
            'kind'    => $kind,
            'name'    => $name,
            'alias'   => self::safeAlias( isset( $input['alias'] ) ? $input['alias'] : '' ),
            'class'   => self::safeClass( isset( $input['class'] ) ? $input['class'] : '' ),
            'title'   => self::text( isset( $input['title'] ) ? $input['title'] : '', 120 ),
            'summary' => self::text( isset( $input['summary'] ) ? $input['summary'] : '', 250 ),
            'author'  => self::text( isset( $input['author'] ) ? $input['author'] : '', 120 ),
            'vendor'  => self::safeName( isset( $input['vendor'] ) ? $input['vendor'] : '', true ),
            'version' => self::text( isset( $input['version'] ) ? $input['version'] : '', 20 ),
            'licence' => self::licence_id( isset( $input['licence'] ) ? $input['licence'] : '' ),
        );

        $recipe = $kinds[$kind];

        if ( $settings['class'] === '' && $settings['name'] !== '' )
            $settings['class'] = self::safeClass( str_replace( '_', '', $settings['name'] ) . $recipe['suffix'] );
        if ( $settings['alias'] === '' && $settings['name'] !== '' )
            $settings['alias'] = self::safeAlias( $settings['name'] );
        if ( $settings['title'] === '' && $settings['name'] !== '' )
            $settings['title'] = ucwords( str_replace( '_', ' ', $settings['name'] ) );
        if ( $settings['version'] === '' )
            $settings['version'] = '1.0.0';
        if ( $settings['vendor'] === '' )
            $settings['vendor'] = 'exponential';
        if ( $settings['summary'] === '' )
            $settings['summary'] = 'A ' . strtolower( $recipe['title'] ) . ' for Exponential.';

        $chosen = isset( $input['parts'] ) && is_array( $input['parts'] ) ? $input['parts'] : null;
        foreach ( self::parts() as $key => $part )
            $settings['parts'][$key] = $chosen === null ? $part['default'] : in_array( $key, $chosen, true );

        return $settings;
    }

    /**
     * A class name: letters and digits, starting with a letter.
     *
     * @param string $value
     * @return string
     */
    public static function safeClass( $value )
    {
        if ( !is_string( $value ) )
            return '';

        $value = preg_replace( '/[^A-Za-z0-9_]+/', '', $value );

        return $value !== null && preg_match( '/^[A-Za-z][A-Za-z0-9_]{2,60}$/', $value ) ? $value : '';
    }

    /**
     * An ini alias: the short word the setting names.
     *
     * @param string $value
     * @return string
     */
    public static function safeAlias( $value )
    {
        if ( !is_string( $value ) )
            return '';

        $value = strtolower( preg_replace( '/[^A-Za-z0-9_]+/', '', $value ) );

        return $value !== null && preg_match( '/^[a-z][a-z0-9_]{1,40}$/', $value ) ? $value : '';
    }

    /**
     * What is wrong with these settings.
     *
     * @param array $settings
     * @return array of string
     */
    public static function problems( array $settings )
    {
        $problems = array();
        $recipe   = self::kind( $settings['kind'] );

        if ( $recipe === false )
            $problems[] = 'That is not a kind of handler this page knows.';

        if ( $settings['name'] === '' )
            $problems[] = 'The extension needs a name: lower case letters, digits and underscores, three to forty one characters, starting with a letter.';

        if ( $settings['name'] !== '' && is_dir( self::extensionPath( $settings['name'] ) ) )
            $problems[] = 'extension/' . $settings['name'] . ' already exists. Choose another name, or remove it first.';

        if ( $settings['class'] === '' )
            $problems[] = 'The class needs a name: letters and digits, starting with a letter.';

        if ( $recipe !== false && ( $recipe['aliased'] || !empty( $recipe['appended'] ) ) && $settings['alias'] === '' )
            $problems[] = 'This kind of handler is named by an alias, and the alias is empty.';

        // A class that already exists would be loaded instead of, or as well as,
        // the one being written.
        if ( $settings['class'] !== '' && class_exists( $settings['class'] ) )
            $problems[] = 'A class called ' . $settings['class'] . ' already exists on this installation. Choose another name.';

        return $problems;
    }

    // ── What it writes ───────────────────────────────────────────────────────

    /**
     * Every file the extension is made of.
     *
     * @param array $settings
     * @return array
     */
    public static function files( array $settings )
    {
        $recipe = self::kind( $settings['kind'] );

        if ( $recipe === false || $settings['name'] === '' || $settings['class'] === '' )
            return array();

        $parts = $settings['parts'];
        $files = array();

        if ( $parts['handler'] )
            $files[self::classPath( $settings, $recipe )] = self::handlerClass( $settings, $recipe );

        if ( $parts['settings'] )
            $files['settings/' . $recipe['ini'] . '.append.php'] = self::handlerIni( $settings, $recipe );

        if ( $parts['examples'] )
            $files['doc/examples.php'] = self::examples( $settings, $recipe );

        if ( $parts['ezinfo'] )
            $files['ezinfo.php'] = self::ezinfo( $settings );

        if ( $parts['extension_xml'] )
            $files['extension.xml'] = self::extensionXml( $settings );

        if ( $parts['composer'] )
            $files['composer.json'] = self::composerJson( $settings );

        if ( $parts['gitignore'] )
            $files['.gitignore'] = self::gitignore( $settings );

        if ( $parts['licence'] )
            $files['LICENSE'] = self::licence( $settings );

        ksort( $files );

        if ( $parts['readme'] )
        {
            $files['README.md'] = self::readme( $settings, $recipe, array_keys( $files ) );
            ksort( $files );
        }

        return $files;
    }

    /**
     * The lines that switch the extension on.
     *
     * @param array $settings
     * @return string
     */
    public static function activation( array $settings )
    {
        return "[ExtensionSettings]\nActiveExtensions[]=" . $settings['name'];
    }

    /**
     * Where inside the extension the class file goes.
     *
     * Most kinds are found by the autoloader and can live anywhere, so they go
     * in classes/. A few - the notification ones, and anything else reached
     * through a RepositoryDirectories setting - are found by looking for a file
     * at an exact path, and those name their own.
     *
     * @param array $settings
     * @param array $recipe
     * @return string
     */
    public static function classPath( array $settings, array $recipe )
    {
        if ( empty( $recipe['path'] ) )
            return 'classes/' . strtolower( $settings['class'] ) . '.php';

        return str_replace( array( '%alias%', '%class%' ),
                            array( $settings['alias'], strtolower( $settings['class'] ) ),
                            $recipe['path'] );
    }

    /**
     * The handler class, with a method per thing the contract requires.
     *
     * @param array $settings
     * @param array $recipe
     * @return string
     */
    protected static function handlerClass( array $settings, array $recipe )
    {
        $class = $settings['class'];

        $php  = "<?php\n/**\n * " . $class . " - " . $recipe['title'] . ".\n *\n";
        $php .= " * " . wordwrap( $recipe['what'], 74, "\n * " ) . "\n *\n";
        $php .= !empty( $recipe['interface'] )
                ? " * Satisfies " . $recipe['base'] . ", which the kernel loads in place of its\n"
                  . " * own. Registered in\n"
                : " * Replaces what " . $recipe['base'] . " does by default. Registered in\n";
        $php .= " * " . $recipe['ini'] . " [" . $recipe['section'] . "] " . $recipe['variable'];
        $php .= $recipe['aliased'] ? "[" . $settings['alias'] . "]" : '';
        $php .= ".\n *\n";
        $php .= " * Every method below is one the kernel calls. None of them does anything\n";
        $php .= " * useful yet; each says what it is for, when it is called, and what it has\n";
        $php .= " * to give back.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $keyword = !empty( $recipe['interface'] ) ? 'implements' : 'extends';
        $php .= "class " . $class . " " . $keyword . " " . $recipe['base'] . "\n{\n";

        // A name that has to be the same in three places is a name worth having
        // in exactly one: the ini, the directory and the class all read it from
        // here.
        if ( !empty( $recipe['constants'] ) )
        {
            foreach ( $recipe['constants'] as $constant )
            {
                $value = str_replace( array( '%alias%', '%class%' ),
                                      array( $settings['alias'], $settings['class'] ),
                                      $constant['value'] );

                $php .= "    /**\n";
                $php .= "     * " . wordwrap( $constant['what'], 70, "\n     * " ) . "\n";
                $php .= "     */\n";
                $php .= "    const " . $constant['name'] . " = '" . self::phpString( $value ) . "';\n\n";
            }
        }

        $methods = array();

        // Some bases are built by the kernel with no arguments and have to tell
        // their parent what they are. Without this the class loads and then
        // fails the moment it is used, which is the worst time to find out.
        if ( !empty( $recipe['constructor'] ) )
        {
            $body  = "    /**\n";
            $body .= "     * " . wordwrap( $recipe['constructor']['what'], 70, "\n     * " ) . "\n";
            $body .= "     */\n";
            $body .= "    public function __construct()\n    {\n";
            $body .= "        parent::__construct( " . $recipe['constructor']['arguments'] . " );\n";
            $body .= "    }";
            $methods[] = $body;
        }
        foreach ( $recipe['methods'] as $method )
        {
            $body  = "    /**\n";
            $body .= "     * " . wordwrap( $method['what'], 70, "\n     * " ) . "\n";
            $body .= "     */\n";
            $reference = !empty( $method['byref'] );

            $body .= "    public " . ( !empty( $method['static'] ) ? 'static ' : '' )
                   . "function " . ( $reference ? '&' : '' ) . $method['signature'] . "\n    {\n";

            if ( !empty( $recipe['override'] ) || !empty( $method['override'] ) )
            {
                // An override starts by doing exactly what it overrode. A
                // handler that half works is worse than one that does not
                // work yet, and half of these are called on every request.
                $body .= "        // Your own behaviour goes here. As it stands this does what the\n";
                $body .= "        // handler it extends does, so the site behaves as before until\n";
                $body .= "        // it is changed.\n";
                $body .= $reference
                         ? "        \$result = parent::" . self::callOf( $method['signature'] ) . ";\n\n"
                           . "        // Handed back through a variable: this method returns a reference,\n"
                           . "        // and php will not make one out of an expression.\n"
                           . "        return \$result;\n"
                         : "        return parent::" . self::callOf( $method['signature'] ) . ";\n";
            }
            else
            {
                $body .= "        // Not written yet. Returning " . $method['returns'] . " keeps the system\n";
                $body .= "        // working the way it did before this handler was switched on.\n";
                $body .= $reference
                         ? "        \$result = " . $method['returns'] . ";\n\n"
                           . "        // Handed back through a variable: this method returns a reference,\n"
                           . "        // and php will not make one out of an expression.\n"
                           . "        return \$result;\n"
                         : "        return " . $method['returns'] . ";\n";
            }

            $body .= "    }";

            $methods[] = $body;
        }

        $php .= implode( "\n\n", $methods ) . "\n}\n";

        return $php;
    }

    /**
     * A call to the parent, built from the signature of the method.
     *
     * The declaration carries default values that a call must not repeat, so
     * "endCacheGeneration( $rename = true )" becomes "endCacheGeneration( $rename )".
     *
     * @param string $signature
     * @return string
     */
    public static function callOf( $signature )
    {
        $open = strpos( $signature, '(' );
        if ( $open === false )
            return $signature;

        $name = substr( $signature, 0, $open );
        $args = trim( substr( $signature, $open + 1, strrpos( $signature, ')' ) - $open - 1 ) );

        if ( $args === '' )
            return $name . '()';

        $passed = array();
        foreach ( explode( ',', $args ) as $argument )
        {
            $argument = trim( $argument );

            // Drop a default, and any type in front of the variable.
            $equals = strpos( $argument, '=' );
            if ( $equals !== false )
                $argument = trim( substr( $argument, 0, $equals ) );

            $dollar = strpos( $argument, '$' );
            if ( $dollar === false )
                continue;

            $passed[] = substr( $argument, $dollar );
        }

        return $name . '( ' . implode( ', ', $passed ) . ' )';
    }

    /**
     * The ini that puts this class in place of the default.
     *
     * @param array $settings
     * @param array $recipe
     * @return string
     */
    protected static function handlerIni( array $settings, array $recipe )
    {
        $ini  = self::iniHeader( $settings, $recipe['title'] . ' registration' );
        $ini .= "[" . $recipe['section'] . "]\n";

        if ( !empty( $recipe['appended'] ) )
        {
            // An appended array: the kernel walks it and looks for a file whose
            // path it works out from the name. The class is never named here.
            $ini .= "# One more entry in the list the kernel walks. It is the name, not the\n";
            $ini .= "# class: the file is looked for at a path worked out from it, which is\n";
            $ini .= "# " . self::classPath( $settings, $recipe ) . " inside this extension.\n";
            $ini .= $recipe['variable'] . "[]=" . $settings['alias'] . "\n";
        }
        elseif ( $recipe['aliased'] )
        {
            $ini .= "# The alias, and the class it stands for. eZExtension::getHandlerClass\n";
            $ini .= "# turns one into the other.\n";
            $ini .= $recipe['variable'] . "[" . $settings['alias'] . "]=" . $settings['class'] . "\n";
        }
        else
        {
            $ini .= "# The class the kernel loads in place of its own.\n";
            $ini .= $recipe['variable'] . "=" . $settings['class'] . "\n";
        }

        // Anything else the setting needs before the entry above is looked at:
        // usually the directory the file is looked for in.
        if ( !empty( $recipe['extra'] ) )
            foreach ( $recipe['extra'] as $line )
            {
                $ini .= "\n# " . wordwrap( $line['what'], 72, "\n# " ) . "\n";
                $ini .= $line['variable'] . "=" . str_replace( array( '%extension%', '%alias%', '%class%' ),
                                                               array( $settings['name'], $settings['alias'], $settings['class'] ),
                                                               $line['value'] ) . "\n";
            }

        if ( $recipe['note'] !== '' )
            $ini .= "\n# " . wordwrap( $recipe['note'], 72, "\n# " ) . "\n";

        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    /**
     * How the kernel gets hold of this kind of handler, as code.
     *
     * Every one is reached differently, and knowing which call returns the
     * handler is most of knowing how to test one.
     *
     * @param array $settings
     * @param array $recipe
     * @return array of array( title, what, code )
     */
    protected static function reachedBy( array $settings, array $recipe )
    {
        $class = $settings['class'];

        switch ( $settings['kind'] )
        {
            case 'session':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'eZSession builds the handler once per request and hands php its methods. Nothing calls it directly.',
                           'code'  => "// lib/ezsession/classes/ezsession.php, at the start of the request\n"
                                    . "\$handler = eZExtension::getHandlerClass(\n"
                                    . "    new ezpExtensionOptions( array( 'iniFile' => 'site.ini',\n"
                                    . "                                   'iniSection' => 'Session',\n"
                                    . "                                   'iniVariable' => 'Handler' ) ) );\n"
                                    . "\$handler->setSaveHandler();" ),
                    array( 'title' => 'Trying it from a script',
                           'what'  => 'The methods can be called straight, which is the only sane way to test one.',
                           'code'  => "\$handler = new " . $class . "();\n\n"
                                    . "\$handler->write( 'probe-session', 'a=1;' );\n"
                                    . "echo \$handler->read( 'probe-session' );   // a=1;\n"
                                    . "\$handler->destroy( 'probe-session' );\n\n"
                                    . "// What the session cronjob calls:\n"
                                    . "\$handler->gc( 86400 );" ),
                    array( 'title' => 'Removing somebody\'s sessions',
                           'what'  => 'Called when a user is disabled or removed. This is what makes that take effect at once rather than at the next timeout.',
                           'code'  => "\$handler->deleteByUserIDs( array( 14, 42 ) );" ) );

            case 'mail':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'eZMailTransport::send() looks up the alias and hands the mail to whatever it names.',
                           'code'  => "\$mail = new eZMail();\n"
                                    . "\$mail->setReceiver( 'someone@example.com' );\n"
                                    . "\$mail->setSender( 'site@example.com' );\n"
                                    . "\$mail->setSubject( 'Hello' );\n"
                                    . "\$mail->setBody( 'Text' );\n\n"
                                    . "eZMailTransport::send( \$mail );   // uses [MailSettings] Transport" ),
                    array( 'title' => 'Trying this transport alone',
                           'what'  => 'Without going through the setting, so the default cannot answer instead.',
                           'code'  => "\$transport = new " . $class . "();\n"
                                    . "var_dump( \$transport->sendMail( \$mail ) );" ) );

            case 'search':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'eZSearch builds the engine named in the setting and passes everything through it.',
                           'code'  => "// Indexing, on publish\n"
                                    . "eZSearch::addObject( \$contentObject );\n\n"
                                    . "// Searching, from the search module\n"
                                    . "\$result = eZSearch::search( 'words', array( 'SearchOffset' => 0,\n"
                                    . "                                            'SearchLimit'  => 10 ) );" ),
                    array( 'title' => 'Trying this engine alone',
                           'what'  => 'The shape of the answer is fixed; anything reading it expects these three keys.',
                           'code'  => "\$engine = new " . $class . "();\n\n"
                                    . "\$object = eZContentObject::fetch( 42 );\n"
                                    . "\$engine->addObject( \$object );\n"
                                    . "\$engine->commit();\n\n"
                                    . "\$result = \$engine->search( 'words' );\n"
                                    . "echo \$result['SearchCount'], ' found', PHP_EOL;\n"
                                    . "foreach ( \$result['SearchResult'] as \$row )\n"
                                    . "    echo \$row['name'], PHP_EOL;" ),
                    array( 'title' => 'Rebuilding the index',
                           'what'  => 'After switching engines, nothing this engine wrote is in the index yet.',
                           'code'  => "// php bin/php/updatesearchindex.php --db-host=... --clean\n"
                                    . "// Run it once, and again whenever the engine changes." ) );

            case 'staticcache':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'The handler is built through eZExtension and asked to write pages as content changes.',
                           'code'  => "\$cache = eZExtension::getHandlerClass(\n"
                                    . "    new ezpExtensionOptions( array( 'iniFile' => 'site.ini',\n"
                                    . "                                   'iniSection' => 'ContentSettings',\n"
                                    . "                                   'iniVariable' => 'StaticCacheHandler' ) ) );\n\n"
                                    . "\$cache->generateNodeListCache( array( 2, 59 ) );" ),
                    array( 'title' => 'Trying this handler alone',
                           'what'  => 'One address at a time is the useful unit; everything else ends up here.',
                           'code'  => "\$cache = new " . $class . "();\n\n"
                                    . "\$cache->cacheURL( '/news/my-article', 59 );\n"
                                    . "\$cache->removeURL( '/news/my-article' );\n\n"
                                    . "// What the Create new button on the cache page calls:\n"
                                    . "\$cache->generateCache( true, false, false, false );" ) );

            case 'binaryfile':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'The handler is asked first, and answers whether it dealt with the download.',
                           'code'  => "\$handler = eZBinaryFileHandler::instance();\n\n"
                                    . "\$result = \$handler->handleDownload( \$contentObject,\n"
                                    . "                                   \$contentObjectAttribute,\n"
                                    . "                                   eZBinaryFileHandler::TYPE_ATTRIBUTE );\n\n"
                                    . "if ( \$result === eZBinaryFileHandler::RESULT_UNAVAILABLE )\n"
                                    . "    // the default storage deals with it instead" ),
                    array( 'title' => 'Trying this handler alone',
                           'what'  => 'Built by name rather than through the setting.',
                           'code'  => "\$handler = new " . $class . "( '" . $settings['alias'] . "',\n"
                                    . "                               'My handler',\n"
                                    . "                               eZBinaryFileHandler::HANDLE_TYPE_DOWNLOAD );\n\n"
                                    . "var_dump( \$handler->handleUpload() );" ) );

            case 'cluster':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'Every file the kernel touches goes through eZClusterFileHandler::instance(), which builds whatever FileHandler names.',
                           'code'  => "\$file = eZClusterFileHandler::instance( 'var/storage/images/photo.jpg' );\n\n"
                                    . "if ( \$file->exists() )\n"
                                    . "    echo \$file->size(), ' bytes', PHP_EOL;\n\n"
                                    . "\$contents = \$file->fetchContents();" ),
                    array( 'title' => 'Storing something',
                           'what'  => 'The scope is what purging works on, so it is worth passing.',
                           'code'  => "\$file = eZClusterFileHandler::instance( 'var/cache/mything.cache' );\n"
                                    . "\$file->fileStoreContents( 'var/cache/mything.cache',\n"
                                    . "                          \$contents,\n"
                                    . "                          'viewcache',\n"
                                    . "                          'text/plain' );" ),
                    array( 'title' => 'Generating a cache entry once',
                           'what'  => 'The three cache generation methods are what stop ten requests building the same entry ten times. Forgetting to abort on failure is how an entry stays locked for ever.',
                           'code'  => "\$file = eZClusterFileHandler::instance( 'var/cache/expensive.cache' );\n\n"
                                    . "if ( \$file->startCacheGeneration() === true )\n"
                                    . "{\n"
                                    . "    try\n"
                                    . "    {\n"
                                    . "        \$file->storeContents( expensiveThing(), 'viewcache', 'text/plain' );\n"
                                    . "        \$file->endCacheGeneration();\n"
                                    . "    }\n"
                                    . "    catch ( Exception \$e )\n"
                                    . "    {\n"
                                    . "        \$file->abortCacheGeneration();\n"
                                    . "        throw \$e;\n"
                                    . "    }\n"
                                    . "}" ),
                    array( 'title' => 'Purging',
                           'what'  => 'Only needed when requiresPurge() says deleting merely marks.',
                           'code'  => "// php runcronjobs.php -s <siteaccess> clusterpurge\n"
                                    . "\$file->purge();" ) );

            case 'dfsbackend':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'eZDFSFileHandler keeps an index of files in the database and the bytes here. The backend is built by a factory from the setting.',
                           'code'  => "// kernel/private/classes/clusterfilehandlers/ezdfsfilehandlerbackendfactory.php\n"
                                    . "\$backend = eZDFSFileHandlerBackendFactory::build();\n\n"
                                    . "\$backend->copyToDFS( 'var/storage/images/photo.jpg' );" ),
                    array( 'title' => 'Trying this backend alone',
                           'what'  => 'The whole interface can be exercised from a script, which is the only way to be sure of it before a site depends on it.',
                           'code'  => "\$backend = new " . $class . "();\n\n"
                                    . "\$backend->createFileOnDFS( 'probe/hello.txt', 'hello' );\n"
                                    . "var_dump( \$backend->existsOnDFS( 'probe/hello.txt' ) );   // true\n"
                                    . "var_dump( \$backend->getDfsFileSize( 'probe/hello.txt' ) ); // 5\n"
                                    . "echo \$backend->getContents( 'probe/hello.txt' ), PHP_EOL;  // hello\n\n"
                                    . "\$backend->renameOnDFS( 'probe/hello.txt', 'probe/there.txt' );\n"
                                    . "\$backend->delete( 'probe/there.txt' );" ),
                    array( 'title' => 'Serving a large file',
                           'what'  => 'passthrough() is what keeps a video out of memory, and what makes a byte range work.',
                           'code'  => "// The whole file\n"
                                    . "\$backend->passthrough( 'var/storage/original/video.mp4' );\n\n"
                                    . "// Bytes 1000 to 1999, for a browser that asked for a range\n"
                                    . "\$backend->passthrough( 'var/storage/original/video.mp4', 1000, 1000 );" ) );

            case 'dfsdbbackend':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'The DFS handler builds the database backend through the same factory as the DFS backend, and asks it about a file before touching any bytes.',
                           'code'  => "\$backend = eZDFSFileHandlerBackendFactory::build();\n\n"
                                    . "if ( \$backend->_exists( 'var/cache/page.cache' ) )\n"
                                    . "    \$backend->_fetch( 'var/cache/page.cache' );" ),
                    array( 'title' => 'The part that is hard',
                           'what'  => 'Two servers asking to build the same entry at the same moment. Exactly one must be told yes, and the other must be told to wait. Everything else in this class is bookkeeping around that one decision.',
                           'code'  => "\$backend = new " . $class . "();\n\n"
                                    . "\$result = \$backend->_startCacheGeneration( 'var/cache/page.cache',\n"
                                    . "                                          'var/cache/page.cache.generating' );\n\n"
                                    . "if ( \$result === true )\n"
                                    . "{\n"
                                    . "    // This process won the claim and has to finish or abort.\n"
                                    . "    \$backend->_endCacheGeneration( 'var/cache/page.cache',\n"
                                    . "                                  'var/cache/page.cache.generating',\n"
                                    . "                                  true );\n"
                                    . "}\n"
                                    . "else\n"
                                    . "{\n"
                                    . "    // Somebody else is building it; \$result says since when.\n"
                                    . "    // The caller waits, or serves the stale copy.\n"
                                    . "}" ),
                    array( 'title' => 'Deleting is marking',
                           'what'  => 'A delete leaves the row behind on purpose, so that every server learns the file has gone. The purge cronjob is what really removes it.',
                           'code'  => "\$backend->_delete( 'var/cache/page.cache' );\n"
                                    . "\$backend->_deleteByLike( 'var/cache/content/%' );\n\n"
                                    . "// Later, from runcronjobs.php clusterpurge:\n"
                                    . "\$backend->_purge( 'var/cache/page.cache' );" ) );

            case 'dbhandler':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'eZDB::instance() builds whatever Implementation names, once per request, and hands the same object to everything.',
                           'code'  => "\$db = eZDB::instance();\n\n"
                                    . "\$rows = \$db->arrayQuery( 'SELECT id, name FROM ezcontentclass' );\n"
                                    . "foreach ( \$rows as \$row )\n"
                                    . "    echo \$row['name'], PHP_EOL;" ),
                    array( 'title' => 'Values from outside',
                           'what'  => 'Nothing that came from a request goes into a statement unescaped. escapeString() is the whole defence, which is why overriding it to do less is the one change that must never be made here.',
                           'code'  => "\$name = \$db->escapeString( \$http->postVariable( 'name' ) );\n"
                                    . "\$db->query( \"UPDATE mytable SET name='\$name' WHERE id=\" . (int) \$id );" ),
                    array( 'title' => 'Transactions',
                           'what'  => 'They nest by counting, so an inner commit does not end the outer transaction. A rollback anywhere inside poisons the whole thing.',
                           'code'  => "\$db->begin();\n"
                                    . "try\n"
                                    . "{\n"
                                    . "    \$db->query( \"INSERT INTO mytable ( name ) VALUES ( 'a' )\" );\n"
                                    . "    \$id = \$db->lastSerialID( 'mytable', 'id' );\n"
                                    . "    \$db->commit();\n"
                                    . "}\n"
                                    . "catch ( Exception \$e )\n"
                                    . "{\n"
                                    . "    \$db->rollback();\n"
                                    . "    throw \$e;\n"
                                    . "}" ),
                    array( 'title' => 'Sending reads elsewhere',
                           'what'  => 'The server argument is how a caller says a read may go to a replica. Overriding query() and arrayQuery() to honour it is the usual reason to write one of these.',
                           'code'  => "\$db->arrayQuery( \$sql, array(), eZDBInterface::SERVER_SLAVE );" ) );

            case 'notificationtype':
                return array(
                    array( 'title' => 'Raising one',
                           'what'  => 'Anywhere in your own code, when the thing has happened. The type string is the one in notification.ini.',
                           'code'  => "\$event = eZNotificationEvent::create( " . "'" . self::phpString( $settings['alias'] ) . "'" . ", array(\n"
                                    . "    'node_id' => \$node->attribute( 'node_id' ),\n"
                                    . "    'user_id' => \$user->attribute( 'contentobject_id' ) ) );\n"
                                    . "\$event->store();\n\n"
                                    . "// Nothing is sent yet. The notification cronjob picks it up." ),
                    array( 'title' => 'What the kernel does next',
                           'what'  => 'The cronjob builds the type by name, calls execute(), and sends whatever execute() added to the event.',
                           'code'  => "// php runcronjobs.php -s <siteaccess> notification\n"
                                    . "\$type = eZNotificationEventType::create( " . "'" . self::phpString( $settings['alias'] ) . "'" . " );\n"
                                    . "\$type->execute( \$event );" ),
                    array( 'title' => 'Saying who should hear about it', 'in' => 'class',
                           'what'  => 'Inside execute(). Each item is one address to send to; the kernel groups them and hands them to the transports.',
                           'code'  => "public function execute( \$event )\n"
                                    . "{\n"
                                    . "    foreach ( \$this->peopleWhoCareAbout( \$event ) as \$user )\n"
                                    . "    {\n"
                                    . "        \$item = eZNotificationCollectionItem::create( \$event->attribute( 'id' ),\n"
                                    . "                                                     \$user->attribute( 'contentobject_id' ),\n"
                                    . "                                                     \$user->attribute( 'email' ) );\n"
                                    . "        \$item->store();\n"
                                    . "    }\n\n"
                                    . "    return eZNotificationEventType::STATUS_ACCEPTED;\n"
                                    . "}" ),
                    array( 'title' => 'Reading the event back',
                           'what'  => 'Only what initializeEvent() stored is there. execute() runs in another request, hours later, with nothing else to go on.',
                           'code'  => "\$nodeID = \$event->attribute( 'data_int1' );\n"
                                    . "\$name   = \$event->attribute( 'data_text1' );" ) );

            case 'notificationhandler':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'The notification cronjob builds every handler named in notification.ini and offers each event to all of them in turn.',
                           'code'  => "// kernel/classes/notification/eznotificationeventfilter.php\n"
                                    . "foreach ( eZNotificationEventFilter::availableHandlers() as \$handler )\n"
                                    . "    \$handler->handle( \$event );" ),
                    array( 'title' => 'Adding somebody', 'in' => 'class',
                           'what'  => 'Inside handle(). This is the whole job: decide who cares, and add one item each.',
                           'code'  => "public function handle( \$event )\n"
                                    . "{\n"
                                    . "    if ( \$event->attribute( 'event_type_string' ) !== 'ezpublish' )\n"
                                    . "        return eZNotificationEventHandler::STATUS_ACCEPTED;\n\n"
                                    . "    foreach ( \$this->subscribers( \$event ) as \$address )\n"
                                    . "        eZNotificationCollectionItem::create( \$event->attribute( 'id' ),\n"
                                    . "                                             \$address['user_id'],\n"
                                    . "                                             \$address['email'] )->store();\n\n"
                                    . "    return eZNotificationEventHandler::STATUS_ACCEPTED;\n"
                                    . "}" ),
                    array( 'title' => 'The settings tab', 'in' => 'class',
                           'what'  => 'The two form methods are separate so a page carrying several handlers can validate all of them before storing any. Prefix every field name, or two handlers read each other\'s input.',
                           'code'  => "public function fetchHttpInput( \$http, \$module )\n"
                                    . "{\n"
                                    . "    \$field = " . "'" . self::phpString( $settings['alias'] . '_enabled' ) . "'" . ";\n"
                                    . "    \$this->wanted = \$http->hasPostVariable( \$field );\n"
                                    . "}\n\n"
                                    . "public function storeSettings( \$http, \$module )\n"
                                    . "{\n"
                                    . "    // Store \$this->wanted against the current user.\n"
                                    . "}" ) );

            case 'packagehandler':
                return array(
                    array( 'title' => 'What a package says',
                           'what'  => 'One <install> element per item, with this handler\'s alias as its type. Everything after that is yours to shape.',
                           'code'  => "// package.xml\n"
                                    . "// <install type=\"" . $settings['alias'] . "\" name=\"My thing\">\n"
                                    . "//   <thing id=\"42\">...</thing>\n"
                                    . "// </install>" ),
                    array( 'title' => 'What the kernel does',
                           'what'  => 'The handler is built from the alias and asked to install each item that names it.',
                           'code'  => "\$package = eZPackage::fetch( 'mypackage' );\n"
                                    . "\$handler = eZPackage::packageHandler( " . "'" . self::phpString( $settings['alias'] ) . "'" . " );\n\n"
                                    . "\$parameters = array();\n"
                                    . "\$data = array();\n"
                                    . "\$handler->install( \$package, 'install', array(), 'My thing',\n"
                                    . "                   false, false, false, \$installItemNode,\n"
                                    . "                   \$parameters, \$data );" ),
                    array( 'title' => 'Building a package instead',
                           'what'  => 'The other direction. createInstallNode() writes what install() will later be handed, so the two have to agree about the shape.',
                           'code'  => "\$package = eZPackage::create( 'mypackage', array( 'name' => 'My package' ) );\n"
                                    . "\$handler->add( " . "'" . self::phpString( $settings['alias'] ) . "'" . ", \$package, false, array( 'id' => 42 ) );\n"
                                    . "\$package->store();" ),
                    array( 'title' => 'Before installing',
                           'what'  => 'What the admin shows on the confirmation page. Somebody is about to let this into their installation; tell them what it is.',
                           'code'  => "print_r( \$handler->explainInstallItem( \$package, \$installItem ) );\n"
                                    . "// array( 'description' => 'My thing (42)' )" ) );

            case 'restprovider':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'Every provider named in rest.ini is asked for its routes, and they are matched in order against the request.',
                           'code'  => "// kernel/private/rest/classes/rest_provider_registry.php\n"
                                    . "\$provider = ezpRestProviderRegistry::getProvider( " . "'" . self::phpString( $settings['alias'] ) . "'" . " );\n"
                                    . "\$routes = \$provider->getRoutes();" ),
                    array( 'title' => 'Returning routes', 'in' => 'class',
                           'what'  => 'Each route is a path pattern, a controller class and a method on it. Specific patterns first: the first match wins, so a catch-all at the top hides everything below it.',
                           'code'  => "public function getRoutes()\n"
                                    . "{\n"
                                    . "    return array(\n"
                                    . "        'thing_list' => new ezpRestVersionedRoute(\n"
                                    . "            new ezcMvcRailsRoute( '/api/" . $settings['alias'] . "/things',\n"
                                    . "                                 'MyThingController', 'listThings' ), 1 ),\n"
                                    . "        'thing_read' => new ezpRestVersionedRoute(\n"
                                    . "            new ezcMvcRailsRoute( '/api/" . $settings['alias'] . "/things/:id',\n"
                                    . "                                 'MyThingController', 'readThing' ), 1 ) );\n"
                                    . "}" ),
                    array( 'title' => 'The controller',
                           'what'  => 'A method per route, returning a result object. The view controller turns that into json or xml, so nothing here decides a format.',
                           'code'  => "class MyThingController extends ezpRestMvcController\n"
                                    . "{\n"
                                    . "    public function readThing()\n"
                                    . "    {\n"
                                    . "        \$result = new ezpRestMvcResult();\n"
                                    . "        \$result->variables = array( 'id' => (int) \$this->id );\n\n"
                                    . "        return \$result;\n"
                                    . "    }\n"
                                    . "}" ),
                    array( 'title' => 'Calling it',
                           'what'  => 'Authentication, versioning and error handling come from the rest layer, not from the provider.',
                           'code'  => "// GET /api/" . $settings['alias'] . "/things/42\n"
                                    . "// Accept: application/json; version=1" ) );

            case 'xmlinput':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'The ezxmltext datatype builds the input handler whenever an attribute is edited, and asks it to validate and then convert.',
                           'code'  => "\$attribute = \$object->attribute( 'data_map' )->offsetGet( 'body' );\n"
                                    . "\$handler   = \$attribute->content()->attribute( 'input' );\n\n"
                                    . "if ( \$handler->validateInput( \$http, \$base, \$attribute ) )\n"
                                    . "    \$handler->convertInput( \$http->postVariable( \$base . '_data_text_' . \$attribute->attribute( 'id' ) ) );" ),
                    array( 'title' => 'Refusing to store something', 'in' => 'class',
                           'what'  => 'Return false from validateInput() and the editor is sent back to the form. Adding a message is what stops that being a mystery.',
                           'code'  => "public function validateInput( \$http, \$base, \$contentObjectAttribute )\n"
                                    . "{\n"
                                    . "    if ( strpos( \$http->postVariable( \$base . '_data_text_'\n"
                                    . "                 . \$contentObjectAttribute->attribute( 'id' ) ), '<script' ) !== false )\n"
                                    . "    {\n"
                                    . "        \$contentObjectAttribute->setValidationError(\n"
                                    . "            ezpI18n::tr( 'extension/" . $settings['name'] . "', 'Script tags are not allowed here.' ) );\n\n"
                                    . "        return false;\n"
                                    . "    }\n\n"
                                    . "    return parent::validateInput( \$http, \$base, \$contentObjectAttribute );\n"
                                    . "}" ),
                    array( 'title' => 'Reading what was stored',
                           'what'  => 'What convertInput() wrote is what every renderer is handed, for the life of the content.',
                           'code'  => "echo \$handler->inputXML();" ) );

            case 'xmloutput':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'Rendering an ezxmltext attribute in a template ends here.',
                           'code'  => "// In a template: {\$node.data_map.body.content.output.output_text}\n"
                                    . "\$output = \$attribute->content()->attribute( 'output' );\n"
                                    . "echo \$output->outputText();" ),
                    array( 'title' => 'Changing one tag', 'in' => 'class',
                           'what'  => 'renderTag() is handed a tag whose children are already rendered. Whatever it returns goes on the page, so anything that came from an editor leaves here escaped.',
                           'code'  => "public function renderTag( \$element, \$content, \$vars )\n"
                                    . "{\n"
                                    . "    if ( \$element->nodeName === 'header' )\n"
                                    . "        return '<h2 class=\"mine\">' . \$content . '</h2>';\n\n"
                                    . "    \$result = parent::renderTag( \$element, \$content, \$vars );\n\n"
                                    . "    return \$result;\n"
                                    . "}" ),
                    array( 'title' => 'Joining children some other way', 'in' => 'class',
                           'what'  => 'renderAll() gets them still separate, which is what a list, a table or anything numbered needs.',
                           'code'  => "public function renderAll( \$element, \$childrenOutput, \$vars )\n"
                                    . "{\n"
                                    . "    \$items = '';\n"
                                    . "    foreach ( \$childrenOutput as \$index => \$child )\n"
                                    . "        \$items .= '<li value=\"' . ( \$index + 1 ) . '\">' . \$child . '</li>';\n\n"
                                    . "    return '<ol>' . \$items . '</ol>';\n"
                                    . "}" ),
                    array( 'title' => 'Rendering to something else entirely',
                           'what'  => 'Nothing says the output has to be html. The same stored content can come out as plain text for a digest, or as a feed. A second handler, switched on for one siteaccess, is how both exist at once.',
                           'code'  => "class PlainTextOutput extends " . $recipe['base'] . "\n"
                                    . "{\n"
                                    . "    public function renderTag( \$element, \$content, \$vars )\n"
                                    . "    {\n"
                                    . "        return \$element->nodeName === 'paragraph'\n"
                                    . "               ? \$content . \"\\n\\n\"\n"
                                    . "               : \$content;\n"
                                    . "    }\n"
                                    . "}" ) );
        }

        return array();
    }

    /**
     * Worked examples: how the kernel reaches this handler and how to call it.
     *
     * @param array $settings
     * @param array $recipe
     * @return string
     */
    protected static function examples( array $settings, array $recipe )
    {
        $php  = "<?php\n/**\n * " . $settings['class'] . " - worked examples.\n *\n";
        $php .= " * Not part of the extension: a file to read, and to copy lines out of. It is\n";
        $php .= " * under doc/ rather than classes/ so nothing loads it by accident.\n *\n";
        $php .= " * Run any of it from the installation root with:\n";
        $php .= " *     php -r 'require \"autoload.php\"; ...'\n";
        $php .= " * or paste it into a script under bin/.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "// Nothing below runs on its own.\nreturn;\n\n";

        // Two sorts of example: statements that could be pasted into a script,
        // and method bodies that belong inside the handler. Mixing them at the
        // top of a file would not parse, so the second sort is collected into a
        // class of its own at the end.
        $loose = array();
        $inside = array();
        foreach ( self::reachedBy( $settings, $recipe ) as $example )
        {
            if ( isset( $example['in'] ) && $example['in'] === 'class' )
                $inside[] = $example;
            else
                $loose[] = $example;
        }

        foreach ( $loose as $example )
        {
            $php .= "// ─── " . $example['title'] . " ";
            $php .= str_repeat( '─', max( 4, 66 - mb_strlen( $example['title'], 'UTF-8' ) ) ) . "\n//\n";
            $php .= "// " . wordwrap( $example['what'], 72, "\n// " ) . "\n\n";
            $php .= $example['code'] . "\n\n\n";
        }

        if ( count( $inside ) )
        {
            $php .= "// ─── Inside the handler itself ";
            $php .= str_repeat( '─', 42 ) . "\n//\n";
            $php .= "// " . wordwrap( 'These are method bodies, not statements: they belong in '
                                      . $settings['class'] . '. They are shown in a class of their own '
                                      . 'only so that this file is valid php and an editor can read it '
                                      . 'as code. Nothing here is ever loaded.', 72, "\n// " ) . "\n\n";
            // It extends the same base the handler does, so that a body using
            // parent:: is valid php. An interface cannot be extended, so those
            // stand alone - and none of their examples calls parent.
            $php .= "class " . $settings['class'] . "Examples";
            $php .= empty( $recipe['interface'] ) ? " extends " . $recipe['base'] : '';
            $php .= "\n{\n";

            $bodies = array();
            foreach ( $inside as $example )
            {
                $body  = "    // ─── " . $example['title'] . " ";
                $body .= str_repeat( '─', max( 4, 62 - mb_strlen( $example['title'], 'UTF-8' ) ) ) . "\n    //\n";
                $body .= "    // " . wordwrap( $example['what'], 68, "\n    // " ) . "\n\n";
                $body .= "    " . str_replace( "\n", "\n    ", rtrim( $example['code'] ) );
                $bodies[] = rtrim( $body );
            }

            $php .= implode( "\n\n", $bodies ) . "\n}\n\n\n";
        }

        $php .= "// ─── Every method, and what it has to give back ";
        $php .= str_repeat( '─', 22 ) . "\n//\n";
        foreach ( $recipe['methods'] as $method )
        {
            $php .= "// " . $method['signature'] . "\n";
            $php .= "//     " . wordwrap( $method['what'], 68, "\n//     " ) . "\n//\n";
        }

        return $php;
    }

    /**
     * What it replaces, how to switch it on, and what each method has to do.
     *
     * @param array $settings
     * @param array $recipe
     * @param array $paths
     * @return string
     */
    protected static function readme( array $settings, array $recipe, array $paths = array() )
    {
        $readme  = "# " . $settings['title'] . "\n\n" . $settings['summary'] . "\n\n";
        $readme .= "A " . strtolower( $recipe['title'] ) . " for Exponential / eZ Publish legacy,\n";
        $readme .= "built with the handler wizard in Setup > RAD.\n\n";

        $readme .= "## What it replaces\n\n" . $recipe['what'] . "\n\n" . $recipe['why'] . "\n\n";
        $readme .= "`" . $settings['class'] . "` extends `" . $recipe['base'] . "`\n";
        $readme .= "(`" . $recipe['source'] . "`).\n\n";

        $readme .= "## What has to be written\n\n";
        $readme .= "| Method | What it is for |\n|---|---|\n";
        foreach ( $recipe['methods'] as $method )
            $readme .= "| `" . $method['signature'] . "` | " . $method['what'] . " |\n";

        $readme .= "\nEach returns `" . $recipe['methods'][0]['returns'] . "`-ish as it stands, which is to say\n";
        $readme .= "it does nothing and says so. Switching the extension on before they are\n";
        $readme .= "written will not break the site, but it will not do anything either.\n\n";

        $readme .= "## Switching it on\n\n";
        $readme .= "Add it to the active extensions in `settings/override/site.ini.append.php`:\n\n";
        $readme .= "```ini\n" . self::activation( $settings ) . "\n```\n\n";
        $readme .= "The extension's own `settings/" . $recipe['ini'] . ".append.php` names the class:\n\n";
        $readme .= "```ini\n[" . $recipe['section'] . "]\n";
        $readme .= $recipe['aliased']
                   ? $recipe['variable'] . "[" . $settings['alias'] . "]=" . $settings['class'] . "\n"
                   : $recipe['variable'] . "=" . $settings['class'] . "\n";
        $readme .= "```\n\n";

        if ( $recipe['note'] !== '' )
            $readme .= $recipe['note'] . "\n\n";

        $readme .= "Then clear the caches and regenerate the extension autoloads, so the class\n";
        $readme .= "is found:\n\n```sh\nphp bin/php/ezcache.php --clear-all\n";
        $readme .= "php bin/php/ezpgenerateautoloads.php --extension\n```\n\n";

        $readme .= "## Licence\n\n" . self::licenceLine( $settings ) . "\n\n";

        if ( count( $paths ) )
        {
            $readme .= "## What is inside\n\n";
            foreach ( $paths as $path )
                $readme .= "- `" . $path . "`\n";
        }

        return $readme;
    }
}
