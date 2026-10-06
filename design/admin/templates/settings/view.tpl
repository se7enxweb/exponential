{* The INI settings of one file in one siteaccess (settings/view/<siteaccess>/<file>).

   A file and a siteaccess are picked with the form at the top (selectedINIFile, CurrentSiteAccess, ChangeINIFile).
   Then: what the file is made of (figures, the files read in their order), a search within the file or across every
   file, "changed from the default", a comparison with another siteaccess, and the settings block by block. Every
   setting shows its value in effect, the file it comes from, every file that sets it in load order (the override
   chain) and, for an array, where each element comes from. Secrets are masked (expSettingsSecretRule). Ticked
   settings are removed with RemoveButton (RemoveSettingsArray[] "Block:Setting"); the notice after a write says
   which caches were cleared and whether Velocity needs a restart.

   The same file is in design/admin and design/admin4. The old variables (settings, block_count, setting_count,
   ini_file, ini_files, siteaccess_list, current_siteaccess) are still set. Works without javascript.
   Guide: doc/guides/settings-page.md *}
{include uri='design:settings/exp_style.tpl'}

{def $has_file = and( $ini_file, is_set( $page ), $page )
     $base_url = concat( 'settings/view/', $current_siteaccess, '/', $ini_file )
     $filtered = or( $query|ne( '' ), $changed_only )}

<div class="context-block exp-settings">

<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
<h1 class="context-title">{if $ini_file}{'%file in %siteaccess'|i18n( 'design/admin/settings',, hash( '%file', $ini_file, '%siteaccess', $current_siteaccess ) )|wash}{else}{'Settings'|i18n( 'design/admin/settings' )}{/if}</h1>
{if $ini_file}<span class="exp-muted">{'INI settings'|i18n( 'design/admin/settings' )}</span>{/if}
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Every INI file is read in layers: settings/<file>.ini first, then the files of the active extensions and of the siteaccess, then settings/override. A later file wins: it replaces a plain value, and adds to an array unless it empties the array first. This page shows the value in effect, the file it comes from and every file that sets it. Passwords, keys, tokens and other secrets are never shown.'|i18n( 'design/admin/settings' )|wash}</p>

{* What the last change did *}
{if $notice}
    {if eq( $notice.action, 'unchanged' )}
<div class="exp-feedback is-info" role="status"><p>{'Nothing was changed.'|i18n( 'design/admin/settings' )}</p></div>
    {else}
<div class="exp-feedback {if $notice.cache_ok}is-ok{else}is-warn{/if}" role="status">
    <p><strong>{if eq( $notice.action, 'removed' )}{'Removed:'|i18n( 'design/admin/settings' )}{else}{'Saved:'|i18n( 'design/admin/settings' )}{/if}</strong>
    {foreach $notice.settings as $s}<code>[{$s.block|wash}] {$s.name|wash}</code>{if $s.path} {'in'|i18n( 'design/admin/settings' )} <code>{$s.path|wash}</code>{/if}{delimiter}, {/delimiter}{/foreach}</p>
    {if $notice.cache_ok}
    <p>{'Cleared so that both servers read the change on their next request:'|i18n( 'design/admin/settings' )} {$notice.caches|implode( ', ' )|wash}.</p>
    {else}
    <p>{'The INI cache could not be cleared. Until it is, neither PHP-FPM nor Velocity uses the change: run php bin/php/ezcache.php --clear-tag=ini.'|i18n( 'design/admin/settings' )|wash}</p>
    {/if}
    {if $notice.restart}
    <p><strong>{'Velocity reads this setting only when it starts.'|i18n( 'design/admin/settings' )}</strong> {if $notice.velocity_running}{'Velocity is running: restart it with ./console exp:velocity restart for the change to reach it.'|i18n( 'design/admin/settings' )|wash}{else}{'Velocity is not running here, so nothing more is needed.'|i18n( 'design/admin/settings' )}{/if}</p>
    {/if}
</div>
    {/if}
{/if}

{if $unknown_file}
<div class="exp-feedback is-warn" role="alert"><p>{'There is no INI file of that name. Pick one from the list.'|i18n( 'design/admin/settings' )}</p></div>
{/if}

{* Which file, in which siteaccess *}
<form method="post" action={'settings/view'|ezurl}>
<div class="exp-toolbar is-soft">
    <div class="exp-field">
        <label for="settings-file">{'INI file'|i18n( 'design/admin/settings' )}</label>
        <select name="selectedINIFile" id="settings-file">
        {if $ini_files_common}
            <optgroup label="{'Most used'|i18n( 'design/admin/settings' )}">
            {foreach $ini_files_common as $common_ini}<option value="{$common_ini|wash}"{if eq( $common_ini, $ini_file )} selected="selected"{/if}>{$common_ini|wash}</option>{/foreach}
            </optgroup>
            <optgroup label="{'All ini files'|i18n( 'design/admin/settings' )}">
            {foreach $ini_files_other as $other_ini}<option value="{$other_ini|wash}"{if eq( $other_ini, $ini_file )} selected="selected"{/if}>{$other_ini|wash}</option>{/foreach}
            </optgroup>
        {else}
            {foreach $ini_files as $file}<option value="{$file|wash}"{if eq( $file, $ini_file )} selected="selected"{/if}>{$file|wash}</option>{/foreach}
        {/if}
        </select>
    </div>
    <div class="exp-field">
        <label for="settings-siteaccess">{'Siteaccess'|i18n( 'design/admin/settings' )}</label>
        <select name="CurrentSiteAccess" id="settings-siteaccess">
        {foreach $siteaccess_list as $sa}<option value="{$sa|wash}"{if eq( $sa, $current_siteaccess )} selected="selected"{/if}>{$sa|wash}</option>{/foreach}
        </select>
    </div>
    <div class="exp-field">
        <div class="exp-actions"><button type="submit" class="exp-btn exp-btn-primary" name="ChangeINIFile" value="1">{'Show settings'|i18n( 'design/admin/settings' )}</button></div>
    </div>
</div>
</form>

{if $ini_file|not}
<p class="exp-empty">{'Pick an INI file and a siteaccess to see its settings.'|i18n( 'design/admin/settings' )}</p>
{/if}

{if $has_file}

{* The file as a whole *}
<section class="exp-section" aria-labelledby="settings-summary-title">
<h2 class="exp-sr" id="settings-summary-title">{'Summary'|i18n( 'design/admin/settings' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$summary.blocks}</strong><span>{'Blocks'|i18n( 'design/admin/settings' )}</span></li>
    <li class="exp-figure"><strong>{$summary.settings}</strong><span>{'Settings'|i18n( 'design/admin/settings' )}</span></li>
    <li class="exp-figure"><strong>{$summary.arrays}</strong><span>{'Arrays'|i18n( 'design/admin/settings' )}</span></li>
    <li class="exp-figure"><a href={concat( $base_url, '?changed=1' )|ezurl}><strong>{$summary.changed}</strong><span>{'Changed from the default'|i18n( 'design/admin/settings' )}</span></a></li>
    <li class="exp-figure"><strong>{$summary.added}</strong><span>{'Without a default'|i18n( 'design/admin/settings' )}</span></li>
    <li class="exp-figure"><strong>{$summary.secrets}</strong><span>{'Secrets, masked'|i18n( 'design/admin/settings' )}</span></li>
    <li class="exp-figure"><strong>{$summary.files}</strong><span>{'Files read'|i18n( 'design/admin/settings' )}</span></li>
    {if $runtime_known}
    <li class="exp-figure{if $summary.pending|gt( 0 )} is-attention{/if}"><strong>{$summary.pending}</strong><span>{'Not in effect yet'|i18n( 'design/admin/settings' )}</span></li>
    {/if}
</ul>

{if $runtime_known}
    {if $summary.pending|gt( 0 )}
<div class="exp-feedback is-warn" role="status"><p>{'%count settings in the files differ from what this server is running with. The INI cache still holds the old values: clear it (Setup > Cache management, INI caches, or php bin/php/ezcache.php --clear-tag=ini). They are marked "Not in effect yet" below.'|i18n( 'design/admin/settings',, hash( '%count', $summary.pending ) )|wash}</p></div>
    {/if}
<p class="exp-help">{if $served_by_velocity}{'This page is served by Velocity. "In effect" compares the files with the settings this Velocity worker runs with; PHP-FPM can differ until its INI cache is read again.'|i18n( 'design/admin/settings' )|wash}{else}{'This page is served by PHP-FPM. "In effect" compares the files with the settings PHP-FPM runs with; open the page on the Velocity port to check Velocity.'|i18n( 'design/admin/settings' )|wash}{/if}</p>
{else}
<p class="exp-help">{'Whether the values are in effect can only be checked for %siteaccess, the siteaccess this page runs in.'|i18n( 'design/admin/settings',, hash( '%siteaccess', $this_siteaccess ) )|wash}</p>
{/if}
</section>

{if $summary.unreadable}
<div class="exp-feedback is-bad" role="alert"><p>{'%count files of the load order could not be read by this server, so their settings are missing here and in what it runs with. Check their owner and permissions:'|i18n( 'design/admin/settings',, hash( '%count', $summary.unreadable|count ) )|wash}</p><ul>{foreach $summary.unreadable as $path}<li><code>{$path|wash}</code></li>{/foreach}</ul></div>
{/if}
<details class="exp-fold">
<summary>{'The %count files read for %file, in order'|i18n( 'design/admin/settings',, hash( '%count', $summary.files, '%file', $ini_file ) )|wash}</summary>
<div class="exp-fold-body">
<p class="exp-help">{'Later files win. The number is how many settings each file sets.'|i18n( 'design/admin/settings' )}</p>
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr><th scope="col">#</th><th scope="col">{'File'|i18n( 'design/admin/settings' )}</th><th scope="col" class="exp-num">{'Settings'|i18n( 'design/admin/settings' )}</th></tr></thead>
<tbody>
{foreach $summary.layers as $i => $layer}
<tr><td class="exp-num">{$i|inc}</td><td>{include uri='design:settings/exp_origin.tpl' origin=$layer.placement show_path=true()}</td><td class="exp-num">{if $layer.unreadable}<span class="exp-badge is-bad">{'not readable'|i18n( 'design/admin/settings' )}</span>{else}{$layer.used}{/if}</td></tr>
{/foreach}
</tbody>
</table>
</div>
</div>
</details>

{* Search, filter, compare *}
<form method="get" action={$base_url|ezurl} role="search">
<div class="exp-toolbar">
    <div class="exp-field">
        <label for="settings-q">{'Search'|i18n( 'design/admin/settings' )}</label>
        <input type="search" id="settings-q" name="q" value="{$query|wash}" maxlength="100" aria-describedby="settings-q-help" spellcheck="false" />
        <span class="exp-help" id="settings-q-help">{'Block, setting name or value. The values of secrets are never searched.'|i18n( 'design/admin/settings' )}</span>
    </div>
    <fieldset class="exp-field">
        <legend>{'Search in'|i18n( 'design/admin/settings' )}</legend>
        <div class="exp-chips">
            <label class="exp-chip"><input type="radio" name="scope" value="file"{if $search_all|not} checked="checked"{/if} /><span>{'This file'|i18n( 'design/admin/settings' )}</span></label>
            <label class="exp-chip"><input type="radio" name="scope" value="all"{if $search_all} checked="checked"{/if} /><span>{'Every file'|i18n( 'design/admin/settings' )}</span></label>
        </div>
    </fieldset>
    <div class="exp-field">
        <label for="settings-compare">{'Compare with siteaccess'|i18n( 'design/admin/settings' )}</label>
        <select name="compare" id="settings-compare">
            <option value="">{'No comparison'|i18n( 'design/admin/settings' )}</option>
            {foreach $siteaccess_list as $sa}{if ne( $sa, $current_siteaccess )}<option value="{$sa|wash}"{if eq( $sa, $compare_with )} selected="selected"{/if}>{$sa|wash}</option>{/if}{/foreach}
        </select>
    </div>
    <div class="exp-field">
        <label class="exp-check"><input type="checkbox" name="changed" value="1"{if $changed_only} checked="checked"{/if} />{'Only settings changed from the default'|i18n( 'design/admin/settings' )}</label>
        <div class="exp-actions">
            <button type="submit" class="exp-btn exp-btn-primary">{'Show'|i18n( 'design/admin/settings' )}</button>
            {if or( $filtered, $compare_with, $search_all )}<a class="exp-btn" href={$base_url|ezurl}>{'Show all'|i18n( 'design/admin/settings' )}</a>{/if}
        </div>
    </div>
</div>
</form>

{* A search across every file *}
{if $search_all}
<section class="exp-section" aria-labelledby="settings-hits-title">
<div class="exp-section-head"><h2 class="exp-h2" id="settings-hits-title">{'%count settings in every file match "%query"'|i18n( 'design/admin/settings',, hash( '%count', $search_hits|count, '%query', $query ) )|wash}</h2>
<p>{'In siteaccess %siteaccess. A file name leads to that setting.'|i18n( 'design/admin/settings',, hash( '%siteaccess', $current_siteaccess ) )|wash}</p></div>
{if $search_hits}
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr><th scope="col">{'Setting'|i18n( 'design/admin/settings' )}</th><th scope="col">{'Value in effect'|i18n( 'design/admin/settings' )}</th><th scope="col">{'From'|i18n( 'design/admin/settings' )}</th></tr></thead>
<tbody>
{foreach $search_hits as $hit}
<tr>
    <td class="exp-name"><a href={concat( 'settings/view/', $current_siteaccess, '/', $hit.file, '?q=', $hit.name, '#', $hit.anchor )|ezurl}><code>{$hit.file|wash}</code></a><br /><code>[{$hit.block|wash}] {$hit.name|wash}</code></td>
    <td class="exp-v">{if $hit.secret}<span class="exp-val is-secret">{$hit.text|wash}</span>{elseif eq( $hit.text, '' )}<span class="exp-val is-empty">{'empty'|i18n( 'design/admin/settings' )}</span>{else}<span class="exp-val">{$hit.text|shorten( 160 )|wash}</span>{/if}</td>
    <td>{include uri='design:settings/exp_origin.tpl' origin=$hit.origin}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{else}
<p class="exp-empty">{'No setting matches.'|i18n( 'design/admin/settings' )}</p>
{/if}
</section>

{* A comparison with another siteaccess *}
{elseif $compare_with}
<section class="exp-section" aria-labelledby="settings-compare-title">
<div class="exp-section-head"><h2 class="exp-h2" id="settings-compare-title">{'%count settings of %file differ between %a and %b'|i18n( 'design/admin/settings',, hash( '%count', $compare_rows|count, '%file', $ini_file, '%a', $current_siteaccess, '%b', $compare_with ) )|wash}</h2>
<p>{'Only the settings whose value in effect differs are listed. A secret only says that it differs.'|i18n( 'design/admin/settings' )}</p></div>
{if $compare_rows}
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr><th scope="col">{'Setting'|i18n( 'design/admin/settings' )}</th><th scope="col">{$current_siteaccess|wash}</th><th scope="col">{$compare_with|wash}</th></tr></thead>
<tbody>
{foreach $compare_rows as $c}
<tr>
    <td class="exp-name"><code>[{$c.block|wash}]</code><br /><code>{$c.name|wash}</code>{if $c.secret} <span class="exp-badge is-warn">{'secret'|i18n( 'design/admin/settings' )}</span>{/if}</td>
    {foreach array( hash( 'set', ne( $c.in, 'b' ), 'text', $c.a_text, 'origin', $c.a_origin ), hash( 'set', ne( $c.in, 'a' ), 'text', $c.b_text, 'origin', $c.b_origin ) ) as $side}
    <td class="exp-v">{if $side.set|not}<span class="exp-val is-empty">{'not set'|i18n( 'design/admin/settings' )}</span>{elseif $c.secret}<span class="exp-val is-secret">{$side.text|wash}</span>{elseif eq( $side.text, '' )}<span class="exp-val is-empty">{'empty'|i18n( 'design/admin/settings' )}</span>{else}<span class="exp-val">{$side.text|shorten( 200 )|wash}</span>{/if}
        {if $side.set}<br />{include uri='design:settings/exp_origin.tpl' origin=$side.origin}{/if}</td>
    {/foreach}
</tr>
{/foreach}
</tbody>
</table>
</div>
{else}
<p class="exp-empty">{'Both siteaccesses run with the same values.'|i18n( 'design/admin/settings' )}</p>
{/if}
</section>

{* The settings of the file *}
{else}
<section class="exp-section" aria-labelledby="settings-list-title">
<div class="exp-section-head">
<h2 class="exp-h2" id="settings-list-title">{if $filtered}{'%shown of %total settings'|i18n( 'design/admin/settings',, hash( '%shown', $page.shown, '%total', $page.total ) )|wash}{else}{'All %total settings'|i18n( 'design/admin/settings',, hash( '%total', $page.total ) )|wash}{/if}</h2>
{if $changed_only}<p>{'Only settings whose value in effect differs from settings/%file, or that it does not have.'|i18n( 'design/admin/settings',, hash( '%file', $ini_file ) )|wash}</p>{/if}
</div>

{if $page.blocks|count|gt( 1 )}
<nav aria-label="{'Blocks'|i18n( 'design/admin/settings' )}">
<ul class="exp-blocknav">
{foreach $page.blocks as $b}<li><a href="#{$b.anchor|wash}">{$b.name|wash} <span>{$b.settings|count}</span></a></li>{/foreach}
</ul>
</nav>
<br />
{/if}

{if $page.blocks|not}
<p class="exp-empty">{'No setting matches.'|i18n( 'design/admin/settings' )}</p>
{else}
<form method="post" action={$base_url|ezurl}>
<div class="exp-setlist">
{foreach $page.blocks as $b}
<section class="exp-block" id="{$b.anchor|wash}" aria-labelledby="{$b.anchor|wash}-h">
<div class="exp-block-head">
    <h3 id="{$b.anchor|wash}-h">[{$b.name|wash}]</h3>
    <div class="exp-actions">
        <span class="exp-muted">{if ne( $b.settings|count, $b.total )}{'%shown of %total settings'|i18n( 'design/admin/settings',, hash( '%shown', $b.settings|count, '%total', $b.total ) )|wash}{else}{'%total settings'|i18n( 'design/admin/settings',, hash( '%total', $b.total ) )|wash}{/if}</span>
        {if and( is_set( $settings[$b.name] ), $settings[$b.name].editable )}<a class="exp-btn exp-btn-small" href={concat( 'settings/edit/', $current_siteaccess, '/', $ini_file, '/', $b.name )|ezurl}>{'Add setting'|i18n( 'design/admin/settings' )}</a>{/if}
    </div>
</div>
<ul class="exp-plain">
{foreach $b.settings as $row}
<li class="exp-set{if $row.pending} is-pending{/if}" id="{$row.anchor|wash}">
    <div class="exp-set-select">{if $row.removable}<input type="checkbox" name="RemoveSettingsArray[]" value="{$b.name|wash}:{$row.name|wash}" aria-label="{'Remove %setting from %file'|i18n( 'design/admin/settings',, hash( '%setting', $row.name, '%file', $row.remove_from ) )|wash}" title="{'Remove from %file'|i18n( 'design/admin/settings',, hash( '%file', $row.remove_from ) )|wash}" />{/if}</div>
    <div class="exp-set-main">
        <div class="exp-set-name">
            <code>{$row.name|wash}{if ne( $row.kind, 'plain' )}[]{/if}</code>
            <ul class="exp-badges">
                {if $row.secret}<li class="exp-badge is-warn">{'secret, masked'|i18n( 'design/admin/settings' )}</li>{/if}
                {if $row.in_default|not}<li class="exp-badge is-info">{'no default'|i18n( 'design/admin/settings' )}</li>{elseif $row.changed}<li class="exp-badge is-info">{'changed'|i18n( 'design/admin/settings' )}</li>{/if}
                {if eq( $row.count, 0 )}{elseif eq( $row.kind, 'list' )}<li class="exp-badge">{'list of %count'|i18n( 'design/admin/settings',, hash( '%count', $row.count ) )}</li>{elseif eq( $row.kind, 'hash' )}<li class="exp-badge">{'%count keys'|i18n( 'design/admin/settings',, hash( '%count', $row.count ) )}</li>{elseif eq( $row.kind, 'mixed' )}<li class="exp-badge">{'%count elements, some with keys'|i18n( 'design/admin/settings',, hash( '%count', $row.count ) )}</li>{/if}
                {if $row.restart}<li class="exp-badge is-bad" title="{'Velocity reads it only when it starts'|i18n( 'design/admin/settings' )}">{'Velocity restart'|i18n( 'design/admin/settings' )}</li>{/if}
                {if $row.pending}<li class="exp-badge is-warn">{'not in effect yet'|i18n( 'design/admin/settings' )}</li>{/if}
            </ul>
        </div>
        <div class="exp-set-value">
        {if eq( $row.kind, 'plain' )}
            {if $row.secret}
                {if eq( $row.secret_state, 'empty' )}<span class="exp-val is-empty">{'empty'|i18n( 'design/admin/settings' )}</span>{else}<span class="exp-val is-secret" aria-label="{'set, hidden'|i18n( 'design/admin/settings' )}">{$row.text|wash}</span> <span class="exp-muted">{'set'|i18n( 'design/admin/settings' )}</span>{/if}
            {elseif eq( $row.text, '' )}<span class="exp-val is-empty">{'empty'|i18n( 'design/admin/settings' )}</span>
            {elseif or( eq( $row.text, 'true' ), eq( $row.text, 'enabled' ) )}<span class="exp-val is-true">{$row.text|wash}</span>
            {elseif or( eq( $row.text, 'false' ), eq( $row.text, 'disabled' ) )}<span class="exp-val is-false">{$row.text|wash}</span>
            {else}<span class="exp-val">{$row.text|wash}</span>{if $row.inline_masked} <span class="exp-muted">{'(password masked)'|i18n( 'design/admin/settings' )}</span>{/if}
            {/if}
        {elseif eq( $row.count, 0 )}
            <span class="exp-val is-empty">{'empty array'|i18n( 'design/admin/settings' )}</span>
        {else}
            <ol class="exp-vals">
            {foreach $row.elements as $el}<li><span class="exp-key">{if $el.string_key}[{$el.key|wash}]{else}{$el.key|inc}.{/if}</span>{if $el.empty}<span class="exp-val is-empty">{'empty'|i18n( 'design/admin/settings' )}</span>{else}<span class="exp-val{if $row.secret} is-secret{/if}">{$el.text|wash}</span>{/if}{if and( $el.origin, $row.sources|count|gt( 1 ) )} {include uri='design:settings/exp_origin.tpl' origin=$el.origin}{/if}</li>{/foreach}
            </ol>
            {if $row.more}
            <details class="exp-chain"><summary>{'Show %count more'|i18n( 'design/admin/settings',, hash( '%count', $row.more|count ) )}</summary>
            <ol class="exp-vals">
            {foreach $row.more as $el}<li><span class="exp-key">{if $el.string_key}[{$el.key|wash}]{else}{$el.key|inc}.{/if}</span>{if $el.empty}<span class="exp-val is-empty">{'empty'|i18n( 'design/admin/settings' )}</span>{else}<span class="exp-val{if $row.secret} is-secret{/if}">{$el.text|wash}</span>{/if}{if and( $el.origin, $row.sources|count|gt( 1 ) )} {include uri='design:settings/exp_origin.tpl' origin=$el.origin}{/if}</li>{/foreach}
            </ol>
            </details>
            {/if}
        {/if}
        </div>
        {if $row.pending}<p class="exp-set-note is-warn">{if $row.runtime_missing}{'This server does not have this setting yet.'|i18n( 'design/admin/settings' )}{else}{'This server still runs with: %value'|i18n( 'design/admin/settings',, hash( '%value', $row.runtime_text|shorten( 120 ) ) )|wash}{/if}</p>{/if}
        {if and( $row.changed, $row.in_default )}<p class="exp-set-note">{'Default:'|i18n( 'design/admin/settings' )} {if eq( $row.default_text, '' )}<em>{'empty'|i18n( 'design/admin/settings' )}</em>{else}<code>{$row.default_text|shorten( 160 )|wash}</code>{/if}</p>{/if}
        <details class="exp-chain">
            <summary>{if eq( $row.steps|count, 1 )}{'Set in 1 file'|i18n( 'design/admin/settings' )}{else}{'Set in %count files, %overridden overridden'|i18n( 'design/admin/settings',, hash( '%count', $row.steps|count, '%overridden', $row.overridden ) )}{/if}</summary>
            <ol class="exp-steps">
            {foreach $row.steps as $step}
            <li class="is-{$step.status|wash}">
                <div class="exp-step-head">{include uri='design:settings/exp_origin.tpl' origin=$step.placement}
                    <span class="exp-badge{if or( eq( $step.status, 'wins' ), eq( $step.status, 'adds' ) )} is-ok{/if}">{switch match=$step.status}{case match='wins'}{'in effect'|i18n( 'design/admin/settings' )}{/case}{case match='adds'}{'elements in effect'|i18n( 'design/admin/settings' )}{/case}{case match='reset'}{'empties it, adds nothing'|i18n( 'design/admin/settings' )}{/case}{case}{'overridden'|i18n( 'design/admin/settings' )}{/case}{/switch}</span>
                    <span class="exp-step-path">{$step.path|wash}</span></div>
                <ul class="exp-ops">
                {foreach $step.lines as $line}
                <li>{switch match=$line.op}
                    {case match='reset'}<code>{$row.name|wash}[]</code> <span class="exp-op-what">{'empties the array: what earlier files added is dropped'|i18n( 'design/admin/settings' )}</span>{/case}
                    {case match='append'}<code>{$row.name|wash}[]={$line.text|wash}</code> <span class="exp-op-what">{'adds an element'|i18n( 'design/admin/settings' )}</span>{/case}
                    {case match='hash'}<code>{$row.name|wash}[{$line.key|wash}]={$line.text|wash}</code> <span class="exp-op-what">{'sets the key, replacing an earlier one'|i18n( 'design/admin/settings' )}</span>{/case}
                    {case}<code>{$row.name|wash}={$line.text|wash}</code>{/case}
                {/switch}</li>
                {/foreach}
                </ul>
            </li>
            {/foreach}
            </ol>
        </details>
    </div>
    <div class="exp-set-side">
        {if and( ne( $row.kind, 'plain' ), $row.sources )}<span class="exp-muted exp-set-from">{'from'|i18n( 'design/admin/settings' )}</span>{foreach $row.sources as $source}{include uri='design:settings/exp_origin.tpl' origin=$source}{/foreach}{else}{include uri='design:settings/exp_origin.tpl' origin=$row.origin}{/if}
        {if $row.editable}<a class="exp-btn exp-btn-small" href={concat( 'settings/edit/', $current_siteaccess, '/', $ini_file, '/', $b.name, '/', $row.name, '/', $row.edit_placement )|ezurl} aria-label="{'Edit %setting'|i18n( 'design/admin/settings',, hash( '%setting', $row.name ) )|wash}">{'Edit'|i18n( 'design/admin/settings' )}</a>{/if}
    </div>
</li>
{/foreach}
</ul>
</section>
{/foreach}
</div>

<div class="exp-bottombar">
    <p class="exp-meta">{'Remove takes a ticked setting out of settings/override or the siteaccess file that sets it last; the value of the next file down, or the default, takes over. Settings from settings/%file and from extensions cannot be removed here.'|i18n( 'design/admin/settings',, hash( '%file', $ini_file ) )|wash}</p>
    <button type="submit" class="exp-btn exp-btn-outline-danger" name="RemoveButton" value="1">{'Remove selected'|i18n( 'design/admin/settings' )}</button>
</div>
</form>
{/if}
</section>
{/if}

{/if}

</div></div></div>
</div>
{undef $has_file $base_url $filtered}
