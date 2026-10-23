{* expimageeditor: a new drawing for an image attribute without an image (included by admin4's
   content/datatype/edit/ezimage.tpl, whose own copy draws nothing). Offered to users with the policy
   expimageeditor/edit when the image datatype accepts SVG (image.ini AllowedMIMETypes[] with image/svg+xml,
   SanitizeSVG not disabled) and the editor may store it (expimageeditor.ini OutputMIMETypes[]). The SVG editor starts
   with an empty drawing of [EditorSettings] NewDrawingSize; the save view stores it as drawing.svg. *}
{if and( fetch( 'user', 'has_access_to', hash( 'module', 'expimageeditor', 'function', 'edit' ) ),
         ezini( 'ImageUploadSettings', 'AllowedMIMETypes', 'image.ini' )|contains( 'image/svg+xml' ),
         ezini( 'ImageUploadSettings', 'SanitizeSVG', 'image.ini' )|ne( 'disabled' ),
         ezini( 'EditorSettings', 'OutputMIMETypes', 'expimageeditor.ini' )|contains( 'image/svg+xml' ) )}
{include uri='design:expimageeditor/config.tpl'}
<button class="button expimageeditor-open" type="button" hidden
        data-expimageeditor-save="{concat( 'expimageeditor/save/', $attribute.id, '/', $attribute.version )|ezurl( 'no' )}"
        data-expimageeditor-src=""
        data-expimageeditor-type="image/svg+xml"
        data-expimageeditor-name="drawing.svg">{'New SVG drawing'|i18n( 'extension/expimageeditor' )}</button>
{/if}
