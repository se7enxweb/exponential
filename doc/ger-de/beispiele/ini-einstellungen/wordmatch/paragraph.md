---
title: wordmatch.ini [paragraph]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [paragraph] in wordmatch.ini: MatchString"
keywords: Einstellung, wordmatch.ini, paragraph, INI, setting, MatchString
type: generated
generated: exp:help:examples
---
# wordmatch.ini [paragraph]

Der Block `[paragraph]` von `settings/wordmatch.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/wordmatch.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/wordmatch.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/wordmatch.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/wordmatch.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MatchString

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/wordmatch.ini.append.php
[paragraph]
# ein leeres "MatchString[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MatchString[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von wordmatch.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/wordmatch.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
