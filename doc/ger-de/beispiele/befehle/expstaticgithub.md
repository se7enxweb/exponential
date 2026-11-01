---
title: exp:expstaticgithub
audience: developer, admin
category: examples
summary: Ein Ziel des statischen Veröffentlichens auf GitHub einrichten (das Repository, GitHub Pages aus dem Branch oder mit Actions, die eigene Domain, HTTPS), seinen Zustand dort zeigen oder sich bei GitHub anmelden.
keywords: Konsole, Befehl, exp:expstaticgithub, expstaticgithub, console, command, CLI, expstaticgithub.php, exp:static:github
type: generated
generated: exp:help:examples
---
# exp:expstaticgithub

Ein Ziel des statischen Veröffentlichens auf GitHub einrichten (das Repository, GitHub Pages aus dem Branch oder mit Actions, die eigene Domain, HTTPS), seinen Zustand dort zeigen oder sich bei GitHub anmelden.

Skript `bin/php/expstaticgithub.php`, Code in `kernel/private/classes/commands/expstaticgithub.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expstaticgithub --help
./console exp:static:github --help
php bin/php/expstaticgithub.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
GitHub for a static publishing target
setup creates the repository when asked (--create-repo, public unless --private), switches GitHub Pages
on from the target's branch and folder or from GitHub Actions, and sets the custom domain and HTTPS.
status shows the repository, GitHub Pages and the state of the last Pages build. Both sign in with the
target's Credential (token: or app:). signin signs a user in with GitHub (an OAuth App's device flow,
[StaticPublishSettings] GitHubOAuthClientId) and keeps the token as the secret --secret.

./console exp:static:github setup github-public --create-repo
./console exp:static:github setup github-public --pages=actions --plan
./console exp:static:github status github-public
./console exp:static:github signin --secret=github_pages_token
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--create-repo` |  | setup: das Repository anlegen, wenn es fehlt (leer; die erste Veröffentlichung füllt es) |
| `--public` |  | Mit --create-repo: ein öffentliches Repository (der Standard; GitHub Pages im kostenlosen Tarif braucht eines) |
| `--private` |  | Mit --create-repo: ein privates Repository (GitHub Pages liefert es nur mit einem bezahlten Tarif aus) |
| `--pages` | erforderlich | setup: branch oder actions statt der Delivery des Ziels, oder off, um GitHub Pages so zu lassen, wie es ist |
| `--domain` | erforderlich | setup: diese eigene Domain statt der CustomDomain des Ziels (none entfernt sie) |
| `--no-https` |  | setup: HTTPS für die eigene Domain nicht erzwingen |
| `--plan` |  | setup: nur sagen, was getan würde, nichts ändern |
| `--client-id` | erforderlich | signin: die Client-ID der OAuth App (Standard [StaticPublishSettings] GitHubOAuthClientId) |
| `--secret` | erforderlich | signin: der Name des Geheimnisses, unter dem das Token abgelegt wird (Setup > Secrets); das Ziel nennt es als Credential=token:<secret> |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-static/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[action][target]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expstaticgithub.php`, `kernel/private/classes/commands/expstaticgithub.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
