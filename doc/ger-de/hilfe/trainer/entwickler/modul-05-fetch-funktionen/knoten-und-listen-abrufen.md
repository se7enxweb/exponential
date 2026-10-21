---
title: "Lektion 5.1: Knoten und Listen holen"
audience: trainer, developer
category: training
order: 51
summary: Die Teilnehmenden lesen in Templates Daten mit fetch, laden einen Knoten nach ID oder Pfad, listen die Unterelemente eines Knotens mit content list auf, zählen sie mit list_count und wissen, wo jede Fetch-Funktion und ihre Parameter definiert sind. 45 Minuten.
keywords: entwickler, fetch, fetch-funktion, content node, content list, list_count, parent_node_id, limit, function_definition.php, eZContentFunctionCollection, as_object, load_data_map, knoten holen, liste, developers course, fetching nodes, lists
type: help
---
# Lektion 5.1: Knoten und Listen holen

Mit `fetch()` fragt ein Template den Kernel nach Daten: nach einem Knoten, einer Liste von Unterelementen, dem
aktuellen Benutzer. Jedes Modul kann Fetch-Funktionen anbieten, und jede ist in seiner `function_definition.php`
beschrieben. Diese Lektion vermittelt die Inhalts-Fetches, die jedes Template verwendet, und zeigt den Teilnehmenden,
wo sie die Parameter jedes beliebigen Fetches nachlesen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 5: Fetch-Funktionen](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Template-Entwickler |
| Voraussetzungen | [Modul 2](../modul-02-die-template-sprache/README.md): der Template-Spielplatz |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- `fetch( '<module>', '<function>', hash( ... ) )` schreiben und das Ergebnis lesen;
- einen Knoten mit `fetch( 'content', 'node', hash( 'node_id', ... ) )` laden;
- Unterelemente mit `fetch( 'content', 'list', ... )` auflisten und mit `list_count` zählen;
- eine Fetch-Funktion und ihre Parameter in `kernel/<module>/function_definition.php` finden;
- erklären, warum ein Listen-Fetch ein `limit` braucht.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: `kernel/content/function_definition.php` und
[Templates und Design, Teil 6](../../../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten).

1. **Die Form** (3 Minuten). `fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 5 ) )`:
   Modul, Funktion, benannte Parameter. Das Ergebnis ist das, was die Funktion zurückgibt, hier ein Array von Knoten.
2. **Wo Fetches definiert sind** (4 Minuten). Öffnen Sie `kernel/content/function_definition.php`. Jedes
   `$FunctionList['<name>']` nennt die PHP-Methode (`eZContentFunctionCollection::fetchContentNode` für `node`) und
   ihre Parameter mit Typ, Standardwert und der Angabe, ob sie erforderlich sind. Listen Sie die Inhaltsfunktionen
   auf: `grep -o "FunctionList\['[a-z_]*'\]" kernel/content/function_definition.php`.
3. **`node`** (2 Minuten). Die Parameter `node_id`, `node_path`, `language_code` und `remote_id`.
4. **`list` und `list_count`** (4 Minuten). `list` nimmt `parent_node_id`, `limit`, `offset`, `sort_by`,
   `class_filter_type`, `class_filter_array`, `attribute_filter`, `depth`, `language`, `load_data_map` und weitere
   an; `list_count` nimmt dieselben Filter an und gibt eine Zahl zurück. Holen Sie immer mit einem `limit` und zählen
   Sie für die Seitennavigation mit `list_count`.
5. **Berechtigungen** (2 Minuten). Inhalts-Fetches geben nur zurück, was der aktuelle Benutzer lesen darf. Ein
   anonymer Besucher sieht weniger Elemente als ein Redakteur.

### Häufige Fragen

**`fetch` oder `$node.children`?** `$node.children` lädt alle Unterelemente ohne Begrenzung und ohne Kontrolle über
die Sortierung; `fetch` mit einem `limit` lädt eine Seite.

**Warum gibt mein Fetch in einem Skript nichts zurück?** Skripte laufen als anonymer Benutzer, sofern sie keinen
Benutzer anmelden; Fetches beachten Berechtigungen.

**Kann ich nach Pfad holen?** `fetch( 'content', 'node', hash( 'node_path', 'about-us' ) )` findet einen Knoten über
seinen URL-Alias.

### Stolperfallen

- **Eine Liste ohne `limit`.** Bei einem Ordner mit Tausenden von Elementen wird die Seite langsam und braucht viel
  Speicher.
- **Fetches in einer Schleife.** Ein `fetch` in einem `foreach` läuft einmal pro Element; holen Sie nach Möglichkeit
  einmal vor der Schleife.
- **Parameternamen.** Ein falsch geschriebener Parameter wird stillschweigend ignoriert; vergleichen Sie mit
  `function_definition.php`.

## Praktische Übung

Verwenden Sie den Template-Spielplatz (`extension/mysite_hello/scripts/play.tpl`) und führen Sie ihn nach jedem
Schritt aus.

1. Laden Sie Knoten 2 mit einem Fetch statt über die Variable:

   ```
   {def $top = fetch( 'content', 'node', hash( 'node_id', 2 ) )}
   [{$top.name|wash}]
   ```

2. Listen Sie seine fünf neuesten Unterelemente auf:

   ```
   {def $items = fetch( 'content', 'list', hash( 'parent_node_id', 2,
                                                  'limit', 5,
                                                  'sort_by', array( 'published', false() ) ) )}
   {foreach $items as $item}{$item.name|wash} ({$item.class_identifier}){delimiter}, {/delimiter}{/foreach}
   ```

3. Zählen Sie alle Unterelemente: `{fetch( 'content', 'list_count', hash( 'parent_node_id', 2 ) )}`.
4. Listen Sie die Inhalts-Fetch-Funktionen in einer Shell auf:
   `grep -o "FunctionList\['[a-z_]*'\]" kernel/content/function_definition.php`.
5. Lesen Sie die Parameter von `list` in `kernel/content/function_definition.php` und suchen Sie `class_filter_array`.
6. Laden Sie Ihren Kursordner über seine Knoten-ID und listen Sie seine Unterelemente mit einem `limit` von 3 auf.
7. Vergleichen Sie mit der Administration: dieselben Elemente, in der Reihenfolge, die Sie gewählt haben.
8. Geben Sie die Variablen mit `{undef}` frei und committen Sie das Template.

## Erwartetes Ergebnis

- Schritt 1 gibt den Namen von Knoten 2 aus.
- Schritt 2 gibt bis zu fünf Namen mit ihren Klassenbezeichnern aus, die neuesten zuerst.
- Schritt 3 gibt eine Zahl aus, die mindestens so groß ist wie die Liste aus Schritt 2.
- Schritt 6 listet die eigenen Elemente der Teilnehmenden aus den früheren Modulen auf.
- Der Trainer lässt alle Teilnehmenden die PHP-Methode hinter `fetch( 'content', 'node', ... )` nennen.

## Quiz

1. Aus welchen drei Teilen besteht ein Fetch-Aufruf?
2. Welcher Fetch gibt die Anzahl der Unterelemente zurück, ohne sie zu laden?
   A. `list` B. `list_count` C. `tree` D. `node`
3. Wo sind die Parameter der Inhalts-Fetch-Funktionen definiert?
4. Warum sieht ein anonymer Besucher in einem Fetch weniger Elemente als ein Redakteur?
5. Nennen Sie zwei Parameter von `fetch( 'content', 'node', ... )`.

<details>
<summary>Antworten</summary>

1. Das Modul, der Funktionsname und ein Hash mit benannten Parametern.
2. **B.** `list_count`.
3. In `kernel/content/function_definition.php`.
4. Inhalts-Fetches geben nur zurück, was der aktuelle Benutzer lesen darf.
5. Zwei von `node_id`, `node_path`, `language_code`, `remote_id`.

</details>

## Weiterführende Literatur

- [Templates und Design](../../../../anleitungen/templates-und-design.md): die Sprache und ein vollständiges Listen-Template.
- [Das Inhaltsmodell und das Bearbeiten von Inhalten, Teil 9](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#standard-klassenfilter-für-fetches-template-entwickler):
  der Standard-Klassenfilter für Fetches.
- [Zugriffs- und Cache-Filter](../../../../../eng-us/features/6.0/access-and-cache-filters.md): wie Erweiterungen an
  Zugriffsprüfungen und am View-Caching teilnehmen können.

## Verwandte Seiten

- [Modul 5: Fetch-Funktionen](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
