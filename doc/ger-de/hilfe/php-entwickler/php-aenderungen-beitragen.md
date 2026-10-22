---
title: PHP-Änderungen beitragen
audience: php-developer, developer
category: php-development
order: 470
summary: Wie eine PHP-Änderung in den Exponential-Kernel gelangt - die Contributor License Agreement, Fork und Branch, die Prüfungen vor einem Pull Request, Commit-Nachrichten, Tests, das Datei-Manifest, Review-Runden, Squashen und Rebasen und was Sie stattdessen als Erweiterung beitragen.
keywords: Mitwirken, Beitragen, Pull Request, Übertragung des Urheberrechts, Commit-Nachricht, Datei-Manifest, Code-Review, contributing, pull request, CLA, contributor license agreement, copyright assignment, fork, branch, commit message, Added, Updated, Fixed, Removed, Renamed, make check, file manifest, code review, squash, rebase, CONTRIBUTING.md
type: help
---
# PHP-Änderungen beitragen

Diese Seite richtet sich an PHP-Entwickler, die eine Korrektur oder eine Funktion im Exponential-Kernel selbst möchten,
dem Repository `se7enxweb/exponential`. Der verbindliche Text ist `CONTRIBUTING.md` im Wurzelverzeichnis des
Repositorys; diese Seite bringt seine Schritte in die Reihenfolge, in der ein PHP-Entwickler ihnen begegnet, und
verweist auf die Prüfungen.

## Inhalt

1. [Kernel oder Erweiterung?](#kernel-oder-erweiterung)
2. [Vor Ihrem ersten Pull Request: die CLA](#vor-ihrem-ersten-pull-request-die-cla)
3. [Forken, klonen, Branch anlegen](#forken-klonen-branch-anlegen)
4. [Die Änderung schreiben](#die-änderung-schreiben)
5. [Prüfen](#prüfen)
6. [Committen](#committen)
7. [Der Pull Request und das Review](#der-pull-request-und-das-review)
8. [Nach dem Merge](#nach-dem-merge)

## Kernel oder Erweiterung?

| Zum Kernel beitragen | In einer Erweiterung behalten |
|---|---|
| eine Fehlerkorrektur in Kernel- oder Bibliothekscode | eine Funktion, die nur Ihre Sites brauchen |
| eine Korrektur der PHP-Kompatibilität | ein eigenes Modul, einen eigenen Datentyp oder Operator |
| ein neuer Erweiterungspunkt, der dem Kernel fehlt | Code, der vorhandene Erweiterungspunkte nutzt |
| Dokumentation vorhandenen Verhaltens | |

Ihre eigenen Erweiterungen sind von den Beitragsbedingungen des Kernels nicht betroffen. Wie Sie eine eigene
Erweiterung veröffentlichen, beschreiben [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md) und das
[Buch für Erweiterungsentwickler, Teil 5](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/README.md).

## Vor Ihrem ersten Pull Request: die CLA

7x und die Exponential Foundation verlangen für jeden Code-Beitrag zum Kern-Kernel ein unterzeichnetes Contributor
Licensing Agreement, bevor er gemergt werden kann. Lesen Sie die Vereinbarung und die Zusammenfassung in
`CONTRIBUTING.md` („Copyright assignment (CLA)“), bevor Sie unterzeichnen: Indem Sie unterzeichnen und einen Beitrag zum
Kern-Kernel einreichen, übertragen Sie das Urheberrecht an diesem Beitrag an 7x. Sie unterzeichnen einmal für alle Ihre
künftigen Beiträge; ein Pull Request ohne hinterlegte unterzeichnete CLA wird geprüft, aber erst gemergt, wenn sie
eingegangen ist.

## Forken, klonen, Branch anlegen

```bash
git clone https://github.com/<your-account>/exponential.git
cd exponential
composer install
make devtools && make hooks
git checkout -b fix/acme-short-description
```

Richten Sie eine Installation zum Testen ein ([Einen PHP-Entwicklungs-Checkout einrichten](ein-php-entwicklungs-checkout.md)).

## Die Änderung schreiben

- Folgen Sie dem Code-Stil von `phpcs.xml.dist` und den Dateien rund um Ihre Änderung ([Der Coding-Standard](der-coding-standard.md)).
- Halten Sie die Kompatibilität mit PHP 8.0 ([Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md)).
- Benennen Sie neuen Code mit dem Präfix `exp`; benennen Sie vorhandene Bezeichner `eZ*` niemals um
  ([Klassenbenennung und das Präfix exp](klassenbenennung-und-das-praefix-exp.md)).
- Templates und Stylesheets der Administration folgen [Die Administration gestalten](../../bc/6.0/admin4-gestaltung.md):
  keine `<style>`-Blöcke oder `style=""`-Attribute, gemeinsame Komponenten und Tokens.
- Fügen Sie einen Test hinzu, der ohne Ihre Änderung fehlschlägt und mit ihr besteht ([PHPUnit ohne Datenbank](phpunit-ohne-datenbank.md)).
- Aktualisieren Sie die Dokumentation, die die Änderung betrifft.

## Prüfen

```bash
make check                                   # syntax, manifest, code style, PHPStan
make lint80                                  # PHP 8.0 syntax of changed files (needs a PHP 8.0 binary)
php vendor/bin/phpunit <your test files>
make manifest-fix                            # refresh share/filelist.md5 for added and changed files
```

[CI und das Nachstellen einer roten Prüfung](ci-und-eine-rote-pruefung-nachstellen.md) listet auf, was die CI ausführt und wie
Sie jeden Schritt nachstellen.

## Committen

Der Betreff beginnt mit dem, was die Änderung tut, und die Nachricht sagt, warum:

```text
Fixed: The redirect keeps the query string on the own host

The query string was dropped when the target host was the current one, so
paged lists lost their page after a save.
```

Präfixe: `Added: `, `Updated: `, `Fixed: `, `Removed: `, `Renamed: `. Der commit-msg-Hook prüft sie
([Commit-Nachrichten](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/commit-nachrichten.md)). Committen Sie
`share/filelist.md5` mit der Änderung.

## Der Pull Request und das Review

1. Pushen Sie Ihren Branch in Ihren Fork und öffnen Sie einen Pull Request auf `main`; kreuzen Sie das CLA-Kästchen der
   Vorlage an.
2. **Rechnen Sie mit vielen Review-Runden.** Das Projekt prüft mehr als üblich, bevor es eine Änderung annimmt; jede
   Runde macht die Änderung leichter annehmbar. Beantworten Sie jeden Kommentar im Pull Request, mit einer Änderung
   oder einer Begründung. Eine Änderung an einer [negativen Funktion](../projekt/unterstuetzung-erhalten.md#negative-funktionen),
   also an Verhalten, das bewusst so ist oder Auslegungssache ist und von dem andere Sites abhängen, braucht eine klare
   Begründung und wird als Option leichter angenommen denn als neuer Standard.
3. **Halten Sie ihn klein und fokussiert**: Eine Korrektur und ein Refactoring sind zwei Pull Requests.
4. **Squashen und rebasen Sie, solange er offen ist.** Fassen Sie die Fixups jeder Runde zu logischen Commits zusammen
   und rebasen Sie auf das aktuelle `main`; force-pushen Sie nur Ihren Pull-Request-Branch (`git push --force-with-lease`).
5. Ein Pull Request wird als in `main` gemergt getestet; wenn sich `main` bewegt, mergen oder rebasen Sie und pushen Sie
   erneut.

## Nach dem Merge

Die Historie von `main` wird nach einem Merge nie umgeschrieben: Eine spätere Korrektur kommt als neuer Pull Request.
Releases und Tags erstellt das Projekt; eine veröffentlichte Version ändert sich nie.

## Verwandte Seiten

- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Mit Pull Requests zurückgeben](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/mit-pull-requests-zurueckgeben.md)
- [Standard für Versionskontrolle und Releases](../../standards/versionsverwaltung-und-releases.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
