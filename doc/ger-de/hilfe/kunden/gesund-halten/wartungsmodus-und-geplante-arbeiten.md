---
title: Wartungsmodus und geplante Arbeiten
audience: customer, admin, editor
category: customer-success
order: 13
summary: Wie ein Betreiber Arbeiten plant, die eine Exponential-Website vom Netz nehmen - was Besucher und Suchmaschinen sehen, was in die Nachricht gehört, wer weiterhin hineinkommt, was anzukündigen und was vorher und nachher zu prüfen ist.
keywords: Wartungsmodus, geplante Arbeiten, Wartungsfenster, 503, Retry-After, Ankündigung, Nachricht, erlaubte Adressen, maintenance mode, planned work, maintenance window, announcement, message, allowed addresses
type: help
---
# Wartungsmodus und geplante Arbeiten

Manche Arbeiten, etwa ein Upgrade, ein großer Import oder eine Reparatur, sind sicherer, wenn die Website kurz vom Netz
ist. Exponential hat dafür einen Wartungsmodus. Diese Seite ist der Teil des Betreibers: entscheiden, ankündigen und
prüfen. Die Person, die die Arbeit erledigt, folgt [Wartungsmodus](../../../anleitungen/wartungsmodus.md), und die Sicht
für Unternehmen steht unter [Wartungsmodus und geplante Arbeiten für Unternehmen](../../unternehmen/zuverlaessigkeit/wartungsmodus-und-geplante-arbeiten.md).

## Was Besucher und Suchmaschinen sehen

Solange der Wartungsmodus eingeschaltet ist, erhält jede Seitenanfrage eine Wartungsseite mit dem Status
`503 Service Unavailable`, `Retry-After` und `no-store`; Bilder, Stylesheets und Skripte werden weiterhin ausgeliefert,
und die Administration unter `/admin` bleibt erreichbar ([Wartungsmodus](../../../anleitungen/wartungsmodus.md)). „Ein 503
mit `Retry-After` ist die Antwort, die Suchmaschinen als ‚später wiederkommen‘ verstehen: Die Seiten behalten ihren
Platz im Index“ (derselbe Leitfaden).

Die Seite zeigt eine optionale Nachricht und ein optionales voraussichtliches Ende. Die Wartung „endet nie von selbst,
wenn die erwartete Zeit verstreicht“: Jemand muss die Website zurückholen.

## Was der Betreiber entscheidet

| Entscheidung | Hinweise |
|---|---|
| Ob die Arbeit die Website vom Netz nehmen muss | Viele Änderungen müssen das nicht; fragen Sie die Person, die die Arbeit erledigt ([Unterbrechungsfreie Deployments](../../unternehmen/zuverlaessigkeit/unterbrechungsfreie-deployments.md)) |
| Wann | Außerhalb Ihrer geschäftigsten Zeiten; nie während einer Kampagne oder eines Newsletter-Versands |
| Wie lange, ehrlich | Die Seite kann das voraussichtliche Ende zeigen |
| Die Nachricht | In den Worten der Besucher, mit der Angabe, wann die Website zurück ist |
| Wer die Website weiterhin sieht | Ihre eigenen Tester können über ihre Adresse zugelassen werden |
| Wer es ankündigt | Redaktion, Newsletter, soziale Medien, wie es zu Ihrem Publikum passt |

## Eine Nachricht, die funktioniert

Das Beispiel des Leitfadens: „We are upgrading the site. Back at 06:00 UTC.“ Halten Sie sie bei einem oder zwei
Sätzen, nennen Sie eine Uhrzeit mit Zeitzone, und versprechen Sie nicht mehr, als Ihnen die Person gesagt hat, die die
Arbeit erledigt. Für einen Notfall lautet das Beispiel des Leitfadens: „We are fixing a problem. Back shortly.“
([Wartungsmodus](../../../anleitungen/wartungsmodus.md)).

## Vor dem Zeitfenster

- [ ] Die Person, die die Arbeit erledigt, hat ein Backup und einen Weg zurück hinein über die Shell des Servers, falls
      die Administration nicht erreichbar sein sollte (der Ablauf für geplante Upgrades im Leitfaden verlangt beides).
- [ ] Besucher und Mitarbeitende sind informiert.
- [ ] Die Adressen der Tester sind der Person übergeben, die die Arbeit erledigt, wenn diese die Website während des
      Zeitfensters sehen müssen.
- [ ] Geplante Versände (Newsletter, Kampagnen) sind aus dem Zeitfenster heraus verschoben.
- [ ] Ihre externe Überwachung ist pausiert oder so eingestellt, dass sie den 503 akzeptiert ([Überwachung](ueberwachung.md)).

## Nach dem Zeitfenster

- [ ] Die Website antwortet für alle, über jeden Server, der sie ausliefert (die Sicherheits-Checkliste des Leitfadens).
- [ ] Caches vor der Website wurden geleert, wenn sich Inhalte geändert haben.
- [ ] Sie haben Ihre Testliste durchlaufen ([Checkliste für den Upgrade-Tag](checkliste-upgrade-tag.md)).
- [ ] Die Überwachung ist wieder eingeschaltet.

## Der Nachweis

„Jedes Ein- und Ausschalten wird ins Prüfprotokoll geschrieben (`system.maintenance.change`); die Seite listet die
letzten acht“ ([Wartungsmodus](../../../anleitungen/wartungsmodus.md)). Die Zeiten, in denen die Website vom Netz war, und
wer sie umgeschaltet hat, sind also für Ihren Monatsbericht festgehalten.

> **Hinweis**
> Der Wartungsmodus betrifft nur Webanfragen. Befehle und Cronjobs laufen weiter ([Wartungsmodus](../../../anleitungen/wartungsmodus.md));
> die Person, die die Arbeit erledigt, hält diejenigen an, die nicht laufen dürfen.

## Verwandte Seiten

- [Checkliste für den Upgrade-Tag](checkliste-upgrade-tag.md)
- [Wenn die Website nicht erreichbar ist](wenn-die-website-nicht-erreichbar-ist.md)
- [Wartungsmodus (Funktionsreferenz)](../../../../eng-us/features/6.0/maintenance-mode.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
