---
title: admin4-Styling für Erweiterungsseiten
audience: developer, designer
category: extension-development
order: 190
summary: Lassen Sie die Administrationsseiten einer alten Erweiterung aussehen wie den Rest von admin4 - gemeinsame a4-Komponenten, nur Tokens, dunkler Modus, kein Inline-CSS - mit dem Template-Editor von explayouts_ui als echtem Vorher-nachher-Beispiel.
keywords: a4-Komponenten, Design-Tokens, dunkler Modus, Administrationsseite einer Erweiterung, Upgrade, admin4, a4 components, design tokens, --a4-, dark mode, components.css, BackendCSSFileList, a4-card, a4-btn, a4-table, extension admin page, upgrade
type: help
---
# admin4-Styling für Erweiterungsseiten

Teil 4, Kapitel 19. Die Administrationsseiten einer alten Erweiterung bringen meist ihr eigenes Aussehen mit: eine
Tabelle mit Rahmen aus dem Jahr 2010, graue Schaltflächen, fest ins Stylesheet geschriebene Farben und nichts für den
dunklen Modus. Im Design admin4 fallen sie auf, und im dunklen Modus zeigen sie einen weißen Kasten auf einer dunklen
Seite. Dieses Kapitel gibt Ihren Seiten das Aussehen der Administration mit dem, was sie bereits hat. Die vollständige
Referenz ist [Die Administration gestalten](../../../../eng-us/bc/6.0/admin4-styling.md); Teil 3 behandelt neue
Seiten in [Administrationsseiten im admin4-Stil](../teil-3-erweiterungsentwickler/admin-seiten-im-admin4-stil.md).

## Die Regeln in Kürze

Die Regeln der Administration gelten auch für die Administrationsdesigns von Erweiterungen:

1. Kein `<style>`-Block und kein `style=""`-Attribut in Templates ([Von Inline-CSS zu Stylesheets](inline-css-in-stylesheets.md)).
2. Verwenden Sie zuerst die gemeinsamen Komponentenklassen: Sie beginnen mit `.a4-` und liegen in `components.css`.
3. Farben, Radius, Abstände und Schriften kommen nur aus den `--a4-*`-Tokens. Keine fest eingetragenen Werte, keine
   lokalen Aliase.
4. Jede Farbe, die Sie verwenden, muss im dunklen Modus funktionieren, was die Tokens für Sie erledigen.
5. Ihre eigenen Klassen erhalten das Präfix Ihrer Erweiterung und liegen in Ihrem Stylesheet, das zu
   `BackendCSSFileList[]` hinzugefügt wird.
6. Prüfen Sie die Seite im hellen und im dunklen Modus, bei normaler Breite und bei 200 % Zoom.

## Schritt 1: die Klassen Ihres Markups durch Komponenten ersetzen

Ordnen Sie die Teile der alten Seite den Komponenten zu. Die häufigsten:

| Altes Markup | Komponente |
|---|---|
| Ein Kasten mit Überschrift (`div.box-header` + `div.box-content` oder Ihr eigenes `.myoldext-box`) | `.a4-card` mit `.a4-card-head`, `.a4-card-body`, `.a4-card-foot` |
| Eine Datentabelle (`table.list` oder Ihre eigene) | `table.list` funktioniert bereits; für neues Markup `.a4-table-wrap` > `table.a4-table` |
| Schaltflächen | `.a4-btn`, `.a4-btn-primary`, `.a4-btn-danger`, `.a4-btn-small` |
| Eine Reihe von Schaltflächen über oder unter einer Liste | `.a4-toolbar` (`.is-bottom` unter einer Liste) |
| Formularfelder | `.a4-field` > `.a4-label` + `.a4-input` |
| Text „Nichts gefunden“ | `.a4-empty` |
| Erfolgs- und Fehlermeldungen | `.a4-notice` mit `.is-ok`, `.is-warn`, `.is-bad`, `.is-info` |
| Statuswörter („aktiv“, „fehlgeschlagen“) | `.a4-badge` mit denselben Zustandsklassen |
| Ein Seitentitel mit Aktionen | `.a4-pagehead` |

Umschließen Sie den Inhalt der Seite mit `.a4-page`, das das Box-Modell, die Schrift, Links und den Fokusring setzt.

Eine Listenseite, wie die Referenz sie zeigt:

```html
<div class="a4-page">
  <div class="a4-toolbar"><p class="a4-meta">12 roles</p>
    <div class="a4-actions"><a class="a4-btn a4-btn-primary" href="...">New role</a></div></div>
  <ul class="a4-cards">
    <li class="a4-card"><div class="a4-card-head"><div class="a4-card-title"><h3 class="a4-h3">Editor</h3>
      <span class="a4-badge is-ok">assigned</span></div></div></li>
  </ul>
  <p class="a4-empty">No roles match.</p>
</div>
```

> **Hinweis**
> Die `.a4-*`-Klassen sind für die Designs admin, admin3 und admin4 definiert (design/admin hat eine identische Kopie
> von `components.css`). Eine Erweiterung, die auch in einer viel älteren Administration richtig aussehen muss,
> behält ihre alten Klassen neben den neuen, bis sie deren Unterstützung aufgibt.

## Schritt 2: was übrig bleibt, kommt in Ihr Stylesheet, aus Tokens

Manches können die Komponenten nicht: die Legende eines Diagramms, ein besonderes Raster. Diese Regeln kommen in das
Stylesheet Ihrer Erweiterung, mit Ihrem Präfix, nur aus Tokens gebaut.

Ein echtes Beispiel: se7enxweb/explayouts_ui, Commit 9babf78, „The admin4 template editor takes its colors, spacing
and type straight from admin4's tokens, without local aliases or fallback colors“. Vorher hat das Stylesheet eigene
Variablen mit Fallback-Farben definiert:

```css
:root {
    --bg: var(--a4-surface, #f6f8fa);
    --panel: var(--a4-card, #ffffff);
    --panel-2: var(--a4-line, #d0d7de);
    --fg: var(--a4-ink, #1f2328);
    --accent: var(--a4-accent-strong, #c2410c);
}
html, body { background: var(--bg); color: var(--fg); font-size: 14px; }
```

Nachher sind die Aliase weg, und jede Regel nennt das Token selbst:

```css
html, body { background: var(--a4-surface); color: var(--a4-ink); font-family: var(--a4-mono); font-size: var(--a4-text-m); }
```

Warum die Fallbacks entfernen? Eine Fallback-Farbe ist eine fest eingetragene helle Farbe, die immer dann erscheint,
wenn das Token fehlt, und im dunklen Modus ist das ein weißes Panel. Lokale Aliase (`--bg`, `--panel`) verbergen
außerdem, welches Token eine Regel verwendet, sodass die nächste Person es nicht mit der Referenz abgleichen kann.

Die Tokens, die Sie am häufigsten brauchen werden:

| Wofür | Token |
|---|---|
| Text, gedämpfter Text | `--a4-ink`, `--a4-muted` |
| Hintergründe | `--a4-surface` (Seite), `--a4-card` (Karten), `--a4-soft` (eine leichte Tönung) |
| Linien | `--a4-line`, `--a4-line-strong` |
| Die Akzentfarbe | `--a4-accent`, `--a4-accent-strong` (Flächen mit weißem Text), `--a4-accent-text` (Text und Links) |
| Zustände | `--a4-ok`, `--a4-warn`, `--a4-bad`, `--a4-info`, jeweils mit `-bg` und `-line` |
| Abstände | `--a4-space-1` bis `--a4-space-7` (4 bis 48 px) |
| Radius | `--a4-radius-s`, der eine Radius der Komponenten |
| Schrift | `--a4-font`, `--a4-mono`, `--a4-text-xs` bis `--a4-text-3xl` |

Laden Sie das Stylesheet über `BackendCSSFileList[]` in der `design.ini.append.php` Ihrer Erweiterung, niemals über
`AdminComponentCSSFileList[]`, die eigene Liste des Kernels.

## Schritt 3: dunkler Modus

Der Modus von admin4 ist `html[data-a4-theme="light"]` oder `"dark"`. Wenn jede Farbe aus einem Token kommt, brauchen
Sie überhaupt keine Regeln für den dunklen Modus: Die Tokens wechseln. Wenn Sie glauben, eine Regel
`html[data-a4-theme="dark"]` zu brauchen, suchen Sie das Token, das Sie übersehen haben. Wenn Sie ein eigenes Token
hinzufügen (nur für etwas, das die gemeinsamen Tokens wirklich nicht ausdrücken können), geben Sie ihm im selben
Commit einen Wert für den dunklen Modus.

## Schritt 4: ansehen

Öffnen Sie jede Seite der Erweiterung in admin4:

- im hellen und im dunklen Modus (der Umschalter ist in der Kopfzeile);
- bei etwa 1440 Pixeln Breite und bei 960 Pixeln mit dem Browser auf 200 % Zoom;
- bei geöffneter Konsole.

Prüfen Sie: keine weiße Karte auf der dunklen Seite, lesbarer Text (Kontrast mindestens 4,5:1), keine horizontale
Bildlaufleiste, keine Fehler in der Konsole. Leeren Sie nach einer Änderung am Stylesheet die Caches, die den Link zum
gepackten Stylesheet enthalten (template und template-block), und machen Sie einen harten Reload; hängen Sie kein `?v=`
an den Link.

## Schritt 5: ein Test

explayouts_ui hat im selben Commit `tests/Admin4StylingTest.php` hinzugefügt: Er hält die admin4-Templates frei von
Inline-CSS und Versionsabfragen und die Stylesheets frei von lokalen Custom Properties und fest eingetragenen Farben.
Ein solcher Test ist kurz und braucht keine Datenbank; siehe
[Tests und CI für eine aktualisierte Erweiterung](tests-und-ci-fuer-eine-aktualisierte-erweiterung.md).

## Verwandte Seiten

- Zurück: [Vom alten jQuery zu jQuery 4](altes-jquery-zu-jquery-4.md)
- Weiter: [Kontextmenüs und Integration in die Administration](kontextmenues-und-admin-integration.md)
- [Die Administration gestalten](../../../../eng-us/bc/6.0/admin4-styling.md)
- [Administrationsseiten im admin4-Stil](../teil-3-erweiterungsentwickler/admin-seiten-im-admin4-stil.md)
- [Dunkler Modus und Design-Tokens in Themes](../teil-6-theme-entwickler/dunkler-modus-und-tokens-in-themes.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
