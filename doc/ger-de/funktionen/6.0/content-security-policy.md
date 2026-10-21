# Content Security Policy

Eine Content Security Policy teilt dem Browser mit, welche Skripte, Stylesheets, Bilder und Verbindungen eine Seite
verwenden darf. Eine strenge Policy (`script-src 'self'`, kein `'unsafe-inline'`) bedeutet, dass ein Skript, das über
einen gespeicherten oder reflektierten Wert in eine Seite eingeschleust wurde, nicht ausgeführt wird, selbst wenn ein
Template vergisst, ihn zu maskieren.

Exponential sendet eine Policy aus `site.ini [ContentSecurityPolicy]`, neben den Sicherheits-Headern von
`[HTTPHeaderSettings] SecurityHeaders[]` (siehe [Sicherheitshärtung](../../installation/13-sicherheitshaertung.md), 13.9).
Standardmäßig ist sie **in der Administration nur meldend (report-only)** und auf den Websites ausgeschaltet.

## Einstellungen

```ini
[ContentSecurityPolicy]
# disabled | report-only | enforce | auto
Mode=auto
Directives[]
Directives[default-src]='self'
Directives[script-src]='self'
Directives[style-src]='self'
Directives[img-src]='self' data: blob:
Directives[font-src]='self' data:
Directives[media-src]='self' blob:
Directives[connect-src]='self'
Directives[frame-src]='self'
Directives[object-src]='none'
Directives[base-uri]='self'
Directives[form-action]='self'
Directives[frame-ancestors]='self'
ReportURI=
```

- **Mode**
  - `disabled`: keine Policy.
  - `report-only`: der Header `Content-Security-Policy-Report-Only`. Der Browser meldet, was die Policy blockieren
    würde (in seiner Entwicklerkonsole und an `ReportURI`, sofern gesetzt), und blockiert nichts.
  - `enforce`: der Header `Content-Security-Policy`. Der Browser blockiert, was die Policy nicht erlaubt. Er wird als
    zweite Policy nach `SecurityHeaders[Content-Security-Policy]` (standardmäßig `frame-ancestors 'self'`) gesendet;
    der Browser wendet beide an.
  - `auto` (der Standard): `report-only` auf Siteaccesses der Administration, also solchen, deren
    `[DesignSettings] SiteDesign` mit `admin` beginnt (admin, admin3, admin4, admin4l, adminui) oder deren
    `AdditionalSiteDesignList[]` ein solches Design enthält (ein Editor-Siteaccess auf admin4l), `disabled` auf allen
    anderen.
- **Directives[]**: die Policy, eine Direktive pro Eintrag. Setzen Sie einen Eintrag in einer Überschreibung leer, um
  die Direktive wegzulassen; eine Überschreibung, die mit `Directives[]` allein beginnt, ersetzt die ganze Liste. Ein
  Eintrag, dessen Wert `;` oder `,` enthält oder dessen Name kein Direktivenname ist, wird ignoriert, sodass eine
  Einstellung den Header weder aufteilen noch eine eigene Direktive hinzufügen kann. `frame-ancestors` und `sandbox`
  werden nur gesendet, wenn die Policy durchgesetzt wird: Browser ignorieren sie in einer Report-only-Policy (und weisen
  in der Konsole darauf hin). Das Einbetten in Frames bleibt in jedem Modus durch `SecurityHeaders[]`
  (`X-Frame-Options`, `frame-ancestors 'self'`) eingeschränkt.
- **ReportURI**: wohin der Browser seine Berichte sendet (`report-uri`), zum Beispiel an einen eigenen Sammeldienst.
  Leer: nur die Entwicklerkonsole.

Pro Pfad überschreibt `[HTTPHeaderSettings] HeaderList` weiterhin jeden Header, auch die Policy. Seiten, die aus dem
HTTP-Cache des Kernels beantwortet werden, tragen dieselben Header.

## Einschalten

1. Lassen Sie die Administration auf `report-only` und arbeiten Sie eine Weile damit: Öffnen Sie die Entwicklerkonsole
   (oder sammeln Sie die Berichte mit `ReportURI`) und notieren Sie, was die Policy blockieren würde. Eigene Extensions
   und Site-Designs sind die übliche Quelle.
2. Setzen Sie für eine Website `Mode=report-only` in ihrem Siteaccess und gehen Sie ebenso vor.
3. Wenn für die von Ihnen genutzten Seiten nichts mehr gemeldet wird, setzen Sie `Mode=enforce`.

```ini
# settings/siteaccess/<admin siteaccess>/site.ini.append.php
[ContentSecurityPolicy]
Mode=enforce
```

### Schritt für Schritt auf enforce umstellen

Der ausgelieferte Standard ändert für Besucher nichts: `Mode=auto` sendet auf den Siteaccesses der Administration eine
Report-only-Policy und auf den Websites keine. Das Durchsetzen ist eine Entscheidung pro Siteaccess, die nach einer
Prüfung getroffen wird:

1. **Prüfen Sie die Templates.** `AdminContentSecurityPolicyTest` prüft die Kern-Designs und die Extensions, die dieses
   Repository ausliefert. Führen Sie ihn einmal mit allen Extensions der Installation aus, so wie die Administration
   sie rendert (die Ketten von `uiswitch.ini DesignChain[]`, `AdditionalSiteDesignList[]` jedes Siteaccess der
   Administration und die aktiven Extensions aus `settings/override`):

   ```bash
   CSP_ALL_EXTENSIONS=1 php vendor/bin/phpunit tests/tests/design/AdminContentSecurityPolicyTest.php
   ```

   Jede Zeile des Fehlschlags ist ein Template mit Inline-Code, den eine durchgesetzte Policy brechen würde, geordnet
   nach Extension. Ein Template einer Extension wird im Repository dieser Extension korrigiert (Skripte in Dateien, die
   mit `ezscript` geladen werden, Werte in `data-*`-Attribute, Handler in Listener oder die `data-a4-*`-Aktionen,
   Menüeinträge in `data-menu-*`).
2. **Sammeln Sie die Berichte aus der echten Nutzung.** Setzen Sie `ReportURI` auf einen eigenen Sammeldienst oder
   beobachten Sie die Entwicklerkonsole (Chrome und Firefox protokollieren „[Report Only] Refused to ...“), und
   verwenden Sie die Administration einige Tage lang: die Seiten jeder Extension, die Editoren, die Dialoge. Handler-
   Attribute werden nur gemeldet, wenn sie ausgeführt werden; öffnen Sie also auch die Kontextmenüs und Dialoge.
3. **Setzen Sie die Policy zuerst in einem Siteaccess durch**, am besten in einem zweiten Siteaccess der
   Administration, den nur Sie verwenden, mit denselben Designs und Extensions wie der Haupt-Siteaccess. Behalten Sie
   für alle anderen den Report-only-Siteaccess bei.
4. **Lockern Sie nur, was Sie müssen**, pro Siteaccess und pro Direktive, zum Beispiel für TinyMCE 3:

   ```ini
   [ContentSecurityPolicy]
   Mode=enforce
   Directives[script-src]='self' 'unsafe-inline'
   Directives[style-src]='self' 'unsafe-inline'
   ```

5. **Leeren Sie den INI-Cache** (`php bin/php/ezcache.php --clear-tag=ini`) und laden Sie PHP neu (und starten Sie
   Velocity neu), damit jeder Prozess den neuen Header sendet. **Zum Zurückkehren** setzen Sie `Mode=report-only` (oder
   `disabled`) und leeren den INI-Cache erneut; sonst ändert sich nichts.

Websites (Siteaccesses außerhalb der Administration) erhalten mit `auto` keine Policy. Ihre Designs und der von ihnen
gerenderte Rich Text (`style=""` aus dem Inhalt, eingebettete Player) werden vom Test nicht geprüft: Beginnen Sie dort
mit `Mode=report-only`.

## Berichte

Der Kernel sammelt die Berichte selbst: Die Ansicht **`csp/report`** nimmt entgegen, was der Browser sendet, und
schreibt pro Verstoß eine Zeile nach `var/log/csp.log`, die der Log-Viewer als **Berichte der Content Security
Policy** auflistet (**Setup > Logdateien**, Schlüssel `csp`). Richten Sie die Policy mit `ReportEndpoint=enabled`
darauf aus (`report-uri` und der Header `Reporting-Endpoints` nennen dann `csp/report` des Siteaccess) oder mit
`ReportURI=/csp/report`.

- **Beide Formate**: `application/csp-report` (die `report-uri` von Firefox und älteren Browsern) und
  `application/reports+json` (die Reporting API: `Reporting-Endpoints` und `report-to`, Chrome und Edge);
  `application/json` wird als eines der beiden gelesen. Berichte der Reporting API anderen Typs (deprecation,
  intervention) werden übersprungen.
- **Für jeden Besucher offen**: Browser senden Berichte ohne Anmeldung, auch an eine Administration, die von allen eine
  Anmeldung verlangt. `site.ini [SiteAccessSettings] AnonymousAccessList[]` und `[RoleSettings] PolicyOmitList[]`
  nennen `csp/report`. Ein angemeldeter Browser kann sein Session-Cookie mit einem Bericht senden: Die Prüfung des
  Formular-Tokens (ezformtoken) lässt einen POST an `csp/report` mit einem dieser Content-Types unbehelligt; ein
  Formular einer anderen Website kann keinen davon senden, und die Ansicht schreibt nur eine Log-Zeile.
- **Es kommt nichts zurück**: Die Antwort ist ein Status ohne Body: `204` für einen angenommenen Bericht, `405` für
  eine andere Methode als POST, `415` für einen anderen Content-Type, `413` oberhalb von `MaxReportBytes`, `400` für
  einen Body, der kein Bericht ist, und `429` (mit `Retry-After: 60`) oberhalb eines Ratenlimits. Sie wird nie
  zwischengespeichert (`Cache-Control: no-store`).
- **Ratenbegrenzt**: pro Client (`ReportsPerMinute`, gezählt über den gesalzenen Adress-Hash des Anfrage-Klassifikators,
  sodass keine Adresse gespeichert wird) und für alle Clients zusammen (`ReportsPerMinuteTotal`). Der erste abgelehnte
  Bericht eines Clients in einer Minute wird vom [Anfrage-Klassifikator](anfrageprotokolle.md) als `abuse/csp-report-flood`
  nach `security.log` geschrieben, sodass eine Flut dort eine Zeile pro Minute ergibt, nicht eine pro Anfrage.
- **Begrenzt und maskiert**: Die Adresse wird wie in den Anfrage-Logs maskiert (`203.0.x.x`, `site.ini
  [RequestLogSettings] MaskIP`); die Query jeder URI durchläuft die Masken des Log-Viewers (Geheimnisse, E-Mail- und
  IP-Adressen), ein Benutzername oder Passwort in einer URI sowie das Fragment werden entfernt, eine `data:`- oder
  `blob:`-URI wird nur mit ihrem Schema geschrieben, das Beispiel wird auf 40 Zeichen gekürzt, und kein Wert kann eine
  Zeile oder ein Feld hinzufügen. Aus einem Bericht werden höchstens `MaxViolationsPerReport` Zeilen geschrieben.

```ini
[ContentSecurityPolicy]
# per client and minute; above it 429 and one abuse/csp-report-flood line in security.log
ReportsPerMinute=60
# all clients together, per minute
ReportsPerMinuteTotal=600
# the biggest report body taken, in bytes
MaxReportBytes=65536
# the most violations written from one report
MaxViolationsPerReport=20
# the log file, in the log directory
LogFile=csp.log
```

Eine Zeile von `csp.log`, nach dem Datum und der maskierten Adresse:

```
[ Oct 17 2026 12:00:00 ] [203.0.x.x] csp/script-src-elem blocked=inline disposition=enforce document=https://example.com/admin/content/view/full/2 siteaccess=admin line=12 column=5 source=https://example.com/admin/content/view/full/2 status=200 sample="alert(1)" ua=3f2a9c1b "Mozilla/5.0 (X11; Linux x86_64)"
```

- `csp/<directive>`: die verletzte Direktive (`script-src-elem`, `style-src-attr`, `img-src` ...).
- `blocked=`: was blockiert wurde: ein Schlüsselwort (`inline`, `eval`, `wasm-eval`, `data`, `blob`, `self`,
  `trusted-types-policy`) oder die Adresse.
- `disposition=`: `enforce` (der Browser hat es blockiert) oder `report` (eine Report-only-Policy: nichts wurde
  blockiert).
- `document=`, `source=`, `line=`, `column=`: die Seite sowie Datei und Position des Codes, sofern der Browser sie
  nennt. `sample=`: die ersten Zeichen des Inline-Codes oder -Stils.

**Im Log-Viewer** werden die Zeilen Feld für Feld gelesen (Quelle `csp/<directive>`, Anfrage die Seite). Die
**Fehlerübersicht** gruppiert `csp.log` nach Direktive und dem, was blockiert wurde, wobei eine Adresse auf ihren
Ursprung gekürzt wird (`[DigestSettings] GroupBy[csp]=directive-blocked`): `csp/script-src-elem blocked=inline 42`,
`csp/img-src blocked=https://maps.example.com 3`. Sie wird wie die Anfrage-Logs rotiert: 5 Archive, komprimiert,
10 MB, nach 30 Tagen bereinigt (`logviewer.ini [RotationSettings]`, Schlüssel `csp`).

## Die ausgelieferten Direktiven

- `script-src 'self'` erlaubt nur Skriptdateien dieser Website: kein Inline-`<script>`, kein `on...=`-Attribut, kein
  `javascript:`-Link, kein `eval()`.
- `style-src 'self'` erlaubt nur Stylesheet-Dateien: kein `<style>`-Element und kein `style=""`-Attribut. Ein Skript,
  das `element.style` setzt, ist nicht betroffen.
- `img-src` und `media-src` ergänzen `data:` und `blob:` (Vorschauen von für einen Upload ausgewählten Dateien, in
  Stylesheets eingebettete Icons); `font-src` ergänzt `data:`.
- `object-src 'none'`, `base-uri 'self'`, `form-action 'self'`, `frame-ancestors 'self'` schließen die Wege über
  Plugins, Basis-URL, Formularziel und Einbettung in Frames.

Bevor Sie `Mode=enforce` setzen, beachten Sie, was `object-src 'none'` blockiert: die `<object>`/`<embed>`-Player der
alten Medienvorschauen (`admin_preview/flash.tpl`, `windows_media.tpl`, `real_video.tpl`, `quicktime.tpl` und die
Ansichts-Templates von `ezmedia`; Browser führen diese Plugins ohnehin nicht mehr aus) sowie ein über `<object>` oder
`<embed>` inline angezeigtes PDF. Erlauben Sie es dort, wo Sie es brauchen, z. B. `Directives[object-src]='self'`.

Eine Seite, die mehr benötigt (einen Kartendienst, einen Font-Host, ein Analyse-Skript), ergänzt den Host in der
Direktive ihres Siteaccess, zum Beispiel `Directives[script-src]='self' https://maps.example.com`.

## Die Administration ohne Inline-Code

admin4 und admin4l sowie die Templates von admin3, admin2, admin und standard, die sie rendern, enthalten kein
Inline-`<script>`, kein `on...=`-Attribut, keinen `javascript:`-Link, kein `style=""`-Attribut und kein
`<style>`-Element, sodass die Administration unter der ausgelieferten Policy läuft.
`tests/tests/design/AdminContentSecurityPolicyTest.php` sichert dies für jedes Template der beiden Design-Ketten und
für die Skripte von admin4 ab; die Ausnahmen, die er auflistet, stehen weiter unten.

Der Test liest die Kette so, wie der Kernel sie aufbaut: die Designs von `uiswitch.ini [UISwitch] DesignChain[]` (oder
die eines Siteaccess der Administration: `SiteDesign`, `AdditionalSiteDesignList[]`, `StandardDesign`) und für jedes
Design zuerst die Design-Verzeichnisse der aktiven Extensions (`design.ini [ExtensionSettings] DesignExtensions[]`),
dann das Kernverzeichnis. Das Template einer Extension zählt daher dort, wo es ein Kern-Template ersetzt, ebenso wie die
Extension-Templates, die im Kern kein Gegenstück haben, etwa die Bearbeitungs-Templates des Online-Editors. Aktiv sind
die Extensions aus `site.ini [ExtensionSettings] ActiveExtensions[]` (mit `settings/override`) und die
`ActiveAccessExtensions[]` jedes Siteaccess, dessen `SiteDesign` admin4 oder admin4l ist; ein Checkout ohne
`settings/override` zählt jede Extension, die er enthält. Was eine Extension noch inline enthält, ist pro Extension mit
Begründung in `EXTENSION_ALLOWLIST` aufgeführt; wie die Liste der Kern-Templates wird sie nur kürzer: Der Test schlägt
fehl, wenn eine aufgeführte Datei weniger Inline-Code enthält als angegeben oder nicht mehr gerendert wird, bis der
Eintrag verringert oder entfernt wird.

Wie die einzelnen Teile funktionieren, für eigene Templates und für Extensions:

- **Seitenskripte** sind Dateien: `design/admin4/javascript/page/<module>_<view>.js`, ausgegeben mit `{ezscript( array(
  'page/<file>.js' ) )}` an der Stelle, an der das Inline-Skript stand, sodass der Code an derselben Stelle der Seite
  ausgeführt wird. Verwenden Sie in einem Template des Seiteninhalts `ezscript`, nicht `ezscript_load`: `ezscript_load`
  würde dort jede Datei ausgeben, die `ezscript_require` bis dahin gesammelt hat, und deren Laden im Head beenden (eine
  später angeforderte Datei, etwa das Skript der Unterelement-Tabelle, käme dann nach dem Seitenskript, das sie
  braucht). Die Werte, die ein Skript aus dem Template benötigt, sind `data-*`-Attribute an dem Element, mit dem es
  arbeitet, für HTML bereinigt (`data-text-selected="{'%count selected.'|i18n( ... )|wash}"`); strukturierte Werte sind
  JSON: `data-conf="{json_encode( hash( ... ) )|wash}"`. Ein Template, das mehrmals auf einer Seite vorkommen kann (das
  Bearbeitungs-Template eines Datentyps), erhält seine Datei jedes Mal: Die Datei richtet sich nur einmal ein.
- **Das Head-Skript** jeder Seite (Farbmodus, `window.a4Pref`) ist `design/admin4/javascript/a4_head.js`, geladen von
  `page_head_style.tpl` (mit `ezscript`) vor den Stylesheets, als normales (blockierendes) Skript im `<head>`: Es wird weiterhin
  ausgeführt, bevor der Body geparst wird, sodass schon die erste Darstellung den gespeicherten Farbmodus, die
  Spaltenbreiten und die `data-a4-pref-*`-Werte hat.
- **Kleine Aktionen** sind `data-a4-*`-Attribute, die von einem Listener pro Ereignis in
  `design/admin4/javascript/a4_actions.js` (auf jeder admin4-Seite geladen) behandelt werden:
  `data-a4-confirm="Question?"` (ein Link, ein Button oder ein Formular), `data-a4-discard-confirm`,
  `data-a4-toggle-checkboxes="Name[]"` (mit `data-a4-form` für ein anderes Formular), `data-a4-check-all="#selector"`,
  `data-a4-dismiss`, `data-a4-submit-on-change`, `data-a4-navigate-on-change`, `data-a4-reset-field` und
  `data-a4-focus`. Der Kopfkommentar der Datei beschreibt jedes davon.
- **Kontextmenüs** (`popupmenu/*.tpl`, `ezpopupmenu.js`) werden in `data-menu-*`-Attributen deklariert: am Menü
  `data-menu-depth` und `data-menu-header`; an einem Eintrag `data-menu-url`, `data-menu-shown-if`,
  `data-menu-disabled-class`, `data-menu-disabled-for`, `data-menu-variable` mit `data-menu-template` (die ID eines
  `<template>` mit dem Markup eines Eintrags); was ein Eintrag tut: `data-menu-submenu` (mit `data-menu-hide`),
  `data-menu-form` (mit `data-menu-form-values`) oder `data-menu-redirect`. Ein Element, das ein Menü öffnet, hat
  `data-menu-open`, `data-menu-values` (JSON), `data-menu-languages`, `data-menu-classes`, `data-menu-title`,
  `data-menu-disable` und `data-menu-disable-item`. Der Kopfkommentar von `ezpopupmenu.js` listet sie auf.
  **Extensions funktionieren weiter:** Ein Menüeintrag mit eigenem `onmouseover`/`onclick`-Attribut bleibt diesem
  überlassen, und ein Skript, das einem Kernmenü einen Eintrag hinzufügt
  (`menuArray['ContextMenu']['elements']['my-item'] = { ... }`), findet das aus den Attributen erzeugte Menü vor. Ein
  solcher Extension-Eintrag ist allerdings Inline-Code und wird von der Policy gemeldet, bis die Extension ihn auf die
  neue Weise deklariert.
- **Der Inhaltsbaum** (`contentstructuremenu/content_structure_menu_dynamic.tpl`) ist ein `div.content-tree-menu` mit
  seinen Daten in `data-tree-root`, `data-tree-params` und `data-tree-request` (JSON);
  `contentstructuremenu_dynamic.js` zeichnet ihn, wenn die Seite geparst ist (`window.treeMenu` wird wie bisher
  gesetzt), und seine Buttons zum Auf- und Zuklappen funktionieren über einen Listener statt über erzeugte
  `onclick`-Attribute.
- **Erzeugter Skript- und Stilinhalt** von ezjscore (Serverfunktionen wie `ezjsc::jqueryio` bei ausgeschaltetem
  Packen, etwa im `DevelopmentMode`) wird in eine Datei im Cache-Verzeichnis des Packers geschrieben, nach seinem
  Inhalt benannt und verlinkt (`ezjscPacker::generatedContentURL()`); zuvor wurde er inline ausgegeben.

Dasselbe gilt für die Kopien der Kontextmenü- und Baumskripte und -Templates in `design/admin`, die identisch mit
denen von admin4 gehalten werden; die übrigen Templates des klassischen Admin-Designs sind unverändert.

### Was weiterhin mehr als 'self' benötigt

- **Inhaltsausgabe**: die Breite einer Rich-Text-Tabelle (`ezxmltags/table.tpl`), die Ausrichtung ihrer Zellen
  (`td.tpl`) und der Rahmen eines Bildes (`content/datatype/view/ezimage.tpl`) sind `style=""`-Werte, die aus dem Inhalt
  stammen. Unter einem durchgesetzten `style-src 'self'` verwirft der Browser sie (die Admin-Vorschau zeigt die Tabelle
  in ihrer natürlichen Breite). Ergänzen Sie `'unsafe-inline'` in `style-src`, wenn das für Ihre Inhalte wichtig ist.
- **Der Beleg** (`shop/orderreceipt_style.tpl`) behält sein `<style>`-Element: Es wird mit dem Beleg als
  aufzubewahrende Datei (`orderreceipt_document.tpl`) geteilt, die ihre eigenen Stile mitführen muss.
- **Rich-Text-Editoren.** Die TinyMCE-8-Engine von ezoe startet aus einer Datei: `ezxmltext_ezoe_tinymce8.tpl`
  schreibt die Einstellungen, die alle Editoren teilen, als `<script type="application/json" id="ezoe8-settings">` und
  die Toolbar-Zuordnung als `id="ezoe8-button-map"`, jede Textarea trägt ihre Attribut-ID, Buttons, Pfadposition und
  Zeilen in `data-ezoe8-*`-Attributen, und `javascript/tinymce8_ez/ezoe8_start.js` (mit `defer` geladen) liest sie und
  ruft `tinymce.init` auf.
  Einen eigenen Datenblock geben Sie aus einem `set-block` mit `|explode( '<' )|implode( '<' )|explode( '>' )|
  implode( '>' )` aus: `json_encode` lässt `<!--` und `<script` in einem Wert unverändert, und `<!--<script>` lässt
  den Browser über das `</script>` des Blocks hinweglesen, sodass die Skripte danach nicht ausgeführt werden.
  TinyMCE 8 setzt selbst Stile an seinen eigenen Elementen (seine Dokumentation verlangt `style-src 'unsafe-inline'`);
  setzen Sie die Policy dort, wo es verwendet wird, also mit `'unsafe-inline'` in `style-src` durch.
  TinyMCE 3, die Standard-Engine (`ezoe.ini [EditorSettings] EditorEngine=tinymce3`), ist nicht umgestellt: Sein
  Bearbeitungs-Template `ezxmltext_ezoe.tpl` startet den Editor mit einem Inline-Skript, und seine Dialoge (die 27
  Templates unter `extension/ezoe/design/standard/templates/ezoe/`, die Inline-Code enthalten) haben 23 Inline-Skripte,
  13 Event-Handler-Attribute, 51 `javascript:`-Links, 52 `style=""`-Attribute und ein `<style>`-Element (gezählt am
  16. Oktober 2026).
  `tiny_mce.js` und `tiny_mce_popup.js` schreiben `javascript:`-Links und `onclick`-Attribute in die Toolbars und
  Dialoge, die sie aufbauen, sodass das Verschieben der Templates in Dateien allein TinyMCE 3 nicht unter der Policy lauffähig machen würde. Ein Siteaccess, der mit TinyMCE 3 bearbeitet, benötigt
  `Directives[script-src]='self' 'unsafe-inline'` und `Directives[style-src]='self' 'unsafe-inline'`, bevor er die
  Policy durchsetzt, oder er wechselt zu TinyMCE 8. Editoren anderer Pakete (Tiptap, exp_oe_tiptap) werden vom selben
  Test geprüft, wenn sie installiert sind; was sie noch inline schreiben, steht in `EXTENSION_ALLOWLIST`.
- **Extensions**, die Inline-Skripte oder Handler in Admin-Seiten schreiben (eigene Menüeinträge, Seitenskripte),
  werden gemeldet, bis sie wie oben beschrieben auf Dateien und `data-*`-Attribute umgestellt sind.

### Gemessen auf einer Installation mit vielen Extensions (17. Oktober 2026)

Ein Crawl von 45 Administrationsseiten pro Siteaccess (admin4l, admin4, adminui von Exponential UI), auf Apache und auf
Velocity, angemeldet als Administrator, mit der Report-only-Policy dieses Releases:

| | vorher | nachher |
|---|---|---|
| Inline-Skripte pro Seite | 32-34 | 11-15 |
| Event-Handler-Attribute pro Seite | 165-167 | 36-42 |
| Policy-Berichte pro Seite (admin4, admin4l) | 35 | 11-14 |

Keine Seite ging kaputt, und es kam kein Skriptfehler hinzu (report-only blockiert nichts). Jeder verbleibende Befund
stammt aus einer installierten Extension oder dem eigenen Design von Exponential UI, nicht aus den Templates des
Kernels; `CSP_ALL_EXTENSIONS=1` listet sie pro Datei auf. Auf dieser Installation waren es, nach Anzahl der Befunde in
den Templates, die admin4 und admin4l rendern:
cjw_newsletter (193), eztags (49), xrowextract (24, seine Kontextmenüs für Export und Import), explayouts_ui_api (22),
sevenx_authentication_2fa (14), ezodf (14, die OpenOffice-Menüeinträge), explayouts (12), ezgmaplocation, expai (je
8), xrowmetadata, expchangeclass (je 7), syndication, recaptcha, bcwebsitestatistics (je 6), ezie,
explayouts_ui, enhancedselection2 (je 5), ezwt, ezownerchange, ezautosave (je 4), ezstarrating (3) sowie ein oder zwei
in sevenx_dse, git_manager, bccie, ezupdate, expui, expqueue, explayouts_content_browser_ui, enhancedezbinaryfile,
cjw_tmv und cjw_themes_jumper. Zur Laufzeit kommen die Kontextmenüeinträge von ezmultiupload und ezownerchange, die
Debug-Ausgabe und das Head-Skript von Exponential UI (Ace und der jQuery-UI-Fallback) hinzu. Jede davon wird in ihrem
eigenen Repository geändert, bevor ein Siteaccess, der sie verwendet, auf `enforce` gesetzt wird.

### Extensions ohne Inline-Skripte (17. Oktober 2026)

Die in `tests/tests/design/ExtensionInlineScriptsTest.php` aufgeführten Extensions enthalten ab der dort genannten
Version (composer.json verlangt diese Versionen) in keinem Template ein Inline-Skript, ein Event-Handler-Attribut oder
einen `javascript:`-Link; der Test hält sie daran, sobald sie installiert sind. Sie folgen den obigen Mustern mit einem
Unterschied: Eine Extension läuft in jedem Administrationsdesign und auf früheren Releases, daher verlässt sie sich
weder auf die `data-a4-*`-Aktionen von admin4 noch auf die `data-menu-*`-Menüs. Jede liefert ihre eigene Skriptdatei
mit delegierten Listenern aus; ihre Kontextmenüeinträge werden in Datenattributen deklariert, und ihr Skript fügt sie
`menuArray` hinzu und ruft dieselben `ezpopmenu_*`-Funktionen wie bisher auf.

Was sie noch inline enthalten, sind Stile (`style=""`, `<style>`), die `style-src 'self'` meldet; setzen Sie die
Policy mit `style-src 'self' 'unsafe-inline'` durch, bis auch diese verschoben sind.

Gemessen auf derselben Installation nach diesen Releases, wobei der Browsertest die Policy auf jede Seite setzte und
`script-src 'self'` durchgesetzt war: Die Administrationsseiten von admin4l, admin4 und Exponential UI (darunter die
Seiten für Newsletter, Tags, Export, Layouts, Eigentümerwechsel und die übrigen Extension-Seiten) melden keinen
Skriptverstoß, und ihre Funktionen arbeiten, auf Apache und auf Velocity. Was im Sinne von Skripten noch inline ist, ist
die TinyMCE-3-Engine (ezoe und die Kopie ihres Bearbeitungs-Templates in Exponential UI); externe Skripte (Google Maps,
reCAPTCHA, die Social-Buttons von ezdemo, ein Ad-Server von ezflow) benötigen ihre Hosts in `script-src`.

### Bevor Sie die Policy in der Administration durchsetzen

- **Menüeinträge von Extensions.** Eine Extension, die Kontextmenüeinträge mit einem Inline-Skript
  (`menuArray['ContextMenu']['elements'][...] = ...`) und `onmouseover`/`onclick`-Attributen hinzufügt - zum Beispiel
  die Menüs von xrowextract, ezodf (die OpenOffice-Einträge für Import/Export) und ezmultiupload (der Eintrag Multi Upload der Kontextmenüs) -,
  funktioniert unter report-only. Unter einer durchgesetzten Policy führt der Browser weder das Skript noch die Handler
  aus: Die Einträge werden weiterhin gezeichnet, bewirken aber nichts, ohne einen Fehler auf der Seite. Stellen Sie sie
  auf `data-menu-*` um (wie die Kernmenüs), bevor Sie `Mode=enforce` setzen, oder behalten Sie `'unsafe-inline'` in
  `script-src` für diesen Siteaccess.
- **`menuArray` neu zugewiesen.** `ezpopupmenu.js` erzeugt `menuArray` und legt den Eintrag eines mit `data-menu-*`
  deklarierten Menüs beim ersten Abruf an. Ein Skript, das es neu zuweist (`var menuArray = new Array();`,
  `menuArray = {};`), ersetzt dies: Die deklarierten Kernmenüs (Kontextmenü, Unterelemente, Klassen-, Lesezeichenmenüs
  und deren Untermenüs) öffnen sich dann nicht mehr. Ergänzen Sie `menuArray`, weisen Sie es nie neu zu.
- **Plugins und Inline-PDF**: siehe `object-src` oben.

## Durchsetzen: Nonce, weitere Quellen, ein strengerer Begleiter, Rückkehr

Diese Einstellungen von `site.ini [ContentSecurityPolicy]` machen eine durchgesetzte Policy praktikabel. Keine davon
ändert die Policy einer neuen Installation, außer dass die Report-only-Policy der Administration eine Nonce trägt.

```ini
[ContentSecurityPolicy]
# sources added to a directive of Directives[], a list
AdditionalSources[]
# with Mode=enforce: a stricter Content-Security-Policy-Report-Only beside it
ReportOnlyDirectives[]
# designs that stay report-only under Mode=enforce
ReportOnlyDesigns[]
ReportOnlyDesigns[]=admin
ReportOnlyDesigns[]=admin2
ReportOnlyDesigns[]=admin_classic
Nonce=enabled
NonceDirectives[]
NonceDirectives[]=script-src
ReportURI=
ReportEndpoint=disabled
```

- **AdditionalSources[]** `=<directive> <sources>`: ergänzt Quellen in einer Direktive, die `Directives[]` bereits hat,
  zum Beispiel `AdditionalSources[]=script-src https://maps.googleapis.com`. Da es eine Liste ist, können die
  `site.ini.append.php` einer Extension und der Siteaccess jeweils ihre Hosts hinzufügen, ohne `'self'` oder die Hosts
  des jeweils anderen zu wiederholen. Ein Eintrag für eine Direktive, die `Directives[]` nicht hat, wird ignoriert: Er
  würde sonst eine Direktive beginnen, die den Rückfall auf `default-src` ersetzt. Duplikate werden entfernt.
- **ReportOnlyDirectives[]**: nur mit `Mode=enforce`. Ein zweiter Header, `Content-Security-Policy-Report-Only`, trägt
  `Directives[]` mit diesen Einträgen an deren Stelle (und denselben zusätzlichen Quellen, derselben Nonce und
  derselben Berichtsadresse). Der Browser setzt den ersten durch und meldet nur, was der zweite blockieren würde.
  Verwenden Sie ihn, um den Weg zu einer strengeren Policy zu vermessen, zum Beispiel `style-src 'self'`, während die
  durchgesetzte Policy noch `'unsafe-inline'`-Stile erlaubt:

  ```ini
  Mode=enforce
  Directives[style-src]='self' 'unsafe-inline'
  ReportOnlyDirectives[style-src]='self'
  ```
- **ReportOnlyDesigns[]**: Eine Anfrage, deren Design (`[DesignSettings] SiteDesign`, nachdem der UI-Switch die Wahl
  des Benutzers angewendet hat) aufgeführt ist, erhält die Report-only-Policy, auch wenn der Siteaccess sie durchsetzt.
  Die klassische Administration (`admin` und `admin2`, das deren Pagelayout rendert) hat Inline-Skripte in vielen
  Templates; ein Benutzer, der sie mit dem UI-Switch auswählt, behält eine funktionierende Seite.
- **Nonce**, **NonceDirectives[]**: Mit `Nonce=enabled` erhält jede Anfrage eine Nonce (16 Zufallsbytes, base64url),
  die als `'nonce-...'` jeder Direktive aus `NonceDirectives[]` hinzugefügt wird. Ein Template gibt sie mit dem Operator
  `csp_nonce()` aus:

  ```html
  <script nonce="{csp_nonce()}">...</script>
  ```

  Der Operator gibt eine leere Zeichenkette zurück, wenn die Policy keine Nonce trägt. Die Nonce wird keiner Direktive
  hinzugefügt, die `'unsafe-inline'` erlaubt, weil Browser `'unsafe-inline'` neben einer Nonce ignorieren. So bleibt ein
  Siteaccess, der `'unsafe-inline'` noch braucht (TinyMCE 3), funktionsfähig. Die persistenten Worker von Velocity
  erhalten pro Anfrage eine neue Nonce (der Kernel setzt sie zu Beginn jeder Anfrage zurück). **Geben Sie
  `csp_nonce()` nie innerhalb eines `{cache-block}`, eines im View-Cache gespeicherten Templates oder eines statischen
  Caches aus**: Die zwischengespeicherte Kopie trüge die Nonce einer anderen Anfrage, und der Browser würde das Skript
  blockieren. Bevorzugen Sie in dieser Reihenfolge: eine mit `ezscript` geladene Datei (ein Skript im Head läuft vor
  der ersten Darstellung, es flackert also nicht), einen Hash eines statischen Inline-Skripts
  (`AdditionalSources[]=script-src 'sha256-...'`), dann die Nonce, nur für nicht zwischengespeicherte Ausgaben. Der
  HTTP-Cache des Kernels speichert eine Seite mit ihren Headern, sodass eine zwischengespeicherte Seite und ihre Nonce
  zusammenbleiben.
- **ReportURI**, **ReportEndpoint**: Mit `ReportEndpoint=enabled` und leerem `ReportURI` gehen die Berichte an die
  Ansicht `csp/report` des Siteaccess, die pro Verstoß eine Zeile nach `var/log/csp.log` protokolliert (Schlüssel `csp`
  im Log-Viewer). Eine absolute Berichtsadresse wird zusätzlich als Reporting-Endpunkt `csp-endpoint` angekündigt, mit
  dem Header `Reporting-Endpoints` und der Direktive `report-to`. Chrome sendet an diesen, Firefox an `report-uri`.
- `allowsInlineScripts()`: `expContentSecurityPolicy::allowsInlineScripts()` teilt Code (zum Beispiel einem Editor,
  der seine Engine wählt) mit, ob Inline-Skripte und Handler-Attribute unter der Policy des aktuellen Siteaccess
  ausgeführt werden.

**Rückkehr.** Setzen Sie `Mode=report-only` im Siteaccess, leeren Sie den INI-Cache (`php bin/php/ezcache.php
--clear-tag=ini`), laden Sie PHP-FPM neu und starten Sie Velocity neu. Sonst ändert sich nichts: Dieselbe Policy wird
als `Content-Security-Policy-Report-Only` gesendet, und der strengere Begleiter wird nicht mehr gesendet.

Der ausgelieferte Standard bleibt `Mode=auto`, report-only in der Administration. Das Durchsetzen ist eine Entscheidung
pro Installation, die nach der obigen Prüfung getroffen wird.

**Inline-Wächter und Editoren.** Kein Pagelayout der Administration benötigt eine Nonce oder einen Hash: admin4 und
admin4l führen ihren Code vor der ersten Darstellung aus `a4_head.js` aus, admin3 aus `admin3_head.js`, beide im Head
geladen. Der Online-Editor bietet TinyMCE 3 nicht an, wo die Policy ohne `'unsafe-inline'` in `script-src`
durchgesetzt wird (TinyMCE 3 schreibt Handler-Attribute); er weicht dann auf TinyMCE 8 aus. Ein Siteaccess, der
TinyMCE 3 behalten muss, setzt für sich `Directives[script-src]='self' 'unsafe-inline'` und
`Directives[style-src]='self' 'unsafe-inline'`. Die ausgelieferte Policy nennt außerdem `worker-src 'self'`, sodass
`AdditionalSources[]=worker-src blob:` einen Blob-Worker erlauben kann, wo eine Funktion einen benötigt.

**Eine Ansicht mit eigener Policy.** Der Kernel sendet seine Header, bevor die Ansicht läuft. Eine Ansicht, die mit
PHPs `header()` eine eigene `Content-Security-Policy` sendet (das Datenbankwerkzeug AdminNeo von sevenx_dse, mit eigener
Nonce), ersetzt für diese Antwort die durchgesetzte Policy des Kernels, unter PHP-FPM wie unter Exponential Velocity.
Das ist beabsichtigt: Die Policy der Ansicht passt zu ihren eigenen Inline-Skripten. Der strengere Report-only-Begleiter
ist ein eigener Header; die Ansicht von AdminNeo entfernt ihn mit `header_remove()`, sodass ihre Seite nur die Policy
von AdminNeo trägt. Eine Ansicht, die zusätzlich die Policy des Kernels möchte, fügt ihren Header mit
`header( ..., false )` hinzu.
Als Sicherheitsnetz für einen Server, der beide Header behält, führen Sie eine solche Ansicht in `ReportOnlyViews[]`
(`module/view`) auf; der Kernel sendet seine Policy für diese Ansicht dann nur meldend, sodass die eigene Policy der
Ansicht die einzige durchgesetzte ist. sevenx_dse (ab 1.1.13) liefert diese Zeile in seinen eigenen Einstellungen aus:

```ini
[ContentSecurityPolicy]
ReportOnlyViews[]=dse/adminneo
```

`'report-sample'` in `script-src` und `style-src` (zum Beispiel `Directives[script-src]='self' 'report-sample'`) lässt
Browser die ersten 40 Zeichen eines blockierten Inline-Skripts oder -Stils mit dem Bericht senden; `csp.log` zeigt sie
als `sample="..."`. Es ändert nichts daran, was die Policy erlaubt.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
