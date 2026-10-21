---
title: composer.json modernisieren
audience: developer, app-creator
category: extension-development
order: 120
summary: Geben Sie einer alten Erweiterung eine composer.json, die sie in Exponential 6 installiert, oder bringen Sie eine alte auf den aktuellen Stand - PHP-Bedingung, Installer, Lizenz, Autoren und Support - mit der Änderung, die expchangeclass für PHP 8.0 vorgenommen hat.
keywords: PHP-Bedingung, Installer, Lizenz, Autoren, Upgrade, composer.json, Composer, Packagist, PHP constraint, ezpublish-legacy-extension, legacy installer, license, SPDX, authors, upgrade
type: help
---
# composer.json modernisieren

Teil 4, Kapitel 12. Die meisten Erweiterungen aus der Zeit vor 2014 haben überhaupt keine `composer.json`: Sie wurden
als Archiv heruntergeladen und nach `extension/` entpackt. Die aus den Jahren 2014 bis 2020 haben meist eine, die für
einen älteren Installer und ein älteres PHP geschrieben wurde. Dieses Kapitel bringt beide Fälle auf den aktuellen
Stand. Die Bedeutung jedes Feldes wird in
[composer.json für eine Erweiterung](../teil-3-erweiterungsentwickler/composer-json-fuer-erweiterungen.md)
erklärt; hier gehen wir durch, was sich typischerweise ändern muss.

## Fall 1: keine composer.json

Legen Sie eine im Wurzelverzeichnis der Erweiterung an. Die kleinste nützliche Datei:

```json
{
    "name": "your-account/myoldext",
    "description": "My Old Extension: what it does, in one sentence.",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "require": {
        "php": "^8.0",
        "se7enxweb/ezpublish-legacy-installer": "*"
    },
    "extra": {
        "ezpublish-legacy-extension-name": "myoldext"
    }
}
```

- `name`: Ihr GitHub-Konto oder Ihre Organisation und der Repository-Name, kleingeschrieben. Wenn Sie die Erweiterung
  eines anderen in einem Fork aktualisieren, verwenden Sie Ihr eigenes Konto: Ein Packagist-Name gehört dem, der ihn
  registriert hat.
- `ezpublish-legacy-extension-name`: der Ordnername, den die Erweiterung hatte. Behalten Sie ihn genau bei, denn
  Einstellungen, Templates und andere Erweiterungen verweisen darauf.
- `license`: der SPDX-Bezeichner der Lizenz, die die Dateien bereits tragen. Wenn Sie die Arbeit eines anderen
  aktualisieren, steht Ihnen die Wahl der Lizenz nicht zu.

Prüfen Sie sie dann:

```bash
cd extension/myoldext
composer validate --no-check-publish
```

## Fall 2: eine alte composer.json

Das sind die Felder, die meist veraltet sind, und was Sie ändern sollten.

| Alt | Neu | Warum |
|---|---|---|
| `"php": ">=5.3.3"` | `"php": "^8.0"` (oder das älteste PHP, das Sie getestet haben) | der alte Wert behauptet eine Unterstützung, die Sie nie getestet haben |
| `"ezsystems/ezpublish-legacy-installer": "*"` | `"se7enxweb/ezpublish-legacy-installer": "*"` oder `"se7enxweb/exponential-legacy-installer": "*"` | die Installer, die die eigene `composer.json` des Exponential-Kernels verlangt |
| `"ezsystems/ezpublish-legacy": "..."` | `"se7enxweb/exponential": "^6.0"` oder nichts | das Kernel-Paket von Exponential 6; verlangen Sie es nur, wenn Ihr Code ein bestimmtes Release braucht |
| `"license": "GPL-2.0"` | `"license": "GPL-2.0-or-later"` oder `"GPL-2.0-only"` | der einfache Bezeichner `GPL-2.0` ist bei SPDX veraltet; verwenden Sie, was in den Dateiköpfen steht („or any later version“ bedeutet `-or-later`) |
| `"minimum-stability": "dev"` | entfernen | es gilt nur für das Root-Paket und führt Leser in die Irre |
| `"version": "1.2"` | entfernen oder bei jedem Tag mitführen | Packagist nimmt die Versionen aus den Tags |
| keine `authors` | jeder Autor, auch die ursprünglichen | Nennung der Urheber |
| kein `support` | URLs für `issues` und `source` | wo Benutzer Probleme melden |

## Eine echte Änderung: die PHP-Bedingung erweitern

Aus se7enxweb/expchangeclass, Commit 32196f0, „installs and runs on PHP 8.0, the oldest PHP Exponential 6
supports“:

```text
before:
"php": "^8.1 || ^8.2 || ^8.3 || ^8.4"

after:
"php": "^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4"
```

Die Commit-Nachricht erklärt, wie das begründet wurde: Der Code wurde mit PHPCompatibility für PHP 8.0 geprüft, mit
PHP 8.0.30 gelintet und geladen und auf einer vollständigen Installation von PHP 8.0 ausgeliefert. Erst dann wurde die
Bedingung erweitert. Erweitern Sie niemals eine Bedingung, die Sie nicht getestet haben.

> **Hinweis**
> In Composer bedeutet `^8.0` bereits „8.0 oder neuer, unter 9“. Die Liste `^8.0 || ^8.1 || ...` ist gleichwertig;
> manche Erweiterungen führen jede Minor-Version auf, um zu zeigen, welche getestet wurden. Beide Formen installieren
> sich auf PHP 8.5.

## Nennung der Urheber in composer.json

Wenn Sie die Erweiterung eines anderen aktualisieren oder forken, bleibt er in `authors`. se7enxweb/expchangeclass
nennt den Maintainer zuerst und den ursprünglichen Autor von eZChangeclass als Zweiten, mit der Rolle „Original
author of eZChangeclass“. Die `description` sagt dasselbe in Worten: „A hardened fork of eZChangeclass“. Mehr dazu in
[Namensnennung und Copyright-Hinweise](../teil-5-erweitern-forken-zurueckgeben/namensnennung-und-urheberrechtshinweise.md).

## Bibliotheken, die die Erweiterung mitliefert

Alte Erweiterungen liefern oft Bibliotheken von Dritten in einem Ordner `lib/` mit. Wenn Sie `composer.json`
hinzufügen, können Sie eine solche Bibliothek stattdessen nach `require` verschieben, sofern sie auf Packagist ist und
ihre aktuelle Version noch tut, was die Erweiterung braucht. Zwei Vorsichtsmaßnahmen:

- Eine Erweiterung, die der Legacy-Installer nach `extension/` installiert, verwendet für ihre Composer-Abhängigkeiten
  den Autoloader in `vendor/` der Website. Testen Sie die Erweiterung in einer mit Composer installierten Website,
  nicht nur in einer Kopie.
- Eine Bibliothek, die seit dem Schreiben der Erweiterung mehrere Major-Versionen durchlaufen hat, hat möglicherweise
  ihre API geändert. Lesen Sie zuerst ihr Changelog.

Wenn Sie die Bibliothek in der Erweiterung behalten, legen Sie ihre Lizenzdatei daneben und nennen Sie sie in
`ezinfo.php` mit einem `Includes`-Eintrag ([ezinfo.php und extension.xml modernisieren](ezinfo-und-extension-xml-modernisieren.md)).

## Prüfen

```bash
composer validate --no-check-publish
```

Installieren Sie die Erweiterung dann aus Ihrem Repository in eine Test-Website, bevor Sie sie auf Packagist
veröffentlichen. Die Schritte stehen in
[composer.json für eine Erweiterung](../teil-3-erweiterungsentwickler/composer-json-fuer-erweiterungen.md),
im Abschnitt „Installing it from a repository before Packagist“ (Installation aus einem Repository vor Packagist).

## Verwandte Seiten

- Zurück: [ezinfo.php und extension.xml modernisieren](ezinfo-und-extension-xml-modernisieren.md)
- Weiter: [CSP: Inline-Skripte in Dateien verschieben](csp-inline-skripte-auslagern.md)
- [Composer und Packagist](../teil-7-veroeffentlichen/composer-und-packagist.md)
- [Das Schema von composer.json](https://getcomposer.org/doc/04-schema.md) auf getcomposer.org
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
