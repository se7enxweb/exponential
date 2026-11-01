---
title: exp:expstaticdns
audience: developer, admin
category: examples
summary: "Die DNS-Einträge einer eigenen Domain für GitHub Pages prüfen: die A-, AAAA- und CNAME-Einträge verglichen mit den Adressen von GitHub, und was zu ändern ist."
keywords: Konsole, Befehl, exp:expstaticdns, expstaticdns, console, command, CLI, expstaticdns.php, exp:static:dns
type: generated
generated: exp:help:examples
---
# exp:expstaticdns

Die DNS-Einträge einer eigenen Domain für GitHub Pages prüfen: die A-, AAAA- und CNAME-Einträge verglichen mit den Adressen von GitHub, und was zu ändern ist.

Skript `bin/php/expstaticdns.php`, Code in `kernel/private/classes/commands/expstaticdns.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expstaticdns --help
./console exp:static:dns --help
php bin/php/expstaticdns.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The DNS check of a custom domain for GitHub Pages
Looks the domain up and compares its records with what GitHub Pages needs: an apex domain
(example.com) has the four A records 185.199.108.153 to 185.199.111.153 (and, optionally, the
AAAA records), a subdomain (www.example.com) a CNAME to <owner>.github.io. It lists what to change.
The owner comes from --owner, or from the Remote of the target whose CustomDomain is the domain.

./console exp:static:dns www.example.com
./console exp:static:dns example.com --owner=se7enxweb --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--owner` | erforderlich | Das GitHub-Konto oder die Organisation, der das Repository gehört (der CNAME muss <owner>.github.io nennen) |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-static/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[domain]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expstaticdns.php`, `kernel/private/classes/commands/expstaticdns.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
