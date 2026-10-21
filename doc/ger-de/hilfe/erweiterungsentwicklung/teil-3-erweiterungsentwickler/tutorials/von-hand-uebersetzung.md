---
title: "Von Hand: eine Erweiterung übersetzen"
audience: developer, app-creator, designer
category: extension-development
order: 210
summary: Eine Erweiterung ins Deutsche übersetzen - die Texte sammeln, die translation.ts-Dateien schreiben, sie mit TranslationExtensions registrieren, den Cache leeren und die deutschen Texte sehen.
keywords: Tutorial, von Hand, Übersetzung, Deutsch, unübersetzt, Kontext, Platzhalter, Übersetzungs-Cache, tutorial, by hand, translation, i18n, translation.ts, ger-DE, untranslated, TranslationExtensions, ezpI18n, context, placeholders, translation cache, xmllint
type: help
---
# Von Hand: eine Erweiterung übersetzen

Tutorial 20. Jeder Text von `mysite_hello` lief über `i18n` oder `ezpI18n::tr()` mit dem Kontext
`extension/mysite_hello`. Nun fügen Sie Deutsch hinzu. Mit den Dateien unten und registrierter Erweiterung gab
`ezpI18n::tr( 'extension/mysite_hello', 'Hello, %name!', null, array( '%name' => 'Ada' ) )` für die Locale `ger-DE`
`Hallo, Ada!` zurück.

## 1. Die Texte sammeln

```bash
grep -rhoE "'[^']+'\|i18n\( 'extension/mysite_hello'" extension/mysite_hello/design | sort -u
grep -rhoE "ezpI18n::tr\( 'extension/mysite_hello', '[^']+'" extension/mysite_hello --include=*.php | sort -u
```

Jede Zeile ist ein Quelltext des Kontexts.

## 2. Die deutsche Datei

`extension/mysite_hello/translations/ger-DE/translation.ts`:

```xml
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE TS>
<TS version="2.0" language="de_DE">
<context>
    <name>extension/mysite_hello</name>
    <message>
        <source>Hello, %name!</source>
        <translation>Hallo, %name!</translation>
    </message>
    <message>
        <source>Newest articles</source>
        <translation>Neueste Artikel</translation>
    </message>
    <message>
        <source>Compose a greeting</source>
        <translation>Einen Gruß verfassen</translation>
    </message>
</context>
</TS>
```

Das `source` muss genau mit dem Text im Code übereinstimmen, einschließlich der Platzhalter. Fügen Sie jeden Text
hinzu; ein fehlender erscheint auf Englisch.

Behalten Sie `translations/untranslated/translation.ts` mit denselben `source`-Zeilen und leeren Elementen
`<translation type="unfinished"></translation>` als Vorlage für den nächsten Übersetzer.

## 3. Prüfen und registrieren

```bash
xmllint --noout extension/mysite_hello/translations/ger-DE/translation.ts
```

In `extension/mysite_hello/settings/site.ini.append.php`:

```ini
[RegionalSettings]
TranslationExtensions[]=mysite_hello
```

Dann:

```bash
php bin/php/ezcache.php --clear-id=translation
php bin/php/ezcache.php --clear-tag=ini
```

## 4. Ansehen

Öffnen Sie `/hello/world/Ada` in einem Siteaccess, dessen `Locale` `ger-DE` ist (oder stellen Sie die Sprache der
Administration auf Deutsch um): „Hallo, Ada!“ und „Neueste Artikel“. Prüfen Sie die Abdeckung:

```bash
./console exp:i18n fallbacks --locale=ger-DE
```

listet pro Erweiterung auf, wie viel für die Locale und ihre Fallbacks übersetzt ist.

## Verwandte Seiten

- Zurück: [Von Hand: ein Custom Tag für Rich Text](von-hand-custom-tag.md)
- Weiter: [Von Hand: Hilfeseiten für Ihre Erweiterung](von-hand-hilfeseiten-fuer-ihre-erweiterung.md)
- [Übersetzungen in einer Erweiterung](../uebersetzungen-in-einer-erweiterung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
