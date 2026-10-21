---
title: Entwürfe und alte Entwürfe
audience: government, editor, admin
category: public-sector
order: 50
summary: Für Redaktionen und Administratoren - was Entwürfe in Exponential sind, wie Redaktionen ihre eigenen alten Entwürfe finden und entfernen, welche Einstellungen und Cronjob-Skripte es zum Aufräumen alter Entwürfe gibt und warum Entwürfe selten Schriftgut sind.
keywords: Entwürfe, alte Entwürfe, Meine Entwürfe, content/draft, Alte Entwürfe entfernen, DraftsDuration, old_drafts_cleanup.php, interne Entwürfe, internal_drafts_cleanup.php, ausstehende Einträge, Schriftgut, öffentlicher Sektor, drafts, old drafts, My drafts, remove old drafts, internal drafts, pending items, records, public sector
type: help
---
# Entwürfe und alte Entwürfe

Diese Seite richtet sich an Redaktionen und Administratoren. Entwürfe sind Versionen, die nie veröffentlicht wurden.
Sie sammeln sich an, sie können unfertigen oder heiklen Text enthalten, und sie sind meist kein Schriftgut. Exponential
lässt Redaktionen ihre eigenen Entwürfe aufräumen und bietet Administratoren Aufräumskripte
([Entwürfe und ausstehende Einträge](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)).

## Inhalt

1. [Redaktionen: Meine Entwürfe](#redaktionen-meine-entwürfe)
2. [Administratoren: Einstellungen zum Aufräumen](#administratoren-einstellungen-zum-aufräumen)
3. [Sicht der Schriftgutverwaltung](#sicht-der-schriftgutverwaltung)
4. [Verwandte Seiten](#verwandte-seiten)

## Redaktionen: Meine Entwürfe

**Meine Entwürfe** (`/content/draft`) zeigt nur Ihre eigenen Entwürfe:

- Kennzahlen (Ihre, von neuen Objekten, seit 30 Tagen oder dem gewählten Alter nicht geändert);
- Filter nach Übersetzung, Klasse und Alter (7, 30, 90, 365 Tage);
- **Alte Entwürfe entfernen** nach 7, 30, 90 oder 365 Tagen ohne Änderung, **Ausgewählte entfernen**, **Alle
  entfernen**, jeweils an Ort und Stelle bestätigt.

Ein Entwurf eines neuen Objekts (nie veröffentlicht) nimmt das Objekt beim Entfernen mit; ein Entwurf eines
veröffentlichten Objekts lässt die veröffentlichte Version unverändert. Jeder Entwurf wird beim Entfernen erneut
geprüft: Sie können nur Ihre eigenen entfernen.

## Administratoren: Einstellungen zum Aufräumen

`settings/content.ini [VersionManagement]` enthält die Zeiträume, die die Aufräumskripte verwenden:

| Einstellung | Ausgeliefert | Verwendet von |
|---|---|---|
| `DraftsDuration[days]` | 90 | `cronjobs/old_drafts_cleanup.php`, „Remove user drafts that exceed the configured age limit“ (Benutzerentwürfe entfernen, die die eingestellte Altersgrenze überschreiten) |
| `DraftsCleanUpLimit` | 100 | dasselbe, pro Lauf |
| `InternalDraftsDuration[days]` | 1 | `cronjobs/internal_drafts_cleanup.php` (interne Entwürfe) |
| `InternalDraftsCleanUpLimit` | 100 | dasselbe, pro Lauf |

`internal_drafts_cleanup.php` gehört zum Standardteil der Cronjobs; `old_drafts_cleanup.php` gehört in der
ausgelieferten `settings/cronjob.ini` zu keinem Teil, sodass alte Benutzerentwürfe nur entfernt werden, wenn Sie das
Skript einem Teil hinzufügen (in `settings/override/cronjob.ini.append.php`). Entscheiden Sie das mit Ihren Redaktionen:
Ein seit 90 Tagen unveränderter Entwurf kann trotzdem noch gewollt sein.

## Sicht der Schriftgutverwaltung

- Entwürfe sind laufende Arbeit; die veröffentlichten und archivierten Versionen sind die Kandidaten für Schriftgut.
- Ein Entwurf kann noch personenbezogene Daten enthalten (der Entwurf einer Antwort an eine Bürgerin); das Entfernen
  alter Entwürfe unterstützt die Datenminimierung.
- Ausstehende Versionen (in einem Freigabe-Workflow) sind keine Entwürfe; sie stehen in **Meine Warteliste** und können
  nicht entfernt werden, solange der Workflow läuft.

## Verwandte Seiten

- [Entwürfe und ausstehende Einträge](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)
- [Versionen und Inhaltsgeschichte](versionen-und-inhaltsgeschichte.md)
- [Cronjobs](../../../anleitungen/cronjobs.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
