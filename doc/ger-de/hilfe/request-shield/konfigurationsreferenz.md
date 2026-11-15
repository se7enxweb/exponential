---
title: "Request Shield: Konfigurationsreferenz"
audience: admin, hosting, developer
category: administration
order: 45
summary: Alles, womit sich der Request Shield (exp_request_shield) konfigurieren lässt, an einer Stelle - wo seine Konfiguration liegt, die Konstanten in config.php, requestshield.ini, der Cronjob-Teil, die Dateien auf der Platte, das Format der Regeldateien, die Reihenfolge der Prüfungen, jede Regelzeile und jeder set-Schlüssel mit Vorgabe und Wirkung, jede mitgelieferte Exponential-Regel nach ID, die expect-Zeile, die Logzeile und die signierten eigenen Anfragen.
keywords: Request Shield, exp_request_shield, Konfiguration, Referenz, Regeldatei, Regeln, Einstellungen, Vorgaben, EXP_REQUEST_SHIELD_RULES, EXP_REQUEST_SHIELD_ROOT, EXP_REQUEST_SHIELD_VAR, EXP_REQUEST_SHIELD_DISABLED, requestshield.ini, AdminSettings, Cronjob, host, trust, method, allow, restrict, block, unblock, query, limit, no-limit, challenge, challenge-exempt, exempt, deny, feed, ban, crawlers, post-origin, api-path, backend, cache-query, match, site, replace, include, expect, monitor, mode, pass-ttl, store, store-dir, log, log-level, log-ip, recheck, debug-header, EXP-PACE, EXP-IMAGES, EXP-SEARCHES, EXP-LOGIN, EXP-POST, X-Exp-Self, selfHeader, configuration, reference, settings, defaults
type: help
---
# Request Shield: Konfigurationsreferenz

Diese Seite führt alles auf, was den Request Shield konfiguriert (die Erweiterung `exp_request_shield`, die die
Bibliothek request-shield mitbringt): die wenigen Einstellungen außerhalb der Regeldateien, das Format der
Regeldateien, jede Regelzeile und jeden `set`-Schlüssel mit seiner Vorgabe und jede Regel, die die Erweiterung für
Exponential mitliefert. Sie ist zum Nachschlagen gedacht. Ausgearbeitete Beispiele zum Kopieren stehen im
[Kochbuch](kochbuch.md), die Installation in der [Anleitung](../../anleitungen/request-shield.md).

Die Tabellen folgen request-shield 0.4.0, der Version, die exp_request_shield 0.0.1.x mitbringt. Auf dem Server gibt
`php extension/exp_request_shield/bin/request-shield vocabulary` die Regelwörter der installierten Version aus.

## Inhalt

1. [Wo die Konfiguration liegt](#wo-die-konfiguration-liegt)
2. [Die Hauptregeldatei und die Konstanten](#die-hauptregeldatei-und-die-konstanten)
3. [requestshield.ini und der Cronjob-Teil](#requestshieldini-und-der-cronjob-teil)
4. [Dateien auf der Platte](#dateien-auf-der-platte)
5. [Das Format der Regeldateien](#das-format-der-regeldateien)
6. [In welcher Reihenfolge eine Anfrage geprüft wird](#in-welcher-reihenfolge-eine-anfrage-geprüft-wird)
7. [Jede Regelzeile](#jede-regelzeile)
8. [Jeder set-Schlüssel](#jeder-set-schlüssel)
9. [Die mitgelieferten Exponential-Regeln](#die-mitgelieferten-exponential-regeln)
10. [Die eingebauten Regelsätze](#die-eingebauten-regelsätze)
11. [Die expect-Zeile](#die-expect-zeile)
12. [Die Logzeile](#die-logzeile)
13. [Die eigenen Anfragen der Installation](#die-eigenen-anfragen-der-installation)
14. [Verwandte Seiten](#verwandte-seiten)

## Wo die Konfiguration liegt

Der Shield läuft, bevor der Kernel startet, und liest deshalb nie eine INI-Datei. Seine Konfiguration liegt hier:

| Wo | Was es konfiguriert | Wer es ändert |
|---|---|---|
| Die **Regeldateien** (`settings/request-shield/*.rules`) | alles, was der Shield entscheidet: Hosts, Pfade, Methoden, Budgets, die Browserprüfung, das Log, den Modus | Sie, auf dem Server; den Modus auch in der Administration |
| **Konstanten** in `config.php` | welche Regeldatei, welche Verzeichnisse, Shield ausgeschaltet | Sie, vor der Hook-Zeile |
| `var/request-shield/PAUSED` | der Shield pausiert, solange die Datei existiert | **Pausieren** / **Einschalten** in der Administration, oder `touch` / löschen |
| `settings/override/requestshield.ini.append.php` | nur die Seiten der Administration (Zeitraum der Übersicht, Seitengröße des Logs) | Sie |
| `settings/override/cronjob.ini.append.php` | wann der tägliche Cronjob-Teil `requestshield` läuft | Sie |

## Die Hauptregeldatei und die Konstanten

Der Shield liest eine **Hauptregeldatei**, die die anderen einbindet. Es ist die erste von:

| Reihenfolge | Datei | Wann |
|---|---|---|
| 1 | die Datei aus `EXP_REQUEST_SHIELD_RULES` | die Konstante ist in `config.php` vor der Hook-Zeile definiert |
| 2 | `settings/request-shield/request-shield.rules` | die Website hat eine eigene Datei (empfohlen) |
| 3 | `extension/exp_request_shield/rules/request-shield.rules` | die Vorgabe der Erweiterung, die jedes Update ersetzt |

`php extension/exp_request_shield/bin/request-shield rules` gibt die verwendete Datei aus und woher sie kommt; der
Einrichtungskasten der Übersicht sagt dasselbe.

Die Konstanten, in `config.php` **vor** der Hook-Zeile definiert, nur wenn die Vorgabe nicht passt:

| Konstante | Vorgabe | Wirkung |
|---|---|---|
| `EXP_REQUEST_SHIELD_RULES` | siehe oben | die Hauptregeldatei, ein absoluter Pfad |
| `EXP_REQUEST_SHIELD_ROOT` | zwei Ebenen über der Erweiterung | das Wurzelverzeichnis der Installation |
| `EXP_REQUEST_SHIELD_VAR` | `<root>/var/request-shield` | wo die übersetzten Einstellungen (`cache/`), `PAUSED`, die Sicherungen der Moduswechsel und der Schlüssel für eigene Anfragen (`cache/self.key`) liegen |
| `EXP_REQUEST_SHIELD_DISABLED` | nicht definiert | `true`: der Hook tut gar nichts (Velocity nach einer Änderung neu starten) |

```php
// config.php, über der Hook-Zeile: eine Regeldatei außerhalb der Installation
define( 'EXP_REQUEST_SHIELD_RULES', '/etc/exponential/www.example.org/request-shield.rules' );

if ( is_file( __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php' ) )
    require __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php';
```

## requestshield.ini und der Cronjob-Teil

`requestshield.ini` enthält nur Einstellungen der Seiten in der Administration. Überschreiben Sie sie in
`settings/override/requestshield.ini.append.php`:

| `[AdminSettings]` | Vorgabe | Wirkung |
|---|---|---|
| `DashboardHours` | `24` | die Stunden, die die Übersicht zusammenfasst und zeichnet, 1 bis 168 |
| `DashboardRecent` | `15` | die neuesten Logzeilen auf der Übersicht |
| `LogHours[]` | `1`, `6`, `24`, `72`, `168` | die Zeiträume, die die Logseite anbietet |
| `LogPageSize` | `50` | Logzeilen pro Seite |
| `ShowRuleText` | `enabled` | den Text der Regeldateien auf der Regelseite zeigen (`disabled` blendet ihn aus, etwa wenn eine Regeldatei Büroadressen nennt) |

Die Erweiterung bringt außerdem den Cronjob-Teil **`requestshield`** mit, täglich um 04:23
(`CrontabSchedule_requestshield=23 4 * * *` in ihrer `cronjob.ini.append.php`). Er führt `request-shield feeds update`
aus (die öffentlichen Sperrlisten, die die Regeln mit `feed` nennen) und `request-shield crawlers update` (die
Adresslisten, die Suchmaschinen und KI-Crawler veröffentlichen), damit Sperrlisten und Crawler-Prüfung aktuell bleiben.
Lassen Sie ihn als Benutzer der Website laufen; der Crontab-Installer des Kernels schreibt ihn in dessen Crontab. Bis
zum ersten Lauf entscheiden `feed`-Regeln nichts.

## Dateien auf der Platte

| Pfad | Was | Geschrieben von |
|---|---|---|
| `extension/exp_request_shield/lib/requestshield/` | die Bibliothek (und `php74/`, dieselben Dateien für PHP 7.4) | dem Paket |
| `extension/exp_request_shield/rules/` | die mitgelieferten Regeldateien | dem Paket, von jedem Update ersetzt |
| `settings/request-shield/` | die Hauptregeldatei der Website und ihre Kopien der `exponential*.rules` | Ihnen, oder dem ersten Moduswechsel in der Administration |
| `var/request-shield/cache/` | die übersetzten Einstellungen; `self.key`, der Schlüssel der eigenen Anfragen | dem Shield |
| `var/request-shield/store/` | Zähler (ohne APCu), das Geheimnis des Pass-Cookies, Listen, Feeds, Statistiken | dem Shield (Pfad aus `set store-dir`) |
| `var/request-shield/log/shield.log` | eine Zeile pro Entscheidung, rotiert bei `log-max-size` | dem Shield (Pfad aus `set log`) |
| `var/request-shield/backup/` | die vorige Regeldatei jedes Moduswechsels | der Administration |
| `var/request-shield/PAUSED` | pausiert den Shield, solange sie existiert | Ihnen, oder **Pausieren** |
| `var/log/requestshield.log` | jeder Schalter in der Administration, mit dem Login des Benutzers | der Administration |

Ganz `var/request-shield/` muss für den Benutzer beschreibbar sein, unter dem das PHP der Website läuft, und darf nie
vom Webserver ausgeliefert werden (ein Standard-Virtual-Host von Exponential verweigert `var/` außer den
Speicherpfaden, die er absichtlich ausliefert). Dateien legt der Shield mit `0600` an, Verzeichnisse mit `0700`,
sofern `set file-mode` und `set dir-mode` nichts anderes sagen. Rufen Sie die Kommandozeile als Benutzer der Website
auf (`sudo -u <Webbenutzer> php extension/exp_request_shield/bin/request-shield ...`), sonst kann die Website Dateien
von root nicht lesen.

## Das Format der Regeldateien

```
ids SITE                                          # die Regel-IDs dieser Datei beginnen mit SITE-
version 2026-11-15.1                              # von check und auf der Regelseite angezeigt

include exponential-admin-uri.rules               # eine weitere Datei, relativ zu dieser

[SITE-OLD] block /old/**                          # eine Regel mit ID; der Kommentar ist ihre Beschreibung
expect GET /old/page   404                        # ein Beispiel, entschieden von "request-shield test"
expect GET /oldies     answered                   # knapp daneben: muss durchgehen
```

- **Eine Regel pro Zeile**: ein Wort, dann Werte, durch Leerzeichen getrennt. `#` beginnt einen Kommentar am
  Zeilenanfang oder nach einem Leerzeichen (`\#` ist ein `#`). Der Kommentar nach einer Regel ist ihre
  **Beschreibung**, gezeigt auf den Regelseiten und wenn ein Beispiel fehlschlägt.
- **`[ID]`** vor einer Regel gibt ihr einen Namen: Buchstaben, Ziffern, `-`, `_`, `.`. Das Log, `trace` und die
  Übersicht nennen die Regel so (`rule=SITE-OLD`). Eine Regel ohne ID heißt nach Datei und Zeile
  (`request-shield.rules:12`). Eine doppelte ID ist ein Fehler. `[ID@n]` ist eine Revision; als Verweis
  (`unblock [SCAN-BACKUP@1]`) nennt sie die Revision, die Sie geprüft haben, und `check` warnt, wenn ein Update der
  Bibliothek diese Regel ändert.
- **`ids <PRÄFIX> [required]`** oben in der Datei: jede ID der Datei muss mit `<PRÄFIX>-` beginnen; `required` macht
  eine ID für jede Regel zur Pflicht.
- **`include <Datei oder Muster>`** liest weitere Dateien, relativ zur einbindenden Datei. Ein Include darf sein
  Verzeichnis **nicht** mit `..` verlassen: deshalb liegen neben der Hauptdatei der Website Kopien der
  `exponential*.rules`. `include @name` liest einen eingebauten Satz (`@wordpress`, `@tracking`, ...).
- **Pfade** sind einfache Muster, verglichen mit dem Pfad ohne Query:

  | Muster | Trifft | Trifft nicht |
  |---|---|---|
  | `/wp-admin/**` | `/wp-admin`, `/wp-admin/x/y.php` | `/wp-adminx` |
  | `/page/*` | `/page/about` (`*` bleibt in einem Segment) | `/page/a/b` |
  | `*.sql` | `/dump.sql`, `/a/b/dump.sql` (ohne führenden `/`: überall) | `/dump.sqlx` |
  | `**/admin/**` | `/admin`, `/shop/admin/x` (in jedem Verzeichnis) | `/administrator` |
  | `/file?.txt` | `/file1.txt` (`?` ist ein Zeichen) | `/file12.txt` |

  `regex` schaltet den Rest der Zeile auf reguläre Ausdrücke ohne Begrenzer um
  (`block regex (?i)^/(phpmyadmin|adminer)`); `(?i)` ignoriert Groß- und Kleinschreibung. Jedes Muster wird beim
  Lesen der Dateien übersetzt; ein kaputtes ist dann ein Fehler, nie ein stiller Fehltreffer. URI-Siteaccesses stehen
  vor dem Pfad (`/ger/content/search`): schreiben Sie `**/content/search` oder einen Ausdruck mit `(/[a-z0-9_]+)?`
  davor, wo es darauf ankommt.
- **`none`** als erster Wert leert eine Liste zuerst, Vorgaben eingeschlossen (`exempt none 192.0.2.1`).
- **`match <Pfade> { ... }`** fasst die Regeln eines Bereichs zusammen: darin gelten Regeln nur für diese Pfade, und
  ein `limit` zählt nur die Anfragen dort. `match regex ... { }` nimmt einen regulären Ausdruck.
- **`site <Namen> { ... }`** fügt Regeln für einzelne Websites hinzu (Hostnamen, `*.example.org`, `default`). Die
  Website wählt der Servername des Webservers (`set site-from server-name`, die Vorgabe), sodass ein Besucher nicht mit
  einem `Host`-Header die Regeln einer anderen Website wählen kann. **Nach dem ersten `site`-Block dürfen nur weitere
  `site`-Blöcke, `include`, `ids` und `version` folgen**, und serverweite Zeilen (`trust`, `store-dir`, `secret`,
  `ban`, `feed`, `set recheck` ...) dürfen nicht darin stehen. Verwenden Sie in einer Datei mit `site`-Blöcken absolute
  Pfade für `store-dir` und `log`.
- **Spätere Zeilen gewinnen** bei `set`-Schlüsseln: Schreiben Sie die eigenen Einstellungen der Website **nach** das
  `include` der mitgelieferten Regeln, die selbst einen Modus, einen Speicher und ein Log setzen.
- **Änderungen brauchen keinen Neustart.** Jeder Server bemerkt eine geänderte Regeldatei innerhalb von `recheck`
  Sekunden (Vorgabe 10) und übersetzt sie neu. Eine fehlerhafte Datei legt die Website nie lahm: die letzten guten
  Einstellungen bleiben in Kraft (beim allerersten Übersetzen lässt der Shield alles durch), und das Fehlerlog von PHP
  nennt einmal pro Minute Datei und Zeile.

## In welcher Reihenfolge eine Anfrage geprüft wird

`request-shield trace <main.rules> "GET https://www.example.org/pfad"` zeigt diese Schritte für eine Anfrage, jeden mit
seinem Ergebnis; die erste Abweisung beendet die Anfrage:

| # | Schritt | Regelzeilen, die darüber entscheiden |
|---|---|---|
| 1 | Ausgesperrt | `deny`, die Sperrliste |
| 2 | Öffentliche Listen | `feed` |
| 3 | Gebannt | `ban` |
| 4 | Art der Anfrage | `method` |
| 5 | Größe | `set max-uri`, `max-query-parameters`, `max-header-bytes` |
| 6 | Verschleierte Adresse | eingebaut: Kodierungen, `..`, Pfade aus dem Verzeichnis der Website heraus |
| 7 | Name der Website | `host` |
| 8 | Adressen, die nur Angreifer aufrufen | `block`, `unblock`, die eingebauten Scanner-Pfade |
| 9 | Wohin Formulare gesendet werden dürfen | `allow` |
| 10 | Woher Formulare kommen | `post-origin` |
| 11 | Bereiche für bestimmte Besucher | `restrict` |
| 12 | Bekannte Crawler | `crawlers`, `crawler` |
| 13 | Bekannte Parameter | `query` |
| 14 | Angriffsmuster | `block query`, `block header`, `@attacks` |
| 15 | Darf ein Cache die Antwort behalten? | `cache-path`, `cache-query`, `cache-ignore` |
| 16 | Tempo (eine Zeile pro Budget) | `limit`, `no-limit` |
| 17 | Browserprüfung | `challenge`, `challenge-exempt`, `api-path` |

Eine `exempt`-Adresse überspringt die Budgets und die Browserprüfung, blockierte Pfade und Angriffsmuster weisen sie
aber weiter ab. Eine Anfrage mit gültiger `X-Exp-Self`-Signatur (eine eigene Anfrage der Installation) überspringt den
Shield ganz.

## Jede Regelzeile

Die Vorgaben sind die der Bibliothek; wo die mitgelieferten Exponential-Regeln etwas anderes setzen, sagt es die
Spalte „Mitgeliefert“.

| Zeile | Vorgabe | Mitgeliefert | Wirkung |
|---|---|---|---|
| `host <Namen>` | jeder Host | nicht gesetzt: in der Datei der Website eintragen | die Hostnamen der Website; eine Anfrage für einen anderen Namen bekommt 404 |
| `trust <Adressen oder Netze>` | niemand | nicht gesetzt | wer `X-Forwarded-For`, `-Proto` und `-Host` senden darf: Load Balancer, Reverse Proxy oder CDN. Von allen anderen werden diese Header entfernt, bevor die Website läuft. `X-Forwarded-For` wird von rechts nach links gelesen, bis zur ersten Adresse, für die kein vertrauenswürdiger Proxy bürgt |
| `method <METHODEN>` | `GET HEAD POST OPTIONS` | ergänzt `PUT PATCH DELETE` (Admin-Regeln) | die überhaupt erlaubten Methoden; jede andere: 405 |
| `allow <METHODEN> <Pfade>` | überall | `EXP-POST`, `EXP-ADMIN-POST`, `EXP-ADMIN-SPA` | diese Methoden nur an diesen Pfaden, sonst 405. Mehrere `allow`-Zeilen für eine Methode addieren sich |
| `restrict <Pfade> to <Adressen oder Netze>` | offen | `SITE-DASH` (`/rs/**`) | nur diese Adressen dürfen die Pfade öffnen, andere 403. Im Beobachtungsmodus weist ein `restrict` niemanden ab |
| `block <Pfade>` | die eingebauten Scanner- und Devtools-Pfade | `EXP-SYSVIEW`, `EXP-MODULES`, `EXP-INTERNAL`, `EXP-INI`, `EXP-VIEWPARAMS` | 404, bevor die Website die Anfrage sieht |
| `unblock <Muster>` / `unblock [ID]` / `unblock @satz` | | | nimmt eine Sperre zurück: ein Muster, eine Regel, einen ganzen Satz |
| `unblock [<ID>] at <Pfade> [for <Adressen>]` | | `EXP-SYSVIEW-OK`, `EXP-DOWNLOAD`, `EXP-ADMIN-SYS` | eine Sperre nur an einigen Pfaden aufgehoben (und nur für einige Adressen) |
| `block query\|header <Name>\|headers\|anywhere <Ausdruck>` | | `@attacks` | ein Angriffsmuster in der Query oder den Headern: 403. Die Werte werden vorher dekodiert und normalisiert. Formularinhalte werden nicht angesehen |
| `query <Name> <Typ> ... [at <Pfade>]` | | `EXP-SEARCH-Q`, `EXP-ADMIN-Q` | die Query-Parameter, die die Website nimmt, mit ihren Typen: `int`, `number`, `word`, `id`, `list`, `text`, `any` oder `/Ausdruck/`; `*` für jeden Namen. Nur `text`-Werte und unbekannte Parameter werden auf Angriffe geprüft |
| `query strict` | aus | `monitor` (`EXP-STRICT`) | jeder Parameter, den keine `query`-Zeile nennt: 404 |
| `query drop [at <Pfade>]` | aus | | unbekannte Parameter werden stattdessen entfernt, die Anfrage geht ohne sie weiter (GET und HEAD) |
| `cache-path <Pfade>` | `any` | | was ein Cache behalten darf |
| `cache-query <Namen>` | `any` | `none` (`EXP-CACHE-Q`) | die Parameter, die eine zwischengespeicherte Adresse haben darf; eine Anfrage mit anderen wird beantwortet, aber nie behalten (`allow-uncached`) |
| `cache-ignore <Namen>` / `cache-ignore @tracking` | | | Parameter, die kein Cache-Schlüssel enthält, vor Cache und Website entfernt |
| `limit <Name> <n>/<Einheit> [challenge-at <n>] [on-demand] [on-exceeded challenge] [at <Pfade>]` | `requests 600/min` | `no-limit requests`; `EXP-PACE`, `EXP-IMAGES`, `EXP-SEARCHES` | ein Budget pro Besucher (eine IPv4-Adresse oder ein IPv6-/64). Einheiten `s`, `sec`, `min`, `hour`, `day`, auch `20/10s`. Über `challenge-at` die Browserprüfung; über dem Limit 429 mit `Retry-After`, oder mit `on-exceeded challenge` die Prüfung, nach der der Zähler neu beginnt. `at <Pfade>` oder ein `match`-Block: nur Anfragen dort zählen. Jedes Budget braucht einen eigenen Namen |
| `no-limit <Name>` | | `requests` | schaltet ein Budget ab, auch das vorgegebene |
| `challenge [<METHODEN>] <Pfade> [max-age <Dauer>]` | | `EXP-LOGIN`, `EXP-FORMS`, `EXP-ADMIN-CHECK` | dort immer die Browserprüfung, einmal pro Pass; `max-age 15m`: ein Pass aus den letzten 15 Minuten. Mit Methoden davor nur diese (`challenge POST **`: jedes gesendete Formular) |
| `challenge-exempt <Pfade>` | | | dort nie die Browserprüfung (Feeds, Webhooks, APIs für Programme); Budgets über ihrem Limit antworten dann mit 429 |
| `api-path <Pfade>` | | `EXP-API`, `EXP-ADMIN-BG` | eine Prüfung kommt dort als JSON (429 mit dem Header `Request-Shield-Challenge`), nie als Seite. Anfragen, die JSON verlangen oder Hintergrundabrufe einer Seite sind, bekommen ohnehin JSON |
| `post-origin same [missing check\|allow\|refuse] [except <Pfade>]` | aus | `EXP-ORIGIN`, `EXP-ORIGIN-X` | Formulare (POST, PUT, PATCH, DELETE) nur von den eigenen Seiten der Website: `Origin`, sonst `Referer`; eine andere Website 403; keiner der Header: die Browserprüfung (`missing check`, die Vorgabe) |
| `post-origin except <Pfade>` | | `EXP-ORIGIN-X` | ausgenommene Pfade (Rückrufe von Zahlungsanbietern, Webhooks) |
| `backend <Pfade>` | | `EXP-ADMIN-AREA` | der Bereich der Redakteure, in der Statistik getrennt gezählt |
| `exempt <Adressen oder Netze> [until <Tag>[T<hh:mm>]]` | `127.0.0.1 ::1` | | nie gezählt und nie geprüft; für blockierte Pfade und Angriffsmuster weiter abgewiesen |
| `deny <Adressen oder Netze> [until <Tag>[T<hh:mm>]]` | | | 403 vor jeder anderen Prüfung |
| `feed <Name> [<URL> \| from <Datei>] deny\|check\|count\|ban-signal <n> [at <Pfade>]` | | `spamhaus-drop deny`, `feodo deny` | eine öffentliche Sperrliste, vom Cronjob-Teil abgerufen; über den `site`-Blöcken |
| `ban after <n> <Signal> in <Zeit> for <Zeit>` | | `monitor` (`EXP-BAN`: 10 Abweisungen in 10 Minuten, 1 Stunde) | ein Besucher über `<n>` Signalen (`refusals`, `limits`, `checks` oder der Name eines Budgets) bekommt eine Weile nur noch 429; wiederholte Banns wachsen (`ban-growth`, `ban-max`) |
| `crawlers <Art> allow\|check\|block` | `allow` | alle vier Arten `allow` | was geprüfte Crawler bekommen, nach Art: `search`, `ai-search`, `ai-user`, `ai-training`. `allow`: nie die Browserprüfung (nur die Budgets, mit einer Pause beantwortet); `check`: wie jeder Besucher; `block`: 403 |
| `crawler <ID> allow\|check\|block` | | | dasselbe für einen Crawler (`CRAWL-GPTBOT`, ...) |
| `crawler <Art> ua /<Muster>/ [dns <Suffixe>] [ranges <Listen>]` | | | ein eigener Crawler, geprüft per DNS oder Adressliste |
| `monitor <Regel>` | | `EXP-BAN`, `EXP-STRICT` | vor `block`, `restrict`, `allow`, `limit`, `challenge`, `ban`, `feed`, `query strict`: die Regel wird protokolliert, wie sie entschiede (`monitor-...`), aber nicht durchgesetzt |
| `replace [<ID>] <Regel>` | | | tauscht eine Regel in einer Zeile aus und behält ihre ID und ihre Zählung im Log. Schreiben Sie den Ersatz vollständig: eine Regel aus einem `match`-Block braucht ihr `at <Pfade>` (oder einen eigenen `match`-Block), sonst zählt sie überall |
| `expect ...` | | etwa 150 | ein Beispiel für `request-shield test`, siehe [unten](#die-expect-zeile) |
| `match <Pfade> { ... }` / `site <Namen> { ... }` | | | siehe [das Format](#das-format-der-regeldateien) |
| `ids`, `version`, `include` | | | siehe [das Format](#das-format-der-regeldateien) |
| `dashboard-access "<Prinzipal>"\|* sha256:<Hash> [until <Tag>]` | | | wer die eigenen Seiten der Bibliothek unter `/rs` mit einem Token öffnen darf (`request-shield access-token`); in einer Datei aufbewahren, die nur der Eigentümer lesen kann |
| `stats-group "<Name>" <Websites>`, `stats-skip <Pfade>` | | | Statistik pro Kunde; Pfade, die nicht gezählt werden, wenn sie durchgehen |
| `plugin <Klasse> [from <Datei>]` | | | ein Plugin, das von jeder Entscheidung erfährt |
| `set <Schlüssel> <Wert>` | | | siehe [jeder set-Schlüssel](#jeder-set-schlüssel) |

## Jeder set-Schlüssel

`${NAME}` in einem Wert ist eine Umgebungsvariable, `${NAME:-vorgabe}` mit einer Vorgabe.

| Schlüssel | Vorgabe | Mitgeliefert | Wirkung |
|---|---|---|---|
| `mode` | `enforce` | `enforce` | `off` (nichts wird entschieden), `monitor` (alles protokolliert, wie es entschieden würde, niemand abgewiesen), `enforce`, `strict` (enforce, und die als strict markierten Regeln) |
| `recheck` | `10` (Sekunden) | | wie oft die Regeldateien auf Änderungen geprüft werden; `0`: bei jeder Anfrage |
| `store` | `auto` | | wo die Zähler liegen: `auto` (APCu, wenn nutzbar, sonst Dateien), `apcu`, `file`, `memory`. `file` unter Velocity und bei mehreren PHP-FPM-Pools, damit alle Worker einen Zähler teilen |
| `store-dir` | `.request-shield/store` neben der Hauptdatei | `var/request-shield/store` | Zähler ohne APCu, das Geheimnis, Listen, Feeds, Statistiken. Relativ zur Regeldatei |
| `secret` | in `store-dir` erzeugt | | der Schlüssel des Pass-Cookies, mindestens 32 Zeichen. Mehrere Server hinter einem Load Balancer brauchen denselben: `set secret ${SHIELD_SECRET}` |
| `pass-ttl` | `1h` | `2h` (Admin-Host: `8h`) | wie lange eine gelöste Browserprüfung gilt |
| `solution-ttl` | `5m` | | wie lange eine Lösung brauchen darf |
| `difficulty-min`, `difficulty-max` | `50000`, `500000` | | der Aufwand der Browserprüfung; verdoppelt sich pro Lösung innerhalb einer Stunde, bis zum Maximum |
| `cookie`, `solution-cookie` | `rsp`, `rss` | | die Cookie-Namen |
| `bind-user-agent` | `on` | | der Pass gilt nur für den Browser, in dem er gelöst wurde |
| `debug-header` | `off` | | `on`: jede Antwort trägt `X-RS: <Entscheidung>; rule=<ID>` (zum Testen; danach wieder ausschalten) |
| `strip-untrusted-forwarded` | `on` | | entfernt `X-Forwarded-*` aus `$_SERVER`, wenn der Absender nicht vertrauenswürdig ist |
| `search-engines` | `on` | | `off`: kein Crawler wird erkannt |
| `crawler-verify` | `both` | | `ranges` (nur die veröffentlichten Adresslisten, kein DNS während der Anfrage), `dns`, `both` |
| `dns-lookups` | `30` pro Minute | | DNS-Abfragen zur Prüfung von Crawlern, für alle Anfragen zusammen; `0`: keine |
| `app-challenge` | `off` | | `on`: die Website darf die Prüfung mit dem Antwort-Header `X-RS-Check: 1` anfordern |
| `ipv6-prefix` | `64` | | das IPv6-Präfix, unter dem ein Besucher gezählt wird |
| `max-uri`, `max-query-parameters`, `max-header-bytes` | `4096`, `64`, `16384` | | Größengrenzen; darüber wird die Anfrage abgewiesen, bevor die Website läuft |
| `language` | `auto` | | die Sprache der eigenen Seiten des Shields: die Browsersprache des Besuchers, sofern es Texte dafür gibt (Englisch und Deutsch eingebaut), oder ein Code |
| `text.<Schlüssel>`, `text.<Sprache>.<Schlüssel>` | | | eigene Texte für die Prüfseite und die Fehlerseiten (`set text.de.title Einen Moment, bitte`) |
| `home` | keine | `/` | wohin die eigenen Seiten des Shields verlinken, „Zur Startseite“ |
| `challenge-logo` | ein Schild | | eine eigene SVG-Datei in der Mitte der Prüfseite |
| `error-page` | die Seite des Shields | | eine eigene Seite für einen Status: `set error-page 404 errors/404.html`, `4xx` für alle; Platzhalter `{status}` `{title}` `{text}` `{wait}` `{home}` `{lang}` `{reference}` |
| `widget-path`, `widget-difficulty` | aus, `25000` | | die Browserprüfung im Formular, gelöst, während der Besucher tippt |
| `log` | keine | `var/request-shield/log/shield.log` | die Logdatei; ohne sie bleibt das Diagramm der Übersicht leer |
| `log-level` | `stop` | `flag` | `stop`: abgewiesen, gebremst, geprüft; `flag`: auch ungecacht durchgelassene Anfragen (unbekannte Parameter, POSTs) und gelöste Prüfungen; `all`: jede Anfrage (nur kurz); `off` |
| `log-ip` | `masked` | | `masked`: das Netz des Besuchers (IPv4 /24, IPv6 /48), nie die Adresse einer Person; `full`: die Adresse (personenbezogene Daten: in der Datenschutzerklärung nennen) |
| `log-max-size` | `10M` | | danach eine Rotation nach `shield.log.1` |
| `file-mode`, `dir-mode` | `0600`, `0700` | | die Rechte dessen, was der Shield schreibt; für eine Gruppe: `0640` / `02750` |
| `stats` | `off` | `off` | die Statistik der Bibliothek unter `/rs/stats/` (`on`, oder Teile) |
| `dashboard-path` | `/rs` | | wo die eigenen Seiten der Bibliothek liegen |
| `dashboard-session` | `8h` | | wie lange eine Token-Anmeldung an diesen Seiten gilt |
| `api`, `api-write`, `api-origins` | `on`, `off`, keine | `api off` | die JSON-API unter `/rs/api/v1` |
| `live`, `live-keep` | `off`, `1h` | | die Live-Ansicht mit vollen Adressen (APCu oder Dateien) |
| `lists-dir` | `<store-dir>/lists` | | wo `allow.rules` und `deny.rules` der Kommandozeile liegen |
| `feeds-max-age` | `3d` | | eine ältere abgerufene Sperrliste wird nicht verwendet |
| `ban-growth`, `ban-max`, `ban-keep` | `2`, `1d`, `memory` | | jeder Bann innerhalb eines Tages so viel mal länger, höchstens so lang; `file`: Banns überstehen einen Neustart von APCu |
| `site-from` | `server-name` | | welcher Name einen `site`-Block wählt: der Servername des Webservers, oder `host` (der `Host`-Header) |
| `cache-unknown-query` | `uncached` | | ein Parameter, den `cache-query` nicht nennt: `uncached` oder `hit-only` |
| `http-cache`, `http-cache-*` | `off` | | der eigene HTTP-Cache der Bibliothek; Exponential-Websites verwenden stattdessen den HTTP-Cache des Kernels |
| `stats-hours`, `stats-days`, `stats-months`, `stats-flush`, `stats-hosts`, `stats-depth`, `stats-slow`, `stats-path`, `crawler-log*` | | | Einzelheiten der Statistik |

Die eigene Referenz der Bibliothek erklärt jeden ausführlicher (auf Englisch):
[settings](https://github.com/cjw-network/request-shield/blob/main/docs/reference/settings.md).

## Die mitgelieferten Exponential-Regeln

Die vorgegebene Hauptdatei bindet `exponential-admin-uri.rules` ein, die `exponential.rules` einbindet. Jede Regel hat
eine ID; ändern Sie eine in der Hauptdatei der Website mit `replace [ID] ...`, nehmen Sie sie mit `unblock [ID]` oder
`no-limit <Name>` zurück, und bearbeiten Sie nie die Kopien, damit sie sich auffrischen lassen.

**Das Frontend** (`exponential.rules`):

| ID | Regel | Was sie tut |
|---|---|---|
| (Einstellungen) | `include @tracking @attacks @devtools`, `set pass-ttl 2h`, `set home /` | Marketing-Parameter bekannt, Angriffsmuster abgewiesen, Build-Dateien und Debug-Seiten abgewiesen |
| `EXP-SYSVIEW` | `block regex (?i)/content/view(/\|$)` | `/content/view/...`, Seiten nach Knotennummer, 404 im Frontend |
| `EXP-SYSVIEW-OK` | `unblock [EXP-SYSVIEW] at **/content/view/sitemap/* **/content/view/tagcloud/*` | Sitemap und Schlagwortwolke bleiben offen |
| `EXP-MODULES` | `block regex (?i)^(/(ger\|eng))?/(setup\|class\|section\|role\|...\|pdf)(/\|$)` | Admin-Module, 404 im Frontend; **nennen Sie Ihre URI-Siteaccesses** statt `ger\|eng` |
| `EXP-INTERNAL` | `block regex ^/(settings\|kernel\|lib\|bin\|autoload\|cronjobs\|update\|tests\|doc\|var/[^/]+/(cache\|log))(/\|$)` | die eigenen Verzeichnisse der Installation |
| `EXP-INI` | `block *.ini *.ini.append *.ini.append.php *.tpl` | Einstellungen und Template-Quellen, überall |
| `EXP-DOWNLOAD` | `unblock [SCAN-BACKUP@1] at **/content/download/** /var/*/storage/**` | hochgeladene Archive (`.zip`, `.sql` ...) bleiben herunterladbar |
| `EXP-VIEWPARAMS` | `block regex (?i)/\((offset\|limit\|year\|month\|day)\)/(?![0-9]{1,6}(/\|$))` | View-Parameter müssen Zahlen mit bis zu 6 Stellen sein |
| `EXP-POST` | `allow POST **/user/login **/content/action ... /api/**` | POST nur, wo Exponential ein Formular nimmt; **ergänzen Sie die Module Ihrer Erweiterungen** |
| `EXP-LOGIN` | `challenge **/user/login **/user/register **/user/forgotpassword **/user/password/** max-age 15m` | Anmeldung und Registrierung: zuerst die Browserprüfung |
| `EXP-FORMS` | `challenge **/content/action **/content/tipafriend/** **/comment/add/** **/newsletter/subscribe/** max-age 30m` | Kontaktformulare, Kommentare, Newsletter |
| `EXP-ORIGIN` | `post-origin same` | Formulare nur von den eigenen Seiten der Website |
| `EXP-ORIGIN-X` | `post-origin except **/paypal/notify_url/** **/xrowpaypal/notify/**` | Rückrufe von Zahlungsanbietern |
| `EXP-API` | `api-path **/ezjscore/** /api/**` | ezjscore-Aufrufe und die REST-API bekommen die Prüfung als JSON |
| `EXP-SEARCHES` | `limit searches 10/min on-exceeded challenge` (in `match regex (?i)/content/(advanced)?search(/\|$)`) | zehn Suchen pro Minute, dann die Prüfung |
| `EXP-SEARCH-Q` | `query SearchText text ... SearchDate /-1\|[1-5]/ ...` | die Parameter des Suchformulars mit ihren Typen |
| `EXP-CACHE-Q` | `cache-query none` | ein Cache behält nur Adressen ohne Query |
| `EXP-SEARCH-CHECK` | `challenge **/content/search **/content/advancedsearch max-age 1h` | auskommentiert: nehmen Sie das `#` weg, solange die Suche angegriffen wird |
| `EXP-PACE` | `limit pages 300/min challenge-at 120 on-exceeded challenge at regex ^(?!...images...)` | das Tempo: die Prüfung ab 120 Seiten pro Minute und erneut ab 300, Bildadressen ausgenommen |
| `EXP-IMAGES` | `limit images 3000/min on-exceeded challenge at regex ^(...content/image/\|/index_image.php\|...storage/images...)` | bei Bedarf erzeugte Bild-Aliase: 3000 pro Minute |
| `EXP-BAN` | `monitor ban after 10 refusals in 10m for 1h` | ein Scanner eine Stunde von der Website fern; nur beobachtet |
| `EXP-STRICT` | `monitor query strict` | unbekannte Query-Parameter: 404; nur beobachtet |

**Die Administration als `/admin`** (`exponential-admin-uri.rules`): `EXP-ADMIN-BG`
(`api-path /admin/user/megamenu/**`), die `method`-Zeile mit `PUT PATCH DELETE`, `EXP-ADMIN-SPA`
(`allow PUT PATCH DELETE /admin/explayouts_ui_api/**`, der Layout-Editor), und in `match /admin/**`: `EXP-ADMIN-SYS`
(System-URLs und Admin-Module offen), `EXP-ADMIN-POST` (`allow POST`), `EXP-ADMIN-CHECK` (`challenge`: die
Browserprüfung einmal pro Pass), `EXP-ADMIN-AREA` (`backend`), `EXP-ADMIN-Q` (`query * text`) und das auskommentierte
`EXP-ADMIN-NET` (`restrict to <Büro>`).

**Die Administration auf einem eigenen Host** (`exponential-admin-host.rules`): dasselbe als `EXP-AH-*`-Regeln in
`site admin.example.org { ... }`, mit `set pass-ttl 8h` für Redakteure.

**Die vorgegebene Hauptdatei** (`request-shield.rules`): `SITE-ADMIN-*` für die übrigen Admin-Siteaccesses
(`/adminui`, `/editor`, `/admin2` bis `/admin4l`, `/admintest_*`), `include @wordpress`, die `crawlers`-Zeilen, die
beiden `feed`-Zeilen, die Pfade unter `var/request-shield/`, `set stats off`, `SITE-DASH`
(`restrict /rs/** to 127.0.0.1 ::1`) und `set api off`.

## Die eingebauten Regelsätze

| Satz | IDs | Gelesen |
|---|---|---|
| scanners | `SCAN-HIDDEN`, `SCAN-BACKUP`, `SCAN-TEST`, `SCAN-DBTOOL`, `SCAN-CGI`, `SCAN-CONFIG`, `SCAN-SECRETS`, `SCAN-KEYS` | immer (`unblock @scanners` nimmt sie zurück) |
| devtools | `DEV-BUILD`, `DEV-APPCONFIG`, `DEV-DEBUG`, `DEV-APIDOCS`, `DEV-SOURCE` | immer |
| crawlers | `CRAWL-GOOGLE`, `CRAWL-GPTBOT`, ... | immer: die bekannten Crawler und ihre Adresslisten |
| `@wordpress` | `WP-FOLDERS`, `WP-SCRIPTS` | `include @wordpress`: `/wp-admin`, `/wp-login.php`, `xmlrpc.php` ... 404 |
| `@tracking` | `TRACK-UTM`, `TRACK-GOOGLE`, `TRACK-META`, ... | `include @tracking`: `utm_*`, `gclid`, `fbclid` ... sind bekannte Parameter |
| `@attacks` | `ATK-SQL-*`, `ATK-XSS-*`, `ATK-LFI`, `ATK-JNDI`, ... | `include @attacks`: Angriffsmuster in Query und Headern, 403 |

## Die expect-Zeile

```
expect <METHODE> <Adresse> <Ergebnis> [by <ID>] [from <Adresse>] [with pass] [times <n>] [ua "<User-Agent>"] [header <Name>:<Wert>]...
```

| Teil | Bedeutung |
|---|---|
| `<Adresse>` | ein Pfad mit Query, oder eine volle URL, wenn der Host zählt (`https://admin.example.org/...`), kodiert wie von einem Browser gesendet |
| `passes` | beantwortet, und ein Cache darf es behalten |
| `uncached` | beantwortet, nie behalten |
| `answered` | eines von beiden: die Website antwortet (für knappe Fälle, die nicht abgewiesen werden dürfen) |
| `check` | die Browserprüfung (Seite oder JSON) |
| `json-check` | die Prüfung nur als JSON |
| `403`, `404`, `405`, `429` ... | mit diesem Status abgewiesen |
| `by <ID>` | die Regel, die entscheiden muss; ohne Angabe die letzte Regel mit ID über der Zeile in derselben Datei |
| `from <Adresse>` | die Adresse des Besuchers; Vorgabe `198.51.100.7` |
| `with pass` | der Besucher hat eine gelöste Prüfung |
| `times <n>` | `n`-mal hintereinander gesendet; das Ergebnis ist das der letzten |
| `ua "..."`, `header Name:Wert` | ein User-Agent, zusätzliche Header (mehrere erlaubt) |

`php extension/exp_request_shield/bin/request-shield test <main.rules>` entscheidet jedes Beispiel mit einem frischen
Speicher im Arbeitsspeicher: nichts wird gezählt, protokolliert oder gesendet. Als `monitor` markierte Regeln und
`set mode monitor` werden **wie durchgesetzt** getestet, sodass die Beispiele vor dem Einschalten einer Regel
geschrieben werden können (`--as-written` testet sie so, wie sie sind). `--only=<ID>` testet eine Regel;
`--junit=<Datei>` schreibt einen Bericht für CI. Rückgabewert 0: alle bestanden; 1: eines schlägt fehl; 2: ein Fehler
in den Dateien.

## Die Logzeile

```
2026-11-15T10:32:48+00:00 198.51.100.0/24 challenge 429 "always" rule=EXP-LOGIN "GET https://www.example.org/user/login" "Mozilla/5.0 ..."
2026-11-15T10:34:33+00:00 203.0.113.0/24 reject 404 "blocked path" rule=EXP-MODULES "GET https://www.example.org/setup/info" "curl/8.5"
2026-11-15T10:35:02+00:00 198.51.100.0/24 allow-uncached 200 "challenge solved" "GET https://www.example.org/user/login" "Mozilla/5.0 ..."
```

Die Felder: Zeit, das Netz des Besuchers (oder die Adresse mit `log-ip full`), die Entscheidung, der Status, der
Grund, dann wahlweise `rule=`, `claimed=` (ein Crawler-Name, der nicht bestätigt wurde), `wait=` (Sekunden einer
Pause), `ref=` (die Referenz auf der Fehlerseite), die Anfrage mit voller Adresse und der User-Agent.

| Entscheidung | Bedeutung | Gruppe auf der Übersicht |
|---|---|---|
| `reject` | abgewiesen (403, 404, 405, 414 ...) | Abgewiesen |
| `throttle` | eine Pause, 429 mit `Retry-After` (ein Budget über dem Limit, ein Bann) | Abgewiesen |
| `challenge` | die Browserprüfung wurde gesendet (429 ist der Status der Prüfseite; ein echter Browser löst sie selbst) | Browserprüfung |
| `allow-uncached` | durchgelassen, aber kein Cache darf es behalten (POSTs, unbekannte Parameter, eine gelöste Prüfung) | Sonstige |
| `monitor-...` | was eine beobachtete Regel oder der Beobachtungsmodus getan hätte; der Besucher kam durch | Beobachtet (Monitor) |

## Die eigenen Anfragen der Installation

Seit exp_request_shield 0.0.1.5 tragen Anfragen, die die Installation an ihre eigenen Seiten schickt (Setup > Vorladen, die
Adressprüfung nach einem Upgrade, statisches Veröffentlichen), eine Signatur und passieren den Shield,
bevor er läuft:

- Der Header ist `X-Exp-Self: <Unix-Zeit>.<Signatur>`, die Signatur ein HMAC-SHA256 über Zeit, Methode, Host (ohne
  Port) und Pfad mit Query, erstellt mit `var/request-shield/cache/self.key` (64 Hexadezimalzeichen, nur für den
  Benutzer der Website lesbar, bei der ersten Verwendung angelegt).
- Sie gilt **120 Sekunden** und nur für die Adresse und Methode, für die sie erstellt wurde. Die Anfragen aller anderen
  werden wie immer entschieden; der Schlüssel verlässt den Server nie.
- PHP-Code der Installation erzeugt eine mit `expRequestShield::selfHeader( $url, $method )`, das die Headerzeile
  zurückgibt oder `null` (kein Schlüssel, oder eine Adresse ohne Host). Die Klasse braucht nichts vom Kernel. Siehe
  [das Kochbuch](kochbuch.md#die-anfragen-eigener-skripte-signieren).
- Der Vorwärmer des Kernels (`expPreloadRunner`) und die Prüfung nach dem Upgrade senden ihn, sobald die Erweiterung
  installiert ist (Exponential 6.0.15 ab dem 14.11.2026).

## Verwandte Seiten

- [Request Shield: Kochbuch](kochbuch.md): Rezepte mit Regeln zum Kopieren und ihren Beispielen
- [Request Shield: Fehlersuche](fehlersuche.md)
- [Request Shield: die Regeln](die-regeln.md): die Regelseite der Administration
- [Den Request Shield einrichten](../../anleitungen/request-shield.md)
- [Der Request Shield](../../funktionen/6.0/request-shield.md) (Referenz der Funktion)
- Die Referenz der Bibliothek (auf Englisch):
  [rule files](https://github.com/cjw-network/request-shield/blob/main/docs/reference/rule-files.md),
  [settings](https://github.com/cjw-network/request-shield/blob/main/docs/reference/settings.md),
  [command line](https://github.com/cjw-network/request-shield/blob/main/docs/reference/cli.md)
