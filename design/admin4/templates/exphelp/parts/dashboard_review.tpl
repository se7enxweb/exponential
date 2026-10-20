{* The dashboard's list of help topics overdue for review (doc/en/us/features/6.0/help-content.md, "Overdue reviews"):
   the review date has passed, or the topic has been in Review too long. Shown only to who can act on a topic (edit it,
   or assign its state). Variables: review (help_review of the dashboard view: items, count, max_days).
   The same file is in design/admin and design/admin4. *}
<section class="a4-card" aria-labelledby="help-dash-review">
    <div class="a4-help-section-head">
        <h2 id="help-dash-review"><span class="a4-icon a4-bi-hourglass-split" aria-hidden="true"></span>{'Help topics overdue for review'|i18n( 'kernel/exphelp' )}</h2>
    </div>
    <p class="a4-meta">{'The review date has passed, or the topic has been in Review for more than %days days without a change.'|i18n( 'kernel/exphelp',, hash( '%days', $review.max_days ) )|wash}</p>
    <div class="a4-table-wrap" role="region" aria-labelledby="help-dash-review" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Topic'|i18n( 'kernel/exphelp' )}</th>
            <th scope="col">{'State'|i18n( 'kernel/exphelp' )}</th>
            <th scope="col">{'Overdue'|i18n( 'kernel/exphelp' )}</th>
            <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'kernel/exphelp' )}</span></th>
        </tr></thead>
        <tbody>
        {foreach $review.items as $review_t}
        <tr>
            <td><a href={$review_t.url|ezurl} dir="auto">{$review_t.title|wash}</a><br /><span class="a4-meta">{$review_t.help_id|wash}</span></td>
            <td><span class="a4-tag{if eq( $review_t.state, 'review' )} is-warn{elseif eq( $review_t.state, 'released' )} is-ok{/if}">{cond( eq( $review_t.state, 'released' ), 'Released'|i18n( 'kernel/exphelp' ), eq( $review_t.state, 'review' ), 'Review'|i18n( 'kernel/exphelp' ), 'Draft'|i18n( 'kernel/exphelp' ) )}</span></td>
            <td>{if eq( $review_t.reason, 'review-date' )}{'Review date %date passed %days days ago'|i18n( 'kernel/exphelp',, hash( '%date', $review_t.since|l10n( shortdate ), '%days', $review_t.days ) )|wash}{else}{'In Review for %days days without a change'|i18n( 'kernel/exphelp',, hash( '%days', $review_t.days ) )|wash}{/if}</td>
            <td>{if $review_t.node_id}<a class="a4-btn a4-btn-small a4-btn-ghost" href={concat( 'content/view/full/', $review_t.node_id )|ezurl}><span class="a4-icon a4-bi-pencil" aria-hidden="true"></span> {cond( $review_t.can_edit, 'Edit'|i18n( 'kernel/exphelp' ), 'Set state'|i18n( 'kernel/exphelp' ) )}<span class="a4-sr-only">: {$review_t.title|wash}</span></a>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {if $review.count|gt( $review.items|count )}
    <p class="a4-meta">{'%shown of %count overdue topics are listed. ./console exp:help:content review lists them all.'|i18n( 'kernel/exphelp',, hash( '%shown', $review.items|count, '%count', $review.count ) )|wash}</p>
    {/if}
</section>
