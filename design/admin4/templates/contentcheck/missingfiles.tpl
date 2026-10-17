{* The missing files section of contentcheck/brokenrelations (doc/features/6.0/content-checks.md, "Missing files"):
   shown when the target "Missing files" is ticked.

   Variables: files (from expContentCheckFiles::section(): result (node, rows, checked, last, done, error), params
   (node_id, problems, versions, after, limit), can_fix, actions, max_items, form_token, token_field, feedback),
   page_uri, filter_suffix (the page's query string, with the missing files asked for). The objects are walked by
   id: "Next page of missing files" goes on after the last object checked (FileAfter). With contentcheck/fixfiles
   the ticked rows (or one row's button) are posted to contentcheck/fixfiles, which shows what will happen and asks
   for a confirmation first. The same file is in design/admin and design/admin4. *}
{def $cc_action_names = hash( 'regenerate', 'Regenerate the aliases from the original'|i18n( 'kernel/contentcheck' ),
                              'restore', 'Restore from another version'|i18n( 'kernel/contentcheck' ),
                              'replace', 'Re-upload or replace the file'|i18n( 'kernel/contentcheck' ),
                              'remove', 'Remove the dead reference'|i18n( 'kernel/contentcheck' ),
                              'review', 'Mark for review'|i18n( 'kernel/contentcheck' ) )
     $cc_rows = $files.result.rows
     $cc_selectable = false()
     $cc_separator = cond( $filter_suffix|eq( '?' ), '', '&' )}
{if $files.can_fix}{foreach $cc_rows as $cc_row}{if $cc_row.can_fix}{set $cc_selectable = true()}{/if}{/foreach}{/if}

<section class="a4-section" aria-labelledby="cc-files-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="cc-files-title">{'Missing files in %dir'|i18n( 'kernel/contentcheck',, hash( '%dir', $files.storage_dir ) )|wash}</h2>
    <span class="a4-meta">{if $files.params.after|gt( 0 )}{'%count objects checked after object %after.'|i18n( 'kernel/contentcheck',, hash( '%count', $files.result.checked, '%after', $files.params.after ) )}{else}{'%count objects checked.'|i18n( 'kernel/contentcheck',, hash( '%count', $files.result.checked ) )}{/if}</span>
</div>
<p class="a4-meta">{'Images, files and media whose file is not in the storage, in the published version and in drafts, and images whose aliases are missing or were made with other settings.'|i18n( 'kernel/contentcheck' )}</p>

{if $files.feedback}
    {if eq( $files.feedback.reason, 'changed' )}
<div class="a4-notice is-warn" role="alert">{'Nothing was changed: the files or your access changed after the confirmation. Choose the rows again.'|i18n( 'kernel/contentcheck' )}</div>
    {elseif and( $files.feedback.fixed|eq( 0 ), $files.feedback.failed|eq( 0 ) )}
<div class="a4-notice is-warn" role="alert">{'Nothing was changed.'|i18n( 'kernel/contentcheck' )}</div>
    {else}
<div class="a4-notice {if $files.feedback.failed|gt( 0 )}is-warn{else}is-ok{/if}" role="status">{'%count field versions done.'|i18n( 'kernel/contentcheck',, hash( '%count', $files.feedback.fixed ) )}{if $files.feedback.failed|gt( 0 )} {'%count could not be done.'|i18n( 'kernel/contentcheck',, hash( '%count', $files.feedback.failed ) )}{/if}</div>
        {if $files.feedback.failed_items|count|gt( 0 )}
<ul class="a4-meta">
{foreach $files.feedback.failed_items as $cc_failed}
    <li>{$cc_failed.object_name|wash} &middot; {$cc_failed.field_name|wash} &middot; {'Version %version'|i18n( 'kernel/contentcheck',, hash( '%version', $cc_failed.version ) )}: {include uri='design:contentcheck/fixreason.tpl' reason=$cc_failed.why}</li>
{/foreach}
</ul>
        {/if}
    {/if}
{/if}

{if $files.can_fix}
<form name="contentcheckmissingfiles" method="post" action={'/contentcheck/fixfiles'|ezurl}>
<input type="hidden" name="{$files.token_field|wash}" value="{$files.form_token|wash}" />
<input type="hidden" name="NodeID" value="{$files.params.node_id}" />
{/if}

{if $cc_rows|count|eq( 0 )}
<p class="a4-empty">{if $files.result.done}{'No missing file and no alias problem found.'|i18n( 'kernel/contentcheck' )}{else}{'Nothing found among these objects. Go on with the next page.'|i18n( 'kernel/contentcheck' )}{/if}</p>
{else}
<div class="a4-table-wrap">
<table class="a4-table">
<thead>
<tr>
    {if $cc_selectable}<th scope="col" class="tight"><span class="a4-sr-only">{'Select'|i18n( 'kernel/contentcheck' )}</span></th>{/if}
    <th scope="col">{'Object'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Field'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Version'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Expected path'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Last modified'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'State'|i18n( 'kernel/contentcheck' )}</th>
    {if $files.can_fix}<th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'kernel/contentcheck' )}</span></th>{/if}
</tr>
</thead>
<tbody>
{foreach $cc_rows as $cc_row}
{include uri='design:contentcheck/file_row.tpl' row=$cc_row selectable=$cc_selectable can_fix=$files.can_fix}
{/foreach}
</tbody>
</table>
</div>
{/if}

<div class="a4-listfoot">
    <div class="a4-pager">
        {if $files.params.after|gt( 0 )}<a class="a4-btn" href={concat( $page_uri, $filter_suffix )|ezurl}>{'Missing files from the start'|i18n( 'kernel/contentcheck' )}</a>{/if}
        {if $files.result.done|not}<a class="a4-btn" href={concat( $page_uri, $filter_suffix, $cc_separator, 'FileAfter=', $files.result.last )|ezurl}>{'Next page of missing files'|i18n( 'kernel/contentcheck' )}</a>{/if}
    </div>
</div>

{if $files.can_fix}
{if $cc_selectable}
<div class="a4-toolbar is-bottom">
    <div class="a4-field">
        <label for="cc-fix-action">{'With the ticked rows'|i18n( 'kernel/contentcheck' )}</label>
        <select id="cc-fix-action" name="FixAction" aria-describedby="cc-fix-help">
        {foreach $files.actions as $cc_action}
            <option value="{$cc_action|wash}">{$cc_action_names[$cc_action]|wash}</option>
        {/foreach}
        </select>
    </div>
    <div class="a4-actions">
        <button type="submit" class="a4-btn a4-btn-primary" name="PreviewButton" value="1">{'Preview'|i18n( 'kernel/contentcheck' )}</button>
    </div>
    <p class="a4-meta" id="cc-fix-help">{'The next page shows what will happen to each row and asks you to confirm. At most %count rows at a time; they are done in batches.'|i18n( 'kernel/contentcheck',, hash( '%count', $files.max_items ) )}</p>
</div>
{/if}
</form>
{/if}
</section>
{undef $cc_action_names $cc_rows $cc_selectable $cc_separator}
