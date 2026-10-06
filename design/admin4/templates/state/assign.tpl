{* Set the states of one object (state/assign/<object id>): one select per state group the user may set states
   in, the object's current state chosen. The field names and the button are the kernel's. The same file is in
   design/admin and design/admin4. *}
{include uri='design:state/style.tpl'}

{def $assign_list = $node.object.allowed_assign_state_list}

<form name="statesform" method="post" action={'state/assign'|ezurl}>
<input type="hidden" name="ObjectID" value="{$node.object.id}" />
<input type="hidden" name="RedirectRelativeURI" value="{$node.url_alias|wash}" />

<div class="context-block exp-states">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Object states for object'|i18n( 'design/admin/node/view/full' )}: <a href={$node.url_alias|ezurl}>{$node.name|wash}</a></h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'An object is in one state of every state group. Choose the states and press Set states; only the states your role lets you set are offered.'|i18n( 'design/admin/state/assign' )}</p>

{if $assign_list|count}
<div class="exp-form">
{foreach $assign_list as $allowed_assign_state_info}
    <div class="exp-field">
        <label for="state-assign-{$allowed_assign_state_info.group.id}">{$allowed_assign_state_info.group.current_translation.name|wash} <code class="exp-key">{$allowed_assign_state_info.group.identifier|wash}</code></label>
        <select id="state-assign-{$allowed_assign_state_info.group.id}" name="SelectedStateIDList[]"{if $allowed_assign_state_info.states|count|eq(1)} disabled="disabled"{/if}>
        {foreach $allowed_assign_state_info.states as $state}
            <option value="{$state.id}"{if $node.object.state_id_array|contains( $state.id )} selected="selected"{/if}>{$state.current_translation.name|wash}</option>
        {/foreach}
        </select>
        {if $allowed_assign_state_info.states|count|eq(1)}<span class="exp-hint">{'Only the current state may be set.'|i18n( 'design/admin/state/assign' )}</span>{/if}
    </div>
{/foreach}
</div>
{else}
<p class="exp-empty">{'No content object state is configured. This can be done %urlstart here %urlend.'|i18n( 'design/admin/node/view/full', '', hash( '%urlstart', concat( '<a href=', 'state/groups'|ezurl, '>' ), '%urlend', '</a>' ) )}</p>
{/if}

<div class="exp-actions-bar">
    <p class="exp-meta">{'The object is saved with the new states at once; no new version is made.'|i18n( 'design/admin/state/assign' )}</p>
    <div class="exp-actions">
        {if $assign_list|count}
        <button type="submit" class="exp-btn exp-btn-primary" name="AssignButton" value="1" title="{'Apply states from the list above.'|i18n( 'design/admin/node/view/full' )|wash}">{'Set states'|i18n( 'design/admin/node/view/full' )}</button>
        {else}
        <button type="submit" class="exp-btn" name="AssignButton" value="1" disabled="disabled" title="{'No state to be applied to this content object. You might need to be assigned a more permissive access policy.'|i18n( 'design/admin/node/view/full' )|wash}">{'Set states'|i18n( 'design/admin/node/view/full' )}</button>
        {/if}
        <a class="exp-btn" href={$node.url_alias|ezurl}>{'Back to the object'|i18n( 'design/admin/state/assign' )}</a>
    </div>
</div>

</div></div></div>

</div>

</form>

{undef $assign_list}
