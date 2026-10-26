{* The page of a help manual (help_manual) or chapter (help_section) on the public site
   (doc/eng-us/features/6.0/help-content.md, "Help on the site's pages"): its title, short description and contents,
   two levels deep, each entry a link to the page of the topic or chapter. Only what the reader may see is listed
   (expHelpContentSite::map()).

   Variables: node. *}
{def $help = fetch( 'exphelp', 'site_map', hash( 'object_id', $node.contentobject_id ) )}
<article class="exphelp-map exphelp-map-{if $help}{$help.kind|wash}{else}none{/if}">
    <header class="exphelp-head">
        <h1>{if $help}{$help.title|wash}{else}{$node.name|wash}{/if}</h1>
        {if and( $help, $help.subtitle|ne( '' ) )}<p class="exphelp-subtitle">{$help.subtitle|wash}</p>{/if}
        {if and( $help, $help.shortdesc|ne( '' ) )}<p class="exphelp-shortdesc">{$help.shortdesc|wash}</p>{/if}
        {if and( $help, $help.edition|ne( '' ) )}<p class="exphelp-edition">{'Edition %edition'|i18n( 'kernel/exphelp',, hash( '%edition', $help.edition ) )|wash}</p>{/if}
        {if and( $help, $help.preview )}
        <p class="exphelp-preview" role="note"><strong>{'Preview'|i18n( 'kernel/exphelp' )}:</strong> {'This topic is not Released; readers do not see it.'|i18n( 'kernel/exphelp' )}</p>
        {/if}
    </header>
    {if and( $help, $help.toc|count )}
    <nav class="exphelp-toc" aria-labelledby="exphelp-toc-{$node.node_id}">
        <h2 id="exphelp-toc-{$node.node_id}">{'Contents'|i18n( 'kernel/exphelp' )}</h2>
        <ol>
        {foreach $help.toc as $help_e}
            <li><a href="{$help_e.url|wash}">{$help_e.title|wash}</a>{if $help_e.shortdesc|ne( '' )} <span class="exphelp-toc-summary">{$help_e.shortdesc|wash}</span>{/if}
            {if $help_e.children|count}
                <ol>
                {foreach $help_e.children as $help_c}
                    <li><a href="{$help_c.url|wash}">{$help_c.title|wash}</a>{if $help_c.shortdesc|ne( '' )} <span class="exphelp-toc-summary">{$help_c.shortdesc|wash}</span>{/if}</li>
                {/foreach}
                </ol>
            {/if}
            </li>
        {/foreach}
        </ol>
    </nav>
    {/if}
</article>
{undef $help}
