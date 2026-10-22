---
title: Beiträge an das Projekt
audience: software-engineer, developer
category: software-engineering
order: 910
summary: Wie ein Ingenieur eine Änderung an den Exponential-Kernel oder eine seiner Erweiterungen schickt - Forken und Branchen, die vorher auszuführenden Prüfungen, die für Kernel-Beiträge verlangte Übertragung des Urheberrechts (CLA) und was sie bedeutet, Erwartungen an Pull-Requests und viele Review-Runden, Squash und Rebase, Lizenzierung unter der GPL, Issues und vertrauliche Sicherheitsmeldungen.
keywords: Beitragen, Upstream, Pull-Request, Fork, CLA, Übertragung des Urheberrechts, Code-Review, Review-Runden, Squash, Rebase, GPL, Lizenz, Issues, CONTRIBUTING.md, Verhaltenskodex, Softwareingenieur, contributing, upstream, copyright assignment, code review, code of conduct
type: help
---
# Beiträge an das Projekt

Diese Seite richtet sich an Ingenieure, die eine Korrektur oder eine Funktion in Exponential selbst haben möchten, statt
sie im eigenen Code mitzuführen. Maßgeblich ist der Text `CONTRIBUTING.md` im Hauptverzeichnis des Repositorys; diese
Seite ordnet ihn und ergänzt die technischen Prüfungen.

## Inhalt

1. [Kernel oder Erweiterung](#kernel-oder-erweiterung)
2. [Die Übertragung des Urheberrechts (CLA)](#die-übertragung-des-urheberrechts-cla)
3. [Die Änderung vorbereiten](#die-änderung-vorbereiten)
4. [Der Pull-Request](#der-pull-request)
5. [Review](#review)
6. [Lizenzierung](#lizenzierung)
7. [Issues und Sicherheitsmeldungen](#issues-und-sicherheitsmeldungen)

## Kernel oder Erweiterung

| Ziel | Repository | Hinweise |
|---|---|---|
| Der Kernel | `se7enxweb/exponential`: `kernel/`, `lib/`, `bin/`, `design/`, `settings/`, `doc/`, die darin mitgelieferten Erweiterungen | die CLA ist vor einem Merge erforderlich |
| Eine Erweiterung | ihr eigenes Repository `se7enxweb/<name>` | siehe [Mit Pull-Requests zurückgeben](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/mit-pull-requests-zurueckgeben.md) |
| Dokumentation | die Dateien in `doc/` des Kernels | derselbe Ablauf ([Dokumentation als Code](dokumentation-als-code.md)) |

## Die Übertragung des Urheberrechts (CLA)

7x und die Exponential Foundation verlangen für jeden Code-Beitrag zum Kern-Kernel ein unterschriebenes Contributor
Licensing Agreement, bevor er zusammengeführt werden kann, gleich in welcher Form er ankommt (Pull-Request, Patch, Code in
einem Issue, E-Mail). `CONTRIBUTING.md` sagt es deutlich: **Indem Sie die CLA unterschreiben und einen Beitrag zum
Kern-Kernel einreichen, übertragen Sie das Urheberrecht an diesem Beitrag an 7x**, das ihn ohne Rückfrage oder Bezahlung
verwenden, ändern, neu lizenzieren und verbreiten darf; Sie können ihn nicht zurückziehen oder an Bedingungen knüpfen. Sie
wird einmal für alle künftigen Beiträge unterschrieben. Ihre eigenen Erweiterungen sind nicht betroffen. Lesen Sie die
Vereinbarung selbst, bevor Sie unterschreiben; die Zusammenfassung ist keine Rechtsberatung. Hat Ihr Arbeitgeber Rechte an
dem, was Sie schreiben, brauchen Sie seine Erlaubnis.

## Die Änderung vorbereiten

```bash
composer install
make hooks
make devtools
```

Dann, auf einem Branch:

- folgen Sie den [Programmierstandards](../../standards/programmierstandards.md) und dem Stil der Dateien rund um Ihre Änderung;
- benennen Sie neuen Code mit dem Präfix `exp` ([Die Präfixregel](../../standards/namenspraefixe-sevenx-und-exp.md));
- fügen Sie einen Test hinzu, der ohne Ihre Änderung scheitert und mit ihr besteht;
- aktualisieren Sie, wo nötig, die Dokumentation und das Changelog;
- erneuern Sie das Dateimanifest: `make manifest-fix`, und committen Sie `share/filelist.md5` mit der Änderung;
- führen Sie `make check` und die Tests Ihrer Änderung aus ([CI-Gates](ci-gates.md)).

Wenden Sie die [Checkliste für Code-Reviews](checkliste-fuer-code-reviews.md) auf Ihre eigene Änderung an, bevor es jemand anderes tut.

## Der Pull-Request

- Eine Änderung je Pull-Request; eine Korrektur und ein Refactoring sind zwei.
- Beschreiben Sie, was sich geändert hat, warum und wie Sie es getestet haben.
- Kreuzen Sie das CLA-Kästchen der Pull-Request-Vorlage an, sobald Ihre CLA vorliegt.
- Halten Sie den Branch mit `main` aktuell; ein Pull-Request, der mit `main` in Konflikt steht, erhält überhaupt keine
  CI-Prüfungen.

## Review

`CONTRIBUTING.md` warnt: 7x führt eine weit überdurchschnittliche Zahl von Review-Runden durch, manchmal Hunderte von
Überarbeitungen, bevor eine Änderung angenommen wird. So bleibt der Kernel wartbar; es ist kein Urteil über den Autor.
Schneller geht es so: Halten Sie die Änderung klein, beantworten Sie jeden Review-Kommentar im Pull-Request, setzen Sie neu
auf, wenn sich `main` bewegt, und fassen Sie die Korrekturen jeder Runde zu logischen Commits zusammen.

## Lizenzierung

Exponential steht unter der GNU GPL, Version 2 oder später; Beiträge werden genauso lizenziert. Die Klausel "oder später"
erlaubt dem Projekt, zu späteren GPL-Versionen zu wechseln. Prüfen Sie, ob Code, den Sie einbringen, damit vereinbar ist
([Lizenzen und Vereinbarkeit](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/lizenzen-und-kompatibilitaet.md)).
Der Verhaltenskodex des Projekts ist der Contributor Covenant 2.1 mit Ergänzungen für Exponential (`CODE_OF_CONDUCT.md`).

## Issues und Sicherheitsmeldungen

- Fehler, Funktionswünsche und Diskussionen gehören in den Issue-Tracker, mit seinen Vorlagen; die Seite **Fehler melden**
  der Hilfe bereitet eine sichere Zusammenfassung der Installation vor ([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)).
- Manche gemeldeten Fehler sind [negative Funktionen](../projekt/unterstuetzung-erhalten.md#negative-funktionen): bewusstes oder
  auslegungsfähiges Verhalten, auf das andere Websites angewiesen sind. Eines davon zu ändern braucht eine klare
  Begründung, wird am besten als Option statt als neuer Standard vorgeschlagen und kann mehrere Review-Runden dauern,
  bevor es zusammengeführt wird.
- Ein Sicherheitsproblem geht vertraulich an die Adresse in `SECURITY.md`, nie in ein öffentliches Issue oder einen
  Pull-Request.

## Verwandte Seiten

- [Git-Arbeitsablauf](git-arbeitsablauf.md)
- [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md)
- [Der Weg zum Maintainer](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/der-weg-zum-maintainer.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
