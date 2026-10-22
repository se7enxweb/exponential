---
title: Ein DXP-Projekt aktualisieren
audience: dxp-developer
category: dxp-development
order: 39
summary: Ein DXP-Projekt aktuell halten - Composer- und Node-Pakete aktualisieren, was nach jeder Aktualisierung auszuführen ist, die Release Notes der Forks lesen, Versionen festlegen, wenn das Skeleton Entwicklungszweige erlaubt, und Aktualisierungen von Rezepten.
keywords: Aktualisierung, aktualisieren, composer update, yarn upgrade, Release Notes, se7enxweb-Forks, minimum-stability, Versionen festlegen, recipes:update, Migrationen, Neuindexierung, Sicherheitsaktualisierungen, update, release notes, pinning, security updates
type: help
---
# Ein DXP-Projekt aktualisieren

Diese Seite richtet sich an Entwickler, die ein DXP-Projekt aktuell halten. Abschnitt 16 der Anleitung nennt die
Befehle; diese Seite ergänzt die Reihenfolge der Arbeit und die Prüfungen, mit denen Sie eine Aktualisierung nicht
überrascht.

## Inhalt

1. [Vor einer Aktualisierung](#vor-einer-aktualisierung)
2. [Composer-Pakete aktualisieren](#composer-pakete-aktualisieren)
3. [Node-Pakete aktualisieren](#node-pakete-aktualisieren)
4. [Rezepte](#rezepte)
5. [Entwicklungszweige und festgelegte Versionen](#entwicklungszweige-und-festgelegte-versionen)
6. [Was Sie lesen sollten](#was-sie-lesen-sollten)

## Vor einer Aktualisierung

- Arbeiten Sie in einem Branch und an einer Kopie der Datenbank.
- Notieren Sie die aktuellen Versionen: `composer show "se7enxweb/*" "ibexa/*" > var/before-update.txt`.

## Composer-Pakete aktualisieren

```bash
composer update                                              # everything within constraints
composer update se7enxweb/exponential-platform-dxp-core     # a single package

php bin/console doctrine:migration:migrate --allow-no-migration
php bin/console cache:clear
php bin/console exponential:reindex                          # if the content model schema may have changed
```

Bauen Sie dann die Admin-Assets neu, wenn sich `se7enxweb/admin-ui` geändert hat
([Die Admin-Oberfläche](die-admin-oberflaeche.md#was-einen-neubau-braucht)), führen Sie die Tests aus und committen Sie
`composer.lock`:

```bash
git add composer.lock && git commit -m "Updated: Composer dependencies"
```

## Node-Pakete aktualisieren

```bash
yarn upgrade
yarn dev
git add yarn.lock && git commit -m "Updated: Node dependencies"
```

## Rezepte

Eine Paketaktualisierung kann ein neueres Flex-Rezept mitbringen. `composer recipes` listet die mit Aktualisierungen;
`composer recipes:update <package>` wendet eines an und zeigt den Diff. Prüfen Sie ihn: Er berührt Ihre Konfiguration.

## Entwicklungszweige und festgelegte Versionen

Das Skeleton setzt `minimum-stability: dev` und `prefer-stable: false`, und mehrere Pakete werden in Zweigen
`5.0.x-dev` verlangt ([Das Metapaket lesen](das-metapaket-lesen.md#das-skeleton-abschnitt-für-abschnitt)). Ein
`composer update` kann daher neue Commits bringen, nicht nur neue Tags. Für die Produktion:

- committen Sie `composer.lock` und stellen Sie nur mit `composer install` bereit;
- aktualisieren Sie bewusst, in einem Branch, und testen Sie vor dem Mergen;
- wo ein se7enxweb-Fork Tags hat, können Sie in Ihrer eigenen `composer.json` einen Tag-Bereich verlangen.

## Was Sie lesen sollten

| Quelle | Was sie Ihnen sagt |
|---|---|
| [Release Notes von exponential-platform-dxp](../../../changelogs/extensions/exponential-platform-dxp.md) | Änderungen des Metapakets |
| [Releases der Admin-Oberfläche](../../../changelogs/extensions/admin-ui.md) | Änderungen des Administrations-Forks |
| [Forks der Plattformpakete und Umbenennungen von Befehlen](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md) | Schritte beim Umstieg von Upstream-Namen |
| [Übersicht der Plattformpakete](../../spezifikationen/6.0/platform-paketzuordnung.md) | die neuesten Tags jedes Forks |

## Verwandte Seiten

- [Bereitstellen](dxp-bereitstellen.md)
- [Doctrine-Migrationen](doctrine-migrationen.md)
- [Fehlersuche](fehlersuche-in-der-dxp.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
