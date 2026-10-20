---
title: requestclassifier.ini [RuleSet-traversal]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RuleSet-traversal] in requestclassifier.ini: Class, Patterns, Disabled"
keywords: Einstellung, requestclassifier.ini, RuleSet-traversal, INI, setting, Class, Patterns, Disabled
type: generated
generated: exp:help:examples
---
# requestclassifier.ini [RuleSet-traversal]

Der Block `[RuleSet-traversal]` von `settings/requestclassifier.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/requestclassifier.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/requestclassifier.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/requestclassifier.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/requestclassifier.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> Matched against the raw URI and the URI decoded once. Rules: double-encoded (%252e), null-byte (%00),  
> encoded-dot-dot (%2e%2e, ..%2f in any case), dot-dot (../ and ..\\).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[RuleSet-traversal]
Class=
*/ ?>
```

## Patterns

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[RuleSet-traversal]
# ein leeres "Patterns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Patterns[]
*/ ?>
```

## Disabled

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[RuleSet-traversal]
# ein leeres "Disabled[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Disabled[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von requestclassifier.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestclassifier.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
