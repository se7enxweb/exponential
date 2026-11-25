-- The DFS cluster metadata tables for Microsoft SQL Server 2017 and later and Azure SQL
-- (expDFSFileHandlerSQLServerBackend). Names are NVARCHAR(830) in a binary collation: case sensitive, as MySQL's
-- binary LIKE compares them, and within SQL Server's 1,700 byte index key with the 34 bytes of the clustered
-- name_hash a non-unique index adds. name_hash is in a Windows (BIN2) collation too: the backend binds every value as
-- NVARCHAR, and only a Windows collation lets SQL Server seek a VARCHAR key with an NVARCHAR value (a SQL_ collation,
-- the server's default in English, scans the table). filesize and mtime are BIGINT.

CREATE TABLE ezdfsfile (
  name NVARCHAR(830) COLLATE Latin1_General_100_BIN2 NOT NULL,
  name_trunk NVARCHAR(830) COLLATE Latin1_General_100_BIN2 NOT NULL,
  name_hash VARCHAR(34) COLLATE Latin1_General_100_BIN2 NOT NULL,
  datatype NVARCHAR(255) NULL CONSTRAINT DF_ezdfsfile_datatype DEFAULT N'application/octet-stream',
  scope NVARCHAR(25) NULL,
  filesize BIGINT NOT NULL CONSTRAINT DF_ezdfsfile_filesize DEFAULT 0,
  mtime BIGINT NOT NULL CONSTRAINT DF_ezdfsfile_mtime DEFAULT 0,
  expired SMALLINT NOT NULL CONSTRAINT DF_ezdfsfile_expired DEFAULT 0,
  CONSTRAINT PK_ezdfsfile PRIMARY KEY (name_hash)
);

CREATE INDEX ezdfsfile_name ON ezdfsfile (name);
CREATE INDEX ezdfsfile_name_trunk ON ezdfsfile (name_trunk);
CREATE INDEX ezdfsfile_mtime ON ezdfsfile (mtime);
CREATE INDEX ezdfsfile_expired_scope ON ezdfsfile (expired, scope);

CREATE TABLE ezdfsfile_cache (
  name NVARCHAR(830) COLLATE Latin1_General_100_BIN2 NOT NULL,
  name_trunk NVARCHAR(830) COLLATE Latin1_General_100_BIN2 NOT NULL,
  name_hash VARCHAR(34) COLLATE Latin1_General_100_BIN2 NOT NULL,
  datatype NVARCHAR(255) NULL CONSTRAINT DF_ezdfsfile_cache_datatype DEFAULT N'application/octet-stream',
  scope NVARCHAR(25) NULL,
  filesize BIGINT NOT NULL CONSTRAINT DF_ezdfsfile_cache_filesize DEFAULT 0,
  mtime BIGINT NOT NULL CONSTRAINT DF_ezdfsfile_cache_mtime DEFAULT 0,
  expired SMALLINT NOT NULL CONSTRAINT DF_ezdfsfile_cache_expired DEFAULT 0,
  CONSTRAINT PK_ezdfsfile_cache PRIMARY KEY (name_hash)
);

CREATE INDEX ezdfsfile_cache_name ON ezdfsfile_cache (name);
CREATE INDEX ezdfsfile_cache_name_trunk ON ezdfsfile_cache (name_trunk);
CREATE INDEX ezdfsfile_cache_mtime ON ezdfsfile_cache (mtime);
CREATE INDEX ezdfsfile_cache_expired_scope ON ezdfsfile_cache (expired, scope);
