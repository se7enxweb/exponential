# git_manager: Deployment und Sicherung aus dem Admin

Diese Seite richtet sich an Administratoren von Servern, auf denen niemand eine Shell hat oder auf denen eine Shell
nicht die gewünschte Arbeitsweise ist. `git_manager` stellt im Web-Admin zwei Werkzeuge bereit:

- ein **Git-Dashboard** (**Setup**, `/git_manager/dashboard`): Sehen Sie, wo die Installation gegenüber ihrem Branch
  auf dem Remote steht, wechseln Sie Branches, checken Sie einen Commit aus, führen Sie Pull und Push aus, verwalten
  Sie Remotes und Submodule;
- eine **Backup-Verwaltung** (`/git_manager/backup`): mit Zeitstempel versehene Sicherungen („Captions“) der Datenbank
  und der Dateien, optionale Verschlüsselung und bereinigte SQL-Abzüge, die gefahrlos weitergegeben werden können.

Die Extension erschien am 21. Juni 2026 als 2.0.1 (Backup-Verwaltung) und erreichte am 2. Oktober 2026 die
Version 2.0.14.

## Voraussetzungen

Exponential 6, PHP 8.0 oder neuer (PHP 7.4 ohne Support nutzbar), ein Git-Programm, das der Benutzer des Webservers ausführen kann, GnuPG
(`/usr/bin/gpg`) für verschlüsselte Sicherungen und `mysqldump` für Datenbanksicherungen.

## Schnellstart: vor einer Änderung eine Sicherung erstellen

1. Öffnen Sie **Setup > Sicherung** (`/git_manager/backup`).
2. Wählen Sie **Sicherung von Datenbank und Dateien**, fügen Sie eine Beschreibung wie „Vor dem Upgrade“ hinzu und
   klicken Sie auf Erstellen.
3. Die neue Sicherung erscheint unter **Vorhandene Sicherungen**, mit ihren Archiven zum Herunterladen.

Dasselbe auf der Kommandozeile:

```bash
php extension/git_manager/bin/php/backup-create.php full -d "Before upgrade"
php extension/git_manager/bin/php/backup-list.php -v -s
```

## Das Dashboard

### Upstream-Karte (2.0.14)

Oben im Dashboard: die Adresse des Remotes (ohne Zugangsdaten), der Branch, mit dem
verglichen wird, der Zeitpunkt des letzten Fetch, ein Badge (**Aktuell**, **zurück**,
**voraus**, **Auseinandergelaufen**), die Commits auf dem Remote, die der Installation fehlen, und
umgekehrt die lokalen (kurzer Hash, Datum, Name des Autors, Betreff und ein Link zum
Commit auf GitHub oder GitLab) sowie die Anzahl der geänderten, vorgemerkten, nicht verfolgten und
im Konflikt stehenden Dateien.

**Jetzt holen** führt `git fetch --prune` auf dem Remote aus und sonst nichts: kein Pull, Merge,
Reset oder Checkout. Es wartet nie auf ein Passwort, wird nach `FetchTimeout`
Sekunden abgebrochen und zeigt bei einem Fehlschlag die Ausgabe von Git. Wenn der Benutzer der Website die
Remote-Tracking-Branches nicht schreiben kann, nennt die Karte statt des Fetch, welche Dateien betroffen sind und
wem sie gehören. Funktioniert die SSH-Adresse für den Benutzer der Website nicht und liegt das Repository auf
GitHub, holt die Karte anonym über HTTPS in dieselben Branches (ein privates
Repository schlägt dann weiterhin fehl). Jeder Fetch wird als Audit-Ereignis
`system.git_manager.fetch` protokolliert. Der Status wird im Cache-Verzeichnis zwischengespeichert und neu berechnet,
wenn sich HEAD, der Branch, der Remote-Tracking-Branch, `FETCH_HEAD`, `packed-refs` oder der
Index ändern.

### Branches, Commits, Pull

Wechseln Sie zu einem lokalen oder einem entfernten Branch, checken Sie einen Commit per Hash aus, holen Sie per Pull
den neuesten Stand, aktualisieren Sie beim Checkout die Submodule, prüfen Sie den Commit an HEAD und seinen Diff, und
durchsuchen Sie den Commit-Verlauf gefiltert nach Autor oder Datumsbereich. Der Verlauf ist ein aufklappbarer Bereich,
der so bleibt, wie jeder Benutzer ihn hinterlässt (die Präferenz `admin_git_manager_commit_log`). Seine Zusammenfassung
sagt, wie viele der neuesten Commits auf keinem Remote liegen. Ein Commit, den kein Remote hat, ist orange
hervorgehoben und als **nicht gepusht** markiert; einer, der nur einigen Remotes fehlt, ist blau mit
**nicht auf <remote>**; der ausgecheckte Commit ist grün. **Nur zu pushende Commits** blendet den Rest aus.

### Push (2.0.6)

**Zu einem Remote pushen** listet jedes Remote mit seiner Adresse auf (ein darin enthaltener Benutzername, ein
Passwort oder Token wird weggelassen) und zeigt, wie der ausgecheckte Branch dazu steht: aktuell,
Commits zu pushen, Commits zurück oder noch nicht auf diesem Remote. **Pushen** sendet einen lokalen Branch
an den gleichnamigen Branch (`git push --porcelain <remote> refs/heads/x:refs/heads/x`).
Der Push wird **nie erzwungen**: Ein Remote mit Commits, die dem Branch fehlen, weist ihn ab, und die
Ausgabe sagt es. Die Seite fragt vorher nach und nennt dabei den Branch, das Remote, seine Adresse und wie
weit der Branch voraus und zurück ist. Git wartet nie auf jemanden: keine Passwortabfrage, SSH im
Batch-Modus mit 15 Sekunden Verbindungs-Timeout, Abbruch nach 90 Sekunden.

### Remotes (2.0.7)

Jedes Remote hat einen aufklappbaren Bereich **Bearbeiten**, um Name und Adresse zu ändern oder es zu entfernen (mit
Rückfrage); **Ein Remote hinzufügen** nimmt einen Namen und eine Adresse entgegen. Ein Name besteht aus Buchstaben,
Ziffern, Punkt, Bindestrich und Unterstrich und beginnt nicht mit einem Bindestrich oder Punkt. Eine Adresse ist eine
`https://`-, `http://`-, `ssh://`-, `git://`- oder `file://`-URL, `user@host:path`, `host:path` oder ein absoluter Pfad;
ohne Leerzeichen, Steuerzeichen oder führenden Bindestrich und ohne `::`, sodass Gits `ext::` und andere
Transport-Helfer nicht zum Ausführen eines Befehls genutzt werden können. Eine Adresse, die die Seite ohne ihre
Zugangsdaten anzeigt und unverändert speichert, bleibt wie sie ist, sodass das Bearbeiten eines Remotes nie ein Token
verliert.

### Submodule (2.0.8)

Der Abschnitt Submodule listet jedes Submodul auf (Pfad, Adresse ohne Zugangsdaten, der Branch,
dem es folgt, sein Commit und Zustand), mit **Aktualisieren** und einem aufklappbaren Bereich **Bearbeiten**, um
Adresse und Branch zu ändern oder es zu entfernen. **Ein Submodul hinzufügen** (standardmäßig eingeklappt, merkt sich
seinen Zustand in der Präferenz `admin_git_manager_add_submodule`) klont in einen neuen Pfad. Hinzufügen,
Ändern und Entfernen merken die Änderung für einen Commit vor.

### Kommandozeile

```bash
php extension/git_manager/bin/php/remote.php --help      # list, add, set-url, rename, remove, fetch, push
php extension/git_manager/bin/php/submodule.php --help   # list, add, set-url, set-branch, update, remove
```

## Die Backup-Verwaltung

Öffnen Sie **Setup > Sicherung** (`/git_manager/backup`; die Adresse vor 2.0.4,
`/git_manager/dump`, leitet dorthin weiter).

| Typ | Erzeugt | Verwendung |
|---|---|---|
| Vollständiges Backup der Website | SQL-Abzug, `var/`-Archiv und Website-Dateien (`extension/`, `settings/`, `config.php`): drei Archive | Neuaufbau der Website auf einem neuen Server (die Kerndateien kommen aus Git) |
| Sicherung von Datenbank und Dateien | SQL-Abzug und `var/`-Archiv | Die tägliche Sicherung |
| Nur Datenbank | SQL-Abzug | Vor Datenbankänderungen oder Migrationen |
| Nur Dateien (`var/`) | `var/`-Archiv | Vor umfangreichen Dateiänderungen |

Wählen Sie einen Typ, fügen Sie optional eine Beschreibung hinzu, setzen Sie optional das Häkchen bei
**Backup-Dateien verschlüsseln** und geben Sie eine Passphrase ein (AES256 über GnuPG), setzen Sie optional das Häkchen
bei **AGPL-kompatible Veröffentlichung**, und erstellen Sie die Sicherung. Laden Sie jedes Archiv unter **Vorhandene
Sicherungen** herunter; löschen Sie alte Sicherungen einzeln oder mit **Alle auswählen + Ausgewählte löschen**. Ein
auffälliger roter Kasten erscheint, wenn es überhaupt keine Sicherungen gibt, ein orangefarbener, wenn die neueste
älter als 7 Tage ist, und jede Sicherung hat eine Altersanzeige (grün unter 7 Tagen, orange 7 bis 90 Tage, rot älter).

### Bereinigte Abzüge zum Weitergeben

Die AGPL-Option schreibt neben den privaten Abzug ein zusätzliches `sql_agpl_<time>.tar.gz`, mit einer
Markierungsdatei `agpl_compatible.txt` und einem Ordner `licenses/` (Texte der GPL v2 und AGPL v3) in
jeder Sicherung. Bereinigt werden: Passwort-Hashes, E-Mail-Adressen, Analytics-IDs, API-Schlüssel für Zahlung, Mail
und Cloud, Captcha-Schlüssel, benannte Einstellungen wie `Password`, `ApiKey`, `AuthToken`,
`WebhookSecret`, `SMTPPassword`, absolute Pfade auf dem Datenträger, Datenbank-Zugangsdaten, IP-Adressen und
SMTP-Zugangsdaten. Seit 2.0.8 entfernt sie außerdem jede Tabelle mit Geheimnissen oder personenbezogenen Daten:
Schlüssel für Passwort-Zurücksetzung und Konto (`ezforgot_password`, `ezuser_accountkey`), `ezuservisit`,
Benachrichtigungseinstellungen, jede `ezprest_`-Tabelle (OAuth-Clients und -Tokens), `ezinfocollection`
(was Besucher in Formularen gesendet haben), `ezorder`, `ezbasket`, `ezproductcollection`, `ezwishlist`,
`ezpaymentobject`, `ezcollab_` und `cjwnl_` (Newsletter-Abonnenten), zusammen mit Session-, Audit- und
Tabellen ausstehender Aktionen. Das Hash-Muster wird nur auf `ezuser`-Zeilen angewendet, sodass
`remote_id`s erhalten bleiben und der Abzug sich importieren lässt.

### Sicherungen auf der Kommandozeile

```bash
php extension/git_manager/bin/php/backup-list.php -v -s   # -v lists the files of each backup, -s shows sizes
php extension/git_manager/bin/php/backup-create.php fullsite -d "Before upgrade"   # fullsite | full | db | var
php extension/git_manager/bin/php/backup-create.php full -e --passphrase-file=/root/backup.pass -d "Encrypted"
php extension/git_manager/bin/php/backup-info.php 2026-06-21_20-20-49
php extension/git_manager/bin/php/backup-delete.php 2026-06-21_20-20-49 -f
```

Übergeben Sie die Passphrase mit `--passphrase-file` oder über die Umgebungsvariable
`GIT_MANAGER_BACKUP_PASSPHRASE`, oder lassen Sie sie von einem Terminal ohne Echo abfragen. `-p` funktioniert
weiterhin, legt sie aber in die Prozessliste und die Shell-Historie und gibt deshalb eine Warnung aus.

Eine verschlüsselte Datei (`.tar.gz.gpg`) entschlüsseln:

```bash
gpg --batch --yes --passphrase "YOUR_PASSPHRASE" -d backup_file.tar.gz.gpg | tar -xzf -
```

Für eine verlorene Passphrase gibt es keine Wiederherstellung.

### Sicherheit der Sicherungen (2.0.8)

* Jede Datei und jedes Verzeichnis, das eine Sicherung schreibt, wird nur für den Eigentümer zugänglich angelegt
  (umask 077, Ordner 0700); zuvor waren sie für alle lesbar und enthielten Datenbank und Einstellungen.
* `mysqldump` erhält Host, Port, Benutzer und Passwort aus einer Optionsdatei, die nur für ihren Eigentümer lesbar ist
  (`--defaults-extra-file`) und nach dem Abzug entfernt wird; das Passwort steht nie auf einer Kommandozeile, und
  Datenbankname und Ausgabedatei werden quotiert.
* Der Sicherungsordner hat eine `.htaccess`, die alles verweigert, und eine leere `index.html`.
* Downloads werden von einem authentifizierten Controller mit `Cache-Control: private, no-store`
  und `X-Content-Type-Options: nosniff` ausgeliefert. `VarExcludeDirs` lässt `site/sessions` aus.
* Die Sicherungsseite maskiert Beschreibungen, Zeitstempel, Dateinamen und Meldungen.
* Branch-Namen und Commit-Hashes erreichen Git nur, wenn sie gültig und quotiert sind; ein Hash muss aus 7 bis 40
  hexadezimalen Zeichen bestehen.

## Einstellungen

Alle in `git_manager.ini`, Blocknamen wie angegeben.

| Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|
| GitManagerSettings | `BackupPath` | `var/site/backups/captions` | Wo die Sicherungen gespeichert werden |
| GitManagerSettings | `MySQLDumpCommand` | `mysqldump --host={host} --port={port} ...` | Dump-Befehl mit Platzhaltern |
| GitManagerSettings | `VarExcludeDirs[]` | `cache`, `log`, `site/backups`, `site/sessions` | Aus dem `var/`-Archiv ausgelassen |
| GitManagerSettings | `MaxBackups` | `36` | Darüber hinaus werden die ältesten Sicherungen entfernt; 0 bedeutet unbegrenzt |
| GitManagerSettings | `CompressBackups` | `enabled` | gzip |
| GitManagerSettings | `EnableEncryption` | `enabled` | GPG-Verschlüsselung anbieten |
| GitManagerSettings | `DeleteUnencryptedAfterEncryption` | `enabled` | Nur die `.gpg`-Datei behalten |
| UpstreamSettings | `Remote` | `origin` | Remote, mit dem die Upstream-Karte vergleicht |
| UpstreamSettings | `Branch` | leer | Branch auf diesem Remote; leer verwendet den Upstream des ausgecheckten Branches |
| UpstreamSettings | `FetchTimeout` | `60` | Sekunden, bevor ein Fetch abgebrochen wird |
| UpstreamSettings | `HttpsFallback` | `enabled` | Anonymer HTTPS-Fetch eines GitHub-Repositorys |
| UpstreamSettings | `WebUrl` | leer | Browser-Adresse für Commit-Links; leer leitet sie aus dem Remote ab |
| UpstreamSettings | `MaxCommits` | `100` | In jede Richtung aufgelistete Commits |
| UpstreamSettings | `StatusCacheTTL` | `60` | Sekunden, für die die Anzahlen nicht committeter Dateien aufbewahrt werden |

## Richtlinien

Rollenrichtlinien im Modul `git_manager`:

| Funktion | Erlaubt |
|---|---|
| `git_manager` | Das Dashboard, Commit-Details, Fetch |
| `backup` | Die Sicherungsseite und Downloads (vor 2.0.4 `dump` genannt; Rollen, die den alten Namen gewähren, funktionieren weiter) |
| `push` | Einen Branch pushen |
| `remotes` | Remotes hinzufügen, ändern und entfernen |
| `submodules` | Submodule hinzufügen, ändern und entfernen |

Alte Rollenrichtlinien benennen Sie so um:

```bash
php extension/git_manager/bin/php/upgrade-policy-dump-to-backup.php --dry-run
php extension/git_manager/bin/php/upgrade-policy-dump-to-backup.php
```

## Sprachen

Die Extension enthält Übersetzungsdateien in `translations/<locale>/translation.ts`: eng-US und ger-DE. Die deutsche
Datei enthält 199 Meldungen (zählen Sie `<message` in `translations/ger-DE/translation.ts`). Die Texte werden in den
Kontexten `extension/git_manager`, `kernel/navigationpart`, `design/admin/pagelayout` und
`design/admin/parts/setup/menu` nachgeschlagen.

Nach dem Bearbeiten einer Übersetzungsdatei aktualisieren Sie den kompilierten Übersetzungs-Cache mit
`./console exp:ezgeneratetranslationcache` und leeren den Template- und den Inhalts-Cache.
`./console exp:ezchecktranslation ger-DE` gibt Statistiken zur `share/translations/ger-DE/translation.ts` des Kernels
aus, nicht zur Datei dieser Extension.

## Verwandte Seiten

- [Prüfprotokoll](../../../bc/6.0/audit.md): das Ereignis `system.git_manager.fetch`
- [ezupdate](ezupdate.md): Aktualisierungen nach einer Sicherung
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/git_manager.md) und [Versionshinweise](../../../../changelogs/extensions/git_manager.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/git_manager.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2026-06](../../../geschichte/erweiterungen/monate/2026-06.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
