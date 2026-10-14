{* One file of a package as a card (package/view/full's contents browser): what it is by its content
   (eZPackageFileBrowser::sniff()), its size, its SHA-256 and whether it matches the checksum package.json lists.
   $file: the viewed file row (path, size, media, mime, sha256, checksum, index). *}
<dl class="a4-facts pvf-card" data-media="{$file.media|wash}" data-checksum="{$file.checksum|wash}">
    <div><dt>{'Name'|i18n( 'design/admin/package' )}</dt><dd><code>{$file.path|wash}</code></dd></div>
    <div><dt>{'Size'|i18n( 'design/admin/package' )}</dt><dd>{$file.size|si( byte )} ({$file.size|wash} B)</dd></div>
    <div><dt>{'Type'|i18n( 'design/admin/package' )}</dt><dd><code>{$file.mime|wash}</code></dd></div>
    <div><dt>SHA-256</dt><dd><code class="pvf-sha">{$file.sha256|wash}</code></dd></div>
    <div><dt>{'Checksum'|i18n( 'design/admin/package' )}</dt><dd>{switch match=$file.checksum}
        {case match='match'}{'Matches package.json'|i18n( 'design/admin/package' )}{/case}
        {case match='mismatch'}<strong>{'Does not match package.json: the file was changed after the package was written'|i18n( 'design/admin/package' )}</strong>{/case}
        {case}{'Not listed (a classic package, or package.json lists no checksum for it)'|i18n( 'design/admin/package' )}{/case}
        {/switch}</dd></div>
</dl>
{if $file.media|ne( 'image' )}<p class="pvf-note">{'This file is not text, so it is not shown here; Download gives it.'|i18n( 'design/admin/package' )}</p>{/if}
