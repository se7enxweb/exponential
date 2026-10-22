---
title: Medien und Bilder für Apps
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 500
summary: Die Einstiegsseite der Medienseiten für App-Entwickler - was die REST-API für Bilder liefert (den Pfad der Originaldatei), wie Bildaliase über die expimage-Dienste Versionen in Bildschirmgröße liefern und was nicht möglich ist (Uploads von Geräten).
keywords: Bilder, Medien, Bildalias, ezimage, Original, expimage, Dateien, Uploads, Bildschirmgrößen, Mobile App, images, media, image alias, original, files, uploads, screen sizes, mobile app
type: help
---
# Medien und Bilder für Apps

Dieser Ordner richtet sich an App-Entwickler, die Bilder und Dateien aus Exponential anzeigen, und an diejenigen, die
mit der App Fotos dorthin senden möchten. Bilder sind meist der schwerste Teil des Datenverkehrs einer App, daher zahlen
sich Bildschirmgrößen und Caching hier am meisten aus.

## Inhalt

1. [Die Lage in Kürze](#die-lage-in-kürze)
2. [Seiten in diesem Ordner](#seiten-in-diesem-ordner)

## Die Lage in Kürze

| Bedarf | Verfügbar | Lesen |
|---|---|---|
| Das Originalbild eines Elements | der REST-Feldwert eines `ezimage`-Felds: der Pfad der Originaldatei | [Bilder in REST-Antworten](bilder-in-rest-antworten.md) |
| Das Bild in einer benannten Größe | `expimage::url` und `expimage::urls` der Remote-Dienste, bei Bedarf erzeugt | [Bildaliase für Bildschirmgrößen](bildaliase-fuer-bildschirmgroessen.md) |
| Die Liste der benannten Größen | `expimage::aliases` (öffentlich) | dieselbe Seite |
| Alternativtext | `expimage::alt`, `expimage::info` | [Bilder in REST-Antworten](bilder-in-rest-antworten.md) |
| Dateien herunterladen | die `expfile`-Dienste | [Dateien und Uploads](dateien-und-uploads.md) |
| Ein Foto oder eine Datei von einem Gerät hochladen | keine dokumentierte Route | dieselbe Seite |

## Seiten in diesem Ordner

| Seite | Beantwortet |
|---|---|
| [Bilder in REST-Antworten](bilder-in-rest-antworten.md) | Was der `ezimage`-Wert ist und wie Sie daraus eine URL machen |
| [Bildaliase für Bildschirmgrößen](bildaliase-fuer-bildschirmgroessen.md) | Benannte Größen, Größen für die App ergänzen, Pixeldichte und Caching |
| [Dateien und Uploads](dateien-und-uploads.md) | Downloads, und warum Uploads einen eigenen Endpunkt brauchen |

## Verwandte Seiten

- [Bilder und Bildvarianten](../../ersteller/website-ersteller/bilder-und-bildvarianten.md)
- [Headless-Exponential: Rich Text, Bilder und Links](../../ersteller/anwendungsentwickler/headless-exponential.md#rich-text-bilder-und-links)
- [Medien und Uploads](../../medien-und-uploads.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
