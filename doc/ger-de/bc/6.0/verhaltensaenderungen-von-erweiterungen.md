# Verhaltensänderungen der Legacy-Erweiterungen (September und Oktober 2026)

Lesen Sie diese Seite, bevor Sie eine Website aktualisieren, die die Legacy-Erweiterungen, Designs oder Themes
verwendet, die mit oder neben Exponential 6.0.15 ausgeliefert werden. Zwischen dem 22. September und dem 2. Oktober
2026 wurden alle auf denselben Stand gebracht. Die meisten Änderungen sind unsichtbar; diese Seite führt die auf, die
es nicht sind, in der Reihenfolge, in der Sie sie prüfen sollten, jeweils mit Prüfung und Lösung. Die eigene Seite
jeder Erweiterung unter [Erweiterungen](../../../eng-us/features/6.0/extensions/README.md) führt ihre Releases auf.

## Kurze Checkliste

1. Aktualisieren Sie die Erweiterungen, die Sie verwenden, auf ihr aktuelles Release (siehe die
   [Release Notes](../../../changelogs/extensions/README.md)). Lassen Sie `se7enxweb/expui` installiert.
2. Erzeugen Sie die Autoloads neu und leeren Sie die Caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

3. Durchsuchen Sie Ihre eigenen Erweiterungen und Designs nach den Mustern der Abschnitte 1, 4 und 6 unten.
4. Prüfen Sie Rollen auf umbenannte und neue Richtlinien (Abschnitt 7) und handeln Sie bei den Sicherheitskorrekturen
   (Abschnitt 9).
5. Erneuern Sie alles, was gepackte Skripte und Styles cacht (`ezjscore` 1.5; `--clear-tag=template,content`).

## 1. Persistente PHP-Worker: Modul-Views deklarieren auf Dateiebene nicht mehr ohne Schutz

**Was sich geändert hat (22. September 2026).** Exponential bindet bei jeder Anfrage eine Modul-View ein. Eine
Funktion oder Klasse, die auf der obersten Ebene dieser Datei deklariert ist, wird beim zweiten Einbinden ein zweites
Mal deklariert, und das ist fatal. Mit einem Prozess pro Anfrage passiert das nie; unter einem persistenten Worker
(Exponential Velocity, FrankenPHP) beendet es den Worker bei der zweiten Anfrage, und der Client erhält ein 502.

In `explayouts_ui`, `explayouts_ui_api`, `cjw_newsletter`, `eztags`, `ezupdate`, `ezflow`, `ezodf`, `ezpm`,
`xrowmetadata`, `nxc_powercontent`, `syndication`, `sevenx_dse` und `xrowextract` ist jede solche Deklaration jetzt in
`function_exists()` oder `class_exists()` eingeschlossen und an den Anfang ihrer Datei verschoben. (PHP bindet eine
unbedingte Deklaration der obersten Ebene, bevor die erste Zeile läuft; eine geschützte existiert erst, wenn die
Ausführung sie erreicht, sodass eine Datei, die eine Funktion oberhalb ihrer eigenen Deklaration aufrief, sonst
brechen würde.) Unter Apache und PHP-FPM ist das Verhalten unverändert.

**So prüfen Sie Ihren eigenen Code.**

```bash
grep -rln "^function \|^class " extension/*/modules extension/*/classes/runnable/views 2>/dev/null
```

Jede aufgeführte Datei deklariert auf Dateiebene und braucht einen Schutz.

**So beheben Sie es.**

- Schließen Sie jede Deklaration ein:
  `if ( !function_exists( 'myHelper' ) ) { function myHelper() { ... } }` und
  `if ( !class_exists( 'MyClass', false ) ) { class MyClass { ... } }`.
- Besser: Machen Sie die View zu einer Klasse, wie in
  [Befehle, Cronjob-Teile und Modul-Views als Klassen](cli-cronjob-und-view-abstraktionen.md)
  beschrieben.
- Vermeiden Sie Zustand in statischen Funktionsvariablen und `static`-Variablen, der zwischen Anfragen zurückgesetzt
  werden muss. Beispiele: `ezstarrating` 6.0.3, dessen Cache der Bewertungsstatistik in einer statischen
  Funktionsvariablen lag; `sevenx_dse` 1.1.1, bei dem das für die Nonce der Sicherheitsrichtlinie galt.

## 2. Seiten für vergessene Passwörter verraten nicht mehr, ob eine Adresse ein Konto hat

**Was sich geändert hat.** `user/forgotpassword` des Kernels antwortet für eine gültige Adresse nicht mehr mit „There
is no registered user with that email address“, weil das jedem erlaubt, Adressen eine nach der anderen zu testen. Die
Designs und die Erweiterung, die eine eigene Kopie der Seite hatten, taten am 27. September 2026 dasselbe: `ezwebin`
6.0.7, `ezdemo` 6.0.4 und `ezmbpaex` (die View `userpaex/forgotpassword`) 6.0.2. Jetzt gilt:

- Eine unbekannte, aber gültige Adresse erhält dieselbe Seite wie eine bekannte („wenn ein Konto mit der Adresse
  registriert ist, wurde eine E-Mail daran gesendet“);
- eine Eingabe, die keine E-Mail-Adresse ist, erhält „Bitte geben Sie eine gültige E-Mail-Adresse ein.“;
- eine als Array gesendete Adresse wird behandelt, als sei keine angegeben;
- jede Adresse, die die Seite ausgibt, wird mit `|wash` maskiert.

Die Meldungen verwenden den Übersetzungskontext `design/standard/user/forgotpassword` des Kernels, daher gelten
vorhandene Übersetzungen. `ezmbpaex` 6.0.3 macht den Schlüssel zum Zurücksetzen außerdem zu 128 Zufallsbits aus der
sicheren Quelle des Systems (bisher ein `md5` aus der Zeit und `mt_rand()`). Schlüssel und Links behalten ihre Form;
vorhandene Links funktionieren, bis sie ablaufen.

**So prüfen Sie es.** Überschreibt das Design Ihrer Website `user/forgotpassword.tpl` oder das Template von
`userpaex`, vergleichen Sie es mit dem des Kernels. Tests oder Dokumentation, die die alte Meldung erwarten, müssen
sich ändern.

## 3. Fehlerseiten zeigen ihren eigenen Fehler im Seitentitel

**Was sich geändert hat.** Jede Fehlerseite erreicht das Pagelayout mit derselben URI, daher gab der Fehler, der
zuerst gecacht wurde, allen späteren seinen Titel und Pfad: Ein „Seite nicht gefunden“ konnte den Titel des Fehlers
„Zugriff verweigert“ tragen. Die Cache-Blöcke für Kopf, Pfad und Menü von `ezwebin` 6.0.8, `ezdemo` 6.0.5 und
`sevenx_themes_simple` 1.0.15 fügen dem URI-Schlüssel jetzt Fehlertyp und Fehlernummer hinzu, wenn die Seite eine
Fehlerseite ist. Jede andere Seite behält genau denselben Cache-Schlüssel.

**So prüfen und beheben Sie es.** Hat Ihr eigenes `pagelayout.tpl` ein
`{cache-block keys=array( $module_result.uri, ... )}` um Kopf, Pfad oder Menüs, schlüsseln Sie Fehlerseiten ebenfalls
nach Fehlertyp und Fehlernummer, wie es die Erweiterungen tun:

```
{def $uri_cache_key = $module_result.uri}
{if is_set( $module_result.errorType )}
    {set $uri_cache_key = concat( $module_result.uri, '|error|', $module_result.errorType, '|', first_set( $module_result.errorNumber, '' ) )}
{/if}
{cache-block keys=array( $uri_cache_key, $user_hash, $extra_cache_key )}
    ...
{/cache-block}
```

## 4. YUI ist weg; jQuery 4 und Exponential UI ersetzen es

Das ganze Bild und die Ersatztabelle stehen in [YUI aus Exponential entfernt](yui-entfernung.md). Für die
Erweiterungen:

| Erweiterung | Release | Inkompatibler Teil |
|---|---|---|
| `ezjscore` | 1.5.0 | `ezjsc::yui2`, `ezjsc::yui3`, `ezjsc::yui3io`, die YUI-Bibliotheken und `ezjscore.ini [YUI3]` entfernt; `PreferredLibrary` ist `jquery` |
| `ezautosave` | 6.0.6, 6.0.7 | `ezautosubmit.js`, `ezcontentpreview.js` (Administration) und das YUI-Autosave im Frontend entfernt; verwenden Sie `exp::autosave` |
| `ezmultiupload` | 6.0.6 | `design/standard/javascript/ezmultiupload.js` und der YUI-3-Uploader entfernt; `upload.tpl` verwendet `exp::io`, `exp::dialog`, `exp::upload` |
| `ezstarrating` | 6.0.6 | `ezstarrating_yui3.js` entfernt; `ezstarrating_jquery.js` wird immer verwendet |
| `eztags` | 2.4.9 | `eztags_children_yui.tpl` umbenannt in `eztags_children_table.tpl`; `ezjsc::yui2` wird nicht mehr jeder Admin-Seite hinzugefügt; `$.fn.eZTagsChildren` ist der alte Name der Tabelle von Exponential UI |
| `ezflow` | 6.1.4 | YUI-Widgets und -Skin, die Bibliothek Prototype und die Sprite-Kopie von YUI entfernt; Tab-Klassen `.ezpage-tabs*` |
| `ezwebin`, `ezwt`, `ezdemo` | 6.0.15, 6.0.8, 6.0.8 | Datumsfelder verwenden `exp::datepicker`; die Sortierseite der Werkzeugleiste und die Demo-Galerien laufen mit jQuery |
| `cjw_newsletter` | 4.1.9 | die Liste der Unterelemente in admin2 lädt kein Skript mehr, das es nicht mehr gibt |
| `sevenx_themes_simple` | 1.0.20 | YUI-Einstellungen und Kalender-Styles entfernt |

Diese Erweiterungen verlangen `se7enxweb/expui ^1.0.0.1`, wo sie dessen Module verwenden (`eztags`, `ezwebin`,
`ezwt`, `ezmultiupload`, `ezautosave`).

jQuery 4 selbst hat Hilfsfunktionen entfernt: `$.isArray`, `$.trim`, `$.proxy`, `$.browser`, die Kurzformen `.click()`,
`.bind()` und `.unbind()` und das boolesche `.attr()`. `eztags`, `ezie`, `ezstarrating`, `cjw_newsletter`, `ezwebin`,
`ezdemo`, `xrowmetadata` und das Magnific Popup des Themes simple wurden portiert (`.on()`, `.off()`, `.prop()`,
`Array.isArray`).

**So prüfen Sie Ihre eigenen Skripte:**

```bash
grep -rnE 'YUI\(|YAHOO\.|ezjsc::yui|\$\.browser|\$\.isArray|\.bind\(|\.unbind\(|\.load\(function' extension/ design/
```

## 5. HTML5-Markup in den Designs

`ezwebin` 6.0.3, sein Installationspaket `ezwebin-ezpackage`, `ezwt` 6.0.3 und `sevenx_themes_simple` 1.0.12 (Juli
2026) schreiben HTML5:

- selbstschließende XHTML-Schrägstriche (`<br />`, `<input />`, `<img />`, `<meta />`, `<link />`) sind entfernt;
- veraltete Attribute `type` und `language` an Tags `<script>`, `<style>` und Stylesheet-`<link>` sind entfernt;
- das Stylesheet der Werkzeugleiste verzichtet auf Herstellerpräfixe.

`ezdemo`, `ezodf` und `ezflow` folgten mit `<br>` (27. September). `bcwebsitestatistics` 1.0.6 tut dasselbe für die
Analyse-Tags. Überschreibt Ihr Design diese Templates, muss ein Style oder Skript, das nach den alten Attributen
auswählt oder XHTML-Ausgabe erwartet, möglicherweise geändert werden.

## 6. Code, der auf jeder Datenbank laufen muss

Eine Reihe von Korrekturen (29. und 30. September 2026) hat Abfragen portabel gemacht. Eigener Code sollte denselben
Regeln folgen; die Korrekturen der Erweiterungen zeigen jede davon:

| Problem | Wo | Stattdessen verwenden |
|---|---|---|
| An SQL angehängtes `LIMIT n`, `DELETE ... ORDER BY ... LIMIT` | Exportvorschau von `cjw_newsletter` (Oracle ORA-03049), Kürzen des Block-Pools von `ezflow` | den Parameter `limit` von `eZDB::arrayQuery()`, und nach Schlüssel löschen |
| `%` oder `MOD()` für Bit-Tests | `eztags` (`language_mask`), SQLite und Oracle | `eZDB::bitAnd( column, 1 )` |
| Zeichenketten in doppelten Anführungszeichen (`LIKE "%x%"`) | Benutzersuche von `cjw_newsletter`, Baumfilter von `eztags` | Zeichenketten in einfachen Anführungszeichen; doppelte Anführungszeichen sind außerhalb von MySQL Bezeichner |
| Backticks und abschließende Semikolons | Abbruch des Versands in `cjw_newsletter` | einfache Bezeichner, kein Semikolon |
| `SELECT DISTINCT` über CLOB-Spalten | Benutzerfilter von `cjw_newsletter` | `EXISTS` |
| `AS` vor einem Tabellenalias | `eZPageBlock::fetch()` von `ezflow` (Oracle ORA-00933) | `table alias` ohne `AS` |
| nur `FROM_UNIXTIME()` | `ezarchive()` von `ezwebin` | ein datenbankspezifischer Zweig (Oracle berechnet Daten ab 1970-01-01) |
| Joins und `GROUP BY` unter MongoDB | `eztags` (2.3.4), Listen von `explayouts_ui` | Collections getrennt lesen und in PHP zusammenführen |

## 7. Geänderte Namen, Registerkarten und Richtlinien

| Erweiterung | Änderung | Was zu tun ist |
|---|---|---|
| `git_manager` 2.0.4 | Die View `git_manager/dump` und die Richtlinienfunktion `dump` wurden zu `backup`; `/git_manager/dump` leitet weiter. | Rollen, die `dump` vergeben, funktionieren weiter. Benennen Sie sie mit `php extension/git_manager/bin/php/upgrade-policy-dump-to-backup.php --dry-run` um, dann ohne `--dry-run`. |
| `git_manager` 2.0.6 bis 2.0.8 | Neue Richtlinienfunktionen `push`, `remotes`, `submodules`. | Vergeben Sie sie an die Rollen, die pushen, Remotes oder Submodule ändern dürfen. Eine Rolle mit jeder Funktion des Moduls hat sie. |
| `xrowextract` 2.5.3 | Registerkarten umbenannt in **Inhaltsexport einer Klasse**, **Inhaltsexport mehrerer Klassen**, **Inhaltsdatei importieren**, **Inhaltspaket importieren** (die Views `csv`, `archive`, `import`, `package` behalten ihre Adressen und Richtlinien); neue Richtlinienfunktionen `jobs`, `all_jobs`, `schedule`, `destinations`, `history`, `password_hash`. | Vergeben Sie die neuen Funktionen, wo gewünscht. |
| `explayouts_ui` | Die Registerkarte heißt **Layouts**; „Regeln“ heißen **Layout-Zuordnungen**; der Zieltyp `content_node` erscheint als `node`; die Klassenbedingung heißt `class`; Bedingungsnamen `ibexa_*` werden nicht mehr angeboten (vorhandene funktionieren weiter). | Nichts. |
| `eztags` 2.3.5, 2.4.1 | Menüeintrag und Tooltip lauten **Tags**. | Nichts. |
| `bccie` 1.1.4 | Menüname **CIE**. | Nichts. |
| `bccie` 1.1.12 | Das Entfernen gesammelter Informationen braucht die neue Richtlinienfunktion `remove` (bisher nur `read`). | Vergeben Sie `bccie / remove` an die Rollen, die gesammelte Informationen entfernen dürfen; eine Rolle mit jeder Funktion des Moduls hat sie. |
| `bccie` 1.1.12 | Exportdateien geändert: Text bleibt UTF-8 (er wurde nach ISO-8859-1 umgewandelt und verlor Zeichen), CSV-Zellen maskieren Anführungszeichen und die Zeile hat nach der letzten Zelle kein Trennzeichen, Text, der mit `=`, `+`, `-` oder `@` beginnt, erhält ein führendes einfaches Anführungszeichen, die SYLK-Datei hat eine Kopfzeile mit den Feldnamen, geplante Dateien beginnen mit der Spalte der Sammlungs-ID, die ihr Kopf bereits ankündigte. | Liest ein Skript die Dateien, lesen Sie UTF-8 und entfernen Sie das führende Anführungszeichen, wo es darauf ankommt. |
| `sevenx_dse` 1.1.2 | Navigationsteil `dsenavigationpart` (bisher `ezupdatenavigationpart`). | Haben Sie die alte Kennung überschrieben, ändern Sie sie. |
| `ezupdate` 1.1.3 | Die Views `dump` und `show` und der Link *Dump assets*, die zu `git_manager` gehörten, sind entfernt. | Verwenden Sie `git_manager`. |
| `ezie` 6.0.6 | Die Werkzeuge Weichzeichnen, Tonwerte und Sättigung (sie haben nie funktioniert) sind entfernt. | Nichts. |

## 8. Die API des Layout-Editors: Tokens, Entwürfe und gesperrte Zonen

- `explayouts_ui_api` 1.3.3 lehnt jede Anfrage außer GET, die das Formular-Token der Sitzung nicht mitbringt (Header
  `X-CSRF-Token` oder Feld `ezxform_token`), mit HTTP 403 und dem JSON-Body
  `{"error":{"code":403,"reason":"form_token_missing"}}` ab.
- 1.3.4 lehnt Schreibvorgänge an Blöcken ab, sofern der Block nicht zu einem Entwurf gehört.
- 1.2.1 lehnt Schreibvorgänge an Blöcken eines veröffentlichten gemeinsamen Layouts und in verknüpfte Zonen ab.

Skripte, die die API angesteuert haben, müssen das Token von `GET /explayouts_ui_api/app/api/config` holen
(`csrf_token`), an Entwürfen arbeiten und veröffentlichen. Siehe die
[Spezifikation der API](../../spezifikationen/6.0/explayouts-ui-api.md).

## 9. Sicherheitskorrekturen, die Handeln erfordern können

| Erweiterung | Release | Korrektur | Handlung |
|---|---|---|---|
| `ezie` | 6.0.6 | Der Name des Wasserzeichens konnte jedes Bild lesen (`../`); der Arbeitsordner kam aus einem Schlüssel des Browsers und konnte rekursiv gelöscht werden; Berechtigungen wurden nur in `prepare` geprüft. | Aktualisieren; sonst nichts. |
| `git_manager` | 2.0.8 | Sicherungen waren für alle lesbar; das Datenbankpasswort stand auf der Kommandozeile von `mysqldump`; Branch-Namen und Hashes wurden nicht maskiert; der AGPL-Dump behielt Geheimnisse und Formularantworten. | Aktualisieren. **Prüfen Sie die Berechtigungen vorhandener Sicherungen** unter `var/site/backups/captions` erneut (neue werden nur für den Besitzer angelegt) und löschen Sie Sicherungen, die Sie nicht mehr brauchen. |
| `cjw_newsletter` | 4.1.15 | Objektinjektion über ein verstecktes Formularfeld, offene Weiterleitungen, erratbare Hashes, ungeprüfte Eingaben. | Aktualisieren. |
| `xrowextract` | 2.5.3, 2.5.4 | URLs importierter Dateien auf Schema, Host und Port der eigenen Website beschränkt; IDs vor Abfragen geprüft; Paketarchive nur mit einfachen Dateien akzeptiert; Paketnamen maskiert. | Aktualisieren. |
| `ezmbpaex` | 6.0.3 | Erratbarer Schlüssel zum Zurücksetzen. | Aktualisieren; alte Links funktionieren, bis sie ablaufen. |
| `explayouts_ui_api` | 1.3.3, 1.3.4 | Formular-Token bei jedem Schreiben; nur Entwürfe. | Siehe Abschnitt 8. |
| `owsimpleoperator` | (Einstellung) | Die ausgelieferte `PermittedFunctionList` enthält `file_get_contents`, womit ein Template jede Datei lesen kann, die der Webserver lesen kann. | Entfernen Sie es aus den Produktionseinstellungen, wenn Sie es nicht brauchen. |
| `bcwebsitestatistics` | (Einstellung) | Der ausgelieferte Wert `Urchin` ist eine Beispiel-Tracking-ID, nicht Ihre. | Setzen Sie Ihre eigene in `settings/override/bcwebsitestatistics.ini.append.php`. |
| `AdminAid` | (Einstellung) | Benutzerwechsel ohne Passwort. | Schalten Sie `LimitByIP` ein, beschränken Sie `UserSwitchIDLimit`, entfernen Sie die Erweiterung aus der Produktion. |

## 10. Metadaten, Übersetzungen und Integritätsmanifeste

- Jede Erweiterung hat jetzt eine `ezinfo.php` und eine `extension.xml`, die beide Name, Version, Copyright, Lizenz
  und Website angeben, sodass **Setup > Über** und die Upgrade-Prüfungen sie zeigen. Einige zeigten den
  Verzeichnisnamen als ihren Namen; sie zeigen jetzt ihre echten Namen (zum Beispiel „Enhanced eZBinary File Type“,
  „Xrow Meta Data“, „Exponential Layouts UI API“).
- Registerkartennamen, Tooltips, Navigationsteile und Einträge des linken Menüs von Erweiterungen werden in den
  Kontexten `design/admin/pagelayout`, `kernel/navigationpart` und `design/admin/parts/<menu>/menu` übersetzt; fügen
  Sie Ihre Sprache dort hinzu.
- Die `share/filelist.md5` von `cjw_newsletter` (das Manifest hinter **Setup > Systemupgrade Überprüfung >
  Überprüfe Dateikonsistenz**) entspricht jetzt den Dateien. Vor 4.1.5 meldete die Prüfung Dateien, die niemand
  angefasst hatte.
- Die Copyright-Hinweise in den Erweiterungen nennen zuerst „1998 - 2026 7x & Exponential Foundation“, über den
  ursprünglichen Hinweisen von eZ Systems, die bleiben, wie es die GPL verlangt.

## 11. REST API v1 und v2 kommen aus einer Erweiterung; ezprestapiprovider ist veraltet

**Was sich geändert hat (15. Oktober 2026).** `ezprestapi` 1.2.6 liefert REST API v1 und v2 allein aus: eine
Provider-Klasse, `ezp7xRestApiProvider`, für die Präfixe `ezp` und `ezpl`, eine Routentabelle, eine OAuth2-Einrichtung.
`ezprestapiprovider` 6.0.4 ist sein letztes Release und eine Kompatibilitätsschicht: `ezpRestApiProvider` und
`ezpRestContentController` erweitern die Klassen von `ezprestapi`, seine `rest.ini` ordnet `ezp` und `ezpl`
`ezp7xRestApiProvider` zu, und die fünf Klassen, die es doppelt hatte, sind entfernt. Bisher antwortete jede Route
`/api/ezp/v2/...` mit 404, und die Lesezugriffe von v1 liefen ohne die Berechtigungsprüfungen, wenn
`ezprestapiprovider` früher in `ActiveExtensions` stand oder allein aktiv war (wie es der Setup-Assistent früher
aktivierte). Jetzt leitet jede Kombination jede URL an `ezp7xRestContentController`. `OutputFormat=xhtml` füllt
`renderedOutput` wieder; jede andere Antwort von v1 und v2 ist unverändert.

**So prüfen Sie es.** `grep -rn "ezprestapiprovider\|ezpRestApiProvider\|ezpRestContentController" settings/override extension/*/settings extension/*/classes`
findet die Aktivierung, ein Override von `rest.ini`, das den alten Provider nennt, und Code, der den alten Controller
erweitert.

**So beheben Sie es.** Behalten Sie `ActiveExtensions[]=ezprestapi` oder fügen Sie es hinzu und entfernen Sie
`ActiveExtensions[]=ezprestapiprovider`; ändern Sie `ProviderClass[ezp]=ezpRestApiProvider` in
`ezp7xRestApiProvider`; führen Sie dann `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` und
`php bin/php/ezpgenerateautoloads.php -e` aus, laden Sie PHP-FPM neu und starten Sie Velocity neu. Für OAuth-Clients,
Tokens, persönliche API-Schlüssel oder URLs von v1 ändert sich nichts. Die Schritte und die Tabelle vorher und seitdem
stehen in [REST API v1 und v2: eine Erweiterung](../../anleitungen/rest-api-v1-und-v2.md).

## Verwandte Seiten

- [REST API v1 und v2: eine Erweiterung](../../anleitungen/rest-api-v1-und-v2.md)

- [YUI aus Exponential entfernt](yui-entfernung.md)
- [Befehle, Cronjob-Teile und Modul-Views als Klassen](cli-cronjob-und-view-abstraktionen.md)
- [Unterstützung für PHP 8](php8.md), [Velocity-Engines](velocity-engines.md), [Sicherheitshärtung](haertung.md)
- [Release Notes aller Erweiterungen](../../../changelogs/extensions/README.md)
- [Geschichte der Erweiterungen](../../../eng-us/history/extensions/README.md)
- [Eine Erweiterung kann ein Kernel-Modul ersetzen](../../../eng-us/features/6.0/extension-module-override.md)
- [Erweiterungen in mehr als einem Ordner](../../../eng-us/features/6.0/additional-extension-directories.md)
- Geschichte: [Januar 2024, 16. bis 31.](../../../eng-us/history/2024/2024-01b.md), [September 2024](../../../eng-us/history/2024/2024-09.md),
  [Oktober 2024](../../../eng-us/history/2024/2024-10.md)
