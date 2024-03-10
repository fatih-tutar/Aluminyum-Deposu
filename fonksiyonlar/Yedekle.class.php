<?php
class Yedekle {
	/**
	 * Bağlantı kurulacak alanın aktarıldığı değişken
	 * @var
	 */
	private $host;
	/**
	 * Bağlantı aşamasında kullanacağımız değişken
	 * @var
	 */
	private $src;
	/**
	 * Bağlantı aşamasında kullanılan kullanıcı adı
	 * @var
	 */
	private $kadi;
	/**
	 * Bağlantı aşamasında kullanacağımız parola
	 * @var
	 */
	private $parola;
	/**
	 * Bağlanacağımız veritabanının adı
	 * @var
	 */
	private $vt_adi;
	/**
	 * PDO kullanarak veritabanına bağlanacağımız değişken
	 * @var
	 */
	private $bilgiler;
	/**
	 * Veritabanındaki tabloların dizi olarak tutulacağı değişken
	 * @var
	 */
	private $tablolar = array();
	/**
	 * Veritabanına bağlıyken kullanacağımız nesne
	 * @var
	 */
	private $islem;
	/**
	 * Alınan hataların dizi olarak aktarıldığı değişken
	 * @var
	 */
	private $hata = array();
	/**
	 * Başarılı olduğumuzda kullanacağımız sonuç dizesi
	 * @var
	 */
	private $son;


  

   	/**
	 * Veritabanı bağlantısı @uses private
	 */
	private function baglan(){
		try {
			$this->islem = new PDO($this->bilgiler.';charset=utf8;', $this->kadi, $this->parola);
			$this->islem->query('SET CHARACTER SET UTF8');
		} catch (PDOException $e) {
			$this->islem = null;
			$this->hata[] = $e->getMessage();
			return false;
		}
	}

	/**
	 * Tabloları al @uses private
	 */
	private function tablo_al(){
		try {
			$goster = $this->islem->query('SHOW TABLES');
			$bol = $goster->fetchAll();
			$indis = 0;
			foreach($bol as $tablo){
				$this->tablolar[$indis]['ad'] = $tablo[0];
				$this->tablolar[$indis]['olustur'] = $this->sutun_al($tablo[0]);
				$this->tablolar[$indis]['veri'] = $this->veri_al($tablo[0]);
				$indis++;
			}
			unset($goster);
			unset($bol);
			unset($indis);

			return true;
		} catch (PDOException $e) {
			$this->islem = null;
			$this->hata[] = $e->getMessage();
			return false;
		}
	}

	/**
	 *
	 * Sütunları al @uses private
	 */
	private function sutun_al($tablo_adi){
		try {
			$goster = $this->islem->query('SHOW CREATE TABLE '.$tablo_adi);
			$bol = $goster->fetchAll();
			$bol[0][1] = preg_replace("/AUTO_INCREMENT=[\w]*./", '', $bol[0][1]);
			return $bol[0][1];
		} catch (PDOException $e){
			$this->islem = null;
			$this->hata[] = $e->getMessage();
			return false;
		}
	}

	/**
	 *
	 * Tablolardan kayıtlı verileri al @uses private
	 */
	private function veri_al($tablo_adi){
		try {
			$goster = $this->islem->query('SELECT * FROM '.$tablo_adi);
			$bol = $goster->fetchAll(PDO::FETCH_NUM);
			$veri = '';
			foreach ($bol as $parcalar){
				foreach($parcalar as &$deger){
					$deger = addslashes($deger);
				}
				$veri .= 'INSERT INTO '. $tablo_adi .' VALUES (\'' . implode('\',\'', $parcalar) . '\');'."\n";
			}
			return $veri;
		} catch (PDOException $h){
			$this->islem = null;
			$this->hata[] = $h->getMessage();
			return false;
		}
	}

	public function Yedekle($parametre){
		if( !$parametre['host'] ){
			$this->hata[] = 'Host adresi geçersiz.';
		}
		if( !$parametre['kadi'] ){
			$this->hata[] = 'Kullanıcı adı geçersiz.';
		}
		if( !isset($parametre['parola']) ){
			$this->hata[] = 'Parola geçersiz.';
		}
		if( !$parametre['veritabani'] ){
			$this->hata[] = 'Veritabanı adı geçersiz.';
		}
		if( !$parametre['src'] ){
			$this->hata[] = 'Veritabanı türü geçersiz.';
		}

		if( count($this->hata) > 0 ){
			return;
		}

		$this->host = $parametre['host'];
		$this->tip = $parametre['src'];
		$this->kadi = $parametre['kadi'];
		$this->parola = $parametre['parola'];
		$this->vt_adi = $parametre['veritabani'];

		if($this->host=='localhost'){
			$this->host = '127.0.0.1';
		}
		$this->bilgiler = $this->tip.':host='.$this->host.';dbname='.$this->vt_adi;

		$this->baglan();
		$this->tablo_al();
		$this->olustur();
	}

	/**
	 * Yedek almak için çağırılan fonksiyon
	 * @example Yedekle::yedek();
	 */
	public function yedek(){
		if(count($this->hata)>0){
			return array('hata'=>true, 'mesaj'=>$this->hata);
		}
		return array('hata'=>false, 'mesaj'=>$this->son);
	}

	/**
	 * Yedek dizeleri oluşturur @uses private
	 */
	private function olustur(){
		foreach ($this->tablolar as $t) {
			$this->son .= $t['olustur'] . ";\n\n";
			$this->son .= $t['veri']."\n\n\n";
		}
	}
}
?>