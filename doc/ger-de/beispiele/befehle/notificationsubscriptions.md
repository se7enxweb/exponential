---
title: exp:notificationsubscriptions
audience: developer, admin
category: examples
summary: Die Teilbaum-Benachrichtigungsabonnements je Benutzer und Teilbaum auflisten oder die entfernen, deren Inhalt fehlt (Probelauf, außer mit --apply).
keywords: Konsole, Befehl, exp:notificationsubscriptions, notificationsubscriptions, console, command, CLI, notificationsubscriptions.php, exp:notification:subscriptions, exp:notify:subscriptions
type: generated
generated: exp:help:examples
---
# exp:notificationsubscriptions

Die Teilbaum-Benachrichtigungsabonnements je Benutzer und Teilbaum auflisten oder die entfernen, deren Inhalt fehlt (Probelauf, außer mit --apply).

Skript `bin/php/notificationsubscriptions.php`, Code in `kernel/private/classes/commands/notificationsubscriptions.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:notificationsubscriptions --help
./console exp:notification:subscriptions --help
./console exp:notify:subscriptions --help
php bin/php/notificationsubscriptions.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Notification subscriptions
  list                      the subtree subscriptions: user, node, path, class, last change below the node
  remove-missing            remove the subscriptions whose node no longer exists (lists them; --apply removes)

remove-missing without --apply removes nothing: it lists what it would remove. --dry-run is still
accepted and means the same as no flag; --apply together with --dry-run is a usage error (exit 2).

./console exp:notification:subscriptions list --user=editor --limit=20
./console exp:notification:subscriptions list --missing
./console exp:notification:subscriptions remove-missing
./console exp:notification:subscriptions remove-missing --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--user` | erforderlich | nur dieser Benutzer: ein Login oder die Inhaltsobjekt-ID des Benutzers |
| `-q` | erforderlich | nur Knoten, deren Name dies enthält |
| `--class` | erforderlich | nur Knoten dieses Klassenbezeichners |
| `--missing` |  | nur Abonnements, deren Knoten nicht mehr existiert |
| `--limit` | erforderlich | wie viele (Standard 50) |
| `--offset` | erforderlich | so viele überspringen |
| `--addresses` |  | auch die E-Mail-Adresse des Benutzers zeigen (standardmäßig verborgen) |
| `--apply` |  | remove-missing: die Abonnements entfernen (Standard listet sie und entfernt nichts) |
| `--dry-run` |  | remove-missing: der Standard, beibehalten für Skripte, die die Option übergeben: auflisten, nichts entfernen |
| `--json` |  | list: JSON |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/notificationsubscriptions.php`, `kernel/private/classes/commands/notificationsubscriptions.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
