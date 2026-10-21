---
title: Schritt der rechtlichen Prüfung
audience: government, admin, editor
category: public-sector
order: 70
summary: Zwei Wege, dem Veröffentlichen in Exponential eine rechtliche oder Compliance-Prüfung hinzuzufügen - eine zweite Gruppe von Freigebenden im Workflow oder ein Objektzustand „Rechtlich geprüft“, den Rollen berücksichtigen - mit den Vor- und Nachteilen und dem, was keiner von beiden leistet.
keywords: rechtliche Prüfung, Compliance-Prüfung, rechtliche Freigabe, Rechtsberatung, Abnahme, Freigabe, Objektzustand, Freigabe-Ereignis, zweite freigebende Person, regulierte Inhalte, Bekanntmachungen, öffentliche Mitteilungen, legal review, compliance review, legal clearance, counsel, sign-off, approval, object state, Approve event, second approver, regulated content, notices, public announcements
type: help
---
# Schritt der rechtlichen Prüfung

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für Ihre Organisation geltende
> Recht mit Ihrer Rechtsberatung.

Diese Seite richtet sich an Webverantwortliche, deren Organisation für bestimmte Inhalte vor der Veröffentlichung eine rechtliche oder
Compliance-Prüfung verlangt: Bekanntmachungen, Verordnungen, Ausschreibungen der Beschaffung, Stellungnahmen. Exponential hat keine eigene Funktion
für die rechtliche Prüfung; die Prüfung wird aus einem **Freigabe-Workflow** oder einem **Objektzustand** gebaut. Quellen:
[Workflows](../../../../anleitungen/workflows.md) und [Objektzustände](../../../../anleitungen/objektzustaende.md).

## Inhalt
1. [Den Mechanismus wählen](#den-mechanismus-wählen)
2. [Möglichkeit A: rechtlich Prüfende als Freigebende](#möglichkeit-a-rechtlich-prüfende-als-freigebende)
3. [Möglichkeit B: eine Zustandsgruppe „Recht“](#möglichkeit-b-eine-zustandsgruppe-recht)
4. [Entscheiden, welche Inhalte eine Prüfung brauchen](#entscheiden-welche-inhalte-eine-prüfung-brauchen)
5. [Was es nicht leistet](#was-es-nicht-leistet)
6. [Verwandte Seiten](#verwandte-seiten)

## Den Mechanismus wählen

| | Möglichkeit A: Workflow | Möglichkeit B: Zustand |
|---|---|---|
| Hält die Veröffentlichung zurück | ja, die Version wartet | nur wenn die Leserichtlinie für Besucher den Zustand verlangt |
| Die prüfende Person arbeitet in | dem Posteingang der Zusammenarbeit | dem Tab **Details** des Objekts, Zustand des Objekts |
| Übersteht spätere Bearbeitungen | nein, jede Version wird erneut geprüft | ja, der Zustand bleibt, bis jemand ihn ändert |
| Am besten für | jede Veröffentlichung in einem rechtlich relevanten Bereich | eine Freigabe je Dokument, das später für Tippfehler bearbeitet wird |

## Möglichkeit A: rechtlich Prüfende als Freigebende

1. Legen Sie eine Benutzergruppe „Rechtliche Prüfung“ an und geben Sie ihr die Rolle für Freigebende
   ([Rollenentwurf: Freigebende](../personen-und-berechtigungen/rollenentwurf-freigebende.md)).
2. Legen Sie die rechtlich relevanten Inhalte in eine eigene Sektion (zum Beispiel „Bekanntmachungen“) oder entscheiden Sie anhand einer bereits
   verwendeten Sektion.
3. Fügen Sie im Freigabe-Workflow ein zweites Ereignis **Freigeben** hinzu, mit **Betroffene Sektionen** = die rechtlich relevante(n) Sektion(en)
   und **Gruppen, die Content freigeben** = Rechtliche Prüfung. Inhalte brauchen dann die redaktionelle und die rechtliche Freigabe.
4. Oder verwenden Sie, wenn die rechtliche Prüfung dort die redaktionelle ersetzt, ein Freigabe-Ereignis je Bereich.

Die eigenen Inhalte der freigebenden Person lässt das Ereignis durch, daher sollten rechtlich Prüfende in dem Bereich, den sie freigeben, keine
Inhalte erstellen.

## Möglichkeit B: eine Zustandsgruppe „Recht“

Nach dem Prüfbeispiel aus [Objektzustände, Abschnitt 9](../../../../anleitungen/objektzustaende.md#9-ein-durchgearbeitetes-beispiel-ein-prüf-workflow):

1. **Setup > Zustände**: Legen Sie die Gruppe `legal` mit den Zuständen Nicht nötig (als erster, also Standard), Rechtliche Prüfung ausstehend
   und Geprüft an.
2. Rolle Anonymous: Fügen Sie ihrer Richtlinie `content/read` die Einschränkung `StateGroup_legal = Not needed, Cleared` hinzu, damit ausstehende
   Inhalte für Besucher unsichtbar sind.
3. Redaktionen: `state/assign` mit `NewState = legal/pending_legal` für die rechtlich relevante Sektion.
4. Rechtlich Prüfende: `state/assign` mit `NewState = legal/cleared, legal/pending_legal`.
5. Jede Zustandsänderung wird im Prüfprotokoll als `content.object.state` erfasst, mit den Zuständen vorher und nachher.

Setzen Sie bestehende öffentliche Inhalte auf Nicht nötig (oder Geprüft), bevor Sie die Rolle Anonymous einschränken.

## Entscheiden, welche Inhalte eine Prüfung brauchen

Schreiben Sie es auf: welche Sektionen oder Klassen, welche Arten von Änderungen (nur neue Elemente oder jede Änderung), wie schnell die
Prüfenden antworten und was geschieht, wenn sie abwesend sind. Drücken Sie es mit Betroffene Sektionen, Betroffene Versionen oder einer
Einschränkung nach Klasse aus; vermeiden Sie „die Redaktion entscheidet im Einzelfall“, was sich später nicht überprüfen lässt.

## Was es nicht leistet

- Es prüft den Text nicht: Das tun die Prüfenden.
- Es führt keine eigene Rechtsakte; der Nachweis sind das Element der Zusammenarbeit, die Versionsgeschichte und das Prüfprotokoll. Exportieren Sie,
  was Ihre Regeln zum Schriftgut verlangen ([Versionsgeschichte und Rechenschaft](versionsverlauf-und-nachvollziehbarkeit.md)).
- Es verhindert nicht das Entfernen oder Verbergen von Inhalten; das sind getrennte Rechte.

## Verwandte Seiten
- [Vier-Augen-Prinzip beim Veröffentlichen](vier-augen-prinzip-beim-veroeffentlichen.md)
- [Objektzustände in Berechtigungen](../personen-und-berechtigungen/objektzustaende-in-berechtigungen.md)
- [Eine Zustandsgruppe für Prüfungen einrichten](../../aufgaben/personen-und-berechtigungen/eine-zustandsgruppe-fuer-die-pruefung-einrichten.md)
- [Das Freigabe-Ereignis einrichten](das-freigabe-ereignis-einrichten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
