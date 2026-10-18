{* The table of contents of a manual (exphelp/manual.tpl), one level; chapters include it again for their entries.
   Variables: toc_entries (kind chapter|topic, title, shortdesc, url, preview, children).
   The same file is in design/admin and design/admin4. *}
<ol class="a4-help-toc-list">
{foreach $toc_entries as $toc_e}
    <li class="a4-help-toc-item">
    {if eq( $toc_e.kind, 'chapter' )}
        <strong dir="auto">{$toc_e.title|wash}</strong>{if $toc_e.preview} <span class="a4-tag is-warn">{'Preview'|i18n( 'kernel/exphelp' )}</span>{/if}
        {if $toc_e.shortdesc|ne( '' )}<span class="a4-meta"> {$toc_e.shortdesc|wash}</span>{/if}
        {if $toc_e.children|count}{include name=ManualToc uri='design:exphelp/parts/manual_toc.tpl' toc_entries=$toc_e.children}{/if}
    {else}
        <a class="a4-help-toc-link" href={$toc_e.url|ezurl} dir="auto">{$toc_e.title|wash}</a>{if $toc_e.preview} <span class="a4-tag is-warn">{'Preview'|i18n( 'kernel/exphelp' )}</span>{/if}
        {if $toc_e.shortdesc|ne( '' )}<span class="a4-meta"> {$toc_e.shortdesc|wash}</span>{/if}
    {/if}
    </li>
{/foreach}
</ol>
