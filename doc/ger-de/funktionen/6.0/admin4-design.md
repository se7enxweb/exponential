# Das Design admin4

Diese Seite richtet sich an Administratoren, die das Aussehen der Administrationsoberfläche wählen, und an Entwickler,
die sie gestalten. admin4 ist ein vollständiges Administrationsdesign im Stil des Einrichtungsassistenten, mit einem
hellen und einem dunklen Modus. Es enthält jede Datei, die der Admin-Siteaccess aus `admin3`, `admin2` (nicht mehr Teil
des Baums) und `admin` ausgeliefert hat (463 Dateien bei seiner Erstellung, 491 zum Zeitpunkt der Abfassung; für jeden
Pfad die Datei, die sich in der Suchreihenfolge durchgesetzt hat), sodass es ohne jedes dieser Designs funktioniert:
Jedes Template, das eine Ansicht des Kernels abruft oder ein admin4-Template einbindet, liegt in `design/admin4` oder
`design/standard` (`tests/tests/design/AdminSelfContainedTest.php` schlägt für ein Template fehl, das nur
`design/admin` oder `design/admin3` hat). Hinzugefügt am 02.10.2026 und im Lauf des Tages verfeinert.

## Darauf umstellen

1. Setzen Sie in der `site.ini.append.php` des Admin-Siteaccess:

   ```ini
   [DesignSettings]
   SiteDesign=admin4
   ```

2. Leeren Sie die Caches:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

3. Laden Sie die Administrationsoberfläche neu. Die Kopfzeile zeigt nach dem Suchfeld einen Modusschalter (hell,
   dunkel, System).

4. Für die Symbole der Klassen, Dateitypen und Klassengruppen ist nichts zu tun: `settings/icon.ini` gibt admin4 und
   admin4l das Theme `bootstrap-icons` (`[IconSettings] DesignTheme[admin4]`, `DesignTheme[admin4l]`), unabhängig
   davon, welches `Theme` der Siteaccess setzt; dieses Theme wird direkt danach durchsucht. Um die Crystal-PNGs zu
   behalten, setzen Sie `DesignTheme[admin4]=` (leer) in der `icon.ini.append.php` des Siteaccess (siehe
   [Symbole](#symbole)).

Die Designliste des Siteaccess nennt weiterhin `admin3`, `admin2` und `admin`, für die gleichnamigen Ordner der
Extensions (die drei Templates, die `bccie` und `enhancedezbinaryfile` in ihrem eigenen admin2-Ordner überschreiben,
bleiben bei der Extension). Der Redakteurs-Siteaccess (`SiteDesign=editor`) fällt zuerst auf `admin4l` zurück, dann
auf `admin4`, `admin3`, `admin2`, `admin` (`kernel/setup/steps/ezstep_create_sites.php`).

## Was Sie erhalten

- **Heller und dunkler Modus.** Hell (Standard): blasse Seite, mattierte Kopfzeile, Karten mit Licht- und
  Schattenkanten. Dunkel: schiefergraue Seite, weiße Karten. Der Schalter in der Kopfzeile wechselt hell -> dunkel ->
  System; „System“ folgt der Einstellung des Betriebssystems (`prefers-color-scheme`), auch wenn sie sich ändert,
  während die Seite geöffnet ist. Das Symbol des Schalters (Sonne, Mond, Halbkreis) und sein zugänglicher Name nennen
  den aktiven Modus („Farbmodus: hell/dunkel/System“); sein Titel nennt den nächsten Schritt („Zum dunklen Modus
  wechseln“), und ein Statusbereich kündigt einen Tastendruck an. Die Wahl ist die eZPreference `admin_color_mode` des
  angemeldeten Benutzers und folgt ihm daher von Browser zu Browser (siehe „Gemerkte Einstellungen“ unten); sie wird
  angewendet, bevor die Seite gezeichnet wird, und ohne Wahl ist die Administrationsoberfläche wie bisher hell.
  `html[data-a4-theme]` bleibt für die Stylesheets hell oder dunkel, `html[data-a4-theme-choice]` trägt den gewählten
  Modus. Eine Kopie des Schalters ohne `data-label-system` (die eigene Kopfzeile einer Extension) wechselt weiterhin
  nur zwischen hell und dunkel. Der Modus braucht das Head-Skript: Ohne JavaScript ist die Administrationsoberfläche
  hell, ihr dokumentierter Standard (ein dunkler Fallback für `prefers-color-scheme` würde den gesamten dunklen
  Token-Block wiederholen, und ohne JavaScript funktioniert die Administrationsoberfläche ohnehin nicht).
- **Drucken.** Eine gedruckte Seite besteht nur aus dem Inhalt in voller Breite (keine Kopfzeile, Menüs,
  Seitenspalten, Bedienleisten, Toasts oder Fußzeile, keine Schatten) und immer in den hellen Farben: Das Head-Skript
  schaltet vor dem Drucken auf hell um und danach zurück.
- **Typografie.** Eine Schrift für jeden Text: `--a4-font` (`components.css`) ist `"Helvetica Neue", "Noto Sans",
  "Liberation Sans", Arial, sans-serif`, gefolgt von den Farb-Emoji-Schriften von macOS, Windows und Linux (`"Apple Color
  Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"`). Nur Systemschriften, nichts wird heruntergeladen.
  Code und wörtlicher Text verwenden `--a4-mono`. Fließtext, Überschriften, Formularfelder, Schaltflächen, die
  Seitennavigation und der Text des Rich-Text-Editors verwenden `var(--a4-font)`; kein admin4-Stylesheet setzt eine
  eigene Schriftliste (abgesehen von Symbolschriften; `core.css` gibt Arial als Fallback von `var()` für Layouts ohne
  `components.css` an), sodass ein Theme die Schrift ändert, indem es allein das Token neu definiert. Test:
  `AdminFontStackTest`.
- **Eine Dichte.** Listen, Tabellen, Karten und Bedienelemente haben einen einzigen Abstand, abgestimmt auf 1440 bis
  1920 px: eine Höhe für Bedienelemente (32 px), Listenzellen wie `.a4-table`-Zellen (8/12 px), ein Abstand von 24 px
  zwischen den Karten des Rahmens. Die kompakte Ansicht vom Oktober 2026 und ihr Schalter in der Kopfzeile sind
  entfallen; eine gespeicherte Wahl bewirkt nichts.
- **Gemerkte Einstellungen.** Der Farbmodus, die Seitenspalten bei einem breiten Fenster, die geschlossenen Gruppen
  der linken Menüs und die Höhe des Tab-Bereichs der Knotenansicht sind eZPreferences des angemeldeten Benutzers
  (`admin_color_mode`, `admin_left_sidebar`, `admin_right_sidebar`, `admin_navgroups`,
  `admin_node_preview_height`; das Gerät der Versionsvorschau: `admin_preview_device`, `admin_preview_landscape`),
  genau wie die Spaltenbreite. Ein Helfer,
  `window.a4Pref` (das erste Skript von `page_head_style.tpl`), liest sie aus `<html>`
  (`design:parts/a4_preference_attributes.tpl`, `data-a4-pref-*`) und speichert sie über
  `user/preferences/set_and_exit`. Eine anonyme Seite behält sie nur im Browser (`localStorage`); eine früher im
  Browser getroffene Wahl wird einmalig übernommen. Eine Einstellung, die nicht gespeichert werden kann, wird
  stattdessen im Browser behalten, und `set( name, '' )` vergisst eine Wahl (die Einstellung wird leer gesetzt, die
  Kopie im Browser entfernt). `set()` sendet nichts, wenn der Wert bereits der auf `<html>` ist, sodass ein Seitenaufruf
  keine Einstellung zurückschreibt. `set( name, value,
  legacyKey, share )`: Mit `share` wird ein gespeicherter Wert auch in `localStorage` geschrieben, sodass andere
  geöffnete Tabs folgen (der Farbmodus tut das). Die Breite der linken Spalte (`admin_left_menu_size`) und der
  Tab-Schalter der Knotenansicht (`admin_navigation_content`) werden ebenfalls über `window.a4Pref` gespeichert; eine
  Breite, die nicht gespeichert werden konnte, wird aus dem Browser übernommen (`exp-a4-admin_left_menu_size`),
  solange weder eine Einstellung noch eine voreingestellte Größe gesetzt ist. `tests/tests/design/AdminPreferencesTest.php` prüft das.
  Die Seitenspalten werden schon beim ersten Zeichnen mit diesen Einstellungen dargestellt: Bis `ezadmin_menubar.js`
  ihren Zustand angewendet hat, zeigt `html.a4-preload` (beide Seitenlayouts) eine Spalte neben dem Inhalt, sofern sie
  nicht als geschlossen gespeichert ist, mit dem Platz, den eine offene Spalte einnimmt, und führt keine Übergänge
  aus; das Skript entfernt die Klasse zwei Frames später (`tests/tests/design/AdminFirstPaintColumnsTest.php`).
- **Kopfzeile und oberes Menü.** Ab 1280 px eine Zeile von 56 px (Logo, Menü, Suche, Site, hell/dunkel,
  Benutzermenü, Abmelden), ab 576 px zwei Zeilen von 48 + 40 px, auf einem Telefon eine Zeile von 56 px mit dem
  Hamburger-Menü (Logo, Site, hell/dunkel, Menüschaltfläche). Das Menü bleibt einzeilig; Tabs, die nicht passen,
  kommen in eine Liste „Mehr“. Auf einem Telefon öffnet sich das Menü als Bereich unter der Kopfzeile, der immer auf
  den sichtbaren Bildschirm passt (höchstens `100dvh` abzüglich der Kopfzeile, mit Bildlauf im Inneren): zuerst die
  Suche, die Tabs als Raster aus 44-px-Kacheln (drei pro Zeile bei 390 px, zwei bei 320 px), die Aktionen des Benutzers
  (Name, Informationen ändern, Passwort ändern, Meine E-Mail-Einstellungen, Abmelden) als eigene Zeile und am Ende eine
  Schaltfläche Schließen; solange es geöffnet ist, bleibt die Tab-Taste in der Kopfzeile, Escape, Schließen und ein
  Tippen außerhalb schließen es (`AdminPhoneMenuTest`). Der Block des Tab-Menüs von admin4l endet mit derselben
  Schaltfläche Schließen. „Website
  öffnen“ ist das erste der Werkzeuge, eine runde Symbolschaltfläche wie die anderen; Abmelden ist eine
  Symbolschaltfläche („Abmelden (admin)“); das Benutzermenü enthält den Namen des Benutzers und Links, und der Name
  führt zum eigenen Objekt des Benutzers, wenn dieser es lesen darf (nicht auf Bearbeitungs- und Auswahlseiten). Die
  Tab-Taste erreicht die Werkzeuge in der Reihenfolge, in der sie
  gezeichnet sind. „/“ springt zur Suche. Die Seite beginnt immer unterhalb der Kopfzeile (`AdminHeaderTest`).
- **Logo.** Ein einziges Template zeichnet es, `parts/a4_logo.tpl`, eingebunden in die Kopfzeile (`page_topmenu.tpl`,
  der Block `admin_logo` von admin4l) und auf der Anmeldeseite. Es ist Inline-SVG: Buchstaben und Umrisse in
  `currentColor` (das `--a4-ink` des Links, also dunkel auf hellen Seiten und hell auf dunklen), das orangefarbene
  Quadrat in der Markenfarbe `#f15a28`, das innere Quadrat des hinteren Zeichens in `--a4-surface` (weiß auf hellen
  Seiten wie in der Vorlage; auf dunklen Seiten die Farbe der Seite statt eines weißen Blocks). Bei erzwungenen Farben
  CanvasText und Canvas. 24 px hoch in der Kopfzeile (152 px breit,
  im 32-px-Rahmen des alten Bildes, sodass die Kopfzeile ihre 56/88 px behält), 28 px auf der Anmeldeseite; unter
  576 px zeigt die Kopfzeile nur das Zeichen (in einem 36-px-Link, wie die Werkzeuge). Die Zeichnungen sind
  `aria-hidden`; die umgebenden Links tragen den Namen. Dieselbe
  Zeichnung als Dateien: `images/admin4/exponential-logo.svg` (Schriftzug und Zeichen, viewBox auf die Zeichnung
  zugeschnitten) und `images/admin4/exponential-mark.svg` (das Zeichen, quadratisch). `AdminLogoTest` hält das Partial
  und die Dateien gleich und prüft, dass sie kein Skript, keinen Stil und keinen externen Verweis enthalten. Der
  Schriftzug ist so hoch wie das Zeichen (das E teilt Oberkante und Grundlinie der Quadrate). Die Raster-Logos
  (`logo-light.png`, `Exponential-logo-title.png` des Setups, `Exponential-logo.gif` des Standarddesigns, das weiße
  Logo von AdminUI) werden aus dieser Zeichnung in doppelter Größe ihrer Darstellung gerendert.
- **Anmeldeseite.** Ein ruhiges Formular, schnell auf Telefonen: Beschriftungen über 46 bis 48 px hohen Feldern, 16 px
  Schrift, damit mobiles Safari nicht zoomt, Hinweise für das automatische Ausfüllen, Enter springt vom Benutzernamen
  zum Passwort und meldet an, eine Schaltfläche zum Ein- und Ausblenden, eine Schaltfläche über die volle Breite, die
  nicht doppelt abgesendet werden kann, sowie Links für vergessene Passwörter und die Registrierung, wenn diese Module
  vorhanden sind. Der Kernel liest dieselben Felder wie zuvor.
- **Dashboard.** Eine Begrüßung mit Schnellaktionen, Kennzahlen, den letzten 14 Tagen der Veröffentlichungen und dem
  System auf einen Blick, rund um jeden Block aus `dashboard.ini`. Es verweist auf die beiden Aktualisierungen, die
  eine Installation sicher halten: das CMS mit dem Git-Manager und die Composer-Bibliotheken auf dem Updates-Dashboard,
  verlinkt nur dort, wo der Benutzer sie öffnen darf.
- **Seitenkopf und Brotkrümelnavigation.** Jeder Seitentitel hat eine einzige Größe (22px; 16px in Fenstern bis
  600px): die Titelleiste des Kernels, die Knotenansicht und das Dashboard. Die Brotkrümelnavigation darüber ist eine
  Liste in einer Navigations-Landmark: Trennzeichen vom Stylesheet gezeichnet, die aktuelle Seite für Screenreader
  markiert, lange Namen enden mit Auslassungspunkten und zeigen den vollen Namen als Tooltip (nicht mehr auf
  18 Zeichen gekürzt). In Fenstern bis 600px (und bei 400 % Zoom) werden die mittleren Schritte nur optisch zu „...“
  zusammengefaltet: Screenreader lesen weiterhin den ganzen Pfad vor, und ein Schritt, der den Tastaturfokus erhält,
  wird wieder angezeigt.
  Jede Kennzahl hat eine Symbolkachel; eine Kennzahl, die irgendwohin führt, zeigt einen Pfeil in ihrer Ecke. Die
  Wochenzahl sagt, wie ihre vollen Tage im Vergleich zu den 6 Tagen davor stehen, ohne den heutigen Tag (eine Zahl, ein
  Pfeil und Worte, nie eine Farbe für „gut“ oder „schlecht“),
  gezählt aus denselben 14 Tagen wie das Diagramm, sodass sie keine eigene Abfrage braucht. Die Zahlen des Diagramms
  klappen darunter als Tabelle auf („Zahlen anzeigen“). Zustände in Tabellen verwenden das vorhandene
  `.a4-badge.is-ok|is-warn|is-bad|is-info` (Tönung und Textfarbe des Zustands); es gibt keine zweite
  „Pill“-Komponente.
- **Knotenansicht.** Eine Kopfzeile mit dem Klassensymbol, dem Titel, Plaketten (Klasse, versteckt, sekundärer Ort) und
  den Aktionen (Sprache, Bearbeiten, Verschieben, Entfernen, Auf der Website ansehen, Vorschau, Versionen verwalten).
  Eine Metazeile mit Änderungsdatum und Bearbeiter, Veröffentlichung, Version, Sektion, Knoten-, Objekt- und Remote-IDs
  (zum Kopieren anklicken) und den Übersetzungen (zum Wechseln anklicken). Die Tabs bilden eine segmentierte Reihe.
  Tabs und Unterelemente beginnen etwa 300 px unter der Oberkante der Karte statt 500. Auf Telefonen umbrechen die
  Aktionen innerhalb der Karte auf zwei kurze Zeilen.
- **Versionsvorschau.** Die Vorschau einer Version (content/versionview: „Vorschau“ aus dem Bearbeitungsformular, der
  Knotenansicht und dem Versionsverlauf) zeigt die eigene Darstellung der Site für den gewählten Siteaccess in der
  Breite eines Geräts: eine segmentierte Leiste iPhone (390 × 844 px) | iPad (820 × 1180) | Desktop (1280 × 800) |
  Großer Desktop (1920 × 1080) und ein Querformat-Schalter für Telefon und Tablet. Die Seite wird in der Größe des
  Geräts gezeichnet und verkleinert, wenn die Spalte schmaler ist; die Zeile neben den Schaltern nennt Größe und
  Maßstab („390 × 844 px, angezeigt mit 100 %“). Die Wahl gehört dem angemeldeten Benutzer, als eZPreferences
  `admin_preview_device` und `admin_preview_landscape` (über `window.a4Pref`, sodass sie dem Benutzer in einen anderen
  Browser folgt; auf einer anonymen Seite der Browser); ohne gespeicherte Wahl ist es Desktop. In einer schmalen
  Spalte zeigen die Schalter nur ihre Symbole. Ohne JavaScript bleiben die Schalter verborgen und die Vorschau füllt
  wie bisher die Spalte. Die Pfeiltasten wechseln zwischen den Geräten.
- **Höhe des Tab-Bereichs der Knotenansicht.** Der Bereich der Tabs (Ansicht, Details, ...) behält die Höhe, die der
  Benutzer ihm gibt, eine Höhe für alle Knoten: Der Griff an seiner unteren rechten Kante wird mit der Maus, einem
  Finger oder einem Stift gezogen oder mit der Tastatur bewegt (er ist ein fokussierbarer Trenner: Pfeil nach oben und
  unten 16 px, Umschalt+Pfeil, Bild auf und Bild ab 64 px, Pos1 und Ende die kleinste und größte Höhe, dieselben
  Schritte wie bei den Griffen der linken Spalte und des Debug-Bereichs). Die Höhe wird zwischen 10em und 60em
  gehalten (die Hälfte und das Dreifache
  des Standards von 20em) und höchstens bei 85 % des Fensters, sodass eine auf einem großen Bildschirm gespeicherte
  Höhe auf einen kleinen passt; ein Telefon (unter 576 px) zeigt immer den Standard, und der Bereich mit
  ausgeschalteten Tabs behält seine eigene Größe. Eine Schaltfläche neben dem Griff, „Standardgröße“, erscheint nur,
  solange die Höhe nicht der Standard ist; sie und ein Doppelklick auf den Griff wie bei den anderen Griffen kehren
  zum Standard zurück und vergessen die Höhe, statt den Standard zu speichern, sodass eine spätere Änderung des
  Standards den Benutzer erreicht. Die Höhe
  ist die eZPreference `admin_node_preview_height` (Pixel), einmal am Ende eines Ziehens oder 400 ms nach dem letzten
  Tastendruck gespeichert und nur, wenn sie sich geändert hat; sie wird außerhalb jedes cache-block auf `<html>`
  geschrieben (die Knotenansicht selbst wird im Ansichts-Cache gehalten und trägt keinen Wert des Benutzers) und
  angewendet, bevor die Seite gezeichnet wird. Eine anonyme Seite und eine Einstellung, die nicht gespeichert werden
  kann (ein Netzwerkfehler, ein Fehlerstatus, die Anmeldeseite einer abgelaufenen Session), behalten sie im Browser;
  die Einstellung hat Vorrang vor der Kopie im Browser. Ohne JavaScript behält der Bereich den eigenen
  Größengriff des Browsers. Die Ansicht des obersten Knotens hat dieselbe Steuerung.
  `tests/tests/design/AdminNodeTabsHeightTest.php` prüft das.
- **Umgebungsplakette.** Standardmäßig aus. Ist `site.ini [AdminEnvironment] Level` gesetzt, nennt die Kopfzeile die
  Umgebung am Logo, in der Farbe der Stufe: Entwicklung grün, Test bernsteinfarben, Staging violett,
  Produktion rot, jeweils mit einer dunkleren oder helleren Kante für den dunklen Modus (`--a4-env-<level>`, `-ink`,
  `-line` in `admin4.css`; Text 4,5:1 auf der Füllung, die Kante 3:1 auf der Kopfzeile). Der Seitentitel beginnt mit
  dem Namen in Großbuchstaben,
  „[PRODUCTION] Dashboard - Site“, sodass sich Fenster und Tabs unterscheiden lassen; auch die Anmeldeseite zeigt die
  Plakette und das Präfix. Standardmäßig (`BadgeDisplay=hover`) nimmt die Plakette keinen Platz ein und zeigt nichts,
  bis ein Maus- oder Trackpad-Zeiger auf dem Logo ruht oder das Logo den Tastaturfokus hat: Dann erscheint der Name
  (und für angemeldete Benutzer das Release) als kleines Etikett neben dem Logo (576 bis 1279 px und auf der
  Anmeldeseite) oder darunter (ab 1280 px, wo das Hauptmenü auf das Logo folgt, und unter 576 px), außerhalb des
  Flusses, sodass sich nichts verschiebt. Nur für einen feinen Zeiger, der schweben
  kann (`@media (hover: hover) and (pointer: fine)`). Ein Touchscreen kann nicht schweben, und ein Tippen auf das Logo
  folgt dem Link, daher steht die Plakette dort wie bei `always` neben dem Logo (bis 767 px der Kurzcode). Der
  Zeiger kann auf das Etikett wandern, ohne es zu verlieren, das Etikett bleibt, bis Zeiger oder Fokus es verlassen,
  und Escape blendet es aus, bis Zeiger oder Fokus zurückkehren (WCAG 1.4.13: überfahrbar, dauerhaft, schließbar;
  `admin4.js`); es wird über 120 ms eingeblendet, bei reduzierter Bewegung sofort. Screenreader
  behalten es: Das Etikett wird beschnitten, nicht entfernt, und der Logo-Link nennt es in `aria-describedby`
  („Environment:
  Production“). Mit `BadgeDisplay=always` bleibt die Plakette neben dem Logo. Die Plakette ist Text, nicht nur Farbe:
  ab 768 px der Name (und für angemeldete Benutzer `Release` sowie mit
  `ShowVersion=enabled` die Version von Exponential), bis 767 px, Telefone eingeschlossen, der Kurzcode (`ShortName`,
  standardmäßig DEV,
  TEST, STAGE, PROD; das volle Logo weicht auch von 576 bis 767 px dem bloßen Zeichen), wobei Screenreader immer den
  Namen vorlesen; das Attribut title enthält den ganzen Text. Sie ist 24 px hoch in der 36-px-Zeile der Werkzeuge der
  Kopfzeile, sodass die Kopfzeile 56, 88 und 56 px behält; mit `always` gibt ihr bis 1279 px das Suchfeld Platz,
  ab 1280 px das Hauptmenü (seine letzten Tabs kommen in „Mehr“). Bei erzwungenen Farben wird sie mit ihrem Text in
  den Systemfarben gezeichnet. Dasselbe gilt in admin4l (der Logo-Block). Siehe [Einstellungen](#einstellungen).
  `tests/tests/design/AdminEnvironmentBadgeTest.php` prüft das.
- **Site-Menü.** Standardmäßig an. Der Globus in der Kopfzeile, der die Site öffnet, ist für angemeldete Benutzer eine
  Schaltfläche: Ihre Liste öffnet zuerst die Site, hervorgehoben, dann die anderen Sites der Installation, bei denen
  sich der angemeldete Benutzer anmelden darf (`user/login` mit seiner SiteAccess-Einschränkung; die
  Administrationsoberflächen nur mit `ListAdministrations=enabled` als eigene Gruppe), jeweils mit Namen (`SiteName`,
  sonst der Bezeichner), Bezeichner, Locale und Adresse; der
  verwendete Siteaccess ist markiert (`aria-current`). Die Adressen werden wie die des Globus selbst ermittelt
  (`ezpSiteAccessURL::root()`: MatchOrder, URIMatchType, HostMatchMapItems, SiteURL, Schema, Host und Port der
  Anfrage) und öffnen sich im selben Fenster. Ein Aufklappelement wie das Benutzermenü: Pfeil nach unten öffnet es und
  springt zum ersten Link, die Pfeiltasten, Pos1 und Ende bewegen, Escape schließt es und gibt den Fokus zurück, ein
  Klick außerhalb schließt es. Eine
  Schaltfläche an der Stelle des Links, sodass die Kopfzeile keinen zusätzlichen Platz braucht; auf einem Telefon
  öffnet sich die Liste unter der Kopfzeile. Ohne JavaScript, mit `Show=disabled` oder ohne einen Siteaccess außer der
  Site bleibt der Globus ein einfacher Link.
  Dasselbe gilt in admin4l (der Werkzeug-Block). Siehe [Einstellungen](#einstellungen).
  `tests/tests/design/AdminSiteAccessMenuTest.php`
  prüft das.
- **Bearbeitungsformular.** Die Schaltflächenleisten (Zur Veröffentlichung schicken, Entwurf speichern, Entwurf
  speichern und beenden, Entwurf verwerfen) bleiben sichtbar, während das Formular scrollt (CSS `position: sticky`,
  als Ersatz für das nicht mehr funktionierende YUI-Skript). Sie sind schmal (42 px auf einem Desktop) und passen auf
  einen Telefonbildschirm. Bearbeitungsseiten zeigen eine linke Spalte, nicht zwei. Pflichtfelder tragen ein rotes
  `*` (ein Hinweis über den Attributen erklärt seine Bedeutung; Screenreader hören „erforderlich“, und die
  Bedienelemente sind `aria-required`). Nach einem fehlgeschlagenen Speichern übernimmt eine Zusammenfassung oben den
  Fokus und verlinkt auf jedes Feld; jedes ungültige Feld zeigt seine Meldung unter seiner Beschriftung, trägt eine
  rote Kante, und seine Bedienelemente sind `aria-invalid` mit der Meldung als Beschreibung. Jedes Attribut ist eine
  benannte Gruppe (`role="group"`, ID `ezcca-attr-<attribute id>`). Die Templates der Datentypen sind unverändert: Die
  Markierungen setzt das Skript der Seite auf ihre Datenelemente (nicht auf Schaltflächen, Dateieingaben oder
  Kontrollkästchen zum Entfernen); ein Datentyp-Template nimmt ein Bedienelement mit der Klasse `a4-no-field-mark`
  aus. Mehrfachbearbeitung, Benutzerregistrierung und der Template-Look-Editor binden dasselbe Attribut-Template ein
  und verhalten sich gleich. Die Felder, Schaltflächen und Kontrollkästchen der Datentyp-Templates sehen aus wie die
  Komponenten von admin4 (`.a4-input`, `.a4-btn`,
  `.a4-check`), die Attributnamen tragen keinen abschließenden Doppelpunkt, und eine gruppierte Eingabe hat keinen
  eigenen grauen Kasten
  ([Gestaltungsregeln, „Bearbeitungsformulare“](../../../eng-us/bc/6.0/admin4-styling.md#edit-forms)).
- **Rahmen.** Die Ränder der Seitenspalten sind halbiert (ein Abstand von 16 px). Die Schaltflächen der Seitenleisten
  sitzen an den Fensterrändern auf 48 % der Fensterhöhe; sie sind nach ihrer Spalte benannt („Linke Spalte“, „Rechte
  Spalte“), geben mit `aria-expanded` an, ob sie angezeigt wird, und bei einem schmalen Fenster schließt Escape eine
  über der Seite geöffnete Spalte und gibt den Fokus an ihren Tab zurück. Unter 1024px bleiben beide Tabs am
  Fensterrand fixiert, solange ihre Spalte über der Seite geöffnet ist, in den 24 px, die ihre Karte dort frei lässt;
  auf einem Touchscreen sind sie 48 px hoch. Der Breitengriff der linken Spalte wird auf einem Telefon nicht angezeigt.
  Lange Namen in den Seitenspalten umbrechen, statt abgeschnitten zu werden.
  Der Block Benutzereinstellungen der rechten Spalte (Orte, Wiederbearbeiten) zeigt „an“ und „aus“ als Tabs, wie die
  Tabs der Knotenansicht: der aktuelle Zustand in der Akzenttönung mit einem Balken darunter (die gedrückte
  Schaltfläche eines POST-Formulars, `aria-pressed`), der andere zurückhaltend
  ([Gestaltungsregeln, „Legacy-Bedienelemente und die Seitenspalten“](../../../eng-us/bc/6.0/admin4-styling.md#legacy-controls-and-the-side-columns)).
- **Linkes Menü.** Die linke Spalte ist die Navigation „Seitenmenü“. Ein langes Menü wird in Gruppen angezeigt: Setup
  in Inhaltsmodell, Website und URLs, Design (solange sein Tab ausgelassen ist), Workflows, Benutzer und Nachrichten,
  Audit (ebenso), System, KI, Updates und zuletzt „Mehr“ für Links ohne Gruppe. Jede Gruppe hat einen kleinen
  Titel in Großbuchstaben mit einem Pfeil und lässt sich öffnen und schließen (die eZPreference `admin_navgroups`
  merkt sich die Wahl; die Gruppe der aktuellen Seite ist immer
  geöffnet). Der Link der aktuellen Seite trägt `aria-current="page"` und ist mit der Akzenttönung und einem Balken
  markiert. Ein Menü
  wird über menu.ini gruppiert (`Groups[]`, `GroupNames[<group>]`, `GroupLinks_<group>[]`); ohne `Groups[]` oder mit
  auf keine Werte zurückgesetztem `Groups[]` ist es eine einzige Liste, benannt nach ihrer Überschrift. Der Zugriff
  wird wie bisher je Link geprüft.
  Unter jedem linken Menü ist die Breite der Spalte (schmal, mittel, breit) eine Leiste aus drei runden
  Symbolschaltflächen
  (`.a4-tabs.is-segmented.is-icons`); die aktuelle Breite ist in der Akzentfarbe getönt und gedrückt
  (`aria-pressed="true"`).
- **Setup-Übersicht.** Der Tab Setup öffnet eine Übersicht aller Setup-Funktionen als Kacheln, in den Gruppen des
  linken Menüs (siehe [Setup-Übersicht](#setup-übersicht)).
- **Tabellen.** Die sortierte Spalte einer Listentabelle ist gut erkennbar (sanft orangefarbener Kopf).
- **Symbole.** Bootstrap Icons in der Textfarbe, sodass sie Links, Schaltflächen, Hover und dem dunklen Modus folgen
  (siehe unten).
- **Sprachen.** Ein Kurzcode statt einer Flagge (siehe [Sprachen](#sprachen)).

## Symbole

admin4 zeichnet seine Symbole mit [Bootstrap Icons](https://icons.getbootstrap.com/) 1.13.2 (MIT-Lizenz). Die
SVG-Dateien liegen unverändert in `share/icons/bootstrap-icons/svg/`, mit der Lizenz daneben; nichts wird von einem CDN
geladen.

- **In Templates:** `<span class="a4-icon a4-bi-pencil" aria-hidden="true"></span>`, die Komponente `.a4-icon`
  aus `components.css`. Das Symbol ist eine Maske in `currentColor`, 1em im Quadrat, oder 12, 16 bzw. 22 px mit
  `.is-small`, `.is-medium`, `.is-large`. Ein Bedienelement, das nur aus einem Symbol besteht, ist ein `<button>` oder
  Link mit `.a4-icon-btn` und seinem Namen in `title` und `aria-label`; ein Symbol, das innerhalb eines Links für ein
  Wort steht, trägt `role="img"` und `aria-label`.
  Die GIF-Symbole der alten Templates (`edit.gif`, `toggle-button-16x16.gif`, `up-16x16-grey.png`, `move.gif`,
  `button-move_up.gif` ...) und die Inline-SVGs sind aus den Templates von admin4 verschwunden. Die Bildschaltflächen
  der Klassenbearbeitung (ein Attribut ganz nach oben, nach oben, nach unten, ganz nach unten verschieben) und des
  Fensters Orte (verschieben) sind `<button>`-Elemente, die dieselbe Variable senden: `MoveUp` = die Attribut-ID, wo die
  Bildschaltfläche `MoveUp_<id>_x` und `_y` sendete, die `eZHTTPTool` in dasselbe umwandelte.
- **In den Stylesheets von admin4:** Die Meldungen, die sortierten Spalten, der Tab-Schalter, die Tabelle der
  Unterelemente (ihr Zeilenmenü, die Bildvorschau, die Schaltflächen darüber und ihre Menüs), das Suchfeld, die
  Kontextmenüs und der Inhaltsbaum zeichnen Bootstrap-Symbole als Masken auf einem Pseudo-Element, anstelle der GIF-
  und PNG-Sprites. Öffnen und Schließen im Baum sind ein Pfeil nach rechts und ein Pfeil nach unten auf dem eigenen
  Hintergrund der Spalte, sodass die Verbindungslinie sie nicht kreuzt.
- **Symbole für Klassen, Dateitypen, Klassengruppen und andere** (`class_icon`, `mimetype_icon`, `classgroup_icon`,
  `icon`, `icon_info` des Inhaltsbaums): das Symbol-Theme `bootstrap-icons`, das dem Design folgt
  (`icon.ini [IconSettings] DesignTheme[admin4]`, Schritt 4 oben): Ein Benutzer, der zu admin3 wechselt, erhält wieder
  das `Theme` des Siteaccess. Seine `icon.ini` ordnet jede Klasse und jeden MIME-Typ von `crystal-admin`, die Klassen
  der Website-Interface- und Demo-Pakete sowie die Typen OOXML, EPUB, RTF, CSV, XML, JSON und SVG zu; ein SVG dient
  jeder Größe. Für
  eine Klasse, die es nicht zuordnet, nehmen `class_icon` und die anderen Operatoren das PNG des `Theme` des
  Siteaccess (`crystal-admin` in einem Admin-Siteaccess), der Inhaltsbaum (`icon_info`) liest aber nur das erste Theme
  und zeigt dessen
  `Default`: Fügen Sie eine eigene Klasse zu `ClassMap` in einer `icon.ini.append.php` hinzu. Im dunklen Modus macht
  ein Filter in `admin4.css` diese
  `<img>`-Symbole hell, da ein SVG in einem `<img>` die Farben der Seite nicht übernehmen kann.
- **Die Tabs des Hauptmenüs in der Kopfzeile:** Jeder Tab zeigt vor seinem Namen ein Symbol, benannt durch `menu.ini
  [Topmenu_<tab>] Icon` (Inhaltsstruktur `diagram-3`, Medien `images`, Benutzer `people`, Shop `box-seam`, Design
  `palette`, Setup `gear`, Audit `shield-fill-check`, Dashboard `speedometer2`). Der Operator `topmenu` reicht es als
  `icon` weiter; `page_topmenuitem.tpl` zeichnet es nur, wenn `design.ini [AdminDesignSettings] IconList[]` den Namen
  enthält, daher setzt ein Tab einer Extension `Icon=` in seiner `menu.ini.append.php` mit einem Namen aus dieser
  Liste, und ein Tab ohne Symbol zeigt nur seinen Namen. `design.ini [AdminDesignSettings] TopMenuIcons=disabled`
  lässt jedes Tab-Symbol weg (Standard
  `enabled`); ein Siteaccess oder eine Extension kann in der eigenen `menu.ini.append.php` für jeden Tab ein anderes
  `Icon` setzen.
- **Ein einziger Symbolsatz:** Die Schnellaktionen des Dashboards zeigen die Symbole der Tabs (und `upload`, `tags`,
  `layout-sidebar-inset`, `arrow-clockwise`), das Benutzermenü der Kopfzeile die Symbole der Aktionskacheln des
  Telefonmenüs (`pencil-square`, `key`, `envelope`), und die Schaltfläche des Telefonmenüs die Symbole `list` und,
  solange es geöffnet ist, `x-lg`; in Kopfzeile und Dashboard ist kein Textsymbol (☰, ×, ▣, ☻) mehr übrig.
- Die klassischen Designs (`admin`, `admin3`) behalten ihre Bilder; `.a4-icon` und `.a4-icon-btn` stehen auch in ihrer
  Kopie von `components.css`, für Templates, die sich alle Administrationsdesigns teilen.

So fügen Sie ein Symbol hinzu: Kopieren Sie das SVG unverändert aus dem Paket Bootstrap Icons 1.13.2 nach
`share/icons/bootstrap-icons/svg/`, ergänzen Sie `.a4-bi-<name>` in beiden Kopien von `components.css` (oder nennen
Sie die Datei in der `icon.ini` des Themes) und verwenden Sie es. `tests/tests/design/AdminIconsTest.php` schlägt fehl
bei einer Datei, die nichts nennt, einem Namen ohne Datei, einer geänderten Datei sowie einem GIF-Symbol, Inline-SVG
oder einer Bildschaltfläche in einem admin4-Template.

## Setup-Übersicht

Der Tab Setup öffnet `setup/menu`: einen Seitenkopf und jede Funktion des Setup-Menüs als Kachel, in den Gruppen des
linken Menüs (Inhaltsmodell, Website und URLs, Design, Workflows, Benutzer und Nachrichten, Audit, System, KI,
Updates; Design und Audit nur, solange ihre Tabs ausgelassen sind; und zuletzt „Mehr“ für Links
ohne Gruppe).

- **Eine Quelle.** Die Kacheln sind das linke Menü, nur anders gezeichnet: `setup/setupmenu.tpl` bindet
  `parts/setup/menu.tpl` ein, das `[Leftmenu_setup]` aus `menu.ini` mit `menu_layout='tiles'` an
  `parts/ini_menu.tpl` übergibt. Die Links, ihre Namen, ihre Gruppen und die Zugriffsprüfungen (`PolicyList_<link>[]`,
  `CheckViewAccess`, `NoAccessLinks`) sind die des Menüs: Eine Funktion, die der Benutzer nicht öffnen darf, hat keine
  Kachel, und der Link einer Extension erscheint, wenn er zu `[Leftmenu_setup] Links[]` hinzugefügt wird, wie beim
  Menü: in der Gruppe, die ihn in `GroupLinks_<group>[]` nennt, sonst unter „Mehr“. Die Reihenfolge innerhalb einer
  Gruppe ist die von `Links[]`.
- **Website und URLs** beginnt mit Seiten vorladen, RSS und Pakete, dann folgen PDF-Export, die Suchstatistiken und
  die URL-Funktionen, und es endet mit RAD (`GroupLinks_site[]` und `Links[]` in derselben Reihenfolge, da eine Gruppe
  ihre Links in der Reihenfolge von `Links[]` zeichnet). Die klassischen Menüs (`menu_management`,
  `visual/menuconfig`) sind kein Setup-Link: Sie stehen im linken Menü des Tabs Design (`[Leftmenu_design]`, nach
  Werkzeugleisten Verwaltung) und damit in der Gruppe Design, solange dieser Tab ausgelassen
  ist; ihre Seite markiert den Tab Design.
- **Gruppen für Extensions.** „KI“ und „Updates“ enthalten die Setup-Links der se7enx-Extensions, die bereits in der
  `menu.ini` des Kerns benannt sind, damit sie nicht unter „Mehr“ landen, wo ein unsortierter Eintrag fehl am Platz
  wirkt: `expai` (`expai_summary`, `expai_models`, `expai_budgets`) unter KI; `ezupdate` (`ezupdate`) und
  `git_manager` (`git_manager`, `git_manager_backup`) unter Updates. Die Job-Warteschlange (`expqueue`, auch mit `expai`
  ausgeliefert) und der Datenbankeditor von `sevenx_dse` (`dse`) sind System-Werkzeuge und stehen unter System. Ein
  Schlüssel, dessen Extension nicht aktiv ist, hat keinen Link und zeigt nichts; eine Gruppe ohne Link, den der
  Benutzer öffnen darf, wird nicht gezeichnet, sodass die Seite ohne diese Extensions aussieht wie zuvor.
  „Mehr“ bleibt die letzte Gruppe, für Links, die noch keine Gruppe nennen.
- **Eine Kachel** ist eine Karte (`.a4-card`) mit einem Bootstrap-Symbol in einem Akzentquadrat, dem
  Namen der Funktion als Link, über die ganze Karte gestreckt mit einem einzigen Fokusring darum, und einer Zeile
  darüber, was die Funktion tut, als Beschreibung des Links. Symbol, Beschreibung und Kennzahl stammen aus dem eigenen
  Abschnitt des Menüs in `menu.ini`, je Link-Schlüssel (siehe „Erweitern“ unten); ein Link ohne diese Angaben erhält
  das Steckersymbol und keine Beschreibung.
  Mit `NoAccessLinks=disabled` ist eine Funktion, die der Benutzer nicht öffnen darf, eine Kachel ohne Link und ohne
  Kennzahl, so wie das Menü ihren Namen ohne Link zeigt.
- **Kennzahlen**, nur wo ein einziger günstiger Fetch sie liefert und nur auf einer Kachel, die der Benutzer öffnen
  darf (`setup/setupmenu_figure.tpl`, benannt durch `LinkFigureTemplates[]`): Klassen, Sektionen, Inhaltssprachen,
  aktive Extensions, die existieren (`expinfo( 'active' )`), angemeldete Benutzer, pausierte Cronjob-Teile (nur solange
  einer pausiert ist, `fetch( 'setup', 'cronjob_pauses' )`); Einzahl und Mehrzahl sind zwei Zeichenketten.
- **Raster.** Eine Spalte auf einem Telefon, dann zwei, drei und vier, je breiter die Inhaltsspalte wird (Container
  Queries auf `.a4-setup-overview`, sodass eine breite linke Spalte mitzählt). Stile in
  `stylesheets/modules/setup.css`, nur Tokens, hell und dunkel, erzwungene Farben.
- **Linkes Menü.** „Übersicht“ ist der erste Eintrag des Setup-Menüs, vor den Gruppen: menu.ini `LeadLinks[]` nennt
  Links, die vor den Gruppen und in keiner von ihnen stehen (`.a4-navlead`). Auf der Übersicht ist er als aktuelle
  Seite markiert, was ihn aus ihren Kacheln herausnimmt. `TrailLinks[]` ist das Gegenstück am Ende: Die genannten Links
  werden nach allen anderen Links gezeichnet, in ihrer Reihenfolge, auch nach den Links, die Extensions anhängen; in
  einem gruppierten Menü nach den Gruppen und „Mehr“, in keiner von ihnen (`.a4-navtrail`), in einer einfachen Liste
  als ihre letzten Einträge, in den Kacheln als letzte Kacheln. Das Dashboard-Menü endet auf diese Weise mit Passwort
  ändern und Profil bearbeiten.
- **Zugriff.** Die Ansicht `setup/menu` öffnet sich mit jeder einzelnen Funktion des Setup-Moduls (`setup`,
  `administrate`, `managecache`, `managecronjobs`, `preload`, `system_info`), sodass eine Rolle, die die frühere Seite
  des Tabs (`setup/cache`) erreichte, den Tab behält. Der Tab verlangt nichts weiter (`[Topmenu_setup]` hat keine
  `PolicyList[]`): Eine Rolle nur mit `system_info` hat den Tab und mit ihm die Tabs aus `[TopAdminMenu] SetupTabs[]`.
- **Klassische Designs** zeigen eine kurze Seite mit einem Hinweis auf das Setup-Menü und die Werkzeugleiste `setup`
  aus toolbar.ini.

| Link-Schlüssel | Symbol | Link-Schlüssel | Symbol |
|---|---|---|---|
| classes | `diagram-3` | cache | `arrow-clockwise` |
| sections | `collection` | system_information | `info-circle` |
| states | `toggles` | upgrade_check | `arrow-up-circle` |
| languages | `translate` | maintenance | `wrench-adjustable` |
| contentfinder | `search` | cronjobs | `clock` |
| collected | `inbox` | extensions | `puzzle` |
| url_management | `link-45deg` | ini | `sliders` |
| url_translator | `signpost-split` | sitedata | `table` |
| url_wildcards | `asterisk` | sessions | `people` |
| menu_management (Design-Menü) | `menu-button-wide` | notification | `bell` |
| rss | `rss` | mailpreferences | `envelope-paper` |
| pdf_export | `file-earmark-pdf` | oauth_list | `key` |
| packages | `box-seam` | audit | `journal-check` |
| rad | `magic` | workflows | `diagram-2` |
| preload | `speedometer2` | workflow_processes | `hourglass-split` |
| search_statistics | `bar-chart-line` | triggers | `lightning` |
| roles_and_policies | `person-lock` | templates | `file-earmark-code` |
| template_editor | `code-square` | toolbar_management | `tools` |
| look_and_feel | `palette` | global_setting | `globe` |
| expai_summary | `stars` | ezupdate | `cloud-arrow-down` |
| expai_models | `cpu` | git_manager | `git` |
| expai_budgets | `piggy-bank` | git_manager_backup | `archive` |
| expqueue | `list-task` | dse | `database` |
| unreferenced_objects | `file-earmark-break` | broken_relations | `exclamation-diamond` |
| (der Link einer Extension) | `plug` | | |

### Erweitern

Jedes als Kacheln gezeichnete linke Menü (`parts/ini_menu.tpl` mit `menu_layout='tiles'`) liest drei optionale
Einstellungen seines Abschnitts, je Link-Schlüssel; die Setup-Links des Kerns tragen ihre in
`settings/menu.ini [Leftmenu_setup]`:

| Schlüssel | Wert | Ohne ihn |
|---|---|---|
| `LinkIcons[<key>]` | ein Symbolname aus `design.ini [AdminDesignSettings] IconList[]`, z. B. `bar-chart` | das Steckersymbol; ein Name, der nicht in der Liste steht, zeichnet ebenfalls das Steckersymbol |
| `LinkDescriptions[<key>]` | eine Zeile auf Englisch, übersetzt im Kontext des Menüs (`design/admin/parts/setup/menu` für Setup), wie `LinkNames[]` | keine Beschreibung |
| `LinkFigureTemplates[<key>]` | `design:<path>.tpl`, eingebunden mit `figure_key=<key>` auf einer Kachel, die der Benutzer öffnen darf | keine Kennzahl; ein Wert, der nicht mit `design:` beginnt, wird ignoriert |

Eine Extension, die eine Setup-Funktion hinzufügt, gibt ihr in ihrer eigenen `settings/menu.ini.append.php` eine
Kachel:

```ini
<?php /* #?ini charset="utf-8"?

[Leftmenu_setup]
Links[myreports]=myreports/list
LinkNames[myreports]=Reports
PolicyList_myreports[]=myreports/read
LinkIcons[myreports]=bar-chart
LinkDescriptions[myreports]=Monthly reports of the shop.
LinkFigureTemplates[myreports]=design:myreports/setup_figure.tpl

*/ ?>
```

Der Link erscheint im Menü und in der Übersicht unter „Mehr“, bis er eine Gruppe nennt. Eine Extension ordnet ihren
Link mit einer weiteren Zeile im selben Abschnitt einer Gruppe des Menüs zu, sodass „Mehr“ nur das Auffangbecken für
Links ohne Kategorie ist:

```ini
# one of: ai (assistants, models, their costs), updates (packages, updates, backups) or any other group of Groups[]
GroupLinks_ai[]=myreports
```

Eine Gruppe listet die Schlüssel mehrerer Extensions auf; jede fügt ihre eigene Zeile hinzu, und ein Schlüssel, dessen
Extension fehlt, zeigt nichts. Ein Link, der in zwei Gruppen genannt ist, erscheint nur in der ersten. Eine neue Gruppe
kommt in `Groups[]` mit einem Namen in `GroupNames[<group>]` (übersetzt im Kontext des Menüs); sie wird nach den
Gruppen davor gezeichnet und nur dann, wenn sie einen Link enthält, den der Benutzer öffnen darf.
Das Kennzahl-Template zeichnet ein kurzes `<span class="a4-setup-tile-figure">`, fragt nur ab, was die Funktion seiner
Kachel zeigen darf, und bleibt günstig: Es läuft bei jedem Aufruf der Übersicht. Die Beschreibung wird übersetzt, wenn
die Übersetzungsdatei der Extension die Zeichenkette im Kontext des Menüs enthält.

**Symbole.** Eine Kachel zeichnet nur Symbole, die in `IconList[]` aufgeführt sind, jedes eine Maskenregel
`.a4-bi-<name>` über einem SVG von Bootstrap Icons; kein Inline-Stil, kein Bild, und eine Einstellung kann weder Markup
noch eine unbekannte Klasse auf die Seite bringen. Jedes Bootstrap-Symbol, das admin4 mitliefert, ist aufgeführt
(siehe [Symbole](#symbole)). Eine Extension, die ein weiteres braucht, liefert es selbst mit:

1. das SVG, unverändert aus Bootstrap Icons 1.13.2 (MIT), in der Extension, z. B.
   `extension/myreports/design/admin4/images/icons/bar-chart.svg`;
2. eine Maskenregel in einem Stylesheet der Extension, das in `design.ini [StylesheetSettings] BackendCSSFileList[]`
   registriert ist, mit dem Pfad relativ zu diesem Stylesheet:
   `.a4-bi-bar-chart { -webkit-mask-image: url(../images/icons/bar-chart.svg); mask-image: url(../images/icons/bar-chart.svg); }`;
3. `IconList[]=bar-chart` unter `[AdminDesignSettings]` in der `design.ini.append.php` der Extension.

`tests/tests/design/AdminSetupOverviewTest.php` schlägt fehl bei einem Setup-Link ohne Symbol oder Beschreibung,
einem Symbol, das nicht aufgeführt ist, keine Regel oder keine Datei hat, einem doppelt verwendeten Symbol, einer
fehlenden Zugriffsprüfung, einer Kennzahl außerhalb des Link-Zweigs oder aus einem anderen Fetch, einem Bild auf der
Seite oder einer fehlenden Übersetzung; er zeichnet die Kacheln eines Fixture-Menüs mit dem Link einer Extension
(Symbol, Beschreibung, Kennzahl) und eines mit einem unbekannten Symbol.

### Einen Tab nach Setup verschieben

Eine Site, die weniger obere Tabs möchte, lässt Design, Audit oder das Dashboard aus dem oberen Menü weg und verliert
keine Seite:

```ini
<?php /* #?ini charset="utf-8"?

[TopAdminMenu]
HiddenTabs[]
HiddenTabs[]=dashboard
HiddenTabs[]=design
HiddenTabs[]=audit

*/ ?>
```

in `settings/siteaccess/<admin siteaccess>/menu.ini.append.php` oder `settings/override/menu.ini.append.php`; leeren
Sie danach den INI- und den Template-Cache (`php bin/php/ezcache.php --clear-tag=ini` und `--clear-tag=template`):
Werte von `ezini()` werden in die Templates kompiliert. `Tabs[]` neu anzugeben (eine Zeile `Tabs[]` ohne Wert, dann
die zu behaltenden Tabs) funktioniert für die Tabs des Kerns ebenso; der Tab einer Extension wird nur mit
`HiddenTabs[]` ausgelassen. Standardmäßig bleibt jeder Tab.

- **Je Rolle:** `[TopAdminMenu] SetupTabs[]` nennt Tabs, die das obere Menü nur für einen Benutzer verlassen, der die
  Setup-Übersicht öffnen darf (`setup/menu`, geprüft wie die Anfrage). Dieser Benutzer findet die Gruppe des Tabs unter
  Setup; ein Benutzer, der die Übersicht nicht öffnen darf, etwa eine Rolle, die nur die eigene Richtlinie des Tabs
  hat, behält den Tab mit seinem eigenen linken Menü, und dessen Seiten markieren ihn weiterhin. Der Operator des
  oberen Menüs, das Setup-Menü, `parts/a4_navigation_part.tpl` und das Logo stellen dieselbe Frage. Die Kopfzeile wird
  je Rollensatz zwischengespeichert (`$user_hash`), der die Antwort bestimmt; leeren Sie nach einer Änderung der
  Richtlinien einer Rolle den Template-Cache.
- **Design und Audit** haben eine Gruppe im Setup-Menü und in der Übersicht, benannt durch
  `[Leftmenu_setup] GroupTabs[]`
  (`GroupTabs[design]=design`, `GroupTabs[audit]=audit`). Eine solche Gruppe wird nur gezeichnet, solange ihr Tab
  nicht im oberen Menü ist (aus `Tabs[]` ausgelassen, in `HiddenTabs[]` genannt oder in `SetupTabs[]` für einen
  Benutzer, der `setup/menu` öffnen darf); solange der Tab dort ist, werden die Gruppe und die Links, die sie nennt,
  gar nicht gezeichnet, sodass nichts doppelt erscheint.
- **Eine Quelle.** `GroupSections[design]=Leftmenu_design` und `GroupSections[audit]=Leftmenu_audit` fügen der Gruppe
  die Links des eigenen linken Menüs des Tabs hinzu, nach den eigenen `GroupLinks_<group>[]` der Gruppe: dieselben
  Links, Namen (`LinkNames[]` im Kontext dieses Menüs, sonst benennt `parts/setup/menu.tpl` sie `<group>-<key>`),
  Zugriffsprüfungen (`PolicyList[]`, `PolicyList_<key>[]`, `CheckViewAccess`, die Audit-Kanäle, die der Benutzer
  nicht lesen darf) und Kachel-Einstellungen (`LinkIcons[]`, `LinkDescriptions[]`, `LinkFigureTemplates[]` dieses
  Abschnitts, die Beschreibungen übersetzt in seinem Kontext, z. B. `design/admin/parts/audit/menu`). Ein Link, den
  eine Extension zu `[Leftmenu_audit]` hinzufügt, steht ebenfalls in der Gruppe; geben Sie ihm `LinkNames[<key>]`,
  sonst zeigt er dort seinen Schlüssel als Namen. Die Gruppe Design beginnt mit der eigenen Seite des Tabs Design,
  `Links[design_node]=content/view/full/58` in
  `[Leftmenu_setup]` mit der `PolicyList[]=58` des Tabs.
- **Ein Link nur einmal.** Ein Setup-Link auf eine Adresse, die auch das hinzugefügte Menü hat, erscheint nur in
  dieser Gruppe: Mit der Gruppe Audit steht „Einstellungen“ (`audit/settings`) dort, und der Link „Audit“ unter
  „Benutzer und Nachrichten“ entfällt. Ein Link, den das hinzugefügte Menü für den Benutzer auslässt (ein Audit-Kanal,
  den der Benutzer nicht lesen darf), hält auch den Setup-Link auf seine Adresse
  fern.
- **Navigation.** Eine Seite eines solchen Tabs (ihr `NavigationPartIdentifier`, z. B. `audit/console` oder
  `visual/toolbarlist`) markiert den Tab Setup und zeichnet das Setup-Menü, mit ihrer Gruppe am aktuellen Link
  geöffnet, auch wenn ihre Ansicht ein eigenes linkes Menü nennt (`$module_result.left_menu`, wie es die
  Audit-Ansichten tun). Keine Seite markiert einen Tab, der
  nicht im oberen Menü ist, oder zeichnet ein Menü, zu dem kein Tab führt; die Brotkrümelnavigation ist die eigene der
  Seite. Solange der Tab im oberen Menü ist, behalten seine Seiten ihren Tab und ihr linkes Menü.
  `parts/a4_navigation_part.tpl` liefert den Navigationsteil, den beide Menüs
  verwenden, in admin4 und in den Blöcken für Kopfzeile und linkes Menü von admin4l.
- **Dashboard.** Ohne den Tab Dashboard führt das Logo für einen Benutzer, der es öffnen darf
  (`content/dashboard`), zum Dashboard (`[Topmenu_dashboard] URL[default]`) statt zur Inhaltswurzel, die weiterhin der
  Tab Inhalte öffnet. Das
  Dashboard ist auch die Startseite der Administrationsoberfläche nach der Anmeldung, wenn
  `site.ini [SiteSettings] DefaultPage=content/dashboard` gesetzt ist, wie es der Einrichtungsassistent schreibt; sein
  linkes Menü enthält die Seiten des Kontos (Passwort, Entwürfe, Lesezeichen, Benachrichtigungen).
  Das Dashboard hat keine Setup-Gruppe: Es gehört jedem Benutzer, Setup den Administratoren.

Der Tab einer Extension wird auf dieselbe Weise verschoben, in der `menu.ini.append.php` der Extension oder der Site:
eine Gruppe in `[Leftmenu_setup] Groups[]` mit `GroupNames[<group>]`, `GroupTabs[<group>]=<tab>`,
`GroupSections[<group>]=<its
Leftmenu_ section>` (oder `GroupLinks_<group>[]` mit eigenen Links), die Kachel-Einstellungen in ihrem Abschnitt und
der Tab in `HiddenTabs[]`. `GroupTabs[]` und `GroupSections[]` liest nur `parts/ini_menu.tpl` von admin4; die
klassischen Designs behalten ihre Tabs.

| Link-Schlüssel | Symbol | Link-Schlüssel | Symbol |
|---|---|---|---|
| design_node | `brush` | audit-dashboard | `speedometer` |
| design-look_and_feel | `palette` | audit-console | `terminal` |
| design-toolbar_management | `tools` | audit-recent | `list-ul` |
| design-template_editor | `code-square` | audit-charts | `graph-up-arrow` |
| design-templates | `file-earmark-code` | audit-alerts | `exclamation-triangle` |
| | | audit-export | `download` |
| | | audit-archives | `archive` |
| | | audit-settings | `journal-check` |

`AdminSetupOverviewTest` schlägt fehl, wenn Gruppe, Abschnitt, Seite, Symbol, Beschreibung oder Übersetzung eines
verschobenen Tabs fehlen oder wenn ein Tab standardmäßig verborgen ist, und zeichnet `parts/a4_navigation_part.tpl`
mit angezeigten und verborgenen Tabs.

### Mega-Menü (Prototyp, Opt-in)

Eine kleine Schaltfläche neben einem oberen Tab öffnet unter der Kopfzeile einen Bereich mit den meistgenutzten
Funktionen des Tabs in ihren Gruppen: jede mit ihrem Symbol, ihrem Namen und ihrer Beschreibung darunter, bis zu drei
Gruppen nebeneinander, und am Ende „Weiter zu
<tab>“ (die eigene Seite des Tabs, bei Setup die Übersicht) für den Rest. Setup, Benutzer und Dashboard haben eines.
Standardmäßig aus; um sie einzuschalten, in einer `menu.ini.append.php` eines Siteaccess oder in override:

```ini
<?php /* #?ini charset="utf-8"?

[TopAdminMenu]
MegaMenuTabs[]=setup
MegaMenuTabs[]=users
MegaMenuTabs[]=dashboard
*/ ?>
```

Leeren Sie danach die Caches (den INI-Cache und die Template-Caches: Die Kopfzeile liest die Einstellung, wenn ihr
Template kompiliert wird). Mit leerem `MegaMenuTabs[]` zeichnet die Kopfzeile keine Schaltfläche, und die Seite lädt
kein Skript. Es ist immer nur ein Bereich geöffnet: Das Öffnen eines Bereichs, per Klick, Taste oder Hover-Vorschau,
schließt den anderen. Ein per Klick oder Taste geöffneter Bereich wird nicht
durch die Hover-Vorschau eines anderen Tabs ersetzt.

#### Welche Tabs teilnehmen und wie Sie einen hinzufügen

Ein Tab nimmt teil, wenn er in `MegaMenuTabs[]` genannt ist **und** sein `[Topmenu_<tab>]` ein `MegaMenuTemplate`
nennt; ein Name ohne dieses wird ignoriert. Das Template zeichnet das Menü des Tabs als Gruppen des Bereichs, immer
über `parts/ini_menu.tpl` mit `menu_layout='tiles'` und `tiles_layout='mega'`, sodass die Links die Namen, Gruppen,
Symbole, Beschreibungen und Zugriffsprüfungen eines menu.ini-Abschnitts behalten:

| Tab | `MegaMenuTemplate` | Abschnitt | Standardauswahl (`MegaMenuLinks[]`) |
|---|---|---|---|
| `setup` | `design:parts/setup/mega_menu.tpl` (das eigene `parts/setup/menu.tpl` des Setup-Menüs) | `Leftmenu_setup` | neunzehn, siehe unten |
| `users` | `design:parts/user/mega_menu.tpl` | `Leftmenu_user_mega` | acht, siehe unten |
| `dashboard` | `design:parts/my/mega_menu.tpl` | `Leftmenu_my_mega` | siebzehn, siehe unten |

So geben Sie einem anderen Tab eines (etwa dem einer Extension): Schreiben Sie in admin4 ein Template, das
`parts/ini_menu.tpl` mit dem Abschnitt des Tabs, `menu_layout='tiles'`, `tiles_layout='mega'` und dem `i18n_hash`
seiner Linknamen einbindet; nennen Sie es in
`[Topmenu_<tab>] MegaMenuTemplate=design:...` (ein `design:`-Template, nichts anderes wird akzeptiert) und seinen
Abschnitt in `MegaMenuSection=<section>` (nur von der Prüfung der Einstellungen gelesen, siehe unten); geben Sie den
Links des Abschnitts `LinkIcons[]` (Namen aus `design.ini [AdminDesignSettings] IconList[]`) und `LinkDescriptions[]`
sowie ein `MegaMenuLinks[]`; fügen Sie den Tab dann zu `MegaMenuTabs[]` hinzu. Ein Tab, dessen linkes Menü keine
menu.ini-Liste ist (ein Baum, ein eigenes Template), erhält einen Abschnitt, der nur seine Funktionen sammelt, wie es
der Tab Benutzer tut.

#### Der Tab Benutzer

Das linke Menü des Tabs Benutzer ist der Benutzerbaum und `[Leftmenu_user]` (Rollen und Richtlinien, Nicht aktivierte
Benutzer), gezeichnet von `parts/user/menu.tpl`; ein Baum ist keine Liste von Links, und das linke Menü bleibt, wie es
ist. Sein Mega-Menü hat daher einen eigenen Abschnitt, `[Leftmenu_user_mega]`, der keinen Link definiert, den bereits
ein Menü definiert: `GroupSections[]` fügt `[Leftmenu_user]` seiner Gruppe „Zugangskontrolle“ und `[Leftmenu_my]`
(die eigenen Einstellungen des Benutzers, das Menü des Tabs Dashboard) der Gruppe „Mein Konto“ hinzu, mit ihren
Adressen, Richtlinien, Symbolen und Beschreibungen (`LinkIcons[]`/`LinkDescriptions[]`
sind in diesen Abschnitten gesetzt; sie werden nur dort gelesen, wo ein Menü als Kacheln oder als Mega-Menü
gezeichnet wird). Seine eigenen Links sind
die beiden, für die der Tab keinen Menüeintrag hat: der Benutzerbaum (`content/view/full/5`, die eigene Seite und
Richtlinie des Tabs, wie in `[Topmenu_users]`; eine Site mit einem anderen `content.ini [NodeSettings] UserRootNode`
ändert beides) und die Sitzungen (`setup/session`, die Richtlinie des Setup-Menüs). Er heißt `Leftmenu_user_*`, damit
er den Übersetzungskontext des Menüs des Tabs teilt, `design/admin/parts/user/menu`. Die Standardauswahl nach Gruppe:

| Gruppe | Links (`MegaMenuLinks[]`) |
|---|---|
| Benutzer | `accounts` (Benutzer und Gruppen), `sessions` (Sitzungen) |
| Zugangskontrolle | `access-roles_and_policies` (Rollen und Richtlinien), `access-unactivated` (Nicht aktivierte Benutzer) |
| Mein Konto | `account-edit_profile`, `account-change_password`, `account-my_notifications`, `account-my_mail` |

Es gibt keinen Link zum Anlegen eines Benutzers: Inhalte werden durch einen POST aus dem Baum angelegt
(`content/action`), und keine Adresse öffnet das Benutzerformular direkt. Auch eine Berechtigungsprüfung für einen
einzelnen Benutzer hat im Kernel keine Ansicht.

#### Der Tab Dashboard

Das linke Menü des Tabs Dashboard ist `[Leftmenu_my]`, eine einzige Liste (`parts/my/menu.tpl`), an die die
Extensions ihre Links anhängen: Exponential Intelligence (Extension expai) fügt dort sein Dashboard, Vector, Chains,
Log und Hilfe hinzu, und die Hilfe ergänzt das Hilfe-Center. Sein Mega-Menü
(`[Topmenu_dashboard] MegaMenuTemplate=design:parts/my/mega_menu.tpl`)
sortiert diese mit einem eigenen Abschnitt, `[Leftmenu_my_mega]`, in Gruppen; dieser definiert wie der des Tabs
Benutzer keinen Link: Jede Gruppe fügt die Menüs hinzu, die ihre Links definieren (`GroupSections[]`), sodass jeder
Link seine Adresse, Richtlinien, sein Symbol und seine Beschreibung behält, und der Link einer nicht aktiven Extension
ist einfach nicht vorhanden. Eine Gruppe kann
mehrere Abschnitte hinzufügen, getrennt durch `;`, in dieser Reihenfolge
(`GroupSections[ei]=Leftmenu_my;Leftmenu_design`); ihre Link-Schlüssel müssen sich unterscheiden, da ein Eintrag
`<group>-<key>` heißt. Die Gruppe „Aktivität und Audit“ fügt `[Leftmenu_audit]` hinzu (die
Audit-Konsole, die das Audit-Protokoll des Dashboard-Menüs ist, und die KI-Ereignisse von expai; die Links in Kanäle,
die der Benutzer nicht lesen darf, werden wie im Audit-Menü ausgelassen), die Gruppe „Exponential Intelligence“ fügt
`[Leftmenu_my]` und
`[Leftmenu_design]` hinzu (Missing alt text von expai). Die Symbole und Beschreibungen der expai-Links sind in der
`menu.ini` des Kerns neben dem Menü gesetzt, zu dem sie gehören, wie bei den EI-Links des Setup-Menüs; sie werden nur
dort gelesen, wo ein Menü
als Kacheln oder als Mega-Menü gezeichnet wird. Die Standardauswahl nach Gruppe, in dieser Reihenfolge (das Audit vor
Exponential Intelligence, Passwort ändern und Profil bearbeiten zuletzt, wie im linken Menü):

| Gruppe | Links (`MegaMenuLinks[]`) |
|---|---|
| Übersicht | `overview-dashboard` (Dashboard); das Hilfe-Center (`overview-help_center`) kommt hinzu, sobald die Hilfe ihren Link zu `[Leftmenu_my]` hinzufügt |
| Ihre Arbeit | `work-my_drafts`, `work-my_pending`, `work-collaboration`, `work-my_bookmarks` |
| Aktivität und Audit | `activity-console` (Audit-Protokoll), `activity-expai_events` (KI-Ereignisse) |
| Exponential Intelligence | `ei-expai`, `ei-expai_vector`, `ei-expai_chain`, `ei-expai_alttext`, `ei-expai_log`, `ei-expai_help` |
| Mein Konto | `account-my_notifications`, `account-my_mail`, `account-change_password`, `account-edit_profile` |

Siebzehn Links, jede Funktion des Tabs außer der bevorzugten Währung des Shops, die im linken Menü bleibt. Jeder wird
nur einem Benutzer angezeigt, der ihn öffnen darf (die expai-Links brauchen `expai/write` und ihre eigenen
Richtlinien, die Audit-Links `audit/read`), und eine Gruppe, die ohne Link bleibt, wird nicht gezeichnet.

Der Bereich ist breit (`[Topmenu_dashboard] MegaMenuLayout=wide`): Statt bis zu drei Gruppen von 17rem nebeneinander
werden die Gruppen in bis zu vier ausgeglichenen Spalten von 15rem gesetzt (CSS-Spalten; eine Gruppe wird nie
geteilt), vier bei 1440px, drei bei 960px (ein 1920px-Bildschirm bei 200 % Zoom), darunter zwei. Siebzehn Links mit
ihren Beschreibungen passen dann in die Fensterhöhe, ohne innerhalb des Bereichs zu scrollen. Ein anderer Tab mit
einer langen Auswahl kann dasselbe Layout mit `MegaMenuLayout=wide` in seinem `[Topmenu_<tab>]` erhalten; ohne dies
(Setup, Benutzer) wird der Bereich wie bisher gezeichnet.

- **Eine kurze Auswahl, nicht das ganze Menü.** `MegaMenuLinks[]` des Abschnitts (für Setup `[Leftmenu_setup]`)
  nennt die angezeigten Links nach Link-Schlüssel,
  in ihrer Reihenfolge; höchstens 19 werden angezeigt (6 bis 19 lesen sich am besten). Es ist eine Auswahl aus den
  Links des Menüs, kein zweites Menü: Jeder Link behält seinen Namen, sein Symbol, seine Beschreibung, seine Gruppe
  (`GroupNames[]`) und seine Zugriffsprüfungen. Ein Link, den der Benutzer
  nicht öffnen darf oder dessen Extension nicht aktiv ist, wird ausgelassen, ebenso eine Gruppe, in der kein Link
  übrig bleibt; die Gruppen folgen der Reihenfolge ihres ersten gewählten Links. Der Standard in `settings/menu.ini`
  sind neunzehn, hier nach Gruppe: Klassen, Sektionen,
  Gesammelte Informationen (Inhaltsmodell) | RSS, Seiten vorladen, Pakete (Website und URLs) | Audit,
  oAuth-Verwaltung (Benutzer und Nachrichten) | Cache-Verwaltung, Cronjobs, Logdateien,
  Extensions, Systeminformationen, Ini Einstellungen (System) | `expai_summary`, `expai_models`, `expai_budgets` (KI) |
  `ezupdate`, `git_manager` (Updates). Ohne `expai`, `ezupdate` und `git_manager` fehlen die Gruppen KI und
  Updates, hier wie in der Übersicht, und es bleiben vierzehn Links. Um andere zu wählen, geben Sie die Liste neu an:

  ```ini
  [Leftmenu_setup]
  MegaMenuLinks[]
  MegaMenuLinks[]=classes
  MegaMenuLinks[]=languages
  MegaMenuLinks[]=cache
  ```

  Ein nach Setup verschobener Tab ([Einen Tab nach Setup verschieben](#einen-tab-nach-setup-verschieben)) bringt sein eigenes
  Menü in seine Gruppe mit,
  und seine Links werden als `<group>-<key>` gewählt, z. B. `MegaMenuLinks[]=audit-console` (die Namen, die auch der
  i18n-Hash von
  `parts/setup/menu.tpl` verwendet). Ein Setup-Link, der dann nicht gezeichnet wird, weil die Gruppe einen Link auf
  dieselbe Adresse hat, übernimmt diesen Link: Bei verschobenem Tab Audit erscheint `audit` (`audit/settings`) als
  Einstellungen der Gruppe Audit, sodass der Standard mit und ohne verschobene Tabs funktioniert. Ein Schlüssel,
  dessen Link gezeichnet wird, wird immer zuerst über seinen Schlüssel übernommen.

  Eine auf keine Werte zurückgesetzte Liste zeigt jeden Link des Menüs, wie es die Übersicht tut (nicht empfohlen:
  etwa 35 Links).
  Der Standard steht in `settings/menu.ini` statt im Template, damit er dort gelesen und geändert werden kann, wo die
  Links definiert sind.
- **Der Tab bleibt ein Link.** Die Schaltfläche daneben ist ein Aufklappelement (das WAI-ARIA-Muster „Disclosure
  Navigation“, kein Menü): `aria-expanded`, `aria-controls`, benannt „Menü „<tab>““ (ein Screenreader ergänzt
  „reduziert“ oder „erweitert“ aus `aria-expanded`, daher sagt der Name nicht „öffnen“). Klick, Enter oder Leertaste
  öffnen und schließen den
  Bereich; Pfeil nach unten öffnet ihn und springt zu seinem ersten Link; Tab durchläuft seine Links der Reihe nach
  und geht über den Bereich hinaus, was ihn schließt, ebenso wie ein Klick außerhalb; Escape schließt ihn und gibt
  den Fokus an die Schaltfläche zurück. Jede Gruppe ist ein
  Abschnitt mit einer Überschrift (`h2`, klein gezeichnet); die Links sind einfache Links, die Beschreibung ist die
  Beschreibung des Links (`aria-describedby`); der Link auf die angezeigte Seite (auch der Link des Tabs) ist mit
  `aria-current="page"` markiert.
- **Hover ist nur eine Vorschau.** Ruht eine Maus 300 ms auf dem Tab oder der Schaltfläche, öffnet sich der Bereich;
  verlässt sie Tab, Schaltfläche und Bereich für 400 ms, schließt er sich (die Lücke zwischen Kopfzeile und Bereich
  gehört zum Bereich, sodass der Weg darüber nicht als Verlassen zählt). Ein Klick auf die Schaltfläche oder ein Fokus,
  der in den Bereich wandert, hält die Vorschau offen. Nur ein Zeiger vom Typ
  Maus auf einem Gerät, das `(hover: hover) and (pointer: fine)` meldet; nie bei Berührung und nicht, solange die
  Liste „Mehr“ oder das Benutzermenü der Kopfzeile geöffnet ist.
- **Ab 576px**, überall dort, wo das Hauptmenü einzeilig ist: die einzeilige Kopfzeile ab 1280px und die zweizeilige
  Kopfzeile von 576
  bis 1279px, die auch ein Browser bei 200 % Zoom auf einem 1920px-Bildschirm zeigt (960px). Der Bereich wird unter
  der gemessenen Höhe der Kopfzeile (`--header-height`) platziert, und seine Gruppen umbrechen bei einem schmaleren
  Fenster auf zwei oder eine Spalte und scrollen innerhalb des Bereichs, wenn sie höher als das Fenster sind. Unter
  576px, im Telefonmenü, werden Schaltfläche und
  Bereich nicht gezeichnet, und der Tab ist ein einfacher Link. Ein Tab, der in die Liste „Mehr“ wandert, verliert
  dort seine Schaltfläche.
- **Unter dem eigenen Tab, innerhalb des Fensters.** Das Skript platziert jeden Bereich unter seinem Tab: die
  Startkante des Bereichs an der Startkante des Tabs (bei Rechts-nach-links-Schrift die rechten Kanten), zurück ins
  Fenster geschoben, wenn der Tab zu nah an dessen
  Ende liegt, mit 24px (`--a4-space-5`) Reserve auf beiden Seiten. Es setzt `--a4-mega-start` des Bereichs über das
  CSSOM (kein `style`-Attribut, sodass die Content Security Policy eingehalten wird), wenn der Bereich sich öffnet,
  wenn seine Links geladen sind und
  wenn sich die Fenstergröße ändert; `admin4.css` liest es auf `.a4-mega-panel.is-anchored`. Ohne das Skript bleibt
  der Bereich zentriert unter der Kopfzeile.
- **Die Schaltfläche ist ein Pfeil in der Pille des Tabs.** Sie steht im Endabstand des Tabs (24px statt 12px),
  sodass ein Tab mit Mega-Menü 12px breiter ist als einer ohne (zuvor 26px mit der Schaltfläche daneben), und ein
  ausgewählter Tab behält seine Pille mit dem Pfeil darin (`--a4-on-accent`). Der Tab ist weiterhin ein Link (Enter
  folgt ihm); der Pfeil ist die
  Schaltfläche (Enter, Leertaste, Pfeil nach unten öffnen sie). Mit drei Tabs, die eines haben (Dashboard, Benutzer,
  Setup), braucht die Reihe weniger
  Platz als zuvor mit zwei.
- **Die Tab-Symbole der Reihe ab 1600px.** Zwischen 576 und 1599px zeigen die Tabs der einzeiligen Reihe ihre Namen
  ohne
  ihre Symbole (`menu.ini [Topmenu_<tab>] Icon`), etwa 22px je Tab: Bei 1440px bleiben so Setup und die Pfeile mit dem
  Hilfe-Link der Kopfzeile in der
  Reihe. In „Mehr“ und in den Kacheln des Telefonmenüs bleiben die Symbole. Der Tab der aktuellen Seite
  wandert als letzter in „Mehr“ (`admin4.js`), sodass er ausgewählt in der Reihe erscheint.
- **Unbekannte Einstellungen werden im Debug-Bericht genannt.** `parts/a4_mega_menu_check.tpl` (mit dem Skript
  eingebunden, also nur, solange `MegaMenuTabs[]` einen Tab nennt) schreibt eine Debug-Meldung für einen Wert in
  `MegaMenuTabs[]` ohne
  `MegaMenuTemplate` und `NavigationPartIdentifier`, für einen Schlüssel in `MegaMenuLinks[]` des Abschnitts
  (`[Topmenu_<tab>] MegaMenuSection`), der kein Link des Abschnitts und kein `<group>-<key>` eines Abschnitts ist, den
  sein
  `GroupSections[]` hinzufügt (ein unbekannter Schlüssel oder der Link einer Extension, die in diesem Siteaccess nicht
  aktiv ist), und
  für mehr als 12 Schlüssel. Kopfzeile und Bereich lassen einfach weg, was sie nicht verwenden können. Die Meldung
  wird geschrieben, wenn
  der cache-block der Seite aufgebaut wird; leeren Sie daher den template-block-Cache, um sie erneut zu sehen.
- **Eine Quelle, bei Bedarf geladen.** Die Links sind die des Menüs des Tabs: `user/megamenu/<tab>`, eine Ansicht für
  jeden Tab, ohne Seitenlayout (`ui_context` ajax, `Cache-Control: private, no-store`, `Vary: Cookie`, kein
  Debug-Bericht, selbst mit `DebugOutput=enabled`, die Einrückung der Templates zwischen den Tags entfernt: etwa 5 KB
  für Setup
  statt 75 KB), zeichnet das
  `MegaMenuTemplate` des Tabs (`user/megamenu.tpl`), das über `parts/ini_menu.tpl` mit `tiles_layout='mega'` läuft
  (`parts/a4_mega_menu.tpl`): dieselben Namen, Gruppen, Symbole (`LinkIcons[]`), Beschreibungen
  (`LinkDescriptions[]`)
  und Zugriffsprüfungen (`PolicyList_<link>[]`, `CheckViewAccess`, `NoAccessLinks`), ohne Kennzahlen.
  `a4_mega_menu.js`
  fragt sie einmal an, wenn der Bereich zum ersten Mal geöffnet wird; die Seite selbst trägt nur die Schaltfläche,
  eine Statuszeile und den Link des Tabs (etwa 1 KB), mit der Kopfzeile je Rollensatz (`$user_hash`)
  zwischengespeichert, wie die Tabs.
- **Das Laden.** Eine Anfrage je Tab und Seite (`credentials: same-origin`, `Accept: text/html`,
  `X-Requested-With`, `cache: no-store`): Ein Öffnen, während sie läuft, wartet auf dieselbe Anfrage, das Schließen
  des Bereichs bricht nichts ab, und die Links bleiben für die Lebensdauer der Seite erhalten. Die Antwort gilt nur
  dann als Menü, wenn sie die
  Markierung des Fragments enthält, `data-a4-mega-fragment="<tab>"` (gesetzt von `user/megamenu.tpl`; ein eigenes
  Fragment eines Designs ohne
  sie wird weiterhin über sein `data-a4-mega-groups` angenommen). Ein vorübergehender Fehler (keine Verbindung, 5xx,
  408, 429, keine Antwort
  nach 10 s, eine Antwort, die nicht das Menü ist) wird nach 600 ms noch einmal versucht. Ein 401, ein 403, eine
  Weiterleitung auf
  `user/login` oder eine Anmeldeseite an seiner Stelle zeigt, dass der Benutzer abgemeldet ist, mit einem Link
  „Anmelden“
  (`data-a4-mega-signed-out`, `data-a4-mega-signin`, `data-a4-mega-login` am Bereich). Ein endgültiger Fehler wird
  gemeldet (`data-a4-mega-error`), mit einer Schaltfläche „Erneut versuchen“ (`data-a4-mega-retry`); der Link des Tabs
  bleibt darunter, und das
  nächste Öffnen versucht es ebenfalls erneut. Eine Anfrage, die der Browser abbricht, weil eine andere Seite geöffnet
  wird (ein AbortError oder der Netzwerkfehler, den Firefox liefert), ist kein
  Fehler. Das Skript bindet jeden Tab einmal (`data-a4-mega-bound`), wie oft es auch geladen wird.
- **Die Rechte.** Die Richtlinie der Ansicht ist `user/login` (jeder Benutzer des Siteaccess, wie bei
  `user/preferences`), da die
  Tabs zu verschiedenen Modulen gehören und eine Richtlinie eines davon die Benutzer der anderen aussperren würde. Die
  Rechte sind
  die des Tabs und der Links: Die Ansicht antwortet nur für einen in `MegaMenuTabs[]` genannten Tab mit einem
  `MegaMenuTemplate`
  (jeder andere: nicht verfügbar) und nur, solange der Tab im oberen Menü des Benutzers steht, aktiviert und für den
  Benutzer geöffnet ist
  (sonst verweigert), nie für den anonymen Benutzer. Dieses Urteil ist das des Operators topmenu, mit dem die
  Kopfzeile die Tabs zeichnet: `Tabs[]`, `HiddenTabs[]`, `SetupTabs[]`, die `PolicyList[]` des Tabs und, mit
  `CheckViewAccess`, seine
  Adresse; `Shown[]` und `Enabled[]` werden für den Kontext `navigation` gelesen, den der Seiten der
  Administrationsoberfläche (ein Tab,
  der dort vom Kontext der Seite abweicht, erhält eine Schaltfläche, deren Bereich verweigert wird). Jeder Link wird
  dann wie im linken Menü geprüft. Die Kopfzeile zeichnet nur bei einem Tab eine Schaltfläche, den sie aktiviert und
  geöffnet anzeigt. Ein Tab-Name besteht aus Kleinbuchstaben, Ziffern und `_` (wie `[Topmenu_<tab>]`); geben Sie einem
  Tab mit Mega-Menü eine `PolicyList[]`, wie es die mitgelieferten haben. Die Anfrage ist eine
  Ansicht des Moduls user, daher erfasst das Prüfprotokoll sie als `access.view.sensitive`, einmal je Seite, auf der
  ein Bereich geöffnet wird.
- **Antworten für das Skript.** Die Ansicht antwortet ihrem Skript, nie einer Person, daher ist jede Antwort eine
  nackte Antwort mit
  einem Status und einem Header `X-Exp-Mega`, der das Urteil nennt: `200 ok` (das Fragment), `404 unknown` (ein Tab,
  den sie nicht bedient), `403 signed-out` (kein angemeldeter Benutzer oder eine Session, die zur erneuten Anmeldung
  aufgefordert wurde) und `403 denied` (kein
  `user/login` für diesen Siteaccess, oder der Tab ist für den Benutzer nicht geöffnet). Nie die Anmeldeseite oder
  eine Fehlerseite mit dem Seitenlayout. Dafür nennt `site.ini` `user/megamenu` in
  `[SiteAccessSettings] AnonymousAccessList[]`
  und `[RoleSettings] PolicyOmitList[]`, und die Ansicht führt die Prüfungen des Kernels selbst aus (`user/login` mit
  seiner `SiteAccess`-Einschränkung, dann den Tab). Eine Site, die einen der beiden Einträge entfernt, erhält das alte
  Verhalten zurück: die Anmeldeseite
  oder die Fehlerseite, die das Skript ebenfalls als fehlgeschlagenes Laden behandelt.
- **Die Session.** Die Ansicht liest die Session nur, daher schließt sie sie (`session_write_close()`), sobald das
  Urteil gefällt ist: Die anderen Anfragen der Seite warten nicht auf das Menü. Sie wartet trotzdem, wie jede Anfrage
  einer
  Session, solange eine andere Anfrage derselben Session deren Sperre hält (der Datei-Handler und Velocity sperren
  die Session für die gesamte Anfrage), sodass eine langsam ladende Seite in einem anderen Tab den Bereich um die Zeit
  dieser Seite verzögert.
- **Aussehen:** eine admin4-Karte (`--a4-card`, `--a4-line`, `--a4-radius-s`, `--a4-shadow-m`), zentriert unter der
  Kopfzeile und so breit, wie ihre Gruppen es brauchen: bis zu drei Gruppen von 17rem nebeneinander, sodass eine kurze
  Auswahl eine schmale Karte ergibt; Beschreibungen umbrechen, statt abgeschnitten zu werden. Hell und dunkel, ein
  CanvasText-Rahmen und Highlight-Zustände bei erzwungenen Farben, kein Übergang
  bei reduzierter Bewegung. Stile in `admin4.css`.
- **admin4 und admin4l gleichermaßen:** Die Tabs werden von einem einzigen Template gezeichnet,
  `parts/a4_topmenu_items.tpl`, das entscheidet, welche Tabs ein Mega-Menü erhalten; `page_topmenu.tpl` von admin4 und
  der Block des Tab-Menüs von admin4l
  (`design/admin4l/templates/explayouts/block/admin_tab_menu.tpl`) binden es beide ein.
- **Ebenen:** Der Bereich ist `z-index: var(--a4-z-menu)` innerhalb der fixierten Kopfzeile (`--a4-z-header`): über
  den Randtabs der Seitenspalten, unter der Maske eines Dialogs und dem Dialog (`--a4-z-overlay`, `--a4-z-modal`) und
  unter Toasts
  (`--a4-z-toast`); die Leiste des Debug-Berichts bleibt über allem.

`tests/tests/design/AdminMegaMenuTest.php` enthält die Standards beider Tabs, das Markup, die Quelle und die Ansicht
(welche Tabs sie bedient, und nur, solange der Benutzer sie hat: verborgen, deaktiviert, nicht angezeigt, nicht
aufgeführt), die Breiten und
die Zeichenketten, den Abschnitt Benutzer (seine hinzugefügten Menüs, vorhandene Links, Symbole, Namen und
Beschreibungen in beiden Sprachen), den Abschnitt Dashboard (seine hinzugefügten Menüs, eine Gruppe, die zwei
hinzufügt, jeder gewählte EI-Link, Symbole, Namen und Beschreibungen in jedem vollständigen Katalog, Links ohne Zugriff
oder einer inaktiven Extension ausgelassen, das breite Layout), rendert das Template des Bereichs mit Pseudo-Gruppen
(die Auswahl, ihre Reihenfolge, Zugriff, ausgelassene Gruppen, die
Grenze von 19, keine Auswahl, ein Schlüssel eines verschobenen Tabs, der auf den Link der Gruppe mit derselben Adresse
zurückfällt) und führt das Skript in node aus (`tests/tests/design/js/mega_menu.js`: Klick, Pfeil nach unten, Escape,
Tab, ein
Klick außerhalb, die Verzögerungen, Berührung, der Fokus, der in eine Vorschau kommt, die Breite, die Liste „Mehr“,
ein Laden und ein
fehlgeschlagenes, zwei Tabs mit jeweils nur einem geöffneten Bereich), prüft, dass admin4 und admin4l die Tabs mit dem
einen Template zeichnen, rendert die Prüfung der Einstellungen mit unbekannten Tabs und Schlüsseln und liest die
Debug-Meldungen, die sie schreibt.

## Sprachen

Eine Flagge steht für ein Land, nicht für eine Sprache: Englisch wird unter mehreren Flaggen gesprochen, und ein Land
hat mehrere Sprachen. admin4 zeigt eine Inhaltssprache daher als kurze Code-Plakette mit ihrem Namen, so wie die
meisten Backoffices Sprachen auflisten:

- **Der Code** ist der Sprachteil des Sprach-Tags der Locale (`ger-DE` hat `de-DE`, also `DE`; `eng-GB` ergibt
  `EN`), immer: Hat die Installation zwei Inhaltssprachen derselben Sprache (`eng-GB` und `eng-US`), zeigen beide
  `EN`. Die Plakette nennt die Sprache, nicht die Region; die beiden unterscheiden sich durch ihren Namen (English
  (United Kingdom), English (American)), der Titel und zugänglicher Name der Plakette ist oder daneben steht. Eine
  Locale ohne Sprach-Tag zeigt ihren Locale-Code. Jede Locale folgt der Regel, auch eine später hinzugefügte
  (`esl-AR`, `es-AR`: `ES`);
  `AdminLanguageBadgeTest` prüft jede in `share/locale/` mitgelieferte Locale-Datei.
- **Zwei Regionen einer Sprache an einem Objekt** (ein Objekt in `eng-GB` und `eng-US`): Es wurde entschieden, auf
  beiden Plaketten `EN` beizubehalten. Ein sehender Benutzer unterscheidet sie am Namen: neben der Plakette im Tab
  Übersetzungen, in der Sprachauswahl der Bearbeitungsseite und im Menü „Bearbeiten in“ sowie als Tooltip (`title`)
  jeder Plakette in der Metazeile der Knotenansicht und in der Tabelle der Unterelemente; ein Screenreader hört den
  vollen Namen mit dem Zustand („English (United
  Kingdom) – veraltet, älter als English (American)“), weil der Name der Zustandsplakette der Name der Inhaltssprache
  ist, nicht der Code. Die Reihenfolge ist die Sprachreihenfolge der Site, sodass die beiden Plaketten ihre Plätze
  behalten. Die Region wird dem Code nicht wieder hinzugefügt: Eine Zeile würde sonst wieder `DE` mit `EN-GB` mischen,
  was #231 entfernt hat.
- **Der Name** (der internationale Name der Locale, z. B. German) steht neben der Plakette, wo immer Platz ist: im Tab
  Übersetzungen, in den Übersetzungslisten von Versionen, Entwürfen, URL-Aliasen, Zuständen und Klassen und in der
  Sprachauswahl der Bearbeitungsseite. Wo kein Platz ist, in der Metazeile der Knotenansicht und in der Spalte
  Übersetzungen der Tabelle der Unterelemente, ist er Titel und zugänglicher Name der Plakette; dort sind die
  Plaketten die Links, um eine Übersetzung anzusehen oder zu
  bearbeiten, und die angezeigte Sprache trägt `aria-current`.
- Wo Name und Locale-Code bereits beide angezeigt werden (die Sprachkarten unter **Setup > Sprachen**, die Legenden
  der Zustandseditoren), ist die Flagge einfach entfallen.

Jede Plakette stammt aus `design:parts/language_badge.tpl` (Parameter `locale`; optional `badge_name`,
`badge_named`, wenn der Name daneben steht, `badge_plain` für den bloßen Code, `badge_class`), gestaltet durch
`.a4-lang` / `.a4-langs` in beiden Kopien von
`components.css`. Der Code stammt aus dem neuen Attribut `content_language_code` von `eZLocale`, dem
`[HTTP] ContentLanguage` der Locale-Datei: Anders als `http_locale_code` wird es nicht von
`site.ini [RegionalSettings] HTTPLocale` überschrieben,
das ein einziges Tag für eine ganze Site setzt. Die optionalen Parameter tragen das Präfix `badge_`, weil ein include
die Variablen des einbindenden Templates sieht: Ein Parameter namens `class` würde die Inhaltsklasse von
`class/view.tpl` lesen, wenn er nicht übergeben wird. Das klassische Admin-Design hat eine identische Kopie des
include und von
`ezajaxsubitems_expdatatable.js`, sodass auch seine Tabelle der Unterelemente den Code zeigt (der Plakettenstil steht
in beiden Kopien von
`components.css`). Die Flaggen in `share/icons/flags/` und der Operator `flag_icon` bleiben für seine anderen Templates
und die anderen Designs. `tests/tests/design/AdminLanguageBadgeTest.php` schlägt bei einer Flagge in einem
admin4-Template oder -Skript fehl, oder in
der Tabelle der Unterelemente eines der beiden Designs.

## Sichtbarkeit und Kontostatus

Ob Besucher der Site ein Element sehen und ob sich ein Benutzer anmelden kann, zeigt sich auf einen Blick als kleine
Statusmarkierung
(`.a4-mark` in beiden Kopien von `components.css`, 10 px). Die Form zeigt den Zustand ebenso wie die Farbe, sodass sie
in Graustufen, für farbenblinde Benutzer und bei erzwungenen Farben lesbar ist (wo die Markierungen die
Systemtextfarbe annehmen):

| Markierung | Form | Farb-Token | Bedeutung |
|---|---|---|---|
| `is-visible` | gefüllte Scheibe | `--a4-ok` | sichtbar |
| `is-hidden` | Ring | `--a4-bad` | versteckt (`is_hidden`) |
| `is-hidden-by-parent` | Ring mit Balken | `--a4-bad` | versteckt, weil ein übergeordnetes Element versteckt ist (`is_invisible` ohne `is_hidden`) |
| `is-disabled` | durchgestrichener Ring | `--a4-muted` | Benutzerkonto deaktiviert (**Setup > Benutzer**, `ezuser_setting.is_enabled`) |
| `is-locked` | gefülltes Quadrat | `--a4-warn` | Benutzerkonto gesperrt nach mehr fehlgeschlagenen Anmeldungen als `site.ini [UserSettings] MaxNumberOfFailedLogin` (`eZUser::isLocked()`) |

Jede Farbe erreicht 3:1 gegenüber der Karte, den Zebra- und Hover-Zeilen und ihrer eigenen Plakettentönung, im hellen
und im dunklen Modus.
Steht die Markierung allein, ist sie `role="img"` mit einem `aria-label` und einem erklärenden `title`; neben ihrem
eigenen Text ist sie `aria-hidden`. Die Markierung eines sichtbaren Elements ist nur zu sehen: Ein Screenreader hört
die Ausnahmen.

Wo sie erscheint:

- **Kopf der Knotenansicht:** eine Plakette neben der Klasse (Sichtbar, Versteckt oder Versteckt durch übergeordneten
  Eintrag, jeweils mit ihrer Markierung und der Erklärung als Titel); bei einem Benutzerobjekt auch „Deaktiviert“ oder
  „Gesperrt“. Die Plakette stammt aus
  `design:parts/visibility_status.tpl` (Parameter `status_node`, `status_badge`, `status_quiet`).
- **Tabelle der Unterelemente:** die Markierung vor jedem Namen (alle drei Zustände, sodass ein verstecktes Element
  zwischen grünen auffällt) und eine Plakette „Deaktiviert“ oder „Gesperrt“ nach dem Namen eines Benutzerkontos. Die
  Spalte Sichtbarkeit zeigt, wenn sie in den
  Tabellenoptionen gewählt ist, die Markierung neben ihrem Text. Die Zeilen von `ezjscnode::subtree` und
  `expsubitems::rows` tragen
  `is_hidden` und `is_invisible` (`ezjscAjaxContent`) und bei Benutzerkonten `user_status`.
- **Inhaltsbaum:** nur das Abweichende, damit der Baum ruhig bleibt: ein Ring oder ein Ring mit Balken nach dem Namen
  eines versteckten Knotens (dekorativ: der Text „(Hidden)“ danach, den admin4 früher mit `display: none` entfernte,
  bleibt jetzt für Screenreader erhalten) und ein durchgestrichener Ring nach einem deaktivierten Benutzerkonto. Eine
  Sperre nach fehlgeschlagenen Anmeldungen wird dort nicht angezeigt: Der
  Baum wird über das `modified_subnode` der Knoten zwischengespeichert, das das Aktivieren und Deaktivieren eines
  Kontos jetzt erneuert
  (`eZUserOperationCollection::setSettings()` und `activation()`), eine fehlgeschlagene Anmeldung aber nicht.
- **Auswahldialog** (Listenansicht): die Markierung vor einem versteckten Knoten.

Der Kontostatus stammt aus `expUserAccountStatus` (`kernel/classes/user/`), das ihn für eine ganze Seite mit
höchstens zwei Abfragen liest (`ezuser_setting`, dann `ezuservisit` für die aktivierten Benutzer darunter), nie mit
einer je Zeile; Templates fragen
`fetch( 'user', 'account_status', hash( 'user_id', ... ) )` ab (`enabled`, `disabled`, `locked` oder false). Er wird
Benutzern mit `role/read` angezeigt, denselben, die die Spalten „Konto aktiviert“ und „Ausgesperrt“ der
Unterelemente sehen dürfen. Tests:
`AdminVisibilityStatusTest` (Formen, Kontrast, Templates, die Skripte über eine Node-Testumgebung, die Texte auf
Deutsch und
amerikanischem Englisch) und `expUserAccountStatusTest` (die gebündelten Lesezugriffe, die Reihenfolge deaktiviert vor
gesperrt, die Zugriffsprüfung).
### Der Zustand einer Sprache

Für ein Objekt zeigt eine Plakette auch den Zustand seiner Sprache, durch ihre Form und nie durch eine Farbe je
Sprache (Grün, Bernstein und Rot gehören zu Sichtbarkeit und Zuständen):

| Zustand | Form | Titel und zugänglicher Name |
|---|---|---|
| Hauptsprache (die Ausgangssprache des Objekts) | in der Textfarbe gefüllt (`.is-main`) | „Deutsch – Hauptsprache“ |
| Übersetzung vorhanden | umrandet (`.is-present`, die einfache Plakette) | „Englisch – Übersetzung vorhanden“ |
| veraltete Übersetzung | umrandet mit einem kleinen Punkt oben rechts (`.is-outdated`) | „Spanisch – veraltet, älter als Deutsch“ |
| fehlend (eine Sprache der Site, die dem Objekt fehlt) | gestrichelter Rand, gedämpfter Text (`.is-missing`) | „Französisch – fehlt“ oder ein Link „In Französisch übersetzen“ |

Wo: Die Metazeile der Knotenansicht zeigt jede Sprache der Site (`prioritized_languages`) sowie jede andere Sprache,
die das Objekt hat; eine vorhandene verlinkt auf die Ansicht in ihr, eine fehlende ist ein Link auf
`content/edit/<object>/f/<locale>`
(der GET-Weg, den das Menü „Bearbeiten in“ nimmt und der das Recht erneut prüft), wenn der Benutzer diese Übersetzung
anlegen darf
(`can_create_languages`), sonst einfacher Text. Der Tab Übersetzungen zeigt den Zustand an jeder Übersetzung und
listet die fehlenden unter der Tabelle auf. Die Spalte Übersetzungen der Tabelle der Unterelemente zeigt Haupt- und
vorhandene Sprachen und, auf einer Site
mit bis zu sechs Sprachen (`expContentLanguageStates::LIST_MISSING_LIMIT`), die fehlenden als Text: Bei mehr würden
die Plaketten einer Zeile umbrechen und die vorhandenen verdecken, und die Spalte Fehlende Übersetzungen der
Tabellenoptionen listet sie ohnehin auf. Ob der Benutzer eine Übersetzung anlegen darf, wird nicht je Zeile
ermittelt, daher hat die Liste keinen Übersetzungslink.

Veraltet: Die neueste veröffentlichte oder archivierte Version, die in einer Sprache bearbeitet wurde
(`ezcontentobject_version.initial_language_id`,
wie content/edit es festhält), ist älter als die neueste in der Hauptsprache bearbeitete. Beide müssen existieren: Ein
Objekt, dessen
Sprachen alle in einer einzigen Version geschrieben wurden (ein Import, eine Kopie) oder dessen ältere Versionen das
Versionslimit entfernt hat, wird
nie markiert. `expContentLanguageStates::lastEdited()` liest beliebig viele Objekte mit einer einzigen gruppierten
Abfrage (SQL; ein
`find` unter MongoDB); die Markierung wird nur für das eine Objekt der Knotenansicht und seinen Tab Übersetzungen
angezeigt, die Listenzeilen erhalten Haupt-, vorhandene und fehlende Sprachen aus der Zeile, die sie bereits haben
(`initial_language` im Knoten-JSON), keine Abfrage
je Zeile.

Die Fetch-Funktion `fetch( 'content', 'language_states', hash( 'object', $object ) )` liefert eine Liste aus `locale`,
`name`, `state`, `outdated` und `can_translate`; `design:parts/language_state_badge.tpl` zeichnet einen Eintrag
(`state_entry`,
`state_main_name`, optional `state_object_id`, `state_href`, `state_current`). Bei erzwungenen Farben werden Füllung
und Punkt in Systemfarben gezeichnet. `tests/tests/design/AdminLanguageStatesTest.php`.

Aufwand, gemessen auf alpha mit einem Ordner von 50 Artikeln: Die Anfrage der Unterelemente (`ezjscnode::subtree`,
50 Zeilen) braucht vor und nach den Zuständen gleich viele Abfragen (357 für 50 Zeilen, 42 für 5, beide Male gleich:
je Zeile kommt nichts hinzu); die Knotenansicht ergänzt die eine gruppierte Abfrage von `lastEdited()`, die der
SQL-Abfrage-Cache bei der
zweiten Verwendung auf derselben Seite beantwortet (Metazeile und Tab Übersetzungen). Da die Fetch-Funktion zu einer
Kernel-Klasse gehört, darf ein
Template, das sie verwendet, nicht live gehen, bevor die Worker die Klasse kennen: Deployen Sie mit
`exp:velocity deploy
--kernel`; ein kompiliertes `fetch()` einer Klasse, die ein Worker nicht laden kann, rendert nichts statt eines
fatalen Fehlers.

## Kontextmenüs

Die Menüs des Inhaltsbaums, der Tabelle der Unterelemente, des Klassensymbols und der Lesezeichen (`ezpopupmenu.js`
mit den Templates in `templates/popupmenu/`) sehen aus wie die anderen Popover von admin4 und funktionieren mit der
Tastatur.

- **Aussehen:** Kartenfarbe, Rand, Radius und Schatten der Admin-Tokens im hellen und im dunklen Modus; Text in der
  Größe des Fließtexts (14 px) in kompakten Zeilen von etwa 27 px (4 px oben und unten, Zeilenhöhe 1,35, mindestens
  24 px, die Zielgröße von WCAG 2.2; Trenner im Abstand von 2 px); das Menü so breit wie sein längster Eintrag
  (13,5 bis 22 rem) statt fester 14em, bei denen lange Einträge umbrachen, ein noch längerer Eintrag mit
  Auslassungspunkten gekürzt; der Knoten- oder Klassenname als zurückhaltende Titelzeile; die überfahrene, fokussierte
  und geöffnete
  Zeile in der Akzenttönung; deaktivierte Einträge in der gedämpften Textfarbe. Im Windows-Hochkontrastmodus behalten
  die Zeilen einen sichtbaren Umriss.
- **Tastatur:** Die Menüschaltfläche des Baums (das Klassensymbol vor einem Knoten) und die Zeilenschaltfläche der
  Tabelle der Unterelemente öffnen das Menü mit Enter oder Leertaste; Umschalt+F10 oder die Kontextmenütaste öffnen
  das Menü des fokussierten Baumelements oder der fokussierten Tabellenzeile (in einem Formularfeld der Zeile bleibt
  das eigene Menü des Browsers); auch auf einer gescrollten Seite öffnet sich ein per Tastatur geöffnetes Menü unter
  seiner Schaltfläche. Der erste Eintrag erhält den Fokus. Pfeil nach unten und oben, Pos1 und Ende wechseln zwischen
  den Einträgen, Pfeil nach rechts,
  Enter oder Leertaste öffnen ein Untermenü, Pfeil nach links schließt es, Enter oder Leertaste führen einen Eintrag
  aus, Escape schließt das Menü und gibt den Fokus an seine Schaltfläche zurück, Tab schließt das Menü und geht weiter.
- **Screenreader:** `ezpopupmenu.js` gibt jedem Menü `role="menu"`, beschriftet durch seine Titelzeile, seinen
  Einträgen
  `role="menuitem"` (`aria-disabled`, wenn deaktiviert, `aria-haspopup`/`aria-expanded` für ein Untermenü) und seinen
  Linien `role="separator"`, wenn es das Menü anzeigt, sodass auch Menüs, die Extensions über
  `admininterface.ini` hinzufügen, sie erhalten.
  Die Schaltfläche des Menüs trägt `aria-haspopup="menu"` und `aria-expanded`; die Schaltfläche des Baums ist nach
  ihrem Knoten benannt.
- Die Funktionen, die Extensions aufrufen (`ezpopmenu_showTopLevel`, `ezpopmenu_showSubLevel`, `ezpopmenu_mouseOver`,
  `ezpopmenu_submitForm`, `ezpopmenu_hideAll` ...), sind unverändert. `design/admin` behält eine identische Kopie des
  Menüskripts sowie der beiden Skripte und des Templates, die es öffnen; ändern Sie beide Kopien gemeinsam.

## Landmarks und Namen

Der Rahmen von admin4 teilt Screenreadern und Tastaturen mit, wie die Seite aufgebaut ist:

- **Sprunglink.** Der erste Halt der Tab-Taste ist „Zum Hauptinhalt springen“, verborgen, bis er den Fokus hat. Er
  springt zu
  `#maincontent`, das den Fokus übernimmt (`tabindex="-1"`), oder zu `#a4-main` (siehe unten).
- **Landmarks.** Die Kopfzeile ist das Banner, das obere Menü (`#a4-navbar-menu`), das Seitenmenü (`#leftmenu`) und die
  Brotkrümelnavigation (`#path`) sind benannte Navigations-Landmarks, die rechte Spalte (`#rightmenu`) ist eine
  ergänzende Landmark,
  die Fußzeile die Inhaltsinformation und `#maincontent` die Haupt-Landmark. Die Seiten vom Bearbeitungstyp
  (Inhaltsbearbeitung, Verlauf,
  Versionsansicht, Upload ...) bringen ihr eigenes `#maincontent` und ihre eigene Seitenspalte mit; diese tragen
  dieselben Rollen. Eine Seite
  ohne linke Spalte, deren Template kein `#maincontent` mitbringt (Entfernen, Kopieren, Verschieben ...), wird von
  `pagelayout.tpl` in `#a4-main` gehüllt, eine Haupt-Landmark ohne Stile, sodass sich ihr Layout nicht ändert. Die
  Rollen sitzen auf
  den vorhandenen Elementen, sodass jeder Selektor der Stylesheets und Extensions weiterhin passt.
- **Namen und Zustand.** Die Brotkrümelnavigation und das obere Menü markieren die aktuelle Seite
  (`aria-current="page"`), ein deaktivierter Eintrag des oberen
  Menüs ist `aria-disabled` und nicht in der Tab-Reihenfolge, die Links des Logos und „Website öffnen“ haben einen
  Namen, die
  Menüschaltfläche in Telefonbreite ist für Tastaturen und Screenreader eine Schaltfläche (Enter und Leertaste,
  `aria-expanded`), und
  Auswahllisten, Kontrollkästchen und Optionsfelder, die nur ein `title` oder ihre Tabellenposition hatten, haben einen
  Namen (die Sprache oder der
  Ort der Zeile, wo eine Liste sie wiederholt). Der doppelte Bildlink vor dem Namen des aktuellen Benutzers ist vor
  beiden verborgen.
- **Tabellen.** Jede Kopfzelle der Templates von admin4 hat `scope="col"` (ihre Tabellen haben nur Kopfzeilen).
- **Formularfelder.** Jedes Feld hat einen Namen: seine sichtbare Beschriftung (`<label for>` oder ein
  `<label class="a4-inline-label">`
  um den Text neben einem Optionsfeld oder Kontrollkästchen), den Spaltenkopf und den Namen der Zeile in einer Liste,
  die Kartenüberschrift in
  den Assistenten unter **Setup > RAD** und ein `aria-label` nur dort, wo nichts angezeigt wird. Gruppen von
  Optionsfeldern sind benannte Gruppen (Versionsvorschau, Template-Erstellung, Paketexport).
  `tests/tests/design/AdminFormLabelsTest.php` prüft jedes
  `input`, `select` und `textarea` der Templates und Überschreibungen von admin4.

`tests/tests/design/AdminLandmarksTest.php` prüft den Sprunglink, die Landmarks, eine Haupt-Landmark je
`#maincontent`, die Hülle `#a4-main`, die Scopes, die Rahmentitel und die benannten Bedienelemente in den Templates von
admin4 und seinen Überschreibungen.

## Tastatur

- **Knoten-Tabs** (Ansicht, Details, Übersetzungen ... und die Tabs, die Extensions hinzufügen) folgen dem
  WAI-ARIA-Tabs-Muster:
  `node_tabs.js` gibt der Liste `role="tablist"`, jedem Link `role="tab"` mit `aria-selected` und `aria-controls`,
  jedem Bereich `role="tabpanel"`. Nur der ausgewählte Tab ist in der Tab-Reihenfolge; Pfeil nach links und rechts
  (die Liste ist horizontal, daher bleiben Pfeil nach oben und unten bei der Seite), Pos1 und Ende wechseln zu
  einem anderen Tab und öffnen ihn, wie es ein Klick tut; ist kein Tab ausgewählt, ist der erste der Tab-Halt. Ein
  Klick auf ein
  Element innerhalb des Links eines Tabs öffnet den Tab ebenfalls. Solange die Tabs ausgeschaltet sind (jeder Bereich
  angezeigt), werden die Rollen
  entfernt.
- **Tabs ein- und ausblenden** ist eine Schaltfläche mit einem einzigen Namen, „Tabs anzeigen“, und `aria-pressed`
  (die Tabs werden angezeigt oder nicht); ihr Titel sagt, was ein Druck bewirkt. Die Leertaste drückt sie wie Enter.
- **Der Inhaltsbaum** ist ein Baum (das WAI-ARIA-Muster „Tree View“, wie in seinem Beispiel eines Navigationsbaums):
  Die Liste hat
  `role="tree"` und den Namen „Inhaltsbaum“, der Namenslink jedes Knotens ist ein `treeitem` mit `aria-level`,
  `aria-posinset`, `aria-setsize` und, bei einem Knoten mit Unterelementen, `aria-expanded` (`aria-busy`, während sie
  laden);
  die Liste der Unterelemente ist eine `group`, die dem Element gehört (`aria-owns`), das Element des aktuellen Knotens
  hat
  `aria-current="page"`. Ein Element ist in der Tab-Reihenfolge (das des aktuellen Knotens, sobald es angezeigt wird,
  sonst das erste; danach das
  zuletzt fokussierte). Tasten: Pfeil nach oben und unten bewegen durch die angezeigten Elemente; Pfeil nach rechts
  öffnet einen Knoten (lädt seine Elemente) oder springt
  zu seinem ersten Unterelement; Pfeil nach links schließt ihn oder springt zu seinem übergeordneten Element; Pos1 und
  Ende gehen zum ersten und letzten Element; ein Buchstabe
  springt zum nächsten Element, das mit ihm beginnt (innerhalb einer halben Sekunde getippte Buchstaben bilden eine
  Suche); Enter folgt
  dem Link; Umschalt+F10 oder die Kontextmenütaste öffnen das eigene Menü des Knotens (jedes Baumelement sagt das in
  `aria-keyshortcuts`). Eine Taste, die dort nichts bewirkt (Pfeil nach rechts bei einem
  Element ohne Unterelemente), behält ihr Standardverhalten. Die Schaltfläche zum Öffnen und Schließen vor einem Knoten
  („Elemente unter <node>“,
  `aria-expanded`) und das Knotensymbol bleiben für die Maus, sind aber nicht in der Tab-Reihenfolge und vor
  Screenreadern verborgen,
  da das Element ihren Zustand trägt; ein Fokus, den die Tastatur oder ein sich schließendes Menü ihnen gibt, geht an
  das Element (ein Klick auf
  das Knotensymbol hält ihn dort, damit die Tasten des Menüs funktionieren). Ein Knoten, dessen Elemente nicht geladen
  werden können, nennt den Grund über
  `aria-describedby`; ein Knoten, dessen Unterelemente alle verborgen sind (`hideNodes`), hat kein `aria-expanded`. Ein
  mit der Maus geschlossener Knoten übernimmt den Tab-Halt, wenn dieser auf einem seiner Unterelemente lag. Skript und
  Einstellungs-Template des Baums sind
  dieselbe Datei in `design/admin` und `design/admin4`; der Baum von `show_content_structure.tpl` (kein dynamisches
  Menü) ist
  unverändert.
- **Datumsfelder:** Der Kalender öffnet sich über eine Schaltfläche (`aria-haspopup="dialog"`), die nach ihrem Feld
  benannt ist („Datum für Veröffentlichungsdatum auswählen“); `exp::datepicker` gibt ihr den Fokus zurück,
  wenn sich der Kalender schließt. Die klassischen Designs behalten ihr Kalenderbild.

`tests/tests/design/AdminKeyboardTest.php` enthält das Markup und die Ansatzpunkte, auf die sich die Skripte
verlassen, dass die Dateien des Baums in beiden Designs gleich sind und dass kein admin4-Template ein anklickbares Bild
hat. `tests/tests/design/AdminTreeViewTest.php` enthält die
Rollen, Zustände, den Tab-Halt und die Tasten des Baums.

## Unterelemente per Drag and Drop neu anordnen

Wo ein Knoten seine Kinder nach Priorität sortiert und der Benutzer die Prioritäten ändern darf (`$node.can_edit`:
dieselbe Bedingung wie bei den klassischen Prioritätsfeldern), hat die Tabelle der Unterelemente vor den Namen eine
Spalte mit Griffen. Sie erscheint, solange
die Tabelle so sortiert ist wie der Knoten (nach Priorität, in der Richtung des Knotens), ohne Namensfilter und mit
mehr als einer Zeile; nach einer anderen Spalte sortiert ist die Spalte verborgen.
`design/admin4/javascript/subitems_priority_drag.js` liefert sie;
das Tabellenskript (`ezajaxsubitems_expdatatable.js`, dieselbe Datei in `design/admin`) fügt die Spalte nur hinzu,
wenn das Template `confObj.priorityDrag` setzt, sodass die anderen Admin-Designs unverändert bleiben.

- **Zeiger** (Maus, Berührung, Stift; Pointer-Events, kein HTML5-Drag-and-Drop, keine Bibliothek): Ziehen Sie den
  Griff; die Zeile ist ihr eigener Platzhalter (gestrichelt, getönt), ein Geisterbild mit dem Namen folgt dem Zeiger,
  und das Fenster scrollt, wenn der Zeiger sich seinem oberen oder unteren Rand nähert. Escape oder eine abgebrochene
  Berührung setzt die Zeile zurück.
- **Tastatur**: Der Griff ist eine Schaltfläche namens „<name> verschieben“ mit der Anleitung als Beschreibung.
  Leertaste oder Enter nehmen die Zeile auf (`aria-pressed`), die Pfeiltasten bewegen sie, Pos1 und Ende an den ersten
  und letzten Platz, Leertaste oder Enter legen sie ab, Escape oder Tab brechen ab. Alt+Pfeil nach oben und Alt+Pfeil
  nach unten verschieben sie sofort um einen Platz. Jeder Schritt wird über den Statusbereich der Tabelle angekündigt.
- **Das Speichern** erfolgt direkt nach dem Ablegen, über `ezjscnode::updatepriority`. Nur die aktuelle Seite wird
  neu angeordnet: Ihre eigenen Prioritäten werden in der neuen Reihenfolge erneut an ihre Zeilen vergeben, sodass
  nichts auf eine andere Seite wandert. Hat die Seite
  gleiche Prioritäten oder teilt sie eine mit der letzten Zeile der vorherigen oder der ersten der nächsten Seite,
  erhalten die Zeilen verschiedene Werte strikt zwischen diesen beiden Nachbarn (Schritte von 10, wo Platz ist,
  kleinere Schritte, wo
  nicht; die erste Seite darf unter 0 gehen, die letzte Seite über die vorherige). Lassen die Nachbarn keinen Platz
  (gleiche Prioritäten über eine Seitengrenze hinweg), nummeriert der Server alle Kinder neu in Schritten von 10, die
  Zeilen der Seite an ihrem
  Platz. Die Prioritäten, die die Tabelle zeigte, und die der Nachbarn werden mitgesendet; hat sich zwischenzeitlich
  eine geändert, wird nichts
  geschrieben, die Tabelle lädt neu und ein Toast meldet das. „Reihenfolge gespeichert“ bietet 10 Sekunden lang
  Rückgängig an; das sendet die alten
  Prioritäten, auf dieselbe Weise geprüft. Ein fehlgeschlagenes Speichern setzt die Zeilen zurück und zeigt den Fehler.
- **Die Serverseite** (`ezjscnode::updatepriority`): Die Listen werden jeweils als ein Text übertragen
  (`PriorityList`,
  `ExpectedPriorityList`, „node_id:priority,...“), sodass eine Seite mit 500 Zeilen weit unter `max_input_vars`
  bleibt; Prüfung
  und Schreiben laufen in einer Transaktion (Zeilen unter MySQL, PostgreSQL und Oracle gesperrt). Jeder zu schreibende
  Knoten muss
  mit der Priorität kommen, die die Tabelle zeigte, und die Listen werden für einen Knoten abgelehnt, der nicht nach
  Priorität sortiert oder den
  der Benutzer nicht bearbeiten darf (die Regel des klassischen Formulars); das Schreiben läuft über die Operation
  `content/updatepriority` des Kernels, sodass ihre Workflows, das Leeren der Caches und die Aktualisierung des
  Suchindex laufen. Listen, die nach Priorität sortiert sind, ordnen gleiche
  Prioritäten nach Knoten-ID, der Reihenfolge, die die Neunummerierung übernimmt. Während eine neue Reihenfolge
  gespeichert wird, warten Seitennavigation, Sortierung
  und Filter (`inert`); ein Speichern, das endet, nachdem die Tabelle andere Zeilen geladen hat, zeichnet nicht über
  sie.
- **Grenzen**: Eine Zeile bewegt sich nur innerhalb ihrer Seite (um sie auf eine andere Seite zu verschieben, ändern
  Sie die Zeilen je Seite oder tippen Sie ihre
  Priorität ein); eine Seite mit einer Zeile hat keinen Griff; eine Taste, die eine Zeile verschiebt, während die
  vorherige Verschiebung noch gespeichert wird, wird mit „Bitte warten, bis die Reihenfolge gespeichert ist.“
  abgewiesen.
- **Die Prioritätszellen bleiben** und können weiterhin von Hand bearbeitet werden; nach einer Verschiebung zeigen sie
  die neuen Werte. Eine Verschiebung
  beginnt nicht, solange eine Priorität mit geändertem Wert bearbeitet wird (ein Toast bittet, sie zuerst zu speichern
  oder zu verwerfen) oder solange
  sie gespeichert wird, und keine Zelle kann bearbeitet werden, während eine neue Reihenfolge gespeichert wird.
- Hell und dunkel, erzwungene Farben (der Umriss des Platzhalters und der gedrückte Griff in Highlight), reduzierte
  Bewegung (kein
  Kippen des Geisterbilds, kein Ausblenden), Druck (keine Griffe). Stile: `modules/content.css` (`.a4-content-drag-*`)
  und
  `.a4-toast-action` in `components.css`. Tests: `AdminSubitemsPriorityDragTest`,
  `ezjscServerFunctionsNodePriorityTest`.

## Das Formular zur Inhaltsbearbeitung

`content/edit` (auch beim Übersetzen), die Mehrfachbearbeitung und `user/register` zeichnen jedes Attribut über
`design:content/edit_attribute.tpl`. In admin4 ist jedes Attribut eine Karte (`div.block.a4-content-attr`): Ihr Rand
zeigt, wo die Bedienelemente eines Datentyps enden und die nächsten beginnen, eine Rich-Text-Werkzeugleiste
eingeschlossen, und ihre Ecke nennt die Art der
Eingabe mit einem Symbol und einem Wort (`.a4-content-attr-kind`, „Text“, „Formatierter Text“, „Datum“, „Verknüpfung“
...). Das Wort ist ein
visueller Hinweis (`aria-hidden`); das Bedienelement teilt einem Screenreader seine Art selbst mit. Auf einem Telefon
steht es über dem Namen.

- **Kurze Eingaben teilen sich eine Zeile:** Zwei benachbarte Karten, deren Datentypen in `CompactDatatypes[]` stehen
  (eine Zahl, ein
  Datum, ein Kontrollkästchen ...), stehen ab 64em nebeneinander; eine dritte belegt wieder die ganze Zeile. Eine
  Übersetzung behält eine Karte
  je Zeile, da neben jedem Feld das Original steht.
- **Die Felder auf einen Blick:** Ab `FieldNavigationFrom` Attributen (standardmäßig 6, 0 schaltet es ab) nennt eine
  Reihe von Links
  über dem Formular jedes Feld mit dem Symbol seiner Art, ein Pflichtfeld mit seiner Markierung, ein Feld mit einem
  Fehler in der
  Fehlerfarbe. Ein Link auf ein Feld in einer eingeklappten Attributgruppe öffnet die Gruppe
  (`javascript/page/content_edit_attribute.js`); die Links der Fehlerzusammenfassung tun dasselbe.
- **Attributgruppen** (`content.ini [ClassAttributeSettings] CategoryList`, die Kategorie eines Klassenattributs) sind
  eine Überschriftenzeile über ihren Karten; eingeklappt nur die Zeile.

Die Arten sind Einstellungen, `admininterface.ini [ContentEditAttributeSettings]`: `DatatypeKind[<datatype>]=<kind>`
ordnet einem
Datentyp eine Art zu, `KindName[<kind>]` ist ihr englisches Wort (übersetzt im Kontext
`design/admin/content/edit_attribute`), `KindIcon[<kind>]` ein Name aus Bootstrap Icons, gezeichnet als
`.a4-bi-<name>`, und ein
nicht zugeordneter Datentyp erhält `DefaultKind` („Sonstige“, das Puzzleteil). Die Datentypen des Kernels und die der
Extensions, die `composer.json` voraussetzt, sind zugeordnet. Eine Extension mit einem eigenen Datentyp ergänzt
`DatatypeKind[<its datatype>]` (und für eine eigene Art `KindName[]`, `KindIcon[]` und die Klasse des Symbols, siehe
[Symbole](#symbole)) in ihrer `settings/admininterface.ini.append.php` und nennt eine kurze Eingabe in
`CompactDatatypes[]`.
`tests/tests/design/AdminContentEditAttributeKindsTest.php` schlägt fehl bei einem Datentyp des Kernels ohne Art,
einer Art ohne
Namen oder Symbol, einem Symbol, das nicht mitgeliefert oder aufgeführt ist, und einem Namen, der im deutschen oder
englischen Katalog fehlt.

Die Richtlinien einer Rolle (`role/view`, `role/edit`) und die Rollenkarten von `role/list` sind wie die
Datentabellen gestreift
(`--a4-zebra`), sodass sich eine lange Liste Zeile für Zeile lesen lässt.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/admininterface.ini` | `ContentEditAttributeSettings` | `DatatypeKind[]`, `KindName[]`, `KindIcon[]`, `DefaultKind`, `CompactDatatypes[]`, `FieldNavigationFrom` | die Datentypen des Kernels und der vorausgesetzten Extensions, `other`, 15 kurze Datentypen, `6` (siehe [Das Formular zur Inhaltsbearbeitung](#das-formular-zur-inhaltsbearbeitung)) | Siteaccess |
| `settings/design.ini` | `AdminDesignSettings` | `ShowCommunityLinks` | `disabled` | Siteaccess |
| `settings/design.ini` | `StylesheetSettings` | `AdminComponentCSSFileList[]` | `components.css` und die fünf `modules/*.css` | Siteaccess |
| `settings/menu.ini` | `MenuAccessSettings` | `CheckViewAccess` | `enabled` | Siteaccess |
| `settings/menu.ini` | `MenuAccessSettings` | `NoAccessLinks` | `hidden` | Siteaccess |
| `settings/menu.ini` | `TopAdminMenu` | `HiddenTabs[]` | leer | Siteaccess |
| `settings/menu.ini` | `TopAdminMenu` | `SetupTabs[]` | leer (siehe [Einen Tab nach Setup verschieben](#einen-tab-nach-setup-verschieben)) | Siteaccess |
| `settings/menu.ini` | `TopAdminMenu` | `MegaMenuTabs[]` | leer; `setup`, `users` und `dashboard` haben ein Template (siehe [Mega-Menü](#mega-menü-prototyp-opt-in)) | Siteaccess |
| `settings/menu.ini` | `Topmenu_<tab>` | `MegaMenuTemplate` | `design:parts/setup/mega_menu.tpl` (setup), `design:parts/user/mega_menu.tpl` (users), `design:parts/my/mega_menu.tpl` (dashboard) | Siteaccess |
| `settings/menu.ini` | `Leftmenu_user_mega` | `MegaMenuLinks[]` | acht Links des Tabs Benutzer (siehe [Mega-Menü](#mega-menü-prototyp-opt-in)) | Siteaccess |
| `settings/menu.ini` | `Topmenu_<tab>` | `MegaMenuLayout` | nicht gesetzt (bis zu drei Gruppen nebeneinander); `wide` für dashboard (bis zu vier ausgeglichene Spalten) | Siteaccess |
| `settings/menu.ini` | `Leftmenu_my_mega` | `MegaMenuLinks[]` | siebzehn Links des Tabs Dashboard, darunter jeder Link von Exponential Intelligence (siehe [Mega-Menü](#mega-menü-prototyp-opt-in)) | Siteaccess |
| `settings/menu.ini` | `Leftmenu_setup` | `MegaMenuLinks[]` | neunzehn häufige Links, höchstens 19 angezeigt (siehe [Mega-Menü](#mega-menü-prototyp-opt-in)) | Siteaccess |
| `settings/menu.ini` | `Topmenu_<tab>` | `Name`, `Tooltip`, `TranslationContext` | die Standardnamen der Tabs, Kontext `design/admin/pagelayout` | Siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `Groups[]`, `GroupNames[<group>]`, `GroupLinks_<group>[]` | die neun Gruppen von `Leftmenu_setup` (Design und Audit für verschobene Tabs, KI und Updates für Links von Extensions) | Siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `LeadLinks[]` | `overview` in `Leftmenu_setup` | Siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `TrailLinks[]` | `change_password`, `edit_profile` in `Leftmenu_my` | Siteaccess |
| `settings/menu.ini` | `Leftmenu_setup` | `GroupTabs[<group>]`, `GroupSections[<group>]` | `design` und `audit` (siehe [Einen Tab nach Setup verschieben](#einen-tab-nach-setup-verschieben)) | Siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `LinkIcons[<link>]`, `LinkDescriptions[<link>]`, `LinkFigureTemplates[<link>]` | die Setup-Links des Kerns | Siteaccess |
| `settings/design.ini` | `AdminDesignSettings` | `IconList[]` | jedes `.a4-bi-*`-Symbol aus `components.css` | Siteaccess |
| `settings/menu.ini` | `Topmenu_setup` | `URL[default]` | `setup/menu` (die Setup-Übersicht) | Siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `PolicyList_<link>[]` | eine Knoten-ID, `<module>/<function>` oder `<module>/*` für ein Modul ohne Richtlinienfunktionen | Siteaccess |
| `settings/site.ini` | `SiteAccessSettings` | `FrontSiteAccess` | leer (`DefaultAccess`) | Siteaccess |
| `settings/site.ini` | `AdminEnvironment` | `Level`, `Name`, `ShortName`, `Release`, `ShowVersion`, `ShowInTitle`, `BadgeDisplay` | leer (keine Plakette), leer, leer, leer, `disabled`, `enabled`, `hover` | Siteaccess |

`ShowCommunityLinks=enabled` zeigt die Fußzeile „Evaluate Exponential / Become a member of the Exponential
Community“. Ein Template kann auch `$show_community_links` setzen.

- `AdminComponentCSSFileList[]`: die gemeinsamen `.a4-*`-Komponenten und die Modul-Stylesheets, geladen nach den
  eigenen Stylesheets des Designs und vor `BackendCSSFileList`
  ([Die Administrationsoberfläche gestalten](../../../eng-us/bc/6.0/admin4-styling.md)).
- `CheckViewAccess=enabled` zeigt einen oberen Tab oder einen Link des linken Menüs nur einem Benutzer, der dessen
  Adresse öffnen kann, geprüft wie die Anfrage selbst; `NoAccessLinks=hidden` lässt einen solchen Link weg
  (`disabled` zeigt seinen Namen ohne Link).
- `HiddenTabs[]` lässt obere Tabs weg, unabhängig davon, was `Tabs[]` sagt, auch Tabs, die eine Extension anhängt.
  Design und Audit erscheinen dann im Setup-Menü, und ohne den Tab Dashboard führt das Logo zum Dashboard (siehe
  [Einen Tab nach Setup verschieben](#einen-tab-nach-setup-verschieben)).
- `Name`, `Tooltip` und `TranslationContext` eines `[Topmenu_<tab>]` geben Beschriftung und Tooltip des Tabs auf
  Englisch und den Übersetzungskontext an, in dem sie nachgeschlagen werden.
- `Groups[]`, `GroupNames[<group>]` und `GroupLinks_<group>[]` gruppieren ein langes linkes Menü (siehe „Linkes Menü“
  oben).
- `LeadLinks[]` nennt Links eines gruppierten linken Menüs, die vor den Gruppen und in keiner von ihnen stehen (die
  Setup-Übersicht); `TrailLinks[]` nennt Links, die nach allen anderen gezeichnet werden, auch nach den Links, die
  Extensions anhängen, und nach den
  Gruppen (Passwort ändern und Profil bearbeiten im Dashboard-Menü), in jedem Design; `URL[default]` von
  `[Topmenu_setup]` ist die Seite, die der Tab Setup öffnet (siehe [Setup-Übersicht](#setup-übersicht)).
- `PolicyList_<link>[]` nennt eine Knoten-ID, die lesbar sein muss, eine Richtlinienfunktion oder `<module>/*` für die
  Module, die keine Richtlinienfunktionen haben (visual, class, role, workflow, trigger, url, search, settings,
  collaboration);
  `tests/tests/design/AdminMenuPolicyListTest.php` schlägt bei einer Funktion fehl, die ein Modul nicht hat. Der Tab
  Design öffnet
  den obersten Design-Knoten über seine ID (`content/view/full/58`, `content.ini [NodeSettings] DesignRootNode`).
- `FrontSiteAccess` ist die Site, zu der ein Admin-Siteaccess gehört: Seine Links „Website öffnen“ und „Auf der
  Website ansehen“ führen dorthin.
  Beide zeigen den Globus (Bootstrap Icons `globe2`, `.a4-bi-globe2`), sodass ein Link zur Site nicht wie ein Link
  aussieht, der die Administrationsoberfläche für etwas anderes verlässt (`box-arrow-up-right`).
- `[AdminSiteAccessMenu]`: das Site-Menü (siehe „Site-Menü“ oben), gelesen aus den Einstellungen des
  Admin-Siteaccess.
  `Show=disabled` schaltet es ab. `List=related` (der Standard) listet `[SiteAccessSettings] RelatedSiteAccessList[]`
  des Admin-Siteaccess auf, die Siteaccesses, die seine Datenbank teilen, oder nur den Admin-Siteaccess, wo sie leer
  ist (es fällt
  nie auf jeden Siteaccess der Installation zurück);
  `List=available` listet `AvailableSiteAccessList[]` auf; ein Siteaccess, der nicht in `AvailableSiteAccessList[]`
  steht, wird nie
  aufgeführt; eine Installation, die mehrere Sites mit getrennten Datenbanken bedient, setzt
  `RelatedSiteAccessList[]` für jede
  Administrationsoberfläche, sodass das Menü die Siteaccesses ihrer Site auflistet. `HiddenSiteAccessList[]` lässt
  Siteaccesses weg (die Site, die der
  Globus öffnet, bleibt der erste Eintrag). `AdminSiteAccessList[]` nennt die Administrationsoberflächen; ist sie leer,
  ist ein Siteaccess, dessen `SiteDesign` mit `admin` beginnt oder der `FrontSiteAccess` setzt, eine solche.
  `ListAdministrations`
  (standardmäßig `disabled`) listet sie bei `enabled` als eigene Gruppe auf. Außer der Site, die der Globus öffnet,
  werden nur
  die Siteaccesses aufgeführt, bei denen sich der angemeldete Benutzer anmelden darf
  (`eZUser::canLoginToSiteAccess()`, wie es die Anmeldung
  prüft). Die cache-blocks der Kopfzeile sind über `siteaccess_menu_key()` (die Liste, `ListAdministrations` und
  die Anfrage) und den Rollen-Hash des Benutzers verschlüsselt, sodass eine geänderte Liste bei der nächsten Anfrage
  erscheint; ein
  geändertes `SiteName`, `Locale` oder `SiteDesign` eines aufgeführten Siteaccess erscheint, sobald die
  Template-Caches des
  Administrations-Siteaccess geleert sind.
  Die Template-Operatoren sind `siteaccess_menu( $current_user )` und `siteaccess_menu_key()`.
- `[AdminEnvironment]`: die Umgebungsplakette (siehe „Umgebungsplakette“ oben). `Level` ist `development`, `test`,
  `staging` oder `production` oder `auto` für die Stufe von `EXP_ENV` (`dev`, `development`; `test`; `stage`,
  `staging`;
  `prod`, `production`, `live`; ein anderer Name oder keiner: keine Plakette). Ein leeres `Name` übernimmt den
  übersetzten Namen der Stufe.
  `BadgeDisplay` ist `hover` (der Standard: nur angezeigt, solange ein feiner Zeiger auf dem Logo ruht oder das Logo den
  Tastaturfokus hat) oder `always`; das Titelpräfix (`ShowInTitle`) hängt nicht davon ab. `BadgeDisplay`,
  `ShowInTitle`
  und `ShowVersion` werden wie `Level` gelesen: Groß- und Kleinschreibung sowie umgebende Leerzeichen spielen keine
  Rolle.
  Setzen Sie es je Umgebung, zum Beispiel in `settings/override/site.ini.prod.append.php`
  ([Einstellungen je Umgebung](umgebungseinstellungen.md)); die Einstellung wird bei jeder Anfrage gelesen. Die
  cache-blocks der Kopfzeile und der
  Anmeldeseite sind über die Plakette verschlüsselt, sodass eine geänderte Einstellung bei der nächsten Anfrage
  erscheint, ohne den Template-Cache zu leeren; wo `EZP_INI_FILEMTIME_CHECK` abgeschaltet ist, leeren Sie den
  INI-Cache
  (`php bin/php/ezcache.php --clear-id=global_ini`). `Release` und die Version werden nur angemeldeten Benutzern
  angezeigt.
  Der Template-Operator ist `exp_environment_badge()`.
- Der Name des Logos in der Kopfzeile trägt das Release nur für angemeldete Benutzer; die anonymen Seiten der
  Administrationsoberfläche
  (die 401-Seite, `user/register`, `user/forgotpassword`) sagen „Exponential“.

## Für Entwickler

- **Gestaltungsregeln: [Die Administrationsoberfläche gestalten](../../../eng-us/bc/6.0/admin4-styling.md).** Kein
  `<style>`-Block und kein `style=""`-Attribut
  in einem Template; die gemeinsamen `.a4-*`-Komponenten und `--a4-*`-Tokens (hell und dunkel) aus
  `stylesheets/components.css`; Modul-CSS in `stylesheets/modules/`, geladen über `design.ini`
  `[StylesheetSettings] AdminComponentCSSFileList`; geprüft durch `tests/tests/design/AdminStylingRulesTest.php`.
- `design/admin4/stylesheets/admin4.css` wird nach den Basis-Stylesheets und vor denen der Extensions geladen. Es
  verwendet
  die eigenen Selektoren der Basisregeln, sodass sich das, was eine Basisregel zuvor überstimmte, weiterhin
  durchsetzt. Gemeinsame Standardwerte haben überhaupt kein Gewicht
  (`:where`), sodass die Regeln der Extensions gewinnen. Es gibt keine Cascade Layers.
- Die Basis-Stylesheets, die admin4 von admin3 übernommen hat (`core.css`, `content.css`, `pagelayout.css`,
  `theme/rounded.css`,
  `theme/admin_datatable.css`, `theme/modalwindow.css`), beziehen ebenfalls jede Farbe aus den Tokens (seit
  14.10.2026,
  `AdminStylingRulesTest::testBaseStylesheetsUseOnlyColourTokens`): Links sind `--a4-accent-text`, die
  Diff-Markierungen
  `--a4-ok`/`--a4-bad` auf der Warntönung. admin4 hat kein `responsive.css` mehr (seine einzige aktive Regel steht in
  `pagelayout.css`) und kein `setup-extensions.css` (kein Kernel-Template lud es; exp_adminui bringt eine eigene Kopie
  mit).
- admin4 behält eine eigene Kopie von `ezajaxsubitems_expdatatable.js`: Es muss ohne jedes andere Admin-Design
  funktionieren, und
  eine einzige Kopie in `design/standard` würde von jedem Design verdeckt, das eine eigene hat. Ändern Sie beide Kopien
  gemeinsam.
- Der CSS-Packer entfernte früher das Leerzeichen vor einem Doppelpunkt in Selektoren (`a :hover` wurde zu
  `a:hover`). Behoben.
- Die Widgets von expui (Dialoge, die Upload-Schaltfläche, die Datumsauswahl) übernehmen die Tokens von admin4 über
  eine Brücke in `admin4.css`:
  `--exp-accent` ist `--a4-accent-strong`, `--exp-accent-dark` ist `--a4-accent-press` (dunkler Modus:
  `--a4-accent-strong`), `--exp-accent-ring` ist `--a4-accent-soft`, sodass weißer Text auf der primären Schaltfläche
  5,2:1 behält;
  ihre Fokusringe werden durch den 3-px-`--a4-ring` von admin4 überschrieben.
- admin4 lädt `theme/rounded.css` über den Namen; die Einstellung `admin_theme` älterer Designs wird nicht gelesen,
  sodass ein alter
  gespeicherter Wert das Theme-Stylesheet nicht austauschen kann.

## Änderungen an den anderen Designs am selben Tag

- Das klassische graue Administrationsdesign (`classic`) erhielt das Exponential-Logo, ein linkes Menü, das sich
  einklappen und in der Breite ändern lässt, und Layouts-Seiten in seinem eigenen Stil.
- Das Popup für den Suchbereich in der Kopfzeile des alten `design/admin` funktioniert jetzt in beiden Kopien (jede hat
  ihre eigenen Element-IDs) und
  schließt sich bei einem Klick außerhalb.

## Verwandte Seiten

- [Die Administrationsoberfläche gestalten: Komponenten, Tokens, kein Inline-CSS](../../../eng-us/bc/6.0/admin4-styling.md)
- [Symbole aus Extensions und Symbol-Themes](../../../eng-us/bc/6.0/ICONS.md)
- [Das responsive Admin-Design (admin3)](admin3-responsives-admin.md), [Breite der linken Seitenleiste und Schriftgröße](admin3-seitenleistenbreite-und-schriftgroesse.md)
- [Einrichtungsassistent und Redakteurs-Siteaccess](einrichtungsassistent-und-editor-siteaccess.md), [jQuery 4 und die Entfernung von YUI](jquery4-und-entfernung-von-yui.md)
- [Admin-Links folgen den Berechtigungen](admin-links-folgen-den-berechtigungen.md), [Sortierung der Bestellliste](sortierung-der-bestellliste.md)
- [Liste der Unterelemente: Spalten, Voreinstellungen und CSV-Export](optionen-der-unterelementtabelle.md), [ausgewählte Unterelemente kopieren](unterelemente-auswahl-kopieren.md), [ausgewählte verstecken und wieder anzeigen](../../../eng-us/bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md)
- [admin4l: die aus Layouts aufgebaute Admin-Seite](admin4l.md), [ihre Verhaltensänderung](../../../eng-us/bc/6.0/admin4l.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Historie: [Oktober 2026](../../../eng-us/history/2026/2026-10.md), [Juni 2026, zweite Hälfte](../../../eng-us/history/2026/2026-06b.md), [Juni 2025](../../../eng-us/history/2025/2025-06.md), [Januar 2025](../../../eng-us/history/2025/2025-01.md), [November 2024](../../../eng-us/history/2024/2024-11.md), [Oktober 2024](../../../eng-us/history/2024/2024-10.md), [August 2024](../../../eng-us/history/2024/2024-08.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
