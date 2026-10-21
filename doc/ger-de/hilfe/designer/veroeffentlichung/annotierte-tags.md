---
title: "Annotierte Tags"
audience: designer, developer
category: theme-distribution
order: 120
summary: Releases mit annotierten Tags (git tag -a) markieren, die eine Nachricht, einen Autor und ein Datum tragen; wie Sie die Nachricht schreiben, den Tag vor dem Push prüfen und nur diesen Tag pushen.
keywords: annotierter Tag, git tag -a, Tag-Nachricht, leichtgewichtiger Tag, git show, Tag pushen, Release-Tag, annotated tag, tag message, lightweight tag, push tag, release tag
type: help
---
# Annotierte Tags

Diese Seite ist für Designer, die einen Release-Tag anlegen. Ein **annotierter** Tag ist ein eigenes Objekt mit einer
Nachricht, dem Ersteller und einem Datum; ein **leichtgewichtiger** Tag ist nur ein Name für einen Commit. Verwenden
Sie für Releases annotierte Tags.

Die veröffentlichten Themes von 7x tun das: `git cat-file -t v0.1.6` in `cjw_themes_jumper` gibt `tag` aus, und seine
Nachricht lautet „v0.1.6: the starter design without inline scripts“.

## Schritt für Schritt

1. Stellen Sie sicher, dass Sie auf dem Release-Commit stehen ([Die Version vor dem Tag erhöhen](version-vor-dem-tag-erhoehen.md)):
   `git log --oneline -1`.
2. Legen Sie den Tag mit einer Nachricht der Form `v<version>: <worum es geht>` an:

   ```bash
   git tag -a v1.0.25 -m "v1.0.25: the event pages in dark mode"
   ```

3. Prüfen Sie ihn:

   ```bash
   git cat-file -t v1.0.25        # tag
   git show v1.0.25 --stat | head -20
   ```

   Erwartet: `tag`, dann die Nachricht, dann der Release-Commit mit seinen Dateien.
4. Pushen Sie nur diesen Tag (nie `git push --tags`, das jeden lokalen Tag pusht, auch Experimente):

   ```bash
   git push origin v1.0.25
   ```

## Die Nachricht schreiben

- Beginnen Sie mit der Version und einem Doppelpunkt, dann folgt eine kurze Wendung über das Release.
- Verwenden Sie den Produktnamen „Exponential“ und keine internen Pfade oder privaten Namen.
- Dieselbe Wendung ergibt einen guten Release-Titel ([GitHub-Releases](github-releases.md)).

## Wenn Ihnen vor dem Push ein Fehler unterlaufen ist

Ein Tag, der **noch nicht** gepusht wurde, kann gelöscht und neu angelegt werden: `git tag -d v1.0.25`. Nach dem Push
bleibt er ([Ein gepushter Tag ist endgültig](ein-gepushter-tag-ist-endgueltig.md)).

## Verwandte Seiten

- [Release Notes schreiben](release-notes-schreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
