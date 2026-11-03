---
title: migration.ini [TriggerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TriggerSettings] in migration.ini: Trigger, Secret, AllowedAddresses, Lifetime, MaxRequestsPerMinute, Php, Timeout, AllowRootUser"
keywords: Einstellung, migration.ini, TriggerSettings, INI, setting, Trigger, Secret, AllowedAddresses, Lifetime, MaxRequestsPerMinute, Php, Timeout, AllowRootUser
type: generated
generated: exp:help:examples
---
# migration.ini [TriggerSettings]

Der Block `[TriggerSettings]` von `settings/migration.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Trigger

> Der signierte HTTP-Trigger: Eine Quellinstallation, die diese nur per FTP erreicht, führt die Migrationen hier über die  
> Ansicht migration/trigger aus (POST, JSON). Jede Anfrage ist mit HMAC-SHA256 mit dem Geheimnis signiert, das Secret  
> nennt, wird einmal verwendet (ihre Nonce), ist Lifetime Sekunden gültig und wird nur von AllowedAddresses\[\]  
> angenommen. Anleitung: doc/ger-de/anleitungen/migrationen-remote-deploy.md („FTP-Gegenstellen mit dem HTTP-Trigger“)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[TriggerSettings]
Trigger=disabled
*/ ?>
```

## Secret

> Das Geheimnis, mit dem die Anfragen signiert werden: ein Name aus dem Geheimnisspeicher (Einrichtung > Geheimnisse) oder  
> env:VARIABLE, mindestens 16 Zeichen; TriggerSecret der Quelle enthält denselben Wert. Nie das Geheimnis selbst.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[TriggerSettings]
Secret=
*/ ?>
```

## AllowedAddresses

> Die Adressen (oder CIDR-Bereiche, 192.0.2.0/24, 2001:db8::/32), von denen die Quelle verbindet. Leer: Keine Anfrage wird angenommen.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[TriggerSettings]
# ein leeres "AllowedAddresses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedAddresses[]
*/ ?>
```

## Lifetime

> Sekunden, die eine Signatur nach ihrer Ausstellung gültig ist.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[TriggerSettings]
Lifetime=300
*/ ?>
```

## MaxRequestsPerMinute

> Höchstens so viele Anfragen pro Minute (darüber 429).  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[TriggerSettings]
MaxRequestsPerMinute=30
*/ ?>
```

## Php

> Das PHP-CLI, das die Konsolenskripte ausführt, und wie viele Sekunden eines dauern darf.  

Standard: `php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[TriggerSettings]
Php=php
*/ ?>
```

## Timeout

Standard: `900`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[TriggerSettings]
Timeout=900
*/ ?>
```

## AllowRootUser

> enabled: Die Konsolenskripte laufen mit --allow-root-user (nur, wenn das PHP des Webservers als root läuft).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[TriggerSettings]
AllowRootUser=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von migration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
