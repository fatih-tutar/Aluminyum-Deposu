<script src="js/jquery-3.3.1.slim.min.js"></script>

<script src="js/popper.min.js"></script>

<script src="js/bootstrap.min.js"></script>

<script src="js/jquery-2.2.4.min.js" type="text/javascript"></script>

<script src="js/jquery-ui.js"></script>

<script type="text/javascript">

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

    $(document).ready(function(){

        var p = 0;

        $('.client-search-box input[type="text"]').on("keyup input", function(){

            /* Input Box'da değişiklik olursa aşağıdaki durumu çalıştırıyoruz. */

            var inputVal = $(this).val();

            var resultDropdown = $(this).siblings(".clientliveresult");

            if(inputVal.length < 10){

                $.get('client-live-search.php', {term: inputVal}).done(function(data){

                    /* Gelen sonucu ekrana yazdırıyoruz. */

                    resultDropdown.html(data);

                });

            }//else{

            // resultDropdown.empty();

            //}

        });

        /* Sonuç listesinden üzerinde tıklanıp bir öğe seçilirse input box'a yazdırıyoruz. */

        $(document).on("click", ".clientliveresult li", function(){

            $(this).parents(".client-search-box").find('input[type="text"]').val($(this).text());

            $(this).parent(".clientliveresult").empty();

        });

        $(document).on("click", function(e){
            var container = $(".client-search-box");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.find(".clientliveresult").empty();
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

    function confirmForm(message) {
        return confirm(message);
    }

    function openModal(divId) {
        document.getElementById(divId).style.display = "block";
        document.getElementById("overlay").style.display = "block";
        var id = divId.replace("edit-div-", "");
        calculateDayDifferenceWithId(id);
    }

    function closeModal(divId = null) {
        if (divId) {
            const modal = document.getElementById(divId);
            if (modal && modal.style.display === "block") {
                modal.style.display = "none";
            }
        } else {
            document.querySelectorAll(".modal").forEach(modal => {
                if (modal.style.display === "block") {
                    modal.style.display = "none";
                }
            });
            const overlay = document.getElementById("overlay");
            if (overlay) {
                overlay.style.display = "none";
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('menuToggleBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');
        const mainCol = document.getElementById('mainCol');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('d-none');
                mainCol.classList.toggle('col-md-12');
                mainCol.classList.toggle('col-md-9');
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                sidebar.classList.add('d-none');
                mainCol.classList.remove('col-md-9');
                mainCol.classList.add('col-md-12');
            });
        }
    });

</script>
