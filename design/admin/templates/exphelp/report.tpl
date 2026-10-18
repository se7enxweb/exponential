{* exphelp/report: how to report an error (doc/features/6.0/help-system.md, "Reporting an error"). From top to bottom:
   the security warning, what to do before reporting, where to report (the trackers, an extension's repository, the
   community), the report form that prepares a GitHub issue, the diagnostics to copy, the logs, and the private path
   for security issues (id="security", linked from the dashboard). Styles: stylesheets/modules/help.css (.a4-help-*,
   section "Report an error"); no inline CSS or script. The copy button uses exphelp.js ([data-exphelp-copy] in an
   .a4-help-code block) and is shown only with scripts (exp-js-only); without them the summary can be selected.

   Variables (kernel/private/classes/views/exphelp/report.php): help_trackers (key => key, label, description, url,
   new_issue_url, repository, slug), help_extension_trackers (key => ... plus extension, version), help_tracker (chosen
   key or ''), help_chosen (its tracker or false), help_draft (title, what, steps, expected), help_drafted,
   help_environment (label => value), help_environment_text, help_issue_urls (key => URL), help_community (label,
   url), help_security (email, policy_url, advisory_url), help_can_logs, help_logs_url, help_logs_digest_url,
   help_report_url, help_i18n_json. *}
{def $help_ctx = 'kernel/exphelp'
     $help_i18n = cond( is_set( $help_i18n_json ), $help_i18n_json, '' )
     $help_report = cond( is_set( $help_report_url ), $help_report_url, '/exphelp/report' )
     $help_digest = cond( is_set( $help_logs_digest_url ), $help_logs_digest_url, '/setup/logsdigest' )
     $help_logs = cond( is_set( $help_logs_url ), $help_logs_url, '/setup/logs' )
     $help_logs_ok = and( is_set( $help_can_logs ), $help_can_logs )
     $help_ext = cond( is_set( $help_extension_trackers ), $help_extension_trackers, array() )
     $help_target = cond( and( is_set( $help_chosen ), $help_chosen ), $help_chosen, false() )
     $help_target_url = ''
     $help_d = cond( is_set( $help_draft ), $help_draft, hash( 'title', '', 'what', '', 'steps', '', 'expected', '' ) )
     $help_sec = cond( is_set( $help_security ), $help_security, hash( 'email', '', 'policy_url', '', 'advisory_url', '' ) )
     $help_tracker_icons = hash( 'software', 'code-square', 'velocity', 'lightning', 'community', 'people' )}
{if and( $help_target|not, is_set( $help_trackers.software ) )}{set $help_target = $help_trackers.software}{/if}
{if and( $help_target, is_set( $help_issue_urls[$help_target.key] ) )}{set $help_target_url = $help_issue_urls[$help_target.key]}{/if}
<div class="a4-page a4-help a4-help-reportpage" data-exphelp{if $help_i18n|ne( '' )} data-exphelp-i18n="{$help_i18n|wash}"{/if}>

    {* ---- Hero ---- *}
    <section class="a4-help-hero" aria-labelledby="help-report-title">
        <p class="a4-help-hero-pre"><span class="a4-icon a4-bi-journal-text" aria-hidden="true"></span><a href={'/exphelp/dashboard'|ezurl}>{'Help center'|i18n( $help_ctx )}</a></p>
        <h1 id="help-report-title">{'Report an error'|i18n( $help_ctx )}</h1>
        <p class="a4-help-hero-sub">{'A good report gets an error fixed fast. This page tells you where to send it, what to write, and gives you a safe summary of this installation to paste in.'|i18n( $help_ctx )}</p>
        <nav class="a4-help-issue-jump" aria-label="{'On this page'|i18n( $help_ctx )|wash}">
            <a class="a4-btn a4-btn-small" href="#help-report-before"><span class="a4-icon a4-bi-check2-square" aria-hidden="true"></span>{'Before you report'|i18n( $help_ctx )}</a>
            <a class="a4-btn a4-btn-small" href="#help-report-where"><span class="a4-icon a4-bi-signpost-split" aria-hidden="true"></span>{'Where to report'|i18n( $help_ctx )}</a>
            <a class="a4-btn a4-btn-small" href="#help-report-write"><span class="a4-icon a4-bi-pencil-square" aria-hidden="true"></span>{'Write the report'|i18n( $help_ctx )}</a>
            <a class="a4-btn a4-btn-small" href="#help-report-diagnostics"><span class="a4-icon a4-bi-cpu" aria-hidden="true"></span>{'Diagnostics'|i18n( $help_ctx )}</a>
            <a class="a4-btn a4-btn-small" href="#security"><span class="a4-icon a4-bi-shield-lock" aria-hidden="true"></span>{'Security issues'|i18n( $help_ctx )}</a>
        </nav>
    </section>

    <div class="a4-notice is-warn a4-help-issue-secwarn" role="note">
        <span class="a4-icon a4-bi-shield-lock" aria-hidden="true"></span>
        <p><strong>{'Found a security problem?'|i18n( $help_ctx )}</strong> {'Never report it in a public issue tracker or forum. Use the private path described under'|i18n( $help_ctx )} <a href="#security">{'Security issues'|i18n( $help_ctx )}</a>.</p>
    </div>

    {* ---- Before you report ---- *}
    <section class="a4-help-issue-section" aria-labelledby="help-report-before">
        <div class="a4-help-section-head">
            <h2 id="help-report-before"><span class="a4-icon a4-bi-check2-square" aria-hidden="true"></span>{'Before you report'|i18n( $help_ctx )}</h2>
            <p>{'A minute of checking often answers the question, or turns a vague report into one that can be fixed.'|i18n( $help_ctx )}</p>
        </div>
        <ol class="a4-help-issue-steps">
            <li>
                <h3>{'Search the help'|i18n( $help_ctx )}</h3>
                <p>{'The answer may already be documented, including the known limits of a feature.'|i18n( $help_ctx )} <a href={'/exphelp/dashboard'|ezurl}>{'Open the help center'|i18n( $help_ctx )}</a></p>
            </li>
            <li>
                <h3>{'Look at the logs'|i18n( $help_ctx )}</h3>
                {if $help_logs_ok}
                <p>{'The error digest lists the distinct error messages of the last hours. The message and the time it happened are what a maintainer needs most.'|i18n( $help_ctx )} <a href={$help_digest|ezurl}>{'Open the error digest'|i18n( $help_ctx )}</a></p>
                {else}
                <p>{'Ask an administrator of this site for the matching lines of the error digest (Setup, Logs). The message and the time it happened are what a maintainer needs most.'|i18n( $help_ctx )}</p>
                {/if}
            </li>
            <li>
                <h3>{'Try it again'|i18n( $help_ctx )}</h3>
                <p>{'Repeat what you did. Note whether it fails every time, only for one user or one page, and whether clearing the caches changes anything.'|i18n( $help_ctx )}</p>
            </li>
            <li>
                <h3>{'Look for an existing report'|i18n( $help_ctx )}</h3>
                <p>{'Search the open issues of the tracker below. If your error is there, add what you know to it instead of opening a new one.'|i18n( $help_ctx )}</p>
            </li>
        </ol>
    </section>

    {* ---- Where to report ---- *}
    <section class="a4-help-issue-section" aria-labelledby="help-report-where">
        <div class="a4-help-section-head">
            <h2 id="help-report-where"><span class="a4-icon a4-bi-signpost-split" aria-hidden="true"></span>{'Where to report'|i18n( $help_ctx )}</h2>
            <p>{'Each part of Exponential has its own public tracker on GitHub. You need a GitHub account to open an issue.'|i18n( $help_ctx )}</p>
        </div>
        {if is_set( $help_trackers )}
        <ul class="a4-help-issue-trackers">
        {foreach $help_trackers as $tracker}
            <li class="a4-card a4-help-issue-tracker{if and( $help_target, $help_target.key|eq( $tracker.key ) )} is-chosen{/if}">
                <div class="a4-help-issue-tracker-head">
                    <span class="a4-help-tile is-small" aria-hidden="true"><span class="a4-icon a4-bi-{cond( is_set( $help_tracker_icons[$tracker.key] ), $help_tracker_icons[$tracker.key], 'git' )}"></span></span>
                    <h3>{$tracker.label|i18n( $help_ctx )|wash}</h3>
                </div>
                {if $tracker.description|ne( '' )}<p>{$tracker.description|i18n( $help_ctx )|wash}</p>{/if}
                <p class="a4-help-issue-repo"><span class="a4-icon a4-bi-git" aria-hidden="true"></span>{$tracker.slug|wash}</p>
                <div class="a4-actions">
                    {if is_set( $help_issue_urls[$tracker.key] )}<a class="a4-btn a4-btn-primary" href="{$help_issue_urls[$tracker.key]|wash}" target="_blank" rel="noopener noreferrer">{'New issue'|i18n( $help_ctx )}<span class="a4-sr-only"> ({'opens GitHub in a new window'|i18n( $help_ctx )})</span><span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span></a>{/if}
                    <a class="a4-btn" href="{$tracker.url|wash}" target="_blank" rel="noopener noreferrer">{'Open issues'|i18n( $help_ctx )}<span class="a4-sr-only"> ({'opens GitHub in a new window'|i18n( $help_ctx )})</span></a>
                </div>
            </li>
        {/foreach}
        </ul>
        {/if}

        {if $help_ext|count|gt( 0 )}
        <form class="a4-card a4-help-issue-extension" method="get" action={$help_report|ezurl}>
            <div class="a4-help-issue-tracker-head">
                <span class="a4-help-tile is-small" aria-hidden="true"><span class="a4-icon a4-bi-puzzle"></span></span>
                <h3>{'An extension'|i18n( $help_ctx )}</h3>
            </div>
            <p>{'An error that only happens in one extension (a datatype, a module, a design of its own) is fixed in that extension\'s repository.'|i18n( $help_ctx )}</p>
            <div class="a4-help-issue-extension-row">
                <div class="a4-field">
                    <label for="help-report-extension">{'Extension'|i18n( $help_ctx )}</label>
                    <select id="help-report-extension" name="tracker">
                        <option value="">{'Choose an extension'|i18n( $help_ctx )}</option>
                        {foreach $help_ext as $ext}
                        <option value="{$ext.key|wash}"{if $help_tracker|eq( $ext.key )} selected="selected"{/if}>{$ext.label|wash}{if $ext.version|ne( '' )} {$ext.version|wash}{/if}</option>
                        {/foreach}
                    </select>
                </div>
                <button type="submit" class="a4-btn">{'Use this extension'|i18n( $help_ctx )}</button>
            </div>
            {if and( $help_target, is_set( $help_target.extension ) )}
            <p class="a4-help-issue-repo"><span class="a4-icon a4-bi-git" aria-hidden="true"></span>{$help_target.slug|wash}</p>
            <div class="a4-actions">
                <a class="a4-btn a4-btn-primary" href="{$help_target_url|wash}" target="_blank" rel="noopener noreferrer">{'New issue for %name'|i18n( $help_ctx,, hash( '%name', $help_target.label ) )|wash}<span class="a4-sr-only"> ({'opens GitHub in a new window'|i18n( $help_ctx )})</span><span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span></a>
                <a class="a4-btn" href="{$help_target.url|wash}" target="_blank" rel="noopener noreferrer">{'Open issues'|i18n( $help_ctx )}<span class="a4-sr-only"> ({'opens GitHub in a new window'|i18n( $help_ctx )})</span></a>
            </div>
            {/if}
        </form>
        {/if}

        {if and( is_set( $help_community ), $help_community|count|gt( 0 ) )}
        <div class="a4-help-issue-community">
            <h3><span class="a4-icon a4-bi-people" aria-hidden="true"></span>{'Not sure it is an error?'|i18n( $help_ctx )}</h3>
            <p>{'Ask the community first: questions about how something works, ideas and experiences belong there.'|i18n( $help_ctx )}</p>
            <ul class="a4-help-links">
            {foreach $help_community as $link}
                <li><a href="{$link.url|wash}" target="_blank" rel="noopener noreferrer"><span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span>{$link.label|i18n( $help_ctx )|wash}<span class="a4-sr-only"> ({'opens in a new window'|i18n( $help_ctx )})</span></a></li>
            {/foreach}
            </ul>
        </div>
        {/if}
    </section>

    {* ---- Write the report ---- *}
    <section class="a4-help-issue-section" aria-labelledby="help-report-write">
        <div class="a4-help-section-head">
            <h2 id="help-report-write"><span class="a4-icon a4-bi-pencil-square" aria-hidden="true"></span>{'Write the report'|i18n( $help_ctx )}</h2>
            <p>{'Fill in what you know; the form prepares the issue with the diagnostics of this installation. Nothing is sent to GitHub until you open the issue there and submit it yourself.'|i18n( $help_ctx )}</p>
        </div>

        <ol class="a4-help-issue-steps is-compact">
            <li><h3>{'A title that names the error'|i18n( $help_ctx )}</h3><p>{'"Saving an article with an image fails with a 500 error", not "Bug" or "Help".'|i18n( $help_ctx )}</p></li>
            <li><h3>{'What happened'|i18n( $help_ctx )}</h3><p>{'What you did, and what went wrong. Quote the error message exactly.'|i18n( $help_ctx )}</p></li>
            <li><h3>{'Steps to reproduce'|i18n( $help_ctx )}</h3><p>{'The shortest way from a fresh page to the error, one step per line.'|i18n( $help_ctx )}</p></li>
            <li><h3>{'What you expected'|i18n( $help_ctx )}</h3><p>{'What should have happened instead.'|i18n( $help_ctx )}</p></li>
            <li><h3>{'Leave out anything private'|i18n( $help_ctx )}</h3><p>{'No passwords, keys, e-mail addresses, names of people or customer data. The form removes the obvious ones, but read the issue before you submit it.'|i18n( $help_ctx )}</p></li>
        </ol>

        {if and( is_set( $help_drafted ), $help_drafted, $help_target_url|ne( '' ) )}
        <div class="a4-notice is-ok a4-help-issue-ready" role="status">
            <p><strong>{'Your issue is ready.'|i18n( $help_ctx )}</strong> {'It opens on GitHub with your text and the diagnostics filled in; check it there and submit it.'|i18n( $help_ctx )}</p>
            <div class="a4-actions">
                <a class="a4-btn a4-btn-primary" href="{$help_target_url|wash}" target="_blank" rel="noopener noreferrer">{'Open the issue in %tracker'|i18n( $help_ctx,, hash( '%tracker', $help_target.label|i18n( $help_ctx ) ) )|wash}<span class="a4-sr-only"> ({'opens GitHub in a new window'|i18n( $help_ctx )})</span><span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span></a>
            </div>
        </div>
        {/if}

        <form class="a4-card a4-form a4-help-issue-form" method="post" action={concat( $help_report, '#help-report-write' )|ezurl}>
            <div class="a4-field">
                <label for="help-report-tracker">{'Report to'|i18n( $help_ctx )}</label>
                <select id="help-report-tracker" name="tracker">
                {if is_set( $help_trackers )}
                    {foreach $help_trackers as $tracker}
                    <option value="{$tracker.key|wash}"{if and( $help_target, $help_target.key|eq( $tracker.key ) )} selected="selected"{/if}>{$tracker.label|i18n( $help_ctx )|wash}</option>
                    {/foreach}
                {/if}
                {if $help_ext|count|gt( 0 )}
                    <optgroup label="{'Extensions'|i18n( $help_ctx )|wash}">
                    {foreach $help_ext as $ext}
                        <option value="{$ext.key|wash}"{if and( $help_target, $help_target.key|eq( $ext.key ) )} selected="selected"{/if}>{$ext.label|wash}</option>
                    {/foreach}
                    </optgroup>
                {/if}
                </select>
            </div>
            <div class="a4-field a4-field-wide">
                <label for="help-report-title-field">{'Title'|i18n( $help_ctx )}</label>
                <input type="text" id="help-report-title-field" name="title" maxlength="200" value="{$help_d.title|wash}" autocomplete="off" />
            </div>
            <div class="a4-field a4-field-wide">
                <label for="help-report-what">{'What happened'|i18n( $help_ctx )}</label>
                <textarea id="help-report-what" name="what" rows="5" maxlength="2000">{$help_d.what|wash}</textarea>
            </div>
            <div class="a4-field a4-field-wide">
                <label for="help-report-steps">{'Steps to reproduce'|i18n( $help_ctx )}</label>
                <textarea id="help-report-steps" name="steps" rows="5" maxlength="2000">{$help_d.steps|wash}</textarea>
            </div>
            <div class="a4-field a4-field-wide">
                <label for="help-report-expected">{'What you expected'|i18n( $help_ctx )}</label>
                <textarea id="help-report-expected" name="expected" rows="3" maxlength="1000">{$help_d.expected|wash}</textarea>
            </div>
            <div class="a4-actions">
                <button type="submit" class="a4-btn a4-btn-primary">{'Prepare the issue'|i18n( $help_ctx )}</button>
            </div>
        </form>
    </section>

    {* ---- Diagnostics ---- *}
    <section class="a4-help-issue-section" aria-labelledby="help-report-diagnostics">
        <div class="a4-help-section-head">
            <h2 id="help-report-diagnostics"><span class="a4-icon a4-bi-cpu" aria-hidden="true"></span>{'Diagnostics'|i18n( $help_ctx )}</h2>
            <p>{'The version, the server and the extensions of this installation. It never contains passwords, keys, database hosts or names, e-mail addresses or paths outside the installation.'|i18n( $help_ctx )}</p>
        </div>
        {if and( is_set( $help_environment ), $help_environment|count|gt( 0 ) )}
        <dl class="a4-facts a4-help-issue-facts">
        {foreach $help_environment as $label => $value}
            <div><dt>{$label|wash}</dt><dd>{$value|wash}</dd></div>
        {/foreach}
        </dl>
        {/if}
        <div class="a4-help-code a4-help-issue-env">
            <pre class="a4-pre" tabindex="0" aria-label="{'Diagnostics as text'|i18n( $help_ctx )|wash}"><code>{$help_environment_text|wash}</code></pre>
            <button type="button" class="a4-btn a4-help-copy exp-js-only" data-exphelp-copy aria-label="{'Copy the diagnostics'|i18n( $help_ctx )|wash}" hidden>{'Copy'|i18n( $help_ctx )}</button>
        </div>
    </section>

    {* ---- Security issues ---- *}
    <section class="a4-help-issue-section a4-help-issue-security" id="security" aria-labelledby="help-report-security">
        <div class="a4-help-section-head">
            <h2 id="help-report-security"><span class="a4-icon a4-bi-shield-lock" aria-hidden="true"></span>{'Security issues'|i18n( $help_ctx )}</h2>
            <p>{'A weakness that lets someone read, change or take over what they should not is reported privately, so it can be fixed before anyone else learns about it.'|i18n( $help_ctx )}</p>
        </div>
        <div class="a4-notice is-bad">
            <p><strong>{'Do not open a public issue, a pull request or a forum post about it.'|i18n( $help_ctx )}</strong></p>
            <ul class="a4-help-issue-security-list">
                {if $help_sec.advisory_url|ne( '' )}<li><a href="{$help_sec.advisory_url|wash}" target="_blank" rel="noopener noreferrer"><span class="a4-icon a4-bi-lock" aria-hidden="true"></span>{'Report a vulnerability privately on GitHub'|i18n( $help_ctx )}<span class="a4-sr-only"> ({'opens GitHub in a new window'|i18n( $help_ctx )})</span></a></li>{/if}
                {if $help_sec.email|ne( '' )}<li><a href="mailto:{$help_sec.email|wash}"><span class="a4-icon a4-bi-envelope" aria-hidden="true"></span>{'E-mail %address'|i18n( $help_ctx,, hash( '%address', $help_sec.email ) )|wash}</a></li>{/if}
                {if $help_sec.policy_url|ne( '' )}<li><a href="{$help_sec.policy_url|wash}" target="_blank" rel="noopener noreferrer"><span class="a4-icon a4-bi-shield-fill-check" aria-hidden="true"></span>{'Read the security policy'|i18n( $help_ctx )}<span class="a4-sr-only"> ({'opens GitHub in a new window'|i18n( $help_ctx )})</span></a></li>{/if}
            </ul>
            <p>{'Include the version, the PHP version and the engine (the diagnostics above), what an attacker can do and the steps to reproduce it. Leave out passwords, secrets and personal data. You get a first answer within one business day.'|i18n( $help_ctx )}</p>
        </div>
    </section>
    {include uri='design:exphelp/parts/license_notice.tpl' license_page_id=''}
</div>
{ezscript_require( array( 'exphelp.js' ) )}
