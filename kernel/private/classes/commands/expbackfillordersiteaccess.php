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
                    . "from the content tree each siteaccess serves: an order whose products all lie in the tree of exactly\n"
                    . "one siteaccess (its PathPrefix node, else its RootNode) gets that siteaccess; orders matching none or\n"
                    . "several stay unknown. An order that has a siteaccess is never changed. Without --apply nothing is\n"
                    . "written: each order is listed with the siteaccess it would get, or why it stays unknown.\n\n"
                    . "Example: php bin/php/expbackfillordersiteaccess.php\n"
                    . "         php bin/php/expbackfillordersiteaccess.php --prefer-deepest-root\n"
                    . "         php bin/php/expbackfillordersiteaccess.php --apply",
                'use-session' => false,
                'use-modules' => false,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            "[apply][prefer-deepest-root]",
            '',
            array(
                'apply' => "Write the inferred siteaccesses (the default is a dry run that changes nothing)",
                'prefer-deepest-root' => "Of several siteaccesses that serve every product, take the one whose root is deepest\n"
                    . "when it is the only one that deep (a shop's siteaccess beats an administration that serves the whole tree)"
            )
        );
        $apply = (bool)$options['apply'];
        $preferDeepest = (bool)$options['prefer-deepest-root'];

        if ( !\eZOrder::hasSiteaccessColumn( true ) )
        {
            $script->shutdown( 1, 'ezorder has no column siteaccess yet: run the 6.0.0 to 6.0.15 database update first' );
        }

        $problems = array();
        $roots = \expOrderSiteaccessBackfill::siteaccessRoots( $problems );
        foreach ( $roots as $siteaccess => $root )
            $cli->output( sprintf( "siteaccess %-20s serves %s", $siteaccess, $root ) );
        foreach ( $problems as $siteaccess => $why )
            $cli->warning( "siteaccess $siteaccess left out: $why" );
        $cli->output();

        $counts = array( 'inferred' => 0, 'unknown' => 0, 'kept' => 0 );
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
                $result = \expOrderSiteaccessBackfill::infer( \expOrderSiteaccessBackfill::orderItemPaths( $order ), $roots, $preferDeepest );
                if ( $result['siteaccess'] === '' )
                {
                    $counts['unknown']++;
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
        $cli->output( sprintf( "%d order(s) %s a siteaccess, %d stay unknown, %d had one already and were left alone%s",
                               $counts['inferred'], $apply ? 'got' : 'would get', $counts['unknown'], $counts['kept'],
                               $apply ? '' : ' (dry run, nothing changed; --apply writes)' ) );
        $script->shutdown();
    }
}

}
