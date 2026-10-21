---
title: Objektzustände für Schriftgut
audience: government, admin, editor
category: public-sector
order: 70
summary: Für Administratoren öffentlicher Websites - wie Sie die Objektzustände von Exponential nutzen, um den Lebenszyklus von Inhalten zu kennzeichnen (aktuell, archiviert, unter Aufbewahrungssperre, zur Veröffentlichung freigegeben), steuern, wer Inhalte in jedem Zustand sehen und ändern darf, und Zustände für ganze Teilbäume setzen.
keywords: Objektzustände, Zustandsgruppen, Lebenszyklus, archiviert, Aufbewahrungssperre, Legal Hold, Beschränkung StateGroup, NewState, state/assign, Zustand des Objekts, Teilbaum, Inhaltsauftrag, expstate, Schriftgut, öffentlicher Sektor, object states, state groups, lifecycle, archived, legal hold, StateGroup limitation, content job, records, public sector
type: help
---
# Objektzustände für Schriftgut

Diese Seite richtet sich an Administratoren. Ein **Objektzustand** ist eine Kennzeichnung, die jedes Objekt trägt, eine
aus jeder Zustandsgruppe. Zustände ändern von sich aus nichts; erst Rollen machen sie wirksam: Eine Richtlinie, die nach
einer Zustandsgruppe beschränkt ist, gilt nur für Objekte in den gewählten Zuständen
([Objektzustände](../../../anleitungen/objektzustaende.md)). Für Schriftgut drücken Zustände aus, wo sich ein Inhalt in seinem
Lebenszyklus befindet.

## Inhalt

1. [Gruppen für Schriftgut](#gruppen-für-schriftgut)
2. [Einrichten](#einrichten)
3. [Richtlinien, die Zustände verwenden](#richtlinien-die-zustände-verwenden)
4. [Zustände in großer Zahl setzen](#zustände-in-großer-zahl-setzen)
5. [Verwandte Seiten](#verwandte-seiten)

## Gruppen für Schriftgut

| Gruppe (Beispiel) | Zustände | Verwendung |
|---|---|---|
| `lifecycle` | Aktuell (Voreinstellung), Archiviert | aktuelle von archivierten Inhalten trennen, ohne sie zu verschieben |
| `hold` | Keine (Voreinstellung), Aufbewahrungssperre | Inhalte während einer Sperre vor Bearbeiten und Entfernen schützen (siehe „Aufbewahrungssperren“) |
| `clearance` | Entwurf (Voreinstellung), Freigegeben | nur freigegebene Inhalte dürfen von Besuchern gelesen werden |

Der erste Zustand einer Gruppe ist ihre Voreinstellung; wenn eine Gruppe ihren ersten Zustand erhält, bekommt jedes
vorhandene Objekt diesen.

## Einrichten

1. **Setup > Zustände** (`/state/groups`): Legen Sie die Gruppe an (der Bezeichner darf nicht mit `ez` beginnen), dann
   ihre Zustände in der gewünschten Reihenfolge.
2. Übersetzen Sie Namen und Beschreibungen für jede Sprache der Administration.
3. Ändern Sie die Rollen (siehe unten).
4. Testen Sie mit einem Testbenutzer, bevor Sie die Redaktionen informieren.

## Richtlinien, die Zustände verwenden

- `StateGroup_<group>` beschränkt `content/read`, `content/edit`, `content/publish`, `content/manage_locations`,
  `content/remove` und `state/assign` auf Objekte in den gewählten Zuständen.
- `NewState` bei `state/assign` legt fest, welche Zustände eine Rolle setzen darf.

Beispiele:

| Ziel | Richtlinie |
|---|---|
| Besucher sehen nur aktuelle Inhalte | Anonymous: `content/read` mit `StateGroup_lifecycle = Current` (zusätzlich zu ihrer Sektionsbeschränkung) |
| Niemand bearbeitet Inhalte unter Sperre | `content/edit` und `content/remove` der Redaktion beschränkt auf `StateGroup_hold = None` |
| Nur die Schriftgutverwaltung setzt oder hebt eine Sperre auf | Rolle der Schriftgutverwaltung: `state/assign` mit `NewState = hold/legal_hold, hold/none`; die Redaktion ohne dieses Recht |

Richtlinien mehrerer Rollen addieren sich: Prüfen Sie jede Rolle, die eine Redakteurin oder ein Redakteur hat.

## Zustände in großer Zahl setzen

- Ein Objekt: der Reiter **Details** des Knotens, **Zustand des Objekts**.
- Ein Teilbaum: dasselbe, mit „Also for everything below this node“ (auch für alles unterhalb dieses Knotens; bei
  großen Teilbäumen ein Inhaltsauftrag).
- Von einer Shell oder einem entfernten Client aus: die im Leitfaden beschriebenen Dienste von `expstate`.

Zustandsänderungen werden im Audit als `content.object.state` aufgezeichnet (standardmäßig eingeschaltet).

## Verwandte Seiten

- [Objektzustände](../../../anleitungen/objektzustaende.md)
- [Sektionen und Objektzustände für Websiteersteller](../../ersteller/website-ersteller/sektionen-und-objektzustaende.md)
- [Aufbewahrungssperren](aufbewahrungssperren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
