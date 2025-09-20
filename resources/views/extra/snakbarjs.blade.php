<div id="snackbar">
</div>

<div id="snackbar2">
  
</div>


<script>

    function myFunction() {

      var x = document.getElementById("snackbar");

      x.className = "show";

      setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);

    }



    function myFunction2() {

      var x = document.getElementById("snackbar2");

      x.className = "show";

      setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);

    }

</script>

