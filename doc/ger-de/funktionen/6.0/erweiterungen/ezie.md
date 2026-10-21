# ezie: der Bildeditor

Diese Seite ist für Redakteure, die beim Bearbeiten von Inhalten Bilder anpassen, und für Administratoren, die
Wasserzeichen hinzufügen. `ezie` („eZ Image Editor“) ist ein Bildeditor im Bearbeitungsformular jedes Inhaltsobjekts, das
ein Bildattribut hat. Zuschneiden, spiegeln, drehen, ein Wasserzeichen hinzufügen, Kontrast und Helligkeit ändern, dann
**Save & Close**: Das bearbeitete Bild wird als neue Version des Attributs gespeichert.

Die Releases von September bis Oktober 2026 (6.0.3 bis 6.0.8) reparierten ihn für aktuelles PHP und jQuery, behoben
mehrere Fehler, die Werkzeuge scheitern ließen, und schlossen zwei Sicherheitslücken. Wenn Sie den Editor früher probiert
und aufgegeben haben, versuchen Sie es erneut.

## Anforderungen

Exponential 6, PHP 8.1 oder neuer, GD2 oder ImageMagick und ein Bearbeitungsformular von Exponential (Admin oder ein
Design, das die Skripte des Bearbeitungsformulars lädt). Er verwendet jQuery, jQuery UI, die Plugins Jcrop und
colorpicker und die Bildkonvertierung der Zeta Components.

## Verwenden

1. Bearbeiten Sie ein Objekt mit einem Bildattribut und öffnen Sie den Editor aus dem Bildfeld.
2. Wählen Sie im Fenster **Aktionen** ein Werkzeug: auswählen, zuschneiden, spiegeln, drehen, Wasserzeichen, Kontrast,
   Helligkeit. Tastenkürzel werden im Titel jedes Werkzeugs gezeigt.
3. Arbeiten Sie an der Auswahl (Seitenverhältnis beibehalten, frei oder eine Größe eingeben), wenden Sie an und machen Sie
   bei Bedarf rückgängig.
4. Klicken Sie auf **Save & Close**. Das Bearbeitungsformular zeigt das gespeicherte Bild überall dort, wo das Attribut
   steht.

**Quit** stellt eine übersetzte Frage („If you leave without saving, all your modifications will be definitely lost“),
und nur, wenn etwas geändert wurde.

### Was wo läuft

Die Serverseite jedes Werkzeugs ist eine Ansicht des Moduls `ezie` (`module.php`):

| Ansicht | Zweck |
|---|---|
| `prepare` | öffnet die Bearbeitungssitzung |
| `tool_crop`, `tool_flip_hor`, `tool_flip_ver`, `tool_rotation`, `tool_pixelate`, `tool_watermark` | Werkzeuge |
| `filter_bw` (Schwarz-Weiß), `filter_sepia`, `filter_contrast`, `filter_brightness` | Filter |
| `save_and_quit`, `no_save_and_quit` | die beiden Wege hinaus |

Auswählen, Zoomen, Rückgängig und Wiederholen arbeiten im Browser
(`design/standard/javascript/ezie.gui.config.bind.tool_*.js`). Der Editor hat also auch Verpixeln, Schwarz-Weiß und
Sepia, die die Werkzeugliste oben nicht nennt.

Die mitgelieferten Skripte Dritter werden in `ezinfo.php` genannt (jQuery UI 1.8.9, Jcrop 0.9.8, jQuery Hotkeys 0.7.9);
der Farbwähler liegt in `design/standard/javascript/colorpicker`. jQuery selbst kommt aus `ezjscore`.

### Ein eigenes Wasserzeichen hinzufügen

1. Legen Sie das Bild (meist eine PNG-Datei) in einen Ordner `design/standard/images/watermarks` Ihrer Extension, zum
   Beispiel `extension/mydesign/design/standard/images/watermarks/logo_ubuntu.png`.
2. Registrieren Sie es in `extension/mydesign/settings/image.ini.append.php`:

```ini
[eZIE]
watermarks[]=logo_ubuntu.png
```

3. Leeren Sie die Caches. Das Wasserzeichen wird im Editor angeboten.

Angenommen wird nur ein einfacher Dateiname, der in einem Ordner `design/standard/images/watermarks` existiert. Die
mitgelieferten sind `elephpant.png` und `ez-logo.png`.

## Was behoben wurde

| Release | Problem | Behoben |
|---|---|---|
| 6.0.3 | Der Editor forderte eine Datei `jquery-migrate-1.1.1.min.js` an, die niemand mitliefert | Die Anforderung ist weg |
| 6.0.6 | Websites, die **WebP** als Ausgabe erlauben (`image.ini [OutputSettings] AllowedOutputFormat` enthält `image/webp`): Die Handler für ImageMagick und GD können es nicht schreiben, sodass jedes Werkzeug mit 500 antwortete | Nur Ausgabeformate, die der gewählte Konverter unterstützt, werden weitergegeben (JPEG, PNG und GIF, wenn keines übrig bleibt). Die Prüfung von GD2 las die falsche Einstellung und protokollierte bei jedem Aufruf eine undefinierte Variable |
| 6.0.6 | GD-Handler unter **PHP 8.5**: `imagedestroy()` veraltet; Verpixeln übergab gebrochene Größen | Keine Deprecations; Pixelgrößen sind ganze Zahlen von mindestens eins |
| 6.0.6 | Ein **horizontales Spiegeln** mit ImageMagick spiegelte das ganze Bild (`-flop` ignoriert `-region`), während GD die Auswahl spiegelte | Die Auswahl wird ausgeschnitten, gespiegelt und wieder eingesetzt |
| 6.0.6 | Weichzeichnen, Tonwerte und Sättigung antworteten mit einem fatalen Fehler (Klassen fehlten) | Die Ansichten sind entfernt; der Editor bietet sie nicht mehr an |
| 6.0.6 | Arbeitsordner blieben im öffentlichen Cache, wenn die Bearbeitungsseite geschlossen wurde, ohne den Editor zu schließen | Sie werden entfernt; das Öffnen des Editors entfernt Ihre eigenen Ordner, die älter als ein Tag sind |
| 6.0.6 | Das Öffnen des Editors scheiterte unter jQuery 3.7 mit `e.indexOf is not a function` (Kurzform `.load()` entfernt) | Mit `.on('load')` gebunden |
| 6.0.6 | Auswählen, Zuschneiden und Wasserzeichen warfen Fehler, bevor der Auswahlrahmen erschien (Jcrop 0.9.8 las das entfernte `$.browser`) | Behoben |
| 6.0.6 | Der Kasten mit dem Vorschaubild ließ sich nach dem Abkoppeln nicht wieder andocken | Behoben |
| 6.0.6 | Der Editor öffnete sich unter der linken Spalte der Administrationsoberfläche, und die Eingabetaste in seinen Feldern sendete den Entwurf ab | Öffnet sich über der Administrationsoberfläche, nebeneinander und zentriert, passt ins Fenster; lange Bereiche scrollen |
| 6.0.6 | Stile des Schiebereglers forderten fehlende Bilder an (404) | Verwenden das mitgelieferte Icon-Blatt |
| 6.0.6 | Handler der Werkzeuge wurden bei jedem Öffnen erneut gebunden („Select (s) (s) (s)“), das aktuelle Werkzeug nicht hervorgehoben, die Kürzel `i` und `1` warfen Fehler | Handler mit Namensraum; Bindungen ohne Funktion übersprungen |
| 6.0.6 | Die Auswahloptionen „Keep ratio“, „Free“ warfen jedes Mal eine Ausnahme | Funktionieren |
| 6.0.6 | Eine fehlgeschlagene Aktion zeigte nur „image not loaded“, und Schließen verwarf alle Änderungen | Der Fehlerdialog zeigt den Grund des Servers, und Schließen verbirgt ihn nur |
| 6.0.7 | jQuery 4 und jQuery UI 1.14 | Öffnen, Werkzeuge, Rückgängig, Auswahl und Wasserzeichen funktionieren |

## Sicherheit (6.0.6)

* Der Name des Wasserzeichens aus der Anfrage wurde früher an einen Ordnerpfad angehängt, sodass `../` jedes Bild las,
  das der Webserver lesen kann. Angenommen wird nur ein einfacher Dateiname, der im Ordner der Wasserzeichen gefunden
  wird.
* Der Arbeitsordner einer Bearbeitung kam aus einem `key`, den der Browser sendete, sodass jeder Benutzer mit Zugriff auf
  das Modul `ezie` die Werkzeuge in jeden Ordner unter der Installation schreiben und `no_save_and_quit` jeden Ordner
  rekursiv löschen lassen konnte. Der Ordner wird jetzt aus dem aktuellen Benutzer und dem Bild gebaut; der Schlüssel wird
  ignoriert.
* Nur `prepare` prüfte Berechtigungen, mit dem in eine Ganzzahl umgewandelten Sprachcode, was die Spracheinschränkung
  abschaltete. Jede Ansicht verlangt jetzt, dass das Bildattribut zu einem **Entwurf des aktuellen Benutzers** gehört, den
  der Benutzer in dieser Sprache bearbeiten darf, und `prepare` prüft, dass das Attribut zum Objekt und zur Sprache in
  seiner URL gehört. Eine veröffentlichte Version lässt sich nicht mehr über `save_and_quit` ändern.
* Fehler antworten mit JSON `{"error": ...}` und 400, 403, 404 oder 500 (vorher: eine HTML-Fehlerseite, ein Text aus
  `die()` oder eine 200 mit einem kaputten Bild). Anfragewerte werden nur aus POST gelesen und geprüft (Zahlen, eine
  sechsstellige Farbe, eine Auswahl mit vier positiven Zahlen).
* Der Editor sendet das Formular-Token als Feld `ezxform_token` und als Header `X-CSRF-Token`, gelesen für jede Anfrage
  aus dem verborgenen span oder aus dem Meta-Tag `csrf-token`.

## Sprachen

Die Extension enthält Übersetzungsdateien in `translations/<locale>/translation.ts`: cro-HR, ell-GR, eng-US, esl-ES,
fre-FR, ger-DE, ita-IT, jpn-JP, pol-PL, por-BR. Die deutsche Datei enthält 47 Meldungen (zählen Sie `<message` in
`translations/ger-DE/translation.ts`). Die Texte werden in den Kontexten `design/standard/ezie` und
`design/standard/content/datatype` nachgeschlagen.

Erneuern Sie nach dem Bearbeiten einer Übersetzungsdatei den kompilierten Übersetzungs-Cache mit
`./console exp:ezgeneratetranslationcache` und leeren Sie die Template- und Inhalts-Caches.
`./console exp:ezchecktranslation ger-DE` gibt Statistiken zur Datei `share/translations/ger-DE/translation.ts` des
Kernels aus, nicht zur Datei dieser Extension.

## Verwandte Seiten

- [Entfernung von YUI und jQuery 4](../../../bc/6.0/yui-entfernung.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/ezie.md) und [Versionshinweise](../../../../changelogs/extensions/ezie.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezie.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2024-03](../../../geschichte/erweiterungen/monate/2024-03.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
