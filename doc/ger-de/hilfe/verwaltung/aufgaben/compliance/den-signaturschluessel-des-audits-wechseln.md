---
title: Den Signaturschlüssel des Audits wechseln
audience: government, admin
category: public-sector
order: 230
summary: Schritt für Schritt - den Schlüssel ersetzen, der die Checkpoints und Archivmanifeste des Audits von Exponential signiert, jährlich oder sofort nach einem vermuteten Leck, dabei alte Schlüssel für alte Archive behalten und das Ergebnis sichern.
keywords: Signaturschlüssel des Audits, Schlüsselwechsel, exp:audit key rotate, SigningKey, HMAC, Checkpoints, Manifeste, Fingerabdruck, Pseudonym-Schlüssel, Sicherung von settings override, öffentlicher Sektor, audit signing key, key rotation, checkpoints, manifests, fingerprint, pseudonym key, settings override backup, public sector
type: help
---
# Den Signaturschlüssel des Audits wechseln

Für Administratoren. Das Audit signiert seine täglichen Checkpoints und Archivmanifeste mit einem Schlüssel, der in
`settings/override/audit.ini.append.php` liegt. Wechseln Sie ihn einmal im Jahr (das Dashboard erinnert Sie nach 365
Tagen) und sofort, wenn er gelesen worden sein könnte
([Anleitung zum Audit, 4.6 Schlüssel](../../../../bc/6.0/audit.md#46-schlüssel)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang.
- Eine aktuelle Sicherung von `settings/override/audit.ini.append.php`, als Geheimnis aufbewahrt.

## Schritte

1. Notieren Sie den aktuellen Schlüssel: `./console exp:audit key list --allow-root-user` (nur IDs und Fingerabdrücke).
2. Wechseln Sie:

   ```bash
   ./console exp:audit key rotate --apply --allow-root-user
   ```

   Der neue Schlüssel wird aktiv; alte Schlüssel bleiben für die Archive, die sie signiert haben;
   `system.audit.key.rotate` wird aufgezeichnet. Der Befehl leert den INI-Cache.
3. Starten Sie Velocity neu, wenn er läuft, damit kein Worker weiter mit dem alten Schlüssel signiert:
   `./console exp:velocity restart --allow-root-user`.
4. Sichern Sie `settings/override/audit.ini.append.php` erneut.
5. In einem Cluster kopieren Sie die Schlüsseldatei identisch auf jeden Server.

## Prüfen, ob es funktioniert hat

- `./console exp:audit status --allow-root-user` zeigt die ID und den Fingerabdruck des neuen Signaturschlüssels.
- Nach dem nächsten Checkpoint (`./console exp:audit checkpoint --apply --allow-root-user`, um jetzt einen zu erstellen) meldet
  `./console exp:audit verify --allow-root-user` den Zustand als unversehrt.

Entfernen Sie alte Schlüssel nicht, solange damit signierte Archive bestehen; `exp:audit purge` nennt die Schlüssel, die
noch gebraucht werden. Der Pseudonym-Schlüssel ist ein anderer Schlüssel und wird nur absichtlich gewechselt
(`key rotate --pseudonym`).

## Verwandte Seiten

- [Integrität und Verifikation des Audits](../../sicherheit/audit-integritaet-und-pruefung.md)
- [Geheimnisse in settings/override](../../sicherheit/geheimnisse-in-settings-override.md)
- [Langfristige Aufbewahrung des Audits](../../schriftgut-und-archivierung/langfristige-aufbewahrung-von-audit-eintraegen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
