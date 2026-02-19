TYPE=VIEW
query=select `a`.`nilai` AS `nilai`,`a`.`tp_nilai_id` AS `tp_nilai_id`,`a`.`anggota_rombel_id` AS `anggota_rombel_id`,`b`.`tp_id` AS `tp_id`,`c`.`pembelajaran_id` AS `pembelajaran_id`,`c`.`rencana_penilaian_id` AS `rencana_penilaian_id`,round(`a`.`nilai`,0) AS `nilai_kurmer` from ((`erapor8`.`nilai_tp` `a` join `erapor8`.`tp_nilai` `b` on(`b`.`tp_nilai_id` = `a`.`tp_nilai_id`)) join `erapor8`.`rencana_penilaian` `c` on(`c`.`rencana_penilaian_id` = `b`.`rencana_penilaian_id`)) where `c`.`deleted_at` is null group by `a`.`nilai`,`a`.`tp_nilai_id`,`a`.`anggota_rombel_id`,`b`.`tp_id`,`c`.`pembelajaran_id`,`c`.`rencana_penilaian_id`
md5=a3abad941c6105e3176addee4423368c
updatable=0
algorithm=0
definer_user=root
definer_host=localhost
suid=2
with_check_option=0
timestamp=0001770807380699453
create-version=2
source=SELECT a.nilai, a.tp_nilai_id, a.anggota_rombel_id, b.tp_id, c.pembelajaran_id, c.rencana_penilaian_id, round(a.nilai) AS nilai_kurmer FROM nilai_tp a JOIN tp_nilai b ON b.tp_nilai_id = a.tp_nilai_id JOIN rencana_penilaian c ON c.rencana_penilaian_id = b.rencana_penilaian_id WHERE c.deleted_at IS NULL GROUP BY a.nilai, a.tp_nilai_id, a.anggota_rombel_id, b.tp_id, c.pembelajaran_id, c.rencana_penilaian_id
client_cs_name=utf8mb4
connection_cl_name=utf8mb4_unicode_ci
view_body_utf8=select `a`.`nilai` AS `nilai`,`a`.`tp_nilai_id` AS `tp_nilai_id`,`a`.`anggota_rombel_id` AS `anggota_rombel_id`,`b`.`tp_id` AS `tp_id`,`c`.`pembelajaran_id` AS `pembelajaran_id`,`c`.`rencana_penilaian_id` AS `rencana_penilaian_id`,round(`a`.`nilai`,0) AS `nilai_kurmer` from ((`erapor8`.`nilai_tp` `a` join `erapor8`.`tp_nilai` `b` on(`b`.`tp_nilai_id` = `a`.`tp_nilai_id`)) join `erapor8`.`rencana_penilaian` `c` on(`c`.`rencana_penilaian_id` = `b`.`rencana_penilaian_id`)) where `c`.`deleted_at` is null group by `a`.`nilai`,`a`.`tp_nilai_id`,`a`.`anggota_rombel_id`,`b`.`tp_id`,`c`.`pembelajaran_id`,`c`.`rencana_penilaian_id`
mariadb-version=100432
