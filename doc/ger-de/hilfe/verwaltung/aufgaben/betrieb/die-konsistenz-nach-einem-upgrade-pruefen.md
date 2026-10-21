---
title: Die Konsistenz nach einem Upgrade prüfen
audience: government, admin, hosting
category: public-sector
order: 210
summary: Schritt für Schritt - die Prüfung der Dateikonsistenz gegen das Manifest des Release unter Setup > Upgrade-Prüfung oder mit checkmanifest.php ausführen, den Bericht herunterladen und entscheiden, was jede geänderte oder fehlende Datei bedeutet.
keywords: Upgrade-Prüfung, Dateikonsistenz, checkmanifest, filelist.md5, geänderte Dateien, fehlende Dateien, Überprüfe Dateikonsistenz, CSV herunterladen, Manifest des Release, Integrität, upgrade check, file consistency, modified files, missing files, Check file consistency, Download CSV, release manifest, integrity
type: help
---
# Die Konsistenz nach einem Upgrade prüfen

Diese Aufgabe ist für Administratoren vor und nach einem Upgrade und monatlich als Integritätsprüfung. Am Ende haben Sie
einen Bericht über jede Datei, die von dem Release abweicht, das Sie betreiben. Referenz:
[Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [In der Administration](#in-der-administration)
3. [In der Shell](#in-der-shell)
4. [Was die Befunde bedeuten](#was-die-befunde-bedeuten)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Administratorkonto (die Seite ist **Setup > Upgrade-Prüfung**) oder Shell-Zugang.
- `share/filelist.md5` des Release, das Sie betreiben, ist vorhanden (es wird mit dem Release ausgeliefert).

## In der Administration

1. **Setup > Upgrade-Prüfung** (`/setup/systemupgrade`).
2. **Überprüfe Dateikonsistenz**.
3. Lesen Sie die Befunde; **CSV herunterladen** oder **Text herunterladen** für die Akten.

Die Prüfung liest nur; jeder Lauf und jeder Download wird als `system.upgrade.run` aufgezeichnet.

## In der Shell

```bash
php bin/php/checkmanifest.php --all
php bin/php/checkmanifest.php --all --extensions
php bin/php/checkmanifest.php --all --extensions --csv > upgrade-check-$(date +%F).csv
```

Exit-Code 0, wenn nichts geändert ist oder fehlt. Der Befehl braucht weder Kernel noch Einstellungen noch Datenbank.

## Was die Befunde bedeuten

| Befund | Tun |
|---|---|
| Eine Datei, die Sie absichtlich geändert haben | die Änderung in eine Erweiterung oder ein Override verlegen; bis dahin als akzeptiert führen |
| Eine Datei, die niemand erklären kann | als mögliche Kompromittierung untersuchen ([Defacement](../../betrieb/vorfaelle/verunstaltung-der-website.md)) |
| Fehlende Dateien | sie aus dem Release wiederherstellen |
| „File share/filelist.md5 does not exist.“ | das Manifest des Release kopieren, das diese Installation betreibt |

## Verwandte Seiten
- [Das Datenbankschema prüfen](das-datenbankschema-pruefen.md)
- [Die Upgrade-Prüfung vorher und nachher](../../betrieb/aktualisierungen-und-aenderungen/upgrade-pruefung-vorher-und-nachher.md)
- [Die Upgrade-Prüfung als Monitor](../../betrieb/ueberwachung/upgrade-pruefung-als-ueberwachung.md)
- [Prüfung der Dateikonsistenz](../../../../funktionen/6.0/dateikonsistenzpruefung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
