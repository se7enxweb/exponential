{def $poll_node = fetch( 'content', 'node', hash( 'node_id', $block.custom_attributes.poll_node_id ) )
     $poll_object = $poll_node.object
     $question_attribute = $poll_object.data_map.question}
<div class="block-type-poll">

    <div class="attribute-header">
        <h2>{$block.name|wash()}</h2>
    </div>

    <div class="block-content">
        <form method="post" action={"content/action"|ezurl}>
            <input type="hidden" name="ContentNodeID" value="{$poll_object.main_node_id}" />
            <input type="hidden" name="ContentObjectID" value="{$poll_object.id}" />
            <input type="hidden" name="ViewMode" value="full" />

            <h3>{$question_attribute.content.name|wash()}</h3>

            {foreach $question_attribute.content.option_list as $index => $option}
                <label class="radio" for="poll_option_{$question_attribute.id}_{$option.id}"><input type="radio" name="ContentObjectAttribute_data_option_value_{$question_attribute.id}" value="{$option.id}"
               {if eq( $index, '0' )}checked="checked"{/if}
                id="poll_option_{$question_attribute.id}_{$option.id}" />&nbsp;{$option.value|wash()}</label>
            {/foreach}

            <div class="actions">
                <button type="submit" name="ActionCollectInformation" class="btn btn-warning">{'Vote'|i18n('design/ezflow/embed/poll')}</button>
            </div>
        </form>
    </div>

</div>
{undef}
