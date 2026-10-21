---
title: "FAQ: Releases und Aktualisierungen"
audience: government, project-manager, admin, hosting
category: public-sector
order: 150
summary: Antworten auf die Fragen öffentlicher Stellen zu Releases und Aktualisierungen von Exponential - Versionen, unterstützte Versionslinien, wie oft Releases erscheinen, ob sich Versionen ändern, wie Aktualisierungen funktionieren, automatische Updates und PHP-Versionen.
keywords: FAQ Releases, Aktualisierungen, Versionen, 6.0.x, unterstützte Versionen, Tags, Changelog, Upgrade-Prüfung, Git Manager, automatische Updates, PHP-Versionen, öffentlicher Sektor, releases FAQ, upgrades, versions, supported versions, tags, changelog, upgrade check, automatic updates, PHP versions, public sector
type: help
---
# FAQ: Releases und Aktualisierungen

Diese Seite beantwortet die Fragen öffentlicher Stellen zu Releases und Aktualisierungen von Exponential. Die Antworten
stammen aus `SECURITY.md`, [Versionsverwaltung und Releases](../../../../eng-us/standards/source-control-and-releases.md) und den
Anleitungen zum Aktualisieren; der Teil zum Lebenszyklus im Bereich Beschaffung erklärt sie für Verträge
([Der Produktlebenszyklus](../../../../eng-us/help/government/procurement/lifecycle/README.md)).

## Inhalt

1. [Welche Version ist aktuell?](#welche-version-ist-aktuell)
2. [Welche Versionen werden unterstützt?](#welche-versionen-werden-unterstützt)
3. [Wie oft gibt es Releases?](#wie-oft-gibt-es-releases)
4. [Kann sich eine veröffentlichte Version ändern?](#kann-sich-eine-veröffentlichte-version-ändern)
5. [Wie funktioniert eine Aktualisierung?](#wie-funktioniert-eine-aktualisierung)
6. [Kann sich eine Website selbst aktualisieren?](#kann-sich-eine-website-selbst-aktualisieren)
7. [Welche PHP-Versionen werden unterstützt?](#welche-php-versionen-werden-unterstützt)

## Welche Version ist aktuell?

Die Versionslinie 6.0. Die veröffentlichten Tags des Kernels reichen von `v6.0.0` bis `v6.0.14`; 6.0.15 ist auf `main` in
Entwicklung ([Versionsverwaltung und Releases](../../../../eng-us/standards/source-control-and-releases.md#the-kernels-versions)).
**Setup > Systeminformationen** zeigt, was Ihre Website ausführt.

## Welche Versionen werden unterstützt?

6.0.x; Korrekturen erscheinen als nächstes 6.0.x. 5.x, 4.x, 3.x und ältere werden nicht unterstützt (`SECURITY.md`;
[Unterstützte Versionen](../../../../eng-us/help/government/procurement/lifecycle/supported-versions.md)).

## Wie oft gibt es Releases?

Es gibt keinen festen Zeitplan. Die letzten Releases wurden im Dezember 2025 sowie im Februar, April und Juni 2026
getaggt ([Die Roadmap](../../projekt/roadmap.md#was-bereits-geliefert-wurde)). Planen Sie mehrmals im Jahr
Aktualisierungen ein.

## Kann sich eine veröffentlichte Version ändern?

Nein. Ein veröffentlichter Tag wird nie geändert; eine Korrektur kommt als nächste Version (`SECURITY.md`;
[Release-Politik und Versionsnummern](../../../../eng-us/help/government/procurement/lifecycle/release-policy-and-version-numbers.md)).

## Wie funktioniert eine Aktualisierung?

Sieben Schritte: sichern und ein Wartungsfenster beginnen, sehen, was ansteht, den Kernel aktualisieren (Git oder der
Git Manager), die Erweiterungen aktualisieren (Composer oder **Setup > Updates and packages**), die Datenbank prüfen und
aktualisieren, Caches leeren, die Website prüfen ([Mit Git aktualisieren](../../mit-git-aktualisieren.md)).
**Setup > Upgrade-Prüfung** vergleicht vorher Ihre Dateien mit dem Release
([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).

## Kann sich eine Website selbst aktualisieren?

Die Anleitung zum Aktualisieren zeigt ein Skript für Cron oder einen systemd-Timer, das Release-Tags folgt; es führt nie
Datenbank-Updates aus und hält an, wenn etwas einen Menschen braucht. Sie rät, zuerst auf einem Staging-System zu
testen und die Live-Website auf Releases zu halten
([Mit Git aktualisieren: Selbst aktualisieren lassen](../../mit-git-aktualisieren.md#automatisch-aktualisieren-lassen)).

## Welche PHP-Versionen werden unterstützt?

PHP 8.0 bis 8.5, auch mit Velocity (ab seinem Release 0.0.4.54); PHP 7.4 nur bis 6.0.7
([2. Anforderungen](../../../installation/02-voraussetzungen.md#versionen)).

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Bereitschaft für Aktualisierungen](../checklisten/bereitschaft-fuer-aktualisierungen.md)
- [Aktualisieren](../../../anleitungen/aktualisieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
