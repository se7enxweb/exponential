<?php

class eZClusterDirectoryIterator implements Iterator
{
    function __construct( $dirname, $scope = null )
    {
        $handler = eZClusterFileHandler::instance();
        if ( $handler instanceof eZFSFileHandler )
        {
            $dir = new DirectoryIterator( $dirname );
            foreach ( $dir as $file )
            {
                if ( $file->isDot() and $file->isDir() )
                {
                    continue;
                }

                $this->array[] = eZClusterFileHandler::instance( $dirname . '/' . $file->getFilename() );
            }
        }
        elseif ( $handler instanceof eZDFSFileHandler or $handler instanceof eZDBFileHandler )
        {
            $sitemaplist = $handler->getFileList( array(
                "sitemap"
            ) );
            foreach ( $sitemaplist as $sitemap )
            {
                $so = eZClusterFileHandler::instance( $sitemap );
                if ( strpos( $so->name(), $dirname ) !== false and !$so->isExpired( -1, time(), null ) )
                #if ( strpos( $so->name(), $dirname ) !== false )
                {
                    $this->array[] = $so;
                }
            }
        }
        $this->position = 0;
    }
    private $position = 0;
    private $array = array();

    function rewind()
    {

        $this->position = 0;
    }

    function current()
    {

        return $this->array[$this->position];
    }

    function key()
    {

        return $this->position;
    }

    function next()
    {

        ++ $this->position;
    }

    function valid()
    {

        return isset( $this->array[$this->position] );
    }
}
