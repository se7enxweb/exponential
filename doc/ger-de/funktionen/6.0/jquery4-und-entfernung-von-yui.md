# jQuery 4 in der Administrationsoberfläche, admin4 ohne jQuery auf jeder Seite, und YUI ist weg

Diese Seite ist für Administratoren, die eine Installation aktualisieren, und für Entwickler, deren Templates oder
Extensions JavaScript-Bibliotheken laden. Am 01.10.2026 und 02.10.2026 wechselte die Administrationsoberfläche zu jQuery 4
und Exponential UI (`exp::*`), und YUI wurde aus den Admin-Designs und aus dem mitgelieferten ezjscore entfernt. Die
Administrationsoberfläche lädt keine YUI-Anfrage mehr. Das installierte ezjscore ist 1.5.4
(`extension/ezjscore/ezinfo.php`). Seit dem 23. Oktober 2026 **laden admin4 und admin4l jQuery nur auf den Seiten,
die es anfordern**; die eigenen admin4-Seiten des Kernels fordern keines an (siehe
[admin4 und admin4l ohne jQuery](#admin4-und-admin4l-ohne-jquery), wo auch die Pakete stehen, die es noch
mitbringen). Die älteren Admin-Designs und die Website-Designs laden weiterhin jQuery 4.

Wenn eine Ihrer Websites oder Extensions noch YUI verwendet, folgen Sie den Schritten zur Aktualisierung in
[Entfernung von YUI](../../bc/6.0/yui-entfernung.md).

## Eine Installation in zwei Minuten prüfen

1. Öffnen Sie eine beliebige Admin-Seite bei geöffneter Browserkonsole. Erwartet: kein `YUI is not defined` und keine
   Migrate-Warnungen.
2. Listen Sie auf, was in Ihrem eigenen Code noch nach YUI fragt (es lädt jetzt nichts):

   ```bash
   grep -rn "ezjsc::yui" extension design
   ```

   Keine Ausgabe bedeutet, dass nichts mehr umzustellen ist.
3. Führen Sie nach einem Deployment, das eine Extension hinzufügt, `./console exp:velocity deploy --allow-root-user` aus.
   Mit der Option `--packer` leert es auch den Packer-Cache von ezjscore, direkt vor dem Template-Block-Cache (allein
   geleert, würden zwischengespeicherte Seitenköpfe der Administrationsoberfläche gepackte Dateien nennen, die es nicht
   mehr gibt). Es leert immer den Cache design_base, sodass die Templates einer neu aktivierten Extension gefunden
   werden.

## Was Sie bekommen

- **jQuery 4.0.0 mit jQuery Migrate 4.0.2 und jQuery UI 1.14.2** von `ezjsc::jquery` und `ezjsc::jqueryUI` (ezjscore
  1.4.0). Migrate ist standardmäßig der stille Build, sodass älterer Code weiter funktioniert, ohne die Konsole zu füllen;
  der meldende Build ist eine Einstellung entfernt. Die CDN-Einträge nennen dieselben Releases wie die lokalen Dateien
  (sie nannten früher jQuery 1.10.2 und jQuery UI 1.10.3). Die älteren Dateien jQuery 3.7.1, Migrate 3.4.1 und jQuery UI
  1.10.3 sind entfernt (14. Oktober 2026): Nichts in der Auslieferung lud sie, und jQuery UI 1.10.3 hat bekannte
  Sicherheitslücken.
- **Der eigene Code der Administrationsoberfläche ist sauber für jQuery 4.** Kurzformen für Ereignisse (`.click(fn)`,
  `.change(fn)`), `.bind()`, `.unbind()`, boolesches `.attr()` und `jQuery.trim()` wurden durch `.on()`, `.off()`,
  `.trigger()`, `.prop()` und `String.prototype.trim()` ersetzt, in den Knotenreitern, den Editoren für Klassen und
  Rollen, den Übersetzungen, den Platzierungen, der Extension-Liste, der Suche im Kopfbereich, der Breitensteuerung des
  linken Menüs und den Popups des Rich-Text-Editors.
- **Exponential UI ersetzt YUI** in der Unterelementtabelle (`exp::datatable`), den Upload-Dialogen (`exp::dialog`,
  `exp::upload`), den Datumsfeldern (`exp::datepicker`), den aufklappbaren Menüs (`exp::collapse`), der haftenden
  Bearbeitungsleiste (`exp::sticky`), dem automatischen Speichern und der Vorschau (`exp::autosave`) und der Statusseite
  des asynchronen Veröffentlichens (`Exp.io`).
- **YUI ist entfernt** aus den Designs `admin`, `admin3`, `admin4` und `standard` und aus ezjscore 1.5.0: die
  YUI-Bibliotheken, die Packer-Schlüssel `ezjsc::yui2`, `ezjsc::yui3`, `ezjsc::yui3io` und ihre Einstellungen.
- Jedes Logo-Bild der Kernel-Designs zeigt das Logo von Exponential; die Debug-Ausgabe hat die Überschrift „Exp Debug“.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `extension/ezjscore/settings/ezjscore.ini` | `eZJSCore` | `LoadFromCDN` | `disabled` (lokale Dateien) | Installation |
| `extension/ezjscore/settings/ezjscore.ini` | `eZJSCore` | `PreferredLibrary` | `jquery` | Installation |
| `extension/ezjscore/settings/ezjscore.ini` | `eZJSCore` | `ExternalScripts[jquery]`, `[jqueryMigrate]`, `[jqueryUI]` | `jquery-4.0.0.min.js`, `jquery-migrate-4.0.2.min.js`, `ui/1.14.2/jquery-ui.min.js` von code.jquery.com | Installation |
| `extension/ezjscore/settings/ezjscore.ini` | `eZJSCore` | `LocalScripts[jquery]`, `[jqueryMigrate]`, `[jqueryUI]` | `jquery-4.0.0.min.js`, `jquery-migrate-4.0.2.min.js`, `jquery-ui-1.14.2.min.js` | Installation |

Um jQuery 4 allein zu laden, lassen Sie `LocalScripts[jqueryMigrate]` leer.

## Weitere Korrekturen derselben Änderung

- Das Hochladen für Felder mit Objektbeziehungen läuft auf `exp::dialog` und `exp::upload`, wenn die Extension expui
  aktiv ist, über `expajaxuploader.js` (Kopien in `design/admin` und `design/admin4`). Die YUI-Version bleibt als
  Rückfall für Installationen ohne expui.
- Die Dialoge für eigene Tags und Tabellenzellen des Rich-Text-Editors riefen `.size()` auf, das schon jQuery 3 entfernt
  hatte.
- Der Daemon für asynchrones Veröffentlichen brach unter PHP 8 mit einem Typfehler ab, weil ein Signal-Handler die
  Signalinformationen als Array erhält.
- Der CSS-Packer machte aus `a :hover` `a:hover` (ein anderer Selektor). Das Leerzeichen vor einem Doppelpunkt wird
  jetzt nur innerhalb von Deklarationsblöcken entfernt.

## admin4 und admin4l ohne jQuery

**admin4 und admin4l laden nicht mehr auf jeder Seite jQuery** (23. Oktober 2026). `page_head_script.tpl` stellt
`ezjsc::jquery` und `ezjsc::jqueryio` nicht mehr voran; sein erstes `ezscript_load` beginnt mit `a4_url.js` und
`a4_dom.js`, dann folgen `BackendJavaScriptList[]` und die Skripte, die die Templates der Seite angefordert haben. Eine
Seite, die jQuery oder ein Modul von Exponential UI braucht, fordert es selbst an.

### Was im Kernel ohne jQuery läuft

- **Die Skripte auf jeder Seite**: die Menüleiste (`ezadmin_menubar.js`), der Baum der Inhaltsstruktur
  (`contentstructuremenu_dynamic.js`), die Kontextmenüs (`popupmenu/ezpopupmenu.js`), `a4_url.js`, `a4_dom.js`,
  `admin4.js` und `a4_actions.js`.
- **Knotenansicht**: die Reiter (`node_tabs.js`) und die Tabelle der Unterelemente (`ezajaxsubitems_expdatatable.js`,
  `subitems_priority_drag.js`, `page/children_detailed.js`) auf `Exp.datatable.create()`. `children.tpl` und
  `children_detailed.tpl` fordern `exp::core::shared`, `exp::io` und `exp::datatable` an.
- **Inhalt bearbeiten und Klasse bearbeiten**: `content/edit.tpl` und `class/edit.tpl` fordern `exp::sticky` für die
  Werkzeugleiste an; die Felder für Datum und Datum/Uhrzeit fordern `exp::datepicker` an; das Hochladen für Beziehungen
  fordert `exp::io`, `exp::dialog`, `exp::upload` und `expajaxuploader.js` an (Einstiegspunkt `window.expAjaxUploader`;
  `$.fn.expAjaxUploader` bleibt, wo jQuery auf der Seite ist); die Suche nach Beziehungen (`ezajaxrelations_jquery.js`,
  trotz seines Namens reines JavaScript) und der Status des asynchronen Veröffentlichens (`ezasynchronouspublishing.js`)
  fordern `exp::core::shared` und `exp::io` an. Die Seitenskripte teilen sich die Helfer von `a4_dom.js`.
- **Rich Text**: TinyMCE 8 bringt seine eigenen Skripte mit; ezoe fordert jQuery nur für die eingebaute Engine
  TinyMCE 3 an (`ezxmltext_ezoe.tpl`), und seine Popups haben ein eigenes Seitenlayout, das es lädt.
- **Dashboard**: der Block mit der Aktivität der Community (`dashboard_community_activity.js`).
- **Bildbearbeitung**: expimageeditor läuft ohne jQuery.
- Reduzierte Bewegung schaltet auch die Effekte von jQuery ab, wenn eine Seite jQuery nach `admin4.js` lädt.

Exponential UI 1.0.0.15 (`se7enxweb/expui`) läuft ohne jQuery: `exp::core::shared` startet ohne es (`Exp.$` bleibt
undefiniert), `exp::io` verwendet `fetch()`, und jedes Modul hat einen Weg in reinem JavaScript mit Einstiegspunkten für
Elemente (`Exp.datatable.create()`, `Exp.sticky.start()`, `Exp.dialog`, `Exp.upload.create()`, ...). Nur `exp::compat`
braucht noch jQuery.

### jQuery oder ein Modul von Exponential UI anfordern

Ein Template, dessen Skripte jQuery oder ein `exp::`-Modul brauchen, fordert sie selbst an, **außerhalb jedes
cache-block**:

```
{ezscript_require( array( 'ezjsc::jquery', 'ezjsc::jqueryio', 'my_admin_page.js' ) )}
{ezscript_require( array( 'exp::core::shared', 'exp::io', 'exp::datatable' ) )}
```

`pagelayout.tpl` von admin4 und admin4l bindet `page_head_script.tpl` außerhalb des cache-block des Kopfes ein, sodass
der Kopf bei jeder Anfrage ausgibt, was die Seite angefordert hat, und eine später in der Seite gestellte Anforderung
(zum Beispiel durch das linke Menü) ihre Skripte an Ort und Stelle ausgibt. Eine Anforderung innerhalb eines
`{cache-block}` läuft bei einem Cache-Treffer nicht, und die Seite verliert dann ihre Skripte.

### Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/design.ini` | `JavaScriptSettings` | `BackendJavaScriptList[]` | Skripte auf jeder Admin-Seite; **nur Skripte, die ohne jQuery laufen** (kein jQuery, kein `exp::`-Modul) | Installation, Siteaccess |
| `settings/design.ini` | `JavaScriptSettings` | `ClassicBackendJavaScriptList[]` | leer | Installation, Siteaccess |

`ClassicBackendJavaScriptList[]` wird von den älteren Admin-Designs gelesen, zu denen ein Benutzer wechseln kann
(`settings/uiswitch.ini`): `admin` (mit `admin2`) und `admin3` laden auf jeder Seite jQuery, dann
`BackendJavaScriptList[]` und `ClassicBackendJavaScriptList[]`. admin4 und admin4l lesen sie nicht. Eine Extension, die
ein jQuery-Skript oder ein `exp::`-Modul in `BackendJavaScriptList[]` eingetragen hat, verschiebt es für die älteren
Designs nach `ClassicBackendJavaScriptList[]` und fordert es in ihren eigenen admin4-Templates an. Das Design Admin UI
(`adminui`, das Paket `se7enxweb/exp_adminui`) hat ein eigenes `page_head_script.tpl` und liest die neue Liste, sobald
sein Paket es tut.

### Die mit Composer installierten Extensions

Die Pakete der Root-`composer.json` gehören nicht zu diesem Repository, und jedes ändert sich mit seinem eigenen
Release. Bis dahin lädt admin4 weiterhin, was sie auf jede Seite setzen:

| Paket | Was admin4 noch jQuery bringt | Änderung |
|---|---|---|
| eztags (`~2.4.24`) | `BackendJavaScriptList[]` nennt `ezjsc::jquery`, `ezjsc::jqueryUI`, jqModal, jsTree und die Plugins `jquery.eztags*.js`: jQuery ist auf jeder admin4-Seite, solange eztags aktiv ist | die admin4-Skripte ohne jQuery (eztags PR #3) und das Verschieben der Plugins nach `ClassicBackendJavaScriptList[]` mit Anforderungen in den Tags-Templates |
| expui (`~1.0.0.15`) | `BackendJavaScriptList[]` nennt die `exp::`-Module: kein jQuery, aber jedes Modul auf jeder Seite | das Verschieben der Module nach `ClassicBackendJavaScriptList[]` |
| xrowmetadata (`~1.4.16`) | `admin-maxlength.js` (jede Seite) ruft `jQuery( document ).ready()` auf | der Zähler in reinem JavaScript |
| expai (`~0.0.1.17`) | `expai.js` (jede Seite) und die Vector-Seite laufen auf jQuery | expai PR #11 und #12 |
| ezautosave (`~6.0.15`) | das automatische Speichern der Admin läuft auf jQuery, ohne es anzufordern | ezautosave PR #7 |
| ezmultiupload (`~6.1.12`) | die Upload-Seiten fordern jQuery selbst an | ezmultiupload PR #12 |
| ezstarrating (`~6.0.15`) | `ezsrrating.tpl` fordert jQuery selbst an | ezstarrating PR #8 |
| explayouts_ui_api (`~1.3.29`) | der Layout-Editor (`app.tpl`) lädt jQuery und jQuery UI selbst | explayouts_ui_api PR #12 |

Solange eztags 2.4.24 aktiv ist, bricht nichts: jQuery bleibt über seine Liste auf jeder admin4-Seite. Sobald eztags es
nicht mehr aufführt, brauchen xrowmetadata, expai und ezautosave ihre Releases ohne jQuery.
`AdminJQueryOnDemandTest::testInstalledPackagesRequestTheJQueryTheyNeed` (JQ-13) nennt in seiner Skip-Meldung, was
noch aussteht, und schlägt fehl, sobald keine Liste für jede Seite mehr jQuery bringt und ein Paket sich noch darauf
verlässt.

### Was noch jQuery verwendet

Außerhalb von admin4 und admin4l wird jQuery 4 weiterhin geladen:

- ezjscore liefert weiterhin jQuery 4.0.0, Migrate 4.0.2 und jQuery UI 1.14.2 (`ezjsc::jquery`, `ezjsc::jqueryUI`,
  `ezjsc::jqueryio`, `$.ez`); `PreferredLibrary=jquery` ist unverändert.
- Die älteren Admin-Designs `admin`, `admin3` und `adminui`, auf jeder Seite.
- Die Website-Designs und Themes, TinyMCE 3 von ezoe und seine Popups, das Portal-Design `expportal_jquery` von
  expservices, `exp::compat` und die obigen Pakete bis zu ihren Releases.

Neuer Code braucht kein jQuery: reines JavaScript, `a4_dom.js` in admin4 und Exponential UI, wo ein Widget oder ein
Serveraufruf nötig ist.

### Schutztests

- `tests/tests/design/AdminJQueryOnDemandTest.php` (JQ-01 bis JQ-13; der Dateikopf listet jede Regel): Der Kopf von
  admin4 lädt kein jQuery, `BackendJavaScriptList[]` des Kernels nennt nichts, was jQuery oder Exponential UI braucht,
  jedes Admin-Template, das ein Skript lädt, das jQuery aufruft, fordert es an, keine solche Anforderung steht in einem
  cache-block, die Seitenlayouts geben die Skripte des Kopfes außerhalb der cache-blocks aus, und die genannten Seiten
  fordern nur an, was sie verwenden.
- `tests/tests/design/AdminPageScriptsWithoutJQueryTest.php`: die kleinen Seitenskripte von admin4 und `a4_dom.js`.
- `tests/tests/design/AdminPlainScriptsExpUIPacksTest.php`: Jede Seite, die ein Skript auf Exponential UI lädt, bekommt
  die `exp::`-Pakete, die es aufruft, auch im Design admin über `ClassicBackendJavaScriptList[]`.

## Wie es begann: jQuery 3.7.1 im August

Am 16. August 2026 (`f7c57376aa`) ersetzte ezjscore das mitgelieferte jQuery 1.10.2 durch **jQuery 3.7.1** und
**jQuery Migrate 3.4.1**, und `ezjsc::jquery` lud Migrate für jeden Aufrufer, damit ältere Skripte weiter funktionierten.
Die Änderung im Oktober ging weiter zu jQuery 4. Die Dateien 3.7.1 und Migrate 3.4.1 blieben bis zum 14. Oktober 2026 in
`extension/ezjscore/design/standard/javascript/`; seitdem enthält der Ordner je ein Release jeder Bibliothek
(`ezjscoreVendoredLibrariesTest`).

## Verwandte Seiten

- [Entfernung von YUI (Schritte zur Aktualisierung)](../../bc/6.0/yui-entfernung.md)
- [ezjscore](erweiterungen/ezjscore.md), [das Design admin4](admin4-design.md), [Exp-Debug-Leiste](exp-debug-leiste.md)
- [Online-Editor: TinyMCE 8](online-editor-tinymce8.md)
- [Die Sicherheitspatches vom August 2026](../../spezifikationen/6.0/sicherheitshaertung-2026-08.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../bc/6.0/verhaltensaenderungen-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [Oktober 2026](../../geschichte/2026/2026-10.md), [August 2026](../../geschichte/2026/2026-08.md), [März 2024](../../geschichte/2024/2024-03.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
