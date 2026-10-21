---
title: "Lektion 4.1: Die Gestaltungsregeln der Administration"
audience: trainer, designer, developer
category: training
order: 41
summary: Die Teilnehmenden erklären, warum jedes Administrationsdesign Inline-CSS verbietet, zählen die sieben Gestaltungsregeln und ihre dokumentierten Ausnahmen auf und finden Regelverstöße in einem Beispiel-Template. 45 Minuten.
keywords: theme-design, admin4, administrationsdesign, gestaltungsregeln, kein inline-css, style-block, style-attribut, components.css, token, dunkelmodus, packer, AdminStylingRulesTest, administrationsdesign einer erweiterung, theme design, administration design, styling rules, no inline CSS, extension admin design
type: help
---
# Lektion 4.1: Die Gestaltungsregeln der Administration

Öffentliche Themes gehören den Designern selbst; die Administration wird von allen Erweiterungen und allen Redakteuren
geteilt, und für sie gelten strenge Regeln. Sie sind aus Erfahrung entstanden: In den Modulseiten von admin4 waren 55
Inline-`<style>`-Blöcke mit vier verschiedenen Akzentfarben und weißen Karten im Dunkelmodus gewachsen. Diese Lektion
vermittelt die Regeln und ihre Gründe, bevor die Teilnehmenden in den nächsten Lektionen Markup für die Administration
schreiben.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 4: Die Administration gestalten](README.md) |
| Dauer | 45 Minuten (25 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Entwickler, die Administrationsseiten oder Erweiterungen mit Administrationsseiten bauen |
| Voraussetzungen | [Modul 3](../modul-03-stylesheets-und-skripte/README.md); [Lektion 1.4](../modul-01-designs-und-die-fallback-kette/website-designs-und-admin-designs.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die sieben Gestaltungsregeln der Administrationsdesigns nennen;
- für jede Regel erklären, welches Problem sie verhindert;
- die Designs und Erweiterungsdesigns nennen, für die die Regeln gelten;
- die dokumentierten Ausnahmen aufzählen und sagen, warum jede bleibt;
- Regelverstöße in einem Template finden und sagen, wohin das CSS stattdessen gehört.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Die Administration gestalten](../../../../bc/6.0/admin4-gestaltung.md), Abschnitte „Warum“, „Die Regeln“ und
„Ausnahmen“.

1. **Die Geschichte** (5 Minuten). Nennen Sie die Zahlen aus dem Abschnitt „Warum“: ein `<style>`-Block in jedem von 55
   admin4-Templates (7.831 Zeilen), Hilfsklassen bis zu 41-mal kopiert, 35 private Präfixe für Custom Properties, vier
   Akzentfarben, 46 Blöcke, die Weiß fest codierten und keine dunklen Regeln hatten. Inline-CSS wird weder gepackt noch
   zwischengespeichert, und Erweiterungen hatten keine gemeinsamen Klassen, auf die sie aufbauen konnten.
2. **Die sieben Regeln** (12 Minuten). Stellen Sie sie auf eine Folie:
   1. Kein `<style>`-Block und kein Attribut `style=""` in einem Template eines Administrationsdesigns. Werte pro
      Element sind eine Klasse (`.a4-pct-0` bis `.a4-pct-100`) oder ein `data-*`-Attribut.
   2. Verwenden Sie zuerst die gemeinsamen Komponentenklassen (`components.css`, Präfix `.a4-`); kopieren Sie nie eine
      davon in ein Modul oder eine Erweiterung.
   3. Farben, Radius, Abstände und Schriften kommen nur aus den Tokens `--a4-*`; keine Literalwerte, keine lokalen
      Aliasse.
   4. Jedes Farb-Token hat einen Wert für `html[data-a4-theme="dark"]`, hinzugefügt in derselben Änderung.
   5. Modul-CSS ist die Ausnahme: nur, was Komponenten nicht können, in `stylesheets/modules/<group>.css`, als Klassen
      `.a4-<module>-*`, gebaut aus Tokens.
   6. Prüfen Sie jede neue oder geänderte Seite im hellen und im dunklen Modus, bei 1440 px und bei 960 px mit Skalierung
      2 (ein Browser mit 200 % Zoom).
   7. CSS erreicht den Browser über den Packer; nie ein Parameter `?v=` an einem Link.
3. **Geltungsbereich** (3 Minuten). Jedes Administrationsdesign (`admin`, `admin3`, `admin4`, `admin4l`) und die
   Administrationsdesigns von Erweiterungen (`extension/*/design/admin*`, `exp_adminui` und die anderen). Erweiterungen
   behalten ihr eigenes Stylesheet, geladen über `BackendCSSFileList[]`, und folgen denselben Regeln mit ihrem eigenen
   Präfix.
4. **Ausnahmen** (5 Minuten). Drei, jede dort dokumentiert, wo sie steht: das Inline-Skript oben in der
   `page_head_style.tpl` von admin4 (es setzt den Farbmodus, bevor die Seite gezeichnet wird, damit sie nicht
   aufblitzt), ein zweizeiliges Skript im `pagelayout.tpl` von admin3 (die gezogene Spaltenbreite) und ein
   `<style>`-Block in `design/admin/templates/pagelayout.tpl` (Spaltenbreiten aus `menu.ini`, die ein Stylesheet nicht
   kennen kann). Betonen Sie: Zwei davon sind Skripte, kein CSS.

### Häufige Fragen

**Gelten diese Regeln für mein öffentliches Theme?** Die Regeln sind für Administrationsdesigns geschrieben. Die erste
Regel ist für jedes Theme gute Praxis (Modul 3: Eine Content Security Policy blockiert Inline-Stile), und die Idee der
Tokens funktioniert auch auf einer öffentlichen Website, aber Farben und Radien eines öffentlichen Themes sind die Wahl
der Designer.

**Wer prüft sie?** Ein Test, `tests/tests/design/AdminStylingRulesTest.php`, schlägt bei Inline-CSS in den
Administrationsdesigns des Kerns und bei Literalfarben in den Modul-Stylesheets fehl (Lektion 4.5). Den Rest prüfen
Reviewer mit der Checkliste.

**Was ist mit einem alten Template, das noch einen `<style>`-Block hat?** Es steht auf der Allowlist des Tests mit den
Templates, die gerade migriert werden. Die Liste wird nur kürzer; niemand ergänzt sie, damit eine Änderung durchgeht.

### Stolperfallen

- **Inline-Stile „nur dieses eine Mal“.** Jeder Block in der Geschichte hat so angefangen. Bleiben Sie in der Schulung
  standhaft.
- **Regel 2 mit „nie CSS schreiben“ verwechseln.** Modul- und Erweiterungs-CSS gibt es; es kommt nach den Komponenten
  und verwendet Tokens.
- **Den Dunkelmodus vergessen.** Lektion 4.3 zeigt die Wirkung; erwähnen Sie ihn hier.

## Praktische Übung

Arbeiten Sie paarweise, auf Papier oder in einem Editor; an der Installation wird nichts geändert. Der Trainer teilt
das Beispiel-Template unten aus (oder projiziert es).

```
<div class="my-report" style="padding: 12px; background: #fff;">
  <style>
    .my-report h2 { color: #c2410c; border-radius: 6px; }
    .my-report .sr { position: absolute; left: -999em; }
  </style>
  <h2>Report</h2>
  <span class="sr">Status:</span>
  <div class="bar" style="width: {$percent}%"></div>
  <a class="my-button" href={'report/run'|ezurl} onclick="return confirm('Run?')">Run</a>
</div>
```

1. Markieren Sie jede Stelle, die gegen Regel 1 verstößt.
2. Markieren Sie jeden Literalwert für Farbe, Radius und Abstand (Regel 3). Schreiben Sie zu jedem, welche Art von
   Token ihn ersetzen sollte (Farbe, Radius, Abstand).
3. Finden Sie die Hilfsklasse, die kopiert statt wiederverwendet wird (Regel 2). Nennen Sie die gemeinsame Klasse, die
   das bereits leistet, anhand der Komponententabelle aus
   [Die Administration gestalten](../../../../bc/6.0/admin4-gestaltung.md#komponenten).
4. Schreiben Sie für `style="width: {$percent}%"` auf, wie Regel 1 einen Wert pro Element auszudrücken verlangt.
5. Finden Sie das `onclick`. Gegen welche andere Regel aus Modul 3 verstößt es, und wohin gehört der Code?
6. Beschreiben Sie für `background: #fff`, was ein Redakteur im Dunkelmodus sehen würde.
7. Entscheiden Sie, wohin das restliche CSS gehört: in ein Modul-Stylesheet des Kerns oder in ein in
   `BackendCSSFileList[]` aufgeführtes Erweiterungs-Stylesheet. Schreiben Sie den Dateinamen auf, den Sie verwenden
   würden, wenn dieses Template in Ihrer eigenen Erweiterung stünde.
8. Vergleichen Sie mit einem anderen Paar.

## Erwartetes Ergebnis

- Schritte 1 und 2: Der `<style>`-Block, die beiden Attribute `style=""` und die Literale `12px`, `#fff`, `#c2410c`,
  `6px` sind markiert.
- Schritt 3: Die Hilfsklasse für Screenreader `.sr` ist eine Kopie von `.a4-sr-only`.
- Schritt 4: eine der Klassen `.a4-pct-0` bis `.a4-pct-100` (mit `.a4-meter` oder `.a4-bars`) oder ein
  `data-*`-Attribut.
- Schritt 5: Der Inline-Handler verstößt gegen die Content Security Policy (`script-src 'self'`); der Code gehört in
  eine Skriptdatei des Designs.
- Schritt 6: ein weißer Kasten auf der dunklen Seite.
- Der Trainer sammelt ein Blatt pro Paar ein und geht die Antworten am Projektor durch.

## Quiz

1. Nennen Sie die beiden Arten von Inline-CSS, die Regel 1 in Administrations-Templates verbietet.
2. Welche Datei enthält die gemeinsamen Komponenten `.a4-*` und die Tokens?
   A. `admin4.css` B. `components.css` C. `core.css` D. `site.css`
3. Was muss mit jedem neuen Farb-Token mitkommen?
4. Bei welchen zwei Fenstergrößen wird eine geänderte Administrationsseite geprüft?
5. Warum bleibt das Inline-Skript oben in der `page_head_style.tpl` von admin4?

<details>
<summary>Antworten</summary>

1. Ein `<style>`-Block und ein Attribut `style=""`
   ([Die Administration gestalten](../../../../bc/6.0/admin4-gestaltung.md#die-regeln)).
2. **B.** `design/admin4/stylesheets/components.css` (mit einer identischen Kopie in `design/admin`).
3. Ein Wert für `html[data-a4-theme="dark"]`, in derselben Änderung.
4. 1440 px bei Skalierung 1 und 960 px bei Skalierung 2 (200 % Zoom), jeweils im hellen und im dunklen Modus.
5. Es setzt den Farbmodus, bevor etwas gezeichnet wird, damit die Seite nicht erst hell und dann dunkel aufblitzt; es
   ist ein Skript, kein CSS.

</details>

## Weiterführende Literatur

- [Die Administration gestalten: ein Komponenten-Stylesheet, Tokens, kein Inline-CSS](../../../../bc/6.0/admin4-gestaltung.md):
  die Referenz für dieses ganze Modul.
- [Das Design admin4](../../../../../eng-us/features/6.0/admin4-design.md): das Design, für das die Regeln gemacht wurden.
- [Inline-Skripte und Inline-Stile entfernen](../../../../../eng-us/help/designers/lifecycle/removing-inline-scripts-and-styles.md): ein
  älteres Template migrieren.

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
