---
title: Upgrade-Prüfung als Überwachung
audience: government, admin, hosting
category: public-sector
order: 110
summary: Die Prüfung der Dateikonsistenz (checkmanifest.php und Setup > Upgrade-Prüfung) als geplante Integritätsüberwachung einsetzen, damit eine nicht autorisierte Änderung am Code einer öffentlichen Website - eine Verunstaltung, eine Web-Shell, eine Änderung von Hand - bemerkt wird.
keywords: Dateiintegrität, Integritätsüberwachung, checkmanifest, filelist.md5, Dateikonsistenz, Erkennung von Verunstaltungen, Web-Shell, nicht autorisierte Änderung, Upgrade-Prüfung, geplante Prüfung, file integrity, integrity monitoring, file consistency, defacement detection, web shell, unauthorized change, upgrade check, scheduled check
type: help
---
# Upgrade-Prüfung als Überwachung

Diese Seite richtet sich an die Administration und Informationssicherheitsbeauftragte, die wissen möchten, wenn sich der
Code einer öffentlichen Website außerhalb einer Bereitstellung ändert. Die Prüfung der Dateikonsistenz vergleicht die
Dateien der Installation mit dem Release-Manifest `share/filelist.md5`; nach Zeitplan ausgeführt, ist sie eine einfache
Integritätsüberwachung. Referenz: [Prüfung der Dateikonsistenz](../../../../../eng-us/features/6.0/file-consistency-check.md) und
[Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md).

## Inhalt
1. [Was sie erkennt](#was-sie-erkennt)
2. [Einplanen](#einplanen)
3. [Eine saubere Ausgangslage schaffen](#eine-saubere-ausgangslage-schaffen)
4. [Was sie nicht erkennt](#was-sie-nicht-erkennt)
5. [Verwandte Seiten](#verwandte-seiten)

## Was sie erkennt

- Eine Kernel-Datei oder ausgelieferte Datei, die geändert (Prüfsumme weicht ab) oder entfernt wurde.
- Mit `--extensions` dasselbe für jedes Erweiterungsverzeichnis, das ein eigenes Manifest mitbringt.

Das deckt eine Änderung von Hand auf dem Live-Server, eine von einem Angreifer ersetzte Datei und eine unvollständige
Bereitstellung ab.

## Einplanen

```bash
php bin/php/checkmanifest.php --all --extensions
```

Der Befehl braucht weder Kernel noch Einstellungen noch Datenbank und läuft daher auch, wenn die Website defekt ist. Er
gibt Hinweise als Zeilen `warning:` und Probleme als Zeilen `error:` aus und endet mit 0, wenn nichts geändert ist oder
fehlt. Planen Sie ihn täglich über Ihr Überwachungssystem oder die Crontab ein und lösen Sie bei einem Exit-Code
ungleich null eine Warnung aus. In der Administration ist dieselbe Prüfung **Setup > Upgrade-Prüfung**,
**Überprüfe Dateikonsistenz**; jeder Lauf wird als `system.upgrade.run` aufgezeichnet.

## Eine saubere Ausgangslage schaffen

Die Überwachung ist nur nützlich, wenn „nichts geändert“ der Normalzustand ist:

1. Führen Sie die Prüfung aus; listen Sie jede geänderte Datei auf.
2. Verlagern Sie beabsichtigte Änderungen aus Kernel-Dateien in Ihre eigene Erweiterung oder Ihr eigenes Design
   (Overrides), wo sie nicht als geänderte Kernel-Dateien zählen.
3. Führen Sie eine kurze Liste akzeptierter Abweichungen, bis diese verlagert sind, und vergleichen Sie damit.

## Was sie nicht erkennt

- Ihre eigenen Erweiterungen und Designs ohne Manifest.
- Dateien, die das Manifest nicht aufführt (eine neue Datei, die in einem Verzeichnis abgelegt wurde); kombinieren Sie
  die Prüfung mit der Dateiüberwachung Ihres Servers oder einer Prüfung von `git status`, wenn Sie aus der
  Versionskontrolle bereitstellen ([Konfiguration unter Versionskontrolle](../hosting/konfiguration-unter-versionskontrolle.md)).
- Änderungen in der Datenbank (Inhalte, dort gespeicherte Einstellungen); diese deckt das Prüfprotokoll ab.
- Hochgeladene Dateien in `var/storage`.

## Verwandte Seiten
- [Upgrade-Prüfung vorher und nachher](../aktualisierungen-und-aenderungen/upgrade-pruefung-vorher-und-nachher.md)
- [Verunstaltung der Website](../vorfaelle/verunstaltung-der-website.md)
- [Die Konsistenz nach einer Aktualisierung prüfen](../../../../../eng-us/help/government/tasks/operations/check-upgrade-consistency.md)
- [Kompromittierte Website (Hosting-Runbook)](../../../hosting/runbooks/kompromittierte-website.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
