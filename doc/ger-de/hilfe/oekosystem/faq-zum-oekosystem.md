---
title: FAQ zum Ökosystem
audience: ecosystem, community, partner, developer, businesses
category: ecosystem
order: 6
summary: Kurze Antworten auf häufige Fragen zum Ökosystem von Exponential - was dazugehört, die Produkte und Distributionen, Pakete und Versionen, PHP und Datenbanken, die Organisationen und wie Sie mitmachen.
keywords: FAQ, Ökosystem, Fragen, Produkte, Distributionen, DXP, Nexus, Pakete, Packagist, PHP-Versionen, Datenbanken, 7x, Foundation, Partner, ecosystem, questions, products, distributions, packages, PHP versions, databases, partners
type: help
---
# FAQ zum Ökosystem

## Inhalt

1. [Das Ökosystem](#das-ökosystem)
2. [Produkte](#produkte)
3. [Pakete](#pakete)
4. [Stack](#stack)
5. [Organisationen und Menschen](#organisationen-und-menschen)

## Das Ökosystem

**Was ist das Ökosystem von Exponential?**
"The greater ecosystem extends beyond the community to cover all aspects of the Exponential world within the PHP
stack: its users and creators", also: Das größere Ökosystem reicht über die Community hinaus und umfasst alle Aspekte
der Welt von Exponential innerhalb des PHP-Stacks, ihre Anwender und Ersteller ([Was das Ökosystem ist](was-das-oekosystem-ist.md)).

**Worin unterscheidet es sich von der Community?**
Die Community sind Menschen, die mitmachen; das Ökosystem umfasst auch Produkte, Pakete, den Stack, Organisationen und
Anwender, die nie mitmachen ([Partner, Community und Ökosystem](../../../eng-us/help/community/partners-community-and-ecosystem.md)).

## Produkte

**Welche Produkte gibt es?**
Exponential 6, Velocity, Layouts, Intelligence, AdminUI, Exponential Basic und die Platform-Linie: Platform 3.2.9,
Legacy, Nexus und DXP ([Produkte](produkte/README.md)).

**Enthält die DXP den Kernel von Exponential 6 oder einen Layout-Editor?**
Nein. Sie liefert keinen Layout-Editor, keinen Freigabe-Workflow, keinen Zeitplaner und keinen Legacy-Kernel; Legacy und
Nexus enthalten den Kernel ([Exponential Platform DXP](produkte/platform-dxp.md)).

**Ist Velocity Pflicht?**
`composer.json` verlangt es seit 6.0.15, es wird also installiert; Sie können die Website trotzdem mit Apache, Nginx
oder einem anderen Server ausliefern ([Webserver und Engines](php-stack/webserver-und-engines.md)).

## Pakete

**Wie viele Pakete gibt es?**
Am 18. Oktober 2026 verlangte der Kernel 66 Pakete von se7enxweb, und der Anbieter hatte 219 Pakete auf Packagist;
zählen Sie neu, bevor Sie eine Zahl zitieren ([Composer und Packagist](pakete/composer-und-packagist.md)).

**Kann sich eine veröffentlichte Version ändern?**
Nein. "A published tag is never changed", ein veröffentlichtes Tag wird nie geändert; eine Korrektur kommt als nächste
Version ([Wie Pakete versioniert werden](pakete/wie-pakete-versioniert-werden.md)).

**Wo finde ich eine Erweiterung?**
Siehe [Eine Erweiterung finden](pakete/eine-erweiterung-finden.md).

## Stack

**Welche PHP-Versionen?**
PHP 8.0 bis 8.5 für Exponential 6.0.15 ([PHP-Versionen](php-stack/php-versionen.md)).

**Welche Datenbanken?**
SQLite 3 (Standard), PostgreSQL, MongoDB, MariaDB, Oracle und MySQL, in dieser Reihenfolge der Bevorzugung
([Datenbanken](php-stack/datenbanken.md)).

**Welche Betriebssysteme?**
Fast jedes, auf dem PHP 8.0 oder neuer läuft, in vier Stufen ([Betriebssysteme](php-stack/betriebssysteme.md)).

## Organisationen und Menschen

**Wer führt Exponential?**
7x führt die Entwicklung; die Exponential Foundation betreibt das Projekt ([Organisationen](organisationen/README.md)).

**Ist Exponential mit dem ursprünglichen Hersteller verbunden?**
Nein: "This project is not associated with the original eZ Publish software or its original developer, eZ Systems or
Ibexa", also: Dieses Projekt ist weder mit der ursprünglichen Software eZ Publish noch mit ihrem ursprünglichen
Entwickler, eZ Systems oder Ibexa, verbunden (`README.md`).

**Wie mache ich mit?**
Beginnen Sie mit [Community: hier beginnen](../../../eng-us/help/community/community-start-here.md); Unternehmen mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md).

## Verwandte Seiten

- [Ökosystem: hier beginnen](oekosystem-hier-beginnen.md)
- [Glossar zum Ökosystem](glossar-zum-oekosystem.md)
- [FAQ für die Community](../../../eng-us/help/community/community-faq.md)
- [FAQ zum Projekt](../projekt/faq.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
