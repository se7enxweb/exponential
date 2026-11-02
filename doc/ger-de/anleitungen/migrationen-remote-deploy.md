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
| preflight | Dort wird `exp:migration:status --format=json` gelesen: erreichbar, die Laufsperre frei, keine gescheiterte Migration, dieselbe Kernel-Version wie hier, keine Migration nach dem Ausführen dort geändert. Die zu sendenden Dateien werden geplant. | Nirgends wurde etwas geändert. |
| transfer | Die geplanten Migrationsdateien werden gesendet. | Keine Gegenstelle wurde umgeschaltet; die gesendeten Dateien bleiben dort ausstehend. |
| verify | Ein Probelauf von `exp:migration:migrate` dort muss jede gesendete Datei mit der Prüfsumme dieser Installation sehen, und Sicherungspunkte müssen eingeschaltet sein. FTP: Jede Datei wird zurückgelesen und verglichen. | Keine Gegenstelle wurde umgeschaltet. |
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

FTP kann keine Befehle ausführen; ein Deploy auf eine FTP-Gegenstelle sendet und prüft daher nur die Dateien:

```
./console exp:migration:deploy --remotes=webspace --transfer-only --apply
```

Jede ausgewählte Migration wird gesendet (der Stand der Gegenstelle lässt sich über FTP nicht lesen) und zurückgelesen,
um zu prüfen, dass sie unverändert ankam. Führen Sie die Migrationen dort mit den eigenen Mitteln der Gegenstelle aus.

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

## Rezepte

**Die Migrationen von beta auf production bringen.** Auf beta: `./console exp:migration:deploy --remotes=production`
(der Probelauf), den Plan lesen, dann dasselbe mit `--apply`.

**dev, stage, beta und production im Gleichstand halten.** Alle in `RemoteList[]` aufnehmen, mit `--remotes=all`
übertragen und danach `./console exp:migration:remote:status` prüfen: „In sync: yes“.

**Nur eine Migration.** `--only=20261102090000_add_news_class` sendet und führt nur diese auf jeder Gegenstelle aus.

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

## Noch nicht verfügbar

Die Verwaltungsseiten (Setup > Migrations > Remotes und Deploys), ein gestufter Deploy mit einer Freigabe zwischen den
Umgebungen, der signierte Auslöse-Endpunkt, der Migrationen auf reinen FTP-Hosts ausführen würde, und die automatische
Reparatur einer verwaisten Sperre oder von Dateirechten sind geplant; bis dahin verwenden Sie die Befehle oben.
