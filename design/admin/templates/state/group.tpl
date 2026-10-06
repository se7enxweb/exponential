{* One object state group: what it is, its states in order (the first is the default for new objects) with the
   objects in each, the order to change, its translations and the roles whose policies use it. Removing states
   asks first and says where their objects go; the view removes only when the confirmation is posted
   (ConfirmRemove).

   The same file is in design/admin and design/admin4. Without javascript the order is changed by typing the
   positions and saving; the script below adds Move up and Move down, which renumber the positions on the page
   (saved with Save order, as before). *}
{include uri='design:state/style.tpl'}

{def $info = $group_info
     $locked = $group.is_internal
     $base_url = concat( '/state/group/', $group.identifier )}

<div class="context-block exp-states">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{$info.name|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-title-row">
    <span class="exp-meta">{'State group'|i18n( 'design/admin/state/group' )}</span>
    <code class="exp-key">{$info.identifier|wash}</code>
    <ul class="exp-badges">
        {if $locked}<li class="exp-badge is-system"><svg width="12" height="12" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M5 7V5a3 3 0 0 1 6 0v2h1v7H4V7zm2 0h2V5a1 1 0 0 0-2 0z"/></svg>{'System, protected'|i18n( 'design/admin/state/group' )}</li>{/if}
        <li class="exp-badge">{if $info.states|count|eq(1)}{'1 state'|i18n( 'design/admin/state/group' )}{else}{'%count states'|i18n( 'design/admin/state/group',, hash( '%count', $info.states|count ) )}{/if}</li>
        {if $current_language|ne('')}<li class="exp-badge is-info">{'Shown in %locale'|i18n( 'design/admin/state/group',, hash( '%locale', $current_language ) )|wash}</li>{/if}
    </ul>
</div>
{if $info.description|ne('')}<p class="exp-intro">{$info.description|wash|nl2br}</p>{else}<p class="exp-intro">{'Every content object is in exactly one state of this group. New objects get the first state.'|i18n( 'design/admin/state/group' )}</p>{/if}

{foreach $state_feedback as $state_message}
<div class="exp-feedback {if $state_message.ok}is-ok{else}is-bad{/if}" role="{if $state_message.ok}status{else}alert{/if}">{$state_message.message|wash}</div>
{/foreach}

{if $locked}
<p class="exp-feedback is-info" role="note">{'Exponential keeps this group for its own use (locking content): its states, their order and its translations cannot be changed here, and roles cannot limit by it.'|i18n( 'design/admin/state/group' )}</p>
{/if}

{if $confirm_remove}
{* The confirmation. Nothing has been removed yet. *}
<form method="post" action={$base_url|ezurl}>
    <div class="exp-feedback is-warn" role="alert">
        <h2>{if $confirm_remove.removed|count|eq(1)}{'Remove this state?'|i18n( 'design/admin/state/group' )}{else}{'Remove %count states?'|i18n( 'design/admin/state/group',, hash( '%count', $confirm_remove.removed|count ) )}{/if}</h2>
        {if $confirm_remove.all}
        <p>{'No state would be left in the group. The %count objects in the removed states then have no state of this group, and new objects get none until a state is added again.'|i18n( 'design/admin/state/group',, hash( '%count', $confirm_remove.objects ) )}</p>
        {elseif $confirm_remove.objects|gt(0)}
        <p>{'The %count objects in the removed states are moved to "%state", the first state that stays.'|i18n( 'design/admin/state/group',, hash( '%count', $confirm_remove.objects, '%state', $confirm_remove.target.name ) )|wash}</p>
        {else}
        <p>{'No object is in a state that is removed, so no object changes.'|i18n( 'design/admin/state/group' )}</p>
        {/if}
        <p>{'A removed state is gone for good, with its translations. Policies that name it no longer match it. This cannot be undone.'|i18n( 'design/admin/state/group' )}</p>
    </div>
    <ul class="exp-flow" aria-label="{'States to remove'|i18n( 'design/admin/state/group' )}">
    {foreach $confirm_remove.removed as $removed_state}
        <li><span class="exp-chip">{$removed_state.name|wash} <code class="exp-key">{$removed_state.identifier|wash}</code> <span class="exp-count">{'%count objects'|i18n( 'design/admin/state/group',, hash( '%count', $removed_state.object_count ) )}</span></span></li>
    {/foreach}
    </ul>
    <div class="exp-actions-bar">
        {foreach $confirm_remove.ids as $remove_id}<input type="hidden" name="RemoveIDList[]" value="{$remove_id}" />{/foreach}
        <input type="hidden" name="ConfirmRemove" value="1" />
        <div class="exp-actions">
            <button type="submit" class="exp-btn exp-btn-danger is-solid" name="RemoveButton" value="1">{'Remove for good'|i18n( 'design/admin/state/group' )}</button>
            <a class="exp-btn" href={$base_url|ezurl}>{'Cancel'|i18n( 'design/admin/state/group' )}</a>
        </div>
    </div>
</form>

{else}

<form action={$base_url|ezurl} method="post" id="stateList">

<dl class="exp-facts exp-card" style="margin: 0 0 22px;">
    <div>
        <dt>{'Default state'|i18n( 'design/admin/state/group' )}</dt>
        <dd>{if $info.default_state}{$info.default_state.name|wash}{else}<span class="exp-muted">{'None yet'|i18n( 'design/admin/state/group' )}</span>{/if}</dd>
    </div>
    <div>
        <dt>{'Objects with a state of this group'|i18n( 'design/admin/state/group' )}</dt>
        <dd>{$info.objects}</dd>
    </div>
    <div>
        <dt>{'Policy limitation'|i18n( 'design/admin/state/group' )}</dt>
        <dd>{if $info.limitation|ne('')}<code>{$info.limitation|wash}</code>{else}<span class="exp-muted">{'None: policies cannot limit by system groups'|i18n( 'design/admin/state/group' )}</span>{/if}</dd>
    </div>
    <div>
        <dt>{'ID'|i18n( 'design/admin/state/group' )}</dt>
        <dd>{$info.id}</dd>
    </div>
</dl>

{* The states, in order. *}
<section class="exp-section" aria-labelledby="state-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="state-list-title">{'States, in order'|i18n( 'design/admin/state/group' )}</h2>
    {if $locked|not}
    <button type="submit" class="exp-btn exp-btn-primary" name="CreateButton" value="1"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 2h2v5h5v2H9v5H7V9H2V7h5z"/></svg>{'New state'|i18n( 'design/admin/state/group' )}</button>
    {/if}
</div>
<p class="exp-meta" id="state-order-hint" style="margin: 0 0 10px;">{'The first state is the default: every new object gets it. The order is also the order editors see the states in. To change it, change the positions and save the order.'|i18n( 'design/admin/state/group' )}</p>

{if $info.states|count|eq(0)}
<div class="exp-empty">
    <p>{'This group has no states yet. The first state you add is given to every object that exists, and becomes the default for new ones.'|i18n( 'design/admin/state/group' )}</p>
</div>
{else}
<ol class="exp-state-list" id="state-list">
{foreach $info.states as $state_info}
    {def $state_url = concat( '/state/view/', $group.identifier, '/', $state_info.identifier, cond( $current_language|ne(''), concat( '/', $current_language ), true(), '' ) )}
<li class="exp-state{if $state_info.is_default} is-default{/if}" id="state-{$state_info.id}" data-state-name="{$state_info.name|wash}">
    <span class="exp-pos" aria-hidden="true">{$state_info.position}</span>
    <div class="exp-state-main">
        <div class="exp-state-name">
            <h3><a href={$state_url|ezurl}>{$state_info.name|wash}</a></h3>
            <code class="exp-key">{$state_info.identifier|wash}</code>
            {if $state_info.is_default}<span class="exp-badge is-ok">{'Default for new objects'|i18n( 'design/admin/state/group' )}</span>{/if}
        </div>
        <p class="exp-state-meta">
            <span>{'%count objects'|i18n( 'design/admin/state/group',, hash( '%count', $state_info.object_count ) )}</span>
            <span>{'Languages: %locales'|i18n( 'design/admin/state/group',, hash( '%locales', $state_info.locales|implode( ', ' ) ) )|wash}</span>
        </p>
        {if $state_info.description|ne('')}<p class="exp-desc">{$state_info.description|wash}</p>{/if}
    </div>
    <div class="exp-state-side">
        <span class="exp-order">
            <label for="state-order-{$state_info.id}">{'Position'|i18n( 'design/admin/state/group' )}</label>
            <input type="text" inputmode="numeric" class="exp-order-input" id="state-order-{$state_info.id}" name="Order[{$state_info.id}]" value="{$state_info.position}" size="3" aria-describedby="state-order-hint"{if $locked} disabled="disabled"{/if} />
        </span>
        {if $locked|not}
        <button type="button" class="exp-btn exp-btn-small exp-btn-icon exp-move-up exp-js-only" hidden aria-label="{'Move %state up'|i18n( 'design/admin/state/group',, hash( '%state', $state_info.name ) )|wash}" title="{'Move up'|i18n( 'design/admin/state/group' )|wash}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M8 3l5 6H3z"/></svg></button>
        <button type="button" class="exp-btn exp-btn-small exp-btn-icon exp-move-down exp-js-only" hidden aria-label="{'Move %state down'|i18n( 'design/admin/state/group',, hash( '%state', $state_info.name ) )|wash}" title="{'Move down'|i18n( 'design/admin/state/group' )|wash}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M8 13L3 7h10z"/></svg></button>
        <a class="exp-btn exp-btn-small" href={concat( '/state/edit/', $group.identifier, '/', $state_info.identifier )|ezurl} aria-label="{'Edit the state %state'|i18n( 'design/admin/state/group',, hash( '%state', $state_info.name ) )|wash}">{'Edit'|i18n( 'design/admin/state/group' )}</a>
        <label class="exp-select"><input type="checkbox" name="RemoveIDList[]" value="{$state_info.id}" /><span>{'Select'|i18n( 'design/admin/state/group' )}<span class="exp-sr"> {$state_info.name|wash}</span></span></label>
        {/if}
    </div>
</li>
    {undef $state_url}
{/foreach}
</ol>
<p class="exp-sr" id="state-order-status" role="status" aria-live="polite"></p>
{/if}

{if $locked|not}
<div class="exp-actions-bar">
    <p class="exp-meta">{'Save order keeps the positions above. Remove selected asks first and says where the objects of the removed states go.'|i18n( 'design/admin/state/group' )}</p>
    <div class="exp-actions">
        {if $info.states|count|gt(1)}
        <button type="submit" class="exp-btn exp-btn-primary" name="UpdateOrderButton" value="1" title="{'Update the order of the content object states in this group.'|i18n( 'design/admin/state/group' )|wash}">{'Save order'|i18n( 'design/admin/state/group' )}</button>
        {/if}
        {if $info.states|count|gt(0)}
        <button type="submit" class="exp-btn exp-btn-danger" id="remove_state_button" name="RemoveButton" value="1" title="{'Remove selected states.'|i18n( 'design/admin/state/group' )|wash}">{'Remove selected'|i18n( 'design/admin/state/group' )}</button>
        {/if}
        <button type="submit" class="exp-btn" id="create_state_button" name="CreateButton" value="1" title="{'Create a new state.'|i18n( 'design/admin/state/group' )|wash}">{'Create new'|i18n( 'design/admin/state/group' )}</button>
    </div>
</div>
{/if}
</section>

{* The translations of the group's name and description. *}
<section class="exp-section" aria-labelledby="state-translations-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="state-translations-title">{'Translations (%count)'|i18n( 'design/admin/state/group',, hash( '%count', $group.languages|count ) )}</h2>
    {if $locked|not}
    <button type="submit" class="exp-btn" name="EditButton" value="1">{'Edit group and translations'|i18n( 'design/admin/state/group' )}</button>
    {/if}
</div>
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr>
    <th scope="col">{'Language'|i18n( 'design/admin/state/group' )}</th>
    <th scope="col">{'Locale'|i18n( 'design/admin/state/group' )}</th>
    <th scope="col">{'Main'|i18n( 'design/admin/state/group' )}</th>
</tr></thead>
<tbody>
{foreach $group.languages as $language}
<tr>
    <td><img class="exp-flag" src="{$language.locale|flag_icon}" width="18" height="12" alt="" /> <a href={concat( $base_url, '/', $language.locale )|ezurl} title="{'Show the group in %language'|i18n( 'design/admin/state/group',, hash( '%language', $language.name ) )|wash}">{$language.name|wash}</a></td>
    <td><code>{$language.locale|wash}</code></td>
    <td>{if $language.id|eq( $group.default_language_id )}{'Yes'|i18n( 'design/admin/state/group' )}{else}<span class="exp-muted">{'No'|i18n( 'design/admin/state/group' )}</span>{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
</section>

{* Who this group matters to. *}
<section class="exp-section" aria-labelledby="state-roles-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="state-roles-title">{'Roles that use this group'|i18n( 'design/admin/state/group' )}</h2>
</div>
{if $info.roles|count|gt(0)}
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr>
    <th scope="col">{'Role'|i18n( 'design/admin/state/group' )}</th>
    <th scope="col">{'Policies'|i18n( 'design/admin/state/group' )}</th>
    <th scope="col">{'How'|i18n( 'design/admin/state/group' )}</th>
</tr></thead>
<tbody>
{foreach $info.roles as $role_info}
<tr>
    <td><a href={concat( '/role/view/', $role_info.role_id )|ezurl}>{$role_info.role_name|wash}</a></td>
    <td><code>{$role_info.policies|implode( ', ' )|wash}</code></td>
    <td>{if $role_info.condition}{'Applies only to objects in some states'|i18n( 'design/admin/state/group' )}{/if}{if and( $role_info.condition, $role_info.new_state )}<br />{/if}{if $role_info.new_state}{'Lets users set some states'|i18n( 'design/admin/state/group' )}{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{else}
<p class="exp-empty">{if $info.limitation|ne('')}{'No role policy uses this group yet. To let a role read, edit or publish only content in some states, add the limitation %limitation to its policy.'|i18n( 'design/admin/state/group',, hash( '%limitation', $info.limitation ) )|wash}{else}{'Roles cannot limit by system groups.'|i18n( 'design/admin/state/group' )}{/if}</p>
{/if}
</section>

</form>
{/if}

</div></div></div>

</div>{* class="context-block exp-states" *}

{if and( $locked|not, $confirm_remove|not, $info.states|count|gt(1) )}
{* Move up and Move down: they move the state on the page and renumber every position, which Save order then
   stores. Nothing is saved until then. *}
<script type="text/javascript">
var expStateOrderText = {ldelim}
    moved: '{'%state is now at position %position. Save the order to keep it.'|i18n( 'design/admin/state/group' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var list = document.getElementById( 'state-list' );
    if ( !list ) return;
    var status = document.getElementById( 'state-order-status' );
    Array.prototype.forEach.call( document.querySelectorAll( '.exp-states .exp-js-only' ), function ( el ) { el.hidden = false; } );
    function renumber() {
        var items = list.querySelectorAll( '.exp-state' );
        Array.prototype.forEach.call( items, function ( item, i ) {
            item.querySelector( '.exp-order-input' ).value = i + 1;
            item.querySelector( '.exp-pos' ).textContent = i + 1;
            item.querySelector( '.exp-move-up' ).disabled = i === 0;
            item.querySelector( '.exp-move-down' ).disabled = i === items.length - 1;
        } );
    }
    list.addEventListener( 'click', function ( event ) {
        var button = event.target.closest( '.exp-move-up, .exp-move-down' );
        if ( !button ) return;
        var item = button.closest( '.exp-state' );
        var up = button.classList.contains( 'exp-move-up' );
        var other = up ? item.previousElementSibling : item.nextElementSibling;
        if ( !other ) return;
        list.insertBefore( item, up ? other : other.nextElementSibling );
        renumber();
        var target = item.querySelector( up ? '.exp-move-up' : '.exp-move-down' );
        ( target.disabled ? item.querySelector( up ? '.exp-move-down' : '.exp-move-up' ) : target ).focus();
        var position = Array.prototype.indexOf.call( list.children, item ) + 1;
        status.textContent = expStateOrderText.moved.replace( '%state', item.getAttribute( 'data-state-name' ) ).replace( '%position', position );
    } );
    renumber();
})();
{/literal}
</script>
{/if}

{undef $info $locked $base_url}
