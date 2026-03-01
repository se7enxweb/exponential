{* Feedback form - Full view *}

<section class="content-view-full">
    <article class="class-feedback-form">

        <div class="attribute-header">
            <h1>{$node.name|wash()}</h1>
        </div>

        {include name=Validation uri='design:content/collectedinfo_validation.tpl'
                 class='message-warning'
                 validation=$validation collection_attributes=$collection_attributes}

        <div class="attribute-short">
                {attribute_view_gui attribute=$node.data_map.description}
        </div>
        <form method="post" action={"content/action"|ezurl}>

        <div class="row">
            <div class="span4">
                <div class="attribute-first-name">
                    {attribute_view_gui attribute=$node.data_map.first_name css_class='span4' label=$node.data_map.first_name.contentclass_attribute.name}
                </div>
            </div>
            <div class="span4">
                <div class="attribute-last-name">
                    {attribute_view_gui attribute=$node.data_map.last_name css_class='span4' label=$node.data_map.last_name.contentclass_attribute.name}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="span8">
                <div class="attribute-email">
                    {attribute_view_gui attribute=$node.data_map.email css_class='span8' label=$node.data_map.email.contentclass_attribute.name}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="span8">
                <div class="attribute-subject">
                    {attribute_view_gui attribute=$node.data_map.subject css_class='span8' label=$node.data_map.subject.contentclass_attribute.name}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="span8">
                <div class="attribute-country">
                    <label>{$node.data_map.country.contentclass_attribute.name}</label>
                    {attribute_view_gui attribute=$node.data_map.country css_class='span8' label=$node.data_map.country.contentclass_attribute.name}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="span8">
                <div class="attribute-message">
                    {attribute_view_gui attribute=$node.data_map.message css_class='span8' label=$node.data_map.message.contentclass_attribute.name}
                </div>
            </div>
        </div>

        <div class="content-action">
            <input type="submit" class="btn btn-warning pull-right" name="ActionCollectInformation" value="{"Send form"|i18n("design/ezdemo/full/feedback_form")}" />
            <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
            <input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
            <input type="hidden" name="ViewMode" value="full" />
        </div>
        </form>

    </article>
</section>

