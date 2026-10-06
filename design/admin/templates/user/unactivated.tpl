{* The users who registered and never activated their account (user/unactivated).

   How many there are, a search by login, e-mail or name (?q=), the order (registration date, login, e-mail, name,
   either way round; the view parameters SortField and SortOrder), the paging, and one row per user with the age of
   the registration (old ones marked). Three actions on the ticked users, each confirmed in place: activate (the
   account works at once, as if the user had clicked the link), send the activation mail again (with a new link; the
   old one stops working), and remove (the user object is removed for good). The view does each only to users who are
   still unactivated.

   "Remove all" (RemoveAllButton) removes every unactivated user, or every one matching the search, after an in-place
   confirmation that states the count; it reports how many were removed and why any were kept.
   Every name the view reads is kept (DeleteIDArray[], ActivateButton, RemoveButton); ResendButton and RemoveAllButton are new; every
   template variable of the old page is still set. Works without javascript; the script only adds Select all and the
   selection count. Overrides design/standard/templates/user/unactivated.tpl in the administration designs; the same
   file is in design/admin and design/admin4. Guide: doc/guides/roles-and-policies.md *}
{include uri='design:role/exp_style.tpl'}

{def $uri = $module.functions.unactivated.uri
     $rows = first_set( $unactivated_rows, array() )
     $search = first_set( $unactivated_search, '' )
     $search_suffix = first_set( $unactivated_search_suffix, '' )
     $total = first_set( $unactivated_total, $unactivated_count )
     $old_days = first_set( $unactivated_old_days, 30 )
     $transport = first_set( $unactivated_mail_transport, '' )
     $sort_part = concat( '/', $sort_field, '/', $sort_order )}

<div class="context-block exp-roles">
<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Unactivated users (%users_count)'|i18n( 'design/admin/user',, hash( '%users_count', $total ) )}</h1>
</div></div>
<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'People who registered on the site but never clicked the link of their activation mail. Their accounts exist but cannot be used to log in. You can activate them by hand, send the mail again, or remove registrations nobody will finish.'|i18n( 'design/admin/user/unactivated' )}</p>

{* Results of the last action *}
{if and( is_set( $success_activate ), is_set( $errors_activate ) )}
    {if $success_activate}
    <div class="exp-feedback is-ok" role="status">
        <h2 class="exp-h2">{'The following users have been successfully activated:'|i18n( 'design/admin/user/activations' )}</h2>
        <ul>
        {foreach $success_activate as $userid}
            {def $object = fetch( content, object, hash( 'object_id', $userid ) )}
            {if $object}<li>{if $object.status|eq( 1 )}<a href={$object.main_node.url_alias|ezurl}>{$object.name|wash}</a>{else}{$object.name|wash}{/if}</li>{/if}
            {undef $object}
        {/foreach}
        </ul>
    </div>
    {/if}
    {if $errors_activate}
    <div class="exp-feedback is-bad" role="alert"><h2 class="exp-h2">{'Some users have not been activated'|i18n( 'design/admin/user/activations' )}</h2> <p>{'They were activated or removed meanwhile.'|i18n( 'design/admin/user/unactivated' )}</p></div>
    {/if}
{elseif and( is_set( $success_remove ), is_set( $errors_remove ) )}
    {if $success_remove}
    <div class="exp-feedback is-ok" role="status">
        <h2 class="exp-h2">{'The following unactivated users have been successfully removed:'|i18n( 'design/admin/user/activations' )}</h2>
        <ul>{foreach $success_remove as $name}<li>{$name|wash}</li>{/foreach}</ul>
    </div>
    {/if}
    {if $errors_remove}
    <div class="exp-feedback is-bad" role="alert"><h2 class="exp-h2">{'Some users have not been removed'|i18n( 'design/admin/user/activations' )}</h2> <p>{'Only users who are still unactivated are removed; the others were activated or removed meanwhile.'|i18n( 'design/admin/user/unactivated' )}</p></div>
    {/if}
{elseif is_set( $success_resend )}
    {if $success_resend|gt( 0 )}
    <div class="exp-feedback is-ok" role="status">{'The activation mail was sent again to %count users, each with a new link.'|i18n( 'design/admin/user/unactivated',, hash( '%count', $success_resend ) )}</div>
    {/if}
    {if $errors_resend}
    <div class="exp-feedback is-bad" role="alert">
        {'The mail could not be sent to some users:'|i18n( 'design/admin/user/unactivated' )}
        <ul>
        {foreach $errors_resend as $reason => $count}
            <li>{switch match=$reason}
                {case match='no_email'}{'%count without a valid e-mail address'|i18n( 'design/admin/user/unactivated',, hash( '%count', $count ) )}{/case}
                {case match='not_unactivated'}{'%count no longer unactivated'|i18n( 'design/admin/user/unactivated',, hash( '%count', $count ) )}{/case}
                {case}{'%count not accepted by the mail transport'|i18n( 'design/admin/user/unactivated',, hash( '%count', $count ) )}{/case}
            {/switch}</li>
        {/foreach}
        </ul>
    </div>
    {/if}
{elseif is_set( $remove_all_result )}
    <div class="exp-feedback {if $remove_all_result.removed|gt( 0 )}is-ok{else}is-warn{/if}" role="status" id="unact-remove-all-result">
        <p><strong>{'%removed removed, %skipped skipped.'|i18n( 'design/admin/user/unactivated',, hash( '%removed', $remove_all_result.removed, '%skipped', $remove_all_result.skipped_total ) )}</strong></p>
        {if $remove_all_result.skipped}
        <ul>
        {foreach $remove_all_result.skipped as $reason => $count}
            <li>{switch match=$reason}
                {case match='activated'}{'%count were activated meanwhile and kept'|i18n( 'design/admin/user/unactivated',, hash( '%count', $count ) )}{/case}
                {case match='protected'}{'%count are the anonymous user, the administrator account or you, and are never removed'|i18n( 'design/admin/user/unactivated',, hash( '%count', $count ) )}{/case}
                {case match='gone'}{'%count were removed meanwhile by someone else'|i18n( 'design/admin/user/unactivated',, hash( '%count', $count ) )}{/case}
                {case}{'%count could not be removed'|i18n( 'design/admin/user/unactivated',, hash( '%count', $count ) )}{/case}
            {/switch}</li>
        {/foreach}
        </ul>
        {/if}
    </div>
{elseif is_set( $nothing_selected )}
    <div class="exp-feedback is-warn" role="alert">{'No user was selected. Tick the users first.'|i18n( 'design/admin/user/unactivated' )}</div>
{/if}

<section aria-labelledby="unact-find-title">
<h2 class="exp-sr" id="unact-find-title">{'Find users'|i18n( 'design/admin/user/unactivated' )}</h2>
<form class="exp-toolbar" method="get" action={concat( $uri, $sort_part )|ezurl} role="search">
    <div class="exp-field exp-field-wide">
        <label for="unact-search">{'Find a registration'|i18n( 'design/admin/user/unactivated' )}</label>
        <div class="exp-searchrow">
            <input type="search" id="unact-search" name="q" value="{$search|wash}" autocomplete="off" maxlength="100" aria-describedby="unact-search-help" />
            <button type="submit" class="exp-btn exp-btn-primary">{'Search'|i18n( 'design/admin/user/unactivated' )}</button>
            {if $search|ne( '' )}<a class="exp-btn" href={concat( $uri, $sort_part )|ezurl}>{'Clear search'|i18n( 'design/admin/user/unactivated' )}</a>{/if}
        </div>
        <span class="exp-help" id="unact-search-help">{'Any part of the login, the e-mail address or the name.'|i18n( 'design/admin/user/unactivated' )}</span>
    </div>
    <div class="exp-field exp-field-wide">
        <span id="unact-sort-label"><strong>{'Order'|i18n( 'design/admin/user/unactivated' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="unact-sort-label">
        {foreach array( hash( 'key', 'time', 'text', 'Registration date'|i18n( 'design/admin/user' ) ),
                        hash( 'key', 'login', 'text', 'Login'|i18n( 'design/admin/user' ) ),
                        hash( 'key', 'email', 'text', 'E-mail'|i18n( 'design/admin/user' ) ),
                        hash( 'key', 'name', 'text', 'Name'|i18n( 'design/admin/user' ) ) ) as $tab}
            {if eq( $tab.key, $sort_field )}
            <li><a class="current" aria-current="true" href={concat( $uri, '/', $tab.key, '/', cond( eq( $sort_order, 'asc' ), 'desc', 'asc' ), $search_suffix )|ezurl} title="{'Reverse the order'|i18n( 'design/admin/user/unactivated' )}">{$tab.text|wash} {if eq( $sort_order, 'asc' )}&#8593;{else}&#8595;{/if}</a></li>
            {else}
            <li><a href={concat( $uri, '/', $tab.key, '/asc', $search_suffix )|ezurl}>{$tab.text|wash}</a></li>
            {/if}
        {/foreach}
        </ul>
    </div>
</form>
</section>

<form name="activations" method="post" action={concat( $uri, $sort_part, $search_suffix )|ezurl}>
<section class="exp-section" aria-labelledby="unact-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="unact-list-title">{if $search|ne( '' )}{'Registrations matching “%search”'|i18n( 'design/admin/user/unactivated',, hash( '%search', $search ) )|wash}{else}{'Registrations'|i18n( 'design/admin/user/unactivated' )}{/if}</h2>
    {if $unactivated_count|gt( 0 )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/user/unactivated',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $number_of_items ), $unactivated_count ), '%count', $unactivated_count ) )}</span>
    {/if}
</div>

{if $rows}
<div class="exp-table-wrap">
<table class="exp-table" id="unact-list">
<thead><tr>
    <th scope="col"><span class="exp-sr">{'Select'|i18n( 'design/admin/user/unactivated' )}</span><input type="checkbox" class="exp-js-only" hidden id="unact-select-all" aria-label="{'Select all on this page'|i18n( 'design/admin/user/unactivated' )}" /></th>
    <th scope="col">{'Name'|i18n( 'design/admin/user' )}</th>
    <th scope="col">{'Login'|i18n( 'design/admin/user' )}</th>
    <th scope="col">{'E-mail'|i18n( 'design/admin/user' )}</th>
    <th scope="col">{'Registered'|i18n( 'design/admin/user/unactivated' )}</th>
</tr></thead>
<tbody>
{foreach $rows as $row}
<tr>
    <td><input type="checkbox" name="DeleteIDArray[]" id="delete-{$row.contentobject_id}" value="{$row.contentobject_id}" /></td>
    <td><label for="delete-{$row.contentobject_id}">{if $row.name|ne( '' )}{$row.name|wash}{else}<span class="exp-muted">{'(no name)'|i18n( 'design/admin/user/unactivated' )}</span>{/if}</label></td>
    <td><code>{$row.login|wash}</code></td>
    <td>{$row.email|wash}</td>
    <td>
        {if $row.age.unknown}<span class="exp-muted">{'Unknown'|i18n( 'design/admin/user/unactivated' )}</span>
        {else}
        {$row.time|l10n( 'shortdatetime' )}<br />
        <span class="exp-muted">{if $row.age.days|gt( 0 )}{'%count days ago'|i18n( 'design/admin/user/unactivated',, hash( '%count', $row.age.days ) )}{else}{'%count hours ago'|i18n( 'design/admin/user/unactivated',, hash( '%count', $row.age.hours ) )}{/if}</span>
        {if $row.age.is_old} <span class="exp-badge is-warn" title="{'Registered more than %days days ago: it is unlikely to be finished.'|i18n( 'design/admin/user/unactivated',, hash( '%days', $old_days ) )}">{'Old'|i18n( 'design/admin/user/unactivated' )}</span>{/if}
        {/if}
    </td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{else}
<p class="exp-empty">{if $search|ne( '' )}{'No unactivated user matches this search.'|i18n( 'design/admin/user/unactivated' )}{else}{'There are no unactivated users'|i18n( 'design/admin/user/activations' )}{/if}</p>
{/if}

<div class="exp-listfoot">
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/user/unactivated' )}:</span>
        {foreach array( 10, 25, 50 ) as $size_index => $size}
        {if eq( $size, $number_of_items )}<span class="current" aria-current="true">{$size}</span>{else}<a href={concat( '/user/preferences/set/', $limit_preference, '/', $size_index|inc )|ezurl}>{$size}</a>{/if}
        {/foreach}
    </p>
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=concat( '/user/unactivated/', $sort_field, '/', $sort_order )
             page_uri_suffix=$search_suffix
             item_count=$unactivated_count
             view_parameters=$view_parameters
             item_limit=$number_of_items}
    </div>
</div>
</section>

{if $rows}
<div class="exp-bottombar">
    <div class="exp-actions">
        <details class="exp-confirm">
            <summary>{'Activate selected users'|i18n( 'design/admin/user' )}</summary>
            <div>
                <p>{'The ticked accounts work at once, as if their owners had clicked the activation link. Only do this when you know the e-mail addresses are theirs.'|i18n( 'design/admin/user/unactivated' )}</p>
                <button class="exp-btn exp-btn-primary" type="submit" name="ActivateButton" value="1" title="{'Activate selected users.'|i18n( 'design/admin/user' )}">{'Activate the ticked users'|i18n( 'design/admin/user/unactivated' )}</button>
            </div>
        </details>
        <details class="exp-confirm">
            <summary>{'Send the activation mail again'|i18n( 'design/admin/user/unactivated' )}</summary>
            <div>
                <p>{'Each ticked user gets a new activation mail with a new link; the link of the earlier mail stops working.'|i18n( 'design/admin/user/unactivated' )}{if $transport|ne( '' )} {'Mail is sent with the transport “%transport” (site.ini [MailSettings]).'|i18n( 'design/admin/user/unactivated',, hash( '%transport', $transport ) )|wash}{/if}</p>
                <button class="exp-btn" type="submit" name="ResendButton" value="1">{'Send the mail to the ticked users'|i18n( 'design/admin/user/unactivated' )}</button>
            </div>
        </details>
        <details class="exp-confirm">
            <summary>{'Remove selected users'|i18n( 'design/admin/user' )}</summary>
            <div>
                <p>{'The ticked registrations are removed for good, with their user objects. This cannot be undone. Activated users are never removed here.'|i18n( 'design/admin/user/unactivated' )}</p>
                <button class="exp-btn exp-btn-danger" type="submit" name="RemoveButton" value="1" title="{'Remove selected users.'|i18n( 'design/admin/user' )}">{'Remove the ticked users'|i18n( 'design/admin/user/unactivated' )}</button>
            </div>
        </details>
        <details class="exp-confirm" id="unact-remove-all">
            <summary>{if $search|ne( '' )}{'Remove all %count matching the search'|i18n( 'design/admin/user/unactivated',, hash( '%count', $unactivated_count ) )}{else}{'Remove all unactivated users'|i18n( 'design/admin/user/unactivated' )}{/if}</summary>
            <div>
                <p>{if $search|ne( '' )}{'The %count unactivated users matching “%search” are removed for good, on every page, not only this one. This cannot be undone.'|i18n( 'design/admin/user/unactivated',, hash( '%count', $unactivated_count, '%search', $search ) )|wash}{else}{'All %count unactivated users are removed for good, on every page, not only this one. This cannot be undone.'|i18n( 'design/admin/user/unactivated',, hash( '%count', $unactivated_count ) )}{/if}
                {'Each one is checked again just before it is removed: anyone activated meanwhile, the anonymous user, the administrator account and you are kept.'|i18n( 'design/admin/user/unactivated' )}</p>
                <button class="exp-btn exp-btn-danger" type="submit" name="RemoveAllButton" value="1">{if $search|ne( '' )}{'Remove the %count matching users'|i18n( 'design/admin/user/unactivated',, hash( '%count', $unactivated_count ) )}{else}{'Remove all %count'|i18n( 'design/admin/user/unactivated',, hash( '%count', $unactivated_count ) )}{/if}</button>
            </div>
        </details>
    </div>
    <p class="exp-meta" id="unact-selected-count" aria-live="polite"></p>
</div>
{/if}
</form>

</div></div></div>
</div>

<script type="text/javascript">
var expUnactText = {ldelim} selected: '{'%count selected.'|i18n( 'design/admin/user/unactivated' )|wash( javascript )}' {rdelim};
{literal}
(function () {
    var table = document.getElementById( 'unact-list' ), all = document.getElementById( 'unact-select-all' ), out = document.getElementById( 'unact-selected-count' );
    if ( !table ) return;
    function boxes() { return table.querySelectorAll( 'input[name="DeleteIDArray[]"]' ); }
    function update() {
        var b = boxes(), n = 0, i;
        for ( i = 0; i < b.length; i++ ) if ( b[i].checked ) n++;
        if ( out ) out.textContent = n ? expUnactText.selected.split( '%count' ).join( n ) : '';
        if ( all ) { all.checked = n > 0 && n === b.length; all.indeterminate = n > 0 && n < b.length; }
    }
    if ( all ) { all.hidden = false; all.addEventListener( 'change', function () { var b = boxes(), i; for ( i = 0; i < b.length; i++ ) b[i].checked = all.checked; update(); } ); }
    table.addEventListener( 'change', update );
})();
{/literal}
</script>
{undef $uri}
