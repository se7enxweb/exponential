---
title: Übersetzungen
audience: developer
category: examples
order: 110
summary: Die Texte einer Erweiterung mit ezpI18n::tr() in PHP und dem Operator i18n in Templates übersetzbar machen, eine deutsche translation.ts in der Erweiterung mitliefern, sie mit TranslationExtensions anmelden und auf einem Siteaccess mit Locale=ger-DE prüfen.
keywords: Übersetzung, Übersetzungen, Sprache, Katalog, Kontext, Platzhalter, Übersetzungscache, translation, translations, i18n, ezpI18n::tr, translation.ts, Qt Linguist, TranslationExtensions, RegionalSettings, Locale, TextTranslation, context, placeholders, ezlupdate, ezchecktranslation, translation cache, exp:i18n
type: example
---
# Übersetzungen

Jeder Text, den eine Erweiterung anzeigt, steht im Code auf Englisch und wird über einen Katalog je Locale übersetzt.
Sie übersetzen die Texte der Beispielerweiterung ins Deutsche: Der Code markiert sie, die Erweiterung liefert
`translations/ger-DE/translation.ts` mit, und ein Siteaccess mit `Locale=ger-DE` zeigt sie auf Deutsch.

Diese Seite behandelt die Texte der Oberfläche. Die Sprachen von **Inhalten** (ein Artikel auf Englisch und Deutsch)
sind Inhaltssprachen: siehe [Inhaltssprachen](../../anleitungen/inhaltssprachen.md).

Voraussetzungen: die Erweiterung aus [Ein Erweiterungsgerüst](erweiterungsgeruest.md), aktiviert, mit den Ansichten
aus [Ein eigenes Modul mit Ansicht](eigenes-modul-mit-ansicht.md).

## 1. Die Texte markieren

In PHP mit `ezpI18n::tr( $context, $source, $comment = null, $arguments = null )`:

```php
$title = ezpI18n::tr( 'extension/expexample', 'About the example' );
$line  = ezpI18n::tr( 'extension/expexample', '%count items', null, array( '%count' => $total ) );
```

In einem Template mit dem Operator `i18n` und demselben Kontext; der zweite Parameter ist der Kommentar, der dritte
die Argumente:

```tpl
<h1>{'About the example'|i18n( 'extension/expexample' )}</h1>
<p>{'%count items'|i18n( 'extension/expexample', '', hash( '%count', $total ) )}</p>
```

- Der **Kontext** fasst die Texte einer Stelle zusammen; verwenden Sie `extension/<extension>` oder
  `extension/<extension>/<part>`.
- Die **Quelle** ist der englische Text und zugleich der Schlüssel der Übersetzung: Wird ein Wort darin geändert,
  ist jede Übersetzung davon veraltet.
- **Argumente** (`%count`) werden nach der Übersetzung eingesetzt, sodass ein Übersetzer sie verschieben kann. Setzen
  Sie niemals einen Satz aus übersetzten Teilstücken zusammen.
- Maskieren Sie dort, wo Sie ausgeben: `|wash` nach `i18n`, wenn ein Argument von einem Benutzer stammt.

## 2. Der Katalog

`extension/expexample/translations/ger-DE/translation.ts`, im Qt-Linguist-Format, das der Kernel verwendet:

```xml
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE TS>
<TS version="2.0" language="de_DE" sourcelanguage="en">
<context>
    <name>extension/expexample</name>
    <message>
        <source>About the example</source>
        <translation>Über das Beispiel</translation>
    </message>
    <message>
        <source>Version %version</source>
        <translation>Version %version</translation>
    </message>
    <message>
        <source>%count items</source>
        <translation>%count Einträge</translation>
    </message>
    <message>
        <source>Previous</source>
        <translation>Zurück</translation>
    </message>
    <message>
        <source>Next</source>
        <translation>Weiter</translation>
    </message>
</context>
</TS>
```

Der Verzeichnisname ist der Locale-Code von Exponential (`ger-DE`, `fre-FR`, `esl-ES`), das Attribut `language` die
Qt-Form (`de_DE`). Ein `<translation type="unfinished">` oder ein leeres wird nicht verwendet: Dann erscheint die
englische Quelle.

Bearbeiten Sie die Datei mit Qt Linguist oder einem beliebigen XML-Editor. `ezlupdate -e extension/expexample ger-DE`
(gebaut aus `support/ezlupdate-qt4.5`) sammelt die markierten Texte der Erweiterung in die Datei und markiert
verschwundene als veraltet; siehe
[Sprachen, Abschnitt 8](../../../eng-us/languages.md#8-adding-or-completing-an-administration-translation).

## 3. Anmelden

Der Übersetzer liest `translations/` jeder Erweiterung in `site.ini [RegionalSettings] TranslationExtensions[]`.
In `extension/expexample/settings/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[RegionalSettings]
# Look for translations/<locale>/translation.ts in this extension
TranslationExtensions[]=expexample

*/ ?>
```

## 4. Auf Deutsch ansehen

Ein Siteaccess zeigt den Katalog seiner `Locale`. In `settings/siteaccess/<siteaccess>/site.ini.append.php`:

```ini
[RegionalSettings]
Locale=ger-DE
```

Die Textübersetzung ist ausgeschaltet, wenn die Locale `eng-GB` ist, der ausgelieferte Standardwert, und wenn
`TextTranslation=disabled` gesetzt ist.

```bash
xmllint --noout extension/expexample/translations/ger-DE/translation.ts
php bin/php/ezcache.php --clear-tag=i18n --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Öffnen Sie `/expexample/about` auf diesem Siteaccess: Die Überschrift lautet *Über das Beispiel*. Ein Redakteur kann
die Sprache der Administration auch für sich selbst wählen: siehe
[UI-Umschalter: Sprachen](../../../eng-us/features/6.0/ui-switch-languages.md).

## 5. Messen und prüfen

```bash
php bin/php/ezchecktranslation.php ger-DE --allow-root-user      # the finished share of the kernel catalogue
./console exp:i18n fallbacks --locale=ger-AT --allow-root-user   # the chain a locale falls back to, per extension
```

Eine Locale ohne eigenen Katalog fällt entlang einer Kette zurück (`ger-AT` auf `ger-DE`,
`esl-AR` auf `esl-ES`), konfiguriert in `i18n.ini [TranslationSettings]`: Ein österreichischer Siteaccess zeigt Ihre
deutschen Texte ohne eigene Datei. Siehe [Sprachen, Abschnitt 13](../../../eng-us/languages.md#13-translation-fallbacks).

Für diese Seite geprüft (2026-10-18): `xmllint --noout` auf den Katalog; die Signatur von `ezpI18n::tr()` ist die aus
`kernel/common/ezpi18n.php`; die Suche in Erweiterungen ist die von `eZTSTranslator` über `TranslationExtensions` mit
dem Verzeichnis `translations`. Die deutsche Seite wurde für diese Seite nicht angesehen.

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| Der englische Text bleibt | der Siteaccess läuft mit `eng-GB`; der Kontext oder die Quelle weicht um ein Zeichen ab; die Übersetzung ist als unfertig markiert; der Übersetzungscache |
| Ein Platzhalter erscheint als `%count` | der Argumentschlüssel im Code weicht von dem in der Quelle ab |
| Manche Texte sind übersetzt, andere nicht | die nicht übersetzten Zeichenketten stehen ohne `i18n`/`ezpI18n::tr()` im Code, oder unter einem anderen Kontext |
| Kaputte Zeichen | die Datei ist nicht als UTF-8 gespeichert, oder die XML-Deklaration sagt etwas anderes |

## Abgeleitet aus

- `kernel/common/ezpi18n.php` (`tr()`), `kernel/common/eztemplateautoload.php` (der Operator `i18n`)
- `lib/ezi18n/classes/eztstranslator.php`, `lib/ezi18n/classes/eztranslationcache.php` (`TranslationExtensions`)
- `extension/ezjscore/translations/ger-DE/translation.ts`, `extension/ezjscore/settings/site.ini.append.php`
- `settings/site.ini` `[RegionalSettings]`; `doc/eng-us/languages.md`, Abschnitte 7, 8 und 13

## Verwandte Seiten

- [Kochbuch](README.md)
- [Sprachen in Exponential](../../../eng-us/languages.md), [Übersetzungen und Sprachen](../../../eng-us/features/6.0/translations-and-languages.md)
- [Mehrere Websites: Siteaccesses](multisite-siteaccess.md) für einen Siteaccess je Sprache
- [Template-Operatoren](../template-operatoren/README.md): `i18n` und seine Parameter
- Die Programmierregeln und der Musterkatalog: [die Standards](../../../eng-us/standards/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
