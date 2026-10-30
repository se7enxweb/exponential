---
title: rest.ini [ApiKeySettings]
audience: developer, admin
category: examples
summary: "The settings of [ApiKeySettings] in rest.ini: ApiKeys, ExpiryChoices, DefaultExpiryDays, MaxExpiryDays, MaxKeysPerUser, LastUsedInterval, RateLimitPerMinute, Scopes ..."
keywords: rest.ini, ApiKeySettings, INI, setting, ApiKeys, ExpiryChoices, DefaultExpiryDays, MaxExpiryDays, MaxKeysPerUser, LastUsedInterval, RateLimitPerMinute, Scopes, ScopeNames, DefaultReadScope, RouteScopes, RouteGuards, ExamplePath
type: generated
generated: exp:help:examples
---
# rest.ini [ApiKeySettings]

The block `[ApiKeySettings]` of `settings/rest.ini`, with 13 setting(s).

> Personal API keys (publishing keys): doc/eng-us/guides/api-keys.md.  
> A signed in user with the apikey/create policy makes keys on the API access page (apikey/list) and  
> sends one as "Authorization: Bearer expk\_<id>\_<secret>". The request runs as the key's owner, limited  
> to the key's scopes. Administrators see and revoke every key at oauthadmin/keys.  

Never edit `settings/rest.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/rest.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in an extension (read when the extension is active).

## ApiKeys

> enabled or disabled. Disabled: no key is accepted and none can be made; the pages say so.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
ApiKeys=enabled
*/ ?>
```

## ExpiryChoices

> The lifetimes offered when a key is made, in days. 0 = does not expire, offered only when  
> MaxExpiryDays is 0.  

Default: a list of 5: `7`, `30`, `90`, `365`, `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
# an empty "ExpiryChoices[]" first replaces the shipped list; without it the items are added
ExpiryChoices[]
ExpiryChoices[]=7
ExpiryChoices[]=30
ExpiryChoices[]=90
ExpiryChoices[]=365
ExpiryChoices[]=0
*/ ?>
```

## DefaultExpiryDays

> The lifetime chosen when the form opens.  

Default: `90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
DefaultExpiryDays=90
*/ ?>
```

## MaxExpiryDays

> The longest lifetime a key may have, in days; 0 = no limit (keys that never expire are allowed).  

Default: `365`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
MaxExpiryDays=365
*/ ?>
```

## MaxKeysPerUser

> How many keys that are not revoked or expired one user may have; 0 = no limit.  

Default: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
MaxKeysPerUser=10
*/ ?>
```

## LastUsedInterval

> last\_used and last\_ip of a key are written at most once in this many seconds, so a busy key does  
> not write the database on every request.  

Default: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
LastUsedInterval=60
*/ ?>
```

## RateLimitPerMinute

> Requests per minute one key may send; 0 = no limit. Counted in APCu (per PHP-FPM pool or per  
> application server); without APCu the limit is not enforced and oauthadmin/keys says so.  

Default: `120`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
RateLimitPerMinute=120
*/ ?>
```

## Scopes

> The scopes a key can be given: <scope>=<module>/<function> of the policy the owner needs for it,  
> checked when the key is made and again on every request.  
> Queries of the GraphQL API (/api/graphql), within the owner's rights to read content  
> Mutations of the GraphQL API (graphql.ini MutationsEnabled), with graphql:read: each mutation still checks the  
> owner's policies on the content it changes  

Default: a hash of 7: `read=content/read`, `publish=content/create`, `edit=content/edit`, `remove=content/remove`, `help=exphelp/context`, `graphql:read=content/read`, `graphql:write=content/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
# an empty "Scopes[]" first replaces the shipped list; without it the items are added
Scopes[]
Scopes[read]=content/read
Scopes[publish]=content/create
Scopes[edit]=content/edit
Scopes[remove]=content/remove
Scopes[help]=exphelp/context
Scopes[graphql:read]=content/read
Scopes[graphql:write]=content/edit
*/ ?>
```

## ScopeNames

Default: a hash of 7: `read=Read content`, `publish=Create and publish content`, `edit=Edit content`, `remove=Remove content`, `help=Read context help`, `graphql:read=Read content through GraphQL`, `graphql:write=Change content through GraphQL`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
# an empty "ScopeNames[]" first replaces the shipped list; without it the items are added
ScopeNames[]
ScopeNames[read]=Read content
ScopeNames[publish]=Create and publish content
ScopeNames[edit]=Edit content
ScopeNames[remove]=Remove content
ScopeNames[help]=Read context help
ScopeNames[graphql:read]=Read content through GraphQL
ScopeNames[graphql:write]=Change content through GraphQL
*/ ?>
```

## DefaultReadScope

> The scope a route needs: <controller class>\_<action>=<scope>. A GET or HEAD route that is not  
> listed needs DefaultReadScope; any other route that is not listed is refused for a key.  

Default: `read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
DefaultReadScope=read
*/ ?>
```

## RouteScopes

Default: a hash of 6: `ezp7xRestContentController_CreateContentNode=publish`, `ezp7xRestContentController_UpdateContentNode=edit`, `ezp7xRestContentController_DeleteContentNode=remove`, `expHelpRestController_topic=help`, `expHelpRestController_manual=help`, `expGraphQLRestController_query=graphql:read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
# an empty "RouteScopes[]" first replaces the shipped list; without it the items are added
RouteScopes[]
RouteScopes[ezp7xRestContentController_CreateContentNode]=publish
RouteScopes[ezp7xRestContentController_UpdateContentNode]=edit
RouteScopes[ezp7xRestContentController_DeleteContentNode]=remove
RouteScopes[expHelpRestController_topic]=help
RouteScopes[expHelpRestController_manual]=help
RouteScopes[expGraphQLRestController_query]=graphql:read
*/ ?>
```

## RouteGuards

> The owner's rights checked on the node a write route acts on: create (content/create of the posted  
> classIdentifier under the posted parentNodeID), edit or remove (the route's nodeId).  

Default: a hash of 3: `ezp7xRestContentController_CreateContentNode=create`, `ezp7xRestContentController_UpdateContentNode=edit`, `ezp7xRestContentController_DeleteContentNode=remove`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
# an empty "RouteGuards[]" first replaces the shipped list; without it the items are added
RouteGuards[]
RouteGuards[ezp7xRestContentController_CreateContentNode]=create
RouteGuards[ezp7xRestContentController_UpdateContentNode]=edit
RouteGuards[ezp7xRestContentController_DeleteContentNode]=remove
*/ ?>
```

## ExamplePath

> The REST path the API access page shows in its curl examples, after ApiPrefix. Empty: a node read  
> of the ezp provider, /ezp/v2/... with the ezprestapi extension, /ezp/v1/... otherwise.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
ExamplePath=
*/ ?>
```

## Related

- [All blocks of rest.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/rest.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
