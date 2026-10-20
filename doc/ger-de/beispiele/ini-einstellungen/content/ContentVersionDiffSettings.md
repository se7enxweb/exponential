---
title: content.ini [ContentVersionDiffSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentVersionDiffSettings] in content.ini: UseSimplifiedXML"
keywords: Einstellung, content.ini, ContentVersionDiffSettings, INI, setting, UseSimplifiedXML
type: generated
generated: exp:help:examples
---
# content.ini [ContentVersionDiffSettings]

Der Block `[ContentVersionDiffSettings]` von `settings/content.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## UseSimplifiedXML

> Wenn aktiviert, wird der Vergleich von eZXMLText im vereinfachten XML-Eingabeformat mit Formatierungs-Tags erstellt.  
> Wenn deaktiviert, wird der Vergleich von eZXMLText nur mit reinem Text erstellt.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentVersionDiffSettings]
UseSimplifiedXML=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
