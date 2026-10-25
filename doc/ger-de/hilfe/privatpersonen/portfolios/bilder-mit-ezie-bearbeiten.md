---
title: Bilder mit ezie bearbeiten
audience: individual, editor
category: personal-sites
order: 1613
summary: Wie eine Person den Bildeditor ezie im Bearbeitungsformular von Exponential nutzt, um zuzuschneiden, zu spiegeln, zu drehen, zu verpixeln, Kontrast und Helligkeit anzupassen, in Schwarzweiß oder Sepia umzuwandeln und ein Wasserzeichen zu setzen - wofür er gut ist, wie das Speichern funktioniert und wann ein Desktop-Programm die bessere Wahl ist.
keywords: ezie, Bildeditor, zuschneiden, drehen, spiegeln, Wasserzeichen, Kontrast, Helligkeit, Schwarzweiß, Sepia, verpixeln, Portfolio, Bearbeitungsformular, GD, ImageMagick, image editor, crop, rotate, flip, watermark, pixelate
type: help
---
# Bilder mit ezie bearbeiten

Diese Seite ist für Privatpersonen, die kleine Änderungen an einem Bild vornehmen möchten, ohne den Browser zu
verlassen. Der Bildeditor `ezie` ist ein eigenes Paket (`composer require se7enxweb/ezie
zetacomponents/image-conversion`; das Kernel-Paket installiert ihn nicht mehr, admin4 hat stattdessen
`expimageeditor`) und funktioniert im Bearbeitungsformular jedes Objekts mit einem Bildfeld, etwa *Image* und
*Gallery*. Die technische Seite ist [ezie: der Bildeditor](../../../funktionen/6.0/erweiterungen/ezie.md).

## Inhalt

1. [Was er kann](#was-er-kann)
2. [Schritt für Schritt](#schritt-für-schritt)
3. [Speichern und Rückgängigmachen](#speichern-und-rückgängigmachen)
4. [Wasserzeichen](#wasserzeichen)
5. [Wann ein Desktop-Programm besser ist](#wann-ein-desktop-programm-besser-ist)
6. [Wenn sich der Editor nicht öffnet](#wenn-sich-der-editor-nicht-öffnet)

## Was er kann

| Werkzeug | Verwenden Sie es, um |
|---|---|
| Auswählen und zuschneiden | Einen Rand zu entfernen, den Ausschnitt zu ändern, ein quadratisches Detail zu erstellen |
| Horizontal spiegeln, vertikal spiegeln | Ein Bild oder eine Auswahl zu spiegeln |
| Drehen | Ein hochkant aufgenommenes Foto aufzurichten |
| Wasserzeichen | Ein registriertes Wasserzeichenbild auf das Bild zu setzen |
| Kontrast, Helligkeit | Kleine Korrekturen vorzunehmen |
| Schwarzweiß, Sepia | Schnelle Filter anzuwenden |
| Verpixeln | Ein Gesicht, ein Kennzeichen oder eine Hausnummer in einer Auswahl unkenntlich zu machen |
| Zoom, Rückgängig, Wiederholen | Genau zu arbeiten und einen Schritt zurückzugehen |

Die Werkzeuge sind auf der Funktionsseite aufgelistet; Tastenkürzel stehen im Titel jedes Werkzeugs.

## Schritt für Schritt

1. Bearbeiten Sie das *Image*-Objekt (oder ein anderes Objekt mit einem Bildfeld).
2. Öffnen Sie den Bildeditor über das Bildfeld.
3. Wählen Sie im Fenster **Aktionen** ein Werkzeug.
4. Für Zuschneiden, Wasserzeichen oder Verpixeln ziehen Sie zuerst eine Auswahl auf: mit festem Seitenverhältnis,
   frei oder mit eingetippter Größe.
5. Anwenden. Machen Sie es rückgängig, wenn das Ergebnis nicht Ihren Vorstellungen entspricht.
6. Klicken Sie auf **Save & Close** (Speichern und schließen). Das Bearbeitungsformular zeigt nun das geänderte Bild.
7. **Veröffentlichen** Sie das Objekt. Bis dahin sehen Besucher weiterhin das alte Bild.

## Speichern und Rückgängigmachen

- **Save & Close** speichert das bearbeitete Bild als neue Version des Bildfelds. Andere Versionen bleiben unberührt.
- **Quit** (Beenden) fragt nach, bevor Ihre Änderungen verworfen werden, und nur, wenn Sie etwas geändert haben.
- Weil das Ergebnis eine neue Version des Objekts ist, können Sie nach dem Veröffentlichen zur vorherigen Version
  zurückkehren ([Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md)).
- Bewahren Sie Ihr unverändertes Original trotzdem auf Ihrem eigenen Computer auf; der Editor arbeitet mit dem, was
  hochgeladen wurde.

## Wasserzeichen

Ein Wasserzeichen ist ein Bild (meist ein PNG mit Transparenz), das auf ein Foto gesetzt wird. `ezie` bietet nur
Wasserzeichen an, die in den Einstellungen registriert sind: eine Datei in einem Ordner
`design/standard/images/watermarks` einer Erweiterung und eine Zeile `watermarks[]=<file name>` in der
`image.ini.append.php` dieser Erweiterung unter `[eZIE]`, danach ein Leeren des Caches. Die mitgelieferten
Wasserzeichen sind Beispiellogos; für Ihr eigenes Zeichen fügen Sie Ihre Datei auf diese Weise hinzu
([ezie, Ein eigenes Wasserzeichen hinzufügen](../../../funktionen/6.0/erweiterungen/ezie.md#ein-eigenes-wasserzeichen-hinzufügen)).

Zwei Dinge sollten Sie wissen:

- Wasserzeichen werden pro Bild von Hand gesetzt. Nichts versieht jeden Upload automatisch damit.
- Ein Wasserzeichen schreckt vom Kopieren ab; es verhindert es nicht.

## Wann ein Desktop-Programm besser ist

| Aufgabe | Verwenden Sie |
|---|---|
| Zuschneiden, Drehen, ein schnelles Wasserzeichen, ein Detail verbergen | `ezie` |
| Farbkorrektur, Retusche, Ebenen | Ein Fotoprogramm für Desktop oder Smartphone |
| Hunderte Dateien verkleinern | Ein Stapelverarbeitungswerkzeug, bevor Sie [viele Bilder hochladen](viele-bilder-hochladen.md) |
| Standortdaten entfernen | Die Exportoptionen Ihres Fotoprogramms, vor dem Hochladen |

## Wenn sich der Editor nicht öffnet

- Der Editor braucht GD2 oder ImageMagick auf dem Server; der Einrichtungsassistent prüft, ob eines davon vorhanden
  ist ([Bildverarbeitung und Medien](../../hosting/grundlagen/bildverarbeitung-und-medien.md)).
- Er braucht die Skripte des Bearbeitungsformulars der Administration oder ein Design, das sie lädt.
- Laden Sie die Seite nach einem Update einmal neu; Ihr Browser hält womöglich alte Skripte vor.
- Wenn ein Werkzeug mit einem Fehler abbricht, notieren Sie die Uhrzeit und bitten Sie Ihren Hoster oder Partner,
  ins Protokoll zu sehen ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)).

## Verwandte Seiten

- [Portfolios](README.md)
- [ezie: der Bildeditor](../../../funktionen/6.0/erweiterungen/ezie.md)
- [Eine Bildergalerie aufbauen](eine-bildergalerie-aufbauen.md)
- [Bildvarianten für Portfolios](bildvarianten-fuer-portfolios.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
