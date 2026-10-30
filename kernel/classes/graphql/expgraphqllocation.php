<?php
/**
 * A location (a node of the content tree) as the GraphQL schema sees it. The resolvers read only through these
 * methods; a test extends the class to answer them without a database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLLocation
{
    /** @var eZContentObjectTreeNode|null */
    protected $node;

    /** @var string|false */
    protected $language;

    /**
     * @param eZContentObjectTreeNode|null $node
     * @param string|false $language the translation the content below it is read in
     */
    public function __construct( $node, $language = false )
    {
        $this->node = $node;
        $this->language = $language;
    }

    /** @return eZContentObjectTreeNode|null */
    public function node()
    {
        return $this->node;
    }

    /** @return string|false */
    public function language()
    {
        return $this->language;
    }

    /** @return int */
    public function id()
    {
        return (int)$this->node->attribute( 'node_id' );
    }

    /** @return int */
    public function parentId()
    {
        return (int)$this->node->attribute( 'parent_node_id' );
    }

    /** @return int */
    public function contentId()
    {
        return (int)$this->node->attribute( 'contentobject_id' );
    }

    /** @return int */
    public function depth()
    {
        return (int)$this->node->attribute( 'depth' );
    }

    /** @return string /1/2/58/ */
    public function pathString()
    {
        return (string)$this->node->attribute( 'path_string' );
    }

    /** @return int */
    public function priority()
    {
        return (int)$this->node->attribute( 'priority' );
    }

    /** @return bool */
    public function hidden()
    {
        return (bool)$this->node->attribute( 'is_hidden' );
    }

    /** @return bool hidden, or below a hidden location */
    public function invisible()
    {
        return (bool)$this->node->attribute( 'is_invisible' );
    }

    /** @return string the URL alias, about/company */
    public function urlAlias()
    {
        return (string)$this->node->urlAlias();
    }

    /** @return string the remote id of the location */
    public function remoteId()
    {
        return (string)$this->node->attribute( 'remote_id' );
    }

    /**
     * @return array the sorting of the location's children: array( array( field, ascending ) ) as eZ sort arrays
     */
    public function sortArray()
    {
        return (array)$this->node->sortArray();
    }
}

?>
