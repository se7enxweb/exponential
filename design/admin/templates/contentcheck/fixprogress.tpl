{* The progress of contentcheck/fixfiles between two batches (doc/features/6.0/content-checks.md, "Resolving").

   Variables: action, node_id, keys (the field versions still to do), done, failed, total, last_failed (the failures
   of the last batch), auto (go on by itself), confirm_token (bound to keys), confirm_field, form_token,
   token_field. The form posts the rest on; with "go on by itself" contentcheck_fixfiles.js sends it after a moment,
   unless the user stops. Without JavaScript the button does it. The same file is in design/admin and design/admin4. *}
{def $finished = sum( $done, $failed )
     $percent = cond( $total|gt( 0 ), div( mul( $finished, 100 ), $total )|floor, 0 )}
{if $auto}{ezscript_require( 'contentcheck_fixfiles.js' )}{/if}
<div class="context-block a4-page a4-contentcheck">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Resolve missing files'|i18n( 'kernel/contentcheck' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<section class="a4-section" aria-labelledby="cc-progress-title">
<h2 class="a4-h2" id="cc-progress-title">{'Progress'|i18n( 'kernel/contentcheck' )}</h2>
<div class="a4-meter" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{$percent}" aria-label="{'Progress'|i18n( 'kernel/contentcheck' )|wash}"><span class="a4-pct-{$percent}"></span></div>
<p role="status">{'%done of %total field versions done, %failed not done, %left to go.'|i18n( 'kernel/contentcheck',, hash( '%done', $done, '%total', $total, '%failed', $failed, '%left', $keys|count ) )}</p>
{if $last_failed|count|gt( 0 )}
<ul class="a4-meta">
{foreach $last_failed as $item}
    <li>{$item.object_name|wash} &middot; {$item.field_name|wash} &middot; {'Version %version'|i18n( 'kernel/contentcheck',, hash( '%version', $item.version ) )}: {include uri='design:contentcheck/fixreason.tpl' reason=$item.why}</li>
{/foreach}
</ul>
{/if}
</section>

<form method="post" action={'/contentcheck/fixfiles'|ezurl}{if $auto} data-cc-continue="1"{/if}>
<input type="hidden" name="{$token_field|wash}" value="{$form_token|wash}" />
<input type="hidden" name="{$confirm_field|wash}" value="{$confirm_token|wash}" />
<input type="hidden" name="NodeID" value="{$node_id}" />
<input type="hidden" name="FixAction" value="{$action|wash}" />
<input type="hidden" name="Done" value="{$done}" />
<input type="hidden" name="Failed" value="{$failed}" />
<input type="hidden" name="Total" value="{$total}" />
{if $auto}<input type="hidden" name="AutoContinue" value="1" />{/if}
{foreach $keys as $key}
<input type="hidden" name="Keys[]" value="{$key|wash}" />
{/foreach}
<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        {if $keys|count|gt( 0 )}<button type="submit" class="a4-btn a4-btn-primary" name="ContinueButton" value="1">{'Go on with the next batch'|i18n( 'kernel/contentcheck' )}</button>{/if}
        <button type="submit" class="a4-btn" name="CancelButton" value="1">{'Stop here'|i18n( 'kernel/contentcheck' )}</button>
    </div>
</div>
</form>

</div></div></div>
</div>
{undef $finished $percent}
