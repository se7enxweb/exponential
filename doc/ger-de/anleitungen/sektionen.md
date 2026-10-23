# Sektionen: Inhalte für Berechtigungen, Navigation und Designs gruppieren

Diese Anleitung erklärt alles über Sektionen in Exponential: was eine Sektion ist und was nicht, die Seiten zu den
Sektionen in der Administrationsoberfläche (die Liste, eine einzelne Sektion, das Bearbeitungsformular, die
Bestätigung des Entfernens, das Zuweisen von Inhalten), wie für neue Inhalte eine Sektion gewählt wird und wie sie sich
ändert, wenn Inhalte verschoben werden, wie Rollen und Richtlinien Sektionen verwenden, was der Navigationsbereich
einer Sektion bewirkt, wie Templates nach Sektion gewählt werden, was sich in einer Shell oder aus einem anderen
Programm heraus tun lässt, drei vollständige Abläufe und was zu tun ist, wenn sich etwas nicht wie erwartet verhält.

Sie richtet sich an Administratoren, die festlegen, wer was sehen und bearbeiten darf, und an Site-Entwickler, die
einem Teil einer Site ein eigenes Aussehen geben. Lesen Sie zuerst die Abschnitte 1 und 2; danach steht jeder
Abschnitt für sich. Jede Seite, jedes Feld, jede Regel und jeder Befehl unten wurde am 5. Oktober 2026 am Code dieses
Repositorys geprüft; die Dateinamen stehen unter [Quellen](#quellen), damit sich jede Aussage erneut prüfen lässt.

[Anleitungen](README.md) · Siehe auch: [Inhaltsjobs](../funktionen/6.0/inhaltsauftraege.md) ·
[Remote-Dienste (expsection)](../bc/6.0/backend-dienste-ueber-ezjscore.md#sektionen-expsection-17-dienste-5-schreibvorgänge) ·
[Audit-Protokoll](../funktionen/6.0/pruefprotokoll.md) · [Sicherheit und Audit](sicherheit-und-audit.md)

## Kurz gesagt

- Eine **Sektion** ist eine benannte Gruppe von Inhaltsobjekten. Jedes Objekt gehört zu genau einer Sektion. Eine
  Sektion hat einen Namen, einen Bezeichner, eine ID und einen Navigationsbereich, sonst nichts.
- Sektionen werden an drei Stellen verwendet: bei **Berechtigungen** (eine Richtlinie oder eine Rollenzuweisung kann
  auf Sektionen eingeschränkt werden), in **der Administrationsoberfläche** (der Navigationsbereich entscheidet, welcher
  Reiter im oberen Menü aktiv ist, während der Inhalt angezeigt wird) und bei **Designs** (Templates können für eine
  Sektion überschrieben werden, `Match[section]` oder `Match[section_identifier]`).
- **Setup > Sektionen** (`/section/list`) zeigt jede Sektion mit ihren Objekten, den Rollen, die sie verwenden, und ob
  sie entfernt werden kann. Von dort aus legen Sie Sektionen an, zeigen sie an, bearbeiten, entfernen und weisen zu.
- **Neue Inhalte übernehmen die Sektion ihres übergeordneten Elements.** Das Verschieben eines Teilbaums überträgt die
  Sektion des neuen übergeordneten Elements auf die Objekte, die noch die Sektion des alten übergeordneten Elements
  hatten. Um vorhandene Inhalte in eine Sektion zu bringen, verwenden Sie **Inhalt zuweisen** oder den Reiter Details
  eines Objekts und wählen, welche Objekte darunter die Sektion mit erhalten: alle (die Voreinstellung), nur die in der
  Sektion, die das gewählte Element hatte, oder keine (Abschnitt 8.5).
- **Eine Sektion kann nur entfernt werden, wenn nichts sie verwendet**: kein Objekt (Entwürfe und archivierte Objekte
  eingeschlossen), keine Richtlinie mit einer Sektions-Einschränkung, die sie nennt, und keine auf sie eingeschränkte
  Rollenzuweisung. Das Entfernen fragt nach einer Bestätigung und lässt sich nicht rückgängig machen.
- Die Standardinstallation hat fünf Sektionen: Standard (1), Users (2), Media (3), Setup (4) und Design (5). Die Rolle
  Anonymous darf nur Standard lesen; deshalb sind Inhalte, die in eine neue Sektion kommen, für Besucher verborgen,
  bis eine Richtlinie sie erlaubt.

## Inhalt

- [1. Was eine Sektion ist und was nicht](#1-was-eine-sektion-ist-und-was-nicht)
- [2. Die Sektionen einer neuen Installation](#2-die-sektionen-einer-neuen-installation)
- [3. Die Liste der Sektionen](#3-die-liste-der-sektionen)
- [4. Die Seite einer Sektion](#4-die-seite-einer-sektion)
- [5. Eine Sektion anlegen](#5-eine-sektion-anlegen)
- [6. Eine Sektion bearbeiten und was eine Änderung betrifft](#6-eine-sektion-bearbeiten-und-was-eine-änderung-betrifft)
- [7. Eine Sektion entfernen](#7-eine-sektion-entfernen)
- [8. In welcher Sektion Inhalte liegen: anlegen, verschieben und zuweisen](#8-in-welcher-sektion-inhalte-liegen-anlegen-verschieben-und-zuweisen)
  ([8.5 Welche Objekte darunter die Sektion erhalten](#85-welche-objekte-darunter-die-sektion-erhalten))
- [9. Sektionen in Rollen und Richtlinien](#9-sektionen-in-rollen-und-richtlinien)
- [10. Navigationsbereiche](#10-navigationsbereiche)
- [11. Templates und Designs nach Sektion](#11-templates-und-designs-nach-sektion)
- [12. In einer Shell und aus anderen Programmen](#12-in-einer-shell-und-aus-anderen-programmen)
- [13. Abläufe Schritt für Schritt](#13-abläufe-schritt-für-schritt)
- [14. Fehlerbehebung](#14-fehlerbehebung)
- [15. Wer die Seiten zu den Sektionen verwenden darf](#15-wer-die-seiten-zu-den-sektionen-verwenden-darf)
- [Quellen](#quellen)

## 1. Was eine Sektion ist und was nicht

Eine Sektion ist eine Zeile in der Tabelle `ezsection` mit vier Feldern, auf die es ankommt:

| Feld | Beispiel | Verwendet für |
|---|---|---|
| ID | `1` | Alles, was einen Verweis speichert: die `section_id` jedes Objekts, Einschränkungen von Richtlinien, Rollenzuweisungen, `Match[section]` in Override-Einstellungen. Sie ändert sich nie. |
| Name | `Standard` | Was Menschen lesen: die Sektionslisten, die Seiten der Rollen, die Sektionsauswahl eines Objekts. |
| Bezeichner | `standard` | Was Templates und Code nennen: `fetch( 'section', 'object', hash( 'identifier', 'standard' ) )`, `Match[section_identifier]=standard`. Buchstaben, Ziffern und `_`, beginnend mit einem Buchstaben, eindeutig. |
| Navigationsbereich | `ezcontentnavigationpart` | Welcher Reiter im oberen Menü der Administrationsoberfläche aktiv ist, während ein Objekt der Sektion angezeigt oder bearbeitet wird. |

Ein fünftes Feld, `locale`, existiert in der Tabelle und wird beibehalten, wenn ein Formular es sendet, aber keine
Seite der Administrationsoberfläche setzt es, und nichts im Kernel liest es, um eine Entscheidung zu treffen.

Was eine Sektion **nicht** ist:

- Sie ist kein Ort im Inhaltsbaum. Eine Sektion hat keinen Wurzelknoten und keine URL. Die Objekte einer Sektion
  können überall im Baum liegen, und ein Ordner kann Objekte mehrerer Sektionen enthalten. Das Zuweisen von Inhalten zu
  einer Sektion beginnt an einem Knoten, gespeichert wird aber die Sektion jedes einzelnen Objekts.
- Sie ist kein Siteaccess. Welche Site ein Besucher sieht, entscheidet der Siteaccess; was der Besucher darin lesen
  darf, entscheiden Rollen, die nach Sektion eingeschränkt werden können.
- Sie ist kein Zustand. Objektzustände (Setup > Zustände) sind ein zweiter, unabhängiger Weg, Objekte für
  Berechtigungen zu gruppieren; eine Richtlinie kann nach beidem eingeschränkt werden.
- Sie gehört nicht zu einem Ort. Ein Objekt mit zwei Orten hat eine Sektion, an beiden dieselbe.

## 2. Die Sektionen einer neuen Installation

Eine neue Installation (`share/db_data.dba`, `kernel/sql/common/cleandata.sql`) legt fünf Sektionen an:

| ID | Name | Bezeichner | Navigationsbereich | Enthält |
|---|---|---|---|---|
| 1 | Standard | `standard` | Inhaltsstruktur (`ezcontentnavigationpart`) | den Inhaltsbaum vom Wurzelordner abwärts |
| 2 | Users | `users` | Benutzerkonten (`ezusernavigationpart`) | die Benutzerkonten und Benutzergruppen |
| 3 | Media | `media` | Medienbibliothek (`ezmedianavigationpart`) | die Medienbibliothek: Bilder, Dateien, ihre Ordner |
| 4 | Setup | `setup` | Setup (`ezsetupnavigationpart`) | Setup-Objekte |
| 5 | Design | `design` | Design (`ezvisualnavigationpart`) | Design-Objekte |

Die Standardrolle Anonymous hat `content/read` und `content/pdf`, eingeschränkt auf die Sektion Standard. Diese eine
Einschränkung hält Benutzerkonten, Medienordner und alles in anderen Sektionen von nicht angemeldeten Besuchern fern,
und sie ist der Grund, warum die meisten Abläufe in Abschnitt 13 mit einer neuen Sektion beginnen.

Sites, die aus Paketen oder Extensions installiert wurden, haben oft mehr. Auf dem Demonstrationsserver zum Beispiel
hat die Newsletter-Extension „CJW Newsletter“ (34) angelegt, und ein Paket hat „Restricted“ (6) und „Sample:
collaboration“ (35) hinzugefügt. Zwei davon haben keinen Bezeichner, was die Liste der Sektionen als
aufmerksamkeitsbedürftig kennzeichnet (Abschnitt 3.4).

## 3. Die Liste der Sektionen

**Setup > Sektionen**, Adresse `/section/list`. Die Seite beantwortet vier Fragen auf einen Blick: welche Sektionen es
gibt, was in jeder liegt, von welchen das Berechtigungssystem abhängt und welche entfallen könnten.

### 3.1 Die Übersicht

Sechs Kennzahlen oben, gezählt über alle Sektionen, nicht nur über die auf der aktuellen Seite:

| Kennzahl | Zählt |
|---|---|
| Sektionen | alle Sektionen |
| Veröffentlichte Objekte | veröffentlichte Objekte in allen Sektionen zusammen |
| Von Rollen verwendet | Sektionen, die eine Einschränkung einer Richtlinie oder eine Rollenzuweisung nennt |
| Ohne veröffentlichte Objekte | Sektionen ohne veröffentlichtes Objekt (sie können noch Entwürfe oder archivierte Objekte enthalten) |
| Kann entfernt werden | Sektionen, die nichts verwendet (Abschnitt 7) |
| Brauchen Aufmerksamkeit | Sektionen ohne Bezeichner oder mit einem Navigationsbereich, den `menu.ini` nicht aufführt; rot angezeigt, wenn nicht 0 |

Die Zahlen stammen aus drei gruppierten Datenbankabfragen, unabhängig davon, wie viele Sektionen es gibt (unter
MongoDB eine Abfrage pro Sektion und Status), sodass die Seite auch bei Hunderten von Sektionen und Millionen von
Objekten schnell bleibt.

### 3.2 Eine Sektion finden

**Sektion finden** durchsucht den Namen, den Bezeichner, die ID, den Navigationsbereich (seinen Namen und seinen
Bezeichner) und die Namen der Rollen, die die Sektion verwenden. Mehrere Wörter müssen alle passen. **Anzeigen** grenzt
die Liste auf eine Art ein: Alle, Mit veröffentlichten Objekten, Ohne veröffentlichte Objekte, Von Rollen verwendet,
Kann entfernt werden, Brauchen Aufmerksamkeit. Eine Zeile unter den Bedienelementen sagt, wie viele Sektionen der Seite
angezeigt werden; wenn keine passt, sagt die Seite das.

Suche und Filter wirken auf die Sektionen der aktuellen Seite. Mit dem Standardwert von 25 pro Seite sind das auf fast
jeder Site alle Sektionen. Beide brauchen JavaScript; ohne JavaScript werden die Bedienelemente nicht angezeigt, und
die Liste ist vollständig.

### 3.3 Die Karte einer Sektion

Jede Sektion ist eine Karte:

- **Das Kontrollkästchen** wählt die Sektion für **Ausgewählte entfernen** aus (nur für Benutzer, die Sektionen
  bearbeiten dürfen).
- **Der Name** öffnet die Seite der Sektion; daneben stehen der Bezeichner, in Festbreitenschrift, und die ID.
- **Badges**: die Anzahl der veröffentlichten Objekte (oder „Keine veröffentlichten Objekte“), „Von Rollen verwendet“,
  „Kann entfernt werden“ und die Warnungen „Kein Bezeichner“ und „Unbekannter Navigationsbereich“.
- **Anzeigen**, **Bearbeiten** und **Inhalt zuweisen**. Inhalt zuweisen ist ausgegraut, mit dem Grund im Tooltip, wenn
  Ihre Rolle Ihnen nicht erlaubt, diese Sektion zuzuweisen (Abschnitt 9.3).
- **Angaben**: der Navigationsbereich, die Objekte (veröffentlicht, Entwürfe, archiviert), die Rollen mit einer auf die
  Sektion eingeschränkten Richtlinie (jeweils ein Link zur Rolle) und die Anzahl der auf sie eingeschränkten
  Rollenzuweisungen.

Karten, die Aufmerksamkeit brauchen, haben links einen bernsteinfarbenen Rand.

### 3.4 Was „Brauchen Aufmerksamkeit“ bedeutet

- **Kein Bezeichner**: Ältere Installationen und manche Pakete haben Sektionen angelegt, bevor es Bezeichner gab. Eine
  solche Sektion funktioniert für Berechtigungen und Inhalte, aber Templates,
  `fetch( 'section', 'object', hash( 'identifier', ... ) )` und `Match[section_identifier]` können sie nicht nennen,
  und das Bearbeitungsformular speichert sie erst, wenn sie einen hat (Abschnitt 5).
- **Unbekannter Navigationsbereich**: Die Sektion nennt einen Bereich, den keine `menu.ini` aufführt, meist weil die
  Extension, die ihn hinzugefügt hat, nicht mehr aktiv ist. Ihre Inhalte zeigen dann keinen aktiven Reiter im oberen
  Menü. Wählen Sie einen anderen Bereich (Abschnitt 10).

### 3.5 Seitengröße, Seiten und die Schaltflächen

**Pro Seite** unter der Liste wählt, wie viele Sektionen eine Seite zeigt. Die Größen stammen aus `admininterface.ini
[PaginationSettings]` (standardmäßig `ItemsPerPage[section/list]=25`), und die Wahl wird als Präferenz
`admin_section_list_limit` gespeichert. Die Links zu den Seiten (`/section/list/(offset)/25`) folgen.

Darunter: **Ausgewählte entfernen** (Abschnitt 7) und **Neue Sektion** (Abschnitt 5); Neue Sektion steht auch oben.
Eine Meldung oben in der Liste sagt, was die letzte Aktion bewirkt hat: „Die Sektion Members area wurde angelegt.“,
„Die Sektion ... wurde gespeichert.“, „Entfernt: ...“, oder dass Ausgewählte entfernen gedrückt wurde, ohne dass etwas
markiert war.

## 4. Die Seite einer Sektion

Klicken Sie auf den Namen einer Sektion oder auf **Anzeigen**: `/section/view/<ID>`.

1. **Der Titel**: Name, Bezeichner und ID.
2. **Bearbeiten**, **Inhalt zuweisen** und **Alle Sektionen**. Bearbeiten und Inhalt zuweisen erscheinen nur für
   Benutzer, die sie verwenden dürfen.
3. **Die Kennzahlen**: veröffentlichte Objekte, Entwürfe neuer Objekte (Objekte, die nie veröffentlicht wurden),
   archivierte Objekte, Rollen mit Richtlinien für die Sektion, auf sie eingeschränkte Rollenzuweisungen.
4. **Details**: Name, Bezeichner (oder eine Warnung, dass sie keinen hat), ID, Navigationsbereich und **Entfernen**:
   entweder „Kann entfernt werden“ oder „In Gebrauch“ mit der Regel.
5. **Rollen mit auf diese Sektion eingeschränkten Richtlinien**: jede Rolle mit ihrer ID, als Link, und die
   Richtlinien dieser Rolle, deren Sektions-Einschränkung diese Sektion nennt, als `module/function` (zum Beispiel
   `content/read`). Zwei Rollen mit demselben Namen werden an ihren IDs unterschieden.
6. **Benutzer und Gruppen mit Rolleneinschränkungen bzgl. dieser Sektion**: jede Rollenzuweisung, die mit „Mit
   Einschränkungen zuordnen: Sektion“ für diese Sektion vorgenommen wurde (Abschnitt 9.2), der Benutzer oder die
   Gruppe und die Rolle.
7. **Objekte innerhalb dieser Sektion**: die veröffentlichten Objekte, die neuesten zuerst, mit ihrer Klasse und dem
   Zeitpunkt ihrer letzten Änderung, jeweils 10, 25 oder 50 (die allgemeine Präferenz für die Listenlänge der
   Administrationsoberfläche). Jeder Name öffnet das Objekt.

## 5. Eine Sektion anlegen

1. **Setup > Sektionen**, **Neue Sektion**. Das Formular öffnet sich unter `/section/edit/0`.
2. **Name**: was Menschen lesen werden, zum Beispiel `Members area`.
3. **Bezeichner**: wird beim Tippen des Namens aus dem Namen ausgefüllt (`members_area`), solange Sie nicht selbst
   einen Bezeichner eingegeben haben. Er muss mit einem Buchstaben beginnen und darf nur Buchstaben, Ziffern und `_`
   enthalten, und keine andere Sektion darf ihn haben. Die Seite prüft das vor dem Absenden; der Server prüft es
   erneut.
4. **Navigationsbereich**: der Reiter im oberen Menü, der für diese Inhalte aktiv sein wird. Für gewöhnliche Seiten
   lassen Sie **Inhaltsstruktur** stehen.
5. **Sektion anlegen**. Die Liste öffnet sich mit „Die Sektion Members area wurde angelegt.“

Die neue Sektion ist leer. Noch kann niemand sie sehen außer Benutzern, deren Richtlinien nicht nach Sektion
eingeschränkt sind, etwa Administratoren. Abschnitt 13 zeigt die üblichen nächsten Schritte.

Der Server weist drei Arten von Bezeichnern zurück, mit der Meldung über dem Formular und neben dem Feld:

| Meldung | Ursache |
|---|---|
| Bezeichner darf nicht leer sein | das Feld war leer oder enthielt nur Leerzeichen |
| Bezeichner sollte aus Buchstaben, Zahlen oder Unterstrich mit Buchstabenvorzeichen bestehen. | er beginnt mit etwas anderem als einem Buchstaben oder enthält ein Leerzeichen, einen Bindestrich, einen Buchstaben mit Akzent oder ein anderes Zeichen |
| Der Bezeichner wurde in einer anderen Sektion benutzt. | eine andere Sektion hat ihn (exakt verglichen) |

Der Name wird nicht geprüft; ein leerer Name wird so gespeichert, wie er ist, weshalb das Formular nach einem fragt.

Jedes Speichern wird im Audit-Protokoll als `content.section.change` festgehalten, mit den Werten vorher und nachher
([Audit-Protokoll](../funktionen/6.0/pruefprotokoll.md)).

## 6. Eine Sektion bearbeiten und was eine Änderung betrifft

**Bearbeiten** auf der Karte oder auf der Seite der Sektion öffnet `/section/edit/<ID>`. Es ist dasselbe Formular, und
unter den Feldern steht, was eine Änderung für diese Sektion betrifft: wie viele Objekte sie enthält und wie viele
Richtlinien, Rollen und Rollenzuweisungen auf sie eingeschränkt sind.

| Sie ändern | Wirkung |
|---|---|
| Name | Nur das, was Menschen lesen. Richtlinien und Rollenzuweisungen speichern die ID, daher ändert sich keine Berechtigung. |
| Bezeichner | Alles, was die Sektion über ihren Bezeichner nennt, greift nicht mehr: `Match[section_identifier]` in Override-Einstellungen, `fetch( 'section', 'object', hash( 'identifier', ... ) )`, Code, der `eZSection::fetchByIdentifier()` aufruft. Ändern Sie diese zusammen mit ihm. |
| Navigationsbereich | Der aktive Reiter im oberen Menü für jedes Objekt der Sektion, ab dem nächsten Seitenaufruf. |

Das Speichern leert den View-Cache jedes Objekts in der Sektion (`eZContentCacheManager::clearContentCacheIfNeededBySectionID()`)
und sendet das Ereignis `content/section/cache`, damit Listener wie ein HTTP-Cache ebenfalls leeren können. Bei einer
Sektion mit sehr vielen Objekten dauert das einen Moment.

**Abbrechen** kehrt ohne zu speichern zur Liste zurück.

## 7. Eine Sektion entfernen

### 7.1 Die Regel

Eine Sektion kann nur entfernt werden, wenn nichts sie verwendet (`eZSection::canBeRemoved()`):

1. Kein Objekt liegt in ihr, in keinem Status: veröffentlicht, nie veröffentlicht (ein Entwurf eines neuen Objekts)
   oder archiviert;
2. keine Richtlinie hat eine Sektions-Einschränkung, die sie nennt;
3. keine Rollenzuweisung ist auf sie eingeschränkt.

Die Liste zeigt mit dem Badge **Kann entfernt werden**, welche Sektionen diese Regel erfüllen, und die gleichnamige
Kennzahl zählt sie.

### 7.2 Schritt für Schritt

1. Markieren Sie die Sektionen in der Liste und drücken Sie **Ausgewählte entfernen**.
2. Die Bestätigungsseite listet in Rot die Sektionen auf, die **gelöscht werden**, mit der Warnung, und getrennt davon
   die Sektionen, die **nicht entfernt werden können**, jeweils mit ihren Gründen: „Enthält 173 Objekte
   (173 veröffentlicht, 0 Entwürfe, 0 archiviert)“, „18 Richtlinien sind auf sie eingeschränkt“ mit Links zu den
   Rollen, „2 Rollenzuweisungen sind auf sie eingeschränkt“.
3. **Diese Sektionen entfernen** entfernt die, die entfallen dürfen. **Abbrechen** behält alle Sektionen. Darf keine der
   markierten Sektionen entfallen, gibt es nur **Zurück zu den Sektionen**.
4. Die Liste öffnet sich mit „Entfernt: ...“ und nennt sie.

Jedes Entfernen leert den View-Cache der Objekte der Sektion (nach der Regel gibt es keine), sendet
`content/section/cache` und wird im Audit-Protokoll als `content.section.remove` festgehalten. Die Bestätigung wird
durch das Entfernen verbraucht: Wird dasselbe Formular erneut gesendet, wird nichts entfernt.

Entfernen braucht `section/edit` ohne Einschränkung (Abschnitt 15).

### 7.3 Was das Entfernen nicht prüft

- **`NewSection`-Einschränkungen** von Richtlinien `section/assign`. Eine Richtlinie, die die Sektion zum Zuweisen
  angeboten hat, behält deren ID, die dann ins Leere zeigt. Bearbeiten Sie solche Richtlinien vorher oder nachher.
- **Templates und Einstellungen.** `Match[section]=<ID>`, `Match[section_identifier]=...` und Template-Code, der die
  Sektion nennt, greifen stillschweigend nicht mehr.
- **Sektions-IDs, die in Einstellungen verwendet werden**, etwa `site.ini [UserSettings] DefaultSectionID`
  (Abschnitt 8.1). Sie werden nicht berücksichtigt.

Es gibt kein Rückgängigmachen. Eine neue Sektion mit demselben Namen und Bezeichner erhält eine neue ID, sodass
Richtlinien und Einstellungen, die die alte ID nannten, nicht für sie gelten.

## 8. In welcher Sektion Inhalte liegen: anlegen, verschieben und zuweisen

### 8.1 Neue Inhalte

- Ein unterhalb eines Knotens angelegtes Objekt übernimmt **die Sektion des Objekts seines übergeordneten Knotens**
  (`eZContentObject::createWithNodeAssignment()`), und bei seiner ersten Veröffentlichung trägt der Schritt
  `update-section-id` der Veröffentlichungsoperation für jedes Objekt, das noch keine hat, die Sektion des
  übergeordneten Elements ein.
- Eine Sektion, die ein Objekt bereits hat, bleibt beim Veröffentlichen erhalten. Eine Kopie beginnt ohne Sektion und
  übernimmt daher die Sektion des Ortes, an den sie kopiert wird.
- Ein Benutzer, der sich auf der Site registriert, wird mit `site.ini [UserSettings] DefaultSectionID` angelegt; der
  Standardwert `0` bedeutet „die Sektion der Benutzergruppe in `DefaultUserPlacement`“, normalerweise Users.

### 8.2 Verschieben und Orte

- **Das Verschieben eines Teilbaums** (des Hauptorts seines obersten Objekts) gibt jedem Objekt im Teilbaum, **das noch
  die Sektion des alten übergeordneten Elements hatte**, die Sektion des neuen übergeordneten Elements. Objekte mit
  einer eigenen Sektion behalten sie. Das Verschieben eines zusätzlichen Ortes ändert nichts.
- **Das Ändern des Hauptorts** eines Objekts tut dasselbe für seinen Teilbaum.
- **Das Hinzufügen eines zusätzlichen Ortes** ändert nichts: Ein Objekt hat eine Sektion, wo immer es erscheint.

### 8.3 Einem Teilbaum Inhalte zuweisen: section/assign

Das ist der übliche Weg, vorhandene Inhalte in eine Sektion zu bringen.

1. Auf der Karte oder der Seite der Sektion **Inhalt zuweisen**. Der Inhaltsbrowser öffnet sich mit einem Hinweis
   oben: „Wählen Sie einen Startort für die Sektion <Members area>“.
2. Wählen Sie über dem Browser, **welche Objekte die Sektion erhalten** (Abschnitt 8.5): das gewählte Element und jedes
   Objekt darunter (vorausgewählt), das gewählte Element und die Objekte darunter, die seine Sektion haben, oder nur das
   gewählte Element.
3. Navigieren Sie mit den Reitern, dem Baum und der Liste, wählen Sie das Element mit seinem Optionsfeld und drücken Sie
   **Auswählen**.
4. Für ein einzelnes Element mit einem großen Teilbaum (ab `content.ini [ContentJobSettings] SynchronousLimit`
   Elementen, standardmäßig 50) bietet eine Bestätigung an, es **sofort** oder **im Hintergrund** als Inhaltsjob
   auszuführen, mit einer Fortschrittsseite ([Inhaltsjobs](../funktionen/6.0/inhaltsauftraege.md)). Kleinere Teilbäume und
   „Nur das gewählte Element“ werden sofort zugewiesen.
5. Sofort ausgeführt, öffnet sich die Liste der Sektionen wieder, und jeder Inhalts-View-Cache wird geleert
   (`eZContentCacheManager::clearAllContentCache()`). Als Job ausgeführt, öffnet sich seine Fortschrittsseite; der Job
   leert die View-Caches der Objekte, die er ändert, Stapel für Stapel.

Welche Objekte sich bei der ersten Wahl ändern (bei der zweiten fehlen die Zweige mit einer anderen Sektion):

- Sofort ausgeführt (`eZContentObjectTreeNode::assignSectionToSubTree()`): das gewählte Element und jedes Objekt
  darunter, **dessen Hauptort in diesem Teilbaum liegt**. Ein Objekt, das dort nur einen zusätzlichen Ort hat, behält
  seine Sektion.
- Als Inhaltsjob ausgeführt: jedes Objekt mit einem Ort im Teilbaum, und jedes wird gegen Ihre Berechtigungen geprüft;
  Objekte, die Sie nicht zuweisen dürfen, werden mit einer Warnung im Log des Jobs übersprungen.

Die Berechtigungen werden für die gewählten Elemente geprüft. Dürfen einige nicht zugewiesen werden, werden die anderen
zugewiesen, und eine Seite listet die auf, bei denen das nicht geschah. Erlaubt Ihre Rolle die Sektion, aber keine
Objektklasse, oder erlaubt sie die Sektion überhaupt nicht, sagt die Seite stattdessen das (Abschnitt 9.3).

### 8.4 Ein Objekt: der Reiter Details und das Bearbeitungsformular

Auf der Seite eines Objekts in der Administrationsoberfläche hat der Reiter **Details** eine Auswahl **Sektion** mit den
Sektionen, die Ihre Rolle Ihnen für dieses Objekt zuzuweisen erlaubt, bei einem Knoten mit Unterknoten eine zweite
Auswahl mit den drei Möglichkeiten aus Abschnitt 8.5 (**Ganzer Teilbaum** vorausgewählt), und **Setzen**. Dieselben
Auswahlen gibt es im Bearbeitungsformular eines Entwurfs. Beide rufen `eZSection::applyTo()` auf, das das Objekt ändert,
mit **Ganzer Teilbaum** auch **jedes Objekt unterhalb jedes seiner Orte**, dessen Hauptort dort liegt, den Suchindex
aktualisiert und die View-Caches leert, die Seiten des statischen Caches eingeschlossen, wenn das statische Caching
eingeschaltet ist. Mit **Nur dieses Objekt** ändert sich nur das Objekt, gleich wie viele Orte es hat.

### 8.5 Welche Objekte darunter die Sektion erhalten

Jede Stelle, die eine Sektion zuweist, bietet dieselben drei Möglichkeiten (`eZSection::SCOPE_*`):

| Wahl | Wert | Was sich ändert |
|---|---|---|
| Ganzer Teilbaum / Das gewählte Element und jedes Objekt darunter | `subtree` | Das Objekt und jedes Objekt darunter, wie bevor es eine Wahl gab. Die Voreinstellung überall, wo nichts gewählt wird. |
| Teilbaum ohne Zweige mit anderer Sektion / … die Objekte darunter, die seine Sektion haben | `inherited` | Das Objekt und die Objekte darunter, die in der Sektion sind, die das Objekt hatte. Ein Objekt darunter mit einer anderen Sektion behält sie, und ebenso alles darunter, auch Objekte dort mit der alten Sektion. |
| Nur dieses Objekt / Nur das gewählte Element | `object` | Nur das Objekt, an allen seinen Orten. |

Ein Beispiel: Ein Ordner in Standard enthält `a` (Standard), `b` (Members area) mit `b1` (Standard) darunter und `c`
(Standard). Intranet mit `inherited` zugewiesen ändert den Ordner, `a` und `c`; `b` bleibt in Members area und `b1` in
Standard. Mit `subtree` sind alle fünf in Intranet, mit `object` nur der Ordner.

Was es nicht ändert: Neue Inhalte darunter übernehmen weiterhin die Sektion ihres übergeordneten Elements (Abschnitt
8.1), und das Verschieben folgt weiter Abschnitt 8.2. Eine Sektion bleibt eine Eigenschaft des Objekts; `object` gibt
nicht einem einzelnen Ort eine eigene Sektion.

Wer auch zuweist, bei jeder Wahl gilt:

- **Nur Objekte, denen Sie die Sektion zuweisen dürfen, ändern sich.** Das gilt für das gewählte Element und für jedes
  Objekt darunter: Ein Objekt, das Ihre Richtlinie section/assign ausschließt (ihre Einschränkung nach Klasse, Sektion,
  Besitzer oder Teilbaum), behält seine Sektion, ob sofort, als Inhaltsauftrag oder über den Reiter Details. Ein
  Administrator ohne Einschränkungen ändert alle. Die eigenen Änderungen des Kernels (ein neuer Hauptort übernimmt die
  Sektion seines übergeordneten Elements) werden nicht geprüft.
- **Ein sehr großer Teilbaum läuft im Hintergrund.** Über den Reiter Details und das Bearbeitungsformular wird ein
  Teilbaum mit mehr Knoten als `content.ini [ContentJobSettings] NowLimit` (Standard 1000) vom Inhaltsauftrag `section`
  in Stapeln zugewiesen: Über den Reiter Details öffnet sich seine Fortschrittsseite, im Bearbeitungsformular bleibt
  der Entwurf offen, während er läuft. **Nur dieses Objekt** geschieht immer sofort.
- Jede Zuweisung schreibt das Audit-Ereignis `content.node.section` mit der Wahl (`after.scope`) und der Zahl der
  Objekte, die sich ändern; der Suchindex und die Ansichts-Caches der geänderten Objekte werden aktualisiert.

## 9. Sektionen in Rollen und Richtlinien

Sektionen sind in erster Linie ein Werkzeug für Berechtigungen. Es gibt drei Arten, wie eine Rolle eine Sektion
verwenden kann.

### 9.1 Eine Richtlinie mit einer Sektions-Einschränkung

Eine Richtlinie des Moduls `content` kann auf Sektionen eingeschränkt werden. Die Richtlinie gilt dann nur für Objekte
in diesen Sektionen. Diese Funktionen akzeptieren die Sektions-Einschränkung: `read`, `diff`, `view_embed`, `create`,
`edit`, `publish`, `manage_locations`, `hide`, `translate`, `remove`, `versionread`, `versionremove` und `pdf`.

Beispiel, die Standardrolle Anonymous:

| Modul | Funktion | Einschränkung |
|---|---|---|
| content | read | Section( Standard ) |
| content | pdf | Section( Standard ) |

Damit Besucher auch eine neue Sektion „Public documents“ lesen können: **Rollen und Richtlinien** > Anonymous >
**Bearbeiten** > die Richtlinie `content/read` > **Bearbeiten** > „Public documents“ in der Liste Sektion neben
Standard markieren > **OK** > **Speichern**.

Eine Sektions-Einschränkung wird mit den anderen Einschränkungen derselben Richtlinie per UND verknüpft (Klasse,
Eigentümer, Knoten, Teilbaum, Zustand ...), und Richtlinien derselben Funktion werden per ODER verknüpft. „Redakteure
dürfen Artikel in Members area bearbeiten“ ist eine Richtlinie: `content/edit` mit Class( Article ) und
Section( Members area ).

Die Seite der Sektion (Abschnitt 4) listet jede Rolle mit einer solchen Richtlinie auf, und die Liste zählt die Sektion
als „Von Rollen verwendet“.

### 9.2 Eine mit der Einschränkung auf eine Sektion zugewiesene Rolle

Eine ganze Rolle kann einem Benutzer oder einer Gruppe **nur für eine Sektion** gegeben werden. Wählen Sie auf der
Seite der Rolle, unter der Liste der Zuweisungen, **Sektion** in der Auswahl der Einschränkung, dann **Mit
Einschränkungen zuordnen**, wählen Sie die Sektion und dann die Benutzer oder Gruppen. Jede Richtlinie der Rolle gilt
dann nur für Objekte in dieser Sektion (intern erhalten die Richtlinien eine Einschränkung `User_Section`).

Verwenden Sie das, wenn dieselbe Rolle mehrfach für verschiedene Teile der Site gebraucht wird: eine Rolle „Editor“,
der Gruppe Marketing eingeschränkt auf die Sektion Marketing und der Gruppe Sales eingeschränkt auf die Sektion Sales
zugewiesen.

Die Seite der Sektion listet diese Zuweisungen unter „Benutzer und Gruppen mit Rolleneinschränkungen bzgl. dieser
Sektion“ auf. Sie verhindern, dass die Sektion entfernt wird.

### 9.3 Wer Sektionen zuweisen darf: section/assign

`section/assign` entscheidet, wer Inhalte in Sektionen bringen darf, mit vier Einschränkungen:

| Einschränkung | Bedeutet |
|---|---|
| NewSection | die Sektionen, die zugewiesen werden dürfen (das Ziel) |
| Section | die Sektionen, in denen die Objekte jetzt liegen dürfen (die Quelle) |
| Class | die Klassen der Objekte, die zugewiesen werden dürfen |
| Owner: Self | nur Objekte, deren Eigentümer der Benutzer ist |

Beispiel: „Redakteure dürfen Artikel und Ordner aus Standard nach Members area verschieben, sonst nichts“:
`section/assign` mit Class( Article, Folder ), Section( Standard ), NewSection( Members area ).

Ohne eine NewSection-Einschränkung darf jede Sektion zugewiesen werden. **Inhalt zuweisen** ist in der Liste für die
Sektionen ausgegraut, die ein Benutzer nicht zuweisen darf.

### 9.4 Die eigenen Funktionen des Moduls section

| Funktion | Öffnet |
|---|---|
| `section/view` | die Liste und die Seite jeder Sektion |
| `section/edit` | das Bearbeitungsformular, Neue Sektion, Ausgewählte entfernen |
| `section/assign` | Inhalt zuweisen sowie die Liste und die Seiten der Sektionen |

## 10. Navigationsbereiche

Die Administrationsoberfläche hat ein oberes Menü: Inhaltsstruktur, Medienbibliothek, Benutzer, Setup und mehr. Ein
Navigationsbereich ist das, was eine Seite mit einem dieser Reiter verbindet. Während ein Objekt angezeigt oder
bearbeitet wird, entscheidet der Navigationsbereich **seiner Sektion**, welcher Reiter aktiv ist und welches linke Menü
angezeigt wird. Deshalb verwendet die Sektion Users den Bereich Benutzerkonten und die Sektion Media den Bereich
Medienbibliothek.

Die Bereiche sind in `settings/menu.ini [NavigationPart]` aufgeführt:

| Bezeichner | Name |
|---|---|
| `ezcontentnavigationpart` | Inhaltsstruktur |
| `ezmedianavigationpart` | Medienbibliothek |
| `ezusernavigationpart` | Benutzerkonten |
| `ezshopnavigationpart` | Web-Shop |
| `ezvisualnavigationpart` | Design |
| `ezsetupnavigationpart` | Setup |
| `ezmynavigationpart` | Mein Konto |
| `expauditnavigationpart` | Audit |

Extensions fügen in ihrer `settings/menu.ini.append.php` eigene hinzu (`Part[eznewsletternavigationpart]=...` und so
weiter), und das Formular der Sektion listet sie auf. Eine Sektion, deren Bereich nicht mehr aufgeführt ist (die
Extension wurde deaktiviert), zeigt „Unbekannter Navigationsbereich“, und ihre Inhalte zeigen keinen aktiven Reiter.

Der Navigationsbereich ändert für Besucher nichts: Die öffentlichen Siteaccesses haben kein solches Menü. Die
Pagelayouts der Administration setzen außerdem den Bezeichner und `section_id_<ID>` in die Klasse der Seite, für die
Gestaltung.

## 11. Templates und Designs nach Sektion

### 11.1 Ein Template für eine Sektion überschreiben

`override.ini` (eines Siteaccess oder in einer Extension) kann ein Template nach Sektion wählen, nach ID oder nach
Bezeichner:

```ini
[members_article_full]
Source=node/view/full.tpl
MatchFile=full/members_article.tpl
Subdir=templates
Match[class_identifier]=article
Match[section_identifier]=members_area
```

`Match[section]=<ID>` tut dasselbe nach ID. Die Knotenansichten, content/edit, die Suche, die Versionsgeschichte und
die Template-Funktionen `node_view_gui`, `content_view_gui` und die anderen setzen die Schlüssel `section` (und
`section_identifier`, wo die Sektion einen hat). Leeren Sie den Template-Override-Cache, nachdem Sie `override.ini`
geändert haben:

```bash
php bin/php/ezcache.php --clear-id=template-override,template,content --allow-root-user
```

Eine Sektion ohne Bezeichner kann nicht mit `section_identifier` angesprochen werden; geben Sie ihr zuerst einen.

### 11.2 Templates, die nach Sektionen fragen

```
{* one section, by ID or by identifier: exactly one of the two *}
{def $members = fetch( 'section', 'object', hash( 'identifier', 'members_area' ) )}

{* every section *}
{def $sections = fetch( 'section', 'list' )}

{* published objects of a section, 10 from the start; archived ones with status 'archived' *}
{def $objects = fetch( 'section', 'object_list', hash( 'section_id', $members.id, 'limit', 10, 'offset', 0 ) )
     $count = fetch( 'section', 'object_list_count', hash( 'section_id', $members.id ) )}

{* the roles whose policies are limited to it, and the role assignments limited to it *}
{def $roles = fetch( 'section', 'roles', hash( 'section_id', $members.id ) )
     $assigned = fetch( 'section', 'user_roles', hash( 'section_id', $members.id ) )}

{* the children of a node that are in one section *}
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                 'attribute_filter', array( array( 'section', '=', $members.id ) ) ) )}
```

`object_list` sortiert nach Objekt-ID, die neuesten zuerst, sofern `sort_order` nichts anderes sagt. In einem
Knoten-Template ist die Sektion `$node.object.section_id`. Die Fetches für Inhaltslisten und -bäume können auch nach
Sektion sortieren (`sort_by`, `array( 'section', true() )`).

## 12. In einer Shell und aus anderen Programmen

Es gibt keinen eigenen Befehl zum Anlegen oder Entfernen von Sektionen; verwenden Sie die Seiten oder die
Remote-Dienste unten.

**Einen Teilbaum in einer Shell zuweisen**, als Inhaltsjob:

```bash
php bin/php/expcontentjob.php section <node ID> <section ID>               # now, with progress
php bin/php/expcontentjob.php section <node ID> <section ID> --background  # queued for the worker
php bin/php/expcontentjob.php section <node ID> <section ID> --scope=inherited  # subtree (default), inherited or object
./console exp:expcontentjob section 1234 7 --allow-root-user               # the same through the console
php bin/php/expcontentjob.php list                                         # what is running, what finished
```

Der Job läuft als der mit `--login` angegebene Benutzer, sonst als `site.ini [UserSettings] UserCreatorID`, und die
Berechtigungen dieses Benutzers gelten für jedes Objekt. Der Cronjob-Teil `contentjobs` startet wartende Jobs und setzt
unterbrochene fort ([Inhaltsjobs](../funktionen/6.0/inhaltsauftraege.md)).

**Remote-Dienste** der Extension expservices, unter `/ezjscore/call/expsection::<method>`: `list`, `count`, `get`,
`getByIdentifier`, `objects`, `objectCount`, `usage`, `canRemove`, `assignable`, `navigationParts`, `ofObject`,
`ofNode` und die schreibenden `create`, `update`, `remove`, `assign` und `assignSubtree` (beide nehmen das POST-Feld
`scope`, Abschnitt 8.5), jeweils mit der Richtlinie,
die sie brauchen ([die Liste der Dienste](../bc/6.0/backend-dienste-ueber-ezjscore.md#sektionen-expsection-17-dienste-5-schreibvorgänge)).

**PHP**, in einer Extension oder einem mit `php bin/php/ezexec.php <script> --allow-root-user` gestarteten Skript:

```php
$section = eZSection::fetchByIdentifier( 'members_area' );   // or eZSection::fetch( 7 )
$object  = eZContentObject::fetch( 1234 );
$section->applyTo( $object );                                // checks section/assign for the current user
$section->applyTo( $object, eZSection::SCOPE_INHERITED );    // or SCOPE_OBJECT; SCOPE_SUBTREE is the default
eZContentObjectTreeNode::assignSectionToSubTree( 1234, 7, false, true, eZSection::SCOPE_INHERITED ); // what section/assign does
$free    = $section->canBeRemoved();                         // the rule of section 7.1
```

## 13. Abläufe Schritt für Schritt

### 13.1 Ein Mitgliederbereich

Ziel: Seiten, die nur angemeldete Mitglieder lesen dürfen.

1. **Neue Sektion**: Name `Members area`, Bezeichner `members_area`, Navigationsbereich Inhaltsstruktur. **Sektion
   anlegen**.
2. Legen Sie einen Ordner `Members` an der passenden Stelle im Baum an, mit den Seiten darunter.
3. Auf der Karte von Members area **Inhalt zuweisen**, den Ordner `Members` wählen, **Auswählen**. Die Liste zeigt nun
   „N veröffentlicht“ auf der Karte.
4. Prüfen Sie als Besucher (abgemeldet): Der Ordner und seine Seiten sind aus den Menüs verschwunden und liefern
   „Zugriff verweigert“, weil Anonymous nur Standard lesen darf.
5. **Rollen und Richtlinien** > die Rolle, die Ihre Mitglieder haben (oder eine neue Rolle „Member“, der Benutzergruppe
   Members zugewiesen) > **Bearbeiten** > **Neue Richtlinie** > `content` > `read` > Section( Members area ) > **OK** >
   **Speichern**.
6. Melden Sie sich als Mitglied an: Die Seiten sind da. Die Seite der Sektion listet nun die Rolle auf.

### 13.2 Ein Intranet mit Abteilungen

Ziel: Jede Abteilung bearbeitet ihre eigenen Seiten, mit einer gemeinsamen Rolle Editor.

1. Eine Sektion pro Abteilung: `Sales` (`sales`), `Marketing` (`marketing`). Weisen Sie jeder den Ordner ihrer
   Abteilung zu.
2. Geben Sie allen Lesezugriff: Fügen Sie der Rolle, die alle Mitarbeitenden haben, `content/read`
   Section( Sales, Marketing ) hinzu.
3. Wählen Sie auf der Seite der Rolle Editor **Sektion** in der Auswahl der Einschränkung, **Mit Einschränkungen
   zuordnen**, wählen Sie Sales und dann die Gruppe Sales. Wiederholen Sie das für Marketing.
4. Die Seite jeder Sektion zeigt nun ihre Gruppe unter „Benutzer und Gruppen mit Rolleneinschränkungen bzgl. dieser
   Sektion“; ein Redakteur aus dem Vertrieb kann Seiten von Sales bearbeiten, aber nicht die von Marketing.

### 13.3 Inhalte vorbereiten, bevor sie öffentlich werden

Ziel: Seiten im echten Baum vorbereiten, ohne dass Besucher sie sehen, und sie dann in einem Schritt freigeben.

1. **Neue Sektion** `Staging` (`staging`). Fügen Sie sie nicht der Rolle Anonymous hinzu.
2. Geben Sie Redakteuren `content/read` und `content/edit` für Section( Staging ).
3. Legen Sie die neuen Seiten an ihrem endgültigen Platz an und weisen Sie dann ihr oberstes Element mit **Inhalt
   zuweisen** der Sektion Staging zu. Besucher sehen sie nicht; Redakteure schon.
4. Zum Freigeben: Auf der Karte von **Standard** **Inhalt zuweisen**, dasselbe oberste Element wählen. Die Seiten sind
   sofort lesbar, weil jeder Inhalts-View-Cache geleert wird.

Um Staging-Seiten in einem eigenen Aussehen zu sehen, während sie warten, geben Sie der Sektion ein eigenes Template
mit `Match[section_identifier]=staging` (Abschnitt 11.1).

## 14. Fehlerbehebung

| Symptom | Ursache | Was zu tun ist |
|---|---|---|
| „Die Sektionen können nicht entfernt werden“ | Objekte, Richtlinien oder Rollenzuweisungen verwenden sie noch; die Seite nennt, welche | Weisen Sie ihre Inhalte einer anderen Sektion zu; bearbeiten Sie die aufgeführten Rollen; entfernen Sie die Zuweisungen. Entwürfe zählen mit: siehe nächste Zeile. |
| Eine Sektion mit „Keine veröffentlichten Objekte“ kann trotzdem nicht entfernt werden | Sie enthält Entwürfe von Objekten, die nie veröffentlicht wurden, oder archivierte Objekte; die Seite der Sektion zählt sie | Lassen Sie deren Eigentümer die Entwürfe veröffentlichen oder entfernen. Entwürfe, die älter sind als `content.ini [VersionManagement] DraftsDuration` (90 Tage), entfernt das Cronjob-Skript `old_drafts_cleanup.php`, wenn es eingeplant ist. |
| Inhalte sind nach Inhalt zuweisen für Besucher verschwunden | Die neue Sektion ist nicht in `content/read` der Rolle Anonymous | Fügen Sie die Sektion dieser Richtlinie hinzu (Abschnitt 9.1), oder weisen Sie die Inhalte zurück zu. |
| Besucher sehen weiterhin Inhalte einer Sektion, die sie nicht lesen dürfen | Zwischengespeicherte Seiten: statischer Cache, ein HTTP-Cache, ein Proxy | Inhalt zuweisen leert den Inhalts-View-Cache; leeren Sie auch den statischen Cache und jeden HTTP-Cache. |
| Einige Objekte unter dem gewählten Element haben ihre alte Sektion behalten | Ihr Hauptort liegt woanders; sie haben im Teilbaum nur einen zusätzlichen Ort | Weisen Sie die Inhalte an ihrem Hauptort zu, oder führen Sie die Zuweisung als Inhaltsjob aus (Abschnitt 8.3). |
| Inhalt zuweisen ist ausgegraut | Ihre Rolle hat keine Richtlinie `section/assign`, deren NewSection diese Sektion enthält | Ein Administrator fügt die Sektion zu NewSection hinzu, oder eine Richtlinie ohne diese Einschränkung (Abschnitt 9.3). |
| „Es sind keine Objekte im System vorhanden, denen Sie die Sektion ... zuweisen können“ | Ihre Richtlinie `section/assign` erlaubt die Sektion, aber keine Klasse | Erweitern Sie die Klassen-Einschränkung dieser Richtlinie. |
| „Der Bezeichner wurde in einer anderen Sektion benutzt.“ | Bezeichner sind eindeutig | Wählen Sie einen anderen, oder ändern Sie zuerst den Bezeichner der anderen Sektion. |
| Das Bearbeitungsformular speichert eine alte Sektion nicht | Sie hat keinen Bezeichner; das Formular verlangt einen | Geben Sie ihr einen; prüfen Sie, dass kein Template erwartet hat, dass sie keinen hat. |
| „Unbekannter Navigationsbereich“ | Die Extension, die den Bereich definiert hat, ist inaktiv | Wählen Sie einen aufgeführten Bereich, oder aktivieren Sie die Extension. |
| Ein Template-Override nach Sektion greift nicht | Der Override-Cache ist veraltet, der Bezeichner hat sich geändert, oder die Sektion hat keinen Bezeichner | Leeren Sie `template-override`, `template` und `content`; prüfen Sie `Match[...]` anhand der Seite der Sektion. |
| Eine auf eine Sektion eingeschränkte Rollenzuweisung hat ihr Entfernen nicht verhindert (SQLite, PostgreSQL, MongoDB, vor Oktober 2026) | Der Kernel hat die Einschränkung der Zuweisung mit Beachtung der Groß- und Kleinschreibung verglichen | Behoben: `eZRole::fetchRolesByLimitation()` vergleicht jetzt ohne Beachtung der Groß- und Kleinschreibung. |

## 15. Wer die Seiten zu den Sektionen verwenden darf

| Seite | Adresse | Braucht |
|---|---|---|
| Die Liste | `/section/list` | `section/view`, `section/edit` oder `section/assign` |
| Eine Sektion | `/section/view/<ID>` | `section/view` oder `section/assign` |
| Neu und Bearbeiten | `/section/edit/<ID>`, `/section/edit/0` | `section/edit` |
| Ausgewählte entfernen | die Liste | `section/edit` ohne Einschränkung |
| Inhalt zuweisen | `/section/assign/<ID>` | `section/assign` mit der Sektion in NewSection (oder ohne diese Einschränkung) |

Die Liste und die Seite der Sektion zeigen Bearbeiten, Neue Sektion und die Kontrollkästchen nur Benutzern, die
Sektionen bearbeiten dürfen, und Inhalt zuweisen nur dort, wo es erlaubt ist.

## Quellen

- Modul und Views: `kernel/section/module.php`, `kernel/private/classes/views/section/list.php` (die Übersicht:
  `ListView::overview()`, `overviewFromDatabase()`), `view.php`, `edit.php`, `assign.php`.
- Templates: `design/admin4/templates/section/` und dieselben Dateien in `design/admin/templates/section/`
  (`list.tpl`, `view.tpl`, `edit.tpl`, `confirmremove.tpl`, `assign_notification.tpl`, `browse_assign.tpl`,
  `exp_style.tpl`).
- Die Klasse der Sektion: `kernel/classes/ezsection.php` (`canBeRemoved()`, `applyTo()`, `fetchByIdentifier()`).
- Template-Fetch-Funktionen: `kernel/section/function_definition.php`, `kernel/section/ezsectionfunctioncollection.php`.
- Einen Teilbaum zuweisen: `eZContentObjectTreeNode::assignSectionToSubTree()` in `kernel/classes/ezcontentobjecttreenode.php`;
  der Inhaltsjob in `kernel/classes/contentjob/expcontentjobsectionsubtree.php`.
- Sektion beim Anlegen und Veröffentlichen: `eZContentObject::createWithNodeAssignment()`,
  `eZContentOperationCollection::updateSectionID()`; beim Verschieben: `kernel/classes/ezcontentobjecttreenodeoperations.php`.
- Berechtigungen: die Sektions-Einschränkung der content-Funktionen in `kernel/content/module.php`;
  `eZUser::canAssignSection()`, `canAssignSectionToObject()`, `canAssignSectionList()` in
  `kernel/classes/datatypes/ezuser/ezuser.php`; Rollenzuweisungen mit einer Sektion in `eZRole::assignToUser()` und
  `eZRole::fetchRolesByLimitation()` (`kernel/classes/ezrole.php`).
- Navigationsbereiche: `settings/menu.ini [NavigationPart]`, `kernel/classes/eznavigationpart.php`,
  `kernel/classes/eznodeviewfunctions.php`.
- Override-Schlüssel: `kernel/common/eztemplatedesignresource.php`, `kernel/classes/eznodeviewfunctions.php`,
  `settings/override.ini`.
- Caches: `eZContentCacheManager::clearContentCacheIfNeededBySectionID()` und `clearAllContentCache()` in
  `kernel/classes/ezcontentcachemanager.php`; das Ereignis `content/section/cache`.
- Standarddaten: `share/db_data.dba`, `kernel/sql/common/cleandata.sql`.
- Seitenaufteilung: `settings/admininterface.ini [PaginationSettings]`, [Seitenaufteilung der Admin-Listen](../funktionen/6.0/seitenweise-listen-im-admin.md).
- Tests: `tests/tests/kernel/classes/expSectionOverviewTest.php` (die Übersicht ohne Datenbank und gegen die eigenen
  Abfragen des Kernels auf einer laufenden Installation).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
