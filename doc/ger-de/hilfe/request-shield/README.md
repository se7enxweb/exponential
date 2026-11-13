---
title: "Request Shield: hier beginnen"
audience: admin, hosting, developer
category: administration
order: 1
summary: Was der Request Shield (exp_request_shield) ist - der Filter vor der Website, der jede Anfrage vor dem Kernel entscheidet -, wer sich um ihn kümmert, was Setup > Request Shield zeigt und wo Sie weiterlesen.
keywords: Request Shield, request-shield, exp_request_shield, Web Application Firewall, WAF, Firewall, Filter, Scanner, Bots, Anfragefluten, Browserprüfung, Pass-Cookie, Regeldatei, Modus monitor, Modus enforce, Pausieren, Setup, requestshield/dashboard, requestshield/read, requestshield/manage, Velocity, PHP-FPM, browser check, rule file, monitor mode, enforce mode, pause
type: help
---
# Request Shield: hier beginnen

Der **Request Shield** (die Extension `exp_request_shield`) ist ein Filter vor der Website. Er sieht sich jede
Anfrage an, **bevor Exponential startet**, und entscheidet: durchlassen, abweisen oder den Browser bitten, sich als
Browser auszuweisen (die **Browserprüfung**). Er weist Scanner ab, die nach Schwachstellen suchen, Anfragefluten aus
einem einzigen Netz, Formularsendungen, die nicht von den Seiten der Website kommen, und Anfragen nach internen
Dateien, für wenige Mikrosekunden pro Anfrage.

Sie betreuen ihn unter **Setup > Request Shield**, im Setup-Menü direkt nach **Logdateien**.

## Inhalt

1. [Für wen er gedacht ist](#für-wen-er-gedacht-ist)
2. [Was Sie in der Administration sehen](#was-sie-in-der-administration-sehen)
3. [Was Besucher und Redaktion bemerken](#was-besucher-und-redaktion-bemerken)
4. [Die Seiten dieses Abschnitts](#die-seiten-dieses-abschnitts)
5. [Verwandte Seiten](#verwandte-seiten)

## Für wen er gedacht ist

- **Administratoren** lesen die Übersicht und das Protokoll, pausieren den Shield oder wechseln zwischen monitor und
  enforce und antworten, wenn jemand sagt, die Website habe ihn abgewiesen.
- **Hosting-Personal und Entwickler** installieren ihn, geben der Website eine eigene Regeldatei und fügen auf dem
  Server Ausnahmen hinzu. Ihr Leitfaden ist [Den Request Shield einrichten](../../anleitungen/request-shield.md).

Um die Seiten der Administration zu nutzen, müssen Sie nichts bearbeiten: Sie zeigen, was der Shield tut, und die
Schalter ändern genau die Dateien, die diese Seiten beschreiben.

## Was Sie in der Administration sehen

| Seite | Wofür sie da ist |
|---|---|
| **Übersicht** | Läuft der Shield, in welchem Modus, was hat er in den letzten 24 Stunden entschieden, welche Regeln haben am häufigsten entschieden. Die Schalter stehen oben. [Die Übersicht und ihr Diagramm](die-uebersicht-und-ihr-diagramm.md) |
| **Protokoll** | Jede Zeile, die der Shield geschrieben hat, nach Zeitraum und Entscheidung. [Das Protokoll](das-log.md) |
| **Regeln** | Ob die Regeldateien in Ordnung sind, und ihr Text. [Die Regeln](die-regeln.md) |

Zwei Richtlinien entscheiden, wer was sieht: `requestshield/read` für die Seiten, `requestshield/manage` für die
Schalter. In einer neuen Installation hat nur die Rolle Administrator sie.

## Was Besucher und Redaktion bemerken

Meist nichts. Die Anfragen echter Besucher gehen durch. Bei der Anmeldung und in der Administration einmal pro Pass
löst der Browser die Browserprüfung von selbst in einem Bruchteil einer Sekunde; es gibt kein Rätsel zu lösen. Ein
Browser ohne JavaScript oder Cookies erfährt, was fehlt.

Ein Scanner, eine Anfrageflut oder eine Anfrage nach einer internen Datei erhält eine kurze Abweisung (meist 404 oder
403, 429 bei zu vielen Anfragen) und erreicht die Website nie.

## Die Seiten dieses Abschnitts

| Seite | Was sie beantwortet |
|---|---|
| [Die Übersicht und ihr Diagramm](die-uebersicht-und-ihr-diagramm.md) | Die Hinweise, die Kennzahlen, das Diagramm der Entscheidungen pro Stunde, der Kasten Einrichtung, die Regeln, die am häufigsten entschieden haben |
| [Die Schalter](die-schalter.md) | Pausieren und Einschalten, Durchsetzen, Beobachten, Aus: was jeder ändert, wann er wirkt, wer ihn benutzen darf |
| [Das Protokoll](das-log.md) | Das Protokoll nach Zeitraum und Entscheidung lesen, die Spalten, das Netz des Clients, ein Link auf eine Ansicht |
| [Die Regeln](die-regeln.md) | Welche Regeldatei verwendet wird, ihre Einbindungen, ob sie sich übersetzen lässt, die Befehle, die sie prüfen |
| [Beobachten und durchsetzen](beobachten-und-durchsetzen.md) | Die Modi, wann Sie zuerst beobachten, die Rückkehr zu enforce |
| [Unter Velocity](unter-velocity.md) | Was anders ist, wenn Exponential Velocity die Website ausliefert |
| [Fehlersuche](fehlersuche.md) | Symptome, Ursachen und Abhilfe |
| [FAQ](faq.md) | Kurze Antworten auf die üblichen Fragen |

## Verwandte Seiten

- [Der Request Shield](../../funktionen/6.0/request-shield.md) (Referenz der Funktion: wie er arbeitet, Dateien,
  Lizenzen, wer ihn wann bekommt)
- [Den Request Shield einrichten](../../anleitungen/request-shield.md) (Installation, eigene Regeln, prüfen und
  testen, aktualisieren, deinstallieren)
- [Aktualisierung auf 6.0.15: der Request Shield](../../bc/6.0/request-shield.md)
- [Sicherheitshärtung](../sicherheitshaertung.md)
- Die Dokumentation der Bibliothek: [request-shield](https://github.com/cjw-network/request-shield/tree/main/docs)
  (auf Englisch)
