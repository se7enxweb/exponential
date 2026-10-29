---
title: site.ini [ReauthGroup_content]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ReauthGroup_content] in site.ini: Enabled, Rules"
keywords: Einstellung, site.ini, ReauthGroup_content, INI, setting, Enabled, Rules
type: generated
generated: exp:help:examples
---
# site.ini [ReauthGroup_content]

Der Block `[ReauthGroup_content]` von `settings/site.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Inhalt entfernen (auch in den Papierkorb) und Veröffentlichen. Standardmäßig aus; eingeschaltet unter setup/reauth.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_content]
Enabled=false
*/ ?>
```

## Rules

Standard: eine Liste mit 6: `content/removeobject ConfirmButton`, `content/removenode ConfirmButton`, `content/trash EmptyButton RemoveButton`, `content/edit PublishButton PublishNotNotifyButton`, `content/versionview PreviewPublishButton PreviewPublishNotNotifyButton`, `content/multiedit MultiPublishButton`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_content]
# ein leeres "Rules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Rules[]
Rules[]=content/removeobject ConfirmButton
Rules[]=content/removenode ConfirmButton
Rules[]=content/trash EmptyButton RemoveButton
Rules[]=content/edit PublishButton PublishNotNotifyButton
Rules[]=content/versionview PreviewPublishButton PreviewPublishNotNotifyButton
Rules[]=content/multiedit MultiPublishButton
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
