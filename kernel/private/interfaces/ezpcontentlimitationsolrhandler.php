<?php
/**
 * File containing the ezpContentLimitationSolrHandler interface
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * A content limitation handler that can also restrict a search in a search engine that filters by the policies of
 * the user itself, such as eZ Find with Solr.
 *
 * A handler registered in site.ini [RoleSettings] LimitationHandlers[] (ezpContentLimitationHandler) implements this
 * interface as well when its limitation can be expressed as a search filter. The search engine asks
 * ezpContentLimitation::solrFilter(); a limitation whose handler does not implement this interface gives no access
 * in searches, as one without a handler gives none in fetches.
 *
 * @see ezpContentLimitation::solrFilter()
 * @package kernel
 */
interface ezpContentLimitationSolrHandler extends ezpContentLimitationHandler
{
    /**
     * Returns the filter that keeps the documents of a content/read search the limitation allows.
     *
     * The filter is joined with AND to the other limitations of the same policy. The field names are those of the
     * search engine's index (eZ Find: eZSolr::getMetaFieldName()). Two forms are accepted:
     *
     * - A string, put in parentheses by the kernel. It must be self-contained: quotes and parentheses balanced and
     *   no local parameters ("{!"); otherwise the policy gives no access in searches. Values from the policy must be
     *   escaped by the handler (ezpContentLimitation::solrValue()).
     * - array( 'field' => 'meta_section_id_si', 'values' => $values, 'not' => false ), or a list of such arrays
     *   joined by AND: the kernel writes the condition and escapes the values itself. The safer form wherever it is
     *   enough.
     *
     * A handler that cannot express the limitation as a filter returns false, and the policy then gives no access in
     * searches. So does an exception, or any other answer (see ezpContentLimitation::solrCondition()).
     *
     * @param string $limitation The limitation name, as the policy stores it
     * @param array $values The values of the limitation in the policy, as strings
     * @param int $userID The user the search is for
     * @return string|array|false
     */
    public function solrFilter( $limitation, array $values, $userID );
}
