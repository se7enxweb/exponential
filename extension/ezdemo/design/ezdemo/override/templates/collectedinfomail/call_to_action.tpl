{set-block scope=root variable=subject}{"Collected information from %1"|i18n("design/ezdemo/colledtedinfomail",,array($collection.object.name|wash))}{/set-block}

{set-block scope=root variable=email_receiver}{$object.data_map.recipient.content}{/set-block}

{set-block scope=root variable=redirect_to_node_id}{$object.data_map.feedback_page.content.main_node_id}{/set-block}

{"The following information was collected"|i18n("design/ezdemo/colledtedinfomail")}:

{section name=Attribute loop=$collection.attributes}
{$Attribute:item.contentclass_attribute_name|wash}:
{$Attribute:item.data_text|wash}


{/section}

