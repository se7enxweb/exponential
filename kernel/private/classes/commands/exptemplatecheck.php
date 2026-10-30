<?php
/**
 * @description Check templates of every engine (.tpl, .stpl, .twig, .html.twig) for valid syntax: files, directories, designs, extensions, siteaccesses; levels syntax/compile/strict; text, json, jsonl, junit, checkstyle, github and sarif output
 * @alias exp:template:check
 * @alias exp:template:lint
 * @alias exp:template:convert:check
 *
 * File containing the exp:template:check command (bin/php/exptemplatecheck.php). The work is done by
 * expTemplateChecker (lib/templatebridge/classes/) with one checker per engine. The names, options, exit codes, rule
 * ids and the JSON schema are a frozen contract (expTemplateCommandContractTest); guide:
 * doc/eng-us/guides/template-syntax-check.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Exptemplatecheck extends \Exponential\Runnable\Command
{
    /** The options (the contract: an option is never removed, renamed or changed in meaning). */
    const OPTIONS = '[design:*][design-dir:*][extension:*][extensions][all-extensions][siteaccess:*][all-siteaccesses]' .
                    '[engine:][suffix-map:*][level:][security][includes][overrides][parity;][deprecations][encoding][max-depth:]' .
                    '[format:][output:][color:][no-progress][progress][summary-by:][fail-on:][baseline:][generate-baseline:]' .
                    '[ignore:*][rules][explain:][schema:][stdin-engine:][stdin-name:][allow-missing-engine][keep-compiled]' .
                    '[jobs:][changed-since:][files-from:]';

    public function run()
    {
        $this->script( array( 'description' => "Check templates of every template engine for valid syntax\n" .
                                               "Exponential templates (.tpl), Smarty templates (.stpl) and Twig templates (.twig, .html.twig), each by\n" .
                                               "its own parser and compiler with the bridge loaded. Targets: files and directories (TARGET ...), '-' for\n" .
                                               "stdin, --design, --design-dir, --extension, --extensions, --siteaccess, --all-siteaccesses; nothing:\n" .
                                               "the current siteaccess's design chain. Nothing is written: compiled output goes to a private directory.\n" .
                                               "\n" .
                                               "./console exp:template:check design/standard/templates/node/view/full.tpl\n" .
                                               "./console exp:template:check --design=<design> --level=strict\n" .
                                               "./console exp:template:check --siteaccess=<siteaccess> --format=json --output=var/tmp/check.json\n" .
                                               "./console exp:template:check --changed-since=HEAD --format=github\n" .
                                               "./console exp:template:check --rules\n" .
                                               "\n" .
                                               "Levels: syntax (the parser), compile (the default: + compiled and the compiled PHP linted), strict\n" .
                                               "(+ the security policy, includes resolved, unknown operators). Every finding has a rule id\n" .
                                               "(--rules, --explain=<id>). Aliases: exp:template:lint, exp:template:convert:check.\n" .
                                               "Exit code: 0 all passed; 1 a finding at or above --fail-on; 2 usage error; 3 an engine needed is\n" .
                                               "unavailable (unless --allow-missing-engine); 4 internal error.",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( self::OPTIONS, '[TARGET*]', array(
            'design' => 'a design by name, in the kernel and in the (active) extensions; repeatable',
            'design-dir' => 'a design directory by path; repeatable',
            'extension' => 'every design of the extension; repeatable',
            'extensions' => 'every design of every active extension',
            'all-extensions' => 'with --design/--extensions: the inactive extensions on disk too',
            'siteaccess' => 'the design chain and override rules of the siteaccess; repeatable',
            'all-siteaccesses' => 'every siteaccess of AvailableSiteAccessList',
            'engine' => 'all (the default) or a list: tpl,stpl,twig',
            'suffix-map' => '<ending>:<engine>, an extra file ending (.tpl.html:tpl); repeatable',
            'level' => 'syntax, compile (the default) or strict',
            'security' => 'check against the engine\'s security policy (on at strict)',
            'includes' => 'every design: include must resolve (on at strict)',
            'overrides' => 'every override rule must point to an existing template (on with --siteaccess)',
            'parity' => 'report templates present in two engines; =render is experimental',
            'deprecations' => 'report deprecated constructs (Twig deprecations while compiling)',
            'encoding' => 'UTF-8 validity, byte order mark, mixed line endings',
            'max-depth' => 'include depth for --includes (default 32)',
            'format' => 'text (the default), json, jsonl, junit, checkstyle, github or sarif',
            'output' => 'write the machine format to this file; the console keeps the text',
            'color' => 'auto, always or never',
            'no-progress' => 'no progress counter',
            'progress' => 'a progress counter on a terminal',
            'summary-by' => 'group the summary by engine, design, extension, siteaccess or directory',
            'fail-on' => 'the severity that fails: error (the default), warn or note',
            'baseline' => 'report only findings that are not in this baseline file',
            'generate-baseline' => 'write today\'s findings to this baseline file',
            'ignore' => 'a glob of files to leave out; repeatable (site.ini [TemplateCheckSettings] IgnoreList[] too)',
            'rules' => 'list every rule id and exit',
            'explain' => 'explain one rule id and exit',
            'schema' => 'the machine output schema version (1)',
            'stdin-engine' => 'the engine of the template read from stdin (-): tpl, stpl or twig',
            'stdin-name' => 'the name of the template read from stdin, for the messages',
            'allow-missing-engine' => 'an unavailable engine does not make the exit code 3',
            'keep-compiled' => 'keep the private directory of compiled output for inspection',
            'jobs' => 'check in this many worker processes',
            'changed-since' => 'only the templates changed since this git reference (and untracked ones)',
            'files-from' => 'check the files listed in this file, one per line (- for stdin)',
        ) );
        // -q: eZCLI::output() writes nothing, but the findings at ERROR are the output of a quiet check
        $quiet = $this->isQuiet();
        $options['quiet'] = $quiet;
        $checker = new \expTemplateChecker( $options,
            function ( $line ) use ( $quiet ) { if ( $quiet ) { fwrite( STDOUT, $line . "\n" ); } else { $this->cli()->output( $line ); } },
            function ( $line ) { $this->cli()->error( $line ); } );
        $this->shutdown( $checker->run( $options['arguments'] ) );
    }
}

}
