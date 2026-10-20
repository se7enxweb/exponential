---
title: cronjob.ini [ScriptDocumentation]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ScriptDocumentation] in cronjob.ini: Link, DocumentationBase"
keywords: Einstellung, cronjob.ini, ScriptDocumentation, INI, setting, Link, DocumentationBase
type: generated
generated: exp:help:examples
---
# cronjob.ini [ScriptDocumentation]

Der Block `[ScriptDocumentation]` von `settings/cronjob.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Link

> Ein Link „Doku“ neben einem Skript unter Setup > Cronjobs. Ein Skript nennt seine Dokumentation  
> mit dem Tag @link seines Kopfes (oder seiner Klassendatei), eine URL oder einen Pfad wie  
> doc/eng-us/guides/sessions.md. Eine Installation kann pro Skript einen angeben oder ersetzen, über den  
> Dateinamen, wie ihn Scripts\[\] hat; none entfernt den Link, den der Kopf angibt.  
> Anleitung: doc/eng-us/features/6.0/cronjobs-console.md  
> Link\[session\_gc.php\]=https://intranet.example.com/operations/sessions  
> Link\[sitemap.php\]=none  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[ScriptDocumentation]
# ein leeres "Link[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Link[]
*/ ?>
```

## DocumentationBase

> Wo ein Pfad nachgeschlagen wird. Leer: die Dokumentation von Exponential auf GitHub.  
> Es werden nur http- und https-Links angezeigt.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[ScriptDocumentation]
DocumentationBase=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
