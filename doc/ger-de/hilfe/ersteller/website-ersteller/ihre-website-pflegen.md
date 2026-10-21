---
title: Ihre Website pflegen
audience: website-creator, admin
category: website-creation
order: 690
summary: Eine Live-Website gesund halten - tägliche, wöchentliche, monatliche und jährliche Aufgaben, die Seiten der Administration, die zeigen, was Aufmerksamkeit braucht, Logs, Cronjobs, Updates, Backups und Inhaltspflege.
keywords: Pflege, Wartung, Betrieb, Zustand der Website, Cronjobs, Logs, Updates, Backups, Inhaltsprüfungen, Linkprüfung, Überwachung, Website-Ersteller, maintenance, operations, site health, cronjobs, logs, updates, backups, content checks, link check, monitoring, website creator
type: help
---
# Ihre Website pflegen

Diese Seite richtet sich an Website-Ersteller, die sich nach dem Start um eine Website kümmern. Die meiste Arbeit ist eine
Routine von Prüfungen; die Administration zeigt, was Aufmerksamkeit braucht. Das vollständige Betriebshandbuch sind
[Nach der Installation](../../../installation/10-nach-der-installation.md) und
[Eine Website betreiben](../../../anleitungen/betrieb-einer-website.md).

## Inhalt

1. [Die Routine](#die-routine)
2. [Wo Sie nachsehen](#wo-sie-nachsehen)
3. [Inhaltspflege](#inhaltspflege)
4. [Wenn etwas kaputtgeht](#wenn-etwas-kaputtgeht)

## Die Routine

| Wie oft | Aufgabe | Lesen |
|---|---|---|
| Täglich | einen Blick auf das Fehlerlog und fehlgeschlagene Cronjobs werfen | [Log-Viewer](../../../../eng-us/features/6.0/log-viewer.md), [Cronjobs](../../../anleitungen/cronjobs.md) |
| Wöchentlich | Backups sind gelaufen; Formulareinsendungen bearbeitet; defekte Links | [Backups und Wiederherstellungen](backups-und-wiederherstellungen.md), [Link- und Inhaltsprüfungen](link-und-inhaltspruefungen.md) |
| Monatlich | Updates von Exponential und Erweiterungen; ungenutzte Medien; Benutzerkonten | [Updates und Aktualisierungen](updates-und-aktualisierungen.md), [Pflege der Benutzerkonten](../../../../eng-us/features/6.0/user-hygiene.md) |
| Vierteljährlich | Wiederherstellungstest; erneute Prüfung der Barrierefreiheit; Überprüfung der Rollen | [Barrierefreiheit testen](barrierefreiheit-testen.md), [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) |
| Jährlich | Zertifikate und Domains; PHP-Version; Inhaltsüberprüfung mit den Verantwortlichen | [Livegang: Hosts und HTTPS](livegang-hosts-und-https.md), [Anforderungen](../../../installation/02-voraussetzungen.md) |

## Wo Sie nachsehen

| Seite | Zeigt |
|---|---|
| **Setup > Cronjobs** | welche Teile gelaufen sind, fehlgeschlagen sind oder Aufmerksamkeit brauchen |
| **Setup > Logs** | Fehler- und Warnungslogs mit einer Zusammenfassung |
| **Setup > Systeminformationen** | PHP, Datenbank, Erweiterungen, Versionen |
| **Setup > Upgrade-Prüfung** | Dateikonsistenz- und Datenbankprüfungen ([Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)) |
| **Setup > Caches** | Cache-Größen und Leeren |
| Inhaltsprüfungen | nicht referenzierte Medien, defekte Relationen, Speicherprobleme |
| **Shop**-Dashboard | Shop-Probleme, falls Sie einen betreiben |

## Inhaltspflege

- Veraltete Seiten entfernen oder archivieren; ihre Adressen weiterleiten
  ([Weiterleitungen und verschobene Inhalte](weiterleitungen-und-verschobene-inhalte.md)).
- Den Papierkorb nach seiner Prüffrist leeren ([Papierkorb](../../../../eng-us/features/6.0/trash-who-and-where.md)).
- Alte Versionen mit `exp:cleanupversions` aufräumen (lesen Sie zuerst `--help`).
- Ungenutzte Medien entfernen, die die Inhaltsprüfungen gefunden haben.

## Wenn etwas kaputtgeht

1. Sehen Sie zuerst ins Fehlerlog; Fehler tragen eine Referenz, nach der Sie suchen können
   ([Einen Fehler anhand seiner Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)).
2. Leeren Sie den kleinsten Cache, der das Problem erklärt.
3. Verwenden Sie den Wartungsmodus für Reparaturen, die Daten ändern ([Wartungsmodus](wartungsmodus.md)).
4. Melden Sie Fehler im Produkt ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)).

## Verwandte Seiten

- [Backups und Wiederherstellungen](backups-und-wiederherstellungen.md)
- [Updates und Aktualisierungen](updates-und-aktualisierungen.md)
- [Systeminformationen](../../../anleitungen/systeminformationen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
