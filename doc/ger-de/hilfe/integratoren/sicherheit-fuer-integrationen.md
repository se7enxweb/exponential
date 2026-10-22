---
title: Sicherheit für Integrationen
audience: integrator, developer, admin
category: integration
order: 340
summary: Die Sicherheitsregeln, denen eine Exponential-Integration folgen muss - Zugangsdaten nie in Git, settings/override und die Umgebung, minimale Rechte für Dienstkonten, Transportsicherheit, Eingaben anderer Systeme als nicht vertrauenswürdig behandeln, ausgehende Aufrufe, die Content Security Policy für Skripte Dritter sowie Audit und Umgang mit Vorfällen.
keywords: Sicherheit, Zugangsdaten, Geheimnisse, Umgebungsvariablen, minimale Rechte, Dienstkonto, Eingabeprüfung, ausgehende Aufrufe, Skripte Dritter, Vorfall, Schlüsselrotation, security, credentials, secrets, settings/override, gitignore, environment variables, least privilege, service account, HTTPS, TLS, input validation, SSRF, outbound calls, Content Security Policy, third-party scripts, analytics, audit, incident, key rotation
type: help
---
# Sicherheit für Integrationen

Diese Seite richtet sich an alle, die eine Integration mit Exponential bauen oder betreiben. Eine Integration öffnet
eine Tür: Sie hält Zugangsdaten für zwei Systeme, sie bewegt Daten über eine Grenze, und sie läuft mit Rechten, die
niemand täglich beobachtet. Die allgemeine Härtung einer Installation steht unter
[Sicherheitshärtung](../sicherheitshaertung.md) und [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md); diese
Seite ergänzt, was für Integrationen gilt. Die Fassung als Checkliste ist
[Sicherheits-Checkliste für Integrationen](sicherheits-checkliste-fuer-integrationen.md).

## Inhalt

1. [Zugangsdaten](#zugangsdaten)
2. [Minimale Rechte](#minimale-rechte)
3. [Transport](#transport)
4. [Daten anderer Systeme sind nicht vertrauenswürdig](#daten-anderer-systeme-sind-nicht-vertrauenswürdig)
5. [Ausgehende Aufrufe](#ausgehende-aufrufe)
6. [Skripte Dritter in Seiten](#skripte-dritter-in-seiten)
7. [Audit und Vorfälle](#audit-und-vorfälle)

## Zugangsdaten

| Regel | Wie in Exponential |
|---|---|
| Nie in Git | Halten Sie Geheimnisse in `settings/override/<file>.ini.append.php` jeder Installation und dieses Verzeichnis (oder zumindest diese Dateien) mit `.gitignore` aus dem Repository des Projekts heraus; nie in den eigenen `settings/` einer Erweiterung |
| Oder in der Umgebung | Lesen Sie sie in Ihrer Erweiterung mit `getenv()`, wenn das Hosting einen Geheimnisspeicher als Umgebungsvariablen bereitstellt |
| Nie in einer URL | In der Adresse gesendete API-Schlüssel lehnt die REST-Schicht ab; tun Sie in Ihren eigenen Endpunkten dasselbe |
| Nie in Protokollen oder Paketen | Maskieren Sie sie; Pakete reisen |
| Eine Zugangsberechtigung je System, Umgebung und Zweck | Ein Schlüssel für "ERP-Import, Produktion" wird nicht vom Staging verwendet |
| Rotation geplant | Enddaten auf Schlüsseln; ein Rotationsablauf im Runbook ([Persönliche API-Schlüssel](persoenliche-api-schluessel-fuer-integrationen.md#rotieren-ohne-ausfall)) |
| Verschlüsselt abgelegt, wo das Werkzeug es unterstützt | `xrowextract` verschlüsselt Geheimnisse der Ziele mit einem Schlüssel in `settings/override/xrowextract-secrets.key`; `sevenx_authentication_2fa` verschlüsselt Authenticator-Geheimnisse mit `[TOTPSettings] SecretKey` |

`settings/override` hat Vorrang vor jeder Einstellung von Siteaccesses und Erweiterungen; deshalb ist es der richtige
Ort für Geheimnisse, und deshalb kann ein Override auf einem Server auch unbemerkt das Verhalten ändern:
Dokumentieren Sie jedes Override, das die Integration braucht.

## Minimale Rechte

- **Ein Dienstkonto je Integration**, in einer eigenen Benutzergruppe, mit einer Rolle, die nur gewährt, was die
  Integration tut: Lesen auf den Teilbäumen, die sie exportiert, Anlegen, nach Klasse und Teilbaum begrenzt, für das,
  was sie importiert ([Dienstkonten, Rollen und Richtlinien](dienstkonten-rollen-und-richtlinien.md)).
- **Scopes auf Schlüsseln**: Eine lesende Integration erhält einen Schlüssel nur mit dem Scope `read`.
- **Keine Administrator-Zugangsdaten** in irgendeiner Integration, niemals. Ein Import, der "Admin braucht", braucht
  meist eine Richtlinie mehr.
- **Benutzer des Betriebssystems**: Führen Sie Skripte als Benutzer der Website aus, nicht als root.

## Transport

- HTTPS für jede API, jeden Webhook und jede Dateiübertragung, in beide Richtungen.
  `rest.ini [Authentication] RequireHTTPS` lässt die REST-Schicht reines HTTP ablehnen.
- Prüfen Sie Zertifikate bei ausgehenden Aufrufen (der SOAP-Client und curl tun das standardmäßig; schalten Sie es nie
  ab, "damit es funktioniert").
- LDAP über eine verschlüsselte Verbindung (`ldaps://`); StartTLS steht nicht zur Verfügung
  ([Login-Handler](login-handler-ldap-und-textdatei.md)).
- SFTP statt FTP; das SFTP-Ziel von `xrowextract` vertraut Hostschlüsseln ausdrücklich.

## Daten anderer Systeme sind nicht vertrauenswürdig

Behandeln Sie jeden Wert aus einem anderen System wie ein Formularfeld aus dem Internet:

- Prüfen Sie Typen und Längen vor der Verwendung; wandeln Sie IDs mit `(int)` um; maskieren Sie Zeichenketten in
  eigenem SQL mit `$db->escapeString()`.
- Wandeln Sie Rich Text mit einer kleinen erlaubten Menge an Elementen in das gespeicherte XML von `ezxmltext` um;
  geben Sie HTML nie direkt an Templates weiter.
- Lehnen Sie unsichere URL-Schemata ab; der Datentyp `ezurl` lehnt Skript-URLs beim Import ab, tun Sie in Ihrem
  eigenen Code dasselbe.
- Dateipfade für Bilder und Dateien müssen im Importverzeichnis der Installation bleiben; der Datentyp `ezimage` lehnt
  Pfade außerhalb der Installation, ihres var- und ihrer temporären Verzeichnisse ab.
- Begrenzen Sie Größen: Eine bösartige oder defekte Quelle kann ein "Bild" von 2 GB senden.

## Ausgehende Aufrufe

Wenn Ihre Integration Adressen aufruft, die aus Daten oder Einstellungen stammen (eine Webhook-URL, eine Bild-URL aus
einem Feed):

- Erlauben Sie nur `https` (und `http`, wo unvermeidlich).
- Lehnen Sie private und Loopback-Adressen ab, sofern sie nicht erwartet sind, um serverseitige Request-Fälschung zu
  vermeiden.
- Setzen Sie Timeouts für Verbindungsaufbau und Gesamtdauer sowie eine maximale Antwortgröße.
- Der Link-Checker des Kernels zeigt das Muster: `cronjob.ini [linkCheckSettings] AllowedPrivateHosts[]`.

## Skripte Dritter in Seiten

Analysewerkzeuge, Chat-Widgets und Tag-Manager laufen in den Browsern der Besucher. Exponential sendet eine Content
Security Policy; ein Skript von einem anderen Host muss dort erlaubt sein, und Inline-Skripte sind es nicht
([Content Security Policy](../../funktionen/6.0/content-security-policy.md)). Fügen Sie jeden Host eines Drittanbieters
bewusst hinzu, laden Sie Skripte aus Dateien Ihres Designs und halten Sie die Folgen für den Datenschutz mit dem Kunden
fest (keine Rechtsberatung).

## Audit und Vorfälle

- Das Audit-Protokoll zeichnet die Verwendung und Ablehnung von API-Schlüsseln (`access.apikey.*`), Schreibvorgänge
  von `expservices` (`service.*`) und Inhaltsänderungen auf. Prüfen Sie das Dienstkonto der Integration nach den
  ersten Läufen im Audit.
- Leiten Sie Sicherheitsereignisse mit dem Syslog- oder Webhook-Ziel an das SIEM des Kunden weiter
  ([Ausgehende Webhooks](ausgehende-webhooks-aus-dem-audit-trail.md#sicherheitsereignisse-an-ein-siem-senden)).
- Wenn Zugangsdaten offengelegt sein könnten: Widerrufen Sie sie sofort, erstellen Sie neue, prüfen Sie das Audit auf
  ihre Verwendung und informieren Sie den Kunden. Melden Sie Schwachstellen in Exponential vertraulich an
  security@se7enx.com.

## Verwandte Seiten

- [Sicherheitshärtung](../sicherheitshaertung.md)
- [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md)
- [Sicherheit in Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/sicherheit-in-erweiterungen.md)
- [Sicherheitsverantwortung von Partnern](../partner/sicherheitspflichten.md)
- [Sicherheits-Checkliste für Integrationen](sicherheits-checkliste-fuer-integrationen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
