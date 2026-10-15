{* The Upload button of the node view's actions (node/view/full.tpl of design/admin, node/view/a4_full_body.tpl of
   design/admin4; admin4l finds it through admin4): a link to ezmultiupload's upload page for this node, shown only
   when the ezmultiupload module is enabled and reachable here and fetch( 'ezmultiupload', 'can_upload' ) says the
   current user can upload at this node (expMultiuploadService::canUploadAt(): the ezmultiupload/upload policy,
   read and content/create access, a container or a configured upload class or node). Published objects only, so a
   draft or the edit pages never show it. Styled by .a4-btn of components.css, the icon from Bootstrap Icons.
   design/admin4 has an identical copy (admin4 is self-contained; AdminNodeUploadButtonTest keeps them equal). *}
{def $exp_can_upload = false()}
{if and( is_set( $node.node_id ), eq( $node.object.status, 1 ) )}
    {if ezmodule( 'ezmultiupload/upload' )}{set $exp_can_upload = fetch( 'ezmultiupload', 'can_upload', hash( 'node_id', $node.node_id ) )}{/if}
{/if}
{if $exp_can_upload}
    <a class="a4-btn a4-node-upload" href={concat( 'ezmultiupload/upload/', $node.node_id )|ezurl} title="{'Upload files to this location.'|i18n( 'design/admin/node/view/full' )|wash}"><span class="a4-icon a4-bi-upload" aria-hidden="true"></span>{'Upload'|i18n( 'design/admin/node/view/full' )}</a>
{/if}
{undef $exp_can_upload}
