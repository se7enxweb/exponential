{* The missing files section of contentcheck/brokenrelations (doc/features/6.0/content-checks.md, "Missing files"):
   shown when the target "Missing files" is ticked. The order of every result list of the checks: results header
   (title, "X to Y of N", the selection count), table, bulk actions, pages.

   Variables: files (from expContentCheckFiles::section(): result (node, rows of the page, count, offset, checked,
   incomplete, error), params (node_id, problems, versions), select_all, can_fix, actions, max_items, form_token,
   token_field, feedback, storage_dir), page_uri, filter_suffix (the page's query string, with the missing files
   asked for), limit, limit_choices, limit_preference. The pages are (file_offset) in the address, apart from the
   relations' own. With contentcheck/fixfiles the ticked rows (or one row's button) are posted to
   contentcheck/fixfiles, which shows what will happen and asks for a confirmation first. The same file is in
   design/admin and design/admin4. *}
{def $cc_action_names = hash( 'regenerate', 'Regenerate the aliases from the original'|i18n( 'kernel/contentcheck' ),
                              'restore', 'Restore from another version'|i18n( 'kernel/contentcheck' ),
                              'replace', 'Re-upload or replace the file'|i18n( 'kernel/contentcheck' ),
                              'remove', 'Remove the dead reference'|i18n( 'kernel/contentcheck' ),
                              'review', 'Mark for review'|i18n( 'kernel/contentcheck' ) )
     $cc_rows = $files.result.rows
     $cc_count = $files.result.count
     $cc_offset = $files.result.offset
     $cc_selectable = false()
     $cc_tickable = 0
     $cc_page_url = concat( $page_uri, '/(file_offset)/', $cc_offset, $filter_suffix )}
{if $files.can_fix}{foreach $cc_rows as $cc_row}{if $cc_row.can_fix}{set $cc_selectable = true()}{set $cc_tickable = $cc_tickable|inc}{/if}{/foreach}{/if}

<section class="a4-section" aria-labelledby="cc-files-title">
{include uri='design:contentcheck/results_head.tpl' title='Missing files in %dir'|i18n( 'kernel/contentcheck',, hash( '%dir', $files.storage_dir ) ) title_id='cc-files-title' offset=$cc_offset limit=$limit count=$cc_count count_text='%count objects checked.'|i18n( 'kernel/contentcheck',, hash( '%count', $files.result.checked ) )}
<p class="a4-meta">{'Images, files and media whose file is not in the storage, in the published version and in drafts, and images whose aliases are missing or were made with other settings.'|i18n( 'kernel/contentcheck' )}</p>

{if $files.result.incomplete}
<div class="a4-notice is-warn" role="status">{'Only the first %count objects were checked. Choose a smaller subtree to see the rest.'|i18n( 'kernel/contentcheck',, hash( '%count', $files.result.checked ) )}</div>
{/if}
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
<p class="a4-empty">{if is_set( $files.result.nothing_chosen )}{'Tick at least one problem and one version.'|i18n( 'kernel/contentcheck' )}{else}{'No missing file and no alias problem found.'|i18n( 'kernel/contentcheck' )}{/if}</p>
{else}
<div class="a4-table-wrap">
<table class="a4-table"{if $cc_selectable} data-a4-select="1"{/if}>
<thead>
<tr>
    {if $cc_selectable}<th scope="col" class="tight">{include uri='design:contentcheck/select_all.tpl' select_all=$files.select_all}</th>{/if}
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
{include uri='design:contentcheck/file_row.tpl' row=$cc_row selectable=$cc_selectable can_fix=$files.can_fix select_all=$files.select_all}
{/foreach}
</tbody>
</table>
</div>
{/if}
{if and( $files.can_fix, $cc_selectable )}
{set-block variable=cc_actions}<label class="a4-sr-only" for="cc-fix-action">{'With the ticked rows'|i18n( 'kernel/contentcheck' )}</label>
<select id="cc-fix-action" name="FixAction" aria-describedby="cc-fix-help">{foreach $files.actions as $cc_action}<option value="{$cc_action|wash}">{$cc_action_names[$cc_action]|wash}</option>{/foreach}</select>
<button type="submit" class="a4-btn a4-btn-primary" name="PreviewButton" value="1" aria-describedby="cc-fix-help">{'Preview'|i18n( 'kernel/contentcheck' )}</button>
{if $cc_count|gt( $cc_rows|count )}{foreach $files.params.problems as $cc_problem}<input type="hidden" name="Problem[]" value="{$cc_problem|wash}" />{/foreach}{foreach $files.params.versions as $cc_version}<input type="hidden" name="Version[]" value="{$cc_version|wash}" />{/foreach}
<button type="submit" class="a4-btn" name="AllResultsButton" value="1" aria-describedby="cc-fix-help">{'Preview for all %count results'|i18n( 'kernel/contentcheck',, hash( '%count', $cc_count ) )}</button>{/if}{/set-block}
{include uri='design:contentcheck/bulkbar.tpl' select_all=$files.select_all total=$cc_tickable page_url=$cc_page_url actions=$cc_actions help='The next page shows what will happen to each row and asks you to confirm. At most %count rows at a time; they are done in batches.'|i18n( 'kernel/contentcheck',, hash( '%count', $files.max_items ) ) help_id='cc-fix-help'}
{/if}
{if $files.can_fix}
</form>
{/if}

{include uri='design:contentcheck/pager.tpl' page_uri=$page_uri page_uri_suffix=$filter_suffix item_count=$cc_count view_parameters=hash( 'file_offset', $cc_offset ) limit=$limit limit_choices=$limit_choices limit_preference=$limit_preference offset_name='file_offset' size_title='Show %count findings per page.'|i18n( 'kernel/contentcheck' )}
</section>
{undef $cc_action_names $cc_rows $cc_count $cc_offset $cc_selectable $cc_tickable $cc_page_url}{if is_set( $cc_actions )}{undef $cc_actions}{/if}
