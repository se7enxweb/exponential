# Persönliche API-Schlüssel: über REST veröffentlichen, ohne Passwort

Diese Anleitung erklärt die persönlichen API-Schlüssel von Exponential 6.0.15: Ein angemeldeter Benutzer erstellt
auf der Site einen Schlüssel, ein Skript sendet ihn mit jeder REST-Anfrage, und die Anfrage läuft als dieser Benutzer,
beschränkt auf das, wofür der Schlüssel erstellt wurde. Sie behandelt das ganze Leben eines Schlüssels (erstellt,
benutzt, vermerkt, rotiert, widerrufen, abgelaufen), was ein Administrator einschaltet und sieht, und wie die
REST-Schicht aus einem Schlüssel einen Benutzer macht.

Sie richtet sich an drei Lesergruppen. **Benutzer der Site**, die einen Schlüssel für ein eigenes Skript möchten,
lesen die Abschnitte 1 bis 4. **Administratoren**, die entscheiden, wer Schlüssel haben darf, und diese im Blick
behalten, lesen die Abschnitte 5 bis 8. **Entwickler**, die auf der REST-Schnittstelle aufbauen oder sie erweitern,
lesen die Abschnitte 9 und 10. Sicherheitshinweise und Fehlerbehebung (Abschnitte 11 und 12) sind für alle.

Jeder Befehl und jede Antwort unten wurde am 5. Oktober 2026 auf dem Demonstrationsserver (Apache mit PHP-FPM auf
Port 443 und Velocity auf 8080) mit Wegwerfkonten ausgeführt, die danach entfernt wurden. Schlüssel, Adressen und
Hostnamen sind gekürzt oder durch Dokumentationswerte ersetzt (`example.com`, `203.0.113.0/24`).

[Anleitungen](README.md) · Siehe auch: [Remote-Dienste und Apps](entfernte-dienste-und-apps.md) (die `expservices`-Tokens) ·
[ezprestapi](../../eng-us/features/6.0/extensions/ezprestapi.md) (die REST-Routen für Inhalte) ·
[REST-API v1 und v2](rest-api-v1-und-v2.md) ·
[Sicherheit und Audit](sicherheit-und-audit.md) · Änderungshinweis: [doc/eng-us/bc/6.0/api-keys.md](../../eng-us/bc/6.0/api-keys.md)

## Kurz gesagt

- Ein Benutzer mit der Richtlinie `apikey/create` öffnet **Mein Konto > API-Zugang** (`/apikey/list`), benennt einen
  Schlüssel, kreuzt die Bereiche an, die er braucht, und wählt eine Laufzeit. Der Schlüssel,
  `expk_<12 Zeichen>_<40 Zeichen>`, wird **einmal** angezeigt.
- Ein Skript sendet ihn als `Authorization: Bearer expk_...`. Die Anfrage läuft als Eigentümer des Schlüssels,
  beschränkt auf die Bereiche des Schlüssels und nie über die eigenen Richtlinien des Eigentümers hinaus.
- Gespeichert wird nur ein HMAC-SHA-256-Hash des Geheimnisses, mit einem Salt je Schlüssel. Niemand kann einen
  Schlüssel erneut anzeigen, auch kein Administrator.
- Schlüssel enden, wenn sie widerrufen werden (vom Eigentümer oder von einem Administrator), wenn sie ablaufen oder
  wenn der Eigentümer deaktiviert oder entfernt wird. Ein Widerruf wirkt ab der nächsten Anfrage auf jedem Server.
- Administratoren sehen jeden Schlüssel unter **Setup > oAuth-Verwaltung > API-Schlüssel** (`/oauthadmin/keys`),
  durchsuchen und filtern sie, öffnen die Schlüssel eines Benutzers und widerrufen mit einer Bestätigung. Jedes
  Erstellen, jeder Widerruf, jede erste Benutzung und jede Ablehnung steht im Audit-Protokoll als `access.apikey.*`.
- Neu in 6.0.15: die Tabelle `expapikey`, das Modul `apikey` mit der Richtlinie `apikey/create`, der Block
  `[ApiKeySettings]` der `rest.ini`. Die HTTP-Basic-Authentifizierung der REST-Schnittstelle prüft jetzt jeden
  Passwort-Hash-Typ.

## Inhalt

1. [Was ein Schlüssel ist und was nicht](#1-was-ein-schlüssel-ist-und-was-nicht)
2. [Einen Schlüssel bekommen](#2-einen-schlüssel-bekommen)
3. [Einen Schlüssel mit curl benutzen](#3-einen-schlüssel-mit-curl-benutzen): [Routen](#31-routen-des-providers-ezp), [Berechtigungen](#32-berechtigungen-der-rest-schnittstelle)
4. [Ihre Schlüssel rotieren und widerrufen](#4-ihre-schlüssel-rotieren-und-widerrufen)
5. [Schlüssel für Ihre Benutzer einschalten](#5-schlüssel-für-ihre-benutzer-einschalten)
6. [Bereiche](#6-bereiche)
7. [Die Administration: jeder Schlüssel, ein Benutzer, Widerruf](#7-die-administration-jeder-schlüssel-ein-benutzer-widerruf)
8. [Audit und Grenzen](#8-audit-und-grenzen)
9. [Wie die REST-Schicht einen Schlüssel auflöst](#9-wie-die-rest-schicht-einen-schlüssel-auflöst)
10. [Das Datenmodell und das Upgrade](#10-das-datenmodell-und-das-upgrade)
11. [Sicherheitshinweise](#11-sicherheitshinweise)
12. [Fehlerbehebung](#12-fehlerbehebung)
13. [Jede Einstellung](#13-jede-einstellung)

---

## 1. Was ein Schlüssel ist und was nicht

| Ein persönlicher API-Schlüssel ist | Er ist nicht |
|---|---|
| Eine lange Zufallszeichenfolge, die ein Benutzer für eines seiner eigenen Programme erstellt: ein Import-Skript, einen CI-Job, ein Desktop-Werkzeug | Ein Passwort: Er kann sich weder an der Site noch an der Administration anmelden und fragt nie nach einem |
| Wird mit jeder REST-Anfrage im Header `Authorization` gesendet | Eine Session: Es gibt kein Cookie und kein Formular-Token |
| Für diese Anfrage der Benutzer, innerhalb der Bereiche, die beim Erstellen gewählt wurden | Mehr als der Benutzer: Ein Schlüssel bekommt nie ein Recht, das sein Eigentümer nicht hat |
| Einzeln widerrufbar, ohne das Passwort des Benutzers oder seine anderen Schlüssel anzurühren | Eine OAuth-Anwendung: Diese registriert ein Administrator für Apps, die *andere* Benutzer anmelden |

Es gibt drei Wege, wie ein Programm in Exponential hineinkommt. Wählen Sie den, der zur Aufgabe passt:

| Sie möchten | Verwenden Sie |
|---|---|
| Ein eigenes Skript liest oder veröffentlicht Inhalte über die REST-Schnittstelle (`/api/...`) | **Einen persönlichen API-Schlüssel** (diese Anleitung) |
| Ein Skript ruft die Remote-Dienste auf (`/expservices/...`): Lesezeichen, Sessions, Inhaltsaufträge | Ein `expservices`-Token, siehe [Remote-Dienste und Apps](entfernte-dienste-und-apps.md#5-ein-persönliches-api-token-für-skripte-und-apps-3-minuten) |
| Eine App oder Partner-Site lässt *viele* Benutzer sich anmelden und handelt für sie | Eine OAuth-Anwendung, registriert unter **Setup > oAuth-Verwaltung** ([Abschnitt 7](#7-die-administration-jeder-schlüssel-ein-benutzer-widerruf)) |

> [!NOTE]
> **Warum nicht einfach das Passwort?** Ein Passwort öffnet alles, was der Benutzer darf, auf jeder Seite, bis es
> geändert wird, und eine Änderung legt jedes Skript auf einmal lahm. Ein Schlüssel öffnet nur die REST-Schnittstelle,
> nur für seine Bereiche, endet an seinem eigenen Datum und wird einzeln widerrufen.

## 2. Einen Schlüssel bekommen

### 2.1 Sie brauchen ein Konto, das Schlüssel erstellen darf

Schlüssel gehören zu einem Konto. Wenn Sie keines haben, registrieren Sie sich zuerst über die normale Registrierung
der Site (`/user/register`) und aktivieren das Konto. Bitten Sie dann den Administrator der Site, Ihnen API-Schlüssel
zu erlauben, falls Ihr Konto dieses Recht noch nicht hat: In einer neuen Installation hat es keine Rolle
([Abschnitt 5](#5-schlüssel-für-ihre-benutzer-einschalten)).

Abgemeldet sagt die Seite für den API-Zugang das und bietet beide Wege hinein an:

```
$ curl -s https://example.com/apikey/list | grep -o 'Sign in to manage your keys\|Create an account'
Sign in to manage your keys
Create an account
```

### 2.2 Den Schlüssel erstellen

1. Melden Sie sich an der Site an und öffnen Sie **Mein Konto** (`/user/edit`). Die Liste der Konto-Links enthält
   **API-Zugang** (mit der Anzahl Ihrer Schlüssel), wenn Sie Schlüssel erstellen dürfen oder bereits welche haben.
   Seine Adresse ist `/apikey/list`.
2. Geben Sie ihm unter **Neuen Schlüssel erstellen** einen Namen, der sagt, wo er benutzt wird („Import-Skript der
   Redaktion“). An diesem Namen erkennen Sie ihn später wieder.
3. Kreuzen Sie nur die **Bereiche** an, die das Programm braucht. Die Seite bietet nur die Bereiche an, die Ihre
   eigenen Rechte erlauben: Jeder nennt die Berechtigung, die er braucht („Braucht Ihre Berechtigung content/read“).
4. Wählen Sie, wie lange er gültig bleibt: standardmäßig 7, 30, 90 oder 365 Tage. Ohne Enddatum nur dort, wo die Site
   es erlaubt.
5. Klicken Sie auf **Schlüssel erstellen**.

Die nächste Seite zeigt den Schlüssel in einem grünen Kasten mit **Schlüssel kopieren** und einer fertigen
`curl`-Zeile:

```
Your new key "Newsroom import script"
Copy it now. It is shown this once and is not stored anywhere: if you lose it, revoke it and make a new one.
expk_du8hrkekh7wy_... (here shortened: the page shows all 58 characters)
```

> [!NOTE]
> **Einmal angezeigt heißt einmal.** Die Seite, die den Schlüssel zeigt, wird mit `Cache-Control: no-store` gesendet,
> sodass weder der Browser noch ein Proxy sie behält. Neu laden erzeugt keinen zweiten Schlüssel; die Seite sagt, dass
> das Formular bereits gesendet wurde. Geht der Schlüssel verloren, widerrufen Sie ihn
> ([Abschnitt 4](#4-ihre-schlüssel-rotieren-und-widerrufen)) und erstellen einen neuen.

### 2.3 Was die Liste zeigt

Jeden Schlüssel, den Sie haben, die neuesten funktionierenden zuerst, mit seinem Namen, seinen ersten Zeichen
(`expk_du8hrkekh7wy_…`), seinen Bereichen, wann er erstellt wurde, wann und von welcher Adresse er zuletzt benutzt
wurde und wann er endet. Ein Schlüssel zeigt **Aktiv**, **Läuft bald ab** (innerhalb von sieben Tagen),
**Abgelaufen** oder **Widerrufen**.

## 3. Einen Schlüssel mit curl benutzen

Senden Sie den Schlüssel im Header `Authorization` mit dem Schema `Bearer`. Die Beispiele lesen Knoten 2 über den
Inhalts-Provider `ezp`. Die Extension `ezprestapi` bedient beide Versionen: Lesezugriffe antworten unter Version 1 und
Version 2 (`/api/ezp/v1/...` und `/api/ezp/v2/...`), Schreibzugriffe unter Version 2. Das veraltete
`ezprestapiprovider` 6.0.4 ist eine Zwischenschicht, die auf denselben Provider abbildet
([REST-API v1 und v2](rest-api-v1-und-v2.md)). Die Seite für den API-Zugang zeigt in ihrem Beispiel den Pfad Ihrer
Installation.

```bash
export EXP_API_KEY='expk_...'
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/2
```

```
HTTP/2 200
{"metadata":{"objectName":"Websites","classIdentifier":"folder","datePublished":1033917596,
 "dateModified":1791063800,"objectId":1,"nodeId":2,"fullUrl":"https:\/\/example.com\/websites"},"links":{...}}
```

Dieselbe Anfrage über Velocity (Port 8080 auf dem Referenzserver) liefert dieselbe Antwort. Die Antworten, die ein
Skript behandeln muss:

| Status | Rumpf | Bedeutung |
|---|---|---|
| 200 | der Inhalt | der Schlüssel ist gültig und hat den Bereich, den die Route braucht |
| 201 | `{"message":"Created","objectId":...,"nodeId":...}` | ein Anlegen wurde veröffentlicht |
| 400 | `{"error":"invalid_request","error_message":...}` | ein Feld eines Schreibzugriffs fehlt oder nennt eine Klasse oder Sprache, die es nicht gibt |
| 403 | `{"error":"access_denied","error_message":...}` | der Eigentümer darf diesen Inhalt nicht lesen oder schreiben (content/read, create, edit, remove; [3.2](#32-berechtigungen-der-rest-schnittstelle)) |
| 404 | `{"error":"not_found",...}` | der Knoten oder das Objekt existiert nicht |
| 401 | `{"error":"invalid_token"}` | kein Schlüssel, ein unbekannter Schlüssel, ein falsches Geheimnis, ein widerrufener Schlüssel, ein deaktivierter Eigentümer oder ein in der Adresse gesendeter Schlüssel |
| 401 | `{"error":"expired_token"}` | der Schlüssel hat sein Enddatum überschritten |
| 403 | `{"error":"insufficient_scope"}` | dem Schlüssel fehlt der Bereich, den diese Route braucht, oder sein Eigentümer hat die Berechtigung dahinter verloren |
| 429 | `{"error":"rate_limited",...}` mit `Retry-After` | der Schlüssel hat in dieser Minute mehr Anfragen gesendet, als die Site erlaubt |

Ein Schlüssel, der irgendwo anders als im Header gesendet wird, wird abgelehnt, auch ein gültiger:

```
$ curl -s -D - -o /dev/null "https://example.com/api/ezp/v2/content/node/2?oauth_token=$EXP_API_KEY" | grep -i '^HTTP\|www-auth'
HTTP/2 401
www-authenticate: OAuth realm='Exponential REST', error='invalid_token'
```

Aus Python, nur mit der Standardbibliothek:

```python
import json, os, urllib.request

request = urllib.request.Request(
    'https://example.com/api/ezp/v2/content/node/2',
    headers={'Authorization': 'Bearer ' + os.environ['EXP_API_KEY']})
with urllib.request.urlopen(request) as answer:
    print(json.load(answer)['metadata']['objectName'])
```

### 3.1 Routen des Providers ezp

Die Extension `ezprestapi` bedient den Provider `ezp`. Lesezugriffe antworten unter `v1` und `v2`; `v1` ist die API,
die bestehende Clients aufrufen (eine mobile App liest
`/api/ezp/v1/content/node/<id>/list/offset/0/limit/20?ResponseGroups=Fields`), und die Links in ihren Antworten
bleiben in `v1`. Schreibzugriffe antworten nur unter `v2`. Die vollständige Routentabelle, OAuth2 und die
Konfiguration stehen in [REST-API v1 und v2: eine Extension](rest-api-v1-und-v2.md).

| Methode | Pfad unter `/api/ezp/v1` und `/api/ezp/v2` | Was er tut |
|---|---|---|
| `GET` | `/content/node/<id>` und `/content/object/<id>` | ein Knoten oder Objekt (`ResponseGroups=Metadata,Locations,Fields`) |
| `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc\|desc))` | die Kinder |
| `GET` | `/content/node/<id>/childrenCount`, `.../fields`, `.../field/<identifier>`, `.../listAtom` | Anzahlen, Felder, Atom (`listAtom` antwortet mit 500, ein [bekanntes Problem](rest-api-v1-und-v2.md#8-bekanntes-problem-listatom)) |
| `POST` | `/content/node/create` (v2) | unter `parentNodeID` anlegen und veröffentlichen |
| `DELETE` oder `POST` | `/content/node/delete/<id>` (v2) | das Objekt des Knotens mit allen seinen Orten und allem darunter entfernen |
| `POST` | `/content/node/<id>` (v2) | aktualisieren: prüft content/edit und antwortet dann mit 501, Aktualisieren ist noch nicht implementiert |

```bash
# create a folder below node 1234 (fields: parentNodeID, classIdentifier, languageLocale, then the attributes)
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/create \
     -d parentNodeID=1234 -d classIdentifier=folder -d languageLocale=eng-US -d name='From a script'
{"message":"Created","objectId":5678,"nodeId":4321}

# remove it again
curl -s -X DELETE -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/delete/4321
{"message":"Removed","nodeId":4321,"objectId":5678}
```

Eine Route, deren Pfad passt, die aber die Methode nicht annimmt, beendet die Suche nicht mehr: Der Router probiert
die Routen danach und antwortet nur dann mit `405` und einem Header `Allow`, wenn keine die Methode annimmt. Ein
abgelehnter Schreibzugriff antwortet mit `401` oder `403`, nie mit `405` (vor 6.0.15 wurden die
Authentifizierungsantworten eines `POST` oder `DELETE` zu `405`).

### 3.2 Berechtigungen der REST-Schnittstelle

Jeder Lese- und Schreibzugriff des Inhalts-Controllers von `ezprestapi` fragt die Richtlinien des aktuellen Benutzers
so ab, wie das Inhaltsmodul sie abfragt, unabhängig davon, womit die Anfrage authentifiziert wurde: ein OAuth-Token,
ein persönlicher API-Schlüssel, HTTP-Basic-Authentifizierung oder der anonyme Benutzer, wenn die Authentifizierung
ausgeschaltet ist. Das erledigt ein einziger Helfer des Kernels, `expRestContentPermission`
(`kernel/private/rest/classes/auth/content_permission.php`), und die Schlüsselprüfung aus
[Abschnitt 6](#6-bereiche) fragt denselben Helfer, sodass ein Schlüssel und ein Token dieselbe Antwort bekommen.

| Aufruf | Richtlinie | Geprüft wie |
|---|---|---|
| einen Knoten, ein Objekt, eine Liste, eine Anzahl lesen | `content/read` des Knotens oder Objekts (bei einer Liste auch seines Elternknotens; die Kinder filtert der Fetch) | `canRead()` |
| anlegen | `content/create` der Klasse unter dem Elternknoten, in der Sprache: Limitierungen Class, ParentClass, Section, Node, Subtree und Language | `checkAccess( 'create', ... )` des Elternobjekts, so wie `content/action` es für **Neu** tut |
| aktualisieren | `content/edit` des Knotens (und `user/selfedit` für den eigenen Benutzer), in der gesendeten Sprache | `canEdit()` |
| entfernen | `content/remove` jedes Orts des Objekts und von allem darunter | `canRemove()` jedes Orts und `can_remove_all` von `subtreeRemovalInformation()`, wie `content/removeobject` |

Eine Ablehnung antwortet mit `403` und `{"error":"access_denied","error_message":"You may not create 'article' below node 60
in eng-US (content/create)."}` und ändert nichts. Das Inhaltsmodul hat keine Prüfung `content/publish`, und die
REST-Schnittstelle auch nicht: Ein Anlegen veröffentlicht, wenn `content/create` es erlaubt, und der
Veröffentlichungs-Workflow läuft wie beim Inhaltsmodul.

Der Antwort-Cache der REST-Schnittstelle (`rest.ini [CacheSettings] ApplicationCache`) hält eine Antwort je Benutzer
und nur für `GET` und `HEAD`: Vor 6.0.15 wurde der Lesezugriff eines Benutzers zehn Minuten lang dem nächsten
ausgeliefert, an `content/read` vorbei, und ein wiederholtes Anlegen oder Löschen wurde aus dem Cache beantwortet,
ohne zu laufen. Eine Ablehnung wird nie gecacht (sie folgt den Rechten jeder Anfrage), und eine Cache-Datei, die sich
nicht zurücklesen lässt, wird neu erzeugt, statt mit 500 zu antworten.

> [!NOTE]
> **Halten Sie den Schlüssel aus dem Code heraus.** Legen Sie ihn in eine Umgebungsvariable, den Geheimnisspeicher
> Ihres CI oder den Schlüsselbund Ihres Systems, nie in ein Repository, ein gemeinsam genutztes Skript oder eine URL.
> Jeder Schlüssel beginnt mit `expk_`, sodass man einem Secret-Scanner beibringen kann, danach zu suchen.

## 4. Ihre Schlüssel rotieren und widerrufen

**Rotieren** heißt, den neuen Schlüssel zu erstellen, bevor der alte endet: Erstellen Sie einen zweiten Schlüssel mit
denselben Bereichen, setzen Sie ihn in das Programm ein, prüfen Sie, dass das Programm funktioniert (bei dem neuen
Schlüssel füllt sich **Zuletzt benutzt**), und widerrufen Sie dann den alten. Zwei Schlüssel überlappen so lange, wie
der Wechsel dauert; dazwischen hält nichts an.

**Widerrufen** beendet einen Schlüssel sofort: Klicken Sie daneben auf **Widerrufen** und dann bei der folgenden
Frage auf **Schlüssel widerrufen**. Jede Anfrage damit wird ab der nächsten abgelehnt, auf jedem Server. Ein
widerrufener Schlüssel bleibt in Ihrer Liste, markiert als **Widerrufen** mit der Uhrzeit, damit Sie sehen, was er
war. Er kann nicht wieder gültig gemacht werden.

Widerrufen Sie einen Schlüssel sofort, wenn:

- ihn jemand anderes gesehen haben könnte (in einen Chat eingefügt, committet, in einem Log ausgegeben);
- das Programm, für das er war, ausgemustert wird;
- der Laptop oder Server, auf dem er lag, verloren geht oder weitergegeben wird.

Auch ein Administrator kann Ihre Schlüssel widerrufen; Sie sehen sie dann in Ihrer Liste als widerrufen.

## 5. Schlüssel für Ihre Benutzer einschalten

Schlüssel sind in jeder Installation eingeschaltet (`rest.ini [ApiKeySettings] ApiKeys=enabled`), aber niemand darf
einen erstellen, bevor eine Rolle die Richtlinie vergibt:

| Richtlinie | Gewährt | Limitierung |
|---|---|---|
| `apikey/create` | Schlüssel auf `/apikey/list` erstellen | `Scope`: welche Bereiche ein Schlüssel bekommen darf (zum Beispiel nur `read`) |

Die eigenen Schlüssel zu sehen und zu widerrufen braucht keine Richtlinie: Jeder angemeldete Benutzer darf das, sodass
ein Benutzer, dem das Recht entzogen wurde, trotzdem aufräumen kann. Anonyme Besucher können nie Schlüssel erstellen,
was auch immer ihre Rolle sagt.

So lassen Sie Ihre Mitglieder über die API veröffentlichen:

1. **Setup > Rollen und Richtlinien**: Öffnen Sie die Rolle, die Ihre Mitglieder haben (Member in einer
   Standardinstallation), oder legen Sie eine Rolle „API-Publisher“ an.
2. **Neue Richtlinie**, Modul **apikey**, Funktion **create**. Lassen Sie die Limitierung leer, um jeden Bereich zu
   erlauben, oder wählen Sie **Scope** und die Bereiche, die sie einem Schlüssel geben dürfen.
3. Stellen Sie sicher, dass die Rolle auch das hat, wofür die Bereiche stehen: `content/read` für read,
   `content/create` für publish, `content/edit` für edit, `content/remove` für remove
   ([Abschnitt 6](#6-bereiche)). Ein Bereich, dessen Berechtigung dem Benutzer fehlt, wird nicht angeboten.
4. Weisen Sie die Rolle den Benutzern oder der Gruppe zu.

Der Link **API-Zugang** erscheint bei der nächsten Seitenansicht in ihrem Profil. Um Schlüssel für die ganze Site
auszuschalten, setzen Sie `ApiKeys=disabled`: Dann wird kein Schlüssel mehr angenommen und keiner kann erstellt
werden; die Seiten sagen das.

> [!NOTE]
> **Mitglieder einer öffentlichen Site.** Selbstregistrierung plus `apikey/create` für Members bedeutet, dass jeder,
> der sich registriert, einen Schlüssel erstellen kann. Für das Lesen ist das in Ordnung, und eine Community-Site
> will genau das. Für das Veröffentlichen vergeben Sie `apikey/create` mit dem Bereich `publish` an eine eigene
> Rolle, die Sie von Hand zuweisen, und halten `content/create` auf die Teilbäume beschränkt, in die diese Benutzer
> schreiben dürfen.

## 6. Bereiche

Ein Bereich (Scope) ist das, wofür ein Schlüssel benutzt werden darf. Jeder steht für eine Richtlinie des
Eigentümers, benannt in der `rest.ini`:

```ini
[ApiKeySettings]
Scopes[read]=content/read
Scopes[publish]=content/create
Scopes[edit]=content/edit
Scopes[remove]=content/remove
ScopeNames[read]=Read content
ScopeNames[publish]=Create and publish content
```

Eine Route der REST-Schnittstelle braucht einen Bereich:

```ini
DefaultReadScope=read
RouteScopes[ezp7xRestContentController_CreateContentNode]=publish
RouteScopes[ezp7xRestContentController_UpdateContentNode]=edit
RouteScopes[ezp7xRestContentController_DeleteContentNode]=remove
```

Eine `GET`- oder `HEAD`-Route, die nicht aufgeführt ist, braucht `DefaultReadScope`. Jede andere nicht aufgeführte
Route wird für Schlüssel abgelehnt, sodass eine neue schreibende Route einer Extension für Schlüssel gesperrt ist, bis
ihr Bereich benannt ist.

Drei Prüfungen stehen bei jeder Anfrage zwischen einem Schlüssel und einer Route:

1. Der Schlüssel hat den Bereich, den die Route braucht.
2. Der Eigentümer hat die Richtlinie des Bereichs noch (`hasAccessTo()` ist nicht `no`): Ein Mitglied, das
   `content/create` verliert, verliert im selben Moment den Bereich publish jedes seiner Schlüssel.
3. Bei einer schreibenden Route, die in `RouteGuards[]` aufgeführt ist, die Rechte des Eigentümers an dem Knoten, auf
   den sie schreibt: `create` prüft `content/create` des gesendeten `classIdentifier` unter der gesendeten
   `parentNodeID` (die Limitierungen Node, Subtree, Class und Section gelten), `edit` und `remove` prüfen den Knoten
   der Route.

Die Anfrage läuft dann als Eigentümer, sodass auch jede Richtlinienprüfung des Controllers gilt.

> [!NOTE]
> **Warum die dritte Prüfung.** Vor `ezprestapi` 1.2.5 hat sein Inhalts-Controller über `nxc_powercontent` angelegt
> und entfernt, ohne das Berechtigungssystem zu fragen. Jetzt prüft er jeden Aufruf bei jeder Art der
> Authentifizierung ([3.2](#32-berechtigungen-der-rest-schnittstelle)); die Schlüsselprüfung bleibt als zweite Linie und
> fragt denselben Helfer, sodass die beiden nie uneins sind. Nur eine Ablehnung aufgrund der Rechte stoppt einen
> Schlüssel hier (`403 insufficient_scope`, aufgezeichnet als `access.apikey.use.failed` mit dem Grund `permission`);
> ein Schreibzugriff, der keinen oder einen unbekannten Knoten nennt, geht weiter an den Controller, der mit `400`
> oder `404` antwortet, wie er es bei einem Token tut.

## 7. Die Administration: jeder Schlüssel, ein Benutzer, Widerruf

**Setup > oAuth-Verwaltung** (`/oauthadmin/list`) hat zwei Reiter.

**Anwendungen** sind die OAuth-Clients. Jede Karte zeigt die Client-Kennung, den Eigentümer, die letzte Änderung,
den Endpunkt, wie viele Benutzer sie autorisiert haben und wie viele ihrer Tokens noch gültig sind. **Neue Anwendung**
legt eine an; ihre Kennung und ihr Geheimnis sind zufällig (`random_bytes()`) und werden beim ersten Speichern
erzeugt. Das Geheimnis ist auf der Seite der Anwendung eingeklappt, bis **Das Geheimnis zeigen** gedrückt wird. Das
Entfernen einer Anwendung entfernt auch ihre Autorisierungen, Tokens und Codes. Alle Adressen, Formularfelder und
Schaltflächen der früheren Seiten sind unverändert.

**API-Schlüssel** (`/oauthadmin/keys`) listet jeden Schlüssel jedes Benutzers auf:

- Kennzahlen: aktiv, innerhalb von sieben Tagen ablaufend, aktiv, aber nie benutzt, abgelaufen, widerrufen (jede
  ein Filter);
- eine Suche über Schlüsselnamen, Präfixe, Logins und E-Mail-Adressen sowie ein Statusfilter;
- je Schlüssel: Name und Präfix, Eigentümer (der Link öffnet die Schlüssel dieses Benutzers), Bereiche, letzte
  Benutzung und Adresse, Enddatum, Status;
- **Widerrufen** in einer Zeile, oder mehrere ankreuzen und **Ausgewählte widerrufen**; eine Bestätigungsseite nennt
  die Schlüssel, bevor irgendetwas geschieht.

Die Schlüssel eines Benutzers stehen unter `/oauthadmin/keys/(user)/<user id>`. Die Einstellungsseite des Benutzers
in der Administration (**Benutzer > der Benutzer > Einstellungen**, `/user/setting/<id>`) zeigt, wie viele Schlüssel
der Benutzer hat, und verlinkt dorthin.

Wer diese Seiten sieht: jeder mit `oauthadmin/*` (die Rolle Administrator). Die Kennzahlen auf der Einstellungsseite
eines Benutzers werden nur solchen Benutzern angezeigt.

## 8. Audit und Grenzen

Vier Ereignisse des Audit-Kanals `access`, standardmäßig eingeschaltet ([Audit](../../eng-us/bc/6.0/audit.md#8-event-reference)):

| Ereignis | Wann | Zeichnet auf |
|---|---|---|
| `access.apikey.create` | ein Schlüssel wird erstellt | die ID, den Namen, das Präfix und den Eigentümer des Schlüssels; seine Bereiche und sein Enddatum |
| `access.apikey.revoke` | ein Schlüssel wird widerrufen | dasselbe; wer ihn widerrufen hat (`by`: Eigentümer oder Administrator) |
| `access.apikey.use` | ein Schlüssel wird zum ersten Mal benutzt | den Schlüssel; seine Bereiche |
| `access.apikey.use.failed` | ein Schlüssel wird abgelehnt | den Grund: `malformed`, `unknown`, `secret`, `revoked`, `expired`, `owner`, `transport`, `scope`, `policy`, `permission`, `rate_limited`; bei einer Ablehnung wegen des Bereichs die Route |

Nie aufgezeichnet werden das Geheimnis, sein Hash oder sein Salt. Ein unbekannter Schlüssel wird mit dem Präfix
aufgezeichnet, das er vorgibt. Ein Schlüssel über seiner Ratenbegrenzung wird einmal pro Minute aufgezeichnet, nicht
einmal pro abgelehnter Anfrage.

```
$ ./console exp:audit tail --channel=access --name='access.apikey.*' --lines=3 --allow-root-user
2026-10-06 05:34:31.613  access   access.apikey.use.failed   anonymous(10)          apikey 13 Live test second     refused  r-01M47V9N5X...
2026-10-06 05:40:34.614  access   access.apikey.use          apishotsdaf3ad0b(30320) apikey 18 Throwaway probe key  success  r-01M47VMQNP...
2026-10-06 05:40:34.861  access   access.apikey.use.failed   anonymous(10)          -                              refused  r-01M47VMQXD...
```

Die erste Zeile ist ein widerrufener Schlüssel, der erneut versucht wurde (der Akteur ist anonym: Ein abgelehnter
Schlüssel meldet niemanden an). Die zweite ist die erste Benutzung eines Schlüssels, aufgezeichnet für seinen
Eigentümer. Die dritte ist derselbe Schlüssel, in der Adresse gesendet: abgelehnt vor jeder Suche, daher nennt sie
keinen Schlüssel (Grund `transport`). Gekürzt: die Anfrage- und Ereignis-IDs.

**Letzte Benutzung.** Die letzte Benutzung und die Adresse eines Schlüssels werden höchstens einmal pro Minute
geschrieben (`LastUsedInterval`), sodass ein viel benutzter Schlüssel keinen Datenbank-Schreibzugriff pro Anfrage
kostet. Die Adresse ist die des Clients, über vertrauenswürdige Proxys hinweg (`eZSys::clientIP()`, siehe
[vertrauenswürdige Proxys](../../eng-us/bc/6.0/trusted-proxies.md)).

**Grenzen.**

| Einstellung | Standard | Wirkung |
|---|---|---|
| `MaxKeysPerUser` | 10 | noch funktionierende Schlüssel je Benutzer; 0 = keine Grenze |
| `MaxExpiryDays` | 365 | die längste Laufzeit; 0 = Schlüssel ohne Enddatum sind erlaubt |
| `RateLimitPerMinute` | 120 | Anfragen je Schlüssel und Minute; 0 = keine Grenze. Gezählt in APCu, je PHP-FPM-Pool oder je Velocity-Server; ohne APCu wird sie nicht durchgesetzt, und die Seite der API-Schlüssel sagt das |

## 9. Wie die REST-Schicht einen Schlüssel auflöst

Für Entwickler. Die Klassen stehen im Autoload-Array des Kernels.

1. `ezpRestAuthConfiguration::filter()` (`kernel/private/rest/classes/auth/auth_configuration.php`) beginnt jede
   Anfrage mit `expApiKeyRest::reset()`: Ein persistenter Worker (Velocity) behält Statics, und der Schlüssel der
   vorherigen Anfrage darf nicht dazugehören.
2. Der Authentifizierungsstil fragt `expApiKeyRest::authentication()`. Beide Stile des Kernels tun das:
   `ezpRestOauthAuthenticationStyle` (der Standard, `AuthenticationStyle` in der `rest.ini`) und
   `ezpRestBasicAuthStyle`. Wenn der Header `Authorization` `Bearer expk_...` (oder `OAuth expk_...`) lautet, ist die
   Antwort eine `ezcAuthentication` mit `expApiKeyAuthFilter`. Ein Schlüssel in `oauth_token`, `access_token` oder
   `api_key` der Query oder des Rumpfs wird ohne Suche abgelehnt. Alles andere geht wie bisher an den eigenen Filter
   des Stils.
3. `expApiKeyAuthFilter::run()` zerlegt den Schlüssel (`expk_` + 12 `[a-z0-9]` + `_` + 40 `[A-Za-z0-9]`), lädt die
   Zeile anhand ihres Präfixes, vergleicht das Geheimnis mit `hash_equals()`, prüft Status, Eigentümer, Sperre wegen
   fehlgeschlagener Anmeldungen und Ratenbegrenzung, vermerkt die Benutzung und zeichnet die erste auf. Seine Status
   sind die von `ezpOauthFilter`, sodass die Antworten der REST-Schicht (401 `invalid_token`, `expired_token`)
   dieselben sind wie bei OAuth-Tokens.
4. Der Stil gibt den Eigentümer zurück; `filter()` meldet die Anfrage als dieser Benutzer an.
5. `filter()` ruft dann `expApiKeyRest::authorize()` mit den Routing-Informationen auf: Bereich, Richtlinie, Prüfung
   ([Abschnitt 6](#6-bereiche)). Eine Ablehnung ist eine interne Weiterleitung auf `/auth/oauth/login` mit
   `insufficient_scope`: 403.

```php
// A route of your own extension, open to keys with the publish scope:
// extension/myext/settings/rest.ini.append.php
// [ApiKeySettings]
// RouteScopes[myExtRestController_doImport]=publish
```

Schlüsselwerte, die keine Datenbank brauchen, sind statisch und bekommen ihre Einstellungen als Argumente, sodass sie
leicht zu testen sind: `expApiKey::newToken()`, `parseToken()`, `hashSecret()`, `secretMatches()`, `statusFor()`,
`expiryFor()`, `normaliseScopes()`, `limitScopes()`, `allowedScopes()`, `routeScope()`. Templates haben
`fetch( 'apikey', 'can_create' )` und `fetch( 'apikey', 'counts', hash( 'user_id', <id> ) )` (die eigenen des
aktuellen Benutzers oder die jedes Benutzers für einen `oauthadmin`-Administrator).

**Basic-Authentifizierung** (`AuthenticationStyle=ezpRestBasicAuthStyle`) prüft das Passwort jetzt mit
`expRestPasswordAuthFilter`: den Benutzer anhand des Logins (oder der E-Mail, wenn `AuthenticateMatch` es erlaubt),
aktiviert, veröffentlicht, nicht gesperrt, und `eZUser::authenticateHash()` mit dem eigenen Hash-Typ des Benutzers.
Bis 6.0.15 verglich sie `md5("login\npassword")` per SQL mit `ezuser.password_hash`, sodass jeder Benutzer mit einem
modernen Hash (bcrypt oder argon2, `php_default`, der Standard seit 6.0) abgelehnt wurde. Ein falsches Passwort zählt
jetzt als fehlgeschlagene Anmeldung.

## 10. Das Datenmodell und das Upgrade

Eine Tabelle, `expapikey`, eine Zeile je Schlüssel:

| Spalte | Enthält |
|---|---|
| `id` | die Nummer des Schlüssels |
| `user_id` | den Eigentümer (Inhaltsobjekt-ID des Benutzers) |
| `name` | den Namen, den der Eigentümer ihm gegeben hat (höchstens 100 Zeichen) |
| `key_prefix` | `expk_<id>`, eindeutig: der öffentliche Teil, gezeigt in Listen und Logs |
| `secret_hash`, `salt` | HMAC-SHA-256 des Geheimnisses mit dem Salt als Schlüssel (16 Zufallsbytes, hex), 64 Hex-Zeichen |
| `scopes` | die IDs der Bereiche, durch Leerzeichen getrennt |
| `created`, `created_by` | wann und von wem |
| `expires` | Enddatum, 0 = keines |
| `last_used`, `last_ip` | die zuletzt vermerkte Benutzung (höchstens einmal pro `LastUsedInterval`) |
| `revoked`, `revoked_by` | wann und von wem; 0 = nicht widerrufen |

Eine neue Installation bekommt sie aus dem Schema ihrer Engine (`share/db_schema.dba`, von jedem Installer benutzt,
und die Kernel-Schemas von MySQL, PostgreSQL und SQLite). Eine bestehende 6.0-Installation fügt sie mit der
Update-Datei ihrer Engine hinzu (`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`; so geschrieben, dass ein
zweiter Lauf nichts ändert) oder, auf jeder Engine einschließlich Oracle und MongoDB, mit:

```bash
php update/common/scripts/6.0/createapikeytable.php --dry-run --allow-root-user   # says whether it is missing
php update/common/scripts/6.0/createapikeytable.php --allow-root-user
```

```
Database: sqlite, table expapikey: missing
created expapikey
```

Erzeugen Sie dann die Kernel-Autoloads neu (`php bin/php/ezpgenerateautoloads.php -k`), leeren Sie die INI-, Template-
und Template-Override-Caches, laden Sie PHP-FPM neu und starten Sie Velocity neu.

## 11. Sicherheitshinweise

- **Nichts aus der Datenbank zu stehlen.** Das Geheimnis besteht aus 40 Zeichen aus 62 (etwa 238 Bit), und gespeichert
  wird nur sein mit einem Schlüssel gebildeter Hash. Eine Kopie der Tabelle lässt sich nicht in funktionierende Schlüssel
  zurückverwandeln.
- **Konstante Laufzeit.** Ein vorgelegtes Geheimnis wird mit `hash_equals()` verglichen, und auch ein unbekannter oder
  fehlerhafter Schlüssel verbraucht die Zeit einer Hash-Berechnung, sodass die Laufzeit nicht verrät, wie nah ein
  Rateversuch war. Dasselbe wurde für die Geheimnisse von OAuth-Clients getan (`ezpRestClient::validateSecret()`), die
  `===` benutzten.
- **Nie mehr als der Eigentümer.** Bereiche werden beim Erstellen des Schlüssels und erneut bei jeder Anfrage gegen
  die Richtlinien des Eigentümers geprüft, schreibende Routen gegen die Rechte des Eigentümers an dem Knoten.
- **Nur im Header.** Schlüssel in Adressen landen in Zugriffslogs, Proxys und im Browserverlauf; dort werden sie
  abgelehnt.
- **CSRF.** Jedes Formular der Seite für den API-Zugang und der Administration sendet das Formular-Token. Die Seite für
  den API-Zugang braucht keine eigene Richtlinie (`PolicyOmitList[]=apikey/list`) und prüft die Anmeldung und
  `apikey/create` selbst; sie rührt nie den Schlüssel eines anderen Benutzers an, auch nicht mit einer gefälschten ID.
- **Einmal angezeigt.** Die Seite mit einem neuen Schlüssel wird mit `Cache-Control: no-store` gesendet; der
  Formular-Token-Filter behält diesen strengeren Header statt seines eigenen. Eine Einmal-Nonce verhindert, dass ein
  Neuladen einen zweiten Schlüssel erstellt.
- **Nicht im Antwort-Cache.** Die Seite ist eine Modul-View und wird nie vom HTTP-Cache gespeichert, der nur
  Inhaltsansichten hält.
- **Die Geheimnisse der Anwendungen.** Die Geheimnisse von OAuth-Clients werden weiterhin unverändert gespeichert (ein
  OAuth-Client muss sie auf seiner Seite zurücklesen können); die Seite klappt sie ein, bis sie angefordert werden.

## 12. Fehlerbehebung

| Symptom | Ursache und Abhilfe |
|---|---|
| Kein Link **API-Zugang** im Profil | Der Benutzer hat kein `apikey/create` und keine Schlüssel. Vergeben Sie die Richtlinie ([Abschnitt 5](#5-schlüssel-für-ihre-benutzer-einschalten)) |
| „Ihr Konto darf Schlüssel erstellen, aber keiner der Bereiche ... steht Ihnen offen“ | Die Rolle hat `apikey/create`, aber nicht `content/read` (oder die anderen Richtlinien), oder eine Limitierung `Scope` nennt keine davon |
| 401 `invalid_token` mit einem frischen Schlüssel | Der Schlüssel wurde beim Kopieren abgeschnitten (er hat genau 58 Zeichen), oder er wurde in der Adresse gesendet, oder der Header lautet `Authorization: expk_...` ohne `Bearer` |
| 401 nur auf einem Server | Ein vorgeschalteter Proxy verwirft den Header `Authorization`. Apache mit PHP-FPM muss ihn durchreichen (`SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1` oder `CGIPassAuth On`) |
| 403 `insufficient_scope` | Die Route braucht einen Bereich, der dem Schlüssel fehlt, der Eigentümer hat die Richtlinie verloren, oder der Schreibzugriff liegt außerhalb der Rechte des Eigentümers. Das Audit-Ereignis `access.apikey.use.failed` nennt, welches davon (`scope`, `policy`, `permission`), und die Route |
| 405 „This method is not supported“ bei `/content/node/create` | Vor 6.0.15 wurde ein abgelehnter `POST` oder `DELETE` (kein oder ein falscher Schlüssel) auf eine Authentifizierungsroute weitergeleitet, die nur `GET` annahm. Aktualisieren Sie den Kernel und `ezprestapi` 1.2.5; `v1` ist nur lesend, ein Schreibzugriff dort antwortet weiterhin mit 405 |
| 403 `access_denied` | Der Benutzer hinter dem Token oder Schlüssel darf dies mit diesem Knoten nicht tun ([3.2](#32-berechtigungen-der-rest-schnittstelle)); die Meldung nennt die Richtlinie |
| 429 | Über `RateLimitPerMinute`; warten Sie `Retry-After` Sekunden oder erhöhen Sie die Einstellung |
| Ein Datenbankfehler auf `/apikey/list` | Die Tabelle fehlt: Führen Sie `createapikeytable.php` aus ([Abschnitt 10](#10-das-datenmodell-und-das-upgrade)) |
| Velocity antwortet anders als Apache | Velocity lädt Klassen bei seinem Start; starten Sie es nach dem Upgrade neu (`./console exp:velocity restart`) |

## 13. Jede Einstellung

`settings/rest.ini`, Block `[ApiKeySettings]`; Override in `settings/override/rest.ini.append.php` oder in der
`rest.ini.append.php` einer Extension.

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `ApiKeys` | `enabled` | `disabled`: kein Schlüssel wird angenommen oder erstellt |
| `ExpiryChoices[]` | 7, 30, 90, 365, 0 | die angebotenen Laufzeiten in Tagen; 0 (kein Ende) nur bei `MaxExpiryDays=0` |
| `DefaultExpiryDays` | 90 | vorausgewählte Laufzeit |
| `MaxExpiryDays` | 365 | längste Laufzeit; 0 = keine Grenze |
| `MaxKeysPerUser` | 10 | funktionierende Schlüssel je Benutzer; 0 = keine Grenze |
| `LastUsedInterval` | 60 | Sekunden zwischen zwei Schreibvorgängen der letzten Benutzung eines Schlüssels |
| `RateLimitPerMinute` | 120 | Anfragen je Schlüssel und Minute (APCu); 0 = keine Grenze |
| `Scopes[<id>]` | read, publish, edit, remove | die Richtlinie (`module/function`), für die ein Bereich steht |
| `ScopeNames[<id>]` | | der Name des Bereichs auf den Seiten |
| `DefaultReadScope` | `read` | der Bereich einer nicht aufgeführten GET- oder HEAD-Route; leer = keiner |
| `RouteScopes[<Controller>_<action>]` | die schreibenden Routen von ezprestapi | der Bereich, den eine Route braucht |
| `RouteGuards[<Controller>_<action>]` | `create`, `edit`, `remove` | die Rechte des Eigentümers, geprüft an dem Knoten, auf den eine schreibende Route wirkt |
| `ExamplePath` | leer | der REST-Pfad, den die Seite für den API-Zugang in ihrem curl-Beispiel verwendet |

Außerdem: `site.ini [RoleSettings] PolicyOmitList[]=apikey/list`, `module.ini ModuleList[]=apikey`,
`admininterface.ini ItemsPerPage[oauthadmin/keys]=25`.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
