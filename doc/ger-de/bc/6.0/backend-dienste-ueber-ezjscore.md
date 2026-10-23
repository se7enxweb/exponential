# Backend-Dienste über ezjscore (expservices)

Lesen Sie diese Seite, wenn Sie eine entfernte Administrations-App (GTK, Qt, KDE/GNOME, iOS, Android, Shell) oder ein
JavaScript-Frontend auf Basis von Exponential bauen. `extension/expservices` macht aus den Server-Funktionen von
ezjscore eine Bibliothek entfernter Dienste, die die Funktionen von Exponential anbieten, mit Discovery, einem
einheitlichen Antwort-Umschlag, Richtlinien pro Dienst und persönlichen API-Tokens. Version 0.1.0 (erster Entwurf,
2026-10-02).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neue Erweiterung `expservices`: eine Klasse pro Domäne (`exp<Domain>Services`), registriert als `[ezjscServer_exp<domain>]`, aufgerufen als `<root>/ezjscore/call/exp<domain>::<function>`. Discovery über `expservices::catalog`. Persönliche API-Tokens (Tabelle `expservices_token`). Clients in Shell und Python sowie Code-Beispiele für andere Plattformen. |
| Wer betroffen ist | Niemand, bis die Erweiterung aktiviert wird. Um API-Tokens in einer bestehenden Installation zu verwenden, muss die Token-Tabelle einmal angelegt werden. Hinter Apache mit `mod_proxy_fcgi` erreicht der Header `Authorization` PHP möglicherweise nicht; verwenden Sie `X-Exp-Token`. |
| Wie Sie es prüfen | `curl -s 'https://www.example.com/ezjscore/call/expsystem::version?ContentType=json'` und `php vendor/bin/phpunit tests/tests/extension/expservices/` |
| Wie Sie es beheben | Aktivieren Sie die Erweiterung, erzeugen Sie die Autoloads neu (`php bin/php/ezpgenerateautoloads.php -e`) und legen Sie die Token-Tabelle aus `extension/expservices/sql/<engine>/schema.sql` an. |

Die Hostnamen in den Beispielen sind Platzhalter (`https://www.example.com`); verwenden Sie Ihre eigenen. Der
mitgelieferte Shell-Client nimmt die Adresse aus `-u` oder `EXPSERVICES_URL`; setzen Sie immer eines von beiden.

## Überblick

### Was eine Server-Funktion von ezjscore ist

Mit ezjscore (`extension/ezjscore`) kann eine Seite PHP über HTTP aufrufen. Eine Server-Funktion ist eine öffentliche
statische Methode einer Klasse, die in `ezjscore.ini` als Block `[ezjscServer_<name>]` (`Class=<class>`) registriert
ist. Sie wird aufgerufen als

    <root>/ezjscore/call/<name>::<function>[::<arg>[::<arg>...]]

(der Admin-Siteaccess hat dieselbe URL unter `/admin/`). Der Router (`ezjscServerRouter`) lehnt jede Klasse ab, die
keinen Block in `ezjscore.ini` hat, ruft `<class>::<function>( array $args )` auf und antwortet im Format des Headers
`Accept` oder von `?ContentType=json|xml|text`. `ezjscore/call` selbst prüft keine Richtlinie: Jede Funktion prüft
selbst. Mehrere Aufrufe können in einer Anfrage gesendet werden: POST `ezjscServer_function_arguments` mit den Aufrufen,
verbunden durch `@SEPARATOR$`.

Die Antwort von ezjscore umschließt den Rückgabewert: `{"error_text": "", "content": <what the function returned>}`.
Funktionen von expservices geben den unten beschriebenen Umschlag als `content` zurück.

### Die Klassen: eine Domäne, eine Klasse, ein Block

Jede Domäne ist eine Klasse `exp<Domain>Services`, die `expServiceBase` erweitert und in
`extension/expservices/settings/ezjscore.ini.append.php` als `[ezjscServer_exp<domain>]` registriert ist; ein Dienst
der Domäne `system` wird daher als `ezjscore/call/expsystem::version` aufgerufen. Jede öffentliche statische Methode
ist ein Dienst und wird in der Eigenschaft `public static $services` der Klasse deklariert:

    'version' => array( 'summary' => '...', 'access' => 'public' | 'user' | array( 'setup', 'system_info' ),
                        'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '...' )

`ezjscServerRouter::call()` übergibt jede Klasse, die `expServiceBase` erweitert, an `expServiceBase::invoke()`, das
prüft, dass der Dienst deklariert ist, ihn aufruft und jede Ausnahme in den Fehler-Umschlag umwandelt.

Deklarieren Sie die Liste als Wert der Eigenschaft im Rumpf der Klasse, nie durch eine Zuweisung nach der Klasse
(`expFooServices::$services = ...;`). Ein persistenter Velocity-Worker setzt die statischen Eigenschaften einer
Klasse, die er zuerst während einer Anfrage geladen hat, vor seiner nächsten Anfrage auf ihren deklarierten Wert
zurück, und `include_once` führt eine solche Zuweisung nicht erneut aus: Ab der zweiten Anfrage antwortete auf diesem
Worker jeder Dienst der Klasse mit „nicht deklariert“ (404), und die Übersicht der Erweiterungspunkte führte sie als
fehlend auf. Die zehn Domänen für Benutzer und Zugriff taten das bis zum 2026-10-12 (207 Dienste;
`expUsersDeclarationsTest::testTheServicesSurviveAResetToTheDeclaredValue` sichert das jetzt ab).

### Discovery

| Aufruf | Antwortet mit |
|---|---|
| `expservices::catalog[::<domain>]` | jedem Dienst: Domäne, Methode, Zusammenfassung, Zugriff, Schreiben, KI, Argumente, Rückgabe, Aufruf |
| `expservices::schema::<domain>` | den Diensten einer Domäne |
| `expservices::service::<domain>::<method>` | einer Beschreibung |
| `expservices::domains` | den Domänen mit ihrer Klasse und der Zahl ihrer Dienste |
| `expservices::version` | den Versionen von expservices, des Umschlags und von Exponential |

Die Domäne ist der Teil des Blocknamens nach `exp` (`session`, `system`, `ini`, `cache`, ...). Die Aufrufe der
Discovery-Funktionen sind öffentlich; jeder andere Dienst prüft seinen eigenen `access`.

### Der Umschlag

    { "ok": true,  "data": <the result>, "meta": { ... } }
    { "ok": false, "error": { "code": 403, "message": "No access to setup/managecache" } }

Listen werden seitenweise geliefert: `data` sind die Einträge und `meta` ist `total`, `offset`, `limit`, `count`,
`has_more`.

### Authentifizierung

Melden Sie sich einmal mit `expsession::login` an (POST `username`, `password`); die Antwort setzt das Sitzungs-Cookie,
das ein Client, der kein Browser ist, in einem Cookie-Speicher hält und mit jedem Aufruf sendet. `expsession::whoami`
sagt, wer die Sitzung ist, `expsession::token` gibt das für Schreibvorgänge nötige Formular-Token zurück,
`expsession::ping` hält die Sitzung am Leben, `expsession::logout` beendet sie. Clients, die kein Cookie halten
können, verwenden stattdessen ein persönliches API-Token, siehe unten.

    curl -c jar -d 'username=admin' --data-urlencode 'password=...' https://site/ezjscore/call/expsession::login
    curl -b jar https://site/ezjscore/call/expsession::whoami

### Authentifizierung mit einem API-Token

Für Apps, Skripte und Server ist ein Sitzungs-Cookie das falsche Werkzeug: Es läuft ab, es braucht den Anmeldeablauf
und für jeden Schreibvorgang ein Formular-Token. Ein persönliches API-Token ist die Alternative. Ein angemeldeter
Benutzer (Anmeldung per Sitzung, nicht per Token) erzeugt eines mit `expsession::tokenCreate` (POST `name`,
`expires_in_days` 1 bis 365, Standard 90); die Antwort enthält das Token **einmal** (`data.token`, `expt_` und 48
Hexadezimalzeichen). Gespeichert wird nur sein SHA-256-Hash (Tabelle `expservices_token`), daher kann ein verlorenes
Token nicht zurückgelesen, sondern nur widerrufen (`expsession::tokenRevoke`, POST `id`) und ersetzt werden.
`expsession::tokenList` listet die eigenen Tokens des Aufrufers: ID, Name, ein Hinweis (die ersten Zeichen), erzeugt,
zuletzt verwendet, Ablauf, widerrufen, gültig, nie das Token.

    curl -b jar -H 'X-CSRF-Token: <form token>' --data-urlencode 'name=backup script' https://site/ezjscore/call/expsession::tokenCreate?ContentType=json
    curl -H 'Authorization: Bearer expt_...' https://site/ezjscore/call/expsession::whoami?ContentType=json
    curl -H 'X-Exp-Token: expt_...' --data-urlencode 'id=7' https://site/ezjscore/call/expsession::tokenRevoke?ContentType=json

- **Senden.** `Authorization: Bearer <token>` oder der Header `X-Exp-Token: <token>`. Verwenden Sie `X-Exp-Token`, wo
  ein Proxy oder ein FastCGI-Aufbau den Header `Authorization` nicht an PHP übergibt (Apache mit `mod_proxy_fcgi`
  braucht `CGIPassAuth On` oder `SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1`; auf der
  Referenzinstallation kommt `Authorization` nicht an, und `X-Exp-Token` ist der Header, der funktioniert). Die
  mitgelieferten Clients senden beide.
- **Was es bewirkt.** Bevor irgendeine Prüfung den Benutzer ansieht, meldet die Basisklasse die Anfrage als Benutzer
  des Tokens an, nur für diese eine Anfrage: Die Sitzung wird nicht angetastet, daher wird kein Cookie erzeugt, und
  danach bleibt nichts angemeldet. Jede andere Prüfung ist unverändert: dieselben Richtlinien und Einschränkungen wie
  für diesen Benutzer, POST für Schreibvorgänge, das Audit-Ereignis jedes Schreibvorgangs.
- **Schreibvorgänge brauchen kein Formular-Token**, wenn ein Bearer-Token verwendet wird (aber weiterhin POST). Das
  Formular-Token schützt einen Browser, der sein Cookie an eine Anfrage hängt, die eine feindliche Seite von sich aus
  stellt; eine Anfrage, die das Token in einem Header trägt, den sie selbst setzen muss, lässt sich so nicht fälschen,
  daher hat das Formular-Token nichts zu beweisen. Eine Anfrage ohne den Token-Header fällt wie bisher auf das Cookie
  und das Formular-Token zurück.
- **Ablehnungen.** Ein unbekanntes, widerrufenes oder abgelaufenes Token oder ein Token, dessen Benutzer deaktiviert
  ist, wird bei jedem Dienst mit 401 beantwortet, auch bei öffentlichen (ein Client muss bemerken, dass sein Token
  ungültig geworden ist, statt stillschweigend anonym zu werden). Ein Token kann keine Tokens erzeugen (403): Dafür ist
  die Anmeldung per Sitzung nötig. Tokens anderer Benutzer sind unsichtbar (404).
- **Audit.** `access.expservices.token.create`, `access.expservices.token.revoke` und
  `access.expservices.token.failed` (ein abgelehntes Token, mit dem Grund) im Kanal `access`; der Zweig der Taxonomie
  `expServicesAuditBranch` ist in `extension/expservices/settings/audit.ini.append.php` registriert. Verwendungen werden
  nicht einzeln protokolliert; `last_used` des Tokens wird höchstens einmal pro Minute aktualisiert.
- **Installation.** Die Tabelle steht in `extension/expservices/sql/{mysql,postgresql,sqlite}/schema.sql` (die
  Erweiterung hat keine `share/db_schema.dba`); legen Sie sie in einer bestehenden Installation an, indem Sie das
  Schema Ihrer Datenbank-Engine einmal ausführen, zum Beispiel
  `mysql -u YOUR_USER -p YOUR_DATABASE < extension/expservices/sql/mysql/schema.sql` (es ist ein einfaches
  `CREATE TABLE`: Überspringen Sie es, wenn die Tabelle existiert).
- Von einem Token wird nur sein Hinweis je wieder angezeigt; bewahren Sie das Token im Geheimnisspeicher der Plattform
  auf (Keychain, Keystore, KWallet, libsecret), nie im Quelltext oder in einer URL.

### Zugriff

`'access'` eines Dienstes ist `public` (jeder), `user` (jeder angemeldete Benutzer) oder `array( module, function )`,
eine Richtlinie des Benutzers (`eZUser::hasAccessTo`; eine eingeschränkte Richtlinie besteht die Prüfung, und der
Dienst prüft dann das Objekt, z. B. `canRead()` eines Knotens). Eine fehlende Anmeldung wird mit 401 beantwortet, eine
fehlende Richtlinie mit 403.

Eine klassische Server-Funktion (ein Block `[ezjscServer_<name>]` in `ezjscore.ini`) wird durch ihre `Functions[]`
geschützt, die gegen die Richtlinie `ezjscore/call` mit der Einschränkung `FunctionList` geprüft werden.
`RequirePolicy` legt fest, wie: nicht gesetzt, werden `Functions[]` geprüft, und ein abgelehnter Aufruf wird wie bisher
mit seinem Fehlertext in einer Antwort mit 200 beantwortet; `disabled`, wird nicht geprüft; `enabled`, wird geprüft,
und eine Anfrage, deren Aufrufe alle abgelehnt werden, wird mit 403 beantwortet. `ezjscnode` wird mit
`Functions[]=ezjscnode` und `RequirePolicy=disabled` ausgeliefert; neue Installationen schreiben `enabled` (siehe
[Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md), Abschnitt 8).
`ezjscServerRouter::getInstance()` meldet in einem optionalen vierten Argument, warum es null zurückgegeben hat
(`invalid`, `access`, `policy-required`). Die Idee, `ezjscnode` durch eine Richtlinie zu schützen, stammt aus der
Distribution LoveStack von Mugo Web (Commit `aa307a7ff3`), mit Dank.

### Seitenweise Ausgabe

Dienste, die Listen liefern, nehmen die Argumente `limit` und `offset` an (`expsystem::phpextensions::10::20` sind 10
Einträge ab 20). Standard und Höchstwert stehen in `expservices.ini [Paging]` (25 und 200).

### Fehler

| Code | Bedeutung |
|---|---|
| 400 | falsche oder fehlende Argumente |
| 401 | Anmeldung erforderlich |
| 403 | keine Richtlinie, kein POST oder ein falsches Formular-Token |
| 404 | kein solcher Dienst, Knoten, kein solches Objekt ... |
| 409 | Konflikt |
| 422 | die Daten sind nicht gültig |
| 500 | der Dienst ist fehlgeschlagen (in der Debug-Ausgabe protokolliert, nie ein fataler PHP-Fehler) |

### Schreibvorgänge

Ein Dienst mit `'write' => true` braucht ein POST, das Formular-Token (Feld `ezxform_token` oder Header
`X-CSRF-Token`; Sie erhalten es von `expsession::token`) und seine Richtlinie; Argumente kommen im POST-Body
(`expServiceBase::post()`), nicht in der URL. Jeder erfolgreiche Schreibvorgang verzeichnet das Audit-Ereignis
`service.<domain>.<method>` (`expAudit`), zusätzlich zu den Ereignissen, die die Kernel-Operation selbst schreibt.
`expservices.ini [Writes]` kann die Prüfung des Tokens (nur für Tests) und das Audit ausschalten. Eine durch ein
API-Token angemeldete Anfrage braucht POST, aber kein Formular-Token (siehe „Authentifizierung mit einem API-Token“).

### Einen Dienst schreiben

    class expFooServices extends expServiceBase
    {
        public static $services = array( 'bar' => array( 'summary' => '...', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '...' ) );

        public static function bar( $args )
        {
            self::guard( __FUNCTION__ );
            $node = self::node( self::arg( $args, 0, 'int' ) );
            return self::ok( array( 'name' => $node->attribute( 'name' ) ) );
        }
    }

Hilfsmethoden: `arg()`, `post()`, `paging()`/`pageOf()`, `ok()`, `page()`, `guard()`, `audit()`, `node()`, `can()`.
Registrieren Sie die Klasse in `ezjscore.ini.append.php` und führen Sie `php bin/php/ezpgenerateautoloads.php -e` aus.

### Eine Deklaration, drei Kanäle

Dieselbe Deklaration bedient HTTP (`ezjscore/call/exp<domain>::<method>`), MCP (die Werkzeuge
`exp_svc_<domain>_<method>`, die expmcp aus dem Katalog erzeugt) und, wenn der Dienst sich dafür entscheidet, den
Assistenten der Erweiterung expai. Ein lesender Dienst entscheidet sich mit `'ai' => true` dafür, oder mit
`'ai' => array( 'summary' => '...' )`, um dem Modell eine eigene Beschreibung zu geben; standardmäßig sind Dienste für
den Assistenten privat, und ein Dienst mit `'write' => true` wird nie angeboten. Der Katalog meldet das Flag als `ai`.
Der Assistent ruft den Dienst im Prozess als angemeldeter Redakteur auf, mit der eigenen Prüfung `access` des
Dienstes, der Richtlinie `ezjscore/call` und der Richtlinie `expai/services` von expai (Einschränkung Domain); die
Datenrichtlinie von expai filtert die Antwort. Hier freigegeben: `expnode::childrenNames`, `childrenCount`,
`breadcrumb`, `tree`, `latest`, `find`; `expsection::list`, `ofNode`; `expstate::groups`, `ofObject`;
`exptags::roots`, `children`, `tree`, `search`, `ofnode`. Eine Website kann ohne Codeänderung weitere in
`expai.ini [ServiceTools]` freigeben (siehe die Dokumentation von expai, `doc/service-tools.md`).

### Einen Aufruf testen

    extension/expservices/bin/expservices-client.sh -u https://www.example.com call expsystem::version   # from a shell
    php vendor/bin/phpunit tests/tests/extension/expservices/

## Domänen

### Katalog (`expservices`, 5 Dienste)

Discovery der API; jeder Aufruf ist öffentlich.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expservices::catalog::<domain>` | public |  | Jeder Dienst jeder Domäne: Domäne, Methode, Zusammenfassung, Zugriff, Schreiben, Argumente, Rückgabe |
| `expservices::schema::<domain>` | public |  | Die Dienste einer Domäne |
| `expservices::service::<domain>::<method>` | public |  | Die Beschreibung eines Dienstes |
| `expservices::domains` | public |  | Die Domänen mit ihrer Klasse und der Zahl ihrer Dienste |
| `expservices::version` | public |  | Die Version von expservices, die Version des Umschlags und die Version von Exponential |

### Sitzung und Authentifizierung (`expsession`, 11 Dienste)

Entfernte Clients melden sich einmal an und behalten das Sitzungs-Cookie, oder sie verwenden ein persönliches
API-Token; siehe die Abschnitte zur Authentifizierung.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expsession::login::<username>::<password>` | public |  | Meldet mit POST username und password an; das Sitzungs-Cookie der Antwort ist die Sitzung |
| `expsession::logout` | user | POST | Meldet ab (POST mit dem Formular-Token) |
| `expsession::whoami` | public |  | Der aktuelle Benutzer: ID, Anmeldename, Name, E-Mail, registriert, Gruppen |
| `expsession::token` | public |  | Das Formular-Token dieser Sitzung für Schreibvorgänge (Feld ezxform_token oder Header X-CSRF-Token) |
| `expsession::ping` | public |  | Hält die Sitzung am Leben und sagt, ob sie angemeldet ist |
| `expsession::access::<module>::<function>` | user |  | Ob der Benutzer module/function hat (accessWord yes, no oder limited) |
| `expsession::roles` | user |  | Die Rollen des Benutzers mit den Richtlinien jeder Rolle (module, function) |
| `expsession::groups` | user |  | Die Benutzergruppen des Benutzers |
| `expsession::tokenCreate` | user | POST | Erzeugt ein persönliches API-Token für den angemeldeten Benutzer (Anmeldung per Sitzung, nicht durch ein anderes Token); `name`, `expires_in_days`. Das Token steht einmal in der Antwort, gespeichert wird nur sein Hash |
| `expsession::tokenList` | user |  | Die eigenen API-Tokens: ID, Name, Hinweis, erzeugt, zuletzt verwendet, Ablauf, widerrufen, gültig (nie das Token) |
| `expsession::tokenRevoke` | user | POST | Widerruft ein eigenes API-Token (`id`) |

### Systeminformationen (`expsystem`, 16 Dienste)

Was die Installation ist und worauf sie läuft; die Geheimnisse der Installation sind nie Teil einer Antwort.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expsystem::version` | public |  | Die Version von Exponential, Release, Zustand und die PHP-Version |
| `expsystem::info` | setup/system_info |  | Name der Website, URLs, Siteaccess, Datenbanktyp, Locale |
| `expsystem::settings` | public |  | Die öffentlichen Einstellungen der Website: Name der Website, Standardzugang, Locale, Design |
| `expsystem::time` | public |  | Die Serverzeit, die Zeitzone und die Zeit der Anfrage |
| `expsystem::php` | setup/system_info |  | PHP-Version, SAPI, Limits und Zustand von OPcache |
| `expsystem::phpextensions::<limit>::<offset>` | setup/system_info |  | Die geladenen PHP-Erweiterungen mit ihren Versionen |
| `expsystem::database` | setup/system_info |  | Datenbanktyp, Name, Serverversion und Verbindungszustand (keine Zugangsdaten) |
| `expsystem::siteaccesses` | setup/system_info |  | Die verfügbaren Siteaccesses |
| `expsystem::siteaccess` | public |  | Der Siteaccess dieser Anfrage |
| `expsystem::languages` | user |  | Die Inhaltssprachen der Installation |
| `expsystem::locales::<limit>::<offset>` | user |  | Die Locales, die die Installation kennt |
| `expsystem::health` | setup/system_info |  | Schnelle Prüfungen: Datenbankverbindung, Verzeichnisse var und Cache beschreibbar |
| `expsystem::directories` | setup/system_info |  | Die Verzeichnisse var, storage und Cache (relativ zur Installation) und ob sie beschreibbar sind |
| `expsystem::statistics` | setup/system_info |  | Anzahlen: Inhaltsobjekte, Knoten, Klassen, Benutzer, Sitzungen |
| `expsystem::load` | setup/system_info |  | Die durchschnittliche Last des Servers und der PHP-Speicherverbrauch dieser Anfrage |
| `expsystem::urls` | public |  | Host, Root-URL, Indexdatei und Server-URL dieser Anfrage |

### Einstellungen (nur lesend) (`expini`, 10 Dienste)

Liest die INI-Einstellungen so, wie der Siteaccess der Anfrage sie sieht. Werte von Geheimnissen (Passwort, Token,
Schlüssel ...) werden genau so maskiert, wie exp:ini sie maskiert.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expini::files::<limit>::<offset>` | setup/setup |  | Die INI-Dateien der Installation (Einstellungen von Kernel, Override und Erweiterungen) |
| `expini::groups::<file>::<limit>::<offset>` | setup/setup |  | Die Gruppen (Blöcke) einer INI-Datei, wie der aktuelle Siteaccess sie liest |
| `expini::variables::<file>::<group>` | setup/setup |  | Die Variablen einer Gruppe mit ihren Werten (Geheimnisse maskiert) |
| `expini::get::<file>::<group>::<variable>` | setup/setup |  | Ein Einstellungswert, wie der aktuelle Siteaccess ihn liest (Geheimnisse maskiert) |
| `expini::has::<file>::<group>::<variable>` | setup/setup |  | Ob eine Gruppe oder eine Variable existiert |
| `expini::search::<file>::<text>::<limit>::<offset>` | setup/setup |  | Findet Variablen, deren Name einen Text enthält, in einer INI-Datei (Geheimnisse maskiert) |
| `expini::siteaccesses` | setup/setup |  | Die Siteaccesses, die eigene Einstellungen haben können |
| `expini::scopes` | setup/setup |  | Die Geltungsbereiche von Einstellungen, die exp:ini kennt (global, Siteaccesses, Erweiterungen) |
| `expini::secret::<variable>` | setup/setup |  | Ob ein Variablenname als Geheimnis behandelt (maskiert) wird |
| `expini::activeextensions` | setup/setup |  | Die aktiven Erweiterungen in der Reihenfolge, in der die Einstellungen sie lesen |

### Caches (`expcache`, 14 Dienste)

Die Dienste zum Leeren sind Schreibvorgänge: POST, das Formular-Token, setup/managecache, ein Audit-Ereignis. Übergeben
Sie dry_run=1, um zu sehen, was geleert würde.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expcache::list::<limit>::<offset>` | setup/managecache |  | Jeder Cache mit seiner ID, seinem Namen, seinen Tags und wie er geleert wird |
| `expcache::tags` | setup/managecache |  | Die Cache-Tags mit den Cache-IDs, die jedes leert |
| `expcache::describe::<id>` | setup/managecache |  | Ein Cache nach ID, mit seinem Pfad und der Größe seiner Dateien |
| `expcache::php` | setup/managecache |  | Die Caches auf PHP-Ebene: Zustand von OPcache und APCu |
| `expcache::velocity` | setup/managecache |  | Antwort-Cache von Velocity: wo er liegt, wie viele Dateien, wann geleert |
| `expcache::http` | setup/managecache |  | Der HTTP-Cache: ob er eingeschaltet ist und sein Status |
| `expcache::staticcache` | setup/managecache |  | Der statische Cache: ob er eingeschaltet ist und sein Status |
| `expcache::query` | setup/managecache |  | Der Zustand des Cache für Datenbankabfragen |
| `expcache::clear::<by>::<names>::<dry_run>` | setup/managecache | POST | Leert Caches: POST by=all/tag/id, names=kommagetrennte Liste, dry_run=1, um nur zu berichten |
| `expcache::cleartag::<tag>::<dry_run>` | setup/managecache | POST | Leert die Caches eines Tags (POST tag, dry_run) |
| `expcache::clearid::<id>::<dry_run>` | setup/managecache | POST | Leert einen Cache nach ID (POST id, dry_run) |
| `expcache::clearnode::<node_id>` | setup/managecache | POST | Leert den View-Cache eines Knotens (POST node_id) |
| `expcache::clearvelocity::<dry_run>` | setup/managecache | POST | Leert den Antwort-Cache von Velocity (POST dry_run) |
| `expcache::clearopcache::<dry_run>` | setup/managecache | POST | Setzt OPcache dieses PHP-Prozess-Pools zurück (POST dry_run) |

### Cronjobs (`expcronjob`, 7 Dienste)

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expcronjob::parts` | setup/managecronjobs |  | Die Cronjob-Teile (Gruppen, die runcronjobs.php <part> ausführt) mit der Zahl ihrer Skripte |
| `expcronjob::scripts::<part>` | setup/managecronjobs |  | Die Skripte eines Cronjob-Teils |
| `expcronjob::all::<limit>::<offset>` | setup/managecronjobs |  | Jeder Teil mit seinen Skripten |
| `expcronjob::settings` | setup/managecronjobs |  | Die Cronjob-Einstellungen: Skriptverzeichnisse, Zeitlimit der Ausführung, Einstellungen der Admin-Konsole |
| `expcronjob::status` | setup/managecronjobs |  | Die Logdateien der Cronjobs: ob sie existieren, ihre Größe und letzte Änderung |
| `expcronjob::deferred` | setup/managecronjobs |  | Die an Cron übergebenen Workflow-Prozesse: wie viele warten |
| `expcronjob::runnables::<limit>::<offset>` | setup/managecronjobs |  | Die als Runnables implementierten Cronjobs (Kernel und Erweiterungen) |

### Erweiterungen (`expextension`, 9 Dienste)

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expextension::list::<limit>::<offset>` | setup/system_info |  | Die aktiven Erweiterungen mit Version, Lizenz und Website, soweit bekannt |
| `expextension::available::<limit>::<offset>` | setup/system_info |  | Die in den Erweiterungsverzeichnissen gefundenen Erweiterungen (aktiv oder nicht) |
| `expextension::info::<name>` | setup/system_info |  | Die ezinfo einer Erweiterung: Name, Version, Autor, Lizenz, Website |
| `expextension::isactive::<name>` | setup/system_info |  | Ob eine Erweiterung aktiv ist |
| `expextension::accessextensions` | setup/system_info |  | Die für den aktuellen Siteaccess aktiven Erweiterungen (ActiveAccessExtensions) |
| `expextension::designextensions` | setup/system_info |  | Die Erweiterungen, die Designs liefern (DesignExtensions) |
| `expextension::settingsfiles::<name>` | setup/system_info |  | Die Einstellungsdateien, die eine Erweiterung mitliefert |
| `expextension::directories` | setup/system_info |  | Die Wurzelverzeichnisse der Erweiterungen |
| `expextension::count` | setup/system_info |  | Die Zahl der aktiven und der verfügbaren Erweiterungen |

### Pakete (`exppackage`, 7 Dienste)

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `exppackage::list::<limit>::<offset>::<type>::<repository>` | package/list |  | Die Pakete: Name, Version, Anbieter, Typ, Zusammenfassung, installiert |
| `exppackage::view::<name>::<repository>` | package/read |  | Ein Paket: Beschreibung, Anbieter, Betreuer, Abhängigkeiten, Zustand |
| `exppackage::files::<name>::<limit>::<offset>` | package/read |  | Die Zahl der Dateien und die einfache Dateiliste eines Pakets |
| `exppackage::types` | package/list |  | Die Pakettypen |
| `exppackage::states` | package/list |  | Die Paketzustände |
| `exppackage::repositories` | package/list |  | Die Paket-Repositorys |
| `exppackage::count` | package/list |  | Die Zahl der Pakete und der installierten Pakete |

### Workflows und Trigger (`expworkflow`, 10 Dienste)

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expworkflow::list::<limit>::<offset>` | setup/administrate |  | Die Workflows (veröffentlichte Versionen) |
| `expworkflow::view::<id>` | setup/administrate |  | Ein Workflow mit seinen Ereignissen |
| `expworkflow::events::<id>` | setup/administrate |  | Die Ereignisse eines Workflows in ihrer Reihenfolge |
| `expworkflow::eventtypes` | setup/administrate |  | Die registrierten Workflow-Ereignistypen |
| `expworkflow::groups` | setup/administrate |  | Die Workflow-Gruppen |
| `expworkflow::triggers::<limit>::<offset>::<module>` | setup/administrate |  | Die Trigger: Modul, Funktion, Verbindungstyp und der Workflow |
| `expworkflow::trigger::<id>` | setup/administrate |  | Ein Trigger |
| `expworkflow::processes::<limit>::<offset>` | setup/administrate |  | Die laufenden Workflow-Prozesse |
| `expworkflow::process::<id>` | setup/administrate |  | Ein Workflow-Prozess |
| `expworkflow::statuses` | setup/administrate |  | Die Statuscodes und -namen der Workflows |

### Velocity (`expvelocity`, 5 Dienste)

Pfade des Servers bleiben auf dem Server und sind nicht Teil der Antworten.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expvelocity::status::<engine>` | setup/system_info |  | Ob Velocity läuft: Prozesse, lauschende Ports, Engine |
| `expvelocity::engines` | setup/system_info |  | Die Engines, die Velocity kennt, und die konfigurierte Standard-Engine |
| `expvelocity::urls` | setup/system_info |  | Die URLs, unter denen der Server antwortet |
| `expvelocity::cache` | setup/system_info |  | Der Antwort-Cache von Velocity: Dateien, Bytes, zuletzt geleert |
| `expvelocity::installed` | setup/system_info |  | Ob Velocity Teil dieser Installation ist |

### Übersicht der Erweiterungspunkte (rad) (`exprad`, 7 Dienste)

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `exprad::summary` | setup/system_info |  | Die Zahlen der Übersicht: INI-Dateien, Einstellungen, Module, Operatoren, Runnables ... |
| `exprad::counts::<name>` | setup/system_info |  | Eine Zahl der Übersicht nach Name |
| `exprad::groups` | setup/system_info |  | Die Gruppen der Erweiterungspunkte mit ihren Zahlen |
| `exprad::runnables::<limit>::<offset>::<kind>` | setup/system_info |  | Die Runnables (Befehle, Cronjobs, Views) mit Art und Besitzer |
| `exprad::files::<limit>::<offset>` | setup/system_info |  | Die INI-Dateien, die die Übersicht liest |
| `exprad::inicommand` | setup/system_info |  | Die Aktionen und Geltungsbereich-Provider von exp:ini |
| `exprad::contentjobtypes` | setup/system_info |  | Die Typen von Inhaltsjobs |

### Debug-Zusammenfassung (`expdebug`, 6 Dienste)

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expdebug::summary` | setup/setup |  | Zeit, SQL, Speicher, Templates und Warnungen dieser Anfrage mit ihren Stufen (die Zusammenfassung der Debug-Leiste) |
| `expdebug::enabled` | setup/setup |  | Ob die Debug-Ausgabe für diese Anfrage eingeschaltet ist |
| `expdebug::level` | setup/setup |  | Die Einstellungen der Debug-Stufe der Website |
| `expdebug::thresholds` | setup/setup |  | Die Schwellen für Warnung und hoch der Debug-Leiste |
| `expdebug::engine` | user |  | Welche Engine diese Anfrage ausliefert (Apache, Velocity, FrankenPHP, CLI) |
| `expdebug::phpversion` | user |  | Die PHP-Version und SAPI dieser Anfrage |


### Medien und weitere Dienste: 218 Dienste in 14 Domänen

Geschrieben vom Teil Medien/Sonstiges von expservices: `extension/expservices/classes/media/` (Bilder, Dateien, Medien,
Tags) und `classes/misc/` (Layouts, Audit, Unterelemente, Newsletter, Sitemaps, Statistiken, Designs, Sprachen, Links,
PDF). Domänen von Erweiterungen antworten mit 404 `... is not available`, wenn die Erweiterung inaktiv ist. Tests:
`tests/tests/extension/expservices/media/` und `misc/` (Live-Datenbank, Testinhalte unter dem Wurzelknoten Media 43,
die in tearDown entfernt werden, nie eine Testdatenbank); die Dienste wurden außerdem über HTTP geprüft.

### Bilder und Aliase (`expimage`, 18 Dienste)

Bild-Aliase (image.ini), Informationen zum Originalbild und die URL jedes Alias eines Knotenbildes. Lesevorgänge
folgen content/read auf dem Knoten; `purge` braucht content/edit.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expimage::aliases` | public |  | Die konfigurierten Bild-Aliase (image.ini AliasList) mit Größe und Filtern |
| `expimage::alias::<name>` | public |  | Ein konfigurierter Bild-Alias |
| `expimage::aliasnames` | public |  | Nur die Namen der Aliase |
| `expimage::formats` | public |  | Die Bild-MIME-Typen, die die Konverter unterstützen |
| `expimage::filters` | public |  | Die Bildfilter (image.ini [ImageMagick]/[GD]), die der Manager kennt |
| `expimage::quality` | public |  | Die konfigurierte Ausgabequalität pro MIME-Typ |
| `expimage::attributes::<node>` | content/read |  | Die Bildattribute eines Knotens mit ihrer Originaldatei |
| `expimage::info::<node>::<attribute>` | content/read |  | Das Originalbild eines Knotenattributs: Dateiname, Größe, Abmessungen, MIME-Typ, Alternativtext |
| `expimage::url::<node>::<attribute>::<alias>` | content/read |  | Die URL eines Alias eines Knotenbildes (erzeugt, wenn er fehlt) |
| `expimage::urls::<node>::<attribute>` | content/read |  | Die URLs aller konfigurierten Aliase eines Knotenbildes |
| `expimage::generate::<node>::<attribute>::<alias>` | content/read |  | Stellt sicher, dass für ein Knotenbild eine Alias-Datei existiert (abgeleitete Daten, keine Änderung am Inhalt) |
| `expimage::alt::<node>::<attribute>` | content/read |  | Der Alternativtext eines Knotenbildes |
| `expimage::files::<node>::<limit>::<offset>` | content/read |  | Die für das Objekt eines Knotens registrierten Bilddateien (ezimagefile) |
| `expimage::list::<parent>::<limit>::<offset>::<alias>` | content/read |  | Die Bildobjekte unter einem Elternknoten mit ihrem Original und einem Alias |
| `expimage::search::<text>::<parent>::<limit>::<offset>` | content/read |  | Bildobjekte nach Name unter einem Elternknoten |
| `expimage::stats` | content/read |  | Wie viele Bilddateien und Bilder die Installation enthält |
| `expimage::bymime` | content/read |  | Anzahl der Bilddateien pro MIME-Typ der Originaldateien |
| `expimage::purge::<node>::<attribute>` | content/edit | POST | Entfernt die Alias-Dateien eines Knotenbildes (sie werden bei Bedarf neu erzeugt) |

### Dateien und Downloads (`expfile`, 16 Dienste)

Binärdateien: Informationen, die URL von `content/download` (der Download selbst prüft content/read), zeitlich
begrenzte signierte Links (HMAC über Knoten, Attribut und Ablauf mit dem Geheimnis der Website; `verify` ist
öffentlich) und Download-Statistiken.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expfile::attributes::<node>` | content/read |  | Die Dateiattribute eines Knotens |
| `expfile::info::<node>::<attribute>` | content/read |  | Eine Datei eines Knotens: Namen, MIME-Typ, Größe, Zahl der Downloads |
| `expfile::downloadurl::<node>::<attribute>` | content/read |  | Die URL von content/download für eine Knotendatei (der Download selbst prüft content/read) |
| `expfile::signedurl::<node>::<attribute>::<seconds>` | content/read |  | Ein zeitlich begrenzter signierter Link auf eine Knotendatei, für Clients ohne Sitzung |
| `expfile::verify::<node>::<attribute_id>::<expires>::<signature>` | public |  | Ob ein signierter Link echt und nicht abgelaufen ist |
| `expfile::exists::<node>::<attribute>` | content/read |  | Ob die gespeicherte Datei eines Knotenattributs auf der Festplatte existiert |
| `expfile::downloads::<node>::<attribute>` | content/read |  | Die Zahl der Downloads einer Knotendatei |
| `expfile::list::<parent>::<limit>::<offset>` | content/read |  | Die Dateiobjekte unter einem Elternknoten mit ihren Dateiinformationen |
| `expfile::search::<text>::<parent>::<limit>::<offset>` | content/read |  | Dateiobjekte nach Name unter einem Elternknoten |
| `expfile::byname::<filename>::<limit>::<offset>` | content/read |  | Die gespeicherten Dateien mit einem ursprünglichen oder gespeicherten Dateinamen (exakt) |
| `expfile::top::<limit>` | content/read |  | Die am häufigsten heruntergeladenen Dateien |
| `expfile::stats` | content/read |  | Zahl der Dateien, Downloads insgesamt und Anzahlen pro MIME-Gruppe |
| `expfile::bymime` | content/read |  | Anzahl der Dateien pro MIME-Typ |
| `expfile::mimegroup::<mime>` | public |  | Die MIME-Gruppe (application, image, ...) eines MIME-Typs |
| `expfile::safename::<filename>` | user |  | Ob ein Dateiname für einen Upload zulässig ist, und seine sichere Form |
| `expfile::resetdownloads::<node>::<attribute>` | content/edit | POST | Setzt die Zahl der Downloads einer Knotendatei auf null zurück |

### Audio und Video (`expmedia`, 10 Dienste)

Der Datentyp ezmedia: Einstellungen des Players, Dateiinformationen, URL, Auflistungen und Statistiken.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expmedia::attributes::<node>` | content/read |  | Die Medienattribute eines Knotens |
| `expmedia::info::<node>::<attribute>` | content/read |  | Eine Mediendatei eines Knotens: Datei, MIME-Typ, Abmessungen und Flags des Players |
| `expmedia::player::<node>::<attribute>` | content/read |  | Nur die Einstellungen des Players (Breite, Höhe, Steuerelemente, automatisches Abspielen, Wiederholung, Qualität) |
| `expmedia::url::<node>::<attribute>` | content/read |  | Die Download-/Stream-URL einer Mediendatei eines Knotens |
| `expmedia::mimegroup::<mime>` | public |  | Die MIME-Gruppe (audio, video, ...) eines MIME-Typs |
| `expmedia::safename::<filename>` | user |  | Ob der Name einer Mediendatei zulässig ist, und seine sichere Form |
| `expmedia::list::<parent>::<limit>::<offset>` | content/read |  | Die Medienobjekte unter einem Elternknoten |
| `expmedia::byobject::<object>` | content/read |  | Die für ein Objekt gespeicherten Medienzeilen (alle Versionen) |
| `expmedia::stats` | content/read |  | Zahl der Mediendateien, insgesamt und pro MIME-Gruppe |
| `expmedia::bymime` | content/read |  | Anzahl der Mediendateien pro MIME-Typ |

### Tags (`exptags`, 27 Dienste)

Der Baum von eztags mit den Richtlinien von eztags (tags/read, add, edit, delete, addsynonym). Antwortet mit 404 „not
available“, wenn eztags inaktiv ist. `attach` und `detach` ändern das Tag-Attribut eines Knotens (content/edit auf dem
Knoten).

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `exptags::available` | public |  | Ob die Erweiterung eztags aktiv ist und wie viele Tags es gibt |
| `exptags::roots::<limit>::<offset>` | tags/read |  | Die Tags der obersten Ebene |
| `exptags::get::<id>` | tags/read |  | Ein Tag mit Anzahlen |
| `exptags::children::<id>::<limit>::<offset>` | tags/read |  | Die Kinder eines Tags |
| `exptags::childrencount::<id>` | tags/read |  | Wie viele Kinder ein Tag hat |
| `exptags::tree::<id>::<depth>` | tags/read |  | Ein Teilbaum eines Tags als verschachtelte Knoten bis zu einer Tiefe (höchstens 5) |
| `exptags::path::<id>` | tags/read |  | Der Pfad eines Tags ab der Wurzel |
| `exptags::parent::<id>` | tags/read |  | Das Eltern-Tag eines Tags |
| `exptags::search::<text>::<limit>::<offset>` | tags/read |  | Tags, deren Schlüsselwort den Text enthält |
| `exptags::suggest::<text>::<limit>` | tags/read |  | Autovervollständigung: Tags, deren Schlüsselwort mit dem Text beginnt |
| `exptags::bykeyword::<keyword>` | tags/read |  | Tags mit genau diesem Schlüsselwort |
| `exptags::byremote::<remote>` | tags/read |  | Das Tag mit einer Remote-ID |
| `exptags::bypath::<path>` | tags/read |  | Das Tag unter einer Pfadzeichenkette (/1/5/7/) |
| `exptags::synonyms::<id>` | tags/read |  | Die Synonyme eines Tags |
| `exptags::translations::<id>` | tags/read |  | Die Übersetzungen (Schlüsselwort pro Locale) eines Tags |
| `exptags::related::<id>::<limit>::<offset>` | tags/read |  | Die mit einem Tag versehenen Objekte, die der Benutzer lesen darf |
| `exptags::relatedcount::<id>` | tags/read |  | Wie viele veröffentlichte Objekte ein Tag tragen |
| `exptags::ofnode::<node>` | content/read |  | Die einem Knoten zugeordneten Tags |
| `exptags::popular::<limit>` | tags/read |  | Die am häufigsten verwendeten Tags |
| `exptags::recent::<limit>` | tags/read |  | Die zuletzt geänderten Tags |
| `exptags::stats` | tags/read |  | Anzahlen von Tags, Synonymen, Übersetzungen und Zuordnungen |
| `exptags::add::<parent>` | tags/add | POST | Erzeugt ein Tag unter einem Eltern-Tag (0: oberste Ebene) |
| `exptags::rename::<id>` | tags/edit | POST | Ändert das Schlüsselwort eines Tags in einer Locale |
| `exptags::addsynonym::<id>` | tags/addsynonym | POST | Fügt einem Tag ein Synonym hinzu |
| `exptags::delete::<id>` | tags/delete | POST | Löscht ein Tag mit seinen Kindern und Synonymen |
| `exptags::attach::<node>::<attribute>` | content/edit | POST | Ordnet einem Tag-Attribut eines Knotens ein vorhandenes Tag zu |
| `exptags::detach::<node>::<attribute>` | content/edit | POST | Entfernt ein Tag aus einem Tag-Attribut eines Knotens |

### Layouts (`explayout`, 27 Dienste)

explayouts: Layouts, Zonen (eine verknüpfte Zone antwortet mit den Blöcken ihres gemeinsamen Layouts), Blöcke, Regeln
und das Layout, auf das ein Knoten oder Pfad aufgelöst wird (berechnet, ohne den Cache des Resolvers anzutasten).
Richtlinien explayouts/read und explayouts/edit; die Schreibvorgänge verwenden den Layout-Dienst von explayouts_core.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `explayout::available` | public |  | Ob die Erweiterung explayouts aktiv ist, und die Zahl der veröffentlichten Layouts |
| `explayout::layouts::<limit>::<offset>::<status>` | explayouts/read |  | Die veröffentlichten Layouts |
| `explayout::shared` | explayouts/read |  | Die gemeinsamen Layouts (Header, Footer, ...) |
| `explayout::drafts` | explayouts/read |  | Die Entwürfe von Layouts |
| `explayout::view::<id>` | explayouts/read |  | Ein Layout mit seinen Zonen und der Zahl ihrer Blöcke |
| `explayout::byidentifier::<identifier>::<status>` | explayouts/read |  | Ein Layout nach Kennung (veröffentlicht, außer status ist 1) |
| `explayout::types` | explayouts/read |  | Die Layout-Typen (explayouts.ini LayoutType_*) mit ihren Zonen |
| `explayout::blocktypes::<category>` | explayouts/read |  | Die Block-Definitionen (explayouts.ini BlockDefinition_*) |
| `explayout::zones::<id>` | explayouts/read |  | Die Zonen eines Layouts, mit ihrer Verknüpfung zu einem gemeinsamen Layout |
| `explayout::zone::<id>::<zone>` | explayouts/read |  | Eine Zone eines Layouts |
| `explayout::blocks::<id>::<zone>` | explayouts/read |  | Die Blöcke einer Zone (eine verknüpfte Zone antwortet mit den Blöcken des gemeinsamen Layouts) |
| `explayout::block::<id>` | explayouts/read |  | Ein Block mit seinen Parametern |
| `explayout::blockchildren::<id>` | explayouts/read |  | Die Kind-Blöcke eines Container-Blocks |
| `explayout::blockparameters::<id>` | explayouts/read |  | Die Parameter eines Blocks als name => value |
| `explayout::linkedzones` | explayouts/read |  | Jede Zone, die ihre Blöcke von einem gemeinsamen Layout erbt |
| `explayout::rules::<limit>::<offset>` | explayouts/read |  | Die Layout-Regeln in der Reihenfolge ihrer Priorität |
| `explayout::rule::<id>` | explayouts/read |  | Eine Regel mit ihren Zielen und Bedingungen |
| `explayout::rulesfor::<id>` | explayouts/read |  | Die Regeln, die ein Layout anzeigen |
| `explayout::resolve::<node>` | explayouts/read |  | Das Layout, das ein Inhaltsknoten erhält (erste passende eingeschaltete Regel, sonst der Standard); ändert nichts |
| `explayout::resolvepath::<path>` | explayouts/read |  | Das Layout, das ein URL-Pfad erhält |
| `explayout::stats` | explayouts/read |  | Anzahlen von Layouts, Zonen, Blöcken, Regeln und verwendeten Block-Typen |
| `explayout::createdraft::<id>` | explayouts/edit | POST | Erzeugt den Entwurf eines veröffentlichten Layouts (oder gibt ihn zurück) |
| `explayout::publish::<id>` | explayouts/edit | POST | Veröffentlicht den Entwurf eines Layouts |
| `explayout::discard::<id>` | explayouts/edit | POST | Verwirft den Entwurf eines Layouts |
| `explayout::enablerule::<id>` | explayouts/edit | POST | Schaltet eine Regel ein |
| `explayout::disablerule::<id>` | explayouts/edit | POST | Schaltet eine Regel aus |
| `explayout::clearcache` | explayouts/edit | POST | Leert den Cache des Layout-Resolvers |

### Audit (nur lesend) (`expaudit`, 19 Dienste)

Richtlinie audit/read, eingeschränkt auf die Kanäle ihrer Einschränkung Channel; jede Verwendung wird als
`system.audit.read` verzeichnet. Zahlen brauchen den Audit-Index (409, wenn er nicht verwendbar ist). Nichts hier
prüft, exportiert oder ändert das Audit.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expaudit::available` | public |  | Ob die Audit-Klassen geladen sind, ob das Audit eingeschaltet und der Index verwendbar ist |
| `expaudit::channels` | audit/read |  | Die Kanäle, die der Benutzer lesen darf, mit der Zahl der Einträge von heute |
| `expaudit::recent::<channel>::<limit>::<offset>` | audit/read |  | Die neuesten Ereignisse, die neuesten zuerst, optional eines Kanals |
| `expaudit::search::<q>::<name>::<login>::<result>::<from>::<to>::<limit>::<offset>` | audit/read |  | Ereignisse nach Text, Namensmuster (access.*), Anmeldename, Ergebnis und Zeitraum |
| `expaudit::event::<id>` | audit/read |  | Ein Ereignis vollständig nach ID |
| `expaudit::related::<id>::<limit>` | audit/read |  | Die Ereignisse derselben Anfrage wie ein Ereignis |
| `expaudit::byrequest::<request>::<limit>::<offset>` | audit/read |  | Die Ereignisse einer Anfrage-ID |
| `expaudit::bylogin::<login>::<limit>::<offset>` | audit/read |  | Die Ereignisse eines Anmeldenamens |
| `expaudit::byobject::<object>::<limit>::<offset>` | audit/read |  | Die Ereignisse zu einem Objekt (Typ oder Typ:ID, node:275) |
| `expaudit::refused::<limit>::<offset>` | audit/read |  | Die neuesten abgelehnten Ereignisse |
| `expaudit::failed::<limit>::<offset>` | audit/read |  | Die neuesten fehlgeschlagenen Ereignisse |
| `expaudit::names` | audit/read |  | Die Ereignisnamen im Index (die Vorschläge für den Namensfilter) |
| `expaudit::volume` | audit/read |  | Ereignisse von heute und der letzten 7 Tage pro Kanal |
| `expaudit::perday::<days>` | audit/read |  | Ereignisse pro lokalem Tag für die letzten N Tage (höchstens 30) |
| `expaudit::results` | audit/read |  | Anzahlen von Erfolg, abgelehnt und fehlgeschlagen für heute und die Woche |
| `expaudit::topactors::<limit>` | audit/read |  | Die aktivsten Anmeldenamen von heute |
| `expaudit::failedlogins` | audit/read |  | Fehlgeschlagene Anmeldungen der letzten 24 Stunden: insgesamt und nach Adresse |
| `expaudit::chains` | audit/read |  | Der Zustand der Hash-Kette jedes Kanals bei der letzten Prüfung (hier wird nichts geprüft) |
| `expaudit::summary` | audit/read |  | Die Zahlen des Dashboards in einer Antwort: Volumen, Ergebnisse, Sicherheit, Ketten |

### Spalten der Unterelemente (`expsubitems_svc`, 12 Dienste)

Der Spaltenkatalog und die Zeilen der Liste der Unterelemente in der Administration im Umschlag von expservices. Der
Block heißt `expsubitems_svc`, weil `expsubitems` die ursprüngliche Server-Funktion von ezjscore für die Liste der
Administration ist, die bleibt, wie sie ist. `rows` nimmt die Spalten als Argument an (kommagetrennte Liste von
Schlüsseln nicht eingebauter Spalten).

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expsubitems_svc::catalogue` | content/read |  | Jede der Registry bekannte Spalte (eingebaut und per INI definiert), ohne die Attributspalten pro Elternknoten |
| `expsubitems_svc::builtin` | content/read |  | Die eingebauten Spalten |
| `expsubitems_svc::describe::<key>::<parent>` | content/read |  | Eine Spalte nach Schlüssel |
| `expsubitems_svc::columns::<parent>` | content/read |  | Die Spalten, die der Benutzer unter einem Elternknoten sehen darf, Standardwerte, Vorlagen und die gespeicherte Auswahl |
| `expsubitems_svc::defaults::<parent>` | content/read |  | Die Standard-Spaltenschlüssel unter einem Elternknoten |
| `expsubitems_svc::presets` | content/read |  | Die Spaltenvorlagen der INI |
| `expsubitems_svc::pagesizes` | content/read |  | Die Seitengrößen, die die Liste anbietet |
| `expsubitems_svc::sortfields::<parent>` | content/read |  | Die Sortierschlüssel, die eine Liste akzeptiert |
| `expsubitems_svc::groups::<parent>` | content/read |  | Die Spalten, gruppiert nach ihrer Group unter einem Elternknoten |
| `expsubitems_svc::preference::<parent>` | content/read |  | Die eigene gespeicherte Spaltenauswahl des Benutzers für den Navigationsteil des Elternknotens |
| `expsubitems_svc::rows::<parent>::<columns>::<limit>::<offset>::<sort>::<ascending>::<filter>` | content/read |  | Die Kinder eines Elternknotens mit den angeforderten Spalten (kommagetrennte Liste) |
| `expsubitems_svc::settings` | content/read |  | Die Einstellungen der Registry: Attributspalten, CSV-Export, CSV-Limit |

### Newsletter (`expnewsletter`, 13 Dienste)

cjw_newsletter: Listen, Ausgaben, Abonnentenzahlen und die eigenen Abonnements eines Benutzers. `subscribe` und
`unsubscribe` wirken nur auf die E-Mail-Adresse des eigenen Kontos des angemeldeten Benutzers; ein neues Abonnement
beginnt als ausstehend und folgt den Bestätigungsregeln der Erweiterung. 404 „not available“, wenn die Erweiterung
inaktiv ist.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expnewsletter::available` | public |  | Ob cjw_newsletter aktiv ist und wie viele Listen und Newsletter-Benutzer es gibt |
| `expnewsletter::statuses` | public |  | Die Statuscodes und -namen der Abonnements |
| `expnewsletter::lists::<limit>::<offset>` | content/read |  | Die Newsletter-Listen, die der Benutzer lesen darf |
| `expnewsletter::list::<node>` | content/read |  | Eine Newsletter-Liste nach Knoten-ID |
| `expnewsletter::subscriberscount::<node>` | content/read |  | Die Zahl der bestätigten Abonnenten einer Liste |
| `expnewsletter::statistics::<node>` | newsletter/subscription_list |  | Abonnements einer Liste nach Status |
| `expnewsletter::editions::<node>::<limit>::<offset>` | content/read |  | Die Ausgaben unter einem Listenknoten |
| `expnewsletter::edition::<node>` | content/read |  | Eine Ausgabe nach Knoten-ID |
| `expnewsletter::mysubscriptions` | user |  | Die eigenen Abonnements des angemeldeten Benutzers (nach der E-Mail-Adresse seines Kontos) |
| `expnewsletter::subscribe::<node>` | user | POST | Abonniert eine Liste mit der eigenen E-Mail-Adresse des angemeldeten Benutzers |
| `expnewsletter::unsubscribe::<node>` | user | POST | Entfernt das eigene Abonnement einer Liste des angemeldeten Benutzers |
| `expnewsletter::users::<limit>::<offset>::<search>` | newsletter/user_list |  | Die Newsletter-Benutzer (Administration) |
| `expnewsletter::usercount` | newsletter/user_list |  | Die Zahl der Newsletter-Benutzer |

### Sitemaps (`expsitemap`, 11 Dienste)

Einträge des Inhaltsbaums so, wie eine Sitemap sie auflistet (loc, lastmod, changefreq, priority; Einstellungen aus
bcgooglesitemaps.ini), der Sitemap-Index und die erzeugten Dateien.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expsitemap::config` | content/read |  | Die Einstellungen der Sitemap (Wurzelknoten, Protokoll, Dateiname, Klassenfilter) |
| `expsitemap::available` | public |  | Ob die Sitemap-Erweiterungen aktiv sind |
| `expsitemap::rootnode` | content/read |  | Der Knoten, bei dem die Sitemap beginnt |
| `expsitemap::entries::<node>::<limit>::<offset>` | content/read |  | Sitemap-Einträge eines Teilbaums: loc, lastmod, changefreq, priority (mit dem Klassenfilter der Einstellungen) |
| `expsitemap::count::<node>` | content/read |  | Wie viele Einträge ein Teilbaum hat |
| `expsitemap::entry::<node>` | content/read |  | Der Sitemap-Eintrag eines Knotens |
| `expsitemap::recent::<node>::<limit>` | content/read |  | Die zuletzt geänderten Einträge (eine News-Sitemap) |
| `expsitemap::index::<node>` | content/read |  | Der Sitemap-Index: die Seiten mit 50000 Einträgen mit ihrem Eintragsfenster |
| `expsitemap::files` | setup/administrate |  | Die erzeugten Sitemap-Dateien (Name, Größe, geändert) |
| `expsitemap::classes` | content/read |  | Die Klassen, die die Sitemap ein- oder ausschließt |
| `expsitemap::robots` | public |  | Die Sitemap-Zeile der robots.txt für die erzeugte Sitemap |

### Statistiken (`expstats`, 16 Dienste)

Suchbegriffe, Inhaltszahlen, Wachstum, Sprachen, Benutzer und Sitzungen. Inhaltszahlen folgen content/read, der Rest
braucht setup/administrate.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expstats::searchtop::<limit>::<offset>` | setup/administrate |  | Die häufigsten Suchbegriffe mit ihrer durchschnittlichen Trefferzahl |
| `expstats::searchtotal` | setup/administrate |  | Zahl der verschiedenen Suchbegriffe und der Suchen |
| `expstats::searchnoresults::<limit>` | setup/administrate |  | Suchbegriffe, die nichts gefunden haben |
| `expstats::searchphrase::<phrase>` | setup/administrate |  | Die Statistik eines Suchbegriffs |
| `expstats::contenttotals` | content/read |  | Anzahlen von Objekten, Knoten, Versionen und Klassen |
| `expstats::classcounts::<limit>` | content/read |  | Veröffentlichte Objekte pro Klasse |
| `expstats::sectioncounts` | content/read |  | Veröffentlichte Objekte pro Sektion |
| `expstats::statecounts` | content/read |  | Objekte pro Objektzustand |
| `expstats::recentpublished::<limit>` | content/read |  | Die zuletzt veröffentlichten Objekte |
| `expstats::recentmodified::<limit>` | content/read |  | Die zuletzt geänderten Objekte |
| `expstats::growth::<days>` | content/read |  | Veröffentlichte Objekte pro Tag für die letzten N Tage (höchstens 90) |
| `expstats::topowners::<limit>` | setup/administrate |  | Die Benutzer, denen die meisten veröffentlichten Objekte gehören |
| `expstats::languagecounts` | content/read |  | Veröffentlichte Übersetzungen von Objekten pro Sprache |
| `expstats::users` | setup/administrate |  | Zahl der Benutzerkonten, aktiviert und deaktiviert |
| `expstats::sessions` | setup/administrate |  | Aktive Sitzungen (nicht abgelaufen) und wie viele davon angemeldete Benutzer sind |
| `expstats::overview` | content/read |  | Die wichtigsten Zahlen in einer Antwort |

### Designs und Templates (`expdesign`, 13 Dienste)

Nur lesend, setup/administrate: Designs, Basen, die Overrides aus override.ini, die Templates, die ein Design auflöst,
und der Quelltext eines Templates in einem Design-Verzeichnis (Pfad geprüft, höchstens 512 KB).

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expdesign::designs` | setup/administrate |  | Die Design-Verzeichnisse der Website und der Erweiterungen |
| `expdesign::current` | setup/administrate |  | Das Standard-Design, das Site-Design und die zusätzlichen Designs dieses Siteaccess |
| `expdesign::bases` | setup/administrate |  | Die Design-Basen in der Reihenfolge ihrer Priorität (die erste gewinnt) |
| `expdesign::extensions` | setup/administrate |  | Die Erweiterungen, die Designs für diesen Siteaccess liefern |
| `expdesign::overrides::<limit>::<offset>::<source>` | setup/administrate |  | Die Template-Overrides aus override.ini |
| `expdesign::override::<name>` | setup/administrate |  | Ein Override nach Name |
| `expdesign::overridecount` | setup/administrate |  | Wie viele Overrides es gibt |
| `expdesign::overridesfor::<source>` | setup/administrate |  | Die Overrides einer Template-Quelle (node/view/full.tpl) |
| `expdesign::templates::<limit>::<offset>::<prefix>` | setup/administrate |  | Die Templates des aufgelösten Designs, mit der Design-Basis, aus der jedes stammt |
| `expdesign::templatecount` | setup/administrate |  | Wie viele Templates das Design auflöst |
| `expdesign::resolve::<template>` | setup/administrate |  | Auf welche Datei ein Template-Pfad aufgelöst wird, und seine Overrides |
| `expdesign::source::<path>` | setup/administrate |  | Der Quelltext eines Templates eines Design-Verzeichnisses (design/<name>/templates/..., nur lesend) |
| `expdesign::cachestate` | setup/administrate |  | Einstellungen des Template-Caches: Kompilieren, Cache, Override-Cache und Cache der Design-Orte |

### Sprachen und Übersetzungen (`explanguage`, 14 Dienste)

Inhaltssprachen, Sprachmasken, Locales und der Fortschritt der Übersetzungen der Oberfläche in share/translations.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `explanguage::languages` | public |  | Die Inhaltssprachen der Installation mit der Zahl ihrer Objekte |
| `explanguage::language::<locale>` | public |  | Eine Inhaltssprache nach Locale |
| `explanguage::prioritized` | public |  | Die Sprachen in der Prioritätsreihenfolge dieses Siteaccess |
| `explanguage::top` | public |  | Die Sprache mit der höchsten Priorität in diesem Siteaccess |
| `explanguage::decode::<mask>` | public |  | Die Sprachen in einer Sprachmaske |
| `explanguage::maskfor::<locales>::<always_available>` | public |  | Die Sprachmaske einiger Locales |
| `explanguage::locales::<limit>::<offset>` | public |  | Die Locales, die das System kennt (share/locale) |
| `explanguage::localeinfo::<locale>` | public |  | Namen, Formate und Währung einer Locale |
| `explanguage::countries` | public |  | Die den Locales bekannten Ländercodes |
| `explanguage::translations` | user |  | Die in share/translations vorhandenen Übersetzungen der Oberfläche und ihre Größe |
| `explanguage::translationstats::<locale>` | user |  | Meldungen, fertig, unfertig und veraltet einer Übersetzung der Oberfläche |
| `explanguage::translationcontexts::<locale>::<limit>::<offset>` | user |  | Die Übersetzungskontexte (Dateien) einer Übersetzung der Oberfläche mit der Zahl ihrer Meldungen |
| `explanguage::siteaccess` | public |  | Die Spracheinstellungen des aktuellen Siteaccess |
| `explanguage::objectcounts` | content/read |  | Veröffentlichte Objektnamen pro Sprache |

### Externe Links (`expurl`, 12 Dienste)

Die von URL-Attributen gespeicherten Links: Listen, Suche, Gültigkeit, die Objekte, die einen Link verwenden. `check`
fragt einen gespeicherten Link mit HEAD an, ohne Weiterleitungen, nur für http- und https-Adressen, die sich auf
öffentliche Hosts auflösen (Loopback, private, Link-Local-Adressen und Zugangsdaten werden mit 422 abgelehnt).

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expurl::list::<limit>::<offset>::<validity>` | url/list |  | Die gespeicherten Links, die neuesten zuerst |
| `expurl::get::<id>` | url/view |  | Ein Link mit seinem Prüfzustand |
| `expurl::count::<validity>` | url/list |  | Zahl der gespeicherten Links |
| `expurl::stats` | url/list |  | Links nach Gültigkeit: gültig, ungültig, nie geprüft |
| `expurl::invalid::<limit>::<offset>` | url/list |  | Die als ungültig erkannten Links |
| `expurl::unchecked::<limit>::<offset>` | url/list |  | Die nie geprüften Links |
| `expurl::search::<text>::<limit>::<offset>` | url/list |  | Links, deren Adresse den Text enthält |
| `expurl::byurl::<url>` | url/view |  | Der gespeicherte Link mit genau dieser Adresse |
| `expurl::objects::<id>::<limit>::<offset>` | url/view |  | Die Objekte (für den Benutzer lesbar), die einen Link verwenden |
| `expurl::bydomain::<limit>` | url/list |  | Anzahl der Links pro Hostname |
| `expurl::check::<id>` | url/edit | POST | Fragt einen gespeicherten Link an (HEAD) und verzeichnet, ob er gültig ist; nur öffentliche http-/https-Hosts |
| `expurl::setvalid::<id>` | url/edit | POST | Setzt die Gültigkeit eines gespeicherten Links von Hand |

### PDF (`exppdf`, 10 Dienste)

PDF-Links von Knoten (`content/pdf/<node>`, das die Richtlinie erneut prüft, wenn dem Link gefolgt wird) und die
Definitionen für den PDF-Export des Moduls pdf.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `exppdf::available` | public |  | Ob der PDF-Export verfügbar ist (Klassen vorhanden) und wie viele Exportdefinitionen existieren |
| `exppdf::canpdf::<node>` | content/read |  | Ob der Benutzer einen Knoten als PDF exportieren darf |
| `exppdf::link::<node>::<language>` | content/read |  | Die URL, die das PDF eines Knotens herunterlädt (optionale Sprache) |
| `exppdf::links::<nodes>` | content/read |  | PDF-Links für mehrere Knoten auf einmal (die der Benutzer exportieren darf) |
| `exppdf::exports::<limit>::<offset>` | pdf/edit |  | Die Definitionen für den PDF-Export |
| `exppdf::export::<id>` | pdf/edit |  | Eine Definition für den PDF-Export |
| `exppdf::exportcount` | pdf/edit |  | Wie viele Definitionen für den PDF-Export existieren |
| `exppdf::exportsfor::<node>` | pdf/edit |  | Die Exportdefinitionen, deren Quellknoten ein Knoten oder einer seiner Vorfahren ist |
| `exppdf::exportfile::<id>` | pdf/edit |  | Ob die erzeugte Datei eines gespeicherten Exports existiert und wie groß sie ist |
| `exppdf::statuses` | public |  | Die Exportmodi: einmal erzeugt oder bei Bedarf |


## Clients

Jeder Dienst ist ein einfacher HTTP-Aufruf, daher ist jede Sprache mit einem HTTP-Client und einem Cookie-Speicher ein
Client. Der Vertrag, dem alle Beispiele unten folgen:

1. **Übertragungsformat.** `GET|POST <site>/ezjscore/call/<service>[::arg...]?ContentType=json` mit
   `Accept: application/json`. ezjscore umschließt die Antwort: `{ "error_text": "", "content": <envelope> }`.
   `error_text` ist nur gesetzt, wenn der Aufruf selbst nicht weitergeleitet werden konnte; das eigene Ergebnis des
   Dienstes (oder sein Fehler) ist `content`, siehe „Der Umschlag“. Argumente in der URL werden prozentkodiert und mit
   `::` verbunden; ein Schreibvorgang sendet seine Argumente als POST-Formularfelder.
2. **Authentifizierung.** `POST expsession::login` mit `username` und `password` (formularkodiert). Das `Set-Cookie`
   der Antwort ist die Sitzung; halten Sie es in einem Cookie-Speicher. Die Antwort enthält außerdem das
   Formular-Token der neuen Sitzung (`data.token`), das jedes vor der Anmeldung geholte Token ersetzt. Anonyme Aufrufe
   funktionieren für die Dienste, deren Zugriff `public` ist. **Oder verwenden Sie ein persönliches API-Token**
   (`expsession::tokenCreate`, siehe „Authentifizierung mit einem API-Token“): Senden Sie
   `Authorization: Bearer <token>` und `X-Exp-Token: <token>` mit jedem Aufruf, halten Sie kein Cookie, holen Sie kein
   Formular-Token; Schreibvorgänge sind weiterhin POST.
3. **Schreibvorgänge.** Mit einem API-Token ist ein Schreibvorgang nur ein POST (kein Cookie, daher kein
   Formular-Token). Mit der Sitzung: Holen Sie `expsession::token` (`data.token`, `data.field` = `ezxform_token`,
   `data.header` = `X-CSRF-Token`) und senden Sie dann `POST` mit dem Token als Feld `ezxform_token` und als Header
   `X-CSRF-Token`. Ein fehlendes oder falsches Token ergibt 403.
4. **Fehler.** HTTP 200 bedeutet nicht Erfolg: Sehen Sie auf `content.ok`. `content.error.code` ist
   400/401/403/404/409/422/500 (Tabelle unter „Fehler“). Ein 401 bedeutet, sich erneut anzumelden, ein 403 eine
   fehlende Richtlinie oder ein fehlendes Token (holen Sie einmal ein frisches Token und versuchen Sie es einmal
   erneut, nie in einer Schleife), 404/409/422 sind für den Benutzer bestimmt. Ein HTTP-Status ungleich 200 oder ein
   Body, der kein JSON ist, ist ein Transportfehler (Proxy, TLS, Server nicht erreichbar): Wiederholen Sie mit
   zunehmender Wartezeit, interpretieren Sie nichts.
5. **Seitenweise Ausgabe.** Listendienste nehmen `offset` und `limit` als ihre letzten beiden URL-Argumente an und
   antworten mit `meta.total`, `meta.offset`, `meta.limit`, `meta.has_more`. Wiederholen Sie, solange `has_more` wahr
   ist (oder `offset + count < total`); halten Sie `limit` bei oder unter `expservices.ini [Paging] MaxLimit` (200).
6. **Discovery.** `expservices::catalog` liefert jeden Dienst mit seinen `args`, `access`, `write`; erzeugen Sie
   daraus einen typisierten Client, oder rufen Sie `expservices::service::<domain>::<method>` für eine Beschreibung
   auf.

**Toolchains und was kompiliert wurde** (geprüft am 2026-10-02 auf der Build-Maschine): Es gibt nur `bash`+`curl`,
`python3` (mit `requests` und PyGObject `gi`) und `g++` ohne Qt. Die Shell- und Python-Clients und das
GTK/Python-Beispiel (beide Varianten) wurden aus dieser Anleitung entnommen und gegen die Live-Website ausgeführt. Die
Beispiele in Kotlin, Swift, Objective-C und Qt/C++ konnten hier **nicht** kompiliert werden (kein `kotlinc`, `swiftc`,
`clang`/`gobjc` und keine Qt-Header); jedes davon sagt das, und sie sind gegen die dokumentierten APIs von OkHttp 4,
URLSession, NSURLSession und Qt 5/6 Network geschrieben und von Hand geprüft. Kompilieren Sie sie in Ihrem Projekt,
bevor Sie sich darauf verlassen.

In den Beispielen ist `$SITE` die Basis-URL, z. B. `https://www.example.com` (oder
`https://www.example.com/<siteaccess>`, wenn die Installation einen Siteaccess im Pfad braucht), `expnode::children`
ist ein seitenweise gelieferter Lesevorgang, und `expforum::reply` steht für jeden schreibenden Dienst (nehmen Sie
einen echten aus dem Katalog). Die Shell- und Python-Clients liegen in `extension/expservices/bin/` und sind gegen die
Live-Dienste getestet; die anderen Beispiele folgen demselben Vertrag und sind als Ausgangspunkt einer App gedacht.

### Shell (bash + curl): `extension/expservices/bin/expservices-client.sh` (hier getestet)

    export EXPSERVICES_URL=https://www.example.com
    expservices-client.sh whoami                          # anonymous or signed in, pretty printed
    expservices-client.sh catalog session                 # the services of one domain
    expservices-client.sh call expsystem::version         # any read call; arguments follow, joined with ::
    expservices-client.sh login editor                    # asks for the password (or EXPSERVICES_PASSWORD); cookie jar kept
    expservices-client.sh page expnode::children 2 --limit 50 --max 500   # every item of a paged service, one JSON per line
    expservices-client.sh post expforum::reply topic=123 'message=Hello' # write: token fetched and sent as field and header
    expservices-client.sh --raw call expsession::ping     # the answer as received
    EXPSERVICES_TOKEN=expt_... expservices-client.sh post expforum::reply topic=123 message=Hi   # API token: no login, no form token

Der Cookie-Speicher ist `${XDG_CACHE_HOME:-~/.cache}/expservices/cookies.txt` (`--jar` oder `EXPSERVICES_JAR` ändern
ihn). Exit-Status: 0 in Ordnung, 1 Fehler des Dienstes (`error 401: ...` auf stderr), 2 Transportfehler. Er braucht
bash und curl, dazu jq (oder python3) für das JSON.

### Python: `extension/expservices/bin/expservices_client.py` (hier getestet)

Nur die Standardbibliothek; ein Modul und eine Kommandozeile (`expservices_client.py call expsystem::version`,
`page`, `post`, `login`...).

    from expservices_client import Client, ServiceError, TransportError

    c = Client("https://www.example.com", jar="expservices.cookies")   # jar optional; keeps the session between runs
    c.login("editor", "secret")                                         # POST, stores the new token
    version = c.call("expsystem::version")                              # read: returns data
    for node in c.pages("expnode::children", 2, limit=50):              # paged read: yields every item
        print(node["name"])
    try:
        c.post("expforum::reply", {"topic": 123, "message": "Hello"})   # write: field + header token
    except ServiceError as e:                                           # the envelope's error
        print(e.code, e)                                                # 401 login again, 403 policy/token, 422 data
    except TransportError as e:                                         # HTTP/network/JSON fault
        print("retry later:", e)

    t = Client("https://www.example.com", api_token="expt_...")        # API token variant: no login, no jar, no form token
    t.post("expforum::reply", {"topic": 123, "message": "Hello"})       # a POST with the token headers; command line: --token or EXPSERVICES_TOKEN

### Kotlin / Android (OkHttp)

*Hier nicht kompiliert (keine Kotlin-Toolchain auf der Build-Maschine).*

    // build.gradle: implementation("com.squareup.okhttp3:okhttp:4.12.0"); org.json comes with Android
    class Expservices(val site: String) {
        private val jar = HashMap<String, List<Cookie>>()                 // use PersistentCookieJar for a real app
        private val http = OkHttpClient.Builder().cookieJar(object : CookieJar {
            override fun saveFromResponse(url: HttpUrl, cookies: List<Cookie>) { jar[url.host] = cookies }
            override fun loadForRequest(url: HttpUrl) = jar[url.host] ?: emptyList()
        }).build()

        class ServiceException(val code: Int, message: String) : Exception(message)

        private fun url(service: String, args: List<Any> = emptyList()) =
            "$site/ezjscore/call/$service" + args.joinToString("") { "::" + Uri.encode(it.toString()) } + "?ContentType=json"

        private fun send(req: Request): JSONObject {                      // returns the envelope, throws on error
            http.newCall(req).execute().use { r ->
                if (!r.isSuccessful) throw IOException("HTTP ${r.code}")
                val outer = JSONObject(r.body!!.string())
                if (outer.optString("error_text").isNotEmpty()) throw ServiceException(500, outer.getString("error_text"))
                val env = outer.getJSONObject("content")
                if (!env.getBoolean("ok")) env.getJSONObject("error").let { throw ServiceException(it.getInt("code"), it.getString("message")) }
                return env
            }
        }

        // auth
        fun login(user: String, pass: String): JSONObject = send(Request.Builder().url(url("expsession::login"))
            .post(FormBody.Builder().add("username", user).add("password", pass).build()).build())

        // read, with paging
        fun children(nodeId: Int, offset: Int = 0, limit: Int = 25): JSONObject =
            send(Request.Builder().url(url("expnode::children", listOf(nodeId, offset, limit))).build())
        // loop: do { val e = children(2, off); ...; off += 25 } while (e.getJSONObject("meta").getBoolean("has_more"))

        // write with the form token
        fun write(service: String, fields: Map<String, String>): JSONObject {
            val t = send(Request.Builder().url(url("expsession::token")).build()).getJSONObject("data").getString("token")
            val body = FormBody.Builder().add("ezxform_token", t)
            fields.forEach { (k, v) -> body.add(k, v) }
            return send(Request.Builder().url(url(service)).header("X-CSRF-Token", t).post(body.build()).build())
        }
    }
    // error handling: catch ServiceException (401: show the sign-in screen; 403: refresh the token once; 422: show the message)
    // and IOException (offline: retry with backoff). Run it off the main thread (coroutines Dispatchers.IO).

    // API token variant: no cookie jar, no login, no form token. Keep the token in the Android Keystore / EncryptedSharedPreferences.
    class ExpservicesToken(val site: String, private val token: String) {
        private val http = OkHttpClient()                                   // nothing to keep between calls
        private fun url(service: String, args: List<Any> = emptyList()) =
            "$site/ezjscore/call/$service" + args.joinToString("") { "::" + Uri.encode(it.toString()) } + "?ContentType=json"
        private fun auth(b: Request.Builder) = b.header("Authorization", "Bearer $token").header("X-Exp-Token", token)
        private fun send(req: Request): JSONObject = http.newCall(req).execute().use { r ->
            if (!r.isSuccessful) throw IOException("HTTP ${r.code}")
            val env = JSONObject(r.body!!.string()).getJSONObject("content")
            if (!env.getBoolean("ok")) env.getJSONObject("error").let { throw Expservices.ServiceException(it.getInt("code"), it.getString("message")) }
            env                                                            // 401 here: the token is revoked or expired, ask for a new one
        }
        fun read(service: String, vararg args: Any) = send(auth(Request.Builder().url(url(service, args.toList()))).build())
        fun write(service: String, fields: Map<String, String>): JSONObject {   // POST only, no form token
            val body = FormBody.Builder(); fields.forEach { (k, v) -> body.add(k, v) }
            return send(auth(Request.Builder().url(url(service))).post(body.build()).build())
        }
    }

### Swift / iOS (URLSession)

*Hier nicht kompiliert (keine Swift-Toolchain auf der Build-Maschine).*

    struct ServiceError: Error { let code: Int; let message: String }

    final class Expservices {
        let site: URL                                    // https://www.example.com
        let session = URLSession(configuration: .default)   // the default configuration keeps cookies (HTTPCookieStorage.shared)
        init(site: URL) { self.site = site }

        private func url(_ service: String, _ args: [Any] = []) -> URL {
            let tail = args.map { "::" + "\($0)".addingPercentEncoding(withAllowedCharacters: .alphanumerics)! }.joined()
            return URL(string: "\(site)/ezjscore/call/\(service)\(tail)?ContentType=json")!
        }
        private func envelope(_ req: URLRequest) async throws -> [String: Any] {
            let (data, resp) = try await session.data(for: req)
            guard (resp as? HTTPURLResponse)?.statusCode == 200,
                  let outer = try JSONSerialization.jsonObject(with: data) as? [String: Any],
                  let env = outer["content"] as? [String: Any] else { throw ServiceError(code: 0, message: "transport fault") }
            if env["ok"] as? Bool != true {
                let e = env["error"] as? [String: Any] ?? [:]
                throw ServiceError(code: e["code"] as? Int ?? 500, message: e["message"] as? String ?? "error")
            }
            return env
        }
        private func form(_ url: URL, _ fields: [String: String], token: String? = nil) -> URLRequest {
            var r = URLRequest(url: url); r.httpMethod = "POST"
            var f = fields; if let t = token { f["ezxform_token"] = t; r.setValue(t, forHTTPHeaderField: "X-CSRF-Token") }
            var c = URLComponents(); c.queryItems = f.map { URLQueryItem(name: $0.key, value: $0.value) }
            r.httpBody = c.percentEncodedQuery?.data(using: .utf8)
            r.setValue("application/x-www-form-urlencoded", forHTTPHeaderField: "Content-Type"); return r
        }

        func login(_ user: String, _ pass: String) async throws -> [String: Any] {          // auth
            try await envelope(form(url("expsession::login"), ["username": user, "password": pass]))
        }
        func children(_ node: Int, offset: Int = 0, limit: Int = 25) async throws -> [String: Any] {   // read, paged
            try await envelope(URLRequest(url: url("expnode::children", [node, offset, limit])))     // meta.has_more drives the next page
        }
        func write(_ service: String, _ fields: [String: String]) async throws -> [String: Any] {     // write
            let t = ((try await envelope(URLRequest(url: url("expsession::token"))))["data"] as! [String: Any])["token"] as! String
            return try await envelope(form(url(service), fields, token: t))
        }
    }
    // Errors: catch ServiceError (401 sign in, 403 refresh token once, 422 show message); other errors are transport faults.

    // API token variant: no cookies, no login, no form token. Keep the token in the Keychain.
    final class ExpservicesToken {
        let site: URL, token: String
        let session = URLSession(configuration: { let c = URLSessionConfiguration.ephemeral; c.httpCookieStorage = nil; return c }())
        init(site: URL, token: String) { self.site = site; self.token = token }

        private func request(_ service: String, _ args: [Any] = [], form: [String: String]? = nil) -> URLRequest {
            let tail = args.map { "::" + "\($0)".addingPercentEncoding(withAllowedCharacters: .alphanumerics)! }.joined()
            var r = URLRequest(url: URL(string: "\(site)/ezjscore/call/\(service)\(tail)?ContentType=json")!)
            r.setValue("Bearer \(token)", forHTTPHeaderField: "Authorization")
            r.setValue(token, forHTTPHeaderField: "X-Exp-Token")
            if let f = form {                                              // a write: POST, no form token
                var c = URLComponents(); c.queryItems = f.map { URLQueryItem(name: $0.key, value: $0.value) }
                r.httpMethod = "POST"; r.httpBody = c.percentEncodedQuery?.data(using: .utf8)
                r.setValue("application/x-www-form-urlencoded", forHTTPHeaderField: "Content-Type")
            }
            return r
        }
        func call(_ service: String, _ args: [Any] = [], form: [String: String]? = nil) async throws -> [String: Any] {
            let (data, resp) = try await session.data(for: request(service, args, form: form))
            guard (resp as? HTTPURLResponse)?.statusCode == 200,
                  let env = (try JSONSerialization.jsonObject(with: data) as? [String: Any])?["content"] as? [String: Any]
            else { throw ServiceError(code: 0, message: "transport fault") }
            if env["ok"] as? Bool != true {                               // 401: the token is revoked or expired
                let e = env["error"] as? [String: Any] ?? [:]
                throw ServiceError(code: e["code"] as? Int ?? 500, message: e["message"] as? String ?? "error")
            }
            return env
        }
    }

### Objective-C (NSURLSession)

*Hier nicht kompiliert (kein Objective-C-Compiler und kein Foundation auf der Build-Maschine; `gcc` hat kein
Objective-C-Frontend).*

    // The shared session keeps cookies. Wrap in a method taking a completion block; shown here for one write.
    - (void)post:(NSString *)service fields:(NSDictionary<NSString *, NSString *> *)fields
      completion:(void (^)(NSDictionary *data, NSError *error))done {
        NSString *base = @"https://www.example.com/ezjscore/call/";
        NSURL *tokenURL = [NSURL URLWithString:[base stringByAppendingString:@"expsession::token?ContentType=json"]];
        [[NSURLSession.sharedSession dataTaskWithURL:tokenURL completionHandler:^(NSData *d, NSURLResponse *r, NSError *e) {
            NSDictionary *env = [NSJSONSerialization JSONObjectWithData:d options:0 error:nil][@"content"];   // d nil = transport fault
            if (e || ![env[@"ok"] boolValue]) { done(nil, e ?: [self errorFrom:env]); return; }
            NSString *token = env[@"data"][@"token"];
            NSMutableArray *pairs = [NSMutableArray arrayWithObject:[@"ezxform_token=" stringByAppendingString:token]];
            [fields enumerateKeysAndObjectsUsingBlock:^(NSString *k, NSString *v, BOOL *s) {
                [pairs addObject:[NSString stringWithFormat:@"%@=%@", k,
                    [v stringByAddingPercentEncodingWithAllowedCharacters:NSCharacterSet.alphanumericCharacterSet]]];
            }];
            NSString *u = [NSString stringWithFormat:@"%@%@?ContentType=json", base, service];
            NSMutableURLRequest *req = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:u]];
            req.HTTPMethod = @"POST"; req.HTTPBody = [[pairs componentsJoinedByString:@"&"] dataUsingEncoding:NSUTF8StringEncoding];
            [req setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
            [req setValue:token forHTTPHeaderField:@"X-CSRF-Token"];
            [[NSURLSession.sharedSession dataTaskWithRequest:req completionHandler:^(NSData *d2, NSURLResponse *r2, NSError *e2) {
                NSDictionary *env2 = d2 ? [NSJSONSerialization JSONObjectWithData:d2 options:0 error:nil][@"content"] : nil;
                done([env2[@"ok"] boolValue] ? env2[@"data"] : nil, e2 ?: ([env2[@"ok"] boolValue] ? nil : [self errorFrom:env2]));
            }] resume];
        }] resume];
    }
    - (NSError *)errorFrom:(NSDictionary *)env {      // { ok:false, error:{ code, message } } -> NSError (domain "expservices")
        NSDictionary *err = env[@"error"];
        return [NSError errorWithDomain:@"expservices" code:[err[@"code"] integerValue]
                               userInfo:@{NSLocalizedDescriptionKey: err[@"message"] ?: @"transport fault"}];
    }
    // Login: the same POST without token to expsession::login with username and password (cookies are kept by the session).
    // Read with paging: GET .../expnode::children::2::<offset>::<limit>, continue while env[@"meta"][@"has_more"] is true.

    // API token variant: no cookies, no token fetch; the same POST without ezxform_token and with the token headers.
    - (NSMutableURLRequest *)requestFor:(NSString *)service token:(NSString *)token {
        NSString *u = [NSString stringWithFormat:@"https://www.example.com/ezjscore/call/%@?ContentType=json", service];
        NSMutableURLRequest *req = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:u]];
        req.HTTPShouldHandleCookies = NO;                                       // the token is the only credential
        [req setValue:[@"Bearer " stringByAppendingString:token] forHTTPHeaderField:@"Authorization"];
        [req setValue:token forHTTPHeaderField:@"X-Exp-Token"];
        return req;       // for a write set HTTPMethod = @"POST", the form body and the Content-Type as above (no ezxform_token)
    }
    // Run it with [[NSURLSession sessionWithConfiguration:NSURLSessionConfiguration.ephemeralSessionConfiguration] dataTaskWithRequest:...];
    // keep the token in the Keychain. A 401 envelope means the token is revoked or expired.

### Qt / C++ (QNetworkAccessManager)

*Hier nicht kompiliert (keine Qt-Header und kein `qmake` auf der Build-Maschine).*

    // QT += network. One QNetworkAccessManager per client; set a QNetworkCookieJar (the default one keeps session cookies).
    class Expservices : public QObject {
        Q_OBJECT
        QNetworkAccessManager net; QString site = "https://www.example.com"; QString token;
        QUrl url(const QString &svc, const QStringList &args = {}) const {
            QString u = site + "/ezjscore/call/" + svc;
            for (const QString &a : args) u += "::" + QString::fromUtf8(QUrl::toPercentEncoding(a));
            return QUrl(u + "?ContentType=json");
        }
        // Parses the answer; emits failed(code, message) for { ok:false }, for a non-200 status or a body that is not JSON.
        void handle(QNetworkReply *r, std::function<void(const QJsonObject &)> ok) {
            connect(r, &QNetworkReply::finished, this, [=] {
                const QByteArray body = r->readAll(); const int http = r->attribute(QNetworkRequest::HttpStatusCodeAttribute).toInt();
                r->deleteLater();
                const QJsonObject env = QJsonDocument::fromJson(body).object().value("content").toObject();
                if (r->error() != QNetworkReply::NoError && http != 200) { emit failed(0, r->errorString()); return; }   // transport
                if (!env.value("ok").toBool()) { const auto e = env.value("error").toObject();
                    emit failed(e.value("code").toInt(500), e.value("message").toString()); return; }                  // service
                ok(env);
            });
        }
    public:
        void login(const QString &user, const QString &pass) {                        // auth: POST, no token needed
            QNetworkRequest rq(url("expsession::login"));
            rq.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
            QUrlQuery q; q.addQueryItem("username", user); q.addQueryItem("password", pass);
            handle(net.post(rq, q.toString(QUrl::FullyEncoded).toUtf8()), [this](const QJsonObject &env) {
                token = env["data"].toObject()["token"].toString(); emit signedIn(env["data"].toObject()); });
        }
        void children(int node, int offset, int limit) {                              // read, paged
            handle(net.get(QNetworkRequest(url("expnode::children", {QString::number(node), QString::number(offset), QString::number(limit)}))),
                   [this](const QJsonObject &env) { emit page(env["data"].toArray(), env["meta"].toObject()["has_more"].toBool()); });
        }
        void write(const QString &svc, const QMap<QString, QString> &fields) {         // write: field + header token
            QNetworkRequest rq(url(svc));
            rq.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
            rq.setRawHeader("X-CSRF-Token", token.toUtf8());
            QUrlQuery q; q.addQueryItem("ezxform_token", token);
            for (auto it = fields.begin(); it != fields.end(); ++it) q.addQueryItem(it.key(), it.value());
            handle(net.post(rq, q.toString(QUrl::FullyEncoded).toUtf8()), [this](const QJsonObject &env) { emit written(env["data"]); });
        }
    signals: void failed(int code, const QString &message); void signedIn(const QJsonObject &); void page(const QJsonArray &, bool more); void written(const QJsonValue &);
    };
    // The token comes from the login answer; after a 403 fetch expsession::token once and retry once.

    // API token variant: no login, no cookie jar needed, no form token. Keep the token in KWallet / libsecret.
    class ExpservicesToken : public Expservices {
        QString apiToken;
    public:
        explicit ExpservicesToken(const QString &t) : apiToken(t) {}
        QNetworkRequest authorised(QNetworkRequest rq) const {
            rq.setRawHeader("Authorization", "Bearer " + apiToken.toUtf8());
            rq.setRawHeader("X-Exp-Token", apiToken.toUtf8());
            return rq;
        }
        // read: net.get(authorised(QNetworkRequest(url("expsession::whoami"))));
        // write: rq = authorised(QNetworkRequest(url(svc))); content type form-urlencoded; net.post(rq, body) with the fields only (no ezxform_token)
        // a { ok:false, error:{ code:401 } } answer means the token is revoked or expired: ask the user for a new one
    };

### GTK / Python (requests oder libsoup) (hier getestet: beide Varianten laufen gegen die Live-Website)

Führen Sie mit PyGObject die Aufrufe außerhalb der Hauptschleife aus (`GLib.idle_add` oder ein Thread) und übergeben
Sie die Daten an die Widgets. `requests` ist der einfachste Transport; der Shell-/Python-Client oben hat dieselbe
Logik ohne ihn.

    import http.cookiejar
    import requests
    from gi.repository import GLib

    class Api:
        def __init__(self, site): self.site, self.s, self.token = site, requests.Session(), None   # Session keeps cookies
        def _url(self, svc, args=()): return f"{self.site}/ezjscore/call/{svc}" + "".join("::" + requests.utils.quote(str(a), safe="") for a in args) + "?ContentType=json"
        def _env(self, r):
            r.raise_for_status()                                  # transport fault: HTTPError
            env = r.json()["content"]
            if not env["ok"]: raise RuntimeError(f'{env["error"]["code"]}: {env["error"]["message"]}')
            return env
        def login(self, u, p): env = self._env(self.s.post(self._url("expsession::login"), data={"username": u, "password": p})); self.token = env["data"]["token"]; return env["data"]
        def read(self, svc, *args): return self._env(self.s.get(self._url(svc, args)))            # paged: args end with offset, limit
        def write(self, svc, **fields):
            self.token = self.token or self.read("expsession::token")["data"]["token"]
            return self._env(self.s.post(self._url(svc), data={"ezxform_token": self.token, **fields}, headers={"X-CSRF-Token": self.token}))

    # in a handler: threading.Thread(target=lambda: GLib.idle_add(fill_list, api.read("expnode::children", 2, 0, 25)["data"])).start()

    class TokenApi(Api):                                    # API token variant: no login, no cookies, no form token
        def __init__(self, site, token):
            super().__init__(site)
            self.s.cookies.set_policy(http.cookiejar.DefaultCookiePolicy(allowed_domains=[]))   # keep no cookie
            self.s.headers.update({"Authorization": "Bearer " + token, "X-Exp-Token": token})
        def write(self, svc, **fields): return self._env(self.s.post(self._url(svc), data=fields))   # POST, no ezxform_token

libsoup (Soup 3): `Soup.Session()` behält Cookies, wenn ein Feature `Soup.CookieJar` hinzugefügt wird
(`session.add_feature(Soup.CookieJar())`); `Soup.Message.new_from_encoded_form("POST", url, Soup.form_encode(...))` baut
den POST, `msg.get_request_headers().append("X-CSRF-Token", token)` fügt den Header hinzu, und
`session.send_and_read_async(msg, ...)` gibt die Bytes desselben JSON `{ error_text, content }` zurück.

### Hinweise zur Integration in KDE und GNOME

- **KDE (Qt/KF6).** Verwenden Sie den Qt-Client oben. Speichern Sie das Passwort in KWallet
  (`KWallet::Wallet::openWallet`) und nie in der Konfigurationsdatei; nur das Sitzungs-Cookie lebt im Speicher (oder ein
  nach `~/.cache` gespeicherter `QNetworkCookieJar`). Zeigen Sie die Dienste des Katalogs als KRunner-Plugin (Abfrage
  `expsearch::search`), als Plasma-Widget (lesende Dienste per Timer, `expsession::ping` hält die Sitzung) oder als
  KIO-Slave-artiges Durchblättern von `expnode::children`. `KJob`/`QNetworkReply` liefern das asynchrone Modell;
  KNotification für 401 („erneut anmelden“).
- **GNOME (GTK4/libadwaita).** Verwenden Sie den GTK/Python-Client oder `Soup 3` aus C/Vala/Rust. Speichern Sie das
  Passwort mit libsecret (`Secret.password_store_sync`); zeigen Sie die Dienste in einem Suchanbieter der GNOME Shell
  (D-Bus `org.gnome.Shell.SearchProvider2`, gestützt auf `expsearch::search`), in einer libadwaita-App mit einer
  `Adw.NavigationView` pro Knotenebene und einem seitenweise gefüllten `Gio.ListStore`. `Gio.Task`/`GLib.idle_add`
  halten die Oberfläche reaktionsfähig; GNotification für 401.
- **Beide.** Ermitteln Sie die Dienste beim Start mit `expservices::catalog` (zwischengespeichert nach
  `expservices::version`), bauen Sie Formulare aus den `args` eines schreibenden Dienstes, senden Sie
  Schreibvorgänge nur nach einer Aktion des Benutzers, und behandeln Sie jedes 403 als „das Token einmal erneuern“.
  Online-Konten: KDE (KAccounts) und GNOME (GOA) haben keinen allgemeinen Anbieter für eine „Cookie-Sitzung“; halten
  Sie das Konto daher in der App.

### Tests

Die Shell- und Python-Clients wurden gegen eine Live-Website ausgeführt (lesende Aufrufe; der Schreibpfad bis zum
401, das eine anonyme Sitzung erhält). Der Ablauf mit API-Token wurde über HTTP geprüft (Erzeugen, Lesen und Schreiben
mit Bearer, Widerrufen, 401), die Shell- und Python-Clients wurden mit einem Token betrieben, und die GTK/Python-Beispiele
dieser Anleitung wurden entnommen und ausgeführt.

## Frontend-Designs: React und Wireframe

Beide sind Designs der Erweiterung (`extension/expservices/design/`), werden von Test-Siteaccesses angezeigt und über
die URI erreicht:

| Design | Siteaccess / URL | Was |
|---|---|---|
| `expportal_react` | `/portalreact/` | das Portal in React 18 mit React Bootstrap 2.10 (Bootstrap 5.3), Hash-Routen `#/news`, `#/shop`, `#/shop/basket`, `#/forums`, `#/media`, `#/feeds`, `#/search`, `#/login`, `#/profile` |
| `expportal_wireframe` | `/portalwireframe/` | Bildschirme mit geringer Detailtreue (graue Kästen) mit einer Anmerkung pro Block, die den Dienst nennt, der ihn speist, einem Umschalter für Telefon- und Desktop-Rahmen und einem Umschalter für Notizen; ist der Katalog lesbar, wird jeder Dienstname als vorhanden oder fehlend markiert |

**Kein Build-Schritt; mitgelieferte Bibliotheken.** Das React-Design braucht kein Node, kein JSX und keinen Bundler:
Sein Code besteht aus ES-Modulen (`javascript/portal/`), die `React.createElement` (`h`) verwenden. Die Bibliotheken
sind in das Design übernommen (`javascript/vendor/`, `stylesheets/vendor/`), sodass die Website offline funktioniert
und sich die Versionen nicht unter ihr verschieben können: React und ReactDOM 18.3.1 (UMD) und React Bootstrap 2.10.2
von `cdnjs.cloudflare.com/ajax/libs/` sowie das CSS von Bootstrap 5.3.3. Für ein Upgrade laden Sie dieselben
Dateinamen in der neuen Version von cdnjs herunter. Aufbau: `services.js` (der Client: fetch, Sitzungs-Cookie,
Formular-Token, Auspacken des Umschlags, `ServiceError`), `api.js` (eine Funktion pro Bedarf eines Bildschirms, die
einzige Datei, die Dienste nennt), `ui.js` (Abkürzungen für das Kit, `useAsync`, Blätterleiste, Fehler- und
Ladezustände), `pages/*.js` (eine Komponente pro Seite), `app.js` (Rahmen und Hash-Router).

**Siteaccesses.** `portalreact` und `portalwireframe` sind Verzeichnisse `settings/siteaccess/<name>/site.ini.append.php`
(`SiteDesign` = das Design, `IndexPage=user/login`, damit der Rahmen mit einem 200 gerendert wird) plus der Eintrag
`AvailableSiteAccessList[]=<name>` im globalen Override, hinzugefügt mit
`exp:ini add site/SiteAccessSettings/AvailableSiteAccessList[] <name> override`. Machen Sie es mit `exp:ini rem` für
denselben Eintrag und durch Entfernen des Verzeichnisses rückgängig; der Live-Siteaccess `site` bleibt unberührt. Die
Erweiterung registriert ihre Designs mit `settings/design.ini.append.php` (`DesignExtensions[]=expservices`); ohne diese
Datei wird ein Design einer Erweiterung nicht gefunden.

## Frontends: jQuery-4-Referenz und reaktives jQuery

Zwei Portal-Designs in `extension/expservices/design/` nutzen die Dienste aus dem Browser. Beide sind
Unternehmensportale (Startseite, Nachrichtenliste und Artikel, Shop-Katalog, Produkt und Warenkorb, Foren mit Themen
und Antworten, Mediengalerie, Feeds, Suche, Anmeldung und Profil), clientseitig gerendert, ohne Build-Schritt, auf dem
jQuery 4, das ezjscore mitliefert (`ezjsc::jquery`). Beide sind barrierefrei (Sprunglink, Landmarks, Ansagen über
`aria-live`, Fokus bei der Navigation auf die Seite verschoben, beschriftete Formulare, natives `<dialog>` für die
Lightbox), responsiv (Mobile First, ein Breakpoint bei 48em) und hell oder dunkel (`prefers-color-scheme`, dazu ein
Umschalter, der in `localStorage` gespeichert wird).

| Design | Siteaccess (Test) | URL | Idee |
|---|---|---|---|
| `expportal_jquery` | `portaljq` | `/portaljq/` | der Referenzstandard: kleine Module, ein Skript pro Funktion, Seiten bauen das DOM direkt |
| `expportal_reactive` | `portalreactive` | `/portalreactive/` | dieselben Funktionen mit einem React-ähnlichen Modell: Komponenten, Store, Datenfluss in eine Richtung |

### Architektur (expportal_jquery)

`templates/pagelayout.tpl` ist das einzige Template. Es ignoriert das Modulergebnis, gibt den Seitenrahmen aus,
übergibt die Konfiguration als `data-`-Attribute von `<body>` (URL des Siteaccess, URL von `ezjscore/call`,
Knoten-IDs der Inhaltswurzeln aus `extension/expservices/settings/expportal.ini`), lädt jQuery 4 über
`ezscript_load( 'ezjsc::jquery' )` und dann die Skripte in `javascript/portal/`, eines pro Funktion:

| Datei | Aufgabe |
|---|---|
| `core.js` | Konfiguration, **die Diensttabelle** (`ExpPortal.services`), `api.call()`, DOM-Hilfen (Text wird immer als Text gesetzt), UI-Zustände, Hash-Router, Theme |
| `home.js`, `news.js`, `shop.js`, `forums.js`, `media.js`, `feeds.js`, `search.js`, `account.js` | je eine Funktion: registriert ihre Routen mit `router.add( '/news/:id', handler )` |
| `app.js` | Start: Rahmen, Dienstkatalog, wer angemeldet ist, Zahl im Warenkorb, Router |

Die Adresse ist die Hash-Route (`#/news/12`), daher wird jede Portal-URL von dem einen Pagelayout ausgeliefert, und
das Portal kann unter jeden Siteaccess gelegt werden. `api.call( feature, args, post )` baut
`<siteaccess>/ezjscore/call/<service>::<arg>::<arg>?ContentType=json`, sendet Lesevorgänge als GET und
Schreibvorgänge als POST mit dem Formular-Token (`expsession::token`, Feld `ezxform_token`, Header `X-CSRF-Token`; die
Anmeldung ist der eine Schreibvorgang, der ohne es gesendet wird), packt `{ error_text, content }` und den Umschlag
des Dienstes `{ ok, data, meta }` aus und lehnt mit `{ code, message }` ab. Beim Start liest es
`expservices::catalog`: Ein Dienst, der **nicht im Katalog steht, wird nicht aufgerufen**, und seine Seite zeigt statt
eines Fehlers einen klaren Zustand „Dienst nicht verfügbar“; kann der Katalog selbst nicht gelesen werden, werden die
Aufrufe versucht, und eine Antwort 404 oder „no such function“ erhält denselben Zustand.

### Die reaktive Variante (expportal_reactive)

Sie fällt auf `expportal_jquery` zurück (`AdditionalSiteDesignList`), daher werden `core.js` (API, Dienste, Router,
Theme) und `portal.css` geteilt. Ihre eigenen Skripte in `javascript/reactive/` setzen das Muster um, das in jeder
Datei dokumentiert ist:

```
state --(components)--> description --(vdom: keyed diff)--> DOM
  ^                                                            |
  |   dispatch( action ) <--------- event handlers ------------+
  +--- reducer( state, action ) -> new state;  effects run after the reducer and call services
```

| Datei | Aufgabe |
|---|---|
| `vdom.js` | `h( tag, props, ...children )` gibt eine Beschreibung zurück; `mount( container, view )` rendert sie und rendert sie neu. Abgleich von Listen mit Schlüsseln (Knoten werden verschoben und behalten Fokus und eingegebenen Text), ein delegierender Listener pro Ereignistyp (Handler werden ersetzt, nie gestapelt), Callback `ref`, keine Möglichkeit, HTML einzuschleusen |
| `store.js` | `createStore( reducer )`: `getState`, `dispatch`, `subscribe` (nur bei geändertem Zustand), `effect`; `combine()` für Teilbereiche |
| `components.js` | jede Komponente ist eine reine Funktion `( state, dispatch ) -> description`; die Routentabelle (Muster, Seite, die Ressourcen, die die Seite braucht) |
| `effects.js` | die einzige Stelle, die Dienste aufruft: `load( key, feature, args )` sendet `res/request`, `res/success` oder `res/failure`; Formular-POSTs, Anmeldung, Warenkorb |
| `app.js` | die Reducer (`route`, `user`, `basket`, `res`, `form`, `lightbox`, `feedOpen`), der Store und die Einhängepunkte (`#main`, `#nav`, `#account-slot`, `#basket-count`) |

Eine Seite holt nie selbst Daten: Der Routen-Reducer speichert die Route, der Effekt liest die `needs` der
Routentabelle und lädt die fehlenden Ressourcen in `state.res[ key ]` (`loading`, `ok`, `error`, `unavailable`), die
Komponente liest das und zeichnet.

### Wie jede Seite auf Dienste abgebildet wird

Die eine Tabelle ist `ExpPortal.services` in `core.js`; `ExpPortal.argMap` wandelt `( node, offset, limit )` des
Portals in die Reihenfolge der Positionsargumente eines Dienstes um. Um einem umbenannten Dienst zu folgen, ändern Sie
nur die Tabelle.

| Seite (Route) | Dienste |
|---|---|
| Startseite `/` | `expnode::children` (Nachrichten), `expproduct::list` (Shop), `expimage::list` (Medien) |
| Nachrichten `/news`, Artikel `/news/:id` | `expnode::children`, `expnode::get` |
| Shop `/shop`, Produkt `/shop/:id`, Warenkorb `/basket` | `expproduct::list`, `expproduct::view`, `expbasket::view`, `expbasket::add`, `expbasket::remove` |
| Foren `/forums`, Themen `/forums/:id`, Thema `/forums/topic/:id` | `expnode::children` (Foren), `expforum::topics`, `expforum::replies`, `expforum::reply`, `expforum::create_topic` |
| Medien `/media` | `expimage::list` |
| Feeds `/feeds` | `expfeed::list`, `expfeed::items` |
| Suche `/search?q=` | `expsearch::search` |
| Anmeldung `/login`, Profil `/profile`, Kopfzeile | `expsession::login`, `expsession::logout`, `expsession::whoami`, `expsession::token`, `expuser::profile` |
| jede Seite | `expservices::catalog` (welche Dienste existieren) |

Dienste für Foren, Feeds, Suche und Profil standen noch nicht im Katalog, als die Designs geschrieben wurden; ihre
Namen sind die Namen des Vertrags, und diese Seiten zeigen den Zustand „nicht verfügbar“, bis die Dienste existieren.
Die Knoten-IDs der Inhaltswurzeln (Nachrichten, Shop, Foren, Medien) stehen in `expportal.ini`; die Standardwerte sind
die Inhaltswurzel (2) und Media (43).

### So führen Sie sie aus

Die Test-Siteaccesses sind lokale Einstellungen (nicht committet): Legen Sie `settings/siteaccess/portaljq` und
`portalreactive` an und fügen Sie sie mit `exp:ini` zu `[SiteAccessSettings] AvailableSiteAccessList` des globalen
Overrides hinzu (umkehrbar: Löschen Sie die beiden Verzeichnisse, und
`php bin/php/console exp:ini rem 'site/SiteAccessSettings/AvailableSiteAccessList[]' <name> override`). Sie werden über
die URI zugeordnet, öffnen Sie daher `https://www.example.com/portaljq/` und `https://www.example.com/portalreactive/`;
der Live-Siteaccess `site` und sein Design bleiben unberührt. `DefaultPage` ist `user/login`, weil dieses Modul
anonymen Benutzern mit 200 antwortet und das Pagelayout seine Ausgabe ersetzt; Velocity-Worker brauchen einen
Neustart, um einen neuen Siteaccess zu sehen, Apache/FPM nicht.

### Tests

Ein Browsertest führt beide Portale bei 960 px mit Gerätefaktor 2 und bei 390 px aus: Jede Seite rendert ihre
Überschrift und keinen unfertigen Zustand, das Menü (und der Menüumschalter auf dem Telefon) navigiert, Suche und
Theme-Umschalter funktionieren, kein horizontales Scrollen, kein Konsolenfehler und keine Warnung von jQuery Migrate
(der Code ist sauber für jQuery 4: kein `$.isArray`, `$.trim` und Ähnliches). Unit-Tests auf der Seite decken den Kern
ab (Formen von Knoten, Zusammenfalten von Listen, Bau von URLs, Behandlung von Nichtverfügbarkeit, Textsicherheit) und
für das reaktive Design das Umsortieren mit Schlüsseln unter Erhalt der Knotenidentität, das Ersetzen von Handlern,
die Textsicherheit, den Store (Reducer, Benachrichtigung nur bei Änderung, Effekte) und die Routentabelle. Das
Browser-Testgerüst von expui (`/expui/test`) ist eine Admin-Seite, die an Admin-Sitzungen gebunden ist, daher laufen die
Portaltests stattdessen in der Playwright-Seite.

## Benutzer und Zugriff (neun Domänen, 195 Dienste)

Klassen in `extension/expservices/classes/users/`, Tests in `tests/tests/extension/expservices/users/` (113 Tests,
gegen die Live-Datenbank ausgeführt; sie legen eine Testgruppe, Testbenutzer, eine Testrolle, Test-Sitzungen und
Kollaborationselemente an und entfernen sie wieder). Benutzer werden über die ID des Inhaltsobjekts angesprochen,
Benutzergruppen über die Knoten-ID, Rollen und Richtlinien über die ID.

Regeln, die für jeden Dienst dieser Domänen gelten:

- Nichts gibt einen Passwort-Hash, einen Aktivierungs- oder Reset-Schlüssel, einen Sitzungsschlüssel oder
  Sitzungsdaten zurück. Anmeldename, E-Mail-Adresse und Kontozustand eines Benutzers werden nur für einen selbst oder
  für einen Benutzer zurückgegeben, den der Aufrufer bearbeiten darf (`content/edit` auf dem Benutzerobjekt); andere
  Aufrufer erhalten den öffentlichen Teil (ID, Name, Knoten, Klasse).
- Schreibvorgänge sind POST mit dem Formular-Token (`expsession::token`), werden gegen die in der Tabelle gezeigte
  Richtlinie geprüft und als `service.<domain>.<method>` verzeichnet sowie, wo der Kernel eines verzeichnet, mit
  dessen eigenem Ereignis `access.*` (Ereignisse zu Rollen, Richtlinien, Benutzern, Passwörtern und Zurücksetzen
  schreibt der Kernel-Code, den die Dienste aufrufen).
- Rollen werden bearbeitet wie im Rolleneditor: `exprole::draftCreate`, `draftAddPolicy`, `draftRemovePolicy`,
  `publish` (oder `draftDiscard`); `exppolicy` ändert die veröffentlichte Rolle direkt und prüft die Namen von Modul,
  Funktion und Einschränkung gegen die Moduldefinition.
- `expaccount` trägt die öffentlichen Abläufe. Die Reset- und Aktivierungsschlüssel werden nur per E-Mail übermittelt;
  ein Aktivierungsschlüssel läuft nach 24 Stunden ab, ein Reset-Schlüssel nach
  `site.ini [UserSettings] ForgotPasswordKeyLifetime` (eine Stunde im Auslieferungszustand), beides über die Dienste
  genauso wie auf der Seite „Passwort vergessen?“; `forgotRequest` antwortet für bekannte und unbekannte Adressen
  gleich. Wo ein Schreibvorgang Besuchern offensteht, ist sein Zugriff die Anmelde- oder Registrierungsrichtlinie des
  Moduls user (der Katalog erlaubt keinen öffentlichen Schreibvorgang).
- Ein Kollaborationselement ist nur für seine Teilnehmer sichtbar (alle anderen erhalten 404); Genehmigen und
  Ablehnen brauchen die Rolle des Genehmigenden und eine wartende Genehmigung und lassen den Workflow genau so
  fortfahren, wie es die eigene View des Kernels tut.
- `expsessionadmin` bezeichnet eine Sitzung mit einer kurzen abgeleiteten Kennung; das Auflisten und Beenden anderer
  Sitzungen braucht `setup/administrate`. Die Zahlen der Sitzungen lesen die Tabelle `ezsession`, die leer ist, wenn der
  Datei-Session-Handler von PHP verwendet wird (`expsessionadmin::settings` sagt, welcher Handler aktiv ist).
Aus großer Macht folgt große Verantwortung.

#### `expuser` (42 Dienste)

| Dienst | Zugriff | Schreiben | Argumente | Zusammenfassung |
|---|---|---|---|---|
| `expuser::current` | public |  |  | Der aktuelle Benutzer: ID, Name, Gruppen und die privaten Daten von einem selbst |
| `expuser::fetch` | user |  | id:int | Ein Benutzer nach ID des Inhaltsobjekts; Anmeldename, E-Mail-Adresse und Zustand nur für einen selbst oder für Benutzer, die der Aufrufer bearbeiten darf |
| `expuser::byLogin` | user |  | login:string | Ein Benutzer nach Anmeldename |
| `expuser::byEmail` | user |  | email:string | Ein Benutzer nach E-Mail-Adresse, nur wenn der Aufrufer die privaten Daten dieses Benutzers sehen darf |
| `expuser::exists` | public |  | login:string | Ob ein Anmeldename vergeben ist |
| `expuser::search` | user |  | query:string, limit:int, offset:int | Benutzer nach Anmeldename oder Name suchen (2 Zeichen oder mehr) |
| `expuser::listAll` | user |  | limit:int, offset:int | Alle Benutzer, seitenweise |
| `expuser::count` | user |  |  | Die Zahl der Benutzer |
| `expuser::listByGroup` | user |  | group:int, limit:int, offset:int | Die Benutzer, die direkt in einer Benutzergruppe sind (Knoten-ID der Gruppe), seitenweise |
| `expuser::loggedIn` | setup/administrate |  | limit:int, offset:int | Die Benutzer mit einer aktiven Sitzung |
| `expuser::loggedInCount` | setup/administrate |  |  | Angemeldete registrierte und anonyme Sitzungen |
| `expuser::isOnline` | user |  | id:int | Ob ein Benutzer eine aktive Sitzung hat |
| `expuser::profile` | user |  | id:int | Die Inhaltsattribute eines Benutzers (nicht das Konto), ohne ID man selbst |
| `expuser::updateProfile` | user | POST | id:int, fields:json | Profilattribute eines Benutzers aktualisieren, den der Aufrufer bearbeiten darf (POST id, fields als JSON) |
| `expuser::create` | user | POST | group:int, login:string, email:string, password:string, fields:json, enabled:bool, class:string | Einen Benutzer in einer Gruppe anlegen (POST group, login, email, password, fields, enabled, class) |
| `expuser::remove` | user | POST | id:int | Einen Benutzer in den Papierkorb verschieben (POST id) |
| `expuser::enable` | user | POST | id:int | Ein Konto aktivieren (POST id) |
| `expuser::disable` | user | POST | id:int | Ein Konto deaktivieren und seine Sitzungen beenden (POST id) |
| `expuser::unlock` | user | POST | id:int | Den Zähler fehlgeschlagener Anmeldungen zurücksetzen (POST id) |
| `expuser::setPassword` | user | POST | id:int, password:string | Das Passwort eines anderen Benutzers setzen, beendet seine Sitzungen (POST id, password) |
| `expuser::changeEmail` | user | POST | id:int, email:string | Die E-Mail-Adresse eines Benutzers ändern, den der Aufrufer bearbeiten darf (POST id, email) |
| `expuser::changeLogin` | user | POST | id:int, login:string | Den Anmeldenamen eines Benutzers ändern, den der Aufrufer bearbeiten darf (POST id, login) |
| `expuser::loginInfo` | user |  | id:int | Zahl der Anmeldungen, letzter Besuch, fehlgeschlagene Versuche, Sperre und Online-Zustand |
| `expuser::lastVisit` | user |  | id:int | Der letzte Besuch eines Benutzers |
| `expuser::settings` | user |  | id:int | Kontoeinstellungen: aktiviert und die Höchstzahl der Anmeldungen |
| `expuser::setMaxLogin` | user | POST | id:int, max_login:int | Die Höchstzahl gleichzeitiger Anmeldungen ändern (POST id, max_login) |
| `expuser::validateLogin` | public |  | login:string | Einen Anmeldenamen gegen die Validierungsregeln prüfen und ob er vergeben ist |
| `expuser::validatePassword` | public |  | password:string | Ein Passwort gegen die Längenregel prüfen |
| `expuser::passwordPolicy` | public |  |  | Die Regeln der Website für Passwörter und E-Mail-Adressen |
| `expuser::generatePassword` | user |  | length:int | Ein generiertes Passwort |
| `expuser::rolesOf` | role/list |  | id:int | Die Rollen, die ein Benutzer hat, direkt und über Gruppen |
| `expuser::groupsOf` | user |  | id:int | Die Gruppen, in denen ein Benutzer Mitglied ist |
| `expuser::effectivePolicies` | role/list |  | id:int, limit:int, offset:int | Der wirksame Zugriff eines Benutzers auf Modul/Funktion, seitenweise |
| `expuser::limitations` | role/list |  | id:int | Die eingeschränkten Rollenzuweisungen eines Benutzers |
| `expuser::hasAccess` | public |  | module:string, function:string | Ob der aktuelle Benutzer Zugriff auf ein Modul und eine Funktion hat |
| `expuser::hasAccessOf` | role/list |  | id:int, module:string, function:string | Ob ein bestimmter Benutzer Zugriff auf ein Modul und eine Funktion hat |
| `expuser::canNode` | public |  | node:int | Die Rechte des aktuellen Benutzers auf einem Knoten: lesen, bearbeiten, entfernen, anlegen, verschieben, verbergen, übersetzen |
| `expuser::accessSummary` | user |  |  | Die Module und Funktionen, auf die der aktuelle Benutzer irgendeinen Zugriff hat |
| `expuser::canLoginTo` | role/list |  | id:int, siteaccess:string | Ob sich ein Benutzer an einem Siteaccess anmelden darf |
| `expuser::hashTypes` | user |  |  | Die Typen von Passwort-Hashes und der Standard |
| `expuser::userClasses` | user |  |  | Die Kennungen der Inhaltsklassen von Benutzern und Benutzergruppen |
| `expuser::anonymous` | public |  |  | Die ID des anonymen Benutzers |

#### `expusergroup` (20 Dienste)

| Dienst | Zugriff | Schreiben | Argumente | Zusammenfassung |
|---|---|---|---|---|
| `expusergroup::root` | user |  |  | Der Wurzelknoten der Benutzer, die Standardplatzierung und die Gruppenklassen |
| `expusergroup::tree` | user |  | group:int, depth:int | Der Gruppenbaum unter einem Knoten bis zu einer Tiefe (Standard: die Wurzel der Benutzer, Tiefe 3) |
| `expusergroup::fetch` | user |  | group:int | Eine Benutzergruppe nach Knoten-ID |
| `expusergroup::children` | user |  | group:int, limit:int, offset:int | Die Untergruppen einer Gruppe, seitenweise |
| `expusergroup::subgroupCount` | user |  | group:int | Die Zahl der Untergruppen |
| `expusergroup::path` | user |  | group:int | Der Pfad von der Wurzel zu einer Gruppe |
| `expusergroup::parent` | user |  | group:int | Die Elterngruppe |
| `expusergroup::search` | user |  | query:string, limit:int, offset:int | Gruppen nach Name suchen |
| `expusergroup::members` | user |  | group:int, limit:int, offset:int | Die Benutzer, die direkt in einer Gruppe sind, seitenweise |
| `expusergroup::memberCount` | user |  | group:int | Die Zahl der Benutzer, die direkt in einer Gruppe sind |
| `expusergroup::isMember` | user |  | user:int, group:int | Ob ein Benutzer direkt in einer Gruppe ist |
| `expusergroup::ofUser` | user |  | id:int | Die Gruppen eines Benutzers |
| `expusergroup::rolesOf` | role/list |  | group:int | Die einer Gruppe zugewiesenen Rollen |
| `expusergroup::create` | user | POST | parent:int, name:string, class:string, fields:json | Eine Gruppe anlegen (POST parent, name, class, fields) |
| `expusergroup::rename` | user | POST | group:int, name:string | Eine Gruppe umbenennen (POST group, name) |
| `expusergroup::remove` | user | POST | group:int, force:bool | Eine Gruppe in den Papierkorb verschieben (POST group, force bei einer Gruppe mit Kindern) |
| `expusergroup::addMember` | user | POST | group:int, user:int | Einen Benutzer zu einer Gruppe hinzufügen, eine neue Platzierung (POST group, user) |
| `expusergroup::removeMember` | user | POST | group:int, user:int | Einen Benutzer aus einer Gruppe entfernen, nie aus der letzten (POST group, user) |
| `expusergroup::moveMember` | user | POST | user:int, from:int, to:int | Einen Benutzer von einer Gruppe in eine andere verschieben (POST user, from, to) |
| `expusergroup::addMembers` | user | POST | group:int, users:list | Bis zu 100 Benutzer zu einer Gruppe hinzufügen (POST group, users) |

#### `exprole` (26 Dienste)

| Dienst | Zugriff | Schreiben | Argumente | Zusammenfassung |
|---|---|---|---|---|
| `exprole::listAll` | role/list |  | limit:int, offset:int | Alle Rollen, seitenweise |
| `exprole::count` | role/list |  |  | Die Zahl der Rollen |
| `exprole::fetch` | role/list |  | id:int | Eine Rolle nach ID |
| `exprole::view` | role/view |  | id:int | Eine Rolle mit ihren Richtlinien und Zuweisungen |
| `exprole::byName` | role/list |  | name:string | Eine Rolle nach Name |
| `exprole::exists` | role/list |  | name:string | Ob ein Rollenname existiert |
| `exprole::policies` | role/view |  | id:int, limit:int, offset:int | Die Richtlinien einer Rolle, seitenweise |
| `exprole::policyCount` | role/view |  | id:int | Die Zahl der Richtlinien einer Rolle |
| `exprole::assignments` | role/view |  | id:int, limit:int, offset:int | Wem eine Rolle zugewiesen ist, mit Einschränkungen |
| `exprole::assignmentsOf` | role/view |  | object:int | Die einem Benutzer- oder Gruppenobjekt zugewiesenen Rollen |
| `exprole::byLimitation` | role/view |  | identifier:string, value:string | Mit einer Einschränkung zugewiesene Rollen (Subtree/Section, Wert) |
| `exprole::create` | role/edit | POST | name:string | Eine leere Rolle anlegen (POST name) |
| `exprole::rename` | role/edit | POST | id:int, name:string | Eine Rolle umbenennen (POST id, name) |
| `exprole::copy` | role/edit | POST | id:int, name:string | Eine Rolle mit ihren Richtlinien kopieren (POST id, optional name) |
| `exprole::remove` | role/edit | POST | id:int, force:bool | Eine Rolle entfernen (POST id, force, wenn sie zugewiesen ist) |
| `exprole::draftCreate` | role/edit | POST | id:int | Mit dem Bearbeiten beginnen: den Entwurf einer Rolle anlegen (oder zurückgeben) (POST id) |
| `exprole::draft` | role/edit |  | id:int | Der Entwurf einer Rolle |
| `exprole::draftRename` | role/edit | POST | id:int, name:string | Den Entwurf umbenennen (POST id, name) |
| `exprole::draftAddPolicy` | role/edit | POST | id:int, module:string, function:string, limitations:json | Dem Entwurf eine Richtlinie hinzufügen (POST id, module, function, limitations als JSON) |
| `exprole::draftRemovePolicy` | role/edit | POST | id:int, policy:int | Eine Richtlinie aus dem Entwurf entfernen (POST id, policy) |
| `exprole::draftDiscard` | role/edit | POST | id:int | Den Entwurf verwerfen (POST id) |
| `exprole::publish` | role/edit | POST | id:int | Den Entwurf veröffentlichen: Er ersetzt die Richtlinien der Rolle (POST id) |
| `exprole::assign` | role/assign | POST | id:int, object:int, limit:string, limit_value:string | Einem Benutzer oder einer Gruppe eine Rolle zuweisen (POST id, object, limit subtree/section, limit_value) |
| `exprole::unassign` | role/assign | POST | id:int, object:int, assignment:int | Eine Zuweisung entfernen (POST id, object, optional ID der Zuweisung) |
| `exprole::assigned` | role/view |  | id:int | Die Objekte (Benutzer, Gruppen), denen eine Rolle zugewiesen ist |
| `exprole::usersWith` | role/view |  | id:int, limit:int, offset:int | Die Benutzer und Gruppen, die eine Rolle haben |

#### `exppolicy` (16 Dienste)

| Dienst | Zugriff | Schreiben | Argumente | Zusammenfassung |
|---|---|---|---|---|
| `exppolicy::fetch` | role/view |  | id:int | Eine Richtlinie mit ihren Einschränkungen |
| `exppolicy::listOfRole` | role/view |  | id:int, limit:int, offset:int | Die Richtlinien einer Rolle, seitenweise |
| `exppolicy::limitations` | role/view |  | id:int | Die Einschränkungen einer Richtlinie, Kennung => Werte |
| `exppolicy::summary` | role/view |  | id:int | Eine Richtlinie als eine Textzeile |
| `exppolicy::add` | role/edit | POST | role:int, module:string, function:string, limitations:json | Einer Rolle eine Richtlinie hinzufügen, gegen das Modul geprüft (POST role, module, function, limitations) |
| `exppolicy::remove` | role/edit | POST | id:int | Eine Richtlinie entfernen (POST id) |
| `exppolicy::setLimitations` | role/edit | POST | id:int, limitations:json | Alle Einschränkungen einer Richtlinie ersetzen (POST id, limitations) |
| `exppolicy::addLimitation` | role/edit | POST | id:int, identifier:string, values:list | Eine Einschränkung hinzufügen (POST id, identifier, values) |
| `exppolicy::removeLimitation` | role/edit | POST | id:int, identifier:string | Eine Einschränkung entfernen (POST id, identifier) |
| `exppolicy::copy` | role/edit | POST | id:int, role:int | Eine Richtlinie in eine andere Rolle kopieren (POST id, role) |
| `exppolicy::modules` | role/view |  |  | Die Module, die eine Richtlinie nennen kann |
| `exppolicy::functions` | role/view |  | module:string | Die Funktionen eines Moduls |
| `exppolicy::availableLimitations` | role/view |  | module:string, function:string | Die Kennungen der Einschränkungen einer Modulfunktion |
| `exppolicy::limitationValues` | role/view |  | module:string, function:string, identifier:string, limit:int, offset:int | Die Werte, die eine Einschränkung annehmen kann, seitenweise |
| `exppolicy::ofModule` | role/view |  | module:string, limit:int, offset:int | Die Richtlinien, die ein Modul nennen, über alle Rollen |
| `exppolicy::findByLimitation` | role/view |  | identifier:string, value:string, limit:int, offset:int | Die Richtlinien mit einer Kennung und einem Wert einer Einschränkung |

#### `expsessionadmin` (16 Dienste)

| Dienst | Zugriff | Schreiben | Argumente | Zusammenfassung |
|---|---|---|---|---|
| `expsessionadmin::own` | public |  |  | Die aktuelle Sitzung: Benutzer, gestartet, Cookie, kurze Kennung |
| `expsessionadmin::ownList` | user |  | limit:int, offset:int | Die aktiven Sitzungen des aktuellen Benutzers (andere Geräte) |
| `expsessionadmin::ownCount` | user |  |  | Die Zahl der aktiven Sitzungen des aktuellen Benutzers |
| `expsessionadmin::ownRemove` | user | POST | id:string | Eine Ihrer eigenen Sitzungen beenden (POST id) |
| `expsessionadmin::ownRemoveOthers` | user | POST |  | Alle Ihre Sitzungen außer dieser beenden (POST) |
| `expsessionadmin::regenerate` | user | POST |  | Einen neuen Sitzungsschlüssel für diese Sitzung ausgeben (POST) |
| `expsessionadmin::listAll` | setup/administrate |  | limit:int, offset:int, registered:bool | Die aktiven Sitzungen der Installation, seitenweise |
| `expsessionadmin::byUser` | setup/administrate |  | id:int, limit:int, offset:int | Die Sitzungen eines Benutzers |
| `expsessionadmin::count` | setup/administrate |  |  | Aktive, abgelaufene und alle Sitzungen |
| `expsessionadmin::stats` | setup/administrate |  |  | Registrierte und anonyme Sitzungen und abgelaufene |
| `expsessionadmin::expiredCount` | setup/administrate |  |  | Die Zahl der abgelaufenen Sitzungen |
| `expsessionadmin::removeExpired` | setup/administrate | POST |  | Abgelaufene Sitzungen löschen (POST) |
| `expsessionadmin::remove` | setup/administrate | POST | id:string | Eine Sitzung über ihre Kennung beenden (POST id) |
| `expsessionadmin::removeByUser` | setup/administrate | POST | id:int | Alle Sitzungen eines Benutzers außer Ihrer eigenen beenden (POST id) |
| `expsessionadmin::settings` | setup/administrate |  |  | Session-Handler und Timeouts |
| `expsessionadmin::handler` | setup/administrate |  |  | Die Klasse des Session-Handlers und der Save-Handler von PHP |

#### `exppreferences` (13 Dienste)

| Dienst | Zugriff | Schreiben | Argumente | Zusammenfassung |
|---|---|---|---|---|
| `exppreferences::get` | user |  | name:string | Eine Einstellung des aktuellen Benutzers |
| `exppreferences::getMany` | user |  | names:list | Mehrere Einstellungen des aktuellen Benutzers (kommagetrennte Namen) |
| `exppreferences::listAll` | user |  | limit:int, offset:int | Alle Einstellungen des aktuellen Benutzers, seitenweise |
| `exppreferences::byPrefix` | user |  | prefix:string, limit:int, offset:int | Die Einstellungen, deren Name mit einem Präfix beginnt |
| `exppreferences::exists` | user |  | name:string | Ob eine Einstellung gesetzt ist |
| `exppreferences::count` | user |  |  | Die Zahl der Einstellungen des aktuellen Benutzers |
| `exppreferences::set` | user | POST | name:string, value:string | Eine Einstellung setzen (POST name, value) |
| `exppreferences::setMany` | user | POST | values:json | Bis zu 100 Einstellungen setzen (POST values als JSON) |
| `exppreferences::remove` | user | POST | name:string | Eine Einstellung entfernen (POST name) |
| `exppreferences::increment` | user | POST | name:string, by:int | Zu einer numerischen Einstellung addieren (POST name, by) |
| `exppreferences::listOf` | setup/administrate |  | id:int, limit:int, offset:int | Die Einstellungen eines anderen Benutzers |
| `exppreferences::getOf` | setup/administrate |  | id:int, name:string | Eine Einstellung eines anderen Benutzers |
| `exppreferences::setOf` | setup/administrate | POST | user:int, name:string, value:string | Eine Einstellung eines anderen Benutzers setzen (POST user, name, value) |

#### `expnotification` (21 Dienste)

| Dienst | Zugriff | Schreiben | Argumente | Zusammenfassung |
|---|---|---|---|---|
| `expnotification::settings` | notification/use |  |  | Einstellungen der Zusammenfassung, Zahl der Abonnements und Kollaborationstypen des aktuellen Benutzers |
| `expnotification::digestTypes` | notification/use |  |  | Die Typen der Zusammenfassung |
| `expnotification::setDigest` | notification/use | POST | receive_digest:bool, digest_type:int, day:string, time:string | Die Einstellungen der Zusammenfassung setzen (POST receive_digest, digest_type, day, time) |
| `expnotification::subscriptions` | notification/use |  | limit:int, offset:int | Die Knoten, die der aktuelle Benutzer abonniert hat, seitenweise |
| `expnotification::subscriptionCount` | notification/use |  |  | Die Zahl der Abonnements |
| `expnotification::isSubscribed` | notification/use |  | node:int | Ob der aktuelle Benutzer einen Knoten abonniert hat |
| `expnotification::subscribe` | notification/use | POST | node:int, use_digest:bool | Einen Knoten und seinen Teilbaum abonnieren (POST node, use_digest) |
| `expnotification::unsubscribe` | notification/use | POST | node:int | Das Abonnement eines Knotens beenden (POST node) |
| `expnotification::unsubscribeAll` | notification/use | POST |  | Alle Abonnements des aktuellen Benutzers entfernen (POST) |
| `expnotification::setDigestForSubscription` | notification/use | POST | node:int, use_digest:bool | Die Zusammenfassung für ein Abonnement ein- oder ausschalten (POST node, use_digest) |
| `expnotification::collaborationTypes` | notification/use |  |  | Die Benachrichtigungstypen der Kollaboration, die der aktuelle Benutzer erhält |
| `expnotification::subscribeCollaboration` | notification/use | POST | type:string | Benachrichtigungen eines Kollaborationstyps erhalten (POST type) |
| `expnotification::unsubscribeCollaboration` | notification/use | POST | type:string | Keine Benachrichtigungen eines Kollaborationstyps mehr erhalten (POST type) |
| `expnotification::handlers` | notification/administrate |  |  | Die Benachrichtigungs-Handler |
| `expnotification::eventTypes` | notification/administrate |  |  | Die verfügbaren Typen von Benachrichtigungsereignissen |
| `expnotification::subscriptionsOf` | notification/administrate |  | id:int, limit:int, offset:int | Die Abonnements eines Benutzers |
| `expnotification::subscribeUser` | notification/administrate | POST | user:int, node:int, use_digest:bool | Einen Benutzer für einen Knoten anmelden (POST user, node, use_digest) |
| `expnotification::unsubscribeUser` | notification/administrate | POST | user:int, node:int | Das Abonnement eines Benutzers für einen Knoten beenden (POST user, node) |
| `expnotification::subscribers` | notification/administrate |  | node:int, limit:int, offset:int | Die Benutzer, die einen Knoten abonniert haben |
| `expnotification::events` | notification/administrate |  | limit:int, offset:int | Die Benachrichtigungsereignisse, die neuesten zuerst, seitenweise |
| `expnotification::queue` | notification/administrate |  |  | Die Größe der Benachrichtigungswarteschlange |

#### `expcollaboration` (22 Dienste)

| Dienst | Zugriff | Schreiben | Argumente | Zusammenfassung |
|---|---|---|---|---|
| `expcollaboration::items` | user |  | limit:int, offset:int, status:string, is_read:bool, group:int | Die Kollaborationselemente des aktuellen Benutzers, seitenweise (status active/inactive/archive, is_read, group) |
| `expcollaboration::itemCount` | user |  | status:string, is_read:bool, group:int | Die Zahl der Elemente, mit denselben Filtern |
| `expcollaboration::summary` | user |  |  | Anzahlen von aktiven, ungelesenen, archivierten und auf Genehmigung wartenden Elementen |
| `expcollaboration::fetch` | user |  | id:int | Ein Element, für Teilnehmer |
| `expcollaboration::messages` | user |  | id:int, limit:int, offset:int | Die Nachrichten eines Elements, seitenweise |
| `expcollaboration::messageCount` | user |  | id:int | Die Zahl der Nachrichten und der ungelesenen Nachrichten eines Elements |
| `expcollaboration::addMessage` | user | POST | item:int, text:string | Einem Element eine Nachricht hinzufügen (POST item, text) |
| `expcollaboration::participants` | user |  | id:int | Die Teilnehmer eines Elements mit Typ und Rolle |
| `expcollaboration::markRead` | user | POST | item:int | Ein Element als gelesen markieren (POST item) |
| `expcollaboration::setActive` | user | POST | item:int, active:bool | Ein Element in der Liste des aktuellen Benutzers zeigen oder verbergen (POST item, active) |
| `expcollaboration::handlers` | user |  |  | Die aktiven Kollaborations-Handler |
| `expcollaboration::groups` | user |  | limit:int, offset:int | Die Kollaborationsgruppen des aktuellen Benutzers |
| `expcollaboration::groupInfo` | user |  | id:int | Eine Kollaborationsgruppe |
| `expcollaboration::groupItems` | user |  | id:int, limit:int, offset:int | Die Elemente einer Gruppe, seitenweise |
| `expcollaboration::groupCreate` | user | POST | title:string, parent:int | Eine Gruppe anlegen (POST title, parent) |
| `expcollaboration::groupRename` | user | POST | group:int, title:string | Eine Gruppe umbenennen (POST group, title) |
| `expcollaboration::groupRemove` | user | POST | group:int | Eine leere Gruppe entfernen (POST group) |
| `expcollaboration::moveToGroup` | user | POST | item:int, group:int | Ein Element in eine Gruppe legen (POST item, group) |
| `expcollaboration::pendingApprovals` | user |  | limit:int, offset:int | Die Genehmigungen, die auf den aktuellen Benutzer warten |
| `expcollaboration::approvalStatus` | user |  | id:int | Der Zustand eines Genehmigungselements und ob der aktuelle Benutzer entscheiden darf |
| `expcollaboration::approve` | user | POST | item:int, comment:string | Ein wartendes Element als Genehmigender genehmigen (POST item, comment) |
| `expcollaboration::deny` | user | POST | item:int, comment:string | Ein wartendes Element als Genehmigender ablehnen (POST item, comment) |

#### `expaccount` (19 Dienste)

| Dienst | Zugriff | Schreiben | Argumente | Zusammenfassung |
|---|---|---|---|---|
| `expaccount::registrationOptions` | public |  |  | Ob die Registrierung offen ist und wie sie bestätigt wird |
| `expaccount::register` | user/register | POST | login:string, email:string, password:string, fields:json | Ein neues Konto registrieren (POST login, email, password, fields) |
| `expaccount::activationKeyValid` | public |  | key:string | Ob ein Aktivierungsschlüssel gültig ist |
| `expaccount::activate` | user/login | POST | key:string | Ein Konto mit dem Schlüssel aus der Aktivierungs-E-Mail aktivieren (POST key) |
| `expaccount::forgotRequest` | user/login | POST | email:string | Einen Link zum Zurücksetzen des Passworts senden, mit derselben Antwort für bekannte und unbekannte Adressen (POST email) |
| `expaccount::forgotKeyValid` | public |  | key:string | Ob ein Reset-Schlüssel gültig ist |
| `expaccount::reset` | user/login | POST | key:string, password:string | Mit einem Reset-Schlüssel ein neues Passwort setzen (POST key, password) |
| `expaccount::changePassword` | user | POST | old_password:string, new_password:string, confirm_password:string | Ihr Passwort mit dem alten ändern (POST old_password, new_password, confirm_password) |
| `expaccount::changeEmail` | user | POST | password:string, email:string | Ihre E-Mail-Adresse ändern, bestätigt mit dem Passwort (POST password, email) |
| `expaccount::me` | user |  |  | Ihr Konto: Profildaten, Gruppen, Zahl der Rollen |
| `expaccount::myRoles` | user |  |  | Ihre Rollen |
| `expaccount::myGroups` | user |  |  | Ihre Gruppen |
| `expaccount::myLoginInfo` | user |  |  | Ihre Zahl der Anmeldungen, Ihr letzter Besuch und fehlgeschlagene Versuche |
| `expaccount::updateMyProfile` | user | POST | fields:json | Ihre eigenen Profilattribute aktualisieren (POST fields) |
| `expaccount::unactivated` | role/list |  | limit:int, offset:int | Die Konten, die auf Aktivierung warten, seitenweise |
| `expaccount::unactivatedCount` | role/list |  |  | Die Zahl der Konten, die auf Aktivierung warten |
| `expaccount::activateUser` | user | POST | id:int | Ein Konto von Hand aktivieren (POST id) |
| `expaccount::pendingResets` | role/list |  |  | Die Zahl der offenen Anfragen zum Zurücksetzen von Passwörtern |
| `expaccount::cancelReset` | user | POST | id:int | Die offenen Anfragen zum Zurücksetzen eines Benutzers abbrechen (POST id) |

Insgesamt: 195 Dienste.

## Handel, Community und Feeds (16 Domänen, 256 Dienste)

Klassen in `extension/expservices/classes/commerce/` und `classes/community/`, Tests in
`tests/tests/extension/expservices/commerce/` und `community/` (151 Tests, wie bei den anderen Domänen gegen die
Live-Datenbank ausgeführt: Testprodukte, Foren, Umfragen, Formulare und Exporte werden in einem Testordner unter der
Wurzel Media (Knoten 43) oder als benannte Testzeilen angelegt und in `tearDown` entfernt; Warenkörbe existieren nur für
einen Sitzungsschlüssel eines Tests; die Checkout-Tests legen die temporäre Bestellung eines Test-Warenkorbs an und
brechen sie ab, keine Bestellung wird abgeschlossen und keine Zahlung angelegt; eine Testdatenbank gibt es nie).
Ausführen: `php vendor/bin/phpunit tests/tests/extension/expservices/commerce/ tests/tests/extension/expservices/community/`.

Regeln, die für jeden Dienst dieser Domänen gelten:

- **Der Shop arbeitet auf dem Kernel.** Produkte sind Inhaltsobjekte, deren Klasse ein Preisattribut hat (`ezprice`,
  `ezmultiprice`; `expproduct::classes`). Preise kommen mit und ohne MwSt., mit dem Rabatt des Produkts und, wenn eine
  Währung angefragt wird (`USD`, `EUR` ...), umgerechnet mit den Kursen der Währungstabelle (`in_currency`; `null`, wenn
  ein Kurs fehlt). Die Dienste für Warenkorb, Wunschliste, Bestellungen, MwSt., Währung, Rabatt und Zahlung rufen
  `eZBasket`, `eZOrder`, `eZVatType`/`eZVatRule`, `eZCurrencyData`, `eZDiscountRule`, `eZWishList`, `eZPaymentObject`
  und die Operationen von `shop` (`addtobasket`, `confirmorder`, `checkout`) auf, sodass sich Workflows, Trigger und die
  eigenen Audit-Ereignisse des Kernels (`commerce.order.status`, `.archive`, `.unarchive`, `.delete`,
  `commerce.vat.change`, `.currency.change`, `.discount.change`, `.payment.approve`, `.basket.checkout`) genau wie in
  den Shop-Views verhalten.
- **Der Warenkorb gehört zur Sitzung.** Eine entfernte App behält ihr Sitzungs-Cookie (`expsession::login`); ohne eines
  antworten Lesevorgänge mit einem leeren Warenkorb und Schreibvorgänge mit 401. Der Checkout erfolgt in Schritten:
  `checkoutStatus` (was blockiert), `startCheckout` (die temporäre Bestellung, nichts wird berechnet), `review` (Versand
  und MwSt. wie im Bestätigungsschritt), `cancelCheckout` und, nur wenn der Client es so meint, `placeOrder`, das die
  Operation `checkout` ausführt: Sie aktiviert die Bestellung oder übergibt an das Zahlungs-Gateway (`redirect_url`).
  Nichts anderes schließt eine Bestellung ab.
- **Bestellungen**: Ein Kunde liest seine eigenen (`exporder::mine`, `myOrder`, `myReceipt`); die
  Shop-Administratoren (`shop/administrate`) listen, sehen, durchsuchen, archivieren, holen zurück und löschen jede
  Bestellung, ändern den Status im Rahmen ihrer Richtlinie `shop/setstatus` (die Einschränkungen
  `FromStatus`/`ToStatus` wenden `statusOptions` und `setStatus` an) und lesen Statistiken, Kunden und das Dashboard.
  Kontodaten gibt `exporder::account` nur für Administratoren zurück; Karten- oder Kontonummern speichert der Kernel nie
  und gibt sie nie zurück.
- **Geldbeträge** sind auf vier Nachkommastellen gerundete Zahlen, MwSt.-Prozentsätze Zahlen, Währungen
  Drei-Buchstaben-Codes, Zeitangaben ISO 8601 in UTC.
- **Foren, Themen, Antworten, Kommentare, Bewertungen und Umfragen sind Inhaltsdienste** über die Klassen, die in
  `settings/expservices.ini` `[Community]` konfiguriert sind (`ForumClass=forum`, `TopicClass=forum_topic`,
  `ReplyClass=forum_reply`, `CommentClass=comment`, `PollClass=poll`, `ReviewClass=review`,
  `TopicStickyAttribute=sticky`), sodass eine Website mit anderen Klassenkennungen die INI ändert, nicht den Code.
  Anlegen, Bearbeiten und Entfernen folgen den Inhaltsrichtlinien des Aufrufers (die Einschränkung Owner lässt
  Mitglieder ihre eigenen Beiträge bearbeiten und entfernen); die Textfelder einer Klasse werden als POST-Felder mit
  dem Namen des Attributs oder als ein JSON-Objekt `fields` geschrieben (Rich-Text-Attribute nehmen einfachen Text an
  und speichern Absätze); unbekannte Felder werden abgelehnt (422), Pflichtfelder geprüft. Moderation ist
  `hide`/`unhide` (Richtlinie `content/hide`), `hidden` listet die verborgenen Kommentare, ein angeheftetes Thema bleibt
  oben, ein Thema kann in ein anderes Forum verschoben werden. Entfernen geht in den Papierkorb.
- **Gesammelte Formulare und Umfragen** verwenden die Informationssammlung des Kernels. `expinfocollection::submit` tut,
  was `content/collectinformation` tut (Validierung durch den Datentyp, anonyme Sammlung und die Behandlung von
  unique/overwrite/multiple aus `collect.ini`), für Text-, Zahlen-, Boolesche und Auswahlfelder; Lesen,
  Zusammenfassung, CSV-Export und Entfernen brauchen die Richtlinie `infocollector/read` (die CSV entschärft Formeln).
  Eine Stimme in einer Umfrage ist eine Sammlung: `exppoll::vote` antwortet mit den Ergebnissen; die Umfrageklasse ist
  in der Standard-`collect.ini` pro Benutzer `unique`.
- **Feeds**: `expfeed::exports` und `output` liefern die RSS-Exporte der Website so aus, wie der Leser sie erhält
  (RSS 1.0, 2.0, Atom, OPML, iTunes; `data.content_type` und `data.content`), `rss`, `atom` und `json` bauen RSS 2.0,
  Atom 1.0 und JSON Feed 1.1 aus den neuesten Inhalten unter jedem Knoten, den der Aufrufer lesen darf (`classes`
  beschränkt die Klassen, `limit` ist auf 100 begrenzt), `discover` listet die Feed-Adressen eines Knotens. Die
  Verwaltung der Exporte (`rss/edit`) legt aktive Exporte direkt an (die Entwurfs-/Veröffentlichungskopie des
  Admin-Editors wird nicht verwendet); RSS-Importe werden gelesen (`imports`, `importStatus`, `importCheck`) und
  geschaltet (`importSetActive`); `runImport` (`rss/edit`, POST) führt einen Import mit dem Code des Cronjobs
  `rssimport` aus (holen, die neuen Einträge unter dem Ziel anlegen und veröffentlichen, einmal pro Eintrag, erkannt am
  Link oder an der guid des Eintrags) und antwortet mit `found`, `new`, `created` und einer Zeile pro Eintrag
  (`imported`, `would_import`, `exists`, `no_identifier`, `skipped_max_items`) sowie dem Log, das der Cronjob ausgeben
  würde. Mit `dry_run=1` wird nichts angelegt: Es berichtet nur, was importiert würde. `max_items` begrenzt die neuen
  Einträge eines Laufs (0 = alle). Der Aufrufer braucht außerdem das Recht, unter dem Zielknoten anzulegen, ein Import
  im Entwurf kann nicht laufen (409), und `importCheck` und `runImport` holen nur `http`-/`https`-Adressen. Ein echter
  Lauf verzeichnet `data.import.rss`, wie es der Cronjob tut, zusätzlich zu `service.feed.runimport`.
- Schreibvorgänge sind POST mit dem Formular-Token (`expsession::token`) und werden als `service.<domain>.<method>`
  verzeichnet; wo der Kernel für die Änderung ein eigenes Ereignis verzeichnet, wird auch dieses verzeichnet.
- Bekannter Fehler im Kernel, umgangen: `eZVatRule::removeVatRule()` ruft eine Instanzmethode statisch auf, was PHP 8
  ablehnt; `expvat::removeRule` und `removeType` entfernen die Regeln selbst (mit demselben Audit-Ereignis).

#### Shop

##### Produkte (`expproduct`, 19 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `classes` | public |  | Die Produktklassen: Inhaltsklassen mit einem Preisattribut |
| `list` | public | `parent_node_id:int`, `limit:int`, `offset:int`, `currency:string`, `sort:string name/published/price` | Die Produkte unter einem Knoten, die neuesten zuerst oder nach Name, mit Preisen (optional in einer Währung) |
| `count` | public | `parent_node_id:int` | Wie viele Produkte unter einem Knoten liegen |
| `search` | public | `text:string`, `limit:int`, `offset:int`, `currency:string` | Produkte nach Wörtern in Name, Nummer oder Beschreibung |
| `view` | public | `node_id:int`, `currency:string` | Ein Produkt nach Knoten-ID mit seinen Feldern, Preisen, Optionen |
| `viewByObject` | public | `object_id:int`, `currency:string` | Ein Produkt nach Objekt-ID |
| `isProduct` | public | `node_id:int` | Ob ein Knoten ein Produkt ist |
| `price` | public | `node_id:int`, `currency:string` | Der Preis eines Produkts, optional in eine Währung umgerechnet |
| `prices` | public | `node_id:int` | Der Preis eines Produkts in jeder aktiven Währung mit einem Kurs |
| `options` | public | `node_id:int` | Die Optionen (ezoption/ezmultioption), die ein Käufer wählen kann, mit Aufpreisen |
| `variations` | public | `node_id:int`, `limit:int` | Jede Kombination der Optionsgruppen mit ihrem resultierenden Preis |
| `optionPrice` | public | `node_id:int`, `choices:json` | Der Preis eines Produkts mit gewählten Optionen (ID des Optionsattributs => ID der Auswahl als JSON) |
| `latest` | public | `limit:int`, `currency:string` | Die zuletzt veröffentlichten Produkte |
| `categories` | public | `parent_node_id:int` | Die Produktkategorien: Knoten unter einem Knoten, die Produkte enthalten, mit der Zahl ihrer Produkte |
| `priceRange` | public | `parent_node_id:int` | Der niedrigste, höchste und durchschnittliche Preis unter einem Knoten |
| `byNumber` | public | `number:string`, `currency:string` | Ein Produkt nach seiner Produktnummer |
| `related` | public | `node_id:int`, `limit:int` | Andere Produkte desselben Elternknotens |
| `vat` | public | `node_id:int` | Die MwSt. eines Produkts: der gewählte Typ und der Prozentsatz |
| `setPrice` (POST) | `content/edit` | `node_id:int`, `price:string POST` | Setzt den Preis eines Produkts (veröffentlicht eine neue Version) |

##### Warenkorb und Checkout (`expbasket`, 25 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `view` | public |  | Der Warenkorb dieser Sitzung: Zeilen, Optionen, Summen pro MwSt.-Satz, Währung |
| `items` | public |  | Die Zeilen des Warenkorbs |
| `item` | public | `item_id:int` | Eine Zeile des Warenkorbs |
| `count` | public |  | Zahl der Zeilen und Einheiten im Warenkorb |
| `totals` | public |  | Summen mit und ohne MwSt., MwSt. pro Satz, Versand |
| `isEmpty` | public |  | Ob der Warenkorb keine Zeilen hat |
| `currency` | public |  | Die Währung des Warenkorbs und die bevorzugte des Benutzers |
| `canAdd` | public | `object_id:int` | Ob ein Produkt in diesen Warenkorb gelegt werden kann (Typ und Währung kompatibel) |
| `add` (POST) | `shop/buy` | `object_id:int POST`, `quantity:int POST`, `options:json POST` | Legt ein Produkt in den Warenkorb (POST-Felder object_id, quantity, options als JSON ID des Attributs => ID der Auswahl) |
| `addNode` (POST) | `shop/buy` | `node_id:int POST`, `quantity:int POST`, `options:json POST` | Legt ein Produkt über seine Knoten-ID in den Warenkorb |
| `update` (POST) | `shop/buy` | `item_id:int POST`, `quantity:int POST` | Setzt die Menge einer Zeile (0 entfernt sie) |
| `updateMany` (POST) | `shop/buy` | `quantities:json POST` | Setzt die Mengen mehrerer Zeilen: JSON-Objekt ID des Eintrags => Menge |
| `remove` (POST) | `shop/buy` | `item_id:int POST` | Entfernt eine Zeile |
| `empty` (POST) | `shop/buy` |  | Entfernt jede Zeile |
| `refreshPrices` (POST) | `shop/buy` |  | Bringt die Preise der Zeilen auf den Stand der Produkte |
| `setCurrency` (POST) | user | `currency:string POST` | Setzt die bevorzugte Währung des Benutzers für neue Zeilen |
| `checkoutStatus` | public |  | Was vor dem Checkout nötig ist: Zeilen, Anmeldung, Land, Konto-Handler, MwSt. bekannt |
| `accountInfo` | `shop/buy` |  | Die Kontoinformationen des aktuellen Checkouts (der temporären Bestellung) |
| `startCheckout` (POST) | `shop/buy` |  | Legt die temporäre Bestellung des Warenkorbs an (Schritt 1); nichts wird berechnet |
| `review` | `shop/buy` |  | Die temporäre Bestellung mit Versand und MwSt., wie der Bestätigungsschritt sie zeigt |
| `cancelCheckout` (POST) | `shop/buy` |  | Bricht die temporäre Bestellung ab und kehrt zum Warenkorb zurück |
| `placeOrder` (POST) | `shop/buy` |  | Führt die Checkout-Operation der temporären Bestellung aus: aktiviert sie oder übergibt an das Zahlungs-Gateway |
| `adminList` | `shop/administrate` | `limit:int`, `offset:int` | Warenkörbe aller Sitzungen mit ihrer Größe und ihrem Alter |
| `adminView` | `shop/administrate` | `basket_id:int` | Ein Warenkorb einer beliebigen Sitzung |
| `adminCleanup` (POST) | `shop/administrate` | `days:int POST` | Entfernt die Warenkörbe von Sitzungen, die nicht mehr existieren und älter als die angegebenen Tage sind |

##### Bestellungen (`exporder`, 24 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `mine` | user | `limit:int`, `offset:int` | Die Bestellungen des angemeldeten Kunden, die neuesten zuerst |
| `myOrder` | user | `order_id:int` | Eine Bestellung des angemeldeten Kunden mit Zeilen, zusätzlichen Bestellposten und Statusverlauf |
| `myCount` | user |  | Wie viele Bestellungen der angemeldete Kunde hat |
| `myReceipt` | user | `order_id:int` | Der Link zur Quittung und die Summen einer Bestellung des Kunden |
| `statuses` | `shop/administrate` | `all:bool` | Die Bestellstatus |
| `list` | `shop/administrate` | `limit:int`, `offset:int`, `show:string normal/archived/all`, `sort:string created/id/total/user_name`, `order:string asc/desc`, `status_id:int` | Bestellungen aller Kunden, filterbar nach Status und Archiv und sortiert |
| `count` | `shop/administrate` | `show:string normal/archived/all` | Zahl der Bestellungen |
| `view` | `shop/administrate` | `order_id:int` | Eine Bestellung nach ID mit Zeilen, Bestellposten und Statusverlauf |
| `byNumber` | `shop/administrate` | `order_nr:int` | Eine Bestellung nach ihrer Bestellnummer |
| `items` | `shop/administrate` | `order_id:int` | Die Produktzeilen einer Bestellung |
| `orderItems` | `shop/administrate` | `order_id:int` | Die zusätzlichen Posten einer Bestellung: Versand, Rabatte |
| `history` | `shop/administrate` | `order_id:int` | Der Statusverlauf einer Bestellung |
| `account` | `shop/administrate` | `order_id:int` | Die Kontoinformationen des Kunden einer Bestellung |
| `statusOptions` | `shop/setstatus` | `order_id:int` | Die Status, auf die der aktuelle Benutzer eine Bestellung setzen darf (Richtlinie FromStatus/ToStatus) |
| `setStatus` (POST) | `shop/setstatus` | `order_id:int POST`, `status_id:int POST` | Ändert den Status einer Bestellung |
| `archive` (POST) | `shop/administrate` | `order_id:int POST` | Verschiebt eine Bestellung ins Archiv |
| `unarchive` (POST) | `shop/administrate` | `order_id:int POST` | Holt eine archivierte Bestellung zurück |
| `remove` (POST) | `shop/administrate` | `order_id:int POST` | Löscht eine Bestellung mit ihren Zeilen und ihrem Verlauf (im Audit verzeichnet) |
| `search` | `shop/administrate` | `text:string`, `limit:int`, `offset:int` | Bestellungen nach E-Mail-Adresse oder Kontoname des Kunden |
| `statistics` | `shop/administrate` | `year:int`, `month:int` | In einem Jahr oder Monat verkaufte Produkte mit Summen |
| `customers` | `shop/administrate` | `limit:int`, `offset:int` | Die Kunden (verschiedene E-Mail-Adressen) mit der Zahl und Summe ihrer Bestellungen |
| `customerOrders` | `shop/administrate` | `user_id:int`, `email:string` | Die Bestellungen eines Kunden nach Benutzer-ID |
| `customerProducts` | `shop/administrate` | `user_id:int`, `email:string` | Die Produkte, die ein Kunde gekauft hat |
| `dashboard` | `shop/administrate` |  | Anzahl der Bestellungen pro Status und die neuesten Bestellungen |

##### MwSt. (`expvat`, 19 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `types` | public | `skip_dynamic:bool` | Die MwSt.-Typen mit ihrem Prozentsatz |
| `type` | public | `id:int` | Ein MwSt.-Typ |
| `typeUsage` | `shop/administrate` | `id:int` | Wie viele Produkte, Produktklassen und Regeln einen MwSt.-Typ verwenden |
| `createType` (POST) | `shop/administrate` | `name:string POST`, `percentage:string POST` | Legt einen MwSt.-Typ an |
| `updateType` (POST) | `shop/administrate` | `id:int POST`, `name:string POST`, `percentage:string POST` | Ändert den Namen oder Prozentsatz eines MwSt.-Typs |
| `removeType` (POST) | `shop/administrate` | `id:int POST` | Entfernt einen MwSt.-Typ und seine Regeln; Produkte fallen auf den Standard ihrer Klasse zurück |
| `rules` | `shop/administrate` | `limit:int`, `offset:int` | Die Regeln der MwSt.-Berechnung |
| `rule` | `shop/administrate` | `id:int` | Eine Regel der MwSt.-Berechnung |
| `createRule` (POST) | `shop/administrate` | `country_code:string POST`, `vat_type:int POST`, `categories:list POST` | Legt eine Regel an: Ländercode (oder Any), ID des MwSt.-Typs, IDs der Produktkategorien |
| `updateRule` (POST) | `shop/administrate` | `id:int POST`, `country_code:string POST`, `vat_type:int POST`, `categories:list POST` | Ändert eine Regel |
| `removeRule` (POST) | `shop/administrate` | `id:int POST` | Entfernt eine Regel |
| `categories` | `shop/administrate` |  | Die Produktkategorien, auf die sich die Regeln beziehen, mit der Zahl ihrer Produkte |
| `createCategory` (POST) | `shop/administrate` | `name:string POST` | Legt eine Produktkategorie an |
| `removeCategory` (POST) | `shop/administrate` | `id:int POST` | Entfernt eine Produktkategorie und ihre Verweise |
| `forProduct` | public | `node_id:int`, `country:string` | Der MwSt.-Satz eines Produkts für ein Land (dynamischer MwSt.-Handler) oder der eigene des Produkts |
| `settings` | public |  | Die MwSt.-Einstellungen des Shops: dynamische Berechnung, Handler, Pflicht zur Länderangabe |
| `userCountry` | public |  | Das Land, das für die MwSt. des aktuellen Benutzers verwendet wird |
| `setUserCountry` (POST) | public | `country:string POST` | Setzt das bevorzugte Land der aktuellen Sitzung für die MwSt. |
| `countries` | public |  | Die Ländercodes mit MwSt.-Regeln und die Länder der Installation |

##### Währungen (`expcurrency`, 19 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `list` | public | `only_active:bool` | Die Währungen mit Symbol, Status und Kursen |
| `view` | public | `code:string` | Eine Währung nach ihrem Code |
| `count` | public |  | Wie viele Währungen existieren |
| `codes` | public |  | Die Codes der aktiven Währungen |
| `exists` | public | `code:string` | Ob ein Währungscode existiert |
| `baseCurrency` | public |  | Die Basiswährung der Wechselkurse und der Standard des Shops |
| `rate` | public | `code:string` | Der Kurs einer Währung gegenüber der Basis |
| `crossRate` | public | `from:string`, `to:string` | Der Kurs zwischen zwei Währungen |
| `convert` | public | `amount:string`, `from:string`, `to:string` | Rechnet einen Betrag mit den Kursen und der Rundung des Shops von einer Währung in eine andere um |
| `rounding` | public |  | Die Rundungseinstellungen für Umrechnungen |
| `preferred` | public |  | Die bevorzugte Währung des aktuellen Benutzers |
| `setPreferred` (POST) | user | `code:string POST` | Setzt die bevorzugte Währung des aktuellen Benutzers |
| `create` (POST) | `shop/setup` | `code:string POST`, `symbol:string POST`, `locale:string POST`, `custom_rate:string POST`, `rate_factor:string POST`, `status:string POST active/inactive` | Legt eine Währung an |
| `update` (POST) | `shop/setup` | `code:string POST`, `symbol:string POST`, `locale:string POST`, `custom_rate:string POST`, `rate_factor:string POST`, `status:string POST` | Ändert Symbol, Locale, Kurse, Faktor oder Status einer Währung |
| `setStatus` (POST) | `shop/setup` | `code:string POST`, `status:string POST active/inactive` | Aktiviert oder deaktiviert eine Währung |
| `remove` (POST) | `shop/setup` | `code:string POST` | Entfernt eine Währung |
| `updateRates` (POST) | `shop/setup` |  | Holt die automatischen Kurse vom konfigurierten Anbieter und speichert sie |
| `updateAutoprices` (POST) | `shop/setup` |  | Berechnet die automatischen Preise von Produkten mit mehreren Preisen aus den Kursen neu |
| `providers` | `shop/setup` |  | Die Einstellungen der Wechselkurse: Anbieter, Server, Basis |

##### Rabatte (`expdiscount`, 16 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `groups` | `shop/administrate` | `limit:int`, `offset:int` | Die Rabattgruppen mit der Zahl ihrer Regeln und Mitglieder |
| `group` | `shop/administrate` | `id:int` | Eine Rabattgruppe mit ihren Regeln und Mitgliedern |
| `createGroup` (POST) | `shop/administrate` | `name:string POST` | Legt eine Rabattgruppe an |
| `renameGroup` (POST) | `shop/administrate` | `id:int POST`, `name:string POST` | Benennt eine Rabattgruppe um |
| `removeGroup` (POST) | `shop/administrate` | `id:int POST` | Entfernt eine Rabattgruppe mit ihren Regeln und Mitgliedschaften |
| `rules` | `shop/administrate` | `group_id:int` | Die Regeln einer Rabattgruppe |
| `rule` | `shop/administrate` | `id:int` | Eine Regel mit ihren Einschränkungen |
| `createRule` (POST) | `shop/administrate` | `group_id:int POST`, `name:string POST`, `percent:string POST`, `classes:list POST`, `sections:list POST`, `products:list POST` | Legt eine Regel in einer Gruppe an: Prozentsatz und optionale Beschränkungen auf Klasse/Sektion/Produkt |
| `updateRule` (POST) | `shop/administrate` | `id:int POST`, `name:string POST`, `percent:string POST`, `classes:list POST`, `sections:list POST`, `products:list POST` | Ändert Name, Prozentsatz oder Beschränkungen einer Regel |
| `removeRule` (POST) | `shop/administrate` | `id:int POST` | Entfernt eine Regel |
| `members` | `shop/administrate` | `group_id:int` | Die Benutzer und Benutzergruppen einer Rabattgruppe |
| `addMember` (POST) | `shop/administrate` | `group_id:int POST`, `object_id:int POST` | Fügt einer Rabattgruppe einen Benutzer oder eine Benutzergruppe (Objekt-ID) hinzu |
| `removeMember` (POST) | `shop/administrate` | `group_id:int POST`, `object_id:int POST` | Entfernt einen Benutzer oder eine Benutzergruppe aus einer Rabattgruppe |
| `groupsOfUser` | `shop/administrate` | `object_id:int` | Die Rabattgruppen, zu denen ein Benutzer oder eine Benutzergruppe gehört |
| `forUser` | `shop/administrate` | `user_id:int`, `node_id:int` | Der beste Rabattsatz eines Benutzers für ein Produkt (Klasse, Sektion, Objekt) |
| `mine` | user | `node_id:int` | Der Rabattsatz, den der angemeldete Benutzer für ein Produkt erhält |

##### Wunschlisten (`expwishlist`, 11 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `view` | user |  | Die Wunschliste des angemeldeten Benutzers mit ihren Zeilen |
| `items` | user | `limit:int`, `offset:int` | Die Zeilen der Wunschliste |
| `count` | user |  | Wie viele Zeilen die Wunschliste hat |
| `contains` | user | `object_id:int` | Ob ein Produkt auf der Wunschliste steht |
| `add` (POST) | user | `object_id:int POST`, `options:json POST` | Setzt ein Produkt auf die Wunschliste (Optionen als JSON ID des Attributs => ID der Auswahl) |
| `remove` (POST) | user | `item_id:int POST` | Entfernt eine Zeile |
| `empty` (POST) | user |  | Entfernt jede Zeile |
| `moveToBasket` (POST) | `shop/buy` | `item_id:int POST`, `quantity:int POST` | Legt eine Zeile der Wunschliste in den Warenkorb und entfernt sie aus der Liste |
| `adminList` | `shop/administrate` | `limit:int`, `offset:int` | Die Wunschlisten aller Benutzer mit ihrer Größe |
| `adminView` | `shop/administrate` | `user_id:int` | Die Wunschliste eines Benutzers nach Objekt-ID |
| `popular` | `shop/administrate` | `limit:int` | Die Produkte auf den meisten Wunschlisten |

##### Versand (`expshipping`, 8 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `status` | public |  | Ob der Versand konfiguriert ist: Handler, Workflow für einfachen Versand |
| `handler` | `shop/administrate` |  | Der Versand-Handler aus shop.ini und wo er gesucht wird |
| `simpleShipping` | public |  | Die Kosten und die Beschreibung des Workflows für einfachen Versand |
| `basket` | public |  | Die Versandinformationen des aktuellen Warenkorbs |
| `order` | `shop/administrate` | `order_id:int` | Die Versandzeile einer Bestellung (Bestellposten vom Typ Versand) |
| `basketInfoHandler` | public |  | Der Handler für Warenkorbinformationen, der Summen und Versand berechnet |
| `vatOfShipping` | public |  | Die Aufteilung der MwSt. auf Versandkosten (die MwSt. der Produkte, auf die sie verteilt werden) |
| `settings` | `shop/administrate` |  | Die ShippingSettings und BasketInfoSettings aus shop.ini |

##### Zahlungen (`exppayment`, 10 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `list` | `shop/administrate` | `limit:int`, `offset:int`, `status:string all/pending/approved` | Die Zahlungsobjekte, die neuesten zuerst, optional nur ausstehende oder genehmigte |
| `view` | `shop/administrate` | `id:int` | Ein Zahlungsobjekt nach ID |
| `byOrder` | `shop/administrate` | `order_id:int` | Die Zahlung einer Bestellung |
| `myStatus` | user | `order_id:int` | Der Zahlungsstatus einer Bestellung des angemeldeten Kunden |
| `counts` | `shop/administrate` |  | Wie viele Zahlungen ausstehen und genehmigt sind |
| `gateways` | `shop/administrate` |  | Die verfügbaren Zahlungs-Gateways und die durchsuchten Verzeichnisse |
| `workflowEvents` | `shop/administrate` |  | Die Workflow-Ereignisse vom Typ Zahlungs-Gateway, mit ihrem Gateway |
| `handlers` | public |  | Der Konto-Handler und der Handler zur Bestätigung der Bestellung im Checkout |
| `methods` | public |  | Was ein Client als Zahlung anbieten kann: eingeschaltete Gateways oder die manuelle Methode |
| `approve` (POST) | `shop/administrate` | `id:int POST` | Genehmigt eine Zahlung und setzt ihren Workflow fort (im Audit als commerce.payment.approve verzeichnet) |

#### Formulare, Umfragen und Community

##### Gesammelte Formulare (`expinfocollection`, 12 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `forms` | `infocollector/read` | `limit:int`, `offset:int` | Die Inhaltsobjekte, die Daten gesammelt haben, mit der Zahl der Sammlungen |
| `collections` | `infocollector/read` | `object_id:int`, `limit:int`, `offset:int` | Die Sammlungen eines Formulars, die neuesten zuerst |
| `view` | `infocollector/read` | `collection_id:int` | Eine Sammlung mit ihren Werten |
| `count` | `infocollector/read` | `object_id:int` | Wie viele Sammlungen ein Formular (oder alle Formulare) hat |
| `fields` | public | `node_id:int` | Die Felder eines Formulars: die Attribute, die Informationen sammeln, mit Typ und Pflichtangabe |
| `summary` | `infocollector/read` | `object_id:int` | Pro Feld eines Formulars: wie viele Werte und, bei Auswahl- und Zahlenfeldern, die Verteilung |
| `mine` | user | `limit:int`, `offset:int` | Die Sammlungen, die der angemeldete Benutzer abgeschickt hat |
| `export` | `infocollector/read` | `object_id:int` | Alle Sammlungen eines Formulars als CSV-Text (vom Kernel als Datenexport im Audit verzeichnet, wenn eingeschaltet) |
| `submit` (POST) | public | `object_id:int POST`, `fields:json POST` | Schickt ein Formular ab: POST object_id und fields als JSON Kennung => Wert (Text, Zahl, Boolescher Wert, ID der Auswahl) |
| `remove` (POST) | `infocollector/read` | `collection_id:int POST` | Entfernt eine Sammlung (im Audit als data.infocollection.remove verzeichnet) |
| `removeAll` (POST) | `infocollector/read` | `object_id:int POST` | Entfernt jede Sammlung eines Formulars |
| `settings` | public | `node_id:int` | Wie die Sammlung für ein Formular behandelt wird: anonym erlaubt, unique/overwrite/multiple, Template-Typ |

##### Umfragen (`exppoll`, 11 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `list` | public | `parent_node_id:int`, `limit:int`, `offset:int` | Die Umfragen unter einem Knoten mit ihrer Frage und der Zahl der Stimmen |
| `view` | public | `node_id:int` | Eine Umfrage: Frage, Auswahlmöglichkeiten, Stimmen pro Auswahl |
| `results` | public | `node_id:int` | Die Ergebnisse einer Umfrage: Stimmen und Prozent pro Auswahl |
| `choices` | public | `node_id:int` | Die Auswahlmöglichkeiten einer Umfrage |
| `canVote` | public | `node_id:int` | Ob der aktuelle Besucher jetzt abstimmen darf, und warum nicht |
| `myVote` | public | `node_id:int` | Die Auswahl, für die der aktuelle Besucher gestimmt hat |
| `vote` (POST) | public | `node_id:int POST`, `choice:int POST` | Stimmt für eine Auswahl (POST node_id, choice) |
| `latest` | public | `limit:int` | Die neuesten Umfragen |
| `create` (POST) | `content/create` | `parent_node_id:int POST`, `name:string POST`, `question:string POST`, `choices:list POST` | Legt eine Umfrage unter einem Knoten an: Name, Frage und Auswahlmöglichkeiten |
| `remove` (POST) | `content/remove` | `node_id:int POST` | Entfernt eine Umfrage (in den Papierkorb) mit ihren Stimmen |
| `resetVotes` (POST) | `infocollector/read` | `node_id:int POST` | Entfernt jede Stimme einer Umfrage |

##### Foren (`expforum`, 13 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `containers` | public | `limit:int`, `offset:int` | Die Forencontainer (die Klasse „forums“) der Website |
| `list` | public | `parent_node_id:int`, `limit:int`, `offset:int` | Die Foren unter einem Knoten (einem Container oder der Inhaltswurzel: alle Foren) |
| `view` | public | `node_id:int` | Ein Forum mit der Zahl seiner Themen und Antworten und dem neuesten Thema |
| `stats` | public | `node_id:int` | Themen, Antworten und die letzte Aktivität eines Forums |
| `latest` | public | `limit:int` | Die neuesten Themen über alle Foren |
| `search` | public | `text:string`, `limit:int`, `offset:int` | Wörter in Namen und Beschreibungen von Foren |
| `path` | public | `node_id:int` | Der Pfad (Container, Forum) eines Knotens für eine Brotkrumennavigation |
| `fields` | public |  | Die Felder, die ein Forum hat, und die verwendeten Klassen |
| `create` (POST) | `content/create` | `parent_node_id:int POST`, `fields:json POST` | Legt ein Forum in einem Container an (Felder als JSON oder POST-Felder: name, description) |
| `edit` (POST) | `content/edit` | `node_id:int POST`, `fields:json POST` | Ändert die Felder eines Forums |
| `remove` (POST) | `content/remove` | `node_id:int POST` | Verschiebt ein Forum mit seinen Themen in den Papierkorb |
| `hide` (POST) | `content/hide` | `node_id:int POST` | Verbirgt ein Forum vor Besuchern |
| `unhide` (POST) | `content/hide` | `node_id:int POST` | Zeigt ein verborgenes Forum wieder an |

##### Themen (`exptopic`, 17 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `list` | public | `forum_node_id:int`, `limit:int`, `offset:int` | Die Themen eines Forums, angeheftete zuerst, dann die neuesten zuerst |
| `view` | public | `node_id:int` | Ein Thema mit seinem Text und der Zahl seiner Antworten |
| `count` | public | `forum_node_id:int` | Wie viele Themen ein Forum hat |
| `latest` | public | `limit:int`, `forum_node_id:int` | Die neuesten Themen, in einem Forum oder überall |
| `sticky` | public | `forum_node_id:int` | Die angehefteten Themen eines Forums |
| `byUser` | public | `user_id:int`, `limit:int`, `offset:int` | Die Themen, die ein Benutzer begonnen hat |
| `mine` | user | `limit:int`, `offset:int` | Die Themen, die der angemeldete Benutzer begonnen hat |
| `search` | public | `text:string`, `forum_node_id:int`, `limit:int`, `offset:int` | Wörter in Betreffzeilen und Texten von Themen |
| `lastReply` | public | `node_id:int` | Die neueste Antwort eines Themas |
| `canCreate` | public | `forum_node_id:int` | Ob der aktuelle Benutzer in einem Forum ein Thema beginnen darf |
| `create` (POST) | `content/create` | `forum_node_id:int POST`, `fields:json POST` | Beginnt ein Thema in einem Forum (Felder subject, message, sticky als JSON oder POST-Felder) |
| `edit` (POST) | `content/edit` | `node_id:int POST`, `fields:json POST` | Ändert Betreff oder Nachricht eines Themas |
| `remove` (POST) | `content/remove` | `node_id:int POST` | Verschiebt ein Thema mit seinen Antworten in den Papierkorb |
| `setSticky` (POST) | `content/edit` | `node_id:int POST`, `sticky:bool POST` | Heftet ein Thema an oder löst es (Moderation) |
| `move` (POST) | `content/edit` | `node_id:int POST`, `forum_node_id:int POST` | Verschiebt ein Thema in ein anderes Forum |
| `hide` (POST) | `content/hide` | `node_id:int POST` | Verbirgt ein Thema mit seinen Antworten vor Besuchern (Moderation) |
| `unhide` (POST) | `content/hide` | `node_id:int POST` | Zeigt ein verborgenes Thema wieder an |

##### Antworten (`expreply`, 13 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `list` | public | `topic_node_id:int`, `limit:int`, `offset:int`, `newest_first:bool` | Die Antworten eines Themas, die ältesten zuerst |
| `view` | public | `node_id:int` | Eine Antwort |
| `count` | public | `topic_node_id:int` | Wie viele Antworten ein Thema hat |
| `latest` | public | `limit:int`, `parent_node_id:int` | Die neuesten Antworten, überall oder unter einem Knoten |
| `byUser` | public | `user_id:int`, `limit:int`, `offset:int` | Die Antworten, die ein Benutzer geschrieben hat |
| `mine` | user | `limit:int`, `offset:int` | Die Antworten, die der angemeldete Benutzer geschrieben hat |
| `search` | public | `text:string`, `topic_node_id:int`, `limit:int`, `offset:int` | Wörter in Betreffzeilen und Texten von Antworten |
| `quote` | public | `node_id:int` | Eine Antwort als Zitat, um eine Antwort damit zu beginnen: „Re: Betreff“ und der zitierte Text |
| `create` (POST) | `content/create` | `topic_node_id:int POST`, `fields:json POST` | Schreibt eine Antwort auf ein Thema (Felder subject, message als JSON oder POST-Felder) |
| `edit` (POST) | `content/edit` | `node_id:int POST`, `fields:json POST` | Ändert eine Antwort |
| `remove` (POST) | `content/remove` | `node_id:int POST` | Verschiebt eine Antwort in den Papierkorb |
| `hide` (POST) | `content/hide` | `node_id:int POST` | Verbirgt eine Antwort (Moderation) |
| `unhide` (POST) | `content/hide` | `node_id:int POST` | Zeigt eine verborgene Antwort wieder an |

##### Kommentare und Bewertungen (`expcomment`, 17 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `list` | public | `node_id:int`, `limit:int`, `offset:int`, `newest_first:bool` | Die Kommentare unter einem Knoten, die ältesten oder die neuesten zuerst |
| `view` | public | `node_id:int` | Ein Kommentar |
| `count` | public | `node_id:int` | Wie viele Kommentare ein Knoten hat |
| `latest` | public | `limit:int`, `parent_node_id:int` | Die neuesten Kommentare, überall oder unter einem Knoten |
| `byUser` | public | `user_id:int`, `limit:int`, `offset:int` | Die Kommentare, die ein Benutzer geschrieben hat |
| `mine` | user | `limit:int`, `offset:int` | Die Kommentare, die der angemeldete Benutzer geschrieben hat |
| `search` | public | `text:string`, `parent_node_id:int`, `limit:int`, `offset:int` | Wörter in Betreffzeilen, Autoren und Texten von Kommentaren |
| `hidden` | `content/hide` | `parent_node_id:int`, `limit:int`, `offset:int` | Die verborgenen Kommentare unter einem Knoten (Moderationswarteschlange) |
| `canComment` | public | `node_id:int` | Ob der aktuelle Benutzer einen Knoten kommentieren darf |
| `create` (POST) | `content/create` | `node_id:int POST`, `fields:json POST` | Kommentiert einen Knoten (Felder subject, author, message als JSON oder POST-Felder) |
| `edit` (POST) | `content/edit` | `node_id:int POST`, `fields:json POST` | Ändert einen Kommentar |
| `remove` (POST) | `content/remove` | `node_id:int POST` | Verschiebt einen Kommentar in den Papierkorb |
| `hide` (POST) | `content/hide` | `node_id:int POST` | Verbirgt einen Kommentar (Moderation) |
| `unhide` (POST) | `content/hide` | `node_id:int POST` | Zeigt einen verborgenen Kommentar wieder an |
| `reviews` | public | `node_id:int`, `limit:int`, `offset:int` | Die Bewertungen eines Produkts mit der durchschnittlichen Wertung |
| `ratingSummary` | public | `node_id:int` | Durchschnittliche Wertung und die Anzahl pro Stern eines Produkts |
| `addReview` (POST) | `content/create` | `node_id:int POST`, `fields:json POST` | Bewertet ein Produkt (Felder title, rating 1-5, author, body) |

#### Feeds

##### Feeds (`expfeed`, 22 Dienste)

| Dienst | Zugriff | Argumente | Was er tut |
|---|---|---|---|
| `exports` | public | `limit:int`, `offset:int`, `only_active:bool` | Die RSS-Exporte mit ihrer Adresse, ihrem Format und der Zahl ihrer Einträge |
| `export` | public | `id:int` | Ein RSS-Export mit seinen Quellen |
| `exportByUrl` | public | `access_url:string` | Ein RSS-Export nach dem Namen seiner Zugriffs-URL |
| `output` | public | `access_url:string` | Das Feed-Dokument eines RSS-Exports, wie der Leser es erhält (RSS 1.0, 2.0, Atom, OPML, iTunes) |
| `formats` | public |  | Die Exportformate und ihre Namen |
| `rss` | public | `node_id:int`, `limit:int`, `classes:list` | Ein RSS-2.0-Feed der neuesten Inhalte unter einem Knoten |
| `atom` | public | `node_id:int`, `limit:int`, `classes:list` | Ein Atom-1.0-Feed der neuesten Inhalte unter einem Knoten |
| `json` | public | `node_id:int`, `limit:int`, `classes:list` | Ein JSON Feed 1.1 der neuesten Inhalte unter einem Knoten |
| `items` | public | `node_id:int`, `limit:int`, `offset:int`, `classes:list` | Die Feed-Einträge eines Knotens als einfache Daten (Titel, URL, Datum, Zusammenfassung) |
| `discover` | public | `node_id:int` | Die Feed-Adressen, die eine Website für einen Knoten anbietet: die allgemeinen und die Exporte, die ihn abdecken |
| `createExport` (POST) | `rss/edit` | `title:string POST`, `access_url:string POST`, `source_node_id:int POST`, `rss_version:string POST 1.0/2.0/ATOM`, `number_of_objects:int POST`, `description:string POST` | Legt einen aktiven RSS-Export eines Teilbaums an |
| `updateExport` (POST) | `rss/edit` | `id:int POST`, `title:string POST`, `description:string POST`, `number_of_objects:int POST`, `rss_version:string POST`, `main_node_only:bool POST` | Ändert Titel, Beschreibung, Umfang oder Format eines Exports |
| `setActive` (POST) | `rss/edit` | `id:int POST`, `active:bool POST` | Schaltet einen Export ein oder aus |
| `addSource` (POST) | `rss/edit` | `id:int POST`, `source_node_id:int POST`, `subnodes:bool POST` | Fügt einem Export einen Quellteilbaum hinzu |
| `removeSource` (POST) | `rss/edit` | `id:int POST`, `source_id:int POST` | Entfernt eine Quelle aus einem Export |
| `removeExport` (POST) | `rss/edit` | `id:int POST` | Entfernt einen Export mit seinen Quellen |
| `imports` | `rss/edit` | `limit:int`, `offset:int` | Die RSS-Importe mit ihrer Quell-URL, ihrem Ziel und ihrem Zustand |
| `import` | `rss/edit` | `id:int` | Ein RSS-Import |
| `importStatus` | `rss/edit` | `id:int` | Was ein Import gebracht hat: die von ihm angelegten Objekte, das neueste und wann |
| `importCheck` | `rss/edit` | `id:int`, `fetch:bool` | Ob die Quelle eines Imports eine Adresse ist, die der Server holen darf, und optional ihre Feed-Version (holt die Quelle) |
| `importSetActive` (POST) | `rss/edit` | `id:int POST`, `active:bool POST` | Schaltet einen Import ein oder aus (der Cronjob rssimport führt die aktiven aus) |
| `runImport` (POST) | `rss/edit` | `id:int POST`, `dry_run:bool POST`, `max_items:int POST` | Führt einen RSS-Import so aus, wie es der Cronjob rssimport tut; `dry_run` berichtet nur, was importiert würde |


Insgesamt: 256 Dienste in 16 Domänen (84 Schreibvorgänge).

<!-- expservices-content:begin -->
## Inhaltsdienste: 350 Dienste in 15 Domänen (112 Schreibvorgänge)

Geschrieben vom Inhaltsteil von expservices: `extension/expservices/classes/content/` (eine Klasse pro Domäne über die
gemeinsame `expContentServiceBase`: Hilfsmethoden zum Holen mit den Zugriffsprüfungen, die Exporter, die Knoten,
Objekte, Versionen, Klassen, Sektionen und Zustände in einfache Arrays umwandeln, Attributwerte pro Datentyp, das Parsen
von Sortierung und Filter und die Weiche „sofort oder als Job“). Tests: `tests/tests/extension/expservices/content/`
(Live-Datenbank wie bei den anderen Domänen: Lesevorgänge auf den stabilen Knoten, Schreibvorgänge auf Testinhalten
unter dem Wurzelknoten Media 43 und auf Testklassen, Klassengruppen, Sektionen, Zustandsgruppen, Aliasen und
Platzhaltern, die tearDown entfernt; nie eine Testdatenbank). Die Beispielaufrufe wurden außerdem über HTTP auf Apache
mit einer angemeldeten Admin-Sitzung geprüft (Lesevorgänge, ein ohne Token abgelehnter Schreibvorgang, dann Anlegen,
Aktualisieren und Entfernen eines Testordners).

Wie die Inhaltsdienste über die obigen Regeln hinaus arbeiten:

- **Richtlinien mit Einschränkungen.** Die Spalte Zugriff ist die Richtlinie, die der Aufruf überhaupt braucht
  (`content/read`, `content/edit`, ...); darin prüft jeder Aufruf den Knoten oder das Objekt so, wie es der Kernel tut
  (`canRead`, `canEdit`, `canRemove`, `canMoveFrom`, `canCreateClassList`, `canAssignSectionToObject`, die Liste der
  erlaubten Zustände ...), sodass eine Einschränkung nach Klasse, Sektion, Teilbaum, Besitzer oder Zustand für die
  Dienste genauso gilt wie in der Administration. Listen verwenden die Fetch-Funktionen des Kernels mit ihrem
  Berechtigungs-SQL, sodass ein Benutzer nur zählt und blättert, was er lesen darf.
- **Kennungen.** Knoten, Objekte, Versionen, Sektionen und Zustände werden über ihre Nummer angesprochen; Klassen über
  ID oder Kennung; Zustandsgruppen über ID oder Kennung; Sprachen über den Locale-Code (`eng-GB`).
- **Eingabe von Attributen.** Schreibvorgänge nehmen Attributwerte als JSON-Objekt `identifier => string` an, in der
  Zeichenkettenform, die der Datentyp liest (`fromString()`: siehe `expattribute::string`). Datentypen, deren
  Zeichenkettenform ein Dateipfad oder ein Zugangsdatum ist (`ezimage`, `ezbinaryfile`, `ezmedia`, `ezuser`,
  `ezpassword`), werden mit 422 abgelehnt: Ein entfernter Aufrufer darf den Server nie einen lokalen Pfad lesen lassen.
  Pflichtattribute müssen beim Anlegen angegeben werden. Werte kommen pro Datentyp zurück (Text, Zahl, Boolescher Wert,
  Datum mit ISO-Form, XML-Text als xml und html, Relationen als IDs, Bilder mit der URL ihres Originals).
- **Listen** antworten mit dem seitenweisen Umschlag (`data` Einträge, `meta` total, offset, limit, count, has_more).
  Knotenlisten nehmen `sort` (path, published, modified, section, depth, class_identifier, class_name, priority, name,
  modified_subnode, node_id, contentobject_id), `order` (asc, desc), `limit`, `offset` und einen JSON-`filter` an:
  `class[]`, `exclude_class[]`, `section`, `state[]`, `owner`, `name` (Präfix), `language`, `from` und `to`
  (veröffentlicht), `depth`, `hidden`.
- **Sofort oder als Job.** Große Operationen (Verschieben, Kopieren eines Teilbaums, Verbergen, Anzeigen, Entfernen von
  Teilbäumen, Sektion und Zustand eines Teilbaums, Hinzufügen und Entfernen von Platzierungen) nehmen das POST-Feld
  `mode` an: `now` läuft in der Anfrage (abgelehnt oberhalb von `NowLimit` Knoten aus content.ini, Antwort 422), `job`
  legt einen Inhaltsjob an und startet seinen Worker, `auto` (der Standard) wählt anhand von `SynchronousLimit`. Eine
  Job-Antwort ist `{ job_id, type, state, spawned }` mit `meta.mode = job`; verfolgen Sie sie mit
  `expcontentjob::progress::<job_id>`. Ein Teilbaum, der von einem laufenden Job gesperrt ist, beantwortet einen
  synchronen Schreibvorgang mit 409.
- **Entwürfe von Klassen** sind die temporäre Version der Klasse, die, an der der Klasseneditor der Administration
  arbeitet: `editDraft` oder `createDraft`, ändern, dann `publishDraft` (der Bearbeitungs-Handler der Klasse
  aktualisiert vorhandene Objekte) oder `discardDraft`.
- **Entfernen.** `remove` nimmt `move_to_trash` an (Standard 1); die Wurzelknoten können nicht entfernt werden;
  `exptrash::purge`, `purgeMany` und `emptyTrash` (POST `confirm=yes`) entfernen endgültig über den Papierkorb-Dienst
  des Kernels. Klassen mit Objekten, verwendete Sektionen, Zustandsgruppen mit dem Präfix `ez` und die letzte
  Platzierung eines Objekts werden mit 409 oder 403 abgelehnt.
- **Audit.** Jeder erfolgreiche Schreibvorgang verzeichnet `service.<domain>.<method>`; der Kernel verzeichnet seine
  eigenen Ereignisse `content.*` für die Operationen, die die Dienste aufrufen (Anlegen, Veröffentlichen, Verschieben,
  Entfernen, Sektion, Zustand, Übersetzung, Klasse, URL-Alias).

Fehler: 400 falsches Argument, 401 nicht angemeldet, 403 verweigert (Richtlinie, Methode oder Formular-Token), 404 nicht
gefunden, 409 Konflikt (existiert, gesperrt, in Verwendung), 422 nicht gültig (Eingabe von einem Datentyp oder einer
Regel abgelehnt).

Beispiel: einen Artikel anlegen, ihn ändern und eine große Verschiebung als Job verfolgen (Shell, nach
`expsession::login`):

```bash
curl -b jar -c jar --data-urlencode 'class=article' --data-urlencode 'attributes={"title":"Hello","intro":"First"}' \
     --data-urlencode "ezxform_token=$TOKEN" "$BASE/ezjscore/call/expnode::create::43?ContentType=json"
curl -b jar -c jar --data-urlencode 'attributes={"title":"Hello again"}' --data-urlencode "ezxform_token=$TOKEN" \
     "$BASE/ezjscore/call/expnode::update::<node>?ContentType=json"
curl -b jar -c jar --data-urlencode 'mode=job' --data-urlencode "ezxform_token=$TOKEN" \
     "$BASE/ezjscore/call/expnode::move::<node>::<new-parent>?ContentType=json"      # { job_id: ... }
curl -b jar "$BASE/ezjscore/call/expcontentjob::progress::<job_id>?ContentType=json"
```

#### Knoten (`expnode`, 49 Dienste, 16 Schreibvorgänge)

Der Inhaltsbaum: holen nach ID, Remote-ID, Pfad und Objekt; Kinder und Teilbäume seitenweise, sortiert und gefiltert;
Anzahlen, Vorfahren, Geschwister, Data Maps, Rechte; anlegen, aktualisieren, verschieben, kopieren, verbergen,
tauschen, entfernen, Sortierung und Priorität.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expnode::get::<node_id>` | content/read |  | Ein Knoten mit seinem Objekt |
| `expnode::getMany::<node_ids>` | content/read |  | Mehrere Knoten nach ID (nicht lesbare und fehlende werden weggelassen) |
| `expnode::getByRemoteId::<remote_id>` | content/read |  | Ein Knoten nach seiner Remote-ID |
| `expnode::getByPath::<path>` | content/read |  | Ein Knoten nach seinem URL-Alias, z. B. Media/Images |
| `expnode::getByObject::<object_id>` | content/read |  | Der Hauptknoten eines Objekts |
| `expnode::exists::<node_id>` | user |  | Ob ein Knoten existiert und lesbar ist |
| `expnode::children::<node_id>::<sort>::<order>::<limit>::<offset>::<filter>` | content/read |  | Kinder eines Knotens, seitenweise, sortiert und gefiltert |
| `expnode::childrenCount::<node_id>::<filter>` | content/read |  | Zahl der Kinder |
| `expnode::childrenNames::<node_id>::<limit>::<offset>` | content/read |  | Schlanke Liste der Kinder: Knoten-ID, Objekt-ID, Klasse, Name |
| `expnode::subtree::<node_id>::<sort>::<order>::<limit>::<offset>::<filter>` | content/read |  | Nachkommen eines Knotens, seitenweise, sortiert und gefiltert (filter.depth begrenzt die Tiefe) |
| `expnode::subtreeCount::<node_id>::<filter>` | content/read |  | Zahl der Nachkommen |
| `expnode::byClass::<node_id>::<classes>::<limit>::<offset>` | content/read |  | Nachkommen einer oder mehrerer Klassen |
| `expnode::countByClass::<node_id>::<depth>` | content/read |  | Facette: Zahl der Nachkommen pro Klasse (braucht uneingeschränktes Lesen) |
| `expnode::latest::<node_id>::<limit>::<classes>` | content/read |  | Die zuletzt veröffentlichten Nachkommen |
| `expnode::modifiedSince::<node_id>::<since>::<limit>::<offset>` | content/read |  | Seit einem Zeitpunkt veröffentlichte Nachkommen (Zeitstempel oder Datum) |
| `expnode::find::<node_id>::<name>::<limit>::<offset>` | content/read |  | Nachkommen, deren Name mit einem Text beginnt |
| `expnode::hidden::<node_id>::<limit>::<offset>` | content/read |  | Verborgene Knoten unter einem Knoten |
| `expnode::tree::<node_id>::<depth>::<per_level>` | content/read |  | Verschachtelte Kinder bis zu einer Tiefe, begrenzt pro Ebene |
| `expnode::path::<node_id>` | content/read |  | Die Vorfahren eines Knotens ab der Wurzel, mit dem Knoten selbst |
| `expnode::breadcrumb::<node_id>` | content/read |  | Schlanke Liste der Vorfahren: Knoten-ID, Name, URL-Alias |
| `expnode::parent::<node_id>` | content/read |  | Der Elternknoten |
| `expnode::siblings::<node_id>::<limit>::<offset>` | content/read |  | Andere Kinder des Elternknotens, seitenweise |
| `expnode::neighbours::<node_id>` | content/read |  | Vorheriges und nächstes Geschwister in der Sortierreihenfolge des Elternknotens |
| `expnode::dataMap::<node_id>::<language>` | content/read |  | Die Attribute des Objekts des Knotens mit ihren Werten |
| `expnode::attribute::<node_id>::<identifier>::<language>` | content/read |  | Ein Attribut des Objekts des Knotens |
| `expnode::names::<node_id>` | content/read |  | Der Name des Knotens in jeder Sprache |
| `expnode::url::<node_id>` | content/read |  | URL-Alias und System-URL eines Knotens |
| `expnode::sortInfo::<node_id>` | content/read |  | Sortierfeld und -reihenfolge der Kinder und die erlaubten Namen |
| `expnode::visibility::<node_id>` | content/read |  | Die Flags verborgen und unsichtbar mit dem nächsten verborgenen Vorfahren |
| `expnode::rights::<node_id>` | user |  | Was der aktuelle Benutzer mit dem Knoten tun kann |
| `expnode::creatableClasses::<node_id>` | content/create |  | Klassen, die der aktuelle Benutzer unter dem Knoten anlegen kann |
| `expnode::locations::<node_id>` | content/read |  | Alle Knoten desselben Objekts |
| `expnode::pendingDrafts::<node_id>::<limit>::<offset>` | content/read |  | Entwürfe unter einem Knoten, die auf Veröffentlichung warten |
| `expnode::create::<parent_node_id>` | content/create | POST | Legt ein Objekt unter einem Knoten an und veröffentlicht es. POST: class, attributes (JSON identifier => string), language, remote_id |
| `expnode::update::<node_id>` | content/edit | POST | Ändert Attribute des Objekts des Knotens und veröffentlicht eine neue Version. POST: attributes (JSON), language |
| `expnode::rename::<node_id>` | content/edit | POST | Benennt das Objekt eines Knotens um. POST: name |
| `expnode::move::<node_id>::<new_parent_node_id>` | content/move | POST | Verschiebt einen Knoten und seinen Teilbaum. POST: mode |
| `expnode::copy::<node_id>::<new_parent_node_id>` | content/create | POST | Kopiert einen Knoten (das Objekt) unter einen Knoten |
| `expnode::copySubtree::<node_id>::<new_parent_node_id>` | content/create | POST | Kopiert einen Knoten mit allen seinen Nachkommen. POST: mode, all_versions, keep_creator, keep_time |
| `expnode::hide::<node_id>` | content/hide | POST | Verbirgt einen Knoten und seinen Teilbaum. POST: mode |
| `expnode::reveal::<node_id>` | content/hide | POST | Zeigt einen verborgenen Knoten und seinen Teilbaum wieder an. POST: mode |
| `expnode::toggleHide::<node_id>` | content/hide | POST | Verbirgt einen sichtbaren Knoten, zeigt einen verborgenen an |
| `expnode::swap::<node_id>::<other_node_id>` | content/edit | POST | Tauscht zwei Knoten (ihre Objekte tauschen die Plätze) |
| `expnode::remove::<node_id>` | content/remove | POST | Entfernt einen Knoten und seinen Teilbaum. POST: move_to_trash (Standard 1), mode |
| `expnode::removeMany` | content/remove | POST | Entfernt mehrere Knoten. POST: node_ids, move_to_trash, mode |
| `expnode::setSort::<node_id>::<field>::<order>` | content/edit | POST | Legt fest, wie die Kinder sortiert werden |
| `expnode::setPriority::<node_id>::<priority>` | content/edit | POST | Setzt die Priorität eines Knotens |
| `expnode::setPriorities::<parent_node_id>` | content/edit | POST | Setzt die Prioritäten von Kindern. POST: priorities (JSON Knoten-ID => Priorität) |
| `expnode::setRemoteId::<node_id>::<remote_id>` | content/edit | POST | Setzt die Remote-ID eines Knotens |

#### Objekte (`expobject`, 36 Dienste, 11 Schreibvorgänge)

Inhaltsobjekte: Listen nach Klasse und Besitzer, Data Maps und Attribute mit ihren Werten, Namen, Besitzer, Klasse,
Sektion, Zustände, Platzierungen, Sprachen; anlegen, aktualisieren (eine neue veröffentlichte Version), umbenennen,
Besitzer, Sprachen, kopieren, entfernen.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expobject::get::<object_id>` | content/read |  | Ein Objekt mit seinen Sprachen und Knoten-IDs |
| `expobject::getByRemoteId::<remote_id>` | content/read |  | Ein Objekt nach seiner Remote-ID |
| `expobject::exists::<object_id>` | user |  | Ob ein Objekt existiert und lesbar ist |
| `expobject::list::<sort>::<order>::<limit>::<offset>::<filter>` | content/read |  | Objekte (Hauptknoten) seitenweise, sortiert und gefiltert |
| `expobject::count::<filter>` | content/read |  | Zahl der Objekte, die einem Filter entsprechen |
| `expobject::byClass::<class>::<limit>::<offset>` | content/read |  | Objekte einer Klasse |
| `expobject::byOwner::<owner_id>::<limit>::<offset>` | content/read |  | Objekte, die einer Objekt-ID (Benutzer) gehören |
| `expobject::recent::<limit>::<classes>` | content/read |  | Die zuletzt veröffentlichten Objekte |
| `expobject::recentlyModified::<limit>::<classes>` | content/read |  | Die zuletzt geänderten Objekte |
| `expobject::dataMap::<object_id>::<version>::<language>` | content/read |  | Die Attribute einer Objektversion mit ihren Werten |
| `expobject::attributes::<object_id>::<language>` | content/read |  | Die Attribute eines Objekts ohne Werte |
| `expobject::attribute::<object_id>::<identifier>::<language>` | content/read |  | Ein Attribut eines Objekts |
| `expobject::name::<object_id>::<language>` | content/read |  | Der Name eines Objekts in einer Sprache |
| `expobject::names::<object_id>` | content/read |  | Der Name eines Objekts in jeder Sprache |
| `expobject::owner::<object_id>` | content/read |  | Das Besitzerobjekt eines Objekts |
| `expobject::class::<object_id>` | content/read |  | Die Klasse eines Objekts mit ihren Attributen |
| `expobject::section::<object_id>` | content/read |  | Die Sektion eines Objekts |
| `expobject::states::<object_id>` | content/read |  | Die Zustände eines Objekts |
| `expobject::nodes::<object_id>` | content/read |  | Alle Knoten (Platzierungen) eines Objekts |
| `expobject::mainNode::<object_id>` | content/read |  | Der Hauptknoten eines Objekts |
| `expobject::languages::<object_id>` | content/read |  | Sprachen, in denen das Objekt existiert, Ausgangssprache und immer verfügbar |
| `expobject::versionCount::<object_id>` | content/read |  | Zahl der Versionen |
| `expobject::rights::<object_id>` | user |  | Was der aktuelle Benutzer mit dem Objekt tun kann |
| `expobject::url::<object_id>` | content/read |  | URL-Aliase des Hauptknotens eines Objekts |
| `expobject::summary::<object_id>` | content/read |  | Schlanke Karte eines Objekts: ID, Name, Klasse, Hauptknoten, geändert |
| `expobject::create` | content/create | POST | Legt ein Objekt an und veröffentlicht es. POST: parent_node_id, class, attributes (JSON), language, remote_id |
| `expobject::update::<object_id>` | content/edit | POST | Ändert Attribute und veröffentlicht eine neue Version. POST: attributes (JSON), language |
| `expobject::rename::<object_id>` | content/edit | POST | Benennt ein Objekt um. POST: name |
| `expobject::setRemoteId::<object_id>::<remote_id>` | content/edit | POST | Setzt die Remote-ID eines Objekts |
| `expobject::setOwner::<object_id>::<owner_id>` | content/edit | POST | Ändert den Besitzer eines Objekts (braucht uneingeschränkten Bearbeitungszugriff) |
| `expobject::setInitialLanguage::<object_id>::<language>` | content/translate | POST | Ändert die Ausgangssprache eines Objekts |
| `expobject::setAlwaysAvailable::<object_id>::<value>` | content/translate | POST | Legt fest, ob das Objekt in Sprachen angezeigt wird, für die es keine Übersetzung hat |
| `expobject::copy::<object_id>::<new_parent_node_id>` | content/create | POST | Kopiert das Objekt eines Hauptknotens unter einen Knoten |
| `expobject::remove::<object_id>` | content/remove | POST | Entfernt ein Objekt mit allen seinen Knoten. POST: move_to_trash (Standard 1), mode |
| `expobject::expireCache::<object_id>` | content/edit | POST | Leert die View-Caches eines Objekts |
| `expobject::cleanupDrafts::<object_id>` | content/edit | POST | Entfernt die internen Entwürfe des aktuellen Benutzers zu einem Objekt |

#### Klassen (`expclass`, 37 Dienste, 12 Schreibvorgänge)

Definitionen von Inhaltsklassen und der Klasseneditor als Dienste: Lesen von Klassen, Attributen, Gruppen, Verwendung
und Datentypen; Ablauf mit Entwurf (createDraft oder editDraft, den Entwurf und seine Attribute ändern, publishDraft
oder discardDraft), kopieren und entfernen.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expclass::list::<group_id>::<limit>::<offset>` | class/* |  | Inhaltsklassen, optional einer Gruppe |
| `expclass::count` | class/* |  | Zahl der Klassen |
| `expclass::identifiers` | user |  | Schlanke Zuordnung Klassen-ID => Kennung und Name |
| `expclass::get::<class>` | class/* |  | Eine Klasse mit ihren Attributen und Gruppen (ID oder Kennung) |
| `expclass::getByRemoteId::<remote_id>` | class/* |  | Eine Klasse nach ihrer Remote-ID |
| `expclass::search::<text>::<limit>::<offset>` | class/* |  | Klassen, deren Name oder Kennung einen Text enthält |
| `expclass::attributes::<class>` | class/* |  | Die Attribute einer Klasse in ihrer Reihenfolge |
| `expclass::attribute::<attribute_id>` | class/* |  | Ein Klassenattribut nach ID |
| `expclass::attributeByIdentifier::<class>::<identifier>` | class/* |  | Ein Klassenattribut nach Klasse und Kennung |
| `expclass::searchableAttributes::<class>` | class/* |  | Die durchsuchbaren Attribute einer Klasse |
| `expclass::requiredAttributes::<class>` | class/* |  | Die Pflichtattribute einer Klasse |
| `expclass::collectorAttributes::<class>` | class/* |  | Die Attribute einer Klasse, die Informationen sammeln |
| `expclass::groups::<class>` | class/* |  | Die Gruppen, in denen eine Klasse ist |
| `expclass::objectCount::<class>` | class/* |  | Zahl der Objekte einer Klasse |
| `expclass::usage::<limit>::<offset>` | class/* |  | Klassen mit der Zahl ihrer Objekte, die meistverwendeten zuerst |
| `expclass::containers` | class/* |  | Klassen, die Container sind |
| `expclass::names::<class>` | class/* |  | Der Name einer Klasse in jeder Sprache |
| `expclass::languages::<class>` | class/* |  | Sprachen, in denen eine Klasse Namen hat |
| `expclass::patterns::<class>` | class/* |  | Muster für Objektnamen und URL-Aliase einer Klasse |
| `expclass::removable::<class>` | class/* |  | Ob die Klasse entfernt werden kann und was das verhindert |
| `expclass::canInstantiate` | user |  | Klassen, von denen der aktuelle Benutzer Objekte anlegen darf |
| `expclass::drafts` | class/* |  | Klassenentwürfe (temporäre Versionen), die bearbeitet werden |
| `expclass::draft::<class_id>` | class/* |  | Ein Klassenentwurf mit seinen Attributen |
| `expclass::dataTypes` | class/* |  | Die für Klassenattribute verfügbaren Datentypen |
| `expclass::sortFields` | user |  | Die Namen, die für die Standardsortierung der Kinder akzeptiert werden |
| `expclass::createDraft` | class/* | POST | Beginnt einen neuen Klassenentwurf. POST: name, identifier, group_id, language, description, is_container, always_available, object_name_pattern, url_alias_pattern, sort_field, sort_order |
| `expclass::editDraft::<class_id>` | class/* | POST | Beginnt oder setzt einen Entwurf einer vorhandenen Klasse fort |
| `expclass::updateDraft::<class_id>` | class/* | POST | Ändert die Felder des Klassenentwurfs (dieselben POST-Felder wie createDraft) |
| `expclass::addAttributeDraft::<class_id>` | class/* | POST | Fügt dem Entwurf ein Attribut hinzu. POST: data_type, identifier, name, description, is_required, is_searchable, can_translate, is_information_collector, language |
| `expclass::updateAttributeDraft::<class_id>::<attribute_id>` | class/* | POST | Ändert ein Attribut des Entwurfs (dieselben POST-Felder außer data_type) |
| `expclass::removeAttributeDraft::<class_id>::<attribute_id>` | class/* | POST | Entfernt ein Attribut aus dem Entwurf |
| `expclass::moveAttributeDraft::<class_id>::<attribute_id>::<direction>` | class/* | POST | Verschiebt ein Attribut des Entwurfs: up, down, top oder bottom |
| `expclass::publishDraft::<class_id>` | class/* | POST | Veröffentlicht den Entwurf als Definition der Klasse |
| `expclass::discardDraft::<class_id>` | class/* | POST | Verwirft den Klassenentwurf |
| `expclass::copy::<class_id>` | class/* | POST | Beginnt einen Entwurf, der eine Kopie einer Klasse ist (publishDraft macht daraus eine Klasse) |
| `expclass::remove::<class_id>` | class/* | POST | Entfernt eine Klasse, die keine Objekte hat |
| `expclass::setName::<class_id>::<language>::<name>` | class/* | POST | Setzt den Namen einer Klasse in einer Sprache (über einen Entwurf, der veröffentlicht wird) |

#### Klassengruppen (`expclassgroup`, 13 Dienste, 5 Schreibvorgänge)

Die Gruppen, in denen Klassen abgelegt sind: auflisten, Mitglieder, anlegen, umbenennen, entfernen (nur leere Gruppen),
eine Klasse einer Gruppe hinzufügen, sie herausnehmen.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expclassgroup::list::<limit>::<offset>` | class/* |  | Alle Klassengruppen mit der Zahl ihrer Klassen |
| `expclassgroup::count` | class/* |  | Zahl der Klassengruppen |
| `expclassgroup::get::<group_id>` | class/* |  | Eine Klassengruppe |
| `expclassgroup::getByName::<name>` | class/* |  | Eine Klassengruppe nach Name |
| `expclassgroup::classes::<group_id>::<limit>::<offset>` | class/* |  | Die Klassen in einer Gruppe |
| `expclassgroup::classCount::<group_id>` | class/* |  | Zahl der Klassen in einer Gruppe |
| `expclassgroup::ofClass::<class>` | class/* |  | Die Gruppen, in denen eine Klasse ist |
| `expclassgroup::isEmpty::<group_id>` | class/* |  | Ob eine Gruppe keine Klassen hat (und entfernt werden kann) |
| `expclassgroup::create::<name>` | class/* | POST | Legt eine Klassengruppe an |
| `expclassgroup::rename::<group_id>::<name>` | class/* | POST | Benennt eine Klassengruppe um |
| `expclassgroup::remove::<group_id>` | class/* | POST | Entfernt eine leere Klassengruppe |
| `expclassgroup::addClass::<group_id>::<class>` | class/* | POST | Legt eine Klasse in einer Gruppe ab |
| `expclassgroup::removeClass::<group_id>::<class>` | class/* | POST | Nimmt eine Klasse aus einer Gruppe heraus (eine Klasse behält mindestens eine Gruppe) |

#### Attribute und Datentypen (`expattribute`, 21 Dienste, 5 Schreibvorgänge)

Die Attribute von Inhaltsobjekten (Werte pro Datentyp, Zeichenketten, Titel, Verlauf, Sprachen) und der Katalog der
Datentypen; ein oder mehrere Attribute eines Objekts (eine neue veröffentlichte Version) oder eines Entwurfs setzen.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expattribute::get::<object_id>::<identifier>::<language>` | content/read |  | Ein Attribut eines Objekts mit seinem Wert |
| `expattribute::getById::<attribute_id>::<version>` | content/read |  | Ein Attribut nach seiner ID und Version |
| `expattribute::value::<object_id>::<identifier>::<language>` | content/read |  | Nur der Wert eines Attributs |
| `expattribute::string::<object_id>::<identifier>::<language>` | content/read |  | Das Attribut in seiner Zeichenkettenform (dem Format, das Schreibvorgänge akzeptieren) |
| `expattribute::title::<object_id>::<identifier>::<language>` | content/read |  | Der Titeltext eines Attributs |
| `expattribute::hasContent::<object_id>::<identifier>::<language>` | content/read |  | Ob das Attribut Inhalt hat |
| `expattribute::dataType::<object_id>::<identifier>` | content/read |  | Der Datentyp eines Attributs |
| `expattribute::classAttribute::<object_id>::<identifier>` | content/read |  | Die Definition des Klassenattributs eines Attributs |
| `expattribute::history::<object_id>::<identifier>::<limit>` | content/read |  | Der Wert eines Attributs in jeder Version, die neueste zuerst |
| `expattribute::languages::<object_id>::<identifier>` | content/read |  | Der Wert eines Attributs in jeder Sprache |
| `expattribute::missingRequired::<object_id>::<version>` | content/read |  | Pflichtattribute ohne Inhalt in einer Version |
| `expattribute::byDataType::<datatype>::<limit>::<offset>` | content/read |  | Attribute eines Datentyps über die aktuellen Objekte hinweg (die lesbaren) |
| `expattribute::dataTypes` | user |  | Der Katalog der Datentypen |
| `expattribute::dataTypeInfo::<datatype>` | user |  | Ein Datentyp: Name, Flags und die Klassen, die ihn verwenden |
| `expattribute::dataTypeClasses::<datatype>` | class/* |  | Die Klassen, die ein Attribut eines Datentyps haben |
| `expattribute::set::<object_id>::<identifier>` | content/edit | POST | Setzt ein Attribut und veröffentlicht eine neue Version. POST: value, language |
| `expattribute::setMany::<object_id>` | content/edit | POST | Setzt mehrere Attribute und veröffentlicht eine neue Version. POST: values (JSON identifier => string), language |
| `expattribute::clear::<object_id>::<identifier>` | content/edit | POST | Leert ein Attribut und veröffentlicht eine neue Version |
| `expattribute::setDraft::<object_id>::<version>::<identifier>` | content/edit | POST | Setzt ein Attribut einer Entwurfsversion (nicht veröffentlicht). POST: value, language |
| `expattribute::setDraftMany::<object_id>::<version>` | content/edit | POST | Setzt mehrere Attribute einer Entwurfsversion. POST: values (JSON), language |
| `expattribute::validateDraft::<object_id>::<version>` | content/edit |  | Prüft einen Entwurf: Pflichtattribute ausgefüllt, Eingaben gültig |

#### Versionen und Entwürfe (`expversion`, 25 Dienste, 8 Schreibvorgänge)

Der Versionsverlauf eines Objekts, Entwürfe eines Benutzers und aller Benutzer, zwei Versionen vergleichen; einen
Entwurf anlegen (in einer Sprache), veröffentlichen, verwerfen, alte Versionen entfernen, zurücksetzen.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expversion::list::<object_id>::<limit>::<offset>::<status>` | content/versionread |  | Die Versionen eines Objekts, die neueste zuerst, seitenweise |
| `expversion::count::<object_id>` | content/versionread |  | Zahl der Versionen eines Objekts |
| `expversion::get::<object_id>::<version>` | content/versionread |  | Eine Version |
| `expversion::current::<object_id>` | content/versionread |  | Die aktuelle (veröffentlichte) Version |
| `expversion::published::<object_id>` | content/read |  | Die Nummer der veröffentlichten Version |
| `expversion::status::<object_id>::<version>` | content/versionread |  | Der Status einer Version |
| `expversion::creator::<object_id>::<version>` | content/versionread |  | Das Erstellerobjekt einer Version |
| `expversion::dataMap::<object_id>::<version>::<language>` | content/versionread |  | Die Attribute einer Version mit ihren Werten |
| `expversion::translations::<object_id>::<version>` | content/versionread |  | Die Sprachen einer Version |
| `expversion::nodeAssignments::<object_id>::<version>` | content/versionread |  | Die Platzierungen, denen eine Version zugewiesen ist |
| `expversion::compare::<object_id>::<version_a>::<version_b>::<language>` | content/diff |  | Unterschiede zwischen zwei Versionen, Attribut für Attribut |
| `expversion::drafts::<object_id>` | content/versionread |  | Die Entwürfe eines Objekts |
| `expversion::myDrafts::<limit>::<offset>` | content/edit |  | Die Entwürfe des aktuellen Benutzers zu allen Objekten, seitenweise |
| `expversion::allDrafts::<limit>::<offset>` | content/versionread |  | Entwürfe aller Benutzer (braucht uneingeschränkten Zugriff auf Versionen), seitenweise |
| `expversion::pending::<limit>::<offset>` | content/pendinglist |  | Versionen, die in der Veröffentlichungswarteschlange warten, seitenweise |
| `expversion::hasConflicts::<object_id>::<version>::<language>` | content/edit |  | Ob ein Entwurf älter ist als die veröffentlichte Version seiner Sprache |
| `expversion::viewUrl::<object_id>::<version>::<language>` | content/versionread |  | Die relative URL, die eine Version in der Vorschau zeigt |
| `expversion::createDraft::<object_id>` | content/edit | POST | Legt einen Entwurf aus der aktuellen oder einer angegebenen Version an. POST: language, copy_from_version |
| `expversion::createDraftIn::<object_id>::<language>` | content/edit | POST | Legt einen Entwurf für eine neue oder vorhandene Sprache an. POST: copy_from_language |
| `expversion::publish::<object_id>::<version>` | content/publish | POST | Veröffentlicht einen Entwurf |
| `expversion::discard::<object_id>::<version>` | content/edit | POST | Verwirft einen Entwurf |
| `expversion::remove::<object_id>::<version>` | content/versionremove | POST | Entfernt eine archivierte oder Entwurfsversion (nie die veröffentlichte) |
| `expversion::removeArchived::<object_id>::<keep>` | content/versionremove | POST | Entfernt archivierte Versionen eines Objekts und behält die neuesten N |
| `expversion::revert::<object_id>::<version>` | content/edit | POST | Legt einen Entwurf aus einer alten Version an (veröffentlichen Sie ihn, um den alten Inhalt zurückzubringen). POST: language |
| `expversion::cleanupDrafts::<hours>` | content/versionremove | POST | Entfernt interne (nicht gespeicherte) Entwürfe, die älter als eine Zahl von Stunden sind, aus allen Objekten. Braucht uneingeschränktes Entfernen von Versionen |

#### Übersetzungen (`exptranslation`, 19 Dienste, 5 Schreibvorgänge)

Die Sprachen der Website und die Übersetzungen von Objekten: was existiert, was fehlt, Stand eines Teilbaums;
übersetzen, eine Sprache kopieren, eine Übersetzung entfernen, eine Sprache der Website hinzufügen und entfernen.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `exptranslation::languages` | user |  | Die Sprachen der Website |
| `exptranslation::language::<locale>` | user |  | Eine Sprache der Website nach Locale-Code |
| `exptranslation::prioritized` | public |  | Die Sprachcodes des Siteaccess in der Reihenfolge ihrer Priorität |
| `exptranslation::topPriority` | public |  | Die erste Sprache des Siteaccess |
| `exptranslation::stats` | content/translations |  | Objekte und Klassen pro Sprache der Website |
| `exptranslation::knownLocales::<text>::<limit>::<offset>` | content/translations |  | Locales, die als Sprachen der Website hinzugefügt werden können |
| `exptranslation::ofObject::<object_id>` | content/read |  | Die Übersetzungen eines Objekts mit Namen und Flag für die Ausgangssprache |
| `exptranslation::missing::<object_id>` | content/read |  | Sprachen der Website, in denen ein Objekt keine Übersetzung hat |
| `exptranslation::content::<object_id>::<language>` | content/read |  | Die Attribute eines Objekts in einer Sprache |
| `exptranslation::names::<object_id>` | content/read |  | Der Objektname in jeder Sprache |
| `exptranslation::nodeNames::<node_id>` | content/read |  | Die Namen eines Knotens in jeder Sprache |
| `exptranslation::classNames::<class>` | user |  | Der Name einer Klasse in jeder Sprache |
| `exptranslation::canTranslate::<object_id>` | user |  | Ob der aktuelle Benutzer ein Objekt übersetzen kann, und in welche Sprachen |
| `exptranslation::status::<node_id>` | content/read |  | Vollständigkeit der Übersetzungen eines Teilbaums: Objekte pro Sprache |
| `exptranslation::translate::<object_id>::<language>` | content/translate | POST | Legt eine Übersetzung an oder ändert sie und veröffentlicht sie. POST: attributes (JSON), copy_from_language |
| `exptranslation::copyLanguage::<object_id>::<from>::<to>` | content/translate | POST | Kopiert den Inhalt einer Sprache in eine andere und veröffentlicht |
| `exptranslation::remove::<object_id>::<language>` | content/translate | POST | Entfernt eine Übersetzung eines Objekts |
| `exptranslation::addLanguage::<locale>` | content/translations | POST | Fügt eine Sprache der Website hinzu (Locale-Code). POST: name |
| `exptranslation::removeLanguage::<locale>` | content/translations | POST | Entfernt eine Sprache der Website, die kein Objekt und keine Klasse verwendet |

#### Relationen (`exprelation`, 15 Dienste, 4 Schreibvorgänge)

Relationen zwischen Objekten in beiden Richtungen nach Typ (common, embed, link, attribute), Anzahlen, defekte
Relationen; allgemeine Relationen hinzufügen, entfernen und ersetzen.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `exprelation::related::<object_id>::<type>::<limit>::<offset>` | content/read |  | Objekte, auf die sich ein Objekt bezieht (in der aktuellen Version), seitenweise. type: common, embed, link, attribute oder all |
| `exprelation::reverse::<object_id>::<type>::<limit>::<offset>` | content/read |  | Objekte, die sich auf ein Objekt beziehen, seitenweise |
| `exprelation::counts::<object_id>` | content/read |  | Anzahl der Relationen pro Typ und Richtung |
| `exprelation::relatedCount::<object_id>::<type>` | content/read |  | Zahl der Relationen, die von einem Objekt ausgehen |
| `exprelation::reverseCount::<object_id>::<type>` | content/read |  | Zahl der Objekte, die sich auf ein Objekt beziehen |
| `exprelation::reverseCountForNodes::<node_ids>` | content/read |  | Anzahl der umgekehrten Relationen für eine Liste von Knoten |
| `exprelation::byAttribute::<object_id>::<identifier>` | content/read |  | Über ein Attribut verbundene Objekte |
| `exprelation::exists::<from_object_id>::<to_object_id>` | content/read |  | Ob eine Relation zwischen zwei Objekten existiert, und ihre Typen |
| `exprelation::neighbours::<object_id>::<limit>` | content/read |  | Beide Richtungen auf einmal: verbunden und umgekehrt verbunden, schlank |
| `exprelation::broken::<object_id>` | content/read |  | Relationen eines Objekts, die auf Objekte zeigen, die nicht mehr existieren oder nicht veröffentlicht sind |
| `exprelation::types` | user |  | Die Namen der Relationstypen und ihre Codes |
| `exprelation::add::<from_object_id>::<to_object_id>` | content/edit | POST | Fügt eine Relation von einem Objekt zu einem anderen hinzu. POST: type (common, embed, link; Standard common), version |
| `exprelation::remove::<from_object_id>::<to_object_id>` | content/edit | POST | Entfernt eine Relation. POST: type, version |
| `exprelation::removeAll::<object_id>` | content/edit | POST | Entfernt jede allgemeine Relation eines Objekts (Relationen vom Typ embed, link und attribute gehören zum Inhalt) |
| `exprelation::replace::<object_id>` | content/edit | POST | Macht die allgemeinen Relationen eines Objekts genau zu der angegebenen Liste. POST: object_ids |

#### Platzierungen (`explocation`, 15 Dienste, 5 Schreibvorgänge)

Die Orte, an denen ein Objekt lebt: Liste, Hauptplatzierung, Zuweisungen, Kandidaten und Prüfungen; hinzufügen,
entfernen, die Hauptplatzierung setzen (große Änderungen als Jobs).

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `explocation::list::<object_id>` | content/read |  | Alle Platzierungen (Knoten) eines Objekts |
| `explocation::count::<object_id>` | content/read |  | Zahl der Platzierungen eines Objekts |
| `explocation::main::<object_id>` | content/read |  | Die Hauptplatzierung eines Objekts |
| `explocation::parents::<object_id>` | content/read |  | Die Elternknoten der Platzierungen eines Objekts |
| `explocation::assignments::<object_id>` | content/read |  | Die Knotenzuweisungen eines Objekts (aktuelle Version) |
| `explocation::isMain::<node_id>` | content/read |  | Ob ein Knoten die Hauptplatzierung seines Objekts ist |
| `explocation::canAdd::<object_id>::<parent_node_id>` | user |  | Ob der aktuelle Benutzer das Objekt einem Knoten hinzufügen kann |
| `explocation::canRemove::<node_id>` | user |  | Ob eine Platzierung entfernt werden kann (sie ist nicht die letzte und hat keine Kinder) |
| `explocation::candidates::<node_id>::<class>::<limit>::<offset>` | content/read |  | Containerknoten unter einem Knoten, denen ein Objekt einer Klasse hinzugefügt werden kann |
| `explocation::orphans::<limit>::<offset>` | content/read |  | Veröffentlichte Objekte, die keinen Knoten haben (braucht uneingeschränktes Lesen) |
| `explocation::add::<object_id>::<parent_node_id>` | content/manage_locations | POST | Fügt eine Platzierung eines Objekts unter einem Knoten hinzu |
| `explocation::addMany::<target_node_id>` | content/manage_locations | POST | Fügt die Objekte mehrerer Knoten unter einem Knoten hinzu. POST: node_ids, mode |
| `explocation::remove::<node_id>` | content/manage_locations | POST | Entfernt eine Platzierung eines Objekts (nicht die letzte) |
| `explocation::removeMany` | content/manage_locations | POST | Entfernt mehrere Platzierungen. POST: node_ids, mode |
| `explocation::setMain::<node_id>` | content/manage_locations | POST | Macht eine Platzierung zur Hauptplatzierung |

#### Papierkorb (`exptrash`, 12 Dienste, 4 Schreibvorgänge)

Objekte im Papierkorb: auflisten, filtern, ursprünglicher Elternknoten; wiederherstellen, eines oder mehrere endgültig
löschen, den Papierkorb leeren (Bestätigungsfeld).

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `exptrash::list::<sort>::<order>::<limit>::<offset>::<filter>` | content/restore |  | Objekte im Papierkorb, seitenweise und sortiert (sort: name, class_name, published, modified, section) |
| `exptrash::count` | content/restore |  | Zahl der Objekte im Papierkorb |
| `exptrash::get::<object_id>` | content/restore |  | Ein Objekt im Papierkorb |
| `exptrash::isTrashed::<object_id>` | content/restore |  | Ob ein Objekt im Papierkorb ist |
| `exptrash::originalParent::<object_id>` | content/restore |  | Der Knoten, aus dem ein Objekt im Papierkorb entfernt wurde, wenn er noch an derselben Stelle existiert |
| `exptrash::byClass::<class>::<limit>::<offset>` | content/restore |  | Objekte einer Klasse im Papierkorb |
| `exptrash::since::<since>::<limit>::<offset>` | content/restore |  | Seit einem Zeitpunkt in den Papierkorb verschobene Objekte (Zeitstempel oder Datum) |
| `exptrash::canEmpty` | user |  | Ob der aktuelle Benutzer aus dem Papierkorb endgültig löschen darf |
| `exptrash::restore::<object_id>` | content/restore | POST | Stellt ein Objekt unter seinem ursprünglichen Elternknoten oder unter POST parent_node_id wieder her |
| `exptrash::purge::<object_id>` | content/cleantrash | POST | Entfernt ein Objekt endgültig aus dem Papierkorb |
| `exptrash::purgeMany` | content/cleantrash | POST | Löscht mehrere Objekte endgültig. POST: object_ids |
| `exptrash::emptyTrash` | content/cleantrash | POST | Leert den ganzen Papierkorb. POST: confirm=yes |

#### Sektionen (`expsection`, 17 Dienste, 5 Schreibvorgänge)

Sektionen und was darin ist; anlegen, ändern, entfernen; ein Objekt oder einen Teilbaum zuweisen (Jobs für große
Teilbäume).

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expsection::list::<limit>::<offset>` | section/view |  | Alle Sektionen |
| `expsection::count` | section/view |  | Zahl der Sektionen |
| `expsection::get::<section_id>` | section/view |  | Eine Sektion nach ID |
| `expsection::getByIdentifier::<identifier>` | section/view |  | Eine Sektion nach Kennung |
| `expsection::objects::<section_id>::<limit>::<offset>` | content/read |  | Objekte (Hauptknoten) in einer Sektion, seitenweise |
| `expsection::objectCount::<section_id>` | section/view |  | Zahl der Objekte in einer Sektion |
| `expsection::usage` | section/view |  | Sektionen mit der Zahl ihrer Objekte |
| `expsection::canRemove::<section_id>` | section/view |  | Ob eine Sektion entfernt werden kann und was das verhindert |
| `expsection::assignable` | user |  | Sektionen, die der aktuelle Benutzer zuweisen darf |
| `expsection::navigationParts` | section/view |  | Die Navigationsteile, die eine Sektion verwenden kann |
| `expsection::ofObject::<object_id>` | content/read |  | Die Sektion eines Objekts |
| `expsection::ofNode::<node_id>` | content/read |  | Die Sektion eines Knotens |
| `expsection::create` | section/edit | POST | Legt eine Sektion an. POST: name, identifier, navigation_part |
| `expsection::update::<section_id>` | section/edit | POST | Ändert eine Sektion (dieselben POST-Felder) |
| `expsection::remove::<section_id>` | section/edit | POST | Entfernt eine nicht verwendete Sektion |
| `expsection::assign::<object_id>::<section_id>` | section/assign | POST | Weist ein Objekt mit den Objekten darunter einer Sektion zu. POST: scope (subtree, inherited oder object) |
| `expsection::assignSubtree::<node_id>::<section_id>` | section/assign | POST | Weist einen Knoten und seinen Teilbaum einer Sektion zu. POST: mode, scope (subtree, inherited oder object) |

#### Objektzustände (`expstate`, 23 Dienste, 9 Schreibvorgänge)

Zustandsgruppen und Zustände, die Zustände eines Objekts, Objekte in einem Zustand; anlegen, ändern, entfernen,
ordnen; einem Objekt oder einem Teilbaum zuweisen (Jobs für große Teilbäume).

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expstate::groups::<limit>::<offset>` | state/administrate |  | Die Zustandsgruppen mit ihren Zuständen |
| `expstate::groupCount` | state/administrate |  | Zahl der Zustandsgruppen |
| `expstate::group::<group>` | state/administrate |  | Eine Zustandsgruppe nach ID oder Kennung, mit ihren Zuständen |
| `expstate::states::<group>` | state/administrate |  | Die Zustände einer Gruppe in ihrer Reihenfolge |
| `expstate::state::<state_id>` | state/administrate |  | Ein Zustand nach ID |
| `expstate::stateByIdentifier::<group>::<identifier>` | state/administrate |  | Ein Zustand nach Gruppe und Kennung |
| `expstate::translations::<state_id>` | state/administrate |  | Die Namen und Beschreibungen eines Zustands in jeder Sprache |
| `expstate::groupTranslations::<group>` | state/administrate |  | Die Namen und Beschreibungen einer Gruppe in jeder Sprache |
| `expstate::defaultState::<group>` | state/administrate |  | Der erste (Standard-)Zustand einer Gruppe |
| `expstate::ofObject::<object_id>` | content/read |  | Die Zustände eines Objekts |
| `expstate::allowedForObject::<object_id>` | content/read |  | Die Zustände, die der aktuelle Benutzer einem Objekt zuweisen darf |
| `expstate::objects::<state_id>::<limit>::<offset>` | content/read |  | Objekte (Hauptknoten) in einem Zustand, seitenweise |
| `expstate::objectCount::<state_id>` | state/administrate |  | Zahl der Objekte in einem Zustand |
| `expstate::limitations` | state/administrate |  | Die Zustandseinschränkungen, die Richtlinien verwenden können |
| `expstate::createGroup` | state/administrate | POST | Legt eine Zustandsgruppe an. POST: identifier, name, description, language |
| `expstate::updateGroup::<group>` | state/administrate | POST | Ändert eine Zustandsgruppe. POST: identifier, name, description, language |
| `expstate::removeGroup::<group>` | state/administrate | POST | Entfernt eine Zustandsgruppe mit ihren Zuständen |
| `expstate::createState::<group>` | state/administrate | POST | Legt einen Zustand in einer Gruppe an. POST: identifier, name, description, language |
| `expstate::updateState::<state_id>` | state/administrate | POST | Ändert einen Zustand. POST: identifier, name, description, language |
| `expstate::removeState::<state_id>` | state/administrate | POST | Entfernt einen Zustand (seine Objekte erhalten den Standard der Gruppe) |
| `expstate::reorderStates::<group>` | state/administrate | POST | Ordnet die Zustände einer Gruppe. POST: state_ids (die Zustands-IDs der Gruppe in der neuen Reihenfolge) |
| `expstate::assign::<object_id>::<state_id>` | state/assign | POST | Weist einem Objekt einen Zustand zu |
| `expstate::assignSubtree::<node_id>::<state_id>` | state/assign | POST | Weist einem Knoten und seinem Teilbaum einen Zustand zu. POST: mode |

#### URL-Aliase und Platzhalter (`expurlalias`, 24 Dienste, 6 Schreibvorgänge)

Die sprechenden URLs: Aliase von Knoten, einen Pfad auflösen, Text normalisieren, globale Aliase, Weiterleitungen,
Platzhalter; eigene Aliase und Platzhalter anlegen und entfernen.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expurlalias::forNode::<node_id>::<type>::<limit>::<offset>` | content/read |  | Die URL-Aliase eines Knotens. type: alias (eigene), name (erzeugte) oder all |
| `expurlalias::forObject::<object_id>` | content/read |  | Die erzeugte URL jeder Platzierung eines Objekts |
| `expurlalias::path::<node_id>` | content/read |  | Der URL-Pfad eines Knotens (erzeugter Alias) |
| `expurlalias::resolve::<path>` | user |  | Löst einen URL-Pfad in seine Aktion und seinen Knoten auf |
| `expurlalias::exists::<path>` | content/read |  | Ob ein Pfad von einem Alias belegt ist |
| `expurlalias::children::<path>::<limit>::<offset>` | content/urltranslator |  | Die Alias-Elemente direkt unter einem Pfad (leerer Pfad: die oberste Ebene) |
| `expurlalias::normalize::<text>` | user |  | Wandelt Text in die Form um, die ihm die Regeln der URL-Umwandlung geben |
| `expurlalias::normalizePath::<path>` | user |  | Normalisiert jedes Element eines Pfads |
| `expurlalias::list::<type>::<limit>::<offset>::<text>` | content/urltranslator |  | Alle eigenen Aliase (type alias), name oder all, seitenweise |
| `expurlalias::count::<type>` | content/urltranslator |  | Zahl der Aliase eines Typs |
| `expurlalias::redirects::<limit>::<offset>` | content/urltranslator |  | Aliase, die auf die echte URL weiterleiten, seitenweise |
| `expurlalias::byAction::<action>` | content/urltranslator |  | Die Aliase einer Aktion wie eznode:43 oder module:search |
| `expurlalias::pathPrefix` | public |  | Das Pfadpräfix des Siteaccess |
| `expurlalias::wildcards::<limit>::<offset>` | content/urltranslator |  | Die URL-Platzhalter, seitenweise |
| `expurlalias::wildcardCount` | content/urltranslator |  | Zahl der Platzhalter |
| `expurlalias::wildcard::<wildcard_id>` | content/urltranslator |  | Ein Platzhalter nach ID |
| `expurlalias::wildcardBySource::<source>` | content/urltranslator |  | Ein Platzhalter nach seiner Quell-URL |
| `expurlalias::wildcardMatches::<path>` | content/urltranslator |  | Ob ein Pfad von irgendeinem Platzhalter erfasst wird |
| `expurlalias::createAlias::<node_id>` | content/urltranslator | POST | Fügt einem Knoten einen eigenen URL-Alias hinzu. POST: alias, language, parent_is_root, redirects |
| `expurlalias::removeAlias::<node_id>` | content/urltranslator | POST | Entfernt eigene Aliase eines Knotens. POST: elements (Liste von parent.md5.language aus forNode) |
| `expurlalias::removeAllAliases::<node_id>` | content/urltranslator | POST | Entfernt jeden eigenen Alias eines Knotens |
| `expurlalias::createWildcard` | content/urltranslator | POST | Fügt einen Platzhalter hinzu. POST: source, destination, type (forward oder direct) |
| `expurlalias::removeWildcard::<wildcard_id>` | content/urltranslator | POST | Entfernt einen Platzhalter |
| `expurlalias::removeWildcards` | content/urltranslator | POST | Entfernt mehrere Platzhalter. POST: ids |

#### Suche (`expsearch`, 18 Dienste, 3 Schreibvorgänge)

Volltextsuche mit seitenweiser Ausgabe, Filtern nach Klasse, Sektion, Teilbaum und Datum und Facetten; Vorschläge,
Status und Statistik des Index; ein Objekt indexieren und aus dem Index nehmen.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expsearch::search::<text>::<limit>::<offset>::<filter>` | content/read |  | Durchsucht die Inhalte. filter: class[], section, subtree[], date (day, week, month, 3months, year) |
| `expsearch::count::<text>::<filter>` | content/read |  | Zahl der Treffer einer Suche |
| `expsearch::facets::<text>::<filter>` | content/read |  | Anzahl der Treffer pro Klasse und pro Sektion (über die ersten 500 Treffer) |
| `expsearch::byClass::<text>::<classes>::<limit>::<offset>` | content/read |  | Sucht innerhalb von Klassen |
| `expsearch::inSubtree::<text>::<node_id>::<limit>::<offset>` | content/read |  | Sucht unter einem Knoten |
| `expsearch::bySection::<text>::<section_id>::<limit>::<offset>` | content/read |  | Sucht innerhalb einer Sektion |
| `expsearch::byAttribute::<text>::<class_attribute_id>::<limit>::<offset>` | content/read |  | Durchsucht die Werte eines Klassenattributs |
| `expsearch::similar::<object_id>::<limit>` | content/read |  | Objekte, die einem Objekt ähnlich sind (sucht mit seinem Namen) |
| `expsearch::suggest::<prefix>::<limit>` | content/read |  | Wörter des Index, die mit einem Präfix beginnen, die meistverwendeten zuerst |
| `expsearch::normalize::<text>` | user |  | Der Text, wie die Suchmaschine ihn normalisiert |
| `expsearch::engine` | user |  | Die verwendete Suchmaschine und ihre Flags |
| `expsearch::searchableClasses` | user |  | Klassen mit durchsuchbaren Attributen |
| `expsearch::indexStatus::<object_id>` | content/read |  | Ob ein Objekt Wörter im Index hat |
| `expsearch::stats` | setup/administrate |  | Größe des Index: Wörter, Links, Objekte mit Wörtern |
| `expsearch::topPhrases::<limit>::<offset>` | setup/administrate |  | Die häufigsten Suchbegriffe der Website mit ihren durchschnittlichen Treffern |
| `expsearch::reindex::<object_id>` | content/edit | POST | Indexiert ein Objekt erneut |
| `expsearch::removeFromIndex::<object_id>` | content/edit | POST | Entfernt ein Objekt aus dem Index |
| `expsearch::clearPhrases` | setup/administrate | POST | Leert die gespeicherte Statistik der Suchbegriffe. POST: confirm=yes |

#### Inhaltsjobs (`expcontentjob`, 26 Dienste, 14 Schreibvorgänge)

Die Hintergrundjobs für große Operationen: auflisten und verfolgen, abschätzen, ob eine Operation sofort oder als Job
läuft, einen Job jedes Typs anlegen, abbrechen, fortsetzen, starten.

| Aufruf | Zugriff | Schreiben | Zusammenfassung |
|---|---|---|---|
| `expcontentjob::list::<state>::<limit>::<offset>` | content/jobs |  | Die Jobs des aktuellen Benutzers, die neuesten zuerst. state: queued, running, done, failed, cancelled oder all |
| `expcontentjob::listAll::<state>::<limit>::<offset>` | setup/administrate |  | Die Jobs aller Benutzer (braucht setup/administrate) |
| `expcontentjob::active` | content/jobs |  | Die wartenden und laufenden Jobs des aktuellen Benutzers |
| `expcontentjob::summary` | content/jobs |  | Anzahl der Jobs des aktuellen Benutzers pro Zustand |
| `expcontentjob::get::<job_id>::<log_lines>` | content/jobs |  | Ein Job mit seinem Fortschritt, seinem Ergebnis und den letzten Logzeilen |
| `expcontentjob::progress::<job_id>` | content/jobs |  | Nur der Fortschritt eines Jobs: erledigt, gesamt, Prozent, Phase, Meldung |
| `expcontentjob::log::<job_id>::<lines>` | content/jobs |  | Die letzten Zeilen des Job-Logs |
| `expcontentjob::result::<job_id>` | content/jobs |  | Das Ergebnis eines abgeschlossenen Jobs |
| `expcontentjob::types` | content/jobs |  | Die registrierten Job-Typen mit ihren Parametern |
| `expcontentjob::settings` | content/jobs |  | Die Grenzen, die zwischen sofort und Job entscheiden: SynchronousLimit, NowLimit, BatchSize |
| `expcontentjob::estimate::<type>::<params>` | content/jobs |  | Wie viele Knoten eine Operation berührt und ob sie sofort oder als Job läuft. params wie bei create |
| `expcontentjob::lockOf::<node_id>` | content/jobs |  | Der Job, der einen Knoten sperrt, falls es einen gibt |
| `expcontentjob::create::<type>` | content/jobs | POST | Legt einen Job an und startet seinen Worker. POST: params (JSON, siehe types), spawn (Standard 1) |
| `expcontentjob::remove` | content/remove | POST | Job: entfernt Knoten. POST: node_ids, move_to_trash, spawn |
| `expcontentjob::copy` | content/create | POST | Job: kopiert einen Teilbaum. POST: source_node_id, destination_node_id, all_versions, keep_creator, keep_time, spawn |
| `expcontentjob::move` | content/move | POST | Job: verschiebt einen Teilbaum. POST: node_id, new_parent_node_id, spawn |
| `expcontentjob::hide` | content/hide | POST | Job: verbirgt einen Teilbaum. POST: node_id, spawn |
| `expcontentjob::reveal` | content/hide | POST | Job: zeigt einen Teilbaum wieder an. POST: node_id, spawn |
| `expcontentjob::section` | section/assign | POST | Job: weist einem Teilbaum eine Sektion zu. POST: node_id, section_id, spawn |
| `expcontentjob::state` | state/assign | POST | Job: weist einem Teilbaum einen Zustand zu. POST: node_id, state_id, spawn |
| `expcontentjob::addLocation` | content/manage_locations | POST | Job: fügt Platzierungen hinzu. POST: node_ids, target_node_id, spawn |
| `expcontentjob::removeLocation` | content/manage_locations | POST | Job: entfernt Platzierungen. POST: node_ids, spawn |
| `expcontentjob::cancel::<job_id>` | content/jobs | POST | Bricht einen Job ab (ein laufender Job hält nach seinem aktuellen Stapel an) |
| `expcontentjob::resume::<job_id>` | content/jobs | POST | Setzt einen fehlgeschlagenen Job fort oder einen, dessen Worker gestorben ist |
| `expcontentjob::spawn::<job_id>` | content/jobs | POST | Startet den Worker eines wartenden Jobs |
| `expcontentjob::purgeFinished::<days>` | setup/administrate | POST | Entfernt abgeschlossene Jobs, die älter als eine Zahl von Tagen sind (braucht setup/administrate) |

Insgesamt: 350 Inhaltsdienste in 15 Domänen (112 Schreibvorgänge).
<!-- expservices-content:end -->

**Demomodus.** Mit der Umgebungsvariablen `EXP_DEMO_MODE=1` (eine öffentliche Demo) weigern sich die Konto- und
Benutzerdienste, das Passwort eines Administrators zu ändern oder zurückzusetzen: HTTP 403 mit „Diese Installation ist
eine öffentliche Demo: Die Passwörter von Administratoren können nicht geändert oder zurückgesetzt werden.“ für
`expaccount.reset`, `expaccount.changePassword` und `expuser.setPassword`; kein Schlüssel und keine E-Mail für einen
Administrator aus `expaccount.forgotRequest`. [Demomodus](demomodus.md).

## Verwandte Seiten

- [Funktionsseite zu den entfernten Diensten (expservices)](../../funktionen/6.0/entfernte-dienste-expservices.md)
- [Anleitung zu entfernten Diensten und Apps](../../anleitungen/entfernte-dienste-und-apps.md)
- [Inhaltsjobs](inhaltsjobs.md)
- [Audit](audit.md)
- [YUI-Entfernung](yui-entfernung.md)
