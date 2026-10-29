---
title: Wie ändere ich, wie viele Versionen eines Objekts aufbewahrt werden?
audience: admin
category: manual
order: 640
summary: Die Grenze ist content.ini [VersionManagement] DefaultVersionHistoryLimit, mit VersionHistoryClass[] für einzelne Klassen; erreicht ein Objekt sie, wird seine älteste archivierte Version entfernt, und bin/php/cleanupversions.php kürzt alte Objekte, nachdem Sie die Grenze gesenkt haben.
keywords: Versionen, Versionsverlauf, Versionsgrenze, Versionslimit, archivierte Versionen, DefaultVersionHistoryLimit, VersionHistoryClass, cleanupversions, content.ini, VersionManagement, versions, version history, version limit, archived versions
context: content/history
type: help
---
# Wie ändere ich, wie viele Versionen eines Objekts aufbewahrt werden?

Jede Bearbeitung erzeugt eine neue Version, und die Version, die sie ersetzt, wird archiviert. Damit die Datenbank
nicht endlos wächst, bewahrt jedes Objekt höchstens eine festgelegte Anzahl von Versionen auf. Würde eine Bearbeitung
die Grenze überschreiten, wird die älteste archivierte Version dieses Objekts entfernt.

## Wer das tun kann

Ein Administrator mit **Setup** > **INI-Einstellungen** (eine Rolle mit dem Modul `settings`) oder jemand, der die
Einstellungsdateien der Website ändern darf. Für das Bereinigungsskript braucht man Shell-Zugang.

## Schritte

1. Sehen Sie die aktuellen Werte nach: **Setup** > **INI-Einstellungen**, Datei `content.ini`, Block
   `[VersionManagement]`.
   `DefaultVersionHistoryLimit` gilt für alle Klassen; jede Zeile `VersionHistoryClass[]` setzt die Grenze einer
   Klasse, über Klassen-ID oder Bezeichner. Eine neue Installation bewahrt 45 Versionen auf, bei Ordnern 75.
2. Ändern Sie die Werte mit **Bearbeiten** auf dieser Seite und speichern Sie sie für jeden Siteaccess, oder schreiben
   Sie sie in eine Override-Datei, zum Beispiel `settings/override/content.ini.append.php`:

   ```ini
   [VersionManagement]
   DefaultVersionHistoryLimit=20
   VersionHistoryClass[article]=50
   ```

3. Leeren Sie die INI-Caches ([Welchen Cache leere ich, und wie?](welchen-cache-leere-ich.md)).
4. Wenn Sie eine Grenze gesenkt haben, entfernen Sie die überzähligen Versionen aus vorhandenen Objekten:

   ```bash
   php bin/php/cleanupversions.php
   ```

   Das Skript wartet 10 Sekunden, bevor es beginnt; `-n` überspringt die Wartezeit. Es entfernt nur archivierte
   Versionen.

## Was schiefgehen kann

- **„Der Versionsverlauf hat sein Limit erreicht und keine archivierte Version kann vom System entfernt werden.“** Das
  Objekt hat so viele Entwürfe und wartende Versionen, wie die Grenze erlaubt, also lässt sich nichts entfernen.
  Veröffentlichen oder entfernen Sie einige Entwürfe des Objekts, oder erhöhen Sie seine Grenze.
- **Eine Grenze unter 2.** Das System verwendet stattdessen 2 und schreibt eine Warnung in die Debug-Ausgabe.
- **Eine alte Version, die Sie wollten, ist weg.** Das Entfernen von Versionen lässt sich nicht rückgängig machen.
  Erhöhen Sie die Grenze, bevor Sie den Verlauf brauchen, nicht danach.
- **Die Einstellung wirkt nicht.** Sie wurde nur in eine Datei eines einzigen Siteaccess geschrieben, oder der
  INI-Cache wurde nicht geleert.

## Verwandte Seiten

- [Wie kehre ich zu einer früheren Version zurück?](wie-kehre-ich-zu-einer-frueheren-version-zurueck.md)
- [Inhaltsverlauf](../../anleitungen/inhaltsverlauf.md)
- [Versionen, Entwürfe und Veröffentlichen](../cms-entwickler/versionen-entwuerfe-und-veroeffentlichen.md)
- [Die Einstellungsseite](../../anleitungen/einstellungsseite.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
