<script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script type="text/javascript">

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

    function yuzdeinputuac(elementId) {
        var elem = document.getElementById(elementId);
        if (!elem) return; // Eğer element bulunamazsa fonksiyon çıkıyor

        if (elem.style.display === "none" || elem.style.display === "") {
            elem.style.display = "block";
        } else {
            elem.style.display = "none";
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

    /**
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('menuToggleBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');
        const mainCol = document.getElementById('mainCol');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('d-none');
                mainCol.classList.toggle('col-md-12');
                mainCol.classList.toggle('col-md-10');
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                sidebar.classList.add('d-none');
                mainCol.classList.remove('col-md-10');
                mainCol.classList.add('col-md-12');
            });
        }
    });
     **/

    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const menuBtn = document.getElementById('menuToggleBtn');
        const closeBtn = document.getElementById('closeSidebar');

        // Menü butonuna tıklanınca sidebar açılır
        if (menuBtn) {
            menuBtn.addEventListener('click', function (e) {
                e.preventDefault(); // olası form davranışını engelle
                sidebar.classList.add('active');
            });
        }

        // Çarpıya tıklanınca sidebar kapanır
        if (closeBtn) {
            closeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                sidebar.classList.remove('active');
            });
        }

        // Sidebar dışına tıklanırsa da kapanır (isteğe bağlı)
        document.addEventListener('click', function (event) {
            if (
                sidebar.classList.contains('active') &&
                !sidebar.contains(event.target) &&
                event.target !== menuBtn
            ) {
                sidebar.classList.remove('active');
            }
        });
    });

    function toggleAccordion(header) {
        const content = header.nextElementSibling;
        const isActive = header.classList.contains('active');

        // Kapat
        document.querySelectorAll('.accordion-header').forEach(h => {
            h.classList.remove('active');
            if (window.innerWidth <= 768) h.nextElementSibling.style.display = 'none';
        });

        // Eğer kapalıysa aç
        if (!isActive && window.innerWidth <= 768) {
            header.classList.add('active');
            content.style.display = 'block';
        }
    }
</script>
