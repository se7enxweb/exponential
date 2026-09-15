<?php
/**
 * File containing the expCleanupRSS class.
 *
 * Trims the content an RSS import has created, keeping the newest items of
 * each feed and removing the rest.
 *
 * An import run adds an object for every item in the feed and never takes one
 * away, so a site importing a busy feed grows without limit: the destination
 * folder reaches tens of thousands of children, the administration interface
 * slows to a crawl on it and the search index fills with items nobody will
 * read again. Trimming it is a job every site importing a feed eventually has
 * to do, which is why this is in the kernel rather than left to each site to
 * arrange.
 *
 * Ported from the bccleanuprss extension by Brookins Consulting, whose shape
 * this keeps: the newest items of a feed are found by asking for the feed's
 * children sorted newest first and skipping the number to keep, so whatever is
 * returned is by definition the surplus. What has changed is everything around
 * that - see doc/bc/6.0/rss-import-cleanup.md.
 *
 * @copyright Copyright (C) 1999 - 2011 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or later)
 * @package kernel
 */

class expCleanupRSS
{
    /** Settings live beside the rest of the import's settings. */
    const INI_FILE  = 'content.ini';
    const INI_BLOCK = 'RSSImportCleanupSettings';

    /** @var array */
    private $counts = array( 'feeds' => 0, 'examined' => 0, 'removed' => 0 );

    /** @var array one line per feed: name, id, examined, removed */
    private $perFeed = array();

    private $enabled;
    private $keep;
    private $classes;
    private $userLogin;
    private $logFile;
    private $moveToTrash;
    private $dryRun;
    private $quiet;
    private $logger;
    private $reason = '';

    /**
     * @param array $options overrides the settings, for a caller that wants to
     *        ask for something other than what the site is configured to do:
     *        'dry-run', 'keep', 'quiet'.
     */
    public function __construct( array $options = array() )
    {
        $ini = eZINI::instance( self::INI_FILE );

        $this->enabled = $this->setting( $ini, 'Enabled', 'false' ) === 'true';
        $this->keep    = (int)$this->setting( $ini, 'KeepPerFeed', 100 );
        $this->classes = (array)$this->setting( $ini, 'ClassIdentifiers', array() );
        $this->userLogin   = (string)$this->setting( $ini, 'CleanupUser', 'admin' );
        $this->logFile     = (string)$this->setting( $ini, 'LogFile', 'rsscleanup.log' );
        $this->moveToTrash = $this->setting( $ini, 'MoveToTrash', 'true' ) === 'true';

        if ( isset( $options['keep'] ) && (int)$options['keep'] >= 0 )
            $this->keep = (int)$options['keep'];

        $this->dryRun = !empty( $options['dry-run'] );
        $this->quiet  = !empty( $options['quiet'] );

        $this->classes = array_values( array_filter( array_map( 'trim', $this->classes ), 'strlen' ) );

        $this->logger = new eZLog();
    }

    private function setting( eZINI $ini, $name, $default )
    {
        return $ini->hasVariable( self::INI_BLOCK, $name )
             ? $ini->variable( self::INI_BLOCK, $name )
             : $default;
    }

    /**
     * Whether this is configured to the point where it may remove anything.
     *
     * Three separate things have to be true, and none of them is true of an
     * installation that has not been set up for this. It removes content
     * permanently when the site says so, and there is no undoing that, so it
     * does nothing at all until somebody has said what to remove.
     *
     * @return bool
     */
    public function isEnabled()
    {
        if ( !$this->enabled )
        {
            $this->reason = self::INI_BLOCK . '/Enabled is not true in ' . self::INI_FILE . '.';
            return false;
        }

        if ( !$this->classes )
        {
            $this->reason = self::INI_BLOCK . '/ClassIdentifiers names no class, so there is nothing'
                          . ' this is allowed to remove.';
            return false;
        }

        if ( $this->keep < 1 )
        {
            // Keeping none would empty the feed on the next run, which is not
            // a cleanup and is never what anyone means.
            $this->reason = self::INI_BLOCK . '/KeepPerFeed is ' . $this->keep
                          . '; it has to be at least 1.';
            return false;
        }

        return true;
    }

    /** Why isEnabled() said no. */
    public function reason()
    {
        return $this->reason;
    }

    /**
     * Trim every active import's destination.
     *
     * @return bool false when it was not configured to run.
     */
    public function cleanup()
    {
        if ( !$this->isEnabled() )
        {
            $this->log( 'RSS import cleanup is not running: ' . $this->reason );
            return false;
        }

        if ( !$this->loginCleanupUser() )
            return false;

        $this->log( sprintf( '%sKeeping the newest %d item(s) of each feed, of class %s.',
                             $this->dryRun ? 'Dry run. ' : '',
                             $this->keep, implode( ', ', $this->classes ) ) );

        $imports = eZRSSImport::fetchActiveList();

        if ( !$imports )
        {
            $this->log( 'No active RSS import; nothing to do.' );
            return true;
        }

        foreach ( $imports as $import )
            $this->cleanupImport( $import );

        $this->log( sprintf( '%s%d item(s) of %d examined removed, across %d feed(s).',
                             $this->dryRun ? 'Dry run, nothing was removed. ' : '',
                             $this->counts['removed'], $this->counts['examined'], $this->counts['feeds'] ) );

        return true;
    }

    /** One import's destination. */
    private function cleanupImport( $import )
    {
        $name         = (string)$import->attribute( 'name' );
        $parentNodeID = (int)$import->attribute( 'destination_node_id' );

        ++$this->counts['feeds'];

        if ( $parentNodeID < 1 )
        {
            $this->log( sprintf( 'Feed "%s" has no destination; skipped.', $name ) );
            return;
        }

        if ( !eZContentObjectTreeNode::fetch( $parentNodeID ) instanceof eZContentObjectTreeNode )
        {
            $this->log( sprintf( 'Feed "%s" points at node %d, which is gone; skipped.',
                                 $name, $parentNodeID ), true );
            return;
        }

        $surplus = $this->surplusNodes( $parentNodeID );
        $count   = count( $surplus );

        $this->counts['examined'] += $count;
        $this->perFeed[] = array( 'name' => $name, 'node' => $parentNodeID, 'surplus' => $count );

        if ( !$count )
        {
            $this->log( sprintf( 'Feed "%s" (node %d): nothing above the limit.', $name, $parentNodeID ) );
            return;
        }

        $this->log( sprintf( 'Feed "%s" (node %d): %d item(s) above the limit.',
                             $name, $parentNodeID, $count ) );

        $nodeIDs = array();
        foreach ( $surplus as $node )
        {
            $nodeIDs[] = (int)$node->attribute( 'node_id' );
            $this->log( sprintf( '  %s [node %d]', $node->attribute( 'name' ), $node->attribute( 'node_id' ) ) );
        }

        if ( $this->dryRun )
            return;

        // One call rather than one per item: removeSubtrees() works on a list,
        // and each call of it clears caches and reindexes.
        eZContentObjectTreeNode::removeSubtrees( $nodeIDs, $this->moveToTrash );

        $this->counts['removed'] += $count;
    }

    /**
     * The items of a feed that are past the number to keep.
     *
     * Asked for newest first and offset by the number to keep, so what comes
     * back is the surplus and nothing else - there is no second pass deciding
     * what to drop, and no window in which the newest items could be returned.
     *
     * @param int $parentNodeID
     * @return eZContentObjectTreeNode[]
     */
    private function surplusNodes( $parentNodeID )
    {
        $nodes = eZContentObjectTreeNode::subTreeByNodeID(
            array( 'ClassFilterType'  => 'include',
                   'ClassFilterArray' => $this->classes,
                   'Depth'            => 1,
                   'Offset'           => $this->keep,
                   'SortBy'           => array( 'published', false ),
                   'status'           => eZContentObject::STATUS_PUBLISHED ),
            $parentNodeID );

        return is_array( $nodes ) ? $nodes : array();
    }

    /**
     * Become the user the removal runs as.
     *
     * Removing content is permission checked, so this has to be somebody who
     * may do it. A missing account used to be a fatal error one line later;
     * here it stops the run and says so.
     *
     * @return bool
     */
    private function loginCleanupUser()
    {
        $user = eZUser::fetchByName( $this->userLogin );

        if ( !$user instanceof eZUser )
        {
            $this->log( sprintf( 'The cleanup user "%s" does not exist; nothing was removed.'
                               . ' Set %s/CleanupUser in %s.',
                                 $this->userLogin, self::INI_BLOCK, self::INI_FILE ), true );
            return false;
        }

        eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );

        return true;
    }

    /**
     * @return array feeds, examined, removed
     */
    public function counts()
    {
        return $this->counts;
    }

    /**
     * @return array one entry per feed: name, node, surplus
     */
    public function perFeed()
    {
        return $this->perFeed;
    }

    /**
     * To the log file, and to the terminal when there is one.
     *
     * The original reached for a global $cli, which is set only when a script
     * happens to have made one. eZCLI::instance() is always answerable and
     * knows itself whether anything is listening.
     */
    private function log( $message, $isError = false )
    {
        if ( $this->logFile !== '' )
            $this->logger->write( $message, $this->logFile );

        if ( $this->quiet )
            return;

        $cli = eZCLI::instance();

        if ( $isError )
            $cli->error( $message );
        else
            $cli->output( $message );
    }
}

?>
