# Der Inhaltsfinder

Lesen Sie diese Seite, wenn Sie ein Inhaltsobjekt finden müssen und nur eine ID oder einen Pfad haben (eine Objekt-ID aus
einem Protokoll, eine Knoten-ID aus einer URL, die Attribut-ID einer Fehlermeldung, den Pfad einer Bilddatei auf der
Festplatte), wenn Sie jede Version, jeden Ort und jeden Papierkorbeintrag davon sehen oder es von dort entfernen wollen,
und wenn Sie entscheiden, wer das darf.

## Kurz gefasst

- **Setup > Inhaltsfinder** (`/contentfinder/search`) und ein kleines Suchfeld unter der Inhaltsstruktur im Reiter
  Inhalt. Ein Suchfeld und eine optionale Auswahl, was der Text ist.
- Er findet das Objekt hinter einer Objekt-, Knoten-, Attribut- oder Versions-ID, einer Remote-ID, einem URL-Alias,
  einer Seiten-URL oder dem Pfad bzw. der URL einer Bild-, Binär- oder Mediendatei unterhalb von `var/`.
- Jedes Ergebnis zeigt Klasse, Name, Eigentümer, Sektion, Sprachen, Zustände, Remote-ID und Daten des Objekts; jede
  Version (ID, Nummer, Status, Sprache, Ersteller, Änderungsdatum, mit einem Link zur Versionsansicht); jeden Ort
  (Knoten-ID, Pfad, URL-Alias, Hauptort, verborgen); und, wenn es im Papierkorb liegt, wo es war, wann und von wem es
  in den Papierkorb gelegt wurde.
- Aus dem Ergebnis heraus: **In den Papierkorb**, **Endgültig löschen**, **Aus dem Papierkorb löschen**,
  **Wiederherstellen** und **Version entfernen**. Jede Aktion führt über eine Bestätigungsseite, die genau zeigt, was
  geschehen wird; was sich nicht rückgängig machen lässt, braucht ein zusätzliches Häkchen.
- Zwei Richtlinien: `contentfinder/search`, um ihn überhaupt zu verwenden, `contentfinder/delete` für seine Aktionen.
  Jedes Ergebnis zeigt und bietet nur, was die Inhaltsrichtlinien des Benutzers erlauben; etwas, das der Benutzer nicht
  sehen darf, wird genau wie ein Fehltreffer beantwortet.
- Nur auf Administrations-Siteaccesses. Jede Suche und jede Aktion wird im Audit festgehalten.

## Etwas finden

Geben Sie die ID, den Pfad oder die URL ein und klicken Sie auf **Finden**. Mit **Suchen nach: Automatisch erkennen**
(der Standard) entscheidet der Text:

| Sie geben ein | Es wird nachgeschlagen als |
|---|---|
| `4242` | eine Objekt-ID, eine Knoten-ID (im Baum und im Papierkorb), eine Attribut-ID und eine Versions-ID: jeder Treffer wird angezeigt |
| `node:4242`, `object:4242`, `attribute:…`, `version:…`, `remote:…`, `alias:…`, `file:…` (oder `n:`, `o:`, `a:`, `v:`, `r:`, `f:`) | nur dieser Typ |
| `https://www.example.com/admin/content/view/full/4242` | Knoten 4242 (jeder Ansichtsmodus, mit oder ohne Siteaccess) |
| `https://www.example.com/content/download/12/345/file/report.pdf` | Attribut 345 |
| `var/site/storage/images/news/summer/1234-2-eng-GB/summer_large.jpg` oder die vollständige URL dazu | die Bilddatei: das Attribut, das Objekt und seine Knoten |
| `var/site/storage/original/application/4c1b…e9.pdf` | die Binär- oder Mediendatei dieses Namens (und dieser MIME-Gruppe) |
| `var/site/storage/images/news/summer/` oder `…/original/application/*` | jede Datei unterhalb dieses Präfixes (bis zum Zeilenlimit) |
| `d41d8cd98f00b204e9800998ecf8427e` (32 hexadezimale Ziffern) | eine Remote-ID |
| `News/Summer-party`, `/News/Summer-party/`, `https://www.example.com/News/Summer-party?x=1` | ein URL-Alias oder eine Seitenadresse (exakt; siehe „URL-Aliasse und Siteaccesses“ weiter unten) |
| jedes andere Wort | eine Remote-ID und ein URL-Alias |

Wählen Sie in der Auswahl einen Typ, um den Text nur als diesen nachzuschlagen: **Objekt-ID**, **Knoten-ID**,
**Attribut-ID**, **Versions-ID**, **Remote-ID**, **URL-Alias**, **Dateipfad**.

Jeder Ergebnisblock beginnt mit **Gefunden über**: was gepasst hat. Bei einem Attribut oder einer Datei nennt er das
Attribut (ID, Bezeichner, Datentyp), die Version und die Sprache, in der der Treffer lag, sowie den Dateipfad.

Ein Bild wird einmal pro Alias gespeichert (`…_large.jpg`, `…_small.jpg`) und in jeder Version, die es behalten hat;
jeder dieser Pfade findet es. Ein Pfad zu einer Datei, auf die kein Attribut verweist (eine in `var/storage`
zurückgebliebene Waise), findet nichts.

### URL-Aliasse und Siteaccesses

Ein Siteaccess mit `site.ini [SiteAccessSettings] PathPrefix` zeigt seine Seiten ohne das Präfix: Der als
`fit-healthy/showcase/test-product` gespeicherte Alias ist die Seite `/site/showcase/test-product` des Siteaccess
`site` mit `PathPrefix=fit-healthy`. Der Finder nimmt alle drei an: Ein URL-Alias oder eine Seitenadresse wird exakt und
in dieser Reihenfolge versucht, bis eine Variante aufgelöst wird:

1. der Pfad wie eingegeben;
2. der Pfad als Seitenadresse: Der Siteaccess, den der Kernel dafür erkennt (`eZSiteAccess::match()`: über das erste
   Segment beim URI-Matching, über den Host einer vollständigen URL beim Host- oder Host-und-URI-Matching), wird um
   sein Segment gekürzt, und das eigene `PathPrefix` dieses Siteaccess wird vorangestellt, wie es `eZURLAliasML` tut
   (nicht, wenn der Pfad bereits damit beginnt, und nicht, wenn sein erstes Element in `PathPrefixExclude[]` steht);
   dann dasselbe ohne das Präfix;
3. ein erstes Segment, das einen Siteaccess benennt, wird entfernt und das Präfix dieses Siteaccess vorangestellt,
   dann ohne;
4. der Pfad mit jedem unterschiedlichen `PathPrefix` der Siteaccesses (`AvailableSiteAccessList[]` und
   `RelatedSiteAccessList[]`) davor.

5. wenn keine dieser Varianten aufgelöst wird: der **letzte Teil** eines Alias. Jedes Alias-Element, dessen eigener
   Text das letzte Element des Pfads ist (ohne Beachtung der Groß- und Kleinschreibung verglichen, wie `eZURLAliasML`
   vergleicht: über die indizierte Spalte `text` und nur dann, wenn das nichts findet, über `text_md5`, den MD5-Wert des
   kleingeschriebenen Textes; niemals mit `LIKE`), wird jeweils nach oben gegen den Rest des Pfads geprüft
   (`showcase/test-product` muss `showcase` direkt darüber haben). Jeder gefundene Knoten wird mit seinem
   vollständigen Pfad aufgeführt, bis zu `ResultLimit`; eine als Verlauf aufbewahrte alte Adresse wird nur dann
   aufgeführt (und markiert), wenn nichts Aktuelles passt. **Gefunden über** lautet „den letzten Teil des URL-Alias
   `<full path>`“. Dies ist die einzige Abfrage, die `ezurlalias_ml` ohne Index lesen darf (der Schritt über
   `text_md5`, wenn der Text in seiner Schreibweise keinem gespeicherten Text entspricht).

6. wenn auch das nichts findet und der Text ein einzelnes Element ist (kein Schrägstrich): ein **Teil** eines Alias.
   `product`, `test` und `test product` finden `fit-healthy/showcase/test-product`. Der Text wird an Bindestrichen,
   Unterstrichen und Leerzeichen in Wörter zerlegt; ein Alias-Element passt, wenn es die Wörter in dieser Reihenfolge
   mit je einem Bindestrich, Unterstrich oder Leerzeichen dazwischen enthält, ohne Beachtung der Groß- und
   Kleinschreibung. Die Abfrage ist ein einziges `LOWER( text ) LIKE '%…%' ESCAPE '!'` auf `ezurlalias_ml` (wobei
   `%`, `_` und `!` im Text maskiert werden, sodass sie nur für sich selbst stehen; bei MongoDB ein regulärer Ausdruck
   ohne Beachtung der Groß- und Kleinschreibung), und der Finder prüft danach jede Zeile exakt. Aktuelle Einträge
   werden zuerst genommen; alte Adressen (Verlauf) nur, wenn kein aktueller passt, und dann markiert. Die nächsten
   Treffer stehen vorn: das Element selbst, dann Elemente, die mit den Wörtern beginnen, dann die Wörter als ganze
   Wörter, dann am Anfang eines Wortes, dann innerhalb eines Wortes; höchstens `PartRowLimit` Elemente (2000) werden
   gelesen und höchstens `ResultLimit` Objekte angezeigt (die Seite sagt, wenn es mehr gab). **Gefunden über** lautet
   „einen Alias, der „product“ enthält (*vollständiger Pfad*)“. Ein Text kürzer als `MinimumPartLength`
   (3 Zeichen) wird nicht als Teil gesucht: Die Seite meldet „Geben Sie mindestens 3 Zeichen ein, um nach einem Teil
   eines Alias zu suchen“.

   Dies ist die einzige Abfrage, die die gesamte Alias-Tabelle liest. Gemessen an einer Kopie der Inhalte einer
   echten Installation (2833 Alias-Zeilen; `test` passt auf 1768 davon): SQLite 10 ms, MariaDB 61 ms, PostgreSQL
   55 ms, MongoDB 95 ms, Oracle 287 ms,
   jeweils beim Lesen jeder passenden Zeile. Sie läuft nur, wenn nichts anderes gepasst hat; wo die Tabelle sehr groß
   ist, schalten Sie sie mit `contentfinder.ini [ContentFinderSettings] PartialAliasSearch=disabled` ab.

Das Präfix jedes Siteaccess wird aus dessen eigener `site.ini` gelesen. **Gefunden über** nennt den Alias, der
aufgelöst wurde, und, wenn ein Siteaccess oder ein Präfix den Unterschied gemacht hat, welcher. Die Schritte 1 bis 5
sind exakte Abfragen, niemals ein `LIKE`; bei jedem Schritt entscheiden die Rechte pro Ergebnis wie bei jeder Abfrage.

### Was angenommen wird

- IDs sind ganze Zahlen von 1 bis 2147483647 (ein vorangestelltes `#` ist erlaubt). Alles andere ist keine ID:
  `1 OR 1=1` wird als Remote-ID und als URL-Alias nachgeschlagen, also als Text.
- Eine URL wird auf ihren Pfad reduziert: Schema, Host, Query und Fragment fallen weg; der Pfad wird einmal
  prozentdekodiert, und mehrfache Schrägstriche werden zu einem zusammengefasst. Nur URLs mit `http`, `https` und
  `ftp` werden angenommen.
- Ein Dateipfad muss unterhalb von `var/` liegen. Ein Pfad mit einem Segment `.` oder `..` (auch prozentkodiert),
  einem Backslash, einem NUL-Byte oder einem anderen Steuerzeichen wird abgewiesen. Ein Präfix (ein abschließendes `/`
  oder `*`) muss mindestens `var/<dir>/storage` benennen. Ein `*` an jeder anderen Stelle wird abgewiesen.
- Texte länger als 1024 Byte und Texte, die kein gültiges UTF-8 sind, werden abgewiesen.

### Wie er nachschlägt

Jede Abfrage ist eine Abfrage auf einer indizierten Spalte (oder ein exakter Vergleich auf einer kurzen), mit einem
festen Zeilenlimit: `ezcontentobject.id`, `ezcontentobject_tree.node_id`, `ezcontentobject_trash.node_id`,
`ezcontentobject_attribute.id`, `ezcontentobject_version.id`, die `remote_id` von Objekten, Knoten und Knoten im
Papierkorb, `eZURLAliasML::fetchNodeIDByPath()`, `ezimagefile.filepath` sowie `filename` und `mime_type` von
`ezbinaryfile` und `ezmedia`. Ganzzahlen werden umgewandelt, Texte von der Datenbank maskiert; ein Präfix ist
`LIKE 'prefix%' ESCAPE '!'`, wobei `%`, `_` und `!` im Text maskiert werden. Es gibt kein anderes
`LIKE '%text%'` als das für den Teil eines Alias (Schritt 6 oben). Remote-IDs und Dateipfade werden auf jeder
Datenbank-Engine unter Beachtung der Groß- und Kleinschreibung verglichen: Die Datenbank grenzt ein
(die Kollationen von MySQL und das `LIKE` von SQLite ignorieren die Schreibweise), und der Finder vergleicht danach den
gespeicherten Wert exakt. URL-Aliasse unterscheiden nicht zwischen Groß- und Kleinschreibung, so wie `eZURLAliasML`
sie auflöst.

Höchstens `ResultLimit` Objekte (25) werden für eine Suche beschrieben, und höchstens `FileRowLimit` Dateizeilen (50)
werden für einen Pfad oder ein Präfix gelesen; die Seite sagt, wenn es mehr gab.

## Die Adresse einer Suche

Eine Suche ist eine Adresse mit View-Parametern, `contentfinder/search/(type)/object/(text)/58`: `(text)` ist der
Text (unverändert, wenn er aus Buchstaben, Ziffern, Leerzeichen und `. _ : @ , + = -` besteht, sonst `~` und sein
base64url, wie bei einem Pfad oder einer URL), und `(type)` ist der Typ (keiner für *auto*). Das Formular wird per
POST gesendet und mit einem 303 auf diese Adresse beantwortet; ein Query-String wie `?SearchText=` wird nicht gelesen.

## Wer ihn verwenden darf

| Richtlinie | Was sie erlaubt |
|---|---|
| `contentfinder/search` | den Finder öffnen (der Setup-Eintrag und das Suchfeld erscheinen nur damit) und suchen |
| `contentfinder/delete` | die Aktionen des Finders; jede braucht zusätzlich ihre Inhaltsrichtlinien (siehe unten) |

In einer neuen Installation hat keine Rolle außer Administrator (die jede Richtlinie besitzt) diese Richtlinien. Geben
Sie `contentfinder/search` einer Support-Rolle; sie zeigt dieser Rolle nichts, was sie nicht auch in der
Inhaltsstruktur, im Papierkorb oder im Versionsverlauf sehen könnte. Geben Sie `contentfinder/delete` nur den Rollen,
die auch Inhalte entfernen.

### Was jedes Ergebnis zeigt

Der Finder zeigt nie mehr, als der Benutzer anderswo sehen darf. Er fragt die eigenen Prüfungen des Kernels ab:

| Teil | Angezeigt, wenn |
|---|---|
| das Objekt (Name, Klasse, Eigentümer, Sektion, Sprachen, Zustände, Remote-ID, Daten) | `content/read` auf dem Objekt (`eZContentObject::canRead()`) oder ein Papierkorbeintrag davon, den der Benutzer sehen darf |
| ein Ort | `content/read` auf diesem Knoten (`eZContentObjectTreeNode::canRead()`) |
| eine Version | `content/versionread` auf dieser Version (`eZContentObjectVersion::canVersionRead()`); die veröffentlichte Version auch für alle, die das Objekt lesen dürfen |
| ein Papierkorbeintrag | `content/restore` oder `content/cleantrash` sowie das eigene Berechtigungs-SQL der Papierkorbliste (`content/read` dort, wo der Knoten war) |
| ein Treffer über Knoten, URL-Alias oder Knoten-Remote-ID | dieser Knoten ist einer, den der Benutzer lesen darf |
| ein Treffer über Attribut, Dateipfad oder Versions-ID | die Version, in der der Treffer liegt, ist eine, die der Benutzer sehen darf |

Ein Treffer, den der Benutzer nicht sehen darf, wird verworfen. Bleibt nichts übrig, meldet die Seite **Nichts
gefunden, oder Sie haben keinen Zugriff auf das Gefundene.**, genau wie bei einer ID, die nicht existiert, sodass sich
IDs nicht ausforschen lassen. (Der Audit-Eintrag der Suche zählt die gefundenen, aber nicht angezeigten Objekte; nur
`audit/read` sieht das.)

### Die Aktionen

| Aktion | Braucht (neben `contentfinder/search` und `contentfinder/delete`) | Ausgeführt durch |
|---|---|---|
| In den Papierkorb | `content/read` und `content/remove` auf jedem Ort des Objekts und auf allem darunter; das Objekt kann in den Papierkorb (kein Benutzer darunter) | die Operation `content/delete` mit `move_to_trash` (wie **Entfernen** in der Inhaltsstruktur) |
| Endgültig löschen | wie In den Papierkorb | die Operation `content/delete` ohne Papierkorb |
| Aus dem Papierkorb löschen | der Papierkorbeintrag ist sichtbar; `content/cleantrash` und `content/remove` | das Löschen des Papierkorb-Dienstes (wie **Papierkorb leeren** für ein einzelnes Objekt) |
| Wiederherstellen | `content/restore` | die eigene Seite **content/restore** des Kernels, die der Button öffnet |
| Version entfernen | wer das Objekt bearbeiten darf; `content/versionremove` auf der Version; eine Version im Status Entwurf, archiviert, abgelehnt oder interner Entwurf (niemals die veröffentlichte oder eine, die ein Workflow hält) | `eZContentObjectVersion::removeThis()`, wie im Versionsverlauf |

- Jede Aktion ist ein POST von der Ergebnisseite und öffnet eine **Bestätigungsseite**, die genau aufführt, was
  geschehen wird: jeden Ort mit der Anzahl der Knoten darunter, die Anzahl der Versionen, andere Objekte, die darauf
  verweisen. Nichts geschieht, bevor dort auf **Bestätigen** geklickt wird.
- **Endgültig löschen**, **Aus dem Papierkorb löschen** und **Version entfernen** lassen sich nicht rückgängig machen:
  Die Bestätigung braucht zusätzlich das Häkchen **Mir ist klar, dass sich das nicht rückgängig machen lässt.**
- Die Bestätigung ist an die Sitzung des Benutzers, die Aktion, das Objekt und das gebunden, was die Seite gezeigt hat:
  Hat sich dazwischen etwas geändert (ein Knoten darunter hinzugefügt, das Objekt von jemand anderem in den Papierkorb
  gelegt), wird die Aktion mit **Das Objekt hat sich geändert, nachdem die Bestätigungsseite angezeigt wurde.**
  abgewiesen.
- Eine Entfernung mit mehr Knoten darunter als `content.ini [RemoveSettings] MaxNodesRemoveSubtree` (100) wird hier
  abgewiesen: Entfernen Sie sie in der Inhaltsstruktur, wo eine große Entfernung als Hintergrundauftrag läuft. Ein
  Teilbaum, an dem ein Inhaltsauftrag arbeitet, und einer, in dem etwas auf Freigabe wartet, werden ebenfalls
  abgewiesen.
- Nach der Aktion zeigt der Finder das Ergebnis und das Objekt in seinem jetzigen Zustand.

### Anfragen, die abgewiesen werden

- `contentfinder/action` antwortet auf alles außer POST mit **405** und auf ein POST ohne die Formular-Tokens mit
  **403**: das ezformtoken-Token (bei jedem POST geprüft und noch einmal von der Ansicht) und das eigene Token des
  Finders, das an die Sitzung und den Benutzer gebunden ist.
- Auf einem Siteaccess, der kein Administrations-Siteaccess ist, antwortet der Finder mit **404** (ein Benutzer ohne
  `contentfinder/search` bekommt zuerst die Seite „Zugriff verweigert“).

## Einstellungen

`settings/contentfinder.ini`, überschrieben in `settings/override/contentfinder.ini.append.php` oder einem Siteaccess:

```ini
[ContentFinderSettings]
# true | false: with false the finder answers 404 everywhere
Enabled=true
# The siteaccesses it answers on; empty: every siteaccess whose design list has an
# administration design (admin, admin2, admin3, admin4, admin4l, adminui, ...)
SiteAccessList[]
# At most this many objects per search (1 to 100)
ResultLimit=25
# At most this many file rows per path or prefix (1 to 500)
FileRowLimit=50
# The part of an alias: enabled | disabled, the shortest part, the most alias elements read
PartialAliasSearch=enabled
MinimumPartLength=3
PartRowLimit=2000
```

Der Setup-Eintrag ist `menu.ini [Leftmenu_setup] Links[contentfinder]` (mit
`PolicyList_contentfinder[]=contentfinder/search`); das Suchfeld ist `design:contentfinder/box.tpl`, eingebunden von
`parts/content/menu.tpl` der Designs admin und admin4 (und damit auch von admin2, admin3, admin4l und dem Siteaccess
editor, die diese verwenden). Ein Design mit eigenem `parts/content/menu.tpl` bindet das Suchfeld ein mit

```smarty
{include uri='design:contentfinder/box.tpl'}
```

## Das Audit

| Ereignis | Erfasst | Daten |
|---|---|---|
| `content.finder.search` | jede Suche (Kanal `content`, info) | der gewählte Typ, die versuchten Typen, der Text (bis zu 128 Zeichen), die angezeigten Objekte und die gefundenen, aber nicht angezeigten Objekte; `refused` mit dem Grund bei einem Text, der nicht angenommen wird |
| `content.finder.action` | jede ausgeführte Aktion (Kanal `content`, notice) und jede Abweisung durch die Aktionsansicht | das Objekt, das Ziel (die Knoten, der Papierkorbeintrag oder die Version), die Aktion, die Orte und die Knoten darunter; die eigenen Entfernungsereignisse des Kernels (`content.node.remove.trash`, `content.object.remove`, `content.object.purge`, `content.version.remove`) werden als seine Kinder erfasst |

Der Akteur ist der angemeldete Benutzer, wie bei jedem Audit-Ereignis. Siehe [das Prüfprotokoll](pruefprotokoll.md).

## Für Entwickler

- `expContentFinderInput::parse( $text, $type )`: die Erkennung, ohne Datenbank.
- `( new expContentFinder() )->search( $text, $type )`: die Ergebnisse für den aktuellen Benutzer;
  `expContentFinder::isAvailableHere()`, `expContentFinder::hasPolicy( $module, $function )`.
- `expContentFinderAction::plan( $action, $objectID, $versionID )` und `::execute( ..., $confirmToken )`.
- Die Ansichten sind `Exponential\View\Kernel\Contentfinder\Search` und `\Action` (`Action::decide()` ist die Prüfung
  der Anfrage, ohne Nebenwirkungen).
- Tests: `tests/tests/kernel/contentfinder/` (`--testsuite kernel-contentfinder`). `expContentFinderInputTest` braucht
  nichts; `expContentFinderSandboxTest` schreibt Inhalte, Benutzer und Rollen
  (`fixtures/expcontentfindertestfixtures.php`) und läuft deshalb nur in einer Wegwerfkopie einer Installation, die
  `EXP_CONTENTFINDER_SANDBOX` benennt; überall sonst wird er übersprungen.
  Auf einer anderen Engine als SQLite braucht er zusätzlich `EXP_CONTENTFINDER_SANDBOX_DATABASE`, das die
  Wegwerfdatenbank benennt (die `Database` der Einstellungen bzw. bei Oracle den `User`).
  `expContentFinderURLAliasTest` braucht nichts.

## Datenbanken

Jede Abfrage, die Rechte pro Ergebnis, die vier Aktionen und die Audit-Einträge sind auf jeder der folgenden Engines
getestet, mit `--testsuite kernel-contentfinder` und einer End-to-End-Prüfung im Browser, in einer Wegwerfdatenbank mit
einer Kopie der Inhalte einer echten Installation:

| Engine | Getestete Version | Hinweise |
|---|---|---|
| SQLite | 3.34.1 | |
| MySQL / MariaDB | MariaDB 10.5.29 | ihre Kollationen vergleichen ohne Beachtung der Groß- und Kleinschreibung: Remote-IDs und Dateipfade vergleicht der Finder exakt |
| PostgreSQL | 13.23 | `LIKE` vergleicht unter Beachtung der Schreibweise, wie der Finder; URL-Aliasse brauchen `digest()` (pgcrypto), das der Installer einrichtet |
| Oracle | Oracle AI Database 26ai Free (23.26.3), mit `extension/ezoracle` 2.3.5 | `LIKE … ESCAPE` und `LOWER()` wie überall; kein Unterschied gefunden |
| MongoDB | 8.3.11 | über den Treiber: `LIKE … ESCAPE`, `LOWER( column )` (das Laden der Benutzer) und URL-Alias-Pfade (`eZURLAliasML::fetchByPath()`, ein Element nach dem anderen statt des Joins) werden übersetzt; jede Abfrage des Finders betrifft nur eine Tabelle |

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
