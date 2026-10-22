---
title: Einen Lernpfad wählen
audience: student
category: learning
order: 4
summary: Die vier Lernpfade für Lernende - Redakteur, Administrator, Entwickler und Designer -, für wen jeder passt, was Sie vor dem Start brauchen, wie lange jeder dauert, wie sie aufeinander aufbauen und welche Lernprojekte zu welchem Pfad gehören.
keywords: lernpfad, schwerpunkt, redakteur, administrator, entwickler, designer, wählen, beruf, voraussetzungen, reihenfolge, lehrplan, fahrplan, learning path, track, editor, developer, choose, career, prerequisites, order, curriculum, roadmap
type: help
---
# Einen Lernpfad wählen

Diese Seite hilft Ihnen zu entscheiden, wo Sie beginnen. Exponential wird von Menschen in sehr unterschiedlichen
Rollen genutzt, und die Hilfe hat Seiten für jede davon. Die vier Lernpfade dieses Ordners bringen diese Seiten in eine
Reihenfolge, die für Lernende funktioniert.

## Inhalt

1. [Die vier Pfade auf einen Blick](#die-vier-pfade-auf-einen-blick)
2. [Welcher Pfad zu Ihnen passt](#welcher-pfad-zu-ihnen-passt)
3. [Wie die Pfade aufeinander aufbauen](#wie-die-pfade-aufeinander-aufbauen)
4. [Was jeder Pfad erfordert](#was-jeder-pfad-erfordert)
5. [Lernprojekte je Pfad](#lernprojekte-je-pfad)
6. [Kurse, die zu den Pfaden passen](#kurse-die-zu-den-pfaden-passen)

## Die vier Pfade auf einen Blick

| Pfad | Sie lernen | Startseite |
|---|---|---|
| Redakteur | Inhalte schreiben, strukturieren, übersetzen und veröffentlichen; Seiten aus Blöcken bauen | [Lernpfad: Redakteur](lernpfad-redakteur.md) |
| Administrator | Eine Website installieren, Benutzer, Rollen, Sektionen, Sprachen, Caches, Logs und Updates verwalten | [Lernpfad: Administrator](lernpfad-administrator.md) |
| Entwickler | Mit Git, Templates, Inhaltsklassen, Einstellungen, Erweiterungen und der REST API arbeiten | [Lernpfad: Entwickler](lernpfad-entwickler.md) |
| Designer | Das Aussehen einer Website ändern: Designs, Templates, Overrides, Stylesheets, Themes als Erweiterungen | [Lernpfad: Designer](lernpfad-designer.md) |

## Welcher Pfad zu Ihnen passt

| Wenn Sie … | Beginnen Sie mit |
|---|---|
| Kommunikation, Journalismus, Marketing oder Bibliothekswissenschaft studieren | Redakteur |
| Websites für eine Organisation betreiben möchten oder IT-Betrieb studieren | Redakteur, dann Administrator |
| Informatik oder Webentwicklung studieren und etwas PHP können | Redakteur (kurz), dann Entwickler |
| Design oder Frontend-Entwicklung studieren und HTML und CSS können | Redakteur (kurz), dann Designer |
| unsicher sind | Redakteur: Jeder andere Pfad setzt voraus, dass Sie Inhalte finden und veröffentlichen können |

Die Hilfe selbst beschreibt dieses Wachstum „vom Benutzer zum Entwickler“: Zuerst nutzen Sie die
Administrationsoberfläche, dann ändern Sie Templates, dann arbeiten Sie mit dem Code ([Mit Git entwickeln](../mit-git-entwickeln.md),
[Die Leiter: Benutzer, Website-Bauer, Erweiterungsentwickler, Theme-Entwickler](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/die-leiter-vom-benutzer-zum-entwickler.md)).

## Wie die Pfade aufeinander aufbauen

```text
                      Editor
                        |
          +-------------+-------------+
          |                           |
    Administrator                 Designer
          |                           |
          +-------------+-------------+
                        |
                    Developer
```

- **Redakteur** ist die Grundlage für alle. Die ersten Schritte dauern ein bis zwei Tage.
- **Administrator** und **Designer** können in beliebiger Reihenfolge gelernt werden.
- **Entwickler** nutzt Teile der anderen drei: Inhaltsklassen aus dem Administratorpfad, Templates aus dem
  Designerpfad. Wenn Sie PHP können, dürfen Sie ihn direkt nach dem Redakteurpfad beginnen; die anderen Teile begegnen
  Ihnen unterwegs.

Der [Kurskatalog](../trainer/kurskatalog.md#empfohlene-reihenfolge) der Trainer zeigt dieselbe Reihenfolge für die
Kurse.

## Was jeder Pfad erfordert

| Pfad | Voraussetzungen | Empfohlene Dauer im Selbststudium | Dauer des passenden Trainerkurses |
|---|---|---|---|
| Redakteur | Ein Browser | 2 bis 3 Wochen, einige Stunden pro Woche | Redakteure 101: etwa 19,5 Stunden |
| Administrator | Redakteurpfad; ein Terminal | 4 bis 6 Wochen | Website-Administration: etwa 30 Stunden |
| Entwickler | Redakteurpfad; Grundlagen in PHP und Git | 6 bis 10 Wochen | Entwickler: etwa 44 Stunden |
| Designer | Redakteurpfad; HTML und CSS | 4 bis 6 Wochen | Theme-Design: etwa 24 Stunden |

Die Zeiten für das Selbststudium sind Vorschläge für Lernende mit einigen Stunden pro Woche; die Kursdauern sind die
aus dem [Kurskatalog](../trainer/kurskatalog.md#die-kurse).

## Lernprojekte je Pfad

Die [Lernprojekte](lernprojekte-ueberblick.md) sind bewertete Übungsprojekte auf Ihrer eigenen Installation. Jeder Pfad
nutzt einige davon:

| Projekt | Redakteur | Administrator | Entwickler | Designer |
|---|---|---|---|---|
| [1. Eine eigene Inhaltsklasse](projekt-01-eine-eigene-inhaltsklasse.md) | | ja | ja | |
| [2. Eine kleine Website-Struktur](projekt-02-eine-kleine-website-struktur.md) | ja | ja | | ja |
| [3. Ein Template-Override](projekt-03-ein-template-override.md) | | | ja | ja |
| [4. Eine Seite mit Layouts](projekt-04-eine-seite-mit-layouts.md) | ja | | | ja |
| [5. Eine mehrsprachige Website](projekt-05-eine-mehrsprachige-website.md) | ja | ja | | |
| [6. Eine Suche, die das Richtige findet](projekt-06-eine-suche-die-das-richtige-findet.md) | | ja | ja | |
| [7. Inhalte über die REST API lesen](projekt-07-inhalte-ueber-die-rest-api-lesen.md) | | | ja | |
| [8. Ihre erste Erweiterung](projekt-08-ihre-erste-erweiterung.md) | | | ja | |
| [9. Rollen für einen eingeschränkten Redakteur](projekt-09-rollen-fuer-einen-eingeschraenkten-redakteur.md) | | ja | | |
| [10. Abschlussprojekt: eine vollständige kleine Website](projekt-10-abschlussprojekt-eine-vollstaendige-kleine-website.md) | ja | ja | ja | ja |

## Kurse, die zu den Pfaden passen

Wenn Sie mit einem Trainer lernen, decken diese Kurse dasselbe Gebiet ab ([Einen Kurs bei einem Trainer besuchen](einen-kurs-bei-einem-trainer-besuchen.md)):

| Pfad | Kurse |
|---|---|
| Redakteur | [Redakteure 101](../trainer/redakteure-101/README.md), [Redakteure 201](../trainer/redakteure-201/README.md), [Exponential Layouts](../trainer/layouts/README.md) |
| Administrator | [Website-Administration](../trainer/website-administratoren/README.md), [Suche](../trainer/suche/README.md), [Multisite und Site-Pakete](../trainer/multisite-und-pakete/README.md) |
| Entwickler | [Entwickler](../trainer/entwickler/README.md) |
| Designer | [Theme-Design](../trainer/theme-designer/README.md) |

## Verwandte Seiten

- [Lesepfad für Lernende](lesepfad-fuer-lernende.md)
- [Lernprojekte: Überblick](lernprojekte-ueberblick.md)
- [Lernende: hier beginnen](lernende-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
