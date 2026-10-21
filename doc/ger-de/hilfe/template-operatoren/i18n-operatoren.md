---
title: Die Übersetzungsoperatoren i18n, d18n und x18n
audience: developer, designer, app-creator
category: development
order: 310
summary: Die drei Übersetzungsoperatoren von eZi18nOperator, ihre Parameter (Kontext, Kommentar, Argumente, Erweiterung), wie ezpI18n::tr() eine Übersetzung findet und Argumente einsetzt, wie kompilierte Templates beim Kompilieren übersetzen und warum es d18n gibt, mit Beispielen, die auf alpha auf Englisch und Deutsch gerendert wurden.
keywords: i18n, d18n, x18n, Übersetzung, übersetzen, Kontext, Argumente, Sprache, translation, translate, ezpI18n, eZi18nOperator, context, arguments, translation.ts, ezlupdate, locale
type: help
---
# Die Übersetzungsoperatoren i18n, d18n und x18n

`eZi18nOperator` (`kernel/common/ezi18noperator.php`) registriert `i18n`, `x18n` und `d18n` in
`kernel/common/eztemplateautoload.php`. Alle drei enden in `ezpI18n::tr()` (`kernel/common/ezpi18n.php`), derselben
Funktion, mit der PHP-Code übersetzt.

## Inhalt

1. [Die Operatoren](#die-operatoren): [i18n](#i18n), [d18n](#d18n), [x18n](#x18n)
2. [Wie ein Text übersetzt wird](#wie-ein-text-übersetzt-wird)
3. [Beispiele](#beispiele)
4. [Kompilierte Templates](#kompilierte-templates)

## Die Operatoren

### i18n

Generierte Seite: [i18n](../../beispiele/template-operatoren/i18n.md).

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| `context` | Zeichenkette | nein | `false` |
| `comment` | Zeichenkette | nein | leer |
| `arguments` | Hash | nein | `false` |

- **Eingabe:** der Quelltext (amerikanisches Englisch).
- **Rückgabe:** der übersetzte Text mit eingesetzten Argumenten.

Der Kontext benennt die Gruppe von Meldungen in der Übersetzungsdatei (`design/admin/content/edit`). Der Kommentar
unterscheidet zwei gleiche Quelltexte innerhalb eines Kontexts. Die Argumente werden nach der Übersetzung mit `strtr()`
ersetzt: Ein Schlüssel wie `%count` wird so ersetzt, wie er geschrieben ist, ein ganzzahliger Schlüssel `n` ersetzt
`%1` bis `%9` (`'%' . ( ( n % 9 ) + 1 )`), sodass `array( 3, 7 )` die Platzhalter `%1` und `%2` füllt.

Das Werkzeug `ezlupdate` sucht in Templates nach `|i18n(` mit einem konstanten Text, um die Übersetzungsdateien zu
erstellen; deshalb sollte der Text ein Literal sein.

### d18n

Generierte Seite: [d18n](../../beispiele/template-operatoren/d18n.md).

Dieselben Parameter und derselbe Code wie bei `i18n`. Der Kommentar im Konstruktor sagt, warum es ihn gibt: Er ist ein
Alias "for use with dynamic variables as input where you don't want ezlupdate to pick up the string", also für
dynamische Variablen als Eingabe, deren Zeichenkette `ezlupdate` nicht aufnehmen soll, zum Beispiel Menübezeichnungen
aus Einstellungen. Die Admin-Designs verwenden ihn so für die Menüeinträge aus `menu.ini`
(`design/admin4/templates/parts/ini_menu.tpl`).

### x18n

Generierte Seite: [x18n](../../beispiele/template-operatoren/x18n.md).

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| `extension` | Zeichenkette | ja | `false` |
| `context` | Zeichenkette | nein | `false` |
| `comment` | Zeichenkette | nein | leer |
| `arguments` | Hash | nein | `false` |

Der erste Parameter nennt eine Erweiterung, danach folgen die Parameter von `i18n`. Im aktuellen Code liest `modify()`
den Namen der Erweiterung, verwendet ihn aber nicht: Der Text wird genau wie bei `i18n` mit `ezpI18n::tr()` aus den
Übersetzungen aller aktiven Erweiterungen übersetzt. `x18n` hat keine Template-Hinweise und wird daher nie beim
Kompilieren übersetzt.

## Wie ein Text übersetzt wird

`ezpI18n::tr( $context, $source, $comment, $arguments )`:

1. Mit `[RegionalSettings] TextTranslation=disabled` (und ohne dynamische Übersetzungen) werden nur die Argumente
   eingesetzt.
2. Für das Locale `eng-GB` (das Quell-Locale, ohne Übersetzungsdatei) werden nur die Argumente eingesetzt.
3. Andernfalls wird die `translation.ts` des Locales des Siteaccess geladen (aus `[RegionalSettings]
   TranslationRepository`, dann aus dem Ordner `translations/` jeder Erweiterung in `TranslationExtensions[]`) und
   zwischengespeichert, außer bei `TranslationCache=disabled`. Mit aktiviertem `[RegionalSettings] DevelopmentMode`
   werden fehlende Texte in "Bork"-Form zwischen eckigen Klammern angezeigt.
4. Ein Text, den die Datei nicht enthält, kommt unverändert zurück. Für `eng-US` geschieht das stillschweigend (die
   Quelltexte sind amerikanisches Englisch); für andere Locales wird die Debug-Meldung "Missing translation for message
   in context" geschrieben.

## Beispiele

Dasselbe Template zweimal gerendert: auf dem Siteaccess `site` (Locale eng-US) und auf `bold_ger` (Locale ger-DE).

```tpl
{def $label = 'Search'}
locale: {ezini( 'RegionalSettings', 'Locale' )}
1 {'Search'|i18n( 'design/standard/content/search' )}
2 {'%count items'|i18n( 'design/admin/node/view/full', '', hash( '%count', 12 ) )}
3 {'Page %1 of %2'|i18n( 'example/context', 'positional arguments', array( 3, 7 ) )}
4 {$label|d18n( 'design/standard/content/search' )}
5 {'Search'|x18n( 'myextension', 'design/standard/content/search' )}
```

Ausgabe auf alpha:

```text
locale: eng-US
1 Search
2 12 items
3 Page 3 of 7
4 Search
5 Search
```

Auf `bold_ger`:

```text
locale: ger-DE
1 Suchen
2 12 items
3 Page 3 of 7
4 Suchen
5 Suchen
```

`Search` hat im Kontext `design/standard/content/search` eine deutsche Übersetzung (`Suchen`); die beiden anderen
Texte stehen in keiner deutschen Datei und kommen daher so zurück, wie sie geschrieben sind, mit eingesetzten
Argumenten. `x18n` (Zeile 5) hat wie `i18n` übersetzt.

## Kompilierte Templates

`operatorTemplateHints()` gibt `i18n` und `d18n` die Transformation `i18nTrans()`. Sind Text, Kontext und Kommentar
Konstanten, übersetzt der Compiler den Text **beim Kompilieren** und schreibt die übersetzte Zeichenkette in die
kompilierte Datei; sind auch die Argumente konstant, schreibt er Code, der nur noch die Argumente einsetzt. Ein
kompiliertes Template behält daher die Übersetzung vom Zeitpunkt des Kompilierens: Leeren Sie nach einer Änderung an
einer `translation.ts` den Übersetzungs-Cache und die kompilierten Templates
(`php bin/php/ezcache.php --clear-id=template,translation`).

## Verwandte Seiten

- [Der Operator i18n für Designer](../designer/vorlagen/der-i18n-operator.md)
- [Zeichenketten in Templates übersetzen](../designer/vorlagen/template-texte-uebersetzen.md)
- [Alle Template-Operatoren (generiert)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
