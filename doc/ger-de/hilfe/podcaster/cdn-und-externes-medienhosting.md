---
title: CDN und externes Medienhosting
audience: podcaster
category: podcasting-streaming
order: 41
summary: Was ein Content Delivery Network für eine Sendung auf einer Exponential-Website leisten kann und was nicht, warum Episodendownloads keine einfachen statischen Dateien sind, was Exponential nicht bietet, um Medien in einen Objektspeicher oder zu einem Podcast-Hoster auszulagern, und welche Möglichkeiten bleiben.
keywords: CDN, Content Delivery Network, Edge-Cache, Medienhosting, Podcast-Hoster, Objektspeicher, S3, Auslagern, content/download, Rechteprüfung, Cache-Control, Expires, statische Dateien, externes Hosting, content delivery network, edge cache, media hosting, podcast host, object storage, offload, permission check, static files, external hosting
type: help
---
# CDN und externes Medienhosting

Diese Seite ist für Produzenten und Website-Betreiber, deren Sendung der Bandbreite eines Servers entwächst, und für
die Hoster, mit denen sie arbeiten. Sie erklärt, was ein CDN einer Exponential-Website bringt, warum Episodendateien
ein Sonderfall sind und was für das Hosting von Medien an einem anderen Ort verfügbar ist und was nicht.

## Inhalt

1. [Was ein CDN gut kann](#was-ein-cdn-gut-kann)
2. [Warum Episodendateien anders sind](#warum-episodendateien-anders-sind)
3. [Downloads am Edge cachen](#downloads-am-edge-cachen)
4. [Medien anderswo hosten](#medien-anderswo-hosten)
5. [Wählen](#wählen)

## Was ein CDN gut kann

Die Seite [CDN](../hosting/leistung/cdn.md) im Hosting-Bereich ist die Referenz. Kurz gesagt:

| Inhalt | Am Edge cachebar |
|---|---|
| Stylesheets, Skripte, Schriften, Designbilder | ja, mit der Lebensdauer, die der Server sendet |
| Inhaltsbilder (`var/*/storage/images`) | ja: Die Adresse eines Bildes ändert sich, wenn es ersetzt wird |
| Anonyme Seiten | nur, wenn Sie cachebare Header für anonyme Besucher einschalten |
| Seiten für Angemeldete, Administration, Formulare | nein |

## Warum Episodendateien anders sind

Episodendateien werden nicht von einem statischen Pfad ausgeliefert. Sie laufen über `/content/download/...`, wo der
Kernel zuerst prüft, ob der Besucher das Objekt lesen darf, und dann die Datei über PHP sendet
(`kernel/classes/binaryhandlers/ezfilepassthrough/ezfilepassthroughhandler.php`). Die Antwort trägt ein leeres
`Cache-Control`, kein `Last-Modified` und ein `Expires` zehn Minuten in der Zukunft (`eZFile::downloadHeaders()` in
`lib/ezfile/classes/ezfile.php`). Der Speicherpfad selbst (`var/.../storage/original/...`) sollte nicht verlinkt oder
offengelegt werden, weil das die Rechteprüfung umgeht
([Dateien und Downloads](../ersteller/website-ersteller/dateien-und-downloads.md#geschützte-downloads)).

## Downloads am Edge cachen

Ein CDN, das die Header des Ursprungs beachtet, behält einen Download höchstens etwa zehn Minuten. Ein Hoster kann das
CDN so konfigurieren, dass es `/content/download/` für öffentliche Episoden länger cacht. Bedenken Sie vorher:

- **Rechte**: Eine gecachte Datei wird an jeden ausgeliefert. Nur öffentliche Episoden dürfen gecacht werden; Dateien
  nur für Mitglieder nie ([Episoden nur für Mitglieder](episoden-nur-fuer-mitglieder.md)).
- **Ersetzte Dateien**: Die Download-Adresse enthält die Objekt-ID, die Attribut-ID und den Dateinamen. Ersetzen Sie das
  Audio einer Episode durch eine Datei gleichen Namens, bleibt die Adresse gleich, und der Edge behält die alte Datei
  vielleicht, bis sie abläuft oder gelöscht wird.
- **Range-Anfragen**: Das CDN muss `Range`-Anfragen korrekt durchreichen oder beantworten, sonst können Player nicht
  springen.
- **Zählen**: Anfragen, die der Edge beantwortet, erreichen die Website nie, daher sieht der Download-Zähler des
  Dateifelds sie nicht ([Downloads und Statistiken](downloads-und-statistiken.md)).

Das ist Konfiguration des CDN, außerhalb von Exponential; die Dokumentation des Anbieters beschreibt, wie.

## Medien anderswo hosten

Was Exponential im Kernel oder in den mitgelieferten Erweiterungen **nicht** bietet:

- hochgeladene Medien in einem Objektspeicher (S3 und ähnliche) statt auf der Festplatte des Servers zu speichern;
- Dateien automatisch zu einem Podcast-Hoster oder einem Medien-CDN zu verschieben;
- einen Feed, dessen Enclosures auf anderswo gehostete Dateien zeigen: Der Podcast-Export schreibt für das zugeordnete
  Medien- oder Dateifeld immer `<site>/content/download/...` (`podcastItemList()` in `kernel/classes/ezrssexport.php`).

Was bleibt:

| Möglichkeit | Wirkung |
|---|---|
| Ein Cluster mit gemeinsamem Speicher (`eZDFSFileHandler`) | mehrere Webserver teilen sich die Dateien; immer noch Ihre eigenen Server ([Clustering und gemeinsamer Speicher](../hosting/betrieb/clustering-und-gemeinsamer-speicher.md)) |
| Ein Podcast-Hoster für Audio und Feed, die Website für die Seiten | der Dienst hostet Dateien und Feed; die Website bettet seinen Player ein und verlinkt seinen Feed. Der Podcast-Export der Website wird dann nicht verwendet |
| Ein Videodienst für Video | Einbetten mit dem Block External video ([Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)) |
| Eigene Entwicklung | eine Erweiterung kann einen anderen Feed-Writer oder einen Dateihandler liefern; das ist Entwicklungsarbeit, keine Konfiguration |

## Wählen

- Eine kleine oder mittlere Sendung mit ausreichendem Hosting: Behalten Sie alles auf der Website; das ist am
  einfachsten und lässt den Feed in Ihrer Hand ([Speicher und Bandbreite](speicher-und-bandbreite.md)).
- Eine Sendung mit großen Spitzen: Fügen Sie ein CDN für statische Dateien hinzu und, mit Sorgfalt, für Downloads
  öffentlicher Episoden.
- Sehr großes Publikum oder Video: Verwenden Sie einen spezialisierten Dienst für die Medien und behalten Sie die Website
  für Seiten, Notizen und Archiv.

## Verwandte Seiten

- [CDN](../hosting/leistung/cdn.md)
- [HTTP-Caching und CDNs](../ersteller/website-ersteller/http-caching-und-cdns.md)
- [Speicher und Bandbreite](speicher-und-bandbreite.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
