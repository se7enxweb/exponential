<?php
/**
 * @package nxcPowerContent
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    12 Apr 2010
 **/

class nxcPowerContent {

	private $cli = false;

	private $rest = false;

	public function __construct( $cli = false, $rest = false ) {
		$this->db  = eZDB::instance();
		$this->cli = $cli;
		$this->rest = $rest;
	}

	/**
	 * Creates new content object and store it
	 *
	 * @param $params array(
	 *     'class'                   => eZContentClass          Content class object
	 *     'classIdentifier'         => string                  Content object`s class identifier, not necessary if class is set
	 *     'parentNode'              => eZContentObjectTreeNode Parent node object
	 *     'parentNodeID'            => int                     Content object`s parent node ID, not necessary if parentNode is set
	 *     'attributes'              => array(                  Content object`s attributes
	 *         string identifier => string stringValue
	 *     ),
	 *     'remoteID'                => string                  Content object`s remote ID, not necessary
	 *     'ownerID'                 => int                     Owner`s content object ID, not necessary
	 *     'sectionID'               => int                     Section ID, not necessary
	 *     'languageLocal'           => string                  Language local, not necessary
	 *     'publishDate'             => int                     Creation timestamp, if not specified - current timestamp will be used
	 *     'additionalParentNodeIDs' => array                   additionalParentNodes, Additional parent node ids
	 *     'versionStatus'           => int                     Published version status, not necessary
	 *     'visibility'              => bool                    Nodes visibility
	 * )
	 * @return eZContentObject|bool Created content object if it was created, otherwise false
	 */
	public function createObject( $params ) {
		$this->db->begin();

		$class = ( isset( $params['class'] ) ) ? $params['class'] : eZContentClass::fetchByIdentifier( $params['classIdentifier'] );
		if( $class instanceof eZContentClass === false ) {
			$this->error( 'Can`t fetch class by Identifier: ' . $params['classIdentifier'] );
			$this->db->rollback();
			return false;
		}

		$parentNode = ( isset( $params['parentNode'] ) ) ? $params['parentNode'] : eZContentObjectTreeNode::fetch( $params['parentNodeID'] );
		if( $parentNode instanceof eZContentObjectTreeNode === false ) {
			$this->error( 'Can`t fetch parent node by ID: ' . $params['parentNodeID'] );
			$this->db->rollback();
			return false;
		}

		$additionalParentNodes = array();
		if( isset( $params['additionalParentNodeIDs'] ) ) {
			foreach( $params['additionalParentNodeIDs'] as $nodeID ) {
				$node = eZContentObjectTreeNode::fetch( $nodeID );
				if( $node instanceof eZContentObjectTreeNode ) {
					if( $nodeID != $parentNode->attribute( 'node_id' ) ) {
						$additionalParentNodes[] = $node;
					}
				} else {
					$this->error( 'Can`t fetch additional parent node by ID: ' . $nodeID );
				}
			}
		}

		$ownerID        = ( isset( $params['ownerID'] ) ) ? $params['ownerID'] : eZUser::currentUserID();
		$sectionID      = ( isset( $params['sectionID'] ) ) ? $params['sectionID'] : $parentNode->attribute( 'object' )->attribute( 'section_id' );
		$languageLocale = ( isset( $params['languageLocale'] ) ) ? $params['languageLocale'] : false;
		$remoteID       = ( isset( $params['remoteID'] ) ) ? $params['remoteID'] : false;
		$publishDate    = ( isset( $params['publishDate'] ) ) ? $params['publishDate'] : time();
		$versionStatus  = ( isset( $params['versionStatus'] ) ) ? $params['versionStatus'] : eZContentObjectVersion::STATUS_PUBLISHED;
		$visibility     = ( isset( $params['visibility'] ) ) ? (bool) $params['visibility'] : true;

		if( $remoteID !== false ) {
			$object = eZContentObject::fetchByRemoteID( $remoteID );
			if( $object instanceof eZContentObject ) {
				$this->db->rollback();
				$this->error( 'Object "' . $object->attribute( 'name' ) . '" (class: ' . $object->attribute( 'class_name' ) . ') with remote ID ' . $remoteID . ' allready exist.' );
				return false;
			}
		}

		$object = $class->instantiate( $ownerID, $sectionID, false, $languageLocale );
		$object->setAttribute( 'published', $publishDate );
		$object->setAttribute( 'modified', $publishDate );
		if( $remoteID ) {
			$object->setAttribute( 'remote_id',  $remoteID );
		}
		$object->store();

		$this->debug( 'Starting create object (class: ' . $object->attribute( 'class_name' ) . ') with remote ID ' . $remoteID . ' in main node: ' . $parentNode->attribute( 'node_id' ) );

		$nodeAssignment = eZNodeAssignment::create(
			array(
				'contentobject_id'      => $object->attribute( 'id' ),
				'contentobject_version' => $object->attribute( 'current_version' ),
				'parent_node'           => $parentNode->attribute( 'node_id' ),
				'is_main'               => 1,
				'sort_field' => $class->attribute( 'sort_field' ),
				'sort_order' => $class->attribute( 'sort_order' )
			)
		);
		$nodeAssignment->store();

		$version = $object->version( $object->attribute( 'current_version' ) );
		$version->setAttribute( 'modified', $publishDate );
		$version->setAttribute( 'status', $versionStatus );
		$version->store();

		// return var_dump( $params['attributes']); die();
		//$this->debug("Attribute-Set:\n\n");
		//$this->debug( $this->setObjectAttributes( $object, $params['attributes'] ) );

		$this->setObjectAttributes( $object, $params['attributes'] );

		foreach( $additionalParentNodes as $node ) {
			$nodeAssignment = eZNodeAssignment::create(
				array(
					'contentobject_id'      => $object->attribute( 'id' ),
					'contentobject_version' => $object->attribute( 'current_version' ),
					'parent_node'           => $node->attribute( 'node_id' ),
					'is_main'               => 0
				)
			);
			$nodeAssignment->store();
		}

		$object->commitInputRelations( $object->attribute( 'current_version' ) );
		$object->resetInputRelationList();

		$pathResult = eZModule::addGlobalPathList( array( 'kernel', 'extension/nxc_powercontent' ) );
		$contentModule = eZModule::findModule( 'content', null, array( 'kernel', 'extension/nxc_powercontent' ) );
		// return var_dump($contentModule); die();

		$operationResult = eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $object->attribute( 'id' ),
                                                                                             'version' => $version->attribute( 'version' ) ) );
                //var_dump( $operationResult );
		
		$this->db->commit();

		if( $visibility === false ) {
			$this->updateVisibility( $object, $visibility );
		}

		$this->debug( '[Created] "' . $object->attribute( 'name' ) . '" (Node ID: ' . $object->attribute( 'main_node_id' ) . ')', array( 'green' ) );
		return $object;
	}

	/**
	 * Updates content object
	 *
	 * @param $params array(
	 *     'object'                  => eZContentObject         Content object
	 *     'attributes'              => array(                  Content object`s attributes
	 *         string identifier => string stringValue
	 *     ),
	 *     'parentNode'              => eZContentObjectTreeNode Parent node object, not necessary
	 *     'parentNodeID'            => int                     Content object`s parent node ID, not necessary
	 *     'additionalParentNodeIDs' => array                   additionalParentNodeIDs, Additional parent node ids
	 *     'visibility'              => bool                    Nodes visibility
	 * )
	 * @return bool true if object was updated, otherwise false
	 */
 	public function updateObject( $params ) {
 		$this->db->begin();

 		$object = $params['object'];
		if( $object instanceof eZContentObject === false ) {
			$this->error( 'Content object is empty' );
			$this->db->rollback();
			return false;
		}
		$this->debug( 'Starting update "' . $object->attribute( 'name' ) . '" object (class: ' . $object->attribute( 'class_name' ) . ') with remote ID ' . $object->attribute( 'remote_id' ) );

		$visibility = ( isset( $params['visibility'] ) ) ? (bool) $params['visibility'] : true;
		$parentNode = false;
		if( isset( $params['parentNode'] ) ) {
			$parentNode = $params['parentNode'];
		} elseif( isset( $params['parentNodeID'] ) ) {
			$parentNode = eZContentObjectTreeNode::fetch( $params['parentNodeID'] );
		}
		if(
			$parentNode instanceof eZContentObjectTreeNode
			&& $object->attribute( 'main_node' ) instanceof eZContentObjectTreeNode
		) {
			if( $parentNode->attribute( 'node_id' ) != $object->attribute( 'main_node' )->attribute( 'parent_node_id' ) ) {
				eZContentOperationCollection::moveNode(
					$object->attribute( 'main_node_id' ),
					$object->attribute( 'id' ),
					$parentNode->attribute( 'node_id' )
				);
			}
		}

		$additionalParentNodeIDs = ( isset( $params['additionalParentNodeIDs'] ) ) ? (array) $params['additionalParentNodeIDs'] : array();
		$additionalParentNodes = array();
		foreach( $additionalParentNodeIDs as $nodeID ) {
			$additionalParentNode = eZContentObjectTreeNode::fetch( $nodeID );
			if( $additionalParentNode instanceof eZContentObjectTreeNode ) {
				if( ( $parentNode instanceof eZContentObjectTreeNode ) && ( $nodeID != $parentNode->attribute( 'node_id' ) ) ) {
					$additionalParentNodes[] = $additionalParentNode;
				}
			} else {
				$this->error( 'Can`t fetch additional parent node by ID: ' . $nodeID );
			}
		}

		if( count( $additionalParentNodes ) > 0 ) {
			$nodeAssigments = eZPersistentObject::fetchObjectList(
				eZNodeAssignment::definition(),
				null,
				array(
					'contentobject_id'      => $object->attribute( 'id' ),
					'contentobject_version' => $object->attribute( 'current_version' ),
					'is_main'               => 0
				),
				null,
				null,
				true
			);
			$removeNodeIDs = array();
			foreach( $nodeAssigments as $assigment ) {
				$node = $assigment->attribute( 'node' );
				if( $node instanceof eZContentObjectTreeNode ) {
					if( in_array( $node->attribute( 'parent_node_id' ), $additionalParentNodeIDs ) === false ) {
						$removeNodeIDs[] = $node->attribute( 'node_id' );
						$assigment->purge();
					}
				}
			}

			if( count( $removeNodeIDs ) > 0 ) {
				$info = eZContentObjectTreeNode::subtreeRemovalInformation( $removeNodeIDs );
				foreach( $info['delete_list'] as $deleteItem ) {
				    $node = $deleteItem['node'];
					if( $node === null ) {
						continue;
					}

					if( $deleteItem['can_remove'] ) {
						eZContentObjectTreeNode::removeSubtrees( array( $node->attribute( 'node_id' ) ), false );
						$this->debug( '[Removed additional location] "' . $node->attribute( 'name' ) . '"', array( 'red' ) );
					}
				}
			}

			foreach( $additionalParentNodes as $node ) {
				$nodeAssignment = eZNodeAssignment::create(
					array(
						'contentobject_id'      => $object->attribute( 'id' ),
						'contentobject_version' => $object->attribute( 'current_version' ),
						'parent_node'           => $node->attribute( 'node_id' ),
						'is_main'               => 0
					)
				);
				$nodeAssignment->store();
			}
		}

		$this->setObjectAttributes( $object, $params['attributes'] );

		$object->commitInputRelations( $object->attribute( 'current_version' ) );
		$object->resetInputRelationList();
		eZOperationHandler::execute(
			'content',
			'publish',
			array(
				'object_id' => $object->attribute( 'id' ),
				'version'   => $object->attribute( 'current_version' )
			)
		);

		$this->db->commit();

		$this->updateVisibility( $object, $visibility );

		$this->debug( '[Updated] "' . $object->attribute( 'name' ) . '"', array( 'yellow' ) );
		return true;
	}

	/**
	 * Removes content object
	 *
	 * @param eZContentObject $object
	 * @return bool true if object was removed, otherwise false
	 */
	public function removeObject( eZContentObject $object ) {
	        $ret = false;
		$objectName = $object->attribute( 'name' );
		$this->debug( 'Removing "' . $objectName . '" object (class: ' . $object->attribute( 'class_name' ) . ') with remote ID ' . $object->attribute( 'remote_id' ) );

		$this->db->begin();

		$object->resetDataMap();
		eZContentObject::clearCache( $object->attribute( 'id' ) );

		if( is_null( $object->attribute( 'main_node' ) ) ) {
			$object->purge();
			$this->db->commit();
			$this->debug( '[Removed] "' . $objectName . '"' );
			$ret = true;
		} else {
			$removeNodeIDs = array( $object->attribute( 'main_node' )->attribute( 'node_id' ) );

			$nodeAssigments = eZNodeAssignment::fetchForObject( $object->attribute( 'id' ) );
			foreach( $nodeAssigments as $assigment ) {
				$node = $assigment->attribute( 'node' );
				if( $node instanceof eZContentObjectTreeNode ) {
					$removeNodeIDs[] = $node->attribute( 'node_id' );
				}
			}
			$removeNodeIDs = array_unique( $removeNodeIDs );

			$info = eZContentObjectTreeNode::subtreeRemovalInformation( $removeNodeIDs );
			foreach( $info['delete_list'] as $deleteItem ) {
				$node = $deleteItem['node'];
				if( $node === null ) {
					continue;
				}

				if( $deleteItem['can_remove'] ) {
					eZContentObjectTreeNode::removeSubtrees( array( $node->attribute( 'node_id' ) ), false );
					$this->debug( '[Removed] "' . $objectName . '", Node ID: ' . $node->attribute( 'node_id' ), array( 'red' ) );
   				}
			}

			$this->db->commit();
			$ret = true;
		}

		return $ret;
	}

	/**
	 * Set content object attributes
	 *
	 * @private
	 * @param eZContentObject $object
	 * @param array( attributeIdentifier => attributeStringValue ) $attributesValues
	 * @return void
	 */
	private function setObjectAttributes( eZContentObject $object, array $attributesValues ) {
		$attributes = $object->dataMap();
		foreach( $attributesValues as $identifier => $value ) {
			if( isset( $attributes[ $identifier ] ) ) {
				$attribute = $attributes[ $identifier ];
				switch ( $attribute->attribute( 'data_type_string' ) ) {
					case 'ezimage': {
						$arr      = explode( '|', trim( $value ) );
						$source   = str_replace( ' ', '%20', $arr[0] );

						if( file_exists( $source ) ) {
							// Handle local files
							$content = $attribute->attribute( 'content' );
							$content->initializeFromFile( $source, isset( $arr[1] ) ? $arr[1] : null );
							$content->store( $attribute );
						} else {
							// Handle remote files
							$filename = 'var/cache/'. md5( microtime() ) . substr( $source, strrpos( $source, '.' ) );

							if( !empty( $source ) ) {
								if( in_array( 'curl', get_loaded_extensions() ) ) {
									$ch = curl_init();
									$out = fopen( $filename, 'w' );
									curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
									curl_setopt( $ch, CURLOPT_URL, $source );
									curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
									curl_setopt( $ch, CURLOPT_FILE, $out );
									curl_exec( $ch );
									curl_close( $ch );
									fclose( $out );
								} else {
									copy( $source, $filename );
								}
							}

							if( file_exists( $filename ) ) {
								$content = $attribute->attribute( 'content' );
								$content->initializeFromFile( $filename, isset( $arr[1] ) ? $arr[1] : null );
								$content->store( $attribute );
								unlink( $filename );
							}
						}

						break;
					}
					case 'ezxmltext': {
						$parser     = new eZOEInputParser();
						$value      = '<div>' . trim( $value ) . '</div>';
						$document   = $parser->process( $value );
						$urlIDArray = $parser->getUrlIDArray();
						if( count( $urlIDArray ) > 0 ) {
							eZOEXMLInput::updateUrlObjectLinks( $attribute, $urlIDArray );
						}

						$object->appendInputRelationList(
							$parser->getLinkedObjectIDArray(),
							eZContentObject::RELATION_LINK
						);
						$object->appendInputRelationList(
							$parser->getEmbeddedObjectIDArray(),
							eZContentObject::RELATION_EMBED
						);

						$value = ( $document ) ? eZXMLTextType::domString( $document ) : null;
						$attribute->fromString( $value );
						break;
					}
					default: {
						if( is_callable( array( $attribute, 'fromString' ) ) ) {
							$attribute->fromString( $value );
						} else {
							$attribute->setAttribute( 'data_text', $value );
						}
					}
				}

				$attribute->store();
			}
		}
	}

	/**
	 * Change node`s visibility
	 *
	 * @private
	 * @param eZContentObject $object
	 * @param bool $visibility
	 * @return void
	 */
	private function updateVisibility( $object, $visibility = true ) {
		$action = $visibility ? 'show' : 'hide';

		$nodeAssigments = eZPersistentObject::fetchObjectList(
			eZNodeAssignment::definition(),
			null,
			array(
				'contentobject_id'      => $object->attribute( 'id' ),
				'contentobject_version' => $object->attribute( 'current_version' )
			),
			null,
			null,
			true
		);
		foreach( $nodeAssigments as $nodeAssigment ) {
			$node = $nodeAssigment->attribute( 'node' );
			if( $node instanceof eZContentObjectTreeNode === false ) {
				continue;
			}

			if( (bool) !$node->attribute( 'is_hidden' ) === (bool) $visibility ) {
				continue;
			}

			if( $action == 'show' ) {
				eZContentObjectTreeNode::unhideSubTree( $node );
			} else {
				eZContentObjectTreeNode::hideSubTree( $node );
			}

			eZSearch::updateNodeVisibility( $node->attribute( 'node_id' ), $action );
		}
	}

	/**
	 * Debugs error
	 *
	 * @private
	 * @param string $message Error message
	 * @return void
	 */
	public function error( $message ) {
		if( $this->cli instanceof eZCLI ) {
			$string = $this->cli->stylize( 'error', $message );
			$this->cli->output( $string );
		} elseif( $this->rest == true ) {
			throw new ezpContentFieldNotFoundException( $message );
		} else {
			eZDebug::writeError( $message, 'NXC PowerContent' );
		}
	}

	/**
	 * Debugs text message
	 *
	 * @private
	 * @param string $message Debug message
	 * @param array $styles CLI styles
	 * @return void
	 */
	public function debug( $message, array $styles = array( 'white' ) ) {
		if( $this->cli instanceof eZCLI ) {
			foreach( $styles as $style ) {
				$message = $this->cli->stylize( $style, $message );
			}
			$this->cli->output( $message );
		} else {
			eZDebug::writeDebug( $message, 'NXC PowerContent' );
		}
	}
}
?>
