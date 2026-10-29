---
title: Langfristige Aufbewahrung von Audit-Einträgen
audience: government, admin, hosting
category: public-sector
order: 170
summary: Für öffentliche Stellen, deren Schriftgutregeln die Aufbewahrung von Audit-Einträgen über Jahre verlangen - die Konfiguration für lange Aufbewahrung aus dem Audit-Leitfaden (Archive pro Kanal, Komprimierung, einmal beschreibbarer Speicher, signierte Prüfpunkte außerhalb des Servers), die Schlüsselverwaltung über die Jahre und die monatliche Prüfroutine.
keywords: langfristige Aufbewahrung, Audit-Archive, ArchiveDays, ArchiveFormat, xz, WORM, einmal beschreibbarer Speicher, ArchiveDir, Signaturschlüssel, Prüfpunkte, Webhook, Archive prüfen, Wiederherstellen, Compliance, Aufbewahrungsplan, öffentlicher Sektor, long-term retention, audit archives, write-once storage, signing keys, checkpoints, verify archives, restore, records schedule, public sector
type: help
---
# Langfristige Aufbewahrung von Audit-Einträgen

Diese Seite richtet sich an Administratoren und Hosting-Teams öffentlicher Stellen, deren Aufbewahrungsplan
Audit-Einträge länger als die standardmäßigen zwei Jahre verlangt. Die Referenzkonfiguration „Compliance: long
retention“ des Audit-Leitfadens bewahrt zehn Jahre Nachweise komprimiert auf einmal beschreibbarem Speicher auf, mit
täglichen signierten Prüfpunkten, die vom Server weggeschickt werden
([6.6 Compliance: lange Aufbewahrung](../../../bc/6.0/audit.md#66-compliance-lange-aufbewahrung)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Konfiguration](#die-konfiguration)
2. [Speicher](#speicher)
3. [Schlüssel über die Jahre](#schlüssel-über-die-jahre)
4. [Routine](#routine)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Konfiguration

In `settings/override/audit.ini.append.php` (Auszug aus der Referenzkonfiguration des Leitfadens; passen Sie den
Zeitraum an):

```ini
[AuditChannel_content]
ArchiveDays=3650
ArchiveFormat=xz

[AuditChannel_access]
ArchiveDays=3650
ArchiveFormat=xz

[AuditChannel_system]
ArchiveDays=3650
ArchiveFormat=xz
Sinks[]=webhook

[AuditArchiveSettings]
ArchiveDir=/srv/worm/exponential-audit

[AuditIndexSettings]
KeepDays=730
```

Die Fassung des Leitfadens schaltet außerdem `content.object.create`, `content.object.publish` und einige Ereignisse zu
Handel und Daten ein, sowie eine Webhook-Senke, die die Prüfpunkte, Bereinigungen und Schlüsselwechsel empfängt.

## Speicher

- `ArchiveDir` auf einem Mount, dem der Webserver Dateien hinzufügen, an dem er sie aber nicht ändern kann (einmal
  beschreibbarer Speicher oder ein Sicherungsziel).
- Archive werden pro Tag mit einem signierten Manifest komprimiert; die Manifeste sind verkettet, sodass ein entfernter
  Tag auffällt.
- Der Index hält zwei Jahre durchsuchbar; frühere Jahre werden nach `exp:audit restore` gelesen.
- Archivformate: gzip (immer verfügbar), bzip2, xz, zstd, zip, je nach PHP-Erweiterungen und Programmen; das Manifest
  nennt den verwendeten Handler.

## Schlüssel über die Jahre

- Jeder `SigningKey[]` muss so lange aufbewahrt werden wie die Archive, die er signiert hat; ein Schlüsselwechsel fügt
  Schlüssel hinzu und entfernt nie welche.
- Bewahren Sie die Sicherung von `settings/override/audit.ini.append.php` mit derselben Frist auf wie die Archive.
- Wechseln Sie den Signaturschlüssel jährlich (`./console exp:audit key rotate --apply --allow-root-user`).

## Routine

| Wann | Was |
|---|---|
| täglich (automatisch) | der Cronjob-Teil archiviert, bereinigt, signiert Prüfpunkte; der Webhook schickt sie vom Server weg |
| monatlich | `./console exp:audit verify --archives --allow-root-user`, die Ausgabe aufbewahren |
| jährlich | den Signaturschlüssel wechseln; die Wiederherstellung eines alten Tages testen; den freien Platz auf dem Archiv-Mount prüfen |
| bei einer Migration | Archive und Schlüssel gemeinsam kopieren; auf dem neuen Server prüfen |

## Verwandte Seiten

- [Audit-Leitfaden, 4.2 Rotation und Archive](../../../bc/6.0/audit.md#42-rotation-und-archive)
- [Audit-Einträge als Nachweis](audit-eintraege-als-nachweis.md)
- [Einordnung nach OAIS](einordnung-nach-oais.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
