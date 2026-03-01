{def $node = $block.valid_nodes[0]}
<div class="block-type-feedback-form">
    <div class="attribute-header">
        <h2>{$block.name|wash()}</h2>
    </div>
    <article>
        <form method="post" action={"content/action"|ezurl} class="contact-form">
            <fieldset>
                <label>
                    <span class="hidden">{$node.data_map.first_name.contentclass_attribute.name}</span>
                    {attribute_view_gui attribute=$node.data_map.first_name placeholder=$node.data_map.first_name.contentclass_attribute.name}
                </label>
                <label>
                    <span class="hidden">{$node.data_map.last_name.contentclass_attribute.name}</span>
                    {attribute_view_gui attribute=$node.data_map.last_name placeholder=$node.data_map.last_name.contentclass_attribute.name}
                </label>
                <label>
                    <span class="hidden">{$node.data_map.email.contentclass_attribute.name}</span>
                    {attribute_view_gui attribute=$node.data_map.email placeholder=$node.data_map.email.contentclass_attribute.name}
                </label>
                <label>
                    <span class="hidden">{$node.data_map.country.contentclass_attribute.name}</span>
                    {attribute_view_gui attribute=$node.data_map.country placeholder=$node.data_map.country.contentclass_attribute.name}
                </label>
                <label>
                    <span class="hidden">{$node.data_map.subject.contentclass_attribute.name}</span>
                    {attribute_view_gui attribute=$node.data_map.subject placeholder=$node.data_map.subject.contentclass_attribute.name}
                </label>
                <label>
                    <span class="hidden">{$node.data_map.message.contentclass_attribute.name}</span>
                    {attribute_view_gui attribute=$node.data_map.message placeholder=$node.data_map.message.contentclass_attribute.name}
                </label>
                <div class="actions">
                    <button type="submit" class="btn btn-warning pull-right" name="ActionCollectInformation">{"Send form"|i18n("design/ezdemo/block/feedback_form")}</button>
                    <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
                    <input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
                    <input type="hidden" name="ViewMode" value="full" />
                </div>
            </fieldset>
        </form>
    </article>
</div>
{undef $node}
