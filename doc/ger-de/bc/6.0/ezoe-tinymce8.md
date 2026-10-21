# Die Engines des Online-Editors: TinyMCE 3 und TinyMCE 8

Lesen Sie diese Seite, wenn Ihre Redakteure den Online-Editor (ezoe) verwenden, um Attribute mit XML-Text
(`ezxmltext`) zu bearbeiten, oder wenn Sie TinyMCE 8 oder einen eigenen Editor anbieten möchten. Seit Exponential 6.0.15
läuft ezoe auf einer Editor-Engine, die aus einer Registry gewählt wird. Für eine bestehende Installation ändert sich
nichts: Der Standard bleibt TinyMCE 3. Eine Prüfung auf dem Server ist neu: Der Upload des Einbettungsdialogs lehnt
einige Dateitypen ab (siehe „Prüfung beim Upload“).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Zwei Engines werden ausgeliefert: TinyMCE 3.5.12 (Standard) und TinyMCE 8.9.2 (optional, GPL-2.0-or-later, `license_key: 'gpl'`, kein Aufruf in die Cloud). `ezoe/upload` prüft Dateitypen. |
| Wer betroffen ist | Websites, die auf TinyMCE 8 umstellen; Redakteure, die ungewöhnliche Dateitypen über den Einbettungsdialog hochladen. |
| Wie Sie es prüfen | `grep -n "EditorEngine\|EngineSwitch\|UploadExtensionCheck" extension/ezoe/settings/ezoe.ini` |
| Wie Sie es beheben | Fügen Sie fehlende Upload-Typen zu `UploadFileExtensions[]` hinzu. Wechseln Sie die Engine mit `EditorEngine` oder pro Benutzer. |

Beide Engines schreiben dasselbe XHTML, das `eZOEXMLInput` und `eZOEInputParser` lesen, daher ist das gespeicherte XML
dasselbe, gleich welche Engine ein Redakteur verwendet.

## TinyMCE 8 in einer Minute ausprobieren

1. Öffnen Sie als Redakteur `/user/preferences/set/ezoe_engine/tinymce8`.
2. Bearbeiten Sie einen Artikel. Der Editor ist TinyMCE 8.
3. Um zurückzukehren, öffnen Sie `/ezoe/engine` und wählen TinyMCE 3, oder setzen Sie auf den Standard der Website
   zurück.

Um TinyMCE 8 zum Standard für alle ohne eigene Einstellung zu machen, setzen Sie `EditorEngine=tinymce8` (siehe
„Einstellungen“).

## Einstellungen

Datei `extension/ezoe/settings/ezoe.ini`, Block `[EditorSettings]`. Jeder Schlüssel lässt sich wie bei jeder INI-Datei
pro Siteaccess überschreiben.

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `EditorEngine` | `tinymce3` | Die Engine jedes Benutzers ohne eigene Einstellung. |
| `EngineSwitch` | `disabled` | `enabled` zeigt den Umschalter der Engine unter dem Editor: eine Gruppe namens „Editor“ mit jeder verfügbaren Engine, Umschaltflächen, die verwendete gedrückt (`aria-pressed="true"`), jede andere eine Absendeschaltfläche, die auf sie umschaltet. |
| `Engines[<id>]` | `tinymce3`, `tinymce8` | Die Registry: Kennung = Klasse, die `expOEEditorEngine` implementiert. |
| `UploadFileExtensions[]` | Bilder, PDF, Office, Text, ZIP, Audio, Video | Dateitypen der Registerkarte Hochladen des Einbettungsdialogs. |
| `UploadExtensionCheck` | `engine` | Prüfung dieser Typen auf dem Server: `engine` (nur wenn die Engine des Benutzers nicht tinymce3 ist), `always`, `disabled`. |

Der Block `[Engine_tinymce8]` enthält `ToolbarMap[<[EditorLayout] button>]=<toolbar item>` und
`ExternalPlugins[<name>]=<design path>`. Die Engine-Klasse liest sie; sie sind nicht fest im Template eingetragen.

### Tabellendialog

`[Engine_tinymce8]` `TableDialog=classic|modern` wählt den Dialog von „Tabelle einfügen“ und „Tabelleneinstellungen“:

| Wert | Dialog |
|---|---|
| `classic` (Standard) | Das Design des TinyMCE-3-Dialogs aus `tag_table.tpl`: Fenstertitel „Neues Tag <table>“ / „Tag <table> bearbeiten“, Registerkarte Eigenschaften, das anklickbare Größenraster 6 x 5 mit Spalten und Zeilen (nur bei neuen Tabellen), Breite und Rahmen mit einer Auswahl px / %, Klasse, dann die benutzerdefinierten Attribute aus `content.ini [table]` (Summary (WAI), Beschriftung), OK und Abbrechen. Texte und Hilfetitel sind die Übersetzungen aus `design/standard/ezoe`, daher braucht Deutsch keine neuen Zeichenketten. Der Dialog ist ein TinyMCE-8-Fenster mit einem kleinen Adapter in `plugins/eztable/plugin.js` (die TinyMCE-3-Seite selbst hängt von `tinyMCEPopup` und der API von TinyMCE 3 ab und läuft nicht in TinyMCE 8). |
| `modern` | Der Formulardialog von TinyMCE 8 (Zeilen, Spalten, Breite, Rahmen, Klasse und die benutzerdefinierten Attribute als einfache Felder). |

Beide schreiben dasselbe Markup (Klasse, `width`, `border`, `customattributes`), daher ist das gespeicherte ezxml
identisch. Die Dialoge für Zellen und Zeilen sind von der Einstellung nicht betroffen. Der Wert erreicht den Editor als
Option `ez_table_dialog`.

## Welche Engine ein Redakteur erhält

`expOEEditor::resolve()` nimmt das Erste aus: der Benutzereinstellung `ezoe_engine`, dem `EditorEngine` des
Siteaccess, dem globalen `EditorEngine`, dann `tinymce3`. Eine unbekannte, defekte oder nicht verfügbare Wahl wird
übersprungen, nie ein Fehler.

### Unter einer durchgesetzten Content Security Policy

TinyMCE 3 kann nicht laufen, wo der Siteaccess seine Content Security Policy ohne `'unsafe-inline'` in `script-src`
durchsetzt (`site.ini [ContentSecurityPolicy] Mode=enforce`): Sein Bearbeitungs-Template startet es mit einem
Inline-Skript, und `tiny_mce.js` und seine Dialoge schreiben selbst `onclick`-Attribute und `javascript:`-Links, die
weder eine Nonce noch ein Hash erlauben kann. Dort ist die Engine für diese Anfrage **gesperrt**:

- eine Benutzereinstellung, `EditorEngine` oder `ForceEngine`, die `tinymce3` nennt, wird übersprungen; ist die
  konfigurierte Engine gesperrt, erhalten Redakteure die erste Engine der Registry, die läuft (im Auslieferungszustand
  TinyMCE 8), sodass das Feld nie ohne Editor bleibt;
- der Umschalter der Engine und die Seite `/ezoe/engine` lassen sie bei der Auswahl weg (die Seite führt sie mit dem
  Status „Nicht verwendbar“ und dem Grund auf), und ihre Wahl wird abgelehnt;
- unter report-only (dem ausgelieferten `Mode=auto`) und mit `'unsafe-inline'` in `script-src` ändert sich nichts.

Eine Engine teilt mit der optionalen Methode `needsInlineScripts()` mit, dass sie Inline-Skripte braucht
(`expOETinyMCE3Engine`: true; eine auf `expOEEditorEngineBase` aufgebaute Engine liest
`[Engine_<id>] NeedsInlineScripts=true`, false, wenn nicht gesetzt). `expOEEditor::blockedByPolicy( $engine )` und
`expOEEditor::inlineScriptsAllowed()` (das `expContentSecurityPolicy::allowsInlineScripts()` fragt) sind die
Prüfungen. Ein Siteaccess, der mit TinyMCE 3 bearbeiten muss, setzt nur für sich:

```ini
# settings/siteaccess/<siteaccess>/site.ini.append.php
[ContentSecurityPolicy]
Directives[script-src]='self' 'unsafe-inline'
Directives[style-src]='self' 'unsafe-inline'
```

Der Kernel sendet dann in diesem `script-src` keine Nonce (eine Nonce würde `'unsafe-inline'` im Browser ausschalten).

Ein Benutzer wählt mit:

- der Seite `/ezoe/engine` (eine Tabelle jeder registrierten Engine mit Version, Beschreibung und Status, auch der
  nicht verwendbaren, und eine Auswahl über die verfügbaren; braucht die Richtlinie `ezoe/editor`);
- dem Umschalter der Engine unter dem Editor (`EngineSwitch=enabled`);
- der URL `/user/preferences/set/ezoe_engine/tinymce8`;
- dem entfernten Dienst `expeditor::set` (POST `engine`; leer bedeutet den Standard der Website).

Jede Wahl über die Seite, die Schaltfläche oder den Dienst schreibt das Audit-Ereignis `content.ezoe.engine.change`
(Kanal `content`; Engine vorher und nachher).

## Prüfung beim Upload

- Der Einbettungsdialog lädt über `ezoe/upload` und `eZContentUpload` hoch (Klasse durch `upload.ini` festgelegt;
  Zugriff über die Richtlinie `ezoe/relations` und `canEdit()`).
- Seit 6.0.15 lehnt diese View eine Datei ab, deren Typ nicht in `UploadFileExtensions[]` steht (siehe
  `UploadExtensionCheck`), sowie jeden Namen, der eine ausführbare Endung enthält (`shell.php.jpg`), weil eine Anfrage
  den Dialog überspringen kann.
- Wird der Upload eines Redakteurs abgelehnt, fügen Sie den Typ in einem Override zu `UploadFileExtensions[]` hinzu.

### Upload von einer URL

Die Registerkarte Hochladen nimmt auch die Adresse einer Datei an („Von einer URL“; Feld `uploadUrl` von
`ezoe/upload`, Klasse `expOEUrlFetcher`). Einstellungen in `ezoe.ini [EditorSettings]`:
`UploadFromUrl=enabled|disabled`, `UploadFromUrlMaxSize=145M` (nur diese Grenze, `upload_max_filesize` von PHP gilt
nicht), `UploadFromUrlTimeout=300`. Sicherheit: nur http/https, keine Zugangsdaten, jede aufgelöste Adresse muss
öffentlich sein und ist diejenige, zu der verbunden wird, höchstens 3 Weiterleitungen, jede erneut geprüft, die Größe
wird beim Streamen in eine temporäre Datei unter `var/tmp/ezoe_url/` durchgesetzt, die Endung gegen
`UploadFileExtensions[]` und der Inhalt mit finfo geprüft. `eZContentUpload::handleLocalFile()` hat ein optionales
Argument `$publish` erhalten (Standard true), sodass die View wie bei `handleUpload()` die Attribute vor dem
Veröffentlichen setzen kann. Die Änderung am Kernel braucht `exp:velocity deploy --kernel`. Tests:
`tests/tests/extension/ezoe/expOEUrlFetcherTest.php`.

## Weitere Hinweise zum Server

- Die Plugins von TinyMCE 8 rufen nur vorhandene Endpunkte auf (Views von ezoe, ezjscore-Funktionen von ezoe). Sie
  fügen keine ezjscore-Funktion und keinen neuen Endpunkt ohne Authentifizierung hinzu.
- Die Leiste Exp Debug zeigt und ändert `EditorEngine` und `EngineSwitch` (Gruppe Extensions).
- `setup/rad` zählt jeden Eintrag von `Engines[]` als Erweiterungspunkt („Editor-Engines des Online-Editors“) und zeigt
  einen Eintrag, dessen Klasse fehlt oder kein `expOEEditorEngine` ist, als defekt an.

## Entfernte Dienste (`ezjscore/call/expeditor::<service>`)

| Dienst | Zugriff | Schreibt | Gibt zurück |
|---|---|---|---|
| `engines` | `ezoe/editor` | nein | registrierte Engines, die aktuelle, den Standard, Probleme |
| `get` | `ezoe/editor` | nein | Benutzereinstellung, verwendete Engine, Standard, Umschalter eingeschaltet |
| `set` | `ezoe/editor` | POST + Token | wählt die Engine des aktuellen Benutzers |
| `uploadExtensions` | `ezoe/editor` | nein | erlaubte Endungen, Prüfmodus, ob er durchgesetzt wird |
| `config` | `ezoe/editor` | nein | Engine, Zuordnung der Werkzeugleiste, Plugins, Schaltflächen des Layouts, Upload-Typen |

## Erweiterungspunkte

1. **Eine Engine:** eine Klasse und eine INI-Zeile.
2. **Einträge der Werkzeugleiste und Plugins** einer Engine: `[Engine_<id>]` `ToolbarMap[]` und `ExternalPlugins[]`.
3. **Upload-Typen:** `UploadFileExtensions[]`.
4. **Audit:** der Zweig `expOEAuditBranch` (`audit.ini.append.php`).

### Beispiel: eine dritte Engine hinzufügen

```php
// extension/myeditor/classes/myeditorengine.php
class myEditorEngine extends expOEEditorEngineBase
{
    public function identifier() { return 'myeditor'; }
    public function label()      { return 'My editor'; }          // translated in design/standard/ezoe
    public function isAvailable() { return is_file( __DIR__ . '/../design/standard/javascript/my.js' ); }
    public function template()   { return 'design:content/datatype/edit/ezxmltext_myeditor.tpl'; }
    public function version()    { return '1.0.0'; }               // optional, shown on /ezoe/engine
    public function description() { return 'My own editor.'; }    // optional, translated in design/standard/ezoe
}
```

```ini
# extension/myeditor/settings/ezoe.ini.append.php
[EditorSettings]
Engines[myeditor]=myEditorEngine
[Engine_myeditor]
ToolbarMap[bold]=bold
```

Dann:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Das Template erhält `attribute`, `input_handler`, `attribute_base` und `editorRow`; `$input_handler.engine` liefert
Kennung, Beschriftung, Zuordnung der Werkzeugleiste, Plugins und Konfiguration. Die Engine erscheint jetzt auf
`/ezoe/engine`, in den Umschaltflächen, in `expeditor::engines` und in `setup/rad`. Eine Engine, deren `template()`
`''` zurückgibt, wird mit dem eingebauten TinyMCE-3-Markup von `ezxmltext_ezoe.tpl` gerendert.

`version()` und `description()` sind optional (sie gehören nicht zu `expOEEditorEngine`, sodass Engines, die vor ihnen
geschrieben wurden, weiter funktionieren): `expOEEditor::details()` fragt danach, wenn eine Engine sie hat, und
`expOEEditorEngineBase` beantwortet beide aus `[Engine_<id>] Version` und `Description`. `/ezoe/engine` zeigt sie mit
dem Status jeder Engine: verfügbar, nicht verfügbar (`isAvailable()` ist false: ihre Dateien fehlen) oder warum sie
nicht verwendet werden kann.

## Registerkarten zum Durchsuchen (Einbettungs- und Linkdialog)

Die Registerkarten zum Durchsuchen navigieren wie die Dialoge von TinyMCE 3 durch den ganzen Inhaltsbaum: eine Zeile
mit den Wurzeln der obersten Ebene, die der Benutzer lesen darf (Wurzel von Inhalten, Medien und Benutzern sowie das
bearbeitete Objekt, sobald es veröffentlicht ist), ein Pfad, der bei „Top level nodes“ (Knoten 1) beginnt, mit einem
Link für jede Ebene, ein Link „Hinauf“ zum Elternknoten, Blättern innerhalb großer Ordner und die Auswahl eines Objekts
auf jeder Ebene. Die Wurzeln kommen aus `ez_settings.browse_roots`, im Template aus content.ini `[NodeSettings]` und
der Richtlinie `ezoe/browse` gebaut.


## Registerkarte Lesezeichen (Linkdialog)

Der Dialog „Link einfügen/bearbeiten“ hat eine Registerkarte Lesezeichen wie der Einbettungsdialog und der Linkdialog
von TinyMCE 3. Sie führt die Lesezeichen des aktuellen Benutzers über `ezoe::bookmarks` auf (in Seiten, mit derselben
Vorschauspalte wie Suche und Durchsuchen). Die Wahl einer Zeile füllt das Linkziel mit `eznode://<node id>`
(`ezobject://<object id>`, wenn „Link to the object instead of the node“ angehakt ist), genau wie Suche und
Durchsuchen, sodass das gespeicherte ezxml dasselbe `<link node_id="...">` ist, das TinyMCE 3 für einen Link auf einen
Knoten speichert. Ohne Lesezeichen sagt die Registerkarte „Sie haben keine Lesezeichen“ und wie man eines hinzufügt
(„Lesezeichen setzen“ an einem Inhaltselement in der Administrationsoberfläche). Die Registerkarte zum Durchsuchen
behält ihre Navigation durch den ganzen Baum. Die Texte stehen in der deutschen Übersetzung des Plugins
(`ezlink/plugin.js`); die Meldung auf dem Server verwendet die vorhandenen Übersetzungen aus
`design/standard/content/view`.

## Upstream-Code

Die Distribution von TinyMCE 8 ist unverändert in `extension/ezoe/design/standard/javascript/tinymce8/` eingebunden
(README, Lizenz und Hinweise eingeschlossen); nur Deutsch ist mitgeliefert. Die Browsertests stehen in
`extension/ezoe/tests/tinymce8/` (`npm install && npm test`).

## Tests

```bash
php vendor/bin/phpunit tests/tests/extension/ezoe/
```

Die Tests laufen gegen die laufende Installation, ohne Testdatenbank. Jeder Test stellt die Engine-Einstellung des
Administrators und die INI-Werte wieder her.

| Datei | Deckt ab |
|---|---|
| `expOEEditorRegistryTest` | Registry, Auflösung (Benutzereinstellung, Siteaccess, global), unbekannte / defekte / nicht verfügbare Engines, dritte Engine, die Details (Version, Beschreibung, Status) jedes Eintrags, Audit, setup/rad |
| `expOEContentSecurityPolicyEngineTest` | eine Engine, die Inline-Skripte braucht (TinyMCE 3), unter einer durchgesetzten Policy: Ersatz für Benutzereinstellung, Konfiguration und ForceEngine, aus dem Umschalter und der Engine-Seite weggelassen, ihre Wahl abgelehnt; unverändert, wenn erlaubt |
| `expOEEngineConfigTest` | Template, Zuordnung der Werkzeugleiste, Plugins und Konfiguration jeder Engine, ausgelieferte Standardwerte, eingebundenes TinyMCE 8 und seine Lizenz, Übersetzungen |
| `expOEUploadRulesTest` | erlaubte und abgelehnte Dateinamen, die drei Prüfmodi, die Reihenfolge der Prüfung in der Upload-View |
| `expOEViewsJsonTest` | keine PHP-Datei von ezoe hat Text vor `<?php` oder nach `?>`, die Moduldefinition gibt nichts aus, `ezoe/load` antwortet mit JSON ohne etwas davor, Durchsuchen und Lesezeichen |
| `expOEEngineViewTest` | die Seite `/ezoe/engine`: die Tabelle der Engines, Auswahl, Speichern, Zurücksetzen, unbekannte Engine, Richtlinie |
| `expOEEditorServicesTest` | die Dienste `expeditor`, ihre Schutzprüfungen und der Katalog |
| `expOERoundTripTest` | das XHTML beider Editoren ergibt dasselbe XML; einem Redakteur angezeigtes und erneut gespeichertes XML behält seinen Inhalt |

`tests/tests/extension/ezoe/helpers/run_ezoe_view.php` führt eine View in einem eigenen Prozess aus, weil eine View,
die mit `cleanExit()` endet, nicht innerhalb von PHPUnit laufen kann.

Eine View oder Moduldefinition mit Text außerhalb der PHP-Tags beschädigt die Dialoge des Editors
(`JSON.parse: unexpected character` beim abschließenden OK des Einbettungsdialogs). Deshalb sehen sich die Tests die
rohe Ausgabe an.

## Verwandte Seiten

- [Der Online-Editor mit TinyMCE 8](../../../eng-us/features/6.0/online-editor-tinymce8.md) und [ezoe](../../../eng-us/features/6.0/extensions/ezoe.md)
- [Verhaltensänderungen vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md#access-and-security) (die Prüfung beim Upload)
- [Upgrade des Audit-Protokolls](audit.md)
- [Backend-Dienste von ezjscore](backend-dienste-ueber-ezjscore.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
