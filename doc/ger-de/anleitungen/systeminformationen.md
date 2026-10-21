# Systeminformationen: worauf die Site läuft und was Aufmerksamkeit braucht

Diese Anleitung erklärt die Administrationsseite **Setup > Systeminformationen** (`/setup/info`) und den Befehl
`./console exp:system:info`: welcher Server geantwortet hat, wie er PHP ausführt, die Zustandsprüfungen und was bei
jeder zu tun ist, die Übersichtskarten, die ausführlichen Teile darunter, den Bericht für eine Supportanfrage und was
niemals angezeigt wird.

Sie richtet sich an Administratoren und an alle, die eine Site betreuen. Jede Prüfung, jedes Feld und jeder Pfad unten
wurde gegen den Code geprüft (`kernel/classes/expsystemreport.php`, `kernel/classes/expsystemreportmask.php`,
`kernel/private/classes/views/setup/info.php`, `kernel/private/classes/commands/systeminfo.php`). Die genannten Zahlen
stammen vom Demonstrationsserver am 6. Oktober 2026, nur lesend erhoben.

[Anleitungen](README.md) · Verwandte Anleitungen: [Bereitstellung](bereitstellung.md), [Betrieb einer Site](betrieb-einer-website.md),
[Cronjobs](cronjobs.md), [Sicherheit und Audit](sicherheit-und-audit.md)

## Kurz gesagt

- **Setup > Systeminformationen** erfordert die Richtlinie `setup/system_info`. Die Seite liest nur; sie ändert nichts.
  (Die Schaltflächen des HTTP-Cache, des Abfrage-Cache und des SQL-Profils, weiter unten eingeklappt, senden an sie
  und erfordern `setup/managecache`.)
- Die Seite sagt zuerst, **welcher Server geantwortet hat**: Apache mit PHP-FPM (und dessen Pool), Exponential
  Velocity (mit seinem Worker-Modell und seiner Release), FrankenPHP, der eingebaute Server von PHP. Jeder hat sein
  eigenes PHP, OPcache und eigene Einstellungen, daher zeigt dieselbe Seite unter `https://example.org/admin` und auf
  dem Port von Velocity unterschiedliche Zahlen. Öffnen Sie sie auf beiden.
- **Zustandsprüfungen** stehen an erster Stelle, Fehler und Warnungen vor Hinweisen, jeweils mit dem, was zu tun ist.
  Die Prüfungen, die in Ordnung sind, sind unter „N Prüfungen in Ordnung“ eingeklappt.
- **Übersichtskarten**: Exponential, Server, PHP, OPcache und APCu, Datenbank, Speicher, Caches, Cronjobs, E-Mail,
  Gebietsschema und Zeit, Maschine.
- **Bericht herunterladen (.txt)**, **Als JSON herunterladen** und **Bericht als Text kopieren** liefern denselben
  Bericht für eine Supportanfrage. Passwörter, Schlüssel, Tokens, Session-IDs, Zugangsdaten in Adressen und
  vollständige Serverpfade stehen niemals darin.
- **Größen messen** (`/setup/info/sizes`) ergänzt die Größe von `var/`, jedes Cache-Verzeichnisses und einer
  Datenbank auf einem Server. Das Messen liest jede Datei von `var/` einmal (die Cache-Verzeichnisse werden im selben
  Durchlauf gezählt), innerhalb von zehn Sekunden; das Ergebnis wird zehn Minuten lang in
  `var/<site>/cache/exp/system-report-sizes-<key>.json` aufbewahrt und von Apaches PHP-FPM und Velocity gemeinsam
  genutzt, daher gibt die Seite an, wann gemessen wurde. **Erneut messen** misst neu.
- `./console exp:system:info [--json] [--sizes] --allow-root-user` gibt den Bericht des Kommandozeilen-PHP aus und
  endet mit 1, wenn eine Prüfung fehlschlägt.

## 1. Die Seite öffnen

Setup > Systeminformationen, oder `/setup/info` im Administrations-Siteaccess. Das linke Menü von Setup führt sie auf.

| Adresse | Was sie liefert |
|---|---|
| `/setup/info` | Die Seite |
| `/setup/info/sizes` | Die Seite, mit Verzeichnis- und Datenbankgrößen (höchstens zehn Minuten zuvor gemessen) |
| `/setup/info/report` | Den Bericht als Textdatei (`exponential-system-information-<date>.txt`) |
| `/setup/info/json` | Den Bericht als JSON |
| `/setup/info/php` | `phpinfo()` des Serverprozesses, ohne seine Anfrage- und Umgebungsteile |

Alle fünf erfordern `setup/system_info`. Vergeben Sie die Richtlinie nur an Administratoren: Auch maskiert verrät die
Seite viel über die Installation.

## 2. Welcher Server geantwortet hat

Das blaue Banner (unter Velocity orange) nennt den Server, der diese Anfrage beantwortet hat, und wie er PHP ausführt:

| Server | Was die Seite sagt | Was das für die Zahlen bedeutet |
|---|---|---|
| Apache + PHP-FPM | „ein Pool von PHP-Prozessen, ein sauberer Zustand für jede Anfrage“, der Name des Pools und seine beschäftigten, freien und gesamten Prozesse | OPcache und APCu sind die des Pools; ein Reload von PHP-FPM leert sie |
| Exponential Velocity | „dauerhafte Worker: ein Prozess beantwortet viele Anfragen und behält seine Klassen und Caches zwischen ihnen“, die Release (`v0.0.4.42+...`), Worker und Reserve-Worker aus `velocity.ini`, ob die Engine aus dem Archiv läuft | OPcache und APCu gehören dem Velocity-Elternprozess und seinen Workern; `max_execution_time` ist „keine Grenze“, weil Velocity die Kommandozeilen-SAPI von PHP ausführt |
| FrankenPHP | Worker-Modus oder ein Pool von Threads | wie oben, für seinen eigenen Prozess |
| Eingebauter Webserver von PHP | „ein Entwicklungsserver, nicht für den Produktivbetrieb“ | |

Das Abzeichen rechts wiederholt die PHP-Version und die SAPI (`fpm-fcgi`, `cli`, `frankenphp`, `cli-server`).

Auf dem Demonstrationsserver antwortete am 6. Oktober 2026 Apache mit PHP 8.5.11 `fpm-fcgi`, `memory_limit` 4999M, OPcache an, dessen
Statistik verborgen war; Velocity antwortete mit PHP 8.5.11 `cli`, `memory_limit` 4828M, OPcache an mit einer Trefferquote, und
`opcache.file_update_protection` 0.

## 3. Zustandsprüfungen

Jede Prüfung hat einen Zustand: **Fehler** (rot), **Warnung** (gelb), **Hinweis** (blau) oder **OK** (grün), einen
Titel und bei den ersten drei, was er bedeutet und was zu tun ist (nach dem Pfeil).

| Prüfung | Fehler oder Warnung, wenn | Was zu tun ist |
|---|---|---|
| PHP-Version | unter `setup.ini [phpversion] MinimumVersion` (Fehler); ein Zweig, dessen Sicherheitsunterstützung beendet ist (Warnung) | PHP aktualisieren |
| PHP-Erweiterungen | eine, die der Kernel braucht, fehlt: ctype, dom, iconv, json, libxml, mbstring, pcre, session, simplexml, spl, xml, zlib und der Treiber der konfigurierten Datenbank (Fehler) | installieren und aktivieren, PHP neu laden |
| Empfohlene Erweiterungen | curl, fileinfo, intl, xsl, OPcache, APCu fehlen, oder sowohl gd als auch imagick fehlen (Warnung) | installieren |
| OPcache | aus; voll; Trefferquote unter 90 % nach 5000 Zugriffen (nur Apache und FPM: Ein junger Velocity-Worker hat naturgemäß eine niedrige Quote) | `opcache.enable=1` (FPM) oder `opcache.enable_cli=1` in `velocity.ini [PHPSettings] IniOptions[]` |
| file_update_protection | unter Velocity `opcache.file_update_protection` über 0: Ein Worker cacht niemals eine Datei, die weniger als so viele Sekunden vor der Anfrage geschrieben wurde | `IniOptions[]=opcache.file_update_protection=0`, Velocity neu starten |
| memory_limit | unter 128M (Fehler), unter 256M (Warnung) | erhöhen |
| max_execution_time | unter 30 Sekunden auf einem Webserver | erhöhen |
| var-Verzeichnis | `var/`, sein Cache- oder Speicherverzeichnis nicht beschreibbar (Fehler) | dem Benutzer des Webservers Schreibzugriff geben |
| Freier Speicherplatz | unter 1 GB oder 5 % (Fehler), unter 5 GB oder 10 % (Warnung) | Platz freigeben |
| Debug-Ausgabe | `[DebugSettings] DebugOutput` für jeden Besucher an (Fehler); nur für ausgewählte Adressen oder Benutzer an (Hinweis) | `DebugByIP=enabled` mit `DebugIPList[]`, oder aus |
| Entwicklungseinstellungen | `[TemplateSettings] Debug`, `ShowUsedTemplates` oder `[DatabaseSettings] SQLOutput` an | im Produktivbetrieb deaktivieren |
| display_errors | PHP zeigt Fehler in der Seite an | `display_errors=Off`, `log_errors=On` |
| Caches | `ViewCaching`, `TemplateCompile`, `TemplateCache` oder der Override-Cache aus | im Produktivbetrieb aktivieren |
| SiteURL | leer oder ein Loopback-Name | `[SiteSettings] SiteURL` setzen |
| Cronjobs | kein Lauf gefunden, oder der letzte liegt mehr als zwei Tage zurück | `runcronjobs.php` einplanen (siehe [Cronjobs](cronjobs.md)) |
| E-Mail | `Transport=file` (Hinweis: es wird nichts versendet); SMTP ohne Server (Fehler) | `[MailSettings]` setzen |
| Datenbank | keine Verbindung (Fehler); ein anderer Zeichensatz als UTF-8 (Warnung) | `[DatabaseSettings]` prüfen; nach UTF-8 konvertieren |
| Zeitzone | weder `date.timezone` noch `[TimeZoneSettings] TimeZone` gesetzt | eine setzen |

Die Cronjob-Prüfung liest den jüngsten Eintrag aus: dem Laufverlauf von Setup > Cronjobs
(`var/<site>/cronjobs/history.json`, so gelesen, wie er ist) und der Änderungszeit einer nicht leeren
`var/log/cronjob*.log`.

## 4. Die Übersichtskarten

| Karte | Was sie zeigt |
|---|---|
| Exponential | Version und Zustand, Release-Linie, die Version, auf die die Datenbank installiert oder aktualisiert wurde (`ezsite_data`), den Build des Engine-Archivs, wenn Velocity daraus läuft, den Siteaccess, die Anzahl der Siteaccesses mit dem Standard und der Match-Reihenfolge („17 (default: site; match: uri, host)“), die Anzahl der Extensions |
| Server | wie Abschnitt 2, dazu den Port und ob HTTPS verwendet wird |
| PHP | Version (ZTS gekennzeichnet), SAPI, `memory_limit`, `max_execution_time`, Upload-Grenzen, `max_input_vars`, das Betriebssystem |
| OPcache und APCu | an oder aus; Trefferquote, Speicher und Skripte, wenn die Statistik gelesen werden kann; andernfalls, warum nicht; JIT; `validate_timestamps` und wie oft; `file_update_protection` unter Velocity; freier APCu-Speicher |
| Datenbank | Engine und Serverversion, Treiberklasse, die Datei (SQLite) oder Server und Datenbankname, Zeichensatz, Anzahl der Tabellen, Größe (SQLite immer; MySQL und PostgreSQL mit Größen messen) |
| Speicher | das var-Verzeichnis und ob es beschreibbar ist, seine Größe (auf Anfrage), freier Speicherplatz |
| Caches | View-Cache, Template-Kompilierung, Template-Cache, Override-Cache, statischer Cache, HTTP-Cache, Modus des SQL-Abfrage-Cache und unter Velocity der Antwort-Cache von Velocity |
| Cronjobs | letzter Lauf und wo er gesehen wurde |
| E-Mail | Transport; bei SMTP Server, Port, Verschlüsselung und ob er sich anmeldet; ob eine Absenderadresse gesetzt ist |
| Gebietsschema und Zeit | Gebietsschema, Inhaltssprachen, Zeitzone und wo sie gesetzt ist, Serverzeit |
| Maschine | Prozessor und Anzahl, Arbeitsspeicher, Last |

Eine Karte, unter deren Prüfungen ein Fehler oder eine Warnung ist, hat einen roten oder gelben Rand und ein ebensolches
Abzeichen.

**OPcache „Statistik nicht verfügbar“.** Plesk kann `opcache_get_status` für eine Domain in `disable_functions`
eintragen (auf dem Demonstrationsserver ist es in der Pool-Konfiguration gesetzt). Der Cache funktioniert; nur seine
Zahlen können aus PHP nicht gelesen werden. Früher sagte die Seite dort „nicht installiert“. Velocity liest sie, weil
sein PHP keine solche Einstellung hat.

## 5. Die ausführlichen Teile

Unter den Karten eingeklappt, jeder öffnet sich mit einem Klick:

- **Aktive Extensions**, in Ladereihenfolge, mit der Version, die ihre `extension.xml`, `ezinfo.php` oder
  `composer.json` angibt (Autoren, Lizenzen und Websites stehen unter `ezinfo/about`).
- **PHP-Erweiterungen und -Einstellungen**: jede geladene Erweiterung, `file_uploads`, `open_basedir` (gesetzt oder
  nicht), `max_file_uploads`, `display_errors`, die Liste `disable_functions`, die Autoload-Funktionen und der Link zu
  `phpinfo()`.
- **Adressen der Website**: die öffentliche Site, die Adresse, von der diese Seite ausgeliefert wurde, die Einstellung
  `SiteURL`.
- **Siteaccesses** (#552): jeder Siteaccess aus `site.ini [SiteAccessSettings] AvailableSiteAccessList` und
  `RelatedSiteAccessList` und wie eine Anfrage zu jedem gelangt. Siehe Abschnitt 5a.
- **OPcache und APCu**: Speicher, Skripte, Trefferquote und interned strings als Balken, und ihre Einstellungen.
- **Velocity** (nur unter Velocity): Engine und Rolle, Adresse, Prozess, Konsolenprotokoll, die Ansichten, die der
  Server selbst beantwortet, und wer sie öffnen darf, andere laufende Engines.
- **Phar-App-Engine** (unter dem eigenen Server von Velocity): ob die Engine aus dem Archiv läuft und ob es aktuell ist.
- **HTTP-Cache**, **Datenbankabfragen** (Abfrage-Cache und SQL-Profil) und **Antwort-Cache**, mit ihren Schaltflächen
  für Benutzer mit `setup/managecache`: alle Seiten verwerfen, tote Einträge entfernen und die Zähler des HTTP-Cache
  zurücksetzen; den Abfrage-Cache leeren und seine Zähler zurücksetzen; das SQL-Profil ein- und ausschalten. Das sind
  die einzigen Dinge auf der Seite, die etwas senden, und jedes Senden trägt das Formular-Token. **Setup > Caches**
  (`/setup/cache`) hat dieselben Schaltflächen in seinem Block „HTTP-Cache, Abfrage-Cache und SQL-Profil“: Beide
  Seiten senden dieselben Felder (`HttpCacheAction`, `QueryCacheAction`, `SQLProfileAction`) an denselben Code,
  `expCacheManager::sharedActionFromPost()`, der `setup/managecache` selbst prüft. Ein Senden ohne das Formular-Token
  wird abgewiesen (403).
- **Datenbankverbindung**: Typ, Server, Socket, Datenbankname, Zeichensatz, Lesereplikat.
- **Bericht für den Support**: der Text des Berichts in einem schreibgeschützten Feld.

## 5a. Siteaccesses

Welche Siteaccesses eine Installation hat und welchen eine Anfrage erhält, war früher nur in den INI-Dateien zu sehen.
Der Teil **Siteaccesses** der Seite zeigt es, eingeklappt (ein natives `<details>`: Eingabe oder Leertaste öffnet es).

- **Diese Anfrage**: der Siteaccess, den diese Seite erhalten hat, und die Regel, die ihn gewählt hat (ein Eintrag der
  Host-Zuordnung, ein URI-Element, der Standard-Siteaccess ...), der Standard-Siteaccess (`[SiteSettings]
  DefaultAccess`) und `MatchOrder`. Ein `StaticMatch` wird ebenfalls angezeigt: Er gibt jeder Anfrage einen
  Siteaccess, und die Regeln werden nicht verwendet.
- **Zuordnungstabelle**: die Regeln in der Reihenfolge von `MatchOrder`, jede mit den Einträgen, die einen Siteaccess
  nennen: das URI-Element (`URIMatchType=element`, ein Eintrag pro verfügbarem Siteaccess) oder die URI-Zuordnung
  (`URIMatchMapItems`), die Host-Zuordnung (`HostMatchMapItems`, mit der Match-Methode, wenn sie nicht `strict` ist),
  die Host- und URI-Zuordnung (`HostUriMatchMapItems`), die Ports (`[PortAccessSettings]`), die Servervariable und
  die Indexdatei; bei den Typen `text` und `regexp` das Muster. Die Regel und der Eintrag, die diese Anfrage gewählt
  haben, sind markiert; die letzte Zeile ist der Standard-Siteaccess, der verwendet wird, wenn keine Regel passt. Die
  Regeln werden von oben nach unten probiert, und der erste Treffer entscheidet.
- **Alle Siteaccesses**: je eine Karte mit Art, Design (`SiteDesign` und `AdditionalSiteDesignList`), Gebietsschema
  und Site-Sprachen, Zugriffs-Extensions, ob versteckte Knoten angezeigt werden, ob eine Anmeldung erforderlich ist,
  und den Einträgen, die zu ihm führen. Markierungen kennzeichnen den Standard, den Siteaccess dieser Seite, einen, der
  nur in `RelatedSiteAccessList` steht (er kann gelesen, aber nicht geöffnet werden), und einen, den keine Regel
  erreicht.
- **Öffnen** öffnet den Siteaccess dort, wo ein Besucher ihn finden würde, ermittelt vom Code des Admin-Links
  „Website öffnen“ (`ezpSiteAccessURL::root()`): sein Host, wenn die Host-Zuordnung ihm einen gibt, sein URI-Präfix,
  wenn er über die URI gematcht wird, und der Port dieser Seite (ein auf Velocitys :8080 geöffneter Admin öffnet die
  Sites auf :8080). **Einstellungen site.ini** öffnet `settings/view/<siteaccess>/site.ini`.

**Die Art** ist `admin`, `editor`, `login` (eine Site, die eine Anmeldung verlangt) oder `site`, ermittelt aus den
Designs und `RequireUserLogin`. Ein Siteaccess kann seine eigene Art mit der optionalen Einstellung `site.ini
[SiteAccessSettings] SiteAccessType` benennen (zum Beispiel `SiteAccessType=shop`); standardmäßig ist sie nicht gesetzt.

**Wie es gelesen wird.** Die Match-Regeln stammen aus `site.ini` ohne Siteaccess-Overrides, so wie
`eZSiteAccess::match()` sie liest, niemals aus der Anfrage. Die Einstellungen jedes Siteaccess stammen aus
`eZSiteAccess::getIni()`, das sie in ein separates `eZINI` lädt und den aktuellen Siteaccess unverändert lässt. Es
wird keine Datenbankeinstellung, kein Passwort und kein Schlüssel gelesen. Die Seite erfordert `setup/system_info`,
wie der Rest von Setup > Systeminformationen. Die Klasse ist `expSiteAccessOverview`
(`kernel/classes/expsiteaccessoverview.php`); `build()` ist rein und wird ohne Datenbank in
`tests/tests/kernel/classes/expSiteAccessOverviewTest.php` getestet.

**Übersetzungen.** Die Texte stehen in `design/admin/setup/info` und sind nach `eng-US` und `ger-DE` übersetzt. Die
fünf maschinell unterstützten Administrationssprachen (`esl-ES`, `esl-MX`, `heb-IL`, `lat-VA`, `tlh-QO`, #547)
übernehmen sie in ihrem nächsten Durchgang: Der Hauptkatalog führt sie als unfertige Meldungen, sodass die
Lückenerkennung dieses Durchgangs sie findet.

## 6. Der Bericht für eine Supportanfrage

**Bericht herunterladen (.txt)** liefert eine Textdatei wie diese (gekürzt):

```
Exponential system information
==============================
Generated: 2026-10-06T04:31:14-07:00
Answered by: Exponential Velocity (persistent workers: one process answers many requests ...)
Health: 9 ok, 0 warnings, 0 failures, 1 notes

[Server]
  Answered by              Exponential Velocity v0.0.4.42+da360b4-dirty
  Web server               QbixServer/1.5.0
  Workers                  148 configured, 12 spare
  Port                     8080 (HTTPS)

[Database]
  Engine                   sqlite 3.34.1
  File                     var/storage/sqlite3/sqlite.db
  Tables                   136
  Size                     389 MB
...
```

**Als JSON herunterladen** ergänzt jede Angabe, aus der die Karten bestehen (`facts`), und wie viele Millisekunden das
Lesen jedes Teils gedauert hat (`facts.ms`; auf dem Demonstrationsserver insgesamt etwa 10 ms). **Bericht als Text kopieren** kopiert
denselben Text in die Zwischenablage; die Schaltfläche wird nur mit JavaScript angezeigt und markiert den Text zum
Kopieren von Hand, wo die Zwischenablage nicht verfügbar ist.

## 7. Was niemals angezeigt wird

Was ein Geheimnis ist, entscheidet eine einzige Regel, `expSecretRule`, die exp:ini und `expIniEditor`, das
Audit-Protokoll, die Einstellungsseiten (`expSettingsSecretRule`) und diese Seite (`expSystemReportMask`) gemeinsam
nutzen, sodass keine von ihnen zeigt, was eine andere verbirgt. Jede behält ihren eigenen Maskentext und darf Namen
hinzufügen, niemals entfernen. Tests: `expSecretRuleTest`, `expSystemReportMaskTest`, `expSettingsPageTest`.

- Ein Wert, dessen Name ein Geheimnis bezeichnet (er enthält password, passwd, passphrase, secret, token, salt,
  credential, privatekey oder apikey, endet auf pwd oder dsn, lautet „key“ oder endet auf Key, _key oder -key; SortKey,
  KeyField und Ähnliche nicht), zeigt nur „(gesetzt, verborgen)“ oder „(nicht gesetzt)“; der Bericht ergänzt eine
  Session-ID, ein Cookie, einen Authorization- und einen Signature-Wert; der Datenbankbenutzer wird immer verborgen;
- das Passwort innerhalb einer Adresse wird abgeschnitten: `mysql://user:pw@db/site` wird zu `mysql://user:***@db/site`;
- `password=...`, `token: ...` und Ähnliches in freiem Text verlieren ihren Wert;
- eine Folge von 26 oder mehr Buchstaben und Ziffern, die eine Ziffer enthält (eine Session-ID, ein Token, ein
  Schlüssel), wird zu `***`;
- Pfade innerhalb der Installation werden relativ zu ihr angegeben (`var/site/cache`), Pfade unter einem Web- oder
  Home-Verzeichnis außerhalb von ihr behalten nur ihre letzten beiden Teile (`…/other/site`); Systempfade wie
  `/etc/vc/vc.conf` bleiben erhalten.

`/setup/info/php` ruft `phpinfo()` ohne seine Variablen- und Umgebungsteile auf, die die HTTP-Anmeldung, das
Session-Cookie und alles enthalten, was ein Server in die Umgebung legt. Unter Velocity ist es reiner Text, so wie die
Kommandozeilen-SAPI von PHP ihn schreibt.

## 8. Von der Kommandozeile

```
./console exp:system:info --allow-root-user            the text report
./console exp:system:info --json --allow-root-user     JSON
./console exp:system:info --sizes --allow-root-user    with directory and database sizes
```

`bin/php/systeminfo.php` ist derselbe Befehl. Er berichtet über das **Kommandozeilen**-PHP, das weder PHP-FPM noch
Velocity ist: OPcache ist dort meist aus, und das ist keine Warnung, und `max_execution_time` ist „keine Grenze“. Er
endet mit 1, wenn eine Prüfung fehlschlägt, und kann daher ein Deploy-Skript absichern.

## 9. Site-Daten

**Setup > Site-Daten** (`setup/sitedata`) listet die Zeilen der Tabelle `ezsite_data` auf: Name-Wert-Paare, die die
Installation und ihre Extensions in der Datenbank aufbewahren, etwa `ezpublish-version` (die Version des installierten
Datenbankschemas) und `ezpublish-release`. Die Liste ist schreibgeschützt, nach Namen sortiert, 50 Zeilen pro Seite,
mit einem Filter, der einen vollständigen Namen oder `*` für beliebige Zeichen annimmt (`ezpublish-*`). Eine Spalte
gibt an, ob Templates die Zeile lesen dürfen (siehe [Templates und Design](templates-und-design.md), „Site-Daten in
Templates“). Die Seite erfordert dieselbe Richtlinie wie diese Seite, `setup/system_info`, und steht für Inhaber
dieser Richtlinie im Setup-Menü. Die Liste und der Template-Fetch wurden aus LoveStack übernommen
(mugoweb/ezpublish-legacy, Commits `55029bd473`, `a679a13176`, `94b7b6c239`), mit Dank an Mugo Web.

## 10. Häufige Fragen

**Die Zahlen unterscheiden sich zwischen Apache und Velocity.** Das sollen sie: zwei Server, zwei PHP-Prozesse, zwei
OPcaches. Prüfen Sie das Banner, bevor Sie vergleichen.

**Die OPcache-Trefferquote unter Velocity ist niedrig.** Ein Worker, der vor wenigen Minuten gestartet ist, hat jede
Datei einmal kompiliert; die Quote steigt, während er Anfragen bedient. Die Prüfung ignoriert sie unter Velocity.

**Größen zeigen „auf Anfrage gemessen“.** Öffnen Sie Größen messen. Eine mit „≥“ markierte Größe wurde durch das
Zeitbudget von zehn Sekunden abgebrochen.
**Größen wirken veraltet.** Sie werden zehn Minuten lang aufbewahrt, gleich welcher Server sie gemessen hat; Erneut
messen misst neu.

**Die Cronjob-Prüfung warnt, obwohl Cron läuft.** Cron-Läufe, die weder nach `var/log/cronjob*.log` noch über
Setup > Cronjobs schreiben, werden nicht gesehen. Leiten Sie ihre Ausgabe nach `var/log/cronjob-<part>.log`.

## Siehe auch

- [Datenbankunterstützung](../../eng-us/help/database-support.md): was Engine und Version auf der Datenbank-Karte bedeuten, die
  unterstützten Datenbanken in ihrer offiziellen Reihenfolge (SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL)
  und die Versionen, die jede braucht.
- [Unterstützte Betriebssysteme](../../eng-us/help/os-support.md): die Betriebssysteme, Prozessoren, Webserver und PHP-SAPIs, die
  die Karten Server und PHP anzeigen können, in Stufen eingeteilt.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
