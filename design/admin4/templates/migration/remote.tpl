{* The migration/remote view: the wizard that adds or changes a remote, in numbered steps on one page. Review checks the
   settings and shows the lines that will be written; Save writes them (and asks "Verify it's you"). Test connection
   tries the settings as entered, without saving them.
   Variables: is_new, id, settings (Environment, Transport, Host, Port, User, Path, Php, Credential, Groups,
   HostKeyFingerprint, AllowRootUser), groups_text, credential (kind, name, env), transports, environments,
   secret_names, errors, warnings, messages, review (bool), diff (the lines to be written), test
   (expMigrationRemoteAdmin::testConnection() after Test connection, else null), can_run, can_rollback, view. *}
<div class="a4-page a4-setup-migration" id="exp-migration-remote">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        {if $is_new}
        <h1 class="a4-pagehead-title context-title">{'New remote'|i18n( 'design/admin/migration' )}</h1>
        {else}
        <h1 class="a4-pagehead-title context-title">{'Remote %id'|i18n( 'design/admin/migration',, hash( '%id', $id ) )|wash}</h1>
        {/if}
        <p class="a4-pagehead-sub">{'Nothing is sent or run here: Save writes the settings to settings/override/migration.ini.append.php. The credential is a name; its value stays in Setup > Secrets or in the environment.'|i18n( 'design/admin/migration' )}</p>
    </div>
    <div class="a4-pagehead-actions"><a class="a4-btn" href={'exphelp/viewer/help/migrations/remotes-and-deploys.md'|ezurl}>{'Help'|i18n( 'design/admin/migration' )}</a></div>
</div>

{include uri='design:migration/parts/tabs.tpl' view=$view can_run=$can_run can_rollback=$can_rollback}

{foreach $messages as $m}<div class="a4-notice is-bad" role="alert"><p>{$m.text|wash}</p></div>{/foreach}
{if $errors|count}
<div class="a4-notice is-bad a4-error-summary" role="alert" tabindex="-1">
    <p class="a4-notice-title">{'Check these settings'|i18n( 'design/admin/migration' )}</p>
    <ul>{foreach $errors as $error}<li>{$error|wash}</li>{/foreach}</ul>
</div>
{/if}
{if $warnings|count}<div class="a4-notice is-warn" role="status"><ul class="a4-plain">{foreach $warnings as $warning}<li>{$warning|wash}</li>{/foreach}</ul></div>{/if}

{if $test}
<section class="a4-card a4-section{if $test.ok|not} is-bad{/if}" aria-labelledby="migration-remote-test">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-remote-test">{'Test connection'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'The settings as entered, not saved yet.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body a4-stack">{include uri='design:migration/parts/remote_test.tpl' test=$test}</div>
</section>
{/if}

<form method="post" action={concat( 'migration/remote/', cond( $is_new, 'new', $id ) )|ezurl} class="a4-form">
{if $review}
{* ---- the review: the settings travel as hidden fields ---- *}
{if $is_new}<input type="hidden" name="RemoteIdentifier" value="{$id|wash}" />{/if}
{foreach array( 'Environment', 'Transport', 'Host', 'Port', 'User', 'Path', 'Php', 'HostKeyFingerprint', 'AllowRootUser' ) as $name}<input type="hidden" name="Remote[{$name}]" value="{$settings[$name]|wash}" />{/foreach}
<input type="hidden" name="RemoteGroups" value="{$groups_text|wash}" />
<input type="hidden" name="RemoteCredentialKind" value="{$credential.kind|wash}" />
<input type="hidden" name="RemoteCredentialName" value="{$credential.name|wash}" />
<input type="hidden" name="RemoteCredentialEnv" value="{$credential.env|wash}" />
<section class="a4-card a4-section" aria-labelledby="migration-remote-review">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-remote-review">{'Review'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'The settings are valid. These lines will be written:'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body a4-stack">
        <dl class="a4-facts is-rows">
            <dt>{'Remote'|i18n( 'design/admin/migration' )}</dt><dd><code class="a4-code">{$id|wash}</code> {$settings.Environment|wash}</dd>
            <dt>{'Transport'|i18n( 'design/admin/migration' )}</dt><dd>{$settings.Transport|wash}</dd>
            <dt>{'Credential'|i18n( 'design/admin/migration' )}</dt><dd><code class="a4-code">{$settings.Credential|wash}</code> <span class="a4-meta">{'a name only; the value stays in Setup > Secrets'|i18n( 'design/admin/migration' )}</span></dd>
        </dl>
        <pre class="a4-pre is-tall" tabindex="0" aria-label="{'The lines that will be written'|i18n( 'design/admin/migration' )|wash}">{$diff|wash}</pre>
    </div>
    <div class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn" name="EditButton" value="1">{'Change'|i18n( 'design/admin/migration' )}</button>
        <button type="submit" class="a4-btn a4-btn-primary" name="SaveButton" value="1">{'Save'|i18n( 'design/admin/migration' )}</button>
        <a class="a4-btn a4-btn-ghost" href={'migration/remotes'|ezurl}>{'Cancel'|i18n( 'design/admin/migration' )}</a>
    </div>
</section>
{else}
<ol class="a4-steps" aria-label="{'Steps'|i18n( 'design/admin/migration' )|wash}">
    <li><a href="#remote-step-1">{'Remote'|i18n( 'design/admin/migration' )}</a></li>
    <li><a href="#remote-step-2">{'Connection'|i18n( 'design/admin/migration' )}</a></li>
    <li><a href="#remote-step-3">{'Credential'|i18n( 'design/admin/migration' )}</a></li>
    <li><a href="#remote-step-4">{'Host key'|i18n( 'design/admin/migration' )}</a></li>
    <li><a href="#remote-step-5">{'Review'|i18n( 'design/admin/migration' )}</a></li>
</ol>

<section class="a4-card a4-section" id="remote-step-1" aria-labelledby="remote-step-1-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="remote-step-1-title">{'1. The remote'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'What the remote is called and which environment it is.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body a4-stack">
        {if $is_new}
        <div class="a4-field">
            <label class="a4-label" for="remote-id">{'Id'|i18n( 'design/admin/migration' )} <span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input class="a4-input" id="remote-id" name="RemoteIdentifier" value="{$id|wash}" maxlength="64" autocomplete="off" spellcheck="false" aria-describedby="remote-id-help" />
            <span class="a4-help" id="remote-id-help">{'Letters, digits, - and _, for example production1. It names the remote in reports and on the console (--remotes=production1) and cannot be changed later.'|i18n( 'design/admin/migration' )}</span>
        </div>
        {else}
        <p>{'Id'|i18n( 'design/admin/migration' )}: <code class="a4-code">{$id|wash}</code></p>
        {/if}
        <div class="a4-field">
            <label class="a4-label" for="remote-environment">{'Environment'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="remote-environment" name="Remote[Environment]" value="{$settings.Environment|wash}" maxlength="50" list="remote-environments" autocomplete="off" />
            <datalist id="remote-environments">{foreach $environments as $e}<option value="{$e|wash}"></option>{/foreach}</datalist>
            <span class="a4-help">{'alpha, dev, stage, beta, production ... A staged deploy goes through the environments in this order.'|i18n( 'design/admin/migration' )}</span>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="remote-groups">{'Groups'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="remote-groups" name="RemoteGroups" value="{$groups_text|wash}" maxlength="500" autocomplete="off" />
            <span class="a4-help">{'Optional, separated by commas, for example production. A deploy to a group goes to every remote of it.'|i18n( 'design/admin/migration' )}</span>
        </div>
    </div>
</section>

<section class="a4-card a4-section" id="remote-step-2" aria-labelledby="remote-step-2-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="remote-step-2-title">{'2. The connection'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'How the files get there, and where the installation is.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body a4-stack">
        <fieldset class="a4-field">
            <legend>{'Transport'|i18n( 'design/admin/migration' )}</legend>
            <ul class="a4-radios">
            {foreach $transports as $t}
                <li><label><input type="radio" name="Remote[Transport]" value="{$t|wash}"{if eq( $settings.Transport, $t )} checked="checked"{/if} /> <span><strong>{$t|wash}</strong>
                    <span class="a4-help">{switch match=$t}
                    {case match='rsync'}{'Over SSH, only what changed, a broken transfer resumed. The best choice where rsync is installed.'|i18n( 'design/admin/migration' )}{/case}
                    {case match='ssh'}{'SSH with scp for the files.'|i18n( 'design/admin/migration' )}{/case}
                    {case match='sftp'}{'SSH with sftp for the files.'|i18n( 'design/admin/migration' )}{/case}
                    {case match='ftp'}{'Files only: the migrations are sent and read back, but cannot be run there.'|i18n( 'design/admin/migration' )}{/case}
                    {case match='ftps'}{'FTP over TLS; files only.'|i18n( 'design/admin/migration' )}{/case}
                    {case match='local'}{'Another installation on this server, in a folder.'|i18n( 'design/admin/migration' )}{/case}
                    {case}{/case}
                    {/switch}</span></span></label></li>
            {/foreach}
            </ul>
        </fieldset>
        <div class="a4-field">
            <label class="a4-label" for="remote-host">{'Host'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="remote-host" name="Remote[Host]" value="{$settings.Host|wash}" maxlength="253" autocomplete="off" spellcheck="false" />
            <span class="a4-help">{'A host name or address, for example node1.example.com. Not for local.'|i18n( 'design/admin/migration' )}</span>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="remote-port">{'Port'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="remote-port" name="Remote[Port]" value="{$settings.Port|wash}" inputmode="numeric" maxlength="5" autocomplete="off" />
            <span class="a4-help">{'Empty: 22 for SSH, 21 for FTP.'|i18n( 'design/admin/migration' )}</span>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="remote-user">{'User'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="remote-user" name="Remote[User]" value="{$settings.User|wash}" maxlength="64" autocomplete="off" spellcheck="false" />
        </div>
        <div class="a4-field">
            <label class="a4-label" for="remote-path">{'Path'|i18n( 'design/admin/migration' )} <span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input class="a4-input" id="remote-path" name="Remote[Path]" value="{$settings.Path|wash}" maxlength="500" autocomplete="off" spellcheck="false" />
            <span class="a4-help">{'The installation\'s root there, for example /var/www/site (for FTP: the path the FTP account sees).'|i18n( 'design/admin/migration' )}</span>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="remote-php">{'PHP command line'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="remote-php" name="Remote[Php]" value="{$settings.Php|wash}" maxlength="255" autocomplete="off" spellcheck="false" />
            <span class="a4-help">{'The PHP CLI there: php, or a full path such as /opt/plesk/php/8.4/bin/php.'|i18n( 'design/admin/migration' )}</span>
        </div>
        <label class="a4-check"><input type="checkbox" name="Remote[AllowRootUser]" value="enabled"{if eq( $settings.AllowRootUser, 'enabled' )} checked="checked"{/if} />
            <span><strong>{'The console scripts run as root there'|i18n( 'design/admin/migration' )}</strong><span class="a4-help">{'Only when the remote\'s user is root (they are then run with --allow-root-user).'|i18n( 'design/admin/migration' )}</span></span></label>
    </div>
</section>

<section class="a4-card a4-section" id="remote-step-3" aria-labelledby="remote-step-3-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="remote-step-3-title">{'3. The credential'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'How this installation signs in there. Only a name is saved.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body a4-stack">
        <fieldset class="a4-field">
            <legend>{'Credential'|i18n( 'design/admin/migration' )}</legend>
            <ul class="a4-radios">
                <li><label><input type="radio" name="RemoteCredentialKind" value="none"{if eq( $credential.kind, 'none' )} checked="checked"{/if} /> <span><strong>{'None'|i18n( 'design/admin/migration' )}</strong> <span class="a4-help">{'The SSH agent and keys of the user this installation runs as (and always for local).'|i18n( 'design/admin/migration' )}</span></span></label></li>
                <li><label><input type="radio" name="RemoteCredentialKind" value="sshkey"{if eq( $credential.kind, 'sshkey' )} checked="checked"{/if} /> <span><strong>{'SSH key'|i18n( 'design/admin/migration' )}</strong> <span class="a4-help">{'A private key kept as a secret.'|i18n( 'design/admin/migration' )}</span></span></label></li>
                <li><label><input type="radio" name="RemoteCredentialKind" value="password"{if eq( $credential.kind, 'password' )} checked="checked"{/if} /> <span><strong>{'Password'|i18n( 'design/admin/migration' )}</strong> <span class="a4-help">{'A password kept as a secret (FTP signs in with one).'|i18n( 'design/admin/migration' )}</span></span></label></li>
            </ul>
        </fieldset>
        <div class="a4-field">
            <label class="a4-label" for="remote-secret">{'Secret name'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="remote-secret" name="RemoteCredentialName" value="{$credential.name|wash}" maxlength="150" list="remote-secrets" autocomplete="off" spellcheck="false" />
            <datalist id="remote-secrets">{foreach $secret_names as $s}<option value="{$s|wash}"></option>{/foreach}</datalist>
            <span class="a4-help">{'A secret of Setup > Secrets, for example deploy_production1.'|i18n( 'design/admin/migration' )} <a href={'secrets/list'|ezurl}>{'Setup > Secrets'|i18n( 'design/admin/migration' )}</a></span>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="remote-env">{'Or an environment variable'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="remote-env" name="RemoteCredentialEnv" value="{$credential.env|wash}" maxlength="100" autocomplete="off" spellcheck="false" />
            <span class="a4-help">{'The variable of the server process that holds it, for example DEPLOY_KEY (saved as env:DEPLOY_KEY). It takes the place of the secret name.'|i18n( 'design/admin/migration' )}</span>
        </div>
    </div>
</section>

<section class="a4-card a4-section" id="remote-step-4" aria-labelledby="remote-step-4-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="remote-step-4-title">{'4. The host key'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'Optional, for SSH, sftp and rsync.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body a4-stack">
        <div class="a4-field a4-field-wide">
            <label class="a4-label" for="remote-fingerprint">{'Host key fingerprint'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="remote-fingerprint" name="Remote[HostKeyFingerprint]" value="{$settings.HostKeyFingerprint|wash}" maxlength="60" autocomplete="off" spellcheck="false" />
            <span class="a4-help">{'SHA256:... as ssh-keygen -l prints it. With it, the host must show this key; without it, the key is learnt on first contact.'|i18n( 'design/admin/migration' )}</span>
        </div>
    </div>
</section>

<section class="a4-card a4-section" id="remote-step-5" aria-labelledby="remote-step-5-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="remote-step-5-title">{'5. Review'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'Test the connection with the settings above, then review the lines that will be written.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn" name="TestButton" value="1">{'Test connection'|i18n( 'design/admin/migration' )}</button>
        <button type="submit" class="a4-btn a4-btn-primary" name="ReviewButton" value="1">{'Review'|i18n( 'design/admin/migration' )}</button>
        <a class="a4-btn a4-btn-ghost" href={'migration/remotes'|ezurl}>{'Cancel'|i18n( 'design/admin/migration' )}</a>
    </div>
</section>
{/if}
</form>

{if and( $is_new|not, $review|not )}
<form method="post" action={concat( 'migration/remote/', $id )|ezurl} class="a4-form">
<section class="a4-card a4-section is-attention" aria-labelledby="remote-remove">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="remote-remove">{'Remove this remote'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'Its settings are taken out of settings/override/migration.ini.append.php. Nothing changes on the remote, and its deploys stay in the history.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-foot a4-actions">
        <label class="a4-check"><input type="checkbox" name="ConfirmRemove" value="1" /> <span>{'Yes, remove this remote'|i18n( 'design/admin/migration' )}</span></label>
        <button type="submit" class="a4-btn" name="RemoveButton" value="1">{'Remove'|i18n( 'design/admin/migration' )}</button>
    </div>
</section>
</form>
{/if}

</div>
