---
title: site.ini [TemplateBridgeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TemplateBridgeSettings] in site.ini: ConverterList, EngineSuffixList, RendererList, ParameterOperatorList"
keywords: Einstellung, site.ini, TemplateBridgeSettings, INI, setting, ConverterList, EngineSuffixList, RendererList, ParameterOperatorList
type: generated
generated: exp:help:examples
---
# site.ini [TemplateBridgeSettings]

Der Block `[TemplateBridgeSettings]` von `settings/site.ini`, mit 4 Einstellung(en).

> CompileIncludeNodeTree\[\]=final  
> CompileIncludeNodeTree\[\]=transformed  
> CompileIncludeNodeTree\[\]=static  
> CompileIncludeNodeTree\[\]=combined  
> CompileIncludeNodeTree\[\]=original  
> Die Konverter von exp:template:convert (lib/templatebridge/classes/exptemplatedesignconverter.php):  
> ConverterList\[<von>-<nach>\]=<Klasse> wandelt einen Template-Text von einer Engine in eine andere um.  
> Engines: tpl (Exponential-Templates, .tpl), stpl (Smarty-Templates, .stpl), twig (Twig-Templates,  
> .twig und .html.twig).  
> Die Klasse nimmt im Konstruktor ein eZTemplate und hat convert( $text, $name ) und warnings().  
> Eine Extension fügt eine Richtung mit einem eigenen Eintrag in ConverterList\[\] hinzu.  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ConverterList

Standard: ein Hash mit 4: `tpl-stpl=expSmartyConverter`, `stpl-tpl=expSmartyReverseConverter`, `tpl-twig=expTwigConverter`, `twig-tpl=expTwigReverseConverter`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateBridgeSettings]
# ein leeres "ConverterList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ConverterList[]
ConverterList[tpl-stpl]=expSmartyConverter
ConverterList[stpl-tpl]=expSmartyReverseConverter
ConverterList[tpl-twig]=expTwigConverter
ConverterList[twig-tpl]=expTwigReverseConverter
*/ ?>
```

## EngineSuffixList

> Template-Dateien anderer Engines, nach Dateiendung (die längste Endung gilt zuerst):  
> EngineSuffixList\[<endung>\]=<engine>. Das Template-System parst eine solche Datei als Stub  
> des Renderers der Engine, RendererList\[<engine>\]=<Klasse> (expTemplateEngines).  

Standard: ein Hash mit 3: `.stpl=stpl`, `.twig=twig`, `.html.twig=twig`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateBridgeSettings]
# ein leeres "EngineSuffixList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
EngineSuffixList[]
EngineSuffixList[.stpl]=stpl
EngineSuffixList[.twig]=twig
EngineSuffixList[.html.twig]=twig
*/ ?>
```

## RendererList

Standard: ein Hash mit 2: `stpl=expSmartyRenderer`, `twig=expTwigRenderer`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateBridgeSettings]
# ein leeres "RendererList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RendererList[]
RendererList[stpl]=expSmartyRenderer
RendererList[twig]=expTwigRenderer
*/ ?>
```

## ParameterOperatorList

> Operatoren ohne Template-Hinweise, die nur ihre Parameter lesen, nie ihre Eingabe,  
> für jede Engine (ParameterOperatorList\[\] in smarty.ini und twig.ini wird ebenfalls gelesen).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateBridgeSettings]
# ein leeres "ParameterOperatorList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ParameterOperatorList[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
