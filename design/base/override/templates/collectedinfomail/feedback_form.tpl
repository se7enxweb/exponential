{set-block scope=root variable=subject}{"Collected information from %1"|i18n("design/base",,array($collection.object.name))}{/set-block}

{set-block scope=root variable=email_receiver}{$object.data_map.recipient.content}{/set-block}

{* Set this to redirect to another node
{set-block scope=root variable=redirect_to_node_id}2{/set-block}
*}

{* An HTML mail with a plain-text part in design:mail/layout.tpl (content/collectedinfomail_body.tpl) *}
{include uri='design:content/collectedinfomail_body.tpl' collection=$collection ci_gui=false()
         ci_title="Collected information from %1"|i18n("design/base",,array($collection.object.name))
         ci_intro="The following information was collected"|i18n("design/base")}
