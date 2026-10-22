---
title: Releases als Community-Mitglied testen
audience: community, admin, developer, hosting, supporter
category: community
order: 28
summary: Wie Community-Mitglieder die Entwicklungslinie von Exponential vor einem Release testen - eine sichere Testinstallation auf SQLite, was Sie für Ihre eigene Einrichtung testen, wie Sie eine Regression beschreiben und wie Hoster und Agenturen auf vielen Servern testen können.
keywords: Tests, testen, Release, Entwicklungslinie, main, dev-main, Regression, Testinstallation, SQLite, Git-Klon, Engines, Datenbanken, PHP-Versionen, testing, development line, test installation, Git clone, engines, databases, PHP versions
type: help
---
# Releases als Community-Mitglied testen

Korrekturen und Funktionen entstehen auf dem Branch `main`, „der Linie 6.0.15, `dev-main`“, und werden mit dem
nächsten 6.0.x-Release veröffentlicht (`SECURITY.md`). Jedes Community-Mitglied, das `main` vor einem Release auf
seiner eigenen Art von Einrichtung ausprobiert, macht dieses Release für alle mit derselben Einrichtung besser.

> **Warnung**
> Testen Sie nur auf einer separaten Installation mit erfundenen Daten. Nie auf einer Produktivseite, und richten Sie
> einen Installer nie auf eine Datenbank, die Sie behalten möchten ([Upgrade](../../../installation/11-aktualisieren.md)).

## Inhalt

1. [Eine Testinstallation einrichten](#eine-testinstallation-einrichten)
2. [Was Sie testen](#was-sie-testen)
3. [Eine Regression beschreiben](#eine-regression-beschreiben)
4. [Auf vielen Servern testen](#auf-vielen-servern-testen)

## Eine Testinstallation einrichten

Am einfachsten ist ein Git-Klon von `main`, installiert auf SQLite, das keinen Datenbankserver braucht:

1. Klonen Sie das Repository und notieren Sie den Commit, den Sie testen (`git log -1 --format='%h %ad %s'`).
2. Installieren Sie ihn so, wie es [Installation mit einem Befehl](../../../funktionen/6.0/installation-mit-einem-befehl.md)
   beschreibt.
3. Halten Sie ihn mit Git aktuell ([Mit Git entwickeln](../../mit-git-entwickeln.md)).

Die ausführliche Anleitung für Fürsprecher ist [Die Entwicklungslinie testen](../../fuersprecher/die-entwicklungslinie-testen.md).

## Was Sie testen

| Bereich | Wie |
|---|---|
| Ihre alltäglichen Aufgaben | Bearbeiten, veröffentlichen, hochladen, übersetzen, eine Seite mit Layouts aufbauen |
| Was laut Changelog neu ist | Folgen Sie den Funktionsseiten, die im [Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md) verlinkt sind |
| Ihre Einrichtung | Ihre PHP-Version, Datenbank, Engine (Velocity, Apache oder nginx mit PHP-FPM, FrankenPHP), Ihr Betriebssystem |
| Ihre Erweiterungen | Die Erweiterungen, die Ihre echten Websites verwenden |
| Ihre Sprache und Ihre Anforderungen an Barrierefreiheit | Die Administration in Ihrer Sprache, mit Ihrer assistiven Technologie |
| Ein Upgrade | Eine Kopie der Struktur einer echten Website mit erfundenem Inhalt, aktualisiert wie in [Upgrade](../../../installation/11-aktualisieren.md) beschrieben |

## Eine Regression beschreiben

„Lief im letzten Release, auf main kaputt“ ist der nützlichste Befund überhaupt, weil er auf eine kürzliche Änderung
zeigt. Nennen Sie den Commit:

```text
Environment: Exponential main at <commit>, PHP <version>, <engine>, <database>, <design>
Worked in <last release>: <yes / no / unknown>
Steps: ...
```

Melden Sie sie im richtigen Tracker ([Einen Fehler melden](einen-fehler-melden.md)).

## Auf vielen Servern testen

Hosting-Unternehmen und Agenturen betreiben oft viele PHP-Versionen, Datenbanken und Betriebssysteme. Ein Test auf
jedem davon vor einem Release ist ein Beitrag, den nur wenige andere leisten können. Partner haben dazu eine eigene
Seite: [Releases testen (Partner)](../../partner/releases-testen.md). Anforderungen und unterstützte Kombinationen:
[Anforderungen](../../../installation/02-voraussetzungen.md), [Datenbankunterstützung](../../datenbankunterstuetzung.md),
[Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md).

## Verwandte Seiten

- [Als Community-Mitglied mitwirken](README.md)
- [Die Entwicklungslinie testen](../../fuersprecher/die-entwicklungslinie-testen.md)
- [Auf dem Laufenden bleiben](../beitreten/auf-dem-laufenden-bleiben.md)
- [Wie Releases entstehen](../so-funktioniert-es/wie-releases-entstehen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
