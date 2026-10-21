---
title: Debug-Ausgabe im Produktivbetrieb
audience: government, admin, developer
category: public-sector
order: 350
summary: Für Administratorinnen und Administratoren öffentlicher Websites - warum die Debug-Ausgabe im Produktivbetrieb aus bleiben muss, die Debug-Einstellungen von Exponential und ihre sicheren Standardwerte, wie Sie ein Live-Problem nur für Ihre eigene Adresse debuggen und das wieder entfernen, und die eigene Fehleranzeige von PHP.
keywords: Debug-Ausgabe, Fehleranzeige, Informationspreisgabe, Produktivbetrieb, Debug-Leiste, öffentlicher Sektor, debug output, DebugOutput, DebugByIP, DebugIPList, DebugByUser, template debug, display_errors, expose_php, error.log, debug bar, information disclosure, production, TrustedProxies, public sector
type: help
---
# Debug-Ausgabe im Produktivbetrieb

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Entwicklerinnen und Entwickler.
Debug-Berichte zeigen SQL, Dateipfade, Template-Namen und Laufzeiten: nützlich während der Entwicklung, eine Landkarte
für Angreifer auf einer öffentlichen Website
([13.4 Debug-Ausgabe und Fehleranzeige](../../../installation/13-sicherheitshaertung.md#134-debug-ausgabe-und-fehleranzeige)).

## Inhalt

1. [Die sicheren Standardwerte](#die-sicheren-standardwerte)
2. [Ein Live-Problem debuggen](#ein-live-problem-debuggen)
3. [PHP selbst](#php-selbst)
4. [Prüfen](#prüfen)
5. [Verwandte Seiten](#verwandte-seiten)

## Die sicheren Standardwerte

`settings/site.ini [DebugSettings]` ab Werk:

| Einstellung | Ab Werk | Bedeutung |
|---|---|---|
| `DebugOutput` | `disabled` | kein Debug-Bericht auf den Seiten |
| `AlwaysLog[]` | `error` | Fehler werden weiterhin ins Fehlerprotokoll geschrieben |
| `ScriptDebugOutput` | `disabled` | Kommandozeilenskripte |
| `DebugByIP` | `disabled` | eingeschaltet: nur Adressen aus `DebugIPList[]` erhalten Debug-Ausgabe |
| `DebugByUser` | `disabled` | eingeschaltet: nur Benutzer aus `DebugUserIDList[]` |
| `DisplayDebugWarnings` | `disabled` | |

dazu `[TemplateSettings] Debug=disabled` (keine Template-Namen im HTML).

## Ein Live-Problem debuggen

Nur wenn es sein muss, nur für Ihre eigene Adresse, und am selben Tag wieder entfernen:

```ini
# settings/override/site.ini.append.php
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]
DebugIPList[]=203.0.113.10
```

- Einträge können einzelne Adressen oder CIDR-Bereiche sein, IPv4 und IPv6; die Debug-Leiste dokumentiert
  Bezeichnungen und Ablaufdaten für Einträge ([Die Exp-Debug-Leiste](../../../bc/6.0/debug-leiste.md)).
- `DebugByIP` vertraut der Adresse, die Exponential ermittelt; hinter einem Proxy stammt sie nur dann aus einem
  weitergeleiteten Header, wenn der Proxy in `TrustedProxies[]` steht. Ein Bereich, der weiter ist als Ihre Proxys,
  oder ein Eintrag für die Adresse des Proxys selbst gibt allen die Debug-Ausgabe.
- Der Einstellungsreiter der Debug-Leiste kann diese Werte ändern und benötigt `setup/setup`.
- Schreibvorgänge an Einstellungen werden im Audit aufgezeichnet (`system.setting.write`), sodass sich ein vergessener
  Debug-Schalter finden lässt: `/audit/console/(name)/system.setting.write`.

Eine sicherere Alternative für viele Probleme: Finden Sie den Fehler über seine Referenz in der Protokollansicht
([Einen Fehler anhand seiner Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)).

## PHP selbst

In der `php.ini` von PHP-FPM: `display_errors=Off`, `log_errors=On`, `expose_php=Off`. Velocity liefert für
FrankenPHP `display_errors=Off` und `log_errors=On` mit; für die qbix-Engine ergänzen Sie sie in
`settings/override/velocity.ini.append.php` unter `[PHPSettings] IniOptions[]`, falls Ihre CLI-`php.ini` Fehler anzeigt.

## Prüfen

```bash
./console exp:ini get site.ini/DebugSettings/DebugOutput --allow-root-user
curl -s https://www.example.gov/ | grep -i -c 'debug'          # from an address not in the list
curl -sI https://www.example.gov/ | grep -i x-powered-by        # nothing with expose_php=Off
```

Nehmen Sie `DebugOutput=disabled` in Ihre Go-live-Checkliste und Ihre monatliche Überprüfung auf.

## Verwandte Seiten

- [13.4 Debug-Ausgabe und Fehleranzeige](../../../installation/13-sicherheitshaertung.md#134-debug-ausgabe-und-fehleranzeige)
- [Sicherheit und Audit, 4](../../../anleitungen/sicherheit-und-audit.md#4-debug-ausgabe-nur-für-ihre-adresse-5-minuten)
- [Proxys und vertrauenswürdige Header](proxys-und-vertrauenswuerdige-header.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
