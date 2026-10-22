---
title: "Was ist Content Management?"
audience: user, editor, developer, builder
category: books
order: 200
summary: Was ein Content-Management-System tut (sammeln, verwalten, veröffentlichen), was das in Exponential bedeutet und der Planungsschritt, der vor jedem Klick kommt: die Struktur planen.
keywords: Content Management, CMS, Inhalte, Bestandteile, Versionen, Workflow, Struktur, Taxonomie, Planung, Buch Learning, content management, content, components, versions, workflow, structure, taxonomy, planning, Learning book
type: help
---
# Was ist Content Management?

Content Management heißt, Informationen so zu ordnen, einzuteilen und zu strukturieren, dass sie gespeichert,
veröffentlicht und auf mehrere Arten wiederverwendet werden können. Ein **Content-Management-System** (CMS) sammelt,
verwaltet und veröffentlicht Inhalte. Das Buch beschreibt vier Dinge, die ein CMS tut, und jedes hat in Exponential
seinen Platz:

| Ein CMS sollte | In Exponential |
|---|---|
| Inhalte als Teile (mit Metadaten) oder als ganze Dokumente speichern | Inhalte werden als **Objekte** aus typisierten **Attributen** gespeichert. Ein Dokument, etwa ein PDF, ist ein Dateiattribut eines Objekts. Siehe [Klassen, Attribute und Objekte](klassen-attribute-und-objekte.md). |
| Die Verbindungen zwischen Teilen bewahren | Objekte stehen in Beziehung zueinander, und ein Objekt kann an mehreren Stellen des Baums stehen. Siehe [Der Knotenbaum und die Sektionen](der-knotenbaum-und-die-sektionen.md). |
| Versionen verfolgen | Jedes Objekt ist versioniert. Siehe [Versionen, Übersetzungen und verwandte Inhalte](versionen-uebersetzungen-und-verwandte-inhalte.md). |
| Einen Workflow ausführen: Freigaben, Benachrichtigungen | Workflows laufen bei Ereignissen wie dem Veröffentlichen, und Benachrichtigungen gehen per E-Mail hinaus. Siehe [Workflows und Trigger](workflows-und-trigger.md). |

## Wie die Inhalte hinausgelangen

Das Buch nennt die Protokolle: HTTP für den Browser sowie WebDAV und SOAP für andere Programme. Heute verwenden die
Website und die Administration HTTP, und Programme verwenden die REST-API ([REST-API](../../anleitungen/rest-api-v1-und-v2.md)).
Das Textformat ist weiterhin ein strukturiertes
XML aus Überschriften, Listen, Tabellen, Links und eingebetteten Objekten ([Rich-Text-Tags](rich-text-tags.md)).

## Planen, bevor Sie bauen

Der erste praktische Rat des Buchs ist nicht gealtert: **Planen Sie die Inhalte, bevor Sie sie bauen.** Es gibt keine
festen Regeln, nur bewährte Praktiken von den Leuten, die Taxonomien für Dokumentensammlungen aufbauen. Eine kurze
Liste:

1. Schreiben Sie die Arten von Inhalten auf (Artikel, Produkte, Personen, Veranstaltungen) und was jede braucht. Daraus
   werden Inhaltsklassen ([Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md)).
2. Skizzieren Sie den Baum: was unter was gehört. Die obersten Einträge werden oft das Hauptmenü.
3. Legen Sie fest, wer was tun darf: Gruppen und Rollen ([Rollen und Richtlinien, Schritt für Schritt](rollen-und-richtlinien-schritt-fuer-schritt.md)).
4. Legen Sie die Sektionen fest: Teile der Website, die ein anderes Aussehen oder andere Rechte brauchen.
5. Erst dann legen Sie die erste Klasse an.

Bei einem kleinen Projekt und mit Erfahrung im Rücken erlaubt das Buch, sofort mit einer groben Gliederung in der
Administration anzufangen. Bei einem großen Projekt nehmen Sie sich die Zeit an der Tafel. Die Seiten
[Ihr Inhaltsmodell entwerfen](../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md) und
[Eine Beispielsite planen](eine-beispielsite-planen.md) arbeiten das durch.

Weiter: [Der Knotenbaum und die Sektionen](der-knotenbaum-und-die-sektionen.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
