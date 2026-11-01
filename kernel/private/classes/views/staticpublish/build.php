<?php
/**
 * The staticpublish/build view (/staticpublish/build/<target>): Build starts a full build of the target in the
 * background as the signed-in user (expStaticJob: exp:static:build --full in a process of its own); the page shows the
 * job going on, or the last one, with its progress followed live (page/staticpublish_job.js over staticpublish/stream)
 * and, once it has ended, its report and the way on to the preview. Nothing is published by a build: the build goes
 * into the target's folder, and the preview shows it before Publish. Policy staticpublish/build.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Staticpublish
{

class Build extends Page
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $target = self::target( $Params, 'build' );
        if ( $target === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $messages = array();
        $workDir = \expStaticAdmin::publisher( $target )->workDirectory();

        if ( self::isPost() && isset( $_POST['BuildButton'] ) )
        {
            if ( !\expStaticTarget::enabled() )
                $messages[] = array( 'kind' => 'bad', 'text' => self::t( 'Static publishing is switched off. Switch it on in Setup > Static publishing first.' ) );
            else
            {
                $built = \expStaticManifest::load( rtrim( \expStaticAdmin::publisher( $target )->checkoutDirectory() . '/' . $target->folder(), '/' ) );
                $started = \expStaticJob::start( 'build', $target->id(), array(), self::login(),
                                                 array( 'work_dir' => $workDir, 'expected' => $built !== null ? $built->count( 'page' ) : 0 ) );
                if ( $started['ok'] )
                    return $Module->redirectTo( '/staticpublish/build/' . $target->id() );
                $messages[] = array( 'kind' => 'bad', 'text' => self::refusal( $started ) );
            }
        }

        $job = \expStaticJob::running( $workDir, $target->id() );
        if ( !$job )
            $job = \expStaticJob::lastBuild( $workDir, $target->id() );
        return self::page( 'build.tpl', array(
            'target' => \expStaticAdmin::overview( $target ),
            'messages' => $messages,
            'enabled' => \expStaticTarget::enabled(),
            'view' => 'build',
        ) + Log::jobVariables( $job ), self::path( self::t( 'Build' ), $target ) );
    }
}

}
?>
