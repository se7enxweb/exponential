# Sprachen mit Schreibrichtung von rechts nach links in der Administrationsoberfläche

Seit 6.0.15 (#521). Baut auf der Wahl der [Sprache der Oberfläche](ui-umschalter-sprachen.md) auf.

Ist die Sprache der Oberfläche eine, die von rechts nach links geschrieben wird (Hebräisch, Arabisch, Persisch, Urdu und
die anderen in [doc/eng-us/languages.md](../../sprachen.md), Abschnitt „Right-to-left languages“, aufgeführten
Locales), dreht sich die Administrationsoberfläche um: Die Seite trägt `dir="rtl"` am Element `<html>`, die
Seitenspalten tauschen die Plätze, Text beginnt rechts, und die Symbole, die entlang der Zeile zeigen, zeigen in die
andere Richtung. Eine Seite von links nach rechts ist genau das, was sie vorher war: dasselbe Markup Byte für Byte und,
wie Screenshots bestätigen, dieselben Pixel.

Inhalte in einer *Inhaltssprache* mit Schreibrichtung von rechts nach links werden dort, wo sie angezeigt und
bearbeitet werden, auf dieselbe Weise gekennzeichnet, unabhängig von der Sprache der Oberfläche (siehe „Inhalte“
weiter unten).

## Woher die Richtung kommt

| Baustein | Was er tut |
|---|---|
| `share/locale/<locale>.ini`, `[LanguageSettings] TextDirection=rtl` | Die einzige Quelle. Eine Locale-Datei ohne den Schlüssel oder mit einem anderen Wert gilt als links nach rechts. 40 Locale-Dateien tragen ihn. |
| `eZLocale::textDirection()` | `'rtl'` oder `'ltr'` für ein Locale-Objekt. |
| `eZLocale::isRightToLeft()` | `true` für eine Locale mit Schreibrichtung von rechts nach links. |
| `eZLocale::textDirectionOf( 'heb-IL' )` | Statisch: die Richtung eines Locale-Codes, `'ltr'` für einen unbekannten Code. |
| `eZLocale::normalizeTextDirection( $value )` | Statisch: `rtl` / `right-to-left` (in beliebiger Schreibweise) werden zu `'rtl'`, alles andere zu `'ltr'`. |
| Template-Attribute `text_direction`, `is_right_to_left` | Dasselbe für eine Locale in einem Template, zum Beispiel `$code\|locale().is_right_to_left`. |
| `$site.text_direction` | Die Richtung der aktuellen Locale der Oberfläche, vom Kernel für jede Seite gesetzt (`ezpKernelWeb`). |

## Der Seitenrahmen

Jedes Pagelayout der Administrationsoberfläche gibt das Attribut nur bei einer Oberflächensprache mit Schreibrichtung
von rechts nach links aus:

```
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if} ...>
```

- `design/admin4/templates/pagelayout.tpl`, `design/admin4/templates/loginpagelayout.tpl`
- `design/admin4l/templates/pagelayout.tpl`
- `design/admin/templates/pagelayout.tpl`, `design/admin/templates/loginpagelayout.tpl` (das klassische Design)
- `extension/exp_adminui/design/adminui/templates/pagelayout.tpl` und `loginpagelayout.tpl` (AdminUI)

`first_set()` hält eine Seite, die von einem älteren Kernel (noch ohne `text_direction`) gerendert wird, bei links
nach rechts.

## Wie das Grundgerüst der Richtung folgt

Die Regeln für die Stylesheets jedes Administrationsdesigns (sie ergänzen die
[Regeln für Admin-Design und Template-Gestaltung](admin4-design.md)):

1. **Logische Eigenschaften für das Layout.** `margin-inline-start` / `-end`, `padding-inline-*`, `border-inline-*`,
   `inset-inline-start` / `-end`, die logischen Eckenradien (`border-start-start-radius` ...) und
   `text-align: start` / `end` statt ihrer Formen für links und rechts. Von links nach rechts bedeuten sie dasselbe,
   sodass sich die Seite nicht ändert; von rechts nach links tauschen sie von selbst. Umgestellt: `components.css`
   (admin4 und seine vorgeschriebene identische Kopie in design/admin), `admin4.css`, `pagelayout.css` und `core.css`
   von admin4 und design/admin sowie die Modul-Stylesheets
   `stylesheets/modules/{content,people,setup,structure,workflow}.css` (beide identischen Kopien).
2. **Eine Spiegelregel, wo eine logische Eigenschaft es nicht ausdrücken kann.** Kurzschreibweisen mit vier Werten und
   unterschiedlichen Werten für links und rechts, `float` / `clear`, `background-position`, horizontale Versätze von
   `box-shadow` und asymmetrische Radien erhalten eine Regel direkt nach der Regel, die sie spiegeln:

   ```css
   .x { margin: 0 4px 0 12px; }
   :where([dir="rtl"]) .x { margin: 0 12px 0 4px; }
   ```

   `:where()` erhöht die Spezifität nicht, sodass von rechts nach links die Spiegelregel genau dort gewinnt und
   verliert, wo es das Original tut. Keine Seite von links nach rechts trifft auf sie zu.
3. **Symbole, die entlang der Zeile zeigen, werden gespiegelt**, sonst nichts: `components.css` spiegelt die
   Chevrons, Pfeile, Carets sowie die Symbole für Antworten/Weiterleiten und Abmelden von `.a4-bi-*` mit
   `scale: -1 1` (das sich mit dem eigenen `rotate()` einer Komponente verbindet); die Symbole, die die Stylesheets
   selbst zeichnen (das Caret des Inhaltsbaums, das Caret der Popup-Untermenüs, der Pfeil der Dashboard-Kennzahlen, das
   Caret der Gruppen im Seitenmenü, die IcoMoon-Pfeile der Blätterleisten der Datentabellen in `core.css`), haben eine
   eigene Spiegelregel. Eine mit gedrehten Rahmen gezeichnete Form, die *nach unten* zeigt, behält ihre physischen
   Rahmen (der Chevron des Oberflächenumschalters, gekennzeichnet mit „the same in both directions“).
4. **Eine von einem Redakteur gewählte Ausrichtung von Inhalten wird nicht gespiegelt.** `text-left`, `object-left`,
   `align-right` und Ähnliches bedeuten in jeder Sprache dieselbe Seite.
5. **Skripte, die etwas messen oder verschieben, verwenden die Richtung.** Der Überlauf „Mehr“ der Reiterzeile von
   admin4 (`admin4.js`), die Größenänderung der linken Spalte per Ziehen und per Tastatur (`ezadmin_menubar.js`), die
   Steuerung der Spaltenbreite des klassischen Designs (`leftmenu_widthcontrol.js`) und seine Animationen zum Ein- und
   Ausblenden (`page_leftmenu.tpl`, `pagelayout.tpl`) verwenden logische Eigenschaften (`marginInlineStart`,
   `insetInlineEnd`) oder prüfen `getComputedStyle( document.documentElement ).direction`.
6. **AdminUI** lässt das `style.css` der Referenz unverändert. Bei einer Sprache mit Schreibrichtung von rechts nach
   links verlinken `page_head_style.tpl` und `loginpagelayout.tpl` stattdessen `stylesheets/style-rtl.css`: eine von
   einem Skript erzeugte Kopie von `style.css`, deren Deklarationen für links und rechts als logische Eigenschaften
   geschrieben sind und die nach jeder Regel, die eine logische Eigenschaft nicht ausdrücken kann, eine
   Spiegelregel `:where([dir="rtl"])` enthält (Regeln 1 und 2; die Ausrichtung von Inhalten behält ihre Seite,
   Regel 4). Erzeugen Sie sie neu, wann immer sich `style.css` ändert, und bearbeiten Sie sie nie von Hand. Die eigenen
   Stylesheets von AdminUI (`pagelayout.css`, `core.css`, `content.css`, `adminui*.css`, `setup-extensions.css`,
   `theme/*.css`) verwenden logische Eigenschaften, sodass sich der Rahmen und die Seiten darin (Tabellen, Formulare,
   Blättern, Dashboard-Blöcke) selbst spiegeln. `stylesheets/adminui-rtl.css`, als letztes und nur von rechts nach
   links verlinkt, dreht die Font-Awesome- und IcoMoon-Symbole, die entlang der Zeile zeigen (Pfeile, Chevrons,
   Carets, An- und Abmelden, Einrücken und Ausrücken, die Blätterleisten und den Umschalter der Seitenspalte der
   Bearbeitungsseite), und platziert den Griff zur Größenänderung der Spalte des Inhaltsbaums; `ngadminui/app.js` gibt
   dieser Spalte von rechts nach links den westlichen Griff von jQuery UI (Ziehen nach links verbreitert sie). Eine
   AdminUI-Seite von links nach rechts lädt genau das, was sie vorher geladen hat.

Neues CSS in einem Administrationsdesign muss die Regeln 1 bis 4 befolgen; die Designtests (`tests/tests/design`)
lesen die Namen der logischen Eigenschaften.

## Inhalte

Unabhängig von der Sprache der Oberfläche trägt Text in einer Inhaltssprache mit Schreibrichtung von rechts nach links
`lang` und `dir="rtl"` (der Vollständigkeit halber hier aufgeführt):

- `design/standard/templates/parts/exp_content_direction.tpl`: gibt ` lang="he-IL" dir="rtl"` für eine
  Inhalts-Locale mit Schreibrichtung von rechts nach links aus, nichts für eine von links nach rechts. Leitfaden:
  [doc/eng-us/guides/content-languages.md](../../anleitungen/inhaltssprachen.md).
- Die Bearbeitungsformulare: `content/edit_attribute.tpl` von design/standard, design/admin, admin4 und AdminUI.
- Die Vollansicht und die Vorschau der Administrationsoberfläche: `node/view/admin_preview.tpl`, `node/view/full.tpl`
  (klassisch, AdminUI), `node/view/a4_full_body.tpl` (admin4), `preview.tpl` jedes Administrationsdesigns.
- Der Online-Editor: TinyMCE 3 (`ezxmltext_ezoe.tpl`) und TinyMCE 8 (`ezxmltext_ezoe_tinymce8.tpl`) schalten ihre
  `directionality` für einen Text mit Schreibrichtung von rechts nach links auf `rtl`; der tiptap-Editor
  (`exp_oe_tiptap`) setzt über seine Option `contentDirection` `dir="rtl"` auf den Bearbeitungsbereich.

## Namen im Text der Oberfläche

Ein von einem Redakteur eingegebener Name (ein Knoten- oder Objektname, der Name oder Anmeldename eines Benutzers, ein
Klassen- oder Sektionsname) bildet einen eigenen Richtungsabschnitt, wo die Administrationsoberfläche ihn in ihren Text
einfügt, sodass ein englischer Name auf einer hebräischen Seite seine abschließende Interpunktion und seine Klammern
am Ende behält („Hello, world (test)!“, nicht „!Hello, world (test)“). Ein hebräischer Name auf einer englischen Seite
wird auf dieselbe Weise isoliert.

- `dir="auto"` an einem Inline-Element, das nur den Namen enthält (ein Link in einer Liste, ein Schritt der
  Brotkrumennavigation); `<bdi>` innerhalb eines Elements, das ein Block oder ein Flex-Element ist (eine Überschrift,
  eine Tabellenzelle, der Name im Benutzermenü), sodass das Element die Ausrichtung der Seite behält; `<bdi>` um den in
  einen übersetzten Satz eingefügten Namen („by %name“, „Below %location“, „Versions of “%name”“), wobei der Satz
  vorher oder der Name für sich maskiert wird.
- Gemeinsame Ansichten: `node/view/text_linked.tpl`, `content/view/text_linked.tpl`,
  `content/version/view/text_linked.tpl` (Beziehungslisten und viele Tabellen), `node/view/line.tpl` (admin4,
  klassisch: Suchergebnisse und die anderen Zeilenlisten), die Dashboard-Blöcke von design/standard,
  `content/bookmark_tree.tpl`, `parts/exp_content_direction.tpl`.
- Der Rahmen: die Brotkrumennavigation (`page_toppath.tpl`), die Seitenüberschrift und die Zeile „Geändert ... von“
  der Vollansicht, das Benutzermenü, die Benutzerzeile auf dem Smartphone und der Kasten „Aktueller Benutzer“, die
  Begrüßung im Dashboard; die Listen: Unterelemente, Lesezeichen, Entwürfe, Papierkorb, Verlauf, ausstehende
  Elemente, Details, Objektinformationen.
- Skripte: die Tabelle der Unterelemente (`ezajaxsubitems_expdatatable.js`: der Namenslink, die Zellen für Klasse,
  Bearbeiter und Sektion) und der Inhaltsbaum (`contentstructuremenu_dynamic.js`: jeder Name, nur auf einer Seite von
  rechts nach links, weil ein isolierter Name nicht silbengetrennt wird und der Baum lange Namen von links nach rechts
  silbentrennt).
- Der Seitentitel kann kein Markup enthalten: Von rechts nach links setzt `page_head.tpl` jeden Schritt des Titels
  zwischen U+2068 FIRST STRONG ISOLATE und U+2069 POP DIRECTIONAL ISOLATE (als Zeichenreferenzen geschrieben); ein
  Titel von links nach rechts bleibt unverändert. Der Titel von AdminUI ist ein fester Text und braucht nichts.
- Die Admin-Vorschau von Inhalten in einer Sprache von links nach rechts innerhalb einer Seite von rechts nach links
  wird mit `dir="ltr"` gekennzeichnet (`exp_content_direction.tpl` mit `exp_dir_ltr=true()`, verwendet von
  `admin_preview.tpl` und `preview.tpl`), sodass englische Inhalte als Englisch gelesen werden; eine Seite von links
  nach rechts gibt nichts Neues aus. Der Parameter liest die Locale der Oberfläche aus site.ini
  `[RegionalSettings] Locale`, weil `$site` für die Templates eines Moduls nicht gesetzt ist.

Von links nach rechts sehen die Seiten aus wie vorher (Screenshots vorher und nachher Pixel für Pixel verglichen: Nur
Glyphenpositionen im Subpixelbereich am Rand eines isolierten Namens haben sich verschoben).

## Wie Sie testen

1. Melden Sie sich mit einem Test-Administrator an (niemals mit dem gemeinsam genutzten Konto `admin`: Seine
   Einstellungen sind der Arbeitsstand von jemandem).
2. Wählen Sie in der rechten Werkzeugleiste (AdminUI: das Menü Oberfläche der Seitenleiste) **Sprache der Oberfläche:
   עברית (ישראל)** (Hebräisch, `heb-IL`) und übernehmen Sie die Auswahl.
3. Prüfen Sie: `<html dir="rtl" lang="he-IL">`; die linke Spalte (Inhaltsbaum) rechts, die Werkzeugleiste links;
   Kopfbereich, Reiter, Tabellen und Formulare lesen sich von rechts; Carets und Chevrons zeigen nach links; keine
   horizontale Bildlaufleiste. Prüfen Sie hell und dunkel, in einem breiten Fenster und bei 200 % Zoom (etwa 960 px).
4. Machen Sie eine Stichprobe mit Arabisch (`ara-SA`): `<html dir="rtl" lang="ar-SA">`.
5. Wählen Sie wieder Englisch: Die Seite hat kein Attribut `dir` und sieht aus wie vorher.

Für 6.0.15 geprüft in admin4, admin4l, dem klassischen Design und AdminUI auf dem Dashboard, einem Ordner, einem
Bearbeitungsformular und der Klassenliste, hell und dunkel, bei 1440 px und bei 960 px mit Skalierung 2: kein
horizontaler Überlauf, keine Skriptfehler und kein Barrierefreiheitsbefund, den die Seite von links nach rechts nicht
ebenfalls hat. Von links nach rechts wurden Screenshots mit den alten und den neuen Stylesheets auf denselben Seiten
Pixel für Pixel verglichen (die einzigen verbliebenen Unterschiede lagen im Rendering-Rauschen der Live-Website).

## Folgearbeiten

- **Stylesheets von Extensions** (`BackendCSSFileList` der Extensions) verwenden stellenweise noch physisches links und
  rechts: expai (`expai.css`), expui (`exp/datatable.css`: Die Gruppen der Blätterleiste floaten links/rechts,
  für AdminUI in `adminui-rtl.css` gespiegelt; `dialog.css`, `datepicker.css`, `core.css`), eztags
  (`jstree/eztags/style.css`, `tagssuggest.css`, `contentstructure-tree.css`, `jqmodal.css`), ezautosave
  (`preview.css`, `autosave.css`), expqueue, ezfind (`ezajax_autocomplete.css`), sevenx_alpha_settings
  (`a4-showcase.css`). Stellen Sie sie mit ihrem nächsten Release auf logische Eigenschaften um.
- **Die Seitenspalten des klassischen Designs** schneiden ihren Inhalt am inneren Rand ab, wie sie es auch von links
  nach rechts tun; nichts Neues, aber bei langen hebräischen oder arabischen Beschriftungen sichtbarer.
- **Anmeldeseite in einer Sprache mit Schreibrichtung von rechts nach links**: Die Sprache der Oberfläche wählt ein
  angemeldeter Benutzer, sodass die Anmeldeseite nur der eigenen Locale des Siteaccess folgt. Sie ist
  richtungssicher (geprüft mit von Hand gesetztem `dir="rtl"`), aber eine Anmeldeseite von rechts nach links braucht
  einen Siteaccess, dessen Locale von rechts nach links geschrieben wird.
- **Bidirektionaler Text**: Namen werden isoliert (siehe „Namen im Text der Oberfläche“), auf den dort aufgeführten
  Seiten. Andere Modulseiten (Setup, Rollen, Klassen, die Seitenmenüs der Bearbeitungsformulare) und nicht übersetzter
  englischer Oberflächentext innerhalb einer Seite von rechts nach links folgen noch dem einfachen bidirektionalen
  Unicode-Algorithmus; das Mittel gegen Letzteres ist die Übersetzung.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
