---
title: Die Template-Sprache lernen
audience: student
category: learning
order: 22
summary: Ein Lernplan für die Template-Sprache von Exponential (eZ TPL) - was sie ist, ein erstes Template zum Abtippen, die fünf Konstrukte, die Sie zuerst lernen, eine Lesereihenfolge durch die Seiten zur Template-Sprache und für Designer, Übungen auf Ihrer Lernwebsite und die häufigsten Fehler von Lernenden.
keywords: template-sprache, eZ TPL, tpl, templates, def, foreach, if, wash, fetch, attribute_view_gui, Smarty, lernplan, übungen, lernende, template language, study plan, exercises, learner
type: help
---
# Die Template-Sprache lernen

Exponential gibt jede Seite, jede E-Mail und jedes PDF mit seiner eigenen Template-Sprache aus, bekannt als eZ TPL:
Tags in geschweiften Klammern wie bei Smarty, mit Operatoren und Funktionen für Inhaltsobjekte, einer Fetch-API zum
Lesen von Daten, einem Override-System zur Auswahl von Templates und einem Compiler, der Templates in PHP übersetzt
([Die Template-Sprache](../template-sprache/README.md)). Designer und Entwickler brauchen sie gleichermaßen. Diese
Seite gibt Ihnen einen Lernplan.

## Inhalt

1. [Die fünf Konstrukte, die Sie zuerst lernen](#die-fünf-konstrukte-die-sie-zuerst-lernen)
2. [Ihr erstes Template](#ihr-erstes-template)
3. [Eine Lesereihenfolge](#eine-lesereihenfolge)
4. [Übungen](#übungen)
5. [Fehler, die Lernende machen](#fehler-die-lernende-machen)
6. [Wenn Sie Smarty kennen](#wenn-sie-smarty-kennen)

## Die fünf Konstrukte, die Sie zuerst lernen

| Konstrukt | Beispiel | Bedeutung |
|---|---|---|
| Ausgabe mit Maskierung | `{$node.name\|wash}` | Einen Wert ausgeben; `wash` maskiert HTML. Verwenden Sie es für alles, was ein Benutzer eingegeben hat |
| Variablen | `{def $items = ...}`, `{set $items = ...}`, `{undef $items}` | Einmal deklarieren, später ändern, freigeben |
| Daten | `fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 5 ) )` | Inhalte lesen; `hash` baut benannte Werte |
| Schleifen und Bedingungen | `{foreach $items as $child}...{/foreach}`, `{if $node.depth\|gt( 2 )}...{/if}` | Operatoren werden als Pipes geschrieben: `gt`, `lt`, `eq`, `and`, `or`, `not` |
| Inhalte anzeigen | `{attribute_view_gui attribute=$node.data_map.title}`, `{node_view_gui content_node=$child view='line'}` | Jedes Attribut und jeden Knoten sein eigenes Template verwenden lassen |

Quelle: [Templates und Design, Teil 6](../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten).
Kommentare schreibt man `{* ... *}`. Links verwenden `ezurl`, in einem Attribut ohne Anführungszeichen geschrieben:
`<a href={$child.url_alias|ezurl}>`.

## Ihr erstes Template

Die Anleitung zu Templates nennt ein kleines Template zum Ausprobieren in einem Override (es listet die Unterknoten
des übergeordneten Knotens des aktuellen Elements auf, höchstens zehn):

```text
<h1>{$node.name|wash}</h1>
{def $siblings = fetch( 'content', 'list', hash( 'parent_node_id', $node.parent_node_id, 'limit', 10 ) )}
<ul>
{foreach $siblings as $sibling}
    <li><a href={$sibling.url_alias|ezurl}>{$sibling.name|wash}</a></li>
{/foreach}
</ul>
{undef $siblings}
```

Erwartet: die Überschrift, gefolgt von einer Liste von Links. Setzen Sie es in das Override aus
[Projekt 3](projekt-03-ein-template-override.md) und erklären Sie dann jede Zeile in Ihrem
[Lerntagebuch](ein-lerntagebuch-fuehren.md).

## Eine Lesereihenfolge

Beginnen Sie aufgabenorientiert und gehen Sie dann in die Tiefe:

1. [Überblick über Templates](../designer/vorlagen/templates-ueberblick.md) und
   [Grundlagen der TPL-Syntax](../designer/vorlagen/tpl-syntax-grundlagen.md), die behutsame Einführung.
2. [Templates und Design](../../anleitungen/templates-und-design.md), Teile 1 bis 6: wie ein Template gefunden wird,
   Overrides, Syntaxprüfungen.
3. [Was die Template-Sprache ist](../template-sprache/was-die-template-sprache-ist.md).
4. [Werte und Literale](../template-sprache/werte-und-literale.md) und
   [Variablen im Detail](../template-sprache/variablen-im-detail.md).
5. [Referenz der Kontrollstrukturen](../template-sprache/referenz-der-kontrollstrukturen.md).
6. [Das Objektmodell in Templates](../template-sprache/das-objektmodell-in-templates.md) und
   [Data Map und Inhalte](../template-sprache/data-map-und-inhalt.md).
7. [Fehler und Fehlersuche](../template-sprache/fehler-und-fehlersuche.md).
8. [Template-Ressourcen und Overrides](../template-sprache/template-ressourcen-und-overrides.md) und
   [Template-Caches](../template-sprache/template-caches.md).
9. Die Operator-Referenz nach Bedarf: [Template-Operatoren](../template-operatoren/README.md).

Der Kurs [Entwickler](../trainer/entwickler/README.md) der Trainer hat ein ganzes Modul zur Sprache,
[Modul 2](../trainer/entwickler/modul-02-die-template-sprache/README.md), mit Übungen und Quizzen.

## Übungen

Kurze Übungen, jeweils auf Ihrer Lernwebsite, in einem Override, das Sie danach entfernen können:

1. Geben Sie den Namen und die Knotennummer des aktuellen Elements aus.
2. Listen Sie die ersten fünf Unterknoten des Elements mit Links auf. Zeigen Sie dann „Keine Elemente“ an, wenn es
   keine gibt.
3. Zeigen Sie das Attribut title mit `attribute_view_gui` an, geben Sie dann `$node.data_map.title.content` aus und
   vergleichen Sie.
4. Verwenden Sie `{delimiter}`, um in einer Schleife Kommas zwischen die Namen zu setzen.
5. Bauen Sie absichtlich einen Fehler in ein Template ein (einen unbekannten Operator), prüfen Sie es mit
   `php bin/php/eztemplatecheck.php` und lesen Sie dann, was die Engine protokolliert
   ([Fehler und Fehlersuche](../template-sprache/fehler-und-fehlersuche.md)).

## Fehler, die Lernende machen

| Fehler | Ergebnis | Lösung |
|---|---|---|
| `wash` vergessen | Text mit `<` oder Anführungszeichen kann die Seite zerstören oder eine Sicherheitslücke öffnen | `\|wash` auf jedem Wert aus Inhalten oder von Besuchern |
| Dieselbe Variable zweimal mit `{def}` | Eine Warnung | Einmal `{def}`, danach `{set}` |
| `design/standard/templates/` bearbeiten | Beim nächsten Upgrade verloren | Ein Override in Ihrem eigenen Design |
| Arithmetik wie `$a + 1` erwarten | Funktioniert nicht | Operatoren wie `sum`; siehe [die Unterschiede zu Smarty](../template-sprache/smarty-unterschiede-im-detail.md) |
| Ein neuer Template-Pfad, ohne Caches zu leeren | Das alte Template wird verwendet | Einmal den Override- und den Template-Cache leeren |

## Wenn Sie Smarty kennen

Lesen Sie [Smarty und die Template-Sprache im Vergleich](../template-sprache/smarty-nebeneinander.md) und
[die Unterschiede im Detail](../template-sprache/smarty-unterschiede-im-detail.md): keine Infix-Arithmetik und keine
Infix-Vergleiche, kein PHP in Templates, keine automatische Maskierung und Overrides statt Vererbung.

## Verwandte Seiten

- [Projekt 3: ein Template-Override](projekt-03-ein-template-override.md)
- [Lernpfad: Designer](lernpfad-designer.md)
- [Lernpfad: Entwickler](lernpfad-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
