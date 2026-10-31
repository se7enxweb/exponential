<?php
/**
 * @description Switch the GraphQL API, or only its mutations, off for every siteaccess or for some; a dry run unless --apply
 * @alias exp:graphql:disable
 *
 * File containing the exp:graphql:disable command (bin/php/expgraphqldisable.php); guide: doc/eng-us/features/6.0/graphql.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expgraphqldisable extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_ERRORS = 1;
    const EXIT_USAGE = 2;

    public function run()
    {
        $this->script( array( 'description' => "Switch the GraphQL API off\n" .
                                               "Sets graphql.ini [GraphQLSettings] Enabled=disabled in settings/override (every siteaccess), or takes\n" .
                                               "the siteaccesses of --siteaccess out of SiteAccessList[] there (settings/override wins over the files\n" .
                                               "of a siteaccess; a list that would become empty switches the API off). With --mutations only the\n" .
                                               "mutations are switched off (MutationsEnabled=disabled) and the queries keep answering. /api/graphql\n" .
                                               "then answers 404 (DISABLED); the persisted queries, the keys and the cache stay.\n" .
                                               "Shows the changes as a diff and writes nothing without --apply.\n" .
                                               "\n" .
                                               "./console exp:graphql:disable                               what would change\n" .
                                               "./console exp:graphql:disable --apply\n" .
                                               "./console exp:graphql:disable --siteaccess=site --mutations --apply\n" .
                                               "\n" .
                                               "Exit code: 0 done (or shown); 1 a file could not be written; 2 wrong options (an unknown siteaccess).",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[siteaccess:][mutations][apply]', '',
                                   array( 'siteaccess' => 'only these siteaccesses, comma separated (default: every siteaccess)',
                                          'mutations' => 'switch only the mutations off',
                                          'apply' => 'write; without it nothing is changed' ) );
        $apply = !empty( $options['apply'] );
        $siteAccesses = Expgraphqlenable::listOption( $options, 'siteaccess' );
        $known = \expIniEditor::knownSiteAccesses();
        foreach ( $siteAccesses as $siteAccess )
        {
            if ( !in_array( $siteAccess, $known, true ) )
            {
                $this->cli()->error( "FAIL no siteaccess $siteAccess (" . implode( ', ', $known ) . ')' );
                $this->shutdown( self::EXIT_USAGE );
            }
        }
        $result = \expGraphQLSetup::apply( \expGraphQLSetup::disableChanges( $siteAccesses, !empty( $options['mutations'] ) ), !$apply );
        $failed = Expgraphqlenable::report( $this->cli(), $result, $apply );
        if ( !$apply )
            $this->cli()->output( 'DRY RUN nothing written; run again with --apply' );
        $this->shutdown( $failed ? self::EXIT_ERRORS : self::EXIT_OK );
    }
}

}
?>
