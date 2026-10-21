---
title: Als Erweiterung paketieren
audience: app-creator, developer
category: app-creation
order: 440
summary: Eine Anwendung in eine installierbare Erweiterung verwandeln - Aufbau des Repositorys, composer.json mit dem Erweiterungstyp, extension.xml und ezinfo.php, Lizenz und Copyright, Installationsschritte, Standardeinstellungen, Dokumentation und die Checkliste, bevor andere sie verwenden.
keywords: paketieren, Erweiterung, Lizenz, Installation, Verteilung, Anwendungsentwickler, packaging, extension, composer.json, ezpublish-legacy-extension, extension.xml, ezinfo.php, license, GPL, install, README, distribution, app creator
type: help
---
# Als Erweiterung paketieren

Diese Seite richtet sich an Anwendungsentwickler, die möchten, dass andere (oder andere Installationen) ihre Anwendung
installieren. Eine Erweiterung, die sich mit einem einzigen `composer require` installieren und mit einer einzigen
Einstellung aktivieren lässt und sich selbst dokumentiert, wird leicht übernommen.

![Die Seite „Über“ listet jede Erweiterung mit dem auf, was in ihrer ezinfo.php oder extension.xml steht](../../../../eng-us/help/images/creators/about-extensions.webp)

## Inhalt

1. [Das Repository](#das-repository)
2. [composer.json](#composerjson)
3. [extension.xml und ezinfo.php](#extensionxml-und-ezinfophp)
4. [Installationsschritte](#installationsschritte)
5. [Lizenz](#lizenz)
6. [Checkliste](#checkliste)

## Das Repository

Ein Git-Repository je Erweiterung, dessen Wurzel das Erweiterungsverzeichnis ist
([Aufbau einer Erweiterung](../../../../eng-us/help/creators/app-creators/extension-anatomy.md)):

```text
expdemo/
├── composer.json
├── extension.xml
├── ezinfo.php
├── LICENSE
├── README.md          what it does, requirements, install, settings
├── doc/               the longer documentation
├── classes/ modules/ design/ settings/ share/ bin/ cronjobs/ translations/
└── tests/
```

## composer.json

Erweiterungen sind Composer-Pakete vom Typ `ezpublish-legacy-extension`; das Installer-Plugin legt sie unter
`extension/<name>` ab ([3.5 Erweiterungen sind Composer-Pakete](../../../installation/03-den-code-beschaffen.md#35-extensions-sind-composer-pakete)):

```json
{
    "name": "yourvendor/expdemo",
    "description": "Demo application for Exponential",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "require": { "php": ">=8.0" },
    "extra": { "ezpublish-legacy-extension-name": "expdemo" }
}
```

Bibliotheken, die Ihr Code braucht, gehören in `require`; committen Sie niemals `vendor/`. Zur Veröffentlichung:
[Composer und Packagist](composer-und-packagist.md).

## extension.xml und ezinfo.php

Beide nennen denselben Namen, dieselbe Version, dasselbe Copyright, dieselbe Lizenz und dieselbe Website; die Seite
„Über“ (`/ezinfo/about`) liest zuerst `extension.xml`. Abhängigkeiten von anderen Erweiterungen gehören in
`<dependencies>` von `extension.xml`. Ein vollständiges Beispiel ist die mitgelieferte
`extension/ezstarrating/extension.xml`.

## Installationsschritte

Dokumentieren Sie sie und automatisieren Sie, wo es geht:

1. `composer require yourvendor/expdemo`
2. `ActiveExtensions[]=expdemo`
3. `./console ext:expdemo:install-schema` (Ihr idempotenter Tabellen-Installer, [Schema und .dba-Dateien](schema-und-dba-dateien.md))
4. `php bin/php/ezpgenerateautoloads.php -e` und `./console exp:velocity deploy`
5. Zu vergebende Richtlinien, hinzuzufügende Cron-Zeilen und Einstellungen, die in `settings/override/` zu setzen sind.

## Lizenz

Exponential steht unter der GPL v2 oder später; Code, der darin läuft, wird meist unter einer kompatiblen Lizenz
verteilt. Nennen Sie die Lizenz in `LICENSE`, `composer.json`, `ezinfo.php` und `extension.xml`. Nennen Sie Code, auf
dem Ihre Arbeit aufbaut ([Lizenzierung für Käufer](../../geschaeft/lizenzen-fuer-kaeufer.md)); dies ist keine
Rechtsberatung.

## Checkliste

- [ ] Installiert sich mit Composer nach `extension/<name>`; wird mit einer Zeile aktiviert.
- [ ] Funktioniert mit einer sauberen Installation und dem mitgelieferten Design (Templates lassen sich überschreiben).
- [ ] Keine Geheimnisse, keine Hostnamen, keine personenbezogenen Daten im Repository.
- [ ] Übersetzbare Texte, mindestens auf Englisch.
- [ ] Tests laufen in der CI ([CI für Erweiterungen](ci-fuer-erweiterungen.md)).
- [ ] Die Version in allen Metadatendateien stimmt mit dem Tag überein ([Versionen und Releases](versionen-und-releases.md)).
- [ ] README mit Voraussetzungen, Installation, Einstellungen und Support.

## Verwandte Seiten

- [Composer und Packagist](composer-und-packagist.md)
- [Versionen und Releases](versionen-und-releases.md)
- [Standard für Versionsverwaltung und Releases](../../../../eng-us/standards/source-control-and-releases.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
