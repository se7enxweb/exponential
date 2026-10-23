# expimageeditor: der Bildeditor von admin4

Diese Seite ist für Redakteure, die Bilder beim Bearbeiten von Inhalten anpassen, und für Administratoren, die den
Editor einschalten. `expimageeditor` ist ein Bildeditor neben dem Bildattribut im Bearbeitungsformular des Designs
`admin4` (und `admin4l`, das damit zeichnet). Er bearbeitet Rasterbilder (Zuschneiden, Drehen, Spiegeln, Größe
ändern, Helligkeit, Kontrast, Schwarzweiß, Sepia, einen Bereich verpixeln oder weichzeichnen, Wasserzeichen) und
SVG-Zeichnungen (Formen, Pfade, Freihand, Text, Füllung und Kontur), und er kann für ein Bildattribut ohne Bild eine
neue SVG-Zeichnung beginnen. Er arbeitet ohne jQuery und unter der Content Security Policy der Administration. Er wird
im Repository des Kernels ausgeliefert, wie `ezjscore` und `ezoe`, und ist aus, bis er aktiviert wird. Hinzugefügt in
6.0.15 (22.10.2026). Die vollständige Referenz ist `extension/expimageeditor/README.md` der Erweiterung (Englisch).

## Einschalten

1. In `settings/override/site.ini.append.php` `[ExtensionSettings] ActiveExtensions[]=expimageeditor`. Schalten Sie
   `ezie` aus: Beide ersetzen dasselbe Include von `content/datatype/edit/ezimage.tpl`.
2. Geben Sie Redakteuren die Richtlinie **expimageeditor/edit** (Administratoren mit `*/*` haben sie). Ohne sie wird
   die Schaltfläche nicht angezeigt.
3. `php bin/php/ezpgenerateautoloads.php -e`, die Caches leeren und mit `--kernel --packer` ausrollen (neue Klassen,
   neue Skripte und Styles).

## Verwenden

1. Bearbeiten Sie in admin4 ein Objekt mit einem Bildattribut. Neben dem Bild **Bild bearbeiten** (oder **Neue
   SVG-Zeichnung**, wenn das Attribut noch kein Bild hat).
2. Wählen Sie ein Werkzeug in der Werkzeugleiste; jedes Werkzeug hat eine Beschriftung, ein Tastenkürzel und ein Feld
   mit seinen Einstellungen. Die Pfeiltasten verschieben eine Auswahl, Escape fragt, bevor Änderungen verworfen werden,
   und der Fokus kehrt zur Schaltfläche zurück, wenn der Dialog schließt.
3. **Speichern** legt das Ergebnis als Bild Ihres Entwurfs ab; das Formular zeigt das neue Bild ohne Neuladen. Die
   veröffentlichte Version behält ihr eigenes Bild, bis Sie den Entwurf veröffentlichen.

## Was der Server beim Speichern prüft

- Sie sind angemeldet, der Entwurf ist **Ihrer**, und Sie dürfen das Objekt in der Sprache des Attributs bearbeiten
  (wie `content/edit` es verlangt); die Anfrage trägt das Formular-Token der Sitzung (`X-CSRF-Token`).
- Die Art bleibt gleich: Ein SVG bleibt ein SVG, ein Rasterbild ein Rasterbild. Der Typ wird am Inhalt erkannt und
  muss in `expimageeditor.ini OutputMIMETypes[]` und `image.ini AllowedMIMETypes[]` stehen.
- Grenzen: die maximale Dateigröße des Klassenattributs, `expimageeditor.ini MaxFileSize` (20 MB) und `MaxPixels`
  (25 Megapixel) sowie `image.ini MaxImagePixels`.
- **Ein Rasterbild wird neu kodiert**: GD dekodiert es und schreibt es neu, sodass nur die Pixel gespeichert werden
  (keine angehängten Daten, keine Metadaten). Die Kopie wartet in `<Cache-Verzeichnis>/expimageeditor/` unter einem
  zufälligen Namen und wird am Ende der Anfrage entfernt. `ReEncodeRaster=disabled` schaltet das ab.
- **Ein SVG wird nur als Ausgabe von `expSvgSanitizer` gespeichert**: kein Skript, kein Ereignisattribut, kein
  `foreignObject`, kein Verweis außerhalb der Zeichnung, kein `javascript:`-Link.

Jede Ablehnung antwortet mit einem HTTP-Status und einer übersetzten Meldung, die der Editor in seiner Statuszeile
zeigt.

## Einstellungen

`expimageeditor.ini [EditorSettings]`: `OutputMIMETypes[]`, `Quality`, `ReEncodeRaster`, `MaxPixels`,
`MaxFileSize`, `NewDrawingSize`, `AspectRatios[]`, `Watermarks[]`, `Fonts[]`, `TextToPaths`. Die Tabelle mit jedem
Standardwert steht in der README der Erweiterung, „Settings“.

## Sprachen und Designs

Englisch und Deutsch (`extension/expimageeditor/translations/ger-DE/`). Die Farben kommen aus den Tokens von admin4,
daher folgt der Dialog dem hellen und dem dunklen Thema. Nur admin4 (und admin4l) hat den Editor; die klassischen
Admin-Designs behalten `ezie`.

## Tests

`php vendor/bin/phpunit --testsuite expimageeditor` (ohne Datenbank: die Prüfungen beim Speichern, das Neukodieren,
die Grenzen, der Sanitizer, das Formular-Token, Template und Skripte unter der Content Security Policy).

## Siehe auch

- [ezie, der Bildeditor der klassischen Admin-Designs](ezie.md)
- [SVG-Bilder](../../../bc/6.0/svg-bilder.md)
- [Content Security Policy](../content-security-policy.md)
