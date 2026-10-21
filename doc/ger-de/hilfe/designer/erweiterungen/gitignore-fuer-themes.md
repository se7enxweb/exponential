---
title: "Eine .gitignore für Themes"
audience: designer
category: theme-extensions
order: 290
summary: Was die .gitignore eines Themes aus dem Repository heraushält - Editor-Reste, Sicherungen, Logs, Build-Ausgaben und lokale Einstellungen -, die Datei, die der Design-Assistent schreibt, und wie Sie prüfen, dass nichts Geheimes oder Privates hineingerutscht ist.
keywords: gitignore, ignorieren, Sicherungen, Editor-Dateien, node_modules, vendor, Logs, Geheimnisse, git status, ignore, backups, editor files, logs, secrets
type: help
---
# Eine .gitignore für Themes

Diese Seite ist für Designer, die ein Theme-Repository vorbereiten. Eine `.gitignore` hält Dateien, die nie
veröffentlicht werden sollten, aus jedem Commit heraus.

## Was der Design-Assistent schreibt

```
# Editor leftovers
*~
#*#
.#*
*.orig
*.rej
*.bak
*.swp
*.swo

# Operating system
.DS_Store
Thumbs.db

# Build output and dependencies
/vendor/
/node_modules/
*.log
```

## Was Sie für ein Theme ergänzen

| Ergänzen | Wann |
|---|---|
| `var/` | Ihre Skripte schreiben Caches oder Logs innerhalb des Themes |
| `share/*.bak*`, `share/*.backup-*` | Sie sichern `.dba`-Dateien daneben (so wie `sevenx_themes_media` `share/db_data.dba.bak.*` ignoriert) |
| `/design/*/stylesheets/*.map` | Sie bauen CSS aus Quellen und liefern keine Source Maps mit |
| `.env`, `*.key`, `*.pem` | In einem Theme nie nötig; ignorieren Sie sie, damit sie nicht versehentlich committet werden |

Kompilieren Sie Stylesheets aus Sass oder einer anderen Quelle, entscheiden Sie, ob Sie das Ergebnis committen.
Installationen führen Ihre Build-Werkzeuge nicht aus, also muss das kompilierte CSS normalerweise committet werden.

## Schritt für Schritt: vor jedem Release prüfen

1. `git status --short --ignored` zeigt, was ignoriert wird (`!!`) und was nicht.
2. `git ls-files | grep -E '\.(bak|orig|log|key|pem)$|/\.env$'` gibt nichts aus.
3. Durchsuchen Sie die versionierten Dateien nach Geheimnissen:
   `git grep -n -i -E 'password|secret|api[_-]?key|token' -- settings/` und lesen Sie jeden Treffer.
4. Wurde etwas Privates committet und gepusht, entfernt ein neuer Commit es nicht aus der Historie. Ändern Sie das
   Geheimnis (es ist keines mehr) und holen Sie Hilfe, bevor Sie eine veröffentlichte Historie umschreiben.

## Verwandte Seiten

- [Die Geheimnisprüfung, bevor eine Erweiterung paketiert wird](geheimnispruefung-vor-dem-paketieren.md)
- [Ein Theme unter Git stellen](ein-theme-unter-git-stellen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
