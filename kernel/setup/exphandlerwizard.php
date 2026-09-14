<?php
/**
 * File containing the expHandlerWizard class.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
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

        'login' => array(
            'title'    => 'User login handler',
            'what'     => 'Where the system goes to find out whether a password is right.',
            'why'      => 'The default checks a hash in ezuser. A handler of your own asks somebody else - a directory, a single sign on service, another application - and makes the user here when the answer comes back yes. It is how a site stops being the place passwords are kept.',
            'base'     => 'eZUser',
            'source'   => 'kernel/classes/datatypes/ezuser/ezuserloginhandler.php',
            'ini'      => 'site.ini',
            'section'  => 'UserSettings',
            'variable' => 'LoginHandler',
            'aliased'  => false,
            'appended' => true,
            'classFrom' => 'eZ%alias%User',
            'path'     => 'login_handler/ez%alias%user.php',
            'suffix'   => 'user',
            'override' => true,
            'extra'    => array(
                array( 'variable' => 'ExtensionDirectory[]',
                       'value'    => '%extension%',
                       'what'     => 'The extension whose login_handler/ directory is searched. Without this line the file is never looked for, however correctly it is named.' ) ),
            'note'     => 'The class name and the file name are both worked out from the setting: LoginHandler[]=x means class eZxUser in login_handler/ezxuser.php. All three have to agree or the handler is reported missing and the default answers instead - which means a site that looks like it is using your handler and is not.',
            'methods'  => array(
                array( 'name' => 'loginUser', 'signature' => 'loginUser( $login, $password, $authenticationMatch = false )',
                       'static' => true,
                       'returns' => 'false',
                       'what' => 'The whole job. Given a name and a password, return an eZUser when they are right and false when they are not. Returning anything for a wrong password is the worst bug it is possible to write here, so fail closed: anything unexpected - a service that is down, an answer that does not parse, a user with no name - returns false.' ),
                array( 'name' => 'fetchByName', 'signature' => 'fetchByName( $login, $asObject = true )',
                       'static' => true,
                       'returns' => 'false',
                       'what' => 'Finds the local user row for a name. A handler authenticating elsewhere still needs a user here to own content and carry roles, and this is where one is found or made.' ) ) ),

        'vat' => array(
            'title'    => 'VAT handler',
            'what'     => 'What decides which rate of tax a product is sold at.',
            'why'      => 'The handler that ships reads the rate off the product class and the buyer country. A handler of your own is how any other rule applies - a category held elsewhere, a rate that depends on the buyer rather than the goods, a rate fetched from a service - without a copy of the tax rules in every template.',
            'base'     => 'eZDefaultVATHandler',
            'source'   => 'kernel/classes/vathandlers/ezdefaultvathandler.php',
            'ini'      => 'shop.ini',
            'section'  => 'VATSettings',
            'variable' => 'Handler',
            'aliased'  => false,
            'namesAlias' => true,
            'classFrom' => '%alias%VATHandler',
            'path'     => 'vathandlers/%alias%vathandler.php',
            'suffix'   => 'vathandler',
            'override' => true,
            'extra'    => array(
                array( 'variable' => 'ExtensionDirectories[]',
                       'value'    => '%extension%',
                       'what'     => 'The extension whose vathandlers/ directory is searched. Without this line the file is never looked for.' ) ),
            'note'     => 'The class name and the file name are both worked out from the setting above, so neither is free to change on its own.',
            'methods'  => array(
                array( 'name' => 'getVatPercent', 'signature' => 'getVatPercent( $object, $country )',
                       'returns' => 'false',
                       'what' => 'The rate for this product sold into this country, as a number - 25 for twenty five per cent. Return false when there is no answer, and the sale is refused rather than taxed at a guess. Called for every line of every basket, so it wants to be cheap.' ),
                array( 'name' => 'getProductCategory', 'signature' => 'getProductCategory( $object )',
                       'returns' => 'false',
                       'what' => 'Which category of goods this is, when the rate depends on the category rather than on the product. Return an eZProductCategory, or false.' ),
                array( 'name' => 'chooseVatType', 'signature' => 'chooseVatType( $productCategory, $country )',
                       'returns' => 'false',
                       'what' => 'Which of the configured VAT types applies to a category in a country. This is the rule, in one place; getVatPercent() only reads the number off what this chooses.' ) ) ),

        'shipping' => array(
            'title'    => 'Shipping handler',
            'what'     => 'What a basket costs to deliver.',
            'why'      => 'Nothing ships as a default, so without a handler of your own the shop has no shipping at all. This is where a weight table, a flat rate, a carrier api or free delivery over a threshold lives.',
            'base'     => 'eZShippingManager',
            'source'   => 'kernel/classes/ezshippingmanager.php',
            'ini'      => 'shop.ini',
            'section'  => 'ShippingSettings',
            'variable' => 'Handler',
            'aliased'  => false,
            'namesAlias' => true,
            'standalone' => true,
            'classFrom' => '%alias%ShippingHandler',
            'path'     => 'shippinghandlers/%alias%shippinghandler.php',
            'suffix'   => 'shippinghandler',
            'extra'    => array(
                array( 'variable' => 'ExtensionDirectories[]',
                       'value'    => '%extension%',
                       'what'     => 'The extension whose shippinghandlers/ directory is searched. Without this line the file is never looked for.' ) ),
            'note'     => 'There is no shipping handler by default, so nothing is being replaced here: until one is registered a basket has no delivery cost at all.',
            'methods'  => array(
                array( 'name' => 'getShippingInfo', 'signature' => 'getShippingInfo( $productCollectionID )',
                       'returns' => 'false',
                       'what' => "What delivery costs, as array( 'description' => ..., 'cost' => ..., 'vat_value' => ..., 'is_vat_inc' => ... ). A shipping_items key may carry one such array per parcel when the basket is split. Return false for no charge. Read on every basket and checkout page." ),
                array( 'name' => 'updateShippingInfo', 'signature' => 'updateShippingInfo( $productCollectionID )',
                       'returns' => 'false',
                       'what' => 'Works the cost out again because the basket changed. The place to call a carrier, if one is called at all - not getShippingInfo(), which is read far more often.' ),
                array( 'name' => 'purgeShippingInfo', 'signature' => 'purgeShippingInfo( $productCollectionID )',
                       'returns' => 'true',
                       'what' => 'Forgets whatever was worked out for this basket. Called when the basket is emptied or the order placed.' ) ) ),

        'basketinfo' => array(
            'title'    => 'Basket info handler',
            'what'     => 'What the basket totals come to, once everything else has had its say.',
            'why'      => 'This runs after the prices, the VAT and the shipping are known and may change the totals. It is where a discount code, a member price, a rounding rule or a minimum order charge belongs - in one place, rather than in every template that shows a total.',
            'base'     => 'eZDefaultBasketInfoHandler',
            'source'   => 'kernel/classes/basketinfohandlers/ezdefaultbasketinfohandler.php',
            'ini'      => 'shop.ini',
            'section'  => 'BasketInfoSettings',
            'variable' => 'Handler',
            'aliased'  => false,
            'namesAlias' => true,
            'classFrom' => '%alias%BasketInfoHandler',
            'path'     => 'basketinfohandlers/%alias%basketinfohandler.php',
            'suffix'   => 'basketinfohandler',
            'override' => true,
            'extra'    => array(
                array( 'variable' => 'ExtensionDirectories[]',
                       'value'    => '%extension%',
                       'what'     => 'The extension whose basketinfohandlers/ directory is searched. Without this line the file is never looked for.' ) ),
            'note'     => 'ezdefault is registered out of the box. Registering another replaces it, so whatever the default did has to be done here too, or be deliberately dropped.',
            'methods'  => array(
                array( 'name' => 'updatePriceInfo', 'signature' => 'updatePriceInfo( $productCollectionID, &$basketInfo )',
                       'returns' => 'true',
                       'what' => "Changes the totals in place. \$basketInfo carries total_ex_vat, total_inc_vat and the per rate lists; whatever is left in it is what the basket and the order show. Called on every basket page, so anything slow here is felt everywhere." ) ) ),

        'exchangerate' => array(
            'title'    => 'Exchange rate handler',
            'what'     => 'Where the rates between the shop currencies come from.',
            'why'      => 'The handler that ships reads the European Central Bank feed, which covers the currencies it covers and no others. A handler of your own is how rates come from a bank, a provider, or a spreadsheet a person maintains - updated by the same cronjob, stored the same way, shown in the same place.',
            'base'     => 'eZExchangeRatesUpdateHandler',
            'source'   => 'kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php',
            'ini'      => 'shop.ini',
            'section'  => 'ExchangeRatesSettings',
            'variable' => 'ExchangeRatesUpdateHandler',
            'aliased'  => false,
            'namesAlias' => true,
            'classFrom' => '%alias%handler',
            'path'     => 'exchangeratehandlers/%alias%/%alias%handler.php',
            'suffix'   => 'handler',
            'override' => true,
            'extra'    => array(
                array( 'variable' => 'ExtensionDirectories[]',
                       'value'    => '%extension%',
                       'what'     => 'The extension whose exchangeratehandlers/ directory is searched. Without this line the file is never looked for.' ) ),
            'note'     => 'The alias goes in the directory name, the file name and the class name. All three are lower case, because the kernel lower cases the setting before it looks.',
            'methods'  => array(
                array( 'name' => 'initialize', 'signature' => 'initialize( $params = array() )',
                       'returns' => 'true',
                       'what' => 'Reads whatever the handler needs to know before it can ask for rates - a url, a key, a list of currencies. Called before requestRates().' ),
                array( 'name' => 'requestRates', 'signature' => 'requestRates()',
                       'returns' => 'true',
                       'what' => 'Fetches the rates and puts them in place with setRateList() and setBaseCurrency(). Return false on a failure rather than storing half a list: a partial update leaves some prices converted at yesterday\'s rate and some at the wrong one.' ),
                array( 'name' => 'rateList', 'signature' => 'rateList()',
                       'returns' => 'array()',
                       'what' => 'What was fetched, as currency code to rate against the base. Read by the shop after requestRates() has run.' ),
                array( 'name' => 'baseCurrency', 'signature' => 'baseCurrency()',
                       'returns' => "''",
                       'what' => 'Which currency the rates are against. The shop converts through this, so it has to be one it knows.' ) ) ),

        'attributeoperator' => array(
            'title'    => 'Attribute operator format',
            'what'     => 'A new format the |attribute template operator can print in.',
            'why'      => 'attribute( show ) is how a template author finds out what is in a variable, and it prints html because that is where it usually goes. A formatter of your own prints the same walk as json for a browser console, as plain text for a log, or as anything else that reads better than a table in a page.',
            'base'     => 'ezpAttributeOperatorFormatterInterface',
            'source'   => 'kernel/private/eztemplate/ezpattributeoperatorformatterinterface.php',
            'ini'      => 'template.ini',
            'section'  => 'AttributeOperator',
            'variable' => 'OutputFormatter',
            'aliased'  => true,
            'interface' => true,
            'suffix'   => 'attributeformatter',
            'note'     => 'The alias is the third argument to the operator: {$node|attribute( show, 2, myformat )}. It has to be a word a template author will remember, because nothing lists them.',
            'methods'  => array(
                array( 'name' => 'header', 'signature' => 'header( $value, $showValues )',
                       'returns' => "''",
                       'what' => 'What comes before the walk: a table head, an opening bracket, a line saying what is being shown. Called once, before any line.' ),
                array( 'name' => 'line', 'signature' => 'line( $key, $item, $showValues, $level )',
                       'returns' => "''",
                       'what' => 'One key and its value, at a depth. Called once per attribute, depth first. $showValues says whether the value is wanted or only the name, and $level is how deep, which is what indenting reads.' ),
                array( 'name' => 'exportScalar', 'signature' => 'exportScalar( $value )',
                       'returns' => "''",
                       'what' => 'One plain value on its way into the output. This is where escaping belongs: what is being printed is content, and it is being printed into a page.' ) ) ),

        'restroutefilter' => array(
            'title'    => 'REST route filter',
            'what'     => 'What decides whether a REST route needs the caller to have proved who they are.',
            'why'      => 'The filter that ships reads a list of exceptions out of rest.ini. A filter of your own can decide per request - by route, by method, by what is being asked for - which is the difference between one public endpoint and a second copy of the api with the authentication taken out.',
            'base'     => 'ezpRestRouteFilterInterface',
            'source'   => 'kernel/private/rest/classes/interfaces/route_filter.php',
            'ini'      => 'rest.ini',
            'section'  => 'RouteSettings',
            'variable' => 'RouteSettingImpl',
            'aliased'  => false,
            'suffix'   => 'routefilter',
            'note'     => 'This one decides who may reach what. A filter that answers false too easily opens the whole api; the safe default is to let nothing through that is not listed.',
            'methods'  => array(
                array( 'name' => 'shallDoActionWithRoute', 'signature' => 'shallDoActionWithRoute( $routeInfo )',
                       'returns' => 'true',
                       'what' => 'Whether this route still has to be authenticated. True means it does, which is the safe answer; false lets the request past without a caller. Returning true when unsure is how a mistake here costs nothing.' ) ) ),

        'ajaxfunction' => array(
            'title'    => 'Server side ajax function',
            'what'     => 'A class of functions a page can call over http and get json back from.',
            'why'      => 'A module view is a page: it has a template, a layout and a policy. An ajax function is a method that takes an argument list and returns a value, reached at one address, with the answer encoded for you. It is the right shape for the small things a page asks for while it is open.',
            'base'     => 'ezjscServerFunctions',
            'source'   => 'extension/ezjscore/classes/ezjscserverfunctions.php',
            'ini'      => 'ezjscore.ini',
            'section'  => 'ezjscServer',
            'variable' => 'FunctionList',
            'aliased'  => false,
            'appended' => true,
            'suffix'   => 'serverfunctions',
            'note'     => 'The arguments arrive from the browser as strings in an array. Nothing has checked them. Everything this class does with them is as exposed as a module view, and has none of a module view\'s policy checking unless it is asked for below.',
            'extra'    => array(
                array( 'section'  => 'ezjscServer_%alias%',
                       'variable' => 'Class',
                       'value'    => '%class%',
                       'what'     => 'The class the alias above stands for.' ),
                array( 'section'  => 'ezjscServer_%alias%',
                       'variable' => 'Functions[]',
                       'value'    => 'call',
                       'what'     => 'The functions that may be reached. Anything not listed here cannot be called from a browser, which is the point of listing them.' ),
                array( 'section'  => 'ezjscServer_%alias%',
                       'variable' => 'PermissionPrFunction',
                       'value'    => 'enabled',
                       'what'     => 'Check a policy for each function separately rather than one for the class. With this on, add <alias>_<function> to a role before anybody can call it.' ) ),
            'methods'  => array(
                array( 'name' => 'call', 'signature' => 'call( $args )',
                       'static' => true,
                       'new' => true,
                       'returns' => 'array()',
                       'what' => 'The function itself. $args is what the browser sent, as an array of strings, in the order it sent them - untrusted, unchecked, and every one of them to be looked at before it is used. Whatever is returned is encoded and sent back.' ),
                array( 'name' => 'getCacheTime', 'signature' => 'getCacheTime( $functionName )',
                       'static' => true,
                       'returns' => '0',
                       'what' => 'How long an answer may be kept. Return -1 for an answer that must never be cached, which is anything that depends on who is asking.' ) ) ),

        'packagecreation' => array(
            'title'    => 'Package creation handler',
            'what'     => 'A wizard in the admin that gathers something up into a package.',
            'why'      => 'The export screens for classes, objects, styles and extensions are each one of these. A handler of your own adds a screen of the same kind for whatever an extension owns, with the steps, the forms and the validation it needs, rather than a document telling somebody what to copy.',
            'base'     => 'eZPackageCreationHandler',
            'source'   => 'kernel/classes/ezpackagecreationhandler.php',
            'ini'      => 'package.ini',
            'section'  => 'CreationSettings',
            'variable' => 'HandlerAlias',
            'aliased'  => true,
            'suffix'   => 'packagecreator',
            'note'     => 'A creation handler pairs with a package handler: this gathers the item up, and that one installs it somewhere else. Neither is much use alone.',
            'constructor' => array(
                'what' => 'The steps the wizard walks, in order. The four below the first are the ones every package needs; the first is yours. Each step names a template and up to three methods.',
                'parameters' => '$id',
                'body' => array(
                    '$steps = array();',
                    '',
                    "// One step of your own. The template is looked for in",
                    "// design/<design>/templates/packagecreators/ .",
                    "\$steps[] = array( 'id'      => 'mystep',",
                    "                  'name'    => ezpI18n::tr( 'extension/%extension%', 'What to include' ),",
                    "                  'methods' => array( 'initialize' => 'initializeMyStep',",
                    "                                      'validate'   => 'validateMyStep',",
                    "                                      'commit'     => 'commitMyStep' ),",
                    "                  'template' => 'mystep.tpl' );",
                    '',
                    '// The steps every package has. Leave these last.',
                    '$steps[] = $this->packageInformationStep();',
                    '$steps[] = $this->packageMaintainerStep();',
                    '$steps[] = $this->packageChangelogStep();',
                    '',
                    "parent::__construct( \$id, ezpI18n::tr( 'extension/%extension%', 'My export' ), \$steps );" ) ),
            'methods'  => array(
                array( 'name' => 'initializeStep', 'signature' => 'initializeStep( $package, $http, $step, &$persistentData, $tpl )',
                       'returns' => 'true',
                       'what' => 'Puts what a step needs in front of the person: lists to pick from, defaults, anything fetched. Runs before the form is drawn.' ),
                array( 'name' => 'validateStep', 'signature' => 'validateStep( $package, $http, $currentStepID, &$stepMap, &$persistentData, &$errorList )',
                       'returns' => 'eZPackageCreationHandler::STEP_VALIDATION_STATE_VALID',
                       'what' => 'Checks what was sent. Add a message to $errorList and return INVALID to keep the person on this step; nothing is stored until every step has passed.' ),
                array( 'name' => 'commitStep', 'signature' => 'commitStep( $package, $http, $step, &$persistentData, $tpl )',
                       'returns' => 'true',
                       'what' => 'Writes this step\'s answer into the package being built. By the time this runs the answer has been checked.' ),
                array( 'name' => 'finalize', 'signature' => 'finalize( &$package, $http, &$persistentData )',
                       'returns' => 'true',
                       'what' => 'The last thing, once every step is done: the place to put the files into the package and write the install nodes that the package handler will read back.' ) ) ),

        'packageinstall' => array(
            'title'    => 'Package installation handler',
            'what'     => 'A wizard in the admin that puts a package item in, with the questions that go with it.',
            'why'      => 'Installing is rarely one button: something already exists, a name clashes, a choice has to be made. This is where those questions are asked, once, instead of the install half failing and leaving somebody to work out what happened.',
            'base'     => 'eZPackageInstallationHandler',
            'source'   => 'kernel/classes/ezpackageinstallationhandler.php',
            'ini'      => 'package.ini',
            'section'  => 'InstallerSettings',
            'variable' => 'HandlerAlias',
            'aliased'  => true,
            'suffix'   => 'packageinstaller',
            'note'     => 'The alias here and the alias of the package handler that carries the item are the same word. If they disagree the item is carried and never offered.',
            'constructor' => array(
                'what' => 'The kernel builds this with the package and the item it is about to install. The steps are the questions asked before it goes in.',
                'parameters' => '$package, $type, $installItem, $name = null, $steps = null',
                'body' => array(
                    '$steps = array();',
                    '',
                    "\$steps[] = array( 'id'      => 'mystep',",
                    "                  'name'    => ezpI18n::tr( 'extension/%extension%', 'How to install this' ),",
                    "                  'methods' => array( 'initialize' => 'initializeMyStep',",
                    "                                      'validate'   => 'validateMyStep',",
                    "                                      'commit'     => 'commitMyStep' ),",
                    "                  'template' => 'mystep.tpl' );",
                    '',
                    "parent::__construct( \$package, \$type, \$installItem,",
                    "                     ezpI18n::tr( 'extension/%extension%', 'My install' ), \$steps );" ) ),
            'methods'  => array(
                array( 'name' => 'initializeStep', 'signature' => 'initializeStep( $package, $http, $step, &$persistentData, $tpl, $module )',
                       'returns' => 'true',
                       'what' => 'Works out what the person has to be asked - what already exists, what would be replaced - and puts it in front of them.' ),
                array( 'name' => 'validateStep', 'signature' => 'validateStep( $package, $http, $currentStepID, &$stepMap, &$persistentData, &$errorList )',
                       'returns' => 'eZPackageInstallationHandler::STEP_VALIDATION_STATE_VALID',
                       'what' => 'Checks the answer before anything is changed. Nothing has been installed yet at this point, and that is the whole value of the step.' ),
                array( 'name' => 'commitStep', 'signature' => 'commitStep( $package, $http, $step, &$persistentData, $tpl )',
                       'returns' => 'true',
                       'what' => 'Remembers the answer for the install itself to read.' ),
                array( 'name' => 'finalize', 'signature' => 'finalize( $package, $http, &$persistentData )',
                       'returns' => 'true',
                       'what' => 'Does the install, with every question already answered.' ),
                array( 'name' => 'reset', 'signature' => 'reset()',
                       'returns' => 'true',
                       'what' => 'Forgets a half finished run, so that starting again starts clean rather than carrying the last attempt.' ) ) ),
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

        // Some kinds are not looked up: the kernel builds the class name out of
        // the alias and includes a file whose path it builds the same way. For
        // those the name is not a choice, and offering it as one only invites
        // a handler that is never found.
        if ( !empty( $recipe['classFrom'] ) && $settings['alias'] !== '' )
            $settings['class'] = self::safeClass(
                str_replace( '%alias%', $settings['alias'], $recipe['classFrom'] ) );
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

        if ( $recipe !== false
             && ( $recipe['aliased'] || !empty( $recipe['appended'] ) || !empty( $recipe['namesAlias'] ) )
             && $settings['alias'] === '' )
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
        if ( !empty( $recipe['standalone'] ) )
            $php .= " * Answers the calls " . $recipe['base'] . " makes. There is nothing to\n"
                  . " * extend: the contract is the method list below, and nothing else.\n"
                  . " * Registered in\n";
        elseif ( !empty( $recipe['interface'] ) )
            $php .= " * Satisfies " . $recipe['base'] . ", which the kernel loads in place of its\n"
                  . " * own. Registered in\n";
        else
            $php .= " * Replaces what " . $recipe['base'] . " does by default. Registered in\n";
        $php .= " * " . $recipe['ini'] . " [" . $recipe['section'] . "] " . $recipe['variable'];
        $php .= $recipe['aliased'] ? "[" . $settings['alias'] . "]" : '';
        $php .= ".\n *\n";
        $php .= " * Every method below is one the kernel calls. None of them does anything\n";
        $php .= " * useful yet; each says what it is for, when it is called, and what it has\n";
        $php .= " * to give back.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $keyword = !empty( $recipe['interface'] ) ? 'implements' : 'extends';
        $php .= "class " . $class;
        // A contract with nothing behind it: the kernel only asks that the
        // methods are there, so there is nothing to extend.
        $php .= empty( $recipe['standalone'] ) ? " " . $keyword . " " . $recipe['base'] : '';
        $php .= "\n{\n";

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
            $body .= "    public function __construct( "
                   . ( isset( $recipe['constructor']['parameters'] ) ? $recipe['constructor']['parameters'] : '' )
                   . " )\n    {\n";

            if ( isset( $recipe['constructor']['body'] ) )
                foreach ( $recipe['constructor']['body'] as $line )
                    $body .= $line === '' ? "\n" : "        " . $line . "\n";
            else
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

        if ( !empty( $recipe['namesAlias'] ) )
        {
            // The kernel builds the class name and the file path out of this
            // one word. Nothing names the class anywhere.
            $ini .= "# The name the kernel works both the file path and the class name out\n";
            $ini .= "# of: " . self::classPath( $settings, $recipe ) . ",\n";
            $ini .= "# holding class " . $settings['class'] . ".\n";
            $ini .= $recipe['variable'] . "=" . $settings['alias'] . "\n";
        }
        elseif ( !empty( $recipe['appended'] ) )
        {
            // An appended array: the kernel walks it and looks for a file whose
            // path it works out from the name. The class is never named here.
            $ini .= "# One more entry in the list the kernel walks. It is the name, not the\n";
            $ini .= empty( $recipe['path'] )
                    ? "# class: what the name stands for is said below.\n"
                    : "# class: the file is looked for at a path worked out from it, which is\n"
                      . "# " . self::classPath( $settings, $recipe ) . " inside this extension.\n";
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
        {
            $section = $recipe['section'];

            foreach ( $recipe['extra'] as $line )
            {
                // A setting in another section opens that section. They are
                // written in the order the recipe gives, so a recipe that
                // jumps back and forth writes a file that says so.
                $wanted = isset( $line['section'] )
                          ? str_replace( array( '%extension%', '%alias%', '%class%' ),
                                         array( $settings['name'], $settings['alias'], $settings['class'] ),
                                         $line['section'] )
                          : $recipe['section'];

                if ( $wanted !== $section )
                {
                    $ini .= "\n[" . $wanted . "]\n";
                    $section = $wanted;
                }

                $ini .= "\n# " . wordwrap( $line['what'], 72, "\n# " ) . "\n";
                $ini .= $line['variable'] . "=" . str_replace( array( '%extension%', '%alias%', '%class%' ),
                                                               array( $settings['name'], $settings['alias'], $settings['class'] ),
                                                               $line['value'] ) . "\n";
            }
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

            case 'vat':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'Every price shown with tax goes through the manager, which builds whichever handler the setting names.',
                           'code'  => "\$percent = eZVATManager::getVAT( \$contentObject, \$country );\n\n"
                                    . "// And, where the buyer is known:\n"
                                    . "\$percent = eZVATManager::getVAT( \$contentObject, false, \$user );" ),
                    array( 'title' => 'Trying it alone',
                           'what'  => 'Built by name rather than through the setting, so the default cannot answer instead.',
                           'code'  => "\$handler = new " . $class . "();\n\n"
                                    . "var_dump( \$handler->getVatPercent( eZContentObject::fetch( 42 ), 'GB' ) );" ),
                    array( 'title' => 'Refusing to guess', 'in' => 'class',
                           'what'  => 'Returning false stops the sale rather than taxing it at a rate nobody chose. Charging the wrong tax quietly is worse than not selling.',
                           'code'  => "public function getVatPercent( \$object, \$country )\n"
                                    . "{\n"
                                    . "    \$category = \$this->getProductCategory( \$object );\n"
                                    . "    if ( !\$category )\n"
                                    . "        return false;\n\n"
                                    . "    \$type = \$this->chooseVatType( \$category, \$country );\n\n"
                                    . "    return \$type ? \$type->attribute( 'percentage' ) : false;\n"
                                    . "}" ) );

            case 'shipping':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'The basket and checkout pages ask the manager, which builds whichever handler the setting names. Without a handler there is no delivery cost at all.',
                           'code'  => "\$info = eZShippingManager::getShippingInfo( \$basket->attribute( 'productcollection_id' ) );\n\n"
                                    . "if ( \$info !== false )\n"
                                    . "    echo \$info['description'], ': ', \$info['cost'], PHP_EOL;" ),
                    array( 'title' => 'What to give back',
                           'what'  => 'One charge for the whole basket, or one per parcel under shipping_items when it is split. is_vat_inc says whether the cost already has tax in it, and getting that wrong is a rounding error nobody finds for months.',
                           'code'  => "return array(\n"
                                    . "    'description' => 'Next day delivery',\n"
                                    . "    'cost'        => 9.99,\n"
                                    . "    'vat_value'   => 20,\n"
                                    . "    'is_vat_inc'  => 0 );\n\n"
                                    . "// Or, when it goes in more than one parcel:\n"
                                    . "return array(\n"
                                    . "    'description'    => 'Two parcels',\n"
                                    . "    'cost'           => 14.98,\n"
                                    . "    'vat_value'      => 20,\n"
                                    . "    'is_vat_inc'     => 0,\n"
                                    . "    'shipping_items' => array(\n"
                                    . "        array( 'cost' => 9.99, 'vat_value' => 20, 'is_vat_inc' => 0 ),\n"
                                    . "        array( 'cost' => 4.99, 'vat_value' => 20, 'is_vat_inc' => 0 ) ) );" ),
                    array( 'title' => 'Where the slow work goes', 'in' => 'class',
                           'what'  => 'getShippingInfo() is read on every page that shows a basket. Anything that has to be asked of a carrier belongs in updateShippingInfo(), which runs only when the basket changes, with the answer kept in between.',
                           'code'  => "public function updateShippingInfo( \$productCollectionID )\n"
                                    . "{\n"
                                    . "    \$cost = \$this->askTheCarrier( \$productCollectionID );\n"
                                    . "    \$this->remember( \$productCollectionID, \$cost );\n\n"
                                    . "    return true;\n"
                                    . "}\n\n"
                                    . "public function getShippingInfo( \$productCollectionID )\n"
                                    . "{\n"
                                    . "    return \$this->remembered( \$productCollectionID );\n"
                                    . "}" ),
                    array( 'title' => 'In a template',
                           'what'  => 'What the handler returns is what the basket template is given.',
                           'code'  => "// {def \$shipping=fetch( 'shop', 'basket' ).shipping_info}\n"
                                    . "// {\$shipping.description}: {\$shipping.cost|l10n( 'currency' )}" ) );

            case 'basketinfo':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'The handler is asked after the prices, the tax and the shipping are all known, and may change the totals before anything is shown.',
                           'code'  => "\$basketInfo = eZShippingManager::updateBasketInfo( \$productCollectionID, \$basketInfo );\n\n"
                                    . "echo \$basketInfo['total_ex_vat'], ' / ', \$basketInfo['total_inc_vat'];" ),
                    array( 'title' => 'Changing a total', 'in' => 'class',
                           'what'  => 'The array is passed by reference: change it in place and give back true. Whatever is left in it is what the basket page, the order and the confirmation email all show, so the three cannot disagree.',
                           'code'  => "public function updatePriceInfo( \$productCollectionID, &\$basketInfo )\n"
                                    . "{\n"
                                    . "    parent::updatePriceInfo( \$productCollectionID, \$basketInfo );\n\n"
                                    . "    if ( \$basketInfo['total_ex_vat'] < 25 )\n"
                                    . "    {\n"
                                    . "        \$basketInfo['total_ex_vat']  += 3.00;\n"
                                    . "        \$basketInfo['total_inc_vat'] += 3.60;\n"
                                    . "    }\n\n"
                                    . "    return true;\n"
                                    . "}" ),
                    array( 'title' => 'What is in the array',
                           'what'  => 'The totals, and the same split by rate of tax. Change a total without changing the list it came from and the two stop adding up, which shows on the invoice and not before.',
                           'code'  => "// total_ex_vat            the whole basket before tax\n"
                                    . "// total_inc_vat           and after it\n"
                                    . "// price_list_ex_vat       per rate of tax, before\n"
                                    . "// price_list_inc_vat      per rate of tax, after" ) );

            case 'exchangerate':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'The currency cronjob builds the handler by name and asks it for the rates.',
                           'code'  => "// php runcronjobs.php -s <siteaccess> currency\n"
                                    . "\$handler = eZExchangeRatesUpdateHandler::create( " . self::phpString( $settings['alias'] ) . " );\n"
                                    . "\$handler->initialize();\n\n"
                                    . "if ( \$handler->requestRates() )\n"
                                    . "{\n"
                                    . "    print_r( \$handler->rateList() );\n"
                                    . "    echo \$handler->baseCurrency(), PHP_EOL;\n"
                                    . "}" ),
                    array( 'title' => 'Fetching them', 'in' => 'class',
                           'what'  => 'Store the whole list or none of it. Half a list leaves some prices converted at the new rate and some at the old, and nothing in the shop will say so.',
                           'code'  => "public function requestRates()\n"
                                    . "{\n"
                                    . "    \$rates = \$this->fetchFromWherever();\n\n"
                                    . "    if ( !is_array( \$rates ) || !count( \$rates ) )\n"
                                    . "        return false;\n\n"
                                    . "    \$this->setBaseCurrency( 'EUR' );\n"
                                    . "    \$this->setRateList( \$rates );   // array( 'GBP' => 0.85, 'USD' => 1.09 )\n\n"
                                    . "    return true;\n"
                                    . "}" ),
                    array( 'title' => 'What the shop does with them',
                           'what'  => 'The rates are stored against the currencies the shop knows. A currency the list does not mention keeps the rate it had.',
                           'code'  => "foreach ( eZCurrencyData::fetchList() as \$currency )\n"
                                    . "    echo \$currency->attribute( 'code' ), ' ',\n"
                                    . "         \$currency->attribute( 'rate_value' ), PHP_EOL;" ) );

            case 'attributeoperator':
                return array(
                    array( 'title' => 'What a template author writes',
                           'what'  => 'The third argument to the operator is the alias this formatter is registered under.',
                           'code'  => "// {\$node|attribute( show, 2, " . $settings['alias'] . " )}\n"
                                    . "//\n"
                                    . "// show   - print the values as well as the names\n"
                                    . "// 2      - how many levels down to go\n"
                                    . "// " . str_pad( $settings['alias'], 6 ) . " - this formatter" ),
                    array( 'title' => 'What the kernel does',
                           'what'  => 'The manager builds the formatter named for the format, then walks the variable and calls header() once and line() for everything in it.',
                           'code'  => "\$output  = \$formatter->header( \$value, \$showValues );\n"
                                    . "\$output .= \$formatter->line( 'name', 'My article', true, 0 );\n"
                                    . "\$output .= \$formatter->line( 'id', 42, true, 1 );" ),
                    array( 'title' => 'Trying it alone',
                           'what'  => 'The three methods take plain values, so the whole formatter can be exercised without a template anywhere near it.',
                           'code'  => "\$formatter = new " . $class . "();\n\n"
                                    . "echo \$formatter->header( array( 'a' => 1 ), true );\n"
                                    . "echo \$formatter->line( 'a', 1, true, 0 );" ),
                    array( 'title' => 'The part that matters', 'in' => 'class',
                           'what'  => 'What is being printed is content, and it is being printed into a page. exportScalar() is where that is made safe, once, rather than in each of the other two.',
                           'code'  => "public function exportScalar( \$value )\n"
                                    . "{\n"
                                    . "    return htmlspecialchars( (string) \$value, ENT_QUOTES, 'UTF-8' );\n"
                                    . "}" ) );

            case 'restroutefilter':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'Before a REST request is answered, the filter is asked whether that route still needs a caller who has proved who they are.',
                           'code'  => "// kernel/private/rest/classes/auth/auth_configuration.php\n"
                                    . "\$filter = ezpRestRouteFilterInterface::getRouteFilter();\n\n"
                                    . "if ( \$filter->shallDoActionWithRoute( \$routeInfo ) )\n"
                                    . "{\n"
                                    . "    // The request is authenticated before it is answered.\n"
                                    . "}" ),
                    array( 'title' => 'What is in $routeInfo',
                           'what'  => 'The controller and the action that matched, and the version asked for. That is what a decision can be made on.',
                           'code'  => "// \$routeInfo->controllerClass   e.g. ezpRestContentController\n"
                                    . "// \$routeInfo->action            e.g. viewContent\n"
                                    . "// \$routeInfo->version           e.g. 1" ),
                    array( 'title' => 'The safe shape', 'in' => 'class',
                           'what'  => 'A list of what may be reached without a caller, and true for everything else. Written the other way round - a list of what must be authenticated - a route added later is public by accident.',
                           'code'  => "public function shallDoActionWithRoute( \$routeInfo )\n"
                                    . "{\n"
                                    . "    \$open = array( 'MyPublicController_ping' );\n\n"
                                    . "    \$route = \$routeInfo->controllerClass . '_' . \$routeInfo->action;\n\n"
                                    . "    return !in_array( \$route, \$open, true );\n"
                                    . "}" ) );

            case 'ajaxfunction':
                return array(
                    array( 'title' => 'Calling it from a page',
                           'what'  => 'One address, the alias, the function, then the arguments. The answer comes back as json.',
                           'code'  => "// GET /ezjscore/call/" . $settings['alias'] . "::call::42\n"
                                    . "//\n"
                                    . "// {\\\"content\\\":...,\\\"error_text\\\":\\\"\\\",\\\"error_code\\\":0}\n\n"
                                    . "// With the javascript ezjscore ships:\n"
                                    . "// ez.setLoading( true );\n"
                                    . "// \$.ez( '" . $settings['alias'] . "::call', [ 42 ], function( data ) {\n"
                                    . "//     console.log( data.content );\n"
                                    . "// } );" ),
                    array( 'title' => 'What the kernel does',
                           'what'  => 'The router reads the alias out of ezjscore.ini, checks the function is one that may be called, checks the policy if one is asked for, and then calls it.',
                           'code'  => "\$router = ezjscServerRouter::getInstance( array( " . self::phpString( $settings['alias'] ) . ", 'call', '42' ) );\n"
                                    . "\$result = \$router->call();" ),
                    array( 'title' => 'What arrives, and what it is worth', 'in' => 'class',
                           'what'  => 'An array of strings, straight off the query string, from anyone who can reach the site. Nothing has looked at them. Every one has to be checked here, because there is nowhere else it will be.',
                           'code'  => "public static function call( \$args )\n"
                                    . "{\n"
                                    . "    \$id = isset( \$args[0] ) ? (int) \$args[0] : 0;\n\n"
                                    . "    \$node = eZContentObjectTreeNode::fetch( \$id );\n"
                                    . "    if ( !\$node instanceof eZContentObjectTreeNode )\n"
                                    . "        throw new ezcBaseFunctionalityNotSupportedException( 'node', 'not found' );\n\n"
                                    . "    // Being able to call the function is not the same as being\n"
                                    . "    // allowed to see what it answers with.\n"
                                    . "    if ( !\$node->attribute( 'can_read' ) )\n"
                                    . "        throw new ezcBaseFunctionalityNotSupportedException( 'node', 'denied' );\n\n"
                                    . "    return array( 'name' => \$node->attribute( 'name' ) );\n"
                                    . "}" ),
                    array( 'title' => 'Who may call it',
                           'what'  => 'With PermissionPrFunction on, each function needs its own policy on a role before anybody reaches it. Without it, listing a function in Functions[] is the only thing standing in the way.',
                           'code'  => "// A policy on the role: ezjscore / call / FunctionList "
                                    . $settings['alias'] . "_call" ) );

            case 'packagecreation':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'The export screen in the admin builds the handler by alias and walks its steps.',
                           'code'  => "\$handler = eZPackageCreationHandler::instance( " . self::phpString( $settings['alias'] ) . " );\n\n"
                                    . "\$package = eZPackage::create( 'mypackage', array( 'name' => 'My package' ) );\n"
                                    . "\$handler->generateStepMap( \$package, \$persistentData );" ),
                    array( 'title' => 'What a step looks like',
                           'what'  => 'An id, a name a person reads, up to three methods, and a template. The methods are named in the map; nothing looks for them by convention.',
                           'code'  => "\$step = array( 'id'       => 'mystep',\n"
                                    . "               'name'     => 'What to include',\n"
                                    . "               'methods'  => array( 'initialize' => 'initializeMyStep',\n"
                                    . "                                    'validate'   => 'validateMyStep',\n"
                                    . "                                    'commit'     => 'commitMyStep' ),\n"
                                    . "               'template' => 'mystep.tpl' );" ),
                    array( 'title' => 'Stopping on a bad answer', 'in' => 'class',
                           'what'  => 'The person stays on the step until it passes. Nothing is written until every step has, so a wizard that checks properly cannot half build a package.',
                           'code'  => "public function validateMyStep( \$package, \$http, \$currentStepID, &\$stepMap, &\$persistentData, &\$errorList )\n"
                                    . "{\n"
                                    . "    if ( !\$http->hasPostVariable( 'MyThing' ) )\n"
                                    . "    {\n"
                                    . "        \$errorList[] = array( 'description' => 'Choose something first.' );\n\n"
                                    . "        return eZPackageCreationHandler::STEP_VALIDATION_STATE_INVALID;\n"
                                    . "    }\n\n"
                                    . "    \$persistentData['my_thing'] = \$http->postVariable( 'MyThing' );\n\n"
                                    . "    return eZPackageCreationHandler::STEP_VALIDATION_STATE_VALID;\n"
                                    . "}" ),
                    array( 'title' => 'The template',
                           'what'  => 'Looked for under packagecreators/ in the design. It draws the form; the methods above read it back.',
                           'code'  => "// design/<design>/templates/packagecreators/mystep.tpl" ) );

            case 'packageinstall':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'When a package is installed, each item is offered to the installation handler registered for its type, which asks its questions before anything changes.',
                           'code'  => "\$handler = eZPackageInstallationHandler::instance( \$package,\n"
                                    . "                                                 " . self::phpString( $settings['alias'] ) . ",\n"
                                    . "                                                 \$installItem );\n\n"
                                    . "\$handler->generateStepMap( \$package, \$persistentData );" ),
                    array( 'title' => 'Asking before changing', 'in' => 'class',
                           'what'  => 'The value of a step is that it runs while nothing has happened yet. Whatever already exists can be reported, and a choice offered, rather than discovered halfway through.',
                           'code'  => "public function initializeMyStep( \$package, \$http, \$step, &\$persistentData, \$tpl, \$module )\n"
                                    . "{\n"
                                    . "    \$tpl->setVariable( 'existing', \$this->whatIsAlreadyHere( \$package ) );\n\n"
                                    . "    return true;\n"
                                    . "}" ),
                    array( 'title' => 'Starting again cleanly',
                           'what'  => 'reset() is what makes a second attempt a second attempt rather than a continuation of a failed first one.',
                           'code'  => "\$handler->reset();" ),
                    array( 'title' => 'The pair',
                           'what'  => 'This handler and the package handler that carries the item share one alias. They are two halves of the same thing: one puts the item in a package, the other takes it out again.',
                           'code'  => "// package.ini [PackageSettings]   HandlerAlias[" . $settings['alias'] . "]=...\n"
                                    . "// package.ini [InstallerSettings] HandlerAlias[" . $settings['alias'] . "]=" . $class ) );

            case 'login':
                return array(
                    array( 'title' => 'What the kernel does',
                           'what'  => 'Every handler listed in site.ini is tried in turn until one says yes. The default, standard, is eZUser itself, so leaving it in the list means a local password still works.',
                           'code'  => "// kernel/classes/datatypes/ezuser/ezuserloginhandler.php\n"
                                    . "\$handler = eZUserLoginHandler::instance( " . self::phpString( $settings['alias'] ) . " );\n"
                                    . "\$user = \$handler->loginUser( \$login, \$password );" ),
                    array( 'title' => 'Trying it alone',
                           'what'  => 'The one test worth writing, and the one people skip: that a wrong password is refused. A handler that lets everybody in passes every other test there is.',
                           'code'  => "\$user = " . $class . "::loginUser( 'someone', 'the right password' );\n"
                                    . "var_dump( \$user instanceof eZUser );   // true\n\n"
                                    . "\$user = " . $class . "::loginUser( 'someone', 'not the right password' );\n"
                                    . "var_dump( \$user );                     // false, and nothing else" ),
                    array( 'title' => 'Failing closed', 'in' => 'class',
                           'what'  => 'Everything that is not a definite yes is a no. A service that is down, an answer that does not parse, an empty name: all false. The temptation is to let people in when the directory is unreachable, and that is how a site is opened by an outage.',
                           'code'  => "public static function loginUser( \$login, \$password, \$authenticationMatch = false )\n"
                                    . "{\n"
                                    . "    if ( !is_string( \$login ) || \$login === '' || \$password === '' )\n"
                                    . "        return false;\n\n"
                                    . "    try\n"
                                    . "    {\n"
                                    . "        if ( !self::somebodyElseSaysYes( \$login, \$password ) )\n"
                                    . "            return false;\n"
                                    . "    }\n"
                                    . "    catch ( Exception \$e )\n"
                                    . "    {\n"
                                    . "        eZDebug::writeError( \$e->getMessage(), __METHOD__ );\n\n"
                                    . "        // The service is down. Nobody gets in rather than everybody.\n"
                                    . "        return false;\n"
                                    . "    }\n\n"
                                    . "    \$user = self::fetchByName( \$login );\n\n"
                                    . "    return \$user instanceof eZUser ? \$user : self::makeOne( \$login );\n"
                                    . "}" ),
                    array( 'title' => 'The user still has to exist here',
                           'what'  => 'Authenticating elsewhere does not remove the need for a local user: content is owned by one, roles are granted to one, and the admin lists them. A handler that authenticates and makes nobody leaves the visitor logged in as anonymous.',
                           'code'  => "\$user = eZUser::create( \$parentNodeID );\n"
                                    . "\$user->setAttribute( 'login', \$login );\n"
                                    . "\$user->setAttribute( 'email', \$email );\n"
                                    . "\$user->store();" ),
                    array( 'title' => 'Switching it on carefully',
                           'what'  => 'Leave standard in the list while testing. Taking it out before the new handler is proved locks everybody out of the site, including whoever would have to put it back.',
                           'code'  => "// site.ini [UserSettings]\n"
                                    . "// LoginHandler[]=standard\n"
                                    . "// LoginHandler[]=" . $settings['alias'] ) );
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
            $php .= empty( $recipe['interface'] ) && empty( $recipe['standalone'] )
                    ? " extends " . $recipe['base'] : '';
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
