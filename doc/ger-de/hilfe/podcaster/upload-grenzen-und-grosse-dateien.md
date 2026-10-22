---
title: Upload-Grenzen und große Dateien
audience: podcaster
category: podcasting-streaming
order: 40
summary: Welche Grenzen entscheiden, wie groß eine Episodendatei auf einer Exponential-Website sein kann - upload_max_filesize und post_max_size von PHP, die Höchstgröße des Medien- oder Dateifelds, MaxFileSize der Upload-Seite -, die Meldung, die jede auslöst, und was Sie mit zu großen Dateien tun.
keywords: Upload-Grenze, Dateigröße, große Dateien, upload_max_filesize, post_max_size, max_execution_time, MaxFileSize, ezmultiupload, maximale Dateigröße, MB, zu groß, Videodateien, Zeitüberschreitung, upload limit, file size, large files, max file size, too big, video files, timeout
type: help
---
# Upload-Grenzen und große Dateien

Diese Seite ist für Produzenten, deren Episoden groß sind, und für Administratoren, die die Grenzen festlegen. Mehrere
Grenzen gelten gleichzeitig; die kleinste gewinnt. Zu wissen, welche eine Datei abgelehnt hat, erspart viel Raten.

## Inhalt

1. [Die Grenzen](#die-grenzen)
2. [Welche Meldung welche Grenze bedeutet](#welche-meldung-welche-grenze-bedeutet)
3. [Die Grenzen wählen](#die-grenzen-wählen)
4. [Wenn eine Datei zu groß ist](#wenn-eine-datei-zu-groß-ist)
5. [Checkliste](#checkliste)

## Die Grenzen

| Grenze | Wo | Mitgelieferter oder empfohlener Wert | Gilt für |
|---|---|---|---|
| `upload_max_filesize` | PHP (`php.ini` oder der FPM-Pool) | `64M` im empfohlenen Produktionssatz | jeden Upload |
| `post_max_size` | PHP | `64M` im empfohlenen Produktionssatz; muss mindestens so groß sein wie der Upload | jeden Upload |
| **Maximale Dateigröße** des Klassenattributs | **Setup > Klassen**, Medien- oder Dateifeld, in MB | 0: keine eigene Grenze | dieses Feld |
| `MaxFileSize` | `extension/ezmultiupload/settings/ezmultiupload.ini [MultiUploadSettings]`, auf Installationen mit der Upload-Seite | `64M` | die Upload-Seite |
| `max_execution_time`, `request_terminate_timeout` | PHP, der FPM-Pool | `60` empfohlen; bei FPM aus, sofern nicht gesetzt | die Zeit, die eine Anfrage dauern darf |

Die PHP-Werte stammen aus [PHP-Einstellungen für Exponential](../hosting/grundlagen/php-einstellungen-fuer-exponential.md#ein-produktionssatz-für-einen-pool).
Der eigene Kommentar der Upload-Seite sagt es deutlich: "PHP's upload_max_filesize and post_max_size apply as well: the
smallest of the three is the limit", also: Auch upload_max_filesize und post_max_size von PHP gelten; die kleinste der
drei ist die Grenze.

Die Upload-Seite zeigt ihre Grenze an, zum Beispiel *At most 64 MB per file*, also höchstens 64 MB pro Datei
([Bilder, Medien und Uploads](../medien-und-uploads.md#dateien-hochladen)).

## Welche Meldung welche Grenze bedeutet

| Meldung oder Anzeichen | Grenze | Quelle |
|---|---|---|
| "The size of the uploaded file exceeds the limit set by upload_max_filesize directive in php.ini. Please contact the site administrator." | PHP `upload_max_filesize` | `kernel/classes/datatypes/ezmedia/ezmediatype.php` |
| "The size of the uploaded file exceeds the limit set by the upload_max_filesize directive in php.ini.", bei einem Dateifeld | PHP `upload_max_filesize` | `kernel/classes/datatypes/ezbinaryfile/ezbinaryfiletype.php` |
| "The size of the uploaded file exceeds site maximum: %1 bytes." (Medienfeld) oder "... exceeds the maximum upload size: %1 bytes." (Dateifeld) | die **Maximale Dateigröße** des Klassenattributs | dieselben Dateien |
| Die Upload-Seite markiert die Zeile vor dem Senden als "too big" | `MaxFileSize` der Upload-Seite | `ezmultiupload` |
| Das ganze Formular kommt leer zurück, als wäre nichts gesendet worden | `post_max_size` überschritten: PHP verwirft die ganze Anfrage | PHP |
| Der Upload läuft, dann schlägt die Seite nach langem Warten fehl | ein Zeitlimit von PHP, FPM oder dem Webserver | der Server |

Die Meldungen sind hier im englischen Wortlaut der Quelle wiedergegeben; die deutsche Administration zeigt sie in ihrer
Übersetzung.

## Die Grenzen wählen

- Nehmen Sie Ihre größte erwartete Datei: Ein 90-minütiges Video kann mehrere Gigabyte haben, ein zweistündiges MP3 mit
  128 kbit/s etwa 115 MB.
- Setzen Sie `upload_max_filesize` darüber und `post_max_size` etwas darüber.
- Heben Sie `MaxFileSize` der Upload-Seite entsprechend an, in einem Override von `ezmultiupload.ini`.
- Verwenden Sie die **Maximale Dateigröße** des Klassenattributs, um Dateien pro Klasse im Rahmen zu halten, zum
  Beispiel 200 MB für Audioepisoden, sodass eine falsche Datei (ein unkomprimiertes WAV) sofort abgelehnt wird.
- Prüfen Sie Zeitlimits für langsame Verbindungen: Ein Upload von 500 MB mit 10 Mbit/s dauert etwa sieben Minuten.
- PHP braucht auch Speicherplatz für den temporären Upload; das temporäre Verzeichnis muss die größte Datei aufnehmen
  können ([Aufbau des Speichers](../hosting/betrieb/aufbau-des-speichers.md)).

## Wenn eine Datei zu groß ist

1. **Machen Sie sie kleiner**: Exportieren Sie mit der Bitrate, die Hörer brauchen. Sprache kommt meist mit einer
   niedrigeren Bitrate aus als Musik; Exponential kodiert nicht neu, die hochgeladene Datei ist also das, was jeder
   Hörer herunterlädt.
2. **Teilen Sie sie**: Eine sehr lange Aufnahme kann zwei Episoden werden.
3. **Heben Sie die Grenze an**: Fragen Sie den Administrator, mit der Größe, die Sie brauchen
   ([Mit Ihrem Administrator arbeiten](mit-ihrem-administrator-arbeiten.md)).
4. **Hosten Sie sie anderswo**: Verwenden Sie für sehr große Videos einen Videodienst und betten Sie sie ein
   ([Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)). Das Enclosure eines Podcast-Feeds muss eine
   Datei auf Ihrer Website sein; für Audiopodcasts ist das daher ohne Änderungen keine Möglichkeit
   ([CDN und externes Medienhosting](cdn-und-externes-medienhosting.md)).

## Checkliste

- [ ] `upload_max_filesize` und `post_max_size` übersteigen die größte Episode.
- [ ] `MaxFileSize` der Upload-Seite passt dazu.
- [ ] Jedes Medienfeld hat eine sinnvolle **Maximale Dateigröße**.
- [ ] Die Zeitlimits erlauben den langsamsten erwarteten Upload.
- [ ] Der Speicherplatz reicht für die temporäre Kopie und die gespeicherte Datei.

## Verwandte Seiten

- [Audio hochladen](audio-hochladen.md)
- [PHP-Einstellungen für Exponential](../hosting/grundlagen/php-einstellungen-fuer-exponential.md)
- [Dateien und Downloads](../ersteller/website-ersteller/dateien-und-downloads.md#große-dateien)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
