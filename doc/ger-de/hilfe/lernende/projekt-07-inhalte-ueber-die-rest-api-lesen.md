---
title: "Projekt 7: Inhalte über die REST API lesen"
audience: student
category: learning
order: 31
summary: Lernprojekt 7 - prüfen, ob die Erweiterung ezprestapi aktiv ist, einem Testbenutzer das Recht geben, einen persönlichen API-Schlüssel mit dem Bereich read zu erstellen, einen Schlüssel erstellen, Knoten, Unterknoten und Felder mit curl über v1 und v2 lesen, Fehlerantworten behandeln, ein kleines Skript schreiben, das den Schlüssel aus dem Code heraushält, und Nachweise abgeben, bewertet mit einem Bewertungsraster.
keywords: lernprojekt, REST API, ezprestapi, persönlicher API-Schlüssel, expk, Bearer, curl, v1, v2, content/node, felder, bereiche, scopes, apikey/create, JSON, lernende, study project, personal API key, fields, learner
type: help
---
# Projekt 7: Inhalte über die REST API lesen

Exponential liefert seine Inhalte als JSON über eine REST API aus, die Apps und andere Systeme verwenden. In diesem
Projekt lesen Sie die Inhalte Ihrer Vereinswebsite auf der Kommandozeile mit einem persönlichen API-Schlüssel, so wie
es ein Skript oder eine mobile App tut. Die Schritte folgen [REST API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md)
und [Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md).

## Inhalt

1. [Auf einen Blick](#auf-einen-blick)
2. [Ziel](#ziel)
3. [Bevor Sie beginnen](#bevor-sie-beginnen)
4. [Aufgaben](#aufgaben)
5. [Abgabe](#abgabe)
6. [Bewertungsraster](#bewertungsraster)
7. [Zusatzziele](#zusatzziele)

## Auf einen Blick

| | |
|---|---|
| Stufe | Fortgeschritten |
| Zeit | Etwa 3 Stunden |
| Pfade | Entwickler |
| Voraussetzungen | [Projekt 2](projekt-02-eine-kleine-website-struktur.md); ein Terminal mit `curl`; [Projekt 9](projekt-09-rollen-fuer-einen-eingeschraenkten-redakteur.md) hilft bei Aufgabe 2 |

## Ziel

Ein Testbenutzer mit einem persönlichen API-Schlüssel nur zum Lesen, eine Reihe von curl-Aufrufen, die Ihren
Vereinsordner, seine Unterknoten und deren Felder über v1 und v2 lesen, eine Tabelle der Fehlerantworten, auf die Sie
gestoßen sind, und ein kurzes Skript, das den Schlüssel aus der Umgebung liest.

## Bevor Sie beginnen

Kennen Sie die Fakten aus den Anleitungen:

- Eine Erweiterung, `ezprestapi`, bedient beide Versionen. **Lesezugriffe** antworten unter `/api/ezp/v1/...` und
  `/api/ezp/v2/...`; **Schreibzugriffe** nur unter v2 ([REST API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md#kurz-gesagt)).
- Jeder Lese- und Schreibzugriff prüft die Richtlinien des Benutzers, unabhängig von der Anmeldeart.
- Ein persönlicher API-Schlüssel wird auf der Website erstellt, **einmal** angezeigt und als
  `Authorization: Bearer expk_...` gesendet. Schlüssel sind in jeder Installation eingeschaltet, aber niemand darf
  einen erstellen, bevor eine Rolle die Richtlinie `apikey/create` vergibt
  ([Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md#5-schlüssel-für-ihre-benutzer-einschalten)).

## Aufgaben

1. **Prüfen Sie die Erweiterung.** Öffnen Sie **Setup > Extensions** und bestätigen Sie, dass `ezprestapi` aktiv ist.
   Das Standard-Site-Paket aktiviert sie ([Erweiterungen, die sevenx_multisite aktiviert](../seitenpakete/sevenx-multisite/erweiterungen-die-sevenx-multisite-aktiviert.md)).
2. **Bereiten Sie einen Testbenutzer vor.** Legen Sie einen Benutzer „api-reader“ mit einer Adresse unter
   `example.invalid` an, in einer Gruppe, deren Rolle ihm die Anmeldung an der Website erlaubt (das Beispiel der
   Anleitung ist die Rolle Member). Geben Sie ihm eine Rolle mit `content/read` und `apikey/create`, eingeschränkt auf
   den **Scope** `read` ([Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md#5-schlüssel-für-ihre-benutzer-einschalten)).
   Verwenden Sie für die API-Arbeit nicht den Administrator: Ein Schlüssel geht nie über die Richtlinien seines
   Besitzers hinaus, also begrenzt ein eingeschränkter Besitzer den Schaden eines verlorenen Schlüssels.
3. **Erstellen Sie einen Schlüssel.** Melden Sie sich als Testbenutzer an, öffnen Sie **Mein Konto > API-Zugang**
   (`/apikey/list`), nennen Sie den Schlüssel „Lernprojekt 7“, setzen Sie nur das Häkchen beim Bereich read, wählen
   Sie 7 Tage und drücken Sie **Schlüssel erstellen**. Kopieren Sie ihn in eine Umgebungsvariable, nicht in eine
   Datei:

   ```bash
   export EXP_API_KEY='expk_...'
   ```

   Die Seite API-Zugang zeigt eine fertige `curl`-Zeile mit dem Pfad Ihrer Installation.
4. **Lesen Sie einen Knoten.** `curl -s -H "Authorization: Bearer $EXP_API_KEY" <your site>/api/ezp/v1/content/node/<node id>`,
   mit der Knotennummer Ihres Vereinsordners.
5. **Lesen Sie die Unterknoten** mit `/list/offset/0/limit/20?ResponseGroups=Fields`, dann ihre Anzahl mit
   `/childrenCount`, dann ein Feld eines Artikels mit `/content/object/<object id>/field/title`
   ([Die Routen](../../anleitungen/rest-api-v1-und-v2.md#2-die-routen)).
6. **Vergleichen Sie v1 und v2** für denselben Knoten und probieren Sie unter v2 `?OutputFormat=xhtml` aus.
7. **Begegnen Sie den Fehlern.** Rufen Sie ohne den Header auf, mit einem falschen Schlüssel, mit einem unbekannten
   Knoten, und versuchen Sie einen Schreibzugriff wie ein Anlegen unter v2. Halten Sie jeden Status und jede Antwort
   fest; der Schlüssel nur zum Lesen sollte beim Schreibzugriff abgewiesen werden.
8. **Schreiben Sie ein Skript** in Python oder PHP, das die Unterknoten des Vereinsordners liest und ihre Namen
   ausgibt und den Schlüssel aus `EXP_API_KEY` liest ([Einen Schlüssel mit curl verwenden](../../anleitungen/api-schluessel.md#3-einen-schlüssel-mit-curl-benutzen)
   enthält ein Beispiel in Python).
9. **Widerrufen Sie den Schlüssel** unter `/apikey/list`, wenn Sie fertig sind, und zeigen Sie, dass der nächste
   Aufruf abgewiesen wird.

## Abgabe

- Die Befehle, die Sie ausgeführt haben, mit dem Schlüssel ersetzt durch `expk_...`, und gekürzte Antworten.
- Eine Tabelle der Statuscodes, denen Sie begegnet sind, und was jeder bedeutet
  ([Einen Schlüssel mit curl verwenden](../../anleitungen/api-schluessel.md#3-einen-schlüssel-mit-curl-benutzen) listet sie auf).
- Das Skript, in Ihrem Git-Repository, ohne einen Schlüssel darin oder in seiner Historie.
- Ein kurzer Bericht: warum der Testbenutzer, warum nur der Bereich read und was sich zwischen v1 und v2 geändert hat.

## Bewertungsraster

Bewerten Sie die vier gemeinsamen Kriterien aus [Die eigene Arbeit bewerten](die-eigene-arbeit-bewerten.md) und diese
Prüfungen (8):

1. Es wurde gezeigt, dass `ezprestapi` aktiv ist.
2. Der Schlüssel gehört einem eingeschränkten Testbenutzer, nicht dem Administrator.
3. Der Schlüssel hat nur den Bereich read und eine kurze Laufzeit.
4. Knoten, Unterknoten, Anzahl der Unterknoten und ein Feld wurden gelesen.
5. Mindestens vier verschiedene Fehlerantworten sind dokumentiert.
6. Ein Schreibzugriff mit dem Schlüssel nur zum Lesen wurde abgewiesen, und der Bericht erklärt, warum.
7. Kein Schlüssel erscheint in der Abgabe, im Skript oder in der Historie des Repositorys.
8. Der Schlüssel wurde am Ende widerrufen.

## Zusatzziele

- Lesen Sie über OAuth2-Tokens für Anwendungen, die für andere Benutzer handeln
  ([Authentifizierung](../../anleitungen/rest-api-v1-und-v2.md#3-authentifizierung)), und erklären Sie, wann ein Schlüssel die
  bessere Wahl ist.
- Geben Sie einem zweiten Schlüssel den Bereich publish für einen eigenen Teilbaum, legen Sie unter v2 einen Ordner an
  und entfernen Sie ihn wieder.
- Folgen Sie dem Kurs [Entwickler](../trainer/entwickler/README.md) der Trainer, Modul 11, zur REST API.

## Verwandte Seiten

- [Lernprojekte: Überblick](lernprojekte-ueberblick.md)
- [REST API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md)
- [Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
