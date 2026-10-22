---
title: "Workflows und Trigger"
audience: admin, builder, developer
category: books
order: 300
summary: Was ein Workflow, ein Ereignis, ein Trigger und der Multiplexer sind, welche Ereignistypen heute mitgeliefert werden und wo Sie einen ersten Freigabe-Workflow bauen.
keywords: Workflow, Workflow-Ereignis, Trigger, Auslöser, Multiplexer, Freigabe, bis Datum warten, Veröffentlichen, Cronjob, Buch Learning, workflow, workflow event, trigger, multiplexer, approve, wait until date, content publish, cronjob, Learning book
type: help
---
# Workflows und Trigger

Ein **Workflow** erledigt etwas im Hintergrund, ohne Benutzer, oder bittet einen Benutzer, etwas zu tun, etwa ein Objekt
freizugeben, bevor es veröffentlicht wird. Das System liefert die Bausteine für Geschäftsabläufe; Sie setzen sie in der
Administration zusammen, und ein Entwickler kann in PHP eigene Ereignistypen hinzufügen.

## Trigger

Ein **Trigger** startet die Kette: Er ist ein Punkt in einer Kernel-Operation, „vor dem Veröffentlichen“, „nach dem
Veröffentlichen“, und einige Shop-Schritte (etwa die Kasse). Für jeden Trigger wählen Sie den Workflow, der laufen soll.
Die Liste steht unter **Setup > Trigger** (im Menü des Buchs der Teil **Fortgeschritten**).

Das Buch weist darauf hin, dass ein Trigger einen einzigen Workflow aufnimmt, Workflows aber parallel laufen können.
Das Ereignis **Multiplexer** ist ein Verteiler: Es startet andere Workflows nach Inhaltsklasse und Sektion und hat eine
Ausschlussliste von Benutzern und Gruppen. Wieder einmal die Sektionen.

## Ereignisse

Ein Workflow ist eine Liste von **Ereignissen**. Das Buch nannte vier Ereignistypen (Multiplexer, Freigabe, bis Datum
warten und einen weiteren). Die Liste der Installation steht heute in `settings/workflow.ini` (`AvailableEventTypes[]`)
und enthält:

| Ereignistyp | Was er tut |
|---|---|
| Freigeben (`event_ezapprove`) | Hält das Objekt zurück, bis ein Benutzer es freigibt, mit Benachrichtigung an die Freigebenden. |
| Bis Datum warten (`event_ezwaituntildate`) | Hält das Objekt zurück, bis ein Datumsattribut erreicht ist. |
| Multiplexer (`event_ezmultiplexer`) | Führt einen gewählten Workflow nach Klasse, Sektion und Benutzer aus. |
| Einfacher Versand, Zahlungsgateway | Schritte der Shop-Kasse. |
| Finish user register (`event_ezfinishuserregister`) | Schließt die Registrierung eines Benutzers ab. |

Erweiterungen fügen weitere hinzu (sehen Sie in die Liste im Workflow-Editor).

## Cronjob und Prozesse

Ein Workflow, der wartet, ist ein **Prozess**; der Cronjob, der Workflows bearbeitet, greift ihn auf, wenn das
Erwartete eintritt. Läuft der Cron nicht, bleibt das Objekt ausstehend. Deshalb stellt das Buch den Cronjob in Kapitel 1
([Installieren, damals und heute](installieren-damals-und-heute.md)). Ausstehende Objekte erscheinen auf der Seite
[Ausstehende Einträge](../handbuch/wo-sehe-ich-meine-wartenden-elemente.md).

## Wo Sie es tun

[Workflows: Freigaben, Trigger und die Prozesse, die warten](../../anleitungen/workflows.md) ist das ausgearbeitete Beispiel
(den Workflow anlegen, das Ereignis Freigeben einrichten, den Trigger verbinden, sicherstellen, dass der Cronjob läuft,
ausprobieren). [Workflows für Builder](../erweiterungsentwicklung/teil-2-builder/workflows-fuer-builder.md) ist die
Kurzfassung.

Weiter: [Weitere Funktionen der Administration](weitere-funktionen-der-administration.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
