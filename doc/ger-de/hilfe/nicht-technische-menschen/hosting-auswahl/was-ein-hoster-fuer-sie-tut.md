---
title: Was ein Hoster für Sie tut
audience: non-technical, customer
category: plain-language
order: 42
summary: Die Aufgaben, die ein Hosting-Unternehmen einem Menschen ohne technische Vorkenntnisse bei einer Exponential-Website abnehmen kann - Server, HTTPS, Aktualisierungen, Sicherungen, Überwachung, E-Mail -, welche davon oft nicht enthalten sind und wie Sie herausfinden, was Ihr Hoster wirklich tut.
keywords: Hoster, Hosting-Unternehmen, was enthalten ist, Server, HTTPS, Aktualisierungen, Sicherungen, Überwachung, E-Mail, Cron, Support, nicht enthalten, nicht technisch, host, hosting company, what is included, server, updates, backups, monitoring, mail, support, not included, non-technical
type: help
---
# Was ein Hoster für Sie tut

"Hosting" kann sehr Verschiedenes bedeuten. Ein Hoster vermietet Ihnen nur Platz auf einem Server; ein anderer betreibt
alles für Sie. Diese Seite listet die Aufgaben auf, damit Sie abhaken können, was Ihr Hoster tut, und die Lücken sehen.

## Inhalt

1. [Die Aufgaben](#die-aufgaben)
2. [Oft nicht enthalten](#oft-nicht-enthalten)
3. [Herausfinden, was Ihr Hoster tut](#herausfinden-was-ihr-hoster-tut)
4. [Die Lücken füllen](#die-lücken-füllen)

## Die Aufgaben

Eine Exponential-Website braucht diese Dinge um sich herum
([Einen Hoster wählen](../../kunden/partner-und-hoster/einen-hoster-auswaehlen.md#was-die-website-braucht)):

| Aufgabe | In einfachen Worten | Macht Ihr Hoster das? |
|---|---|---|
| Der Server | Ein Computer, der immer eingeschaltet ist, mit PHP und einer Datenbank | |
| HTTPS | Das Schloss: ein Zertifikat, rechtzeitig erneuert | |
| Cron | Eine kleine Aufgabe, die alle paar Minuten läuft, etwa um die Suche zu aktualisieren und Benachrichtigungen zu senden | |
| Ausgehende E-Mail | Damit Mails zum Zurücksetzen des Passworts und Formularbenachrichtigungen ankommen | |
| Aktualisierungen von Exponential | Neue Versionen und Sicherheitskorrekturen einspielen | |
| Aktualisierungen von PHP und System | Die eigene Software des Servers aktuell halten | |
| Sicherungen | Datenbank, hochgeladene Dateien, Einstellungen, an einem anderen Ort gespeichert | |
| Tests der Wiederherstellung | Beweisen, dass die Sicherungen funktionieren | |
| Überwachung | Bemerken, wenn die Website nicht erreichbar ist, auch nachts | |
| Support | Jemand, den Sie anrufen können, mit bekannten Zeiten und Antwortfristen | |

Drucken Sie diese Tabelle aus oder kopieren Sie sie, und füllen Sie die letzte Spalte mit Ihrem Hoster aus.

## Oft nicht enthalten

Auch gute Hosting-Angebote lassen diese Punkte oft weg oder berechnen sie extra:

- **Aktualisierungen von Exponential.** Ein allgemeiner Hoster aktualisiert den Server, aber nicht die Anwendung darauf.
  Das ist dann die Aufgabe Ihres Entwicklers ([Wie Updates funktionieren](../../kunden/gesund-halten/wie-updates-funktionieren.md)).
- **Tests der Wiederherstellung.** Sicherungen werden gemacht, aber niemand prüft, ob sie sich wiederherstellen lassen
  ([Test der Wiederherstellung](../../kunden/gesund-halten/wiederherstellungstest.md)).
- **Änderungen an der Website**: neue Layouts, Designänderungen, Erweiterungen.
- **Hilfe für Redakteure.** "Wie füge ich ein Bild hinzu?" ist keine Hosting-Frage; diese Hilfe oder Ihr Entwickler
  beantwortet sie.
- **Der Domainname und die E-Mail-Postfächer** sind vielleicht eigene Produkte ([Domainname und E-Mail](domainname-und-e-mail.md)).

## Herausfinden, was Ihr Hoster tut

Fragen Sie schriftlich und bewahren Sie die Antwort auf:

1. "Wer spielt Versionen und Sicherheitskorrekturen von Exponential ein, und wie bald nach ihrer Veröffentlichung?"
2. "Was wird gesichert, wie oft, wohin, und wann wurde zuletzt eine Sicherung zum Test wiederhergestellt?"
3. "Wie bemerken Sie, dass unsere Website nicht erreichbar ist, und was geschieht dann, auch am Wochenende?"
4. "Was sind Ihre Supportzeiten, und wie schnell antworten Sie?"

[Was Sie vor der Unterschrift fragen sollten](was-sie-vor-der-unterschrift-fragen-sollten.md) hat die vollständige Liste und
[Was Sie Ihren Hoster fragen](../../kunden/gesund-halten/was-sie-ihren-hoster-fragen-sollten.md) die Fassung für bestehende Websites.

## Die Lücken füllen

Entscheiden Sie für jede Aufgabe, die noch niemand übernimmt, wer es tun wird: der Hoster gegen eine zusätzliche
Gebühr, ein Entwickler, ein Partner oder, bei Aufgaben rund um die Inhalte, Sie. Tragen Sie die Antwort in
[Wen Sie anrufen](../wenn-es-hakt/wen-sie-anrufen.md) ein. Eine Lücke, von der Sie wissen, ist ein Plan; eine Lücke, von der
Sie nichts wissen, ist ein Ausfall, der nur darauf wartet, zu geschehen.

## Verwandte Seiten

- [Hosting-Auswahl](README.md)
- [Drei Wege zu einer Website](drei-wege-zu-einer-website.md)
- [Wer macht was](../../kunden/partner-und-hoster/wer-was-macht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
