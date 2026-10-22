---
title: Zugangsdaten für Agenten
audience: ai-agents, developer, app-creator, admin
category: ai-agents
order: 64
summary: Welche Zugangsdaten Sie einem KI-Agenten geben und wie Sie sie aufbewahren - ein eigenes Konto, ein persönlicher API-Schlüssel mit Geltungsbereichen oder ein expservices-Token mit kurzer Laufzeit, OAuth2 für Anwendungen, Schlüssel von KI-Anbietern nur in settings/override - sowie Aufbewahrung, Erneuerung, Widerruf und was nie in einen Prompt gehört.
keywords: Zugangsdaten, Geheimnisse, API-Schlüssel, expk_, expt_-Token, OAuth2, Anbieterschlüssel, settings/override, Erneuerung, Widerruf, minimale Rechte, Geheimnisspeicher, Prompt, credentials, secrets, API key, provider key, rotation, revocation, least privilege, secret store
type: help
---
# Zugangsdaten für Agenten

Diese Seite richtet sich an Entwickler und Administratoren, die einem KI-Agenten Zugangsdaten übergeben. Ein Agent kann sich irren,
kann durch gelesenen Text irregeleitet werden und hinterlässt Transkripte. Er sollte daher die engsten Zugangsdaten halten, die die
Aufgabe erledigen, für die kürzeste Zeit, und sie nie in seinem eigenen Kontext sehen.

## Inhalt

1. [Ein Konto je Agent](#ein-konto-je-agent)
2. [Welche Zugangsdaten](#welche-zugangsdaten)
3. [Wo Zugangsdaten liegen](#wo-zugangsdaten-liegen)
4. [Erneuerung und Widerruf](#erneuerung-und-widerruf)
5. [Was nie in einen Prompt gehört](#was-nie-in-einen-prompt-gehört)

## Ein Konto je Agent

Geben Sie jedem Agenten sein eigenes Konto, in seiner eigenen Benutzergruppe, mit einer für ihn gemachten Rolle. Dann:

- sind seine Berechtigungen genau das, was Sie gewährt haben, nicht das, was eine Person zufällig hat;
- wird jede seiner Aktionen unter seinem eigenen Namen im Audit-Trail festgehalten;
- stoppt ihn das Deaktivieren des Kontos sofort, überall.

Geben Sie einem Agenten nie das Administratorkonto oder das Konto einer Person. Details:
[Rollen und minimale Rechte](../steuerung/rollen-und-minimale-rechte.md).

## Welche Zugangsdaten

| Schnittstelle | Zugangsdaten | Eingrenzung | Lesen |
|---|---|---|---|
| REST-API | Persönlicher API-Schlüssel `expk_...` | Geltungsbereiche (`read`, `publish`, `edit`, `remove`), eine Laufzeit, nie über die Richtlinien des Eigentümers hinaus | [Ein nur lesender API-Schlüssel, Schritt für Schritt](ein-nur-lesender-api-schluessel-schritt-fuer-schritt.md) |
| REST-API für viele Benutzer | OAuth2-Token | Die eigenen Richtlinien des Benutzers, `oauth/authorize` | [OAuth und API-Schlüssel](../../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md) |
| expservices | Persönliches Token `expt_...` | Die Richtlinien des Benutzers des Tokens, eine Laufzeit | [Agenten und expservices](agenten-und-expservices.md) |
| Exponential Intelligence | Der Anbieterschlüssel | Liegt auf dem Server; Redakteure sehen ihn nie | [Einen Anbieter konfigurieren](../expai/einen-anbieter-konfigurieren.md) |

## Wo Zugangsdaten liegen

- **Schlüssel von KI-Anbietern**: nur in `settings/override` (Modus 0640), nie in einer committeten Datei. Setup > EI: Models warnt
  vor einem Anbieterschlüssel an jedem anderen Ort.
- **Schlüssel und Tokens, die ein externer Agent verwendet**: im Geheimnisspeicher der Agentenplattform oder in einer
  Umgebungsvariablen, nie in Quellcode, einer URL, einem Prompt oder einer geteilten Konfigurationsdatei.

Allgemeine Regeln für Geheimnisse in Exponential: [Umgebung, Einstellungen und Geheimnisse](../../ersteller/anwendungsentwickler/umgebungseinstellungen-und-geheimnisse.md).

## Erneuerung und Widerruf

| Zugangsdaten | Widerrufen | Erneuern |
|---|---|---|
| Persönlicher API-Schlüssel | Mein Konto > API-Zugang oder ein Administrator unter Setup > oAuth-Verwaltung > API-Schlüssel | Den neuen Schlüssel erstellen, bevor der alte endet, umstellen, den alten widerrufen |
| expservices-Token | `expsession::tokenRevoke` | Ein neues Token erstellen, umstellen, widerrufen |
| OAuth2 | Setup > oAuth-Verwaltung | Tokens laufen nach `TokenTTL` ab; Refresh-Tokens erneuern sie |
| Jede davon | Das Deaktivieren des Kontos des Eigentümers beendet sofort jeden Schlüssel und jedes Token | |

Erstellen, Widerrufen, erste Verwendung und Ablehnungen von API-Schlüsseln stehen im Audit-Trail als `access.apikey.*`;
expservices-Tokens als `access.expservices.token.*` ([Der Audit-Trail für KI](../steuerung/audit-trail-fuer-ki.md)).

## Was nie in einen Prompt gehört

- Ein Schlüssel, Token, Passwort oder Session-Cookie, auch nicht "nur zum Testen".
- Der Inhalt von `settings/override`.
- Personenbezogene Daten, die die Aufgabe nicht braucht.

Haben Zugangsdaten den Kontext eines Modells oder ein Transkript erreicht, behandeln Sie sie als offengelegt: Widerrufen Sie sie und
erstellen Sie neue ([Reaktion auf KI-Vorfälle](../steuerung/reaktion-auf-ki-vorfaelle.md)).

## Verwandte Seiten

- [Sicherheit für Anwendungen](../../ersteller/anwendungsentwickler/sicherheit-fuer-anwendungen.md)
- [Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md)
- [Bevor Sie einen Agenten verbinden](../checklisten/bevor-sie-einen-agenten-verbinden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
