---
title: Ein Beteiligungsverfahren veröffentlichen und Antworten sammeln
audience: government, editor
category: public-sector
order: 130
summary: Für Fach- und Web-Redakteure - eine öffentliche Beteiligung mit einem Antwortformular auf Basis des Informationssammlers veröffentlichen, sagen, wie die Antworten verwendet werden, sie rechtzeitig schließen und die Antworten unter Setup > Gesammelte Informationen lesen.
keywords: Beteiligungsverfahren, öffentliche Beteiligung, Konsultation, Antwortformular, Informationssammler, Gesammelte Informationen, Einsendeschluss, Datenschutzhinweis, Antworten, Export, consultation, public consultation, response form, information collector, collected information, closing date, privacy note, responses, export
type: help
---
# Ein Beteiligungsverfahren veröffentlichen und Antworten sammeln

Diese Seite ist für Redakteure, die auf der Website ein Beteiligungsverfahren eröffnen. Sie nutzt eine Klasse
Consultation mit Sammelfeldern, wie in [Beteiligungsverfahren und Umfragen](../../aufbauen/formulare/beteiligungsverfahren-und-umfragen.md)
beschrieben; die Antworten werden unter **Setup > Gesammelte Informationen** gespeichert
([Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Schließen und Antworten lesen](#schließen-und-antworten-lesen)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Der Vorschlag, die Fragen und der Datenschutzhinweis (wer die Antworten sieht, ob sie veröffentlicht werden, wie
  lange sie aufbewahrt werden) sind genehmigt.
- Ihre Site hat eine Klasse Consultation mit Antwortfeldern (fragen Sie das Web-Team) oder ein Feedbackformular, das
  Sie verwenden können.
- Sie kennen das Datum der Eröffnung und des Schlusses.

## Schritte

1. Öffnen Sie **Consultations**, **Neu erstellen**, wählen Sie **Consultation**.
2. Füllen Sie Titel, den Vorschlag in Kürze, den Vorschlag, Dokumente (**Neu hochladen** oder **Vorhandenes
   hinzufügen**), Eröffnung und Schluss aus.
3. **How we will use your response**: der genehmigte Datenschutzhinweis.
4. **Vorschau**: Die Formularfelder (Ihre Antwort, Antwort als, Organisation, E-Mail) erscheinen unter dem Vorschlag.
5. **Zur Veröffentlichung schicken**.
6. Senden Sie abgemeldet eine Testantwort; prüfen Sie sie unter **Setup > Gesammelte Informationen** und entfernen Sie
   den Test dann ([Nicht mehr benötigte Formularantworten entfernen](nicht-mehr-benoetigte-formularantworten-entfernen.md)).

## Das Ergebnis prüfen

- [ ] Abgemeldet zeigt die Seite des Beteiligungsverfahrens den Vorschlag, den Einsendeschluss und das Formular.
- [ ] Das Formular lässt sich allein mit der Tastatur ausfüllen und absenden.
- [ ] Eine Testantwort erscheint unter **Setup > Gesammelte Informationen** beim Beteiligungsverfahren.

## Schließen und Antworten lesen

| Wann | Tun |
|---|---|
| Der Einsendeschluss ist vorbei | das Template zeigt statt des Formulars „geschlossen“, wenn es so gebaut wurde; sonst bearbeiten Sie die Seite und schreiben, dass es geschlossen ist |
| Auswertung | **Setup > Gesammelte Informationen**, die Karte des Beteiligungsverfahrens, **CSV exportieren** ([Formularantworten als CSV exportieren](formularantworten-als-csv-exportieren.md)) |
| Ergebnis | bearbeiten Sie das Beteiligungsverfahren, füllen Sie **Outcome** aus, veröffentlichen Sie |
| Die Aufbewahrungsfrist endet | entfernen Sie die Antworten |

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Mehr personenbezogene Daten erfragen als nötig | Name und Adresse nur, wenn das Beteiligungsverfahren sie erfordert |
| Eine lange Antwort geht verloren, weil das Formular abgelaufen ist | bitten Sie die Menschen, von langen Antworten eine Kopie zu behalten; die Seite „Formular abgelaufen“ sagt, dass nichts gespeichert wurde ([CSRF-Formulartoken](../../aufbauen/formulare/csrf-formular-token.md)) |
| Einzelne Antworten ohne Erlaubnis veröffentlichen | fragen Sie im Formular, ob eine Antwort veröffentlicht werden darf |
| Späte Antworten nach dem Schluss | vermerken Sie sie; der Sammler selbst setzt das Datum nicht durch |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Eine Umfrage veröffentlichen](eine-umfrage-veroeffentlichen.md)
- [Barrierefreie Formulare](../../aufbauen/formulare/barrierefreie-formulare.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
