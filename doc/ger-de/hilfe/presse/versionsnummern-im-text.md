---
title: Versionsnummern im Text
audience: press
category: press-media
order: 42
summary: Wie Sie Versionsnummern von Exponential und seiner Familie in veröffentlichten Texten schreiben - vollständige Nummern ohne „v“, Hauptlinien, Releases gegenüber Entwicklung, Nummern von Erweiterungen und Velocity, deren letzte Stelle weiterzählt, Versionen vergleichen und sortieren und die Fehler, die Sie vermeiden sollten.
keywords: Version, Versionsnummern, Release-Nummer, 6.0.14, 6.0.15, Exponential 6, semantische Versionierung, Tag, Präfix v, Version von Velocity, Version einer Erweiterung, Sortieren, 0.0.4.10, version, version numbers, release number, semantic versioning, tag, v prefix, Velocity version, extension version, sorting
type: help
---
# Versionsnummern im Text

Diese Seite ist für Reporter und Redakteure, die Versionsnummern in Texte setzen. Die Nummern von Exponential folgen ein
paar einfachen Regeln, und eine davon überrascht viele: Die letzte Stelle zählt weiter, sodass `0.0.4.10` nach
`0.0.4.9` kommt.

## Inhalt

1. [Eine Version schreiben](#eine-version-schreiben)
2. [Linien und Releases](#linien-und-releases)
3. [Veröffentlicht oder in Entwicklung](#veröffentlicht-oder-in-entwicklung)
4. [Nummern von Velocity und Erweiterungen](#nummern-von-velocity-und-erweiterungen)
5. [Versionen vergleichen und sortieren](#versionen-vergleichen-und-sortieren)
6. [Häufige Fehler](#häufige-fehler)

## Eine Version schreiben

| Schreiben Sie | Nicht |
|---|---|
| Exponential 6.0.14 | Exponential v6.0.14, Exponential 6.0.14.0 |
| Version 6.0.14 | Release v6.0.14 (das „v“ gehört nur zum Git-Tag) |
| Exponential 6 | Exponential 6.0 (sofern Sie nicht die Linie 6.0.x meinen) |
| die Linie 6.0.x | die Serie 6.x (es gibt kein 6.1) |

Quelle: [Regeln für Marke und Namen](../geschaeft/marken-und-namensregeln.md#das-produkt-exponential).

## Linien und Releases

| Begriff | Bedeutung |
|---|---|
| Exponential 6 | Die Hauptlinie |
| 6.0.x | Alle Releases der Linie, Stand 18. Oktober 2026 6.0.0 bis 6.0.14 |
| 6.0.14 | Ein Release, ein Git-Tag `v6.0.14` |
| 6.0.15 | Das Release in Entwicklung |

Alle Releases stehen unter [Die 6.0.x-Releases auf einen Blick](die-6-0-x-releases-auf-einen-blick.md).

## Veröffentlicht oder in Entwicklung

Eine Version ist veröffentlicht, wenn ihr Git-Tag veröffentlicht ist. Bis dahin:

- schreiben Sie „6.0.15, das in Entwicklung ist“ oder „das nächste Release“;
- nennen Sie kein Datum, sofern das Projekt keines veröffentlicht hat;
- beschreiben Sie Funktionen als „im Changelog von 6.0.15“, nicht als in einem Release verfügbar
  ([Über 6.0.15 berichten, solange es in Entwicklung ist](ueber-6-0-15-in-entwicklung-berichten.md)).

Einmal veröffentlicht, wird eine Version nie geändert: „a fix for a problem in `v6.0.14` or an earlier tag reaches you
as the next release, not as a corrected copy of the old one“ ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md);
[Veröffentlichte Versionen ändern sich nie](veroeffentlichte-versionen-aendern-sich-nie.md)).

## Nummern von Velocity und Erweiterungen

Velocity und die Erweiterungen haben eigene Nummern, unabhängig von denen des Kernels:

- Velocity verwendet vier Stellen, zum Beispiel `0.0.4.50` (die Version, die das README am 18. Oktober 2026 vorschlägt).
- „After `0.1.9` the next release is `0.1.10`; only the last position counts up unless you decide otherwise“
  ([Erweiterungen, „Release it“](../../anleitungen/extensions.md#7-die-extension-veröffentlichen)).
- Erweiterungen, die im Kernel mitgeliefert werden, tragen die Version des Kernels; eigenständige Erweiterungen ihre
  eigene.

Schreiben Sie das Produkt mit seiner Nummer: „Velocity 0.0.4.50“, „die Erweiterung expai 0.0.1.12“. Vermischen Sie sie
nicht mit der Nummer des Kernels.

## Versionen vergleichen und sortieren

Vergleichen Sie Stelle für Stelle als Zahlen: `6.0.10` ist neuer als `6.0.9`. Eine einfache alphabetische Sortierung
macht das falsch und setzt `6.0.10` vor `6.0.2`. In einem Klon des Repositorys:

```bash
git tag -l 'v6.0.*' --sort=version:refname    # right order
git tag -l 'v6.0.*' | sort -V                  # right order
git tag -l 'v6.0.*' | tail -1                  # WRONG: alphabetical, may not be the newest
```

## Häufige Fehler

| Fehler | Richtig |
|---|---|
| „Exponential 7“ | Eine solche Version gibt es nicht |
| „6.0.15 (veröffentlicht im Oktober 2026)“ | 6.0.15 ist in Entwicklung |
| „Velocity 0.0.5“ als nächste nach 0.0.4.9 | Die nächste nach 0.0.4.9 ist 0.0.4.10 |
| „die neueste Version, 6.0.9“ | Prüfen Sie die Tags; 6.0.14 ist am 18. Oktober 2026 die neueste |
| „eZ Publish 6.0“ für das Release von 2024 | „6.0.0, veröffentlicht im Januar 2024, als die Software noch eZ Publish hieß“ |

## Verwandte Seiten

- [Wie Versionsnummern funktionieren](wie-versionsnummern-funktionieren.md)
- [Die 6.0.x-Releases auf einen Blick](die-6-0-x-releases-auf-einen-blick.md)
- [Ein Release selbst prüfen](ein-release-selbst-pruefen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
