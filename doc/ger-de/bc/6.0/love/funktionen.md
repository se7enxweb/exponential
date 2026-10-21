# Funktionen

Jede LoveStack-Funktion, die in Exponential 6.0.15 übernommen wurde, mit dem, was eine **aktualisierte** Installation
erhält, und dem, was eine **neue** Installation erhält. Alle sind in 6.0.15 verfügbar. Der
[Überblick](../lovestack.md#funktion-für-funktion) enthält die Details zu jeder: für wen sie gedacht ist, ihre
Einstellungen, wie Sie sie einschalten, ihren Test und den LoveStack-Commit.

„Ein“ bedeutet ohne jede Einstellung aktiv; „aus“ bedeutet verfügbar, aber inaktiv, bis Sie sie einschalten.

## Sicherheit und Sitzungen

| ID | Funktion | Für | Upgrade | Neue Installation |
|---|---|---|---|---|
| [L01](../lovestack.md#l01--knotendaten-aufrufe-können-eine-richtlinie-verlangen) | Die Knotendaten-Aufrufe `ezjscnode::load`, `::subtree`, `::updatepriority` können die Richtlinie *ezjscore / call* mit der Funktion `ezjscnode` verlangen (`RequirePolicy`) | Administratoren | aus (`RequirePolicy=disabled`) | ein; die Rolle Editor erhält die Richtlinie |
| [L02](../lovestack.md#l02--die-registrierung-kann-eine-richtlinie-verlangen) | Die Registrierung kann die Richtlinie *User / Register* verlangen (`PolicyOmitListExclude[]`); Anmeldeseiten zeigen den Link zum Registrieren nur denen, die sich registrieren dürfen | Administratoren | aus (Registrierung offen, wie bisher) | ein (`PolicyOmitListExclude[]=user/register`) |
| [L03](../lovestack.md#l03--das-abmelden-beendet-die-sitzung-und-ihr-cookie) | Das Abmelden beendet die Sitzung und lässt ihr Cookie ablaufen | alle | ein | ein |
| [L04](../lovestack.md#l04--ein-konto-pro-anmeldename) | Ein Konto pro Anmeldename: `exp:user-login-check` meldet Duplikate, `--fix-index` fügt den eindeutigen Index hinzu | Administratoren | Bericht verfügbar; Index auf Wunsch hinzugefügt | Index im Schema |
| [L05](../lovestack.md#l05--hochgeladene-bilder-werden-anhand-ihres-inhalts-geprüft) | Hochgeladene Bilder werden anhand ihres Inhalts gegen `AllowedMIMETypes[]` geprüft (SVG nicht aufgeführt) | alle | ein | ein |
| [L06](../lovestack.md#l06--vorschaubilder-von-paketen-werden-ebenso-geprüft) | Dieselbe Prüfung für Vorschaubilder von Paketen und Bilder von Style-Paketen | Administratoren | ein | ein |
| [SameSite](../lovestack.md#die-sitzungseinstellung-samesite-von-lovestack-wird-ebenfalls-gelesen) | `[Session] SameSite` von LoveStack wird gelesen, wenn `CookieSameSite` nicht gesetzt ist | Administratoren | ein | ein |

## Diagnose und Protokollierung

| ID | Funktion | Für | Upgrade | Neue Installation |
|---|---|---|---|---|
| [L07](../lovestack.md#l07--php-deprecations-erhalten-ein-eigenes-log) | PHP-Deprecations erhalten eine eigene Debug-Stufe und `var/log/deprecated.log`, gezählt, nicht im Speicher gehalten | Administratoren, Entwickler | ein (`DeprecatedLog=enabled`) | ein |
| [L08](../lovestack.md#l08--größe-der-log-dateien-in-siteini) | Größe und Rotation der Log-Dateien in `site.ini [FileSettings]` | Administratoren | 2 MB, 3 Dateien | 2 MB, 3 Dateien |
| [L09](../lovestack.md#l09--eine-warnung-für-einen-operator-parameter-ohne-standardwert) | Eine Warnung nennt den Template-Operator und den Parameter ohne Standardwert | Entwickler | ein | ein |
| [L10](../lovestack.md#l10--bearbeitungslinks-in-der-liste-der-verwendeten-templates) | Das Format der Bearbeitungslinks in der Liste der verwendeten Templates der Debug-Ausgabe (`UsedTemplateEditUrl`) | Entwickler | der übliche Link | der übliche Link |
| [L11](../lovestack.md#l11--datenbankfehler-tragen-die-fehlernummer-des-treibers) | Datenbank-Ausnahmen tragen die Fehlernummer des Treibers | Entwickler | ein | ein |
| [L12](../lovestack.md#l12--eine-warnung-wenn-eine-änderung-jeden-view-cache-leert) | Eine Warnung, wenn der Cache-Schwellenwert ein Leeren in „alles leeren“ verwandelt | Administratoren | ein | ein |

## Inhalte, Datentypen und die Administration

| ID | Funktion | Für | Upgrade | Neue Installation |
|---|---|---|---|---|
| [L13](../lovestack.md#l13--audit-einträge-für-änderungen-an-orten) | Audit-Ereignisse für das Hinzufügen und Entfernen von Orten und das Ändern des Hauptorts | Administratoren | Entfernen ein (wie bisher); Hinzufügen und Ändern des Hauptorts aus | dasselbe |
| [L14](../lovestack.md#l14--site-daten-in-templates-und-in-der-administration) | Site-Daten in Templates für aufgeführte Namen; Setup > Site-Daten | Entwickler, Administratoren | nichts aufgeführt; Admin-Liste vorhanden | dasselbe |
| [L15](../lovestack.md#l15--eine-schaltfläche-kopieren-in-der-vollständigen-admin-ansicht) | Eine Schaltfläche Kopieren in der vollständigen Admin-Ansicht | Redakteure | ein | ein |
| [L16](../lovestack.md#l16--pflicht-alternativtext-für-bildfelder) | Alternativtext kann pro Bildattribut verlangt werden | Redakteure, Administratoren | aus für jede Klasse | aus für jede Klasse |
| [L17](../lovestack.md#l17--den-gespeicherten-wert-von-textzeilenfeldern-trimmen) | Ein Textzeilenattribut kann seinen gespeicherten Wert trimmen | Redakteure, Administratoren | aus für jede Klasse | aus für jede Klasse |
| [L18](../lovestack.md#l18--von-einer-php-klasse-erzeugte-namen) | Eine PHP-Klasse kann Objektnamen und Namen von URL-Aliasen erzeugen (`{MyResolver}`) | Entwickler | aus, bis eine Klasse aufgeführt ist | dasselbe |
| [L19](../lovestack.md#l19--standard-des-klassenfilters-für-fetches) | Standard für den Typ des Klassenfilters bei Fetches (`DefaultClassFilterType`) | Entwickler | leer (wie bisher) | leer |
| [L20](../lovestack.md#l20--das-fenster-orte-lässt-einen-versteckten-ort-versteckt) | Das Fenster Orte erzwingt nicht mehr „Sichtbar“ | Redakteure | ein | ein |
| [L21](../lovestack.md#l21--auswahl--und-matrixfelder-ohne-überzählige-zeilenumbrüche) | Werte von Auswahl und Matrix ohne überzählige Zeilenumbrüche | Designer | ein | ein |

## Rich-Text-Editor

| ID | Funktion | Für | Upgrade | Neue Installation |
|---|---|---|---|---|
| [L22](../lovestack.md#l22--versteckte-custom-tags) | Custom Tags, die existieren, aber für neue Tags nicht angeboten werden (`IsHidden[]`) | Administratoren, Redakteure | keiner versteckt | keiner versteckt |
| [L23](../lovestack.md#l23--bedienelemente-für-custom-tags-im-rich-text-editor) | Bedienelemente an einem fokussierten Custom Tag (verschieben, bearbeiten, entfernen, Tasten), Beschriftungen und Vorschauen (`CustomTagControls`) | Redakteure | aus | ein |
| [L24](../lovestack.md#l24--der-dialog-subsup-schließt-bei-ok) | Der Dialog des Custom Tags sub/sup schließt bei OK | Redakteure | ein (war bereits so) | ein |

## APIs und Einstellungen für Entwickler

| ID | Funktion | Für | Upgrade | Neue Installation |
|---|---|---|---|---|
| [L25](../lovestack.md#l25--ezuribuild-baut-eine-site-url-in-einem-aufruf) | `eZURI::build()` | Entwickler | verfügbar | verfügbar |
| [L26](../lovestack.md#l26--eine-nachricht-mit-einem-anderen-mail-transport) | `eZMailTransport::send( $mail, $transportType )` | Entwickler | verfügbar | verfügbar |
| [L27](../lovestack.md#l27--die-template-operatoren-rawurlencode-und-rawurldecode) | Die Template-Operatoren `rawurlencode` und `rawurldecode` | Entwickler | verfügbar | verfügbar |
| [L28](../lovestack.md#l28--include--in-einstellungsdateien) | `[Include <pattern>]` in Einstellungsdateien | Administratoren, Entwickler | nur wo eine Datei eine solche Zeile hat | dasselbe |
| [L29](../lovestack.md#l29--antwort-header-für-fehlerseiten) | Antwort-Header pro HTTP-Fehlerseite (`HeaderList[]`), auch aus dem View-Cache | Administratoren | keine gesetzt | keine gesetzt |
| [L30](../lovestack.md#l30--die-reihenfolge-der-suchergebnisse-in-den-dialogen-der-redakteure) | Die Suche von ezjscore sortiert nach `ezfind.ini [SearchSettings] SortBy[]` | Administratoren | dieselbe Reihenfolge wie bisher | dasselbe |
| [L31](../lovestack.md#l31--templates-können-layout-ansichten-unterscheiden) | Der Design-Schlüssel `layout` für Knotenansichten; `setKeyValue()` | Designer, Entwickler | ein | ein |

## URLs und Suche

| ID | Funktion | Für | Upgrade | Neue Installation |
|---|---|---|---|---|
| [L32](../lovestack.md#l32--anfrageadressen-werden-einmal-dekodiert) | Anfrageadressen werden einmal dekodiert (`DecodeOnce`) | Administratoren | aus | aus |
| [L33](../lovestack.md#l33--das-doppelte-anführungszeichen-in-iri-url-aliasen) | Das doppelte Anführungszeichen wird aus IRI-URL-Aliasen entfernt (`TrailingCleanup`) | Administratoren | aus | aus |
| [L34](../lovestack.md#l34--sehr-große-zahlen-halten-den-suchindex-nicht-mehr-an) | Die eingebaute Suche begrenzt Ganzzahlen auf den Wertebereich der Spalte | Administratoren | ein | ein |
| [L35](../lovestack.md#l35--relationen-verknüpfter-objekte-bleiben-aus-dem-suchindex-heraus) | Relationsfelder verknüpfter Objekte bleiben aus dem Suchindex heraus | Administratoren | ein (das Verhalten von Exponential seit 2018) | ein |
| [L36](../lovestack.md#l36--der-cronjob-hide-und-der-suchindex) | Der Cronjob hide aktualisiert den Suchindex; das Ereignis `content/cache` trägt Objekt-IDs | Administratoren, Entwickler | ein | ein |

## PHP-8-Prüfung

Die Korrekturen von LoveStack für PHP 7.2 bis 8.3 wurden einzeln unter PHP 8.5 und 8.1 nachgestellt, jede als
Regressionstest. Die meisten waren in Exponential bereits behandelt; die noch fehlschlagenden sind für jede Website
behoben. Stelle für Stelle: [php8-pruefung.md](php8-pruefung.md).

## Bereits in Exponential, und nicht übernommen

Siehe den Überblick: [Bereits in Exponential](../lovestack.md#bereits-in-exponential) und
[Nicht übernommen, und warum](../lovestack.md#nicht-übernommen-und-warum).
