<?php
/**
 * @description Split the migrations folder of the installation's git checkout into a repository of its own with its history (every commit that changed it, same authors, dates and messages); a dry run unless --apply
 * @alias exp:migration:repo:split
 *
 * File containing the exp:migration:repo:split command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationreposplit extends ExpmigrationBase
{
    protected function name()
    {
        return 'exp:migration:repo:split';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Split the migrations folder into a repository of its own, with its history\n" .
                             "Every commit of the installation's checkout that changed the folder becomes a commit of the folder\n" .
                             "alone (same author, committer, dates and message), on the branch --branch of the checkout (default\n" .
                             "migrations-split; a branch that exists is never moved). With --to a new repository is made in that\n" .
                             "new or empty folder with the history as its branch main and, with --remote, the remote origin; it is\n" .
                             "never pushed. Neither the settings nor the files of the site are changed: the report says what to set\n" .
                             "in migration.ini ([RepositorySettings] Mode=separate, Path) and how to take the folder out of the\n" .
                             "site's checkout. Needs only git (no git subtree). Without --apply it says what it would do.\n" .
                             "\n" .
                             "./console exp:migration:repo:split\n" .
                             "./console exp:migration:repo:split --to=../site-migrations --apply\n" .
                             "./console exp:migration:repo:split --to=../site-migrations --remote=git@github.com:example/site-migrations.git --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][format:][directory:][branch:][to:][remote:]', '', array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'directory' => 'The migrations folder to split (default the first [MigrationSettings] DirectoryList entry)',
            'branch' => 'The branch of the checkout the split history goes to (default migrations-split; never an existing one)',
            'to' => 'A new or empty folder for the new repository (its branch main); without it only the branch is made',
            'remote' => 'The remote origin of the new repository (a URL or path; it is not pushed)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $report = \expMigrationRepoSplit::run( array( 'apply' => $apply ) + array_intersect_key( array_filter( $options, 'is_string' ),
                                               array_flip( array( 'directory', 'branch', 'to', 'remote' ) ) ) );
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'Exponential migrations: ' . ( $report['mode'] === 'repo-split' ? 'split the migrations repository' : 'split the migrations repository, dry run (--apply makes it)' ) );
        if ( $report['prefix'] !== '' )
            $lines[] = 'Folder ' . $report['prefix'] . ' of ' . $report['top'] . ': ' . (int)$report['commits'] . ' commit(s) changed it';
        if ( $report['head'] !== '' )
            $lines[] = 'Branch ' . $report['branch'] . ' at ' . substr( $report['head'], 0, 12 ) . ( $report['created'] ? ', new repository ' . $report['to'] : '' );
        else if ( !$report['errors'] )
            $lines[] = 'Would make the branch ' . $report['branch'] . ( $report['to'] !== '' ? ' and the repository ' . $report['to'] : '' );
        foreach ( $report['next'] as $line )
            $lines[] = 'Then: ' . $line;
        return $lines;
    }
}

}
