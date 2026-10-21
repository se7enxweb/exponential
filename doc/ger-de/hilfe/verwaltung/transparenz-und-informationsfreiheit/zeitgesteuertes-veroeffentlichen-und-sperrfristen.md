---
title: Zeitgesteuertes Veröffentlichen und Sperrfristen
audience: government, admin, editor
category: public-sector
order: 170
summary: Wie Sie Transparenzinhalte mit dem Workflow-Ereignis Bis Datum warten zu einem festen Zeitpunkt veröffentlichen, wie Sie Inhalte mit dem Cronjob hide nach einem Datum verbergen, warum der Cronjob unpublish Inhalte entfernt und nicht für Schriftgut verwendet werden sollte, und wie genau die Zeitsteuerung ist.
keywords: zeitgesteuertes Veröffentlichen, Sperrfrist, Embargo, veröffentlichen um, Bis Datum warten, Workflow, hide.php, HideSettings, unpublish.php, UnpublishSettings, Cronjob, Veröffentlichungszeitpunkt, Veröffentlichung von Statistiken, scheduled publishing, embargo, publish at, wait until date, cronjob, release time, statistics release
type: help
---
# Zeitgesteuertes Veröffentlichen und Sperrfristen

Diese Seite richtet sich an Administratoren und Redaktionen, die Informationen zu einem festen Zeitpunkt veröffentlichen
müssen (eine Statistikveröffentlichung, eine Frist für eine Tagesordnung) oder sie nach einem Datum aus der
öffentlichen Ansicht nehmen müssen. Exponential erledigt beides mit Cronjobs: einem Workflow-Ereignis für das
Veröffentlichen und einem Cronjob hide für das Enddatum.

## Inhalt

1. [Zu einem festen Zeitpunkt veröffentlichen](#zu-einem-festen-zeitpunkt-veröffentlichen)
2. [Einrichten](#einrichten)
3. [Wie genau es ist](#wie-genau-es-ist)
4. [Nach einem Datum verbergen](#nach-einem-datum-verbergen)
5. [Verwenden Sie den Cronjob unpublish nicht für Schriftgut](#verwenden-sie-den-cronjob-unpublish-nicht-für-schriftgut)
6. [Entwürfe unter Sperrfrist](#entwürfe-unter-sperrfrist)

## Zu einem festen Zeitpunkt veröffentlichen

Das Workflow-Ereignis *Bis Datum warten* hält eine Version zurück, bis das Datum in einem Datums- oder Datums- und
Zeitfeld des Objekts verstrichen ist (`kernel/classes/workflowtypes/event/ezwaituntildate/`). Die Redaktion klickt auf
**Zur Veröffentlichung schicken**; die Version wartet; der Workflow-Cronjob veröffentlicht sie bei seinem ersten Lauf
nach dem Datum. Das Ereignis kann außerdem das Veröffentlichungsdatum des Objekts auf dieses Datum setzen.

## Einrichten

1. Fügen Sie der Klasse ein Feld **Veröffentlichen um** (Datum und Zeit) hinzu, zum Beispiel Ihrer Sitzungs- oder
   Veröffentlichungsklasse.
2. **Setup > Workflows**: Legen Sie einen Workflow mit dem Ereignis **Bis Datum warten** an. Wählen Sie im Ereignis die
   **Klasse**, klicken Sie auf **Attribute aktualisieren**, wählen Sie das **Attribut** (Veröffentlichen um) und fügen
   Sie die Kombination hinzu; sie erscheint unter **Class/attribute combinations** (Kombinationen aus Klasse und
   Attribut).
3. **Setup > Trigger**: Verbinden Sie den Workflow mit **Vor dem Veröffentlichen von Inhalten**. Wenn bereits ein
   Freigabe-Workflow diesen Trigger nutzt, legen Sie beide mit einem Ereignis **Multiplexer** in einen Workflow, oder
   fügen Sie beide Ereignisse einem Workflow hinzu (zuerst freigeben, dann warten).
4. Stellen Sie sicher, dass die Cronjob-Gruppe `frequent` oft genug läuft (siehe unten).

**Setup > Workflow-Prozesse** listet wartende Versionen mit dem auf, worauf sie warten; **Abbrechen...** stoppt eine
und macht die Version wieder zum Entwurf ([Workflows](../../../anleitungen/workflows.md)).

## Wie genau es ist

Die Veröffentlichung geschieht beim ersten Lauf des Workflow-Cronjobs nach dem Datum. Läuft der Cronjob alle 5
Minuten, erscheint eine Veröffentlichung, die auf 9:30 Uhr gesetzt ist, zwischen 9:30 und etwa 9:35 Uhr, zuzüglich der
Zeit, die die Caches zum Aktualisieren brauchen. Wenn es auf die genaue Minute ankommt, lassen Sie den Cronjob öfter
laufen, testen Sie die Zeitsteuerung vorher und prüfen Sie zum Veröffentlichungszeitpunkt von Hand.

## Nach einem Datum verbergen

Der Cronjob `hide.php` (im globalen Teil) verbirgt Inhalte, deren Datum verstrichen ist: für jede Klasse in
`content.ini [HideSettings] HideDateAttributeList[]` (Klassenbezeichner und Datumsattribut) unterhalb der Knoten aus
`RootNodeList[]`. Er verbirgt so, wie es **Verstecken** in der Administration tut: Der Inhalt bleibt mit seinen
Versionen erhalten und kann wieder angezeigt werden ([Cronjobs](../../../anleitungen/cronjobs.md)).

```ini
# settings/override/content.ini.append.php
[HideSettings]
RootNodeList[]=<node id of the consultations folder>
HideDateAttributeList[consultation]=close_date
```

## Verwenden Sie den Cronjob unpublish nicht für Schriftgut

`unpublish.php` (`content.ini [UnpublishSettings] RootNodeList[]`, `ClassList[]`) sucht nach einem Attribut
`unpublish_date` und **entfernt** den Knoten, wenn das Datum verstrichen ist (`removeThis()` in
`kernel/private/classes/cronjobs/unpublish.php`). Entfernte Inhalte kommen in den Papierkorb oder werden gelöscht, und
ihre Adresse antwortet nicht mehr. Für Transparenzinhalte verwenden Sie das Verbergen oder einen Status, nie diesen
Cronjob.

## Entwürfe unter Sperrfrist

Vor der Veröffentlichung existieren Inhalte nur als Entwurf oder als wartende Version. Ein Entwurf ist für die Person
sichtbar, die ihn angelegt hat; Mitarbeitende mit `content/versionread` können Versionen lesen. Halten Sie Material
unter Sperrfrist in einer Sektion, die nur das Team der Veröffentlichung lesen darf, und laden Sie keine Dateien vorab
in veröffentlichte Objekte hoch: Eine Datei in einem veröffentlichten Objekt kann sofort heruntergeladen werden.

## Verwandte Seiten

- [Workflows](../../../anleitungen/workflows.md)
- [Cronjobs](../../../anleitungen/cronjobs.md)
- [Sitzungsunterlagen, Tagesordnungen und Protokolle](sitzungsunterlagen-tagesordnungen-und-protokolle.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
