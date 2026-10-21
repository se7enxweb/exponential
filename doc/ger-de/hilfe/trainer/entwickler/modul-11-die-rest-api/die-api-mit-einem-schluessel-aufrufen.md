---
title: "Lektion 11.2: Die API mit einem persönlichen Schlüssel aufrufen"
audience: trainer, developer
category: training
order: 112
summary: Die Teilnehmenden erlauben einem Testkonto mit der Richtlinie apikey/create API-Schlüssel, erzeugen einen Schlüssel mit den Scopes zum Lesen und Veröffentlichen, lesen Knoten und Kinder mit curl, legen über v2 einen Ordner an und entfernen ihn wieder und widerrufen den Schlüssel. 55 Minuten.
keywords: entwickler, REST, API-schlüssel, expk_, Bearer, apikey/create, scopes, Mein Konto, API-Zugang, curl, v1, v2, anlegen, löschen, OAuth2, token, widerrufen, 401, 403, developers course, API key, My account, API access, create, delete, revoke
type: help
---
# Lektion 11.2: Die API mit einem persönlichen Schlüssel aufrufen

Ein eigenes Skript spricht mit der REST-Schnittstelle über einen **persönlichen API-Schlüssel**: kein Passwort im
Skript, beschränkt auf die gewählten Scopes und niemals über die Rechte der Eigentümerin oder des Eigentümers hinaus.
In dieser Lektion erlauben die Teilnehmenden einem Testkonto Schlüssel, erzeugen einen Schlüssel, lesen und schreiben
Inhalte mit `curl` und widerrufen den Schlüssel wieder.

> **Hinweis**
> Verwenden Sie auf Ihrer Schulungsinstallation ein Testkonto mit einer Adresse unter `example.invalid`. Bewahren Sie
> den Schlüssel in einer Umgebungsvariablen auf, niemals in einer Datei, die Sie committen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 11: Die REST-API](README.md) |
| Dauer | 55 Minuten (15 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 11.1](die-rest-api-im-ueberblick.md); ein Administratorkonto und ein Testbenutzerkonto |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- einer Rolle `apikey/create` (mit einer Scope-Einschränkung) gewähren;
- unter **Mein Konto** > **API-Zugang** einen Schlüssel erzeugen und ihn einmalig kopieren;
- mit `curl` und `Authorization: Bearer` einen Knoten und seine Kinder unter v1 und v2 lesen;
- über v2 einen Ordner anlegen und entfernen und die Antworten 401, 403 und 404 lesen;
- erklären, wann OAuth2-Tokens besser passen als persönliche Schlüssel, und einen Schlüssel widerrufen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Persönliche API-Schlüssel](../../../../anleitungen/api-schluessel.md) und
[REST-API v1 und v2, Abschnitte 3 und 4](../../../../anleitungen/rest-api-v1-und-v2.md#3-authentifizierung).

1. **Schlüssel oder Tokens** (3 Minuten). Ein persönlicher Schlüssel ist für ein eigenes Skript des Benutzers; OAuth2
   ist für eine Anwendung, bei der sich andere Benutzer anmelden und die in ihrem Namen handelt (registriert unter
   **Setup** > **oAuth-Verwaltung**).
2. **Schlüssel einschalten** (3 Minuten). Schlüssel sind standardmäßig eingeschaltet (`rest.ini [ApiKeySettings]
   ApiKeys=enabled`), aber keine Rolle darf einen erzeugen, bevor sie die Richtlinie `apikey/create` hat, optional
   eingeschränkt durch `Scope`.
3. **Einen Schlüssel erzeugen** (3 Minuten). **Mein Konto** > **API-Zugang** (`/apikey/list`): ein Name, die Scopes,
   die Gültigkeit, **Schlüssel erstellen**. Der Schlüssel wird einmal angezeigt.
4. **Aufrufe** (4 Minuten). `curl -H "Authorization: Bearer $EXP_API_KEY" .../api/ezp/v1/content/node/2`;
   Schreibzugriffe unter v2.
5. **Antworten** (2 Minuten). 200 und 201 bei Erfolg; 401 ohne gültigen Schlüssel; 403 `access_denied`, wenn der
   Benutzer es nicht darf; 404 für einen unbekannten Knoten.

### Häufige Fragen

**Kann ein Schlüssel mehr als sein Eigentümer?** Niemals: Eine Anfrage läuft als Eigentümer des Schlüssels,
beschränkt auf die Scopes des Schlüssels und die Richtlinien des Eigentümers.

**Was, wenn der Schlüssel nach außen gelangt?** Widerrufen Sie ihn unter **API-Zugang** und erzeugen Sie einen neuen;
Schlüssel werden einmal angezeigt und nicht im Klartext gespeichert.

**Brauche ich HTTPS?** Auf jedem Rechner, den andere erreichen können, ja; `[Authentication] RequireHTTPS=enabled`
lehnt unverschlüsseltes HTTP ab.

### Stolperfallen

- **Schlüssel im Shell-Verlauf auf einem gemeinsam genutzten Rechner.** Verwenden Sie `read -s` oder eine
  Umgebungsdatei, die nicht committet wird.
- **Als Administrator testen.** Verwenden Sie ein normales Testkonto, damit sich die Berechtigungen wie bei einem
  echten Client verhalten.
- **Schreibzugriffe unter v1.** Dort gibt es sie nicht; verwenden Sie v2.

## Praktische Übung

Auf Ihrer Schulungsinstallation. Sie brauchen ein Administratorkonto und einen Testbenutzer (E-Mail unter
`example.invalid`).

1. Öffnen Sie als Administrator **Benutzer** > **Rollen und Richtlinien**, öffnen Sie die Rolle Ihres Testbenutzers
   (oder eine neue, ihm zugewiesene Rolle), **Bearbeiten**, **Neue Richtlinie**, Modul `apikey`, Funktion `create`;
   lassen Sie die Einschränkung leer oder wählen Sie **Scope**; **Speichern**.
2. Stellen Sie sicher, dass die Rolle `content/read` und `content/create` in Ihrem Kursordner erlaubt.
3. Melden Sie sich als Testbenutzer an der Website an, öffnen Sie **Mein Konto** und dann **API-Zugang**.
4. Nennen Sie unter **Neuen Schlüssel erstellen** den Schlüssel `Course script`, kreuzen Sie die Scopes zum Lesen und
   Veröffentlichen an, wählen Sie 7 Tage und klicken Sie auf **Schlüssel erstellen**. Kopieren Sie den Schlüssel.
5. In der Shell: `read -s EXP_API_KEY` (Schlüssel einfügen, Enter drücken) und `export EXP_API_KEY`.
6. Lesen Sie Knoten 2 unter v1 und unter v2:

   ```bash
   curl -s -H "Authorization: Bearer $EXP_API_KEY" http://127.0.0.1:8087/api/ezp/v1/content/node/2
   curl -s -H "Authorization: Bearer $EXP_API_KEY" "http://127.0.0.1:8087/api/ezp/v2/content/node/2?OutputFormat=xhtml"
   ```

7. Lesen Sie die ersten fünf Kinder mit ihren Feldern:
   `curl -s -H "Authorization: Bearer $EXP_API_KEY" "http://127.0.0.1:8087/api/ezp/v1/content/node/2/list/offset/0/limit/5?ResponseGroups=Fields"`.
8. Legen Sie in Ihrem Kursordner einen Ordner an (v2):

   ```bash
   curl -s -H "Authorization: Bearer $EXP_API_KEY" http://127.0.0.1:8087/api/ezp/v2/content/node/create \
        -d parentNodeID=<course folder node id> -d classIdentifier=folder -d languageLocale=eng-US -d name='From a script'
   ```

9. Versuchen Sie, einen Ordner unterhalb von Knoten 2 anzulegen, wo der Testbenutzer nichts anlegen darf, und lesen
   Sie die Antwort.
10. Entfernen Sie den Ordner aus Schritt 8 (v2): `curl -s -X DELETE -H "Authorization: Bearer $EXP_API_KEY" http://127.0.0.1:8087/api/ezp/v2/content/node/delete/<node id>`.
11. Widerrufen Sie unter **API-Zugang** den Schlüssel und wiederholen Sie dann Schritt 6.

## Erwartetes Ergebnis

- Schritt 6: JSON von Knoten 2; unter v2 mit gefülltem `renderedOutput`.
- Schritt 8: `{"message":"Created","objectId":...,"nodeId":...}` und der Ordner in der Administration.
- Schritt 9: `403` mit `access_denied`.
- Schritt 10: `{"message":"Removed",...}`.
- Schritt 11: `401` nach dem Widerruf.
- Der Trainer prüft, dass alle Teilnehmenden ihren Schlüssel widerrufen haben.

## Quiz

1. Welche Richtlinie erlaubt einem Benutzer, API-Schlüssel zu erzeugen?
2. Wie wird ein persönlicher Schlüssel gesendet?
   A. Als Cookie B. Als `Authorization: Bearer expk_...` C. Als Formularfeld `password` D. Im Pfad der URL
3. Ein Schlüssel mit dem Scope zum Veröffentlichen gehört einem Benutzer ohne `content/create` unterhalb von Knoten 2.
   Was antwortet ein Anlegen dort?
4. Wann sollte eine Anwendung OAuth2 statt persönlicher Schlüssel verwenden?
5. Was tun Sie, wenn ein Schlüssel nach außen gelangt ist?

<details>
<summary>Antworten</summary>

1. `apikey/create`, optional eingeschränkt durch `Scope`.
2. **B.** `Authorization: Bearer expk_...`.
3. `403 access_denied`: Ein Schlüssel geht niemals über die Richtlinien seines Eigentümers hinaus.
4. Wenn sich andere Benutzer bei ihr anmelden und sie in deren Namen handelt.
5. Ihn unter **API-Zugang** widerrufen und einen neuen erzeugen.

</details>

## Weiterführende Literatur

- [Persönliche API-Schlüssel](../../../../anleitungen/api-schluessel.md): Scopes, Audit, Grenzen und jede Einstellung.
- [REST-API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md): Routen, Authentifizierung, Konfiguration.
- [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md).

## Verwandte Seiten

- [Modul 11: Die REST-API](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
