{* The package upload (package/upload).

   What an upload does and what it checks, the errors of the last attempt, and the file field. The archive is
   inspected before anything of it is written (eZPackageUploadInspector); the limits come from the view
   (upload_limits). POST names as before: PackageBinaryFile, UploadPackageButton, UploadCancelButton.
   Guide: doc/guides/packages.md *}
{include uri='design:package/exp_style.tpl'}
{def $limits = first_set( $upload_limits, false() )}

<div class="context-block exp-packages">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Upload package'|i18n('design/admin/package')}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if $error_list}
<div class="exp-feedback is-bad" role="alert">
    <p><strong>{'The package was not imported.'|i18n('design/admin/package')}</strong></p>
    <ul>
    {foreach $error_list as $error}
        <li>{$error.description|wash}</li>
    {/foreach}
    </ul>
</div>
{/if}

<p class="exp-intro">{'Import a package exported from this or another installation. It is added to the repository its vendor names ("local" when it names none) and is not installed yet: a package with install items opens its install step next, any other its page.'|i18n('design/admin/package')}</p>

<form enctype="multipart/form-data" method="post" action={'package/upload'|ezurl}>

<div class="exp-panel">
    <div class="exp-form-fields">
        <div class="exp-field">
            <label for="package-file">{'Package file'|i18n('design/admin/package')}</label>
            <input type="hidden" name="MAX_FILE_SIZE" value="{if $limits}{$limits.max_archive_size}{else}32000000{/if}" />
            <input class="exp-file" id="package-file" name="PackageBinaryFile" type="file" accept=".ezpkg,.tgz,.gz,application/gzip,application/x-gzip" aria-describedby="package-file-help" />
            <span class="exp-help" id="package-file-help">{if $limits}{'An .ezpkg, .tar.gz or .tgz archive of at most %size. The server accepts uploads of at most %server.'|i18n( 'design/admin/package',, hash( '%size', $limits.max_archive_size|si( byte ), '%server', $limits.upload_max_filesize ) )|wash}{else}{'Select the file containing the package then click the upload button'|i18n('design/admin/package')}{/if}</span>
        </div>
    </div>
</div>

<div class="exp-panel">
    <h2 class="exp-h2">{'What is checked first'|i18n('design/admin/package')}</h2>
    <ol class="exp-steps">
        <li>{'The file is a gzip compressed tar archive with one of the allowed names.'|i18n('design/admin/package')}</li>
        <li>{if $limits}{'It has at most %entries entries that unpack to at most %size.'|i18n( 'design/admin/package',, hash( '%entries', $limits.max_entries, '%size', $limits.max_unpacked_size|si( byte ) ) )|wash}{else}{'It is not too large.'|i18n('design/admin/package')}{/if}</li>
        <li>{'Every entry is a plain file or directory inside the package: no "..", no absolute path, no link, no device.'|i18n('design/admin/package')}</li>
        <li>{'It has a well formed package.xml with a valid package name, and no package of that name exists yet: an existing package is never overwritten.'|i18n('design/admin/package')}</li>
    </ol>
    {if $upload_vendor}<p class="exp-help" style="margin-top: 8px">{"A package whose vendor is %vendor goes into the setup wizard's repository and becomes an installer source."|i18n( 'design/admin/package',, hash( '%vendor', $upload_vendor ) )|wash}</p>{/if}
</div>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="UploadPackageButton" value="1">{'Import package'|i18n('design/admin/package')}</button>
        <button class="exp-btn" type="submit" name="UploadCancelButton" value="1">{'Cancel'|i18n('design/admin/package')}</button>
    </div>
</div>

</form>

</div></div></div>

</div>
{undef $limits}
