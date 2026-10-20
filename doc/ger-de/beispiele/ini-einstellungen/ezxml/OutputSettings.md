---
title: ezxml.ini [OutputSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [OutputSettings] in ezxml.ini: HandlerClass, AliasClasses"
keywords: Einstellung, ezxml.ini, OutputSettings, INI, setting, HandlerClass, AliasClasses
type: generated
generated: exp:help:examples
---
# ezxml.ini [OutputSettings]

Der Block `[OutputSettings]` von `settings/ezxml.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/ezxml.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezxml.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezxml.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezxml.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HandlerClass

> Welcher Ausgabe-Handler verwendet wird, ezxhtml ist der Standard  
> mit ezplain lassen sich die rohen XML-Daten ansehen.  

Standard: `eZXHTMLXMLOutput`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[OutputSettings]
HandlerClass=eZXHTMLXMLOutput
*/ ?>
```

## AliasClasses

> HandlerClass=eZPlainXMLOutput  

Standard: ein Hash mit 1: `ezpdf=eZPDFXMLOutput`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[OutputSettings]
# ein leeres "AliasClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AliasClasses[]
AliasClasses[ezpdf]=eZPDFXMLOutput
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezxml.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/ezxml.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
