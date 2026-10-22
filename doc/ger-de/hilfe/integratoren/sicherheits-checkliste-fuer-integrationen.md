---
title: Sicherheits-Checkliste für Integrationen
audience: integrator, admin, developer
category: integration
order: 500
summary: Eine Checkliste für die Sicherheitsprüfung einer Exponential-Integration vor dem Livegang und einmal im Jahr - Zugangsdaten, Konten und Rechte, Transport, Umgang mit Eingaben, ausgehende Aufrufe, Protokollierung, Audit, Datenschutz und Bereitschaft für Vorfälle - jeder Punkt mit der Einstellung oder Seite, die ihn belegt.
keywords: Sicherheitsprüfung, Checkliste, Zugangsdaten, Geheimnisse, Dienstkonto, minimale Rechte, HTTPS, TLS, Eingabeprüfung, SSRF, Protokollierung, Audit, personenbezogene Daten, Vorfall, Schlüsselrotation, Livegang, security review, checklist, credentials, secrets, service account, least privilege, input validation, logging, personal data, incident, key rotation, go-live
type: help
---
# Sicherheits-Checkliste für Integrationen

Diese Seite ist für die Person, die eine Integration vor dem Livegang prüft, und danach einmal im Jahr erneut. Jeder
Punkt nennt, wo er zu prüfen ist. Die Gründe hinter den Punkten stehen auf
[Sicherheit für Integrationen](sicherheit-fuer-integrationen.md).

## Inhalt

1. [Zugangsdaten](#zugangsdaten)
2. [Konten und Rechte](#konten-und-rechte)
3. [Transport](#transport)
4. [Eingabe und Ausgabe](#eingabe-und-ausgabe)
5. [Protokollierung und Audit](#protokollierung-und-audit)
6. [Datenschutz](#datenschutz)
7. [Bereitschaft für Vorfälle](#bereitschaft-für-vorfälle)
8. [Freigabe](#freigabe)

## Zugangsdaten

- [ ] Kein Geheimnis im Repository der Erweiterung oder in seiner Historie (suchen Sie nach `expk_`, `expt_`,
      `password`, `secret`, `key`).
- [ ] Geheimnisse nur in `settings/override/*.ini.append.php` (außerhalb von Git gehalten) oder in der Umgebung.
- [ ] Ein Satz Zugangsdaten pro Integration, Umgebung und Zweck; die Namen sagen, welcher.
- [ ] API-Schlüssel auf die nötigen Bereiche (Scopes) beschränkt; Ablaufdaten gesetzt; Rotationstermin im Kalender
      ([Persönliche API-Schlüssel](persoenliche-api-schluessel-fuer-integrationen.md)).
- [ ] OAuth-Client-Geheimnisse nur auf Servern; `oauth/authorize` nur für die Rollen, die es brauchen.
- [ ] Zugangsdaten, die das Personal des Integrators kannte, werden bei der Übergabe rotiert.

## Konten und Rechte

- [ ] Ein Dienstkonto pro Integration; keine Administratorrolle ([Dienstkonten](dienstkonten-rollen-und-richtlinien.md)).
- [ ] Richtlinien so weit wie möglich nach Teilbaum, Klasse, Sektion und Sprache eingeschränkt.
- [ ] Ein verbotener Aufruf wurde ausprobiert und mit `403` beantwortet.
- [ ] Skripte laufen als Betriebssystembenutzer der Website, nicht als root.
- [ ] LDAP: eigenes Bind-Konto nur mit Leserechten; `LDAPDebugTrace=disabled`.
- [ ] SSO-Handler: Der vertrauenswürdige Header kann nur vom Proxy kommen; Administratoren melden sich auf andere Weise
      an.

## Transport

- [ ] HTTPS bei jeder API und jedem Webhook, in beide Richtungen; `rest.ini [Authentication] RequireHTTPS` erwogen.
- [ ] Zertifikatsprüfung bei jedem ausgehenden Aufruf eingeschaltet.
- [ ] SFTP statt FTP; Host-Schlüssel ausdrücklich als vertrauenswürdig hinterlegt.
- [ ] LDAP über `ldaps://`.
- [ ] Endpunkte, die nur andere Systeme nutzen, sind wo möglich am Webserver auf deren Adressen beschränkt.

## Eingabe und Ausgabe

- [ ] Jeder Wert aus einem anderen System wird geprüft (Typ, Länge, erlaubte Zeichen); IDs werden in Ganzzahlen
      umgewandelt.
- [ ] Eigenes SQL verwendet `(int)` und `$db->escapeString()`; keine Verkettung von Zeichenketten aus ungeprüfter
      Eingabe.
- [ ] Rich Text wird in eine erlaubte Menge von Elementen umgewandelt; kein rohes HTML in Templates.
- [ ] URLs: nur `https`/`http`; unsichere Schemata abgewiesen.
- [ ] Dateiimporte bleiben innerhalb des Importverzeichnisses unter `var/`; Größen begrenzt.
- [ ] Ausgehende Aufrufe an Adressen aus Daten weisen private und Loopback-Adressen ab; Timeouts und Größengrenzen
      gesetzt.
- [ ] Eigene Endpunkte prüfen den Zugriff (`canRead()`, Richtlinien) und verlangen für Schreibzugriffe `POST` und das
      Formular-Token.
- [ ] Antworten enthalten keine Passwörter, Tokens oder personenbezogenen Daten, die der Aufrufer nicht braucht.

## Protokollierung und Audit

- [ ] Eine Zusammenfassungszeile pro Lauf im Protokoll der Integration; Fehler in `error.log` über
      `eZDebug::writeError()`.
- [ ] Keine Geheimnisse oder vollständigen Nutzdaten mit personenbezogenen Daten in irgendeinem Protokoll.
- [ ] Die Aktivität des Dienstkontos nach den ersten Läufen im Audit geprüft (`exp:audit tail`).
- [ ] Abweisungen (`access.apikey.use.failed`, `access.permission.refused`) werden beobachtet.
- [ ] Falls gefordert, werden Sicherheitsereignisse über die Syslog- oder Webhook-Senke an das SIEM gesendet
      ([Ausgehende Webhooks](ausgehende-webhooks-aus-dem-audit-trail.md)).

## Datenschutz

Diese Punkte sind Praxis, keine Rechtsberatung.

- [ ] Jeder Fluss mit personenbezogenen Daten ist mit Zweck, Feldern, Empfängern und Aufbewahrung aufgeführt.
- [ ] Auftragsverarbeiter (E-Mail-Anbieter, KI-Anbieter, CDN, SaaS-Suche) sind dem Kunden bekannt und durch
      Vereinbarungen abgedeckt.
- [ ] Nur nötige Felder werden gesendet; Exporte werden gelöscht, wenn sie nicht mehr gebraucht werden.
- [ ] Gesammelte Formulardaten werden aus Exponential entfernt, wenn die Aufbewahrung endet.
- [ ] Testdaten werden anonymisiert, wenn eine Kopie der Produktion die Produktion verlässt.

## Bereitschaft für Vorfälle

- [ ] Das Runbook sagt, wie jeder Satz Zugangsdaten sofort widerrufen wird.
- [ ] Das Runbook sagt, wie jeder Fluss angehalten wird (Setup > Cronjobs, Anhalten).
- [ ] Ansprechpartner für jedes andere System und für Sicherheit sind aufgeführt.
- [ ] Sicherheitslücken in Exponential werden vertraulich an security@se7enx.com gemeldet.

## Freigabe

```text
Integration: ...............   Version: .........   Environment: production
Reviewed by: ...............   Date: .........
Open findings (id, severity, owner, due): ...............
Decision: go-live approved / approved with conditions / not approved
```

Auf Deutsch: Integration, Version, Umgebung (Produktion); geprüft von, Datum; offene Befunde (ID, Schweregrad,
Verantwortlicher, Frist); Entscheidung: Livegang freigegeben / unter Auflagen freigegeben / nicht freigegeben.

## Verwandte Seiten

- [Sicherheit für Integrationen](sicherheit-fuer-integrationen.md)
- [Checkliste für die Sicherheitsprüfung beim Hosting](../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md)
- [Checklisten zu Barrierefreiheit, Sicherheit und Compliance](../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md)
- [Checkliste zum Livegang einer Integration](checkliste-fuer-den-livegang-einer-integration.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
