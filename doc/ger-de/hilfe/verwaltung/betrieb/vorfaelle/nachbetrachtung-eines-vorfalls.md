---
title: Nachbetrachtung eines Vorfalls
audience: government, admin, project-manager
category: public-sector
order: 150
summary: Eine Nachbetrachtung ohne Schuldzuweisung nach einem Vorfall auf einer öffentlichen Website durchführen - den Zeitablauf aus Prüfprotokoll und Logs rekonstruieren, Ursachen und begünstigende Faktoren finden, Maßnahmen vereinbaren und die Dokumentation erstellen, die Ihre Organisation und die Prüfenden erwarten.
keywords: Nachbetrachtung, Postmortem, Lessons Learned, ohne Schuldzuweisung, Zeitablauf, Grundursache, begünstigende Faktoren, Maßnahmen, Prüfprotokoll, Beweise, kontinuierliche Verbesserung, post-incident review, postmortem, lessons learned, blameless, timeline, root cause, contributing factors, actions, audit trail, evidence, continuous improvement
type: help
---
# Nachbetrachtung eines Vorfalls

Diese Seite richtet sich an Vorfallsleitungen und die Verantwortlichen für den Dienst. Eine Nachbetrachtung macht aus
einem Vorfall Verbesserungen. Viele öffentliche Stellen verlangen sie bei schweren Vorfällen; auch wo sie nicht
vorgeschrieben ist, ist sie der günstigste Weg, eine Wiederholung zu vermeiden.

## Inhalt
1. [Wann Sie eine abhalten](#wann-sie-eine-abhalten)
2. [Den Zeitablauf rekonstruieren](#den-zeitablauf-rekonstruieren)
3. [Ursachen und begünstigende Faktoren](#ursachen-und-begünstigende-faktoren)
4. [Maßnahmen](#maßnahmen)
5. [Die Dokumentation](#die-dokumentation)
6. [Verwandte Seiten](#verwandte-seiten)

## Wann Sie eine abhalten

Nach jedem Vorfall mit Schweregrad 1, nach jedem Vorfall mit personenbezogenen Daten oder einer Sicherheitsverletzung
und nach einem Beinahe-Vorfall, der schwer hätte ausfallen können. Innerhalb weniger Arbeitstage, solange die
Erinnerung frisch ist. Ohne Schuldzuweisung: Die Frage ist, warum System und Prozess es zuließen, nicht, wer schuld ist.

## Den Zeitablauf rekonstruieren

| Quelle | Liefert |
|---|---|
| Notizen zum Vorfall | was Personen gesehen und getan haben, mit Uhrzeiten |
| Prüfprotokoll | Anmeldungen, Inhaltsänderungen, geschriebene Einstellungen, geleerte Caches, Bereitstellungen, Wartungsmodus an und aus, mit Uhrzeiten und Benutzern |
| `error.log`, `security.log`, `404.log` | wann Fehler und Angriffe begannen und endeten |
| Überwachung | wann die Prüfung von außen fehlschlug und wieder erfolgreich war |
| Logs von Webserver und Velocity | Anfragemengen und Statuscodes |

```bash
./console exp:audit search --from='<start>' --to='<end>' --limit=500 --allow-root-user
./console exp:logs --search=<keyword> --scope=all --from='<start>' --to='<end>'
```

Exportieren Sie die Audit-Einträge für die Akte ([Audit-Einträge für eine Prüfung exportieren](../../aufgaben/betrieb/audit-eintraege-fuer-einen-pruefer-exportieren.md)).

## Ursachen und begünstigende Faktoren

Fragen Sie jeweils: Was ist passiert, warum war es möglich, warum wurde es nicht früher bemerkt, warum hat die
Wiederherstellung so lange gedauert? Typische Befunde auf Exponential-Websites: ein Cronjob-Teil, der unbemerkt
pausierte, eine Änderung, die ohne Staging bereitgestellt wurde, eine Rolle, die weiter gefasst war als nötig, ein
abgelaufenes Zertifikat ohne Verantwortliche, Logs, die vor der Untersuchung bereinigt wurden, Datensicherungen, deren
Wiederherstellung nie getestet wurde.

## Maßnahmen

Jede Maßnahme hat eine verantwortliche Person und ein Datum und ist klein genug, um abgeschlossen zu werden. Beispiele
mit Verweisen in diesen Bereich:

| Befund | Maßnahme |
|---|---|
| Stundenlang hat es niemand bemerkt | eine Prüfung von außen ergänzen ([Externe Verfügbarkeitsüberwachung](../ueberwachung/externe-verfuegbarkeitsueberwachung.md)) |
| Die Wiederherstellung dauerte zu lange | Wiederherstellungstests einplanen ([Wiederherstellungstests](../datensicherung-und-wiederherstellung/wiederherstellungstests.md)) |
| Zu viele Personen mit vollem Zugriff | Überprüfung der Zugänge ([Überprüfung administrativer Zugänge](../personen-und-berechtigungen/ueberpruefung-administrativer-zugaenge.md)) |
| Eine Änderung hat die Produktion beschädigt | Staging-Schritt ([Staging und Freigabe](../aktualisierungen-und-aenderungen/staging-und-freigabe.md)) |
| Beweise fehlen | längere Aufbewahrung ([Aufbewahrung und Rotation von Logs](../ueberwachung/aufbewahrung-und-rotation-von-protokollen.md)) |

## Die Dokumentation

Zusammenfassung, Auswirkungen (wer konnte was wie lange nicht tun), Zeitablauf, Ursachen, Maßnahmen mit
Verantwortlichen und was gut gelaufen ist. Teilen Sie sie mit dem Änderungsbeirat und dem Sicherheitsteam;
veröffentlichen Sie eine kurze öffentliche Zusammenfassung, wenn Ihre Organisation das bei Störungen ihrer Dienste tut.

## Verwandte Seiten
- [Grundlagen der Vorfallsbehandlung](grundlagen-der-vorfallsbehandlung.md)
- [Meldung von Datenschutzverletzungen](meldung-von-datenschutzverletzungen.md)
- [Notfallwiederherstellungsplan](../datensicherung-und-wiederherstellung/notfallwiederherstellungsplan.md)
- [Hosting-Runbooks](../../../hosting/runbooks/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
