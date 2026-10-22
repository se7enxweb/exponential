---
title: Ein Veröffentlichungskalender
audience: podcaster
category: podcasting-streaming
order: 29
summary: Wie eine Sendung ihre Veröffentlichungen mit dem plant und verfolgt, was Exponential bietet - Entwürfe und ausstehende Versionen, zeitgesteuertes Veröffentlichen, Objektzustände als Produktionsphasen, Listen dessen, was kommt - und eine Wochenroutine und Kalendervorlage für ein Team.
keywords: Veröffentlichungskalender, Redaktionskalender, Inhaltskalender, Planung, Produktionsplan, Entwürfe, Warteliste, geplante Veröffentlichung, Objektzustände, Wochenroutine, Team, release calendar, editorial calendar, content calendar, planning, production schedule, drafts, pending items, scheduled release, object states, weekly routine, team
type: help
---
# Ein Veröffentlichungskalender

Diese Seite ist für Produzenten und Teams von Sendungen, die nach Plan veröffentlichen. Exponential hat keine
Kalenderansicht geplanter Episoden; der Kalender lebt in Ihrem Planungswerkzeug oder einem gemeinsamen Dokument. Was
die Website Ihnen gibt, ist der Zustand jeder Episode: Entwurf, wartend, veröffentlicht und, wenn Sie sie einrichten,
Ihre eigenen Produktionsphasen. Diese Seite bringt beides zusammen.

## Inhalt

1. [Was die Website Ihnen sagt](#was-die-website-ihnen-sagt)
2. [Eine Kalendervorlage](#eine-kalendervorlage)
3. [Eine Wochenroutine](#eine-wochenroutine)
4. [Staffeln und Pausen](#staffeln-und-pausen)
5. [Wenn ein Termin rutscht](#wenn-ein-termin-rutscht)

## Was die Website Ihnen sagt

| Frage | Wo Sie nachsehen |
|---|---|
| Woran wird geschrieben? | **Meine Entwürfe** jedes Redakteurs ([Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md#meine-entwürfe)) |
| Was wartet auf Freigabe oder einen Termin? | **Meine Warteliste** und für Administratoren **Setup > Workflow-Prozesse** ([Workflows](../../anleitungen/workflows.md)) |
| In welcher Phase ist jede Episode? | der Objektzustand, wenn Ihre Website Zustände verwendet ([Produktionsphasen mit Objektzuständen](produktionsphasen-mit-objektzustaenden.md)) |
| Was ist zuletzt erschienen? | der Ordner der Sendung, nach Datum der Veröffentlichung sortiert, oder der Feed |

Die allgemeinere Planungspraxis für eine Website steht unter [Inhaltskalender](../kunden/alltag/redaktionskalender.md),
mit einer Vorlage unter [Vorlage für den Inhaltskalender](../kunden/alltag/vorlage-redaktionskalender.md).

## Eine Kalendervorlage

Kopieren Sie dies in Ihr Planungsdokument. Eine Zeile pro Episode:

```text
| Nr. | Arbeitstitel | Gäste  | Aufnahme | Schnitt fertig | Notizen fertig | Veröffentlichung (Datum, Uhrzeit, Zone) | Verantwortlich | Status auf der Website |
|-----|--------------|--------|----------|----------------|----------------|-----------------------------------------|----------------|------------------------|
| 42  | <Titel>      | <Name> | <Datum>  | <Datum>        | <Datum>        | <Datum> 06:00 <Zone>                    | <Name>         | Entwurf / ausstehend / veröffentlicht |
```

Die letzte Spalte wird mit der Website abgeglichen, nicht aus dem Gedächtnis eingetippt.

## Eine Wochenroutine

**Montag**

- Vergleichen Sie den Kalender mit der Website: Jede Episode, die diese Woche fällig ist, existiert, zumindest als
  Entwurf.
- Prüfen Sie, ob die Episoden der letzten Woche im Feed stehen ([Ihren Feed prüfen](ihren-feed-pruefen.md)).

**Zwei Tage vor jeder Veröffentlichung**

- Die Episode ist vollständig: Audio, Shownotes, Gäste, Transkript ([Checklisten für Podcaster](checklisten-fuer-podcaster.md)).
- Sie ist mit ihrem Veröffentlichungsdatum zur Veröffentlichung geschickt und **ausstehend**
  ([Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md)).
- Ist eine Freigabe nötig, weiß der Freigebende Bescheid ([Freigaben vor der Veröffentlichung](freigaben-vor-der-veroeffentlichung.md)).

**Veröffentlichungstag**

- Nach dem ersten Cron-Lauf nach der Veröffentlichungszeit: Die Episode ist abgemeldet auf der Website zu sehen.
- Innerhalb der Cache-Zeit des Feeds: Sie steht im Feed.
- Kündigen Sie sie auf Ihren Kanälen mit der Adresse der Episode an.

**Freitag**

- Entfernen Sie Testobjekte und veraltete Entwürfe.
- Notieren Sie im Kalender, was sich verschoben hat und warum.

## Staffeln und Pausen

- Legen Sie den Ordner der nächsten Staffel vor ihrer ersten Episode an ([Die Sendungsstruktur einrichten](die-sendungsstruktur-einrichten.md)).
- Halten Sie den Feed während einer Pause aktiv; nichts muss sich ändern ([Eine Sendung umziehen, pausieren oder beenden](eine-sendung-umziehen-pausieren-oder-beenden.md)).
- Prüfen Sie am Ende einer Staffel, ob die **Anzahl der Objekte** des Feeds noch das ganze Archiv abdeckt.

## Wenn ein Termin rutscht

- Ist die Episode nicht fertig, schicken Sie sie nicht zur Veröffentlichung; lassen Sie sie als Entwurf.
- Wartet sie bereits mit einem alten Datum und darf nicht erscheinen, bitten Sie den Administrator, den wartenden
  Prozess vor dem Datum abzubrechen ([Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md#ändern-oder-abbrechen)).
- Ist sie zu früh erschienen, nimmt Verbergen sie von der Website und bei der nächsten Aktualisierung des Caches aus dem
  Feed, aber Apps, die sie in der Zwischenzeit abgerufen haben, behalten sie
  ([Episoden verbergen und zurückziehen](episoden-verbergen-und-zurueckziehen.md)).

## Verwandte Seiten

- [Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md)
- [Inhaltskalender](../kunden/alltag/redaktionskalender.md)
- [Checklisten für Podcaster](checklisten-fuer-podcaster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
