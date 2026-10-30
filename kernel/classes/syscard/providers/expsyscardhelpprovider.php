<?php
/**
 * File containing the expSysCardHelpProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The help: how many pages it serves, its languages, and whether its index is current (an index older than the
 * pages is a hint: ./console exp:help index builds it). exp:info adds the coverage of each language. The index is
 * never built here: only its cached files are read.
 */
class expSysCardHelpProvider extends expSysCardBaseProvider
{
    protected $id = 'help';
    protected $section = 'site';
    protected $priority = 20;
    protected $budget = 2.0;

    public function collect( expSysCardContext $context )
    {
        if ( !$context->iniEnabled( 'exphelp.ini', 'HelpSettings', 'Enabled', 'true' ) )
            return array( $this->fact( 'state', 'Help', 'off' ) );
        $state = $context->get( 'help.state', function () {
            if ( !class_exists( 'expHelpDocumentIndex' ) || !class_exists( 'expHelpLanguage' ) )
                return null;
            // the default language's index, whatever the locale of the siteaccess the card runs in: its pages are the
            // pages of every language (a translation shows the original where it has no page of its own)
            $languages = expHelpLanguage::fromIni();
            $default = $languages->defaultDirectory();
            $index = expHelpDocumentIndex::forLanguage( $default, $languages );
            $dir = $index->cacheDir();
            $count = expHelpDocumentIndex::pageCount( $dir );
            if ( $count !== null )
                $pages = $count['pages'];
            else
            {
                // a page list written before it carried the number: only its titles, which leave out the generated pages
                $titles = expHelpDocumentIndex::pageTitles( $dir );
                $pages = is_array( $titles ) ? count( $titles ) : null;
            }
            // current when every language has the index of its current signature, as ./console exp:help index leaves it
            $staleLanguages = array();
            foreach ( $languages->directories() as $directory )
            {
                $language = $directory === $default ? $index : expHelpDocumentIndex::forLanguage( $directory, $languages );
                $languageDir = $language->cacheDir();
                if ( $languageDir === null || !is_file( $languageDir . '/index-' . $language->signature() . '.php' ) )
                    $staleLanguages[] = $directory;
            }
            return array( 'pages' => $pages, 'current' => empty( $staleLanguages ), 'stale' => $staleLanguages,
                          'languages' => $languages->directories() );
        } );
        if ( !is_array( $state ) )
            return array( $this->fact( 'state', 'Help', 'n/a' ) );
        $languages = isset( $state['languages'] ) ? (array)$state['languages'] : array();
        $pages = isset( $state['pages'] ) ? $state['pages'] : null;
        $text = ( $pages === null ? 'not indexed yet' : number_format( (int)$pages ) . ' pages' ) . ( empty( $languages ) ? '' : ', ' . implode( ' ', $languages ) );
        $stale = empty( $state['current'] );
        $staleLanguages = isset( $state['stale'] ) ? (array)$state['stale'] : array();
        // only some languages: name them; all of them (or not known which): just "index stale"
        $which = !empty( $staleLanguages ) && count( $staleLanguages ) < count( $languages ) ? ' (index stale: ' . implode( ' ', $staleLanguages ) . ')' : ' (index stale)';
        $facts = array( $this->fact( 'state', 'Help', $text . ( $stale ? $which : '' ), $stale ? expSysCardFact::WARN : expSysCardFact::INFO,
                                     $stale ? 'The help index is older than the pages (or missing): run ./console exp:help index so search and navigation show every page.' : '' ) );
        if ( $context->mode !== expSysCardContext::MODE_INFO )
            return $facts;
        $coverage = $context->get( 'help.coverage', function () {
            return class_exists( 'expHelpDocumentIndex' ) ? expHelpDocumentIndex::instance()->languageCoverage() : null;
        } );
        foreach ( is_array( $coverage ) ? $coverage : array() as $directory => $row )
        {
            $total = isset( $row['total'] ) ? (int)$row['total'] : 0;
            $count = isset( $row['pages'] ) ? (int)$row['pages'] : 0;
            $facts[] = $this->fact( 'coverage:' . $directory, 'Help ' . $directory, number_format( $count ) . ' / ' . number_format( $total ) . ' pages'
                                    . ( $total > 0 ? ' (' . (int)floor( 100 * $count / $total ) . '%)' : '' ), expSysCardFact::INFO, '', 'help' );
        }
        return $facts;
    }
}
