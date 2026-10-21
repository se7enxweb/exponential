---
title: Versionen und Inhaltsgeschichte
audience: government, editor, admin
category: public-sector
order: 30
summary: Für Redaktionen und Zuständige für Schriftgut - wie Exponential jedes Speichern eines Objekts als Version aufbewahrt (Entwurf, veröffentlicht, archiviert, ausstehend, abgelehnt), wie Sie sehen, wer wann was geändert hat, Versionen vergleichen, einen früheren Text zurückholen, und die Grenzen, die für Schriftgut wichtig sind.
keywords: Versionen, Inhaltsgeschichte, Versionsgeschichte, archivierte Versionen, Versionen vergleichen, Diff, neuen Entwurf machen, Ersteller, geändert, content/history, content/versionread, content/diff, Rechenschaft, öffentlicher Sektor, versions, content history, version history, archived versions, compare versions, make a new draft, creator, modified, accountability, public sector
type: help
---
# Versionen und Inhaltsgeschichte

Diese Seite richtet sich an Redaktionen, Veröffentlichende und Zuständige für Schriftgut. Jedes Speichern eines Objekts
in Exponential ist eine **Version**; die Versionsseite zeigt sie, vergleicht sie und erstellt aus alten Versionen neue
Entwürfe ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)).

## Inhalt

1. [Status einer Version](#status-einer-version)
2. [Die Versionsseite](#die-versionsseite)
3. [Die Frage beantworten: „Was stand an jenem Tag auf der Seite?“](#die-frage-beantworten-was-stand-an-jenem-tag-auf-der-seite)
4. [Grenzen](#grenzen)
5. [Verwandte Seiten](#verwandte-seiten)

## Status einer Version

| Status | Bedeutet |
|---|---|
| Entwurf | wird bearbeitet; nur sein Ersteller bearbeitet ihn |
| Veröffentlicht | die Version, die Besucher sehen |
| Archiviert | eine früher veröffentlichte Version, aufbewahrt |
| Ausstehend | wartet in einem Freigabe-Workflow |
| Abgelehnt | in einem Freigabe-Workflow abgelehnt |

Wenn ein Entwurf veröffentlicht wird, wird die Version davor archiviert.

## Die Versionsseite

`/content/history/<object id>`, geöffnet mit **Versionen verwalten** im Editor oder über das Kontextmenü des Eintrags:

- Kennzahlen (alle Versionen, pro Status, Übersetzungen), Filter nach Status, Übersetzung und Ersteller, Sortierung;
- eine Karte pro Version: Nummer, Status, Übersetzungen, **Ersteller**, **Erstellt** und **Geändert**;
- **Ansehen** (erfordert `content/versionread`), **Vergleichen** (erfordert `content/diff`), **Neuen Entwurf machen**;
- **Ausgewählte Versionen entfernen**: Entwürfe, archivierte, abgelehnte; nie die veröffentlichte Version oder
  Versionen in einem laufenden Workflow.

## Die Frage beantworten: „Was stand an jenem Tag auf der Seite?“

1. Öffnen Sie die Versionsseite; filtern Sie nach dem Status **Archiviert** und sortieren Sie mit den ältesten zuerst.
2. Suchen Sie die letzte Version, deren Veröffentlichungsdatum (geändert) vor dem fraglichen Datum liegt.
3. **Ansehen**; **Vergleichen** Sie sie mit der nächsten, um zu sehen, was sich geändert hat.
4. Für einen Nachweis notieren Sie Versionsnummer und Daten und speichern Sie eine Kopie (Drucken als PDF aus der
   Versionsansicht).
5. Wer sie veröffentlicht hat: der Ersteller der Version; für eine systemweite Aufzeichnung schalten Sie das
   Audit-Ereignis `content.object.publish` ein (standardmäßig aus), damit das Veröffentlichen im Audit-Trail
   protokolliert wird.

## Grenzen

- Versionen werden bis zur **Grenze der Versionsgeschichte** aufbewahrt (`content.ini [VersionManagement]`,
  standardmäßig 45); dann wird die älteste archivierte Version entfernt. Siehe „Die Grenze der Versionsgeschichte“.
- Das Entfernen von Versionen von Hand ist endgültig.
- Ein entferntes Objekt (nach dem Leeren des Papierkorbs) nimmt seine Versionen mit.
- Redaktionen, die ein Objekt bearbeiten dürfen, können seine Versionen auch ohne Leserichtlinie öffnen; beschränken
  Sie die Bearbeitungsrichtlinien entsprechend.

## Verwandte Seiten

- [Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)
- [Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md)
- [Entwürfe und ausstehende Einträge](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
