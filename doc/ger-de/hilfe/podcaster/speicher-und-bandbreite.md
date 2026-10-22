---
title: Speicher und Bandbreite
audience: podcaster
category: podcasting-streaming
order: 39
summary: Was das Hosting eines Podcasts oder einer Videosendung auf einer Exponential-Website an Speicherplatz und Datenübertragung kostet - wo Mediendateien gespeichert werden, wie alte Versionen Dateien behalten, wie jeder Download mit Range-Unterstützung durch PHP läuft, wie Sie den Datenverkehr schätzen und was Sie mit Ihrem Hoster über Sicherungen und Grenzen vereinbaren.
keywords: Speicher, Speicherplatz, Bandbreite, Datenverkehr, Datenübertragung, Hosting, var storage, original/audio, Versionen, DefaultVersionHistoryLimit, PHP-Passthrough, Range, Sicherungen, Cluster, Schätzung, storage, disk space, bandwidth, traffic, data transfer, versions, backups, cluster, estimate
type: help
---
# Speicher und Bandbreite

Diese Seite ist für Produzenten und Website-Betreiber, die das Hosting einer Sendung planen, und für die Hoster, mit
denen sie sprechen. Audio und Video sind die größten Dateien, die eine Website trägt, und ein beliebter Feed bringt
Podcast-Apps dazu, jede neue Episode viele Male herunterzuladen. Planen Sie Speicherplatz und Datenverkehr vor der
ersten Episode.

## Inhalt

1. [Wo Dateien gespeichert werden](#wo-dateien-gespeichert-werden)
2. [Versionen behalten Dateien](#versionen-behalten-dateien)
3. [Wie Downloads ausgeliefert werden](#wie-downloads-ausgeliefert-werden)
4. [Speicherplatz und Datenverkehr schätzen](#speicherplatz-und-datenverkehr-schätzen)
5. [Sicherungen](#sicherungen)
6. [Was Sie mit Ihrem Hoster vereinbaren](#was-sie-mit-ihrem-hoster-vereinbaren)

## Wo Dateien gespeichert werden

Medien- und Dateifelder speichern Uploads unterhalb des Speicherverzeichnisses der Website in `original/<mime group>`:
Audio in `original/audio`, Video in `original/video` (`kernel/classes/datatypes/ezmedia/ezmediatype.php`). Das
Speicherverzeichnis ist `var/storage/` oder `var/<site>/storage/`, je nach den Einstellungen der Installation
([Aufbau des Speichers](../hosting/betrieb/aufbau-des-speichers.md#das-verzeichnis-var)). In einem Cluster mit dem
DFS-Dateihandler liegen die Dateien stattdessen auf dem gemeinsamen Speicher
([Clustering und gemeinsamer Speicher](../hosting/betrieb/clustering-und-gemeinsamer-speicher.md)).

## Versionen behalten Dateien

Jede Bearbeitung eines Objekts erzeugt eine neue Version, und ältere Versionen werden bis zu `settings/content.ini
[VersionManagement] DefaultVersionHistoryLimit` aufbewahrt (im Auslieferungszustand 45; `VersionHistoryClass[]` kann
das pro Klasse festlegen). Wenn Sie das Audio einer Episode ersetzen, bleibt die alte Datei auf der Festplatte, solange
eine alte Version sie noch nennt; der Mediendatentyp löscht eine Datei erst, wenn keine Version oder Kopie mehr auf sie
verweist (`deleteStoredObjectAttribute()`).

Rechnen Sie das bei einer Sendung, die Audio oft ersetzt, mit ein, oder bitten Sie den Administrator um eine niedrigere
Versionsgrenze für die Episodenklasse. `bin/php/cleanupversions.php` entfernt Versionen jenseits einer gesenkten Grenze
(Kommentar in `settings/content.ini`).

## Wie Downloads ausgeliefert werden

Player und Podcast-Apps holen Dateien von `/content/download/...`. Der Kernel prüft, ob der Besucher das Objekt lesen
darf, dann sendet der PHP-Passthrough-Handler die Datei
(`kernel/classes/binaryhandlers/ezfilepassthrough/ezfilepassthroughhandler.php`):

- Jeder Download belegt einen PHP-Worker, solange die Übertragung läuft; ein langsamer Hörer mit einer großen Datei
  hält ihn beschäftigt;
- `Range`-Anfragen werden mit `206 Partial Content` beantwortet, sodass Player springen und Apps fortsetzen, statt von
  vorn zu beginnen;
- die Antwort trägt ein `Expires` zehn Minuten in der Zukunft und ein leeres `Cache-Control`
  (`eZFile::downloadHeaders()` in `lib/ezfile/classes/ezfile.php`), sodass Browser und Proxys sie nur kurz behalten
  dürfen.

Die Zahl der PHP-Worker begrenzt daher, wie viele Downloads gleichzeitig laufen; bemessen Sie den Pool entsprechend
([PHP-FPM abstimmen](../hosting/leistung/abstimmung-von-php-fpm.md)).

## Speicherplatz und Datenverkehr schätzen

| Größe | Wie Sie sie schätzen |
|---|---|
| Speicherplatz pro Episode | Dateigröße: Bitrate (kbit/s) x Länge (s) / 8. Ein 60-minütiges MP3 mit 128 kbit/s hat etwa 57,6 MB |
| Speicherplatz pro Jahr | Episoden pro Jahr x Dateigröße, plus in Versionen behaltene ersetzte Dateien, plus Sicherungen |
| Datenverkehr pro Episode | Dateigröße x Downloads; viele Apps laden neue Episoden für jeden Abonnenten automatisch herunter |
| Spitze | die Stunden nach einer Veröffentlichung, wenn Apps die neue Episode abrufen |

Beispiel: 52 Episoden mit 57,6 MB sind etwa 3 GB im Jahr. Mit je 2.000 automatischen Downloads beträgt der
Episodenverkehr des Jahres etwa 6 TB, das meiste davon in den ersten Tagen nach jeder Veröffentlichung. Das sind Zahlen,
die Sie durch Ihre eigenen ersetzen; messen Sie echte Downloads in den Logs Ihres Webservers
([Downloads und Statistiken](downloads-und-statistiken.md)).

## Sicherungen

Mediendateien gehören zur Dateihälfte einer Sicherung: Das Speicherverzeichnis muss zusammen mit der Datenbank und zur
selben Zeit gesichert werden ([Sicherungen: was gesichert werden muss](../hosting/betrieb/backups-was-gesichert-werden-muss.md)).
Das Archiv einer Sendung ist oft der größte Teil der Sicherung einer Website; vereinbaren Sie die Aufbewahrung und
testen Sie die Wiederherstellung einer Episode.

## Was Sie mit Ihrem Hoster vereinbaren

- [ ] Speicherplatz für die Episoden, Versionen und Sicherungen des nächsten Jahres.
- [ ] Enthaltene Datenübertragung und den Preis darüber hinaus.
- [ ] Upload-Grenzen für Redakteure ([Upload-Grenzen und große Dateien](upload-grenzen-und-grosse-dateien.md)).
- [ ] Die Zahl der PHP-Worker und die Zeitlimits für lange Downloads.
- [ ] Ob ein CDN angeboten wird und was es cachen darf ([CDN und externes Medienhosting](cdn-und-externes-medienhosting.md)).
- [ ] Häufigkeit und Aufbewahrungsdauer der Sicherungen des Speicherverzeichnisses.

## Verwandte Seiten

- [Arbeitsspeicher, CPU und Festplatte bemessen](../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md)
- [Hosting für eine Website](../ersteller/website-ersteller/hosting-fuer-eine-website.md)
- [Festplatte voll](../hosting/runbooks/festplatte-voll.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
