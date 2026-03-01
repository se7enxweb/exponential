<?php

class xrowSitemapConverter implements xrowSitemapImageConverterInterface, xrowSitemapVideoConverterInterface, xrowSitemapNewsConverterInterface
{
    function addNews( eZContentObjectTreeNode $node )
    {
        $ini = eZINI::instance( 'xrowsitemap.ini' );
        $news = new xrowSitemapItemNews();
        $images = array();
        // Adding the root node
        $object = $node->object();

        $news->publication_date = new DateTime( '@' . $object->attribute( 'published' ) );
        $news->title = $object->attribute( 'name' );
        $user = eZUser::fetch( eZUser::anonymousId() );
        if ( ! xrowSitemapTools::checkAccess( $object, $user, 'read' ) )
        {
            $news->access = 'Subscription';
        }
        // Get Genres, if enable
        if ( ( ! $ini->hasVariable( 'NewsSitemapSettings', 'UseGenres' ) || ( $ini->hasVariable( 'NewsSitemapSettings', 'UseGenres' ) && $ini->variable( 'NewsSitemapSettings', 'UseGenres' ) != 'disable' ) ) && $ini->hasVariable( 'NewsSitemapSettings', 'Genres' ) )
        {
            $genres_array = $ini->variable( 'NewsSitemapSettings', 'Genres' );

            // set genre if set
            if ( isset( $genres_array[$node->ClassIdentifier] ) )
            {
                $news->genres = array(
                    $genres_array[$node->ClassIdentifier]
                );
            }
        }

        $dm = $node->dataMap();
        $news->keywords = array();

        foreach ( $dm as $attribute )
        {
            switch ( $attribute->DataTypeString )
            {

                case 'xrowmetadata':
                    if ( $attribute->hasContent() )
                    {
                        $keywordattribute = $attribute->content();
                        $news->keywords = array_merge( $news->keywords, $keywordattribute->keywords );
                    }
                    break;
                case 'ezkeyword':
                    if ( $attribute->hasContent() )
                    {
                        $keywordattribute = $attribute->content();
                        $news->keywords = array_merge( $news->keywords, $keywordattribute->KeywordArray );
                    }
                    break;
            }
        }
        if ( $ini->hasVariable( 'NewsSitemapSettings', 'AdditionalKeywordList' ) )
        {
            $news->keywords = array_merge( $news->keywords, $ini->variable( 'NewsSitemapSettings', 'AdditionalKeywordList' ) );
        }
        return $news;
    }

    function addVideo( eZContentObjectTreeNode $node )
    {
        $ini = eZINI::instance( 'xrowsitemap.ini' );
        $video = new xrowSitemapItemVideo();
        $video->title = $node->attribute( 'name' );
        #fixing nodes without parents. They should not exist.
        if( $node->attribute( 'parent' ) )
        {
            $video->categories = array(
                $node->attribute( 'parent' )->attribute( 'name' )
            );
        }
        $object = $node->object();
        $video->view_count = eZViewCounter::fetch( $node->attribute( 'node_id' ) )->Count;
        $video->publication_date = new DateTime( '@' . $object->attribute( 'published' ) );

        $dm = $node->attribute( 'data_map' );
        foreach ( $dm as $attribute )
        {
            switch ( $attribute->DataTypeString )
            {
                case 'xrowmetadata':
                    if ( $attribute->hasContent() )
                    {
                        $keywordattribute = $attribute->content();
                        $video->tags = array_merge( $video->tags, $keywordattribute->keywords );
                    }
                    break;
                case 'ezkeyword':
                    if ( $attribute->hasContent() )
                    {
                        $keywordattribute = $attribute->content();
                        $video->tags = array_merge( $video->tags, $keywordattribute->KeywordArray );
                    }
                    break;
                case 'ezimage':

                    if ( $attribute->hasContent() )
                    {
                        $imagedata = $attribute->content();
                        if ( $ini->hasVariable( 'SitemapSettings', 'ImageAlias' ) )
                        {
                            $aliasdata = $imagedata->attribute( $ini->variable( 'SitemapSettings', 'ImageAlias' ) );
                            $video->thumbnail_loc = 'https://' . xrowSitemapTools::domain() . '/' . $aliasdata['url'];
                        }
                        else
                        {
                            $aliasdata = $imagedata->attribute( 'original' );
                            $video->thumbnail_loc = 'https://' . xrowSitemapTools::domain() . '/' . $aliasdata['url'];
                        }
                    }
                    break;
                case 'ezmedia':
                    if ( $attribute->hasContent() )
                    {
                        $content = $attribute->content();
                        $uri = "content/download/" . $attribute->attribute( 'contentobject_id' ) . '/' . $content->attribute( 'contentobject_attribute_id' ) . '/' . $content->attribute( 'original_filename' );
                        $video->content_loc = 'https://' . xrowSitemapTools::domain() . '/' . $uri;
                    }
                    break;
                case 'xrowvideo':
                    if ( $attribute->hasContent() )
                    {
                        $content = $attribute->content();
                        $uri = "content/download/" . $content["media"]->attribute->ContentObjectID . '/' . $content["media"]->attribute->ID . '/' . $content["binary"]->OriginalFilename ;
                        $video->content_loc = 'https://' . xrowSitemapTools::domain() . '/' . $uri;
                        $video->duration = (int) $content["video"]["duration"];
                    }
                    break;
            }
            switch ( $attribute->ContentClassAttributeIdentifier )
            {
                case 'description':
                    if ( $attribute->hasContent() )
                    {
                        $content = $attribute->content();
                        $descriptions=substr(strip_tags($content->ContentObjectAttribute->DataText),0,2048);
                        $video->description = $descriptions;
                    }
                    break;
            }
        }
        return $video;
    }

    function addImage( eZContentObjectTreeNode $node )
    {
        $images = false;
        $ini = eZINI::instance( 'xrowsitemap.ini' );
        $dm = $node->attribute( 'data_map' );
        foreach ( $dm as $attribute )
        {
            switch ( $attribute->DataTypeString )
            {
                case 'ezimage':

                    if ( $attribute->hasContent() )
                    {
                        if ( $images === false )
                        {
                            $images = array();
                        }
                        $imagedata = $attribute->content();
                        $image = new xrowSitemapItemImage();
                        if ( $ini->hasVariable( 'SitemapSettings', 'ImageAlias' ) )
                        {
                            $aliasdata = $imagedata->attribute( $ini->variable( 'SitemapSettings', 'ImageAlias' ) );
                            $image->url = 'https://' . xrowSitemapTools::domain() . '/' . $aliasdata['url'];
                        }
                        else
                        {
                            $aliasdata = $imagedata->attribute( 'original' );
                            $image->url = 'https://' . xrowSitemapTools::domain() . '/' . $aliasdata['url'];
                        }
                        if ( $imagedata->attribute( 'alternative_text' ) )
                        {
                            $image->caption = $imagedata->attribute( 'alternative_text' );
                        }
                        $image->title = $node->attribute( 'name' );
                        $images[] = $image;
                    }
                    break;
            }
        }
        return $images;
    }
}
