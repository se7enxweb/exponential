<?php
/**
 * The code of bin/php/expbackfillordersiteaccess.php.
 * @description Infer the siteaccess of orders placed before ezorder.siteaccess was recorded (dry run unless --apply)
 * Guide: doc/eng-us/features/6.0/order-siteaccess.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expbackfillordersiteaccess extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $script = $this->script(
            array(
                'description' => "Infer the siteaccess of orders that have none (placed before Exponential 6.0.15 recorded it)\n"
                    . "from the content tree each site siteaccess serves: an order whose products all lie in the tree of\n"
                    . "exactly one site (its PathPrefix node, else its RootNode) gets that siteaccess; orders matching none\n"
                    . "or several stay unknown. Administrations (admin, editor ...) never take orders and are left out,\n"
                    . "decided as the siteaccess menu decides them ([AdminSiteAccessMenu] AdminSiteAccessList[], else the\n"
                    . "design). Siteaccesses named with --exclude are never chosen either: the next candidate can win, and an\n"
                    . "order only excluded siteaccesses serve stays unknown. An order that has a siteaccess is never changed.\n"
                    . "Without --apply nothing is written: the siteaccesses considered, excluded and left out are listed,\n"
                    . "then each order with the siteaccess it would get, or why it stays unknown.\n\n"
                    . "Example: php bin/php/expbackfillordersiteaccess.php\n"
                    . "         php bin/php/expbackfillordersiteaccess.php --prefer-deepest-root\n"
                    . "         php bin/php/expbackfillordersiteaccess.php --exclude=site_mobile,site_preview\n"
                    . "         php bin/php/expbackfillordersiteaccess.php --exclude=site_mobile --exclude=site_preview --prefer-deepest-root\n"
                    . "         php bin/php/expbackfillordersiteaccess.php --apply",
                'use-session' => false,
                'use-modules' => false,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            "[apply][prefer-deepest-root][exclude:*]",
            '',
            array(
                'apply' => "Write the inferred siteaccesses (the default is a dry run that changes nothing)",
                'prefer-deepest-root' => "Off by default. Only for public sites with nested roots (a shop inside another site's tree):\n"
                    . "of several sites that serve every product, take the one whose root is deepest when it is the only one that deep",
                'exclude' => "Never choose these siteaccesses: a comma list, and the option may be repeated (--exclude=a,b --exclude=c).\n"
                    . "A name not in AvailableSiteAccessList is a warning, not an error"
            )
        );
        $apply = (bool)$options['apply'];
        $preferDeepest = (bool)$options['prefer-deepest-root'];
        $unknownNames = array();
        $exclude = \expOrderSiteaccessBackfill::parseExclude( $options['exclude'],
                                                             (array)\eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ),
                                                             $unknownNames );

        if ( !\eZOrder::hasSiteaccessColumn( true ) )
        {
            $script->shutdown( 1, 'ezorder has no column siteaccess yet: run the 6.0.0 to 6.0.15 database update first' );
        }

        foreach ( $unknownNames as $name )
            $cli->warning( "--exclude names siteaccess $name, which is not in [SiteAccessSettings] AvailableSiteAccessList" );
        $problems = array();
        $administrations = array();
        $roots = \expOrderSiteaccessBackfill::siteaccessRoots( $problems, $administrations );
        foreach ( $roots as $siteaccess => $root )
        {
            if ( in_array( (string)$siteaccess, $exclude, true ) )
                $cli->output( sprintf( "excluded   siteaccess %-20s serves %s, never chosen (--exclude)", $siteaccess, $root ) );
            else
                $cli->output( sprintf( "considered siteaccess %-20s serves %s", $siteaccess, $root ) );
        }
        foreach ( $administrations as $siteaccess )
            $cli->output( sprintf( "left out   siteaccess %-20s an administration, never takes orders", $siteaccess ) );
        foreach ( $problems as $siteaccess => $why )
            $cli->warning( "siteaccess $siteaccess left out: $why" );
        $cli->output();

        $counts = array( 'inferred' => 0, 'unknown' => 0, 'excluded' => 0, 'kept' => 0 );
        $db = \eZDB::instance();
        $offset = 0;
        $batch = 200;
        do
        {
            $orders = \eZOrder::active( true, $offset, $batch, 'created', 'asc', \eZOrder::SHOW_ALL );
            foreach ( (array)$orders as $order )
            {
                $label = sprintf( "order %d (#%d)", $order->attribute( 'id' ), $order->attribute( 'order_nr' ) );
                if ( $order->siteaccessName() !== '' )
                {
                    $counts['kept']++;
                    continue;
                }
                $result = \expOrderSiteaccessBackfill::infer( \expOrderSiteaccessBackfill::orderItemPaths( $order ), $roots, $preferDeepest, $exclude );
                if ( $result['siteaccess'] === '' )
                {
                    $counts['unknown']++;
                    if ( !empty( $result['excluded'] ) )
                        $counts['excluded']++;
                    $cli->output( "$label: stays unknown, {$result['reason']}" );
                    continue;
                }
                $counts['inferred']++;
                $cli->output( "$label: " . ( $apply ? 'set to ' : 'would be ' ) . "{$result['siteaccess']} ({$result['reason']})" );
                if ( $apply )
                {
                    $db->begin();
                    $order->setAttribute( 'siteaccess', $result['siteaccess'] );
                    $order->store();
                    $db->commit();
                }
            }
            $offset += $batch;
        }
        while ( count( (array)$orders ) === $batch );

        $cli->output();
        $cli->output( sprintf( "%d order(s) %s a siteaccess, %d stay unknown%s, %d had one already and were left alone%s",
                               $counts['inferred'], $apply ? 'got' : 'would get', $counts['unknown'],
                               $exclude ? " ({$counts['excluded']} because only excluded siteaccesses serve them)" : '',
                               $counts['kept'],
                               $apply ? '' : ' (dry run, nothing changed; --apply writes)' ) );
        $cli->output( 'excluded siteaccesses: ' . ( $exclude ? implode( ', ', $exclude ) : 'none' ) );
        $script->shutdown();
    }
}

}
