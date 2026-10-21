---
title: Ersatz für Oberflächenübersetzungen
audience: government, admin, developer
category: public-sector
order: 180
summary: Wie Exponential entscheidet, welche Oberflächenübersetzung eine Locale ohne eigene Übersetzung anzeigt (eingerichtete Ersatzsprachen, Regeln, der automatische Ersatz innerhalb derselben Sprache), wie Sie die Kette mit exp:i18n und unter Setup > Oberflächensprachen sehen, und welche Einstellungen eine öffentliche Stelle ändern kann.
keywords: Ersatzübersetzung, Fallback, FallbackLanguages, i18n.ini, AutomaticLanguageFallback, FallbackMode, merge, exp:i18n, Setup Oberflächensprachen, regionale Locale, Oberflächenübersetzung, translation fallback, regional locale, interface translation
type: help
---
# Ersatz für Oberflächenübersetzungen

Diese Seite richtet sich an Administratoren, die die Administration oder ein öffentliches Design in einer regionalen
Locale betreiben, etwa kanadischem Französisch oder dem Spanisch eines lateinamerikanischen Landes. Eine Locale ohne
eigene Übersetzung fällt nicht sofort auf Englisch zurück: Der Übersetzer probiert der Reihe nach die Übersetzungen,
auf die sie zurückfällt
([Sprachen in Exponential, Abschnitt 13](../../../sprachen.md#13-übersetzungs-fallbacks)).

## Inhalt

1. [Die Kette](#die-kette)
2. [Erste oder Zusammenführen](#erste-oder-zusammenführen)
3. [Die Ersatzsprachen sehen](#die-ersatzsprachen-sehen)
4. [Einstellungen](#einstellungen)
5. [Typische Fälle im öffentlichen Sektor](#typische-fälle-im-öffentlichen-sektor)

## Die Kette

Jeder Schritt wird von der ersten Regel entschieden, die eine Antwort gibt:

1. `i18n.ini [TranslationSettings] FallbackLanguages[]`: Was eingerichtet ist, gewinnt immer.
2. Die Klassen aus `FallbackRules[]`, von Erweiterungen hinzugefügt.
3. Mit `AutomaticLanguageFallback=enabled` (Voreinstellung) die vollständigste Übersetzung derselben Sprache:
   `fre-CA` zu `fre-FR`, `ger-AT` zu `ger-DE`.

Die Antwort wird erneut befragt, wodurch eine Kette wie `esl-AR -> esl-MX -> esl-ES` entsteht. Sie endet bei einer
Locale, für die nichts antwortet, bei den englischen Ausgangstexten, bei einem Zyklus (wird gemeldet) oder nach
`FallbackMaxDepth` Schritten.

## Erste oder Zusammenführen

`FallbackMode=first` (Voreinstellung) lädt für jede Übersetzungswurzel die erste Datei der Kette, die es gibt.
`FallbackMode=merge` lädt jede Datei der Kette, die allgemeinste zuerst, sodass eine Meldung, die der spezifischen
Übersetzung fehlt, aus der nächsten kommt. Mit Zusammenführen bleiben in regionalen Locales weniger englische Reste
stehen.

## Die Ersatzsprachen sehen

```bash
./console exp:i18n fallbacks                          # every locale: coverage, extensions, chain
./console exp:i18n fallbacks --locale=fre-CA,ger-AT   # each step with its rule
./console exp:i18n check                              # exit 1 on a problem, for CI
```

**Setup > Oberflächensprachen** (`/setup/languages`, Richtlinie `setup/system_info`) zeigt dasselbe in der
Administration, nur lesend.

## Einstellungen

| `i18n.ini [TranslationSettings]` | Voreinstellung | Bedeutung |
|---|---|---|
| `FallbackLanguages[<locale>]` | 100 Einträge | die Übersetzung, auf die eine Locale zurückfällt |
| `FallbackRules[]` | leer | Klassen, die nach den eingerichteten Ersatzsprachen befragt werden |
| `AutomaticLanguageFallback` | `enabled` | der Ersatz innerhalb derselben Sprache |
| `AutomaticFallbackExclude[]` | `eng` | Sprachen, die ausgenommen bleiben |
| `FallbackMaxDepth` | `4` | höchste Zahl der Schritte einer Kette |
| `FallbackMode` | `first` | `first` oder `merge` |

Tragen Sie Änderungen in `settings/override/i18n.ini.append.php` ein und leeren Sie die Caches
(`php bin/php/ezcache.php --clear-tag=i18n` nach Änderungen an Übersetzungsdateien, `--clear-tag=ini` nach
Einstellungen).

## Typische Fälle im öffentlichen Sektor

| Fall | Vorschlag |
|---|---|
| Eine regionale Varietät mit eigenen Rechtschreibregeln | eine eigene `.ts`-Übersetzung für die abweichenden Texte hinzufügen, `FallbackMode=merge` |
| Eine Minderheitensprache ohne Übersetzung | die Oberfläche bleibt in der eingerichteten Ersatzsprache oder auf Englisch; übersetzen Sie die wenigen Texte des öffentlichen Designs |
| Mitarbeitende in zwei Amtssprachen | jede Person wählt; kein Ersatz nötig |

## Verwandte Seiten

- [Sprachen der Administrationsoberfläche](sprachen-der-administrationsoberflaeche.md)
- [Liste der Oberflächensprachen](../../../funktionen/6.0/ui-umschalter-sprachen.md)
- [Sprachen in Exponential](../../../sprachen.md#13-übersetzungs-fallbacks)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
