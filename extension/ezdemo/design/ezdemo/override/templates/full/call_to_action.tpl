{set scope=global persistent_variable=hash('top_menu', false(),
                                           'show_path', false(),
                                           'left_menu', false(),
                                           'extra_menu', false())}

<section class="content-view-full">
    <article class="class-call-to-action">
        <div class="attribute-header">
            <h1>{$node.name|wash()}</h1>
        </div>
        <div class="row">
            <div class="span7">
                {attribute_view_gui attribute=$node.data_map.description}
            </div>
            <div class="span5 form">
                <form method="post" action={"content/action"|ezurl}>
                    <div class="row">
                        <div class="span5">
                        {include name=Validation uri='design:content/collectedinfo_validation.tpl'
                                 class='message-warning'
                                 validation=$validation collection_attributes=$collection_attributes}
                        </div>
                        <div class="attribute-sender-first-name">
                            <div class="span2">
                                {$node.data_map.first_name.contentclass_attribute.name}
                            </div>
                            <div class="span3">
                                {attribute_view_gui attribute=$node.data_map.first_name}
                            </div>
                        </div>
                        <div class="attribute-sender-last-name">
                            <div class="span2">
                                {$node.data_map.last_name.contentclass_attribute.name}
                            </div>
                            <div class="span3">
                                {attribute_view_gui attribute=$node.data_map.last_name}
                            </div>
                        </div>
                        <div class="attribute-sender-email">
                            <div class="span2">
                                {$node.data_map.email.contentclass_attribute.name}
                            </div>
                            <div class="span3">
                                {attribute_view_gui attribute=$node.data_map.email}
                            </div>
                        </div>
                        <div class="attribute-sender-country">
                            <div class="span2">
                                {$node.data_map.country.contentclass_attribute.name}
                            </div>
                            <div class="span3">
                                {attribute_view_gui attribute=$node.data_map.country}
                            </div>
                        </div>
                        <div class="attribute-sender-comment">
                            <div class="span2">
                                {$node.data_map.comment.contentclass_attribute.name}
                            </div>
                            <div class="span3">
                                {attribute_view_gui attribute=$node.data_map.comment}
                            </div>
                        </div>
                    </div>
                    <div class="content-action">
                        <input type="submit" class="btn btn-warning pull-right" name="ActionCollectInformation" value="{if $node.data_map.action_button_label.has_content}{$node.data_map.action_button_label.data_text|wash( xhtml )}{else}{"Submit"|i18n("design/ezdemo/full/call_to_action")}{/if}" />
                        <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
                        <input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
                        <input type="hidden" name="ViewMode" value="full" />
                    </div>
                </form>
            </div>
        </div>
    </article>
</section>
