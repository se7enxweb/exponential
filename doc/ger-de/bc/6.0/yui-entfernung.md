# YUI aus Exponential entfernt

Lesen Sie diese Seite, wenn Ihre Templates, Designs oder Erweiterungen YUI verwenden (die Yahoo! User Interface
Library, YUI 2 und YUI 3) oder wenn Sie Admin-Templates oder -Styles überschreiben. Exponential 6.0.15 liefert YUI
weder im Kernel noch in einem Design noch in einer seiner Erweiterungen aus, lädt es nicht und ruft es nicht auf. Alles,
was YUI verwendet hat, läuft auf jQuery 4 und Exponential UI (Erweiterung `expui`). Code, der noch nach YUI fragt,
erhält nichts und muss daher umgestellt werden.

Die klassische graue Administrationsoberfläche steht weiterhin zur Verfügung, ohne YUI und mit dem gewohnten
Aussehen, als Siteaccess `classic`.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Nirgends mehr YUI. ezjscore 1.5.0 entfernt die YUI-Bibliotheken und die Packer-Schlüssel `ezjsc::yui*`. |
| Wer betroffen ist | Templates und Erweiterungen, die `ezjsc::yui2`, `ezjsc::yui3` oder `ezjsc::yui3io` laden oder `YUI(...)` / `YAHOO.*` verwenden; Overrides der umbenannten Templates, Klassen und Stylesheets. |
| Wie Sie es prüfen | `grep -rnE "ezjsc::yui\|YUI\(\|YAHOO\.\|YUI3_config\|YUILoader\|yui3-js-enabled" extension/ design/ settings/` |
| Wie Sie es beheben | Folgen Sie den Schritten 1 bis 5 unter „So beheben Sie es“. |

## Was sich geändert hat

| Bereich | Vorher | Jetzt |
|---|---|---|
| Admin-Seiten (`admin`, `admin3`, `admin4`, `classic`) | YUI 3 und 2 auf jeder Seite geladen über `design.ini [JavaScriptSettings] BackendJavaScriptList` | jQuery 4 + Exponential UI (`exp::*`); keine YUI-Anfrage, kein Objekt `YUI` oder `YAHOO` |
| Tabelle der Unterelemente, Upload-Dialoge, Datumsfelder, einklappbare Menüs, feststehende Bearbeitungsleiste, automatisches Speichern, Seite der Veröffentlichungswarteschlange | YUI mit einer Exponential-UI-Version daneben | nur Exponential UI |
| ezjscore | mitgeliefertes YUI 2.8.2 und 3.17.2; Packer-Schlüssel `ezjsc::yui2`, `ezjsc::yui3`, `ezjsc::yui3io` | **1.5.0**: entfernt; `PreferredLibrary=jquery` |
| Logos | Bilder des eZ-Logos in den Designs admin und standard | dieselben Dateien zeigen das Exponential-Logo |
| Debug-Ausgabe | mit der Überschrift „eZ debug“ | mit der Überschrift „Exp Debug“ |

### Releases von Erweiterungen ohne YUI

Die `composer.json` im Wurzelverzeichnis verlangt diese Versionen.

| Erweiterung | Version | Hinweise |
|---|---|---|
| ezjscore | 1.5.0 | YUI-Bibliotheken, Schlüssel `ezjsc::yui*` und Einstellungen `[YUI3]` entfernt (inkompatibel, siehe unten) |
| expui (Exponential UI) | 1.0.0.2 | die Ersatzbibliothek; ihre Kommentare verweisen nicht mehr auf YUI |
| ezautosave | 6.0.7 | automatisches Speichern in admin und ezwebin über `exp::autosave` |
| eztags | 2.4.9 | Tabelle der Unter-Tags über `exp::datatable`; `eztags_children_yui.tpl` umbenannt in `eztags_children_table.tpl` |
| ezstarrating | 6.0.6 | nur jQuery (`ezstarrating_yui3.js` entfernt) |
| ezmultiupload | 6.0.6 | Upload nur über `exp::upload` |
| ezwt | 6.0.8 | Drag and Drop auf der Sortierseite mit jQuery; Datumsfelder von ezdemo über `exp::datepicker` |
| ezwebin | 6.0.15 | Datumsfelder über `exp::datepicker` |
| ezflow | 6.1.4 | Seiteneditor, Block-Werkzeuge, In-Block-Schieben, Zeitplan-Dialog, Zeitleiste mit jQuery; Tab-Klassen `.ezpage-tabs*` |
| ezdemo | 6.0.8 | Galerien, Flyouts, Kampagnenblock mit jQuery |
| cjw_newsletter | 4.1.9 | totes YUI-Drag-and-Drop der admin2-Liste entfernt |
| sevenx_themes_simple | 1.0.20 | YUI-Einstellungen und Kalender-Styles entfernt |

## So prüfen Sie es

1. Durchsuchen Sie Ihren eigenen Code:

   ```bash
   grep -rnE "ezjsc::yui|YUI\(|YAHOO\.|YUI3_config|YUILoader|yui3-js-enabled" extension/ design/ settings/
   ```

   Keine Ausgabe bedeutet, dass nichts übrig ist. (Die mitgelieferten Erweiterungen sind bereits bereinigt; ein
   Treffer liegt in Ihrem eigenen Code oder in einem Override.)

2. In den Entwicklerwerkzeugen eines Browsers, auf einer beliebigen Admin-Seite: keine Anfrage, deren URL `yui`
   enthält; in der Konsole sind `typeof YUI` und `typeof YAHOO` gleich `"undefined"` und `typeof Exp` gleich
   `"object"`.

## So beheben Sie es

1. **Stellen Sie YUI-Code auf jQuery oder Exponential UI um.** `{ezscript_require( 'ezjsc::yui3' )}`, `'ezjsc::yui2'`
   und `'ezjsc::yui3io'` laden nichts mehr, und `YUI(...)`, `YAHOO.*`, `YUI3_config` und `YUILoader` sind nicht
   definiert.

   | YUI | Stattdessen verwenden |
   |---|---|
   | `ezjsc::yui3` + `ezjsc::yui3io`, `Y.io.ez('fn::args', ...)` | `ezjsc::jquery` + `ezjsc::jqueryio`, `$.ez('fn::args', post, callback)`; oder `exp::io`, `Exp.io.call(fn, args)` |
   | `Y.one`, `Y.all`, `Y.on`, `Y.Node` | jQuery `$(...)` |
   | YUI 2 DataTable / Paginator | `exp::datatable` (`$.fn.expDataTable`) |
   | YUI 2 Calendar, `ezdatepicker.js` | `exp::datepicker` (definiert `showDatePicker()`) |
   | `ezmodalwindow`, YUI Dialog / SimpleDialog | `exp::dialog` (`Exp.dialog`) |
   | `ezajaxuploader` | `exp::upload` + `expajaxuploader.js` (`$.fn.expAjaxUploader`) |
   | `ezcollapsiblemenu` | `exp::collapse` (`Exp.collapse`) |
   | `fixed_toolbar.js` | `exp::sticky` |
   | `ezautosubmit`, `ezcontentpreview` | `exp::autosave` (`Exp.autosave.AutoSubmit`, `Exp.autosave.Preview`) |
   | YUI DD (Drag and Drop) | native Drag-Ereignisse oder Pointer-Ereignisse mit jQuery |
   | `.yui3-js-enabled` an `<html>` | `.exp-js` (von Exponential UI gesetzt) |

   Die Schritt-für-Schritt-Anleitung mit Beispielen für jedes Modul ist
   `extension/expui/doc/CONVERTING_YUI_to_EXPUI.md` von Exponential UI; die API steht in
   `extension/expui/doc/API.md`.

2. **Frontend-Designs laden Exponential UI selbst.** Die Skriptliste der Administration wird dort nicht geladen:

   ```
   {exp_config()}
   {ezscript_require( array( 'ezjsc::jquery', 'exp::core::shared', 'exp::datepicker' ) )}
   {ezcss_require( array( 'exp/core.css', 'exp/datepicker.css' ) )}
   ```

3. **Benennen Sie Overrides und CSS für umbenannte Namen um.**
   - `eztags_children_yui.tpl` von eZ Tags heißt `eztags_children_table.tpl`.
   - Die Klassen `yui-dt*` der Papierkorb-Tabelle in der Administration heißen `admin-dt*`.
   - Die Tab-Klassen von ezflow heißen `.ezpage-tabs*`, und die Body-Klasse der Zeitleiste ist `ezflow-skin`.

4. **Stylesheets.** `theme/yui_datatable.css`, `yui_menu.css` und `yui_container.css` der Admin-Designs gibt es nicht
   mehr. Die eigenen Regeln der Administration daraus (Werkzeugleiste der Unterelemente, Tabellenoptionen, Masken)
   stehen in `theme/admin_datatable.css`.

5. **Einstellungen.** Overrides von `ezjscore.ini`, die `ExternalScripts[yui*]`, `LocalScripts[yui*]`,
   `LocalScriptBasePath[yui*]` oder `[YUI3]` setzen, können gelöscht werden. `PreferredLibrary=yui3` hat keine
   Bedeutung mehr.

## Die klassische graue Administrationsoberfläche (`classic`)

Der Siteaccess `classic` (erreichbar unter `/classic`) verwendet allein das ursprüngliche graue Design `admin`, ohne
zusätzliche Designs. Es ist frei von YUI und pixelgenau wie das Original: Die Screenshots jedes Widgets, das YUI
verwendet hat, wurden mit den YUI-Originalen verglichen (der Kalender wird gezeichnet wie der von YUI; die Seiten
Papierkorb und Setup sind identisch). Er hat außerdem erhalten, was die neueren Designs haben: ein linkes Menü, das
sich einklappen und durch Ziehen in der Breite ändern lässt (pro Benutzer gespeichert), und einen Inhaltsbaum, der
innerhalb des Menüs scrollt.

So fügen Sie ihn einer Installation hinzu:

1. Kopieren Sie die Einstellungen des Admin-Siteaccess nach `settings/siteaccess/classic/`.
2. Setzen Sie in `settings/siteaccess/classic/site.ini.append.php`:

   ```ini
   [DesignSettings]
   SiteDesign=admin
   AdditionalSiteDesignList[]
   ```

3. Fügen Sie `classic` zu `[SiteAccessSettings] AvailableSiteAccessList[]` und `RelatedSiteAccessList[]` sowie zu
   `[SiteSettings] SiteList[]` hinzu.
4. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

## In einem Satz

Exponential 6.0.15 ist im Kernel, in jedem Administrationsdesign und in jeder Erweiterung frei von YUI; die klassische
graue Administrationsoberfläche steht als Siteaccess `classic` zur Verfügung und läuft wie die anderen auf jQuery 4
und Exponential UI, mit dem gewohnten Aussehen.

## Verwandte Seiten

- [jQuery 4 in der Administration, und YUI ist weg](../../funktionen/6.0/jquery4-und-entfernung-von-yui.md)
- [ezjscore: JavaScript- und CSS-Packer, Serveraufrufe, jQuery](../../funktionen/6.0/erweiterungen/ezjscore.md)
- [Verhaltensänderungen vom 1. und 2. Oktober 2026](verhaltensaenderungen-2026-10.md)
- [März 2024](../../geschichte/2024/2024-03.md)
