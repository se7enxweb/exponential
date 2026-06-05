<h2>{"MongoDB"|i18n("design/standard/setup/db")}</h2>
<h3>{"Introduction"|i18n("design/standard/setup/db")}</h3>
<p>
  {"MongoDB is a source-available, cross-platform, document-oriented NoSQL database program developed by MongoDB, Inc."|i18n("design/standard/setup/db")}
  {"It stores data in flexible, JSON-like BSON documents, meaning fields can vary from document to document and data structure can be changed over time."|i18n("design/standard/setup/db")}
</p>
<p>{"From their homepage"|i18n("design/standard/setup/db")}:</p>
<div class="quote">{"MongoDB is a general purpose, document-based, distributed database built for modern application developers and for the cloud era. No database makes you more productive. MongoDB stores data in flexible, JSON-like documents, meaning fields can vary from document to document and data structure can be changed over time. The document model maps to the objects in your application code, making data easy to work with. Ad hoc queries, indexing, and real time aggregation provide powerful ways to access and analyze your data. MongoDB is a distributed database at its core, so high availability, horizontal scaling, and geographic distribution are built in and easy to use."|i18n("design/standard/setup/db")}</div>
<p>{"More information can be found on"|i18n("design/standard/setup/db")} <a href="https://www.mongodb.com">mongodb.com</a>.</p>

<h3>{"Details"|i18n("design/standard/setup/db")}</h3>
<p>{"MongoDB is an excellent choice for high-throughput applications that require flexible schema design, horizontal scalability, and full Unicode support."|i18n("design/standard/setup/db")}</p>
<p>{"Exponential CMS uses the sevenxMongoDB adapter which requires the PHP 'mongodb' PECL extension (version 1.5+) and the 'mongodb/mongodb' Composer package."|i18n("design/standard/setup/db")}</p>

<h3>{"Installation"|i18n("design/standard/setup/db")}</h3>
<p>{"To enable MongoDB support, install the PHP mongodb extension via PECL:"|i18n("design/standard/setup/db")}</p>
<pre>pecl install mongodb</pre>
<p>{"Then enable it in your php.ini:"|i18n("design/standard/setup/db")}</p>
<pre>extension=mongodb.so</pre>
<p>{"And install the MongoDB PHP library via Composer:"|i18n("design/standard/setup/db")}</p>
<pre>composer require mongodb/mongodb</pre>
<p>{"More information on the MongoDB PHP extension can be found at"|i18n("design/standard/setup/db")} <a href="https://www.php.net/manual/en/book.mongodb">php.net</a>.</p>
