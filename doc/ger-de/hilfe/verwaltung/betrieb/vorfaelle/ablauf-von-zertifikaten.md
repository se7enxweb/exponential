---
title: Ablauf von Zertifikaten
audience: government, admin, hosting
category: public-sector
order: 140
summary: Runbook für Browserwarnungen zum HTTPS-Zertifikat einer öffentlichen Website - sehen, was ausgeliefert wird, unter Velocity (ssl show und renew, automatische Erneuerung mit Let's Encrypt) oder unter Apache, nginx oder einem Control Panel erneuern und mit Überwachung und klarer Verantwortung für Zertifikate vorbeugen.
keywords: Ablauf von Zertifikaten, abgelaufenes Zertifikat, HTTPS, TLS, SSL, Browserwarnung, Let's Encrypt, ACME, Erneuerung, Velocity, selbstsigniert, HSTS, openssl, Vorfall, certificate expiry, expired certificate, browser warning, renewal, self-signed, incident
type: help
---
# Ablauf von Zertifikaten

Dieses Runbook richtet sich an die Administration, wenn Besucher eine Browserwarnung zum Zertifikat einer öffentlichen
Website erhalten. Bürgerinnen und Bürgern wird beigebracht, bei solchen Warnungen abzubrechen; ein abgelaufenes
Zertifikat auf einer Verwaltungswebsite kommt daher einem Ausfall gleich. Das ausführliche Runbook ist
[Ablauf von Zertifikaten (Hosting)](../../../hosting/runbooks/ablauf-von-zertifikaten.md).

## Inhalt
1. [Sehen, was ausgeliefert wird](#sehen-was-ausgeliefert-wird)
2. [Unter Velocity](#unter-velocity)
3. [Unter Apache, nginx oder einem Control Panel](#unter-apache-nginx-oder-einem-control-panel)
4. [Nicht auf unverschlüsseltes HTTP umschalten](#nicht-auf-unverschlüsseltes-http-umschalten)
5. [Vorbeugen](#vorbeugen)
6. [Verwandte Seiten](#verwandte-seiten)

## Sehen, was ausgeliefert wird

```bash
openssl s_client -connect www.example.gov:443 -servername www.example.gov </dev/null 2>/dev/null \
  | openssl x509 -noout -subject -issuer -dates
```

Ein `notAfter` in der Vergangenheit bedeutet abgelaufen; ein Aussteller, der unter Velocity dem Subjekt entspricht,
bedeutet, dass Velocity auf ein selbstsigniertes Zertifikat ausgewichen ist, weil das konfigurierte fehlt, abgelaufen
ist oder nicht zu seinem Schlüssel passt.

## Unter Velocity

Velocity prüft ein Zertifikat, bevor es es verwendet, und lässt ein selbstsigniertes Zertifikat einspringen, bis das
echte verwendbar ist ([Velocity: HTTPS und Zertifikate](../../../../../eng-us/features/6.0/velocity-https-certificates.md)).

```bash
./console exp:velocity ssl show --allow-root-user
./console exp:velocity ssl renew www.example.gov --allow-root-user
```

Der laufende Server übernimmt eine Erneuerung innerhalb einer Minute, ohne Neustart. Mit Let's Encrypt läuft die
Erneuerung in einem Hintergrundjob mit wachsenden Pausen nach einem Fehlschlag; wenn sie immer wieder scheitert, prüfen
Sie, ob Port 80 aus dem Internet erreichbar ist und DNS auf den Server zeigt. Mit eigenen Dateien legen Sie das neue
Paar an seinen Platz und prüfen mit `ssl show`
([Ein HTTPS-Zertifikat in Velocity erneuern](../../../../../eng-us/help/government/tasks/operations/renew-an-https-certificate-in-velocity.md)).

## Unter Apache, nginx oder einem Control Panel

Erneuern Sie mit dem Werkzeug, das das Zertifikat ausgestellt hat (ein ACME-Client wie certbot, das Control Panel oder
der Zertifikatsdienst Ihrer Organisation), installieren Sie es, laden Sie den Webserver neu und prüfen Sie erneut von
außen.

## Nicht auf unverschlüsseltes HTTP umschalten

Browser, die `Strict-Transport-Security` gesehen haben, verweigern unverschlüsseltes HTTP für die gemerkte Zeit;
Velocity sendet standardmäßig `HSTSMaxAge=300`, ein Header der Website kann ein Jahr festlegen. Erneuern Sie
stattdessen.

## Vorbeugen

- Benennen Sie für jedes Zertifikat eine verantwortliche Stelle (oft das Infrastrukturteam oder ein zentraler
  Zertifikatsdienst) und halten Sie das Ablaufdatum fest.
- Überwachen Sie den Ablauf von außen ([Externe Verfügbarkeitsüberwachung](../ueberwachung/externe-verfuegbarkeitsueberwachung.md)).
- Bevorzugen Sie die automatische Erneuerung (Let's Encrypt oder den ACME-Dienst Ihrer Organisation), wo Ihre
  Richtlinien es erlauben; manche Verwaltungsdomains verlangen Zertifikate einer bestimmten Zertifizierungsstelle,
  planen Sie die manuelle Erneuerung dann frühzeitig.

## Verwandte Seiten
- [Velocity auf Let's Encrypt umstellen](../../../../../eng-us/help/government/tasks/operations/switch-velocity-to-lets-encrypt.md)
- [TLS und HTTPS (Hosting)](../../../hosting/sicherheit/tls-und-https.md)
- [Velocity-HTTPS-Zertifikate (Hosting)](../../../hosting/server/https-zertifikate-in-velocity.md)
- [Website nicht erreichbar](website-nicht-erreichbar.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
