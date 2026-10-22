---
title: Assets mit Yarn und Encore bauen
audience: dxp-developer
category: dxp-development
order: 24
summary: Die beiden Asset-Builds eines DXP-Projekts - das eigene CSS und JavaScript der Website mit Webpack Encore und die Administrationsoberfläche - mit den Yarn-Skripten, was nach welcher Änderung neu gebaut wird und wie Sie ohne Node.js auf dem Server bereitstellen.
keywords: Assets, Webpack Encore, Yarn, Node.js 20, yarn dev, yarn build, yarn watch, yarn ibexa:build, assets:install, encore_entry_link_tags, public/assets, corepack, assets
type: help
---
# Assets mit Yarn und Encore bauen

Diese Seite richtet sich an Entwickler, die die Stylesheets und Skripte eines DXP-Projekts bauen. Es gibt zwei Builds:
die eigenen Assets der Website und die der Administrationsoberfläche. Beide verwenden Webpack Encore und Yarn unter
Node.js 20 LTS (Anleitung des Skeletons, Abschnitte 8 und 9).

## Inhalt

1. [Vor jedem Build](#vor-jedem-build)
2. [Der Build der Website](#der-build-der-website)
3. [Der Build der Administration](#der-build-der-administration)
4. [Was nach welcher Änderung neu gebaut wird](#was-nach-welcher-änderung-neu-gebaut-wird)
5. [Die gebauten Dateien in Twig einbinden](#die-gebauten-dateien-in-twig-einbinden)
6. [Für die Produktion bauen](#für-die-produktion-bauen)

## Vor jedem Build

```bash
source ~/.nvm/nvm.sh && nvm use 20
corepack enable          # Yarn 1.22 as declared in package.json
yarn install             # first time, and after package.json changes
```

## Der Build der Website

Die Quellen liegen in `assets/js/`, `assets/scss/` und `assets/styles/`; die Ausgabe geht nach `public/assets/`.

```bash
yarn dev       # development, with source maps
yarn build     # production, minified
yarn watch     # rebuild on change
```

## Der Build der Administration

```bash
php bin/console assets:install --symlink --relative public   # first: fills public/bundles/ and var/encore/
yarn ibexa:build     # production
yarn ibexa:dev       # development
yarn ibexa:watch     # rebuild on change
```

`assets:install` schreibt `var/encore/ibexa.config.js`, `ibexa.config.setup.js` und `ibexa.config.manager.js`, die
Webpack sagen, wo die Einstiegspunkte jedes Bundles liegen. Ohne sie schlägt `yarn ibexa:build` mit "Module not found"
fehl.

## Was nach welcher Änderung neu gebaut wird

| Geändert | Ausführen |
|---|---|
| `assets/js/**`, `assets/scss/**`, `assets/styles/**` | `yarn dev` (oder `yarn watch`) |
| `package.json` | `yarn install && yarn dev` |
| `webpack.config.js` | `yarn dev` und `yarn ibexa:build` |
| Ein neues `se7enxweb/admin-ui` nach `composer update` | `yarn ibexa:build` |
| JavaScript oder SCSS in `Resources/public/` eines Bundles | `yarn ibexa:build` |
| Übersetzungstexte der Administration | `php bin/console bazinga:js-translation:dump public/assets --merge-domains` |

## Die gebauten Dateien in Twig einbinden

Das Bundle von Webpack Encore liefert Twig-Funktionen für die Einträge Ihrer `webpack.config.js`:

```twig
{{ encore_entry_link_tags('app') }}
{{ encore_entry_script_tags('app') }}
```

Verwenden Sie sie im Pagelayout, statt Pfade für `<link>` und `<script>` von Hand zu schreiben; Encore ergänzt die
Dateinamen mit Hash, sodass Browser nach einem Build neue Dateien erhalten, ohne Abfrageparameter an den URLs.

## Für die Produktion bauen

Die Assets der Administration werden nicht bei `composer install` gebaut; ein Produktionsserver braucht also kein
Node.js, wenn Sie auf einem Build-Rechner oder in der CI bauen und `public/assets/` und `public/bundles/` mit dem Code
bereitstellen. Bauen Sie auf dem Server, verwenden Sie dieselben Versionen von Node.js und Yarn wie in der Entwicklung
([Bereitstellen](dxp-bereitstellen.md)).

## Verwandte Seiten

- [Die Admin-Oberfläche](die-admin-oberflaeche.md)
- [Das Standarddesign und Ihr Theme](das-standarddesign-und-ihr-theme.md)
- [Versionen, die Sie brauchen](versionen-von-php-symfony-und-node.md#nodejs-und-yarn)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
