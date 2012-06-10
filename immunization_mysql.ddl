create database immunization character set utf8;

use immunization;

create table clinic(clinic_id int auto_increment, passwd varchar(20), name varchar(50), yomi varchar(100), zipcode varchar(8), location1 varchar(255), location2 varchar(255), tel varchar(13), email varchar(50), index(clinic_id));

create table person(person_id int auto_increment, clinic_id int, patient_id varchar(20), family_name varchar(10), family_name_yomi varchar(20), personal_name varchar(10), personal_name_yomi varchar(20), birthday varchar(10), zipcode varchar(8), location1 varchar(255), location2 varchar(255), tel varchar(13), email varchar(50), index(person_id));

create table immunization(immunization_id int auto_increment, immunization_name varchar(30), regular varchar(2), kinds varchar(3), frequency int, comment varchar(255), timestamp varchar(10), index(immunization_id));

create table immunization_term(detail_id varchar(4), term_start int, term_end int);

create table book(person_id int, immunization_id int, number int, day varchar(10), lot_num varchar(20), state int);

insert into immunization values(null, 'インフルエンザb型(ヒブ)', '任意', '不活性', 4, '7ヶ月ー11ヶ月で初回接種：2回接種の1年後に追加1回接種。1歳ー4歳で初回接種：1回接種のみ','2012_05_06');
insert into immunization values(null, '肺炎球菌(PCV7)', '任意', '不活性', 4, '7ヶ月ー11ヶ月で初回接種：2回接種後60日以上あけて1歳以降に追加1回接種。1歳ー23ヶ月で初回接種：1回目と2回目の接種を60日以上あける。2歳ー9歳以下初回接種：1回接種のみ','2012_05_06');
insert into immunization values(null, 'B型肝炎(HBV)', '任意', '不活性', 3, '10歳以上の接種：乳児期に接種していない児の水平感染予防のための接種','2012_05_06');
insert into immunization values(null, 'ロタウイルス', '任意', '生', 2, '計2回、2回目の接種は生後6ヶ月までに完了すること','2012_05_06');
insert into immunization values(null, '三種混合(DPT)', '定期', '不活性', 4, '生後3ヶ月から生後90ヶ月未満の児が対象','2012_05_06');
insert into immunization values(null, 'BCG', '定期', '生', 1, 'やむを得ない事情を有する場合のみ1歳まで定期接種可能','2012_05_06');
insert into immunization values(null, 'ポリオ', '定期', '生', 2, 'や生後3ヶ月から生後90ヶ月未満の児が対象、不活性ポリオワクチンへの移行が進行中','2012_05_06');
insert into immunization values(null, '麻しん、風しん(MR)', '定期', '生', 2, '期間限定の処置あり。','2012_05_06');
insert into immunization values(null, '水痘', '定期', '生', 2, 'ワクチン接種によって自然感染によるブースターがなくなると、2回接種が必要','2012_05_06');
insert into immunization values(null, 'おたふくかぜ', '定期', '生', 2, 'ワクチン接種によって自然感染によるブースターがなくなると、2回接種が必要','2012_05_06');
insert into immunization values(null, '日本脳炎', '定期', '不活性', 4, '後から接種するものに対する異なるスケジュールあり','2012_05_06');
insert into immunization values(null, 'インフルエンザ', '任意', '不活性', 2, '13歳未満は2回接種、13歳以上は1回接種','2012_05_06');
insert into immunization values(null, '2種混合(DT)', '定期', '不活性', 1, '11歳以上13歳未満','2012_05_06');
insert into immunization values(null, 'ヒトパピローマウイルス(HPV) - 2価ワクチン', '任意', '不活性', 3, '1回目接種と2回目接種の間は1ヶ月、1回目から3回目の間は6ヶ月あける','2012_05_06');
insert into immunization values(null, 'ヒトパピローマウイルス(HPV) - 4価ワクチン', '任意', '不活性', 3, '1回目接種と2回目接種の間は2ヶ月、1回目から3回目の間は6ヶ月あける','2012_05_06');
insert into immunization values(null, 'A型肝炎', '任意', '不活性', 3, '2ー4周間隔で2回、24周を経過した後に1回、合計3回接種','2012_05_06');

insert into immunization_term values("1_1", 2, 3);
insert into immunization_term values("1_2", 3, 4);
insert into immunization_term values("1_3", 4, 5);
insert into immunization_term values("1_4", 12, 24);
insert into immunization_term values("2_1", 2, 3);
insert into immunization_term values("2_2", 3, 4);
insert into immunization_term values("2_3", 4, 5);
insert into immunization_term values("2_4", 12, 18);
insert into immunization_term values("3_1", 2, 3);
insert into immunization_term values("3_2", 3, 4);
insert into immunization_term values("3_3", 5, 12);
insert into immunization_term values("4_1", 2, 3);
insert into immunization_term values("4_2", 3, 4);
insert into immunization_term values("5_1", 3, 4);
insert into immunization_term values("5_2", 4, 5);
insert into immunization_term values("5_3", 5, 12);
insert into immunization_term values("5_4", 12, 24);
insert into immunization_term values("6_1", 3, 5);
insert into immunization_term values("7_1", 3, 12);
insert into immunization_term values("7_2", 12, 24);
insert into immunization_term values("8_1", 12, 24);
insert into immunization_term values("8_2", 60, 72);
insert into immunization_term values("9_1", 12, 18);
insert into immunization_term values("9_2", 60, 84);
insert into immunization_term values("10_1", 12, 18);
insert into immunization_term values("10_2", 60, 84);
insert into immunization_term values("11_1", 24, 36);
insert into immunization_term values("11_2", 24, 36);
insert into immunization_term values("11_3", 36, 48);
insert into immunization_term values("11_4", 108, 121);
insert into immunization_term values("12_1", 6, 157);
insert into immunization_term values("12_2", 6, 156);
insert into immunization_term values("13_1", 132, 144);
insert into immunization_term values("14_1", 120, 0);
insert into immunization_term values("14_2", 121, 0);
insert into immunization_term values("14_3", 126, 0);
insert into immunization_term values("15_1", 108, 0);
insert into immunization_term values("15_2", 110, 0);
insert into immunization_term values("15_3", 114, 0);
insert into immunization_term values("16_1", 192, 0);
insert into immunization_term values("16_2", 193, 0);
insert into immunization_term values("16_3", 196, 0);
