---
title: "Modul 2: Inhalte auf Exponential abbilden"
audience: trainer, admin, developer
category: training
order: 20
summary: Das Zuordnungsmodul des Migrationskurses - Klassen, Datentypen und der Inhaltsbaum; Benutzer, Rollen und Passwörter; URLs, Weiterleitungen und Metadaten für Suchmaschinen. Drei Lektionen, 2 Stunden 45 Minuten.
keywords: migration zu exponential, inhaltsmodell zuordnen, klassen, datentypen, remote-id, benutzer, passwörter, url-alias, weiterleitungen, url-wildcard, modul, migrating to exponential, content model mapping, classes, datatypes, remote id, users, passwords, url alias, redirects, url wildcard, seo, module
type: help
---
# Modul 2: Inhalte auf Exponential abbilden

Jede Quelle endet in denselben Bausteinen. Dieses Modul bringt den Teilnehmenden bei, die Zuordnung vor dem Code zu
schreiben: zu welcher Klasse und welchem Datentyp jedes Feld wird, wo jedes Element im Baum steht, was mit Benutzern
und ihren Passwörtern geschieht und wie jede alte Adresse weiter funktioniert.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Dauer | 2 Stunden 45 Minuten |
| Lektionen | 3 |
| Niveau | Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [2.1 Klassen, Datentypen und der Inhaltsbaum](klassen-datentypen-und-der-baum.md) | 60 Minuten | Eine Klasse in der Administration lesen und eine Zuordnungstabelle für einen WordPress-Beitrag schreiben |
| [2.2 Benutzer, Rollen und Passwörter](benutzer-rollen-und-passwoerter.md) | 45 Minuten | Hash-Typen mit nur lesenden Abfragen lesen und für vier Quellen zwischen Import und Zurücksetzen entscheiden |
| [2.3 URLs, Weiterleitungen und Metadaten für Suchmaschinen](urls-weiterleitungen-und-seo.md) | 60 Minuten | Eine URL-Wildcard anlegen, ausprobieren und entfernen; eine Weiterleitungstabelle schreiben und testen |

## Dieses Modul vorbereiten

- [ ] Eine Schulungsinstallation pro Person mit Administratorkonto und Shell-Zugang; das Werkzeug `sqlite3`
      installiert.
- [ ] Die Klasse **Article** (oder eine ähnliche Klasse) im Seitenpaket der Schulung vorhanden; prüfen Sie einmal
      ihre Attribute.
- [ ] Die Schulungswebsite antwortet unter einer bekannten Adresse, sodass die Teilnehmenden sie mit `curl` abfragen
      können (Lektion 2.3).
- [ ] Die Zuordnungs- und Adresstabellen aus der [WordPress-Anleitung](../../../../installation/migration/wordpress.md)
      für die Teilnehmenden verfügbar.
- [ ] Prüfen Sie am Ende des Moduls, dass jede Person die Wildcard aus Lektion 2.3 entfernt hat.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 2.1 | 60 |
| Lektion 2.2 | 45 |
| Pause | 15 |
| Lektion 2.3 | 60 |
| Gesamt | 180 (2 Stunden 45 Minuten Lektionen, zuzüglich einer Pause von 15 Minuten) |

## Verwandte Seiten

- [Migration zu Exponential: Kursübersicht](../README.md)
- [Inhalte auf Exponential abbilden](../../../../installation/migration/inhaltsmodell-zuordnen.md): die Seite, auf der
  dieses Modul aufbaut.
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
