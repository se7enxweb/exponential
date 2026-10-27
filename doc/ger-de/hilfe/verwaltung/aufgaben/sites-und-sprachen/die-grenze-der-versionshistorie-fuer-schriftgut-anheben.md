---
title: Die Grenze der Versionshistorie für Schriftgut anheben
audience: government, admin
category: public-sector
order: 300
summary: Aufgabe für Administratoren - für schriftgutrelevante Klassen (Beschlüsse, Offenlegungseinträge, Sitzungen) mehr Versionen als die voreingestellten 45 aufbewahren, mit VersionHistoryClass in content.ini, und die Wirkung prüfen.
keywords: Versionsgrenze, Versionen, Schriftgut, Aufbewahrung, version limit, DefaultVersionHistoryLimit, VersionHistoryClass, content.ini, versions, records, cleanupversions.php
type: help
---
# Die Grenze der Versionshistorie für Schriftgut anheben

Diese Aufgabe richtet sich an Administratoren. Wenn ein Objekt seine Versionsgrenze erreicht, wird die älteste
archivierte Version entfernt, um Platz zu schaffen. Hintergrund:
[Versionen länger aufbewahren](../../transparenz-und-informationsfreiheit/versionen-laenger-aufbewahren.md).

## Bevor Sie beginnen

- Stimmen Sie die Zahl mit Ihrem Verantwortlichen für das Schriftgut ab.
- Kennen Sie die Bezeichner der Klassen (**Setup > Klassen**).

## Schritte

1. Legen Sie `settings/override/content.ini.append.php` an oder bearbeiten Sie sie:

   ```ini
   <?php /*
   [VersionManagement]
   VersionHistoryClass[decision]=500
   VersionHistoryClass[disclosure_entry]=200
   VersionHistoryClass[meeting]=200
   */ ?>
   ```

   Die mitgelieferten Werte sind `DefaultVersionHistoryLimit=45` und `VersionHistoryClass[1]=75`.
2. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini`; mit Velocity auch
   `./console exp:velocity cache clear`.

## Prüfen

**INI-Einstellungen** (`/settings/view`) zeigt den Wert für `content.ini`; nach mehr als 45 Bearbeitungen eines
Beschlusses listet seine Seite **Versionen** weiterhin die frühen Versionen.

## Probleme

| Problem | Hinweis |
|---|---|
| Versionen bereits entfernt | die neue Grenze gilt ab jetzt; entfernte Versionen sind weg |
| Eine Grenze später senken | entfernt keine Versionen, bis `php bin/php/cleanupversions.php` läuft; sichern Sie vorher |

## Verwandte Seiten

- [Versionsgeschichte als Rechenschaft](../../transparenz-und-informationsfreiheit/versionsgeschichte-als-rechenschaft.md)
