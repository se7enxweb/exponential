{* A role (role/view/<id>).

   What the role holds and whom it reaches (the figures: policies, users and groups it is assigned to, and how many
   users that is in all, counting the members of its groups once each), a warning when a policy gives access to
   everything or lets its users change roles, then its policies, each as a sentence ("May read content, in section
   Standard, of the classes Article and Folder") with its module, function and limitations under it, and the users
   and groups it is assigned to, with their limitation, a name filter and paging.

   The two lists keep their place in the address as before: the policies on (policy_offset), (policy_sort) and
   (policy_dir), the assignments on (assignment_offset) and (assignment_filter). Ordered by module, the policies are
   grouped under their module. Every name the view reads is kept (EditRoleButton, AssignRoleButton,
   AssignRoleLimitedButton, AssignRoleType, RemoveRoleAssignmentButton, IDArray[], AssignmentFilter,
   AssignmentFilterButton, AssignmentFilterClearButton) and every template variable is still set. Removing
   assignments asks first, in place. The same file is in design/admin and design/admin4.
   Guide: doc/guides/roles-and-policies.md *}
{include uri='design:role/exp_style.tpl'}

{def $sentences = first_set( $policy_sentences, hash() )
     $summary = first_set( $role_summary, false() )
     $affected = first_set( $role_affected, false() )
     $removed = first_set( $assignments_removed, false() )
     $grouped = eq( $policy_sort.field, 'module' )
     $last_module = false()}

<form name="role" method="post" action={concat( $module.functions.view.uri, '/', $role.id, $policy_uri_suffix, $assignment_uri_suffix )|ezurl} class="exp-roles">
{* Enter in the name filter of the assignments filters: the first button of a form is the one Enter presses, and it must not be Edit. *}
{if or( $assignment_total|gt( $assignment_limit ), $assignment_filter )}<input type="submit" name="AssignmentFilterButton" value="{'Filter'|i18n( 'design/admin/role/view' )}" tabindex="-1" aria-hidden="true" class="exp-hidden-default exp-sr" />{/if}

<div class="context-block">

<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
<h1 class="context-title">{'%role_name [Role]'|i18n( 'design/admin/role/view',, hash( '%role_name', $role.name ) )|wash}</h1>
<span class="exp-meta">{'ID %id'|i18n( 'design/admin/role/view',, hash( '%id', $role.id ) )}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-actionbar">
    <p class="exp-meta">{'A user has every policy of every role assigned to them or to one of their groups. Changing the policies of this role changes what all of them may do.'|i18n( 'design/admin/role/view' )}</p>
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="EditRoleButton" value="1" title="{'Edit this role.'|i18n( 'design/admin/role/view' )}">{'Edit'|i18n( 'design/admin/role/view' )}</button>
        <a class="exp-btn" href={concat( '/role/copy/', $role.id )|ezurl}>{'Copy'|i18n( 'design/admin/role/view' )}</a>
        <a class="exp-btn" href={'/role/list'|ezurl}>{'All roles'|i18n( 'design/admin/role/view' )}</a>
    </div>
</div>

{if $removed|ne( false() )}
    {if $removed|gt( 0 )}
<div class="exp-feedback is-ok" role="status">{'%count assignments were removed. Those users and groups no longer have the policies of this role.'|i18n( 'design/admin/role/view',, hash( '%count', $removed ) )}</div>
    {else}
<div class="exp-feedback is-warn" role="alert">{'No assignment was selected. Tick the users and groups to remove first.'|i18n( 'design/admin/role/view' )}</div>
    {/if}
{/if}

<section aria-labelledby="role-overview-title">
<h2 class="exp-sr" id="role-overview-title">{'Overview'|i18n( 'design/admin/role/view' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$policy_count}</strong> <span>{'Policies'|i18n( 'design/admin/role/view' )}</span></li>
    <li class="exp-figure"><strong>{$assignment_total}</strong> <span>{'Assignments to users and groups'|i18n( 'design/admin/role/view' )}</span></li>
    {if $affected}
    <li class="exp-figure"><strong>{$affected.groups}</strong> <span>{'User groups'|i18n( 'design/admin/role/view' )}</span></li>
    <li class="exp-figure"><strong>{$affected.users_direct}</strong> <span>{'Users assigned directly'|i18n( 'design/admin/role/view' )}</span></li>
    <li class="exp-figure{if and( $summary, $summary.full_access, $affected.users_total|gt( 0 ) )} is-attention{/if}"><strong>{if is_null( $affected.users_total )}&ndash;{else}{$affected.users_total}{/if}</strong> <span>{'Users affected in all'|i18n( 'design/admin/role/view' )}</span></li>
    {/if}
</ul>
{if and( $affected, $affected.limited|gt( 0 ) )}
<p class="exp-who">{'%count of the assignments have a subtree or section limitation: the role applies to those users only there. They are counted above.'|i18n( 'design/admin/role/view',, hash( '%count', $affected.limited ) )}</p>
{/if}
</section>

{if and( $summary, $summary.full_access )}
<div class="exp-feedback is-bad" role="note"><strong>{'Full access.'|i18n( 'design/admin/role/view' )}</strong> {'A policy of this role gives access to every function of every module, including roles, users and setup. Assign it only to administrators.'|i18n( 'design/admin/role/view' )}</div>
{elseif and( $summary, $summary.manages_roles )}
<div class="exp-feedback is-warn" role="note"><strong>{'Can change roles.'|i18n( 'design/admin/role/view' )}</strong> {'Its users may change roles and policies, and so give themselves any access.'|i18n( 'design/admin/role/view' )}</div>
{/if}

{* ---- The policies ---- *}
<section class="exp-section" aria-labelledby="role-policies-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="role-policies-title">{'Policies (%policies_count)'|i18n( 'design/admin/role/view',, hash( '%policies_count', $policy_count ) )}</h2>
    {if $policy_count|gt( 0 )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/role/view',, hash( '%from', sum( $view_parameters.policy_offset, 1 ), '%to', min( sum( $view_parameters.policy_offset, $policy_limit ), $policy_count ), '%count', $policy_count ) )}</span>
    <ul class="exp-tabs" aria-label="{'Order of the policies'|i18n( 'design/admin/role/view' )}">
    {foreach array( hash( 'key', 'id', 'text', 'Role order'|i18n( 'design/admin/role/view' ) ),
                    hash( 'key', 'module', 'text', 'By module'|i18n( 'design/admin/role/view' ) ),
                    hash( 'key', 'function', 'text', 'By function'|i18n( 'design/admin/role/view' ) ),
                    hash( 'key', 'limitation', 'text', 'By limitation'|i18n( 'design/admin/role/view' ) ) ) as $tab}
        {if eq( $tab.key, $policy_sort.field )}
        <li><a class="current" aria-current="true" href={concat( $policy_page_uri, '/(policy_sort)/', $tab.key, '/(policy_dir)/', $policy_sort.opposite, $assignment_uri_suffix )|ezurl} title="{'Reverse the order'|i18n( 'design/admin/role/view' )}">{$tab.text|wash} {if eq( $policy_sort.direction, 'asc' )}&#8593;{else}&#8595;{/if}</a></li>
        {else}
        <li><a href={concat( $policy_page_uri, '/(policy_sort)/', $tab.key, '/(policy_dir)/asc', $assignment_uri_suffix )|ezurl}>{$tab.text|wash}</a></li>
        {/if}
    {/foreach}
    </ul>
    {/if}
</div>

{if $policies}
<ul class="exp-policies" id="role-policies">
{foreach $policies as $policy}
    {def $said = first_set( $sentences[$policy.id], false() )}
    {if and( $grouped, ne( $policy.module_name, $last_module ) )}
        {set $last_module = $policy.module_name}
</ul>
<div class="exp-group-head"><h3>{if eq( $policy.module_name, '*' )}{'Every module'|i18n( 'design/admin/role/view' )}{else}{'Module %module'|i18n( 'design/admin/role/view',, hash( '%module', $policy.module_name ) )|wash}{/if}</h3></div>
<ul class="exp-policies">
    {/if}
<li class="exp-policy{if and( $said, $said.full_access )} is-full{elseif and( $said, $said.manages_roles )} is-roles{/if}" id="policy-{$policy.id}">
    <div class="exp-policy-main">
        <p class="exp-sentence">{if $said}{$said.sentence|wash}{else}{$policy.module_name|wash} / {$policy.function_name|wash}{/if}</p>
        <p class="exp-policy-detail">
            <span class="exp-sr">{'Policy'|i18n( 'design/admin/role/view' )} </span><code>#{$policy.id}</code>
            <code>{if eq( $policy.module_name, '*' )}{'all modules'|i18n( 'design/admin/role/view' )}{else}{$policy.module_name|wash}{/if} / {if eq( $policy.function_name, '*' )}{'all functions'|i18n( 'design/admin/role/view' )}{else}{$policy.function_name|wash}{/if}</code>
            {if $policy.limitations}
            &middot;
            {foreach $policy.limitations as $limitation}
                {$limitation.label|wash}{if $limitation.denies_without_handler} <em class="limitation-denies exp-denies" title="{'No extension handler evaluates this limitation, so this policy gives no access'|i18n( 'design/admin/role/view' )|wash}">{'(no handler, denies)'|i18n( 'design/admin/role/view' )|wash}</em>{/if}:
                {foreach $limitation.values_as_array_with_names as $limitation_value}{if is_set( $limitation_value.node_data )}<a href={concat( 'content/view/full/', $limitation_value.node_data.node_id )|ezurl} title="{'Path: \'/%path_string\', Class identifier: \'%class_identifier\''|i18n( 'design/admin/role/view',, hash( '%path_string', $limitation_value.node_data.path_identification_string, '%class_identifier', $limitation_value.node_data.class_identifier ) )|wash}">{$limitation_value.Name|wash}</a>{else}{$limitation_value.Name|wash}{/if}{delimiter}, {/delimiter}{/foreach}{delimiter}; {/delimiter}
            {/foreach}
            {else}
            &middot; {'No limitations'|i18n( 'design/admin/role/view' )}
            {/if}
        </p>
    </div>
</li>
    {undef $said}
{/foreach}
</ul>
{* Paged in the database on its own offset; a role can carry more policies than a screen can draw. *}
{if $policy_count|gt( $policy_limit )}
<div class="exp-listfoot"><div class="exp-pager">
{include name=PolicyNavigator
         uri='design:navigator/google.tpl'
         offset_name='policy_offset'
         page_uri=$policy_page_uri
         item_count=$policy_count
         view_parameters=$view_parameters
         item_limit=$policy_limit}
</div></div>
{/if}
{else}
<p class="exp-empty">{'There are no policies set up for this role.'|i18n( 'design/admin/role/view' )}</p>
{/if}
</section>

{* ---- The users and groups ---- *}
<section class="exp-section" aria-labelledby="role-users-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="role-users-title">{'Users and groups using the <%role_name> role (%users_count)'|i18n( 'design/admin/role/view',, hash( '%role_name', $role.name, '%users_count', $assignment_total ) )|wash}</h2>
    <p>{'Assign the role to a user group rather than to single users where you can: every member of the group, now and later, gets it.'|i18n( 'design/admin/role/view' )}</p>
</div>

{* A name filter, kept in the address as (assignment_filter); both pagers keep it. *}
{if or( $assignment_total|gt( $assignment_limit ), $assignment_filter )}
<div class="exp-toolbar role-assignment-filter">
    <div class="exp-field exp-field-wide">
        <label for="role-assignment-filter">{'Name contains'|i18n( 'design/admin/role/view' )}</label>
        <div class="exp-searchrow">
            <input type="search" id="role-assignment-filter" name="AssignmentFilter" value="{$assignment_filter|wash}" maxlength="100" title="{'Show only the users and user groups whose name contains this text.'|i18n( 'design/admin/role/view' )}" />
            <button class="exp-btn exp-btn-primary" type="submit" name="AssignmentFilterButton" value="1">{'Filter'|i18n( 'design/admin/role/view' )}</button>
            {if $assignment_filter}<button class="exp-btn" type="submit" name="AssignmentFilterClearButton" value="1">{'Show all'|i18n( 'design/admin/role/view' )}</button>{/if}
        </div>
        {if $assignment_filter}<span class="exp-help">{'%count of %total match "%filter".'|i18n( 'design/admin/role/view',, hash( '%count', $assignment_count, '%total', $assignment_total, '%filter', $assignment_filter ) )|wash}</span>{/if}
    </div>
</div>
{/if}

{if $assignment_orphan_count}
<div class="exp-feedback is-warn role-assignment-orphans">{'%count of these assignments belong to a user or user group that no longer exists. They are listed first and can be removed.'|i18n( 'design/admin/role/view',, hash( '%count', $assignment_orphan_count ) )|wash}</div>
{/if}

{if $user_array}
<div class="exp-table-wrap">
<table class="exp-table" id="role-assignments">
<thead><tr>
    <th scope="col"><span class="exp-sr">{'Select'|i18n( 'design/admin/role/view' )}</span><input type="checkbox" class="exp-js-only" hidden id="role-assign-all" aria-label="{'Select all on this page'|i18n( 'design/admin/role/view' )}" /></th>
    <th scope="col">{'User/group'|i18n( 'design/admin/role/view' )}</th>
    <th scope="col">{'Limitation'|i18n( 'design/admin/role/view' )}</th>
</tr></thead>
<tbody>
{foreach $user_array as $assignment}
<tr>
    <td><input type="checkbox" value="{$assignment.user_role_id}" name="IDArray[]" aria-label="{'Select %name for removal'|i18n( 'design/admin/role/view',, hash( '%name', cond( $assignment.user_object, $assignment.user_name, $assignment.user_id ) ) )|wash}" title="{'Select user or user group for removal.'|i18n( 'design/admin/role/view' )}" /></td>
    <td>
        {if $assignment.user_object}
            {$assignment.user_object.class_identifier|class_icon( 'small', $assignment.user_object.class_name|wash )}&nbsp;{if $assignment.main_node_id}<a href={concat( '/content/view/full/', $assignment.main_node_id )|ezurl}>{$assignment.user_name|wash}</a>{else}{$assignment.user_name|wash}{/if}
        {else}
            <i class="role-assignment-orphan">{'User or user group no longer exists (object %object_id)'|i18n( 'design/admin/role/view',, hash( '%object_id', $assignment.user_id ) )|wash}</i>
        {/if}
    </td>
    <td>
        {if $assignment.limit_ident}
            {if $assignment.limit_node}
              <a href={concat( '/content/view/full/', $assignment.limit_node.node_id )|ezurl} title="{'Path: \'/%path_string\', Class identifier: \'%class_identifier\''|i18n( 'design/admin/role/view',, hash( '%path_string', $assignment.limit_node.path_identification_string, '%class_identifier', $assignment.limit_node.class_identifier ) )|wash}">{'Only in the subtree %name'|i18n( 'design/admin/role/view',, hash( '%name', $assignment.limit_node.name ) )|wash}</a>
            {elseif $assignment.limit_section}
              <a href={concat( '/section/view/', $assignment.limit_value|wash( url ) )|ezurl}>{'Only in section %name'|i18n( 'design/admin/role/view',, hash( '%name', $assignment.limit_section.name ) )|wash}</a>
            {else}
              {$assignment.limit_ident|wash}:&nbsp;({$assignment.limit_value|wash})&nbsp;<i>{'not found'|i18n( 'design/admin/role/view' )}</i>
            {/if}
        {else}
        <span class="exp-muted">{'No limitations'|i18n( 'design/admin/role/view' )}</span>
        {/if}
    </td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{if $assignment_count|gt( $assignment_limit )}
<div class="exp-listfoot"><div class="exp-pager">
{include name=AssignmentNavigator
         uri='design:navigator/google.tpl'
         offset_name='assignment_offset'
         page_uri=$policy_page_uri
         item_count=$assignment_count
         view_parameters=$view_parameters
         item_limit=$assignment_limit}
</div></div>
{/if}
{else}
<p class="exp-empty">
{if $assignment_filter}
{'No user or user group of this role has a name containing "%filter".'|i18n( 'design/admin/role/view',, hash( '%filter', $assignment_filter ) )|wash}
{else}
{'This role is not assigned to any users or user groups.'|i18n( 'design/admin/role/view' )}
{/if}
</p>
{/if}

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="AssignRoleButton" value="1" title="{'Assign the <%role_name> role to a user or a user group.'|i18n( 'design/admin/role/view',, hash( '%role_name', $role.name ) )|wash}">{'Assign'|i18n( 'design/admin/role/view' )}</button>
        <span class="exp-field">
            <label for="role-assign-type" class="exp-sr">{'Select limitation.'|i18n( 'design/admin/role/view' )}</label>
            <select id="role-assign-type" name="AssignRoleType" title="{'Select limitation.'|i18n( 'design/admin/role/view' )}">
                <option value="subtree">{'Subtree'|i18n( 'design/admin/role/view' )}</option>
                <option value="section">{'Section'|i18n( 'design/admin/role/view' )}</option>
            </select>
        </span>
        <button class="exp-btn" type="submit" name="AssignRoleLimitedButton" value="1" title="{'Assign the <%role_name> role with limitation (specified to the left) to a user or a user group.'|i18n( 'design/admin/role/view',, hash( '%role_name', $role.name ) )|wash}">{'Assign with limitation'|i18n( 'design/admin/role/view' )}</button>
        {if $user_array}
        <details class="exp-confirm">
            <summary>{'Remove selected'|i18n( 'design/admin/role/view' )}</summary>
            <div>
                <p>{'The ticked users and groups lose this role at once: they keep only what other roles give them. The users and groups themselves are not removed.'|i18n( 'design/admin/role/view' )}</p>
                <button class="exp-btn exp-btn-danger" type="submit" name="RemoveRoleAssignmentButton" value="1" title="{'Remove selected users and/or user groups.'|i18n( 'design/admin/role/view' )}">{'Remove the ticked assignments'|i18n( 'design/admin/role/view' )}</button>
            </div>
        </details>
        {/if}
    </div>
    <p class="exp-meta">{'With a limitation, the role applies only inside one subtree or section. You choose the users and groups in the next step.'|i18n( 'design/admin/role/view' )}</p>
</div>
</section>

</div></div></div>
</div>
</form>

<script type="text/javascript">
{literal}
(function () {
    var all = document.getElementById( 'role-assign-all' ), table = document.getElementById( 'role-assignments' );
    if ( !all || !table ) return;
    all.hidden = false;
    all.addEventListener( 'change', function () {
        var boxes = table.querySelectorAll( 'input[name="IDArray[]"]' ), i;
        for ( i = 0; i < boxes.length; i++ ) boxes[i].checked = all.checked;
    } );
})();
{/literal}
</script>
