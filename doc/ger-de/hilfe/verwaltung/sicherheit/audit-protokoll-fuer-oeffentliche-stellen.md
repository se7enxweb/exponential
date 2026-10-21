---
title: Das Audit-Protokoll für öffentliche Stellen
audience: government, admin, project-manager
category: public-sector
order: 70
summary: Für öffentliche Stellen, die zeigen müssen, wer was in ihrer Website getan hat - was das Audit-Protokoll von Exponential standardmäßig aufzeichnet (und was nicht, etwa das Veröffentlichen), wo es aufbewahrt wird, wer es lesen darf, wie lange es aufbewahrt wird und die ersten Schritte in einer neuen Installation.
keywords: Audit-Protokoll, Nachvollziehbarkeit, Rechenschaft, wer hat was getan, Anmeldungen, fehlgeschlagene Anmeldungen, Rollenänderungen, Einstellungsänderungen, Entfernen von Inhalten, Veröffentlichen, Hash-Kette, Aufbewahrung, öffentlicher Sektor, audit trail, audit log, accountability, who did what, sign-ins, failed logins, role changes, setting changes, content removal, publishing, hash chain, retention, audit/read, audit/manage, exp:audit, public sector
type: help
---
# Das Audit-Protokoll für öffentliche Stellen

Diese Seite richtet sich an Administratorinnen und Administratoren, Informationssicherheitsbeauftragte und die
Verantwortlichen für das Schriftgut öffentlicher Stellen. Von öffentlichen Stellen wird allgemein erwartet, dass
Änderungen an amtlichen Informationen nachvollziehbar sind. Das Audit-Protokoll von Exponential zeichnet in
manipulationssicheren Dateien auf, was in einer Installation geschieht, und ist **in jeder Installation standardmäßig
eingeschaltet** ([Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md), [Audit-Leitfaden](../../../bc/6.0/audit.md)).

## Inhalt

1. [Was aufgezeichnet wird](#was-aufgezeichnet-wird)
2. [Was standardmäßig nicht aufgezeichnet wird](#was-standardmäßig-nicht-aufgezeichnet-wird)
3. [Wo und wie lange](#wo-und-wie-lange)
4. [Wer es lesen darf](#wer-es-lesen-darf)
5. [Erste Schritte in einer neuen Installation](#erste-schritte-in-einer-neuen-installation)
6. [Verwandte Seiten](#verwandte-seiten)

## Was aufgezeichnet wird

| Frage | Aufgezeichnet als |
|---|---|
| Wer sich angemeldet hat, wem es misslang, aus welchem Netz | `access.session.login`, `access.session.login.failed` (mit dem Grund), `access.session.logout`; Adressen standardmäßig auf /24 gekürzt |
| Wer Inhalte entfernt, verschoben, verborgen oder wiederhergestellt hat | `content.node.*`, `content.object.remove`, `content.object.restore`; das Entfernen eines Teilbaums ist ein übergeordnetes Ereignis mit einem untergeordneten je Knoten |
| Wer Rollen, Richtlinien, Benutzer, Sektionen, Zustände, Klassen geändert hat | `access.role.*`, `access.policy.*`, `access.user.*`, `content.section.*`, `content.state.*`, `content.class.*`, mit den Werten vorher und nachher |
| Wer eine Einstellung geändert, einen Cache geleert, einen Befehl oder Cronjob ausgeführt hat | `system.setting.write` (mit der Differenz, Geheimnisse maskiert), `system.cache.clear`, `system.command.run`, `system.cronjob.run` |
| Wer Preise geändert oder eine Bestellung entfernt hat | `commerce.*` |
| Ob jemand das Protokoll manipuliert hat | `exp:audit verify` und die Konsole: je Kanal **intakt** oder **unterbrochen** |

148 Ereignisnamen sind in einer Taxonomie (`domain.subject.action`) festgelegt und lassen sich nach Muster ein- oder
ausschalten.

## Was standardmäßig nicht aufgezeichnet wird

Die mitgelieferte Liste `settings/audit.ini [AuditEventSettings] Enabled[]` lässt einige Inhaltsereignisse aus. Der
Audit-Leitfaden sagt: „‚Wer hat das veröffentlicht?‘ ist die am häufigsten gestellte Frage; die mitgelieferte
Voreinstellung lässt sie aus.“ Um das Anlegen und Veröffentlichen aufzuzeichnen, ergänzen Sie
`settings/override/audit.ini.append.php`:

```ini
[AuditEventSettings]
Enabled[]=content.object.create
Enabled[]=content.object.publish
```

Auch der Kanal `read` (stichprobenartig erfasste Lesezugriffe) ist standardmäßig aus. Inhaltswerte, Passwörter, Tokens
und Geheimnisse werden nie aufgezeichnet. Für die Veröffentlichungsgeschichte eines einzelnen Objekts zeigt auch die
Versionsseite Ersteller und Daten jeder Version ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)).

## Wo und wie lange

- Eine JSON-Zeile je Eintrag in `var/<site>/log/audit/<channel>-<YYYY-MM-DD>.jsonl`; fünf Kanäle: `content`,
  `access`, `system`, `commerce`, `read`.
- Jede Zeile trägt den Hash der vorherigen Zeile; täglich signierte Prüfpunkte; signierte Archivmanifeste.
- Aufbewahrung je Kanal: 90 Tage live und 730 Tage archiviert (read: 30 und 90), automatisch vom Cronjob-Teil entfernt
  ([4.3 Aufbewahrung](../../../bc/6.0/audit.md#43-aufbewahrung)). Verlangt ein Aktenplan längere Fristen, lässt sich
  `ArchiveDays` verlängern ([6.6 Compliance: lange Aufbewahrung](../../../bc/6.0/audit.md#66-compliance-lange-aufbewahrung)).

## Wer es lesen darf

| Richtlinie | Gewährt |
|---|---|
| `audit/read` | den Reiter Audit (`/audit/dashboard`), Konsole, Ereignisansicht, Diagramme, Alarme, Export; lässt sich mit `Channel` einschränken |
| `audit/manage` | die Ansichten für Archive und Einstellungen, „Jetzt prüfen“ |

In einer neuen Installation hat nur die Rolle Administrator diese Richtlinien. Ein Benutzer ohne die Richtlinie sieht
nichts vom Audit, und eine eingetippte URL wird abgewiesen und aufgezeichnet
([3.1 Wer was sehen darf](../../../bc/6.0/audit.md#31-wer-was-sehen-darf-die-richtlinien)).

## Erste Schritte in einer neuen Installation

1. `./console exp:audit status --allow-root-user`: Audit eingeschaltet, jeder Kanal intakt.
2. Stellen Sie sicher, dass `php runcronjobs.php -q frequent` alle paar Minuten als Benutzer der Website läuft; das
   Dashboard warnt, wenn der Cronjob-Teil eine Stunde lang nicht gelaufen ist.
3. Prüfen Sie, wer die Alarm-E-Mails erhält: `./console exp:audit alerts recipients --allow-root-user`.
4. Sichern Sie `settings/override/audit.ini.append.php` als Geheimnis: Die Datei enthält die Signatur- und
   Pseudonymschlüssel.
5. Entscheiden Sie, ob das Veröffentlichen aufgezeichnet werden soll (siehe oben) und wie lange Archive aufbewahrt
   werden.
6. Leiten Sie das Protokoll vom Rechner weg (siehe „Das Audit-Protokoll an ein SIEM weiterleiten“).

## Verwandte Seiten

- [Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md)
- [Audit: was in Exponential geschieht, aufgezeichnet](../../../bc/6.0/audit.md)
- [13.14 Das Audit-Protokoll](../../../installation/13-sicherheitshaertung.md#1314-das-audit-protokoll)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
