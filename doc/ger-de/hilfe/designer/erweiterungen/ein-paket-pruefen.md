---
title: "Ein Paket vor dem Weitergeben prüfen"
audience: designer, admin
category: theme-extensions
order: 420
summary: Ein Paket so prüfen, wie eine Installation es prüfen wird, bevor Sie es weitergeben - die Schaltfläche Prüfen, ezpm validate, die Upload-Regeln (gzip, Größen, Einträge, sichere Pfade, wohlgeformte package.xml) und ein Blick von Hand ins Archiv.
keywords: prüfen, Paketprüfung, ezpm validate, Upload-Prüfung, package.xml, xmllint, tar, Prüfsummen, sichere Pfade, validate, package validation, upload inspector, checksums, safe paths
type: help
---
# Ein Paket vor dem Weitergeben prüfen

Diese Seite ist für Designer, die gleich ein `.ezpkg` veröffentlichen. Die empfangende Installation untersucht jeden
Upload, bevor sie etwas schreibt; ein Paket, das dort scheitert, scheitert für Ihre Nutzer. Prüfen Sie es zuerst.

## Was eine Installation beim Upload prüft

1. Der Name endet auf eine erlaubte Endung (`ezpkg`, `tar.gz`, `tgz`), und die Datei beginnt mit der gzip-Signatur.
2. Sie liegt innerhalb von `MaxArchiveSize` (standardmäßig 256 MiB), `MaxEntries` (50.000) und `MaxUnpackedSize` (2 GiB).
3. Jeder Eintrag ist eine einfache Datei oder ein Verzeichnis mit relativem Pfad: kein `..`, kein absoluter Pfad, kein
   Backslash, kein Steuerzeichen, kein Link, kein Gerät.
4. Eine wohlgeformte `package.xml` (oder `package.json`) liegt oben, mit `<package>` als Wurzel, einem gültigen Namen,
   Version und Paketierungsangaben und einem Anbieter, der einen Repository-Namen ergibt.
5. In keinem Repository gibt es schon ein Paket dieses Namens.

Dann muss jedes Dokument wohlgeformt sein und bei einem JSON-Paket dem Schema und seinen Prüfsummen entsprechen.

## Schritt für Schritt

1. Öffnen Sie auf Ihrer Entwicklungsinstallation die Seite des Pakets und drücken Sie **Prüfen**. Sie zeigt, was geprüft
   wurde, oder jedes Problem.
2. Prüfen Sie auf der Kommandozeile die exportierte Datei selbst:

   ```bash
   php ezpm.php validate var/tmp/mytheme_classes-1.0.0-1.ezpkg
   ```

3. Sehen Sie hinein:

   ```bash
   tar tzvf var/tmp/mytheme_classes-1.0.0-1.ezpkg | head -30
   ```

   Jeder Pfad ist relativ und liegt unterhalb des Pakets; keine Einträge `l` (Link) in der ersten Spalte.
4. Entpacken Sie die Definition und prüfen Sie sie:

   ```bash
   mkdir -p var/tmp/pkgcheck && tar xzf var/tmp/mytheme_classes-1.0.0-1.ezpkg -C var/tmp/pkgcheck
   xmllint --noout var/tmp/pkgcheck/package.xml
   ```

   Erwartet: keine Ausgabe von `xmllint`.
5. Importieren Sie es in eine Wegwerf-Installation ([Ihr Paket auf einer Testseite installieren](ihr-paket-auf-einer-testseite-installieren.md)).

## Wenn die Prüfung fehlschlägt

| Die Meldung nennt | Abhilfe |
|---|---|
| Einen Pfad außerhalb des Pakets oder einen Link | Erneut mit `ezpm export` aus dem Paketverzeichnis exportieren |
| `package.xml` nicht wohlgeformt | `xmllint` darauf ausführen und die genannte Zeile korrigieren |
| „existiert bereits“ (auf der empfangenden Seite) | Der Nutzer entfernt zuerst das alte, oder Sie geben dem Paket einen neuen Namen |

## Verwandte Seiten

- [Pakete, Abschnitt 6: hochladen](../../../anleitungen/pakete.md#6-ein-paket-hochladen)
- [Klassische XML- und JSON-Pakete](klassische-und-json-paketformate.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
