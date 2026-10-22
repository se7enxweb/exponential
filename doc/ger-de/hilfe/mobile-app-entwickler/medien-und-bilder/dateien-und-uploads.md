---
title: Dateien und Uploads
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 530
summary: Dateien aus Exponential in einer App herunterladen (expfile-Dienste, die URL content/download, zeitlich begrenzte signierte Links für Clients ohne Sitzung) und warum das Hochladen von Fotos oder Dateien von einem Gerät einen eigenen Endpunkt braucht - mit dem, was in den Auftrag dafür gehört.
keywords: Dateien, Downloads, ezbinaryfile, expfile, downloadurl, signedurl, content/download, Uploads, Fotos, Kamera, deniedInputTypes, eigener Endpunkt, Mobile App, files, downloads, uploads, photos, camera, custom endpoint, mobile app
type: help
---
# Dateien und Uploads

Diese Seite richtet sich an App-Entwickler, deren App Dokumente (PDFs, Broschüren) aus Exponential öffnet oder Benutzer
Fotos und Dateien senden lassen möchte. Downloads decken die Remote-Dienste ab; Uploads nicht, und diese Seite sagt, was
Sie stattdessen tun.

## Inhalt

1. [Dateien herunterladen](#dateien-herunterladen)
2. [Uploads: was es gibt und was nicht](#uploads-was-es-gibt-und-was-nicht)
3. [Ein Auftrag für einen Upload-Endpunkt](#ein-auftrag-für-einen-upload-endpunkt)

## Dateien herunterladen

Dateifelder (Datentyp `ezbinaryfile`) kommen über REST in der Zeichenkettenform des Datentyps. Für eine App sind die
`expfile`-Dienste klarer (deklariert in `extension/expservices/classes/media/expfileservices.php`):

| Dienst | Argumente | Liefert |
|---|---|---|
| `expfile::attributes` | Knoten-ID | die Dateifelder eines Knotens, mit `has_content` |
| `expfile::info` | Knoten-ID, Feldbezeichner | Namen, MIME-Typ, Größe, Anzahl der Downloads |
| `expfile::downloadurl` | Knoten-ID, Feldbezeichner | `url` und `absolute_url` der Adresse `content/download` der Website; der Download selbst prüft `content/read` |
| `expfile::signedurl` | Knoten-ID, Feldbezeichner, Sekunden | einen zeitlich begrenzten signierten Link, beschrieben als „for clients without a session“ |

Die Adresse `content/download` prüft die Rechte des anfragenden Benutzers, und eine native App hat meist keine Sitzung
auf der Website; dafür ist der signierte Link da. Fordern Sie ihn kurz vor dem Öffnen der Datei mit einer kurzen
Laufzeit an und speichern Sie ihn nicht.

## Uploads: was es gibt und was nicht

- **Das Anlegen über REST** nimmt Feldwerte als Formularfelder entgegen und hat keinen dokumentierten Datei-Upload; die
  Beispiele der Anleitungen setzen nur Textfelder ([Inhalte anlegen und entfernen](../rest-api/inhalte-anlegen-und-entfernen.md)).
- **expservices** lehnt entfernte Schreibzugriffe auf die Datentypen `ezimage`, `ezbinaryfile`, `ezmedia`, `ezuser`,
  `ezpassword` und `ezflowblock` ab, „whose fromString() reads a local path or URL, or that hold credentials“
  (`expContentBase::$deniedInputTypes`).
- Kein Dienst im mitgelieferten Katalog nimmt eine hochgeladene Datei entgegen (der Dienst `expfile::safename` prüft
  nur einen Dateinamen).

Eine App, die Fotos, Sprachnotizen oder Dokumente sendet, braucht also einen eigens dafür gebauten Endpunkt.

## Ein Auftrag für einen Upload-Endpunkt

Für den Anwendungsentwickler, der ihn baut (als REST-Provider, expservices-Domäne oder Modul-View; siehe
[Ihr eigener REST-Provider](../../ersteller/anwendungsentwickler/rest-api.md#ein-eigener-rest-provider) und
[Formulare, CSRF-Schutz und HTTP-Eingaben](../../ersteller/anwendungsentwickler/formulare-csrf-und-http-eingaben.md)):

- Multipart-Upload von einer Datei je Anfrage, mit einer Höchstgröße und einer Liste erlaubter Typen, geprüft anhand des
  Inhalts und nicht nur der Endung;
- der Benutzer, als der er läuft, und der Ordner und die Klasse, die er anlegt (zum Beispiel ein `image`-Objekt unterhalb
  eines Ordners „App-Uploads“), geprüft mit `content/create` wie jeder andere Schreibzugriff;
- eine Antwort mit der neuen Knoten-ID und ein klarer Fehler im Stil von `413` oder `415` für zu große Dateien oder
  Dateien des falschen Typs;
- keine Datei, die von einer URL oder einem vom Client angegebenen Serverpfad gelesen wird;
- Moderation: Uploads landen unveröffentlicht oder in einem Bereich, den nur Redakteure sehen, wenn Benutzern nicht
  vertraut wird.

In der App: Verkleinern Sie Fotos vor dem Upload, laden Sie im WLAN hoch, wo der Benutzer das bevorzugt, und beginnen
Sie einen unterbrochenen Upload von vorn.

## Verwandte Seiten

- [Über expservices schreiben](../json-endpunkte/ueber-expservices-schreiben.md)
- [Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md)
- [Was die APIs nicht können](../was-die-apis-nicht-koennen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
