{set-block scope=root variable=subject}{"Collected information from %1"|i18n("design/standard/content/edit",,array($collection.object.name|wash))}{/set-block}

{* Use this line to specify the email in the template, can read this from the object to
   make it dynamic pr form
{set-block scope=root variable=email_receiver}nospam@ez.no{/set-block}
*}

{* Set this to redirect to another node
{set-block scope=root variable=redirect_to_node_id}2{/set-block}
*}

{"The following information was collected"|i18n("design/standard/content/edit")}:

{foreach $collection.attributes as $Attribute}
{$Attribute.contentclass_attribute_name|wash}:
	{attribute_result_gui view=info attribute=$Attribute mail=true()}
	{*show_fullpath is to allow the full path of the collected info to be shown in the admin mail sent WITHOUT it necessarily being shown to the
	user on the front end.*}
{/foreach}
