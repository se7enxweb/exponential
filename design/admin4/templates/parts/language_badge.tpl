{* A content language as a short code badge (DE, EN, FR) where admin4 showed a flag: a flag stands for a country,
   not a language. The code is the language part of the locale's language tag (ger-DE: de-DE, so DE); when the
   installation has two content languages of the same language (eng-GB and eng-US), both show the region as well
   (EN-GB, EN-US). A locale without a language tag shows its locale code.

   Parameters:
     locale        the locale code, e.g. ger-DE (required)
     badge_name    the language's name (optional, default: the locale's international name, e.g. German)
     badge_named   true when the name is written next to the badge: the badge is then hidden from screen readers;
                   otherwise it carries the name as its accessible name (role="img", aria-label)
     badge_plain   true: only the code as text, for scripts and attribute values
     badge_class   further classes, e.g. is-current

   The optional parameters carry the prefix badge_ because an include sees the variables of the template that
   includes it: an unset optional parameter called class would read the caller's $class, which is the content class
   in class/view.tpl and class/edit.tpl, and writing that object as text is a fatal error.
*}{def $a4_lang_object = $locale|locale()
     $a4_lang_tag = ''
     $a4_lang_badge = $locale|upcase
     $a4_lang_label = $locale}{*
*}{if $a4_lang_object.is_valid}{set $a4_lang_tag = $a4_lang_object.content_language_code
                                    $a4_lang_label = $a4_lang_object.intl_language_name}{/if}{*
*}{if and( is_set( $badge_name ), $badge_name|ne( '' ) )}{set $a4_lang_label = $badge_name}{/if}{*
*}{if $a4_lang_tag|ne( '' )}{*
    *}{def $a4_lang_primary = $a4_lang_tag|downcase|explode( '-' )
           $a4_lang_same = 0}{*
    *}{set $a4_lang_primary = $a4_lang_primary[0]}{*
    *}{foreach fetch( 'content', 'translation_list' ) as $a4_lang_other}{*
        *}{if and( $a4_lang_other.is_valid, $a4_lang_other.content_language_code|downcase|begins_with( concat( $a4_lang_primary, '-' ) ) )}{set $a4_lang_same = $a4_lang_same|inc}{/if}{*
    *}{/foreach}{*
    *}{if $a4_lang_same|gt( 1 )}{set $a4_lang_badge = $a4_lang_tag|upcase}{else}{set $a4_lang_badge = $a4_lang_primary|upcase}{/if}{*
    *}{undef $a4_lang_primary $a4_lang_same}{*
*}{/if}{*
*}{if and( is_set( $badge_plain ), $badge_plain )}{$a4_lang_badge|wash}{else}<span class="a4-lang{if is_set( $badge_class )} {$badge_class|wash}{/if}" title="{$a4_lang_label|wash}" translate="no"{if and( is_set( $badge_named ), $badge_named )} aria-hidden="true"{else} role="img" aria-label="{$a4_lang_label|wash}"{/if}>{$a4_lang_badge|wash}</span>{/if}{*
*}{undef $a4_lang_object $a4_lang_tag $a4_lang_badge $a4_lang_label}