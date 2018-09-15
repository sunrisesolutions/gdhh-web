JOIN phanbo va thanhvien
---
SELECT pb.id, pb.id_thanh_vien, pb.id_nam_hoc, tv.id as tv_id, pb.id_chi_doan, tv.chi_doan FROM `hoso__phan_bo` pb JOIN `hoso__thanh_vien` tv ON `pb`.id_thanh_vien = tv.id WHERE `id_nam_hoc` <= 2018 ORDER BY `id_nam_hoc` ASC
==============
==============
