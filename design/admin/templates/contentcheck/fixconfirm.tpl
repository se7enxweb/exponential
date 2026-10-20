{* The preview of contentcheck/fixfiles (doc/eng-us/features/6.0/content-checks.md, "Resolving").

   Variables: plan (action, items, allowed, too_many, fingerprint), action, node_id, nothing_selected, batch_size,
   max_items, confirm_token, confirm_field, form_token, token_field. Every chosen field version is listed with what
   will happen to it; only the allowed ones are posted back, with the confirmation token that binds them. Replace
   asks for one file per field version. The same file is in design/admin and design/admin4. *}
{def $titles = hash( 'regenerate', 'Regenerate the aliases'|i18n( 'kernel/contentcheck' ),
                     'restore', 'Restore from another version'|i18n( 'kernel/contentcheck' ),
                     'replace', 'Re-upload or replace the file'|i18n( 'kernel/contentcheck' ),
                     'remove', 'Remove the dead reference'|i18n( 'kernel/contentcheck' ),
                     'review', 'Mark for review'|i18n( 'kernel/contentcheck' ) )
     $intros = hash( 'regenerate', 'The aliases of each image are made again from its original, with the settings of image.ini now. The original is not changed.'|i18n( 'kernel/contentcheck' ),
                     'restore', 'The file of another version of the same field, which still has it, is copied to the expected path. Nothing is overwritten; an image then gets its aliases again.'|i18n( 'kernel/contentcheck' ),
                     'replace', 'Choose a file for each field version. It is put at the expected path and must have the same extension; an image must be an image. Nothing is overwritten.'|i18n( 'kernel/contentcheck' ),
                     'remove', 'The field is emptied in this version: it no longer names the missing file. No file is deleted. A required field must be filled again at the next edit.'|i18n( 'kernel/contentcheck' ),
                     'review', 'Each field version is marked for review in the list, with your name and the time. Nothing else changes.'|i18n( 'kernel/contentcheck' ) )
     $problem_names = hash( 'missing', 'File missing'|i18n( 'kernel/contentcheck' ),
                            'alias_missing', 'Alias missing'|i18n( 'kernel/contentcheck' ),
                            'alias_stale', 'Alias out of date'|i18n( 'kernel/contentcheck' ),
                            'alias_absent', 'Alias never made'|i18n( 'kernel/contentcheck' ) )
     $is_replace = eq( $action, 'replace' )}
<div class="context-block a4-page a4-contentcheck">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if is_set( $titles[$action] )}{$titles[$action]|wash}{else}{'Resolve missing files'|i18n( 'kernel/contentcheck' )}{/if}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if $nothing_selected}
<div class="a4-notice is-warn" role="alert">{'No row was ticked or no action chosen. Tick the rows first.'|i18n( 'kernel/contentcheck' )}</div>
<p><a class="a4-btn" href={$back_uri|ezurl}>{'Back to the list'|i18n( 'kernel/contentcheck' )}</a></p>
{else}

{if $plan.too_many}
<div class="a4-notice is-warn" role="alert">{'Only the first %count rows are taken; do the others afterwards.'|i18n( 'kernel/contentcheck',, hash( '%count', $max_items ) )}</div>
{/if}

{if $plan.allowed|count|gt( 0 )}
<p class="a4-intro">{$intros[$action]|wash}</p>
<p class="a4-meta">{'%count field versions, done %batch at a time.'|i18n( 'kernel/contentcheck',, hash( '%count', $plan.allowed|count, '%batch', $batch_size ) )}</p>
{else}
<div class="a4-notice is-warn" role="alert">{'None of the chosen rows can be resolved this way.'|i18n( 'kernel/contentcheck' )}</div>
{/if}

<form method="post" action={'/contentcheck/fixfiles'|ezurl}{if $is_replace} enctype="multipart/form-data"{/if}>
<input type="hidden" name="{$token_field|wash}" value="{$form_token|wash}" />
<input type="hidden" name="{$confirm_field|wash}" value="{$confirm_token|wash}" />
<input type="hidden" name="NodeID" value="{$node_id}" />
<input type="hidden" name="FixAction" value="{$action|wash}" />
<input type="hidden" name="Total" value="{$plan.allowed|count}" />
{foreach $plan.allowed as $key}
<input type="hidden" name="Keys[]" value="{$key|wash}" />
{/foreach}

<div class="a4-table-wrap">
<table class="a4-table">
<thead>
<tr>
    <th scope="col">{'Object'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Field'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Version'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Expected path'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'What happens'|i18n( 'kernel/contentcheck' )}</th>
</tr>
</thead>
<tbody>
{foreach $plan.items as $item}
<tr>
    <td>{if $item.object_name|ne( '' )}{$item.object_name|wash}{else}&ndash;{/if}
        {if $item.object_id|gt( 0 )}<div class="a4-meta">{'Object %id'|i18n( 'kernel/contentcheck',, hash( '%id', $item.object_id ) )}</div>{/if}</td>
    <td>{if $item.field_name|ne( '' )}{$item.field_name|wash}{else}{'Field %id'|i18n( 'kernel/contentcheck',, hash( '%id', $item.attribute_id ) )}{/if}{if $item.language|ne( '' )} <span class="a4-tag is-mono">{$item.language|wash}</span>{/if}
        {if is_set( $problem_names[$item.problem] )}<div class="a4-meta">{$problem_names[$item.problem]|wash}</div>{/if}</td>
    <td>{'Version %version'|i18n( 'kernel/contentcheck',, hash( '%version', $item.version ) )}</td>
    <td>{if $item.path|ne( '' )}<code class="a4-code">{$item.path|wash}</code>{else}&ndash;{/if}</td>
    <td>{if $item.allowed}
            {if eq( $action, 'restore' )}<span class="a4-status is-ok">{'Copied from version %version'|i18n( 'kernel/contentcheck',, hash( '%version', $item.source_version ) )}</span>
                <div class="a4-meta a4-wrap">{$item.source_path|wash}</div>
            {elseif $is_replace}
                {def $field_id = concat( 'cc-file-', $item.key )}
                <label for="{$field_id|wash}">{'File'|i18n( 'kernel/contentcheck' )}</label>
                <input type="file" id="{$field_id|wash}" name="{$item.upload_field|wash}"{if $item.extension|ne( '' )} accept=".{$item.extension|wash}"{/if} />
                {undef $field_id}
            {elseif eq( $action, 'regenerate' )}<span class="a4-status is-ok">{'Aliases made again'|i18n( 'kernel/contentcheck' )}</span>
                {if $item.aliases|count|gt( 0 )}<div class="a4-meta">{foreach $item.aliases as $alias}{delimiter}, {/delimiter}{$alias.alias|wash}{/foreach}</div>{/if}
            {elseif eq( $action, 'remove' )}<span class="a4-status is-warn">{'Reference removed'|i18n( 'kernel/contentcheck' )}</span>
            {else}<span class="a4-status is-ok">{'Marked for review'|i18n( 'kernel/contentcheck' )}</span>{/if}
        {else}<span class="a4-status is-warn">{'Not done'|i18n( 'kernel/contentcheck' )}</span> {include uri='design:contentcheck/fixreason.tpl' reason=$item.reason}{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>

<div class="a4-toolbar is-bottom">
    {if $plan.allowed|count|gt( $batch_size )}
    <div class="a4-field">
        <label class="a4-check" for="cc-auto"><input type="checkbox" id="cc-auto" name="AutoContinue" value="1" checked="checked" /> {'Go on with the next batch by itself'|i18n( 'kernel/contentcheck' )}</label>
    </div>
    {/if}
    <div class="a4-actions">
        {if $plan.allowed|count|gt( 0 )}<button type="submit" class="a4-btn {if eq( $action, 'remove' )}a4-btn-danger{else}a4-btn-primary{/if}" name="ConfirmButton" value="1">{'Confirm for %count field versions'|i18n( 'kernel/contentcheck',, hash( '%count', $plan.allowed|count ) )}</button>{/if}
        <button type="submit" class="a4-btn" name="CancelButton" value="1">{'Cancel'|i18n( 'kernel/contentcheck' )}</button>
    </div>
</div>
</form>
{/if}

</div></div></div>
</div>
{undef $titles $intros $problem_names $is_replace}
