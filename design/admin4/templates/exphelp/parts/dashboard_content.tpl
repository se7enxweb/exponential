{* The dashboard's section of help maintained as content (doc/features/6.0/help-content.md): the manuals and the newest
   topics the user may see. Variables: content (help_content of the dashboard view: manuals, topics, count).
   The same file is in design/admin and design/admin4. *}
<section class="a4-card" aria-labelledby="help-dash-content">
    <div class="a4-help-section-head">
        <h2 id="help-dash-content"><span class="a4-icon a4-bi-journal-text" aria-hidden="true"></span>{'Manuals and help topics'|i18n( 'kernel/exphelp' )}</h2>
    </div>
    <ul class="a4-help-featured">
    {foreach $content.manuals as $content_m}
        <li class="a4-help-feature">
            <a href={concat( 'exphelp/manual/', $content_m.remote_id )|ezurl}>
                <span class="a4-help-tile is-small" aria-hidden="true"><span class="a4-icon a4-bi-journal-text"></span></span>
                <span class="a4-help-feature-text">
                    <span class="a4-help-feature-title">{$content_m.title|wash}</span>
                    {if $content_m.shortdesc|ne( '' )}<span class="a4-help-feature-summary">{$content_m.shortdesc|shorten( 140 )|wash}</span>{/if}
                    {if $content_m.preview}<span class="a4-help-feature-meta"><span class="a4-tag is-warn">{'Preview'|i18n( 'kernel/exphelp' )}</span></span>{/if}
                </span>
            </a>
        </li>
    {/foreach}
    {foreach $content.topics as $content_t}
        <li class="a4-help-feature">
            <a href={$content_t.url|ezurl}>
                <span class="a4-help-tile is-small" aria-hidden="true"><span class="a4-icon a4-bi-file-earmark-code"></span></span>
                <span class="a4-help-feature-text">
                    <span class="a4-help-feature-title">{$content_t.title|wash}</span>
                    {if $content_t.summary|ne( '' )}<span class="a4-help-feature-summary">{$content_t.summary|shorten( 140 )|wash}</span>{/if}
                    {if $content_t.preview}<span class="a4-help-feature-meta"><span class="a4-tag is-warn">{'Preview'|i18n( 'kernel/exphelp' )}</span></span>{/if}
                </span>
            </a>
        </li>
    {/foreach}
    </ul>
    <p class="a4-meta"><a href={'exphelp/manual'|ezurl}>{'All manuals'|i18n( 'kernel/exphelp' )}</a> · {'%count help topics'|i18n( 'kernel/exphelp',, hash( '%count', $content.count ) )|wash}</p>
</section>
