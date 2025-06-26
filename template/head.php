<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta http-equiv="Content-Language" content="tr" />

<meta name="robots" content="index, follow" />

<meta http-equiv="X-UA-Compatible" content="IE=edge">
   
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="icon" type="image/png" href="img/stokicon.png" />

<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">

<link href="fontawesome/css/all.css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="css/style8.css">

<link rel="stylesheet" type="text/css" href="css/datepicker.css">

<script language="javascript" type="text/javascript">

    function ackapa(x) {

        if ($('#'+x).is(":hidden")) {

            $('#'+x).slideDown();

        }else{

            $('#'+x).hide(); 

        }

    } 

    function ackapa2(x,y) {

        if ($('#'+x).is(":hidden")) {

            $('#'+x).slideDown(0);

            $('#'+y).hide(0); 

        }else{

            $('#'+x).hide(); 

        }

    }

    function ackapa3(x,y,z) {

        if ($('#'+x).is(":hidden")) {

            $('#'+x).slideDown(0);

            $('#'+y).hide(0); 

            $('#'+z).hide(0); 

        }else{

            $('#'+x).hide(0);

        }

    }

    function ackapa4(x,y,z,t) {

        if ($('#'+x).is(":hidden")) {

            $('#'+x).slideDown(0);

            $('#'+y).hide(0); 

            $('#'+z).hide(0); 

            $('#'+t).hide(0); 

        }else{

            $('#'+x).hide(0);

        }

    }

    function printDiv(divName) {
    
        var printContents = document.getElementById(divName).innerHTML;
    
        var originalContents = document.body.innerHTML;
    
        document.body.innerHTML = printContents;
    
        window.print();
    
        document.body.innerHTML = originalContents;
    
    } 

</script>

<div id="overlay" class="overlay" onclick="closeModal()"></div>