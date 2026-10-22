---
title: Composer und Packagist im Ökosystem
audience: ecosystem, developer, admin, hosting
category: ecosystem
order: 22
summary: Wie Composer und Packagist das Ökosystem von Exponential tragen - Kernel und Erweiterungen installieren, der Anbieter se7enxweb, Git-Klone als Alternative, der Packagist-Webhook, der die Versionen aktuell hält, und Updates and packages in der Administration.
keywords: Composer, Packagist, composer install, composer require, Anbieter se7enxweb, Git-Klon, Webhook, Pakete, Updates and packages, ezupdate, se7enxweb vendor, Git clone, webhook, packages
type: help
---
# Composer und Packagist im Ökosystem

## Inhalt

1. [Warum Composer](#warum-composer)
2. [Der Anbieter se7enxweb](#der-anbieter-se7enxweb)
3. [Composer oder Git-Klone](#composer-oder-git-klone)
4. [Auf Packagist veröffentlichen](#auf-packagist-veröffentlichen)
5. [Pakete in der Administration](#pakete-in-der-administration)

## Warum Composer

"Exponential 6.0.x specifically requires the php package manager called Composer", Exponential 6.0.x verlangt also
ausdrücklich den PHP-Paketmanager Composer (`README.md`). Composer "installs the required Zeta Components php libraries
and the extensions listed in `composer.json`", installiert also die benötigten Bibliotheken der Zeta Components und
die in `composer.json` aufgeführten Erweiterungen (`README.md`, "Requirements"). Ohne `composer install` läuft die
Software nicht.

## Der Anbieter se7enxweb

- Der Kernel ist `se7enxweb/exponential`; Erweiterungen, Themes, Server und Forks heißen `se7enxweb/<name>`.
- "On Packagist the vendor se7enxweb has 219 packages", auf Packagist hat der Anbieter se7enxweb 219 Pakete, gezählt am
  18. Oktober 2026 (<https://packagist.org/packages/list.json?vendor=se7enxweb>,
  [Das Ökosystem der Erweiterungen](../../wer-ist-die-exponential-foundation.md#das-ökosystem-der-erweiterungen)).
- Auch Pakete Dritter gehören dazu: Nexus zum Beispiel verlangt `netgen/layouts-core`
  ([Die Platform-Linie](../produkte/die-platform-linie.md)).

## Composer oder Git-Klone

"Every package is a GitHub repository under se7enxweb and a Composer package; `composer require` or a Git clone
installs it", jedes Paket ist also ein GitHub-Repository und ein Composer-Paket, und `composer require` oder ein
Git-Klon installiert es ([Releases und Versionsnummern](../../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern)).
Git-Klone erleichtern das Aktualisieren und das Beitragen von Korrekturen ([Mit Git entwickeln](../../mit-git-entwickeln.md),
[Den Code holen](../../../installation/03-den-code-beschaffen.md)).

## Auf Packagist veröffentlichen

Für Ihr eigenes Paket: eine `composer.json`, ein öffentliches Repository, ein Eintrag auf Packagist und ein Webhook,
damit Packagist neue Tags sieht ([Composer und Packagist](../../erweiterungsentwicklung/teil-7-veroeffentlichen/composer-und-packagist.md),
[Der Packagist-Webhook](../../erweiterungsentwicklung/teil-7-veroeffentlichen/der-packagist-webhook.md)). Noch nicht jede
Erweiterung hat eine `composer.json`; sie zu ergänzen ist einer der Aufrufe der Roadmap an Freiwillige
([Roadmap](../../projekt/roadmap.md)).

## Pakete in der Administration

"**Setup > Updates and packages**, from the `ezupdate` extension, finds, checks and installs Composer packages from
package servers", die Seite aus der Erweiterung `ezupdate` findet, prüft und installiert also Composer-Pakete von
Paketservern ([Roadmap](../../projekt/roadmap.md)); siehe [Mit Git aktualisieren](../../mit-git-aktualisieren.md).

## Verwandte Seiten

- [Pakete](README.md)
- [Wie Pakete versioniert werden](wie-pakete-versioniert-werden.md)
- [Composer und Git auf Servern](../../hosting/grundlagen/composer-und-git-auf-servern.md)
- [Standards für PHP und Composer](../../../standards/php-und-composer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
