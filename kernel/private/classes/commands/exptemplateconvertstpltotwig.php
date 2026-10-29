<?php
/**
 * @description Convert the Smarty templates (.stpl) of a design to Twig templates (.twig); --dry-run, --file, --force, --override-ini, --twig-suffix
 * @alias exp:template:convert:stpl-to-twig
 *
 * File containing the exp:template:convert:stpl-to-twig command (bin/php/exptemplateconvertstpltotwig.php): the general
 * command exp:template:convert with --from-engine=stpl --to-engine=twig. The work is done by expTemplateDesignConverter
 * (lib/templatebridge/classes/) and expSmartyReverseConverter and expTwigConverter, through Exponential templates in memory; guide: doc/eng-us/guides/template-conversion.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Exptemplateconvertstpltotwig extends \Exponential\Runnable\Command
{
    /** The engines of this command. */
    const FROM_ENGINE = 'stpl';
    const TO_ENGINE = 'twig';

    public function run()
    {
        $this->script( array( 'description' => "Convert Smarty templates (.stpl) to Twig templates (.twig)\n" .
                                               "Every *.stpl below --from becomes the *.twig at the same place below --to (templates/ and\n" .
                                               "override/templates/ alike), so that it prints what the .stpl printed.\n" .
                                               "The .stpl is converted to Exponential templates and then to Twig, in memory, in one run.\n" .
                                               "--override-ini writes the override rules that make the -s siteaccess use the .twig.\n" .
                                               "The same as exp:template:convert --from-engine=stpl --to-engine=twig.\n" .
                                               "\n" .
                                               "./console exp:template:convert:stpl-to-twig --from=extension/<ext>/design/<design> --to=var/tmp/<design>\n" .
                                               "./console exp:template:convert:stpl-to-twig --from=extension/<ext>/design/<design> --dry-run\n" .
                                               "./console exp:template:convert:stpl-to-twig --file=design/<design>/templates/<name>.stpl\n" .
                                               "./console exp:template:convert:stpl-to-twig -s <siteaccess> --from=<design> --to=<design> --override-ini=<file>\n" .
                                               "\n" .
                                               "Every written file ends with a marker comment ({# exp:template:convert stpl->twig md5:... from ... #});\n" .
                                               "a target that has no marker, or was changed after it was written, is skipped (SKIP) unless --force\n" .
                                               "is given. The result is checked before it is written: a .twig must compile with Twig 3 (when\n" .
                                               "installed). Prints WRITE or REPLACE for every file written, NOTE for every construct that could not\n" .
                                               "be converted exactly, ERROR for a template that could not be converted, then PASS or FAIL with the counts.\n" .
                                               "Exit code: 0 PASS; 1 FAIL (a template could not be read, converted, checked or written); 2 wrong options;\n" .
                                               "3 the conversion is not available (the engine's library or converter is missing).",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[from:][to:][file:][override-ini:][twig-suffix:][dry-run][force]', '', array(
            'from' => 'design directory with the .stpl templates (every .stpl below it is converted)',
            'to' => 'design directory for the .twig templates (default: the --from directory, next to the sources)',
            'file' => 'convert this one .stpl only (below --from, or a file of its own: then --to is the target file)',
            'override-ini' => 'override.ini.append.php to write for the --to design (the -s siteaccess\'s override rules, MatchFile pointing at the converted .twig)',
            'twig-suffix' => 'the ending of the written Twig templates: .twig (the default) or .html.twig',
            'dry-run' => 'show what would be written; nothing is written',
            'force' => 'also replace a .twig that was changed or written by hand (no marker, or not matching it)',
        ) );
        $this->shutdown( \expTemplateDesignConverter::runCommand( $options, $this->cli(), self::FROM_ENGINE, self::TO_ENGINE ) );
    }
}

}
