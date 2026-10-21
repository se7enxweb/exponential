---
title: Tags und Releases sind dauerhaft
audience: developer, designer, app-creator
category: extension-development
order: 30
summary: Warum ein gepushter Tag und ein veröffentlichtes Release nie verschoben, ersetzt oder neu erstellt werden können - Packagist und jede Installation behalten, was sie zuerst gesehen haben - und was Sie stattdessen tun, wenn ein Release fehlerhaft hinausging.
keywords: Tag, Release, dauerhaft, unveränderlich, Packagist, neu taggen, Force-Push, Fehler, nächste Version, Release Notes, tag, release, permanent, immutable, re-tag, force push, mistake, next version, release notes
type: help
---
# Tags und Releases sind dauerhaft

Teil 7, Kapitel 3. Dies ist die wichtigste Regel beim Veröffentlichen, und die ohne Ausnahmen: **Ein Tag, der gepusht
wurde, und ein daraus gebautes Release werden nie geändert.** Nicht, um einen Tippfehler zu korrigieren, nicht, um eine
vergessene Datei hinzuzufügen, nicht, um ein Archiv neu zu bauen. Die einzige Korrektur für eine fehlerhafte Version
ist die nächste Version.

## Warum

Git erlaubt Ihnen, einen Tag zu löschen und einen neuen mit demselben Namen zu pushen (`git push --force`). Das
Problem ist alles, was den alten schon gesehen hat:

- **Packagist** liest einen Tag einmal und behält die Version, die es gefunden hat.
- **Jede Installation**, die zwischendurch `composer update` ausgeführt hat, hat die alten Dateien, und ihre
  `composer.lock` hält sie fest.
- **Jeder Klon** Ihres Repositorys behält seinen alten Tag; Git ersetzt beim Fetch keinen Tag.
- **Spiegel und Caches** behalten das alte Archiv.

Nach einem verschobenen Tag tragen zwei verschiedene Dateisätze dieselbe Versionsnummer. Ein Benutzer, der einen
Fehler in „1.2.3“ meldet, kann den einen oder den anderen haben. Das ist schlimmer als der ursprüngliche Fehler und
von außen nicht zu diagnostizieren.

Dasselbe gilt für ein GitHub-Release: Ein angehängtes Archiv durch ein korrigiertes zu ersetzen, hat dieselbe Wirkung
wie das Verschieben des Tags.

## Was Sie stattdessen tun

Wenn ein Release fehlerhaft ist:

1. **Lassen Sie es stehen.** Löschen Sie weder den Tag noch das Release.
2. **Beheben Sie das Problem** in einem neuen Commit.
3. **Veröffentlichen Sie die nächste Version**, zum Beispiel 1.2.4 nach einem fehlerhaften 1.2.3.
4. **Sagen Sie, was falsch war**, in den Notes der neuen Version: „1.2.3 hat die Übersetzungsdateien vergessen; dieses
   Release fügt sie hinzu. Aktualisieren Sie von 1.2.3.“ Benutzer mit Bereichen wie `~1.2.3` erhalten die Korrektur
   mit ihrem nächsten Update.

Eine Lücke in der Historie (eine Version, die übersprungen oder zurückgezogen wurde) hilft niemandem; ein klarer
Hinweis schon.

## Machen Sie es richtig, bevor Sie pushen

Weil sich ein Tag nicht korrigieren lässt, prüfen Sie, bevor Sie ihn pushen. Ein Tag ist leicht richtig zu machen und
unmöglich zu reparieren:

- [ ] Der Arbeitsbaum ist sauber (`git status` zeigt nichts).
- [ ] Die Tests bestehen ([Vor einem Release testen](vor-einem-release-testen.md)).
- [ ] Die Version in `ezinfo.php`, `extension.xml` und (falls vorhanden) `composer.json` ist die Version, die Sie
      gleich taggen, in dem Commit, auf den der Tag zeigt ([ezinfo.php und extension.xml anheben](ezinfo-und-extension-xml-anheben.md)).
- [ ] Die Nummer ist frei: geprüft gegen Tags und Releases ([Die nächste Versionsnummer wählen](die-naechste-version-waehlen.md)).
- [ ] Erzeugte Dateien (ein gebautes JavaScript-Bundle, ein minifiziertes Stylesheet, `share/filelist.md5`) passen zu
      ihren Quellen.

Dann:

```bash
git tag -a v1.2.4 -m "Version 1.2.4"
git push origin v1.2.4
```

Ein annotierter Tag (`-a`) hält fest, wer wann getaggt hat. Pushen Sie den Tag mit seinem Namen, nicht mit `--tags`,
damit Sie nie versehentlich ein lokales Experiment pushen.

## Lokale Tags kosten nichts

Bis Sie pushen, existiert ein Tag nur in Ihrem Klon. Wenn Sie den falschen Commit getaggt und noch nicht gepusht
haben:

```bash
git tag -d v1.2.4
```

und taggen Sie erneut. Sobald er gepusht ist, ist er dauerhaft.

## Verwandte Seiten

- Zurück: [Die nächste Versionsnummer wählen](die-naechste-version-waehlen.md)
- Weiter: [ezinfo.php und extension.xml anheben](ezinfo-und-extension-xml-anheben.md)
- [Die Release-Checkliste](die-release-checkliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
