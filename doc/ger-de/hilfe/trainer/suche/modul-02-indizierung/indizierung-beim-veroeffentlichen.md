---
title: "Lektion 2.1: Indizierung beim Veröffentlichen"
audience: trainer, admin
category: training
order: 21
summary: Die Teilnehmenden verfolgen, was mit dem Suchindex geschieht, wenn Inhalte veröffentlicht, bearbeitet, verborgen, verschoben oder in den Papierkorb gelegt werden, und weisen es mit Suchen auf ihrer Schulungsinstallation nach. 25 Minuten.
keywords: suchkurs, indizierung, veröffentlichen, suchindex, neue version, verbergen, verschieben, papierkorb, entwürfe, eZSearchEngine, search course, indexing, publish, search index, new version, hide, move, trash, drafts
type: help
---
# Lektion 2.1: Indizierung beim Veröffentlichen

Mit den mitgelieferten Einstellungen aktualisiert Exponential den Suchindex in dem Moment, in dem Inhalte
veröffentlicht werden. Diese Lektion zeigt den Teilnehmenden, was das in der Praxis bedeutet: Ein Entwurf wird nie
gefunden, eine neue Version ersetzt die alten Wörter, und manche Vorgänge brauchen überhaupt keine Neuindizierung.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 2: Indizierung](README.md) |
| Dauer | 25 Minuten (10 Präsentation, 15 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren und Support-Mitarbeitende |
| Voraussetzungen | [Modul 1](../modul-01-wie-die-suche-funktioniert/README.md); ein Administratorkonto auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, dass nur veröffentlichte Versionen indiziert werden, niemals Entwürfe;
- zeigen, dass das Veröffentlichen einer neuen Version die indizierten Wörter des Objekts ersetzt;
- sagen, für welche Vorgänge die eingebaute Suchmaschine keine Indexaktualisierung braucht (Verschieben, Verbergen,
  Anzeigen, ein neuer Ort);
- vorhersagen, was eine Suche findet, nachdem ein Element in den Papierkorb verschoben wurde.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [10.8 Suchindizierung und Bildverarbeitung](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung)
und [Veröffentlichen, Versionen und der Papierkorb](../../../veroeffentlichen-und-versionen.md).

1. **Veröffentlichen indiziert** (3 Minuten). Der Veröffentlichungsvorgang übergibt das Objekt an die Suchmaschine,
   die seine alten Wörter entfernt und die Wörter der neuen veröffentlichten Version hinzufügt. Ein Entwurf ist nicht
   Teil des Index; nur die veröffentlichte Version ist es.
2. **Was die eingebaute Suchmaschine nicht braucht** (4 Minuten). Zeigen Sie die Tabelle aus Kapitel 10.8. Für
   `eZSearchEngine` stellen Verschieben, ein neuer Ort, ein Tausch, Verbergen und Anzeigen nichts in die
   Warteschlange, weil ihr Index keine Pfade enthält: Die Suchmaschine prüft Orte und Sichtbarkeit beim Suchen. Solr
   speichert Pfade, deshalb aktualisieren diese Vorgänge bei ezfind den Index (Modul 4).
3. **Papierkorb und Löschen** (3 Minuten). Das Verschieben in den Papierkorb oder das Löschen entfernt das Objekt aus
   der Suche; das Wiederherstellen aus dem Papierkorb macht es wieder auffindbar.

### Häufige Fragen

**Ich habe veröffentlicht, und das Element wird nicht gefunden. Warum?** Mit den mitgelieferten Einstellungen wird es
sofort gefunden. Wenn nicht, verwendet die Website möglicherweise die verzögerte Indizierung (Lektion 2.2), der
suchende Benutzer darf es nicht lesen, oder das Attribut, das das Wort enthält, ist nicht durchsuchbar (Lektion 2.4).

**Werden verborgene Elemente gefunden?** Die Suchmaschine filtert verborgene Elemente beim Suchen nach den
Sichtbarkeitsregeln des Siteaccess; auf der öffentlichen Website werden sie nicht gefunden.

**Taucht der Entwurf eines anderen Redakteurs in meiner Suche auf?** Nein. Entwürfe werden unter **Meine Entwürfe**
aufgelistet, nicht in der Suche.

### Stolperfallen

- **Mit einem Wort aus der alten Version testen.** Die Teilnehmenden suchen nach einem Wort, das sie gerade entfernt
  haben, und sind überrascht, dass es weg ist. Genau das ist der Sinn der Übung: Lassen Sie sie es entdecken.
- **Verzögerte Indizierung in der Übungsumgebung eingeschaltet.** Wenn ein vorheriger Kurs `DelayedIndexing=enabled`
  hinterlassen hat, wird nichts sofort gefunden. Prüfen Sie die Einstellung vor dem Kurs
  (`exp:ini get site.ini/SearchSettings/DelayedIndexing`).

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, als Administrator in der Administration angemeldet.

1. Klicken Sie unter einem eigenen Ordner auf **Neu erstellen**, wählen Sie **Article** und geben Sie ihm den Titel
   `Indexing test pear`. Füllen Sie die Pflichtfelder mit Text, der das Wort `orchard` enthält.
2. Klicken Sie auf **Entwurf speichern und beenden**, statt zu veröffentlichen. Suchen Sie mit dem Feld **Suchen...**
   nach `orchard`.
3. Öffnen Sie auf dem Dashboard **Meine Entwürfe**, setzen Sie den Entwurf fort und klicken Sie auf **Zur
   Veröffentlichung schicken**. Suchen Sie erneut nach `orchard`.
4. Klicken Sie beim Artikel auf **Bearbeiten**, ersetzen Sie das Wort `orchard` überall durch `greenhouse` und
   veröffentlichen Sie.
5. Suchen Sie nach `orchard`, dann nach `greenhouse`.
6. Verschieben Sie den Artikel in einen anderen Ordner (**Verschieben**). Suchen Sie nach `greenhouse`: Wird er noch
   gefunden, und mit welchem Pfad?
7. Klicken Sie beim Artikel auf **Entfernen** und bestätigen Sie, dass er in den Papierkorb kommt (löschen Sie ihn
   nicht endgültig). Suchen Sie nach `greenhouse`.
8. Öffnen Sie den **Papierkorb**, stellen Sie den Artikel an seinem ursprünglichen Ort wieder her und suchen Sie noch
   einmal.

## Erwartetes Ergebnis

- Schritt 2 findet nichts: Der Entwurf ist nicht indiziert.
- Schritt 3 findet den Artikel sofort.
- Schritt 5: `orchard` findet nichts, `greenhouse` findet den Artikel (die neue Version hat die Wörter ersetzt).
- Schritt 6 findet ihn mit seinem neuen Pfad; nichts musste neu indiziert werden.
- Schritt 7 findet nichts; Schritt 8 findet ihn wieder.
- Der Trainer prüft das, indem er eine Person bittet, die Schritte 5 und 7 am Beamer zu zeigen.

## Quiz

1. Richtig oder falsch: Ein gespeicherter Entwurf wird von der Suche der Administration gefunden.
2. Sie veröffentlichen Version 3 eines Artikels. Welche Wörter enthält der Index danach für ihn?
   A. die der Versionen 1 bis 3 B. nur die der Version 3 C. nur die der Version 1 D. keine, bis der Cronjob läuft
3. Braucht das Verschieben eines Elements mit der eingebauten Suchmaschine eine Indexaktualisierung? Warum?
4. Was geschieht mit den Suchergebnissen, wenn ein Element in den Papierkorb verschoben wird?
5. Nennen Sie zwei Gründe, warum ein gerade veröffentlichtes Element möglicherweise nicht gefunden wird.

<details>
<summary>Antworten</summary>

1. **Falsch.** Nur veröffentlichte Versionen werden indiziert.
2. **B.** Das Veröffentlichen ersetzt die indizierten Wörter des Objekts durch die der neuen veröffentlichten Version
   (mit den mitgelieferten Einstellungen, ohne verzögerte Indizierung).
3. Nein. Ihr Index enthält keine Pfade; Orte und Sichtbarkeit werden beim Suchen geprüft
   ([10.8](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung)).
4. Es wird nicht mehr gefunden; nach einer Wiederherstellung wird es wieder gefunden.
5. Zwei beliebige aus: Die verzögerte Indizierung ist eingeschaltet und der Cronjob ist nicht gelaufen; der Benutzer
   darf es nicht lesen; das Wort steht in einem nicht durchsuchbaren Attribut; es ist verborgen.

</details>

## Weiterführende Informationen

- [10.8 Suchindizierung und Bildverarbeitung](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung):
  was jeder Vorgang in die Warteschlange stellt.
- [Veröffentlichen, Versionen und der Papierkorb](../../../veroeffentlichen-und-versionen.md): Versionen und der Papierkorb für
  Redakteure.
- [Suchen](../../../suchen.md): was Redakteuren über neue Elemente in der Suche gesagt wird.

## Verwandte Seiten

- [Modul 2: Indizierung](README.md)
- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
