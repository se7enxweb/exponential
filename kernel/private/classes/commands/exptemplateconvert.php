<?php
/**
 * @description Convert the templates of a design from one template engine to another (--from-engine, --to-engine: tpl, stpl, twig); --dry-run, --file, --force, --override-ini, --twig-suffix
 * @alias exp:template:convert
 *
 * File containing the exp:template:convert command (bin/php/exptemplateconvert.php). The named commands
 * exp:template:convert:<from>-to-<to> (tpl-to-stpl, stpl-to-tpl, tpl-to-twig, twig-to-tpl, stpl-to-twig, twig-to-stpl)
 * are the same command with the engines fixed.
 * The work is done by expTemplateDesignConverter (lib/templatebridge/classes/); guide:
 * doc/eng-us/guides/template-conversion.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Exptemplateconvert extends \Exponential\Runnable\Command
{
    public function run()
    {
        $this->script( array( 'description' => "Convert the templates of a design from one template engine to another\n" .
                                               "Engines: tpl (Exponential templates, .tpl; also exp, exponential), stpl (Smarty templates, .stpl;\n" .
                                               "also smarty) and twig (Twig templates, .twig and .html.twig). Every template of the --from-engine\n" .
                                               "below --from becomes the template of the --to-engine at the same place below --to (templates/ and\n" .
                                               "override/templates/ alike), so that it prints what the source printed. Smarty and Twig convert to\n" .
                                               "each other through Exponential templates, in memory, in one run. The named commands do the same\n" .
                                               "with the engines fixed: exp:template:convert:tpl-to-stpl, :stpl-to-tpl, :tpl-to-twig, :twig-to-tpl,\n" .
                                               ":stpl-to-twig and :twig-to-stpl.\n" .
                                               "\n" .
                                               "./console exp:template:convert --from-engine=tpl --to-engine=stpl --from=extension/<ext>/design/<design> --to=var/tmp/<design>\n" .
                                               "./console exp:template:convert --from-engine=stpl --to-engine=tpl --from=var/tmp/<design> --to=var/tmp/<design>-tpl\n" .
                                               "./console exp:template:convert --from-engine=tpl --to-engine=stpl --from=extension/<ext>/design/<design> --dry-run\n" .
                                               "./console exp:template:convert --from-engine=tpl --to-engine=stpl --file=design/<design>/templates/<name>.tpl\n" .
                                               "./console exp:template:convert --from-engine=tpl --to-engine=twig --from=extension/<ext>/design/<design> --to=var/tmp/<design> --twig-suffix=.html.twig\n" .
                                               "\n" .
                                               "Every written file ends with a marker comment in the target engine's comment syntax\n" .
                                               "({* exp:template:convert tpl->stpl md5:... from ... *}, {# exp:template:convert tpl->twig ... #});\n" .
                                               "a target that has no marker, or was changed after it was written, is skipped (SKIP) unless --force\n" .
                                               "is given. The result is checked before it is written: a .stpl must compile with Smarty 5 and a .twig\n" .
                                               "with Twig 3 (when installed), a .tpl must parse. Prints WRITE or REPLACE for every file written, NOTE for every\n" .
                                               "construct that could not be converted exactly, ERROR for a template that could not be converted,\n" .
                                               "then PASS or FAIL with the counts.\n" .
                                               "Exit code: 0 PASS; 1 FAIL (a template could not be read, converted, checked or written); 2 wrong\n" .
                                               "options; 3 the conversion between the two engines is not available.",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[from-engine:][to-engine:][from:][to:][file:][override-ini:][twig-suffix:][dry-run][force]', '', array(
            'from-engine' => 'the engine of the templates to convert: tpl, stpl or twig',
            'to-engine' => 'the engine to convert them to: tpl, stpl or twig',
            'from' => 'design directory with the templates to convert (every template of --from-engine below it)',
            'to' => 'design directory for the converted templates (default: the --from directory, next to the sources)',
            'file' => 'convert this one template only (below --from, or a file of its own: then --to is the target file)',
            'override-ini' => 'override.ini.append.php to write for the --to design (the -s siteaccess\'s override rules, MatchFile pointing at the converted templates)',
            'twig-suffix' => 'the ending of written Twig templates: .twig (the default) or .html.twig',
            'dry-run' => 'show what would be written; nothing is written',
            'force' => 'also replace a target that was changed or written by hand (no marker, or not matching it)',
        ) );
        $this->shutdown( \expTemplateDesignConverter::runCommand( $options, $this->cli() ) );
    }
}

}
