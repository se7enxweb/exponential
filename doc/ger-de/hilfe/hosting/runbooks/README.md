---
title: Runbooks für Hoster: Überblick
audience: hosting
category: hosting
order: 10
summary: Runbooks Schritt für Schritt für die Vorfälle, auf die ein Hoster bei Exponential-Websites trifft - Website nicht erreichbar, langsame Website, Festplatte voll, Wiederherstellung aus einem Backup, kompromittierte Website, PHP-Upgrade, Ablauf von Zertifikaten und Velocity, das nicht startet.
keywords: Runbook, Runbooks, Vorfall, Ausfall, Website nicht erreichbar, langsam, Festplatte voll, Wiederherstellung, kompromittiert, gehackt, PHP-Upgrade, Zertifikat, Velocity, Bereitschaft, incident, outage, site down, slow, disk full, restore, compromised, hacked, PHP upgrade, certificate, on-call
type: help
---
# Runbooks für Hoster: Überblick

Ein Runbook ist die Seite, die die Bereitschaft öffnet, wenn etwas nicht stimmt. Jedes beginnt mit den Symptomen, dann den ersten
Minuten, dann Diagnose und Abhilfe, und endet mit dem, was danach zu tun ist. Die Befehle sind die aus dem Kapitel zur Fehlersuche des
Installationsbuchs ([Kapitel 12](../../../installation/12-fehlerbehebung.md)), für einen Vorfall geordnet.

## Seiten in diesem Bereich

| Runbook | Wann |
|---|---|
| [Website nicht erreichbar](website-nicht-erreichbar.md) | Seiten liefern Fehler oder gar nichts |
| [Langsame Website](langsame-website.md) | Seiten laden, aber langsam |
| [Festplatte voll](festplatte-voll.md) | Schreibvorgänge scheitern, Uploads scheitern, Logs hören auf |
| [Aus einem Backup wiederherstellen](aus-einem-backup-wiederherstellen.md) | Daten gingen verloren oder wurden beschädigt und müssen aus einem Backup zurückkommen |
| [Kompromittierte Website](kompromittierte-website.md) | Sie vermuten, dass ein Angreifer etwas geändert hat |
| [PHP-Upgrade](php-upgrade.md) | ein geplanter Wechsel auf eine neuere PHP-Version |
| [Ablauf von Zertifikaten](ablauf-von-zertifikaten.md) | HTTPS-Warnungen oder ein Zertifikat kurz vor dem Ablauf |
| [Velocity startet nicht](velocity-startet-nicht.md) | die Engine Velocity kommt nicht hoch |
| [Probleme mit dem Demomodus](probleme-mit-dem-demomodus.md) | der Demomodus zeigt sich nicht, ein Aufbau einer Demo scheitert mit einem erzeugten Passwort, oder der Demomodus zeigt sich auf einer echten Website |

## Regeln für jeden Vorfall

1. **Schreiben Sie die Uhrzeit auf**, zu der Sie begonnen haben, und jeden Schritt, den Sie tun. Der Log-Viewer und das Prüfprotokoll
   nutzen genaue Zeiten.
2. **Lesen, bevor Sie ändern.** Der erste Blick gilt immer `var/log/error.log` und dem Log der Engine oder des Webservers
   ([12.2 Wo die Logs liegen](../../../installation/12-fehlerbehebung.md#122-wo-die-logs-liegen)).
3. **Ändern Sie eine Sache nach der anderen**, und leeren Sie den kleinsten Cache, der sie abdeckt.
4. **Führen Sie Befehle als Eigentümer der Installation aus**, nicht als root; ein als root ausgeführtes Skript hinterlässt Dateien, die
   die Website nicht ändern kann ([12.3](../../../installation/12-fehlerbehebung.md#123-berechtigungen-und-eigentümerschaft)).
5. **Informieren Sie den Kunden** früh, mit dem, was Sie wissen, und wann Sie ihn auf den neuesten Stand bringen.

## Die Befehle, die Sie am häufigsten nutzen

```bash
./console exp:logs --tail=var/log/error.log --lines=50     # the last 50 errors
./console exp:logs --reference=ERR-0123456789               # one error page's details
php bin/php/console exp:velocity status --allow-root-user   # is Velocity running and answering?
php bin/php/maintenance.php status --allow-root-user        # is maintenance mode on?
php bin/php/ezcache.php --clear-all --allow-root-user       # when unsure which cache
php bin/php/console exp:velocity cache clear --allow-root-user  # Velocity's response cache, after a restart
```

## Verwandte Seiten

- [Checklisten für Hoster](../checklisten/README.md)
- [Überblick über die Überwachung](../betrieb/ueberblick-ueber-die-ueberwachung.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
