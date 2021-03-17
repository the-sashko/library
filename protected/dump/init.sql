CREATE TYPE "share_type" AS ENUM ('twitter', 'telegram');

CREATE SEQUENCE "categories_id_seq"
INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE SEQUENCE "cron_jobs_id_seq"
INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 5 CACHE 1;

CREATE SEQUENCE "files_id_seq"
INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE SEQUENCE "share_id_seq"
INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE SEQUENCE "users_id_seq"
INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "users" (
    "id"            integer DEFAULT nextval('users_id_seq') NOT NULL,
    "email"         character varying(128) NOT NULL,
    "password_hash" character varying(128) NOT NULL,
    "token"         character varying(128),
    "cdate"         timestamp DEFAULT now() NOT NULL,
    "mdate"         timestamp,
    "is_ban"        boolean DEFAULT false NOT NULL,
    "ddate"         smallint,
    "is_active"     boolean DEFAULT true NOT NULL,

    CONSTRAINT "users_email" UNIQUE ("email"),
    CONSTRAINT "users_id" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE TABLE "categories" (
    "id"        integer DEFAULT nextval('categories_id_seq') NOT NULL,
    "id_parent" integer,
    "title"     character varying(128) NOT NULL,
    "slug"      character varying(64) NOT NULL,
    "cdate"     timestamp DEFAULT now() NOT NULL,
    "mdate"     timestamp,
    "id_user"   integer,
    "ddate"     timestamp,
    "is_active" boolean DEFAULT true NOT NULL,

    CONSTRAINT "categories_id" PRIMARY KEY ("id"),
    CONSTRAINT "categories_slug" UNIQUE ("slug"),
    CONSTRAINT "categories_title" UNIQUE ("title"),

    CONSTRAINT "categories_id_parent_fkey"
    FOREIGN KEY (id_parent) REFERENCES categories(id)
    ON UPDATE CASCADE ON DELETE SET NULL NOT DEFERRABLE,
    
    CONSTRAINT "categories_id_user_fkey"
    FOREIGN KEY (id_user) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL NOT DEFERRABLE
) WITH (oids = false);

CREATE TABLE "cron_jobs" (
    "id"               integer DEFAULT nextval('cron_jobs_id_seq') NOT NULL,
    "action"           character varying(128) NOT NULL,
    "interval"         numeric(8,0),
    "time_next_exec"   numeric(11,0) DEFAULT 0 NOT NULL,
    "last_exec_status" boolean DEFAULT true NOT NULL,
    "is_active"        boolean DEFAULT false NOT NULL,
    "error_message"    character varying(255),

    CONSTRAINT "cron_id" PRIMARY KEY ("id"),
    CONSTRAINT "cron_jobs_action_interval" UNIQUE ("action", "interval")
) WITH (oids = false);

CREATE TABLE "files" (
    "id"           integer DEFAULT nextval('files_id_seq') NOT NULL,
    "id_category"  integer NOT NULL,
    "title"        character varying(128) NOT NULL,
    "short_title"  character varying(64) NOT NULL,
    "slug"         character varying(64) NOT NULL,
    "description"  text,
    "cdate"        timestamp DEFAULT now() NOT NULL,
    "mdate"        timestamp,
    "file_path"    character varying(128) NOT NULL,
    "qr_path"      character varying(128) NOT NULL,
    "id_user"      integer,
    "ddate"        timestamp,
    "is_active"    boolean DEFAULT true NOT NULL,
    "views"        integer DEFAULT '0' NOT NULL,
    "preview_path" character varying(128) NOT NULL,

    CONSTRAINT "files_id" PRIMARY KEY ("id"),
    CONSTRAINT "files_short_title" UNIQUE ("short_title"),
    CONSTRAINT "files_slug" UNIQUE ("slug"),
    CONSTRAINT "files_title" UNIQUE ("title"),

    CONSTRAINT "files_id_category_fkey"
    FOREIGN KEY (id_category) REFERENCES categories(id)
    ON UPDATE CASCADE ON DELETE RESTRICT NOT DEFERRABLE,

    CONSTRAINT "files_id_user_fkey"
    FOREIGN KEY (id_user) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL NOT DEFERRABLE
) WITH (oids = false);

CREATE TABLE "share" (
    "id"         integer DEFAULT nextval('share_id_seq') NOT NULL,
    "id_file"    integer NOT NULL,
    "share_type" share_type NOT NULL,
    "cdate"      timestamp DEFAULT now() NOT NULL,

    CONSTRAINT "share_id" PRIMARY KEY ("id"),
    CONSTRAINT "share_id_file_share_type" UNIQUE ("id_file", "share_type"),

    CONSTRAINT "share_id_file_fkey"
    FOREIGN KEY (id_file) REFERENCES files(id)
    ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE
) WITH (oids = false);

CREATE INDEX "categories_id_is_active"
ON "categories" USING btree ("id", "is_active");

CREATE INDEX "categories_id_parent" ON "categories" USING btree ("id_parent");

CREATE INDEX "categories_id_parent_is_active"
ON "categories" USING btree ("id_parent", "is_active");

CREATE INDEX "categories_id_user" ON "categories" USING btree ("id_user");

CREATE INDEX "categories_is_active" ON "categories" USING btree ("is_active");

CREATE INDEX "categories_slug_is_active"
ON "categories" USING btree ("slug", "is_active");

CREATE INDEX "cron_action" ON "cron_jobs" USING btree ("action");

CREATE INDEX "cron_action_time_next_exec"
ON "cron_jobs" USING btree ("action", "time_next_exec");

CREATE INDEX "cron_action_time_next_exec_is_active"
ON "cron_jobs" USING btree ("action", "time_next_exec", "is_active");

CREATE INDEX "cron_is_active" ON "cron_jobs" USING btree ("is_active");

CREATE INDEX "cron_last_next_status"
ON "cron_jobs" USING btree ("last_exec_status");

CREATE INDEX "cron_time_next_exec"
ON "cron_jobs" USING btree ("time_next_exec");

CREATE INDEX "files_id_category" ON "files" USING btree ("id_category");

CREATE INDEX "files_id_is_active" ON "files" USING btree ("id", "is_active");

CREATE INDEX "files_id_user" ON "files" USING btree ("id_user");

CREATE INDEX "files_is_active" ON "files" USING btree ("is_active");

CREATE INDEX "files_slug_is_active"
ON "files" USING btree ("slug", "is_active");

CREATE INDEX "share_id_file" ON "share" USING btree ("id_file");

CREATE INDEX "share_share_type" ON "share" USING btree ("share_type");

CREATE INDEX "users_email_is_active"
ON "users" USING btree ("email", "is_active");

CREATE INDEX "users_id_is_active" ON "users" USING btree ("id", "is_active");

CREATE INDEX "users_is_active" ON "users" USING btree ("is_active");

CREATE INDEX "users_is_ban" ON "users" USING btree ("is_ban");

INSERT INTO "cron_jobs" (
    "id",
    "action",
    "interval",
    "time_next_exec",
    "last_exec_status",
    "is_active",
    "error_message"
) VALUES
(1, 'translations', 36000, 0, 't', 't', NULL),
(2, 'uploads',      600,   0, 't', 't', NULL),
(3, 'share',        300,   0, 't', 't', NULL),
(4, 'sitemap',      900,   0, 't', 't', NULL);
