---
title: content.ini [EditSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [EditSettings] in content.ini: ExtensionDirectories, AdditionalTemplates, AttributeTemplates"
keywords: Einstellung, content.ini, EditSettings, INI, setting, ExtensionDirectories, AdditionalTemplates, AttributeTemplates
type: generated
generated: exp:help:examples
---
# content.ini [EditSettings]

Der Block `[EditSettings]` von `settings/content.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ExtensionDirectories

> Eine Liste der Erweiterungen mit eigenen Handlern für das Bearbeiten von Inhalten.  
> Diese Handler müssen hier liegen: <extensions>/<extension-name>/content/<extension-name>handler.php  
> Und eine Klasse namens <extension-name>Handler mit der Funktion fetchInput enthalten  
> Diese Klasse muss eZContentObjectEditHandler implementieren ( kernel/classes/ezcontentobjectedithandler.php )  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[EditSettings]
# ein leeres "ExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectories[]
*/ ?>
```

## AdditionalTemplates

> Eine Liste zusätzlicher Templates, die aus dem Template content/edit.tpl eingebunden werden  
> Zum Beispiel  
> AdditionalTemplates\[\]=content/edit/autosave.tpl  
> bindet design:content/edit/autosave.tpl ein  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[EditSettings]
# ein leeres "AdditionalTemplates[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdditionalTemplates[]
*/ ?>
```

## AttributeTemplates

> Eine Liste von Templates, die in jedes Feld des Bearbeitungsformulars der Administrations-Designs eingebunden werden  
> (content/edit\_attribute.tpl), mit dem Feld als $attribute und den Variablen des Bearbeitungsformulars: direkt nach der  
> Beschriftung des Feldes oder zuerst in seinem Block, wenn das Feld ein eigenes Fieldset hat oder übersetzt wird. Für die Werkzeuge, die eine  
> Erweiterung einem Feld hinzufügt (expai: content/edit/expai\_attribute.tpl), damit sie mit der Seite gezeichnet werden und das Formular  
> sich nicht verschiebt, wenn die Skripte starten.  
> AttributeTemplates\[\]=content/edit/myextension\_attribute.tpl  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[EditSettings]
# ein leeres "AttributeTemplates[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AttributeTemplates[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
