# Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache

Lesen Sie diese Seite, wenn Ihre Website unter mehreren Hostnamen antwortet (live, Test, lokal) oder ihre Sprachen
unter einem eigenen Pfad liegen (`/ger`, `/eng`) und die Startseite `example.com/` Besucher zur richtigen Sprache
führen soll. Zwei Einstellungen von `site.ini [SiteAccessSettings]` erledigen das ohne einen Patch von
`eZSiteAccess::match()`.

## Kurz gefasst

| | |
|---|---|
| `HostMatchMethod` | Wie die Host-Zuordnungstabelle (`HostMatchType=map`) einen Host vergleicht: `strict` (Standard, wie bisher), `start`, `end` oder `part`. Mit `start` erreicht `www.example.com.test.local` den Siteaccess von `www.example.com`. |
| `HostMatchMapItems[]=host;siteaccess;method` | Dasselbe pro Eintrag. |
| `DefaultHostUriMatchMapItems[]` | Wo der Standard-Siteaccess greifen würde (keine Prüfung von `MatchOrder` hat zugeordnet), der nach Host und Browsersprache gewählte Siteaccess und das Sprachsegment. |
| Die Weiterleitung | `/` geht nach `/ger`, `/news/an-article` nach `/ger/news/an-article` (302, einmal, Adresse und Query bleiben erhalten). |
| `DefaultHostUriRedirect` | `enabled` (Standard) oder `disabled` (die Seite wird unter der Adresse selbst angezeigt). |
| Caches | Nur die Weiterleitung hängt vom Browser ab; jede Seite dahinter hat eine Adresse pro Sprache. Kein Cookie. |
| Ohne die Einstellungen | Nichts ändert sich: `site.ini` wird mit `HostMatchMethod=strict` und ohne Eintrag `DefaultHostUriMatchMapItems[]` ausgeliefert. |

## Die Host-Zuordnungstabelle nach dem Anfang eines Hosts

Bisher nahm `MatchOrder=host` mit `HostMatchType=map` nur einen Host an, der genau so aufgeführt war. Ein Testhost
brauchte eigene Einträge. Jetzt lässt sich der Vergleich wählen, mit den Methoden, die `HostUriMatchMapItems` bereits
kennt:

| Methode | Trifft zu, wenn der Host ... | Beispiel für `www.example.com` |
|---|---|---|
| `strict` | der aufgeführte Host ist (Standard) | `www.example.com` |
| `start` | damit beginnt | `www.example.com.test.local` |
| `end` | damit endet | `preview.www.example.com` |
| `part` | ihn enthält | `a.www.example.com.b` |

```ini
[SiteAccessSettings]
MatchOrder=host
HostMatchType=map
# For every entry
HostMatchMethod=start
HostMatchMapItems[]=www.example.com;site
# Or for one entry, the others stay strict
HostMatchMapItems[]=admin.example.com;admin;start
```

`start` und `part` nehmen auch Hosts an, die Sie nicht gemeint haben (`www.example.com.anything`). Verwenden Sie sie
dort, wo der Webserver nur für Ihre eigenen Hosts antwortet. Der statische Cache kennt nur `strict`, wie bei
`host_uri`.

Jede Methode, in der Host-Zuordnungstabelle, in `HostUriMatchMapItems` und in `DefaultHostUriMatchMapItems`,
vergleicht Hostnamen so wie DNS:

- ohne Rücksicht auf Groß- und Kleinschreibung und auf den Punkt eines vollqualifizierten Namens (`WWW.Example.com.`
  ist `www.example.com`);
- ohne den Port der Anfrage (`www.example.com:8080`, wie bei einem zweiten Webserver), es sei denn, der Eintrag nennt
  selbst einen Port;
- einen in den Einstellungen in Unicode geschriebenen Namen (`münchen.example`) als die ASCII-Form, die ein Browser
  sendet (`xn--mnchen-3ya.example`), wenn PHP die Extension intl hat;
- `end` bedeutet das Ende: `example.com` nimmt `www.example.com` und `example.com.example.com` an, nicht
  `example.com.evil`. Ein leerer Host trifft mit `start` und `part` auf jeden Host zu, mit `strict` und `end` auf
  keinen.

## Die Weiterleitung zur Sprache

### Die Regel

Wenn keine Prüfung von `MatchOrder` eine Adresse zuordnet, antwortet der Standard-Siteaccess (`DefaultAccess`). Das
ist die Adresse ohne Siteaccess- oder Sprachsegment: `example.com/`, `example.com/contact`. Mit
`DefaultHostUriMatchMapItems[]` benennen Sie stattdessen den Siteaccess und das Segment für diesen Fall:

```ini
DefaultHostUriMatchMapItems[]=host;uri;siteaccess[;method[;language]]
```

| Feld | Bedeutung |
|---|---|
| `host` | Der Host, verglichen nach `method`. Leer mit `part` trifft auf jeden Host zu. |
| `uri` | Das Segment, unter dem der Siteaccess erreicht wird, zum Beispiel `ger`. |
| `siteaccess` | Der Siteaccess. |
| `method` | `strict`, `start`, `end` oder `part`; leer oder `default` bedeutet `HostUriMatchMethodDefault`. |
| `language` | Optional. Der Eintrag gilt nur, wenn der Browser diese Sprache akzeptiert; `de` nimmt auch `de-CH` an, und `de_DE` wird als `de-DE` gelesen. |

Der erste Eintrag für den Host, dessen Sprache der Browser akzeptiert, gewinnt: Die Sprachen von `Accept-Language`
werden von der meistgewünschten an probiert (nach `q`; `q=0` zählt nicht, ein `q` über 1 zählt als 1, die ersten 32
Sprachen des Headers werden gelesen, und alles, was kein Sprach-Tag ist, wird übersprungen). Wenn der Browser keine
davon möchte, gilt der erste Eintrag für den Host ohne Sprache; gibt es keinen, antwortet `DefaultAccess` wie bisher.
Ein Eintrag, dessen Siteaccess nicht in `AvailableSiteAccessList` steht, wird ignoriert und protokolliert.

Der Browser wird dann einmal (302) zur selben Adresse mit dem Segment weitergeleitet:

| Angefragt | Weitergeleitet zu |
|---|---|
| `example.com/` | `example.com/ger` |
| `example.com/contact` | `example.com/ger/contact` |
| `example.com/news/an-article?page=2` | `example.com/ger/news/an-article?page=2` |
| `example.com/ger/...`, `example.com/admin/...` | nicht weitergeleitet: eine Prüfung hat zugeordnet |

Die Adresse bleibt so, wie sie angefragt wurde: Ein URL-Alias bleibt einer und wird nicht in `content/view/full/...`
umgewandelt. Nur GET- und HEAD-Anfragen werden weitergeleitet; ein POST an eine Adresse ohne Segment wird dort
beantwortet.

Die `Location` ist immer ein Pfad auf dem angefragten Host, nie eine vollständige URL, sodass ein gefälschter
`Host`-Header nie hineingeschrieben wird und die Weiterleitung hinter Apache und Velocity gleich funktioniert, auf
jedem Port. Der Pfad, den der Kernel dekodiert hält, wird erneut kodiert: `%0D%0A` kann den Header nicht teilen,
`%2F%2F` oder ein Backslash kann das Ziel nicht in eine Adresse auf einem anderen Host verwandeln, leere Segmente und
Punktsegmente (`//`, `..`) werden verworfen, und die Query behält ihre Kodierung (Bytes außerhalb des druckbaren ASCII
werden kodiert). Der Pfad beginnt mit der Index-Datei und dem Segment des Eintrags, nicht mit der Adresse, die der
Kernel für Links bilden würde: Mit `RemoveSiteAccessIfDefaultAccess=enabled` würde diese das Segment des
Standard-Siteaccess weglassen und den Browser dorthin zurückschicken, woher er kam. Ein Ziel, das der angefragten
Adresse entspricht, wird nie gesendet.

### Eine Sprache unter `/ger`, Siteaccesses nach URL zugeordnet

Die übliche Einrichtung einer Installation: `MatchOrder=uri`, die Siteaccesses `site`, `ger`, `admin` unter ihren
Namen. `example.com/` soll die deutsche Website zeigen:

```ini
[SiteAccessSettings]
MatchOrder=uri;host
DefaultHostUriMatchMapItems[]=;ger;ger;part
```

Der leere Host mit `part` lässt die Regel auf jedem Host gelten (lokal, Test, live). `/admin` und `/ger/...` werden
über die URL zugeordnet und bleiben, wo sie sind.

### Mehrere Sprachen, vom Browser gewählt

Deutsch und Englisch auf einem Host, zugeordnet nach Host und Segment:

```ini
[SiteAccessSettings]
MatchOrder=host_uri
HostUriMatchMapItems[]=www.example.com;de;site_de
HostUriMatchMapItems[]=www.example.com;en;site_en
DefaultHostUriMatchMapItems[]=www.example.com;de;site_de;;de
DefaultHostUriMatchMapItems[]=www.example.com;en;site_en
```

Ein Browser, der Deutsch möchte (`de-DE,de;q=0.9,en;q=0.8`), geht von `/` nach `/de`; jeder andere nach `/en`. Wer
zur anderen Sprache wechselt, folgt deren Links, die alle ihr Segment tragen, auch die Startseite dieser Sprache
(`/en`). Diese Adressen werden von `HostUriMatchMapItems` zugeordnet und nie wieder weitergeleitet, sodass die Wahl
ohne Cookie bestehen bleibt.

### Wenn es keine Weiterleitung gibt

Die Weiterleitung braucht eine Prüfung, die die Adresse mit dem Segment für den gewählten Siteaccess annimmt; der
Kernel prüft das (`eZSiteAccess::reachesSiteAccess()`), sonst käme die Weiterleitung beim Standard wieder an. Wenn es
keine solche Prüfung gibt, bei anderen Anfragen als GET und HEAD, oder mit

```ini
DefaultHostUriRedirect=disabled
```

wird die Seite unter der Adresse selbst im gewählten Siteaccess angezeigt, und ihre Links tragen das Segment.

## Caches

Jede Seite hinter der Weiterleitung hat eine Adresse pro Sprache und wird wie jede andere gecacht. Die Antwort unter
der Adresse ohne Segment hängt vom Browser ab, wenn ein Eintrag für den Host eine Sprache nennt und die Einträge zu
mehr als einem Siteaccess oder Segment führen, oder wenn kein Eintrag ohne Sprache gilt (dann wird ein deutscher
Browser nach `/ger` geschickt, und jeder andere erhält `DefaultAccess` unter derselben Adresse). Eine solche Antwort,
die Weiterleitung, eine unter der Adresse selbst angezeigte Seite und ebenso die Seite von `DefaultAccess` für die
anderen Browser, wird mit `Vary: Accept-Language` (ergänzt zu einem `Vary`, das `[HTTPHeaderSettings]` bereits sendet)
und `Cache-Control: private` gesendet, sodass weder ein Proxy noch der Antwort-Cache von Velocity (der nach Adresse
verschlüsselt und nur `200`-Antworten behält) sie aufbewahrt, und der rollenbewusste HTTP-Cache speichert sie nicht.
Sein früher Ausstieg, der den Siteaccess findet, bevor der Kernel läuft, liefert keine Seite für eine Adresse aus, für
die ein Eintrag des Hosts gilt. Wenn jeder Eintrag zum selben Siteaccess und Segment führt, ist die Weiterleitung für
alle gleich und wird wie jede andere gesendet.

Seiten, die der statische Cache oder der HTTP-Cache aufbewahrt hat, bevor Sie einen Eintrag hinzugefügt haben, werden
weiterhin unter der alten Adresse ohne die Weiterleitung ausgeliefert: Leeren Sie diese Caches, nachdem Sie die
Einstellung geändert haben.

## Prüfen

```bash
curl -sI -H 'Accept-Language: de' https://example.com/ | grep -iE '^(HTTP|location|vary|cache-control)'
curl -sI https://example.com/contact | grep -i '^location'
curl -sI https://example.com/admin | grep -i '^HTTP'
```

Die ersten beiden antworten mit `302` und dem Sprachsegment in `Location`, die dritte wie bisher.

## So funktioniert es

- `eZSiteAccess::match()` führt die Prüfungen von `MatchOrder` aus. Wenn keine zugeordnet hat, fragt es
  `eZSiteAccess::matchDefaultHostUri()` (mit `acceptedLanguages()` für den Header) und markiert den Siteaccess für die
  Weiterleitung, wenn `reachesSiteAccess()` eine Prüfung für die Zieladresse findet. Beide Prüfungen mit Host-Methoden
  verwenden `eZSiteAccess::hostMatches()`.
- Nur die Anfrage einer Webseite tut dies: `ezpKernelWeb` ruft `eZSiteAccess::match()` mit seinem fünften Parameter
  `$languageDefault` auf `true` auf. Die REST-Schnittstelle, das Baummenü, Extensions und Skripte lassen ihn weg und
  erhalten wie bisher die Prüfungen, dann `DefaultAccess`: Ihr Siteaccess hängt nie von der Browsersprache ab, und sie
  werden nie weitergeleitet. Ein neuer Aufrufer erhält dasselbe, sofern er nicht nach dem Sprachstandard fragt.
- `ezpKernelWeb::run()` sendet die Weiterleitung an `ezpKernelWeb::languageRedirectLocation()` (gebildet von
  `languageRedirectURI()`) und gibt sie als `ezpKernelRedirect` zurück, so wie eine Modulweiterleitung zurückgegeben
  wird, sodass sie auch unter Velocity funktioniert.
- `ezpHttpCacheContract::resolveSiteAccess()`, das der frühe Ausstieg des HTTP-Caches verwendet, kennt
  `HostMatchMethod`, die Methode eines Eintrags der Host-Zuordnungstabelle und die Hosts von
  `DefaultHostUriMatchMapItems`.
- Während `reachesSiteAccess()` fragt, wohin das Ziel führen würde, sendet `RedirectOnNormalize` nichts.
- Tests: `eZSiteAccessMatchTest` und `ezpHttpCacheContractTest` (ohne Datenbank).

## Verwandte Seiten

- [Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md)
- [Inhaltssprachen](../../anleitungen/inhaltssprachen.md)
- [Antwort-Cache von Velocity](velocity-antwort-cache.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
