# Rich Text: Zeichen oberhalb von U+FFFF und TeX-Quelltext in eigenen Tags

Diese Seite ist für Administratoren und Entwickler, deren Rich Text (der Datentyp XML-Block, bearbeitet im
Online-Editor) Emoji, mathematische Buchstaben wie `𝔸` (U+1D538) oder `𝑥` (U+1D465) oder LaTeX-Formeln in einem
eigenen Tag enthält.

## Zeichen oberhalb von U+FFFF

XML 1.0 erlaubt die Zeichen #x10000-#x10FFFF. Bis 6.0.15 entfernte die Rich-Text-Eingabe jedes davon, wenn ein Text
in einem Editor geöffnet wurde, und ersetzte jedes beim Speichern durch ein Leerzeichen. Jetzt bleiben sie erhalten,
wo die Datenbank sie speichern kann.

`ezxml.ini`:

```ini
[InputSettings]
# auto (default), enabled or disabled
SupplementaryCharacters=auto
```

| Wert | Wirkung |
|---|---|
| `auto` | Erhalten, außer wenn die Datenbank MySQL oder MariaDB mit einem anderen Verbindungszeichensatz als `utf8mb4` ist. |
| `enabled` | Immer erhalten. |
| `disabled` | Ersetzt wie bisher (beim Öffnen entfernt, beim Speichern ein Leerzeichen). |

Warum `auto` MySQL ausnimmt: `[DatabaseSettings] Charset=utf-8` verbindet sich mit MySQL und MariaDB als `utf8`, was
höchstens drei Byte pro Zeichen aufnimmt. Ein Zeichen oberhalb von U+FFFF, das über diese Verbindung gesendet wird,
wird als `????` gespeichert und lässt im strikten SQL-Modus das ganze Speichern scheitern. Eine Website, deren Tabellen
`utf8mb4` sind, kann `[DatabaseSettings] Charset=utf8mb4` in site.ini oder `SupplementaryCharacters=enabled` in
ezxml.ini setzen. SQLite, PostgreSQL, Oracle und MongoDB speichern sie, wie sie sind.

Text, der kein gültiges UTF-8 ist, leert nicht mehr den ganzen Text: Jede ungültige Bytefolge wird zu U+FFFD.

## TeX-Quelltext in eigenen Tags

`content.ini`:

```ini
[CustomTagSettings]
TeXSource[]
TeXSource[math_latex]=true
```

Der Text eines hier genannten eigenen Tags wird beim Öffnen im Online-Editor so gelesen, wie TeX ihn liest: Ein nicht
maskierter Kommentar mit `%` wird mit dem Rest seiner Zeile, dem Zeilenumbruch und der Einrückung der nächsten Zeile
verworfen. Nach einem Steuerwort bleibt ein Leerzeichen stehen (`\alpha%` gefolgt von `beta` in der nächsten Zeile
ergibt `\alpha beta`). Führender Leerraum wird verworfen, statt als geschütztes Leerzeichen gespeichert zu werden.
Standardmäßig ist kein Tag eingetragen; bei anderen eigenen Tags werden nur die Zeilenumbrüche in Leerzeichen
umgewandelt.

Ein Zeilenumbruch im Wert eines eigenen Attributs (mehrzeiliges MathML) wird bei jedem eigenen Tag zu einem
Leerzeichen, statt zu verschwinden.

## Tests

`eZXMLInputSupplementaryCharactersTest` (Suite kernel-datatypes) und `expOEFormulaTextTest` (Suite ezoe), beide ohne
Datenbank.

## Verwandte Seiten

- [ezoe](erweiterungen/ezoe.md), [Online-Editor auf TinyMCE 8](online-editor-tinymce8.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
