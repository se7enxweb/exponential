-- description: An index for the news list (MySQL)
-- requires: 20261001100000_news_class
CREATE INDEX news_list ON ezcontentobject ( published ) USING BTREE;
