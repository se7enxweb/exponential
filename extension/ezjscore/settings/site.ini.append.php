<?php /* #?ini charset="utf-8"?


[TemplateSettings]
ExtensionAutoloadPath[]=ezjscore

[RegionalSettings]
TranslationExtensions[]=ezjscore


[SSLZoneSettings] 
ModuleViewAccessMode[ezjscore/*]=keep


# Permissions to the specific ajax calls are handled inside the ezjscore/call
# view, so only comment this out if you want to disable it!
[RoleSettings]
PolicyOmitList[]=ezjscore/hello
PolicyOmitList[]=ezjscore/call

[SiteAccessSettings]
AnonymousAccessList[]=ezjscore/hello
# ezjscore/call is not listed. AnonymousAccessList only matters on a siteaccess with
# RequireUserLogin=true, normally the admin: there it let anonymous users past the login,
# and PolicyOmitList[]=ezjscore/call above skips the policy check, so ezjscnode::load,
# ezjscnode::subtree and ezjsc::search answered them with node data and search results.
# A front end (RequireUserLogin=false) does not need the entry. A siteaccess cannot reset
# it either, since extension settings rank above siteaccess settings.

# Cache item entry (for eZ Publish 4.3 and up)
[Cache]
CacheItems[]=ezjscore

[Cache_ezjscore]
name=eZJSCore Public Packer cache
id=ezjscore-packer
tags[]=content
tags[]=template
path=public
isClustered=true
class=ezjscCacheManager



*/ ?>
