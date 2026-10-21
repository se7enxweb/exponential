---
title: "Lektion 2.6: Exponential Intelligence, praktische Aufgabe"
audience: trainer, editor, admin
category: training
order: 26
summary: Eine praktische Aufgabe von 40 Minuten für das Ende des Kurses Exponential Intelligence, einen Artikel mit den Aktionen des Bearbeitungsformulars verbessern und jeden Vorschlag prüfen, den Assistenten aus dem Reiter Intelligence einen Entwurf schreiben lassen, Unerwünschtes verwerfen und als Administrator die Seite EI Summary lesen, bewertet mit einem Bewertungsraster aus fünf Kriterien. 65 Minuten mit Bewertung.
keywords: prüfungen und praktische übungen, exponential intelligence, expai, praktische aufgabe, bewertungsraster, umschreiben, verbessern, alternativtext, reiter intelligence, entwürfe, prüfung, EI Summary, kosten, assessments and practical exercises, practical task, rubric, rewrite, improve, alt text, intelligence tab, drafts, review, cost
type: help
---
# Lektion 2.6: Exponential Intelligence, praktische Aufgabe

Die geprüfte Fähigkeit ist nicht „KI benutzen“; es ist das **Prüfen** dessen, was der Assistent geschrieben hat, bevor
irgendetwas die Website erreicht. In dieser Aufgabe verwenden alle Teilnehmenden den Assistenten an ihrem eigenen
Artikel, behalten, was richtig ist, verwerfen, was es nicht ist, und zeigen dem Trainer, wo jeder Entwurf geblieben ist.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Verwenden Sie eine Schulungsinstallation
> mit eingeschaltetem Assistenten und einem Anbieter, den der Trainer kontrolliert: ein lokales Modell oder einen
> Anbieterschlüssel mit kleinem Budget und Limit. Geben Sie niemals personenbezogene Daten in Prompts ein und
> verwenden Sie niemals eine Live-Website.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 2: Die redaktionellen Kurse prüfen](README.md) |
| Dauer | 65 Minuten (5 Einweisung, 40 Aufgabe, 20 Bewertung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Teilnehmende am Ende des Kurses Exponential Intelligence |
| Voraussetzungen | [Lektion 2.5](intelligence-schriftlicher-test.md); ein Redakteurskonto mit den Richtlinien des Assistenten auf einer Schulungsinstallation |

## Lernziele

Wer diese Aufgabe besteht, hat gezeigt, dass er Folgendes kann:

- Feldaktionen im Bearbeitungsformular verwenden und jeden Vorschlag annehmen oder ablehnen;
- Fakten und Ton eines Vorschlags prüfen, bevor er gespeichert wird;
- den Assistenten aus dem Reiter **Intelligence** einen Entwurf schreiben lassen und ihn wiederfinden;
- einen unerwünschten Entwurf verwerfen;
- (Administratoren) die Seite **EI: Summary** lesen und ihre Abschnitte erklären.

## Hinweise für Trainer

### So führen Sie sie durch

1. **Vorher**: Schalten Sie den Assistenten auf der Schulungsinstallation ein, wie in
   [expai: Exponential Intelligence](../../../../../eng-us/features/6.0/extensions/expai.md#switch-it-on) beschrieben: die
   Erweiterung für den Siteaccess der Administration aktiv, der Schlüssel in
   `settings/override/expai.ini.append.php`, die Tabellen angelegt, die Richtlinien `expai/write` und `ezjscore/call`
   für die Rolle der Teilnehmenden und die Cronjob-Teile `expqueue` und `expai` bei Bedarf von Hand ausgeführt. Setzen
   Sie Limits pro Benutzer und pro Tag. Bereiten Sie pro Teilnehmer einen Artikel mit einem absichtlich schwachen
   Intro und einem sachlichen Fehler im Body vor (zum Beispiel ein falsches Eröffnungsdatum) sowie ein Bild ohne
   Alternativtext.
2. **Einweisung**: Bei der Aufgabe geht es um das Prüfen. Sagen Sie, dass ein ungeprüft gespeicherter Vorschlag als
   Fehler bewertet wird.
3. **Bewertung**: Lesen Sie den Artikel jeder Teilnehmerin und jedes Teilnehmers, seine Versionen und die Liste der
   Entwürfe im Reiter **Intelligence**.

### Häufige Fragen

**Das Modell ist langsam oder nicht verfügbar.** Halten Sie die Zeit der Teilnehmenden an und geben Sie die Zeit
zurück, wie bei jeder ausgefallenen Übungsumgebung
([Lektion 1.5](../modul-01-pruefungen-gestalten/eine-pruefung-durchfuehren.md)).

**Verschiedene Teilnehmende bekommen verschiedene Vorschläge.** Das ist zu erwarten. Das Bewertungsraster bewertet das
Prüfen, nicht die Formulierung, die das Modell erzeugt hat.

### Stolperfallen

- **Überraschende Kosten.** Beobachten Sie bei einer Probe die Kostenzeile oben im Bearbeitungsformular und setzen Sie
  Limits vor dem Unterricht.
- **Teilnehmende, die eigene Daten in Prompts einfügen.** Sagen Sie vor der Aufgabe: nur den Text des
  Schulungsartikels.

## Praktische Übung

Die Aufgabe, wie sie an die Teilnehmenden ausgeteilt wird. Verwenden Sie Ihre Schulungsinstallation und Ihr
Redakteurskonto.

1. Öffnen Sie den Artikel, den Ihr Trainer für Sie vorbereitet hat, und drücken Sie **Bearbeiten**. Lesen Sie den
   Kasten oben im Formular: Notieren Sie, welcher Dienst verwendet wird und was ein Klick kostet.
2. Verwenden Sie beim **Intro** **Verbessern** (oder **Umschreiben**). Lesen Sie den Vorschlag. Nehmen Sie ihn nur an,
   wenn er richtig ist und den Sinn bewahrt; lehnen Sie ihn andernfalls ab und schreiben Sie das Intro selbst.
3. Lesen Sie den **Body**. Er enthält einen sachlichen Fehler, den Ihr Trainer eingebaut hat. Verwenden Sie beim Body
   nach Belieben **Kürzen** oder **Umschreiben**, aber stellen Sie sicher, dass der Fehler in der Version, die Sie
   behalten, korrigiert ist.
4. Drücken Sie **Entwurf speichern und beenden**. Veröffentlichen Sie noch nicht.
5. Öffnen Sie das Bild ohne Alternativtext, bearbeiten Sie es und lassen Sie den Assistenten einen Alternativtext
   vorschlagen. Korrigieren Sie ihn bei Bedarf und veröffentlichen Sie das Bild.
6. Öffnen Sie auf der Seite Ihres Artikels den Reiter **Intelligence** und bitten Sie: „Schreibe auf Grundlage dieses
   Artikels einen kurzen Teaser-Artikel für den Newsletter.“ Sehen Sie sich die Schritte an, die er zeigt.
7. Suchen Sie den Entwurf, den er geschrieben hat, in der Liste der Entwürfe im Reiter. Öffnen Sie ihn, lesen Sie ihn
   und entscheiden Sie: als Entwurf behalten oder verwerfen. Schreiben Sie in einem Satz, warum.
8. Gehen Sie zurück zu Ihrem eigenen Entwurf aus Schritt 4, prüfen Sie ihn noch einmal und veröffentlichen Sie ihn.
9. (Nur Administratoren) Öffnen Sie **EI: Summary**. Notieren Sie die Namen seiner Abschnitte und für einen Anbieter
   seine Region und Aufbewahrung.

## Erwartetes Ergebnis

- Der Artikel ist mit einem richtigen Intro und ohne den eingebauten Fehler veröffentlicht; seine Seite
  **Versionen verwalten** zeigt die neue Version.
- Das Bild hat einen sinnvollen Alternativtext.
- Der Reiter **Intelligence** führt den Teaser-Entwurf als behalten oder verworfen, passend zum Satz der Teilnehmenden.
- Die Notizen der Teilnehmenden nennen Dienst und Kosten aus Schritt 1 und (Administratoren) die Abschnitte von
  **EI: Summary**: Hauptschalter und Oberflächen, Anbieter, Lanes und ihre Aufbewahrung, Datenrichtlinie, Limits,
  Budget und Nutzung in diesem Monat, Werkzeuge, die über die Website hinausreichen, Was diese Website speichert,
  Rollen mit Richtlinien für Exponential Intelligence.

## Bewertungsraster

| Kriterium | 0: Nicht gezeigt | 1: Teilweise | 2: Größtenteils | 3: Vollständig | Nachweis |
|---|---|---|---|---|---|
| Prüfen der Vorschläge (Pflicht) | Eingebauter Fehler veröffentlicht | Fehler gefunden, aber anderswo ein falscher Vorschlag behalten | Fehler behoben, Ton oder Sinn leicht daneben | Fehler behoben, jeder behaltene Vorschlag richtig | Der veröffentlichte Artikel |
| Feldaktionen verwenden | Keine verwendet | Eine Aktion, Ergebnis nicht geprüft | Aktionen verwendet, ein Vorschlag ungelesen gespeichert | Aktionen verwendet und jeweils bewusst entschieden | Die Schilderung der Teilnehmenden, die Versionen |
| Alternativtext | Fehlt | Erzeugt, nicht geprüft, falsch | Vorhanden, vage | Vorhanden und beschreibt das Bild | Die Seite des Bildes |
| Entwürfe aus dem Reiter | Kein Entwurf erstellt | Entwurf erstellt, nicht gefunden | Gefunden, keine Entscheidung | Gefunden, mit Begründung behalten oder verworfen | Der Reiter **Intelligence**, die Notiz |
| Bewusstsein für Kosten und Richtlinien | Nichts notiert | Nur der Dienst | Dienst und Kosten | Dienst, Kosten und (Administratoren) die Abschnitte der Übersicht | Die Notizen der Teilnehmenden |

**Bestanden**: 10 von 15 Punkten oder mehr und **Prüfen der Vorschläge** mit 2 oder 3. Bei Redakteuren bewerten Sie
das letzte Kriterium nur nach Dienst und Kosten.

## Quiz

Fragen für die Nachbesprechung nach der Bewertung.

1. Wer hat den Artikel in Schritt 8 veröffentlicht: Sie oder der Assistent?
2. Warum war der eingebaute Fehler der wichtigste Teil der Aufgabe?
3. Was geschah mit dem veröffentlichten Artikel, während Sie in den Schritten 2 bis 4 gearbeitet haben?
4. Wo würde ein Administrator sehen, dass Sie den Assistenten heute verwendet haben?

<details>
<summary>Antworten</summary>

1. Sie. Der Assistent veröffentlicht nie von sich aus
   ([expai](../../../../../eng-us/features/6.0/extensions/expai.md#in-short)).
2. Ein Modell kann flüssigen Text schreiben, der falsch ist; die Redakteurin bleibt verantwortlich für das, was
   veröffentlicht wird.
3. Nichts änderte sich, bis Sie veröffentlicht haben; Vorschläge ändern nichts, bis sie angenommen und gespeichert sind
   ([Inhalte erstellen und bearbeiten](../../../inhalte-bearbeiten.md#exponential-intelligence)).
4. Im Audit, Kanal `ai`, und im Nutzungsprotokoll, das nur Benutzer-IDs enthält
   ([expai](../../../../../eng-us/features/6.0/extensions/expai.md#for-administrators)).

</details>

## Weiterführende Literatur

- [expai: Exponential Intelligence](../../../../../eng-us/features/6.0/extensions/expai.md): was vor dem Unterricht
  einzuschalten ist.
- [Eine Prüfungssitzung durchführen](../modul-01-pruefungen-gestalten/eine-pruefung-durchfuehren.md): ausgefallene
  Übungsumgebungen und verlorene Zeit.

## Verwandte Seiten

- [Lektion 2.5: Exponential Intelligence, schriftlicher Test](intelligence-schriftlicher-test.md)
- [Modul 2: Die redaktionellen Kurse prüfen](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
