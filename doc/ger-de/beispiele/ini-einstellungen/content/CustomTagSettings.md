---
title: content.ini [CustomTagSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CustomTagSettings] in content.ini: AvailableCustomTags, IsInline, TeXSource"
keywords: Einstellung, content.ini, CustomTagSettings, INI, setting, AvailableCustomTags, IsInline, TeXSource
type: generated
generated: exp:help:examples
---
# content.ini [CustomTagSettings]

Der Block `[CustomTagSettings]` von `settings/content.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AvailableCustomTags

> Eigene Tags können Sie über das Tag <custom> in XML-Feldern verwenden.  
> Beispiel: <custom name="sub">Sub text</custom>  

Standard: eine Liste mit 5: `factbox`, `quote`, `strike`, `sub`, `sup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CustomTagSettings]
# ein leeres "AvailableCustomTags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableCustomTags[]
AvailableCustomTags[]=factbox
AvailableCustomTags[]=quote
AvailableCustomTags[]=strike
AvailableCustomTags[]=sub
AvailableCustomTags[]=sup
*/ ?>
```

## IsInline

Standard: ein Hash mit 3: `strike=true`, `sub=true`, `sup=true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CustomTagSettings]
# ein leeres "IsInline[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IsInline[]
IsInline[strike]=true
IsInline[sub]=true
IsInline[sup]=true
*/ ?>
```

## TeXSource

> Eigene Tags, deren Text TeX-Quelltext ist (z. B. eine LaTeX-Formel). Wird ein solcher  
> Text im Online Editor geöffnet, wird er so gelesen, wie TeX ihn liest: Ein nicht maskierter  
> %-Kommentar entfällt mit dem Rest seiner Zeile, dem Zeilenumbruch und der  
> Einrückung der nächsten Zeile (sonst wird der Zeilenumbruch zu einem Leerzeichen, und  
> der Kommentar verschluckt den Rest der Formel), und führende Leerzeichen entfallen,  
> statt als geschützte Leerzeichen gespeichert zu werden. Bei anderen eigenen Tags werden nur  
> die Zeilenumbrüche zu Leerzeichen. Standardmäßig ist keines gesetzt.  
> TeXSource\[<custom tag name>\]=true  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[CustomTagSettings]
# ein leeres "TeXSource[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TeXSource[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
