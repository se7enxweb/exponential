---
title: Newsletter-Operatoren von cjw_newsletter
audience: developer, designer, app-creator
category: development
order: 450
summary: Die dreizehn Template-Operatoren der Erweiterung cjw_newsletter - drei allgemeine (cjw_newsletter_str_replace, cjw_newsletter_preg_replace, cjw_newsletter_variable) und zehn Operatoren für die Ausgabe (Bedingungen, Umwandlung in Klartext und Rich Text, Interessenblock, absolute URLs, Unterstreichen, Umbrechen, Ausgabedaten) - mit Parametern, Rückgabewerten, wann die Bedingungsmarken erscheinen, und Beispielen, die auf alpha ausgegeben wurden.
keywords: cjw_newsletter, Newsletter, Newsletter-Operatoren, Klartext, Bedingungen, Interessen, absolute URL, Textumbruch, cjw_newsletter_str_replace, cjw_newsletter_preg_replace, cjw_newsletter_variable, cjwnl_condition_open, cjwnl_condition_else, cjwnl_condition_close, cjwnl_plaintext, cjwnl_richtext, cjwnl_interests_block, cjwnl_abs_url, cjwnl_text_underline, cjwnl_text_wrap, cjwnl_rendering_data, newsletter operators, plain text
type: help
---
# Newsletter-Operatoren von cjw_newsletter

Die Erweiterung `cjw_newsletter` registriert zwei Operatorklassen in `extension/cjw_newsletter/autoloads/eztemplateautoload.php`
(und `ExtensionAutoloadPath[]=cjw_newsletter`). Es gibt sie nur, solange die Erweiterung aktiv ist, und sie sind für
die Templates gedacht, die Newsletter-Ausgaben darstellen: den HTML-Teil, den Klartextteil und das Webarchiv.

| Klasse | Datei | Operatoren |
|---|---|---|
| `CjwNewsletterOperators` | `autoloads/cjwnewsletteroperators.php` | `cjw_newsletter_str_replace`, `cjw_newsletter_preg_replace`, `cjw_newsletter_variable` |
| `CjwNewsletterRenderingOperators` | `classes/rendering/cjwnewsletterrenderingoperators.php` | die zehn `cjwnl_*`-Operatoren |

## Inhalt

1. [Allgemeine Operatoren](#allgemeine-operatoren): [cjw_newsletter_str_replace](#cjw_newsletter_str_replace),
   [cjw_newsletter_preg_replace](#cjw_newsletter_preg_replace), [cjw_newsletter_variable](#cjw_newsletter_variable)
2. [Textausgabe](#textausgabe): [cjwnl_plaintext](#cjwnl_plaintext), [cjwnl_richtext](#cjwnl_richtext),
   [cjwnl_text_underline](#cjwnl_text_underline), [cjwnl_text_wrap](#cjwnl_text_wrap), [cjwnl_abs_url](#cjwnl_abs_url)
3. [Marken und Daten](#marken-und-daten): [cjwnl_condition_open](#cjwnl_condition_open),
   [cjwnl_condition_else](#cjwnl_condition_else), [cjwnl_condition_close](#cjwnl_condition_close),
   [cjwnl_interests_block](#cjwnl_interests_block), [cjwnl_rendering_data](#cjwnl_rendering_data)

## Allgemeine Operatoren

### cjw_newsletter_str_replace

Parameter `string_search` und `string_replace` (Zeichenketten, Pflicht). PHP `str_replace()` auf die Eingabe.

### cjw_newsletter_preg_replace

Parameter `string_search` (ein regulärer Ausdruck) und `string_replace` (Pflicht). PHP `preg_replace()` auf die Eingabe.
Verwenden Sie nur feste Muster.

### cjw_newsletter_variable

Parameter `variable_name` (Pflicht). Der einzige Name, den er kennt, ist `available_subscription_status_id_name_array`:
die IDs der Abonnementstatus und ihre Namen (`CjwNewsletterSubscription::availableStatusIdNameArray()`). Jeder andere
Name ergibt `false`. Zeile 7 des zweiten Beispiels.

## Textausgabe

### cjwnl_plaintext

Keine Parameter. Eingabe: ein `ezxmltext`- oder Rich-Text-Attribut (oder dessen gespeichertes XML). Gibt Klartext für
den Textteil einer Mail zurück (`CjwNewsletterPlainText::xmlText()`): DocBook-Rich-Text wandelt
`CjwNewsletterRichText::toText()` um, das ältere XML-Format der Klartext-Ausgabehandler der Erweiterung; Überschriften
werden unterstrichen, Hervorhebungen mit `*` gekennzeichnet.

### cjwnl_richtext

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `format` | Zeichenkette | nein | `html` |

Eingabe: DocBook-Rich-Text (das Format `ezrichtext`). Gibt HTML mit Inline-Styles für Mailprogramme zurück, oder mit
`format` `text` Klartext. Jeder Text wird maskiert; Links bleiben nur mit den Schemata `http`, `https`, `mailto` und
`ez*` erhalten, und ein Link auf Inhalt wird zur absoluten Adresse seines Knotens.

### cjwnl_text_underline

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `char` | Zeichenkette | nein | `=` |

Gibt die getrimmte Eingabe zurück, gefolgt von einer Zeile aus `char` in der Länge des Textes, mindestens 3 und
höchstens 72 Zeichen.

### cjwnl_text_wrap

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `width` | Ganzzahl | nein | 72 (mindestens 20 werden verwendet) |

Bricht den Text nach `width` Zeichen um (`CjwNewsletterPlainText::wrap()`).

### cjwnl_abs_url

Keine Parameter. Macht einen Pfad absolut: mit dem Schema und dem Host der Website (`eZURI::transformURI( ..., 'full' )`)
oder mit der Basisadresse, die der Ausgabebefehl für eine Ausgabe setzt. Adressen mit einem Schema, `//` oder `#`
werden unverändert zurückgegeben.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
{def $docbook = '<section xmlns="http://docbook.org/ns/docbook" version="5.0-variant ezpublish-1.0"><para>Hello <emphasis role="strong">world</emphasis></para><itemizedlist><listitem><para>one</para></listitem><listitem><para>two</para></listitem></itemizedlist></section>'}

1 cjw_newsletter_str_replace: {'Hello [name]'|cjw_newsletter_str_replace( '[name]', 'reader' )}
2 cjw_newsletter_preg_replace: {'Order 2026-10-19'|cjw_newsletter_preg_replace( '/(\d+)-(\d+)-(\d+)/', '$3.$2.$1' )}
3 cjwnl_text_underline: {'Monthly news'|cjwnl_text_underline}
4 cjwnl_text_underline with -: {'Ok'|cjwnl_text_underline( '-' )}
5 cjwnl_text_wrap 30: {'Exponential sends newsletters as HTML and as plain text, wrapped for mail clients.'|cjwnl_text_wrap( 30 )}
6 cjwnl_abs_url: {'/privacy-policy'|cjwnl_abs_url} / {'mailto:info@example.com'|cjwnl_abs_url}
7 cjwnl_richtext text: {$docbook|cjwnl_richtext( 'text' )}
7b cjwnl_richtext html: {$docbook|cjwnl_richtext}
8 cjwnl_plaintext of node 90 body: {$node.data_map.body|cjwnl_plaintext|extract_left( 160 )}
```

Ausgabe auf alpha:

```text

1 cjw_newsletter_str_replace: Hello reader
2 cjw_newsletter_preg_replace: Order 19.10.2026
3 cjwnl_text_underline: Monthly news
============
4 cjwnl_text_underline with -: Ok
---
5 cjwnl_text_wrap 30: Exponential sends newsletters
as HTML and as plain text,
wrapped for mail clients.
6 cjwnl_abs_url: http://alpha.se7enx.com/privacy-policy / mailto:info@example.com
7 cjwnl_richtext text: Hello *world*

- one
- two
7b cjwnl_richtext html: <p style="margin:0 0 1em 0;">Hello <strong>world</strong></p>
<ul style="margin:0 0 1em 0;padding:0 0 0 1.5em;"><li style="margin:0 0 .3em 0;">one</li><li style="margin:0 0 .3em 0;">two</li></ul>
8 cjwnl_plaintext of node 90 body: Effective date: [Date]

Last updated: [Date]

Summary
=======

- *Who we are*: this website is operated from the United States by [Legal name of the operator] (
```

Die Adresse in Zeile 6 verwendet `http`, weil das Beispiel auf der Kommandozeile lief.

## Marken und Daten

### cjwnl_condition_open

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `settings` | Array (Hash) | nein | leer |

Beginnt einen Textteil, den nur ein Teil der Abonnenten erhält. Während die Ausgabe einer Newsletter-Ausgabe erzeugt
wird, gibt er die Marke `[[cjwnl:if:<id>:<settings as base64url JSON>]]` zurück; der Versandschritt behält oder
entfernt den Teil je Abonnent. Die Einstellungen: `field` (`salutation`, `first_name`, `last_name`, `organisation`,
`email`, `language`, `custom_data_text_1` bis `4`), `operator` (`eq`, `ne`, `contains`, `starts`, `empty`,
`not_empty`, `in`), `value`, `list`, `language`, `interest`, `negate` (dokumentiert in
`classes/rendering/cjwnewsletterconditions.php`).

**Außerhalb des Ausgabebefehls sind die Marken leere Zeichenketten**, daher erscheinen auf der Website beide Zweige;
das ist in Zeile 1 unten geschehen.

### cjwnl_condition_else

Keine Parameter. Die Else-Marke der innersten offenen Bedingung oder eine leere Zeichenkette.

### cjwnl_condition_close

Keine Parameter. Die Endmarke der innersten offenen Bedingung oder eine leere Zeichenkette.

### cjwnl_interests_block

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `limit` | Ganzzahl | nein | 0 |
| `heading` | Zeichenkette | nein | leer |
| `accent` | Zeichenkette | nein | leer |

Gibt eine Marke `[[cjwnl:interests:<limit>:<options>]]` zurück (limit wird zwischen 0 und 20 gehalten, `accent` nur
verwendet, wenn es eine Farbe `#rrggbb` ist), die je Abonnent durch die Liste seiner Interessen ersetzt wird.

### cjwnl_rendering_data

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `name` | Zeichenkette | ja | leer |
| `param` | beliebig | nein | `false` |

Liefert den Bearbeitungs- und Ausgabe-Templates die Daten, die sie brauchen. Namen: `available_skins`, `skin_settings`
(param: Skin), `allowed_skins` und `list_of_node` (param: Knoten-ID), `list_languages` und `main_language` (param:
Objekt-ID der Liste), `content_languages`, `interests` (param: Liste), `all_interests`, `interest_user_count` (param:
Interessen-ID), `placeholder_names`, `condition_fields`, `user_choices` (param: Benutzer-ID) und `plaintext_template`
(param: Datentyp-Zeichenkette; der `design:`-Pfad eines Klartext-Override-Templates oder `false`). Unbekannte Namen
ergeben `false`.

```tpl
1 condition: {cjwnl_condition_open( hash( 'field', 'interest', 'value', 'running' ) )}A{cjwnl_condition_else()}B{cjwnl_condition_close()}
2 interests block: {cjwnl_interests_block( 3, 'Your topics' )}
3 skins: {cjwnl_rendering_data( 'available_skins' )|implode( ', ' )}
4 placeholders: {cjwnl_rendering_data( 'placeholder_names' )|implode( ', ' )}
5 condition fields: {foreach cjwnl_rendering_data( 'condition_fields' ) as $k => $v}{$k}{delimiter}, {/delimiter}{/foreach}

6 unknown name: {cjwnl_rendering_data( 'nosuchname' )|get_type}
7 subscription statuses: {foreach cjw_newsletter_variable( 'available_subscription_status_id_name_array' ) as $id => $name}{$id}={$name}{delimiter}, {/delimiter}{/foreach}
```

Ausgabe auf alpha:

```text
1 condition: AB
2 interests block: [[cjwnl:interests:3:eyJoIjoiWW91ciB0b3BpY3MiLCJhIjoiIn0]]
3 skins: default, company, news, shop
4 placeholders: [[name]], [[salutation_name]], [[first_name]], [[last_name]], [[email]], [[organisation]], [[custom_1]], [[custom_2]], [[custom_3]], [[custom_4]], [[list_name]], [[unsubscribe_url]], [[configure_url]], [[manage_url]]
5 condition fields: 0, 1, 2, 3, 4, 5, 6, 7, 8, 9
6 unknown name: boolean[false]
7 subscription statuses: 0=Pending, 1=Confirmed, 2=Approved, 3=Removed by user, 4=Removed by admin, 6=Bounced soft, 7=Bounced hard, 8=Blacklisted
```

## Verwandte Seiten

- [Benachrichtigungen und Mail für Anwendungen](../ersteller/anwendungsentwickler/benachrichtigungen-und-mail.md)
- [Alle Template-Operatoren (generiert)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
