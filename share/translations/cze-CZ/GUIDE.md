# Czech (cze-CZ): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Czech (spisovná čeština) as used in the Czech Republic. No other locale falls back to this catalogue. The
tone is neutral, concise and professional, as in the Czech versions of common operating systems and office
software. Avoid colloquial forms (obecná čeština) and needless English words.

## 2. Form of address

- **Buttons, menu items, links and action labels** use the infinitive: "Uložit", "Odstranit", "Upravit", "Přidat",
  "Zrušit", "Vytvořit nový", "Použít změny", "Odeslat k publikování", "Vybrat vše".
- **Titles, column headings and tabs** are nouns: "Struktura obsahu", "Uživatelské účty", "Typ rozvržení",
  "Nastavení oznámení".
- **Sentences, hints, questions and error messages** address the user with the formal **vy**, written in lower
  case (vy, vás, váš, vaše), and the plural imperative for instructions:
  - "Opravdu chcete tuto třídu odstranit?"
  - "Vyberte umístění" (Please choose a location)
  - "Nemáte oprávnění odstraňovat třídy."
  - "Klikněte na tlačítko "Zrušit" a zkuste odstranit pouze umístění, která smíte odstranit."
  - "Nastavení historie verzí můžete změnit v souboru content.ini ..."
- **Tooltips that describe a click** use "Kliknutím ...": "Kliknutím na ikonu zobrazíte kontextovou nabídku."
- Status messages are passive or impersonal: "Objekt byl odstraněn.", "Nebyl nalezen žádný běh úloh cron".
- Not allowed: the familiar form (ty, tvůj, chceš, můžeš), "Vás/Váš" with a capital letter inside a sentence, and
  the infinitive as an instruction inside a sentence ("Použít toto pole k ..." is wrong; write "Toto pole slouží
  k ...").

## 3. Script and typography

- Latin script with the full set of diacritics (á č ď é ě í ň ó ř š ť ú ů ý ž). Sentence case: "Struktura obsahu",
  "Stavy objektů".
- Quotation marks in running text: „…“. Inside HTML attributes and JavaScript the straight quotes of the source
  stay.
- Czech has three plural forms (1, 2–4, 5+), the source only "item(s)". Never write "položka(y)". Use a label form:
  "Objekty: %count", "Pracovní procesy: %count", "Odstraněné koncepty: %count."
- A placeholder is never declined: "Upravit objekt %name", "Třída <%class_name>".
- Time units after a number: "min", "h", "s"; "před %n min".

## 4. Glossary

| English | Czech | Note |
|---|---|---|
| content | obsah | |
| content object / object | objekt obsahu / objekt | |
| node | uzel | main node: hlavní uzel |
| location / main location | umístění / hlavní umístění | |
| content class / class | třída obsahu / třída | class group: skupina tříd |
| attribute | atribut | |
| datatype | datový typ | |
| version | verze | |
| draft | koncept | Store draft: Uložit koncept; Discard draft: Zahodit koncept |
| publish / published | publikovat / publikováno | Send for publishing: Odeslat k publikování |
| siteaccess | přístup k webu | pl. přístupy k webu |
| section | sekce | |
| object state / state | stav objektu / stav | |
| role | role | |
| policy | zásada | pl. zásady |
| limitation | omezení | |
| permission / access denied | oprávnění / přístup odepřen | |
| user / user group | uživatel / skupina uživatelů | |
| user accounts | uživatelské účty | |
| owner / creator | vlastník / autor | |
| created / modified | vytvořeno / změněno | column headings |
| cache | mezipaměť | Clear cache: Vymazat mezipaměť |
| template | šablona | |
| override | přepsání | override template: přepisující šablona |
| fallback (language) | záložní jazyk | fallback chain: záložní řetězec |
| design | vzhled | |
| workflow | pracovní postup | |
| trigger / event | spouštěč / událost | |
| trash | koš | |
| bookmarks | záložky | |
| settings | nastavení | |
| Setup (tab) | Nastavení | |
| Dashboard | Nástěnka | |
| Content structure | Struktura obsahu | |
| Media library | Knihovna médií | |
| layout | rozvržení | layout type: typ rozvržení; the name Exponential Layouts stays |
| block / zone | blok / zóna | |
| extension | rozšíření | |
| package | balíček | |
| module / view | modul / zobrazení | "Module view": pohled modulu; the button "View": Zobrazit |
| search | hledat | noun: vyhledávání; search engine: vyhledávač |
| search index / reindex | vyhledávací index / přeindexování | |
| log / log file | protokol / soubor protokolu | |
| audit / audit trail | audit / auditní stopa | |
| job / queue | úloha / fronta | job queue: fronta úloh |
| worker | pracovní proces | |
| cronjob | úloha cron | |
| preview | náhled | |
| upload / download | nahrát / stáhnout | |
| export / import | exportovat / importovat | |
| remove | odstranit | taking something out of a list or group: odebrat |
| delete | odstranit | |
| edit | upravit | |
| save / store | uložit | Store (shop tab): Obchod |
| discard | zahodit | |
| apply | použít | Apply changes: Použít změny |
| cancel | zrušit | |
| OK / yes / no | OK / ano / ne | |
| back / next / previous | zpět / další / předchozí | |
| create / add | vytvořit / přidat | |
| select / browse | vybrat / procházet | Select all: Vybrat vše; Deselect all: Zrušit výběr všeho |
| move / copy | přesunout / kopírovat | |
| hide / reveal | skrýt / zobrazit | hidden / visible: skryto / viditelné |
| enable / disable | povolit / zakázat | enabled / disabled: povoleno / zakázáno |
| restore / assign | obnovit / přiřadit | |
| approve / reject / pending | schválit / zamítnout / čeká na vyřízení | |
| archive | archiv | |
| order (shop) | objednávka | order status: stav objednávky |
| order (sorting) | pořadí | |
| shop / basket / customer / product | obchod / košík / zákazník / produkt | |
| VAT / discount / price / currency | DPH / sleva / cena / měna | |
| wish list | seznam přání | |
| newsletter / subscription | zpravodaj / odběr | |
| tag / keywords | štítek / klíčová slova | an HTML/XML tag: značka |
| URL alias / wildcard | alias URL / zástupný znak | |
| translation / language | překlad / jazyk | main language: hlavní jazyk; interface language: jazyk rozhraní |
| locale | národní prostředí | |
| relation / related objects | vazba / související objekty | reverse related: objekty odkazující na tento objekt |
| subtree / parent / sub items | podstrom / nadřazená položka / podřízené položky | |
| notification / collaboration | oznámení / spolupráce | |
| identifier / remote ID | identifikátor / vzdálené ID | |
| login (field) / password | přihlašovací jméno / heslo | |
| log in / log out | přihlásit se / odhlásit se | |
| e-mail | e-mail | |
| file / folder / image | soubor / složka / obrázek | |
| item / items | položka / položky | |
| name / title / description | název / název / popis | a person's name: jméno |
| type / status | typ / stav | |
| default / required | výchozí / povinné | |
| error / warning | chyba / upozornění | |
| filter / details | filtr / podrobnosti | |
| run / refresh / update / reset | spustit / obnovit / aktualizovat / vynulovat | |
| dry run | zkušební běh | |
| debug | ladění | |
| editor (software) / editor (person) | editor / redaktor | template editor: editor šablon |
| provider | poskytovatel | |
| prompt / model / token | výzva / model / token | |
| embedding | vektorová reprezentace | |
| thread / chain | vlákno / řetězec | |

## 5. Decisions after the translation

- **Store**: the shop tab of the administration header is "Obchod"; every other "Store" is the verb "Uložit".
- **Exponential** in running text, where Czech grammar needs a case: "systém Exponential".
