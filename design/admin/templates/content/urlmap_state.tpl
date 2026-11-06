{* The tag of a URL map row's state (expUrlMapPlan): new, changed, unchanged, duplicate, conflict, broken-target,
   unknown-host, not-importable. Parameter: state. Also the label alone with label_only=true(). The same file is in
   design/admin and design/admin4. *}
{def $label = ''
     $tone = ''}
{switch match=$state}
{case match='new'}{set $label = 'New'|i18n( 'design/admin/content/urlimport' )}{set $tone = ' is-ok'}{/case}
{case match='changed'}{set $label = 'Changed'|i18n( 'design/admin/content/urlimport' )}{set $tone = ' is-warn'}{/case}
{case match='unchanged'}{set $label = 'Unchanged'|i18n( 'design/admin/content/urlimport' )}{/case}
{case match='duplicate'}{set $label = 'Duplicate'|i18n( 'design/admin/content/urlimport' )}{/case}
{case match='conflict'}{set $label = 'Conflict'|i18n( 'design/admin/content/urlimport' )}{set $tone = ' is-bad'}{/case}
{case match='broken-target'}{set $label = 'Broken target'|i18n( 'design/admin/content/urlimport' )}{set $tone = ' is-bad'}{/case}
{case match='unknown-host'}{set $label = 'Unknown host'|i18n( 'design/admin/content/urlimport' )}{set $tone = ' is-warn'}{/case}
{case match='not-importable'}{set $label = 'Not importable'|i18n( 'design/admin/content/urlimport' )}{set $tone = ' is-bad'}{/case}
{case}{set $label = $state}{/case}
{/switch}
{if and( is_set( $label_only ), $label_only )}{$label|wash}{else}<span class="a4-tag{$tone}">{$label|wash}</span>{/if}
{undef $label $tone}
