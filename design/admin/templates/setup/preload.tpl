{* Preloader console. The page holds no state: it opens an EventSource against
   setup/preloadstream and renders what arrives. *}
<div class="context-block">

<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h1 class="context-title">{'Preload'|i18n('design/admin/setup/preload')}</h1>
<div class="header-mainline"></div>
</div></div></div></div></div></div>

<div class="box-ml"><div class="box-mr"><div class="box-content">
<div class="context-attributes">

{if $base_url|eq('')}
<div class="warning" style="padding:1em;border:2px solid #c00;margin:1em 0;">
    <b>{'Cannot determine the site url.'|i18n('design/admin/setup/preload')}</b><br />
    {'SiteSettings/SiteURL is not set in site.ini, so there is nothing to warm.'|i18n('design/admin/setup/preload')}
</div>
{else}

<p>
    {'Requests every page of the site so the caches are warm before a visitor arrives. Start with the section pages, then follow links outwards.'|i18n('design/admin/setup/preload')}
</p>

<table class="list" cellspacing="0" style="margin-bottom:1em;">
    <tr><th>{'Site'|i18n('design/admin/setup/preload')}</th>
        <th>{'Starting pages'|i18n('design/admin/setup/preload')}</th></tr>
    <tr class="bglight">
        <td style="white-space:nowrap;">{$base_url|wash}</td>
        <td>{foreach $start_urls as $preload_url}{$preload_url|wash}{delimiter}<br />{/delimiter}{/foreach}</td>
    </tr>
</table>

{* Which site to warm is chosen, not inferred: this view is served from the
   administration siteaccess, whose own SiteURL is the administration host. *}
<div class="block">
    <label for="preload-siteaccess">{'Site to warm'|i18n('design/admin/setup/preload')}</label>
    <select id="preload-siteaccess">
    {foreach $targets as $preload_target}
        <option value="{$preload_target.name|wash}"{if eq($preload_target.name,$selected_siteaccess)} selected="selected"{/if}>{$preload_target.name|wash} &mdash; {$preload_target.url|wash}</option>
    {/foreach}
    </select>
</div>

<div class="block">
    <label for="preload-max-pages">{'Page limit'|i18n('design/admin/setup/preload')}</label>
    <input id="preload-max-pages" type="text" size="6" value="{$default_max_pages}" />
    &nbsp;
    <label for="preload-max-depth">{'Link depth'|i18n('design/admin/setup/preload')}</label>
    <input id="preload-max-depth" type="text" size="4" value="{$default_max_depth}" />
</div>

<div class="block">
    <input id="preload-start" class="button" type="button" value="{'Start preloading'|i18n('design/admin/setup/preload')}" />
    <input id="preload-stop" class="button" type="button" disabled="disabled" value="{'Stop'|i18n('design/admin/setup/preload')}" />
    <span id="preload-status" style="margin-left:1em;"></span>
</div>

{* A fixed height, scrolling region: a full run prints hundreds of lines and
   should not push the rest of the interface off the screen. *}
<pre id="preload-console" style="background:#1b1b1b;color:#d8d8d8;padding:.8em;
     height:26em;overflow:auto;font:12px/1.5 monospace;border:1px solid #444;
     margin:0;white-space:pre-wrap;word-break:break-word;">{'Idle. Press Start preloading to begin.'|i18n('design/admin/setup/preload')}</pre>

{* The console is a log and a broken link scrolls past in the middle of it.
   This is the same information the other way round: each missing page once,
   and under it the pages an editor has to open to repair it. *}
<div id="preload-broken" style="margin-top:1em;"></div>

<script type="text/javascript">
(function () {ldelim}
    var streamUrl = {$stream_url|ezurl()};
    var consoleEl = document.getElementById( 'preload-console' );
    var startBtn  = document.getElementById( 'preload-start' );
    var stopBtn   = document.getElementById( 'preload-stop' );
    var statusEl  = document.getElementById( 'preload-status' );
    var source    = null;
    var lines     = 0;

    var colours = {ldelim}
        phase:        '#7fd1ff',
        'phase-item': '#ffffff',
        ok:           '#9bdc7a',
        warn:         '#e8c765',
        error:        '#ff8a80',
        info:         '#9a9a9a',
        report:       '#ffd9d9',
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
        // Only follow the tail while the operator is already at the bottom, so
        // scrolling back to read something is not yanked away on the next line.
        if ( atBottom )
            consoleEl.scrollTop = consoleEl.scrollHeight;
    {rdelim}

    var brokenEl = document.getElementById( 'preload-broken' );

    function textCell( row, text, nowrap )
    {ldelim}
        var td = document.createElement( 'td' );
        if ( nowrap ) td.style.whiteSpace = 'nowrap';
        td.appendChild( document.createTextNode( text ) );
        row.appendChild( td );
        return td;
    {rdelim}

    // Only ever an address this installation's own crawl produced, and only
    // ever as an http address: anything else goes in as text.
    function link( url )
    {ldelim}
        if ( !/^https?:\/\//i.test( url ) )
            return document.createTextNode( url );
        var a = document.createElement( 'a' );
        a.setAttribute( 'href', url );
        a.setAttribute( 'target', '_blank' );
        a.setAttribute( 'rel', 'noopener' );
        a.appendChild( document.createTextNode( url ) );
        return a;
    {rdelim}

    function renderBroken( broken )
    {ldelim}
        brokenEl.innerHTML = '';
        if ( !broken || !broken.length )
        {ldelim}
            var okEl = document.createElement( 'p' );
            okEl.appendChild( document.createTextNode(
                'No broken links were found.' ) );
            brokenEl.appendChild( okEl );
            return;
        {rdelim}

        var pages = {ldelim}{rdelim}, pageCount = 0, i, j;
        for ( i = 0; i < broken.length; i++ )
            for ( j = 0; j < ( broken[i].referrers || [] ).length; j++ )
                if ( !pages[ broken[i].referrers[j] ] )
                {ldelim} pages[ broken[i].referrers[j] ] = true; pageCount++; {rdelim}

        var head = document.createElement( 'h2' );
        head.appendChild( document.createTextNode(
            broken.length + ( broken.length === 1 ? ' broken link' : ' broken links' )
            + ' on ' + pageCount + ( pageCount === 1 ? ' page' : ' pages' ) ) );
        brokenEl.appendChild( head );

        var hint = document.createElement( 'p' );
        hint.appendChild( document.createTextNode(
            'Open each page in the right hand column, correct the link, then run this again.' ) );
        brokenEl.appendChild( hint );

        var table = document.createElement( 'table' );
        table.className = 'list';
        table.setAttribute( 'cellspacing', '0' );
        table.style.width = '100%';

        var hr = document.createElement( 'tr' );
        [ 'Broken link', 'Status', 'Linked from' ].forEach( function ( label )
        {ldelim}
            var th = document.createElement( 'th' );
            th.appendChild( document.createTextNode( label ) );
            hr.appendChild( th );
        {rdelim} );
        table.appendChild( hr );

        for ( i = 0; i < broken.length; i++ )
        {ldelim}
            var entry = broken[i];
            var tr = document.createElement( 'tr' );
            tr.className = ( i % 2 ) ? 'bgdark' : 'bglight';

            var td = document.createElement( 'td' );
            td.style.wordBreak = 'break-all';
            td.appendChild( link( entry.url ) );
            tr.appendChild( td );

            textCell( tr, entry.status ? String( entry.status ) : 'no response', true );

            var from = document.createElement( 'td' );
            from.style.wordBreak = 'break-all';
            var list = entry.referrers || [];

            if ( !list.length )
            {ldelim}
                from.appendChild( document.createTextNode(
                    'a starting page; nothing on the site links to it' ) );
            {rdelim}
            else
            {ldelim}
                for ( j = 0; j < list.length; j++ )
                {ldelim}
                    from.appendChild( link( list[j] ) );
                    from.appendChild( document.createElement( 'br' ) );
                {rdelim}
                if ( entry.more > 0 )
                    from.appendChild( document.createTextNode(
                        '... and ' + entry.more + ' more' ) );
            {rdelim}

            tr.appendChild( from );
            table.appendChild( tr );
        {rdelim}

        brokenEl.appendChild( table );
    {rdelim}

    function finish( message )
    {ldelim}
        if ( source ) {ldelim} source.close(); source = null; {rdelim}
        startBtn.disabled = false;
        stopBtn.disabled  = true;
        statusEl.innerHTML = '';
        statusEl.appendChild( document.createTextNode( message ) );
    {rdelim}

    startBtn.onclick = function ()
    {ldelim}
        if ( source ) return;
        consoleEl.innerHTML = '';
        brokenEl.innerHTML = '';
        lines = 0;

        var pages = parseInt( document.getElementById( 'preload-max-pages' ).value, 10 );
        var depth = parseInt( document.getElementById( 'preload-max-depth' ).value, 10 );
        if ( isNaN( pages ) || pages < 1 ) pages = 250;
        if ( isNaN( depth ) || depth < 0 ) depth = 3;

        var saEl = document.getElementById( 'preload-siteaccess' );
        var sa = saEl ? saEl.value : '';

        var url = streamUrl + '?MaxPages=' + pages + '&MaxDepth=' + depth
                + '&SiteAccess=' + encodeURIComponent( sa );

        startBtn.disabled = true;
        stopBtn.disabled  = false;
        statusEl.innerHTML = '';
        statusEl.appendChild( document.createTextNode( 'running…' ) );

        source = new EventSource( url );

        source.onmessage = function ( event )
        {ldelim}
            var payload;
            try {ldelim} payload = JSON.parse( event.data ); {rdelim}
            catch ( e ) {ldelim} return; {rdelim}
            if ( payload.type === 'report' )
            {ldelim}
                renderBroken( payload.broken );
                // The console keeps the plain text of the report as well, so
                // that selecting the whole log still carries it.
                write( 'report', payload.message );
                return;
            {rdelim}

            write( payload.type, payload.message );
            if ( payload.type === 'done' )
                finish( 'finished' );
        {rdelim};

        source.addEventListener( 'end', function () {ldelim} finish( 'finished' ); {rdelim} );

        source.onerror = function ()
        {ldelim}
            // EventSource reconnects on its own, which would start the run
            // again from the beginning; closing here keeps one run to a press.
            if ( lines === 0 )
                write( 'error', 'Could not open the stream. Check that you have the setup/preload policy.' );
            else
                write( 'warn', 'Stream closed.' );
            finish( 'stopped' );
        {rdelim};
    {rdelim};

    stopBtn.onclick = function () {ldelim}
        write( 'warn', 'Stopped by operator.' );
        finish( 'stopped' );
    {rdelim};
{rdelim})();
</script>

{/if}

</div>
</div></div></div>
</div>
