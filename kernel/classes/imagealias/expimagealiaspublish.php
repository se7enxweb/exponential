<?php
/**
 * File containing the expImageAliasPublish class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Pre-generation after publishing (image.ini [AliasSettings] PreGenerate=enabled, on-demand mode): when a version is
 * published, the aliases its images will need are queued (image.alias.generate, the class editorial), so the first
 * visitor finds them made.
 *
 * - Which aliases: the union of AliasList[] over this siteaccess and its related siteaccesses (site.ini
 *   [SiteAccessSettings] RelatedSiteAccessList[]), each read through the INI system of that siteaccess and made as
 *   that siteaccess defines it (expImageAliasSettings::siteAccessAliasPlan()); plus the WebP/AVIF variants the alias
 *   blocks list in Formats[].
 * - Which images: the image attributes of the published version (every language) that have an image and miss at least
 *   one of those aliases (or a variant of one). An edit that kept the image queues nothing: the new version names the
 *   aliases already made.
 * - Old versions follow the kernel's rules: nothing is made for them; their files go with them as before.
 * - No queue (expqueue not active, not installed, or image.alias.generate not configured): nothing is converted in
 *   the publishing request; the decision is logged (imagealias.log event=queue result=no-queue) and the aliases are
 *   made on demand as before.
 *
 * Called by the content/publish operation (eZContentOperationCollection::publishNode()) in every process; it never
 * stops a publication (errors are logged).
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasPublish
{
    /** The formats pre-generation asks for (each alias gets those its Formats[] lists) */
    const FORMATS = array( 'webp', 'avif' );

    /**
     * What pre-generation does after a publication.
     *
     * @param bool $enabled [AliasSettings] PreGenerate
     * @param bool $onDemand the mode of the settings is on-demand
     * @param int $images image attributes of the version that miss an alias
     * @param bool $queueAvailable expqueue takes image.alias.generate
     * @return string disabled, render-mode, nothing, no-queue or queue
     */
    public static function decide( $enabled, $onDemand, $images, $queueAvailable )
    {
        if ( !$enabled )
            return 'disabled';
        if ( !$onDemand )
            return 'render-mode';
        if ( (int)$images <= 0 )
            return 'nothing';
        if ( !$queueAvailable )
            return 'no-queue';
        return 'queue';
    }

    /**
     * The content/publish hook.
     *
     * @param int $objectID
     * @param int $version
     * @return string the decision (see decide()), or failed
     */
    public static function published( $objectID, $version )
    {
        try
        {
            $enabled = expImageAliasSettings::preGenerateEnabled();
            $onDemand = expImageAliasSettings::configuredMode() === expImageAliasSettings::MODE_ON_DEMAND;
            if ( !$enabled || !$onDemand )
                return self::decide( $enabled, $onDemand, 0, false );
            $attributes = eZPersistentObject::fetchObjectList( eZContentObjectAttribute::definition(), null,
                                                               array( 'contentobject_id' => (int)$objectID, 'version' => (int)$version,
                                                                      'data_type_string' => 'ezimage' ) );
            if ( !$attributes )
                return 'nothing';
            $plan = expImageAliasSettings::siteAccessAliasPlan();
            $items = array();
            foreach ( $attributes as $attribute )
            {
                if ( self::needsWork( (string)$attribute->attribute( 'data_text' ), $plan ) )
                    $items[] = array( (int)$attribute->attribute( 'id' ), (int)$attribute->attribute( 'version' ), (string)$attribute->attribute( 'language_code' ) );
            }
            $available = $items ? expImageAliasJob::queueAvailable( expImageAliasJob::TYPE_GENERATE ) : false;
            $decision = self::decide( true, true, count( $items ), $available );
            if ( $decision === 'no-queue' )
            {
                eZDebug::writeNotice( "Pre-generation of the image aliases of object $objectID version $version skipped: no job queue takes image.alias.generate", __METHOD__ );
                expImageAliasLog::write( 'queue', array( 'result' => 'no-queue', 'alias' => '-', 'origin' => 'publish', 'object' => (int)$objectID,
                                                         'version' => (int)$version, 'images' => count( $items ) ) );
            }
            elseif ( $decision === 'queue' )
            {
                $queued = expImageAliasJob::queuePublish( $items, $plan, self::FORMATS, $objectID, $version );
                if ( $queued === null )
                    $decision = 'no-queue';
                expImageAliasLog::write( 'queue', array( 'result' => $queued === null ? 'refused' : ( $queued['duplicate'] ? 'duplicate' : 'queued' ),
                                                         'alias' => '-', 'run' => $queued !== null ? $queued['run'] : '-',
                                                         'job' => $queued !== null ? $queued['job'] : 0, 'origin' => 'publish',
                                                         'object' => (int)$objectID, 'version' => (int)$version, 'images' => count( $items ) ) );
            }
            return $decision;
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( "Pre-generation of the image aliases of object $objectID version $version failed: " . $e->getMessage(), __METHOD__ );
            return 'failed';
        }
    }

    /**
     * Whether an image attribute (its stored XML) misses an alias of $plan, or a WebP/AVIF variant an alias block
     * lists of an alias it has. An attribute without an image needs nothing.
     *
     * @param string $xml the attribute's data_text
     * @param array $plan siteaccess => alias names
     * @return bool
     */
    public static function needsWork( $xml, array $plan )
    {
        $files = expImageAliasStager::filesOfXml( $xml );
        if ( !isset( $files['original'] ) )
            return false;
        // An SVG gets no aliases of its own (every alias is the original)
        if ( strtolower( pathinfo( $files['original'], PATHINFO_EXTENSION ) ) === 'svg' )
            return false;
        foreach ( $plan as $siteAccess => $names )
        {
            foreach ( (array)$names as $name )
            {
                if ( !isset( $files[$name] ) )
                    return true;
            }
        }
        foreach ( $files as $name => $path )
        {
            if ( $name === 'original' )
                continue;
            foreach ( expImageAliasSettings::aliasFormats( $name ) as $format )
            {
                $mime = eZMimeType::findByURL( $path );
                if ( expImageFormatConverter::variantApplies( $mime['name'], $format ) && expImageFormatConverter::canWrite( $format )
                     && !expImageAliasServer::exists( expImageFormatConverter::variantPath( $path, $format ) ) )
                    return true;
            }
        }
        return false;
    }
}
