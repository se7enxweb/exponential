---
title: "Lektion 2.3: Overrides mit override.ini"
audience: trainer, designer, developer
category: training
order: 23
summary: Die Teilnehmenden lesen eine Override-Regel (Source, MatchFile, Subdir, Match), sehen im Seitenquelltext, wie der Artikel-Override des Assistenten antwortet, schreiben einen zweiten Override für die Zeilenansicht von Artikeln und finden beide im Template-Editor. 60 Minuten.
keywords: theme-design, override, override.ini, Source, MatchFile, Subdir, Match, class_identifier, node, section, viewmode, Priority, template-override-cache, template-editor, templateview, theme design, template-override cache, Template Editor
type: help
---
# Lektion 2.3: Overrides mit override.ini

Eine Datei in `templates/` ersetzt ein Template für jede Seite. Ein **Override** ersetzt es nur dort, wo eine Regel
passt: eine Klasse, ein Knoten, eine Sektion. Mit Overrides gibt ein Theme Artikeln, Ordnern und der Startseite ihr
eigenes Aussehen, ohne irgendetwas anderes anzurühren. In dieser Lektion lesen die Teilnehmenden den Beispiel-Override,
den der Assistent geschrieben hat, schreiben einen eigenen und finden beide in der Administration.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 2: Templates, Overrides und das Pagelayout](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Frontend-Entwickler |
| Voraussetzungen | [Lektion 2.2](das-pagelayout.md); veröffentlichte Artikel in einem Ordner der Schulungs-Website |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Override-Regel mit `Source`, `MatchFile`, `Subdir` und `Match[...]` lesen und schreiben;
- das Override-Template in den richtigen Ordner ihres Designs legen;
- die Caches leeren, die ein neuer Template-Pfad verlangt;
- mit Template-Pfadkommentaren belegen, welcher Override geantwortet hat;
- die Overrides eines Templates im **Template-Editor** finden.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Templates und Design, Teil 4](../../../../anleitungen/templates-und-design.md#4-eine-änderung-mit-einem-override-vornehmen),
[Template-Overrides für Website-Bauer](../../../../../eng-us/help/creators/website-creators/template-overrides.md) und
[Template-Editor](../../../../../eng-us/features/6.0/template-editor-overrides.md).

1. **Die Regel** (6 Minuten). Öffnen Sie `extension/training_theme/settings/override.ini.append.php`:

   ```ini
   [full_article_training_theme]
   Source=node/view/full.tpl
   MatchFile=full/article.tpl
   Subdir=templates
   Match[class_identifier]=article
   ```

   `Source` ist das ersetzte Template, `MatchFile` der Ersatz relativ zu `override/templates/`, `Subdir` ist immer
   `templates`, und jede Zeile `Match[...]` muss zutreffen (`class_identifier`, `node`, `object`, `section`,
   `parent_node`, `viewmode` und weitere). Der Name des Blocks muss nur eindeutig sein.
2. **Reihenfolge** (3 Minuten). Regeln mit einer `Priority` kommen zuerst, die niedrigste Zahl zuerst; Regeln ohne
   Priorität folgen. Die erste Regel, deren Bedingungen alle zutreffen, gewinnt.
3. **Die Caches** (3 Minuten). Ein neuer Template-Pfad verlangt einmal das Leeren des Caches `template-override` und
   des Template-Caches, dazu den View-Cache der Inhalte, damit zwischengespeicherte Seiten neu gezeichnet werden.
   Spätere Änderungen derselben Datei brauchen kein Leeren, solange der Template-Cache ausgeschaltet ist.
4. **Der Template-Editor** (5 Minuten). Öffnen Sie in der Administration den **Template-Editor** (Menü Design,
   `/visual/templatelist`) und das Template `node/view/full.tpl`. Die Seite „Overrides für das Template ... im
   Siteaccess ...“ listet die Overrides in der Reihenfolge, in der sie versucht werden, mit ihren Bedingungen, Pfeilen
   zum Umordnen, **Bearbeiten**, **Neues Überschreib-Template**, **Bedingungen speichern** und **Ausgewählte
   entfernen**. Zeigen Sie es; ändern Sie auf einer gemeinsamen Website nichts.
5. **Wohin Overrides gehören** (3 Minuten). In einem Theme: die Regel in die `settings/override.ini.append.php` der
   Erweiterung und die Datei in das Design der Erweiterung. Regeln, die der Template-Editor schreibt, landen in
   `settings/siteaccess/<site>/override.ini.append.php` der Installation; ein Theme-Autor verschiebt sie in die
   Erweiterung.

### Häufige Fragen

**Was ist der Unterschied zwischen `node` und `object` in `Match`?** `node` ist eine Stelle im Baum, `object` das
Inhaltselement an all seinen Stellen.

**Kann ein Override mehrere Klassen bedienen?** Nein; ein `Match[class_identifier]` pro Regel. Schreiben Sie eine Regel
pro Klasse, alle mit derselben `MatchFile`, wenn das Aussehen gleich ist.

**Mein Override wird ignoriert.** Prüfen Sie `Source` (den genauen Template-Namen), `MatchFile` (relativ zu
`override/templates/`) und die Bedingungen und leeren Sie dann `template-override` und `template`.
`php bin/php/console exp:ini where override/<block>/MatchFile <site>` listet die Dateien auf, die den Block definieren.

### Stolperfallen

- **Die Datei in `templates/` statt in `override/templates/`.** Die Regel findet sie nie.
- **Ein Tippfehler im Klassenbezeichner.** Die Regel passt stillschweigend nie; lesen Sie den Bezeichner unter
  **Setup > Klassen** oder in der Zeile der Klasse in der Inhaltsansicht ab.
- **Auf einer zwischengespeicherten Seite testen.** Leeren Sie nach einem neuen Override den View-Cache der Inhalte,
  sonst wird die alte Seite ausgeliefert.

## Praktische Übung

Auf Ihrer Schulungsinstallation, mit eingeschalteten Template-Pfadkommentaren für `<site>`. Der Design-Ordner ist
`extension/training_theme/design/training_theme/`.

1. Lesen Sie `extension/training_theme/settings/override.ini.append.php` und das Template, das sie nennt,
   `override/templates/full/article.tpl`.
2. Öffnen Sie einen Artikel auf `<site>`. Suchen Sie im Seitenquelltext den Kommentar
   `<!-- START .../full/article.tpl (node/view/full.tpl) -->`.
3. Öffnen Sie einen Ordner, der Artikel auflistet. Suchen Sie den Kommentar `START` des Templates, das jeden Artikel
   in der Liste zeichnet (`node/view/line.tpl`, aus dem Standard-Design).
4. Legen Sie das Template für den Zeilen-Override an:

   ```bash
   mkdir -p extension/training_theme/design/training_theme/override/templates/line
   ```

   und speichern Sie es als `.../override/templates/line/article.tpl`:

   ```
   <div class="training_theme-line">
       <h2><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></h2>
       {if $node.data_map.intro.has_content}
           {attribute_view_gui attribute=$node.data_map.intro}
       {/if}
   </div>
   ```

5. Fügen Sie die Regel in `extension/training_theme/settings/override.ini.append.php` vor dem schließenden `*/ ?>` ein:

   ```ini
   [line_article_training_theme]
   Source=node/view/line.tpl
   MatchFile=line/article.tpl
   Subdir=templates
   Match[class_identifier]=article
   ```

6. Prüfen Sie das Template: `php bin/php/eztemplatecheck.php -s<site> extension/training_theme/design/training_theme/override/templates/line/article.tpl`.
7. Leeren Sie die Caches, die ein neuer Template-Pfad verlangt:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-id=template-override
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-tag=content
   ```

8. Laden Sie den Ordner neu. Suchen Sie `<!-- START .../line/article.tpl (node/view/line.tpl) -->` im Quelltext.
9. Öffnen Sie in der Administration den **Template-Editor**, filtern Sie nach `node/view/line.tpl`, öffnen Sie es,
   wählen Sie unter **Siteaccess** Ihren Siteaccess und suchen Sie Ihren Override in der Liste. Klicken Sie nicht auf
   **Bedingungen speichern** oder **Ausgewählte entfernen**.

## Erwartetes Ergebnis

- Schritt 2 zeigt, wie der Override des Assistenten für Artikel auf `node/view/full.tpl` antwortet.
- Schritt 8: Der Ordner listet jeden Artikel mit verlinktem Namen und Intro auf, und der Kommentar nennt das
  `line/article.tpl` der Teilnehmenden mit `(node/view/line.tpl)` in Klammern. Andere Klassen im Ordner verwenden
  weiter das Standard-Zeilentemplate.
- Schritt 9: Der Override erscheint in der Liste für den Siteaccess der Teilnehmenden mit der Bedingung
  `class_identifier`.
- Der Trainer prüft die Ordnerseite einer Person und ihren Quelltext am Projektor.

## Quiz

1. Was ist in einer Override-Regel `Source`, und relativ wozu ist `MatchFile`?
2. Eine Regel hat `Match[class_identifier]=article` und `Match[section]=1`. Wann gilt sie?
   A. Für jeden Artikel B. Für jedes Element in Sektion 1 C. Für Artikel in Sektion 1 D. Nie
3. Welche Caches müssen für einen neuen Override-Pfad geleert werden?
4. Zwei Regeln passen auf dieselbe Seite; keine hat eine `Priority`. Wie stellen Sie fest, welche verwendet wird, und
   wie ändern Sie das?
5. Wo bewahrt ein Theme seine Override-Regeln auf?

<details>
<summary>Antworten</summary>

1. `Source` ist das ersetzte Template; `MatchFile` ist relativ zu `override/templates/` des Designs
   ([Templates und Design, Teil 4](../../../../anleitungen/templates-und-design.md#4-eine-änderung-mit-einem-override-vornehmen)).
2. **C.** Alle Zeilen `Match` müssen zutreffen.
3. `template-override` und der Template-Cache, dazu der View-Cache der Inhalte, damit zwischengespeicherte Seiten neu
   gezeichnet werden (und der INI-Cache, wenn die Regel neu ist).
4. Die zuerst versuchte Regel gewinnt; prüfen Sie die Reihenfolge im **Template-Editor** und geben Sie den Regeln eine
   `Priority` (die niedrigste zuerst) oder ordnen Sie sie dort um
   ([Template-Editor](../../../../../eng-us/features/6.0/template-editor-overrides.md)).
5. In der `settings/override.ini.append.php` der Erweiterung, mit den Templates in `override/templates/` ihres
   Designs.

</details>

## Weiterführende Literatur

- [Template-Overrides für Website-Bauer](../../../../../eng-us/help/creators/website-creators/template-overrides.md): Bedingungen und
  gängige Regeln.
- [Template-Editor](../../../../../eng-us/features/6.0/template-editor-overrides.md): der Klickpfad, das Umordnen, Sicherungen.
- [Reihenfolge der Template-Overrides](../../../../../eng-us/features/6.0/template-override-ordering.md): wie die Reihenfolge
  entschieden wird.
- [Overrides mit dem Standard vergleichen](../../../../../eng-us/help/designers/lifecycle/comparing-overrides-with-the-standard.md):
  Overrides nach einem Upgrade aktuell halten.

## Verwandte Seiten

- [Modul 2: Templates, Overrides und das Pagelayout](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
