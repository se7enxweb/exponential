---
title: Produktionsphasen mit Objektzuständen
audience: podcaster
category: podcasting-streaming
order: 31
summary: Wie eine Sendung Episoden mit den Objektzuständen von Exponential durch Produktionsphasen wie aufgenommen, geschnitten, Notizen geschrieben und fertig verfolgt - eine eigene Zustandsgruppe, Richtlinien, die von der Phase abhängen, unfertige Episoden von der Website fernhalten und die Prüfungen, dass der Feed sauber bleibt.
keywords: Objektzustände, Produktionsphasen, Workflow-Phasen, aufgenommen, geschnitten, fertig, Prüfung, Zustandsgruppe, state/assign, Einschränkung StateGroup, NewState, Rollen, Status der Episode, object states, production stages, workflow stages, recorded, edited, ready, review, state group, StateGroup limitation, roles, episode status
type: help
---
# Produktionsphasen mit Objektzuständen

Diese Seite ist für Produzenten und Administratoren von Sendungen mit einem Team: Eine Moderatorin nimmt auf, ein
Cutter schneidet, jemand schreibt die Notizen, jemand nimmt ab. **Objektzustände** lassen jede Episode ihre Phase
tragen, und Rollen können davon abhängen. Die vollständige Referenz ist [Objektzustände](../../anleitungen/objektzustaende.md);
diese Seite wendet sie auf eine Sendung an.

## Inhalt

1. [Was Zustände tun](#was-zustände-tun)
2. [Eine Zustandsgruppe für eine Sendung](#eine-zustandsgruppe-für-eine-sendung)
3. [Rollen nach Phase](#rollen-nach-phase)
4. [Die Phase setzen](#die-phase-setzen)
5. [Unfertige Episoden von der Website fernhalten](#unfertige-episoden-von-der-website-fernhalten)
6. [Zustände oder Freigabe?](#zustände-oder-freigabe)

## Was Zustände tun

Ein Objektzustand ist eine Markierung, die jedes Inhaltsobjekt trägt, eine aus jeder Zustandsgruppe. Zustände ändern
von sich aus nichts; Rollen machen sie wirksam. Eine Richtlinie, die durch `StateGroup_<group>` eingeschränkt ist, gilt
nur für Objekte in den gewählten Zuständen, und die Einschränkung `NewState` von `state/assign` legt fest, welche
Zustände ein Benutzer setzen darf ([Objektzustände](../../anleitungen/objektzustaende.md#8-zustände-in-rollen-und-richtlinien)).
Gruppen und Zustände werden unter **Setup > Zustände** verwaltet.

## Eine Zustandsgruppe für eine Sendung

Beispielgruppe `production` mit ihren Zuständen in Reihenfolge (der erste ist der Standard für neue Objekte):

| Zustand | Bedeutung |
|---|---|
| `planned` | das Episodenobjekt existiert als Platzhalter |
| `recorded` | das Rohaudio ist da |
| `edited` | das fertige Audio ist hochgeladen |
| `notes` | Shownotes, Gäste und Transkript sind fertig |
| `ready` | zur Veröffentlichung freigegeben |

Den ersten Zustand, den Sie in einer neuen Gruppe anlegen, erhält jedes vorhandene Objekt. Hat die Website bereits
veröffentlichte Inhalte, setzen Sie diese zuerst auf den letzten Zustand, wie es das durchgearbeitete Beispiel der
Anleitung zu Objektzuständen tut
([Ein durchgearbeitetes Beispiel](../../anleitungen/objektzustaende.md#9-ein-durchgearbeitetes-beispiel-ein-prüf-workflow)).

## Rollen nach Phase

| Rolle | Richtlinien (Beispiele) |
|---|---|
| Moderation | `content/edit` mit `StateGroup_production` = planned, recorded; `state/assign` mit `NewState` = production/recorded |
| Audioschnitt | `content/edit` mit `StateGroup_production` = recorded, edited; `state/assign` mit `NewState` = production/edited |
| Notizen | `content/edit` mit `StateGroup_production` = edited, notes; `state/assign` mit `NewState` = production/notes |
| Produktion | `content/edit` ohne Einschränkung nach Zustand; `state/assign` mit `NewState` = production/ready und zurück zu jedem früheren Zustand |

Kombinieren Sie diese mit einer Einschränkung **Teilbaum** auf den Ordner der Sendung, damit eine Rolle für eine
Sendung keine andere berühren kann ([Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)).

## Die Phase setzen

Öffnen Sie bei einer Episode den Reiter **Details** und setzen Sie den **Zustand des Objekts**. Eine ganze Staffel
lässt sich auf einmal mit "Auch für alles unterhalb dieses Knotens" setzen
([Zustände an Inhalten setzen](../../anleitungen/objektzustaende.md#7-zustände-an-inhalten-setzen)).

## Unfertige Episoden von der Website fernhalten

Zustände allein machen keine Veröffentlichung rückgängig. Drei Wege halten unfertige Episoden von Hörern fern:

1. **Veröffentlichen Sie sie nicht.** Arbeiten Sie in Entwürfen, bis die Episode fertig ist; das ist am einfachsten.
2. **Schränken Sie `content/read` der Rolle Anonymous** mit `StateGroup_production = ready` ein: Besucher sehen nur
   fertige Episoden; alles andere ist für sie unsichtbar und erscheint nicht in Fetches.
3. **Verwenden Sie eine Freigabe** für den letzten Schritt ([Freigaben vor der Veröffentlichung](freigaben-vor-der-veroeffentlichung.md)).

Wenn Sie den zweiten Weg wählen, öffnen Sie nach jeder Änderung den Podcast-Feed abgemeldet und prüfen, ob nur fertige
Episoden aufgeführt sind, bevor Sie sich darauf verlassen.

## Zustände oder Freigabe?

| | Objektzustände | Freigabe-Workflow |
|---|---|---|
| Zeigt die Phase auf einen Blick | ja | nur "ausstehend" |
| Blockiert das Veröffentlichen | nur über Richtlinien | ja |
| Mehrere Schritte | ja, so viele Zustände, wie Sie brauchen | eine Freigabe pro Ereignis |
| Braucht den Workflow-Cronjob | nein | ja |

Viele Sendungen verwenden beides: Zustände für die Schritte der Produktion, Freigabe für die abschließende Abnahme.

## Verwandte Seiten

- [Objektzustände](../../anleitungen/objektzustaende.md)
- [Ein Veröffentlichungskalender](ein-veroeffentlichungskalender.md)
- [Rollen für Ihr Team](../kunden/alltag/rollen-fuer-ihr-team.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
