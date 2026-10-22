---
title: Der Verzeichnisaufbau des Projekts
audience: dxp-developer
category: dxp-development
order: 14
summary: Eine Übersicht über die Ordner eines DXP-Projekts - src, config, templates, assets, public, var, vendor - was wohin gehört, was Flex schreibt, was Sie committen und was Sie nie committen.
keywords: Verzeichnisaufbau, Projektstruktur, src, config, templates, themes, assets, public, var, vendor, config/packages, Flex, gitignore, Namensraum App, directory layout, project structure, App namespace
type: help
---
# Der Verzeichnisaufbau des Projekts

Diese Seite richtet sich an Entwickler, die zum ersten Mal ein DXP-Projekt öffnen. Sie zeigt die Ordner aus Abschnitt
2 der Anleitung des Skeletons ("Key Directories") und sagt, was Sie ändern, was Flex verwaltet und was außerhalb von Git
bleibt.

## Inhalt

1. [Die Übersicht](#die-übersicht)
2. [Wohin Ihre Arbeit gehört](#wohin-ihre-arbeit-gehört)
3. [Was Flex schreibt](#was-flex-schreibt)
4. [Was Sie committen](#was-sie-committen)
5. [Herausfinden, woher eine Einstellung kommt](#herausfinden-woher-eine-einstellung-kommt)

## Die Übersicht

```text
project-root/
  src/                    your PHP code, namespace App\ (controllers, services, commands)
    Kernel.php            the application kernel
  config/
    packages/             bundle configuration (ibexa.yaml, security.yaml, ...)
    routes/               route definitions
    services.yaml         your service definitions
    jwt/                  the JWT keypair (generated, never committed)
  templates/
    themes/               Twig templates by theme
  assets/
    js/  scss/  styles/   site JavaScript and stylesheet sources
  public/                 the web root
    index.php             the only PHP entry point
    assets/               built site assets (Webpack Encore output)
    bundles/              bundle public files (symlinked by assets:install)
    var/                  stored files and image variations
  translations/           your translation files
  var/
    cache/  log/          Symfony cache and logs
    data_<env>.db         the SQLite database, if you use SQLite
  vendor/                 Composer packages
```

## Wohin Ihre Arbeit gehört

| Sie schreiben | Legen Sie es ab in |
|---|---|
| Einen Controller, einen Service, einen Event-Subscriber, einen Konsolenbefehl | `src/` (geladen als `App\`) |
| Die Konfiguration eines Bundles | `config/packages/<bundle>.yaml` |
| Ihre Routen | `config/routes/` |
| Twig-Templates Ihres Themes | `templates/themes/<theme>/` ([Twig und die Design-Engine](twig-templates-und-die-design-engine.md)) |
| Stylesheets und JavaScript | `assets/`, gebaut von Encore ([Assets bauen](assets-mit-yarn-und-encore-bauen.md)) |
| Eine Datenbankänderung eigener Entities | eine Doctrine-Migration ([Doctrine-Migrationen](doctrine-migrationen.md)) |

Ändern Sie nie Dateien unter `vendor/`. Um ein Paket zu ändern, erweitern oder dekorieren Sie seine Services in `src/`
oder tragen die Änderung zum Paket bei ([Zu den Forks beitragen](zu-den-forks-beitragen.md)).

## Was Flex schreibt

Wird ein Paket mit einem Rezept installiert, ergänzt Symfony Flex seine Konfiguration unter `config/`, seine Routen
unter `config/routes/`, seine Standardwerte für die Umgebung in `.env` und vermerkt das Rezept in `symfony.lock`. Das
DXP-Skeleton liest Rezepte aus `ibexa/recipes`, `se7enxweb/sevenx-recipes` und den Standards von Flex
([Das Metapaket lesen](das-metapaket-lesen.md#das-skeleton-abschnitt-für-abschnitt)). Committen Sie, was Flex schreibt;
es ist von da an die Konfiguration Ihres Projekts.

```bash
composer recipes                       # which recipes are installed, and which have updates
composer recipes:update <package>      # apply a newer recipe version (review the diff)
```

## Was Sie committen

| Committen | Nie committen |
|---|---|
| `src/`, `config/` (ohne `config/jwt/*.pem`), `templates/`, `assets/`, `translations/` | `.env.local` und andere Dateien mit Geheimnissen |
| `composer.json`, `composer.lock`, `symfony.lock` | `vendor/`, `node_modules/` |
| `package.json`, `yarn.lock`, `webpack.config.js` | `var/`, `public/var/`, gebaute `public/assets/` (außer Sie stellen sie absichtlich per Git bereit) |
| `.env` (nur Standardwerte) | die JWT-Schlüssel, Datenbank-Dumps |

## Herausfinden, woher eine Einstellung kommt

```bash
php bin/console debug:config ibexa                     # the resolved configuration of the content packages
php bin/console exponential:debug:config-resolver <parameter> --siteaccess=site
php bin/console debug:router | grep adminui            # which routes exist
php bin/console debug:container <service id>           # where a service is defined
```

## Verwandte Seiten

- [Siteaccesses](siteaccesses-site-und-admin.md)
- [Installation mit dem Skeleton](installation-mit-dem-skeleton.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
