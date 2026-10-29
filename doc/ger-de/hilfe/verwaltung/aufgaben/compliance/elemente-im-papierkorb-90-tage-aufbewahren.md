---
title: Elemente im Papierkorb 90 Tage aufbewahren
audience: government, admin
category: public-sector
order: 500
summary: Schritt für Schritt - den Papierkorb von Exponential nach Plan leeren und dabei jedes Element eine festgelegte Zahl von Tagen behalten, mit content.ini [TrashSettings] KeepItemsForDays und dem Teil trashpurge der Cronjobs (standardmäßig in keinem Teil), und mit der Kommandozeile prüfen.
keywords: Papierkorb, Papierkorb bereinigen, KeepItemsForDays, trashpurge, Teil der Cronjobs, bin/php/trashpurge.php, trashed-days, Aufbewahrung, Speicherbegrenzung, öffentlicher Sektor, trash, trash purge, cronjob part, retention, storage limitation, public sector
type: help
---
# Elemente im Papierkorb 90 Tage aufbewahren

Für Administratoren. Entfernte Inhalte warten im Papierkorb, bis jemand ihn leert. Ein Bereinigen nach Plan verhindert,
dass der Papierkorb personenbezogene Daten für immer hält, und lässt dennoch Zeit, Fehler rückgängig zu machen
([Die Ansicht des Papierkorbs](../../../../bc/6.0/papierkorb.md#elemente-eine-zeit-lang-im-papierkorb-behalten)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Vereinbaren Sie den Zeitraum mit den Verantwortlichen für das Schriftgut; prüfen Sie zuerst auf rechtliche
  Aufbewahrungspflichten („Legal Holds“).
- Elemente, die vor 6.0 in den Papierkorb kamen, haben keine Papierkorbzeit (`trashed = 0`) und werden beim ersten Lauf
  unabhängig von der Einstellung bereinigt; siehe die Anleitung zum Papierkorb, um sie zu behalten.

## Schritte

1. In `settings/override/content.ini.append.php` (es gibt einen Papierkorb je Datenbank, also setzen Sie es dort):

   ```ini
   [TrashSettings]
   KeepItemsForDays=90
   ```

   Leer oder 0 bereinigt alles; ein Wert, der keine ganze Zahl von Tagen bis 36500 ist, bereinigt nichts und meldet einen
   Fehler, sodass ein Tippfehler den Papierkorb nicht leeren kann.
2. Fügen Sie in `settings/override/cronjob.ini.append.php` den Teil der Cronjobs hinzu, der standardmäßig in keinem Teil
   steht:

   ```ini
   [CronjobPart-trashpurge]
   Scripts[]
   Scripts[]=trashpurge.php
   ```

3. Planen Sie ihn in der Crontab des Benutzers der Site, zum Beispiel nachts: `php runcronjobs.php -q trashpurge`.

## Prüfen, ob es funktioniert hat

- Auf der Kommandozeile dieselbe Bereinigung zum Test: `php bin/php/trashpurge.php --apply --trashed-days=90`, zuerst auf einer
  Kopie.
- `var/log/trashpurge.log` (Setup > Logdateien) zeigt jeden Lauf; der Papierkorb behält Elemente, die jünger als 90 Tage
  sind.
- Das Audit zeichnet auf, was bereinigt wurde.

## Verwandte Seiten

- [Der Papierkorb und das Wiederherstellen von Inhalten](../../schriftgut-und-archivierung/papierkorb-und-wiederherstellen.md)
- [Ein Element aus dem Papierkorb wiederherstellen](ein-element-aus-dem-papierkorb-wiederherstellen.md)
- [Cronjobs](../../../../anleitungen/cronjobs.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
