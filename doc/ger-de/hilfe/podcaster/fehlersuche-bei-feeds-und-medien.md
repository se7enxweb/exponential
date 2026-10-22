---
title: Fehlersuche bei Feeds und Medien
audience: podcaster
category: podcasting-streaming
order: 44
summary: Anzeichen, Ursachen und Lösungen für die Probleme, auf die Sendungen auf einer Exponential-Website am häufigsten stoßen - leere Podcast-Feeds, falsche Enclosure-Typen, fehlende Dauer, Feeds, die auf den falschen Host zeigen, Änderungen, die nicht erscheinen, Audio, das heruntergeladen statt abgespielt wird, abgelehnte Uploads, tote Einbettungen und geplante Episoden, die nie erscheinen.
keywords: Fehlersuche, Probleme, leerer Feed, keine Episoden, Enclosure-Typ, application/octet-stream, Dauer, falscher Host, Cache, Download statt Wiedergabe, Upload zu groß, Einbettung blockiert, geplante Episode fehlt, Cronjob, troubleshooting, problems, empty feed, no episodes, enclosure type, duration, wrong host, cache, download instead of play, upload too big, embed blocked, scheduled episode missing, cronjob
type: help
---
# Fehlersuche bei Feeds und Medien

Diese Seite ist für Produzenten und Administratoren, wenn bei einer Sendung etwas nicht funktioniert. Suchen Sie das
Anzeichen, prüfen Sie die wahrscheinliche Ursache, wenden Sie die Lösung an. Die meisten Lösungen stehen auf der in der
Zeile verlinkten Seite.

## Inhalt

1. [Der Podcast-Feed](#der-podcast-feed)
2. [Player und Dateien](#player-und-dateien)
3. [Uploads](#uploads)
4. [Einbettungen und Streams](#einbettungen-und-streams)
5. [Zeitplanung und Freigabe](#zeitplanung-und-freigabe)
6. [Bevor Sie um Hilfe bitten](#bevor-sie-um-hilfe-bitten)

## Der Podcast-Feed

| Anzeichen | Wahrscheinliche Ursache | Lösung |
|---|---|---|
| Der Feed hat einen Kanal, aber keine Elemente | **Anhang (Medien)** nicht zugeordnet oder einem Feld zugeordnet, das kein Medien- oder Dateifeld ist | das Audiofeld zuordnen ([Referenz der Episodenzuordnung](referenz-der-episodenzuordnung.md)) |
| Einige Episoden fehlen | ihr Audiofeld ist leer; sie liegen in Staffelordnern und **Unterknoten** ist aus; sie sind versteckt; sie liegen jenseits der **Anzahl der Objekte** | die Datei hochladen, **Unterknoten** ankreuzen, wieder zeigen, die Zahl erhöhen ([Wie eine Sendung zu Inhalten wird](wie-eine-sendung-zu-inhalten-wird.md)) |
| Gar keine Episode, die Quelle zeigt, dass sie fehlt | der Quellordner wurde entfernt oder in den Papierkorb verschoben | den Ordner der Sendung erneut wählen ([Ihren Feed prüfen](ihren-feed-pruefen.md)) |
| `type="application/octet-stream"` bei einem Enclosure | die Endung der Datei (m4a, aac, opus) steht nicht in `settings/mime.ini` | die Endung in einem Override ergänzen, dann erneut hochladen ([Mit Ihrem Administrator arbeiten](mit-ihrem-administrator-arbeiten.md)) |
| Kein `itunes:duration` | die Datei ist kein MP3 mit konstanter Bitrate | CBR-MP3 exportieren oder es hinnehmen: das Element ist optional |
| Links und Enclosures zeigen auf den Host der Administration oder auf `localhost` | **URL der Website** falsch gesetzt, oder der Siteaccess hat keine öffentliche Adresse | **URL der Website** leeren oder korrigieren; **Links zeigen auf** auf der RSS-Karte prüfen |
| Links verwenden `http://` hinter einem Proxy oder CDN | der Proxy ist nicht vertrauenswürdig eingetragen, daher hält die Website die Anfrage für einfaches HTTP | **URL der Website** auf `https://...` setzen und den Hoster vertrauenswürdige Proxys konfigurieren lassen ([CDN](../hosting/leistung/cdn.md#dem-cdn-vertrauen)) |
| Eine Änderung erscheint nicht | der Feed-Cache (`CacheTime`, 1200 Sekunden) | warten oder `rss_cache` leeren ([Ihren Feed prüfen](ihren-feed-pruefen.md#wenn-eine-änderung-nicht-erscheint)) |
| Kategorie oder Cover ist nach dem Speichern leer | der Wert wurde von den Prüfungen verworfen: eine Kategorie, die Apple nicht aufführt, eine Adresse mit einem anderen Schema | aus der Liste wählen; eine vollständige `https://`-Adresse eingeben ([Referenz der Kanalfelder](referenz-der-kanalfelder.md)) |
| Die Feed-Adresse antwortet mit einem Fehler | der Export ist inaktiv oder hat keine Zugriffs-URL | **Aktiv** ankreuzen, die Zugriffs-URL setzen |
| Apps zeigen eine Episode doppelt | sie wurde gelöscht und neu angelegt, daher hat sich ihre `guid` geändert | das Duplikat verstecken; künftig bearbeiten statt neu anlegen |
| Der Autor eines Elements ist ein technisches Konto | `itunes:author` eines Elements ist der anlegende Benutzer | Episoden mit persönlichen Konten anlegen ([Gäste und Mitwirkende](gaeste-und-mitwirkende.md)) |

## Player und Dateien

| Anzeichen | Wahrscheinliche Ursache | Lösung |
|---|---|---|
| Die Seite zeigt keinen Player oder einen leeren Kasten | das Medienfeld verwendet einen alten Plugin-Playertyp | den Playertyp des Klassenattributs auf Html5 Audio oder Html5 Video setzen ([Das Medienfeld und das Dateifeld](medienfeld-und-dateifeld.md)) |
| Ein Link auf das Audio lädt es herunter, statt es abzuspielen | `file.ini [PassThroughSettings]` sendet Audio als `attachment` (der mitgelieferte Standard) | `ContentDisposition[audio/mpeg]=inline` ergänzen ([Mit Ihrem Administrator arbeiten](mit-ihrem-administrator-arbeiten.md)) |
| Springen funktioniert nicht | ein vorgeschalteter Proxy oder ein CDN reicht `Range`-Anfragen nicht durch | den Hoster prüfen lassen; die Website selbst antwortet mit `206` ([Ihren Feed prüfen](ihren-feed-pruefen.md#auf-der-kommandozeile)) |
| Die Datei spielt für Redakteure, nicht aber für Besucher | das Objekt liegt in einer Sektion, die Besucher nicht lesen dürfen | in eine öffentliche Sektion verschieben, oder es ist so gewollt ([Episoden nur für Mitglieder](episoden-nur-fuer-mitglieder.md)) |
| Das MP3 wurde zu einem Objekt *File* ohne Player | `upload.ini` ordnet `audio/mpeg` keine Klasse zu | Episoden in der Episodenklasse anlegen oder den Typ zuordnen ([Eine Klasse für Episoden wählen](eine-klasse-fuer-episoden-waehlen.md)) |

## Uploads

| Anzeichen | Wahrscheinliche Ursache | Lösung |
|---|---|---|
| "exceeds the limit set by upload_max_filesize" | Grenze von PHP | anheben oder die Datei verkleinern ([Upload-Grenzen und große Dateien](upload-grenzen-und-grosse-dateien.md)) |
| "exceeds site maximum" oder "exceeds the maximum upload size" | die **Maximale Dateigröße** des Klassenattributs | in der Klasse anheben oder die Datei verkleinern |
| Das Formular kommt leer zurück | `post_max_size` überschritten | über die Dateigröße anheben |
| Die Upload-Seite lehnt die Datei vor dem Senden ab | ihr `MaxFileSize` | in einem Override von `ezmultiupload.ini` anheben |
| Der Upload schlägt nach langer Zeit fehl | Zeitlimits von PHP, FPM oder dem Webserver | für große Uploads anheben |

## Einbettungen und Streams

| Anzeichen | Wahrscheinliche Ursache | Lösung |
|---|---|---|
| "No video configured." | falsche ID oder eine abgelehnte Adresse (nicht http oder https, oder Ihre eigene Website) | die ID prüfen oder die Adresse des Players einfügen ([Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)) |
| Der Rahmen bleibt nach **Load video** leer | eine durchgesetzte Content Security Policy erlaubt den Host nicht | ihn mit `AdditionalSources[]=frame-src ...` ergänzen ([Datenschutz und Player von Drittanbietern](datenschutz-und-player-von-drittanbietern.md#content-security-policy)) |
| In formatierten Text eingefügter Einbettungscode verschwindet | formatierter Text kann keine iframes speichern | den Block External video verwenden ([Medien im formatierten Text](medien-im-formatierten-text.md)) |

## Zeitplanung und Freigabe

| Anzeichen | Wahrscheinliche Ursache | Lösung |
|---|---|---|
| Eine geplante Episode erscheint nie | der Cronjob-Teil `frequent` führt `workflow.php` nicht aus | der Administrator plant ihn ein; die Seite der Cronjobs zeigt den Zustand jedes Teils ([Eine Veröffentlichung planen](eine-veroeffentlichung-planen.md)) |
| Eine geplante Episode erschien sofort | das Datumsfeld war leer oder in der Vergangenheit, oder der Workflow hängt nicht am Trigger | das Feld und **Setup > Trigger** prüfen |
| Eine freigegebene Episode wird nicht veröffentlicht | der Workflow-Cronjob ist seit der Freigabe nicht gelaufen | darauf warten oder ihn laufen lassen ([Freigaben vor der Veröffentlichung](freigaben-vor-der-veroeffentlichung.md)) |
| Eine versteckte oder entfernte Episode steht noch im Feed | der Feed-Cache | bis zu `CacheTime` warten oder `rss_cache` leeren |

## Bevor Sie um Hilfe bitten

Notieren Sie: die Feed-Adresse, die Adresse der Episode, was Sie erwartet haben, was geschehen ist, die Uhrzeit und die
genaue Meldung. Der Administrator kann den Fehler dann in den Logs nachschlagen
([Einen Fehler anhand seiner Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md),
[Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)).

## Verwandte Seiten

- [Ihren Feed prüfen](ihren-feed-pruefen.md)
- [Was nicht eingebaut ist](was-nicht-eingebaut-ist.md)
- [FAQ für Podcaster](faq-fuer-podcaster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
