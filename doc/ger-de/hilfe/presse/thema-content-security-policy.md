---
title: "Thema: die Content Security Policy"
audience: press
category: press-media
order: 60
summary: Ein Themenbeitrag über die Content Security Policy von Exponential - standardmäßig nur meldend in der Administration, auf öffentlichen Websites aus, eine ohne Inline-Skripte neu gebaute Administration, damit eine strikte Richtlinie durchgesetzt werden kann, und ein Protokoll für Meldungen - mit druckbaren Fakten, Quellen und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, Content Security Policy, CSP, nur melden, durchsetzen, Inline-Skripte, XSS, Administration, Sicherheits-Header, Presse, story, report-only, enforce, inline scripts, administration, security headers, press
type: help
---
# Thema: die Content Security Policy

Diese Seite ist für Technik- und Sicherheitsjournalisten. Eine Content Security Policy ist eine Browserregel, die
einschränkt, welche Skripte und Styles eine Seite ausführen darf. Im Oktober 2026 wurde die Administration von
Exponential so umgebaut, dass sich eine strikte Richtlinie einschalten lässt. Diese Seite erklärt, was das bedeutet und
wie Sie es genau beschreiben.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
5. [Den Lesern erklären](#den-lesern-erklären)
6. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
7. [Wen Sie fragen](#wen-sie-fragen)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

„Eine strikte Richtlinie (`script-src 'self'`, kein `'unsafe-inline'`) bedeutet, dass ein Skript, das über einen
gespeicherten oder reflektierten Wert in eine Seite eingeschleust wird, nicht läuft, selbst wenn ein Template vergisst,
es zu maskieren“ ([Content Security Policy](../../../eng-us/features/6.0/content-security-policy.md)). Um eine solche Richtlinie
möglich zu machen, wurden die Administrationsdesigns admin4 und admin4l so geändert, dass sie „kein Inline-`<script>`,
kein `on...=`-Attribut, keinen `javascript:`-Link, kein `style=""`-Attribut und kein `<style>`-Element“ enthalten
([Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#16-october-2026-the-administration-runs-without-inline-scripts-handlers-and-styles)).
Die Richtlinie wird in der Administration im Modus „nur melden“ ausgeliefert und ist auf öffentlichen Websites aus; der
Administrator entscheidet, wann er sie durchsetzt, und das Zurücknehmen ist eine einzige Einstellung.

## Was neu ist, und seit wann

Alles in der Entwicklungslinie 6.0.15, die nicht erschienen ist.

| Was | Wann | Quelle |
|---|---|---|
| Die Einstellung für die Richtlinie, standardmäßig nur meldend in der Administration | 16. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#16-october-2026-a-content-security-policy-report-only-on-the-administration) |
| Die Administration ohne Inline-Skripte, Handler und Styles | 16. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#16-october-2026-the-administration-runs-without-inline-scripts-handlers-and-styles) |
| Was das Durchsetzen braucht: zusätzliche Quellen, eine Nonce pro Anfrage, eine strengere Begleitrichtlinie | 17. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#17-october-2026-enforcing-the-content-security-policy) |
| Meldungen in einem eigenen Protokoll, mit Ratenbegrenzung | 17. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#17-october-2026-the-reports-of-the-content-security-policy-in-a-log-of-their-own) |
| Erweiterungen ohne Inline-Skripte in ihren Templates | 17. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#17-october-2026-extensions-without-inline-scripts-in-their-templates) |

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Standardmäßig ist die Richtlinie „in der Administration nur meldend und auf den Websites aus“. | [Content Security Policy](../../../eng-us/features/6.0/content-security-policy.md) |
| Die mitgelieferte Richtlinie erlaubt Skripte und Styles nur von der Website selbst (`'self'`) und blockiert Plugins (`object-src 'none'`). | [Content Security Policy, Einstellungen](../../../eng-us/features/6.0/content-security-policy.md#settings) |
| Im Modus „nur melden“ meldet der Browser, was er blockieren würde, und blockiert nichts. | ebenda |
| Das Zurücknehmen nach dem Durchsetzen ist eine Einstellung: `Mode=report-only`. | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#17-october-2026-enforcing-the-content-security-policy) |
| Der Online-Editor fällt von TinyMCE 3 auf TinyMCE 8 zurück, wo eine strikte Richtlinie durchgesetzt wird. | ebenda |
| Neue Templates der Administration dürfen keine Inline-Styles oder -Skripte enthalten; Mitwirkende müssen die Regel befolgen. | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| Meldungen gehen nach `var/log/csp.log`, begrenzt pro Client und insgesamt. | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#17-october-2026-the-reports-of-the-content-security-policy-in-a-log-of-their-own) |

## Was Sie nicht behaupten sollten

- „Exponential blockiert Cross-Site-Scripting.“ Standardmäßig meldet die Administration nur; das Durchsetzen ist die
  Entscheidung des Administrators, und öffentliche Websites haben keine Richtlinie, wenn keine gesetzt ist.
- „Jede Erweiterung ist für eine durchgesetzte Richtlinie bereit.“ Das Changelog führt eine schrumpfende Ausnahmeliste
  von Erweiterungen, die noch Inline-Code haben.
- „Die CSP schützt Sie“ ohne den Modus. Sagen Sie immer „nur melden“ oder „durchgesetzt“.

## Den Lesern erklären

```text
A Content Security Policy is a list the website sends to the browser: "only run scripts that come from my own
server". If an attacker manages to slip a script into a page, the browser refuses to run it. Exponential's
administration was rewritten so that this list can be strict. It starts in "report" mode, where the browser
only tells the site what it would have blocked.
```

Auf Deutsch: Eine Content Security Policy ist eine Liste, die die Website an den Browser schickt: „Führe nur Skripte
aus, die von meinem eigenen Server kommen.“ Gelingt es einem Angreifer, ein Skript in eine Seite zu schmuggeln,
weigert sich der Browser, es auszuführen. Die Administration von Exponential wurde neu geschrieben, damit diese Liste
streng sein kann. Sie beginnt im Modus „melden“, in dem der Browser der Website nur mitteilt, was er blockiert hätte.

## Blickwinkel und Ideen für Überschriften

```text
"Rewriting an administration so the browser can say no"
"Report first, enforce later: how Exponential rolls out a strict browser policy"
"No inline scripts: the quiet security rule behind Exponential's new admin"
```

Auf Deutsch zum Beispiel: „Eine Administration neu schreiben, damit der Browser Nein sagen kann“, „Erst melden, dann
durchsetzen: wie Exponential eine strikte Browserrichtlinie einführt“, „Keine Inline-Skripte: die stille
Sicherheitsregel hinter der neuen Administration von Exponential“.

## Wen Sie fragen

| Frage | Fragen Sie | Wo |
|---|---|---|
| Warum zuerst nur melden, Pläne für das Durchsetzen | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Eine Umgehung oder Sicherheitslücke | vertraulich | security@se7enx.com ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |

## Verwandte Seiten

- [Thema: Sicherheit](thema-sicherheit.md)
- [Content Security Policy (Funktionsseite)](../../../eng-us/features/6.0/content-security-policy.md)
- [Gesprächspunkte: Sicherheit](../fuersprecher/gespraechspunkte-sicherheit.md)
- [Funktionsthemen: Übersicht](../../../eng-us/help/press/feature-stories-index.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
