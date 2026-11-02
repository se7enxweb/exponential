# Statisches Veröffentlichen: Repositorys und Zugangsdaten

Diese Anleitung ist für Administratoren und Entwickler, die eine statische Kopie einer Exponential-6.0.15+-Website zu
einem Git-Host veröffentlichen: GitHub Pages, GitLab Pages, Gitea oder ein Repository auf dem eigenen Server. Sie
erklärt, wo das Git-Repository der Kopie liegt, was eine Veröffentlichung committet, wie sich der Push anmeldet (ein
Token, ein SSH-Deploy-Key oder die Zugangsdaten des Hosts) und wie ein fehlgeschlagener Push erneut gesendet wird. Am
Ende baut, committet, taggt und pusht `./console exp:static:publish <ziel>` Ihre Website in einem Schritt.

[Anleitungen](README.md) · Verwandt: [Statische Websites auf GitHub Pages hosten](statische-websites-auf-github-pages-hosten.md),
[Verlauf und Zurücknehmen](statisches-veroeffentlichen-verlauf-und-zuruecknehmen.md),
[SSH-Deploy-Keys](statisches-veroeffentlichen-deploy-keys.md), [Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md),
[Geheimnisspeicher](../funktionen/6.0/geheimnisspeicher.md)

## Was Sie am Ende haben

- Den Ordner des Ziels `var/<site>/static-publish/<ziel>/` als eigenes Git-Repository, auf dem Branch des Ziels, mit
  dem `Remote` des Ziels als `origin`.
- Jede Veröffentlichung als ein Commit, der nur die Dateien des Builds enthält, getaggt mit `static/<ziel>/1`,
  `static/<ziel>/2` und so weiter.
- Einen Push, der sich anmeldet, ohne dass ein Token je auf einer Befehlszeile, in einer Remote-Adresse oder in einem
  Log steht.
- Einen Eintrag jeder Veröffentlichung in der Tabelle `exp_static_run`, angezeigt von `exp:static:status`.

## Bevor Sie beginnen

- Ein Ziel, das baut: [Statische Websites auf GitHub Pages hosten](statische-websites-auf-github-pages-hosten.md),
  Schritte 1 bis 4.
- git auf dem Server (`git --version`) und OpenSSH für einen Deploy-Key (`ssh -V`).
- Die Tabellen des statischen Veröffentlichens: `./console exp:upgrade --apply` führt den Schritt
  `6.0.15-static-publish-tables` aus. Veröffentlichen geht auch ohne sie, aber dann wird kein Lauf aufgezeichnet und
  `exp:static:push` findet nichts zu wiederholen.
- Ein leeres Repository beim Git-Host, oder eines, auf dessen Veröffentlichungs-Branch Exponential committen darf.

## 1. Das Repository nennen

Ergänzen Sie `Remote`, `Branch` und, für eine Website in `/docs`, `Folder` beim Ziel in
`settings/override/staticcache.ini.append.php`:

```ini
[Target_github-public]
SiteAccess=site
BasePath=/my-site/
PublicURL=https://owner.github.io/my-site/
Remote=https://github.com/owner/my-site.git
Branch=gh-pages
Folder=
Credential=token:github_pages_token
```

| Einstellung | Bedeutung |
|---|---|
| `Remote` | Die Git-Adresse: `https://...`, `git@host:owner/repo.git`, `ssh://...` oder ein Pfad auf diesem Server. Nie ein Passwort oder Token darin; `exp:static:targets` lehnt das ab. |
| `Branch` | Der Branch, auf den die Website veröffentlicht wird: `gh-pages`, oder `main` mit `Folder=docs`. |
| `Folder` | Leer: die Website ist der ganze Branch. `docs`: die Website liegt in `docs/` des Branches, neben Ihren anderen Dateien (GitHub Pages liefert beides aus). |
| `Credential` | Wie sich der Push anmeldet, siehe unten. Nur ein Name; der Wert bleibt im Geheimnisspeicher oder in der Umgebung. |

Das Repository ist der Ordner des Ziels selbst. Es liegt nie im eigenen Repository Ihrer Website, deshalb kann eine
Veröffentlichung nie Ihren Code, Ihre Einstellungen oder `var/` committen.

## 2. Wählen, wie sich der Push anmeldet

| Credential | Wann | Remote |
|---|---|---|
| `token:<Name des Geheimnisses>` | Ein fein granuliertes GitHub-Token oder ein GitLab- oder Gitea-Zugriffstoken, gespeichert unter Setup > Geheimnisse | `https://...` |
| `token:env:<VARIABLE>` | Dasselbe Token aus einer Umgebungsvariablen, etwa in CI | `https://...` |
| `sshkey:<Name des Geheimnisses>` | Ein Deploy-Key für genau dieses Repository ([SSH-Deploy-Keys](statisches-veroeffentlichen-deploy-keys.md)) | `git@host:owner/repo.git` |
| `none` | Die Zugangsdaten des Hosts: ein SSH-Agent, ein Credential-Helper oder ein Remote auf diesem Server | jede |
| `app:<Installation>` | Eine GitHub-App-Installation: ein für jeden Push erstelltes Token der App, die in `GitHubAppId` und `GitHubAppKey` gesetzt ist ([Mit GitHub verbinden](../hilfe/statisches-veroeffentlichen/mit-github-verbinden-token-key-oder-app.md)) | `https://github.com/...` |

### Ein Token

1. Öffnen Sie bei GitHub **Settings > Developer settings > Personal access tokens > Fine-grained tokens** und
   erstellen Sie ein Token für das eine Repository mit **Contents: Read and write**. Bei GitLab ein
   Projekt-Zugriffstoken mit `write_repository`.
2. Öffnen Sie in Exponential **Setup > Geheimnisse**, legen Sie ein Geheimnis `github_pages_token` an und fügen Sie das
   Token ein. Oder legen Sie es in die Umgebung des Prozesses, der veröffentlicht: `Credential=token:env:GH_PAGES_TOKEN`.
3. Setzen Sie beim Ziel `Credential=token:github_pages_token`.

Wie es zu git gelangt: Der Push läuft mit `GIT_ASKPASS` auf `bin/shell/expstaticaskpass.sh`, und das Token steht in
der Umgebungsvariablen `EXP_STATIC_GIT_TOKEN` genau dieses einen git-Prozesses. git fragt das Hilfsprogramm nach
Benutzername und Passwort; es antwortet aus der Umgebung. Credential-Helper des Hosts sind für diesen Aufruf
abgeschaltet, damit kein gespeichertes Passwort an seiner Stelle antwortet. Das Token steht nie auf einer Befehlszeile
(wo `ps` es zeigen würde), nie in der Remote-Adresse in `.git/config`, in einem Bericht oder einer Logzeile, und jede
Ausgabe von git, die es zitiert, wird vor dem Anzeigen oder Speichern zu `[secret token:github_pages_token]`
geschwärzt.

### None

Mit `Credential=none` meldet sich git so an, wie es der Systembenutzer täte, der den Befehl ausführt: mit seinem
SSH-Agenten und seinen Schlüsseln oder einem für ihn eingerichteten Credential-Helper. git wartet nie auf ein Passwort
(`GIT_TERMINAL_PROMPT=0`, ssh im `BatchMode`); fehlende Zugangsdaten lassen den Push sofort scheitern, und er wartet
auf eine Wiederholung.

## 3. Veröffentlichen

```bash
./console exp:static:publish github-public --build
```

```text
Building github-public
...
Static publishing: publish of github-public, branch gh-pages in var/site/static-publish/github-public
  made var/site/static-publish/github-public a git repository on the branch gh-pages
  the remote has no branch gh-pages yet: the first push makes it
  committed 4e1f0c9a2b on gh-pages: 3547 added, 0 changed, 0 removed
  tagged static/github-public/1
  pushed gh-pages and the tags static/github-public/* to https://github.com/owner/my-site.git
  Commit: 4e1f0c9a2b7d, tag static/github-public/1
  Push:   pushed (https://github.com/owner/my-site.git, credential token:github_pages_token)
PASS
```

- `--build` baut zuerst (wie `exp:static:build`); ohne es wird der letzte Build im Ordner veröffentlicht. Wird der
  Build nicht fertig (etwa weil eine Seite abgelehnt wurde), wird nichts committet oder gepusht.
- `--message="Neue Öffnungszeiten"` setzt die Commit-Nachricht. Ohne sie nennt sie das Ziel, die Seiten und Dateien,
  den Lauf und wann gebaut wurde.
- `--no-push` committet und taggt nur; `exp:static:push` pusht später. `[StaticPublishSettings] Push=disabled` tut
  dasselbe für jede Veröffentlichung.
- `--format=json` gibt den Bericht aus (`exp-static/1`): `state`, `commit_id`, `tag_name`, `push_state`, die Zahlen.

Die erste Veröffentlichung macht den Ordner zu einem Repository. Hat das Remote den Branch schon (eine bestehende
Website, ein Branch mit README), wird sein Verlauf übernommen: Die Veröffentlichung wird ein Commit darauf, und die
Dateien dieses Branches, die kein Build geschrieben hat, bleiben.

### Was eine Veröffentlichung committet

Nur die Dateien des Builds: jede Datei, die das Manifest `.static-publish/manifest.json` aufführt, das Manifest selbst,
und das Löschen von Dateien, die das zuletzt committete Manifest aufführte und dieses nicht mehr. Eine Datei, die Sie
selbst in den Branch legen (eine README, ein von Hand gepflegtes `CNAME`, eine Workflow-Datei), wird von einer
Veröffentlichung nie committet, geändert oder entfernt. Das Manifest des vorigen Builds
(`.static-publish/manifest.previous.json`) bleibt lokal.

Eine Veröffentlichung ohne Neues committet und taggt nichts (`state: unchanged`).

### Der Committer

Die Commits nennen als Committer `CommitName` und `CommitEmail` aus `[StaticPublishSettings]`
(`Exponential static publishing`, `static-publish@<Hostname>`), es sei denn, die eigene git-Konfiguration des
Repositorys nennt einen (`git -C var/site/static-publish/github-public config user.email sie@example.com`).

## 4. Wenn ein Push scheitert

Ein gescheiterter Push (der Host nicht erreichbar, ein Token abgelaufen, ein Geheimnis fehlt) lässt Commit und Tag im
Ordner des Ziels. Der Push-Zustand des Laufs ist `pending`, der Befehl endet mit Exit 1 und sagt, warum:

```text
FAIL: the push failed (unable to access 'https://github.com/owner/my-site.git/': Could not resolve host: github.com): the commit stays local and exp:static:push or the cronjob part staticpublish tries again
```

Beheben Sie die Ursache und pushen Sie erneut:

```bash
./console exp:static:push github-public
./console exp:static:push                 jedes Ziel mit einem wartenden Push
```

Ein Push sendet den Branch mit allen Commits und alle Tags des Ziels, daher sendet ein Push jede Veröffentlichung, die
gewartet hat. Damit das von selbst geschieht, nehmen Sie den Cronjob-Teil `staticpublish` in eine Cronjob-Gruppe auf,
die die Website ausführt, zum Beispiel alle 15 Minuten, in `settings/override/cronjob.ini.append.php`:

```ini
[CronjobPart-frequent]
Scripts[]=staticpublish.php
```

oder führen Sie ihn allein aus: `php runcronjobs.php staticpublish`.

Die Push-Zustände eines Laufs: `pushed`, `pending` (wartet auf eine Wiederholung), `disabled` (`--no-push` oder
`Push=disabled`), `no-remote` (das Ziel hat kein `Remote`), `none` (nichts committet).

### Nie erzwungen

Eine Veröffentlichung schreibt den Verlauf des Remotes nie um: kein `--force`, keine `+`-Refspec, kein Löschen (der
Code lehnt sie ab, bevor git läuft). Hat jemand anderes auf den Veröffentlichungs-Branch gepusht, wird der Push als
kein Fast-Forward abgelehnt und wartet; der Branch gehört dem statischen Veröffentlichen, committen Sie also nicht von
Hand darauf. Tags werden nie verschoben oder ersetzt.

## 5. Den Zustand sehen

```bash
./console exp:static:status github-public
```

```text
Static publishing: github-public
  Folder:     var/site/static-publish/github-public
  Remote:     https://github.com/owner/my-site.git, branch gh-pages
  Credential: token:github_pages_token (readable)
  Repository: 4e1f0c9a2b7d "Publish github-public: 560 pages, 3547 files", 1 tag(s), last static/github-public/1; 0 change(s) not published, 0 commit(s) not pushed
  Pages:      560 published, 0 stale; 0 run(s) wait for a push

  Run                     Kind      State      Tag                          Push       By
  20261101-120000-4f2a9c  publish   done       static/github-public/1       pushed     cli alpha
PASS
```

„readable“ heißt, das Geheimnis kann gelesen werden; der Wert wird nie gezeigt.

## Eins nach dem anderen

Ein Build, eine Veröffentlichung, ein Zurücknehmen oder ein Push eines Ziels nimmt die Sperre des Ziels
(`var/<site>/static-publish/.locks/<ziel>.lock`). Ein zweiter Vorgang, der derweil startet, von der Konsole, einem
Cronjob oder später der Administration, endet mit `another process works on the target github-public (publish by
process 4711 since ...)`. Die Sperre gehört zum Prozess, ein abgestürzter Build blockiert also nie den nächsten.

## Dateien und Besitzer

Führen Sie die Befehle als der Systembenutzer aus, dem die Dateien der Website gehören (`sudo -u <benutzer>
./console ...`). Ein Ordner oder `.git`, der einem anderen Benutzer gehört, wird abgelehnt, denn Dateien, die git als
dieser Benutzer schreibt, würden den nächsten Build stören. Unter Windows laufen die Befehle unverändert: git wird ohne
Shell gestartet, Pfade verwenden `/`, und die Datei des Deploy-Keys wird mit den Rechten des aktuellen Benutzers
geschrieben.

## Ziele in eine andere Installation mitnehmen

`./console exp:static:targets --export=all` (oder `--export=github-public,docs`) gibt die Ziele als INI-Migration
aus: ihre Blöcke `[Target_<id>]` und `TargetList[]`. Legen Sie sie im Migrationsordner der anderen Installation ab
(`YYYYMMDDHHMMSS_static_targets.ini`) oder in einem Site-Paket, und führen Sie sie dort aus. Ein Credential reist als
Name seines Secrets (`token:github_pages_token`), nie als Wert: Legen Sie den Wert unter Setup > Secrets der anderen
Installation ab. Die Migration schaltet das statische Veröffentlichen nicht ein.

Auch ein Paket nimmt Ziele mit: `php bin/php/ezpm.php -r <repository> add <paket> expstatictarget github-public`
(IDs durch Kommas getrennt, oder `all`) legt jedes als `statictargets/target-<id>.xml` ab. Es reisen nur die
Einstellungen des Assistenten; das Credential reist als Name, ein Benutzer oder Passwort in der Git-Adresse bleibt
weg, ebenso der Webhook, dessen Adresse oft selbst ein Secret ist. Beim Installieren des Pakets wird ein Ziel, das die
Installation noch nicht hat, in `settings/override/staticcache.ini.append.php` geschrieben (protokolliert wie ein
Speichern im Assistenten); ein vorhandenes bleibt, wie es ist. Das Deinstallieren lässt die Einstellungen in Ruhe:
Entfernen Sie ein Ziel unter Setup > Statisches Veröffentlichen.

## Fehlerbehebung

| Symptom | Ursache und Abhilfe |
|---|---|
| `holds no build (no manifest)` | Erst bauen, oder mit `--build` veröffentlichen |
| `is on the branch main, not gh-pages` | Jemand hat im Ordner den Branch gewechselt; `git -C <ordner> checkout gh-pages`, oder `Branch` setzen |
| `has staged changes that are not the build's` | Im Ordner wurde etwas von Hand vorgemerkt; dort committen oder zurücknehmen |
| `the secret github_pages_token is not set` | Unter Setup > Geheimnisse anlegen, oder `token:env:VARIABLE` verwenden |
| `Credential=app:... needs the GitHub integration` | PHP fehlt curl oder openssl, oder `[StaticPublishSettings] GitHubAPI=disabled`; ein Token oder einen Deploy-Key verwenden |
| `the GitHub App installation ... gave no token` | `GitHubAppId` oder `GitHubAppKey` ist nicht gesetzt, der Schlüssel gehört nicht zur App, oder die App ist nicht auf dem Repository installiert |
| `not pushed: the build is ..., more than the ... GitHub Pages allows a site` | Jeder Push eines GitHub-Pages-Ziels wird gegen die Grenzen von GitHub geprüft; den Build verkleinern (`Assets=referenced`, `ExcludePaths[]`) ([Grenzen](../hilfe/statisches-veroeffentlichen/grenzen-und-bedingungen-von-github-pages.md)) |
| Push `pending`, `rejected (fetch first)` | Der Branch hat Commits, die nicht von hier veröffentlicht wurden; nicht von Hand auf den Veröffentlichungs-Branch committen |
| `another process works on the target` | Warten, bis der andere Build oder die andere Veröffentlichung fertig ist |

## Nächster Schritt

[Verlauf und Zurücknehmen](statisches-veroeffentlichen-verlauf-und-zuruecknehmen.md): zu einer früheren
Veröffentlichung zurückkehren, ohne den Verlauf zu verlieren.
