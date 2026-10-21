---
title: Code zurückgeben
audience: partner
category: partnership
order: 630
summary: Wie Entwickler bei Partnern Korrekturen und Funktionen zum Kernel von Exponential beitragen - warum es sich lohnt, der Ablauf vom Fork bis zum übernommenen Pull Request, Regeln für Commit-Nachrichten, das Datei-Manifest und CI, die Merge-Richtlinie und die Abstimmung von Beiträgen mit Kunden.
keywords: beitragen, Pull Request, Fork, Branch, Commit-Nachricht, CI, Manifest, filelist.md5, GPL, Upstream, Kernel, Merge-Richtlinie, contribute, pull request, fork, branch, commit message, manifest, upstream, kernel, merge policy
type: help
---
# Code zurückgeben

Diese Seite ist für Entwickler in Partnerunternehmen. Durch Beiträge machen Partner aus privaten Korrekturen eine
gemeinsame Pflege: Sobald Ihre Korrektur im Kernel ist, enthält jedes Release sie, und Sie pflegen sie nicht mehr allein.

## Warum es sich lohnt

- **Weniger Pflege.** Ein privater Patch muss bei jedem Upgrade erneut angewendet werden; eine übernommene Korrektur nie wieder.
- **Punkte und Ansehen.** Codebeiträge zählen („zu Open-Source-Erweiterungen beitragen“, „Codebeiträge auf
  exponential.earth“ werden erfasst, <https://exponential.earth/partner/requirements>).
- **Einfluss.** Code ist der deutlichste Funktionswunsch ([An der Roadmap mitwirken](an-der-roadmap-mitwirken.md)).

## Der Ablauf

Aus CONTRIBUTING.md und [Mit Git entwickeln](../mit-git-entwickeln.md):

1. Forken Sie <https://github.com/se7enxweb/exponential> und klonen Sie Ihren Fork.
2. Legen Sie einen Branch für eine Änderung an: `git checkout -b feature/your-feature`.
3. Nehmen Sie die Änderung vor. Folgen Sie den PSR-Standards und dem Programmierstil; Templates der Administration
   folgen den Gestaltungsregeln (keine `<style>`-Blöcke oder `style=""`-Attribute; gemeinsame Komponenten und Tokens).
4. Committen Sie mit einer Betreffzeile, die damit beginnt, was die Änderung tut: `Added: `, `Updated: `, `Fixed: `,
   `Removed: ` oder `Renamed: `.
5. Halten Sie das Datei-Manifest aktuell: Jede hinzugefügte, geänderte oder entfernte Datei aktualisiert ihre Zeile in
   `share/filelist.md5` (`make manifest-fix`); `make hooks` installiert den Pre-Commit-Hook, der es prüft.
6. Stellen Sie sicher, dass die Tests bestehen, aktualisieren Sie die Dokumentation, pushen Sie in Ihren Fork und
   eröffnen Sie einen Pull Request.

## Was danach geschieht

- „Jeder Pull Request wird übernommen, und seine Commits bleiben so erhalten, wie der Mitwirkende sie geschrieben hat,
  und werden nie umgeschrieben“ (`doc/eng-us/bc/6.0/ci.md`). Was korrigiert werden muss, wird in Commits darüber korrigiert.
- CI prüft Syntax, Manifest, Commit-Nachrichten, Programmierstil, PHPStan und die Tests auf den unterstützten
  PHP-Versionen (`doc/eng-us/bc/6.0/ci.md`).
- „Mit Ihrem Beitrag erklären Sie sich damit einverstanden, dass Ihre Beiträge unter der GNU General Public License,
  Version 2.0 oder später, lizenziert werden“ (CONTRIBUTING.md).
- Ändert Ihre Korrektur ein Verhalten, das beabsichtigt oder Auslegungssache ist (eine negative Funktion), betrifft
  sie jede Site, die sich darauf verlässt, auch die anderer Partner. Rechnen Sie dann mit einer Bitte um eine klare
  Begründung und mehreren Runden der Prüfung, und bieten Sie das neue Verhalten lieber als Option an als als neuen
  Standard ([Negative Funktionen](../projekt/unterstuetzung-erhalten.md#negative-funktionen)).

## Mit Kunden abstimmen

Bezahlte Kundenarbeit gehört zum Vertrag. Fügen Sie eine Klausel hinzu:

```text
Generic improvements to Exponential and its public extensions made during this project (bug fixes and
general features that contain no customer data or business rules) may be contributed to the public
projects under their open source licenses. Customer content, data, designs and confidential logic are
never published.
```

## Auch kleine Beiträge zählen

- Ein fehlschlagender Test, der einen Fehler zeigt.
- Eine Korrektur der Dokumentation: Jede Hilfeseite hat „Auf GitHub bearbeiten“.
- Eine Korrektur einer Übersetzung ([Dokumentation und Übersetzungen beitragen](dokumentation-und-uebersetzungen-beitragen.md)).

## Verwandte Seiten

- [Erweiterungen an die Upstream-Projekte zurückgeben](erweiterungen-zurueckgeben.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Fehler gut melden](fehler-gut-melden.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
