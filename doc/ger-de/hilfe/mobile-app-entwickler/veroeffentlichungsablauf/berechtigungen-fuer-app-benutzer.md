---
title: Berechtigungen für App-Benutzer
audience: mobile-app-developer, app-creator, developer, admin
category: mobile-apps
order: 720
summary: Die Benutzerkonten und Rollen einrichten, unter denen eine App läuft - Integrationsbenutzer, auf Bereiche oder Teilbäume beschränkte Leserollen, auf einen Ordner und eine Klasse beschränkte Schreibrollen, die Richtlinien, die OAuth und API-Schlüssel brauchen, die Rechte eines Benutzers über die APIs prüfen und die App-Aktivität im Prüfprotokoll nachvollziehen.
keywords: Berechtigungen, Rollen, Richtlinien, Integrationsbenutzer, content/read, content/create, content/edit, content/remove, oauth/authorize, apikey/create, Einschränkungen, Teilbaum, Bereich, Klasse, expsession::access, Prüfprotokoll, exp:audit, access.apikey, Dienstereignisse, Mobile App, permissions, roles, policies, integration user, limitations, subtree, section, class, audit trail, service events, mobile app
type: help
---
# Berechtigungen für App-Benutzer

Diese Seite richtet sich an App-Entwickler und die Administratoren, mit denen sie zusammenarbeiten. Jeder API-Aufruf
läuft als ein Benutzer, und die Rollen dieses Benutzers entscheiden über alles, was die App sehen und tun kann. Die
Rollen richtig einzustellen ist die wirksamste Sicherheitsmaßnahme, die eine App hat. Der allgemeine Leitfaden ist
[Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md).

## Inhalt

1. [Richtlinien, die die APIs prüfen](#richtlinien-die-die-apis-prüfen)
2. [Rollen für typische Apps](#rollen-für-typische-apps)
3. [Rechte aus der App prüfen](#rechte-aus-der-app-prüfen)
4. [App-Aktivität nachvollziehen](#app-aktivität-nachvollziehen)

## Richtlinien, die die APIs prüfen

| Aktion | Richtlinie |
|---|---|
| REST-Lesezugriff auf einen Knoten, ein Objekt, eine Liste, eine Anzahl | `content/read` |
| Anlegen über REST | `content/create` (Einschränkungen Class, ParentClass, Section, Node, Subtree, Language) |
| Aktualisieren über REST (antwortet nach der Prüfung mit `501`) | `content/edit` |
| Entfernen über REST | `content/remove` für jede Platzierung und alles darunter |
| Eine OAuth-App auf der Autorisierungsseite zulassen | `oauth/authorize` (optional auf einen Siteaccess beschränkt) |
| Persönliche API-Schlüssel erstellen | `apikey/create` (optional durch `Scope` beschränkt) |
| Ein Remote-Dienst | die Richtlinie aus seiner `access`-Deklaration oder `public` / `user` |

Quellen: [Berechtigungen der REST-Schnittstelle](../../../anleitungen/api-schluessel.md#32-berechtigungen-der-rest-schnittstelle),
[Die Richtlinie oauth/authorize](../../../bc/6.0/oauth-autorisierungsrichtlinie.md), die Dienstdeklarationen von `expservices`.

## Rollen für typische Apps

| App | Rolle | Richtlinien |
|---|---|---|
| Öffentliche Lese-App | „App reader“ | `content/read`, beschränkt auf die Bereiche oder Teilbäume, die die App anzeigt |
| Mitglieder-App | die Rolle der Mitglieder plus `oauth/authorize` | was Mitglieder auf der Website lesen dürfen |
| Berichte aus dem Außendienst | „App reporter“ | `content/read` für den Berichtsordner; `content/create` mit Subtree = dieser Ordner und Class = die Berichtsklasse |
| Begleit-App für Redakteure | die Rollen der Redakteure | wie in der Administrationsoberfläche, plus `oauth/authorize` |

Faustregeln:

- Ein Integrationsbenutzer pro App, nie das Konto einer Person und nie der Administrator.
- Keine Richtlinien für `setup`, `class` oder `user` für den Benutzer einer App.
- Schreibzugriffe nur unterhalb eines Ordners, der für die App angelegt wurde, damit ein Fehler den Rest der Website
  nicht berühren kann.
- Überprüfen Sie die Rolle jedes Mal, wenn die App eine neue Funktion erhält.

## Rechte aus der App prüfen

| Bedarf | Dienst |
|---|---|
| Hat der Benutzer ein Modul bzw. eine Funktion? | `expsession::access` (antwortet mit `yes`, `no` oder `limited`) |
| Was darf der Benutzer mit diesem Knoten tun? | `expnode::rights::<node id>` |
| Welche Klassen darf der Benutzer hier anlegen? | `expnode::creatableClasses::<node id>` |
| Wer ist angemeldet? | `expsession::whoami` |

Verwenden Sie sie, um Schaltflächen auszublenden, die der Benutzer nicht nutzen kann; der Server prüft trotzdem bei
jedem Aufruf erneut.

## App-Aktivität nachvollziehen

Das Prüfprotokoll zeichnet auf, was Zugangsdaten und Schreibzugriffe tun, im Kanal `access` und als Dienstereignisse:

| Ereignisse | Wann |
|---|---|
| `access.apikey.create`, `.revoke`, `.use` (erste Verwendung), `.use.failed` | persönliche API-Schlüssel ([Prüfung und Grenzen](../../../anleitungen/api-schluessel.md#8-audit-und-grenzen)) |
| `access.expservices.token.create`, `.revoke`, `.failed` | Tokens von expservices |
| `service.<domain>.<method>` | jeder erfolgreiche Schreibzugriff über expservices |

Ein Administrator liest sie unter **Setup > Audit** oder in einer Shell:

```bash
./console exp:audit tail --channel=access --name='access.apikey.*' --lines=20 --allow-root-user
```

Bitten Sie nach jedem App-Release um einen Blick auf die Ablehnungen (`*.failed`): Ein sprunghafter Anstieg bedeutet
einen Fehler in der App oder jemanden, der gestohlene Zugangsdaten ausprobiert. Das Audit selbst ist in
[Audit](../../../bc/6.0/audit.md) beschrieben.

## Verwandte Seiten

- [Zugangsdaten auswählen](../anmeldung/zugangsdaten-auswaehlen.md)
- [Zugriffskontrolle im Code](../../ersteller/anwendungsentwickler/zugriffskontrolle-im-code.md)
- [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
