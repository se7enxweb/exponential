---
title: Einen Teil der Cronjobs von Hand ausführen
audience: government, admin
category: public-sector
order: 110
summary: Schritt für Schritt - einen Teil der Cronjobs oder ein Skript jetzt ausführen, unter Setup > Cronjobs mit Live-Ausgabe oder aus der Shell, zum Beispiel um freigegebene Inhalte sofort zu veröffentlichen oder nach einer Wiederherstellung.
keywords: Cronjob ausführen, Teil starten, runcronjobs, --script, workflow.php, frequent, manueller Lauf, Ausgabe, Konsolen-Alias cron, run cronjob, Run part, manual run, output, console cron alias
type: help
---
# Einen Teil der Cronjobs von Hand ausführen

Diese Aufgabe ist für Administratoren, die nicht auf den Zeitplan warten können: Freigegebene Inhalte müssen jetzt
online gehen, ein Import muss nach einer Wiederherstellung laufen, oder ein neues Skript soll ausprobiert werden.
Referenz: [Cronjobs, Abschnitt 7](../../../../anleitungen/cronjobs.md#7-einen-teil-ein-skript-oder-jeden-teil-ausführen).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Aus der Administration](#aus-der-administration)
3. [Aus der Shell](#aus-der-shell)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- `setup/managecronjobs` oder Shell-Zugang als Benutzer der Site.
- Der Teil ist nicht angehalten (ein angehaltener Teil wird von jedem Ausführenden ausgelassen).

## Aus der Administration

1. **Setup > Cronjobs**; wählen Sie die Site unter **Für Website ausführen** (ein Auftrag liest die Einstellungen
   dieses Siteaccess).
2. Klicken Sie auf der Karte des Teils auf **Teil starten** oder öffnen Sie seine Skripte und führen Sie eines allein
   aus.
3. Die Ausgabe folgt, während der Auftrag läuft; **Letzte Ausführungen** zeichnet ihn auf.

Einige zerstörerische Teile (wie das Bereinigen des Clusters) werden durch `cronjob.ini [AdminSettings]
ForbiddenParts[]` von den Schaltflächen ferngehalten und laufen nur aus einer Shell.

## Aus der Shell

```bash
php runcronjobs.php --siteaccess=site frequent --allow-root-user
php runcronjobs.php --siteaccess=site --script=workflow.php --allow-root-user
./console list cron                    # the console aliases, for example ./console cron:frequent
```

Verwenden Sie den Namen Ihres öffentlichen Siteaccess; lassen Sie `--allow-root-user` weg, wenn Sie als Benutzer der Site
ausführen (bevorzugt, damit die geschriebenen Dateien für die Site beschreibbar bleiben).

## Prüfen, ob es funktioniert hat

- Die Ausgabe endet ohne Fehler; bei `workflow.php` endet sie etwa mit „N out of M processes was finished“.
- Das Audit-Protokoll enthält `system.cronjob.run` für den Lauf.
- Bei Workflows: **Setup > Workflow Prozesse** listet die freigegebenen Prozesse nicht mehr.

## Verwandte Seiten
- [Prüfen, ob die Cronjobs laufen](pruefen-ob-die-cronjobs-laufen.md)
- [Fehlersuche bei Workflows](../../betrieb/redaktionelle-workflows/workflow-fehlerbehebung.md)
- [Konsole der Cronjobs (Hosting)](../../../hosting/betrieb/die-cronjob-konsole.md)
- [Anleitung zu den Cronjobs](../../../../anleitungen/cronjobs.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
