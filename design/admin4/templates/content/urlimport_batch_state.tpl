{* The tag of an import's state (expUrlMapBatch): applied, undone, partly-undone. Parameter: state. The same file is
   in design/admin and design/admin4. *}
{switch match=$state}
{case match='applied'}<span class="a4-tag is-ok">{'Applied'|i18n( 'design/admin/content/urlimport' )}</span>{/case}
{case match='undone'}<span class="a4-tag">{'Undone'|i18n( 'design/admin/content/urlimport' )}</span>{/case}
{case match='partly-undone'}<span class="a4-tag is-warn">{'Partly undone'|i18n( 'design/admin/content/urlimport' )}</span>{/case}
{case}<span class="a4-tag">{$state|wash}</span>{/case}
{/switch}
