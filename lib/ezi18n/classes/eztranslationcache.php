<?php
/**
 * File containing the eZTranslationCache class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZTranslationCache eztranslationcache.php
  \brief Cache handling for translations.

*/

class eZTranslationCache
{
    const CODE_DATE = 1058863428;

    /*!
     \static
     \return the locale the cache is kept for: \a $locale, or the RegionalSettings/Locale setting when it is not given.
     A translator passes its own locale, so a translator for another locale than the setting (a second language in
     the same request, a test) neither reads nor writes the cache of the setting's locale.
    */
    static function cacheLocale( $locale = null )
    {
        if ( $locale !== null && $locale !== false && $locale !== '' )
        {
            return $locale;
        }
        return eZINI::instance()->variable( 'RegionalSettings', 'Locale' );
    }

    /*!
     \static
     \return the cache table of locale \a $locale which has cache keys and cache data.
    */
    static function cacheTable( $locale = null )
    {
        $locale = eZTranslationCache::cacheLocale( $locale );
        if ( !isset( $GLOBALS['eZTranslationCacheTable'][$locale] ) )
        {
            $GLOBALS['eZTranslationCacheTable'][$locale] = array();
        }
        return $GLOBALS['eZTranslationCacheTable'][$locale];
    }

    /*!
     \static
     \return the cache translation context which is stored with the cache key \a $contextName.
             Returns \c null if no cache data was found.
    */
    static function contextCache( $contextName, $locale = null )
    {
        $translationCache = eZTranslationCache::cacheTable( $locale );
        if ( isset( $translationCache[$contextName] ) )
        {
            return $translationCache[$contextName]['root'];
        }
        return null;
    }

    /*!
     \static
     Sets the translation context \a $context to be cached with the cache key $contextName.
     \note Trying to overwrite and existing cache key will give a warning and fail.
    */
    static function setContextCache( $contextName, $context, $locale = null )
    {
        if ( $context === null )
        {
            return;
        }
        $locale = eZTranslationCache::cacheLocale( $locale );
        if ( isset( $GLOBALS['eZTranslationCacheTable'][$locale][$contextName] ) )
        {
            eZDebug::writeWarning( "Translation cache for context '$contextName' already exists", __METHOD__ );
        }
        else
        {
            $GLOBALS['eZTranslationCacheTable'][$locale][$contextName] = array();
        }
        $GLOBALS['eZTranslationCacheTable'][$locale][$contextName]['root'] = $context;
        $GLOBALS['eZTranslationCacheTable'][$locale][$contextName]['info'] = array( 'context' => $contextName );
    }

    /*!
     \static
     \return the cache directory for the translation cache files of locale \a $locale.
    */
    static function cacheDirectory( $locale = null )
    {
        $locale = eZTranslationCache::cacheLocale( $locale );
        $cacheDirectory =& $GLOBALS['eZTranslationCacheDirectory'][$locale];
        if ( !isset( $cacheDirectory ) )
        {
            $rootCacheDirectory = eZTranslationCache::rootCacheDirectory();
            $cacheDirectory = eZDir::path( array( $rootCacheDirectory, $locale ) );

        }
        return $cacheDirectory;
    }

    /*!
     \static
    */
    static function rootCacheDirectory()
    {
        $internalCharset = eZTextCodec::internalCharset();

        $ini = eZINI::instance();
        $translationRepository = $ini->variable( 'RegionalSettings', 'TranslationRepository' );
        $translationExtensions = $ini->variable( 'RegionalSettings', 'TranslationExtensions' );

        $uniqueParts = array( $internalCharset, $translationRepository, implode( ';', $translationExtensions ) );
        // the fallbacks decide which files a locale's cache is built from: other fallbacks, another cache
        if ( class_exists( 'expI18nFallbackResolver' ) )
            $uniqueParts[] = expI18nFallbackResolver::instance()->signature();

        $sharedTsCacheDir = $ini->hasVariable( 'RegionalSettings', 'SharedTranslationCacheDir' ) ?
                            trim( $ini->variable( 'RegionalSettings', 'SharedTranslationCacheDir' ) ) :
                            '';
        if ( $sharedTsCacheDir !== '')
        {
            $rootCacheDirectory = eZDir::path( array( $sharedTsCacheDir, md5( implode( '-', $uniqueParts ) ) ) );
        }
        else
        {
            $rootCacheDirectory = eZDir::path( array( eZSys::cacheDirectory(), 'translation', md5( implode( '-', $uniqueParts ) ) ) );
        }
        return $rootCacheDirectory;
    }

    /*!
     \static
     \return true if the cache with the key \a $key can be restored.
             A cache file is found restorable when it exists and has a timestamp
             higher or equal to \a $timestamp.
    */
    static function canRestoreCache( $key, $timestamp, $locale = null )
    {
        $translationCache = eZTranslationCache::cacheTable( $locale );
        if ( isset( $translationCache[$key] ) )
        {
            return false;
        }
//         $internalCharset = eZTextCodec::internalCharset();
//         $cacheFileKey = "$key-$internalCharset";
        $cacheFileKey = $key;
        $cacheFileName = md5( $cacheFileKey ) . '.php';

        $php = new eZPHPCreator( eZTranslationCache::cacheDirectory( $locale ), $cacheFileName );
        return $php->canRestore( $timestamp );
    }

    /*!
     \static
     Loads the cache with the key \a $key from a file and sets the result in the cache table.
     \return true if the cache was successfully restored.
    */
    static function restoreCache( $key, $locale = null )
    {
        $translationCache = eZTranslationCache::cacheTable( $locale );
        if ( isset( $translationCache[$key] ) )
        {
            eZDebug::writeWarning( "Translation cache for key '$key' already exist, cannot restore cache", __METHOD__ );
            return false;
        }
//         $internalCharset = eZTextCodec::internalCharset();
//         $cacheFileKey = "$key-$internalCharset";
        $cacheFileKey = $key;
        $cacheFileName = md5( $cacheFileKey ) . '.php';

        $php = new eZPHPCreator( eZTranslationCache::cacheDirectory( $locale ), $cacheFileName );
        $variables = $php->restore( array( 'info' => 'TranslationInfo',
                                           'root' => 'TranslationRoot',
                                           'cache-date' => 'eZTranslationCacheCodeDate' ) );
        if ( !isset($variables['cache-date']) || $variables['cache-date'] != self::CODE_DATE )
            return false;
        eZTranslationCache::setContextCache( $key, $variables['root'], $locale );
        return true;
    }

    /*!
     \static
     Stores the data of the cache with the key \a $key to a file.
     \return false if the cache does not exist.
    */
    static function storeCache( $key, $locale = null )
    {
        $translationCache = eZTranslationCache::cacheTable( $locale );
        if ( !isset( $translationCache[$key] ) )
        {
            eZDebug::writeWarning( "Translation cache for key '$key' does not exist, cannot store cache", __METHOD__ );
            return;
        }
        $internalCharset = eZTextCodec::internalCharset();
//         $cacheFileKey = "$key-$internalCharset";
        $cacheFileKey = $key;
        $cacheFileName = md5( $cacheFileKey ) . '.php';

        $cache =& $translationCache[$key];

        if ( !file_exists( eZTranslationCache::cacheDirectory( $locale ) ) )
        {
            eZDir::mkdir( eZTranslationCache::cacheDirectory( $locale ), false, true );
        }
        $php = new eZPHPCreator( eZTranslationCache::cacheDirectory( $locale ), $cacheFileName );
        $php->addRawVariable( 'eZTranslationCacheCodeDate', self::CODE_DATE );
        $php->addSpace();
        $php->addRawVariable( 'CacheInfo', array( 'charset' => $internalCharset ) );
        $php->addRawVariable( 'TranslationInfo', $cache['info'] );
        $php->addSpace();
        $php->addRawVariable( 'TranslationRoot', $cache['root'] );
        $php->store( true );
    }

    /*!
     \static
     Reset values strored in $GLOABLS variable
    */
    static function resetGlobals()
    {
        unset( $GLOBALS['eZTranslationCacheDirectory'] );
        unset( $GLOBALS['eZTranslationCacheTable'] );
    }
}

?>
