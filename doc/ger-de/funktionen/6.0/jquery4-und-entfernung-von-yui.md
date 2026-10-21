# jQuery 4 in der Administrationsoberfläche, und YUI ist weg

Diese Seite ist für Administratoren, die eine Installation aktualisieren, und für Entwickler, deren Templates oder
Extensions JavaScript-Bibliotheken laden. Am 01.10.2026 und 02.10.2026 wechselte die Administrationsoberfläche zu jQuery 4
und Exponential UI (`exp::*`), und YUI wurde aus den Admin-Designs und aus dem mitgelieferten ezjscore entfernt. Die
Administrationsoberfläche lädt keine YUI-Anfrage mehr. Das installierte ezjscore ist 1.5.4
(`extension/ezjscore/ezinfo.php`).

Wenn eine Ihrer Websites oder Extensions noch YUI verwendet, folgen Sie den Schritten zur Aktualisierung in
[Entfernung von YUI](../../../eng-us/bc/6.0/yui-removal.md).

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

## Wie es begann: jQuery 3.7.1 im August

Am 16. August 2026 (`f7c57376aa`) ersetzte ezjscore das mitgelieferte jQuery 1.10.2 durch **jQuery 3.7.1** und
**jQuery Migrate 3.4.1**, und `ezjsc::jquery` lud Migrate für jeden Aufrufer, damit ältere Skripte weiter funktionierten.
Die Änderung im Oktober ging weiter zu jQuery 4. Die Dateien 3.7.1 und Migrate 3.4.1 blieben bis zum 14. Oktober 2026 in
`extension/ezjscore/design/standard/javascript/`; seitdem enthält der Ordner je ein Release jeder Bibliothek
(`ezjscoreVendoredLibrariesTest`).

## Verwandte Seiten

- [Entfernung von YUI (Schritte zur Aktualisierung)](../../../eng-us/bc/6.0/yui-removal.md)
- [ezjscore](extensions/ezjscore.md), [das Design admin4](../../../eng-us/features/6.0/admin4-design.md), [Exp-Debug-Leiste](exp-debug-leiste.md)
- [Online-Editor: TinyMCE 8](../../../eng-us/features/6.0/online-editor-tinymce8.md)
- [Die Sicherheitspatches vom August 2026](../../spezifikationen/6.0/sicherheitshaertung-2026-08.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [Oktober 2026](../../../eng-us/history/2026/2026-10.md), [August 2026](../../../eng-us/history/2026/2026-08.md), [März 2024](../../../eng-us/history/2024/2024-03.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
