---
title: "Einwand: „Ist Open Source sicher?“"
audience: advocate
category: advocacy
order: 35
summary: Wie Sie „Open Source ist weniger sicher“ oder „Jeder kann den Code lesen und angreifen“ mit dem dokumentierten Sicherheitsprozess von Exponential beantworten, mit den Belegen, die ein Skeptiker prüfen kann, und den Grenzen, die ein ehrlicher Fürsprecher nennt.
keywords: Einwand, Sicherheit von Open Source, Sicherheitslücken, öffentlicher Code, Angreifer, Sicherheitsprozess, SECURITY.md, Offenlegung, Härtung, Prüfprotokoll, CSP, Lieferkette, objection, open source security, vulnerabilities, public code, attackers, security process, disclosure, hardening, audit trail, supply chain
type: help
---
# Einwand: „Ist Open Source sicher?“

Diese Seite ist für Fürsprecher, die hören: „Open Source ist weniger sicher“, „Jeder kann den Code lesen und Lücken
finden“ oder „Wer behebt es, wenn etwas gefunden wird?“ Diskutieren Sie nicht über Open Source im Allgemeinen. Zeigen
Sie den Prozess, den Exponential dokumentiert, und wie man ihn prüft.

## Inhalt

1. [Die Frage umformulieren](#die-frage-umformulieren)
2. [Der Prozess, Schritt für Schritt](#der-prozess-schritt-für-schritt)
3. [Was ein Website-Betreiber tun kann](#was-ein-website-betreiber-tun-kann)
4. [Antwortvorlagen](#antwortvorlagen)
5. [Sagen Sie nicht](#sagen-sie-nicht)

## Die Frage umformulieren

„Ist es sicher?“ lässt sich für keine Software mit Ja oder Nein beantworten. Machen Sie daraus Fragen, die Antworten haben:

| Statt | Fragen Sie |
|---|---|
| „Ist es sicher?“ | „Wie werden Sicherheitslücken gemeldet und behoben?“ |
| „Können Angreifer den Code lesen?“ | „Können Verteidiger ihn auch lesen und jede Änderung sehen?“ |
| „Wer ist verantwortlich?“ | „Wer beantwortet eine Meldung, und wie schnell?“ |
| „Ist unsere Website sicher?“ | „Was müssen unsere Administratoren tun?“ |

## Der Prozess, Schritt für Schritt

| Schritt | Was geschieht | Quelle |
|---|---|---|
| 1. Melden | Vertraulich per E-Mail oder über die vertrauliche Meldung von Sicherheitslücken bei GitHub; nie öffentlich | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md), [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#sicherheitsprobleme) |
| 2. Erste Antwort | „innerhalb eines Werktags“ | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| 3. Entscheidung | Die meldende Person erfährt, ob die Meldung angenommen wird, und wird auf dem Laufenden gehalten | ebenda |
| 4. Korrektur | Auf `main`, veröffentlicht mit der nächsten Version 6.0.x | [Sicherheitskorrekturen](../wer-ist-die-exponential-foundation.md#sicherheitskorrekturen) |
| 5. Release | Eine neue Version; veröffentlichte Versionen ändern sich nie | [Releases und Versionsnummern](../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern) |
| 6. Upstream | Sicherheitskorrekturen gehen auch an Abhängigkeiten (Zeta Components) | [Der PHP-Stack](../wer-ist-die-exponential-foundation.md#der-php-stack-auf-dem-es-steht-und-dem-es-etwas-zurückgibt) |

Offener Code hilft hier: Jeder kann jede Änderung in der öffentlichen Historie lesen, auch Sicherheitskorrekturen, und
prüfen, was ein Release enthält ([Grundsätze, die man im Code sieht](../wer-ist-7x.md#grundsätze-die-sie-im-code-sehen-können)).

## Was ein Website-Betreiber tun kann

Die meisten Vorfälle bei jedem CMS entstehen durch die Art, wie eine Website betrieben wird. Die Dokumentation gibt
Administratoren:

- eine [Anleitung zur Härtung](../sicherheitshaertung.md) Bedrohung für Bedrohung, mit einer [Checkliste](../sicherheitshaertung.md#checkliste);
- die [Content Security Policy](../../../eng-us/features/6.0/content-security-policy.md);
- das [Prüfprotokoll](../../../eng-us/features/6.0/audit-trail.md) und die [Request-Logs](../../../eng-us/features/6.0/request-logs.md);
- die [zweistufige Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md);
- erzeugte Administratorpasswörter bei neuen Installationen ([Das Standardpasswort des Administrators](../standard-administratorpasswort.md)).

## Antwortvorlagen

```text
SHORT
Security is a process, not a claim. Exponential's is written down: private reports, a first answer within
one business day, fixes in the next 6.0.x release (SECURITY.md). Admins get a hardening guide and checklist.

LONGER
Open code cuts both ways: attackers can read it, but so can defenders, and every fix is visible in the
public history. What matters is the process. For Exponential: reports go privately to the address in
SECURITY.md with a first answer within one business day; accepted issues are fixed on main and shipped in
the next 6.0.x release; published versions never change. For your own site, the hardening guide and its
checklist are the place to start. (links)
```

## Sagen Sie nicht

- „Open Source ist immer sicherer.“ Das ist eine Debatte, keine Tatsache.
- „Es wurde nie gehackt.“ Keine Quelle sagt das.
- „Sicherheit ist garantiert.“ Nichts ist garantiert.
- Details einer Sicherheitslücke, die nicht öffentlich ist. Wenn Ihnen jemand eine erzählt, folgen Sie
  [Sicherheitsmeldungen bleiben vertraulich](sicherheitsmeldungen-bleiben-vertraulich.md).

## Verwandte Seiten

- [Gesprächspunkte: Sicherheit](gespraechspunkte-sicherheit.md)
- [Für Sicherheit eintreten](fuer-sicherheit-eintreten.md)
- [Mit Einwänden umgehen, Sicherheit](../geschaeft/einwandbehandlung.md#sicherheit)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
