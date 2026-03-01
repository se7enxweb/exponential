<?php

class xrowMetaData extends ezcBaseStruct
{
    public $priority;
    public $change;
    public $title;
    public $keywords = array();
    public $description;
    public $sitemap_use;
    public $canonical_url;

    public function __construct( $title = false, $keywords = array(), $description = false, $priority = false, $change = false, $sitemap_use = false, $canonical_url = false)
    {
        $this->title = $title;
        $this->keywords = $keywords;
        $this->description = $description;
        $this->canonical_url = $canonical_url;
        $this->sitemap_use = $sitemap_use;
        if ( empty( $priority ) )
        {
            $this->priority = null;
        }
        else
        {
            $this->priority = $priority;
        }
        if ( empty( $change ) )
        {
            $this->change = 'daily';
        }
        else
        {
            $this->change = $change;
        }
        if ( $sitemap_use === false )
        {
            $this->sitemap_use = '1';
        }
        elseif ( empty( $sitemap_use ) )
        {
            $this->sitemap_use = '0';
        }
        else
        {
            $this->sitemap_use = '1';
        }
    }

    function hasAttribute( $name )
    {
        $classname = get_class( $this );
        $vars = get_class_vars( $classname );
        if ( array_key_exists( $name, $vars ) )
            return true;
        else
            return false;
    }

    function attributes()
    {
        return array('title','description','keywords','sitemap_use','canonical_url');
    }

    function attribute( $name )
    {
        return $this->$name;
    }

    /**
     * @return xrowMetaData
     */
    static public function __set_state( array $array )
    {
        return new xrowMetaData( $array['title'], $array['keywords'], $array['description'], $array['priority'], $array['change'], $array['canonical_url'] );
    }
}
