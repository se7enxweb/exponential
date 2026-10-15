{* admin4: the height control of the node view's tab box (#window-controls in node/view/a4_full_body.tpl and
   node/top_node_full.tpl): a reset button and the grip, a focusable separator that is dragged (mouse, finger, pen) or
   moved with the arrow keys. Hidden until admin4.js takes them on; without it the box keeps the browser's own resize
   grip. Nothing of the user's is written here: the node view is view cached, and the height (the eZPreference
   admin_node_preview_height) reaches the page on <html>, outside every cache-block (parts/a4_preference_attributes.tpl). *}
<div class="a4-tabs-height" hidden>
    <button type="button" class="a4-icon-btn a4-tabs-height-reset" hidden title="{'Default size'|i18n( 'design/admin/node/view/full' )|wash}" aria-label="{'Default size'|i18n( 'design/admin/node/view/full' )|wash}"><span class="a4-icon a4-bi-arrow-counterclockwise" aria-hidden="true"></span></button>
    <div class="a4-tabs-height-grip" role="separator" aria-orientation="horizontal" aria-controls="window-controls" tabindex="0" aria-label="{'Height of the tab panel'|i18n( 'design/admin/node/view/full' )|wash}" title="{'Drag, or use the arrow keys, to change the height'|i18n( 'design/admin/node/view/full' )|wash}"></div>
</div>
