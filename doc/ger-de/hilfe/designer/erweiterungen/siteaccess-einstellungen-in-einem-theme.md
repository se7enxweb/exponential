---
title: "Siteaccess-Einstellungen in einem Theme"
audience: designer, developer
category: theme-extensions
order: 150
summary: Einstellungen, die nur gelten, solange ein Siteaccess läuft, in extension/<theme>/settings/siteaccess/<siteaccess>/ mitliefern, und Einstellungen für alle Siteaccesses eines Themes in settings/override/ - am Beispiel von cjw_themes_jumper.
keywords: Siteaccess, Siteaccess-Einstellungen, settings/siteaccess, Einstellungen pro Website, Multisite, ext-siteaccess-override, cjw_starter, siteaccess settings, per-site settings, multi-site
type: help
---
# Siteaccess-Einstellungen in einem Theme

Diese Seite ist für Designer, deren Theme mit einer eigenen Website (einem Siteaccess) kommt oder die mehrere Websites
aus einer Installation betreiben, jede in ihrer eigenen Erweiterung.

## Zwei Orte

| Ordner im Theme | Gilt |
|---|---|
| `settings/siteaccess/<siteaccess>/` | Nur solange dieser Siteaccess läuft |
| `settings/override/` | Solange ein Siteaccess läuft, der in dieser Erweiterung einen Ordner `settings/siteaccess/<name>/` hat (seit Januar 2026) |

Der zweite Ort ist in [Einstellungen pro Website innerhalb von Erweiterungen](../../../../eng-us/features/6.0/multi-site-ini-overrides.md)
beschrieben. Sein Ablagename, wie ihn die Debug-Ausgabe zeigt, ist `ext-siteaccess-override:<extension>`.

## Ein echtes Beispiel

`cjw_themes_jumper` liefert `settings/siteaccess/cjw_starter/` mit Append-Dateien für `collect.ini`, `explayouts.ini`,
`module.ini`, `override.ini` und `site.ini` mit. Seine Einstellungen für die Informationssammlung werden zum Beispiel nur
im Starter-Siteaccess gelesen, sodass die Formulare des Themes keine andere Website der Installation verändern.

## Schritt für Schritt

1. Legen Sie den Ordner an: `mkdir -p extension/mytheme/settings/siteaccess/mysite`.
2. Fügen Sie `site.ini.append.php` mit dem hinzu, was nur diese Website braucht:

   ```php
   <?php /* #?ini charset="utf-8"?

   [DesignSettings]
   SiteDesign=mytheme

   */ ?>
   ```

3. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
4. Prüfen Sie den Wert für diesen Siteaccess:

   ```bash
   ./console exp:ini where site.ini/DesignSettings/SiteDesign -s mysite --allow-root-user
   ```

   Erwartet: Ihre Datei unter den Dateien und `mytheme` als geltender Wert.
5. Öffnen Sie den Siteaccess in einem Browser und prüfen Sie, dass das Design verwendet wird.

## Gut zu wissen

- Den Siteaccess selbst (seinen Namen in `site.ini [SiteAccessSettings]`, die Zuordnung über Host oder URL) richtet die
  Installation ein, nicht das Theme. Ihr README erklärt die Zeilen, die hinzuzufügen sind.
- Liefern Sie keinen Siteaccess-Ordner mit, der wie ein üblicher Siteaccess heißt (`site`, `admin`), außer das Theme ist
  wirklich dafür gedacht; er würde Websites ändern, bei denen der Nutzer das nicht erwartet.

## Verwandte Seiten

- [Designs in einer Multisite-Installation](../theme-design/designs-in-einer-multisite-installation.md)
- [Einstellungsdateien in einer Erweiterung](einstellungsdateien-in-einer-erweiterung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
