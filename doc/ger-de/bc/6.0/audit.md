# Audit: Was in Exponential geschieht, aufgezeichnet

Exponential 6.0.15 zeichnet auf, was in einer Installation geschieht: wer sich angemeldet hat und bei wem das
fehlgeschlagen ist, wer Inhalte verschoben, verborgen oder entfernt hat, wer eine Rolle, eine Einstellung oder einen
Preis geändert hat, welcher Befehl oder Cronjob gelaufen ist und was das Audit selbst getan hat. Jeder Eintrag ist eine
Zeile JSON in einer Datei pro Kanal und Tag. Jede Zeile trägt den Hash der Zeile davor, sodass eine geänderte,
entfernte oder eingefügte Zeile auffällt. Das Audit ist **in jeder Installation standardmäßig eingeschaltet**. Es
kostet weniger als eine Zehntelmillisekunde auf einer Seite, die nichts aufzeichnet.

Dieses Handbuch beschreibt das fertige Subsystem. Es ist für drei Lesergruppen geschrieben:

- **Administratoren**, die das Audit in der Administrationsoberfläche lesen (Abschnitte 2 und 3);
- **Betreiber**, die es am Laufen halten: Cron, Rotation, Archive, Schlüssel, Aufbewahrung, Datenschutz,
  Fehlerbehebung (Abschnitte 4, 6 und 7);
- **Entwickler**, die eigene Ereignisse aufzeichnen oder das Audit um Sinks, Alarmregeln oder Archivformate erweitern
  (Abschnitte 3.4 und 5).

Jeder Befehl in diesem Handbuch wurde am 2026-10-02/03 auf einer Referenzinstallation (Exponential 6.0.15, SQLite,
Apache mit PHP-FPM, und Velocity) ausgeführt, und die gezeigte Ausgabe ist die echte Ausgabe. Personenbezogene und
installationsspezifische Werte wurden durch Dokumentationswerte ersetzt: Adressen durch `203.0.113.0/24`, der Hostname
durch `web1`, die Installations-ID, die Schlüssel-ID und der Fingerabdruck durch Beispielwerte und E-Mail-Adressen
durch Adressen unter `@example.com`. Aktionen, die etwas verändern (Archivieren, Löschen, Schlüsselrotation, Import),
wurden auf einer Sandbox-Kopie des Audits mit Testeinträgen ausgeführt, sodass das Live-Protokoll blieb, wie es war.
Das wird überall dort gesagt, wo es zutrifft.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neues Audit-Subsystem, **standardmäßig eingeschaltet** (in 4.x war es aus): Einträge gehen nach `var/<site>/log/audit/<channel>-<YYYY-MM-DD>.jsonl`, mit einem Index in der Datenbank der Website, der Konsole in der Administration, dem Befehl `exp:audit`, Alarmregeln, Sinks und Archiven. Neue Richtlinien `audit/read` und `audit/manage` (nur die Rolle Administrator besitzt sie). |
| Wer betroffen ist | Jede Installation, die ein Upgrade durchführt. Bestehende Installationen brauchen einmalig die Index-Tabellen. Alte Aufrufe von `eZAudit::writeAudit()` funktionieren weiter. Die tägliche Arbeit des Audits läuft aus der Cronjob-Gruppe `frequent`. |
| Wie Sie es prüfen | `php bin/php/console exp:audit verify` (jeder Kanal **intact** oder **broken**) |
| Wie Sie es beheben | Legen Sie die Index-Tabellen an: `php update/common/scripts/6.0/createaudittables.php`; stellen Sie sicher, dass die Cronjob-Gruppe `frequent` läuft; geben Sie `audit/read` den Rollen, die das Audit lesen sollen. Siehe [Schnellstart](#2-schnellstart-zwei-minuten) und [Upgrades](#4-wartungshandbuch). |

**Inhalt**

1. [Was das Audit bietet](#1-was-das-audit-bietet)
2. [Schnellstart (zwei Minuten)](#2-schnellstart-zwei-minuten)
3. [Benutzerhandbuch](#3-benutzerhandbuch): die Administrationsoberfläche · der Befehl `exp:audit` · Templates · die
   Entwickler-API
4. [Wartungshandbuch](#4-wartungshandbuch): Cron · Rotation und Archive · Aufbewahrung · Prüfen und Wiederherstellen
   · Schlüssel · der Index · Logs von 4.x importieren · Upgrades · Einstellungen ändern · Fehlerbehebung ·
   Leistungsoptimierung · Datenschutz und DSGVO-Aufgaben · wenn das Audit nicht schreiben kann
5. [Interna](#5-interna): Ereignisfluss · der Eintrag · die Hash-Kette · Taxonomie und Registries · Puffern · der
   Index · Sinks · Alarme · Velocity · Fehlerfälle · Sicherheit
6. [Referenzkonfigurationen](#6-referenzkonfigurationen): kleine Website · stark ausgelastete Website · Cluster ·
   strenger Datenschutz · Weiterleitung an ein SIEM · lange Aufbewahrung für Compliance
7. [Referenz der Einstellungen](#7-referenz-der-einstellungen)
8. [Referenz der Ereignisse](#8-referenz-der-ereignisse) (aus der Registry der Taxonomie erzeugt)
9. [Nachweise](#9-nachweise): Tests, Manipulationstest, Leistung, Berechtigungen
- [Anhang A: Designentscheidungen](#anhang-a-designentscheidungen-27-fragen-2026-10-02)
- [Anhang B: Wie es gebaut wurde, Stufe für Stufe](#anhang-b-wie-es-gebaut-wurde-stufe-für-stufe)
- [Anhang C: Bekannte Probleme](#anhang-c-bekannte-probleme-2026-10-03)

---

## 1. Was das Audit bietet

| Sie möchten wissen | Das Audit liefert Ihnen |
|---|---|
| Wer sich angemeldet hat, bei wem es fehlgeschlagen ist, aus welchem Netz | `access.session.login`, `access.session.login.failed` (mit dem Grund), `access.session.logout`, Kontosperren; die Adresse standardmäßig auf ihr /24-Netz gekürzt |
| Wer Inhalte entfernt, verschoben, verborgen oder wiederhergestellt hat | `content.node.*` und `content.object.*`. Das Entfernen eines Teilbaums ist ein übergeordnetes Ereignis mit einem untergeordneten pro Knoten. Ein Inhaltsauftrag ist das übergeordnete Ereignis von allem, was seine Stapel tun |
| Wer Rollen, Richtlinien, Benutzer, Sektionen, Zustände, Klassen geändert hat | `access.role.*`, `access.policy.*`, `access.user.*`, `content.section.*`, `content.state.*`, `content.class.*`, mit den Werten vorher und nachher |
| Wer eine Einstellung geändert, einen Cache geleert, einen Befehl oder einen Cronjob ausgeführt hat | `system.setting.write` (mit dem Diff; Geheimnisse maskiert), `system.cache.clear`, `system.command.run`, `system.cronjob.run`; der Velocity-Watchdog nur, wenn er die Engine startet, abwartet oder überspringt (`system.velocity.watchdog.*`), nie bei einer Routineprüfung |
| Wer Preise, Mehrwertsteuer, Währungen, Rabatte geändert oder eine Bestellung entfernt hat | `commerce.*`; Exporte und Importe von Daten sind `data.*` |
| Ob jemand das Protokoll manipuliert hat | `exp:audit verify` und die Konsole zeigen pro Kanal **intact** oder **broken** mit der Datei, der Zeile und der Art der Änderung |
| Benachrichtigt zu werden, wenn etwas Ernstes geschieht | Alarmregeln für Brute Force, vergebene Administratorrollen, außerhalb der Arbeitszeit geschriebene Einstellungen, Massenlöschungen, das ausgeschaltete Audit, eine gebrochene Kette. Alarme gehen an syslog/journald und an E-Mail-Empfänger Ihrer Wahl |
| Nachweise über Jahre aufzubewahren | Tägliche Dateien und komprimierte Archive (gzip, bzip2, xz, zstd, zip). Jeder archivierte Tag hat ein signiertes Manifest. Die Aufbewahrung wird pro Kanal festgelegt |
| Die Einträge herauszubekommen | Export als CSV, JSON Lines oder signiertes Bündel. Kopien gehen an syslog/journald (lokal, UDP, TCP, TLS) und an einen signierten Webhook oder an eigene Sinks |
| Ein Auskunftsersuchen einer betroffenen Person zu beantworten | `exp:audit export --subject-user=<id>` liefert jeden Eintrag von oder über diesen Benutzer, auch die pseudonymisierten |

Woraus es besteht:

- **Kanäle und Dateien.** Fünf Kanäle: `content`, `access`, `system`, `commerce` (mit `data`) und `read` für
  stichprobenartig erfasste Lesezugriffe, der standardmäßig aus ist. Jeder Kanal schreibt eine Datei pro UTC-Tag,
  `var/<site>/log/audit/<channel>-<YYYY-MM-DD>.jsonl`, und beginnt jenseits von `MaxFileSize` einen neuen Teil.
- **148 Ereignisnamen** in einer Taxonomie (`domain.subject.action[.detail]`), auf jeder Ebene schaltbar (`content.*`,
  `content.node.remove.*`). Erweiterungen fügen eigene Zweige hinzu. Siehe die
  [Referenz der Ereignisse](#8-referenz-der-ereignisse).
- **Eine Hash-Kette pro Kanal**, täglich signierte Prüfpunkte und signierte Archiv-Manifeste (HMAC-SHA-256 mit einem
  Schlüssel, der beim ersten Gebrauch erzeugt wird und `settings/override` nie verlässt).
- **Ein Index** in der eigenen Datenbank der Website (SQLite, MySQL/MariaDB, PostgreSQL, Oracle, MongoDB) für die
  Suche, die Filter und die Diagramme der Konsole. Der Index lässt sich jederzeit aus den Dateien neu aufbauen.
- **Der Tab Audit** in der Administration: ein Dashboard, eine Zeitleiste mit Filtern und Volltextsuche, ein Ereignis
  vollständig, Diagramme, Alarme, Export, Archive und Einstellungen. Außerdem gibt es einen Dashboard-Block, einen Link
  in der Seitenleiste, einen Tab Audit an jedem Knoten und Audit-Links an Inhaltsaufträgen. Jedes davon wird nur
  Benutzern angezeigt, die die Richtlinie besitzen.
- **Der Befehl `exp:audit`** mit 19 Aktionen und ein **Cronjob-Teil**, der die tägliche Arbeit erledigt.
- **Datenschutz als Standard.** Adressen werden gekürzt, Sitzungen gehasht und User-Agents verkürzt. Inhaltswerte,
  Passwörter, Tokens und Geheimnisse werden nie aufgezeichnet. Lesezugriffe sind aus. Der Index pseudonymisiert
  personenbezogene Felder nach 90 Tagen.

---

## 2. Schnellstart (zwei Minuten)

Das Audit ist bereits eingeschaltet. Prüfen Sie es, lesen Sie es und verifizieren Sie es:

```
$ ./console exp:audit status --allow-root-user
Audit:        enabled
Directory:    var/site/log/audit
Installation: 6f1c3e0a-2b7d-4c55-9a01-3d2e4f5a6b7c
Signing key:  k1-20261002-3fa94c1b  fingerprint 3FA9 4C1B 77D0 E215
Channel     Files      Bytes    Today  Chain
content         2    1495660      600  intact (1506 records)
access          2    1233762       35  intact (1575 records)
system          2   13957482       64  intact (12641 records)
commerce        1      76442        0  intact (93 records)
read            1     105484        0  intact (133 records)
(exit 0)
```

```
$ ./console exp:audit tail --channel=access --name='access.session.*' --lines=3 --allow-root-user
2026-10-03 00:00:44.254  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZH09NJPKY38DJD7G9T7GQZ  01M3ZH0A6YPVK60J8W374CDQG4
2026-10-03 00:00:49.997  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZH0F9K0MNZG6DQ0KWQ572T  01M3ZH0FTDYQ1D8BW0G3G8EC8T
2026-10-03 00:02:54.653  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZH48ZGGSYK3N7NR3Z2F3JX  01M3ZH49HXBMS8TY7HJCA7NWE0
```

```
$ ./console exp:audit verify --allow-root-user
access     INTACT    1575 records in 2 files, 2026-10-02T22:36:00.716Z to 2026-10-03T00:02:59.302Z, 1 checkpoint(s) matched
commerce   INTACT    93 records in 1 file, 2026-10-02T23:29:45.805Z to 2026-10-02T23:53:55.862Z, 1 checkpoint(s) matched
content    INTACT    1506 records in 2 files, 2026-10-02T23:28:16.227Z to 2026-10-03T00:02:34.006Z, 1 checkpoint(s) matched
read       INTACT    133 records in 1 file, 2026-10-02T23:41:59.913Z to 2026-10-02T23:48:57.088Z, 1 checkpoint(s) matched
system     INTACT    12648 records in 2 files, 2026-10-02T22:35:09.898Z to 2026-10-03T00:04:20.408Z, 3 checkpoint(s) matched
(exit 0)
```

Öffnen Sie dann in der Administrationsoberfläche als Administrator den Tab **Audit** (`/audit/dashboard`). Um einen
Eintrag erscheinen zu sehen, melden Sie sich ab und wieder an und öffnen dann **Konsole**: Die Anmeldung ist das
neueste `access.session.login`.

Drei Dinge sind in einer neuen Installation zu tun:

1. **Stellen Sie sicher, dass der Cronjob-Teil läuft.** Er wird in der Gruppe `frequent` ausgeliefert, daher genügt
   `php runcronjobs.php frequent` alle paar Minuten (siehe [4.1](#41-cron)). Das Dashboard warnt, wenn er eine Stunde
   lang nicht gelaufen ist.
2. **Entscheiden Sie, wer Alarm-E-Mails erhält.** Der Standard ist die `AdminEmail` der Website. Prüfen Sie sie mit
   `./console exp:audit alerts recipients` (siehe [4.5](#45-alarme-und-ihre-empfänger)).
3. **Sichern Sie `settings/override/audit.ini.append.php`.** Die Datei enthält die Signatur- und Pseudonym-Schlüssel.
   Behandeln Sie die Sicherung als Geheimnis. Ohne die Datei lassen sich mit ihren Schlüsseln signierte Archive nicht
   mehr prüfen.

---

## 3. Benutzerhandbuch

### 3.1 Wer was sehen darf: die Richtlinien

| Richtlinie | Gewährt | Einschränkung |
|---|---|---|
| `audit/read` | den Tab Audit, Dashboard, Konsole, Ereignis, Diagramme, Alarme, Export, letzte Ereignisse; den Dashboard-Block, den Link in der Seitenleiste, den Tab am Knoten und die Links an Aufträgen; die Fetch-Funktionen | `Channel` (content, access, system, commerce, read und jeder Kanal, den eine aktive Erweiterung hinzufügt, etwa `ai`): zum Beispiel ein Shop-Prüfer, der nur `commerce` sieht |
| `audit/manage` | die Ansichten Archive und Einstellungen, „Jetzt prüfen“ | keine |

Keine Rolle außer Administrator (die `*/*` besitzt) hat diese Richtlinien in einer neuen Installation. Um jemandem
Lesezugriff zu geben, legen Sie unter Rollen und Richtlinien eine Rolle mit `audit/read` an (optional nach Kanal
eingeschränkt) und weisen sie zu.

Ein Benutzer ohne die Richtlinie sieht **nichts** vom Audit: keinen Tab, keinen Link in der Seitenleiste, keinen Block,
keinen Tab am Knoten, keine Links an Aufträgen und einen leeren Fetch. Eine eingetippte URL wird abgewiesen und als
`access.permission.refused` aufgezeichnet. Jede erlaubte Ansicht wird selbst als `system.audit.read` aufgezeichnet.

**Erneute Passworteingabe vor Verwaltungsaktionen** (Q9; `[AuditConsoleSettings] ReauthForManage`, standardmäßig
aus). Mit `ReauthForManage=enabled` zeigt „Jetzt prüfen“ (Dashboard, Konsole, `audit/recent`) zuerst ein kleines
Formular, das nach dem Passwort des angemeldeten Benutzers fragt. Das Formular sendet an dieselbe View mit derselben
Schaltfläche und dem Formular-Token, sodass ein richtiges Passwort die Aktion sofort ausführt. Das Passwort wird gegen
den gespeicherten Hash geprüft, so wie die Anmeldung es prüft (`eZUser::authenticateHash()`), ohne erneute Anmeldung
und ohne den Benutzer der Sitzung zu ändern. Ein richtiges Passwort gilt `ReauthMinutes` (10) lang in dieser Sitzung
und nur für diesen Benutzer. Jeder Versuch wird aufgezeichnet: `access.session.reauth` (after: action, minutes) oder
`access.session.reauth.failed` (result failed, reason credentials; nie das Passwort). Abbrechen kehrt zur Seite zurück
und tut nichts. Die Kommandozeile fragt nie: Wer `exp:audit` ausführt, hat den Server bereits in der Hand. Die
Ansichten Archive und Einstellungen haben keine Verwaltungsschaltflächen (Anhang C), daher ist „Jetzt prüfen“ heute
die Aktion, die dies absichert; `expAuditReauth::gate()` ist der eine Aufruf, den eine neue Verwaltungsaktion macht:

```php
$form = expAuditReauth::gate( $Module, 'AuditVerifyNowButton', 'audit/dashboard', ezpI18n::tr( 'design/admin/audit', 'Verify now' ) );
if ( $form !== null )
    return $form;   // the password form, or the redirect of Cancel
```

Auf der Referenzinstallation über HTTP (Apache) geprüft, mit `ReauthForManage=enabled` für den Test gesetzt und der
Override-Datei danach byte-genau wiederhergestellt:

```
PASS Verify now asks for the password: 200
PASS the password form carries the form token
PASS wrong password: the form again (200)
PASS right password: the action runs (302)
PASS within ReauthMinutes: not asked again (302)
PASS both attempts are in the console
```

### 3.2 Die Administrationsoberfläche

Der Tab **Audit** (nach Design im oberen Menü) öffnet das Dashboard. Das linke Menü hat Dashboard, Konsole, Letzte
Ereignisse, Diagramme, Alarme und Export, dazu Archive und Einstellungen für `audit/manage`.

| Ansicht | URL | Was Sie sehen |
|---|---|---|
| **Dashboard** | `audit/dashboard` | Karten, die in die detaillierten Ansichten führen. *Zustand*: Audit ein/aus, die Kette jedes Kanals wie zuletzt geprüft, **Jetzt prüfen** (Verwaltung; mit erneutem Passwort, wenn `ReauthForManage` eingeschaltet ist), das Alter des Signaturschlüssels, mit einem Hinweis nach einem Jahr. *Heute und 7 Tage*: pro Kanal und Familie die Ereignisse pro Tag. *Sicherheit*: fehlgeschlagene Anmeldungen nach Adresse und nach Login (für unbekannte Konten gehasht), die letzten Rollenvergaben, abgewiesene Ansichten. *Alarme* und ihre E-Mail-Empfänger. *Aktivität*: die häufigsten Akteure und Objekte heute, die letzten Warnungen. *Betrieb*: der Cronjob-Teil (eine Warnung, wenn er eine Stunde lang nicht gelaufen ist), der Index (Zeilen, Rückstand, letzte Neuindizierung), Archive, Sinks. Schnelllinks. Es liest nie die Dateien und öffnet daher in etwa 25 ms (die Zahlen über 7 Tage werden eine Minute lang gecacht) |
| **Konsole** | `audit/console` | Die Zeitleiste, die neuesten zuerst, mit einem Filterformular, Volltextsuche und Blättern, und oben dem Zustand der Kette jedes Kanals. Noch nicht indizierte Einträge werden in die erste Seite eingemischt, sodass das neueste Ereignis immer dabei ist |
| **Ereignis** | `audit/event/<id>` | Ein Eintrag vollständig: wann (lokal und UTC), wer, die Anfrage, Objekt und Ziel mit Links in die Administration (Knoten, Objekt, Benutzer, Rolle, Auftrag), vorher und nachher nebeneinander, das übergeordnete Ereignis, die untergeordneten, die anderen Ereignisse derselben Anfrage und desselben Auftrags und die Position in der Kette (prev, hash, ob der Hash jetzt zum Eintrag passt). `/(format)/json` gibt die Zeile so zurück, wie sie in der Datei steht |
| **Diagramme** | `audit/charts` | Ereignisse pro Tag und Kanal, Verweigerungen und Fehlschläge pro Tag, Anmeldungen gegenüber fehlgeschlagenen Anmeldungen, die aktivsten Akteure, die häufigsten Ereignisnamen; die letzten 14 Tage (`/(days)/<n>`, bis 366), unter den Filtern der Konsole. In HTML/CSS gezeichnet, jedes Diagramm mit seinen Zahlen in einer Tabelle. Braucht den Index |
| **Alarme** | `audit/alerts` | Die ausgelösten Alarme (`system.audit.alert`: Regel, Gruppe, Anzahl, Zeitfenster, erstes und letztes passendes Ereignis) und die verwendeten Regeln mit etwaigen Problemen. Nur lesend |
| **Export** | `audit/export` | Der aktuelle Filter der Konsole als CSV, JSON Lines (die Zeilen, wie sie geschrieben wurden, sodass Hashes geprüft werden können) oder JSON. Die Seite sagt, wie viele Einträge der Filter hat, bevor irgendetwas beginnt. **Jetzt herunterladen**: höchstens `MaxExportRecords` Einträge (ein größerer Filter wird abgeschnitten und sagt das, Header `X-Exp-Audit-Export-Cut`), gestreamt: Die Datei beginnt sofort und wird gesendet, während sie gelesen wird, unter Apache wie unter Velocity ([Den Export streamen](#den-export-streamen)). **Im Hintergrund exportieren**: Ein Inhaltsauftrag (`auditexport`) schreibt dieselben Bytes in eine Datei, bis zu `MaxBackgroundExportRecords`, zeigt seinen Fortschritt auf `content/job/<id>` und bietet die Datei dann auf der Export-Seite an (`audit/export/(download)/<id>`, nur dem Benutzer, der sie angefordert hat). Die CSV hat die allgemeinen Spalten; gefiltert auf den Kanal der Quelle einer Erweiterung auch die Spalten, die diese Quelle deklariert (expai: Provider, Modell, Region, Zweck, Lane, Tokens ein und aus, Latenz, Status, Fallback, Kosten), und die Seite nennt sie. Aufgezeichnet als `system.audit.export` mit dem sha256 des Gesendeten (ein Hintergrundexport, wenn seine Datei vollständig ist, mit `via` background und dem Auftrag) |
| **Archive** (Verwaltung) | `audit/archives` | Pro Kanal: Live-Dateien (Anzahl, Größe, älteste), Archive und Manifeste, der Prüfzustand jeder Live-Datei (intakt, repariert, gebrochen bei Zeile n, ungeprüft), die ID und der Fingerabdruck des Signaturschlüssels. Nur lesend: Archivieren und Wiederherstellen erledigen der Cronjob-Teil und `exp:audit` |
| **Einstellungen** (Verwaltung) | `audit/settings` | Die wirksame audit.ini mit der Datei, aus der jeder Wert stammt, Geheimnisse maskiert, Schlüssel als IDs und Fingerabdrücke, die Sinks und Format-Handler mit dem, was sie am Funktionieren hindert, der Zustand des Index (Tabellen, Art der Volltextsuche, Zeilen, Rückstand). Nur lesend: Schreiben Sie Einstellungen mit `exp:ini` (siehe [4.9](#49-einstellungen-ändern)) |
| **Letzte Ereignisse** | `audit/recent` | Die letzten 100 Ereignisse, alle Kanäle oder `/(channel)/<name>`, mit einer Zeile zur Kette pro Kanal |

**Quellen von Erweiterungen.** Eine aktive Erweiterung kann eine Quelle der Konsole registrieren
(`[AuditSourceSettings] Sources[]`, siehe [3.5](#35-die-entwickler-api), „Die Quelle einer Erweiterung in der
Konsole“). Ihre Ereignisse haben dann ein Panel im Dashboard, eine Zeile mit Links in den Ansichten Konsole, Letzte
Ereignisse, Diagramme, Alarme und Export, eigene Diagramme, die Felder und Links der Erweiterung in `audit/event`,
eigene Spalten im CSV-Export ihres Kanals und einen Eintrag in diesem linken Menü. Ohne eine solche Erweiterung sind
die Ansichten genau wie oben beschrieben. Exponential Intelligence (expai) ist eine: der Kanal `ai`, das Panel
„Exponential Intelligence“ und die Menüeinträge „AI events“ und „EI: Log“ (mit `expai/log`).

**Das linke Menü folgt der Kanal-Einschränkung.** Ein Link in einen Kanal, den der Benutzer unter der Einschränkung
`Channel` von `audit/read` nicht lesen darf, wird im Menü weggelassen, statt eine Seite anzubieten, die die Konsole
abweist: ein Link, dessen Adresse eine auf einen Kanal gefilterte Audit-Ansicht ist (`audit/console/(channel)/ai`),
oder einer, den menu.ini mit `AuditChannel_<key>=<channel>` benennt. Ein Benutzer von audit/read, der auf `content`
und `access` beschränkt ist, sieht daher „AI events“ nicht. Das Template fragt
`fetch( 'audit', 'hidden_menu_links', hash( 'section', 'Leftmenu_audit' ) )`
(`expAuditSourceRegistry::hiddenMenuLinks()`) und übergibt die Schlüssel als `hidden_links` an
`design:parts/ini_menu.tpl`.

**Filter der Konsole** sind URL-Parameter, sodass sich jede gefilterte Ansicht als Lesezeichen speichern und teilen
lässt:

| Parameter | Beispiel | Bedeutung |
|---|---|---|
| `(channel)` | `(channel)/access` | ein Kanal |
| `(name)` | `(name)/access.session.*` | `*`, ein Präfix, das auf `.*` endet (es passt auch auf das Präfix selbst), oder ein ganzer Name; alles andere, etwa `access.session.login*`, wird abgewiesen: Die Seite sagt, warum, und listet nichts auf |
| `(user)`, `(login)` | `(user)/14`, `(login)/editor1` | nach dem Akteur |
| `(object)`, `(target)` | `(object)/node:275` | `<type>:<id>`: node, object, user, role, job, setting… |
| `(result)` | `(result)/refused` | success, refused, failed |
| `(severity)` | `(severity)/warning` | dieser Schweregrad oder schlimmer |
| `(request)`, `(job)`, `(run)`, `(parent)` | `(job)/20261003-000230-29a7dcc0` | Korrelation: eine Anfrage, ein Inhaltsauftrag, ein Cronjob-Lauf, die untergeordneten Ereignisse eines übergeordneten |
| `(ip)` | `(ip)/203.0.113.0/24` | wie aufgezeichnet (nach dem Datenschutz) |
| `(from)`, `(to)` | `(from)/2026-10-01/(to)/2026-10-02T12:00` | `YYYY-MM-DD`, `…THH:MM` oder `…THH:MM:SS`; ohne Offset in der Zeitzone der Website (die Konsole gibt Zeiten der Website aus), mit `Z` oder `+02:00` genau dieser Zeitpunkt; `(to)` schließt den angegebenen Tag, die Minute oder die Sekunde ein |
| `(q)` | `(q)/workout` | Volltextsuche über Namen, Objektnamen und Werte vorher/nachher |
| `(legacy_file)` | `(legacy_file)/login.log` | die Ereignisse, für die ein Audit-Dateiname von 4.x stand |
| `(offset)`, `(limit)` | `(limit)/200` | Blättern, höchstens 500 pro Seite |

Beispiel: jede fehlgeschlagene Anmeldung des letzten Tages aus einem Netz:
`/audit/console/(name)/access.session.login.failed/(ip)/203.0.113.0/24/(from)/2026-10-02`.

**An anderer Stelle in der Administration** (jeweils nur mit `audit/read` und nur für Kanäle, die der Benutzer lesen
darf):

- **Dashboard-Block** „Audit: Sicherheitsereignisse“ im Dashboard der Administration: die letzten Ereignisse der
  Kanäle access und system mit dem Schweregrad notice oder höher, offene Alarme und die Warnungen „Audit ist aus“
  oder „Kette gebrochen“ (Verwaltung).
- **Link in der Seitenleiste** „Audit-Protokoll“ im linken Menü des Dashboards.
- **Tab am Knoten** „Audit“ in jeder Knotenansicht: die letzten zehn Ereignisse zu diesem Knoten und ein Link zu
  allen (`audit/console/(object)/node:<id>`).
- **Inhaltsaufträge**: „Audit-Protokoll“ auf `content/job/<id>` und ein Link pro Auftrag auf `content/jobs`
  (`audit/console/(job)/<id>`): alles, was dieser Auftrag getan hat, Knoten für Knoten.
- **Setup-Menü**, Eintrag „Audit“ (Verwaltung).

Jede Antwort der Administration trägt den Header `X-Exp-Request-Id`. Dieselbe ID steht in `request.id` jedes
Eintrags, den diese Anfrage geschrieben hat, sodass `audit/console/(request)/<id>` genau zeigt, was ein Klick getan
hat.

### 3.3 Der Befehl `exp:audit`

`./console exp:audit <action> [options] --allow-root-user` (oder `php bin/php/audit.php`). Die Standardaktion ist
`status`. Als root ausgeführt, schreibt er Dateien, die dem Benutzer der Website gehören. Lesen wird als
`system.audit.read` aufgezeichnet und jede Änderung als eigenes Ereignis. `--json` gibt für jede lesende Aktion
maschinenlesbare Ergebnisse aus.

| Aktion | Tut | Optionen |
|---|---|---|
| `status` | Audit ein/aus, Verzeichnis, Installation, Signaturschlüssel, Dateien, Bytes, die heutigen Einträge und die Kette pro Kanal | `--json` |
| `channels` | pro Kanal: Routen, Dateien, neueste Datei, Kopf der Kette | `--json` |
| `tail` | die neuesten Einträge; `--follow` gibt weiter aus | `--channel= --name= --lines=20 --follow --json` |
| `show <id>` | ein Eintrag vollständig, sein Hash erneut geprüft | `--json` |
| `search` | der Index (oder mit `--files` die Dateien), die neuesten zuerst | Filter (siehe unten), `--files --limit=50 --json` |
| `verify` | die Hash-Ketten; Exit 0 intakt, 1 gebrochen, 2 Fehler | `--channel= --date=YYYY-MM-DD --archives --json` |
| `checkpoint` | jetzt ein signierter Prüfpunkt des Kopfes jedes Kanals | |
| `rotate` | schließt die neueste Datei von Kanälen, die seit einem früheren Tag untätig sind; führt die Aufbewahrung aus | `--channel= --dry-run` |
| `archive` | komprimiert Tage, die älter als LiveDays sind (oder `--before=`), zu signierten Archiven | `--channel= --before= --format= --dry-run` |
| `restore` | entpackt einen archivierten Tag zum Lesen | `--channel= --date= [--to=<dir>]` |
| `purge` | entfernt Archive, die älter als ArchiveDays sind (und alte Index-Zeilen) | `--channel= --dry-run` |
| `reindex` | baut den Index neu auf oder holt auf | `--incremental --archives --channel=` |
| `pseudonymise` | die Pseudonymisierung des Index jetzt | `--dry-run` |
| `export` | Einträge in eine Datei | Filter, `--subject-user=<id> --format=jsonl\|csv\|bundle --out=` |
| `import` | die Textlogs von 4.x in das neue Format | `--dir= --file= --dry-run --keep-originals` |
| `key` | `list`, `fingerprint`, `rotate`, `rotate --pseudonym` | |
| `sinks` | `list`, `test <name>`, `flush [<name>]` | |
| `alerts` | `list`, `test <rule> --replay=<date>`, `recipients [--rule=]` | |
| `cron` | ein Lauf des Cronjob-Teils | `--daily` (die täglichen Aufgaben jetzt) |

**Filter** von `search` und `export`: `--channel= --name=<pattern> --user=<id> --login= --object=<type:id>
--target=<type:id> --result=success|refused|failed --severity=<min> --request= --job= --run= --ip=<network>
--from=<time> --to=<time> --query=<text> --legacy-file=<4.x file name>`.

- `--query=<text>` ist die Volltextsuche, das `(q)` der Konsole. Es kann nicht `--q` sein: `-q`/`--quiet` ist die
  Standardoption für stille Ausgabe jedes Skripts, und der Optionsparser behält diesen Namen dafür.
- `--name` (auch für `tail`) nimmt `*`, ein Präfix aus einer bis fünf Ebenen, das auf `.*` endet (`access.*`,
  `access.session.*`; das Präfix selbst passt ebenfalls), oder einen ganzen Namen aus 3 bis 6 Ebenen. Die erste Ebene
  ist eine Domäne (content, access, system, commerce, data). Alles andere, etwa `access.session.login*`,
  `access.*.failed` oder ein bloßes `access.session`, wird mit dem Grund und dem Exit-Code 2 abgewiesen, und es wird
  nichts durchsucht.
- **Zeiten** (eine Regel für den Befehl): `--from`/`--to` nehmen `YYYY-MM-DD`, `YYYY-MM-DDTHH:MM` oder
  `YYYY-MM-DDTHH:MM:SS` (ein Leerzeichen statt des `T` funktioniert auch). Ohne Offset sind sie **UTC**, die Zone, die
  der Befehl ausgibt. Mit `Z`, `+HH:MM`, `-HH:MM`, `+HHMM` oder `+HH` sind sie genau dieser Zeitpunkt. `--to` schließt
  den ganzen angegebenen Tag, die Minute oder die Sekunde ein. Eine fehlerhafte Zeit wird abgewiesen (Exit 2). Die
  Konsole liest `(from)`/`(to)` genauso, dort ist eine bloße Zeit aber in der Zeitzone der Website, weil die Konsole
  Zeiten der Website ausgibt.

```
$ ./console exp:audit search --query=logout --name='access.session.*' --limit=2 --allow-root-user
2026-10-03 00:44:39.636  access   access.session.logout              admin(14)              user 14                            success  r-01M3ZKGQTA32V9XK0NQ4WN57R8  01M3ZKGQTM7DC5263YNBNDGTWF
2026-10-03 00:44:29.179  access   access.session.logout              admin(14)              user 14                            success  r-01M3ZKGDKG2PB5W55ZDAEZRWXA  01M3ZKGDKV0B6SA799A0KDFEBT
2 record(s) from the index (limit 2: --limit=)

$ ./console exp:audit search --name='access.session.login*' --allow-root-user; echo "exit $?"
--name: 'access.session.login*': a * may only be the whole pattern or the last rank after a dot (did you mean 'access.session.login.*'?); allowed: *, a prefix of ranks ending in .* (access.*, access.session.*) or a whole name (access.session.login)
exit 2

$ ./console exp:audit search --q=logout --allow-root-user
bin/php/audit.php: invalid option `--q'

# the same ten minutes, once in UTC (as printed) and once with the offset of Central European Summer Time
$ ./console exp:audit search --name=access.session.login --from=2026-10-03T00:30 --to=2026-10-03T00:40 --limit=3 --allow-root-user
$ ./console exp:audit search --name=access.session.login --from=2026-10-03T02:30+02:00 --to=2026-10-03T02:40+02:00 --limit=3 --allow-root-user
2026-10-03 00:33:56.882  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZJX3M2G4BG5EFY2CMR9Z3X  01M3ZJX44JDSQV7624W6SA9B0S
2026-10-03 00:33:40.684  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZJWKTDYFT34YRJSXWG6118  01M3ZJWMAC35VNXCDRSXF37ER2
2026-10-03 00:33:24.440  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZJW3XRXMD69TEGFYNYDKKX  01M3ZJW4ER0H05HBARG2SF7GPJ
3 record(s) from the index (limit 3: --limit=)            (both commands print these three records)

$ ./console exp:audit search --to=2026-10-02T24:00 --allow-root-user
--to: '2026-10-02T24:00' is not a time (YYYY-MM-DD, YYYY-MM-DDTHH:MM[:SS], optionally with Z or +HH:MM)
```

#### Lesen

```
$ ./console exp:audit channels --allow-root-user
content
  routes:  content.*
  files:   2 (1495660 bytes), newest content-2026-10-03.jsonl
  head:    seq 600 sha256:9861883047ff7fe7…
access
  routes:  access.*
  files:   2 (1233762 bytes), newest access-2026-10-03.jsonl
  head:    seq 35 sha256:086dd84595769212…
system
  routes:  system.*, (default)
  files:   2 (13959023 bytes), newest system-2026-10-03.jsonl
  head:    seq 66 sha256:7ebd8aa793b8e02d…
commerce
  routes:  commerce.*, data.*
  files:   1 (76442 bytes), newest commerce-2026-10-02.jsonl
  head:    seq 93 sha256:90805b7bb47a2979…
read
  routes:  content.node.view, content.search.*, content.object.download
  files:   1 (105484 bytes), newest read-2026-10-02.jsonl
  head:    seq 133 sha256:1c389fc0ecf5597d…
```

Die Spalten von `tail`: Zeit (UTC), Kanal, Name, Akteur (`login(user id)`), Objekt, Ergebnis, Anfrage-ID,
Ereignis-ID.

```
$ ./console exp:audit tail --lines=5 --allow-root-user
2026-10-03 00:04:02.789  system   system.cronjob.run                 anonymous(10)          cronjob audit                      success  r-01M3ZH6C0HHAVXBWZZPPSM6MHS  01M3ZH6C352JHDB4QF2NQB7QP8
2026-10-03 00:04:02.797  system   system.command.run                 anonymous(10)          command runcronjobs.php            success  r-01M3ZH6C0HHAVXBWZZPPSM6MHS  01M3ZH6C3DD30VS3A3FFQQN7P5
2026-10-03 00:04:18.285  system   system.audit.read                  anonymous(10)          audit status                       success  r-01M3ZH6V7EZCJMTDRY3FE67SQN  01M3ZH6V7DV9VTS7JW2VFEQ0XG
2026-10-03 00:04:18.307  system   system.command.run                 anonymous(10)          command bin/php/audit.php          success  r-01M3ZH6V7EZCJMTDRY3FE67SQN  01M3ZH6V83RTAYEB5Y4BBBRSBK
2026-10-03 00:04:19.019  system   system.command.run                 anonymous(10)          command bin/php/audit.php          success  r-01M3ZH6VYBCDG293S16NS72R8C  01M3ZH6VYBJARPGHC93VDZKXYX
```

`tail --follow` gibt die neuesten `--lines` aus und dann jeden neuen Eintrag, sobald er geschrieben wird (Strg-C
beendet):

```
$ ./console exp:audit tail --channel=system --lines=2 --follow --allow-root-user
2026-10-03 00:14:41.422  system   system.audit.export                anonymous(10)          audit export                       success  r-01M3ZHSVREF60990SHTRJ8M3K5  01M3ZHSVREK9D7G6M59MQG6QQE
2026-10-03 00:14:41.450  system   system.command.run                 anonymous(10)          command bin/php/audit.php          success  r-01M3ZHSVREF60990SHTRJ8M3K5  01M3ZHSVSAMKDX7PKRDFJYYYY1
```

`show` gibt einen Eintrag aus, sagt, wo er steht, und berechnet seinen Hash neu:

```
$ ./console exp:audit show 01M3ZH49HXBMS8TY7HJCA7NWE0 --allow-root-user
Event 01M3ZH49HXBMS8TY7HJCA7NWE0   access.session.login   success
  where      access-2026-10-03.jsonl line 26, seq 26
  time       2026-10-03T00:02:54.653Z
  channel    access
  severity   info
  verb       login
  depth      0
  request    {"engine":"velocity","host":"web1","id":"r-01M3ZH48ZGGSYK3N7NR3Z2F3JX","method":"POST","module":"user/login","ms":630,"pid":1322013,"siteaccess":"admin","status":200,"url":"/admin/user/login"}
  actor      {"ip":"203.0.113.0/24","login":"admin","roles":[2],"session":"h:7617a963afc3ca9c","ua":"curl 7","user_id":14}
  object     {"id":14,"login":"admin","type":"user"}
  after      {"handler":"standard"}
  prev       sha256:4c3e5dc8417119194c764488576c7c66afe914cea1eb74119465a824edb376bf
  hash       sha256:949b036fdd6f0c59e0214ca1032d56a7a514a4f6b43f8189214b3774b8cc72ba
  check      the hash matches the record
```

(Host und Adresse oben wurden für dieses Handbuch ersetzt, daher passt dieser ausgegebene Eintrag nicht mehr zu seinem
Hash. Auf der Referenzinstallation passt er.) `show <id> --json` gibt die Zeile genau so aus, wie sie in der Datei
steht.

`search` liest den Index, oder mit `--files` die Dateien:

```
$ ./console exp:audit search --name='access.session.login.*' --limit=3 --allow-root-user
2026-10-03 00:02:54.653  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZH48ZGGSYK3N7NR3Z2F3JX  01M3ZH49HXBMS8TY7HJCA7NWE0
2026-10-03 00:00:49.997  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZH0F9K0MNZG6DQ0KWQ572T  01M3ZH0FTDYQ1D8BW0G3G8EC8T
2026-10-03 00:00:44.254  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZH09NJPKY38DJD7G9T7GQZ  01M3ZH0A6YPVK60J8W374CDQG4
3 record(s) from the index (limit 3: --limit=)
```

```
$ ./console exp:audit search --result=refused --limit=5 --allow-root-user
2026-10-02 23:50:36.333  access   access.permission.refused          anonymous(10)          view visual/templatecreate         refused  r-01M3ZGDRHCS5D6G93K0DC7FSEX  01M3ZGDRHD4C1TCD02FBFGJE1Y
2026-10-02 23:50:29.393  access   access.permission.refused          anonymous(10)          view visual/templatecreate         refused  r-01M3ZGDHRGP2PEGW0TGDE652MP  01M3ZGDHRHRB0P70G8QQPQGRXN
2026-10-02 23:50:28.537  access   access.permission.refused          anonymous(10)          view visual/templatecreate         refused  r-01M3ZGDGXRFDQ0XVBP8KPNP99W  01M3ZGDGXSH26FHEQMX3ERQ0NS
2026-10-02 23:49:05.745  access   access.permission.refused          a4-test-shopauditor(13734) view audit/settings                refused  r-01M3ZGB02AVXHGJTC09J8YNYJY  01M3ZGB02HEHN5M90ANG13V99P
2026-10-02 23:49:05.602  access   access.permission.refused          a4-test-shopauditor(13734) view audit/archives                refused  r-01M3ZGAZXS74X4W4FFQPRCTD91  01M3ZGAZY2KE8MAQ36K8JSXD40
5 record(s) from the index (limit 5: --limit=)
```

Die Spur eines Inhaltsauftrags, aus den Dateien:

```
$ ./console exp:audit search --files --channel=content --name='content.job.*' --limit=4 --allow-root-user
2026-10-03 00:02:34.006  content  content.job.finish                 admin(14)              job 20261003-000230-29a7dcc0       success  r-01M3ZH3JR5MDZ2MX1N4YH1P70E  01M3ZH3NCP3RC9KMSPFNRV3T7J
2026-10-03 00:02:31.308  content  content.job.start                  admin(14)              job 20261003-000230-29a7dcc0       success  r-01M3ZH3JR5MDZ2MX1N4YH1P70E  01M3ZH3JRCYH3PWDPP095T1JFF
2026-10-03 00:02:31.301  content  content.job.create                 admin(14)              job 20261003-000230-29a7dcc0       success  r-01M3ZH3JR5MDZ2MX1N4YH1P70E  01M3ZH3JR5QQ59WJWEWP6ZPM8A
2026-10-03 00:02:20.739  content  content.job.finish                 admin(14)              job 20261003-000218-0611fb7d       success  r-01M3ZH31289HVC0HTG3W5QYKTK  01M3ZH38E3BYY485E46S7ZP4P8
4 record(s) from the files (limit 4: --limit=)
```

#### Prüfen

```
$ ./console exp:audit verify --channel=access --date=2026-10-02 --allow-root-user
access     INTACT    1540 records in 1 file, 2026-10-02T22:36:00.716Z to 2026-10-03T00:00:34.640Z, 1 checkpoint(s) matched
```

```
$ ./console exp:audit verify --channel=commerce --json --allow-root-user
{
    "commerce": {
        "channel": "commerce",
        "result": "intact",
        "records": 93,
        "files": 1,
        "first_time": "2026-10-02T23:29:45.805Z",
        "last_time": "2026-10-02T23:53:55.862Z",
        "breaks": [],
        "repairs": [],
        "notices": [],
        "checkpoints": 1,
        "last_seq": 93,
        "last_hash": "sha256:90805b7bb47a2979b4791f5ad7288fcf2783ae3e01730e0ac3859aefa42bc675"
    }
}
```

`verify --archives` liest außerdem jedes Archiv über seinen Format-Handler und prüft die HMACs der Manifeste, ihre
Kette (`previous_manifest`) und dass die älteste Live-Datei an das neueste archivierte anschließt. Auf einem Kanal mit
Archiven (Sandbox, nach `archive` unten):

```
$ ./console exp:audit verify --archives            (sandbox)
content    INTACT    archives: 3 manifests, 3 files, 21 records, 2026-06-01T09:04:00.000Z to 2026-10-03T00:04:03.819Z
access     INTACT    archives: 2 manifests, 2 files, 6 records, 2026-06-01T09:04:00.000Z to 2026-06-03T09:04:00.000Z
system     INTACT    archives: 3 manifests, 3 files, 10 records, 2026-06-01T09:04:00.000Z to 2026-10-03T00:04:03.822Z
access     INTACT    2 records in 1 file, 2026-06-03T09:04:00.000Z to 2026-06-03T09:04:00.000Z, 1 checkpoint(s) matched
content    INTACT    2 records in 1 file, 2026-10-03T00:04:03.820Z to 2026-10-03T00:04:03.819Z, 1 checkpoint(s) matched
system     INTACT    13 records in 1 file, 2026-10-03T00:04:03.822Z to 2026-10-03T00:05:22.619Z
```

Ein Kanal, dessen Dateien eine Eintrags-ID wiederholen, sagt, wie viele Einträge verschieden sind, mit einem Hinweis
`repeated_ids` pro Datei (kein Bruch: Die Wiederholungen sind verkettet wie jede andere Zeile; siehe
[Wiederholte Einträge](#wiederholte-einträge)):

```
$ ./console exp:audit verify --channel=content --allow-root-user
content    INTACT    41295 records in 9 files, 2026-10-02T23:28:16.227Z to 2026-10-12T14:26:18.609Z, 10 checkpoint(s) matched, 27652 distinct (13643 repeated)
           notice: content-2026-10-04.jsonl: repeated_ids (2322 line(s) repeat a record id written before in this file; the index and the exports keep the first)
```

Eine gebrochene Kette gibt `BROKEN` mit dem ersten Bruch aus (Datei, Zeile, Art) und endet mit Exit 1. Die Arten sind
in [5.3](#53-die-hash-kette-und-was-die-prüfung-findet) erklärt. `checkpoint` schreibt jetzt einen signierten Anker
(der Cronjob-Teil schreibt einen pro Tag):

```
$ ./console exp:audit checkpoint --allow-root-user
Checkpoint written: 01M3ZGYDH0BNG4E5NASHP69XSX (system channel)
```

#### Rotieren, archivieren, wiederherstellen, löschen

Probeläufe auf der Referenzinstallation (die älteste Live-Datei ist vom Vortag, daher ist noch nichts fällig):

```
$ ./console exp:audit rotate --dry-run --allow-root-user
content    nothing to close (today's file is the newest, or the newest is closed)
access     nothing to close (today's file is the newest, or the newest is closed)
system     nothing to close (today's file is the newest, or the newest is closed)
commerce   would close commerce-2026-10-02.jsonl (93 records)
read       would close read-2026-10-02.jsonl (133 records)

$ ./console exp:audit archive --dry-run --allow-root-user
content    nothing due
access     nothing due
system     nothing due
commerce   nothing due
read       nothing due

$ ./console exp:audit purge --dry-run --allow-root-user
content    would remove 0 archived day(s) before 2024-10-03
access     would remove 0 archived day(s) before 2024-10-03
system     would remove 0 archived day(s) before 2024-10-03
commerce   would remove 0 archived day(s) before 2024-10-03
read       would remove 0 archived day(s) before 2026-07-05
```

In der Sandbox, die drei Tage aus dem Juni hat (älter als LiveDays=90):

```
$ ./console exp:audit archive --dry-run            (sandbox)
content    2026-06-01 would be archived with gzip: content-2026-06-01.jsonl
content    2026-06-02 would be archived with gzip: content-2026-06-02.jsonl
content    2026-06-03 would be archived with gzip: content-2026-06-03.jsonl
access     2026-06-01 would be archived with gzip: access-2026-06-01.jsonl
access     2026-06-02 would be archived with gzip: access-2026-06-02.jsonl
system     2026-06-01 would be archived with gzip: system-2026-06-01.jsonl
system     2026-06-02 would be archived with gzip: system-2026-06-02.jsonl
system     2026-06-03 would be archived with gzip: system-2026-06-03.jsonl
commerce   nothing due
read       nothing due

$ ./console exp:audit archive                      (sandbox)
content    2026-06-01 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/content/2026/content-2026-06-01.manifest.json
content    2026-06-02 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/content/2026/content-2026-06-02.manifest.json
content    2026-06-03 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/content/2026/content-2026-06-03.manifest.json
access     2026-06-01 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/access/2026/access-2026-06-01.manifest.json
access     2026-06-02 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/access/2026/access-2026-06-02.manifest.json
system     2026-06-01 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/system/2026/system-2026-06-01.manifest.json
system     2026-06-02 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/system/2026/system-2026-06-02.manifest.json
system     2026-06-03 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/system/2026/system-2026-06-03.manifest.json
commerce   nothing due
read       nothing due
```

`access-2026-06-03.jsonl` wurde nicht archiviert: Sie war noch die neueste Datei ihres Kanals. Die neueste Datei eines
Kanals wird nie archiviert, bevor sie geschlossen ist, entweder durch das erste Schreiben eines späteren Tages oder
durch `rotate`:

```
$ ./console exp:audit rotate --dry-run             (sandbox)
content    nothing to close (today's file is the newest, or the newest is closed)
access     would close access-2026-06-03.jsonl (2 records)
…
```

```
$ ./console exp:audit restore --channel=content --date=2026-06-02      (sandbox)
var/tmp/audit-stage6/sandbox/log/restored/content-2026-06-02.jsonl  (identical to the archived live file)
```

Wiederhergestellte Dateien kommen zum Lesen, Prüfen und Durchsuchen (`search --files`) nach `<LogDir>/restored/`. Sie
kehren nie in die Live-Kette zurück.

```
$ ./console exp:audit purge --dry-run              (sandbox, ArchiveDays=100)
content    would remove 3 archived day(s) before 2026-06-25: content-2026-06-01.manifest.json, content-2026-06-02.manifest.json, content-2026-06-03.manifest.json
access     would remove 2 archived day(s) before 2026-06-25: access-2026-06-01.manifest.json, access-2026-06-02.manifest.json
system     would remove 3 archived day(s) before 2026-06-25: system-2026-06-01.manifest.json, system-2026-06-02.manifest.json, system-2026-06-03.manifest.json
commerce   would remove 0 archived day(s) before 2024-10-03
read       would remove 0 archived day(s) before 2026-07-05

$ ./console exp:audit purge                        (sandbox, ArchiveDays=100)
content    removed 3 archived day(s) before 2026-06-25: content-2026-06-01.manifest.json, content-2026-06-02.manifest.json, content-2026-06-03.manifest.json
…
```

Jedes Entfernen wird in `<ArchiveDir>/<channel>/purged.jsonl` geschrieben und als `system.audit.purge` mit den
Dateinamen und ihrem sha256 aufgezeichnet, sodass der Nachweis dessen, was existierte, die Daten überdauert.
Live-Dateien, die an einen gelöschten Tag anschlossen, werden weiterhin als INTACT geprüft.

#### Der Index

```
$ ./console exp:audit reindex --incremental --allow-root-user
Index: 43 rows in 64 ms

$ ./console exp:audit pseudonymise --dry-run --allow-root-user
Pseudonymised: {"ok":true,"rows":0,"error":"","cutoff":"2026-07-05T00:04:33Z"}
```

`reindex` ohne `--incremental` leert die Index-Tabellen (mit `--channel=` einen Kanal) und baut sie aus den Dateien
neu auf. Auf der Referenzinstallation dauerte das für 13 000 Einträge etwa 6 Sekunden.

#### Exportieren

```
$ ./console exp:audit export --name='access.*' --from=2026-10-02 --format=csv --out=var/tmp/audit-stage6/access.csv --allow-root-user
1573 record(s) exported as csv to var/tmp/audit-stage6/access.csv (sha256 86b22d7342bd43acf46a6668ba40518c7408d1cf5ca663304916b3d9916c34e7)

$ ./console exp:audit export --channel=content --format=jsonl --limit=50 --out=var/tmp/audit-stage6/content.jsonl --allow-root-user
50 record(s) exported as jsonl to var/tmp/audit-stage6/content.jsonl (sha256 53b2145e194588e610470375ed8173642149f5847cd19b999dd814aaa8dfb369)

$ ./console exp:audit export --name='access.*' --format=bundle --out=var/tmp/audit-stage6/bundle --allow-root-user
1573 record(s) exported as bundle to var/tmp/audit-stage6/bundle/records.jsonl (sha256 a87a91ae085f6130d349ada65eb7c32f40779b0c071b77be784b79f576aad2e1), signed manifest var/tmp/audit-stage6/bundle/manifest.json
```

Ein **Bündel** besteht aus `records.jsonl` (den Zeilen, wie sie geschrieben wurden) und `manifest.json`, das den
Filter, die Anzahl, das sha256 und einen HMAC mit dem Signaturschlüssel enthält. Das übergeben Sie einem Prüfer: Die
Hashes der Zeilen lassen sich gegeneinander prüfen und das Manifest gegen den Fingerabdruck des Schlüssels.

#### Die Logs von 4.x importieren

```
$ ./console exp:audit import --dry-run --allow-root-user
var/site/log/audit/login.log             skipped: imported before (same sha256)
```

In einem Sandbox-Verzeichnis mit zwei Dateien aus 4.x:

```
$ ./console exp:audit import --dir=…/old --dry-run          (sandbox)
…/old/login.log 1 entries, 1 record(s) would be imported
…/old/failed_login.log 2 entries, 2 record(s) would be imported

$ ./console exp:audit import --dir=…/old --keep-originals   (sandbox)
…/old/login.log 1 entries, 1 record(s) imported
…/old/failed_login.log 2 entries, 2 record(s) imported
Imported into var/tmp/audit-stage6/sandbox/log/imported (outside the chain, marked imported); originals archived: var/tmp/audit-stage6/sandbox/log/archive/legacy/legacy-20261003-000544-gescfc.manifest.json

$ ./console exp:audit import --dir=…/old --keep-originals   (sandbox, again)
…/old/login.log skipped: imported before (same sha256)
…/old/failed_login.log skipped: imported before (same sha256)
```

Ein importierter Eintrag (eine Zeile von `imported/access-2026-09-30.jsonl`):

```json
{"actor":{"ip":"203.0.113.0/24","login":"editor1","user_id":14},"after":{"legacy":{"User ID":"14"}},"channel":"access","id":"01M3SE0M1G2JED4MV2DQSF32BA","imported":true,"name":"access.session.login","object":{"login":"editor1","type":"user"},"request":{"siteaccess":"admin","url":"/user/login"},"result":"success","severity":"info","source":{"file":"login.log","line":1,"sha256":"7e7a8b9cf24e21b2e1a8658ff2943f9e63aeabb11914eca9224be9643939e622"},"time":"2026-09-30T15:13:02.000Z","v":1,"verb":"login","x":{"legacy":{"name":"user-login"}}}
```

Die Zeitstempel von 4.x tragen keine Zeitzone und werden in der Zeitzone des Servers gelesen (`08:13:02` dort wurde zu
`15:13:02Z`). Die Datenschutzregeln gelten (die Adresse gekürzt), und der versuchte Login einer fehlgeschlagenen
Anmeldung wird gehasht.

#### Schlüssel

```
$ ./console exp:audit key list --allow-root-user
k1-20261002-3fa94c1b  3FA9 4C1B 77D0 E215  (active)
```

Rotation (Sandbox):

```
$ ./console exp:audit key rotate                   (sandbox)
Signing key rotated: k1-20261003-0a43315c -> k2-20261003-f12e3c8d (fingerprint F12E 3C8D 8CC8 8C12); the old key stays for its archives

$ ./console exp:audit key list                     (sandbox)
k1-20261003-0a43315c  0A43 315C DD87 355B
k2-20261003-f12e3c8d  F12E 3C8D 8CC8 8C12  (active)
```

Die mit k1 signierten Archive wurden nach der Rotation weiterhin als INTACT geprüft (`verify --archives`, Sandbox).

#### Sinks und Alarme

```
$ ./console exp:audit sinks list --allow-root-user
syslog     expAuditSyslogSink     ready; spooled 0; channels access, system
webhook    expAuditWebhookSink    not ready: no URL is set ([AuditSink_webhook] URL); spooled 0; channels -
mail       expAuditMailSink       ready; spooled 0; channels -; last delivery 2026-10-02T23:45:02Z

$ ./console exp:audit sinks test syslog --allow-root-user
Test record 01M3ZH9RJ2B3K5B6RSW394HYJK delivered through syslog
(exit 0)

$ ./console exp:audit sinks test webhook --allow-root-user
Not delivered through webhook: no URL is set ([AuditSink_webhook] URL)
(exit 1)

$ ./console exp:audit sinks flush --allow-root-user
Nothing spooled
```

Was der syslog-Sink schreibt, wie journald es anzeigt:

```
$ journalctl -t exponential -n 1 -o short-iso
2026-10-02T17:05:53-0700 web1 exponential[1325003]: 1 2026-10-03T00:05:53.861Z web1 exponential 1325003 system [exp@32473 id="01M3ZH9RJ5912SQKP1B2FAQHAT" name="system.command.run" seq="124" channel="system" user="anonymous" ip="" result="success" request="r-01M3ZH9RJ25FV748WK0Z3SK7N4" hash="sha256:da6e4…"] {"v":1,…}
```

```
$ ./console exp:audit alerts list --allow-root-user
Alerts: enabled, evaluated in flush, cronjob
brute_force              threshold access.session.login.failed        20 in 300 s by actor.ip, critical -> syslog,mail
brute_force_user         threshold access.session.login.failed        10 in 900 s by object.id, alert -> syslog,mail
admin_role_granted       match     access.role.assign                 critical -> syslog,mail
settings_out_of_hours    schedule  system.setting.write               warning -> syslog
mass_delete              threshold content.node.remove.*              500 in 600 s by actor.user_id, critical -> syslog,mail
audit_disabled           match     system.audit.disable               emergency -> syslog,mail
chain_broken             match     system.audit.chain.broken          alert -> syslog,mail

$ ./console exp:audit alerts test brute_force --replay=2026-10-02 --allow-root-user
brute_force over 15986 record(s) since 2026-10-02: 0 alert(s) would fire (nothing recorded)

$ ./console exp:audit alerts recipients --rule=brute_force --allow-root-user
brute_force
  from:     default -> admin
  mail to:  webmaster@example.com  (admin)
```

`alerts test` wendet eine Regel auf vergangene Einträge an und meldet nur. Verwenden Sie es, um eine Schwelle
abzustimmen, bevor Sie sie ändern.

#### Der Cronjob-Teil, von Hand

```
$ ./console exp:audit cron --allow-root-user
Audit index: 2 rows
Audit cronjob run done
```

`cron --daily` führt die täglichen Aufgaben jetzt aus (Rotation, Prüfung, Archivierung, Aufbewahrung,
Pseudonymisierung, Prüfpunkt), statt auf `RotateAfter` zu warten.

### 3.4 Templates: Fetch-Funktionen und der Operator

Das Modul `audit` hat sechs Fetch-Funktionen. Jede prüft `audit/read` und dessen Kanal-Einschränkung für den aktuellen
Benutzer und gibt ein leeres Ergebnis zurück, nie einen Fehler, wenn der Benutzer nicht lesen darf. Ein Template kann
daher keine Ereignisse preisgeben. Die Verwendung wird einmal pro Anfrage und Fetch als `system.audit.read`
aufgezeichnet.

| Fetch | Parameter | Liefert |
|---|---|---|
| `events` | `channel name user login object target result severity request job run from to q offset limit` (limit 10) | Zeilen: `id time time_utc name label channel actor result reason object …` |
| `count` | dieselben Filter | eine Ganzzahl |
| `event` | `id` | ein Eintrag |
| `chain_status` | `channel`, `verify` (false) | pro Kanal: Ergebnis, Dateien, erster Bruch, geprüft am |
| `can_read` | `channel` | ob der aktuelle Benutzer diesen Kanal (oder irgendeinen) lesen darf |
| `hidden_menu_links` | `section` (`Leftmenu_audit`) | die Schlüssel der Links dieses Menüs in einen Kanal, den der aktuelle Benutzer nicht lesen darf (für `hidden_links` von `parts/ini_menu.tpl`) |

Der Tab Audit der Knotenansicht (`design/admin/templates/tabs/audit/node.tpl`) ist ein vollständiges Beispiel:

```smarty
{def $audit_tab_module = 'audit'
     $audit_tab_events = fetch( $audit_tab_module, 'events', hash( 'object', hash( 'type', 'node', 'id', $node.node_id ), 'limit', 10 ) )}
{if fetch( $audit_tab_module, 'can_read', hash( 'channel', 'content' ) )}
{foreach $audit_tab_events as $e}
    {$e.time|wash} <a href={concat( 'audit/event/', $e.id )|ezurl}>{$e.label|wash}</a> {$e.actor|wash} {$e.result|wash}
{/foreach}
<a href={concat( 'audit/console/(object)/node:', $node.node_id )|ezurl}>All audit events of this node</a>
{/if}
{undef $audit_tab_module $audit_tab_events}
```

Der Modulname wird absichtlich über eine Variable übergeben. Der Fetch wird dann nicht zu einem direkten
Klassenaufruf kompiliert, sodass ein Serverprozess, der gestartet wurde, bevor es die Audit-Klassen gab, nichts
anzeigt, statt zu scheitern.

Der Operator `audit_label` macht aus einem Namen seine Beschriftung, übersetzt im Kontext `kernel/audit`:
`{$e.name|audit_label|wash}` ergibt für `content.node.move` „Knoten verschoben“ (in der englischen
Oberfläche „Node move“).

### 3.5 Die Entwickler-API

Alle Klassen liegen in `kernel/classes/audit/` und im Autoload-Array des Kernels. **Sichern Sie jeden Aufruf mit
`class_exists()` ab**, damit Code auch dort läuft, wo das Audit fehlt oder ein Velocity-Worker älter ist als es.
**Das Audit wirft nie eine Ausnahme an seinen Aufrufer**: Ein Fehler beim Aufzeichnen geht in `error.log`, und Ihr
Code läuft weiter.

#### Ein Ereignis aufzeichnen

```php
// kernel/classes/ezcontentobjecttreenode.php, eZContentObjectTreeNode::move()
if ( class_exists( 'expAuditHook' ) )
    expAuditHook::emit( 'content.node.move', function () use ( $node, $oldParentNodeID, $newParentNodeID, $oldPath, $nodeID ) {
        $newParent = eZContentObjectTreeNode::fetch( $newParentNodeID );
        return array( 'object' => expAuditHook::node( $node ),
                      'target' => expAuditHook::node( $newParent ) ?: array( 'type' => 'node', 'id' => $newParentNodeID ),
                      'before' => array( 'parent' => (int)$oldParentNodeID, 'path' => (string)$oldPath ),
                      'after' => array( 'parent' => (int)$newParentNodeID,
                                        'path' => $newParent ? $newParent->attribute( 'path_string' ) . $nodeID . '/' : null ) );
    } );
```

- `expAuditHook::emit( $name, $data )` ist der Hook-Punkt des Kernels. `$data` kann ein Array oder eine Closure sein.
  Die Closure läuft nur, wenn der Name eingeschaltet ist, daher kostet das Beschreiben eines Knotens nichts, wenn das
  Ereignis aus ist. Nichts, was ein Hook-Punkt tut, kann eine Ausnahme in die Anfrage werfen. Hilfsmethoden
  beschreiben Dinge überall auf dieselbe Weise: `node()`, `object()`, `user()`, `role()`, `policy()`, `section()`,
  `contentClass()`, `states()`, `languages()`, `ids()`.
- `expAudit::event( $name, array $data = array() )` ist der zentrale Aufruf und gibt die Ereignis-ID zurück (eine
  ULID mit 26 Zeichen) oder `null`, wenn nichts aufgezeichnet wurde. Schlüssel von `$data`:

| Schlüssel | Bedeutung |
|---|---|
| `object`, `target` | Arrays: `type`, `id` und identifizierende Felder (`name`, `object_id`, `class`, `file`, `block`, `variable` …) |
| `before`, `after` | die geänderten Werte. Sie werden bei `MaxValueLength` abgeschnitten. Geheimnisse werden maskiert. Legen Sie hier nie Werte von Inhaltsattributen ab |
| `result`, `reason`, `error` | `success` (Standard), `refused` oder `failed`; ein Grundcode (`policy`, `token`, `credentials`, `not_found`, `expired`, …); bei failed `array( 'ref' => …, 'message' => … )`. Refused hebt den Schweregrad auf mindestens notice an, failed auf mindestens warning |
| `severity` | ein Name nach RFC 5424, der den der Registry überschreibt |
| `parent` | die ID eines übergeordneten Ereignisses (siehe unten) |
| `x` | die eigenen Felder Ihrer Erweiterung, unter ihrem Namen: `array( 'myext' => array( … ) )` |
| `actor` | nur für einen anderen Akteur als den aktuellen Benutzer (ein Befehl, der für jemanden handelt) |
| `verb` | Standard: das Verb des Katalogs, sonst die dritte Ebene des Namens |

- `expAudit::isOn( $name )`: Fragen Sie, bevor Sie aufwendige Werte bauen, wenn Sie keine Closure verwenden.
- Eine Verweigerung ist derselbe Name mit einem Ergebnis:
  `expAudit::event( 'access.permission.refused', array( 'object' => array( 'type' => 'view', 'id' => 'setup/cache' ), 'result' => 'refused', 'reason' => 'policy' ) );`

#### Übergeordnete und untergeordnete Ereignisse

```php
$parent = expAudit::begin( 'content.node.remove', array( 'object' => array( 'type' => 'node', 'id' => 89, 'name' => 'Archive 2019' ) ) );
foreach ( $subtree as $node )
    expAudit::event( 'content.node.remove', array( 'parent' => $parent, 'object' => array( 'type' => 'node', 'id' => $node->attribute( 'node_id' ) ) ) );
expAudit::end( $parent, array( 'after' => array( 'removed' => count( $subtree ) ) ) );
```

`begin()` gibt die ID sofort zurück und schreibt das übergeordnete Ereignis bei `end()`, nach seinen untergeordneten,
sodass es Summen enthalten kann. Jenseits von `ChildDepth` Ebenen oder `MaxChildren` untergeordneten Ereignissen
werden diese in `after.children_omitted` des übergeordneten gezählt, statt geschrieben zu werden.
`expAudit::withParent( $id, function () { … } )` macht `$id` zum übergeordneten Ereignis jedes Ereignisses darin, für
Code, der keine IDs weitergibt. Der Worker der Inhaltsaufträge umschließt jeden Stapel auf diese Weise. Ein
übergeordnetes Ereignis, das am Ende der Anfrage noch offen ist, wird mit `result: failed`, `reason: error`
geschrieben. `expAudit::setJob( $jobID )` und `expAudit::setRun( $runID )` versehen jedes folgende Ereignis des
Prozesses mit `job` und `run`. `expAuditHook::muted( array( 'content.object.purge' ), function () { … } )` hält innere
Hook-Punkte still, während ein äußerer die Aktion als Ganzes aufzeichnet. Zum Beispiel das Bereinigen innerhalb eines
Entfernens.

#### Eigene Ereignisnamen: ein Zweig der Taxonomie

```php
// extension/myext/classes/myextauditbranch.php
class myExtAuditBranch implements expAuditTaxonomyBranch
{
    public function events()
    {
        return array(
            'content.myext_poll.vote'  => array( 'label' => 'Poll vote',   'severity' => 'info',   'default' => 'off' ),
            'content.myext_poll.close' => array( 'label' => 'Poll closed', 'severity' => 'notice', 'default' => 'on', 'channel' => 'content' ),
        );
    }
}
```

```ini
# extension/myext/settings/audit.ini.append.php
[AuditEventSettings]
Branches[myext]=myExtAuditBranch
```

```php
// extension/myext/modules/poll/close.php
if ( class_exists( 'expAudit' ) )
    expAudit::event( 'content.myext_poll.close', array( 'object' => array( 'type' => 'poll', 'id' => $pollID ) ) );
```

Namen bestehen aus 3 bis 6 Ebenen der Form `[a-z][a-z0-9_]*` in einer der fünf Domänen (`content`, `access`,
`system`, `commerce`, `data`). Beginnen Sie Ihre Subjekte mit dem Namen Ihrer Erweiterung (`content.myext_*`). Ein
doppelt beanspruchter Name, eine Klasse, die fehlt oder die Schnittstelle nicht implementiert, oder ein fehlerhafter
Name wird weggelassen und von der RAD-Bestandsaufnahme (Registry `auditbranches`) und in `audit/settings` gemeldet.
Erzeugen Sie die Autoloads neu, nachdem Sie die Klasse hinzugefügt haben (`php bin/php/ezpgenerateautoloads.php -e`).

#### Ein vorhandenes ezpEvent ohne Code aufzeichnen: die Brücke

```ini
[AuditBridgeSettings]
Bridge[content/state/assign]=content.object.state
Bridge[myext/vote]=content.myext_poll.vote
```

Pro Eintrag wird ein Listener angehängt, wenn `ezpEvent::registerEventListeners()` läuft (Web) oder einmal pro
ezpEvent-Instanz (Befehle). In einem Velocity-Worker wird er nur einmal angehängt. Der Listener zeichnet `after.args`
auf, die Argumente des Ereignisses in skalarer Form (Objekte als `class#id`; Argumente von Ereignissen `session/*`
sind Sitzungs-IDs und werden nur gehasht aufgezeichnet). Der Wert eines Filter-Ereignisses wird unverändert
durchgereicht. Ausgeliefert: `session/regenerate` → `access.session.regenerate` sowie `session/destroy` und
`session/gc` → `access.session.expire`. Beide sind standardmäßig aus.

#### Die Quelle einer Erweiterung in der Konsole

Ein Zweig bringt die Namen einer Erweiterung in das Audit; eine **Quelle** sagt der Konsole, wie sie anzuzeigen sind.
Eine Erweiterung, die eigene Ereignisse aufzeichnet (am besten in einem eigenen Kanal), registriert eine Klasse, die
`expAuditSource` implementiert, meist durch Erweitern von `expAuditSourceBase`, das jede Methode mit „nichts“
beantwortet:

```php
// extension/myext/classes/myextauditsource.php
class myExtAuditSource extends expAuditSourceBase
{
    public function label()   { return ezpI18n::tr( 'extension/myext/audit', 'Polls' ); }
    public function channel() { return 'polls'; }
    public function problem() { return class_exists( 'myExtPoll' ) ? '' : 'the classes of myext are not loaded'; }

    /** Links on the console's views ($view: audit/console, audit/recent, audit/charts, audit/alerts, audit/export) */
    public function links( $view, array $filters )
    {
        $links = array( array( 'label' => 'Votes', 'url' => self::consoleUrl( 'audit/console', array( 'name' => 'content.myext_poll.vote' ) ) ) );
        if ( self::hasPolicy( 'poll', 'manage' ) )
            $links[] = array( 'label' => 'All polls', 'url' => 'poll/list/(state)/open' );
        return $links;
    }

    /** The dashboard panel, from the index: figures, lists, links, note */
    public function dashboard( expAuditQuery $q, ?array $allowed, array $period )
    {
        $week = array( 'from_ms' => $period['week_ms'], 'name' => 'content.myext_poll.vote' );
        return array( 'figures' => array( array( 'label' => 'votes, 7 days', 'value' => $q->count( $week, $allowed ),
                                                 'url' => self::consoleUrl( 'audit/console', array( 'name' => 'content.myext_poll.vote' ) ) ) ),
                      'lists' => array( array( 'title' => 'Top voters', 'items' => array() ) ) );
    }

    /** audit/event: the record's fields as the extension names them, and links into its pages */
    public function event( array $record )
    {
        return array( 'fields' => array( array( 'label' => 'Poll', 'value' => $record['object']['id'] ) ),
                      'links' => array( array( 'label' => 'The poll', 'url' => 'poll/view/' . (int)$record['object']['id'] ) ) );
    }

    /** The address of an object type the extension records (the Object line of audit/event) */
    public function linkFor( $type, $id ) { return $type === 'poll' ? 'poll/view/' . (int)$id : null; }

    /** audit/charts: each series counts a name pattern per day */
    public function charts()
    {
        return array( array( 'title' => 'Polls per day', 'series' => array( array( 'label' => 'votes', 'name' => 'content.myext_poll.vote' ),
                                                                            array( 'label' => 'closed', 'name' => 'content.myext_poll.close' ) ) ) );
    }

    /** The CSV export of the channel: column => path of the value in the record, after the generic columns */
    public function exportColumns()
    {
        return array( 'poll' => 'object.id', 'choice' => 'after.choice', 'votes' => 'after.votes' );
    }
}
```

```ini
# extension/myext/settings/audit.ini.append.php (no empty "Channels[]" or "Rules[]" line: it would clear the kernel's list)
[AuditEventSettings]
Branches[myext]=myExtAuditBranch          # its events() give 'channel' => 'polls'
[AuditChannelSettings]
Channels[]=polls
[AuditChannel_polls]
LiveDays=90
ArchiveDays=730
MaxFileSize=64M
ArchiveFormat=gzip
Sinks[]
[AuditSourceSettings]
Sources[myext]=myExtAuditSource
```

```ini
# extension/myext/settings/menu.ini.append.php: the audit sidebar (each link only with its policies)
[Leftmenu_audit]
Links[myext_events]=audit/console/(channel)/polls
LinkNames[myext_events]=Poll events
PolicyList_myext_events[]=audit/read
```

Was jede Ansicht damit tut (nur für eine Quelle, die hier funktioniert, `problem()` ist `''`, und deren Kanal der
Benutzer unter der Einschränkung `Channel` von `audit/read` lesen darf):

| Ansicht | Was die Quelle hinzufügt |
|---|---|
| `audit/dashboard` | ihr Panel nach Aktivität: Zahlen (jede optional mit Link und einem Zustand success, refused oder failed), Listen, Links, ein Hinweis; pro Benutzer eine Minute lang aufbewahrt (ihre Links folgen den Richtlinien des Benutzers) |
| `audit/console` | die Option des Kanals lautet „polls (Polls)“; eine Zeile mit den Links der Quelle unter dem Filter |
| `audit/recent`, `audit/alerts`, `audit/export` | die Zeile mit Links; bei den Alarmen zeigt eine Regel auf die Namen der Quelle die Quelle unter ihrem Ereignis |
| `audit/charts` | die Zeile mit Links und eine Karte pro Diagramm: ein Balken pro Reihe und Tag, die Zahlen in einer Tabelle (weggelassen, wenn die Seite auf einen anderen Kanal gefiltert ist) |
| `audit/event` | für einen Eintrag im Kanal der Quelle eine Karte mit den Feldern und Links der Quelle; die Zeilen Objekt und Ziel verlinken über `linkFor()` |
| CSV-Export | `audit/export/(channel)/polls/(format)/csv` und `exp:audit export --channel=polls --format=csv`: die allgemeinen Spalten, dann `exportColumns()` (ein fehlender Wert ist leer, ein boolescher 1 oder 0, eine Liste oder ein Hash JSON); ein anderer Kanal und kein Kanalfilter behalten die allgemeinen Spalten. Die Exporte JSON und JSON Lines enthalten ohnehin jedes Feld |
| linkes Menü | ein Link in den Kanal der Quelle (`audit/console/(channel)/polls` oder einer, der in `[Leftmenu_audit]` mit `AuditChannel_<key>=polls` benannt ist) wird für einen Benutzer weggelassen, der den Kanal nicht lesen darf |
| `audit/settings` | eine Karte Quellen: Kennung, Beschriftung, Kanal, Klasse und was sie am Funktionieren hindert (nur angezeigt, wenn eine Quelle registriert ist) |
| Richtlinien | die Einschränkung `Channel` von `audit/read` führt jeden Kanal aus `Channels[]` auf, den einer Erweiterung nach ihrer Quelle benannt |

Regeln, die jede Quelle befolgt, durchgesetzt von `expAuditSourceRegistry`:

- **Adressen sind Modul/View-Pfade mit View-Parametern** (`expai/log/(errors)/1`), nie ein Query-String, eine
  absolute URL, ein führender Schrägstrich oder `..`. Alles andere wird verworfen. Bauen Sie Adressen der Konsole mit
  `consoleUrl()`.
- **Nur Links, denen der Benutzer folgen darf.** Die Quelle prüft ihre eigenen Richtlinien
  (`hasPolicy( $module, $function )`); die Konsole prüft `audit/read` und den Kanal.
- **Nie eine Ausnahme in eine Ansicht werfen.** Eine Quelle, die eine Ausnahme wirft, wird bei diesem Aufruf
  weggelassen und in `error.log` geschrieben.
- **Nur sichere Spalten exportieren.** `exportColumns()` benennt Felder für eine Tabelle, die weitergegeben wird:
  Zählungen, Codes, Kennungen, nie einen Text, den ein Benutzer eingegeben hat, eine Antwort, einen Schlüssel oder ein
  Token. Spaltennamen bestehen aus a-z, Ziffern und `_` (höchstens 48), Pfade sind Ebenen in den Eintrag
  (`after.usage.in`); der Name einer allgemeinen Spalte, ein fehlerhafter Name oder Pfad wird verworfen.
- **Benennen Sie Felder so, dass die Datenschutzregeln sie stehen lassen.** Ein Feld, dessen Name password, secret,
  token, salt, credential, privatekey oder apikey enthält, wird als `[secret]` geschrieben (auch `input_tokens`): Legen
  Sie Zählungen unter einen neutralen Schlüssel (expai: `after.usage.in`, `after.usage.out`).
- **Bei inaktiver Erweiterung ändert sich nichts**: Ihre Einstellungen werden nicht gelesen, keine Quelle, kein Kanal,
  kein Menüeintrag. Ihre Dateien bleiben in `var/log/audit/` und werden von `exp:audit status` wie die jedes Kanals
  aufgeführt.

Exponential Intelligence (Erweiterung expai) ist die Referenz: `classes/audit/expaiauditsource.php`, ihr Zweig
`expaiauditbranch.php`, der Recorder `expaiaudit.php` und ihre Dokumentation `doc/audit-trail.md`.

#### Ein Sink, eine Klasse für Alarmregeln, ein Archivformat

Jedes davon ist eine in audit.ini genannte Klasse, die von der RAD-Bestandsaufnahme gezählt wird (Registries
`auditsinks`, `auditalertrules`, `auditformats`):

```php
class myExtAuditSink extends expAuditSinkBase          // implements expAuditSink
{
    public function name()    { return 'myext'; }
    public function problem() { return trim( (string)$this->setting( 'Endpoint' ) ) === '' ? 'no Endpoint is set' : ''; }
    /** @param array[] $records already through the privacy rules; @return int how many were delivered */
    public function deliver( array $records ) { /* send them */ return count( $records ); }
}
// audit.ini: [AuditSinkSettings] SinkClasses[myext]=myExtAuditSink ; [AuditSink_myext] Endpoint=… ; [AuditChannel_access] Sinks[]=myext
```

- **Alarmregeln** implementieren `expAuditAlertRule::evaluate( array $config, array $records, $state )` und geben die
  Alarme zurück (Gruppe, Anzahl, Ereignisse, Meldung). Sie werden als `[AuditAlertSettings] RuleClasses[<key>]=<class>`
  registriert und von `[AlertRule_<name>] Class=<key>` verwendet. Erweitern Sie `expAuditAlertRuleBase` für den
  Zustand des Zeitfensters.
- **Format-Handler** implementieren `expAuditFormatHandler` (`name`, `problem`, `extension`, `compress( $source,
  $target, $level )`, `open( $archive )`, das einen Stream der einfachen Zeilen zurückgibt). Erweitern Sie
  `expAuditFormatBase` und registrieren Sie `[AuditArchiveSettings] FormatHandlers[<name>]=<class>`.

`problem()` gibt `''` zurück, wenn die Klasse hier funktionieren kann, sonst den Grund. Die Ansicht Einstellungen und
`sinks list` zeigen ihn an.

#### Schreibzugriffe auf Einstellungen und die API von 4.x

- Jedes Schreiben über `expIniEditor` (exp:ini, die Debug-Leiste, die Einstellungsansichten) wird als
  `system.setting.write` aufgezeichnet, ein Eintrag pro geänderter Variable, mit dem Diff. Werte von Variablen, die
  `expIniEditor::isSecret()` erkennt, werden zu `[secret]`. Ein Schreiben in audit.ini ist außerdem
  `system.audit.setting.write`, und `Audit` auf etwas anderes als `enabled` zu setzen ist `system.audit.disable`,
  aufgezeichnet, bevor es wirkt.
- `eZAudit::writeAudit( $name, $attributes )` funktioniert für Erweiterungen weiter. Die alten Namen werden über
  `[AuditCompatSettings] Map[]` zugeordnet, und die Attribute kommen unter `after.legacy` (`Comment`, Schlüssel aus
  `NeverRecord[]` und Geheimnisse werden verworfen). Nicht zugeordnete Namen werden zu `system.legacy.<name>`. Der
  Kernel ruft es nicht mehr auf: Seine Aufrufstellen lösen native Ereignisse aus.
- Die Namen für Orte aus LoveStack (mugoweb/ezpublish-legacy, Commit `9c931192e0`, mit Dank an Mugo Web) werden
  ebenfalls zugeordnet, wobei ihre Attribute in den Eintrag gelesen werden: `location-assign` ist `content.node.add`
  (Objekt; after: Eltern, Hauptknoten), `location-remove` ist `content.node.remove` (der entfernte Knoten als Objekt;
  before: Eltern, main) und `main-node-update` ist `content.node.main` (der neue Hauptknoten als Ziel; after:
  Hauptknoten, Eltern). Ihre Dateien (`location_assign.log`, `location_remove.log`, `location_main_update.log`) stehen
  in `AuditFileNames[]`, sodass `exp:audit import` sie liest und `(legacy_file)` ihre Ereignisse findet.

#### Änderungen an Orten: wer einen Ort hinzugefügt, entfernt oder gewählt hat

Der Kernel zeichnet die Änderungen an Orten selbst auf, aus `eZContentOperationCollection`, im Kanal `content`:

| Was der Redakteur getan hat | Ereignis | Beschriftung in der Konsole | Worüber | Vorher → nachher |
|---|---|---|---|---|
| Einen Ort hinzugefügt (Fenster Orte, Orte hinzufügen, ein Inhaltsauftrag) | `content.node.add`, eines pro neuem Ort | Ort hinzugefügt | das Objekt; der neue Knoten ist das Ziel | Orte → Orte, Knoten, Eltern, Hauptknoten |
| Einen Ort entfernt (Fenster Orte, ein Inhaltsauftrag) | `content.node.remove` | Ort entfernt | der entfernte Knoten | Eltern, Pfad, Objekt-ID, Name, main → – |
| Einen anderen Hauptort gewählt | `content.node.main` | Hauptort geändert | das Objekt; der neue Hauptknoten ist das Ziel | Hauptknoten → Hauptknoten, Eltern |
| Den Hauptort entfernt | `content.node.remove`, dann `content.node.main` mit `cause` `location-remove` | | | |

`content.node.remove` ist standardmäßig eingeschaltet. `content.node.add` und `content.node.main` sind wie bisher aus:
Schalten Sie sie mit `Enabled[]=content.node.add` und `Enabled[]=content.node.main` (oder `content.node.*`) in
`settings/override/audit.ini.append.php` ein. Die Konsole zeigt die Einträge unter ihren Beschriftungen, und
`audit/event/<id>` führt Objekt, Ziel und die Werte vorher und nachher auf, mit Links zu den Knoten. Bei einem Upgrade
ändert sich nichts, bis sie eingeschaltet werden.

---

## 4. Wartungshandbuch

### 4.1 Cron

Der Cronjob-Teil `audit` (`cronjobs/audit.php`, Klasse `Exponential\Cronjob\Kernel\Audit`) erledigt die gesamte
geplante Arbeit:

- **bei jedem Lauf**: den inkrementellen Index (höchstens 120 s), die Zustellung der Spools der Sinks (Webhook,
  Netzwerk-syslog, E-Mail), die Auswertung der Alarme über die Einträge seit seinem Cursor, die Prüfung, dass Audit
  nicht von Hand ausgeschaltet wurde;
- **einmal am Tag**, beim ersten Lauf nach `[AuditRotationSettings] RotateAfter` (00:15, Zeitzone der Website):
  untätige Tagesdateien schließen, jeden Kanal prüfen, archivieren, was älter als `LiveDays` ist, löschen, was älter
  als `ArchiveDays` ist, Index-Zeilen pseudonymisieren, die älter als `PseudonymiseAfterDays` sind, Index-Zeilen
  entfernen, die älter als `KeepDays` sind, und den signierten Prüfpunkt schreiben.

Er wird in der Gruppe `frequent` von `settings/cronjob.ini` ausgeliefert, daher genügt die übliche Crontab-Zeile:

```
*/5 * * * * cd /path/to/exponential && php runcronjobs.php -q frequent
```

Für sich allein: `php runcronjobs.php audit`. Ein Lauf belegt `<LogDir>/.cron.lock`, sodass sich zwei Läufe nie
überschneiden. Die tägliche Markierung ist `<LogDir>/.cron-daily`. Auf der Referenzinstallation läuft der Teil jede
Minute in der Gruppe `publishing` (`settings/override/cronjob.ini.append.php`). Jeder Lauf wird als
`system.cronjob.run` (Teil `audit`) aufgezeichnet. Die Karte Betrieb des Dashboards warnt, wenn der Teil eine Stunde
lang nicht gelaufen ist.

### 4.2 Rotation und Archive

- **Tagesdateien.** Dateien werden nach dem UTC-Datum zum Zeitpunkt des Schreibens benannt. Das erste Schreiben eines
  neuen UTC-Tages schließt die Datei des Vortages mit `system.audit.file.close`. `exp:audit rotate` schließt einen
  Kanal, in den seitdem niemand geschrieben hat.
- **Größe.** Jenseits von `MaxFileSize` (standardmäßig 64M, für read 256M) wird der Tag in
  `<channel>-<date>.2.jsonl`, `.3.jsonl` … fortgesetzt, in derselben Kette verknüpft.
- **Archivieren** (tägliche Aufgabe oder `exp:audit archive`): Jeder geschlossene Tag, der älter als `LiveDays` (90;
  read: 30) ist, wird geprüft (`VerifyBeforeArchive`), mit dem `ArchiveFormat` des Kanals nach
  `<ArchiveDir>/<channel>/<YYYY>/<file>.jsonl.<ext>` komprimiert, zurückgelesen und per sha256 verglichen
  (`VerifyAfterArchive`) und erhält ein signiertes Manifest `<channel>-<date>.manifest.json`. Erst dann wird die
  Live-Datei entfernt. Ein gebrochener Tag wird trotzdem archiviert (Beweise zu entfernen wäre schlimmer); sein
  Manifest sagt broken, und `system.audit.chain.broken` wird aufgezeichnet.
- **Formate**: gzip (zlib, immer vorhanden), bzip2 (ext-bz2), xz (die Binärdatei `xz`), zstd (ext-zstd oder die
  Binärdatei), zip (ext-zip). Ein Handler, der hier nicht funktionieren kann, meldet in `audit/settings`, warum, und
  die Archivierung fällt auf gzip zurück. Das Manifest nennt den tatsächlich verwendeten Handler. Auf der
  Referenzinstallation funktionieren alle fünf.
- **Wohin Archive gehen.** `ArchiveDir` (standardmäßig `log/audit/archive` unter dem var-Verzeichnis) liegt am besten
  auf einem anderen Dateisystem oder auf einem Mount, zu dem der Benutzer des Webservers Dateien hinzufügen, sie aber
  nicht ändern kann (siehe [5.11](#511-sicherheit)). Modi: `FileMode=0440`, `DirMode=0750`.

### 4.3 Aufbewahrung

Pro Kanal in `[AuditChannel_<channel>]`: `LiveDays` (unkomprimiert in LogDir) und `ArchiveDays` (als Archive
aufbewahrt). Ausgeliefert: 90 und 730 für content, access, system und commerce sowie 30 und 90 für read. **Die
ausgelieferten Kanalblöcke setzen beide Werte**, daher erreicht ein Standardwert in `[AuditRotationSettings]` einen
Kanal nur, wenn Sie den eigenen Wert des Kanalblocks leeren. Setzen Sie sie pro Kanal (siehe die
[Konfiguration für eine kleine Website](#61-kleine-website)). Der Index behält seine Zeilen
`[AuditIndexSettings] KeepDays` (730) lang.

Die Aufbewahrung entfernt nie einen Signaturschlüssel aus den Einstellungen: `exp:audit purge` nennt die Schlüssel,
die aufbewahrte Archive noch brauchen. Entfernen Sie einen Schlüssel selbst nur, wenn kein damit signiertes Archiv
mehr übrig ist.

### 4.4 Prüfen und wiederherstellen

- **Prüfen** Sie jeden Tag (der Cronjob-Teil tut es), nach jedem Vorfall und bevor Sie jemandem Einträge übergeben:
  `exp:audit verify` (live), `exp:audit verify --archives` (alles), `--channel=`, `--date=`. Exit-Code 0 = intakt,
  1 = gebrochen, 2 = Fehler, das passt also zur Überwachung: `./console exp:audit verify -q --allow-root-user || alert`.
  Die Prüfung des 14 MB großen Kanals system dauerte auf der Referenzinstallation 2,5 s.
- **Jetzt prüfen** in der Konsole und im Dashboard (Verwaltung) prüft jeden Kanal, den der Benutzer lesen darf, und
  speichert das Ergebnis. Einfache Seitenaufrufe zeigen den gespeicherten Zustand und durchlaufen nie die Dateien.
- **Was bei BROKEN zu tun ist**: Bearbeiten oder entfernen Sie nichts. Notieren Sie Datei, Zeile und Art, kopieren Sie
  das Verzeichnis an einen sicheren Ort und vergleichen Sie mit dem, was den Server verlassen hat (Kopien in syslog
  oder im Journal, Webhook-Empfänger, von den Sinks gesendete Prüfpunkte, Archive).
  [5.3](#53-die-hash-kette-und-was-die-prüfung-findet) erklärt jede Art. Eine gebrochene Datei bleibt ein Nachweis:
  Die Konsole zeigt sie weiterhin an und kennzeichnet sie.
- **Stellen Sie** einen archivierten Tag zum Lesen **wieder her**:
  `exp:audit restore --channel=<c> --date=<YYYY-MM-DD> [--to=<dir>]`, dann `search --files` oder die Datei lesen. Der
  Tag wird nach `<LogDir>/restored/` entpackt und gegen das sha256 des Manifests geprüft.

### 4.5 Alarme und ihre Empfänger

Alarmregeln sind Blöcke `[AlertRule_<name>]`, aufgeführt in `[AuditAlertSettings] Rules[]`. Jedes Auslösen ist ein
Eintrag `system.audit.alert`, der an die `Sinks[]` der Regel gesendet wird. E-Mail geht an:

1. die eigenen `[AlertRule_<rule>] Recipients[]` der Regel, sonst
2. `[AuditAlertSettings] Recipients[]`, sonst
3. `[AuditSink_mail] Receivers[]` (der ältere Name), sonst
4. `admin` = site.ini `[MailSettings] AdminEmail`.

| Empfänger | Bedeutung |
|---|---|
| `admin` | site.ini `[MailSettings] AdminEmail` |
| `address:ops@example.com` (oder eine bloße Adresse) | diese Adresse |
| `group:security` | die benannte Liste `[AlertRecipients_security]`: `Addresses[]` und `Recipients[]` (jeder Art, auch andere Gruppen; Schleifen werden abgeschnitten) |
| `user:14`, `login:editor1` | dieser Benutzer, mit der E-Mail-Adresse, die er hat, wenn die E-Mail gesendet wird |
| `usergroup:12`, `usergroup:<remote id>` | jeder aktivierte Benutzer unterhalb dieser Benutzergruppe, Untergruppen eingeschlossen |
| `role:Administrator`, `role:<id>` | jeder aktivierte Benutzer, dem die Rolle zugewiesen ist, direkt oder über eine Gruppe |

Adressen werden ohne Rücksicht auf Groß- und Kleinschreibung dedupliziert. Deaktivierte Benutzer und ungültige
Adressen werden weggelassen. Nichts im Ereignis selbst wird je zum Empfänger. E-Mails sendet nur der Cronjob-Teil (eine
Anfrage wartet nie auf SMTP), eine E-Mail pro Empfänger, höchstens eine pro Regel und Empfänger innerhalb von
`[AuditSink_mail] Throttle` (900 s). Prüfen Sie es mit `exp:audit alerts recipients [--rule=<rule>]`, das jede Adresse
mit den Einträgen aufführt, aus denen sie entstanden ist, und jedes Problem (eine unbekannte Gruppe, ein deaktivierter
Benutzer, eine fehlende Rolle).

Um eine Regel abzustimmen, spielen Sie sie zuerst nach: `exp:audit alerts test mass_delete --replay=2026-09-01`.
Regelklassen: `threshold` (Threshold Ereignisse in Window Sekunden pro GroupBy; löst einmal pro Zeitfenster aus, erneut,
wenn sich die Anzahl verdoppelt; `CountChildren=enabled` zählt untergeordnete Ereignisse mit), `match` (jeder passende
Eintrag; `Policies[]` für Rollenvergaben), `schedule` (außerhalb von `BusinessDays`/`BusinessHours` in der Zeitzone der
Website, einmal pro Gruppe und Zeitfenster).

### 4.6 Schlüssel

- **Was es gibt**: eine Installations-ID, Signaturschlüssel (`SigningKey[<key id>]`, einer aktiv) und einen
  Pseudonym-Schlüssel, alle in `settings/override/audit.ini.append.php` (Modus 0640, nie committet). Sie werden beim
  ersten Ereignis erzeugt, das `system.audit.key.create` aufzeichnet. exp:ini, die Debug-Leiste und die Ansicht
  Einstellungen maskieren sie. Angezeigt werden nur IDs und Fingerabdrücke.
- **Sichern Sie sie** zusammen mit dem Rest von `settings/override` und behandeln Sie die Sicherung als Geheimnis.
  Ohne die Signaturschlüssel lassen sich damit signierte Archive und Prüfpunkte nicht mehr prüfen (`unknown_key`).
  Ohne den Pseudonym-Schlüssel passen neue Pseudonyme nicht mehr zu alten.
- **Rotieren Sie den Signaturschlüssel** einmal im Jahr (das Dashboard erinnert Sie nach 365 Tagen daran) und sofort,
  wenn der Schlüssel gelesen worden sein könnte: `exp:audit key rotate`. Der neue Schlüssel wird aktiv, die alten
  bleiben für ihre Archive, und `system.audit.key.rotate` wird aufgezeichnet. Der Befehl leert den INI-Cache, damit
  andere Prozesse den neuen aktiven Schlüssel lesen; signiert ein Velocity-Worker weiter mit dem alten, starten Sie
  Velocity neu.
- **Der Pseudonym-Schlüssel** wird nur absichtlich ersetzt: `exp:audit key rotate --pseudonym`, dann
  `exp:audit pseudonymise`. Alte und neue Pseudonyme derselben Person unterscheiden sich dann.
- **Schlüssel aus einem Secrets-Speicher**: Setzen Sie `[AuditKeySettings] GenerateKeys=disabled` und stellen Sie
  `InstallationID`, `ActiveSigningKey`, `SigningKey[<id>]` (base64, 32 Bytes) und `PseudonymKey` selbst in
  settings/override bereit.
- **Mehrere Server** müssen sich eine Schlüsseldatei teilen (siehe die [Konfiguration für einen Cluster](#63-cluster)).

### 4.7 Der Index

Der Index (`expaudit_event`, `expaudit_cursor`, `expaudit_file` in der Datenbank der Website) ist eine Kopie der
Dateien für die Konsole. Während einer Seitenanfrage schreibt nichts in ihn. Er wird gefüllt:

- vom Cronjob-Teil (bei jedem Lauf, inkrementell, höchstens `BatchSize` Zeilen pro Stapel, Zeilen und Cursor in einer
  Transaktion, sodass ein Absturz nie eine Zeile doppelt indiziert oder eine überspringt);
- kurz, wenn eine Ansicht der Konsole geöffnet wird (ein begrenztes Aufholen), und von
  `exp:audit reindex --incremental`;
- von Grund auf durch `exp:audit reindex` (alle Kanäle oder `--channel=`).

Der Indexer prüft das `prev` jedes Eintrags gegen den Cursor. Eine Abweichung kennzeichnet die Datei in
`expaudit_file` als gebrochen und zeichnet einmal `system.audit.chain.broken` auf. Die Volltextsuche ist FTS5 mit dem
Trigram-Tokenizer unter SQLite, `FULLTEXT` unter MySQL/MariaDB, ein erzeugter `tsvector` mit GIN unter PostgreSQL,
Oracle Text, wenn verfügbar, und `LIKE` überall sonst (und für Begriffe unter drei Zeichen). Importierte Einträge
(`<LogDir>/imported/`) werden ohne Prüfung der Kette indiziert. Lesezugriffe werden nur mit `IndexReads=enabled`
indiziert.

#### Wiederholte Einträge

Der Index enthält eine Zeile pro Eintrags-ID (`id` ist sein Primärschlüssel), daher wird eine Zeile, die eine bereits
indizierte ID wiederholt, gelesen und weggelassen. Vor dem 2026-10-12 (#396) schrieb ein Prozess, der mit Einträgen in
seinem Puffer geforkt wurde (`pcntl_fork()`: die Worker der Tests für Inhaltsaufträge, der asynchrone Publisher, ein
Worker, der geforkt wurde, nachdem sein Elternprozess etwas aufgezeichnet hatte), auch seine Kopie des Puffers und der
offenen übergeordneten Ereignisse des Elternprozesses, sodass dieselben Einträge erneut in den Kanal gingen, einmal pro
Kind, jedes Mal mit neuem `seq`, `prev` und `hash`. Die wiederholten Zeilen sind Teil der Kette (verify meldet INTACT
und zählt sie, siehe [Prüfen](#prüfen)), daher bleiben sie in den Dateien: Sie zu entfernen würde die Kette brechen.
Der Index, die Konsole, ihre Zählungen, Diagramme und Alarme, der Web-Export und `exp:audit export` (das eine
wiederholte ID auf dieselbe Weise weglässt) zeigen jeden Eintrag einmal.

Auf der Referenzinstallation (2026-10-12): Jede Wiederholung lag im Kanal content, 13 643 Zeilen, die 1 692 IDs
wiederholten, alle von PHPUnit-Läufen der Tests für Inhaltsaufträge geschrieben (Einträge
`content.job.create`/`resume` des Testprozesses, von jedem geforkten Worker wiederholt), am 10-04 (2 322), 10-05
(5 107), 10-06 (4 414) und 10-12 (1 800). Die Dateien enthielten 41 295 Zeilen im Kanal content, 27 652 verschiedene,
und der Index 27 652 Zeilen: Es fehlte nichts. Jeder andere Kanal stimmte Tag für Tag überein; `read` wird nicht
indiziert (`IndexReads=disabled`).

Seit #396 merkt sich `expAudit` den Prozess, dem sein Puffer gehört. Ein geforktes Kind beginnt mit einem leeren
Puffer, ohne offene übergeordnete Ereignisse und ohne fehlgeschlagene Einträge seines Elternprozesses, und mit einer
eigenen Anfrage-ID (und `request.pid`); es behält den Auftrag, den Lauf und den Stapel der übergeordneten Ereignisse,
sodass seine Einträge weiterhin den Auftrag und das übergeordnete Ereignis nennen, für die sie arbeiten. Der
Elternprozess schreibt seine Einträge einmal.

Um Dateien und Index pro Kanal und Tag zu vergleichen, zählen Sie die verschiedenen IDs der Dateien (nicht ihre
Zeilen) gegen `SELECT channel, COUNT(*) FROM expaudit_event GROUP BY channel`; ein Unterschied außer bei `read` und
den wenigen seit dem letzten Indexlauf geschriebenen Einträgen ist eine echte Lücke, und
`exp:audit reindex --incremental` (idempotent: bereits indizierte IDs werden übersprungen) füllt sie.

Größe auf der Referenzinstallation: etwa 0,8 bis 1,1 KB pro Eintrag in den Dateien. Planen Sie etwa 1,2 KB pro Zeile
im Index ein.

| Ereignisse pro Tag | Index-Zeilen nach 2 Jahren | Index | Live-Dateien (90 Tage) | Archive (2 Jahre, gzip etwa 1:8) |
|---|---|---|---|---|
| 1 000 (kleine Website) | 0,73 M | etwa 0,9 GB | etwa 80 MB | etwa 80 MB |
| 10 000 (redaktionelle Website) | 7,3 M | etwa 9 GB | etwa 0,8 GB | etwa 0,8 GB |
| 100 000 (Stichproben der Lesezugriffe eingeschaltet) | 73 M | Lesezugriffe aus dem Index heraushalten (`IndexReads=disabled`) | 8 GB | 8 GB |

### 4.8 Upgrades

Eine Installation, die vor 6.0.15 angelegt wurde:

1. **Index-Tabellen**: `php update/common/scripts/6.0/createaudittables.php` (jede Engine: MySQL/MariaDB,
   PostgreSQL, SQLite, Oracle, MongoDB). Vorhandene Tabellen bleiben unberührt, und das Skript indiziert danach die
   Dateien (`--no-index` überspringt das, `--dry-run` meldet nur). Auf der Referenzinstallation, wo die Tabellen
   existieren:

   ```
   $ sudo -u <site user> php update/common/scripts/6.0/createaudittables.php --no-index
   Database: sqlite, missing tables: none
   the tables exist already
   Full-text search: fts5
   ```

   Die SQL-Upgrade-Dateien `update/database/{mysql,postgresql,sqlite}/6.0/dbupdate-6.0.0-6.0.15.sql` enthalten
   dieselben Anweisungen.
2. **Ein Override aus 4.x, das das Audit ausschaltet.** 4.x lieferte `Audit=disabled` aus, und viele Websites haben
   das in settings/override behalten. Prüfen Sie, woher der Wert kommt:

   ```
   $ ./console exp:ini where audit.ini/AuditSettings/Audit --allow-root-user
   audit.ini/AuditSettings/Audit (load order of siteaccess site)
    1. settings/audit.ini                                           scope default
         Audit=enabled
   In effect:
         Audit=enabled
   ```

   Entfernen Sie eine alte Zeile `Audit=disabled`, sofern Sie sie nicht so meinen:
   `./console exp:ini rem audit.ini/AuditSettings/Audit global`.
3. **Die alten Logs**: `exp:audit import --dry-run`, dann `exp:audit import` (fügen Sie `--keep-originals` hinzu, um
   die Dateien an Ort und Stelle zu lassen). Siehe [3.3](#33-der-befehl-expaudit).
4. **Code**: `php bin/php/ezpgenerateautoloads.php -k`, Caches leeren, PHP-FPM neu laden und Velocity neu starten
   (seine Worker zeichnen nichts aus Klassen auf, die sie nicht geladen haben).
5. **Richtlinien**: Geben Sie `audit/read` (und `audit/manage`) denen, die das Audit lesen sollen. Nur Administrator
   hat sie.

### 4.9 Einstellungen ändern

Bearbeiten Sie nie `settings/audit.ini`. Legen Sie Änderungen in `settings/override/audit.ini.append.php` ab (oder in
`audit.ini.append.php` eines Siteaccess oder einer Erweiterung), vorzugsweise mit exp:ini, das das Schreiben
aufzeichnet und eine Sicherung anlegt. Vorschau mit `--dry-run`:

```
$ ./console exp:ini set audit.ini/AuditChannel_content/ArchiveDays 365 global --dry-run --allow-root-user
--- a/settings/override/audit.ini.append.php
+++ b/settings/override/audit.ini.append.php
@@ -6,4 +6,7 @@
 ActiveSigningKey=********
 SigningKey[k1-20261002-3fa94c1b]=********

+[AuditChannel_content]
+ArchiveDays=365
+
 */ ?>
Dry run: set audit.ini/AuditChannel_content/ArchiveDays in global, nothing written

$ ./console exp:ini get audit.ini/AuditChannel_content/ArchiveDays --allow-root-user
730
```

Nach einer Änderung: exp:ini leert den INI-Cache selbst. Nach einer Bearbeitung von Hand führen Sie
`php bin/php/ezcache.php --clear-tag=ini` aus. Zeigt ein Velocity-Worker noch den alten Wert, starten Sie Velocity neu.
Eine neue Klasse (ein Sink, ein Zweig) braucht immer einen Neustart von Velocity. Jedes Schreiben in audit.ini wird
aufgezeichnet (`system.audit.setting.write`), und das Ausschalten des Audits löst den Alarm `audit_disabled` aus.

### 4.10 Fehlerbehebung

| Symptom | Ursache | Lösung |
|---|---|---|
| Kein Tab Audit, kein Dashboard-Block | dem Benutzer fehlt `audit/read`; oder die Worker von Velocity starteten, bevor es die Audit-Klassen gab | eine Rolle mit `audit/read` zuweisen; Velocity neu starten |
| Es wird überhaupt nichts aufgezeichnet | irgendwo `Audit=disabled` (das Dashboard sagt „Audit ist ausgeschaltet“; `system.audit.disable` wurde aufgezeichnet) | `exp:ini where audit.ini/AuditSettings/Audit`, das Override entfernen |
| Nichts aufgezeichnet, `error.log` sagt "writing the audit channel …" oder enthält Zeilen `AUDIT-UNWRITTEN` | LogDir nicht beschreibbar (Besitzer, Modus, volle Festplatte) | Besitz korrigieren (`chown -R <site user>: var/<site>/log/audit`, Verzeichnisse 0770, Dateien 0640) oder Platz schaffen. Die Zeilen `AUDIT-UNWRITTEN` enthalten die Einträge, die nicht geschrieben werden konnten |
| Einträge von Apache, aber nicht von Velocity (:8080) | die Worker sind älter als die Audit-Klassen (jeder Hook ist mit `class_exists` abgesichert) | Velocity neu starten |
| Die Dateien haben mehr Zeilen, als die Konsole Ereignisse hat; `verify` sagt `N distinct (M repeated)` | Zeilen, die eine Eintrags-ID wiederholen, vor #396 von geforkten Prozessen geschrieben ([Wiederholte Einträge](#wiederholte-einträge)) | nichts zu tun: Der Index und die Exporte zeigen jeden Eintrag einmal; die Zeilen bleiben, sie sind Teil der Kette |
| Der Konsole fehlen die neuesten Ereignisse, oder Diagramme sind leer | der Index hinkt hinterher (Cronjob-Teil läuft nicht) oder seine Tabellen fehlen | `exp:audit reindex --incremental`; `createaudittables.php`; Cron prüfen |
| Dashboard: „Der Audit-Cronjob ist seit über einer Stunde nicht gelaufen“ | keine Crontab-Zeile, oder die Gruppe, die `audit.php` enthält, wird nicht ausgeführt | `runcronjobs.php frequent` (oder `audit`) zur Crontab hinzufügen |
| `verify` sagt BROKEN | eine Datei wurde geändert, nachdem sie geschrieben wurde (siehe die Art) | nicht reparieren; [4.4](#44-prüfen-und-wiederherstellen) folgen |
| `verify` sagt `no_origin` für die älteste Live-Datei | ihr archivierter Vorgänger liegt nicht dort, wo der Prüfer sucht (ArchiveDir geändert, Archive verschoben) | die Archive unter das konfigurierte ArchiveDir zurücklegen oder mit `--archives` prüfen |
| `unknown_key` | ein Manifest oder Prüfpunkt ist mit einer Schlüssel-ID signiert, die nicht mehr in den Einstellungen steht | dieses `SigningKey[<id>]` aus der Sicherung der Einstellungen wiederherstellen |
| `repaired` | ein Prozess ist mitten im Schreiben gestorben; das nächste Schreiben hat `system.audit.chain.repair` angehängt | nichts zu tun: Die abgerissenen Bytes bleiben zur Untersuchung in der Datei |
| Eine alte Tagesdatei wird nicht archiviert | sie ist noch die neueste Datei ihres Kanals (seitdem wurde nichts in diesen Kanal geschrieben) | `exp:audit rotate`, oder auf das nächste Schreiben warten |
| `journalctl -t exponential` findet nichts | `Transport=devlog` auf einem journald, der RFC-5424-Header auf /dev/log nicht auswertet | `Transport=local` verwenden (der native Socket von journald) |
| Der Spool des Webhooks wächst | der Empfänger ist nicht erreichbar oder lehnt ab (`system.audit.sink.failed`, einmal pro Sink und Stunde) | `exp:audit sinks list` zeigt den letzten Fehler; den Empfänger reparieren; `exp:audit sinks flush` |
| Keine Alarm-E-Mail | kein Empfänger, Mail-Transport im Debug-Modus (`DebugSending`), gedrosselt, oder der Cronjob-Teil läuft nicht | `exp:audit alerts recipients`; site.ini `[MailSettings]`; Cron prüfen |
| `search --name=…login*` endet mit 2: "a * may only be the whole pattern …" | ein Muster ist `*`, ein Präfix, das auf `.*` endet, oder ein ganzer Name | `--name='access.session.login.*'` |
| `search --q=…` sagt "invalid option `--q'" | `-q` ist die Standardoption für stille Ausgabe | `--query=…` |
| `--from` scheint Einträge zu verfehlen | ein bloßes `--from`/`--to` ist UTC, wie ausgegeben (die bloßen Zeiten der Konsole sind Zeit der Website) | UTC angeben oder den Offset hinzufügen: `--from=2026-10-03T01:30+02:00` |
| Ein Formular der Administration antwortet mit 503 „Nicht ausgeführt: Das Audit kann es nicht protokollieren“; `AUDIT-REFUSED` in error.log | `OnWriteFailure=refuse`, und LogDir ist nicht beschreibbar | LogDir wie oben korrigieren; siehe [4.13](#413-wenn-das-audit-nicht-schreiben-kann) |
| „Jetzt prüfen“ fragt nach dem Passwort | `ReauthForManage=enabled` | es eingeben; es gilt `ReauthMinutes` lang in dieser Sitzung |
| Eine Änderung der Einstellungen wirkt nicht | INI-Cache | `php bin/php/ezcache.php --clear-tag=ini` (exp:ini tut das selbst) |
| Das Log-Verzeichnis wächst schnell | Lesezugriffe eingeschaltet, oder ein gesprächiger Name einer Erweiterung eingeschaltet | `exp:audit status` (die heutigen Zählungen pro Kanal), `search --channel=… --limit=…`; den Namen in `Disabled[]` ausschalten; `SampleRate` senken |

### 4.11 Leistungsoptimierung

Gemessene Kosten auf der Referenzinstallation (je 2 000 Anfragen, im Kernel; [9.3](#93-leistung)): Eine Seite, die
nichts aufzeichnet, zahlt mit eingeschaltetem Audit **0,08 ms** mehr. Eine Anfrage, die ein Ereignis aufzeichnet,
zahlt etwa **0,6–0,8 ms** (ein Anhängen). Inhaltsaufträge zeigen keinen messbaren Unterschied. Um weiterzugehen:

- Lassen Sie Lesezugriffe aus, oder erfassen Sie sie mit niedriger Stichprobe (`SampleRate=0.001`) und beschränken Sie
  sie auf `Sections[]`/`Classes[]`.
- Schalten Sie Namen aus, die Sie nicht brauchen: `[AuditEventSettings] Disabled[]=content.node.sort`. Ein
  ausgeschalteter Name kostet ein Nachschlagen in einem Array (0,001 ms).
- Lange Befehle und Worker: Erhöhen Sie `MaxEvents`/`MaxBytes`/`FlushInterval` für weniger, größere Anhänge.
- Beschränken Sie `ImmediateEvents[]` auf Sicherheitsereignisse. Jedes davon ist ein eigenes Anhängen.
- Netzwerk-Sinks (Webhook, syslog über udp/tcp/tls) verlangsamen nie eine Anfrage: Sie werden gespoolt und vom
  Cronjob-Teil gesendet. `Transport=local` schreibt beim Flush in journald (in den gemessenen 0,6 ms enthalten).
- Große Indizes: `KeepDays` kürzer als `ArchiveDays`, `IndexReads=disabled`, `BatchSize` erhöhen für schnelleres
  Aufholen.
- Legen Sie LogDir auf eine lokale Festplatte und ArchiveDir woanders hin.

#### Den Export streamen

Der Download von `audit/export` wird gesendet, während er gelesen wird (`expAuditExportStream`, #393), sodass ein
großer Filter sofort mit dem Herunterladen beginnt und nie die ganze Datei im Speicher hält:

- **Lesen.** Die erste Seite mit 500 Einträgen kommt aus der eigenen Seite der Konsole (mit den noch nicht indizierten
  Einträgen der Live-Dateien obenauf), der Rest nach Position in derselben Reihenfolge: die geordneten IDs des Index in
  Segmenten von 10.000 (`expAuditQuery::orderedIds()`, nach der zuletzt gelesenen ID), dann die vollständigen Zeilen
  jeweils 500 nach ID (`byIds()`). Der Export verwendete bisher OFFSET-Seiten zu 500, und jede ließ die Datenbank
  jeden vollständigen Eintrag des Filters erneut sortieren: unter SQLite 0,5–0,7 s pro Seite, 35 s für 27.000
  Einträge. Die Bytes sind für denselben Filter dieselben wie bisher. MongoDB oder eine Datenbank, die dieses SQL
  ablehnt, behält die OFFSET-Seiten. Der SQL-Abfrage-Cache ist ausgeschaltet, während ein Export liest, damit seine
  Seiten den Cache nicht füllen.
- **Senden.** `Content-Type`, `Content-Disposition: attachment`, `Cache-Control: private, no-store`,
  `X-Content-Type-Options: nosniff`, kein `Content-Length` und `X-Accel-Buffering: no`: Exponential Velocity (0.0.4.49
  und neuer) streamt eine Antwort mit diesem Header aus seinen Pool-Workern, statt sie zu sammeln, und auch ein
  vorgeschalteter Proxy hält sie nicht zurück. Die Ausgabepuffer werden beendet, und das erste Stück (die Kopfzeile
  der CSV) wird sofort gesendet, danach alle 64 KB. Die Sitzung wird zuerst geschlossen (`eZSession::stop()`), sodass
  die anderen Seiten des Benutzers nicht warten.
- **Im Hintergrund.** Oberhalb von `MaxExportRecords`, oder wann immer eine Datei später gewünscht wird, startet „Im
  Hintergrund exportieren“ den Inhaltsauftrag `auditexport` (`expContentJobAuditExport`, content.ini
  `JobTypes[auditexport]`). Er schreibt `var/<site>/jobs/content/<id>.export` (Benutzer der Website, 0660, kein
  Webzugriff) in Stapeln von `BatchSize` × 100 Einträgen und hält den Zustand des Streams und die Länge der Datei in
  seinem Prüfpunkt: Ein nach einem Absturz fortgesetzter Auftrag schneidet ab, was der letzte Stapel nach seinem
  Prüfpunkt geschrieben hat, und schreibt es erneut. Die Seite des Auftrags zeigt den Fortschritt und öffnet die
  Export-Seite, wenn er fertig ist; die Datei wird mit dem Auftrag entfernt (`KeepDays`). Der Download der Datei wird
  genauso gestreamt und mit dem Auftrag als `system.audit.read` aufgezeichnet.

Gemessen auf der Referenzinstallation (Kanal `content`, 26.889 Einträge bis zu einem festen Zeitpunkt, CSV mit 7,7 MB):

| | vorher | nachher |
|---|---|---|
| Apache + PHP-FPM (:443), erstes Byte / gesamt | 0,75 s / 38,3 s | 0,45 s / 3,3 s |
| Velocity (:8080), erstes Byte / gesamt | 504 nach 30,6 s | 0,4–1,3 s / 3,4–4,1 s |
| nur Lesen und Formatieren (CLI), Zeit / Speicherhöchstwert | 36,4 s / 130 MB | 2,9 s / 22 MB |
| Hintergrundauftrag (6 Stapel zu 5.000) | – | 4 s, Höchstwert 18,9 MB; 7–9 s vom Klick bis zur Datei auf der Export-Seite |

Die Ausgabe ist byte-genau dieselbe wie bisher für eine CSV mit 528 Einträgen (Kanal `ai`, mit den Spalten seiner
Quelle), mit 4.390 Einträgen als CSV, JSON Lines und JSON und mit den 26.889 Einträgen; ebenso die Hintergrunddatei
desselben Filters.

### 4.12 Datenschutz und DSGVO-Aufgaben

(Dies beschreibt, was das Audit tut. Es ist keine Rechtsberatung. Dokumentieren Sie es in Ihrem eigenen Verzeichnis
von Verarbeitungstätigkeiten.)

- **Rechtsgrundlage**: berechtigtes Interesse an der Sicherheit und Nachvollziehbarkeit des Systems (Art. 6 Abs. 1
  lit. f) und eine rechtliche Verpflichtung, wo ein Gesetz die Protokollierung von Zugriffen verlangt (Art. 6 Abs. 1
  lit. c). Art. 32 nennt die Protokollierung als Sicherheitsmaßnahme.
- **Datenminimierung als Standard**: Adressen auf /24 gekürzt (IPv6 /48), Sitzungen gehasht (die Sitzungs-ID wird nie
  aufgezeichnet), User-Agents auf die Familie von Browser und Betriebssystem reduziert, E-Mail-Adressen und versuchte
  Logins unbekannter Benutzer gehasht, keine Inhaltswerte, nie Passwörter, Hashes, Tokens, Reset-Schlüssel oder
  Geheimnisse, keine POST-Bodies, Lesezugriffe aus.
- **Strenger**: pro Feld `full | truncate | hash | off` in `[AuditPrivacySettings] Field[]`, `BeforeAfter=keys` und
  kürzere Aufbewahrung. Siehe die [Konfiguration für strengen Datenschutz](#64-strenger-datenschutz). Ein Hash ist
  ein HMAC mit Schlüssel, daher wird dieselbe Person weiterhin zusammengefasst (Alarme nach Adresse funktionieren
  weiter), lässt sich aber nicht zurücklesen.
- **Aufbewahrung**: 90 Tage live und 2 Jahre archiviert, pro Kanal, automatisch entfernt, und jedes Entfernen
  aufgezeichnet.
- **Pseudonymisierung**: Der Index ersetzt Login, Adresse, User-Agent und personenbezogene Felder nach
  `PseudonymiseAfterDays` durch ihre gehashte Form (täglich, aufgezeichnet als `system.audit.pseudonymise`; Vorschau
  mit `exp:audit pseudonymise --dry-run`). Die Dateien werden nicht umgeschrieben (das würde die Kette brechen). Die
  vollständigen Werte verbleiben nur in den Archiven, lesbar mit `audit/manage`. Eine Website, die sie nicht einmal
  dort behalten darf, verwendet von Anfang an `hash`.
- **Auskunftsersuchen (Art. 15)**: jeder Eintrag von oder über einen Benutzer, auch die pseudonymisierten:

  ```
  $ ./console exp:audit export --subject-user=14 --format=jsonl --out=var/tmp/audit-stage6/subject-14.jsonl --allow-root-user
  3625 record(s) exported as jsonl to var/tmp/audit-stage6/subject-14.jsonl (sha256 b82831ef24eeb594b8da40b21498fdefc0ba7f3368c3668997ec042cfa7ad283)
  ```

  Prüfen Sie die Datei, bevor Sie sie herausgeben: Sie enthält auch Einträge über Aktionen anderer Personen an diesem
  Benutzer.
- **Löschung (Art. 17)**: Audit-Einträge werden nach Art. 17 Abs. 3 lit. b und e für ihre Aufbewahrungsdauer
  aufbewahrt. Einen zu löschen würde die Kette brechen. Antworten Sie mit der Aufbewahrungsdauer und der bereits
  angewendeten Pseudonymisierung.

### 4.13 Wenn das Audit nicht schreiben kann

`[AuditSettings] OnWriteFailure` entscheidet, was mit einer Aktion geschieht, deren Eintrag nicht geschrieben werden
kann (volle Festplatte, Berechtigungen, ein fehlender Mount):

- **`continue`** (der ausgelieferte Standard): Die Aktion wird fortgesetzt. error.log erhält den Fehler und am Ende der
  Anfrage eine Zeile `AUDIT-UNWRITTEN <channel> <record>` pro Eintrag, sodass nichts stillschweigend verloren geht.
- **`refuse`**: Die Aktionen der Art, die sofort geschrieben wird (`[AuditBufferSettings] ImmediateEvents[]`:
  `access.*`, `system.audit.*`, `system.setting.write`), werden abgewiesen, solange ihr Kanal nicht geschrieben werden
  kann, sodass nichts Sicherheitsrelevantes unaufgezeichnet geschieht. Jede Abweisung ist eine Zeile `AUDIT-REFUSED`
  in error.log, mit dem Grund.

Was `refuse` absichert, und nur das (`expAuditGuard`):

| Aktion | Geprüftes Ereignis | Abgewiesen mit |
|---|---|---|
| ein POST eines angemeldeten Benutzers an eine View aus `[AuditReadSettings] AlwaysModules[]` (setup, role, user, audit, settings): Änderungen an Rollen und Richtlinien, Benutzerverwaltung, Setup-Formulare, „Jetzt prüfen“ des Audits | der Kanal access (`access.view.sensitive`) und für setup/settings der Kanal system (`system.setting.write`), für audit `system.audit.read` | HTTP 503 mit der Seite `audit/refused.tpl` (sie nennt den Kanal, nie einen Pfad); die View läuft nicht |
| ein INI-Schreiben über `expIniEditor` (`exp:ini`, die Debug-Leiste, die Einstellungsformulare) | `system.setting.write` | `expIniException` "Refused: …", nichts geschrieben |
| `exp:audit rotate, archive, restore, purge, reindex, pseudonymise, import, export, checkpoint, key rotate` | sein eigener Name `system.audit.*` | die Meldung und Exit-Code 2; `--dry-run` wird nie abgewiesen |

Nie abgewiesen, damit die Website benutzbar bleibt: GET-Anfragen, alles, was ein anonymer Besucher tut, Seiten,
Inhaltsbearbeitung, die Views in `[AuditSettings] RefuseExemptViews[]` (`user/login`, `user/logout`: Benutzer können
sich weiterhin an- und abmelden; ihre Einträge gehen in error.log) sowie das Lesen und Diagnostizieren des Audits
(`exp:audit status`, `channels`, `tail`, `search`, `verify`, die GET-Views der Konsole).

„Kann nicht geschrieben werden“ wird vor der Aktion durch eine Probe des Kanals des Ereignisses entschieden, die
nichts schreibt (das Verzeichnis existiert oder kann angelegt werden und ist beschreibbar, die Sperre des Kanals lässt
sich öffnen, an die neueste Datei kann angehängt werden, mehr als 1 MB ist frei), und durch das eigene letzte Schreiben
der Anfrage in diesen Kanal. Bei ausgeschaltetem Audit wird nichts abgewiesen (es gibt nichts aufzuzeichnen). Um
`refuse` zu verlassen, während das Audit nicht schreiben kann, korrigieren Sie LogDir oder bearbeiten
`settings/override/audit.ini.append.php` von Hand: Ein Schreiben mit `exp:ini` würde selbst als unaufgezeichnet
abgewiesen.

Gezeigt, ohne die Live-Einstellungen anzutasten, mit dem Audit auf ein Log-Verzeichnis unter einer einfachen Datei
gerichtet (`expAuditConfig::setOverride()` in einem Prozess):

```
OnWriteFailure=continue
  system.setting.write   allowed
  access.role.assign     allowed
  system.audit.archive   allowed
  content.node.move      allowed
OnWriteFailure=refuse
  system.setting.write   REFUSED (system: the directory var/tmp/audit-refuse-demo/blocker/log does not exist and cannot be created)
  access.role.assign     REFUSED (access: the directory var/tmp/audit-refuse-demo/blocker/log does not exist and cannot be created)
  system.audit.archive   REFUSED (system: the directory var/tmp/audit-refuse-demo/blocker/log does not exist and cannot be created)
  content.node.move      allowed
```

und in error.log:

```
AUDIT-REFUSED system.audit.archive (OnWriteFailure=refuse): the audit channel system cannot be written: the directory var/tmp/audit-refuse-demo/blocker/log does not exist and cannot be created
```

`content.node.move` wird gepuffert, nicht sofort geschrieben, daher wird es nie abgewiesen. Die Tests
(`expAuditFilterGuardReauthTest` AG-06 bis AG-08) belegen die Absicherung, das abgewiesene Schreiben von Einstellungen
und den abgewiesenen POST in der Administration auf dieselbe Weise.

---

## 5. Interna

### 5.1 Ereignisfluss

```
 call site (kernel hook, extension, eZAudit::writeAudit, ezpEvent bridge, settings write)
   │  expAuditHook::emit( name, closure )  ──► off?  return (one array lookup, the closure never runs)
   ▼
 expAudit::event()
   │  expAuditConfig::get()          settings snapshot per request (keyed by REQUEST_TIME_FLOAT)
   │  expAuditTaxonomy::decide()     on? sampled? channel, severity, immediate? (compiled once per settings hash)
   │  parent / depth / child limits
   │  build(): request and actor context, ULID id, time ──► expAuditPrivacy::apply()   (privacy before anything is kept)
   ▼
 immediate?  ── yes ──► write now
   │ no
   ▼
 expAuditBuffer (per channel) ── full (MaxEvents/MaxBytes) or FlushInterval (CLI) ──► flush early (+ system.audit.overflow)
   │  end of request: eZExecution cleanup handler → expAudit::flushFinal()
   │  fatal error: flushOnFatal() (records system.error.fatal); last resort: shutdown function
   ▼
 expAuditWriter::append( channel, records )
   │  flock <LogDir>/.<channel>.lock, read the head from the file's last line (never from memory),
   │  torn last line? → system.audit.chain.repair; new UTC day? → file.close + new file with file.open;
   │  seq, prev, hash per record (canonical JSON), one fwrite, fflush, unlock
   ▼
 after the records are in the file:
   ├─ expAuditSinkRegistry::dispatch()   syslog local at once; webhook / network syslog / mail → spool
   ├─ expAuditAlertEvaluator::afterWrite()   only rules whose Event matches a written record touch their state
   ├─ key creation events, daily checkpoint (first write of a UTC day)
   ▼
 cronjob part (every run): index ◄── files, spools → sinks, alert windows; daily: rotate, verify, archive, purge,
                           pseudonymise, checkpoint
```

Das Schreiben hängt nie von der Datenbank, dem Netzwerk oder einem Sink ab. Die Datei wird immer zuerst geschrieben,
und jede Kopie folgt ihr.

### 5.2 Der Eintrag

Ein Eintrag ist eine Zeile kanonisches JSON. Felder mit dem Wert null werden weggelassen:

| Feld | Bedeutung |
|---|---|
| `v` | Formatversion (1) |
| `id` | Ereignis-ID: eine ULID, 26 Zeichen, nach Zeit sortierbar |
| `seq` | Position in der Datei: 1, 2, 3 … ohne Lücken (Zeilennummer = seq) |
| `name`, `channel`, `severity`, `verb` | Name in der Taxonomie, geschriebener Kanal, Schweregrad nach RFC 5424, die Aktion |
| `time` | RFC 3339 in UTC mit Millisekunden, wann es geschah |
| `request` | `id` (`r-<ULID>`, zugleich der Header `X-Exp-Request-Id`), `siteaccess`, `method` (`CLI` auf der Kommandozeile), `url` (gekürzt: Werte des Query-Strings und geheime Pfadparameter abgeschnitten), `module`, `engine` (apache, velocity, frankenphp, cli), `host`, `pid`, `ms`, `status` (bei einem Befehl der Exit-Code) |
| `actor` | `user_id`, `login`, `roles`, `session` (`h:` + 16 Hex-Zeichen, nie die ID), `ip` (ein Netz), `ua` (Familie), `impersonator` (der Prozessbenutzer, wenn ein Inhaltsauftrag als sein Besitzer handelt), `cli` (Betriebssystembenutzer, Kommandozeile mit maskierten Geheimnissen) |
| `object`, `target` | `type`, `id` und identifizierende Felder |
| `before`, `after` | die geänderten Werte |
| `result`, `reason`, `error` | success, refused oder failed; warum |
| `parent`, `depth`, `job`, `run` | Korrelation |
| `x` | die Felder einer Erweiterung unter ihrem Namen |
| `imported`, `source` | nur importierte Einträge aus 4.x (ohne `prev`/`hash`) |
| `prev`, `hash` | die Kette |

**Kanonisches JSON** (was der Hash abdeckt): der Eintrag ohne `hash`, Objektschlüssel auf jeder Ebene nach ihren
UTF-8-Bytes sortiert, kein Leerraum, `/` und Nicht-ASCII-Zeichen nicht maskiert, nur Ganzzahlen (Dauern sind ganze
ms, Geldbeträge sind Zeichenketten). Ein leeres Objekt ist `{}`. Die Zeile auf der Festplatte *ist* diese Form mit
`"hash"` an seinem sortierten Platz, daher ist eine Zeile, deren Bytes nicht kanonisch sind, selbst ein Zeichen für
eine Bearbeitung (`noncanonical`).

Ein vollständiger Eintrag von der Referenzinstallation (hier umbrochen; auf der Festplatte eine Zeile; Adresse und
Host ersetzt):

```json
{"actor":{"ip":"203.0.113.0/24","login":"admin","roles":[2],"session":"h:7617a963afc3ca9c","ua":"curl 7","user_id":14},
 "after":{"handler":"standard"},"channel":"access","depth":0,
 "hash":"sha256:949b036fdd6f0c59e0214ca1032d56a7a514a4f6b43f8189214b3774b8cc72ba","id":"01M3ZH49HXBMS8TY7HJCA7NWE0",
 "name":"access.session.login","object":{"id":14,"login":"admin","type":"user"},
 "prev":"sha256:4c3e5dc8417119194c764488576c7c66afe914cea1eb74119465a824edb376bf",
 "request":{"engine":"velocity","host":"web1","id":"r-01M3ZH48ZGGSYK3N7NR3Z2F3JX","method":"POST","module":"user/login",
            "ms":630,"pid":1322013,"siteaccess":"admin","status":200,"url":"/admin/user/login"},
 "result":"success","seq":26,"severity":"info","time":"2026-10-03T00:02:54.653Z","v":1,"verb":"login"}
```

### 5.3 Die Hash-Kette und was die Prüfung findet

- `hash = "sha256:" + hex( SHA-256( canonical( record without hash ) ) )`. `prev` ist der Hash des vorherigen
  Eintrags im selben Kanal, über alle seine Dateien hinweg.
- Die erste Datei eines Kanals beginnt mit dem **Genesis**-Wert
  `sha256( "exponential-audit:" + installation id + ":" + channel )`, sodass sich eine Kette nicht als die einer
  anderen Installation oder eines anderen Kanals ausgeben lässt.
- Jede Datei beginnt mit `system.audit.file.open` (seq 1, mit Nennung der vorherigen Datei, ihrer letzten seq und
  ihres Hashs), und eine geschlossene Datei endet mit `system.audit.file.close`.
- **Prüfpunkte**: Einmal am Tag und bei `exp:audit checkpoint` enthält `system.audit.checkpoint` im Kanal system die
  aktuelle Datei, seq und den Hash jedes Kanals, signiert mit HMAC-SHA-256 und dem aktiven Schlüssel, und geht an die
  Sinks. Eine nachträglich neu berechnete Kette passt nicht mehr zu einem vorher signierten Prüfpunkt.
- **Manifeste**: Jeder archivierte Tag hat ein Manifest (Dateien, Zählungen, erste und letzte seq und Hash, sha256 der
  unkomprimierten und der komprimierten Datei, das Prüfergebnis, `previous_manifest`, `key_id`, `hmac`). Die Manifeste
  eines Kanals sind verkettet, sodass auch das Entfernen eines ganzen archivierten Tages auffällt.

Was `verify` meldet:

| Art | Bedeutung |
|---|---|
| `altered` | der Inhalt der Zeile ergibt nicht ihren Hash: Der Eintrag wurde geändert |
| `noncanonical` | die Zeile ist nicht in kanonischer Form: von Hand oder von einem Werkzeug bearbeitet |
| `link` | `prev` ist nicht der Hash des vorherigen Eintrags: Davor wurde eine Zeile entfernt oder eingefügt |
| `gap` / `reordered` | seq springt vorwärts / geht zurück: Zeilen entfernt / vertauscht |
| `unparseable` | kein JSON |
| `missing_file` | eine Datei zwischen zwei anderen fehlt |
| `truncated` | der Öffnungseintrag der nächsten Datei nennt ein anderes Ende, als die vorherige Datei hat: Zeilen wurden abgeschnitten, oder die vorherige Datei wurde neu geschrieben |
| `no_origin` | die erste Datei beginnt weder mit dem Genesis-Wert noch mit einer archivierten Datei |
| `rewritten` | ein signierter Prüfpunkt hat bei dieser seq einen anderen Hash festgehalten: Die Kette wurde neu berechnet |
| `checkpoint_invalid`, `unknown_key` | ein Prüfpunkt, dessen HMAC sich nicht bestätigen lässt oder dessen Schlüssel nicht in den Einstellungen steht |
| Archive: `archive_sha256`, `hmac_invalid`, `previous_manifest` | ein Archiv geändert, ein Manifest bearbeitet, ein archivierter Tag entfernt |
| `repaired` (kein Bruch) | auf eine abgerissene letzte Zeile folgte `system.audit.chain.repair` |

Der Prüfer macht nach einem Bruch weiter (mit dem eigenen `prev` des Eintrags), sodass ein Bericht jeden beschädigten
Abschnitt auflistet. [9.2](#92-manipulationstest) zeigt jede Art, erzeugt an einer Kopie der echten Dateien der
Referenzinstallation.

### 5.4 Taxonomie und Registries

- Ein Name ist `domain.subject.action[.detail[.detail]]`. Einstellungen verwenden Muster: einen ganzen Namen oder ein
  Präfix, das auf `.*` endet und auch auf das Präfix selbst passt (`content.node.remove.*` passt auf
  `content.node.remove` und `content.node.remove.trash`).
- `decide()`: Ein Name `always` ist immer eingeschaltet. Ein Name `sampled` ist nur mit `Reads=enabled` eingeschaltet
  und wird dann mit `SampleRate` stichprobenartig erfasst. Sonst gewinnt der spezifischste Eintrag aus
  `Enabled[]`/`Disabled[]`, und bei Gleichstand gewinnt `Disabled[]`. Ohne Treffer gilt der Standard der Registry.
  `MinSeverity` verwirft niedrigere Schweregrade. Die Antworten werden einmal pro Hash der Einstellungen kompiliert
  und für die Lebensdauer des Prozesses behalten.
- Kanal: ein exakter Eintrag in `Route[]`, dann der eigene Kanal des Zweigs, dann das spezifischste Muster in
  `Route[]`, dann `DefaultChannel`. Verwendet werden nur Kanäle, die in `Channels[]` aufgeführt sind.
- Die vier Registries, die die RAD-Bestandsaufnahme (Setup › RAD) zählt und prüft:

| Registry | Einstellung | Auf der Referenzinstallation |
|---|---|---|
| `auditbranches` | `[AuditEventSettings] Branches[]` | 0 (keine registriert) |
| `auditsinks` | `[AuditSinkSettings] SinkClasses[]` | syslog, webhook, mail; 0 defekt |
| `auditalertrules` | `[AuditAlertSettings] RuleClasses[]` | threshold, match, schedule; 0 defekt |
| `auditformats` | `[AuditArchiveSettings] FormatHandlers[]` | gzip, bzip2, xz, zstd, zip; 0 defekt |
| (Quellen) | `[AuditSourceSettings] Sources[]` | keine im Kernel; expai registriert `expai` (Kanal `ai`). Mit ihren Problemen in `audit/settings` aufgeführt, nicht von der RAD-Bestandsaufnahme gezählt |

### 5.5 Puffern und Schreiben

Ereignisse werden pro Kanal gesammelt und am Ende der Anfrage mit einem Anhängen pro Kanal geschrieben, über den
Cleanup-Handler von `eZExecution`, den `ezpKernelWeb::shutdown()` und `eZScript::shutdown()` ausführen. Ein
fataler Fehler wird von `flushOnFatal()` aufgefangen, das außerdem `system.error.fatal` aufzeichnet, und eine
Shutdown-Funktion ist der letzte Ausweg. `ImmediateEvents[]` (access.*, system.audit.*, system.setting.write) werden
sofort geschrieben, sodass ein Sicherheitsereignis in der Datei steht, auch wenn die Anfrage danach stirbt. Jenseits
von `MaxEvents`/`MaxBytes` schreibt der Puffer vorzeitig und zeichnet einmal `system.audit.overflow` auf. Befehle,
Cronjobs und Worker der Inhaltsaufträge schreiben außerdem alle `FlushInterval` Sekunden. Ein fehlgeschlagenes
Anhängen behält die Einträge für einen weiteren Versuch am Ende. Schlägt auch der fehl, wird jeder Eintrag zu einer
Zeile `AUDIT-UNWRITTEN <channel> <json>` in error.log, sodass kein Eintrag stillschweigend verloren geht. Der Puffer,
die offenen übergeordneten Ereignisse und die fehlgeschlagenen Einträge gehören dem Prozess, der sie gesammelt hat: Ein
mit `pcntl_fork()` geforktes Kind verwirft seine Kopie davon bei seinem ersten Ereignis, Flush oder Reset und erhält
eine eigene Anfrage-ID, sodass nur der Elternprozess sie schreibt ([Wiederholte Einträge](#wiederholte-einträge)).

### 5.6 Die Index-Tabellen

`expaudit_event`: eine Zeile pro Eintrag (`id`, `channel`, `seq`, `file_name`, `name`, `domain_name`, `severity`
0–7, `time_ms`, `request_id`, `siteaccess`, `engine`, `module_view`, `user_id`, `login`, `ip`, `session_h`, `ua`,
`verb`, `object_type/id/name`, `target_type/id`, `result`, `reason`, `parent_id`, `depth`, `job_id`, `run_id`,
`imported`, `pseudonymised`, `record`, `search_text`), mit Indizes auf Zeit, Name, Benutzer, Objekt, Anfrage,
Auftrag, übergeordnetes Ereignis, Adresse, Ergebnis und Domäne/Schweregrad. `expaudit_cursor`: pro Kanal und Datei der
Byte-Offset, die letzte seq und der Hash, die bereits indiziert sind. `expaudit_file`: Zustand und Prüfung jeder
Datei. Das Schema steht in `share/db_schema.dba` und `kernel/sql/<engine>/`. Oracle und MongoDB erhalten es aus
derselben Definition über `expAuditIndexSchema::install()`. Tabellennamen beginnen mit `exp`, und `relationList()`
der Treiber führt nur `ez*` auf, daher fragt `missingTables()` jede Engine direkt.

### 5.7 Sinks

Die Datei wird immer geschrieben. Sinks sind Kopien, zugestellt, nachdem der Eintrag in der Datei steht, und nur mit
Einträgen gespeist, die die Datenschutzregeln durchlaufen haben.

- **syslog** (`expAuditSyslogSink`): RFC 5424, eine Nachricht pro Eintrag, `MSGID` = Kanal, strukturierte Daten
  `exp@32473` (die Enterprise-Nummer für Dokumentation aus RFC 5612) mit id, name, seq, channel, user, ip, result,
  request und hash, dann der JSON-Eintrag. `Transport=local` schreibt auf den nativen Socket von journald, wo journald
  läuft (Kennung `AppName`, Felder `EXP_AUDIT_ID/NAME/CHANNEL/SEQ/HASH/RESULT/SEVERITY/REQUEST`), sonst nach
  `/dev/log`. `devlog` sendet die RFC-5424-Zeile nach `/dev/log`. `udp` sendet nur Header und strukturierte Daten
  (RFC 5426). `tcp` und `tls` senden den ganzen Eintrag mit Octet Counting (RFC 6587/5425). Die lokale Zustellung
  geschieht beim Flush; Netzwerk-Transporte gehen über den Spool.
- **webhook** (`expAuditWebhookSink`): `POST` JSON `{"v":1,"installation":…,"site":…,"batch":…,"events":[…]}` in
  Stapeln von `BatchSize` oder `BatchSeconds`, mit den Headern `X-Exponential-Timestamp`, `X-Exponential-Batch` und
  `X-Exponential-Signature: sha256=<hex HMAC-SHA-256( SigningSecret, timestamp + "." + body )>`. Empfänger prüfen die
  Signatur, lehnen Zeitstempel ab, die mehr als 300 s abweichen (`expAuditWebhookSink::verify()` tut beides), und
  deduplizieren nach Ereignis-ID (die Zustellung erfolgt mindestens einmal). Wiederholungen: `Retries`-mal, wobei
  `RetryBackoff` jedes Mal verdoppelt wird. Nach der letzten bleibt der Stapel im Spool, und
  `system.audit.sink.failed` wird aufgezeichnet.
- **mail** (`expAuditMailSink`): nur aus dem Cronjob-Teil, über den Mail-Transport des Kernels (oder
  `[AuditSink_mail] Transport=<class>`), an die Empfänger aus [4.5](#45-alarme-und-ihre-empfänger).
- **Spools**: `<LogDir>/spool/<sink>.jsonl` unter `flock()`, zugestellt vom Cronjob-Teil und von
  `exp:audit sinks flush`. Kritische Einträge werden außerdem direkt nach der Antwort versucht, wo PHP-FPM es erlaubt.

### 5.8 Auswertung der Alarme

Beim Flush, nach dem Schreiben, werden nur Regeln ausgewertet, deren Muster `Event` auf einen geschriebenen Eintrag
passt, sodass eine Anfrage ohne fehlgeschlagene Anmeldungen oder Rollenänderungen nichts zahlt. Regeln `match` und
`schedule` entscheiden sofort. Regeln `threshold` halten ihr Zeitfenster in `<LogDir>/alerts/<rule>.json` (pro Gruppe
die Ereignis-IDs und Zeiten innerhalb des Fensters) unter `flock()`. Der Cronjob-Teil wertet die Einträge seit seinem
Cursor aus, schließt Zeitfenster und prüft die Datei `.state` gegen die Einstellungen: Eine Änderung durch Bearbeiten
einer Datei von Hand erzeugt trotzdem `system.audit.disable` und den Alarm `audit_disabled`. Ein Alarm für (Regel,
Gruppe) löst einmal pro Zeitfenster aus und erneut, wenn sich die Anzahl verdoppelt. Jedes Auslösen ist
`system.audit.alert` (Regel, Gruppe, Anzahl, Zeitfenster, erstes und letztes Ereignis, Sinks, Meldung) mit dem
Schweregrad der Regel.

### 5.9 Velocity

Die Worker von Velocity sind persistent, daher funktionieren einige Dinge anders als unter PHP-FPM:

- `ezpKernelWeb::__construct()` ruft `expAudit::resetRequest()` auf: Was eine vorherige Anfrage hinterlassen hat, wird
  geschrieben, dann werden Puffer, offene übergeordnete Ereignisse, Anfrage-ID, Auftrag, Lauf und gecachte Akteure
  geleert. Ein geändertes `REQUEST_TIME_FLOAT` setzt außerdem die Momentaufnahme der Einstellungen pro Anfrage zurück.
  Der Cleanup-Handler, nicht die Shutdown-Funktion, schreibt jede Anfrage.
- Die `$GLOBALS`-Caches von `eZAudit` aus 4.x gibt es nicht mehr, daher wird eine geänderte audit.ini bei der nächsten
  Anfrage gesehen.
- Jeder Hook-Punkt prüft `class_exists( 'expAuditHook' )`, und Templates rufen den Fetch über einen variablen
  Modulnamen auf. Ein Worker, der gestartet wurde, bevor es die Audit-Klassen gab, zeichnet nichts auf und zeigt
  nichts an, bis `exp:velocity restart` läuft.
- Die Listener der Brücke werden einmal pro Worker angehängt (ein zweites Anhängen ersetzt das erste), sodass ein
  Ereignis nie doppelt aufgezeichnet wird.
- Getestet wurden 1 000 Anfragen in einem Worker: Kein Eintrag trug die ID einer anderen Anfrage.
- Der Download des Exports sendet `X-Accel-Buffering: no`, sodass ein Pool-Worker ihn streamt, während er geschrieben
  wird (Velocity 0.0.4.49 und neuer; unter `streamTimeout`, nicht dem `requestTimeout` von 30 s, das daraus bisher
  einen 504 machte). Die View umschließt den Download und `eZExecution::cleanExit()` nie mit einem `catch`: Unter
  Velocity wirft `cleanExit()` eine Ausnahme, um die Anfrage zu beenden, und eine abgefangene Ausnahme würde die Seite
  an die Datei anhängen.

### 5.10 Fehlerfälle

| Was ausfällt | Was geschieht |
|---|---|
| LogDir ist nicht beschreibbar | `OnWriteFailure=continue` (Standard): Die Anfrage läuft weiter; error.log erhält den Fehler und am Ende eine Zeile `AUDIT-UNWRITTEN` pro Eintrag. `refuse`: Die abgesicherten Aktionen werden vorher abgewiesen ([4.13](#413-wenn-das-audit-nicht-schreiben-kann)) |
| Ein Prozess stirbt mitten im Schreiben | das nächste Anhängen repariert die abgerissene Zeile (`system.audit.chain.repair`); verify meldet `repaired` |
| Die Datenbank ist nicht erreichbar | zur Anfragezeit ändert sich nichts (der Index wird dann nicht geschrieben); der Indexlauf des Cronjob-Teils schlägt fehl und holt später auf |
| Ein Sink ist nicht erreichbar | Einträge warten im Spool; `system.audit.sink.failed` einmal pro Sink und Stunde; zugestellt, wenn er wieder da ist |
| Der Cronjob-Teil hört auf | Dateien werden weiter geschrieben; Index, Spools, Archive, Prüfpunkte warten; das Dashboard warnt nach einer Stunde |
| Die Schlüsseldatei geht verloren | beim nächsten Ereignis werden neue Schlüssel erzeugt (`system.audit.key.create`); Archive und Prüfpunkte der alten Schlüssel melden `unknown_key`, bis das alte `SigningKey[]` wiederhergestellt ist |
| Die Zweig-Klasse einer Erweiterung fehlt | ihre Namen werden weggelassen (nicht aufgezeichnet), die RAD-Bestandsaufnahme und die Ansicht Einstellungen sagen, warum |
| Die Audit-Klassen fehlen (alter Worker) | dieser Prozess zeichnet nichts auf; nichts geht kaputt |
| Audit wird ausgeschaltet | zuerst wird `system.audit.disable` aufgezeichnet; der Alarm `audit_disabled` (emergency) geht an syslog und E-Mail |

### 5.11 Sicherheit

| Wer | Kann | Die Antwort |
|---|---|---|
| Ein Redakteur ohne Audit-Richtlinien | nichts sehen | Richtlinien an jeder Ansicht, jedem Link, Block und Fetch; Abweisungen aufgezeichnet |
| Jemand, der eine offen gelassene angemeldete Sitzung benutzt | Verwaltungsaktionen des Audits ausführen | `ReauthForManage=enabled` fragt zuerst nach dem Passwort; die Versuche werden aufgezeichnet |
| Ein Benutzer mit `audit/read` | die Kanäle lesen, die die Einschränkung erlaubt, und sie exportieren | Lesezugriffe und Exporte werden aufgezeichnet; personenbezogene Felder standardmäßig gekürzt oder gehasht |
| Ein Administrator mit `audit/manage` | Einstellungen ändern, das Audit ausschalten, Schlüssel rotieren | jedes davon wird aufgezeichnet, bevor es wirkt; Alarm `audit_disabled` an syslog und E-Mail; der Eintrag verlässt den Server über die Sinks |
| Code, der als Benutzer des Webservers läuft | das Aufzeichnen stoppen, den Schlüssel lesen, Live-Dateien neu schreiben und die Kette neu berechnen | er kann nicht neu schreiben, was den Server bereits verlassen hat (syslog, Webhook, E-Mail, Prüfpunkte); Archive auf einem Pfad, den er nicht ändern kann; vor der Kompromittierung signierte Prüfpunkte decken das Neuschreiben auf (`rewritten`) |
| root oder der Hosting-Anbieter | alles auf dem Server | außerhalb des Rahmens: Nur anderswo gehaltene Kopien (Sinks) können zeigen, was geschehen ist |
| Jemand im Netzwerk | — | syslog über `tls`, Webhook über HTTPS mit Signaturen |

**Was die Kette beweist**: Die Einträge eines Kanals, von seinem Genesis-Wert oder dem letzten geprüften Prüfpunkt oder
Manifest bis zur geprüften Stelle, sind die, die geschrieben wurden, in dieser Reihenfolge, ohne dass einer entfernt,
eingefügt oder geändert wurde, **es sei denn**, jemand, der die Dateien schreiben konnte, hat die Kette nach dem
letzten signierten Prüfpunkt neu berechnet. Sie beweist nicht, dass jedes Ereignis aufgezeichnet wurde (Code, der das
Audit nie aufruft, hinterlässt keinen Bruch), dass der Inhalt eines Eintrags wahr ist oder wer eine Datei geändert
hat. Der HMAC fügt hinzu, dass nur ein Schlüsselinhaber einen Prüfpunkt oder ein Manifest signiert haben kann. Ein von
einem Angreifer gelesener Schlüssel schwächt, was nach diesem Moment signiert wird, nicht, was vorher signiert und
weggeschickt wurde. Senden Sie Prüfpunkte vom Server weg (syslog oder Webhook), damit das gilt. Siehe die
Konfigurationen [SIEM](#65-weiterleitung-an-ein-siem) und [Compliance](#66-compliance-lange-aufbewahrung).

---

## 6. Referenzkonfigurationen

Jeder Block unten ist ein vollständiger Inhalt für `settings/override/audit.ini.append.php` (die Datei beginnt mit
`<?php /* #?ini charset="utf-8"?` und endet mit `*/ ?>`). Jeder wurde auf der Referenzinstallation von einem Skript
geprüft, das sie aus diesem Handbuch liest. Für jeden baut es ein vorübergehendes INI-Wurzelverzeichnis mit der
ausgelieferten `settings/audit.ini` und dem Block als Append-Datei, lässt eZINI sie zusammenführen und gibt das
Ergebnis dem Audit in einem Wegwerf-Verzeichnis. Dann fragt es jeden Teil, ob er funktionieren kann: die Taxonomie,
die Entscheidungen über Ereignisse, die Tage, die Größe und den Format-Handler jedes Kanals, die Sinks, die die Kanäle
verwenden, die Alarmregeln, die Datenschutzregeln (an einem Beispieleintrag) und die Einstellungen des Index. Die
Live-Einstellungen wurden nicht geändert. Die Ausgabe des Prüfskripts für jede Konfiguration steht darunter.

### 6.1 Kleine Website

```ini
# Reference configuration: small-site
# One server, a handful of editors. The shipped defaults stay; only who is mailed and
# how long archives are kept change. File: settings/override/audit.ini.append.php
[AuditAlertSettings]
Recipients[]
Recipients[]=admin
Recipients[]=role:Administrator

# One year of archives instead of two. The shipped channel blocks set ArchiveDays
# themselves, so it is set per channel ([AuditRotationSettings] would not be read).
[AuditChannel_content]
ArchiveDays=365

[AuditChannel_access]
ArchiveDays=365

[AuditChannel_system]
ArchiveDays=365

[AuditChannel_commerce]
ArchiveDays=365
```

```
== small-site
  audit enabled; LogDir log/audit; ArchiveDir log/audit/archive
  events recorded: 109 of 135 (as shipped)
  channel content   live   90 d, archived  365 d, gzip, max 64M, sinks -
  channel access    live   90 d, archived  365 d, gzip, max 64M, sinks syslog
  channel system    live   90 d, archived  365 d, gzip, max 64M, sinks syslog
  channel commerce  live   90 d, archived  365 d, gzip, max 64M, sinks -
  channel read      live   30 d, archived   90 d, zstd, max 256M, sinks -
  sink syslog   ready (channels access, system)
  sink webhook  not ready: no URL is set ([AuditSink_webhook] URL)
  sink mail     ready
  alert rules: 7, all usable
  index on, keep 730 d, pseudonymise after 90 d; buffer 500 events / 1048576 bytes, flush every 5 s
  PASS loads and every part can work with it
```

Dazu: eine Crontab-Zeile für `runcronjobs.php frequent` und eine Sicherung von settings/override.

### 6.2 Stark ausgelastete Website

```ini
# Reference configuration: busy-site
# An editorial site with many publishes per hour on one or two servers.
# File: settings/override/audit.ini.append.php
[AuditEventSettings]
# "Who published this?" is the question asked most; the shipped default leaves it off.
Enabled[]=content.object.create
Enabled[]=content.object.publish

[AuditBufferSettings]
# Long requests and commands write fewer, larger appends.
MaxEvents=2000
MaxBytes=4M
FlushInterval=10

[AuditChannel_content]
MaxFileSize=256M
ArchiveFormat=zstd

[AuditChannel_system]
MaxFileSize=256M
ArchiveFormat=zstd

[AuditArchiveSettings]
# Another file system: create it first, owned by the site user, mode 0750.
ArchiveDir=/srv/audit-archive/example

[AuditIndexSettings]
BatchSize=10000
# The console searches one year; older years are in the archives.
KeepDays=365

[AlertRule_mass_delete]
# Editors clean up large sections here; 500 nodes in 10 minutes is normal work.
Threshold=2000
```

```
== busy-site
  audit enabled; LogDir log/audit; ArchiveDir /srv/audit-archive/example
  events recorded: 111 of 135 (against the shipped defaults: +content.object.create +content.object.publish)
  channel content   live   90 d, archived  730 d, zstd, max 256M, sinks -
  channel access    live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel system    live   90 d, archived  730 d, zstd, max 256M, sinks syslog
  channel commerce  live   90 d, archived  730 d, gzip, max 64M, sinks -
  channel read      live   30 d, archived   90 d, zstd, max 256M, sinks -
  sink syslog   ready (channels access, system)
  sink webhook  not ready: no URL is set ([AuditSink_webhook] URL)
  sink mail     ready
  alert rules: 7, all usable
  index on, keep 365 d, pseudonymise after 90 d; buffer 2000 events / 4194304 bytes, flush every 10 s
  PASS loads and every part can work with it
```

Bevor Sie sie einschalten: `mkdir -p /srv/audit-archive/example && chown <site user>: /srv/audit-archive/example && chmod
0750 /srv/audit-archive/example`. Führen Sie `exp:audit alerts test mass_delete --replay=<a month ago>` aus, um zu
prüfen, dass die neue Schwelle passt.

### 6.3 Cluster

```ini
# Reference configuration: cluster
# Several web servers behind a load balancer, one database. Every server writes into
# one shared directory, so each channel stays one chain. The file system must support
# POSIX locks across servers (NFSv4, CephFS, GlusterFS); the writer takes flock() on
# every append. settings/override is shared or copied identically to every server.
[AuditSettings]
LogDir=/mnt/exponential-shared/audit

[AuditArchiveSettings]
ArchiveDir=/mnt/exponential-archive/audit

[AuditPrivacySettings]
# The server's host name tells which server answered (request.host).
Field[request.host]=full

[AuditSink_syslog]
# Each server forwards its own records to the central collector.
Transport=tcp
Host=logs.example.com
Port=514

[AuditChannel_content]
Sinks[]=syslog

[AuditChannel_commerce]
Sinks[]=syslog
```

```
== cluster
  audit enabled; LogDir /mnt/exponential-shared/audit; ArchiveDir /mnt/exponential-archive/audit
  events recorded: 109 of 135 (as shipped)
  channel content   live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel access    live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel system    live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel commerce  live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel read      live   30 d, archived   90 d, zstd, max 256M, sinks -
  sink syslog   ready (channels content, access, system, commerce)
  sink webhook  not ready: no URL is set ([AuditSink_webhook] URL)
  sink mail     ready
  alert rules: 7, all usable
  index on, keep 730 d, pseudonymise after 90 d; buffer 500 events / 1048576 bytes, flush every 5 s
  PASS loads and every part can work with it
```

Wie es über Server hinweg funktioniert: Jede Sperrdatei (`.<channel>.lock`, `.keys.lock`, `.cron.lock`,
`.index.lock`) und die tägliche Markierung liegen im gemeinsamen LogDir. Der Kopf der Kette wird bei jedem Anhängen
unter der Sperre aus der Datei gelesen, nie aus dem Speicher. So schreiben beliebig viele Server eine Kette pro Kanal,
und der Cronjob-Teil darf auf allen laufen: Jeweils ein Lauf erledigt die Arbeit, und die täglichen Aufgaben laufen
einmal am Tag für den Cluster. **Erzeugen Sie die Schlüssel einmal, bevor die anderen Server aufzeichnen**
(`./console exp:audit checkpoint` auf einem Server), dann teilen oder kopieren Sie
`settings/override/audit.ini.append.php` auf jeden Server. Zwei Server mit unterschiedlichen Schlüsseln würden
unterschiedliche Genesis-Werte schreiben und bei der Prüfung scheitern. Was auf der Referenzinstallation geprüft
wurde: Die Konfiguration lädt, und jeder Teil akzeptiert sie, und zehn gleichzeitige Schreiber in einen Kanal halten
die Kette intakt (Test B2). Ein Cluster aus mehreren Servern selbst stand für einen Test nicht zur Verfügung.

### 6.4 Strenger Datenschutz

```ini
# Reference configuration: strict-privacy
# A site that must keep as little personal data as possible, also in its archives.
# File: settings/override/audit.ini.append.php
[AuditPrivacySettings]
Field[actor.login]=hash
Field[actor.ip]=hash
Field[actor.ua]=off
Field[actor.cli.os_user]=hash
Field[request.url]=truncate
Field[request.host]=hash
Field[object.name]=truncate
PseudonymiseAfterDays=30

[AuditRecordSettings]
# Which fields changed, not their values.
BeforeAfter=keys

[AuditChannel_content]
LiveDays=30
ArchiveDays=180

[AuditChannel_access]
LiveDays=30
ArchiveDays=180

[AuditChannel_system]
LiveDays=30
ArchiveDays=180

[AuditChannel_commerce]
LiveDays=30
ArchiveDays=180

[AuditIndexSettings]
KeepDays=180
```

```
== strict-privacy
  audit enabled; LogDir log/audit; ArchiveDir log/audit/archive
  events recorded: 109 of 135 (as shipped)
  channel content   live   30 d, archived  180 d, gzip, max 64M, sinks -
  channel access    live   30 d, archived  180 d, gzip, max 64M, sinks syslog
  channel system    live   30 d, archived  180 d, gzip, max 64M, sinks syslog
  channel commerce  live   30 d, archived  180 d, gzip, max 64M, sinks -
  channel read      live   30 d, archived   90 d, zstd, max 256M, sinks -
  sink syslog   ready (channels access, system)
  sink webhook  not ready: no URL is set ([AuditSink_webhook] URL)
  sink mail     ready
  alert rules: 7, all usable
  sample record: login h:a7b9fa3a5c1c2d01, ip h:b1c47ab3fda84e2d, ua (off), url /user/login?redirect=…, host h:50f7904f8d21e9e6, object.name "A very long article title that goes on and on well past sixty-fo…", before {"parent":"sha256:d4735e3a265e16eee03f59718b9b5d03019c07d8b6c51f90da3a666eec13ab35"}
  index on, keep 180 d, pseudonymise after 30 d; buffer 500 events / 1048576 bytes, flush every 5 s
  PASS loads and every part can work with it
```

Die Zeile des Beispieleintrags zeigt, was geschrieben wird: Logins, Adressen und Hosts als Hashes mit Schlüssel, kein
User-Agent, Namen bei 64 Zeichen abgeschnitten und Werte vorher/nachher als ihr sha256. Das Gruppieren funktioniert
weiterhin: Die Brute-Force-Regeln gruppieren nach der gehashten Adresse. Mit den ausgelieferten Standardwerten lautet
derselbe Beispieleintrag `login editor1, ip 203.0.113.0/24, ua Firefox 131 / Linux`.

### 6.5 Weiterleitung an ein SIEM

```ini
# Reference configuration: siem-forwarding
# Every record leaves the server at once to a SIEM: syslog over TLS for the stream,
# a signed webhook for the security events. File: settings/override/audit.ini.append.php
[AuditSink_syslog]
Transport=tls
Host=siem.example.com
Port=6514
Facility=local4
MinSeverity=info

[AuditSink_webhook]
URL=https://siem.example.com/api/ingest/exponential
# A long random secret (openssl rand -hex 32); the receiver checks X-Exponential-Signature.
SigningSecret=3f9a0c12b47d5e6f8a1b2c3d4e5f60718293a4b5c6d7e8f90a1b2c3d4e5f6071
BatchSize=200
MinSeverity=notice
Events[]
Events[]=access.*
Events[]=system.audit.*
Events[]=system.setting.*

[AuditChannel_content]
Sinks[]=syslog

[AuditChannel_access]
Sinks[]=webhook

[AuditChannel_system]
Sinks[]=webhook

[AuditChannel_commerce]
Sinks[]=syslog
```

```
== siem-forwarding
  audit enabled; LogDir log/audit; ArchiveDir log/audit/archive
  events recorded: 109 of 135 (as shipped)
  channel content   live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel access    live   90 d, archived  730 d, gzip, max 64M, sinks syslog, webhook
  channel system    live   90 d, archived  730 d, gzip, max 64M, sinks syslog, webhook
  channel commerce  live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel read      live   30 d, archived   90 d, zstd, max 256M, sinks -
  sink syslog   ready (channels content, access, system, commerce)
  sink webhook  ready (channels access, system)
  sink mail     ready
  alert rules: 7, all usable
  index on, keep 730 d, pseudonymise after 90 d; buffer 500 events / 1048576 bytes, flush every 5 s
  PASS loads and every part can work with it
```

Zeilen `Sinks[]=…` werden an die ausgelieferten Listen angehängt, daher behalten access und system ihren
ausgelieferten Eintrag `syslog`. Ersetzen Sie das Beispielgeheimnis durch Ihr eigenes (exp:ini maskiert es, und
settings/override wird nie committet). Dann senden `exp:audit sinks test syslog` und `exp:audit sinks test webhook` je
einen Testeintrag, und der Empfänger muss mit 2xx antworten. Mit TLS prüft PHP das Zertifikat des Collectors gegen den
CA-Speicher des Systems. Die Prüfung des Empfängers in PHP ist `expAuditWebhookSink::verify( $body, $headers, $secret )`.

### 6.6 Compliance: lange Aufbewahrung

```ini
# Reference configuration: compliance-long-retention
# Ten years of evidence, kept compressed on write-once storage, with the daily signed
# checkpoints sent off the server. File: settings/override/audit.ini.append.php
[AuditEventSettings]
Enabled[]=content.object.create
Enabled[]=content.object.publish
Enabled[]=commerce.order.create
Enabled[]=commerce.order.status
Enabled[]=commerce.payment.approve
Enabled[]=data.export.pdf
Enabled[]=data.infocollection.view

[AuditChannel_content]
ArchiveDays=3650
ArchiveFormat=xz

[AuditChannel_access]
ArchiveDays=3650
ArchiveFormat=xz

[AuditChannel_system]
ArchiveDays=3650
ArchiveFormat=xz
Sinks[]=webhook

[AuditChannel_commerce]
ArchiveDays=3650
ArchiveFormat=xz

[AuditArchiveSettings]
# A mount the web server can add files to but not change (WORM, or a backup target).
ArchiveDir=/srv/worm/exponential-audit

[AuditIndexSettings]
# Two years searchable in the console; earlier years: exp:audit restore, then read.
KeepDays=730

[AuditSink_webhook]
URL=https://evidence.example.com/exponential/checkpoints
SigningSecret=9b1c0f7e2d4a6c8e0f1a3b5c7d9e1f2a4b6c8d0e2f4a6b8c0d2e4f6a8b0c2d4e
MinSeverity=info
Events[]
Events[]=system.audit.checkpoint
Events[]=system.audit.chain.broken
Events[]=system.audit.purge
Events[]=system.audit.key.rotate
```

```
== compliance-long-retention
  audit enabled; LogDir log/audit; ArchiveDir /srv/worm/exponential-audit
  events recorded: 116 of 135 (against the shipped defaults: +content.object.create +content.object.publish +commerce.order.create +commerce.order.status +commerce.payment.approve +data.export.pdf +data.infocollection.view)
  channel content   live   90 d, archived 3650 d, xz, max 64M, sinks -
  channel access    live   90 d, archived 3650 d, xz, max 64M, sinks syslog
  channel system    live   90 d, archived 3650 d, xz, max 64M, sinks syslog, webhook
  channel commerce  live   90 d, archived 3650 d, xz, max 64M, sinks -
  channel read      live   30 d, archived   90 d, zstd, max 256M, sinks -
  sink syslog   ready (channels access, system)
  sink webhook  ready (channels system)
  sink mail     ready
  alert rules: 7, all usable
  index on, keep 730 d, pseudonymise after 90 d; buffer 500 events / 1048576 bytes, flush every 5 s
  PASS loads and every part can work with it
```

Behalten Sie jedes `SigningKey[]` so lange wie die Archive, die damit signiert wurden (die Rotation fügt Schlüssel
hinzu und entfernt nie welche). Bewahren Sie die Sicherung der Einstellungen genauso lange auf. Führen Sie monatlich
`exp:audit verify --archives` aus und bewahren Sie die Ausgabe auf.

---

## 7. Referenz der Einstellungen

`settings/audit.ini` ist die vollständige, kommentierte Referenz: jede Variable mit ihrem Standardwert und den
erlaubten Werten. Dieser Abschnitt fasst sie zusammen. Ändern Sie Werte in einem Override, nie in dieser Datei
([4.9](#49-einstellungen-ändern)).

| Block | Variable (Standard) | Bedeutung |
|---|---|---|
| `[AuditSettings]` | `Audit` (enabled) | ein oder aus; das Ausschalten wird zuerst aufgezeichnet |
| | `LogDir` (log/audit) | Live-Dateien, relativ zum var-Verzeichnis oder absolut |
| | `AuditFileNames[]` | die Dateinamen von 4.x, als Aliase für Filter `(legacy_file)` und den Import |
| | `OnWriteFailure` (continue) | `continue`: Ein fehlgeschlagenes Schreiben hält die Anfrage nie auf; `refuse`: Die Aktionen aus ImmediateEvents[] werden abgewiesen, solange ihr Kanal nicht geschrieben werden kann ([4.13](#413-wenn-das-audit-nicht-schreiben-kann)) |
| | `RefuseExemptViews[]` (user/login, user/logout) | POSTs, die `refuse` nie abweist |
| `[AuditEventSettings]` | `Enabled[]`, `Disabled[]` | Muster; das spezifischste gewinnt, bei Gleichstand `Disabled[]` |
| | `Branches[<ext>]` | Zweige der Taxonomie von Erweiterungen |
| | `MinSeverity` (info) | niedrigere Schweregrade werden nicht aufgezeichnet |
| `[AuditSourceSettings]` | `Sources[<ext>]` | die Quellen von Erweiterungen in der Konsole: Panel, Links, Ereignisfelder, Diagramme ([3.5](#35-die-entwickler-api), „Die Quelle einer Erweiterung in der Konsole“) |
| `[AuditChannelSettings]` | `Channels[]`, `Route[<pattern>]`, `DefaultChannel` (system) | Kanäle und Routing |
| `[AuditChannel_<c>]` | `LiveDays`, `ArchiveDays`, `MaxFileSize`, `ArchiveFormat`, `Sinks[]` | pro Kanal; die ausgelieferten Werte siehe [4.3](#43-aufbewahrung) |
| `[AuditRecordSettings]` | `BeforeAfter` (enabled \| keys \| disabled), `MaxValueLength` (512), `ChildDepth` (3), `MaxChildren` (10000), `RequestContext`, `RequestIdHeader` (X-Exp-Request-Id), `TrustedRequestIdHeader` (leer; nur von Loopback vertraut), `MaxLineBytes` (262144) | was ein Eintrag enthält |
| `[AuditPrivacySettings]` | `Field[<field>]` (full \| truncate \| hash \| off), `IPv4Prefix` (24), `IPv6Prefix` (48), `SecretPathViews[]`, `NeverRecord[]`, `PseudonymiseAfterDays` (90) | Datenschutz; `actor.session` nimmt nur hash oder off |
| `[AuditBufferSettings]` | `Buffering`, `ImmediateEvents[]`, `MaxEvents` (500), `MaxBytes` (1M), `FlushInterval` (5) | [5.5](#55-puffern-und-schreiben) |
| `[AuditChainSettings]` | `Algorithm` (sha256), `Checkpoints` (enabled) | die Kette ist immer eingeschaltet |
| `[AuditKeySettings]` | `GenerateKeys` (enabled); erzeugt: `InstallationID`, `ActiveSigningKey`, `SigningKey[]`, `PseudonymKey` | [4.6](#46-schlüssel) |
| `[AuditReadSettings]` | `Reads` (disabled), `SampleRate` (0.01), `Sections[]`, `Classes[]`, `AlwaysModules[]` (setup, role, user, audit, settings) | stichprobenartig erfasste Lesezugriffe; Views aus `AlwaysModules[]` sind für angemeldete Benutzer immer `access.view.sensitive` |
| `[AuditSinkSettings]` | `SinkClasses[]`, `SpoolDir` (log/audit/spool) | [5.7](#57-sinks) |
| `[AuditSink_syslog]` | `Transport` (local), `Host`, `Port` (514), `Facility` (authpriv), `AppName` (exponential), `Events[]`, `MinSeverity` (info) | |
| `[AuditSink_webhook]` | `URL`, `SigningSecret`, `BatchSize` (100), `BatchSeconds` (10), `Timeout` (5), `Retries` (5), `RetryBackoff` (30), `Events[]`, `MinSeverity` (notice) | |
| `[AuditSink_mail]` | `Receivers[]` (älterer Name der Standardempfänger), `MinSeverity` (critical), `Events[]`, `Throttle` (900), `Transport` | |
| `[AuditAlertSettings]` | `Alerts`, `EvaluateIn[]` (flush, cronjob), `Rules[]`, `RuleClasses[]`, `BusinessDays` (1-5), `BusinessHours` (7-19), `Recipients[]` | [4.5](#45-alarme-und-ihre-empfänger) |
| `[AlertRecipients_<name>]` | `Addresses[]`, `Recipients[]` | benannte Empfängergruppen |
| `[AlertRule_<name>]` | `Class`, `Event`, `Threshold`, `Window`, `GroupBy`, `Severity`, `Sinks[]`, `Recipients[]`, `CountChildren`, `Policies[]`, `OutOfHours` | die sieben ausgelieferten Regeln |
| `[AuditRotationSettings]` | Standardwerte für die Kanalblöcke; `VerifyBeforeArchive`, `VerifyAfterArchive`, `RotateAfter` (00:15) | |
| `[AuditArchiveSettings]` | `ArchiveDir`, `FormatHandlers[]`, `Level[]`, `FileMode` (0440), `DirMode` (0750) | |
| `[AuditIndexSettings]` | `Index`, `BatchSize` (2000), `IndexReads` (disabled), `FullText`, `KeepDays` (730) | |
| `[AuditConsoleSettings]` | `PageSize` (50), `MaxExportRecords` (100000), `MaxBackgroundExportRecords` (1000000), `ReauthForManage` (disabled), `ReauthMinutes` (10) | das Passwort erneut vor Verwaltungsaktionen ([3.1](#31-wer-was-sehen-darf-die-richtlinien)); der größte Download und Hintergrundexport ([Den Export streamen](#den-export-streamen)) |
| `[AuditCompatSettings]` | `Map[]`, `UnmappedAsLegacy`, `LegacyFiles` (disabled: zusätzlich die Textdateien von 4.x schreiben) | |
| `[AuditBridgeSettings]` | `Bridge[<ezpEvent>]=<name>` | [3.5](#35-die-entwickler-api) |

Verwandte Einstellungen an anderer Stelle: `cronjob.ini [CronjobPart-audit]` und die Gruppe `frequent`;
`menu.ini [Topmenu_audit]`; `dashboard.ini [DashboardBlock_audit]`; `admininterface.ini [AdditionalTab_audit]`;
`module.ini ModuleList[]=audit`.

---

## 8. Referenz der Ereignisse

Erzeugt aus `expAuditTaxonomy::registry()` und den auf der Referenzinstallation wirksamen Einstellungen durch ein
Generator-Skript. **Ausgelöst in** stammt aus dem Code: die Aufrufstellen, die den Namen auslösen. **Hier ein** ist,
was `decide()` mit den Einstellungen der Referenzinstallation antwortet. **Geschrieben** sagt, ob das Ereignis sofort
oder mit dem Puffer der Anfrage angehängt wird. Die beiden beschreibenden Spalten werden vom Generator mit ihren Namen
gehalten (hier übersetzt). Spalten: Schw. = Schweregrad; Standard = ausgeliefert (`always` lässt sich nicht
ausschalten; `sampled` = ein Lesezugriff, nur mit `Reads=enabled` aufgezeichnet). Die Werte in den Spalten Standard,
Hier ein (yes/no) und Geschrieben (immediate/buffered) sind die Werte des Generators.

<!-- event-reference:start (generated from expAuditTaxonomy::registry() and the settings in effect on 2026-10-03; do not edit by hand) -->
148 Namen (44 content, 40 access, 43 system, 12 commerce, 9 data); ausgelieferter Standard: 99 on, 23 off, 23 always,
3 sampled.

| Ereignis | Wird ausgelöst, wenn | Zeichnet auf (vorher → nachher; nie) | Ausgelöst in | Verb | Schw. | Standard | Hier ein | Kan. | Geschrieben |
|---|---|---|---|---|---|---|---|---|---|
| `content.object.create` | die erste Version eines neuen Objekts veröffentlicht wird | – → Klasse, Sprachen, Sektion, Besitzer (nie Attributwerte) | `content/ezcontentoperationcollection.php:auditPublished`, `views/content/restore.php:run` | publish | info | off | no | content | buffered |
| `content.object.publish` | eine spätere Version veröffentlicht wird | Version, Sprachen, geändert → Version, Sprachen, geändert, Kennungen der geänderten Attribute (nie Attributwerte) | `content/ezcontentoperationcollection.php:auditPublished`, `views/content/restore.php:run` | publish | info | off | no | content | buffered |
| `content.object.translate` | eine veröffentlichte Version eine Sprache hinzufügt, die die vorherige nicht hatte | Sprachen → Sprachen | `content/ezcontentoperationcollection.php:auditPublished`, `views/content/restore.php:run` | translate | info | off | no | content | buffered |
| `content.object.translation.remove` | eine Übersetzung aus einem Objekt entfernt wird | Sprachen → Sprachen | `content/ezcontentoperationcollection.php:removeTranslation` | remove | notice | on | yes | content | buffered |
| `content.version.remove` | archivierte oder Entwurfsversionen von einem Redakteur entfernt werden | Status, Sprache → – | `views/content/history.php:run`, `views/content/removeeditversion.php:run` | remove | notice | on | yes | content | buffered |
| `content.node.move` | ein Ort einen neuen Elternknoten erhält `[content-move]` | Eltern, Pfad → Eltern, Pfad | `ezcontentobjecttreenode.php:move` | move | info | on | yes | content | buffered |
| `content.node.copy` | ein Knoten oder Teilbaum kopiert wird | – → neue Knoten-ID, neue Objekt-ID | `contentjob/expcontentjobcopysubtree.php:copyNode`, `ezcontentobjecttreenodeoperations.php:auditSubtreeCopy`, `views/content/copy.php:copyObject` | copy | info | off | no | content | buffered |
| `content.node.add` | einem Objekt ein Ort hinzugefügt wird, ein Eintrag pro neuem Ort `[location-assign]` | Orte → Orte, der neue Knoten und sein Elternknoten, Hauptknoten (der neue Knoten ist das Ziel) | `content/ezcontentoperationcollection.php:addAssignment`, `content/ezcontentoperationcollection.php:auditLocationsAdded` | add | info | off | no | content | buffered |
| `content.node.remove` | ein Ort entfernt wird, das Objekt bleibt `[location-remove]` | Eltern, Pfad, Objekt-ID, Name, main (aus dem Fenster Orte) → – | `ezcontentobjecttreenode.php:removeSubtrees`, `ezcontentobjecttreenode.php:removeNodeFromTree`, `content/ezcontentoperationcollection.php:removeNodes` | remove | notice | on | yes | content | buffered |
| `content.node.remove.trash` | ein Objekt in den Papierkorb geht `[content-delete]` | Eltern, Pfad, Objekt-ID, Name, Klasse → – | `ezcontentobject.php:removeThis`, `ezcontentobjecttreenode.php:removeSubtrees`, `ezcontentobjecttreenode.php:removeNodeFromTree`, `ezcontentobjecttreenode.php:auditRemovalData` | remove | notice | on | yes | content | buffered |
| `content.object.remove` | ein Objekt endgültig entfernt wird, nicht über den Papierkorb `[content-delete]` | Name, Klasse, Besitzer, Orte → – | `ezcontentobjecttreenode.php:removeSubtrees`, `ezcontentobjecttreenode.php:removeNodeFromTree`, `ezcontentobjecttreenode.php:auditRemovalData` | remove | notice | on | yes | content | buffered |
| `content.object.purge` | ein Objekt bereinigt wird (Papierkorb geleert, Versionen bereinigt) `[content-delete]` | Name, Klasse → – | `ezcontentobject.php:purge`, `ezcontentobjecttreenode.php:removeNodeFromTree` | purge | notice | on | yes | content | buffered |
| `content.object.restore` | ein Objekt aus dem Papierkorb wiederhergestellt wird | – → Knoten, Eltern | `views/content/restore.php:run` | restore | info | on | yes | content | buffered |
| `content.trash.empty` | der Papierkorb geleert oder eine Auswahl bereinigt wird | Anzahl → Anzahl | `services/trash.php:audited` | purge | notice | on | yes | content | buffered |
| `content.file.alias.regenerate` | die Aliase der Version eines Bildfelds aus seinem Original neu erzeugt werden (Inhaltsprüfungen, fehlende Dateien) | – → Feld, Version, Sprache, Problem, Aliase | `classes/contentcheck/expcontentcheckfilefix.php:audit` | change | info | on | yes | content | buffered |
| `content.file.restore` | eine fehlende Datei aus einer anderen Version desselben Felds zurückkopiert wird | Quellversion, Quellpfad → Feld, Version, Sprache, Problem | `classes/contentcheck/expcontentcheckfilefix.php:audit` | restore | notice | on | yes | content | buffered |
| `content.file.replace` | eine fehlende Datei unter ihrem erwarteten Pfad erneut hochgeladen wird | – → Feld, Version, Sprache, Problem | `classes/contentcheck/expcontentcheckfilefix.php:audit` | change | notice | on | yes | content | buffered |
| `content.file.reference.remove` | eine Feldversion, die eine fehlende Datei nennt, geleert wird (nur die Datenbank) | – → Feld, Version, Sprache, Problem | `classes/contentcheck/expcontentcheckfilefix.php:audit` | remove | notice | on | yes | content | buffered |
| `content.file.review.mark` | eine Feldversion mit einer fehlenden Datei oder einem fehlenden Alias zur Überprüfung markiert wird | – → Feld, Version, Sprache, Problem | `classes/contentcheck/expcontentcheckfilefix.php:audit` | change | info | on | yes | content | buffered |
| `content.node.hide` | ein Teilbaum verborgen wird `[content-hide]` | Sichtbarkeit → Sichtbarkeit | `contentjob/expcontentjobhidesubtree.php:mainStep`, `ezcontentobjecttreenode.php:hideSubTree` | hide | info | on | yes | content | buffered |
| `content.node.reveal` | ein Teilbaum wieder sichtbar gemacht wird `[content-hide]` | Sichtbarkeit → Sichtbarkeit | `contentjob/expcontentjobhidesubtree.php:mainStep`, `ezcontentobjecttreenode.php:unhideSubTree` | reveal | info | on | yes | content | buffered |
| `content.node.swap` | zwei Orte ihre Objekte tauschen | Objekt-IDs → Objekt-IDs | `content/ezcontentoperationcollection.php:swapNode` | swap | info | on | yes | content | buffered |
| `content.node.section` | einem Teilbaum eine Sektion zugewiesen wird `[section-assign]`; `after.scope` sagt, welche Objekte darunter sich geändert haben (`subtree`, `inherited`, `object`) | Sektion → Sektion | `contentjob/expcontentjobsectionsubtree.php:prepare`, `ezcontentobjecttreenode.php:assignSectionToSubTree`, `ezcontentobjecttreenode.php:removeNodeFromTree` | assign | info | on | yes | content | buffered |
| `content.object.state` | Objektzustände zugewiesen werden `[state-assign]` | Zustände → Zustände | `content/ezcontentoperationcollection.php:updateObjectState` | assign | info | on | yes | content | buffered |
| `content.node.main` | sich der Hauptort ändert: von einem Redakteur gewählt oder der nächste Ort, wenn der Hauptort entfernt wird `[main-node-update]` | Hauptknoten → Hauptknoten, sein Elternknoten, Ursache (location-remove, wenn der Hauptort entfernt wurde) | `content/ezcontentoperationcollection.php:updateMainAssignment`, `content/ezcontentoperationcollection.php:removeNodes`, `content/ezcontentoperationcollection.php:auditMainLocationChanged` | assign | info | off | no | content | buffered |
| `content.node.sort` | sich die Sortierung eines Knotens ändert | Feld, Reihenfolge → Feld, Reihenfolge | `content/ezcontentoperationcollection.php:changeSortOrder` | sort | info | off | no | content | buffered |
| `content.node.priority` | sich Prioritäten von Unterelementen ändern | Prioritäten → Prioritäten | `content/ezcontentoperationcollection.php:updatePriority` | sort | info | off | no | content | buffered |
| `content.object.always_available` | sich das Flag „immer verfügbar“ ändert | Flag → Flag | `content/ezcontentoperationcollection.php:updateAlwaysAvailable` | change | info | off | no | content | buffered |
| `content.object.initial_language` | sich die Ausgangssprache ändert | Sprache → Sprache | `content/ezcontentoperationcollection.php:updateInitialLanguage` | change | info | off | no | content | buffered |
| `content.urlalias.change` | URL-Aliase oder Platzhalter hinzugefügt oder entfernt werden | Alias → Alias | `views/content/urlalias.php:run`, `views/content/urlalias_global.php:run`, `views/content/urlalias_wildcard.php:run` | change | info | off | no | content | buffered |
| `content.class.create` | eine neue Inhaltsklasse gespeichert wird | – → Kennung, Kennungen der Attribute | `views/class/edit.php:storeClass` | create | info | on | yes | content | buffered |
| `content.class.change` | eine Klassendefinition gespeichert wird | Kennung, Attribute (Kennung, Datentyp, Pflicht, durchsuchbar) → dasselbe | `views/class/edit.php:storeClass` | change | info | on | yes | content | buffered |
| `content.class.remove` | Klassen entfernt werden | Kennung, Anzahl der Objekte → – | `views/class/removeclass.php:run`, `views/class/removegroup.php:run` | remove | notice | on | yes | content | buffered |
| `content.class.copy` | eine Klasse kopiert wird | – → Kennung | `views/class/copy.php:run` | copy | info | off | no | content | buffered |
| `content.section.change` | eine Sektion angelegt oder bearbeitet wird | Name, Kennung, Navigationsteil → dasselbe | `views/section/edit.php:run` | change | info | on | yes | content | buffered |
| `content.section.remove` | eine Sektion entfernt wird | Name, Kennung → – | `views/section/list.php:run` | remove | notice | on | yes | content | buffered |
| `content.state.change` | ein Zustand oder eine Zustandsgruppe angelegt oder bearbeitet wird | Kennung, Übersetzungen → dasselbe | `views/state/edit.php:run`, `views/state/group_edit.php:run` | change | info | on | yes | content | buffered |
| `content.state.remove` | Zustandsgruppen oder Zustände entfernt werden | Kennung → – | `views/state/groups.php:run`, `views/state/group.php:run` | remove | notice | on | yes | content | buffered |
| `content.job.create` | ein Inhaltsauftrag angelegt wird | – → Typ, Parameter (nur Knoten-IDs), Modus | `contentjob/expcontentjob.php:create` | create | info | on | yes | content | buffered |
| `content.job.start` | ein Worker einen Auftrag startet oder fortsetzt | Zustand → Zustand, Versuche | `contentjob/expcontentjobworker.php:auditStart` | start | info | on | yes | content | buffered |
| `content.job.finish` | ein Auftrag mit `done` endet | – → erledigte Knoten, ms | `contentjob/expcontentjobworker.php:runLocked` | finish | info | on | yes | content | buffered |
| `content.job.fail` | ein Auftrag mit `failed` endet | – → Fehler, Knoten-ID | `contentjob/expcontentjobworker.php:fail` | fail | warning | on | yes | content | buffered |
| `content.job.cancel` | ein Abbruch angefordert wird oder wirksam wird | Zustand → Zustand | `contentjob/expcontentjob.php:cancel`, `contentjob/expcontentjobworker.php:cancelled`, `contentjob/expcontentjobworker.php:auditEnd` | cancel | info | on | yes | content | buffered |
| `content.job.resume` | ein gestoppter Auftrag fortgesetzt wird | Zustand → Zustand | `contentjob/expcontentjob.php:resume` | resume | info | on | yes | content | buffered |
| `content.node.view` | ein Knoten angesehen wird (Z6) | – (nie die gerenderte Seite) | `views/content/view.php:run` | read | info | sampled | no | read | buffered |
| `content.search.query` | eine Suche ausgeführt wird (Z6) | – → Suchbegriff (auf 64 Zeichen gekürzt), Anzahl der Treffer | `views/content/advancedsearch.php:run`, `views/content/search.php:run` | read | info | sampled | no | read | buffered |
| `content.object.download` | ein Dateiattribut heruntergeladen wird | – | `views/content/download.php:run` | read | info | sampled | no | read | buffered |
| `content.finder.search` | eine Suche im Content Finder ausgeführt wird (contentfinder/search) | – → gefragter Typ, versuchte Typen, Text (auf 128 Zeichen gekürzt), angezeigte Ergebnisse, gefundene, aber nicht angezeigte Ergebnisse | `views/contentfinder/search.php:audit` | read | info | on | yes | content | buffered |
| `content.finder.action` | eine Aktion des Content Finders ausgeführt oder abgewiesen wird (in den Papierkorb verschieben, endgültig löschen, bereinigen, eine Version entfernen); die Entfernungsereignisse des Kernels sind ihre untergeordneten Ereignisse | – → Aktion, Orte, untergeordnete Ereignisse (abgewiesen: Grund) | `contentfinder/expcontentfinderaction.php:execute`, `contentfinder/expcontentfinderaction.php:auditRefusal` | remove | notice | on | yes | content | buffered |
| `content.file.orphan.quarantine` | verwaiste Speicherdateien in die Quarantäne verschoben werden (contentcheck/storageaction, `exp:content-check --quarantine`) | – → Anzahl, Bytes, fehlgeschlagen, Dateien (Pfade unterhalb des Speichers, höchstens 200), via | `contentcheck/expcontentcheckquarantine.php:audit` | move | notice | on | yes | content | buffered |
| `content.file.orphan.restore` | Dateien aus der Quarantäne in den Speicher zurückverschoben werden | – → Anzahl, Bytes, fehlgeschlagen, Dateien, via | `contentcheck/expcontentcheckquarantine.php:audit` | restore | notice | on | yes | content | buffered |
| `content.file.orphan.delete` | Dateien in der Quarantäne gelöscht werden (nach eigener Bestätigung) | – → Anzahl, Bytes, fehlgeschlagen, Dateien, via | `contentcheck/expcontentcheckquarantine.php:audit` | remove | warning | on | yes | content | buffered |
| `content.file.orphan.refused` | eine Aktion auf Speicherdateien wegen einer fehlenden Richtlinie abgewiesen wird | – → Aktion (Grund: policy) | `views/contentcheck/storageaction.php:run` | access | notice | on | yes | content | buffered |
| `content.file.permissions.fix` | der erwartete Besitzer und Modus auf verwendeten Speicherdateien gesetzt werden | – → Anzahl, Bytes, fehlgeschlagen, Dateien, via | `contentcheck/expcontentcheckquarantine.php:audit` | change | notice | on | yes | content | buffered |
| `access.session.login` | sich ein Benutzer anmeldet `[user-login]` | – → Sitzung (gehasht), Handler (standard, ldap, …) (nie das Passwort) | `datatypes/ezuser/ezuser.php:loginSucceeded` | login | info | on | yes | access | at once |
| `access.session.login.failed` | ein Anmeldeversuch fehlschlägt `[user-failed-login]` | – → Versuche, Grund (nie das Passwort; der versuchte Login eines **unbekannten** Benutzers wird immer gehasht, da er oft ein in das falsche Feld eingegebenes Passwort ist) | `datatypes/ezuser/ezuser.php:loginFailed` | login | notice | on | yes | access | at once |
| `access.session.logout` | sich ein Benutzer abmeldet | Sitzung → – | `datatypes/ezuser/ezuser.php:logoutCurrent` | logout | info | on | yes | access | at once |
| `access.session.regenerate` | die Sitzungs-ID erneuert wird | alter Hash → neuer Hash | `ezpEvent session/regenerate (bridge)` | regenerate | info | off | no | access | at once |
| `access.session.expire` | eine Sitzung zerstört oder eingesammelt wird | – → Anzahl | `ezpEvent session/destroy, session/gc (bridge)` | expire | info | off | no | access | at once |
| `access.session.reauth` | ein Benutzer vor einer Aktion von audit/manage das Passwort erneut eingibt (Q9) oder „Identität bestätigen“ beantwortet ([Identität bestätigen](../../anleitungen/identitaet-bestaetigen.md)) | – → Methode, Gruppe, Ansicht, Minuten | `audit/console/expauditreauth.php:confirm`, `reauth/expreauth.php:moduleResult` | reauth | info | on | yes | access | at once |
| `access.session.reauth.failed` | diese erneute Eingabe oder Antwort fehlschlägt | – → Methode, Gruppe, Ansicht, Fehlversuche; Grund credentials, code, passkey_*, method (nie Passwort oder Code) | `audit/console/expauditreauth.php:confirm`, `reauth/expreauth.php:moduleResult` | reauth | notice | on | yes | access | at once |
| `access.session.reauth.ask` | „Identität bestätigen“ vor einer heiklen Aktion gefragt wird | – → Gruppe, Ansicht, Methode | `reauth/expreauth.php:moduleResult` | reauth | info | on | yes | access | at once |
| `access.session.revoke` | die anderen Sitzungen eines Benutzers nach einer Passwortänderung enden (`[PasswordSettings] EndOtherSessions`): vom Datenbank-Session-Handler sofort gelöscht, oder eine Sitzung bei ihrer nächsten Anfrage abgemeldet, weil ihr Passwortstempel veraltet ist | – → Anzahl; Grund password_change oder password_changed | `classes/exppasswordpolicy.php:endOtherSessions`, `classes/exppasswordpolicy.php:endStaleSession` | revoke | notice | on | yes | access | at once |
| `access.session.revoke` (Sitzungsseite) | ein Benutzer meldet unter user/sessions eine Sitzung oder alle anderen Sitzungen ab, oder ein Administrator mit user/sessions meldet unter user/sessions/<id> die Sitzungen eines Benutzers ab (doc/ger-de/funktionen/6.0/benutzersitzungen.md) | – → count, sessions (Zeilen-IDs), scope (one, others, all), by (user, admin), actor_id; Grund user_request oder admin | `user/expusersessiontracker.php:auditRevoke` | revoke | notice | on | yes | access | at once |
| `access.session.new_device` | eine Anmeldung kommt von einer Kombination aus Browser, System und Geräteart, die das Konto innerhalb von `[UserSessionSettings] RetentionDays` nicht benutzt hat; die Mail „Neue Anmeldung“ geht hinaus | – → session, browser, os, device, method | `user/expusersessiontracker.php:newDevice` | new_device | notice | on | yes | access | at once |
| `access.user.lock` | fehlgeschlagene Anmeldungen `[UserSettings] MaxNumberOfFailedLogin` erreichen | Versuche → Versuche, aktiviert | `datatypes/ezuser/ezuser.php:setFailedLoginAttempts` | lock | warning | on | yes | access | at once |
| `access.user.unlock` | der Zähler fehlgeschlagener Anmeldungen von einem Administrator zurückgesetzt wird | Versuche → 0 | `datatypes/ezuser/ezuser.php:setFailedLoginAttempts` | unlock | notice | on | yes | access | at once |
| `access.permission.refused` | eine View eines Moduls durch eine Richtlinie abgewiesen wird | – → die erfragte Richtlinie (Modul/Funktion), die nicht erfüllte Einschränkung | `lib/ezutils/classes/ezmodule.php:handleError` | access | notice | on | yes | access | at once |
| `access.uiswitch.change` | ein angemeldeter Benutzer eine Wahl der Oberfläche der Administration ändert (uiswitch/set: die Anzeigesprache, ...) | Wert, Siteaccess → Wert, Siteaccess; Ziel der Provider | `classes/uiswitch/expuiswitch.php:set` | update | info | on | yes | access | at once |
| `access.token.refused` | ein POST wegen eines fehlenden oder falschen Formular-Tokens abgewiesen wird | post · Modul/View | `ezpkernelweb.php:formTokenRefusalResult` | post | notice | on | yes | access | at once |
| `access.view.sensitive` | eine View von setup, role, user oder audit geöffnet wird (Z6 „always“) | – (nie POST-Bodies) | `ezpkernelweb.php:auditSensitiveView` | read | info | always | yes | access | at once |
| `access.user.create` | ein Benutzerkonto angelegt wird | – → Login, E-Mail (Datenschutzregeln), Gruppen | `datatypes/ezuser/ezuser.php:auditStore` | create | notice | on | yes | access | at once |
| `access.user.activate` | ein Konto über seinen Link aktiviert wird | aktiviert → aktiviert (nie der Aktivierungs-Hash) | `user/ezuseroperationcollection.php:activation` | activate | notice | on | yes | access | at once |
| `access.user.enable` | ein Administrator ein Konto aktiviert oder deaktiviert | aktiviert, max_login → dasselbe | `user/ezuseroperationcollection.php:setSettings` | enable | notice | on | yes | access | at once |
| `access.user.disable` | ein Administrator ein Konto aktiviert oder deaktiviert | aktiviert, max_login → dasselbe | `user/ezuseroperationcollection.php:setSettings` | disable | notice | on | yes | access | at once |
| `access.user.remove` | ein Benutzer entfernt wird | Login, E-Mail (Datenschutzregeln) → – | `datatypes/ezuser/ezuser.php:removeUser` | remove | notice | on | yes | access | at once |
| `access.registration.approve` | ein Administrator eine Registrierung über den Link der E-Mail „Neuer Benutzer registriert“ genehmigt (user/registration): aktiviert das Konto oder nimmt sein Genehmigungselement an | Zustand → Zustand (email, administrator, workflow → active, pending) | `classes/user/expregistrationadminaction.php:approve` | approve | notice | on | yes | access | at once |
| `access.registration.reject` | ein Administrator eine Registrierung über den Link dieser E-Mail ablehnt: lehnt ihr Genehmigungselement ab und entfernt das Konto | Zustand → missing | `classes/user/expregistrationadminaction.php:reject` | reject | notice | on | yes | access | at once |
| `access.registration.refused` | ein Link dieser E-Mail abgewiesen wird: ungültiges, geändertes, abgelaufenes oder benutztes Token, die falsche Aktion, ein Benutzer ohne die Richtlinie, ein Konto, das nicht mehr wartet | – → Grund, Aktion | `classes/user/expregistrationadminaction.php:auditRefused` | access | notice | on | yes | access | at once |
| `access.user.deactivate_move` | ein Konto deaktiviert wird und seine Orte in der Gruppe der deaktivierten Benutzer zusammengeführt werden (Deaktivieren und verschieben) | aktiviert, Haupt-Elternknoten, andere Elternknoten → aktiviert, der Knoten der Gruppe, die entfernten Ortsknoten, ob die Orte gemerkt wurden | `user/expuserdeactivation.php:deactivate` | disable | notice | on | yes | access | at once |
| `access.user.reactivate_move` | ein Konto aktiviert und an seine gemerkten Orte zurückverschoben wird (Reaktivieren und zurückverschieben) | aktiviert, Haupt-Elternknoten, Elternknoten → aktiviert, Haupt-Elternknoten, hinzugefügte Elternknoten, verwendeter Ersatz, Anzahl nicht wiederhergestellt | `user/expuserdeactivation.php:reactivate` | enable | notice | on | yes | access | at once |
| `access.user.deactivated_group.create` | die Gruppe der deaktivierten Benutzer beim ersten Gebrauch oder von `bin/php/ezcreatedeactivatedusergroup.php` angelegt wird | – → Knoten, Objekt, Elternknoten, Remote-ID, Sektion, verborgen | `user/expuserdeactivation.php:createGroup` | create | notice | on | yes | access | at once |
| `access.user.email.change` | sich die E-Mail-Adresse eines Kontos ändert | E-Mail → E-Mail (Datenschutzregeln: standardmäßig gehasht) | `datatypes/ezuser/ezuser.php:auditStore` | change | notice | on | yes | access | at once |
| `access.user.login.change` | sich der Anmeldename ändert | Login → Login | `datatypes/ezuser/ezuser.php:auditStore` | change | notice | on | yes | access | at once |
| `access.user.password.change` | ein Passwort geändert wird `[user-password-change*]` | – (nie das Passwort, der Hash oder sein Typ) | `datatypes/ezuser/ezuser.php:auditStore` | change | notice | on | yes | access | at once |
| `access.user.password.change.failed` | eine Änderung abgewiesen wird (falsches altes Passwort, Regeln; `demo_mode`: ein Administrator, während `EXP_DEMO_MODE=1` gilt, mit dem Pfad) `[user-password-change-self-fail]` | – → Grund | `views/user/password.php:run`, `expdemomode.php:refused` | change | notice | on | yes | access | at once |
| `access.user.password.change_required.set` | ein Konto mit „Passwort bei der nächsten Anmeldung ändern“ markiert wird (Bearbeitungsformular des Kontos, `exp:user-password-change-required set`) | – → Quelle (`edit`, `console`) | `user/exppasswordchangerequired.php:set` | require | notice | on | yes | access | at once |
| `access.user.password.change_required.clear` | die Markierung aufgehoben wird: der Benutzer hat ein neues Passwort gesetzt, oder ein Administrator hat sie abgewählt | – → Quelle (`password`, `edit`, `console`) | `user/exppasswordchangerequired.php:clear` | clear | notice | on | yes | access | at once |
| `access.user.password.change_required.redirect` | ein markiertes Konto statt zu einer Seite zu user/password geschickt oder seine Anmeldung abgewiesen wird (REST 403 `password_change_required`, WebDAV, SOAP, die expservices-Anmeldung) | – → Kanal (`web`, `rest`, `webdav`, `soap`, `expservices`), die angefragte Ansicht | `user/exppasswordchangerequired.php:moduleResult`, `:refusesSignIn` | redirect | info | on | yes | access | at once |
| `access.user.second_factor.refused` | ein richtiges Passwort auf einem Anmeldeweg außerhalb des Anmeldeformulars abgewiesen wird, weil das Konto einen zweiten Schritt braucht ([Zweiter Schritt auf den anderen Anmeldewegen](../../funktionen/6.0/zweiter-schritt-auf-anderen-anmeldewegen.md)) | – → Kanal (`rest`, `webdav`, `soap`, `expservices`), ob ein Code gesendet wurde; Grund `second_factor_required`, `second_factor_invalid`, `second_factor_setup_required` (nie das Passwort oder der Code) | `user/expsigninsecondfactor.php:check` | refuse | notice | on | yes | access | at once |
| `access.user.app_password.create` | ein Benutzer auf der Seite der zweistufigen Anmeldung (user2fa/setup) ein App-Passwort erstellt | – → ID, Name, Kanäle, erstellt, Ablauf (nie das Passwort, sein Hash oder sein Salt) | `user/expsigninsecondfactor.php:audit` (sevenx_authentication_2fa) | create | notice | on | yes | access | at once |
| `access.user.app_password.revoke` | ein App-Passwort von seinem Besitzer widerrufen wird, oder mit allen anderen, wenn der zweite Schritt entfernt oder zurückgesetzt wird | – → ID, Name, Kanäle; Grund `owner`, `second_step_removed`, `second_step_reset` | `user/expsigninsecondfactor.php:audit` (sevenx_authentication_2fa) | revoke | notice | on | yes | access | at once |
| `access.user.app_password.use` | ein App-Passwort über REST, WebDAV oder SOAP anmeldet (höchstens einmal je `[AppPasswords] LastUsedInterval` und App-Passwort) | – → ID, Name, Kanäle, der benutzte Kanal | `user/expsigninsecondfactor.php:audit` (sevenx_authentication_2fa) | use | info | on | yes | access | at once |
| `access.user.password.reset.request` | eine E-Mail zum Zurücksetzen angefordert wird `[user-forgotpassword]` | – → E-Mail gesendet ja/nein (nie der Hash-Schlüssel) | `views/user/forgotpassword.php:run` | request | notice | on | yes | access | at once |
| `access.user.password.reset` | ein Zurücksetzen abgeschlossen wird `[user-forgotpassword]` | – (nie der Hash-Schlüssel, nie das Passwort) | `views/user/forgotpassword.php:run` | reset | notice | on | yes | access | at once |
| `access.user.password.reset.failed` | ein Link zum Zurücksetzen unbekannt oder abgelaufen ist oder die Adresse unbekannt ist `[user-forgotpassword-fail]` | – → Grund `unknown_key`/`expired`/`unknown_email`/`demo_mode` (nie der Schlüssel; eine unbekannte E-Mail-Adresse wird gehasht) | `views/user/forgotpassword.php:run` | reset | notice | on | yes | access | at once |
| `access.role.create` | eine Rolle angelegt wird | – → Name | `views/role/edit.php:auditRoleStored`, `views/role/edit.php:applyRole` | create | notice | on | yes | access | at once |
| `access.role.change` | eine Rolle nach dem Bearbeiten gespeichert wird `[role-change]` | Name, Richtlinien → Name, Richtlinien | `views/role/edit.php:auditRoleStored`, `views/role/edit.php:applyRole` | change | notice | on | yes | access | at once |
| `access.role.remove` | Rollen entfernt werden | Name, Richtlinien, Zuweisungen → – | `ezrole.php:removeThis` | remove | notice | on | yes | access | at once |
| `access.role.copy` | eine Rolle kopiert wird | – → Name | `ezrole.php:copy` | copy | notice | on | yes | access | at once |
| `access.role.assign` | eine Rolle einem Benutzer oder einer Gruppe zugewiesen wird `[role-assign]` | – → Einschränkung (Teilbaum, Sektion) | `ezrole.php:assignToUser`, `ezrole.php:auditAssignment` | assign | notice | on | yes | access | at once |
| `access.role.unassign` | eine Zuweisung entfernt wird | Einschränkung → – | `ezrole.php:removeUserAssignment`, `ezrole.php:removeUserAssignmentByID` | unassign | notice | on | yes | access | at once |
| `access.policy.add` | einer Rolle eine Richtlinie hinzugefügt wird | – → Modul, Funktion, Einschränkungen | `ezrole.php:appendPolicy`, `views/role/edit.php:auditRoleStored` | add | notice | on | yes | access | at once |
| `access.policy.remove` | eine Richtlinie entfernt wird | Modul, Funktion, Einschränkungen → – | `ezpolicy.php:removeThis`, `views/role/edit.php:auditRoleStored` | remove | notice | on | yes | access | at once |
| `access.apikey.create` | ein Benutzer auf apikey/list einen persönlichen API-Schlüssel erzeugt ([API-Schlüssel](../../anleitungen/api-schluessel.md)) | – → Schlüssel-ID, Name, Präfix, Besitzer; Scopes, Ablauf (nie das Geheimnis, sein Hash oder sein Salt) | `apikey/list.php` | create | notice | on | yes | access | at once |
| `access.apikey.revoke` | ein Schlüssel von seinem Besitzer oder auf oauthadmin/keys widerrufen wird | Status → Status, durch (Besitzer oder Administrator) | `private/modules/oauthadmin/keyaction.php`, `apikey/list.php` | revoke | notice | on | yes | access | at once |
| `access.apikey.use` | ein Schlüssel zum ersten Mal verwendet wird | – → erste Verwendung, Scopes | `private/rest/classes/auth/apikey_auth_filter.php:run` | use | info | on | yes | access | at once |
| `access.apikey.use.failed` | eine REST-Anfrage mit einem Schlüssel abgewiesen wird | – → Grund malformed, unknown, secret, revoked, expired, owner, transport, scope, policy, permission, rate_limited; Route und Scope bei einer Abweisung wegen des Scopes (ein unbekannter Schlüssel über das Präfix, das er angibt; nie das Geheimnis) | `private/rest/classes/auth/apikey_auth_filter.php:refuse`, `private/rest/classes/auth/apikey_rest.php:authorize` | use | notice | on | yes | access | at once |
| `access.passkey.add` | ein Benutzer auf user/passkeys einen Passkey hinzufügt ([Passkeys](../../anleitungen/passkeys.md)); abgewiesen: result failed mit dem Grund | – → Passkey-ID, Name, Algorithmus, synchronisiert, AAGUID (nie der Schlüssel oder die Credential-ID) | `passkey/exppasskeyservice.php:register` | add | notice | on | yes | access | at once |
| `access.passkey.rename` | ein Benutzer einen Passkey umbenennt | Name → Passkey-ID, Name | `passkey/exppasskeyservice.php:rename` | rename | info | on | yes | access | at once |
| `access.passkey.remove` | ein Benutzer einen Passkey entfernt | – → Passkey-ID, Name, Algorithmus | `passkey/exppasskeyservice.php:remove` | remove | notice | on | yes | access | at once |
| `access.passkey.use` | ein Passkey anmeldet oder „Identität bestätigen“ beantwortet | – → Passkey-ID, Name, Zweck (login, reauth), Benutzer geprüft | `passkey/exppasskeyservice.php:verifyLogin`, `passkey/exppasskeyservice.php:verifyForUser` | use | info | on | yes | access | at once |
| `access.passkey.use.failed` | ein Passkey abgewiesen wird | – → Zweck; Grund challenge, origin, rp_id, signature, counter, user_verified, unknown, owner, disabled, locked, store ... | `passkey/exppasskeyservice.php:refused` | use | notice | on | yes | access | at once |
| `system.setting.write` | eine INI-Datei geschrieben wird | Wert in dieser Datei und wirksamer Wert → dasselbe; für `expIniEditor` außerdem der Unified Diff aus `diff()` (nie ein Wert, dessen Variable `expIniEditor::isSecret()` erkennt: er wird als `[secret]` geschrieben) | `audit/expaudit.php:settingWrite` | write | notice | on | yes | system | at once |
| `system.setting.undo` | ein Schreiben der Debug-Leiste rückgängig gemacht wird | wie `system.setting.write` | `debugbar/expdebugbarsettings.php:undo` | undo | notice | on | yes | system | buffered |
| `system.extension.change` | ActiveExtensions oder seine Reihenfolge geschrieben wird | Liste → Liste | `ezpactiveextensions.php:write` | change | notice | on | yes | system | buffered |
| `system.cache.clear` | Caches auf Anforderung geleert werden | – → IDs, ms | `ezcache.php:auditCleared` (auch für `bin/php/ezcache.php`, ein Eintrag pro Lauf, über `eZCacheHelper::auditCleared()`) | clear | info | on | yes | system | buffered |
| `system.cronjob.run` | ein Cronjob-Teil läuft (übergeordnet: der Aufruf von runcronjobs) | – → ms, Ergebnis | `ezruncronjobs.php:auditPart` | run | info | on | yes | system | buffered |
| `system.cronjob.fail` | ein Teil eine Ausnahme wirft oder mit einem Code ungleich null endet | – → Fehler | `ezruncronjobs.php:auditPart` | run | warning | on | yes | system | buffered |
| `system.cronjob.pause` | ein Cronjob-Teil oder jeder Teil („all“) unter Setup > Cronjobs pausiert wird | – → pausiert | `expcronjobpause.php:audit` | pause | notice | on | yes | system | buffered |
| `system.cronjob.resume` | ein pausierter Teil oder jeder Teil fortgesetzt wird | – → pausiert | `expcronjobpause.php:audit` | resume | notice | on | yes | system | buffered |
| `system.log.read` | eine Log-Datei unter Setup > Logdateien geöffnet wird (einmal pro Datei und Sitzung pro `logviewer.ini [ViewerAuditSettings] ReadInterval`) oder von `exp:logs` gelesen wird (einmal pro Lauf) | – → Größe, unmaskiert (cli, Dateien) | `expLogViewer::auditRead`, `views/setup/logs.php`, `views/setup/logsfeed.php`, `commands/explogs.php` | read | info | on | yes | system | buffered |
| `system.log.download` | ein Bereich einer Log-Datei heruntergeladen wird | – → Größe, von, bis, unmaskiert | `expLogViewer::audit`, `views/setup/logsaction.php` | download | notice | on | yes | system | buffered |
| `system.log.rotate` | eine Log-Datei sofort rotiert wird | – → Größe, ok, umbenannt (Fehler) | `expLogViewer::audit`, `views/setup/logsaction.php` | rotate | notice | on | yes | system | buffered |
| `system.log.truncate` | eine Log-Datei geleert wird | – → Größe, ok (Fehler) | `expLogViewer::audit`, `views/setup/logsaction.php` | truncate | warning | on | yes | system | buffered |
| `system.log.delete` | eine archivierte Rotation einer Log-Datei entfernt wird | – → Größe, ok (Fehler) | `expLogViewer::audit`, `views/setup/logsaction.php` | delete | notice | on | yes | system | buffered |
| `system.command.run` | ein Befehl läuft (kein Lauf von `exp:velocity watchdog run`, der `system.velocity.watchdog.*` nur aufzeichnet, wenn er handelt; ein Befehl verzichtet darauf mit `eZScript::setAuditCommandRun( false )`) | – → Exit-Code, ms | `ezscript.php:auditCommandRun` (aus `shutdown`) | run | info | on | yes | system | buffered |
| `system.package.install` | ein Paket installiert wird | – → Name, Version, Elemente | `ezpackage.php:install` | install | notice | on | yes | system | buffered |
| `system.package.uninstall` | ein Paket deinstalliert wird | Name, Version → – | `ezpackage.php:uninstall` | uninstall | notice | on | yes | system | buffered |
| `system.package.import` | ein Paketarchiv importiert wird | – → Name, Version, sha256 des Archivs | `ezpackage.php:import` | import | notice | on | yes | system | buffered |
| `system.package.remove` | ein Paket aus seinem Repository entfernt wird (package/list) | Name, Version, Repository, Dateien, Bytes, installiert, Quelle des Installationsprogramms → – | `views/package/list.php:remove` | remove | notice | on | yes | system | buffered |
| `system.install.run` | eine Installation angelegt wird | – → Siteaccesses, Pakete, Datenbank-Engine | `setup/steps/ezstep_create_sites.php:init` | install | notice | on | yes | system | buffered |
| `system.demo.mode` | ein Prozess startet, während der Demomodus eingeschaltet ist (`EXP_DEMO_MODE=1`, [demo-mode.md](demomodus.md)), einmal pro Prozess | – → SAPI, pid | `expdemomode.php:announce` (aus `ezpkernelweb.php:requestInit`, `ezscript.php:initialize`) | mode | warning | always | yes | system | buffered |
| `system.upgrade.run` | eine Upgrade-Prüfung oder ein Upgrade-Skript läuft | – → Ergebnis | `views/setup/systemupgrade.php:run` | run | notice | on | yes | system | buffered |
| `system.velocity.deploy` | `exp:velocity deploy` oder `restart` läuft | – → Schritte, ms, Ergebnis | `expvelocity.php:restart`, `expvelocitydeploy.php:run` | deploy | notice | on | yes | system | buffered |
| `system.velocity.watchdog.start` | der Velocity-Watchdog die Engine gestartet hat (success) oder ein Start fehlgeschlagen ist (failed, warning) | – → Grund (`down`, `restart-stale`), Detail | `expvelocitywatchdog.php:audit` (aus `commands/velocity.php`, `watchdog run`) | start | notice | on | yes | system | buffered |
| `system.velocity.watchdog.backoff` | der Watchdog eine gestoppte Engine nach drei fehlgeschlagenen Starts in Folge in Ruhe gelassen hat | – → Grund, Detail | `expvelocitywatchdog.php:audit` | backoff | warning | on | yes | system | buffered |
| `system.velocity.watchdog.skip` | der Watchdog eine gestoppte Engine in Ruhe gelassen hat: absichtlich gestoppt, ein laufender Neustart, die belegte Sperre, die vorherige Prüfung läuft noch, oder ein Fehler; nur, wenn sich der Grund ändert (der erste Skip eines Zeitraums oder ein neuer Grund; der letzte wird in `watchdog-last.json` neben den Markierungen gehalten und geleert, wenn die Engine läuft oder wieder gestartet wird), nicht bei jeder Prüfung | – → Grund (`intentionally-stopped`, `restart-in-progress`, `lock-held`, `overlap`, `error`), Detail | `expvelocitywatchdog.php:audit` | skip | info | on | yes | system | buffered |
| `system.repair.queue` | die Reparatur-Warteschlange geschrieben oder ein Reparatur-Schlüssel erzeugt wird | Einträge → Einträge (nie der Schlüssel) | `lib/ezutils/classes/ezprepairqueue.php:writeSettings` | change | notice | on | yes | system | buffered |
| `system.maintenance.change` | der Wartungsmodus umgeschaltet wird | Modus → Modus | `expmaintenance.php:auditChange` | change | notice | on | yes | system | buffered |
| `system.template.change` | ein Template in der Administration angelegt oder bearbeitet wird | sha256 → sha256 (nie der Text des Templates) | `views/visual/templateedit.php:run`, `views/visual/templatecreate.php:run` | change | notice | on | yes | system | buffered |
| `system.workflow.trigger.change` | Workflow-Trigger geändert werden | Workflow → Workflow | `views/trigger/list.php:run` | change | notice | on | yes | system | buffered |
| `system.error.fatal` | eine Anfrage mit einem fatalen Fehler endet | – → Fehlerreferenz (`eZExecution::errorReference()`), Datei:Zeile (nie die Argumente der Meldung) | `audit/expaudit.php:flushOnFatal` | fatal | error | on | yes | system | buffered |
| `system.audit.enable` | `Audit=enabled` dort wirksam wird, wo es ausgeschaltet war | Zustand → Zustand | `audit/alerts/expauditalertevaluator.php:cronjob` | enable | notice | always | yes | system | at once |
| `system.audit.disable` | `Audit=disabled` geschrieben oder vorgefunden wird (Standardinstallation) | Zustand → Zustand | `audit/expaudit.php:settingWrite`, `audit/alerts/expauditalertevaluator.php:cronjob` | disable | warning | always | yes | system | at once |
| `system.audit.setting.write` | irgendeine Variable von audit.ini geschrieben wird (untergeordnet zu `system.setting.write`) | wie `system.setting.write` | `audit/expaudit.php:settingWrite` | setting | notice | always | yes | system | at once |
| `system.audit.read` | eine Ansicht der Audit-Konsole, der Fetch oder `exp:audit tail/search` verwendet wird (Q9) | – → Filter, Anzahl der Ergebnisse | `audit/archive/expauditarchiver.php:restore`, `audit/console/expauditconsole.php:recordRead`, `commands/audit.php:status`, `commands/audit.php:tail`, `commands/audit.php:show`, `commands/audit.php:search` | read | info | always | yes | system | at once |
| `system.audit.export` | Einträge exportiert werden | – → Filter, Format, Anzahl, sha256 der Datei | `commands/audit.php:export` | export | info | always | yes | system | at once |
| `system.audit.rotate` | eine Live-Datei geschlossen und eine neue begonnen wird | – → Datei, Einträge, letzter Hash | `audit/archive/expauditmaintenance.php:rotate` | rotate | info | always | yes | system | at once |
| `system.audit.archive` | Dateien in das Archiv komprimiert werden | – → Dateien, Manifest, Schlüssel-ID | `audit/archive/expauditarchiver.php:archiveDay` | archive | info | always | yes | system | at once |
| `system.audit.purge` | die Aufbewahrung Live-Dateien, Archive oder Index-Zeilen entfernt | – → Dateien, Zeilen, älteste aufbewahrte | `audit/archive/expauditarchiver.php:purge`, `audit/index/expauditindexer.php:purgeOld` | purge | info | always | yes | system | at once |
| `system.audit.pseudonymise` | Index-Zeilen nach `PseudonymiseAfterDays` pseudonymisiert werden | – → Zeilen | `audit/index/expauditindexer.php:pseudonymise` | pseudonymise | info | always | yes | system | at once |
| `system.audit.verify` | eine Kette oder ein Archiv geprüft wird | – → intakt/gebrochen, Einträge | `audit/archive/expauditmaintenance.php:verify`, `commands/audit.php:verify` | verify | info | always | yes | system | at once |
| `system.audit.chain.broken` | die Prüfung einen Bruch findet | – → Datei, Zeile, Art (siehe Prüfung) | `audit/archive/expauditarchiver.php:archiveDay`, `audit/archive/expauditmaintenance.php:verify`, `audit/index/expauditindexer.php:recordBreak`, `commands/audit.php:verify` | chain | error | always | yes | system | at once |
| `system.audit.chain.repair` | der Schreiber eine abgerissene letzte Zeile findet und danach weitermacht | – → übersprungene Bytes, letzter gültiger Hash | `audit/expauditwriter.php` | chain | notice | always | yes | system | at once |
| `system.audit.checkpoint` | der täglich signierte Anker des letzten Hashs jedes Kanals | – → pro Kanal: Datei, seq, Hash, HMAC | `audit/expaudit.php:checkpoint` | checkpoint | info | always | yes | system | at once |
| `system.audit.reindex` | der Index neu aufgebaut wird | – → Zeilen, ms | `audit/index/expauditindexer.php:rebuild`, `commands/audit.php:reindex` | reindex | info | always | yes | system | at once |
| `system.audit.import` | alte Textlogs importiert werden (Z4) | – → Datei, sha256, Einträge | `audit/archive/expauditimporter.php:import` | import | info | always | yes | system | at once |
| `system.audit.key.create` | ein Signatur- oder Pseudonym-Schlüssel erzeugt wird (Z7) | – → Schlüssel-ID, Fingerabdruck (nie der Schlüssel) | `audit/expaudit.php:write` | key | notice | always | yes | system | at once |
| `system.audit.key.rotate` | ein neuer Signaturschlüssel aktiv wird | Schlüssel-ID → Schlüssel-ID | `commands/audit.php:key` | key | notice | always | yes | system | at once |
| `system.audit.sink.failed` | ein Sink nicht zustellen kann (nach seinen Wiederholungen) | – → Sink, Fehler, Anzahl im Spool | `audit/sinks/expauditsinkregistry.php:deliverSpool` | sink | warning | always | yes | system | at once |
| `system.audit.alert` | eine Alarmregel auslöst | – → Regel, Anzahl, Zeitfenster, Gruppe | `audit/alerts/expauditalertevaluator.php` | alert | warning | always | yes | system | at once |
| `system.audit.overflow` | der Puffer sein Limit überschritten und vorzeitig geschrieben hat oder nicht geschrieben werden konnte | – → Ereignisse, Bytes | `audit/expaudit.php:store` | overflow | warning | always | yes | system | at once |
| `system.audit.file.open` | der erste Eintrag jeder Kanaldatei (verweist auf die vorherige Datei) | – → vorherige Datei, ihre letzte seq und ihr Hash | `audit/expauditwriter.php` | file | info | always | yes | system | at once |
| `system.audit.file.close` | der letzte Eintrag einer rotierten Datei | – → Einträge | `audit/expauditwriter.php` | file | info | always | yes | system | at once |
| `commerce.order.delete` | eine Bestellung entfernt wird `[order-delete]` | Bestellnummer, Status, Summe → – (nie Adressfelder des Kunden) | `ezorder.php:cleanupOrder` | remove | notice | on | yes | commerce | buffered |
| `commerce.order.purge` | alle Bestellungen entfernt werden `[order-delete]` | Anzahl → – | `ezorder.php:cleanup` | purge | notice | on | yes | commerce | buffered |
| `commerce.order.item.remove` | eine Bestellposition entfernt wird | Produkt, Anzahl → – | `ezorder.php:removeItem` | remove | notice | on | yes | commerce | buffered |
| `commerce.order.create` | eine Bestellung aktiviert wird (Checkout bestätigt) | – → Bestellnummer, Summe, Währung (nie die Adresse des Kunden) | `ezorder.php:activate` | create | info | off | no | commerce | buffered |
| `commerce.order.status` | sich der Status der Bestellung ändert | Status → Status | `ezorder.php:modifyStatus` | change | info | off | no | commerce | buffered |
| `commerce.order.archive` | eine Bestellung archiviert oder zurückgeholt wird | Flag → Flag | `ezorder.php:archiveOrder` | archive | info | off | no | commerce | buffered |
| `commerce.order.unarchive` | eine Bestellung archiviert oder zurückgeholt wird | Flag → Flag | `ezorder.php:unArchiveOrder` | unarchive | info | off | no | commerce | buffered |
| `commerce.basket.checkout` | ein Warenkorb zum Checkout geht | – → Positionen, Summe | `views/shop/checkout.php:run` | checkout | info | off | no | commerce | buffered |
| `commerce.payment.approve` | eine Zahlung genehmigt wird | Status → Status (nie Karten- oder Kontodaten) | `shop/classes/ezpaymentobject.php:approve` | approve | info | off | no | commerce | buffered |
| `commerce.vat.change` | sich Mehrwertsteuertypen oder -regeln ändern | Sätze → Sätze | `ezvatrule.php:removeVatRule`, `ezvatrule.php:store`, `ezvattype.php:store`, `ezvattype.php:removeThis` | change | info | on | yes | commerce | buffered |
| `commerce.currency.change` | Währungen angelegt, geändert oder entfernt werden | Code, Kurs → Code, Kurs | `shop/classes/ezcurrencydata.php:removeCurrencyList`, `shop/classes/ezcurrencydata.php:store` | change | info | on | yes | commerce | buffered |
| `commerce.discount.change` | sich Rabattgruppen oder -regeln ändern | Regel → Regel | `ezdiscountrule.php:store`, `ezdiscountrule.php:removeByID`, `ezdiscountsubrule.php:store`, `ezdiscountsubrule.php:remove` | change | info | on | yes | commerce | buffered |
| `data.export.csv` | Inhalte als CSV exportiert werden | – → Knoten, Zeilen, Spalten | `commands/ezcsvexport.php:run`, `views/content/subitemsexport.php:run` | export | info | on | yes | commerce | buffered |
| `data.export.package` | ein Paket exportiert wird | – → Name, Datei, Format (xml oder json), sha256 | `views/package/export.php:run` | export | info | on | yes | commerce | buffered |
| `data.export.pdf` | ein PDF-Export erzeugt wird | – | `views/content/pdf.php:expModuleViewContentPdfPassthrough` | export | info | off | no | commerce | buffered |
| `data.import.csv` | CSV importiert wird | – → Zeilen, angelegt | `commands/ezcsvimport.php:run` | import | info | on | yes | commerce | buffered |
| `data.import.dba` | eine .dba-Datei importiert wird | – → Tabellen, Zeilen | `commands/ezimportdbafile.php:run` | import | info | on | yes | commerce | buffered |
| `data.import.rss` | ein RSS-Import läuft | – → angelegt | `cronjobs/rssimport.php:rssImportAudit` | import | info | off | no | commerce | buffered |
| `data.infocollection.remove` | gesammelte Informationen entfernt werden | Anzahl → – (nie die gesammelten Werte) | `views/infocollector/collectionlist.php:run`, `views/infocollector/overview.php:run` | remove | notice | on | yes | commerce | buffered |
| `data.infocollection.view` | gesammelte Informationen geöffnet werden | – | `views/infocollector/view.php:run` | read | info | off | no | commerce | buffered |
| `data.index.rebuild` | der Suchindex neu aufgebaut wird | – → Objekte, ms | `commands/updatesearchindex.php:run` | rebuild | info | on | yes | commerce | buffered |
<!-- event-reference:end -->

`access.session.reauth` und `access.session.reauth.failed` werden von der erneuten Passworteingabe vor
Verwaltungsaktionen ausgelöst (`ReauthForManage`, [3.1](#31-wer-was-sehen-darf-die-richtlinien)); mit dem
ausgelieferten Standard (disabled) löst sie nichts aus.

---

## 9. Nachweise

Die Abnahmetests des Entwurfs (Z9), am Ende der Arbeit am 2026-10-02/03 ausgeführt.

### 9.1 Tests

`php vendor/bin/phpunit tests/tests/kernel/classes/audit/`: **119 Tests, 6 049 Assertions, OK** (2026-10-12, als root
ausgeführt: Als Benutzer der Website können die Tests zum Schreiben von Einstellungen ihr Verzeichnis unter einem root
gehörenden `var/tmp/ini-tests` nicht anlegen, und der journald-Test braucht root; frühere Läufe: einer als riskant
markiert: `testWriteFailureNeverThrows` ersetzt absichtlich den Error-Handler, um einen Schreibfehler herbeizuführen).

| Testklasse | Tests | Deckt ab |
|---|---|---|
| `expAuditRecordTest` | 12 | Eintragsformat und kanonisches JSON (B1), Namen, Muster, Routing, Zweige, übergeordnete Ereignisse |
| `expAuditChainTest` | 9 | Genesis, verknüpfte Tagesdateien, Rotation nach Größe, gleichzeitiges Anhängen (B2), Manipulationsfälle T0–T8 |
| `expAuditPrivacyTest` | 7 | jedes Feld × full/truncate/hash/off, /24 und /48, geheime Pfade, unbekannte Logins gehasht (B5) |
| `expAuditBufferTest` | 7 | Ausnahme, exit, cleanExit, fataler Fehler, 1 000 Velocity-Anfragen in einem Worker (B4) |
| `expAuditForkTest` | 4 | ein geforktes Kind schreibt nur seine eigenen Einträge und überlässt die offenen übergeordneten Ereignisse und den Puffer seines Elternprozesses diesem, auch wenn es nur beendet wird; wiederholte IDs werden als intakt geprüft, gezählt, und der Export gibt jede einmal aus (AF-01–AF-04) |
| `expAuditCompatTest` | 7 | die 15 Namen aus 4.x, HashKey nie aufbewahrt, Schlüssel, Besitz, Schreiben von Einstellungen (B3) |
| `expAuditHookTest` | 5 | jeder Name des Katalogs hat einen Hook-Punkt; ausgeschaltete Namen bauen nichts; Verben; die Brücke (Live-Datenbank) |
| `eZAuditLocationTest` | 5 | ein hinzugefügter, als Hauptort gewählter und entfernter Ort (Haupt- und Nebenort) zeichnet add, main, remove und den beförderten Hauptknoten auf; standardmäßig aus; die Ortsnamen aus LoveStack; Beschriftungen (Live-Datenbank, zurückgerollt) |
| `expAuditIndexTest` | 10 | Schema, inkrementeller Index, Absturz zwischen Stapel und Cursor, Neuaufbau = inkrementell, Suche, Pseudonymisierung, Fetch-Funktionen (Live-Datenbank, Testkanäle) |
| `expAuditSinksTest` | 5 | RFC 5424 und journald, ein Webhook-Empfänger, der jede Signatur prüft, Ausfall und Wiederholung, Drosselung der E-Mails (E1) |
| `expAuditAlertsTest` | 9 | jede eingebaute Regel erreicht, nicht erreicht, wiederholt; INI-Regeln; Nachspielen (E2) |
| `expAuditArchiveTest` | 6 | jeder Format-Handler, Wiederherstellen, Aufbewahrung, T9–T12, Schlüsselrotation, der tägliche Lauf (E3) |
| `expAuditImportTest` | 1 | beide Header-Formen aus 4.x, erneuter Import übersprungen, das Legacy-Manifest (E4) |
| `expAuditFilterGuardReauthTest` | 9 | Namensmuster, fehlerhafte Filter finden nichts, die Zeitregel, `--query`, `OnWriteFailure=refuse` (die Absicherung, ein Schreiben von Einstellungen, ein POST in der Administration), `ReauthForManage` (AG-01–AG-09; Live-Datenbank, Wegwerf-Log-Verzeichnisse) |
| `expAuditMailRecipientsTest` | 8 | jede Art von Empfänger gegen die Live-Datenbank (vorübergehende Benutzer und Gruppen, danach entfernt) |
| `expAuditExportStreamTest` | 7 | der gestreamte Export (EX-01–EX-07): dieselben Bytes wie die OFFSET-Schleife vor #393 für CSV, JSON Lines und JSON über mehrere Seiten und durch ein Limit abgeschnitten, Stapel für Stapel über seinen Zustand, mit nicht indizierten Einträgen obenauf; geordnete IDs und Zeilen nach ID; der Abfrage-Cache beim Lesen aus; die Datei des Hintergrundauftrags, ein Absturz zwischen einem Stapel und seinem Prüfpunkt, nur der Besitzer lädt herunter (Live-Datenbank, Testkanäle) |
| `expAuditSourceTest` | 8 | die Quellen der Konsole (AS-01–AS-08): keine registrierte ändert nichts, eine Quelle mit eigenem Kanal, was eine fernhält, nur View-Pfade, eine Quelle, die eine Ausnahme wirft, die Kanal-Einschränkung, ein in den Kanal der Quelle gerouteter Zweig, die Einschränkung der Richtlinie führt ihn auf |

Außerdem `tests/tests/kernel/classes/expViewAccessTest.php` (die Korrektur der Berechtigungen im Dashboard): 14 Tests,
68 Assertions, OK (6 übersprungen).

### 9.2 Manipulationstest

Die Tests belegen T0–T12 an erzeugten Einträgen. Stufe 6 wiederholte die Dateifälle an **Kopien des echten Kanals
access der Referenzinstallation** (1 565 Einträge über zwei Tagesdateien), geprüft mit den Live-Schlüsseln. Die
Live-Dateien wurden vorher gehasht und danach daraufhin geprüft, dass sie weiterhin mit genau den kopierten Bytes
beginnen:

```
Channel access: access-2026-10-02.jsonl, access-2026-10-03.jsonl (copies in var/tmp/audit-stage6/tamper-20261002-170104/)
Edited file: access-2026-10-02.jsonl
PASS T0  no change                                                  INTACT   1565 records (expected intact)
PASS T1  one byte of line 100 changed                               BROKEN   first break access-2026-10-02.jsonl line 100: altered (kinds: altered; expected altered at line 100)
PASS T2  line 50 removed                                            BROKEN   first break access-2026-10-02.jsonl line 50: link (kinds: link, gap; expected link + gap at line 50)
PASS T3  lines 61 and 62 swapped                                    BROKEN   first break access-2026-10-02.jsonl line 61: link (kinds: link, gap, reordered; expected reordered)
PASS T4  a forged line with a correct hash after line 70            BROKEN   first break access-2026-10-02.jsonl line 72: link (kinds: link, reordered; expected link at line 72)
PASS T5  20 records before checkpoint seq 1539 rewritten, chain recomputed BROKEN   first break access-2026-10-02.jsonl line 1539: rewritten (kinds: rewritten, truncated; expected rewritten at line 1539)
PASS T6  the oldest day file access-2026-10-02.jsonl removed        BROKEN   first break access-2026-10-03.jsonl line 1: no_origin (kinds: no_origin; expected no_origin or missing_file)
PASS T7  the last 10 lines of access-2026-10-02.jsonl cut off       BROKEN   first break access-2026-10-03.jsonl line 1: truncated (kinds: truncated; expected truncated)
PASS the live access files were not changed by the test (every live file still begins with the bytes copied)
PASS all cases
```

T5 ist der Angreifer, der die Kette ohne den Schlüssel neu berechnet. Der vorher geschriebene signierte Prüfpunkt zeigt
das Neuschreiben bei seiner seq, und der Eintrag `file.open` des nächsten Tages nennt weiterhin den alten letzten Hash
(`truncated`). T8 (eine abgerissene, reparierte Zeile) und T9–T12 (Archive) belegen `expAuditChainTest` und
`expAuditArchiveTest`. `exp:audit verify` auf dem Live-Protokoll danach: jeder Kanal INTACT (Ausgabe in
[Abschnitt 2](#2-schnellstart-zwei-minuten)).

### 9.3 Leistung

**Pro Anfrage** (P1, P2), im Kernel gemessen wie in den Stufen 2 und 3: 2 000 Anfragen pro Fall, abwechselnd in Blöcken
zu 100 zwischen eingeschaltetem Audit (die Live-Einstellungen, jeder Hook-Punkt der Stufe 3) und `Audit=disabled`:

| Fall | Ein p50 / p95 | Aus p50 / p95 | Zusätzlich (p50) | Ziel |
|---|---|---|---|---|
| R: eine Anfrage ohne Ereignisse (Reset, Header der Anfrage-ID, Brücke angehängt, Prüfung sensibler Views, ein ausgeschalteter Hook-Punkt, ein stichprobenartiger Lesezugriff bei ausgeschalteten Lesezugriffen, abschließender Flush) | 0,118 / 0,156 ms | 0,040 / 0,061 ms | **+0,078 ms** | < 2 ms |
| W: ein gepuffertes Ereignis (ein aus der Datenbank beschriebener Knoten) und sein Anhängen | 0,796 / 1,087 ms | 0,040 / 0,063 ms | **+0,756 ms** | < 2 ms |
| I: ein sofortiges access-Ereignis und der syslog-Sink beim Flush (journald, 200 Anfragen unter einer Testkennung) | 0,615 / 0,768 ms | 0,040 / 0,058 ms | **+0,575 ms** | < 2 ms |

Eine Startseite oder Seite der Administration, die nichts aufzeichnet, zahlt daher weniger als eine Zehntelmillisekunde,
und eine Anfrage, die etwas aufzeichnet, bleibt deutlich unter dem Budget von 2 ms, die Kopie in journald eingeschlossen.

**Inhaltsaufträge** (P3): ein Kopierauftrag und ein Entfernungsauftrag (ohne Papierkorb) für einen Testteilbaum mit 144
Knoten unter Media, drei Runden, abwechselnd mit ein- und ausgeschaltetem Audit:

```
source 14757: 144 nodes; 3 rounds, copy job + remove job, audit on (live settings) and off (Audit=disabled)
round 1 off copy done 144 nodes   5.15 s | remove done   2.91 s | total   8.06 s
round 1 on  copy done 144 nodes   5.21 s | remove done   2.68 s | total   7.89 s
round 2 on  copy done 144 nodes   4.95 s | remove done   2.76 s | total   7.71 s
round 2 off copy done 144 nodes   6.00 s | remove done   2.74 s | total   8.74 s
round 3 off copy done 144 nodes   5.62 s | remove done   2.54 s | total   8.17 s
round 3 on  copy done 144 nodes   4.95 s | remove done   2.63 s | total   7.58 s
copy: on 15.11 s, off 16.77 s | remove: on 8.07 s, off 8.19 s | total on 23.18 s, off 24.97 s, ratio 0.928
PASS content jobs with the audit within +5% of the time without (ratio 0.928)
```

Der Anteil des Audits liegt unter dem Rauschen von Lauf zu Lauf (das Ziel war höchstens 1,05). Die Runden mit
eingeschaltetem Audit schrieben 6 Anlagen von Aufträgen, 6 Starts, 6 Abschlüsse und 432 untergeordnete
`content.object.remove`. Die Runden ohne schrieben nichts. Der Testordner wurde danach von einem Inhaltsauftrag
entfernt:

```
removed the test folder 14756 (146 nodes) by content job 20261003-000230-29a7dcc0: done
PASS no test content left: 0 objects named 'Audit stage 6 test%' or 'A6 %', 0 nodes below 14756
```

P4 (zehn gleichzeitige Schreiber in einen Kanal, die Kette intakt, keine abgerissenen Zeilen) ist
`expAuditChainTest::testConcurrentAppends`.

### 9.4 Berechtigungen

- **A1** (Stufe 1, das Dashboard zeigt nur, was ein Benutzer öffnen darf): 0 Fehlschläge in 336 Prüfungen pro
  Siteaccess für Anonymous, Member, Partner, Editor, einen auf einen Teilbaum beschränkten Editor und Administrator
  (Anhang B, Stufe 1).
- **A2/A3** (Stufe 4, die Audit-Konsole): admin, Editor, Auditor (`audit/read`), Shop-Prüfer (`audit/read`
  beschränkt auf Channel commerce), Audit-Verwalter und Administrator, auf admin4 und admin, Apache und Velocity. Tab,
  Link in der Seitenleiste, Dashboard-Block, Links an Aufträgen und jede Ansicht wurden wie festgelegt angezeigt oder
  abgewiesen, der Shop-Prüfer sah nur commerce, und jede Abweisung wurde als `access.permission.refused`
  aufgezeichnet. Alles PASS (Anhang B, Stufe 4).

### 9.5 Registries und Konfigurationen

```
auditbranches    Audit taxonomy branches    0 entries, 0 broken:
auditsinks       Audit sinks                3 entries, 0 broken: syslog=expAuditSyslogSink, webhook=expAuditWebhookSink, mail=expAuditMailSink
auditalertrules  Audit alert rule classes   3 entries, 0 broken: threshold=expAuditThresholdRule, match=expAuditMatchRule, schedule=expAuditScheduleRule
auditformats     Audit archive formats      5 entries, 0 broken: gzip=expAuditGzipFormat, bzip2=expAuditBzip2Format, xz=expAuditXzFormat, zstd=expAuditZstdFormat, zip=expAuditZipFormat
PASS the RAD survey counts the audit registries
```

Die sechs Referenzkonfigurationen: `PASS 6 reference configurations` ([Abschnitt 6](#6-referenzkonfigurationen)). Die
Referenz der Ereignisse: `PASS 135 names, every one described`.

---

## Anhang A: Designentscheidungen (27 Fragen, 2026-10-02)

| # | Frage | Entscheidung |
|---|---|---|
| Q1 | Speicherung | **Beides**: Dateien in JSON Lines sind der Nachweis; ein Index in der Datenbank der Website bedient die Konsole (F4) |
| Q2 | Felder | **Kontext der Anfrage** (Ereignis-ID, Anfrage-ID, Siteaccess, URL/Methode, Modul/View, Engine Apache/Velocity/CLI, Host, pid, Dauer, HTTP-Status), **Akteur** (Benutzer-ID, Login, Rollen zum Zeitpunkt, Sitzungs-Hash, IP v4/v6, User-Agent, Identitätsübernahme, CLI-Benutzer + Befehl), **Werte vorher/nachher** (konfigurierbar, nie Passwörter/Tokens), **Ergebnis + Grund** (success/refused/failed, Grund aus Richtlinie/Token/Sperre, Fehler) |
| Q3 | Datenschutz | **Konfigurierbar, sicherer Standard**: pro Feld vollständig / gekürzte IP (/24, /48) / gehasht / aus; User-Agent ein/aus; nie Passwörter oder Tokens; personenbezogene Felder nach der Aufbewahrungsfrist pseudonymisiert |
| Q4 | Integrität | **Hash-Kette + signierte Archive**: Jedes Ereignis trägt pro Datei den Hash des vorherigen; Archive erhalten ein Prüfsummen-Manifest und einen HMAC; die Konsole zeigt, ob eine Kette intakt ist oder wo sie bricht |
| Q5 | Familien | **Inhaltslebenszyklus, Benutzer + Zugriff, System + Konfiguration, Handel + Daten**, und die Einteilung muss allgemein und wiederverwendbar sein („fast alles erfassen, wie in der Zoologie“) |
| Q6 | Dateien | **Kanäle nach Familie, Tagesdateien**; INI ordnet Ereignisse Kanälen zu; die alten Dateinamen pro Ereignis funktionieren als Aliase weiter |
| Q7 | Sinks | **syslog/journald, Webhook/HTTP, E-Mail bei kritischen Ereignissen, eine Registry für Sinks** für Erweiterungen |
| Q8 | Rotation | **Nach Tag und Größe; komprimierte Archive** (gzip, bzip2, xz, zstd, zip über Format-Handler in einer Registry) an einen konfigurierbaren Archivpfad; **Aufbewahrung pro Kanal; geplant über einen Cronjob-Teil** (auch aus der Konsole und dem Befehl ausführbar) |
| Q9 | Zugriff | Neue Richtlinien **audit/read** und **audit/manage**; jeder Zugriff auf die Konsole wird selbst auditiert; optional erneute Passworteingabe vor Verwaltungsaktionen |
| Q10 | Konsole | **Zeitleiste + Filter + Suche, Ereignisdetail + Links, Diagramme + Ansicht der Alarme, Export** |
| F1 | Taxonomie | **Beides**: hierarchische Namen mit Punkten für Konfiguration und Routing, und jeder Eintrag trägt außerdem Akteur / Verb / Objekt / Ziel / Ergebnis (ähnlich ActivityStreams) |
| F2 | Korrelation | **Anfrage-ID** (auch ein Antwort-Header), **Sitzungs- und Auftrags-IDs**, **übergeordnete/untergeordnete Ereignisse** (Tiefe konfigurierbar) |
| F3 | Geschwindigkeit | **Gepuffert**, am Ende der Anfrage mit einem Anhängen geschrieben (auch bei fatalen Fehlern); Sicherheitsereignisse sofort geschrieben; Zustand pro Anfrage für die persistenten Worker von Velocity zurückgesetzt |
| F4 | Index | **Die Hauptdatenbank der Website** (Schema auf jeder Engine: Z1) |
| F5 | Alarme | **Eingebaute Regeln** (Brute Force, Administratorrolle vergeben, Einstellungen außerhalb der Arbeitszeit geschrieben, Massenlöschung, Audit ausgeschaltet oder Kette gebrochen), **INI-Regeln**, **Regelklassen** in einer Registry |
| F6 | Aufbewahrung | **90 Tage live, 2 Jahre archiviert**, pro Kanal; personenbezogene Felder im Index nach 90 Tagen pseudonymisiert |
| F7 | Dashboard | **Nur, was die Richtlinien des Benutzers erlauben**: Jeder Dashboard-Block und jeder Link in der Seitenleiste prüft den Zugriff auf sein Modul/seine View (und Einschränkungen) |
| Z1 | Engines | **SQLite, MySQL/MariaDB, PostgreSQL, Oracle, MongoDB**: Schema und Tests auf jeder hier erreichbaren |
| Z2 | Platzierung | **Ein neues Modul `audit` mit eigenem oberen Tab**, **Link in der Seitenleiste des Dashboards + Block**, **Links von content/job und content/jobs**, **das Setup-Menü** |
| Z3 | API | **`expAudit::event()`** (das alte `eZAudit::writeAudit()` funktioniert weiter und wird darauf abgebildet), **Template-Operator/Fetch** (Richtlinie geprüft), **ezpEvent-Brücke**, **Befehl `exp:audit`** |
| Z4 | Alte Logs | **Importiert** in das neue Format (als importiert markiert, außerhalb der Kette); Originale archiviert |
| Z5 | Standard | **In jeder Installation standardmäßig eingeschaltet** (Eigentümer: "enabled in a default installation by default conventions, vs ezp4 where it was off") |
| Z6 | Lesezugriffe | **Optional, stichprobenartig**: Knotenansichten und Suchen pro Sektion/Klasse mit einer Stichprobenrate; Views sensibler Admin-Module (setup, role, user, audit) immer |
| Z7 | Schlüssel | **Beim ersten Gebrauch erzeugt, in settings/override gespeichert** (nie committet), als Fingerabdruck angezeigt; Schlüsselrotation mit Schlüssel-IDs in Archiven |
| Z8 | Lieferung | **Stufen mit Abnahme** |
| Z9 | Nachweis | **Abdeckungsmatrix, Manipulationstest, Leistung, Berechtigungsmatrix** |
| Z10 | Dokumentation | **Handbuch für Betreiber, Handbuch für Entwickler, Referenz der Ereignisse, Sicherheitshinweise** |

Jeder Punkt, den der Entwurf als „vorgeschlagen“ kennzeichnete, wurde vom Eigentümer angenommen und wie in diesem
Handbuch beschrieben gebaut, abgesehen von den in Anhang C aufgeführten Punkten.

## Anhang B: Wie es gebaut wurde, Stufe für Stufe

Vor 6.0.15 schrieb `eZAudit` (standardmäßig aus) einfache Textblöcke pro Ereignisname über `eZLog`. Die Rotation
behielt etwa 800 KB pro Datei und löschte ältere Historie stillschweigend. Seine `$GLOBALS`-Caches machten es unter
Velocity unsicher, ein Schlüssel zum Zurücksetzen des Passworts und vertippte Passwörter konnten im Log landen, und die
Passwort-Ereignisse von ezmbpaex hatten gar keine Datei und wurden verworfen. Zehn Ereignisnamen wurden von 36
Aufrufstellen geschrieben. Das hier beschriebene Audit ersetzt es, und `eZAudit::writeAudit()` bleibt als
Kompatibilitätspfad erhalten.

**Stufe 1: der Berechtigungsfehler im Dashboard (F7).** Das Dashboard der Administration zeigte Setup-Funktionen jedem
Benutzer, der es öffnen konnte. `expViewAccess` (kernel/classes/expviewaccess.php) entscheidet jetzt, ob der aktuelle
Benutzer eine Adresse öffnen kann, so wie `ezpKernelWeb` über die Anfrage entscheidet: URL-Aliase, Modul und View,
`[SiteAccessRules]`, `RequireUserLogin`, `PolicyOmitList`, die Einschränkung auf den Login-Siteaccess,
`hasAccessToView()` mit Einschränkungen und der Knoten oder das Objekt von content/view und content/edit. Templates
fragen `fetch( 'user', 'can_open', hash( 'uri', … ) )`. Obere Tabs, jedes linke Menü, die Dashboard-Blöcke
(`ViewList[]`), die Links des admin4-Dashboards und die Kontextmenüs verwenden es. Neue Einstellungen:
`menu.ini [MenuAccessSettings] CheckViewAccess`, `NoAccessLinks`; `dashboard.ini ViewList[]`. Matrix A1: 0
Fehlschläge in 336 Prüfungen pro Siteaccess (admin und admin4).

| Benutzer | Sichtbare Links | öffnen | Verborgen (Administrator sieht) | abgewiesen |
|---|---|---|---|---|
| Anonymous, Member, Partner | Anmeldeseite | – | 44 | 44 |
| Editor | 26 | 26 | 21 | 21 |
| Editor, auf einen Teilbaum beschränkt | 27 | 27 | 20 | 20 |
| Administrator (Test) / admin | 48 / 61 | alle | 0 | 0 |

**Stufe 2: der Kern der Ereignisse.** `expAudit`, die Registry der Taxonomie (135 Namen), Puffer und Schreiber
(Kanalsperren, der aus der Datei gelesene Kopf der Kette, Einträge zum Öffnen und Schließen, Rotation nach Größe,
Reparatur abgerissener Zeilen, Besitz), Prüfer, Leser, Schlüssel, Datenschutz, kanonisches JSON, der
Kompatibilitätspfad, der Reset für Velocity und der Header `X-Exp-Request-Id`,
`exp:audit` status/channels/tail/show/verify/checkpoint, die View `audit/recent`. Gemessen: eine Anfrage ohne
Ereignisse 0,090 ms eingeschaltet gegenüber 0,029 ms ausgeschaltet. Abweichungen vom ursprünglichen Text, jetzt Teil
des Entwurfs: Der Header ist `X-Exp-Request-Id`; `TrustedRequestIdHeader` wird nur von Loopback vertraut; eine
fehlgeschlagene Anmeldung eines bekannten Kontos hat `reason: credentials`, eine unbekannte `reason: not_found` mit
gehashtem Login.

**Stufe 3: Instrumentierung.** Jeder Hook-Punkt geht über `expAuditHook` (abgesichert, wirft nie, Daten nur gebaut,
wenn eingeschaltet). Die Aufrufe von `writeAudit()` im Kernel wurden zu nativen Ereignissen mit vollständigen Feldern,
Teilbäume und Inhaltsaufträge wurden zu übergeordneten Ereignissen ihrer Teile, innere Hook-Punkte werden stumm
geschaltet, während ein äußerer aufzeichnet, und die ezpEvent-Brücke kam hinzu. Abdeckungsmatrix C1, über echte
HTTP-Anfragen und CLI-Aufrufe an Testdaten ausgeführt:

| Server | PASS | FAIL | n/a |
|---|---|---|---|
| Apache (PHP-FPM) | 59 | 0 | 5 |
| Velocity (:8080) | 59 | 0 | 5 |
| CLI | 78 | 0 | 45 |

Namen, die kein Server im Test ausgelöst hat, waren die eigenen des Audits (`system.audit.*`, belegt durch die Tests
der Stufen 2, 4 und 5) und Aktionen, die nicht gegen Live-Daten ausgeführt wurden (Paketinstallation/-import,
Installation, Deploy, Bereinigen von Bestellungen, Neuaufbau des Suchindex, dba-Import, RSS-Import), jede mit ihrem
Grund im Commit der Stufe 3 dieses Dokuments aufgeführt. Die Testdaten wurden danach nachweislich entfernt.
Zusatzaufwand: eine Anfrage ohne Ereignisse 0,127 ms, +0,04 ms gegenüber Stufe 2. Unterwegs gefunden und getrennt
behoben: content/pdf antwortete mit 500 (`eZContentObject::cacheInfo()` statisch aufgerufen).

**Stufe 4: Index und Konsole.** Die drei Index-Tabellen auf jeder Engine (mit Volltext pro Engine), der inkrementelle
Indexer, die Suche (`expAuditQuery`), das Modul `audit` mit Dashboard, Konsole, Ereignis, Diagrammen, Alarmen,
Export, Archiven und Einstellungen, die Richtlinien mit der Kanal-Einschränkung, der Navigationsteil, die
Fetch-Funktionen und der Operator, der obere Tab, der Link in der Seitenleiste, der Dashboard-Block, der Setup-Eintrag,
die Links an Aufträgen und der Tab am Knoten sowie Übersetzungen (eng-US, ger-DE, 240 Meldungen). Nachweis: Schema
unter SQLite (21 Prüfungen) und erzeugtes SQL für MySQL, PostgreSQL, Oracle und MongoDB (15 Prüfungen); 13 097
Einträge in 6,0 s neu aufgebaut; FTS5 und LIKE ergaben dieselben IDs; `expAuditIndexTest` 10/10; Berechtigungsmatrix
A2/A3 PASS für sechs Benutzer auf admin4 und admin, Apache und Velocity; Playwright bei 960 px, Skalierung 2, hell und
dunkel. Das Dashboard öffnet in etwa 25 ms mit gecachten Zahlen über 7 Tage. Abweichungen: SQLite FTS5 verwendet den
Trigram-Tokenizer; Filterformulare senden per POST und leiten auf die als Lesezeichen speicherbare URL weiter;
Diagramme sind HTML/CSS.

**Stufe 5: Sinks, Alarme, Rotation.** Die Registry der Sinks mit Spools und Wiederholungen, syslog/journald (das native
Protokoll von journald für `Transport=local`, weil journald 252 RFC-5424-Header auf /dev/log nicht auswertet), der
signierte Webhook, E-Mail mit konfigurierbaren Empfängern (eine Entscheidung des Eigentümers vom 2026-10-02), der
Auswerter der Alarme mit den drei Regelklassen und sieben ausgelieferten Regeln, fünf Format-Handler, Archive mit
signierten und verketteten Manifesten, Aufbewahrung mit einem Löschprotokoll, der Import der Logs von 4.x (die eigene
`login.log` der Referenzinstallation importiert, Originale archiviert), der Cronjob-Teil und `exp:audit` vollständig.
Tests E1–E4 plus der Empfängertest: zu diesem Zeitpunkt insgesamt 81 Tests. Abweichungen: Regeln `match` lösen einmal
pro Eintrag aus, Regeln `schedule` einmal pro Gruppe und Zeitfenster; die Aufbewahrung entfernt nie einen Schlüssel;
wiederhergestellte und importierte Dateien haben eigene Verzeichnisse.

**Stufe 6: Nachweise und dieses Handbuch.** Der vollständige Testlauf, der Manipulationstest an Kopien echter Dateien,
die Leistungsmessung gegen die Ziele, die Zeitmessung der Inhaltsaufträge, die Zählung der RAD-Bestandsaufnahme, die
Prüfung der Referenzkonfigurationen und die erzeugte Referenz der Ereignisse ([Abschnitt 9](#9-nachweise)).

## Anhang C: Bekannte Probleme (2026-10-03)

Behoben, seit diese Liste zuerst geschrieben wurde: `exp:audit search --query` ([3.3](#33-der-befehl-expaudit)),
fehlerhafte Namensmuster werden abgewiesen, eine Zeitregel für `--from`/`--to`, die erneute Passworteingabe
`ReauthForManage` ([3.1](#31-wer-was-sehen-darf-die-richtlinien)) und `OnWriteFailure=refuse`
([4.13](#413-wenn-das-audit-nicht-schreiben-kann)), sowie der Export der Konsole, der für einen Kanal mit 27.000
Einträgen 35 s brauchte, unter Velocity in einen Timeout lief und einen größeren Filter nicht im Hintergrund ausführen
konnte ([Den Export streamen](#den-export-streamen), 2026-10-12).

Beim Schreiben dieses Handbuchs gefunden. Jedes wurde auf der Referenzinstallation geprüft:

| Problem | Auswirkung | Bis es behoben ist |
|---|---|---|
| Die Ansichten Archive und Einstellungen sowie die Ansicht Alarme sind nur lesend (kein „Jetzt archivieren“, kein Bestätigen, kein Einstellungsformular) | diese Aktionen werden auf der Kommandozeile erledigt | `exp:audit archive/restore/key`, `exp:ini set audit.ini/…` |

## Verwandte Seiten

- [Audit-Protokoll (Funktion)](../../funktionen/6.0/pruefprotokoll.md)
- [Verhaltensänderungen vom 1. und 2. Oktober 2026](verhaltensaenderungen-2026-10.md)
- [exp:ini](console-exp-ini.md), verwendet zum Ändern der Audit-Einstellungen
- [Handbuch zu Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md)
