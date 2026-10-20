---
title: Archivieren und der Papierkorb
audience: customer, editor, admin
category: customer-success
order: 24
summary: Die Möglichkeiten des Betreibers für Inhalte, die nicht mehr aktuell sind, auf einer Exponential-Website - behalten, verbergen, in einen Archivbereich verschieben, in den Papierkorb entfernen oder endgültig löschen -, was jede davon für Besucher und Adressen bedeutet, wer wiederherstellen darf, und eine Routine für den Papierkorb.
keywords: Archiv, Papierkorb, verbergen, entfernen, wiederherstellen, Papierkorb leeren, archivierte Inhalte, Sektion, Objektzustände, Aufbewahrung, Website-Betreiber, archive, trash, hide, remove, restore, empty trash, archived content, section, object states, retention, site owner
type: help
---
# Archivieren und der Papierkorb

Diese Seite ist für den Betreiber und die Chefredaktion, die entscheiden, was mit Inhalten geschieht, die nicht mehr
aktuell sind. Exponential bietet mehrere Stufen zwischen „auf der Website“ und „endgültig weg“; die richtige Wahl
hält Historie, Adressen und Suchergebnisse in Ordnung. Die Klicks stehen unter
[Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md).

## Inhalt

1. [Fünf Möglichkeiten](#fünf-möglichkeiten)
2. [Verbergen](#verbergen)
3. [Ein Archivbereich](#ein-archivbereich)
4. [Der Papierkorb](#der-papierkorb)
5. [Endgültig löschen](#endgültig-löschen)
6. [Eine Routine für den Papierkorb](#eine-routine-für-den-papierkorb)

## Fünf Möglichkeiten

| Möglichkeit | Sehen Besucher es? | Funktioniert die Adresse? | Rückgängig zu machen? |
|---|---|---|---|
| Behalten, als älter gekennzeichnet (ein Hinweis im Text) | ja | ja | ja |
| Verbergen | nein, und auch nichts darunter | nein | ja, wieder einblenden |
| In einen Archivbereich verschieben | hängt von der Sektion des Bereichs ab | die alte Adresse leitet an den neuen Ort weiter | ja, zurückverschieben |
| In den Papierkorb entfernen | nein | nein | ja, wiederherstellen |
| Aus dem Papierkorb löschen | nein | nein | **nein** |

Quellen: [Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md),
[Weiterleitungen und verschobene Inhalte](../../../../en/us/help/creators/website-creators/redirects-and-moved-content.md).

## Verbergen

Ein verborgenes Element bleibt in der Administration, wird aber auf der Website nicht angezeigt, und auch nichts
darunter. Verwenden Sie das für Inhalte, die nicht fertig sind oder eine Zeit lang nicht gebraucht werden
([Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md#ein-objekt-verstecken)). Ihr Administrator
kann Elemente auch automatisch zu einem im Element gespeicherten Datum verbergen lassen ([Zeitgesteuertes Veröffentlichen](zeitgesteuertes-veroeffentlichen.md)).

## Ein Archivbereich

Für Inhalte, die öffentlich zugänglich bleiben sollen (alte Pressemitteilungen, vergangene Veranstaltungen), ist ein
**Archivordner** oft die beste Lösung:

- Wenn Inhalte dorthin verschoben werden, funktioniert ihre alte Adresse weiter: Der alte Pfad leitet weiter.
- Liegt der Archivordner in einer **Sektion**, die anonyme Besucher nicht lesen dürfen, wird er zum internen Archiv;
  dürfen Besucher sie lesen, ist es ein öffentliches Archiv ([Sektionen](../../../anleitungen/sektionen.md)).
- Objektzustände können Inhalte als archiviert kennzeichnen, ohne sie zu verschieben, zur Verwendung durch Rollen und
  Templates ([Objektzustände](../../../anleitungen/objektzustaende.md)).

Fragen Sie Ihren Website-Ersteller, welche dieser Möglichkeiten zu Ihrer Website passt; das Verschieben von Inhalten
zwischen Sektionen kann ändern, wer sie sehen darf.

## Der Papierkorb

**Entfernen** verschiebt ein Element mit allem, was darunter liegt, in den Papierkorb. Hat ein Element mehr als einen
Ort, bleibt es beim Entfernen eines Ortes an seinen anderen Orten erhalten; es kommt erst in den Papierkorb, wenn sein
letzter Ort entfernt wird ([Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md#entfernen-und-wiederherstellen)).

Der Papierkorb listet jedes Element mit der Angabe, wer es wann entfernt hat und wo es lag, mit Filtern nach Benutzer,
Typ und Datum ([Die Ansicht des Papierkorbs](../../../../en/us/bc/6.0/trash.md)). **Wiederherstellen** bringt ein Element
zurück: an seinen ursprünglichen Ort, wenn dieser noch existiert; liegt auch das übergeordnete Element im Papierkorb,
stellen Sie zuerst dieses wieder her; andernfalls wählen Sie einen neuen Ort.

> **Hinweis**
> Ihre Rolle entscheidet, ob Sie aus dem Papierkorb wiederherstellen oder löschen dürfen. Benutzer kommen nie in den
> Papierkorb: Das Entfernen eines Benutzerkontos ist endgültig ([Benutzerkonten prüfen](../../../../en/us/features/6.0/user-hygiene.md)).

## Endgültig löschen

**Ausgewählte entfernen** im Papierkorb und **Papierkorb leeren** löschen Elemente endgültig; beides lässt sich nicht
rückgängig machen. **Papierkorb leeren** leert den ganzen Papierkorb, unabhängig vom angezeigten Filter
([Die Ansicht des Papierkorbs](../../../../en/us/bc/6.0/trash.md)). Bevor Sie ihn leeren:

- prüfen Sie, dass nichts mehr auf die Elemente verweist (kaputte Beziehungen, [Inhalts-Audits](inhalts-audits.md));
- prüfen Sie, ob Ihre Organisation bestimmte Unterlagen für einen Zeitraum aufbewahren muss (fragen Sie die
  zuständige Person; dies ist keine Rechtsberatung, siehe [Datenaufbewahrung und Löschung](../../../../en/us/help/businesses/security/data-retention-and-deletion.md));
- denken Sie daran, dass die Backups die Inhalte so lange enthalten, wie sie aufbewahrt werden.

## Eine Routine für den Papierkorb

| Wann | Was | Wer |
|---|---|---|
| Wöchentlich | ansehen, was diese Woche in den Papierkorb kam (Filter nach Datum); Versehen wiederherstellen | Chefredaktion |
| Monatlich | Elemente löschen, die vor mehr als einem bestimmten Zeitraum in den Papierkorb kamen und die niemand vermisst hat | Chefredaktion, mit dem vereinbarten Zeitraum |
| Jährlich | den Zeitraum mit dem Eigentümer und Ihren Aufbewahrungsregeln abstimmen | Eigentümer |

Tragen Sie hier Ihren Zeitraum ein: `Items stay in the trash for at least ____ before they are deleted.`

## Verwandte Seiten

- [Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md)
- [Inhalts-Audits](inhalts-audits.md)
- [Der Inhaltsfinder](../../../../en/us/features/6.0/content-finder.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
