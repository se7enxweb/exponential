{* URL wildcards (content/urlwildcards).

   What a wildcard is, the form that creates one with each field explained and its errors on the field, a tester
   that says which wildcard an address matches and what it becomes, a search and the kind (redirecting or direct),
   then one card per wildcard with its pattern, destination and kind. Ticked wildcards are removed after a
   confirmation; Remove all asks in place first.

   The same file is in design/admin and design/admin4. The search, the kind and the tester come from the view
   (wildcard_search, wildcard_kind, wildcard_test_result); without them (an older view class) the page shows the
   list and the form as before. Everything works without javascript. Guide: doc/guides/urls-and-aliases.md *}
{include uri='design:url/exp_style.tpl'}

{def $total = first_set( $wildcards_total_count, $wildcards_count )
     $search = first_set( $wildcard_search, '' )
     $search_suffix = first_set( $wildcard_search_suffix, '' )
     $kind = first_set( $wildcard_kind, 'all' )
     $kind_part = cond( $kind|ne( 'all' ), concat( '/(kind)/', $kind ), '' )
     $test = first_set( $wildcard_test, '' )
     $test_result = first_set( $wildcard_test_result, false() )
     $error_field = ''}
{switch match=$info_code}
{case match='error-no-wildcard-text'}{set $error_field = 'source'}{/case}
{case match='feedback-wildcard-exists'}{set $error_field = 'source'}{/case}
{case match='error-no-wildcard-destination-text'}{set $error_field = 'destination'}{/case}
{case match='error-wildcard-placeholder'}{set $error_field = 'destination'}{/case}
{case}{/case}
{/switch}

<div class="context-block content-urlalias-wildcard exp-urls">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'URL wildcards (%wildcard_count)'|i18n( 'design/admin/content/urlalias_wildcard',, hash( '%wildcard_count', $total ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A URL wildcard sends a whole group of addresses somewhere else with one rule: news/* to articles/{1\} turns news/2026/october into articles/2026/october. Each * of the pattern matches any text, and {1\}, {2\} ... in the destination put that text back. Wildcards are tried in the order they were created, the first that matches is used, and they are only consulted when no URL alias matches the address.'|i18n( 'design/admin/content/urlalias_wildcard' )|wash}</p>

{* Feedback and errors *}
{switch match=$info_code}
{case match='feedback-wildcard-removed'}
<div class="exp-feedback is-ok" role="status">{'The selected aliases were successfully removed.'|i18n( 'design/admin/content/urlalias_wildcard' )}</div>
{/case}
{case match='feedback-wildcard-removed-all'}
<div class="exp-feedback is-ok" role="status">{'All wildcard aliases were successfully removed.'|i18n( 'design/admin/content/urlalias_wildcard' )}</div>
{/case}
{case match='error-no-wildcard-text'}
<div class="exp-feedback is-bad" role="alert"><p><strong>{'Text is missing for the URL alias'|i18n( 'design/admin/content/urlalias_wildcard' )}</strong></p><p>{'Enter text in the input box to create a new alias.'|i18n( 'design/admin/content/urlalias_wildcard' )}</p></div>
{/case}
{case match='error-no-wildcard-destination-text'}
<div class="exp-feedback is-bad" role="alert"><p><strong>{'Text is missing for the URL alias destination'|i18n( 'design/admin/content/urlalias_wildcard' )}</strong></p><p>{'Enter some text in the destination input box to create a new alias.'|i18n( 'design/admin/content/urlalias_wildcard' )}</p></div>
{/case}
{case match='error-wildcard-placeholder'}
<div class="exp-feedback is-bad" role="alert"><p><strong>{'The destination uses %placeholders, but the pattern has %count * only.'|i18n( 'design/admin/content/urlalias_wildcard',, hash( '%placeholders', $info_data['placeholders'], '%count', $info_data['stars'] ) )|wash}</strong></p><p>{'Each * of the pattern is one placeholder, numbered from {1\}. Add a * to the pattern or remove the placeholder; otherwise visitors would be sent to an address with a part missing.'|i18n( 'design/admin/content/urlalias_wildcard' )|wash}</p></div>
{/case}
{case match='feedback-wildcard-created'}
<div class="exp-feedback is-ok" role="status">{'The URL alias <%wildcard_src_url> was successfully created'|i18n( 'design/admin/content/urlalias_wildcard',, hash( '%wildcard_src_url', $info_data['wildcard_src_url'] ) )|wash}</div>
{/case}
{case match='feedback-wildcard-exists'}
<div class="exp-feedback is-warn" role="alert">{'The URL alias <%wildcard_src_url> already exists, and it points to <%wildcard_dst_url>'|i18n( 'design/admin/content/urlalias_wildcard',, hash( '%wildcard_src_url', $info_data['wildcard_src_url'], '%wildcard_dst_url', $info_data['wildcard_dst_url'] ) )|wash}</div>
{/case}
{case}
{/case}
{/switch}

<div class="exp-columns" style="margin-bottom: 24px">

{* Create *}
<section class="exp-panel" aria-labelledby="wildcard-new-title">
<form name="wildcardcreate" method="post" action={concat( 'content/urlwildcards/', $kind_part, $search_suffix )|ezurl}>
<div class="exp-section-head">
    <h2 class="exp-h2" id="wildcard-new-title">{'Create new URL forwarding with wildcard'|i18n( 'design/admin/content/urlalias' )}</h2>
</div>
<div class="exp-form-fields">
    <div class="exp-field">
        <label for="ezcontent_urlalias_wildcard_source">{'New URL wildcard'|i18n( 'design/admin/content/urlalias_wildcard' )}</label>
        <input id="ezcontent_urlalias_wildcard_source" type="text" name="WildcardSourceText" value="{$wildcardSourceText|wash}" spellcheck="false" aria-describedby="wildcard-source-help{if eq( $error_field, 'source' )} wildcard-source-error{/if}"{if eq( $error_field, 'source' )} aria-invalid="true"{/if} title="{'Enter the URL for the new wildcard. Example: developer/*'|i18n( 'design/admin/content/urlalias_wildcard' )}" />
        <span class="exp-help" id="wildcard-source-help">{'The addresses to catch, without the host, with * where any text may follow. Example: developer/*'|i18n( 'design/admin/content/urlalias_wildcard' )}</span>
        {if eq( $error_field, 'source' )}<span class="exp-field-error" id="wildcard-source-error">{if eq( $info_code, 'feedback-wildcard-exists' )}{'A wildcard with this pattern exists already. Change the pattern, or remove the old wildcard first.'|i18n( 'design/admin/content/urlalias_wildcard' )}{else}{'Enter the pattern of the wildcard.'|i18n( 'design/admin/content/urlalias_wildcard' )}{/if}</span>{/if}
    </div>
    <div class="exp-field">
        <label for="ezcontent_urlalias_wildcard_destination">{'Destination'|i18n( 'design/admin/content/urlalias_wildcard' )}</label>
        <input id="ezcontent_urlalias_wildcard_destination" type="text" name="WildcardDestinationText" value="{$wildcardDestinationText|wash}" spellcheck="false" aria-describedby="wildcard-destination-help{if eq( $error_field, 'destination' )} wildcard-destination-error{/if}"{if eq( $error_field, 'destination' )} aria-invalid="true"{/if} title="{'Enter the destination URL for the new wildcard. Example: dev/{1\}'|i18n( 'design/admin/content/urlalias_wildcard' )}" />
        <span class="exp-help" id="wildcard-destination-help">{'Where the addresses go: a path of this site, or a full address when redirecting. {1\} is the text the first * matched. Example: dev/{1\}'|i18n( 'design/admin/content/urlalias_wildcard' )|wash}</span>
        {if eq( $error_field, 'destination' )}<span class="exp-field-error" id="wildcard-destination-error">{if eq( $info_code, 'error-wildcard-placeholder' )}{'Use only placeholders the pattern has a * for.'|i18n( 'design/admin/content/urlalias_wildcard' )}{else}{'Enter the destination.'|i18n( 'design/admin/content/urlalias_wildcard' )}{/if}</span>{/if}
    </div>
    <div class="exp-field">
        <label class="exp-check" for="wildcard-type"><input type="checkbox" id="wildcard-type" name="WildcardType"{if $wildcardType} checked="checked"{/if} /> <span><strong>{'Redirecting URL'|i18n( 'design/admin/content/urlalias' )}</strong><br /><span class="exp-help">{'Checked, visitors are redirected (HTTP 301) and see the destination address. Unchecked, the destination is shown under the address they asked for.'|i18n( 'design/admin/content/urlalias_wildcard' )}</span></span></label>
    </div>
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-primary" name="NewWildcardButton" value="{'Create'|i18n( 'design/admin/content/urlalias_wildcard' )}" title="{'Create a new wildcard URL alias.'|i18n( 'design/admin/content/urlalias_wildcard' )}">{'Create'|i18n( 'design/admin/content/urlalias_wildcard' )}</button>
    </div>
</div>
<input type="hidden" name="Offset" value="{$view_parameters.offset|wash}" />
</form>
</section>

{* Try an address *}
<section class="exp-panel" aria-labelledby="wildcard-test-title">
<form method="get" action={concat( 'content/urlwildcards/', $kind_part )|ezurl}>
<div class="exp-section-head">
    <h2 class="exp-h2" id="wildcard-test-title">{'Try an address'|i18n( 'design/admin/content/urlalias_wildcard' )}</h2>
    <p>{'See which wildcard an address matches and where it would lead. Nothing is changed.'|i18n( 'design/admin/content/urlalias_wildcard' )}</p>
</div>
<div class="exp-field">
    <label for="wildcard-test">{'Address'|i18n( 'design/admin/content/urlalias_wildcard' )}</label>
    <div class="exp-searchrow">
        <input type="text" id="wildcard-test" name="test" value="{$test|wash}" spellcheck="false" maxlength="200" aria-describedby="wildcard-test-help" />
        {if $search|ne( '' )}<input type="hidden" name="q" value="{$search|wash}" />{/if}
        <button type="submit" class="exp-btn">{'Try'|i18n( 'design/admin/content/urlalias_wildcard' )}</button>
    </div>
    <span class="exp-help" id="wildcard-test-help">{'Without the host, for example news/2026/october.'|i18n( 'design/admin/content/urlalias_wildcard' )}</span>
</div>
{if $test|ne( '' )}
<div class="exp-result" role="status">
    {if $test_result}
    <div class="exp-feedback is-info">
        <p><strong>{'The address matches the wildcard %pattern.'|i18n( 'design/admin/content/urlalias_wildcard',, hash( '%pattern', $test_result.source ) )|wash}</strong></p>
        <dl>
            <dt>{'Becomes'|i18n( 'design/admin/content/urlalias_wildcard' )}</dt>
            <dd><code>{$test_result.destination|wash}</code></dd>
            <dt>{'Visitors'|i18n( 'design/admin/content/urlalias_wildcard' )}</dt>
            <dd>{if eq( $test_result.type, 1 )}{'are redirected to this address (HTTP 301).'|i18n( 'design/admin/content/urlalias_wildcard' )}{else}{'see this address\'s page under the address they asked for.'|i18n( 'design/admin/content/urlalias_wildcard' )}{/if}</dd>
            <dt>{'Resolves to'|i18n( 'design/admin/content/urlalias_wildcard' )}</dt>
            <dd>{if $test_result.external}{'An address on another site.'|i18n( 'design/admin/content/urlalias_wildcard' )}{elseif $test_result.resolves}<code>{$test_result.resolves|wash}</code>{else}{'Nothing on this site: no URL alias or module has this address, so visitors get an error page.'|i18n( 'design/admin/content/urlalias_wildcard' )}{/if}</dd>
        </dl>
    </div>
    {else}
    <div class="exp-feedback is-warn"><p>{'No wildcard matches this address. It is answered by URL aliases and modules alone.'|i18n( 'design/admin/content/urlalias_wildcard' )}</p></div>
    {/if}
</div>
{/if}
</form>
</section>

</div>

<form name="wildcardform" method="post" action={concat( 'content/urlwildcards/', $kind_part, $search_suffix )|ezurl}>

<section class="exp-section" aria-labelledby="wildcard-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="wildcard-list-title">{'Wildcards'|i18n( 'design/admin/content/urlalias_wildcard' )}</h2>
    {if $wildcards_count|gt( 0 )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/content/urlalias_wildcard',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $wildcards_limit ), $wildcards_count ), '%count', $wildcards_count ) )}</span>
    <label class="exp-meta exp-js-only" hidden><input type="checkbox" id="wildcard-select-all" /> {'Select all on this page'|i18n( 'design/admin/content/urlalias_wildcard' )}</label>
    {/if}
</div>

{if $total|gt( 0 )}
<div class="exp-toolbar" role="search">
    <div class="exp-field exp-field-wide">
        <label for="wildcard-search">{'Find a wildcard'|i18n( 'design/admin/content/urlalias_wildcard' )}</label>
        <div class="exp-searchrow">
            <input type="search" id="wildcard-search" name="q" form="wildcard-search-form" value="{$search|wash}" autocomplete="off" spellcheck="false" maxlength="200" aria-describedby="wildcard-search-help" />
            <button type="submit" class="exp-btn" form="wildcard-search-form">{'Search'|i18n( 'design/admin/content/urlalias_wildcard' )}</button>
            {if $search|ne( '' )}<a class="exp-btn" href={concat( 'content/urlwildcards/', $kind_part )|ezurl}>{'Clear search'|i18n( 'design/admin/content/urlalias_wildcard' )}</a>{/if}
        </div>
        <span class="exp-help" id="wildcard-search-help">{'Any part of the pattern or of the destination.'|i18n( 'design/admin/content/urlalias_wildcard' )}</span>
    </div>
    <div class="exp-field">
        <span id="wildcard-kind-label"><strong>{'Show'|i18n( 'design/admin/content/urlalias_wildcard' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="wildcard-kind-label">
        {foreach array( hash( 'kind', 'all', 'text', 'All'|i18n( 'design/admin/content/urlalias_wildcard' ) ),
                        hash( 'kind', 'redirect', 'text', 'Redirecting'|i18n( 'design/admin/content/urlalias_wildcard' ) ),
                        hash( 'kind', 'direct', 'text', 'Direct'|i18n( 'design/admin/content/urlalias_wildcard' ) ) ) as $tab}
            <li>{if eq( $tab.kind, $kind )}<span class="current" aria-current="true">{$tab.text|wash}</span>{else}<a href={concat( 'content/urlwildcards/', cond( $tab.kind|ne( 'all' ), concat( '/(kind)/', $tab.kind ), '' ), $search_suffix )|ezurl}>{$tab.text|wash}</a>{/if}</li>
        {/foreach}
        </ul>
    </div>
</div>
{/if}

{if eq( count( $wildcard_list ), 0 )}
<p class="exp-empty">
{if $total|eq( 0 )}
    {'The URL wildcard list does not contain any aliases.'|i18n( 'design/admin/content/urlalias_wildcard' )} {'Create one with the form above, for example old-blog/* to blog/{1\}.'|i18n( 'design/admin/content/urlalias_wildcard' )|wash}
{else}
    {'No wildcard matches. Search for a shorter part, or show all wildcards.'|i18n( 'design/admin/content/urlalias_wildcard' )}
{/if}
</p>
{else}
<ul class="exp-cards" id="wildcard-list">
{foreach $wildcard_list as $wildcard}
    {def $card_id = concat( 'wildcard-', $wildcard.id )}
<li class="exp-card" id="{$card_id}">
    <div class="exp-card-head">
        <div class="exp-card-title">
            <label class="exp-select" title="{'Select this wildcard for removal.'|i18n( 'design/admin/content/urlalias_wildcard' )}">
                <input type="checkbox" name="WildcardIDList[]" value="{$wildcard.id}" aria-label="{'Select %wildcard for removal'|i18n( 'design/admin/content/urlalias_wildcard',, hash( '%wildcard', $wildcard.source_url ) )|wash}" />
            </label>
            <h3 class="exp-card-addr" id="{$card_id}-title">/{$wildcard.source_url|wash} <span class="exp-arrow" aria-label="{'leads to'|i18n( 'design/admin/content/urlalias_wildcard' )}">&rarr;</span> {$wildcard.destination_url|wash}</h3>
            <ul class="exp-badges">
            {switch match=$wildcard.type}
            {case match=1}<li class="exp-badge is-info" title="{'Visitors are redirected (HTTP 301).'|i18n( 'design/admin/content/urlalias_wildcard' )}">{'Forward'|i18n( 'design/admin/content/urlalias_wildcard' )}</li>{/case}
            {case match=2}<li class="exp-badge" title="{'The destination is shown under the address asked for.'|i18n( 'design/admin/content/urlalias_wildcard' )}">{'Direct'|i18n( 'design/admin/content/urlalias_wildcard' )}</li>{/case}
            {case}<li class="exp-badge is-warn">{'Undefined'|i18n( 'design/admin/content/urlalias_wildcard' )}</li>{/case}
            {/switch}
            </ul>
        </div>
    </div>
    <dl class="exp-facts">
        <div class="exp-field-wide">
            <dt>{'Type'|i18n( 'design/admin/content/urlalias_wildcard' )}</dt>
            <dd>{switch match=$wildcard.type}{case match=1}{'Visitors are redirected (HTTP 301).'|i18n( 'design/admin/content/urlalias_wildcard' )}{/case}{case match=2}{'The destination is shown under the address asked for.'|i18n( 'design/admin/content/urlalias_wildcard' )}{/case}{case}{'Undefined'|i18n( 'design/admin/content/urlalias_wildcard' )}{/case}{/switch}</dd>
        </div>
        <div>
            <dt>{'ID'|i18n( 'design/admin/content/urlalias_wildcard' )}</dt>
            <dd>{$wildcard.id}</dd>
        </div>
    </dl>
</li>
    {undef $card_id}
{/foreach}
</ul>
{/if}

<div class="exp-listfoot">
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/content/urlalias_wildcard' )}:</span>
    {foreach $limitList as $limitEntry}
        {if eq( $limitID, $limitEntry['id'] )}
        <span class="current" aria-current="true">{$limitEntry['value']}</span>
        {else}
        <a href={concat( '/user/preferences/set/admin_urlwildcard_list_limit/', $limitEntry['id'] )|ezurl} title="{'Show %number_of items per page.'|i18n( 'design/admin/content/urlalias_wildcard',, hash( '%number_of', $limitEntry['value'] ) )}">{$limitEntry['value']}</a>
        {/if}
    {/foreach}
    </p>
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri='content/urlwildcards/'
             page_uri_suffix=$search_suffix
             item_count=$wildcards_count
             view_parameters=$view_parameters
             item_limit=$wildcards_limit}
    </div>
</div>
</section>

<div class="exp-bottombar">
{if $wildcard_list|count|gt( 0 )}
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-outline-danger" name="RemoveWildcardButton" value="{'Remove selected'|i18n( 'design/admin/content/urlalias_wildcard' )}" title="{'Remove selected aliases from the list above.'|i18n( 'design/admin/content/urlalias_wildcard' )}" onclick="return confirm( '{'Are you sure you want to remove the selected wildcards?'|i18n( 'design/admin/content/urlalias_wildcard' )|wash( javascript )}' );">{'Remove selected'|i18n( 'design/admin/content/urlalias_wildcard' )}</button>
    </div>
    <p class="exp-meta">{'A removed wildcard stops working at once: its addresses are then answered by URL aliases and modules alone.'|i18n( 'design/admin/content/urlalias_wildcard' )} <span id="wildcard-selected-count" aria-live="polite"></span></p>
    <details class="exp-confirm">
        <summary>{'Remove all'|i18n( 'design/admin/content/urlalias_wildcard' )}</summary>
        <div>
            <p>{'This removes every wildcard, %count in all, not only those shown. It cannot be undone.'|i18n( 'design/admin/content/urlalias_wildcard',, hash( '%count', $total ) )}</p>
            <button type="submit" class="exp-btn exp-btn-danger" name="RemoveAllWildcardsButton" value="{'Remove all'|i18n( 'design/admin/content/urlalias_wildcard' )}" title="{'Remove all wildcard aliases.'|i18n( 'design/admin/content/urlalias_wildcard' )}">{'Remove all wildcards'|i18n( 'design/admin/content/urlalias_wildcard' )}</button>
        </div>
    </details>
{else}
    <button type="submit" class="exp-btn" name="RemoveWildcardButton" value="{'Remove selected'|i18n( 'design/admin/content/urlalias_wildcard' )}" title="{'There are no removable aliases.'|i18n( 'design/admin/content/urlalias_wildcard' )}" disabled="disabled">{'Remove selected'|i18n( 'design/admin/content/urlalias_wildcard' )}</button>
    <p class="exp-meta">{'There are no removable aliases.'|i18n( 'design/admin/content/urlalias_wildcard' )}</p>
{/if}
</div>

<input type="hidden" name="Offset" value="{$view_parameters.offset|wash}" />

</form>
{* The search is a form of its own (GET); its fields point to it with form="" *}
<form id="wildcard-search-form" method="get" action={concat( 'content/urlwildcards/', $kind_part )|ezurl}></form>

</div></div></div>
</div>

<script type="text/javascript">
var expWildcardText = {ldelim}
    selected: '{'%count selected.'|i18n( 'design/admin/content/urlalias_wildcard' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var nodes = document.querySelectorAll( '.exp-urls .exp-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    var invalid = document.querySelector( '.exp-urls [aria-invalid="true"]' );
    if ( invalid ) invalid.focus();
    var list = document.getElementById( 'wildcard-list' );
    if ( !list ) return;
    var selectAll = document.getElementById( 'wildcard-select-all' );
    var countEl = document.getElementById( 'wildcard-selected-count' );
    function boxes() { return list.querySelectorAll( 'input[name="WildcardIDList[]"]' ); }
    function update() {
        var all = boxes(), n = 0, j;
        for ( j = 0; j < all.length; j++ ) {
            var card = all[j].closest( '.exp-card' );
            if ( card ) card.classList.toggle( 'is-selected', all[j].checked );
            if ( all[j].checked ) n++;
        }
        if ( countEl ) countEl.textContent = n ? expWildcardText.selected.split( '%count' ).join( n ) : '';
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
