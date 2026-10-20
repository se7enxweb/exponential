---
title: "Versionsbedingungen Ihrer Nutzer"
audience: designer, developer, admin
category: theme-distribution
order: 220
summary: Im README eine Composer-Versionsbedingung für Ihr Theme empfehlen - Tilde, Caret, feste Versionen und dev-main -, was jede ein Update bringen lässt und wie Ihre Nummerierung sie sicher macht.
keywords: Versionsbedingung, composer require, Tilde, Caret, feste Version, dev-main, Update, empfohlene Bedingung, composer.json im Wurzelverzeichnis, version constraint, tilde, caret, pin, update, recommended constraint
type: help
---
# Versionsbedingungen Ihrer Nutzer

Diese Seite ist für Designer, die Nutzern sagen, wie sie ihr Theme einbinden. Die Bedingung in der `composer.json` des
Nutzers entscheidet, welche Ihrer künftigen Releases `composer update` installiert.

## Die üblichen Bedingungen

| Bedingung | Erlaubt | Gut für |
|---|---|---|
| `~1.0.24` | `>=1.0.24 <1.1.0` | Jede Korrektur der Linie `1.0` bekommen, nichts Größeres |
| `^1.0` | `>=1.0.0 <2.0.0` | Jedes Release bekommen, das kompatibel bleibt, wenn Sie die mittlere Stelle nur für kompatible Ergänzungen verschieben |
| `1.0.24` | Genau diese | Websites, die erst nach eigener Prüfung aktualisieren |
| `dev-main` | Den neuesten Commit auf `main` | Unveröffentlichte Arbeit testen, nie in Produktion |

Die Tilde lässt die zuletzt angegebene Stelle frei: `~1.0.24` akzeptiert `1.0.25` und auch `1.0.100`. Weil bei normalen
Releases nur die letzte Stelle hochzählt ([Nach .9 kommt .10](nur-die-letzte-stelle-zaehlt.md)), erhält eine
Tilde-Bedingung jedes normale Release und nie einen bewussten Schritt.

## Was Sie empfehlen

Schreiben Sie eine Zeile in Ihr README, passend dazu, wie Sie Releases nummerieren. Zum Beispiel:

```bash
composer require yourvendor/mytheme:~1.0.24
```

und erklären Sie: „Updates bringen Korrekturen und Verbesserungen der Linie 1.0; 1.1 wird mit ihren Änderungen
angekündigt.“

## Wenn Sie eine höhere Stelle verschieben

Sagen Sie es in den Release Notes und im README: was sich geändert hat und dass Nutzer ihre Bedingung ändern müssen, um
es zu erhalten (`~1.1.0`). Nutzer mit `~1.0.24` bleiben auf der Linie `1.0`, bis sie sich entscheiden.

## Schritt für Schritt: eine Bedingung prüfen

```bash
composer why-not yourvendor/mytheme 1.1.0
```

Erwartet bei `~1.0.24`: eine Zeile, die sagt, dass das Root-Paket `~1.0.24` verlangt, was `1.1.0` nicht erlaubt.

## Verwandte Seiten

- [require und suggest](../erweiterungen/composer-require-und-suggest.md)
- [Inkompatible Änderungen und die nächste Major-Version](../lebenszyklus/inkompatible-aenderungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
