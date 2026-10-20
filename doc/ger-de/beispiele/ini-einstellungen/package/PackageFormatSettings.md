---
title: package.ini [PackageFormatSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PackageFormatSettings] in package.ini: RawXMLDatatypes, MaxDocumentBytes"
keywords: Einstellung, package.ini, PackageFormatSettings, INI, setting, RawXMLDatatypes, MaxDocumentBytes
type: generated
generated: exp:help:examples
---
# package.ini [PackageFormatSettings]

Der Block `[PackageFormatSettings]` von `settings/package.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RawXMLDatatypes

> The JSON package format (doc/eng-us/bc/6.0/package-json-format.md).  
> Datatypes whose attribute value is kept as an XML string in a JSON document (\["#xml", "..."\])  

Standard: eine Liste mit 1: `ezxmltext`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[PackageFormatSettings]
# ein leeres "RawXMLDatatypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RawXMLDatatypes[]
RawXMLDatatypes[]=ezxmltext
*/ ?>
```

## MaxDocumentBytes

> The largest JSON document of a package that is read, in bytes  

Standard: `67108864`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[PackageFormatSettings]
MaxDocumentBytes=67108864
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
