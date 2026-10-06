{* The object state groups: what states are, a short overview, then one card per group with its states in order,
   the objects in each, its languages, and the roles whose policies use it. Removing groups asks first, on a page
   of its own that says what goes and what it touches; the view removes only when the confirmation is posted
   (ConfirmRemove).

   The same file is in design/admin and design/admin4. Everything works without javascript: selecting is a
   checkbox, every action a submit button, the confirmation a page of its own. *}
{include uri='design:state/style.tpl'}

<div class="context-block exp-states">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Object states'|i18n( 'design/admin/state/groups' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'An object state is a label every content object carries, one from each state group: a stage of your own such as draft, in review and approved. States change nothing by themselves; roles use them to decide who may read, edit or publish what, and templates and searches can ask for them. New objects get the first state of each group.'|i18n( 'design/admin/state/groups' )}</p>

{foreach $state_feedback as $state_message}
<div class="exp-feedback {if $state_message.ok}is-ok{else}is-bad{/if}" role="{if $state_message.ok}status{else}alert{/if}">{$state_message.message|wash}</div>
{/foreach}

{if $confirm_remove}
{* The confirmation. Nothing has been removed yet. *}
<form method="post" action={'/state/groups'|ezurl}>
    {if $confirm_remove.groups|count|gt(0)}
    <div class="exp-feedback is-warn" role="alert">
        <h2>{if $confirm_remove.groups|count|eq(1)}{'Remove this state group?'|i18n( 'design/admin/state/groups' )}{else}{'Remove %count state groups?'|i18n( 'design/admin/state/groups',, hash( '%count', $confirm_remove.groups|count ) )}{/if}</h2>
        <p>{'A removed group and its states are gone for good, with their translations. This cannot be undone.'|i18n( 'design/admin/state/groups' )}</p>
    </div>

    <ul class="exp-cards">
    {foreach $confirm_remove.groups as $remove_group}
        <li class="exp-card">
            <div class="exp-card-title">
                <h3>{$remove_group.name|wash}</h3>
                <code class="exp-key">{$remove_group.identifier|wash}</code>
            </div>
            <ul class="exp-flow" aria-label="{'States of %group'|i18n( 'design/admin/state/groups',, hash( '%group', $remove_group.name ) )|wash}">
            {foreach $remove_group.states as $remove_state}
                <li><span class="exp-chip">{$remove_state.name|wash} <span class="exp-count">{'%count objects'|i18n( 'design/admin/state/groups',, hash( '%count', $remove_state.object_count ) )}</span></span></li>
            {/foreach}
            </ul>
            <ul class="exp-roles exp-plain">
                <li>{if $remove_group.objects|gt(0)}{'%count objects lose their state in this group.'|i18n( 'design/admin/state/groups',, hash( '%count', $remove_group.objects ) )}{else}{'No object has a state of this group.'|i18n( 'design/admin/state/groups' )}{/if}</li>
                {if $remove_group.roles|count|gt(0)}
                <li>{'These roles have policies that use the group. A policy limited by %limitation matches no object any more and stops granting access; edit those policies first:'|i18n( 'design/admin/state/groups',, hash( '%limitation', $remove_group.limitation ) )|wash}
                    {foreach $remove_group.roles as $remove_role}{delimiter}, {/delimiter}<a href={concat( '/role/view/', $remove_role.role_id )|ezurl}>{$remove_role.role_name|wash}</a> <span class="exp-muted">({$remove_role.policies|implode( ', ' )|wash})</span>{/foreach}</li>
                {else}
                <li>{'No role policy uses this group.'|i18n( 'design/admin/state/groups' )}</li>
                {/if}
            </ul>
        </li>
    {/foreach}
    </ul>
    {/if}

    {if $confirm_remove.skipped|count|gt(0)}
    <p class="exp-note">{'Left alone, because system groups belong to Exponential and cannot be removed:'|i18n( 'design/admin/state/groups' )} {foreach $confirm_remove.skipped as $skipped_group}{delimiter}, {/delimiter}<strong>{$skipped_group.name|wash}</strong>{/foreach}</p>
    {/if}

    <div class="exp-actions-bar">
        {foreach $confirm_remove.ids as $remove_id}<input type="hidden" name="RemoveIDList[]" value="{$remove_id}" />{/foreach}
        <input type="hidden" name="ConfirmRemove" value="1" />
        <div class="exp-actions">
            {if $confirm_remove.groups|count|gt(0)}
            <button type="submit" class="exp-btn exp-btn-danger is-solid" name="RemoveButton" value="1">{'Remove for good'|i18n( 'design/admin/state/groups' )}</button>
            {/if}
            <a class="exp-btn" href={'/state/groups'|ezurl}>{'Cancel'|i18n( 'design/admin/state/groups' )}</a>
        </div>
    </div>
</form>

{else}

{* The overview. *}
<section aria-labelledby="state-overview-title">
<h2 class="exp-sr" id="state-overview-title">{'Overview'|i18n( 'design/admin/state/groups' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$state_summary.groups}</strong><span>{'State groups'|i18n( 'design/admin/state/groups' )}</span></li>
    <li class="exp-figure"><strong>{$state_summary.states}</strong><span>{'States in them'|i18n( 'design/admin/state/groups' )}</span></li>
    <li class="exp-figure"><strong>{$state_summary.custom}</strong><span>{'Your own groups'|i18n( 'design/admin/state/groups' )}</span></li>
    <li class="exp-figure"><strong>{$state_summary.system}</strong><span>{'System groups'|i18n( 'design/admin/state/groups' )}</span></li>
    <li class="exp-figure"><strong>{$state_summary.roles}</strong><span>{'Roles that use states'|i18n( 'design/admin/state/groups' )}</span></li>
</ul>
</section>

<form action={'/state/groups'|ezurl} method="post" id="stateGroupList">

<section class="exp-section" aria-labelledby="state-groups-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="state-groups-title">{'State groups'|i18n( 'design/admin/state/groups' )}</h2>
    {if $group_count|gt( $limit )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/state/groups',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $group_count ), '%count', $group_count ) )}</span>
    {/if}
    <button type="submit" class="exp-btn exp-btn-primary" name="CreateButton" value="1"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 2h2v5h5v2H9v5H7V9H2V7h5z"/></svg>{'New state group'|i18n( 'design/admin/state/groups' )}</button>
</div>

{if $groups_info|count|eq(0)}
<div class="exp-empty">
    <p>{'There are no state groups yet. A group holds the states content can be in, for example "Review" with Draft, In review and Approved.'|i18n( 'design/admin/state/groups' )}</p>
</div>
{else}
<ul class="exp-cards" id="state-group-list">
{foreach $groups_info as $info}
    {def $group_url = concat( '/state/group/', $info.identifier )}
<li class="exp-card{if $info.internal} is-system{/if}" id="state-group-{$info.identifier|wash}">
    <div class="exp-card-head">
        <div class="exp-card-title">
            <h3><a href={$group_url|ezurl}>{$info.name|wash}</a></h3>
            <code class="exp-key">{$info.identifier|wash}</code>
            <ul class="exp-badges">
                {if $info.internal}
                <li class="exp-badge is-system" title="{'Belongs to Exponential; it cannot be edited or removed'|i18n( 'design/admin/state/groups' )|wash}"><svg width="12" height="12" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M5 7V5a3 3 0 0 1 6 0v2h1v7H4V7zm2 0h2V5a1 1 0 0 0-2 0z"/></svg>{'System, protected'|i18n( 'design/admin/state/groups' )}</li>
                {/if}
                <li class="exp-badge">{if $info.states|count|eq(1)}{'1 state'|i18n( 'design/admin/state/groups' )}{else}{'%count states'|i18n( 'design/admin/state/groups',, hash( '%count', $info.states|count ) )}{/if}</li>
                {if $info.roles|count|gt(0)}<li class="exp-badge is-info">{'Used by %count roles'|i18n( 'design/admin/state/groups',, hash( '%count', $info.roles|count ) )}</li>{/if}
            </ul>
        </div>
        <div class="exp-actions">
            {if $info.internal|not}
            <a class="exp-btn exp-btn-small" href={concat( '/state/group_edit/', $info.identifier )|ezurl} aria-label="{'Edit the group %group'|i18n( 'design/admin/state/groups',, hash( '%group', $info.name ) )|wash}">{'Edit'|i18n( 'design/admin/state/groups' )}</a>
            <label class="exp-select"><input type="checkbox" name="RemoveIDList[]" value="{$info.id}" data-identifier="{$info.identifier|wash}" /><span>{'Select'|i18n( 'design/admin/state/groups' )}<span class="exp-sr"> {$info.name|wash}</span></span></label>
            {/if}
            <a class="exp-btn exp-btn-small" href={$group_url|ezurl} aria-label="{'Open the group %group'|i18n( 'design/admin/state/groups',, hash( '%group', $info.name ) )|wash}">{'Open'|i18n( 'design/admin/state/groups' )}</a>
        </div>
    </div>

    {if $info.description|ne('')}<p class="exp-desc">{$info.description|wash}</p>{/if}

    {if $info.states|count|gt(0)}
    <ol class="exp-flow" aria-label="{'States of %group, in order'|i18n( 'design/admin/state/groups',, hash( '%group', $info.name ) )|wash}">
    {foreach $info.states as $state_info}
        <li><span class="exp-chip{if $state_info.is_default} is-default{/if}"><a href={concat( '/state/view/', $info.identifier, '/', $state_info.identifier )|ezurl}>{$state_info.name|wash}</a>{if $state_info.is_default} <span class="exp-badge is-ok">{'default'|i18n( 'design/admin/state/groups' )}</span>{/if} <span class="exp-count">{'%count objects'|i18n( 'design/admin/state/groups',, hash( '%count', $state_info.object_count ) )}</span></span></li>
    {/foreach}
    </ol>
    {else}
    <p class="exp-note">{'This group has no states yet, so it does nothing. Open it and add the first state; every existing object gets it.'|i18n( 'design/admin/state/groups' )}</p>
    {/if}

    <dl class="exp-facts">
        <div>
            <dt>{'Policy limitation'|i18n( 'design/admin/state/groups' )}</dt>
            <dd>{if $info.limitation|ne('')}<code>{$info.limitation|wash}</code>{else}<span class="exp-muted">{'None: policies cannot limit by system groups'|i18n( 'design/admin/state/groups' )}</span>{/if}</dd>
        </div>
        <div>
            <dt>{'Languages'|i18n( 'design/admin/state/groups' )}</dt>
            <dd>{$info.locales|implode( ', ' )|wash}{if $info.default_locale|ne('')} <span class="exp-muted">({'main: %locale'|i18n( 'design/admin/state/groups',, hash( '%locale', $info.default_locale ) )|wash})</span>{/if}</dd>
        </div>
        <div>
            <dt>{'Used in roles'|i18n( 'design/admin/state/groups' )}</dt>
            <dd>{if $info.roles|count|gt(0)}{foreach $info.roles as $role_info}{delimiter}, {/delimiter}<a href={concat( '/role/view/', $role_info.role_id )|ezurl}>{$role_info.role_name|wash}</a> <span class="exp-muted">({$role_info.policies|implode( ', ' )|wash})</span>{/foreach}{else}<span class="exp-muted">{'No role policy uses this group'|i18n( 'design/admin/state/groups' )}</span>{/if}</dd>
        </div>
    </dl>
    {if $info.internal}
    <p class="exp-note">{'Exponential keeps this group for its own use (locking content) and keeps it as it is: it cannot be edited or removed here, and roles cannot limit by it.'|i18n( 'design/admin/state/groups' )}</p>
    {/if}
</li>
    {undef $group_url}
{/foreach}
</ul>
{/if}

<div class="exp-actions-bar">
    <p class="exp-meta">{'Select groups with their checkbox to remove them. You will see what is removed and what it touches before anything happens.'|i18n( 'design/admin/state/groups' )}</p>
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-danger" id="remove_state_group_button" name="RemoveButton" value="1" title="{'Remove selected state groups.'|i18n( 'design/admin/state/groups' )|wash}">{'Remove selected'|i18n( 'design/admin/state/groups' )}</button>
        <button type="submit" class="exp-btn" id="create_state_group_button" name="CreateButton" value="1" title="{'Create a new state group.'|i18n( 'design/admin/state/groups' )|wash}">{'Create new'|i18n( 'design/admin/state/groups' )}</button>
    </div>
</div>

<div class="exp-pager">
    {* The sizes come from admininterface.ini [PaginationSettings]; the preference stores the position in that list. *}
    <p class="exp-sizes">{'Groups per page:'|i18n( 'design/admin/state/groups' )}
    {foreach $limit_choices as $limit_index => $limit_option}
        {if eq( $limit_index|inc, $limit_choice )}
            <span class="current" aria-current="true">{$limit_option}</span>
        {else}
            <a href={concat( '/user/preferences/set/', $list_limit_preference_name, '/', $limit_index|inc )|ezurl}>{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    {if $group_count|gt( $limit )}
    <div class="context-toolbar">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri='/state/groups'
             item_count=$group_count
             view_parameters=$view_parameters
             item_limit=$limit}
    </div>
    {/if}
</div>
</section>

</form>
{/if}

</div></div></div>

</div>{* class="context-block exp-states" *}
