<?php
/**
 * File containing the expSysCardProvider interface.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A provider of the system card: one class per group of facts (the machine, PHP, the engine, the database ...).
 * The providers are listed in syscard.ini [Providers] ProviderClasses[], in the order their facts are shown; an
 * extension adds its own in its settings/syscard.ini.append.php. Each can be switched off with Enabled[<id>]=disabled.
 *
 * A provider never prints anything and never changes anything. It reads through the context only (so a test can
 * give it made-up values) and returns its facts. It gets a time budget: when it takes longer, its facts show "n/a"
 * (see expSysCardRegistry). Most providers extend expSysCardBaseProvider.
 */
interface expSysCardProvider
{
    /**
     * @return string a short id, lowercase (host, php, velocity ...): the key of Enabled[], Priority[] and Budget[]
     */
    public function id();

    /**
     * @return string[] the cards it adds facts to: card (exp:expfetch, and so all three), info (exp:info), about (exp:about)
     */
    public function modes();

    /**
     * @return int the higher, the earlier it runs; when the total budget is spent, the lowest ones are left out
     */
    public function priority();

    /**
     * @return float seconds it may take
     */
    public function budget();

    /**
     * @return bool whether --watch refreshes it (load, memory, workers); the others keep their first facts
     */
    public function watched();

    /**
     * @param expSysCardContext $context
     * @return expSysCardFact[]
     */
    public function collect( expSysCardContext $context );
}
