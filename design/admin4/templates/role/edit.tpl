{* The role editor (role/edit/<id>).

   The editor works on a draft of the role: every change below (the name, new, changed, moved and removed policies)
   is kept in the draft and reaches the users of the role only with Save; Cancel throws the draft away and goes back
   to the page it came from (RedirectIfDiscarded, under eZRedirectManager's rules), else the role list. The page says
   when the draft differs from the saved role (draft_differs), and the browser asks before leaving it then, or after
   the name was changed, other than by a button of the form.

   The policies are listed as sentences with their module, function and limitations; ordered by module they are
   grouped under it. The order buttons move a policy in the role's own order (shown when the list is in that order).
   New policy opens the three steps of the policy wizard; Remove selected asks first, in place.

   Every name the view reads is kept (NewName, ChangeRoleName, Apply, Discard, CreatePolicy, RemovePolicies,
   DeleteIDArray[], RedirectIfDiscarded); the order buttons send MovePolicyUp / MovePolicyDown with the policy's id
   as value, which is what the view reads (the old image buttons MovePolicyUp_<id> still work). Every template
   variable is still set. This is an edit view, which admin4 draws without its main card; .exp-standalone gives the
   page its own. The same file is in design/admin and design/admin4. Guide: doc/guides/roles-and-policies.md *}
{include uri='design:role/exp_style.tpl'}

{def $sentences = first_set( $policy_sentences, hash() )
     $differs = first_set( $draft_differs, false() )
     $removed = first_set( $policies_removed, false() )
     $grouped = eq( $policy_sort.field, 'module' )
     $last_module = false()
     $policy_position = 0}

{* The address keeps the page and the sort of the policy list, so a button pressed on page three of a sorted list
   comes back to that page, sorted. *}
<form name="roleedit" id="roleedit" method="post" class="exp-roles exp-standalone" action={concat( $module.functions.edit.uri, '/', $role.id,
                                                    '/(policy_offset)/', $policy_offset,
                                                    '/(policy_sort)/', $policy_sort.field,
                                                    '/(policy_dir)/', $policy_sort.direction )|ezurl}>

{* Enter in the name field presses the first submit button of the form, which would otherwise be the order button
   of a policy. This one only keeps the name. *}
<input type="submit" name="ChangeRoleName" value="{'Save'|i18n( 'design/admin/role/edit' )}" tabindex="-1" aria-hidden="true" class="exp-hidden-default exp-sr" />

<div class="context-block">
<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
<h1 class="context-title" id="role-edit-title">{'Edit <%role_name> [Role]'|i18n( 'design/admin/role/edit',, hash( '%role_name', $role.name ) )|wash}</h1>
{if first_set( $original_role_id, 0 )|gt( 0 )}<span class="exp-meta">{'ID %id'|i18n( 'design/admin/role/edit',, hash( '%id', $original_role_id ) )}</span>{/if}
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'You are editing a draft of this role. Nothing changes for its users until you press Save; Cancel throws every change of this draft away.'|i18n( 'design/admin/role/edit' )}</p>

{if $differs}
<div class="exp-feedback is-warn" role="status" id="role-edit-unsaved"><strong>{'Unsaved changes.'|i18n( 'design/admin/role/edit' )}</strong> {'This draft differs from the saved role. Save to apply the changes, or Cancel to discard them.'|i18n( 'design/admin/role/edit' )}</div>
{/if}
{if first_set( $policy_moved_to, false() )}
<div class="exp-feedback is-ok" role="status" id="role-edit-moved">{'The policy was moved to position %position. Save to keep the new order.'|i18n( 'design/admin/role/edit',, hash( '%position', $policy_moved_to ) )}</div>
{/if}
{if $removed|ne( false() )}
    {if $removed|gt( 0 )}
<div class="exp-feedback is-ok" role="status">{'%count policies were removed from the draft. Save to apply this.'|i18n( 'design/admin/role/edit',, hash( '%count', $removed ) )}</div>
    {else}
<div class="exp-feedback is-warn" role="alert">{'No policy was selected. Tick the policies to remove first.'|i18n( 'design/admin/role/edit' )}</div>
    {/if}
{/if}

<div class="exp-field exp-name-field">
    <label for="roleName">{'Name'|i18n( 'design/admin/role/edit' )}</label>
    <input id="roleName" type="text" name="NewName" value="{$role.name|wash}" maxlength="255" required="required" aria-describedby="roleName-help" />
    <span class="exp-help" id="roleName-help">{'Shown in the role list and wherever the role is assigned.'|i18n( 'design/admin/role/edit' )}</span>
</div>

<section class="exp-section" aria-labelledby="role-edit-policies-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="role-edit-policies-title">{'Policies (%policy_count)'|i18n( 'design/admin/role/edit',, hash( '%policy_count', $policy_count ) )}</h2>
    {if $policy_count|gt( 0 )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/role/edit',, hash( '%from', sum( $policy_offset, 1 ), '%to', min( sum( $policy_offset, $policy_limit ), $policy_count ), '%count', $policy_count ) )}</span>
    <ul class="exp-tabs" aria-label="{'Order of the policies'|i18n( 'design/admin/role/edit' )}">
    {foreach array( hash( 'key', 'id', 'text', 'Role order'|i18n( 'design/admin/role/edit' ) ),
                    hash( 'key', 'module', 'text', 'By module'|i18n( 'design/admin/role/edit' ) ),
                    hash( 'key', 'function', 'text', 'By function'|i18n( 'design/admin/role/edit' ) ),
                    hash( 'key', 'limitation', 'text', 'By limitation'|i18n( 'design/admin/role/edit' ) ) ) as $tab}
        {if eq( $tab.key, $policy_sort.field )}
        <li><a class="current" aria-current="true" href={concat( $policy_page_uri, '/(policy_sort)/', $tab.key, '/(policy_dir)/', $policy_sort.opposite )|ezurl} title="{'Reverse the order'|i18n( 'design/admin/role/edit' )}">{$tab.text|wash} {if eq( $policy_sort.direction, 'asc' )}&#8593;{else}&#8595;{/if}</a></li>
        {else}
        <li><a href={concat( $policy_page_uri, '/(policy_sort)/', $tab.key, '/(policy_dir)/asc' )|ezurl}>{$tab.text|wash}</a></li>
        {/if}
    {/foreach}
    </ul>
    <p id="role-edit-grip-help">{if $policy_order_editable}{'Drag a policy by its grip to a new place, or focus the grip and press the up or down arrow key. The arrow buttons move a policy one place, also across pages; the position field moves it to any place in the whole list. The order is kept in the draft until you save.'|i18n( 'design/admin/role/edit' )}{else}{'Sort the list by ID, ascending, to change the order of the policies.'|i18n( 'design/admin/role/edit' )}{/if}</p>
    {/if}
</div>

{if $policies}
<ul class="exp-policies" id="role-edit-policies">
{foreach $policies as $index => $policy}
    {set $policy_position = sum( $policy_offset, $index )}
    {def $said = first_set( $sentences[$policy.id], false() )}
    {if and( $grouped, ne( $policy.module_name, $last_module ) )}
        {set $last_module = $policy.module_name}
</ul>
<div class="exp-group-head"><h3>{if eq( $policy.module_name, '*' )}{'Every module'|i18n( 'design/admin/role/edit' )}{else}{'Module %module'|i18n( 'design/admin/role/edit',, hash( '%module', $policy.module_name ) )|wash}{/if}</h3></div>
<ul class="exp-policies">
    {/if}
<li class="exp-policy{if and( $said, $said.full_access )} is-full{elseif and( $said, $said.manages_roles )} is-roles{/if}" id="policy-{$policy.id}">
    <label class="exp-select" title="{'Select policy for removal.'|i18n( 'design/admin/role/edit' )}">
        <input type="checkbox" name="DeleteIDArray[]" value="{$policy.id}" aria-describedby="policy-{$policy.id}-text" aria-label="{'Select policy %id for removal'|i18n( 'design/admin/role/edit',, hash( '%id', $policy.id ) )}" />
    </label>
    <div class="exp-policy-main">
        <p class="exp-sentence" id="policy-{$policy.id}-text">{if $said}{$said.sentence|wash}{else}{$policy.module_name|wash} / {$policy.function_name|wash}{/if}</p>
        <p class="exp-policy-detail">
            <code>#{$policy.id}</code>
            <code>{if eq( $policy.module_name, '*' )}{'all modules'|i18n( 'design/admin/role/edit' )}{else}{$policy.module_name|wash}{/if} / {if eq( $policy.function_name, '*' )}{'all functions'|i18n( 'design/admin/role/edit' )}{else}{$policy.function_name|wash}{/if}</code>
            {if $policy.limitations}
            &middot;
            {foreach $policy.limitations as $limitation}{$limitation.label|wash}{if $limitation.denies_without_handler} <em class="limitation-denies exp-denies" title="{'No extension handler evaluates this limitation, so this policy gives no access'|i18n( 'design/admin/role/view' )|wash}">{'(no handler, denies)'|i18n( 'design/admin/role/view' )|wash}</em>{/if}: {foreach $limitation.values_as_array_with_names as $limitation_value}{$limitation_value.Name|wash}{delimiter}, {/delimiter}{/foreach}{delimiter}; {/delimiter}{/foreach}
            {else}
            &middot; {'No limitations'|i18n( 'design/admin/role/edit' )}
            {/if}
        </p>
    </div>
    <div class="exp-policy-tools">
        {if $policy_order_editable}
        <span class="exp-order policy-order">
            <button type="button" class="exp-grip exp-js-only" hidden draggable="true" data-policy="{$policy.id}" data-position="{sum( $policy_position, 1 )}" aria-describedby="policy-{$policy.id}-text role-edit-grip-help" aria-label="{'Move policy at position %position'|i18n( 'design/admin/role/edit',, hash( '%position', sum( $policy_position, 1 ) ) )}" title="{'Drag to move, or use the up and down arrow keys'|i18n( 'design/admin/role/edit' )}"><span aria-hidden="true">&#10303;</span></button>
            <button type="submit" class="exp-btn exp-btn-small exp-move-up" name="MovePolicyUp" value="{$policy.id}" title="{'Move up'|i18n( 'design/admin/role/edit' )}" aria-label="{'Move policy %id up'|i18n( 'design/admin/role/edit',, hash( '%id', $policy.id ) )}"{if eq( $policy_position, 0 )} disabled="disabled"{/if}>&#8593;</button>
            <button type="submit" class="exp-btn exp-btn-small exp-move-down" name="MovePolicyDown" value="{$policy.id}" title="{'Move down'|i18n( 'design/admin/role/edit' )}" aria-label="{'Move policy %id down'|i18n( 'design/admin/role/edit',, hash( '%id', $policy.id ) )}"{if ge( sum( $policy_position, 1 ), $policy_count )} disabled="disabled"{/if}>&#8595;</button>
            {if $policy_count|gt( 2 )}
            <span class="exp-moveto">
                <label for="policy-{$policy.id}-position" class="exp-sr">{'Position of policy %id'|i18n( 'design/admin/role/edit',, hash( '%id', $policy.id ) )}</label>
                <input type="number" id="policy-{$policy.id}-position" name="MovePolicyPosition[{$policy.id}]" value="{sum( $policy_position, 1 )}" min="1" max="{$policy_count}" inputmode="numeric" />
                <button type="submit" class="exp-btn exp-btn-small" name="MovePolicyTo" value="{$policy.id}" title="{'Move the policy to the position in the field, in the whole list'|i18n( 'design/admin/role/edit' )}">{'Move'|i18n( 'design/admin/role/edit' )}</button>
            </span>
            {/if}
        </span>
        {/if}
        <a class="exp-btn exp-btn-small" href={concat( 'role/policyedit/', $policy.id )|ezurl} title="{"Edit the policy's function limitations."|i18n( 'design/admin/role/edit' )}" aria-describedby="policy-{$policy.id}-text">{'Limitations'|i18n( 'design/admin/role/edit' )}</a>
    </div>
</li>
    {undef $said}
{/foreach}
</ul>

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
<p class="exp-empty">{'There are no policies set up for this role. Add one with New policy.'|i18n( 'design/admin/role/edit' )}</p>
{/if}

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn" type="submit" name="CreatePolicy" value="1" title="{'Create a new policy.'|i18n( 'design/admin/role/edit' )}">{'New policy'|i18n( 'design/admin/role/edit' )}</button>
        {if $policies}
        <details class="exp-confirm">
            <summary>{'Remove selected'|i18n( 'design/admin/role/edit' )}</summary>
            <div>
                <p>{'The ticked policies are removed from this draft. Their users lose what they gave when you press Save; Cancel keeps them.'|i18n( 'design/admin/role/edit' )}</p>
                <button class="exp-btn exp-btn-danger" type="submit" name="RemovePolicies" value="1" title="{'Remove selected policies.'|i18n( 'design/admin/role/edit' )}">{'Remove the ticked policies'|i18n( 'design/admin/role/edit' )}</button>
            </div>
        </details>
        {/if}
    </div>
</div>
</section>

<div class="exp-savebar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="Apply" value="1" title="{'Save policy changes to this role'|i18n( 'design/admin/role/edit' )}">{'Save'|i18n( 'design/admin/role/edit' )}</button>
        <button class="exp-btn" type="submit" name="Discard" value="1" formnovalidate="formnovalidate">{'Cancel'|i18n( 'design/admin/role/edit' )}</button>
    </div>
    <p class="exp-meta"><span class="exp-dirty" id="role-edit-dirty"{if not( $differs )} hidden{/if}>&#9679; {'Unsaved changes'|i18n( 'design/admin/role/edit' )}</span> {'Save applies the draft to everyone the role is assigned to. Cancel discards it and goes back.'|i18n( 'design/admin/role/edit' )}</p>
</div>

</div></div></div>
</div>

{if and( is_set( $redirect_if_discarded ), $redirect_if_discarded )}<input type="hidden" name="RedirectIfDiscarded" value="{$redirect_if_discarded|wash}" />{/if}
</form>

<script type="text/javascript">
var expRoleEditText = {ldelim} leave: '{'This draft has unsaved changes. Leave the page anyway?'|i18n( 'design/admin/role/edit' )|wash( javascript )}' {rdelim};
var expRoleEditDiffers = {if $differs}true{else}false{/if};
{literal}
(function () {
    var form = document.getElementById( 'roleedit' ), name = document.getElementById( 'roleName' );
    if ( !form || !name ) return;
    var initial = name.value, submitting = false, dirtyEl = document.getElementById( 'role-edit-dirty' );
    function dirty() { return expRoleEditDiffers || name.value !== initial; }
    name.addEventListener( 'input', function () { if ( dirtyEl ) dirtyEl.hidden = !dirty(); } );
    form.addEventListener( 'submit', function () { submitting = true; } );
    // Leaving by a link, the address bar or closing the tab, not by Save, Cancel or another button of the form
    window.addEventListener( 'beforeunload', function ( e ) {
        if ( submitting || !dirty() ) return;
        e.preventDefault();
        e.returnValue = expRoleEditText.leave;
        return expRoleEditText.leave;
    } );
    if ( !expRoleEditDiffers && !document.getElementById( 'role-edit-moved' ) ) { name.focus(); name.select(); }
})();
{/literal}
</script>

{if $policy_order_editable}
<script type="text/javascript">
var expRoleOrder = {ldelim} offset: {$policy_offset}, moved: '{'Moved to position %position.'|i18n( 'design/admin/role/edit' )|wash( javascript )}' {rdelim};
{literal}
/* Drag and drop and the arrow keys on the grip. Both send the move the buttons send (MovePolicyTo with the place in
   the whole list, or MovePolicyUp / MovePolicyDown), so the server makes it in the draft; nothing is reordered only
   on screen. */
(function () {
    var form = document.getElementById( 'roleedit' ), list = document.getElementById( 'role-edit-policies' );
    if ( !form || !list ) return;
    var grips = list.querySelectorAll( '.exp-grip' ), i, dragged = null, startIndex = -1;
    for ( i = 0; i < grips.length; i++ ) grips[i].hidden = false;
    function items() { return Array.prototype.slice.call( list.querySelectorAll( ':scope > li.exp-policy' ) ); }
    function send( name, value, position ) {
        var b = document.createElement( 'button' );
        b.type = 'submit'; b.name = name; b.value = value; b.hidden = true;
        if ( position ) {
            var field = form.querySelector( 'input[name="MovePolicyPosition[' + value + ']"]' );
            if ( !field ) { field = document.createElement( 'input' ); field.type = 'hidden'; field.name = 'MovePolicyPosition[' + value + ']'; form.appendChild( field ); }
            field.value = position;
            try { sessionStorage.setItem( 'exp-role-grip', String( position ) ); } catch ( e ) {}
        }
        form.appendChild( b );
        if ( form.requestSubmit ) form.requestSubmit( b ); else b.click();
    }
    list.addEventListener( 'dragstart', function ( e ) {
        var grip = e.target.closest ? e.target.closest( '.exp-grip' ) : null;
        if ( !grip ) { e.preventDefault(); return; }
        dragged = grip.closest( 'li.exp-policy' );
        startIndex = items().indexOf( dragged );
        dragged.classList.add( 'is-dragging' );
        e.dataTransfer.effectAllowed = 'move';
        try { e.dataTransfer.setData( 'text/plain', grip.getAttribute( 'data-policy' ) ); e.dataTransfer.setDragImage( dragged, 20, 20 ); } catch ( x ) {}
    } );
    list.addEventListener( 'dragover', function ( e ) {
        if ( !dragged ) return;
        e.preventDefault();
        var over = e.target.closest ? e.target.closest( 'li.exp-policy' ) : null;
        if ( !over || over === dragged ) return;
        var box = over.getBoundingClientRect();
        list.insertBefore( dragged, e.clientY > box.top + box.height / 2 ? over.nextSibling : over );
    } );
    list.addEventListener( 'drop', function ( e ) { if ( dragged ) e.preventDefault(); } );
    list.addEventListener( 'dragend', function () {
        if ( !dragged ) return;
        var index = items().indexOf( dragged ), grip = dragged.querySelector( '.exp-grip' );
        dragged.classList.remove( 'is-dragging' );
        var moved = dragged; dragged = null;
        if ( index !== startIndex && index >= 0 ) send( 'MovePolicyTo', grip.getAttribute( 'data-policy' ), expRoleOrder.offset + index + 1 );
    } );
    list.addEventListener( 'keydown', function ( e ) {
        var grip = e.target.closest ? e.target.closest( '.exp-grip' ) : null;
        if ( !grip || ( e.key !== 'ArrowUp' && e.key !== 'ArrowDown' ) ) return;
        e.preventDefault();
        var row = grip.closest( 'li.exp-policy' ), b = row.querySelector( e.key === 'ArrowUp' ? '.exp-move-up' : '.exp-move-down' );
        if ( !b || b.disabled ) return;
        var position = parseInt( grip.getAttribute( 'data-position' ), 10 ) + ( e.key === 'ArrowUp' ? -1 : 1 );
        try { sessionStorage.setItem( 'exp-role-grip', String( position ) ); } catch ( x ) {}
        if ( form.requestSubmit ) form.requestSubmit( b ); else b.click();
    } );
    // Back from a move: the grip of the moved policy gets the focus again
    var last = null;
    try { last = sessionStorage.getItem( 'exp-role-grip' ); sessionStorage.removeItem( 'exp-role-grip' ); } catch ( e ) {}
    if ( last ) {
        var target = list.querySelector( '.exp-grip[data-position="' + last + '"]' );
        if ( target ) { target.focus(); target.closest( 'li.exp-policy' ).classList.add( 'is-moved' ); }
    }
})();
{/literal}
</script>
{/if}
