# Redaktioneller Leitfaden: Hilfethemen in Exponential

Status: Vorschlag, 18. Oktober 2026. Grundlage: [der Plan für das Hilfesystem](hilfesystem.md) (Inhaltsmodell, Zugriff,
Arbeitsablauf). Die maschinell prüfbaren Regeln prüft seit 6.0.15 `./console exp:help:content check`
([Hilfe als Inhalt gepflegt](../../funktionen/6.0/hilfeinhalte.md#der-validator)).

Dieser Leitfaden gilt für jedes Thema im Themenpool (`help-pool`). Einen Teil
davon prüft der Validator `check_help.php`; der Rest ist Aufgabe des
Reviews. Die Begriffe sind im Glossar festgelegt (siehe [Terminologie](#terminologie)).

Die Beispiele stammen aus der Administrationsoberfläche von Exponential, sodass sie
sich an einer laufenden Installation überprüfen lassen. Eine Site schreibt ihre eigenen Themen in
derselben Form.

**Verwenden Sie den Produktnamen, den die Leserin oder der Leser auf dem Bildschirm sieht**, keinen internen Projekt-
oder Codenamen.

## 1. Thementypen

Jedes Thema hat **genau einen** Typ (DITA 1.3). Mischformen gibt es nicht: Ein
Thema, das erklärt *und* anleitet, wird in zwei Themen aufgeteilt, die über
`related` aufeinander verweisen. Ein „FAQ“ ist eine Ansicht (eine `help_section` mit
Verweisen), kein Typ.

| Klasse | DITA | Frage, die das Thema beantwortet | Form des Titels | Pflichtfelder |
|---|---|---|---|---|
| `help_concept` | concept | Was ist das, wozu dient es? | Substantiv: „Sektionen“ | `title`, `shortdesc`, `help_id`, `audience`, `body` |
| `help_task` | task | Wie mache ich das? | Verb, im Infinitiv oder substantiviert: „Einen Artikel veröffentlichen“ | `title`, `shortdesc`, `help_id`, `audience`, `steps` |
| `help_reference` | reference | Welche Werte, Optionen, Grenzen gibt es? | Substantiv, oft im Plural: „Objektzustände“ | `title`, `shortdesc`, `help_id`, `audience`, `body` |
| `help_troubleshooting` | troubleshooting | Was stimmt nicht, und wie behebe ich es? | Das Symptom in den Worten der Lesenden: „Ein veröffentlichter Artikel erscheint nicht auf der Site“ | `title`, `shortdesc`, `help_id`, `audience`, `condition`, `remedy` |

Gemeinsame optionale Felder: `platform`, `version_from`, `version_to`,
`review_date`, `owner`, `related`, `figures`. Redakteure füllen `platform`,
`version_from`, `review_date` und `owner` **immer** aus (Abschnitt 8); das
Inhaltsmodell erzwingt sie nur deshalb nicht, damit sich ein Entwurf auch
ohne sie speichern lässt.

**Welcher Typ wann:**

| Wenn der Text … | dann |
|---|---|
| eine Folge von Handlungen beschreibt, die die Lesenden ausführen | `help_task` |
| erklärt, Hintergrund liefert, Regeln erläutert (Berechtigungen, warum etwas gesperrt ist) | `help_concept` |
| hauptsächlich aus einer Tabelle oder Liste zum Nachschlagen besteht | `help_reference` |
| von einer Fehlermeldung oder einem unerwarteten Zustand ausgeht | `help_troubleshooting` |
| im `context` einer Aufgabe mehr als zwei Sätze Erklärung braucht | die Erklärung in ein `help_concept` verschieben, einen Satz in `context` behalten und `related` ergänzen |

### 1.1 Vorlage `help_concept`

| Feld | Inhalt |
|---|---|
| `title` | Entwürfe und Versionen |
| `shortdesc` | Jede Änderung an Inhalten wird als neue Version gespeichert; die Lesenden sehen nur die veröffentlichte. |
| `help_id` | `content.versions` |
| `audience` | Redakteure |
| `platform` | Admin |
| `version_from` | 6.0.0 |
| `body` | siehe unten |

```
When you edit an object, Exponential creates a draft. The draft is a new
version of the object. Readers of the site do not see it.

When you publish the draft, it becomes the current version. The previous
version stays in the history. You can compare versions and copy an old one
into a new draft.

Drafts you do not publish stay in "My drafts" until you discard them.
```

### 1.2 Vorlage `help_task`

| Feld | Inhalt |
|---|---|
| `title` | Einen Artikel veröffentlichen |
| `shortdesc` | Einen Artikel in einem Ordner anlegen und auf der Site veröffentlichen. |
| `help_id` | `content.publish-article` |
| `audience` | Redakteure |
| `platform` | Admin |
| `version_from` | 6.0.0 |
| `context` | Ein Artikel erscheint auf der Site, sobald Sie ihn veröffentlichen, sofern seine Sektion und sein Zustand es den Lesenden erlauben, ihn zu sehen. |
| `prereq` | Sie sind in der Administrationsoberfläche angemeldet und dürfen in dem Ordner Inhalte anlegen. |
| `steps` | siehe unten |
| `result` | Der Artikel ist veröffentlicht. Er wird im Ordner aufgelistet und ist auf der Site sichtbar. |
| `postreq` | Prüfen Sie den Artikel auf der Site mit **Vorschau** oder indem Sie seine Seite öffnen. |
| `related` | `content.versions`, `content.not-visible` |

```
1. Open the folder in which the article belongs.
2. Select **Article** in the list of content classes.
3. Click **Create here**.
   The edit form opens.
4. Fill in at least the title and the text.
5. Click **Send for publishing**.
   The folder opens again and shows the new article.
```

### 1.3 Vorlage `help_reference`

| Feld | Inhalt |
|---|---|
| `title` | Schaltflächen im Bearbeitungsformular |
| `shortdesc` | Diese Schaltflächen stehen unten im Bearbeitungsformular, und das bewirkt jede von ihnen. |
| `help_id` | `content.edit-buttons` |
| `audience` | Redakteure |
| `body` | Tabelle (unten) |

| Schaltfläche | Was passiert | Auf der Site sichtbar? |
|---|---|---|
| **Zur Veröffentlichung schicken** | der Entwurf wird zur aktuellen Version | ja, sofern ihn kein Workflow zurückhält |
| **Entwurf speichern** | der Entwurf wird gespeichert, das Formular bleibt offen | nein |
| **Entwurf verwerfen** | der Entwurf wird gelöscht | nein, die aktuelle Version bleibt |
| **Vorschau** | zeigt den Entwurf so, wie die Site ihn zeigen würde | nein |

### 1.4 Vorlage `help_troubleshooting`

| Feld | Inhalt |
|---|---|
| `title` | Ein veröffentlichter Artikel erscheint nicht auf der Site |
| `shortdesc` | Wenn ein Artikel veröffentlicht ist, auf der Site aber fehlt, liegt es meist an seiner Sektion, seinem Zustand oder am Cache. |
| `help_id` | `content.not-visible` |
| `audience` | Redakteure, Administratoren |
| `condition` | Der Artikel ist in der Administrationsoberfläche als veröffentlicht aufgeführt. Auf der Site fehlt er in der Liste, oder seine Adresse zeigt „Zugriff verweigert“. |
| `cause` | Anonyme Lesende dürfen die Sektion oder den Objektzustand des Artikels nicht lesen, oder die Site zeigt noch eine zwischengespeicherte Seite. |
| `remedy` | Öffnen Sie den Artikel in der Administrationsoberfläche, und sehen Sie sich **Sektion** und **Objektzustände** an. Vergleichen Sie sie mit einem Artikel, der sichtbar ist. Wenn sie gleich sind, bitten Sie einen Administrator, den Cache zu leeren. |

Ein Thema zur Fehlerbehebung geht vom **Symptom** aus, das die Lesenden sehen. Gibt es
eine Fehlermeldung, steht sie wörtlich in `condition` (in Anführungszeichen,
nicht fett: Sie ist Text, keine Beschriftung). Genau danach suchen die Lesenden.

### 1.5 Zweite Vorlage `help_task`, für die Anmeldung

| Feld | Inhalt |
|---|---|
| `title` | Anmelden |
| `shortdesc` | Melden Sie sich mit Ihrem Benutzernamen und Ihrem Passwort an. |
| `help_id` | `account.log-in` |
| `audience` | Mitglieder |
| `prereq` | Sie haben ein Benutzerkonto. |
| `steps` | 1. Öffnen Sie die Anmeldeseite. 2. Geben Sie Ihren Benutzernamen in **Benutzername** ein. 3. Geben Sie Ihr Passwort in **Passwort** ein. 4. Klicken Sie auf **Login**. Ihre Startseite öffnet sich. |
| `result` | Sie sind angemeldet. |
| `related` | `account.forgot-password` |

Varianten (Single Sign-on, Anmeldung mit zwei Faktoren) sind **eigene** Themen, keine
Wenn-dann-Verzweigungen in den Schritten. Ein Schritt, der mit „Wenn …“ beginnt, ist erlaubt,
wenn er die einzige Verzweigung im Thema ist.

Beschriftungen sind in diesem Leitfaden so zitiert, wie die deutsche Administrationsoberfläche sie zeigt.
Bevor ein Thema freigegeben wird, wird jede Beschriftung an der Installation geprüft,
die es beschreibt (Abschnitt 4).

## 2. Minimalismus

- **Die Handlung zuerst.** Kein „In diesem Abschnitt erfahren Sie …“, kein „Exponential
  bietet die bequeme Möglichkeit, …“. Der erste Satz einer Aufgabe ist der
  erste Schritt oder die Voraussetzung.
- **Nur, was die Lesenden für ihre Aufgabe brauchen.** Technik (Server, Interna des Cache,
  Suchindex) kommt in der Hilfe für Besucher und Mitglieder nicht vor.
- **Eine Aussage pro Satz.** Begründungen kurz und nach der Handlung.
- **Nichts Selbstverständliches:** kein „Klicken Sie auf **Speichern**, um zu speichern“.
- **Fehler einplanen.** Was schiefgehen kann, bekommt ein eigenes Thema zur Fehlerbehebung
  und einen `related`-Verweis, keinen Absatz in der Aufgabe.

## 3. Sprache

### 3.1 Anrede

Sprechen Sie die Lesenden mit **Sie** an, und zwar in jeder Zielgruppe. Ein Thema für mehrere
Zielgruppen verwendet durchgehend dieselbe Form. Für Sprachen mit einer förmlichen und einer
vertraulichen Anrede entscheidet jede Installation pro Zielgruppe und hält das in
ihrem Glossar fest; die Regel lautet, der Oberfläche zu folgen, die die Lesenden verwenden.

### 3.2 Einfache Sprache (Besucher, Mitglieder)

| Regel | Wert |
|---|---|
| Satzlänge | höchstens 20 Wörter |
| Nebensätze | höchstens einer pro Satz |
| Genus Verbi und Zeitform | Aktiv, Präsens |
| Fremdwörter, Abkürzungen | bei der ersten Verwendung erklären; keine in Titeln |
| Verneinung | nie doppelt |
| Zahlen | Ziffern („5 Schritte“) |
| Absatz | höchstens 4 Sätze |
| Verweise auf Gesetze | nur in einem Datenschutzhinweis |

Lesende mit wenig Leseübung (Kinder, Lesende in einer Zweitsprache)
brauchen engere Grenzen; eine Installation mit einer solchen Zielgruppe legt sie hier fest,
z. B. 15 Wörter pro Satz und 3 Sätze pro Absatz.

### 3.3 Fachsprache (Redakteure, Administratoren)

- Sätze mit bis zu 25 Wörtern.
- Die Exponential-Begriffe der Administrationsoberfläche (Objekt, Knoten, Inhaltsklasse,
  Sektion, Objektzustand, Version, Siteaccess) werden ohne Erklärung verwendet.
- „Klasse“ bedeutet in der Hilfe für Redakteure immer **Inhaltsklasse**. Hat die Site
  eine eigene Bedeutung des Wortes, entscheidet das Glossar.

## 4. Schritte

| Regel | Richtig | Falsch |
|---|---|---|
| Imperativ, der erste Satz ist die Handlung | Klicken Sie auf **Hier erstellen**. | Jetzt können Sie auf „Hier erstellen“ klicken. |
| Eine Handlung pro Schritt | 2. Geben Sie Ihr Passwort ein. 3. Klicken Sie auf **Login**. | 2. Geben Sie Ihr Passwort ein, und klicken Sie auf **Login**. |
| Das Ergebnis nach dem Schritt, als eigener Satz | Klicken Sie auf **Zur Veröffentlichung schicken**. Der Ordner öffnet sich wieder. | Klicken Sie auf **Zur Veröffentlichung schicken**, woraufhin sich der Ordner wieder öffnet. |
| Bedingung vor der Handlung | Wenn der Artikel noch kein Bild hat, klicken Sie auf **Datei hochladen**. | Klicken Sie auf **Datei hochladen**, wenn der Artikel noch kein Bild hat. |
| UI-Beschriftungen exakt und fett, ohne Anführungszeichen | **Zur Veröffentlichung schicken** | „Veröffentlichen“, *Zur Veröffentlichung schicken*, „Zur Veröffentlichung schicken“ |
| Ein Verb pro Plattform | Web und Admin: „klicken“; Touch-Geräte: „tippen“ | „klicken“, „drücken“ und „tippen“ in einem Thema mischen |

Weitere Regeln:

- `steps` enthält **genau eine** nummerierte Liste (vom Validator geprüft),
  keine Unterschritte. Mehr als 9 Schritte: in zwei Aufgaben aufteilen.
- Schritte kommen nicht in `context`, `prereq` oder `result` vor.
- **Beschriftungen stammen aus den Übersetzungsdateien der Oberfläche**, nicht aus dem Gedächtnis und
  nicht von einem Bildschirmfoto einer alten Version. Platzhalter werden durch ein
  Beispiel ersetzt.
- Enthält eine Beschriftung ein Wort, das das Glossar vermeidet, wird sie trotzdem
  **exakt** zitiert; im Fließtext gilt der Glossarbegriff. Die Unstimmigkeit
  geht als Ticket an die Entwickler, nicht in den Hilfetext.
- Unterscheidet sich der Weg je Plattform, bekommt jede Plattform ein eigenes Thema mit einer
  `.variant` in der `help_id` (Abschnitt 7) – nur wenn sich die **Schritte** unterscheiden, nicht
  bloß das Aussehen.

## 5. Hinweise

IEC/IEEE 82079-1:2019 verlangt Sicherheitshinweise an der Stelle der Gefahr und eine
abgestufte Kennzeichnung; die Signalwörter GEFAHR, WARNUNG und VORSICHT (nach
ISO 3864-2) beziehen sich auf **Gefährdungen von Personen**. Websoftware ohne Aktoren
verursacht keine körperliche Gefährdung; diese drei Stufen werden deshalb **nicht verwendet**.
Wer sie trotzdem verwendet, entwertet sie für den Fall, in dem es auf sie ankommt, und
lässt das Löschen eines Ordners so laut klingen wie einen Stromschlag.

| Stufe | DITA `note/@type` | Wofür | Beispiel |
|---|---|---|---|
| **Achtung** | `notice` | Folgen, die sich nicht rückgängig machen lassen: Datenverlust, Links brechen, Zugriff geht verloren | Ein Objekt ohne den Papierkorb entfernen; eine Inhaltsklasse löschen; einen Benutzer entfernen |
| **Datenschutz** | `other`, `othertype="dataprotection"` | wer nach dieser Handlung welche Daten sieht, was an Dritte geht | Eine Sektion für anonyme Benutzer lesbar machen; externe Medien einbetten |
| **Hinweis** | `note` | nützliche Zusatzinformation ohne Folgen | Ein Entwurf bleibt erhalten, bis Sie ihn verwerfen |

Regeln:

- **Dort, wo die Handlung geschieht.** Ein Achtungs- oder Datenschutzhinweis
  steht im Schritt, **vor** der Handlung, die ihn auslöst – nicht in
  `context`, nicht am Ende, nicht in einem Sammelkapitel „Datenschutz“.
- Ein solcher Hinweis ist die einzige Ausnahme von „Der erste Satz ist die Handlung“:
  Er steht im Schritt **vor** dem Imperativ.
- Aufbau: Stufe – was passiert – was die Lesenden tun können. Höchstens drei
  Sätze.
- Ein Hinweis pro Schritt. Wer mehr braucht, hat den Schritt zu groß geschnitten.
- Der Datenschutzhinweis wiederholt nicht die Datenschutzerklärung; er nennt
  nur, was **diese** Handlung bewirkt, und verlinkt die Rechtstexte.

Beispiel in einem Schritt:

```
4. **Attention:** Without "Move to trash" the objects are deleted at once and
   cannot be restored.
   Click **OK**.
```

## 6. Abbildungen (`help_screenshot`)

**Wann überhaupt eine Abbildung:** nur wenn ein Bedienelement schwer zu finden ist (Symbol ohne
Beschriftung, Menü hinter einem Symbol) oder wenn der Zielzustand visuell
geprüft werden muss (ein Diagramm, eine Vorschau). Nicht eine pro Schritt, nicht als Dekoration. Ein
Thema muss **ohne** die Abbildung vollständig sein – die Abbildung ergänzt, sie trägt nie
allein eine Information.

**Generierte Abbildungen bevorzugen.** Wo die Oberfläche durch automatisierte
Browser- oder UI-Tests abgedeckt ist, lassen Sie die Tests die echten Bildschirme mit erfundenen Daten
zeichnen und die Bilder speichern. Ändert sich die Oberfläche, laufen die Tests erneut, und die
Abbildung wird ersetzt. Ein von Hand aufgenommenes Bildschirmfoto veraltet unbemerkt.

| Feld | Regel |
|---|---|
| `platform` | die Plattform der Aufnahme; eine andere Plattform nur als eigene Abbildung, wenn sie anders aussieht |
| `version` | die für die Aufnahme verwendete Version, `MAJOR.MINOR.PATCH` |
| `language` | die Sprache der Oberfläche in der Abbildung |
| `caption` | kurz, was gezeigt wird: „Bearbeitungsformular eines Artikels“ |
| `alt_text` | siehe unten; Pflicht |
| `section` | das Feld des Themas, unter dem die Abbildung steht (`steps`, `body`, `context` …); ist es leer, steht sie unter dem letzten |
| `figure_id` | `<help_id>/<n>` für generierte Abbildungen; leer für eine von Hand erstellte Abbildung |

Die Sektion einer Abbildung setzt `check_help.php --fix` anhand der Themen, die
auf sie verweisen: öffentlich, sobald eines davon öffentlich ist. Ausgeliefert wird sie erst, wenn sie
**freigegeben** ist, wie das Thema.

**Aufnahme** (auch für eine von Hand erstellte Abbildung): helles Theme, Standardschriftgröße,
die Oberflächensprache des Themas, keine Benachrichtigungen, keine
Browser-Erweiterungen im Bild. Hervorheben höchstens mit einem Rahmen um das gemeinte Element,
keine Pfeile mit Text.

**Keine echten personenbezogenen Daten.** Nehmen Sie nur mit Testkonten auf: erfundene Namen,
E-Mail-Adressen unter `example.org`, keine echten Kunden-, Firmen- oder
Schulnamen, keine echten Zugangscodes oder Tokens. Wer ein Bildschirmfoto aus einem
Produktivsystem braucht, schwärzt zuerst – deckend, nicht weichgezeichnet.

**Alternativtext:**

- beschreibt, **was die Lesenden für ihren Schritt aus der Abbildung brauchen**, nicht das
  Bild als solches: „Bearbeitungsformular mit den Feldern Titel und Text, darunter die
  Schaltfläche Zur Veröffentlichung schicken.“
- nicht „Bild von …“, „Bildschirmfoto von …“ – das sagt der Screenreader selbst;
- Beschriftungen wörtlich, ohne Formatierung;
- **höchstens 150 Zeichen**, ein Satz (das Feld fasst 500; die Reserve
  ist für Diagramme gedacht, deren Aussage sonst verloren ginge). Der Validator
  meldet mehr bei einer generierten Abbildung als ERROR, sonst als WARNING;
- wiederholt nicht die Bildunterschrift.

Mit jeder Minor-Version prüfen die Redakteure die Abbildungen der betroffenen
Bereiche; eine Abbildung, die eine andere Oberfläche zeigt, wird ersetzt, nicht als
„ungefähr richtig“ stehen gelassen.

## 7. `help_id`

Die `help_id` ist der stabile Schlüssel eines Themas: Kontexthilfe, DITA-`id`,
Dateiname im Export.

**Schema:** `area.topic[.variant]`:

- nur `a-z`, `0-9`, ein Bindestrich innerhalb eines Teils, ein Punkt zwischen den Teilen;
- mindestens zwei Teile, höchstens 80 Zeichen;
- immer Englisch und ASCII, unabhängig von der Sprache des Textes: Die ID ist ein
  Schlüssel, kein Titel;
- `topic` benennt den Gegenstand, nicht den Typ: `account.log-in`, nicht
  `account.task-log-in`;
- `variant` nur für Plattform- oder Rollenvarianten: `account.log-in.sso`,
  `media.upload.mobile`.

**Bereiche** (Beispiel für die Administration von Exponential; eine Site ergänzt eigene):

| Bereich | Inhalt |
|---|---|
| `account` | anmelden, abmelden, Passwort, Profil, Anmeldung mit zwei Faktoren |
| `content` | anlegen, bearbeiten, veröffentlichen, Versionen, Übersetzungen, verschieben, Papierkorb |
| `media` | Bilder, Dateien, Mediathek |
| `search` | Suche auf der Site, Suche in der Administrationsoberfläche |
| `users` | Benutzer, Gruppen, Rollen und Richtlinien |
| `setup` | Sektionen, Objektzustände, Inhaltsklassen, Cache, Siteaccesses |
| `privacy` | personenbezogene Daten, Löschen von Daten, Rechtstexte |
| `help` | Pflege der Hilfe, Glossar |

Ein neuer Bereich wird hier eingetragen, bevor das erste Thema ihn verwendet.

**Eine `help_id` wird nie umbenannt und nie wiederverwendet.** Clients rufen sie auf; eine geänderte
ID ist ein toter Link für jede bereits verwendete Client-Version. Ändert sich der Gegenstand
eines Themas so stark, dass die ID nicht mehr passt, wird ein neues Thema mit einer neuen ID
angelegt; das alte bekommt `version_to` und einen Link auf das neue. Ein Tippfehler
in einer freigegebenen ID bleibt.

## 8. Felder

### `shortdesc`

- **Ein** Satz, mit Punkt, höchstens 250 Zeichen; anzustreben sind weniger als
  150.
- Sagt, was die Lesenden danach tun können oder wissen. Aufgabe: Zweck der Handlung;
  Konzept: die Kernaussage; Referenz: was die Tabelle enthält;
  Fehlerbehebung: Symptom und Richtung der Lösung.
- Wiederholt nicht den Titel, beginnt nicht mit „Dieses Thema …“.
- Kein Markup, kein Link, keine fetten Beschriftungen – die `shortdesc` ist zugleich Linktext,
  Suchtreffer und Vorschau.

### Zielgruppe und Sichtbarkeit

| `audience` enthält | Sektion | Wer liest |
|---|---|---|
| eine öffentliche Zielgruppe (auch zusammen mit anderen) | `help_public` | **alle, ohne Anmeldung** |
| nur Zielgruppen, die eine Anmeldung erfordern | `help_internal` | Angemeldete mit passender Rolle |
| nichts | `help_internal` | sichere Rückfallebene |

Daraus folgt:

- **Ein Thema für Besucher *und* Redakteure ist öffentlich.** Was nur Redakteure
  wissen sollen, gehört in ein eigenes internes Thema.
- Lesende mit einer weiter gefassten Rolle lesen auch die Themen der engeren Zielgruppen;
  ein Thema für Redakteure verlinkt deshalb auf das Thema für Mitglieder, statt es
  zu wiederholen.
- Die Sektion setzt `check_help.php --fix`, **nicht die Redakteure von
  Hand**.

### Zustände (`help_status`)

| Zustand | Bedeutung | Wer ihn setzt |
|---|---|---|
| Entwurf | in Arbeit, wird nirgends ausgeliefert | Hilferedakteur (Standard beim Anlegen) |
| Review | bereit zur Prüfung: fachlich (stimmt es mit der Software überein?) und sprachlich (dieser Leitfaden) | Hilferedakteur |
| Freigegeben | ausgeliefert (Web, Kontexthilfe, E-Book) | nur jemand mit der Freigaberolle, nicht der Autor des Themas |

Jede Änderung an einem freigegebenen Thema, selbst ein Tippfehler: Jemand mit der Freigaberolle
setzt es auf **Review** zurück, der Redakteur ändert es, dann wird es erneut freigegeben.

### Version und Prüfdatum

| Feld | Regel |
|---|---|
| `version_from` | erste Version der Software, für die das Thema zutrifft, `MAJOR.MINOR.PATCH` |
| `version_to` | letzte Version, für die es zutrifft; **leer = aktuell**. Wird gesetzt, wenn eine neue Version das Vorgehen ändert, und gleichzeitig wird das Nachfolgethema angelegt |
| `review_date` | Freigabedatum + 12 Monate; Bereiche `privacy` und `account` + 6 Monate. Der Validator meldet überschrittene Prüfdaten |
| `owner` | eine Person, kein gemeinsam genutztes Konto |

## Terminologie

Das Glossar entscheidet: `help_glossary_entry` im Ordner „Glossar“
(`help-pool-glossary`). Jeder Eintrag nennt den Begriff, die zu vermeidenden Synonyme und
den Grund. Grundsatz: **Das Wort, das die Oberfläche zeigt**, hat Vorrang vor dem Wort
aus Spezifikationen, Tickets oder dem Code. Der Validator meldet die abgelehnte
Form.

Typische Entscheidungen für Exponential:

| Verwenden | Vermeiden | Grund |
|---|---|---|
| Objekt | Inhaltselement, Eintrag | der Begriff der Administrationsoberfläche |
| Inhaltsklasse | Klasse, Typ, Template | „Klasse“ ist mehrdeutig; „Template“ ist eine Design-Datei |
| veröffentlichen | speichern, freigeben | **Zur Veröffentlichung schicken** erzeugt die veröffentlichte Version; „freigeben“ ist dem Arbeitsablauf der Hilfe vorbehalten |
| Entwurf | ungespeicherte Version | der Begriff der Administrationsoberfläche („Meine Entwürfe“) |
| Papierkorb | Mülleimer, gelöschte Elemente | der Begriff der Administrationsoberfläche |

Ein Begriff, der im Glossar fehlt, wird ergänzt, bevor er in mehr als einem
Thema vorkommt.

## Siehe auch

- [Plan für das Hilfesystem](hilfesystem.md)
- [Glossar](../../glossar.md)
