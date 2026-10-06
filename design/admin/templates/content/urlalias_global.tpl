{* Global URL aliases (content/urltranslator).

   What a global alias is, the form that creates one with each field explained and its errors on the field, a
   search over the aliases and their destinations, the kind (redirecting or direct), then one card per alias with
   its whole path, where it points, what that resolves to now, its language and kind. Ticked aliases are removed
   after a confirmation; Remove all asks in place first.

   The same file is in design/admin and design/admin4. The page of aliases, what each resolves to, the search and
   what the form held come from the view (alias_list, alias_info, alias_search, alias_form); without them (an older
   view class) the page reads $filter.items as before. Everything works without javascript.
   Guide: doc/guides/urls-and-aliases.md *}
{include uri='design:url/exp_style.tpl'}

{def $aliasList = first_set( $alias_list, $filter.items )
     $alias_total = first_set( $alias_total_count, $filter.count )
     $alias_shown_count = first_set( $alias_count, $filter.count )
     $info_map = first_set( $alias_info, hash() )
     $search = first_set( $alias_search, '' )
     $search_suffix = first_set( $alias_search_suffix, '' )
     $kind = first_set( $alias_kind, 'all' )
     $kind_part = cond( $kind|ne( 'all' ), concat( '/(kind)/', $kind ), '' )
     $form = first_set( $alias_form, hash( 'language', false(), 'all_languages', false(), 'redirects', true() ) )
     $page_limit = first_set( $alias_limit, $filter.limit )
     $error_field = ''}
{switch match=$info_code}
{case match='error-no-alias-text'}{set $error_field = 'source'}{/case}
{case match='feedback-alias-exists'}{set $error_field = 'source'}{/case}
{case match='error-no-alias-destination-text'}{set $error_field = 'destination'}{/case}
{case match='error-action-invalid'}{set $error_field = 'destination'}{/case}
{case match='error-invalid-language'}{set $error_field = 'language'}{/case}
{case}{/case}
{/switch}

<div class="context-block content-urlalias-global exp-urls">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Globally defined URL aliases (%alias_count)'|i18n( 'design/admin/content/urlalias_global',, hash( '%alias_count', $alias_total ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A global URL alias gives a module view, or another alias, an address of your choosing: for example login for user/login. Visitors who open the alias get the destination, either redirected to its address or shown under the alias. The aliases of content (the addresses of nodes) are made on the URL aliases tab of each node and are not listed here.'|i18n( 'design/admin/content/urlalias_global' )}</p>

{* Feedback and errors *}
{switch match=$info_code}
{case match='feedback-removed'}
<div class="exp-feedback is-ok" role="status">{'The selected aliases were successfully removed.'|i18n( 'design/admin/content/urlalias_global' )}</div>
{/case}
{case match='feedback-removed-all'}
<div class="exp-feedback is-ok" role="status">{'All global aliases were successfully removed.'|i18n( 'design/admin/content/urlalias_global' )}</div>
{/case}
{case match='error-invalid-language'}
<div class="exp-feedback is-bad" role="alert">{'The specified language code <%language> is not valid.'|i18n( 'design/admin/content/urlalias_global',, hash( '%language', $info_data['language'] ) )|wash}</div>
{/case}
{case match='error-no-alias-text'}
<div class="exp-feedback is-bad" role="alert"><p><strong>{'Text is missing for the URL alias'|i18n( 'design/admin/content/urlalias_global' )}</strong></p><p>{'Enter text in the input box to create a new alias.'|i18n( 'design/admin/content/urlalias_global' )}</p></div>
{/case}
{case match='error-no-alias-destination-text'}
<div class="exp-feedback is-bad" role="alert"><p><strong>{'Text is missing for the URL alias destination'|i18n( 'design/admin/content/urlalias_global' )}</strong></p><p>{'Enter some text in the destination input box to create a new alias.'|i18n( 'design/admin/content/urlalias_global' )}</p></div>
{/case}
{case match='error-action-invalid'}
<div class="exp-feedback is-bad" role="alert">
    <p><strong>{'The specified destination URL %url does not exist in the system, cannot create alias for it'|i18n( 'design/admin/content/urlalias_global',, hash( '%url', concat( "<", $info_data['aliasText'], ">" ) ) )|wash}</strong></p>
    <p>{'Ensure that the destination points to a valid entry, one of:'|i18n( 'design/admin/content/urlalias_global' )}</p>
    <ul>
        <li>{'Built-in functionality, e.g. %example.'|i18n( 'design/admin/content/urlalias_global',, hash( '%example', '<i>user/login</i>' ) )}</li>
        <li>{'Existing aliases for the content structure.'|i18n( 'design/admin/content/urlalias_global' )}</li>
    </ul>
</div>
{/case}
{case match='feedback-alias-cleanup'}
<div class="exp-feedback is-ok" role="status">
    <p><strong>{'The URL alias was successfully created, but was modified by the system to <%new_alias>'|i18n( 'design/admin/content/urlalias_global',, hash( '%new_alias', $info_data['new_alias'] ) )|wash}</strong></p>
    <ul>
        {if is_set( $info_data['node_id'] )}
        <li>{'Note that the new alias points to a node and will not be displayed in the global list. It can be examined on the URL-Alias page of the node, %node_link.'|i18n( 'design/admin/content/urlalias_global',, hash( '%node_link', concat( '<a href=', concat( 'content/urlalias/', $info_data['node_id']|int )|ezurl, '>', concat( 'content/urlalias/', $info_data['node_id']|int ), '</a>' ) ) )}</li>
        {/if}
        <li>{'Invalid characters will be removed or transformed to valid characters.'|i18n( 'design/admin/content/urlalias_global' )}</li>
        <li>{'Existing objects or functionality with the same name take precedence on the name.'|i18n( 'design/admin/content/urlalias_global' )}</li>
    </ul>
</div>
{/case}
{case match='feedback-alias-created'}
<div class="exp-feedback is-ok" role="status">
    <p><strong>{'The URL alias <%new_alias> was successfully created'|i18n( 'design/admin/content/urlalias_global',, hash( '%new_alias', $info_data['new_alias'] ) )|wash}</strong></p>
    {if is_set( $info_data['node_id'] )}
    <p>{'Note that the new alias points to a node and will not be displayed in the global list. It can be examined on the URL-Alias page of the node, %node_link.'|i18n( 'design/admin/content/urlalias_global',, hash( '%node_link', concat( '<a href=', concat( 'content/urlalias/', $info_data['node_id']|int )|ezurl, '>', concat( 'content/urlalias/', $info_data['node_id']|int ), '</a>' ) ) )}</p>
    {/if}
</div>
{/case}
{case match='feedback-alias-exists'}
<div class="exp-feedback is-warn" role="alert">{'The URL alias &lt;%new_alias&gt; already exists, and it points to &lt;%action_url&gt;'|i18n( 'design/admin/content/urlalias_global',, hash( '%new_alias', concat( '<a href=', $info_data['url']|ezurl, '>', $info_data['new_alias']|wash, '</a>' ), '%action_url', concat( '<a href=', $info_data['action_url']|ezurl, '>', $info_data['action_url']|wash, '</a>' ) ) )}</div>
{/case}
{case}
{/case}
{/switch}

<form name="aliasform" method="post" action={concat( 'content/urltranslator/', $kind_part, $search_suffix )|ezurl}>

{* Create *}
<section class="exp-panel" aria-labelledby="alias-new-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="alias-new-title">{'Create new alias'|i18n( 'design/admin/content/urlalias' )}</h2>
    <p>{'The alias is the address visitors will use, the destination is what they get there.'|i18n( 'design/admin/content/urlalias_global' )}</p>
</div>
<div class="exp-columns">
<div class="exp-form-fields">
    <div class="exp-field">
        <label for="ezcontent_urlalias_global_source">{'New URL alias'|i18n( 'design/admin/content/urlalias_global' )}</label>
        <input id="ezcontent_urlalias_global_source" type="text" name="AliasSourceText" value="{$aliasSourceText|wash}" spellcheck="false" aria-describedby="alias-source-help{if eq( $error_field, 'source' )} alias-source-error{/if}"{if eq( $error_field, 'source' )} aria-invalid="true"{/if} title="{'Enter the URL for the new alias. Use forward slashes (/) to create subentries.'|i18n( 'design/admin/content/urlalias_global' )}" />
        <span class="exp-help" id="alias-source-help">{'Without the host and without a leading slash, for example login or campaign/autumn. Characters that are not allowed in an address are changed, and the page says so.'|i18n( 'design/admin/content/urlalias_global' )}</span>
        {if eq( $error_field, 'source' )}<span class="exp-field-error" id="alias-source-error">{if eq( $info_code, 'feedback-alias-exists' )}{'This alias is taken. Choose another address, or remove the existing alias first.'|i18n( 'design/admin/content/urlalias_global' )}{else}{'Enter the address of the alias.'|i18n( 'design/admin/content/urlalias_global' )}{/if}</span>{/if}
    </div>
    <div class="exp-field">
        <label for="ezcontent_urlalias_global_destination">{'Destination (path to existing functionality or resource)'|i18n( 'design/admin/content/urlalias_global' )}</label>
        <input id="ezcontent_urlalias_global_destination" type="text" name="AliasDestinationText" value="{$aliasDestinationText|wash}" spellcheck="false" aria-describedby="alias-destination-help{if eq( $error_field, 'destination' )} alias-destination-error{/if}"{if eq( $error_field, 'destination' )} aria-invalid="true"{/if} title="{'Enter the destination URL for the new alias. Use forward slashes (/) to create subentries.'|i18n( 'design/admin/content/urlalias_global' )}" />
        <span class="exp-help" id="alias-destination-help">{'A module view such as user/login or content/search, or an existing address of content such as about-us. A node given as content/view/full/<node ID> becomes an alias of that node, listed on its URL aliases tab.'|i18n( 'design/admin/content/urlalias_global' )|wash}</span>
        {if eq( $error_field, 'destination' )}<span class="exp-field-error" id="alias-destination-error">{'Enter a module view or an existing address.'|i18n( 'design/admin/content/urlalias_global' )}</span>{/if}
    </div>
</div>
<div class="exp-form-fields">
    <div class="exp-field">
        <label for="alias-language">{'Language'|i18n( 'design/admin/content/urlalias_global' )}</label>
        <select id="alias-language" name="LanguageCode" aria-describedby="alias-language-help"{if eq( $error_field, 'language' )} aria-invalid="true"{/if} title="{'Choose the language for the new URL alias.'|i18n( 'design/admin/content/urlalias_global' )}">
        {foreach $languages as $language}
            <option value="{$language.locale|wash}"{if eq( $language.locale, $form.language )} selected="selected"{/if}>{$language.name|wash}</option>
        {/foreach}
        </select>
        <span class="exp-help" id="alias-language-help">{'The alias works in siteaccesses that show this language.'|i18n( 'design/admin/content/urlalias_global' )}</span>
    </div>
    <div class="exp-field">
        <label class="exp-check" for="all-languages"><input type="checkbox" name="AllLanguages" id="all-languages" value="all-languages"{if $form.all_languages} checked="checked"{/if} /> <span><strong>{'Include in other languages'|i18n( 'design/admin/content/urlalias' )}</strong><br /><span class="exp-help">{'Makes the alias available in languages other than the one specified.'|i18n( 'design/admin/content/urlalias_global' )}</span></span></label>
    </div>
    <div class="exp-field">
        <label class="exp-check" for="alias_redirects"><input type="checkbox" name="AliasRedirects" id="alias_redirects" value="alias_redirects"{if $form.redirects} checked="checked"{/if} /> <span><strong>{'Alias should redirect to its destination'|i18n( 'design/admin/content/urlalias' )}</strong><br /><span class="exp-help">{'With <em>Alias should redirect to its destination</em> checked Exponential will redirect to the destination using a HTTP 301 response. Un-check it and the URL will stay the same &#8212; no redirection will be performed.'|i18n( 'design/admin/content/urlalias' )}</span></span></label>
    </div>
</div>
</div>
<div class="exp-actions" style="margin-top: 14px">
    <button type="submit" class="exp-btn exp-btn-primary" name="NewAliasButton" value="{'Create'|i18n( 'design/admin/content/urlalias_global' )}" title="{'Create a new global URL alias.'|i18n( 'design/admin/content/urlalias_global' )}">{'Create'|i18n( 'design/admin/content/urlalias_global' )}</button>
</div>
</section>

{* The list *}
<section class="exp-section" aria-labelledby="alias-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="alias-list-title">{'Global aliases'|i18n( 'design/admin/content/urlalias_global' )}</h2>
    {if $alias_shown_count|gt( 0 )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/content/urlalias_global',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $page_limit ), $alias_shown_count ), '%count', $alias_shown_count ) )}</span>
    <label class="exp-meta exp-js-only" hidden><input type="checkbox" id="alias-select-all" /> {'Select all on this page'|i18n( 'design/admin/content/urlalias_global' )}</label>
    {/if}
</div>

{if $alias_total|gt( 0 )}
<div class="exp-toolbar" role="search">
    <div class="exp-field exp-field-wide">
        <label for="alias-search">{'Find an alias'|i18n( 'design/admin/content/urlalias_global' )}</label>
        <div class="exp-searchrow">
            <input type="search" id="alias-search" name="q" form="alias-search-form" value="{$search|wash}" autocomplete="off" spellcheck="false" maxlength="200" aria-describedby="alias-search-help" />
            <button type="submit" class="exp-btn" form="alias-search-form">{'Search'|i18n( 'design/admin/content/urlalias_global' )}</button>
            {if $search|ne( '' )}<a class="exp-btn" href={concat( 'content/urltranslator/', $kind_part )|ezurl}>{'Clear search'|i18n( 'design/admin/content/urlalias_global' )}</a>{/if}
        </div>
        <span class="exp-help" id="alias-search-help">{'Any part of the last segment of the alias (login in campaign/login) or of its destination.'|i18n( 'design/admin/content/urlalias_global' )}</span>
    </div>
    <div class="exp-field">
        <span id="alias-kind-label"><strong>{'Show'|i18n( 'design/admin/content/urlalias_global' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="alias-kind-label">
        {foreach array( hash( 'kind', 'all', 'text', 'All'|i18n( 'design/admin/content/urlalias_global' ) ),
                        hash( 'kind', 'redirect', 'text', 'Redirecting'|i18n( 'design/admin/content/urlalias_global' ) ),
                        hash( 'kind', 'direct', 'text', 'Direct'|i18n( 'design/admin/content/urlalias_global' ) ) ) as $tab}
            <li>{if eq( $tab.kind, $kind )}<span class="current" aria-current="true">{$tab.text|wash}</span>{else}<a href={concat( 'content/urltranslator/', cond( $tab.kind|ne( 'all' ), concat( '/(kind)/', $tab.kind ), '' ), $search_suffix )|ezurl}>{$tab.text|wash}</a>{/if}</li>
        {/foreach}
        </ul>
    </div>
</div>
{/if}

{if eq( count( $aliasList ), 0 )}
<p class="exp-empty">
{if $alias_total|eq( 0 )}
    {'The global list does not contain any aliases.'|i18n( 'design/admin/content/urlalias_global' )} {'Create one with the form above, for example login with the destination user/login.'|i18n( 'design/admin/content/urlalias_global' )}
{else}
    {'No alias matches. Search for a shorter part, or show all aliases.'|i18n( 'design/admin/content/urlalias_global' )}
{/if}
</p>
{else}
<ul class="exp-cards" id="alias-list">
{foreach $aliasList as $element}
    {def $key = concat( $element.parent, '.', $element.text_md5, '.', $element.language_object.locale )
         $info = first_set( $info_map[$key], false() )
         $card_id = concat( 'alias-', $element.id, '-', $element.language_object.locale|wash )
         $broken = and( $info, eq( $info.destination.kind, 'module' ), $info.destination.module_exists|not )}
<li class="exp-card{if $broken} is-bad{/if}" id="{$card_id}">
    <div class="exp-card-head">
        <div class="exp-card-title">
            <label class="exp-select" title="{'Select this alias for removal.'|i18n( 'design/admin/content/urlalias_global' )}">
                <input type="checkbox" name="ElementList[]" value="{$key|wash}" aria-label="{'Select %alias for removal'|i18n( 'design/admin/content/urlalias_global',, hash( '%alias', first_set( $info.path, $element.text ) ) )|wash}" />
            </label>
            <h3 class="exp-card-addr" id="{$card_id}-title">/{foreach $element.path_array as $el}{if ne( $el.action, "nop:" )}<a href={concat( "/", $el.path )|ezurl}>{$el.text|wash}</a>{else}{$el.text|wash}{/if}{delimiter}/{/delimiter}{/foreach}</h3>
            <ul class="exp-badges">
                {if $element.alias_redirects}
                <li class="exp-badge is-info" title="{'Visitors are sent on to the destination with a 301 redirect.'|i18n( 'design/admin/content/urlalias_global' )}">{'Redirect'|i18n( 'design/admin/content/urlalias_global' )}</li>
                {else}
                <li class="exp-badge" title="{'The destination is shown under the alias; the address stays the same.'|i18n( 'design/admin/content/urlalias_global' )}">{'Direct'|i18n( 'design/admin/content/urlalias_global' )}</li>
                {/if}
                {if $broken}
                <li class="exp-badge is-bad">{'Module not found'|i18n( 'design/admin/content/urlalias_global' )}</li>
                {/if}
            </ul>
        </div>
    </div>
    <dl class="exp-facts">
        <div>
            <dt>{'Destination'|i18n( 'design/admin/content/urlalias_global' )}</dt>
            <dd><span class="exp-arrow" aria-hidden="true">&rarr;</span> <a href={$element.action_url|ezurl}><code>{$element.action_url|wash}</code></a></dd>
        </div>
        {if $info}
        <div>
            <dt>{'Resolves to'|i18n( 'design/admin/content/urlalias_global' )}</dt>
            <dd>
            {switch match=$info.destination.kind}
            {case match='module'}
                {if $info.destination.module_exists}
                {'The view %view of the module %module'|i18n( 'design/admin/content/urlalias_global',, hash( '%view', cond( $info.destination.view|ne( '' ), $info.destination.view, '-' ), '%module', $info.destination.module ) )|wash}
                {else}
                {'The module %module does not exist (any more): visitors get an error page.'|i18n( 'design/admin/content/urlalias_global',, hash( '%module', $info.destination.module ) )|wash}
                {/if}
            {/case}
            {case match='node'}
                <a href={concat( '/content/view/full/', $info.destination.node_id )|ezurl}>{'Node %node'|i18n( 'design/admin/content/urlalias_global',, hash( '%node', $info.destination.node_id ) )}</a>
            {/case}
            {case}
                <code>{$info.destination.url|wash}</code>
            {/case}
            {/switch}
            </dd>
        </div>
        {/if}
        <div>
            <dt>{'Language'|i18n( 'design/admin/content/urlalias_global' )}</dt>
            <dd><img src="{$element.language_object.locale|flag_icon}" width="18" height="12" alt="" /> {$element.language_object.name|wash}</dd>
        </div>
        <div>
            <dt>{'Always available'|i18n( 'design/admin/content/urlalias_global' )}</dt>
            <dd>{if $element.always_available}{'yes'|i18n( 'design/admin/content/urlalias_global' )}{else}{'no'|i18n( 'design/admin/content/urlalias_global' )}{/if}</dd>
        </div>
    </dl>
</li>
    {undef $key $info $card_id $broken}
{/foreach}
</ul>
{/if}

<div class="exp-listfoot">
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/content/urlalias_global' )}:</span>
    {foreach $limitList as $limitEntry}
        {if eq( $limitID, $limitEntry['id'] )}
        <span class="current" aria-current="true">{$limitEntry['value']}</span>
        {else}
        <a href={concat( '/user/preferences/set/admin_urlalias_list_limit/', $limitEntry['id'] )|ezurl} title="{'Show %number_of items per page.'|i18n( 'design/admin/content/urlalias_global',, hash( '%number_of', $limitEntry['value'] ) )}">{$limitEntry['value']}</a>
        {/if}
    {/foreach}
    </p>
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri='content/urltranslator/'
             page_uri_suffix=$search_suffix
             item_count=$alias_shown_count
             view_parameters=$view_parameters
             item_limit=$page_limit}
    </div>
</div>
</section>

<div class="exp-bottombar">
{if $aliasList|count|gt( 0 )}
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-outline-danger" name="RemoveAliasButton" value="{'Remove selected'|i18n( 'design/admin/content/urlalias_global' )}" title="{'Remove selected aliases from the list above.'|i18n( 'design/admin/content/urlalias_global' )}" onclick="return confirm( '{'Are you sure you want to remove the selected aliases?'|i18n( 'design/admin/content/urlalias_global' )|wash( javascript )}' );">{'Remove selected'|i18n( 'design/admin/content/urlalias_global' )}</button>
    </div>
    <p class="exp-meta">{'A removed alias stops working at once; links to it then lead to an error page. The destination itself is not changed.'|i18n( 'design/admin/content/urlalias_global' )} <span id="alias-selected-count" aria-live="polite"></span></p>
    <details class="exp-confirm">
        <summary>{'Remove all'|i18n( 'design/admin/content/urlalias_global' )}</summary>
        <div>
            <p>{'This removes every global alias, %count in all, not only those shown. Aliases of content nodes are kept. It cannot be undone.'|i18n( 'design/admin/content/urlalias_global',, hash( '%count', $alias_total ) )}</p>
            <button type="submit" class="exp-btn exp-btn-danger" name="RemoveAllAliasesButton" value="{'Remove all'|i18n( 'design/admin/content/urlalias_global' )}" title="{'Remove all global aliases.'|i18n( 'design/admin/content/urlalias_global' )}">{'Remove all global aliases'|i18n( 'design/admin/content/urlalias_global' )}</button>
        </div>
    </details>
{else}
    <button type="submit" class="exp-btn" name="RemoveAliasButton" value="{'Remove selected'|i18n( 'design/admin/content/urlalias_global' )}" title="{'There are no removable aliases.'|i18n( 'design/admin/content/urlalias_global' )}" disabled="disabled">{'Remove selected'|i18n( 'design/admin/content/urlalias_global' )}</button>
    <p class="exp-meta">{'There are no removable aliases.'|i18n( 'design/admin/content/urlalias_global' )}</p>
{/if}
</div>

</form>
{* The search is a form of its own (GET), so it never sends the creation fields; its fields point to it with form="" *}
<form id="alias-search-form" method="get" action={concat( 'content/urltranslator/', $kind_part )|ezurl}></form>

</div></div></div>
</div>

<script type="text/javascript">
var expAliasText = {ldelim}
    selected: '{'%count selected.'|i18n( 'design/admin/content/urlalias_global' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var nodes = document.querySelectorAll( '.exp-urls .exp-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    var list = document.getElementById( 'alias-list' );
    var source = document.getElementById( 'ezcontent_urlalias_global_source' );
    // the field with the error, else the alias field when the page is about creating one
    var invalid = document.querySelector( '.exp-urls [aria-invalid="true"]' );
    if ( invalid ) invalid.focus();
    else if ( source ) source.focus();
    if ( !list ) return;
    var selectAll = document.getElementById( 'alias-select-all' );
    var countEl = document.getElementById( 'alias-selected-count' );
    function boxes() { return list.querySelectorAll( 'input[name="ElementList[]"]' ); }
    function update() {
        var all = boxes(), n = 0, j;
        for ( j = 0; j < all.length; j++ ) {
            var card = all[j].closest( '.exp-card' );
            if ( card ) card.classList.toggle( 'is-selected', all[j].checked );
            if ( all[j].checked ) n++;
        }
        if ( countEl ) countEl.textContent = n ? expAliasText.selected.split( '%count' ).join( n ) : '';
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
