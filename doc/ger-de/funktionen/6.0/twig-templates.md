# Twig-Templates (.twig) neben .tpl

Diese Seite ist für Site-Builder und Template-Entwickler, die Twig kennen oder ein Design darauf portieren wollen. Ein
Design kann jetzt Twig-3-Templates (`*.twig`, und `*.html.twig` für Templates, die mit Projekten im Symfony-Stil geteilt
werden) neben den üblichen Exponential-Templates (`*.tpl`) und Smarty-Templates (`*.stpl`) enthalten. Ein `.twig` wird
wie jedes Template gefunden, überschrieben, eingebunden und gecacht, sieht dieselben Variablen und kann jeden
Template-Operator von Exponential, jede Fetch-Funktion, die `*_view_gui`-Funktionen und jede Blockfunktion verwenden.
Ein Konverter wandelt ein ganzes Design aus `.tpl`-Dateien (oder `.stpl`-Dateien) in `.twig` um und zurück. Die
Funktion ist Teil der Kernel-Bibliothek: Der Code liegt in `lib/twig/`, die Einstellungen in `settings/twig.ini`, und
nichts muss aktiviert werden: `.twig`-Templates funktionieren in jedem Siteaccess. Hinzugefügt im Oktober 2026. Wenn Sie
von `.tpl` kommen: [Von .tpl zu .twig umziehen](../../hilfe/template-sprache/von-tpl-zu-twig-umziehen.md). Die Wahl
zwischen den drei Engines: [Die drei Template-Engines im Vergleich](../../anleitungen/template-engines.md).

Die Anleitungen gehen weiter:

| Anleitung | Für |
|---|---|
| [Ein erstes .twig, Schritt für Schritt](../../anleitungen/twig-templates-tutorial.md) | Ihr erstes Twig-Template: ein Siteaccess, eine Override-Regel, Variablen, Operatoren, ein Fetch, eine Schleife, ein Include |
| [.twig-Templates schreiben und ausführen](../../anleitungen/twig-templates.md) | wie es funktioniert: der Stub, die Einrichtung, Variablen und Zurückschreiben, Objekte, Includes, Schleifen, Funktionen, Tags, Caching, Fehlersuche, Velocity, Grenzen |
| [Operatoren und Fetch-Funktionen](../../anleitungen/twig-templates-operatoren.md) | Filter und Funktionen, wie ein Operator seine Eingabe bekommt, das Präfix `exp_`, `fetch()`, die Helfer |
| [Operator-Referenz](../../anleitungen/twig-templates-operator-referenz.md) | jeder Operator der Installation und ihrer Erweiterungen, mit seiner Filter- und Funktionsform |
| [Template-Funktionen und Tags](../../anleitungen/twig-templates-funktionen.md) | die `*_view_gui`-Funktionen, Blockfunktionen als Tags, die `exp*`-Tags, Twigs eigene Tags |
| [Caching](../../anleitungen/twig-templates-caching.md) | die Cache-Schichten, `{% expcacheblock %}`, der Seitenblock mit einem Platzhalter je Besucher, Schlüssel und Ablauf |
| [Leistung (Geschwindigkeit)](../../anleitungen/twig-templates-leistung.md) | was `.twig` schnell macht, die Messungen, wie Sie Ihre eigenen Seiten messen |
| [Der Konverter und das Portieren eines Designs](../../anleitungen/twig-templates-konverter.md) | `exp:template:convert:tpl-to-twig` und `:twig-to-tpl`, jedes Konstrukt, das sie schreiben, ihre Hinweise |
| [Ein Design migrieren](../../anleitungen/twig-templates-migrieren.md) | Planen, Konvertieren, Prüfen, Gleichheit Seite für Seite, die Engines im Gleichschritt halten |
| [Problembehebung und FAQ](../../anleitungen/twig-templates-problembehebung.md) | Fehlermeldungen mit ihrer Ursache und Behebung, häufige Fragen |
| [Operatoren und Funktionen schreiben](../../anleitungen/twig-templates-plugins.md) | eigene Operatoren und Funktionen, die in jeder Engine funktionieren |
| [Sicherheit und Maskierung](../../anleitungen/twig-templates-sicherheit.md) | die Sandbox, `wash`, Autoescape, was ein Template erreichen kann |
| [Twig-native Templates schreiben](../../anleitungen/twig-templates-nativ-schreiben.md) | Vererbung mit dem Override-System, `include` und `embed`, Makros, Tests, Maskierung, Leerraum, die Sandbox, die Variablen, die ein Template sieht |
| [Rezepte](../../anleitungen/twig-templates-rezepte.md) | ein Pagelayout, eine Line-Ansicht, ein Layouts-Block, ein Mail-Template, Cache-Blöcke je Rolle und Benutzer, ein Formular, Texte mit einer Anzahl, Bildvarianten |
| [Fehlersuche in Twig-Templates](../../anleitungen/twig-templates-fehlersuche.md) | die Prüfung, die Meldung in der Seite, Pfadkommentare, die Debug-Leiste, das Twig-Profil, eine Änderung, die sich nicht zeigt |
| [Twig in Ihrem Editor](../../anleitungen/twig-templates-im-editor.md) | Syntaxhervorhebung, Dateiendungen, was die Prüfungen eines Editors nicht wissen können, die Prüfung im Editor, Snippets |
| [Versionen, Upgrades und Kompatibilität](../../anleitungen/twig-templates-versionen-und-kompatibilitaet.md) | Twig je PHP-Version, Composer, fehlendes Twig, Upgrades, Velocity |
| [Geschwindigkeit der drei Template-Engines](../../anleitungen/template-engines-geschwindigkeit.md) | Twig, Smarty und `.tpl` gemessen: gecacht, ungecacht und das Debug-Rendering, mit Bedingungen und wie Sie es wiederholen |
| [Templates nebeneinander](../../hilfe/template-sprache/tpl-twig-smarty-nebeneinander.md) | dasselbe Template in `.tpl`, `.twig` und `.stpl`, und jeder Operator in Twig |
| [Templates zwischen Template-Engines konvertieren](../../anleitungen/template-konvertierung.md) | der Befehlsraum `exp:template:convert`, jede Richtung |
| [Template-Syntax prüfen](../../anleitungen/template-syntaxpruefung.md) | `exp:template:check` für `.tpl`, `.stpl` und `.twig` |

## Was es tut

- **Eine eigene Template-Engine, im selben Template-System.** Wenn die Design-Ressource (oder ein `{include}`) ein
  `.twig` findet, parst das Template-System es nicht: Es parst einen einzeiligen Stub, `{exp_twig_render( '<file>' )}`,
  der die Datei bei jeder Anfrage mit Twig rendert. Alles darum herum bleibt, wie es ist: Override-Regeln,
  Design-Fallback, `design:`-URIs, View-Caches, die `{cache-block}`s der aufrufenden Templates, die Debug-Leiste.
- **Dieselben Variablen.** Ein `.twig` sieht die Variablen seines Namensraums (`node`, `module_result`,
  `view_parameters` ...). Was es mit `{% set %}` setzt, ändert oder auf `null` setzt, wird zurückgeschrieben, wie es
  `{def}`, `{set}` und `{undef}` in einem `.tpl` tun, sodass ein Pagelayout, ein Modul oder ein Mail-Template es liest.
- **Jeder Operator.** `{{ node.name|wash }}`, `{{ 'Close'|i18n('design/standard/content') }}`,
  `{{ fetch('content', 'list', {parent_node_id: 2}) }}`. Ein Filter ist der Operator mit einer Eingabe, eine Funktion
  der Operator ohne eine, genau so, wie das `.tpl` ihn schreibt.
- **Jede Template-Funktion.** `{{ attribute_view_gui({attribute: node.data_map.body}) }}`,
  `{{ node_view_gui({content_node: child, view: 'line'}) }}`; eine Funktion mit Rumpf ist ein Tag:
  `{% explblock name='x' %}...{% endexplblock %}`.
- **Includes in jede Richtung.** `{% expinclude uri='design:parts/x.tpl' %}` bindet ein `.tpl`, `.stpl` oder `.twig`
  über die Design-Ressource ein, mit ihren Override-Regeln; ein `.tpl` bindet ein `.twig` mit `{include}` ein.
- **Caching.** `{% expcacheblock keys=[...] %}...{% endexpcacheblock %}` ist der `{cache-block}` des Template-Systems.
- **Eine standardmäßig eingeschaltete Sandbox.** Überbrückte Operatoren und Funktionen laufen mit den eigenen Regeln
  des Template-Systems; Twigs eigene Tags, Filter und Funktionen nur so, wie `twig.ini` sie auflistet; kein
  Methodenaufruf und keine Eigenschaft eines Objekts.
- **Konverter in jede Richtung** und eine **Syntaxprüfung** für alle drei Engines.

Ein erster Blick:

```twig
<h1>{{ node.name|wash }}</h1>
{% for child in fetch('content', 'list', {parent_node_id: node.node_id, limit: 3}) %}
    {{ node_view_gui({content_node: child, view: 'line'}) }}
{% endfor %}
```

## Einstellungen

`settings/twig.ini [TwigSettings]` (überschreiben Sie sie wie jede Einstellungsdatei):

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `LibraryPath` | leer | woher Twig 3 geladen wird, wenn der Composer-Autoload der Installation kein Twig 3 hat (`twig/twig` oder `se7enxweb/twig`): ein Composer-Verzeichnis (mit `vendor/autoload.php`) oder das Paket selbst |
| `AutoReload` | `enabled` | ein geändertes `.twig` wird neu kompiliert; `disabled` spart eine Dateiprüfung je Template (leeren Sie nach einem Deployment die Template-Caches) |
| `Debug` | `disabled` | Twigs Debug-Modus (`dump()`) |
| `StrictVariables` | `disabled` | eine undefinierte Variable druckt nichts, wie in einem `.tpl` |
| `Autoescape` | `disabled` | nichts wird maskiert, es sei denn, das Template sagt es (`\|wash`), wie in einem `.tpl`; siehe [Sicherheit und Maskierung](../../anleitungen/twig-templates-sicherheit.md) |
| `TemplateSuffixList[]` | `.twig`, `.html.twig` | die Endungen, die Twig rendert |
| `WhileLoopLimit` | `10000` | die höchste Zahl von Runden eines konvertierten `{while}` oder `{do}` |
| `FunctionList[]` | die `*_view_gui`-Funktionen | Template-Funktionen, die als Twig-Funktionen aufrufbar sind (jede Funktion ohne Rumpf wird ohnehin überbrückt) |
| `ParameterOperatorList[]` | leer | Operatoren, die nur ihre Parameter lesen (auch `site.ini [TemplateBridgeSettings] ParameterOperatorList[]`) |
| `Security` | `enabled` | die Sandbox |
| `SecurityAllowedTagList[]`, `SecurityAllowedFilterList[]`, `SecurityAllowedFunctionList[]` | Twigs sichere | was die Sandbox von Twigs eigenen erlaubt |
| `SecurityAllowedMethodList[]`, `SecurityAllowedPropertyList[]` | leer | erlaubte `Class::method`, `Class::property` |

Die Dateiendungen aller Engines und die Klasse, die jede rendert, stehen in `settings/site.ini [TemplateBridgeSettings]`
(`EngineSuffixList[]`, `RendererList[]`); die Konverter in `ConverterList[]`, die Syntaxprüfungen in `CheckerList[]`.

## Twig installieren

Twig 3 kommt mit einer Composer-Installation, über das Metapaket `se7enxweb/exponential-php-floor`, das der Kernel
verlangt (`~1.0.3 || ~1.1.4 || ~1.2.3 || ~1.3.3`). Es installiert das Twig-3-Release, das zu der PHP-Version passt, mit
der Composer läuft:

| PHP | Floor-Version | Twig-Paket |
|---|---|---|
| 7.4 | 1.0.3 | `se7enxweb/twig` `~3.11.4` |
| 8.0 | 1.1.4 | `se7enxweb/twig` `~3.19.1` |
| 8.1 bis 8.3 | 1.2.3 | `twig/twig` `^3.27` |
| 8.4 und später | 1.3.3 | `twig/twig` `^3.27` |

Die `composer.json` des Kernels verlangt `twig/twig` nicht selbst. Das Upstream-Paket `twig/twig` bis 3.26 ist von
Sicherheitshinweisen betroffen, und 3.27 und später brauchen PHP 8.1; mit eingeschalteter Sicherheitssperre von
Composer installiert sich daher keine einzelne `twig/twig`-Anforderung unter PHP 7.4 und 8.0. `se7enxweb/twig` stellt
dort dieselben `Twig\`-Klassen bereit. Es ersetzt `twig/twig`; fügen Sie einer Installation, die unter PHP 7.4 oder 8.0
läuft, also keine `twig/twig`-Anforderung hinzu: Composer würde dann die Sicherheitshinweise zu `twig/twig` auf
`se7enxweb/twig` anwenden und die Installation verweigern. Nach `composer update` ist nichts weiter nötig.

Ohne das Twig von Composer (oder um ein neueres Twig auszuprobieren) installieren Sie es in einem eigenen Verzeichnis
und richten `LibraryPath` darauf aus:

```bash
mkdir -p var/lib/twig-vendor && cd var/lib/twig-vendor
composer require twig/twig:^3
```

```ini
# settings/override/twig.ini.append.php
[TwigSettings]
LibraryPath=var/lib/twig-vendor
```

`./console exp:template:engines` zeigt, welche Engines verfügbar sind, ihre Versionen und Einstellungen:

```
AVAILABLE    tpl (Exponential templates) Exponential 6.0.15stable
AVAILABLE    stpl (Smarty templates) Smarty 5.8.5
AVAILABLE    twig (Twig templates)  Twig 3.30.0
PASS 3 engines available
```

## Wie schnell es ist

Ein `.twig` wird einmal zu PHP kompiliert (abgelegt in `<cache directory>/twig/compile-secure`, mit den Template-Caches
geleert), und eine Twig-Umgebung wird je Prozess gehalten, sodass die dauerhaften Worker von Velocity jedes Template
einmal kompilieren und laden. Auf einer Entwicklungsinstallation rendert das konvertierte Media-Design dieselben Seiten
wie `.tpl` und `.stpl` etwa mit der Geschwindigkeit von `.stpl`;
[Leistung (Geschwindigkeit)](../../anleitungen/twig-templates-leistung.md) enthält die Messungen und wie sie
vorgenommen wurden.

## Konvertieren und prüfen

```bash
./console exp:template:convert:tpl-to-twig --from=extension/<ext>/design/<design> --to=extension/<ext>/design/<design>_twig
./console exp:template:check --design-dir=extension/<ext>/design/<design>_twig
```

Die Konvertierung des Media-Designs (517 Templates) schreibt 517 `.twig`, die kompilieren, und jede auf einer
Entwicklungsinstallation getestete Seite rendert byte-genau, was das `.tpl`-Design rendert, unter Apache mit PHP-FPM und
unter Velocity. Die 453 Fälle der eigenen Tests der Template-Engine ergeben in `.twig` dieselbe Ausgabe wie in `.tpl`,
mit und ohne Sandbox.

## Grenzen

- Ein `.twig`, das von einem `.twig` im gemeinsamen Namensraum eingebunden wird, schreibt die Variablen zurück, die es
  setzt, wie es ein Smarty-Subtemplate tut; ein `{def}` eines eingebundenen `.tpl` ist lokal in seiner Datei und
  verschwindet, wenn sie endet.
- Twig 3 hat kein `break`/`continue`: Der Konverter schreibt sie als Flags; von Hand geschriebene Templates verwenden
  Schleifenfilter oder `{% if %}`.
- Template-Vererbung (`{% extends %}`, `{% block %}`) und Makros funktionieren in von Hand geschriebenen `.twig`, haben
  aber keine `.tpl`-Form: Der Rückwärtskonverter behält sie als Hinweis und Kommentar.
- Methodenaufrufe auf Objekten lehnt die Sandbox ab; lesen Sie Werte über ihre Attribute (`node.data_map`).
