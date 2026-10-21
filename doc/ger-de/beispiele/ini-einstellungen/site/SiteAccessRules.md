---
title: site.ini [SiteAccessRules]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SiteAccessRules] in site.ini: Rules, DisabledModuleResult"
keywords: Einstellung, site.ini, SiteAccessRules, INI, setting, Rules, DisabledModuleResult
type: generated
generated: exp:help:examples
---
# site.ini [SiteAccessRules]

Der Block `[SiteAccessRules]` von `settings/site.ini`, mit 2 Einstellung(en).

> Wenn Sie manche Module oder Teile eines Moduls abschalten, können Sie die  
> Gruppe SiteAccessRules verwenden; sie legt eine Liste von Regeln fest, die  
> der Reihe nach ausgeführt werden.  
>
> Das Folgende ist ein Beispiel, wie man content/search  
> und das Modul rss abschaltet  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Rules

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessRules]
# ein leeres "Rules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Rules[]
*/ ?>
```

## DisabledModuleResult

> Was ein durch die Regeln abgeschaltetes Modul antwortet: notfound (die Seite eines  
> Moduls, das nicht existiert, 404, sodass nichts verrät, dass es da ist) oder  
> disabled (die Seite „deaktiviert“, Fehler 22).  

Standard: `notfound`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessRules]
DisabledModuleResult=notfound
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
