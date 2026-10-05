<div class="block debug-toolbar">

<div class="element">
<h3>{'Clear cache:'|i18n( 'design/standard/setup' )}</h3>
{include uri='design:setup/clear_cache.tpl' form_id='debug-clearcache'}
</div>

<div class="element">
<h3>{'Quick settings:'|i18n( 'design/standard/setup' )}</h3>
{let siteaccess=is_set( $access_type )|choose( '', $access_type.name )
     select_siteaccess=false}
{include uri='design:setup/quick_settings.tpl' form_id='debug-quicksettings'}
{/let}
</div>
<div class="break"></div>

</div>