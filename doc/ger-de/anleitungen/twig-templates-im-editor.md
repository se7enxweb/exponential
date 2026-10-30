# Twig-Templates im Editor

Diese Seite ist für Template-Entwickler, die einen Editor oder eine IDE für die `.twig`-Dateien eines
Exponential-Designs einrichten. Ein Grund, Twig zu wählen, ist, dass verbreitete Editoren es schon verstehen:
Syntaxhervorhebung, Einklappen von Blöcken, passende Tags, Snippets. Diese Seite sagt, was zu installieren ist, was
einzustellen ist, damit der Editor die Dateien von Exponential richtig behandelt, was die eigenen Twig-Prüfungen eines
Editors nicht wissen können und wie Sie die Prüfung von Exponential in den Editor holen. Sie nennt verbreitete Werkzeuge
zur Orientierung; sie bewertet sie nicht, und die Namen von Editor-Erweiterungen ändern sich mit der Zeit.

## Syntaxhervorhebung

| Editor | Twig-Unterstützung |
|---|---|
| PhpStorm und die anderen JetBrains-IDEs | eingebaut über das mitgelieferte Twig-Plugin (unter Settings, Plugins einschalten, falls es aus ist) |
| Visual Studio Code | über eine Erweiterung aus dem Marketplace; suchen Sie nach „Twig“ |
| Vim und Neovim | Neovims Tree-sitter hat eine Twig-Grammatik; für Vim gibt es Twig-Syntax-Plugins |
| Emacs | `web-mode` hebt Twig in HTML-Dateien hervor |
| Sublime Text | über ein Paket aus Package Control; suchen Sie nach „Twig“ |

Jeder davon taugt. Wichtig ist, dass der Editor beide Dateiendungen zuordnet, die Exponential rendert:

| Endung | Behandeln als |
|---|---|
| `.twig` | Twig (HTML) |
| `.html.twig` | Twig (HTML); die meisten Editoren tun das schon |
| `.tpl` | Exponential-Templates. Kein Editor bringt einen Modus dafür mit; Smarty-Modi kommen am nächsten, die Klammer-Syntax ist ähnlich. |
| `.stpl` | Smarty |

Für Visual Studio Code, in der `.vscode/settings.json` des Arbeitsbereichs:

```json
{
    "files.associations": {
        "*.twig": "twig",
        "*.stpl": "smarty"
    }
}
```

(Die Sprachkennungen sind die der installierten Erweiterungen; prüfen Sie deren Dokumentation.)

## Was die Twig-Prüfungen des Editors nicht wissen

Ein Editor oder ein allgemeiner Twig-Linter kennt Twigs eigene Filter, Funktionen und Tags. Was Exponential hinzufügt,
kennt er nicht, und er wird einwandfreie Templates markieren:

- jeden Template-Operator, als Filter oder Funktion benutzt: `wash`, `i18n`, `l10n`, `ezurl`, `ezroot`, `ezdesign`,
  `ezimage`, `fetch`, `cond`, `first_set`, `gt` ... (etwa 1.700 Namen in einer Installation mit den
  Standard-Extensions);
- die Template-Funktionen: `attribute_view_gui`, `node_view_gui` und die anderen;
- die `exp`-Tags: `expinclude`, `expcacheblock`, `expsetblock`, `expappendblock`, `exprunonce`, `expset`, `expwhile`,
  `expundefall`, und jede Blockfunktion einer Extension als Tag;
- die `exp_`-Namen (`exp_count`, `exp_merge`).

Umgekehrt kennt der Editor die Sandbox von Exponential nicht: Er bietet gern `{% do %}`, `|filter` oder `|map` an, die
die Sandbox standardmäßig ablehnt.

Schalten Sie die Warnungen „unbekannter Filter / unbekannte Funktion“ dafür aus oder ignorieren Sie sie, und lassen Sie
die eigene Prüfung von Exponential entscheiden. Sie kennt genau die Operatoren, Funktionen und Tags des Siteaccess und
die Sandbox, die er anwendet.

## Die Prüfung von Exponential im Editor

`exp:template:check` schreibt Formate, die Editoren und CI-Werkzeuge lesen: `checkstyle`, `sarif`, `junit`, `github`,
`json` und `jsonl`. Für eine Datei:

```bash
./console exp:template:check extension/<ext>/design/<design>/templates/full/article.twig --format=checkstyle
```

```
<?xml version="1.0" encoding="UTF-8"?>
<checkstyle version="4.3">
 <file name="var/tmp/twigdocs/check/typo.twig">
  <error line="1" severity="error" message="Unknown &quot;wahs&quot; filter." source="exp.template.check.TWIG-SYNTAX-001"/>
 </file>
</checkstyle>
```

**Visual Studio Code**: eine Aufgabe, die die offene Datei prüft und die Befunde in den Bereich „Probleme“ bringt, in
`.vscode/tasks.json`:

```json
{
    "version": "2.0.0",
    "tasks": [
        {
            "label": "exp:template:check (this file)",
            "type": "shell",
            "command": "./console exp:template:check ${relativeFile} --level=strict",
            "problemMatcher": {
                "owner": "exp-template-check",
                "fileLocation": ["relative", "${workspaceFolder}"],
                "pattern": [
                    { "regexp": "^(ERROR|WARN|NOTE)\\s+\\w+\\s+(.+?):(\\d+)(?::(\\d+))?$", "severity": 1, "file": 2, "line": 3, "column": 4 },
                    { "regexp": "^\\s+(.*)$", "message": 1 }
                ]
            }
        }
    ]
}
```

Das Muster liest die Ausgabe für Menschen (`ERROR  twig  <datei>:<zeile>`, gefolgt von einer eingerückten Zeile mit der
Meldung). Skripte und Plugins sollten stattdessen ein Maschinenformat lesen: Die Ausgabe für Menschen darf sich mit der
Zeit verbessern, die Maschinenformate sind versioniert (`"schema":"exp-template-check/1"`).

**JetBrains-IDEs**: Richten Sie die Konsole als externes Werkzeug ein (Settings, Tools, External Tools): Programm
`./console`, Argumente `exp:template:check $FilePathRelativeToProjectRoot$ --level=strict`, Arbeitsverzeichnis
`$ProjectFileDir$`. Ein Ausgabefilter wie `$FILE_PATH$:$LINE$` macht die Befundzeilen anklickbar. Ein Plugin, das
Checkstyle-Berichte liest, kann die Ausgabe von `--format=checkstyle` direkt im Text zeigen.

**Vor einem Commit**: `./console exp:template:check --changed-since=HEAD --level=compile` prüft nur die Templates, die Sie
geändert haben; [Template-Syntax prüfen](template-syntaxpruefung.md) hat einen Pre-Commit-Hook und Beispiele für die CI.

**Eine Zeile mit Absicht**: Eine Zeile, die stimmt, die die Prüfung aber nicht kennen kann (ein Operator einer Extension,
die auf der prüfenden Maschine nicht aktiv ist), kann einen Ignorier-Kommentar für eine Regel tragen:

```twig
{# exp:template:check ignore-next-line TWIG-STRICT-UNKNOWN-OPERATOR #}
{{ node.name|exp_my_future_operator }}
```

Mit dem Kommentar ist die Datei bei `--level=strict` sauber; ohne ihn ist die zweite Zeile eine Warnung.

## Formatierung

- Twig behält Leerraum, wie er geschrieben ist, außer dem ersten Zeilenumbruch nach einem Tag und dem, was `{%-`/`-%}`
  abschneiden. Ein Formatierer, der Templates umbricht, kann die Ausgabe einer Seite ändern; lassen Sie ihn nur über
  Templates laufen, deren Leerraum keine Rolle spielt, und vergleichen Sie eine Seite vorher und nachher.
- Halten Sie die Dateien in UTF-8 ohne Byte Order Mark, mit Unix-Zeilenenden. `exp:template:check --encoding` meldet
  BOMs (`ENCODING-BOM-002`) und CRLF-Zeilenenden (`ENCODING-CRLF-003`).
- Ein Eintrag in `.editorconfig` hält die Editoren im Gleichschritt:

```ini
[*.{twig,tpl,stpl}]
charset = utf-8
end_of_line = lf
insert_final_newline = false
indent_style = space
indent_size = 4
```

(`insert_final_newline = false`, weil ein abschließender Zeilenumbruch gedruckt wird: Ein Template, das in einem Attribut
oder einem `<title>` eingebunden wird, sollte nicht mit einem enden.)

## Umgewandelte Templates

Ein Template, das `exp:template:convert` geschrieben hat, endet mit einem Markierungskommentar:

```twig
{# exp:template:convert tpl->twig md5:00d79f61df01a62bcbbf3cc10778bfba from templates/parts/cookie_settings_link.tpl -- generated; a file changed by hand is not replaced without --force #}
```

Lassen Sie ihn stehen. Ein späterer Umwandlungslauf vergleicht die Datei mit der Prüfsumme und ersetzt keine Datei, die
Sie von Hand geändert haben; haben Sie eine Datei endgültig übernommen, dürfen Sie die Zeile entfernen, und der Konverter
fasst die Datei dann ohne `--force` nie wieder an. „Dokument formatieren“ Ihres Editors zählt als Änderung von Hand.

## Snippets, die sich lohnen

Die meisten Twig-Snippet-Sammlungen decken Twigs eigene Tags ab. Diese von Exponential tippen Sie am häufigsten:

| Präfix | Wird zu |
|---|---|
| `wash` | `{{ $1\|wash }}` |
| `i18n` | `{{ '$1'\|i18n('$2') }}` |
| `ezurl` | `{{ $1\|ezurl }}` |
| `fetch` | `{% set $1 = fetch('content', '${2:list}', {parent_node_id: $3}) %}` |
| `attr` | `{{ attribute_view_gui({attribute: node.data_map.$1}) }}` |
| `nodeview` | `{{ node_view_gui({content_node: $1, view: '${2:line}'}) }}` |
| `expinclude` | `{% expinclude uri='design:$1' %}` |
| `expcache` | `{% expcacheblock keys=[$1] %}$0{% endexpcacheblock %}` |

## Verwandte Seiten

- [Template-Syntax prüfen](template-syntaxpruefung.md)
- [Fehlersuche in Twig-Templates](twig-templates-fehlersuche.md)
- [Twig-Templates nativ schreiben](twig-templates-nativ-schreiben.md)
- [Twig templates in your editor](../../eng-us/guides/twig-templates-editors.md) (Englisch)
