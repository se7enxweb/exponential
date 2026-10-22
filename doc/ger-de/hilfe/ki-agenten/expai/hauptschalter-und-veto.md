---
title: Der Hauptschalter und der Veto-Hook
audience: ai-agents, admin, developer
category: ai-agents
order: 32
summary: Wie Sie Exponential Intelligence mit einer Einstellung überall oder je Siteaccess ausschalten, was an jedem Einstiegspunkt passiert, wenn es aus ist, und wie der Veto-Hook einzelne Anfragen nach Regeln ablehnt (Sektion, Klasse, Teilbaum, Siteaccess, Anbieter, Region, Zweck) oder über einen Event-Listener, der im Fehlerfall ablehnt.
keywords: Hauptschalter, AISettings Enabled, DisabledSiteAccesses, KI ausschalten, Notschalter, Veto, RequestVeto, Regeln, expai/request/veto, Event-Listener, Ablehnung im Fehlerfall, Wartungsfenster, Compliance, master switch, switch off AI, kill switch, veto, rules, event listener, fail closed, maintenance window, compliance
type: help
---
# Der Hauptschalter und der Veto-Hook

Diese Seite richtet sich an Administratoren, die den Assistenten ganz oder teilweise anhalten müssen, und an Entwickler, die ihn mit
einer Compliance-Prüfung verbinden. Zwei Kontrollen werden vor allem anderen geprüft (Berechtigungen, Datenrichtlinie, Grenzen,
Budgets), an einer Stelle, die jeder Aufruf eines Anbieters durchläuft.

## Inhalt

1. [Der Hauptschalter](#der-hauptschalter)
2. [Was passiert, wenn er aus ist](#was-passiert-wenn-er-aus-ist)
3. [Die Veto-Regeln](#die-veto-regeln)
4. [Ein Veto-Listener](#ein-veto-listener)
5. [Was festgehalten wird](#was-festgehalten-wird)

## Der Hauptschalter

```ini
[AISettings]
Enabled=disabled
# or: on, but off on these siteaccesses
Enabled=enabled
DisabledSiteAccesses[]=intranet_admin
```

`Enabled` ist aus, sofern es nicht `enabled` oder `true` sagt. Ein Siteaccess kann ihn auch in seiner eigenen
`settings/siteaccess/<siteaccess>/expai.ini.append.php` setzen. Oder aktivieren Sie die Erweiterung auf diesem Siteaccess gar nicht.
Änderungen des Schalters, geschrieben über `exp:ini` oder die Einstellungsansichten, werden im Audit-Trail als
`system.setting.write` festgehalten.

## Was passiert, wenn er aus ist

Aus der [Seite zu Schalter und Veto](https://github.com/se7enxweb/expai/blob/main/doc/switch-and-veto.md) der Erweiterung:

| Einstiegspunkt | Wenn aus |
|---|---|
| Der Reiter Intelligence | Wird nicht gezeichnet |
| Elemente des Bearbeitungsformulars und Feldaktionen | Werden nicht gezeichnet; eine Anfrage wird abgelehnt ("Exponential Intelligence is switched off.") |
| Vector | Die Seite sagt, dass KI nicht bereit ist; jede Funktion, die einen Anbieter braucht, wird abgelehnt |
| Das Dashboard | Sagt "Switched off" |
| Ketten, Übersetzungsabgleich, andere eingereihte Arbeit | Nichts Neues wird eingereiht |
| Bereits wartende eingereihte Anfragen | Angehalten und vom Cron-Teil fortgesetzt, sobald der Assistent wieder eingeschaltet ist |
| Modelltest und Probe | Abgelehnt |
| Jeder andere Aufruf eines Anbieters | Abgelehnt, bevor etwas gesendet wird |

Stapel, die vor dem Ausschalten an Anbieter gesendet wurden, werden weiterhin abgeholt, sodass nichts bereits Bezahltes verloren geht.

## Die Veto-Regeln

Ein Veto lehnt eine Anfrage mit einem Grund ab, den der Redakteur liest und der protokolliert wird. Es wird nach dem Hauptschalter
und vor den Grenzen geprüft, bei jedem Aufruf eines Anbieters und bevor eine Anfrage eingereiht wird.

```ini
[RequestVeto]
Rules[]=no_ai_on_users
Rules[]=maintenance

[RequestVeto_no_ai_on_users]
Sections[]=2
Reason=Exponential Intelligence is not used for user accounts.

[RequestVeto_maintenance]
SiteAccesses[]=intranet_admin
Purposes[]=queue.*
Reason=Queued AI work is paused on the intranet until Monday.
```

| Bedingung | Trifft zu, wenn |
|---|---|
| `Sections[]`, `Classes[]`, `Subtrees[]` | Der Inhalt der Anfrage in dieser Sektion liegt, diese Klasse hat oder an oder unter diesem Knoten liegt |
| `SiteAccesses[]` | Der Siteaccess der Anfrage (oder der, aus dem eine eingereihte Anfrage kam) |
| `Providers[]`, `Regions[]` | Der Anbieter oder seine Region (`us`, `eu`, `local`) |
| `Purposes[]` | Der Zweck, etwa `panel`, `vector`, der Name einer Feldaktion, `queue.*` |
| `Reason` | Der angezeigte und protokollierte Text |

Die erste Regel, deren Bedingungen alle zutreffen, lehnt die Anfrage ab; eine Regel ohne Bedingungen lehnt jede Anfrage ab. Die
Inhaltsbedingungen werden nicht nur für den Ausgangsknoten geprüft, sondern für **jedes Objekt, das die Werkzeuge lesen**: Ein
Objekt, das eine Regel nennt, wird mit dem Grund abgelehnt oder aus einer Liste zurückgehalten.

## Ein Veto-Listener

Für Entscheidungen, die die Einstellungen nicht ausdrücken können (ein Compliance-Dienst, Bürozeiten, ein Merkmal je Benutzer),
registrieren Sie einen Filter:

```ini
[Event]
Listeners[]=expai/request/veto@myComplianceCheck::veto
```

Der Listener erhält den Kontext der Anfrage (Benutzer, Knoten, Objekt, Klasse, Sektion, Siteaccess, Anbieter, Region, Aufbewahrung,
Modell, Zweck und was die Datenrichtlinie verlangt) und gibt ihn zurück; setzt er `veto` auf einen Text, wird die Anfrage abgelehnt.
Ein Listener, der scheitert oder nichts zurückgibt, lehnt die Anfrage ebenfalls ab: **Der Hook lehnt im Fehlerfall ab.**

## Was festgehalten wird

Jede Ablehnung ist eine Zeile des Nutzungsprotokolls mit der Fehlerklasse `veto` und der Regel oder dem Listener, die abgelehnt
haben, und ein Audit-Ereignis `system.expai.request.refused`. Es werden keine Tokens gezählt und keine Kosten geschätzt, weil nichts
gesendet wurde ([Der Audit-Trail für KI](../steuerung/audit-trail-fuer-ki.md)).

## Verwandte Seiten

- [Berechtigungen für KI-Funktionen](berechtigungen-fuer-ki-funktionen.md)
- [Datenschutz und Daten, die die Website verlassen](../steuerung/datenschutz-und-daten-die-die-website-verlassen.md)
- [Workflows und Events](../../ersteller/anwendungsentwickler/workflows-und-ereignisse.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
