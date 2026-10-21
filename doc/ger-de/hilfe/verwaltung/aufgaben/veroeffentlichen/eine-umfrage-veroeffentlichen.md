---
title: Eine Umfrage veröffentlichen
audience: government, editor
category: public-sector
order: 500
summary: Für Redakteure - eine schnelle Meinungsfrage mit der mitgelieferten Klasse Poll veröffentlichen, ihre Optionen festlegen, Ergebnisse zeigen und sie ehrlich als Stimmungsbild darstellen; und wann ein Beteiligungsverfahren das bessere Werkzeug ist.
keywords: Umfrage, Klasse Poll, ezoption, Optionen, abstimmen, Ergebnisse, Informationssammler, collect.ini, eindeutig, Stimmungsbild, Meinung, poll, Poll class, options, vote, results, information collector, unique, indicative, opinion
type: help
---
# Eine Umfrage veröffentlichen

Diese Seite ist für Redakteure, die eine schnelle Frage stellen („Welcher Tag passt Ihnen für die Aufräumaktion?“).
Die mitgelieferte Klasse **Poll** (`poll`) hat eine Optionsliste; jede Antwort wird als Sammlung gespeichert, und
`collect.ini` behandelt Umfragen besonders: keine E-Mail je Stimme, eine Antwort je Benutzer, Ergebnisse nach der
Abstimmung ([Beteiligungsverfahren und Umfragen](../../aufbauen/formulare/beteiligungsverfahren-und-umfragen.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Eine Umfrage liefert ein Stimmungsbild, keine Entscheidung; für Meinungen zu einem Vorschlag verwenden Sie ein
  Beteiligungsverfahren ([Ein Beteiligungsverfahren veröffentlichen und Antworten sammeln](ein-beteiligungsverfahren-veroeffentlichen-und-antworten-sammeln.md)).
- Die Frage hat einige klare Optionen.
- Ihr Design zeigt Umfragen an (das Design ezwebin hat ein Template für Umfragen; andere brauchen vielleicht eines).

## Schritte

1. Öffnen Sie die Stelle für die Umfrage, **Neu erstellen**, wählen Sie **Poll**.
2. Füllen Sie den Namen (die Frage) und die Beschreibung aus.
3. Fügen Sie in der Optionsliste jede Option mit **Option hinzufügen** hinzu; markieren Sie leere Optionen und klicken
   Sie auf **Ausgewählte entfernen**.
4. **Vorschau**, dann **Zur Veröffentlichung schicken**.
5. Stimmen Sie zum Testen einmal abgemeldet ab und entfernen Sie die Testantwort dann unter **Setup > Gesammelte
   Informationen** ([Nicht mehr benötigte Formularantworten entfernen](nicht-mehr-benoetigte-formularantworten-entfernen.md)).

## Das Ergebnis prüfen

- [ ] Abgemeldet zeigt die Umfrage die Frage und die Optionen und nimmt eine Stimme an.
- [ ] Nach der Abstimmung zeigt die Ergebnisseite die Zahlen.
- [ ] **Setup > Gesammelte Informationen** listet die Umfrage mit ihren Sammlungen.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Ergebnisse als repräsentativ darstellen | anonyme Besucher teilen sich ein Konto, sodass Antworten keiner Person zugeordnet werden können; sagen Sie „Stimmungsbild“ |
| Suggestivfragen | neutrale Formulierung, ausgewogene Optionen |
| Optionen ändern, nachdem Stimmen eingegangen sind | frühere Stimmen beziehen sich auf die alten Optionen; beginnen Sie eine neue Umfrage |
| Umfragen, die ewig offen bleiben | verstecken Sie sie, wenn die Frage beantwortet ist |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Grundlagen des Informationssammlers](../../aufbauen/formulare/grundlagen-des-information-collectors.md)
- [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
