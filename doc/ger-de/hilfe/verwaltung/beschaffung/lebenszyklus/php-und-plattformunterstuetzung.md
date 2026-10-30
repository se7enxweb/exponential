---
title: Unterstützung von PHP und Plattform über die Vertragslaufzeit
audience: government, project-manager, admin, hosting
category: public-sector
order: 40
summary: Welche PHP-Versionen, Datenbanken, Webserver und Betriebssysteme Exponential 6.0 unterstützt, warum die Plattform unter der Software ihren eigenen Lebenszyklus hat und wie eine öffentliche Stelle Upgrades von PHP und Betriebssystem neben Upgrades von Exponential plant.
keywords: PHP-Versionen, PHP 8.0, PHP 8.5, Plattform, Datenbank, Betriebssystem, Webserver, Velocity, Lebenszyklus, Ende der Unterstützung, Hosting, Upgrade-Planung, öffentlicher Sektor, PHP versions, platform, database, operating system, web server, lifecycle, end of life, hosting, upgrade planning, public sector
type: help
---
# Unterstützung von PHP und Plattform über die Vertragslaufzeit

Diese Seite richtet sich an Projektleitungen und Hosting-Personal einer öffentlichen Stelle. Ein
Content-Management-System läuft auf einer Plattform (PHP, eine Datenbank, ein Webserver, ein Betriebssystem), und jeder
Teil hat seinen eigenen Lebenszyklus. Ein Vertrag über mehrere Jahre erlebt mindestens ein PHP-Upgrade. Die Fakten
stammen aus [2. Anforderungen](../../../../installation/02-voraussetzungen.md) und der Unterstützungszusage in
[Wer ist die Exponential Foundation?](../../../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung).

## Inhalt

1. [PHP](#php)
2. [Datenbanken](#datenbanken)
3. [Webserver und Engines](#webserver-und-engines)
4. [Betriebssysteme](#betriebssysteme)
5. [Plattform-Upgrades planen](#plattform-upgrades-planen)
6. [In einem Vertrag](#in-einem-vertrag)

## PHP

| PHP | Exponential 6.0 |
|---|---|
| 7.4 | ab 6.0.15 ohne Support nutzbar (keine Zusage für Fehler- oder Sicherheitskorrekturen, ohne die Funktionen, die PHP 8 brauchen); nicht von 6.0.8 bis 6.0.14 |
| 8.0 | ab 6.0.15 wieder unterstützt; das mitgelieferte PHP von Red Hat Enterprise Linux 9 und seinen Nachbauten; Velocity läuft darauf ab seinem Release 0.0.4.54 |
| 8.1 bis 8.4 | unterstützt, Velocity eingeschlossen |
| 8.5 | unterstützt; die neueste getestete Version |
| spätere 8.x | von `composer.json` akzeptiert, aber noch nicht veröffentlicht oder getestet |

Quelle: [2. Anforderungen, PHP-Versionen](../../../../installation/02-voraussetzungen.md#versionen). Die kontinuierliche
Integration führt die Testsuite unter PHP 8.1 bis 8.5 aus und prüft, dass 8.0 noch startet
([Wer ist die Exponential Foundation?](../../../wer-ist-die-exponential-foundation.md#php-versionen)).

PHP selbst hat einen veröffentlichten Unterstützungsplan ([php.net: unterstützte Versionen](https://www.php.net/supported-versions.php)).
Ältere PHP-Versionen verlieren die Sicherheitsunterstützung zuerst, deshalb empfiehlt das Kapitel zu den Anforderungen
das neueste PHP, das Ihr System bietet.

## Datenbanken

Exponential 6.0 unterstützt sechs Datenbanksysteme, in dieser offiziellen Reihenfolge der Präferenz: SQLite 3 (der
Standard), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL ([Datenbankunterstützung](../../../datenbankunterstuetzung.md),
[9. Datenbanken](../../../../installation/09-datenbanken.md)). Microsoft SQL Server wird als mit zusätzlichem Aufwand möglich
beschrieben. Die Datenbank ist Teil der Plattform: Planen Sie ihre eigenen Upgrades und ihre Sicherungsmethode
([Datenbanksicherungen je Engine](../../../hosting/betrieb/datenbank-backups-je-engine.md)).

## Webserver und Engines

Eine Website kann von Exponential Velocity, von Apache oder nginx mit PHP-FPM oder von FrankenPHP ausgeliefert werden
([8. Die Website ausliefern](../../../../installation/08-die-website-ausliefern.md)). Velocity braucht PHP 8.0 oder neuer (ab seinem Release 0.0.4.54) mit der Erweiterung `sockets`
([2. Anforderungen](../../../../installation/02-voraussetzungen.md)). Velocity ist ein eigenes Paket mit eigenen Versionen und
eigenem Changelog ([Changelog von Velocity](../../../../../changelogs/extensions/exponential-velocity.md)).

## Betriebssysteme

Unterstützte Betriebssysteme und ihre Stufen stehen in [Unterstützung von Betriebssystemen](../../../betriebssystemunterstuetzung.md);
Hoster finden die praktischen Hinweise in [Betriebssysteme für Hoster](../../../hosting/grundlagen/betriebssysteme-fuer-hoster.md).

## Plattform-Upgrades planen

| Schicht | Wer sie aktualisiert | Typischer Auslöser |
|---|---|---|
| Kernel und Erweiterungen von Exponential | der Lieferant oder Ihre Administration der Website | ein neues Release 6.0.x, eine Sicherheitskorrektur |
| PHP | der Host | das Ende der Sicherheitsunterstützung der PHP-Version |
| Datenbank | der Host | das Ende der Unterstützung der Datenbankversion |
| Betriebssystem | der Host | das Ende der Unterstützung der Distribution |

Praktische Regeln:

- Aktualisieren Sie eine Schicht nach der anderen, mit einem Test auf einer Staging-Kopie dazwischen.
- Prüfen Sie vor einem PHP-Upgrade, ob jede Erweiterung der Website die neue Version unterstützt; Ihre eigenen
  Erweiterungen sind das übliche Risiko ([14.1](../../../../installation/14-migration-von-4x.md#141-was-sich-ändert-und-was-nicht) erklärt, warum
  PHP-Code der größte Teil einer Migration ist).
- Hoster haben ein Runbook: [PHP-Upgrade](../../../hosting/runbooks/php-upgrade.md).
- Nach einem PHP-Wechsel muss die Engine ihren Code neu laden; die Betriebsseiten erklären das je Engine
  ([Ausrollen](../../../../anleitungen/bereitstellung.md)).

## In einem Vertrag

```text
Platform
1. The Supplier keeps PHP, the database and the operating system on versions that receive security
   updates from their publishers, and supported by the Exponential version in use
   (doc/eng-us/install/02-requirements.md).
2. The Supplier announces a platform upgrade <n> days ahead and tests it on a staging copy first.
3. The Supplier reports the PHP, database and operating system versions in each <period> report.
```

## Verwandte Seiten

- [Der Lebenszyklus des Produkts](README.md)
- [Unterstützte Versionen](unterstuetzte-versionen.md)
- [Anforderungen auf einen Blick (für Hoster)](../../../hosting/grundlagen/anforderungen-auf-einen-blick.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
