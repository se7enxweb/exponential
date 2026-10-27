# Pakete: was sie enthalten, wo sie liegen und wie Sie sicher mit ihnen umgehen

Diese Anleitung erklärt die Paketseiten der Administrationsoberfläche (**Setup > Pakete**): was ein Paket ist,
wo Pakete aufbewahrt werden, wie die Liste, die Seite eines Pakets, das Hochladen, die Installations- und
Deinstallationsschritte, das Herunterladen und das Entfernen funktionieren, von welchen Paketen der Installer abhängt,
was geprüft wird, bevor ein Paket angenommen wird, und was zu tun ist, wenn sich etwas nicht wie erwartet verhält.

Sie richtet sich an Administratoren, die Klassen, Inhalte oder Extensions zwischen Installationen übertragen, und an
alle, die sich um die Pakete kümmern, aus denen neue Installationen gebaut werden. Jede Seite, jedes Feld und jede
Regel unten wurde am 6. Oktober 2026 mit dem Code dieses Repositorys abgeglichen; die Dateien sind unter [Quellen](#quellen) aufgeführt.

[Anleitungen](README.md) · Siehe auch: [Audit-Protokoll](../funktionen/6.0/pruefprotokoll.md) ·
[Seitenweise Anzeige in der Administrationsoberfläche](../funktionen/6.0/seitenweise-listen-im-admin.md) ·
[Sichere Weiterleitungen](../funktionen/6.0/sichere-weiterleitungen.md) · [Extensions](extensions.md)

## Kurz gesagt

- Ein **Paket** ist ein Verzeichnis mit einer `package.xml` und den Dateien, die sie nennt: Inhaltsklassen,
  Inhaltsobjekte, Extensions, Einstellungsdateien, Bilder und Dokumente. Ein Paket wird **installiert** (seine
  Elemente werden auf der Site angelegt), **importiert** (es wird nur aufbewahrt, wie ein Site-Paket) oder
  **exportiert** (als eine einzige `.ezpkg`-Datei geschrieben).
- Pakete werden in **Repositories** aufbewahrt, den Verzeichnissen von `var/storage/packages/`: `local` für Pakete,
  die hier erstellt oder importiert wurden, und ein Verzeichnis je Hersteller (`7x`, `ez-systems`, ...).
- **Der Setup-Assistent installiert neue Sites aus diesen Paketen, und die veröffentlichten Pakete werden aus ihnen
  gebaut.** Ein Paket, das der Installer als Quelle verwendet, ist überall als **Installer-Quelle** markiert, und das
  Entfernen eines solchen Pakets erfordert eine eigene Bestätigung.
- **Setup > Pakete** (`/package/list`) zeigt jedes Repository mit seinen Zahlen und eine Karte je Paket, mit einer
  Suche, Filtern nach Repository, Typ und Status, einer Sortierung und Seiten. Die Seite eines Pakets ist schreibgeschützt,
  bis Sie eine Schaltfläche drücken.
- Ein Upload wird geprüft, bevor irgendetwas davon geschrieben wird: Eine Datei, die kein Paket ist, die zu groß ist
  oder die einen Pfad außerhalb des Pakets, einen Link oder ein Gerät enthält, wird mit einer Begründung abgelehnt. Ein
  vorhandenes Paket wird nie überschrieben.
- **Das Entfernen eines Pakets löscht sein Verzeichnis; es deinstalliert das Paket nicht.** Was das Paket installiert
  hat, bleibt bestehen.

## Inhalt

- [1. Wo Pakete liegen](#1-wo-pakete-liegen)
- [2. Die Paketliste](#2-die-paketliste)
- [3. Die Seite eines Pakets](#3-die-seite-eines-pakets)
- [4. Installer-Quellen](#4-installer-quellen)
- [5. Ein Paket herunterladen](#5-ein-paket-herunterladen)
- [6. Ein Paket hochladen](#6-ein-paket-hochladen)
- [7. Installieren und deinstallieren](#7-installieren-und-deinstallieren)
- [8. Ein Paket erstellen](#8-ein-paket-erstellen)
- [9. Pakete entfernen](#9-pakete-entfernen)
- [10. Berechtigungen](#10-berechtigungen)
- [11. Über die Shell](#11-über-die-shell)
- [12. Fehlerbehebung](#12-fehlerbehebung)
- [Quellen](#quellen)

## 1. Wo Pakete liegen

```
var/storage/packages/          site.ini [FileSettings] StorageDir + package.ini [RepositorySettings] RepositoryDirectory
├── local/                     packages made by the creation wizards or uploaded without a vendor
├── 7x/                        package.ini [RepositorySettings] Vendor: the setup wizard's repository
│   ├── sevenx_classes/
│   │   ├── package.xml        the definition: name, version, type, maintainers, dependencies, install items
│   │   ├── ezcontentclass/    one XML file per class
│   │   └── .cache/            the kernel's parsed copy of package.xml (not part of the package)
│   └── ...
└── ez-systems/
```

Ein Verzeichnis eines Repositorys ohne `package.xml` ist kein Paket. Die Liste zählt solche Verzeichnisse je
Repository („Verzeichnisse ohne Paketdefinition“), zeigt sie aber nicht an: Es sind Überbleibsel von Exporten oder
Assistenten, die vorzeitig beendet wurden. Ein Verzeichnis, dessen `package.xml` nicht gelesen werden kann, wird in
einer Warnung über der Liste mit seinem Pfad aufgeführt, statt die Seite wie früher zum Absturz zu bringen.

Das Repository, in dem ein Paket beim Hochladen landet, ist das, welches sein `<vendor>` nennt, umgewandelt in einen
Verzeichnisnamen (`eZ systems` wird zu `ez-systems`); ein Paket ohne Hersteller kommt nach `local`.

## 2. Die Paketliste

**Setup > Pakete**, `/package/list` (alle Repositories) oder `/package/list/<repository>` (eines).

1. **Die Kennzahlen**: Pakete, installiert, nicht installiert, nur importiert, Installer-Quellen, Gesamtgröße und
   Dateien sowie nicht lesbare Verzeichnisse.
2. **Repositories**: eine Kachel je Repository mit seinen Paketen, seiner Größe, den Installationszuständen, den
   Installer-Quellen und den Überbleibseln. Das Repository des Setup-Assistenten ist markiert. Eine Kachel öffnet die
   Liste dieses Repositorys.
3. **Pakete finden**: eine Suche über Name, Zusammenfassung, Typ, Hersteller, Version, Betreuer und benötigte Pakete
   (jedes Wort muss passen), das Repository, den Typ, den Status (installiert, nicht installiert, nur importiert,
   keine Installationselemente, Installer-Quellen) und die Sortierung (Name, letzte Änderung, Größe, Version, Typ,
   Repository, Installationsstatus).
   **Anwenden** übernimmt die Auswahl in die Seitenadresse, sodass sie als Lesezeichen gespeichert werden kann und die Seiten sie beibehalten:
   `/package/list/7x/(search)/demo/(type)/contentobject/(sort)/size`. **Umkehren** dreht die Reihenfolge um.
4. **Die Karten**: Name und Version, Typ, Installationsstatus, **Installer-Quelle**, Zusammenfassung, Repository und
   Hersteller, Betreuer, die Pakete, die es benötigt (ein fehlendes ist markiert), und die Pakete, die es benötigen,
   Größe und Dateien (ohne den `.cache` des Kernels), die letzte Änderung (die neueste Datei seines Verzeichnisses) und
   wann es gepackt wurde.
   **Anzeigen** öffnet seine Seite, **Herunterladen** seine `.ezpkg`-Datei.
5. **Pro Seite** und die Seiten. Die angebotenen Größen sind `admininterface.ini [PaginationSettings]
   ItemsPerPageList_package_list` (10, 25, 50, 100, wenn nicht gesetzt); Ihre Auswahl wird als Präferenz gespeichert.
   Ohne Auswahl verwendet die Liste `ItemsPerPage[package/list]` (25). Siehe [seitenweise Anzeige](../funktionen/6.0/seitenweise-listen-im-admin.md).
6. **Ausgewählte entfernen**, **Neues Paket importieren** und **Neues Paket erstellen**, jeweils ausgegraut, wenn Ihre
   Rolle es nicht erlaubt.

Vor 6.0.15 zeigte die Liste nur das Repository `local`, sofern kein anderes gewählt war, sodass die eigenen Pakete
des Installers nicht zu sehen waren; die Seitennavigation verlor das Repository; und der Titel beim Entfernen lautete
„Remove section?“.

## 3. Die Seite eines Pakets

`/package/view/full/<name>` oder, für ein Paket außerhalb von `local`, `/package/view/full/<name>/<repository>`.
Nichts auf dieser Seite ändert etwas, bevor eine Schaltfläche gedrückt wird.

- **Die Titelzeile**: Name, Version, Typ, Installationsstatus, Installer-Quelle.
- **Die Aktionen**: Vergleichen (der Inhalt des Pakets gegen die Site, `package/compare`), Installieren oder Neu
  installieren, Herunterladen, Deinstallieren, Entfernen. Jede wird nur angezeigt, wenn Ihre Rolle sie erlaubt und das
  Paket sie ausführen kann; gibt es nichts zu installieren, sagt die Seite, warum.
- **Details**: Repository und Hersteller, Status, Lizenz, Betreuer, Dokumente, veröffentlicht, gepackt (Zeit und
  Host), die Exponential-Version, für die es erstellt wurde, was es benötigt und welche Pakete es benötigen, Größe und letzte
  Änderung sowie seine Beschreibung.
- **Was es enthält**: seine Inhaltsklassen, Inhalte (Installationselemente und Objektdateien), Extensions,
  Einstellungsdateien, weitere Installationselemente und eine Tabelle seiner Dateien nach Art mit ihren Größen.
- **Logbuch**, dann **Paketinhalt**: jede Datei, nach Art gefiltert und nach Pfad durchsuchbar, seitenweise, jede
  einsehbar (das erste Megabyte, XML formatiert, ein Inhaltsobjekt zusammengefasst) und herunterladbar. Ein
  JSON-Dokument wird nach seiner Art aufgeführt (Paketdefinition, Inhaltsobjekt, Inhaltsklasse) und als JSON oder als
  das XML angezeigt, das es abbildet, ein XML-Dokument ebenfalls als JSON; lange Zeilen werden umbrochen, mit einem
  Schalter **Nicht umbrechen**. PNG-, JPEG-, GIF- und WebP-Bilder werden direkt angezeigt, SVG nur als Text, jede
  andere Binärdatei als Karte mit ihrem Typ, ihrer Größe, ihrem SHA-256 und der Angabe, ob sie mit `package.json`
  übereinstimmt. Auch die Dokumente des Pakets (seine Lizenz) öffnen sich hier.
- **Prüfen**: prüft das Paket so, wie ein Upload geprüft wird (siehe unten), und zeigt, was geprüft wurde, oder jedes
  Problem.

Eine Adresse, die einen View-Modus ohne Template nennt (`/package/view/nosuchmode/...`), einen Paketnamen mit einem
Pfad darin (`..`, ein Schrägstrich) oder ein Repository, das der Speicher nicht hat, wird mit „nicht gefunden“
beantwortet.

## 4. Installer-Quellen

Ein Paket ist eine Installer-Quelle, wenn mindestens eine dieser Bedingungen zutrifft:

| Angezeigter Grund | Regel |
|---|---|
| Im Repository des Setup-Assistenten | Es liegt in `package.ini [RepositorySettings] Vendor` (`7x`); die veröffentlichten Pakete werden aus diesem Repository gebaut |
| Ein Site-Paket, das der Setup-Assistent anbietet | Sein Typ ist `site`; der Setup-Assistent listet jedes Site-Paket auf |
| Von einer anderen Installer-Quelle benötigt | Eine Installer-Quelle benötigt es (`<require type="ezpackage">`), direkt oder über andere |

Auf dem Demonstrationsserver markiert dies `sevenx_classes`, `sevenx_multisite` und `sevenx_multisite_democontent`. Die Karte hat einen blauen
Rand, die Seite des Pakets sagt, warum, und das Entfernen eines solchen Pakets erfordert zusätzlich, dass das Feld
„Ich verstehe, dass ... Installer-Quelle(n) gelöscht werden“ angekreuzt ist.

## 5. Ein Paket herunterladen

**Herunterladen** (in der Liste und auf der Seite eines Pakets) ist `GET /package/export/<name>[/<repository>]`; die
ältere Schaltfläche **In Datei exportieren** (`ExportButton`) sendet weiterhin an die Seite des Pakets und tut
dasselbe. Das Paket wird als gzip-komprimiertes tar (`<name>-<version>.ezpkg`) in Ihr eigenes Exportverzeichnis
(`var/site/cache/packages/export<user id>/`, das Cache-Verzeichnis von `site.ini [FileSettings] VarDir`) geschrieben, gesendet und wieder entfernt. Das Paket selbst wird nur gelesen.

**JSON** (auf einer Karte) und **Als JSON herunterladen** (auf der Seite eines Pakets) senden dasselbe Paket im
JSON-Format (`package.json`, `<name>-<version>.json.ezpkg`); **Herunterladen** dort ist das klassische Format. Der
View-Parameter ist `(format)/json` oder `(format)/xml`. Die Seite des Pakets zeigt das Format, in dem es gespeichert
ist. Beide Formate werden auf dieselbe Weise hochgeladen und installiert; siehe [das JSON-Paketformat](../bc/6.0/package-json-format.md).

Die Datei wird in Stücken von 256 KiB gesendet, nie vollständig eingelesen, nachdem jeder Ausgabepuffer der Seite
geschlossen wurde. Ein `Range`-Header (`bytes=a-b`, `bytes=a-`, `bytes=-n`) erhält `206 Partial Content`, ein Bereich
jenseits des Endes `416`. Unter Exponential Velocity sammelt die Engine die Antwort eines persistenten Workers, bevor
sie sie sendet, sodass ein sehr großes Paket dort für die Dauer des Downloads entsprechend viel Speicher kostet; der
Download ist auf beiden Servern Byte für Byte identisch. Die Anfrage endet außerhalb jedes `try`/`catch`, sodass
Velocitys Art, sie zu beenden, die Seite nicht an die Datei anhängen kann.

Dateien aus einem Paket (Herunterladen im Inhalt) kommen auf dieselbe Weise von
`/package/viewfile/<name>/<index>[/<repository>]`; ein Bild wird direkt angezeigt, alles andere wird als Download mit
`nosniff` und einer abschottenden Content-Security-Policy angeboten.

## 6. Ein Paket hochladen

**Neues Paket importieren** in der Liste öffnet `/package/upload`. Wählen Sie eine `.ezpkg`-, `.tar.gz`- oder
`.tgz`-Datei und drücken Sie **Importiere Paket**. Bevor irgendetwas geschrieben wird, wird das Archiv geprüft:

1. sein Name endet auf eine erlaubte Endung, und die Datei beginnt mit der gzip-Signatur (oder ist ein
   unkomprimiertes tar-Archiv);
2. es ist höchstens `MaxArchiveSize` Bytes groß und hat höchstens `MaxEntries` Einträge, die entpackt höchstens
   `MaxUnpackedSize` Bytes ergeben;
3. seine tar-Köpfe lassen sich lesen: Die V7-Archive von eZ Publish 3.x, die ustar- und GNU-Archive von 4.x und 5.x
   und von GNU tar sowie PAX-Archive (bsdtar unter macOS, GNU tar `--format=posix`) werden alle importiert;
4. jeder Eintrag hat einen relativen Pfad: kein `..`, kein absoluter Pfad, kein Backslash, kein Steuerzeichen. Ein
   harter oder symbolischer Link auf eine Datei des Pakets wird als Kopie davon importiert; ein Link aus dem Paket
   heraus, auf ein Verzeichnis oder ins Leere, ein Gerät und eine FIFO werden mit einem Hinweis übergangen (unter den
   Warnungen der Validierung aufgeführt), und der Rest des Pakets wird importiert;
5. es hat auf oberster Ebene eine `package.xml`, die wohlgeformt ist, `<package>` als Wurzel und keinen Dokumenttyp
   hat, ein gültiges Paket nennt (Buchstaben in Groß- oder Kleinschreibung, Ziffern, `_` und `-`, nicht mit `-` beginnend) und ihre Versions- und Paketierungsangaben enthält,
   und deren Hersteller einen Repository-Namen ergibt;
6. in keinem Repository existiert ein Paket dieses Namens.

Die Seite nennt die erste Regel, die nicht erfüllt war. Danach wird das Paket selbst validiert: jedes Dokument auf
Wohlgeformtheit, die JSON-Dokumente eines [JSON-Pakets](../bc/6.0/package-json-format.md) gegen sein Schema und die
Prüfsummen seiner `package.json`. Ein Paket, das durchfällt, wird mit der Liste dessen abgelehnt, was fehlgeschlagen
ist. Sind alle Bedingungen erfüllt, wird das Paket in sein Repository entpackt; bei einem Paket mit
Installationselementen öffnet sich als Nächstes sein Installationsschritt, bei jedem anderen seine Seite, und beide
zeigen das Validierungsergebnis bis zur nächsten Aktion am Paket. Entpackte Dateien erhalten den Modus 0644 und
Verzeichnisse 0755, was immer das Archiv angibt.

Die Grenzwerte mit ihren Standardwerten:

```ini
# settings/override/package.ini.append.php
[UploadSettings]
MaxArchiveSize=268435456
MaxUnpackedSize=2147483648
MaxEntries=50000
AllowedSuffixes[]
AllowedSuffixes[]=ezpkg
AllowedSuffixes[]=tar.gz
AllowedSuffixes[]=tgz
```

Die PHP-eigenen Grenzen `upload_max_filesize` und `post_max_size` greifen zuerst; die Seite zeigt das Limit des
Servers an.

Die Archivprüfung läuft auch innerhalb von `eZPackage::import()`, sodass der Upload des Setup-Assistenten und
`ezpm import` dieselben Archive ablehnen.

## 7. Installieren und deinstallieren

**Installieren** auf der Seite eines Pakets öffnet `/package/install/<name>`: die Liste dessen, was installiert wird,
mit **Paket installieren** und **Installation auslassen**. Bei einem bereits installierten Paket weist die Seite
darauf hin, dass eine erneute Installation jedes Element wiederholt. Jedes Element wird der Reihe nach installiert; ein
Element kann in einem eigenen Schritt fragen, wie ein Konflikt behandelt werden soll (zum Beispiel eine bereits
existierende Klasse).

Die Seite zeigt außerdem, **worauf die Installation hier trifft**: jedes Inhaltsobjekt des Pakets, nachgeschlagen über
seine Remote-ID (neu, vorhanden, ein Konflikt oder ohne seine Klasse). **Als Entwürfe installieren** schreibt die
Inhaltsobjekte stattdessen als Entwürfe: Nichts wird veröffentlicht, ein vorhandenes Objekt erhält einen neuen Entwurf
(oder bleibt unverändert), Klassen, Einstellungen und Design-Dateien werden nicht installiert, und die Ergebnisseite
verlinkt jeden Entwurf zur Prüfung. Siehe
[Als Entwürfe installieren](../bc/6.0/package-json-format.md#als-entwürfe-installieren).

Der Bericht führt jedes Objekt auf, mit dem, was die Installation damit tut, und wohin sein Hauptort kommt (ganz oben,
unter einen Knoten hier, unter einen Knoten des Pakets oder unter einen fehlenden), jeweils mit ihrer Anzahl.

**Im Hintergrund installieren** führt dieselbe Installation als [Inhaltsauftrag](../bc/6.0/inhaltsjobs.md) (`packageinstall`)
in Ihrem Namen und mit Ihren Auswahlen aus; die Seite schlägt dies für ein Paket mit mehr Objekten als
`[ContentJobSettings] SynchronousLimit` vor. Die Seite des Pakets zeigt dann den Fortschritt Objekt für Objekt und
listet die Entwürfe auf, wenn der Vorgang abgeschlossen ist. Eine Extension kann über das Ereignis
`package/install/drafts` auf jede Installation als Entwürfe reagieren ([das JSON-Paketformat](../bc/6.0/package-json-format.md#setup--pakete)).

**Deinstallieren** öffnet `/package/uninstall/<name>`: was die Deinstallation von der Site entfernt, Inhalte
eingeschlossen, mit **Deinstalliere Paket** und **Deinstallation auslassen**. Eine Deinstallation kann nicht
rückgängig gemacht werden.

Beide erfordern `package/install` für den Typ des Pakets (früher verlangten sie die Richtlinie für jeden Typ).

## 8. Ein Paket erstellen

**Neues Paket erstellen** öffnet `/package/create`: eine Kachel je Assistent, den Ihre Rolle verwenden darf
(Inhaltsobjekt-Export, Inhaltsklassen-Export, Extension-Export, Site-Stil). Der Assistent fragt nach den Bestandteilen,
dann nach Name, Zusammenfassung, Version, Lizenz und Betreuer sowie dem Format, in dem das Paket aufbewahrt wird
(klassisches XML oder JSON), und schreibt das Paket nach `local`. Auf der Site ändert sich nichts. Jeder Schritt zeigt,
wo Sie sich im Assistenten befinden, und markiert Fehler an ihren Feldern, und der
letzte Schritt zeigt das gesamte Paket zur Prüfung, bevor **Paket erstellen** es schreibt: siehe [Der
Paket-Assistent](../funktionen/6.0/paketassistent.md).

## 9. Pakete entfernen

1. Kreuzen Sie die Pakete in der Liste an (oder drücken Sie **Entfernen** auf der Seite eines Pakets) und drücken Sie
   **Ausgewählte entfernen**.
2. Die Bestätigung listet auf, **was entfernt wird**: je Paket sein Verzeichnis, seine Größe und Dateien, ob es
   installiert ist, welche Pakete es benötigen und warum es eine Installer-Quelle ist. Über der Liste: wie viele
   Pakete, Dateien und Bytes, installierte Pakete, Installer-Quellen und Pakete, die von verbleibenden Paketen benötigt
   werden.
3. Alles Ausgewählte, das nicht entfernt werden kann, wird mit seinem Grund aufgeführt: kein Paketname, kein solches
   Paket, ein Name in mehr als einem Repository, für seinen Typ nicht erlaubt oder ein Verzeichnis, das Links enthält.
4. Drücken Sie **n Paket(e) entfernen**; sind Installer-Quellen darunter, kreuzen Sie zuerst das Feld an.

Das Verzeichnis wird mit allem darin gelöscht. Das Paket wird nicht deinstalliert: Klassen, Inhalte und Dateien, die
es installiert hat, bleiben bestehen; nur der Eintrag, dass es installiert ist, entfällt, sodass es von hier aus nicht
mehr deinstalliert werden kann. Laden Sie ein Paket zuerst herunter, um eine Kopie zu behalten. Jedes Entfernen wird
im Audit-Protokoll als `system.package.remove` festgehalten, mit Name, Version, Repository, Dateien und Bytes des
Pakets sowie der Angabe, ob es installiert und ob es eine Installer-Quelle war.

Nur was die Bestätigungsseite angeboten hat, kann entfernt werden: Ein `ConfirmRemovePackageButton`, der ohne sie
gesendet wird, entfernt nichts. Ein Paket, dessen Verzeichnis einen symbolischen Link enthält, wird nie angeboten:
Das rekursive Löschen des Kernels folgt einem Link auf ein Verzeichnis und würde dessen Ziel leeren.

## 10. Berechtigungen

| Aktion | Richtlinie | Eingeschränkt durch |
|---|---|---|
| Die Liste sehen | `package/list` | |
| Die Seite eines Pakets, seine Dateien, Vergleichen | `package/read` | Typ |
| Herunterladen | `package/export` | Typ |
| Hochladen | `package/import` | |
| Installieren, Neu installieren, Deinstallieren | `package/install` | Typ |
| Entfernen | `package/remove` | Typ |
| Erstellen | `package/create` | Typ, Ersteller, Rolle |

Jedes Formular trägt das Formular-Token; ein Absenden ohne es wird mit 403 abgelehnt.

## 11. Über die Shell

```bash
php ezpm.php list --allow-root-user                            # the packages of the repositories
php ezpm.php export sevenx_classes -d var/tmp --allow-root-user  # write an .ezpkg (only reads the package)
php ezpm.php import var/tmp/x.ezpkg --allow-root-user            # the same archive checks as the upload page
php ezpm.php help --allow-root-user                            # every command
```

## 12. Fehlerbehebung

| Sie sehen | Warum | Was zu tun ist |
|---|---|---|
| Ein Paket fehlt in der Liste | Seine `package.xml` kann nicht gelesen werden oder nennt ein anderes Paket als sein Verzeichnis | Die Warnung über der Liste nennt das Verzeichnis; reparieren oder ersetzen Sie die `package.xml` |
| „N Verzeichnisse ohne Paketdefinition“ | Überbleibsel von Exporten oder Assistenten, die vorzeitig beendet wurden | Sehen Sie sie sich auf dem Server an; nichts liest sie |
| Upload: „zeigt aus dem Paket heraus“ oder „ist ein Link oder ein Gerät“ | Das Archiv wurde mit Links oder unsicheren Pfaden erstellt | Erstellen Sie das Archiv erneut aus dem Paketverzeichnis (`ezpm export`) |
| Upload: „keine wohlgeformte Paketdefinition“ | `package.xml` ist defekt | Prüfen Sie sie mit `xmllint --noout package.xml` |
| Upload: „existiert bereits“ | Ein Paket dieses Namens liegt in einem Repository | Entfernen Sie zuerst das alte oder geben Sie dem neuen einen anderen Namen |
| Herunterladen antwortet „nicht gefunden“ | Der Export ist fehlgeschlagen (die Debug-Ausgabe sagt, warum) | Prüfen Sie, ob `var/site/cache/packages/` (das Cache-Verzeichnis) für den Webserver und Velocity beschreibbar ist |
| Entfernen ist für ein Paket ausgegraut | Sein Verzeichnis enthält einen Link | Entfernen Sie es auf dem Server, nachdem Sie geprüft haben, wohin der Link zeigt |

## Quellen

- Views: `kernel/private/classes/views/package/list.php`, `view.php`, `export.php`, `viewfile.php`, `upload.php`,
  `install.php`, `uninstall.php`; Moduldefinition `kernel/package/module.php`.
- Klassen: `eZPackageCatalog` (Repositories, Karten, Installer-Quellen, Suche, Sortierung, Inhalt),
  `eZPackageRemovalPlan`, `eZPackageUploadInspector`, `eZPackageDownload`, `eZPackageRequestGuard`,
  `eZPackageFileBrowser`, `eZPackage` (alle in `kernel/classes/`).
- Templates: `design/admin/templates/package/` und `design/admin4/templates/package/` (`exp_style.tpl`, `list.tpl`,
  `confirmremove.tpl`, `view/full.tpl`, `upload.tpl`, `install.tpl`, `uninstall.tpl`, `create.tpl`).
- Tests (ohne Datenbank): `tests/tests/kernel/classes/packages/eZPackageCatalogTest.php`,
  `eZPackageUploadInspectorTest.php`, `eZPackageRequestGuardTest.php`, `eZPackageDownloadTest.php`.
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md#6-october-2026-the-package-pages)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
