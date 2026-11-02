{* The result of an action of Dashboard > Recent content: per item done, refused or failed, and why. Shown by
   moderation/action (an action run in the request, or a form that could not be read: errors) and moderation/report
   (an action run as the content job 'moderation': job).

   Variables: form, action_name, extra (the section or state set), report (id, name, status, message), counts (done,
   refused, failed, total), errors (sentences), job (id, state, active, created, finished, error; null in the request),
   return_url. The same file is in design/admin and design/admin4. *}
<div class="a4-page a4-moderation" id="exp-moderation-report">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{if $errors|count}{'Nothing was done'|i18n( 'design/admin/moderation' )}{elseif $extra}{'%action (%extra)'|i18n( 'design/admin/moderation',, hash( '%action', $action_name, '%extra', $extra ) )|wash}{else}{$action_name|wash}{/if}</h1>
        {if $form.reason}<p class="a4-pagehead-sub">{'Reason: %reason'|i18n( 'design/admin/moderation',, hash( '%reason', $form.reason ) )|wash}</p>{/if}
    </div>
</div>

{if $errors|count}
<div class="a4-notice is-bad" role="alert">{foreach $errors as $error}<p>{$error|wash}</p>{/foreach}</div>
{/if}

{if $job}
    {if $job.active}
<div class="a4-notice" role="status"><p>{'The action is still running in the background.'|i18n( 'design/admin/moderation' )} <a href={concat( '/content/job/', $job.id )|ezurl}>{'Its progress'|i18n( 'design/admin/moderation' )}</a></p></div>
    {elseif eq( $job.state, 'failed' )}
<div class="a4-notice is-bad" role="alert"><p>{'The background action failed: %error'|i18n( 'design/admin/moderation',, hash( '%error', $job.error ) )|wash} <a href={concat( '/content/job/', $job.id )|ezurl}>{'Its progress'|i18n( 'design/admin/moderation' )}</a></p></div>
    {/if}
{/if}

{if $report|count}
<dl class="a4-facts">
    <div><dt>{'Done'|i18n( 'design/admin/moderation' )}</dt><dd>{if $counts.done}<span class="a4-badge is-ok">{$counts.done}</span>{else}0{/if}</dd></div>
    <div><dt>{'Refused'|i18n( 'design/admin/moderation' )}</dt><dd>{if $counts.refused}<span class="a4-badge is-warn">{$counts.refused}</span>{else}0{/if}</dd></div>
    <div><dt>{'Failed'|i18n( 'design/admin/moderation' )}</dt><dd>{if $counts.failed}<span class="a4-badge is-bad">{$counts.failed}</span>{else}0{/if}</dd></div>
    <div><dt>{'Items'|i18n( 'design/admin/moderation' )}</dt><dd>{$counts.total}</dd></div>
</dl>

<section class="a4-card is-flush a4-section" aria-labelledby="moderation-report-items">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="moderation-report-items">{'Per item'|i18n( 'design/admin/moderation' )}</h2></div>
    <div class="a4-table-wrap" role="region" aria-labelledby="moderation-report-items" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Item'|i18n( 'design/admin/moderation' )}</th>
            <th scope="col">{'Result'|i18n( 'design/admin/moderation' )}</th>
            <th scope="col">{'Why'|i18n( 'design/admin/moderation' )}</th>
        </tr></thead>
        <tbody>
        {foreach $report as $entry}
        <tr>
            <td>{if $entry.name}{$entry.name|wash}{else}<span class="a4-muted">{'(no name)'|i18n( 'design/admin/moderation' )}</span>{/if} <span class="a4-meta">{'ID %id'|i18n( 'design/admin/moderation',, hash( '%id', $entry.id ) )}</span></td>
            <td>{switch match=$entry.status}
                {case match='done'}<span class="a4-badge is-ok">{'Done'|i18n( 'design/admin/moderation' )}</span>{/case}
                {case match='refused'}<span class="a4-badge is-warn">{'Refused'|i18n( 'design/admin/moderation' )}</span>{/case}
                {case}<span class="a4-badge is-bad">{'Failed'|i18n( 'design/admin/moderation' )}</span>{/case}
                {/switch}</td>
            <td>{$entry.message|wash}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
</section>
{/if}

<p class="a4-actions"><a class="a4-btn a4-btn-primary" href={concat( '/', $return_url )|ezurl}>{'Back to the list'|i18n( 'design/admin/moderation' )}</a></p>

</div>
