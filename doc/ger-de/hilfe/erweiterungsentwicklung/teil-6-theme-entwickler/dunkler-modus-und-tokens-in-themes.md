---
title: Dunkler Modus und Design-Tokens in Themes
audience: designer, developer
category: extension-development
order: 70
summary: Geben Sie einem Theme einen Satz Design-Tokens mit hellen und dunklen Werten, folgen Sie der Systemeinstellung des Besuchers, bieten Sie einen optionalen Umschalter ohne Inline-Skripte an und prüfen Sie den Kontrast, so wie es das Design admin4 tut.
keywords: dunkler Modus, Dark Mode, Design-Tokens, CSS-Variablen, Umschalter, Kontrast, Farbschema, dark mode, design tokens, CSS custom properties, prefers-color-scheme, color-scheme, toggle, data attribute, contrast, admin4, a4 tokens, theme
type: help
---
# Dunkler Modus und Design-Tokens in Themes

Teil 6, Kapitel 7. Viele Besucher nutzen ihr Gerät im dunklen Modus, und eine Website, die das ignoriert, blendet sie
mit Weiß. Der dunkle Modus ist günstig, wenn das Theme von Anfang an auf **Design-Tokens** aufbaut: benannten Werten für
Farben (und Abstände, Radien, Schrift), die überall verwendet werden, mit einem zweiten Satz Werte für Dunkel. Dieses
Kapitel zeigt das Muster, so wie das Design admin4 es verwendet.

## Tokens: ein Name, zwei Werte

```css
:root {
    color-scheme: light dark;
    --mytheme-ink: #1f2328;
    --mytheme-muted: #59636e;
    --mytheme-surface: #ffffff;
    --mytheme-card: #f6f8fa;
    --mytheme-line: #d0d7de;
    --mytheme-accent: #0b6bcb;
}

@media (prefers-color-scheme: dark) {
    :root:not([data-mytheme-theme="light"]) {
        --mytheme-ink: #e6edf3;
        --mytheme-muted: #9198a1;
        --mytheme-surface: #0d1117;
        --mytheme-card: #161b22;
        --mytheme-line: #30363d;
        --mytheme-accent: #4493f8;
    }
}

:root[data-mytheme-theme="dark"] {
    --mytheme-ink: #e6edf3;
    --mytheme-muted: #9198a1;
    --mytheme-surface: #0d1117;
    --mytheme-card: #161b22;
    --mytheme-line: #30363d;
    --mytheme-accent: #4493f8;
}

body { color: var(--mytheme-ink); background: var(--mytheme-surface); }
.mytheme-card { background: var(--mytheme-card); border: 1px solid var(--mytheme-line); }
a { color: var(--mytheme-accent); }
```

So funktioniert es:

- Der erste Block ist immer das helle Schema.
- Die Media Query wendet die dunklen Werte an, wenn das Gerät im dunklen Modus ist, **außer** der Besucher hat auf der
  Website Hell gewählt (`data-mytheme-theme="light"` am `<html>`).
- Der letzte Block wendet die dunklen Werte an, wenn der Besucher auf der Website Dunkel gewählt hat, gleichgültig, was
  das Gerät sagt.
- Jede Regel darunter verwendet nur Tokens. Keine Regel braucht eine eigene dunkle Fassung.
- `color-scheme: light dark` teilt dem Browser mit, dass Formularfelder und Bildlaufleisten ebenfalls folgen dürfen.

Das Design admin4 funktioniert genauso: Seine Tokens sind `--a4-*` in `components.css`, und sein Modus ist das Attribut
`html[data-a4-theme="light"|"dark"]`. Seine Regel lautet, dass „eine Komponenten- oder Modulregel nie eine eigene
Regel `html[data-a4-theme="dark"]` braucht: die Tokens schalten um“ ([Die Administration gestalten](../../../../eng-us/bc/6.0/admin4-styling.md)).

## Regeln für Tokens

1. **Jede Farbe im Theme ist ein Token.** Ein `#fff`, das in eine einzelne Regel geschrieben wurde, ist der weiße Kasten
   auf der dunklen Seite.
2. **Jedes Farb-Token erhält in derselben Änderung einen dunklen Wert.** Der CI-Test von admin4 schlägt bei einem Token
   ohne dunklen Wert fehl; dieselbe Disziplin können Sie von Hand einhalten.
3. **Wenige Tokens, nach ihrer Rolle benannt** (ink, muted, surface, card, line, accent, ok, warn, bad), nicht nach der
   Farbe (`--blue`). Eine Rolle behält ihre Bedeutung in beiden Schemata.
4. **Eine Akzentfarbe.** Zwei Akzentfarben sehen in einem der Schemata wie ein Fehler aus.
5. **Ihr eigenes Präfix** (`--mytheme-*`), damit ein Theme, das auf Ihrem aufbaut, oder das Stylesheet einer Erweiterung
   nicht kollidiert.

## Bilder und das dunkle Schema

- Logos mit dunkler Schrift brauchen eine helle Fassung: Verwenden Sie `<picture>` mit einer Quelle mit
  `prefers-color-scheme`-Media-Query oder ein SVG, das `currentColor` verwendet.
- Fotos brauchen nichts.
- Schatten wirken auf Dunkel anders: Machen Sie auch sie zu Tokens.

## Ein optionaler Umschalter, ohne Inline-Skript

Dem Gerät zu folgen genügt für die meisten Websites. Bieten Sie einen Umschalter an, muss er die Wahl anwenden,
**bevor** die Seite gezeichnet wird, sonst blitzt die Seite im falschen Schema auf. Dafür ist ein Skript im `<head>`
nötig. Unter einer Content Security Policy muss es eine Datei sein, kein Inline-Skript. Eine kleine Datei, als normales
(blockierendes) Skript im `<head>` geladen:

```javascript
// design/mytheme/javascript/mytheme_head.js: apply the saved scheme before the first paint
( function()
{
    var saved = null;
    try { saved = window.localStorage.getItem( 'mytheme-theme' ); } catch ( e ) {}
    if ( saved === 'light' || saved === 'dark' )
    {
        document.documentElement.setAttribute( 'data-mytheme-theme', saved );
    }
} )();
```

```text
<head>
...
{ezscript( array( 'mytheme_head.js' ) )}
{ezcss_load( ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) )}
</head>
```

und eine Schaltfläche, die Ihr Hauptskript behandelt:

```text
<button type="button" class="mytheme-toggle" data-mytheme-toggle>{'Dark mode'|i18n( 'design/mytheme' )|wash}</button>
```

```javascript
document.addEventListener( 'click', function( e )
{
    if ( !e.target.closest || !e.target.closest( '[data-mytheme-toggle]' ) )
        return;
    var root = document.documentElement;
    var dark = root.getAttribute( 'data-mytheme-theme' ) === 'dark' ||
        ( !root.hasAttribute( 'data-mytheme-theme' ) && window.matchMedia( '(prefers-color-scheme: dark)' ).matches );
    var next = dark ? 'light' : 'dark';
    root.setAttribute( 'data-mytheme-theme', next );
    try { window.localStorage.setItem( 'mytheme-theme', next ); } catch ( e2 ) {}
} );
```

Die `try`-Blöcke sind wichtig: Der Speicher kann nicht verfügbar sein (private Fenster, blockierte Website-Daten), und
die Seite muss auch ohne ihn funktionieren.

## Prüfen

- Schalten Sie das Gerät zwischen Hell und Dunkel um: Die Website folgt.
- Verwenden Sie den Umschalter und laden Sie neu: Die Wahl bleibt erhalten, ohne Aufblitzen.
- Kontrast: normaler Text mindestens 4,5:1 gegenüber seinem Hintergrund in beiden Schemata, großer Text 3:1. Die
  Entwicklerwerkzeuge der Browser zeigen das Verhältnis, wenn Sie ein Textelement untersuchen.
- Sehen Sie sich jede Art von Seite an, nicht nur die Startseite: Formulare, Tabellen, Fehlerseiten.

## Verwandte Seiten

- Zurück: [Theme-Assets, der Packer und CSP](theme-assets-der-packer-und-csp.md)
- Weiter: [Ein Theme verteilen](ein-theme-verteilen.md)
- [admin4-Styling für Seiten von Erweiterungen](../../../../eng-us/help/extension-development/part-4-upgrading/admin4-styling-for-extension-pages.md)
- [prefers-color-scheme](https://developer.mozilla.org/en-US/docs/Web/CSS/@media/prefers-color-scheme) bei MDN
- [Contrast (minimum)](https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html) in WCAG 2.2
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
