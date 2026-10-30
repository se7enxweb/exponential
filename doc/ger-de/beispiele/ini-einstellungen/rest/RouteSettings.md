---
title: rest.ini [RouteSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RouteSettings] in rest.ini: RouteSettingImpl, SkipFilter, OptionalAuthentication"
keywords: Einstellung, rest.ini, RouteSettings, INI, setting, RouteSettingImpl, SkipFilter, OptionalAuthentication
type: generated
generated: exp:help:examples
---
# rest.ini [RouteSettings]

Der Block `[RouteSettings]` von `settings/rest.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RouteSettingImpl

Standard: `ezpRestIniRouteFilter`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[RouteSettings]
RouteSettingImpl=ezpRestIniRouteFilter
*/ ?>
```

## SkipFilter

> Muster für den Überspring-Filter  
> SkipFilter\[\]=controller\_action;version  
> Ist der Versionsteil nicht angegeben, wird Version "1" angenommen.  

Standard: eine Liste mit 4: `ezpRestErrorController_show`, `ezpRestAuthController_basicAuth`, `ezpRestAuthController_oauthRequired`, `ezpRestOauthTokenController_handleRequest`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[RouteSettings]
# ein leeres "SkipFilter[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SkipFilter[]
SkipFilter[]=ezpRestErrorController_show
SkipFilter[]=ezpRestAuthController_basicAuth
SkipFilter[]=ezpRestAuthController_oauthRequired
SkipFilter[]=ezpRestOauthTokenController_handleRequest
*/ ?>
```

## OptionalAuthentication

> Routen, die nur authentifiziert werden, wenn die Anfrage Zugangsdaten vorlegt (einen Authorization-Header oder  
> einen Token- oder Schlüssel-Parameter); ohne solche laufen sie als anonymer Benutzer (oder \[Authentication\]  
> DefaultUserID). controller\_action. Eine Anfrage mit einem falschen oder abgelaufenen Token wird wie auf jeder anderen  
> Route abgelehnt, nie als anonym ausgeführt.  

Standard: eine Liste mit 1: `expGraphQLRestController_query`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[RouteSettings]
# ein leeres "OptionalAuthentication[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
OptionalAuthentication[]
OptionalAuthentication[]=expGraphQLRestController_query
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rest.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
