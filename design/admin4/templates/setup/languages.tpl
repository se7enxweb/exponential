{* Setup > Interface languages (setup/languages; doc/eng-us/languages.md, "Translation fallbacks"). Variables:
   language_rows (locale, english, native, http, own, kind: own, fallback or untranslated, has_coverage, coverage, pct,
   from, chain: list of target, source, rule, rule_kind: configured, automatic or rule, translated; problem,
   problem_target, extensions: total, translated, complete, average), language_counts (own, fallback, untranslated),
   language_total, language_show ('' or a kind), language_detail (one row, with exists and extension_rows: name,
   coverage, pct; or null), language_problems (level, locale, code, detail), language_current (site.ini Locale),
   language_extensions (extension translation directories), language_settings (mode, automatic, exclude, max_depth,
   rules, configured).
   Read only. The same file is in design/admin and design/admin4. *}
<div class="context-block a4-page a4-setup-languages">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Interface languages'|i18n( 'design/admin/setup/languages' )}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'The administration is shown in the language of its locale. A locale without a translation of its own shows the first translation of its fallback chain, else the English source texts. This page lists each locale with what it shows and how complete that is. It changes nothing: the fallbacks are set in i18n.ini [TranslationSettings].'|i18n( 'design/admin/setup/languages' )}</p>

<h2 class="a4-sr-only" id="languages-overview-title">{'Overview'|i18n( 'design/admin/setup/languages' )}</h2>
<ul class="a4-figures" aria-labelledby="languages-overview-title">
    <li class="a4-figure"><strong>{$language_total}</strong><span>{'Locales'|i18n( 'design/admin/setup/languages' )}</span></li>
    <li class="a4-figure"><strong>{$language_counts.own}</strong><span>{'With a translation of their own'|i18n( 'design/admin/setup/languages' )}</span></li>
    <li class="a4-figure"><strong>{$language_counts.fallback}</strong><span>{'Translated through a fallback'|i18n( 'design/admin/setup/languages' )}</span></li>
    <li class="a4-figure"><strong>{$language_counts.untranslated}</strong><span>{'In the English source texts'|i18n( 'design/admin/setup/languages' )}</span></li>
    <li class="a4-figure{if $language_problems|count|gt( 0 )} is-attention{/if}"><strong>{$language_problems|count}</strong><span>{'Problems'|i18n( 'design/admin/setup/languages' )}</span></li>
</ul>

{if $language_problems|count|gt( 0 )}
<section class="a4-section" aria-labelledby="languages-problems-title">
<h2 class="a4-h2" id="languages-problems-title">{'Problems'|i18n( 'design/admin/setup/languages' )}</h2>
<ul class="a4-setup-languages-problems">
{foreach $language_problems as $language_problem}
    <li class="a4-notice {if $language_problem.level|eq( 'error' )}is-bad{else}is-warn{/if}">
        {if $language_problem.locale|ne( '' )}<code class="a4-code">{$language_problem.locale|wash}</code> {/if}
        {switch match=$language_problem.code}
        {case match='cycle'}{'Its chain comes back to %target.'|i18n( 'design/admin/setup/languages',, hash( '%target', $language_problem.detail ) )|wash}{/case}
        {case match='depth'}{'Its chain is longer than FallbackMaxDepth and is cut at %target.'|i18n( 'design/admin/setup/languages',, hash( '%target', $language_problem.detail ) )|wash}{/case}
        {case match='invalid-target'}{'Falls back to %target, which is no locale code.'|i18n( 'design/admin/setup/languages',, hash( '%target', $language_problem.detail ) )|wash}{/case}
        {case match='missing-target'}{'Falls back to %target, which has neither a locale file nor a translation.'|i18n( 'design/admin/setup/languages',, hash( '%target', $language_problem.detail ) )|wash}{/case}
        {case match='invalid-rule'}{'FallbackRules[] names %target, which is no fallback rule.'|i18n( 'design/admin/setup/languages',, hash( '%target', $language_problem.detail ) )|wash}{/case}
        {case match='unknown-locale'}{'Has a fallback, but no locale file.'|i18n( 'design/admin/setup/languages' )}{/case}
        {case match='no-translation'}{'Falls back to %target, which has no translation yet: it shows the English source texts.'|i18n( 'design/admin/setup/languages',, hash( '%target', $language_problem.detail ) )|wash}{/case}
        {case}{$language_problem.code|wash} {$language_problem.detail|wash}{/case}
        {/switch}
    </li>
{/foreach}
</ul>
</section>
{/if}

{if $language_detail}
{* ---- one locale, step by step ---- *}
<section class="a4-card a4-setup-languages-detail" aria-labelledby="languages-detail-title">
    <div class="a4-card-head">
        <h2 class="a4-card-title" id="languages-detail-title"><code class="a4-code">{$language_detail.locale|wash}</code> {if $language_detail.english|ne( '' )}{$language_detail.english|wash}{/if}</h2>
        <a class="a4-btn a4-btn-small" href={'/setup/languages'|ezurl}>{'All locales'|i18n( 'design/admin/setup/languages' )}</a>
    </div>
    {if $language_detail.exists|not}
    <p class="a4-notice is-warn">{'The installation has no locale file for %locale.'|i18n( 'design/admin/setup/languages',, hash( '%locale', $language_detail.locale ) )|wash}</p>
    {/if}
    <dl class="a4-facts">
        <div><dt>{'Translation of its own'|i18n( 'design/admin/setup/languages' )}</dt>
            <dd>{if $language_detail.own}{'Yes'|i18n( 'design/admin/setup/languages' )}{else}{'No'|i18n( 'design/admin/setup/languages' )}{/if}</dd></div>
        <div><dt>{'Interface'|i18n( 'design/admin/setup/languages' )}</dt>
            <dd>{if $language_detail.has_coverage}{'%percent % translated'|i18n( 'design/admin/setup/languages',, hash( '%percent', $language_detail.coverage ) )|wash}{if $language_detail.from|ne( '' )} {'through %locale'|i18n( 'design/admin/setup/languages',, hash( '%locale', $language_detail.from ) )|wash}{/if}{else}{'English source texts'|i18n( 'design/admin/setup/languages' )}{/if}</dd></div>
        <div><dt>{'Extensions'|i18n( 'design/admin/setup/languages' )}</dt>
            <dd>{'%translated of %total translated, %complete complete'|i18n( 'design/admin/setup/languages',, hash( '%translated', $language_detail.extensions.translated, '%total', $language_detail.extensions.total, '%complete', $language_detail.extensions.complete ) )|wash}</dd></div>
    </dl>
    <h3 class="a4-h3">{'Fallback chain'|i18n( 'design/admin/setup/languages' )}</h3>
    {if $language_detail.chain|count|gt( 0 )}
    <ol class="a4-setup-languages-steps">
    {foreach $language_detail.chain as $language_step}
        <li>{if $language_step.source}{'English source texts'|i18n( 'design/admin/setup/languages' )}{else}<a class="a4-code" href={concat( '/setup/languages/(locale)/', $language_step.target )|ezurl}>{$language_step.target|wash}</a>{if $language_step.translated|not} <span class="a4-badge is-warn">{'no translation'|i18n( 'design/admin/setup/languages' )}</span>{/if}{/if}
            <span class="a4-meta">{switch match=$language_step.rule_kind}{case match='configured'}{'by FallbackLanguages[]'|i18n( 'design/admin/setup/languages' )}{/case}{case match='automatic'}{'by the automatic same-language fallback'|i18n( 'design/admin/setup/languages' )}{/case}{case}{'by the rule %rule'|i18n( 'design/admin/setup/languages',, hash( '%rule', $language_step.rule ) )|wash}{/case}{/switch}</span></li>
    {/foreach}
    </ol>
    {else}
    <p class="a4-meta">{'None: without a translation of its own it shows the English source texts.'|i18n( 'design/admin/setup/languages' )}</p>
    {/if}
    {if $language_detail.extension_rows|count|gt( 0 )}
    <h3 class="a4-h3" id="languages-detail-extensions">{'Extensions'|i18n( 'design/admin/setup/languages' )}</h3>
    <div class="a4-table-wrap" role="region" aria-labelledby="languages-detail-extensions" tabindex="0">
    <table class="a4-table a4-setup-languages-table">
    <thead><tr><th scope="col">{'Extension'|i18n( 'design/admin/setup/languages' )}</th><th scope="col">{'Translated'|i18n( 'design/admin/setup/languages' )}</th></tr></thead>
    <tbody>
    {foreach $language_detail.extension_rows as $language_extension}
    <tr><th scope="row"><code class="a4-code">{$language_extension.name|wash}</code></th>
        <td>{if $language_extension.coverage|is_numeric}<span class="a4-meter a4-setup-languages-meter{if $language_extension.pct|ge( 100 )} is-ok{/if}" aria-hidden="true"><span class="a4-pct-{$language_extension.pct}"></span></span> {$language_extension.pct} %{else}<span class="a4-muted">{'English source texts'|i18n( 'design/admin/setup/languages' )}</span>{/if}</td></tr>
    {/foreach}
    </tbody>
    </table>
    </div>
    {/if}
</section>
{/if}

{* ---- the settings in effect ---- *}
<details class="a4-fold" id="languages-settings">
    <summary><h2 class="a4-h2">{'Settings'|i18n( 'design/admin/setup/languages' )}</h2> <span class="a4-meta">i18n.ini [TranslationSettings]</span></summary>
    <div class="a4-fold-body">
    <dl class="a4-facts">
        <div><dt>FallbackLanguages[]</dt><dd>{'%count locales with a configured fallback'|i18n( 'design/admin/setup/languages',, hash( '%count', $language_settings.configured ) )|wash}</dd></div>
        <div><dt>FallbackMode</dt><dd>{if $language_settings.mode|eq( 'merge' )}{'merge: every translation of the chain, the most specific winning'|i18n( 'design/admin/setup/languages' )}{else}{'first: the first translation of the chain'|i18n( 'design/admin/setup/languages' )}{/if}</dd></div>
        <div><dt>AutomaticLanguageFallback</dt><dd>{if $language_settings.automatic}{'enabled: a locale without a translation and without a configured fallback takes the most complete translation of its language'|i18n( 'design/admin/setup/languages' )}{if $language_settings.exclude|count|gt( 0 )} <span class="a4-meta">{'Never for: %list'|i18n( 'design/admin/setup/languages',, hash( '%list', $language_settings.exclude|implode( ', ' ) ) )|wash}</span>{/if}{else}{'disabled'|i18n( 'design/admin/setup/languages' )}{/if}</dd></div>
        <div><dt>FallbackMaxDepth</dt><dd>{$language_settings.max_depth}</dd></div>
        <div><dt>FallbackRules[]</dt><dd>{if $language_settings.rules|count|gt( 0 )}<ul class="a4-badges">{foreach $language_settings.rules as $language_rule}<li class="a4-tag is-mono">{$language_rule|wash}</li>{/foreach}</ul>{else}{'None'|i18n( 'design/admin/setup/languages' )}{/if}</dd></div>
    </dl>
    <p class="a4-meta">{'The same list on the command line: %command'|i18n( 'design/admin/setup/languages',, hash( '%command', './console exp:i18n fallbacks' ) )|wash}</p>
    </div>
</details>

{* ---- every locale ---- *}
<section class="a4-section" aria-labelledby="languages-list-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="languages-list-title">{'Locales'|i18n( 'design/admin/setup/languages' )} <span class="a4-meta">{'%count shown'|i18n( 'design/admin/setup/languages',, hash( '%count', $language_rows|count ) )|wash}</span></h2>
</div>

<form method="get" action={'/setup/languages'|ezurl} class="a4-filterbar">
    <label for="languages-show">{'Show'|i18n( 'design/admin/setup/languages' )}</label>
    <select id="languages-show" name="Show" class="a4-input">
        <option value=""{if $language_show|eq( '' )} selected="selected"{/if}>{'All locales'|i18n( 'design/admin/setup/languages' )}</option>
        <option value="own"{if $language_show|eq( 'own' )} selected="selected"{/if}>{'With a translation of their own'|i18n( 'design/admin/setup/languages' )}</option>
        <option value="fallback"{if $language_show|eq( 'fallback' )} selected="selected"{/if}>{'Translated through a fallback'|i18n( 'design/admin/setup/languages' )}</option>
        <option value="untranslated"{if $language_show|eq( 'untranslated' )} selected="selected"{/if}>{'In the English source texts'|i18n( 'design/admin/setup/languages' )}</option>
    </select>
    <button type="submit" class="a4-btn">{'Show'|i18n( 'design/admin/setup/languages' )}</button>
</form>

{if $language_rows|count|gt( 0 )}
<div class="a4-table-wrap" role="region" aria-labelledby="languages-list-title" tabindex="0">
<table class="a4-table a4-setup-languages-table">
<thead>
<tr>
    <th scope="col">{'Locale'|i18n( 'design/admin/setup/languages' )}</th>
    <th scope="col">{'Language'|i18n( 'design/admin/setup/languages' )}</th>
    <th scope="col">{'Interface'|i18n( 'design/admin/setup/languages' )}</th>
    <th scope="col">{'Fallback chain'|i18n( 'design/admin/setup/languages' )}</th>
    {if $language_extensions|gt( 0 )}<th scope="col">{'Extensions'|i18n( 'design/admin/setup/languages' )}</th>{/if}
</tr>
</thead>
<tbody>
{foreach $language_rows as $language_row}
<tr{if $language_row.locale|eq( $language_current )} class="is-current"{/if}>
    <th scope="row"><a class="a4-code" href={concat( '/setup/languages/(locale)/', $language_row.locale )|ezurl}>{$language_row.locale|wash}</a>{if $language_row.locale|eq( $language_current )} <span class="a4-badge is-info">{'This siteaccess'|i18n( 'design/admin/setup/languages' )}</span>{/if}</th>
    <td>{if $language_row.native|ne( '' )}<span{if $language_row.http|ne( '' )} lang="{$language_row.http|wash}"{/if}>{$language_row.native|wash}</span>{/if}{if and( $language_row.english|ne( '' ), $language_row.english|ne( $language_row.native ) )} <span class="a4-meta">{$language_row.english|wash}</span>{/if}</td>
    <td>{if $language_row.has_coverage}<span class="a4-meter a4-setup-languages-meter{if $language_row.pct|ge( 100 )} is-ok{/if}" aria-hidden="true"><span class="a4-pct-{$language_row.pct}"></span></span> {$language_row.pct} %{if $language_row.from|ne( '' )} <span class="a4-meta">{'through %locale'|i18n( 'design/admin/setup/languages',, hash( '%locale', $language_row.from ) )|wash}</span>{/if}{else}<span class="a4-muted">{'English source texts'|i18n( 'design/admin/setup/languages' )}</span>{/if}</td>
    <td>{if $language_row.chain|count|gt( 0 )}<ol class="a4-setup-languages-chain">{foreach $language_row.chain as $language_step}<li>{if $language_step.source}<span class="a4-muted">{'source texts'|i18n( 'design/admin/setup/languages' )}</span>{else}<span class="a4-tag is-mono{if $language_step.rule_kind|eq( 'automatic' )} is-info{/if}">{$language_step.target|wash}</span>{/if}</li>{/foreach}</ol>{if $language_row.problem|ne( '' )} <span class="a4-badge is-bad">{'problem'|i18n( 'design/admin/setup/languages' )}</span>{/if}{else}<span class="a4-muted">-</span>{/if}</td>
    {if $language_extensions|gt( 0 )}<td>{'%translated of %total'|i18n( 'design/admin/setup/languages',, hash( '%translated', $language_row.extensions.translated, '%total', $language_row.extensions.total ) )|wash}{if $language_row.extensions.average|is_numeric} <span class="a4-meta">{'%percent % on average'|i18n( 'design/admin/setup/languages',, hash( '%percent', $language_row.extensions.average ) )|wash}</span>{/if}</td>{/if}
</tr>
{/foreach}
</tbody>
</table>
</div>
<p class="a4-meta">{'A highlighted step was found by the automatic same-language fallback; the others are configured or come from a rule.'|i18n( 'design/admin/setup/languages' )}</p>
{else}
<p class="a4-empty">{'No locale is of this kind.'|i18n( 'design/admin/setup/languages' )}</p>
{/if}
</section>

</div></div></div>

</div>
