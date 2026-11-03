{* The dashboard's list of help topics waiting for release (doc/eng-us/features/6.0/help-content.md, "Waiting for
   release"): the topics in Review, the longest waiting first, for who may release them (the role "Help release"). The
   topic pages link here (#help-dash-waiting) when someone else has to release a topic. A topic the user wrote is
   listed too, marked: someone else releases it. Release posts to exphelp/state like the button on the topic.
   Variables: waiting (help_waiting of the dashboard view: items with help_id, title, url, modified, is_author; count,
   offset, limit), view_parameters (offset, for the navigator).
   The same file is in design/admin and design/admin4. *}
<section class="a4-card" id="help-dash-waiting" aria-labelledby="help-dash-waiting-title">
    <div class="a4-help-section-head">
        <h2 id="help-dash-waiting-title"><span class="a4-icon a4-bi-journal-check" aria-hidden="true"></span>{'Help topics waiting for release'|i18n( 'kernel/exphelp' )}</h2>
    </div>
    <p class="a4-meta">{'These topics are in Review. Readers see them once someone who did not write the current version releases them.'|i18n( 'kernel/exphelp' )}</p>
    <div class="a4-table-wrap" role="region" aria-labelledby="help-dash-waiting-title" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Topic'|i18n( 'kernel/exphelp' )}</th>
            <th scope="col">{'Updated'|i18n( 'kernel/exphelp' )}</th>
            <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'kernel/exphelp' )}</span></th>
        </tr></thead>
        <tbody>
        {foreach $waiting.items as $waiting_t}
        <tr>
            <td><a href={$waiting_t.url|ezurl} dir="auto">{$waiting_t.title|wash}</a><br /><span class="a4-meta">{$waiting_t.help_id|wash}</span></td>
            <td>{if $waiting_t.modified}{$waiting_t.modified|l10n( shortdate )}{/if}</td>
            <td>{if $waiting_t.is_author}<span class="a4-meta">{'You wrote the current version, so someone else with the release role releases it.'|i18n( 'kernel/exphelp' )}</span>{else}<form method="post" action={concat( 'exphelp/state/', $waiting_t.help_id )|ezurl}><button type="submit" class="a4-btn a4-btn-small a4-btn-primary" name="HelpState" value="released"><span class="a4-icon a4-bi-check-lg" aria-hidden="true"></span> {'Release'|i18n( 'kernel/exphelp' )}<span class="a4-sr-only">: {$waiting_t.title|wash}</span></button></form>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri='exphelp/dashboard'
             item_count=$waiting.count
             view_parameters=$view_parameters
             item_limit=$waiting.limit}
</section>
