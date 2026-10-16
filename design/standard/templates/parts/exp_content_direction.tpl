{* The lang and dir attributes of content written in a content language, printed inside a start tag:
   ' lang="he-IL" dir="rtl"' for a locale written right to left (its locale file says [LanguageSettings]
   TextDirection=rtl). For a left-to-right locale it prints nothing, unless exp_lang_always is true, then only
   ' lang="de-DE"': left-to-right content keeps the markup it always had where a lang attribute would be new.
   The language tag is the locale's [HTTP] ContentLanguage (content_language_code). Nothing for an unknown locale.

   Parameters:
     exp_locale_code   the locale code of the content, e.g. heb-IL (required; empty prints nothing)
     exp_lang_always   true: also the lang attribute of a left-to-right language (optional)

   Used by the edit forms (content/edit_attribute.tpl, content/edit.tpl) and the admin full view
   (node/view/admin_preview.tpl) of every design; a template that needs a wrapper only for right-to-left content
   catches the output with set-block and tests it for ''. Guide: doc/guides/content-languages.md.
*}{if and( is_set( $exp_locale_code ), $exp_locale_code|ne( '' ) )}{*
    *}{def $exp_dir_locale = $exp_locale_code|locale()}{*
    *}{if $exp_dir_locale.is_valid}{*
        *}{if $exp_dir_locale.is_right_to_left}{*
            *}{if $exp_dir_locale.content_language_code|ne( '' )} lang="{$exp_dir_locale.content_language_code|wash}"{/if} dir="rtl"{*
        *}{elseif and( is_set( $exp_lang_always ), $exp_lang_always, $exp_dir_locale.content_language_code|ne( '' ) )} lang="{$exp_dir_locale.content_language_code|wash}"{/if}{*
    *}{/if}{*
    *}{undef $exp_dir_locale}{*
*}{/if}