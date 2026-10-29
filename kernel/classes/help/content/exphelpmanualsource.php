<?php
/**
 * The Markdown help as the source of the generated manuals (expHelpManualBuilder): the folders and pages of one
 * language's index of the help (expHelpDocumentIndex), in the order the help shows them.
 *
 * The builder only asks this object, so its tests can give it any object with the same three methods.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpManualSource
{
    /** @var expHelpDocumentIndex */
    protected $index;

    /** @var array|null the whole tree of the index */
    protected $root = null;

    /** @var bool whether pages shown in the default language instead (not translated) count as pages of this source */
    protected $withFallback;

    /**
     * @param expHelpDocumentIndex $index the index of one language (expHelpDocumentIndex::forLanguage())
     * @param bool $withFallback false for a translation: a page that has no translation is no page of it
     */
    public function __construct( expHelpDocumentIndex $index, $withFallback = true )
    {
        $this->index = $index;
        $this->withFallback = (bool)$withFallback;
    }

    /**
     * The source of the default language, and of each translation the help has a directory for
     *
     * @param string $locale a content language (ger-DE)
     * @return expHelpManualSource|null null when the help has no pages in that language
     */
    public static function forLocale( $locale )
    {
        $languages = expHelpLanguage::fromIni();
        $directory = expHelpLanguage::directoryOfLocale( $locale );
        if ( $directory === $languages->defaultDirectory() )
            return new self( expHelpDocumentIndex::instance(), true );
        if ( !$languages->isDirectory( $directory ) )
            return null;
        return new self( expHelpDocumentIndex::forLanguage( $directory, $languages ), false );
    }

    /**
     * A folder of the help (its id, "help/editors"; "" the top) as expHelpDocumentIndex::tree() gives it: title,
     * index (its README), order, children (folders) and documents, in order
     *
     * @param string $folder
     * @return array
     */
    public function tree( $folder )
    {
        // the whole tree once: the index builds all of it for every folder asked for
        if ( $this->root === null )
            $this->root = $this->index->tree();
        $node = $this->root;
        foreach ( $folder === '' ? array() : explode( '/', trim( (string)$folder, '/' ) ) as $segment )
        {
            $found = null;
            foreach ( $node['children'] as $child )
            {
                if ( $child['name'] === $segment )
                {
                    $found = $child;
                    break;
                }
            }
            if ( $found === null )
                return array( 'name' => basename( (string)$folder ), 'path' => (string)$folder, 'title' => expHelpDocumentIndex::humanize( basename( (string)$folder ) ),
                              'index' => null, 'index_id' => null, 'order' => 1000, 'children' => array(), 'documents' => array() );
            $node = $found;
        }
        return $node;
    }

    /**
     * Whether a page is a Markdown page of this source (page() without reading it)
     *
     * @param string $id
     * @return bool
     */
    public function has( $id )
    {
        $doc = $this->index->document( $id );
        return $doc !== null && $doc['format'] === 'markdown' && empty( $doc['hidden'] ) && ( $this->withFallback || empty( $doc['fallback'] ) );
    }

    /**
     * A Markdown page: array( 'doc' => the index entry, 'meta' => its front matter, 'body' => the text without it ),
     * null when it is no Markdown page of this source
     *
     * @param string $id
     * @return array|null
     */
    public function page( $id )
    {
        $doc = $this->index->document( $id );
        if ( $doc === null || $doc['format'] !== 'markdown' || !empty( $doc['hidden'] ) || ( !$this->withFallback && !empty( $doc['fallback'] ) ) )
            return null;
        $text = $this->index->source( $id );
        if ( $text === null )
            return null;
        list( $meta, $body ) = expHelpFrontMatter::split( $text );
        return array( 'doc' => $doc, 'meta' => $meta, 'body' => $body );
    }

    /**
     * The page id of a file of the help (relative to the installation root), null when it is none
     *
     * @param string $path
     * @return string|null
     */
    public function idOf( $path )
    {
        return $this->index->idOf( $path );
    }
}

?>
