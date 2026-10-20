---
title: Das Versionslimit für Bekanntmachungen anheben
audience: government, admin
category: public-sector
order: 520
summary: Schritt für Schritt - von amtlichen Bekanntmachungen mehr Versionen als die standardmäßigen 45 je Objekt in Exponential behalten, indem Sie VersionHistoryClass für diese Klasse setzen, sodass frühere veröffentlichte Fassungen nicht entfernt werden, wenn neue Versionen veröffentlicht werden.
keywords: Limit der Versionsgeschichte, VersionHistoryClass, DefaultVersionHistoryLimit, content.ini, VersionManagement, Bekanntmachungen, archivierte Versionen, Schriftgut, cleanupversions.php, öffentlicher Sektor, version history limit, notices, archived versions, records, public sector
type: help
---
# Das Versionslimit für Bekanntmachungen anheben

Für Administratoren, auf Wunsch der Verantwortlichen für das Schriftgut. Wenn ein Objekt sein Versionslimit erreicht,
entfernt das Veröffentlichen die älteste archivierte Version. Voreingestellt sind 45 je Objekt; Klassen können ein
eigenes Limit haben ([Das Limit der Versionsgeschichte](../../../../../eng-us/help/government/records-and-archiving/version-history-limit.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Der Bezeichner der Klasse (Setup > Klassen) und das Limit, das die Verantwortlichen für das Schriftgut wünschen.
- Das Anheben des Limits wirkt nur auf künftiges Veröffentlichen; bereits entfernte Versionen kommen nicht zurück.

## Schritte

1. In `settings/override/content.ini.append.php`:

   ```ini
   [VersionManagement]
   VersionHistoryClass[notice]=500
   ```

   (Bezeichner von Klassen werden ebenso akzeptiert wie Klassen-IDs; die mitgelieferte Datei setzt außerdem
   `VersionHistoryClass[1]=75`).
2. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

## Prüfen, ob es funktioniert hat

- `./console exp:ini get content.ini/VersionManagement/VersionHistoryClass --allow-root-user` listet Ihren Eintrag.
- Bei einer Bekanntmachung mit vielen Versionen entfernt das Veröffentlichen die älteste archivierte Version nicht mehr,
  bis das neue Limit erreicht ist.

Senken Sie niemals ein Limit und führen `bin/php/cleanupversions.php` ohne Zustimmung der Verantwortlichen für das
Schriftgut aus: Das entfernt archivierte Versionen über dem neuen Limit.

## Verwandte Seiten

- [Das Limit der Versionsgeschichte](../../../../../eng-us/help/government/records-and-archiving/version-history-limit.md)
- [Versionen und Inhaltsverlauf](../../../../../eng-us/help/government/records-and-archiving/versions-and-content-history.md)
- [Eine frühere Version einer Seite zurückholen](eine-fruehere-version-einer-seite-zurueckholen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
