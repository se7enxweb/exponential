---
title: editorpolicies.ini [EditorPolicySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [EditorPolicySettings] in editorpolicies.ini: ClassSets, RemoveDuplicates"
keywords: Einstellung, editorpolicies.ini, EditorPolicySettings, INI, setting, ClassSets, RemoveDuplicates
type: generated
generated: exp:help:examples
---
# editorpolicies.ini [EditorPolicySettings]

Der Block `[EditorPolicySettings]` von `settings/editorpolicies.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/editorpolicies.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/editorpolicies.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/editorpolicies.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/editorpolicies.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ClassSets

> Die verwendeten Klassengruppen: je eine Gruppe \[ClassSet\_<Name>\]. Eine Gruppe, deren Klassen die Installation  
> nicht hat, wird übersprungen, so passt dieselbe Datei zu jeder Installation.  

Standard: eine Liste mit 1: `media_site`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/editorpolicies.ini.append.php
[EditorPolicySettings]
# ein leeres "ClassSets[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ClassSets[]
ClassSets[]=media_site
*/ ?>
```

## RemoveDuplicates

> enabled | disabled. Enabled: Eine Richtlinie, die die Rolle zweimal hat (dasselbe Modul, dieselbe Funktion und  
> dieselben Begrenzungen), wird einmal entfernt, die älteste bleibt. Eine Richtlinie, die sich nur in ihren  
> Begrenzungen unterscheidet, ist nie doppelt.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/editorpolicies.ini.append.php
[EditorPolicySettings]
RemoveDuplicates=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von editorpolicies.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/editorpolicies.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
