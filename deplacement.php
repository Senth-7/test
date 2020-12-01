<?php require_once('login/session.php');?>

<!DOCTYPE html>
<html lang="fr">
<?php require_once('head.php'); ?>

<body id="page-top">

  <!-- Navigation -->
  <?php require_once('nav.php'); ?>
	<!-- Header -->
	<?php require_once('header.php'); ?>

  <section id="about">
    <div class="row" id="titre-form">
      <div class="col-lg-10 titre">              
        <h2>2. TRAÇAGE </h2>
        <p class="lead">Pour déclarer un déplacement,<br> tapez une adresse dans la barre de recherche ou déplacez le marqueur sur la carte. </p>
        <div id="message"></div>
      </div>
    </div>    
      <div class="row" id="map-form">      
        <div class="col-lg-7" id="carte">        	
         <input id="pac-input" class="controls" type="text" placeholder="Rechercher une adresse"/>
         <div id="map"></div>
        </div>
        <div class="col-lg-4" id=formulaire>        	
          <form class="form-group" action="./deplacement.php" method="POST" id="form-deplacement">
          	<input type="hidden" name="id-usager" id="id-usager" value="<?=$_SESSION["id_usager"];?>">            
            <div class="form-group">
              <div class="adr">Adresse :</div><span id="err_adresse"></span>
              <div class="form-group" id="coordonees">
                <div id="nom"><?php echo $lieu->nom;?></div>
                <div id="adresse"><?php echo $lieu->adresse;?></div>
                <?php 
                if($err_adresse != ""){
                  echo "<div class='alert alert-danger'>$err_adresse</div>";
                }
                ?>                
              </div>
              <div class="coordonnees-bd">
                <input type="hidden" name="nom-place" id="nom-bd" value="<?php echo $lieu->nom;?>">
                <input type="hidden" name="adresse-place" id="adresse-bd" value="<?php echo $lieu->adresse;?>">
                <input type="hidden" name="latitude" id="latitude" value="<?php echo $coordonnees->latitude;?>">
                <input type="hidden" name="longitude" id="longitude" value="<?php echo $coordonnees->longitude;?>">
              </div>
              <div class="form-group" id="heure-date">	              
                <label for="date-form" id="label-date"> Date :</label><span id="err_date"></span>
                <input type="text" class="form-control" id="date-visite" name="date-visite" placeholder="2020-12-31" 
					max="<?php echo date('yy-m-d');?>" value="<?php echo $periode->date_visite;?>">
                <?php
                  if($err_date != ""){
                    echo"<div class='alert alert-danger'> $err_date </div>";
                  }
                ?>                
                <label for="heure-arrivee" id="label-depart">Heure d'arrivée :</label><span id="err_heure"></span><span id="err_heure_arrivee"></span>
                <input  type="text" class="form-control" id="heure-arrivee" name="heure-arrivee" data-time-format="H:i" placeholder="14:30" 
					value="<?php echo $periode->heure_arrivee;?>">
                <?php
                  if($err_heure_arrivee != ""){
                    echo"<div class='alert alert-danger'> $err_heure_arrivee</div>";
                  }
                ?>                
                <label for="heure-depart" id="label-depart">Heure de départ :</label><span id="err_heure_depart"></span>
                <input type="text" class="form-control" id="heure-depart" data-time-format="H:i" placeholder="16:15" 
					name="heure-depart" value="<?php echo $periode->heure_depart;?>">                
                <?php
                  if($err_heure_depart != ""){
                    echo"<div class='alert alert-danger'> $err_heure_depart </div>";
                  }
                ?>
              </div>
            </div>
            <input type="button" id="ajoutDeplacement" class="btn btn-primary" value="Enregistrer">            
          </form>
        </div>
    </section>

	<!-- selecteur date et heure -->
	<script>
		$('.saisi-heure').timepicker({
			'minTime': '7:00'
		});
	</script>
	<script>
		var date_max = new Date()
		var calendrier = flatpickr('#date-visite', {maxDate:date_max});
	</script>

  <!-- Footer -->
  <?php require_once('footer.php'); ?>
  
<script>
function clearErrorMessages(arrayError) {	
	var errorLen = arrayError.length;
	for(i=0;i<errorLen;i++){
		document.getElementById(arrayError[i]).innerHTML = "";
	}
}
function validateInputField(inputId, spanId) {	
	var value = document.getElementById(inputId).value;
	if (value == null || value === "") {
		document.getElementById(spanId).innerHTML = "<div class='alert alert-danger'>Ce champ doit contenir une valeur!</div>";
		return false;
	}
	else{
		document.getElementById(spanId).innerHTML = "";		
	}
	return true;
}
function validateHeureVisite(heureArrivee, heureDepart, spanId){
	var heureArrivee = document.getElementById(heureArrivee).value;
	var heureDepart = document.getElementById(heureDepart).value;
	if(heureArrivee>heureDepart){
		document.getElementById(spanId).innerHTML = "<div class='alert alert-warning'>L'heure de départ ne peut être antérieur à l'heure d'arrivée!</div>";
		return false;
	}
	else{
		document.getElementById(spanId).innerHTML = "";		
	}
	return true;
}


function checkRequiredFields() {
	arrayError = ["err_adresse","err_date","err_heure_arrivee", "err_heure_depart"];
	clearErrorMessages(arrayError); 
	// vérifie que l'ancien mot de passe ne soit pas vide
	var field1 = validateInputField("adresse-bd", "err_adresse");
	// vérifie que le mot de passe ne soit pas vide
	var field2 = validateInputField("date-visite", "err_date");
	// vérifie que le nouveau mot de passe ne soit pas vide
	var field3 = validateInputField("heure-arrivee", "err_heure_arrivee");
	// vérifie la longueur du mot de passe
	var field4 = validateInputField("heure-depart", "err_heure_depart");
	// vérifie le mot de passe soir égal au mot de passe confirmé
	var field5 = validateHeureVisite("heure-arrivee", "heure-depart", "err_heure");
	return field1 && field2 && field3 && field4 && field5; 
}
</script>  
<script>
	$(document).ready(function(){
			$("#ajoutDeplacement").click(function(){
					if(checkRequiredFields()){
						var id_usager=$("#id-usager").val();
						var adresse=$("#adresse-bd").val();
						var latitude=$("#latitude").val();
						var longitude=$("#longitude").val();	
						var date_visite=$("#date-visite").val();
						var heure_arrivee=$("#heure-arrivee").val();
						var heure_depart=$("#heure-depart").val();	
						//alert("id usager:"+id_usager);
						//alert(adresse);
						//alert(latitude+' '+longitude);
						//alert(date_visite);
						//alert(heure_arrivee);
						//alert(heure_depart);
						$.ajax({
								url:'deplacement-ajout.php',
								method:'POST',
								data:{
										id_usager:id_usager,
										adresse:adresse,
										latitude:latitude,
										longitude:longitude,
										date_visite:date_visite,
										heure_arrivee:heure_arrivee,
										heure_depart:heure_depart
								},
								success:function(response){
										document.getElementById("message").innerHTML = response;
										window.setTimeout(function(){location.reload()},7000)
								},
								error:function(response){
									document.getElementById("message").innerHTML = '<div class="alert alert-danger elementToFadeInAndOut" role="alert">Une erreur est survenue!</div>';
									window.setTimeout(function(){location.reload()},7000)									
									//alert(response);
								}
						});
					}

			});						
	});

</script>
</body>
</html>


