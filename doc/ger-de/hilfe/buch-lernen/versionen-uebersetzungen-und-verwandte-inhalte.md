---
title: "Versionen, Übersetzungen und verwandte Inhalte"
audience: editor, admin, builder
category: books
order: 290
summary: Zu einer älteren Version zurückkehren, eine Übersetzung hinzufügen und die beiden Wege, Objekte zu verknüpfen (die allgemeine Liste verknüpfter Objekte und die Beziehungs-Datentypen), mit den Einstellungen, die festlegen, wo das Durchsuchen beginnt.
keywords: Version, Entwurf, Version kopieren, Übersetzung, übersetzen, verknüpfte Objekte, Beziehung, Buch Learning, version, draft, copy version, translation, translate, related objects, relation, ezobjectrelation, ezobjectrelationlist, browse.ini, AddRelatedObject, Learning book
type: help
---
# Versionen, Übersetzungen und verwandte Inhalte

## Versionen

Der Versionsteil des Bearbeitungsbildschirms erlaubt Ihnen zweierlei: mit dem Entwurf weiterzumachen, den Sie angelegt
haben, oder eine ältere Version zu wählen und sie **in einen neuen Entwurf zu kopieren** und diesen dann zu
veröffentlichen. Nichts wird überschrieben; die Historie bleibt innerhalb der Grenze aus
[Klassen, Attribute und Objekte](klassen-attribute-und-objekte.md). Die Administration hat außerdem eine Ansicht
**Versionen** mit Gegenüberstellung und einer Liste Ihrer Entwürfe: [Inhaltshistorie](../../anleitungen/inhaltsverlauf.md),
[Entwürfe finden und abschließen](../handbuch/wie-finde-und-beende-ich-meine-entwuerfe.md).

## Übersetzungen

Sprachen werden unter **Setup > Sprachen** hinzugefügt (falls Sie sie nicht bei der Installation gewählt haben; siehe
[Inhaltssprachen](../../anleitungen/inhaltssprachen.md)). Um ein Objekt zu übersetzen, bearbeiten Sie es, verwenden Sie
**Verwalten** im Übersetzungsteil des Bearbeitungsbildschirms, wählen Sie die Sprachen und fügen Sie sie hinzu. Der
Übersetzungsbildschirm zeigt den Originaltext neben dem neuen Text, um den Übersetzer zu leiten. In Exponential ist
das außerdem in [Inhalte übersetzen](../handbuch/wie-uebersetze-ich-inhalte.md) und
[Finden, was nicht übersetzt ist](../handbuch/wie-finde-ich-was-noch-nicht-uebersetzt-ist.md) beschrieben.

## Verwandte Inhalte

Objekte lassen sich auf zwei Arten verknüpfen.

1. **Die allgemeine Beziehungsliste.** Das Feld **Verknüpfte Objekte** des Bearbeitungsbildschirms enthält beliebig
   viele Beziehungen zu beliebigen Objekten, hinzugefügt durch Durchsuchen des Baums. Sie gehört zum Objekt, nicht zu
   einem Attribut.
2. **Die Beziehungs-Datentypen**, `ezobjectrelation` (ein Objekt) und `ezobjectrelationlist` (viele). Der Listentyp ist
   leistungsfähiger: Sie können mehrere haben, sodass verwandte Objekte eingeteilt werden („Autoren“, „siehe auch“); Sie
   können die Klassen einschränken, die verknüpft werden dürfen; und Sie können aus dem Attribut heraus neue Objekte
   anlegen.

Wo das Durchsuchen beginnt, wird in `browse.ini` festgelegt. Für die allgemeine Beziehungsliste ist der Abschnitt
`[AddRelatedObject]` (`StartNode=content`, `SelectionType=multiple`, `ReturnType=ObjectID`). Für die Datentypen gibt
`content.ini [ObjectRelationDataTypeSettings] ClassAttributeStartNode[]` einen Startknoten je Klassenattribut an.

Von der anderen Seite her stehen die **umgekehrt verknüpften** Objekte (was auf dieses Objekt zeigt) Templates über die
Fetch-Funktion `reverse_related_objects` zur Verfügung; siehe [Fetch-Funktionen](../template-operatoren/wie-fetch-funktionen-arbeiten.md).

Weiter: [Workflows und Trigger](workflows-und-trigger.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
