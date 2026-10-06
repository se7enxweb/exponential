{* One section (section/view/<id>).

   The section's facts and actions first (Edit, Assign content, back to the list), then how it is used: its objects
   by status, the roles whose policies are limited to it, the users and groups with a role assigned for it, whether
   it could be removed and why not, and the objects in it, a page at a time.

   The same file is in design/admin and design/admin4. The usage comes from the view (section_usage); without it (an
   older view class) the page falls back to the section's own fetch functions. Works without javascript.
   Guide: doc/guides/sections.md *}
{include uri='design:section/exp_style.tpl'}

{def $usage = first_set( $section_usage, false() )
     $can_edit = first_set( $section_can_edit, true() )
     $can_assign = first_set( $section_can_assign, true() )
     $user_roles = fetch( 'section', 'user_roles', hash( 'section_id', $section.id ) )
     $item_type = ezpreference( 'admin_list_limit' )
     $number_of_items = min( $item_type, 3 )|choose( 10, 10, 25, 50 )
     $objects_count = fetch( 'section', 'object_list_count', hash( 'section_id', $section.id ) )
     $roles = array()}
{if $usage}
    {set $roles = $usage.roles}
{else}
    {def $roles_array = fetch( 'section', 'roles', hash( 'section_id', $section.id ) )}
    {foreach $roles_array.roles as $role}
        {def $functions = array()}
        {foreach $roles_array.limited_policies[$role.id] as $policy}{set $functions = $functions|append( concat( $policy.module_name, '/', $policy.function_name ) )}{/foreach}
        {set $roles = $roles|append( hash( 'id', $role.id, 'name', $role.name, 'functions', $functions|unique ) )}
        {undef $functions}
    {/foreach}
{/if}

<div class="context-block exp-sections">

<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
<h1 class="context-title">{$section.name|wash}</h1>
{if $section.identifier|ne( '' )}<code class="exp-title-key">{$section.identifier|wash}</code>{/if}
<span class="exp-meta">{'Section, ID %id'|i18n( 'design/admin/section/view',, hash( '%id', $section.id ) )}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Every object in this section is affected by what the section stands for: the policies limited to it, its navigation part, and templates overridden for it.'|i18n( 'design/admin/section/view' )}</p>

<div class="exp-actionbar">
    <div class="exp-actions">
        {if $can_edit}
        <form method="post" action={concat( '/section/edit/', $section.id )|ezurl}>
            <button type="submit" class="exp-btn exp-btn-primary" name="_DefaultButton" value="1" title="{'Edit this section.'|i18n( 'design/admin/section/view' )}">{'Edit'|i18n( 'design/admin/section/view' )}</button>
        </form>
        {/if}
        {if $can_assign}
        <form method="post" action={concat( '/section/assign/', $section.id )|ezurl}>
            <button type="submit" class="exp-btn" name="_DefaultButton" value="1" title="{'Assign subtree of objects to this section'|i18n( 'design/admin/section/view' )}">{'Assign content'|i18n( 'design/admin/section/view' )}</button>
        </form>
        {/if}
        <a class="exp-btn" href={'/section/list'|ezurl}>{'All sections'|i18n( 'design/admin/section/view' )}</a>
    </div>
    {if $can_assign}
    <p class="exp-meta">{'Assign content picks one item; it and everything below it move to this section.'|i18n( 'design/admin/section/view' )}</p>
    {/if}
</div>

{if $usage}
<section aria-labelledby="section-overview-title">
<h2 class="exp-sr" id="section-overview-title">{'Overview'|i18n( 'design/admin/section/view' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$usage.published}</strong><span>{'Published objects'|i18n( 'design/admin/section/view' )}</span></li>
    <li class="exp-figure"><strong>{$usage.drafts}</strong><span>{'Drafts of new objects'|i18n( 'design/admin/section/view' )}</span></li>
    <li class="exp-figure"><strong>{$usage.archived}</strong><span>{'Archived objects'|i18n( 'design/admin/section/view' )}</span></li>
    <li class="exp-figure"><strong>{$usage.role_count}</strong><span>{'Roles with policies for it'|i18n( 'design/admin/section/view' )}</span></li>
    <li class="exp-figure"><strong>{$usage.assignment_count}</strong><span>{'Role assignments limited to it'|i18n( 'design/admin/section/view' )}</span></li>
</ul>
</section>
{/if}

<section class="exp-panel" aria-labelledby="section-facts-title">
<h2 class="exp-sr" id="section-facts-title">{'Details'|i18n( 'design/admin/section/view' )}</h2>
<dl class="exp-facts">
    <div>
        <dt>{'Name'|i18n( 'design/admin/section/view' )}</dt>
        <dd>{$section.name|wash}</dd>
    </div>
    <div>
        <dt>{'Identifier'|i18n( 'design/admin/section/view' )}</dt>
        <dd>{if $section.identifier|ne( '' )}<code>{$section.identifier|wash}</code>{else}<span class="exp-badge is-warn">{'None'|i18n( 'design/admin/section/view' )}</span> {'Templates and fetches cannot name it; set one with Edit.'|i18n( 'design/admin/section/view' )}{/if}</dd>
    </div>
    <div>
        <dt>{'ID'|i18n( 'design/admin/section/view' )}</dt>
        <dd>{$section.id}</dd>
    </div>
    <div>
        <dt>{'Navigation part'|i18n( 'design/admin/section/view' )}</dt>
        <dd>{if $usage}{$usage.navigation_part_name|wash} <code>{$usage.navigation_part|wash}</code>{if $usage.navigation_part_known|not} <span class="exp-badge is-warn">{'Unknown navigation part'|i18n( 'design/admin/section/view' )}</span>{/if}{else}<code>{$section.navigation_part_identifier|wash}</code>{/if}</dd>
    </div>
    {if $usage}
    <div class="exp-field-wide">
        <dt>{'Removal'|i18n( 'design/admin/section/view' )}</dt>
        <dd>{if $usage.removable}<span class="exp-badge is-ok">{'Can be removed'|i18n( 'design/admin/section/view' )}</span> {'Nothing uses it: no objects, no policies, no role assignments.'|i18n( 'design/admin/section/view' )}{else}<span class="exp-badge">{'In use'|i18n( 'design/admin/section/view' )}</span> {'It can only be removed when no object is in it (drafts and archived objects included) and no policy or role assignment names it.'|i18n( 'design/admin/section/view' )}{/if}</dd>
    </div>
    {/if}
</dl>
</section>

{* The roles whose policies are limited to this section. *}
<section class="exp-section" aria-labelledby="section-roles-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="section-roles-title">{'Roles with policies limited to this section (%number_of_roles)'|i18n( 'design/admin/section/view',, hash( '%number_of_roles', $roles|count ) )}</h2>
    <p>{'A policy with a Section limitation applies only to objects in the sections it names, for example content/read for Standard.'|i18n( 'design/admin/section/view' )}</p>
</div>
{if $roles|count}
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr>
    <th scope="col">{'Role'|i18n( 'design/admin/section/view' )}</th>
    <th scope="col">{'Limited policies'|i18n( 'design/admin/section/view' )}</th>
</tr></thead>
<tbody>
{foreach $roles as $role}
<tr>
    <td><a href={concat( '/role/view/', $role.id )|ezurl}>{$role.name|wash}</a> <span class="exp-meta">{'ID %id'|i18n( 'design/admin/section/view',, hash( '%id', $role.id ) )}</span></td>
    <td>{foreach $role.functions as $function}<span class="exp-fn">{$function|wash}</span>{/foreach}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{else}
<p class="exp-empty">{'This section is not used to limit the policies of any role.'|i18n( 'design/admin/section/view' )}</p>
{/if}
</section>

{* Users and groups whose role is assigned for this section only. *}
<section class="exp-section" aria-labelledby="section-users-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="section-users-title">{'Users and user groups with role limitations associated with this section (%number_of_roles)'|i18n( 'design/admin/section/view',, hash( '%number_of_roles', $user_roles|count ) )}</h2>
    <p>{'A role assigned with the limitation to a section gives its policies only for objects in that section.'|i18n( 'design/admin/section/view' )}</p>
</div>
{if $user_roles|count}
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr>
    <th scope="col">{'User or user group'|i18n( 'design/admin/section/view' )}</th>
    <th scope="col">{'Role'|i18n( 'design/admin/section/view' )}</th>
</tr></thead>
<tbody>
{foreach $user_roles as $user_role}
<tr>
    <td>{if $user_role.user}{if $user_role.user.main_node}<a href={$user_role.user.main_node.url_alias|ezurl}>{$user_role.user.name|wash}</a>{else}{$user_role.user.name|wash}{/if}{/if}</td>
    <td>{if $user_role.role}<a href={concat( '/role/view/', $user_role.role.id )|ezurl}>{$user_role.role.name|wash}</a>{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{else}
<p class="exp-empty">{'This section is not used for limiting roles that are assigned to users or user groups.'|i18n( 'design/admin/section/view' )}</p>
{/if}
</section>

{* The published objects of the section, a page at a time. *}
<section class="exp-section" aria-labelledby="section-objects-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="section-objects-title">{'Objects within this section (%number_of_objects)'|i18n( 'design/admin/section/view',, hash( '%number_of_objects', $objects_count ) )}</h2>
    {if $objects_count|gt( $number_of_items )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/section/view',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $number_of_items ), $objects_count ), '%count', $objects_count ) )}</span>
    {/if}
    <p>{'Published objects, newest first. Drafts and archived objects are counted above but not listed.'|i18n( 'design/admin/section/view' )}</p>
</div>
{if $objects_count}
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr>
    <th scope="col">{'Name'|i18n( 'design/admin/section/view' )}</th>
    <th scope="col">{'Type'|i18n( 'design/admin/section/view' )}</th>
    <th scope="col" class="exp-num">{'Modified'|i18n( 'design/admin/section/view' )}</th>
</tr></thead>
<tbody>
{foreach fetch( 'section', 'object_list', hash( 'section_id', $section.id, 'limit', $number_of_items, 'offset', $view_parameters.offset ) ) as $object}
<tr>
    <td>{if $object.main_node_id}<a href={$object.main_node.url_alias|ezurl}>{$object.name|wash}</a>{else}{$object.name|wash}{/if}</td>
    <td>{$object.class_name|wash}</td>
    <td class="exp-num">{$object.modified|l10n( shortdatetime )}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
<div class="exp-listfoot">
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=concat( 'section/view/', $section.id )
             item_count=$objects_count
             view_parameters=$view_parameters
             item_limit=$number_of_items}
    </div>
</div>
{else}
<p class="exp-empty">{'This section is not assigned to any objects.'|i18n( 'design/admin/section/view' )}{if $can_assign} {'Use Assign content to move a subtree into it.'|i18n( 'design/admin/section/view' )}{/if}</p>
{/if}
</section>

</div></div></div>
</div>
