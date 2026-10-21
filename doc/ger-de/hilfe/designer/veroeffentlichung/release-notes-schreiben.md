---
title: "Release Notes schreiben"
audience: designer, sales-marketing
category: theme-distribution
order: 150
summary: Release Notes schreiben, nach denen Nutzer handeln können - eine Überschrift, was sich für sie geändert hat, was sie nach dem Upgrade tun müssen, Danksagungen und ein ehrlicher Hinweis auf einen früheren Fehler - mit einem echten Beispiel eines veröffentlichten Themes.
keywords: Release Notes, Changelog, Upgrade-Hinweise, was sich geändert hat, Danksagungen, Überschrift, Text des GitHub-Releases, release notes, upgrade notes, what changed, credits, headline, GitHub release text
type: help
---
# Release Notes schreiben

Diese Seite ist für Designer, die den Text eines GitHub-Releases schreiben. Nutzer lesen Release Notes, um zu
entscheiden, ob sie aktualisieren und was sie danach tun.

## Die Teile

1. **Eine Überschrift**, die sagt, worum es im Release geht.
2. **Was sich für den Nutzer geändert hat**: ein Aufzählungspunkt pro Änderung, mit seinem Präfix (`Added:`,
   `Updated:`, `Fixed:` ...).
3. **Was nach dem Update zu tun ist**: Caches leeren, Einstellungen ergänzen, Upgrade-Skripte ausführen. Lassen Sie es
   nur weg, wenn nichts nötig ist, und schreiben Sie dann „Nach dem Update ist nichts zu tun.“
4. **Kompatibilität**: die Versionen von Exponential und PHP, mit denen getestet wurde, falls sie sich geändert haben.
5. **Danksagungen**: wer gemeldet oder beigetragen hat, mit öffentlichem Namen und mit Einwilligung.
6. **Eine Korrektur**, wenn das Release ein früheres repariert: was an welcher Version falsch war.

## Ein echtes Beispiel

Die Notes von `cjw_themes_jumper` v0.1.6 beginnen so:

```markdown
## Starter design without inline code

The pages of the starter design now work under a Content Security Policy without `'unsafe-inline'`.

- The page script of every page (...) is `javascript/cjw_jumper_page.js`, loaded last in the packed head script ...
- The speech bubble's close link and the icon that opens it again are `href="#"` links with `data-cjw-bubble`
  (no `javascript:` URLs) ...
```

Eine Überschrift mit dem Nutzen, dann die Einzelheiten, die ein Template-Autor braucht.

## Schritt für Schritt

1. Kopieren Sie die Einträge des Releases aus `CHANGELOG.md`.
2. Fügen Sie die Überschrift und den Abschnitt „nach dem Update“ hinzu.
3. Lesen Sie es als Nutzer, der die letzten drei Releases übersprungen hat: Ist etwas unklar?
4. Entfernen Sie alles Private: interne Pfade, Hostnamen, Kundennamen, Zugangsdaten.
5. Speichern Sie es als Notes-Datei für `gh release create` ([GitHub-Releases](github-releases.md)).

## Verwandte Seiten

- [Changelog-Einträge](changelog-praefixe.md)
- [Fallstudien und Content-Marketing](../../geschaeft/fallstudien-und-content-marketing.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
