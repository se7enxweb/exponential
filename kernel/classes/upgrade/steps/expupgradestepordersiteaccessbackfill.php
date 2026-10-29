<?php
/**
 * File containing the expUpgradeStepOrderSiteaccessBackfill class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The siteaccess of orders placed before 6.0.15, inferred from the content tree where it can be, through
 * expOrderSiteaccessBackfill as bin/php/expbackfillordersiteaccess.php --apply with its default options. Only orders
 * whose siteaccess can be inferred count as missing; the rest stay unknown, as the command leaves them.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepOrderSiteaccessBackfill extends expUpgradeScriptStep
{
    public function id()
    {
        return '6.0.15-order-siteaccess-backfill';
    }

    public function description()
    {
        return 'The siteaccess of older orders, where the content tree tells it (bin/php/expbackfillordersiteaccess.php)';
    }

    public function after()
    {
        return array( '6.0.15-order-siteaccess-column' );
    }

    /**
     * The orders that would get a siteaccess: order id => siteaccess.
     *
     * @return array|null null when the column is missing
     */
    protected function inferred()
    {
        if ( !eZOrder::hasSiteaccessColumn( true ) )
            return null;
        $problems = array();
        $administrations = array();
        $roots = expOrderSiteaccessBackfill::siteaccessRoots( $problems, $administrations );
        $out = array();
        $offset = 0;
        $batch = 200;
        do
        {
            $orders = (array)eZOrder::active( true, $offset, $batch, 'created', 'asc', eZOrder::SHOW_ALL );
            foreach ( $orders as $order )
            {
                if ( $order->siteaccessName() !== '' )
                    continue;
                $result = expOrderSiteaccessBackfill::infer( expOrderSiteaccessBackfill::orderItemPaths( $order ), $roots, false, array() );
                if ( $result['siteaccess'] !== '' )
                    $out[(int)$order->attribute( 'id' )] = $result['siteaccess'];
            }
            $offset += $batch;
        }
        while ( count( $orders ) === $batch );
        return $out;
    }

    protected function changes( expUpgradeContext $context )
    {
        $inferred = $this->inferred();
        if ( $inferred === null )
            return array( 'infer the siteaccess of older orders once the database stage added ezorder.siteaccess' );
        $lines = array();
        foreach ( $inferred as $id => $siteaccess )
            $lines[] = "order $id: siteaccess $siteaccess";
        return $lines;
    }

    protected function write( expUpgradeContext $context )
    {
        $db = $context->db();
        $done = array();
        foreach ( (array)$this->inferred() as $id => $siteaccess )
        {
            $order = eZOrder::fetch( $id );
            if ( !$order )
                continue;
            $db->begin();
            $order->setAttribute( 'siteaccess', $siteaccess );
            $order->store();
            $db->commit();
            $done[] = "order $id: siteaccess $siteaccess";
        }
        return $done;
    }
}
