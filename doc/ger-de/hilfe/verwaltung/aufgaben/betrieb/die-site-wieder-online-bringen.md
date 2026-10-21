---
title: Die Site wieder online bringen
audience: government, admin, hosting
category: public-sector
order: 30
summary: Schritt für Schritt - ein Wartungsfenster sicher beenden - die Site von einer erlaubten Adresse aus prüfen, die Wartung ausschalten, die vorgeschalteten Caches leeren, prüfen, dass jeder Server mit 200 antwortet, die Cronjobs fortsetzen und das Fenster festhalten.
keywords: Wartung aus, wieder online bringen, Wartung beenden, Website wieder online bringen, maintenance.php off, Cache leeren, Velocity-Cache, Cronjobs, 200, maintenance.json, maintenance off, bring online, end maintenance, Bring the site back online, cache clear, Velocity cache
type: help
---
# Die Site wieder online bringen

Diese Aufgabe ist für Administratoren, die ein Wartungsfenster schließen. Am Ende erhalten alle wieder die echte Site
mit frischen Caches, und das Fenster ist festgehalten. Referenz:
[Wartungsmodus, Abschnitt 14](../../../../anleitungen/wartungsmodus.md#14-abläufe-schritt-für-schritt).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Wenn es sich nicht ausschalten lässt](#wenn-es-sich-nicht-ausschalten-lässt)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Arbeit ist erledigt und von einer erlaubten Adresse aus geprüft (Startseite, Anmeldung, eine Veröffentlichung an
  einem Testelement).
- Keine Installation läuft: Das Banner auf **Setup > Wartung** würde es sagen.

## Schritte

1. **Setup > Wartung**, **Website wieder online bringen**; oder in der Shell:

   ```bash
   php bin/php/maintenance.php off --allow-root-user
   ```

2. Wenn sich Inhalte oder Code geändert haben, leeren Sie die vorgeschalteten Caches, nachdem PHP und Velocity den
   neuen Code ausführen:

   ```bash
   ./console exp:velocity cache clear --allow-root-user
   php bin/php/cache.php httpcache clear --allow-root-user
   ```

   Leeren Sie ein CDN, wenn Sie eines verwenden.
3. Wärmen Sie die wichtigsten Seiten vor: `php bin/php/preload.php --siteaccess=site --allow-root-user`.
4. Setzen Sie angehaltene Teile der Cronjobs unter **Setup > Cronjobs** fort und führen Sie den häufigen Teil einmal
   aus.

## Prüfen, ob es funktioniert hat

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.gov/      # 200
ls var/maintenance.json                                                 # No such file or directory
```

Prüfen Sie über jeden Server und jede Engine, die Sie betreiben (Apache- und Velocity-Ports). Tragen Sie die Endzeit in
den Änderungsnachweis ein; das Audit-Protokoll hat `system.maintenance.change` für Ein und Aus.

## Wenn es sich nicht ausschalten lässt

| Symptom | Behebung |
|---|---|
| Auch die Administration zeigt die Wartungsseite | `php bin/php/maintenance.php off --allow-root-user` in der Shell |
| Kein PHP auf der Kommandozeile | entfernen Sie die Markierungsdatei `var/maintenance.json`; mehr ist die Wartung nicht |
| Die Site kommt mit alten Seiten zurück | leeren Sie den Cache von Velocity und das CDN |
| **Eine Installation läuft**, aber nichts installiert | ein Lauf des Kickstarters ist nach einem fehlgeschlagenen Schritt stehen geblieben; lesen Sie das Setup-Log, bevor Sie ausschalten |

## Verwandte Seiten
- [Die Site für ein Upgrade in den Wartungsmodus versetzen](die-site-fuer-ein-upgrade-in-den-wartungsmodus-versetzen.md)
- [Die Caches nach einem Deployment vorwärmen](die-caches-nach-einem-deployment-vorwaermen.md)
- [Wartungsfenster](../../../../../eng-us/help/government/operations/upgrades-and-change/maintenance-windows.md)
- [Veralteter Cache](../../../../../eng-us/help/government/operations/incidents/stale-cache.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
