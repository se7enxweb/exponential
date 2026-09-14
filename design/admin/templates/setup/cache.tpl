{* Feedbacks. *}
{if $cache_cleared.content}
    <div class="message-feedback">
        <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Content view cache was cleared'|i18n( 'design/admin/setup/cache' )}</h2>
    </div>
{/if}

{if $cache_cleared.all}
    <div class="message-feedback">
        <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'All caches were cleared'|i18n( 'design/admin/setup/cache' )}</h2>
    </div>
{/if}

{if $cache_cleared.ini}
    <div class="message-feedback">
        <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Ini file cache was cleared'|i18n( 'design/admin/setup/cache' )}</h2>
    </div>
{/if}

{if $cache_cleared.template}
    <div class="message-feedback">
        <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Template cache was cleared'|i18n( 'design/admin/setup/cache' )}</h2>
    </div>
{/if}

{* cache_cleared.static is the number of pages written, not a flag: a run that
   wrote nothing must not report success. *}
{if $cache_cleared.static}
    <div class="message-feedback">
        <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Static content cache was regenerated, %count pages written to %dir'|i18n( 'design/admin/setup/cache',, hash( '%count', $cache_cleared.static, '%dir', $static_cache_storage_dir ) )}</h2>
    </div>
{/if}

{section show=$cache_cleared.list}
    <div class="message-feedback">
        <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'The following caches were cleared'|i18n( 'design/admin/setup/cache' )}:</h2>
        <ul>
        {section var=Caches loop=$cache_cleared.list}
            <li>{'%name was cleared'|i18n( 'design/admin/setup/cache',, hash( '%name', $Caches.item.name ) )}</li>
        {/section}
        </ul>
    </div>
{/section}




<form name="clearcacheform" method="post" action={"/setup/cache/"|ezurl}>

{* Clear caches window. *}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{'Clear caches'|i18n( 'design/admin/setup/cache' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

<table class="list cache" cellspacing="0">

<tr>
    <th width="61%">{'Categories'|i18n( 'design/admin/setup/cache' )}</th>
    <th width="39%"></th>
</tr>

{* Template cache. *}
<tr class="bglight">
<td>{'Template overrides and compiled templates'|i18n( 'design/admin/setup/cache' )}:</td>
<td><input class="button" type="submit" name="ClearTemplateCacheButton" value="{'Clear template caches'|i18n( 'design/admin/setup/cache' )}" title="{'This operation will clear all the template override caches and the compiled templates. It may lead to slower site performance until the caches are recreated.'|i18n( 'design/admin/setup/cache' )}" /></td>
</tr>

{* Content cache. *}
<tr class="bgdark">
<td>{'Content views and template blocks'|i18n( 'design/admin/setup/cache' )}:</td>
<td><input class="button" type="submit" name="ClearContentCacheButton" value="{'Clear content caches'|i18n( 'design/admin/setup/cache' )}" title="{'This operation will clear all caches that are related to either template views or cache blocks inside the pagelayout template. Use it if you have modified templates or if you have made changes inside a cache block.'|i18n( 'design/admin/setup/cache' )}"/></td>
</tr>

{* Configuration cache. *}
<tr class="bglight">
<td>{'Configuration (ini) caches'|i18n( 'design/admin/setup/cache' )}:</td>
<td><input class="button" type="submit" name="ClearINICacheButton" value="{'Clear Ini caches'|i18n( 'design/admin/setup/cache' )}" title="{'This operation will clear all the configuration caches. Use it to force the system to re-read the configuration files if you have changed settings.'|i18n( 'design/admin/setup/cache' )}" /></td>
</tr>

{* All caches. *}
<tr class="bgdark">
<td>{'Everything'|i18n( 'design/admin/setup/cache' )}:</td>
<td><input class="button" type="submit" name="ClearAllCacheButton" value="{'Clear all caches'|i18n( 'design/admin/setup/cache' )}" title="{'This operation will clear all the caches and may lead to slow site response times until the caches are recreated.'|i18n( 'design/admin/setup/cache' )}" /></td>
</tr>

</table>

{* DESIGN: Content END *}</div></div></div>

</div>




{* Cache overview window. *}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h2 class="context-title">{'Fine-grained cache control'|i18n( 'design/admin/setup/cache' )}</h2>



{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} width="16" height="16" alt="{'Invert selection.'|i18n( 'design/admin/setup/cache' )}" onclick="ezjs_toggleCheckboxes( document.clearcacheform, 'CacheList[]' ); return false;" title="{'Invert selection.'|i18n( 'design/admin/setup/cache' )}" /></th>
    <th>{'Name'|i18n( 'design/admin/setup/cache' )}</th>
    <th>{'Path'|i18n( 'design/admin/setup/cache' )}</th>
</tr>
{section var=Caches loop=$cache_list sequence=array( bglight, bgdark )}

{* Checkbox *}
<tr class="{$Caches.sequence}">
{if $cache_enabled.list[$Caches.item.id]}
<td><input type="checkbox" name="CacheList[]" value="{$Caches.item.id}" title="{'Select the <%cache_name> for clearing.'|i18n( 'design/admin/setup/cache',, hash( '%cache_name', $Caches.item.name ) )|wash}" /></td>
{else}
<td><input type="checkbox" name="CacheList[]" value="{$Caches.item.id}" disabled="disabled" title="{'The <%cache_name> is disabled and thus it cannot be marked for clearing.'|i18n( 'design/admin/setup/cache',, hash( '%cache_name', $Caches.item.name ) )|wash}" /></td>
{/if}

{* Name *}
<td>{$Caches.item.name}&nbsp;</td>

{* Path *}
<td>{$Caches.item.path}&nbsp;</td>

</tr>
{/section}
</table>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
<input class="button" type="submit" name="ClearCacheButton" value="{'Clear selected'|i18n( 'design/admin/setup/cache' )}" title="{'Clear the selected caches.'|i18n( 'design/admin/setup/cache' )}" />
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>


{* Regenerate static cache window. *}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h2 class="context-title">{'Static content cache'|i18n( 'design/admin/setup/cache' )}</h2>



{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

{if $static_cache_siteaccess_list|count|eq(0)}
<div class="warning" style="padding:1em;border:2px solid #c00;margin:0 0 1em 0;">
    <b>{'No site can be cached.'|i18n( 'design/admin/setup/cache' )}</b><br />
    {'Every siteaccess either requires a login or has no SiteSettings/SiteURL, so there is no page to fetch and store.'|i18n( 'design/admin/setup/cache' )}
</div>
{else}

<table class="list cache" cellspacing="0">
<tr>
    <th width="61%">{'Categories'|i18n( 'design/admin/setup/cache' )}</th>
    <th width="39%"></th>
</tr>

<tr class="bglight">
<td>{'Pages are written to'|i18n( 'design/admin/setup/cache' )}:</td>
<td><code>{$static_cache_storage_dir|wash}</code></td>
</tr>

{* Which site to generate is chosen, not inferred: one installation serves
   several, each with its own host or url prefix, and generating all of them is
   rarely what is wanted. *}
<tr class="bgdark">
<td>{'Site to generate'|i18n( 'design/admin/setup/cache' )}:</td>
<td>
    <select id="staticcache-siteaccess" name="StaticCacheSiteAccess">
    {foreach $static_cache_siteaccess_list as $static_cache_target}
        <option value="{$static_cache_target.name|wash}">{$static_cache_target.name|wash} &mdash; {$static_cache_target.url|wash}</option>
    {/foreach}
        <option value="">{'All sites'|i18n( 'design/admin/setup/cache' )}</option>
    </select>
</td>
</tr>

{* The crawl is bounded, so a large site cannot run away and a first pass can
   be kept short deliberately. *}
<tr class="bglight">
<td>{'Limits'|i18n( 'design/admin/setup/cache' )}:</td>
<td>
    <label for="staticcache-max-pages">{'Pages'|i18n( 'design/admin/setup/cache' )}</label>
    <input id="staticcache-max-pages" type="text" size="6" value="2500" />
    &nbsp;
    <label for="staticcache-max-depth">{'Link depth'|i18n( 'design/admin/setup/cache' )}</label>
    <input id="staticcache-max-depth" type="text" size="4" value="12" />
</td>
</tr>

{* Static content cache. *}
<tr class="bgdark">
<td width="60%">{'Regenerate static content cache'|i18n( 'design/admin/setup/cache' )}:</td>
<td width="40%"><input class="button" id="staticcache-start" type="submit" name="RegenerateStaticCacheButton" value="{'Create new'|i18n( 'design/admin/setup/cache' )}" title="{'Fetches every url of the chosen site that the site itself links to and stores the page, so the web server can answer the next visitor from a file instead of starting the CMS. This can take some time on a large site. If you encounter time-out problems, use the &quot;bin/php/makestaticcache.php&quot; shell script.'|i18n( 'design/admin/setup/cache' )}" />
<input class="button" id="staticcache-stop" type="button" disabled="disabled" value="{'Stop'|i18n( 'design/admin/setup/cache' )}" title="{'Stops listening and leaves the pages written so far in place.'|i18n( 'design/admin/setup/cache' )}" />
<span id="staticcache-status" style="margin-left:1em;"></span></td>
</tr>

</table>

{if $static_cache_enabled|not}
<div class="warning" style="padding:.8em;border:1px solid #e8c765;margin:1em 0;">
    {'Generated pages will not be refreshed when an editor publishes, because site.ini [ContentSettings] StaticCache is not enabled.'|i18n( 'design/admin/setup/cache' )}
</div>
{/if}

{* A fixed height, scrolling region: a full site prints hundreds of lines and
   should not push the rest of the interface off the screen. *}
<pre id="staticcache-console" style="background:#1b1b1b;color:#d8d8d8;padding:.8em;
     height:20em;overflow:auto;font:12px/1.5 monospace;border:1px solid #444;
     margin:1em 0 0 0;white-space:pre-wrap;word-break:break-word;display:none;"></pre>

<script type="text/javascript">
(function () {ldelim}
    var streamUrl = {$static_cache_stream_url|ezurl()};
    var consoleEl = document.getElementById( 'staticcache-console' );
    var startBtn  = document.getElementById( 'staticcache-start' );
    var stopBtn   = document.getElementById( 'staticcache-stop' );
    var statusEl  = document.getElementById( 'staticcache-status' );
    var saEl      = document.getElementById( 'staticcache-siteaccess' );
    var source    = null;
    var lines     = 0;

    if ( !startBtn || typeof window.EventSource === 'undefined' )
        return; {* No EventSource: the button stays an ordinary form submit. *}

    var colours = {ldelim}
        phase:        '#7fd1ff',
        'phase-item': '#ffffff',
        ok:           '#9bdc7a',
        warn:         '#e8c765',
        error:        '#ff8a80',
        info:         '#9a9a9a',
        done:         '#7fd1ff'
    {rdelim};

    function write( type, text )
    {ldelim}
        var atBottom = consoleEl.scrollTop + consoleEl.clientHeight >= consoleEl.scrollHeight - 8;
        var line = document.createElement( 'div' );
        line.style.color = colours[type] || '#d8d8d8';
        if ( type === 'phase' || type === 'done' )
            line.style.fontWeight = 'bold';
        line.appendChild( document.createTextNode( text ) );
        consoleEl.appendChild( line );
        lines++;
        {* Only follow the tail while the operator is already at the bottom. *}
        if ( atBottom )
            consoleEl.scrollTop = consoleEl.scrollHeight;
    {rdelim}

    function finish( message )
    {ldelim}
        if ( source ) {ldelim} source.close(); source = null; {rdelim}
        startBtn.disabled = false;
        stopBtn.disabled  = true;
        statusEl.innerHTML = '';
        statusEl.appendChild( document.createTextNode( message ) );
    {rdelim}

    {* Run it in the page instead of posting the form, so every page appears as
       it is written rather than after one long silent request. *}
    startBtn.onclick = function ( event )
    {ldelim}
        if ( event && event.preventDefault ) event.preventDefault();
        if ( source ) return false;

        consoleEl.style.display = 'block';
        consoleEl.innerHTML = '';
        lines = 0;

        var pages = parseInt( document.getElementById( 'staticcache-max-pages' ).value, 10 );
        var depth = parseInt( document.getElementById( 'staticcache-max-depth' ).value, 10 );
        if ( isNaN( pages ) || pages < 1 ) pages = 2500;
        if ( isNaN( depth ) || depth < 0 ) depth = 12;

        var url = streamUrl + '?SiteAccess=' + encodeURIComponent( saEl ? saEl.value : '' )
                + '&MaxPages=' + pages + '&MaxDepth=' + depth;

        startBtn.disabled = true;
        stopBtn.disabled  = false;
        statusEl.innerHTML = '';
        statusEl.appendChild( document.createTextNode( 'running…' ) );

        source = new EventSource( url );

        source.onmessage = function ( messageEvent )
        {ldelim}
            var payload;
            try {ldelim} payload = JSON.parse( messageEvent.data ); {rdelim}
            catch ( e ) {ldelim} return; {rdelim}
            write( payload.type, payload.message );
            if ( payload.type === 'done' )
                finish( 'finished' );
        {rdelim};

        source.addEventListener( 'end', function () {ldelim} finish( 'finished' ); {rdelim} );

        source.onerror = function ()
        {ldelim}
            {* EventSource reconnects on its own, which would start the run
               again from the beginning; closing here keeps one run to a press. *}
            if ( lines === 0 )
                write( 'error', 'Could not open the stream. Check that you have the setup/managecache policy.' );
            else
                write( 'warn', 'Stream closed.' );
            finish( 'stopped' );
        {rdelim};

        return false;
    {rdelim};

    {* Closing the stream is all the browser can do: the run is already under
       way on the server and the pages it has written stay written. *}
    stopBtn.onclick = function () {ldelim}
        write( 'warn', 'Stopped by operator. Pages written so far are kept.' );
        finish( 'stopped' );
        return false;
    {rdelim};
{rdelim})();
</script>

{/if}

{* DESIGN: Content END *}</div></div></div>

</div>

</form>
