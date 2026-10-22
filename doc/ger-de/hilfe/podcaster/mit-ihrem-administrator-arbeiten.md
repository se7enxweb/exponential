---
title: Mit Ihrem Administrator arbeiten
audience: podcaster
category: podcasting-streaming
order: 10
summary: Was eine Sendung vom Administrator der Website braucht, und die genauen Einstellungen hinter jeder Anfrage - Rechte für Feeds, Audioformate, Klassenzuordnung beim Upload, Download-Verhalten, Cronjobs für die Zeitplanung, Upload-Grenzen und Erlaubnis für Einbettungen -, damit Produzenten gezielt fragen und Administratoren schnell handeln können.
keywords: Administrator, Einstellungen, Anfrage, rss/edit, mime.ini, upload.ini, file.ini, PassThroughSettings, ContentDisposition, site.ini, RSSSettings, CacheTime, Cronjob, Workflow, ContentSecurityPolicy, AdditionalSources, php.ini, upload_max_filesize, administrator, settings, request
type: help
---
# Mit Ihrem Administrator arbeiten

Diese Seite ist für Produzenten, die etwas eingerichtet brauchen, und für die Administratoren, die es tun. Das meiste,
was eine Sendung braucht, ist Konfiguration, nicht Code. Jede Zeile nennt die Einstellung und die Datei im
mitgelieferten Verzeichnis `settings/`; Änderungen gehören in einen **Override**
(`settings/override/<file>.append.php` oder einen Siteaccess), nie in die mitgelieferte Datei selbst, wie es der Kopf
jeder mitgelieferten Einstellungsdatei sagt.

## Inhalt

1. [Zugriffe, die Sie brauchen](#zugriffe-die-sie-brauchen)
2. [Einstellungen, die eine Sendung brauchen kann](#einstellungen-die-eine-sendung-brauchen-kann)
3. [Beispiele](#beispiele)
4. [Was Ihnen der Administrator sagen sollte](#was-ihnen-der-administrator-sagen-sollte)
5. [Wie Sie fragen](#wie-sie-fragen)

## Zugriffe, die Sie brauchen

| Aufgabe | Was sie braucht |
|---|---|
| Episoden anlegen und bearbeiten | Inhaltsrichtlinien, um die Episodenklasse unterhalb der Ordner der Sendung anzulegen |
| Feeds anlegen und bearbeiten (**Setup > RSS**) | Die Richtlinie `rss/edit`; der Siteaccess für Redakteure bietet das RSS-Modul gar nicht an ([RSS-Feeds](../../anleitungen/rss-feeds.md#1-die-seite-öffnen)) |
| Seiten mit dem Layout-Editor bauen | Zugriff auf den Layout-Editor ([Der Layout-Editor](../layout-editor.md)) |
| Episoden freigeben | Als Freigebender im Workflow-Ereignis *Freigeben* eingetragen sein |

Rollen und Richtlinien: [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md).

## Einstellungen, die eine Sendung brauchen kann

| Bedarf | Datei und Einstellung | Auslieferungszustand |
|---|---|---|
| Podcast-Exportformat angeboten | `site.ini [RSSSettings] AvailableVersionList[]=ITUNES` | an |
| Wie lange ein Feed gecacht wird | `site.ini [RSSSettings] CacheTime` | 1200 Sekunden |
| Wie viele Objekte ein Feed enthalten kann | `site.ini [RSSSettings] NumberOfObjectsList[]` | 5 bis 1000 |
| Ein Audioformat erkannt (m4a, aac, opus) | `mime.ini`, ein Block pro Endung mit `Types[]` | nicht aufgeführt |
| Ein hochgeladenes MP3 wird zur Episode | `upload.ini [CreateSettings] MimeClassMap[audio/mpeg]` und `[<class>_ClassSettings]` | nicht zugeordnet: wird zu *File* |
| Ein Audio-Link spielt beim Öffnen im Browser | `file.ini [PassThroughSettings] ContentDisposition[audio/mpeg]=inline` | `attachment` für Audio |
| Zu einem Termin veröffentlichen | ein Workflow mit *Bis Datum warten* auf `content / publish / before` und der Cronjob-Teil `frequent`, der `workflow.php` ausführt | nicht eingerichtet |
| Zu einem Termin verbergen oder entfernen | `content.ini [HideSettings]`, `[UnpublishSettings]` und die Cronjobs `hide.php`, `unpublish.php` | Listen leer |
| Größere Uploads | PHP `upload_max_filesize`, `post_max_size`, `max_execution_time` | hängt vom Hoster ab |
| Eingebettete Player unter einer durchgesetzten Richtlinie erlaubt | `site.ini [ContentSecurityPolicy] AdditionalSources[]=frame-src <host>` | `Mode=auto`: keine Richtlinie auf öffentlichen Siteaccesses |

## Beispiele

Eine erkannte M4A-Datei (der Typname ist die Wahl des Administrators; `audio/mp4` und `audio/x-m4a` sind beide
gebräuchlich):

```ini
# settings/override/mime.ini.append.php
[m4a]
Types[]=audio/mp4
```

Audio-Links, die im Browser abspielen:

```ini
# settings/override/file.ini.append.php
[PassThroughSettings]
ContentDisposition[audio/mpeg]=inline
```

Player eines Streamingdienstes, erlaubt durch eine durchgesetzte Content Security Policy:

```ini
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
AdditionalSources[]=frame-src https://player.example.com
```

Nach einer Änderung der Einstellungen leert der Administrator den Cache der Einstellungen und bei Feeds auch den
RSS-Cache ([Caches](../../anleitungen/caches.md)).

## Was Ihnen der Administrator sagen sollte

- Welche Klassen Sie wo anlegen dürfen.
- Ob zeitgesteuertes Veröffentlichen eingerichtet ist, welches Datumsfeld zählt und wie oft der Cronjob-Teil
  `frequent` läuft.
- Die größte Datei, die Sie hochladen können (die Upload-Seite zeigt sie ebenfalls an).
- Ob die öffentliche Website eine Content Security Policy durchsetzt.
- Wo Sicherungen der hochgeladenen Medien aufbewahrt werden und wie lange ([Speicher und Bandbreite](speicher-und-bandbreite.md)).

## Wie Sie fragen

Nennen Sie die Sendung, den genauen Bedarf und einen Testfall. Zum Beispiel: "Sendung *Example*: Hochgeladene
MP3-Dateien sollen zu Objekten der Klasse *Audio* im Ordner *Podcasts/Example* werden. Test: `test.mp3` hochladen,
erwartet wird ein Objekt *Audio* mit der Datei im Feld **File**." Eine Anfrage mit Test wird einmal erledigt und
einmal geprüft.

## Verwandte Seiten

- [Was nicht eingebaut ist](was-nicht-eingebaut-ist.md)
- [Die Seite Einstellungen](../../anleitungen/einstellungsseite.md)
- [Cronjobs](../../anleitungen/cronjobs.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
