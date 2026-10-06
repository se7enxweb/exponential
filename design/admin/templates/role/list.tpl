{* The role list (role/list).

   What roles are, a search by name or id (?q=), the order (name, id, number of policies, number of assignments,
   either way round, as (sort) and (dir) in the address), then one card per role: its name, how many policies it
   has and how many users and groups it is assigned to, a warning when a policy gives access to everything or lets
   its users change roles, and its View, Edit, Assign and Copy actions. Remove selected asks first, in place.

   Every name the view has always read is kept (RemoveButton, NewButton, DeleteIDArray[]) and every template
   variable is still set (roles, role_count, assignment_counts, limit, limit_choices, limit_choice, role_sort,
   view_parameters). Works without javascript; the script only adds Select all and the selection count.
   The same file is in design/admin and design/admin4. Guide: doc/guides/roles-and-policies.md *}
{include uri='design:role/exp_style.tpl'}

{def $summaries = first_set( $role_summaries, hash() )
     $search = first_set( $role_search, '' )
     $search_suffix = first_set( $role_search_suffix, '' )
     $match_count = first_set( $role_match_count, $role_count )
     $count_sort = first_set( $role_count_sort, false() )
     $removed = first_set( $role_removed, false() )
     $sort_part = concat( '/(sort)/', $role_sort.field, '/(dir)/', $role_sort.direction )}

<div class="context-block exp-roles">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Roles (%role_count)'|i18n( 'design/admin/role/list',, hash( '%role_count', $role_count ) )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A role is a set of policies; each policy lets its users use one module or function, possibly only in some sections, classes or subtrees. A role does nothing until it is assigned to users or user groups; a user has every policy of every role assigned to them or to one of their groups.'|i18n( 'design/admin/role/list' )}</p>

{if $removed|ne( false() )}
    {if $removed|gt( 0 )}
<div class="exp-feedback is-ok" role="status">{'%count roles were removed, with their policies and assignments.'|i18n( 'design/admin/role/list',, hash( '%count', $removed ) )}</div>
    {else}
<div class="exp-feedback is-warn" role="alert">{'No role was selected. Tick the roles to remove first.'|i18n( 'design/admin/role/list' )}</div>
    {/if}
{/if}

<section aria-labelledby="role-find-title">
<h2 class="exp-sr" id="role-find-title">{'Find roles'|i18n( 'design/admin/role/list' )}</h2>
<form class="exp-toolbar" method="get" action={concat( '/role/list', $sort_part )|ezurl} role="search">
    <div class="exp-field exp-field-wide">
        <label for="role-search">{'Find a role'|i18n( 'design/admin/role/list' )}</label>
        <div class="exp-searchrow">
            <input type="search" id="role-search" name="q" value="{$search|wash}" autocomplete="off" maxlength="100" aria-describedby="role-search-help" />
            <button type="submit" class="exp-btn exp-btn-primary">{'Search'|i18n( 'design/admin/role/list' )}</button>
            {if $search|ne( '' )}<a class="exp-btn" href={concat( '/role/list', $sort_part )|ezurl}>{'Clear search'|i18n( 'design/admin/role/list' )}</a>{/if}
        </div>
        <span class="exp-help" id="role-search-help">{'Any part of the name, or the ID. Upper and lower case are the same.'|i18n( 'design/admin/role/list' )}</span>
    </div>
    <div class="exp-field exp-field-wide">
        <span id="role-sort-label"><strong>{'Order'|i18n( 'design/admin/role/list' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="role-sort-label">
        {def $sort_tabs = array( hash( 'key', 'name', 'text', 'Name'|i18n( 'design/admin/role/list' ) ),
                                 hash( 'key', 'id', 'text', 'ID'|i18n( 'design/admin/role/list' ) ) )}
        {if $count_sort}
            {set $sort_tabs = $sort_tabs|append( hash( 'key', 'policies', 'text', 'Policies'|i18n( 'design/admin/role/list' ) ),
                                                 hash( 'key', 'assigned', 'text', 'Assigned'|i18n( 'design/admin/role/list' ) ) )}
        {/if}
        {foreach $sort_tabs as $tab}
            {if eq( $tab.key, $role_sort.field )}
            <li><a class="current" aria-current="true" href={concat( '/role/list/(sort)/', $tab.key, '/(dir)/', $role_sort.opposite, $search_suffix )|ezurl} title="{'Reverse the order'|i18n( 'design/admin/role/list' )}"><span class="exp-sr">{'Ordered by'|i18n( 'design/admin/role/list' )} </span>{$tab.text|wash} {if eq( $role_sort.direction, 'asc' )}&#8593;{else}&#8595;{/if}</a></li>
            {else}
            <li><a href={concat( '/role/list/(sort)/', $tab.key, '/(dir)/', cond( or( eq( $tab.key, 'policies' ), eq( $tab.key, 'assigned' ) ), 'desc', 'asc' ), $search_suffix )|ezurl}>{$tab.text|wash}</a></li>
            {/if}
        {/foreach}
        {undef $sort_tabs}
        </ul>
    </div>
</form>
</section>

<form name="roles" action={concat( $module.functions.list.uri, '/', $sort_part, $search_suffix )|ezurl} method="post">

<section class="exp-section" aria-labelledby="role-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="role-list-title">{if $search|ne( '' )}{'Roles matching “%search”'|i18n( 'design/admin/role/list',, hash( '%search', $search ) )|wash}{else}{'Roles'|i18n( 'design/admin/role/list' )}{/if}</h2>
    {if $match_count|gt( 0 )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/role/list',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $match_count ), '%count', $match_count ) )}</span>
    <label class="exp-meta exp-js-only" hidden><input type="checkbox" id="role-select-all" /> {'Select all on this page'|i18n( 'design/admin/role/list' )}</label>
    {/if}
</div>

{if $roles|count|eq( 0 )}
<p class="exp-empty">{if $search|ne( '' )}{'No role matches this search.'|i18n( 'design/admin/role/list' )}{else}{'There are no roles yet. Create one with New role.'|i18n( 'design/admin/role/list' )}{/if}</p>
{else}
<ul class="exp-cards" id="role-list">
{foreach $roles as $role}
    {def $role_id = $role.id
         $summary = first_set( $summaries[$role_id], false() )
         $assigned = first_set( $assignment_counts[$role_id], $summary.assigned, 0 )
         $card_id = concat( 'role-', $role_id )}
<li class="exp-card{if and( $summary, $summary.full_access )} is-full{elseif and( $summary, $summary.manages_roles )} is-attention{/if}" id="{$card_id}">
    <div class="exp-card-head">
        <div class="exp-card-title">
            <label class="exp-select" title="{'Select role for removal.'|i18n( 'design/admin/role/list' )}">
                <input type="checkbox" name="DeleteIDArray[]" value="{$role_id}" data-name="{$role.name|wash}" aria-label="{'Select %role_name'|i18n( 'design/admin/role/list',, hash( '%role_name', $role.name ) )|wash}" />
            </label>
            <h3 id="{$card_id}-title"><a href={concat( '/role/view/', $role_id )|ezurl}>{$role.name|wash}</a></h3>
            <ul class="exp-badges">
                {if and( $summary, $summary.full_access )}
                <li class="exp-badge is-bad" title="{'A policy of this role gives access to every function of every module.'|i18n( 'design/admin/role/list' )}">{'Full access'|i18n( 'design/admin/role/list' )}</li>
                {elseif and( $summary, $summary.manages_roles )}
                <li class="exp-badge is-warn" title="{'Its users can change roles and policies, and so give themselves any access.'|i18n( 'design/admin/role/list' )}">{'Can change roles'|i18n( 'design/admin/role/list' )}</li>
                {/if}
                {if $assigned|eq( 0 )}
                <li class="exp-badge" title="{'Not assigned to any user or group, so it gives nobody anything.'|i18n( 'design/admin/role/list' )}">{'Not assigned'|i18n( 'design/admin/role/list' )}</li>
                {/if}
            </ul>
        </div>
        <div class="exp-actions">
            <a class="exp-btn exp-btn-small" href={concat( '/role/view/', $role_id )|ezurl} aria-describedby="{$card_id}-title">{'View'|i18n( 'design/admin/role/list' )}</a>
            <a class="exp-btn exp-btn-small" href={concat( '/role/edit/', $role_id )|ezurl} aria-describedby="{$card_id}-title" title="{'Edit the <%role_name> role.'|i18n( 'design/admin/role/list',, hash( '%role_name', $role.name ) )|wash}">{'Edit'|i18n( 'design/admin/role/list' )}</a>
            <a class="exp-btn exp-btn-small" href={concat( '/role/assign/', $role_id )|ezurl} aria-describedby="{$card_id}-title" title="{'Assign the <%role_name> role to a user or a user group.'|i18n( 'design/admin/role/list',, hash( '%role_name', $role.name ) )|wash}">{'Assign'|i18n( 'design/admin/role/list' )}</a>
            <a class="exp-btn exp-btn-small" href={concat( '/role/copy/', $role_id )|ezurl} aria-describedby="{$card_id}-title" title="{'Copy the <%role_name> role.'|i18n( 'design/admin/role/list',, hash( '%role_name', $role.name ) )|wash}">{'Copy'|i18n( 'design/admin/role/list' )}</a>
        </div>
    </div>
    <dl class="exp-facts">
        <div><dt>{'ID'|i18n( 'design/admin/role/list' )}</dt><dd class="role-id">{$role_id}</dd></div>
        <div><dt>{'Policies'|i18n( 'design/admin/role/list' )}</dt><dd>{if $summary}{$summary.policies}{else}&ndash;{/if}</dd></div>
        <div><dt>{'Assigned to users and groups'|i18n( 'design/admin/role/list' )}</dt><dd class="role-assignment-count"><a href={concat( '/role/view/', $role_id )|ezurl} title="{'Show the users and user groups of the <%role_name> role.'|i18n( 'design/admin/role/list',, hash( '%role_name', $role.name ) )|wash}">{$assigned}</a></dd></div>
    </dl>
</li>
    {undef $role_id $summary $assigned $card_id}
{/foreach}
</ul>
{/if}

<div class="exp-listfoot">
    {* The sizes come from site.ini [RoleSettings] RolesPerPageList; the preference stores the position in that list. *}
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/role/list' )}:</span>
    {foreach $limit_choices as $limit_index => $limit_option}
        {if eq( $limit_index|inc, $limit_choice )}
        <span class="current" aria-current="true">{$limit_option}</span>
        {else}
        <a href={concat( '/user/preferences/set/admin_role_list_limit/', $limit_index|inc )|ezurl} title="{'Show %count items per page.'|i18n( 'design/admin/role/list',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri='/role/list'
             page_uri_suffix=$search_suffix
             item_count=$match_count
             view_parameters=$view_parameters
             item_limit=$limit}
    </div>
</div>
</section>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-primary" name="NewButton" value="1" title="{'Create a new role.'|i18n( 'design/admin/role/list' )}">{'New role'|i18n( 'design/admin/role/list' )}</button>
        {if $roles|count|gt( 0 )}
        <details class="exp-confirm">
            <summary>{'Remove selected'|i18n( 'design/admin/role/list' )}</summary>
            <div>
                <p>{'The ticked roles are removed for good, with their policies. Every user and group they are assigned to loses what they gave. This cannot be undone.'|i18n( 'design/admin/role/list' )} <span id="role-selected-count" aria-live="polite"></span></p>
                <button type="submit" class="exp-btn exp-btn-danger" name="RemoveButton" value="1" title="{'Remove selected roles.'|i18n( 'design/admin/role/list' )}">{'Remove the ticked roles'|i18n( 'design/admin/role/list' )}</button>
            </div>
        </details>
        {/if}
    </div>
</div>

</form>

</div></div></div>
</div>

<script type="text/javascript">
var expRoleListText = {ldelim}
    selected: '{'%count selected.'|i18n( 'design/admin/role/list' )|wash( javascript )}',
    none: '{'Nothing is ticked yet.'|i18n( 'design/admin/role/list' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var list = document.getElementById( 'role-list' );
    var nodes = document.querySelectorAll( '.exp-roles .exp-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    if ( !list ) return;
    var selectAll = document.getElementById( 'role-select-all' );
    var countEl = document.getElementById( 'role-selected-count' );
    function boxes() { return list.querySelectorAll( 'input[name="DeleteIDArray[]"]' ); }
    function update() {
        var all = boxes(), n = 0, j;
        for ( j = 0; j < all.length; j++ ) {
            var card = all[j].closest( '.exp-card' );
            if ( card ) card.classList.toggle( 'is-selected', all[j].checked );
            if ( all[j].checked ) n++;
        }
        if ( countEl ) countEl.textContent = n ? expRoleListText.selected.split( '%count' ).join( n ) : expRoleListText.none;
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
