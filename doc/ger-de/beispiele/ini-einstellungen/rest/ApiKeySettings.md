---
title: rest.ini [ApiKeySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ApiKeySettings] in rest.ini: ApiKeys, ExpiryChoices, DefaultExpiryDays, MaxExpiryDays, MaxKeysPerUser, LastUsedInterval, RateLimitPerMinute, Scopes ..."
keywords: Einstellung, rest.ini, ApiKeySettings, INI, setting, ApiKeys, ExpiryChoices, DefaultExpiryDays, MaxExpiryDays, MaxKeysPerUser, LastUsedInterval, RateLimitPerMinute, Scopes, ScopeNames, DefaultReadScope, RouteScopes, RouteGuards, ExamplePath
type: generated
generated: exp:help:examples
---
# rest.ini [ApiKeySettings]

Der Block `[ApiKeySettings]` von `settings/rest.ini`, mit 13 Einstellung(en).

> Personal API keys (publishing keys): doc/eng-us/guides/api-keys.md.  
> A signed in user with the apikey/create policy makes keys on the API access page (apikey/list) and  
> sends one as "Authorization: Bearer expk\_<id>\_<secret>". The request runs as the key's owner, limited  
> to the key's scopes. Administrators see and revoke every key at oauthadmin/keys.  

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ApiKeys

> enabled or disabled. Disabled: no key is accepted and none can be made; the pages say so.  

Standard: `enabled`.

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

Standard: eine Liste mit 5: `7`, `30`, `90`, `365`, `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
# ein leeres "ExpiryChoices[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: `90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
DefaultExpiryDays=90
*/ ?>
```

## MaxExpiryDays

> The longest lifetime a key may have, in days; 0 = no limit (keys that never expire are allowed).  

Standard: `365`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
MaxExpiryDays=365
*/ ?>
```

## MaxKeysPerUser

> How many keys that are not revoked or expired one user may have; 0 = no limit.  

Standard: `10`.

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

Standard: `60`.

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

Standard: `120`.

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

Standard: ein Hash mit 4: `read=content/read`, `publish=content/create`, `edit=content/edit`, `remove=content/remove`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
# ein leeres "Scopes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Scopes[]
Scopes[read]=content/read
Scopes[publish]=content/create
Scopes[edit]=content/edit
Scopes[remove]=content/remove
*/ ?>
```

## ScopeNames

Standard: ein Hash mit 4: `read=Read content`, `publish=Create and publish content`, `edit=Edit content`, `remove=Remove content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
# ein leeres "ScopeNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ScopeNames[]
ScopeNames[read]=Read content
ScopeNames[publish]=Create and publish content
ScopeNames[edit]=Edit content
ScopeNames[remove]=Remove content
*/ ?>
```

## DefaultReadScope

> The scope a route needs: <controller class>\_<action>=<scope>. A GET or HEAD route that is not  
> listed needs DefaultReadScope; any other route that is not listed is refused for a key.  

Standard: `read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
DefaultReadScope=read
*/ ?>
```

## RouteScopes

Standard: ein Hash mit 3: `ezp7xRestContentController_CreateContentNode=publish`, `ezp7xRestContentController_UpdateContentNode=edit`, `ezp7xRestContentController_DeleteContentNode=remove`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
# ein leeres "RouteScopes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RouteScopes[]
RouteScopes[ezp7xRestContentController_CreateContentNode]=publish
RouteScopes[ezp7xRestContentController_UpdateContentNode]=edit
RouteScopes[ezp7xRestContentController_DeleteContentNode]=remove
*/ ?>
```

## RouteGuards

> The owner's rights checked on the node a write route acts on: create (content/create of the posted  
> classIdentifier under the posted parentNodeID), edit or remove (the route's nodeId).  

Standard: ein Hash mit 3: `ezp7xRestContentController_CreateContentNode=create`, `ezp7xRestContentController_UpdateContentNode=edit`, `ezp7xRestContentController_DeleteContentNode=remove`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
# ein leeres "RouteGuards[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RouteGuards[]
RouteGuards[ezp7xRestContentController_CreateContentNode]=create
RouteGuards[ezp7xRestContentController_UpdateContentNode]=edit
RouteGuards[ezp7xRestContentController_DeleteContentNode]=remove
*/ ?>
```

## ExamplePath

> The REST path the API access page shows in its curl examples, after ApiPrefix. Empty: a node read  
> of the ezp provider, /ezp/v2/... with the ezprestapi extension, /ezp/v1/... otherwise.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
ExamplePath=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rest.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
