---
title: Entwürfe und ausstehende Elemente
audience: government, editor, admin
category: public-sector
order: 50
summary: Wie Redaktionen und Webverantwortliche mit Meine Entwürfe und Meine ausstehenden Elemente den Überblick über unfertige und wartende Arbeit behalten, alte Entwürfe aufräumen und herausfinden, was eine Version zurückhält - mit den Regeln, die die Entwürfe einer Person privat halten.
keywords: Entwürfe, Meine Entwürfe, ausstehend, Meine ausstehenden Elemente, wartet auf Freigabe, alte Entwürfe, alte Entwürfe entfernen, unveröffentlichte Arbeit, Version, redaktioneller Rückstand, drafts, my drafts, pending, my pending items, waiting for approval, old drafts, remove old drafts, unpublished work, version, editorial backlog
type: help
---
# Entwürfe und ausstehende Elemente

Diese Seite richtet sich an Redaktionen und Webverantwortliche. Zwei Seiten des Dashboards der Administration zeigen unfertige und wartende Arbeit:
**Meine Entwürfe** (`/content/draft`) und **Meine ausstehenden Elemente** (`/content/pendinglist`). Die Fakten stammen aus
[Meine Entwürfe und meine ausstehenden Elemente](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md).

## Inhalt
1. [Meine Entwürfe](#meine-entwürfe)
2. [Meine ausstehenden Elemente](#meine-ausstehenden-elemente)
3. [Alte Entwürfe aufräumen](#alte-entwürfe-aufräumen)
4. [Den Rückstand des ganzen Teams sehen](#den-rückstand-des-ganzen-teams-sehen)
5. [Probleme](#probleme)
6. [Verwandte Seiten](#verwandte-seiten)

## Meine Entwürfe

- **Ihre Entwürfe gehören Ihnen.** Nur Sie sehen sie auf dieser Seite, und die Seite entfernt nur Ihre eigenen Entwürfe.
- Die Kennzahlen zählen Ihre Entwürfe, die zu neuen Objekten, die seit 30 Tagen (oder dem gewählten Alter) nicht geänderten, Übersetzungen und
  Klassen. Suchen Sie nach Name, Platzierung und Klasse; filtern Sie nach Übersetzung, Klasse und Alter.
- Jede Karte hat **Bearbeiten**, **Ansehen** (mit `content/versionread`) und **Entfernen**.
- Ein Entwurf eines **neuen Objekts** nimmt beim Entfernen das neue Objekt mit; ein Entwurf eines veröffentlichten Objekts lässt die
  veröffentlichte Version unverändert.

## Meine ausstehenden Elemente

- Kennzahlen: von Ihnen geschickt, wartet auf Ihre Freigabe, von einer Freigabe zurückgehalten.
- Filter: alle, von Ihnen geschickt, wartet auf Ihre Freigabe; Klasse; Reihenfolge (neueste, am längsten wartend, Name).
- Jede Karte sagt, **was die Version zurückhält** (die Freigabe, ihr Stand und ihre Freigebenden, die Workflows), und bietet **Freigabe öffnen**
  oder **Freigabe und Kommentare**, wenn Sie beteiligt sind, sowie **Veröffentlichte Seite**.
- Eine ausstehende Version, die keine Freigabe und kein Workflow mehr zurückhält, sagt das: Ein Administrator sieht dann unter
  **Setup > Workflow-Prozesse** nach.

## Alte Entwürfe aufräumen

**Alte Entwürfe entfernen** entfernt Ihre Entwürfe, die seit 7, 30, 90 oder 365 Tagen nicht geändert wurden, nach einer Bestätigung, die die
Anzahl nennt. Öffentliche Stellen vereinbaren oft eine Regel wie „Entwürfe, die älter als 90 Tage sind, werden am Ende jedes Quartals entfernt“;
jede Redakteurin und jeder Redakteur wendet sie auf der eigenen Seite an. Auch die Versionsgrenze von Exponential gilt:
standardmäßig `content.ini [VersionManagement] DefaultVersionHistoryLimit=45` Versionen je Objekt; ist sie erreicht, macht die älteste
archivierte Version Platz ([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md#3-ein-neuer-entwurf-aus-einer-alten-version)).

## Den Rückstand des ganzen Teams sehen

Meine Entwürfe ist bewusst persönlich, daher gibt es keine Seite, die die Entwürfe aller Redaktionsmitglieder auflistet. Was Webverantwortliche
sehen können:

| Bedarf | Wo |
|---|---|
| Alles, was auf Freigabe wartet | **Setup > Workflow-Prozesse**, Filter *Wartend*; jede Karte nennt den Inhalt, die Redakteurin oder den Redakteur und den Schritt |
| Wie lange Dinge warten | die Übersichtskennzahl **Ältester wartender** und die gelbe Markierung an Prozessen, die seit mehr als einem Tag unverändert sind |
| Entwürfe einer ausgetretenen Person | fragen Sie die Person vor ihrem Austritt; ein Administrator kann die Versionsseite eines Objekts öffnen, um seine Entwürfe zu sehen ([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)) |

## Probleme

| Sie sehen | Warum | Tun Sie |
|---|---|---|
| Ein erwarteter Entwurf fehlt | gefiltert, älter als der Altersfilter oder der einer anderen Person | setzen Sie die Filter zurück (sie werden in der Adresse gespeichert) |
| Eine Version, die auf Ihre Freigabe wartet, wird nicht aufgeführt | Sie dürfen sie nicht lesen | beantragen Sie `content/versionread` für diesen Bereich |
| „Zur Veröffentlichung schicken“ schien einmal nichts zu bewirken | ein fehlgeschlagener Prozess mit demselben Schlüssel wurde zuerst entfernt | siehe [Fehlerbehebung bei Workflows](workflow-fehlerbehebung.md) |

## Verwandte Seiten
- [Posteingang der Zusammenarbeit](posteingang-der-zusammenarbeit.md)
- [Versionsgeschichte und Rechenschaft](versionsverlauf-und-nachvollziehbarkeit.md)
- [Überblick über Freigabe-Workflows](freigabe-workflows-im-ueberblick.md)
- [Meine Entwürfe und meine ausstehenden Elemente (Leitfaden)](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
