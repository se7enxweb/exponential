<?php
/**
 * File containing the expUpgradeStepOrderStatuses class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The order statuses of a whole order lifecycle (payment, fulfilment, the end of an order and returns), as internal
 * statuses 4 to 17. A status is added only when no row has its status_id, so a custom status that uses one of those
 * ids keeps it; the row id is left to the database.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepOrderStatuses extends expUpgradeBaseStep
{
    /** status_id => name, as the 6.0.15 database update and a new installation have them */
    const STATUSES = array(
        4 => 'Awaiting payment', 5 => 'Paid', 6 => 'Payment failed', 7 => 'On hold', 8 => 'Backordered', 9 => 'Packed',
        10 => 'Shipped', 11 => 'Ready for pickup', 12 => 'Completed', 13 => 'Cancelled', 14 => 'Return requested',
        15 => 'Returned', 16 => 'Partially refunded', 17 => 'Refunded',
    );

    public function id()
    {
        return '6.0.15-order-statuses';
    }

    public function stage()
    {
        return 'database';
    }

    public function description()
    {
        return 'The order statuses of a whole order lifecycle (internal statuses 4 to 17 in ezorder_status)';
    }

    /**
     * The status ids that have no row, or null when the table cannot be read.
     *
     * @param eZDBInterface $db
     * @return int[]|null
     */
    public function missing( $db )
    {
        if ( expKernelTableSchema::exists( 'ezorder_status', $db, true ) !== true )
            return null;
        $rows = expKernelTableSchema::tryArrayQuery( $db, 'SELECT status_id FROM ezorder_status' );
        if ( $rows === null )
            return null;
        $have = array();
        foreach ( $rows as $row )
        {
            $row = array_change_key_case( (array)$row, CASE_LOWER );
            $have[(int)$row['status_id']] = true;
        }
        $missing = array();
        foreach ( array_keys( self::STATUSES ) as $id )
            if ( !isset( $have[$id] ) )
                $missing[] = $id;
        return $missing;
    }

    public function detect( expUpgradeContext $context )
    {
        $missing = $this->missing( $context->db() );
        if ( $missing === null )
            return self::effect( self::UNKNOWN, 'cannot read ezorder_status' );
        return $missing ? self::effect( self::MISSING, 'missing status ids: ' . implode( ', ', $missing ) )
                        : self::effect( self::PRESENT, 'statuses 4 to 17 are there' );
    }

    public function plan( expUpgradeContext $context )
    {
        $lines = array();
        foreach ( (array)$this->missing( $context->db() ) as $id )
            $lines[] = sprintf( 'add order status %d "%s"', $id, self::STATUSES[$id] );
        return $lines;
    }

    public function apply( expUpgradeContext $context )
    {
        $db = $context->db();
        $missing = $this->missing( $db );
        if ( $missing === null )
            throw new RuntimeException( 'cannot read ezorder_status' );
        $done = array();
        $db->begin();
        foreach ( $missing as $id )
        {
            $status = new eZOrderStatus( array( 'is_active' => 1, 'name' => self::STATUSES[$id], 'status_id' => $id ) );
            $status->store();
            $done[] = sprintf( 'added order status %d "%s"', $id, self::STATUSES[$id] );
        }
        $db->commit();
        eZOrderStatus::flush();
        return $done;
    }
}
