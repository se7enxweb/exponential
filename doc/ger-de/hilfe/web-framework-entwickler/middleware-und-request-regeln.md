---
title: "Middleware: Request-Regeln, Eingabe- und Ausgabe-Listener"
audience: web-framework-developer, developer
category: web-frameworks
order: 210
summary: Was in Exponential an die Stelle von Middleware, Before- und After-Filtern und Request-Listenern tritt - Request-Regeln, die nach der URL-Übersetzung und den Richtlinien zulassen, ablehnen, weiterleiten, umschreiben oder protokollieren, die Ereignisse request/input und response/output, die ezformtoken verwendet, Schalter auf Siteaccess-Ebene und wo jedes davon im Vergleich zu einer Middleware-Pipeline seinen Platz hat.
keywords: Middleware, middleware, Filter, filter, Before-Filter, before filter, After-Filter, after filter, Request-Listener, request listener, Request-Regeln, request rules, requestrules.ini, RuleList, Conditions, Action, ezpRequestRuleProvider, ezpRequestFactProvider, request/input, response/output, ezformtoken, SiteAccessRules, Pipeline, pipeline, Web-Framework-Entwickler, web framework developer
type: help
---
# Middleware: Request-Regeln, Eingabe- und Ausgabe-Listener

Diese Seite richtet sich an Entwickler, die übergreifende Request-Logik in Middleware (Laravel, Slim, Laminas, Express,
Django), in Before- und After-Filter (Rails, CodeIgniter) oder in Kernel-Listener (Symfony) legen. Exponential hat
keinen allgemeinen Middleware-Stack, dem Sie Klassen hinzufügen. Es hat drei Stellen, die zusammen die meisten
Anwendungsfälle von Middleware abdecken.

## Inhalt

1. [Die drei Stellen](#die-drei-stellen)
2. [Request-Regeln](#request-regeln)
3. [Eingabe- und Ausgabe-Listener](#eingabe--und-ausgabe-listener)
4. [Übliche Middleware, übertragen](#übliche-middleware-übertragen)
5. [Was keine Entsprechung hat](#was-keine-entsprechung-hat)

## Die drei Stellen

| Stelle | Läuft | Kann | Konfiguriert in |
|---|---|---|---|
| Listener für `request/preinput` und `request/input` | vor URL-Übersetzung und Routing | den Request untersuchen, ihn ablehnen (wie es die CSRF-Prüfung tut), Zustand einrichten | `site.ini [Event] Listeners[]` |
| Request-Regeln | nach der Übersetzung des URL-Alias und nachdem die Richtlinien die View erlaubt haben | zulassen, nicht gefunden, verboten, anmelden, weiterleiten, zum Alias weiterleiten, umschreiben, protokollieren | `settings/requestrules.ini` |
| Filter für `response/preoutput` und `response/output` | nachdem das Pagelayout gerendert wurde | die endgültige Ausgabe ändern (wie es das Einfügen des Formular-Tokens tut) | `site.ini [Event] Listeners[]` |

Die Reihenfolge innerhalb eines Requests steht unter [Der Request-Lebenszyklus](der-request-lebenszyklus.md).

## Request-Regeln

Regeln sind Einstellungen: Bedingungen und eine Aktion, der Reihe nach geprüft; die erste Regel, deren Bedingungen
alle zutreffen, entscheidet ([Request-Regeln](../../funktionen/6.0/anfrageregeln.md), vollständiger Leitfaden mit sechzehn
Rezepten: [Vollständige Sicherheit für Views](../../bc/6.0/sicherheit-von-content-view-full.md)).

```ini
# settings/siteaccess/<site>/requestrules.ini.append.php
<?php /*
[RequestRuleSettings]
RuleList[]=members_area

[Rule-members_area]
Description=Below node 864, anonymous visitors sign in first and come back.
Conditions[subtree]=864
Conditions[user]=anonymous
Action=login
*/ ?>
```

- 25 Bedingungen, darunter `requested_via` (System, Alias, Platzhalter, Startseite), `uri`, `module_view`, `view_mode`,
  `node`, `subtree`, `class`, `section`, `state`, `user`, `role`, `policy`, `siteaccess`, `host`, `header`, `ip` und
  `fact`.
- 8 Aktionen: `allow`, `notfound`, `forbidden`, `login`, `redirect`, `redirect_to_alias`, `rewrite`, `log`.
- Regeln nehmen nur Zugriff weg oder schicken den Besucher woandershin; sie gewähren nie einen Zugriff, den eine
  Richtlinie verweigert hat.
- Jede Antwort außer `allow` wird mit `private, no-store` gesendet.
- Prüfen Sie, was die Regeln entscheiden, ohne etwas zu ändern: `php bin/php/ezrequestrules.php -s site --uri=content/view/full/2`.

In PHP können Sie Bedingungen, Aktionen, Fakten (`ezpRequestFactProvider`) und ganze zur Laufzeit berechnete Regelsätze
(`ezpRequestRuleProvider`) ergänzen, registriert in `[RequestRuleSettings]`
([Die Regeln in PHP erweitern](../../bc/6.0/sicherheit-von-content-view-full.md#10-die-regeln-in-php-erweitern)).

## Eingabe- und Ausgabe-Listener

`ezformtoken` ist das Vorbild eines Middleware-Paars in Exponential. Seine `settings/site.ini.append.php` registriert:

```ini
[Event]
Listeners[]=request/input@ezxFormToken::input
Listeners[]=response/output@ezxFormToken::output
```

`ezxFormToken::input()` prüft das Token jedes POST vor dem Routing; `ezxFormToken::output()` fügt das versteckte
Token-Feld jedem Formular mit `method="post"` im endgültigen HTML hinzu. Ein Filter-Listener wie `response/output`
muss den Wert zurückgeben, den er erhält, geändert oder nicht ([Ereignisse und Listener](ereignisse-und-listener.md)).

## Übliche Middleware, übertragen

| Middleware | In Exponential |
|---|---|
| Authentifizierung für einen Bereich erforderlich | Siteaccess-Einstellung `RequireUserLogin`; eine Request-Regel mit `Action=login`; `functions` der View |
| Autorisierung | Richtlinien an der View (`functions`), Objektprüfungen in der View ([Autorisierung](autorisierung-rollen-und-richtlinien.md)) |
| CSRF | `ezformtoken` ([Formulare und CSRF](formulare-und-csrf-schutz.md)) |
| IP-Freigabeliste für einen Bereich | eine Request-Regel mit `Conditions[ip]` |
| Alte Adressen weiterleiten | URL-Platzhalter oder eine Request-Regel mit `redirect` ([Sprechende URLs](url-aliase-und-platzhalter.md)) |
| Wartungsmodus | `expMaintenance` in `index.php` (`php bin/php/maintenance.php on`) ([Wartungsmodus](../../funktionen/6.0/wartungsmodus.md)) |
| Sicherheits-Header, Content Security Policy | `site.ini [ContentSecurityPolicy]` und die Sicherheits-Header ([Content Security Policy](../../funktionen/6.0/content-security-policy.md)) |
| Response-Caching | der View-Cache, der rollenbewusste HTTP-Cache, der Velocity-Response-Cache ([Caching](cache-ebenen.md)) |
| Request-Protokollierung | Request-Logs und das Audit-Protokoll ([Request-Logs](../../funktionen/6.0/anfrageprotokolle.md), [Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md)) |
| Vertrauenswürdige Proxies | `site.ini [HTTPHeaderSettings] TrustedProxies[]` ([Vertrauenswürdige Proxies](../../bc/6.0/vertrauenswuerdige-proxys.md)) |

## Was keine Entsprechung hat

- **Middleware-Listen pro Route** (`->middleware('auth')` an einer Route): Verwenden Sie `functions` der View und
  Request-Regeln mit einer Bedingung `module_view`.
- **Den gesamten Aufruf in eine Closure einhüllen** (`$next($request)`): Listener laufen davor oder danach, nicht darum
  herum. Die Runnable-Ereignisse (`runnable/view/before` und `after`) kommen dem für eine einzelne View am nächsten.
- **Reihenfolge der Middleware nach Priorität**: Listener laufen in der Reihenfolge von `Listeners[]`, Regeln in der
  Reihenfolge von `RuleList[]`.

## Verwandte Seiten

- [Härtung](../../bc/6.0/haertung.md)
- [Sicherheit für Anwendungen](../ersteller/anwendungsentwickler/sicherheit-fuer-anwendungen.md)
- [Muster: Event-Hook](../../standards/muster/ereignis-hook.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
