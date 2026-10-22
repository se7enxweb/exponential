---
title: Ein Portfolio aufbauen
audience: student
category: learning
order: 47
summary: Wie Lernende ihre Lernarbeit mit Exponential in ein Portfolio verwandeln, das ein Arbeitgeber oder eine Schule prüfen kann - was hineingehört (Projekte, Abschlussprojekt, Beiträge, Erweiterungen, Zertifikate), wie Sie jedes Stück ehrlich darstellen, was Sie aus Gründen des Datenschutzes und der Sicherheit weglassen, und eine Vorlage für die Seite.
keywords: portfolio, lebenslauf, bewerbung, stelle, praktikum, nachweis, projekte, abschlussprojekt, pull requests, beiträge, erweiterung, zertifikat, ehrliche angaben, datenschutz, lernende, CV, resume, job, internship, evidence, capstone, contributions, extension, certificate, honest claims, privacy, student
type: help
---
# Ein Portfolio aufbauen

Ein Portfolio zeigt, was Sie können, mit Nachweisen, die jeder prüfen kann. Bei Exponential lassen sich die Nachweise
ungewöhnlich leicht veröffentlichen: Die Software ist frei, Beiträge sind öffentlich, und Ihre Lernwebsites laufen auf
Ihrem eigenen Rechner. Diese Seite hilft Ihnen, alles zusammenzustellen.

## Inhalt

1. [Was hineingehört](#was-hineingehört)
2. [Jedes Stück darstellen](#jedes-stück-darstellen)
3. [Ehrliche Angaben](#ehrliche-angaben)
4. [Was Sie weglassen](#was-sie-weglassen)
5. [Eine Vorlage für die Portfolioseite](#eine-vorlage-für-die-portfolioseite)

## Was hineingehört

| Stück | Nachweis | Aus |
|---|---|---|
| Das Abschlussprojekt | Bericht, Screenshots, eine Anleitung zum Neuaufbau, Repositorys | [Projekt 10](projekt-10-abschlussprojekt-eine-vollstaendige-kleine-website.md) |
| Zwei oder drei Lernprojekte | Abgaben und Bewertungen nach dem Bewertungsraster | [Lernprojekte](lernprojekte-ueberblick.md) |
| Gemergte Pull Requests | Links auf die Pull Requests | [Als Lernende beitragen](als-lernende-beitragen.md) |
| Issues, die Sie gemeldet oder mit gelöst haben | Links | [Fehler, die Sie beim Lernen finden](fehler-die-sie-beim-lernen-finden.md) |
| Eine eigene Erweiterung oder ein eigenes Theme | Ein öffentliches Repository mit README | [Projekt 8](projekt-08-ihre-erste-erweiterung.md), [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md) |
| Übersetzungen | Links auf die gemergten Änderungen | [Als Lernende übersetzen](als-lernende-uebersetzen.md) |
| Zertifikate von einem Trainer | Das Zertifikat, wie ausgestellt | [Zertifizierungen und Zertifikate](zertifizierungen-und-zertifikate.md) |
| Ihr Lerntagebuch (in Auszügen) | Eine Zusammenfassung dessen, was Sie gelernt haben | [Ein Lerntagebuch führen](ein-lerntagebuch-fuehren.md) |

Die Bildungsseite des Partnerprogramms sagt es so: „Echte Beiträge sind das beste Portfolio, das Studierende haben
können: Ein gemergter Pull Request ist ein öffentlicher Nachweis von Können“ ([Partner für Studierende und Bildung](../partner/partner-fuer-studierende-und-bildung.md#ideen-für-kurse-und-projekte)).

## Jedes Stück darstellen

Schreiben Sie zu jedem Stück vier Zeilen:

```text
What:     <one sentence: what it is>
My part:  <what you did yourself; name helpers and team members>
Built on: <Exponential release, PHP version, the features used>
Proof:    <links: repository, pull request, screenshots, report>
```

Kurz und überprüfbar schlägt lang und vage. Nennen Sie das Release („Exponential 6.0.15“), denn Funktionen ändern sich.

## Ehrliche Angaben

| Schreiben Sie | Schreiben Sie nicht |
|---|---|
| „Mehrsprachige Website mit Exponential 6.0.15 gebaut (Abschlussprojekt, selbst bewertet nach einem veröffentlichten Bewertungsraster)“ | „Exponential-Experte“ |
| „Redakteure 101 abgeschlossen (Prüfung des Trainers, bestanden)“ | „Exponential Certified Editor“ |
| „Zwei gemergte Pull Requests für den Exponential-Kernel“ | „Exponential-Core-Entwickler“ |
| „Eine Exponential-Erweiterung geschrieben, auf GitHub veröffentlicht“ | „Offizielle Exponential-Erweiterung“ |

Offizielle Exponential-Zertifizierungen sind geplant, aber noch nicht verfügbar ([Zertifizierungen und Zertifikate](zertifizierungen-und-zertifikate.md)).
Verwenden Sie die Produktnamen korrekt: Exponential, 7x, die Exponential Foundation
([Namen und Geschichte für Lernende](namen-und-geschichte-fuer-lernende.md)). Verwenden Sie keine Logos von 7x, der
Foundation oder des Partnerprogramms.

## Was Sie weglassen

- Passwörter, API-Schlüssel, Tokens, Dateien aus `settings/override/`, `kickstart.ini`.
- Hostnamen und Adressen der Server einer Schule oder eines Arbeitgebers.
- Personenbezogene Daten von irgendjemandem, auch von anderen Lernenden, sofern diese nicht zugestimmt haben.
- Inhalte, die Sie nicht veröffentlichen dürfen: Bilder, Schriften, Texte.
- Screenshots einer öffentlichen Demo, die Inhalte anderer Besucher zeigen.

## Eine Vorlage für die Portfolioseite

```text
# <Your name>: Exponential work

## In short
<Two sentences: which path, which release, what you can do.>

## Featured: <capstone title>
<four lines as above, two screenshots>

## Projects
- <project>: <one line>, <link>

## Contributions
- <pull request title>, <link>, <merged / open>

## Extensions and themes
- <name>: <one line>, <repository link>

## Certificates
- <course>, issued by <trainer or organization>, <date>, release <x>

## How I learned
<three sentences from your journal>
```

Bewahren Sie die Seite in einem eigenen Repository auf und aktualisieren Sie sie nach jedem Projekt.

## Verwandte Seiten

- [Zertifizierungen und Zertifikate](zertifizierungen-und-zertifikate.md)
- [Als Lernende beitragen](als-lernende-beitragen.md)
- [Projekt 10: Abschlussprojekt, eine vollständige kleine Website](projekt-10-abschlussprojekt-eine-vollstaendige-kleine-website.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
