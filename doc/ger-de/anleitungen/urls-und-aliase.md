# Links, URL-Aliase, Platzhalter und Suchstatistiken

Diese Anleitung erklärt die vier Seiten der Administrationsoberfläche, die sich um Adressen kümmern: die **Linkliste**
(welche externen und internen Links Ihre Inhalte verwenden und ob sie funktionieren), die **globalen URL-Aliase**
(Adressen Ihrer Wahl für Modul-Views), die **URL-Platzhalter** (eine Regel für eine ganze Gruppe von Adressen) und die
**Suchstatistiken** (wonach Besucher suchen und ob sie es finden).

Sie richtet sich an Administratoren und Redakteure, die die Links und Adressen einer Site in Ordnung halten. Jede Seite,
jedes Feld und jede Regel unten wurde am 6. Oktober 2026 mit dem Code dieses Repositorys abgeglichen; die Dateien sind
unter [Quellen](#quellen) aufgeführt.

[Anleitungen](README.md) · Siehe auch: [Seitenweise Listen in der Administration](../../eng-us/features/6.0/admin-list-paging.md) ·
[Cronjobs](cronjobs.md) · [Betrieb einer Site](betrieb-einer-website.md)

## Kurz gesagt

- **Setup > URL-Verwaltung** (`/url/list`) listet jede Adresse, auf die veröffentlichte Inhalte verlinken, einmal auf,
  mit ihrem Zustand (gültig, ungültig, nie geprüft), dem Zeitpunkt der Prüfung und der letzten Änderung und den
  Objekten, die sie verwenden. Sie können nach jedem Teil der Adresse suchen, einen Zustand anzeigen, nach Adresse,
  letzter Prüfung oder letzter Änderung sortieren und angehakte Links von Hand als gültig oder ungültig markieren.
- Die **Linkprüfung** (`linkcheck.php`, Teil `infrequent`) ruft http- und https-Links gleichermaßen ab, beurteilt Links
  auf Inhalte anhand ihres Knotens oder Objekts, ruft nie Adressen in einem privaten Netz ab und wartet zwischen
  Anfragen an denselben Host. Ihre Einstellungen stehen in `cronjob.ini [linkCheckSettings]`.
- **Setup > URL-Übersetzer** (`/content/urltranslator`) legt **globale URL-Aliase** an und entfernt sie: zum Beispiel
  `login` für `user/login`. Jeder Alias zeigt seinen vollständigen Pfad, sein Ziel und das, worauf es gerade führt;
  ein Alias auf ein Modul, das es nicht mehr gibt, wird gekennzeichnet.
- **Setup > URL Platzhalter** (`/content/urlwildcards`) legt **Platzhalter** an und entfernt sie: `news/*` nach
  `articles/{1}`. Die Seite lehnt ein Ziel ab, das ein `{n}` verwendet, für das das Muster kein `*` hat, und
  **Eine Adresse ausprobieren** zeigt, welchem Platzhalter eine Adresse entspricht und wohin sie führt, ohne etwas
  zu ändern.
- **Setup > Suchstatistiken** (`/search/stats`) zeigt jeden erfassten Suchbegriff, wie oft danach gesucht wurde und
  die durchschnittliche Zahl der Treffer. Die Seite meldet, wenn kein Siteaccess Suchen erfasst (die
  Voreinstellung), filtert die Begriffe, die nichts gefunden haben, sortiert nach Anzahl, Begriff oder wenigsten
  Treffern und führt einen Begriff mit einem Klick erneut aus.
- Jede Liste ist seitenweise aufgeteilt, jede Suche und jeder Filter bleibt in der Adresse erhalten (übersteht also
  das Blättern und lässt sich als Lesezeichen speichern), und jedes Entfernen von mehr als den angehakten Zeilen
  fragt vorher direkt auf der Seite nach.

## Inhalt

- [1. Die Linkliste](#1-die-linkliste)
- [2. Wie Links geprüft werden](#2-wie-links-geprüft-werden)
- [3. Globale URL-Aliase](#3-globale-url-aliase)
- [4. URL-Platzhalter](#4-url-platzhalter)
- [5. Suchstatistiken](#5-suchstatistiken)
- [6. Adressen und Parameter der Seiten](#6-adressen-und-parameter-der-seiten)
- [7. Wenn sich etwas nicht wie erwartet verhält](#7-wenn-sich-etwas-nicht-wie-erwartet-verhält)
- [8. Adressen dekodieren und bereinigen: zwei optionale Einstellungen](#8-adressen-dekodieren-und-bereinigen-zwei-optionale-einstellungen)
- [9. View-Parameter: wie ein Wert in der Adresse reist](#9-view-parameter-wie-ein-wert-in-der-adresse-reist)
- [Für Entwickler](#für-entwickler)
- [Quellen](#quellen)

## 1. Die Linkliste

Öffnen Sie **Setup > URL-Verwaltung**. Die Seite beginnt mit fünf Kennzahlen:

| Kennzahl | Was sie zählt | Ein Klick zeigt |
|---|---|---|
| Links in veröffentlichten Inhalten | Adressen, auf die mindestens eine veröffentlichte Version verlinkt | alle |
| Gültig | davon die als gültig markierten | die gültigen |
| Ungültig | die als ungültig markierten (rot, sobald es welche gibt) | die ungültigen |
| Nie geprüft | von der Linkprüfung noch nie versucht | diese Links |
| Letzte Linkprüfung | der Zeitpunkt der jüngsten Prüfung irgendeines Links | – |

Darunter durchsucht **Einen Link finden** jeden Teil der Adresse (`example.org`, `/about`, `mailto:`), ohne Rücksicht
auf Groß- und Kleinschreibung; `%` und `_` werden als sie selbst gesucht. **Anzeigen** wählt die Liste (Alle, Gültig,
Ungültig, Nie geprüft), und **Reihenfolge** sortiert sie: Adresse A bis Z (die Voreinstellung), Zuletzt geprüft
(jüngste zuerst) oder Zuletzt geändert.

Jeder Link ist eine Karte mit:

- der Adresse, die die eigene Seite des Links öffnet (`/url/view/<id>`), mit jeder Objektversion, die ihn verwendet;
- Plaketten: **Gültig** oder **Ungültig**, **Nie geprüft** und wie die Linkprüfung die Adresse behandelt (siehe
  Abschnitt 2);
- **Ansehen**, **Bearbeiten** (ändert die Adresse an einer Stelle für jedes Objekt, das sie verwendet) und **Öffnen**,
  das die Adresse in einem neuen Fenster öffnet. Öffnen wird nur für Web-, Mail- und Site-Adressen angeboten, nie für
  `javascript:` oder `data:`, damit ein schädlicher Link in Inhalten nicht in der Administration ausgeführt werden kann;
- den Zeitpunkten **Überprüft** und **Geändert**;
- **Verwendet von**: bis zu drei veröffentlichte Objekte, die darauf verlinken, jeweils verlinkt mit ihrem
  Hauptknoten, und „und N weitere“, das zur Seite des Links führt.

Um Links von Hand zu markieren, haken Sie sie an (oder **Alle auf dieser Seite auswählen**) und klicken Sie auf
**Ausgewählte als gültig markieren** oder **Ausgewählte als ungültig markieren**. Die Seite meldet, wie viele markiert
wurden. Das Markieren ändert nur den Zustand; nichts wird entfernt, und kein Inhalt ändert sich. Die nächste
Linkprüfung testet die Links erneut und kann den Zustand wieder zurücksetzen.

Die Seitengröße wird unter der Liste gewählt (10, 25 oder 50, je Benutzer gespeichert). Die angebotenen Größen sind
die Einstellung `ItemsPerPageList_url_list[]` in `admininterface.ini [PaginationSettings]`, wie bei jeder Liste der
Administration ([Blättern](../../eng-us/features/6.0/admin-list-paging.md)).

## 2. Wie Links geprüft werden

Die Linkprüfung ist das Cronjob-Skript `linkcheck.php` im Teil `infrequent` (`cronjob.ini`). Sie läuft nur über die
Shell, nie in einer Web-Anfrage (auch nicht unter Velocity). Führen Sie den ganzen Teil aus oder nur die Linkprüfung:

```bash
php runcronjobs.php -s <your public siteaccess> infrequent
php runcronjobs.php -s <your public siteaccess> --script=linkcheck.php
```

Führen Sie sie wie die anderen Cronjobs als der Benutzer aus, unter dem der Webserver läuft. Sie prüft zuerst die
Links, deren Prüfung am längsten zurückliegt, und gibt je Link eine Zeile mit dem Ergebnis und dem Grund aus, danach
eine Zusammenfassung wie:

```
All links have been checked! 189 valid, 72 invalid, 89 not decided, 0 skipped (checked recently or over MaxURLsPerRun), 48 changed, 725 requests, 566.7 s
```

Ein Link ändert seinen Zustand nur, wenn die Prüfung eine Entscheidung trifft; **not decided** (nicht entschieden)
behält den bisherigen Zustand. Der Zeitpunkt der Prüfung wird für jeden Link gespeichert, den sie angesehen hat. Die
Plakette auf jeder Karte zeigt, wie die jeweilige Art behandelt wird:

| Plakette | Adresse | Was die Prüfung tut |
|---|---|---|
| (keine) | `http:`, `https:`, `ftp:` | ruft sie ab: HEAD, dann GET, wenn HEAD fehlschlägt, folgt Weiterleitungen bis `MaxRedirects` und prüft TLS-Zertifikate. Eine 2xx-Antwort ist gültig; 404, 410, andere Fehler, ein ungültiges Zertifikat, ein unbekannter Host oder zu viele Weiterleitungen sind ungültig; 429 und 503 (später erneut versuchen) sowie 401, 403 (die Site verweigert automatisierte Anfragen) sind nicht entschieden |
| E-Mail | `mailto:` | gültig, wenn die Domain einen Mailserver hat (MX-Eintrag) |
| Auf dieser Website | ein Pfad wie `/about` oder `/admin/content/dashboard` | ohne Query, Fragment und einen vorangestellten Siteaccess-Namen: gültig, wenn es die Startseite, eine Modul-View oder ein URL-Alias ist, sonst wird er unter den Adressen aus `SiteURL[]` versucht |
| Link auf Inhalt | `ezlocation://`, `eznode://`, `ezobject://` (Links in Rich Text) | gültig, solange der Knoten oder das Objekt existiert, veröffentlicht und nicht verborgen ist; sonst ungültig. Es wird keine Anfrage gesendet |
| Nicht getestet | `file:` | bezeichnet eine Datei auf einem Computer, keine Seite: wird nie getestet, der Zustand bleibt erhalten |
| Andere Adresse | jedes andere Schema (`tel:`, `javascript:` ...) | wird wie bisher als Pfad dieser Site nachgeschlagen, ist also meist ungültig |

**Private Adressen werden nie abgerufen.** Vor jeder Anfrage, auch nach einer Weiterleitung, wird der Host aufgelöst;
liegt eine Adresse in einem privaten, Loopback-, Link-Local- oder anderweitig reservierten Netz (10.0.0.0/8,
127.0.0.0/8, 169.254.0.0/16, 192.168.0.0/16, `::1`, `fe80::/10` ...), bleibt der Link unentschieden, und nichts wird
gesendet. Andernfalls könnte ein Link in Inhalten den Server dazu bringen, in sein eigenes Netz hineinzugreifen. Die
Anfrage geht dann an genau die geprüfte Adresse, sodass der Name einen Augenblick später nicht woandershin zeigen kann.
Ein Intranet, das getestet werden soll, wird in `AllowedPrivateHosts[]` eingetragen; die eigenen Adressen der Site aus
`SiteURL[]` sind immer erlaubt.

**Ein Host wird nicht überlastet.** Anfragen an denselben Host warten `HostDelay` Millisekunden, und ein Link, der vor
weniger als `RecheckInterval` Sekunden geprüft wurde, wird übersprungen; den Teil zweimal hintereinander auszuführen,
kostet also nichts.

Die Einstellungen in `cronjob.ini [linkCheckSettings]` (überschreiben Sie sie in
`settings/override/cronjob.ini.append.php`):

| Schlüssel | Voreinstellung | Bedeutung |
|---|---|---|
| `Timeout` | `15` | Sekunden, die eine Anfrage dauern darf |
| `ConnectTimeout` | `5` | Sekunden für den Verbindungsaufbau |
| `MaxRedirects` | `5` | Weiterleitungen, denen gefolgt wird, bevor der Link als defekt gilt |
| `HostDelay` | `1000` | Millisekunden zwischen zwei Anfragen an denselben Host |
| `RecheckInterval` | `72000` | Sekunden, bevor ein geprüfter Link erneut geprüft wird; `0` prüft bei jedem Lauf jeden Link |
| `MaxURLsPerRun` | `0` | in einem Lauf geprüfte Links (die am längsten nicht geprüften zuerst); `0` prüft alle |
| `UserAgent` | `Exponential Link Validator (+https://exponential.earth)` | der User-Agent der Anfragen |
| `AllowedPrivateHosts[]` | leer | Hosts, Adressen oder IPv4-Bereiche (`10.1.0.0/16`) in einem privaten Netz, die getestet werden dürfen |
| `SiteURL[]` | leer | die eigenen Adressen der Site, versucht für einen Pfad, der kein Alias ist |

Bei den 350 Links einer Demonstrations-Site dauerte der erste Lauf etwa neuneinhalb Minuten und sendete 725 Anfragen:
der größte Teil der Zeit ist die Wartezeit je Host zwischen Links auf dieselben wenigen Hosts.

## 3. Globale URL-Aliase

Ein globaler URL-Alias gibt einer Modul-View oder einem anderen Alias eine Adresse Ihrer Wahl. Öffnen Sie
**Setup > URL-Übersetzer**. Die Aliase von Inhalten (die Adressen von Knoten) stehen nicht hier: Sie finden sie auf dem
Reiter **URL-Aliase** des jeweiligen Knotens.

### Einen Alias anlegen

| Feld | Was Sie eingeben |
|---|---|
| Neuer URL-Alias | die Adresse, die Besucher verwenden werden, ohne Host und ohne führenden Schrägstrich: `login`, `campaign/autumn`. Unzulässige Zeichen werden ersetzt, und die Seite meldet, wodurch |
| Ziel | eine Modul-View (`user/login`, `content/search`) oder eine bestehende Adresse eines Inhalts (`about-us`). `content/view/full/<node ID>` legt stattdessen einen Alias dieses Knotens an, der dann auf dem Reiter URL-Aliase des Knotens aufgeführt wird, nicht hier |
| Sprache | der Alias funktioniert in Siteaccesses, die diese Sprache anzeigen |
| Andere Sprachen miteinschließen | lässt ihn in jeder Sprache funktionieren |
| Der Alias soll zum Ziel weiterleiten | angehakt (die Voreinstellung): Besucher erhalten eine 301-Weiterleitung auf die Adresse des Ziels. Nicht angehakt: das Ziel wird unter dem Alias angezeigt |

Klicken Sie auf **Erstellen**. Lässt sich der Alias nicht anlegen, nennt die Meldung den Grund, das Feld wird
markiert, und Ihre Eingaben bleiben im Formular erhalten:

| Meldung | Bedeutet |
|---|---|
| Der Text für den URL-Alias fehlt | das Alias-Feld ist leer |
| Das Ziel für den URL-Alias fehlt | das Ziel ist leer |
| Die angegebene Ziel-URL existiert nicht im System | das Ziel ist weder eine Modul-View noch eine bestehende Adresse |
| Der URL-Alias existiert bereits und zeigt auf ... | die Adresse ist vergeben; beide Links führen zu dem, was sie jetzt ist |
| Der gewählte Sprachcode ist ungültig | die Sprache existiert nicht |

### Die Liste lesen

Jeder Alias zeigt seinen vollständigen Pfad (jeder Teil verlinkt auf das, was er ist), seine Art (**Weiterleitung**
oder **Direkt**), das **Ziel**, worauf er tatsächlich führt (**Führt zu**: View und Modul oder ein Knoten), seine Sprache und ob er immer
verfügbar ist. **Modul nicht gefunden** kennzeichnet einen Alias, dessen Modul es nicht mehr gibt, meist weil eine
Extension abgeschaltet wurde: Besucher erhalten dort eine Fehlerseite.

**Einen Alias finden** durchsucht das letzte Segment jedes Alias (`login` in `campaign/login`) und sein Ziel,
**Anzeigen** wählt Alle, Weiterleitend oder Direkt. Beides sind Bedingungen der Datenbankabfrage, sodass eine Seite
eine Zählung und eine Seitenabfrage kostet, ganz gleich, wie viele Aliase es gibt.

### Entfernen

Haken Sie Aliase an und klicken Sie auf **Ausgewählte entfernen**; der Browser fragt noch einmal nach. **Alle
entfernen** öffnet direkt auf der Seite eine Bestätigung, die sagt, wie viele Aliase entfernt werden; Aliase von
Inhaltsknoten bleiben erhalten. Ein entfernter Alias funktioniert sofort nicht mehr.

## 4. URL-Platzhalter

Ein Platzhalter (Wildcard) leitet mit einer einzigen Regel eine Gruppe von Adressen woandershin. Jedes `*` im Muster
passt auf beliebigen Text, und `{1}`, `{2}` ... im Ziel setzen diesen Text wieder ein. Platzhalter werden nur
herangezogen, wenn kein URL-Alias passt, und sie werden in der Reihenfolge versucht, in der sie angelegt wurden; der
erste passende gewinnt.

| Muster | Ziel | `news/2026/october` wird zu |
|---|---|---|
| `news/*` | `articles/{1}` | `articles/2026/october` |
| `news/*/*` | `archive/{2}/{1}` | `archive/october/2026` |
| `old-blog/*` | `https://blog.example.org/{1}` (weiterleitend) | einer Weiterleitung auf die andere Site |

Das Muster passt ab dem Anfang der Adresse, ohne Rücksicht auf Groß- und Kleinschreibung; Text nach dem passenden Teil
bleibt erhalten.

### Einen Platzhalter anlegen

Geben Sie **Neuer URL Platzhalter** (das Muster) und **Ziel** ein und haken Sie **URL umleiten** an, um mit einer
301-Weiterleitung zu antworten (Besucher sehen die Zieladresse), oder lassen Sie es frei, um das Ziel unter der
angefragten Adresse anzuzeigen. Die Seite lehnt ab:

- ein leeres Muster oder Ziel;
- ein Muster, das es schon gibt (die Meldung nennt, wohin das alte führt);
- ein Ziel mit einem Platzhalter, für den das Muster kein `*` hat, etwa `{2}` bei nur einem `*`. Ohne diese Prüfung
  würde der Platzhalter Besucher auf eine Adresse schicken, der ein Teil fehlt.

### Eine Adresse ausprobieren

**Eine Adresse ausprobieren** nimmt eine Adresse wie `news/2026/october` entgegen und zeigt, welchem Platzhalter sie
entspricht, wozu sie wird, ob Besucher weitergeleitet werden und worauf das Ergebnis auf dieser Site führt (ein Alias,
eine Modul-View, eine andere Site oder nichts, was eine Fehlerseite bedeutet). Dabei ändert sich nichts; die Adresse
bleibt in der Adresse der Seite erhalten, sodass Sie sie an einen Kollegen schicken können.

### Finden und entfernen

**Einen Platzhalter finden** durchsucht Muster und Ziel, **Anzeigen** wählt Alle, Weiterleitend oder Direkt (beides
in der Datenbankabfrage, in der Reihenfolge, in der die Platzhalter versucht werden). Das Entfernen funktioniert wie
bei den Aliasen: **Ausgewählte entfernen** fragt einmal nach, **Alle entfernen** fragt direkt auf der Seite. Jede
Änderung leert den Platzhalter-Cache.

## 5. Suchstatistiken

Öffnen Sie **Setup > Suchstatistiken**.

**Suchen werden nur dort erfasst, wo es eingeschaltet ist.** Eine Suche zählt, wenn der Siteaccess, in dem der
Besucher sucht, Folgendes enthält:

```ini
# settings/siteaccess/<public siteaccess>/site.ini.append.php
[SearchSettings]
LogSearchStats=enabled
```

Die Voreinstellung ist `disabled`. Die Seite nennt die Siteaccesses, die Suchen erfassen, oder warnt, dass keiner das
tut; was dann aufgeführt ist, wurde früher erfasst.

Die Kennzahlen zählen die **verschiedenen Suchbegriffe**, alle **Suchen**, die **Suchbegriffe ohne Treffer** (ein
Klick listet sie auf) und den Anteil aller Suchen, die nichts gefunden haben.

**Einen Suchbegriff finden** durchsucht die Begriffe; **Anzeigen** wählt alle Begriffe oder nur diejenigen ohne
Treffer; **Reihenfolge** sortiert nach **Am häufigsten gesucht** (die Voreinstellung), **Suchbegriff A bis Z** oder
**Wenigste Treffer** (durchschnittliche Treffer, niedrigste zuerst). Der Balken neben jeder Anzahl vergleicht sie mit
dem am häufigsten gesuchten Begriff auf der Seite. **Jetzt suchen** führt den Begriff in der Suche der Administration
aus, um zu sehen, was er heute findet.

Ein Begriff, nach dem oft gesucht wird und der nichts findet, deutet auf fehlende Inhalte hin oder auf Wörter, die die
Inhalte nicht verwenden: Ergänzen Sie die Inhalte oder die Wörter (ein Synonym im Text oder in den Schlüsselwörtern).

**Statistik zurücksetzen** öffnet direkt auf der Seite eine Bestätigung, die sagt, wie viele Begriffe entfernt
werden, und entfernt sie alle endgültig.

Suchbegriffe sind das, was Besucher eingegeben haben. Sie können Namen oder andere personenbezogene Daten enthalten;
behandeln Sie die Seite entsprechend und setzen Sie sie zurück, wenn die Zahlen nicht mehr gebraucht werden.

## 6. Adressen und Parameter der Seiten

| Seite | Adresse | Parameter |
|---|---|---|
| Linkliste | `/url/list/<all\|valid\|invalid\|unchecked>` | `(sort)/checked`, `(sort)/modified`, `(offset)/<n>`, `(q)/<text>` |
| Globale Aliase | `/content/urltranslator` | `(kind)/redirect`, `(kind)/direct`, `(offset)/<n>`, `(q)/<text>` |
| Platzhalter | `/content/urlwildcards` | `(kind)/...`, `(offset)/<n>`, `(q)/<text>`; das Formular Ausprobieren sendet `test` (eine Adresse) per POST |
| Suchstatistiken | `/search/stats` | `(sort)/phrase`, `(sort)/fewest`, `(show)/none`, `(offset)/<n>`, `(q)/<text>` |

Unbekannte Werte fallen auf die Voreinstellungen zurück. Die Formulare, ihre Feldnamen und ihre Schaltflächen
(`SetValid`, `SetInvalid`, `URLSelection[]`, `NewAliasButton`, `RemoveAliasButton`, `RemoveAllAliasesButton`,
`ElementList[]`, `NewWildcardButton`, `RemoveWildcardButton`, `RemoveAllWildcardsButton`, `WildcardIDList[]`,
`ResetSearchStatsButton`) sind dieselben, die die Seiten schon immer hatten, und jedes POST-Formular trägt das
Formular-Token.

## 7. Wenn sich etwas nicht wie erwartet verhält

| Sie sehen | Warum, und was zu tun ist |
|---|---|
| Die meisten Links zeigen **Nie geprüft** | die Linkprüfung ist für sie noch nicht gelaufen: Führen Sie den Cronjob-Teil `infrequent` aus |
| Ein Link, der im Browser funktioniert, bleibt nach einem Lauf, wie er war | die Site hat der Linkprüfung mit 401, 403, 429 oder 503 geantwortet (verweigert oder ausgelastet), was nichts entscheidet; die Zeile des Laufs nennt den Grund |
| Ein Link auf eine Intranet-Seite wird nie getestet | sein Host liegt in einem privaten Netz; tragen Sie ihn in `AllowedPrivateHosts[]` ein |
| Ein Link auf Inhalt ist **Ungültig** | sein Knoten oder Objekt wurde entfernt, ist verborgen oder nicht veröffentlicht; die Zeile des Laufs nennt den Knoten oder das Objekt |
| Ein Link auf `/admin/...` oder das Modul einer Extension ist **Ungültig** | das Modul ist nur in diesem Siteaccess aktiv: Führen Sie den Teil mit `-s` dieses Siteaccess aus oder ignorieren Sie den Zustand |
| Ein Lauf dauert lange | der größte Teil ist `HostDelay` zwischen Links auf denselben Host; senken Sie den Wert vorsichtig, oder setzen Sie `MaxURLsPerRun`, um die Links auf mehrere Läufe zu verteilen |
| Die Suchstatistiken bleiben leer | kein Siteaccess hat `LogSearchStats=enabled`; die Seite sagt das oben |
| Ein neuer Alias „wurde vom System geändert“ | in einer Adresse unzulässige Zeichen wurden ersetzt; die Meldung zeigt das Ergebnis |
| Ein neuer Alias auf einen Knoten erscheint nicht in der Liste | Aliase von Knoten stehen auf dem Reiter URL-Aliase des Knotens |
| Ein Platzhalter bewirkt nichts | ein URL-Alias passt zuerst auf die Adresse, oder ein älterer Platzhalter passt auf sie; verwenden Sie **Eine Adresse ausprobieren** |
| Der Platzhalter wird mit „Das Ziel verwendet {2}“ abgelehnt | das Muster hat weniger `*`, als das Ziel braucht: Ergänzen Sie ein `*` oder entfernen Sie den Platzhalter |

## 8. Adressen dekodieren und bereinigen: zwei optionale Einstellungen

Zwei Einstellungen von `site.ini [URLTranslator]` ändern, wie Adressen gelesen und wie neue URL-Aliase geschrieben
werden. Beide sind im Auslieferungszustand ausgeschaltet, bei neuen Installationen wie nach einem Upgrade, sodass
sich nichts ändert, bis Sie sie einschalten. Beide stammen aus LoveStack, der Distribution desselben Kernels von
Mugo Web, mit Dank (Commits `f435c2f91e` und `7517c0e925`).

```ini
# settings/override/site.ini.append.php
[URLTranslator]
# decode the address of a request once instead of twice
DecodeOnce=enabled
# IRI aliases: treat the double quote as a reserved character
TrailingCleanup=enabled
```

**DecodeOnce.** Die Adresse einer Anfrage wird zu Beginn der Anfrage dekodiert und wurde ein zweites Mal dekodiert,
wenn der Kernel sie in Modul, View und Parameter zerlegte. Ein kodiertes `+` in einem Pfad kam deshalb als
Leerzeichen an: `/(tag)/bar%2Bbar` gab dem View-Parameter `tag` den Wert `bar bar`, und `%2525` wurde zu `%`. Mit
`DecodeOnce=enabled` wird die Adresse einmal dekodiert: `bar+bar`, und `%2525` bleibt `%25`.

- Was ein Redakteur sieht: nichts, es sei denn, ein Link wurde mit einem kodierten `+` oder `%` in einem
  View-Parameter oder einem Pfad geschrieben.
- Was ein Administrator vor dem Einschalten prüft: Links in Templates oder Inhalten, die sich auf die zweite
  Dekodierung verlassen haben (ein Parameter, geschrieben als `a%2520b` im Sinne von `a b`). URL-Aliase selbst sind
  nicht betroffen: Ihr Text enthält in den Transformationen `urlalias` und `urlalias_compat` nie ein `+` oder `%`,
  und auf der Demonstrationsinstallation führt jede ihrer 696 Knotenadressen mit aus- und eingeschalteter
  Einstellung zum selben Ergebnis.
- Setzen Sie sie in `settings/override/site.ini.append.php`, nicht in einem Siteaccess: Die Adresse wird gelesen,
  bevor der Siteaccess bekannt ist. Sie funktioniert unter PHP-FPM und der Velocity-Engine gleich, die dem Kernel die
  Adresse so übergeben, wie der Browser sie gesendet hat; auch die Schlüssel des Antwort-Caches werden aus dieser
  Adresse gebildet und ändern sich daher nicht.

**TrailingCleanup.** Mit der IRI-Transformation (`TransformationGroup=urlalias_iri`, Aliase, die Akzente und andere
Buchstaben behalten) blieb das doppelte Anführungszeichen in einem Alias erhalten: Ein Artikel namens
`Lecture "Storm surges"` bekam den Alias `Lecture-"Storm-surges"`. Mit `TrailingCleanup=enabled` ist das doppelte
Anführungszeichen ein reserviertes Zeichen wie `?` oder `+`: Es wird zu einem Worttrenner, und Trenner, Punkte und
`!` werden am Anfang und am Ende entfernt, sodass der Alias `Lecture-Storm-surges` lautet. Die Transformationen
`urlalias` und `urlalias_compat` entfernen das doppelte Anführungszeichen bereits und entfernen `!`, Punkte und
Trenner am Ende ohnehin, für sie ändert die Einstellung also nichts.

- Was sich bei einem Upgrade ändert: nichts. Bestehende Aliase bleiben, wie sie sind; ein Objekt erhält den neuen
  Alias, wenn es erneut veröffentlicht wird (oder wenn `bin/php/updateniceurls.php` die Aliase neu aufbaut), und der
  alte Alias bleibt dann als Weiterleitung auf den neuen bestehen, wie bei jedem umbenannten Objekt.
- Auf einer Kopie der Datenbank der Demonstrationsinstallation, umgestellt auf `urlalias_iri`, änderte das erneute
  Veröffentlichen jedes Knotens mit eingeschalteter Einstellung 6 von 2942 Aliasen, allesamt Namen mit doppelten
  Anführungszeichen.

## 9. View-Parameter: wie ein Wert in der Adresse reist

Die Optionen der Administrationsansichten sind View-Parameter, `/(name)/wert` (eine Suche, ein Filter, eine
Sortierung). Manche Zeichen können in einem solchen Wert nicht unverändert reisen: Apache lehnt einen kodierten
Schrägstrich (`%2F`) ab, der Kernel dekodiert die ganze Adresse (zweimal, solange `DecodeOnce` nicht eingeschaltet
ist), bevor er sie an jedem `/` teilt, und ein Segment `(name)` beginnt den nächsten View-Parameter. Jede Ansicht
schreibt ihre Werte deshalb in einer einzigen umkehrbaren Kodierung, sodass eine Suche nach `example.com/a` ihren
Schrägstrich behält:

| Zeichen | Geschrieben als |
|---|---|
| `/` | `~2F` |
| `(` und `)` | `~28` und `~29` |
| `~` (das Escape-Zeichen selbst) | `~7E` |
| `%` und `+` | `~25` und `~2B` (die zweite Dekodierung würde sie ändern) |
| `\`, `?`, `#` | `~5C`, `~3F`, `~23` |
| Steuerzeichen (Tabulator, Zeilenumbruch) | `~09`, `~0A` ... |
| ein Wert, der nur `.` oder `..` ist | `~2E`, `~2E~2E` (ein Browser würde ihn auflösen) |

Alles andere bleibt lesbar: Buchstaben, Ziffern und `, : @ = ! *` werden unverändert geschrieben, Leerzeichen und
anderer Text wie üblich prozentkodiert, was ein Browser als Text anzeigt. Beispiele:

```
/url/list/all/(q)/example.com~2Fa
/contentfinder/search/(text)/var~2Fsite~2Fstorage~2Fimages~2Fa~2Fb%20~281~29.jpg
/setup/logs/view/error/(level)/error,warning/(search)/not%20found/(from)/2026-10-17%2010:00
/visual/templatelist/(filter)/content~2Fview
```

Früher geschriebene Links funktionieren weiter: Der Inhalts-Finder und die Einstellungsseite lesen ihre Form
`~` + base64url weiterhin, die Form `~XX` des Log-Betrachters und der Template-Liste (ein Leerzeichen war `~20`) ist
dieselbe Kodierung, und Paketliste, Paketvergleich und Paketbrowser lesen ihre zweimal kodierte Suche
(`demo%2520content`) weiterhin.

- In einem Template schreibt der Operator `view_parameter` einen Wert:
  `{concat( 'settings/view/', $siteaccess, '/', $file, '/(q)/', $name|view_parameter )|ezurl}`. Nie `|urlencode`:
  Er überlässt `/` Apache und `+` der zweiten Dekodierung.
- In einem Skript der Administrationsdesigns schreibt `window.a4ViewParameter.encode( wert )` (`a4_url.js`, im
  Seitenkopf von admin, admin3 und admin4 geladen) dasselbe; `window.a4ViewParameter.decode()` ist die Umkehrung.
- In PHP schreibt `expViewParameters::suffix( array( 'q' => $text ) )` `/(q)/<wert>`, `expViewParameters::encode()`
  einen Wert, und `expViewParameters::get()` und `text()` lesen ihn dekodiert zurück (`raw()` liefert ihn, wie der
  Kernel ihn übergab). Getestet in `tests/tests/kernel/classes/expViewParametersTest.php` und
  `tests/tests/design/AdminViewParametersOnlyTest.php` (VP-05, VP-06).

## Für Entwickler

- Ein Link innerhalb der Site in einem Aufruf: `eZURI::build( '/content/view/full/2' )` liefert den Pfad, den der
  Template-Operator `ezurl` liefert (Indexdatei und, bei einem Siteaccess in der URL, dessen Namen davor),
  `eZURI::build( $url, true )` die absolute URL mit Schema und Host. Wie bei `ezurl` ist das Ergebnis für HTML
  maskiert; übergeben Sie als drittes Argument `false` für eine URL, die in einen Header oder eine JSON-Antwort geht.
  Ein Link mit Schema (`https:`, `mailto:`) kommt unverändert zurück. Getestet in
  `tests/tests/lib/ezutils/eZURIBuildTest.php`. Mit Dank an Mugo Web (LoveStack, Commit `f2284944b2`).
- `new eZURI( $uri, $decode = null )` und `setURIString( $uri, $fullInitialize = true, $decode = null )`: `true` für
  eine URL-kodierte Zeichenkette, `false` für dekodierten Text, `null` (die Voreinstellung), um `DecodeOnce` zu folgen
  (dekodiert, sofern es nicht eingeschaltet ist). `eZURI::decodeOnce()` sagt, ob es eingeschaltet ist;
  `eZURI::decodeIRI()` und `decodeURL()` dekodieren wie bisher. `eZCharTransform::trailingCleanup()` sagt, ob
  `TrailingCleanup` eingeschaltet ist. Beide Einstellungen werden bei jedem Aufruf gelesen, sodass ein Velocity-Worker
  eine Änderung nach `--clear-tag=ini` sieht. Getestet in `tests/tests/lib/ezutils/eZSysRequestURIDecodeOnceTest.php`
  und `tests/tests/lib/ezi18n/eZCharTransformTrailingCleanupTest.php`.
- Die Views sind `\Exponential\View\Kernel\Url\ListView`, `\Exponential\View\Kernel\Search\Stats`,
  `\Exponential\View\Kernel\Content\UrlaliasGlobal` und `\Exponential\View\Kernel\Content\UrlaliasWildcard`
  (`kernel/private/classes/views/...`). Ihre statischen Hilfsfunktionen (Modi, Sortierungen, der Suchtext, die
  Verwendung, die Linkart, Alias-Ziele, das Abgleichen von Platzhaltern und die Platzhalter im Ziel) werden ohne
  Datenbank in `tests/tests/kernel/classes/expUrlAdminPagesTest.php` getestet.
- `eZURL::fetchList()` / `fetchListCount()` nehmen `last_checked` (`never`, `checked`), `search` (mit
  `only_published`) und `sort` (`address`, `checked`, `modified`, `id`) entgegen, auf jeder Datenbank einschließlich
  MongoDB.
- `eZURLAliasQuery` hat die Eigenschaften `search` (das letzte Segment oder die Aktion enthält den Text) und
  `redirects` (`alias_redirects`); `eZURLWildcard::fetchFilteredList()` und `fetchFilteredListCount()` durchsuchen
  Muster und Ziel und filtern nach Typ. Beides sind Bedingungen der SQL-Abfrage bzw. ein `$match` auf MongoDB.
- `expLinkCheck` (`kernel/classes/explinkcheck.php`) entscheidet, ob ein Link funktioniert: `check( $url )` antwortet
  `valid`, `invalid` oder `unknown` mit einem Grund. Die Anfrage (`curlFetch()`), die DNS-Auflösung, die Inhalts-,
  Alias- und MX-Abfragen, die Uhr und das Warten sind Callables, die dem Konstruktor übergeben werden; so testet
  `tests/tests/kernel/classes/expLinkCheckTest.php` jede Entscheidung ohne Netzwerk und ohne Datenbank.
- Jede Variable, die die Templates hatten, ist weiterhin gesetzt; neu sind: `url_summary`, `url_usage`, `url_checks`,
  `url_search`, `url_sort`, `url_feedback`, `limit`, `limit_choices` (url/list); `search_stats_*`,
  `search_total_count` (search/stats); `alias_list`, `alias_count`, `alias_total_count`, `alias_info`,
  `alias_search`, `alias_kind`, `alias_form` (urltranslator); `wildcards_total_count`, `wildcard_search`,
  `wildcard_kind`, `wildcard_test`, `wildcard_test_result` (urlwildcards). Die Templates fallen auf die alten
  Variablen zurück, wenn eine View sie nicht setzt.
- Das Aussehen stammt aus `design/admin{,4}/templates/url/exp_style.tpl` und `search/exp_style.tpl`, auf die Seite
  beschränkt; kein gemeinsames Stylesheet wurde geändert.

## Quellen

- `kernel/private/classes/views/url/list.php`, `kernel/private/classes/views/search/stats.php`,
  `kernel/private/classes/views/content/urlalias_global.php`, `kernel/private/classes/views/content/urlalias_wildcard.php`
- `kernel/classes/datatypes/ezurl/ezurl.php` (`handleList()`, `listOrderSQL()`, `searchLikePattern()`)
- `kernel/private/classes/cronjobs/linkcheck.php` (der Teil für die Linkprüfung), `kernel/classes/explinkcheck.php`
  (ihre Entscheidungen), `settings/cronjob.ini [linkCheckSettings]`, `kernel/classes/ezurlwildcard.php` (Abgleich,
  `fetchFilteredList()`), `kernel/classes/ezurlaliasquery.php` (`search`, `redirects`)
- `design/admin4/templates/url/list.tpl`, `search/stats.tpl`, `content/urlalias_global.tpl`,
  `content/urlalias_wildcard.tpl` und ihre Kopien in `design/admin`
- `settings/site.ini [SearchSettings] LogSearchStats`, `settings/admininterface.ini [PaginationSettings]`
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md#6-october-2026-links-url-aliases-wildcards-and-search-statistics)
