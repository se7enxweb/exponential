# Twig templates in your editor

This page is for template developers who set up an editor or IDE for the `.twig` files of an Exponential design. One
of the reasons to choose Twig is that common editors already understand it: syntax highlighting, folding of blocks,
matching tags, snippets. This page says what to install, what to configure so the editor treats Exponential's files
right, what an editor's own Twig checks cannot know, and how to bring Exponential's checker into the editor. It names
common tools for orientation; it does not rank them, and the names of editor extensions change over time.

## Syntax highlighting

| Editor | Twig support |
|---|---|
| PhpStorm and the other JetBrains IDEs | built in through the bundled Twig plugin (enable it under Settings, Plugins, if it is off) |
| Visual Studio Code | through an extension from the marketplace; search for "Twig" |
| Vim and Neovim | Neovim's tree-sitter has a Twig grammar; for Vim there are Twig syntax plugins |
| Emacs | `web-mode` highlights Twig in HTML files |
| Sublime Text | through a package from Package Control; search for "Twig" |

Any of them will do. What matters is that the editor maps both file endings Exponential renders:

| Ending | Treat as |
|---|---|
| `.twig` | Twig (HTML) |
| `.html.twig` | Twig (HTML); most editors already do |
| `.tpl` | Exponential templates. No editor ships a mode for them; Smarty modes come closest, the brace syntax is similar. |
| `.stpl` | Smarty |

For Visual Studio Code, in the workspace's `.vscode/settings.json`:

```json
{
    "files.associations": {
        "*.twig": "twig",
        "*.stpl": "smarty"
    }
}
```

(The language identifiers are those of the extensions you installed; check their documentation.)

## What the editor's Twig checks do not know

An editor or a generic Twig linter knows Twig's own filters, functions and tags. It does not know what Exponential
adds, and will mark perfectly good templates:

- every template operator used as a filter or function: `wash`, `i18n`, `l10n`, `ezurl`, `ezroot`, `ezdesign`,
  `ezimage`, `fetch`, `cond`, `first_set`, `gt` ... (about 1,700 names on an installation with the standard
  extensions);
- the template functions: `attribute_view_gui`, `node_view_gui` and the others;
- the `exp` tags: `expinclude`, `expcacheblock`, `expsetblock`, `expappendblock`, `exprunonce`, `expset`, `expwhile`,
  `expundefall`, and every block function of an extension as a tag;
- the `exp_` names (`exp_count`, `exp_merge`).

The other way round, the editor does not know Exponential's sandbox: it will happily offer `{% do %}`, `|filter` or
`|map`, which the sandbox refuses by default.

Turn off "unknown filter/function" warnings for these, or ignore them, and let Exponential's own checker decide. It
knows exactly the operators, functions and tags of the siteaccess, and the sandbox it applies.

## Exponential's checker in the editor

`exp:template:check` writes formats editors and CI tools read: `checkstyle`, `sarif`, `junit`, `github`, `json` and
`jsonl`. On one file:

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

**Visual Studio Code**: a task that checks the open file and puts the findings in the Problems panel, in
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

The pattern reads the human output (`ERROR  twig  <file>:<line>` followed by an indented message line). Scripts and
plugins should read a machine format instead: the human output may improve over time, the machine formats are
versioned (`"schema":"exp-template-check/1"`).

**JetBrains IDEs**: add the console as an external tool (Settings, Tools, External Tools): program `./console`,
arguments `exp:template:check $FilePathRelativeToProjectRoot$ --level=strict`, working directory `$ProjectFileDir$`.
An output filter such as `$FILE_PATH$:$LINE$` makes the finding lines clickable. A plugin that reads checkstyle
reports can show the `--format=checkstyle` output inline.

**Before a commit**: `./console exp:template:check --changed-since=HEAD --level=compile` checks only the templates you
changed; [Checking template syntax](template-syntax-check.md) has a pre-commit hook and CI examples.

**One line on purpose**: a line that is right but that the checker cannot know about (an operator of an extension
that is not active on the machine where you check) can carry an ignore comment for one rule:

```twig
{# exp:template:check ignore-next-line TWIG-STRICT-UNKNOWN-OPERATOR #}
{{ node.name|exp_my_future_operator }}
```

With the comment the file checks clean at `--level=strict`; without it, the second line is a warning.

## Formatting

- Twig keeps whitespace as written, except the first newline after a tag and what `{%-`/`-%}` trim. A formatter that
  reflows templates can change the output of a page; run one only on templates whose whitespace does not matter, and
  compare a page before and after.
- Keep the files UTF-8 without a byte order mark, with Unix line endings. `exp:template:check --encoding` reports
  BOMs (`ENCODING-BOM-002`) and CRLF line endings (`ENCODING-CRLF-003`).
- An `.editorconfig` entry keeps editors in step:

```ini
[*.{twig,tpl,stpl}]
charset = utf-8
end_of_line = lf
insert_final_newline = false
indent_style = space
indent_size = 4
```

(`insert_final_newline = false` because a final newline is printed: a template included inside an attribute or a
`<title>` should not end with one.)

## Converted templates

A template written by `exp:template:convert` ends with a marker comment:

```twig
{# exp:template:convert tpl->twig md5:00d79f61df01a62bcbbf3cc10778bfba from templates/parts/cookie_settings_link.tpl -- generated; a file changed by hand is not replaced without --force #}
```

Leave it in place. A later conversion run compares the file with the checksum and does not replace a file you
changed by hand; once you have taken over a file for good, you may remove the line, and the converter will then never
touch the file again without `--force`. Your editor's "format document" counts as a change by hand.

## Snippets worth having

Most Twig snippet collections cover Twig's own tags. These are the Exponential ones you will type most:

| Prefix | Expands to |
|---|---|
| `wash` | `{{ $1\|wash }}` |
| `i18n` | `{{ '$1'\|i18n('$2') }}` |
| `ezurl` | `{{ $1\|ezurl }}` |
| `fetch` | `{% set $1 = fetch('content', '${2:list}', {parent_node_id: $3}) %}` |
| `attr` | `{{ attribute_view_gui({attribute: node.data_map.$1}) }}` |
| `nodeview` | `{{ node_view_gui({content_node: $1, view: '${2:line}'}) }}` |
| `expinclude` | `{% expinclude uri='design:$1' %}` |
| `expcache` | `{% expcacheblock keys=[$1] %}$0{% endexpcacheblock %}` |

## Related

- [Checking template syntax](template-syntax-check.md)
- [Debugging Twig templates](twig-templates-debugging.md)
- [Writing Twig-native templates](twig-templates-native.md)
- [The converter and porting a design](twig-templates-converter.md)
