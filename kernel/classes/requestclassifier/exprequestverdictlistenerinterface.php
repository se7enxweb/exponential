<?php
/**
 * File containing the expRequestVerdictListenerInterface interface.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Told about every verdict of the request classifier after its security.log line is written
 * (settings/requestclassifier.ini [ClassifierSettings] Listeners[]). The hook for a later rate limit, a ban list or
 * a notification; nothing is blocked by the classifier itself. A listener runs at the end of the request and must be
 * cheap; an exception it throws is caught and ignored.
 *
 * @package kernel
 */
interface expRequestVerdictListenerInterface
{
    /**
     * @param expRequestVerdict $verdict
     * @param expRequestContext $context the raw client address is in $context->ip: never store it unmasked
     * @return void
     */
    public function onVerdict( expRequestVerdict $verdict, expRequestContext $context );
}
