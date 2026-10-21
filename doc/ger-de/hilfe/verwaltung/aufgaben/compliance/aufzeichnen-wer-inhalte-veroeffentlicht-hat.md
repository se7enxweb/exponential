---
title: Aufzeichnen, wer Inhalte veröffentlicht hat
audience: government, admin, project-manager
category: public-sector
order: 200
summary: Schritt für Schritt - die Audit-Ereignisse content.object.create und content.object.publish einschalten, die Exponential standardmäßig ausgeschaltet lässt, damit das Audit-Protokoll beantwortet „Wer hat das wann veröffentlicht?“, und prüfen, dass das Veröffentlichen aufgezeichnet wird.
keywords: Audit, Veröffentlichen, content.object.publish, content.object.create, AuditEventSettings, Enabled, Rechenschaft, wer hat veröffentlicht, Schriftgut, Beweismittel, öffentlicher Sektor, publishing, accountability, who published, records, evidence, public sector
type: help
---
# Aufzeichnen, wer Inhalte veröffentlicht hat

Für Administratoren und Projektleiter. „Wer hat das veröffentlicht?“ ist laut Anleitung zum Audit die am häufigsten
gestellte Frage, und die mitgelieferte Voreinstellung lässt sie ausgeschaltet:
`settings/audit.ini [AuditEventSettings] Enabled[]` zeichnet Verschieben, Verstecken, Entfernen, Zustände und Sektionen
auf, aber nicht das Anlegen und Veröffentlichen
([Anleitung zum Audit, 6.2 Stark besuchte Site](../../../../bc/6.0/audit.md#62-stark-ausgelastete-website)). Behörden möchten es meist
eingeschaltet.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Mehr Einträge: einer je Veröffentlichung. Auf einer stark besuchten Site empfiehlt die Anleitung zusätzlich größere
  Audit-Puffer.
- Die Versionsseite zeigt bereits Ersteller und Daten jeder Version; das Audit fügt einen manipulationssicheren,
  durchsuchbaren Eintrag über alle Inhalte hinzu.

## Schritte

1. In `settings/override/audit.ini.append.php` (keine leere Zeile `Enabled[]` davor, sonst wird die mitgelieferte
   Liste geleert):

   ```ini
   [AuditEventSettings]
   Enabled[]=content.object.create
   Enabled[]=content.object.publish
   ```

2. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`; laden Sie PHP-FPM neu und
   starten Sie Velocity neu (`./console exp:velocity deploy --allow-root-user`).

## Prüfen, ob es funktioniert hat

1. Veröffentlichen Sie einen Testartikel in einem Testordner.
2. `./console exp:audit tail --channel=content --name='content.object.*' --lines=5 --allow-root-user` zeigt die
   Veröffentlichung mit dem Benutzer.
3. Auf **Setup > Audit** (`/audit/settings`) zeigt die Liste der Ereignisse die neuen Einträge und die Datei, aus der sie
   stammen.

Das Schreiben der Einstellung selbst wird als `system.setting.write` aufgezeichnet, wenn es mit `exp:ini` geschieht.

## Verwandte Seiten

- [Das Audit-Protokoll für Behörden](../../sicherheit/audit-protokoll-fuer-oeffentliche-stellen.md)
- [Versionen und Inhaltsverlauf](../../schriftgut-und-archivierung/versionen-und-inhaltsgeschichte.md)
- [Anleitung zum Audit, 8 Referenz der Ereignisse](../../../../bc/6.0/audit.md#8-referenz-der-ereignisse)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
