# Das Inhaltsmodell und das Bearbeiten von Inhalten

Diese Anleitung richtet sich an Redakteure und Site-Entwickler. Sie zeigt, wie Inhalte in Exponential aufgebaut sind
und wie Sie täglich mit ihnen arbeiten. Sie definieren eine Inhaltsklasse, erstellen daraus Inhalte und bearbeiten
diese mit dem Online-Editor (einschließlich der Engine TinyMCE 8). Danach bearbeiten Sie viele Elemente auf einmal,
verwenden die Tabelle der Unterelemente, holen ein Element aus dem Papierkorb zurück und führen eine große Operation
als Hintergrundjob aus.

Planen Sie etwa 30 Minuten ein, mit einem Browser und einem Terminal. Sie brauchen eine laufende Installation von
Exponential 6 ([Erste Schritte](erste-schritte.md)) und eine Administrator-Anmeldung. Die
Administrationsoberfläche liegt auf Ihrem Admin-Siteaccess, zum Beispiel `https://edit.example.com/` oder
`http://localhost:8087/admin/`. Befehle werden aus dem Wurzelverzeichnis der Installation ausgeführt.

## 1. Wie das Inhaltsmodell zusammenpasst

| Begriff | Bedeutung |
|---|---|
| Inhaltsklasse | eine Art von Inhalt: ein Name, ein Bezeichner und eine Liste von Attributen (zum Beispiel Article: Title, Intro, Body, Image) |
| Klassengruppe | ein Ordner für Klassen im Klasseneditor (Content, Users, Media, Setup) |
| Datentyp | die Art eines einzelnen Attributs: `ezstring` (eine Zeile), `eztext` (mehrere Zeilen), `ezxmltext` (Rich Text), `ezimage`, `ezobjectrelationlist` und so weiter |
| Inhaltsobjekt | ein einzelner Inhalt, eine Instanz einer Klasse; hat Versionen (Entwurf, veröffentlicht, archiviert) und Sprachen |
| Knoten / Ort | ein Platz eines Objekts im Inhaltsbaum; ein Objekt kann mehrere haben |

Die für neue Attribute verfügbaren Datentypen sind die Liste `AvailableDataTypes[]` in `settings/content.ini`; die
mitgelieferte Liste hat 35 Einträge. Prüfen: `grep -c 'AvailableDataTypes\[\]=' settings/content.ini` gibt `35` aus.
Andere Extensions fügen eigene hinzu (zum Beispiel stehen `ezkeyword`, `ezmatrix` und `ezselection` in der Liste;
solche von Drittanbietern werden in deren `settings/` registriert).

## 2. Eine Klasse anlegen

1. Öffnen Sie `/class/grouplist` in der Administrationsoberfläche. Dort werden die Klassengruppen aufgelistet.
2. Klicken Sie auf die Gruppe **Content**, dann auf **Neue Klasse**. (`/class/classlist/<group id>` ist die Liste
   ihrer Klassen.)
3. Geben Sie ihr einen Namen, zum Beispiel `Note`, und den Bezeichner `note`. Füllen Sie **Objekt Namensschema** mit
   `<title>`.
4. Wählen Sie unter **Attribut hinzufügen** den Datentyp `Textzeile` und klicken Sie auf **Hinzufügen**; nennen Sie es
   `Title`, Bezeichner `title`, und kreuzen Sie **Erforderlich** an. Fügen Sie ein zweites Attribut `Textabschnitt`
   hinzu, Name `Body`, Bezeichner `body`.
5. Klicken Sie auf **OK** (übernehmen und schließen). Erwartet: Die Klasse erscheint in der Liste.

Erwartetes Ergebnis: **Setup > Klassen** (oder `/class/view/<id>`) zeigt `Note` mit zwei Attributen. Die Klasse kann
jetzt überall erstellt werden, wo die Richtlinien für ihre Elternseite es erlauben.

## 3. Ein Objekt erstellen und bearbeiten

1. Öffnen Sie die Inhaltsstruktur (`/content/view/full/2`), wählen Sie einen Ordner aus, wählen Sie im Erstellen-Menü
   **Note** und klicken Sie auf **Hier erstellen**.
2. Füllen Sie den Titel aus und drücken Sie **Zur Veröffentlichung schicken**. Erwartet: Die Seite des neuen Objekts
   wird angezeigt, und es steht in der Liste der Unterelemente des Ordners.
3. Bearbeiten Sie es erneut mit **Bearbeiten**. Jedes Speichern erzeugt eine Version; eine nicht gespeicherte
   Bearbeitung hinterlässt einen Entwurf, den Sie unter `/content/draft` wiederfinden. Die Anzahl der pro Klasse
   aufbewahrten archivierten Versionen ist durch
   `[VersionManagement] DefaultVersionHistoryLimit` (Standard 45; `VersionHistoryClass[1]=75` für Ordner) in `settings/content.ini` begrenzt; die Bereinigung erfolgt mit
   `./console exp:cleanupversions --help --allow-root-user` (lesen Sie zuerst die Hilfe; ohne `--help` löscht der Befehl alte Versionen).

Die automatische Speicherung legt alle 180 Sekunden einen Entwurf ab, wenn die Extension `ezautosave` aktiv ist
(`autosave.ini`, `[AutosaveSettings] Interval`).

## 4. Der Online-Editor und TinyMCE 8

Das Rich-Text-Attribut (`ezxmltext`) wird mit dem Online-Editor bearbeitet. Es gibt zwei Engines: TinyMCE 3.5.12, die
Voreinstellung, und TinyMCE 8.9.2, seit 6.0.15 wahlweise zuschaltbar. Beide schreiben dasselbe gespeicherte XML, Sie
können also jederzeit wechseln.

Probieren Sie TinyMCE 8 in einer Minute aus, nur für Ihren Benutzer:

1. Öffnen Sie `/user/preferences/set/ezoe_engine/tinymce8`.
2. Bearbeiten Sie ein beliebiges Objekt mit einem Rich-Text-Attribut (zum Beispiel einen Article). Die
   Editor-Werkzeugleiste ist die neue Engine; die Statusleiste zeigt den Tag-Pfad, etwa „Path: paragraph » embed“.
3. Wechseln Sie mit `/user/preferences/set/ezoe_engine/tinymce3` zurück.

Für alle Benutzer eines Siteaccess tragen Sie in `settings/siteaccess/<name>/ezoe.ini.append.php` ein:

```ini
<?php /* #?ini charset="utf-8"?

[EditorSettings]
EditorEngine=tinymce8
EngineSwitch=enabled

*/ ?>
```

`EngineSwitch=enabled` zeigt den Engine-Umschalter unter dem Editor an („Editor“ mit jeder Engine, die verwendete markiert). Leeren Sie dann den INI-Cache:
`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`. Prüfen Sie, ob die Einstellung gelesen wird:
`grep -n 'EditorEngine\|EngineSwitch' extension/ezoe/settings/ezoe.ini`.

Im Editor: Die Werkzeugleiste bettet Objekte ein (Reiter Suche, Reiter Upload), verlinkt auf Knoten oder Objekte,
fügt Custom Tags, Tabellen und Listen ein; doppelklicken Sie auf eine Einbettung, um ihre Ansicht, Klasse, Größe und
Ausrichtung zu bearbeiten. Uploads akzeptieren nur die Typen in `UploadFileExtensions[]` und lehnen Namen wie
`shell.php.jpg` ab. Vertiefung:
[Online-Editor TinyMCE 8](../../en/us/features/6.0/online-editor-tinymce8.md),
[Upgrade-Hinweise zu ezoe](../../en/us/bc/6.0/ezoe-tinymce8.md).

### Die Reihenfolge der Suchergebnisse in den Dialogen des Editors

Der Suchreiter der Einbettungs- und Linkdialoge sowie die Beziehungssuche der Administrationsoberfläche verwenden die
Suche von ezjscore (`ezjsc::search`). Mit eZ Find als Suchmaschine kommen deren Ergebnisse in der Reihenfolge von
`ezfind.ini [SearchSettings] SortBy[]`; ezjscore liefert `SortBy[published]=desc` mit, das Neueste zuerst, die
Reihenfolge, die diese Suchen schon immer hatten. Um die besten Treffer nach vorne zu stellen, zum Beispiel:

```ini
# settings/override/ezfind.ini.append.php
<?php /* #?ini charset="utf-8"?

[SearchSettings]
SortBy[]
SortBy[score]=desc
SortBy[published]=desc

*/ ?>
```

Leeren Sie danach den INI-Cache. Jeder Eintrag ist ein Feld der Sortierung von eZ Find (`published`, `modified`,
`score`, `name`, `class_identifier`, `path`, ein Attribut als `class/attribute`) mit `asc` oder `desc`; ein Eintrag,
der keines ist, wird mit einer Warnung ausgelassen. Ohne eZ Find (die eingebaute Suche) oder ohne die Einstellung
bleibt die Reihenfolge „Neueste zuerst“. Bei einem Upgrade ändert sich nichts. Mit Dank an Mugo Web (LoveStack,
Commits `3d2d337b0d` und `3af1ec3aee`, 2025).

### Bedienelemente für Custom Tags

Für Redakteure: Klicken Sie in ein Custom Tag (einen Infokasten, ein Zitat, einen Durchstrich ...), und darauf
erscheint eine kleine Leiste:

| Schaltfläche | Tasten | Was sie tut |
|---|---|---|
| ▲ Nach oben | Alt+Shift+Up | verschiebt ein Block-Custom-Tag über den vorhergehenden Block |
| ▼ Nach unten | Alt+Shift+Down | verschiebt es unter den nächsten Block (danach wird ein Absatz eingefügt, wenn es zum letzten Block wird) |
| ✎ Bearbeiten | Alt+Shift+E | öffnet den Dialog des Tags |
| ✕ Entfernen | Alt+Shift+Delete | entfernt das Tag und behält seinen Text |

Die Rücktaste in einem leeren Block-Custom-Tag entfernt es, und jedes Block-Custom-Tag zeigt seinen Namen darüber an.
Der Dialog eines Tags zeigt ein Beispielbild, wenn ein Design `images/editor/custom_tags_thumbs/<name>.png` hat.
Dasselbe funktioniert in TinyMCE 3, TinyMCE 8 (die Leiste ist die Kontext-Werkzeugleiste des Tags; Strg+F9 erreicht
sie über die Tastatur) und Tiptap. Weder die Leiste noch die Beschriftungen werden mit dem Text gespeichert.

Für Administratoren: Neue Installationen haben es eingeschaltet. Eine aktualisierte Site behält den bisherigen Editor,
bis Sie es einschalten:

```ini
# settings/override/ezoe.ini.append.php
<?php /* #?ini charset="utf-8"?

[EditorSettings]
CustomTagControls=enabled

*/ ?>
```

Leeren Sie dann den INI-Cache und die gepackten Skripte:
`php bin/php/ezcache.php --clear-tag=ini --clear-id=ezjscore-packer --allow-root-user`.

### Versteckte Custom Tags

Ein Custom Tag kann in Gebrauch bleiben, ohne für neuen Text angeboten zu werden, zum Beispiel ein Tag, das Sie
auslaufen lassen. Führen Sie es in `content.ini` auf:

```ini
# settings/override/content.ini.append.php
<?php /* #?ini charset="utf-8"?

[CustomTagSettings]
IsHidden[]=quote

*/ ?>
```

Die Editoren (TinyMCE 3, TinyMCE 8, Tiptap) lassen es in der Custom-Tag-Liste für ein neues Tag aus. Wo es bereits in
einem Text steht, wird es wie bisher angezeigt, gerendert und gespeichert, und sein eigener Dialog öffnet sich
weiterhin mit seinem Namen. Standardmäßig ist keines versteckt, daher ändert sich bei einem Upgrade nichts. Für
Entwickler: `eZOEXMLInput::hiddenCustomTags()`, `eZOEXMLInput::offeredCustomTags( $tags, $current )` und die
Schlüssel `hidden` und `preview` von `eZOEXMLInput::getCustomTagDefinitions()`.

Beide wurden aus LoveStack, der Distribution desselben Kernels von Mugo Web, nachgebaut, mit Dank an Mugo Web
(Commits `63334f1681`, das Plugin custom_tags_controls, 2019, und `e21ef46691`, 2019). Dass sich der sub/sup-Dialog
bei OK schließt (LoveStack-Commit `3a8c14bc0c`), war bereits Teil des Editors von Exponential.

## 5. Die Tabelle der Unterelemente

Öffnen Sie einen Ordner, der Unterelemente hat. Die Tabelle unter der Ansicht des Knotens ist die Liste der
Unterelemente.

1. Klicken Sie auf **Einstellungen**.
2. Kreuzen Sie Spalten an (129 sind verfügbar, gruppiert nach Basic, Node, Object, Dates, SEO und so weiter) und
   ziehen Sie sie, um sie neu anzuordnen; wählen Sie eine Vorgabe (SEO, Editorial, Technical) oder eine Seitengröße.
3. Wählen Sie unter Elemente pro Seite **Individuell**, geben Sie eine Zahl von 1 bis 10000 ein und drücken Sie die
   Eingabetaste.
4. Verwenden Sie den CSV-Export, um die sichtbaren Spalten herunterzuladen. Ihre Auswahl wird pro Benutzer gespeichert.

Erwartet: Die Tabelle lädt sofort mit den gewählten Spalten und Zeilen neu. Vertiefung:
[Tabelle der Unterelemente](../../en/us/features/6.0/subitems-table-options.md), [Katalog und wie Sie eine Spalte hinzufügen](../../en/us/bc/6.0/subitems-table-options.md),
[Einstellungen zum Blättern](../../en/us/bc/6.0/pagination-settings.md).

## 6. Viele Elemente in einem Formular bearbeiten (Mehrfachbearbeitung)

1. Kreuzen Sie in der Liste der Unterelemente mehrere Elemente an, dann **Weitere Aktionen > Ausgewählte bearbeiten**.
   Oder suchen Sie (`/content/search`), kreuzen Sie Treffer aus der gesamten Site an und klicken Sie auf
   **Ausgewählte bearbeiten**.
2. Das Formular zeigt einen Abschnitt pro Inhaltsklasse und ein aufklappbares Feld pro Objekt. **Alle aufklappen** und
   **Alle zuklappen** stehen darüber.
3. Drücken Sie **Entwürfe speichern**, um die Arbeit ohne Veröffentlichung zu behalten, **Alle veröffentlichen**, um
   jedes Objekt zu veröffentlichen, oder **Verwerfen**, um die Entwürfe zu verwerfen. Der Bericht listet auf, welche
   Objekte veröffentlicht wurden, auf Freigabe warten oder abgelehnt wurden.

Um mehrere neue Elemente auf einmal anzulegen, verwenden Sie **Mehrere neu anlegen** neben **Neu erstellen** (bis zu
50 auf einmal). Prüfen Sie nach dem Anlegen, ob die neuen Elemente unter ihrer Elternseite erscheinen. Vertiefung:
[Mehrfachbearbeitung](../../en/us/bc/6.0/multi-node-edit.md).

## 7. Der Papierkorb

1. Wählen Sie ein Element aus, **Entfernen**, bestätigen Sie. Das Element wandert in den Papierkorb (sofern Sie nicht
   Löschen wählen).
2. Öffnen Sie `/content/trash`. Jede Zeile zeigt, wer das Element in den Papierkorb gelegt hat, wo es war, wie viele
   Knoten darunter lagen und seine Sprachen. Filtern Sie nach Benutzer, Typ oder Datum; **Wiederherstellen** stellt
   ein Element zurück (`/content/restore/<object id>`).
3. **Papierkorb leeren** löscht alles endgültig, unabhängig vom Filter. In der Shell geschieht dasselbe mit
   `./console exp:trashpurge --allow-root-user`: Das ist endgültig, führen Sie es also nicht auf einer Site aus, die
   Ihnen wichtig ist.

Vertiefung: [Papierkorb: wer und wo](../../en/us/features/6.0/trash-who-and-where.md), [Upgrade-Hinweise zum Papierkorb](../../en/us/bc/6.0/trash.md).

## 8. Große Operationen als Inhaltsjobs

Das Entfernen, Kopieren oder Verschieben eines großen Teilbaums kann als Hintergrundjob laufen, der in Stapeln arbeitet
und nach einem Absturz fortgesetzt wird.

1. Starten Sie ein Entfernen, Kopieren oder Verschieben für einen Teilbaum. Die Bestätigungsseite bietet **jetzt
   ausführen** oder **im Hintergrund** an; ab 50 Knoten wird „im Hintergrund“ zuerst angeboten
   (`[ContentJobSettings] SynchronousLimit=50` in `settings/content.ini`).
2. Der Browser folgt `/content/job/<id>`: Fortschritt, Stapel, Schaltflächen zum Abbrechen oder Fortsetzen. Alle Jobs
   werden unter `/content/jobs` aufgelistet.
3. In der Shell:

```bash
./console exp:expcontentjob list --allow-root-user
./console exp:expcontentjob show <id> --allow-root-user
./console exp:expcontentjob resume <id> --allow-root-user
./console exp:expcontentjob cancel <id> --allow-root-user
```

Auf einer Installation ohne Jobs gibt `list` die Meldung `No active or failed content jobs (--all lists every job).`
aus. Der Cronjob-Teil `contentjobs` startet einen Worker neu, dessen Prozess beendet wurde. Vertiefung:
[Inhaltsjobs](../../en/us/features/6.0/content-jobs.md),
[Anleitung zu Inhaltsjobs](../../en/us/bc/6.0/content-jobs.md), [Audit-Protokoll](../../en/us/features/6.0/audit-trail.md) (jeder Job wird festgehalten).

## 9. Pflicht-Alternativtext, gekürzte Textzeilen und von PHP erzeugte Namen

Diese vier Optionen kamen mit Exponential 6.0.15 aus LoveStack, der Distribution des Legacy-Kernels von Mugo Web, mit
Dank an Mugo Web für die Ideen und den ursprünglichen Code. Alle sind ausgeschaltet, bis Sie sie einschalten, auf
neuen wie auf aktualisierten Installationen, sodass sich auf einer bestehenden Site nichts von selbst ändert.

### Alternativtext ist erforderlich (Bildfelder)

Für Redakteure: Ein Bildfeld, in dessen Klasse die Option eingeschaltet ist, kann nicht mit einem Bild, aber ohne
Alternativtext gespeichert werden. Das Bearbeitungsformular kennzeichnet das Feld mit **Alternativer Bildtext
(erforderlich)** und antwortet *Für dieses Bild ist ein Alternativtext erforderlich.*, wenn es leer ist. Dieselbe
Prüfung gilt im Upload-Dialog des Online-Editors (auch wenn die Tiptap-Engine darüber hochlädt) und im AJAX-Uploader
der Beziehungsfelder, der jetzt ein Feld **Alternativtext** für Klassen mit einem Bildattribut hat. Ein leer
gelassenes Bildfeld braucht keinen Text.

Für Administratoren: **Setup > Klassen**, bearbeiten Sie die Klasse, suchen Sie das Bildattribut und kreuzen Sie
**Alternativtext ist erforderlich** an; klicken Sie auf **OK**. Die Klassenansicht zeigt dann *Alternativtext ist
erforderlich: Ja*. Die Einstellung wird in `data_int2` des Klassenattributs gespeichert (die maximale Dateigröße
bleibt in `data_int1`) und wandert mit der Klasse in einem Paket als
`<alternative-text-required>1</alternative-text-required>`.

Nicht abgedeckt: Der allgemeine Datei-Upload (`content/upload`, Mehrfach-Upload) und Importe erstellen Bilder, ohne
nach einem Text zu fragen; tragen Sie ihn nachträglich ein. Die Liste *Fehlender Alternativtext* der Extension expai
zeigt solche Bilder.

### Den gespeicherten Wert trimmen (Textzeilenfelder)

Für Redakteure: Bei eingeschalteter Option werden Leerzeichen, Tabulatoren und Zeilenumbrüche am Anfang und am Ende
einer Textzeile beim Speichern entfernt; Leerraum innerhalb des Textes bleibt erhalten. Für Administratoren:
Bearbeiten Sie die Klasse und kreuzen Sie beim Textzeilen-Attribut **Gespeicherten Wert trimmen** an. Das gilt für das
Bearbeitungsformular, die Informationssammlung, Importe (`fromString()`) und das einfache Einfügen von Zeichenketten
bei Uploads. Gespeichert in `data_int2`; in einem Paket `<trim>1</trim>`. Zuvor gespeicherter Text bleibt
unverändert, bis er erneut gespeichert wird.

Das Formular zum Bearbeiten der Klasse sendet mit beiden Kontrollkästchen eine versteckte Markierung, sodass ein
Design, dessen Template zum Bearbeiten der Klasse eine Option nicht anzeigt, deren Wert unverändert lässt.

### Von einer PHP-Klasse erzeugte Namen (Entwickler)

Das **Objekt Namensschema** oder das **URL Alias Namensschema** einer Klasse darf mit einem PHP-Klassennamen in
geschweiften Klammern beginnen, zum Beispiel `{myNameResolver}` oder `{myNameResolver} <title>`. Diese Klasse erzeugt
dann den Namen. Sie muss:

1. in `settings/content.ini` (einem Override) unter `[NamePatternSettings] ResolverClasses[]=myNameResolver`
   aufgeführt sein;
2. `expNamePatternResolverInterface` (`kernel/classes/expnamepatternresolverinterface.php`) implementieren: Sie wird
   mit dem Rest des Musters nach `{myNameResolver}` konstruiert, und ihre Methode
   `resolveNamePattern( $object, $version, $translation, $length, $sequence )` gibt den Namen zurück;
3. per Autoload ladbar sein (im Verzeichnis `classes/` einer Extension, danach `php bin/php/ezpgenerateautoloads.php -e`).

Ein Name, der nicht aufgeführt ist, wird niemals geladen oder instanziiert: Er wird als Warnung protokolliert, und
das Muster wird wie bisher aus seinen Attribut-Tokens aufgelöst. Eine aufgeführte Klasse ohne das Interface oder eine,
die eine Ausnahme wirft, wird als Fehler protokolliert, und die Tokens werden verwendet. Der Name wird auf die üblichen
Grenzen gekürzt (`[ContentSettings] ContentObjectNameLimit`, `[URLTranslator] UrlAliasNameLimit`). In PHP:
`eZNamePatternResolver::resolve( $pattern, $object, ... )`.

### Standard-Klassenfilter für Fetches (Template-Entwickler)

`fetch( 'content', 'list', hash( 'parent_node_id', 2, 'class_filter_array', array( 'article' ) ) )` ohne
`class_filter_type` ignoriert das Array heute. Mit `settings/content.ini [FetchSettings] DefaultClassFilterType=include`
listet ein solcher Fetch nur diese Klassen auf (`exclude`: jede Klasse außer diesen). Das gilt für `list`, `tree`,
`list_count`, `tree_count` und `calendar`; ein Fetch, der `class_filter_type` angibt, behält ihn. Leer, der
mitgelieferte Wert, behält die heutigen Ergebnisse bei. Schalten Sie es erst ein, nachdem Sie die Templates geprüft
haben, die sich darauf verlassen, dass das Array ignoriert wird:
`grep -rn "class_filter_array" design extension --include=*.tpl | grep -v class_filter_type`.

## 10. Kopieren, Orte und wie Auswahlen angezeigt werden

Diese stammen aus LoveStack (mugoweb/ezpublish-legacy) und wurden in Exponential mit Dank an Mugo Web nachgebaut; die
Commits sind bei jedem Punkt genannt.

### Kopieren aus der Vollansicht

Neben **Verschieben** und **Entfernen** hat die Vollansicht eines Elements in der Administrationsoberfläche (admin,
admin3 und admin4) eine Schaltfläche **Kopieren** (LoveStack-Commit `4d56593614`). Sie öffnet dieselben Schritte wie
**Kopieren** im Kontextmenü: Wählen Sie, wohin die Kopie kommt, und dann, falls Ihre Site danach fragt, welche
Versionen kopiert werden sollen. Die Schaltfläche ist aktiv, wenn Sie in der Elternseite des Elements Inhalte
erstellen dürfen, so wie das Menü; andernfalls ist sie ausgegraut und sagt, warum. Sie brauchen weiterhin
Erstellrechte am Ziel der Kopie; das wird geprüft, wenn Sie den Ort wählen. Es ist nichts einzuschalten.

### Das Fenster Orte lässt einen versteckten Ort versteckt

Im Fenster **Orte** des Bearbeitungsformulars beginnt die Auswahl **Sichtbarkeit nach Veröffentlichung** eines
bestehenden Ortes jetzt bei **Unverändert** statt bei **Sichtbar** (EZP-26501, LoveStack-Commit `f1b7f056b4`). Das
Veröffentlichen nach dem Hinzufügen eines Ortes macht daher keinen Ort mehr sichtbar, der versteckt war. Ein neuer
Ort beginnt so, wie er angegeben wurde (**Versteckt**, wenn er versteckt hinzugefügt wurde, sonst **Unverändert**, was
seiner Elternseite folgt). **Sichtbar** und **Versteckt** können weiterhin für jeden Ort gewählt werden. Dies ist eine
Fehlerbehebung und gilt für jede Site.

### Auswahl- und Matrixfelder ohne überflüssige Zeilenumbrüche

Die Standard-View-Templates der Auswahlfelder (`ezselection`) und Matrixfelder (`ezmatrix`) schreiben kein
überflüssiges `<br />` (LoveStack-Commits `eee7f69fc3`, `67eec8f972`, `492a946963`): Eine ausgewählte Option wird für
sich allein angezeigt, mehrere werden durch jeweils ein `<br />` getrennt, in der Reihenfolge der Klasse, und die
Matrix ist eine Tabelle und nichts anderes. Eine Site mit einem eigenen Override dieser Templates ist nicht betroffen.

## In dieser Anleitung verwendete Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/content.ini` | `[DataTypeSettings]` | `AvailableDataTypes[]` | 35 Datentypen | Installation, Siteaccess |
| `settings/content.ini` | `[VersionManagement]` | `DefaultVersionHistoryLimit` | `45` (`VersionHistoryClass[1]=75`) | Installation, Siteaccess |
| `settings/content.ini` | `[ContentJobSettings]` | `SynchronousLimit` | `50` Knoten | Installation, Siteaccess |
| `settings/content.ini` | `[NamePatternSettings]` | `ResolverClasses[]` | leer | Installation, Siteaccess |
| `settings/content.ini` | `[FetchSettings]` | `DefaultClassFilterType` | leer (Array ignoriert) | Installation, Siteaccess |
| `extension/ezautosave/settings/autosave.ini.append.php` | `[AutosaveSettings]` | `Interval` | `180` Sekunden | Installation, Siteaccess |
| `extension/ezoe/settings/ezoe.ini` | `[EditorSettings]` | `EditorEngine` | `tinymce3` | Installation, Siteaccess, Benutzereinstellung `ezoe_engine` |
| `extension/ezoe/settings/ezoe.ini` | `[EditorSettings]` | `EngineSwitch` | `disabled` | Installation, Siteaccess |
| `extension/ezoe/settings/ezoe.ini` | `[EditorSettings]` | `CustomTagControls` | `disabled` (`enabled` in neuen Installationen) | Installation, Siteaccess |
| `settings/content.ini` | `[CustomTagSettings]` | `IsHidden[]` | leer | Installation, Siteaccess |

Ändern Sie einen Wert in `settings/override/` oder `settings/siteaccess/<name>/`, niemals in der mitgelieferten
Datei, und leeren Sie dann den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

## Siehe auch

- Ändern, wie Inhalte angezeigt werden: [Templates und Design](templates-und-design.md).
- Was wann geschah: [Chronik Oktober 2026](../../en/us/history/2026/2026-10.md), [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md).
- Härtung dessen, was Redakteure eingeben können: [Härtung der Datentyp-Eingaben](../spezifikationen/6.0/datentyp-eingabehaertung.md).
- Hintergrund- und geplante Arbeiten ausführen: [Cronjob-Konsole](../../en/us/features/6.0/cronjobs-console.md).
