--sfcpeep格納用テーブル
--正規化とかはのちほど(>_<)
drop table sfcpeep;
CREATE TABLE sfcpeep (
	felica_idm  CHAR (16), 
	history_num	CHAR(6),
	terminal_code	CHAR(2),
	process		VARCHAR(40),
	in_line_code	CHAR(2),
	in_station_code	CHAR(2),
	in_company_name	VARCHAR(60),
	in_station_name	VARCHAR(60),
	out_line_code	CHAR(2),
	out_station_code	CHAR(2),
	out_company_name	VARCHAR(60),
	out_station_name	VARCHAR(60),
	balance	int,
	CONSTRAINT sfcpeepkey PRIMARY KEY(felica_Idm, history_num)
);
