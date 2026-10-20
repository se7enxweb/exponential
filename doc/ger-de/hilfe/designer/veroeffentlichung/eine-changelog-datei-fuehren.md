---
title: "Eine CHANGELOG.md führen"
audience: designer
category: theme-distribution
order: 180
summary: Die CHANGELOG.md eines Themes Release für Release pflegen - Einträge aus dem Git-Log seit dem letzten Tag sammeln, nach Präfix sortieren, die Überschrift im Release-Commit hinzufügen und Datei und GitHub-Releases stimmig halten.
keywords: CHANGELOG.md, Changelog-Datei, git log, seit dem letzten Tag, Release-Historie, Changelog pflegen, changelog file, since last tag, release history, maintain changelog
type: help
---
# Eine CHANGELOG.md führen

Diese Seite ist für Designer, die regelmäßig veröffentlichen. Die Changelog-Datei reist mit dem Theme in jede
Installation; ein GitHub-Release nicht. Führen Sie beides.

## Schritt für Schritt bei jedem Release

1. **Sammeln Sie die Änderungen seit dem letzten Release**:

   ```bash
   git log --oneline "$(git describe --tags --abbrev=0)"..HEAD
   ```

   Jeder Commit seit dem neuesten Tag, einer pro Zeile.
2. **Formulieren Sie sie um** zu Changelog-Einträgen mit Präfixen ([Changelog-Einträge](changelog-praefixe.md)): Fassen
   Sie zusammengehörende Commits zusammen, lassen Sie interne weg (eine Tippfehlerkorrektur in einem Kommentar) und
   schreiben Sie jeden aus Sicht des Nutzers.
3. **Fügen Sie oben in `CHANGELOG.md` eine Überschrift** mit der neuen Version (ohne `v`) hinzu, dann die Einträge:

   ```markdown
   # Changelog

   ## 1.0.25

   - Added: Dark mode for the event pages.
   - Fixed: The search field keeps its focus ring at 200% zoom.
   ```

4. **Committen Sie sie zusammen mit der Versionserhöhung** ([Die Version vor dem Tag erhöhen](version-vor-dem-tag-erhoehen.md)).
5. **Verwenden Sie die Einträge erneut** für die Release Notes auf GitHub.

## Bewährte Praxis

- Das neueste Release steht oben.
- Schreiben Sie die Einträge eines veröffentlichten Releases nie um, außer um einen sachlichen Fehler zu korrigieren,
  und sagen Sie es dann.
- Wenn Sie das Changelog für ältere Releases ausgelassen haben, beginnen Sie jetzt; ein kurzer Hinweis „Releases vor
  1.0.20: siehe die GitHub-Releases“ ist besser als nichts.

## Verwandte Seiten

- [INSTALL.md und CHANGELOG.md](../../../../eng-us/help/designers/extensions/install-and-changelog-files.md)
- [Release Notes schreiben](release-notes-schreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
