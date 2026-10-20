# Die Seite „Extensions“: Extensions aktivieren, deaktivieren und ordnen

> **Handlungsbedarf:** Das Exportieren von Extensions ist jetzt standardmäßig ausgeschaltet. Tragen Sie die Extension in package.ini
> [ExtensionExportSettings] AllowedExtensions[] ein (oder `*` für alle Extensions) und erteilen Sie der Rolle die Richtlinie
> package/export_extension. Ohne beides zeigt Setup > Extensions keine Download-Knöpfe, und der Paket-Assistent
> lässt die Extension nicht auswählen; die Kommandozeile (`ezpm.php add`) braucht den Listeneintrag.

Diese Anleitung richtet sich an Administratoren. **Setup > Extensions** (`/setup/extensions`) listet jede Extension auf, die die Installation
sieht, zeigt, welche aktiv sind und in welcher Reihenfolge, und ändert diese Liste. Nach dieser Seite können Sie eine Karte lesen,
Extensions sicher verschieben, aktivieren und deaktivieren und wissen, was beim Übernehmen geschieht. Etwa 10 Minuten.
Zum Bauen, Installieren und Veröffentlichen von Extensions siehe die Entwickleranleitung [Extensions](extensions.md).

## 1. Eine Liste, in der Ladereihenfolge

Die Seite hat eine einzige Liste. Zuerst kommen die aktiven Extensions in der Reihenfolge von `[ExtensionSettings] ActiveExtensions[]`
in `settings/override/site.ini.append.php`, ab 1 nummeriert; es folgen die inaktiven, nach Namen. Eine Extension weiter oben in
der Liste hat die höhere Priorität: Wenn zwei Extensions dieselbe Einstellung oder dasselbe Design-Template mitliefern, gewinnt die
früher geladene. Deshalb steht zum Beispiel die eigene Einstellungs-Extension einer Installation an erster Stelle.

Jede Karte zeigt:

| Auf der Karte | Was es bedeutet |
|---|---|
| Die Nummer | Ihre Position in `ActiveExtensions` oder ein Strich, wenn sie nicht aktiv ist |
| Name, Titel, Version, Beschreibung | Aus der `extension.xml` der Extension, sonst aus ihrer `ezinfo.php` |
| Lizenz, Website | Dieselben Quellen; die Website nur, wenn sie eine `http(s)`-Adresse ist |
| Aktiv / Inaktiv / Nur für Siteaccesses | Ob sie in `ActiveExtensions` steht oder nur in den `ActiveAccessExtensions` eines Siteaccess |
| Access-Extension: *Siteaccess* | Ein Siteaccess, der sie in seiner eigenen `site.ini.append.php` einschaltet |
| git | Der Extension-Ordner ist ein Git-Checkout: Aktualisieren Sie ihn mit Git, nicht mit einem Paket |
| Benötigt, Verwendet, Erweitert | Ihre deklarierten Abhängigkeiten in `extension.xml` `<dependencies>` |
| Benötigt von | Aktive Extensions, die sie benötigen oder verwenden |
| Geladen als Nummer *n* | Wo das System sie tatsächlich lädt (siehe Abschnitt 4), wenn das von der geschriebenen Position abweicht |
| Probleme | Eine benötigte Extension, die nicht aktiv ist, ein fehlender Ordner, eine Reihenfolge, die die Abhängigkeiten nicht zulassen |
| Details | Designs der Extension, die Siteaccesses verwenden, Autor, Copyright, letzte Änderung, Downloads und Hinweise |

Über der Liste: Zähler (aktiv, inaktiv, nur für Siteaccesses, Git-Checkouts, mit Problemen), eine Suche (Name,
Beschreibung, Lizenz), die Sortierung (**Ladereihenfolge**, **Name (A bis Z)** oder **Name (Z bis A)**) und ein Filter, jede Auswahl
mit ihrer Anzahl: **Alle (87)**, **Aktiv (62)**, **Inaktiv (25)**, **Probleme (0)**. Die Pfeiltasten wechseln zwischen den
Auswahlmöglichkeiten einer Gruppe. Sortierung und Filter bleiben in der Adresse erhalten, sodass ein Neuladen oder ein Lesezeichen dieselbe Liste zeigt:
`/setup/extensions/(sort)/name/(dir)/desc/(show)/active`. Die Griffe zum Ziehen und Ablegen werden nur in der Ladereihenfolge angezeigt.
In AdminUI sortieren die Spaltenköpfe **Reihenfolge** und **Name** der Tabelle auf dieselbe Weise, aufsteigend und absteigend.

## 2. Die Liste ändern

Während Sie arbeiten, wird nichts geschrieben. Jeder Knopf ändert nur den Plan, den die Seite mitführt, und die Leiste **Noch nicht
übernommen** sagt, wie viele Extensions aktiviert, deaktiviert und verschoben werden.

- **Verschieben**: die Pfeile nach oben und nach unten auf einer aktiven Karte. Mit JavaScript können Sie eine Karte auch an ihrem Griff unter der
  Nummer ziehen oder den Griff fokussieren und die Pfeiltasten nach oben und nach unten drücken.
- **Aktivieren**: der Knopf einer inaktiven Karte. Die Extension wird hinter die Extensions gesetzt, die sie benötigt oder verwendet, und vor
  diejenigen, die sie erweitert; ohne deklarierte Abhängigkeiten kommt sie ans Ende. Verschieben Sie sie anschließend, wenn Sie sie an anderer Stelle möchten.
- **Deaktivieren**: der Knopf einer aktiven Karte. Bei einer Extension, die in `ActiveExtensions` steht, deren Ordner aber fehlt, lautet er stattdessen
  **Aus der Liste entfernen**.
- **Änderungen verwerfen** kehrt zur Liste zurück, wie sie in der Datei steht.

Alles funktioniert ohne JavaScript: Jeder Knopf lädt die Seite mit dem Plan neu, und **Anzeigen** wendet die Suche und
den Filter an. Mit JavaScript funktionieren die Pfeile, das Ziehen und Ablegen, die Suche, der Filter und die Reihenfolge direkt auf der Seite.

## 3. Prüfen und übernehmen

**Änderungen prüfen** zeigt, bevor etwas geschrieben wird:

- die aktivierten, deaktivierten und verschobenen Extensions;
- die Risiken, zum Beispiel:
  - das Deaktivieren einer Extension, die eine andere aktive Extension benötigt;
  - das Deaktivieren der einzigen aktiven Extension, die ein von einem Siteaccess verwendetes Design bereitstellt;
  - das Deaktivieren von `ezformtoken` (des CSRF-Schutzes jedes Formulars) oder von `ezjscore` (der Skripte der Administration);
  - eine Verschiebung, durch die die vollständige Einstellungsdatei einer anderen Extension über die eigenen Änderungen einer Extension daran gewinnt (zum Beispiel eine
    Einstellungs-Extension der Installation, die hinter `ezoracle` verschoben wird, dessen `ezoracle.ini` dann gewinnt);
  - eine Umordnung, die nichts ändert, weil die deklarierten Abhängigkeiten entscheiden (Abschnitt 4);
- die resultierenden `ActiveExtensions[]`-Zeilen, mit markierten geänderten Zeilen.

Rot markierte Risiken müssen mit dem Kästchen darunter bestätigt werden, bevor **Änderungen anwenden** angenommen wird. Das Übernehmen dann:

1. bewahrt eine Kopie von `settings/override/site.ini.append.php` in `var/<site>/backups/settings-override/` auf;
2. schreibt nur die `ActiveExtensions[]`-Zeilen neu. Jede andere Zeile bleibt, wie sie war, einschließlich Kommentaren: Ein Kommentar
   direkt über der Zeile einer Extension wandert mit ihr, und einer einer deaktivierten Extension bleibt in der Datei.
   Die Datei wird anschließend erneut gelesen, und die Kopie wird zurückgespielt, wenn sich irgendetwas anderes geändert hat;
3. leert die Caches für INI, Template-Overrides, Design und aktive Extensions (und den Benutzerinfo-Cache, wenn sich die Module
   geändert haben) und erzeugt die Autoload-Arrays der Extensions neu;
4. zeigt, was sich geändert hat. **Wenn die Site auch von Velocity ausgeliefert wird, starten Sie Velocity neu** (`./console exp:velocity restart`): Seine
   Worker behalten die Extension-Liste, mit der sie gestartet sind. Die Seite sagt, ob Velocity läuft.

Hat jemand die aktiven Extensions in der Zwischenzeit geändert (ein anderer Tab, ein anderer Administrator, eine Bearbeitung von Hand), sagt die
Seite das und beginnt erneut ausgehend von der Datei, statt diese Änderung zu überschreiben.

## 4. Geschriebene Reihenfolge und Ladereihenfolge

Mit `[ExtensionSettings] ExtensionOrdering=enabled` (dem Standard in `settings/site.ini`) lädt das System die
Extensions nicht in der geschriebenen Reihenfolge: Es sortiert sie nach ihren deklarierten Abhängigkeiten (eine Extension wird nach den Extensions geladen,
die sie benötigt oder verwendet, und vor denen, die sie erweitert), und die geschriebene Reihenfolge zählt nur dort, wo nichts deklariert ist. Auf einer
typischen Installation werden die meisten Extensions daher an einer anderen Position geladen als geschrieben; jede Karte sagt, wo
(**Geladen als Nummer n**), und der Hinweis über der Liste sagt, wie viele. Eine Extension zu verschieben, deren Platz die Abhängigkeiten
festlegen, ändert die Datei, aber nicht das, was geladen wird; die Prüfung weist darauf hin.

## 5. Häufige Probleme

| Sie sehen | Tun Sie Folgendes |
|---|---|
| *Benötigt X, das nicht aktiv ist* | Aktivieren Sie X oder deaktivieren Sie die Extension, die es benötigt |
| *In ActiveExtensions aufgeführt, aber ihr Verzeichnis fehlt* | Installieren Sie die Extension erneut oder verwenden Sie **Aus der Liste entfernen** |
| *Ändert F, das X vollständig mitliefert und früher lädt* (ein Hinweis in Details) | Bei Listeneinstellungen meist harmlos; wenn ein einzelner Wert dieser Extension gewinnen muss, verschieben Sie sie über X |
| Die Seite meldet, dass die Liste anderswo geändert wurde | Ihr Plan wurde nicht geschrieben; nehmen Sie die Änderung an der jetzt angezeigten Liste erneut vor |
| Eine Änderung wird auf dem Velocity-Port nicht angezeigt | `./console exp:velocity restart` |
| *Probleme bei der Autoload-Erzeugung festgestellt* | Zwei Extensions deklarieren dieselbe Klasse; entfernen oder benennen Sie eine um |

## 6. Wie viele Extensions: das Dashboard, Info > Über und das Update-Dashboard

Alle drei beziehen ihre Zahlen aus einer einzigen Zählung, `eZExtension::extensionCounts()`, und sagen, was sie zählen:

| Seite | Zeigt |
|---|---|
| Dashboard (admin4) und Info > Über | „63 Extensions in diesem Siteaccess aktiv, 85 im Dateisystem“ |
| Das Update-Dashboard und seine Seite der installierten Pakete | „85 Extensions im Dateisystem, 64 in irgendeinem Siteaccess aktiv“ |

Die Arten sind `on_disk` (jedes Verzeichnis jeder Extension-Wurzel, `ExtensionDirectory` und
`AdditionalExtensionDirectories`, jeder Name einmal), `global` (`ActiveExtensions`), `here` (aktiv im aktuellen
Siteaccess, mit dessen `ActiveAccessExtensions`, in Ladereihenfolge), `anywhere` (aktiv in mindestens einem Siteaccess aus
`AvailableSiteAccessList`), `inactive` und `missing` (in den Einstellungen genannt, aber nicht im Dateisystem). Eine Extension, die nur
der Administrations-Siteaccess einschaltet (zum Beispiel expai), zählt also auf „Über“ dort unter „in diesem Siteaccess aktiv“ und auf
dem Update-Dashboard unter „in irgendeinem Siteaccess aktiv“. Eine Extension, die in den Einstellungen genannt wird, aber nicht im Dateisystem liegt, wird
nur unter „Brauchen Aufmerksamkeit“ aufgeführt.

Info > Über listet die `here`-Extensions in der Ladereihenfolge auf, die der Kernel verwendet. Templates lesen die Zähler mit
`fetch( 'setup', 'extension_counts', hash( 'all_siteaccesses', true() ) )`; ohne `all_siteaccesses` werden die Arten
weggelassen, die die Einstellungen jedes Siteaccess lesen (`anywhere`, `inactive`), was den Aufruf bei etwa einer halben
Millisekunde hält.

„Geändert“ auf einer Karte ist die neueste Dateizeit der Extension; sie wird eine Stunde lang in
`<cache>/exp/extension-file-times.json` gehalten und erneut ermittelt, wenn der Cache der aktiven Extensions geleert wird
(`ezcache --clear-tag=ini`, `--clear-all`, `exp:velocity deploy`, eine Änderung auf dieser Seite); die eigene
`extension.xml`, `ezinfo.php` und `composer.json` der Extension werden jedes Mal angesehen.

## Für Entwickler

- View: `kernel/private/classes/views/setup/extensions.php` (schlank); Template `design/admin{,4}/templates/setup/extensions.tpl`,
  gestaltet in `stylesheets/modules/setup.css`.
- `expExtensionCatalogue` (`kernel/classes/expextensioncatalogue.php`) sammelt die Fakten; `expExtensionChangePlan`
  (`kernel/classes/expextensionchangeplan.php`) verschiebt, aktiviert, deaktiviert, vergleicht und findet Probleme und Risiken; beide
  sind dort, wo es darauf ankommt, frei von Seiteneffekten und werden ohne Datenbank in `tests/tests/kernel/classes/setup/ExtensionChangePlanTest.php` getestet.
- `ezpActiveExtensions::replaceInText()` bearbeitet die Datei als Text; `write()` bewahrt die Kopie auf, prüft das Ergebnis und erfasst
  die Änderung im Audit (`system.extension.change`).
- Das Formular sendet `ExtensionPlanForm`, `ExtensionBase` (einen Fingerabdruck der Liste, von der es ausging), `ExtensionPlan[]`,
  `ExtensionAction` (`up|down|top|bottom|activate|deactivate:<name>`), `ExtensionReviewButton`, `ExtensionApplyButton`,
  `ExtensionAcknowledgeRisks` und `ExtensionDiscardButton`. Die Formularsendungen der Seite vor der Neugestaltung funktionieren weiterhin:
  `ActivateExtensionsButton` mit `ActiveExtensionList[]` (und `ShownExtensionList[]`) schreibt wie bisher sofort, und das
  JSON-Umordnen `ReorderExtensions` mit `ExtensionOrder[]` ändert nur die Reihenfolge.
- Siehe auch: [Ladereihenfolge und sicheres Speichern](../../eng-us/features/6.0/extension-loading-order.md),
  [die Extension-Liste und Downloads](../../eng-us/features/6.0/extension-list-and-downloads.md).
