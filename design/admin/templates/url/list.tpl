{* The link list (url/list/<all|valid|invalid|unchecked>).

   What the list is and how links are checked, an overview of every published link by state, a search over the
   addresses, the order, then one card per link with its state, when it was checked and changed, which objects use
   it, and its View, Edit and Open actions. Ticked links are marked valid or invalid by hand (SetValid, SetInvalid).

   The same file is in design/admin and design/admin4. The figures, the usage and the search come from the view
   (url_summary, url_usage, url_search); without them (an older view class) the cards show what the URL rows hold.
   Everything works without javascript; the script only adds Select all and the selection count.
   Guide: doc/guides/urls-and-aliases.md *}
{include uri='design:url/exp_style.tpl'}

{def $summary = first_set( $url_summary, false() )
     $usage_map = first_set( $url_usage, hash() )
     $search = first_set( $url_search, '' )
     $search_suffix = first_set( $url_search_suffix, '' )
     $sort = first_set( $url_sort, 'address' )
     $page_limit = first_set( $limit, $view_parameters.limit, 10 )
     $sort_part = cond( $sort|ne( 'address' ), concat( '/(sort)/', $sort ), '' )
     $feedback = first_set( $url_feedback, false() )}

<div class="context-block exp-urls">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">
{switch match=$view_mode}
{case match='valid'}{'Valid links (%url_list_count)'|i18n( 'design/admin/url/list',, hash( '%url_list_count', $url_list_count ) )}{/case}
{case match='invalid'}{'Invalid links (%url_list_count)'|i18n( 'design/admin/url/list',, hash( '%url_list_count', $url_list_count ) )}{/case}
{case match='unchecked'}{'Links never checked (%url_list_count)'|i18n( 'design/admin/url/list',, hash( '%url_list_count', $url_list_count ) )}{/case}
{case}{'All links (%url_list_count)'|i18n( 'design/admin/url/list',, hash( '%url_list_count', $url_list_count ) )}{/case}
{/switch}
</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Every address that published content links to, from a URL field or a link in rich text, is registered here once. The link check (the cronjob script linkcheck.php, in the infrequent part) tries each address and marks it valid or invalid; you can also mark links by hand. A link is changed in one place here and every object that uses it shows the new address.'|i18n( 'design/admin/url/list' )}</p>

{if $feedback}
    {if eq( $feedback.type, 'set_valid' )}
<div class="exp-feedback is-ok" role="status">{'%count links were marked valid. The next link check tests them again.'|i18n( 'design/admin/url/list',, hash( '%count', $feedback.count ) )}</div>
    {elseif eq( $feedback.type, 'set_invalid' )}
<div class="exp-feedback is-ok" role="status">{'%count links were marked invalid. The next link check tests them again.'|i18n( 'design/admin/url/list',, hash( '%count', $feedback.count ) )}</div>
    {else}
<div class="exp-feedback is-warn" role="alert">{'No link was selected. Tick the links to mark first.'|i18n( 'design/admin/url/list' )}</div>
    {/if}
{/if}

{if $summary}
<section aria-labelledby="url-overview-title">
<h2 class="exp-sr" id="url-overview-title">{'Overview'|i18n( 'design/admin/url/list' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure{if eq( $view_mode, 'all' )} is-current{/if}"><a href={concat( '/url/list/all', $sort_part )|ezurl}><strong>{$summary.all}</strong> <span>{'Links in published content'|i18n( 'design/admin/url/list' )}</span></a></li>
    <li class="exp-figure{if eq( $view_mode, 'valid' )} is-current{/if}"><a href={concat( '/url/list/valid', $sort_part )|ezurl}><strong>{$summary.valid}</strong> <span>{'Valid'|i18n( 'design/admin/url/list' )}</span></a></li>
    <li class="exp-figure{if $summary.invalid|gt( 0 )} is-attention{/if}{if eq( $view_mode, 'invalid' )} is-current{/if}"><a href={concat( '/url/list/invalid', $sort_part )|ezurl}><strong>{$summary.invalid}</strong> <span>{'Invalid'|i18n( 'design/admin/url/list' )}</span></a></li>
    <li class="exp-figure{if eq( $view_mode, 'unchecked' )} is-current{/if}"><a href={concat( '/url/list/unchecked', $sort_part )|ezurl}><strong>{$summary.unchecked}</strong> <span>{'Never checked'|i18n( 'design/admin/url/list' )}</span></a></li>
    <li class="exp-figure"><strong>{if $summary.last_check|gt( 0 )}{$summary.last_check|l10n( shortdate )}{else}&ndash;{/if}</strong> <span>{if $summary.last_check|gt( 0 )}{'Last link check, %time'|i18n( 'design/admin/url/list',, hash( '%time', $summary.last_check|l10n( shorttime ) ) )}{else}{'The link check has not run yet'|i18n( 'design/admin/url/list' )}{/if}</span></li>
</ul>
</section>
{/if}

<section aria-labelledby="url-find-title">
<h2 class="exp-sr" id="url-find-title">{'Find links'|i18n( 'design/admin/url/list' )}</h2>
<form class="exp-toolbar" method="get" action={concat( '/url/list/', $view_mode, $sort_part )|ezurl} role="search">
    <div class="exp-field exp-field-wide">
        <label for="url-search">{'Find a link'|i18n( 'design/admin/url/list' )}</label>
        <div class="exp-searchrow">
            <input type="search" id="url-search" name="q" value="{$search|wash}" autocomplete="off" spellcheck="false" maxlength="200" aria-describedby="url-search-help" />
            <button type="submit" class="exp-btn exp-btn-primary">{'Search'|i18n( 'design/admin/url/list' )}</button>
            {if $search|ne( '' )}<a class="exp-btn" href={concat( '/url/list/', $view_mode, $sort_part )|ezurl}>{'Clear search'|i18n( 'design/admin/url/list' )}</a>{/if}
        </div>
        <span class="exp-help" id="url-search-help">{'Any part of the address, such as a domain or a path. Upper and lower case are the same.'|i18n( 'design/admin/url/list' )}</span>
    </div>
    <div class="exp-field">
        <span class="exp-field-label" id="url-show-label"><strong>{'Show'|i18n( 'design/admin/url/list' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="url-show-label">
        {foreach array( hash( 'mode', 'all', 'text', 'All'|i18n( 'design/admin/url/list' ) ),
                        hash( 'mode', 'valid', 'text', 'Valid'|i18n( 'design/admin/url/list' ) ),
                        hash( 'mode', 'invalid', 'text', 'Invalid'|i18n( 'design/admin/url/list' ) ),
                        hash( 'mode', 'unchecked', 'text', 'Never checked'|i18n( 'design/admin/url/list' ) ) ) as $tab}
            <li>{if eq( $tab.mode, $view_mode )}<span class="current" aria-current="page">{$tab.text|wash}</span>{else}<a href={concat( '/url/list/', $tab.mode, $sort_part, $search_suffix )|ezurl}>{$tab.text|wash}</a>{/if}</li>
        {/foreach}
        </ul>
    </div>
    <div class="exp-field">
        <span class="exp-field-label" id="url-sort-label"><strong>{'Order'|i18n( 'design/admin/url/list' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="url-sort-label">
        {foreach array( hash( 'sort', 'address', 'text', 'Address A to Z'|i18n( 'design/admin/url/list' ) ),
                        hash( 'sort', 'checked', 'text', 'Last checked'|i18n( 'design/admin/url/list' ) ),
                        hash( 'sort', 'modified', 'text', 'Last modified'|i18n( 'design/admin/url/list' ) ) ) as $tab}
            <li>{if eq( $tab.sort, $sort )}<span class="current" aria-current="true">{$tab.text|wash}</span>{else}<a href={concat( '/url/list/', $view_mode, cond( $tab.sort|ne( 'address' ), concat( '/(sort)/', $tab.sort ), '' ), $search_suffix )|ezurl}>{$tab.text|wash}</a>{/if}</li>
        {/foreach}
        </ul>
    </div>
</form>
</section>

<form name="urllist" method="post" action={concat( '/url/list/', $view_mode, $sort_part, $search_suffix )|ezurl}>

<section class="exp-section" aria-labelledby="url-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="url-list-title">{if $search|ne( '' )}{'Links containing “%search”'|i18n( 'design/admin/url/list',, hash( '%search', $search ) )|wash}{else}{'Links'|i18n( 'design/admin/url/list' )}{/if}</h2>
    {if $url_list_count|gt( 0 )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/url/list',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $page_limit ), $url_list_count ), '%count', $url_list_count ) )}</span>
    <label class="exp-meta exp-js-only" hidden><input type="checkbox" id="url-select-all" /> {'Select all on this page'|i18n( 'design/admin/url/list' )}</label>
    {/if}
</div>

{if $url_list|count|eq( 0 )}
<p class="exp-empty">
{if $search|ne( '' )}
    {'No link matches this search. Check the spelling, search for a shorter part of the address, or show all links.'|i18n( 'design/admin/url/list' )}
{elseif eq( $view_mode, 'invalid' )}
    {'No link is marked invalid. Either every link works, or the link check has not found a broken one yet.'|i18n( 'design/admin/url/list' )}
{elseif eq( $view_mode, 'unchecked' )}
    {'Every link has been checked at least once.'|i18n( 'design/admin/url/list' )}
{elseif eq( $view_mode, 'valid' )}
    {'No link is marked valid yet. Run the link check, or mark links valid by hand.'|i18n( 'design/admin/url/list' )}
{else}
    {'No published content links to an address yet. Links appear here when content with a URL field or a link in rich text is published.'|i18n( 'design/admin/url/list' )}
{/if}
</p>
{else}
<ul class="exp-cards" id="url-list">
{foreach $url_list as $url}
    {def $url_id = $url.id
         $card_id = concat( 'url-', $url_id )
         $info = first_set( $usage_map[$url_id], false() )
         $check = first_set( $url_checks[$url_id], hash( 'kind', '', 'openable', false() ) )
         $kind = $check.kind}
<li class="exp-card{if $url.is_valid|not} is-bad{/if}" id="{$card_id}">
    <div class="exp-card-head">
        <div class="exp-card-title">
            <label class="exp-select" title="{'Select this link.'|i18n( 'design/admin/url/list' )}">
                <input type="checkbox" name="URLSelection[]" value="{$url_id}" aria-label="{'Select %url'|i18n( 'design/admin/url/list',, hash( '%url', $url.url ) )|wash}" />
            </label>
            <h3 class="exp-card-addr" id="{$card_id}-title"><a href={concat( '/url/view/', $url_id )|ezurl} title="{'View information about URL.'|i18n( 'design/admin/url/list' )}">{$url.url|wash}</a></h3>
            <ul class="exp-badges">
                {if $url.is_valid}
                <li class="exp-badge is-ok">{'Valid'|i18n( 'design/admin/url/list' )}</li>
                {else}
                <li class="exp-badge is-bad">{'Invalid'|i18n( 'design/admin/url/list' )}</li>
                {/if}
                {if $url.last_checked|eq( 0 )}
                <li class="exp-badge is-warn">{'Never checked'|i18n( 'design/admin/url/list' )}</li>
                {/if}
                {if eq( $kind, 'https' )}
                <li class="exp-badge" title="{'The link check does not test https addresses: it records the time only, so this state is the one the link was given or set by hand.'|i18n( 'design/admin/url/list' )}">{'https: not tested'|i18n( 'design/admin/url/list' )}</li>
                {elseif eq( $kind, 'mailto' )}
                <li class="exp-badge is-info" title="{'For an e-mail address the link check looks up the mail server of its domain.'|i18n( 'design/admin/url/list' )}">{'E-mail'|i18n( 'design/admin/url/list' )}</li>
                {elseif eq( $kind, 'internal' )}
                <li class="exp-badge is-info" title="{'A path on this site: the link check looks it up as a URL alias.'|i18n( 'design/admin/url/list' )}">{'On this site'|i18n( 'design/admin/url/list' )}</li>
                {elseif eq( $kind, 'content' )}
                <li class="exp-badge" title="{'A link to a node or object in rich text. The link check looks it up as a path, finds nothing and marks it invalid, although the link works while its target exists.'|i18n( 'design/admin/url/list' )}">{'Link to content'|i18n( 'design/admin/url/list' )}</li>
                {elseif eq( $kind, 'other' )}
                <li class="exp-badge" title="{'The link check does not test this kind of address.'|i18n( 'design/admin/url/list' )}">{'Not tested'|i18n( 'design/admin/url/list' )}</li>
                {/if}
            </ul>
        </div>
        <div class="exp-actions">
            <a class="exp-btn exp-btn-small" href={concat( '/url/view/', $url_id )|ezurl} aria-describedby="{$card_id}-title">{'View'|i18n( 'design/admin/url/list' )}</a>
            <a class="exp-btn exp-btn-small" href={concat( '/url/edit/', $url_id )|ezurl} aria-describedby="{$card_id}-title" title="{'Edit URL.'|i18n( 'design/admin/url/list' )}">{'Edit'|i18n( 'design/admin/url/list' )}</a>
            {* only web, mail and site addresses are made links: never javascript: or data: *}
            {if $check.openable}
            <a class="exp-btn exp-btn-small" href="{$url.url|wash}" target="_blank" rel="noopener noreferrer" aria-describedby="{$card_id}-title" title="{'Open URL in new window.'|i18n( 'design/admin/url/list' )}">{'Open'|i18n( 'design/admin/url/list' )}</a>
            {/if}
        </div>
    </div>
    <dl class="exp-facts">
        <div>
            <dt>{'Checked'|i18n( 'design/admin/url/list' )}</dt>
            <dd>{if $url.last_checked|gt( 0 )}{$url.last_checked|l10n( shortdatetime )}{else}{'Never'|i18n( 'design/admin/url/list' )}{/if}</dd>
        </div>
        <div>
            <dt>{'Modified'|i18n( 'design/admin/url/list' )}</dt>
            <dd>{if $url.modified|gt( 0 )}{$url.modified|l10n( shortdatetime )}{else}{'Unknown'|i18n( 'design/admin/url/list' )}{/if}</dd>
        </div>
        <div class="exp-field-wide">
            <dt>{'Used by'|i18n( 'design/admin/url/list' )}</dt>
            <dd>
            {if $info}
                {if $info.count|eq( 0 )}
                {'No published object'|i18n( 'design/admin/url/list' )}
                {else}
                <ul class="exp-usage">
                {foreach $info.objects as $object}
                    <li>{if $object.node_id|gt( 0 )}<a href={concat( '/content/view/full/', $object.node_id )|ezurl}>{$object.name|wash}</a>{else}{$object.name|wash}{/if}</li>
                {/foreach}
                </ul>
                {if $info.count|gt( $info.objects|count )} <a href={concat( '/url/view/', $url_id )|ezurl}>{'and %count more'|i18n( 'design/admin/url/list',, hash( '%count', sub( $info.count, $info.objects|count ) ) )}</a>{/if}
                {/if}
            {else}
                <a href={concat( '/url/view/', $url_id )|ezurl}>{'See the objects on the link page'|i18n( 'design/admin/url/list' )}</a>
            {/if}
            </dd>
        </div>
    </dl>
</li>
    {undef $url_id $card_id $info $check $kind}
{/foreach}
</ul>
{/if}

<div class="exp-listfoot">
    {* The sizes come from admininterface.ini [PaginationSettings]; the preference stores the position in that list. *}
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/url/list' )}:</span>
    {foreach first_set( $limit_choices, array( 10, 25, 50 ) ) as $limit_index => $limit_option}
        {if eq( $limit_option, $page_limit )}
        <span class="current" aria-current="true">{$limit_option}</span>
        {else}
        <a href={concat( '/user/preferences/set/admin_url_list_limit/', $limit_index|inc )|ezurl} title="{'Show %count items per page.'|i18n( 'design/admin/url/list',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=concat( '/url/list/', $view_mode )
             page_uri_suffix=$search_suffix
             item_count=$url_list_count
             view_parameters=$view_parameters
             item_limit=$page_limit}
    </div>
</div>
</section>

{if $url_list|count|gt( 0 )}
<div class="exp-bottombar">
    <div class="exp-actions">
        <button type="submit" class="exp-btn" name="SetValid" value="1" aria-describedby="url-mark-help">{'Mark selected valid'|i18n( 'design/admin/url/list' )}</button>
        <button type="submit" class="exp-btn exp-btn-outline-danger" name="SetInvalid" value="1" aria-describedby="url-mark-help">{'Mark selected invalid'|i18n( 'design/admin/url/list' )}</button>
    </div>
    <p class="exp-meta" id="url-mark-help">{'Marking changes only the state shown here and in templates that hide invalid links; nothing is removed and no content changes. The next link check tests the links again.'|i18n( 'design/admin/url/list' )} <span id="url-selected-count" aria-live="polite"></span></p>
</div>
{/if}

</form>

</div></div></div>
</div>

<script type="text/javascript">
var expUrlListText = {ldelim}
    selected: '{'%count selected.'|i18n( 'design/admin/url/list' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var list = document.getElementById( 'url-list' );
    var nodes = document.querySelectorAll( '.exp-urls .exp-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    if ( !list ) return;
    var selectAll = document.getElementById( 'url-select-all' );
    var countEl = document.getElementById( 'url-selected-count' );
    function boxes() { return list.querySelectorAll( 'input[name="URLSelection[]"]' ); }
    function update() {
        var all = boxes(), n = 0, j;
        for ( j = 0; j < all.length; j++ ) {
            var card = all[j].closest( '.exp-card' );
            if ( card ) card.classList.toggle( 'is-selected', all[j].checked );
            if ( all[j].checked ) n++;
        }
        if ( countEl ) countEl.textContent = n ? expUrlListText.selected.split( '%count' ).join( n ) : '';
        if ( selectAll ) { selectAll.checked = n > 0 && n === all.length; selectAll.indeterminate = n > 0 && n < all.length; }
    }
    list.addEventListener( 'change', update );
    if ( selectAll ) selectAll.addEventListener( 'change', function () {
        var all = boxes(), j;
        for ( j = 0; j < all.length; j++ ) all[j].checked = selectAll.checked;
        update();
    } );
    update();
})();
{/literal}
</script>
