---
title: Sicherheit nach der Einführung
audience: adopter, customer, admin, hosting
category: adoption
order: 63
summary: Was eine eingeführte Exponential-Website auf Dauer sicher hält - Aktualisierungen, Härtung, Konten und zweistufige Anmeldung, Sicherungen, der Audit-Trail und Logs, die vertrauliche Meldung von Schwachstellen und eine jährliche Sicherheitsüberprüfung.
keywords: Sicherheit, Härtung, Aktualisierungen, Konten, Zwei-Faktor, Sicherungen, Audit-Trail, Logs, Schwachstelle, security@se7enx.com, Überprüfung, security, hardening, updates, accounts, two-factor, backups, audit trail, logs, vulnerability, review
type: help
---
# Sicherheit nach der Einführung

## Inhalt

1. [Die Grundlagen](#die-grundlagen)
2. [Belege, wenn etwas geschieht](#belege-wenn-etwas-geschieht)
3. [Eine Schwachstelle melden](#eine-schwachstelle-melden)
4. [Eine jährliche Überprüfung](#eine-jährliche-überprüfung)

## Die Grundlagen

| Praxis | Seite |
|---|---|
| Aktualisierungen einspielen, Sicherheitsversionen zuerst | [Auf dem Laufenden bleiben](auf-dem-laufenden-bleiben.md) |
| Die Installation härten | [Sicherheitshärtung](../../sicherheitshaertung.md), [Kapitel 13 des Installationsbuchs](../../../installation/13-sicherheitshaertung.md) |
| Wenige Administratoren, starke Passwörter, zweistufige Anmeldung | [Administratorkonten und Zugriff](../../kunden/gesund-halten/administratorkonten-und-zugriff.md) |
| Sicherungen, die sich wiederherstellen lassen | [Sicherungen](../../kunden/gesund-halten/backups.md), [Test der Wiederherstellung](../../kunden/gesund-halten/wiederherstellungstest.md) |
| Eine Content Security Policy | [Content Security Policy](../../../funktionen/6.0/content-security-policy.md) |

Eine kurze Liste für Betreiber: [Sicherheitscheckliste für Betreiber](../../kunden/gesund-halten/sicherheits-checkliste-fuer-betreiber.md).

## Belege, wenn etwas geschieht

Exponential führt einen manipulationssicheren Audit-Trail und Logs, mit Ansichten in der Administration
([Audit-Trail](../../../funktionen/6.0/pruefprotokoll.md), [Log-Ansicht](../../../funktionen/6.0/log-betrachter.md),
[Logs für Betreiber](../../kunden/gesund-halten/logs-fuer-betreiber.md)). Machen Sie während eines Vorfalls Notizen
([Vorlage für Notizen zu Vorfällen](../../kunden/gesund-halten/vorlage-vorfallnotizen.md)).

## Eine Schwachstelle melden

Wenn Sie eine Schwachstelle in Exponential selbst finden, melden Sie sie vertraulich an security@se7enx.com, nicht an
einem öffentlichen Ort. "You can expect a first answer within one business day", eine erste Antwort kommt also innerhalb
eines Werktags (`SECURITY.md`; [Wie Sicherheitsmeldungen bearbeitet werden](../../gemeinschaft/so-funktioniert-es/wie-sicherheitsmeldungen-behandelt-werden.md)).

## Eine jährliche Überprüfung

```text
[ ] Alle Software in unterstützten Versionen
[ ] Administratorkonten überprüft; Ausgeschiedene entfernt
[ ] Wiederherstellungstest in diesem Jahr durchgeführt
[ ] Härtung mit dem aktuellen Installationsbuch abgeglichen
[ ] Notizen zu Vorfällen durchgesehen; Erkenntnisse umgesetzt
```

## Verwandte Seiten

- [Nach der Einführung](README.md)
- [Sicherheitsgrundlagen (Kunden)](../../kunden/gesund-halten/sicherheitsgrundlagen.md)
- [Sicherheitsgrundlagen für Unternehmen](../../unternehmen/sicherheit/sicherheitsgrundlagen-fuer-unternehmen.md)
- [Vertrauliche Sicherheitsmeldungen](../../kunden/gesund-halten/vertrauliche-sicherheitsmeldungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
