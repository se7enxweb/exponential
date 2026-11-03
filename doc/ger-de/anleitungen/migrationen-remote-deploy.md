# Migrationen: Deploy auf entfernte Installationen

Diese Seite ist die Anleitung, die Migrationen einer Installation (zum Beispiel beta) auf eine oder viele andere
Installationen (dev, stage, production, eine Gruppe von Produktionsknoten) zu übertragen und dort auszuführen, alle
gleichzeitig und im Gleichstand: Jede ausgewählte Gegenstelle endet im selben Migrationsstand, oder keine ändert sich.

[Anleitungen](README.md) · [Migrationen](migrationen.md) · Verwandt: [Pakete, Deploys und lange Läufe](migrationen-pakete-deploy-und-lange-laeufe.md),
[Rückgängig und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md)

## Was ein Deploy tut

`./console exp:migration:deploy --remotes=<IDs oder Gruppen>` arbeitet in Phasen. Jede Phase läuft auf allen
ausgewählten Gegenstellen parallel, und eine Phase beginnt erst, wenn die vorige auf jeder Gegenstelle bestanden hat:

| Phase | Auf jeder Gegenstelle | Wenn sie scheitert |
|---|---|---|
| prepare | Die Einstellungen der Gegenstelle und ihre Zugangsdaten werden geprüft (noch keine Verbindung). | Nichts wurde angesprochen. |
| preflight | Zuerst die Korrekturen aus `SelfHeal[]` (siehe „Selbstheilung“), dann wird dort `exp:migration:status --format=json` gelesen: erreichbar, die Laufsperre frei, keine gescheiterte Migration, dieselbe Kernel-Version wie hier, PHP- und Erweiterungsversionen, genug freier Plattenplatz, keine Migration nach dem Ausführen dort geändert. Die zu sendenden Dateien werden geplant. | Nirgends wurde etwas geändert. |
| transfer | Die geplanten Migrationsdateien werden gesendet. | Keine Gegenstelle wurde umgeschaltet; die gesendeten Dateien bleiben dort ausstehend. |
| verify | Ein Probelauf von `exp:migration:migrate` dort muss jede gesendete Datei mit der Prüfsumme dieser Installation sehen, und Sicherungspunkte müssen eingeschaltet sein, wo etwas auszuführen ist. FTP ohne Trigger: Jede Datei wird zurückgelesen und verglichen. | Keine Gegenstelle wurde umgeschaltet. |
| switch | `exp:migration:migrate --apply --run-id=<Deploy-ID>` auf allen Gegenstellen gleichzeitig, mit einer gemeinsamen Lauf-ID. | Siehe „Alles oder nichts“. |

Ohne `--apply` ist der Deploy ein Probelauf: Er endet nach dem Preflight und listet je Gegenstelle die Dateien, die er
senden würde, und die Migrationen, die laufen würden. Nichts wird gesendet oder geändert.

```
./console exp:migration:deploy --remotes=stage,production
./console exp:migration:deploy --remotes=stage,production --apply
```

Gesendet werden nur die Migrationen der Site (`[MigrationSettings] DirectoryList[]`) und die der aktiven Erweiterungen.
Migrationen von Paketen erreichen eine Gegenstelle mit ihrem Paket, Kernel-Schritte mit dem Kernel. Eine Migration,
die die Gegenstelle schon unter demselben Pfad mit derselben Prüfsumme hat, wird nicht noch einmal gesendet.

## Alles oder nichts

Scheitert eine Gegenstelle beim Umschalten, setzt der Deploy jede Gegenstelle, die ausgeführt hat, auf den
Sicherungspunkt zurück, den sie unmittelbar vor dem Lauf angelegt hat (dort `exp:migration:rollback <Deploy-ID> --apply`).
Die gescheiterte Gegenstelle hat ihren eigenen Sicherungspunkt schon wiederhergestellt, wie jeder gescheiterte Lauf.
Der Deploy meldet dann `rolled-back`: Jede Gegenstelle steht dort, wo sie vorher stand.

Deshalb lehnt die Phase verify eine Gegenstelle ab, deren Sicherungspunkte ausgeschaltet sind (`[CaptionSettings]`):
Ohne den Sicherungspunkt vor dem Lauf könnte sie nicht zurückgesetzt werden.

Eine Gegenstelle, die nicht zurückgesetzt werden kann (ihr Rollback scheitert, oder die Verbindung brach während des
Laufs ab, sodass ihr Stand unbekannt ist), macht den Deploy `out-of-sync`. Der Bericht nennt den Stand jeder
Gegenstelle; prüfen Sie ihn mit `./console exp:migration:remote:status` und bereinigen Sie die betroffene von Hand vor
dem nächsten Deploy.

Umschalten und Rollback werden nie automatisch wiederholt: Ein Lauf, der begonnen haben könnte, wird kein zweites Mal
gestartet.

## Gegenstellen

Gegenstellen werden in `migration.ini` benannt (in `settings/override/migration.ini.append.php`), je ein Abschnitt:

```ini
[RemoteSettings]
RemoteList[]
RemoteList[]=stage
RemoteList[]=production1
RemoteList[]=production2

[Remote_stage]
Environment=stage
Transport=rsync
Host=stage.example.com
User=deploy
Path=/var/www/site
Credential=sshkey:deploy_stage

[Remote_production1]
Environment=production
Transport=ssh
Host=node1.example.com
User=deploy
Path=/var/www/site
Php=/usr/bin/php8.4
Credential=sshkey:deploy_production
Groups[]=production
HostKeyFingerprint=SHA256:+DiY3wvvV6TuJJhbpZisF/zLDA0zPMSvHdkr4UvCOqU

[Remote_production2]
Environment=production
Transport=sftp
Host=node2.example.com
User=deploy
Path=/var/www/site
Credential=sshkey:deploy_production
Groups[]=production
```

| Einstellung | Bedeutung |
|---|---|
| `Environment` | Die Bezeichnung in den Berichten: alpha, dev, stage, beta, production, post, arch ... |
| `Transport` | `ssh` (Befehle über ssh, Dateien mit scp), `sftp`, `rsync` (über ssh: nur Abweichendes wird gesendet, eine abgebrochene Übertragung wird fortgesetzt), `ftp`, `ftps` (nur Dateien), `local` (ein Ordner dieses Hosts) |
| `Host`, `Port`, `User` | Wo die Gegenstelle ist; der Port ist 22 (21 bei FTP), wenn nichts gesetzt ist |
| `Path` | Das Wurzelverzeichnis der Installation auf der Gegenstelle (bei FTP: der Pfad, den das FTP-Konto sieht) |
| `Php` | Das PHP-CLI auf der Gegenstelle (Vorgabe `php`) |
| `Credential` | `sshkey:<Name des Geheimnisses>`, `password:<Name des Geheimnisses>`, jeweils mit `env:VARIABLE` statt des Namens, oder `none` (SSH-Agent und Schlüssel des Hosts) |
| `Groups[]` | Gruppen der Gegenstelle: `--remotes=production` wählt jede Gegenstelle der Gruppe |
| `HostKeyFingerprint` | Optional: Der Host-Schlüssel muss diesen Fingerabdruck haben (`ssh-keygen -l -E sha256`); ohne ihn wird ein Host-Schlüssel beim ersten Kontakt gelernt und ein geänderter abgelehnt |
| `AllowRootUser` | `enabled`, wenn die Konsolenskripte auf der Gegenstelle als root laufen (`--allow-root-user`) |

`--remotes=all` wählt jede Gegenstelle. `[RemoteSettings]` setzt außerdem `Parallel` (wie viele Gegenstellen
gleichzeitig), `Timeout` (Sekunden je Gegenstelle und Phase), `Retries` (wie oft ein Verbindungsfehler erneut versucht
wird) und `StoreReports`.

```
./console exp:migration:remote:list
```

listet die Gegenstellen und jedes Problem ihrer Einstellungen, ohne sich mit einer zu verbinden.

## Zugangsdaten

Ein Gegenstellen-Abschnitt enthält nie ein Geheimnis: Ein Abschnitt mit einer Einstellung `Password`, `Key`, `Token`
oder `Secret` wird abgelehnt. `Credential` nennt ein Geheimnis des Geheimnisspeichers (Setup > Secrets) oder eine
Umgebungsvariable (`env:VARIABLE`, für CI).

- **SSH-Schlüssel** (`sshkey:`): Der private Schlüssel wird für die Dauer des Deploys in eine Datei geschrieben, die nur
  der Deploy lesen kann, und danach überschrieben und entfernt. Legen Sie den öffentlichen Schlüssel in
  `~/.ssh/authorized_keys` des Benutzers auf der Gegenstelle.
- **Passwort** (`password:`): Bei ssh fragt ssh das Hilfsskript `bin/shell/expstaticaskpass.sh`, das aus der Umgebung
  des ssh-Prozesses antwortet; bei FTP steht es in einer netrc-Datei, die nur der Deploy lesen kann. Es steht nie auf
  einer Befehlszeile.

Jede Ausgabe, die ein Bericht behält oder die angezeigt wird, ist geschwärzt: Der Wert des Geheimnisses wird durch
`[secret <Art>:<Name>]` ersetzt.

## FTP-Gegenstellen

FTP kann keine Befehle ausführen. Ohne Weiteres sendet und prüft ein Deploy auf eine FTP-Gegenstelle daher nur die
Dateien:

```
./console exp:migration:deploy --remotes=webspace --transfer-only --apply
```

Jede ausgewählte Migration wird gesendet (der Stand der Gegenstelle lässt sich über FTP nicht lesen) und zurückgelesen,
um zu prüfen, dass sie unverändert ankam.

### FTP-Gegenstellen mit dem HTTP-Trigger

Bietet die entfernte Installation ihren signierten HTTP-Trigger an, wird eine FTP-Gegenstelle wie jede andere
bespielt: Ihr Stand wird gelesen, die Dateien gehen über FTP, ein Probelauf dort prüft sie, das Umschalten führt sie
aus, und ein Fehler irgendwo setzt sie zurück. Die Quelle schickt jeden Befehl als signiertes `POST` an die Ansicht
`migration/trigger` der Gegenstelle; diese führt dort das Konsolenskript aus (`exp:migration:status`, `migrate`,
`rollback`, `heal`) und antwortet mit dessen JSON-Bericht.

Auf der **Gegenstelle** (der Installation, die per FTP erreicht wird), in `settings/override/migration.ini.append.php`:

```ini
[TriggerSettings]
Trigger=enabled
Secret=migration_trigger             # ein Geheimnis aus Einrichtung > Geheimnisse (oder env:VARIABLE), mindestens 16 Zeichen
AllowedAddresses[]
AllowedAddresses[]=203.0.113.10      # die Adresse, von der die Quelle verbindet (auch CIDR-Bereiche)
Php=/usr/bin/php8.4                  # das PHP-CLI, das dort die Konsolenskripte ausführt
```

Auf der **Quelle** nennt der Abschnitt der Gegenstelle die Adresse und dasselbe Geheimnis beim Namen:

```ini
[Remote_webspace]
Transport=ftps
Host=ftp.example.com
User=web
Path=/htdocs
Credential=password:webspace_ftp
Trigger=https://www.example.com/migration/trigger
TriggerSecret=migration_trigger
```

Was der Trigger annimmt:

| Prüfung | Antwort, wenn sie fehlschlägt |
|---|---|
| Nur `POST` | 405 |
| `Trigger=enabled` | 404 |
| Die Adresse der Verbindung (`REMOTE_ADDR`, nie ein weitergereichter Header) steht in `AllowedAddresses[]`; eine leere Liste nimmt nichts an | 403 |
| Das Geheimnis ist gesetzt und mindestens 16 Zeichen lang | 503 |
| `X-Exp-Migration-Trigger: v1.<Zeit>.<Nonce>.<HMAC-SHA256>` über die Zeit, die Nonce und den SHA-256 des Inhalts, höchstens `Lifetime` Sekunden alt (300) | 401 |
| Höchstens `MaxRequestsPerMinute` Anfragen pro Minute (30) | 429 |
| Die Nonce wurde noch nicht verwendet (keine Wiederholung) | 409 |
| Der Inhalt ist eine bekannte Anfrage: eine Aktion (`status`, `dry-run`, `apply`, `rollback`, `heal`) mit gültiger Lauf-ID, Migrations-IDs oder Korrekturen; nie eine Befehlszeile | 400 |

Jede Anfrage wird neu signiert, auch wenn sie nach einem Verbindungsfehler wiederholt wird. Das Geheimnis wird nie
übertragen, es signiert nur. Jede Anfrage, die bis zu den Prüfungen kommt, steht im Audit-Protokoll der Gegenstelle
(`system.migration.deploy`, Verb `trigger-<Aktion>`). Die Ansicht braucht keine Anmeldung (`site.ini
[SiteAccessSettings] AnonymousAccessList[]` und `[RoleSettings] PolicyOmitList[]` nennen `migration/trigger`); mit
`Trigger=disabled`, der Vorgabe, nimmt sie nichts an.

## Selbstheilung und die Prüfungen des Preflights

Die **Selbstheilung** ist aus, bis `[RemoteSettings] SelfHeal[]` auf der Quelle die Korrekturen nennt, die ein Deploy
auf einer Gegenstelle vornehmen darf. Jede läuft dort als `./console exp:migration:heal --fix=... --apply`, und jede
Korrektur steht in den `fixes` der Gegenstelle im Bericht (die Seite des Deploys zeigt sie unter der Gegenstelle):

| Korrektur | Wann | Was sie tut |
|---|---|---|
| `stale-lock` | vor dem Preflight | Entfernt die Halter-Datei eines beendeten Laufs. Eine noch gehaltene Sperre wird nur beiseitegelegt, wenn ihr Halter auf diesem Host lief, sein Prozess beendet ist (ein Kindprozess, der die Sperre geerbt hat, kann sie noch halten), sie älter als `StaleLockAge` (7200 s) ist und die Sperrdatei demselben Benutzer gehört. Alles andere wird nur gemeldet. |
| `paths` | vor dem Preflight | Legt einen fehlenden Migrationsordner der Website und `var/<site>/migration` an. |
| `modes` | vor dem Preflight | Ergänzt den Ordnern und Dateien der Migrationsordner und von `var/<site>/migration` die Rechte aus `site.ini [FileSettings]`, die ihnen fehlen (nimmt nie Rechte weg; Dateien eines anderen Benutzers werden gemeldet). |
| `caches` | nach dem Umschalten | Leert dort die Caches mit dem Tag `ini` und den Cache der Template-Overrides. |

```ini
[RemoteSettings]
SelfHeal[]
SelfHeal[]=stale-lock
SelfHeal[]=modes
SelfHeal[]=caches
```

Auf der Gegenstelle selbst zeigt `./console exp:migration:heal --fix=stale-lock,paths,modes`, was es tun würde;
mit `--apply` tut es das.

Nie korrigiert, nur gemeldet: eine abweichende PHP- oder Erweiterungsversion, ein Fehler der Migration selbst, eine
Prüfsumme, die der Schutz ablehnt.

**Die Prüfungen des Preflights** lesen den Stand der Gegenstelle (er nennt ihre PHP-Version, ihre Erweiterungen mit
Versionen und den freien Platz ihres Dateisystems) und halten den Deploy an, bevor etwas gesendet wird:

| Einstellung in `[RemoteSettings]` | Vorgabe | Was den Deploy anhält |
|---|---|---|
| `DiskMargin` | `50M` | Weniger freier Platz dort als die gesendeten Dateien plus der Reserve |
| `PhpCheck` | `major` | Eine andere Hauptversion von PHP dort (`minor`: eine andere Haupt- und Nebenversion; `warn`: nur eine Warnung; `off`) |
| `ExtensionCheck` | `migrations` | Eine Erweiterung, deren Migrationen gesendet werden, ist dort nicht aktiv oder hat eine andere Version (`all`: jede aktive Erweiterung; `warn`: nur Warnungen; `off`) |

Eine Gegenstelle mit älterem Kernel, die das nicht meldet, wird nicht geprüft; der Bericht sagt es in einer Warnung.

## Status und Verlauf

```
./console exp:migration:remote:status
./console exp:migration:remote:status --remotes=production --format=json
```

liest den Migrationsstatus jeder Gegenstelle parallel und listet je Gegenstelle ihre Kernel-Version, die Laufsperre und
wie viele Migrationen ausgeführt, ausstehend und gescheitert sind, dann, ob die Gegenstellen **im Gleichstand** sind
(jede Migration auf allen ausgeführt oder auf keiner, nichts gescheitert), und die letzten Deploys dieser Installation.

Jeder Deploy, der etwas sendet oder ausführt, behält seinen Bericht (`exp-migration-deploy/1`) in
`var/<site>/migration/deploy/reports/<Deploy-ID>.json`, und das Audit-Protokoll verzeichnet ihn
(`system.migration.deploy`; `system.migration.deploy.rollback`, wenn Gegenstellen zurückgesetzt wurden).

## Deploys in Stufen, Tore und Rücknahme

`--staged` (mit `--apply`) teilt die ausgewählten Gegenstellen nach ihrer `Environment` in Stufen ein, in der
Reihenfolge von `[RemoteSettings] StageOrder[]` (alpha, dev, test, stage, beta, production, post, arch; eine Umgebung,
die nicht aufgeführt ist, kommt zuletzt). Der Preflight läuft trotzdem zuerst auf jeder Gegenstelle. Dann wird die
erste Stufe übertragen, geprüft und umgeschaltet, alles oder nichts innerhalb der Stufe, und der Deploy endet im Stand
`gated`: Die späteren Gegenstellen sind `waiting`.

```bash
./console exp:migration:deploy --remotes=all --staged --apply
./console exp:migration:deploy --continue=20261102-120000-a1b2c3 --apply     # das Tor freigeben: die nächste Stufe
./console exp:migration:deploy --rollback=20261102-120000-a1b2c3 --apply     # jede Gegenstelle zurücknehmen, die ihn angewendet hat
```

`--continue=<Deploy-ID>` gibt das Tor frei: Die nächste Stufe wird erneut vorbereitet und geprüft (es ist Zeit
vergangen) und genauso umgeschaltet; der Bericht hält fest, wer jedes Tor freigegeben hat. Scheitert eine spätere
Stufe, wird sie zurückgenommen, und die Stufen davor bleiben ausgeliefert (der Bericht sagt, dass die Gegenstellen
nicht im Gleichstand sind). `--rollback=<Deploy-ID>` nimmt jede Gegenstelle zurück, die einen Deploy angewendet hat
(`exp:migration:rollback <Deploy-ID> --apply` auf jeder, alle gleichzeitig), ob in Stufen oder nicht.

Mit dem Aktualisierungsschritt `6.0.15-migration-deploy-tables` wird jeder gespeicherte Bericht auch in die Tabellen
`exp_migration_deploy` (eine Zeile je Deploy) und `exp_migration_deploy_host` (eine Zeile je Gegenstelle) geschrieben.
Ein Deploy, der scheitert, und ein Deploy in Stufen, der an einem Tor wartet, senden einen Hinweis als reinen Text per
E-Mail an `[RemoteSettings] NotifyEmail` (leer: site.ini `[MailSettings] AdminEmail`); `Notify[]` nennt die Arten
(`failure`, `gate`, `success`).

All das gibt es auch in der Administration: [Setup > Migrationen > Gegenstellen und Deploys](../hilfe/migrationen/gegenstellen-und-deploys.md)
(der Assistent einer Gegenstelle, Verbindung testen, ein Deploy mit Fortschritt live je Gegenstelle, der Verlauf, das
Tor und die Rücknahme).

## Rezepte

**Die Migrationen von beta auf production bringen.** Auf beta: `./console exp:migration:deploy --remotes=production`
(der Probelauf), den Plan lesen, dann dasselbe mit `--apply`.

**dev, stage, beta und production im Gleichstand halten.** Alle in `RemoteList[]` aufnehmen, mit `--remotes=all`
übertragen und danach `./console exp:migration:remote:status` prüfen: „In sync: yes“.

**Nur eine Migration.** `--only=20261102090000_add_news_class` sendet und führt nur diese auf jeder Gegenstelle aus.

**Dasselbe in der Administration.** Jeder Schritt oben hat einen Bildschirm in Setup > Migrationen, mit
Bildschirmfotos erklärt in [Eine Gegenstelle anlegen, Schritt für Schritt](../hilfe/migrationen/eine-gegenstelle-anlegen-schritt-fuer-schritt.md)
und [Ausliefern, Schritt für Schritt](../hilfe/migrationen/ausliefern-schritt-fuer-schritt.md).

## Fehlerbehebung

| Symptom | Ursache und Abhilfe |
|---|---|
| `not reachable or no migration status (exit 255)` | ssh konnte sich nicht anmelden oder verbinden: Host, Port, Benutzer, Schlüssel oder `authorized_keys` der Gegenstelle. Versuchen Sie `ssh -p <Port> <Benutzer>@<Host>` von Hand. |
| `Permission denied (publickey)` | Der öffentliche Schlüssel des Geheimnisses steht nicht in `authorized_keys` des Benutzers auf der Gegenstelle. Wird nicht wiederholt. |
| `the host key ... does not have the fingerprint` | `HostKeyFingerprint` passt nicht zum Schlüssel der Gegenstelle. Prüfen Sie ihn dort mit `ssh-keygen -l -E sha256 -f /etc/ssh/ssh_host_ed25519_key.pub`. |
| `a migration run holds the lock there` | Dort läuft ein Lauf, oder ein abgestürzter Lauf hat seine Sperre hinterlassen; siehe `exp:migration:status` auf der Gegenstelle. |
| `the kernel version there is ...` | Aktualisieren Sie zuerst die Gegenstelle, damit die Migrationen auf demselben Code laufen. |
| `was applied there from a file that differs` | Die Migration wurde geändert, nachdem sie dort ausgeführt wurde. Gleichen Sie die Dateien an oder akzeptieren Sie sie dort mit `exp:migration:migrate --force --reason`. |
| `captions are off there` | Schalten Sie in `[CaptionSettings]` der Gegenstelle die Sicherungspunkte ein; ein Deploy braucht sie, um eine Gegenstelle zurückzusetzen. |
| Zustand `out-of-sync` | Lesen Sie den Stand jeder Gegenstelle im Bericht und bereinigen Sie die, die nicht zurückgesetzt werden konnte, von Hand. |
| `an FTP remote cannot run the migrations` | Geben Sie ihr den HTTP-Trigger (`Trigger`, `TriggerSecret` und dort `[TriggerSettings]`), oder übertragen Sie mit `--transfer-only`. |
| `not reachable or no migration status` mit `"status":403` oder `401` darin | Der Trigger hat die Anfrage abgelehnt: Die Adresse der Quelle steht nicht in `AllowedAddresses[]`, die Geheimnisse unterscheiden sich, oder die Uhren liegen mehr als `Lifetime` Sekunden auseinander. |
| `only ... MB free there` | Schaffen Sie Platz auf der Gegenstelle oder senken Sie `DiskMargin`. |
| `PHP there is ...` / `the extension ... there` | Bringen Sie die Gegenstelle auf dieselben Versionen oder lockern Sie `PhpCheck` / `ExtensionCheck`. |
| `stale-lock: the lock is held ... and left alone` | Die Selbstheilung fand eine Sperre, die sie nicht nehmen darf: Ihr Prozess läuft noch, sie ist jünger als `StaleLockAge`, oder sie wird auf einem anderen Host gehalten. Prüfen Sie sie dort. |
