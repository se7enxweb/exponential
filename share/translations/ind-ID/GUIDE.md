# Indonesian (ind-ID): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Indonesian (bahasa Indonesia baku) in the spelling of EYD (Ejaan yang Disempurnakan, fifth edition) and
the KBBI, in the neutral, concise register of current Indonesian software. Terms follow the established usage of
Indonesian software localisation (operating systems, office suites, WordPress): an Indonesian word where one is
in common use (unggah, unduh, berkas, kata sandi, pengaturan), the adapted loan word where that is what the IT
community writes (konten, server, skrip, templat). With its own catalogue `ind-ID` no longer falls back to the
source texts (the line `FallbackLanguages[ind-ID]=untranslated` of `settings/i18n.ini` was removed with it).

- Malay (`msa-MY`) is a related but different standard: no Malaysian words (fail, padam, kemas kini, pautan).
- No slang, no Jakarta colloquial forms (nggak, udah, bikin), no English where the glossary has a word.

## 2. Form of address

The interface addresses the user with the formal **Anda** (capital A), possessive **Anda** after the noun
("kata sandi Anda"). Never kamu, -mu, kau or engkau.

- **Buttons, menu items and links**: the base imperative verb, without "silakan": Simpan, Batal, Hapus, Sunting,
  Kirim untuk diterbitkan, Hapus yang dipilih, Pindahkan ke tempat sampah. Transitive verbs take the imperative
  form with -kan / -i where the standard requires it (Terapkan, Aktifkan, Pindahkan, Pulihkan).
- **Titles, column headings and tabs**: nouns or noun phrases: Pengaturan, Terjemahan, Lokasi utama.
- **Instructions**: the polite imperative with "silakan" or "harap" where English has "please", otherwise the
  base verb: "Pilih item lalu klik \"OK\".", "Harap tunggu hingga prioritas disimpan.". A reference ("see ...") is
  "lihat ...".
- **Confirmations and questions**: "Apakah Anda yakin ingin ...?" or "Apakah Anda ingin ...?":
  "Apakah Anda yakin ingin menghapus kelas ini?".
- **What the user can or may do**: "Anda dapat ...", "Anda tidak dapat ...", or impersonal with the di- passive:
  "Bahasa utama tidak dapat diubah ...". "You do not have permission" is "Anda tidak memiliki izin ...".
- **Messages about a result**: the di- passive with "berhasil": "Draf berhasil disimpan.",
  "Berkas berhasil diunggah.".

## 3. Script and typography

- **Latin script**, the spelling of EYD: praktik (not praktek), analisis (not analisa), sistem, kualitas,
  aktivitas, risiko, antarmuka, nonaktif (bound forms written together: antarmuka, subpohon, nonaktif).
- **di as a prefix is written together, di as a preposition apart**: "disimpan", but "di server", "di folder ini".
- **Reduplication marks the plural only where needed**, with a hyphen: "item-item"; usually the plain noun is
  enough: "Hapus item yang dipilih", "%count objek".
- **Capitals**: only the first word of a button, title or sentence, proper names and Anda: "Sunting draf", not
  "Sunting Draf".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. The number comes before the noun, without plural: "%count objek".
  The same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source; no space before colon, question or exclamation mark.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Indonesian | Note |
|---|---|---|
| content | konten | |
| content object | objek konten | |
| object | objek | |
| node | simpul | main node: simpul utama; parent node: simpul induk |
| location | lokasi | main location: lokasi utama |
| item (list entry) | item | |
| class | kelas | the content class; class group: grup kelas; a PHP class: kelas PHP |
| content class | kelas konten | |
| attribute | atribut | |
| datatype | tipe data | |
| version | versi | version history: riwayat versi |
| draft | draf | store draft: simpan draf |
| publish | terbitkan | published: diterbitkan; publishing: penerbitan |
| send for publishing | kirim untuk diterbitkan | |
| discard | buang | discard draft: buang draf |
| archived | diarsipkan | |
| pending | tertunda | |
| rejected | ditolak | |
| siteaccess | akses situs | siteaccess as a setting name stays in code |
| site, website | situs | |
| section | seksi | the Exponential section; an INI section: bagian |
| role | peran | |
| policy | kebijakan | one rule of a role |
| limitation | batasan | |
| permission | izin | access denied: akses ditolak |
| module | modul | |
| function | fungsi | |
| user | pengguna | |
| user group | grup pengguna | |
| group | grup | |
| anonymous | anonim | |
| administrator | administrator | allowlisted where it stands alone |
| administration (interface) | administrasi | |
| username | nama pengguna | login (the name): nama login |
| password | kata sandi | |
| log in / sign in | masuk | the act: proses masuk |
| log out / sign out | keluar | |
| e-mail | email | e-mail address: alamat email |
| cache | cache | clear cache: bersihkan cache |
| template | templat | |
| override | penimpaan | override template: templat penimpaan; to override: menimpa |
| fallback | cadangan | fallback language: bahasa cadangan |
| design | desain | |
| workflow | alur kerja | |
| trigger | pemicu | |
| event | peristiwa | |
| trash | tempat sampah | empty trash: kosongkan tempat sampah; move to trash: pindahkan ke tempat sampah |
| bookmark | penanda | |
| settings | pengaturan | configuration: konfigurasi |
| setup | penyiapan | the Setup tab |
| layout | tata letak | Exponential Layouts |
| block | blok | |
| zone | zona | |
| extension | ekstensi | |
| package | paket | |
| search | cari | search (noun): pencarian; search index: indeks pencarian; indexing: pengindeksan |
| log | log | a log file: berkas log; never "log in" |
| queue | antrean | job queue: antrean tugas |
| job / task | tugas | |
| cron | cron | a cron job: tugas cron |
| script | skrip | |
| worker | proses pekerja | |
| kernel, core | inti | |
| server | server | |
| database | basis data | |
| folder | folder | |
| file | berkas | |
| image | gambar | thumbnail: gambar mini |
| preview | pratinjau | |
| upload | unggah | |
| download | unduh | |
| export / import | ekspor / impor | |
| remove | lepas, keluarkan | take out of a list or place: hapus dari ... |
| delete | hapus | destroy |
| edit | sunting | editing: penyuntingan |
| editor | penyunting | the editing software: editor |
| save / store | simpan | the verb "Store" of the forms |
| Store (shop tab) | toko | the shop, as "Shop" |
| cancel | batal | |
| OK | OK | kept, as in Indonesian software (allowlisted) |
| apply | terapkan | |
| back | kembali | |
| next / previous | berikutnya / sebelumnya | |
| enable / disable | aktifkan / nonaktifkan | |
| enabled / disabled | aktif / nonaktif | |
| active / inactive | aktif / tidak aktif | |
| run | jalankan | a script, a job |
| start / stop | mulai / hentikan | |
| session | sesi | |
| hidden / visible | tersembunyi / terlihat | hide / reveal: sembunyikan / tampilkan |
| archive | arsip | |
| order (sort) | urutan | sort order: urutan pengurutan |
| order (shop) | pesanan | |
| shop | toko | |
| basket | keranjang | |
| product | produk | |
| customer | pelanggan | |
| discount | diskon | |
| VAT | PPN | value added tax: pajak pertambahan nilai |
| price | harga | |
| currency | mata uang | |
| tag | tag | a keyword tag; an HTML tag: tag HTML |
| translation | terjemahan | |
| language | bahasa | main language: bahasa utama; interface language: bahasa antarmuka |
| locale | lokal | |
| state | keadaan | the object state |
| status | status | |
| view (button) | lihat | a module view (noun): tampilan |
| format | format | |
| sort | urutkan | |
| filter | filter | the act: saring |
| select | pilih | select all: pilih semua; selected: dipilih |
| browse | jelajahi | |
| copy | salin | |
| move | pindahkan | |
| add | tambah | |
| create | buat | |
| new | baru | |
| restore | pulihkan | |
| install / uninstall | pasang / copot | installation: pemasangan |
| sub items | subitem | |
| subtree | subpohon | |
| related objects | objek terkait | relation: relasi |
| URL alias | alias URL | |
| redirect | pengalihan | to redirect: alihkan |
| dashboard | dasbor | |
| content structure | struktur konten | |
| media library | pustaka media | |
| identifier / ID | pengenal / ID | |
| priority | prioritas | |
| owner | pemilik | |
| modified / created | diubah / dibuat | |
| date / time | tanggal / waktu | |
| error / warning | galat / peringatan | |
| help | bantuan | |
| notification | notifikasi | |
| wizard | wisaya | setup wizard: wisaya penyiapan |
| feed (RSS) | umpan | feed reader: pembaca umpan |
| tab | tab | |
| audit, audit trail | audit, jejak audit | |
| repository | repositori | |
| checksum | checksum | |
| account | akun | |
| default | bawaan | default value: nilai bawaan |
| toolbar | bilah alat | |
| container | kontainer | |
| AI | AI | kecerdasan buatan where the source spells it out |
| pattern (name pattern) | pola | wildcard: karakter pengganti |
| remote ID | ID jarak jauh | |
| placeholder | penampung | |
| collection | koleksi | information collector: pengumpul informasi |
| approve, approval | setujui, persetujuan | |
| software | perangkat lunak | |
| creator / modifier | pembuat / pengubah | |
| request (HTTP) | permintaan | |
| query | kueri | |
| handler / provider / listener | penangan / penyedia / pendengar | |
| engine | mesin | |
| release / upgrade | rilis / peningkatan | to upgrade: tingkatkan |
| backup | cadangan | to back up: cadangkan |
| maintenance | pemeliharaan | |
| directory | direktori | |
| header (HTTP) | header | |
| page layout (pagelayout template) | kerangka halaman | kept apart from tata letak (Exponential Layouts) |
| passkey | kunci sandi | |
| two-step sign-in | masuk dua langkah | |
| device / browser | perangkat / peramban | |
| link | tautan | |
| registration, register | pendaftaran, daftar | |
| payment gateway | gerbang pembayaran | |
| none | tidak ada | |
| yes / no | ya / tidak | |
| name | nama | |
| title | judul | |
| description | deskripsi | |
| value | nilai | |
| type | tipe | |
| list | daftar | |
| page | halaman | |
| path | jalur | |
| required | wajib | |
| optional | opsional | |
| options | opsi | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is toko, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is simpan.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes, acronyms and words spelled the same in Indonesian (Exponential, PHP, URL, ID, OK, Status,
  Format, Server, Email ...) are listed with their reason in
  `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
