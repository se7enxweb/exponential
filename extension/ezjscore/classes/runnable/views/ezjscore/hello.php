<?php
/**
 * The code of extension/ezjscore/modules/ezjscore/hello.php, moved into a class (#207 stage 1). The file extension/ezjscore/modules/ezjscore/hello.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * Further original header of extension/ezjscore/modules/ezjscore/hello.php:
 *
 * Created on: <16-Jun-2008 00:00:00 ar>
 * ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
 * SOFTWARE NAME: eZ JSCore extension for eZ Publish
 * SOFTWARE RELEASE: 1.x
 * COPYRIGHT NOTICE: Copyright (C) 1999-2014 eZ Systems AS
 * SOFTWARE LICENSE: GNU General Public License v2.0
 * NOTICE: >
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of version 2.0  of the GNU General
 *   Public License as published by the Free Software Foundation.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of version 2.0 of the GNU General
 *   Public License along with this program; if not, write to the Free
 *   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *   MA 02110-1301, USA.
 *
 *
 * ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
 *
 * /*
 *  * Brief: ezjsc module hello
 *  * A hello world view for benchmarking
 *  * /
 */

namespace Exponential\View\Extension\Ezjscore\Ezjscore
{

class Hello extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $withPageLayout = $Params['with_pagelayout'];

        if ( $withPageLayout )
        {
            $Result = array();
            $Result['content'] = 'Hello World!';
        }
        else
        {
            echo 'Hello World!';
            \eZExecution::cleanExit();
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
