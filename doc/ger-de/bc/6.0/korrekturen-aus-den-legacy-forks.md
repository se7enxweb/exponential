# Korrekturen aus den Forks von Opencontent und Mugo Web: was Sie beim Upgrade prüfen (6.0.15)

6.0.15 übernimmt Korrekturen der Forks von Opencontent und Mugo Web (Pull Request #345, beigetragen von Ekkehard Dörre
von CJW) und schließt die Punkte, die diese Durchsicht offen ließ. Die meisten beseitigen nur Warnungen und Fehler.
Fünf ändern, was eine Site tut; diese Seite listet sie mit dem, was zu prüfen ist, und wie das bisherige Verhalten
erhalten bleibt. Die Funktionsseite erklärt jede ausführlich:
[Korrekturen aus den Forks von Opencontent und Mugo Web](../../funktionen/6.0/korrekturen-aus-den-legacy-forks.md).

## URL-Aliase tiefer als 30 Pfadelemente werden nicht gefunden

`eZURLAliasML::translate()` und `fetchByPath()` schlagen höchstens `site.ini [URLTranslator] MaxPathDepth`
Pfadelemente nach (neu, Standard `30`). Eine URL mit mehr Elementen antwortet „nicht gefunden“ ohne
Datenbankabfrage; bisher lief eine Abfrage mit einem Join von `ezurlalias_ml` je Element.

- **Wen es betrifft:** eine Site mit Inhalten, die tiefer als 30 Ebenen verschachtelt sind (das Pfadpräfix eines
  Siteaccess zählt als eine Ebene), oder mit eigenen Aliasen aus mehr als 30 Elementen. View-Parameter
  (`/(offset)/10`) zählen nicht.
- **Was zu tun ist:** auf fast jeder Site nichts. Sonst setzen Sie in `settings/override/site.ini.append.php` einen
  höheren Wert:

  ```ini
  [URLTranslator]
  MaxPathDepth=40
  ```

  Ein Wert unter 1 bedeutet 30.

## Jeder eZLog-Eintrag ist eine Zeile

`eZLog::write()` und `eZLog::writeStorageLog()` ersetzen jeden Zeilenumbruch der Nachricht durch ein Leerzeichen,
sodass ein protokollierter Wert keine weiteren Einträge vortäuschen kann.

- **Wen es betrifft:** Code, der mit `eZLog::write()` eine Nachricht schreibt, die mehrere Zeilen umfassen soll (ein
  Stack-Trace, ein Dump), und Skripte, die ein solches Log auswerten und diese Zeilen erwarten.
- **Was zu tun ist:** mehrzeiligen Text mit einem `eZLog::write()`-Aufruf je Zeile oder mit `eZDebug::writeError()`
  schreiben. Es gibt keine Einstellung, um das abzuschalten.

## Mehr Upload-Endungen werden abgelehnt

`file.ini [FileSettings] FileExtensionBlackList[]` ergänzt `php`, `php3`, `phar`, `phpt`, `pht`, `phtml` und `pgif` um
`php4`, `php5`, `php7`, `php8`, `phps`, `shtml`, `cgi`, `pl`, `asp`, `aspx`, `jsp` und `htaccess`. Die Datentypen
Datei, Medien und Bild lehnen einen Upload mit einer dieser Endungen ab („A valid file is required. The following file
extensions are blacklisted: ...“).

- **Wen es betrifft:** eine Site, deren Redakteure Perl-Skripte (`.pl`) oder andere aufgeführte Dateien als Downloads
  hochladen.
- **Was zu tun ist:** solche Dateien in ein Archiv (`.zip`) packen, oder die Liste in
  `settings/override/file.ini.append.php` ersetzen: zuerst ein leeres `FileExtensionBlackList[]`, danach die Endungen,
  die abgelehnt werden sollen. Eine Site, die die Liste schon ersetzt, behält ihre eigene; vergleichen Sie sie mit der
  neuen.
- Vor dem Upgrade gespeicherte Dateien bleiben, wo sie sind.

## ezjscnode::subtree liefert höchstens 500 Einträge

`ezjscore.ini [ezjscServer_ezjscnode] HardLimit` ist `500` statt `false`. Eine Anfrage an
`ezjscore/call/ezjscnode::subtree` oder `expsubitems::rows`, die mehr als 500 Einträge verlangt, erhält 500.

- **Wen es betrifft:** ein eigenes Skript oder Frontend, das mehr als 500 Kinder eines Knotens in einer Anfrage holt.
  Die Unterelemente-Liste der Administration verlangt höchstens 500.
- **Was zu tun ist:** die Kinder mit dem Offset-Argument seitenweise holen, oder `HardLimit=false` (oder eine größere
  Zahl) in `settings/override/ezjscore.ini.append.php` setzen.

## Eine als Alias angegebene absolute URL wird als ihr Pfad gespeichert

`eZURLAliasML::storePath()` speichert `https://www.example.com/news` als `news` statt als die verschachtelten
Platzhalter-Aliase `https/noname2/www-example-com/news`.

- **Wen es betrifft:** nur die Art, wie eine eingefügte Adresse gespeichert wird. Vor dem Upgrade angelegte Aliase,
  auch solche verschachtelten, bleiben, wie sie sind; entfernen Sie unerwünschte auf den URL-Alias-Seiten der
  Administration.
- **Was zu tun ist:** nichts.

## Unverändert, aber gut zu wissen

- **publishNode():** Das leere `if ( !$existingNode );` in `eZContentOperationCollection::publishNode()` ist so
  korrigiert, dass der über die Remote-ID einer Zuweisung gefundene Knoten nur genommen wird, wenn er der Knoten des
  Objekts unter dem Elternknoten der Zuweisung ist; sonst entscheidet `findNode()`, wie immer. Eine Veröffentlichung
  ergibt dieselben Knoten wie bisher. Die Korrektur von Mugo Web, die nur das Semikolon entfernt, hätte die erste
  Veröffentlichung eines kopierten Objekts den Knoten des Originals übernehmen lassen, weil eine kopierte Zuweisung die
  Remote-ID des Originals behält.
- **REST-Listener:** `site.ini [Event] Listeners[]` laufen jetzt auch bei Anfragen an die REST-API. Ein Listener, der
  bei REST-Anfragen nicht handeln soll, prüft die Anfrage selbst, etwa ihre URI.
- **Der Flash-Player des Online-Editors:** `moxieplayer.swf` ist entfernt. Eine Site, die das Media-Plugin von TinyMCE
  eingeschaltet hat und sich für MP4- oder FLV-Videos auf dessen Flash-Ersatz verließ, nennt einen eigenen Player in
  `flash_video_player_url`; jeder aktuelle Browser spielt MP4 im Element `<video>`, das das Plugin schreibt.

## Code und Tests

- `tests/tests/kernel/classes/expLegacyForkFixesTest.php` (ohne Datenbank): jede Korrektur und jede Ergänzung, jede
  schlägt ohne ihre Änderung fehl.
- `tests/tests/kernel/content/eZContentOperationCollectionPublishNodeTest.php` (Datenbank): der
  Veröffentlichungsablauf für ein neues Objekt, eine neue Version, einen neuen Ort und eine kopierte Zuweisung.
