---
title: "Runbook: Ablauf von Zertifikaten"
audience: hosting, admin
category: hosting
order: 80
summary: Was zu tun ist, wenn Besucher HTTPS-Warnungen sehen oder ein Zertifikat kurz vor dem Ablauf steht - prüfen, was ausgeliefert wird, unter Velocity mit exp:velocity ssl show und renew, unter Apache oder nginx mit Ihrem Zertifikatswerkzeug oder Panel, und eine Wiederholung verhindern.
keywords: Runbook, Zertifikat, TLS, HTTPS, Ablauf, abgelaufen, erneuern, Let's Encrypt, ACME, selbstsigniert, Velocity, exp:velocity ssl show, ssl renew, HSTS, Apache, nginx, Panel, runbook, certificate, expiry, expired, renew, self-signed, panel
type: help
---
# Runbook: Ablauf von Zertifikaten

Für die Bereitschaft, wenn Besucher eine Zertifikatswarnung sehen oder die Überwachung ein Zertifikat kurz vor dem Ablauf meldet.

## Inhalt

1. [Sehen, was ausgeliefert wird](#sehen-was-ausgeliefert-wird)
2. [Unter Velocity](#unter-velocity)
3. [Unter Apache, nginx oder einem Panel](#unter-apache-nginx-oder-einem-panel)
4. [Nach dem Vorfall](#nach-dem-vorfall)

## Sehen, was ausgeliefert wird

Von außen, für die Domain, die der Kunde gemeldet hat:

```bash
openssl s_client -connect www.example.com:443 -servername www.example.com </dev/null 2>/dev/null \
  | openssl x509 -noout -subject -issuer -dates
```

| Sie sehen | Bedeutung |
|---|---|
| `notAfter` in der Vergangenheit | abgelaufen |
| einen Aussteller, der gleich dem Subjekt ist, oder eine Warnung zu einem selbstsignierten Zertifikat | unter Velocity: das konfigurierte Zertifikat fehlt, ist abgelaufen oder passt nicht zu seinem Schlüssel, und Velocity ist auf ein selbstsigniertes zurückgefallen |
| ein Zertifikat für einen anderen Namen | der falsche virtuelle Host oder die falsche Website antwortet; prüfen Sie die namensbasierte Konfiguration |

## Unter Velocity

Velocity prüft ein Zertifikat, bevor er es nutzt: Ein kaputtes oder abgelaufenes Zertifikat erreicht nie einen Besucher; das Paar wird vor
der Nutzung geprüft (lässt sich lesen, ist nicht abgelaufen, der Schlüssel gehört dazu), und ein selbstsigniertes Zertifikat springt ein,
bis das echte wieder nutzbar ist ([HTTPS und Zertifikate in Velocity](../../../funktionen/6.0/velocity-https-zertifikate.md)).

1. Zeigen Sie, was es ausliefert:

   ```bash
   php bin/php/console exp:velocity ssl show --allow-root-user
   ```

2. Erneuern Sie:

   ```bash
   php bin/php/console exp:velocity ssl renew www.example.com --allow-root-user
   ```

   Der laufende Server übernimmt eine Erneuerung innerhalb einer Minute, ohne Neustart (`exp:velocity --help`).
3. Mit Let's Encrypt braucht die Erneuerung normalerweise nichts: Zertifikate werden in einem Hintergrundjob erneuert, nach einem Fehler
   mit wachsenden Pausen (5 Minuten, verdoppelt bis zu einem Tag). Wenn es weiter scheitert, prüfen Sie, dass Port 80 des Servers aus dem
   Internet erreichbar ist und das DNS darauf zeigt.
4. Mit eigenen Dateien (`[HTTPSSettings] Certificate` und `Key`) legen Sie das neue Paar an seinen Platz und prüfen erneut mit `ssl show`.

## Unter Apache, nginx oder einem Panel

- Erneuern Sie mit dem Werkzeug, das das Zertifikat ausgestellt hat (certbot oder ein anderer ACME-Client oder die Zertifikatsfunktion des
  Panels), und laden Sie dann den Webserver neu.
- Prüfen Sie, dass der Job für die Erneuerung läuft: sein Timer oder Cron-Eintrag, sein Log.
- Siehe [TLS-Zertifikate in Panels](../control-panels/tls-zertifikate-in-panels.md) und [TLS und HTTPS](../sicherheit/tls-und-https.md).

> **Hinweis**
> HSTS: Velocity sendet standardmäßig `Strict-Transport-Security` mit `HSTSMaxAge=300`; ein Browser, der es gesehen hat, verweigert so
> lange reines HTTP zu diesem Hostnamen. Versuchen Sie nicht, ein abgelaufenes Zertifikat zu umgehen, indem Sie die Website auf reines
> HTTP umstellen; erneuern Sie es.

## Nach dem Vorfall

- [ ] Prüfen Sie erneut von außen mit `openssl s_client`.
- [ ] Fügen Sie eine Überwachung des Ablaufs von Zertifikaten hinzu oder korrigieren Sie sie, mit einer Warnung lange vor dem Ende
      ([Zustandsprüfungen und Verfügbarkeit](../betrieb/zustandspruefungen-und-verfuegbarkeit.md)).
- [ ] Halten Sie im Datensatz der Website fest, welche Quelle das Zertifikat ausstellt und wo es erneuert wird.

## Verwandte Seiten

- [HTTPS-Zertifikate in Velocity](../server/https-zertifikate-in-velocity.md)
- [Velocity startet nicht](velocity-startet-nicht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
