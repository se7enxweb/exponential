{* A scan of the storage in progress (doc/en/us/features/6.0/content-checks.md, "Storage files"): the share done and the
   button that runs the next step. The button carries data-a4-autocontinue: contentcheck_storage.js presses it after
   a moment, unless the user stops it; without JavaScript it is pressed by hand.

   Variables: state, percent, node_id, show, form_token, token_field. The same file is in design/admin and
   design/admin4. *}
<div class="context-block a4-page a4-contentcheck">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Scanning the storage'|i18n( 'kernel/contentcheck' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro" role="status">{'%files files read so far (%size), %orphans of them orphans; %percent % done.'|i18n( 'kernel/contentcheck',, hash( '%files', $state.counts.files, '%size', $state.counts.bytes|si( byte ), '%orphans', $state.counts.orphans, '%percent', $percent ) )|wash}</p>
<div class="a4-meter" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{$percent}" aria-label="{'Progress'|i18n( 'kernel/contentcheck' )|wash}"><span class="a4-pct-{$percent}"></span></div>

<form method="post" action={'/contentcheck/storageaction'|ezurl}>
<input type="hidden" name="{$token_field|wash}" value="{$form_token|wash}" />
<input type="hidden" name="NodeID" value="{$node_id}" />
<input type="hidden" name="Show" value="{$show|wash}" />
<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button type="submit" class="a4-btn a4-btn-primary" name="ContinueButton" value="1" data-a4-autocontinue="1">{'Continue the scan'|i18n( 'kernel/contentcheck' )}</button>
        <button type="submit" class="a4-btn" name="CancelButton" value="1" data-a4-stop="1">{'Stop here'|i18n( 'kernel/contentcheck' )}</button>
    </div>
    <p class="a4-meta">{'The scan goes on by itself. Stop it at any time; it can be continued later.'|i18n( 'kernel/contentcheck' )}</p>
</div>
</form>

</div></div></div>
</div>
{ezscript_require( 'contentcheck_storage.js' )}
