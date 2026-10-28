# Turkmen (tuk-TM): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard literary Turkmen (türkmen edebi dili) as written in Turkmenistan, in the neutral, technical register of
current Turkmen software (the Turkmen localisations of Windows, Google and Mozilla) and of the Turkmen Wikipedia. No
other locale falls back to `tuk-TM`; Turkish (`tur-TR`), Azerbaijani (`aze-AZ`) and Uzbek (`uzb-UZ`) only help with
terminology, and their spelling is never copied (Turkmen writes ý, ň, ä, ž and the long vowels its own way).

- Turkmen words are used where the software already has them: ýatda saklamak, pozmak, sazlamalar, bukja, ýüklemek,
  göçürip almak, baglanyşyk, goýmaça, açar sözi. Internationalisms of computing keep the usual Turkmen spelling:
  serwer, skript, modul, keş, faýl, brauzer, programma.
- No Russian words where Turkmen has its own, and no Turkish forms (not "ayarlar", "klasör", "şifre" for
  sazlamalar, bukja, açar sözi).
- Turkmen suffixes follow vowel harmony and the final sound of the stem: faýly, serwere, modulyň, keşi.

## 2. Form of address

The interface addresses the user with the polite **Siz** (you), **Siziň** (your), as Turkmen software does.

- **Buttons, menu items and links**: the short imperative, as Turkmen software does: Ýatda sakla, Ýatyr, Poz,
  Redaktirle, Neşir et, Neşir etmäge iber, Saýlananlary aýyr, Zibil sebedine geçir. Not the polite imperative
  (saklaň) on a button.
- **Titles, column headings and tabs**: nouns or noun phrases: Sazlamalar, Terjimeler, Esasy ýerleşiş.
- **Instructions**: the polite imperative in -yň/-iň, -ŋyz: "Elementi saýlaň we «OK» düwmesine basyň.",
  "Garaşyň ...". A reference ("see ...") is "... serediň".
- **Confirmations and questions**: "Bu klasy hakykatdan hem pozmak isleýärsiňizmi?", "... isleýärsiňizmi?".
- **What the user can or may do**: "... bilersiňiz", "... bilmersiňiz", or impersonal: "Esasy dili üýtgetmek
  mümkin däl ...". "You do not have permission" is "Siziň ... rugsadyňyz ýok".
- Not allowed: the familiar sen / seniň, slash forms.

## 3. Script and typography

- **Latin**, the script of the shipped locale (`share/locale/tuk-TM.ini`, LanguageName Türkmen dili) and the
  official alphabet of Turkmenistan since 1993 (revised 1995/1999). The special letters are written with their
  own code points: ç, ä, ž, ň, ö, ş, ü, ý (lower case; upper case Ç, Ä, Ž, Ň, Ö, Ş, Ü, Ý). Never the Turkish
  dotless ı or the letters c, q, x, w used for other sounds: Turkmen j is the sound of English "j", w is the
  sound of English "w"/"v" (serwer, wersiýa).
- **Cyrillic** is no longer used for Turkmen in Turkmenistan; the diaspora in Iran and Afghanistan writes it in the
  Arabic script. This catalogue is Latin.
- **Latin text inside Turkmen**: product names, codes and acronyms stay as they are. A suffix after them follows a
  hyphen (Exponential-iň, PHP-ni), never an apostrophe; a wording without a suffix often reads better: "PHP faýly",
  "URL salgysy", "HTML kody".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before
  and after them, or a punctuation mark right after. Never a letter or hyphenated suffix glued to a placeholder:
  "%count obýekt", not "%count-y" or "%countobýekt". Put the placeholder where it needs no suffix: "Obýekt: %name",
  "%count element".
- **Numbers**: a noun after a number stays in the singular, as Turkmen grammar wants it: "%count obýekt", not
  "obýektler". Digits 0-9.
- **Quotation marks**: the guillemets «...» where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: comma, full stop, colon, semicolon, question and exclamation mark as in the source. A label
  keeps its colon. Turkmen puts the verb at the end of the sentence and the modifier before its noun.

## 4. Glossary

Binding for the kernel and every extension.

| English | Turkmen | Note |
|---|---|---|
| content | mazmun | |
| content object | mazmun obýekti | |
| object | obýekt | |
| node | düwün | main node: esasy düwün; parent node: ene düwün; child: çaga düwün |
| location | ýerleşiş | main location: esasy ýerleşiş |
| item (list entry) | element | |
| class | klas | the content class; class group: klas topary; a PHP class: PHP klasy |
| content class | mazmun klasy | |
| attribute | atribut | |
| datatype | maglumat görnüşi | |
| version | wersiýa | version history: wersiýalaryň taryhy |
| draft | garalama | store draft: garalamany ýatda sakla |
| publish | neşir etmek | button: Neşir et; published: neşir edilen |
| send for publishing | neşir etmäge ibermek | |
| discard | taşlamak | discard draft: garalamany taşla |
| archived | arhiwlenen | |
| pending | garaşylýar | |
| rejected | ret edilen | |
| siteaccess | saýta giriş nokady | the configuration unit |
| site, website | saýt, web saýt | |
| section | bölüm | the Exponential section |
| role | rol | |
| policy | syýasat | |
| limitation | çäklendirme | |
| permission | rugsat | access rights: giriş hukuklary |
| module | modul | |
| function | funksiýa | |
| user | ulanyjy | |
| user group | ulanyjy topary | |
| group | topar | |
| anonymous | anonim | anonymous user: anonim ulanyjy |
| administrator | administrator | |
| administration (interface) | dolandyryş interfeýsi | |
| username | ulanyjy ady | the column "Login" too |
| password | açar sözi | |
| log in / sign in | girmek | button: Gir |
| log out / sign out | çykmak | button: Çyk |
| e-mail | e-poçta | e-mail address: e-poçta salgysy |
| cache | keş | clear cache: keşi arassala |
| template | şablon | |
| override | täzeden kesgitleme | override template: täzeden kesgitleýän şablon |
| fallback | ätiýaçlyk | fallback language: ätiýaçlyk dil |
| design | dizaýn | |
| workflow | iş akymy | |
| trigger | trigger | |
| event | waka | |
| trash | zibil sebedi | empty trash: zibil sebedini boşat |
| bookmark | bellik | |
| settings | sazlamalar | |
| setup | sazlama | the Setup tab; installation: gurnama |
| layout | maket | Exponential Layouts |
| block | blok | |
| zone | zona | |
| extension | giňeltme | |
| package | paket | |
| search | gözleg | button: Gözle |
| log | žurnal | log file: žurnal faýly |
| queue | nobat | |
| job / task | tabşyryk | |
| cron | cron | cron job: cron tabşyrygy |
| script | skript | |
| worker | işleýji proses | |
| kernel, core | ýadro | |
| server | serwer | |
| database | maglumatlar binýady | |
| folder | bukja | |
| file | faýl | |
| image | surat | |
| preview | öňünden görmek | |
| upload | ýüklemek | button: Ýükle |
| download | göçürip almak | button: Göçürip al |
| export / import | eksport / import | to export: eksport et |
| remove | aýyrmak | take out of a list or a place; button: Aýyr |
| delete | pozmak | destroy; button: Poz |
| edit | redaktirlemek | button: Redaktirle |
| editor | redaktor | |
| save / store | ýatda saklamak | button: Ýatda sakla |
| Store (shop tab) | Dükan | the word the shop menu uses for "Shop" |
| cancel | ýatyrmak | button: Ýatyr |
| OK | OK | |
| apply | ulanmak | button: Ulan |
| back | yza | |
| next / previous | indiki / öňki | |
| enable / disable | işjeňleşdirmek / öçürmek | a setting or feature |
| enabled / disabled | işjeň / öçürilen | |
| active / inactive | işjeň / işjeň däl | |
| run | işletmek | a script or job; start a service: işe girizmek |
| start / stop | başlamak / togtatmak | |
| session | sessiýa | |
| hidden / visible | gizlin / görünýän | hide / reveal: gizle / görkez |
| archive | arhiw | |
| order (sort) | tertip | sort order: tertipleme tertibi |
| order (shop) | sargyt | |
| shop | dükan | |
| basket | sebet | kept apart from zibil sebedi |
| product | haryt | |
| customer | müşderi | |
| discount | arzanladyş | |
| VAT | GBS | goşulan baha üçin salgyt; VAT type: GBS görnüşi |
| price | baha | |
| currency | walýuta | |
| tag | teg | keyword tag; an HTML tag: HTML tegi |
| translation | terjime | |
| language | dil | main language: esasy dil |
| locale | lokal | |
| state | ýagdaý | the object state; state group: ýagdaý topary |
| status | status | |
| view (button) | görmek | button: Gör; a module view: görnüş |
| format | format | |
| sort | tertiplemek | |
| filter | süzgüç | |
| select | saýlamak | selected: saýlanan |
| browse | gözden geçirmek | |
| copy | göçürmek | |
| move | geçirmek | |
| add | goşmak | |
| create | döretmek | |
| new | täze | |
| restore | dikeltmek | |
| install / uninstall | gurnamak / aýyrmak | |
| sub items | içki elementler | |
| subtree | içki agaç | |
| related objects | baglanyşykly obýektler | relation: baglanyşyk |
| URL alias | URL lakamy | |
| redirect | gönükdirmek | |
| dashboard | dolandyryş paneli | |
| content structure | mazmun gurluşy | |
| media library | media kitaphanasy | |
| identifier / ID | identifikator / ID | |
| priority | ileri tutulma | |
| owner | eýesi | |
| modified / created | üýtgedilen / döredilen | |
| date / time | sene / wagt | |
| error / warning | ýalňyşlyk / duýduryş | |
| help | kömek | |
| notification | habarnama | |
| wizard | ussat | |
| feed (RSS) | lenta | RSS feed: RSS lentasy |
| tab | goýmaça | |
| audit, audit trail | audit, audit žurnaly | |
| repository | ammar | |
| checksum | barlag jemi | |
| account | hasap | |
| default | deslapky | default value: deslapky baha |
| toolbar | gurallar paneli | |
| container | konteýner | |
| AI | emeli aň | |
| pattern (name pattern) | nusga | kept apart from şablon (template) |
| wildcard | çalşyryjy belgi | |
| remote ID | uzakdaky ID | |
| placeholder | orunbasar | |
| collection | ýygyndy | information collector: maglumat ýygnaýjy |
| approve, approval | tassyklamak, tassyklama | |
| software | programma üpjünçiligi | |
| creator / modifier | döreden / üýtgeden | |
| request (HTTP) | haýyş | |
| query | sorag | search query: gözleg soragy |
| handler / provider / listener | işleýji / üpjün ediji / diňleýji | |
| engine | hereketlendiriji | |
| release / upgrade | goýberiliş / täzelemek | update: täzelemek |
| backup | ätiýaçlyk nusgasy | |
| maintenance | tehniki hyzmat | |
| directory | katalog | |
| header (HTTP) | sözbaşy | |
| page layout (pagelayout template) | sahypa şablony | kept apart from maket (Exponential Layouts) |
| passkey | giriş açary | |
| two-step sign-in | iki ädimli giriş | |
| device / browser | enjam / brauzer | |
| link | baglanyşyk | |
| registration, register | hasaba alyş, hasaba almak | |
| payment gateway | töleg derwezesi | |
| form | forma | |
| comment | teswir | |
| newsletter / subscription | habar býulleteni / abuna | |
| field | meýdan | |
| value | baha | |
| list | sanaw | |
| page | sahypa | |
| title / name | at / ady | the column "Name": Ady |
| description | düşündiriş | |
| type | görnüş | |
| path | ýol | |
| width / height | ini / beýikligi | |
| size | ölçeg | |
| none | ýok | |
| yes / no | hawa / ýok | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Dükan, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is ýatda saklamak.
- "Value" and "price" are both baha in Turkmen; the context tells them apart, and in the shop the price is baha,
  the value of a field or setting is baha too, written with its noun (meýdanyň bahasy).
