# Die Administration gestalten: ein Stylesheet für Komponenten, Tokens, kein Inline-CSS

Lesen Sie diese Seite, bevor Sie ein Template oder Stylesheet eines Administrationsdesigns (`admin`, `admin3`, `admin4`,
`admin4l`) oder des Admin-Designs einer Erweiterung schreiben oder prüfen (manche Erweiterungen tragen noch einen Ordner
`admin2`). Sie enthält die Regeln, die API der gemeinsamen Komponenten und Tokens, wie die Stylesheets geladen werden,
und die Checkliste für das Code-Review. Gültig seit dem 2026-10-14.

## Warum

Die Modulseiten von admin4 waren auf je einen eigenen `<style>`-Block angewachsen: 55 Templates unter
`design/admin4/templates/` (7.831 Zeilen), 58 weitere in `design/admin` und Attribute `style=""` in 53 Dateien. Jeder
Block kopierte dieselben Hilfsregeln (Text für Bildschirmleser in 27 Dateien, ein Box-Sizing-Reset in 29, der
Fokusring in 29, Regeln für Schaltflächen in 41) unter seiner eigenen Scope-Klasse (`.exp-roles`, `.exp-rad`, `.nf-`,
`.cb-`, `.shop-` ...) und seinen eigenen Custom Properties (35 Präfixe wie `--rl-`, `--rad-`). Die Kopien drifteten
auseinander: vier Akzentfarben (`#c2410c` 27-mal, `#2d6cdf` 12-mal, das Marken-`#f26a21` 3-mal, `#ff5500` einmal) und
zwei Radien. 46 der 55 Blöcke trugen Weiß fest ein, ohne Regeln für den dunklen Modus, sodass der dunkle Modus von
admin4 weiße Karten zeigte. Inline-CSS wird weder gepackt noch gecacht, und Erweiterungen hatten keine gemeinsamen
Klassen, auf denen sie aufbauen konnten.

## Die Regeln

1. **Kein `<style>`-Block und kein Attribut `style=""` in einem Template eines Administrationsdesigns.** Alles CSS
   liegt im Verzeichnis `stylesheets/` des Designs und wird über `design.ini` `[StylesheetSettings]` geladen, sodass der
   ezjscore-Packer es bündelt und cacht. Die wenigen dokumentierten Ausnahmen stehen unten.
   - Ein Wert, der wirklich pro Element gilt (die Höhe eines Balkens, ein Prozentsatz), ist eine Klasse (`.a4-pct-0`
     bis `.a4-pct-100`) oder ein `data-*`-Attribut, das ein Stylesheet oder das Skript der Seite liest, nie `style=""`.
2. **Verwenden Sie zuerst die gemeinsamen Komponentenklassen** (`stylesheets/components.css`, Präfix `.a4-`). Fügen Sie
   eine neue gemeinsame Komponente nur zentral hinzu, wenn nichts passt; kopieren Sie nie eine in ein Modul oder eine
   Erweiterung.
3. **Farben, Radius, Abstände und Schriften kommen nur aus den Tokens `--a4-*`.** Keine fest eingetragenen Werte
   (`#fff`, `#c2410c`, `12px`, `.6em`) und keine lokalen Aliase (`--rl-ink`, `--rad-card`). Es gibt einen Akzent und
   einen Komponentenradius; fügen Sie keinen weiteren hinzu.
4. **Jeder Farb-Token hat einen Wert für `html[data-a4-theme="dark"]`**, hinzugefügt im selben Commit wie der Token.
5. **Modul-CSS ist die Ausnahme**: nur das, was die Komponenten nicht können, in `stylesheets/modules/<group>.css`,
   als Klassen `.a4-<module>-*`, aus Tokens gebaut. Ein Präfix (`.a4-`) für die ganze Administration; keine
   Scope-Klassen pro Modul.
6. **Prüfen Sie jede neue oder geänderte Seite im hellen und im dunklen Modus**, bei 1440 px (Skalierung 1) und bei
   960 px mit Skalierung 2 (ein Browser mit 200 % Zoom): keine helle Karte auf der dunklen Seite, Textkontrast
   mindestens 4,5:1, kein horizontaler Überlauf, keine Fehler in der Konsole.
7. **CSS erreicht den Browser über den Packer.** Stellen Sie mit `exp:velocity deploy --packer` bereit (das leert den
   Cache template-block, der den gepackten Link hält). Hängen Sie nie `?v=` oder eine andere Versionsabfrage an einen
   Link; ein Browser, der noch die alte Datei zeigt, braucht ein hartes Neuladen.

Diese Regeln gelten für jedes Administrationsdesign und für die Admin-Designs von Erweiterungen
(`extension/*/design/admin*`, `exp_adminui` und die übrigen).

## Wie die Stylesheets geladen werden

`settings/design.ini`:

```ini
[StylesheetSettings]
AdminComponentCSSFileList[]
AdminComponentCSSFileList[]=components.css
AdminComponentCSSFileList[]=modules/setup.css
AdminComponentCSSFileList[]=modules/content.css
AdminComponentCSSFileList[]=modules/people.css
AdminComponentCSSFileList[]=modules/structure.css
AdminComponentCSSFileList[]=modules/workflow.css
```

`page_head_style.tpl` von `admin`, `admin3` und `admin4` lädt die Liste nach den eigenen Stylesheets des Designs (und
in admin4 nach `admin4.css`) und vor `BackendCSSFileList`, sodass eine Erweiterung bei ihrem eigenen Markup weiterhin
das letzte Wort hat. Jede Datei ist in der INI genannt: Jede Admin-Seite erhält dasselbe Bündel, einmal gepackt und
gecacht, statt eines Bündels pro Modul.

| Datei | Enthält |
|---|---|
| `components.css` | die Tokens (hell und dunkel) und die Komponenten `.a4-*`; die Kopie in `design/admin` ist identisch (der Test prüft das) |
| `modules/setup.css` | setup/* |
| `modules/content.css` | content/* |
| `modules/people.css` | role, section, state, user, notification, collaboration |
| `modules/structure.css` | class, pdf, rss, package |
| `modules/workflow.css` | workflow, trigger, audit, shop, settings, infocollector, search, url, visual, contentfinder |

Jede Moduldatei existiert in `design/admin4/stylesheets/modules/` und `design/admin/stylesheets/modules/` (`admin3`
findet die Dateien von `design/admin` über seinen Fallback). `admin4.css` bleibt der Rahmen von admin4 (Kopfbereich,
Spalten, Dashboard, Anmeldeseite).

**Erweiterungen** behalten ihr Stylesheet in ihrem eigenen Design-Ordner und fügen es zu `BackendCSSFileList[]` hinzu,
nicht zu `AdminComponentCSSFileList`. In admin4 können sie die Klassen `.a4-*` und die Tokens `--a4-*` verwenden; ihre
eigenen Klassen tragen ihr eigenes Präfix und folgen denselben Regeln (nur Tokens, ein dunkler Wert für jede Farbe).

## Tokens

Alle als `var(--a4-...)`, definiert in `components.css`.

| Gruppe | Tokens |
|---|---|
| Akzent | `--a4-accent` (`#f26a21`), `--a4-accent-hover`, `--a4-accent-strong` (gefüllte Bedienelemente mit weißem Text, 5,2:1), `--a4-accent-press` (deren Füllung beim Überfahren), `--a4-accent-text` (der Akzent als Text oder Link), `--a4-accent-soft` (Tönung), `--a4-on-accent`, `--a4-on-accent-hover` (Text auf der Füllung `-press`), `--a4-ring` (Fokusring, deckend: 3:1 auf jeder Fläche) |
| Neutral | `--a4-ink`, `--a4-muted`, `--a4-line`, `--a4-line-strong`, `--a4-soft`, `--a4-card`, `--a4-surface`, `--a4-zebra` (die geraden Zeilen von Datentabellen), `--a4-field`, `--a4-field-line`, `--a4-disabled`, `--a4-overlay`, `--a4-code-bg`, `--a4-code-ink` |
| Zustände | `--a4-ok`, `--a4-ok-bg`, `--a4-ok-line`; `--a4-warn`, `--a4-warn-bg`, `--a4-warn-line`; `--a4-bad`, `--a4-bad-bg`, `--a4-bad-line`, `--a4-bad-hover`; `--a4-info`, `--a4-info-bg`, `--a4-info-line` |
| Diagramme | `--a4-chart-1` bis `--a4-chart-8`, eine kategoriale Palette in dieser Reihenfolge |
| Tiefe | `--a4-shadow-s`, `--a4-shadow-m`, `--a4-shadow-up` |
| Radius | `--a4-radius-s` (9 px): der eine Komponentenradius. `--a4-radius` (18 px) gilt nur für die drei Spalten des Rahmens |
| Abstände | `--a4-space-1` bis `--a4-space-7` (4, 8, 12, 16, 24, 32, 48 px); eine Länge zwischen zwei Stufen nimmt die nähere (3 und 5 → 4, 6 bis 9 → 8, 10 bis 14 → 12, 15 bis 20 → 16, 22 bis 28 → 24) |
| Bedienelemente | `--a4-control-h` (32 px): eine Höhe für jedes Bedienelement: `.a4-btn` (abgesehen von `.a4-btn-large` 44 px, `.a4-btn-small` 30 px), `.a4-input` und die Felder von `.a4-field`, `.a4-tabs`, `.a4-chip`, die Größen der Seitennavigation, die alten Schaltflächen und Felder, die Aktionen des Knotens und die Werkzeugleiste der Unterelemente. Feldtext ist `--a4-text-m` (14 px), Schaltflächentext `--a4-text-s` (13 px) |
| Layout | `--a4-content-max` (1680 px): die größte Breite, auf die die Inhaltsspalte in einem sehr breiten Fenster wächst, damit die Aktionen einer Seite nahe bei dem bleiben, worauf sie wirken; `--a4-gutter` (24 px): der eine Abstand zwischen den Karten des Rahmens (Seitenspalten und Inhalt, unter dem Kopfbereich, unten); die Ränder des Fensters behalten 24 px für die Tabs der Seitenspalten |
| Abstände von Listen und Karten | `--a4-list-pad` (die Zellen von `table.list`: 8 px oben und unten wie `.a4-table`, 6 px an jeder Seite, damit eine Liste mit zehn Spalten noch in eine Inhaltsspalte von 944 px passt; core.css hatte `.4em`), `--a4-cell-pad-y`/`-x` (8/12 px, `.a4-table`), `--a4-card-pad-y`/`-x` (12/16 px, `.a4-card`). admin4 hat eine Dichte: Die kompakte Ansicht und ihr Schalter im Kopfbereich wurden im Oktober 2026 entfernt (`AdminDensityTest` hält sie fern) |
| Schrift | `--a4-font`, `--a4-mono`, `--a4-text-xs`, `-s`, `-m`, `-l`, `-xl`, `-2xl`, `-3xl` (12, 13, 14, 16, 22, 26, 32 px; nichts unter 12); `--a4-text-body` (der Fließtext: `--a4-text-s` bis zu einem Fenster von 1920px, wachsend bis `--a4-text-m` bei 2560px, ein clamp zwischen zwei Stufen der Skala, sodass die em-Größen der Basis-Stylesheets nie unter 12px fallen), `--a4-leading` (1,5, jeder Block, der umbrechen kann), `--a4-leading-tight` (1,25, nur Überschriften) |

Jeder Farb-, Diagramm- und Tiefen-Token hat einen dunklen Wert; die Tokens für Radius, Abstände, Bedienelemente und
Schrift sind in beiden Modi gleich.

**Tabellen.** Jede Datentabelle ist auf dieselbe Weise gestreift: `table.list`, die Komponente `.a4-table` und die
Tabelle der Unterelemente (`exp/datatable`, über `--exp-dt-row-alt`) färben jede zweite Datenzeile mit `--a4-zebra`,
die überfahrene Zeile mit `--a4-info-bg`, eine ausgewählte Zeile (`.a4-table tr.is-selected`) mit `--a4-accent-soft`;
ein Zeilenkopf (`th scope="row"`) nimmt die Farbe seiner Zeile an. Nur Zeilen mit Datenzellen zählen
(`tr:where(:nth-child(even of :has(> td):not([hidden])))`): Eine Kopfzeile aus `th` innerhalb von `tbody` oder eine
Zeile mit dem Attribut `hidden` wird übersprungen, sodass die erste Datenzeile immer schlicht ist. Die Klassen
`bglight`/`bgdark`, die die Templates noch schreiben, werden für die Farbe ignoriert. Eine von einem Skript versteckte
Zeile (`display: none`) zählt weiterhin; das ist eine bekannte Grenze des Streifens per CSS. Eine Tabelle, die dem
Layout dient, trägt keine dieser Klassen und bleibt schlicht; ein als `table.list` angeordnetes Formular erhält
zusätzlich `a4-plain` (`shop/editvatrule.tpl`, `shop/editcurrency.tpl`). Text auf einer Zebra-Zeile und auf einer
überfahrenen Zeile behält 4,5:1 (`AdminTokenContrastTest`), und kein Stylesheet von admin4 färbt Zeilen in einer
eigenen Farbe (`AdminStylingRulesTest::testDataTablesShareOneZebra`). Die Zellen einer Datentabelle sitzen in der Mitte
ihrer Zeile (Formulartabellen `.a4-plain` behalten ihre Beschriftungen oben). Eine Zeile, deren Auswahl-Kontrollkästchen
(ein Kontrollkästchen direkt in einer ihrer Zellen) angehakt ist, erhält ohne Skript `--a4-accent-soft`
(`tr:has(> td > input[type="checkbox"]:checked)`), über Streifen und Überfahren. Listenköpfe sind nicht klebend: Fast
jede Tabelle sitzt in einem horizontal scrollenden Wrapper (`pagelayout.css`, `div:has(> table)`), und ein klebender
Kopf bindet sich an diesen Wrapper, nicht an die Seite. Eine leere Liste sagt das in `.a4-empty`, nie in einem
einfachen Absatz (`AdminStylingRulesTest::testEmptyListsUseTheEmptyState`).
`--a4-orange`, `--a4-orange-dark` und `--a4-orange-soft` sind die früheren Namen des Akzents, behalten für die
Erweiterungen, die sie verwenden; neuer Code verwendet die Namen `--a4-accent*`.

**Der Akzent** ist das Marken-Orange `#f26a21`, wie im Kopfbereich von admin4 (die ausgewählte Menüpille) und auf
seiner Standardschaltfläche. Weißer Text darauf erreicht nur 3:1, daher verwenden gefüllte Bedienelemente mit weißem
Text seinen dunkleren Ton `--a4-accent-strong` (`#c2410c`, 5,2:1), und Text in Akzentfarbe verwendet
`--a4-accent-text` (`#ad3809` hell, 5,4:1 auch auf einer überfahrenen Tabellenzeile und 4,8:1 für ein Badge
`is-accent` darauf; `#ff9a5c` dunkel).

### Stapelebenen

Die Stapelreihenfolge kommt aus einer Skala in `components.css`; ein Stylesheet schreibt `z-index: var(--a4-z-…)`, nie
eine Zahl (0, 1 und -1 für die Reihenfolge innerhalb einer Komponente ausgenommen). Unabhängig vom Theme, kein dunkler
Wert.

| Token | Wert | Für |
|---|---|---|
| `--a4-z-raised` | 2 | ein Element über seinen Nachbarn (ein überfahrenes Vorschaubild, eine Ablagemarke) |
| `--a4-z-sticky` | 5 | klebende Werkzeugleisten und Hinweise im Inhalt |
| `--a4-z-frame-grip` | 9 | der Breitengriff des klassischen Rahmens, unter seinen Links zum Ein- und Ausblenden |
| `--a4-z-frame-control` | 11 | die Links zum Ein- und Ausblenden und der Breiten-Handler des klassischen Rahmens |
| `--a4-z-hover-preview` | 50 | ein beim Überfahren vergrößertes Vorschaubild, über klebenden Leisten und Griffen |
| `--a4-z-popover` | 60 | ein Fenster im Kopfbereich oder in der Steuerleiste (Suchbereich) |
| `--a4-z-controlbar-top` | 60 | die klebende obere Steuerleiste der Bearbeitungsseiten |
| `--a4-z-scroll-top` | 100 | die Schaltfläche nach oben |
| `--a4-z-navbar` | 110 | das Menü bei Telefonbreite |
| `--a4-z-sidebar` | 120 | über der Seite geöffnete Seitenspalten |
| `--a4-z-sidebar-handle` | 140 | der Breitengriff der linken Spalte |
| `--a4-z-controlbar` | 200 | die feste Steuerleiste der Bearbeitungsseiten |
| `--a4-z-drag-target` | 999 | das Ablageziel, wenn eine Tabellenspalte gezogen wird |
| `--a4-z-header` | 1020 | der feste Kopfbereich |
| `--a4-z-sidebar-toggle` | 1030 | die Tabs an den Fensterrändern (im festen Kopfbereich: unter seinen Kindern eingeordnet) |
| `--a4-z-menu` / `--a4-z-menu-sub` | 1040 / 1050 | Kontextmenüs und ihre Untermenüs, über dem Kopfbereich |
| `--a4-z-overlay` / `--a4-z-modal` | 1060 / 1070 | die Maske eines Dialogs und der Dialog (auch die Grundregel von `theme/modalwindow.css`) |
| `--a4-z-toast` | 1080 | kurzlebige Hinweise |
| `--a4-z-drag` / `--a4-z-drag-mask` | 9000 / 9500 | Stellvertreter beim Ziehen und Ändern der Breite von Spalten der Datentabellen |
| `--a4-z-skip-link` | 10000 | der Sprunglink, solange er fokussiert ist, über allem |
| `--a4-z-card-veil` / `--a4-z-card-spinner` | 2 / 3 | der Beschäftigt-Schleier einer Karte und sein Spinner, innerhalb der Karte |

Innerhalb eines Elternelements mit eigenem Stapelkontext (der feste Kopfbereich, eine transformierte Karte) ordnet ein
Token nur gegenüber den Kindern dieses Elternelements. `AdminStylingRulesTest::testStackingComesFromTheLayerTokens`
schlägt bei jedem anderen z-index fehl, bei einer Änderung der Reihenfolge sticky < grip < Rahmensteuerung < Vorschau
beim Überfahren < Popover < Kopfbereich < Tabs < Menüs < Maske < Dialog < Toasts und bei einem `var(--a4-z-…)`, das
`components.css` nicht definiert (ein Tippfehler fiele auf `auto` zurück).

## Komponenten

| Komponente | Klassen |
|---|---|
| Seiten-Scope | `.a4-page` umschließt eine Modulseite: der Box-Sizing-Reset, Schrift, `[hidden]`, Links, der Fokusring bei `:focus-visible`. `.a4-standalone`: eine Seite, die ohne die Hauptkarte gezeichnet wird |
| Hilfsklassen | `.a4-sr-only`, `.a4-skip-link` (der Sprunglink des Rahmens, nur sichtbar, solange er fokussiert ist), `.a4-focus`, `.a4-muted`, `.a4-meta`, `.a4-help`, `.a4-intro`, `.a4-num`, `.a4-nowrap`, `.a4-wrap`, `.a4-plain`, `.a4-code`, `.a4-ok-text`, `.a4-warn-text`, `.a4-bad-text`, `.a4-middle`, `.a4-flag`, `.a4-end`, `.a4-inset`, `.a4-overflow-visible` |
| Überschriften | `.a4-h2`, `.a4-h3` |
| Layout | `.a4-section`, `.a4-section-head`, `.a4-title-row`, `.a4-row` (`.is-between`), `.a4-stack`, `.a4-grid` (`.is-narrow`, `.is-wide`) |
| Seitenkopf | `.a4-pagehead` > `.a4-pagehead-main` (`.a4-pagehead-pre`, `.a4-pagehead-title`, `.a4-pagehead-sub`) + `.a4-pagehead-actions` (rechts, in schmalen Fenstern unter dem Titel); der eine Titelstil jeder Seite (`h1.context-title`, die Knotenansicht und das Dashboard haben dieselbe Größe) |
| Brotkrumen | `ol.a4-crumbs` (`role="list"`) > `li` (`.is-middle`, `.is-fold`: Die mittleren Schritte klappen bis 600px zu „...“ zusammen, nur optisch, ein fokussierter Schritt erscheint wieder), Trennzeichen aus dem Stylesheet, der aktuelle Schritt mit `aria-current="page"`, innerhalb der Navigations-Landmark `#path` |
| Karte | `.a4-card` (`.is-soft`, `.is-selected`, `.is-attention`, `.is-warn`, `.is-bad`, `.is-ok`, `.is-info`, `.is-flush`, `.is-link`, `.is-busy`), `.a4-cards`, `.a4-card-head` (Titel, ein `<p>` als Untertitel, `.a4-card-actions` an seinem Ende), `.a4-card-title`, `.a4-card-body`, `.a4-card-foot` (die Aktionen der Karte auf einem Streifen an ihrem unteren Rand), `.a4-pick` (das Auswahl-Kontrollkästchen) |
| Zahlen, Fakten | `.a4-figures` > `.a4-figure` (`.is-attention`, `.is-bad`, `.is-ok`, `.is-warn`, `.is-info`, `.is-current`); `dl.a4-facts` (`.is-rows`) |
| Tag, Badge, Status | `.a4-tag`, `.a4-badge` (`.is-ok`, `.is-warn`, `.is-bad`, `.is-info`, `.is-accent`, `.is-mono`), `.a4-badges`; `.a4-status` (`.is-ok`, `.is-running`, `.is-warn`, `.is-bad`) mit `.a4-dot` (ein Status mit seinem Text); `.a4-mark` (`.is-visible`, `.is-hidden`, `.is-hidden-by-parent`, `.is-disabled`, `.is-locked`): eine Marke von 10 px, deren Form den Zustand sagt, benannt durch `role="img"` und `aria-label`, wenn sie allein steht, `aria-hidden` neben ihrem eigenen Text (`design:parts/visibility_status.tpl`). Zwei Klassen: Die Marke gestaltet nie `.a4-status` |
| Schaltflächen | `.a4-btn`, `.a4-btn-primary`, `.a4-btn-danger`, `.a4-btn-outline-danger`, `.a4-btn-ghost`, `.a4-btn-small`, `.a4-btn-large`, `.a4-btn-icon`; `.a4-actions` (eine Reihe davon) |
| Werkzeugleiste | `.a4-toolbar` (eine Aktionsleiste), `.is-bottom` (unter einer Liste), `.is-sticky` (bleibt am unteren Fensterrand) |
| Filterleiste | `.a4-filterbar` (ein Raster von Feldern über einer Liste), `.a4-filter-count` |
| Formulare | `.a4-field`, `.a4-field-wide`, `.a4-label`, `.a4-input` (`.is-auto`), `.a4-field-error`, `.a4-form` (`.is-wide`), `.a4-formrow` (Beschriftung neben dem Bedienelement), `.a4-searchrow`, `.a4-check`, `.a4-radios`, `.a4-chips` > `.a4-chip`, `.a4-inline-label` (eine Beschriftung um Text, der vorher einfach war: ein Inline-Radio oder -Kontrollkästchen mit seinem Text, oder die Wörter vor einem Bedienelement in einer Tabellenzelle; gezeichnet wie der umgebende Text) |
| Formulare | `.a4-field`, `.a4-field-wide`, `.a4-label`, `.a4-input` (`.is-auto`), `.a4-field-error`, `.a4-required-mark` + `.a4-required-hint` (Pflichtfelder), `.a4-error-summary` (die Zusammenfassung eines fehlgeschlagenen Speicherns), `.a4-form` (`.is-wide`), `.a4-formrow` (Beschriftung neben dem Bedienelement), `.a4-searchrow`, `.a4-check`, `.a4-radios`, `.a4-chips` > `.a4-chip` |
| Tabellen | `.a4-table-wrap` > `table.a4-table` (`.is-compact`), `tr.is-selected` |
| Unter einer Liste | `.a4-listfoot`, `.a4-sizes`, `.a4-pager`; `.a4-tabs` (Links, Schaltflächen, `span.current`, `a.current`, `.is-current`; `.is-segmented`: eine Pille mit `--a4-control-h` um jede Wahl, die aktuelle im Akzent getönt mit Akzentrand; eine gedrückte Schaltfläche ist `aria-pressed="true"`, ein aktueller Link `span.current`, `a.current` oder `.is-current` (mit `aria-current`); Links/Rechts und Pos1/Ende bewegen zwischen den Wahlmöglichkeiten (`admin4.js`), jede behält ihren Tab-Stopp; `.is-icons`: runde Icon-Schaltflächen, jede benannt durch Text in `.a4-sr-only` und ihr `title`), `.a4-count` |
| Leerer Zustand, Hinweise | `.a4-empty`; `.a4-notice` (`.is-ok`, `.is-warn`, `.is-bad`, `.is-info`: jeweils mit seinem Icon links vom Text, `.no-icon` lässt es weg; `.a4-notice-title` für eine fette erste Zeile) |
| Toasts | `.a4-toasts` (die beiden Live-Regionen des Rahmens, `#a4-toasts` höflich und `#a4-toasts-alert` bestimmt) und `.a4-toast` (`.is-ok`, `.is-warn`, `.is-bad`); nur hinzugefügt durch `window.a4Toast( message, kind, opener, delay )` aus `admin4.js`; eine Aktion in einem Toast (Rückgängig) ist eine Schaltfläche `.a4-toast-action` vor der Schließen-Schaltfläche, und ihr Toast bleibt `delay` ms |
| Ausklappen | `details.a4-fold` > `summary` + `.a4-fold-body` (`.is-bad`, `.is-warn`); `details.a4-confirm` (eine Bestätigung, die sich an Ort und Stelle öffnet) |
| Schritte | `ol.a4-steps` > `li.is-done` / `li.is-current` |
| Zeitleiste | `ol.a4-timeline` > `li` (`.is-ok`, `.is-warn`, `.is-bad`, `.is-info`) > `time.a4-timeline-time` (mit `datetime`) + `.a4-timeline-body` (`.a4-timeline-meta`, ein Zustands-Badge); für kurze chronologische Listen ohne Aktionen pro Zeile, die neuesten zuerst; der Punkt wiederholt den Zustand, den ein Badge in Worten sagt. Audit-Ereignisse: `parts/audit_timeline.tpl` (`events`, `show_reason`) |
| Code | `.a4-pre` (`.is-tall`), `.a4-kbd` |
| Mengen | `.a4-meter` > `span`, `.a4-bars` > Kinder, bemessen durch `.a4-pct-0` ... `.a4-pct-100` (`--a4-pct`) |
| Sprache | `.a4-lang` (der Kurzcode einer Inhaltssprache, aus `design:parts/language_badge.tpl`; `.is-current` für die angezeigte), `.a4-langs` (mehrere in einer Reihe) |
| Icons | `.a4-icon` mit einem `.a4-bi-<name>` (ein Bootstrap-Icon aus `share/icons/bootstrap-icons/svg/`, in `currentColor`; `.is-small`, `.is-medium`, `.is-large`); `.a4-icon-btn` (ein Link oder eine Schaltfläche, die nur ein Icon ist) |
| Sonstiges | `.a4-grip` (Ziehgriff), `.is-dragging` |

Eine Karte beginnt mit `.a4-card-head` (nicht mit `.a4-section-head`), legt den Rest in `.a4-card-body` und ihre
Schaltflächen in `.a4-card-foot`. Der Fuß und der bündige Kopf lesen den Innenabstand der Karte aus
`--a4-card-pad-x`/`--a4-card-pad-y` (mit der Abstandsskala, `--a4-space-4`/`--a4-space-3`, als Fallback). `.is-flush`
lässt eine Tabelle bis an die Ränder der Karte laufen (Kopf und Fuß behalten ihren Innenabstand, die Eckzellen der
Tabelle übernehmen den Radius der Karte). `.is-link` lässt die ganze Karte dem Link in ihrem `.a4-card-title` folgen:
Links, Schaltflächen und Felder im Body, im Fuß und in den Aktionen des Kopfs bleiben anklickbar, aber einfacher Text
unter der Link-Ebene lässt sich nicht markieren, daher enthält eine Link-Karte keinen Text, den man kopiert; wo der
Browser `:has()` kennt, liegt der Fokusring um die Karte, sonst um den Titel-Link. `.is-busy` arbeitet zusammen mit
`aria-busy="true"` am selben Element und verschleiert die Karte mit einem Spinner, während ihr Inhalt lädt oder
gespeichert wird; der Schleier hält nur den Zeiger auf, daher setzt das Skript, das ihn setzt, auch `inert` an der
Karte (oder deaktiviert ihre Bedienelemente) und entfernt beides danach. `aria-busy` allein kündigt nichts an:
Verwenden Sie `window.a4CardBusy( card, on, message )` (admin4.js), das `aria-busy` und `inert` gemeinsam setzt und
entfernt und die Meldung („Wird gespeichert ...“, dann das Ergebnis) in eine höfliche Live-Region außerhalb der Karte
setzt. Der Titel einer Link-Karte enthält genau einen Link, dessen Text der zugängliche Name der Karte ist; umschließen
Sie eine Karte nie mit `<a>`. `tests/tests/design/AdminCardsTest.php` prüft die Teile und dass keine Karte von admin4
mit einem Abschnittskopf beginnt.

Zustände sind Klassen `.is-*` an der Komponente. Jede Komponente ist eine Klasse plus höchstens ein Element oder ein
Zustand, daher gewinnt eine Modulregel mit einer Klasse mehr immer.

Beispiel, eine Listenseite:

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

## Fensterbreiten

admin4 wird bei 390, 820, 1180, 1440, 1920 und 2560 px geprüft (`AdminResponsiveTest`). Unter 576px ist das obere Menü
ein Hamburger-Panel in `--a4-card` unter der Zeile des Kopfbereichs, höchstens `100dvh` abzüglich des Kopfbereichs
(`100vh` als Fallback), der einzige Scroller (`overscroll-behavior: contain`), während die Seite dahinter gesperrt ist;
die Tabs der Seitenspalten sind versteckt, solange es offen ist, und der Kopfbereich ist eine Zeile von 56px. Darin:
zuerst die Suche, die Tabs als Raster von Kacheln (jeweils mindestens 44px), die Zeile des Benutzers, eine Schaltfläche
Schließen. Tab bleibt im Kopfbereich, solange es offen ist; Escape, Schließen, ein Tippen außerhalb und ein über 575px
verbreitertes Fenster schließen es (`AdminPhoneMenuTest`). Das Panel des Website-Menüs (`.a4-site-menu`) öffnet sich
unter dem Globus auf `--a4-z-popover`; auf einem Telefon ist es fest unter dem Kopfbereich auf `--a4-z-menu`, in der
Breite des Fensters abzüglich der Ränder. Ab 576px hat der Kopfbereich zwei Zeilen (48px für das Logo und die Werkzeuge,
40px für das Menü), ab 1280px eine Zeile von 56px (Logo, Menü, Suche, hell/dunkel, Benutzermenü, Abmelden); bei jeder
Breite ab 576px bleibt das Menü auf einer Zeile, und die Tabs, die nicht passen, kommen in seine Ausklappliste „Mehr“
(`admin4.js`; ohne das Skript scrollt die Zeile seitwärts). Abmelden ist eine Icon-Schaltfläche neben dem Benutzermenü
(`parts/a4_header_user.tpl`), und „/“ setzt den Fokus in die Suche des Kopfbereichs. `--header-height` wird an der
Wurzel gesetzt: die gezeichneten Höhen als Standardwerte, die gemessene durch `admin4.js` (`AdminHeaderTest`). Ab
1024px nimmt die linke Spalte Platz neben dem Inhalt ein, die rechte erst ab 1280px (darunter öffnet sie sich als
Schublade über dem Inhalt). `ezadmin_menubar.js` verwendet dieselben Umbruchpunkte: Eine Spalte unter ihrem
Umbruchpunkt beginnt geschlossen, öffnet sich allein (eine Spalte neben dem Inhalt tritt so lange beiseite), schließt
sich bei Escape mit dem Fokus zurück auf ihrem Tab und wird nie als Wahl für breite Fenster gespeichert
(`AdminSidebarDrawerTest`, der das Skript in node ausführt). Die Inhaltsspalte endet bei `--a4-content-max`. Auf einem
Telefon nehmen die Tabs des Knotens eine eigene Zeile ein und scrollen, der Tab-Umschalter steht darunter, und eine
breite Tabelle in einem Tab scrollt darin. Auf Touchscreens (`pointer: coarse`) erhalten die kleinen Bedienelemente des
Rahmens (die Knoten-Icons und Auf-/Zu-Pfeile des Baums, der Suchbereich, die Kopierschaltflächen, die Kontrollkästchen
der Seitenspalten) ein Ziel von 24px. Suchergebnisse zeigen den Pfad jedes Ergebnisses unter seinem Namen, und auf
einem Telefon steht auch der Typ darunter. In sehr breiten Fenstern wächst der Fließtext von 13 auf 14px
(`--a4-text-body`); die Größen von Bedienelementen und Beschriftungen behalten ihre Tokens.

Die Schublade der rechten Spalte:

| | Telefon (unter 576px) | Fenster in Tablet-Größe (576 bis 1279px, auch 960px bei 200 % Zoom) | Breites Fenster (ab 1280px) |
| --- | --- | --- | --- |
| Gezeigt als | Schublade über der Seite | Schublade über der Seite | Spalte neben dem Inhalt |
| Auf einer neuen Seite | geschlossen | wie der Benutzer sie verlassen hat: eZPreference `admin_right_drawer` (1 offen, 0 oder leer geschlossen) | eZPreference `admin_right_sidebar` |
| Gespeichert durch | nichts | einen Klick auf ihren Tab, Escape oder einen Klick auf den linken Tab, der sie schließt | einen Klick auf ihren Tab |

Tastatur: Das Öffnen einer Schublade mit ihrem Tab bewegt den Fokus auf das erste Bedienelement der Schublade; solange
sie offen ist, wandern Tab und Umschalt+Tab durch die Bedienelemente der Schublade und ihren Tab (Fokusfalle); Escape
schließt sie und setzt den Fokus zurück auf den Tab; der Tab meldet den Zustand in `aria-expanded`. Eine Seite, die mit
wiederhergestellter Schublade öffnet, bewegt den Fokus nicht. Für einen angemeldeten Benutzer ist jede dieser
Entscheidungen eine eZPreference, geschrieben über `window.a4Pref` (und an `<html>` durch
`parts/a4_preference_attributes.tpl`, sodass sie dem Benutzer in einen anderen Browser folgt); eine anonyme Seite hält
sie in localStorage. Die Schublade der linken Spalte (unter 1024px) wird nicht gespeichert.

## Griffe zum Ändern der Größe

admin4 hat vier Griffe, die eine Größe von Hand ändern: die Breite der linken Spalte (`.leftmenu-resize-handle`,
`ezadmin_menubar.js`), die Höhe der Tab-Box der Knotenansicht (`.a4-tabs-height-grip`, `parts/a4_tabs_height.tpl`,
`admin4.js`), die Höhe des Debug-Panels (`expdebugbar.js`) und das Details-Panel von exp_adminui. Sie verhalten sich
gleich, und ein neuer tut dasselbe:

- ein fokussierbarer Trenner (`role="separator"`, `tabindex="0"`, `aria-orientation`, `aria-valuenow`,
  `aria-valuemin` und `aria-valuemax` in Pixeln, bei einer Größenänderung des Fensters aktualisiert), mit einem Namen
  und einem Titel, der sagt, wie man ihn verwendet;
- gezogen mit Pointer-Ereignissen (`touch-action: none`, Pointer Capture), sodass Maus, Finger und Stift funktionieren;
- die Pfeiltasten bewegen den Rand um 16px, Umschalt+Pfeil um 64px, Pos1 und Ende gehen zur kleinsten und größten
  Größe, und ein Doppelklick stellt den Standard wieder her (die linke Spalte hat dafür stattdessen ihre
  Größen-Schaltflächen);
- zwischen Grenzen gehalten, die dem Fenster folgen, sodass eine auf einem großen Bildschirm gespeicherte Größe auch
  auf einen kleinen passt;
- einmal gespeichert, am Ende eines Ziehens oder nach einer kurzen Pause nach der letzten Taste, über `window.a4Pref`
  (eine eZPreference für einen angemeldeten Benutzer, an `<html>` außerhalb jedes cache-block gelesen, sodass die Seite
  ohne Sprung in der gespeicherten Größe gezeichnet wird); die Rückkehr zum Standard leert die Einstellung, statt den
  Standard zu speichern;
- die Größe erreicht das CSS als Custom Property an `<html>`, gesetzt vom Skript (`--left-sidebar-width`,
  `--a4-node-tabs-height`), nie als `style=""` in einem Template.

Die Tab-Box der Knotenansicht: die eZPreference `admin_node_preview_height` (Pixel, eine Höhe für jeden Knoten),
angewendet erst ab 576px, zwischen 10em und 60em und höchstens 85 % des Fensters. Die Schaltfläche „Standardgröße“
neben dem Griff (Icon `arrow-counterclockwise`) erscheint nur, solange eine eigene Höhe des Benutzers gesetzt ist.
`AdminNodeTabsHeightTest` prüft das.

## Alte Bedienelemente und die Seitenspalten

Die Modul-Templates des Kernels schreiben noch `input.button`, `input.defaultbutton`, `input.button-disabled`,
`button.button` sowie Auswahllisten und Textfelder ohne Klasse. Innerhalb von `#page` gibt `admin4.css` ihnen die Größe
der Komponenten `.a4-btn` und der Aktionszeile des Knotens: `min-height: var(--a4-control-h)` (32px), die kleine Stufe
der Schriftskala und die Abstands-Tokens; eine Auswahlliste erhält den Rand des Feldes und einen in `--a4-muted`
gezeichneten Winkel, und Forced Colors erhalten die native Auswahlliste zurück. Die Selektoren behalten das Gewicht der
Grundregeln (Element plus Klasse, der Scope innerhalb von `:where()`), sodass eine Regel mit einer Klasse mehr (die
Aktionszeile des Knotens, die Bearbeitungsleisten, `.a4-input`, ein eigenes Bedienelement einer Erweiterung) immer
gewinnt; eine Auswahlliste oder ein Feld mit einer eigenen Klasse wird nicht angefasst. Neues Markup verwendet weiterhin
`.a4-btn` und `.a4-field`.

Seitenspalten: Blöcke werden durch Abstand getrennt, nicht durch eine Linie: Eine Überschrift, die auf einen Block
folgt, steht `--a4-gutter` (24 px) darunter, gleich wie das Menü-Template verschachtelt (kein Stylesheet von admin4
zeichnet eine Linie über einem `.box-header`); die rechte Spalte ist `--a4-right-width` (16rem) breit, sodass ihre
Karte neben dem Rand von 24px und dem Abstand 208px behält; Blocktitel haben eine Größe (1,25em der Schrift der
Spalte, mit Silbentrennung); Linklisten haben keine Aufzählungszeichen. Jedes linke Menü von admin4 endet mit
`design:parts/left_width_control.tpl` (drei Icon-Schaltflächen in einem POST-Formular, die aktuelle Breite gedrückt),
und ein Baummenü zeigt seinen Link zum Papierkorb über `design:parts/left_trash.tpl`: Binden Sie sie ein, kopieren Sie
nie ihr Markup (`AdminLegacyControlsTest`).

Der Block Benutzereinstellungen der rechten Spalte (`toolbar/full/admin_preferences.tpl`) zeigt jede
An/Aus-Einstellung als ihren Namen und die Tabs des Designs (`ul.tabs`), nicht als segmentierten Schaltflächen-Umschalter.
Die beiden Zustände sind die Schaltflächen eines POST-Formulars an `user/preferences/set` (`Key`, `Value`,
`RedirectURIAfterSet`), wie der Breitenschalter der linken Spalte, sodass eine Einstellung nur von dieser Website aus
geschrieben wird (ezformtoken fügt sein Token hinzu) und nie über einen GET-Link: Der aktuelle Zustand ist die
gedrückte Schaltfläche (`<li class="selected"><button ... aria-pressed="true">`), gezeichnet wie der aktuelle Tab der
Knotenansicht (die Akzenttönung mit dem Balken darunter), der andere eine unauffällige Schaltfläche. Ein Bildschirmleser
sagt den Namen der Einstellung, den Zustand und „gedrückt“. Ein `<div class="a4-pref-tabs" role="group"
aria-labelledby="...">` um die Liste benennt sie nach der Beschriftung der Einstellung (`.a4-pref-label`); die Liste
behält ihre Listenrolle. In der Spalte sitzen die Tabs nebeneinander, mindestens 24px hoch, und unter Forced Colors
kennzeichnet ein Umriss in `Highlight` den aktuellen (für jedes `ul.tabs`). Eine neue An/Aus-Einstellung in der rechten
Spalte ist ein weiterer Eintrag in der Liste des Templates (`AdminLegacyControlsTest`).

## Die Schaltfläche Hochladen der Knotenansicht

Die Aktionen der Knotenansicht haben eine Schaltfläche Hochladen zwischen Bearbeiten und Verschieben (seit dem
2026-10-15). Sie ist ein Link, `<a class="a4-btn a4-node-upload">` mit dem Bootstrap-Icon `.a4-bi-upload` und dem Text
„Upload“ (ger-DE „Hochladen“), zur Upload-Seite von ezmultiupload für den Knoten, `ezmultiupload/upload/<node id>`. Ein
Template zeichnet sie, `node/view/upload_button.tpl`, in identischen Kopien in `design/admin` und `design/admin4`
(admin4 ist in sich geschlossen; `admin4l` fällt auf admin4 zurück), eingebunden von `node/view/full.tpl` und
`node/view/a4_full_body.tpl`; das Icon steht in `design.ini [AdminDesignSettings] IconList[]`. exp_adminui setzt
denselben Link an die erste Stelle seines Menüs „...“ (`window_preview_toolbar.tpl`).

Sie erscheint nur, wenn das Hochladen dort für den aktuellen Benutzer möglich ist: Das Modul `ezmultiupload/upload` ist
im Siteaccess eingeschaltet und erreichbar (`ezmodule()`), und
`fetch( 'ezmultiupload', 'can_upload', hash( 'node_id', $node.node_id ) )` ist wahr. Dieser Fetch fragt
`expMultiuploadService::canUploadAt()`, die eine Prüfung für Links zur Upload-Seite: die Richtlinie
`ezmultiupload/upload`, Zugriff für read und `content/create` am Knoten und ein Container (oder eine Klasse oder ein
Knoten aus `ezmultiupload.ini [MultiUploadSettings] AvailableClasses[]` / `AvailableSubtreeNode[]`). Die Rechte zum
Anlegen pro Klasse für jede Datei prüft die Upload-Seite selbst. Ein Entwurf oder ein archiviertes Objekt zeigt die
Schaltfläche nie, ebenso wenig die Bearbeitungsseiten, die die Knotenansicht nicht einbinden. Die Knotenansicht wird
pro Rollensatz im View-Cache gehalten, wie Bearbeiten und Verschieben.

admin4 bemisst den Link wie die anderen Aktionen (`--a4-control-h`) und nimmt ihn in die Regeln für schmale Breiten von
`.a4-node-actions` auf (`admin4.css`); die klassische Leiste von `design/admin` hält ihn so hoch wie ihre alten
Schaltflächen (`modules/content.css`). Tests: `tests/tests/design/AdminNodeUploadButtonTest.php`.

## Vorschau einer Version

`content/view/versionview.tpl` zeichnet den Geräteumschalter der Vorschau mit den Komponenten (`.a4-tabs.is-segmented`,
Schaltflächen mit `aria-pressed`, `.a4-icon`), und `modules/content.css` fügt hinzu, was sie nicht können:

| Klasse | Enthält |
|---|---|
| `.a4-content-preview-bar` | die Gerätepille, den Querformat-Schalter und `.a4-content-preview-size` (Größe und Skalierung); ein Container: Unter 640 px werden die Namen (`.a4-content-preview-device-name`) optisch versteckt, und die Schaltflächen sind runde Icons |
| `.a4-content-preview-stage` | das `.mainobject-window` um den iframe; `.is-device` (vom Skript gesetzt) zentriert den Rahmen auf `--a4-soft` |
| `.a4-content-preview-device` | der Rahmen: Das Skript setzt seine Größe (die Größe des Geräts mal die Skalierung, `box-sizing: content-box`), der iframe darin hat die Größe des Geräts und `transform: scale()` |

Das Verhalten ist ein Teil von `admin4.js` (es startet, wenn die Seite `#a4-content-preview-bar` und
`#a4-content-preview-stage` hat); Gerät und Ausrichtung sind die eZPreferences `admin_preview_device` und
`admin_preview_landscape`, gehalten über `window.a4Pref` (`AdminPreviewDevicesTest`, `AdminPreferencesTest`). Ein
Website-Design, das `versionview.tpl` überschreibt, behält seine eigene Vorschau; um den Umschalter zu übernehmen,
kopieren Sie die Leiste und die Bühne aus dem Template von admin4.

## Bearbeitungsformulare

Die Bearbeitungs-Templates der Datentypen liegen in `design/standard` und schreiben noch das klassische Markup:
`input.box`, `input.button`, `input.button-disabled`, bloße Kontrollkästchen, `<label>` über einem Feld, `<fieldset>`
und `<legend>`. Skripte suchen nach einigen dieser Klassen (der AJAX-Uploader und die Beziehungsliste tauschen `button`
und `button-disabled`), daher bleiben sie. `modules/content.css` zeichnet sie stattdessen als die Komponenten, innerhalb
von zwei Scopes:

| Scope | Gesetzt von | Enthält |
|---|---|---|
| `.a4-content-attr` | `content/edit_attribute.tpl`, an jedem Attribut (content/edit, translate, multiedit, user/register, der Template-Look-Editor) | die Bedienelemente des Attributs |
| `.a4-content-edit` | das `<form id="editform">` von `content/edit.tpl` | die linke Spalte und den Kasten der Beziehungen der Bearbeitungsseite (nur Schaltflächen) |

| Markup | Gezeichnet als |
|---|---|
| ein Textfeld, eine Auswahlliste oder eine Textarea (beliebige Klasse) | `.a4-input`: `--a4-control-h`, `--a4-text-m`, `--a4-field-line`, `--a4-radius-s`, der Akzentrand und -ring beim Fokus; eine einzeilige Auswahlliste erhält den Winkel der alten Auswahllisten; ein Feld mit `size` behält seine Breite, `.box` ist volle Breite, `.halfbox` behält seine halbe Breite |
| `input.button`, `input.button-disabled`, `button.button` | `.a4-btn`: umrandet, beim Überfahren Akzentrand und -text, deaktiviert mit `opacity: .55` verblasst (keine Schraffur) |
| `input.defaultbutton` | `.a4-btn-primary` |
| ein Kontrollkästchen oder Radio | wie in `.a4-check`: 18 px in `--a4-accent-strong`; ein `<label>` um eines und seinen Text ist eine Zeile, kein fetter Block |
| `input[type=file]` | seine Schaltfläche als `.a4-btn`, sein Text gedämpft |
| ein `<label>` eines Teils (Jahr, URL, Benutzername ...) | `.a4-label` (12 px, halbfett, über seinem Feld) |

Der eigene Name des Attributs ist ein `label` oder `legend` der Klasse `.a4-content-attr-label` (13 px, halbfett, ohne
abschließenden Doppelpunkt), und das Fieldset einer gruppierten Eingabe hat die Klasse `.a4-content-attr-group`: kein
eigener Rand, Hintergrund oder Innenabstand. Die Selektoren sind die Scope-Klasse plus das Element
(`.a4-content-attr :is(input, select, textarea)`), sodass sie `core.css` (`input.box`, `label`, `fieldset`) durch
späteres Laden schlagen, während der Rand für ungültige Eingaben (`.a4-content-attr input[aria-invalid="true"]`), die
klebenden Schaltflächenleisten (`#controlbar-top ...`) und jede Regel mit einer Klasse mehr weiterhin gewinnen. Das
Datentyp-Template einer Erweiterung, das ein eigenes Aussehen möchte, gibt seinem Bedienelement eine solche Klasse.

admin4 trägt die Bearbeitungs-Templates von `ezdate`, `ezdatetime`, `eztime`, `ezurl`, `ezprice`, `ezimage`,
`ezbinaryfile`, `ezoption`, `ezuser`, `ezmedia`, `ezmultioption`, `ezrangeoption`, `ezmultiprice`, `ezinisetting`,
`ezpackage` und `ezobjectrelationlist` (in `templates/content/datatype/edit/`): Die Teile eines Attributs stehen in
einer Reihe (`.a4-content-parts`, Datum und Uhrzeit von `ezdatetime` in `.a4-content-datetime`), jedes mit seiner
Beschriftung über dem Feld und ohne Doppelpunkt; eine Überschrift ohne eigenes Bedienelement ist ein `p.a4-label`; der
Kalender ist ein `button.a4-btn.a4-btn-icon` mit `.a4-icon.a4-bi-calendar3`; keine schwebenden `div.element`, kein
`div.break` und kein Abstand mit `&nbsp;`. Ihre Listen benennen ihre Felder nach ihren Spaltenköpfen (`th scope="col"`
mit einer ID, `aria-labelledby`), ein Kontrollkästchen zum Entfernen nach dem Objekt seiner Zeile oder nach der
Aktion, ein einzelnes Bedienelement ohne eigene Beschriftung nach dem Namen des Attributs
(`aria-labelledby="ezcca-name-<id>"` aus `edit_attribute.tpl`); Radio- und Kontrollkästchen-Listen der Beziehungsliste
sind Zeilen `.a4-check`, ihr Bedienelement zum Umkehren der Auswahl ist ein `.a4-icon-btn`, und leere Listen sind
`.a4-empty`. Jede Kopie sendet dieselben Feldnamen und IDs und behält die Template-Blöcke und die Tabellenstruktur von
design/standard (`AdminContentEditFormsTest`), daher muss eine Korrektur dort von Hand übertragen werden. Die anderen
Datentypen erhalten das Aussehen allein über das Stylesheet. `theme/rounded.css` von admin4 gestaltet `.datepicker-icon`
nicht mehr (der Kalender ist jetzt ein `.a4-btn`); die Klasse bleibt in `design/admin`, dessen Kalender noch das Bild
ist. `tests/tests/design/AdminContentEditFormsTest.php` prüft die Regeln und die Templates.

Der Engine-Umschalter von ezoe unter einem Rich-Text-Feld (`ezxmltext_ezoe_engine_switch.tpl`, `EngineSwitch=enabled`)
schreibt neutrales Markup: `div.ezoe-engine-switch` (`role="group"`, benannt durch seine Beschriftung
`span.ezoe-engine-switch-label` „Editor“), die verwendete Engine als gedrückter Umschalter
`button.ezoe-engine-switch-option.current` (`type="button"`, `aria-pressed="true"`), jede andere Engine als
`button.button.ezoe-engine-switch-option` (`aria-pressed="false"`), der
`CustomActionButton[<attribute id>_switch_engine_<id>]` sendet. Das `span.ezoe-engine-switch-options` der Engines trägt
außerdem `a4-tabs is-segmented`, sodass jedes Administrationsdesign es als die eine segmentierte Pille von
`components.css` zeichnet, wie den Breitenschalter des linken Menüs: die Spur, die verwendete Engine im Akzent getönt
mit Akzentrand, der Fokusring, Forced Colors und die Pfeiltasten von `admin4.js`. `modules/content.css` platziert es nur
(die Beschriftung gedämpft neben der Pille auf derselben Mittellinie, linksbündig unter dem Feld wie seine anderen
Bedienelemente und seine Beschriftung, `--a4-space-2` unter der Statusleiste des Editors; auf einem schmalen Bildschirm
rückt die Pille als ein Stück unter die Beschriftung) und hält das Überfahren der Pille über dem stärkeren Überfahren
alter Schaltflächen dieser Datei (`AdminContentEditFormsTest`).

Die Rich-Text-Editoren (TinyMCE 3 und 8) zeichnen den Text eines Feldes in einem iframe mit dem hellen
Inhalts-Stylesheet von ezoe. Das Bearbeitungsformular nennt `components.css` und `editor_content.css` in
`data-a4-editor-css`; `admin4.js` fügt beide dem Dokument jedes Editors hinzu und kopiert `html[data-a4-theme]`
dorthin, wenn der Editor bereit ist und wenn sich der Modus ändert, sodass Text, Links, Literal-Blöcke, eigene Tags und
Einbettungen in beiden Modi die Tokens übernehmen. Jede Regel von `editor_content.css` ist auf `html[data-a4-theme]`
beschränkt und tut ohne das Skript nichts. Die Werkzeugleiste des Editors (der TinyMCE-Skin) gehört nicht dazu.

## Dunkler Modus

Der Modus von admin4 ist `html[data-a4-theme="light"|"dark"]`, gesetzt, bevor die Seite gezeichnet wird, durch das
Skript am Anfang von `page_head_style.tpl` aus der Wahl des Benutzers (`window.a4Pref`: die eZPreference
`admin_color_mode`, auf einer anonymen Seite der Browser); der Umschalter im Kopfbereich wechselt ihn. Jede gemerkte
Wahl des Rahmens (Modus, Seitenspalten, Menügruppen) geht über `window.a4Pref`; schreiben Sie nie `localStorage` aus
einem Funktionsskript (`AdminPreferencesTest`). Seit dem 2026-10-14 sind die Spalten des Rahmens auch im dunklen Modus
dunkel (`--a4-card-bg` folgt `--a4-surface`), daher muss alles, was einen eigenen Hintergrund malt, ihn aus einem Token
nehmen. Eine Komponenten- oder Modulregel braucht nie eine eigene Regel `html[data-a4-theme="dark"]`: Die Tokens
wechseln.

## Ausnahmen

Inline-CSS, das bleibt, jeweils dort dokumentiert, wo es steht:

| Wo | Warum |
|---|---|
| admin4 `page_head_style.tpl`: das Inline-Skript am Anfang | setzt `data-a4-theme`, bevor irgendetwas gezeichnet wird (eine hell gezeichnete und dann umgeschaltete Seite würde aufblitzen), und die von Hand gezogene Breite der linken Spalte aus `data-a4-left-width` an `<html>`. Ein Skript, kein CSS. |
| admin3 `pagelayout.tpl`: ein zweizeiliges Skript | die von Hand gezogene Breite der linken Spalte, wie oben (admin3 hat kein Theme-Skript) |
| `design/admin/templates/pagelayout.tpl`: ein `<style>`-Block | die Spaltenbreiten aus den Einstellungen des Redakteurs, in em aus `menu.ini [LeftMenuSettings] MenuWidth` oder einer gezogenen Breite, die ein Stylesheet nicht kennen kann, nötig, bevor die Seite gezeichnet wird |

Die voreingestellten Größen der linken Spalte (klein, mittel, groß) sind Regeln `html[data-a4-left-size]` in
`pagelayout.css`. Sie setzen nur die Breite (`--left-sidebar-width`): Der Text der Spalte behält bei jeder Breite die
Größe des Fließtexts (`AdminLeftColumnFontTest`).

## In der CI geprüft

`tests/tests/design/AdminStylingRulesTest.php` (Testsuite `design`, ohne Datenbank) schlägt fehl, wenn:

- ein Template von `admin4`, `admin4l`, `admin3` oder `admin` (in `templates/` oder `override/templates/`) einen
  `<style>`-Block oder ein Attribut `style=""` hat (Template-Kommentare werden nicht gelesen), außerhalb der Ausnahmen
  oben und der `ALLOWLIST` von Templates, die noch umgestellt werden. Die Allowlist schrumpft nur: Eine aufgeführte
  Datei ohne Inline-CSS schlägt fehl, bis sie entfernt ist. Sie ist leer, wenn die Umstellung abgeschlossen ist. Ein
  Design auf der Liste des Tests, das nicht existiert, lässt den Test fehlschlagen, statt übersprungen zu werden;
- eine Regel in `stylesheets/modules/*.css` (admin4 und admin) ein Farbliteral hat, einen Radius, der kein Token, `0`
  oder `50%` ist, eine Abstandslänge, die kein Token ist (`0`, `1px`, `2px`, `auto` und `calc()` von Tokens sind
  erlaubt), eine eigene Schriftfamilie oder Schriftgröße in Pixeln, eine eigene Custom Property oder einen Selektor ohne
  Klasse `.a4-`;
- ein Farb-Token in `components.css` keinen dunklen Wert hat oder die Kopien von admin und admin4 sich unterscheiden;
- `admin4.css` oder eines der Seiten-Stylesheets von admin4 (`ezinfo.css`, `packagecompare.css`,
  `visual-templateview.css`) ein Farbliteral außerhalb einer Token-Definition hat;
- eines der Basis-Stylesheets von admin4 (`core.css`, `content.css`, `pagelayout.css`, `theme/rounded.css`,
  `theme/admin_datatable.css`, `theme/modalwindow.css`) ein Farbliteral außerhalb einer Token-Definition hat. Ein
  absichtlich behaltenes altes Literal käme in `BASE_COLOUR_ALLOWLIST`, die nur schrumpft; sie ist leer. Eine Regel,
  die admin4.css heute überschreibt, nimmt trotzdem einen Token, damit der nächste Selektor, der am Override vorbeirutscht,
  keine alte Farbe zeigen kann;
- eine Regel `:focus` oder `:focus-visible` eines Stylesheets von admin4 den Umriss entfernt (`none`, `hidden` oder eine
  Breite von `0`, in beliebiger Reihenfolge der Kurzschreibweise; `:focus:not(:focus-visible)` und `:focus-within`
  ausgenommen). Der globale Ring (`outline: 3px solid var(--a4-ring)`, Abstand 2px) ist der Fokusindikator. Ein Feld des
  Rahmens, das seinen Ring als `box-shadow` zeichnet (außerhalb von `.a4-page`), setzt `outline: 2px solid transparent`
  statt `none`, was der Modus Forced Colors dort malt, wo er den Schatten weglässt; innerhalb von `.a4-page` gilt
  ohnehin der eigene Ring der Komponenten. Die Prüfung liest nur Regeln, deren Selektor den Fokus nennt: Eine einfache
  Regel, die den globalen Ring übertrumpft (eine ID wie `#logintext { outline: none }`), wird nicht gesehen; geben Sie
  einer solchen Regel daher eine Regel `:focus`, die den Umriss wiederherstellt;
- `components.css` das `color-scheme` eines Themes, seinen Block für Forced Colors oder Touch verliert, `admin4.css`
  seine Regel `prefers-reduced-motion`, oder ein Stylesheet von admin4 `all` animiert (nennen Sie stattdessen die
  Eigenschaften);
- ein Zustand eines Modul-Stylesheets (`.is-current`, `.is-selected`, `.is-moved`, `.is-dirty`, `[aria-pressed="true"]`,
  `:checked`), der sich nur durch Rand, Hintergrund oder box-shadow unterscheidet, kein `outline: 2px solid Highlight`
  im Block `@media (forced-colors: active)` der Datei hat: Forced Colors lassen alle drei weg
  (`AdminBaseStylesheetsTest::testModuleStatesKeepAnOutlineInForcedColors`);
- ein Basis-Stylesheet eine Regel für `html[data-a4-theme]` hat (die Tokens wechseln; ein Fokusring ist in beiden Modi
  `var(--a4-ring)`) oder einen Überrest mit Herstellerpräfix oder nur für IE (Übergänge, Schatten und Verläufe mit
  `-moz-`/`-ms-`/`-o-`, `filter: alpha()`, `zoom: 1`, `linear-gradient(top, …)`) (`AdminBaseStylesheetsTest`);
- `components.css` oder `admin4.css` eine Schriftgröße in Pixeln setzt oder eine Abstandslänge, die kein Token
  `--a4-space-*` ist (`1px` und `2px` ausgenommen; eine Layoutlänge ist ein eigener Token);
- eine in `AdminComponentCSSFileList` genannte Datei fehlt.

Kein Text ist auf einer Seite von admin4 kleiner als 12px. Die Basis- und Seiten-Stylesheets (core.css,
pagelayout.css, content.css, die Seiten Info, Paket und Template) bemessen Text in em des Bodys, den core.css auf
`var(--a4-text-s)` (13px) setzt; eine em-Größe, die unter 12px landen würde, ist stattdessen `var(--a4-text-xs)`
(`AdminStylingRulesTest::testBaseAndPageStylesheetsDrawNoTextBelowTwelvePixels`).

Ziele sind mindestens 24 x 24px groß (WCAG 2.5.8). Wo das gezeichnete Bedienelement kleiner bleibt, nimmt ein
unsichtbares `::after` darum herum den Klick (der Umschalter des Suchbereichs); wo das einen Nachbarn verdecken würde
(Auf/Zu des Inhaltsbaums, 20 x 20px) oder das Bedienelement ein natives Kontrollkästchen ist (18px), gilt die
Abstandsausnahme von 2.5.8: Halten Sie Zeilen mindestens 24px auseinander. Eine Pixellänge, die eine Layoutlänge und
kein Abstand ist (der Platz des Passwortfelds für seinen Umschalter), ist ein eigener Token mit einem dunklen Wert
(`--a4-pw-toggle-room`); ein Versatz ist ein calc() der Abstands-Tokens. Der Test führt keine Ausnahmeliste.

Der Tab-Umschalter der Knotenansicht (`.tab-block .show-hide-tabs`, die IDs `#maincontent-show` / `#maincontent-hide`
oder `aria-pressed` an einer Schaltfläche) ist ein Schalter von 36 x 24px mit der sichtbaren Beschriftung
`.a4-tabs-switch-label` („Tabs“); beide schweben am rechten Ende der Tab-Zeile. Gestalten Sie ihn über diese Hooks,
nicht über das Element, damit Link und Schaltfläche gleich aussehen.

Modi, nach denen der Browser fragt: Jedes Theme setzt `color-scheme` (Scrollleisten, Auswahl-Popups und
Datumsauswahlen folgen); Forced Colors (Windows-Kontrastmodus) erhalten Ränder, die aktuellen und ausgewählten Zustände
und den Fokus in Systemfarben, und als Füllungen gezeichnete Daten (Punkte, Messbalken, Balken) behalten ihre Farben
(`forced-color-adjust: none`); `prefers-reduced-motion: reduce` beendet jeden Übergang und jede Animation sofort; auf
einem Touchscreen (`pointer: coarse`) verwenden Felder `--a4-text-l` (16px), damit ein Telefon beim Fokus nicht
hineinzoomt.

`tests/tests/design/AdminTokenContrastTest.php` berechnet den WCAG-Kontrast der Token-Paare in beiden Modi:
Text-Tokens auf Karte, Fläche und soft 4,5:1, der Text jedes Zustands auf seiner eigenen Tönung 4,5:1, der Text
gefüllter Schaltflächen (auch überfahren) 4,5:1, der Ring und der Feldrand 3:1 auf jeder Fläche und der Seite, die
Diagrammfarben 3:1 auf einer Karte, die Füllung eines Messbalkens auf seiner Spur 3:1 (`.a4-meter` füllt mit
`--a4-accent-text`: `--a4-accent` lag auf der hellen Spur bei 2,9:1, `--a4-accent-strong` auf der dunklen bei 2,8:1).

Drucken: `admin4.css` hat einen Block `@media print` (der Inhalt in voller Breite, kein Rahmen, keine Schatten), und das
Skript im Kopf schaltet `html[data-a4-theme]` vor dem Drucken auf hell, sodass ein Druckstil keine eigenen Farben
braucht.

Schwächen Sie nie den Test ab und fügen Sie keinen Eintrag zur Allowlist hinzu, damit eine Änderung besteht;
verschieben Sie das CSS.

```bash
php vendor/bin/phpunit tests/tests/design/AdminStylingRulesTest.php
```

## Nach einer Änderung an einem Stylesheet

```bash
./console exp:velocity deploy --packer --allow-root-user
```

Neue Templates brauchen außerdem das Leeren des Caches template-override (der Deploy erledigt das). Ein Browser, der noch
das alte Aussehen zeigt, braucht ein hartes Neuladen (Strg+Umschalt+R); der Name der gepackten Datei trägt die
Änderungszeit.

## Checkliste für das Code-Review

Für jede Änderung an einem Template oder Stylesheet in einem Administrationsdesign oder im Admin-Design einer
Erweiterung:

- [ ] Kein `<style>`-Block und kein Attribut `style=""` wurde hinzugefügt (auch nicht in einer JavaScript-Zeichenkette,
      die Markup schreibt).
- [ ] Die Seite verwendet die gemeinsamen Komponenten `.a4-*`; nichts darin kopiert eine Komponente unter einem anderen
      Namen.
- [ ] Neues CSS steht in `stylesheets/modules/<group>.css` (Kern) oder im eigenen Stylesheet der Erweiterung, mit
      Klassennamen `.a4-<module>-*` (oder denen der Erweiterung), und enthält nur, was die Komponenten nicht können.
- [ ] Icons sind `.a4-icon` mit einer Klasse `.a4-bi-*`: kein GIF- oder PNG-Icon über `ezimage`, kein Inline-`<svg>`,
      kein `<input type="image">` (`tests/tests/design/AdminIconsTest.php` prüft admin4).
- [ ] Ein Popup oder Menü verwendet das Kontextmenü wieder (`ezpopupmenu.js`, `div.popupmenu`): Es erhält dort die
      Menürollen und die Tastaturbedienung; nichts öffnet ein Menü, das nur eine Maus erreicht
      (`AdminContextMenuTest`).
- [ ] Ein Bedienelement ohne sichtbaren Text hat einen Namen (`aria-label` oder Text in `.a4-sr-only` darin); eine
      Kopfzelle hat `scope`; eine neue Spalte des Rahmens behält ihre Landmark-Rolle
      (`tests/tests/design/AdminLandmarksTest.php`).
- [ ] Jedes Formularfeld hat einen Namen: ein `<label for>` auf seine ID, ein `<label>` darum herum oder
      `aria-labelledby` auf angezeigten Text (ein Spaltenkopf, der Name der Zeile, eine Kartenüberschrift);
      `aria-label` nur, wo kein Text angezeigt wird. Ein `title` oder ein `placeholder` ist kein Name. Verwenden Sie um
      einfachen Text `<label class="a4-inline-label">`, das das Aussehen des Textes behält. Ein Feld mit einer
      Beschriftung, die Text hat, erhält kein `aria-label` daneben: Es würde die Wörter der Beschriftung ersetzen
      (WCAG 2.5.3) (`tests/tests/design/AdminFormLabelsTest.php` prüft admin4).
- [ ] Farben, Radius, Abstände und Schriften sind Tokens `var(--a4-*)`: kein `#hex`, `rgb()`, keine benannte Farbe, kein
      Literal für Radius oder Abstand in px und keine lokalen Custom Properties.
- [ ] Ein neuer Token steht in `components.css` mit einem hellen und einem dunklen Wert und ist auf dieser Seite
      dokumentiert.
- [ ] Ein z-index ist ein Token `--a4-z-*` (oder 0/1/-1 innerhalb einer Komponente).
- [ ] Ein Zustand, der nur durch Rand, Hintergrund oder Schatten gezeigt wird (aktuell, ausgewählt, gedrückt), hat ein
      `outline: 2px solid Highlight` im Block `@media (forced-colors: active)` der Datei.
- [ ] Ein Pflichtfeld zeigt `.a4-required-mark` (`aria-hidden`) mit dem Wort in `.a4-sr-only`, und sein Bedienelement
      ist `aria-required`; ein ungültiges Feld zeigt seine Meldung (`.a4-field-error` mit einer ID), und sein
      Bedienelement ist `aria-invalid` mit dieser ID in `aria-describedby`
      (`tests/tests/design/AdminFormErrorsTest.php`).
- [ ] Eine neue Datentabelle ist ein `table.list` oder eine `.a4-table` (Zebra-Zeilen, Überfahren); Bedienelemente in
      einer Zeile teilen sich `--a4-control-h`.
- [ ] Ein Hinweis, der das Ergebnis einer Aktion meldet, hat eine Rolle: `role="alert"` für einen Fehler
      (`.a4-notice.is-bad`, `div.message-error`), `role="status"` für den Rest (eine bereits auf der Seite vorhandene
      Status-Region wird beim Laden meist nicht angekündigt, aber Bildschirmleser verhalten sich unterschiedlich); ein
      Hinweis, der einen dauerhaften Zustand erklärt, hat keine. Kein von Hand geschriebenes Zeichen („!“, ein Haken)
      am Anfang eines Hinweises: Die Zustandsklasse zeichnet das Icon. Die Rückmeldung einer Aktion, die auf der Seite
      ohne Neuladen erledigt wird (kopiert, per Ajax gespeichert), ist ein Toast (`window.a4Toast`), kein Hinweis, der
      erscheint; sie nimmt nie den Fokus (`AdminNoticesTest`).
- [ ] Ein neuer Link im linken Menü (menu.ini `Links[]` eines Abschnitts mit `Groups[]`) ist in eine Gruppe eingeordnet
      (`GroupLinks_<group>[]`, bei einem Link einer Erweiterung in deren eigener menu.ini.append.php: `GroupLinks_ai[]`,
      `GroupLinks_updates[]`, ...); sonst erscheint er unter „Mehr“ (`AdminLeftMenuGroupsTest` prüft
      Leftmenu_setup).
- [ ] Ein neues Modul-Stylesheet ist in `AdminComponentCSSFileList` aufgeführt und existiert in `design/admin4` und
      `design/admin`.
- [ ] Die Seite wurde im hellen und im dunklen Modus bei 1440 px und bei 960 px mit Skalierung 2 geprüft: keine helle
      Karte auf der dunklen Seite, Textkontrast mindestens 4,5:1, kein horizontaler Überlauf, keine Fehler in der
      Konsole.
- [ ] `php vendor/bin/phpunit tests/tests/design/AdminStylingRulesTest.php` läuft durch, und die Allowlist ist nur
      geschrumpft.
- [ ] Mit `--packer` bereitgestellt; keine Versionsabfrage an einen Link angehängt.

## Verwandte Seiten

- [Das Design admin4](../../../eng-us/features/6.0/admin4-design.md)
- [admin4l: die aus Layouts gebaute Admin-Seite](admin4l.md)
- [Richtlinien für Beitragende](../../../../CONTRIBUTING.md)
