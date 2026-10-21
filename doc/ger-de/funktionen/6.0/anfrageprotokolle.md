# Anfrageprotokolle: 404.log und security.log

Exponential schreibt eine Anfrage nach einer Seite, die nicht existiert, und eine Anfrage, die wie ein Angriff aussieht,
in eigene Protokolle statt in `error.log`. `error.log` behält die echten Fehler, die Antworten „nicht gefunden“ gehen
mit dem Grund, den der Kernel gefunden hat, nach `var/log/404.log`, und was der Anfrageklassifizierer für eine Sondierung
oder einen Angriff hält, geht mit Kategorie und Regel nach `var/log/security.log`. Beide sind Kernel-Protokolle des
[Log-Viewers](log-betrachter.md), werden in seiner Fehlerübersicht und auf seiner Seite zum Zustand der Logs aufgeführt und
wie die anderen Protokolle rotiert ([Log-Rotation](log-rotation.md)).

## Kurz gesagt

| Protokoll | Was | Stufe im Viewer |
|---|---|---|
| `error.log` | nur echte Fehler (ein Datenbankfehler, ein Template, das sich nicht kompilieren lässt, ein PHP-Fehler ...) | error |
| `404.log` | die Antworten 404 und 410: ein nicht definiertes Modul oder eine nicht definierte Ansicht, ein Modul oder eine Ansicht, die der Siteaccess abschaltet, ein Knoten oder Objekt, das nicht vorhanden ist, eine Anfrageregel, die „nicht gefunden“ antwortet | notice |
| `notice.log` | die Antworten 401 und 403, wie zuvor: Sie sind Zugriffsantworten; die Sicherheitsseite steht in `auth.log` und im Prüfprotokoll | notice |
| `security.log` | eine Anfrage, die der Anfrageklassifizierer als Sondierung, Pfadüberschreitung, Injection, Angriffswerkzeug oder Häufung von Antworten „nicht gefunden“ einstuft, **mit beliebigem Status** (200, 401, 403, 404, 500 ...) | warning |

- **Jede Anfrage wird genau einmal klassifiziert, egal wie sie endet:** am normalen Ende, über
  `eZExecution::cleanExit()` (ein Datei-Download, ein ezjscore-Aufruf, eine Weiterleitung, eine Ansicht, die ihre
  eigene Ausgabe sendet), über das Fehlermodul oder nach einem fatalen Fehler. Eine Sondierung wie
  `/content/download/../../etc/passwd` steht in `security.log`, auch wenn die Download-Ansicht sie selbst beantwortet;
  ein normaler Download oder ezjscore-Aufruf wird nicht markiert.
- **Eine Zeile pro Anfrage, in einem Protokoll.** Eine Anfrage steht in `security.log` *oder* in `404.log` *oder* (bei
  einem echten Fehler) in `error.log`, nie in zweien davon. Eine klassifizierte Sondierung, die mit einem 404 endet,
  steht nur in `security.log`; ihre 404-Zeile (und der 401/403-Hinweis einer klassifizierten Anfrage) wird nicht
  geschrieben.
- **Nichts wird blockiert.** Der Klassifizierer klassifiziert nur: Die Anfrage erhält die Antwort, die sie ohnehin
  erhalten würde. Listener können später auf eine Einstufung reagieren (siehe
  [Listener](#listener-der-ansatzpunkt-zum-blockieren)).
- **Datenschutz zuerst.** Maskierte Client-Adressen, ein Hash und ein kurzer Teil des User-Agents, jeder Query-Wert durch
  `***` ersetzt. Die rohe Adresse wird nie in eine Datei geschrieben.
- **Abwärtskompatibel.** Mit `site.ini [RequestLogSettings] NotFoundLog=disabled` gehen die Antworten „nicht gefunden“
  genau wie zuvor nach `error.log`; mit `requestclassifier.ini [ClassifierSettings] Classification=disabled` wird nichts
  klassifiziert, und `security.log` bleibt leer.

## Die Zeilenformate

Beide Protokolle beginnen ihre Zeilen mit dem Präfix, das eZLog schreibt, der Zeit und der maskierten Client-Adresse:

```
[ Oct 17 2026 09:00:00 ] [203.0.x.x] <the line>
```

### 404.log

```
[ Oct 17 2026 09:00:00 ] [203.0.x.x] 404 GET /site/no-such-page?q=*** siteaccess=site reason=module:no-such-page node=- referrer=- ua=3f2a9c1b "Mozilla/5.0 (X11; Linux x86_64)"
[ Oct 17 2026 09:00:04 ] [2001:db8:x:x] 404 GET /content/view/full/9999 siteaccess=site reason=node:9999 node=9999 referrer=https://example.org/news ua=5d41402a "Mozilla/5.0 (Macintosh; Intel Mac OS X 14_5)"
[ Oct 17 2026 09:00:09 ] [198.51.x.x] 410 GET /old-campaign siteaccess=site reason=kernel:kernel/20 node=- referrer=- ua=- ""
```

| Feld | Bedeutung |
|---|---|
| `404` | der Status der Antwort: 404 oder 410 |
| `GET /site/no-such-page?q=***` | die Methode und die URI: der Pfad wie angefragt (Geheimnisse und E-Mail-Adressen darin maskiert), jeder Query-Wert `***`, Steuerzeichen entfernt, Leerzeichen kodiert, höchstens `MaxURILength` Zeichen (eine längere endet auf `...`) |
| `siteaccess=` | der Siteaccess, der geantwortet hat |
| `reason=` | warum die Seite fehlt, der erste Grund, den der Kernel gefunden hat: `module:<name>` (kein solches Modul), `view:<module>/<view>` (keine solche Ansicht), `disabled:<module>[/<view>]` (für diesen Siteaccess abgeschaltet), `node:<id>`, `object:<id>`, `rule:<name>` (eine Regel aus `requestrules.ini`, die „nicht gefunden“ antwortet), `file:<object>/<attribute>` (ein Download, dessen Datei nicht verfügbar ist) oder `kernel:<error type>/<error number>`, wenn nichts Genaueres bekannt ist |
| `node=` | die Knoten-ID, wenn sie bekannt ist, sonst `-` |
| `referrer=` | die verweisende Adresse, geschrieben wie die URI, oder `-` |
| `ua=3f2a9c1b "..."` | die ersten 8 Hexadezimalziffern des SHA-1 des User-Agents und seine ersten 60 Zeichen ohne Anführungszeichen und Steuerzeichen (`ua=- ""` ohne User-Agent) |

### security.log

```
[ Oct 17 2026 09:00:00 ] [203.0.x.x] probe/wp-login confidence=high 404 GET /wp-login.php siteaccess=site namespace=/wp-login.php ua=3f2a9c1b "curl/8.5.0"
[ Oct 17 2026 09:00:02 ] [203.0.x.x] injection/sql-union confidence=high 200 GET /content/search?SearchText=1%27%20UNION%20SELECT%20login%20FROM%20ezuser-- siteaccess=site namespace=/content/search ua=3f2a9c1b "curl/8.5.0"
[ Oct 17 2026 09:00:03 ] [192.0.x.x] threshold/404-rate confidence=medium 404 GET /a7f3.php siteaccess=site namespace=/a7f3.php ua=0beec7b5 "Mozilla/5.0"
```

| Feld | Bedeutung |
|---|---|
| `probe/wp-login` | `<category>/<rule>`: die Kategorie des Regelsatzes (`probe`, `traversal`, `injection`, `scanner`, `threshold` oder eine, die eine Extension hinzufügt: Kleinbuchstaben, Ziffern, `_` und `-`) und die Regel, die zugetroffen hat |
| `confidence=` | `low`, `medium` oder `high`: wie sicher sich die Regel ist |
| `404` | der endgültige HTTP-Status oder `-`, wenn er nicht bekannt ist |
| `GET /wp-login.php` | die Methode und die URI. Anders als in 404.log bleiben die Query-Werte erhalten, weil sie der Beleg für den Angriff sind; Geheimnisse, E-Mail- und IP-Adressen darin werden mit dem Redaktor des Log-Viewers maskiert, und die URI wird bei `MaxURILength` abgeschnitten |
| `siteaccess=` | der Siteaccess, `-`, wenn unbekannt |
| `namespace=` | der Pfad unterhalb des Siteaccess: `/admin/wp-login.php` und `/site/wp-login.php` sind beide `/wp-login.php`, sodass eine Regel unter jedem Siteaccess denselben Pfad sieht |
| `ua=` | wie in 404.log |

Der Log-Viewer liest beide Formate: Anfrage, Siteaccess und Status sind seine Felder, und die Spalte für die Quelle zeigt
den Status (404.log) oder `<category>/<rule>` (security.log).

## Einstellungen

### site.ini [RequestLogSettings]

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `NotFoundLog` | `enabled` | `enabled`: Antworten 404 und 410 werden in `NotFoundLogFile` geschrieben und nicht mehr in `error.log` („Undefined module“, „Module/View ... is disabled“, „Undefined view“, „Error ocurred using URI ... HTTP 404“, Knoten nicht gefunden). `disabled`: `error.log` wie zuvor. |
| `NotFoundLogFile` | `404.log` | die Datei, im Protokollverzeichnis (`var/log` oder `var/<site>/log` mit `[FileSettings] UseGlobalLogDir=disabled`). Der Log-Viewer kennt sie über den Schlüssel ihres Namens: `404.log` ist der Schlüssel `404`. Eine Datei mit anderem Namen hat keine Beschriftung und keine Rotationsstandards, bis Sie ihr eigene geben (siehe [Der Log-Viewer](#der-log-viewer-die-rotation-und-die-fehlerübersicht)). |
| `MaskIP` | `enabled` | die Client-Adresse mit verborgenem Host-Teil (`203.0.x.x`, `2001:db8:x:x`), dieselbe Ausgabe wie die Maskierung des Log-Viewers. `disabled` schreibt sie vollständig; nur für eine Website, die einen Grund und eine Rechtsgrundlage dafür hat. |
| `MaxURILength` | `300` | die längste geschriebene URI (und der längste Referrer); eine längere wird abgeschnitten und endet auf `...`. |

### requestclassifier.ini

`settings/requestclassifier.ini`, überschrieben in `settings/override/requestclassifier.ini.append.php`, einem
Siteaccess oder einer Extension.

- `[ClassifierSettings]`
  - `Classification=enabled`: `disabled` klassifiziert nichts; Sondierungen werden dann als die Antworten „nicht
    gefunden“ protokolliert, die sie sind.
  - `LogFile=security.log`: die Datei, im selben Protokollverzeichnis.
  - `RuleSets[]`: die Regelsätze in der Reihenfolge, in der sie versucht werden; der erste mit einer Einstufung gewinnt,
    sodass eine Anfrage eine Zeile mit einer Regel ist. Eingebaut: `probes`, `traversal`, `injection`, `scanner`,
    `threshold`. Lassen Sie einen weg, um ihn abzuschalten.
  - `Listeners[]`: Klassen, die über jede Einstufung informiert werden (standardmäßig keine).
  - `CompileCache=enabled`: Die kompilierten Muster werden im Cache-Verzeichnis gehalten, sodass ein frischer Worker sie
    nicht erneut kompiliert.
- `[ThresholdSettings]`: `Max404PerMinute=30` Antworten „nicht gefunden“ (`Statuses[]`: 404 und 410) an einen Client
  innerhalb von `WindowSeconds=60`; jede weitere in diesem Zeitfenster ist eine Einstufung `threshold/404-rate`
  (medium; high bei mehr als dem Doppelten der Grenze). `0` schaltet die Regel ab.
- `[RuleSet-<id>]`, eine Gruppe pro Satz:
  - `Class=`: die Klasse des Satzes (leer für einen eingebauten; nötig für den Satz einer Extension);
  - `Patterns[<rule>]=<regex>`: eine eigene Regel oder eine eingebaute Regel, die durch Angabe ihres Namens ersetzt
    wird (PCRE ohne Begrenzer, ohne Beachtung der Groß- und Kleinschreibung, keine Rückverweise oder benannten Gruppen;
    ein Muster, das sich nicht kompilieren lässt, wird mit einer Warnung in der Debug-Ausgabe ausgelassen);
  - `Disabled[]=<rule>`: eine abgeschaltete eingebaute Regel;
  - `Excludes[<name>]=<regex>`: ein Pfad, der von dem Satz nie markiert wird (`probes` schließt standardmäßig
    Downloads, Storage, die gepackten Skripte und Stile sowie die Design-Assets aus);
  - `Confidence[<rule>]=low|medium|high` und `DefaultConfidence`.

Die Regelnamen jedes eingebauten Satzes sind in der Datei selbst aufgeführt. Eine Suche nach „union station“, O'Reilly
oder `<b>` ist keine Injection, und allgemeine Clients (curl, wget, Headless-Browser, Suchmaschinen) sind keine
Angriffswerkzeuge.

## Einen Regelsatz in einer Extension hinzufügen

Ein Regelsatz ist eine Klasse, die `expRequestRuleSetInterface` implementiert (`identifier()`, `evaluate( expRequestContext
$context )`, das ein `expRequestVerdict` oder `null` zurückgibt). Für einen Satz regulärer Ausdrücke erweitern Sie
`expRequestPatternRuleSet`; diese Klasse kompiliert die Muster einmal, wendet `Patterns[]`, `Disabled[]`, `Excludes[]`
und `Confidence[]` aus der INI-Gruppe an und cacht das Ergebnis:

```php
<?php
// extension/myext/classes/myextrequestrulesetlegacyapps.php
class myextRequestRuleSetLegacyApps extends expRequestPatternRuleSet
{
    /** The first part of the log line: legacyapp/<rule> */
    public function category()
    {
        return 'legacyapp';
    }

    /** rule => regular expression (no delimiters, matched case-insensitively) */
    protected function builtInPatterns()
    {
        return array(
            'joomla-admin' => '^/administrator(?:/|$)',
            'typo3-backend' => '^/typo3(?:/|$)',
        );
    }

    /** What the patterns are matched against: here the path below the siteaccess, decoded */
    protected function subjects( expRequestContext $context )
    {
        return array( $context->decodedNamespacePath() );
    }
}
```

```ini
# extension/myext/settings/requestclassifier.ini.append.php
[ClassifierSettings]
RuleSets[]=legacyapps

[RuleSet-legacyapps]
Class=myextRequestRuleSetLegacyApps
# optional: a rule of the site, a rule switched off, a confidence
Patterns[drupal-install]=^/install\.php$
Disabled[]
Confidence[typo3-backend]=medium
```

Erzeugen Sie die Autoloads der Extensions neu (`php bin/php/ezpgenerateautoloads.php -e`). Eine Klasse, die nicht
existiert oder die Schnittstelle nicht implementiert, wird mit einer Warnung in der Debug-Ausgabe übersprungen; eine
Exception in `evaluate()` überspringt den Satz für diese Anfrage. `evaluate()` läuft für jede klassifizierte Anfrage und
muss daher günstig sein: keine Datenbank, kein Netzwerk, kein Dateizugriff. Die Zeilen lauten dann
`legacyapp/joomla-admin confidence=high 404 GET /administrator/ ...`, und die Fehlerübersicht gruppiert sie ohne weitere
Einstellung unter `legacyapp/joomla-admin`.

## Listener: der Ansatzpunkt zum Blockieren

`[ClassifierSettings] Listeners[]` nennt Klassen, die `expRequestVerdictListenerInterface` implementieren:

```php
class myextVerdictListener implements expRequestVerdictListenerInterface
{
    public function onVerdict( expRequestVerdict $verdict, expRequestContext $context )
    {
        // $verdict->category, $verdict->rule, $verdict->confidence
        // $context->ip is the raw address: hash or mask it before you keep it anywhere
    }
}
```

Ein Listener läuft am Ende der Anfrage, nachdem die Sicherheitszeile geschrieben ist, und muss günstig sein; eine
Exception, die er wirft, wird abgefangen und ignoriert. Standardmäßig ist keiner konfiguriert, und der Kernel blockiert,
verlangsamt oder antwortet nie anders aufgrund einer Einstufung. Hier können später eine Sperrliste, eine Ratenbegrenzung
oder eine Benachrichtigung ergänzt werden.

## Datenschutz

- **Adressen maskiert** (`MaskIP=enabled`): `203.0.x.x`, `2001:db8:x:x`. Eine Adresse in einer Form, die keines der
  beiden Muster kennt, wird als `x` geschrieben, nie vollständig.
- **Der User-Agent** als die ersten 8 Hexadezimalziffern seines SHA-1 plus seine ersten 60 Zeichen: genug, um ein
  Werkzeug von einem anderen zu unterscheiden, kein Fingerabdruck.
- **Die Query geschwärzt** (404.log): Jeder Wert ist `***` (die Namen bleiben erhalten, sodass `?q=***&page=***` noch
  sagt, was gefragt wurde); Geheimnisse und E-Mail-Adressen im Pfad werden mit dem Redaktor des Log-Viewers maskiert.
  security.log behält die Query-Werte (den Beleg der Sondierung) mit maskierten Geheimnissen, E-Mail- und IP-Adressen.
- **Der Zähler für „nicht gefunden“** der Schwellenwertregel wird über einen gesalzenen SHA-1 der Adresse angesprochen
  (ein zufälliges Salz im Cache-Verzeichnis plus `site.ini [HTMLForms] Secret`); die Adresse selbst wird nie
  gespeichert. Ein Leeren des Caches erzeugt ein neues Salz, was nur die Zählung neu startet.
- Der Log-Viewer maskiert, was er anzeigt, erneut (`[RedactionSettings]`), und die Fehlerübersicht ist immer maskiert.

## Leistung

- Kein Datenbankzugriff. Die Muster eines Regelsatzes werden pro Satz zu einem regulären Ausdruck kompiliert und pro
  Version der Einstellungen im Cache-Verzeichnis gecacht (`CompileCache`).
- Eine Anfrage wird einmal klassifiziert; eine Anfrage mit der Antwort 200 wird am Ende der Anfrage klassifiziert, zum
  Preis einiger regulärer Ausdrücke auf ihrem Pfad, ihrer Query und ihrem User-Agent.
- Nur eine Antwort 404 oder 410 berührt den Zähler für „nicht gefunden“ (eine kleine Dateiaktualisierung unter einer
  Sperre).
- Ein `file_put_contents` mit `FILE_APPEND | LOCK_EX` pro protokollierter Anfrage; Dateien werden mit den Rechten einer
  Protokolldatei angelegt, sodass sowohl der Benutzer des Webservers als auch ein Velocity-Worker, der als anderer
  Benutzer läuft, anhängen können.

## Der Log-Viewer, die Rotation und die Fehlerübersicht

`settings/logviewer.ini` macht beide Dateien zu Kernel-Protokollen von **Setup > Logdateien**:

- `KernelLogs[]=404.log` und `KernelLogs[]=security.log`: unter „Kernel“ aufgeführt, geöffnet unter
  `/setup/logs/view/404` und `/setup/logs/view/security`, verwaltet (rotieren, ausdünnen, komprimieren) wie jedes
  Kernel-Protokoll.
- `LogLabel[404]=Not found requests (404)`, `LogLabel[security]=Security (request classifier)`: die Namen, die der
  Navigator und die Übersicht zeigen (übersetzt im Kontext `kernel/logviewer`: „Nicht gefundene Anfragen (404)“,
  „Sicherheit (Anfrageklassifizierung)“). `LogLabel[<key>]` benennt jedes Protokoll der Protokollverzeichnisse über
  seinen Schlüssel.
- `FileLevels[]=404.log*;notice` (eine Antwort „nicht gefunden“ ist eine Antwort, kein Fehler) und
  `FileLevels[]=security.log*;warning` (einen Blick wert; daher führt die Fehlerübersicht es standardmäßig auf).

**Die Fehlerübersicht** (Setup > Logdateien > Fehlerübersicht) führt beide auf, pro Protokoll gruppiert nach
`[DigestSettings]`:

- `GroupBy[security]=category-rule`: security.log nach `<category>/<rule>` (`probe/wp-login 312`,
  `injection/sql-union 4`), nicht nach der ganzen Zeile, die sich in jeder URI unterscheidet.
- `GroupBy[404]=reason`: 404.log nach seinem Feld `reason=`, wobei IDs und lange Pfade ausgeklammert werden
  (`reason=module:wp-admin`, `reason=node:#`, `reason=view:content/#`): warum Seiten fehlen, keine Liste jeder fehlenden
  Adresse.
- `LogLevels[404]=error,warning,notice`: 404.log wird aufgeführt, obwohl seine Zeilen Hinweise sind.
- Andere Gruppierungen für jedes Protokoll: `message` (der Standard), `reason-kind` (nur `module`, `view`, `node` ...)
  und `uri` (die Methode und der Pfad ohne die Query, Zahlen ausgeklammert). Jede Gruppe verlinkt auf das auf sie
  gefilterte Protokoll.

**Rotation** (`[RotationSettings]`, siehe [Log-Rotation](log-rotation.md)): Beide Protokolle behalten 5 Archive,
komprimiert ab `.2`, mit einer Größengrenze von 10 MB für den Cronjob-Teil `logs`; ein Ausdünnen entfernt die Archive
von 404.log nach 30 Tagen und die von security.log nach 90 Tagen. Während eine Zeile geschrieben wird, rotiert der
Schreiber die Datei bei `site.ini [FileSettings] LogFileMaxSize` wie jedes andere Protokoll.

```ini
# settings/override/logviewer.ini.append.php: keep security.log longer
[RotationSettings]
Keep[security]=10
MaxAgeDays[security]=365
```

**Zustand der Logs** (Setup > Logdateien > Zustand) rechnet beide auf das Speicherplatzbudget an und zeigt ihr Wachstum
und ihre Grenzen.

Die Berichte der Content Security Policy haben ein eigenes Protokoll, `var/log/csp.log` (Schlüssel `csp`, „Berichte der
Content Security Policy“), mit derselben Rotation und einer Übersicht, die nach Direktive und blockierter Quelle
gruppiert ist (`GroupBy[csp]=directive-blocked`); eine Flut von Berichten ist eine Zeile `abuse/csp-report-flood` pro
Minute in security.log. Siehe [Content Security Policy](content-security-policy.md), „Berichte“.

Mit `NotFoundLogFile=notfound.log` ist der Schlüssel `notfound`: Geben Sie ihr ihre Einstellungen unter diesem Schlüssel
(`LogLabel[notfound]=...`, `KernelLogs[]=notfound.log`, `FileLevels[]=notfound.log*;notice`, `GroupBy[notfound]=reason`,
`LogLevels[notfound]=error,warning,notice` und ihre `[RotationSettings]`).

## Request Shield und der Klassifizierer

[request-shield](https://github.com/cjw-network/request-shield) (cjw-network) ist eine eigene Schicht, die **vor dem
Start des Kernels** läuft, am Anfang jeder Anfrage: aus `config.php` oder über `auto_prepend_file` von PHP unter Apache
oder nginx mit PHP-FPM, und aus der Datei, die `config.php` in `EXP_REQUEST_PREPEND_FILE` nennt, unter Exponential
Velocity (das Beispiel in `config.php-RECOMMENDED`). Ihre Regeln (zum Beispiel `var/request-shield/rules/*.rules`)
können eine Anfrage ablehnen oder protokollieren, bevor der Kernel startet, in ihrem eigenen Protokoll (zum Beispiel
`var/request-shield/log/shield.log`). Eine Anfrage, die sie ablehnt, erreicht den Kernel nie und steht daher auch nicht
in `security.log`; in ihrem Überwachungsmodus protokolliert sie nur, und dieselbe Sondierung kann dann in `shield.log`
und in `security.log` stehen. Wie Sie sie installieren und einschalten und was die Beispielregeln von Exponential tun:
[Sicherheitshärtung: Ein Schutzschild davor](../../hilfe/sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield).

Der Anfrageklassifizierer ist die eigene Schicht des Kernels, unter Apache/PHP-FPM wie unter Velocity. Er
**klassifiziert nur**: Er lehnt nie eine Anfrage ab. Wo sich die beiden überschneiden, verwenden sie dieselbe
Vorstellung einer „Sondierung“ und ähnliche Namen (`wp-login`, `env`, `git` ...), sodass sich eine Zeile des einen im
anderen wiederfinden lässt, aber sie werden getrennt konfiguriert, und keiner liest die Regeln des anderen.

## Für Entwickler

- `expRequestLog` (`kernel/classes/requestlog/`): der Zustand des Protokolls für „nicht gefunden“ pro Anfrage.
  `reason()` hält fest, warum die Seite fehlt (der erste Grund gewinnt), `handleError()` wird vom Fehlermodul
  aufgerufen und schreibt die Sicherheitszeile oder die 404-Zeile, `finish()` klassifiziert am Ende der Anfrage, was
  noch nicht klassifiziert wurde. `reset()` für Tests und persistente Worker.
- Wie eine Anfrage endet, spielt keine Rolle: `ezpKernelWeb::run()` ruft `expRequestLog::begin()` auf, das den Zustand
  zurücksetzt und `expRequestLog::finish()` als Aufräum-Handler von `eZExecution` registriert. `eZExecution::cleanup()`
  läuft bei `cleanExit()`, am normalen Ende (`ezpKernelWeb::shutdown()`, das auch selbst `finish()` aufruft) und im
  Shutdown-Handler nach einem fatalen Fehler; nur das erste `finish()` einer Anfrage zählt
  (`expRequestLog::finished()`). Unter den persistenten Workern von Velocity hält `eZExecution` die Handler pro Anfrage
  (`REQUEST_TIME_FLOAT`) und fügt einen Handler nur einmal hinzu, sodass nichts übertragen oder verdoppelt wird.
  Umschließen Sie ein `cleanExit()` nie mit `catch ( Exception )`: Unter Velocity beendet es die Anfrage durch Werfen
  einer Exception.
- `expRequestClassifier` (`kernel/classes/requestclassifier/`): `instance()`, `classify( expRequestContext )`,
  `securityLine()`, `log()`; mit `expRequestContext` (`fromGlobals()`, `create()` für Tests), `expRequestVerdict`,
  `expRequestRuleSetInterface`, `expRequestPatternRuleSet` und `expRequestVerdictListenerInterface`.
- Der Log-Viewer: `expLogParser::requestHeader()` liest beide Formate, `expLogDigest::groupText()` die Gruppierungen.
- Tests (ohne Datenbank): `tests/tests/kernel/requestlog/`, `tests/tests/kernel/requestclassifier/` und
  `tests/tests/kernel/logviewer/expLogRequestLogsTest.php`.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
