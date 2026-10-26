# Finnish (fin-FI): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Finnish (yleiskieli) as written in Finland. No other locale falls back to this catalogue. The tone is
neutral, concise and professional, as in the Finnish versions of common operating systems and office software
(Microsoft terminology and style guide for Finnish). Avoid spoken forms (puhekieli: "mä", "tää", "tallentaa
juttuja"), needless English words and literal translations of English sentence structure.

The older Finnish catalogue addressed the user in the singular (sinä forms such as "voit", "haluatko", "sinun";
never the plural te), used the imperative on buttons ("Tallenna", "Peruuta", "Muokkaa", "Poista valitut") and the
passive in status messages ("Välimuisti tyhjennettiin"). This guide keeps those choices. Its terms are kept where
they are correct and current ("solmu", "luokka", "luonnos", "välimuisti", "roskakori", "alipuu", "ALV") and
replaced where they were wrong or outdated: "Sivuyhteys" (siteaccess), "Määritelmä" (attribute), "Samankaltaiset
objektit" (related objects), "Paikallinen" (locale), "Muuttuja" (modifier), "Työprosessi" (workflow), "Lataa" for
upload.

## 2. Form of address

- **Buttons, menu items, links and action labels** use the imperative singular, with the object after the verb:
  "Tallenna", "Poista", "Muokkaa", "Lisää", "Peruuta", "Luo uusi", "Käytä muutoksia", "Lähetä julkaistavaksi",
  "Valitse kaikki", "Tyhjennä välimuisti", "Tallenna luonnos", "Hylkää luonnos".
- **Titles, column headings and tabs** are nouns or noun phrases: "Sisältörakenne", "Käyttäjätilit",
  "Asettelun tyyppi", "Ilmoitusasetukset", "Objektin tilat", "Muokattu".
- **Sentences, hints and instructions** address the user in the singular through the verb ending, without the
  pronoun: "Valitse sijainti uudelle objektille (luokka <%classname>)", "Voit muuttaa versiohistorian asetuksia
  tiedostossa content.ini, …", "Anna kelvollinen sähköpostiosoite.", "Tarkista yhteys ja yritä uudelleen."
  Possessives use the suffix, not "sinun": "salasanasi", "tilisi", "kirjanmerkkiluetteloosi".
- **Questions** use the -ko/-kö form of the second person singular: "Haluatko varmasti poistaa tämän luokan?",
  "Haluatko varmasti poistaa luokkaryhmän?", "Poistetaanko solmu?" (impersonal, also correct).
- **Error messages** state the fact; where the user is meant, use the possessive construction: "Sinulla ei ole
  oikeutta poistaa luokkia." (the pronoun is needed here), "Tiedostoa ei löytynyt.", "Salasana on pakollinen."
- **Status messages** are passive or impersonal: "Lohko poistettiin.", "Kaikki välimuistit tyhjennettiin.", "Rikkinäisiä
  linkkejä ei löytynyt.", "Luonnos tallennettiin."
- **Tooltips that describe a click** use the instrumental infinitive with "napsauttamalla" or a plain imperative:
  "Avaa pikavalikko napsauttamalla kuvaketta.", "Poista valitut kohteet napsauttamalla ”Poista valitut”
  -painiketta."
- **Not allowed**: the plural or polite te (haluatteko, voitte, teidän), "Te"/"Teidän" with a capital letter, the
  passive on a button ("Tallennetaan"), the first infinitive as a button or instruction ("Tallentaa", "Poistaa
  valitut"), "Sinun tulee …" or "Sinun täytyy …" as an instruction (write "Valitse …" or "… on valittava"), the
  pronoun "sinä/sinun" where the verb ending or a possessive suffix says it already, "klikkaa" (write "napsauta").

## 3. Script and typography

- **Letters**: Latin script with ä, ö and å (å only in Swedish names). Sentence case in buttons, titles and tabs:
  "Sisältörakenne", "Objektin tilat", "Tallenna luonnos". No English title case. Days, months and language names are
  written in lower case inside a sentence ("maanantai", "suomi").
- **Quotation marks**: Finnish uses the same mark at both ends, ”…” (U+201D), and ’…’ (U+2019) inside a quotation:
  ”%name”. Inside HTML attributes, JavaScript and code the straight quotes of the source stay.
- **Punctuation and dashes**: no space before ":", ";", "?" and "!". The en dash with spaces – for a break in a
  sentence and without spaces for ranges (5–10). The ellipsis as in the source.
- **Compounds with an abbreviation, a code, a product name or a placeholder** take a hyphen: "URL-alias",
  "INI-tiedosto", "HTML-tagi", "XML-koodi", "PHP-luokka", "Solr-ydin", "Cron-työ", "Exponential-järjestelmä",
  "%engine-rakennekäsittelijä", "napsauta %1-painiketta". A compound with a multi-word name takes a space and a hyphen: "Exponential Layouts -asettelu".
  Finnish compounds are written as one word: "käyttäjäryhmä", "tilaryhmä", "luokkaryhmä".
- **Plurals and numbers with a placeholder**: after a numeral Finnish uses the partitive singular ("5 objektia"),
  but after 1 the nominative ("1 objekti"), and the source only has "item(s)". Never write "objekti(a)". Use a label
  form: "Objekteja: %count", "Kirjanmerkkejä: %count", "Osumia: %count".
- **Placeholders with case endings**: a placeholder is never glued to a Finnish ending, because the checker reads
  `%namen` as a different placeholder. Separate the ending with a colon, as Finnish does after abbreviations and
  numbers: "%name:n", "%name:ssa", "%name:sta", "%name:lle", "%name:ksi". Where the ending would depend on how the
  value is pronounced, prefer a construction in which the placeholder stands free, as the catalogue does throughout: "Muokkaa
  tilaa %state", "PDF-vienti %name tallennettiin.", "Valitse uusi sijainti objektille <%object_name>".
- **Numbers**: digits 0–9, decimal comma ("1,5 Mt"), a non-breaking space as thousands separator in prose
  ("10 000"). Units after a number with a space: "min", "h", "s", "Mt", "Gt", "kt"; "%n min sitten". Dates in the
  form 23.10.2026 come from the locale, not from the catalogue.

## 4. Glossary

Binding for the kernel and every extension. Where the Microsoft Finnish terminology has an established term, it is
used. Product names (Exponential, Exponential Layouts, Solr, OpenAI) stay as they are.

| English | Finnish | Note |
|---|---|---|
| content | sisältö | |
| content object / object | sisältöobjekti / objekti | |
| item (list entry, general) | kohde | items: kohteet |
| node | solmu | main node: pääsolmu |
| location (node placement) | sijainti | the place of an object in the tree |
| main location | pääsijainti | |
| content class / class | sisältöluokka / luokka | a PHP class: PHP-luokka |
| class group | luokkaryhmä | |
| attribute | määrite | not "määritelmä"; class attribute: luokan määrite |
| datatype | tietotyyppi | |
| identifier | tunniste | class identifier: luokan tunniste |
| ID / remote ID | tunnus / etätunnus | object ID: objektin tunnus |
| version | versio | version history: versiohistoria |
| draft | luonnos | Store draft: Tallenna luonnos; Discard draft: Hylkää luonnos |
| publish / published / publishing | julkaise / julkaistu / julkaisu | Send for publishing: Lähetä julkaistavaksi |
| translation / language | käännös / kieli | |
| main language / content language | pääkieli / sisällön kieli | interface language: käyttöliittymän kieli |
| siteaccess | sivustoyhteys | pl. sivustoyhteydet; replaces "sivuyhteys"; siteaccess settings: sivustoyhteyden asetukset |
| site / website | sivusto / verkkosivusto | |
| design (eZ design) | ulkoasu | site design: sivuston ulkoasu |
| structure | rakenne | |
| Content structure | Sisältörakenne | |
| Media library | Mediakirjasto | |
| Dashboard | Koontinäyttö | |
| Setup (admin tab) | Asetukset | the tab; configuration screens under it keep their own names |
| setting / settings | asetus / asetukset | |
| configuration / configure | määritys / määritä | configuration file: määritystiedosto |
| template | malli | template file: mallitiedosto; template editor: mallieditori |
| override / overridden / override template | korvaus / korvattu / korvaava malli | a template or setting that replaces another |
| fallback | varavaihtoehto | fallback language: varakieli; fallback chain: varakieliketju |
| alias / URL alias | alias / URL-alias | |
| wildcard (URL) | yleismerkki | wildcard URL alias: yleismerkki-URL-alias |
| redirect / redirected / forward (URL) | uudelleenohjaus / ohjataan uudelleen / välitä | |
| cache / clear cache | välimuisti / tyhjennä välimuisti | |
| workflow / workflow event / workflow process | työnkulku / työnkulun tapahtuma / työnkulun prosessi | never "työprosessi" (= worker) |
| trigger / event | käynnistin / tapahtuma | |
| role | rooli | |
| policy | käytäntö | pl. käytännöt |
| limitation (policy) | rajoitus | |
| permission / access / access denied | käyttöoikeus / käyttö / pääsy estetty | |
| privilege (DB) / grant | oikeus / myönnä | |
| user / user group | käyttäjä / käyttäjäryhmä | |
| user account / user accounts | käyttäjätili / käyttäjätilit | |
| anonymous user | anonyymi käyttäjä | |
| administrator / admin interface | järjestelmänvalvoja / hallintaliittymä | |
| user interface | käyttöliittymä | |
| log in / log out, sign in / sign out | kirjaudu sisään / kirjaudu ulos | signed out by someone else: kirjattiin ulos |
| login (field) / username | käyttäjätunnus | |
| password | salasana | |
| e-mail | sähköposti | e-mail address: sähköpostiosoite |
| section | osio | always the Exponential section, never "osa-alue"; a part of a page or document: osa ("Tässä osassa …"); an INI section: lohko (section 5) |
| object state / state group | objektin tila / tilaryhmä | |
| status | tila | |
| relation / related objects | suhde / liittyvät objektit | not "samankaltaiset" |
| reverse related objects | tähän viittaavat objektit | |
| trash | roskakori | Empty trash: Tyhjennä roskakori |
| bookmark / bookmarks | kirjanmerkki / kirjanmerkit | |
| sub-items / children | alikohteet | |
| parent | ylätason kohde | parent node: yläsolmu |
| subtree | alipuu | |
| owner | omistaja | |
| creator / author | luoja / tekijä | |
| modifier | muokkaaja | the person; not "muuttuja" |
| created / modified | luotu / muokattu | column headings |
| layout (Exponential Layouts) | asettelu | layout type: asettelun tyyppi; the name Exponential Layouts stays |
| layout (directory or release structure) | rakenne | |
| block / zone | lohko / alue | |
| extension | laajennus | |
| plugin | lisäosa | |
| package | paketti | |
| module / view (noun) / function | moduuli / näkymä / toiminto | module view: moduulin näkymä |
| view (button) | Näytä | |
| search (button) / search (noun) | Hae / haku | search engine: hakukone; search results: hakutulokset |
| search index / reindex / index (verb) | hakuindeksi / indeksoi uudelleen / indeksoi | |
| index (DB) / index file | indeksi / indeksitiedosto | |
| core (Solr) | Solr-ydin | |
| kernel | järjestelmäydin | |
| child documents (Solr) | alidokumentit | |
| cronjob | Cron-työ | |
| schedule | aikataulu / ajoita | noun / verb |
| job / queue / worker | työ / jono / työprosessi | job queue: työjono |
| lane (queue) | kaista | |
| run (noun) / run (verb) | ajo / suorita | cron run: Cron-ajo |
| dry run / self-test | koeajo / itsetesti | |
| log / log file | loki / lokitiedosto | never "log in" |
| rotation / rotate (log) | kierrätys / kierrätä | |
| audit / audit trail, audit log | valvonta / valvontaloki | "audit" stays only in code and module names |
| notification | ilmoitus | notification settings: ilmoitusasetukset |
| collaboration | yhteistyö | |
| preview | esikatselu / Esikatsele | noun / button |
| file / folder / directory | tiedosto / kansio / hakemisto | |
| image / media | kuva / media | |
| thumbnail | pikkukuva | |
| upload / download | lähetä palvelimeen / lataa | upload button: Lähetä; not "Lataa" for upload |
| export / import | vie / tuo | noun: vienti / tuonti |
| move / copy / swap | siirrä / kopioi / vaihda | |
| shop | kauppa | Store (shop tab): Kauppa |
| basket | ostoskori | |
| order (shop) | tilaus | order status: tilauksen tila |
| order (sorting) | järjestys | sort order: lajittelujärjestys |
| sort / ascending / descending | lajittele / nouseva / laskeva | |
| customer / product | asiakas / tuote | |
| checkout | kassa | |
| VAT / VAT type | ALV / ALV-tyyppi | |
| discount / discount rule / rule | alennus / alennussääntö / sääntö | |
| price / currency | hinta / valuutta | |
| wish list | toivelista | |
| payment gateway / gateway | maksupalvelu / yhdyskäytävä | |
| newsletter | uutiskirje | |
| subscriber / subscription / subscribe | tilaaja / tilaus / tilaa | in the shop "tilaus" is the order; the context decides |
| tag (eztags) | asiasana | pl. asiasanat |
| keyword | avainsana | |
| tag (HTML/XML) / custom tag | tagi / mukautettu tagi | HTML-tagi, XML-tagi |
| embed / embedded (object in text) | upota / upotettu | |
| embedding (vector) / chunk / vector | vektoriupotus / tekstipala / vektori | |
| prompt / model / token | kehote / malli / tokeni | AI model where "malli" is ambiguous: tekoälymalli; token count: tokenien määrä; Bearer token: Bearer-tokeni; form token: lomaketunniste |
| AI | tekoäly | |
| provider / backend / endpoint | palveluntarjoaja / taustajärjestelmä / päätepiste | |
| method / listener / controller | metodi / kuuntelija / ohjain | |
| handler / wizard | käsittelijä / ohjattu toiminto | |
| deploy / deployment / release | ota käyttöön / käyttöönotto / julkaisuversio | software that was released: julkaistu; release notes: julkaisutiedot |
| enable / disable | ota käyttöön / poista käytöstä | |
| enabled / disabled | käytössä / ei käytössä | |
| active / inactive | aktiivinen / ei aktiivinen | |
| warning / error / notice | varoitus / virhe / huomautus | |
| required / optional | pakollinen / valinnainen | not "vaadittu" |
| default / priority | oletus / prioriteetti | default value: oletusarvo |
| visible / hidden | näkyvä / piilotettu | |
| hide / show / reveal | piilota / näytä / näytä | |
| expand / collapse | laajenna / kutista | |
| OK / Cancel / Yes / No | OK / Peruuta / Kyllä / Ei | |
| Back / Next / Previous / Close | Takaisin / Seuraava / Edellinen / Sulje | |
| select / select all | valitse / valitse kaikki | |
| deselect all / select none | poista kaikki valinnat | |
| browse / path | selaa / polku | |
| tick (checkbox) | valitse | the box: valintaruutu |
| apply | käytä | Apply changes: Käytä muutoksia |
| save / store | tallenna | Store as a verb is always "Tallenna" |
| discard | hylkää | |
| edit | muokkaa | |
| remove | poista | taking out of a list, group or place: "Poista valitut", "Poista sijainti"; where "delete" stands next to it: "Poista luettelosta" |
| delete | poista | destroy; the confirmation says "pysyvästi" where it matters |
| new / create / add | uusi / luo / lisää | |
| refresh / update / reset | päivitä / päivitä / palauta oletukset | reset a counter: nollaa |
| restore / assign / assignment | palauta / määritä / määritys | Assign role: Määritä rooli |
| approve / approval / approved / approver | hyväksy / hyväksyntä / hyväksytty / hyväksyjä | |
| deny / reject / rejected | estä / hylkää / hylätty | |
| accept / accepted | hyväksy / hyväksytty | |
| pending | odottaa / odottava | column value: Odottaa |
| archived (version, object, order) | arkistoitu | |
| Archive / Unarchive (button) | Arkistoi / Palauta arkistosta | |
| archive (a place, menu item, log archive) | arkisto | |
| archive (file .tar, .zip, .phar, .ezpkg) | arkistotiedosto | a .zip file: .zip-tiedosto |
| statistics / system information | tilastot / järjestelmätiedot | |
| database | tietokanta | |
| allowlist / read-only / argument | sallittujen luettelo / vain luku / argumentti | |
| slot / collection / facets / elevate | paikka / kokoelma / rajausehdot / nosta | collection also for one information collection submission and a MongoDB collection |
| filter | suodatin / suodata | noun / verb |
| container (class flag "Is container") | säilö | |
| container (CSS, Bootstrap) | säilö | the class name `container` stays in code |
| repository | tietovarasto | |
| locale / localization | maa-asetus / lokalisointi | not "paikallinen" |
| charset | merkistö | |
| license | lisenssi | |
| format / formatting | muoto / muotoilu | file format: tiedostomuoto |
| linked / common | linkitetty / yhteinen | |
| flags (class flags) / flag (command line) / flag (job option without value) | merkinnät / valitsin / lippu | "Is container", "Always available" are merkinnät; `--allow-write` is a valitsin |
| teaser | esittelyteksti | |
| slug | URL-nimi | |
| placeholder | paikkamerkki | |
| preset | esiasetus | |
| information collection / collector | tietojen keruu / tietojen kerääjä | |
| overview / summary | yleiskatsaus / yhteenveto | |
| tab | välilehti | |
| rich text | muotoiltu teksti | |
| feed | syöte | RSS-syöte |
| session | istunto | |
| scope | laajuus | |
| thread / chain | keskusteluketju / ketju | AI chat thread: keskusteluketju; a process or database thread: säie |
| batch | erä | |
| schema / registry / manifest | rakennekaavio (skeema) / rekisteri / luettelotiedosto | database schema: tietokannan rakenne |
| stylesheet / theme / icon | tyylitiedosto / teema / kuvake | |
| port / digest / profile | portti / kooste / profiili | digest e-mail: koostesähköposti |
| editor (person) / editor (software) | toimittaja / editori | template editor: mallieditori |
| editor (siteaccess type) | toimittajan näkymä | |
| page / pages / link | sivu / sivut / linkki | |
| title / name / description | otsikko / nimi / kuvaus | |
| date / time | päivämäärä / aika | |
| type / group / list | tyyppi / ryhmä / luettelo | |
| value / field | arvo / kenttä | |
| number / count | numero / määrä | |
| width / height / size | leveys / korkeus / koko | |
| server / client / request / response | palvelin / asiakasohjelma / pyyntö / vastaus | |
| task | tehtävä | |
| details | tiedot | more details: lisätiedot |
| debug | virheenkorjaus | debug output: virheenkorjaustiedot |
| pattern | kuvio | regular expression pattern: hakulauseke |
| maintainer | ylläpitäjä | |
| install / uninstall / installation | asenna / poista asennus / asennus | |
| checksum / quarantine | tarkistussumma / karanteeni | |
| pool / mapping / resolver / checkpoint | varanto / vastaavuus / selvitin / tarkistuspiste | |
| move (in the tree, to a group, a state) | siirrä | |
| confirmation questions ("Delete …?", "Cancel this …?") | "Poistetaanko …?", "Haluatko varmasti peruuttaa …?" | |
| resolve / resolvable | selvitä / selvitettävissä | path, name |
| script | komentosarja | a PHP script: PHP-komentosarja |
| revoke | mitätöi | key, token, session, access |
| label | nimike | field or button label |
| proceed / continue | jatka | |
| confirm | vahvista | |
| close / open | sulje / avaa | |
| send | lähetä | |
| print | tulosta | |
| priority (sorting) | prioriteetti | |
| identifier (class attribute) | tunniste | |
| object name pattern | objektin nimen kuvio | |
| URL | URL-osoite | in compounds: URL-alias |

## 5. Decisions after the translation

Terms fixed after the first translation, for words the glossary did not cover or that had been translated in more
than one way. They take precedence over older wording.

| English | Finnish | Note |
|---|---|---|
| INI section / INI block (`[Name]` in an .ini file) | lohko; where it could be read as a Layouts block: INI-lohko | in prose with the file: "tiedoston menu.ini lohkosta [TopAdminMenu]"; never "osio" (the Exponential section) or "ryhmä" |
| definition (class, package, index) | määritelmä | class definition: luokan määritelmä; never "määritys" (= configuration) or "määrittely" |
| variable (INI setting, environment) | muuttuja | correct here; only modifier (the person) is never "muuttuja" |
| render / rendered / rendering | muodostaa / muodostettu / muodostus | render error: Muodostusvirhe; not "hahmontaa" |
| template operator | mallioperaattori | one word |
| wizard (title) | "<X>:n ohjattu toiminto" | "Mallioperaattorin ohjattu toiminto (vaihe 2/3)"; every step carries the same title |
| setup wizard | ohjattu asennus | |
| page layout (pagelayout.tpl) | sivupohja | not "asettelu" (= Exponential Layouts) |
| product category (shop) | tuotekategoria | "tuoteluokka" only for a product class (a content class with a price) |
| category (general, mail, podcast) | kategoria | not "luokka" |
| autoload / autoloads / autoloader / autoload arrays | automaattilataus / automaattilataukset / automaattilataaja / automaattilatauksen taulukot | one compound, not "automaattinen lataus" |
| mail transport | (sähköpostin) välitystapa | label: Sähköpostin välitystapa; HTTP/stdio transport (MCP): HTTP-siirto, stdio-siirto |
| scan / scanned (storage, log) | läpikäynti / käydä läpi | not "tutkinta" |
| audit sink | vientikohde | Valvonnan vientikohteet |
| option (choice in a datatype) / option (command line, job) / options (settings of a table) | vaihtoehto / valitsin / asetukset | |
| held by an approval / held by a budget | Odottaa hyväksyntää / Budjetin pysäyttämä | held (job, run): pysäytetty |
| issue (tracker) / issue (found by a check) | ongelmailmoitus / ongelma | in the setup wizard: asia |
| hostname, host name | isäntänimi | not "palvelimen nimi" (= server name) |
| vendor (package, Composer) | valmistaja | Composer-valmistaja; toimittaja is the editor (person) |
| link check | linkkien tarkistus | |
| image alias / size alias | kuva-alias / kokoalias | |
| extension point | laajennuskohta | |
| stub (oerimport) | tynkä | partitive plural: tynkiä |
| staging (environment) | esituotanto | badges DEV/STAGE/PROD as in the source, TEST: TESTI |
| cronjob part | Cron-työn osa | Run part: Suorita osa |
| pause / resume | keskeytä / jatka | |
| timeout / timed out | aikakatkaisu / aikakatkaistu | |
| masked / unredacted | peitetty / peittämätön | |
| escape (key) / escape (HTML, \u) | Esc / erikoismerkkien koodaus | in Solr query syntax: koodinvaihto |
| entry (log, registry, list) | merkintä | |
| settings file | asetustiedosto | |
| slave database / read replica | toissijainen tietokanta / lukukopio | |
| Desktop / Large desktop / Landscape (preview) | Työpöytä / Suuri työpöytä / Vaaka | |
| Top / Bottom (move) | Ylimmäksi / Alimmaksi | |
| Need a look (translations) | Vaativat tarkistusta | |
| Trashed by / Trashed from | Roskakoriin siirtänyt / Siirretty roskakoriin sijainnista | |
| in %1 / %1 ago | %1 päästä / %1 sitten | units in the partitive (päivää, vuotta) |
| page range "%from to %to of %count" | %from–%to / %count | en dash without spaces |
| From (column "source of the value") | Lähde | the string is shared by several views |
| in (settings view "[block] name in path"; "5 in 60 s") | / | "[Lohko] nimi / polku", "5 / 60 s"; not "sisään" |
| grade level / subject / lesson / exercise | luokka-aste / oppiaine / oppitunti / harjoitus | Margerite only |
| sidecar (authz.json) | rinnakkaistiedosto | |
| split directory | jakohakemisto | |

- **"(a)" after a placeholder or noun** is not allowed (section 3): "prosesseja", not "prosessi(a)".
- **Dash**: " – " (en dash with spaces) for a break, never " - ", unless the text is code or a command line.
- **"vaadittu/vaaditaan"** only as a verb ("Käyttöoikeus vaaditaan", "vaaditulla välillä"); a column or label
  "required" is "Pakollinen".
- **"paikallinen"** is right for "local" (local model, local business, local file); it is only wrong for locale.
