---
title: Die Grenze der Versionsgeschichte
audience: government, admin, project-manager
category: public-sector
order: 40
summary: Für Administratoren und Zuständige für Schriftgut - wie viele Versionen Exponential pro Objekt aufbewahrt (standardmäßig 45, pro Klasse einstellbar), was beim Erreichen der Grenze geschieht, wie Sie sie für Klassen mit Schriftgut anheben und das Aufräumskript beim Senken.
keywords: Grenze der Versionsgeschichte, DefaultVersionHistoryLimit, VersionHistoryClass, content.ini, VersionManagement, archivierte Versionen, cleanupversions.php, Schriftgut, Aufbewahrung von Versionen, Bekanntmachungen, öffentlicher Sektor, version history limit, archived versions, records, retention of versions, notices, public sector
type: help
---
# Die Grenze der Versionsgeschichte

Diese Seite richtet sich an Administratoren und Zuständige für Schriftgut. Exponential bewahrt nicht jede Version für
immer auf. Wenn ein Objekt seine Grenze erreicht, entfernt das Veröffentlichen einer neuen Version die älteste
archivierte
([Versionen eines Objekts: Ein neuer Entwurf aus einer alten Version](../../../anleitungen/inhaltsverlauf.md#3-ein-neuer-entwurf-aus-einer-alten-version)).
Für Schriftgut sollten Sie die Grenze kennen und pro Klasse festlegen.

## Inhalt

1. [Die Einstellungen](#die-einstellungen)
2. [Werte wählen](#werte-wählen)
3. [Anheben oder senken](#anheben-oder-senken)
4. [Verwandte Seiten](#verwandte-seiten)

## Die Einstellungen

`settings/content.ini [VersionManagement]`:

```ini
[VersionManagement]
DefaultVersionHistoryLimit=45
VersionHistoryClass[1]=75
```

- `DefaultVersionHistoryLimit`: aufbewahrte Versionen pro Objekt, für jede Klasse ohne eigenen Wert.
- `VersionHistoryClass[<class id or class identifier>]`: eine Grenze für eine Klasse (Klassenbezeichner werden seit
  4.1.1 akzeptiert, wie die Einstellungsdatei vermerkt).

## Werte wählen

| Klasse | Typische Wahl | Warum |
|---|---|---|
| Amtliche Bekanntmachungen, Beschlüsse, Vorschriften | eine hohe Grenze | jede veröffentlichte Fassung kann später wichtig sein |
| Nachrichtenartikel | die Voreinstellung | |
| Häufig bearbeitete Seiten (Öffnungszeiten, Kontakt) | die Voreinstellung oder niedriger | viele kleine Änderungen |

Eine hohe Grenze kostet Platz in der Datenbank; die normale Nutzung verlangsamt sie kaum. Um sicher zu wissen, was
veröffentlicht war, verlassen Sie sich nicht allein auf Versionen: Exportieren Sie das Schriftstück beim
Veröffentlichen in Ihr Schriftgutsystem (siehe „Inhalte exportieren“).

## Anheben oder senken

In `settings/override/content.ini.append.php`:

```ini
[VersionManagement]
VersionHistoryClass[notice]=500
```

Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

- **Anheben** wirkt sich auf künftiges Veröffentlichen aus; bereits entfernte Versionen kommen nicht zurück.
- **Senken** entfernt von sich aus nichts; die Einstellungsdatei sagt, dass Sie `bin/php/cleanupversions.php`
  ausführen können, um die archivierten Versionen über der neuen Grenze zu entfernen. Tun Sie das nur mit Zustimmung
  der Zuständigen für Schriftgut und nach einer Datensicherung.

Das Entfernen von Versionen wird im Audit als `content.version.remove` aufgezeichnet.

## Verwandte Seiten

- [Versionen und Inhaltsgeschichte](versionen-und-inhaltsgeschichte.md)
- [Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)
- [Inhaltsmodell und Bearbeitung](../../../anleitungen/inhaltsmodell-und-bearbeitung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
