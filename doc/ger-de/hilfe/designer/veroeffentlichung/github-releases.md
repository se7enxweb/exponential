---
title: "GitHub-Releases"
audience: designer, developer
category: theme-distribution
order: 140
summary: Ein GitHub-Release für einen gepushten Tag mit gh release create anlegen - Titel, Notes aus dem Changelog, Markierung als Latest -, es prüfen und wissen, was ein Release zu einem Tag hinzufügt.
keywords: GitHub-Release, gh release create, Release Notes, Latest, Release-Titel, notes-file, verify-tag, GitHub release, release notes, release title
type: help
---
# GitHub-Releases

Diese Seite ist für Designer, die einen Release-Tag gepusht haben. Ein GitHub-Release fügt einem Tag einen Titel, Notes
und (optional) Dateien hinzu und macht die Version auf der Startseite des Repositorys sichtbar.

## Schritt für Schritt

1. Schreiben Sie die Notes in eine Datei in Ihrem Arbeitsordner, zum Beispiel `var/tmp/notes-1.0.25.md`
   ([Release Notes schreiben](release-notes-schreiben.md)).
2. Legen Sie das Release für den gepushten Tag an:

   ```bash
   gh release create v1.0.25 --verify-tag \
       --title "v1.0.25: the event pages in dark mode" \
       --notes-file var/tmp/notes-1.0.25.md
   ```

   `--verify-tag` weigert sich, ein Release anzulegen, wenn der Tag nicht auf dem Remote ist, sodass `gh` nie aus
   irgendeinem gerade aktuellen Commit einen Tag für Sie anlegt.
3. Prüfen Sie es:

   ```bash
   gh release view v1.0.25
   gh release list --limit 3
   ```

   Erwartet: Ihr Titel und Ihre Notes und das neue Release als **Latest** markiert.

## Titel, die die veröffentlichten Themes verwenden

`gh release list -R se7enxweb/cjw_themes_jumper` zeigt Titel wie „v0.1.6: the starter design without inline scripts“
und „v0.1.4: The map popup escapes the address and title“: die Version, ein Doppelpunkt und worum es im Release geht.
Verwenden Sie denselben Text wie in der Tag-Nachricht.

## Dateien an einem Release

Ein mit Composer installiertes Theme braucht keine Dateien am Release: Composer lädt das Archiv des Tags herunter.
Hängen Sie Dateien nur an, wenn Nutzer etwas anderes brauchen (ein `.ezpkg`, einen Satz Screenshots). Eine angehängte
Datei ist Teil des Releases; ersetzen Sie sie später nie ([Ein gepushter Tag ist endgültig](ein-gepushter-tag-ist-endgueltig.md)).

## Verwandte Seiten

- [Release Notes schreiben](release-notes-schreiben.md)
- [Packagist automatisch aktuell halten](packagist-automatisch-aktualisieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
