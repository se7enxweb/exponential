---
title: Wahl der Datenbank
audience: government, admin, project-manager
category: public-sector
order: 140
summary: Die sechs Datenbanken, die Exponential unterstützt, in der offiziellen Reihenfolge des Projekts (SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL), und worauf es für eine öffentliche Stelle bei der Wahl ankommt - Lizenzierung, vorhandene Kompetenzen, Betrieb, Größe und die Möglichkeit, später zu wechseln.
keywords: Datenbank, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, Wahl der Datenbank, offizielle Reihenfolge, Souveränität, Migration, database, database choice, official order, sovereignty
type: help
---
# Wahl der Datenbank

Diese Seite richtet sich an IT-Verantwortliche und Administratoren einer öffentlichen Stelle, die die Datenbank unter
Exponential auswählen. Die Aussage des Projekts und die Einzelheiten je Datenbank stehen in
[Datenbankunterstützung](../../datenbankunterstuetzung.md); diese Seite ergänzt die Fragen, die öffentliche Stellen stellen.

## Inhalt

1. [Die offizielle Reihenfolge](#die-offizielle-reihenfolge)
2. [Fragen für eine öffentliche Stelle](#fragen-für-eine-öffentliche-stelle)
3. [Typische Entscheidungen](#typische-entscheidungen)
4. [Später wechseln](#später-wechseln)

## Die offizielle Reihenfolge

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (die Voreinstellung)
> · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit
> zusätzlichem Aufwand möglich.

Alle sechs haben einen Treiber im Kernel oder in einer mitgelieferten Erweiterung, einen Eintrag im
Einrichtungsassistenten und einen Weg in den Installationsprogrammen für die Kommandozeile
([Datenbankunterstützung](../../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge)).

| Datenbank | Kurz gesagt |
|---|---|
| SQLite 3 | die Voreinstellung; kein Datenbankserver, das Inhaltsrepository ist eine einzige Datei; für eine Website, die von einem Rechner ausgeliefert wird |
| PostgreSQL | der empfohlene Datenbankserver; in der CI bei jedem Push getestet |
| MongoDB | über einen Adapter; lesen Sie zuerst seine Grenzen |
| MariaDB | derselbe Treiber wie MySQL; der MySQL-kompatible Server, den das Projekt bevorzugt |
| Oracle | 19c und später über die Erweiterung `ezoracle` |
| MySQL | unterstützt und getestet; für neue Websites an letzter Stelle der Reihenfolge |

## Fragen für eine öffentliche Stelle

| Frage | Warum sie zählt |
|---|---|
| Welche Datenbank betreibt und sichert unsere IT bereits? | Vorhandene Kompetenzen und Sicherungswerkzeuge zählen mehr als kleine Unterschiede |
| Ist die Lizenz der Datenbank nach unseren Richtlinien akzeptabel? | Manche Stellen bevorzugen Server aus freier Software |
| Ein Rechner oder mehrere Webknoten? | SQLite passt zu einem Rechner; mehrere Knoten brauchen einen Datenbankserver |
| Bietet unsere Cloud einen verwalteten Dienst für diese Datenbank in dieser Version an? | Prüfen Sie die Version gegen die Anforderungen |
| Wie stellen wir wieder her, und wie schnell? | Testen Sie es (siehe „Ausstiegsplanung“) |

## Typische Entscheidungen

- Eine kleine Website auf einem Server, ohne Datenbankteam: SQLite 3.
- Eine zentrale IT mit einem Standard-Datenbankserver: PostgreSQL oder das MariaDB/MySQL oder Oracle, das die IT
  bereits betreibt.
- Mehrere Webknoten: ein Datenbankserver, nicht SQLite.

Das sind Ausgangspunkte, keine Regeln; die Hilfe zum Hosting vergleicht die Datenbanken für Hoster
([Datenbanken in der offiziellen Reihenfolge](../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)).

## Später wechseln

Eine Website kann mit einem datenbankunabhängigen Dump und Einspielen zwischen SQL-Datenbanken wechseln, und mit deren
eigenen Skripten zu Oracle oder MongoDB; planen Sie ein Wartungsfenster ein und vergleichen Sie danach die
Zeilenanzahlen ([Eine Website auf eine andere Datenbank umziehen](../../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen)).
Die Wahl ist also nicht endgültig, was selbst schon ein Schutz vor Abhängigkeit ist.

## Verwandte Seiten

- [Datenbankunterstützung](../../datenbankunterstuetzung.md)
- [9. Datenbanken](../../../installation/09-datenbanken.md)
- [Die Datenbank wechseln](../../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
