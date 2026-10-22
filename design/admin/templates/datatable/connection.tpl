{* The datatable/connection view: make, change, test or remove one named connection.
   Variables: connection (descriptor), is_new, editable, installed, problems, messages, test (ok, engine, via, version,
   tables, ms, error) or null, engines, secret_names, secret_set, may_store_secrets, sqlite_directories, classes. *}
<div class="a4-page a4-datatable" id="exp-datatable-connection">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <p class="a4-pagehead-pre"><a href={'datatable/connections'|ezurl}>{'Connections'|i18n( 'design/admin/datatable' )}</a></p>
        <h1 class="a4-pagehead-title context-title">{if $is_new}{'New connection'|i18n( 'design/admin/datatable' )}{else}{$connection.name|wash}{/if}</h1>
        {if $is_new|not}<p class="a4-pagehead-sub"><code class="a4-code">{$connection.identifier|wash}</code></p>{/if}
    </div>
</div>

{foreach $messages as $m}
<div class="a4-notice is-{if eq( $m.kind, 'ok' )}ok{elseif eq( $m.kind, 'warn' )}warn{else}bad{/if}" role="{if eq( $m.kind, 'bad' )}alert{else}status{/if}"><p>{$m.text|wash}</p></div>
{/foreach}

{if $problems|count}
<div class="a4-error-summary a4-notice is-bad" role="alert">
    <p class="a4-notice-title">{'The connection was not saved'|i18n( 'design/admin/datatable' )}</p>
    <ul>{foreach $problems as $p}<li>{$p|wash}</li>{/foreach}</ul>
</div>
{/if}

{if $test}
{if $test.ok}
<div class="a4-notice is-ok" role="status"><p>{'Connected through %via in %ms ms: %version, %tables tables.'|i18n( 'design/admin/datatable',, hash( '%via', $test.via, '%ms', $test.ms, '%version', $test.version, '%tables', cond( is_null( $test.tables ), '?', $test.tables ) ) )|wash}</p></div>
{else}
<div class="a4-notice is-bad" role="alert"><p>{'The connection failed after %ms ms: %error'|i18n( 'design/admin/datatable',, hash( '%ms', $test.ms, '%error', $test.error ) )|wash}</p></div>
{/if}
{/if}

{if and( $editable, $installed|not )}
<div class="a4-notice is-warn" role="status"><p>{'The table exp_data_connection is missing: a connection can be tested but not saved until it is created on the Data tables page.'|i18n( 'design/admin/datatable' )}</p></div>
{/if}

<form method="post" action={concat( 'datatable/connection/', cond( $is_new, 'new', $connection.identifier ) )|ezurl} class="a4-form">
<div class="a4-card a4-section">
<div class="a4-card-body a4-stack">

{if $editable|not}
<p class="a4-meta">{if eq( $connection.origin, 'site' )}{'This installation\'s own database, from site.ini. It can be tested here.'|i18n( 'design/admin/datatable' )}{else}{'Declared in datatable.ini [Connection_%id] by the settings or an extension: change it there. It can be tested here.'|i18n( 'design/admin/datatable',, hash( '%id', $connection.identifier ) )|wash}{/if}</p>
<dl class="a4-facts is-rows">
    <dt>{'Engine'|i18n( 'design/admin/datatable' )}</dt><dd>{$connection.engine|wash}</dd>
    {if $connection.server}<dt>{'Server'|i18n( 'design/admin/datatable' )}</dt><dd>{$connection.server|wash}{if $connection.port}:{$connection.port}{/if}</dd>{/if}
    <dt>{'Database'|i18n( 'design/admin/datatable' )}</dt><dd><code class="a4-code">{$connection.database|wash}</code></dd>
    {if $connection.user}<dt>{'User'|i18n( 'design/admin/datatable' )}</dt><dd>{$connection.user|wash}</dd>{/if}
    {if $connection.secret}<dt>{'Password'|i18n( 'design/admin/datatable' )}</dt><dd><code class="a4-code">{$connection.secret|wash}</code> {if $secret_set}<span class="a4-badge is-ok">{'set'|i18n( 'design/admin/datatable' )}</span>{else}<span class="a4-badge is-bad">{'not set'|i18n( 'design/admin/datatable' )}</span>{/if}</dd>{/if}
</dl>
{else}

<div class="a4-grid">
    <div class="a4-field">
        <label class="a4-label" for="dt-identifier">{'Identifier'|i18n( 'design/admin/datatable' )} <span class="a4-required-mark" aria-hidden="true">*</span></label>
        {if $is_new}
        <input class="a4-input" id="dt-identifier" name="Identifier" required="required" pattern="[a-z][a-z0-9_]{ldelim}0,63{rdelim}" maxlength="64" value="{$connection.identifier|wash}" aria-describedby="dt-identifier-help" />
        <p class="a4-help" id="dt-identifier-help">{'What classes and tools name it: lower case letters, digits and _. It cannot be changed later.'|i18n( 'design/admin/datatable' )}</p>
        {else}
        <input class="a4-input" id="dt-identifier" value="{$connection.identifier|wash}" readonly="readonly" />
        {/if}
    </div>
    <div class="a4-field">
        <label class="a4-label" for="dt-name">{'Name'|i18n( 'design/admin/datatable' )}</label>
        <input class="a4-input" id="dt-name" name="Name" maxlength="255" value="{$connection.name|wash}" />
    </div>
    <div class="a4-field">
        <label class="a4-label" for="dt-engine">{'Engine'|i18n( 'design/admin/datatable' )}</label>
        <select class="a4-input" id="dt-engine" name="Engine">
        {foreach $engines as $e}
            <option value="{$e.key|wash}"{if eq( $e.key, $connection.engine )} selected="selected"{/if}{if $e.available|not} disabled="disabled"{/if}>{$e.label|wash}{if $e.available|not} - {'not available here'|i18n( 'design/admin/datatable' )}{elseif $e.untested} - {'untested here'|i18n( 'design/admin/datatable' )}{/if}</option>
        {/foreach}
        </select>
    </div>
</div>

<div class="a4-grid">
    <div class="a4-field">
        <label class="a4-label" for="dt-server">{'Server'|i18n( 'design/admin/datatable' )}</label>
        <input class="a4-input" id="dt-server" name="Server" maxlength="253" value="{$connection.server|wash}" aria-describedby="dt-server-help" />
        <p class="a4-help" id="dt-server-help">{'A host name or address; empty for SQLite.'|i18n( 'design/admin/datatable' )}</p>
    </div>
    <div class="a4-field">
        <label class="a4-label" for="dt-port">{'Port'|i18n( 'design/admin/datatable' )}</label>
        <input class="a4-input is-auto" id="dt-port" name="Port" type="number" min="0" max="65535" value="{if $connection.port}{$connection.port}{/if}" aria-describedby="dt-port-help" />
        <p class="a4-help" id="dt-port-help">{'Empty for the engine\'s usual port.'|i18n( 'design/admin/datatable' )}</p>
    </div>
    <div class="a4-field">
        <label class="a4-label" for="dt-database">{'Database'|i18n( 'design/admin/datatable' )}</label>
        <input class="a4-input" id="dt-database" name="Database" maxlength="255" value="{$connection.database|wash}" aria-describedby="dt-database-help" />
        <p class="a4-help" id="dt-database-help">{'For SQLite the file, e.g. %example (in %dirs). For Oracle the service name.'|i18n( 'design/admin/datatable',, hash( '%example', concat( $sqlite_directories.0, '/archive.sqlite' ), '%dirs', $sqlite_directories|implode( ', ' ) ) )|wash}</p>
    </div>
</div>

<div class="a4-grid">
    <div class="a4-field">
        <label class="a4-label" for="dt-user">{'User'|i18n( 'design/admin/datatable' )}</label>
        <input class="a4-input" id="dt-user" name="User" maxlength="255" autocomplete="off" value="{$connection.user|wash}" />
    </div>
    <div class="a4-field">
        <label class="a4-label" for="dt-secret">{'Password: the name of a secret'|i18n( 'design/admin/datatable' )}</label>
        <input class="a4-input" id="dt-secret" name="Secret" maxlength="128" list="dt-secret-names" value="{$connection.secret|wash}" aria-describedby="dt-secret-help" />
        <datalist id="dt-secret-names">{foreach $secret_names as $n}<option value="{$n|wash}"></option>{/foreach}</datalist>
        <p class="a4-help" id="dt-secret-help">{'A secret of Setup > Secrets, or env:VARIABLE for an environment variable.'|i18n( 'design/admin/datatable' )}{if and( $connection.secret, $secret_set|not )} <span class="a4-bad-text">{'This secret is not set.'|i18n( 'design/admin/datatable' )}</span>{/if}</p>
    </div>
    <div class="a4-field">
        <label class="a4-label" for="dt-password">{'Password itself'|i18n( 'design/admin/datatable' )}</label>
        <input class="a4-input" id="dt-password" name="Password" type="password" autocomplete="new-password" value="" aria-describedby="dt-password-help" />
        <p class="a4-help" id="dt-password-help">{'Used for a test only and never shown again.'|i18n( 'design/admin/datatable' )}{if $may_store_secrets} {'Tick the box below to store it, encrypted, as the secret named above (datatable. and the identifier when the name is empty).'|i18n( 'design/admin/datatable' )|wash}{/if}</p>
        {if $may_store_secrets}<label class="a4-check"><input type="checkbox" name="StoreSecret" value="1" /> {'Store it in the secret store when saving'|i18n( 'design/admin/datatable' )}</label>{/if}
    </div>
</div>

<details class="a4-fold">
    <summary>{'More'|i18n( 'design/admin/datatable' )}</summary>
    <div class="a4-fold-body a4-grid">
        <div class="a4-field">
            <label class="a4-label" for="dt-charset">{'Character set'|i18n( 'design/admin/datatable' )}</label>
            <input class="a4-input" id="dt-charset" name="OptionCharset" maxlength="32" value="{if is_set( $connection.options.charset )}{$connection.options.charset|wash}{/if}" />
        </div>
        <div class="a4-field">
            <label class="a4-label" for="dt-socket">{'Socket'|i18n( 'design/admin/datatable' )}</label>
            <input class="a4-input" id="dt-socket" name="OptionSocket" maxlength="255" value="{if is_set( $connection.options.socket )}{$connection.options.socket|wash}{/if}" />
        </div>
        <div class="a4-field">
            <label class="a4-label" for="dt-driver">{'PDO driver'|i18n( 'design/admin/datatable' )}</label>
            <input class="a4-input" id="dt-driver" name="OptionDriver" maxlength="32" value="{if is_set( $connection.options.driver )}{$connection.options.driver|wash}{/if}" aria-describedby="dt-driver-help" />
            <p class="a4-help" id="dt-driver-help">{'For SQL Server: sqlsrv, dblib or odbc (the first one this server has when empty).'|i18n( 'design/admin/datatable' )}</p>
        </div>
        <div class="a4-field a4-field-wide">
            <label class="a4-label" for="dt-dsn">{'PDO data source name'|i18n( 'design/admin/datatable' )}</label>
            <input class="a4-input" id="dt-dsn" name="OptionDSN" maxlength="1000" value="{if is_set( $connection.options.dsn )}{$connection.options.dsn|wash}{/if}" aria-describedby="dt-dsn-help" />
            <p class="a4-help" id="dt-dsn-help">{'Only for an engine reached through PDO, when server, port and database are not enough. Never a password.'|i18n( 'design/admin/datatable' )}</p>
        </div>
        <div class="a4-field a4-field-wide">
            <label class="a4-label" for="dt-description">{'Description'|i18n( 'design/admin/datatable' )}</label>
            <input class="a4-input" id="dt-description" name="Description" maxlength="255" value="{$connection.description|wash}" />
        </div>
        <label class="a4-check"><input type="checkbox" name="ReadOnly" value="1"{if $connection.read_only} checked="checked"{/if} /> {'Read only: the synchronisation never writes to it'|i18n( 'design/admin/datatable' )}</label>
    </div>
</details>
{/if}

{if $classes|count}
<p class="a4-meta">{'Classes on this connection: %list'|i18n( 'design/admin/datatable',, hash( '%list', $classes|implode( ', ' ) ) )|wash}</p>
{/if}
</div>

<div class="a4-card-foot a4-actions">
    {if $editable}<button type="submit" class="a4-btn a4-btn-primary" name="SaveButton" value="1">{'Save'|i18n( 'design/admin/datatable' )}</button>{/if}
    <button type="submit" class="a4-btn" name="TestButton" value="1">{'Test connection'|i18n( 'design/admin/datatable' )}</button>
    <button type="submit" class="a4-btn a4-btn-ghost" name="CancelButton" value="1" formnovalidate="formnovalidate">{'Back to the list'|i18n( 'design/admin/datatable' )}</button>
</div>
</div>

{if and( $editable, $is_new|not )}
<details class="a4-confirm">
    <summary class="a4-btn a4-btn-outline-danger">{'Remove this connection'|i18n( 'design/admin/datatable' )}</summary>
    <div class="a4-fold-body is-warn">
        <label class="a4-check"><input type="checkbox" name="ConfirmRemove" value="1" /> {'Yes, remove it'|i18n( 'design/admin/datatable' )}</label>
        <button type="submit" class="a4-btn a4-btn-danger" name="RemoveButton" value="1" formnovalidate="formnovalidate">{'Remove'|i18n( 'design/admin/datatable' )}</button>
    </div>
</details>
{/if}
</form>

</div>
