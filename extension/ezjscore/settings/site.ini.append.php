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
# RequireUserLogin=true, normally the admin, where ezjscore/call needs a signed-in user.
# A front end (RequireUserLogin=false) does not need the entry. A siteaccess with
# RequireUserLogin=true whose pages call ezjscore/call anonymously adds the entry to its
# own settings; this file cannot leave it in, since extension settings rank above
# siteaccess settings and a siteaccess could not take it back.

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
