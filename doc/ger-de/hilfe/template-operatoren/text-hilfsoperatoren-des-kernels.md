---
title: Text-Hilfsoperatoren des Kernels
audience: developer, designer, app-creator
category: development
order: 340
summary: Fünf kleine Kernel-Operatoren - alphabet, autolink, simpletags, eztoc und month_overview - mit ihren Parametern, den Einstellungen, die sie lesen, was sie maskieren und was nicht, und auf alpha gerenderten Beispielen.
keywords: Text-Hilfsoperatoren, Inhaltsverzeichnis, Kalender, Archiv, Links, Maskierung, Monatsübersicht, alphabet, autolink, simpletags, eztoc, month_overview, table of contents, calendar, archive, links, mailto, AlphabeticalFilterSettings, SimpleTagsOperator, AutoLinkOperator
type: help
---
# Text-Hilfsoperatoren des Kernels

Fünf Operatoren in `kernel/common/`, jeder in seiner eigenen Klasse und registriert in `kernel/common/eztemplateautoload.php`.

| Operator | Klasse und Datei |
|---|---|
| `alphabet` | `eZAlphabetOperator`, `kernel/common/ezalphabetoperator.php` |
| `autolink` | `eZAutoLinkOperator`, `kernel/common/ezautolinkoperator.php` |
| `simpletags` | `eZSimpleTagsOperator`, `kernel/common/ezsimpletagsoperator.php` |
| `eztoc` | `eZTOCOperator`, `kernel/common/eztocoperator.php` |
| `month_overview` | `eZDateOperatorCollection`, `kernel/common/ezdateoperatorcollection.php` |

## Inhalt

1. [Die Operatoren](#die-operatoren): [alphabet](#alphabet), [autolink](#autolink), [simpletags](#simpletags),
   [eztoc](#eztoc), [month_overview](#month_overview)
2. [Übersicht der Maskierung](#übersicht-der-maskierung)

## Die Operatoren

### alphabet

Generierte Seite: [alphabet](../../../eng-us/examples/template-operators/alphabet.md).

- **Eingabe:** keine. **Parameter:** keine.
- **Gibt zurück:** die Liste der Buchstaben für eine alphabetische Navigation oder `false`, wenn sie ausgeschaltet ist.

`eZAlphabetOperator::fetchAlphabet()` liest `content.ini` `[AlphabeticalFilterSettings]`: Für jeden Namen in
`ContentFilterList[]` nimmt es die Bereiche aus `AlphabetList[<name>]` (Unicode-Codepunkte wie `97-122` oder einzelne Buchstaben,
durch Kommas getrennt), bildet daraus die Buchstaben und wandelt sie in den Zeichensatz der `i18n.ini` um. Eine leere
`ContentFilterList[]` ergibt `false`; eine Liste, deren Namen keine Bereiche haben, fällt auf `a` bis `z` zurück. Auf alpha ist die
Liste leer:

```tpl
{def $letters = alphabet()}
ContentFilterList: {ezini( 'AlphabeticalFilterSettings', 'ContentFilterList', 'content.ini' )|count} entries
alphabet() gives {$letters|get_type}
```

Ausgabe auf alpha:

```text
ContentFilterList: 0 entries
alphabet() gives boolean[false]
```

### autolink

Generierte Seite: [autolink](../../../eng-us/examples/template-operators/autolink.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `max_chars` | Ganzzahl | nein | `template.ini` `[AutoLinkOperator] MaxCharacters` (72) |

- **Eingabe:** ein Text. **Gibt zurück:** den Text mit Links.

Zuerst macht der Operator aus jeder E-Mail-Adresse ein `<a href='mailto:...'>`, dann aus jeder Adresse der Schemata in
`[AutoLinkOperator] Methods[]` ein `<a href="..." title="...">`. Ein Linktext, der länger als `max_chars` ist, wird in der Mitte mit
`...` gekürzt. Adressen, die bereits in Attributen `href=`, `src=` oder `value=` stehen, werden übersprungen. Der Text wird **nicht
maskiert**: Waschen Sie Benutzereingaben zuerst (`|wash|autolink`), sonst wird darin enthaltenes Markup durchgereicht.

```tpl
{def $text = 'Mail info@example.com or read https://doc.exponential.earth/en/latest/guide/templates/operators/index.html today.'}
{$text|autolink}

{$text|autolink( 30 )}
```

Ausgabe auf alpha:

```text
Mail <a href='mailto:info@example.com'>info@example.com</a> or read <a href="https://doc.exponential.earth/en/latest/guide/templates/operators/index.html" title="https://doc.exponential.earth/en/latest/guide/templates/operators/index.html">https://doc.exponential.earth/en/...guide/templates/operators/index.html</a> today.

Mail <a href='mailto:info@example.com'>info@example.com</a> or read <a href="https://doc.exponential.earth/en/latest/guide/templates/operators/index.html" title="https://doc.exponential.earth/en/latest/guide/templates/operators/index.html">https://doc....tors/index.html</a> today.
```

### simpletags

Generierte Seite: [simpletags](../../../eng-us/examples/template-operators/simpletags.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `listname` | Zeichenkette | nein | `false`: verwendet `TagList[]`; ein Name verwendet `TagList_<name>[]` |

- **Eingabe:** ein Text mit einfachen Tags wie `<strong>`. **Gibt zurück:** HTML.

Die Tag-Liste steht in `template.ini` `[SimpleTagsOperator]`. Jeder Eintrag lautet `TagList[<tag>]=<before>;<after>;<php
functions>`: Der Text zwischen Start- und End-Tag wird durch die aufgeführten PHP-Funktionen geleitet und in `<before>` und
`<after>` eingeschlossen. Der Eintrag `text` behandelt alles außerhalb bekannter Tags; ohne ihn wird einfacher Text durch
`htmlspecialchars` geleitet. Unbekannte Tags werden als Text behandelt und daher maskiert. Die mitgelieferte Liste:

```ini
TagList[text]=;;htmlspecialchars,nl2br
TagList[literal]=<pre>;</pre>;htmlspecialchars
TagList[code]=<pre class="code">;</pre>;htmlspecialchars
TagList[strong]=<b>;</b>;htmlspecialchars,nl2br
TagList[emphasize]=<i>;</i>;htmlspecialchars,nl2br
```

`IncludeList[]` nennt PHP-Dateien, die vor der ersten Verwendung eingebunden werden (gesucht im Ordner `simpletags/` der
Erweiterungen in `Extensions[]`), sodass ein Tag eine eigene PHP-Funktion verwenden kann. Jeder vorhandene Funktionsname wird
aufgerufen; daher gehören dort nur vertrauenswürdige Einstellungen hin.

```tpl
{def $text = "Plain <b>&</b> text\n<strong>bold</strong> and <emphasize>italic</emphasize>\n<literal><a href=\"x\"></literal>"}
{$text|simpletags}
```

Ausgabe auf alpha:

```text
Plain &lt;b&gt;&amp;&lt;/b&gt; text<br />
<b>bold</b> and <i>italic</i><br />
<pre>&lt;a href=&quot;x&quot;&gt;</pre>
```

### eztoc

Generierte Seite: [eztoc](../../../eng-us/examples/template-operators/eztoc.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `dom` | Objekt | ja | 0 |

- **Eingabe:** keine. **Parameter:** ein Inhaltsobjekt-Attribut vom Typ `ezxmltext` (alles andere ergibt eine leere Zeichenkette).
- **Gibt zurück:** verschachtelte Listen `<ul><li><a href="#eztoc<attribute id>_<n>">` der Überschriften des Rich Text, mit für HTML
  maskiertem Überschriftentext.

Die Ankernamen folgen der Nummerierung der Abschnitte: `eztoc498_3_1` ist die erste Überschrift im dritten Abschnitt der obersten
Ebene von Attribut 498. Der Zähler einer Ebene wird unter einem neuen übergeordneten Abschnitt nicht zurückgesetzt, daher zählt die
zweite Ebene über die übergeordneten Abschnitte hinweg weiter (auf `3_3` folgt unten `5_4`). Die Rich-Text-Ausgabe desselben
Attributs muss passende Anker enthalten, damit die Links funktionieren.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
{eztoc( $node.data_map.body )}
```

Ausgabe auf alpha:

```text

<ul><li><a href="#eztoc498_1">Summary</a></li>
<li><a href="#eztoc498_2">1. Who we are and what this policy covers</a></li>
<li><a href="#eztoc498_3">2. Information we collect</a>
<ul><li><a href="#eztoc498_3_1">Information you provide to us</a></li>
<li><a href="#eztoc498_3_2">Information collected automatically</a></li>
<li><a href="#eztoc498_3_3">Information from third parties</a></li>
</ul></li>
<li><a href="#eztoc498_4">3. How we use your information, our legal bases and how long we keep it</a></li>
<li><a href="#eztoc498_5">4. Cookies and similar technologies</a>
<ul><li><a href="#eztoc498_5_4">Embedded content from other websites</a></li>
</ul></li>
<li><a href="#eztoc498_6">5. How we disclose your information</a>
<ul><li><a href="#eztoc498_6_5">Service providers and processors</a></li>
<li><a href="#eztoc498_6_6">Other disclosures</a></li>
<li><a href="#eztoc498_6_7">&quot;Sale&quot; and &quot;sharing&quot; under US state laws</a></li>
</ul></li>
<li><a href="#eztoc498_7">6. International data transfers</a></li>
<li><a href="#eztoc498_8">7. Retention</a></li>
<li><a href="#eztoc498_9">8. Security</a></li>
<li><a href="#eztoc498_10">9. Your privacy rights</a>
<ul><li><a href="#eztoc498_10_8">9.1 Rights we offer everyone</a></li>
<li><a href="#eztoc498_10_9">9.2 United States</a>
<ul><li><a href="#eztoc498_10_9_1">Federal laws</a></li>
<li><a href="#eztoc498_10_9_2">California: CCPA as amended by the CPRA, CalOPPA and &quot;Shine the Light&quot;</a></li>
<li><a href="#eztoc498_10_9_3">Other US states with comprehensive privacy laws</a></li>
<li><a href="#eztoc498_10_9_4">Nevada</a></li>
<li><a href="#eztoc498_10_9_5">Washington: My Health My Data Act</a></li>
</ul></li>
<li><a href="#eztoc498_10_10">9.3 European Union and European Economic Area</a></li>
<li><a href="#eztoc498_10_11">9.4 United Kingdom</a></li>
<li><a href="#eztoc498_10_12">9.5 Switzerland</a></li>
<li><a href="#eztoc498_10_13">9.6 Canada</a></li>
<li><a href="#eztoc498_10_14">9.7 Brazil</a></li>
<li><a href="#eztoc498_10_15">9.8 China</a></li>
<li><a href="#eztoc498_10_16">9.9 Japan</a></li>
<li><a href="#eztoc498_10_17">9.10 South Korea</a></li>
<li><a href="#eztoc498_10_18">9.11 India</a></li>
<li><a href="#eztoc498_10_19">9.12 Singapore, Thailand and the wider Asia-Pacific region</a></li>
<li><a href="#eztoc498_10_20">9.13 Australia and New Zealand</a></li>
<li><a href="#eztoc498_10_21">9.14 Africa</a></li>
<li><a href="#eztoc498_10_22">9.15 Middle East and Turkey</a></li>
<li><a href="#eztoc498_10_23">9.16 Latin America</a></li>
<li><a href="#eztoc498_10_24">9.17 Other jurisdictions</a></li>
</ul></li>
<li><a href="#eztoc498_11">10. Children&#039;s privacy</a></li>
<li><a href="#eztoc498_12">11. Do Not Track, Global Privacy Control and third-party tracking</a></li>
<li><a href="#eztoc498_13">12. Changes to this policy</a></li>
<li><a href="#eztoc498_14">13. How to contact us and exercise your rights</a>
<ul><li><a href="#eztoc498_14_25">Verifying your identity</a></li>
<li><a href="#eztoc498_14_26">Response times</a></li>
</ul>
</li>
</ul>
```

### month_overview

Generierte Seite: [month_overview](../../../eng-us/examples/template-operators/month_overview.md).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `field` | Zeichenkette | ja | `false` (fehlt er: Warnung „Missing parameter field“) |
| `date` | Ganzzahl (Zeitstempel) | ja | `false` (fehlt er: Warnung „Missing parameter date“) |
| `optional` | Array (Hash) | nein | `false` |

- **Eingabe:** eine Liste von Objekten (alles mit `hasAttribute()`/`attribute()`) oder Hashes. Der Wert von `field` in jedem
  Element ist ein Zeitstempel; die Tage des Monats von `date`, zu denen es ein Element gibt, erhalten einen Link.
- **Gibt zurück:** einen Hash mit `year`, `month` (lokalisierter Name), `weekdays` (Liste mit `day`-Kurzname und `class`
  `sun`..`sat`, in der Wochenreihenfolge des Locale), `weeks` (Wochennummer => sieben Plätze, `false` oder ein Hash mit `day`,
  `link`, `class`, `highlight`), `current`, `next` und `previous` (jeweils `month`, `year`, `link` oder `false`).
- **Schlüssel von optional:** `link` (Basis der Tageslinks, an die `/(year)/Y/(month)/M/(day)/D` angehängt wird), `current`
  (Zeitstempel eines hervorzuhebenden Tages) sowie `current_class`, `day_class`, `today_class`, `next` und `previous` (Hashes mit
  einem `link`; an die Monatslinks wird `/(year)/Y/(month)/M` angehängt). `year_link`, `month_link` und `day_link` werden gelesen,
  aber nicht verwendet.

Das Feld muss ein Attribut der Elemente selbst sein: `published` ist ein Attribut des Inhaltsobjekts, nicht des Knotens, daher
sammelt das Beispiel zuerst die Objekte.

```tpl
{def $recipes = fetch( 'content', 'list', hash( 'parent_node_id', 144, 'limit', 50, 'sort_by', array( 'published', false() ) ) )
     $objects = array()}
{foreach $recipes as $r}{set $objects = $objects|append( $r.object )}{/foreach}
{def $first = $objects[0].published
     $month = $objects|month_overview( 'published', $first, hash( 'link', '/content/view/archive/144', 'current', $first, 'current_class', 'is-current', 'day_class', 'day', 'previous', hash( 'link', '/content/view/archive/144' ) ) )}
{$month.current.month} {$month.current.year} (previous: {$month.previous.month} {$month.previous.link}; next: {$month.next|choose( 'false', 'set' )})

{foreach $month.weekdays as $wd}{$wd.day} {/foreach}


{foreach $month.weeks as $number => $week}
week {$number}: {foreach $week as $d}{if $d}{$d.day}{if $d.link}*{/if}{if $d.highlight}!{/if} {else}- {/if}{/foreach}


{/foreach}

a day with content links to {foreach $month.weeks as $week}{foreach $week as $d}{if and( $d, $d.link )}{$d.link} {/if}{/foreach}{/foreach}
```

Ausgabe auf alpha:

```text
August 2018 (previous: July /content/view/archive/144/(year)/2018/(month)/7; next: false)
Sun Mon Tue Wed Thu Fri Sat 
week 31: - - - 1 2 3 4 
week 32: 5 6 7 8* 9 10 11 
week 33: 12 13*! 14 15 16 17 18 
week 34: 19 20 21 22 23 24 25 
week 35: 26 27 28 29 30 31 - 

a day with content links to /content/view/archive/144/(year)/2018/(month)/8/(day)/8 /content/view/archive/144/(year)/2018/(month)/8/(day)/13
```

`*` kennzeichnet die beiden Tage mit einem Rezept (sie erhielten einen `link`), `!` den hervorgehobenen Tag `current`.

## Übersicht der Maskierung

| Operator | Maskiert er seine Eingabe? |
|---|---|
| `autolink` | nein: zuerst waschen |
| `simpletags` | ja, über die PHP-Funktionen jedes Tags (`htmlspecialchars` in der mitgelieferten Liste) |
| `eztoc` | Überschriftentexte werden maskiert |
| `month_overview`, `alphabet` | geben Daten zurück; beim Ausgeben maskieren |

## Verwandte Seiten

- [Zeichenketten-Operatoren für Designer](../../../eng-us/help/designers/templates/string-operators.md)
- [Waschen und Maskieren](../../../eng-us/help/designers/templates/wash-and-escaping.md)
- [Rich-Text-Templates](../../../eng-us/help/designers/templates/rich-text-templates.md)
- [Alle Template-Operatoren (generiert)](../../../eng-us/examples/template-operators/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
