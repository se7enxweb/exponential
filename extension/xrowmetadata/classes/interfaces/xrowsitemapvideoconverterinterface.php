<?php

interface xrowSitemapVideoConverterInterface
{
    /**
     * Receives video information from a node
     * @param eZContentObjectTreeNode $node
     * @return xrowSitemapItemVideo
     */
    public function addVideo( eZContentObjectTreeNode $node );
}
