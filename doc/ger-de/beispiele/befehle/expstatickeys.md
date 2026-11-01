---
title: exp:expstatickeys
audience: developer, admin
category: examples
summary: "Den SSH-Deploy-Key eines Ziels des statischen Veröffentlichens erzeugen oder anzeigen: ed25519, die private Hälfte im Geheimnisspeicher, die öffentliche zum Hinzufügen beim Repository."
keywords: Konsole, Befehl, exp:expstatickeys, expstatickeys, console, command, CLI, expstatickeys.php, exp:static:keys
type: generated
generated: exp:help:examples
---
# exp:expstatickeys

Den SSH-Deploy-Key eines Ziels des statischen Veröffentlichens erzeugen oder anzeigen: ed25519, die private Hälfte im Geheimnisspeicher, die öffentliche zum Hinzufügen beim Repository.

Skript `bin/php/expstatickeys.php`, Code in `kernel/private/classes/commands/expstatickeys.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expstatickeys --help
./console exp:static:keys --help
php bin/php/expstatickeys.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The SSH deploy key of a static publishing target
With --generate it makes a key pair for the target (ed25519, with ssh-keygen), stores the private half
in the secret store (Setup > Secrets, never in a file of the site) and prints the public half: add it
to the repository as a deploy key with write access, set the target's Credential=sshkey:<secret> and
its Remote to the SSH address (git@github.com:owner/repo.git). Without --generate it shows the public
half and the fingerprint of the key the target has.

./console exp:static:keys github-public --generate
./console exp:static:keys github-public
./console exp:static:keys github-public --generate --replace       a new key; the old one stops working
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--generate` |  | Einen neuen Deploy-Key (ed25519) erzeugen und seine private Hälfte im Geheimnisspeicher ablegen |
| `--replace` |  | Mit --generate: den Deploy-Key des Ziels ersetzen (der alte funktioniert nicht mehr) |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-static/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[target]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expstatickeys.php`, `kernel/private/classes/commands/expstatickeys.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
