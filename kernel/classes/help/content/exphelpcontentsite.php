<?php
/**
 * Help maintained as content on a site's own pages (doc/eng-us/specifications/6.0/help-system.md, delivery path 1:
 * "templates full/help_*.tpl and line/help_*.tpl in the site design"): what the node templates of the extension
 * exphelpsite (or a site design's own) show for a topic, a manual or a chapter, read through
 * fetch( 'exphelp', 'site_topic', hash( 'object_id', ... ) ) and fetch( 'exphelp', 'site_manual', ... ).
 *
 * Access is decided by Exponential permissions alone, as everywhere in the help (expHelpContentProvider::visible()):
 * a reader sees a Released topic they may read (Anonymous: section help_public and state Released, the one policy of
 * the plan); who may edit it sees it as a preview. The templates do not filter by audience. Relations (related
 * topics, figures, the manual's topics and chapters) are shown only if the reader may see them.
 *
 * Links between topics (help:<help_id> in the text, related topics, a manual's contents) go to the topics' pages on
 * this site (their node's URL alias), not to the help of the administration.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentSite
{
    /** @var expHelpContentProvider */
    protected $provider;

    /** @var array node id => URL (memo) */
    protected $urls = array();

    public function __construct( ?expHelpContentProvider $provider = null )
    {
        $this->provider = $provider !== null ? $provider : expHelpContentProvider::instance();
    }

    /**
     * The topic of an object for a page of the site, or null when the reader may not see it
     *
     * @param int $objectId
     * @return array|null array( help_id, type, title, shortdesc, audience, platform, version_from, version_to, preview,
     *                    html (rendered, safe), related (title, shortdesc, url), figures (image_url, alt_text, caption),
     *                    feedback (whether "Was this helpful?" is offered), modified )
     */
    public function topic( $objectId )
    {
        if ( !expHelpContentProvider::isEnabled() )
            return null;
        $found = $this->provider->repository()->byObjectId( (int)$objectId );
        if ( $found === null || $found['kind'] !== 'topics' )
            return null;
        $topic = $this->provider->visible( $found['item'] );
        if ( $topic === null )
            return null;
        $site = $this;
        $renderer = new expHelpMarkdownRenderer( array(
            'link_resolver' => function ( $href ) use ( $site ) { return $site->link( $href ); },
            'image_resolver' => array( 'expHelpLinks', 'image' ),
            'image_sources' => expContentSecurityPolicy::imageSources() ) );
        $rendered = $renderer->render( expHelpContentProvider::markdown( $topic ), '' );
        $relations = $this->provider->relations( $topic );
        $related = array();
        foreach ( $relations['related'] as $item )
        {
            $url = $this->nodeUrl( isset( $item['node_id'] ) ? (int)$item['node_id'] : 0 );
            if ( $url !== '' )
                $related[] = array( 'title' => $item['title'], 'shortdesc' => $item['shortdesc'], 'url' => $url, 'help_id' => $item['help_id'] );
        }
        return array(
            'help_id' => $topic['help_id'],
            'type' => $topic['type'],
            'title' => $topic['title'],
            'shortdesc' => $topic['shortdesc'],
            'audience' => $topic['audience'],
            'platform' => $topic['platform'],
            'version_from' => $topic['version_from'],
            'version_to' => $topic['version_to'],
            'preview' => !empty( $topic['preview'] ),
            'html' => $rendered['html'],
            'related' => $related,
            'figures' => $relations['figures'],
            'feedback' => empty( $topic['preview'] ) && $topic['state'] === expHelpContentSchema::STATE_RELEASED && expHelpContentFeedback::isEnabled()
                          && self::mayAnswer(),
            'modified' => (int)$topic['modified'],
        );
    }

    /**
     * A manual or chapter for a page of the site, with its contents as links to the pages of its topics and chapters
     *
     * @param int $objectId
     * @return array|null array( kind (manual|chapter), title, subtitle, shortdesc, edition, preview, toc (kind, title,
     *                    shortdesc, url, children) )
     */
    public function map( $objectId )
    {
        if ( !expHelpContentProvider::isEnabled() )
            return null;
        $found = $this->provider->repository()->byObjectId( (int)$objectId );
        if ( $found === null || $found['kind'] !== 'maps' )
            return null;
        $map = $this->provider->visible( $found['item'] );
        if ( $map === null )
            return null;
        return array(
            'kind' => $map['class'] === 'help_manual' ? 'manual' : 'chapter',
            'title' => $map['title'],
            'subtitle' => isset( $map['subtitle'] ) ? $map['subtitle'] : '',
            'shortdesc' => $map['shortdesc'],
            'edition' => isset( $map['edition'] ) ? $map['edition'] : '',
            'preview' => !empty( $map['preview'] ),
            'toc' => $this->siteToc( $this->provider->toc( $map ) ),
        );
    }

    /**
     * The provider's contents with the URLs of the site's pages (an entry without a page is left out)
     *
     * @param array $entries expHelpContentProvider::toc()
     * @return array
     */
    protected function siteToc( array $entries )
    {
        $repository = $this->provider->repository();
        $out = array();
        foreach ( $entries as $entry )
        {
            $row = $entry['kind'] === 'topic' ? $repository->topic( $entry['help_id'] ) : $repository->map( $entry['remote_id'] );
            $url = $row !== null && isset( $row['node_id'] ) ? $this->nodeUrl( (int)$row['node_id'] ) : '';
            if ( $url === '' )
                continue;
            $out[] = array( 'kind' => $entry['kind'], 'title' => $entry['title'], 'shortdesc' => $entry['shortdesc'], 'url' => $url,
                            'children' => $this->siteToc( $entry['children'] ) );
        }
        return $out;
    }

    /**
     * A link in a topic's text: help:<help_id> (written as content/<help_id>.md by the provider) to that topic's page on
     * this site, every other link as the help writes it
     *
     * @param string $href
     * @return string|null
     */
    public function link( $href )
    {
        if ( preg_match( '#^/?content/([a-z0-9.-]{1,80})\.md(?:\#(.*))?$#', (string)$href, $m ) )
        {
            $row = $this->provider->repository()->topic( $m[1] );
            $visible = $row !== null ? $this->provider->visible( $row ) : null;
            $url = $visible !== null ? $this->nodeUrl( (int)$visible['node_id'] ) : '';
            // a topic the reader may not see is not linked: its text stays, without a way to probe for it
            if ( $url === '' )
                return null;
            return $url . ( isset( $m[2] ) && $m[2] !== '' ? '#' . rawurlencode( rawurldecode( $m[2] ) ) : '' );
        }
        return expHelpLinks::link( $href, '' );
    }

    /**
     * The address of a node on this site ('' none)
     *
     * @param int $nodeId
     * @return string
     */
    public function nodeUrl( $nodeId )
    {
        $nodeId = (int)$nodeId;
        if ( $nodeId <= 0 )
            return '';
        if ( !isset( $this->urls[$nodeId] ) )
        {
            $node = eZContentObjectTreeNode::fetch( $nodeId );
            $url = $node instanceof eZContentObjectTreeNode ? (string)$node->attribute( 'url_alias' ) : '';
            if ( $url !== '' )
                eZURI::transformURI( $url );
            $this->urls[$nodeId] = $url;
        }
        return $this->urls[$nodeId];
    }

    /**
     * Whether the reader may answer "Was this helpful?" (exphelp/feedback: exphelp/read or exphelp/feedback)
     *
     * @return bool
     */
    public static function mayAnswer()
    {
        $user = eZUser::currentUser();
        foreach ( array( 'feedback', 'read' ) as $function )
        {
            $access = $user->hasAccessTo( 'exphelp', $function );
            if ( $access['accessWord'] !== 'no' )
                return true;
        }
        return false;
    }
}

?>
