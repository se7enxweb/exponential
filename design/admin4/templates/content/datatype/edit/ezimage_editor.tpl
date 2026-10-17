{* The image editor beside a stored image (included by content/datatype/edit/ezimage.tpl): shown only when the ezie
   extension is active and the user may use it (ezmodule( 'ezie/prepare' )); without it this template draws nothing.

   ezie ships its own copy of ezimage.tpl in design/standard, which admin4's copy hides; this is what that copy adds:
   the editor's scripts once per page (not again when ezie's save view renders the attribute anew with
   $ezie_ajax_response set) and the Edit button, a type="button" the editor binds to by .ezieEditButton and that
   posts nothing. *}
{if ezmodule( 'ezie/prepare' )}
{run-once}
    {if is_set( $ezie_ajax_response )|not()}
        {include uri='design:ezie/gui.tpl' attribute=$attribute}
    {/if}
{/run-once}
<input class="button ezieEdit ezieEditButton" type="button" name="ezieEdit[{array( 'ezie/prepare', $attribute.contentobject_id, $attribute.language_code, $attribute.id, $attribute.version )|implode( '/' )|ezurl( no )}]" id="ezieEdit_{$attribute.id}_{$attribute.version}_{$attribute.contentobject_id}" value="{'Edit'|i18n( 'design/standard/ezie' )}" />
{/if}
