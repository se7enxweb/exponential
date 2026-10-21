# Eine saubere Installation, die zu Shared Hosting passt und Exponential sagt

Diese Seite ist für alle, die Exponential 6.0.15 installieren, besonders auf Shared Hosting. Juli und August 2026 haben
den ersten Start einer neuen Installation reibungsloser gemacht. Unten steht, was Sie jetzt bekommen, in der
Reihenfolge, in der Sie ihm begegnen.

## 1. Der Einrichtungsassistent arbeitet nur mit Ihrer eigenen Datenbank (16. Juli)

Commit `fdf0d52028`. Der Assistent führte früher `SHOW DATABASES` aus und öffnete die Systemdatenbank `mysql`, was
Rechte braucht, die ein Shared Host nicht vergibt. Jetzt brauchen Sie nur Rechte auf Ihrer eigenen Datenbank.

- Für MySQL und MariaDB hat der Datenbankschritt ein Feld **Database name**. Der eingegebene Name wird direkt
  verwendet; der Assistent sieht sich keine anderen Datenbanken an.
- Eine fehlgeschlagene Verbindung, ein falsches Passwort oder eine fehlende Datenbank werden als gewöhnliche Meldung auf
  der Seite gezeigt. Sie enden nicht mehr in einer leeren Fehlerseite.
- Die Einzelheiten gehen nach `var/log/setup.log` (zum Beispiel *eZStepSiteDetails: database requirement check failed
  (error code ...) for server ... user ... db ...*), nie das Passwort.
- Die Fortschrittsanzeige scheitert in einer Webanfrage nicht mehr daran, dass die Konstante `STDOUT` außerhalb der
  Kommandozeile nicht existiert.

Lehnt der Assistent Ihre Datenbank ab, lesen Sie den Grund:

```bash
tail -n 20 var/log/setup.log
```

## 2. Pakete bringen mit, was sie brauchen (29. Juli)

Commit `cb57cdd026`. `eZPackage::install()` installiert zuerst jedes Paket, das im Paket unter *requires* genannt ist,
und dann das Paket selbst.

- Ein Website-Paket, das „nur importiert“ (es trägt keine eigenen Dateien), zieht trotzdem sein Bild- oder
  Demo-Inhaltspaket nach.
- Ein benötigtes Paket, das sich nicht finden lässt, protokolliert eine Warnung; eines, das scheitert, hält die
  Installation an.
- Ein Paket, das bereits installiert wird, wird übersprungen, sodass zirkuläre Anforderungen keine Schleife bilden.

## 3. Die Standardinhalte sagen Exponential und sprechen amerikanisches Englisch (15. August)

Die Commits `4d9f7681b2`, `3dc808f3ec`, `71045a6cd8`, `e0f6916a36`, `ded9116491`, `5f6edbd111`, `3ebaf8dcd9` ändern die
Startdaten in `share/db_data.dba`, die der Installer vor einem Website-Paket lädt:

| Was | Vorher | Nachher |
|---|---|---|
| Standard-Inhaltssprache | `eng-GB` (Englisch, Vereinigtes Königreich) | `eng-US` (Englisch, Vereinigte Staaten), auch für jeden Namen von Klasse, Attribut und Objekt |
| Name des Wurzelordners | der alte Produktname | `Websites` |
| Begrüßungsartikel | der alte Produktname | `Welcome to Exponential` |
| Beschreibung und Metadaten der Wurzel | Produkttext des früheren Herstellers | eine Einführung in Exponential mit Links zu exponential.earth, share.exponential.earth (Artikel, Foren, Support), software.se7enx.com (Downloads), projects.exponential.earth und dem Wiki ezpedia.exponential.earth |
| Autor, Copyright | früherer Hersteller | Exponential Foundation |
| Schlüsselwörter des URL-Alias | früherer Produktname | `exponential`, `welcome to exponential` |

Die Startdaten betreffen die Tabellen `ezcontent_language`, `ezcontentclass`, `ezcontentclass_attribute`,
`ezcontentobject_attribute` und `ezcontentobject_name`. Alle Links in der Beschreibung wurden geprüft, ob sie antworten,
bevor sie aufgenommen wurden.

Wenn Sie mit einem Website-Paket installieren, kommen die eigenen Inhalte des Pakets nach den Startdaten und gewinnen,
wo sie sich überschneiden.

## 4. Namen beim Kickstart (15. August)

Alle Zugangstypen einer Kickstart-Installation registrieren die Siteaccesses `site` und `admin`; siehe
[Kickstarter](kickstarter-cli.md).

## 5. PHP 8 und andere Hindernisse bei der Installation (15. August, 31. Juli)

- Das Deserialisieren von Werten von `ezinteger`, `ezobjectrelation`, `ezobjectrelationlist` und `ezxmltext` toleriert
  fehlende XML-Knoten und Nullwerte (`28d414f422`).
- libxml-Fehler beim Parsen von XML-Text werden unterdrückt; eine eingebettete `object_remote_id` wird in der
  XHTML-Ausgabe zu einer Objekt-ID aufgelöst.
- Die Caches für URL-Aliase und Klassenattribute ignorieren Null- und Leerwerte.
- Eine Inhaltsoperation gibt nur dann false zurück, wenn ein Knoten wirklich fehlt.
- `settings/transform.ini [search]` hat `Commands[]=lowercase`, was der Suchgruppe die Umwandlung in Kleinbuchstaben
  hinzufügt.
- `eZPersistentObject` gibt unter PHP 8.4 eine Ganzzahl zurück, wenn es die nächste Ordnungsnummer berechnet
  (`9cad41280a`), was Installationen zerstört hatte.

## 6. Der Export von Inhaltspaketen ist nachsichtiger (7. August)

Ein Knoten, dessen Ausgangssprache fehlt oder dessen serialisierter Attributknoten fehlt, wird vom Export von
Inhaltspaketen übersprungen, statt den Lauf zu beenden (`752adad843`, `8af48c1e59`). Siehe
[ezpm](ezpm-paketmanager-cli.md).

## Verwandte Seiten

- [Installation mit einem Befehl: eine Website auf SQLite ohne `kickstart.ini`](../../../eng-us/features/6.0/install-in-one-command.md), [Kickstarter](kickstarter-cli.md), [Referenz der Kickstarter-CLI](../../../eng-us/bc/6.0/kickstartercli.md), [Einrichtungsassistent und Editor-Siteaccess](einrichtungsassistent-und-editor-siteaccess.md)
- [Was eine neue Installation mitbringt: die Standard-Extensions](standard-extension-auslieferung.md), [eine brauchbare robots.txt ab Werk](robots-txt.md), [Installationsname in Seitentiteln](installationsname-in-seiten.md)
- [Installer-Protokolle und Startdaten](../../spezifikationen/6.0/installer-protokolle-und-startdaten.md), [SQLite-Installer der Plattform](plattform-sqlite-installation.md)
- [Das Produkt heißt Exponential](umbenennung-in-exponential.md), [Seiten Über und Copyright](info-und-paketseiten.md), [Paketlizenzen und Semantic Versioning](paketlizenzen-und-versionen.md)
- Chronik: [Juli 2026](../../../eng-us/history/2026/2026-07.md), [August 2026](../../../eng-us/history/2026/2026-08.md), [September 2025](../../../eng-us/history/2025/2025-09.md), [August 2025](../../../eng-us/history/2025/2025-08.md), [Juli 2025](../../../eng-us/history/2025/2025-07.md), [Januar 2025](../../../eng-us/history/2025/2025-01.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
