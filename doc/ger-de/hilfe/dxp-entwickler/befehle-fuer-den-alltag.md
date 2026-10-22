---
title: Befehle für den Alltag
audience: dxp-developer
category: dxp-development
order: 37
summary: Die Befehle, die ein DXP-Entwickler jeden Tag ausführt, nach Situation gruppiert - nach dem Holen von Code, nach einer Template- oder Konfigurationsänderung, nach einer Änderung eines Inhaltstyps, nach einer Stylesheet-Änderung, wenn etwas veraltet aussieht - auf einer Seite.
keywords: Alltag, Befehle, Spickzettel, Cache leeren, Vorwärmen, Neuindexierung, yarn, Assets, GraphQL-Schema, Migrationen, symfony server, Routine, day to day, commands, cheat sheet, cache clear, warmup, reindex, routine
type: help
---
# Befehle für den Alltag

Diese Seite richtet sich an Entwickler, die den richtigen Befehl für den Moment möchten, ohne eine Anleitung zu lesen.
Jeder Befehl ist auf seiner eigenen Seite erklärt; folgen Sie im Zweifel den Links.

## Inhalt

1. [Nach dem Holen von Code](#nach-dem-holen-von-code)
2. [Nach einer Änderung](#nach-einer-änderung)
3. [Wenn etwas veraltet aussieht](#wenn-etwas-veraltet-aussieht)
4. [Dienste](#dienste)
5. [Dinge nachschlagen](#dinge-nachschlagen)

## Nach dem Holen von Code

```bash
composer install
php bin/console doctrine:migration:migrate --allow-no-migration
php bin/console cache:clear
yarn install && yarn dev                 # if package.json or assets changed
```

## Nach einer Änderung

| Sie haben geändert | Ausführen |
|---|---|
| Ein Twig-Template | nichts in `dev`; `cache:clear` in `prod` |
| YAML-Konfiguration oder einen Service | `php bin/console cache:clear` |
| Einen Inhaltstyp oder Feldtyp | `php bin/console ibexa:graphql:generate-schema`, `cache:clear` und eine Neuindexierung, wenn die Suche die Felder verwendet |
| Einstellungen von Bildvariationen | `php bin/console liip:imagine:cache:remove`, `cache:clear` |
| Stylesheets oder Skripte der Website | `yarn dev` (oder `yarn watch` laufen lassen) |
| Admin-Assets oder eine neue Version von admin-ui | `yarn ibexa:build` |
| `.env.local` | `cache:clear` |
| Eine Doctrine-Entity | `doctrine:migration:diff`, prüfen, `doctrine:migration:migrate` |

## Wenn etwas veraltet aussieht

```bash
php bin/console cache:clear
php bin/console cache:pool:clear cache.tagaware.filesystem      # or your CACHE_POOL
php bin/console fos:httpcache:invalidate:path / --all
php bin/console exponential:reindex                             # search results
php bin/console liip:imagine:cache:remove                       # images
```

Laden Sie den Browser dann ohne seinen Cache neu. Ist es immer noch veraltet, lesen Sie [Fehlersuche](fehlersuche-in-der-dxp.md).

## Dienste

```bash
symfony server:start -d && symfony server:log          # development server
systemctl reload php8.3-fpm                            # after PHP settings changed (your version)
systemctl reload nginx                                 # or apache2
php bin/console messenger:stop-workers                 # after a deploy, if workers run
```

## Dinge nachschlagen

```bash
php bin/console list exponential                       # the core's commands
php bin/console debug:router                           # routes
php bin/console debug:twig                             # Twig functions, filters, namespaces
php bin/console debug:config ibexa                     # resolved configuration
php bin/console exponential:debug:config-resolver languages --siteaccess=site
composer why <package>                                 # why a package is installed
```

## Verwandte Seiten

- [Konsolenbefehle und Umbenennungen](konsolenbefehle-und-umbenennungen.md)
- [Assets bauen](assets-mit-yarn-und-encore-bauen.md)
- [Fehlersuche](fehlersuche-in-der-dxp.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
