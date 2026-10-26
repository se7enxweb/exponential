{* The review workflow of a help topic or manual (doc/eng-us/features/6.0/help-content.md, "The review workflow"): where
   it stands (Draft, Review, Released), the steps the user may take as buttons (POST to exphelp/state, the kernel's form
   token is added to every POST form), the result of the last step, and for a topic the style guide's findings.
   Shown only to who may edit the item or assign its state.

   Variables: workflow (state, steps: state, allowed, reason), step (the result of the last step or ''), state_url
   (exphelp/state/<help_id> or exphelp/state/manual/<remote_id>), findings, findings_counts (ERROR, WARNING, NOTICE;
   findings false: none shown, a manual), feedback (yes, no, reasons; or null), prefix (ids).
   The same file is in design/admin and design/admin4. *}
{def $wf_states = array( 'draft', 'review', 'released' )
     $wf_current = $workflow.state
     $wf_done = true()}
<section class="a4-card a4-help-workflow" aria-labelledby="{$prefix}-wf-title">
    <h2 class="a4-help-section-head" id="{$prefix}-wf-title"><span class="a4-icon a4-bi-journal-check" aria-hidden="true"></span> {'Review and release'|i18n( 'kernel/exphelp' )}</h2>

    {if $step|ne( '' )}
    <p class="a4-notice {if eq( $step, 'refused-author' )}is-warn{elseif eq( $step, 'refused' )}is-bad{else}is-ok{/if}" role="status">
        {switch match=$step}
        {case match='review'}{'The topic is in Review now.'|i18n( 'kernel/exphelp' )}{/case}
        {case match='released'}{'The topic is Released: readers see it now.'|i18n( 'kernel/exphelp' )}{/case}
        {case match='draft'}{'The topic is a Draft again.'|i18n( 'kernel/exphelp' )}{/case}
        {case match='refused-author'}{'You wrote the current version, so someone else with the release role releases it.'|i18n( 'kernel/exphelp' )}{/case}
        {case}{'The state was not changed: your role does not allow this step.'|i18n( 'kernel/exphelp' )}{/case}
        {/switch}
    </p>
    {/if}

    <ol class="a4-steps" aria-label="{'Review workflow'|i18n( 'kernel/exphelp' )|wash}">
    {foreach $wf_states as $wf_s}
        {if eq( $wf_s, $wf_current )}{set $wf_done = false()}{/if}
        <li class="{if eq( $wf_s, $wf_current )}is-current{elseif $wf_done}is-done{/if}"{if eq( $wf_s, $wf_current )} aria-current="step"{/if}>{switch match=$wf_s}{case match='draft'}{'Draft'|i18n( 'kernel/exphelp' )}{/case}{case match='review'}{'Review'|i18n( 'kernel/exphelp' )}{/case}{case}{'Released'|i18n( 'kernel/exphelp' )}{/case}{/switch}</li>
    {/foreach}
    </ol>

    {if $workflow.steps|count}
    <form class="a4-actions" method="post" action={$state_url|ezurl}>
    {foreach $workflow.steps as $wf_step}
        {if $wf_step.allowed}
        <button type="submit" class="a4-btn{if eq( $wf_step.state, 'released' )} a4-btn-primary{/if}" name="HelpState" value="{$wf_step.state|wash}">{switch match=$wf_step.state}{case match='review'}{if eq( $wf_current, 'released' )}<span class="a4-icon a4-bi-arrow-counterclockwise" aria-hidden="true"></span> {'Back to Review'|i18n( 'kernel/exphelp' )}{else}<span class="a4-icon a4-bi-arrow-up-circle" aria-hidden="true"></span> {'Send to Review'|i18n( 'kernel/exphelp' )}{/if}{/case}{case match='released'}<span class="a4-icon a4-bi-check-lg" aria-hidden="true"></span> {'Release'|i18n( 'kernel/exphelp' )}{/case}{case}<span class="a4-icon a4-bi-arrow-counterclockwise" aria-hidden="true"></span> {'Back to Draft'|i18n( 'kernel/exphelp' )}{/case}{/switch}</button>
        {else}
        <span class="a4-meta"><span class="a4-icon a4-bi-person-check" aria-hidden="true"></span> {'You wrote the current version, so someone else with the release role releases it.'|i18n( 'kernel/exphelp' )}</span>
        {/if}
    {/foreach}
    </form>
    {/if}

    {if is_array( $findings )}
    <h3 class="a4-h3">{'Style guide check'|i18n( 'kernel/exphelp' )}</h3>
    {if $findings|count|eq( 0 )}
    <p class="a4-notice is-ok">{'The style guide check found nothing.'|i18n( 'kernel/exphelp' )}</p>
    {else}
    <p class="a4-meta">{'%errors errors, %warnings warnings, %notices notices. An error keeps ./console exp:help:content check from passing.'|i18n( 'kernel/exphelp',, hash( '%errors', $findings_counts.ERROR, '%warnings', $findings_counts.WARNING, '%notices', $findings_counts.NOTICE ) )|wash}</p>
    <div class="a4-table-wrap" role="region" aria-labelledby="{$prefix}-wf-title" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Level'|i18n( 'kernel/exphelp' )}</th>
            <th scope="col">{'Rule'|i18n( 'kernel/exphelp' )}</th>
            <th scope="col">{'Field'|i18n( 'kernel/exphelp' )}</th>
            <th scope="col">{'Finding'|i18n( 'kernel/exphelp' )}</th>
        </tr></thead>
        <tbody>
        {foreach $findings as $wf_f}
        <tr>
            <td><span class="a4-tag {if eq( $wf_f.level, 'ERROR' )}is-bad{elseif eq( $wf_f.level, 'WARNING' )}is-warn{else}is-info{/if}">{$wf_f.level|wash}</span></td>
            <td><code dir="ltr">{$wf_f.code|wash}</code></td>
            <td><code dir="ltr">{$wf_f.field|wash}</code></td>
            <td dir="auto">{$wf_f.message|wash}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {/if}
    {/if}

    {if $feedback}
    <h3 class="a4-h3">{'Was this helpful?'|i18n( 'kernel/exphelp' )}</h3>
    <p class="a4-meta">{'Readers answered yes %yes times and no %no times.'|i18n( 'kernel/exphelp',, hash( '%yes', $feedback.yes, '%no', $feedback.no ) )|wash}</p>
    {if $feedback.reasons|count}
    <p class="a4-meta">{'Reasons given for no:'|i18n( 'kernel/exphelp' )}
    {foreach $feedback.reasons as $wf_reason => $wf_count}
        <span class="a4-tag">{include uri='design:exphelp/parts/feedback_reason.tpl' reason=$wf_reason}: {$wf_count|wash}</span>
    {/foreach}
    </p>
    {/if}
    {/if}
</section>
{undef $wf_states $wf_current $wf_done}
