---
title: "Ein Theme unter Git stellen"
audience: designer
category: theme-extensions
order: 280
summary: Ein Theme in einem eigenen Git-Repository führen, dessen Wurzel der Erweiterungsordner ist - warum ein Repository pro Theme, wie Sie es beginnen, wie Sie innerhalb einer Installation daran arbeiten und welche Präfixe Sie in Commit-Nachrichten verwenden.
keywords: Git, Repository, git init, Versionsverwaltung, Commit-Nachrichten, Added, Updated, ein Repository pro Theme, Klon, Erweiterungsordner, version control, commit messages, one repository per theme, clone, extension folder
type: help
---
# Ein Theme unter Git stellen

Diese Seite ist für Designer, die ihr Theme noch nicht unter Versionsverwaltung gestellt haben oder es im Repository
einer Website führen. Die Themes, die 7x veröffentlicht, liegen jeweils in einem eigenen Repository, dessen Wurzel der
Erweiterungsordner ist.

## Warum ein Repository pro Theme

| Ein Repository pro Theme | Das Theme im Repository einer Website |
|---|---|
| Eigene Historie, Tags und Releases | Releases vermischt mit den Änderungen der Website |
| Von jeder Website mit Composer installierbar | Von Hand in andere Websites kopiert |
| Mitwirkende sehen nur das Theme | Mitwirkende sehen die Website des Kunden |
| Kann öffentlich sein, während die Website privat bleibt | Es zu veröffentlichen heißt, die Website zu veröffentlichen |

## Schritt für Schritt: das Repository beginnen

1. Gehen Sie in den Ordner des Themes: `cd extension/mytheme`.
2. Legen Sie das Repository mit `main` als Branch an: `git init -b main`.
3. Prüfen Sie, was committet wird: `git status --short`. Fügen Sie zuerst eine `.gitignore` hinzu
   ([Eine .gitignore für Themes](gitignore-fuer-themes.md)).
4. Committen Sie: `git add . && git commit -m "Added: The mytheme theme extension"`.
5. Legen Sie das entfernte Repository an und pushen Sie ([Das GitHub-Repository anlegen](../veroeffentlichung/das-github-repository-anlegen.md)).

## Innerhalb einer Installation daran arbeiten

- Bindet die Installation das Theme über Composer ein, lassen Sie Composer den Klon anlegen, damit seine Historie
  erhalten bleibt: `composer config --global 'preferred-install.yourvendor/*' source`, dann `composer require` (oder
  `composer reinstall`) für das Paket. Ein von Hand angelegter Klon anstelle eines Archivs, das Composer installiert hat,
  wird beim nächsten Update ersetzt. Siehe [Mit Git entwickeln](../../mit-git-entwickeln.md).
- Committen Sie in `extension/mytheme`, nicht im Repository der Installation.

## Commit-Nachrichten

Schreiben Sie, was sich für den Nutzer geändert hat und warum, mit einem dieser Präfixe, damit sich ein Changelog aus der
Historie zusammenstellen lässt:

| Präfix | Für |
|---|---|
| `Added:` | Ein neues Template, einen Ansichtsmodus, eine Einstellung, eine Übersetzung |
| `Updated:` | Geändertes Verhalten oder Aussehen |
| `Removed:` | Ein gelöschtes Template, eine gelöschte Einstellung oder Datei |
| `Renamed:` | Eine umbenannte Datei, Einstellung oder Klasse |
| `Fixed:` | Eine Fehlerkorrektur |

Beispiel: `Fixed: The map popup escapes the address and the title`.

## Verwandte Seiten

- [Vom Ergebnis des Assistenten zum ersten Commit](vom-assistenten-zum-ersten-commit.md)
- [Ein Team mit Git führen](../../fuehrung/ein-team-mit-git-fuehren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
