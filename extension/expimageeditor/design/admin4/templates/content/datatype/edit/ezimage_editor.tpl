{* expimageeditor: the editor beside a stored image (included by admin4's content/datatype/edit/ezimage.tpl). This copy
   takes the place of admin4's, which opens ezie; ezie is not offered while expimageeditor is active.

   Shown to users with the policy expimageeditor/edit; the save view checks the draft and the edit access itself.
   The settings come once per page from expimageeditor/config.tpl. The button stays hidden until the script is there.
   Everything is a file or data: no inline script or style. *}
{if fetch( 'user', 'has_access_to', hash( 'module', 'expimageeditor', 'function', 'edit' ) )}
{include uri='design:expimageeditor/config.tpl'}
<button class="button expimageeditor-open" type="button" hidden
        data-expimageeditor-save="{concat( 'expimageeditor/save/', $attribute.id, '/', $attribute.version )|ezurl( 'no' )}"
        data-expimageeditor-src="{$attribute.content.original.url|ezroot( 'no' )}"
        data-expimageeditor-type="{$attribute.content.original.mime_type|wash}"
        data-expimageeditor-name="{$attribute.content.original.original_filename|wash}">{'Edit image'|i18n( 'extension/expimageeditor' )}</button>
{/if}
