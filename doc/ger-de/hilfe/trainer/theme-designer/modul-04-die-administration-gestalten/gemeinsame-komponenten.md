---
title: "Lektion 4.2: Mit den gemeinsamen Komponenten bauen"
audience: trainer, designer, developer
category: training
order: 42
summary: Die Teilnehmenden finden die gemeinsamen Komponenten .a4-* in admin4 und fügen ihrer Erweiterung einen Dashboard-Block hinzu, dessen Template eine Tabelle, Badges, einen Leerzustand und eine Schaltfläche verwendet, ohne eine Zeile eigenes CSS. 60 Minuten.
keywords: theme-design, admin4, components.css, a4-card, a4-table, a4-badge, a4-btn, a4-empty, a4-notice, a4-sr-only, a4-actions, dashboard-block, dashboard.ini, DashboardBlocks, administrationsdesign einer erweiterung, theme design, dashboard block, extension admin design
type: help
---
# Lektion 4.2: Mit den gemeinsamen Komponenten bauen

Regel 2 sagt: Verwenden Sie zuerst die gemeinsamen Komponenten. Die `components.css` von admin4 hat bereits Karten,
Tabellen, Badges, Schaltflächen, Hinweise, Leerzustände, Werkzeugleisten und Formularfelder, im hellen und im dunklen
Modus, mit Fokusringen und Unterstützung für erzwungene Farben. In dieser Lektion fügen die Teilnehmenden aus ihrer
eigenen Erweiterung dem Dashboard der Administration einen Block hinzu und bauen ihn vollständig aus diesen Klassen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 4: Die Administration gestalten](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Entwickler, die Administrationsseiten bauen |
| Voraussetzungen | [Lektion 4.1](die-gestaltungsregeln-der-administration.md); `training_theme` aktiv; die Administration in `admin4` oder `admin4l` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Komponente und ihre Zustände in der Komponententabelle und in `components.css` finden;
- die Komponenten auf einer admin4-Seite mit dem Inspektor des Browsers erkennen;
- aus einer Erweiterung einen Dashboard-Block hinzufügen (`dashboard.ini`, ein Template im `design/admin4/` der
  Erweiterung);
- Administrations-Markup mit `.a4-table`, `.a4-badge`, `.a4-empty`, `.a4-actions` und `.a4-btn` schreiben, ohne eigenes
  CSS.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Die Administration gestalten, Komponenten](../../../../../eng-us/bc/6.0/admin4-styling.md#components).

1. **Die Komponententabelle** (6 Minuten). Gehen Sie die Gruppen durch: Seitenbereich (`.a4-page`), Hilfsklassen
   (`.a4-sr-only`, `.a4-muted`, `.a4-meta`), Karten (`.a4-card`, `.a4-card-head`, `.a4-card-body`, `.a4-card-foot`),
   Tags und Badges (`.a4-badge` mit `.is-ok`, `.is-warn`, `.is-bad`, `.is-info`, `.is-accent`), Schaltflächen
   (`.a4-btn`, `.a4-btn-primary`, `.a4-btn-danger`, `.a4-btn-ghost`, Größen), Werkzeugleisten, Tabellen
   (`.a4-table-wrap` um `table.a4-table`), der Leerzustand `.a4-empty`, Hinweise `.a4-notice`.
2. **Zustände sind Klassen** (3 Minuten). Eine Komponente ist eine Klasse plus höchstens ein Zustand (`.is-*`) oder ein
   Element, sodass eine Modulregel mit einer Klasse mehr immer gewinnt.
3. **Auf einer echten Seite** (5 Minuten). Öffnen Sie das Dashboard von admin4 mit dem Inspektor; finden Sie eine
   Karte, eine Schaltfläche und eine Tabelle und ihre Klassen. Zeigen Sie, dass die Blöcke des Dashboards aus
   `dashboard.ini [DashboardSettings] DashboardBlocks[]` stammen und jeder von `dashboard/<identifier>.tpl` innerhalb
   einer Karte gezeichnet wird, die das Dashboard bereitstellt.
4. **Eine Erweiterung fügt einen Block hinzu** (6 Minuten). Zeigen Sie die zwei Dateien der Übung. Das Template gehört
   in `design/admin4/templates/dashboard/` der Erweiterung: Die Erweiterung registriert ihre Design-Ordner mit
   `DesignExtensions[]`, und `admin4` ist in der Kette der Administration.

### Häufige Fragen

**Was, wenn keine Komponente passt?** Prüfen Sie zuerst noch einmal (die Tabelle ist lang). Dann gehört das CSS in das
eigene Stylesheet der Erweiterung, mit eigenem Präfix und nur mit Tokens (Lektion 4.4); eine Komponente, die allen
fehlt, wird zentral vom Kern ergänzt, nie in ein Modul kopiert.

**Brauche ich `.a4-page` um meinen Block?** Modulseiten sind in `.a4-page` eingehüllt. Ein Dashboard-Block sitzt im
eigenen Markup des Dashboards, dort ist es also nicht nötig.

**Warum `dir="auto"` am Namenslink?** Der Name eines Elements kann in einer anderen Schreibrichtung stehen als die
Oberfläche; Lektion 5.4 erklärt das.

### Stolperfallen

- **Der Block erscheint nicht.** Der INI-Cache hat noch die alte Liste; oder die Administration verwendet das
  klassische Design, dessen Kette `admin4` nicht enthält. Prüfen Sie `SiteDesign` des Siteaccess der Administration.
- **Klassen aus einem klassischen Template kopieren.** `table.list`, `bglight`/`bgdark` gehören zu älteren Templates;
  neues Markup verwendet `.a4-table`.
- **Ein Link ohne Namen.** Ein Link, der nur aus einem Icon besteht, braucht einen Namen (Text in `.a4-sr-only` oder
  `aria-label`); bleiben Sie in dieser Übung bei Textlinks.

## Praktische Übung

Auf Ihrer Schulungsinstallation, Administration in `admin4` oder `admin4l`, angemeldet als Ihr
Schulungs-Administrator.

1. Öffnen Sie das Dashboard und den Inspektor des Browsers. Finden Sie je ein Element mit den Klassen `a4-btn`,
   `a4-badge` und `a4-table` (oder `table.list`) und notieren Sie, zu welchem Teil der Seite es gehört.
2. Legen Sie `extension/training_theme/settings/dashboard.ini.append.php` an:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [DashboardSettings]
   DashboardBlocks[]=training_notes

   [DashboardBlock_training_notes]
   Priority=70
   NumberOfItems=5
   PolicyList[]=content/read

   */ ?>
   ```

3. Legen Sie den Ordner `extension/training_theme/design/admin4/templates/dashboard/` an und darin `training_notes.tpl`:

   ```
   <h2>{'Latest in the content tree'|i18n( 'design/admin/dashboard/training_notes' )}</h2>
   {def $training_items = fetch( 'content', 'list', hash( 'parent_node_id', ezini( 'NodeSettings', 'RootNode', 'content.ini' ),
                                                          'limit', $block.number_of_items,
                                                          'sort_by', array( 'modified', false() ) ) )}
   {if $training_items}
   <div class="a4-table-wrap">
   <table class="a4-table">
       <thead>
           <tr>
               <th scope="col">{'Name'|i18n( 'design/admin/dashboard/training_notes' )}</th>
               <th scope="col">{'Type'|i18n( 'design/admin/dashboard/training_notes' )}</th>
           </tr>
       </thead>
       <tbody>
       {foreach $training_items as $training_item}
           <tr>
               <td><a href={concat( 'content/view/full/', $training_item.node_id )|ezurl} dir="auto">{$training_item.name|wash}</a></td>
               <td><span class="a4-badge">{$training_item.class_name|wash}</span></td>
           </tr>
       {/foreach}
       </tbody>
   </table>
   </div>
   {else}
   <p class="a4-empty">{'Nothing published below the top of the tree yet.'|i18n( 'design/admin/dashboard/training_notes' )}</p>
   {/if}
   <div class="a4-actions">
       <a class="a4-btn" href={concat( 'content/view/full/', ezini( 'NodeSettings', 'RootNode', 'content.ini' ) )|ezurl}>{'Open the content tree'|i18n( 'design/admin/dashboard/training_notes' )}</a>
   </div>
   {undef $training_items}
   ```

4. Prüfen Sie es: `php bin/php/eztemplatecheck.php -sadmin extension/training_theme/design/admin4/templates/dashboard/training_notes.tpl`.
5. Leeren Sie die Caches:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-id=template-override
   php bin/php/ezcache.php --clear-tag=template
   ```

   In einer Übungsumgebung mit Velocity zusätzlich `php bin/php/console exp:velocity restart`.
6. Laden Sie das Dashboard neu. Suchen Sie den neuen Block unterhalb der mitgelieferten.
7. Ändern Sie das Badge in `<span class="a4-badge is-info">` und laden Sie neu. Probieren Sie `is-ok` und
   `is-accent` aus.
8. Ändern Sie die Schaltfläche in `a4-btn a4-btn-primary`, laden Sie neu und entscheiden Sie, welche der beiden zu
   einem Block passt, der nicht die Hauptaktion der Seite ist.
9. Klicken Sie in der Kopfzeile auf die Schaltfläche für den Farbmodus, um in den Dunkelmodus zu wechseln, und sehen
   Sie sich Ihren Block an. Wechseln Sie zurück.
10. Durchsuchen Sie Ihre beiden neuen Dateien nach `style` und nach `#`: Es darf kein Inline-CSS und keine Farbe
    geben.

## Erwartetes Ergebnis

- Schritt 6: eine Karte auf dem Dashboard mit der Überschrift „Latest in the content tree“, eine gestreifte Tabelle mit
  fünf Elementen und einem Badge pro Typ sowie eine Schaltfläche „Open the content tree“.
- Schritt 9: Der Block ist mit dem Rest der Seite dunkel, ohne weißen Kasten, ohne von den Teilnehmenden
  geschriebenes CSS.
- Schritt 10: kein Treffer.
- Der Trainer prüft das Dashboard einer Person im hellen und im dunklen Modus am Projektor.

![Eine aus den gemeinsamen Komponenten gebaute Seite: Zählerkarten, eine Filterleiste, Schaltflächen und Karten](../../../../../eng-us/help/images/trainers/td-components.webp)

## Quiz

1. Wohin legt eine Erweiterung das Template ihres Dashboard-Blocks, damit admin4 es findet?
2. Welche Klasse umhüllt eine Datentabelle, damit sie in einem schmalen Fenster innerhalb ihrer Karte scrollt?
3. Eine Liste ist leer. Welche Komponente sagt das?
   A. `.a4-notice` B. `.a4-empty` C. ein einfaches `<p>` D. `.a4-meta`
4. Wie wird ein Badge als „in Ordnung“ oder „Warnung“ dargestellt?
5. Warum sah der Block im Dunkelmodus richtig aus, ohne eigenes CSS der Teilnehmenden?

<details>
<summary>Antworten</summary>

1. In `extension/<ext>/design/admin4/templates/dashboard/<identifier>.tpl`; die Erweiterung registriert ihre
   Design-Ordner mit `DesignExtensions[]`, und admin4 ist in der Kette der Administration.
2. `.a4-table-wrap` um `table.a4-table`
   ([Die Administration gestalten, Komponenten](../../../../../eng-us/bc/6.0/admin4-styling.md#components)).
3. **B.** Eine leere Liste sagt das in `.a4-empty`, nie in einem einfachen Absatz.
4. Mit einer Zustandsklasse: `.a4-badge.is-ok`, `.is-warn`, `.is-bad`, `.is-info`, `.is-accent`.
5. Die Komponenten beziehen ihre Farben aus Tokens, und jedes Farb-Token hat einen dunklen Wert.

</details>

## Weiterführende Literatur

- [Die Administration gestalten](../../../../../eng-us/bc/6.0/admin4-styling.md#components): die vollständige
  Komponententabelle, Karten, Tabellen und die Code-Review-Checkliste.
- [Das Design admin4](../../../../../eng-us/features/6.0/admin4-design.md): was admin4 rund um die Komponenten bereitstellt.
- [Template-Operatoren in einem Theme](../../../../../eng-us/help/designers/extensions/template-operators-in-a-theme.md): wenn ein Block
  mehr als einen Fetch braucht.

## Verwandte Seiten

- [Modul 4: Die Administration gestalten](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
