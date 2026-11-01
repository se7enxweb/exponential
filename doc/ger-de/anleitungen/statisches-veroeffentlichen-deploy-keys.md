# Statisches Veröffentlichen: SSH-Deploy-Keys

Diese Anleitung ist für Administratoren, die eine statische Kopie einer Exponential-6.0.15+-Website über SSH zu einem
Git-Host veröffentlichen. Ein Deploy-Key ist ein SSH-Schlüssel, der genau ein Repository öffnet und sonst nichts: Geht
er je verloren, ist nur dieses Repository betroffen, und er läuft nicht ab wie ein Token. Exponential erzeugt den
Schlüssel für das Ziel, legt seine private Hälfte im Geheimnisspeicher ab und zeigt Ihnen die öffentliche Hälfte, die
Sie dem Repository hinzufügen.

[Anleitungen](README.md) · Verwandt: [Repositorys und Zugangsdaten](statisches-veroeffentlichen-repositorys-und-zugangsdaten.md),
[Verlauf und Zurücknehmen](statisches-veroeffentlichen-verlauf-und-zuruecknehmen.md), [Geheimnisspeicher](../funktionen/6.0/geheimnisspeicher.md)

## Was Sie am Ende haben

- Ein ed25519-Schlüsselpaar für ein Ziel, die private Hälfte unter Setup > Geheimnisse (nie in einer Datei der
  Website).
- Die öffentliche Hälfte als Deploy-Key mit Schreibzugriff beim Repository.
- `exp:static:publish`, das mit diesem Schlüssel über SSH pusht.

## Bevor Sie beginnen

- OpenSSH auf dem Server: `ssh -V` und `ssh-keygen` (unter Windows der OpenSSH-Client von Windows oder von Git for
  Windows).
- Der Geheimnisspeicher ist eingerichtet (Setup > Geheimnisse öffnet sich; die Tabelle `exp_secret` existiert).
- Die Tabellen des statischen Veröffentlichens braucht der Schlüssel nicht.

## 1. Den Schlüssel erzeugen

```bash
./console exp:static:keys github-public --generate
```

```text
Static publishing: the deploy key of github-public (secret staticpublish.github-public.deploy_key)
A new key was made. Its public half (add it to the repository as a deploy key with write access; GitHub: Settings > Deploy keys > Add deploy key, Allow write access):

ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIM3k... exponential-static-publish-github-public@web1

Fingerprint: SHA256:Qm1o0B2v6M1N0bUuJmYcZ7m2b6r0n2z1p7l0kq3xYbE
Setting:     Credential=sshkey:staticpublish.github-public.deploy_key
WARN the target's Credential is token:github_pages_token: set Credential=sshkey:staticpublish.github-public.deploy_key to sign in with this key
PASS
```

Der Schlüssel wird mit `ssh-keygen` in einem Ordner erzeugt, den nur der Prozess lesen kann, und sofort von der
Platte entfernt; seine private Hälfte wird verschlüsselt als Geheimnis `staticpublish.github-public.deploy_key`
(Bereich `staticpublish`) gespeichert, seine öffentliche Hälfte als Beschreibung des Geheimnisses. Das Audit zeichnet
`system.staticpublish.credential` mit dem Fingerabdruck auf.

Ein Ziel hat einen Schlüssel. `--generate` lehnt ab, wenn es schon einen hat; `--generate --replace` erzeugt einen
neuen, und der alte funktioniert nicht mehr, sobald Sie ihn beim Repository entfernen.

## 2. Beim Repository hinzufügen

- **GitHub:** im Repository **Settings > Deploy keys > Add deploy key**: ein Titel wie „Exponential static
  publishing“, der öffentliche Schlüssel und **Allow write access**.
- **GitLab:** **Settings > Repository > Deploy keys**, mit **Grant write permissions to this key**.
- **Gitea, Forgejo:** **Settings > Deploy keys**, ohne „Read-only“.
- **Eigener Server:** eine Zeile in `authorized_keys` des git-Benutzers, auf das Repository beschränkt nach den Regeln
  Ihres Git-Servers.

## 3. Verwenden

Setzen Sie beim Ziel `Credential` und ein SSH-`Remote`:

```ini
[Target_github-public]
Remote=git@github.com:owner/my-site.git
Credential=sshkey:staticpublish.github-public.deploy_key
```

Dann veröffentlichen Sie wie immer:

```bash
./console exp:static:publish github-public --build
```

Für jeden Push wird der private Schlüssel in eine Datei geschrieben, die nur der Prozess lesen kann
(`var/<site>/static-publish/.keys/`), in `GIT_SSH_COMMAND` mit `IdentitiesOnly` und `BatchMode` genannt und
überschrieben und entfernt, wenn git fertig ist. Der Schlüssel des Hosts wird beim ersten Push gelernt und in
`var/<site>/static-publish/.known_hosts` behalten; ein Host, dessen Schlüssel sich später ändert, wird abgelehnt.

## Den Schlüssel wieder anzeigen

```bash
./console exp:static:keys github-public
```

zeigt die öffentliche Hälfte und den Fingerabdruck des Schlüssels des Ziels, nie die private Hälfte. Vergleichen Sie
den Fingerabdruck mit dem, den der Git-Host für den Deploy-Key anzeigt.

## Fehlerbehebung

| Symptom | Ursache und Abhilfe |
|---|---|
| `ssh-keygen could not make a key` | OpenSSH auf dem Server installieren |
| `the secret store is not set up` | `./console exp:upgrade --apply`, dann Setup > Geheimnisse |
| Push `pending`, `Permission denied (publickey)` | Der öffentliche Schlüssel fehlt beim Repository oder hat keinen Schreibzugriff |
| Push `pending`, `Host key verification failed` | Der Schlüssel des Hosts hat sich seit dem ersten Push geändert; prüfen Sie warum, dann seine Zeile aus `.known_hosts` entfernen |
| `a deploy key signs in over SSH` | `Remote` auf die SSH-Adresse setzen, `git@github.com:owner/repo.git` |

## Nächster Schritt

[Repositorys und Zugangsdaten](statisches-veroeffentlichen-repositorys-und-zugangsdaten.md): die anderen Wege der
Anmeldung, und was eine Veröffentlichung committet.
