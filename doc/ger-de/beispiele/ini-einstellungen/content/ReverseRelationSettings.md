---
title: content.ini [ReverseRelationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ReverseRelationSettings] in content.ini: ProviderClassList"
keywords: Einstellung, content.ini, ReverseRelationSettings, INI, setting, ProviderClassList
type: generated
generated: exp:help:examples
---
# content.ini [ReverseRelationSettings]

Der Block `[ReverseRelationSettings]` von `settings/content.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ProviderClassList

> Klassen, die Verweise melden, die außerhalb von ezcontentobject\_link gespeichert sind, für die Prüfung von \[RemoveSettings\] und  
> \[HideSettings\] ReverseRelationCheck. Jede beantwortet die statische Methode reverseRelations( array $paths, int $limit )  
> (siehe Exponential\\Service\\ReverseRelationCheck::providerReferences()). Eine Klasse, die es nicht gibt, wird übersprungen.  
> Erweiterungen fügen ihre in ihrer eigenen content.ini.append.php hinzu, z. B. ProviderClassList\[\]=expLayoutsReverseRelationProvider  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ReverseRelationSettings]
# ein leeres "ProviderClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ProviderClassList[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
