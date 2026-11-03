{* The review workflow of a help topic or manual (doc/eng-us/features/6.0/help-content.md, "The review workflow"): a
   status box with the state (Draft, In review, Released) and what it means for readers, the result of the last step
   with the next action, the steps the user may take as buttons with what each does (POST to exphelp/state, the
   kernel's form token is added to every POST form; a step back asks for a confirmation on its own page there), who
   can release when the user cannot, and for a topic the style guide's findings. Shown only to who may edit the item
   or assign its state.

   Variables: workflow (state, steps: state, allowed, reason; may_release, is_author), step (the result of the last
   step or ''), state_url (exphelp/state/<help_id> or exphelp/state/manual/<remote_id>), findings, findings_counts
   (ERROR, WARNING, NOTICE; findings false: none shown, a manual), feedback (yes, no, reasons; or null), prefix (ids).
   The waiting list is the dashboard's "Help topics waiting for release" (parts/dashboard_waiting.tpl).
   The same file is in design/admin and design/admin4. *}
{def $wf_states = array( 'draft', 'review', 'released' )
     $wf_current = $workflow.state
     $wf_done = true()
     $wf_can_release = false()
     $wf_may_release = and( is_set( $workflow.may_release ), $workflow.may_release )
     $wf_waiting_url = 'exphelp/dashboard#help-dash-waiting'}
{foreach $workflow.steps as $wf_step}{if and( eq( $wf_step.state, 'released' ), $wf_step.allowed )}{set $wf_can_release = true()}{/if}{/foreach}
<section class="a4-card a4-help-workflow" aria-labelledby="{$prefix}-wf-title">
    <h2 class="a4-help-section-head" id="{$prefix}-wf-title"><span class="a4-icon a4-bi-journal-check" aria-hidden="true"></span> {'Review and release'|i18n( 'kernel/exphelp' )}</h2>

    {if $wf_states|contains( $wf_current )}
    <p class="a4-notice no-icon a4-help-wf-status {if eq( $wf_current, 'released' )}is-ok{else}is-warn{/if}">
        <span class="a4-badge {if eq( $wf_current, 'released' )}is-ok{elseif eq( $wf_current, 'review' )}is-warn{else}is-info{/if}">{switch match=$wf_current}{case match='draft'}{'Draft'|i18n( 'kernel/exphelp' )}{/case}{case match='review'}{'In review'|i18n( 'kernel/exphelp' )}{/case}{case}{'Released'|i18n( 'kernel/exphelp' )}{/case}{/switch}</span>
        <span>{if eq( $wf_current, 'released' )}{'Readers see this page.'|i18n( 'kernel/exphelp' )}{else}{'Hidden from readers until someone releases it.'|i18n( 'kernel/exphelp' )}{/if}</span>
    </p>
    {/if}

    {if $step|ne( '' )}
    <div class="a4-notice {if eq( $step, 'refused-author' )}is-warn{elseif eq( $step, 'refused' )}is-bad{else}is-ok{/if}" role="status">
        <p>{switch match=$step}
        {case match='review'}{'The page is in Review now. It is hidden from readers until it is released.'|i18n( 'kernel/exphelp' )}{/case}
        {case match='released'}{'The page is Released: readers see it now.'|i18n( 'kernel/exphelp' )}{/case}
        {case match='draft'}{'The page is a Draft again. It is hidden from readers until it is reviewed and released.'|i18n( 'kernel/exphelp' )}{/case}
        {case match='refused-author'}{'You wrote the current version, so someone else with the release role releases it.'|i18n( 'kernel/exphelp' )}{/case}
        {case}{'The state was not changed: your role does not allow this step.'|i18n( 'kernel/exphelp' )}{/case}
        {/switch}</p>
        {if eq( $wf_current, 'review' )}
        {if $wf_can_release}
        <form class="a4-actions" method="post" action={$state_url|ezurl}>
            <button type="submit" class="a4-btn a4-btn-small a4-btn-primary" name="HelpState" value="released"><span class="a4-icon a4-bi-check-lg" aria-hidden="true"></span> {'Release now'|i18n( 'kernel/exphelp' )}</button>
        </form>
        {else}
        <p>{'Only a user with the role %role who did not write this version can release it.'|i18n( 'kernel/exphelp',, hash( '%role', 'Help release' ) )|wash}{if $wf_may_release} <a href={$wf_waiting_url|ezurl}>{'Help topics waiting for release'|i18n( 'kernel/exphelp' )}</a>{/if}</p>
        {/if}
        {/if}
    </div>
    {/if}

    <ol class="a4-steps" aria-label="{'Review workflow'|i18n( 'kernel/exphelp' )|wash}">
    {foreach $wf_states as $wf_s}
        {if eq( $wf_s, $wf_current )}{set $wf_done = false()}{/if}
        <li class="{if eq( $wf_s, $wf_current )}is-current{elseif $wf_done}is-done{/if}"{if eq( $wf_s, $wf_current )} aria-current="step"{/if}>{switch match=$wf_s}{case match='draft'}{'Draft'|i18n( 'kernel/exphelp' )}{/case}{case match='review'}{'Review'|i18n( 'kernel/exphelp' )}{/case}{case}{'Released'|i18n( 'kernel/exphelp' )}{/case}{/switch}</li>
    {/foreach}
    </ol>

    {if $workflow.steps|count}
    <form method="post" action={$state_url|ezurl}>
    <ul class="a4-help-wf-actions" role="list">
    {foreach $workflow.steps as $wf_step}
        {if $wf_step.allowed}
        <li class="a4-help-wf-action">
        <button type="submit" class="a4-btn{if eq( $wf_step.state, 'released' )} a4-btn-primary{/if}" name="HelpState" value="{$wf_step.state|wash}">{switch match=$wf_step.state}{case match='review'}{if eq( $wf_current, 'released' )}<span class="a4-icon a4-bi-arrow-counterclockwise" aria-hidden="true"></span> {'Back to Review'|i18n( 'kernel/exphelp' )}{else}<span class="a4-icon a4-bi-arrow-up-circle" aria-hidden="true"></span> {'Send to Review'|i18n( 'kernel/exphelp' )}{/if}{/case}{case match='released'}<span class="a4-icon a4-bi-check-lg" aria-hidden="true"></span> {'Release'|i18n( 'kernel/exphelp' )}{/case}{case}<span class="a4-icon a4-bi-arrow-counterclockwise" aria-hidden="true"></span> {'Back to Draft'|i18n( 'kernel/exphelp' )}{/case}{/switch}</button>
        <span class="a4-meta">{switch match=$wf_step.state}{case match='review'}{if eq( $wf_current, 'released' )}{'Hides it from readers until it is released again.'|i18n( 'kernel/exphelp' )}{else}{'A reviewer checks it next. Readers do not see it yet.'|i18n( 'kernel/exphelp' )}{/if}{/case}{case match='released'}{'Readers see it at once.'|i18n( 'kernel/exphelp' )}{/case}{case}{'Takes it out of review for more work. Readers do not see it.'|i18n( 'kernel/exphelp' )}{/case}{/switch}</span>
        </li>
        {else}
        <li class="a4-help-wf-action a4-meta"><span class="a4-icon a4-bi-person-check" aria-hidden="true"></span> {'You wrote the current version, so someone else with the release role releases it.'|i18n( 'kernel/exphelp' )}</li>
        {/if}
    {/foreach}
    </ul>
    </form>
    {/if}

    {if and( eq( $wf_current, 'review' ), $wf_can_release|not, eq( $step, '' ) )}
    <p class="a4-meta"><span class="a4-icon a4-bi-info-circle" aria-hidden="true"></span> {'Only a user with the role %role who did not write this version can release it.'|i18n( 'kernel/exphelp',, hash( '%role', 'Help release' ) )|wash}{if $wf_may_release} <a href={$wf_waiting_url|ezurl}>{'Help topics waiting for release'|i18n( 'kernel/exphelp' )}</a>{/if}</p>
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
{undef $wf_states $wf_current $wf_done $wf_can_release $wf_may_release $wf_waiting_url}
