---
title: Keine Pflicht zu externen Diensten
audience: government, admin, project-manager
category: public-sector
order: 50
summary: Exponential liefert Seiten aus und betreibt seine Administration ohne jeden fremden Dienst - kein Lizenzserver und keine Skripte oder Schriften von Drittanbietern in der Administration. Diese Seite listet auf, was doch ins Internet geht, und nur dann, wenn Sie es nutzen.
keywords: externe Dienste, Drittanbieter, offline, Air Gap, abgeschottetes Netz, CDN, LoadFromCDN, Telemetrie, Lizenzserver, ausgehende Verbindungen, Souveränität, Datenschutz, external services, third party, telemetry, license server, outbound connections, admin assets
type: help
---
# Keine Pflicht zu externen Diensten

Diese Seite richtet sich an Administratoren, Informationssicherheits- und Datenschutzbeauftragte, die wissen müssen, ob
die Plattform jemanden außerhalb der Organisation kontaktiert. Exponential braucht keinen fremden Dienst, um die
Website auszuliefern oder die Administration zu betreiben. Einige Funktionen gehen ins Internet, wenn Sie sie nutzen
oder aktivieren; sie sind unten aufgeführt, damit Sie sie erlauben, sperren oder dokumentieren können.

## Inhalt

1. [Was ohne jeden fremden Dienst läuft](#was-ohne-jeden-fremden-dienst-läuft)
2. [Die Administration lädt keine Ressourcen von Drittanbietern](#die-administration-lädt-keine-ressourcen-von-drittanbietern)
3. [Funktionen, die bei Nutzung ins Internet gehen](#funktionen-die-bei-nutzung-ins-internet-gehen)
4. [Erweiterungen, an denen Drittanbieter beteiligt sind](#erweiterungen-an-denen-drittanbieter-beteiligt-sind)
5. [Selbst nachprüfen](#selbst-nachprüfen)

## Was ohne jeden fremden Dienst läuft

Seiten ausliefern, bearbeiten und veröffentlichen, die Suche mit der eingebauten Suchmaschine, Rollen und Sektionen,
Versionen, der Audit-Trail, Workflows, Caches und die Cronjobs, die sie pflegen, laufen alle auf Ihrem Server und mit
Ihrer Datenbank. Es gibt keinen Lizenzschlüssel und keinen Lizenzserver: Die Software steht unter der GPL
([Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md)).

## Die Administration lädt keine Ressourcen von Drittanbietern

Der Datenschutzstandard hält fest: jQuery und die anderen Skripte der Administration werden lokal ausgeliefert
(`extension/ezjscore/settings/ezjscore.ini [eZJSCore] LoadFromCDN=disabled`), und das Design admin4 lädt keine
Webschriften oder Skripte von anderen Hosts (`design/admin4/templates/pagelayout.tpl`)
([Datenschutzstandards](../../../../eng-us/standards/privacy.md)). Die öffentlichen Designs sind Ihre eigenen; prüfen Sie die
von Ihnen verwendeten auf Schriften, Skripte und Einbettungen von anderen Hosts.

## Funktionen, die bei Nutzung ins Internet gehen

| Funktion | Was sie kontaktiert | Wann |
|---|---|---|
| Composer, **Setup > Updates and packages** (`ezupdate`) | packagist.org und die Paketquellen in `composer.json` | wenn Sie Pakete suchen, aktualisieren oder installieren; packagist.org lässt sich abschalten |
| Linkprüfung (`linkcheck.php`) | die externen Adressen in Ihren Inhalten | wenn der Cronjob-Teil läuft; er fragt nie Adressen in einem privaten Netz ab |
| RSS-Importe | die Feeds, die Sie eingerichtet haben | wenn der Cronjob `rssimport` läuft |
| E-Mail | Ihren Mailserver | wenn E-Mails versendet werden |
| Solr (`ezfind`) | den Solr-Server, den Sie einrichten | wenn aktiv |
| REST-API-Clients, OAuth | die Clients, die Sie registriert haben | eingehend, wenn sie aufrufen |

## Erweiterungen, an denen Drittanbieter beteiligt sind

Mit Exponential installiert, aber **nicht aktiv**, bis eine Website sie aktiviert; jede sendet Daten von Besuchern
oder Inhalten an einen Drittanbieter
([Datenschutzstandards](../../../../eng-us/standards/privacy.md#third-party-services-in-shipped-extensions)):

| Erweiterung | Drittanbieter |
|---|---|
| `bcwebsitestatistics` | Google Analytics / Google Tag Manager |
| `recaptcha`, `hcaptcha` | Google reCAPTCHA, hCaptcha |
| Social-Login-Handler von `sevenx_authentication_2fa` | der gewählte Identitätsanbieter |
| `ezgmaplocation` mit einem API-Schlüssel | Google Maps |
| `expai` | der KI-Anbieter, den Sie einrichten; ein lokales Modell über Ollama ist ebenfalls möglich ([expai](../../../../eng-us/features/6.0/extensions/expai.md)) |

Einen Prüfablauf finden Sie unter „Zu prüfende Dienste von Drittanbietern“.

## Selbst nachprüfen

- **Setup > Extensions** listet auf, was aktiv ist; `settings/override/site.ini.append.php` enthält
  `ActiveExtensions[]`.
- Laden Sie in den Entwicklerwerkzeugen eines Browsers eine Seite der Administration und eine öffentliche Seite und
  achten Sie auf Anfragen an andere Hosts.
- Beobachten Sie auf dem Server einen Tag normaler Nutzung lang die ausgehenden Verbindungen der PHP-Prozesse, mit
  Ihren üblichen Netzwerkwerkzeugen oder den Protokollen der Firewall.
- Eine Content Security Policy lässt den Browser melden, was eine Seite lädt
  ([Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)).

## Verwandte Seiten

- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Sicherheitsstandards](../../../../eng-us/standards/security.md)
- [Links, URL-Aliase, Platzhalter und Suchstatistiken](../../../anleitungen/urls-und-aliase.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
