<?php
/**
 * @description Convert the Twig templates (.twig) of a design to Smarty templates (.stpl); --dry-run, --file, --force, --override-ini
 * @alias exp:template:convert:twig-to-stpl
 *
 * File containing the exp:template:convert:twig-to-stpl command (bin/php/exptemplateconverttwigtostpl.php): the general
 * command exp:template:convert with --from-engine=twig --to-engine=stpl. The work is done by expTemplateDesignConverter
 * (lib/templatebridge/classes/) and expTwigReverseConverter and expSmartyConverter, through Exponential templates in memory; guide: doc/eng-us/guides/template-conversion.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Exptemplateconverttwigtostpl extends \Exponential\Runnable\Command
{
    /** The engines of this command. */
    const FROM_ENGINE = 'twig';
    const TO_ENGINE = 'stpl';

    public function run()
    {
        $this->script( array( 'description' => "Convert Twig templates (.twig) to Smarty templates (.stpl)\n" .
                                               "Every *.twig below --from becomes the *.stpl at the same place below --to (templates/ and\n" .
                                               "override/templates/ alike), so that it prints what the .twig printed.\n" .
                                               "A .html.twig is read as the template of its name without .html. The .twig is converted to Exponential\n" .
                                               "templates and then to Smarty, in memory, in one run.\n" .
                                               "--override-ini writes the override rules that make the -s siteaccess use the .stpl.\n" .
                                               "The same as exp:template:convert --from-engine=twig --to-engine=stpl.\n" .
                                               "\n" .
                                               "./console exp:template:convert:twig-to-stpl --from=extension/<ext>/design/<design> --to=var/tmp/<design>\n" .
                                               "./console exp:template:convert:twig-to-stpl --from=extension/<ext>/design/<design> --dry-run\n" .
                                               "./console exp:template:convert:twig-to-stpl --file=design/<design>/templates/<name>.twig\n" .
                                               "./console exp:template:convert:twig-to-stpl -s <siteaccess> --from=<design> --to=<design> --override-ini=<file>\n" .
                                               "\n" .
                                               "Every written file ends with a marker comment ({* exp:template:convert twig->stpl md5:... from ... *});\n" .
                                               "a target that has no marker, or was changed after it was written, is skipped (SKIP) unless --force\n" .
                                               "is given. The result is checked before it is written: a .stpl must compile with Smarty 5 (when\n" .
                                               "installed). Prints WRITE or REPLACE for every file written, NOTE for every construct that could not\n" .
                                               "be converted exactly, ERROR for a template that could not be converted, then PASS or FAIL with the counts.\n" .
                                               "Exit code: 0 PASS; 1 FAIL (a template could not be read, converted, checked or written); 2 wrong options;\n" .
                                               "3 the conversion is not available (the engine's library or converter is missing).",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[from:][to:][file:][override-ini:][dry-run][force]', '', array(
            'from' => 'design directory with the .twig templates (every .twig below it is converted)',
            'to' => 'design directory for the .stpl templates (default: the --from directory, next to the sources)',
            'file' => 'convert this one .twig only (below --from, or a file of its own: then --to is the target file)',
            'override-ini' => 'override.ini.append.php to write for the --to design (the -s siteaccess\'s override rules, MatchFile pointing at the converted .stpl)',
            'dry-run' => 'show what would be written; nothing is written',
            'force' => 'also replace a .stpl that was changed or written by hand (no marker, or not matching it)',
        ) );
        $this->shutdown( \expTemplateDesignConverter::runCommand( $options, $this->cli(), self::FROM_ENGINE, self::TO_ENGINE ) );
    }
}

}
