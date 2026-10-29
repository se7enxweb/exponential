<?php
/**
 * The syntax checker of one template engine, for exp:template:check (expTemplateChecker). An engine's checker is
 * registered in site.ini [TemplateBridgeSettings] CheckerList[<engine>]=<class>.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
interface expTemplateEngineChecker
{
    /**
     * @return bool whether the engine's library is available (Exponential templates always are)
     */
    public function available();

    /**
     * Checks one template.
     *
     * @param string $file the file, relative to the installation root ('-' for stdin)
     * @param string $text the template text
     * @param string $level syntax, compile or strict
     * @param array $context compiledDir (a private directory for compiled output), security (bool), includes (bool),
     *                       deprecations (bool), resolve (callable( $uri ): bool, whether a design: URI resolves)
     * @return array[] findings: rule, severity (ERROR, WARN, NOTE), line, column, message, hint (or null)
     */
    public function check( $file, $text, $level, array $context );
}
