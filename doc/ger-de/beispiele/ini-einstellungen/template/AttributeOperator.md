---
title: template.ini [AttributeOperator]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AttributeOperator] in template.ini: DefaultFormatter, OutputFormatter"
keywords: Einstellung, template.ini, AttributeOperator, INI, setting, DefaultFormatter, OutputFormatter
type: generated
generated: exp:help:examples
---
# template.ini [AttributeOperator]

Der Block `[AttributeOperator]` von `settings/template.ini`, mit 2 Einstellung(en).

> Um eine eigene Tag-Liste zu erstellen, übergeben Sie den Namen dieser Liste als ersten Parameter an  
> simpletags, z. B. simpletags( 'mytags' ), und legen dann eine Liste namens TagList\_<yourname> an,  
> wobei <yourname> der Name der Tag-Liste ist  
> Beispiel:  
> TagList\_mytags\[\]  
> Konfiguration des Template-Operators 'attribute'  

Bearbeiten Sie `settings/template.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/template.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/template.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/template.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultFormatter

Standard: `html`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[AttributeOperator]
DefaultFormatter=html
*/ ?>
```

## OutputFormatter

Standard: ein Hash mit 2: `html=ezpAttributeOperatorHTMLFormatter`, `text=ezpAttributeOperatorTextFormatter`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[AttributeOperator]
# ein leeres "OutputFormatter[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
OutputFormatter[]
OutputFormatter[html]=ezpAttributeOperatorHTMLFormatter
OutputFormatter[text]=ezpAttributeOperatorTextFormatter
*/ ?>
```

## Siehe auch

- [Alle Blöcke von template.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/template.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
