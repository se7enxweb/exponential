{* Editing a REST application: oauthadmin/edit/<id>. The form, its fields (Name, Description, EndPointURI) and its
   buttons (StoreButton, DiscardButton) are those of the old page. A new application gets its client identifier and
   secret when it is first stored.

   The same file is in design/admin and design/admin4. It uses the shared admin components (components.css) and
   stylesheets/modules/setup.css (.a4-setup-oauth), in light and dark. *}
{def $modified = cond( $application.updated|ne(0), $application.updated, $application.created )
     $owner_name = '?'}
{if $application.owner}{set $owner_name = $application.owner.contentobject.name}{/if}

<form action={concat( 'oauthadmin/edit/', $application.id )|ezurl} method="post" id="ClassEdit" name="ApplicationEdit">

<div class="context-block a4-page a4-setup-oauth">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if $is_new}{'New REST application'|i18n( 'extension/oauthadmin' )}{else}{'Edit application <%application_name>'|i18n( 'extension/oauthadmin',, hash( '%application_name', $application.name ) )|wash}{/if}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{include uri='design:oauthadmin/parts/tabs.tpl' current='applications' subpage=true()}

{if $is_new}
<p class="a4-intro">{'Name the application and give the address it receives users back at after they authorized it. Its client identifier and secret are made when you store it, and shown on its page.'|i18n( 'design/admin/oauthadmin' )}</p>
{else}
<p class="a4-intro">{'Last modified: %modified by %owner'|i18n( 'extension/oauthadmin',, hash( '%modified', $modified|l10n( shortdatetime ), '%owner', $owner_name ) )|wash}</p>
{/if}

{if $errors|count|gt(0)}
<div class="a4-notice is-bad" role="alert">
    <strong class="a4-notice-title">{'The application was not stored:'|i18n( 'design/admin/oauthadmin' )}</strong>
    <ul>{foreach $errors as $error}<li>{$error|wash}</li>{/foreach}</ul>
</div>
{/if}

<div class="a4-section">
<div class="a4-card">
<p class="a4-required-hint"><span class="a4-required-mark" aria-hidden="true">*</span>{'Required'|i18n( 'design/admin/oauthadmin' )}</p>
<div class="a4-form">
    <div class="a4-field">
        <label for="ApplicationName">{'Name'|i18n( 'extension/oauthadmin' )}<span class="a4-required-mark" aria-hidden="true">*</span></label>
        <input type="text" id="ApplicationName" name="Name" maxlength="100" required="required" value="{$application.name|wash}" aria-describedby="ApplicationNameHint" />
        <span class="a4-help" id="ApplicationNameHint">{'Shown to users when the application asks for their authorization.'|i18n( 'design/admin/oauthadmin' )}</span>
    </div>
    <div class="a4-field">
        <label for="ApplicationDescription">{'Description'|i18n( 'extension/oauthadmin' )}</label>
        <textarea id="ApplicationDescription" name="Description" rows="3">{$application.description|wash}</textarea>
    </div>
    <div class="a4-field">
        <label for="ApplicationEndpointUri">{'Endpoint URI'|i18n( 'extension/oauthadmin' )}</label>
        <input type="url" id="ApplicationEndpointUri" name="EndPointURI" maxlength="200" value="{$application.endpoint_uri|wash}" placeholder="https://app.example.com/oauth/callback" aria-describedby="ApplicationEndpointHint" spellcheck="false" />
        <span class="a4-help" id="ApplicationEndpointHint">{'The redirect_uri of the application: authorization codes and tokens are only ever sent to exactly this address.'|i18n( 'design/admin/oauthadmin' )}</span>
    </div>
</div>
</div>
</div>

<div class="a4-toolbar" id="controlbar-bottom">
    <span class="a4-meta">{if $is_new}{'Cancel removes this new application again.'|i18n( 'design/admin/oauthadmin' )}{else}{'Cancel leaves the application as it was.'|i18n( 'design/admin/oauthadmin' )}{/if}</span>
    <div class="a4-actions">
        <button class="a4-btn" type="submit" name="DiscardButton" value="{'Cancel'|i18n( 'design/admin/class/edit' )}" formnovalidate="formnovalidate" title="{'Discard all changes and exit from edit mode.'|i18n( 'design/admin/class/edit' )|wash}">{'Cancel'|i18n( 'design/admin/class/edit' )}</button>
        <button class="a4-btn a4-btn-primary" type="submit" name="StoreButton" value="{'OK'|i18n( 'extension/oauthadmin' )}" title="{'Store changes and exit from edit mode.'|i18n( 'design/admin/class/edit' )|wash}"><span class="a4-icon a4-bi-check-lg" aria-hidden="true"></span>{'OK'|i18n( 'extension/oauthadmin' )}</button>
    </div>
</div>

</div></div></div>

</div>

</form>
{undef $modified $owner_name}
