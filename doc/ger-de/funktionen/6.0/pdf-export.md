# PDF-Export

Diese Seite ist für Redakteure und Administratoren, die Inhalte in PDF-Dateien umwandeln. Der PDF-Export liegt unter
**Setup > PDF-Export** (`/pdf/list`, Bearbeitungsseite `/pdf/edit`, Richtlinie `pdf/edit`). In 6.0 erzeugt er Dateien,
die sich öffnen lassen, Text, der richtig lesbar ist, Bilder, die auf die Seite passen, echtes UTF-8 mit einer
TrueType-Schrift und eine Fußzeile, die Sie selbst formulieren.

**Aktualisieren Sie?** Führen Sie zuerst die Datenbankaktualisierung aus: siehe
[Aktualisierung des PDF-Exports](../../bc/6.0/pdf-export.md).

## Einen Export mit eigener Fußzeile erstellen

1. Öffnen Sie **Setup > PDF-Export** und legen Sie einen Export an oder bearbeiten Sie einen.
2. Haken Sie **Show footer** an und geben Sie Ihren Wortlaut in **Fußzeilentext** ein, zum Beispiel „Example Ltd,
   Produktblatt“.
3. Speichern und exportieren Sie. Jede Seite trägt Ihre Zeile statt „Exponential PDF export“.

| Feld | Spalte in `ezpdf_export` | Standard | Bedeutung |
|---|---|---|---|
| Show footer | `show_footer` (int) | 1 | Ob die Fußzeile überhaupt eine Textzeile trägt |
| Fußzeilentext | `footer_text` (varchar 255) | leer | Der Wortlaut. Leer bei angehaktem Kästchen behält den mitgelieferten Wortlaut. |

Vorher standen die Worte fest in `content/pdf/footer.tpl`. Ein vor dieser Änderung erstellter Export sieht genau so aus
wie bisher.

## Jede Sprache drucken: eine TrueType-Schrift verwenden

Die mitgelieferten Schriften enthalten einen Ein-Byte-Zeichensatz. Für Unicode-Text richten Sie `Font` auf eine
`.ttf`-Datei:

```ini
# settings/override/pdf.ini.append.php
[PDFGeneral]
Font=/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf
```

Die Schrift wird als zusammengesetzte Schrift eingebettet, UTF-8 geht unverändert durch, und der Text bleibt markierbar
und durchsuchbar. DejaVu Sans deckt Lateinisch, erweitertes Lateinisch, Griechisch, Kyrillisch, Hebräisch, Armenisch und
Georgisch ab. Für Chinesisch, Japanisch oder Koreanisch richten Sie `Font` auf eine Schrift, die diese enthält (etwa eine
Noto-CJK-Schrift).

## Was repariert wurde

- Der Export konnte überhaupt keine Datei erzeugen, und wenn doch, waren die Seiten unlesbar. Die Bibliothek unter
  `lib/ezpdf` schreibt jetzt gültige Dateien.
- HTML-Entitäten und Link-Adressen kamen unverarbeitet auf die gedruckte Seite; jetzt werden sie vor dem Zeichnen
  dekodiert.
- Akzente, Gedankenstriche, Eurozeichen, Aufzählungspunkte und typografische Anführungszeichen überstehen die
  Standardschrift (siehe `OutputCharset` unten).
- Bilder werden verkleinert, damit sie auf die Seite passen, statt über das Papier hinauszulaufen.
- Unterstreichungen unter Links sind so lang wie der Link.
- Ein Export mit einer Tabelle antwortet unter PHP 8 wieder: Der Tabellenoperator zählte einen booleschen Wert statt
  seiner Parameter, was PHP 8 mit einem `TypeError` zurückweist, sodass `content/pdf` mit 500 antwortete (behoben am
  12.10.2026).

## Einstellungen

Alle Schlüssel stehen in `settings/pdf.ini`, Block `[PDFGeneral]`. Geltungsbereich: global. Prüfen Sie sie mit
`grep -v '^#' settings/pdf.ini`. Die Blöcke `[Header]` und `[Footer]` (Ränder, Linienstärke) sind älter und unverändert.

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `OutputCharset` | `windows-1252` | Der Zeichensatz, in dem Text geschrieben wird. Wird ignoriert, wenn `Font` eine `.ttf`-Datei ist. Für die mitgelieferten Schriften muss er ein Ein-Byte-Zeichensatz sein; `utf-8` wird dort abgelehnt (Fehler im Debug-Protokoll) und `windows-1252` verwendet. Ändern Sie ihn nur zusammen mit `FontEncoding`. War `iso-8859-1`, was Zeichen im Bereich 0x80 bis 0x9F verlor. |
| `Transliterate` | `enabled` | Ein Zeichen, das der Zeichensatz nicht enthält, auf das nächstliegende ASCII zurückführen (Lódz statt eines Fragezeichens). Wird bei einer `.ttf`-Schrift ignoriert. |
| `LinkUnderline` | `enabled` | Unter Links zusätzlich zur Farbe eine Linie zeichnen |
| `MaxImageWidth` | leer | Größte Bildbreite in Punkt (72 pro Zoll). Leer bedeutet so viel, wie die Seite Platz hat, Papier abzüglich Ränder. |
| `MaxImageHeight` | leer | Dasselbe für die Höhe |
| `ImageScaling` | `enabled` | Bilder unter Beibehaltung der Proportionen verkleinern. `disabled` zeichnet sie in der übergebenen Größe. |
| `Format`, `Orientation`, `TopMargin`, `BottomMargin`, `LeftMargin`, `RightMargin` | `A4`, `portrait`, `80`, `100`, `80`, `80` | Seitengröße und Ränder, von dieser Arbeit unverändert |
| `Font` | `lib/ezpdf/classes/fonts/Helvetica` | Eine mitgelieferte Schrift (Helvetica, Times-Roman, Courier) oder der vollständige Pfad einer `.ttf`-Datei |
| `FontEncoding` | `WinAnsiEncoding` | Welche Glyphe jeder der 256 Plätze einer einfachen Schrift enthält: `WinAnsiEncoding` (windows-1252), `MacRomanEncoding` (mac-roman) oder `StandardEncoding` |

Die Liste der Exporte wird über `admininterface.ini [PaginationSettings] ItemsPerPage[pdf/list]` seitenweise angezeigt
([Einstellungen zum Blättern](../../bc/6.0/einstellungen-fuer-seitengroessen.md)).

## Verwandte Seiten

- [Aktualisierung des PDF-Exports](../../bc/6.0/pdf-export.md) (Datenbankspalten, geänderte Standards)
- [Blättern, Sortieren und Seitengrößen](seitenweise-listen-im-admin.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik: September 2026, erste Hälfte, 14. September](../../geschichte/2026/2026-09a.md#14-september-pdf-rss-und-die-rad-werkzeuge)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
