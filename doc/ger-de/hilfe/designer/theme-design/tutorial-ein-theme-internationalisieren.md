---
title: "Tutorial: ein Theme internationalisieren"
audience: designer
category: theme-design
order: 810
summary: Schritt für Schritt jeden Text eines Themes übersetzbar machen: i18n in Templates mit einem Kontext, Platzhalter, eine translation.ts pro Sprache in der Erweiterung, TranslationExtensions, sprachabhängige Daten und Zahlen, Sprachen von rechts nach links und Caches.
keywords: Tutorial, internationalisieren, i18n, Übersetzung, translation.ts, TranslationExtensions, Theme-Übersetzung, Kontext, Platzhalter, l10n, mehrsprachiges Theme, Sprache, tutorial, internationalize, translation, theme translation, context, placeholders, multilingual theme, language
type: help
---
# Tutorial: ein Theme internationalisieren

Dieses Tutorial ist für Designer, die ihr Theme in mehr als einer Sprache möchten. Sie machen jeden sichtbaren Text des Designs übersetzbar
und liefern eine deutsche Übersetzung mit. Planen Sie für ein kleines Theme ein bis zwei Stunden ein.

## Inhalt

1. [Schritt 1: jeder Text über i18n](#schritt-1-jeder-text-über-i18n)
2. [Schritt 2: Platzhalter, keine Verkettung](#schritt-2-platzhalter-keine-verkettung)
3. [Schritt 3: die Übersetzungsdatei](#schritt-3-die-übersetzungsdatei)
4. [Schritt 4: dem Siteaccess sagen, dass er sie liest](#schritt-4-dem-siteaccess-sagen-dass-er-sie-liest)
5. [Schritt 5: Daten, Zahlen und Richtung](#schritt-5-daten-zahlen-und-richtung)
6. [Schritt 6: Caches und Prüfungen](#schritt-6-caches-und-prüfungen)

## Schritt 1: jeder Text über i18n

Jeder Text, den ein Template ausgibt, läuft über den Operator `i18n` mit einem **Kontext**, einem Namen, der die Texte eines Bereichs
gruppiert. Verwenden Sie `design/<your theme>/<area>`:

```tpl
<a class="skip-link" href="#main">{'Skip to main content'|i18n( 'design/mytheme/pagelayout' )}</a>
<button type="button">{'Menu'|i18n( 'design/mytheme/pagelayout' )}</button>
```

Auch Attributwerte (`title`, `aria-label`, `alt` von Design-Bildern, `placeholder`): Maskieren Sie sie, wenn sie in ein Attribut gehen,
`|i18n( ... )|wash`. Siehe [Der Operator i18n](../vorlagen/der-i18n-operator.md).

## Schritt 2: Platzhalter, keine Verkettung

Die Wortstellung unterscheidet sich zwischen Sprachen. Setzen Sie veränderliche Teile als Platzhalter in den Satz:

```tpl
{'Page %current of %total'|i18n( 'design/mytheme/navigator',, hash( '%current', $page, '%total', $pages ) )}
```

Der zweite Parameter (hier leer) ist ein Kommentar für Übersetzer.

## Schritt 3: die Übersetzungsdatei

`extension/mytheme/translations/ger-DE/translation.ts`:

```xml
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE TS>
<TS version="2.0" language="ger-DE" sourcelanguage="en">
<context>
    <name>design/mytheme/pagelayout</name>
    <message>
        <source>Skip to main content</source>
        <translation>Zum Inhalt springen</translation>
    </message>
    <message>
        <source>Menu</source>
        <translation>Menü</translation>
    </message>
</context>
<context>
    <name>design/mytheme/navigator</name>
    <message>
        <source>Page %current of %total</source>
        <translation>Seite %current von %total</translation>
    </message>
</context>
</TS>
```

Die `translations/ger-DE/translation.ts` des Themes starter hat genau diese Form. Die `source` muss Zeichen für Zeichen dem Text im Template
entsprechen. Ein Text ohne Übersetzung wird auf Englisch gezeigt.

## Schritt 4: dem Siteaccess sagen, dass er sie liest

Der Kernel liest `share/translations/` und die Erweiterungen, die in `site.ini [RegionalSettings] TranslationExtensions[]` aufgeführt sind.
In den Siteaccess-Einstellungen der Erweiterung (wie es die Themes media und starter tun):

```ini
# extension/mytheme/settings/siteaccess/<siteaccess>/site.ini.append.php
[RegionalSettings]
TranslationExtensions[]=mytheme
```

`[RegionalSettings] Locale` des Siteaccess (zum Beispiel `ger-DE`) wählt die Sprache.

## Schritt 5: Daten, Zahlen und Richtung

- Daten und Zahlen über `l10n`: `{$date|l10n( 'shortdate' )}`, `{$amount|l10n( 'number' )}`
  ([Daten, Zahlen und l10n](../vorlagen/datum-zahlen-und-l10n.md)).
- `lang` und `dir` am `<html>` aus `$site` ([Designs für rechts nach links](rechts-nach-links-designs.md)).
- Lassen Sie Platz: Deutsche und französische Texte sind oft ein Drittel länger als englische. Knöpfe und Menüs müssen umbrechen oder
  wachsen.

## Schritt 6: Caches und Prüfungen

```bash
php bin/php/ezcache.php --clear-id=translation
./console exp:ezchecktranslation ger-DE    # statistics for one translation
```

- [ ] Öffnen Sie jeden Seitentyp in jeder Sprache: kein Englisch mehr in den eigenen Texten des Designs.
- [ ] Lange Übersetzungen brechen bei 375 px keine Knöpfe oder Menüs.
- [ ] Die Datei `translation.ts` ist gültiges XML (`xmllint --noout`).

## Verwandte Seiten

- [Texte in Templates übersetzen](../vorlagen/template-texte-uebersetzen.md)
- [Übersetzungen in einem Theme](../erweiterungen/uebersetzungen-in-einem-theme.md)
- [Übersetzungen und Sprachen](../../../../eng-us/features/6.0/translations-and-languages.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
