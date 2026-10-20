---
title: Integrität und Prüfung des Audit-Protokolls
audience: government, admin, developer
category: public-sector
order: 90
summary: Für öffentliche Stellen, die sich auf das Audit-Protokoll als Beweismittel stützen - wie die Hash-Ketten, signierten Prüfpunkte und signierten Archivmanifeste von Exponential Manipulationen sichtbar machen, wie Sie sie prüfen, was jeder Befund bedeutet und was zu tun ist, wenn eine Kette unterbrochen ist.
keywords: Audit-Integrität, Hash-Kette, manipulationssicher, Prüfung, Prüfpunkte, Signaturschlüssel, Manifeste, unterbrochene Kette, Beweismittel, Beweiskette, forensisch, öffentlicher Sektor, audit integrity, hash chain, tamper evident, verification, exp:audit verify, checkpoints, HMAC, signing key, manifests, broken chain, evidence, chain of custody, forensic, public sector
type: help
---
# Integrität und Prüfung des Audit-Protokolls

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Informationssicherheitsbeauftragte, die
Audit-Einträge möglicherweise als Beweismittel vorlegen müssen, zum Beispiel in einem Disziplinarverfahren, einer
Untersuchung oder einem Streit darüber, wer amtliche Informationen geändert hat. Sie erklärt, wie die Integrität des
Audit-Protokolls von Exponential geschützt und geprüft wird, auf Grundlage von
[5.3 Die Hash-Kette und was die Prüfung findet](../../../../eng-us/bc/6.0/audit.md#53-the-hash-chain-and-what-verification-finds)
und [4.4 Prüfen und wiederherstellen](../../../../eng-us/bc/6.0/audit.md#44-verify-and-restore).

## Inhalt

1. [Wie Manipulationen sichtbar werden](#wie-manipulationen-sichtbar-werden)
2. [Prüfen](#prüfen)
3. [Was ein Befund bedeutet](#was-ein-befund-bedeutet)
4. [Wenn eine Kette unterbrochen ist](#wenn-eine-kette-unterbrochen-ist)
5. [Schlüssel](#schlüssel)
6. [Verwandte Seiten](#verwandte-seiten)

## Wie Manipulationen sichtbar werden

- **Hash-Kette je Kanal**: Jeder Eintrag trägt den SHA-256-Hash des vorherigen Eintrags; die erste Datei beginnt mit
  einem Wert, der aus der Installations-ID und dem Kanal abgeleitet ist, sodass sich eine Kette nicht als die eines
  anderen ausgeben lässt.
- **Signierte Prüfpunkte**: Einmal täglich zeichnet `system.audit.checkpoint` die aktuelle Position und den Hash jedes
  Kanals auf, signiert mit HMAC-SHA-256, und gibt sie an die Ziele weiter. Eine nachträglich neu berechnete Kette passt
  nicht mehr zu einem zuvor signierten Prüfpunkt.
- **Signierte Manifeste** für jeden archivierten Tag, miteinander verkettet, sodass auch das Entfernen eines ganzen
  archivierten Tages auffällt.

Ein Angreifer mit vollständiger Kontrolle über den Server könnte dennoch alles löschen; deshalb ist es wichtig,
Einträge und Prüfpunkte vom Rechner weg zu übertragen (siehe „Das Audit-Protokoll an ein SIEM weiterleiten“).

## Prüfen

```bash
./console exp:audit verify --allow-root-user                       # live files, every channel
./console exp:audit verify --archives --allow-root-user            # archives too
./console exp:audit verify --channel=access --date=<YYYY-MM-DD> --allow-root-user
./console exp:audit verify -q --allow-root-user || <your alert command>
```

Exit-Code 0 bedeutet intakt, 1 unterbrochen, 2 Fehler, sodass der Befehl in die Überwachung passt. Der Cronjob-Teil
prüft jeden Kanal täglich; **Jetzt prüfen** im Audit-Dashboard tut es auf Anforderung (`audit/manage`; mit
`ReauthForManage=enabled` wird zuerst das Passwort des Benutzers abgefragt). Für lange Aufbewahrungsfristen empfiehlt
der Leitfaden, `verify --archives` monatlich auszuführen und die Ausgabe aufzubewahren.

## Was ein Befund bedeutet

| Art | Bedeutet |
|---|---|
| `altered` | ein Eintrag wurde geändert |
| `link`, `gap`, `reordered` | Zeilen wurden entfernt, eingefügt oder vertauscht |
| `missing_file`, `truncated` | eine Datei fehlt oder Zeilen wurden abgeschnitten |
| `rewritten` | ein signierter Prüfpunkt hat einen anderen Hash festgehalten: Die Kette wurde neu berechnet |
| `checkpoint_invalid`, `unknown_key` | ein Prüfpunkt lässt sich nicht verifizieren, oder sein Schlüssel steht nicht in den Einstellungen |
| `archive_sha256`, `hmac_invalid`, `previous_manifest` | ein Archiv wurde geändert, ein Manifest bearbeitet, ein archivierter Tag entfernt |
| `repaired` (keine Unterbrechung) | eine abgerissene letzte Zeile, gefolgt von einer aufgezeichneten Reparatur |

## Wenn eine Kette unterbrochen ist

Die Anweisung des Audit-Leitfadens lautet: **Bearbeiten oder entfernen Sie nichts.**

1. Notieren Sie Datei, Zeile und Art der Änderung aus der Ausgabe der Prüfung.
2. Kopieren Sie das Audit-Verzeichnis auf einen sicheren, schreibgeschützten Speicher.
3. Vergleichen Sie mit dem, was den Server verlassen hat: Kopien in Syslog oder Journal, der Webhook-Empfänger, die von
   den Zielen versandten Prüfpunkte, die Archive.
4. Behandeln Sie es als Sicherheitsvorfall (siehe „Reaktion auf Sicherheitsvorfälle“).
5. Eine unterbrochene Datei bleibt eine Aufzeichnung: Die Konsole zeigt sie weiterhin an und kennzeichnet sie.

## Schlüssel

Die Signatur- und Pseudonymschlüssel stehen in `settings/override/audit.ini.append.php` (Modus 0640, nie eingecheckt)
und werden bei der ersten Verwendung erzeugt ([4.6 Schlüssel](../../../../eng-us/bc/6.0/audit.md#46-keys)):

- Sichern Sie die Datei als Geheimnis; ohne die Signaturschlüssel lassen sich alte Archive nicht prüfen
  (`unknown_key`);
- wechseln Sie den Signaturschlüssel einmal im Jahr, und sofort, wenn er gelesen worden sein könnte:
  `./console exp:audit key rotate --allow-root-user`; alte Schlüssel bleiben für ihre Archive erhalten;
- Schlüssel können mit `[AuditKeySettings] GenerateKeys=disabled` aus einem Secrets-Speicher kommen.

## Verwandte Seiten

- [Audit-Leitfaden, 5.3](../../../../eng-us/bc/6.0/audit.md#53-the-hash-chain-and-what-verification-finds)
- [Das Audit-Protokoll für öffentliche Stellen](audit-protokoll-fuer-oeffentliche-stellen.md)
- [Audit-Protokoll](../../../../eng-us/features/6.0/audit-trail.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
