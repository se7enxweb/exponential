<?php
/**
 * File containing the expHelpContextPageInterface interface.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A context help provider (expHelpContextProviderInterface) that also serves pages which are not files below doc/:
 * help topics maintained as content, with the id "content/<help_id>" (expHelpContextRegistry::CONTENT_PAGE_PATTERN).
 * The registry accepts such an answer only from a provider implementing this interface, and only when it says the
 * page exists for the current user.
 */
interface expHelpContextPageInterface
{
    /**
     * The address and title of a page this provider serves
     *
     * @param string $id "content/<help_id>"
     * @return array|null array( 'url' => module-relative address below /exphelp/, 'title' => string ), or null when
     *                    there is no such page (or the current user may not see it)
     */
    public function contextPage( $id );
}

?>
