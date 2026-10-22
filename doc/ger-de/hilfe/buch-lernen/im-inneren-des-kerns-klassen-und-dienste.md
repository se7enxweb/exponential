---
title: "Im Inneren des Kerns: Inhaltsklassen, Workflows, Benachrichtigungen, Informationssammlung, Suche"
audience: developer
category: books
order: 510
summary: Eine Übersicht der Kernel-Klassen hinter den Konzepten der früheren Kapitel: Inhaltsklassen und -objekte, Workflows und Trigger, Benachrichtigungen, Informationssammlung und Suche, mit dem Ort, an dem jede heute liegt.
keywords: Kernel-Klassen, Inhaltsklasse, Inhaltsobjekt, Workflow, Benachrichtigung, Informationssammlung, Suche, Buch Learning, eZContentClass, eZContentObject, eZContentObjectAttribute, eZContentObjectVersion, eZContentObjectTreeNode, eZWorkflow, eZWorkflowEvent, notification, eZInformationCollection, eZSearch, kernel/classes, Learning book
type: help
---
# Im Inneren des Kerns: Inhaltsklassen, Workflows, Benachrichtigungen, Informationssammlung, Suche

Die zweite Hälfte von Kapitel 4 des Buchs zeigt, wie die Konzepte auf Kernel-Klassen abgebildet werden. Die Namen sind
immer noch dieselben.

| Konzept | Kernel-Klassen (in `kernel/classes`) | Hinweise |
|---|---|---|
| [Inhaltsklasse](eine-inhaltsklasse-anlegen.md) | `eZContentClass`, `eZContentClassAttribute` | Eine Klasse hat Attribute; jedes Attribut hat eine Datentyp-Zeichenkette und die Datenfelder (`data_int1`..`data_text5`), die der Datentyp für seine Einstellungen verwendet. |
| [Inhaltsobjekt](klassen-attribute-und-objekte.md) | `eZContentObject`, `eZContentObjectVersion`, `eZContentObjectAttribute` | Ein Objekt hat Versionen; jede Version hat Attribute; der Wert des Attributs wird von seinem Datentyp gespeichert. |
| [Knotenbaum](der-knotenbaum-und-die-sektionen.md) | `eZContentObjectTreeNode` | Ein Knoten; zugleich die Stelle, an der alle Abrufe im Baum geschrieben sind. |
| [Workflows und Trigger](workflows-und-trigger.md) | `eZWorkflow`, `eZWorkflowEvent`, `eZWorkflowProcess`, Workflow-Ereignistypen in `kernel/classes/workflowtypes` | Eine Operation (etwa das Veröffentlichen) fragt das Workflow-System, ob ein Trigger einen Workflow hat; der Prozess bewahrt seinen Zustand, bis er fertig ist. |
| Benachrichtigungen | `kernel/classes/notification` | Ereignisse werden gesammelt, Handler machen daraus E-Mails, ein Cronjob versendet sie ([Benachrichtigungen für Entwickler](../../anleitungen/benachrichtigungen-entwickler.md)). |
| Informationssammlung | `eZInformationCollection` und seine Attribute | Die Daten, die ein Besucher an ein Objekt mit [Informationssammler](objekte-anlegen-und-bearbeiten.md) sendet. |
| Suche | `eZSearch` und die Engine in `kernel/search/plugins` (standardmäßig `ezsearchengine`) | Wörter werden beim Veröffentlichen eines Objekts indexiert; andere Engines (Solr über `ezfind`) werden auf dieselbe Weise eingebunden. |

## Ein Objekt im Code anlegen

Das Buch baut ein Objekt von Hand aus einer Klasse, einer Version und Attributen. So arbeitet das Modell im Inneren, und
die Klassen oben sind die Bestandteile: `eZContentClass::fetchByIdentifier()` liefert die Klasse, `instantiate()` erzeugt
das Objekt mit seinem ersten Entwurf, die Attribute enthalten die Werte, und das Veröffentlichen läuft über die
Inhaltsoperation, damit Workflows laufen. Diese Schritte schreiben Sie heute selten selbst.
`eZContentFunctions::createAndPublishObject()` erledigt sie alle in einem Aufruf, und `updateAndPublishObject()` ändert
ein Objekt auf dieselbe Weise. Die Seite
[Inhalte im Code anlegen und aktualisieren](../ersteller/anwendungsentwickler/inhalte-im-code-anlegen.md) enthält die getesteten
Beispiele, das Werteformat für jeden Datentyp und idempotente Importe. Wie Sie ein Skript ausführen, steht in
[CLI-Skripte und Konsolenbefehle](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/cli-skripte-und-console-befehle.md).

## Informationssammlung: die Einstellungen

Der Informationssammler wird in `collect.ini` eingerichtet. Drei Typen werden mitgeliefert (`poll`, `form`, `feedback`),
standardmäßig mit `[InfoSettings] Type=form` gewählt oder je Objekt durch ein Attribut (`TypeAttribute=collection_type`).
Die wichtigsten Einstellungen, jede mit einer Form `...List[<type>]` für einen einzelnen Typ und einer Form
`...Attribute`, bei der ein Attribut des Objekts entscheidet:

| Einstellung | Bedeutung |
|---|---|
| `[CollectionSettings] CollectAnonymousData` | ob nicht angemeldete Besucher senden dürfen |
| `[CollectionSettings] CollectionUserData` | `multiple` (viele Einsendungen je Benutzer), `unique` (eine; eine zweite erhält eine Warnung), `overwrite` (eine; eine neue ersetzt die alte) |
| `[EmailSettings] SendEmail` | ob ein gesammelter Eintrag auch per E-Mail verschickt wird |
| `[DisplaySettings] Display`, `RedirectURL` | was der Besucher nach dem Senden sieht und wohin er weitergeleitet wird |

Ein angemeldeter Benutzer wird über seine Benutzer-ID verfolgt, ein anonymer über die entfernte IP-Adresse. Mit den
mitgelieferten Werten erlaubt `poll` anonyme Daten und eine Einsendung je Benutzer. Die gesammelten Daten lesen Sie in
der Administration ([Gesammelte Informationen](../../anleitungen/gesammelte-informationen.md)).

Weiter: [Erweitern: warum, wo und wie](erweitern-warum-wo-wie.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 4, „A Glimpse Inside the Core“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: gekürzt; PHP-4-Code durch aktuelle Aufrufe ersetzt; Klassennamen, Einstellungen und Konstanten gegen Exponential 6 geprüft.
