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

> Persönliche API-Schlüssel (Veröffentlichungsschlüssel): doc/eng-us/guides/api-keys.md.  
> Ein angemeldeter Benutzer mit der Richtlinie apikey/create erstellt Schlüssel auf der Seite API-Zugang (apikey/list) und  
> sendet einen als "Authorization: Bearer expk\_<id>\_<secret>". Die Anfrage läuft als Eigentümer des Schlüssels, beschränkt  
> auf die Bereiche des Schlüssels. Administratoren sehen und widerrufen jeden Schlüssel unter oauthadmin/keys.  

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ApiKeys

> enabled oder disabled. Disabled: Kein Schlüssel wird akzeptiert, und keiner lässt sich erstellen; die Seiten sagen das.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
ApiKeys=enabled
*/ ?>
```

## ExpiryChoices

> Die beim Erstellen eines Schlüssels angebotenen Lebensdauern, in Tagen. 0 = läuft nicht ab, nur angeboten, wenn  
> MaxExpiryDays 0 ist.  

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

> Die Lebensdauer, die beim Öffnen des Formulars gewählt ist.  

Standard: `90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
DefaultExpiryDays=90
*/ ?>
```

## MaxExpiryDays

> Die längste Lebensdauer, die ein Schlüssel haben darf, in Tagen; 0 = keine Grenze (Schlüssel, die nie ablaufen, sind erlaubt).  

Standard: `365`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
MaxExpiryDays=365
*/ ?>
```

## MaxKeysPerUser

> Wie viele nicht widerrufene oder abgelaufene Schlüssel ein Benutzer haben darf; 0 = keine Grenze.  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
MaxKeysPerUser=10
*/ ?>
```

## LastUsedInterval

> last\_used und last\_ip eines Schlüssels werden höchstens einmal in so vielen Sekunden geschrieben, damit ein viel genutzter Schlüssel  
> nicht bei jeder Anfrage in die Datenbank schreibt.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
LastUsedInterval=60
*/ ?>
```

## RateLimitPerMinute

> Anfragen pro Minute, die ein Schlüssel senden darf; 0 = keine Grenze. In APCu gezählt (pro PHP-FPM-Pool oder pro  
> Anwendungsserver); ohne APCu wird die Grenze nicht durchgesetzt, und oauthadmin/keys sagt das.  

Standard: `120`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
RateLimitPerMinute=120
*/ ?>
```

## Scopes

> Die Bereiche, die ein Schlüssel erhalten kann: <scope>=<module>/<function> der Richtlinie, die der Eigentümer dafür braucht,  
> geprüft beim Erstellen des Schlüssels und erneut bei jeder Anfrage.  
> Abfragen der GraphQL-API (/api/graphql), im Rahmen der Rechte des Eigentümers, Inhalte zu lesen  
> Mutationen der GraphQL-API (graphql.ini MutationsEnabled), zusammen mit graphql:read: Jede Mutation prüft weiterhin die  
> Richtlinien des Eigentümers an dem Inhalt, den sie ändert  

Standard: ein Hash mit 7: `read=content/read`, `publish=content/create`, `edit=content/edit`, `remove=content/remove`, `help=exphelp/context`, `graphql:read=content/read`, `graphql:write=content/edit`.

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
Scopes[help]=exphelp/context
Scopes[graphql:read]=content/read
Scopes[graphql:write]=content/edit
*/ ?>
```

## ScopeNames

Standard: ein Hash mit 7: `read=Read content`, `publish=Create and publish content`, `edit=Edit content`, `remove=Remove content`, `help=Read context help`, `graphql:read=Read content through GraphQL`, `graphql:write=Change content through GraphQL`.

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
ScopeNames[help]=Read context help
ScopeNames[graphql:read]=Read content through GraphQL
ScopeNames[graphql:write]=Change content through GraphQL
*/ ?>
```

## DefaultReadScope

> Der Bereich, den eine Route braucht: <controller class>\_<action>=<scope>. Eine GET- oder HEAD-Route, die nicht  
> aufgeführt ist, braucht DefaultReadScope; jede andere nicht aufgeführte Route wird für einen Schlüssel abgelehnt.  

Standard: `read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
DefaultReadScope=read
*/ ?>
```

## RouteScopes

Standard: ein Hash mit 7: `ezp7xRestContentController_CreateContentNode=publish`, `ezp7xRestContentController_UpdateContentNode=edit`, `ezp7xRestContentController_DeleteContentNode=remove`, `expHelpRestController_topic=help`, `expHelpRestController_manual=help`, `expGraphQLRestController_query=graphql:read`, `expGraphQLRestController_subscribe=graphql:read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiKeySettings]
# ein leeres "RouteScopes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RouteScopes[]
RouteScopes[ezp7xRestContentController_CreateContentNode]=publish
RouteScopes[ezp7xRestContentController_UpdateContentNode]=edit
RouteScopes[ezp7xRestContentController_DeleteContentNode]=remove
RouteScopes[expHelpRestController_topic]=help
RouteScopes[expHelpRestController_manual]=help
RouteScopes[expGraphQLRestController_query]=graphql:read
RouteScopes[expGraphQLRestController_subscribe]=graphql:read
*/ ?>
```

## RouteGuards

> Die Rechte des Eigentümers, die am Knoten geprüft werden, auf den eine schreibende Route wirkt: create (content/create der abgeschickten  
> classIdentifier unter dem abgeschickten parentNodeID), edit oder remove (die nodeId der Route).  

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

> Der REST-Pfad, den die Seite API-Zugang in ihren curl-Beispielen nach ApiPrefix zeigt. Leer: ein Lesezugriff auf einen Knoten  
> des Providers ezp, /ezp/v2/... mit der Erweiterung ezprestapi, sonst /ezp/v1/....  

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
