---
title: site.ini [ReauthGroup_delete]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ReauthGroup_delete] in site.ini: Enabled, Rules"
keywords: Einstellung, site.ini, ReauthGroup_delete, INI, setting, Enabled, Rules
type: generated
generated: exp:help:examples
---
# site.ini [ReauthGroup_delete]

Der Block `[ReauthGroup_delete]` von `settings/site.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Daten endgültig löschen: den Papierkorb leeren oder Elemente daraus entfernen, mehrere  
> Elemente auf einmal entfernen (BulkCount oder mehr) und die Sitzungsliste leeren (setup/session liegt in  
> system). Das Audit-Protokoll hat keine Seite, die es leert: nur die Konsole, die nie gefragt wird.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_delete]
Enabled=true
*/ ?>
```

## Rules

Standard: eine Liste mit 2: `content/trash EmptyButton RemoveButton`, `content/removeobject * bulk`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_delete]
# ein leeres "Rules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Rules[]
Rules[]=content/trash EmptyButton RemoveButton
Rules[]=content/removeobject * bulk
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
