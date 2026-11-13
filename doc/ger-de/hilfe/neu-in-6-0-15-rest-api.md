---
title: "Neu in 6.0.15: Die REST API ändert Inhalte"
audience: developer, app-creator, mobile-app-developer, integrator, cms-developer, php-developer, admin, government, press
category: getting-started
order: 4
summary: Mit Exponential 6.0.15 und ezprestapi 1.2.9 legt die REST API Inhalte über den eigenen Content-Writer des Kernels an, ändert und entfernt sie - ein Ändern veröffentlicht eine neue Version, Werte kommen als Formularfelder oder JSON, Dateien von öffentlichen Adressen, und nxc_powercontent wird nicht mehr gebraucht. Was sich in ezprestapi 1.2.1 bis 1.2.9 geändert hat und wo Sie anfangen.
keywords: REST API, neu in 6.0.15, Neuigkeiten, Inhalte ändern, Ankündigung, Schnittstelle, REST API, new in 6.0.15, what's new, ezprestapi, 1.2.9, update content, PUT, JSON, expContentWriter, nxc_powercontent, v1, v2, permissions, API keys, OAuth2, headless, mobile app, integration, open data, announcement
type: help
---
# Neu in 6.0.15: Die REST API ändert Inhalte

Exponential 6.0.15 mit `ezprestapi` 1.2.9 schließt die Lücke, an die Integratoren und App-Entwickler am häufigsten
stießen: Die REST API kann Inhalte jetzt **ändern**, nicht nur anlegen und entfernen, und jeder Schreibzugriff läuft
über den Kernel selbst. Die Extension `nxc_powercontent`, die die REST-Schreibzugriffe bisher brauchten, ist nicht mehr
erforderlich.

## Was Sie am Ende haben

Einen Überblick, was die REST API seit 6.0.15 tut, was sich von Release zu Release geändert hat, und die Seite, die Sie
als Nächstes lesen.

## Was es bringt

- **Ändern über REST**: `POST` oder `PUT /api/ezp/v2/content/node/<Knoten-ID>` veröffentlicht eine neue Version des
  Objekts des Knotens mit den gesendeten Werten. Die anderen Attribute behalten ihre Werte, die alte Version bleibt in
  der Historie, und eine Sprache, die das Objekt noch nicht hat, fügt diese Übersetzung hinzu.
- **JSON-Bodys**: Anlegen und Ändern nehmen ein JSON-Objekt (`Content-Type: application/json`) ebenso wie
  Formularfelder.
- **Werte, wie eine Redakteurin sie angeben würde**: Rich Text als HTML, ein Bild oder eine Datei als öffentliche
  `https`-Adresse, alles andere in seiner Textform.
- **Der Kernel schreibt**: Anlegen, Ändern und Löschen laufen über [expContentWriter](../funktionen/6.0/inhalte-schreiben.md)
  des Kernels und die Operation `content/publish`, sodass Workflows (zum Beispiel eine Freigabe), Caches, der Suchindex
  und Benachrichtigungen wie bei einer Bearbeitung in der Administration laufen. Eine Version, die ein Workflow hält,
  wird mit `202` beantwortet.
- **Eine Extension weniger**: `nxc_powercontent` ist nicht mehr erforderlich; Composer entfernt sie beim Aktualisieren,
  wenn nichts anderes sie verlangt.
- **Sicherere Dateiwerte**: Eine Datei wird nur von einer öffentlichen Adresse heruntergeladen. Ein Pfad auf dem Server
  und eine Adresse in einem privaten Netz werden abgelehnt, sodass ein REST-Aufrufer den Server weder seine eigenen
  Dateien lesen noch interne Dienste erreichen lassen kann.
- **Berechtigungen bei jedem Aufruf** (seit 1.2.5): Lese- und Schreibzugriffe prüfen die Richtlinien des Benutzers,
  gleich, womit die Anfrage authentifiziert wurde.

Das Ändern in einem Aufruf:

```bash
curl -s -X PUT -H "Authorization: Bearer $EXP_API_KEY" -H "Content-Type: application/json" \
     -d '{"languageLocale":"eng-US","title":"Opening moved to eleven","intro":"<p>Doors open at <strong>eleven</strong>.</p>"}' \
     https://www.example.com/api/ezp/v2/content/node/<node id>
# {"message":"Updated","objectId":<object id>,"nodeId":<node id>,"version":<version>,"languageLocale":"eng-US"}
```

## Was sich geändert hat, Release für Release

| ezprestapi | Datum | Was sich geändert hat |
|---|---|---|
| 1.2.1 | 28. September 2026 | Die Extension nennt ihre Version, ihre Lizenz (GNU General Public License v2.0 oder später) und ihre Website in `extension.xml` und `ezinfo.php`; `info()` ist statisch. |
| 1.2.2 | 30. September 2026 | Die About-Seite nennt das Produkt Exponential. |
| 1.2.3, 1.2.4 | 2. Oktober 2026 | Die Kommandozeilenskripte, Cronjob-Teile und Modul-Views sind Klassen; die Befehle beschreiben sich selbst und starten über die gemeinsamen Befehlshelfer. |
| 1.2.5 | 6. Oktober 2026 | **Berechtigungsprüfungen bei jedem Lese- und Schreibzugriff**: `content/read`, `content/create` der Klasse unterhalb des Elternknotens in der Sprache, `content/edit`, `content/remove` jeder Platzierung; eine Ablehnung antwortet mit `403`. **Lesezugriffe antworten wieder unter v1**, für bestehende Clients wie mobile Apps. Anlegen antwortet mit `201` und `objectId` und `nodeId`; Löschen nimmt `DELETE` ebenso wie `POST` an und antwortet mit `200`. |
| 1.2.6 | 16. Oktober 2026 | **REST API v1 und v2 in einer Extension**: eine Aktivierung, ein Provider (`ezp7xRestApiProvider`) für die Präfixe `ezp` und `ezpl`, eine OAuth2-Einrichtung; `ezprestapiprovider` wird zu einem veralteten Kompatibilitäts-Shim. `OutputFormat=xhtml` rendert wieder. Die v1-Antworten bleiben Byte für Byte gleich. |
| 1.2.7 | 21. Oktober 2026 | Die Liste der Kinder (`/content/node/<id>/list/...`) antwortet wieder auf Installationen ohne `ezprestapiprovider`, also auf jeder neuen Installation; vorher antwortete sie mit `500`. |
| 1.2.8 | 29. Oktober 2026 | Ein Copyright-Vermerk, Exponential Foundation und 7x. |
| 1.2.9 | 12. November 2026 | **Ändern über REST v2**, JSON-Bodys, Schreibzugriffe über `expContentWriter` des Kernels, Dateiwerte nur von öffentlichen Adressen, `nxc_powercontent` nicht mehr erforderlich. |

Was der Kernel von 6.0.15 dazu beiträgt:

- den Berechtigungshelfer `expRestContentPermission`, den die Prüfungen von 1.2.5 verwenden, und Routen, die unter
  mehr als einer Version antworten (nötig für v1 und v2 in einem Provider);
- [persönliche API-Schlüssel](../anleitungen/api-schluessel.md) mit Scopes (`read`, `publish`, `edit`, `remove`);
- [die Härtung der OAuth-Tokens](../bc/6.0/oauth-token-haertung.md) und [PKCE](../bc/6.0/oauth-pkce-und-state.md);
- [CORS für die REST API](../bc/6.0/cors-fuer-die-rest-api.md), standardmäßig aus;
- ein `404` für eine Adresse, die keinen REST-Provider nennt, auf einer Installation ohne `ezprestapi`, wo sie vorher
  in einer Schleife fataler Fehler und einem `500` endete.

## Wo Sie anfangen

| Sie wollen | Lesen Sie |
|---|---|
| Die API aufrufen: Routen, Authentifizierung, curl-Beispiele | [REST API v1 und v2](../anleitungen/rest-api-v1-und-v2.md) |
| Inhalte über REST ändern: Antworten und Werte | [Ändern und die Werte eines Schreibzugriffs](../anleitungen/rest-api-v1-und-v2.md#21-ändern-und-die-werte-eines-schreibzugriffs) |
| Eine Installation aktualisieren und `nxc_powercontent` entfernen | [Hinweise zum Aktualisieren: die REST-Schreibzugriffe über den Kernel](../bc/6.0/rest-schreibzugriffe-ueber-den-kernel.md) |
| Inhalte aus eigenem PHP-Code auf dieselbe Weise schreiben | [Inhalte aus PHP schreiben mit expContentWriter](../anleitungen/inhalte-aus-php-schreiben.md) |
| Eine App darauf bauen | [Inhalte aus einer App anlegen und entfernen](mobile-app-entwickler/rest-api/inhalte-anlegen-und-entfernen.md) |
| Ein anderes System anbinden | [Die REST API für Integratoren](integratoren/rest-api-fuer-integratoren.md) |
| Einem Werkzeug einen Schlüssel mit nur den nötigen Rechten geben | [Persönliche API-Schlüssel](../anleitungen/api-schluessel.md) |
| Jede Route und Einstellung der Extension kennen | [ezprestapi](../funktionen/6.0/erweiterungen/ezprestapi.md) |

## Aktualisieren

Aktualisieren Sie die Extension mit `composer update se7enxweb/ezprestapi` zusammen mit dem Kernel 6.0.15, leeren Sie
die Caches und laden Sie PHP neu. Bestehende Clients arbeiten weiter: Die Routen, die Antworten der Lesezugriffe und die
Authentifizierung bleiben gleich. Zwei Dinge kann ein Client bemerken: Ein Dateiwert, der ein Pfad auf dem Server ist,
wird jetzt mit `400` abgelehnt, und ein Attributbezeichner, den die Klasse nicht hat, wird jetzt mit `400` abgelehnt,
statt übergangen zu werden. Einzelheiten:
[Hinweise zum Aktualisieren: die REST-Schreibzugriffe über den Kernel, nxc_powercontent nicht mehr nötig](../bc/6.0/rest-schreibzugriffe-ueber-den-kernel.md).

## Nächster Schritt

[REST API v1 und v2](../anleitungen/rest-api-v1-und-v2.md).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
