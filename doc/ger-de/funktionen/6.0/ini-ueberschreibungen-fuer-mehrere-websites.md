# Website-spezifische Einstellungen in Extensions (Hosting mehrerer Websites)

Diese Seite ist für Administratoren und Entwickler, die mehrere Websites aus einer Installation von Exponential
betreiben, jede Website in ihrer eigenen Extension. Seit Januar 2026 kann eine Extension, die eine Website enthält,
**Überschreibungseinstellungen mitbringen, die nur aktiv sind, während einer ihrer Siteaccesses läuft**, und das Leeren
des INI-Caches leert ihn für jedes Projekt der Installation.

Vorher bedeutete das Betreiben von zehn Websites aus einer Installation ein großes Verzeichnis `settings/override` für
alle oder zehn Siteaccess-Verzeichnisse unter `settings/`. Eine Website in ihrer eigenen Extension
(`extension/site_projectname`) konnte `settings/siteaccess/<name>/` mitbringen, hatte aber keinen Ort für Einstellungen,
die für alle ihre Siteaccesses oder für eine Sprachgruppe davon gelten.

## Einrichten

1. Sorgen Sie dafür, dass die Extension für ihren Siteaccess ein Verzeichnis `settings/siteaccess/<siteaccess>/` hat.
   Die neuen Verzeichnisse werden nur dann gesucht, sodass Extensions ohne Bezug nichts kosten.
2. Legen Sie Einstellungen für jeden Siteaccess der Extension in `settings/override/` ab:

   ```ini
   # extension/site_example/settings/override/site.ini.append.php
   <?php /*
   [SiteSettings]
   SiteName=Example, all languages

   [DesignSettings]
   SiteDesign=example
   */ ?>
   ```

3. Leeren Sie den INI-Cache:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ```

4. Laden Sie eine Seite des Siteaccess mit eingeschaltetem INI-Debugging (siehe die
   [Debug-Leiste](../../../eng-us/bc/6.0/debug-bar.md)) und suchen Sie das `settings/override/` der Extension unter den geladenen
   Dateien von `site.ini`. Der Platzierungsname einer solchen Einstellung ist `ext-siteaccess-override:<extension>`.

## Wohin die Dateien gehören

Für eine Extension `site_example` mit einem Siteaccess `example` oder mit Siteaccesses einer Gruppe, geschrieben
`example__eng` und `example__ger`, werden diese Verzeichnisse der Extension gelesen, während einer **ihrer** Siteaccesses
aktiv ist. Höchste Priorität zuerst:

| Verzeichnis | Gilt für |
|---|---|
| `extension/site_example/settings/override/` | jeden Siteaccess der Extension |
| `extension/site_example/settings/override__<group>/` | jeden Siteaccess, dessen Name mit `<group>__` beginnt |
| `extension/site_example/settings/siteaccess/<siteaccess>/` | nur diesen Siteaccess (wie bisher) |

Eine Einstellung in `override/` schlägt also dieselbe Einstellung in einem Siteaccess-Verzeichnis derselben Extension,
so wie das globale `settings/override/` alles schlägt. Das eigene `settings/override/` der Installation hat weiterhin
das letzte Wort über alle drei. Die Dateien heißen wie die normalen (`site.ini.append.php` und so weiter).

## Ein INI-Cache-Verzeichnis für jedes Projekt

`eZINI` berechnete sein Cache-Verzeichnis früher relativ zu seiner eigenen Datei. Jetzt nimmt es es aus
`$GLOBALS['eZINI_CONFIG_CACHE_DIR']` und berechnet den alten Standard nur, wenn nichts es gesetzt hat. Ein
Multi-Site-Wrapper kann die globale Variable vor dem Bootstrapping setzen, sodass jedes Projekt der Installation
dasselbe Cache-Verzeichnis verwendet; das Leeren des INI-Caches für ein Projekt leert ihn dann für alle. Setzen Sie sie
nicht, ändert sich nichts. Das Gegenteil, ein eigener INI-Cache für jede Website, ist
`site.ini [FileSettings] INICacheDir=site`: siehe
[Protokolle, INI-Cache und Ablaufzeitstempel pro Website](../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md).

## Optionale Beschleunigung für viele Website-Extensions

Wenn Sie die Konstante `EXP_SITE_STRUCTURE_EZ_INI_OVERRIDE_DIR_LIST` als `true` definieren (zum Beispiel in
`config.php`), werden die Überschreibungsverzeichnisse aller anderen Extensions, deren Verzeichnis mit `extension/site_`
beginnt, für die aktuelle Anfrage aus der Liste genommen. Bei Dutzenden von Website-Extensions spart das pro INI-Datei
einige Prüfungen, ob Dateien existieren. Lassen Sie sie aus, sofern Sie nicht viele Websites betreiben.

## Verwandte Seiten

- [Protokolle, INI-Cache und Ablaufzeitstempel pro Website](../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md)
- [Spezifikation: Platzierungen von INI-Überschreibungen](../../spezifikationen/6.0/ini-override-platzierungen.md)
- [Konsole: exp:ini](exp-ini-befehl.md), [Extensions in mehr als einem Ordner](zusaetzliche-extension-verzeichnisse.md)
- [Changelog 6.0.12](../../../changelogs/6.0/6.0.12.md)
- [Chronik: Januar 2026](../../../eng-us/history/2026/2026-01.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
