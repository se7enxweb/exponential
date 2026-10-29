---
title: Ein HTTPS-Zertifikat in Velocity erneuern
audience: government, admin, hosting
category: public-sector
order: 240
summary: Schritt für Schritt - sehen, welches Zertifikat Exponential Velocity ausliefert und wann es abläuft, es erneuern (automatisch für Let's Encrypt, per Befehl oder durch Ersetzen Ihrer eigenen Dateien) und prüfen, dass der laufende Server es ohne Neustart übernommen hat.
keywords: Velocity, HTTPS, Zertifikat, erneuern, ssl show, ssl renew, qbixconsole ssl:show, Let's Encrypt, ACME, HTTPSSettings, Certificate, Key, Ablauf, certificate, renew, expiry
type: help
---
# Ein HTTPS-Zertifikat in Velocity erneuern

Diese Aufgabe ist für Administratoren von Sites, die von Exponential Velocity ausgeliefert werden. Velocity prüft ein
Zertifikat vor der Verwendung, beobachtet die Dateien, erneuert ACME-Zertifikate im Hintergrund und tauscht ein
erneuertes Zertifikat ohne Neustart ein
([HTTPS und Zertifikate in Velocity](../../../../funktionen/6.0/velocity-https-zertifikate.md)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Sehen, was ausgeliefert wird](#sehen-was-ausgeliefert-wird)
3. [Erneuern](#erneuern)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang als root (oder als der Benutzer, unter dem Velocity läuft).
- Kennen Sie die Quelle des Zertifikats: Ihre eigenen Dateien (`velocity.ini [HTTPSSettings] Certificate` und `Key`)
  oder einen Modus wie `letsencrypt` in der Velocity-Konfiguration der Site (`Q.web.https`).

## Sehen, was ausgeliefert wird

```bash
./console exp:velocity ssl show --allow-root-user
qbixconsole ssl:show
```

Achten Sie auf die verbleibenden Tage: Unter 14 bei einem Zertifikat einer CA bedeutet, dass die Erneuerung seit zwei
Wochen fehlschlägt; `acme.lastError` sagt, warum. Von außen:

```bash
openssl s_client -connect www.example.gov:443 -servername www.example.gov </dev/null 2>/dev/null | openssl x509 -noout -dates
```

## Erneuern

| Quelle | Tun |
|---|---|
| Let's Encrypt oder eine andere ACME-CA | normalerweise nichts: Die Erneuerung läuft in einem Hintergrundauftrag, wenn ein Drittel der Laufzeit übrig ist (`renewAt` 0.33), mit wachsenden Pausen nach Fehlschlägen. Um sie zu erzwingen: `./console exp:velocity ssl renew --apply www.example.gov --allow-root-user` |
| Ihre eigenen Dateien (vom Zertifikatsdienst Ihrer Organisation) | legen Sie die neuen `fullchain.pem` und `privkey.pem` unter die konfigurierten Pfade; Velocity prüft die Dateien alle `watchInterval` (60 Sekunden) und präsentiert der nächsten Verbindung ein gültiges neues Paar |

Erneuerung schlägt fehl mit „Invalid response from .../.well-known/acme-challenge/...“: Port 80 erreicht diesen Server
nicht; öffnen Sie ihn, leiten Sie diesen Pfad per Proxy an Velocity weiter oder verwenden Sie die Option `webroot`.

## Prüfen, ob es funktioniert hat

- `ssl show` listet das neue Ablaufdatum.
- Von außen zeigt `openssl` das neue `notAfter`.
- Es war kein Neustart nötig; wenn nach einigen Minuten noch das alte Zertifikat ausgeliefert wird, lesen Sie das
  Fehler-Log von Velocity (`./console exp:velocity status --allow-root-user` nennt es).

Sichern Sie das Verzeichnis `ssl/` von Velocity: Die ACME-Kontoschlüssel darin lassen sich nicht neu erzeugen.

## Verwandte Seiten
- [Velocity auf Let's Encrypt umstellen](velocity-auf-lets-encrypt-umstellen.md)
- [Ablauf von Zertifikaten](../../betrieb/vorfaelle/ablauf-von-zertifikaten.md)
- [HTTPS-Zertifikate in Velocity (Hosting)](../../../hosting/server/https-zertifikate-in-velocity.md)
- [Bereitstellung, Abschnitt 6](../../../../anleitungen/bereitstellung.md#6-https)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
