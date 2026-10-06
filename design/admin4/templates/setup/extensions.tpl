{* Setup > Extensions (setup/extensions): one list of every extension.

   Each card shows whether the extension is active and where it is in ActiveExtensions, what it is (name, version,
   description, license, website), whether a siteaccess activates it as an access extension or uses one of its
   designs, what it depends on and what depends on it, whether it is a git checkout, and what is wrong. Active
   extensions are moved up and down in the same list, and activated and deactivated there; nothing is written until
   Review changes shows what will change (added, removed, moved, the risks and the resulting ActiveExtensions lines)
   and Apply changes writes it in one step. The plan travels in the form (ExtensionPlan[], ExtensionBase), so every
   button works without javascript; the script moves cards in place, adds drag and drop (with the arrow keys on the
   grip), and filters, searches and sorts without a reload.

   The same file is in design/admin and design/admin4. The variables of the page before the redesign
   (available_extension_array, selected_extension_array, extension_positions, extension_info ...) are still set by
   the view. Guide: doc/guides/extensions-page.md *}
{include uri='design:setup/extensions_exp_style.tpl'}

{def $plan = $extension_plan
     $summary = $extension_summary
     $active_count = $plan.planned|count}

<div class="context-block exp-extpage">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Extensions'|i18n( 'design/admin/setup/extensions' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Every extension this installation can see, in one list. The active ones come first, in the order of ActiveExtensions in %file: an extension higher in the list has the higher priority for settings, templates and designs. Move, activate and deactivate them here, then review and apply the changes in one step.'|i18n( 'design/admin/setup/extensions',, hash( '%file', $settings_file ) )|wash}</p>

{* What happened *}
{if and( is_set( $save_error ), $save_error )}
<div class="exp-feedback is-bad" role="alert"><p><strong>{'The extensions were not changed.'|i18n( 'design/admin/setup/extensions' )}</strong></p><p>{$save_error|wash}</p></div>
{elseif and( is_set( $save_message ), $save_message )}
<div class="exp-feedback is-ok" role="status"><p>{$save_message|wash}</p></div>
{/if}

{if $extension_notice}
<div class="exp-feedback is-ok" role="status">
    <p><strong>{$extension_notice.message|wash}</strong></p>
    {if or( $extension_notice.diff.added, $extension_notice.diff.removed, $extension_notice.diff.moved )}
    <ul>
        {if $extension_notice.diff.added}<li>{'Activated: %names'|i18n( 'design/admin/setup/extensions',, hash( '%names', $extension_notice.diff.added|implode( ', ' ) ) )|wash}</li>{/if}
        {if $extension_notice.diff.removed}<li>{'Deactivated: %names'|i18n( 'design/admin/setup/extensions',, hash( '%names', $extension_notice.diff.removed|implode( ', ' ) ) )|wash}</li>{/if}
        {if $extension_notice.diff.moved}<li>{'Moved: %names'|i18n( 'design/admin/setup/extensions',, hash( '%names', $extension_notice.diff.moved|implode( ', ' ) ) )|wash}</li>{/if}
    </ul>
    {/if}
    <p>{'The autoload arrays were regenerated, and the INI, template override, design and active extension caches were cleared.'|i18n( 'design/admin/setup/extensions' )}</p>
    <p>{if $extension_notice.velocity}<strong>{'Velocity is running: restart it with ./console exp:velocity restart, so that its workers load the new list.'|i18n( 'design/admin/setup/extensions' )|wash}</strong>{elseif is_null( $extension_notice.velocity )}{'If this installation is also served by Velocity, restart it with ./console exp:velocity restart, so that its workers load the new list.'|i18n( 'design/admin/setup/extensions' )|wash}{else}{'Velocity is not running here, so nothing more is needed.'|i18n( 'design/admin/setup/extensions' )}{/if}</p>
</div>
{/if}

{if and( is_set( $warning_messages ), $warning_messages|count|ge( 1 ) )}
<details class="exp-feedback is-warn exp-warnings">
    <summary><strong>{'Problems detected during autoload generation:'|i18n( 'design/admin/setup/extensions' )}</strong> {$warning_messages|count}</summary>
    <ul>
    {foreach $warning_messages as $warning}
        <li>{$warning|break()}</li>
    {/foreach}
    </ul>
</details>
{/if}

{if $extension_stale}
<div class="exp-feedback is-warn" role="alert"><p>{'The active extensions were changed elsewhere since this page was drawn, so the list below starts again from the file. Make your changes again.'|i18n( 'design/admin/setup/extensions' )}</p></div>
{/if}

<section aria-labelledby="ext-overview-title">
<h2 class="exp-sr" id="ext-overview-title">{'Overview'|i18n( 'design/admin/setup/extensions' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$summary.active}</strong><span>{'Active'|i18n( 'design/admin/setup/extensions' )}</span></li>
    <li class="exp-figure"><strong>{$summary.inactive}</strong><span>{'Inactive'|i18n( 'design/admin/setup/extensions' )}</span></li>
    {if $summary.access|gt( 0 )}<li class="exp-figure"><strong>{$summary.access}</strong><span>{'Only for siteaccesses'|i18n( 'design/admin/setup/extensions' )}</span></li>{/if}
    <li class="exp-figure"><strong>{$summary.git}</strong><span>{'Git checkouts'|i18n( 'design/admin/setup/extensions' )}</span></li>
    <li class="exp-figure{if $summary.problems|gt( 0 )} is-attention{/if}"><strong>{$summary.problems}</strong><span>{'With problems'|i18n( 'design/admin/setup/extensions' )}</span></li>
</ul>
</section>

{if and( $extension_ordering, $plan.effective_moves|gt( 0 ) )}
<div class="exp-feedback is-info"><p>{'ExtensionOrdering is enabled, so the system loads the active extensions in the order their extension.xml dependencies (requires, uses, extends) give, and the written order only decides where nothing is declared. %count of %total load at another position than written; each card says where.'|i18n( 'design/admin/setup/extensions',, hash( '%count', $plan.effective_moves, '%total', $active_count ) )|wash}</p></div>
{/if}

<form name="extensionform" id="extension-form" method="post" action={'/setup/extensions'|ezurl}>
{* Enter in the search field presses this one, not the first card's buttons *}
<button type="submit" name="ExtensionFilterButton" value="1" class="exp-sr" tabindex="-1" aria-hidden="true">{'Show'|i18n( 'design/admin/setup/extensions' )}</button>
<input type="hidden" name="ExtensionPlanForm" value="1" />
<input type="hidden" name="ExtensionBase" value="{$plan.base|wash}" />
<div id="ext-plan-inputs" data-initial="{$active_extension_order|implode( ',' )|wash}" data-server-changed="{if $plan.changed}1{else}0{/if}">
{foreach $plan.planned as $name}<input type="hidden" name="ExtensionPlan[]" value="{$name|wash}" />
{/foreach}
</div>

{* The review: what Apply changes writes *}
{if $extension_review}
<section class="exp-review" id="ext-review" aria-labelledby="ext-review-title" tabindex="-1">
    <h2 class="exp-h2" id="ext-review-title">{'Review the changes'|i18n( 'design/admin/setup/extensions' )}</h2>
    <p class="exp-meta">{'Nothing is written yet. Apply changes writes the list below to %file, keeps a copy of the previous file, regenerates the autoload arrays and clears the caches built on the list.'|i18n( 'design/admin/setup/extensions',, hash( '%file', $settings_file ) )|wash}</p>
    <div class="exp-review-cols">
        <div><h3>{'Activated'|i18n( 'design/admin/setup/extensions' )} ({$plan.diff.added|count})</h3>
            {if $plan.diff.added}<ul>{foreach $plan.diff.added as $name}<li><code>{$name|wash}</code></li>{/foreach}</ul>{else}<p class="exp-meta">{'None'|i18n( 'design/admin/setup/extensions' )}</p>{/if}</div>
        <div><h3>{'Deactivated'|i18n( 'design/admin/setup/extensions' )} ({$plan.diff.removed|count})</h3>
            {if $plan.diff.removed}<ul>{foreach $plan.diff.removed as $name}<li><code>{$name|wash}</code></li>{/foreach}</ul>{else}<p class="exp-meta">{'None'|i18n( 'design/admin/setup/extensions' )}</p>{/if}</div>
        <div><h3>{'Moved'|i18n( 'design/admin/setup/extensions' )} ({$plan.diff.moved|count})</h3>
            {if $plan.diff.moved}<ul>{foreach $plan.diff.moved as $name}<li><code>{$name|wash}</code></li>{/foreach}</ul>{else}<p class="exp-meta">{'None'|i18n( 'design/admin/setup/extensions' )}</p>{/if}</div>
    </div>
    {if $plan.risks}
    {foreach $plan.risks as $risk}
    <div class="exp-feedback {if eq( $risk.severity, 'bad' )}is-bad{elseif eq( $risk.severity, 'warn' )}is-warn{else}is-info{/if}"><p>{$risk.text|wash}</p></div>
    {/foreach}
    {/if}
    <p class="exp-meta">{'The ActiveExtensions lines after the change (changed lines marked):'|i18n( 'design/admin/setup/extensions' )}</p>
    <div class="exp-lines"><span class="exp-line">ActiveExtensions[]</span>{foreach $plan.planned as $name}{if $plan.diff.added|contains( $name )}<span class="exp-line is-added">ActiveExtensions[]={$name|wash} <em>+ {'activated'|i18n( 'design/admin/setup/extensions' )}</em></span>{elseif $plan.diff.moved|contains( $name )}<span class="exp-line is-moved">ActiveExtensions[]={$name|wash} <em>~ {'moved'|i18n( 'design/admin/setup/extensions' )}</em></span>{else}<span class="exp-line">ActiveExtensions[]={$name|wash}</span>{/if}{/foreach}</div>
    {if $plan.needs_ack}
    {if and( is_set( $ack_missing ), $ack_missing )}<div class="exp-feedback is-bad" role="alert"><p>{'Tick the box to confirm the risks above, or go back and change the plan.'|i18n( 'design/admin/setup/extensions' )}</p></div>{/if}
    <label class="exp-ack"><input type="checkbox" name="ExtensionAcknowledgeRisks" value="1" /> <span>{'I have read the risks above and want to apply these changes anyway.'|i18n( 'design/admin/setup/extensions' )}</span></label>
    {/if}
    <div class="exp-actions">
        <button type="submit" class="exp-btn {if $plan.needs_ack}exp-btn-danger{else}exp-btn-primary{/if}" name="ExtensionApplyButton" value="1">{'Apply changes'|i18n( 'design/admin/setup/extensions' )}</button>
        <button type="submit" class="exp-btn" name="ExtensionBackButton" value="1">{'Back to the list'|i18n( 'design/admin/setup/extensions' )}</button>
        <button type="submit" class="exp-btn" name="ExtensionDiscardButton" value="1">{'Discard changes'|i18n( 'design/admin/setup/extensions' )}</button>
    </div>
</section>
{/if}

{* Unsaved changes: shown by the server after a change, and by the script after a move in place *}
<div class="exp-bar exp-pending{if $plan.changed} is-dirty{/if}" id="ext-pending"{if or( $plan.changed|not, $extension_review )} hidden{/if} role="status">
    <p class="exp-meta" id="ext-pending-text">{if $plan.changed}{'Not applied yet: %added to activate, %removed to deactivate, %moved moved.'|i18n( 'design/admin/setup/extensions',, hash( '%added', $plan.diff.added|count, '%removed', $plan.diff.removed|count, '%moved', $plan.diff.moved|count ) )}{else}{'The order was changed. Nothing is written until you review and apply it.'|i18n( 'design/admin/setup/extensions' )}{/if}</p>
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-primary" name="ExtensionReviewButton" value="1">{'Review changes'|i18n( 'design/admin/setup/extensions' )}</button>
        <button type="submit" class="exp-btn" name="ExtensionDiscardButton" value="1">{'Discard changes'|i18n( 'design/admin/setup/extensions' )}</button>
    </div>
</div>

<section aria-labelledby="ext-controls-title">
<h2 class="exp-sr" id="ext-controls-title">{'Find extensions'|i18n( 'design/admin/setup/extensions' )}</h2>
<div class="exp-toolbar">
    <div class="exp-field">
        <label for="ext-search">{'Search'|i18n( 'design/admin/setup/extensions' )}</label>
        <input type="search" id="ext-search" name="ExtensionSearch" value="{$extension_search|wash}" autocomplete="off" spellcheck="false" aria-controls="ext-list" aria-describedby="ext-count" placeholder="{'Name, description or license'|i18n( 'design/admin/setup/extensions' )|wash}" />
    </div>
    <fieldset class="exp-field">
        <legend>{'Order'|i18n( 'design/admin/setup/extensions' )}</legend>
        <div class="exp-chips">
            <label class="exp-chip"><input type="radio" name="ExtensionSort" value="order"{if eq( $extension_sort_mode, 'order' )} checked="checked"{/if} /><span>{'Loading order'|i18n( 'design/admin/setup/extensions' )}</span></label>
            <label class="exp-chip"><input type="radio" name="ExtensionSort" value="name"{if eq( $extension_sort_mode, 'name' )} checked="checked"{/if} /><span>{'Name'|i18n( 'design/admin/setup/extensions' )}</span></label>
        </div>
    </fieldset>
    <fieldset class="exp-field exp-field-wide">
        <legend>{'Show'|i18n( 'design/admin/setup/extensions' )}</legend>
        <div class="exp-chips">
            <label class="exp-chip"><input type="radio" name="ExtensionFilter" value="all"{if eq( $extension_filter, 'all' )} checked="checked"{/if} /><span>{'All'|i18n( 'design/admin/setup/extensions' )} <small>{$summary.total}</small></span></label>
            <label class="exp-chip"><input type="radio" name="ExtensionFilter" value="active"{if eq( $extension_filter, 'active' )} checked="checked"{/if} /><span>{'Active'|i18n( 'design/admin/setup/extensions' )} <small>{$summary.active}</small></span></label>
            <label class="exp-chip"><input type="radio" name="ExtensionFilter" value="inactive"{if eq( $extension_filter, 'inactive' )} checked="checked"{/if} /><span>{'Inactive'|i18n( 'design/admin/setup/extensions' )} <small>{sum( $summary.inactive, $summary.access )}</small></span></label>
            <label class="exp-chip"><input type="radio" name="ExtensionFilter" value="problems"{if eq( $extension_filter, 'problems' )} checked="checked"{/if} /><span>{'Problems'|i18n( 'design/admin/setup/extensions' )} <small>{$summary.problems}</small></span></label>
        </div>
    </fieldset>
    <div class="exp-field exp-nojs-only">
        <button type="submit" class="exp-btn" name="ExtensionFilterButton" value="1">{'Show'|i18n( 'design/admin/setup/extensions' )}</button>
    </div>
    <p class="exp-filter-count" id="ext-count" aria-live="polite"></p>
</div>
</section>

<section class="exp-section" aria-labelledby="ext-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="ext-list-title">{'All extensions'|i18n( 'design/admin/setup/extensions' )}</h2>
    <p>{'Use the arrows to move an active extension, or drag it by its grip (on the grip, the up and down arrow keys move it too). Activate places an extension after the extensions it requires and before the ones it extends, otherwise at the end.'|i18n( 'design/admin/setup/extensions' )}</p>
</div>

<p class="exp-empty" id="ext-none"{if $extension_rows|count|gt( 0 )} hidden{/if}>{'No extension matches.'|i18n( 'design/admin/setup/extensions' )}</p>
<p class="exp-sr" id="ext-live" aria-live="assertive"></p>

<ol class="exp-ext-list" id="ext-list"
    data-sort="{$extension_sort_mode|wash}"
    data-moved="{'%name is now number %position.'|i18n( 'design/admin/setup/extensions' )|wash}"
    data-count="{'Showing %shown of %total extensions.'|i18n( 'design/admin/setup/extensions' )|wash}">
{foreach $extension_rows as $row}
<li class="exp-ext{if $row.active} is-active{else} is-inactive{/if}{if eq( $row.problem_level, 'bad' )} is-bad{elseif eq( $row.problem_level, 'warn' )} is-warn{/if}{if $row.state} is-changed{/if}"
    id="ext-{$row.name|wash}" data-name="{$row.name|wash}" data-active="{if $row.active}1{else}0{/if}" data-level="{$row.problem_level|wash}" data-search="{$row.search|wash}"{if $row.hidden} hidden{/if}>
    <div class="exp-ext-pos">
        {if $row.active}
        <span class="exp-ext-num" title="{'Position in ActiveExtensions'|i18n( 'design/admin/setup/extensions' )|wash}"><span class="exp-sr">{'Position'|i18n( 'design/admin/setup/extensions' )} </span><span class="exp-ext-n">{$row.position}</span></span>
        <span class="exp-grip exp-js-only" hidden draggable="true" role="button" tabindex="0" aria-label="{'Move %name: drag, or use the up and down arrow keys'|i18n( 'design/admin/setup/extensions',, hash( '%name', $row.name ) )|wash}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M5 3h2v2H5zm4 0h2v2H9zM5 7h2v2H5zm4 0h2v2H9zm-4 4h2v2H5zm4 0h2v2H9z"/></svg></span>
        {else}
        <span class="exp-ext-num" title="{'Not in ActiveExtensions'|i18n( 'design/admin/setup/extensions' )|wash}" aria-label="{'Inactive'|i18n( 'design/admin/setup/extensions' )|wash}">&ndash;</span>
        {/if}
    </div>
    <div class="exp-ext-main">
        <div class="exp-ext-title">
            <h3><code>{$row.name|wash}</code></h3>
            {if $row.title}<span class="exp-ext-label">{$row.title|wash}</span>{/if}
            {if $row.version}<span class="exp-meta">{$row.version|wash}</span>{/if}
        </div>
        <ul class="exp-badges exp-ext-badges">
            {if $row.active}<li class="exp-badge is-ok">{'Active'|i18n( 'design/admin/setup/extensions' )}</li>{elseif $row.access}<li class="exp-badge is-info">{'Only for siteaccesses'|i18n( 'design/admin/setup/extensions' )}</li>{else}<li class="exp-badge">{'Inactive'|i18n( 'design/admin/setup/extensions' )}</li>{/if}
            {if eq( $row.state, 'added' )}<li class="exp-badge is-change">{'Will be activated'|i18n( 'design/admin/setup/extensions' )}</li>{elseif eq( $row.state, 'removed' )}<li class="exp-badge is-change">{'Will be deactivated'|i18n( 'design/admin/setup/extensions' )}</li>{elseif eq( $row.state, 'moved' )}<li class="exp-badge is-change">{'Moved from %position'|i18n( 'design/admin/setup/extensions',, hash( '%position', $row.current_position ) )}</li>{/if}
            {foreach $row.access as $sa}<li class="exp-badge is-info" title="{'ActiveAccessExtensions of this siteaccess'|i18n( 'design/admin/setup/extensions' )|wash}">{'Access extension: %siteaccess'|i18n( 'design/admin/setup/extensions',, hash( '%siteaccess', $sa ) )|wash}</li>{/foreach}
            {if $row.git}<li class="exp-badge" title="{'A git checkout: updated with git, not by a package'|i18n( 'design/admin/setup/extensions' )|wash}">git</li>{/if}
            {if $row.installed|not}<li class="exp-badge is-bad">{'Not installed'|i18n( 'design/admin/setup/extensions' )}</li>{/if}
            {if eq( $row.problem_level, 'bad' )}<li class="exp-badge is-bad">{'Problem'|i18n( 'design/admin/setup/extensions' )}</li>{elseif eq( $row.problem_level, 'warn' )}<li class="exp-badge is-warn">{'Warning'|i18n( 'design/admin/setup/extensions' )}</li>{/if}
        </ul>
        {if $row.description}<p class="exp-ext-desc">{$row.description|wash}</p>{/if}
        <dl class="exp-facts">
            {if $row.license}<div><dt>{'License'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$row.license|wash}</dd></div>{/if}
            {if $row.info_url}<div><dt>{'Website'|i18n( 'design/admin/setup/extensions' )}</dt><dd><a href="{$row.info_url|wash}" rel="noopener noreferrer" target="_blank">{$row.info_url|wash}</a></dd></div>{/if}
            {if and( $row.active, $extension_ordering, ne( $row.effective, $row.position ) )}<div><dt>{'Loads as'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{'number %position (dependency order)'|i18n( 'design/admin/setup/extensions',, hash( '%position', $row.effective ) )}</dd></div>{/if}
            {if $row.requires}<div><dt>{'Requires'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$row.requires|implode( ', ' )|wash}</dd></div>{/if}
            {if $row.uses}<div><dt>{'Uses'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$row.uses|implode( ', ' )|wash}</dd></div>{/if}
            {if $row.extends}<div><dt>{'Extends'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$row.extends|implode( ', ' )|wash}</dd></div>{/if}
            {if $row.dependents}<div><dt>{'Needed by'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$row.dependents|implode( ', ' )|wash}</dd></div>{/if}
        </dl>
        {if $row.problems}
        <ul class="exp-problems">
            {foreach $row.problems as $problem}{if ne( $problem.severity, 'info' )}<li class="is-{$problem.severity|wash}">{$problem.text|wash}</li>{/if}{/foreach}
        </ul>
        {/if}
        <details class="exp-more">
            <summary>{'Details'|i18n( 'design/admin/setup/extensions' )}<span class="exp-sr"> {$row.name|wash}</span></summary>
            <dl class="exp-facts">
                {if $row.designs}<div><dt>{'Designs in use'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{foreach $row.designs as $d}{$d.design|wash} ({$d.siteaccesses|implode( ', ' )|wash}){delimiter}; {/delimiter}{/foreach}</dd></div>{/if}
                {if $row.author}<div><dt>{'Author'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$row.author|wash}</dd></div>{/if}
                {if $row.copyright}<div><dt>{'Copyright'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$row.copyright|wash}</dd></div>{/if}
                {if $row.mtime}<div><dt>{'Modified'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$row.mtime|wash}</dd></div>{/if}
                <div><dt>{'Download'|i18n( 'design/admin/setup/extensions' )}</dt><dd>
                    <a href={concat( '/setup/extensions/', $row.name, '/tar.gz' )|ezurl}>tar.gz</a>,
                    <a href={concat( '/setup/extensions/', $row.name, '/zip' )|ezurl}>zip</a>,
                    <a href={concat( '/setup/extensions/', $row.name, '/tar.bz2' )|ezurl}>tar.bz2</a>,
                    <a href={concat( '/setup/extensions/', $row.name, '/ezpkg' )|ezurl}>ezpkg</a></dd></div>
            </dl>
            {if $row.problems}
            <ul class="exp-problems">
                {foreach $row.problems as $problem}{if eq( $problem.severity, 'info' )}<li class="is-info">{$problem.text|wash}</li>{/if}{/foreach}
            </ul>
            {/if}
        </details>
    </div>
    <div class="exp-ext-actions">
        {if $row.active}
        <span class="exp-ext-moves">
            <button type="submit" class="exp-btn exp-btn-icon exp-move" data-move="-1" name="ExtensionAction" value="up:{$row.name|wash}" formaction={concat( '/setup/extensions#ext-', $row.name )|ezurl}{if eq( $row.position, 1 )} disabled="disabled"{/if} title="{'Load earlier'|i18n( 'design/admin/setup/extensions' )|wash}" aria-label="{'Load %name earlier'|i18n( 'design/admin/setup/extensions',, hash( '%name', $row.name ) )|wash}">&#8593;</button>
            <button type="submit" class="exp-btn exp-btn-icon exp-move" data-move="1" name="ExtensionAction" value="down:{$row.name|wash}" formaction={concat( '/setup/extensions#ext-', $row.name )|ezurl}{if eq( $row.position, $active_count )} disabled="disabled"{/if} title="{'Load later'|i18n( 'design/admin/setup/extensions' )|wash}" aria-label="{'Load %name later'|i18n( 'design/admin/setup/extensions',, hash( '%name', $row.name ) )|wash}">&#8595;</button>
        </span>
        <button type="submit" class="exp-btn exp-btn-small" name="ExtensionAction" value="deactivate:{$row.name|wash}" formaction={concat( '/setup/extensions#ext-', $row.name )|ezurl} aria-label="{'Deactivate %name'|i18n( 'design/admin/setup/extensions',, hash( '%name', $row.name ) )|wash}">{if $row.installed}{'Deactivate'|i18n( 'design/admin/setup/extensions' )}{else}{'Remove from the list'|i18n( 'design/admin/setup/extensions' )}{/if}</button>
        {elseif $row.installed}
        <button type="submit" class="exp-btn exp-btn-small" name="ExtensionAction" value="activate:{$row.name|wash}" formaction={concat( '/setup/extensions#ext-', $row.name )|ezurl} aria-label="{'Activate %name'|i18n( 'design/admin/setup/extensions',, hash( '%name', $row.name ) )|wash}">{'Activate'|i18n( 'design/admin/setup/extensions' )}</button>
        {/if}
    </div>
</li>
{/foreach}
</ol>
</section>

<div class="exp-bottombar">
    <p class="exp-meta">{'Changes are written only by Apply changes, after the review. Regenerate the autoload arrays after adding classes to an extension.'|i18n( 'design/admin/setup/extensions' )}</p>
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-primary" name="ExtensionReviewButton" value="1" id="ext-review-bottom"{if $plan.changed|not} disabled="disabled"{/if}>{'Review changes'|i18n( 'design/admin/setup/extensions' )}</button>
        <button type="submit" class="exp-btn" name="GenerateAutoloadArraysButton" value="1" title="{'Click this button to regenerate the autoload arrays used by the system for extensions.'|i18n( 'design/admin/setup/extensions' )|wash}">{'Regenerate autoload arrays'|i18n( 'design/admin/setup/extensions' )}</button>
    </div>
</div>

</form>

</div></div></div>

</div>

{undef $plan $summary $active_count}

{literal}
<script>
(function () {
    'use strict';
    var form = document.getElementById( 'extension-form' );
    var list = document.getElementById( 'ext-list' );
    if ( !form || !list ) return;
    var root = form.closest( '.exp-extpage' );
    root.querySelectorAll( '.exp-js-only' ).forEach( function ( el ) { el.hidden = false; } );
    root.querySelectorAll( '.exp-nojs-only' ).forEach( function ( el ) { el.hidden = true; } );

    var planBox = document.getElementById( 'ext-plan-inputs' );
    var pending = document.getElementById( 'ext-pending' );
    var pendingText = document.getElementById( 'ext-pending-text' );
    var reviewBottom = document.getElementById( 'ext-review-bottom' );
    var live = document.getElementById( 'ext-live' );
    var countLine = document.getElementById( 'ext-count' );
    var none = document.getElementById( 'ext-none' );
    var search = document.getElementById( 'ext-search' );
    var initial = planBox.getAttribute( 'data-initial' );
    var serverChanged = planBox.getAttribute( 'data-server-changed' ) === '1';
    var reviewOpen = !!document.getElementById( 'ext-review' );
    var dragged = null;

    function cards() { return Array.prototype.slice.call( list.querySelectorAll( 'li.exp-ext' ) ); }
    function card( name ) { return cards().filter( function ( li ) { return li.getAttribute( 'data-name' ) === name; } )[0]; }
    function plan() { return Array.prototype.map.call( planBox.querySelectorAll( 'input' ), function ( i ) { return i.value; } ); }
    function setPlan( names ) {
        planBox.textContent = '';
        names.forEach( function ( n ) {
            var i = document.createElement( 'input' );
            i.type = 'hidden'; i.name = 'ExtensionPlan[]'; i.value = n;
            planBox.appendChild( i );
        } );
    }
    function sortMode() { var r = form.querySelector( 'input[name="ExtensionSort"]:checked' ); return r ? r.value : 'order'; }
    function filterMode() { var r = form.querySelector( 'input[name="ExtensionFilter"]:checked' ); return r ? r.value : 'all'; }

    // Positions, the arrows' disabled state and the pending bar follow the plan
    function renumber() {
        var names = plan();
        names.forEach( function ( n, i ) {
            var li = card( n ); if ( !li ) return;
            var num = li.querySelector( '.exp-ext-n' ); if ( num ) num.textContent = i + 1;
            var up = li.querySelector( '.exp-move[data-move="-1"]' ), down = li.querySelector( '.exp-move[data-move="1"]' );
            if ( up ) up.disabled = i === 0;
            if ( down ) down.disabled = i === names.length - 1;
        } );
        var dirty = serverChanged || names.join( ',' ) !== initial;
        if ( !reviewOpen ) pending.hidden = !dirty;
        pending.classList.toggle( 'is-dirty', dirty );
        if ( reviewBottom ) reviewBottom.disabled = !dirty;
        if ( !dirty ) cards().forEach( function ( li ) { li.classList.remove( 'is-changed' ); } );
    }

    // Cards in the order chosen: the active ones by the plan, then the others by name; or all by name
    function arrange() {
        var names = plan();
        var byName = function ( a, b ) { return a.getAttribute( 'data-name' ).localeCompare( b.getAttribute( 'data-name' ), undefined, { numeric: true, sensitivity: 'base' } ); };
        var all = cards();
        var sorted;
        if ( sortMode() === 'name' ) sorted = all.sort( byName );
        else {
            var active = names.map( card ).filter( Boolean );
            var rest = all.filter( function ( li ) { return active.indexOf( li ) === -1; } ).sort( byName );
            sorted = active.concat( rest );
        }
        sorted.forEach( function ( li ) { list.appendChild( li ); } );
        var grips = sortMode() === 'order';
        list.querySelectorAll( '.exp-grip' ).forEach( function ( g ) { g.hidden = !grips; } );
    }

    function filter() {
        var q = ( search.value || '' ).trim().toLowerCase(), f = filterMode(), shown = 0, all = cards();
        all.forEach( function ( li ) {
            var ok = true, level = li.getAttribute( 'data-level' ), active = li.getAttribute( 'data-active' ) === '1';
            if ( f === 'active' && !active ) ok = false;
            if ( f === 'inactive' && active ) ok = false;
            if ( f === 'problems' && level !== 'warn' && level !== 'bad' ) ok = false;
            if ( ok && q && li.getAttribute( 'data-search' ).indexOf( q ) === -1 ) ok = false;
            li.hidden = !ok;
            if ( ok ) shown++;
        } );
        none.hidden = shown > 0;
        countLine.textContent = list.getAttribute( 'data-count' ).replace( '%shown', shown ).replace( '%total', all.length );
    }

    function move( name, delta, focusEl ) {
        var names = plan(), i = names.indexOf( name ), j = i + delta;
        if ( i < 0 || j < 0 || j >= names.length ) return;
        names.splice( i, 1 ); names.splice( j, 0, name );
        setPlan( names );
        if ( sortMode() === 'order' ) arrange();
        var li = card( name ); if ( li ) li.classList.add( 'is-changed' );
        renumber();
        if ( focusEl ) focusEl.focus();
        live.textContent = list.getAttribute( 'data-moved' ).replace( '%name', name ).replace( '%position', j + 1 );
    }

    // The arrows move in place; activate and deactivate still ask the server, which places by the dependencies
    list.addEventListener( 'click', function ( e ) {
        var b = e.target.closest ? e.target.closest( '.exp-move' ) : null;
        if ( !b ) return;
        e.preventDefault();
        var li = b.closest( 'li.exp-ext' );
        move( li.getAttribute( 'data-name' ), parseInt( b.getAttribute( 'data-move' ), 10 ), b );
        if ( b.disabled ) { var other = li.querySelector( '.exp-move:not([disabled])' ); if ( other ) other.focus(); }
    } );

    // The grip: drag, or the arrow keys
    list.addEventListener( 'keydown', function ( e ) {
        var g = e.target.closest ? e.target.closest( '.exp-grip' ) : null;
        if ( !g || ( e.key !== 'ArrowUp' && e.key !== 'ArrowDown' ) ) return;
        e.preventDefault();
        move( g.closest( 'li.exp-ext' ).getAttribute( 'data-name' ), e.key === 'ArrowUp' ? -1 : 1, g );
    } );
    list.addEventListener( 'dragstart', function ( e ) {
        var g = e.target.closest ? e.target.closest( '.exp-grip' ) : null;
        if ( !g ) { e.preventDefault(); return; }
        dragged = g.closest( 'li.exp-ext' );
        e.dataTransfer.effectAllowed = 'move';
        try { e.dataTransfer.setData( 'text/plain', dragged.getAttribute( 'data-name' ) ); e.dataTransfer.setDragImage( dragged, 20, 20 ); } catch ( err ) {}
        setTimeout( function () { if ( dragged ) dragged.classList.add( 'is-dragging' ); }, 0 );
    } );
    list.addEventListener( 'dragover', function ( e ) {
        if ( !dragged ) return;
        var over = e.target.closest ? e.target.closest( 'li.exp-ext' ) : null;
        if ( !over || over === dragged || over.getAttribute( 'data-active' ) !== '1' ) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var box = over.getBoundingClientRect();
        list.insertBefore( dragged, ( e.clientY - box.top ) > box.height / 2 ? over.nextSibling : over );
    } );
    list.addEventListener( 'drop', function ( e ) { if ( dragged ) e.preventDefault(); } );
    list.addEventListener( 'dragend', function () {
        if ( !dragged ) return;
        dragged.classList.remove( 'is-dragging' );
        var name = dragged.getAttribute( 'data-name' );
        dragged = null;
        var order = cards().filter( function ( li ) { return li.getAttribute( 'data-active' ) === '1'; } ).map( function ( li ) { return li.getAttribute( 'data-name' ); } );
        var before = plan().join( ',' );
        setPlan( order );
        renumber();
        if ( order.join( ',' ) !== before ) {
            var li = card( name ); if ( li ) li.classList.add( 'is-changed' );
            live.textContent = list.getAttribute( 'data-moved' ).replace( '%name', name ).replace( '%position', order.indexOf( name ) + 1 );
        }
    } );

    // Filter, search and sort without a reload
    form.addEventListener( 'change', function ( e ) {
        if ( e.target.name === 'ExtensionSort' ) { arrange(); filter(); }
        else if ( e.target.name === 'ExtensionFilter' ) filter();
    } );
    search.addEventListener( 'input', filter );
    search.addEventListener( 'keydown', function ( e ) { if ( e.key === 'Enter' ) e.preventDefault(); } );

    renumber();
    filter();
    var review = document.getElementById( 'ext-review' );
    if ( review && !location.hash ) review.focus();
})();
</script>
{/literal}
