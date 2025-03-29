-- Adminer 4.8.1 PostgreSQL 10.22 dump

DROP TABLE IF EXISTS "route_link";
CREATE TABLE "public"."route_link" (
    "id" bigint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1 ),
    "route_id" integer NOT NULL,
    "stop_id" integer NOT NULL,
    "stop_sequence" integer NOT NULL,
    CONSTRAINT "route_link_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "route_link" ("route_id", "stop_id", "stop_sequence") VALUES
(1,	1,	1),
(1,	2,	2),
(1,	3,	3),
(1,	4,	4),
(1,	5,	5),
(1,	6,	6),
(1,	7,	7),
(2,	1,	1),
(2,	3,	2),
(2,	5,	3),
(2,	7,	4),
(3,	1,	7),
(3,	2,	6),
(3,	3,	5),
(3,	4,	4),
(3,	5,	3),
(3,	6,	2),
(3,	7,	1);

DROP TABLE IF EXISTS "routes";
CREATE TABLE "public"."routes" (
    "id" bigint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1 ),
    "name" character varying(255) NOT NULL,
    CONSTRAINT "routes_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "routes" ("name") VALUES
('Автобус №0'),
('Автобус №1'),
('Автобус №2');

DROP TABLE IF EXISTS "schedules";
CREATE TABLE "public"."schedules" (
    "id" bigint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1 ),
    "route_link_id" integer NOT NULL,
    "departure_time" character varying(255) NOT NULL,
    CONSTRAINT "schedules_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "schedules" ("route_link_id", "departure_time") VALUES
(1,	'08:00'),
(1,	'09:00'),
(1,	'10:00'),
(1,	'11:00'),
(8,	'08:15'),
(8,	'09:15'),
(8,	'10:15'),
(12,	'08:30'),
(12,	'09:30'),
(12,	'10:30'),
(3,	'08:45'),
(3,	'09:45'),
(3,	'10:45'),
(3,	'11:45'),
(10,	'08:46'),
(10,	'09:46'),
(10,	'10:46'),
(10,	'11:46'),
(16,	'08:55'),
(16,	'09:55'),
(16,	'10:55'),
(16,	'11:55'),
(8,	'11:15');

DROP TABLE IF EXISTS "stops";
CREATE TABLE "public"."stops" (
    "id" bigint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1 ),
    "name" character varying(255) NOT NULL,
    CONSTRAINT "stops_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "stops" ("name") VALUES
('Пионерстроя'),
('проспект Ветеранов д.149'),
('Лётчика Пилютова'),
('Пограничника Гарькавого'),
('Тамбасова'),
('Добровольцев'),
('Партизана Германа');