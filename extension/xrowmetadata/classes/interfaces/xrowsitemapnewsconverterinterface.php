<?php

interface xrowSitemapNewsConverterInterface
{
    /**
     * Receives news information from a node
     * @param eZContentObjectTreeNode $node
     * @return xrowSitemapItemNews
     */
    public function addNews( eZContentObjectTreeNode $node );
}
