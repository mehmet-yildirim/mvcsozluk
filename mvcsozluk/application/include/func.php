<?php

/**
 * sözlükerciyes (c) 2008
 * 
 * func.php - Genel fonksiyonlar
 * 
 * @author Mehmet Yıldırım <myildirim2007@gmail.com>
 */


/**
 * 
 * Kullanıcı karma puanı hesaplama fonksiyonu
 * 
 * Karma puanı crontab ile günlük olarak hesaplanıp kullanıcı tablosunun karma değerine yazılır.
 * crontab için hergün saat gece 04:30 seçilmiştir.
 * @param string $username
 * @param integer $total_user_count
 * @param integer $total_entry_count
 */
function user_karma($username,$total_user_count,$total_entry_count){
	global $database;
	
	$karma_limit = 1000;
	$vote_min_limit = round($total_user_count * 0.1);
	$entry_min_limit = round($total_entry_count * 0.0005);
	$diff = -3;
	$time_high = time();
	$time_low = $time_high - (3600 * 24 * 365);
	
	$e_query = "SELECT COUNT(o.id) AS say FROM oylar o INNER JOIN mesajlar m ON o.entry_id = m.id WHERE o.entry_sahibi='$username' AND m.statu='' AND m.arsiv = 0";
	$entry_count = $database->sql_fetchrow($database->sql_query($e_query));
	(int) $entry_count = $entry_count["say"];

	$query = "SELECT o.oyveren FROM oylar o INNER JOIN mesajlar m ON o.entry_id = m.id WHERE o.entry_sahibi='$username' AND m.statu='' AND m.arsiv = 0 AND o.oytarihi <= '$time_high' AND o.oytarihi >= '$time_low' GROUP BY o.oyveren";
	$result = $database->sql_query($query);
	$user_array = $database->sql_fetchrowset($result);
	(int) $user_count = $database->sql_numrows($result);
	$avr_sum = 0;
	if($user_count >= $vote_min_limit && $entry_count >= $entry_min_limit){
		foreach ($user_array as $user) {
			$vote_query = "SELECT (SELECT COUNT(oy) FROM oylar WHERE oylar.entry_sahibi=o.entry_sahibi AND oylar.oyveren=o.oyveren AND oy = 1) AS oy_1,(SELECT COUNT(oy) FROM oylar WHERE oylar.entry_sahibi=o.entry_sahibi AND oylar.oyveren=o.oyveren AND oy = 2) AS oy_2,(SELECT COUNT(oy) FROM oylar WHERE oylar.entry_sahibi=o.entry_sahibi AND oylar.oyveren=o.oyveren AND oy = 3) AS oy_3,(SELECT COUNT(oy) FROM oylar WHERE oylar.entry_sahibi=o.entry_sahibi AND oylar.oyveren=o.oyveren AND oy = 4) AS oy_4,(SELECT COUNT(oy) FROM oylar WHERE oylar.entry_sahibi=o.entry_sahibi AND oylar.oyveren=o.oyveren AND oy = 5) AS oy_5 FROM oylar o INNER JOIN mesajlar m ON o.entry_id = m.id WHERE o.entry_sahibi='$username' AND m.statu='' AND m.arsiv = 0 AND o.oyveren='".$user["oyveren"]."' AND o.oytarihi <= '$time_high' AND o.oytarihi >= '$time_low' GROUP BY o.oyveren";
			
			$vote_result = $database->sql_query($vote_query);
			$vote_row = $database->sql_fetchrow($vote_result);
			(int) $v1 = $vote_row["oy_1"];
			(int) $v2 = $vote_row["oy_2"];
			(int) $v3 = $vote_row["oy_3"];
			(int) $v4 = $vote_row["oy_4"];
			(int) $v5 = $vote_row["oy_5"];
			
			$vt = $v1 + $v2 + $v3 + $v4 + $v5;
			$eff = sqrt($vt) - sqrt($vt - 1);
			if($vt != 0){
				$avr = ((($v1 * (1 + $diff) * $eff) + ($v2 * (2 + $diff) * $eff) + ($v3 * (3 + $diff) * $eff) + ($v4 * (4 + $diff) * $eff) + ($v5 * (5 + $diff) * $eff)) / $vt);
			}else{
				$avr = 0;
			}
			
			$avr_sum += $avr;
	 	}
	}
 	(int) $karma = round(($karma_limit / $total_user_count) * $avr_sum);
	
 	return $karma;
}

/**
 * 
 * Karma değerine göre yazılacak eğlenceli metin :)
 * Kullanıcının profil sayfasında görülür.
 * @param integer $k karma puanı
 */
function karma_text($k){
	if($k < -900){
		$t = "kara delik";
	}else if($k < -875){
		$t = "interpol kaçağı";
	}else if($k < -850){
		$t = "ajan smith";
	}else if($k < -800){
		$t = "köyün biricik delisi";
	}else if($k < -750){
		$t = "gulyabani";
	}else if($k < -700){
		$t = "küçük emrah";
	}else if($k < -675){
		$t = "balta sapı";
	}else if($k < -650){
		$t = "at hırsızı";
	}else if($k < -625){
		$t = "boş gezenin boş kalfası";
	}else if($k < -600){
		$t = "serseri mayın";
	}else if($k < -575){
		$t = "zibidi";
	}else if($k < -550){
		$t = "zehirli elma";
	}else if($k < -500){
		$t = "tuz gölündeki balık";
	}else if($k < -480){
		$t = "karaborsacı";
	}else if($k < -450){
		$t = "deccal";
	}else if($k < -425){
		$t = "tomruk";
	}else if($k < -410){
		$t = "mide bulantısı";
	}else if($k < -400){
		$t = "keskin sirke";
	}else if($k < -380){
		$t = "çamaşır suyu";
	}else if($k < -350){
		$t = "gollum";
	}else if($k < -300){
		$t = "saman nezlesi";
	}else if($k < -280){
		$t = "dünyayı kurtaran adam";
	}else if($k < -250){
		$t = "saruman";
	}else if($k < -230){
		$t = "dış kapının mandalı";
	}else if($k < -200){
		$t = "yolgeçen hanı";
	}else if($k < -180){
		$t = "gaydiriguppak";
	}else if($k < -150){
		$t = "orta şut karışımı";
	}else if($k < -120){
		$t = "yerden bitme";
	}else if($k < -100){
		$t = "ukala dümbeleği";
	}else if($k < -80){
		$t = "idealist";
	}else if($k < -50){
		$t = "zıpçıktı";
	}else if($k < -30){
		$t = "uzaylı";
	}else if($k < 0){
		$t = "yeniyetme";
	}else if($k < 10){
		$t = "ademoğlu";
	}else if($k < 30){
		$t = "çiçeği burnunda";
	}else if($k < 50){
		$t = "iki arada bir derede";
	}else if($k < 80){
		$t = "yakışıklı ama sempatik";
	}else if($k < 100){
		$t = "aklı başında";
	}else if($k < 130){
		$t = "göz dolduran";
	}else if($k < 150){
		$t = "frodo baggins";
	}else if($k < 180){
		$t = "kaptan tsubasa";
	}else if($k < 200){
		$t = "sırlar odası";
	}else if($k < 230){
		$t = "açık pencere";
	}else if($k < 250){
		$t = "tadından yenmeyen";
	}else if($k < 280){
		$t = "felsefe taşı";
	}else if($k < 300){
		$t = "nuri leflef";
	}else if($k < 330){
		$t = "anadolu delikanlısı";
	}else if($k < 350){
		$t = "ağır abi";
	}else if($k < 380){
		$t = "kabadayı";
	}else if($k < 400){
		$t = "mangal yürekli";
	}else if($k < 430){
		$t = "veli nimet";
	}else if($k < 450){
		$t = "ortam insanı";
	}else if($k < 480){
		$t = "her eve lazım";
	}else if($k < 500){
		$t = "tanınmamış kahraman";
	}else if($k < 550){
		$t = "aragorn";
	}else if($k < 600){
		$t = "bağır taşı";
	}else if($k < 650){
		$t = "uyuyan dev";
	}else if($k < 700){
		$t = "güç yüzüğü";
	}else if($k < 750){
		$t = "rakının yanındaki kavun";
	}else if($k < 800){
		$t = "reyting uzmanı";
	}else if($k < 850){
		$t = "toros kaplanı";
	}else if($k < 900){
		$t = "gandalf";
	}else if($k <= 1000){
		$t = "insanüstü varlık";
	}
	return "(" . $t . ")";
}

?>


