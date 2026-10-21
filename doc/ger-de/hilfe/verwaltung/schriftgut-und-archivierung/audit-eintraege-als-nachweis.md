---
title: Audit-Einträge als Nachweis
audience: government, admin, project-manager
category: public-sector
order: 160
summary: Für öffentliche Stellen, die beweisen müssen, wer auf ihrer Website was getan hat - wie Sie Audit-Einträge von Exponential auswählen, prüfen, exportieren und übergeben (signierte Bündel, JSON-Zeilen mit Hashes), die Beweiskette dokumentieren und wo die Grenzen dessen liegen, was das Audit zeigt.
keywords: Audit-Einträge, Nachweis, Beweis, Beweiskette, Chain of Custody, signiertes Bündel, exp:audit export, verify, Hash, sha256, Untersuchung, Disziplinarverfahren, Streitfall, Schriftgut, Anfrage-ID, öffentlicher Sektor, audit records, evidence, chain of custody, signed bundle, investigation, disciplinary, dispute, records, request id, public sector
type: help
---
# Audit-Einträge als Nachweis

Diese Seite richtet sich an Administratoren, Zuständige für Schriftgut und Ermittelnde. Wenn ein Beschluss, eine
Veröffentlichung oder eine Kontoänderung strittig ist, kann das Prüfprotokoll zeigen, wer wann was getan hat. Sein Wert
als Nachweis hängt davon ab, wie die Einträge ausgewählt, geprüft und übergeben werden ([Audit](../../../../eng-us/bc/6.0/audit.md)).

![Das Audit-Dashboard: der Zustand der Hash-Ketten, die Ereignisse und die Sicherheitsereignisse](../../../../eng-us/help/images/government/gov-audit-dash.webp)

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Einträge auswählen](#die-einträge-auswählen)
3. [Prüfen](#prüfen)
4. [Exportieren](#exportieren)
5. [Beweiskette](#beweiskette)
6. [Was das Audit nicht zeigt](#was-das-audit-nicht-zeigt)
7. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Prüfen Sie, ob der Zeitraum noch innerhalb der Aufbewahrung liegt (standardmäßig 90 Tage aktiv, 730 archiviert);
  stellen Sie archivierte Tage zuerst wieder her:
  `./console exp:audit restore --channel=<channel> --date=<YYYY-MM-DD> --allow-root-user`.
- Beziehen Sie Ihre Rechtsberatung ein und bei Personalangelegenheiten die Personen, die Ihre Verfahren benennen.

## Die Einträge auswählen

Verwenden Sie die Filter der Konsole (als Lesezeichen speicherbar) oder den Befehl:

| Frage | Filter |
|---|---|
| alles, was eine Person getan hat | `/audit/console/(user)/<id>` oder `--user=<id>` |
| alles zu einer Seite | `/audit/console/(object)/node:<id>` oder `--object=node:<id>` |
| ein einzelner Klick | `/audit/console/(request)/<request id>` (jede Antwort der Administration trägt `X-Exp-Request-Id`) |
| ein Zeitfenster | `(from)/<time>/(to)/<time>`; auf der Kommandozeile sind Zeiten ohne Versatz UTC |

## Prüfen

```bash
./console exp:audit verify --allow-root-user
./console exp:audit verify --archives --allow-root-user
```

Bewahren Sie die Ausgabe zusammen mit dem Nachweis auf. Eine **gebrochene** Kette ist selbst ein Befund: Bearbeiten Sie
nichts und vergleichen Sie mit den Kopien, die den Server verlassen haben
([Audit-Leitfaden 4.4](../../../../eng-us/bc/6.0/audit.md#44-verify-and-restore)).

## Exportieren

```bash
./console exp:audit export --user=<id> --from=<YYYY-MM-DD> --to=<YYYY-MM-DD> --format=bundle --out=var/tmp/<case>/audit-bundle --allow-root-user
./console exp:audit export --object=node:<id> --format=jsonl --out=var/tmp/<case>/node.jsonl --allow-root-user
```

- `jsonl` behält die Zeilen so, wie sie geschrieben wurden, sodass ihre Hashes erneut geprüft werden können.
- `bundle` ist ein signiertes Bündel.
- Der Export gibt den sha256 der Datei aus; jeder Export wird als `system.audit.export` mit diesem Hash aufgezeichnet.
- Die Seite **Export** des Reiters Audit bietet CSV, JSON-Zeilen und JSON für den aktuellen Filter.

## Beweiskette

Halten Sie fest: wer exportiert hat, wann, den Befehl oder Filter, den ausgegebenen sha256, wo die Datei abgelegt
wurde, und jede Übergabe. Bewahren Sie die Datei auf schreibgeschützten Medien oder in einem System auf, das Ihre
Rechtsberatung akzeptiert. Geben Sie der empfangenden Stelle die Ausgabe der Prüfung und bei Bedarf die öffentliche
Beschreibung der Hash-Kette ([5.3](../../../../eng-us/bc/6.0/audit.md#53-the-hash-chain-and-what-verification-finds)).

## Was das Audit nicht zeigt

- Standardmäßig ausgeschaltete Ereignisse (zum Beispiel `content.object.publish`, Lesezugriffe) sind nicht enthalten,
  sofern sie damals nicht eingeschaltet waren.
- Inhaltswerte werden nie aufgezeichnet: Was eine Seite sagte, zeigen ihre Versionen.
- Adressen werden standardmäßig gekürzt, Sitzungen gehasht.
- Handlungen außerhalb von Exponential (Anmeldungen am Server, Änderungen an der Datenbank von Hand) erfordern die
  eigenen Protokolle des Servers.

## Verwandte Seiten

- [Audit](../../../../eng-us/bc/6.0/audit.md)
- [Langfristige Aufbewahrung von Audit-Einträgen](langfristige-aufbewahrung-von-audit-eintraegen.md)
- [Versionen und Inhaltsgeschichte](versionen-und-inhaltsgeschichte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
