---
title: collect.ini [DisplaySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DisplaySettings] in collect.ini: Display, DisplayList, DisplayAttribute, RedirectURL, RedirectURLList, RedirectURLAttribute"
keywords: Einstellung, collect.ini, DisplaySettings, INI, setting, Display, DisplayList, DisplayAttribute, RedirectURL, RedirectURLList, RedirectURLAttribute
type: generated
generated: exp:help:examples
---
# collect.ini [DisplaySettings]

Der Block `[DisplaySettings]` von `settings/collect.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/collect.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/collect.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/collect.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/collect.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Display

> result   - Display IC result, for instance poll result or your form data  
> redirect - Redirect to a specific url  
> node     - Redirect back to content node  

Standard: `result`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[DisplaySettings]
Display=result
*/ ?>
```

## DisplayList

> Matches class id or identifier to information collection type  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[DisplaySettings]
# ein leeres "DisplayList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DisplayList[]
*/ ?>
```

## DisplayAttribute

> DisplayList\[poll\]=result  

Standard: `collection_display`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[DisplaySettings]
DisplayAttribute=collection_display
*/ ?>
```

## RedirectURL

> The url to redirect to if DisplayHandling is set to redirect  

Standard: `/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[DisplaySettings]
RedirectURL=/
*/ ?>
```

## RedirectURLList

> Matches class id or identifier to information collection type  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[DisplaySettings]
# ein leeres "RedirectURLList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RedirectURLList[]
*/ ?>
```

## RedirectURLAttribute

Standard: `collection_redirect`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[DisplaySettings]
RedirectURLAttribute=collection_redirect
*/ ?>
```

## Siehe auch

- [Alle Blöcke von collect.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/collect.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
