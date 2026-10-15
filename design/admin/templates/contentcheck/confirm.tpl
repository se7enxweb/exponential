{* The confirmation of contentcheck/trash (doc/features/6.0/content-checks.md, "Moving to the trash").

   Variables: plan (items, allowed, too_many, fingerprint), node_id, nothing_selected, confirm_token,
   confirm_field, form_token, token_field, batch_limit. Every ticked object is listed with what will happen to it;
   only the allowed ones are posted back, with the confirmation token that binds them. The same file is in
   design/admin and design/admin4. *}
<div class="context-block a4-page a4-contentcheck">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Move to the trash'|i18n( 'kernel/contentcheck' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if $nothing_selected}
<div class="a4-notice is-warn" role="alert">{'No object was ticked. Tick the objects to move first.'|i18n( 'kernel/contentcheck' )}</div>
<p><a class="a4-btn" href={concat( '/contentcheck/unreferenced/', $node_id )|ezurl}>{'Back to the list'|i18n( 'kernel/contentcheck' )}</a></p>
{else}

{if $plan.too_many}
<div class="a4-notice is-warn" role="alert">{'Only the first %count ticked objects are taken; move the others afterwards.'|i18n( 'kernel/contentcheck',, hash( '%count', $batch_limit ) )}</div>
{/if}

{if $plan.allowed|count|gt( 0 )}
<p class="a4-intro">{'%count objects will be moved to the trash with every location they have. Nothing is deleted permanently: they can be restored from the trash until it is emptied.'|i18n( 'kernel/contentcheck',, hash( '%count', $plan.allowed|count ) )}</p>
{else}
<div class="a4-notice is-warn" role="alert">{'None of the ticked objects can be moved to the trash.'|i18n( 'kernel/contentcheck' )}</div>
{/if}

<div class="a4-table-wrap">
<table class="a4-table">
<thead>
<tr>
    <th scope="col">{'Name'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Class'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Location'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'What happens'|i18n( 'kernel/contentcheck' )}</th>
</tr>
</thead>
<tbody>
{foreach $plan.items as $item}
<tr>
    <td>{if $item.name|ne( '' )}{$item.name|wash}{else}{'Object %id'|i18n( 'kernel/contentcheck',, hash( '%id', $item.object_id ) )}{/if}</td>
    <td>{$item.class_name|wash}</td>
    <td>{$item.path|wash}</td>
    <td>{if $item.allowed}<span class="a4-status is-ok">{'Moved to the trash'|i18n( 'kernel/contentcheck' )}</span>{else}<span class="a4-status is-warn">{'Not moved'|i18n( 'kernel/contentcheck' )}</span> {include uri='design:contentcheck/reason.tpl' reason=$item.reason}{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>

<form method="post" action={'/contentcheck/trash'|ezurl}>
<input type="hidden" name="{$token_field|wash}" value="{$form_token|wash}" />
<input type="hidden" name="{$confirm_field|wash}" value="{$confirm_token|wash}" />
<input type="hidden" name="NodeID" value="{$node_id}" />
{foreach $plan.allowed as $object_id}
<input type="hidden" name="ObjectIDList[]" value="{$object_id}" />
{/foreach}
<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        {if $plan.allowed|count|gt( 0 )}<button type="submit" class="a4-btn a4-btn-danger" name="ConfirmButton" value="1">{'Move %count objects to the trash'|i18n( 'kernel/contentcheck',, hash( '%count', $plan.allowed|count ) )}</button>{/if}
        <button type="submit" class="a4-btn" name="CancelButton" value="1">{'Cancel'|i18n( 'kernel/contentcheck' )}</button>
    </div>
</div>
</form>
{/if}

</div></div></div>
</div>
