---
title: "Template-Overrides: die Grundlagen"
audience: developer, designer, app-creator
category: extension-development
order: 100
summary: Ein Override ersetzt ein Template nur für bestimmte Inhalte - Artikel in der Vollansicht, die Startseite, eine Sektion. Der Block in override.ini, wohin die Datei gehört, wie Sie prüfen, dass er greift, und die Fehler, die ihn aufhalten.
keywords: Override, Template-Override, Vollansicht, Listenansicht, Design-Erweiterung, Priorität, override.ini, Source, MatchFile, Subdir, Match, class_identifier, full view, line view, template override, design extension
type: help
---
# Template-Overrides: die Grundlagen

Teil 2, Kapitel 10. Die Design-Kette ersetzt ein Template für **alle** Inhalte. Ein **Override** ersetzt es für
**bestimmte** Inhalte: Artikel in der Vollansicht, die Startseite, alles in einer Sektion. Mit Overrides geben echte
Websites jeder Inhaltsart ihr eigenes Aussehen, und sie sind das wichtigste Werkzeug des Builders.

## Ein Override in vier Zeilen

In der `override.ini.append.php` Ihres Siteaccess oder Ihrer Website-Erweiterung:

```ini
[full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
```

| Zeile | Bedeutung |
|---|---|
| `[full_article]` | Ein eindeutiger Name für die Regel. Beliebig lesbar; `<viewmode>_<class>` ist die Konvention. |
| `Source` | Das ersetzte Template, so wie der Kernel es anfordert (`node/view/full.tpl` für die Vollansicht eines Knotens). |
| `MatchFile` | Ihr Template, relativ zu `<design>/override/templates/`. |
| `Subdir` | Immer `templates`. |
| `Match[<key>]` | Die Bedingung. Mehrere `Match`-Zeilen müssen alle zutreffen. |

Diese Regel bedeutet: „Wenn die Vollansicht eines Knotens gezeichnet wird und der Knoten ein `article` ist, verwende
stattdessen `full/article.tpl`.“ Es ist eine echte Regel; das Media-Theme liefert genau diesen Block in seinen
Siteaccess-Einstellungen aus.

## Wohin die Template-Datei gehört

`MatchFile` wird über die Design-Kette im Ordner `override/templates/` jeder Design-Basis gesucht:

```text
extension/mysite_design/design/mysite/override/templates/full/article.tpl
```

Die Override-Einstellung kann überall liegen, wo sie für den Siteaccess gelesen wird; die Template-Datei muss in einem
Design der Kette dieses Siteaccess liegen. Die meisten Websites halten beides in einer Design-Erweiterung.

## Ein erster Override, Schritt für Schritt

1. Finden Sie das zu ersetzende Template: Öffnen Sie mit `ShowUsedTemplates=enabled` einen Artikel und lesen Sie die
   Template-Tabelle in der Debug-Ausgabe. Das **angeforderte** Template ist Ihre `Source`.
2. Kopieren Sie das Standard-Template als Ausgangspunkt:

   ```bash
   mkdir -p extension/mysite_design/design/mysite/override/templates/full
   cp design/standard/templates/node/view/full.tpl extension/mysite_design/design/mysite/override/templates/full/article.tpl
   ```

3. Fügen Sie die Regel in `extension/mysite_design/settings/siteaccess/mysite/override.ini.append.php` (oder in die
   `override.ini.append.php` Ihres Siteaccess) ein.
4. Leeren Sie die Caches, die die Regeln und die kompilierten Templates enthalten:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-id=template-override,template
   ```

5. Öffnen Sie einen Artikel. Die Template-Tabelle zeigt jetzt `full/article.tpl` als **verwendetes** Template für
   `node/view/full.tpl` und nennt die Regel `full_article`.
6. Ändern Sie die Kopie. Fangen Sie klein an: Setzen Sie den Titel in eine Klasse Ihres Designs.

## Was ein Template für die Vollansicht erhält

In `node/view/full.tpl` und seinen Overrides ist die Variable `$node` der angezeigte Knoten. Der übliche Anfang:

```text
<article class="article-full">
    <h1>{$node.name|wash}</h1>
    {attribute_view_gui attribute=$node.data_map.intro}
    {attribute_view_gui attribute=$node.data_map.body}
</article>
```

`$node.data_map` enthält die Felder (Attribute) nach Bezeichner; `attribute_view_gui` zeichnet ein Feld mit dem
eigenen Ansichts-Template seines Datentyps, sodass Rich Text, Bilder und Datumsangaben richtig dargestellt werden. Die
Attributbezeichner Ihrer Klasse finden Sie unter **Setup > Klassen**.

## Reihenfolge der Regeln

Treffen mehrere Regeln zu, gewinnt die, die **zuerst** probiert wird. Eine Regel kann eine Zeile `Priority=<number>`
haben: Die Template-Engine probiert die Regeln vom niedrigsten `Priority`-Wert an aufwärts, und Regeln ohne `Priority`
kommen zuletzt (`overrideArray()` in `kernel/common/eztemplatedesignresource.php`). Geben Sie spezifischen Regeln (ein
Knoten) eine niedrigere Zahl als allgemeinen (eine Klasse):

```ini
[full_object_1234]
Source=node/view/full.tpl
MatchFile=full/object_1234.tpl
Subdir=templates
Priority=0
Match[object]=1234
```

**Design > Template editor** (`/visual/templatelist`) zeigt die Regeln in der Reihenfolge, in der sie probiert werden,
und lässt Sie sie per Drag-and-drop umordnen; siehe
[Template-Editor: Overrides anlegen, ordnen und bearbeiten](../../../../eng-us/features/6.0/template-editor-overrides.md).

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| Der Override wird nie verwendet | `Source` entspricht nicht genau dem angeforderten Template (prüfen Sie die Debug-Tabelle); die Datei liegt nicht in `override/templates/` eines Designs der Kette; der INI-Cache wurde nicht geleert |
| Ein leerer Inhaltsbereich | eine neue Template-Datei und ein alter Template-Override-Cache: Leeren Sie `template-override` |
| Er greift bei den falschen Inhalten | der `Match`-Schlüssel oder -Wert: `class_identifier` nimmt den Bezeichner (`article`), `class` die numerische ID |
| Funktioniert in einem Siteaccess, im anderen nicht | die Regel steht nur in den Einstellungen eines Siteaccess, oder der andere Siteaccess verwendet ein anderes Design |
| Zwei Regeln kommen sich in die Quere | Reihenfolge: Geben Sie der spezifischen Regel eine niedrigere `Priority` |

## Verwandte Seiten

- Zurück: [Die Design-Kette: wo ein Template gefunden wird](die-design-kette.md)
- Weiter: [Override-Bedingungen](override-bedingungen.md)
- [Template-Editor: Overrides anlegen, ordnen und bearbeiten](../../../../eng-us/features/6.0/template-editor-overrides.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
