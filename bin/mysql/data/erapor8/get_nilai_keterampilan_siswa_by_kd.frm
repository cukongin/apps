TYPE=VIEW
query=select `a`.`kompetensi_id` AS `kompetensi_id`,`a`.`anggota_rombel_id` AS `anggota_rombel_id`,`b`.`kompetensi_dasar_id` AS `kompetensi_dasar_id`,`c`.`pembelajaran_id` AS `pembelajaran_id`,`b`.`id_kompetensi` AS `id_kompetensi`,max(`c`.`bobot`) AS `bobot`,max(`a`.`nilai`) * max(`c`.`bobot`) AS `nilai_kd_keterampilan` from ((`erapor8`.`nilai` `a` join `erapor8`.`kd_nilai` `b` on(`b`.`kd_nilai_id` = `a`.`kd_nilai_id`)) join `erapor8`.`rencana_penilaian` `c` on(`c`.`rencana_penilaian_id` = `b`.`rencana_penilaian_id`)) where `a`.`deleted_at` is null and `b`.`deleted_at` is null and `c`.`deleted_at` is null group by `a`.`kompetensi_id`,`a`.`anggota_rombel_id`,`b`.`kompetensi_dasar_id`,`c`.`pembelajaran_id`,`b`.`id_kompetensi`
md5=dab786ceab8740e7f10ae09c6c7ace6e
updatable=0
algorithm=0
definer_user=root
definer_host=localhost
suid=2
with_check_option=0
timestamp=0001770820197628528
create-version=2
source=SELECT a.kompetensi_id, a.anggota_rombel_id, b.kompetensi_dasar_id, c.pembelajaran_id, b.id_kompetensi, max(c.bobot) AS bobot, max(a.nilai) * max(c.bobot) AS nilai_kd_keterampilan FROM nilai a JOIN kd_nilai b ON b.kd_nilai_id = a.kd_nilai_id JOIN rencana_penilaian c ON c.rencana_penilaian_id = b.rencana_penilaian_id WHERE a.deleted_at IS NULL AND b.deleted_at IS NULL AND c.deleted_at IS NULL GROUP BY a.kompetensi_id, a.anggota_rombel_id, b.kompetensi_dasar_id, c.pembelajaran_id, b.id_kompetensi
client_cs_name=utf8mb4
connection_cl_name=utf8mb4_unicode_ci
view_body_utf8=select `a`.`kompetensi_id` AS `kompetensi_id`,`a`.`anggota_rombel_id` AS `anggota_rombel_id`,`b`.`kompetensi_dasar_id` AS `kompetensi_dasar_id`,`c`.`pembelajaran_id` AS `pembelajaran_id`,`b`.`id_kompetensi` AS `id_kompetensi`,max(`c`.`bobot`) AS `bobot`,max(`a`.`nilai`) * max(`c`.`bobot`) AS `nilai_kd_keterampilan` from ((`erapor8`.`nilai` `a` join `erapor8`.`kd_nilai` `b` on(`b`.`kd_nilai_id` = `a`.`kd_nilai_id`)) join `erapor8`.`rencana_penilaian` `c` on(`c`.`rencana_penilaian_id` = `b`.`rencana_penilaian_id`)) where `a`.`deleted_at` is null and `b`.`deleted_at` is null and `c`.`deleted_at` is null group by `a`.`kompetensi_id`,`a`.`anggota_rombel_id`,`b`.`kompetensi_dasar_id`,`c`.`pembelajaran_id`,`b`.`id_kompetensi`
mariadb-version=100432
