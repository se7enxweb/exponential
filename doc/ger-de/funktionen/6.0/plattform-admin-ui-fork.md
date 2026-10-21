# Die Administrationsoberfläche von Exponential Platform

Diese Seite ist für Entwickler und Administratoren von Exponential Platform v5 (der Symfony-basierten Plattform) und
ihren älteren Linien 2.x und 3.x. Sie beschreibt die Administrationsoberfläche, die Redakteure unter `/adminui/`
verwenden: Inhaltsbaum, Bearbeiten von Inhalten, Suche, Inhaltstypen, Benutzer und Rollen, die Medienbibliothek. Es geht
**nicht** um das Legacy-Admin-Design `admin3` von Exponential 6 (siehe
[die responsive Administrationsoberfläche](admin3-responsives-admin.md)).

| | |
|---|---|
| Repositorys | `se7enxweb/admin-ui` (Platform v5; Repository `admin-ui-7x`, auch als `admin-ui-ibexa` zu sehen), `se7enxweb/admin-ui-assets`, `se7enxweb/fieldtype-richtext`; für die Linie 3.x `se7enxweb/ezplatform-admin-ui`, `se7enxweb/ezplatform-admin-ui-assets`, `se7enxweb/ezplatform-richtext`, `se7enxweb/ezplatform-design-engine`, `se7enxweb/ezplatform-standard-design`, `se7enxweb/ezplatform-alloyeditor-element-width` |
| Chronik | [admin-ui-7x](../../../eng-us/history/ecosystem/admin-ui-7x.md) · [admin-ui-ibexa](../../../eng-us/history/ecosystem/admin-ui-ibexa.md) · [admin-ui-assets](../../../eng-us/history/ecosystem/admin-ui-assets.md) · [fieldtype-richtext-ibexa](../../../eng-us/history/ecosystem/fieldtype-richtext-ibexa.md) · [ezplatform-admin-ui](../../../eng-us/history/ecosystem/ezplatform-admin-ui.md) · [ezplatform-richtext](../../../eng-us/history/ecosystem/ezplatform-richtext.md) |

## Was ein Benutzer bekommt

- Eine Administrationsoberfläche, die im Browser-Tab, auf der Anmeldeseite und auf der Begrüßungsseite („Get to know
  Exponential Platform DXP“) Exponential Platform DXP zeigt, keinen anderen Produktnamen, und E-Mails zum Zurücksetzen
  des Passworts mit demselben Namen sendet.
- Einen Build, der offline aus den Paketen von se7enxweb funktioniert: `yarn ibexa:build` findet die gemeinsamen
  Ressourcen in `vendor/se7enxweb/admin-ui-assets`.
- Rich-Text-Bearbeitung, die ihre XSL-Stylesheets und ihren Webpack-Alias im Fork findet (`se7enxweb/fieldtype-richtext`,
  Release `5.0.0` am 19.04.2026).

## Installieren und bauen

Die Admin-UI ist eine Abhängigkeit des Metapakets `se7enxweb/exponential-platform-dxp`, Sie verlangen sie also nicht
selbst. Bauen Sie nach `composer install` die Ressourcen:

```bash
php bin/console assets:install --symlink --relative public
source ~/.nvm/nvm.sh && nvm use 22      # the Nexus v5 starter; the DXP skeleton guide says Node 20
yarn install
yarn ibexa:build
```

Öffnen Sie `https://<your host>/adminui/` und melden Sie sich mit dem Administratorkonto Ihrer Installation an
([DXP-Skeleton](plattform-dxp-skeleton.md), [Nexus-Starter](plattform-nexus-starter.md)).

**Wenn der Build eine Datei unter `vendor/ibexa/...` nicht findet**, hat das Projekt noch das Upstream-Paket neben dem
Fork installiert. Führen Sie `composer why ibexa/admin-ui-assets` aus, um zu finden, was es verlangt, und prüfen Sie, dass
die Liste `replace` des Forks wirkt (siehe die [Paketkarte](../../spezifikationen/6.0/platform-paketzuordnung.md)).

## Was der Fork ändert

1. **Marke.** Logo, Favicons, Seitentitel, Begrüßungstexte und E-Mail zum Zurücksetzen des Passworts von Exponential
   Platform DXP.
2. **Paketidentität.** Der Fork wird als `se7enxweb/admin-ui` installiert und erklärt die Upstream-Namen als ersetzt,
   sodass nichts in einem Projekt die Upstream-Kopie ein zweites Mal hereinziehen kann.
3. **Build-Pfade.** Die Konfiguration von Webpack Encore zeigt in `vendor/se7enxweb/...` statt in `vendor/ibexa/...`,
   sodass sich die Oberfläche baut, wenn nur die Pakete von se7enxweb installiert sind.

Die übrige Historie (mehr als tausend Commits) ist Upstream-Arbeit, die der Fork übernimmt: der Wechsel zu Symfony 6
(`b9575cf69`, 04.02.2025), PHP-8-Typangaben und strikte Typen durchgehend (IBX-9727, 2025), Zuordnung von Icons
(`1026f2c42`, 05.06.2025), Entfernung von Sass-Deprecations (`c0adbf5b1`, 22.05.2025), Entfernung der BC-Schicht mit
`class_alias` (`26f130a90`, 05.06.2024) und ein eigenständiges Paket für das Universal Discovery Widget (`15704ef6f`,
14.12.2023). Die Monatstabellen auf der [Seite des Repositorys](../../../eng-us/history/ecosystem/admin-ui-7x.md) führen die
umfangreichsten Änderungen jedes Monats auf.

## Releases des Teams von se7enxweb

| Release | Datum | Was es enthält |
|---|---|---|
| `v5.0.5.0` | 21.03.2026 | Paket in `se7enxweb/admin-ui` umbenannt, Anweisung `replace` für die Upstream-Pakete, Lizenzausdruck auf GPL 2.0 oder später geändert, Logo (SVG) und Favicons von Exponential Platform DXP, Templates für Anmeldung, Konto und Layout verwenden Logo und Namen |
| `v5.0.5.1` | 22.03.2026 | `src/bundle/Resources/encore/ibexa.config.setup.js` löst interne Pfade im Vendor-Speicher des Forks auf |
| `v5.0.5.2` | 25.03.2026 | Fehlende Übersetzungstexte für Beschriftungen der Steuerungen der Unterelementansicht |
| `v5.0.5.3` | 16.04.2026 | Die Encore-Konfigurationen (`ibexa.css.config.js`, `ibexa.js.config.js`, `ibexa.webpack.libs.config.js`) verwenden `vendor/se7enxweb/admin-ui-assets`; die englischen Übersetzungstexte sagen Exponential Platform DXP |
| `v5.0.4` (Repository `admin-ui-7x`) | 17.04.2026 | Favicons von se7enxweb (16x16, 32x32, ico) |

Changelog: [Releases der Admin-UI](../../../changelogs/extensions/admin-ui.md).

## Die Linie 3.x

`ezplatform-admin-ui` (Exponential Platform Admin v2) erhielt im September 2025 und April 2026 dieselbe Behandlung:

- Paketnamen und Lizenzen in `composer.json`, Webpack-Ressourcenpfade mit Paketnamen von se7enxweb;
- Ersetzung der Marke „Ibexa DXP“ im HTML-Titel und eine Korrektur der Logo-Farbe;
- die Encore-Konfigurationen verweisen auf die installierte Abhängigkeit `ezsystems/ezplatform-admin-ui-assets`;
- SCSS von `@import` auf `@use` / `@forward` umgestellt, mit ersetzten veralteten Farb- und Mathematikfunktionen (sodass
  es mit aktuellem Sass baut);
- PHP-Bedingung auf `^8.5` erweitert und eine Schicht `replace` für das Gegenstück von ezsystems.

## Grenzen

- Die Oberfläche braucht eine zum Projekt passende Version von Node.js (20 oder 22), Yarn 1.22 und genug Speicher für den
  Webpack-Build.
- Übersetzungstexte außer Englisch wurden nicht geändert; sie können noch den Upstream-Produktnamen zeigen.

## Verwandte Seiten

- Plattformfunktionen: [DXP-Skeleton](plattform-dxp-skeleton.md), [Layouts auf der Plattform](plattform-layouts-core-fork.md), [Nexus-Starter](plattform-nexus-starter.md), [Framework-Forks für PHP 8.5](plattform-php85-framework-forks.md), [Site-Bundles](plattform-site-bundles.md), [SQLite für Exponential Platform](plattform-sqlite-installation.md), [Legacy Bridge](legacy-bridge.md), [Datenbankverwaltung AdminNeo](adminneo-datenbankverwaltung.md)
- Spezifikationen: [Namen der Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md), [Paketkarte der Plattform](../../spezifikationen/6.0/platform-paketzuordnung.md), [SQLite-Installer der Plattform](../../spezifikationen/6.0/platform-sqlite-installer.md), [Bundle der Legacy Bridge](../../spezifikationen/6.0/legacy-bridge-bundle.md)
- Hinweise zur Aktualisierung: [Paket-Forks und Umbenennungen von Befehlen](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- Changelog: [Changelog der Plattform](../../../changelogs/extensions/exponential-platform.md)
- Chronik: [Übersicht über das Ökosystem](../../../eng-us/history/ecosystem.md), [Monate des Ökosystems](../../../eng-us/history/ecosystem/months/2026-04.md), [Änderungsverzeichnis](../../../eng-us/history/ledger/README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
