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

    // What the console writes itself, translated; %name is replaced by tr().
    var T = {ldelim}
        noBroken: '{'No broken links were found.'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        headLinkPage: '{'%links broken link on %pages page'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        headLinkPages: '{'%links broken link on %pages pages'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        headLinksPage: '{'%links broken links on %pages page'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        headLinksPages: '{'%links broken links on %pages pages'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        hint: '{'Open each page in the right hand column, correct the link, then run this again.'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        colLink: '{'Broken link'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        colStatus: '{'Status'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        colFrom: '{'Linked from'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        noResponse: '{'no response'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        startPage: '{'a starting page; nothing on the site links to it'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        more: '{'... and %count more'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        running: '{'running…'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        finished: '{'finished'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        stopped: '{'stopped'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        noStream: '{'Could not open the stream. Check that you have the setup/preload policy.'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        closed: '{'Stream closed.'|i18n( 'design/admin/setup/preload' )|wash( javascript )}',
        byOperator: '{'Stopped by operator.'|i18n( 'design/admin/setup/preload' )|wash( javascript )}'
    {rdelim};
    function tr( name, values )
    {ldelim}
        var s = T[name], key;
        for ( key in values || {ldelim}{rdelim} ) s = s.split( '%' + key ).join( values[key] );
        return s;
    {rdelim}

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
            okEl.appendChild( document.createTextNode( tr( 'noBroken' ) ) );
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
            tr( 'head' + ( broken.length === 1 ? 'Link' : 'Links' ) + ( pageCount === 1 ? 'Page' : 'Pages' ),
                {ldelim} links: broken.length, pages: pageCount {rdelim} ) ) );
        brokenEl.appendChild( head );

        var hint = document.createElement( 'p' );
        hint.appendChild( document.createTextNode( tr( 'hint' ) ) );
        brokenEl.appendChild( hint );

        var table = document.createElement( 'table' );
        table.className = 'list';
        table.setAttribute( 'cellspacing', '0' );
        table.style.width = '100%';

        var hr = document.createElement( 'tr' );
        [ tr( 'colLink' ), tr( 'colStatus' ), tr( 'colFrom' ) ].forEach( function ( label )
        {ldelim}
            var th = document.createElement( 'th' );
            th.appendChild( document.createTextNode( label ) );
            hr.appendChild( th );
        {rdelim} );
        table.appendChild( hr );

        for ( i = 0; i < broken.length; i++ )
        {ldelim}
            var entry = broken[i];
            var row = document.createElement( 'tr' );
            row.className = ( i % 2 ) ? 'bgdark' : 'bglight';

            var td = document.createElement( 'td' );
            td.style.wordBreak = 'break-all';
            td.appendChild( link( entry.url ) );
            row.appendChild( td );

            textCell( tr, entry.status ? String( entry.status ) : tr( 'noResponse' ), true );

            var from = document.createElement( 'td' );
            from.style.wordBreak = 'break-all';
            var list = entry.referrers || [];

            if ( !list.length )
            {ldelim}
                from.appendChild( document.createTextNode( tr( 'startPage' ) ) );
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
                        tr( 'more', {ldelim} count: entry.more {rdelim} ) ) );
            {rdelim}

            row.appendChild( from );
            table.appendChild( row );
        {rdelim}

        brokenEl.appendChild( table );
    {rdelim}

    function finish( message )
    {ldelim}
        if ( source ) {ldelim} source.close(); source = null; {rdelim}
        if ( timer ) {ldelim} window.clearTimeout( timer ); timer = null; {rdelim}
        jobId = null;
        startBtn.disabled = false;
        stopBtn.disabled  = true;
        statusEl.innerHTML = '';
        statusEl.appendChild( document.createTextNode( message ) );
    {rdelim}

    // The run happens in the background (setup/preloadjob starts it); this
    // page polls its events once a second, so no request stays open and no
    // web server or proxy in between can hold the progress back.
    var jobUrl  = {'setup/preloadjob'|ezurl()};
    var jobId   = null;
    var offset  = 0;
    var timer   = null;
    var csrfMeta = document.querySelector( 'meta[name="csrf-token"]' );

    function post( data, done )
    {ldelim}
        var request = new XMLHttpRequest();
        request.open( 'POST', jobUrl, true );
        request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
        request.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        if ( csrfMeta ) request.setRequestHeader( 'X-CSRF-Token', csrfMeta.getAttribute( 'content' ) );
        request.onload = function ()
        {ldelim}
            var answer = null;
            try {ldelim} answer = JSON.parse( request.responseText ); {rdelim} catch ( e ) {ldelim}{rdelim}
            done( request.status, answer );
        {rdelim};
        request.onerror = function () {ldelim} done( 0, null ); {rdelim};
        var pairs = [];
        for ( var key in data ) pairs.push( encodeURIComponent( key ) + '=' + encodeURIComponent( data[key] ) );
        request.send( pairs.join( '&' ) );
    {rdelim}

    function show( payload )
    {ldelim}
        if ( payload.type === 'report' )
        {ldelim}
            renderBroken( payload.broken );
            // The console keeps the plain text of the report as well, so
            // that selecting the whole log still carries it.
            write( 'report', payload.message );
            return;
        {rdelim}
        write( payload.type, payload.message );
    {rdelim}

    function poll()
    {ldelim}
        if ( !jobId ) return;
        var request = new XMLHttpRequest();
        request.open( 'GET', jobUrl + '/' + jobId + '/' + offset, true );
        request.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        request.onload = function ()
        {ldelim}
            var answer = null;
            try {ldelim} answer = JSON.parse( request.responseText ); {rdelim} catch ( e ) {ldelim}{rdelim}
            if ( !answer || !answer.events )
            {ldelim}
                write( 'error', tr( 'noStream' ) );
                finish( tr( 'stopped' ) );
                return;
            {rdelim}
            // One event the page cannot show must not stop the console.
            for ( var i = 0; i < answer.events.length; i++ )
            {ldelim}
                try {ldelim} show( answer.events[i] ); {rdelim}
                catch ( e ) {ldelim} write( 'error', String( e ) ); {rdelim}
            {rdelim}
            offset = answer.offset;
            if ( answer.done )
                finish( tr( 'finished' ) );
            else
                timer = window.setTimeout( poll, 1000 );
        {rdelim};
        request.onerror = function () {ldelim} timer = window.setTimeout( poll, 3000 ); {rdelim};
        request.send();
    {rdelim}

    startBtn.onclick = function ()
    {ldelim}
        if ( jobId ) return;
        consoleEl.innerHTML = '';
        brokenEl.innerHTML = '';
        lines = 0;

        var pages = parseInt( document.getElementById( 'preload-max-pages' ).value, 10 );
        var depth = parseInt( document.getElementById( 'preload-max-depth' ).value, 10 );
        if ( isNaN( pages ) || pages < 1 ) pages = 250;
        if ( isNaN( depth ) || depth < 0 ) depth = 3;

        var saEl = document.getElementById( 'preload-siteaccess' );
        var sa = saEl ? saEl.value : '';

        startBtn.disabled = true;
        stopBtn.disabled  = false;
        statusEl.innerHTML = '';
        statusEl.appendChild( document.createTextNode( tr( 'running' ) ) );

        post( {ldelim} Action: 'start', MaxPages: pages, MaxDepth: depth, SiteAccess: sa {rdelim}, function ( status, answer )
        {ldelim}
            if ( status !== 200 || !answer || !answer.id )
            {ldelim}
                write( 'error', ( answer && answer.error ) ? answer.error : tr( 'noStream' ) );
                finish( tr( 'stopped' ) );
                return;
            {rdelim}
            jobId = answer.id;
            offset = 0;
            poll();
        {rdelim} );
    {rdelim};

    stopBtn.onclick = function () {ldelim}
        if ( !jobId ) return;
        stopBtn.disabled = true;
        // The run ends after the page it is fetching and says so; polling
        // carries on until then.
        post( {ldelim} Action: 'stop', JobID: jobId {rdelim}, function () {ldelim}
            write( 'warn', tr( 'byOperator' ) );
        {rdelim} );
    {rdelim};
{rdelim})();
</script>

{/if}

</div>
</div></div></div>
</div>
