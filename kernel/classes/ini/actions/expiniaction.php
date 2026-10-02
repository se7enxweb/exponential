<?php
/**
 * File containing the expIniAction interface.
 *
 * One action of the exp:ini command (get, set, add, rem, ...). The built-in actions and the
 * actions of extensions are registered in ini.ini [IniCommandSettings] Actions[<name>]=<class>;
 * the command makes the class, hands it the parsed command line as an expIniCommandContext and
 * exits with what run() returns. Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

interface expIniAction
{
    /**
     * The name the action is called by: "exp:ini <name> ...".
     *
     * @return string
     */
    public function name();

    /**
     * One line for "exp:ini actions" and the command's --help.
     *
     * @return string
     */
    public function description();

    /**
     * The arguments after the action's name, e.g. "<file>/<Block>/<Variable> <value> <scope>",
     * with optional lines of explanation and examples after the first line.
     *
     * @return string
     */
    public function usage();

    /**
     * Does the work.
     *
     * @param expIniCommandContext $c the arguments, the options, the output and the editor factory
     * @return int the exit code: expIniCommandContext::EXIT_OK, EXIT_USAGE, EXIT_NOT_FOUND,
     *             EXIT_REFUSED or EXIT_WRITE_FAILED
     */
    public function run( expIniCommandContext $c );
}
