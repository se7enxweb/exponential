# Der Online-Editor auf TinyMCE 8 (optional, neben TinyMCE 3)

Diese Seite richtet sich an Redakteure, die einen modernen Rich-Text-Editor möchten, und an Administratoren, die
entscheiden, welchen Editor eine Website verwendet. Exponential 6.0.15 liefert TinyMCE 8.9.2 als zweite Engine des
eZ Online Editor aus. TinyMCE 3.5.12 bleibt der Standard, und für bestehende Installationen ändert sich nichts. Die neue
Engine schreibt dasselbe XHTML, das der Eingabehandler von ezoe liest, sodass das gespeicherte XML mit beiden Engines
identisch ist und Redakteure hin- und herwechseln können. Hinzugefügt am 02.10.2026.
Referenz: [ezoe und TinyMCE 8](../../bc/6.0/ezoe-tinymce8.md).

## In einer Minute ausprobieren

Für sich selbst, ohne Einstellungen anzufassen:

1. Öffnen Sie `/user/preferences/set/ezoe_engine/tinymce8`.
2. Bearbeiten Sie einen beliebigen Artikel. Der Editor ist TinyMCE 8.
3. Um zurückzuwechseln, öffnen Sie `/user/preferences/set/ezoe_engine/tinymce3`.

Redakteure können auch auf der Seite `/ezoe/engine` wählen.

Für einen ganzen Siteaccess, in `settings/siteaccess/<name>/ezoe.ini.append.php`:

```ini
[EditorSettings]
EditorEngine=tinymce8
EngineSwitch=enabled
```

`EngineSwitch=enabled` zeigt die Engine-Umschaltung unter dem Editor: eine Gruppe namens „Editor“ mit jeder verfügbaren
Engine in der Reihenfolge von `Engines[]` (zum Beispiel „TinyMCE 3 | TinyMCE 8“) als Umschaltflächen, wobei die
verwendete Engine gedrückt ist (`aria-pressed`) und jede andere eine Schaltfläche. Ein Klick speichert den Entwurf mit
dem bisher eingegebenen Text und wechselt; die Wahl wird als Benutzerpräferenz `ezoe_engine` gespeichert. admin4
zeichnet die Gruppe als segmentiertes Bedienelement in Bedienelementhöhe; andere Designs zeigen die Beschriftung, den
Namen der verwendeten Engine und die Schaltflächen. Das Template ist
`content/datatype/edit/ezxmltext_ezoe_engine_switch.tpl`, seine Liste `engine_switch_options` des Eingabehandlers
(`expOEEditor::switchOptions()`; `engine_switch_choices`, nur die anderen Engines, bleibt für Überschreibungen
erhalten).

## Was die Engine TinyMCE 8 leistet

| Inhalt | Dialoge und Verhalten (wie bei TinyMCE 3) |
|---|---|
| Embed und Embed-Inline | Suche über `ezjsc::search` mit dem Klassenfilter und einer Vorschauspalte; Reiter Hochladen, um ein neues Objekt hochzuladen und einzubetten; Bearbeiten einer vorhandenen Einbettung (Ansicht, Klasse, Größe bei Bildern, Ausrichtung, inline, benutzerdefinierte Attribute); Doppelklick und Kontext-Werkzeugleiste |
| Links | ezoe-Dialog mit den Reitern Durchsuchen, Suche und Lesezeichen (die eigenen Lesezeichen des Benutzers, seitenweise, mit einer Vorschauspalte), um einen Knoten oder ein Objekt zu wählen; Anker werden wie bei TinyMCE 3 als `<a name>` gespeichert |
| Benutzerdefinierte Tags | Einfügen, Bearbeiten, Entfernen mit den benutzerdefinierten Attributen des Tags (text, textarea, int, number, email, select, checkbox, color, hidden); Validierung von Pflicht- und numerischen Werten |
| Literal-Tags | Dialog wie der allgemeine Tag-Dialog von TinyMCE 3 |
| Tabellen, Zeilen, Zellen | Klassen und benutzerdefinierte Attribute (`content.ini [table] Defaults` für die Größe neuer Tabellen). Tabelle einfügen / Tabelleneinstellungen öffnen standardmäßig das Dialogdesign von TinyMCE 3 (Größenraster, Breite und Rahmen mit px / %, Klasse, Zusammenfassung, Beschriftung); `TableDialog=modern` in `[Engine_tinymce8]` wählt das schlichte Formular von TinyMCE 8 |
| Absätze, Überschriften, Listen, strong, emphasize | Der allgemeine Tag-Dialog für Klasse und benutzerdefinierte Attribute |
| Statusleiste | Der ezxml-Tag-Pfad, zum Beispiel „Pfad: paragraph » embed“; ein Klick auf ein Element öffnet dessen Dialog |
| Aussehen | Mit `Skin=o2k7` sehen der Editor und seine Dialoge aus wie der o2k7-Skin von TinyMCE 3 |

## Einstellungen

Alle Schlüssel stehen in `extension/ezoe/settings/ezoe.ini`, Block `EditorSettings`. Geltungsbereich:
Benutzerpräferenz, Siteaccess oder global. Reihenfolge der Auflösung: Benutzerpräferenz `ezoe_engine`, Siteaccess,
global, dann `tinymce3`.

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `EditorEngine` | `tinymce3` | Engine für Benutzer ohne Präferenz |
| `EngineSwitch` | `disabled` | Die Engine-Umschaltung unter dem Editor anzeigen |
| `Engines[<id>]` | `tinymce3=expOETinyMCE3Engine`, `tinymce8=expOETinyMCE8Engine` | Registry der Klassen, die `expOEEditorEngine` implementieren; eine Extension fügt eine dritte Engine mit einer Klasse und einer Zeile hinzu |
| `UploadExtensionCheck` | `engine` | Serverseitige Prüfung des Dateityps: `engine` (nicht TinyMCE 3), `always`, `disabled` |
| `UploadFileExtensions[]` | Bilder, Dokumente, Medien | Dateitypen des Reiters Hochladen |
| `UploadFromUrl` | `enabled` | `enabled` / `disabled`: die Auswahl „Von einer URL“ im Reiter Hochladen (beide Editoren) |
| `UploadFromUrlMaxSize` | `145M` | Größte von einer URL abgerufene Datei (K, M, G). Nur dieser Wert gilt, nicht `upload_max_filesize` von PHP |
| `UploadFromUrlTimeout` | `300` | Gesamtzahl der Sekunden, die ein Abruf dauern darf (der Verbindungsaufbau muss innerhalb von 5 Sekunden gelingen) |

Prüfen Sie die ausgelieferten Werte:

```bash
grep -n 'EditorEngine\|EngineSwitch\|Engines\[' extension/ezoe/settings/ezoe.ini
```

Die Registry-Klassen sind `extension/ezoe/classes/expoeeditorengine.php`, `expoetinymce3engine.php` und
`expoetinymce8engine.php`; die Engine-Seite ist die Ansicht `engine` des Moduls `ezoe`.

## Linkprüfungen beim Speichern des Textes

Beide Engines senden HTML, das `eZOEInputParser` in das gespeicherte XML umwandelt. Dort wird jeder Link geprüft, und
was dabei gefunden wird, landet im Validierungsprotokoll über dem Bearbeitungsformular („Der Entwurf wurde nur
teilweise gespeichert“):

| Link | Was geschieht | Meldung |
| --- | --- | --- |
| `eznode://<id>` oder `eznode://<path>` auf einen Knoten, der nicht mehr existiert | der Link bleibt erhalten, die Meldung sagt, wie er zu reparieren ist | nennt den Link über seinen Text (höchstens 70 Zeichen, sonst seine Adresse); liegt das Ziel im Papierkorb: dessen Name, Klasse, früherer Ort und seit wann, nur für einen Benutzer sichtbar, der es lesen darf (sonst nur „liegt im Papierkorb“); bei einem Link über die ID: Wiederherstellen repariert ihn nicht, weil das wiederhergestellte Objekt einen neuen Ort erhält |
| `ezobject://<id>` auf ein Objekt, das nicht mehr existiert | wie oben | „existiert nicht mehr, auch nicht im Papierkorb“ |
| ein Skript-Link: `javascript:`, `vbscript:`, `livescript:`, `data:` außer einem einfachen Bild, in jeder Schreibweise, der ein Browser folgt (Zeichenreferenzen, `&colon;`, Tabulatoren oder Zeilenumbrüche im Schema, führende Leerzeichen, beliebige Groß- und Kleinschreibung) | die Adresse wird entfernt, die Eingabe wird als ungültig markiert (`isValid()` false) | „Skripte in Links sind nicht erlaubt, ‚...‘ wurde entfernt“ |
| `mailto:` mit einer ungültigen Adresse (jede von mehreren, vor `?`, prozentdekodiert; internationalisierte Domains werden akzeptiert) | die Adresse wird entfernt, die Eingabe wird als ungültig markiert | „Ungültige E-Mail-Adresse: ‚...‘“ |

Der Papierkorb wird nur für einen Link nachgeschlagen, dessen Ziel nicht mehr existiert, sodass ein Link auf ein
vorhandenes Ziel keine Abfrage kostet. Die Meldungen sind reiner Text; jedes Design gibt sie mit `|wash` aus, genau
einmal (`expOEValidationMessagesEscapedOnceTest`). Ein eigenes Design, das `content/edit_validation.tpl` überschreibt,
tut dasselbe. Tests: `expOEDeadLinkMessageTest`, `expOEInputValidFlagTest`, `expOELinkAddressCheckTest`.

## Sicherheit

`ezoe/upload` lehnt eine Datei ab, deren letzte Endung nicht in `UploadFileExtensions[]` steht, sowie jeden Namen, der
irgendwo eine ausführbare Endung enthält (`shell.php.jpg`), weil eine Anfrage den Dialog umgehen kann. Ein Wechsel der
Engine wird im [Prüfprotokoll](pruefprotokoll.md) als `content.ezoe.engine.change` festgehalten.

### Hochladen von einer URL

Der Reiter Hochladen des Einbettungsdialogs (TinyMCE 8 und TinyMCE 3) bietet „Von Ihrem Computer“ oder „Von einer URL“.
Der Server ruft die Adresse ab und erstellt das Objekt wie bei einem Hochladen aus dem Browser: dieselbe Klassenauswahl
(`upload.ini`), dieselben Felder für Ort, Name und Beschreibung, dieselbe anschließende Einbettung, dieselben
Berechtigungsprüfungen und dasselbe Formular-Token. Der Abruf ist eine serverseitige Anfrage im Auftrag eines Benutzers,
daher gilt:

- nur `http` und `https`; keine Zugangsdaten in der URL; der Host wird einmal aufgelöst, jede Adresse muss öffentlich
  sein (kein Loopback, keine privaten, Link-Local-, Multicast-, reservierten oder Carrier-Grade-NAT-Adressen, keine
  IPv4-gemappten oder NAT64-IPv6-Varianten), und die Verbindung wird zur geprüften Adresse aufgebaut, sodass DNS
  Rebinding sie nicht austauschen kann;
- Weiterleitungen werden von Hand verfolgt, höchstens 3, und jedes Ziel wird erneut geprüft;
- Verbindungs-Timeout 5 s, Gesamt-Timeout `UploadFromUrlTimeout`; die Größengrenze wird durchgesetzt, während der
  Inhalt in eine temporäre Datei unter `var/tmp/ezoe_url/` fließt (nie im Speicher gehalten), und die Datei wird nach
  der Anfrage gelöscht;
- der Name stammt aus Content-Disposition oder dem URL-Pfad und wird bereinigt; die Endung muss
  `UploadFileExtensions[]` bestehen (egal was `UploadExtensionCheck` sagt), ausführbare Namen werden abgelehnt, und der
  Inhalt (finfo) muss zur Endung passen; ausführbare Dateien und Webseiten werden unabhängig vom Namen abgelehnt;
- ein erfolgreicher Abruf wird im Prüfprotokoll als `content.ezoe.upload.url` festgehalten.

## Tests

- `extension/ezoe/tests/tinymce8` enthält Browsertests (puppeteer-core mit Firefox oder Chrome, echte Mausklicks), die
  jederzeit gegen eine Installation ausgeführt werden können.
- PHPUnit-Tests in `tests/tests/extension/ezoe/` decken die Registry, die Engine-Konfiguration, die Regeln für das
  Hochladen, JSON-Antworten, Ansichten und den XHTML-Roundtrip ab. Zum Zeitpunkt des Schreibens waren es 267 (für diese
  Seite nicht neu gezählt). Führen Sie sie mit `php vendor/bin/phpunit tests/tests/extension/ezoe/` aus.

Das mitgelieferte TinyMCE 8.9.2 wird unter der GPL (Version 2 oder später) verwendet; seine README nennt die Lizenzen
und wo der zugehörige Quellcode liegt.

## Verwandte Seiten

- [ezoe und TinyMCE 8 (bc-Leitfaden)](../../bc/6.0/ezoe-tinymce8.md)
- [Remote-Dienste `expeditor`](entfernte-dienste-expservices.md) und die [Spezifikation von expservices](../../spezifikationen/6.0/expservices.md)
- [ezoe](erweiterungen/ezoe.md), [ezautosave](erweiterungen/ezautosave.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../bc/6.0/verhaltensaenderungen-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik Oktober 2026](../../geschichte/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
