{* The links between the URL pages: Import, Formats and examples, Export, Import history and Redirects (since 6.0.15), each shown only to
   a user with its policy (content/urltranslator_import for importing and the history, content/urltranslator for the
   rest). Included by content/urlalias_global.tpl, content/urlalias_wildcard.tpl and the URL map pages.

   Parameters: current (translator, wildcards, import, examples, history, export, redirects), all (true: also the URL
   translator and the wildcards, for the URL map pages). The same file is in design/admin and design/admin4. *}
{def $can_import = fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'urltranslator_import' ) )
     $can_translate = fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'urltranslator' ) )
     $links = array()}
{if and( is_set( $all ), $all, $can_translate )}
    {set $links = $links|append( hash( 'key', 'translator', 'url', 'content/urltranslator', 'text', 'Global URL aliases'|i18n( 'design/admin/content/urlimport' ) ),
                                 hash( 'key', 'wildcards', 'url', 'content/urlwildcards', 'text', 'Wildcards'|i18n( 'design/admin/content/urlimport' ) ) )}
{/if}
{if $can_import}{set $links = $links|append( hash( 'key', 'import', 'url', 'content/urlimport', 'text', 'Import'|i18n( 'design/admin/content/urlimport' ) ),
                                              hash( 'key', 'examples', 'url', 'content/urlimport/(source)/examples', 'text', 'Formats and examples'|i18n( 'design/admin/content/urlimport' ) ) )}{/if}
{if $can_translate}{set $links = $links|append( hash( 'key', 'export', 'url', 'content/urlexport', 'text', 'Export'|i18n( 'design/admin/content/urlimport' ) ) )}{/if}
{if $can_import}{set $links = $links|append( hash( 'key', 'history', 'url', 'content/urlimporthistory', 'text', 'Import history'|i18n( 'design/admin/content/urlimport' ) ) )}{/if}
{if $can_translate}{set $links = $links|append( hash( 'key', 'redirects', 'url', 'content/urlredirects', 'text', 'Redirects'|i18n( 'design/admin/content/urlimport' ) ) )}{/if}
{if $links|count}
<nav class="a4-toolbar" aria-label="{'URL maps'|i18n( 'design/admin/content/urlimport' )|wash}">
    <div class="a4-actions">
    {foreach $links as $link}
        {if eq( $link.key, $current )}<a class="a4-btn a4-btn-primary" href={$link.url|ezurl} aria-current="page">{$link.text|wash}</a>{else}<a class="a4-btn" href={$link.url|ezurl}>{$link.text|wash}</a>{/if}
    {/foreach}
    </div>
</nav>
{/if}
{undef $can_import $can_translate $links}
