# Die Leiste Exp Debug

Lesen Sie diese Seite, wenn Sie den Debug-Bericht verwenden, wenn Sie die Debug-Ausgabe nach IP-Adresse einschränken
oder wenn Ihre Skripte oder Stylesheets mit dem klassischen Bericht arbeiten. Ab Exponential 6.0.15 ist der
Debug-Bericht am unteren Rand einer Seite die **Leiste Exp Debug**: eine angeheftete Leiste mit einer Live-Übersicht der
Anfrage und ein Panel mit Registerkarten über den Abschnitten des Berichts. Ihre Registerkarte Einstellungen zeigt jede
Debug-Einstellung, den wirksamen Wert und die Datei, aus der er kommt, und ändert ihn im gewählten Geltungsbereich
(globales Override, ein Siteaccess oder eine Erweiterung). Jede Änderung wird über die Engine von exp:ini geschrieben
([console-exp-ini.md](console-exp-ini.md)), protokolliert und kann aus der Leiste
rückgängig gemacht werden.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Der HTML-Debug-Bericht ist die Leiste Exp Debug (`lib/ezutils/classes/expdebugbarreport.php`). Neue Registry `settings/debugbar.ini`, Server-Funktionen `expdebugbar::*`, Schreibprotokoll `var/<site>/log/debugbar.log`. Einträge in `DebugIPList[]` können eine Beschriftung und einen Ablauf tragen, und CIDR-Bereiche beider Adressfamilien passen. |
| Wer betroffen ist | Websites, die eine **einfache IPv6-Adresse** in `DebugIPList[]` aufführen: Bisher erhielt jeder IPv6-Client Debug-Ausgabe, sobald die Liste eine enthielt; jetzt bedeutet dieser Eintrag genau diese eine Adresse. Benutzer, die Debug-Einstellungen aus der Leiste ändern: Sie brauchen `setup/setup` (Cache-Steuerung: `setup/managecache`). Eigenes CSS oder Tests, die den alten Überschriftentext abgleichen (er lautet jetzt „Exp Debug“). |
| Wie Sie es prüfen | `grep -n "Settings\[\]" settings/debugbar.ini \| head` und `php vendor/bin/phpunit tests/tests/kernel/classes/debugbar/` |
| Wie Sie es beheben | Fügen Sie jede IPv6-Adresse oder jedes IPv6-Netz, das Debug-Ausgabe braucht, ausdrücklich zu `DebugIPList[]` hinzu. Geben Sie `setup/setup` an Rollen, die debuggen. Die IDs der Abschnitte sind unverändert, daher funktionieren für den klassischen Bericht geschriebene Skripte weiter. |

Textberichte (`Debug=inline` auf der Kommandozeile) und das Popup-Fenster sind unverändert. Ein Engine-Archiv ohne
`expdebugbarreport.php` gibt den klassischen Bericht aus.

Diese Seite beschreibt zuerst die Serverseite (die Registry, Werte und Geltungsbereiche, Schreibvorgänge, das
Protokoll und das Rückgängigmachen, die IP-Liste, die Übersicht, die Server-Funktionen, den Zugriff und die
RAD-Übersicht), dann [die Leiste](#die-leiste) selbst.

| Teil | Wo |
|---|---|
| Registry | `settings/debugbar.ini` und `extension/<ext>/settings/debugbar.ini.append.php` |
| Registry der Einstellungen | `expDebugBarRegistry` (`kernel/classes/debugbar/expdebugbarregistry.php`) |
| Werte, Schreibvorgänge, Rückgängigmachen, Voreinstellungen | `expDebugBarSettings` (`kernel/classes/debugbar/expdebugbarsettings.php`) |
| Engine der IP-Liste | `expDebugBarIPList` (`kernel/classes/debugbar/expdebugbariplist.php`), von `eZDebug::isAllowedByCurrentIP()` verwendet |
| Schreibprotokoll | `expDebugBarLog` (`kernel/classes/debugbar/expdebugbarlog.php`), `var/<site>/log/debugbar.log` |
| Übersicht | `expDebugBarSummary` (`kernel/classes/debugbar/expdebugbarsummary.php`) |
| Server-Funktionen | `expDebugBarServerFunctions`, `[ezjscServer_expdebugbar]` in `extension/ezjscore/settings/ezjscore.ini` |
| Tests | `tests/tests/kernel/classes/debugbar/` |

## Die Einstellungen

`settings/debugbar.ini` führt die Einstellungen in `[DebugBarSettings] Settings[]` auf und beschreibt jede in einem
eigenen Block:

```ini
[Setting_debug_output]
File=site.ini
Block=DebugSettings
Variable=DebugOutput
Type=bool
Label=Debug output
Group=output
Help=Shows the debug report (this bar) at the bottom of every page for those who get debug.
```

| Schlüssel | |
|---|---|
| `File`, `Block`, `Variable` | die Einstellung |
| `Type` | `bool`, `enum` (mit `Values[]`), `list` (optional `Values[]`: was hinzugefügt werden darf), `text`, `iplist`, `userlist` |
| `Label`, `Group`, `Help` | was die Leiste zeigt; `Group` ist eine aus `Groups[]` (`GroupNames[<id>]` benennt sie) |
| `On`, `Off` | nur bool: die geschriebenen Wörter. Ohne sie werden die Wörter des wirksamen Wertes verwendet (`enabled`/`disabled`, `true`/`false`, wobei `Enabled` oder `TRUE` erhalten bleiben) |

Der Kernel registriert 35 Einstellungen in neun Gruppen: Debug-Ausgabe (DebugOutput, Debug inline/popup,
DebugRedirection, DisplayDebugWarnings, DebugLogOnly, DisplayIncludedFiles, AlwaysLog[], ScriptDebugOutput,
DebugToolbar, REST Debug), wer Debug erhält (DebugByIP, DebugIPList[], DebugByUser, DebugUserIDList[]), Templates
(Debug, ShowXHTMLCode, ShowUsedTemplates, ShowTemplatePathComments, ShowMethodDebug, DevelopmentMode, TemplateCompile,
TemplateCache, CompileAccumulators, CompileTimingPoints, CompileComments), Datenbank (SQLOutput, QueryAnalysisOutput,
SlowQueriesOutput, DebugTransactions), Übersetzungen (RegionalSettings Debug und DevelopmentMode), E-Mail
(DebugSending, DebugReceiverEmail), Debug-Bedingungen (debug.ini ConditionDebug), Caching (ViewCaching).

Zwei Arten von Einstellungen werden gefunden statt registriert:

- **`DiscoverBlocks[]=<file>;<block>;<group>`**: jede bool-Variable des Blocks, so wie sie wirksam ist. Der Kernel
  führt `debug.ini;GeneralCondition;conditions` auf, sodass jede Debug-Bedingung (`kernel-content-view`,
  `kernel-urltranslator`, ... und die, die Erweiterungen hinzufügen) ein Schalter ist, mit der ID `cond_<variable>`.
- **Debug-Schalter von Erweiterungen** (`ExtensionDiscovery=enabled`): in den `settings/*.ini`, `*.ini.append.php` und
  `*.ini.append` jeder aktiven Erweiterung eine Variable, deren Name `DiscoveryPattern` (`/debug/i`) entspricht und
  deren Wert ein bool-Wort ist. Ihre ID ist `ext_<ext>_<file>_<block>_<variable>`, ihre Gruppe `extensions`.
  `DiscoveryExclude[]=<ext>` oder `<ext>;<file>` lässt einige aus. Eine Einstellung, die die Registry bereits hat,
  wird nicht zweimal gefunden.

Auf einer Referenzinstallation hat die Leiste 82 Einstellungen: 35 registrierte, 43 Debug-Bedingungen,
4 Schalter von Erweiterungen (cjw_newsletter, owsimpleoperator, bccie).

### Die Einstellungen einer Erweiterung

Eine Erweiterung registriert ihre eigenen in `extension/<ext>/settings/debugbar.ini.append.php`; eZINI führt sie mit
der Datei des Kernels zusammen, und die Einstellung wird als die der Erweiterung gekennzeichnet:

```ini
<?php /* #?ini charset="utf-8"?
[DebugBarSettings]
Settings[]=myext_trace

[Setting_myext_trace]
File=myext.ini
Block=TraceSettings
Variable=Trace
Type=bool
Label=Trace calls
Group=extensions
Help=Writes every call of the myext API to var/log/myext.log.
*/ ?>
```

### Voreinstellungen

`Presets[]` nennt Blöcke `[Preset_<id>]`: `Name`, `Description`, `Values[<setting id>]=<value>` (für ein bool `on` oder
`off`). Der Kernel hat **Template work**, **SQL tuning**, **Everything** und **Off**. Ein Benutzer speichert eigene
Voreinstellungen aus der Leiste (in seinen Benutzereinstellungen gespeichert, `expdebugbar_presets`), entweder mit
angegebenen Werten oder als Schnappschuss der wirksamen bool- und enum-Einstellungen.

Das Anwenden einer Voreinstellung schreibt jeden Wert, der vom wirksamen abweicht, als eigenen Schreibvorgang, alle in
einer Gruppe, sodass die Gruppe auf einmal rückgängig gemacht werden kann. Braucht einer der Schreibvorgänge eine
Bestätigung (siehe unten), wird nichts geschrieben.

## Wirksame Werte, Herkunft und Geltungsbereiche

Für einen Siteaccess (standardmäßig den aktuellen) liefert `expDebugBarSettings::describe()`:

- **effective**: den eigenen Wert von eZINI, aus einem nicht gecachten eZINI mit den Override-Verzeichnissen dieses
  Siteaccess (`expIniLocator::iniFor()`), sodass ein Schreibvorgang sofort sichtbar ist, bevor der INI-Cache geleert
  wird;
- **files**: jede Datei der Ladereihenfolge, die die Variable setzt, mit ihrem Geltungsbereich;
- **origin**: die letzte davon, die Datei, deren Wert gewinnt;
- **default**: den Wert aus `settings/<file>.ini`;
- **scopes**: den Wert, den die Datei jedes Geltungsbereichs liefert (`null`: sie setzt ihn nicht).

Die Geltungsbereiche, die die Leiste anbietet, sind `global` (`settings/override`), jeder Siteaccess, jede aktive
Erweiterung und die Siteaccess-Verzeichnisse der Erweiterungen für den Siteaccess. Der Standard-Geltungsbereich ist der
Siteaccess. Die ausgelieferten Standardwerte (`settings/*.ini`) werden nie geschrieben. Die Beschreibung aller 82
Einstellungen liest jede INI-Datei einmal und dauert etwa 140 ms.

## Schreibvorgänge

`expDebugBarSettings::write( $id, $op, $value, $scope, $options )`:

| op | für | tut |
|---|---|---|
| `set` | bool, enum, text | bool nimmt `on/off/1/0/true/false/enabled/disabled/yes/no` an und schreibt die eigenen Wörter der Einstellung |
| `toggle` | bool | kehrt den wirksamen Wert um |
| `unset` | alle | entfernt die Variable aus der Datei dieses Geltungsbereichs (dann gewinnt die nächste Datei der Ladereihenfolge) |
| `add`, `remove` | Listen | ein Wert; bei einer IP-Liste nimmt `remove` die Adresse und findet die Zeile mit ihrer Beschriftung |
| `replace` | Listen | `Var[]` (die Rücksetzzeile) und die Werte |

Der Editor (`expIniEditor`) ändert nur die Zeilen der Einstellung, behält Besitzer, Gruppe und Modus der Datei, schreibt
atomar und kopiert die alte Datei zuerst nach `var/backup/ini/<stamp>/<path>`. Nach dem Schreiben wird der Cache-Tag ini
geleert. PHP-FPM sieht die Änderung bei seiner nächsten Anfrage; die Worker von Velocity behalten die Einstellungen
ihres Vorwärmens und sehen sie nach `exp:velocity restart`, was die Antwort sagt.

`dry_run` gibt das Diff zurück und schreibt nichts. Ein Wert, den der Editor ablehnt (ein Zeilenumbruch, `##`, `*/` in
einer in PHP eingeschlossenen Datei), wird mit seinem Grund abgelehnt.

### Sich nie selbst aussperren

Ein Schreibvorgang auf `DebugByIP`, `DebugIPList[]`, `DebugByUser`, `DebugUserIDList[]` oder `DebugOutput` wird gegen
die Werte geprüft, die **danach** wirksam wären, berechnet durch erneutes Durchlaufen der Ladereihenfolge mit der neuen
Datei. Er wird mit `needs_confirm` abgelehnt, wenn:

- **lockout**: DebugByIP eingeschaltet würde und die Liste die Adresse der aktuellen Anfrage nicht enthielte (ein
  abgelaufener Eintrag zählt nicht), während sie jetzt Debug-Ausgabe erhält; oder DebugByUser eingeschaltet würde und
  die Benutzerliste den aktuellen Benutzer nicht enthielte;
- **open**: die Liste einen aktiven Eintrag `/0` bei eingeschaltetem DebugByIP erhielte, oder DebugByIP ausgeschaltet
  würde, während DebugOutput ein- und DebugByUser ausgeschaltet ist, sodass jeder Besucher Debug-Ausgabe erhielte.

Mit `confirm=1` erneut gesendet, wird geschrieben.

## Die IP-Liste

Einträge in `DebugIPList[]` behalten ihr einfaches Format und können eine Beschriftung und einen Ablauf tragen:

```ini
DebugIPList[]=203.0.113.7
DebugIPList[]=10.0.0.0/8
DebugIPList[]=2001:db8::/32 ; VPN
DebugIPList[]=203.0.113.7/32 ; Laptop ; expires=2026-10-02T15:00
DebugIPList[]=commandline
```

- Adressen und Netze beider Familien, CIDR-Präfixe an jedem Bit (`198.51.100.64/26`, `2001:db8:abcd:12::/63`). Ein
  Client, den ein Dual-Stack-Server als `::ffff:203.0.113.9` meldet, passt zu IPv4-Einträgen.
- Eine Beschriftung ist beliebiger Text ohne `;`, `##`, `*/` oder Zeilenumbruch (`label=` davor ist optional).
- `expires=` nimmt `YYYY-MM-DD`, `YYYY-MM-DDTHH:MM[:SS]`, mit `Z` oder einem Versatz; ohne einen die Zeitzone des
  Servers. Die Leiste schreibt `+1h`, `+30m`, `+2d`, `today` (23:59) und `tomorrow` als dieses Datum. Ein abgelaufener
  Eintrag passt nicht mehr und bleibt in der Liste, bis jemand ihn entfernt. Ein Ablauf, der sich nicht lesen lässt,
  passt nie.

`eZDebug::isAllowedByCurrentIP()` fragt `expDebugBarIPList::match()`, wann immer die Klasse ladbar ist, sodass der Test
der Leiste und die echte Prüfung derselbe Code sind, und hält den Eintrag, der gepasst hat, in
`$GLOBALS['eZDebugIPMatch']` für die Leiste fest. Ein Prozess, der die Klasse nicht laden kann (ein Velocity-Worker,
der gestartet wurde, bevor die Klasse existierte), nimmt den Adressteil jeder Zeile und lässt abgelaufene Einträge
aus, sodass Beschriftungen und Abläufe die alte Prüfung nie brechen.

Ein Verhalten hat sich mit der Engine geändert: Die alte Prüfung ließ **jeden** IPv6-Client herein, sobald die Liste
irgendeine einfache IPv6-Adresse enthielt (sie verglich den Eintrag mit sich selbst). Ein einfacher IPv6-Eintrag
bedeutet jetzt genau diese eine Adresse.

`expdebugbar::iptest` zeigt für eine Adresse (Standard: die der Anfrage), ob die Liste sie hereinlässt, welcher Eintrag
passte, jeden geparsten Eintrag, die Adresse, wie der Server sie sieht (`REMOTE_ADDR`) und wie der Kernel sie nimmt
(`eZSys::clientIP()`, das `ClientIpByCustomHTTPHeader` wie X-Forwarded-For nur liest, wenn diese Einstellung gesetzt
ist), die vorzuschlagenden Einträge (`/32` und `/24`, `/128` und `/64`) und Warnungen: `invalid`, `expired`, `open`
(ein Eintrag `/0`), `empty` (kein aktiver Eintrag) und `lockout` (die Liste enthält die Adresse nicht).

## Das Schreibprotokoll und das Rückgängigmachen

Jeder Schreibvorgang ist eine JSON-Zeile in `var/<site>/log/debugbar.log` (`LogFile` in debugbar.ini): ID, Zeit,
Benutzer-ID, Anmeldename, Adresse, op, Einstellung, Datei, Block, Variable, Geltungsbereich, Pfad, der geschriebene
Wert, der Wert in dieser Datei vorher und nachher, der wirksame Wert vorher und nachher, die Sicherung, was der
Schreibvorgang angelegt hat (Datei, Block, Variable) und der sha1 der Datei vorher und nachher. Auch das Leeren von
Caches aus der Leiste wird protokolliert (op `cache`). Ein Protokoll, das die Leiste anlegt, erhält Besitzer und Gruppe
des Log-Verzeichnisses und den Modus 0640.

`undo( <entry id> )` wendet über den Editor das Gegenteil an: den alten Wert erneut gesetzt, die Variable erneut
entfernt, eine Liste in ihrer alten Reihenfolge zurückgegeben, einen hinzugefügten Wert entfernt und einen Block, den
der Schreibvorgang angelegt hat, entfernt, wenn er leer ist. Die Antwort sagt, ob die Datei jetzt Byte für Byte ihrer
Sicherung entspricht (`byte_identical`), was der Fall ist, wann immer die späteren Schreibvorgänge zuerst rückgängig
gemacht wurden. Ein Rückgängigmachen wird abgelehnt, wenn die Einstellung in dieser Datei nicht mehr das ist, was der
Schreibvorgang hinterlassen hat (ein anderer Schreibvorgang hat sie geändert; `force=1` setzt sie trotzdem zurück),
wenn der Eintrag bereits rückgängig gemacht wurde oder wenn er ein Geheimnis geändert hat (das Protokoll enthält keine
geheimen Werte). Das Rückgängigmachen wird mit `undoes` protokolliert, das den Eintrag nennt. `undoGroup( <group> )`
macht eine Voreinstellung rückgängig, den neuesten Schreibvorgang zuerst.

## Die Übersicht

`expDebugBarSummary::collect()` liefert, was eZDebug gemessen hat: die Zeit der Seite, die SQL-Anweisungen und ihre
Zeit (die Akkumulatoren `*_query` der Datenbanktreiber oder der Zähler von eZDB), den höchsten Speicherverbrauch und
das Limit, die verwendeten Templates, Meldungen pro Stufe, eingebundene Dateien, die Akkumulatoren und die
Messpunkte, wer Debug erhält und den IP-Eintrag, der passte, die Engine (Velocity oder die SAPI), den Siteaccess und den
SQL-Abfrage-Cache der Anfrage (`querycache`: `eZDBQueryCache::debugSnapshot()`, wobei die veralteten Anweisungen durch
ihre Anzahl ersetzt sind, `stale_count`; null, solange der Cache aus ist). Jede Zahl hat eine Stufe, `ok`, `warn` oder
`high`, gegenüber `Thresholds[]` in debugbar.ini (`time_ms`, `sql_count`, `sql_ms`, `memory_mb`, `templates`, jeweils
`<warn>;<high>`; `opcache_memory` und `apcu_memory` sind der belegte Speicher in Prozent, `opcache` und `apcu` in der
Übersicht, null, solange sie aus sind, oder für einen Benutzer ohne `setup/setup`; `querycache_hit_rate` ist eine
Trefferquote in Prozent und hebt die Stufe **unterhalb** ihrer Werte an, siehe
`expDebugBarReport::queryCacheLevel()`, die eine Stelle dieser Regeln: `expDebugBarSummary::queryCache()` und der
eigene Rückfall der Leiste bauen die Zahlen beide mit `expDebugBarReport::queryCacheFigures()`).
`expDebugBarSummary::json()` ist dasselbe als JSON, das innerhalb eines `<script>`-Elements sicher ist.

## Server-Funktionen

`/ezjscore/call/expdebugbar::<function>` (mit `?ContentType=json`). ezjscore antwortet mit
`{"error_text": "", "content": ...}`; eine Ablehnung steht in `error_text`.

| Funktion | Anfrage | Braucht |
|---|---|---|
| `settings[::<siteaccess>]` | GET | setup/setup (nichts über die Konfiguration geht an jemand anderen) |
| `set` | POST `setting`, `op`, `value`, `scope` [`siteaccess`, `confirm`, `dry_run`] | setup/setup |
| `undo` | POST `entry` oder `group` [`force`] | setup/setup |
| `preset` | POST `preset` + `scope` (anwenden), `save` [+ `values` als JSON oder `snapshot=1`] oder `delete` | setup/setup |
| `cache` | GET `action=list`; POST `action=clear`, `by=all\|tag\|id\|node\|velocity\|opcache\|querycache_counters`, `names`, `node_id` | setup/managecache (auflisten und leeren) |
| `iptest` | `address`, `list` (JSON-Array) | setup/setup |
| `log[::<limit>]` | GET | setup/setup |
| `summary` | GET | Debug-Ausgabe für die Anfrage (der Bericht selbst; ein Besucher erhält darin keinen Eintrag der IP-Liste) |
| `panelheight` | POST `height` (Pixel, `0` für den Standard) | ein angemeldeter Benutzer (nie ein anonymer Besucher) mit Debug-Ausgabe für die Anfrage: speichert die Benutzereinstellung `expdebugbar_panel_height` (siehe [Die Höhe des Panels](#die-höhe-des-panels)) |

Jeder POST muss das Formular-Token mitbringen (`ezxform_token` oder den Header `X-CSRF-Token`); die Antwort von
`settings` enthält es. Wer nur den Debug-Bericht sieht, erhält die Einstellungen ohne die IP- und Benutzerlisten, das
Protokoll oder die Liste der Caches: Wer Debug-Ausgabe erhält und wer was geändert hat, ist für diejenigen, die es
ändern dürfen. Geheime Werte werden wie in exp:ini maskiert.

`cache` mit `by=node` leert den Ansichts-Cache eines Knotens, der Seite, auf der die Leiste ist
(`eZContentCacheManager::clearNodeViewCacheArray()`); `velocity` leert den Antwort-Cache von Velocity; `opcache` setzt
den OPcache des antwortenden Prozesses zurück; `querycache_counters` setzt die Zähler des Abfrage-Caches dieses Servers
auf null (die gecachten Ergebnisse selbst sind die Cache-ID `querycache`). Die Liste gibt jeden Cache mit der Art, wie
er geleert wird (`expCacheManager`), die Tags, wann jeder zuletzt aus der Leiste geleert wurde, den Cache-Status von
Velocity, den Zustand von OPcache und den SQL-Abfrage-Cache (`querycache`: `eZDBQueryCache::inventory()`,
Einstellungen, die Zähler dieses Servers, der APCu-Speicher, die Einträge gruppiert nach den Tabellen, die sie lesen,
mit Zeilen, Größe und Treffern, und die größten Einträge; ein Eintrag trägt keinen Anweisungstext, nur den Anfang
seines Schlüssels). Das Inventar liest die Einträge des Caches in APCu mit ihren Zeilen (höchstens 5.000 und höchstens
eine Sekunde lang; es sagt, wenn es vorzeitig aufgehört hat), daher läuft es nur, wenn die Registerkarte Cache geöffnet
wird. Die Übersicht (`expdebugbar::summary`, das JSON der Seite) trägt nur die Zahl der Schreibvorgänge, die alles
veraltet gemacht haben (`stale_count`); die Anweisungen stehen im Bericht der Registerkarte SQL. `inventory()` lässt
den Pfad der Zustandsdatei selbst aus. Nach einem Leeren oder einem Zurücksetzen der Zähler wird der Abschnitt erneut
gelesen. Der Abfrage-Cache wird nicht mehr ein zweites Mal unter „Nach ID“ aufgeführt.

Beispiel (ersetzen Sie die Hostnamen und YOUR_USER:YOUR_PASSWORD durch Ihre eigenen; der zweite Aufruf erfolgt ohne
Anmeldung):

```
$ curl -s -u YOUR_USER:YOUR_PASSWORD 'https://admin.example.com/ezjscore/call/expdebugbar::iptest?ContentType=json'
{"error_text":"","content":{"address":"...","valid":true,"family":4,"remote_addr":"...","client_ip":"...",
 "trusts_proxy":false,"proxy_header":null,"suggest":{"self":".../32","network":".../24","family":4},
 "debug_by_ip":false,"allowed":false,"matched":null,...}}
$ curl -s 'https://www.example.com/ezjscore/call/expdebugbar::log?ContentType=json'
{"error_text":"This needs the policy setup\/setup or setup\/managecache","content":""}
```

## RAD

Die Übersicht der Erweiterungspunkte (`setup/radsurvey`) liest zwei Registries aus debugbar.ini: **Debug bar settings**
(jedes `[Setting_<id>]`, auf einen gültigen `Type` geprüft) und **Debug bar presets** (jedes `[Preset_<id>]`, auf einen
Namen geprüft), Dateien des Kernels und von Erweiterungen gleichermaßen. Ihre Einträge nennen keine Klasse, daher werden
sie einmal zur Gesamtzahl addiert. Die Übersicht trägt auch die eigenen Zahlen der Leiste, die nicht noch einmal addiert
werden: `debugbar_settings`, `debugbar_registered`, `debugbar_extension_registered`, `debugbar_discovered`,
`debugbar_presets`, `debugbar_problems` (`expRADSurvey::debugBar()`, also `expDebugBarRegistry::survey()`).

## Tests

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/debugbar/
```

| Test | Deckt ab |
|---|---|
| `expDebugBarReportPanelHeightTest` (`tests/tests/lib/ezutils/`) | die Höhe des Panels: der Name der Benutzereinstellung und die Grenzen, `panelHeight()` bei ganzen Zahlen, Text, Brüchen, negativen Werten und Werten außerhalb des Bereichs, `savedPanelHeight()` mit einem übergebenen Leser, der für einen anonymen Besucher nie gefragt wird, die Data-Attribute am gerenderten Panel (kein `style`-Attribut), die Styles und Tasten des Griffs, die Prüfungen der Server-Funktion. Ohne Datenbank |
| `expDebugBarIPListTest` | Adressen und CIDR beider Familien, IPv4-gemappte Clients, Einträge mit Beschriftungen und Abläufen, das Schreiben von Einträgen, der Abgleich, die Warnungen, die Vorschläge. Braucht keinen Kernel |
| `expDebugBarSettingsTest` | die Registry und das Auffinden, die wirksamen Werte gegenüber `expIniLocator::where()`, Schreibvorgänge jedes Typs und jeder op mit Protokoll und Rückgängigmachen, Probeläufe, Ablehnungen, ein Siteaccess als Geltungsbereich, Voreinstellungen als Gruppe, die Prüfungen auf Aussperren und Öffnen, die IP-Prüfung von eZDebug und ihr Rückfall, die Übersicht, die Server-Funktionen (Registrierung, Antworten, POST, Token und Richtlinie, was ein Besucher ohne Richtlinie sieht) |
| `RadSurveyDebugBarTest` | die zwei Registries, die Gesamtzahl, die Zahlen der Leiste, die neuen Problemarten |

`expDebugBarSettingsTest` startet den Kernel einmal im Siteaccess admin mit der eigenen Datenbank der Installation, wie
es die Tests der Spalten der Unterelemente tun; es wird keine Testdatenbank angelegt. Seine Schreibvorgänge gehen in
einen Probeblock (`[ExpDebugBarTestProbe]` von site.ini) in `settings/override` und `settings/siteaccess/admin`, wobei
der INI-Cache unberührt bleibt, und jeder Test macht seine Schreibvorgänge rückgängig und prüft die Dateien Byte für
Byte gegen ihren Zustand vor dem Test (und stellt sie wieder her, wenn sie abweichen). Sein Protokoll ist eine eigene
Datei unter `var/tmp/debugbar-test/`.

## Die Leiste

| Teil | Wo |
|---|---|
| Markup des Berichts, Übersicht, Datenblock | `expDebugBarReport` (`lib/ezutils/classes/expdebugbarreport.php`), von `eZDebug::printReportInternal()` für HTML-Berichte aufgerufen |
| Öffnen und Schließen, „Beim Neuladen offen lassen“, gespeicherte Höhe des Panels, minimierte Leiste (läuft sofort, vor dem ersten Zeichnen) | `design/standard/javascript/expdebugbar-head.js` |
| Registerkarten, Filter, Einstellungen, IP-Liste, Voreinstellungen, Cache, Änderungsprotokoll | `design/standard/javascript/expdebugbar.js` |
| Styles (hell und dunkel) | `design/standard/stylesheets/expdebugbar.css` |
| Theme Werkzeugleiste (`Theme=toolbar`) | `design/standard/stylesheets/expdebugbar-toolbar.css`, danach verlinkt |
| Wörter | Kontext `design/standard/debugbar` in `share/translations/*/translation.ts` (eng-US, ger-DE) |

Der Bericht verlinkt das Skript und das Stylesheet selbst, sodass jedes Design die Leiste erhält: admin, admin2,
admin3, admin4 und die öffentlichen Designs, mit oder ohne `debug.css`. Die Links sind einfache Pfade, ohne `?v=` oder
eine andere Versionsabfrage (Regel des Inhabers); nach einer Änderung an einer der Dateien wird ein Browser, der noch
die alte zeigt, von Hand neu geladen. Textberichte (`Debug=inline` auf der Kommandozeile) und das Popup-Fenster sind
unverändert. Ohne `expdebugbarreport.php` (ein älteres Engine-Archiv) gibt `eZDebug` den klassischen Bericht aus.

### Kein Inline-CSS

Keiner der beiden Berichte gibt ein `<style>`-Element oder ein `style=""`-Attribut aus (AGENTS.md, „Admin design and
template styling rules“), und kein Inline-Skript: Der Teil, der vor dem ersten Zeichnen laufen muss, ist
`expdebugbar-head.js`, ohne `defer` verlinkt, sodass eine Content-Security-Policy mit `script-src 'self'` hält. Das
Markup der Leiste ist beim ersten Zeichnen vollständig (das Symbol zum Minimieren, der Link, die Chips, „Beim Neuladen
offen lassen“ und die versteckte Registerkarte, die die Leiste wiederherstellt, werden alle von
`expDebugBarReport::barHtml()` ausgegeben), die Leiste behält mindestens die Höhe einer Zeile, und ihre Seiten sind
logisch (`margin-inline-*`, `inset-inline-*`), sodass sie nach dem Laden nicht wächst oder sich verschiebt und ihr Link
in einer Oberfläche von rechts nach links am Ende der Zeile bleibt (#571).

- **Die Leiste**: Jede Regel, einschließlich der angehefteten Leiste und des Panels darüber, steht in `expdebugbar.css`
  und nimmt ihre Farben aus den Tokens `--xd-*` der Leiste, jedes mit einem Wert für `prefers-color-scheme: dark` und
  für `html[data-a4-theme="dark"]` von admin4. Der Bericht verlinkt das Stylesheet *vor* der Leiste, sodass der
  Browser die Regeln hat, bevor er die Leiste zeichnet; eine Regel vor dem Zeichnen wird nicht gebraucht. Die zwei
  Höhen, die das Skript zur Laufzeit misst oder wiederherstellt, sind Custom Properties am Wurzelelement
  (`--exp-debug-bar-h`, `--exp-debug-panel-h`), keine Style-Attribute im Markup.
- **Der klassische Bericht**: Seine Regeln stehen in `debug.css`, das der Bericht selbst verlinkt (oder das Design
  lädt, mit `eZDebug::setUseExternalCSS( true )`), beschränkt auf `#debug:not([data-exp-debug-bar])` mit einer kleinen
  Kopie der Tokens. Seine Leiste folgt dem Farbschema; sein Panel behält in beiden die hellen Zeilen der klassischen
  Tabellenregeln.
- **Die Tabellen beider**: Die Zahlen der Messpunkte und der Zeitakkumulatoren sind `td.num` (rechtsbündig in gleich
  breiten Ziffern), die Zeit einer Meldungsüberschrift `td.debugtime`, die Überschrift `td.debuglabel`, der Name einer
  Akkumulatorgruppe `td.groupname` und die Zählzeilen der eingebundenen Dateien und der Template-Nutzung `tr.summary`;
  kein `<b>` oder `<i>`.
- `eZDebug::$UseDebugBar` (Standard true) auf false gesetzt gibt den klassischen HTML-Bericht aus; der Test unten
  verwendet das.

`expDebugReportNoInlineCssTest` (`tests/tests/lib/ezutils/`, ohne Datenbank) rendert die Leiste und den klassischen
Bericht mit Meldungen, Messpunkten und Akkumulatoren und schlägt bei jedem `style=""`-Attribut, jedem
`<style>`-Element und jeder Asset-URL mit einer Abfrage `?v=` fehl; er prüft außerdem den Quelltext des Berichts zum
Abfrage-Cache, den Textbericht und dass jedes Token `--xd-*` seine dunklen Werte hat.

### Die angeheftete Leiste

Die Leiste bleibt am unteren Rand des Fensters, wohin die Seite auch gescrollt ist; ein Klick auf „Exp Debug“ öffnet
das Panel darüber, das selbst scrollt. Keine Seite muss je gescrollt werden, um sie zu verwenden. „Beim Neuladen offen
lassen“ merkt sich das offene Panel im Browser (`localStorage`-Schlüssel `exp-debug-keep`, `exp-debug-open`) und die
letzte Registerkarte (`exp-debug-tab`). Escape schließt das Panel.

### Themes

`debugbar.ini [DebugBarSettings] Theme` legt fest, wie die angeheftete Leiste aussieht; das Panel darüber ist in beiden
gleich. Es ist auch eine Einstellung der Leiste (Registerkarte Einstellungen, Debug-Ausgabe, „Bar theme“).

| Wert | Die Leiste |
|---|---|
| `default` | Die Übersicht als helle Chips (dunkel in einem dunklen Farbschema), die Details eines Chips in seinem Tooltip (`title`). Auch jeder Wert, der nicht `toolbar` ist. |
| `toolbar` | Eine dunkle, kompakte Werkzeugleiste in beiden Farbschemata, nach Art der Web-Debug-Toolbar von Symfony: jeder Block mit einem Icon, der erste Block die Anfrage (HTTP-Status und Modul/View, grün, ab Status 400 gelb, ab 500 rot; er öffnet die Registerkarte Server), gelbe und rote Blöcke mit ihrer Farbe gefüllt und die Details eines Blocks in einem Popup darüber, solange der Zeiger oder der Tastaturfokus darauf ist. „Beim Neuladen offen lassen“ zeigt sein Kästchen allein, die Wörter als Tooltip. „Exp Debug“, der Schalter des Panels, ist ein hellerer eigener Block am rechten Ende, orange gefüllt, solange das Panel offen ist; sein Pfeil zeigt nach oben und, solange es offen ist, nach unten. |

Im Theme Werkzeugleiste trägt ein Chip seinen Text in `.exp-debug-chip-text` und sein Popup in `.exp-debug-chip-info`
(`aria-hidden`, das `aria-label` des Chips sagt dasselbe) und hat kein `title`. Das Popup führt die Zeilen auf, die ein
Chip liefert (`'rows'`, Beschriftung => Wert: Zeit der Seite und der Abfragen, Abfragen und ihr Anteil an der Zeit der
Seite, höchster Speicherverbrauch und das Speicherlimit, Templates, die Zahl der Fehler, Warnungen, Hinweise und
Veraltungswarnungen, Status, Modul, View, Siteaccess und Knoten der Anfrage), oder allein den Titel des Chips. Ein Chip
des Panels einer Erweiterung erhält ein allgemeines Icon; die Icons sind Bootstrap Icons
(`share/icons/bootstrap-icons`, MIT), als Masken in das Stylesheet eingebettet, sodass keine weitere Datei geladen wird.
Der Status ist `http_response_code()`, wenn der Bericht gerendert wird; wo PHP ihn nicht kennt (die Kommandozeile, ein
persistenter Worker), zeigt der Block nur Modul/View.

Das Panel im Theme Werkzeugleiste folgt ab einer Fensterbreite von 761 px dem Profiler von Symfony: Die Registerkarten
sind ein dunkles Menü an der linken Seite (je ein Icon, die Anzahl rechts, die offene Registerkarte markiert, das
Filterfeld darüber; die Pfeiltasten nach oben und unten wechseln zwischen ihnen), die offene Registerkarte hat einen
großen Titel, und die Registerkarten, deren Chip Zeilen hat (Nachrichten, Zeiten, SQL, Templates, Speicher, Server),
beginnen mit diesen Zahlen als Kennzahlen in großer Schrift (`expDebugBarReport::metricsHtml()`, ein
`<dl class="exp-debug-metrics">`). Tabellen haben offene Zeilen mit einer Linie dazwischen; eine Meldung hat einen
Balken in der Farbe ihrer Stufe. Die Farben des Panels bleiben die aus `expdebugbar.css`, hell und dunkel. Schmalere
Fenster behalten die Reihe der Registerkarten über dem Panel.

### Die Höhe des Panels

Das offene Panel ist voll breit, daher ändert sich nur seine Höhe. Der Streifen an seiner oberen Kante (die Kante zur
Seite hin; das Panel öffnet sich von der Leiste nach oben) ist ein Griff: Ziehen Sie ihn mit der Maus, einem Stift oder
einem Finger, oder fokussieren Sie ihn (er ist ein `role="separator"` mit `aria-valuenow`, `aria-valuemin` und
`aria-valuemax`) und verwenden Sie die Tasten:

| Taste | Tut |
|---|---|
| Pfeil nach oben / unten | 16 px höher / niedriger |
| Umschalt + Pfeil nach oben / unten | 64 px höher / niedriger |
| Pos1 / Ende | die niedrigste / die höchste Höhe, die das Fenster erlaubt |
| Doppelklick auf den Griff | zurück zur Standardhöhe (60 % des Fensters) |

Die Höhe bleibt zwischen 120 px und der Höhe des Fensters abzüglich der angehefteten Leiste, auch wenn das Fenster seine
Größe ändert (eine Höhe aus einem höheren Fenster kommt zurück, wenn das Fenster wieder hoch genug ist). Das Stylesheet
hält dieselbe Grenze (`height: min(var(--exp-debug-panel-h, 60vh), calc(100vh - var(--exp-debug-bar-h)))`), sodass eine
gespeicherte Höhe den oberen Rand des Panels nie aus dem Fenster schiebt.

Die Höhe wird einmal am Ende eines Ziehens gespeichert oder 0,6 s nach dem letzten Tastendruck:

- **Angemeldet**: in der Benutzereinstellung `expdebugbar_panel_height` (eZPreferences, über
  `expdebugbar::panelheight`), sodass sie dem Benutzer in jeden Browser folgt. `0` bedeutet die Standardhöhe. Der
  Bericht liest sie zurück (`expDebugBarReport::savedPanelHeight()`) und gibt sie am Panel als
  `data-exp-debug-panel-height` aus; das erste Skript des Berichts (`expdebugbar-head.js`) setzt `--exp-debug-panel-h`
  daraus, bevor sich das Panel öffnet, sodass es ohne Sprung in dieser Höhe öffnet.
- **Anonym** (die Leiste über die IP angezeigt): Es wird keine Benutzereinstellung gelesen oder gespeichert und keine
  Anfrage gesendet, da eZPreferences die Werte eines anonymen Benutzers in der Sitzung halten und die Debug-Leiste nie
  eine Sitzung starten darf (`savedPanelHeight()` gibt 0 zurück, ohne zu fragen, und `expdebugbar::panelheight` lehnt
  einen anonymen Besucher vor der Prüfung des Tokens ab). Die Höhe wird stattdessen im Browser gehalten:
  `localStorage`-Schlüssel `exp-debug-panel-height`, jeder Zugriff in `try`/`catch` (gesperrter Speicher bedeutet die
  Standardhöhe), von `expdebugbar-head.js` vor dem Öffnen des Panels zurückgelesen und zwischen 120 und 4000 px
  gehalten; alles außer einer ganzen Zahl von Pixeln wird ignoriert. Sie bleibt nur in diesem Browser; ein anderer
  Browser oder ein privates Fenster beginnt mit dem Standard. In einem Siteaccess, der eine Anmeldung verlangt (die
  Administration), braucht `ezjscore/call` seit 6.0.15 einen angemeldeten Benutzer, daher zeigen auf einer anonymen
  Admin-Seite (der Anmeldeseite) die Panels, die den Server fragen (Cache, Protokoll, Übersicht), „Could not load“; die
  Höhe des Panels funktioniert wie bisher, da sie keine Anfrage sendet.

Die Benutzereinstellung eines angemeldeten Benutzers ist maßgeblich: localStorage wird für einen angemeldeten Benutzer
nicht beachtet. Das Panel sagt in `data-exp-debug-panel-store` (`preference` oder `browser`), was gilt.

Eine Seite, die den Debug-Bericht trägt, wird nie vom Seiten-Cache behalten (`X-Exp-Cache: BYPASS (debug report)`,
siehe [What is never cached](../../../eng-us/bc/6.0/httpcache.md#what-is-never-cached)): Der Bericht und die
gespeicherte Höhe werden für jede Anfrage gerendert. Vor dem 2026-10-14 kam die Startseite eines angemeldeten Benutzers
mit dem Bericht einer früheren Anfrage aus diesem Cache, sodass ein Neuladen die Höhe von vor der letzten
Größenänderung zeigte.

Ein gespeicherter Wert ist eine ganze Zahl von Pixeln: `expDebugBarReport::panelHeight()` macht aus allem anderen
(Text, ein Bruch, eine negative Zahl, eine Einheit) „keine Höhe“ (die Server-Funktion lehnt es ab, der Bericht
verwendet den Standard) und hält eine Zahl zwischen 120 und 4000 (`PANEL_HEIGHT_MIN`, `PANEL_HEIGHT_MAX`).

Die Übersicht in der Leiste zeigt die Anfrage auf einen Blick; jeder Wert öffnet seine Registerkarte:

| Wert | Registerkarte | Gelb / rot ab |
|---|---|---|
| Zeit der Seite | Zeiten | `[DebugBarSettings] Thresholds[time_ms]` (Standard 1 s / 3 s ohne die Serverseite) |
| Anzahl / Zeit der SQL-Anweisungen | SQL | `Thresholds[sql_count]`, `Thresholds[sql_ms]` |
| höchster Speicherverbrauch | Speicher | `Thresholds[memory_mb]` (sonst 50 % / 80 % von `memory_limit`) |
| verwendete Templates (nur mit `ShowUsedTemplates`) | Templates | `Thresholds[templates]` |
| SQL-Abfrage-Cache (`QC 83 % · -186 ms (186 %)`: Trefferquote, eingesparte Datenbankzeit und diese Zeit in Prozent der Zeit der Seite; nur solange der Cache eingeschaltet ist) | SQL | `Thresholds[querycache_hit_rate]` (gelb unter 50 %, rot unter 10 %; gelb, wenn ein Schreibvorgang alles veraltet gemacht hat, rot in `shared` ohne APCu) |
| Warnungen, Fehler | Nachrichten (auf diese Stufe gefiltert) | jede Warnung ist gelb, jeder Fehler rot |
| Veraltungswarnungen (`3 depr`) | Nachrichten | jede Veraltungswarnung ist gelb |
| belegter OPcache-Speicher (`OPc 66 %`, der Titel mit MB, Trefferquote, Skripten und Neustarts; nur solange OPcache für den Prozess eingeschaltet ist) | Server | `Thresholds[opcache_memory]` (gelb ab 80 %, rot ab 95 %) |
| belegter APCu-Speicher (`APCu 25 %`, der Titel mit MB, Einträgen, Trefferquote und Verdrängungen; nur solange APCu für den Prozess eingeschaltet ist) | Server | `Thresholds[apcu_memory]` (gelb ab 75 %, rot ab 90 %) |
| Versionen (`PHP 8.4.19 \| Exp 6.0.15`, Exponential gekürzt zu Exp; der letzte Chip, am rechten Ende) | Server | nie eingefärbt |

Die Chips für OPcache, APCu und Versionen erscheinen nur für einen Benutzer, der die privilegierten Zeilen der
Registerkarte Server sehen darf (die Richtlinien `setup/setup` oder `setup/managecache`): Die Leiste erscheint auch für
anonyme Besucher, wenn `DebugByIP` passt, und der Zustand des Servers ist nicht für sie bestimmt. APCu ist ein Speicher
für jede Website des PHP-Pools; bei 75 % Belegung hat der SQL-Abfrage-Cache bereits aufgehört, Ergebnisse hinzuzufügen
(`querycache.ini ApcuReserve=25`). Die Registerkarte Server führt APCu unter OPcache auf. Ein Prozess ohne sie (die
Kommandozeile, wo `apc.enable_cli` meist aus ist) zeigt für sie keinen Chip. Die drei sind die letzten Chips, am rechten
Ende der Übersicht.

PHP-Veraltungswarnungen werden in der Leiste nur gezählt: Mit `site.ini [DebugSettings] DeprecatedLog=enabled` (dem
Standard) wird jede verschiedene Meldung einmal pro Anfrage nach `var/log/deprecated.log` geschrieben, und keine wird im
Speicher gehalten oder in der Registerkarte Nachrichten aufgeführt. Sie gehören weiterhin zur Zahl der Warnungen der
Seite (der Kasten „Some general warnings occurred“ von `DisplayDebugWarnings`). Die Registerkarte Einstellungen schaltet
`DeprecatedLog` („Deprecations to deprecated.log“); ausgeschaltet sind Veraltungswarnungen wieder Warnungen, einzeln
aufgeführt. Siehe
[Veraltungswarnungen und Größen der Logdateien](../../../eng-us/guides/operating-a-site.md#php-deprecations-and-log-file-sizes).

Die Stufen kommen aus `expDebugBarSummary`, wenn es geladen ist, sodass die Schwellen in `debugbar.ini` gelten; sonst
die eigenen Standardwerte des Berichts.

### Die Registerkarten

| Registerkarte | Zeigt |
|---|---|
| Nachrichten | die Hinweise, Warnungen, Fehler, Debug- und Zeitmeldungen, mit Schaltflächen pro Stufe; die SQL-Anweisungen sind in die Registerkarte SQL verschoben |
| Einstellungen | die Oberfläche für Einstellungen unten und unter „Klassische Bedienelemente“ die klassische Werkzeugleiste (`design:setup/debug_toolbar.tpl` oder das Override einer Website davon, wie bisher gerendert) |
| Cache | die Cache-Steuerung unten; ohne die Server-Funktionen ein Formular, das diese Seite, Inhalte, Templates, INI oder alle Caches über `setup/cachetoolbar` leert |
| Zeiten | die Messpunkte und die Zeitakkumulatoren, wie bisher (`#timingpoints`, `#timeaccumulators`) |
| SQL | Zahl der Abfragen, Zeit, Anteil an der Seite, Durchschnitt, die Akkumulatoren der Datenbank und die Anweisungen (`SQLOutput`) mit „Langsamste zuerst“ |
| Templates | die verwendeten Templates (`ShowUsedTemplates`) und der Bericht des CSS/JS-Packers, oder wie man sie einschaltet |
| Eingebundene Dateien | die eingebundenen PHP-Dateien (`DisplayIncludedFiles`), oder ihre Zahl und wie man sie auflistet |
| Speicher | die klassische Tabelle „Main resources“, Speicher jetzt, Höchstwert, Limit und Anteil und die fünf Schritte, die am meisten brauchten |
| Rendering | wie die Seite gerendert wurde: der Controller, der Ansichts-Cache von Inhalten und sein Schlüssel, die kompilierten Templates (siehe [unten](#rendering-controller-ansichts-cache-von-inhalten-und-kompilierte-templates)); nur für `setup/setup` |
| (Erweiterungen) | eine Registerkarte pro Panel, das eine Erweiterung registriert, nach Speicher (siehe [Panels von Erweiterungen](#panels-von-erweiterungen)) |
| Server | wo die Anfrage einging und welcher Prozess den Bericht gerendert hat (Exponential Velocity, PHP-FPM, mod_php, die Kommandozeile ...), in einem Satz (siehe [unten](#server-wo-die-anfrage-einging-und-was-sie-gerendert-hat)), die Fakten dahinter, der Status von OPcache und der Antwort-Cache von Velocity; bis zum 16. Oktober 2026 hieß sie Velocity, ihre Abschnitts-ID ist weiterhin `exp-debug-panel-velocity` |
| Sonstiges | Berichte, die Erweiterungen mit `eZDebug::appendBottomReport()` anhängen und die zu keiner Registerkarte gehören (nur wenn es welche gibt) |

Jeder Abschnitt behält seine ID, sodass für den klassischen Bericht geschriebene Skripte und Stylesheets ihn weiterhin
finden.

Das Filterfeld über den Registerkarten filtert die Zeilen jeder Registerkarte zugleich (Meldungen, Zeitzeilen,
Akkumulatoren, Anweisungen, Dateien, Einstellungen, Caches); eine Registerkarte ohne Treffer wird abgeblendet und sagt
das. Escape im Feld leert es.

### Einstellungen

Wird aus `expdebugbar::settings` gezeichnet, wenn die Registerkarte zum ersten Mal geöffnet wird. Pro Gruppe
(Debug-Ausgabe, wer Debug erhält, Templates, Datenbank, ... und die Schalter der Erweiterungen) zeigt jede Einstellung
ihren Namen, Datei, Block und Variable, ihre Hilfe, ein Bedienelement für ihren Typ (Schalter, Werteliste, Text, ein
Eintrag pro Zeile), den wirksamen Wert und woher er kommt (Standard, globales Override, Siteaccess, Erweiterung; die
Datei steht im Tooltip) und eine Auswahl „Schreiben nach“ mit jedem beschreibbaren Geltungsbereich und dem Wert, den
die Datei dieses Geltungsbereichs liefert. Anwenden schreibt den Wert in den gewählten Geltungsbereich, Zurücksetzen
entfernt die Variable aus diesem Geltungsbereich. Ein Schreibvorgang, der Sie aussperren oder Debug für alle öffnen
würde, fragt zuerst.

„siteaccess“ oben ändert, wessen wirksame Werte angezeigt werden. Voreinstellungen (Template work, SQL tuning,
Everything, Off und die eigenen des Benutzers) werden auf den daneben gewählten Geltungsbereich angewendet und lassen
sich sofort zurücknehmen; „Aktuelle Werte als Voreinstellung speichern“ speichert die wirksamen Werte unter einem
Namen.

Das Änderungsprotokoll führt die letzten Schreibvorgänge auf (wer, welche Einstellung, alter und neuer Wert,
Geltungsbereich), mit Rückgängig; ein Rückgängigmachen, das die Datei seitdem geändert findet, fragt, bevor es erzwingt.
Ohne setup/setup enthält die Registerkarte Einstellungen nur den Hinweis „Melden Sie sich mit Setup-Zugriff an, um die
Debug-Einstellungen zu ändern“ (keine Werte, keine IP- oder Benutzerlisten, kein Protokoll, keine Dateipfade); ohne
setup/managecache enthält die Registerkarte Cache nur den Hinweis „Melden Sie sich mit Cache-Zugriff an, um die Caches
zu verwalten“.

### Wer Debug erhält: die IP-Liste und die Benutzerliste

Die IP-Liste (`DebugIPList[]`) zeigt die Adresse, die der Server für diese Anfrage sieht (und REMOTE_ADDR, wenn die
Website einem Proxy-Header vertraut), den Eintrag, zu dem diese Anfrage passte, und jeden Eintrag mit Beschriftung,
Ablauf und ob er passte. „Meine IPv4-Adresse hinzufügen“ / „Meine IPv6-Adresse hinzufügen“ und „Mein /24 hinzufügen“ /
„Mein /64 hinzufügen“ fügen die eigene Adresse oder das eigene Netz der Anfrage hinzu; das CIDR-Feld nimmt IPv4- und
IPv6-Adressen und -Bereiche an und sagt sofort, was falsch ist (Präfix über 32 oder 128, fehlerhafte Adresse, bereits
aufgeführt); jeder Eintrag erhält eine Beschriftung und einen Ablauf (1 Stunde, heute, bis zum Entfernen). „Eine
Adresse prüfen“ fragt den Server, ob eine Adresse passen würde. Warnungen sagen, wenn die Liste Sie aussperren würde,
leer ist, während „Debug by IP“ eingeschaltet ist, oder Debug für jede Adresse öffnet.

Die Benutzerliste (`DebugUserIDList[]`) nimmt eine Benutzer-ID pro Zeile an, „Mich hinzufügen“ fügt den angemeldeten
Benutzer hinzu.

### Cache

Wird aus `expdebugbar::cache` gezeichnet: „Nur diese Seite“ (der Ansichts-Cache des Knotens, den die Seite zeigt), der
Antwort-Cache von Velocity, OPcache des ausliefernden Prozesses, jeder Cache-Tag und jede Cache-ID mit Beschreibung und
dem Zeitpunkt des letzten Leerens, und alle Caches. Jedes Leeren sagt, was es getan hat.

### Rendering: Controller, Ansichts-Cache von Inhalten und kompilierte Templates

Die Registerkarte beantwortet „warum ist diese Seite langsam oder nicht gecacht“, ohne den Code zu lesen. Nur ein
Benutzer mit `setup/setup` sieht sie und ihre Chips, da der Cache-Schlüssel die IDs der Rollen und Einschränkungen des
Benutzers trägt. Jeder Teil beginnt mit einer kurzen Erklärung, was er zeigt. Sie hat fünf Teile:

- **Controller**: jede Modul-View, die lief, in der Reihenfolge (eine Fehlerseite oder eine Weiterleitung fügt eine
  hinzu), mit der View-Klasse, die sie ausführte (`Exponential\View\Kernel\...`, siehe
  [cli_cronjob_view_abstractions.md](cli-cronjob-und-view-abstraktionen.md)) und dem Skript; der
  Siteaccess, der Navigationsteil und, eingeklappt, die Reihenfolge der Designs (die erste Basis, die ein Template hat,
  gewinnt).
- **Template-Baum**, direkt nach dem Controller und aufgeklappt: jedes Template in der Reihenfolge seines Starts
  (`eZTemplate::fetch()` für das äußerste, `processURI()` für jedes `{include}`, `node_view_gui` und Ähnliches),
  eingerückt unter dem Template, das es eingebunden hat, mit einer Hilfslinie pro Ebene, und die Zeit, die es
  einschließlich dessen brauchte, was es eingebunden hat (nicht das Parsen oder Kompilieren des Templates selbst). Wo
  die Overrides eine andere Datei gewählt haben als den eigenen Platz des Templates in einem Design
  (`node/view/full.tpl` → `.../override/templates/full/folder.tpl`), folgt die Datei dem Namen in der Akzentfarbe; sonst
  ist die Datei der Tooltip. Ein ohne den Compiler ausgeführtes Template ist als *nicht kompiliert* gekennzeichnet.
  Templates einer View, die aus dem Ansichts-Cache gelesen wird, werden nicht gerendert und daher nicht aufgeführt.
  Höchstens 2000 Einträge, jedes Template gezählt; `debugbar.ini [DebugBarSettings] RenderTree=disabled` lässt den
  Baum weg.
- **Ansichtscache**, wenn eine Inhaltsansicht lief: eine Zeile, aufgebaut wie die Cache-Blöcke unten, mit Knoten und
  View-Modus, dem Zustand und seinem Grund, der Zeit für das Nachschlagen der Datei und für das Erzeugen der Ansicht,
  der Größe, dem Alter der Datei und, eingeklappt, dem Schlüssel Teil für Teil, wie er gehasht wurde, mit dem Hash und
  der Cache-Datei. Ein Teil, den ein Listener von `content/view/cachekeys` hinzugefügt oder geändert hat, ist als
  *Listener* gekennzeichnet.

| Zustand | Chip | Bedeutung |
|---|---|---|
| Aus dem Cache gelesen | View: hit | die Datei wurde verwendet; mit ihrem Alter und ihrer Größe (kein Alter und keine Größe bei Valkey) |
| Erzeugt und im Cache gespeichert | View: new | keine Datei, oder die alte wurde nicht verwendet; der Grund sagt, warum (abgelaufen durch `cache_ttl`, durch ein Template im Entwicklungsmodus, durch ein Leeren des Caches, `no_cache`) |
| Erzeugt, ohne sie im Cache zu speichern | View: new | die Cache-Datei sagte `no_cache` (eine View, die `no_cache` setzt, hinterlässt nur diesen Hinweis in ihrer Datei), oder das Ergebnis wird nicht behalten |
| Nicht verwendet | View: off | `ViewCaching` ausgeschaltet, der Aufrufer hat es ausgeschaltet (`$Params['ViewCache']`), der View-Modus steht nicht in `CachedViewModes`, oder `ViewCacheTweaks[<node>]` enthält `disabled` |

- **Cache-Blöcke**: jeder `{cache-block}` in der Reihenfolge, in der er nachgeschlagen wurde, mit Template und Zeile,
  dem Zustand (Treffer, erzeugt, nicht gefunden), der Zeit für das Nachschlagen und für das Erzeugen (die Zeit eines
  äußeren Blocks enthält die Blöcke darin), der Größe, dem Ablauf (`expiry`, `subtree_expiry`) und, eingeklappt, den
  Schlüsseln und der Cache-Datei. Höchstens 200 Zeilen; die Zeile über der Tabelle zählt jeden Block. Der Chip
  **CB hits/count** wird angehoben, solange ein Block nicht aus dem Cache gelesen wurde. Ein Template, das vor dieser
  Version kompiliert wurde, nennt kein Template und keine Erzeugungszeit: Leeren Sie den Template-Cache einmal
  (`php bin/php/ezcache.php --clear-tag=template`). Code, der `eZTemplateCacheBlock::retrieve()` selbst aufruft
  (`content/treemenu`), zeigt seine Schlüssel ohne Template.
- **Templates**: `ViewCaching`, `TemplateCompile`, `CompileExecution`, `TemplateCache` und `DevelopmentMode`, wie sie
  wirksam sind, wie viele Templates gerendert wurden, wie oft ein kompiliertes Template lief und wie viele in dieser
  Anfrage kompiliert wurden, mit ihren Dateien. Der Chip **Compiled N** wird angehoben, solange N nicht 0 ist: Direkt
  nach einem Leeren des Template-Caches wird jedes Template einmal kompiliert.

Werte, die länger als 200 Zeichen sind, werden gekürzt; der ganze Wert ist der Tooltip. Der Sammler ist
`expRenderStats` (`kernel/classes/debugbar/exprenderstats.php`). Der Kernel notiert darin, wo er entscheidet
(`eZModule::run()`, `ModuleView::main()`, die View content/view, `eZNodeviewfunctions`, `eZTemplateCompiler`,
`eZTemplateCacheBlock`, `eZTemplate`); jede Notiz kehrt ohne Debug-Ausgabe sofort zurück, und `ezpKernelWeb` beginnt
jede Anfrage leer, auch in einem persistenten Worker. Der kompilierte Code eines Cache-Blocks übergibt seine Platzierung
an `eZTemplateCacheBlock::handle()` (neue optionale Parameter `$placement` und `$keys`) und speichert über
`eZTemplateCacheBlock::store()`, das die Erzeugungszeit notiert; eigener Code, der `handle()` oder `storeCache()` wie
bisher aufruft, funktioniert weiter.

Tests: `expRenderStatsTest`, `expRenderStatsCacheBlockTest` und `expRenderStatsTreeTest` (ohne Datenbank).

### Panels von Erweiterungen

Eine Erweiterung kann der Leiste eine eigene Registerkarte hinzufügen, mit einem Chip in der Übersicht und Daten im
JSON der Seite - zum Beispiel das Layout, aus dem eine Seite gebaut wurde. Sie nennt eine ID und eine Klasse in ihrer
`settings/debugbar.ini.append.php`:

```ini
[DebugBarSettings]
Panels[]=mylayouts

[Panel_mylayouts]
Class=myLayoutsDebugBarPanel
```

Die Klasse implementiert `expDebugBarPanelInterface` (`kernel/classes/debugbar/expdebugbarpanelinterface.php`):

```php
class myLayoutsDebugBarPanel implements expDebugBarPanelInterface
{
    public function visible()
    {
        // the bar also shows to anonymous visitors when DebugByIP matches
        $access = eZUser::currentUser()->hasAccessTo( 'mylayouts', 'edit' );
        return $access['accessWord'] !== 'no';
    }
    public function title() { return ezpI18n::tr( 'extension/mylayouts', 'Layouts' ); }
    public function chip() { return array( 'text' => 'Layout: Home', 'title' => 'Rule home', 'level' => 'ok' ); }
    public function html() { return '<p>' . htmlspecialchars( 'Home', ENT_QUOTES ) . '</p>'; }
    public function data() { return array( 'layout' => 'home' ); }
}
```

- Die ID der Registerkarte ist `x-<id>` in Kleinbuchstaben (`data-tab="x-mylayouts"`, Abschnitt
  `exp-debug-panel-x-mylayouts`), sodass sie nie auf eine eingebaute Registerkarte trifft. Eine ID besteht aus
  Buchstaben, Ziffern und `_`; zwei IDs, die sich nur in der Groß- und Kleinschreibung unterscheiden, sind eine ID.
- `visible()` kommt zuerst; ein Panel, das nein sagt, zeichnet nichts. `html()` wird unverändert ausgegeben: Das Panel
  maskiert, was es einfügt. Text und Titel des Chips maskiert die Leiste, eine andere Stufe als `ok`, `warn` oder
  `high` ist `ok`, und ein Klick auf den Chip öffnet die Registerkarte. `data()` geht in `panels.<id>` des JSON
  (`#exp-debug-data`).
- Ein Eintrag ohne verwendbare Klasse (kein `Class`, eine unbekannte Klasse, eine ohne die Schnittstelle) und ein Panel,
  das eine Ausnahme wirft oder keinen Titel liefert, werden mit einer Warnung in der Debug-Ausgabe ausgelassen; der
  Rest der Leiste wird gezeichnet.
- Die Klasse wird für jeden Bericht ohne Argumente erzeugt, vor `visible()` und auch für anonyme Besucher: Halten Sie
  den Konstruktor trivial, ohne Datenzugriff, und den Rest günstig, da die Leiste auf jeder Seite rendert, die sie
  zeigt. `html()` gibt sein Markup zurück und gibt selbst nichts aus.

### Server: wo die Anfrage einging und was sie gerendert hat

Die Registerkarte beginnt mit einem Satz, der sagt, wo die Anfrage einging und welcher Prozess den Bericht gerendert
hat, dann folgen die Fakten dahinter (`expDebugBarReport::engineFacts()`, aus den Servervariablen und
`engineProbe()`):

| Gerendert von | Satz (Beispiel) |
|---|---|
| einem Velocity-Worker | Received by Velocity on port 8080 and rendered by a Velocity worker. |
| dem Velocity-Elternprozess (kein Pool) | Received by Velocity on port 8080 and rendered by the Velocity parent process (no worker). |
| php-cgi, von Velocity gestartet für ein Skript unter `Q.webserver.cgi.patterns` | Received by Velocity on port 8080 and rendered by php-cgi, which Velocity starts for this script (Q.webserver.cgi.patterns). |
| PHP-FPM hinter Apache (oder nginx) | Served by Apache / PHP-FPM (port 443), not by Velocity. |
| mod_php, FrankenPHP, eine andere SAPI | Served by Apache with mod_php (port 80), not by Velocity. |

Die Zeilen: **Eingegangen** (Schema, Port und die gesehenen weitergeleiteten Header, etwa `X-Real-IP` und
`X-Forwarded-For` von einem vorgeschalteten Proxy), **Angenommen von** (Exponential Velocity und seine Version, oder
der Server, den `SERVER_SOFTWARE` nennt), **Erzeugt von**, PHP und seine SAPI, **Prozess** und **Host** (nur für einen
Benutzer mit Setup- oder Cache-Zugriff), **Erzeugt um** und **Zwischengespeichert**: Eine Seite mit dem Bericht wird für
jede Anfrage gerendert und von keinem Cache behalten.

Es entscheiden nur Signale, die ein Client nicht setzen kann: `QBIX_SERVER_VERSION` (nur in einem Velocity-Prozess
definiert), die SAPI sowie `SERVER_SOFTWARE` und `SERVER_PORT`, wie der Server sie gesetzt hat. Ein Worker wird vom
Elternprozess an seinem Elternprozess unterschieden, der der Velocity-Elternprozess oder seine Zygote (`qbixserver`)
ist. Weitergeleitete Header werden wie gesehen aufgeführt und entscheiden nichts, da ein Client sie ebenfalls senden
kann. Velocity beantwortet jede Anfrage selbst; es reicht nie eine an PHP-FPM oder Apache weiter, daher gehört ein von
PHP-FPM gerenderter Bericht immer zu einer Anfrage, die an Apache ging (zum Beispiel nginx vor Apache auf 443).

**Ein Bericht, der aus einem Cache kam.** Die Seite trägt den Bericht der Anfrage, die sie gerendert hat. Seiten mit dem
Bericht werden mit `Cache-Control: private, no-store` gesendet (`eZDebugReportNoStore()` in `eZDisplayDebug()`), daher
behält sie weder der Antwort-Cache von Velocity noch der rollenabhängige Seiten-Cache
(`X-Exp-Cache: BYPASS (debug report)`). Sollte trotzdem eine von irgendwoher kommen (eine vor dem 2026-10-14
gespeicherte Seite, ein Browser oder Service Worker, der sie wiedergibt), vergleicht das Skript den Port des Berichts
(`data-entry-port`) mit der Adresse im Browser und seine Zeit (`data-rendered-at`) mit der Zeit, zu der die Seite
geladen wurde, und sagt das über den Fakten: „This report was rendered at 04:08:02 by PHP-FPM 244909 for a request on
port 443, 9 min before this page was loaded on port 8080: the page came from a cache, not from this request.“
(`data-exp-debug-replayed`; 120 s Spielraum für eine abweichende Uhr).

Warum sich die Registerkarte geändert hat (2026-10-14): Auf :8080 sagte die Registerkarte „Not running on Velocity“,
Engine Apache (fpm-fcgi). Die Seite war die Startseite eines angemeldeten Benutzers aus dem rollenabhängigen
Seiten-Cache, um 04:08:02 von PHP-FPM (Prozess 244909) für eine Anfrage auf :443 gespeichert und auf :8080 vom
Elternprozess von Velocity direkt aus diesem gemeinsamen Speicher (`Q.web.appCache`) beantwortet, mit dem Bericht von
PHP-FPM darin. Der eigene Antwort-Cache von Velocity tat dasselbe für anonyme Seiten: Eine für eine aufgeführte Adresse
gerenderte Seite wurde mit ihrem Bericht behalten und jedem Besucher von :8080 ausgeliefert (`X-Cache: HIT`).

Der Kasten OPcache sagt, warum er nichts zu zeigen hat: `opcache_get_status()` für dieses PHP ausgeschaltet (die
`disable_functions` eines Pools) oder OPcache nicht geladen.

Test: `expDebugBarReportEngineTest` (ohne Datenbank; Servervariablen und die Probe werden übergeben).

### Tests

Die Leiste wird in einem Browser bei 960 px Breite und Geräteskalierung 2 getestet, in den Designs admin, admin2 und
admin4 und auf der öffentlichen Website, hell und dunkel: die angeheftete Leiste und das Panel, offen lassen beim
Neuladen, die Registerkarten mit Maus und Tastatur, die Übersicht gegenüber dem Bericht, der Filter, die Prüfung der
IP, keine Seitenfehler. Auf der Admin-Seite außerdem eine Einstellung, die in das globale Override geschrieben und
rückgängig gemacht wird, eine angewendete und zurückgenommene Voreinstellung (jede Einstellungsdatei Byte für Byte
verglichen; bei einem Fehler schreibt der Test die Dateien zurück), die Bedienelemente der IP-Liste und „Nur diese
Seite“. Screenshots gehen nach `var/tmp/debug-bar-<engine>-<page>-<tab>.png`. Eine Prüfung der Wörter stellt sicher,
dass jede Zeichenkette der Leiste übersetzt ist.

## Verwandte Seiten

- [Die Debug-Leiste von Exponential (Funktion)](../../../eng-us/features/6.0/exp-debug-bar.md)
- [exp:ini, die INI-Engine, über die die Leiste schreibt](console-exp-ini.md)
- [Cache-Konsole (`exp:cache`)](cache-konsole.md)
- [Verhaltensänderungen vom 1. und 2. Oktober 2026](verhaltensaenderungen-2026-10.md)
- [August 2024](../../../eng-us/history/2024/2024-08.md), [Januar 2025](../../../eng-us/history/2025/2025-01.md), [August 2025](../../../eng-us/history/2025/2025-08.md)
