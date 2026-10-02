<?php
/**
 * The code of kernel/shop/dashboard.php, moved into a class (#207 stage 1). The file kernel/shop/dashboard.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/dashboard.php:
 *
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Shop
{

class Dashboard extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $tpl = \eZTemplate::factory();
        $db = \eZDB::instance();
        $context = 'design/admin/shop/dashboard';
        $startTime = microtime( true );

        $now = time();
        $midnight = mktime( 0, 0, 0 );
        $since7 = $midnight - 6 * 86400;
        $since30 = $midnight - 29 * 86400;
        $since60 = $midnight - 59 * 86400;

        // Which statuses are finished (no longer open), bring in no revenue (failed,
        // cancelled, refunded), or wait for the customer (the shorter limit below).
        $finishedStatusIDs = \eZOrderStatus::finishedStatusIDs();
        $noRevenueStatusIDs = \eZOrderStatus::noRevenueStatusIDs();
        $customerStatusIDs = \eZOrderStatus::waitingForCustomerStatusIDs();

        // Thresholds for "waiting too long", in days.
        $pendingLimitDays = 2;
        $processingLimitDays = 5;
        // A basket not touched for this long counts as abandoned.
        $abandonedAfterHours = 24;

        $shopINI = \eZINI::instance( 'shop.ini' );
        $siteINI = \eZINI::instance();

        /*
         * Currencies: formatting data for every currency an order or basket uses.
         */
        $localeCurrency = \eZOrder::fetchLocaleCurrencyCode();
        $currencyRows = \eZCurrencyData::fetchList();
        if ( !is_array( $currencyRows ) )
            $currencyRows = array();
        $currencyFormat = array();
        $currencyList = array();
        foreach ( $currencyRows as $code => $currency )
        {
            $currencyFormat[$code] = array( 'locale' => $currency->attribute( 'locale' ), 'symbol' => $currency->attribute( 'symbol' ) );
            $currencyList[] = array( 'code' => $code,
                                     'symbol' => $currency->attribute( 'symbol' ),
                                     'locale' => $currency->attribute( 'locale' ),
                                     'rate' => $currency->attribute( 'rate_value' ),
                                     'auto_rate' => $currency->attribute( 'auto_rate_value' ),
                                     'custom_rate' => $currency->attribute( 'custom_rate_value' ),
                                     'factor' => $currency->attribute( 'rate_factor' ),
                                     'is_active' => $currency->attribute( 'status' ) == \eZCurrencyData::STATUS_ACTIVE );
        }
        $currencyCode = function( $code ) use ( $localeCurrency )
        {
            return ( $code === null || $code === '' ) ? $localeCurrency : $code;
        };

        /*
         * 1. Orders of the last 60 days (30 shown, 30 before them for comparison), with their
         *    product lines and extra order lines (shipping and the like).
         */
        $orderRows = $db->arrayQuery( "SELECT o.id, o.order_nr, o.created, o.status_id, o.status_modified, o.is_archived,
                                              o.email, o.ignore_vat, pc.currency_code
                                       FROM ezorder o, ezproductcollection pc
                                       WHERE pc.id = o.productcollection_id
                                         AND o.is_temporary = 0
                                         AND o.created >= " . (int)$since60 );
        $itemRows = $db->arrayQuery( "SELECT o.id AS order_id, i.contentobject_id, i.name, i.item_count, i.price, i.is_vat_inc,
                                             i.vat_value, i.discount
                                      FROM ezorder o, ezproductcollection_item i
                                      WHERE i.productcollection_id = o.productcollection_id
                                        AND o.is_temporary = 0
                                        AND o.created >= " . (int)$since60 );
        $extraRows = $db->arrayQuery( "SELECT oi.order_id, oi.price, oi.vat_value, oi.is_vat_inc
                                       FROM ezorder o, ezorder_item oi
                                       WHERE oi.order_id = o.id
                                         AND o.is_temporary = 0
                                         AND o.created >= " . (int)$since60 );

        // The same arithmetic as eZOrder::productItems() and eZOrderItem, so each total here
        // equals the total shown in the order list and the order view.
        $lineTotals = function( $price, $count, $isVatInc, $vatValue, $discount )
        {
            if ( $isVatInc )
            {
                $ex = $price / ( 100 + $vatValue ) * 100;
                $inc = $price;
            }
            else
            {
                $ex = $price;
                $inc = $price * ( 100 + $vatValue ) / 100;
            }
            $factor = ( 100 - $discount ) / 100;
            return array( round( $count * $ex * $factor, 2 ), round( $count * $inc * $factor, 2 ) );
        };

        $orders = array();
        foreach ( $orderRows as $row )
        {
            $orders[(int)$row['id']] = array( 'id' => (int)$row['id'],
                                              'order_nr' => (int)$row['order_nr'],
                                              'created' => (int)$row['created'],
                                              'status_id' => (int)$row['status_id'],
                                              'status_modified' => (int)$row['status_modified'],
                                              'is_archived' => (int)$row['is_archived'],
                                              'email' => $row['email'],
                                              'ignore_vat' => (int)$row['ignore_vat'],
                                              'currency' => $currencyCode( $row['currency_code'] ),
                                              'total_ex_vat' => 0.0,
                                              'total_inc_vat' => 0.0,
                                              'item_count' => 0 );
        }
        $productSales = array();
        foreach ( $itemRows as $row )
        {
            $orderID = (int)$row['order_id'];
            if ( !isset( $orders[$orderID] ) )
                continue;
            $vat = $orders[$orderID]['ignore_vat'] ? 0 : (float)$row['vat_value'];
            list( $ex, $inc ) = $lineTotals( (float)$row['price'], (int)$row['item_count'], (int)$row['is_vat_inc'], $vat, (float)$row['discount'] );
            $orders[$orderID]['total_ex_vat'] += $ex;
            $orders[$orderID]['total_inc_vat'] += $inc;
            $orders[$orderID]['item_count'] += (int)$row['item_count'];

            if ( $orders[$orderID]['created'] >= $since30 && !in_array( $orders[$orderID]['status_id'], $noRevenueStatusIDs ) )
            {
                $objectID = (int)$row['contentobject_id'];
                $cur = $orders[$orderID]['currency'];
                if ( !isset( $productSales[$objectID] ) )
                    $productSales[$objectID] = array( 'object_id' => $objectID, 'name' => $row['name'], 'quantity' => 0,
                                                      'orders' => array(), 'revenue' => array() );
                $productSales[$objectID]['quantity'] += (int)$row['item_count'];
                $productSales[$objectID]['orders'][$orderID] = true;
                if ( !isset( $productSales[$objectID]['revenue'][$cur] ) )
                    $productSales[$objectID]['revenue'][$cur] = 0.0;
                $productSales[$objectID]['revenue'][$cur] += $inc;
            }
        }
        foreach ( $extraRows as $row )
        {
            $orderID = (int)$row['order_id'];
            if ( !isset( $orders[$orderID] ) )
                continue;
            list( $ex, $inc ) = $lineTotals( (float)$row['price'], 1, (int)$row['is_vat_inc'], (float)$row['vat_value'], 0 );
            $orders[$orderID]['total_ex_vat'] += $ex;
            $orders[$orderID]['total_inc_vat'] += $inc;
        }

        // Period figures.
        $periodDefinitions = array( 'today' => array( $midnight, $now + 1 ),
                                    'days7' => array( $since7, $now + 1 ),
                                    'days30' => array( $since30, $now + 1 ),
                                    'previous30' => array( $since60, $since30 ) );
        $periods = array();
        $usedCurrencies = array();
        foreach ( $periodDefinitions as $key => $range )
        {
            $period = array( 'orders' => 0, 'items' => 0, 'customers' => array(), 'revenue' => array(), 'revenue_ex_vat' => array(), 'average' => array() );
            foreach ( $orders as $order )
            {
                if ( $order['created'] < $range[0] || $order['created'] >= $range[1] )
                    continue;
                $cur = $order['currency'];
                $usedCurrencies[$cur] = true;
                $period['orders']++;
                $period['items'] += $order['item_count'];
                $period['customers'][strtolower( $order['email'] )] = true;
                // A failed, cancelled or refunded order counts as an order, not as revenue.
                if ( in_array( $order['status_id'], $noRevenueStatusIDs ) )
                    continue;
                if ( !isset( $period['revenue'][$cur] ) )
                {
                    $period['revenue'][$cur] = 0.0;
                    $period['revenue_ex_vat'][$cur] = 0.0;
                    $period['count_by_currency'][$cur] = 0;
                }
                $period['revenue'][$cur] += $order['total_inc_vat'];
                $period['revenue_ex_vat'][$cur] += $order['total_ex_vat'];
                $period['count_by_currency'][$cur]++;
            }
            foreach ( $period['revenue'] as $cur => $sum )
                $period['average'][$cur] = round( $sum / max( 1, $period['count_by_currency'][$cur] ), 2 );
            unset( $period['count_by_currency'] );
            $period['customers'] = count( $period['customers'] );
            $periods[$key] = $period;
        }

        // The currency most revenue is in drives the chart; with one currency, that one.
        $mainCurrency = $localeCurrency;
        if ( $periods['days30']['revenue'] )
        {
            $revenueByCurrency = $periods['days30']['revenue'];
            arsort( $revenueByCurrency );
            $mainCurrency = key( $revenueByCurrency );
        }
        elseif ( $shopINI->hasVariable( 'CurrencySettings', 'PreferredCurrency' ) && $shopINI->variable( 'CurrencySettings', 'PreferredCurrency' ) )
        {
            $mainCurrency = $shopINI->variable( 'CurrencySettings', 'PreferredCurrency' );
        }
        $usedCurrencies[$mainCurrency] = true;

        // Trend against the 30 days before: percent change of orders and main-currency revenue.
        $trend = function( $current, $previous )
        {
            if ( $previous <= 0 )
                return $current > 0 ? null : 0;
            return (int)round( ( $current - $previous ) / $previous * 100 );
        };
        $revenue30 = isset( $periods['days30']['revenue'][$mainCurrency] ) ? $periods['days30']['revenue'][$mainCurrency] : 0;
        $revenuePrevious = isset( $periods['previous30']['revenue'][$mainCurrency] ) ? $periods['previous30']['revenue'][$mainCurrency] : 0;
        $trends = array( 'orders' => $trend( $periods['days30']['orders'], $periods['previous30']['orders'] ),
                         'revenue' => $trend( $revenue30, $revenuePrevious ) );

        // Daily chart, 30 days.
        $daily = array();
        for ( $d = 29; $d >= 0; $d-- )
        {
            $start = $midnight - $d * 86400;
            $daily[$start] = array( 'date' => $start, 'orders' => 0, 'revenue' => 0.0, 'is_today' => $d == 0,
                                    'is_monday' => date( 'N', $start ) == 1 );
        }
        foreach ( $orders as $order )
        {
            if ( $order['created'] < $since30 )
                continue;
            $day = mktime( 0, 0, 0, date( 'n', $order['created'] ), date( 'j', $order['created'] ), date( 'Y', $order['created'] ) );
            if ( !isset( $daily[$day] ) )
                continue;
            $daily[$day]['orders']++;
            if ( $order['currency'] === $mainCurrency && !in_array( $order['status_id'], $noRevenueStatusIDs ) )
                $daily[$day]['revenue'] += $order['total_inc_vat'];
        }
        $maxDailyRevenue = 0.0;
        $maxDailyOrders = 0;
        foreach ( $daily as $day )
        {
            $maxDailyRevenue = max( $maxDailyRevenue, $day['revenue'] );
            $maxDailyOrders = max( $maxDailyOrders, $day['orders'] );
        }
        foreach ( $daily as $key => $day )
            $daily[$key]['height'] = $maxDailyRevenue > 0 ? max( $day['revenue'] > 0 ? 3 : 0, (int)round( $day['revenue'] / $maxDailyRevenue * 100 ) ) : 0;
        $daily = array_values( $daily );

        /*
         * 2. All-time figures: order totals per status, customers.
         */
        $statusRows = $db->arrayQuery( "SELECT status_id, is_archived, COUNT(*) AS order_count, MIN(status_modified) AS oldest
                                        FROM ezorder
                                        WHERE is_temporary = 0
                                        GROUP BY status_id, is_archived" );
        $customerRows = $db->arrayQuery( "SELECT email, COUNT(*) AS order_count, MIN(created) AS first_order
                                          FROM ezorder
                                          WHERE is_temporary = 0
                                          GROUP BY email" );
        $allTime = array( 'orders' => 0, 'archived' => 0, 'open' => 0, 'customers' => 0, 'repeat_customers' => 0,
                          'new_customers_30' => 0, 'first_order' => false );
        foreach ( $customerRows as $row )
        {
            $allTime['customers']++;
            if ( (int)$row['order_count'] > 1 )
                $allTime['repeat_customers']++;
            if ( (int)$row['first_order'] >= $since30 )
                $allTime['new_customers_30']++;
            if ( $allTime['first_order'] === false || (int)$row['first_order'] < $allTime['first_order'] )
                $allTime['first_order'] = (int)$row['first_order'];
        }

        // What each status of the order lifecycle means, and the colour group it is
        // drawn in: new (waits for the shop), customer (waits for the customer), work
        // (the shop is on it), done (finished well), stopped (finished without a sale,
        // or a problem) and custom.
        $statusMeanings = array(
            \eZOrderStatus::PENDING => array( 'new', \ezpI18n::tr( $context, 'Every new order starts here. Check that it is paid, then move it on.' ) ),
            \eZOrderStatus::AWAITING_PAYMENT => array( 'customer', \ezpI18n::tr( $context, 'The order is placed but not paid yet, for example a bank transfer or an invoice.' ) ),
            \eZOrderStatus::PAID => array( 'work', \ezpI18n::tr( $context, 'The payment has arrived. The order can be packed and shipped.' ) ),
            \eZOrderStatus::PAYMENT_FAILED => array( 'stopped', \ezpI18n::tr( $context, 'The payment was refused or cancelled. Ask the customer to pay again, or cancel the order.' ) ),
            \eZOrderStatus::PROCESSING => array( 'work', \ezpI18n::tr( $context, 'You have accepted the order and are packing or shipping it.' ) ),
            \eZOrderStatus::ON_HOLD => array( 'stopped', \ezpI18n::tr( $context, 'Stopped for now, for example while you check a payment, an address or the stock.' ) ),
            \eZOrderStatus::BACKORDERED => array( 'work', \ezpI18n::tr( $context, 'Waiting for products that are out of stock.' ) ),
            \eZOrderStatus::PACKED => array( 'work', \ezpI18n::tr( $context, 'Packed and ready to hand to the carrier.' ) ),
            \eZOrderStatus::SHIPPED => array( 'work', \ezpI18n::tr( $context, 'Handed to the carrier and on its way to the customer.' ) ),
            \eZOrderStatus::READY_FOR_PICKUP => array( 'customer', \ezpI18n::tr( $context, 'Ready and waiting for the customer to collect it.' ) ),
            \eZOrderStatus::DELIVERED => array( 'done', \ezpI18n::tr( $context, 'Shipped and finished. Delivered orders no longer count as open.' ) ),
            \eZOrderStatus::COMPLETED => array( 'done', \ezpI18n::tr( $context, 'Delivered and nothing is left to do. No longer counts as open.' ) ),
            \eZOrderStatus::CANCELLED => array( 'stopped', \ezpI18n::tr( $context, 'Cancelled before it was shipped. No longer counts as open, and not as revenue.' ) ),
            \eZOrderStatus::RETURN_REQUESTED => array( 'customer', \ezpI18n::tr( $context, 'The customer wants to send something back. Agree the return and wait for the parcel.' ) ),
            \eZOrderStatus::RETURNED => array( 'work', \ezpI18n::tr( $context, 'The goods are back. Refund the customer, then set Refunded or Partially refunded.' ) ),
            \eZOrderStatus::PARTIALLY_REFUNDED => array( 'done', \ezpI18n::tr( $context, 'Part of the amount was paid back. No longer counts as open.' ) ),
            \eZOrderStatus::REFUNDED => array( 'stopped', \ezpI18n::tr( $context, 'The whole amount was paid back. No longer counts as open, and not as revenue.' ) ),
        );
        $customMeaning = \ezpI18n::tr( $context, 'A status added for this shop (numbers from 1000 on), for example by a payment extension. Counts as open until the order reaches a finished status.' );
        $unknownMeaning = \ezpI18n::tr( $context, 'A status number below 1000 that this version does not know. Counts as open.' );
        $statusEntry = function( $sid, $name, $isActive ) use ( $statusMeanings, $customMeaning, $unknownMeaning, $finishedStatusIDs, $noRevenueStatusIDs )
        {
            if ( isset( $statusMeanings[$sid] ) )
                list( $group, $meaning ) = $statusMeanings[$sid];
            else
            {
                $group = 'custom';
                $meaning = $sid < \eZOrderStatus::CUSTOM ? $unknownMeaning : $customMeaning;
            }
            return array( 'status_id' => $sid,
                          'name' => $name,
                          'is_active' => $isActive,
                          'is_internal' => $sid < \eZOrderStatus::CUSTOM,
                          'is_finished' => in_array( $sid, $finishedStatusIDs ),
                          'no_revenue' => in_array( $sid, $noRevenueStatusIDs ),
                          'group' => $group,
                          'meaning' => $meaning,
                          'open' => 0, 'archived' => 0, 'oldest' => false, 'percent' => 0 );
        };

        $statusObjects = \eZOrderStatus::fetchList( true, true );
        $statusNames = array();
        $statuses = array();
        foreach ( $statusObjects as $status )
        {
            $sid = (int)$status->attribute( 'status_id' );
            $statusNames[$sid] = $status->attribute( 'name' );
            $statuses[$sid] = $statusEntry( $sid, $status->attribute( 'name' ), (bool)$status->attribute( 'is_active' ) );
        }
        foreach ( $statusRows as $row )
        {
            $sid = (int)$row['status_id'];
            if ( !isset( $statuses[$sid] ) )
                $statuses[$sid] = $statusEntry( $sid, \ezpI18n::tr( $context, 'Unknown status %id', null, array( '%id' => $sid ) ), false );
            $count = (int)$row['order_count'];
            $allTime['orders'] += $count;
            if ( (int)$row['is_archived'] )
            {
                $statuses[$sid]['archived'] += $count;
                $allTime['archived'] += $count;
            }
            else
            {
                $statuses[$sid]['open'] += $count;
                if ( $statuses[$sid]['oldest'] === false || (int)$row['oldest'] < $statuses[$sid]['oldest'] )
                    $statuses[$sid]['oldest'] = (int)$row['oldest'];
                if ( !in_array( $sid, $finishedStatusIDs ) )
                    $allTime['open'] += $count;
            }
        }
        $nonArchived = $allTime['orders'] - $allTime['archived'];
        foreach ( $statuses as $sid => $status )
            $statuses[$sid]['percent'] = $nonArchived > 0 ? (int)round( $status['open'] / $nonArchived * 100 ) : 0;
        // In the order of the lifecycle (the order of $statusMeanings), custom and
        // unknown statuses after it by number.
        $lifecycleOrder = array_flip( array_keys( $statusMeanings ) );
        uksort( $statuses, function( $a, $b ) use ( $lifecycleOrder )
        {
            $pa = isset( $lifecycleOrder[$a] ) ? $lifecycleOrder[$a] : count( $lifecycleOrder ) + $a;
            $pb = isset( $lifecycleOrder[$b] ) ? $lifecycleOrder[$b] : count( $lifecycleOrder ) + $b;
            return $pa <=> $pb;
        } );
        $statuses = array_values( $statuses );

        /*
         * 3. Orders waiting for the shop: every order that is not finished and not archived,
         *    longest waiting first, and the latest orders.
         */
        $finishedList = implode( ', ', array_map( 'intval', $finishedStatusIDs ) );
        $customerList = implode( ', ', array_map( 'intval', $customerStatusIDs ) );
        $waitingOrders = \eZPersistentObject::fetchObjectList( \eZOrder::definition(), null,
                                                              array( 'is_temporary' => 0, 'is_archived' => 0 ),
                                                              array( 'status_modified' => 'asc' ),
                                                              array( 'offset' => 0, 'length' => 10 ), true,
                                                              false, null, null, " AND status_id NOT IN ( $finishedList )" );
        $latestOrders = \eZPersistentObject::fetchObjectList( \eZOrder::definition(), null,
                                                             array( 'is_temporary' => 0 ),
                                                             array( 'created' => 'desc' ),
                                                             array( 'offset' => 0, 'length' => 8 ), true );
        $overdueCount = array( 'pending' => 0, 'processing' => 0, 'other' => 0 );
        $overdueRows = $db->arrayQuery( "SELECT status_id, COUNT(*) AS order_count
                                         FROM ezorder
                                         WHERE is_temporary = 0 AND is_archived = 0
                                           AND ( ( status_id IN ( $customerList ) AND status_modified < " . ( $now - $pendingLimitDays * 86400 ) . " )
                                              OR ( status_id NOT IN ( $customerList ) AND status_id NOT IN ( $finishedList )
                                                   AND status_modified < " . ( $now - $processingLimitDays * 86400 ) . " ) )
                                         GROUP BY status_id" );
        foreach ( $overdueRows as $row )
        {
            if ( (int)$row['status_id'] == \eZOrderStatus::PENDING )
                $overdueCount['pending'] += (int)$row['order_count'];
            elseif ( (int)$row['status_id'] == \eZOrderStatus::PROCESSING )
                $overdueCount['processing'] += (int)$row['order_count'];
            else
                $overdueCount['other'] += (int)$row['order_count'];
        }

        $orderSummary = function( \eZOrder $order ) use ( $orders, $statusNames, $now, $pendingLimitDays, $processingLimitDays, $currencyCode,
                                                         $finishedStatusIDs, $customerStatusIDs, $statusMeanings )
        {
            $id = (int)$order->attribute( 'id' );
            $sid = (int)$order->attribute( 'status_id' );
            if ( isset( $orders[$id] ) )
            {
                $total = $orders[$id]['total_inc_vat'];
                $currency = $orders[$id]['currency'];
            }
            else
            {
                // Older than the 60 days above: the order computes its own total.
                $total = $order->totalIncVAT();
                $currency = $order->currencyCode();
            }
            $waitDays = ( $now - (int)$order->attribute( 'status_modified' ) ) / 86400;
            $limit = in_array( $sid, $customerStatusIDs ) ? $pendingLimitDays : $processingLimitDays;
            return array( 'id' => $id,
                          'order_nr' => (int)$order->attribute( 'order_nr' ),
                          'created' => (int)$order->attribute( 'created' ),
                          'status_id' => $sid,
                          'status_name' => isset( $statusNames[$sid] ) ? $statusNames[$sid] : $sid,
                          'status_group' => isset( $statusMeanings[$sid] ) ? $statusMeanings[$sid][0] : 'custom',
                          'status_modified' => (int)$order->attribute( 'status_modified' ),
                          'wait_days' => (int)floor( $waitDays ),
                          'wait_hours' => (int)floor( $waitDays * 24 ),
                          'overdue' => !in_array( $sid, $finishedStatusIDs ) && $waitDays > $limit,
                          'is_archived' => (int)$order->attribute( 'is_archived' ),
                          'customer' => $order->accountName(),
                          'email' => $order->attribute( 'email' ),
                          'user_id' => (int)$order->attribute( 'user_id' ),
                          'total' => $total,
                          'currency' => $currency );
        };
        $waiting = array();
        foreach ( $waitingOrders as $order )
            $waiting[] = $orderSummary( $order );
        $latest = array();
        foreach ( $latestOrders as $order )
            $latest[] = $orderSummary( $order );

        /*
         * 4. Baskets and checkouts that were never finished.
         */
        $freshSince = $now - $abandonedAfterHours * 3600;
        $basketRows = $db->arrayQuery( "SELECT b.id, pc.created, pc.currency_code, SUM(i.item_count) AS item_count,
                                               SUM(i.item_count * i.price) AS basket_value
                                        FROM ezbasket b, ezproductcollection pc, ezproductcollection_item i
                                        WHERE pc.id = b.productcollection_id
                                          AND i.productcollection_id = pc.id
                                        GROUP BY b.id, pc.created, pc.currency_code" );
        $basketTotalRows = $db->arrayQuery( "SELECT COUNT(*) AS basket_count FROM ezbasket" );
        $baskets = array( 'total' => $basketTotalRows ? (int)$basketTotalRows[0]['basket_count'] : 0,
                          'fresh' => array( 'count' => 0, 'items' => 0, 'value' => array() ),
                          'abandoned' => array( 'count' => 0, 'items' => 0, 'value' => array() ),
                          'oldest' => false );
        foreach ( $basketRows as $row )
        {
            $key = (int)$row['created'] >= $freshSince ? 'fresh' : 'abandoned';
            $cur = $currencyCode( $row['currency_code'] );
            $usedCurrencies[$cur] = true;
            $baskets[$key]['count']++;
            $baskets[$key]['items'] += (int)$row['item_count'];
            if ( !isset( $baskets[$key]['value'][$cur] ) )
                $baskets[$key]['value'][$cur] = 0.0;
            $baskets[$key]['value'][$cur] += round( (float)$row['basket_value'], 2 );
            if ( $baskets['oldest'] === false || (int)$row['created'] < $baskets['oldest'] )
                $baskets['oldest'] = (int)$row['created'];
        }
        $baskets['empty'] = max( 0, $baskets['total'] - $baskets['fresh']['count'] - $baskets['abandoned']['count'] );

        $temporaryRows = $db->arrayQuery( "SELECT COUNT(*) AS order_count, MIN(created) AS oldest
                                           FROM ezorder
                                           WHERE is_temporary = 1 AND created >= " . (int)$since30 );
        $unfinishedCheckouts = array( 'count' => $temporaryRows ? (int)$temporaryRows[0]['order_count'] : 0,
                                      'oldest' => $temporaryRows && $temporaryRows[0]['oldest'] ? (int)$temporaryRows[0]['oldest'] : false );

        /*
         * 5. Products: classes with a price attribute, their objects, price and VAT type.
         */
        $productDatatypes = \eZShopFunctions::productDatatypeStringList();
        $quotedTypes = array();
        foreach ( $productDatatypes as $type )
            $quotedTypes[] = "'" . $db->escapeString( $type ) . "'";
        $priceAttributeRows = $db->arrayQuery( "SELECT a.id, a.identifier, a.data_type_string, a.contentclass_id
                                                FROM ezcontentclass_attribute a, ezcontentclass c
                                                WHERE a.contentclass_id = c.id
                                                  AND a.version = 0 AND c.version = 0
                                                  AND a.data_type_string IN ( " . implode( ', ', $quotedTypes ) . " )" );
        $productClasses = array();
        $priceAttributeIDs = array();
        foreach ( $priceAttributeRows as $row )
        {
            $classID = (int)$row['contentclass_id'];
            if ( isset( $productClasses[$classID] ) )
                continue;
            $class = \eZContentClass::fetch( $classID );
            $productClasses[$classID] = array( 'id' => $classID,
                                               'name' => $class ? $class->attribute( 'name' ) : $classID,
                                               'identifier' => $class ? $class->attribute( 'identifier' ) : '',
                                               'price_attribute' => $row['identifier'],
                                               'datatype' => $row['data_type_string'],
                                               'products' => 0, 'no_price' => 0, 'dynamic_vat' => 0 );
            $priceAttributeIDs[(int)$row['id']] = $classID;
        }
        $products = array( 'total' => 0, 'no_price' => 0, 'no_price_list' => array(), 'dynamic_vat' => 0, 'vat_types' => array(),
                           'no_category' => false, 'uses_multiprice' => false, 'multiprice_missing' => array() );
        $productObjectInfo = array();
        if ( $priceAttributeIDs )
        {
            $productRows = $db->arrayQuery( "SELECT o.id, o.name, o.contentclass_id, a.id AS attribute_id, a.data_type_string, a.data_float, a.data_text
                                             FROM ezcontentobject o, ezcontentobject_attribute a
                                             WHERE a.contentobject_id = o.id
                                               AND a.version = o.current_version
                                               AND o.status = " . \eZContentObject::STATUS_PUBLISHED . "
                                               AND a.contentclassattribute_id IN ( " . implode( ', ', array_keys( $priceAttributeIDs ) ) . " )
                                             ORDER BY o.id",
                                            array( 'offset' => 0, 'limit' => 5000 ) );
            $seen = array();
            $multipriceAttributes = array();
            foreach ( $productRows as $row )
            {
                $objectID = (int)$row['id'];
                if ( isset( $seen[$objectID] ) )
                    continue;       // one row per object, whatever its translations
                $seen[$objectID] = true;
                $classID = (int)$row['contentclass_id'];
                $products['total']++;
                if ( isset( $productClasses[$classID] ) )
                    $productClasses[$classID]['products']++;
                $productObjectInfo[$objectID] = array( 'name' => $row['name'] );

                // ezprice and ezmultiprice keep "<vat type id>,<vat included>" in data_text.
                $vatParts = explode( ',', (string)$row['data_text'] );
                $vatTypeID = (int)$vatParts[0];
                if ( $vatTypeID == -1 )
                {
                    $products['dynamic_vat']++;
                    if ( isset( $productClasses[$classID] ) )
                        $productClasses[$classID]['dynamic_vat']++;
                }
                if ( !isset( $products['vat_types'][$vatTypeID] ) )
                    $products['vat_types'][$vatTypeID] = 0;
                $products['vat_types'][$vatTypeID]++;

                if ( $row['data_type_string'] === 'ezmultiprice' )
                {
                    $products['uses_multiprice'] = true;
                    $multipriceAttributes[(int)$row['attribute_id']] = $objectID;
                }
                elseif ( (float)$row['data_float'] <= 0 )
                {
                    $products['no_price']++;
                    if ( isset( $productClasses[$classID] ) )
                        $productClasses[$classID]['no_price']++;
                    if ( count( $products['no_price_list'] ) < 10 )
                        $products['no_price_list'][] = $objectID;
                }
            }
            if ( $multipriceAttributes )
            {
                // Multi-price products without a single price row in any currency.
                $priced = array();
                foreach ( $db->arrayQuery( "SELECT contentobject_attr_id, COUNT(*) AS price_count FROM ezmultipricedata
                                            WHERE contentobject_attr_id IN ( " . implode( ', ', array_keys( $multipriceAttributes ) ) . " )
                                              AND value > 0
                                            GROUP BY contentobject_attr_id" ) as $row )
                    $priced[(int)$row['contentobject_attr_id']] = true;
                foreach ( $multipriceAttributes as $attributeID => $objectID )
                {
                    if ( isset( $priced[$attributeID] ) )
                        continue;
                    $products['no_price']++;
                    if ( count( $products['no_price_list'] ) < 10 )
                        $products['no_price_list'][] = $objectID;
                }
            }
        }

        // Product categories: only when shop.ini names the category attribute.
        $categoryAttribute = $shopINI->hasVariable( 'VATSettings', 'ProductCategoryAttribute' ) ? $shopINI->variable( 'VATSettings', 'ProductCategoryAttribute' ) : '';
        if ( $categoryAttribute && $productClasses )
        {
            $rows = $db->arrayQuery( "SELECT COUNT(DISTINCT o.id) AS product_count
                                      FROM ezcontentobject o, ezcontentobject_attribute a, ezcontentclass_attribute ca
                                      WHERE a.contentobject_id = o.id
                                        AND a.version = o.current_version
                                        AND a.contentclassattribute_id = ca.id
                                        AND ca.version = 0
                                        AND ca.identifier = '" . $db->escapeString( $categoryAttribute ) . "'
                                        AND o.status = " . \eZContentObject::STATUS_PUBLISHED . "
                                        AND o.contentclass_id IN ( " . implode( ', ', array_keys( $productClasses ) ) . " )
                                        AND ( a.data_int IS NULL OR a.data_int = 0 )" );
            $products['no_category'] = $rows ? (int)$rows[0]['product_count'] : 0;
        }

        // Names and main nodes for every product the page links to (top sellers, missing prices).
        uasort( $productSales, function( $a, $b ) { return $b['quantity'] - $a['quantity']; } );
        $productSales = array_slice( $productSales, 0, 8, true );
        $linkObjectIDs = array_unique( array_merge( array_keys( $productSales ), $products['no_price_list'] ) );
        $objectNodes = array();
        if ( $linkObjectIDs )
        {
            foreach ( $db->arrayQuery( "SELECT o.id, o.name, t.node_id
                                        FROM ezcontentobject o, ezcontentobject_tree t
                                        WHERE t.contentobject_id = o.id
                                          AND t.node_id = t.main_node_id
                                          AND o.id IN ( " . implode( ', ', array_map( 'intval', $linkObjectIDs ) ) . " )" ) as $row )
                $objectNodes[(int)$row['id']] = array( 'name' => $row['name'], 'node_id' => (int)$row['node_id'] );
        }
        $topProducts = array();
        $maxQuantity = 0;
        foreach ( $productSales as $objectID => $sale )
            $maxQuantity = max( $maxQuantity, $sale['quantity'] );
        foreach ( $productSales as $objectID => $sale )
        {
            $topProducts[] = array( 'object_id' => $objectID,
                                    'name' => isset( $objectNodes[$objectID] ) ? $objectNodes[$objectID]['name'] : $sale['name'],
                                    'node_id' => isset( $objectNodes[$objectID] ) ? $objectNodes[$objectID]['node_id'] : false,
                                    'quantity' => $sale['quantity'],
                                    'orders' => count( $sale['orders'] ),
                                    'revenue' => $sale['revenue'],
                                    'percent' => $maxQuantity ? (int)round( $sale['quantity'] / $maxQuantity * 100 ) : 0 );
        }
        $noPriceProducts = array();
        foreach ( $products['no_price_list'] as $objectID )
            $noPriceProducts[] = array( 'object_id' => $objectID,
                                        'name' => isset( $objectNodes[$objectID] ) ? $objectNodes[$objectID]['name'] : $productObjectInfo[$objectID]['name'],
                                        'node_id' => isset( $objectNodes[$objectID] ) ? $objectNodes[$objectID]['node_id'] : false );
        $products['no_price_list'] = $noPriceProducts;
        $products['classes'] = array_values( $productClasses );

        /*
         * 6. How the shop is configured.
         */
        $dynamicVat = \eZVATManager::isDynamicVatChargingEnabled();
        $vatTypes = array();
        foreach ( \eZVatType::fetchList( true, true ) as $vatType )
        {
            $id = (int)$vatType->attribute( 'id' );
            $vatTypes[] = array( 'id' => $id, 'name' => $vatType->attribute( 'name' ), 'percentage' => (float)$vatType->attribute( 'percentage' ),
                                 'products' => isset( $products['vat_types'][$id] ) ? $products['vat_types'][$id] : 0 );
        }
        $vatRules = array();
        $ruleCountries = array();
        foreach ( \eZVatRule::fetchList() as $rule )
        {
            $ruleCountries[$rule->attribute( 'country_code' )] = true;
            $vatRules[] = array( 'id' => (int)$rule->attribute( 'id' ),
                                 'country_code' => $rule->attribute( 'country_code' ),
                                 'country' => $rule->attribute( 'country_code' ) == '*' ? \ezpI18n::tr( $context, 'Any country' ) : $rule->attribute( 'country' ),
                                 'categories' => $rule->attribute( 'product_categories_string' ),
                                 'vat_type' => $rule->attribute( 'vat_type_name' ) );
        }
        $productCategories = array();
        foreach ( (array)\eZProductCategory::fetchList() as $category )
            $productCategories[] = array( 'id' => (int)$category->attribute( 'id' ), 'name' => $category->attribute( 'name' ) );

        $discountRules = array();
        foreach ( (array)\eZDiscountRule::fetchList() as $rule )
        {
            $ruleID = (int)$rule->attribute( 'id' );
            $discountRules[$ruleID] = array( 'id' => $ruleID, 'name' => $rule->attribute( 'name' ), 'sub_rules' => 0, 'members' => 0, 'max_percent' => 0 );
        }
        if ( $discountRules )
        {
            foreach ( $db->arrayQuery( "SELECT discountrule_id, COUNT(*) AS rule_count, MAX(discount_percent) AS max_percent
                                        FROM ezdiscountsubrule GROUP BY discountrule_id" ) as $row )
                if ( isset( $discountRules[(int)$row['discountrule_id']] ) )
                {
                    $discountRules[(int)$row['discountrule_id']]['sub_rules'] = (int)$row['rule_count'];
                    $discountRules[(int)$row['discountrule_id']]['max_percent'] = (float)$row['max_percent'];
                }
            foreach ( $db->arrayQuery( "SELECT discountrule_id, COUNT(*) AS member_count FROM ezuser_discountrule GROUP BY discountrule_id" ) as $row )
                if ( isset( $discountRules[(int)$row['discountrule_id']] ) )
                    $discountRules[(int)$row['discountrule_id']]['members'] = (int)$row['member_count'];
        }
        $discountRules = array_values( $discountRules );

        // Countries customers ordered from in the last 60 days (from the shop account XML),
        // and whether a VAT rule covers each of them.
        $countryNames = array();
        foreach ( (array)\eZCountryType::fetchCountryList() as $country )
            $countryNames[strtolower( $country['Name'] )] = $country['Alpha2'];
        $orderCountries = array();
        if ( $orders )
        {
            $ids = array_slice( array_keys( $orders ), 0, 1000 );
            foreach ( $db->arrayQuery( "SELECT id, data_text_1 FROM ezorder WHERE id IN ( " . implode( ', ', $ids ) . " )" ) as $row )
            {
                $country = '';
                if ( $row['data_text_1'] && preg_match( '#<country>([^<]*)</country>#', $row['data_text_1'], $m ) )
                    $country = html_entity_decode( $m[1], ENT_QUOTES | ENT_XML1, 'UTF-8' );
                if ( $country === '' )
                    $country = '-';
                if ( !isset( $orderCountries[$country] ) )
                {
                    $alpha2 = strlen( $country ) == 2 ? strtoupper( $country ) : ( isset( $countryNames[strtolower( $country )] ) ? $countryNames[strtolower( $country )] : '' );
                    $orderCountries[$country] = array( 'name' => $country, 'code' => $alpha2, 'orders' => 0,
                                                       'has_rule' => isset( $ruleCountries['*'] ) || ( $alpha2 && isset( $ruleCountries[$alpha2] ) ) );
                }
                $orderCountries[$country]['orders']++;
            }
            uasort( $orderCountries, function( $a, $b ) { return $b['orders'] - $a['orders']; } );
        }
        $orderCountries = array_values( $orderCountries );

        // Payment: gateways the installation can offer, and whether a workflow uses one.
        $gateways = array();
        $gatewayINI = \eZINI::instance( 'paymentgateways.ini' );
        foreach ( (array)$gatewayINI->variable( 'GatewaysSettings', 'AvailableGateways' ) as $gateway )
            $gateways[] = array( 'name' => $gateway, 'source' => 'paymentgateways.ini' );
        foreach ( \eZExtension::activeExtensions() as $extension )
        {
            $path = \eZExtension::extensionPath( $extension );
            if ( $path && file_exists( "$path/classes/{$extension}gateway.php" ) )
                $gateways[] = array( 'name' => $extension, 'source' => 'extension' );
        }
        $triggers = array();
        $shopTriggerRows = $db->arrayQuery( "SELECT t.id, t.function_name, t.connect_type, t.workflow_id, w.name AS workflow_name
                                             FROM eztrigger t, ezworkflow w
                                             WHERE w.id = t.workflow_id AND w.version = 0 AND t.module_name = 'shop'" );
        $workflowIDs = array();
        foreach ( $shopTriggerRows as $row )
        {
            $triggers[] = array( 'function' => $row['function_name'], 'connect_type' => $row['connect_type'] == 'b' ? 'before' : 'after',
                                 'workflow_id' => (int)$row['workflow_id'], 'workflow' => $row['workflow_name'], 'events' => array() );
            $workflowIDs[(int)$row['workflow_id']] = true;
        }
        $paymentInCheckout = false;
        if ( $workflowIDs )
        {
            $events = array();
            foreach ( $db->arrayQuery( "SELECT workflow_id, workflow_type_string, description FROM ezworkflow_event
                                        WHERE version = 0 AND workflow_id IN ( " . implode( ', ', array_keys( $workflowIDs ) ) . " )
                                        ORDER BY placement" ) as $row )
                $events[(int)$row['workflow_id']][] = $row['description'] !== null && $row['description'] !== '' ? $row['description'] : $row['workflow_type_string'];
            foreach ( $triggers as $key => $trigger )
            {
                $triggers[$key]['events'] = isset( $events[$trigger['workflow_id']] ) ? $events[$trigger['workflow_id']] : array();
            }
            foreach ( $db->arrayQuery( "SELECT COUNT(*) AS event_count FROM ezworkflow_event
                                        WHERE version = 0 AND workflow_type_string = 'event_ezpaymentgateway'
                                          AND workflow_id IN ( " . implode( ', ', array_keys( $workflowIDs ) ) . " )" ) as $row )
                $paymentInCheckout = (int)$row['event_count'] > 0;
        }
        $paypalBusiness = '';
        if ( in_array( 'ezpaypal', \eZExtension::activeExtensions() ) )
        {
            $paypalINI = \eZINI::instance( 'paypal.ini' );
            $paypalBusiness = $paypalINI->hasVariable( 'PaypalSettings', 'Business' ) ? $paypalINI->variable( 'PaypalSettings', 'Business' ) : '';
        }

        $accountINI = \eZINI::instance( 'shopaccount.ini' );
        $handlerSetting = function( $ini, $group, $name, $default = '' )
        {
            return $ini->hasVariable( $group, $name ) && $ini->variable( $group, $name ) !== '' ? $ini->variable( $group, $name ) : $default;
        };
        $config = array(
            'account_handler' => $handlerSetting( $accountINI, 'AccountSettings', 'Handler', 'ezdefault' ),
            'confirm_handler' => $handlerSetting( $accountINI, 'ConfirmOrderSettings', 'Handler', 'ezdefault' ),
            'shipping_handler' => $handlerSetting( $shopINI, 'ShippingSettings', 'Handler' ),
            'basketinfo_handler' => $handlerSetting( $shopINI, 'BasketInfoSettings', 'Handler' ),
            'vat_handler' => $handlerSetting( $shopINI, 'VATSettings', 'Handler' ),
            'dynamic_vat' => $dynamicVat,
            'category_attribute' => $categoryAttribute,
            'user_country_attribute' => $handlerSetting( $shopINI, 'VATSettings', 'UserCountryAttribute' ),
            'basket_view' => \eZBasket::viewName(),
            'preferred_currency' => $handlerSetting( $shopINI, 'CurrencySettings', 'PreferredCurrency' ),
            'locale_currency' => $localeCurrency,
            'base_currency' => $handlerSetting( $shopINI, 'ExchangeRatesSettings', 'BaseCurrency' ),
            'rates_handler' => $handlerSetting( $shopINI, 'ExchangeRatesSettings', 'ExchangeRatesUpdateHandler' ),
            'send_order_email' => $handlerSetting( $siteINI, 'ShopSettings', 'SendOrderEmail' ) == 'enabled',
            'admin_email_set' => $handlerSetting( $siteINI, 'MailSettings', 'AdminEmail' ) !== '',
            'clear_basket_on_checkout' => $handlerSetting( $siteINI, 'ShopSettings', 'ClearBasketOnCheckout' ),
            'redirect_after_add' => $handlerSetting( $siteINI, 'ShopSettings', 'RedirectAfterAddToBasket' ),
            'basket_cleanup' => $handlerSetting( $siteINI, 'Session', 'BasketCleanup' ),
            'session_handler' => $handlerSetting( $siteINI, 'Session', 'Handler', 'files' ),
            'order_link_view' => $handlerSetting( $shopINI, 'OrderViewSettings', 'OrderLinkView', 'orderview' ),
            'receipt_secret_set' => $handlerSetting( $shopINI, 'OrderReceiptSettings', 'Secret' ) !== '',
            'paypal_active' => in_array( 'ezpaypal', \eZExtension::activeExtensions() ),
            'paypal_business_set' => $paypalBusiness !== '',
            'payment_in_checkout' => $paymentInCheckout,
            'products_ceiling' => 5000,
        );
        $sessionsInDatabase = in_array( strtolower( $config['session_handler'] ), array( 'ezpsessionhandlerdb', 'db' ) );

        /*
         * 7. Next steps, from all of the above.
         */
        $checklist = array();
        $add = function( $level, $text, $url = false, $linkText = false ) use ( &$checklist )
        {
            $checklist[] = array( 'level' => $level, 'text' => $text, 'url' => $url, 'link_text' => $linkText );
        };
        if ( $overdueCount['pending'] )
            $add( 'critical', \ezpI18n::tr( $context, '%count orders have been waiting in status Pending for more than %days days. Check the payment and move them on to Processing, or contact the customer.', null,
                                           array( '%count' => $overdueCount['pending'], '%days' => $pendingLimitDays ) ), '/shop/orderlist', \ezpI18n::tr( $context, 'Order list' ) );
        if ( $overdueCount['processing'] + $overdueCount['other'] )
            $add( 'warning', \ezpI18n::tr( $context, '%count orders have not changed status for more than %days days (Processing or a custom status). Ship them and set them to Delivered.', null,
                                          array( '%count' => $overdueCount['processing'] + $overdueCount['other'], '%days' => $processingLimitDays ) ), '/shop/orderlist', \ezpI18n::tr( $context, 'Order list' ) );
        if ( !$config['payment_in_checkout'] )
            $add( 'critical', \ezpI18n::tr( $context, 'No payment step: no workflow with a payment gateway event is attached to shop/checkout. Orders are confirmed without being paid. Create a workflow with a "Payment Gateway" event and connect it to shop checkout (before) under Triggers.' ),
                  '/trigger/list', \ezpI18n::tr( $context, 'Triggers' ) );
        if ( $config['paypal_active'] && !$config['paypal_business_set'] )
            $add( 'warning', \ezpI18n::tr( $context, 'The PayPal extension is active but paypal.ini [PaypalSettings] Business (the PayPal account that receives the money) is empty.' ) );
        if ( $products['no_price'] )
            $add( 'critical', \ezpI18n::tr( $context, '%count products have no price and cannot be sold.', null, array( '%count' => $products['no_price'] ) ),
                  '/shop/productsoverview', \ezpI18n::tr( $context, 'Products overview' ) );
        if ( !$products['total'] )
            $add( 'critical', \ezpI18n::tr( $context, 'There are no products: create objects of a class with a price attribute.' ), '/class/grouplist', \ezpI18n::tr( $context, 'Classes' ) );
        foreach ( $vatTypes as $vatType )
            if ( $vatType['percentage'] == 0 && $vatType['products'] )
                $add( 'warning', \ezpI18n::tr( $context, 'VAT type "%name" is 0 %, and %count products use it: they are sold without VAT. Check that this is intended.', null,
                                              array( '%name' => $vatType['name'], '%count' => $vatType['products'] ) ), '/shop/vattype', \ezpI18n::tr( $context, 'VAT types' ) );
        if ( $dynamicVat )
        {
            foreach ( $orderCountries as $country )
                if ( !$country['has_rule'] && $country['name'] !== '-' )
                    $add( 'warning', \ezpI18n::tr( $context, 'No VAT rule for %country, where %count recent orders came from.', null,
                                                  array( '%country' => $country['name'], '%count' => $country['orders'] ) ), '/shop/vatrules', \ezpI18n::tr( $context, 'VAT rules' ) );
            if ( !$vatRules )
                $add( 'critical', \ezpI18n::tr( $context, 'Dynamic VAT is switched on but there is not a single VAT rule, so products using it get no VAT.' ), '/shop/vatrules', \ezpI18n::tr( $context, 'VAT rules' ) );
        }
        elseif ( $vatRules )
            $add( 'info', \ezpI18n::tr( $context, 'VAT rules exist but have no effect: shop.ini [VATSettings] Handler is not set, so each product uses the fixed VAT type of its price.' ), '/shop/vatrules', \ezpI18n::tr( $context, 'VAT rules' ) );
        if ( !$currencyList && $products['uses_multiprice'] )
            $add( 'critical', \ezpI18n::tr( $context, 'Products use multi-currency prices but no currency is defined.' ), '/shop/currencylist', \ezpI18n::tr( $context, 'Currencies' ) );
        if ( $config['preferred_currency'] && $currencyList && !isset( $currencyFormat[$config['preferred_currency']] ) )
            $add( 'warning', \ezpI18n::tr( $context, 'The preferred currency %code (shop.ini [CurrencySettings] PreferredCurrency) is not in the currency list.', null,
                                          array( '%code' => $config['preferred_currency'] ) ), '/shop/currencylist', \ezpI18n::tr( $context, 'Currencies' ) );
        if ( $baskets['abandoned']['count'] )
            $add( 'info', \ezpI18n::tr( $context, '%count baskets have not been touched for more than %hours hours. Customers who are signed in can be reminded; guests cannot be reached.', null,
                                       array( '%count' => $baskets['abandoned']['count'], '%hours' => $abandonedAfterHours ) ) );
        if ( $unfinishedCheckouts['count'] )
            $add( 'info', \ezpI18n::tr( $context, '%count checkouts reached the confirmation step in the last 30 days and were never confirmed.', null,
                                       array( '%count' => $unfinishedCheckouts['count'] ) ) );
        if ( $config['basket_cleanup'] == 'cronjob' && !$sessionsInDatabase && $baskets['total'] )
            $add( 'warning', \ezpI18n::tr( $context, 'Sessions are stored by PHP (site.ini [Session] Handler is "%handler"), but basket cleanup runs as a cronjob that removes every basket without a row in the ezsession table: when the infrequent cronjob runs, all current baskets are deleted.', null,
                                          array( '%handler' => $config['session_handler'] ) ) );
        if ( !$config['send_order_email'] )
            $add( 'warning', \ezpI18n::tr( $context, 'Order confirmation e-mails are off (site.ini [ShopSettings] SendOrderEmail).' ) );
        elseif ( !$config['admin_email_set'] )
            $add( 'warning', \ezpI18n::tr( $context, 'site.ini [MailSettings] AdminEmail is empty, so the shop owner gets no copy of new orders.' ) );
        if ( !$discountRules )
            $add( 'info', \ezpI18n::tr( $context, 'No discount rules yet. Discounts are given to users or groups for chosen products or classes.' ), '/shop/discountgroup', \ezpI18n::tr( $context, 'Discounts' ) );
        if ( !$productCategories && $dynamicVat )
            $add( 'info', \ezpI18n::tr( $context, 'No product categories: VAT rules can only be set per country.' ), '/shop/productcategories', \ezpI18n::tr( $context, 'Product categories' ) );
        if ( $categoryAttribute && $products['no_category'] )
            $add( 'warning', \ezpI18n::tr( $context, '%count products have no product category.', null, array( '%count' => $products['no_category'] ) ),
                  '/shop/productsoverview', \ezpI18n::tr( $context, 'Products overview' ) );
        if ( !$allTime['orders'] )
            $add( 'info', \ezpI18n::tr( $context, 'No orders yet. Place a test order on the site to see the whole checkout once.' ) );

        $levelWeight = array( 'critical' => 0, 'warning' => 1, 'info' => 2 );
        usort( $checklist, function( $a, $b ) use ( $levelWeight ) { return $levelWeight[$a['level']] - $levelWeight[$b['level']]; } );
        $checklistCounts = array( 'critical' => 0, 'warning' => 0, 'info' => 0 );
        foreach ( $checklist as $item )
            $checklistCounts[$item['level']]++;

        // Formatting for every currency on the page; one without a currency row falls back
        // to the locale formatting, as the order list does.
        $format = array();
        foreach ( array_keys( $usedCurrencies ) as $code )
            $format[$code] = isset( $currencyFormat[$code] ) ? $currencyFormat[$code] : array( 'locale' => false, 'symbol' => false );
        foreach ( array( 'waiting', 'latest' ) as $listName )
            foreach ( $$listName as $row )
                if ( !isset( $format[$row['currency']] ) )
                    $format[$row['currency']] = isset( $currencyFormat[$row['currency']] ) ? $currencyFormat[$row['currency']] : array( 'locale' => false, 'symbol' => false );

        $tpl->setVariable( 'dashboard', array(
            'now' => $now,
            'since30' => $since30,
            'main_currency' => $mainCurrency,
            'format' => $format,
            'periods' => $periods,
            'trends' => $trends,
            'daily' => $daily,
            'max_daily_revenue' => $maxDailyRevenue,
            'max_daily_orders' => $maxDailyOrders,
            'all_time' => $allTime,
            'statuses' => $statuses,
            'waiting' => $waiting,
            'overdue' => $overdueCount,
            'latest' => $latest,
            'top_products' => $topProducts,
            'baskets' => $baskets,
            'unfinished_checkouts' => $unfinishedCheckouts,
            'products' => $products,
            'vat_types' => $vatTypes,
            'vat_rules' => $vatRules,
            'product_categories' => $productCategories,
            'currencies' => $currencyList,
            'discount_rules' => $discountRules,
            'order_countries' => $orderCountries,
            'gateways' => $gateways,
            'triggers' => $triggers,
            'config' => $config,
            'sessions_in_database' => $sessionsInDatabase,
            'checklist' => $checklist,
            'checklist_counts' => $checklistCounts,
            'limits' => array( 'pending_days' => $pendingLimitDays, 'processing_days' => $processingLimitDays, 'abandoned_hours' => $abandonedAfterHours ),
            'build_ms' => (int)round( ( microtime( true ) - $startTime ) * 1000 ),
        ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:shop/dashboard.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/shop', 'Store dashboard' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
