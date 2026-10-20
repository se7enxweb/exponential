---
title: mailpreferences.ini [ConsentSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ConsentSettings] in mailpreferences.ini: RetentionDays, PendingDays"
keywords: Einstellung, mailpreferences.ini, ConsentSettings, INI, setting, RetentionDays, PendingDays
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [ConsentSettings]

Der Block `[ConsentSettings]` von `settings/mailpreferences.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/mailpreferences.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mailpreferences.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RetentionDays

> Days the consent log of a person is kept after the account (or the last  
> preference of an address without account) is gone. 1095 = 3 years.  

Standard: `1095`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[ConsentSettings]
RetentionDays=1095
*/ ?>
```

## PendingDays

> Days a pending double opt-in waits for its confirmation.  

Standard: `7`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[ConsentSettings]
PendingDays=7
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mailpreferences.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
