---
title: "Lektion 4.4: Modul- und Erweiterungs-Stylesheets"
audience: trainer, designer, developer
category: training
order: 44
summary: Die Teilnehmenden entscheiden, wohin Administrations-CSS gehört (Modul-Stylesheet des Kerns oder Erweiterungs-Stylesheet), benennen Klassen mit einem Präfix, geben einem Zustand eine Kontur für erzwungene Farben, verwenden logische Eigenschaften und Stapel-Tokens und prüfen ihren Block mit emulierten erzwungenen Farben. 45 Minuten.
keywords: theme-design, admin4, modul-stylesheet, stylesheets/modules, AdminComponentCSSFileList, BackendCSSFileList, präfix a4-, präfix der erweiterung, erzwungene farben, Highlight, logische eigenschaften, z-index-tokens, --a4-z, is-current, aria-pressed, theme design, module stylesheet, forced colors, logical properties, z-index tokens
type: help
---
# Lektion 4.4: Modul- und Erweiterungs-Stylesheets

Manches können die gemeinsamen Komponenten nicht: eine Anzeige mit der eigenen Bedeutung eines Moduls, ein Layout, das
nur eine Seite hat. Solches CSS ist erlaubt, aber es hat einen festen Ort, ein festes Namensschema und drei weitere
Pflichten: erzwungene Farben, Rechts-nach-links und Stapelung. Diese Lektion behandelt, wohin das CSS gehört und wie
es geschrieben wird, damit es die Design-Tests und die Einstellungen jedes Benutzers übersteht.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 4: Die Administration gestalten](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Experten |
| Zielgruppe | Designer und Entwickler, die Administrationsseiten gestalten |
| Voraussetzungen | [Lektion 4.3](tokens-und-dunkelmodus.md): `training_admin.css`, geladen über `BackendCSSFileList[]` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- zwischen einem Modul-Stylesheet des Kerns und einem Erweiterungs-Stylesheet wählen und die Einstellung nennen, die
  jedes lädt;
- Klassen mit einem Präfix pro Stylesheet benennen (`.a4-<module>-*` im Kern, außerhalb das eigene Präfix der
  Erweiterung);
- einem Zustand, der nur durch Rahmen, Hintergrund oder Schatten gezeigt wird, eine Kontur für erzwungene Farben geben;
- Layout mit logischen Eigenschaften schreiben und Stapelung mit den Tokens `--a4-z-*`.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Die Administration gestalten](../../../../../eng-us/bc/6.0/admin4-styling.md), Abschnitte „Wie die Stylesheets geladen
werden“, „Stapelebenen“ und „In CI geprüft“; [Rechts-nach-links-Sprachen](../../../../../eng-us/features/6.0/right-to-left.md).

1. **Zwei Orte** (5 Minuten).

   | CSS für | Gehört in | Geladen durch | Klassennamen |
   |---|---|---|---|
   | eine Modulseite des Kerns | `stylesheets/modules/<group>.css` von `design/admin4` und `design/admin` (setup, content, people, structure, workflow) | `design.ini [StylesheetSettings] AdminComponentCSSFileList[]` | `.a4-<module>-*` |
   | die Seite einer Erweiterung | den eigenen Design-Ordner der Erweiterung | `BackendCSSFileList[]` in der `design.ini.append.php` der Erweiterung | das eigene Präfix der Erweiterung |

   Jede Administrationsseite bekommt dasselbe Komponentenbündel, einmal gepackt; Erweiterungsdateien kommen danach,
   sodass eine Erweiterung bei ihrem eigenen Markup das letzte Wort hat. Keine Bereichsklassen pro Modul wie
   `.exp-roles` oder `.nf-`.
2. **Erzwungene Farben** (5 Minuten). Der Hochkontrastmodus von Windows ersetzt Farben und verwirft Rahmenfarben,
   Hintergründe und Schatten. Ein Zustand, der nur dadurch gezeigt wird (`.is-current`, `.is-selected`,
   `[aria-pressed="true"]`, `:checked`), bekommt `outline: 2px solid Highlight` im Block
   `@media (forced-colors: active)` der Datei. Zeigen Sie den Block in `design/admin4/stylesheets/modules/setup.css`.
3. **Rechts-nach-links** (4 Minuten). Verwenden Sie logische Eigenschaften: `margin-inline-start`,
   `padding-inline-end`, `border-inline-start`, `inset-inline-start`, `text-align: start`. Von links nach rechts sind
   sie gleich; von rechts nach links tauschen sie von selbst. Lektion 5.4 vertieft das.
4. **Stapelung** (3 Minuten). Ein `z-index` ist ein Token `--a4-z-*` (`--a4-z-raised`, `--a4-z-sticky`,
   `--a4-z-popover` ...), innerhalb einer Komponente nie eine andere Zahl als 0, 1 oder -1.
5. **Der Fokusring** (3 Minuten). Entfernen Sie nie die Kontur bei `:focus` oder `:focus-visible`; der globale Ring
   (`3px solid var(--a4-ring)`) ist die Fokusanzeige.

### Häufige Fragen

**Darf eine Erweiterung in `stylesheets/modules/` schreiben?** Nein; diese Dateien gehören dem Kern. Das CSS einer
Erweiterung bleibt in der Erweiterung, sodass es mit ihr installiert und entfernt wird.

**Prüfen die CI-Tests das Stylesheet meiner Erweiterung?** Der Gestaltungstest liest die Templates und
Modul-Stylesheets der Kerndesigns. Erweiterungs-Stylesheets folgen denselben Regeln über das Review (Lektion 4.5).

**Warum eine Kontur und kein dickerer Rahmen?** Erzwungene Farben malen auch Rahmen in der Systemfarbe neu, sodass eine
Rahmenänderung nicht als Zustand sichtbar ist; die Kontur in `Highlight` schon.

### Stolperfallen

- **Zwei Präfixe in einer Datei.** Wählen Sie eines (`training-` in diesem Kurs) und bleiben Sie dabei.
- **`left` und `right` aus Gewohnheit.** Durchsuchen Sie die Datei danach, bevor Sie fertig sind.
- **Ein großer z-index „sicherheitshalber“.** Er landet über Dialogen und Menüs. Verwenden Sie das Token für das, was
  das Element ist.

## Praktische Übung

Auf Ihrer Schulungsinstallation, in `training_admin.css` und `training_notes.tpl` aus den Lektionen 4.2 und 4.3.
Verwenden Sie für Schritt 8 einen Chromium-basierten Browser (seine Entwicklerwerkzeuge können erzwungene Farben
emulieren).

1. Lesen Sie `design/admin4/stylesheets/modules/setup.css`: Suchen Sie eine Klasse `.a4-setup-*` und den Block
   `@media (forced-colors: active)` der Datei.
2. Geben Sie in `training_notes.tpl` der ersten Tabellenzeile eine Markierung „aktuell“: Schreiben Sie im `{foreach}`
   `<tr{if $training_item.node_id|eq( $training_items[0].node_id )} class="is-current"{/if}>`.
3. Fügen Sie in `training_admin.css` einen Zustand hinzu, der nur durch Hintergrund und eine Kante auf der Startseite
   gezeigt wird:

   ```css
   .training-notes-table tr.is-current > td {
       background: var(--a4-accent-soft);
       border-inline-start: 3px solid var(--a4-accent-strong);
   }
   ```

4. Geben Sie der Tabelle die Klasse: `<table class="a4-table training-notes-table">`.
5. Fügen Sie am Ende der Datei die Kontur für erzwungene Farben für den Zustand hinzu:

   ```css
   @media (forced-colors: active) {
       .training-notes-table tr.is-current > td { outline: 2px solid Highlight; }
   }
   ```

6. Durchsuchen Sie die Datei nach `left`, `right`, `#`, `px` und `z-index`. Jedes verbliebene `px` muss `1px`, `2px`
   oder `3px` einer Kante oder Kontur sein; ändern Sie alles andere in ein Token.
7. Führen Sie die Routine aus Lektion 3.3 aus und laden Sie das Dashboard im hellen und im dunklen Modus hart neu.
8. Öffnen Sie in den Entwicklerwerkzeugen das Rendering-Panel und emulieren Sie `forced-colors: active`. Suchen Sie
   die aktuelle Zeile.
9. Schalten Sie die Emulation wieder aus.

## Erwartetes Ergebnis

- Schritt 7: Die erste Zeile des Blocks ist in beiden Modi in der Akzentfarbe getönt, mit einer Akzentkante an ihrem
  Anfang.
- Schritt 8: Mit emulierten erzwungenen Farben verschwindet die Tönung, aber die Zeile behält eine sichtbare Kontur in
  der Hervorhebungsfarbe des Systems.
- Schritt 6: Die Datei enthält kein `left`, kein `right`, kein Farbliteral und keinen numerischen `z-index`.
- Der Trainer prüft die Seite einer Person mit emulierten erzwungenen Farben am Projektor.

## Quiz

1. Wohin gehört CSS für eine Administrationsseite einer Erweiterung, und welche Einstellung lädt es?
2. Eine Zeile ist nur durch ihren Hintergrund „aktuell“. Was braucht der Modus mit erzwungenen Farben dafür?
3. Welche Eigenschaft ersetzt `margin-left`, sodass eine Seite von rechts nach links sie von selbst tauscht?
   A. `margin-start` B. `margin-inline-start` C. `margin-block-start` D. `margin-left-rtl`
4. Wie wird ein `z-index` in den Stylesheets von admin4 geschrieben?
5. Richtig oder falsch: Ein Modul-Stylesheet im Kern darf die Klasse `.exp-roles-list` verwenden.

<details>
<summary>Antworten</summary>

1. In den eigenen Design-Ordner der Erweiterung, aufgeführt in `BackendCSSFileList[]`
   ([Die Administration gestalten](../../../../../eng-us/bc/6.0/admin4-styling.md#how-the-stylesheets-are-loaded)).
2. `outline: 2px solid Highlight` im Block `@media (forced-colors: active)` der Datei.
3. **B.**
4. Als Token `--a4-z-*`, `z-index: var(--a4-z-...)`; nur 0, 1 und -1 innerhalb einer Komponente.
5. **Falsch.** Modulklassen des Kerns heißen `.a4-<module>-*`; Bereichspräfixe pro Modul werden nicht verwendet.

</details>

## Weiterführende Literatur

- [Die Administration gestalten](../../../../../eng-us/bc/6.0/admin4-styling.md#stacking-layers): die Stapelskala und die in CI
  ausgeführten Prüfungen.
- [Rechts-nach-links-Sprachen in der Administration](../../../../../eng-us/features/6.0/right-to-left.md): logische
  Eigenschaften und Spiegelungsregeln.
- [Barrierefreiheitsprüfung bei jeder Veröffentlichung](../../../../../eng-us/help/designers/lifecycle/accessibility-audit-per-release.md):
  die Zustände eines Themes sichtbar halten.

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
