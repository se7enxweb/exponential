---
title: shopaccount.ini [AccountSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AccountSettings] in shopaccount.ini: Handler, Alias"
keywords: Einstellung, shopaccount.ini, AccountSettings, INI, setting, Handler, Alias
type: generated
generated: exp:help:examples
---
# shopaccount.ini [AccountSettings]

Der Block `[AccountSettings]` von `settings/shopaccount.ini`, mit 2 Einstellung(en).

> Einstellungen dazu, wie Benutzerkonten im Shop/Bezahlvorgang behandelt werden  
> default verwendet die internen Benutzerkonten des Systems  
> simple behandelt Kontoinformationen ohne Benutzerregistrierung  

Bearbeiten Sie `settings/shopaccount.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shopaccount.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shopaccount.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shopaccount.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Handler

Standard: `ezuser`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shopaccount.ini.append.php
[AccountSettings]
Handler=ezuser
*/ ?>
```

## Alias

> Handler=ezdefault  
> Erlaubt, einen Handler durch einen anderen zu ersetzen  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shopaccount.ini.append.php
[AccountSettings]
# ein leeres "Alias[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Alias[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von shopaccount.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shopaccount.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
