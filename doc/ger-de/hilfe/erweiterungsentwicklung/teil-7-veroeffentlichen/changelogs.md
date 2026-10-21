---
title: Changelogs
audience: developer, designer, app-creator
category: extension-development
order: 70
summary: Schreiben Sie ein Changelog, mit dem Benutzer etwas anfangen können - gruppiert nach Added, Updated, Fixed, Removed und Renamed, zusammengestellt aus den Commit-Nachrichten seit dem letzten Tag, Upgrade-Hinweise zuerst - und bewahren Sie das Changelog einer alten Erweiterung.
keywords: Changelog, Änderungsprotokoll, Release Notes, Upgrade-Hinweise, changelog, release notes, Added, Updated, Fixed, Removed, Renamed, git log, upgrade notes, CHANGELOG.md, changelog.txt
type: help
---
# Changelogs

Teil 7, Kapitel 7. Ein Changelog beantwortet die Frage, die sich jeder Benutzer vor einem Update stellt: „Was ändert
sich für mich?“. Dieses Kapitel zeigt, wie Sie eines aus guten Commit-Nachrichten schnell schreiben und was Sie an
den Anfang stellen.

## Wo es liegt

Führen Sie eine Datei im Repository und ergänzen Sie sie bei jedem Release:

| Datei | Zu sehen in |
|---|---|
| `CHANGELOG.md` im Wurzelverzeichnis (die übliche Wahl) | GitHub, den Links von Packagist, Editoren |
| `doc/changelog.txt` (ältere Erweiterungen) | behalten Sie sie, wenn die Erweiterung eine hat; neue Einträge kommen nach oben |

se7enxweb/expchangeclass hat die `doc/changelog.txt` der Erweiterung behalten, von der sie geforkt wurde, mit deren
alten Einträgen und deren eigener Legende (`*fixed +added -removed`), und fügt jedes Release oben hinzu. So bleibt die
Historie für alle lesbar, die das Original kannten.

## Aus den Commits zusammenstellen

Mit Commit-Betreffs, die mit einem Präfix beginnen
([Commit-Nachrichten, aus denen das Projekt ein Changelog bauen kann](../teil-5-erweitern-forken-zurueckgeben/commit-nachrichten.md)),
ist das Rohmaterial ein einziger Befehl:

```bash
git log --format='%s' v1.2.3..HEAD | sort
```

Die Ausgabe sortiert sich nach dem ersten Wort von selbst in Gruppen. Schreiben Sie jede Zeile für Benutzer um:
Lassen Sie interne Einzelheiten weg, fassen Sie Commits zusammen, die zusammengehören, behalten Sie die Wirkung.

## Die Form eines Eintrags

```markdown
## 1.2.4 (2026-10-19)

Upgrade notes
- Clear the template override cache after updating: two templates were added.

Added
- German translation of the setup page.

Updated
- The context menu entries carry no inline handlers, so they work under a Content Security Policy.

Fixed
- The parameter file shown after a conversion is escaped.

Removed
- The unused YUI calendar stylesheet.
```

Regeln:

1. **Upgrade-Hinweise zuerst**: alles, was ein Benutzer tun muss (einen Cache leeren, ein Skript ausführen, eine
   Einstellung ändern, eine neue Voraussetzung). Die meisten Einträge haben keine; wenn es einen gibt, ist er die
   wichtigste Zeile.
2. **Die Gruppen in einer festen Reihenfolge**, nur die, die Einträge haben.
3. **Eine Zeile je Änderung, so wie der Benutzer sie sieht.** „Das Menü funktioniert unter einer Content Security
   Policy“, nicht „classcontextmenu.tpl refaktoriert“.
4. **Ein fehlerhaftes vorheriges Release sagt es**: „1.2.3 hat die Übersetzungsdateien vergessen; dieses Release fügt
   sie hinzu.“ ([Tags und Releases sind dauerhaft](tags-sind-dauerhaft.md))
5. **Das Datum**, damit Benutzer es mit ihren eigenen Updates in Beziehung setzen können.

## Wann Sie es schreiben

Im letzten Commit des Releases, zusammen mit dem Anheben der Version, vor dem Tag. Dann enthält der Tag das Changelog,
das ihn beschreibt. Wenn Sie das Changelog nach dem Taggen schreiben, bleibt die getaggte Version ohne ihren eigenen
Eintrag.

## Die Changelogs des Kernels

Der Kernel führt eine Seite je Release in `doc/changelogs/` (zum Beispiel `doc/changelogs/6.0/6.0.15.md`), mit den
Upgrade-Schritten oben und Links zu den Seiten über Verhaltensänderungen. Das ist eine größere Fassung derselben Idee
und ein gutes Vorbild für eine Erweiterung mit vielen Änderungen.

## Verwandte Seiten

- Zurück: [Der Packagist-Webhook](der-packagist-webhook.md)
- Weiter: [GitHub-Releases und Release Notes](github-releases-und-release-notes.md)
- [git log](https://git-scm.com/docs/git-log) in der Git-Dokumentation (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
