<?php
/**
 * File containing the expUrlMapPlan class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What an import of a URL map would do, row by row (made by expUrlMapPlanner, applied by expUrlMapImporter): the
 * preview of the console's --dry-run and of the administration's import screen.
 *
 * Each item is an array:
 *   index, line, old_url, new_url, kind, note   the row (index from 0, line in the file)
 *   state        new, unchanged, changed, duplicate, conflict, broken-target, unknown-host, not-importable
 *   messages     why (problems, conflicts, notes on what the import changes)
 *   store        where it goes: alias (a global URL alias, ezurlalias_ml), wildcard (ezurlwildcard), redirect
 *                (exp_url_redirect); '' when it is not imported
 *   key          the store's key of the old address (duplicates have the same one)
 *   siteaccess, language, status (the redirect status, 0 for internal kinds)
 *   source       path, query (the old address inside its siteaccess)
 *   write        what is written: alias: path, action, link, redirects; wildcard: source_url, destination_url,
 *                type; redirect: path, query, target, status
 *   existing     the entry there now (null for none)
 *   decision     import or skip (new: import; changed: import with the conflict policy overwrite; others: skip)
 *   action       create, update or none (what the decision means)
 *
 * setDecision() changes one row's decision (the administration's per-row skip or overwrite); toArray() is the report
 * (schema exp-url-map-plan/1), writeReport() writes it as JSON or CSV.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlMapPlan
{
    const SCHEMA = 'exp-url-map-plan/1';

    const STATE_NEW = 'new';
    const STATE_UNCHANGED = 'unchanged';
    const STATE_CHANGED = 'changed';
    const STATE_DUPLICATE = 'duplicate';
    const STATE_CONFLICT = 'conflict';
    const STATE_BROKEN_TARGET = 'broken-target';
    const STATE_UNKNOWN_HOST = 'unknown-host';
    const STATE_NOT_IMPORTABLE = 'not-importable';

    const STORE_ALIAS = 'alias';
    const STORE_WILDCARD = 'wildcard';
    const STORE_REDIRECT = 'redirect';

    const ON_CONFLICT_SKIP = 'skip';
    const ON_CONFLICT_OVERWRITE = 'overwrite';

    /** @var array[] */
    protected $items = array();

    /** @var array */
    protected $options;

    /**
     * @param array[] $items
     * @param array $options what the planner was given (siteaccess, language, on_conflict, source_name, format)
     */
    public function __construct( array $items, array $options )
    {
        $this->items = array_values( $items );
        $this->options = $options;
        foreach ( array_keys( $this->items ) as $index )
            $this->decide( $index );
    }

    /** @return string[] every state, in the order of the preview */
    public static function states()
    {
        return array( self::STATE_NEW, self::STATE_CHANGED, self::STATE_UNCHANGED, self::STATE_DUPLICATE, self::STATE_CONFLICT,
                      self::STATE_BROKEN_TARGET, self::STATE_UNKNOWN_HOST, self::STATE_NOT_IMPORTABLE );
    }

    /** @return array[] */
    public function items()
    {
        return $this->items;
    }

    /**
     * @param int $index
     * @return array|null
     */
    public function item( $index )
    {
        return isset( $this->items[$index] ) ? $this->items[$index] : null;
    }

    /** @return array */
    public function options()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function option( $name, $default = null )
    {
        return array_key_exists( $name, $this->options ) ? $this->options[$name] : $default;
    }

    /**
     * The items of a state.
     *
     * @param string $state
     * @return array[]
     */
    public function itemsIn( $state )
    {
        return array_values( array_filter( $this->items, function ( $item ) use ( $state ) { return $item['state'] === $state; } ) );
    }

    /**
     * Changes a row's decision: import or skip. Only new and changed rows can be imported.
     *
     * @param int $index
     * @param string $decision import or skip
     * @return bool whether it was taken
     */
    public function setDecision( $index, $decision )
    {
        if ( !isset( $this->items[$index] ) || !in_array( $decision, array( 'import', 'skip' ), true ) )
            return false;
        if ( $decision === 'import' && !in_array( $this->items[$index]['state'], array( self::STATE_NEW, self::STATE_CHANGED ), true ) )
            return false;
        $this->items[$index]['decision'] = $decision;
        $this->items[$index]['action'] = $decision === 'skip' ? 'none' : ( $this->items[$index]['state'] === self::STATE_NEW ? 'create' : 'update' );
        return true;
    }

    /** @return array[] the items the import writes */
    public function actionable()
    {
        return array_values( array_filter( $this->items, function ( $item ) { return $item['action'] !== 'none'; } ) );
    }

    /**
     * The counts: rows, each state, create, update, skip.
     *
     * @return int[]
     */
    public function counts()
    {
        $counts = array( 'rows' => count( $this->items ) );
        foreach ( self::states() as $state )
            $counts[$state] = 0;
        $counts += array( 'create' => 0, 'update' => 0, 'skip' => 0 );
        foreach ( $this->items as $item )
        {
            $counts[$item['state']]++;
            $counts[$item['action'] === 'none' ? 'skip' : $item['action']]++;
        }
        return $counts;
    }

    /** @return array the report (exp-url-map-plan/1) */
    public function toArray()
    {
        return array( 'schema' => self::SCHEMA, 'options' => $this->options, 'counts' => $this->counts(), 'items' => $this->items );
    }

    /** @return string[] the columns of the CSV report */
    public static function reportColumns()
    {
        return array( 'line', 'old_url', 'new_url', 'kind', 'status', 'state', 'action', 'store', 'siteaccess', 'language', 'message' );
    }

    /**
     * The report as a table of strings (the CSV report, the spreadsheets).
     *
     * @return array[]
     */
    public function reportTable()
    {
        $table = array( self::reportColumns() );
        foreach ( $this->items as $item )
            $table[] = array( (string)$item['line'], $item['old_url'], $item['new_url'], $item['kind'], $item['status'] ? (string)$item['status'] : '',
                              $item['state'], $item['action'], $item['store'], $item['siteaccess'], $item['language'], implode( '; ', $item['messages'] ) );
        return $table;
    }

    /**
     * Writes the report: JSON (exp-url-map-plan/1) or CSV, by the format or the file's extension.
     *
     * @param string $path
     * @param string|null $format json or csv
     * @throws expUrlMapFormatException
     */
    public function writeReport( $path, $format = null )
    {
        if ( $format === null || $format === '' )
            $format = strtolower( substr( (string)$path, -4 ) ) === '.csv' ? 'csv' : 'json';
        if ( $format === 'csv' )
        {
            $out = "\xEF\xBB\xBF";
            foreach ( $this->reportTable() as $cells )
                $out .= implode( ',', array_map( function ( $cell ) { return expUrlMapCsvWriter::field( (string)$cell, ',' ); }, $cells ) ) . "\r\n";
        }
        else
            $out = json_encode( $this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ) . "\n";
        if ( @file_put_contents( $path, $out ) === false )
            throw new expUrlMapFormatException( "The report $path cannot be written" );
    }

    /**
     * The default decision of a row from its state and the conflict policy.
     *
     * @param int $index
     */
    protected function decide( $index )
    {
        $item =& $this->items[$index];
        $overwrite = $this->option( 'on_conflict', self::ON_CONFLICT_SKIP ) === self::ON_CONFLICT_OVERWRITE;
        if ( $item['state'] === self::STATE_NEW )
        {
            $item['decision'] = 'import';
            $item['action'] = 'create';
        }
        elseif ( $item['state'] === self::STATE_CHANGED && $overwrite )
        {
            $item['decision'] = 'import';
            $item['action'] = 'update';
        }
        else
        {
            $item['decision'] = 'skip';
            $item['action'] = 'none';
        }
    }
}
