---
title: "Lektion 3.5: Diese Kurse anpassen und verbessern"
audience: trainer, developer
category: training
order: 35
summary: Passen Sie die Trainerkurse an Ihre Organisation an und geben Sie Verbesserungen zurück: Auf GitHub bearbeiten, ein Fork und ein Pull Request für eine Korrektur, Ihre eigene geänderte Kopie unter der GFDL und das Melden von Fehlern. 40 Minuten.
keywords: anpassen, verbessern, auf github bearbeiten, pull request, fork, beitragen, fehler melden, kursmaterial, GFDL, community, adapt, improve, edit on github, contribute, report an error, course material
type: help
---
# Lektion 3.5: Diese Kurse anpassen und verbessern

Jeder Trainer findet etwas zu verbessern: einen unklaren Schritt, eine geänderte Schaltfläche, eine fehlende Frage.
Diese Kurse sind Teil der Dokumentation von Exponential, deshalb kann eine Korrektur allen Trainern nach Ihnen helfen.
Diese Lektion zeigt die drei Wege: eine private Kopie anpassen, eine Korrektur zurückgeben oder ein Problem melden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 3: Einen Kurs gestalten](README.md) |
| Dauer | 40 Minuten (10 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer; Entwickler, die ihnen helfen |
| Voraussetzungen | [Lektion 1.4](../modul-01-den-unterricht-vorbereiten/ihr-material-und-die-gfdl.md); ein GitHub-Konto für den Weg über einen Pull Request |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Quelldatei jeder Hilfeseite und ihren Link **Auf GitHub bearbeiten** finden;
- zwischen einer privaten Anpassung, einer zurückgegebenen Korrektur und einer Fehlermeldung wählen;
- eine kleine Änderung als Pull Request vorbereiten;
- ein Problem an der richtigen Stelle melden, ohne personenbezogene Daten.

## Hinweise für Trainer

### So präsentieren Sie es

1. Öffnen Sie eine beliebige Lektion im Hilfe-Center und zeigen Sie die Angaben zur Seite: die Quelldatei (unter
   `doc/eng-us/help/trainers/...`) und den Link **Auf GitHub bearbeiten** (3 Minuten).
2. Erklären Sie die drei Wege (5 Minuten):
   - **Private Anpassung** für Ihre Organisation: unter der GFDL erlaubt, zu den Bedingungen von Lektion 1.4.
   - **Zurückgegebene Korrektur**: ein Pull Request; angenommen unter derselben Lizenz, sodass sie für alle frei bleibt.
   - **Fehlermeldung**: Die Seite **Fehler melden** des Hilfe-Centers zeigt, welcher Tracker zu verwenden ist und was
     man schreiben sollte.
3. Zeigen Sie [Mit Git entwickeln](../../../mit-git-entwickeln.md) und seinen Abschnitt über das Einbringen einer Änderung
   mit einem Pull Request (2 Minuten).

### Häufige Fragen

**Muss ich Entwickler sein, um eine Korrektur zu schicken?** Nein. **Auf GitHub bearbeiten** öffnet die Datei im
Browser; GitHub erstellt den Fork und den Pull Request für Sie.

**Welcher Tracker für einen Fehler in der Dokumentation?** Die Seite **Fehler melden** listet die Tracker auf (die
Software, Velocity, das Projekt und die Community) und die Erweiterungen; die Dokumentation des Kernels gehört zur
Software.

### Stolperfallen

- **Große Überarbeitungen in einem Pull Request.** Kleine, gezielte Änderungen werden schneller geprüft.
- **Personenbezogene Daten in einer Meldung.** Fügen Sie niemals Namen von Teilnehmenden, E-Mail-Adressen oder
  Passwörter ein.
- **Sicherheitsprobleme.** Niemals in einem öffentlichen Issue; folgen Sie `SECURITY.md` (dem Teil
  **Sicherheitsprobleme** der Meldeseite).

## Praktische Übung

1. Öffnen Sie diese Lektion im Hilfe-Center und suchen Sie den Pfad ihrer Quelldatei und den Link
   **Auf GitHub bearbeiten**.
2. Öffnen Sie die Seite **Fehler melden** des Hilfe-Centers (`/exphelp/report`) und lesen Sie die Überschriften
   **Bevor Sie melden**, **Wohin melden**, **Die Meldung schreiben**, **Diagnosedaten** und **Sicherheitsprobleme**.
3. Suchen Sie in einer beliebigen Trainerlektion etwas zu verbessern (einen Tippfehler, einen unklaren Schritt).
4. Wenn Sie ein GitHub-Konto haben: Klicken Sie auf **Auf GitHub bearbeiten**, nehmen Sie die Änderung vor, beschreiben
   Sie sie in einem Satz und öffnen Sie einen Pull Request. Wenn nicht: Schreiben Sie die Änderung als Entwurf einer
   Fehlermeldung mit Titel, was passiert ist, Schritten und was Sie erwartet haben, und schicken Sie sie nicht ab.
5. Schreiben Sie die Lizenz auf, unter der Ihre Änderung angenommen wird.

## Erwartetes Ergebnis

- Die Teilnehmenden kennen den Quellpfad der Lektion und wissen, wo die Meldeseite ist.
- Ein Link auf einen Pull Request oder ein vollständiger Entwurf einer Fehlermeldung ohne personenbezogene Daten.
- Die Teilnehmenden können die Lizenz nennen (GNU FDL 1.3 oder eine spätere Version).

## Quiz

1. Wo finden Sie die Quelldatei einer Hilfeseite?
2. Unter welcher Lizenz wird eine Änderung an diesen Seiten angenommen?
3. Wohin gehört ein Sicherheitsproblem?
4. Nennen Sie die drei Wege, einen Kurs zu verbessern.

<details>
<summary>Antworten</summary>

1. In den Angaben zur Seite in der Ansicht, mit dem Link **Auf GitHub bearbeiten**.
2. Unter der GNU Free Documentation License 1.3 oder einer späteren Version
   ([Freie Dokumentation](../../../freie-dokumentation.md)).
3. Vertraulich, wie `SECURITY.md` es beschreibt; niemals in ein öffentliches Issue.
4. Eine private Anpassung, eine als Pull Request zurückgegebene Korrektur und eine Fehlermeldung.

</details>

## Weiterführende Literatur

- [Mit Git entwickeln](../../../mit-git-entwickeln.md): vom Benutzer zum Mitwirkenden.
- [Die Hilfe verbessern](../../../fuersprecher/die-hilfe-verbessern.md): die Anleitung der Fürsprecher für Korrekturen an der
  Hilfe.
- [Wenn etwas schiefgeht](../../../wenn-etwas-schiefgeht.md): Fehlermeldungen und Sicherheitsprobleme.

## Verwandte Seiten

- [Modul 3: Einen Kurs gestalten](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
