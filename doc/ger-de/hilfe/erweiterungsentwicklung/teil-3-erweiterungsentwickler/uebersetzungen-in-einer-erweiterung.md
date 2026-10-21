---
title: Übersetzungen in einer Erweiterung
audience: developer, app-creator, designer
category: extension-development
order: 250
summary: Jeder Text in jeder Sprache - i18n in Templates und ezpI18n::tr() in PHP, translation.ts-Dateien, TranslationExtensions, der Cache, Fallbacks zwischen Locales und Prüfungen der Abdeckung.
keywords: Übersetzung, Kontext, Locale, unübersetzt, Fallback, Übersetzungs-Cache, Platzhalter, translation, i18n, ezpI18n, tr, context, translation.ts, TS file, TranslationExtensions, locale, eng-US, ger-DE, untranslated, fallback, translation cache, ezlupdate, placeholders
type: help
---
# Übersetzungen in einer Erweiterung

Teil 3, Kapitel 25. Exponential läuft in vielen Sprachen, und die Texte einer Erweiterung sollten der Sprache des
Benutzers folgen, wie es die des Kernels tun. Dieses Kapitel zeigt, wie Sie Texte kennzeichnen, wo Übersetzungen liegen
und wie sie gefunden werden.

## Jeden Text kennzeichnen

In Templates mit dem Operator `i18n` und einem **Kontext**:

```text
{'Read more'|i18n( 'extension/mysite_hello' )}
{'Hello %name'|i18n( 'extension/mysite_hello',, hash( '%name', $name|wash ) )}
```

Die zweite Zeile, von der Template-Engine mit `Ada` gerendert, gibt `Hello Ada` aus. Das leere zweite Argument ist der
optionale **Kommentar**, ein Hinweis für Übersetzer.

In PHP mit `ezpI18n::tr( $context, $source, $comment = null, $arguments = null )` (`kernel/common/ezpi18n.php`):

```php
<?php
$text = ezpI18n::tr( 'extension/mysite_hello', 'There are %count greetings.', null, array( '%count' => $count ) );
```

| Regel | Warum |
|---|---|
| Ein Kontext pro Erweiterung oder pro Bereich davon: `extension/mysite_hello`, `extension/mysite_hello/admin` | Übersetzer sehen die Texte zusammen |
| Ganze Sätze mit Platzhaltern, nie zusammengeklebte Stücke | die Wortstellung unterscheidet sich zwischen Sprachen |
| Platzhalter beginnen mit `%` | `%name`, `%count` |
| Amerikanisches Englisch als Quelltext | die Quellsprache des Projekts; `license`, `color` |
| Maskieren Sie Platzhalter, die von Personen stammen | `hash( '%name', $name\|wash )` |

## Die Dateien

```text
extension/mysite_hello/translations/
    untranslated/translation.ts   every source text, no translations (a template for translators)
    eng-US/translation.ts         English, when it differs from the source
    ger-DE/translation.ts
    fre-FR/translation.ts
```

Eine `.ts`-Datei ist Qt-Linguist-XML. Aus der deutschen Datei der Erweiterung `expqueue`:

```xml
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE TS>
<TS version="2.0" language="de_DE">
<context>
    <name>extension/expqueue</name>
    <message>
        <source>Job queue</source>
        <translation>Auftragswarteschlange</translation>
    </message>
</context>
</TS>
```

## Die Dateien registrieren

`extension/mysite_hello/settings/site.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[RegionalSettings]
TranslationExtensions[]=mysite_hello

*/ ?>
```

`expqueue` registriert sich genau auf diese Weise. Leeren Sie danach den Übersetzungs-Cache:

```bash
php bin/php/ezcache.php --clear-id=translation
```

## Fallbacks

Eine Locale ohne eigene Übersetzung kann auf eine verwandte zurückfallen (zum Beispiel österreichisches Deutsch auf
Deutsch). Die Ketten stehen in `i18n.ini [TranslationSettings]`; die Konsole zeigt sie an und auch, wie viel vom Kernel
und von jeder Erweiterung abgedeckt ist:

```bash
./console exp:i18n fallbacks --locale=ger-AT
./console exp:i18n check
```

Siehe [Übersetzungen und Sprachen](../../../../eng-us/features/6.0/translations-and-languages.md) und
[Sprachen](../../sprachen.md).

## Die Dateien erzeugen

- **Von Hand**: Für eine kleine Erweiterung ist es in Ordnung, die `.ts`-Dateien direkt zu schreiben; halten Sie
  `untranslated/` vollständig.
- **ezlupdate**: Der Kernel liefert den Quellcode von `ezlupdate` (ein Qt-basiertes Werkzeug, das `i18n`-Texte aus
  Templates und PHP extrahiert) in `support/ezlupdate-qt4.5/` aus; seine `README.txt` erklärt, wie Sie es bauen. Die
  `.ts`-Dateien lassen sich dann in Qt Linguist oder einem beliebigen XML-Editor bearbeiten.

## Prüfen

```bash
./console exp:ezchecktranslation ger-DE
```

zeigt Statistiken zu einer Übersetzung. Schalten Sie die Administration auf eine andere Sprache um (**Mein Konto** oder
ein Siteaccess mit einer anderen `Locale`) und sehen Sie sich Ihre Seiten an: Jeder verbliebene englische Text ist ein
Text, der nicht mit `i18n` gekennzeichnet ist, in der Datei fehlt oder dessen Cache nicht geleert wurde.

## Verwandte Seiten

- Zurück: [Ajax mit ezjscore-Serverfunktionen](ajax-mit-ezjscore-serverfunktionen.md)
- Weiter: [Persistente Objekte und Ihre eigenen Tabellen](persistente-objekte-und-tabellen.md)
- [Von Hand: eine Erweiterung übersetzen](tutorials/von-hand-uebersetzung.md)
- [Übersetzungen und Sprachen](../../../../eng-us/features/6.0/translations-and-languages.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
