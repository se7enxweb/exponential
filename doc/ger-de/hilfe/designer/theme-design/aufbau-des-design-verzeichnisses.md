---
title: Der Aufbau des Design-Verzeichnisses
audience: designer
category: theme-design
order: 50
summary: Jeder Ordner, den ein Design haben kann, was Kernel und Webserver mit jedem tun, aus welchen Ordnern Browser direkt laden dürfen und wie die Designs media und starter dieser Installation ihre Templates ordnen.
keywords: Design-Ordner, Verzeichnisaufbau, Ordner templates, Override-Templates, Stylesheets, JavaScript, Bilder, Schriften, Ordnerstruktur, ausgelieferte Ordner, Rewrite-Regeln, htaccess, Aufbau eines Designs, design folder, directory layout, templates folder, override templates, fonts, folder structure, served folders, rewrite rules, design layout
type: help
---
# Der Aufbau des Design-Verzeichnisses

Diese Seite ist für Designer, die gleich Dateien in einem Design anlegen. Sie beantwortet: welche Ordner ein Design hat, wofür jeder
da ist, aus welchen ein Browser Dateien laden darf und wie echte Designs dieser Installation ihre Templates anordnen.

## Inhalt

1. [Die Ordner](#die-ordner)
2. [Ordner, die der Browser erreicht](#ordner-die-der-browser-erreicht)
3. [In templates/](#in-templates)
4. [In override/templates/](#in-overridetemplates)
5. [Zwei echte Beispiele](#zwei-echte-beispiele)
6. [Namensregeln](#namensregeln)

## Die Ordner

```
design/<name>/                      or  extension/<extension>/design/<name>/
├── templates/                       TPL templates found by path (pagelayout.tpl, node/view/full.tpl ...)
├── override/templates/              templates used only when an override.ini rule matches
├── stylesheets/                     CSS, loaded through ezjscore (ezcss_load) or ezdesign
├── javascript/                      JavaScript, loaded through ezjscore (ezscript_load)
├── images/                          images of the design (ezimage)
└── fonts/                           web fonts referenced from your CSS
```

Keiner der Ordner ist Pflicht. Ein Design, das nur Farben ändert, kann eine einzige Datei in `stylesheets/` enthalten; ein Design,
das das Markup von Artikeln ändert, kann nur `override/templates/full/article.tpl` enthalten.

## Ordner, die der Browser erreicht

Templates werden von PHP gelesen und nie an einen Browser gesendet. Assets holt der Browser direkt, daher muss der Webserver sie
durchlassen. Die mitgelieferten Rewrite-Regeln (`.htaccess_root` in der Wurzel der Installation und das Apache-Beispiel
`doc/eng-us/examples/ezpublish.conf`) liefern nur diese Ordner als statische Dateien aus:

| Ort | Direkt ausgelieferte Ordner |
|---|---|
| `design/<name>/` | `stylesheets`, `images`, `javascript`, `fonts` |
| `extension/<ext>/design/<name>/` | `stylesheets`, `flash`, `images`, `lib`, `javascript` / `javascripts`, `fonts`, `vendor`, `media` (die Liste in `.htaccess_root`) |

Jede andere Anfrage geht an `index.php`. Also:

- Legen Sie Bilder unter `images/` ab, nie unter `templates/`;
- legen Sie Schriften unter `fonts/` ab;
- eine Bibliothek eines Drittanbieters mit eigenen Dateien kann im Design einer Erweiterung unter `lib/` oder `vendor/` liegen.

> **Hinweis**
> Gepackte Stylesheets und Skripte werden aus `var/<site>/cache/public/` ausgeliefert, das die Regeln ebenfalls durchlassen; in der
> Produktion lädt der Browser also meist die Ausgabe des Packers, nicht Ihre Originaldateien. Siehe [Der Packer](../../../../eng-us/help/designers/theme-design/the-packer.md).

## In templates/

Der Template-Pfad ist das, wonach andere Templates und der Kernel fragen, daher ist er durch Konvention festgelegt. Die wichtigsten
Pfade:

| Pfad | Was es ist |
|---|---|
| `pagelayout.tpl` | der Rahmen jeder Seite ([Das Pagelayout](../../../../eng-us/help/designers/theme-design/the-pagelayout.md)) |
| `node/view/<viewmode>.tpl` | wie ein Knoten in einem View-Modus gezeigt wird: `full.tpl`, `line.tpl`, `embed.tpl`, `embed-inline.tpl` ... |
| `content/view/<viewmode>.tpl` | wie ein Inhaltsobjekt (ohne Knoten) gezeigt wird |
| `content/datatype/view/<datatype>.tpl` | wie ein Feld eines Datentyps gezeigt wird, z. B. `ezstring.tpl`, `ezimage.tpl` |
| `content/datatype/edit/<datatype>.tpl` | das Bearbeitungsformular eines Feldes |
| `content/datatype/view/ezxmltags/<tag>.tpl` | ein Tag des Rich Text (`paragraph.tpl`, `link.tpl`, `factbox.tpl` ...) |
| `user/login.tpl`, `user/register.tpl` ... | die Seiten des Moduls user |
| `error/kernel/<number>.tpl` | Fehlerseiten |
| `navigator/google.tpl` | Seitennavigation (Blättern) |
| `parts/...`, `pagelayout/...` | freie Namen für Ihre eigenen Teil-Templates |

Jeder Pfad in `design/standard/templates/` ist ein Template, nach dem der Kernel oder ein Modul fragen kann. Eines an denselben Pfad
in Ihr Design zu kopieren, ersetzt es für Siteaccesses, die Ihr Design verwenden.

## In override/templates/

Templates hier werden nicht über den Pfad gefunden. Sie werden von einer Regel in `override.ini` benannt (`MatchFile=`) und unter
`override/templates/` in jeder Design-Basis gesucht. Designs gruppieren sie meist nach View-Modus:

```
override/templates/
├── full/article.tpl        MatchFile=full/article.tpl     Match[class_identifier]=article
├── line/article.tpl        MatchFile=line/article.tpl
└── embed/image.tpl         MatchFile=embed/image.tpl
```

Das Design media hat hier `content`, `edit`, `embed`, `full`, `line`, `node`, `standard` und `view`. Siehe
[Grundlagen der Overrides](../templates/override-grundlagen.md).

## Zwei echte Beispiele

**Das Design media** (`extension/sevenx_themes_media/design/media/templates/`) teilt seinen Rahmen in Teile:

```
pagelayout.tpl
pagelayout/variables.tpl
pagelayout/head/...            (meta, style.tpl, script.tpl)
pagelayout/header.tpl, header/...
pagelayout/breadcrumbs.tpl
pagelayout/footer.tpl
pagelayout/accessibility_links.tpl
pagelayout/cookie_control.tpl
pagelayout/script_bottom.tpl
explayouts/...                 (its versions of the layouts block templates)
parts/...                      (Open Graph, consent, maps ...)
```

**Das Design starter** (`extension/cjw_themes_jumper/design/starter/`) folgt derselben Idee mit `pagelayout/head/` für `meta.tpl`,
`opengraph.tpl`, `link.tpl`, `style.tpl`, `script.tpl` und hält seine Schriften in `fonts/`, seine Favicons in `images/favicon/` und
seine Skripte in `javascript/`.

Das Pagelayout in kleine eingebundene Dateien aufzuteilen, ist nicht Pflicht, macht aber jeden Teil leicht zu finden, in einem
darüberliegenden Design zu überschreiben und in den Kommentaren mit Template-Pfaden zu lesen.

## Namensregeln

- Datei- und Ordnernamen: Kleinbuchstaben, Wörter mit `_` oder `-` verbunden, `.tpl` für Templates.
- Ein Template für eine Inhaltsklasse verwendet den Bezeichner der Klasse: `full/ng_article.tpl` für die Klasse `ng_article`.
- Ihre eigenen Teile unter einem eigenen Ordner (`parts/`, `pagelayout/`) können nicht mit Pfaden des Kernels kollidieren.
- Legen Sie nie Geheimnisse, Passwörter oder Schlüssel in ein Design: Alles unter `stylesheets/`, `javascript/`, `images/` und
  `fonts/` ist öffentlich.

## Verwandte Seiten

- [Was ein Design ist](../../../../eng-us/help/designers/theme-design/what-a-design-is.md)
- [Die Design-Kaskade](../../../../eng-us/help/designers/theme-design/the-design-cascade.md)
- [Stylesheets laden](../../../../eng-us/help/designers/theme-design/loading-stylesheets.md)
- [Schriften in einem Design](schriften-in-einem-design.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
