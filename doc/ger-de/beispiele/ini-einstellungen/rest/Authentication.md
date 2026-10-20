---
title: rest.ini [Authentication]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Authentication] in rest.ini: RequireAuthentication, AuthenticationStyle, RequireHTTPS, DefaultUserID"
keywords: Einstellung, rest.ini, Authentication, INI, setting, RequireAuthentication, AuthenticationStyle, RequireHTTPS, DefaultUserID
type: generated
generated: exp:help:examples
---
# rest.ini [Authentication]

Der Block `[Authentication]` von `settings/rest.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RequireAuthentication

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[Authentication]
RequireAuthentication=enabled
*/ ?>
```

## AuthenticationStyle

Standard: `ezpRestOauthAuthenticationStyle`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[Authentication]
AuthenticationStyle=ezpRestOauthAuthenticationStyle
*/ ?>
```

## RequireHTTPS

> AuthenticationStyle=ezpRestBasicAuthStyle  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[Authentication]
RequireHTTPS=disabled
*/ ?>
```

## DefaultUserID

> Default user to authenticate when authentication is disabled or the route used doesn't need authentication  
> If left empty, site.ini/\[UserSettings\].AnonymousUserID will be used  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[Authentication]
DefaultUserID=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rest.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
