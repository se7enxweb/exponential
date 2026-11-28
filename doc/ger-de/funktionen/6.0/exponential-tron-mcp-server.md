# Exponential Tron: die MCP-Server-Schicht

Diese Seite ist für Administratoren, die einen KI-Agenten über das Model Context Protocol (MCP) in einer
Exponential-Installation arbeiten lassen wollen, und für Extension-Entwickler, die ihre eigenen Daten als MCP-Werkzeuge
anbieten wollen. Sie sagt, was Exponential Tron ist, wie es in eine Installation kommt, wie man es einschaltet, wie
Agenten sich verbinden, was sie in Schach hält und wo die vollständige Dokumentation liegt.

**Stand.** Exponential Tron ist eine öffentliche Vorschau. Seit dem 25. November 2026 verlangt die Root-`composer.json`
von Exponential das Paket `se7enxweb/tron` (heute `~0.0.0.16`), also hat jede mit Composer erstellte Installation der
Linie 6.0.15 es in `extension/tron`. Es ist **nicht standardmäßig aktiviert**: Nichts lauscht und keine Tabellen
existieren, bis ein Administrator es einschaltet. Seine Schnittstellen können sich zwischen Vorschau-Releases noch
ändern; für die Versionsnummern gilt die Regel des Projekts (nur die letzte Stelle zählt weiter: 0.0.0.9, 0.0.0.10, ...).

## Inhalt

1. [Was es ist](#was-es-ist)
2. [Wie es in eine Installation kommt](#wie-es-in-eine-installation-kommt)
3. [Einschalten](#einschalten)
4. [Einen Agenten verbinden](#einen-agenten-verbinden)
5. [Der HTTP-Endpunkt](#der-http-endpunkt)
6. [Programme, Werkzeuge und Skills](#programme-werkzeuge-und-skills)
7. [Was einen Agenten in Schach hält](#was-einen-agenten-in-schach-hält)
8. [Setup > tron in der Administration](#setup--tron-in-der-administration)
9. [Einstellungen](#einstellungen)
10. [Die Kommandozeile](#die-kommandozeile)
11. [Cluster, mehrere Sites und Hosting](#cluster-mehrere-sites-und-hosting)
12. [Kompatibilität und Grenzen](#kompatibilität-und-grenzen)
13. [Fehlersuche](#fehlersuche)
14. [Verwandte Seiten](#verwandte-seiten)

## Was es ist

MCP ist ein offenes Protokoll, über das ein KI-Client (Claude Code, Claude Desktop, Cursor, VS Code und andere)
Werkzeuge aufruft, die ein Server anbietet. Exponential Tron ist dieser Server für Exponential. Was es von einem
gewöhnlichen Connector unterscheidet:

| | Ein typischer MCP-Connector | Exponential Tron |
|---|---|---|
| Identität | ein gemeinsames Dienstkonto | jeder Aufruf läuft als echter Benutzer der Installation |
| Rechte | alle Werkzeuge für alle | die Rollen und Richtlinien der Installation entscheiden, über `eZUser::hasAccessTo`; `tools/list` zeigt nur, was der Benutzer aufrufen darf |
| Zugangsdaten | ein langlebiger Schlüssel in einer Datei | Tokens je Agent: nur als SHA-256-Hash gespeichert, einmal angezeigt, mit Ablauf, auf Programme beschränkt, in einem Schritt rotiert oder widerrufen |
| Angebot | beim Bauen festgelegt | Programme aus den aktiven Extensions; jedes Programm und Werkzeug lässt sich je Installation abschalten |
| Nachvollziehbarkeit | die Logs des Clients | jeder Aufruf mit Benutzer, Token, Ergebnis und Zeit protokolliert; ein schreibender Aufruf läuft erst, wenn sein Audit-Eintrag geschrieben ist |
| Zugänge | meist einer | stdio (auf dem Server oder über SSH; nichts lauscht) und zustandsloses Streamable HTTP unter `/tron/mcp` |

Es spricht MCP 2025-11-25 und die früheren Versionen 2025-06-18, 2025-03-26 und 2024-11-05: Werkzeuge, Ressourcen,
Ressourcen-Vorlagen, Prompts, Seitenweise Abfrage und strukturierte Inhalte.

## Wie es in eine Installation kommt

- **Composer.** Die Root-`composer.json` verlangt `se7enxweb/tron`. Das Paket hat den Typ `ezpublish-legacy-extension`
  und wird wie die anderen verlangten Extensions nach `extension/tron` installiert.
- **Die Protokollschicht.** Ab PHP 8.2 übergibt Tron JSON-RPC, die Versionsverhandlung, den Lebenszyklus und beide
  Transporte an das offizielle MCP-PHP-SDK, wenn das Paket `mcp/sdk` (Apache 2.0) und eine PSR-7-Implementierung wie
  `nyholm/psr7` installiert sind. `se7enxweb/exponential-php-floor` 1.3.14, die Linie für PHP 8.4 und neuer, bringt
  `mcp/sdk ^0.10` und `nyholm/psr7 ^1.8` mit, auf PHP 8.4 und 8.5 ist das SDK also ohne weiteren Schritt da. Auf PHP 7.4
  bis 8.1, wo das SDK nicht läuft, und überall, wo es fehlt, spricht Trons eigene, eingefrorene Protokollschicht
  dieselben Versionen. Auf PHP 8.2 und 8.3 holt `composer require mcp/sdk nyholm/psr7` das SDK.
- **`config.allow-plugins` der Root** nennt `php-http/discovery`, das Composer-Plugin des SDK; wo das SDK fehlt, ist das
  ohne Wirkung.
- **Lizenzen.** Tron steht unter GPL-2.0-or-later, das SDK unter Apache 2.0. Apache-2.0-Code lässt sich mit der GPL
  Version 3 verbinden, und Trons „or later“ lässt beide zusammen laufen.

## Einschalten

Im Wurzelverzeichnis der Installation, als der Benutzer, dem die Dateien gehören (als root hängen Sie an jeden Befehl
`--allow-root-user` an). `<sa>` ist ein Siteaccess der Administration.

1. Die Extension in `settings/override/site.ini.append.php` aktivieren:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=tron
   ```

2. Die Autoloads neu erzeugen und den INI-Cache leeren. Auf einer Site, die Velocity ausliefert, stattdessen ausrollen:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-tag=ini
   # oder, wo Velocity die Site ausliefert:
   ./console exp:velocity deploy
   ```

3. Trons Tabellen anlegen (sieben Tabellen `tron_*`; mehrfach ausführbar; `--dry-run` zeigt das SQL) und prüfen:

   ```bash
   php extension/tron/bin/php/tron.php install --siteaccess=<sa>
   php extension/tron/bin/php/tron.php status --siteaccess=<sa>      # PASS, oder Exit 1 mit dem Grund
   ```

4. **Einen Benutzer je Agent anlegen** (nie das Konto eines Menschen, nie den gemeinsamen Administrator) und ihm eine
   Rolle mit den nötigen Richtlinien geben, ohne Einschränkungen: immer `tron/use`, dann zum Beispiel
   `tron/centre_read` und `tron/centre_write`. Eine Richtlinie mit Einschränkung gilt als nicht gewährt.

5. **Ein Token erzeugen.** Das erste, Ihr eigenes, kommt aus **Setup > tron > Tokens**. Weitere erzeugt die
   Kommandozeile mit dem Token eines Benutzers, der `tron/admin` hat:

   ```bash
   php extension/tron/bin/php/tron.php token:create --siteaccess=<sa> --token-file=<Ihre Token-Datei> \
       --user=agent-claude --name="claude@laptop" --days=30 --scopes=centre \
       --out=$HOME/.config/tron/agent-claude.token
   ```

   Das Token (`tron_<prefix>_<secret>`, 256 Zufallsbits) wird mit dem Modus 0600 geschrieben und nie ausgegeben. Es
   läuft nach `--days` ab (Standard 90, höchstens 365).

## Einen Agenten verbinden

| Zugang | Wann | Identität |
|---|---|---|
| stdio auf dem Server | der Agent läuft auf dem Rechner der Installation | `--token-file=<Datei>` oder die Umgebungsvariable `TRON_TOKEN` |
| stdio über SSH | der Agent läuft anderswo und darf per SSH auf den Server | eine Token-Datei, die auf dem Server bleibt |
| HTTP | überall sonst | `Authorization: Bearer <token>` |

Claude Code, stdio auf dem Server:

```bash
claude mcp add tron -- php /pfad/zur/site/extension/tron/bin/php/tron.php serve \
    --siteaccess=<sa> --token-file=$HOME/.config/tron/agent-claude.token
```

Claude Desktop (`claude_desktop_config.json`) und Cursor (`.cursor/mcp.json`) nehmen denselben Befehl als JSON:

```json
{
  "mcpServers": {
    "tron": {
      "command": "php",
      "args": ["/pfad/zur/site/extension/tron/bin/php/tron.php", "serve", "--siteaccess=<sa>",
               "--token-file=/home/<sie>/.config/tron/agent-claude.token"]
    }
  }
}
```

Über SSH startet der Client `ssh -o BatchMode=yes <benutzer>@<server> "php .../tron.php serve ..."`; über HTTP lautet
der Befehl `claude mcp add --transport http tron https://<host>/tron/mcp --header "Authorization: Bearer $TRON_TOKEN"`.

**stdio braucht ein Token.** Ab tron 0.0.0.14 wird `serve --user=<login>` ohne Token abgelehnt, außer eine Override-Datei
setzt `[StdioSettings] AllowLoginOption=enabled`: Ohne Token könnte jeder, der den Prozess starten kann, als beliebiger
Benutzer handeln. Ab 0.0.0.15 gilt dasselbe für `tron.php call`, `token:create` und die Befehle von `tron-skills.php`,
die als Benutzer handeln.

Eine gute erste Bitte an einen Agenten: „Rufe `tron_whoami` und `tron_status` auf und sag mir, was du hier tun kannst.“
Die Antwort nennt Benutzer, Token, dessen Bereiche, den Transport und die Protokollversion.

## Der HTTP-Endpunkt

Aus, bis er geöffnet wird. Er ist zustandslos (ein POST hinein, eine JSON-Antwort heraus), also kann jeder Server eines
Clusters jede Anfrage beantworten, und ein Load Balancer braucht keine Sitzungsbindung.

```ini
# settings/override/tron.ini.append.php
[HTTPSettings]
Enabled=true
SiteAccesses[]
SiteAccesses[]=<ein Siteaccess für Besucher mit RequireUserLogin=false>
AllowedHosts[]
AllowedHosts[]=<host>
```

Dann:

- ein globaler URL-Alias `mcp` auf `tron/mcp` ergibt die kurze Adresse `/mcp`;
- hinter Apache mit PHP-FPM den Header weiterreichen: `SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1`;
- hinter dem [Request Shield](request-shield.md) die Regeln einbinden, die Tron in `doc/request-shield/tron.rules`
  mitbringt, sonst beantwortet der Shield die MCP-Anfragen mit 405.

| Status | Bedeutung |
|---|---|
| 200 | die JSON-RPC-Antwort |
| 202 | eine Benachrichtigung, angenommen |
| 400 | eine nicht unterstützte `MCP-Protocol-Version` |
| 401 | kein Token, oder ein fehlerhaftes, unbekanntes, abgelaufenes oder widerrufenes, oder ein gesperrter Benutzer |
| 403 | ein fremder `Origin`, oder ein Benutzer ohne `tron/use` |
| 404 | der Endpunkt ist für diesen Siteaccess aus |
| 405 | kein POST (Tron bietet keinen Ereignisstrom) |
| 413, 415, 421 | der Body ist zu groß, nicht `application/json`, oder der Host steht nicht in `AllowedHosts[]` |

Ein Token in einer URL wird nie angenommen.

## Programme, Werkzeuge und Skills

Was ein Agent tun kann, kommt aus **Programmen**. Jedes Programm registriert Werkzeuge, Ressourcen und Prompts unter
seinem eigenen Namenspräfix, lädt isoliert (ein defektes Programm fällt allein aus und sagt warum) und lässt sich
abschalten.

| Programm | Werkzeuge (Beispiele) | Richtlinien |
|---|---|---|
| `tron` (der Kern, immer an) | `tron_whoami`, `tron_status`; Ressourcen `tron://status`, `tron://programs` | `tron/use` |
| `centre` | Tickets, Karten und Entscheidungen: `centre_ticket_create`, `_update`, `_comment`, `_move`, `_close`, `_link`, `_list`, `_get`, `centre_board_view`, `centre_decision_review`, `centre_decision_record`; Prompts `centre_weekly_review`, `centre_triage` | `tron/centre_read`, `tron/centre_write`, `tron/centre_decide` |
| `search` | `search_query` (die Suchmaschine der Installation, mit Filtern nach Teilbaum, Klasse, Sektion und Sprache, Sortierung, Seiten und Facetten), `search_index` | `content/read`: nur was der Benutzer lesen darf, wird gefunden oder gezählt |
| weitere Programme | Inhalte, Klassen, Knoten, Rollen, INI, Templates, Layouts, Validierung, Diffs, die REST- und GraphQL-APIs, Webabrufe hinter einem SSRF-Schutz | die Richtlinien, die jedes nennt |

**Ein eigenes Programm.** Legen Sie `tron/program.json` in eine beliebige Extension und implementieren Sie
`tronProgram`:

```
extension/myext/tron/program.json          {"name": "myext", "class": "myextTronProgram", "version": "1.0.0.0", "requires": "0.0.0.1"}
extension/myext/classes/myexttronprogram.php
```

`register()` des Programms fügt Werkzeuge mit `tronTool::define()` hinzu: ein Name unter dem Präfix des Programms, eine
Beschreibung, ein JSON-Schema für die Eingabe (geprüft, bevor der Handler läuft), die Richtlinie
(`array( 'myext', 'read' )`) und ein Handler. `'write' => true` lässt Tron den Audit-Eintrag schreiben, bevor der
Handler läuft. Nach `ezpgenerateautoloads.php -e` zeigt `tron.php status` die Zeile `myext ... loaded`.

**Skills** sind geprüfte Ketten von Werkzeugaufrufen, als Daten geschrieben (ein Manifest mit typisierten Parametern,
die Einheiten, die es ausführt, deklarative Schritte dazwischen und das Schema der Antwort). Sie enthalten keinen Code
und laufen als ihr Aufrufer. Tron liefert 16 Start-Pakete (Inhaltsinventar, Klassen, Medien, SEO, Sprachen, INI und
Einstellungen, Site-Audit, Exponential Layouts, Templates, Benutzer und Rollen und andere); eine Extension kann eigene
unter `extension/<ext>/tron/skills/<paket>/` mitbringen. Ein Skill wird erst angeboten, wenn sein Harness bestanden
hat und ein Mensch ihn in **Setup > tron > Skills** freigegeben hat.

## Was einen Agenten in Schach hält

| Ebene | Schutz |
|---|---|
| Benutzer | ein Agent ist ein echter, eigener Benutzer der Installation |
| Rollen und Richtlinien | Modul `tron`: `use`, `admin`, `tokens`, `audit`, `centre_read`, `centre_write`, `centre_decide` und die Funktionen, die Programme nennen; Tron hat kein eigenes Rechtesystem |
| Tokens | nur der Hash gespeichert, Vergleich in konstanter Zeit, Ablauf, Bereiche, die nur einengen, Widerruf ab der nächsten Anfrage; ein gesperrter Benutzer stoppt alle seine Tokens |
| Transporte | HTTP aus, bis es je Siteaccess eingeschaltet wird; Origin-Liste (leer: jede Browser-Anfrage abgelehnt), Host-Liste, nur JSON, eine Größengrenze, die Prüfung der Protokollversion |
| Audit | jedes `tools/call`, `resources/read`, `prompts/get`, jede abgelehnte Anmeldung und Token-Aktion, in der Tabelle `tron_audit` und/oder der JSON-Lines-Datei `var/log/tron-audit.log`; Geheimnisse maskiert; ein Schreibaufruf wird abgelehnt, wenn sein Eintrag nicht geschrieben werden kann |
| Antworten | dem Agenten als Daten erklärt, nicht als Anweisungen; interne Fehler sagen nur „internal error“; Antworten über `ResponseMaxBytes` werden mit einem Hinweis gekürzt |

Tron speichert nie ein Token im Klartext, nimmt nie eines in einer URL an, zeigt nie ein Werkzeug, das der Benutzer
nicht aufrufen darf, führt nie einen Schreibaufruf ohne Audit-Eintrag aus und ändert nie eine Tabelle des Kernels.

## Setup > tron in der Administration

| Seite | Richtlinie | Tut |
|---|---|---|
| Dashboard | `tron/admin` | Version, Programme und ihr Zustand, Werkzeuge, aktive Tokens, die Aufrufe des letzten Tages nach Ergebnis, die Zugänge |
| Tokens | `tron/tokens` | erzeugen (einmal angezeigt), rotieren, widerrufen; Liste nach Benutzer und Zustand |
| Programme und Werkzeuge | `tron/admin` | jedes Programm und Werkzeug mit seiner Richtlinie und den Rollen, die sie gewähren, und einem Schalter für jedes |
| Audit | `tron/audit` | neueste zuerst, Filter nach Ergebnis, Transport, Programm und Benutzer, eine Live-Ansicht |
| Einstellungen | `tron/admin` | `tron.ini`, wie der Siteaccess sie liest, mit Links in den INI-Editor |

Token- und Schalteraktionen fragen zuerst „Bestätigen Sie, dass Sie es sind“. Die Seiten haben einen hellen und einen
dunklen Modus und sind ins Englische und Deutsche übersetzt.

## Einstellungen

`extension/tron/settings/tron.ini.append.php` enthält die Standardwerte; überschreiben Sie sie in
`settings/override/tron.ini.append.php`, je Siteaccess oder mit [`exp:ini`](exp-ini-befehl.md).

| Gruppe | Schlüssel | Standard | Bedeutung |
|---|---|---|---|
| `ServerSettings` | `Title` | Exponential tron | der Servertitel in `initialize` |
| | `Instructions` | leer | Text, der an die Anweisungen für den Agenten angehängt wird |
| | `PageSize` | 100 | Einträge je Listenseite |
| | `ResponseMaxBytes` | 200000 | längere Antworten werden mit einem Hinweis gekürzt |
| `ProgramSettings` | `ExtensionScan` | enabled | Programme in den aktiven Extensions suchen |
| | `Disabled[]` | keine | ausgelassene Programme (nie der Kern) |
| `AuditSettings` | `Sinks[]` | database | `database` und/oder `file` |
| | `FilePath` | `var/log/tron-audit.log` | die Datei |
| `TokenSettings` | `DefaultLifetimeDays`, `MaxLifetimeDays` | 90, 365 | Laufzeiten der Tokens |
| `StdioSettings` | `AllowLoginOption` | disabled | `enabled` (in einer Override-Datei) erlaubt `serve --user=<login>` ohne Token |
| `HTTPSettings` | `Enabled` | false | der Endpunkt antwortet mit 404, bis es `true` ist |
| | `SiteAccesses[]` | keine | die Siteaccesse, die den Endpunkt bedienen |
| | `AllowedOrigins[]` | keine | Browser-Origins; leer lehnt jede Anfrage mit `Origin` ab |
| | `AllowedHosts[]` | keine | die bedienten Hostnamen; auf einem öffentlichen Endpunkt setzen |
| | `MaxBodyBytes` | 1048576 | größere Bodies bekommen 413 |
| `CentreSettings` | `Backend` | auto | `auto`, `expcentre` oder `tron` |
| | `Lanes[]`, `Boards[]` | inbox, next, doing, waiting, done; beliebig | die Board-Aufteilung des Programms centre |

## Die Kommandozeile

```bash
php extension/tron/bin/php/tron.php <befehl> --siteaccess=<sa> [optionen]
```

| Befehl | Tut |
|---|---|
| `serve --token-file=<f>` | der stdio-MCP-Server (`TRON_TOKEN` geht auch) |
| `install [--dry-run]` | legt die fehlenden Tabellen an (mehrfach ausführbar) |
| `status` | Programme, Werkzeuge und Tabellen; Exit 1, wenn etwas nicht stimmt |
| `token:create --token-file=<aussteller> --user=<login> --name=<wofür> [--days=<n>] [--scopes=a,b] [--out=<datei>]` | ein Token, einmal angezeigt oder mit 0600 geschrieben |
| `token:list [--user=<login>]` | Tokens mit Zustand, Ablauf und letzter Nutzung, nie das Geheimnis |
| `token:revoke <id oder präfix>` | widerruft sofort |
| `audit:tail [--limit=50] [--user=<login>]` | die neuesten Audit-Einträge |
| `call <werkzeug> --token-file=<f> [--json='{...}']` | ein Werkzeugaufruf als Benutzer des Tokens, durch die volle Rechte-, Schema- und Audit-Prüfung |

Exit-Codes: 0 PASS, 1 FAIL, 2 Aufruffehler. `bin/php/tron-schema.php` gibt das SQL je Datenbank aus;
`bin/php/tron-skills.php` prüft, probiert, führt aus und gibt Skills frei.

## Cluster, mehrere Sites und Hosting

- **Mehrere Siteaccesse** einer Installation teilen sich Trons Tabellen (Tokens, Audit), wenn sie eine Datenbank teilen;
  die Einstellungen können je Siteaccess abweichen.
- **Mehrere Installationen** haben je ihr eigenes Tron; ein Agent bekommt je Installation einen Eintrag als MCP-Server
  (`tron-shop`, `tron-docs`), und der Client hält deren Werkzeuge über den Servernamen auseinander.
- **Cluster.** HTTP ist zustandslos: keine Sitzungsbindung. Installieren und aktivieren Sie Tron auf jedem
  ausliefernden Knoten, bevor Verkehr ihn erreicht (`tron.php install` einmal, auf der gemeinsamen Datenbank).
- **Hosting.** Entweder stdio über SSH in einem privaten Netz (nichts lauscht, das Token bleibt auf dem Knoten) oder
  öffentliches HTTPS mit Tokens hinter einem Load Balancer. Durch Velocity dauert eine HTTP-Anfrage etwa 120 ms
  (Median); der Aufbau des Servers etwa 10 bis 45 ms, lesende Werkzeuge 20 bis 40 ms, schreibende 50 bis 100 ms mit
  ihren Audit-Schreibvorgängen.

## Kompatibilität und Grenzen

| | |
|---|---|
| Exponential | der 6.x-Kernel; erprobt auf 6.0.15 |
| PHP | 7.4 bis 8.5, jede Version getestet |
| Datenbanken | MariaDB / MySQL, PostgreSQL und SQLite für Trons eigene Tabellen; Oracle und MongoDB sind geplant, die übrigen Datenbanken von 6.0.15 werden für Trons Tabellen noch nicht unterstützt |
| Laufzeitumgebungen | Apache mit PHP-FPM und Velocity erprobt; nginx mit PHP-FPM sollte funktionieren |
| MCP | 2025-11-25, 2025-06-18, 2025-03-26, 2024-11-05 |

Noch nicht: Anmeldung über OAuth 2.1, Tokens in Selbstbedienung, Ratenbegrenzung, Streaming mit Fortschritt und
Abbruch, Änderungsbenachrichtigungen und die Aktivierung als Standard; sie stehen ohne Termin auf Trons Roadmap.

## Fehlersuche

Beginnen Sie jede Diagnose mit `tron.php status` und `tron.php audit:tail --limit=20`.

| Symptom | Abhilfe |
|---|---|
| „Running scripts as root may be dangerous“ | `--allow-root-user` anhängen, auch im `serve`-Befehl des Clients |
| `serve` endet mit „does not hold the policy tron/use“ | der Rolle des Benutzers `tron/use` ohne Einschränkung geben |
| `serve` endet mit „missing tables“ | `tron.php install` |
| `serve --user=...` abgelehnt | ein Token verwenden (`--token-file`, `TRON_TOKEN`); `--user` braucht `[StdioSettings] AllowLoginOption=enabled` |
| HTTP 404 | `[HTTPSettings] Enabled` und `SiteAccesses[]`, oder diesem Knoten fehlt Tron |
| HTTP 405 mit einer `reference` | der Request Shield: `tron.rules` einbinden |
| HTTP 401, obwohl das Token über stdio geht | Apache verwirft `Authorization`: weiterreichen |
| Ein Werkzeug fehlt | `tron_status` nennt die ausgelassenen Werkzeuge und den Grund: Rollen, Bereiche, Schalter |
| Eine Rollenänderung wird nicht gesehen | den Client neu starten: stdio behält seine Antworten für die Dauer des Prozesses |
| Ein Schreibaufruf wird mit „audit log is unavailable“ abgelehnt | die Audit-Tabelle oder -Datei ist für diesen Prozess nicht beschreibbar |

## Verwandte Seiten

- Trons eigene Dokumentation, im Paket: `extension/tron/README.md` und `extension/tron/doc/index.md` (Schnellstart,
  Clients, Tokens, Rechte, Audit, HTTP-Transport, Hosting, Programme, Skills, Sicherheit, Aktualisieren), und
  [github.com/se7enxweb/tron](https://github.com/se7enxweb/tron) (Englisch).
- [KI-Provider, die Jobwarteschlange und MCP](../../hilfe/integratoren/ki-jobwarteschlange-und-mcp.md) (für
  Integratoren), [Eine Schnittstelle für einen Agenten wählen](../../hilfe/ki-agenten/agentische-ablaeufe/eine-schnittstelle-waehlen.md).
- [Der Request Shield](request-shield.md), [Velocity-Webserver](velocity-webserver.md),
  [Einstellungen auf der Kommandozeile ändern: exp:ini](exp-ini-befehl.md).
