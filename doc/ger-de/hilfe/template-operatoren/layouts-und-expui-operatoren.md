---
title: Operatoren von Layouts, expui und expai
audience: developer, designer, app-creator
category: development
order: 460
summary: Die Template-Operatoren der Exponential-Layout-Erweiterungen (expl_parse, expl_strip, expl_has, expl_first, explayouts_ui_has_template), von expui (exp_config) und von expai (expai_money, expai_tokens, expai_number, expai_retention, expai_json), mit Parametern, Rückgabewerten, Maskierung und Beispielen, auf alpha gerendert, die von expai auf dem Siteaccess admin.
keywords: Blockmarkierungen, Layouts, Konfigurationsblock, KI-Nutzung, Maskierung, explayouts, expl_parse, expl_strip, expl_has, expl_first, explblock, explayouts_ui_has_template, expui, exp_config, expai, expai_money, expai_tokens, expai_number, expai_retention, expai_json
type: help
---
# Operatoren von Layouts, expui und expai

Drei Familien von Exponential-Erweiterungen bringen eigene Operatoren mit. Jeder existiert nur dort, wo seine Erweiterung
aktiv ist: explayouts, explayouts_ui und expui auf den Websites und in der Administration, expai nur auf den
Administrations-Siteaccesses von alpha (seine Operatoren sind auf `site` nicht registriert).

| Erweiterung | Klasse und Datei | Operatoren |
|---|---|---|
| explayouts | `ExplBlockOperator`, `extension/explayouts/classes/explblockoperator.php` | `expl_parse`, `expl_strip`, `expl_has`, `expl_first` |
| explayouts_ui | `expLayoutsUiTemplateOperators`, `extension/explayouts_ui/classes/explayoutsuitemplateoperators.php` | `explayouts_ui_has_template` |
| expui | `expUITemplateOperators`, `extension/expui/classes/expuitemplateoperators.php` | `exp_config` |
| expai | `expAiTemplateOperators`, `extension/expai/autoloads/expaitemplateoperators.php` | `expai_money`, `expai_tokens`, `expai_number`, `expai_retention`, `expai_json` |

## Inhalt

1. [Blockmarkierungen von explayouts](#blockmarkierungen-von-explayouts): [expl_parse](#expl_parse), [expl_strip](#expl_strip),
   [expl_has](#expl_has), [expl_first](#expl_first)
2. [explayouts_ui](#explayouts_ui): [explayouts_ui_has_template](#explayouts_ui_has_template)
3. [expui](#expui): [exp_config](#exp_config)
4. [expai](#expai): [expai_money](#expai_money), [expai_tokens](#expai_tokens), [expai_number](#expai_number),
   [expai_retention](#expai_retention), [expai_json](#expai_json)

## Blockmarkierungen von explayouts

Die Template-Funktion `{explblock}` von explayouts umschließt Teile der gerenderten Ausgabe mit Kommentarmarkierungen
`<!--expl:s:<name>-->` und `<!--expl:e:<name>-->` (Namen: ein Kleinbuchstabe, dann Kleinbuchstaben, Ziffern oder `_`).
Die vier Operatoren lesen solche Ausgabe über `ExplBlockParser` (`extension/explayouts/classes/explblockparser.php`),
einen Parser ohne Abhängigkeiten vom Kernel, sodass sie auch mit gecachtem HTML funktionieren. Alle vier nehmen bis zu
zwei Positionsparameter (beide `mixed`, optional); der erste wird nur verwendet, wenn es keine Eingabe gibt.

### expl_parse

Gibt einen Hash zurück: `blocks` (Name => Liste der Inhalte aller Blöcke dieses Namens, in Reihenfolge) und `stripped`
(die Eingabe ohne Markierungen). Überkreuzte Verschachtelung verwirft den inneren Rahmen; eine Endmarkierung ohne Anfang
wird ignoriert; ein am Ende noch offener Block reicht bis zum Ende des Textes.

### expl_strip

Gibt nur den Text ohne Markierungen zurück (`stripped` von `expl_parse`).

### expl_has

Eingabe: das HTML, das Ergebnis von `expl_parse` oder dessen `blocks`. Parameter: der Blockname. `true`, wenn dieser
Block existiert und nicht leer ist.

### expl_first

Eingabe: eine Liste (etwa `$parsed.blocks.title`): ihr erstes Element; eine Zeichenkette wird unverändert zurückgegeben;
alles andere ergibt eine leere Zeichenkette.

## explayouts_ui

### explayouts_ui_has_template

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `path` | Zeichenkette | ja | leer |

`true`, wenn ein Template mit diesem Pfad in einer der Designbasen des Siteaccess existiert
(`eZTemplateDesignResource::fileMatch()` auf `templates/`). Ein Pfad, der `..` enthält, ist immer `false`. Die
Layouts-Oberfläche verwendet den Operator, um optionale Teile nur dann einzubinden, wenn ein Design sie bereitstellt.

```tpl
{def $html = '<div><!--expl:s:title--><h1>Recipes</h1><!--expl:e:title--><!--expl:s:lead-->Fresh food<!--expl:e:lead--></div>'
     $parsed = $html|expl_parse}
1 expl_parse blocks: {foreach $parsed.blocks as $name => $parts}{$name} ({$parts|count}){delimiter}, {/delimiter}{/foreach}

2 expl_parse stripped: {$parsed.stripped}
3 expl_strip: {$html|expl_strip}
4 expl_first: {$parsed.blocks.title|expl_first}
5 expl_has title: {expl_has( $html, 'title' )|choose( 'no', 'yes' )}, footer: {$parsed|expl_has( 'footer' )|choose( 'no', 'yes' )}
6 explayouts_ui_has_template: {explayouts_ui_has_template( 'pagelayout.tpl' )|choose( 'no', 'yes' )} / {explayouts_ui_has_template( 'no/such/template.tpl' )|choose( 'no', 'yes' )} / {explayouts_ui_has_template( '../settings/site.ini' )|choose( 'no', 'yes' )}
```

Ausgabe auf alpha:

```text
1 expl_parse blocks: title (1), lead (1)
2 expl_parse stripped: <div><h1>Recipes</h1>Fresh food</div>
3 expl_strip: <div><h1>Recipes</h1>Fresh food</div>
4 expl_first: <h1>Recipes</h1>
5 expl_has title: yes, footer: no
6 explayouts_ui_has_template: yes / no / no
```

## expui

### exp_config

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `options` | Array (Hash) | nein | leer |

Gibt den Konfigurationsblock zurück, den der JavaScript-Kern von expui liest: `<script type="application/json"
data-exp-config>` mit `config` (Version, Wurzel- und www-Pfade, Siteaccess, Aufrufadresse von ezjscore, Adresse der
Einstellungen, Element-ID des Formular-Tokens, Trennzeichen, Locale-Code, HTTP-Locale und erster Wochentag), `strings`
und `prefs`. `options.strings` fügt zu übersetzende Texte (im Kontext `extension/expui`) zu denen aus `expui.ini`
`[ExpUI] Strings[]` hinzu; `options.prefs` nennt Benutzereinstellungen, die aufgenommen werden sollen (Namen, die auf
`[A-Za-z0-9_.-]{1,64}` passen). Das JSON wird mit maskierten `<`, `>`, `&` und Anführungszeichen kodiert, sodass kein Wert
das script-Element beenden kann. Ein Block mit `type="application/json"` sind Daten und braucht daher keine CSP-Nonce.
Geben Sie ihn ohne wash aus; das Beispiel wendet wash nur an, um das Tag zu zeigen:

```tpl
{def $script = exp_config( hash( 'strings', array( 'Close' ) ) )}
starts: {$script|extract_left( 48 )|wash}
ends:   {$script|extract_right( 9 )|wash}
```

Ausgabe auf alpha:

```text
starts: &lt;script type=&quot;application/json&quot; data-exp-config&gt;
ends:   &lt;/script&gt;
```

## expai

Die Operatoren von expai formatieren die Zahlen der Seiten zur KI-Nutzung. Gerendert auf dem Siteaccess `admin`:

### expai_money

Parameter `approx` (boolescher Wert, Standard `false`). Formatiert einen Betrag in Dollar (`expAiCost::money()`): kleine
Beträge als `< $0.01`, mit vorangestelltem `≈`, wenn `approx` wahr ist.

### expai_tokens

Keine Parameter. Gekürzte Token-Zahlen: `950`, `22k`, `1.2M` (`expAiCost::tokens()`).

### expai_number

Parameter `decimals` (Ganzzahl, Standard 0). Eine Zahl mit den Trennzeichen der Locale (`expAiCost::number()`).

### expai_retention

Keine Parameter. Ein Wert zur Datenaufbewahrung aus den Anbietereinstellungen (`none`, `zdr`, `30d`, `unknown`): `unknown`
wird übersetzt, jeder andere Wert wird so angezeigt, wie er geschrieben ist.

### expai_json

Keine Parameter. JSON der Eingabe für einen Block `<script type="application/json">`: `<`, `>`, `&`, `'` und `"` werden
als `\u`-Sequenzen maskiert, und ungültiges UTF-8 wird zu U+FFFD, statt das ganze Ergebnis leer zu machen. Ein Wert, der
sich nicht kodieren lässt, ergibt `null` und eine Warnung im Debug-Log.

```tpl
1 expai_money: {0.0625|expai_money} / {0.004|expai_money} / {0.0625|expai_money( true() )}
2 expai_tokens: {950|expai_tokens} / {21600|expai_tokens} / {1200000|expai_tokens}
3 expai_number: {1234567.891|expai_number( 2 )}
4 expai_retention: {'unknown'|expai_retention} / {'30d'|expai_retention}
5 expai_json: {hash( 'text', '</script><b>"x"</b>', 'n', 3 )|expai_json}
```

Ausgabe auf alpha:

```text
1 expai_money: $0.06 / < $0.01 / ≈$0.06
2 expai_tokens: 950 / 22k / 1.2M
3 expai_number: 1,234,567.89
4 expai_retention: unknown / 30d
5 expai_json: {"text":"<\/script><b>"x"<\/b>","n":3}
```

## Verwandte Seiten

- [ezjscore-Operatoren im Detail](ezjscore-operatoren.md) (`json_encode` für denselben Zweck ohne die zusätzliche Maskierung)
- [Der Layout-Editor](../layout-editor.md)
- [Content Security Policy für Anwendungen](../../../eng-us/help/creators/app-creators/content-security-policy.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
