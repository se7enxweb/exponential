---
title: template.ini [CharsetSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CharsetSettings] in template.ini: DefaultTemplateCharset, DefaultTemplateLocale, AutoConvertOnSave"
keywords: Einstellung, template.ini, CharsetSettings, INI, setting, DefaultTemplateCharset, DefaultTemplateLocale, AutoConvertOnSave
type: generated
generated: exp:help:examples
---
# template.ini [CharsetSettings]

Der Block `[CharsetSettings]` von `settings/template.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/template.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/template.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/template.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/template.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultTemplateCharset

> Der Zeichensatz, der verwendet wird, wenn im Template keiner angegeben ist  

Standard: `utf-8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[CharsetSettings]
DefaultTemplateCharset=utf-8
*/ ?>
```

## DefaultTemplateLocale

> Die Locale für Zeichenoperationen, wenn im Template keine angegeben ist.  
> (C ist eine besondere Locale und bedeutet 7-Bit-ASCII). Beispiele für diese  
> Einstellung sind "german", "de\_DE.ISO-8859-1"; abhängig von Ihrem Betriebs-  
> system.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[CharsetSettings]
DefaultTemplateLocale=
*/ ?>
```

## AutoConvertOnSave

> Die folgende Einstellung wandelt das Template beim Speichern einer Template-Datei aus  
> der Administrationsoberfläche automatisch in den HTTPCharset aus  
> i18n.ini:CharacterSettings:HTTPCharset um und fügt den Template-Kopf bei Bedarf hinzu oder passt ihn an.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[CharsetSettings]
AutoConvertOnSave=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von template.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/template.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
