---
title: Checkliste für Code-Reviews
audience: software-engineer, developer
category: software-engineering
order: 540
summary: Eine Checkliste für die Prüfung einer Änderung am Kernel von Exponential oder einer Erweiterung - Umfang und Historie, Schichtung und Benennung, Kompatibilität mit PHP 8.0 und jeder Datenbank und jedem Server, Tests, Caches und Bereitstellung, Einstellungen, Templates und Gestaltung, Dokumentation, Changelog und Upgrade-Hinweise, das Dateimanifest und die CI - mit der Quelle jeder Regel.
keywords: Code-Review, Checkliste, Pull-Request, Prüfung, Schichtung, Benennung, PHP 8.0, Kompatibilität, Tests, Einstellungen, Templates, Manifest, Changelog, Upgrade-Hinweise, CI, Softwareingenieur, code review, checklist, pull request, review, layering, naming, compatibility, upgrade notes
type: help
---
# Checkliste für Code-Reviews

Diese Seite ist eine Checkliste für die Prüfung einer Änderung an Exponential oder einer seiner Erweiterungen. Das Projekt
macht viele Prüfrunden, bevor es eine Änderung annimmt ([Upstream beitragen](upstream-beitragen.md)); eine gemeinsame
Liste macht jede Runde kürzer. Kombinieren Sie sie mit der [Checkliste für Sicherheitsprüfungen](checkliste-fuer-sicherheitspruefungen.md).

## Inhalt

1. [Umfang und Historie](#umfang-und-historie)
2. [Architektur](#architektur)
3. [Kompatibilität](#kompatibilität)
4. [Laufzeit](#laufzeit)
5. [Tests und CI](#tests-und-ci)
6. [Einstellungen, Templates und Gestaltung](#einstellungen-templates-und-gestaltung)
7. [Dokumentation und Release-Aufzeichnung](#dokumentation-und-release-aufzeichnung)

## Umfang und Historie

- [ ] Eine Änderung je Pull-Request: eine Korrektur und ein Refactoring sind zwei.
- [ ] Betreffzeilen von Commits beginnen mit `Added: `, `Updated: `, `Fixed: `, `Removed: ` oder `Renamed: `; der Text
      sagt warum und was ([Git-Arbeitsablauf](git-arbeitsablauf.md)).
- [ ] Das Produkt heißt Exponential; Kernel-Bezeichner wie `eZContentObject` bleiben, wie sie sind.
- [ ] Keine Nennung von Werkzeugen, keine privaten Pfade, Zugangsdaten oder rechnerspezifischen Einstellungen in Commits.
- [ ] Der Branch ist zu logischen Commits zusammengefasst und, solange er offen ist, auf `main` rebased.

## Architektur

- [ ] Der Code liegt in der richtigen Schicht ([Kernel, lib und Erweiterungen: die Schichtung](kernel-lib-und-erweiterungen.md)):
      keine neue Abhängigkeit von `lib/` auf `kernel/`, kein HTML, das eine Domänenklasse ausgibt.
- [ ] Neuer Code verwendet das Präfix `exp`; nichts Vorhandenes wird umbenannt.
- [ ] Ein Erweiterungspunkt wird vor einem Kernel-Override genutzt
      ([Events, Erweiterungspunkte und Plugins](events-erweiterungspunkte-und-plugins.md)).
- [ ] Gemeinsame Logik ist eine Dienstklasse, nicht zwischen View, Befehl und Cronjob-Teil kopiert.
- [ ] Einstellungen werden über `eZINI` gelesen; Debug-Ausgaben über `eZDebug`; Texte über `ezpI18n::tr()`.
- [ ] Für neue Klassen sind die Klassenzuordnungen neu erzeugt.

## Kompatibilität

- [ ] Läuft auf PHP 8.0: keine neuere Syntax oder Funktionen ohne Absicherung; keine Typdeklarationen von PHP 7+ im
      Kernel-Code ([Statische Analyse und Codestil](statische-analyse-und-codestil.md)).
- [ ] SQL funktioniert auf jeder unterstützten Datenbank; Schemaänderungen werden für jeden Treiber ausgeliefert
      ([Schemaänderungen und Migrationen](schemaaenderungen-und-migrationen.md)).
- [ ] Keine öffentliche Signatur, Einstellung, Template-Variable, Befehlsoption und kein Exit-Code wurde ohne
      Upgrade-Hinweise geändert ([Richtlinie zur Rückwärtskompatibilität](richtlinie-zur-rueckwaertskompatibilitaet.md)).
- [ ] Neues Verhalten ist optional oder ein sicherer Standard, der das alte Ergebnis behält.

## Laufzeit

- [ ] Korrekt in einem langlebigen Velocity-Worker: kein Zustand je Anfrage in Statics, idempotente Registrierungen, kein
      `exit` ([Nebenläufigkeit, Statics und langlebige Prozesse](nebenlaeufigkeit-statics-und-langlebige-prozesse.md)).
- [ ] Eine neue Klasse, die eine mitgelieferte INI-Datei nennt, ist mit `class_exists()` abgesichert.
- [ ] Cache-Dateien werden über `eZClusterFileHandler` geschrieben; Caches haben IDs und Tags und werden invalidiert
      ([Cache-Invalidierung und Leeren](cache-invalidierung-und-leeren.md)).
- [ ] Befehle halten `--help` und Exit-Codes stabil und bieten `--dry-run`, wenn sie Daten ändern.
- [ ] Leistung: keine Abfrage in einer Schleife, wo eine Abfrage genügt; messen Sie im Zweifel
      ([Leistungsmessung und Benchmarks](leistungsmessung-und-benchmarks.md)).

## Tests und CI

- [ ] Ein Test schlägt ohne die Änderung fehl und besteht mit ihr.
- [ ] Tests brauchen keine Datenbank, sofern sie nicht SQL testen, und lesen nichts aus der Installation eines Entwicklers.
- [ ] Die Tests gehören zu einer Suite in `phpunit.xml`, sonst laufen sie nie.
- [ ] Quality und PHPUnit sind beim Merge mit `main` grün ([CI-Gates](ci-gates.md)).
- [ ] `share/filelist.md5` hat die Prüfsumme jeder hinzugefügten, geänderten oder entfernten Datei (`make manifest-fix`).

## Einstellungen, Templates und Gestaltung

- [ ] Eine neue Einstellung hat ihren Standard und einen Kommentar in `settings/<file>.ini`; Schalter verwenden
      `enabled`/`disabled`.
- [ ] Templates maskieren Ausgaben und enthalten kein SQL.
- [ ] Administrations-Templates folgen [Gestaltung der Administration](../../bc/6.0/admin4-gestaltung.md): kein `<style>`
      oder `style=""`, gemeinsame Komponenten und Tokens, ein Wert für den Dunkelmodus je neuem Token, Modul-CSS in
      `stylesheets/modules/`.
- [ ] Geänderte Seiten im hellen und dunklen Modus und bei den Breiten geprüft, die die Administration unterstützt.

## Dokumentation und Release-Aufzeichnung

- [ ] Die Funktionsseite in `doc/eng-us/features/6.0/` ist geschrieben oder aktualisiert.
- [ ] Das Changelog führt die Änderung ([Changelogs und Upgrade-Hinweise](changelogs-und-bc-hinweise.md)).
- [ ] Eine Änderung, die von Installationen eine Handlung verlangt, hat eine Seite in `doc/eng-us/bc/6.0/`.
- [ ] Hilfeseiten, die die Änderung berührt, bestehen `exp:help check` ([Dokumentation als Code](dokumentation-als-code.md)).

## Verwandte Seiten

- [Checkliste für Design-Reviews](checkliste-fuer-design-reviews.md)
- [Umgangsformen bei Pull-Requests](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/pull-request-etikette.md)
- [Codierstandards](../../standards/programmierstandards.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
