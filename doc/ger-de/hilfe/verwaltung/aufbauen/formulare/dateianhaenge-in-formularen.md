---
title: Dateianhänge in Formularen
audience: government, website-creator, admin
category: public-sector
order: 60
summary: Wie Besucher in Exponential mit dem Datentyp Enhanced File der Erweiterung enhancedezbinaryfile eine Datei an ein Formular anhängen können, seine Speichergrenze und sein Verhalten bei Mails, die Risiken, Dateien aus der Öffentlichkeit anzunehmen, und wann stattdessen ein eigenes System sinnvoll ist.
keywords: Datei-Upload, Anhang, Formular, Speichergrenze, Upload-Risiken, Schadsoftware, personenbezogene Daten, Information Collector, file upload, attachment, form, enhancedezbinaryfile, Enhanced File, MaxFiles, DownloadPath, filecheck, upload risks, malware, personal data, information collector
type: help
---
# Dateianhänge in Formularen

Diese Seite richtet sich an Site-Ersteller und Administratoren, bei denen Besucher eine Datei an ein Formular anhängen
müssen, zum Beispiel ein Foto eines beschädigten Verkehrsschilds oder ein Nachweisdokument. Der Datentyp Datei des
Kernels (`ezbinaryfile`) ist für Redakteure gedacht; für das Sammeln von Informationen verwendet Exponential den
Datentyp **Enhanced File** der Erweiterung `enhancedezbinaryfile`
([enhancedezbinaryfile](../../../../../eng-us/features/6.0/extensions/enhancedezbinaryfile.md)). Dateien von beliebigen Menschen
im Internet anzunehmen, ist ein Risiko; lesen Sie die ganze Seite, bevor Sie es einschalten.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was der Datentyp tut](#was-der-datentyp-tut)
2. [Einrichtung](#einrichtung)
3. [Risiken und Maßnahmen](#risiken-und-maßnahmen)
4. [Wann ein anderes System sinnvoll ist](#wann-ein-anderes-system-sinnvoll-ist)

## Was der Datentyp tut

| Verhalten | Detail |
|---|---|
| Upload in einem sammelnden Attribut | im Klasseneditor als **Enhanced File** angezeigt |
| Speicherung mit Grenze | `module.ini [RemoveFiles] MaxFiles=25`: nur so viele hochgeladene Dateien werden behalten; `DownloadPath=original/attachments` |
| Mail | die gesammelten Informationen werden als mehrteilige Mail mit angehängter Datei versendet, wenn es einen Anhang gibt |
| Fehlgeschlagene Mail | Dateien werden unter `var/<siteaccess>/storage/original/collected/<filetype>` gespeichert |
| Prüfung im Template | der Operator `filecheck` prüft, ob eine Datei noch existiert, bevor er sie verlinkt |

`MaxFiles=0` behält eine unbegrenzte Zahl von Dateien, wovon die eigene Dokumentation der Erweiterung bei Sites mit
anonymen Uploads abrät. Die Standardinstallation aktiviert die Erweiterung
([Erweiterungen, die es aktiviert](../../../seitenpakete/sevenx-multisite/erweiterungen-die-sevenx-multisite-aktiviert.md)).

## Einrichtung

1. Fügen Sie in der Formularklasse (**Setup > Klassen**) ein Attribut vom Datentyp **Enhanced File** hinzu, kreuzen Sie
   **Informationssammler** an und entscheiden Sie, ob es erforderlich ist.
2. Setzen Sie die Grenzen in `settings/override/module.ini.append.php` (`[RemoveFiles] MaxFiles`) und die
   Upload-Grenze von PHP (`upload_max_filesize`) auf dem Server. Der nötige Speicherplatz ist höchstens `MaxFiles` mal
   `upload_max_filesize`.
3. Stellen Sie sicher, dass das Formular-Template mit `enctype="multipart/form-data"` sendet.
4. Testen Sie: Senden Sie abgemeldet eine Datei, prüfen Sie die Mail mit dem Anhang (zuerst mit dem Datei-Transport,
   [Mail an Empfänger](mail-an-empfaenger.md)) und die gespeicherte Datei.

## Risiken und Maßnahmen

| Risiko | Maßnahme |
|---|---|
| Schadsoftware in hochgeladenen Dateien | scannen Sie Dateien auf dem Server (ein Virenscanner auf dem Speicherpfad oder dem Mail-Gateway); öffnen Sie Anhänge nie auf ungeschützten Arbeitsplätzen |
| Dateien, die zum Speichern oder Verbreiten illegaler Inhalte genutzt werden | halten Sie `MaxFiles` klein; Dateien sind nicht öffentlich, sie liegen im Speicher und werden per Mail versendet; prüfen Sie, dass der Download-Pfad nicht vom Webserver ausgeliefert wird |
| Personenbezogene Daten in Dokumenten (Ausweise, ärztliche Schreiben) | fragen Sie nur, was Sie brauchen; sagen Sie das im Formular; entfernen Sie Dateien nach der Bearbeitung |
| Sehr große Dateien | Upload-Grenzen von PHP und Webserver |
| Spam mit Anhängen | Formular-Token, Moderation, ein CAPTCHA nach einer Abwägung ([Spamschutz und Drittanbieter](spamschutz-und-drittanbieter.md)) |
| Größengrenzen bei Mails | der empfangende Mailserver kann große Anhänge ablehnen; testen Sie mit der größten erlaubten Datei |

Exponential bereinigt SVG-Uploads und lehnt gefährliche Dateinamen wie `shell.php.jpg` beim Upload im Editor ab
([Inhaltsmodell und Bearbeitung](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#4-der-online-editor-und-tinymce-8),
[Standards zur Sicherheit](../../../../../eng-us/standards/security.md)); es prüft Dateien nicht auf Schadsoftware.

## Wann ein anderes System sinnvoll ist

| Situation | Verwenden Sie |
|---|---|
| Anträge mit mehreren erforderlichen Dokumenten | das Fall- oder Antragssystem |
| Dokumente mit besonderen Kategorien personenbezogener Daten | ein für diese Daten zugelassenes System |
| Viele Uploads am Tag | ein System mit Prüfung, Kontingenten und Fallbearbeitung |
| Uploads, die als Schriftgut aufbewahrt werden müssen | ein Schriftgut- oder Fallsystem, keine Postfächer |

Für die meisten öffentlichen Formulare ist es sicherer, Menschen um eine Beschreibung des Problems zu bitten und für
Dokumente einen getrennten Weg anzubieten, als Uploads anzunehmen.

## Verwandte Seiten

- [Formulare aufbauen](README.md)
- [Aufbewahrung gesammelter Informationen](aufbewahrung-gesammelter-informationen.md)
- [Dateien und Downloads](../../../../../eng-us/help/creators/website-creators/files-and-downloads.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
