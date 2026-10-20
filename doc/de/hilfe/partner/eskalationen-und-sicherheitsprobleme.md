---
title: Eskalationen und Sicherheitsprobleme
audience: partner
category: partnership
order: 340
summary: Was Partner tun, wenn ein Ticket nicht vorankommt oder ein Sicherheitsproblem gefunden wird - eine Eskalationsleiter, vertrauliche Meldung an security@se7enx.com, was Sie Kunden sagen und wie Sicherheitskorrekturen Sie erreichen.
keywords: Eskalation, Sicherheit, Sicherheitslücke, Offenlegung, vertraulich, security@se7enx.com, Vorfall, Kunden, Korrekturen, SECURITY.md, escalation, security, vulnerability, disclosure, private, incident, customers, fixes
type: help
---
# Eskalationen und Sicherheitsprobleme

Diese Seite ist für Verantwortliche der Partnerschaft und technische Leitungen. Sie behandelt zwei Situationen: ein
Support-Anliegen, das mehr Aufmerksamkeit braucht, und eine Sicherheitslücke.

## Ein Support-Anliegen eskalieren

Das Programm veröffentlicht kein Eskalationsverfahren. Eine sinnvolle Leiter, aufgebaut aus den veröffentlichten Kanälen:

| Schritt | Wann | Wie |
|---|---|---|
| 1 | Die Reaktionszeit Ihrer Stufe ist überschritten | Kommentieren Sie das Ticket mit dem Zeitpunkt der Eröffnung und Ihrer Stufe |
| 2 | Die Auswirkung auf den Produktivbetrieb hält an | Schreiben Sie dem Partnerteam (<https://exponential.earth/partner/contact-us>) mit dem Link zum Ticket |
| 3 | Bringen Sie es ins nächste Beratungstreffen ein oder bitten Sie um ein früheres | [Partnertreffen](partnertreffen-und-briefings.md) |
| 4 | Sie brauchen jetzt garantierte Hilfe | Kaufen Sie bezahlten Support bei 7x (<https://exponential.earth/support>) |

Bleiben Sie bei Eskalationen sachlich und höflich: was kaputt ist, seit wann, was es kostet, was Sie brauchen.

## Sicherheitslücken

Die Regeln stehen in [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md):

- **Melden Sie vertraulich** an **security@se7enx.com**. Nie in einem öffentlichen Issue, Forum oder Chat und nie in
  einem Partner-Ticket in einem Repository, das andere lesen können.
- „Sie können eine erste Antwort innerhalb eines Werktags erwarten“ (SECURITY.md).
- „Exponential 6.0 ist die gepflegte Linie. Sicherheitskorrekturen werden auf dem Branch `main` gemacht ... und mit dem
  nächsten Release 6.0.x veröffentlicht“ (SECURITY.md). 5.x, 4.x, 3.x und älter werden nicht unterstützt.
- exponential.earth bittet ebenfalls um verantwortungsvolle Meldungen („please report it responsibly to security @
  exponential [dot] earth“, <https://exponential.earth/about/project>).

### Was Sie senden

```text
Subject: Security: <component> <one-line summary>

Affected version(s): ...
Component / file / URL pattern: ...
Impact: (data exposure / privilege escalation / XSS / ...)
Steps to reproduce (on a test installation): ...
Proof of concept: ...  (no customer data)
Your contact and preferred disclosure timeline: ...
```

### Kunden und Offenlegung

- Informieren Sie die Öffentlichkeit nicht, bevor eine Korrektur veröffentlicht ist.
- Schützen Sie betroffene Kunden mit vorübergehenden Maßnahmen, die Sie selbst anwenden können (Konfiguration,
  Webserver-Regeln), aus [Sicherheitshärtung](../../../en/us/help/security-hardening.md).
- Aktualisieren Sie nach dem Release jede betroffene Kunden-Website und sagen Sie, was getan wurde, ohne Details, die
  Angreifern bei nicht aktualisierten Websites helfen.

## Verwandte Seiten

- [Sicherheitspflichten von Partnern](sicherheitspflichten.md)
- [Support-Stufen und Reaktionszeiten](support-stufen-und-reaktionszeiten.md)
- [Sicherheitshärtung](../../../en/us/help/security-hardening.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
