{* The reader's language of the help, with the switch to its other languages (doc/eng-us/help/reading-help-in-other-languages.md).

   "Reading in: English / Deutsch": one button per language of [DocLanguages] Directories[] that has pages and that the
   interface language switch can choose (expHelp::reading()). Each button posts to uiswitch/set, the same view as the
   language switch of the top bar (ezformtoken adds its token): it changes the interface language of the whole
   administration and comes back to this page (RedirectURI). The reader's language carries aria-current="true",
   each language's name its own lang. Without the switch (no uiswitch/language policy, one language) it shows the
   reader's language only. Below it, how much of the help each translation has ("German: 120 of 7,265 pages").
   Styles: stylesheets/modules/help.css (.a4-help-reading-*).

   Parameters: reading (expHelp::reading()), prefix (id prefix, unique on the page). *}
{if and( is_set( $reading ), $reading )}
{def $rl_ctx = 'kernel/exphelp'
     $rl_prefix = cond( is_set( $prefix ), $prefix, 'exphelp-reading' )
     $rl_current = false()}
{foreach $reading.choices as $rl_choice}{if $rl_choice.current}{set $rl_current = $rl_choice}{/if}{/foreach}
<div class="a4-help-reading" role="group" aria-labelledby="{$rl_prefix}-label">
    <span class="a4-help-reading-label" id="{$rl_prefix}-label"><span class="a4-icon a4-bi-translate" aria-hidden="true"></span>{'Reading in:'|i18n( $rl_ctx )}</span>
    {if $reading.can_switch}
    <form class="a4-help-reading-form" method="post" action={'/uiswitch/set'|ezurl} aria-describedby="{$rl_prefix}-note">
        <input type="hidden" name="Provider" value="{$reading.provider|wash}" />
        <input type="hidden" name="RedirectURI" value="{$reading.back|wash}" />
        <ul class="a4-help-reading-list">
            {foreach $reading.choices as $rl_choice}
            {if $rl_choice.locale|ne( '' )}
            <li><button type="submit" class="a4-help-reading-option" name="Value" value="{$rl_choice.locale|wash}" lang="{$rl_choice.lang|wash}"{if $rl_choice.current} aria-current="true"{/if}>{$rl_choice.label|wash}</button></li>
            {/if}
            {/foreach}
        </ul>
        <span class="a4-sr-only" id="{$rl_prefix}-note">{'Choosing a language changes the language of the whole administration.'|i18n( $rl_ctx )}</span>
    </form>
    {elseif $rl_current}
    <strong class="a4-help-reading-current" lang="{$rl_current.lang|wash}">{$rl_current.label|wash}</strong>
    {/if}
    {if $reading.coverage|count}
    <ul class="a4-help-reading-coverage">
        {foreach $reading.coverage as $rl_row}
        <li>{'%language: %count of %total pages'|i18n( $rl_ctx, '', hash( '%language', $rl_row.name, '%count', $rl_row.pages_text, '%total', $rl_row.total_text ) )|wash}</li>
        {/foreach}
    </ul>
    {/if}
    <a class="a4-help-reading-about" href={'/exphelp/viewer/help/reading-help-in-other-languages.md'|ezurl}>{'About the help in other languages'|i18n( $rl_ctx )}</a>
</div>
{undef $rl_ctx $rl_prefix $rl_current}
{/if}
