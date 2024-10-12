<script src="js/jquery-3.3.1.slim.min.js"></script>

<script src="js/popper.min.js"></script>

<script src="js/bootstrap.min.js"></script>

<script src="js/jquery-2.2.4.min.js" type="text/javascript"></script>

<script src="js/jquery-ui.js"></script>

<script type="text/javascript">

    function hesaplaGunFarki() {
        var baslangicTarihi = document.getElementById("izin_baslangic_tarihi").value;
        var bitisTarihi = document.getElementById("ise_baslama_tarihi").value;

        if (baslangicTarihi && bitisTarihi) {
            var baslangic = new Date(baslangicTarihi);
            var bitis = new Date(bitisTarihi);

            var farkZaman = bitis.getTime() - baslangic.getTime();
            var gunFarki = farkZaman / (1000 * 3600 * 24); // Milisaniyeleri gün cinsinden hesaplama

            if (gunFarki >= 0) {
                document.getElementById("gun_sayisi").value = gunFarki;
            } else {
                document.getElementById("gun_sayisi").value = 0;
            }
        }
    }
    // Her iki tarih girişini dinleyen olay tetikleyicisi
    document.getElementById("izin_baslangic_tarihi").addEventListener("change", hesaplaGunFarki);
    document.getElementById("ise_baslama_tarihi").addEventListener("change", hesaplaGunFarki);

    $( function() {

        for (var id = 1; id < 1000; id++) {

             $( "#tarih"+id ).datepicker({

                dateFormat: "dd-mm-yy",

                altFormat: "yy-mm-dd",
     
                altField:"#tarih-db",
                
                monthNames: [ "Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık" ],
                
                dayNamesMin: [ "Pa", "Pt", "Sl", "Ça", "Pe", "Cu", "Ct" ],
                
                firstDay:1
            
            });

        }
     
    } );

	
	$(document).ready(function(){

        var p = 0;

        $('.search-box input[type="text"]').on("keyup input", function(){

            /* Input Box'da değişiklik olursa aşağıdaki durumu çalıştırıyoruz. */

            var inputVal = $(this).val();

            var resultDropdown = $(this).siblings(".liveresult");

            if(inputVal.length < 10){

                $.get('live-search.php', {term: inputVal}).done(function(data){

                    /* Gelen sonucu ekrana yazdırıyoruz. */

                    resultDropdown.html(data);

                });

            }//else{

               // resultDropdown.empty();

            //}

        });

        /* Sonuç listesinden üzerinde tıklanıp bir öğe seçilirse input box'a yazdırıyoruz. */

        $(document).on("click", ".liveresult li", function(){

            $(this).parents(".search-box").find('input[type="text"]').val($(this).text());

            $(this).parent(".liveresult").empty();

        });

        $(document).on("click", function(e){
            var container = $(".search-box");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.find(".liveresult").empty();
            }
        });

    }); 

    $(document).ready(function(){

        var p = 0;

        $('.urun-search-box input[type="text"]').on("keyup input", function(){

            /* Input Box'da değişiklik olursa aşağıdaki durumu çalıştırıyoruz. */

            var inputVal = $(this).val();

            var resultDropdown = $(this).siblings(".urunliveresult");

            if(inputVal.length < 10){

                $.get('urun-live-search.php', {term: inputVal}).done(function(data){

                    /* Gelen sonucu ekrana yazdırıyoruz. */

                    resultDropdown.html(data);

                });

            }//else{

            // resultDropdown.empty();

            //}

        });

        /* Sonuç listesinden üzerinde tıklanıp bir öğe seçilirse input box'a yazdırıyoruz. */

        $(document).on("click", ".urunliveresult li", function(){

            $(this).parents(".urun-search-box").find('input[type="text"]').val($(this).text());

            $(this).parent(".urunliveresult").empty();

        });

        $(document).on("click", function(e){
            var container = $(".urun-search-box");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.find(".urunliveresult").empty();
            }
        });

    }); 

    function yuzdeinputuac(){

        $durum = document.getElementById('yuzdeinputu').style.display;

        if ($durum == "none") {

            document.getElementById('yuzdeinputu').style.display="block";

        }else{

            document.getElementById('yuzdeinputu').style.display="none";

        }        

    }

    function degergoster() {
        var selectkutu = document.getElementById('selectkutuID');
        var selectkutu_value = selectkutu.options[selectkutu.selectedIndex].value;
        var selectkutu_text = selectkutu.options[selectkutu.selectedIndex].text;

        if (selectkutu_value == '1') {

            document.getElementById('malzeme1').style.display="block";
            document.getElementById('malzeme2').style.display="none";
            document.getElementById('malzeme3').style.display="none";
            document.getElementById('malzeme4').style.display="none";
            document.getElementById('malzeme5').style.display="none";
            
        }else if (selectkutu_value == '2') {

            document.getElementById('malzeme1').style.display="none";
            document.getElementById('malzeme2').style.display="block";
            document.getElementById('malzeme3').style.display="none";
            document.getElementById('malzeme4').style.display="none";
            document.getElementById('malzeme5').style.display="none";
            
        }else if (selectkutu_value == '3') {

            document.getElementById('malzeme1').style.display="none";
            document.getElementById('malzeme2').style.display="none";
            document.getElementById('malzeme3').style.display="block";
            document.getElementById('malzeme4').style.display="none";
            document.getElementById('malzeme5').style.display="none";
            
        }else if (selectkutu_value == '4') {

            document.getElementById('malzeme1').style.display="none";
            document.getElementById('malzeme2').style.display="none";
            document.getElementById('malzeme3').style.display="none";
            document.getElementById('malzeme4').style.display="block";
            document.getElementById('malzeme5').style.display="none";
            
        }else if (selectkutu_value == '5') {

            document.getElementById('malzeme1').style.display="none";
            document.getElementById('malzeme2').style.display="none";
            document.getElementById('malzeme3').style.display="none";
            document.getElementById('malzeme4').style.display="none";
            document.getElementById('malzeme5').style.display="block";
            
        }
    }

</script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-156936360-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-156936360-1');
</script>
