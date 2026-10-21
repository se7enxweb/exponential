---
title: Eine Zustandsgruppe für die Prüfung einrichten
audience: government, admin
category: public-sector
order: 300
summary: Schritt für Schritt - eine Zustandsgruppe „Review“ mit Entwurf, In Prüfung und Freigegeben anlegen, zuerst bestehende öffentliche Inhalte als Freigegeben markieren, dann Besucher nur freigegebene Inhalte lesen lassen und Autoren und Prüfern die richtigen Zustandswechsel geben.
keywords: Objektzustände, Zustandsgruppe, Prüfung, Entwurf, In Prüfung, Freigegeben, StateGroup, NewState, state/assign, Setup Zustände, Prüfung von Inhalten, Freigabe, object states, state group, review, Draft, In review, Approved, Setup States, content review, approval
type: help
---
# Eine Zustandsgruppe für die Prüfung einrichten

Diese Aufgabe ist für Administratoren. Am Ende tragen Inhalte eine Prüfstufe, Besucher sehen nur Inhalte im Zustand
*Approved*, Autoren können ihre Entwürfe zur Prüfung schicken, und Prüfer geben frei. Sie folgt
[Objektzustände, Abschnitte 4 und 9](../../../../anleitungen/objektzustaende.md#9-ein-durchgearbeitetes-beispiel-ein-prüf-workflow).
Hintergrund: [Objektzustände in Berechtigungen](../../betrieb/personen-und-berechtigungen/objektzustaende-in-berechtigungen.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Gruppe und die Zustände anlegen](#die-gruppe-und-die-zustände-anlegen)
3. [Zuerst bestehende Inhalte freigeben](#zuerst-bestehende-inhalte-freigeben)
4. [Die Rollen ändern](#die-rollen-ändern)
5. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- `state/administrate` (Gruppen und Zustände), `state/assign` (sie setzen) und das Modul `role`; Administratoren haben
  sie.
- Ein ruhiger Moment: Schritt 3 läuft über die ganze Site.
- Erwägen Sie stattdessen den Freigabe-Workflow, wenn Sie nur eine Abzeichnung vor dem Veröffentlichen brauchen
  ([Überblick über Freigabe-Workflows](../../betrieb/redaktionelle-workflows/freigabe-workflows-im-ueberblick.md)).

## Die Gruppe und die Zustände anlegen

1. **Setup > Zustände** (`/state/groups`), **Neue Zustandsgruppe**.
2. **Bezeichner** `review` (Kleinbuchstaben, Ziffern und Unterstriche, nicht mit `ez` beginnend); Name `Review` in der
   Hauptsprache. **Erstellen**.
3. Auf der Seite der Gruppe **Neuer Zustand**: Bezeichner `draft`, Name `Draft`. **Zustand anlegen**. Der erste Zustand
   wird die Voreinstellung und jedem bestehenden Objekt gegeben.
4. Noch einmal: `in_review` / `In review`, dann `approved` / `Approved`.

## Zuerst bestehende Inhalte freigeben

Jedes Objekt ist jetzt in *Draft*. Bevor Sie Besucher einschränken:

1. Öffnen Sie den obersten Knoten der öffentlichen Site, Reiter **Details**, Kasten **Zustand des Objekts**.
2. Wählen Sie *Approved*, markieren Sie **Auch für alles unterhalb dieses Knotens**, klicken Sie auf
   **Zustände einstellen**.
3. Ein großer Teilbaum wird nach einer Bestätigung zu einem Inhaltsauftrag; warten Sie, bis er abgeschlossen ist.
4. Tun Sie dasselbe für die Medienbibliothek, wenn Besucher sie sehen.

## Die Rollen ändern

| Rolle | Änderung |
|---|---|
| Anonymous | ihre Richtlinie `content/read`: `StateGroup_review = Approved` hinzufügen |
| Autoren (Redakteure der Abteilungen) | `content/edit` mit `StateGroup_review = Draft`; `state/assign` mit `Owner = Self`, `StateGroup_review = Draft`, `NewState = review/in_review` |
| Prüfer | `state/assign` mit `NewState = review/draft, review/approved` |

Jede Änderung: die Rolle, **Bearbeiten**, **Neue Richtlinie** oder **Einschränkungen**, die Werte wählen, **OK**,
**Speichern**.

## Prüfen, ob es funktioniert hat

- **Setup > Zustände**, die Gruppe: „Rollen, die diese Gruppe verwenden“ listet Anonymous und die Rollen der Autoren
  und Prüfer.
- Ein neuer Artikel bleibt in einem privaten Fenster unsichtbar, bis ein Prüfer *Approved* setzt.
- Ein Autor kann *In review* setzen und danach nicht mehr bearbeiten; ein Prüfer kann freigeben.
- Zustandsänderungen erscheinen im Audit-Protokoll als `content.object.state`.

## Verwandte Seiten
- [Prüfdaten mit Objektzuständen](../../betrieb/redaktionelle-workflows/pruefdaten-mit-objektzustaenden.md)
- [Juristischer Prüfschritt](../../betrieb/redaktionelle-workflows/rechtliche-pruefung-als-schritt.md)
- [Anleitung zu den Objektzuständen](../../../../anleitungen/objektzustaende.md)
- [Inhaltsaufträge](../../../../funktionen/6.0/inhaltsauftraege.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
