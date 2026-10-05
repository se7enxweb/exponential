<?php
/**
 * File containing the expMailCategoryHandler interface.
 *
 * The optional PHP class of a category ([Category_<id>] HandlerClass): it reads data that existed before the
 * preference system (subtree notifications, digest settings, newsletter subscriptions) as the state of the
 * category, so nothing has to be migrated, and hears about changes so its own data can follow.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

interface expMailCategoryHandler
{
    /**
     * The state of the category for a person without a stored preference.
     *
     * @param expMailRecipient $recipient
     * @param expMailCategory $category
     * @return bool|null true on, false off, null: no opinion (the category default applies)
     */
    public function stateFor( expMailRecipient $recipient, expMailCategory $category );

    /**
     * The frequency for a person without a stored frequency.
     *
     * @return string|null immediate|daily|weekly, or null
     */
    public function frequencyFor( expMailRecipient $recipient, expMailCategory $category );

    /**
     * Called after the person's preference of the category changed (state 'on', 'off' or 'pending').
     *
     * @param expMailRecipient $recipient
     * @param expMailCategory $category
     * @param string $state
     * @param expConsentContext $context
     */
    public function changed( expMailRecipient $recipient, expMailCategory $category, $state, expConsentContext $context );
}
