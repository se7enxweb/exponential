---
title: "Objekte anlegen und bearbeiten"
audience: editor, admin, builder
category: books
order: 280
summary: Ein Objekt aus einer Klasse anlegen, die Leiste der Orte (Hauptort, verschieben, hinzufügen, entfernen), was zusätzliche Orte bewirken und was nicht, und Informationssammler.
keywords: Objekt anlegen, Hier erstellen, bearbeiten, Orte, Hauptort, verschieben, zusätzlicher Ort, Informationssammler, Entwurf, Buch Learning, create object, create here, edit, locations, main location, move, additional location, information collector, draft, Learning book
type: help
---
# Objekte anlegen und bearbeiten

## Ein Objekt anlegen

Objekte werden in der **Inhaltsstruktur** (und unter **Medien**) angelegt. Gehen Sie im Baum zu der Stelle, an der das
neue Objekt stehen soll, wählen Sie die Klasse in **Hier erstellen** (die Liste enthält nur die Klassen, die Ihre Rolle
dort anlegen darf) und drücken Sie die Schaltfläche. Der Bearbeitungsbildschirm öffnet sich mit den Attributen der
Klasse, die Pflichtfelder markiert. Ab diesem Moment gibt es einen **Entwurf** mit einer Objekt-ID und einer Knoten-ID;
nichts ist öffentlich, bis Sie **Veröffentlichen**.

Die Schritt-für-Schritt-Fassung für Redakteure steht in [Inhalte bearbeiten](../inhalte-bearbeiten.md) und
[Veröffentlichen und Versionen](../veroeffentlichen-und-versionen.md). Die Referenz ist
[Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md).

## Orte

Der Bearbeitungsbildschirm zeigt, wo das Objekt platziert ist:

- Der **Hauptort** ist die Platzierung, deren Position dem Objekt seine Adresse und seine Standardsektion gibt. Ein Ort
  ist immer der Hauptort.
- **Verschieben** ändert den Hauptort: Gehen Sie durch den Baum und wählen Sie den neuen Elternknoten.
- Das Symbol **Entfernen** löscht einen oder mehrere der zusätzlichen Orte dieses Objekts.
- **Orte hinzufügen** öffnet eine Auswahlansicht, in der Sie weitere Stellen für dasselbe Objekt wählen.

Der Hauptzweck eines zusätzlichen Orts ist ein zusätzlicher **Link** auf das Objekt: Ein Artikel steht im Archivbaum
und erscheint auch im Zweig der Startseite. Wenn an dieser Stelle die Vollansicht eines Objekts angefordert wird, wird
das Objekt gezeigt, mit seiner eigenen Vollansicht.

Zwei Dinge aus dem Buch, die Sie sich merken sollten:

- **Unterelemente werden nicht vererbt.** Erhält ein Objekt mit Unterelementen einen weiteren Ort, erscheinen die
  Unterelemente dort nicht. Ein Template, das sie zeigen muss, sucht den Hauptknoten und ruft dessen Unterelemente ab
  (siehe [fetch für Builder](../erweiterungsentwicklung/teil-2-builder/fetch-fuer-builder.md)).
- Die Sektion eines neuen Orts ist die seines Elternknotens; das Objekt behält die Sektion seines Hauptorts.

## Informationssammler

Einige Attribute können in der Klasse als **Informationssammler** markiert werden. Ein Objekt einer solchen Klasse wird
dann als Formular gezeigt: Die Daten, die ein Besucher sendet, werden gespeichert (oder an eine festgelegte Adresse
gemailt), statt das Objekt zu ändern. Verwenden Sie das für kleine Umfragen, Kontaktformulare und Abstimmungen. Für
alles, was der Besucher veröffentlichen soll, legen Sie stattdessen ein normales Objekt an einer Stelle an, die die
Rolle erlaubt. Die gesammelten Daten stehen in der Administration, siehe
[Gesammelte Informationen](../../anleitungen/gesammelte-informationen.md) und [Formulareinsendungen ansehen](../handbuch/wie-sehe-ich-formulareinsendungen.md).

Weiter: [Versionen, Übersetzungen und verwandte Inhalte](versionen-uebersetzungen-und-verwandte-inhalte.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
