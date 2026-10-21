---
title: Bekanntmachungen an einem Datum automatisch verstecken
audience: government, admin, developer
category: public-sector
order: 510
summary: Schritt für Schritt - Redakteure jeder Bekanntmachung ein Versteckdatum geben lassen und den Cronjob hide.php von Exponential sie verstecken lassen, wenn das Datum verstrichen ist, indem Sie der Klasse ein Datumsattribut hinzufügen und content.ini [HideSettings] konfigurieren.
keywords: Versteckdatum, geplantes Verstecken, hide.php, HideSettings, RootNodeList, HideDateAttributeList, Cronjob, Bekanntmachungen, Ablauf, Archivierung, Datumsattribut, öffentlicher Sektor, hide date, scheduled hide, cronjob, notices, expiry, archiving, date attribute, public sector
type: help
---
# Bekanntmachungen an einem Datum automatisch verstecken

Für Administratoren und Entwickler. Das Cronjob-Skript `hide.php` des Kernels (im Standardteil der Cronjobs) versteckt
Objekte ausgewählter Klassen, deren Datumsattribut verstrichen ist; es tut nichts, bevor es konfiguriert ist. Versteckte
Bekanntmachungen bleiben mit ihrem Verlauf im Baum und lassen sich wieder einblenden
([Alte Bekanntmachungen archivieren](../../schriftgut-und-archivierung/alte-bekanntmachungen-archivieren.md#muster-an-einem-datum-verstecken)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Klasse der Bekanntmachungen (zum Beispiel mit dem Bezeichner `notice`) und die Knoten-ID des Ordners, der sie
  enthält.
- Rechte, Klassen zu bearbeiten; Zugriff auf die Einstellungen.
- Der Standardteil der Cronjobs läuft auf dem Server (`php runcronjobs.php` aus der Crontab).

## Schritte

1. **Setup > Klassen**, bearbeiten Sie die Klasse der Bekanntmachungen, fügen Sie ein Attribut vom Datentyp Datum (oder
   Datum und Uhrzeit) mit dem Bezeichner `hide_date` und dem Namen „Verstecken am“ hinzu. Speichern.
2. In `settings/override/content.ini.append.php`:

   ```ini
   [HideSettings]
   RootNodeList[]
   RootNodeList[]=<node id of the notices folder>
   HideDateAttributeList[]
   HideDateAttributeList[notice]=hide_date
   ```

3. Leeren Sie den INI-Cache.
4. Bitten Sie die Redakteure, „Verstecken am“ auszufüllen, wenn sie eine Bekanntmachung veröffentlichen.

Nicht zu verwechseln mit `unpublish.php` und `[UnpublishSettings]`, die Objekte mit einem verstrichenen
`unpublish_date` in den **Papierkorb** verschieben; für Schriftgut ist das Verstecken die sicherere Wahl.

## Prüfen, ob es funktioniert hat

1. Veröffentlichen Sie eine Test-Bekanntmachung mit einem Versteckdatum in der Vergangenheit, in einem Testordner unter
   der konfigurierten Wurzel.
2. Führen Sie den Standardteil von Hand aus: `php runcronjobs.php -q`, oder nur das Skript: Die Ausgabe listet
   „Hiding node: ...“.
3. Die Bekanntmachung erscheint in der Administration als **Versteckt** und ist von der öffentlichen Site
   verschwunden; das Audit zeichnet `content.node.hide` auf.

## Verwandte Seiten

- [Alte Bekanntmachungen archivieren](../../schriftgut-und-archivierung/alte-bekanntmachungen-archivieren.md)
- [Inhalte verstecken](../../schriftgut-und-archivierung/inhalte-verbergen.md)
- [Cronjobs](../../../../anleitungen/cronjobs.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
