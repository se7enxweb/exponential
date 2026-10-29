---
title: Wie arbeite ich über WebDAV mit Inhalten als Dateien?
audience: editor
category: manual
order: 498
summary: Wo ein Administrator WebDAV eingeschaltet hat, verbinden Sie Ihren Dateimanager mit Ihrem gewohnten Konto mit der WebDAV-Adresse der Website; Ordner sind Elemente, hineingezogene Dateien erzeugen Elemente, das Speichern einer gleichnamigen Datei erzeugt eine neue Version, und Löschen schickt Elemente in den Papierkorb.
keywords: WebDAV, Netzlaufwerk, Dateimanager, viele Dateien hochladen, Ziehen und Ablegen, neue Version, Papierkorb, einbinden, index_webdav.php, webdav.php, Content, Media, siteaccess, network drive, file manager, upload many files, drag and drop, new version, trash, mount
type: help
---
# Wie arbeite ich über WebDAV mit Inhalten als Dateien?

Mit WebDAV öffnet der Dateimanager Ihres Computers den Inhaltsbaum wie ein Netzlaufwerk. Sie können einen Stapel
Bilder hineinziehen, Dateien herausholen oder Dokumente aktuell halten, indem Sie sie überschreiben. Im Hintergrund ist
jede Datei ein Element, das mit Ihren normalen Rechten angelegt und geändert wird.

WebDAV ist in einer neuen Installation ausgeschaltet. Ein Administrator muss es einschalten und Ihnen die Adresse
nennen.

## Wer das tun kann

Jeder Benutzer mit einem Konto auf der Website; die üblichen Rollen entscheiden, was Sie lesen, anlegen, ändern und
entfernen dürfen.

## Schritte

1. Lassen Sie sich die WebDAV-Adresse von Ihrem Administrator geben. Sie ist entweder ein eigener Host, etwa
   `https://webdav.example.com/<site>/`, oder die Adresse der Website, gefolgt von `/index_webdav.php/<site>/`. Eine
   Adresse mit `/webdav.php/` funktioniert noch, aber dieser alte Name wird in einer späteren Hauptversion entfernt:
   Fragen Sie nach der Adresse mit `index_webdav.php` und verbinden Sie sich neu
   ([warum](../../bc/6.0/webdav-einstiegsskript-index-webdav.md)).
2. Verbinden Sie Ihren Dateimanager damit und melden Sie sich mit Ihrem Benutzernamen und Passwort an:
   - Windows: **Dieser PC** > **Netzlaufwerk verbinden** oder **Netzwerkadresse hinzufügen**;
   - macOS: Finder > **Gehe zu** > **Mit Server verbinden**;
   - Linux: im Dateimanager **Mit Server verbinden** oder **Andere Orte**, mit einer Adresse `davs://`.
3. Öffnen Sie den Ordner einer Website. Jede Website der Installation ist ein oberster Ordner; darin liegen **Content**
   und **Media**, die beiden Zweige des Baums.
4. Arbeiten Sie mit den Dateien:
   - **Öffnen oder herauskopieren** Sie eine Datei, um sie zu lesen; Bilder kommen in der Größe, in der sie
     hochgeladen wurden.
   - **Ziehen Sie Dateien** in einen Ordner, um Elemente anzulegen. Die Art der Datei bestimmt die Klasse, zum Beispiel
     wird ein Bild zu einem *Image*, andere Dateien meist zu einem *File*.
   - **Speichern Sie eine Datei über eine vorhandene** mit demselben Namen, um eine neue Version dieses Elements zu
     erzeugen.
   - **Legen Sie einen neuen Ordner an**, um ein Element *Folder* zu erstellen.
   - **Benennen Sie ein Element um oder verschieben Sie es**, um es im Baum umzubenennen oder zu verschieben;
     **Kopieren** kopiert es mit allem darunter.
   - **Löschen** Sie ein Element, um es in den Papierkorb zu schicken.

## Was schiefgehen kann

- **Die Adresse antwortet nicht.** WebDAV ist nicht eingeschaltet, oder der Webserver reicht die Adresse nicht an
  Exponential weiter: Seit 6.0.15 müssen seine Regeln `index_webdav.php` nennen, siehe
  [Das WebDAV-Einstiegsskript heißt jetzt index_webdav.php](../../bc/6.0/webdav-einstiegsskript-index-webdav.md).
  Fragen Sie einen Administrator.
- **Neue Elemente sind in der falschen Sprache.** Über WebDAV erstellte Elemente erhalten die Sprache des
  Website-Ordners, den Sie verwendet haben. Wählen Sie den Ordner der Website in der gewünschten Sprache.
- **Einige Einträge sind leere Dateien.** Elemente ohne eigene Datei, etwa ein Artikel, erscheinen als leere Einträge.
  Bearbeiten Sie diese in der Administration.
- **Eine umbenannte Datei hat ihren Typ verloren.** Behalten Sie beim Umbenennen die Dateiendung bei, sonst kann das
  passende Programm das Element nicht mehr öffnen.
- **Das Löschen hat nur eine Stelle entfernt.** Hat das Element mehrere Orte, bleiben beim Löschen eines Ortes die
  anderen erhalten. Zusätzliche Orte lassen sich über WebDAV nicht hinzufügen.
- **Entwürfe bleiben unberührt.** WebDAV arbeitet nur mit veröffentlichten Inhalten; ein Entwurf, den jemand offen
  gelassen hat, bleibt, wie er ist.

## Verwandte Seiten

- [WebDAV und SOAP](../integratoren/webdav-und-soap.md), wie man WebDAV einschaltet
- [Bilder, Medien und Uploads](../medien-und-uploads.md), die Upload-Seite im Browser
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
