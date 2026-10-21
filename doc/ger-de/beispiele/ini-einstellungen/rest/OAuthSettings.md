---
title: rest.ini [OAuthSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [OAuthSettings] in rest.ini: TokenTTL, AuthorizeRequiresPolicy"
keywords: Einstellung, rest.ini, OAuthSettings, INI, setting, TokenTTL, AuthorizeRequiresPolicy
type: generated
generated: exp:help:examples
---
# rest.ini [OAuthSettings]

Der Block `[OAuthSettings]` von `settings/rest.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TokenTTL

> Lebensdauer von OAuth-Token/Autorisierungscode, in Sekunden  
> Standard ist eine Stunde  

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
TokenTTL=3600
*/ ?>
```

## AuthorizeRequiresPolicy

> enabled oder disabled. Enabled (der Standard): Ein angemeldeter Benutzer darf einen OAuth-Client unter oauth/authorize nur  
> mit der Richtlinie oauth/authorize autorisieren (wahlweise nach Siteaccess eingeschränkt); ohne sie erhält der Client error=access\_denied.  
> disabled: Jeder angemeldete Benutzer darf Clients autorisieren, wie vor 6.0.15. Verwenden Sie es nur kurz während eines  
> Upgrades, bis "php bin/php/addoauthauthorizepolicy.php" die Richtlinie gewährt hat (doc/eng-us/bc/6.0/oauth-authorize-policy.md).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
AuthorizeRequiresPolicy=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rest.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
