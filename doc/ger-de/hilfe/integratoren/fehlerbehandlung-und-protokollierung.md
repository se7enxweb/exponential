---
title: Fehlerbehandlung und Protokollierung in Integrationen
audience: integrator, developer
category: integration
order: 330
summary: Wie eine Integration in Exponential scheitern, wiederholen und eine Spur hinterlassen sollte - Fehlerarten, Wiederholungen und Dead Letters, Transaktionen, eZDebug und eZLog, die Protokolldateien unter var/log, Fehlerreferenzen, die Protokollansicht und exp:logs, das Audit-Protokoll und was nie in ein Protokoll gehört.
keywords: Fehlerbehandlung, Wiederholung, Backoff, Dead Letter, Transaktion, eZDebug, writeError, writeWarning, eZLog, Protokolldatei, var/log, error.log, Fehlerreferenz, ERR-, Protokollansicht, exp:logs, Audit, Maskierung, personenbezogene Daten in Protokollen, error handling, retry, transaction, log file, error reference, log viewer, masking
type: help
---
# Fehlerbehandlung und Protokollierung in Integrationen

Diese Seite ist für Integrationsentwickler. Eine Integration scheitert früher oder später: Das andere System ist
nicht erreichbar, ein Datensatz ist ungültig, ein Zugangsdatum läuft ab. Ob das ein Nichtereignis oder ein Vorfall
ist, hängt davon ab, wie der Code scheitert, wiederholt und berichtet. Die Protokollierungswerkzeuge des Kernels stehen
unter [eZDebug und Protokollierung](../erweiterungsentwicklung/teil-8-referenz/ezdebug-und-protokollierung.md).

## Inhalt

1. [Drei Arten von Fehlern](#drei-arten-von-fehlern)
2. [Wiederholungen und der Dead Letter](#wiederholungen-und-der-dead-letter)
3. [Transaktionen](#transaktionen)
4. [In die Protokolle schreiben](#in-die-protokolle-schreiben)
5. [Die Protokolle lesen](#die-protokolle-lesen)
6. [Was nie in ein Protokoll gehört](#was-nie-in-ein-protokoll-gehört)

## Drei Arten von Fehlern

| Art | Beispiele | Behandlung |
|---|---|---|
| **Vorübergehend** | Timeout, `429`, `502`, `503`, ein Warten auf eine Sperre | Mit wachsenden Abständen wiederholen; erst alarmieren, wenn die Wiederholungen erschöpft sind |
| **Dauerhaft für einen Datensatz** | Ungültiger Wert, unbekannte Klasse, eine bereits belegte Remote-ID, `400`, `422` | Den Datensatz überspringen, ihn mit Schlüssel und Grund protokollieren, weitermachen; die Liste nach dem Lauf melden |
| **Fatal für den Lauf** | Zugangsdatum abgelehnt (`401`), Quelldatei fehlt oder ist abgeschnitten, Datenbank nicht verfügbar | Den Lauf sofort mit einem Exit-Code ungleich null beenden; alarmieren |

Ein Lauf, der Datensätze überspringt, muss das trotzdem sagen: Die Zusammenfassungszeile zählt sie, und der Exit-Code
oder ein Statuskennzeichen teilt der Überwachung mit, dass der Lauf nicht sauber war.

## Wiederholungen und der Dead Letter

- Wiederholen Sie vorübergehende Fehler mit exponentiellem Backoff und einer Obergrenze (zum Beispiel 2, 4, 8 ...
  Minuten, höchstens eine Stunde), und beachten Sie `Retry-After` des anderen Systems.
- Verschieben Sie das Element nach einer festen Zahl von Versuchen in einen Zustand **Dead Letter** (ein Status in
  Ihrer Outbox-Tabelle oder die Dead-Letter-Ablage von `expqueue`) und alarmieren Sie. Ein Element, das ewig
  wiederholt wird, verdeckt ein echtes Problem.
- Machen Sie Wiederholungen sicher: Der Empfänger muss eine wiederholte Zustellung an einem Idempotenzschlüssel
  erkennen ([Einen Export schreiben](einen-export-oder-ausgehenden-abgleich-schreiben.md#der-outbox-sender)).

## Transaktionen

`eZContentFunctions::createAndPublishObject()` verwaltet seine eigene Transaktion. Wenn Ihr Code mehrere
zusammengehörige Zeilen Ihrer eigenen Tabellen schreibt, klammern Sie sie ein:

```php
<?php
$db = eZDB::instance();
$db->begin();
$db->query( 'UPDATE myintegration_outbox SET sent = ' . time() . ' WHERE id = ' . (int)$rowID );
$db->query( 'INSERT INTO myintegration_delivery ( outbox_id, answered ) VALUES ( ' . (int)$rowID . ', ' . time() . ' )' );
$db->commit();
```

Halten Sie eine Transaktion nie über einen Aufruf an ein anderes System hinweg offen: Eine langsame Antwort hält
Datenbanksperren für jeden Redakteur.

## In die Protokolle schreiben

| Aufruf | Landet in (unter `var/log/` oder im Protokollverzeichnis der Website) | Verwenden für |
|---|---|---|
| `eZDebug::writeError( $message, __METHOD__ )` | `error.log` | Fehler, die sich jemand ansehen muss |
| `eZDebug::writeWarning( $message, __METHOD__ )` | `warning.log` (wenn diese Stufe protokolliert wird) | Übersprungene Datensätze, Wiederholungen |
| `eZDebug::writeNotice( $message, __METHOD__ )` | `notice.log` (wenn protokolliert) | Einzelheiten zur Fehlersuche |
| `eZLog::write( $message, 'myintegration.log' )` | `var/log/myintegration.log` | Die eigene Spur der Integration: eine Zusammenfassungszeile pro Lauf, eine Zeile pro übersprungenem Datensatz |

Welche Stufen die Protokolldateien erreichen, legt `site.ini [DebugSettings] AlwaysLog[]` fest (standardmäßig
`error`). Verwenden Sie für die Spur der Integration eine eigene `eZLog`-Datei, damit Betreiber sie finden, ohne die
Kernel-Protokolle zu durchsuchen.

Eine gute Zusammenfassungsmeldung (`eZLog` stellt das Datum voran):

```text
import erp-products: rows=20412 created=12 updated=388 unchanged=19990 hidden=3 skipped=19 failed=0 seconds=274
```

## Die Protokolle lesen

- **Setup > Logdateien** (die Protokollansicht) listet, verfolgt und durchsucht die Protokolldateien und ihre
  Rotationen, mit Maskierung personenbezogener Daten ([Die Protokollansicht](../../funktionen/6.0/log-betrachter.md)).
- **Fehlerreferenzen**: Eine Fehlerseite zeigt eine Referenz wie `ERR-0123456789`, die auch in `error.log`
  geschrieben wird; die Protokollansicht und `php bin/php/console exp:logs --reference=ERR-...` zeigen jede Zeile
  dieser Anfrage ([Einen Fehler über seine Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md)).
- Aus einer Shell:

  ```bash
  php bin/php/console exp:logs --list
  php bin/php/console exp:logs --tail=var/log/myintegration.log --lines=50
  php bin/php/console exp:logs --search=erp-product --file=var/log/error.log --rotations
  ```

- **Das Audit-Protokoll** beantwortet „wer hat was geändert": Schreibzugriffe über `expservices`, Nutzung und
  Ablehnung von API-Schlüsseln, Entfernen von Inhalten ([Audit](../../bc/6.0/audit.md)). Eine Integration, die für
  Benutzer handelt, kann über die Audit-API eigene Ereignisse festhalten.
- Protokolldateien rotiert der Cronjob-Teil `logs` (`explogrotate.php`) ([Protokollrotation](../../funktionen/6.0/log-rotation.md)).

## Was nie in ein Protokoll gehört

- Passwörter, API-Schlüssel, Token, Signaturgeheimnisse, Sitzungs-IDs.
- Vollständige Nutzdaten mit personenbezogenen Daten; protokollieren Sie stattdessen Schlüssel und Anzahlen.
- Kartendaten jeder Art.

Die Protokollansicht maskiert E-Mail- und IP-Adressen und Token, aber Maskierung ist ein Sicherheitsnetz, keine
Erlaubnis, sie zu schreiben.

## Verwandte Seiten

- [eZDebug und Protokollierung](../erweiterungsentwicklung/teil-8-referenz/ezdebug-und-protokollierung.md)
- [Fehlersuche und Protokolle für App-Ersteller](../ersteller/anwendungsentwickler/fehlersuche-und-logs.md)
- [Integrationen überwachen](integrationen-ueberwachen.md)
- [Protokollansicht und Rotation für Hoster](../hosting/betrieb/log-viewer-und-log-rotation.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
