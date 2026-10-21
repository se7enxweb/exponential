---
title: REST API v1 und v2 für Unternehmen
audience: businesses, app-creator, admin
category: business-solutions
order: 40
summary: Die REST API für Inhalte von Exponential 6.0.15 für Entscheider erklärt - was v1 und v2 tun, was sich für bestehende Apps geändert hat (nichts an ihren Antworten), die hinzugefügten Berechtigungsprüfungen, das veraltete ezprestapiprovider, bekannte Lücken und was Sie Ihren Administrator fragen sollten.
keywords: REST API, v1, v2, ezprestapi, ezprestapiprovider, veraltet, Berechtigungen, mobile App, Kompatibilität, Aktualisierung, bekannte Probleme, listAtom, Unternehmen, deprecated, permissions, mobile app, compatibility, upgrade, known issues, business
type: help
---
# REST API v1 und v2 für Unternehmen

Diese Seite ist für Führungskräfte und Produktverantwortliche, deren App, Partnerintegration oder Frontend die REST API
von Exponential bereits verwendet oder verwenden wird. Sie erklärt die beiden Versionen der API in einfacher Sprache,
was das Release 6.0.15 für bestehende Clients geändert und nicht geändert hat, welche Lücken bekannt sind und was Sie
die Person fragen sollten, die Ihre Installation betreibt. Die vollständige technische Anleitung ist
[REST API v1 und v2: eine Erweiterung](../../../anleitungen/rest-api-v1-und-v2.md).

## Inhalt

1. [Zwei Versionen, eine Erweiterung](#zwei-versionen-eine-erweiterung)
2. [Was jede Version kann](#was-jede-version-kann)
3. [Was 6.0.15 für bestehende Apps geändert hat](#was-6015-für-bestehende-apps-geändert-hat)
4. [Berechtigungen bei jedem Aufruf](#berechtigungen-bei-jedem-aufruf)
5. [Bekannte Lücken](#bekannte-lücken)
6. [Fragen an Ihren Administrator](#fragen-an-ihren-administrator)

## Zwei Versionen, eine Erweiterung

Seit Exponential 6.0.15 kommen beide Versionen der REST API für Inhalte aus **einer** Erweiterung, `ezprestapi` (1.2.6
oder neuer). Lesezugriffe antworten in v1 und v2 (`/api/ezp/v1/...`, `/api/ezp/v2/...`); Schreibzugriffe antworten nur
in v2 ([REST API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md#kurz-gesagt)).

Die ältere Erweiterung `ezprestapiprovider` ist **veraltet**. Ihr letztes Release, 6.0.4, ist eine
Kompatibilitätsschicht auf `ezprestapi`; Installationen werden gebeten, `ezprestapi` zu aktivieren und
`ezprestapiprovider` zu entfernen
([6. Migration von ezprestapiprovider](../../../anleitungen/rest-api-v1-und-v2.md#6-migration-von-ezprestapiprovider)).
Die `composer.json` im Wurzelverzeichnis von 6.0.15 verlangt `ezprestapi` und nicht mehr die alte Erweiterung.

## Was jede Version kann

Aus [2. Die Routen](../../../anleitungen/rest-api-v1-und-v2.md#2-die-routen):

| Aufgabe | v1 | v2 |
|---|---|---|
| Einen Eintrag lesen (Knoten oder Objekt), seine Felder oder ein Feld | ja | ja |
| Die Unterelemente eines Ordners seitenweise auflisten | ja | ja |
| Die Unterelemente zählen | ja | ja |
| Einen Eintrag anlegen und veröffentlichen | nein | ja |
| Einen Eintrag entfernen (mit allem darunter) | nein | ja |
| Einen Eintrag aktualisieren | nein | prüft die Bearbeitungsberechtigung und antwortet dann „not implemented yet“ (501) |

Für ein Unternehmen bedeutet das: Eine App, die Inhalte **zeigt**, funktioniert mit beiden Versionen; eine App oder
Integration, die Inhalte **hinzufügt** (aus einem Marktplatz importierte Bestellungen, Einträge aus einer App für den
Außendienst), verwendet v2; vorhandene Inhalte über die REST API zu ändern, ist noch nicht möglich. Für Aufgaben der
Administration über Inhalte hinaus bieten die Remote-Dienste mehr ([Entfernte Dienste](entfernte-dienste.md)).

## Was 6.0.15 für bestehende Apps geändert hat

Die Anleitung sagt: „Nothing changes for clients: the v1 and v2 answers are byte for byte the same as before, except
that `OutputFormat=xhtml` fills `renderedOutput` again.“ (Für Clients ändert sich nichts: Die Antworten von v1 und v2
sind Byte für Byte dieselben wie vorher, außer dass `OutputFormat=xhtml` `renderedOutput` wieder füllt.) Sie berichtet,
dass das gegen aufgezeichnete Antworten von 33 URLs unter Apache und unter Velocity geprüft wurde, vor und nach der
Änderung, einschließlich des Listenaufrufs einer bestehenden mobilen App
([7. Aktivierungskombinationen](../../../anleitungen/rest-api-v1-und-v2.md#7-aktivierungskombinationen-vorher-und-seitdem)).

Derselbe Abschnitt dokumentiert, was vorher bei manchen Kombinationen der beiden Erweiterungen schiefging:
v2-Adressen, die mit 404 antworteten, und v1-Lesezugriffe, die ohne Berechtigungsprüfung liefen. Mit den aktuellen
Releases verhält sich jede Kombination gleich.

> **Hinweis**
> Wenn Ihre App vor Jahren gegen v1 gebaut wurde, funktioniert sie weiter. Die Aktualisierung ist eine Aufgabe des
> Administrators auf dem Server, kein Release der App.

## Berechtigungen bei jedem Aufruf

„Every read and write checks the user's policies (`content/read`, `content/create`, `content/edit`,
`content/remove`), whatever the request was authenticated with“ (Jeder Lese- und Schreibzugriff prüft die Richtlinien
des Benutzers, womit auch immer die Anfrage authentifiziert wurde, dieselbe Anleitung). Eine Authentifizierung ist
standardmäßig vorgeschrieben. Zwei Arten von Zugangsdaten werden akzeptiert: OAuth2-Tokens für Apps, die für ihre
Benutzer handeln, und persönliche API-Schlüssel für die eigenen Skripte eines Benutzers
([Apps und API-Schlüssel](apps-und-api-schluessel.md)).

Für ein Unternehmen ist das die entscheidende Sicherheitseigenschaft: Eine App kann nie mehr sehen oder ändern, als das
Benutzerkonto dahinter darf, und eine öffentliche App sollte ein Konto verwenden, das nur lesen darf, was die
Öffentlichkeit lesen darf.

## Bekannte Lücken

| Lücke | Wirkung | Ausweg aus den Quellen |
|---|---|---|
| Die Atom-Listenroute `listAtom` | Antwortet in v1 und v2 mit 500, vor und nach der Änderung | Verwenden Sie die Route `list` oder den RSS-Export der Website ([Feeds und Syndication](feeds-und-syndication.md)) |
| Inhalte über REST aktualisieren | Antwortet nach der Berechtigungsprüfung mit 501 | Legen Sie einen neuen Eintrag an, oder verwenden Sie die Administration oder die Remote-Dienste |
| Apache mit FastCGI | Der Header `Authorization` muss PHP erreichen | Eine Aufgabe der Serverkonfiguration ([REST API für Anwendungen](../../../../eng-us/help/creators/app-creators/rest-api.md#configuration)) |

## Fragen an Ihren Administrator

- [ ] Ist `ezprestapi` 1.2.6 oder neuer aktiv und `ezprestapiprovider` aus den aktiven Erweiterungen entfernt?
- [ ] Antworten `/api/ezp/v1/content/node/2` und `/api/ezp/v2/content/node/2` mit einem gültigen Schlüssel mit `200`?
      (Die Prüfung der Anleitung nach der Migration.)
- [ ] Ist eine Authentifizierung vorgeschrieben (`RequireAuthentication=enabled`, der Standard), und wird reines HTTP
      abgelehnt (`RequireHTTPS`)?
- [ ] Wie lang ist die Lebensdauer der Tokens für OAuth-Apps (`TokenTTL`, standardmäßig eine Stunde)?
- [ ] Wurden nach einer Änderung an `rest.ini` der INI-Cache geleert, PHP-FPM neu geladen und Velocity neu gestartet,
      wie die Anleitung sagt?
- [ ] Welche Benutzerkonten verwenden unsere Apps, und was darf jedes davon?

## Verwandte Seiten

- [Headless mit der REST API](headless-mit-der-rest-api.md)
- [Apps und API-Schlüssel](apps-und-api-schluessel.md)
- [REST API v1 und v2: eine Erweiterung](../../../anleitungen/rest-api-v1-und-v2.md)
- [REST API v1 und v2 für Anwendungen](../../../../eng-us/help/creators/app-creators/rest-api.md)
- [ezprestapi](../../../../eng-us/features/6.0/extensions/ezprestapi.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
