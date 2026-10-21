---
title: Bildverarbeitung und Medien
audience: hosting, admin
category: hosting
order: 180
summary: Was ein Hoster für die Bildverarbeitung von Exponential bereitstellen muss - GD und ImageMagick und ihre Reihenfolge, die Bild-Aliase und wo Varianten gespeichert werden, die akzeptierten und geprüften Upload-Typen, die Bereinigung von SVG, die Größenänderung auf Anfrage in Velocity, Upload-Grenzen, Berechtigungen und die Befehle zum Leeren der Varianten.
keywords: Bilder, Bildverarbeitung, Medien, GD, ImageMagick, convert, Bild-Aliase, Varianten, image.ini, ImageConverters, AliasList, AllowedOutputFormat, ImageUploadSettings, AllowedMIMETypes, fileinfo, SVG, SanitizeSVG, WebP, AVIF, var/storage, upload_max_filesize, imagealias, Velocity, Hosting, images, image processing, media, image aliases, variations, hosting
type: help
---
# Bildverarbeitung und Medien

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende bei einem Hosting-Anbieter. Sie beantwortet: Welche
Bildbibliotheken und Programme ein Exponential-Server braucht, wie Bilder und ihre Varianten gespeichert werden, welche
Uploads akzeptiert werden, was Velocity hinzufügt und welche PHP- und Berechtigungseinstellungen Medien betreffen. Die
Einstellungen stehen in `settings/image.ini`; die Zusammenfassung für den Betrieb ist
[Kapitel 10.8](../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung).

## Inhalt

1. [Was der Server braucht](#was-der-server-braucht)
2. [Die Konverter und ihre Reihenfolge](#die-konverter-und-ihre-reihenfolge)
3. [Bild-Aliase und Speicher](#bild-aliase-und-speicher)
4. [Uploads: Typen, Prüfung und Grenzen](#uploads-typen-prüfung-und-grenzen)
5. [Die Bildverarbeitung von Velocity](#die-bildverarbeitung-von-velocity)
6. [Berechtigungen für Medien](#berechtigungen-für-medien)
7. [Befehle](#befehle)
8. [Checkliste](#checkliste)
9. [Verwandte Seiten](#verwandte-seiten)

## Was der Server braucht

| Element | Gebraucht für | Geprüft von |
|---|---|---|
| GD (Erweiterung `gd`, `imagegd2()`) oder ImageMagick (`convert`) | Bildvarianten; mindestens eines ist erforderlich | kritischer Test `image_conversion` (`Require=one`) |
| Sowohl GD als auch ImageMagick | GD für die üblichen Formate im Prozess, ImageMagick für Formate, die GD nicht lesen kann | empfohlen ([Kapitel 2](../../../installation/02-voraussetzungen.md#21-die-checkliste)) |
| GD mit FreeType (`imagettftext()`, `imagettfbbox()`) | den Template-Operator Text-zu-Bild | optionaler Test `texttoimage_functions` |
| `fileinfo` | den Typ eines hochgeladenen Bildes aus seinem Inhalt lesen | `[ImageUploadSettings]`; Systeminformationen warnen, wenn es fehlt |
| `system()` nicht deaktiviert | der ImageMagick-Konverter führt `convert` darüber aus | siehe [PHP-Einstellungen für Exponential](php-einstellungen-fuer-exponential.md#funktionen-die-manche-funktionen-aufrufen) |

Der Setup-Assistent sucht `convert` in `/bin`, `/sbin`, `/usr/bin`, `/usr/sbin`, `/usr/local/bin` und
`/usr/local/sbin` und schaltet den ImageMagick-Konverter ab, wenn er keines findet. Prüfen Sie mit `convert -version`
und `php --ri gd`.

## Die Konverter und ihre Reihenfolge

`settings/image.ini`, `[ImageConverterSettings] ImageConverters[]` listet die Konverter in Reihenfolge auf; der erste,
der ein Bild verarbeiten kann, wird genutzt:

```ini
ImageConverters[]
ImageConverters[]=GD
ImageConverters[]=ImageMagick
```

- **GD kommt zuerst**: Es ist in PHP eingebaut, konvertiert im Prozess, ohne pro Bild ein Programm zu starten, und liest
  und schreibt JPEG, PNG, GIF und WBMP.
- **ImageMagick ist die Rückfallebene** für Formate, die GD nicht lesen kann (zum Beispiel PSD, TIFF, PDF oder WebP), und
  für Filter, die nur es unterstützt. Es übernimmt vollständig, wenn GD fehlt oder `[GD] IsEnabled=false` gesetzt ist.
- `[ImageMagick] ExecutablePath` (leer: der Suchpfad) und `Executable` (`convert`) finden das Programm.

Um ImageMagick zu bevorzugen, überschreiben Sie die Liste in `settings/override/image.ini.append.php` mit ImageMagick
zuerst.

`[ImageConverterSettings] LockTimeout` (mitgeliefert `60`) ist, wie viele Sekunden ein Prozess wartet, während ein anderer
denselben Alias erzeugt; danach beendet der wartende Prozess den anderen und erzeugt den Alias selbst.

## Bild-Aliase und Speicher

- Hochgeladene Bilder liegen im Speicherverzeichnis der Website, `var/<site var dir>/storage/images`
  (`image.ini [FileSettings] PublishedImages=images`; Entwürfe und archivierte Versionen in `images-versioned`).
- Ihre Varianten, die **Aliase**, werden bei der ersten Verwendung erzeugt. `[AliasSettings] AliasList[]` benennt sie
  (`reference`, `small`, `tiny`, `medium`, `large`, `rss` und die des Designs der Website).
- `[OutputSettings] AllowedOutputFormat[]` listet die Formate auf, die Seiten zeigen dürfen: JPEG, PNG, WebP und GIF.
- Temporäre Bilddateien landen im Unterverzeichnis `imagetmp` des Caches (`[FileSettings] TemporaryDir`).

Für einen Hoster heißt das: Der Bildspeicher wächst mit den Uploads **und** mit der Anzahl der Aliase, die ein Design
definiert, und der erste Aufruf einer neuen Seite kann CPU kosten, während ihre Varianten erzeugt werden.

## Uploads: Typen, Prüfung und Grenzen

`image.ini [ImageUploadSettings]` entscheidet, welche hochgeladenen Dateien als Bilder akzeptiert werden, auf jedem Weg
(das Bearbeitungsformular, das Upload-Modul, der Rich-Text-Editor, der AJAX-Uploader, WebDAV, Importe):

| Schlüssel | Mitgeliefert | Bedeutung |
|---|---|---|
| `ValidateMIMEType` | `enabled` | der Typ wird mit `fileinfo` aus dem Inhalt der Datei gelesen, nicht aus ihrem Namen |
| `AllowedMIMETypes[]` | JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP, SVG | eine abgelehnte Datei wird nicht gespeichert |
| `SanitizeSVG` | `enabled` | ein SVG wird als Ausgabe des Bereinigers gespeichert, ohne Skript, Ereignisattribute, `foreignObject` oder externe Verweise; SVG-Aliase werden nie skaliert |

Liefern Sie SVG-Dateien mit den Headern aus [SVG-Bilder](../../../bc/6.0/svg-bilder.md) aus.

**Die Upload-Größe** ist die von PHP: `upload_max_filesize` und `post_max_size` müssen so groß sein wie die größte Datei,
die Kunden hochladen; Exponential prüft sie nicht. Nennen Sie die Grenze im Tarif und setzen Sie sie im Pool
([PHP-Einstellungen für Exponential](php-einstellungen-fuer-exponential.md#ein-produktionssatz-für-einen-pool)).

## Die Bildverarbeitung von Velocity

Wenn Velocity die Website ausliefert, beantwortet er statische Dateien selbst und kann Bilder auf Anfrage in der Größe
ändern ([Statische Dateien und Bilder in Velocity](../../../funktionen/6.0/velocity-statische-dateien-und-bilder.md)):

- `?w=400` oder `?h=300` an einer Bild-URL ändert die Größe, nie über die Größe der Quelle hinaus; für Browser, die es
  akzeptieren, wird AVIF oder WebP gewählt; `Save-Data: on` senkt die Qualität.
- Ergebnisse werden zwischengespeichert; `Q.images.cache.maxSize` (mitgeliefert 268435456 Byte) begrenzt den Cache.
- Es braucht GD in PHP (ohne GD wird das Original ausgeliefert), WebP-Unterstützung in GD für WebP und `imageavif()`
  (PHP 8.1 mit libavif) für AVIF.

Das ist getrennt von den Bild-Aliasen des Kernels oben, die unter jedem Webserver funktionieren.

## Berechtigungen für Medien

`image.ini [FileSettings]` hat eigene Modi für Bildverzeichnisse und -dateien, mitgeliefert für alle beschreibbar:

```ini
# settings/override/image.ini.append.php
[FileSettings]
DirPermissions=0770
ImagePermissions=0660
```

`image.ini` selbst bevorzugt `0770` und `0660` mit dem richtigen Benutzer und der richtigen Gruppe. Jeder Prozess, der
einen Alias erzeugen darf (das Web-PHP, die Worker von Velocity, Cron und die Konsole), muss in das Speicherverzeichnis
schreiben können; siehe [Beschreibbare Verzeichnisse und Berechtigungen](beschreibbare-verzeichnisse-und-berechtigungen.md).

## Befehle

Nachdem sich die Definition eines Alias geändert hat, entfernen Sie die erzeugten Varianten, damit sie neu erzeugt werden:

```bash
php bin/php/cache.php imagealias clear --allow-root-user
```

Dasselbe gibt es in der Konsole als `exp:cache imagealias`. Bilder und ihre Varianten gehören zu `var/storage`, das
jedes Backup enthalten muss ([Was gesichert werden muss](../betrieb/backups-was-gesichert-werden-muss.md)).

## Checkliste

- [ ] GD (mit FreeType) und ImageMagick installiert; `convert -version` und `php --ri gd` antworten.
- [ ] `fileinfo` im Web-PHP und im PHP der Kommandozeile geladen.
- [ ] `system` nicht in `disable_functions`, wo ImageMagick genutzt wird.
- [ ] Upload-Grenzen gesetzt und im Tarif genannt.
- [ ] Modi in `image.ini [FileSettings]` gehärtet.
- [ ] `var/storage` in Backups und in der Überwachung des Speicherplatzes enthalten.

## Verwandte Seiten

- [Kapitel 10.8: Suchindizierung und Bildverarbeitung](../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung)
- [Medien und Uploads](../../medien-und-uploads.md) (für Redakteure)
- [Statische Dateien und Bilder in Velocity](../../../funktionen/6.0/velocity-statische-dateien-und-bilder.md)
- [Speicher, CPU und Festplatte dimensionieren](speicher-cpu-und-festplatte-dimensionieren.md)
- [CDN](../leistung/cdn.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
