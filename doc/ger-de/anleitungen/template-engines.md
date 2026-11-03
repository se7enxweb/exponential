# Die drei Template-Engines im Vergleich

Diese Anleitung richtet sich an Site-Bauer und Template-Entwickler, die zwischen den Template-Engines von Exponential
wählen: Exponential-Templates (`.tpl`), Smarty-Templates (`.stpl`) und Twig-Templates (`.twig`, `.html.twig`). Alle drei
laufen im selben Template-System, sehen dieselben Variablen, können jeden Operator, jede Fetch-Funktion und jede
Template-Funktion aufrufen und lassen sich in einem Design und auf einer Seite mischen. Was sich unterscheidet, sind die
Sprache, ihre Werkzeuge und wer sie schon kennt.

## Auf einen Blick

| | `.tpl` | `.stpl` | `.twig` |
|---|---|---|---|
| Sprache | die eigene Template-Sprache von Exponential | Smarty 5 | Twig 3 |
| Bibliothek | der Kernel | Smarty 5 (`smarty/smarty`, `[SmartySettings] LibraryPath`) | Twig 3 (`twig/twig` ab PHP 8.1, `se7enxweb/twig` auf 7.4 und 8.0, beide über `se7enxweb/exponential-php-floor`; `[TwigSettings] LibraryPath`) |
| Einstellungen | `template.ini` | `smarty.ini` | `twig.ini` |
| Kompiliert zu | PHP (`<cache>/template/compiled`) | PHP (`<cache>/smarty/compile-secure`) | PHP-Klassen (`<cache>/twig/compile-secure`) |
| Operatoren | nach Namen | als Modifier; `exp_<name>`, wo Smarty den Namen schon hat | als Filter und Funktionen; `exp_<name>`, wo Twig den Namen schon hat |
| Template-Funktionen | `{attribute_view_gui ...}` | `{attribute_view_gui ...}` (dieselben Parameter) | `{{ attribute_view_gui({...}) }}` |
| Einbinden | `{include uri=...}` | `{expinclude uri=...}` | `{% expinclude uri=... %}` |
| Cache-Block | `{cache-block}` | `{expcacheblock}` | `{% expcacheblock %}` |
| Sandbox | die Regeln des Template-Systems | Smartys Sicherheitsrichtlinie, standardmäßig an | Twigs Sandbox, standardmäßig an |
| Maskierung | `wash` | `wash` (kein automatisches Maskieren) | `wash` (standardmäßig kein automatisches Maskieren) |
| Vererbung, Makros | nein | `{extends}`, `{block}`, `{function}`, soweit `smarty.ini` sie erlaubt | `{% extends %}`, `{% block %}`, `{% macro %}` |
| Debug-Leiste | ja | ja, mit der `.stpl`-Datei | ja, mit der `.twig`-Datei |
| Konverter | in beide anderen und aus beiden anderen | in beide anderen und aus beiden anderen | in beide anderen und aus beiden anderen |

## Welche wählen

- **`.tpl`** für ein bestehendes Design und für Entwickler, die Exponential kennen: Jedes Beispiel, jedes mitgelieferte
  Design und die Templates jeder Erweiterung sind `.tpl`, und es muss nichts installiert werden.
- **`.stpl`** für ein Team, das Smarty kennt, oder ein Design, das aus einem Smarty-Projekt kommt.
- **`.twig`** für ein Team, das Twig aus Symfony, Drupal oder Craft kennt, oder für Templates, die mit einem solchen
  Projekt geteilt werden (`.html.twig`). Die Editoren und Werkzeuge, die Twig unterstützen, arbeiten mit den Dateien,
  so wie sie sind.

Die Geschwindigkeit entscheidet es nicht: Gecachte Seiten erreichen nie eine Template-Engine, und ungecachte Seiten
rendern mit geringem Abstand zueinander. Gemessen auf einer Entwicklungsinstallation renderte ein konvertiertes Design
ungecachte Seiten als `.twig` in der 0,83- bis 0,98-fachen Zeit des `.tpl`-Designs und der 0,90- bis 1,19-fachen Zeit des
`.stpl`-Designs; mit eingeschalteter Debug-Ausgabe brauchten `.twig`- und `.stpl`-Seiten 0,037 bis 0,044 s und
`.tpl`-Seiten 0,059 bis 0,110 s. Jede Zahl mit ihren Bedingungen, das Profil einer Anfrage und wie Sie selbst messen:
[Geschwindigkeit der drei Template-Engines](template-engines-geschwindigkeit.md) ([Twig-Leistung](twig-templates-leistung.md),
[Smarty-Leistung](smarty-templates-leistung.md)).

## Eine lesen, wenn Sie eine andere kennen

[Templates nebeneinander](../hilfe/template-sprache/tpl-twig-smarty-nebeneinander.md) stellt dasselbe Template in allen
drei Sprachen nebeneinander, Zeile für Zeile, jede Zeile mit identischer Ausgabe gerendert. Für Twig, das auf die
Twig-Art geschrieben ist (Vererbung, `embed`, Makros, Tests), siehe [Twig-native Templates
schreiben](twig-templates-nativ-schreiben.md); für vollständige Ausgangspunkte die
[Twig-Rezepte](twig-templates-rezepte.md).

## Sie mischen

Jedes Template eines Designs kann zu jeder Engine gehören. Die Design-Ressource findet `x.tpl`, `x.stpl` oder `x.twig`,
wie es die Override-Regeln und die Reihenfolge der Designs vorgeben, und jede Engine bindet die anderen darüber ein:

```
{include uri='design:parts/menu.twig'}                 .tpl bindet ein .twig ein
{expinclude uri='design:parts/menu.tpl'}               .stpl bindet ein .tpl ein (oder was eine Override-Regel nennt)
{% expinclude uri='design:parts/menu.stpl' %}          .twig bindet ein .stpl ein
```

Variablen gehen mit dem Einbinden hinüber (seine Parameter, der gemeinsame Namensraum). Ein Design kann deshalb ein
Template nach dem anderen umstellen: das Pagelayout konvertieren, den Rest behalten und mehr konvertieren, wenn es
passt.

## Zwischen ihnen konvertieren

Sechs Befehle konvertieren in jede Richtung, und `exp:template:convert` nimmt `--from-engine` und `--to-engine`:

```bash
./console exp:template:engines
./console exp:template:convert:tpl-to-twig --from=<design> --to=<design>_twig
./console exp:template:convert:twig-to-stpl --from=<design>_twig --to=<design>_stpl
```

Jede konvertierte Datei wird geprüft, bevor sie geschrieben wird, und trägt eine Markierung, sodass ein zweiter Lauf
nur ersetzt, was konvertiert und seitdem nicht geändert wurde; siehe [Templates zwischen Template-Engines
konvertieren](template-konvertierung.md). `exp:template:check` prüft die Syntax aller drei
([Template-Syntax prüfen](template-syntaxpruefung.md)).

## Verwandte Seiten

- [Twig-Templates](../funktionen/6.0/twig-templates.md)
- [Smarty-Templates](../funktionen/6.0/smarty-templates.md)
- [Zwischen Template-Engines umwandeln](../hilfe/template-sprache/zwischen-template-engines-umwandeln.md)
- [Geschwindigkeit der drei Template-Engines](template-engines-geschwindigkeit.md)
- [Templates nebeneinander: .tpl, .twig und .stpl](../hilfe/template-sprache/tpl-twig-smarty-nebeneinander.md)
- [Twig-Templates: Versionen, Aktualisierungen und Kompatibilität](twig-templates-versionen-und-kompatibilitaet.md)
