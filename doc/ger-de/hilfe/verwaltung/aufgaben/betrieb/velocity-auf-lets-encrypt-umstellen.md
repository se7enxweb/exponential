---
title: Velocity auf Let's Encrypt umstellen
audience: government, admin, hosting
category: public-sector
order: 250
summary: Schritt für Schritt - eine Site auf Exponential Velocity von einem manuell installierten Zertifikat auf automatische Zertifikate von Let's Encrypt umstellen, zuerst mit dem Staging-Verzeichnis, nachdem Sie die Zertifikatsrichtlinie Ihrer Organisation geprüft haben.
keywords: Let's Encrypt, ACME, Velocity, automatische Zertifikate, letsencrypt-staging, Q.web.https, mode letsencrypt, Domains, http-01, dns-01, Zertifikatsrichtlinie, automatic certificates, domains, certificate policy
type: help
---
# Velocity auf Let's Encrypt umstellen

Diese Aufgabe ist für Administratoren, die Zertifikate möchten, die sich selbst erneuern. Viele Behörden haben eine
Zertifikatsrichtlinie (eine zugelassene Zertifizierungsstelle, organisationsvalidierte Zertifikate für manche Domains);
prüfen Sie sie zuerst, denn Let's Encrypt stellt domainvalidierte Zertifikate aus. Referenz:
[HTTPS und Zertifikate in Velocity](../../../../../eng-us/features/6.0/velocity-https-certificates.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Das Staging-Verzeichnis ausprobieren](#das-staging-verzeichnis-ausprobieren)
3. [Auf die Produktion umstellen](#auf-die-produktion-umstellen)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Fallstricke](#fallstricke)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ihre Zertifikatsrichtlinie erlaubt für diese Domain eine ACME-CA.
- Das DNS verweist die Domain auf diesen Server, und Port 80 dieses Servers ist aus dem Internet erreichbar (für die
  voreingestellte Challenge `http-01`). Wildcard-Namen brauchen `dns-01` mit einem Hook-Programm.
- Ein Kontaktpostfach für Hinweise zum Ablauf (eine Team-Adresse).

## Das Staging-Verzeichnis ausprobieren

In der Velocity-Konfiguration der Site (zum Beispiel `/etc/vc/sites-enabled/<site>.conf`) unter `Q.web.https`:

```json
{ "Q": { "web": { "https": {
  "port": 443,
  "mode": "letsencrypt",
  "acme": { "email": "webteam@example.gov", "domains": ["example.gov", "www.example.gov"],
            "directory": "letsencrypt-staging" }
} } } }
```

Starten Sie Velocity neu (`./console exp:velocity restart --allow-root-user`) und prüfen Sie mit `qbixconsole ssl:show`.
Zertifikate aus dem Staging gelten Browsern nicht als vertrauenswürdig, aber seine Ratenbegrenzungen sind großzügig:
Verwenden Sie es, bis die Ausstellung funktioniert.

## Auf die Produktion umstellen

Ändern Sie `"directory"` auf `"letsencrypt"` (oder entfernen Sie es; das ist die Voreinstellung) und starten Sie neu. Die
Erneuerung geschieht dann im Hintergrund, wenn ein Drittel der Laufzeit übrig ist.

## Prüfen, ob es funktioniert hat

- `qbixconsole ssl:show` listet das Zertifikat, seinen Aussteller und Ablauf und kein `acme.lastError`.
- Von außen: `openssl s_client -connect www.example.gov:443 -servername www.example.gov </dev/null 2>/dev/null | openssl x509 -noout -issuer -dates`.
- Sichern Sie das Verzeichnis `ssl/` von Velocity, das den ACME-Kontoschlüssel enthält.

## Fallstricke

| Symptom | Behebung |
|---|---|
| `Invalid response from .../.well-known/acme-challenge/...` | Port 80 erreicht diesen Server nicht; öffnen Sie ihn, leiten Sie den Pfad per Proxy weiter oder setzen Sie `acme.webroot` für den Server, dem Port 80 gehört |
| Ein selbstsigniertes Zertifikat wird ausgeliefert | die Ausstellung ist fehlgeschlagen; Velocity hält HTTPS mit einem selbstsignierten Zertifikat aufrecht, bis das echte verwendbar ist |
| Warnungen zur Uhrzeit | lassen Sie NTP laufen |

## Verwandte Seiten
- [Ein HTTPS-Zertifikat in Velocity erneuern](ein-https-zertifikat-in-velocity-erneuern.md)
- [Velocity für Teams im öffentlichen Sektor](../../../../../eng-us/help/government/operations/hosting/velocity-for-public-sector-teams.md)
- [TLS und HTTPS (Hosting)](../../../hosting/sicherheit/tls-und-https.md)
- [Control Panel von Velocity](../../../../../eng-us/features/6.0/velocity-control-panel.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
