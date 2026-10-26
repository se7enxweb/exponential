{* The page of a help topic (help_concept, help_task, help_reference, help_troubleshooting) on the public site
   (doc/eng-us/features/6.0/help-content.md, "Help on the site's pages"). What the reader may see is decided by the
   permissions (expHelpContentSite): a Released topic they may read, or a preview for who may edit it. The text is the
   help's rendering of the topic's fields (safe HTML, not washed); related topics and figures only when the reader may
   see them; "Was this helpful?" under a Released topic for who may answer (exphelp/feedback).

   Variables: node, view_parameters (helpful: thanks after an answer). *}
{def $help = fetch( 'exphelp', 'site_topic', hash( 'object_id', $node.contentobject_id ) )}
<article class="exphelp-topic exphelp-topic-{if $help}{$help.type|wash}{else}none{/if}">
    <header class="exphelp-head">
        <h1>{if $help}{$help.title|wash}{else}{$node.name|wash}{/if}</h1>
        {if and( $help, $help.shortdesc|ne( '' ) )}
        <p class="exphelp-shortdesc">{$help.shortdesc|wash}</p>
        {/if}
        {if and( $help, $help.preview )}
        <p class="exphelp-preview" role="note"><strong>{'Preview'|i18n( 'kernel/exphelp' )}:</strong> {'This topic is not Released; readers do not see it.'|i18n( 'kernel/exphelp' )}</p>
        {/if}
    </header>

    {if $help}
    <div class="exphelp-body">
{$help.html}
    </div>

    {foreach $help.figures as $help_f}
    <figure class="exphelp-figure">
        <img src={$help_f.image_url|ezroot} alt="{$help_f.alt_text|wash}" loading="lazy" decoding="async" />
        {if $help_f.caption|ne( '' )}<figcaption>{$help_f.caption|wash}</figcaption>{/if}
    </figure>
    {/foreach}

    {if $help.related|count}
    <nav class="exphelp-related" aria-labelledby="exphelp-related-{$node.node_id}">
        <h2 id="exphelp-related-{$node.node_id}">{'Related topics'|i18n( 'kernel/exphelp' )}</h2>
        <ul>
        {foreach $help.related as $help_r}
            <li><a href="{$help_r.url|wash}">{$help_r.title|wash}</a>{if $help_r.shortdesc|ne( '' )} <span class="exphelp-related-summary">{$help_r.shortdesc|wash}</span>{/if}</li>
        {/foreach}
        </ul>
    </nav>
    {/if}

    {if $help.feedback}
    <section class="exphelp-feedback" aria-labelledby="exphelp-feedback-{$node.node_id}">
        <h2 id="exphelp-feedback-{$node.node_id}">{'Was this helpful?'|i18n( 'kernel/exphelp' )}</h2>
        {if and( is_set( $view_parameters.helpful ), eq( $view_parameters.helpful, 'thanks' ) )}
        <p role="status">{'Thank you. Your answer helps us improve the help.'|i18n( 'kernel/exphelp' )}</p>
        {else}
        <form method="post" action={concat( 'exphelp/feedback/', $help.help_id )|ezurl}>
            <input type="hidden" name="RedirectURI" value="/{$node.url_alias|wash}" />
            <p><button type="submit" name="Helpful" value="yes">{'Yes'|i18n( 'kernel/exphelp' )}</button></p>
            <p>
                <label for="exphelp-reason-{$node.node_id}">{'If not, what was wrong?'|i18n( 'kernel/exphelp' )}</label>
                <select id="exphelp-reason-{$node.node_id}" name="Reason">
                    <option value="">{'No reason given'|i18n( 'kernel/exphelp' )}</option>
                    {foreach array( 'outdated', 'unclear', 'incomplete', 'wrong', 'other' ) as $help_reason}
                    <option value="{$help_reason}">{include uri='design:exphelp/parts/feedback_reason.tpl' reason=$help_reason}</option>
                    {/foreach}
                </select>
                <button type="submit" name="Helpful" value="no">{'No'|i18n( 'kernel/exphelp' )}</button>
            </p>
            <p class="exphelp-privacy">{'Only your answer is kept, not who you are.'|i18n( 'kernel/exphelp' )}</p>
        </form>
        {/if}
    </section>
    {/if}
    {/if}
</article>
{undef $help}
