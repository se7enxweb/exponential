---
title: "Changelog-Einträge: Added, Updated, Removed, Renamed, Fixed"
audience: designer, developer
category: theme-distribution
order: 170
summary: Changelog-Einträge und Commit-Nachrichten mit einem von fünf Präfixen schreiben, damit sich ein Changelog aus der Historie zusammenstellen lässt - was jedes Präfix bedeutet, gute und schlechte Beispiele und eine Änderung pro Eintrag.
keywords: Changelog, Präfixe, Added, Updated, Removed, Renamed, Fixed, Commit-Nachrichten, Release Notes, Konventionen, changelog, prefixes, commit messages, release notes, conventions
type: help
---
# Changelog-Einträge: Added, Updated, Removed, Renamed, Fixed

Diese Seite ist für Designer, die Commit-Nachrichten und Changelog-Zeilen schreiben. Das Projekt Exponential und die
Themes von 7x verwenden dieselben fünf Präfixe, sodass ein Leser eine Historie überfliegen und ein Changelog aus den
Commits zusammengestellt werden kann.

## Die Präfixe

| Präfix | Für | Beispiel |
|---|---|---|
| `Added:` | Etwas Neues: ein Template, ein Ansichtsmodus, eine Einstellung, eine Übersetzung, ein Screenshot | `Added: Open Graph and Twitter tags on every page of the starter design` |
| `Updated:` | Geändertes Verhalten oder Aussehen von etwas Bestehendem | `Updated: The login page shows the register link only to a visitor who may register` |
| `Removed:` | Etwas Gelöschtes | `Removed: The unused YUI stylesheet` |
| `Renamed:` | Eine umbenannte Datei, Einstellung, Klasse oder ein umbenanntes Design | `Renamed: mytheme.ini [Front] to [FrontPage]` |
| `Fixed:` | Eine Fehlerkorrektur | `Fixed: The map popup escapes the address and the title` |

Die Beispiele für `Added:` und `Updated:` stammen aus dem Changelog von `cjw_themes_jumper` (0.1.2 und 0.1.3).

## Regeln

1. **Eine Änderung pro Eintrag.** Eine Änderung, die etwas hinzufügt und etwas entfernt, bekommt zwei Einträge.
2. **Sagen Sie, was sich für den Nutzer geändert hat**, nicht welche Datei Sie angefasst haben.
3. **Nennen Sie den Grund**, wenn er nicht offensichtlich ist: „... damit die Seite unter einer Content Security
   Policy funktioniert“.
4. **Nennen Sie, was der Nutzer tun muss**, falls etwas.
5. **Keine privaten Angaben**: keine internen Pfade, Hostnamen oder Kundennamen. Nennen Sie Personen mit ihrem
   öffentlichen Namen.
6. **Schreiben Sie „Exponential“** für das Produkt.

## Gut und schlecht

| Schlecht | Gut |
|---|---|
| `fix stuff` | `Fixed: Event teasers keep their date in dark mode` |
| `Updated: login.tpl` | `Updated: The login form names its fields for screen readers` |
| `Added: new feature and removed old one` | Zwei Einträge: `Added: ...` und `Removed: ...` |

## Verwandte Seiten

- [Eine CHANGELOG.md führen](eine-changelog-datei-fuehren.md)
- [INSTALL.md und CHANGELOG.md](../../../../eng-us/help/designers/extensions/install-and-changelog-files.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
