{* The validation of a package (expPackageJson::validateDirectory(), doc/bc/6.0/package-json-format.md): every
   document well-formed and, in the JSON format, valid against the schema; the checksums of package.json.
   $validation: valid, errors, warnings, format, documents, files, checksums (bool); $validation_title: the heading. *}
<div class="exp-panel" data-validation="{if $validation.valid}valid{else}invalid{/if}">
<h2 class="exp-h2">{$validation_title|wash}</h2>
{if is_set( $validation.at )}<p class="exp-meta">{'Checked %time'|i18n( 'design/admin/package',, hash( '%time', $validation.at|l10n( shortdatetime ) ) )}</p>{/if}
{if $validation.valid}
    <p class="exp-feedback is-ok" role="status">{'Valid: %documents documents and %files files checked.'|i18n( 'design/admin/package',, hash( '%documents', $validation.documents, '%files', $validation.files ) )}
    {if first_set( $validation.checksums, false() )}{'The checksums of package.json match every file.'|i18n( 'design/admin/package' )}
    {elseif $validation.format|eq( 'json' )}{'package.json lists no checksums, so the files could not be compared with them.'|i18n( 'design/admin/package' )}
    {else}{'The classic format carries no checksums; every document was read as XML.'|i18n( 'design/admin/package' )}{/if}</p>
{else}
    <div class="exp-feedback is-bad" role="alert">
        <p><strong>{'Not valid: %count errors.'|i18n( 'design/admin/package',, hash( '%count', $validation.errors|count ) )}</strong></p>
        <ul class="exp-validation-errors">{foreach $validation.errors as $line max 20}<li><code>{$line|wash}</code></li>{/foreach}</ul>
    </div>
{/if}
{if $validation.warnings|count}
    <ul class="exp-muted exp-validation-warnings">{foreach $validation.warnings as $line max 20}<li>{$line|wash}</li>{/foreach}</ul>
{/if}
</div>
