---
title: Was die Template-Sprache ist
audience: developer, designer, app-creator
category: development
order: 10
summary: Die Template-Sprache von Exponential (eZ TPL, ezpl) auf einer Seite - eine Smarty-ähnliche Sprache für Dateien .tpl, wo sie genutzt wird (Designs, Modulansichten, Mail, PDF), die Teile der Engine in lib/eztemplate und was sie von Haus aus über Smarty hinaus kann.
keywords: Template-Sprache, eZ TPL, ezpl, tpl, Smarty, eZTemplate, lib/eztemplate, Parser, Compiler, Ressource design, Überblick, template language, parser, compiler, design resource, overview
type: help
---
# Was die Template-Sprache ist

Jede Seite, die Exponential rendert, jede Mail, die es aus einem Template sendet, und jeder PDF-Export ist in seiner eigenen Template-Sprache
geschrieben. Im Code ist es die Bibliothek `eZTemplate` in `lib/eztemplate/`, die Dateien enden auf `.tpl`, und man nennt sie **eZ TPL**
oder **ezpl**. Auf diesen Seiten heißt sie „die Template-Sprache“.

Sie sieht aus wie Smarty: Tags in geschweiften Klammern, Variablen mit `$`, Werte, die durch `|` geleitet werden. Darunter wurde sie für ein
Content-Management-System gebaut, sie hat also eine große Menge registrierter Operatoren und Funktionen, sie liest PHP-Objekte über ein
Attributprotokoll, sie holt Inhalte selbst mit `fetch()`, und sie wählt über das Override-System statt über Vererbung von Templates, welches
Template genutzt wird.

## Inhalt

1. [Ein erster Blick](#ein-erster-blick)
2. [Wo Templates genutzt werden](#wo-templates-genutzt-werden)
3. [Die Teile der Engine](#die-teile-der-engine)
4. [Über Smarty hinaus, von Haus aus](#über-smarty-hinaus-von-haus-aus)
5. [Wie die Beispiele in diesem Abschnitt entstanden sind](#wie-die-beispiele-in-diesem-abschnitt-entstanden-sind)
6. [Verwandte Seiten](#verwandte-seiten)

## Ein erster Blick

Ein Template mischt Text mit Tags. Dieses holt die Startseite „Fit & Healthy“ von alpha (Knoten 89), gibt ihren Namen für HTML maskiert aus
und führt ihre ersten vier Kinder auf:

```tpl
{def $site = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
<h2>{$site.name|wash}</h2>
<p>{$site.children_count} pages below, class {$site.class_identifier}</p>
<ul>
{foreach $site.children as $child max 4}
  <li>{$child.name|wash} ({$child.class_identifier})</li>
{/foreach}
</ul>
```

Ausgabe auf alpha:

```text
<h2>Fit &amp; Healthy</h2>
<p>16 pages below, class ng_frontpage</p>
<ul>  <li>Fitness (ng_landing_page)</li>  <li>Healthy eating (ng_landing_page)</li>  <li>Recipes (ng_category)</li>  <li>Running (ng_topic)</li></ul>
```

Vier Arten von Dingen kommen darin vor:

- **Funktionen** sind Tags mit einem Namen: `{def ...}`, `{foreach ...}{/foreach}`. Sie steuern das Template: Sie legen Variablen an,
  laufen in Schleifen, verzweigen, binden andere Templates ein.
- **Variablen** beginnen mit `$`: `$site`, `$child`.
- **Attribute** folgen auf einen Punkt: `$site.name`, `$child.class_identifier`. Der Wert kann ein Array oder ein PHP-Objekt sein.
- **Operatoren** berechnen einen Wert: `fetch( ... )`, `hash( ... )`, `|wash`. Sie werden entweder als Aufruf oder nach einer Pipe
  geschrieben.

## Wo Templates genutzt werden

| Wo | Wie ein Template erreicht wird | Beispiel im Code |
|---|---|---|
| Designs (die Website und die Administration) | URIs `design:`, aufgelöst über die Design-Basen und das Override-System | `kernel/common/eztemplatedesignresource.php` |
| Modulansichten | das PHP einer Ansicht setzt Variablen und holt ein Template; das Ergebnis wird im Pagelayout zu `$module_result.content` | `kernel/content/view.php` |
| Mail | dieselbe Engine rendert den Text der Mail | `kernel/user/ezuseroperationcollection.php` holt `design:user/registrationinfo.tpl` |
| Benachrichtigungen | Handler rendern ihre Zusammenfassungs- und Teilbaum-Mails | `kernel/classes/notification/handler/ezsubtree/ezsubtreehandler.php` |
| PDF-Export | der Operator `pdf` und die Funktionen `content_pdf_gui` und `attribute_pdf_gui` | `lib/ezpdf/classes/ezpdf.php`, `kernel/content/pdf.php` |

Dasselbe Engine-Objekt dient allen: `eZTemplate::factory()` (in `lib/eztemplate/classes/eztemplate.php`) erzeugt es einmal pro Anfrage, lädt
die Listen der Operatoren und Funktionen und registriert die Design-Ressource.

## Die Teile der Engine

| Teil | Datei | Was er tut |
|---|---|---|
| Die Engine | `lib/eztemplate/classes/eztemplate.php` | Variablen und Namensräume, Ressourcen, registrierte Operatoren und Funktionen, Verarbeitung |
| Parser | `lib/eztemplate/classes/eztemplatemultipassparser.php`, `eztemplateelementparser.php` | teilt den Text in Text, Tags und Kommentare, entfernt Leerraum um Tags, baut den Knotenbaum |
| Knotenbaum | `eztemplateroot.php`, `eztemplatetextelement.php`, `eztemplatevariableelement.php`, `eztemplatefunctionelement.php`, `eztemplateoperatorelement.php` | das gelesene Template im Speicher |
| Compiler | `lib/eztemplate/classes/eztemplatecompiler.php`, `eztemplateoptimizer.php`, `eztemplatenodetool.php` | wandelt den Baum einmal in PHP-Code um, sodass spätere Anfragen nur eine PHP-Datei einbinden |
| Ressourcen | `eztemplatefileresource.php`; `kernel/common/eztemplatedesignresource.php` | `file:` liest einen Pfad; `design:` durchsucht die Design-Basen und wendet Overrides an |
| Baum-Cache | `lib/eztemplate/classes/eztemplatetreecache.php` | behält gelesene Bäume ([TemplateSettings] NodeTreeCaching) |
| Operatoren | `lib/eztemplate/classes/eztemplate*operator.php`, `kernel/common/`, Erweiterungen | eine PHP-Klasse pro Familie, in einer `eztemplateautoload.php` aufgeführt |
| Funktionen | `lib/eztemplate/classes/eztemplate*function.php`, `kernel/common/ezobjectforwarder.php` | `if`, `foreach`, `def`, `include`, `cache-block`, die Funktionen `*_view_gui` |

Die Bibliothek hängt nicht vom Inhaltsmodell ab: Sie kennt nur Text, Werte und Objekte, die auf `attributes()`, `hasAttribute()` und
`attribute()` antworten. Der Kernel fügt die inhaltsbezogenen Teile hinzu: die Design-Ressource, die Funktionen `fetch` jedes Moduls und die
Ansichtsfunktionen wie `node_view_gui`.

## Über Smarty hinaus, von Haus aus

Was die Sprache ohne installierte Erweiterung bietet, mit der Seite, die es behandelt:

- **Mehrere hundert registrierte Operatoren.** Auf dem Siteaccess `site` von alpha registriert die Engine 395 Operatoren und 48 Funktionen
  (gezählt durch Laden jedes Eintrags der Listen von Operatoren und Funktionen). Die Template-Bibliothek allein bringt Arithmetik, Arrays,
  Zeichenketten, die Zeichenkettenfunktionen von PHP, Logik, Typen, Locale, Digests und Rollen mit.
- **Datenzugriff im Template.** `fetch( 'content', 'list', hash( ... ) )` liest Inhalte mit Filtern, Sortierung und Berechtigungen, und
  jedes Modul kann eigene Fetch-Funktionen anbieten.
- **Objekte mit Attributen.** Ein Knoten ist ein PHP-Objekt, und doch liest sich `$node.parent.name` oder `$node.data_map.title.content` wie
  ein Array. Siehe [Zugriff auf Attribute](zugriff-auf-attribute.md).
- **Das Override-System** wählt ein Template nach Klasse, Sektion, Knoten, Ansichtsmodus und mehr, statt dass Templates einander erweitern
  ([Grundlagen der Overrides](../../../eng-us/help/designers/templates/override-ini-basics.md)).
- **Namensräume** halten die Variablen eingebundener Templates auseinander ([Namensräume](namensraeume.md)).
- **Ein Compiler**, der für jedes Template PHP schreibt, und **Cache-Blöcke**, die fertiges HTML speichern
  ([Kompilierung und Cache von Templates](../../../eng-us/help/designers/templates/template-compilation-and-cache.md)).
- **Übersetzung und Lokalisierung** eingebaut: `i18n`, `l10n`, `datetime`.

Ein zweites Beispiel nutzt mehrere davon an einem Artikel (Knoten 90, „Privacy Policy“):

```tpl
{def $article = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
Fields: {$article.data_map|count}
Title field: {$article.data_map.title.content|wash}
Parent: {$article.parent.name|wash}
Path: {foreach $article.path as $step}{$step.name|wash} / {/foreach}{$article.name|wash}
URL: {$article.url_alias|ezurl( 'no', 'relative' )}
Can the visitor read it: {if $article.can_read}yes{else}no{/if}
```

Ausgabe auf alpha:

```text
Fields: 18
Title field: Privacy Policy
Parent: Fit &amp; Healthy
Path: Websites / Fit &amp; Healthy / Privacy Policy
URL: /privacy-policy
Can the visitor read it: yes
```

## Wie die Beispiele in diesem Abschnitt entstanden sind

Jedes Beispiel auf den Seiten zur Template-Sprache wurde auf alpha, der Referenzinstallation, von der echten Engine gegen echte Inhalte
gerendert, und die Ausgabe darunter ist das, was die Engine ausgegeben hat. Die Beispiele liegen außerhalb des Designbaums und werden mit der
Ressource `file:` gelesen; Kompilierung und Template-Caching waren für diese Läufe ausgeschaltet, und jedes Beispiel wurde ein zweites Mal
durch den Compiler gerendert, um zu prüfen, dass beide dieselbe Ausgabe ergeben. Wo ein Beispiel absichtlich einen Fehler zeigt, zeigt die
Seite auch, was die Engine protokolliert hat.

## Verwandte Seiten

- Weiter: [Geschichte der Template-Sprache](geschichte-der-template-sprache.md)
- [Überblick über Templates](../../../eng-us/help/designers/templates/templates-overview.md) für Designer
- [Grundlagen der TPL-Syntax](../../../eng-us/help/designers/templates/tpl-syntax-basics.md)
- [Referenz der Operatoren](../../../eng-us/help/designers/templates/operator-reference.md) und [Referenz der Funktionen](../../../eng-us/help/designers/templates/function-reference.md)
- [Jeder Template-Operator](../../../eng-us/examples/template-operators/README.md) und [jede Template-Funktion](../../../eng-us/examples/template-functions/README.md), aus dem Code erzeugt
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
