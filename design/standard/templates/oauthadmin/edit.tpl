{* Editing a REST application: oauthadmin/edit/<id>. The form, its fields (Name, Description, EndPointURI) and its
   buttons (StoreButton, DiscardButton) are those of the old page. A new application gets its client identifier and
   secret when it is first stored. Styles: parts/style.tpl. *}
{include uri='design:oauthadmin/parts/style.tpl'}
{def $modified = cond( $application.updated|ne(0), $application.updated, $application.created )
     $owner_name = '?'}
{if $application.owner}{set $owner_name = $application.owner.contentobject.name}{/if}

<form action={concat( 'oauthadmin/edit/', $application.id )|ezurl} method="post" id="ClassEdit" name="ApplicationEdit">

<div class="context-block exp-oauth">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if $is_new}{'New REST application'|i18n( 'extension/oauthadmin' )}{else}{'Edit application <%application_name>'|i18n( 'extension/oauthadmin',, hash( '%application_name', $application.name ) )|wash}{/if}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{include uri='design:oauthadmin/parts/tabs.tpl' current='applications'}

{if $is_new}
<p class="exp-intro">{'Name the application and give the address it receives users back at after they authorized it. Its client identifier and secret are made when you store it, and shown on its page.'|i18n( 'design/admin/oauthadmin' )}</p>
{else}
<p class="exp-intro">{'Last modified: %modified by %owner'|i18n( 'extension/oauthadmin',, hash( '%modified', $modified|l10n( shortdatetime ), '%owner', $owner_name ) )|wash}</p>
{/if}

{if $errors|count|gt(0)}
<div class="exp-feedback is-bad" role="alert">
    <strong>{'The application was not stored:'|i18n( 'design/admin/oauthadmin' )}</strong>
    <ul>{foreach $errors as $error}<li>{$error|wash}</li>{/foreach}</ul>
</div>
{/if}

<div class="exp-form">
    <div class="exp-field">
        <label for="ApplicationName">{'Name'|i18n( 'extension/oauthadmin' )}</label>
        <input type="text" id="ApplicationName" name="Name" maxlength="100" required="required" value="{$application.name|wash}" aria-describedby="ApplicationNameHint" />
        <span class="exp-hint" id="ApplicationNameHint">{'Shown to users when the application asks for their authorization.'|i18n( 'design/admin/oauthadmin' )}</span>
    </div>
    <div class="exp-field">
        <label for="ApplicationDescription">{'Description'|i18n( 'extension/oauthadmin' )}</label>
        <textarea id="ApplicationDescription" name="Description" rows="3">{$application.description|wash}</textarea>
    </div>
    <div class="exp-field">
        <label for="ApplicationEndpointUri">{'Endpoint URI'|i18n( 'extension/oauthadmin' )}</label>
        <input type="url" id="ApplicationEndpointUri" name="EndPointURI" maxlength="200" value="{$application.endpoint_uri|wash}" placeholder="https://app.example.com/oauth/callback" aria-describedby="ApplicationEndpointHint" spellcheck="false" />
        <span class="exp-hint" id="ApplicationEndpointHint">{'The redirect_uri of the application: authorization codes and tokens are only ever sent to exactly this address.'|i18n( 'design/admin/oauthadmin' )}</span>
    </div>
</div>

<div class="exp-bar" id="controlbar-bottom">
    <span class="exp-meta">{if $is_new}{'Cancel removes this new application again.'|i18n( 'design/admin/oauthadmin' )}{else}{'Cancel leaves the application as it was.'|i18n( 'design/admin/oauthadmin' )}{/if}</span>
    <div class="exp-actions">
        <button class="exp-btn" type="submit" name="DiscardButton" value="{'Cancel'|i18n( 'design/admin/class/edit' )}" formnovalidate="formnovalidate" title="{'Discard all changes and exit from edit mode.'|i18n( 'design/admin/class/edit' )|wash}">{'Cancel'|i18n( 'design/admin/class/edit' )}</button>
        <button class="exp-btn exp-btn-primary" type="submit" name="StoreButton" value="{'OK'|i18n( 'extension/oauthadmin' )}" title="{'Store changes and exit from edit mode.'|i18n( 'design/admin/class/edit' )|wash}">{'OK'|i18n( 'extension/oauthadmin' )}</button>
    </div>
</div>

</div></div></div>

</div>

</form>
{undef $modified $owner_name}
