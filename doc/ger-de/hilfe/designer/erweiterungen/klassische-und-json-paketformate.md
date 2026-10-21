---
title: "Klassische XML- und JSON-Pakete"
audience: designer, developer
category: theme-extensions
order: 400
summary: Die beiden Formate, in denen ein Exponential-Paket gespeichert werden kann - klassisch package.xml und JSON package.json -, was sich unterscheidet (Prüfsummen, Dateinamen, Schema), wann JSON einem Designer hilft und wie Sie zwischen ihnen umwandeln.
keywords: Paketformat, JSON-Paket, package.json, package.xml, klassisches Format, umwandeln, SHA-256, JsonML, ezpm convert, json.ezpkg, package format, JSON package, classic format, convert
type: help
---
# Klassische XML- und JSON-Pakete

Diese Seite ist für Designer, die im Paket-Assistenten gefragt werden: „Classic XML oder JSON?“ Beide Formate enthalten
dasselbe Paket, Element für Element, und installieren sich auf dieselbe Weise. Das klassische Format bleibt der Standard.

## Die Unterschiede

| | Klassisch | JSON |
|---|---|---|
| Definition | `package.xml` | `package.json` |
| Dokumente | `ezcontentclass/class-article.xml` ... | dieselben Namen mit `.json` |
| Andere Dateien (Bilder, Templates, Erweiterungsdateien) | unverändert | unverändert, Byte für Byte |
| Prüfsummen | MD5 nur der Design-Dateien | SHA-256 jeder Datei |
| Archivname | `<name>-<version>-<release>.ezpkg` | `<name>-<version>-<release>.json.ezpkg` |
| Schema | der Leser des Kernels | `share/package/schema/package-v1.json` |

Das Format wird aus dem Inhalt erkannt (welche Definitionsdatei oben liegt), nie aus dem Dateinamen.

## Wann JSON einem Designer hilft

- Sie prüfen Pakete in Pull-Requests: JSON-Diffs lesen sich für viele Menschen und Werkzeuge besser.
- Sie möchten, dass jede Datei von einer Prüfsumme abgedeckt ist, damit eine geänderte oder fehlende Datei gefunden wird,
  bevor etwas installiert wird.
- Ihre eigenen Skripte lesen oder schreiben Pakete.

Bleiben Sie beim klassischen Format, wenn die Menschen, die Ihr Paket installieren, ältere Werkzeuge verwenden, die nur
`package.xml` kennen.

## Schritt für Schritt: wählen oder umwandeln

1. Wählen Sie im Schritt **Paket Informationen** des Assistenten **Classic XML** oder **JSON**.
2. Um später das andere Format herunterzuladen, verwenden Sie auf der Seite des Pakets **Als JSON herunterladen** (oder
   **Herunterladen**).
3. Wandeln Sie auf der Kommandozeile ein Archiv um:

   ```bash
   php ezpm.php convert mytheme_classes-1.0-1.ezpkg var/tmp/mytheme_classes-1.0-1.json.ezpkg
   ```

   `php ezpm.php help convert` erklärt das Ziel (ein Archiv oder ein neues Verzeichnis) und `--to`.
4. Prüfen Sie das Ergebnis: `php ezpm.php validate var/tmp/mytheme_classes-1.0-1.json.ezpkg`.

## Verwandte Seiten

- [Das JSON-Paketformat](../../../bc/6.0/package-json-format.md)
- [Ein Paket vor dem Weitergeben prüfen](ein-paket-pruefen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
