---
title: collect.ini [EmailSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [EmailSettings] in collect.ini: SendEmail, SendEmailList, SendEmailAttribute"
keywords: Einstellung, collect.ini, EmailSettings, INI, setting, SendEmail, SendEmailList, SendEmailAttribute
type: generated
generated: exp:help:examples
---
# collect.ini [EmailSettings]

Der Block `[EmailSettings]` von `settings/collect.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/collect.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/collect.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/collect.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/collect.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SendEmail

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[EmailSettings]
SendEmail=enabled
*/ ?>
```

## SendEmailList

> Ordnet Klassen-ID oder -Bezeichner einem Informationssammlungstyp zu  

Standard: ein Hash mit 1: `poll=disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[EmailSettings]
# ein leeres "SendEmailList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SendEmailList[]
SendEmailList[poll]=disabled
*/ ?>
```

## SendEmailAttribute

> Welches Attribut im Objekt bestimmt,  
> ob eine E-Mail gesendet wird.  
> Wenn gefunden, überschreibt die Einstellung alle anderen Einstellungen.  

Standard: `collection_email`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[EmailSettings]
SendEmailAttribute=collection_email
*/ ?>
```

## Siehe auch

- [Alle Blöcke von collect.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/collect.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
