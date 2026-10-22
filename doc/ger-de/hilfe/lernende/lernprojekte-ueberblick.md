---
title: "Lernprojekte: Überblick"
audience: student
category: learning
order: 23
summary: Die zehn bewerteten Lernprojekte für Lernende - was jedes mit echten Exponential-Funktionen baut (Inhaltsklassen, Website-Struktur, Template-Overrides, Layouts, Sprachen, Suche, die REST API, Erweiterungen, Rollen und ein Abschlussprojekt), seine Stufe und Dauer, was Sie abgeben, wie die Projekte bewertet werden und wie sie sich zu den Prüfungen der Trainer verhalten.
keywords: lernprojekte, übungsprojekte, bewertet, übungen, aufgaben, portfolio, inhaltsklasse, template, layouts, mehrsprachig, suche, REST API, erweiterung, rollen, abschlussprojekt, bewertungsraster, study projects, practice projects, graded, exercises, assignments, content class, multilingual, search, extension, roles, capstone, rubric
type: help
---
# Lernprojekte: Überblick

Die Lernprojekte verwandeln Lesen in funktionierende Ergebnisse. Jedes baut etwas Kleines, aber Echtes auf Ihrer
Lerninstallation, verwendet nur Funktionen, die die Hilfe dokumentiert, und endet mit einer Liste von Abgaben und
einem Bewertungsraster. Bearbeiten Sie sie allein, in einer Lerngruppe oder als Aufgaben einer Lehrkraft.

> **Hinweis**
> Diese Projekte und ihre Noten sind Übung für Sie, Ihre Lerngruppe oder Ihre Lehrkraft. Sie sind keine offizielle
> Zertifizierung und werden weder von 7x noch von der Exponential Foundation ausgestellt oder anerkannt; offizielle
> Zertifizierungen sind geplant, aber noch nicht verfügbar ([Zertifizierungen und Zertifikate](zertifizierungen-und-zertifikate.md)).

## Inhalt

1. [Die zehn Projekte](#die-zehn-projekte)
2. [Wie jedes Projekt aufgebaut ist](#wie-jedes-projekt-aufgebaut-ist)
3. [Was Sie abgeben](#was-sie-abgeben)
4. [Wie Projekte bewertet werden](#wie-projekte-bewertet-werden)
5. [Regeln für jedes Projekt](#regeln-für-jedes-projekt)
6. [Projekte und die Prüfungen der Trainer](#projekte-und-die-prüfungen-der-trainer)

## Die zehn Projekte

| # | Projekt | Stufe | Zeit | Wichtigste Funktionen |
|---|---|---|---|---|
| 1 | [Eine eigene Inhaltsklasse](projekt-01-eine-eigene-inhaltsklasse.md) | Einsteiger | 2 h | Inhaltsklassen, Attribute, Datentypen |
| 2 | [Eine kleine Website-Struktur](projekt-02-eine-kleine-website-struktur.md) | Einsteiger | 3 h | Ordner, Artikel, Medien, Versionen, Papierkorb |
| 3 | [Ein Template-Override](projekt-03-ein-template-override.md) | Fortgeschritten | 3 h | Designs, Override-Regeln, die Template-Sprache |
| 4 | [Eine Seite mit Layouts](projekt-04-eine-seite-mit-layouts.md) | Einsteiger | 2 h | Exponential Layouts: Layouts, Zonen, Blöcke, Zuordnungen |
| 5 | [Eine mehrsprachige Website](projekt-05-eine-mehrsprachige-website.md) | Fortgeschritten | 3 h | Inhaltssprachen, Übersetzungen, `SiteLanguageList` |
| 6 | [Eine Suche, die das Richtige findet](projekt-06-eine-suche-die-das-richtige-findet.md) | Fortgeschritten | 2 h | Durchsuchbare Attribute, der Suchindex |
| 7 | [Inhalte über die REST API lesen](projekt-07-inhalte-ueber-die-rest-api-lesen.md) | Fortgeschritten | 3 h | `ezprestapi`, persönliche API-Schlüssel, curl |
| 8 | [Ihre erste Erweiterung](projekt-08-ihre-erste-erweiterung.md) | Fortgeschritten | 4 h | Erweiterungen, Module, Views, Templates |
| 9 | [Rollen für einen eingeschränkten Redakteur](projekt-09-rollen-fuer-einen-eingeschraenkten-redakteur.md) | Fortgeschritten | 2 h | Benutzer, Gruppen, Rollen, Richtlinien, Einschränkungen |
| 10 | [Abschlussprojekt: eine vollständige kleine Website](projekt-10-abschlussprojekt-eine-vollstaendige-kleine-website.md) | Erfahren | 2 bis 4 Tage | Alles oben Genannte |

Die Zeiten gelten für Lernende, die die Schritte des passenden Lernpfads abgeschlossen haben. Welche Projekte zu
welchem Pfad gehören, steht in [Einen Lernpfad wählen](einen-lernpfad-waehlen.md#lernprojekte-je-pfad).

## Wie jedes Projekt aufgebaut ist

Jede Projektseite hat dieselben Teile:

| Teil | Was er Ihnen gibt |
|---|---|
| Auf einen Blick | Stufe, Zeit, Voraussetzungen und den Lernpfad |
| Ziel | Was Sie am Ende haben werden |
| Bevor Sie beginnen | Was Sie lesen und vorbereiten |
| Aufgaben | Nummerierte Aufgaben, jeweils mit dem, was Sie sehen sollten |
| Abgabe | Die Liste der Dinge, die Sie abliefern |
| Bewertungsraster | Wie die Arbeit bewertet wird |
| Zusatzziele | Freiwillige Extras für eine bessere Note oder zum Spaß |

## Was Sie abgeben

Sofern ein Projekt nichts anderes sagt:

1. **Einen kurzen Bericht** (ein oder zwei Seiten, Markdown genügt): was Sie gebaut haben, welche Seiten Sie verwendet
   haben, auf welche Probleme Sie gestoßen sind und wie Sie sie gelöst haben. Ihr [Lerntagebuch](ein-lerntagebuch-fuehren.md)
   ist das Rohmaterial.
2. **Screenshots** des Ergebnisses, ohne Passwörter, Schlüssel oder personenbezogene Daten auf dem Bildschirm.
3. **Dateien, die Sie geschrieben haben** (Templates, Einstellungen, PHP, Skripte), in einem eigenen Git-Repository
   ([Git als Lernende nutzen](git-als-lernende-nutzen.md)). Committen Sie niemals `settings/override/`, Passwörter oder
   Schlüssel.
4. **Das Release**, mit dem Sie gearbeitet haben (die erste Zeile von `php bin/php/console --version`).

## Wie Projekte bewertet werden

Jedes Projekt verwendet dieselben vier Kriterien mit 0 bis 3 Punkten, dazu projektspezifische Prüfungen. Die
vollständige Methode steht auf [Die eigene Arbeit bewerten](die-eigene-arbeit-bewerten.md).

| Kriterium | Fragt |
|---|---|
| Es funktioniert | Tut das Ergebnis, was das Ziel sagt, mit dem genannten Release? |
| Es ist auf dem dokumentierten Weg gebaut | Overrides statt bearbeiteter Kerndateien, Einstellungen am richtigen Ort, Rollen statt gemeinsamer Konten |
| Es ist sicher | Maskierte Ausgabe, keine Geheimnisse in Dateien, nur erfundene Daten, Zugriffskontrolle, wo nötig |
| Es ist erklärt | Können andere dem Bericht folgen und die Arbeit wiederholen? |

## Regeln für jedes Projekt

- Arbeiten Sie auf einer **Lerninstallation**, niemals auf einer Live-Website oder einer Website mit echten Daten.
- Verwenden Sie **erfundene Inhalte** und Testadressen unter `example.invalid`.
- **Nennen Sie die Seiten**, die Sie verwendet haben, mit Titel und Link.
- Kopieren Sie nicht die Arbeit anderer Lernender. Sich gegenseitig zu helfen ist in Ordnung; sagen Sie, wer geholfen
  hat.
- Wenn ein Schritt auf einer Hilfeseite falsch ist, melden Sie es ([Die Hilfe als Lernende verbessern](die-hilfe-als-lernende-verbessern.md)).
  Das zählt zu Ihren Gunsten.

## Projekte und die Prüfungen der Trainer

Die Trainer haben eigene Prüfungen: schriftliche Tests, praktische Aufgaben mit Bewertungsrastern und
Abschlussprojekte, geschrieben für die Kurse, die sie unterrichten ([Prüfungen und praktische Übungen](../trainer/pruefungen/README.md)).
Die Lernprojekte hier sind für das Selbststudium geschrieben und decken ein ähnliches Gebiet ab. Wenn Sie einen Kurs
besuchen, zählt für diesen Kurs die Prüfung Ihres Trainers; die Projekte sind eine gute Vorbereitung darauf
([Einen Kurs bei einem Trainer besuchen](einen-kurs-bei-einem-trainer-besuchen.md)).

## Verwandte Seiten

- [Die eigene Arbeit bewerten](die-eigene-arbeit-bewerten.md)
- [Ein Portfolio aufbauen](ein-portfolio-aufbauen.md)
- [Checklisten für Lernende](checklisten-fuer-lernende.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
