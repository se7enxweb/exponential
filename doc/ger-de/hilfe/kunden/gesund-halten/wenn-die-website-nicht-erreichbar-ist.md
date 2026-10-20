---
title: Wenn die Website nicht erreichbar ist
audience: customer, admin, businesses
category: customer-success
order: 21
summary: Was der Betreiber in den ersten Minuten tut, wenn eine Exponential-Website nicht antwortet oder eine Fehlerseite zeigt - sehen, was Besucher sehen, die richtige Person anrufen, entscheiden, Kunden informieren und alles festhalten - mit der Bedeutung jeder Fehlerseite.
keywords: Website nicht erreichbar, Ausfall, Fehlerseite, Vorfall, 503, 500, Something went wrong, Wartung, Hoster anrufen, erste Schritte, site down, outage, error page, incident, maintenance, call the host, first steps
type: help
---
# Wenn die Website nicht erreichbar ist

Bewahren Sie diese Seite dort auf, wo Sie sie ohne die Website finden: Drucken Sie sie aus, oder legen Sie sie zu Ihren
[Eskalationskontakten](../partner-und-hoster/vorlage-eskalationskontakte.md). Sie ist der Teil des Betreibers an einem
schlechten Tag. Die technische Arbeit erledigt Ihr Hoster oder Partner nach Runbooks wie
[Website nicht erreichbar](../../hosting/runbooks/website-nicht-erreichbar.md); der Plan für ein kleines Team ist
[Vorfallreaktion für kleine Teams](../../../../en/us/help/businesses/reliability/incident-response-for-small-teams.md).

## Die ersten fünf Minuten

1. **Notieren Sie die Uhrzeit**, zu der Sie es bemerkt haben, und wie (ein Kunde, eine Überwachung, Sie selbst).
2. **Sehen Sie es selbst**, von zwei Orten aus: Ihrem Computer und einem Smartphone über mobile Daten. Notieren Sie genau,
   was die Seite anzeigt.
3. **Wurde etwas geändert?** Ein Upgrade, ein Deployment, eine Einstellungsänderung oder ein großer Import heute?
   „Fehler, die direkt nach einem Deployment oder einem Update begonnen haben: Die Ursache liegt fast immer in dieser
   Änderung“ ([Website nicht erreichbar](../../hosting/runbooks/website-nicht-erreichbar.md)).
4. **Rufen Sie die richtige Person an**, mit dem, was Sie gesehen haben ([Wann Sie wen anrufen](../partner-und-hoster/wann-sie-wen-anrufen.md)).
5. **Benennen Sie eine leitende Person.** Eine Person entscheidet; die anderen ändern nicht parallel etwas.

## Was die Seite Ihnen sagt

Aus [Website nicht erreichbar](../../hosting/runbooks/website-nicht-erreichbar.md):

| Besucher sehen | Wahrscheinliche Ursache | Wen Sie anrufen |
|---|---|---|
| „Down for maintenance“ (503) | Der Wartungsmodus ist eingeschaltet | Wer die geplanten Arbeiten begonnen hat; wenn niemand, Ihren Hoster |
| „We'll be right back“ (503) | Die Datenbank war nicht erreichbar | Hoster |
| „The site is missing the software libraries it needs“ (503) | Ein Teil der Installation fehlt | Hoster oder Partner, wer zuletzt ein Deployment gemacht hat |
| „Something went wrong on our side“ (500) mit `ERR-...` | Ein Fehler in der Anwendung | Partner oder Hoster; geben Sie die Referenz weiter |
| Eine leere Seite, oder ein Fehler ohne Referenz | PHP ist fehlgeschlagen, bevor Exponential protokollieren konnte | Hoster |
| Eine Verbindungs- oder Zertifikatswarnung im Browser | Server, Netzwerk oder Zertifikat | Hoster ([Ablaufendes Zertifikat](../../hosting/runbooks/ablauf-von-zertifikaten.md)) |

Wenn nur eine Seite fehlschlägt, ist es kein Ausfall; melden Sie es auf normalem Weg ([Probleme melden](probleme-melden.md)).

## Entscheiden

Drei Möglichkeiten, entschieden von der leitenden Person zusammen mit der Person, die behebt
([Vorfallreaktion für kleine Teams](../../../../en/us/help/businesses/reliability/incident-response-for-small-teams.md)):

- **Eine schnelle Korrektur**, wenn die Ursache bekannt und klein ist.
- **Die letzte Änderung zurücknehmen**, wenn das Problem mit ihr begann.
- **Eine Wartungsseite, während gearbeitet wird**, wenn Kunden auf Fehler stoßen und die Korrektur länger dauert. Eine
  ehrliche Nachricht wie „We are fixing a problem. Back shortly.“ ist besser als Fehler
  ([Wartungsmodus und geplante Arbeiten](wartungsmodus-und-geplante-arbeiten.md)).

Eine Wiederherstellung aus dem Backup ist der letzte Ausweg, weil alles seit dem Backup verloren geht ([Backups](backups.md)).

## Menschen informieren

- **Früh und ehrlich**, mit der Uhrzeit des nächsten Updates, und halten Sie diese ein
  ([Vorfallreaktion für kleine Teams](../../../../en/us/help/businesses/reliability/incident-response-for-small-teams.md)).
- **Nicht nur auf der kaputten Website**: Verwenden Sie die vereinbarten Kanäle (E-Mail, soziale Medien, Ansage am Telefon).
- **Dieselbe Nachricht** für alle, die Kunden antworten.

## Wenn es ein Einbruch sein könnte

Unbekannte Administratorkonten, geänderte Dateien oder Seiten, die Sie nicht veröffentlicht haben, ändern die Priorität
von „schnell wieder online“ zu „eindämmen und Beweise sichern“. Sagen Sie Ihrem Hoster, dass es sich um einen
vermuteten Einbruch handelt; löschen Sie selbst nichts ([Kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md),
[Vertrauliche Sicherheitsmeldungen](vertrauliche-sicherheitsmeldungen.md)).

## Danach

- [ ] Füllen Sie die [Vorfallnotizen](vorlage-vorfallnotizen.md) aus, solange Sie sich erinnern.
- [ ] Innerhalb einer Woche eine kurze Auswertung ohne Schuldzuweisung: Wie haben wir es erfahren, was hat uns
      aufgehalten, was hätte es verhindert, was ändern wir ([Vorfallreaktion für kleine Teams](../../../../en/us/help/businesses/reliability/incident-response-for-small-teams.md)).
- [ ] Bitten Sie Ihren Hoster oder Partner um seine schriftliche Darstellung von Ursache und Korrektur.

## Verwandte Seiten

- [Überwachung](ueberwachung.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Runbooks für Hoster](../../hosting/runbooks/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
