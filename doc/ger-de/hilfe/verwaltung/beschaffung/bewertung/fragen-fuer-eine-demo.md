---
title: Fragen für die Demo eines CMS
audience: government, project-manager, sales-marketing, partner
category: public-sector
order: 70
summary: Fragen, die ein Bewertungsgremium einem Bieter während einer Vorführung von Exponential stellt, nach Thema gruppiert, mit der Antwort, die die Dokumentation stützt, damit das Gremium eine genaue Antwort von einer vagen unterscheiden kann.
keywords: Fragen für die Demo, Fragen an Hersteller, Bewertungsfragen, Sorgfaltsprüfung, Barrierefreiheit, Sicherheit, Datenschutz, Support, Ausstieg, Hosting, CMS-Demo, demo questions, vendor questions, evaluation questions, due diligence, accessibility, security, privacy, support, exit, hosting, CMS demo
type: help
---
# Fragen für die Demo eines CMS

Diese Seite richtet sich an Bewertende, die an der Vorführung eines auf Exponential beruhenden
Content-Management-Systems teilnehmen. Zu jeder Frage unten gibt es die Antwort, die die Dokumentation des Projekts
stützt, und einen Link dorthin. Ein Bieter kann für seine eigenen Leistungen eine bessere Antwort geben; über das
Produkt sollte er nie eine stärkere Antwort geben als dessen Dokumentation.

## Inhalt
1. [Barrierefreiheit](#barrierefreiheit)
2. [Sicherheit](#sicherheit)
3. [Datenschutz und Schriftgut](#datenschutz-und-schriftgut)
4. [Sprachen](#sprachen)
5. [Hosting und Betrieb](#hosting-und-betrieb)
6. [Releases und Support](#releases-und-support)
7. [Ausstieg und Lizenzierung](#ausstieg-und-lizenzierung)
8. [Wie Sie die Antworten verwenden](#wie-sie-die-antworten-verwenden)

## Barrierefreiheit

| Frage | Was die Dokumentation stützt |
|---|---|
| Welche WCAG-Stufe erfüllt die Administration? | admin4 und admin4l: WCAG 2.2 AA „Partly complies“, gegen AA-Kriterien entworfen und durch Regressionstests abgesichert; kein Konformitätsanspruch ([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)) |
| Haben Sie ein VPAT oder einen Accessibility Conformance Report? | für das Produkt gibt es keinen; ein Bieter kann einen für die gelieferte Website erstellen |
| Ist das öffentliche Design barrierefrei? | öffentliche Designs prüft das Projekt nicht; es liegt in der Verantwortung des Website-Erstellers |
| Ist der Rich-Text-Editor barrierefrei? | die Integrationen von TinyMCE wurden nicht geprüft: „Unverified“ |
| Wer schreibt unsere Erklärung zur Barrierefreiheit? | keine Vorlage mitgeliefert; der Bieter oder Sie schreiben sie |

## Sicherheit

| Frage | Was die Dokumentation stützt |
|---|---|
| Wie bilden Sie die OWASP Top 10 ab? | Gegenmaßnahmen je Kategorie, „Partly complies“ ([Sicherheitsstandards](../../../../../eng-us/standards/security.md)) |
| Wurde eine Bewertung nach ASVS durchgeführt? | nein: „Unverified“; die Standard-Mindestlänge des Passworts (10) liegt unter ASVS Stufe 1 (12), sofern nicht geändert |
| Gibt es eine zweistufige Anmeldung? | ja, als Erweiterung auf Wunsch ([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)) |
| Ist die Kontosperre an? | standardmäßig aus (`MaxNumberOfFailedLogin=0`); Brute-Force wird von Alarmregeln des Audits erkannt |
| Wird eine Content Security Policy durchgesetzt? | Report-only in Administrations-Siteaccesses, standardmäßig keine auf öffentlichen Websites; das Durchsetzen entscheidet der Betreiber |
| Wie werden Schwachstellen gemeldet und behoben? | vertraulich an die Adresse in `SECURITY.md`; Korrekturen kommen im nächsten Release 6.0.x; veröffentlichte Tags ändern sich nie |

## Datenschutz und Schriftgut

| Frage | Was die Dokumentation stützt |
|---|---|
| Haben Sie ein Cookie-Banner? | nein; der Kernel setzt nur funktionale Cookies, und eine Website, die Analyse hinzufügt, muss eine Einwilligung ergänzen ([Datenschutzstandards](../../../../../eng-us/standards/privacy.md)) |
| Können wir alle Daten zu einer Person exportieren? | keine einzelne Funktion; E-Mail-Einstellungen und Audit-Einträge haben Exporte, andere Daten über eigene Werkzeuge |
| Was wird über Beschäftigte protokolliert? | das Audit-Protokoll, mit gekürzten Adressen und Pseudonymisierung nach einer festgelegten Zeit ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)) |
| Wie lange werden Protokolle aufbewahrt? | Audit je Kanal 90 Tage live und 2 Jahre archiviert; einige Protokolle haben Altersgrenzen, andere rotieren nach Größe ([Datenschutzstandards](../../../../../eng-us/standards/privacy.md#known-gaps)) |

## Sprachen

| Frage | Was die Dokumentation stützt |
|---|---|
| Kann jede Seite in mehreren Sprachen existieren? | ja, mit Übersetzungen je Objekt ([Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md)) |
| Schriften von rechts nach links? | `dir="rtl"` in Inhalten und Administration ([Rechts nach links](../../../../../eng-us/features/6.0/right-to-left.md)) |
| Welche Sprachen der Administration sind vollständig? | siehe [Sprachen](../../../sprachen.md); fragen Sie gezielt nach Ihrer Sprache |

## Hosting und Betrieb

| Frage | Was die Dokumentation stützt |
|---|---|
| Welche Datenbanken und PHP-Versionen? | PHP 8.0 bis 8.5; SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL ([Anforderungen](../../../../installation/02-voraussetzungen.md)) |
| Können wir es selbst hosten? | ja; die Software ist frei und läuft auf Ihren eigenen Servern ([Ein Hosting-Modell wählen](../../../hosting/modelle/ein-hosting-modell-waehlen.md)) |
| Wie werden Sicherungen erstellt? | Datenbank und Dateien zusammen, mit dem Wartungsmodus ([Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)) |
| Wie prüfen wir ein Upgrade? | **Setup > Upgrade-Prüfung** ([Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md)) |

## Releases und Support

| Frage | Was die Dokumentation stützt |
|---|---|
| Welche Versionen erhalten Sicherheitskorrekturen? | die Linie 6.0.x; 5.x und älter werden nicht unterstützt (`SECURITY.md`) |
| Was verspricht das Projekt zur Reaktionszeit? | eine erste Antwort auf eine vertrauliche Sicherheitsmeldung innerhalb eines Werktags; kein anderes SLA ist veröffentlicht ([SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)) |
| Wer unterstützt uns? | das eigene Angebot des Bieters; das Projekt bietet Hilfe der Community und bezahlte Hilfe von 7x ([Support erhalten](../../../projekt/unterstuetzung-erhalten.md)) |

## Ausstieg und Lizenzierung

| Frage | Was die Dokumentation stützt |
|---|---|
| Was kostet die Lizenz? | keine Lizenzgebühr; GNU GPL Version 2 oder neuer ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)) |
| Erhalten wir den Quellcode dessen, was Sie für uns bauen? | lassen Sie es sich vom Bieter im Vertrag zusagen; die GPL regelt die Weitergabe |
| Wie steigen wir aus? | Website-Archiv, Pakete, Datenbank-Dump, Dateien, Layouts, Formulardaten ([Exponential verlassen](../../../../installation/migration/exponential-verlassen.md)) |

## Wie Sie die Antworten verwenden

- Notieren Sie den genauen Wortlaut der Antwort und die Minute der Demo.
- Geht die Antwort über die Dokumentation hinaus, fragen Sie: „Wo steht das?“, und halten Sie die genannte Quelle fest.
- Ein ehrliches „Nein, das ist eine Lücke, und so würden wir sie schließen“ ist eine gute Antwort; bewerten Sie den Plan.

## Verwandte Seiten
- [Bewertung: Übersicht](README.md)
- [Beschaffung: Übersicht](../README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
