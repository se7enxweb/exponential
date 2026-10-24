---
title: Datei-Uploads und SVG
audience: government, admin, developer
category: public-sector
order: 220
summary: Für öffentliche Stellen, deren Beschäftigte Bilder und Dokumente hochladen - wie Exponential verhindert, dass hochgeladene Dateien Code ausführen (keine Ausführung unterhalb des Wurzelverzeichnisses, Typ aus dem Inhalt gelesen, SVG-Bereiniger, Downloads über content/download), und was der Betreiber beibehält.
keywords: Datei-Uploads, Upload-Sicherheit, SVG, Bereinigung, Positivliste, Downloads, Umschreiberegeln, öffentlicher Sektor, file uploads, upload security, expSvgSanitizer, SanitizeSVG, expImageUploadValidator, AllowedMIMETypes, fileinfo, content/download, rewrite rules, TemporaryDir, packages, ManagedByDeployment, public sector
type: help
---
# Datei-Uploads und SVG

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Entwicklerinnen und Entwickler. Uploads sind
ein klassischer Weg auf einen Webserver: ein als Bild getarntes Skript, eine SVG- oder HTML-Datei, die im Browser
Skript ausführt, oder eine riesige Datei, die den Server erschöpft. Exponential hat Schutzmaßnahmen auf jeder Stufe
([Sicherheitshärtung: Missbrauch von Datei-Uploads](../../sicherheitshaertung.md#missbrauch-von-datei-uploads)).

## Inhalt

1. [Die Schutzmaßnahmen](#die-schutzmaßnahmen)
2. [SVG-Bilder](#svg-bilder)
3. [Beibehalten](#beibehalten)
4. [Prüfen](#prüfen)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Schutzmaßnahmen

| Schritt | Schutz | Wo |
|---|---|---|
| Ein Bild annehmen | Typ mit fileinfo von PHP aus dem Dateiinhalt gelesen und mit einer Positivliste verglichen | `expImageUploadValidator`, `image.ini [ImageUploadSettings] AllowedMIMETypes[]` |
| Ein SVG speichern | bereinigt; nur was als sicher bekannt ist, bleibt erhalten | `expSvgSanitizer`, `image.ini [ImageUploadSettings] SanitizeSVG=enabled` |
| Ausliefern | unterhalb des Document-Root wird nichts ausgeführt (`.php`, `.phtml`, `.phar` antworten mit 404); direkt aus dem Speicher werden nur Bilddateien ausgeliefert | `.htaccess_root` |
| Andere Uploads ausliefern | nur über `content/download`, das die Leseberechtigung prüft | Kernel |
| SVG aus dem Speicher ausliefern | ein Sandbox-Header (`sandbox; default-src 'none'`) | `.htaccess_root`, benötigt `mod_headers` |
| Pakete | Uploads benötigen `package`-Richtlinien; `ManagedByDeployment` sperrt sie | [Pakete](../../../anleitungen/pakete.md) |

Die ab Werk erlaubten Bildtypen sind JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP und SVG
([Sicherheit und Audit, 1](../../../anleitungen/sicherheit-und-audit.md#1-die-bereits-aktive-härtung-prüfen-5-minuten)).

## SVG-Bilder

Seit dem 15. Oktober 2026 nimmt der Bild-Datentyp auf jeder Website SVG an (`.svg`, `.svgz`). Ein SVG ist ein Dokument,
aus dem ein Browser Skript ausführen kann, deshalb wird es nie so gespeichert, wie es hochgeladen wurde: Die
gespeicherte Datei ist die Ausgabe des Bereinigers. Jeder Weg, der ein Bild speichert, bereinigt (Bearbeitungsformular,
Upload-Modul, Mehrfach-Upload, Upload-Dialog des Editors, AJAX-Uploader, Paketimport). Eine Datei, die vorgibt,
ein SVG zu sein, aber nicht als solches erkannt wird, wird nicht gespeichert ([SVG-Bilder](../../../bc/6.0/svg-bilder.md)).

## Beibehalten

- Halten Sie die Umschreiberegeln nach jedem Upgrade aktuell (`.htaccess_root` oder den abgeleiteten nginx-Block);
  liefern Sie die Installation nie mit einer Konfiguration „Datei ausliefern, wenn sie existiert“ aus.
- Lassen Sie `SanitizeSVG=enabled` stehen.
- Prüfen Sie, dass fileinfo im PHP des Webservers geladen ist: `php -m | grep fileinfo` auf der Kommandozeile, und
  dasselbe für PHP-FPM. Ohne fileinfo gilt eine ältere Prüfung, und das Protokoll sagt es.
- Vergeben Sie Upload-Rechte nur an Rollen, die sie brauchen.
- Halten Sie `TemporaryDir` privat für die Website, zum Beispiel unterhalb von `var/` (das mitgelieferte `/tmp/` wird
  mit allen Benutzern des Rechners geteilt).
- Dokumente und andere Dateien prüft Exponential nicht auf Schadsoftware. Verlangt Ihre Richtlinie eine Prüfung, führen
  Sie sie auf Ebene des Servers oder des Speichers durch.

## Prüfen

```bash
./console exp:ini get 'image.ini/ImageUploadSettings/AllowedMIMETypes' --allow-root-user
for p in .env .git/config extension/expservices/ezinfo.php; do
  curl -s -o /dev/null -w "$p %{http_code}\n" https://www.example.gov/$p
done
```

Erwartet: die Positivliste so, wie Sie sie beabsichtigen, und dreimal `404`.

## Verwandte Seiten

- [SVG-Bilder](../../../bc/6.0/svg-bilder.md)
- [13.2 Was der Webserver nie herausgeben darf](../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf)
- [SVG und Bildformate](../../ersteller/website-ersteller/svg-und-bildformate.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
