<?php
//
// Definition of eZRecieptType class
//
// Created on: <03-02-2007 19:42:02 gb>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//
// SOFTWARE NAME: BC Website Statistics
// COPYRIGHT NOTICE: Copyright (C) 2001-2007 Brookins Consulting
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0 of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301,  USA.
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*! \file ezreceipttype.php
 */

/*!
  \class Reciept ezreceipttype.php
  \brief The event Reciept handles the display of a order reciept module view to a user for dispay, save, print and to also display custom javascript to transmit order purchase details (only durring the final order process) to statistics colletion service.
*/

class eZReceiptType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'ezreceipt';

    /*!
     Constructor
    */
    function __construct()
    {
        $this->eZWorkflowEventType( eZReceiptType::WORKFLOW_TYPE_STRING,
         ezpI18n::tr( 'kernel/workflow/event',
         "BC Website Statistics - Order Statistics Submission and Order Completed View" ) );
	
        $this->setTriggerTypes( array( 'shop' => array(
				       'checkout' => array (
                                       'after' ) ) ) );
    }

    function execute( $process, $event )
    {
        // Fetch request variables
        $http = eZHTTPTool::instance();
        $requestUri = eZSys::requestURI();

        // Fetch custom settings
        $ini = eZINI::instance( 'bcwebsitestatistics.ini' );

        $urchin = $ini->variable( 'BCWebsiteStatisticsSettings', 'Urchin' );
        $udn = $ini->variable( 'BCWebsiteStatisticsSettings', 'HostName' );

        /* Debug settings scheduled to be removed 
        $test = $ini->variable( 'BCWebsiteStatisticsSettings', 'TestMode' ) == 'enabled' ? true : false;
        $debug = $ini->variable( 'BCWebsiteStatisticsSettings', 'DebugMode' ) == 'enabled' ? true : false;
        */

        // Setting to control submission of information
        // to service via client side script (javascript)

        if ( $ini->hasVariable( 'BCWebsiteStatisticsSettings', 'OrderSubmit' ) )
        {
          $settingSubmit = $ini->variable( 'BCWebsiteStatisticsSettings', 'OrderSubmit' ) == 'enabled' ? true : false;
        }
        else
        {
          $settingSubmit = false;
        }

        if ( $settingSubmit == true )
        {
          // Add hook to trigger template override of pagelayout.tpl
          include_once( 'kernel/common/template.php' );
          include_once( 'kernel/common/eztemplatedesignresource.php' );
          $res = eZTemplateDesignResource::instance();
          $res->setKeys( array( array( 'bcwebsitestatistics', '1' ) ) );

          // Template Settings
          $process->Template = array(
                               'templateName' => "design:bcwebsitestatistics/order.tpl",
                               'templateVars' => array( 'request_uri' => $requestUri ),
                               'path' => array(
					       array(
						     'url' => false,
						     'text' => ezpI18n::tr( 'kernel/shop', 'Order Completed' )
						     )
					       )
                               );

          return eZWorkflowType::STATUS_FETCH_TEMPLATE_REPEAT;
        }
        else
        {
            return eZWorkflowType::STATUS_ACCEPTED;
        }
    }
}

eZWorkflowEventType::registerType( "event", eZReceiptType::WORKFLOW_TYPE_STRING, "ezreceipttype" );

?>