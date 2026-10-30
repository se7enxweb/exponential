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
7. Stellen Sie sicher, dass bei 7x ein unterzeichnetes Contributor Licensing Agreement vorliegt, im Wissen, dass es die
   Kontrolle über den Beitrag an 7x gibt (siehe unten)
   ([Übertragung des Urheberrechts (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla)). Hat das
   Partnerunternehmen die Rechte an der Arbeit seiner Entwickler, unterzeichnet das Unternehmen sie oder gibt seine
   Erlaubnis (CLA, Abschnitt 6); eine Unterschrift deckt alle künftigen Beiträge ab.

## Was danach geschieht

- **Übertragung des Urheberrechts (CLA).** Ein Beitrag zum Kernel, in welcher Form auch immer eingereicht (Pull
  Request, Patch, Issue oder E-Mail), wird erst gemergt, wenn seine unterzeichnete CLA eingegangen ist; 7x und die
  Exponential Foundation verlangen sie. **Mit Unterschrift und Einreichung überträgt der Partner das Urheberrecht an dem
  Beitrag an 7x, das damit sein Eigentümer wird, und gibt die Kontrolle darüber ab:** 7x darf ihn auf jede Weise
  verwenden, ändern, neu lizenzieren und verbreiten, auch unter anderen Lizenzen und in kommerziellen Produkten, ohne
  den Partner zu fragen oder zu bezahlen, der ihn nicht zurückziehen und keine Bedingungen daran knüpfen kann. Die
  eigenen individuellen Erweiterungen des Partners und die Sites seiner Kunden sind davon nicht betroffen.

- Jeder Pull Request wird übernommen. Solange er offen ist, fassen Sie ihn zusammen (Squash) und setzen ihn per Rebase
  auf `main` auf, wie es die Reviews verlangen (`git fetch origin`, `git rebase -i origin/main`,
  `git push --force-with-lease` nur auf den Branch Ihres Pull Requests); sobald er übernommen ist, wird die Historie von
  `main` nie umgeschrieben, und spätere Korrekturen kommen als neue Pull Requests (`doc/eng-us/bc/6.0/ci.md`).
- Eine Korrektur, die ein Verhalten ändert, das Ihr Kunde als Fehler sah, auf das sich andere Installationen aber
  verlassen (eine [negative Funktion](../projekt/unterstuetzung-erhalten.md#negative-funktionen)), braucht eine klare
  Begründung und kann mehrere Runden der Prüfung durchlaufen, bevor sie angenommen und übernommen wird. Den heutigen
  Standard zu behalten und eine Option hinzuzufügen, wird leichter angenommen.
- CI prüft Syntax, Manifest, Commit-Nachrichten, Programmierstil, PHPStan und die Tests auf den unterstützten
  PHP-Versionen (`doc/eng-us/bc/6.0/ci.md`).
- „Mit Ihrem Beitrag erklären Sie sich damit einverstanden, dass Ihre Beiträge unter der GNU General Public License,
  Version 2.0 oder später, lizenziert werden“ (CONTRIBUTING.md).

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
