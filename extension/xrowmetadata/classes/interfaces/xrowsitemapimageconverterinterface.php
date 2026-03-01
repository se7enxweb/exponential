<?php

interface xrowSitemapImageConverterInterface
{
    /**
     * Receives image information from a node
     * @param eZContentObjectTreeNode $node
     * @return xrowSitemapItemImage
     */
    public function addImage( eZContentObjectTreeNode $node );
}
