{def	$ShowFullPath=ezini( "Security", "ShowFullPath", 'module.ini' )
	$current_user=fetch('user','current_user')
	$inGroup=false()
}

{if $mail} {*set in content/collectedinfo/form only for mail*}
	{if eq(ezini( "Security", "FullPathMail", 'module.ini' ),"enabled")}
		{set $inGroup=true()}
	{/if}
{/if}

{if $attribute.data_text}
	{if and(eq($ShowFullPath,"enabled"),$inGroup|not)}
		{*No need to do this check if $inGroup is already true*}
		{def $FullPathGroups=ezini( "Security", "FullPathGroups", 'module.ini' )}
		{foreach $FullPathGroups as $group}
			{if $current_user.groups|contains($group)}
				{set $inGroup=true()}
				{break}
			{/if}
		{/foreach}
	{/if}
{$attribute.data_text|parsexml("OriginalFilename")|wash} {'succesfully uploaded.'|i18n( 'design/standard/content/datatype' )}
	{if $inGroup}
		{if eq($attribute.data_text|filecheck,true)}
			{if $mail} {*text*}
				{* This goes to the collectedinfo admin mail if enabled*}
http://{ezini( "SiteSettings", "SiteURL" )}{$attribute.data_text|parsexml("Filename")|wash|ezroot(no)}
			{else} {*html*}
					<br>{'Link'|i18n( 'design/standard/content/datatype' )}: <a href={$attribute.data_text|parsexml('Filename')|ezroot}>{$attribute.data_text|parsexml("OriginalFilename")}</a>
			{/if}
		{/if}
	{/if}
{else}
{'No file uploaded.'|i18n( 'design/standard/content/datatype' )}
{/if}