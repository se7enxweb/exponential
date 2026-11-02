{* The staticpublish/target view: the wizard that makes or changes a target. One step at a time with
   page/staticpublish_wizard.js; without JavaScript every step shows at once. Review checks the settings and shows the
   lines that will be written; Save writes them (and asks "Verify it's you").
   Variables: target (expStaticAdmin::overview(), null for a new one), view, is_new, id, settings (every setting of the
   target), credential (kind, token), siteaccesses (name, url: the front siteaccesses), secret_names, key
   (expStaticAdmin::deployKey(), null for a new target), key_secret, extras, post_process, triggers, notify,
   link_relative, link_base, errors, warnings, messages, review (bool), diff (the lines to be written), enabled,
   enable (switch on when saving), site_limit, file_limit, github (expStaticAdmin::github()), github_report
   (expStaticGitHub::setupTarget() after "Set up on GitHub"), dns (expStaticDnsCheck::check() after "Check the DNS"),
   pages_a (GitHub Pages' A records), start_step (the step to open after one of those, 0 for the first). *}
<div class="a4-page a4-setup-publish" id="exp-staticpublish-target">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        {if $is_new}
        <h1 class="a4-pagehead-title context-title">{'New target'|i18n( 'design/admin/staticpublish' )}</h1>
        {else}
        <h1 class="a4-pagehead-title context-title">{'Settings of %name'|i18n( 'design/admin/staticpublish',, hash( '%name', $target.name ) )|wash}</h1>
        {/if}
        <p class="a4-pagehead-sub">{'A target is one site published to one git repository. Nothing is built or published here: Save writes the settings to settings/override/staticcache.ini.append.php.'|i18n( 'design/admin/staticpublish' )}</p>
    </div>
    <div class="a4-pagehead-actions"><a class="a4-btn" href={'exphelp/viewer/help/static-publishing/the-setup-wizard.md'|ezurl}>{'Help with the wizard'|i18n( 'design/admin/staticpublish' )}</a></div>
</div>

{if $target}{include uri='design:staticpublish/parts/tabs.tpl' target=$target view=$view}{/if}

{foreach $messages as $m}<div class="a4-notice is-{if eq( $m.kind, 'warn' )}warn{else}bad{/if}" role="alert"><p>{$m.text|wash}</p></div>{/foreach}
{if $errors|count}
<div class="a4-notice is-bad a4-error-summary" role="alert" tabindex="-1">
    <p class="a4-notice-title">{'Check these settings'|i18n( 'design/admin/staticpublish' )}</p>
    <ul>{foreach $errors as $error}<li>{$error|wash}</li>{/foreach}</ul>
</div>
{/if}
{if $warnings|count}
<div class="a4-notice is-warn" role="status"><ul class="a4-plain">{foreach $warnings as $warning}<li>{$warning|wash}</li>{/foreach}</ul></div>
{/if}

{if $review}
{* ---- the review ---------------------------------------------------------------------------------------------- *}
<form method="post" action={concat( 'staticpublish/target/', cond( $is_new, 'new', $id ) )|ezurl} class="a4-form">
{if $is_new}<input type="hidden" name="TargetIdentifier" value="{$id|wash}" />{/if}
{foreach $settings as $name => $value}
{if is_array( $value )}{foreach $value as $item}<input type="hidden" name="Target[{$name|wash}][]" value="{$item|wash}" />{/foreach}<input type="hidden" name="Target[{$name|wash}][]" value="" />
{else}<input type="hidden" name="Target[{$name|wash}]" value="{$value|wash}" />{/if}
{/foreach}
<section class="a4-card a4-section" aria-labelledby="sp-review">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-review">{'Review'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'The settings are valid. These lines will be written:'|i18n( 'design/admin/staticpublish' )}</p></div>
    <div class="a4-card-body a4-stack">
        <dl class="a4-facts is-rows">
            <dt>{'Target'|i18n( 'design/admin/staticpublish' )}</dt><dd><code class="a4-code">{$id|wash}</code> {$settings.Name|wash}</dd>
            <dt>{'Siteaccesses'|i18n( 'design/admin/staticpublish' )}</dt><dd>{include uri='design:staticpublish/parts/siteaccesses.tpl' members=$members}</dd>
            <dt>{'Repository'|i18n( 'design/admin/staticpublish' )}</dt><dd>{if $settings.Remote}<code class="a4-code">{$settings.Remote|wash}</code>{else}<span class="a4-muted">{'none: commits stay in the folder'|i18n( 'design/admin/staticpublish' )}</span>{/if}
                &middot; {$settings.Branch|wash}{if $settings.Folder} /{$settings.Folder|wash}{/if}</dd>
            <dt>{'Credential'|i18n( 'design/admin/staticpublish' )}</dt><dd><code class="a4-code">{$settings.Credential|wash}</code> <span class="a4-meta">{'a name only; the value stays in Setup > Secrets'|i18n( 'design/admin/staticpublish' )}</span></dd>
            <dt>{'Links'|i18n( 'design/admin/staticpublish' )}</dt><dd>{if eq( $settings.Links, 'base-path' )}{'below %path'|i18n( 'design/admin/staticpublish',, hash( '%path', $settings.BasePath ) )|wash}{else}{'relative'|i18n( 'design/admin/staticpublish' )}{/if}</dd>
        </dl>
        <pre class="a4-pre is-tall" tabindex="0" aria-label="{'The lines that will be written'|i18n( 'design/admin/staticpublish' )|wash}">{$diff|wash}</pre>
        {if $enabled|not}<label class="a4-check"><input type="checkbox" name="EnableOnSave" value="1"{if $enable} checked="checked"{/if} /> <span><strong>{'Switch static publishing on'|i18n( 'design/admin/staticpublish' )}</strong><span class="a4-help">{'It is switched off: nothing can be built until it is on.'|i18n( 'design/admin/staticpublish' )}</span></span></label>{/if}
    </div>
    <div class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn" name="EditButton" value="1">{'Change'|i18n( 'design/admin/staticpublish' )}</button>
        <button type="submit" class="a4-btn a4-btn-primary" name="SaveButton" value="1">{'Save'|i18n( 'design/admin/staticpublish' )}</button>
        <a class="a4-btn a4-btn-ghost" href={'staticpublish/dashboard'|ezurl}>{'Cancel'|i18n( 'design/admin/staticpublish' )}</a>
    </div>
</section>
</form>

{else}
{* ---- the wizard ---------------------------------------------------------------------------------------------- *}
<form method="post" action={concat( 'staticpublish/target/', cond( $is_new, 'new', $id ) )|ezurl} class="a4-form" id="sp-wizard"
      data-text-step="{'Step %step of %total'|i18n( 'design/admin/staticpublish' )|wash}" data-sp-start="{$start_step|wash}">
{if $enable}<input type="hidden" name="EnableOnSave" value="1" />{/if}
<input type="hidden" name="Target[Credential]" value="{$settings.Credential|wash}" />
<ol class="a4-steps" id="sp-wizard-steps" aria-label="{'Steps'|i18n( 'design/admin/staticpublish' )|wash}">
    <li><a href="#sp-step-1">{'Site'|i18n( 'design/admin/staticpublish' )}</a></li>
    <li><a href="#sp-step-2">{'Host'|i18n( 'design/admin/staticpublish' )}</a></li>
    <li><a href="#sp-step-3">{'Credential'|i18n( 'design/admin/staticpublish' )}</a></li>
    <li><a href="#sp-step-4">{'Repository'|i18n( 'design/admin/staticpublish' )}</a></li>
    <li><a href="#sp-step-5">{'Links'|i18n( 'design/admin/staticpublish' )}</a></li>
    <li><a href="#sp-step-6">{'Domain'|i18n( 'design/admin/staticpublish' )}</a></li>
    <li><a href="#sp-step-7">{'Output'|i18n( 'design/admin/staticpublish' )}</a></li>
    <li><a href="#sp-step-8">{'Triggers'|i18n( 'design/admin/staticpublish' )}</a></li>
    <li><a href="#sp-step-9">{'Review'|i18n( 'design/admin/staticpublish' )}</a></li>
</ol>

{* 1. the site *}
<section class="a4-card a4-section" id="sp-step-1" data-sp-step="1" aria-labelledby="sp-step-1-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-step-1-title">{'1. The site'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'Which site is published, and what the target is called.'|i18n( 'design/admin/staticpublish' )}</p></div>
    <div class="a4-card-body a4-stack">
        {if $is_new}
        <div class="a4-field">
            <label class="a4-label" for="sp-id">{'Id'|i18n( 'design/admin/staticpublish' )} <span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input class="a4-input" id="sp-id" name="TargetIdentifier" value="{$id|wash}" maxlength="50" autocomplete="off" spellcheck="false" aria-describedby="sp-id-help" />
            <span class="a4-help" id="sp-id-help">{'Lower case letters, digits, _ and -, for example github-public. It names the folder and the tags (static/<id>/<n>) and cannot be changed later.'|i18n( 'design/admin/staticpublish' )|wash}</span>
        </div>
        {else}
        <p>{'Id'|i18n( 'design/admin/staticpublish' )}: <code class="a4-code">{$id|wash}</code></p>
        {/if}
        <div class="a4-field">
            <label class="a4-label" for="sp-name">{'Name'|i18n( 'design/admin/staticpublish' )}</label>
            <input class="a4-input" id="sp-name" name="Target[Name]" value="{$settings.Name|wash}" maxlength="150" />
            <span class="a4-help">{'Shown in lists, for example "Public site on GitHub Pages".'|i18n( 'design/admin/staticpublish' )}</span>
        </div>
        <fieldset class="a4-field">
            <legend>{'Siteaccesses to publish'|i18n( 'design/admin/staticpublish' )}</legend>
            {if $siteaccesses|count}
            <input type="hidden" name="Target[SiteAccessList][]" value="" />
            <div class="a4-table-wrap" role="region" aria-label="{'Siteaccesses to publish'|i18n( 'design/admin/staticpublish' )|wash}" tabindex="0">
            <table class="a4-table a4-setup-sa-table">
                <thead><tr>
                    <th scope="col">{'Publish'|i18n( 'design/admin/staticpublish' )}</th>
                    <th scope="col">{'Siteaccess'|i18n( 'design/admin/staticpublish' )}</th>
                    <th scope="col">{'Folder'|i18n( 'design/admin/staticpublish' )}</th>
                    <th scope="col">{'At the root'|i18n( 'design/admin/staticpublish' )}</th>
                </tr></thead>
                <tbody>
                {foreach $siteaccesses as $sa}
                <tr>
                    <td><input type="checkbox" id="sp-sa-{$sa.name|wash}" name="Target[SiteAccessList][]" value="{$sa.name|wash}"{if $chosen|contains( $sa.name )} checked="checked"{/if} /></td>
                    <td><label for="sp-sa-{$sa.name|wash}"><strong>{$sa.name|wash}</strong>{if $sa.url} <span class="a4-meta">{$sa.url|wash}</span>{/if}</label></td>
                    <td>{if $sa.folder}<code class="a4-code">{$sa.folder|wash}</code>{else}<span class="a4-muted" aria-hidden="true">&ndash;</span>{/if}</td>
                    <td><input type="radio" name="Target[DefaultSiteAccess]" value="{$sa.name|wash}" aria-label="{'At the root'|i18n( 'design/admin/staticpublish' )|wash}: {$sa.name|wash}"{if eq( $chosen_default, $sa.name )} checked="checked"{/if} /></td>
                </tr>
                {/foreach}
                </tbody>
            </table>
            </div>
            <label class="a4-check"><input type="radio" name="Target[DefaultSiteAccess]" value=""{if eq( $chosen_default, '' )} checked="checked"{/if} /> <span>{'No siteaccess at the root: each is in its folder, and the root links to them'|i18n( 'design/admin/staticpublish' )}</span></label>
            {else}
            <p class="a4-warn-text">{'This installation has no front siteaccess. Administration and editing siteaccesses are never published.'|i18n( 'design/admin/staticpublish' )}</p>
            {/if}
            <span class="a4-help">{'Tick one siteaccess, or several that share one host by their URI prefix. The one at the root is served there, the others in the folder of their prefix (/ger/); links between them stay in the copy. Administration siteaccesses are never offered.'|i18n( 'design/admin/staticpublish' )}</span>
        </fieldset>
    </div>
    <div class="a4-card-foot a4-actions" data-sp-nav="1" hidden="hidden"><button type="button" class="a4-btn a4-btn-primary" data-sp-next="2">{'Next'|i18n( 'design/admin/staticpublish' )}</button><a class="a4-btn a4-btn-ghost" href={'staticpublish/dashboard'|ezurl}>{'Cancel'|i18n( 'design/admin/staticpublish' )}</a></div>
</section>

{* 2. the host *}
<section class="a4-card a4-section" id="sp-step-2" data-sp-step="2" aria-labelledby="sp-step-2-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-step-2-title">{'2. The host'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'Where the static copy is served from.'|i18n( 'design/admin/staticpublish' )}</p></div>
    <div class="a4-card-body a4-stack">
        <fieldset class="a4-field">
            <legend>{'Host'|i18n( 'design/admin/staticpublish' )}</legend>
            <ul class="a4-radios">
                <li><label><input type="radio" name="Target[Kind]" value="github-pages"{if eq( $settings.Kind, 'github-pages' )} checked="checked"{/if} /> <span><strong>{'GitHub Pages'|i18n( 'design/admin/staticpublish' )}</strong> <span class="a4-help">{'Free hosting from a GitHub repository.'|i18n( 'design/admin/staticpublish' )}</span></span></label></li>
                <li><label><input type="radio" name="Target[Kind]" value="git"{if eq( $settings.Kind, 'git' )} checked="checked"{/if} /> <span><strong>{'Another git host'|i18n( 'design/admin/staticpublish' )}</strong> <span class="a4-help">{'GitLab, Gitea, your own server: any git repository.'|i18n( 'design/admin/staticpublish' )}</span></span></label></li>
            </ul>
        </fieldset>
        <div class="a4-notice is-info">
            <p class="a4-notice-title">{'GitHub Pages limits'|i18n( 'design/admin/staticpublish' )}</p>
            <p>{'A site may be up to %site, one file up to %file, with about 100 GB of traffic a month. On a free plan the repository must be public. GitHub Pages is not meant for shops or other commercial transactions.'|i18n( 'design/admin/staticpublish',, hash( '%site', $site_limit|si( 'byte' ), '%file', $file_limit|si( 'byte' ) ) )|wash}</p>
            <p>{'Every push is checked against these limits first.'|i18n( 'design/admin/staticpublish' )} <a href={'exphelp/viewer/help/static-publishing/github-pages-limits-and-terms.md'|ezurl}>{'Limits and terms'|i18n( 'design/admin/staticpublish' )}</a></p>
        </div>
        {if $github.available}
        <p class="a4-help">{'With a GitHub repository and a token or GitHub App, the wizard can create the repository, switch GitHub Pages on and set the domain for you (step 4).'|i18n( 'design/admin/staticpublish' )}</p>
        {else}
        <p class="a4-warn-text">{'The GitHub integration is not available here: %problem. Publishing to the branch works all the same; switch GitHub Pages on in the repository\'s settings.'|i18n( 'design/admin/staticpublish',, hash( '%problem', $github.problem ) )|wash}</p>
        {/if}
    </div>
    <div class="a4-card-foot a4-actions" data-sp-nav="2" hidden="hidden"><button type="button" class="a4-btn" data-sp-next="1">{'Back'|i18n( 'design/admin/staticpublish' )}</button><button type="button" class="a4-btn a4-btn-primary" data-sp-next="3">{'Next'|i18n( 'design/admin/staticpublish' )}</button><a class="a4-btn a4-btn-ghost" href={'staticpublish/dashboard'|ezurl}>{'Cancel'|i18n( 'design/admin/staticpublish' )}</a></div>
</section>

{* 3. the credential *}
<section class="a4-card a4-section" id="sp-step-3" data-sp-step="3" aria-labelledby="sp-step-3-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-step-3-title">{'3. Signing in to the repository'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'Only a name is stored with the target; the token or key itself stays in Setup > Secrets and never appears in a log.'|i18n( 'design/admin/staticpublish' )}</p></div>
    <div class="a4-card-body a4-stack">
        <fieldset class="a4-field">
            <legend>{'Credential'|i18n( 'design/admin/staticpublish' )}</legend>
            <ul class="a4-radios">
                <li><label><input type="radio" name="CredentialKind" value="token"{if eq( $credential.kind, 'token' )} checked="checked"{/if} /> <span><strong>{'A token from Setup > Secrets'|i18n( 'design/admin/staticpublish' )}</strong> <span class="a4-help">{'For an https:// address. GitHub: a fine-grained token with Contents read and write on the repository.'|i18n( 'design/admin/staticpublish' )}</span></span></label></li>
                <li><label><input type="radio" name="CredentialKind" value="sshkey"{if eq( $credential.kind, 'sshkey' )} checked="checked"{/if} /> <span><strong>{'A deploy key made for this target'|i18n( 'design/admin/staticpublish' )}</strong> <span class="a4-help">{'For an SSH address. The key is made after saving; you add its public half to the repository.'|i18n( 'design/admin/staticpublish' )}</span></span></label></li>
                <li><label><input type="radio" name="CredentialKind" value="none"{if eq( $credential.kind, 'none' )} checked="checked"{/if} /> <span><strong>{'None'|i18n( 'design/admin/staticpublish' )}</strong> <span class="a4-help">{'The server signs in by itself (its own SSH key or credential helper), or the repository is on this server.'|i18n( 'design/admin/staticpublish' )}</span></span></label></li>
                <li><label><input type="radio" name="CredentialKind" value="app"{if eq( $credential.kind, 'app' )} checked="checked"{/if} /> <span><strong>{'A GitHub App'|i18n( 'design/admin/staticpublish' )}</strong> <span class="a4-help">{'For an https:// address on GitHub. The App set up for this installation signs in; it needs the installation id.'|i18n( 'design/admin/staticpublish' )}</span></span></label></li>
            </ul>
        </fieldset>
        <div class="a4-field">
            <label class="a4-label" for="sp-app">{'Installation id of the GitHub App'|i18n( 'design/admin/staticpublish' )}</label>
            <input class="a4-input" id="sp-app" name="CredentialApp" value="{$credential.app|wash}" maxlength="20" inputmode="numeric" autocomplete="off" />
            <span class="a4-help">{'Only with a GitHub App: the number at the end of the address of the App\'s installation on GitHub. The App itself is set in staticcache.ini (GitHubAppId, GitHubAppKey).'|i18n( 'design/admin/staticpublish' )}</span>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="sp-token">{'Name of the token in Setup > Secrets'|i18n( 'design/admin/staticpublish' )}</label>
            <input class="a4-input" id="sp-token" name="CredentialToken" value="{$credential.token|wash}" list="sp-secret-names" maxlength="104" autocomplete="off" />
            <datalist id="sp-secret-names">{foreach $secret_names as $name}<option value="{$name|wash}"></option>{/foreach}</datalist>
            <span class="a4-help">{'Only with a token. env:NAME reads an environment variable of the server instead.'|i18n( 'design/admin/staticpublish' )} <a href={'secrets/list'|ezurl}>{'Setup > Secrets'|i18n( 'design/admin/staticpublish' )}</a></span>
        </div>
        <p class="a4-help">{'A deploy key is kept as the secret %secret.'|i18n( 'design/admin/staticpublish',, hash( '%secret', $key_secret ) )|wash}{if and( $key, $key.exists )} {'This target has one.'|i18n( 'design/admin/staticpublish' )}{/if}</p>
        <p class="a4-help"><a href={'exphelp/viewer/help/static-publishing/connect-to-github-token-key-or-app.md'|ezurl}>{'Which to choose: token, deploy key or GitHub App'|i18n( 'design/admin/staticpublish' )}</a></p>
    </div>
    <div class="a4-card-foot a4-actions" data-sp-nav="3" hidden="hidden"><button type="button" class="a4-btn" data-sp-next="2">{'Back'|i18n( 'design/admin/staticpublish' )}</button><button type="button" class="a4-btn a4-btn-primary" data-sp-next="4">{'Next'|i18n( 'design/admin/staticpublish' )}</button><a class="a4-btn a4-btn-ghost" href={'staticpublish/dashboard'|ezurl}>{'Cancel'|i18n( 'design/admin/staticpublish' )}</a></div>
</section>

{* 4. the repository *}
<section class="a4-card a4-section" id="sp-step-4" data-sp-step="4" aria-labelledby="sp-step-4-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-step-4-title">{'4. The repository'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'Make the repository on the host first; an empty one is fine.'|i18n( 'design/admin/staticpublish' )}</p></div>
    <div class="a4-card-body a4-stack">
        <div class="a4-field">
            <label class="a4-label" for="sp-remote">{'Git address'|i18n( 'design/admin/staticpublish' )}</label>
            <input class="a4-input" id="sp-remote" name="Target[Remote]" value="{$settings.Remote|wash}" maxlength="500" autocomplete="off" spellcheck="false" />
            <span class="a4-help">{'https://github.com/owner/repo.git (with a token) or git@github.com:owner/repo.git (with a deploy key); never a password or token in it.'|i18n( 'design/admin/staticpublish' )}</span>
        </div>
        <div class="a4-grid">
            <div class="a4-field">
                <label class="a4-label" for="sp-branch">{'Branch'|i18n( 'design/admin/staticpublish' )}</label>
                <input class="a4-input" id="sp-branch" name="Target[Branch]" value="{$settings.Branch|wash}" maxlength="100" spellcheck="false" />
                <span class="a4-help">{'gh-pages, or main with the folder docs.'|i18n( 'design/admin/staticpublish' )}</span>
            </div>
            <div class="a4-field">
                <label class="a4-label" for="sp-folder">{'Folder'|i18n( 'design/admin/staticpublish' )}</label>
                <select id="sp-folder" name="Target[Folder]">
                    <option value=""{if eq( $settings.Folder, '' )} selected="selected"{/if}>{'The root of the branch'|i18n( 'design/admin/staticpublish' )}</option>
                    <option value="docs"{if eq( $settings.Folder, 'docs' )} selected="selected"{/if}>/docs</option>
                    {if and( $settings.Folder, ne( $settings.Folder, 'docs' ) )}<option value="{$settings.Folder|wash}" selected="selected">/{$settings.Folder|wash}</option>{/if}
                </select>
            </div>
        </div>
        <fieldset class="a4-field">
            <legend>{'How GitHub Pages receives the build'|i18n( 'design/admin/staticpublish' )}</legend>
            <ul class="a4-radios">
                <li><label><input type="radio" name="Target[Delivery]" value="branch"{if eq( $settings.Delivery, 'branch' )} checked="checked"{/if} /> <span><strong>{'From the branch'|i18n( 'design/admin/staticpublish' )}</strong> <span class="a4-help">{'GitHub serves the branch as it is pushed. The default.'|i18n( 'design/admin/staticpublish' )}</span></span></label></li>
                <li><label><input type="radio" name="Target[Delivery]" value="actions"{if eq( $settings.Delivery, 'actions' )} checked="checked"{/if} /> <span><strong>{'GitHub Actions'|i18n( 'design/admin/staticpublish' )}</strong> <span class="a4-help">{'A workflow of the repository publishes it. The build writes the workflow file; leave the folder empty.'|i18n( 'design/admin/staticpublish' )}</span></span></label></li>
            </ul>
            <span class="a4-help"><a href={'exphelp/viewer/help/static-publishing/choose-branch-or-github-actions.md'|ezurl}>{'Branch or GitHub Actions?'|i18n( 'design/admin/staticpublish' )}</a></span>
        </fieldset>
        <div class="a4-inset a4-stack" id="sp-github">
            <p class="a4-notice-title">{'On GitHub'|i18n( 'design/admin/staticpublish' )}</p>
            {if $github_report}
            <ul class="a4-plain" role="status">
            {foreach $github_report.steps as $step}
                <li><span class="a4-status is-{if eq( $step.state, 'failed' )}bad{elseif eq( $step.state, 'planned' )}warn{else}ok{/if}"><span class="a4-dot" aria-hidden="true"></span>{$step.step|wash}</span> {$step.message|wash}</li>
            {/foreach}
            {foreach $github_report.errors as $error}{if $github_report.steps|count|not}<li class="a4-warn-text">{$error|wash}</li>{/if}{/foreach}
            {foreach $github_report.warnings as $warning}<li class="a4-warn-text">{$warning|wash}</li>{/foreach}
            </ul>
            {/if}
            {if $github.usable}
            <p class="a4-help">{'Creates the repository %repo when it is missing (when asked), switches GitHub Pages on from the branch or from GitHub Actions as saved, and sets the custom domain and HTTPS. It uses the saved settings: save changes first.'|i18n( 'design/admin/staticpublish',, hash( '%repo', concat( $github.owner, '/', $github.repo ) ) )|wash}</p>
            <label class="a4-check"><input type="checkbox" name="GitHubCreate" value="1" /> <span>{'Create the repository if it does not exist'|i18n( 'design/admin/staticpublish' )}</span></label>
            <ul class="a4-radios">
                <li><label><input type="radio" name="GitHubVisibility" value="public" checked="checked" /> <span>{'Public'|i18n( 'design/admin/staticpublish' )} <span class="a4-help">{'Needed for GitHub Pages on a free plan.'|i18n( 'design/admin/staticpublish' )}</span></span></label></li>
                <li><label><input type="radio" name="GitHubVisibility" value="private" /> <span>{'Private'|i18n( 'design/admin/staticpublish' )} <span class="a4-help">{'GitHub Pages serves a private repository only on a paid plan.'|i18n( 'design/admin/staticpublish' )}</span></span></label></li>
            </ul>
            <div class="a4-actions"><button type="submit" class="a4-btn" name="GitHubSetupButton" value="1">{'Set up on GitHub'|i18n( 'design/admin/staticpublish' )}</button></div>
            {elseif $is_new}
            <p class="a4-help">{'After saving a target with a GitHub repository and a token or GitHub App, this step can create the repository and switch GitHub Pages on. Otherwise create the repository on GitHub first; an empty one is fine.'|i18n( 'design/admin/staticpublish' )}</p>
            {else}
            <p class="a4-help">{'Create the repository on GitHub and switch GitHub Pages on in its settings (Settings > Pages).'|i18n( 'design/admin/staticpublish' )}{if $github.problem} {'The GitHub steps are not available for this target: %problem.'|i18n( 'design/admin/staticpublish',, hash( '%problem', $github.problem ) )|wash}{/if}</p>
            {/if}
        </div>
    </div>
    <div class="a4-card-foot a4-actions" data-sp-nav="4" hidden="hidden"><button type="button" class="a4-btn" data-sp-next="3">{'Back'|i18n( 'design/admin/staticpublish' )}</button><button type="button" class="a4-btn a4-btn-primary" data-sp-next="5">{'Next'|i18n( 'design/admin/staticpublish' )}</button><a class="a4-btn a4-btn-ghost" href={'staticpublish/dashboard'|ezurl}>{'Cancel'|i18n( 'design/admin/staticpublish' )}</a></div>
</section>

{* 5. links and base path *}
<section class="a4-card a4-section" id="sp-step-5" data-sp-step="5" aria-labelledby="sp-step-5-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-step-5-title">{'5. Links and base path'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'A GitHub project site lives below /<repository>/; relative links work there and anywhere else.'|i18n( 'design/admin/staticpublish' )|wash}</p></div>
    <div class="a4-card-body a4-stack">
        <fieldset class="a4-field">
            <legend>{'Links'|i18n( 'design/admin/staticpublish' )}</legend>
            <ul class="a4-radios">
                <li><label><input type="radio" name="Target[Links]" value="relative"{if eq( $settings.Links, 'relative' )} checked="checked"{/if} /> <span><strong>{'Relative'|i18n( 'design/admin/staticpublish' )}</strong> <span class="a4-help">{'Work below any path. The default.'|i18n( 'design/admin/staticpublish' )}</span></span></label></li>
                <li><label><input type="radio" name="Target[Links]" value="base-path"{if eq( $settings.Links, 'base-path' )} checked="checked"{/if} /> <span><strong>{'Below a base path'|i18n( 'design/admin/staticpublish' )}</strong> <span class="a4-help">{'Every link starts with the base path.'|i18n( 'design/admin/staticpublish' )}</span></span></label></li>
            </ul>
        </fieldset>
        <div class="a4-grid">
            <div class="a4-field">
                <label class="a4-label" for="sp-basepath">{'Base path'|i18n( 'design/admin/staticpublish' )}</label>
                <input class="a4-input" id="sp-basepath" name="Target[BasePath]" value="{$settings.BasePath|wash}" maxlength="200" spellcheck="false" />
                <span class="a4-help">{'/repository/ for a GitHub project site; empty for a custom domain or a user site.'|i18n( 'design/admin/staticpublish' )}</span>
            </div>
            <div class="a4-field">
                <label class="a4-label" for="sp-publicurl">{'Public address'|i18n( 'design/admin/staticpublish' )}</label>
                <input class="a4-input" id="sp-publicurl" name="Target[PublicURL]" value="{$settings.PublicURL|wash}" maxlength="300" spellcheck="false" />
                <span class="a4-help">{'https://owner.github.io/repository/, for the sitemap and the canonical links.'|i18n( 'design/admin/staticpublish' )}</span>
            </div>
        </div>
        <div class="a4-inset" id="sp-link-preview" data-relative="{$link_relative|wash}" aria-live="polite">
            <p class="a4-meta">{'A link from the page /news/ to the page /about-us/team will be written as'|i18n( 'design/admin/staticpublish' )}</p>
            <p><code class="a4-code" id="sp-link-example">{if eq( $settings.Links, 'base-path' )}{$link_base|wash}{else}{$link_relative|wash}{/if}</code></p>
        </div>
    </div>
    <div class="a4-card-foot a4-actions" data-sp-nav="5" hidden="hidden"><button type="button" class="a4-btn" data-sp-next="4">{'Back'|i18n( 'design/admin/staticpublish' )}</button><button type="button" class="a4-btn a4-btn-primary" data-sp-next="6">{'Next'|i18n( 'design/admin/staticpublish' )}</button><a class="a4-btn a4-btn-ghost" href={'staticpublish/dashboard'|ezurl}>{'Cancel'|i18n( 'design/admin/staticpublish' )}</a></div>
</section>

{* 6. the custom domain *}
<section class="a4-card a4-section" id="sp-step-6" data-sp-step="6" aria-labelledby="sp-step-6-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-step-6-title">{'6. Your own domain'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'Optional. With a domain the site is served at its root.'|i18n( 'design/admin/staticpublish' )}</p></div>
    <div class="a4-card-body a4-stack">
        <div class="a4-field">
            <label class="a4-label" for="sp-domain">{'Custom domain'|i18n( 'design/admin/staticpublish' )}</label>
            <input class="a4-input" id="sp-domain" name="Target[CustomDomain]" value="{$settings.CustomDomain|wash}" maxlength="253" spellcheck="false" />
            <span class="a4-help">{'www.example.com: written to the file CNAME. Point the DNS of the domain at the host as it explains; HTTPS can take a while to start.'|i18n( 'design/admin/staticpublish' )}</span>
        </div>
        <div class="a4-inset a4-stack" id="sp-dns">
            <p class="a4-help">{'GitHub Pages: a subdomain (www.example.com) needs a CNAME record to <owner>.github.io; an apex domain (example.com) the A records %ips.'|i18n( 'design/admin/staticpublish',, hash( '%ips', $pages_a|implode( ', ' ) ) )|wash} <a href={'exphelp/viewer/help/static-publishing/use-your-own-domain.md'|ezurl}>{'Use your own domain'|i18n( 'design/admin/staticpublish' )}</a></p>
            <div class="a4-actions"><button type="submit" class="a4-btn" name="DnsCheckButton" value="1">{'Check the DNS'|i18n( 'design/admin/staticpublish' )}</button></div>
            {if $dns}
            <div role="status">
                <p><span class="a4-status is-{if $dns.ok}ok{else}bad{/if}"><span class="a4-dot" aria-hidden="true"></span>{if $dns.ok}{'The DNS of %domain points at GitHub Pages.'|i18n( 'design/admin/staticpublish',, hash( '%domain', $dns.domain ) )|wash}{else}{'The DNS of %domain does not point at GitHub Pages yet.'|i18n( 'design/admin/staticpublish',, hash( '%domain', $dns.domain ) )|wash}{/if}</span></p>
                {if $dns.problems|count}<ul>{foreach $dns.problems as $problem}<li>{$problem|wash}</li>{/foreach}</ul>{/if}
                {if $dns.fixes|count}<p class="a4-meta">{'Set these records at the provider of the domain:'|i18n( 'design/admin/staticpublish' )}</p>
                <pre class="a4-pre" tabindex="0">{foreach $dns.fixes as $fix}{$fix|wash}
{/foreach}</pre>{/if}
                {if $dns.notes|count}<ul class="a4-plain">{foreach $dns.notes as $note}<li class="a4-help">{$note|wash}</li>{/foreach}</ul>{/if}
            </div>
            {/if}
        </div>
    </div>
    <div class="a4-card-foot a4-actions" data-sp-nav="6" hidden="hidden"><button type="button" class="a4-btn" data-sp-next="5">{'Back'|i18n( 'design/admin/staticpublish' )}</button><button type="button" class="a4-btn a4-btn-primary" data-sp-next="7">{'Next'|i18n( 'design/admin/staticpublish' )}</button><a class="a4-btn a4-btn-ghost" href={'staticpublish/dashboard'|ezurl}>{'Cancel'|i18n( 'design/admin/staticpublish' )}</a></div>
</section>

{* 7. what is written *}
<section class="a4-card a4-section" id="sp-step-7" data-sp-step="7" aria-labelledby="sp-step-7-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-step-7-title">{'7. What the build holds'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'The defaults suit most sites.'|i18n( 'design/admin/staticpublish' )}</p></div>
    <div class="a4-card-body a4-stack">
        <fieldset class="a4-field">
            <legend>{'Files'|i18n( 'design/admin/staticpublish' )}</legend>
            <ul class="a4-radios">
                <li><label><input type="radio" name="Target[Assets]" value="referenced-and-storage"{if eq( $settings.Assets, 'referenced-and-storage' )} checked="checked"{/if} /> <span>{'What the pages name, and every image of published content'|i18n( 'design/admin/staticpublish' )}</span></label></li>
                <li><label><input type="radio" name="Target[Assets]" value="referenced"{if eq( $settings.Assets, 'referenced' )} checked="checked"{/if} /> <span>{'Only what the pages name'|i18n( 'design/admin/staticpublish' )}</span></label></li>
            </ul>
        </fieldset>
        <fieldset class="a4-field">
            <legend>{'Extras'|i18n( 'design/admin/staticpublish' )}</legend>
            <input type="hidden" name="Target[Extras][]" value="" />
            {foreach $extras as $extra}
            <label class="a4-check"><input type="checkbox" name="Target[Extras][]" value="{$extra|wash}"{if $settings.Extras|contains( $extra )} checked="checked"{/if} />
                <span>{switch match=$extra}
                {case match='nojekyll'}<strong>.nojekyll</strong><span class="a4-help">{'GitHub Pages publishes the files as they are (needed for files starting with _).'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case match='404'}<strong>404.html</strong><span class="a4-help">{'The site\'s own page for addresses that do not exist.'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case match='robots'}<strong>robots.txt</strong><span class="a4-help">{'Tells search engines what to read.'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case match='sitemap'}<strong>sitemap.xml</strong><span class="a4-help">{'The list of pages for search engines; needs the public address.'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case match='manifest'}<strong>{'Manifest'|i18n( 'design/admin/staticpublish' )}</strong><span class="a4-help">{'What each file is; always written.'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case match='cname'}<strong>CNAME</strong><span class="a4-help">{'Written whenever a custom domain is set.'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case match='redirects'}<strong>{'Redirect pages'|i18n( 'design/admin/staticpublish' )}</strong><span class="a4-help">{'A small page for every old address and alias of a page, so old links keep working.'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case match='readme'}<strong>README.md</strong><span class="a4-help">{'What the repository is, for whoever opens it on the host.'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case match='license'}<strong>LICENSE</strong><span class="a4-help">{'The license of the content, from the field below.'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case match='workflow'}<strong>{'GitHub Actions workflow'|i18n( 'design/admin/staticpublish' )}</strong><span class="a4-help">{'Always written when GitHub Actions delivers the site.'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case}<strong>{$extra|wash}</strong>{/case}
                {/switch}</span></label>
            {/foreach}
        </fieldset>
        <fieldset class="a4-field">
            <legend>{'Post-processing'|i18n( 'design/admin/staticpublish' )}</legend>
            <input type="hidden" name="Target[PostProcess][]" value="strip" />
            <label class="a4-check"><input type="checkbox" checked="checked" disabled="disabled" /> <span><strong>{'Strip'|i18n( 'design/admin/staticpublish' )}</strong><span class="a4-help">{'Debug output, form tokens, session ids and nonces are always taken out.'|i18n( 'design/admin/staticpublish' )}</span></span></label>
            {foreach $post_process as $step}{if ne( $step, 'strip' )}
            <label class="a4-check"><input type="checkbox" name="Target[PostProcess][]" value="{$step|wash}"{if $settings.PostProcess|contains( $step )} checked="checked"{/if} /> <span>{switch match=$step}
                {case match='minify'}<strong>{'Minify'|i18n( 'design/admin/staticpublish' )}</strong><span class="a4-help">{'Less white space and no comments in the pages.'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case match='precompress'}<strong>{'Precompress'|i18n( 'design/admin/staticpublish' )}</strong><span class="a4-help">{'A .gz (and .br) next to each text file, for hosts that serve them.'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case match='fingerprint'}<strong>{'Fingerprint'|i18n( 'design/admin/staticpublish' )}</strong><span class="a4-help">{'Style sheets and scripts named by the hash of their content, so browsers may keep them.'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case}<strong>{$step|wash}</strong>{/case}
                {/switch}</span></label>
            {/if}{/foreach}
        </fieldset>
        <div class="a4-grid">
            <div class="a4-field">
                <label class="a4-label" for="sp-search">{'Search page'|i18n( 'design/admin/staticpublish' )}</label>
                <select id="sp-search" name="Target[Search]">
                    <option value="disabled"{if eq( $settings.Search, 'disabled' )} selected="selected"{/if}>{'No'|i18n( 'design/admin/staticpublish' )}</option>
                    <option value="enabled"{if eq( $settings.Search, 'enabled' )} selected="selected"{/if}>{'Yes'|i18n( 'design/admin/staticpublish' )}</option>
                </select>
            </div>
            <div class="a4-field">
                <label class="a4-label" for="sp-forms">{'Forms'|i18n( 'design/admin/staticpublish' )}</label>
                <select id="sp-forms" name="Target[Forms]">
                    <option value="hide"{if eq( $settings.Forms, 'hide' )} selected="selected"{/if}>{'Hide them'|i18n( 'design/admin/staticpublish' )}</option>
                    <option value="endpoint"{if eq( $settings.Forms, 'endpoint' )} selected="selected"{/if}>{'Send them to the live site'|i18n( 'design/admin/staticpublish' )}</option>
                </select>
            </div>
            <div class="a4-field">
                <label class="a4-label" for="sp-maxpages">{'Most pages'|i18n( 'design/admin/staticpublish' )}</label>
                <input class="a4-input" id="sp-maxpages" name="Target[MaxPages]" value="{$settings.MaxPages|wash}" inputmode="numeric" maxlength="5" />
            </div>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="sp-license">{'License of the content'|i18n( 'design/admin/staticpublish' )}</label>
            <input class="a4-input" id="sp-license" name="Target[License]" value="{$settings.License|wash}" maxlength="300" spellcheck="false" />
            <span class="a4-help">{'An SPDX id such as CC-BY-4.0, or the text itself.'|i18n( 'design/admin/staticpublish' )}</span>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="sp-exclude">{'Leave out these pages'|i18n( 'design/admin/staticpublish' )}</label>
            <textarea class="a4-input" id="sp-exclude" name="Target[ExcludePaths]" rows="3" spellcheck="false">{$settings.ExcludePaths|implode( "\n" )|wash}</textarea>
            <span class="a4-help">{'One path per line, * matches any part: /members/*'|i18n( 'design/admin/staticpublish' )}</span>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="sp-sections">{'Leave out these sections'|i18n( 'design/admin/staticpublish' )}</label>
            <input class="a4-input" id="sp-sections" name="Target[ExcludeSections]" value="{$settings.ExcludeSections|implode( ', ' )|wash}" spellcheck="false" />
            <span class="a4-help">{'Section ids, separated by commas.'|i18n( 'design/admin/staticpublish' )}</span>
        </div>
        <div class="a4-grid">
            <div class="a4-field">
                <label class="a4-label" for="sp-crawlurl">{'Fetch the pages from'|i18n( 'design/admin/staticpublish' )}</label>
                <input class="a4-input" id="sp-crawlurl" name="Target[CrawlURL]" value="{$settings.CrawlURL|wash}" maxlength="300" spellcheck="false" />
                <span class="a4-help">{'Empty: the address of the site. An internal address of the same site otherwise.'|i18n( 'design/admin/staticpublish' )}</span>
            </div>
            <div class="a4-field">
                <label class="a4-label" for="sp-liveurl">{'Live site'|i18n( 'design/admin/staticpublish' )}</label>
                <input class="a4-input" id="sp-liveurl" name="Target[LiveURL]" value="{$settings.LiveURL|wash}" maxlength="300" spellcheck="false" />
                <span class="a4-help">{'Where links to what the copy does not hold go (the search, the login).'|i18n( 'design/admin/staticpublish' )}</span>
            </div>
        </div>
        <div class="a4-grid">
            <div class="a4-field">
                <label class="a4-label" for="sp-outsidelinks">{'Remove links to pages the copy does not hold'|i18n( 'design/admin/staticpublish' )}</label>
                <select id="sp-outsidelinks" name="Target[OutsideLinks]">
                    <option value="keep"{if eq( $settings.OutsideLinks, 'keep' )} selected="selected"{/if}>{'No'|i18n( 'design/admin/staticpublish' )}</option>
                    <option value="unlink"{if eq( $settings.OutsideLinks, 'unlink' )} selected="selected"{/if}>{'Yes'|i18n( 'design/admin/staticpublish' )}</option>
                </select>
                <span class="a4-help">{'Their text stays. Images, forms and links to other sites are never removed.'|i18n( 'design/admin/staticpublish' )}</span>
            </div>
            <div class="a4-field">
                <label class="a4-label" for="sp-adminlinks">{'Remove links into the administration'|i18n( 'design/admin/staticpublish' )}</label>
                <select id="sp-adminlinks" name="Target[AdminLinks]">
                    <option value="keep"{if eq( $settings.AdminLinks, 'keep' )} selected="selected"{/if}>{'No'|i18n( 'design/admin/staticpublish' )}</option>
                    <option value="unlink"{if eq( $settings.AdminLinks, 'unlink' )} selected="selected"{/if}>{'Yes'|i18n( 'design/admin/staticpublish' )}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="a4-card-foot a4-actions" data-sp-nav="7" hidden="hidden"><button type="button" class="a4-btn" data-sp-next="6">{'Back'|i18n( 'design/admin/staticpublish' )}</button><button type="button" class="a4-btn a4-btn-primary" data-sp-next="8">{'Next'|i18n( 'design/admin/staticpublish' )}</button><a class="a4-btn a4-btn-ghost" href={'staticpublish/dashboard'|ezurl}>{'Cancel'|i18n( 'design/admin/staticpublish' )}</a></div>
</section>

{* 8. triggers *}
<section class="a4-card a4-section" id="sp-step-8" data-sp-step="8" aria-labelledby="sp-step-8-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-step-8-title">{'8. When it is published'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'You can always build and publish by hand here and on the console.'|i18n( 'design/admin/staticpublish' )}</p></div>
    <div class="a4-card-body a4-stack">
        <fieldset class="a4-field">
            <legend>{'Triggers'|i18n( 'design/admin/staticpublish' )}</legend>
            <input type="hidden" name="Target[Triggers][]" value="" />
            {foreach $triggers as $trigger}
            <label class="a4-check"><input type="checkbox" name="Target[Triggers][]" value="{$trigger|wash}"{if $settings.Triggers|contains( $trigger )} checked="checked"{/if} />
                <span>{switch match=$trigger}
                {case match='manual'}<strong>{'By hand, here'|i18n( 'design/admin/staticpublish' )}</strong>{/case}
                {case match='cli'}<strong>{'On the console'|i18n( 'design/admin/staticpublish' )}</strong>{/case}
                {case match='publish'}<strong>{'When content is published'|i18n( 'design/admin/staticpublish' )}</strong><span class="a4-help">{'The changed pages, a few minutes after the last change (the cronjob part staticpublish).'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case match='schedule'}<strong>{'On a schedule'|i18n( 'design/admin/staticpublish' )}</strong><span class="a4-help">{'The whole site, at the times of the schedule (the cronjob part staticpublish).'|i18n( 'design/admin/staticpublish' )}</span>{/case}
                {case}{$trigger|wash}{/case}
                {/switch}</span></label>
            {/foreach}
        </fieldset>
        <div class="a4-grid">
            <div class="a4-field">
                <label class="a4-label" for="sp-schedule">{'Schedule'|i18n( 'design/admin/staticpublish' )}</label>
                <input class="a4-input" id="sp-schedule" name="Target[Schedule]" value="{$settings.Schedule|wash}" maxlength="100" spellcheck="false" />
                <span class="a4-help">{'A cron expression, for example 0 3 * * *'|i18n( 'design/admin/staticpublish' )}</span>
            </div>
            <div class="a4-field">
                <label class="a4-label" for="sp-webhook">{'Webhook'|i18n( 'design/admin/staticpublish' )}</label>
                <input class="a4-input" id="sp-webhook" name="Target[Webhook]" value="{$settings.Webhook|wash}" maxlength="300" spellcheck="false" />
            </div>
        </div>
        <fieldset class="a4-field">
            <legend>{'Notify'|i18n( 'design/admin/staticpublish' )}</legend>
            <input type="hidden" name="Target[Notify][]" value="" />
            {foreach $notify as $n}
            <label class="a4-check"><input type="checkbox" name="Target[Notify][]" value="{$n|wash}"{if $settings.Notify|contains( $n )} checked="checked"{/if} /> <span>{if eq( $n, 'failure' )}{'When a publish fails'|i18n( 'design/admin/staticpublish' )}{else}{'When a publish succeeds'|i18n( 'design/admin/staticpublish' )}{/if}</span></label>
            {/foreach}
        </fieldset>
    </div>
    <div class="a4-card-foot a4-actions" data-sp-nav="8" hidden="hidden"><button type="button" class="a4-btn" data-sp-next="7">{'Back'|i18n( 'design/admin/staticpublish' )}</button><button type="button" class="a4-btn a4-btn-primary" data-sp-next="9">{'Next'|i18n( 'design/admin/staticpublish' )}</button><a class="a4-btn a4-btn-ghost" href={'staticpublish/dashboard'|ezurl}>{'Cancel'|i18n( 'design/admin/staticpublish' )}</a></div>
</section>

{* 9. review *}
<section class="a4-card a4-section" id="sp-step-9" data-sp-step="9" aria-labelledby="sp-step-9-title">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-step-9-title">{'9. Review and save'|i18n( 'design/admin/staticpublish' )}</h2></div>
    <div class="a4-card-body a4-stack">
        <p>{'Review checks every setting and shows the lines that will be written. Nothing is saved yet.'|i18n( 'design/admin/staticpublish' )}</p>
    </div>
    <div class="a4-card-foot a4-actions">
        <button type="button" class="a4-btn" data-sp-next="8" data-sp-nav="9" hidden="hidden">{'Back'|i18n( 'design/admin/staticpublish' )}</button>
        <button type="submit" class="a4-btn a4-btn-primary" name="ReviewButton" value="1">{'Review'|i18n( 'design/admin/staticpublish' )}</button>
        <a class="a4-btn a4-btn-ghost" href={'staticpublish/dashboard'|ezurl}>{'Cancel'|i18n( 'design/admin/staticpublish' )}</a>
    </div>
</section>
</form>
{ezscript( array( 'page/staticpublish_wizard.js' ) )}
{/if}

</div>
