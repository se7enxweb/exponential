---
title: Der Operator i18n
audience: designer, developer
category: templates
order: 250
summary: Oberflächentexte in Templates mit i18n übersetzen - der Kontext, der Kommentar für Übersetzer, Platzhalter mit einem Hash oder nummeriert mit einem Array, Maskierung, die Varianten x18n und d18n und die Benennung von Kontexten für ein Theme.
keywords: i18n, übersetzen, Übersetzung, Kontext, Platzhalter, %1, Hash-Argumente, x18n, d18n, Oberflächentext, übersetzbare Texte, ezpI18n, translate, translation, context, placeholders, hash arguments, interface text, translatable strings
type: help
---
# Der Operator i18n

Diese Seite ist für Designer, die Template-Texte übersetzbar machen. Jeder Oberflächentext, den ein Design ausgibt, läuft
durch `i18n` (`kernel/common/ezi18noperator.php`, das `ezpI18n::tr()` aufruft).

## Inhalt

1. [Der Aufruf](#der-aufruf)
2. [Kontexte](#kontexte)
3. [Platzhalter](#platzhalter)
4. [Maskierung](#maskierung)
5. [Varianten](#varianten)

## Der Aufruf

```tpl
{'Read more'|i18n( 'design/mytheme/line' )}
{'Read more'|i18n( 'design/mytheme/line', 'Link under a teaser' )}
```

| Parameter | Bedeutung |
|---|---|
| Eingabe | der englische Quelltext |
| `context` | die Gruppe des Texts in den Übersetzungsdateien |
| `comment` | ein Hinweis für Übersetzer (optional) |
| `arguments` | Werte für Platzhalter (optional) |

Gibt es für die aktuelle Locale keine Übersetzung, wird der Quelltext angezeigt.

## Kontexte

Verwenden Sie einen Kontext pro Bereich Ihres Designs, beginnend mit `design/<theme>/`: `design/mytheme/pagelayout`,
`design/mytheme/full/article`, `design/mytheme/forms`. Derselbe Text in zwei Kontexten kann zwei Übersetzungen haben, was bei
kurzen Wörtern wichtig ist („Open“ für ein Menü gegenüber „Open“ als Zustand).

## Platzhalter

Bauen Sie Sätze nie, indem Sie übersetzte Stücke aneinanderhängen: Die Wortstellung unterscheidet sich zwischen Sprachen.
Setzen Sie Platzhalter in den Text.

Benannt, mit einem Hash:

```tpl
{'%count results for "%query"'|i18n( 'design/mytheme/search',, hash( '%count', $count, '%query', $query|wash ) )}
```

Nummeriert, mit einem Array (die Mail-Templates des Kernels tun das):

```tpl
{"Collected information from %1"|i18n( "design/standard/content/edit",, array( $collection.object.name|wash ) )}
```

Der leere zweite Parameter überspringt den Kommentar.

## Maskierung

- Der übersetzte Text wird von `i18n` nicht maskiert. Maskieren Sie die Werte, die Sie in Platzhalter setzen (wie oben), wenn
  sie aus Inhalten oder der Anfrage stammen.
- In einem Attribut maskieren Sie das ganze Ergebnis: `title="{'Close'|i18n( 'design/mytheme' )|wash}"`.

## Varianten

Der Kernel registriert drei Namen in derselben Klasse:

- `i18n`: der Normalfall.
- `x18n`: nimmt den Namen der Erweiterung als zusätzlichen ersten Parameter.
- `d18n`: ein Alias von `i18n` für eine **variable** Eingabe, damit Werkzeuge zum Extrahieren von Texten ihn nicht aufgreifen
  (`{$label|d18n( 'design/mytheme' )}`); der Text muss trotzdem in der Übersetzungsdatei stehen, um übersetzt zu werden.

## Verwandte Seiten

- [Template-Texte übersetzen](../../../../eng-us/help/designers/templates/translating-template-strings.md)
- [Tutorial: ein Theme internationalisieren](../../../../eng-us/help/designers/theme-design/tutorial-internationalize-a-theme.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
