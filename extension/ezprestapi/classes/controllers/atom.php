<?php
/**
 *  File containing the atom controller.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

/**
 * Controller class for producing atom feeds of content structure.
 *
 * This controller will provide several actions for retrieving content. There
 * will be basic collections, and more specialiced actions to retrieve delta
 * of new content based on updates since last-modified-date and/or feed entry
 * IDs.
 */
class ezpRestAtomController extends ezcMvcController
{

    public function doCollection()
    {
        // Document need to contain the minimum require data for each collection
        // Author, title, updated, id, link

        $crit = new ezpContentCriteria();
        $crit->accept[] = ezpContentCriteria::location()->subtree( ezpContentLocation::fetchByNodeId( $this->nodeId ) );

        $retData = array();
        $baseUri = substr( $this->request->protocol, 0, strpos( $this->request->protocol, "-" ) ) . "://{$this->request->host}";

        foreach ( ezpContentRepository::query( $crit ) as $node )
        {
            $retData[] = array(
                "objectName" => $node->name,
                "author" => $node->owner->Name,
                "modified" => $node->dateModified,
                "published" => $node->datePublished,
                "classIdentifier" => $node->classIdentifier,
                "nodeUrl" => $baseUri . $this->getRouter()->generateUrl( 1, array( "nodeId" => $node->locations->node_id ) )
            );
        }

        $result = new ezcMvcResult();
        $result->variables["collection"] = $retData;
        return $result;
    }
}
