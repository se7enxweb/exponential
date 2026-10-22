---
title: Dienstkonten, Rollen und Richtlinien
audience: integrator, admin, developer
category: integration
order: 350
summary: Einer Integration genau die Rechte geben, die sie braucht - ein Dienstkonto je Integration, eine eigene Benutzergruppe und Rolle, die Richtlinien, die typische Integrationen brauchen (content read, create, edit, remove, apikey/create, ezjscore/call), Einschränkungen nach Teilbaum, Klasse, Sektion und Sprache, das Ergebnis prüfen und es regelmäßig überprüfen.
keywords: Dienstkonto, Benutzer, Benutzergruppe, Rolle, Richtlinie, Einschränkung, Teilbaum, Klasse, Sektion, Sprache, minimale Rechte, Zugriffsprüfung, service account, user, user group, role, policy, limitation, Subtree, Class, Section, Language, least privilege, content/read, content/create, content/edit, content/remove, apikey/create, ezjscore/call, oauth/authorize, accesscheck, access review
type: help
---
# Dienstkonten, Rollen und Richtlinien

Diese Seite richtet sich an Administratoren und Integratoren, die das Konto einrichten, als das eine Integration
handelt. Jede Anfrage, jedes Skript und jeder Job in Exponential läuft **als ein Benutzer**, und die Rollen dieses
Benutzers entscheiden, was er tun darf. Die REST-API, API-Schlüssel, `expservices` und die Inhaltsfunktionen prüfen
alle dieselben Richtlinien, daher begrenzt eine gut gebaute Rolle die Integration auf jeder Schnittstelle. Die Seite
der Administration ist [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md).

## Inhalt

1. [Ein Dienstkonto je Integration](#ein-dienstkonto-je-integration)
2. [Die Richtlinien, die Integrationen brauchen](#die-richtlinien-die-integrationen-brauchen)
3. [Einschränkungen](#einschränkungen)
4. [Einrichten](#einrichten)
5. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
6. [Regelmäßig überprüfen](#regelmäßig-überprüfen)

## Ein Dienstkonto je Integration

| Tun Sie | Warum |
|---|---|
| Ein Benutzer je Integration (`svc-erp-import`, `svc-crm-leads`), in einer Gruppe "Integrationen" | Das Audit-Protokoll und **Zuletzt benutzt** der Schlüssel nennen die Integration; eine lässt sich deaktivieren, ohne eine andere zu berühren |
| Eine Rolle je Integration, diesem Benutzer zugewiesen (oder einer eigenen Gruppe) | Änderungen für eine Integration erweitern keine andere |
| Eine echte, aber ungenutzte E-Mail-Adresse auf einer reservierten Domain oder das Postfach des Betreibers | Passwort-Zurücksetzungen und Benachrichtigungen landen nirgends überraschend |
| Ein langes Zufallspasswort, das überhaupt nicht zur Anmeldung verwendet wird, wenn Schlüssel oder Tokens im Einsatz sind | Das Passwort ist die weitreichendste Zugangsberechtigung |
| Keine Abonnements von Benachrichtigungen | Importe dürfen der Integration keine Mails schicken |

## Die Richtlinien, die Integrationen brauchen

| Die Integration | Richtlinien |
|---|---|
| liest Inhalte über REST, `expservices` oder einen Export | `content/read`, begrenzt auf die Teilbäume und Klassen, die sie exportiert |
| legt Inhalte an | `content/create`, begrenzt nach Klasse, Elternklasse, Teilbaum und Sprache; dazu `content/read` |
| aktualisiert Inhalte | `content/edit`, auf dieselbe Weise begrenzt |
| entfernt oder verbirgt Inhalte | `content/remove`, `content/hide`, wo nötig |
| verwendet einen persönlichen API-Schlüssel | `apikey/create` mit der Einschränkung **Scope** auf die Scopes, die sie braucht |
| ruft ezjscore-Serverfunktionen auf | `ezjscore/call` mit der Einschränkung **FunctionList** für Ihre Funktionsliste |
| lässt Benutzer eine OAuth-Anwendung autorisieren | `oauth/authorize` für die Rollen dieser Benutzer (nicht des Dienstkontos) |
| liest gesammelte Informationen | `infocollector/read` |
| führt Inhaltsjobs aus | `content/jobs`, dazu die Richtlinie der Operation selbst (`content/remove`, `content/move`, `content/hide` ...) |

Ein Anlegen über die REST-API oder `eZContentFunctions` veröffentlicht, wenn `content/create` es erlaubt, und der
Workflow für das Veröffentlichen läuft wie bei einem Redakteur. Das Argument `$notify = false` der Inhaltsfunktionen
braucht keine eigene Richtlinie; die Schaltfläche **Ohne Benachrichtigung veröffentlichen** des Editors dagegen schon
(`content/publish_without_notification`).

## Einschränkungen

| Einschränkung | Verwendung |
|---|---|
| **Subtree** (oder **Node**) | Nur unterhalb des Ordners der Integration, zum Beispiel `/Products` |
| **Class** | Nur die Klassen, die der Integration gehören (`product`, `product_category`) |
| **ParentClass** (beim Anlegen) | Nur unterhalb der richtigen Art von Container |
| **Section** | Nur Inhalte in der Sektion, die die Integration speist |
| **Language** | Nur die Sprachen, die sie importiert |
| **Mit Einschränkungen zuordnen** (Teilbaum oder Sektion bei der Zuweisung der Rolle) | Dieselbe Rolle, nur auf einen Teilbaum angewendet |

Mehrere Einschränkungen einer Richtlinie müssen alle erfüllt sein; mehrere Werte einer Einschränkung sind Alternativen.

## Einrichten

1. **Benutzer**: Legen Sie den Benutzer in der Gruppe "Integrationen" an (die Gruppe einmal anlegen, ohne eigene
   Rollen).
2. **Setup > Rollen und Richtlinien**: **Neue Rolle**, benannt nach der Integration ("ERP-Import").
3. Fügen Sie jede Richtlinie aus der Tabelle oben mit **Eingeschränkten Zugriff gewähren** und den Einschränkungen
   hinzu.
4. **Speichern**, dann die Rolle dem Dienstkonto **Zuweisen**.
5. Wenn die Integration einen API-Schlüssel verwendet: Melden Sie sich einmal als Dienstkonto an und erstellen Sie den
   Schlüssel ([Persönliche API-Schlüssel für Integrationen](persoenliche-api-schluessel-fuer-integrationen.md)).
6. Halten Sie die Rolle und ihren Zweck in der Schnittstellenspezifikation fest.

## Das Ergebnis prüfen

- Rufen Sie eine Route auf, die das Konto **nicht** erreichen darf (ein anderer Teilbaum, eine andere Klasse), und
  erwarten Sie `403`.
- Führen Sie den Probelauf des Imports als dieses Konto aus; eine fehlende Richtlinie zeigt sich als fehlgeschlagenes
  Anlegen.
- `php bin/php/accesscheck.php --help` zeigt, wie Sie in der Shell prüfen, was ein Benutzer mit einem Knoten oder
  Objekt tun darf und welche Einschränkung es verweigert.
- Das Audit-Protokoll zeigt Ablehnungen (`access.permission.refused`, `access.apikey.use.failed` mit dem Grund
  `policy` oder `permission`).

## Regelmäßig überprüfen

- [ ] Einmal im Jahr und nach jeder Änderung der Integration: Passt die Rolle noch zu dem, was die Integration tut?
- [ ] Schlüssel des Kontos: noch verwendet (**Zuletzt benutzt**), noch nötig, Enddatum geplant?
- [ ] Stillgelegte Integrationen: Konto deaktiviert, Schlüssel widerrufen, Rolle entfernt.
- [ ] Kein Dienstkonto hat "zum Testen" eine Administratorrolle erhalten.

## Verwandte Seiten

- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md)
- [Richtlinienfunktionen und Zugriff](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/richtlinienfunktionen-und-zugriff.md)
- [Zugriffskontrolle im Code](../ersteller/anwendungsentwickler/zugriffskontrolle-im-code.md)
- [Sicherheit für Integrationen](sicherheit-fuer-integrationen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
