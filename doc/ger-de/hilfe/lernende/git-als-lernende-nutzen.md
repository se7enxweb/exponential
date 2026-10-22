---
title: Git als Lernende nutzen
audience: student
category: learning
order: 45
summary: Wie Lernende Git mit Exponential nutzen - drei Repositorys, die alle Lernenden führen (der Kernel-Klon auf einem eigenen Branch, eigene Erweiterungen, eigene Notizen), tägliche Gewohnheiten, aus der Historie des Projekts lernen, Commit-Nachrichten im Stil des Projekts, Geheimnisse heraushalten und wo Mit Git entwickeln und Mit Git aktualisieren weitergehen.
keywords: git, versionsverwaltung, klon, branch, commit, historie, log, blame, diff, repository, commit-nachricht, geheimnisse, gitignore, lernende, mit git entwickeln, version control, clone, history, commit message, secrets, student, develop with git
type: help
---
# Git als Lernende nutzen

Mit Git wird Exponential entwickelt, installiert (als Klone) und verbessert. Diese Seite zeigt, wie Lernende es
täglich nutzen. Die vollständige Anleitung ist [Mit Git entwickeln](../mit-git-entwickeln.md); die Befehle sind in der
[Git-Kurzreferenz für Lernende](git-kurzreferenz-fuer-lernende.md) gesammelt.

## Inhalt

1. [Drei Repositorys, die Lernende führen](#drei-repositorys-die-lernende-führen)
2. [Tägliche Gewohnheiten](#tägliche-gewohnheiten)
3. [Aus der Historie lernen](#aus-der-historie-lernen)
4. [Commit-Nachrichten](#commit-nachrichten)
5. [Geheimnisse heraushalten](#geheimnisse-heraushalten)
6. [Auf dem neuesten Stand bleiben](#auf-dem-neuesten-stand-bleiben)

## Drei Repositorys, die Lernende führen

| Repository | Was es enthält | Begonnen mit |
|---|---|---|
| Der Kernel-Klon | Exponential mit seiner vollständigen Historie, auf Ihrem eigenen Branch `my-site` | [Aus Git-Klonen zum Lernen installieren](aus-git-klonen-zum-lernen-installieren.md) |
| Ihre Erweiterungen | Ein Repository pro Erweiterung oder Design, das Sie schreiben, in `extension/<name>` | `git init` darin ([Projekt 8](projekt-08-ihre-erste-erweiterung.md)) |
| Ihre Notizen | Ihr Lerntagebuch, Berichte und Abgaben | `git init` in einem Ordner außerhalb der Installation ([Ein Lerntagebuch führen](ein-lerntagebuch-fuehren.md)) |

Die Erweiterungen, die Composer installiert, sind ebenfalls eigene Repositorys; `git status` im Wurzelverzeichnis
listet sie als nicht versionierte Ordner auf, was so erwartet ist ([Mit Git entwickeln](../mit-git-entwickeln.md#prüfen-ob-es-funktioniert-hat)).

## Tägliche Gewohnheiten

1. Beginnen Sie die Sitzung mit `git status` in dem Repository, das Sie ändern werden.
2. Arbeiten Sie auf einem Branch, niemals direkt auf dem Tag eines anderen oder auf `main` des Projekts.
3. Committen Sie kleine Schritte mit klaren Nachrichten; ein Commit pro Idee.
4. Beenden Sie die Sitzung mit `git status` und `git log --oneline -5`: Ist alles committet?
5. Pushen Sie Ihre eigenen Repositorys auf Ihr eigenes Konto bei einem Git-Hoster, damit ein kaputter Laptop Ihre
   Arbeit nicht mitnimmt.

## Aus der Historie lernen

Die Historie des Kernels ist ein Lehrbuch, geschrieben von seinen Entwicklern. Aus [Mit Git entwickeln](../mit-git-entwickeln.md#herausfinden-warum-und-wann):

```bash
git log --oneline -10 -- kernel/content/view.php   # the last ten changes to one file
git blame kernel/content/view.php                   # who last changed each line
git log -S "CheckValidity" --oneline                # every commit that added or removed a word
```

Übung: Wählen Sie eine Einstellung, der Sie in einem Lernprojekt begegnet sind, finden Sie mit `git log -S` den
Commit, der sie eingeführt hat, und erklären Sie in Ihrem Tagebuch, warum sie hinzugefügt wurde.

## Commit-Nachrichten

Die Commit-Betreffzeilen des Projekts beginnen mit dem, was die Änderung tut: `Added: `, `Updated: `, `Fixed: `,
`Removed: ` oder `Renamed: ` ([Richtlinien für Mitwirkende](../../../../CONTRIBUTING.md#making-changes)). Verwenden Sie
denselben Stil in Ihren eigenen Repositorys; das macht Ihre Historie lesbar und Ihre künftigen Pull Requests
vertraut:

```text
Added: The club event class and three sample events
Fixed: The event template escapes the title
Updated: The search test plan after the index rebuild
```

## Geheimnisse heraushalten

- Committen Sie niemals `settings/override/` aus einer Installation, `kickstart.ini` (es enthält das
  Datenbankpasswort, [Erste Schritte](../../anleitungen/erste-schritte.md#33-aus-einer-datei-wiederholbare-installationen)),
  API-Schlüssel oder Passwörter.
- Verwenden Sie in Ihren eigenen Repositorys eine `.gitignore` für Dateien, die lokal bleiben müssen.
- Wenn ein Geheimnis committet wurde, behandeln Sie es als öffentlich: Ändern Sie das Passwort oder widerrufen Sie den
  Schlüssel und entfernen Sie es dann aus dem Repository.

## Auf dem neuesten Stand bleiben

Zuerst Git (der Kernel), dann Composer (die Erweiterungen):

```bash
git fetch origin --tags
git merge origin/main
composer update
```

`composer update`, nicht `composer install`, weil sich die `composer.json` des Kernels geändert hat
([Mit Git entwickeln](../mit-git-entwickeln.md#auf-dem-neuesten-stand-bleiben)). Der ganze Ablauf, mit der Datenbankhälfte eines
Upgrades, steht in [Mit Git aktualisieren](../mit-git-aktualisieren.md). Wenn Composer sich wegen Ihrer eigenen Änderungen
weigert, lesen Sie [Wenn Composer sich weigert](../mit-git-entwickeln.md#wenn-composer-sich-weigert).

## Verwandte Seiten

- [Git-Kurzreferenz für Lernende](git-kurzreferenz-fuer-lernende.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Ihr erster Beitrag, Schritt für Schritt](ihr-erster-beitrag-schritt-fuer-schritt.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
