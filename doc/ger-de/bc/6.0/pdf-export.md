# Upgrade des PDF-Exports

Lesen Sie diese Seite, wenn Sie eine Installation aktualisieren, die den **PDF-Export** verwendet (`content/pdf`, die
Seiten des PDF-Exports in der Administration). Der Export hat eine konfigurierbare Fußzeile, bessere Schriften und
eine Skalierung von Bildern erhalten. Das Upgrade braucht zwei Datenbankspalten, und einige Standardwerte von
`pdf.ini` haben sich geändert. Die Funktion selbst ist unter [PDF-Export](../../funktionen/6.0/pdf-export.md)
beschrieben.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | `ezpdf_export` hat zwei neue Spalten; sechs Standardwerte in `[PDFGeneral]` sind neu oder geändert; `footer.tpl` liest die neuen Einstellungen der Fußzeile. |
| Wer betroffen ist | Jede Installation, die PDF-Exporte hält. Installationen, die `OutputCharset` oder `content/pdf/footer.tpl` überschreiben. |
| Wie Sie es prüfen | Suchen Sie `show_footer` in der Tabelle `ezpdf_export` und Overrides von `OutputCharset` und `footer.tpl`. |
| Wie Sie es beheben | Wenden Sie das Datenbank-Update an; bringen Sie `OutputCharset` mit `FontEncoding` in Einklang; aktualisieren Sie ein überschriebenes `footer.tpl`. |

## Schritt 1: die Datenbank aktualisieren

`ezpdf_export` erhält zwei Spalten. Die Anweisungen stehen in
`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`; die Datei gibt es für `mysql`, `postgresql` und `sqlite`.
Dieselbe Datei enthält auch die Spalten für OPML und Podcasts. Wenden Sie die ganze Datei einmal an, in der
Reihenfolge, und nur die Anweisungen, die Ihre Datenbank noch nicht hat.

```sql
-- mysql
ALTER TABLE ezpdf_export ADD COLUMN show_footer int(11) NOT NULL DEFAULT 1;
ALTER TABLE ezpdf_export ADD COLUMN footer_text varchar(255) NOT NULL DEFAULT '';
-- sqlite (same two columns; int type is `integer`, text `varchar(255)`)
ALTER TABLE ezpdf_export ADD COLUMN show_footer integer NOT NULL DEFAULT 1;
ALTER TABLE ezpdf_export ADD COLUMN footer_text varchar(255) NOT NULL DEFAULT '';
-- postgresql
ALTER TABLE ezpdf_export ADD COLUMN show_footer integer NOT NULL DEFAULT 1;
ALTER TABLE ezpdf_export ADD COLUMN footer_text character varying(255) NOT NULL DEFAULT '';
```

Eine Neuinstallation erhält die Spalten aus `share/db_schema.dba`. Vorhandene Exporte zeigen weiterhin den
ausgelieferten Text der Fußzeile (`show_footer=1`, leerer Text).

## Schritt 2: die geänderten Einstellungen prüfen

Datei `settings/pdf.ini`, Block `[PDFGeneral]`, Geltungsbereich: Installation.

| Schlüssel | Vorher | Jetzt |
|---|---|---|
| `OutputCharset` | `iso-8859-1` | `windows-1252` |
| `Transliterate` | gab es nicht | `enabled` |
| `LinkUnderline` | gab es nicht | `enabled` |
| `ImageScaling` | gab es nicht | `enabled` |
| `MaxImageWidth`, `MaxImageHeight` | gab es nicht | leer (Seitengröße) |
| `FontEncoding` | gab es nicht | `WinAnsiEncoding` |

- Wenn Sie `OutputCharset` überschreiben, stellen Sie sicher, dass es weiterhin zu `FontEncoding` passt.
- Wenn Sie sich darauf verlassen, dass Bilder in ihrer Pixelgröße gezeichnet werden, setzen Sie `ImageScaling=disabled`.

Prüfen Sie Ihre Overrides:

```bash
grep -rn "OutputCharset\|ImageScaling\|FontEncoding" settings/override settings/siteaccess extension/*/settings 2>/dev/null
```

Keine Ausgabe bedeutet, dass Sie die ausgelieferten Standardwerte verwenden.

## Schritt 3: ein überschriebenes Template der Fußzeile aktualisieren

`design/standard/templates/content/pdf/footer.tpl` liest die Einstellungen der Fußzeile des Exports. Ein Override
dieses Templates funktioniert weiter, zeigt den neuen Text der Fußzeile aber erst, wenn es `show_footer` und
`footer_text` liest. Kopieren Sie diese Teile aus dem ausgelieferten Template in Ihr Override.

## Verwandte Seiten

- [PDF-Export](../../funktionen/6.0/pdf-export.md)
- [September 2026, erste Hälfte: 14. September](../../geschichte/2026/2026-09a.md#14-september-pdf-rss-und-die-rad-werkzeuge)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Upgrade von OPML](opml.md), das dieselbe Datei für das Datenbank-Update verwendet
