---
title: Die Site für ein Upgrade in den Wartungsmodus versetzen
audience: government, admin, hosting
category: public-sector
order: 20
summary: Schritt für Schritt - eine öffentliche Website für ein geplantes Upgrade in den Wartungsmodus versetzen, mit einer klaren Meldung für die Bürger, einem erwarteten Ende, Zugang für Tester und die Administration, und prüfen, dass Besucher die Seite mit 503 erhalten.
keywords: Wartungsmodus, geplantes Upgrade, offline nehmen, 503, Setup Wartung, maintenance.php, allow-ip, allow-admin, Retry-After, Wartungsfenster, Website offline nehmen, maintenance mode, planned upgrade, take offline, Setup Maintenance, maintenance window
type: help
---
# Die Site für ein Upgrade in den Wartungsmodus versetzen

Diese Aufgabe ist für Administratoren, die ein geplantes Wartungsfenster eröffnen. Am Ende erhalten Besucher eine
Wartungsseite mit Ihrer Meldung und der Antwort `503`, während Sie und Ihre Tester die Site weiterhin sehen.
Hintergrund: [Wartungsfenster](../../betrieb/aktualisierungen-und-aenderungen/wartungsfenster.md); Referenz:
[Wartungsmodus](../../../../anleitungen/wartungsmodus.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [In der Administration](#in-der-administration)
3. [In der Shell](#in-der-shell)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Fallstricke](#fallstricke)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Änderung ist genehmigt und angekündigt; ein Sicherungsplan liegt bereit
  ([Vor einer Änderung eine Sicherung erstellen](vor-einer-aenderung-eine-sicherung-erstellen.md)).
- `setup/administrate` für die Seite oder Shell-Zugang zum Installationsverzeichnis.
- In jedem Fall Shell-Zugang, damit Sie die Wartung beenden können, wenn ein Browser es nicht kann.
- Entscheiden Sie, welche Cronjobs nicht laufen dürfen
  ([Cronjobs während der Wartung anhalten](cronjobs-waehrend-der-wartung-anhalten.md)).

## In der Administration

1. Öffnen Sie **Setup > Wartung** unter der Adresse `/admin`. Das Banner sagt **Die Website ist online**.
2. **Message for visitors**: zum Beispiel „Wir aktualisieren diesen Dienst. Zurück um 06:00 UTC. In dringenden Fällen
   rufen Sie <Ihre Servicenummer> an.“
3. **Expected duration**: Wählen Sie 15 Min., 30 Min., 1 h, 2 h oder 4 h (gesendet als `Retry-After`).
4. **Let my own address still see the site**: Markieren Sie es, um Ihre Arbeit zu prüfen.
5. **Other addresses that still see the site**: die Adressen Ihrer Tester, eine je Zeile.
6. Sehen Sie sich die **Vorschau** an.
7. Markieren Sie die Bestätigung und klicken Sie auf **Website offline nehmen**.

## In der Shell

```bash
php bin/php/maintenance.php on --message="We are upgrading this service. Back at 06:00 UTC." --until=2h --allow-admin --allow-ip=<your address> --allow-root-user
php bin/php/maintenance.php status --allow-root-user
```

`./console exp:maintenance on|off|status` tut dasselbe. Das Einschalten leert außerdem die Caches vor `index.php`; unter
Velocity pausiert dessen Antwort-Cache während der Wartung.

## Prüfen, ob es funktioniert hat

```bash
curl -sI https://www.example.gov/ | head -1      # HTTP/1.1 503 Service Unavailable
```

Aus einem anderen Netz (ein Telefon ohne WLAN) erscheint die Wartungsseite; von Ihrer Adresse aus die echte Site. Die
Änderung wird als `system.maintenance.change` aufgezeichnet und auf der Seite unter **Letzte Änderungen** gelistet.

## Fallstricke

- Eine Administration, die nicht unter `/admin` liegt (eigener Hostname), wird standardmäßig nicht durchgelassen;
  verwenden Sie `--allow-ip` für Ihre Adresse oder halten Sie eine Shell offen.
- Die REST-API antwortet ebenfalls mit 503; lassen Sie API-Clients zu, auf die Sie angewiesen sind.
- Das erwartete Ende ist kein Zeitschalter: Die Wartung bleibt eingeschaltet, bis jemand sie ausschaltet.

## Verwandte Seiten
- [Die Site wieder online bringen](die-site-wieder-online-bringen.md)
- [Wartungsmodus und geplante Ausfallzeiten](../../betrieb/verfuegbarkeit/wartungsmodus-und-geplante-ausfallzeiten.md)
- [Die Funktion Wartungsmodus](../../../../funktionen/6.0/wartungsmodus.md)
- [Wartungsmodus (Hosting)](../../../hosting/betrieb/wartungsmodus.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
