{* The plan of an action on storage files (doc/eng-us/features/6.0/content-checks.md, "Quarantine"): the dry run. Nothing
   has changed when this page is shown; it lists every ticked file with what would happen to it, and only the
   allowed ones are posted back with the confirmation token that binds them. Deleting from the quarantine needs the
   box ConfirmDelete as well.

   Variables: plan (action, items, allowed, bytes, too_many), node_id, show, nothing_selected, needs_delete_box,
   confirm_token, confirm_field, form_token, token_field, batch_limit, quarantine_root. The same file is in
   design/admin and design/admin4. *}
{def $titles = hash( 'quarantine', 'Move to the quarantine'|i18n( 'kernel/contentcheck' ),
                     'restore', 'Restore from the quarantine'|i18n( 'kernel/contentcheck' ),
                     'delete', 'Delete from the quarantine'|i18n( 'kernel/contentcheck' ),
                     'permissions', 'Fix owner and mode'|i18n( 'kernel/contentcheck' ) )
     $happens = hash( 'quarantine', 'Moved to the quarantine'|i18n( 'kernel/contentcheck' ),
                      'restore', 'Moved back to the storage'|i18n( 'kernel/contentcheck' ),
                      'delete', 'Deleted permanently'|i18n( 'kernel/contentcheck' ),
                      'permissions', 'Owner and mode set'|i18n( 'kernel/contentcheck' ) )
     $reasons = hash( 'not_found', 'The file is not there (any more).'|i18n( 'kernel/contentcheck' ),
                      'outside', 'The file is not in a directory the check covers.'|i18n( 'kernel/contentcheck' ),
                      'link', 'The file is a symbolic link.'|i18n( 'kernel/contentcheck' ),
                      'ignored', 'The path is in the ignored list.'|i18n( 'kernel/contentcheck' ),
                      'recent', 'The file is too new to judge.'|i18n( 'kernel/contentcheck' ),
                      'used', 'Content uses the file now.'|i18n( 'kernel/contentcheck' ),
                      'not_used', 'No content uses the file: it is an orphan, not a problem.'|i18n( 'kernel/contentcheck' ),
                      'exists', 'A file is already at the destination.'|i18n( 'kernel/contentcheck' ),
                      'no_index', 'The reference index cannot be read now.'|i18n( 'kernel/contentcheck' ),
                      'cluster', 'Files of a cluster file handler cannot be changed here.'|i18n( 'kernel/contentcheck' ),
                      'needs_root', 'Only the system administrator can change the owner: run ./console exp:content-check --fix-permissions as root.'|i18n( 'kernel/contentcheck' ),
                      'not_owner', 'This server process does not own the file, so it cannot change its mode.'|i18n( 'kernel/contentcheck' ),
                      'fine', 'Owner and mode are already as expected.'|i18n( 'kernel/contentcheck' ) )}
<div class="context-block a4-page a4-contentcheck">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{$titles[$plan.action]|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if $nothing_selected}
<div class="a4-notice is-warn" role="alert">{'No file was ticked. Tick the files first.'|i18n( 'kernel/contentcheck' )}</div>
<p><a class="a4-btn" href={concat( '/contentcheck/storage/', $node_id, '/(show)/', $show )|ezurl}>{'Back to the list'|i18n( 'kernel/contentcheck' )}</a></p>
{else}

<div class="a4-notice is-info" role="status">{'Dry run: nothing has been changed yet.'|i18n( 'kernel/contentcheck' )}</div>
{if $plan.too_many}
<div class="a4-notice is-warn" role="alert">{'Only the first %count ticked files are taken; do the others afterwards.'|i18n( 'kernel/contentcheck',, hash( '%count', $batch_limit ) )}</div>
{/if}
{if $plan.allowed|count|gt( 0 )}
<p class="a4-intro">{if eq( $plan.action, 'quarantine' )}{'%count files (%size) will be moved to %dir, below the date of today, keeping their path. They can be restored from there.'|i18n( 'kernel/contentcheck',, hash( '%count', $plan.allowed|count, '%size', $plan.bytes|si( byte ), '%dir', $quarantine_root ) )|wash}{elseif eq( $plan.action, 'delete' )}{'%count files (%size) will be deleted from the quarantine. This cannot be undone.'|i18n( 'kernel/contentcheck',, hash( '%count', $plan.allowed|count, '%size', $plan.bytes|si( byte ) ) )|wash}{elseif eq( $plan.action, 'restore' )}{'%count files (%size) will be moved back to their place in the storage.'|i18n( 'kernel/contentcheck',, hash( '%count', $plan.allowed|count, '%size', $plan.bytes|si( byte ) ) )|wash}{else}{'%count files will get the expected owner and mode; nothing else is changed.'|i18n( 'kernel/contentcheck',, hash( '%count', $plan.allowed|count ) )|wash}{/if}</p>
{else}
<div class="a4-notice is-warn" role="alert">{'None of the ticked files can be changed.'|i18n( 'kernel/contentcheck' )}</div>
{/if}

<div class="a4-table-wrap">
<table class="a4-table">
<thead>
<tr>
    <th scope="col">{'Path'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col" class="a4-num">{'Size'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'What happens'|i18n( 'kernel/contentcheck' )}</th>
</tr>
</thead>
<tbody>
{foreach $plan.items as $item}
<tr>
    <td><span class="a4-tag is-mono">{$item.path|wash}</span>{if and( $item.detail|ne( '' ), $item.allowed )}<div class="a4-meta">{if eq( $plan.action, 'quarantine' )}{'To: %path'|i18n( 'kernel/contentcheck',, hash( '%path', $item.detail ) )|wash}{else}{$item.detail|wash}{/if}</div>{/if}</td>
    <td class="a4-num">{if $item.size|ge( 0 )}{$item.size|si( byte )}{else}&ndash;{/if}</td>
    <td>{if $item.allowed}<span class="a4-status is-ok">{$happens[$plan.action]|wash}</span>{else}<span class="a4-status is-warn">{'Left as it is'|i18n( 'kernel/contentcheck' )}</span> {if is_set( $reasons[$item.reason] )}{$reasons[$item.reason]|wash}{/if}{if and( $item.detail|ne( '' ), eq( $plan.action, 'permissions' ) )} <span class="a4-meta">{$item.detail|wash}</span>{/if}{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>

<form method="post" action={'/contentcheck/storageaction'|ezurl}>
<input type="hidden" name="{$token_field|wash}" value="{$form_token|wash}" />
<input type="hidden" name="{$confirm_field|wash}" value="{$confirm_token|wash}" />
<input type="hidden" name="NodeID" value="{$node_id}" />
<input type="hidden" name="Show" value="{$show|wash}" />
<input type="hidden" name="Action" value="{$plan.action|wash}" />
{foreach $plan.allowed as $allowed_item}
<input type="hidden" name="Items[]" value="{$allowed_item|wash}" />
{/foreach}
{if and( $needs_delete_box, $plan.allowed|count|gt( 0 ) )}
<p><label class="a4-check" for="cc-confirm-delete"><input type="checkbox" id="cc-confirm-delete" name="ConfirmDelete" value="1" required="required" /> {'I understand that these files are deleted permanently.'|i18n( 'kernel/contentcheck' )}</label></p>
{/if}
<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        {if $plan.allowed|count|gt( 0 )}<button type="submit" class="a4-btn {if eq( $plan.action, 'delete' )}a4-btn-danger{else}a4-btn-primary{/if}" name="ConfirmButton" value="1">{'Confirm for %count files'|i18n( 'kernel/contentcheck',, hash( '%count', $plan.allowed|count ) )}</button>{/if}
        <button type="submit" class="a4-btn" name="CancelButton" value="1" formnovalidate="formnovalidate">{'Cancel'|i18n( 'kernel/contentcheck' )}</button>
    </div>
</div>
</form>
{/if}

</div></div></div>
</div>
{undef $titles $happens $reasons}
