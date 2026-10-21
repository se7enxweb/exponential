---
title: Composer-Pakete und Packagist
audience: app-creator, developer
category: app-creation
order: 450
summary: Eine Erweiterung als Composer-Paket veröffentlichen - Paketname und Typ, Versionsbedingungen, Tags, Packagist und sein Webhook, ein Paket in eine Installation installieren und sicher aktualisieren.
keywords: Composer, Packagist, Paket, Versionsbedingung, Tilde, Tag, Webhook, Aktualisieren, Anwendungsentwickler, composer.json, package, version constraint, tilde, tag, webhook, composer require, composer update, ezpublish-legacy-extension, app creator
type: help
---
# Composer-Pakete und Packagist

Diese Seite ist für Anwendungsentwickler, die ihre Erweiterung veröffentlichen, damit Installationen sie per
`composer require` einbinden können. Exponential und jede mitgelieferte Erweiterung sind Composer-Pakete auf
[Packagist](https://packagist.org/packages/se7enxweb/exponential).

## Inhalt

1. [Grundlagen eines Pakets](#grundlagen-eines-pakets)
2. [Versionen und Bedingungen](#versionen-und-bedingungen)
3. [Auf Packagist veröffentlichen](#auf-packagist-veröffentlichen)
4. [In eine Installation installieren](#in-eine-installation-installieren)
5. [Sicher aktualisieren](#sicher-aktualisieren)

## Grundlagen eines Pakets

| Schlüssel | Wert |
|---|---|
| `name` | `vendor/name` in Kleinbuchstaben |
| `type` | `ezpublish-legacy-extension` |
| `extra.ezpublish-legacy-extension-name` | der Verzeichnisname unter `extension/` |
| `license` | eine SPDX-Kennung, zum Beispiel `GPL-2.0-or-later` |
| `require` | PHP-Version und Bibliotheken |

## Versionen und Bedingungen

- Versionen stammen aus Git-Tags (`v1.2.3`). Ein veröffentlichtes Tag ist dauerhaft: Verschieben oder löschen Sie es
  nie; veröffentlichen Sie stattdessen die nächste Version ([Versionen und Releases](versionen-und-releases.md)).
- `~1.2.3` bedeutet `>=1.2.3 <1.3.0`: Die letzte angegebene Stelle ist variabel. So pinnt die `composer.json` im
  Wurzelverzeichnis die Erweiterungen (`"se7enxweb/eztags": "~2.4.11"`).

## Auf Packagist veröffentlichen

1. Pushen Sie das Repository zu einem öffentlichen Host (die Erweiterungen von se7enxweb liegen auf GitHub).
2. Reichen Sie die URL des Repositorys bei Packagist ein.
3. Fügen Sie dem Repository den Webhook von Packagist hinzu, damit neue Tags ohne manuelle Aktualisierung erscheinen;
   ein Paket, das auf Packagist bei einer alten Version stehen bleibt, hat meist keinen Webhook.
4. Taggen Sie ein Release; prüfen Sie, dass Packagist es anzeigt.

## In eine Installation installieren

```bash
composer require yourvendor/expdemo:~1.0.0
php bin/php/ezpgenerateautoloads.php -e
```

Aktivieren Sie die Erweiterung anschließend ([3.5 Erweiterungen sind Composer-Pakete](../../../installation/03-den-code-beschaffen.md#35-extensions-sind-composer-pakete)).

## Sicher aktualisieren

- Aktualisieren Sie ein einzelnes Paket gezielt (`composer update yourvendor/expdemo`), nachdem Sie seine Release Notes
  gelesen haben; nicht `composer update` für alles auf einer Live-Website.
- Ein von Hand angelegtes `git clone` anstelle eines Pakets, das Composer als Archiv installiert hat, wird bei der
  nächsten Aktualisierung samt Historie ersetzt; lassen Sie Composer die Klone anlegen (`preferred-install`), wenn Sie
  direkt in der Installation entwickeln.
- Testen Sie die Aktualisierung zuerst auf Staging ([Eine Deployment-Pipeline](deployment-pipeline.md)).

## Verwandte Seiten

- [Ihre Anwendung als Erweiterung paketieren](als-erweiterung-paketieren.md)
- [Standard für PHP und Composer](../../../../eng-us/standards/php-and-composer.md)
- [Den Code beschaffen](../../../installation/03-den-code-beschaffen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
