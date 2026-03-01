<?php
/**
 * Stub classes for exp_cms_features_2026_02_test.php.
 *
 * Provides minimal implementations of eZ Publish kernel classes needed by the
 * CMS features test suite, without bootstrapping the full application stack.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group exp_cms_features_2026_02
 */

// ---------------------------------------------------------------------------
// eZINIStub — minimal INI reader for test isolation
// ---------------------------------------------------------------------------
if ( !class_exists( 'eZINIStub', false ) )
{
    class eZINIStub
    {
        private static array $_data = [];
        private static ?eZINIStub $_inst = null;

        public static function instance(): eZINIStub
        {
            if ( !self::$_inst )
                self::$_inst = new self();
            return self::$_inst;
        }

        public static function set( string $section, string $key, $value ): void
        {
            self::$_data[$section][$key] = $value;
        }

        public function variable( string $section, string $key )
        {
            return self::$_data[$section][$key] ?? null;
        }

        public static function reset(): void
        {
            self::$_data = [];
            self::$_inst = null;
        }
    }
}

// Alias so code that calls eZINI::instance() picks up the stub
if ( !class_exists( 'eZINI', false ) )
{
    class eZINI extends eZINIStub {}
}

// ---------------------------------------------------------------------------
// eZDebugStub — implements the patched eZDebug methods under test
// ---------------------------------------------------------------------------
if ( !class_exists( 'eZDebugStub', false ) )
{
    class eZDebugStub
    {
        public const LEVEL_NOTICE  = 1;
        public const LEVEL_WARNING = 2;
        public const LEVEL_ERROR   = 3;
        public const LEVEL_DEBUG   = 4;
        public const LEVEL_STRICT  = 5;

        /** @var array<int,array{0:string,1:string}> */
        public array $LogFiles = [];

        private static ?eZDebugStub $_inst = null;
        private static ?string $_lastWarning = null;
        private static ?string $_lastError   = null;

        public static function instance(): eZDebugStub
        {
            if ( !self::$_inst )
                self::$_inst = new self();
            return self::$_inst;
        }

        public static function setInstance( eZDebugStub $inst ): void
        {
            self::$_inst = $inst;
        }

        public static function reset(): void
        {
            self::$_inst = null;
            self::$_lastWarning = null;
            self::$_lastError   = null;
        }

        public static function writeWarning( string $msg, string $ctx = '' ): void
        {
            self::$_lastWarning = $msg;
        }

        public static function writeError( string $msg, string $ctx = '' ): void
        {
            self::$_lastError = $msg;
        }

        public static function accumulatorStart( string $key, string $group = '', string $text = '' ): void {}
        public static function accumulatorStop( string $key ): void {}

        /**
         * ###exp_feature_g03_ez2014.11### — ported from eZDebug
         * Updates log file directory based on site.ini FileSettings/UseGlobalLogDir.
         */
        public static function updateLogFileDirForCurrentSiteaccess(): void
        {
            $ini   = eZINI::instance();
            $debug = self::instance();

            $useGlobalLogDir = $ini->variable( 'FileSettings', 'UseGlobalLogDir' );
            if ( $useGlobalLogDir === 'disabled' )
            {
                $varDir    = $ini->variable( 'FileSettings', 'VarDir' );
                $logDir    = $ini->variable( 'FileSettings', 'LogDir' );
                $varLogDir = "$varDir/$logDir/";
                $debug->setLogFiles( $varLogDir );
            }
            else
            {
                $debug->setLogFiles();
            }
        }

        /**
         * ###exp_feature_g03_ez2014.11### / ###exp_feature_g44_ez2014.11###
         * Set log file paths for all debug levels.
         *
         * @param string $logDir Base log directory (default: 'var/log/').
         */
        public function setLogFiles( string $logDir = 'var/log/' ): void
        {
            // exp_feature_g44: use separate var_log/ dir when configured
            if ( defined( 'EXP_USE_EXTRA_FOLDER_VAR_LOG' ) && EXP_USE_EXTRA_FOLDER_VAR_LOG === true )
            {
                $logDir = str_replace( 'var/', 'var_log/', $logDir );
            }

            $this->LogFiles = [
                self::LEVEL_NOTICE  => [ $logDir, 'notice.log'  ],
                self::LEVEL_WARNING => [ $logDir, 'warning.log' ],
                self::LEVEL_ERROR   => [ $logDir, 'error.log'   ],
                self::LEVEL_DEBUG   => [ $logDir, 'debug.log'   ],
                self::LEVEL_STRICT  => [ $logDir, 'strict.log'  ],
            ];
        }
    }
}

// Alias so test calls to eZDebug:: work against the stub
if ( !class_exists( 'eZDebug', false ) )
{
    class eZDebug extends eZDebugStub {}
}
