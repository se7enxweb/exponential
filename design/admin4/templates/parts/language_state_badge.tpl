{* A language badge (design:parts/language_badge.tpl) that also shows the language's state for one object, by its
   shape and not by a color of its own (components.css .a4-lang):
     main      filled: the object's main language
     present   outlined: a translation that exists
     missing   dashed and pale: a language of the site the object does not have; a link "Translate into ..."
               (content/edit/<object>/f/<language>, the way the Edit in menu takes) when the user may create it
     outdated  a present one with a small mark: last edited before the main language (expContentLanguageStates)
   The code stays the text; the state is in the title and in the accessible name ("German – main language").

   Parameters:
     state_entry       one entry of fetch( 'content', 'language_states', hash( 'object', ... ) ) (required):
                       locale, name, state, outdated, can_translate
     state_main_name   the name of the main language, for the outdated title (required when outdated)
     state_object_id   the object, for the translate link of a missing language (optional: no link without it)
     state_href        the link of a present or main badge, e.g. the view in that language (optional)
     state_current     true: the language shown (.is-current, aria-current) (optional)

   The parameters carry the prefix state_ because an include sees the variables of the template that includes it.
*}{def $a4_ls_code = ''
     $a4_ls_label = ''
     $a4_ls_href = ''
     $a4_ls_current = and( is_set( $state_current ), $state_current )}{*
*}{set-block variable=a4_ls_code}{include uri='design:parts/language_badge.tpl' locale=$state_entry.locale badge_plain=true()}{/set-block}{*
*}{switch match=$state_entry.state}{*
*}{case match='main'}{set $a4_ls_label = '%language – main language'|i18n( 'design/admin/node/view/full',, hash( '%language', $state_entry.name ) )}{/case}{*
*}{case match='missing'}{*
    *}{if and( $state_entry.can_translate, is_set( $state_object_id ) )}{*
        *}{set $a4_ls_label = 'Translate into %language'|i18n( 'design/admin/node/view/full',, hash( '%language', $state_entry.name ) )
               $a4_ls_href = concat( 'content/edit/', $state_object_id, '/f/', $state_entry.locale )|ezurl( 'no' )}{*
    *}{else}{set $a4_ls_label = '%language – missing'|i18n( 'design/admin/node/view/full',, hash( '%language', $state_entry.name ) )}{/if}{*
*}{/case}{*
*}{case}{*
    *}{if $state_entry.outdated}{set $a4_ls_label = '%language – outdated, older than %main'|i18n( 'design/admin/node/view/full',, hash( '%language', $state_entry.name, '%main', $state_main_name ) )}{*
    *}{else}{set $a4_ls_label = '%language – translation exists'|i18n( 'design/admin/node/view/full',, hash( '%language', $state_entry.name ) )}{/if}{*
*}{/case}{*
*}{/switch}{*
*}{if and( ne( $state_entry.state, 'missing' ), is_set( $state_href ) )}{set $a4_ls_href = $state_href}{/if}{*
*}{if $a4_ls_href|ne( '' )}<a class="a4-lang is-{$state_entry.state|wash}{if $state_entry.outdated} is-outdated{/if}{if $a4_ls_current} is-current{/if}" href="{$a4_ls_href|wash}" title="{$a4_ls_label|wash}"{if $a4_ls_current} aria-current="true"{/if} translate="no">{$a4_ls_code|wash}<span class="a4-sr-only"> {$a4_ls_label|wash}</span></a>{*
*}{else}<span class="a4-lang is-{$state_entry.state|wash}{if $state_entry.outdated} is-outdated{/if}{if $a4_ls_current} is-current{/if}" title="{$a4_ls_label|wash}" role="img" aria-label="{$a4_ls_label|wash}" translate="no">{$a4_ls_code|wash}</span>{/if}{*
*}{undef $a4_ls_code $a4_ls_label $a4_ls_href $a4_ls_current}