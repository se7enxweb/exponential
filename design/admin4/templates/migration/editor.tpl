{* migration/editor[/<migration id>]: write or change a YAML migration. Variables: migration (id, source, path, applied,
   writable; null for a new one), migration_id, text, check (expMigrationEditor::check(): ok, errors, warnings, header,
   steps), checked (open the preview), types (expMigrationEditor::stepTypes()), builder (the step type of the form, or
   null), builder_fields (mode, keys, if, references, environments, engines, comment, unknown), builder_index ('' for a
   new step), builder_list (steps or down), files (the YAML migrations: id, source, description, path, applied,
   editable), sources, name, source, messages, can_run, can_rollback, view. One form; its buttons are the actions of
   the view (module.php). *}
<div class="a4-page a4-setup-migration" id="exp-migration-editor">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{if $migration}{'Migration %id'|i18n( 'design/admin/migration',, hash( '%id', $migration.id ) )|wash}{else}{'New migration'|i18n( 'design/admin/migration' )}{/if}</h1>
        <p class="a4-pagehead-sub">{if $migration}<code class="a4-code">{$migration.path|wash}</code>{else}{'A YAML migration: write it as text or build its steps in the form, check it, then save it into a migrations folder.'|i18n( 'design/admin/migration' )}{/if}</p>
    </div>
</div>

{include uri='design:migration/parts/tabs.tpl' view=$view can_run=$can_run can_rollback=$can_rollback}

{foreach $messages as $message}
<div class="a4-notice is-{$message.kind|wash}" role="{if eq( $message.kind, 'bad' )}alert{else}status{/if}"><p>{$message.text|wash}</p></div>
{/foreach}
{if and( $migration, $migration.applied )}
<div class="a4-notice is-warn" role="status"><p>{'This migration has run already, so it is not saved again: a changed file would be refused by the checksum guard. Make the change in a new migration.'|i18n( 'design/admin/migration' )}</p></div>
{elseif and( $migration, not( $migration.writable ) )}
<div class="a4-notice is-warn" role="status"><p>{'The file cannot be written by the web server.'|i18n( 'design/admin/migration' )}</p></div>
{/if}

<form method="post" action={if $migration}{concat( 'migration/editor/', $migration.id )|ezurl}{else}{'migration/editor'|ezurl}{/if}>

<div class="a4-setup-editor-grid">
<div class="a4-stack">

<section class="a4-card a4-section" aria-labelledby="migration-editor-text">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-editor-text">{'Migration'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'The comment lines at the top are the header (description, author, requires, environments, engines). When a step is added or changed in the form, the migration is written again: comments between the steps are not kept.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body a4-stack">
        {if not( $migration )}
        <div class="a4-row">
            <div class="a4-field">
                <label class="a4-label" for="migration-editor-name">{'Name of the migration'|i18n( 'design/admin/migration' )}</label>
                <input class="a4-input" id="migration-editor-name" name="MigrationName" maxlength="100" value="{$name|wash}" placeholder="add_news_class" />
            </div>
            <div class="a4-field">
                <label class="a4-label" for="migration-editor-source">{'Where it goes'|i18n( 'design/admin/migration' )}</label>
                <select class="a4-input" id="migration-editor-source" name="MigrationSource">
                {foreach $sources as $s}<option value="{$s|wash}"{if eq( $s, $source )} selected="selected"{/if}>{if eq( $s, 'site' )}{'The site'|i18n( 'design/admin/migration' )}{else}{$s|wash}{/if}</option>{/foreach}
                </select>
            </div>
        </div>
        {/if}
        <div class="a4-field a4-field-wide">
            <label class="a4-label" for="migration-editor-yaml">{'YAML'|i18n( 'design/admin/migration' )}</label>
            <textarea class="a4-input a4-setup-editor-text" id="migration-editor-yaml" name="MigrationText" rows="24" spellcheck="false" autocapitalize="off">{$text|wash}</textarea>
        </div>
    </div>
    <div class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn" name="CheckButton" value="1">{'Check'|i18n( 'design/admin/migration' )}</button>
        {if or( not( $migration ), and( not( $migration.applied ), $migration.writable ) )}
        <button type="submit" class="a4-btn a4-btn-primary" name="SaveButton" value="1">{'Save'|i18n( 'design/admin/migration' )}</button>
        {/if}
    </div>
</section>

<section class="a4-card is-flush a4-section" aria-labelledby="migration-editor-steps">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-editor-steps">{'Steps'|i18n( 'design/admin/migration' )}</h2></div>
    {if $check.steps|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-editor-steps" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">#</th>
            <th scope="col">{'Step'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Keys'|i18n( 'design/admin/migration' )}</th>
            <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/migration' )}</span></th>
        </tr></thead>
        <tbody>
        {foreach $check.steps as $s}
        <tr>
            <td class="a4-nowrap">{if eq( $s.list, 'down' )}down {/if}{sum( $s.index, 1 )}</td>
            <td><code class="a4-code">{$s.type|wash}{if $s.mode}/{$s.mode|wash}{/if}</code></td>
            <td>{$s.summary|wash}</td>
            <td class="a4-nowrap"><span class="a4-actions">
                {if $s.form}<button type="submit" class="a4-btn a4-btn-small" name="EditStepButton" value="{$s.list|wash}:{$s.index}">{'Edit'|i18n( 'design/admin/migration' )}</button>{/if}
                <button type="submit" class="a4-btn a4-btn-small a4-btn-ghost" name="MoveUpButton" value="{$s.list|wash}:{$s.index}" aria-label="{'Move up'|i18n( 'design/admin/migration' )|wash}" title="{'Move up'|i18n( 'design/admin/migration' )|wash}"><i class="a4-bi-arrow-up" aria-hidden="true"></i></button>
                <button type="submit" class="a4-btn a4-btn-small a4-btn-ghost" name="MoveDownButton" value="{$s.list|wash}:{$s.index}" aria-label="{'Move down'|i18n( 'design/admin/migration' )|wash}" title="{'Move down'|i18n( 'design/admin/migration' )|wash}"><i class="a4-bi-arrow-down" aria-hidden="true"></i></button>
                <button type="submit" class="a4-btn a4-btn-small a4-btn-outline-danger" name="RemoveStepButton" value="{$s.list|wash}:{$s.index}">{'Remove'|i18n( 'design/admin/migration' )}</button>
            </span></td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'No steps yet: add one with the form, or write them in the text.'|i18n( 'design/admin/migration' )}</p></div>
    {/if}
</section>

<section class="a4-card a4-section" aria-labelledby="migration-editor-builder">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-editor-builder">{if ne( $builder_index, '' )}{'Change step %number'|i18n( 'design/admin/migration',, hash( '%number', sum( $builder_index, 1 ) ) )|wash}{else}{'Add a step'|i18n( 'design/admin/migration' )}{/if}</h2>
        <p class="a4-meta">{'The form of a step is made from its step type: its keys, what each takes and the modes it belongs to.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body a4-stack">
        <div class="a4-row">
            <div class="a4-field">
                <label class="a4-label" for="migration-editor-type">{'Step type'|i18n( 'design/admin/migration' )}</label>
                <select class="a4-input" id="migration-editor-type" name="BuilderType">
                    <option value="">–</option>
                {foreach $types as $t}<option value="{$t.name|wash}"{if and( $builder, eq( $builder.name, $t.name ) )} selected="selected"{/if}>{$t.name|wash}</option>{/foreach}
                </select>
            </div>
            <div class="a4-field a4-end">
                <button type="submit" class="a4-btn" name="ChooseTypeButton" value="1">{'Show its form'|i18n( 'design/admin/migration' )}</button>
            </div>
        </div>
        {if $builder}
        <input type="hidden" name="BuilderIndex" value="{$builder_index|wash}" />
        {if $builder.description}<p>{$builder.description|wash}</p>{/if}
        {if $builder_fields.unknown|count}<div class="a4-notice is-warn" role="status"><p>{'The step has keys its type does not know; they are left out when the step is changed: %keys'|i18n( 'design/admin/migration',, hash( '%keys', $builder_fields.unknown|implode( ', ' ) ) )|wash}</p></div>{/if}
        <div class="a4-row">
            {if $builder.modes|count}
            <div class="a4-field">
                <label class="a4-label" for="migration-editor-mode">{'Mode'|i18n( 'design/admin/migration' )}</label>
                <select class="a4-input" id="migration-editor-mode" name="BuilderMode">
                {foreach $builder.modes as $m}<option value="{$m|wash}"{if or( eq( $m, $builder_fields.mode ), and( eq( $builder_fields.mode, '' ), eq( $m, $builder.mode ) ) )} selected="selected"{/if}>{$m|wash}</option>{/foreach}
                </select>
            </div>
            {/if}
            <div class="a4-field">
                <label class="a4-label" for="migration-editor-list">{'In'|i18n( 'design/admin/migration' )}</label>
                <select class="a4-input" id="migration-editor-list" name="BuilderList">
                    <option value="steps"{if eq( $builder_list, 'steps' )} selected="selected"{/if}>steps</option>
                    <option value="down"{if eq( $builder_list, 'down' )} selected="selected"{/if}>down</option>
                </select>
            </div>
        </div>
        {foreach $builder.keys as $k}
        {def $value = cond( is_set( $builder_fields.keys[$k.name] ), $builder_fields.keys[$k.name], '' )}
        <div class="a4-field a4-field-wide">
            <label class="a4-label" for="migration-editor-key-{$k.name|wash}">{$k.name|wash}{if $k.required} <span class="a4-required-mark" aria-hidden="true">*</span>{/if}
                <span class="a4-meta">{$k.type|wash}{if $k.kind} ({$k.kind|wash}){/if}{if $k.modes|count} · {'only in %modes'|i18n( 'design/admin/migration',, hash( '%modes', $k.modes|implode( ', ' ) ) )|wash}{/if}</span></label>
            {if $k.values|count}
            <select class="a4-input" id="migration-editor-key-{$k.name|wash}" name="BuilderKey[{$k.name|wash}]">
                <option value="">{if $k.default}{'Default: %value'|i18n( 'design/admin/migration',, hash( '%value', $k.default ) )|wash}{else}–{/if}</option>
            {foreach $k.values as $v}<option value="{$v|wash}"{if eq( $v, $value )} selected="selected"{/if}>{$v|wash}</option>{/foreach}
            </select>
            {elseif eq( $k.type, 'bool' )}
            <select class="a4-input" id="migration-editor-key-{$k.name|wash}" name="BuilderKey[{$k.name|wash}]">
                <option value="">{if $k.default}{'Default: %value'|i18n( 'design/admin/migration',, hash( '%value', $k.default ) )|wash}{else}–{/if}</option>
                <option value="true"{if eq( $value, 'true' )} selected="selected"{/if}>true</option>
                <option value="false"{if eq( $value, 'false' )} selected="selected"{/if}>false</option>
            </select>
            {elseif $k.structured}
            <textarea class="a4-input a4-setup-editor-text" id="migration-editor-key-{$k.name|wash}" name="BuilderKey[{$k.name|wash}]" rows="4" spellcheck="false" placeholder="{$k.default|wash}">{$value|wash}</textarea>
            {else}
            <input class="a4-input" id="migration-editor-key-{$k.name|wash}" name="BuilderKey[{$k.name|wash}]" value="{$value|wash}" placeholder="{$k.default|wash}" />
            {/if}
            {if $k.description}<p class="a4-help">{$k.description|wash}</p>{/if}
        </div>
        {undef $value}
        {/foreach}
        <details class="a4-fold">
            <summary>{'Conditions, references and comment'|i18n( 'design/admin/migration' )}</summary>
            <div class="a4-fold-body a4-stack">
                <div class="a4-field a4-field-wide">
                    <label class="a4-label" for="migration-editor-if">if</label>
                    <textarea class="a4-input a4-setup-editor-text" id="migration-editor-if" name="BuilderIf" rows="2" spellcheck="false" placeholder="{ldelim} ref: news_parent, set: true {rdelim}">{$builder_fields.if|wash}</textarea>
                </div>
                <div class="a4-field a4-field-wide">
                    <label class="a4-label" for="migration-editor-references">references</label>
                    <input class="a4-input" id="migration-editor-references" name="BuilderReferences" value="{$builder_fields.references|wash}" placeholder="folder={$builder.values|implode( ', ' )|wash}" />
                    <p class="a4-help">{'Name=value pairs separated by commas. The values of this step: %values'|i18n( 'design/admin/migration',, hash( '%values', cond( $builder.values|count, $builder.values|implode( ', ' ), '–' ) ) )|wash}</p>
                </div>
                <div class="a4-row">
                    <div class="a4-field">
                        <label class="a4-label" for="migration-editor-environments">environments</label>
                        <input class="a4-input" id="migration-editor-environments" name="BuilderEnvironments" value="{$builder_fields.environments|wash}" placeholder="staging, production" />
                    </div>
                    <div class="a4-field">
                        <label class="a4-label" for="migration-editor-engines">engines</label>
                        <input class="a4-input" id="migration-editor-engines" name="BuilderEngines" value="{$builder_fields.engines|wash}" placeholder="mysql, postgresql" />
                    </div>
                </div>
                <div class="a4-field a4-field-wide">
                    <label class="a4-label" for="migration-editor-comment">comment</label>
                    <input class="a4-input" id="migration-editor-comment" name="BuilderComment" value="{$builder_fields.comment|wash}" />
                </div>
            </div>
        </details>
        {if $builder.example}<details class="a4-fold"><summary>{'Example'|i18n( 'design/admin/migration' )}</summary><pre class="a4-pre">{$builder.example|wash}</pre></details>{/if}
        {/if}
    </div>
    {if $builder}
    <div class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn a4-btn-primary" name="AddStepButton" value="1">{if ne( $builder_index, '' )}{'Change step'|i18n( 'design/admin/migration' )}{else}{'Add step'|i18n( 'design/admin/migration' )}{/if}</button>
    </div>
    {/if}
</section>

</div>
<div class="a4-stack">

<section class="a4-card a4-section{if $check.errors|count} is-attention{/if}" aria-labelledby="migration-editor-check">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-editor-check">{'Check'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'What exp:migration:check says: the header, the document and every step checked by its step type.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body a4-stack">
        {if $check.ok}<p class="a4-ok-text">{'The migration passes the check.'|i18n( 'design/admin/migration' )}</p>{/if}
        {if $check.errors|count}<ul>{foreach $check.errors as $e}<li class="a4-bad-text">{$e|wash}</li>{/foreach}</ul>{/if}
        {if $check.warnings|count}<ul>{foreach $check.warnings as $w}<li class="a4-warn-text">{$w|wash}</li>{/foreach}</ul>{/if}
    </div>
</section>

<section class="a4-card a4-section" aria-labelledby="migration-editor-preview">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-editor-preview">{'Preview'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'The YAML as it will be saved.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body">
        <details class="a4-fold"{if $checked} open="open"{/if}><summary>{'Show the YAML'|i18n( 'design/admin/migration' )}</summary><pre class="a4-pre is-tall">{$text|wash}</pre></details>
    </div>
</section>

<section class="a4-card is-flush a4-section" aria-labelledby="migration-editor-files">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-editor-files">{'YAML migrations'|i18n( 'design/admin/migration' )}</h2></div>
    <div class="a4-card-body">
        {if $migration}<p><a class="a4-btn" href={'migration/editor'|ezurl}>{'New migration'|i18n( 'design/admin/migration' )}</a></p>{/if}
        {if $files|count}
        <ul class="a4-setup-editor-files">
        {foreach $files as $f}
            <li><a href={concat( 'migration/editor/', $f.id )|ezurl}><code class="a4-code">{$f.id|wash}</code></a>
                {if $f.applied}<span class="a4-tag">{'has run'|i18n( 'design/admin/migration' )}</span>{/if}
                {if ne( $f.source, 'site' )}<span class="a4-meta">{$f.source|wash}</span>{/if}
                {if $f.description}<br /><span class="a4-meta">{$f.description|wash}</span>{/if}</li>
        {/foreach}
        </ul>
        {else}
        <p class="a4-empty">{'There are no YAML migrations yet.'|i18n( 'design/admin/migration' )}</p>
        {/if}
    </div>
</section>

</div>
</div>
</form>

</div>
