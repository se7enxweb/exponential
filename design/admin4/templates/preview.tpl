{* Content (pre)view in content window. *}
{def $custom_actions = $node.object.content_action_list}
{if $custom_actions}
    <form method="post" action={'content/action'|ezurl}>
        <input type="hidden" name="TopLevelNode" value="{$node.object.main_node_id}" />
        <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
        <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}" />
{/if}

    {* lang and dir="rtl" around content in a right-to-left language (nothing for a left-to-right one) *}
    {def $exp_preview_language = cond( $node.object.available_languages|contains( $node.object.current_language ), $node.object.current_language, '' )}{set-block variable=exp_text_attrs}{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$exp_preview_language}{/set-block}{if $exp_text_attrs|ne( '' )}<div class="exp-content-text"{$exp_text_attrs}>{/if}
    {node_view_gui content_node=$node view='admin_preview'}
    {if $exp_text_attrs|ne( '' )}</div>{/if}{undef $exp_text_attrs $exp_preview_language}

{if $custom_actions}
        <div class="button-right">
        <div class="block">
            {* Custom content action buttons. *}
            {foreach $custom_actions as $custom_action}
                <input class="button" type="submit" name="{$custom_action.action}" value="{$custom_action.name}" />
            {/foreach}
        </div>
        </div>
    </form>
{/if}