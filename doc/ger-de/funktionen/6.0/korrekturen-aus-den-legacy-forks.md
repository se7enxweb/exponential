# Korrekturen aus den Forks von Opencontent und Mugo Web: Grenzen, einzeilige Logs und sicherere Standards

Seit 6.0.15 enthält Exponential die Korrekturen, die die beiden aktiven Forks seines Vorgängers, Opencontent
(`Opencontent/ezpublish-legacy`) und Mugo Web (`mugoweb/ezpublish-legacy`), am selben Kernel vorgenommen haben und die
Exponential noch fehlten. Ekkehard Dörre (CJW) hat beide Forks mit Exponential verglichen, jede fehlende Korrektur für
diesen Baum neu geschrieben und als Pull Request #345 beigetragen. Darüber hinaus schließt 6.0.15 die Punkte, die
seine Durchsicht als offen aufführte: ein leeres `if` im Veröffentlichungsablauf, die Obergrenze für Teilbäume in
ezjscore, die Upload-Sperrliste, den Flash-Player des Online-Editors und absolute URLs als URL-Aliase.

Diese Seite richtet sich an Administratoren, die eine Site einrichten, und an Entwickler, die sich auf die geänderten
Klassen verlassen. Was Sie beim Upgrade prüfen müssen, steht in den [Upgrade-Hinweisen](../../bc/6.0/korrekturen-aus-den-legacy-forks.md).

## Was sich für Administratoren ändert

### URL-Aliase: höchstens 30 Pfadelemente werden nachgeschlagen

Jedes Element einer URL ist ein Self-Join der Tabelle `ezurlalias_ml`, wenn die URL in einen Knoten übersetzt wird.
Ein Crawler, der endlose Pfade abruft (`/a/b/c/d/...`, oft aus relativen Links einer fehlerhaften Seite gebildet), ließ
die Datenbank bei jeder Anfrage Joins über Dutzende Tabellen ausführen. `eZURLAliasML::translate()` und
`fetchByPath()` schlagen jetzt höchstens `site.ini [URLTranslator] MaxPathDepth` Elemente nach; eine tiefere URL ist
„nicht gefunden“, ohne Abfrage.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
# der Standard; ein Wert unter 1 bedeutet ebenfalls 30
MaxPathDepth=30
*/ ?>
```

- Das Pfadpräfix eines Siteaccess (`PathPrefix`) zählt als Element, wenn es vorangestellt wird.
- View-Parameter wie `/(offset)/10` werden vor dem Nachschlagen von der Adresse abgenommen und zählen nicht.
- Eine Site mit Aliasen, die tiefer als 30 Ebenen verschachtelt sind, erhöht den Wert. Der tiefste Knoten-Alias ist
  der `path_identification_string` von `ezcontentobject_tree` mit den meisten Schrägstrichen; eigene Aliase listen die
  URL-Alias-Seiten der Administration.

### Log-Einträge bleiben auf einer Zeile

`eZLog::write()` (jedes Log, das eine Erweiterung oder der Kernel damit schreibt, etwa `common.log`) und
`eZLog::writeStorageLog()` (`storage.log`) ersetzen einen Zeilenumbruch (CR LF, CR oder LF) in der Nachricht durch
ein Leerzeichen. Ein Zeilenumbruch in einem protokollierten Wert (ein Benutzername bei einer fehlgeschlagenen
Anmeldung, eine URL, ein Dateiname) könnte sonst eine Zeile beginnen, die wie ein eigener Eintrag aussieht, mit
Datum und Siteaccess nach Wahl des Angreifers. Das neue `eZLog::oneLine()` übernimmt die Ersetzung für Code, der
eigene Log-Zeilen schreibt. Die Logs von `eZDebug` (`error.log`, `warning.log`, `notice.log`, `debug.log`) schreibt
anderer Code; sie sind nicht Teil dieser Änderung.

Eine Nachricht, die absichtlich mehrere Zeilen umfasste (ein mit `eZLog::write()` geschriebener Stack-Trace), kommt
jetzt als eine lange Zeile an. Schreiben Sie solchen Text mit `eZDebug::writeError()` oder mit einem
`eZLog::write()`-Aufruf je Zeile.

### Uploads: eine längere Liste abgelehnter Dateiendungen

`file.ini [FileSettings] FileExtensionBlackList[]` nennt die Endungen, die die Datentypen Datei, Medien und Bild
ablehnen. Bisher enthielt sie `php`, `php3`, `phar`, `phpt`, `pht`, `phtml` und `pgif`, die Liste beider Forks. Jetzt
nennt sie jede Endung, die ein Webserver als Code ausführen kann:

| Gruppe | Endungen |
|---|---|
| PHP, wie es die Handler von PHP-FPM, mod_php und den Hosting-Panels zuordnen | `php`, `php3`, `php4`, `php5`, `php7`, `php8`, `phps`, `phtml`, `pht`, `phar`, `phpt`, `pgif` |
| Server-Side Includes | `shtml` |
| CGI-Skripte | `cgi`, `pl` |
| Seiten anderer Plattformen | `asp`, `aspx`, `jsp` |
| Webserver-Konfiguration | `htaccess` |

Die Endung des Dateinamens wird in Kleinbuchstaben verglichen (`shell.PHP5` wird als `php5` abgelehnt); schreiben Sie
die Werte deshalb klein. Um mehr abzulehnen, ergänzen Sie die Liste in einem Override; um sie zu ersetzen, beginnen
Sie mit einem leeren `FileExtensionBlackList[]`:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[FileSettings]
# ergänzt die mitgelieferte Liste
FileExtensionBlackList[]=svgz
*/ ?>
```

Die Liste ist eine Schicht. Der Webserver darf trotzdem nie eine Datei unterhalb der Speicherverzeichnisse ausführen:
siehe [Sicherheitshärtung](../../hilfe/sicherheitshaertung.md#missbrauch-von-datei-uploads).

### ezjscore: eine Teilbaum-Anfrage liefert höchstens 500 Einträge

`ezjscore/call/ezjscnode::subtree::<knoten>::<limit>::<offset>` listet die Kinder eines Knotens für jeden Benutzer,
der ihn lesen darf, und die Unterelemente-Liste der Administration holt ihre Zeilen darüber (und über
`expsubitems::rows`). `ezjscore.ini [ezjscServer_ezjscnode] HardLimit` wurde als `false` ausgeliefert, sodass eine
Anfrage mit einem Limit von einer Million eine Million Knoten lud. Der ausgelieferte Wert ist jetzt `500`:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
# ein größeres Limit wird auf diesen Wert gekürzt, ein kleineres bleibt; false oder 0 schaltet es ab
HardLimit=500
*/ ?>
```

Mugo Web liefert `100` aus. Exponential nimmt `500`, weil die Unterelemente-Liste Seiten mit 10, 25, 50, 100, 200 und
500 Einträgen anbietet und ein kleinerer Wert die größeren Seiten kürzen würde, die ein Redakteur wählt. Eine Site,
deren Redakteure die größeren Seiten nie nutzen, kann `100` setzen.

### URL-Alias-Formular: eine absolute URL wird zu ihrem Pfad

Eine Adresse, die in ein URL-Alias-Formular der Administration als Alias selbst eingefügt wurde
(`https://www.example.com/news`), wurde an jedem Schrägstrich geteilt und als verschachtelter Alias
`https/noname2/www-example-com/news` mit drei Platzhalter-Einträgen gespeichert. `eZURLAliasML::storePath()` speichert
jetzt nur den Pfad einer absoluten URL (`news`); Schema, Host, Port, Query und Fragment entfallen, und das Formular
zeigt den gespeicherten Alias, wie bei jedem Alias, den es bereinigen musste. Eine absolute URL ohne Pfad wird wie
bisher gespeichert, weil der leere Pfad der Eintrag des Wurzelknotens ist.

Opencontent lehnt einen solchen Pfad stattdessen ab (Status `ACTION_INVALID`). Exponential behält den Pfad, weil das
Alias-Formular zu diesem Status keine Meldung zeigt: Der Redakteur sähe das Formular kommentarlos zurückkommen.

### Der Online-Editor liefert keinen Flash-Player mehr aus

Das mit ezoe gelieferte Media-Plugin von TinyMCE 3 enthielt `moxieplayer.swf`, den Flash-Ersatz für ein MP4- oder
FLV-Video. Flash hat 2020 sein Lebensende erreicht, und kein aktueller Browser führt es aus. Die Datei ist entfernt,
und `flash_video_player_url` des Plugins ist standardmäßig `false`, sodass ein Video ein HTML5-Element `<video>` ohne
Flash-Ersatz bleibt. Das Media-Plugin steht standardmäßig nicht in `ezoe.ini [EditorSettings] Plugins[]`.

## Was sich für Entwickler ändert

| Wo | Änderung | Gefunden von |
|---|---|---|
| `ezpKernelRest` | ruft `ezpEvent::getInstance()->registerEventListeners()` auf wie der Web-Kernel, sodass `site.ini [Event] Listeners[]` auch bei REST-Anfragen laufen. Der Aufruf ersetzt den Satz, den eine frühere Anfrage registriert hat, sodass ein persistenter Worker keinen Listener doppelt registriert. | Opencontent |
| `ezpContentFieldSet` | das Durchlaufen der Felder in einer Sprache, in der das Objekt keine Übersetzung hat, ergibt keine Felder statt einer Warnung und eines `TypeError` | Opencontent |
| `eZPersistentObject::fetchObjectList()` | ein `$limit` mit `offset` und ohne `length` liest keinen undefinierten Array-Schlüssel mehr | Mugo Web |
| `clusterize.php` | Zeilen von `ezbinaryfile`, `ezmedia` und `ezimagefile` ohne Dateinamen werden übersprungen, statt das Verzeichnis zu speichern | Mugo Web |
| `index_cluster.php` | ohne Cluster-Konfiguration antwortet es mit seiner Fehlerseite statt mit `Error: Undefined constant "CLUSTER_ENABLE_DEBUG"` | Mugo Web |
| ezoe `browse` | ein leerer Ordner ergibt `count` 0 statt `count( false )` (ein `TypeError` unter PHP 8) | Mugo Web |
| Operator `texttoimage` | eine Eingabe null oder eine Zahl wird als Zeichenkette genommen | Mugo Web |
| `eZINI::parameterSet()` | ohne Wurzelverzeichnis sucht es in `settings/`; bisher setzte es den *Dateinamen* auf `settings` | Mugo Web |
| `eZSession` | `session_name()` wird für eine bereits aktive Sitzung nicht aufgerufen (eine Warnung) | Mugo Web |
| `pdf/ezxmltags/object_denied.tpl` | das PDF eines eingebetteten Objekts, das der Leser nicht sehen darf, nennt den Namen des Objekts nicht mehr, wie es die HTML-Templates nie taten | Mugo Web |
| `benchmarks/classes/ezbenchmarkclirunner.php` | lädt wieder (`display()` war unverträglich mit der Elternklasse deklariert, ein fataler Fehler) | Mugo Web |
| `eZContentOperationCollection::publishNode()` | der über die Remote-ID einer Knotenzuweisung gefundene Knoten wird nur genommen, wenn er der Knoten des Objekts unter dem Elternknoten der Zuweisung ist (siehe unten) | Mugo Web, in Exponential abgesichert |
| `eZURLAliasML` | neu `maxPathDepth()` und `stripURLOrigin()` | Opencontent, Exponential |
| `eZLog` | neu `oneLine()`; `writeStorageLog()` schreibt eine Zeile | Opencontent, in Exponential erweitert |

### publishNode() und die Remote-ID einer Knotenzuweisung

`publishNode()` sucht den Knoten, den eine Zuweisung meint, zuerst über `parent_remote_id` der Zuweisung (das die
Remote-ID des Knotens enthält, den die Zuweisung anlegt), dann über `findNode()` (den Knoten des Objekts unter dem
Elternknoten). Der Code lautete `if ( !$existingNode );` mit einer leeren Anweisung, sodass `findNode()` immer lief und
immer entschied.

Mugo Web hat das Semikolon entfernt. Exponential geht weiter, weil die Remote-ID allein nicht reicht:
`eZNodeAssignment::cloneNodeAssignment()` behält `parent_remote_id`, wenn es eine Zuweisung auf ein anderes Objekt
kopiert, sodass die erste Version einer Kopie die Remote-ID des Knotens des Originals trägt. Nur mit entferntem
Semikolon hätte die Veröffentlichung der Kopie den Knoten des Originals übernommen: kein Knoten für die Kopie, und der
Knoten des Originals überschrieben. Jetzt wird der Knoten der Remote-ID nur genommen, wenn seine `contentobject_id`
das Objekt und seine `parent_node_id` der Elternknoten der Zuweisung ist; in jedem anderen Fall läuft `findNode()` wie
bisher. Wo beide einen Knoten finden, ist es derselbe, das Ergebnis einer Veröffentlichung bleibt also gleich; die
Suche über die Remote-ID spart jetzt die zweite Abfrage, statt verworfen zu werden.

Die Datenbanktests in `tests/tests/kernel/content/eZContentOperationCollectionPublishNodeTest.php` veröffentlichen ein
neues Objekt, eine neue Version, einen neuen Ort und eine Zuweisung mit der Remote-ID eines Knotens eines anderen
Objekts und prüfen danach jeweils die Knoten. Die Suchmaschine (`ezsearchengine.php`) hatte dasselbe leere `if` bei der
Behandlung von `www.`-Adressen; es ist ebenfalls korrigiert, und ein Test durchsucht `kernel/` und `lib/` nach jedem
`if` oder `elseif`, dessen Rumpf eine leere Anweisung ist.

## Geprüft und bewusst nicht übernommen

- **Das Auslassen eines Umhänge-`UPDATE` von `ezurlalias_ml`**, wenn das neue Element der alte Elternknoten ist: Die
  Anweisung ändert dann keine Zeile. Sie auszulassen spart eine Anweisung in einem seltenen Fall und behebt keinen
  Fehler.
- **Ein `trim()` der Ausgabe des CSS-Packers**: Leerraum am Anfang und Ende ändert nicht, wie ein Stylesheet wirkt,
  und die gepackten Dateien werden nach Namen zwischengespeichert.

## Vorschläge, nicht in 6.0.15

Diese Funktionen der Forks sind nützlich, aber optional. Sie sind hier festgehalten, damit sie später aufgegriffen
werden können:

- **INI-Werte aus Umgebungsvariablen** (Opencontent, `EZINI_...`): eine Einstellung wie das Datenbankpasswort aus der
  Umgebung statt aus `settings/override/`. Das änderte, wie jede INI-Datei gelesen wird, und braucht eine Entscheidung
  über den Vorrang gegenüber Overrides und über das Caching.
- **`i18n.ini` `OverrideDefault` für `eng-GB`** (Opencontent): erlaubt einer Site, die eingebauten englischen Texte
  durch eine eigene Übersetzungsdatei zu ersetzen.
- **Eine Flagge für Griechisch** (`share/icons/flags/ell-GR.gif`): Das Locale `ell-GR` gibt es, die Flagge nicht. Die
  Flaggen sind ein GIF-Satz mit 18 mal 12 Pixeln; eine griechische Flagge sollte aus demselben Satz stammen, nicht neu
  gezeichnet werden.
- **Die Locales `moc-IT` und `lad-IT`** (Opencontent): zwei Regionalsprachen Italiens.

## Dank

Gemeldet und beigetragen von Ekkehard Dörre (CJW) in Pull Request #345 von `se7enxweb/exponential`. Gefunden wurden
die Korrekturen von Opencontent und Mugo Web, die die beiden aktiven Forks des Kernels pflegen, aus dem Exponential
hervorgegangen ist; jede ist für Exponential neu geschrieben, und die Tabelle oben nennt den Fork, von dem sie stammt.

## Verwandte Seiten

- [Upgrade-Hinweise: Korrekturen aus den Forks von Opencontent und Mugo Web](../../bc/6.0/korrekturen-aus-den-legacy-forks.md)
- [Sicherheitshärtung](../../hilfe/sicherheitshaertung.md)
- [site.ini [URLTranslator]](../../beispiele/ini-einstellungen/site/URLTranslator.md),
  [file.ini [FileSettings]](../../beispiele/ini-einstellungen/file/FileSettings.md),
  [ezjscore.ini [ezjscServer_ezjscnode]](../../beispiele/ini-einstellungen/ezjscore-ezjscore/ezjscServer_ezjscnode.md)
- [Das Änderungsprotokoll von 6.0.15](../../../changelogs/6.0/6.0.15.md)
