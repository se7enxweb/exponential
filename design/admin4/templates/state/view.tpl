{* One object state: its place in its group (the first is the default for new objects), the objects in it, its
   description and translations, and the roles whose policies name it. The same file is in design/admin and
   design/admin4. *}
{include uri='design:state/style.tpl'}

{def $locked = $group.is_internal
     $state_name = $state.current_translation.name
     $group_url = concat( '/state/group/', $group.identifier )}

<form action={concat( '/state/view/', $group.identifier, '/', $state.identifier )|ezurl} method="post" id="state">

<div class="context-block exp-states">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{$state_name|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-title-row">
    <span class="exp-meta">{'State in the group'|i18n( 'design/admin/state/view' )} <a href={$group_url|ezurl}>{$group_info.name|wash}</a></span>
    <code class="exp-key">{$group.identifier|wash}/{$state.identifier|wash}</code>
    <ul class="exp-badges">
        {if and( $state_info, $state_info.is_default )}<li class="exp-badge is-ok">{'Default for new objects'|i18n( 'design/admin/state/view' )}</li>{/if}
        {if $locked}<li class="exp-badge is-system">{'System, protected'|i18n( 'design/admin/state/view' )}</li>{/if}
        {if $current_language|ne('')}<li class="exp-badge is-info">{'Shown in %locale'|i18n( 'design/admin/state/view',, hash( '%locale', $current_language ) )|wash}</li>{/if}
    </ul>
</div>
{if $state.current_translation.description|ne('')}
<p class="exp-intro">{$state.current_translation.description|wash|nl2br}</p>
{else}
<p class="exp-intro exp-muted">{'No description. A description tells editors when content belongs in this state.'|i18n( 'design/admin/state/view' )}</p>
{/if}

<dl class="exp-facts exp-card" style="margin: 0 0 22px;">
    <div>
        <dt>{'Objects in this state'|i18n( 'design/admin/state/view' )}</dt>
        <dd>{if $state_info}{$state_info.object_count}{else}{$state.object_count}{/if}</dd>
    </div>
    <div>
        <dt>{'Position'|i18n( 'design/admin/state/view' )}</dt>
        <dd>{if $state_info}{'%position of %count'|i18n( 'design/admin/state/view',, hash( '%position', $state_info.position, '%count', $group_info.states|count ) )}{else}&mdash;{/if}</dd>
    </div>
    <div>
        <dt>{'Identifier'|i18n( 'design/admin/state/view' )}</dt>
        <dd><code>{$state.identifier|wash}</code></dd>
    </div>
    <div>
        <dt>{'ID'|i18n( 'design/admin/state/view' )}</dt>
        <dd>{$state.id}</dd>
    </div>
</dl>

<section class="exp-section" aria-labelledby="state-group-flow-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="state-group-flow-title">{'The states of %group'|i18n( 'design/admin/state/view',, hash( '%group', $group_info.name ) )|wash}</h2>
</div>
<ol class="exp-flow" aria-label="{'States of %group, in order'|i18n( 'design/admin/state/view',, hash( '%group', $group_info.name ) )|wash}">
{foreach $group_info.states as $other_state}
    <li><span class="exp-chip{if $other_state.is_default} is-default{/if}">{if $other_state.id|eq( $state.id )}<strong aria-current="true">{$other_state.name|wash}</strong>{else}<a href={concat( '/state/view/', $group.identifier, '/', $other_state.identifier )|ezurl}>{$other_state.name|wash}</a>{/if} <span class="exp-count">{'%count objects'|i18n( 'design/admin/state/view',, hash( '%count', $other_state.object_count ) )}</span></span></li>
{/foreach}
</ol>
</section>

<section class="exp-section" aria-labelledby="state-translations-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="state-translations-title">{'Translations (%count)'|i18n( 'design/admin/state/view',, hash( '%count', $state.languages|count ) )}</h2>
</div>
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr>
    <th scope="col">{'Language'|i18n( 'design/admin/state/view' )}</th>
    <th scope="col">{'Locale'|i18n( 'design/admin/state/view' )}</th>
    <th scope="col">{'Main'|i18n( 'design/admin/state/view' )}</th>
</tr></thead>
<tbody>
{foreach $state.languages as $language}
<tr>
    <td><img class="exp-flag" src="{$language.locale|flag_icon}" width="18" height="12" alt="" /> <a href={concat( '/state/view/', $group.identifier, '/', $state.identifier, '/', $language.locale )|ezurl} title="{'Show the state in %language'|i18n( 'design/admin/state/view',, hash( '%language', $language.name ) )|wash}">{$language.name|wash}</a></td>
    <td><code>{$language.locale|wash}</code></td>
    <td>{if $language.id|eq( $state.default_language_id )}{'Yes'|i18n( 'design/admin/state/view' )}{else}<span class="exp-muted">{'No'|i18n( 'design/admin/state/view' )}</span>{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
</section>

<section class="exp-section" aria-labelledby="state-roles-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="state-roles-title">{'Roles that name this state'|i18n( 'design/admin/state/view' )}</h2>
</div>
{if $state_roles|count|gt(0)}
<ul class="exp-plain">
{foreach $state_roles as $role_info}
    <li><a href={concat( '/role/view/', $role_info.role_id )|ezurl}>{$role_info.role_name|wash}</a> <span class="exp-muted">(<code>{$role_info.policies|implode( ', ' )|wash}</code>)</span></li>
{/foreach}
</ul>
{else}
<p class="exp-empty">{'No role policy names this state.'|i18n( 'design/admin/state/view' )}</p>
{/if}
</section>

<div class="exp-actions-bar">
    <p class="exp-meta">{if $locked}{'This state belongs to a system group and cannot be edited.'|i18n( 'design/admin/state/view' )}{else}{'Edit changes the identifier, the names and the descriptions.'|i18n( 'design/admin/state/view' )}{/if}</p>
    <div class="exp-actions">
        {if $locked|not}
        <button type="submit" class="exp-btn exp-btn-primary" name="EditButton" value="1">{'Edit'|i18n( 'design/admin/state/view' )}</button>
        {/if}
        <a class="exp-btn" href={$group_url|ezurl}>{'Back to the group'|i18n( 'design/admin/state/view' )}</a>
    </div>
</div>

</div></div></div>

</div>{* class="context-block exp-states" *}

</form>

{undef $locked $state_name $group_url}
