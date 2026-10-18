<?php
/**
 * File containing the expHelpRecordRenderer class.
 *
 * Plain text pages that are lists of records (doc/release-overview, doc/branch-overview: the release and branch
 * history kept from the original SVN repository) shown as a structured page instead of one block of text
 * (doc/features/6.0/help-system.md, "Record pages"). A record is a name line ("3.0-1:", "stable/3.5:", with or
 * without the colon) followed by "- " field lines: "Key=value" ("Revision=2251", "Merged Revision=1159:1162",
 * "Release url=http://... (2252)") or "Key value" for dates and revisions ("Date 01-Nov-2002", "Release date
 * 29-Sep-2009", "Revision 2454 and 2458"); other field lines are notes. A name line without fields is a section
 * label ("Various development branches:"); text before the first record is the page's introduction.
 *
 * The format is recognised by the shape of the text, never by the file name (parse(), detect()), so other files
 * written the same way are shown the same way. Records are grouped by the section label they are under, else by
 * their major series ("3.x", "4.x"; the version comes from the release name when there is one, so 2.9-3 "AKA
 * 3.0-beta2" is a 3.x record); a series of more than 15 records is split into its minor versions (3.0, 3.1 ...),
 * each a collapsible part with a heading in the page's contents. Every record is one table row: the version, its
 * release name, the date (ISO 8601, in a <time> element), the revisions (single, start, end, merged ranges, release
 * branch) and the addresses with notes. Addresses on hosts that are gone (DEAD_HOSTS: the old SVN servers) are
 * shown as text marked "historical (no longer online)", never as links; other addresses are links only with https.
 *
 * Safe: every value is escaped; the only markup is the renderer's own, and no form control (exphelp.js adds the
 * filter field and the sort buttons when it runs; without it the page is complete). Markup (styled by help.css):
 * div.a4-help-body.a4-help-records[data-exphelp-records], p.a4-help-records-intro, p.a4-help-records-stats,
 * h2.a4-help-h.a4-help-records-group[id] (span.a4-tag with the count), then per table either
 * p.a4-help-records-range or (a part) h3.a4-help-h[id] + details.a4-help-details.a4-help-records-details[open] >
 * summary, and div.a4-help-table-wrap > table.a4-table.a4-help-table.a4-help-records-table[data-exphelp-sortable]
 * (th[data-sort="none"] for a column that is not sorted, cells with data-sort-value); code.a4-help-records-url and
 * span.a4-tag.a4-help-records-dead for historical addresses.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpRecordRenderer
{
    /**
     * Hosts that no longer answer (the old SVN servers; confluence.ez.no redirects to a dead port): their addresses are
     * shown as historical text, never as links
     */
    const DEAD_HOSTS = array( 'zev.ez.no', 'svn.ez.no', 'pubsvn.ez.no', 'svn.ez.no:80', 'confluence.ez.no' );

    /** Fewest records for a text to count as a record list */
    const MIN_RECORDS = 3;

    /** @var array month names and abbreviations (English, also as the old files wrote them) => number */
    protected static $months = array(
        'jan' => 1, 'january' => 1, 'feb' => 2, 'february' => 2, 'mar' => 3, 'march' => 3, 'apr' => 4, 'april' => 4,
        'may' => 5, 'jun' => 6, 'june' => 6, 'jul' => 7, 'july' => 7, 'aug' => 8, 'august' => 8, 'sep' => 9,
        'sept' => 9, 'september' => 9, 'oct' => 10, 'october' => 10, 'nov' => 11, 'november' => 11, 'dec' => 12,
        'december' => 12, 'mai' => 5, 'okt' => 10, 'des' => 12, 'dez' => 12,
    );

    /** @var callable|null */
    protected $translate;

    /** @var array hosts treated as gone */
    protected $deadHosts;

    /** @var callable|null the address of a relative link (a page of the documentation), or null */
    protected $link;

    /**
     * @param array $options 'translate' => callable( string ): string, 'dead_hosts' => list of host names,
     *                       'link' => callable( string $relativePath ): ?string, the address of a page of the documentation
     *                       a record names by a relative path (the Markdown renderer's link policy); without it such a
     *                       path is shown as text
     */
    public function __construct( array $options = array() )
    {
        $this->translate = isset( $options['translate'] ) && is_callable( $options['translate'] ) ? $options['translate'] : null;
        $this->deadHosts = isset( $options['dead_hosts'] ) && is_array( $options['dead_hosts'] )
            ? array_map( 'strtolower', $options['dead_hosts'] ) : self::DEAD_HOSTS;
        $this->link = isset( $options['link'] ) && is_callable( $options['link'] ) ? $options['link'] : null;
    }

    /**
     * Parse a text into records, or null when it is not a record list.
     * Lines that start with "#" are comments: they say where the records come from and are shown as such.
     *
     * @param string $text
     * @return array|null array( 'intro' => list of lines, 'records' => list of record arrays (see record()),
     *                    'sections' => list of section labels in order, 'comments' => list of comment lines )
     */
    public static function parse( $text )
    {
        $text = str_replace( array( "\r\n", "\r" ), "\n", (string)$text );
        if ( strlen( $text ) > 2000000 || !preg_match( '//u', $text ) )
            return null;
        $lines = explode( "\n", $text );
        $count = count( $lines );
        $intro = array();
        $comments = array();
        $records = array();
        $sections = array();
        $section = '';
        $structured = 0;
        $fieldLines = 0;
        $keyed = 0;
        $other = 0;
        for ( $i = 0; $i < $count; $i++ )
        {
            $line = rtrim( $lines[$i] );
            if ( trim( $line ) === '' )
                continue;
            if ( $line[0] === '#' )
            {
                $comments[] = trim( ltrim( $line, '#' ) );
                continue;
            }
            if ( preg_match( '/^-\s/', $line ) )
            {
                // a field line outside a record
                $other++;
                continue;
            }
            // a name line: the field lines that follow it make it a record
            $fields = array();
            $j = $i + 1;
            while ( $j < $count && preg_match( '/^-\s*(.*)$/', rtrim( $lines[$j] ), $m ) )
            {
                $fields[] = trim( $m[1] );
                $j++;
            }
            $name = trim( $line );
            if ( $fields )
            {
                $name = rtrim( $name, ':' );
                if ( $name === '' || mb_strlen( $name, 'UTF-8' ) > 100 )
                {
                    $other += 1 + count( $fields );
                    $i = $j - 1;
                    continue;
                }
                $record = self::record( $name, $fields, $section );
                $records[] = $record;
                $structured += 1 + count( $fields );
                $fieldLines += count( $fields );
                $keyed += $record['keyed'];
                $i = $j - 1;
                continue;
            }
            // a line without fields: a section label when it ends in a colon and records follow, else text
            if ( substr( $name, -1 ) === ':' && $records && mb_strlen( $name, 'UTF-8' ) <= 100 )
            {
                $section = rtrim( $name, ': ' );
                $sections[] = $section;
                $structured++;
                continue;
            }
            if ( !$records )
            {
                $intro[] = $name;
                continue;
            }
            $other++;
        }
        $nonBlank = $structured + $other + count( $intro );
        if ( count( $records ) < self::MIN_RECORDS || $nonBlank === 0 || $fieldLines === 0
             || $structured / $nonBlank < 0.85 || $keyed / $fieldLines < 0.6 || count( $intro ) > 10 )
            return null;
        return array( 'intro' => $intro, 'records' => $records, 'sections' => $sections, 'comments' => $comments );
    }

    /**
     * Whether a text is a record list.
     *
     * @param string $text
     * @return bool
     */
    public static function detect( $text )
    {
        return self::parse( $text ) !== null;
    }

    /**
     * One record from its name and field lines.
     *
     * @return array( 'name', 'section', 'aka', 'date' (ISO or ''), 'date_text', 'release_date', 'release_date_text',
     *                'revisions' => list of array( label, value ), 'urls' => list of array( label, url, revision ),
     *                'notes' => list of text, 'sources' => list of text ("- Source=..."), 'series' ("- Series=5.x"
     *                or from the version), 'minor' ("- Part=..." or from the version), 'keyed' => number of
     *                fields that were understood )
     */
    protected static function record( $name, array $fields, $section )
    {
        $record = array( 'name' => $name, 'section' => $section, 'aka' => '', 'date' => '', 'date_text' => '',
                         'release_date' => '', 'release_date_text' => '', 'revisions' => array(), 'urls' => array(),
                         'notes' => array(), 'sources' => array(), 'series' => '', 'minor' => '', 'keyed' => 0 );
        $series = $part = '';
        foreach ( $fields as $field )
        {
            $key = null;
            $value = '';
            if ( preg_match( '/^([A-Za-z][A-Za-z ]{0,40}?)\s*=\s*(.*)$/', $field, $m ) )
            {
                $key = strtolower( trim( $m[1] ) );
                $value = trim( $m[2] );
            }
            elseif ( preg_match( '/^((?:release branch |release )?date|aka|(?:start |end |final )?revisions?)\b[\s:]+(.+)$/i', $field, $m ) )
            {
                $key = strtolower( trim( $m[1] ) );
                $value = trim( $m[2] );
            }
            if ( $key === null )
            {
                $record['notes'][] = $field;
                continue;
            }
            $record['keyed']++;
            if ( $key === 'aka' )
                $record['aka'] = $value;
            elseif ( $key === 'date' )
            {
                $record['date_text'] = $value;
                $record['date'] = self::isoDate( $value );
            }
            elseif ( $key === 'release date' )
            {
                $record['release_date_text'] = $value;
                $record['release_date'] = self::isoDate( $value );
            }
            elseif ( $key === 'release branch date' )
                $record['revisions'][] = array( 'Release branch date', self::isoDate( $value ) ?: $value );
            elseif ( $key === 'url' || substr( $key, -4 ) === ' url' )
            {
                $revision = '';
                if ( preg_match( '/^(\S+)\s+\((\d+)\)\s*$/', $value, $u ) )
                {
                    $value = $u[1];
                    $revision = $u[2];
                }
                $label = $key === 'url' ? 'Address' : ucfirst( substr( $key, 0, -4 ) );
                $record['urls'][] = array( $label, $value, $revision );
            }
            elseif ( $key === 'series' && preg_match( '/^\d{1,2}\.x$/', $value ) )
                $series = $value; // the group, when the name holds no version of its own (2014.11 belongs to 5.x)
            elseif ( $key === 'part' && $value !== '' )
                $part = $value; // the part of a long series the record is listed in
            elseif ( $key === 'source' && $value !== '' )
                $record['sources'][] = $value;
            elseif ( $key === 'release branch configured' && $value === '' )
                continue; // an empty marker: understood, nothing to show
            else
                $record['revisions'][] = array( ucfirst( $key ), $value === '' || $value === '?' ? '' : $value );
        }
        $record['series'] = $series !== '' ? $series : self::series( $record['aka'], $name );
        $record['minor'] = $part !== '' ? $part : self::minor( $record['aka'], $name );
        $record['part'] = $part !== '';
        return $record;
    }

    /**
     * The major series of a record: "3.x" from "3.0 final", "3.0-beta2", "stable/3.5", "4.2.0rc1"; '' when there is
     * no version in it.
     */
    protected static function series( $aka, $name )
    {
        foreach ( array( $aka, $name ) as $text )
            if ( preg_match( '/(?:^|[^\d.])(\d{1,2})\.\d+/', (string)$text, $m ) )
                return (int)$m[1] . '.x';
        return '';
    }

    /**
     * The minor version of a record: "3.0" from "3.0 final" or "3.0-1", "4.2" from "4.2.0rc1"; '' without one.
     */
    protected static function minor( $aka, $name )
    {
        foreach ( array( $aka, $name ) as $text )
            if ( preg_match( '/(?:^|[^\d.])(\d{1,2})\.(\d+)/', (string)$text, $m ) )
                return (int)$m[1] . '.' . (int)$m[2];
        return '';
    }

    /**
     * An ISO 8601 date (2002-11-01) from the dates the old files wrote: 01-Nov-2002, 5-Feb-2003, 02-June-2004,
     * "20. feb. 2006", 2006-02-20, 20.02.2006. '' when it cannot be read or is not a real day.
     *
     * @param string $text
     * @return string
     */
    public static function isoDate( $text )
    {
        $text = strtolower( trim( (string)$text ) );
        $day = $month = $year = 0;
        if ( preg_match( '/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $text, $m ) )
            list( , $year, $month, $day ) = $m;
        elseif ( preg_match( '/^(\d{1,2})[-. \/]+([a-z]+)\.?[-. \/]+(\d{4})$/', $text, $m ) && isset( self::$months[$m[2]] ) )
        {
            $day = $m[1];
            $month = self::$months[$m[2]];
            $year = $m[3];
        }
        elseif ( preg_match( '/^([a-z]+)\.?\s+(\d{1,2}),?\s+(\d{4})$/', $text, $m ) && isset( self::$months[$m[1]] ) )
        {
            $month = self::$months[$m[1]];
            $day = $m[2];
            $year = $m[3];
        }
        elseif ( preg_match( '/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $text, $m ) )
            list( , $day, $month, $year ) = $m;
        else
            return '';
        if ( !checkdate( (int)$month, (int)$day, (int)$year ) )
            return '';
        return sprintf( '%04d-%02d-%02d', $year, $month, $day );
    }

    /**
     * Title and summary of a record page, for the document index; null when the text is not one.
     *
     * @param string $text
     * @return array|null array( 'title' => string, 'summary' => string, 'version' => the major version most records belong to, or '' )
     */
    public function describe( $text )
    {
        $parsed = self::parse( $text );
        return $parsed === null ? null : $this->describeParsed( $parsed );
    }

    protected function describeParsed( array $parsed )
    {
        $records = $parsed['records'];
        $branches = 0;
        $svnRecords = 0;
        $dead = false;
        $series = array();
        $dates = array();
        foreach ( $records as $record )
        {
            if ( strpos( $record['name'], '/' ) !== false || stripos( $record['name'], 'branch' ) !== false )
                $branches++;
            $svn = false;
            foreach ( $record['revisions'] as $revision )
                if ( stripos( $revision[0], 'revision' ) !== false )
                    $svn = true;
            foreach ( $record['urls'] as $url )
            {
                if ( stripos( $url[1], '/svn' ) !== false )
                    $svn = true;
                if ( $this->isDead( $url[1] ) )
                    $dead = true;
            }
            if ( $svn )
                $svnRecords++;
            if ( $record['series'] !== '' )
                $series[(int)$record['series']] = isset( $series[(int)$record['series']] ) ? $series[(int)$record['series']] + 1 : 1;
            foreach ( array( 'date', 'release_date' ) as $key )
                if ( $record[$key] !== '' )
                    $dates[] = $record[$key];
        }
        ksort( $series );
        $majors = array_keys( $series );
        $range = !$majors ? '' : ( count( $majors ) === 1 ? $majors[0] . '.x' : reset( $majors ) . ".x\u{2013}" . end( $majors ) . '.x' );
        $isBranches = $branches * 2 > count( $records );
        $kind = $this->t( $isBranches ? 'Branch history' : 'Release history' );
        $extra = array();
        if ( $range !== '' )
            $extra[] = $range;
        // "from the original SVN" only when every record comes from it
        if ( $svnRecords > 0 && $svnRecords === count( $records ) )
            $extra[] = $this->t( 'from the original SVN' );
        $title = $kind . ( $extra ? ' (' . implode( ', ', $extra ) . ')' : '' );
        sort( $dates );
        $summary = str_replace(
            array( '%count', '%first', '%last' ),
            array( (string)count( $records ), $dates ? reset( $dates ) : '?', $dates ? end( $dates ) : '?' ),
            $this->t( $isBranches
                ? '%count branches from %first to %last with the revisions they started and ended at.'
                : '%count releases from %first to %last with their release names, dates and revisions.' ) );
        if ( $svnRecords > 0 && $svnRecords < count( $records ) )
            $summary .= ' ' . $this->t( 'The older records come from the original SVN repository.' );
        if ( $dead )
            $summary .= ' ' . $this->t( 'Addresses on servers that are no longer online are kept as historical text.' );
        // the major version most records belong to: search ranks the page like the pages of that version (a history
        // of mostly 3.x releases ranks like the 3.x pages, VersionBoost)
        $most = $series ? array_search( max( $series ), $series, true ) : null;
        return array( 'title' => $title, 'summary' => $summary, 'version' => $most === null ? '' : (string)$most );
    }

    /**
     * Render a record page, or null when the text is not one.
     *
     * @param string $text
     * @param callable|null $slug function( string $text ): string, the id of a heading (unique on the page)
     * @return array|null array( 'html', 'toc' => list of array( level, text, anchor, id ), 'title' )
     */
    public function render( $text, $slug = null )
    {
        $parsed = self::parse( $text );
        if ( $parsed === null )
            return null;
        $used = array();
        if ( !is_callable( $slug ) )
            $slug = function( $text ) use ( &$used ) { return expHelpMarkdownRenderer::slug( $text, $used ); };
        $meta = $this->describeParsed( $parsed );
        $other = $this->t( 'Other' );

        // the groups: a section label, else the major series; a record without a version of its own ("Security fix
        // 1", "Datatype branch") stays in the series of the record before it
        $groups = array();
        $previous = array( '', '' );
        foreach ( $parsed['records'] as $index => $record )
        {
            if ( $record['section'] === '' )
            {
                if ( $record['series'] === '' )
                    list( $record['series'], $record['minor'] ) = $previous;
                else
                    $previous = array( $record['series'], $record['minor'] );
            }
            $key = $record['section'] !== '' ? $record['section'] : ( $record['series'] !== '' ? $record['series'] : $other );
            $groups[$key][] = $record + array( 'index' => $index );
        }
        // series groups in version order, then the section groups in file order, then the rest
        $order = array_keys( $groups );
        usort( $order, function( $a, $b ) use ( $parsed, $other )
        {
            $rank = function( $g ) use ( $parsed, $other )
            {
                return $g === $other ? 2 : ( in_array( $g, $parsed['sections'], true ) ? 1 : 0 );
            };
            if ( $rank( $a ) !== $rank( $b ) )
                return $rank( $a ) <=> $rank( $b );
            if ( $rank( $a ) === 1 )
                return array_search( $a, $parsed['sections'], true ) <=> array_search( $b, $parsed['sections'], true );
            return strnatcmp( $a, $b );
        } );

        $toc = array();
        $titleId = $slug( $meta['title'] );
        $html = '<div class="a4-help-body a4-help-records" data-exphelp-records>';
        $html .= '<h1 id="' . self::escape( $titleId ) . '" class="a4-help-h">' . self::escape( $meta['title'] ) . '</h1>';
        if ( $parsed['intro'] )
            $html .= '<p class="a4-help-records-intro">' . self::escape( implode( ' ', $parsed['intro'] ) ) . '</p>';
        $html .= '<p class="a4-help-records-stats">' . self::escape( $meta['summary'] ) . '</p>';
        if ( $parsed['comments'] )
            $html .= '<p class="a4-help-records-source">' . self::escape( implode( ' ', array_filter( $parsed['comments'] ) ) ) . '</p>';

        foreach ( $order as $group )
        {
            $rows = $groups[$group];
            $isSeries = preg_match( '/^\d+\.x$/', $group ) === 1;
            $label = $isSeries ? sprintf( $this->t( 'Series %s' ), $group ) : $group;
            $id = $slug( $label );
            $toc[] = array( 'level' => 2, 'text' => $label, 'anchor' => $id, 'id' => $id );
            $eid = self::escape( $id );
            // flat under the body (no section element), so the viewer's collapsible h2s (exphelp.js) find every group
            $html .= '<h2 id="' . $eid . '" class="a4-help-h a4-help-records-group">' . self::escape( $label ) . ' <span class="a4-tag">' . count( $rows ) . '</span>'
                . ' <a class="a4-help-anchor" href="#' . $eid . '" aria-hidden="true" tabindex="-1">#</a></h2>';
            // a long series is split into its minor versions (3.0, 3.1 ...) or the parts its records name ("- Part=..."),
            // each with a heading of its own: minor versions in version order, named parts in the order of the file
            $parts = array();
            $named = false;
            if ( $isSeries && count( $rows ) > 15 )
            {
                foreach ( $rows as $row )
                {
                    $parts[$row['minor'] !== '' ? $row['minor'] : $group][] = $row;
                    $named = $named || $row['part'];
                }
                if ( !$named )
                    uksort( $parts, 'strnatcmp' );
            }
            if ( count( $parts ) < 2 )
                $parts = array( '' => $rows );
            foreach ( $parts as $minor => $partRows )
            {
                $partLabel = $label;
                if ( $minor !== '' )
                {
                    $partLabel = $minor;
                    $partId = $slug( $minor );
                    $toc[] = array( 'level' => 3, 'text' => $minor, 'anchor' => $partId, 'id' => $partId );
                    $html .= '<h3 id="' . self::escape( $partId ) . '" class="a4-help-h">' . self::escape( $minor )
                        . ' <span class="a4-tag">' . count( $partRows ) . '</span>'
                        . ' <a class="a4-help-anchor" href="#' . self::escape( $partId ) . '" aria-hidden="true" tabindex="-1">#</a></h3>';
                }
                $html .= $this->table( (string)$partLabel, $partRows, $slug, $minor !== '' );
            }
        }
        $html .= '</div>';
        return array( 'html' => $html, 'toc' => $toc, 'title' => $meta['title'] );
    }

    /**
     * One table of records, oldest first; a part of a long series is collapsible (the series itself collapses with
     * its heading, exphelp.js).
     */
    protected function table( $label, array $rows, $slug, $collapsible = false )
    {
        usort( $rows, function( $a, $b )
        {
            $da = $a['date'] !== '' ? $a['date'] : $a['release_date'];
            $db = $b['date'] !== '' ? $b['date'] : $b['release_date'];
            if ( $da !== '' && $db !== '' && $da !== $db )
                return strcmp( $da, $db );
            return $a['index'] <=> $b['index'];
        } );
        $dates = array();
        foreach ( $rows as $row )
            if ( ( $d = $row['date'] !== '' ? $row['date'] : $row['release_date'] ) !== '' )
                $dates[] = $d;
        sort( $dates );
        $summary = count( $rows ) === 1
            ? str_replace( '%first', $dates ? $dates[0] : '?', $this->t( '1 entry, %first' ) )
            : str_replace( array( '%count', '%first', '%last' ), array( (string)count( $rows ), $dates ? reset( $dates ) : '?', $dates ? end( $dates ) : '?' ),
                           $this->t( '%count entries, %first to %last' ) );
        $html = ( $collapsible ? '<details class="a4-help-details a4-help-records-details" open><summary>' . self::escape( $summary ) . '</summary>'
                               : '<p class="a4-help-records-range">' . self::escape( $summary ) . '</p>' )
            . '<div class="a4-help-table-wrap" role="region" tabindex="0" aria-label="' . self::escape( $label ) . '">'
            . '<table class="a4-table a4-help-table a4-help-records-table" data-exphelp-sortable>'
            . '<caption class="a4-sr-only">' . self::escape( $label ) . '</caption><thead><tr>'
            . '<th scope="col">' . self::escape( $this->t( 'Version' ) ) . '</th>'
            . '<th scope="col">' . self::escape( $this->t( 'Name' ) ) . '</th>'
            . '<th scope="col">' . self::escape( $this->t( 'Date' ) ) . '</th>'
            . '<th scope="col">' . self::escape( $this->t( 'Revisions' ) ) . '</th>'
            . '<th scope="col" data-sort="none">' . self::escape( $this->t( 'Addresses and notes' ) ) . '</th>'
            . '</tr></thead><tbody>';
        foreach ( $rows as $row )
            $html .= $this->row( $row, $slug );
        return $html . '</tbody></table></div>' . ( $collapsible ? '</details>' : '' );
    }

    /**
     * One table row.
     */
    protected function row( array $record, $slug )
    {
        $id = $slug( 'v-' . $record['name'] );
        $html = '<tr id="' . self::escape( $id ) . '">';
        $html .= '<th scope="row" data-sort-value="' . self::escape( self::versionKey( $record['name'] ) ) . '">'
            . self::escape( $record['name'] ) . '</th>';
        $html .= '<td>' . self::escape( $record['aka'] ) . '</td>';

        $date = $record['date'] !== '' ? $record['date'] : $record['release_date'];
        $html .= '<td data-sort-value="' . self::escape( $date ) . '">' . $this->dateHtml( $record['date'], $record['date_text'] );
        if ( $record['release_date_text'] !== '' && $record['release_date'] !== $record['date'] )
            $html .= '<span class="a4-help-records-sub">' . self::escape( $this->t( 'Released' ) ) . ' '
                . $this->dateHtml( $record['release_date'], $record['release_date_text'] ) . '</span>';
        $html .= '</td>';

        $first = '';
        $items = array();
        foreach ( $record['revisions'] as $revision )
        {
            list( $label, $value ) = $revision;
            // the revision column sorts by the first revision number, not by a tag or a date
            if ( $first === '' && !preg_match( '/date|tag/i', $label ) && preg_match( '/^\d+/', $value, $m ) )
                $first = $m[0];
            $items[] = '<li><span class="a4-help-records-key">' . self::escape( $this->t( $label ) ) . '</span> '
                . ( $value === '' ? '<span class="a4-help-records-none">' . self::escape( $this->t( 'not recorded' ) ) . '</span>'
                                  : '<code>' . self::escape( $value ) . '</code>' ) . '</li>';
        }
        $html .= '<td data-sort-value="' . self::escape( $first === '' ? '' : sprintf( '%09d', (int)$first ) ) . '">'
            . ( $items ? '<ul class="a4-help-records-list">' . implode( '', $items ) . '</ul>' : '' ) . '</td>';

        $items = array();
        foreach ( $record['urls'] as $url )
            $items[] = '<li><span class="a4-help-records-key">' . self::escape( $this->t( $url[0] ) ) . '</span> ' . $this->urlHtml( $url[1] )
                . ( $url[2] !== '' ? ' <span class="a4-help-records-sub">' . self::escape( sprintf( $this->t( 'revision %s' ), $url[2] ) ) . '</span>' : '' ) . '</li>';
        foreach ( $record['notes'] as $note )
            $items[] = '<li class="a4-help-records-note">' . self::escape( $note ) . '</li>';
        foreach ( $record['sources'] as $source )
            $items[] = '<li class="a4-help-records-source"><span class="a4-help-records-key">' . self::escape( $this->t( 'Source' ) ) . '</span> '
                . self::escape( $source ) . '</li>';
        $html .= '<td>' . ( $items ? '<ul class="a4-help-records-list">' . implode( '', $items ) . '</ul>' : '' ) . '</td>';
        return $html . '</tr>';
    }

    protected function dateHtml( $iso, $text )
    {
        if ( $iso !== '' )
            return '<time datetime="' . self::escape( $iso ) . '">' . self::escape( $iso ) . '</time>';
        return $text === '' ? '' : '<span class="a4-help-records-none">' . self::escape( $text ) . '</span>';
    }

    /**
     * An address: a link only for https on a host that still exists, else text; the gone hosts marked historical. A
     * relative path (a page of the documentation: changelogs/6.0/6.0.14.md) is a link when the link option gives its
     * address.
     */
    protected function urlHtml( $url )
    {
        if ( !preg_match( '/^[A-Za-z][A-Za-z0-9+.-]*:|^\/\//', $url ) )
        {
            $href = $this->link !== null ? call_user_func( $this->link, $url ) : null;
            if ( is_string( $href ) && $href !== '' && !preg_match( '/^\s*(?:javascript|data|vbscript|file):/i', $href ) )
                return '<a href="' . self::escape( $href ) . '">' . self::escape( $url ) . '</a>';
            return '<code class="a4-help-records-url">' . self::escape( $url ) . '</code>';
        }
        $dead = $this->isDead( $url );
        if ( !$dead && preg_match( '#^https://[A-Za-z0-9.-]+(?::\d+)?(?:/[^\s<>"]*)?$#', $url ) )
            return '<a class="a4-help-ext" rel="noopener noreferrer" href="' . self::escape( $url ) . '">' . self::escape( $url ) . '</a>';
        $html = '<code class="a4-help-records-url">' . self::escape( $url ) . '</code>';
        if ( $dead )
            $html .= ' <span class="a4-tag a4-help-records-dead">' . self::escape( $this->t( 'historical (no longer online)' ) ) . '</span>';
        return $html;
    }

    /**
     * Whether an address is on a host that is gone.
     */
    protected function isDead( $url )
    {
        $host = strtolower( (string)parse_url( $url, PHP_URL_HOST ) );
        $port = parse_url( $url, PHP_URL_PORT );
        return $host !== '' && ( in_array( $host, $this->deadHosts, true ) || ( $port && in_array( $host . ':' . $port, $this->deadHosts, true ) ) );
    }

    /**
     * A sort key that orders versions naturally: 3.10 after 3.9, 2.9-3 before 3.0-1, v2014.11 with 2014.9.
     */
    protected static function versionKey( $name )
    {
        return preg_replace_callback( '/\d+/', function( $m ) { return sprintf( '%06d', (int)$m[0] ); },
                                      preg_replace( '/^v(?=\d)/', '', strtolower( $name ) ) );
    }

    protected function t( $text )
    {
        if ( $this->translate !== null )
            return (string)call_user_func( $this->translate, $text );
        if ( class_exists( 'ezpI18n', false ) )
        {
            try
            {
                return ezpI18n::tr( 'kernel/exphelp', $text );
            }
            catch ( Throwable $e )
            {
            }
        }
        return $text;
    }

    protected static function escape( $text )
    {
        return htmlspecialchars( (string)$text, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8' );
    }
}
