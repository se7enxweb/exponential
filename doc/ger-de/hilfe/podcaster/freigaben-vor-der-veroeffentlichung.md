---
title: Freigaben vor der Veröffentlichung
audience: podcaster
category: podcasting-streaming
order: 30
summary: Wie eine Sendung Episoden auf die Prüfung eines Redakteurs oder der Rechtsabteilung warten lässt, bevor sie erscheinen - das Workflow-Ereignis Freigeben und seine Felder, Freigabe kombiniert mit einer geplanten Veröffentlichung, was Produzenten und Freigebende sehen, und leichtere Alternativen.
keywords: Freigabe, freigeben, Prüfung, Abnahme, rechtliche Prüfung, Chefredaktion, Workflow, ezapprove, ausstehend, Zusammenarbeit, Trigger, Multiplexer, geplante Veröffentlichung, approval, approve, review, sign-off, legal review, editor in chief, pending, collaboration, trigger, scheduled release
type: help
---
# Freigaben vor der Veröffentlichung

Diese Seite ist für Sendungen, bei denen jemand eine Episode abnehmen muss, bevor die Hörer sie erhalten: eine
Chefredaktion, eine rechtliche Prüfung bei einem heiklen Interview, eine Prüfung durch den Sponsor. Exponential tut das
mit dem Workflow-Ereignis **Freigeben** (`kernel/classes/workflowtypes/event/ezapprove/`), eingerichtet von einem
Administrator.

## Inhalt

1. [Wie Freigabe funktioniert](#wie-freigabe-funktioniert)
2. [Für eine Sendung einrichten](#für-eine-sendung-einrichten)
3. [Freigabe und Veröffentlichungsdatum zusammen](#freigabe-und-veröffentlichungsdatum-zusammen)
4. [Was Produzenten und Freigebende tun](#was-produzenten-und-freigebende-tun)
5. [Was die Freigabe nicht abdeckt](#was-die-freigabe-nicht-abdeckt)
6. [Leichtere Alternativen](#leichtere-alternativen)

## Wie Freigabe funktioniert

Wenn ein Produzent **Zur Veröffentlichung schicken** drückt, läuft der Workflow am Trigger "vor dem Veröffentlichen von
Inhalten". Das Ereignis Freigeben hält die Version als **ausstehend**, bis ein Freigebender sie annimmt; dann wird sie
veröffentlicht. Lehnt der Freigebende sie ab, wird sie wieder zu einem Entwurf für den Autor. Damit das funktioniert,
muss der Workflow-Cronjob laufen
([Workflows](../../anleitungen/workflows.md#3-durchgerechnetes-beispiel-freigabe-vor-dem-veröffentlichen)).

## Für eine Sendung einrichten

Die Felder des Ereignisses, aus der Anleitung zu Workflows:

| Feld | Für eine Sendung |
|---|---|
| **Betroffene Sektionen** | die Sektion, in der die Inhalte Ihrer Sendung liegen; geben Sie Sendungen eine eigene Sektion, wenn nur sie eine Freigabe brauchen |
| **Betroffene Sprachen** | meist alle |
| **Betroffene Versionen** | *Alle Versionen*, um jede Änderung zu prüfen, oder *Neues Objekt publizieren*, um nur neue Episoden zu prüfen |
| **Benutzer, die den Inhalt bewilligen**, **Gruppen, die Content freigeben** | die Chefredaktion oder eine Gruppe von Prüfern |
| **Excluded user groups** (ausgenommene Benutzergruppen) | Personen, die ohne Prüfung veröffentlichen dürfen |

Ein Freigebender muss seine eigenen Inhalte nie freigeben: Ist der veröffentlichende Benutzer einer der Freigebenden,
lässt das Ereignis die Version durch.

## Freigabe und Veröffentlichungsdatum zusammen

Ein Trigger nimmt **einen** Workflow auf ([Workflows](../../anleitungen/workflows.md#33-mit-einem-trigger-verbinden)). Um
sowohl eine Freigabe als auch eine geplante Veröffentlichung zu haben, setzen Sie beide Ereignisse in einen Workflow,
in der gewünschten Reihenfolge:

1. **Freigeben**: Die Episode wartet auf den Freigebenden.
2. **Bis Datum warten**: Nach der Freigabe wartet sie auf ihr Veröffentlichungsdatum ([Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md)).

In dieser Reihenfolge erscheint eine Episode, die nach ihrem Veröffentlichungsdatum freigegeben wird, beim nächsten
Cron-Lauf. Für getrennte Workflows beschreibt die Anleitung das Ereignis *Multiplexer*.

## Was Produzenten und Freigebende tun

**Produzent**

1. Stellen Sie die Episode fertig und drücken Sie **Zur Veröffentlichung schicken**.
2. Die Version steht in **Meine Warteliste** ([Meine Warteliste](../veroeffentlichen-und-versionen.md#meine-warteliste)).
3. Wird sie abgelehnt, ist die Version wieder ein Entwurf für Sie, mit dem Kommentar des Freigebenden. Bearbeiten Sie
   sie und schicken Sie sie erneut.

**Freigebender**

1. Öffnen Sie **Mein Konto > Zusammenarbeit** (oder **Meine Warteliste**): Dort stehen die Freigabeanfragen.
2. Öffnen Sie die Anfrage, sehen Sie sich die Vorschau an, hören Sie das Audio und lesen Sie die Notizen.
3. Drücken Sie **Freigeben**, oder **Ablehnen** mit einem Kommentar für den Autor.

Nach einer Freigabe wird die Episode beim nächsten Lauf des Workflow-Cronjobs veröffentlicht
([Workflows](../../anleitungen/workflows.md#35-ausprobieren)).

Der Kundenbereich beschreibt eine tägliche Routine für Freigebende
([Freigabe und Prüfung](../kunden/alltag/freigabe-und-pruefung.md#eine-tägliche-routine-für-freigebende-personen)).

## Was die Freigabe nicht abdeckt

- **Änderungen außerhalb der Inhalte.** Die Kanalfelder des Feeds, die Adresse des Titelbilds und Layouts sind keine
  Inhaltsversionen; die Freigabe hält sie nicht auf. Beschränken Sie stattdessen, wer RSS-Exporte bearbeiten darf (die
  Richtlinie `rss/edit`).
- **Eingebettete Player.** Eine freigegebene Seite mit einem Block External video zeigt, was der Dienst zum jeweiligen
  Zeitpunkt ausliefert.
- **Verbergen und Entfernen.** Das ist kein Veröffentlichen; das Ereignis Freigeben hält es nicht auf.

## Leichtere Alternativen

| Bedarf | Alternative |
|---|---|
| Ein zweites Paar Augen, keine harte Schranke | der Produzent speichert den Entwurf und bittet eine Kollegin, ihn vor dem Veröffentlichen zu prüfen |
| Sichtbare Phasen ohne Blockieren | Objektzustände wie *bereit zur Prüfung* ([Produktionsphasen mit Objektzuständen](produktionsphasen-mit-objektzustaenden.md)) |
| Nur bestimmte Personen dürfen die Sendung überhaupt ändern | Rollen, die `content/create` und `content/edit` für ihr Team auf den Teilbaum oder die Sektion der Sendung beschränken ([Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)) |

## Verwandte Seiten

- [Freigabe und Prüfung](../kunden/alltag/freigabe-und-pruefung.md)
- [Workflows](../../anleitungen/workflows.md)
- [Ein Veröffentlichungskalender](ein-veroeffentlichungskalender.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
