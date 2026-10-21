---
title: codetemplate.ini [Files]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Files] in codetemplate.ini: Templates, PHPFiles"
keywords: Einstellung, codetemplate.ini, Files, INI, setting, Templates, PHPFiles
type: generated
generated: exp:help:examples
---
# codetemplate.ini [Files]

Der Block `[Files]` von `settings/codetemplate.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/codetemplate.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/codetemplate.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/codetemplate.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/codetemplate.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Templates

Standard: ein Hash mit 2: `can-instantiate-class-list=templates/classcreatelist.ctpl`, `class-list-from-policy=templates/classlistfrompolicy.ctpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/codetemplate.ini.append.php
[Files]
# ein leeres "Templates[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Templates[]
Templates[can-instantiate-class-list]=templates/classcreatelist.ctpl
Templates[class-list-from-policy]=templates/classlistfrompolicy.ctpl
*/ ?>
```

## PHPFiles

Standard: eine Liste mit 3: `kernel/classes/ezcontentclass.php`, `kernel/classes/ezcontentobject.php`, `kernel/classes/ezcontentobjecttreenode.php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/codetemplate.ini.append.php
[Files]
# ein leeres "PHPFiles[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PHPFiles[]
PHPFiles[]=kernel/classes/ezcontentclass.php
PHPFiles[]=kernel/classes/ezcontentobject.php
PHPFiles[]=kernel/classes/ezcontentobjecttreenode.php
*/ ?>
```

## Siehe auch

- [Alle Blöcke von codetemplate.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/codetemplate.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
