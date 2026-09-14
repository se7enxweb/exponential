--
-- Exponential 6.0.0 to 6.0.15, MySQL.
--
-- Note: no SET storage_engine here. That variable was removed in MySQL 5.7.6
-- and errors on anything newer, and nothing below creates a table anyway.
--

UPDATE ezsite_data SET value='6.0.15stable' WHERE name='ezpublish-version';
UPDATE ezsite_data SET value='1' WHERE name='ezpublish-release';

--
-- The pdf export carries its own footer wording.
--
-- Every page of every export used to read "Exponential PDF export", because
-- those words were written into content/pdf/footer.tpl and there was no way to
-- change them short of overriding the template. The wording now belongs to the
-- export and is set on its own edit page: show_footer says whether the footer
-- carries a line of text at all, footer_text says what it reads. Left empty
-- with the box still ticked, the wording that shipped is used, so an export
-- that predates this change looks exactly as it did.
--

ALTER TABLE ezpdf_export ADD COLUMN show_footer int(11) NOT NULL DEFAULT 1;
ALTER TABLE ezpdf_export ADD COLUMN footer_text varchar(255) NOT NULL DEFAULT '';
