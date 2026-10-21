---
title: Verunstaltung der Website
audience: government, admin, project-manager
category: public-sector
order: 50
summary: Runbook für eine öffentliche Website, die Inhalte zeigt, die niemand freigegeben hat - mit dem Wartungsmodus eindämmen, herausfinden, ob es über ein Konto (Audit-Trail) oder über die Dateien (Prüfung der Dateikonsistenz) geschah, wiederherstellen und kommunizieren.
keywords: Verunstaltung, gehackte Website, nicht autorisierte Inhalte, Vandalismus, Kompromittierung, Wartungsmodus, Audit-Trail, checkmanifest, Dateiintegrität, Wiederherstellung, Versionsverlauf, Vorfall, defacement, hacked site, unauthorized content, vandalism, compromise, maintenance mode, audit trail, file integrity, restore, version history, incident
type: help
---
# Verunstaltung der Website

Dieses Runbook richtet sich an die Administration und die Vorfallsleitung, wenn eine öffentliche Website Texte oder
Bilder zeigt, die niemand in der Organisation veröffentlicht hat. Die Verunstaltung einer Verwaltungswebsite schadet dem
Vertrauen schnell; dämmen Sie zuerst ein und untersuchen Sie danach. Beginnen Sie mit den
[Grundlagen der Vorfallsbehandlung](grundlagen-der-vorfallsbehandlung.md); das ausführlichere Vorgehen beschreibt
[Kompromittierte Website (Hosting-Runbook)](../../../hosting/runbooks/kompromittierte-website.md).

## Inhalt
1. [Eindämmen](#eindämmen)
2. [Über ein Konto oder über die Dateien?](#über-ein-konto-oder-über-die-dateien)
3. [Verunstaltete Inhalte: reparieren](#verunstaltete-inhalte-reparieren)
4. [Verunstaltete Dateien: neu aufbauen](#verunstaltete-dateien-neu-aufbauen)
5. [Kommunizieren und abschließen](#kommunizieren-und-abschließen)
6. [Verwandte Seiten](#verwandte-seiten)

## Eindämmen

```bash
php bin/php/maintenance.php on --message="This service is temporarily unavailable." --allow-admin --allow-ip=<your address> --allow-root-user
```

Wenn die Verunstaltung über ein Administratorkonto gekommen sein könnte, beschränken Sie die Administration zusätzlich
auf Netzwerkebene ([Die Administration einschränken](../../../hosting/sicherheit/die-administration-einschraenken.md)).
Sichern Sie Beweise, bevor Sie etwas ändern (Dateien, Datenbank-Dump, `var/log/` mit `audit/`).

## Über ein Konto oder über die Dateien?

| Prüfung | Befehl oder Seite | Deutet hin auf |
|---|---|---|
| Wer hat die verunstalteten Elemente veröffentlicht oder bearbeitet? | die Versionsseite des Elements (`/content/history/<object id>`): Ersteller jeder Version; Reiter **Audit**, **Konsole**, Kanal `content` | ein Konto |
| Wer hat sich kürzlich angemeldet, und von wo? | `./console exp:audit tail --channel=access --lines=50 --allow-root-user` | ein Konto |
| Wurden Rollen geändert? | die Warnung `admin_role_granted`; Audit-Kanal `access` | ein Konto mit ausgeweiteten Rechten |
| Haben sich Kernel-Dateien geändert? | `php bin/php/checkmanifest.php --all --extensions` | die Dateien |
| Gibt es unerwartete PHP-Dateien dort, wo PHP schreiben kann? | `find var/storage -name '*.php'` | die Dateien |
| Ist der Audit-Trail unversehrt? | `./console exp:audit verify --allow-root-user` | Manipulation |

## Verunstaltete Inhalte: reparieren

1. Deaktivieren Sie das verwendete Konto ([Verdacht auf ein kompromittiertes Konto](verdacht-auf-kompromittiertes-konto.md)).
2. Öffnen Sie für jedes verunstaltete Objekt seine Versionsseite, wählen Sie bei der letzten guten Version
   **Neuen Entwurf machen** und veröffentlichen Sie ihn (er durchläuft den Freigabe-Workflow wie jede Version)
   ([Versionsverlauf und Nachvollziehbarkeit](../../../../../eng-us/help/government/operations/editorial-workflows/version-history-and-accountability.md)).
3. Entfernen Sie Elemente, die der Angreifer angelegt hat; prüfen Sie den Papierkorb auf Elemente, die er entfernt hat,
   und stellen Sie sie wieder her.
4. Viele Elemente betroffen: Erwägen Sie stattdessen, die Datenbank aus der letzten sauberen Datensicherung
   wiederherzustellen, und nehmen Sie den Verlust legitimer Änderungen seitdem in Kauf
   ([Planung der Rücknahme](../aktualisierungen-und-aenderungen/planung-der-ruecknahme.md)).

## Verunstaltete Dateien: neu aufbauen

Vertrauen Sie keinen Dateien aus dem kompromittierten Verzeichnisbaum: Stellen Sie den Code des Tags der Version aus
einer bekannt guten Quelle bereit, stellen Sie `var/storage` und die Einstellungen aus einer sauberen Datensicherung
wieder her, aktualisieren Sie auf die neueste 6.0.x, falls die Lücke bekannt sein könnte, und ändern Sie alle
Zugangsdaten, die die Website enthält. Folgen Sie den Abschnitten „Eradicate and rebuild“ und „Recover“ des
Hosting-Runbooks.

## Kommunizieren und abschließen

1. Leeren Sie jeden Cache, damit keine verunstaltete Seite ausgeliefert wird:
   `php bin/php/ezcache.php --clear-all --allow-root-user`, dann
   `./console exp:velocity cache clear --allow-root-user`; leeren Sie ein CDN, falls Sie eines nutzen.
2. Bringen Sie die Website wieder online und prüfen Sie sie von außen.
3. Öffentliche Erklärung wie mit der Kommunikation vereinbart; wenn personenbezogene Daten betroffen sein könnten,
   siehe [Meldung von Datenschutzverletzungen](meldung-von-datenschutzverletzungen.md).
4. Melden Sie den Vorfall Ihrem Sicherheitsteam oder der nationalen Behörde, wie es Ihre Regeln verlangen; halten Sie
   eine [Nachbetrachtung des Vorfalls](nachbetrachtung-eines-vorfalls.md) ab.

## Verwandte Seiten
- [Upgrade-Prüfung als Überwachung](../ueberwachung/upgrade-pruefung-als-ueberwachung.md)
- [Prüfung des Audit-Trails](../ueberwachung/pruefung-des-audit-trails.md)
- [Massenhafter Inhaltsfehler](massenhafter-inhaltsfehler.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
