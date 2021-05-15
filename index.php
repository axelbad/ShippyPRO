<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ShippyPRO</title>

    <!--
    Neaty HTML Template
    http://www.templatemo.com/tm-501-neaty
    -->

    <!-- load stylesheets -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400"> <!-- Google web font "Open Sans" -->
    <link rel="stylesheet" href="css/bootstrap.min.css"> <!-- Bootstrap style -->
    <link rel="stylesheet" href="css/templatemo-style.css"> <!-- Templatemo style -->

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />

    <link rel="stylesheet" href="css/toastr.min.css" />
	<script src="js/toastr.min.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
          <![endif]-->
    <style>
        #map {
            height: 600px;
        }
    </style>
<?php
    require 'data.php';
?>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="tm-left-right-container">
                <!-- Left column: logo and menu -->
                <div class="tm-blue-bg tm-left-column">
                    <div class="tm-logo-div text-xs-center">
                        <img src="img/shippypro.svg" alt="Logo">
                    </div>
                    <div class="row text-xs-center">
                        <form>
                            <div class="form-group">
                                <label for="departure">Departure</label><br>
                                <select class="" name="departure" id="departure">
                                    <option value="">&nbsp;</option>
<?php
                                    foreach ($airports as $airport) 
                                    {
?>
                                        <option value="<?php echo $airport['id']; ?>" data-lat="<?php echo $airport['lat']; ?>" data-lng="<?php echo $airport['lng']; ?>">
                                            <?php echo $airport['name']; ?>
                                        </option>
<?php
                                    }
?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="arrival">Arrival</label><br>
                                <select class="" name="arrival" id="arrival">
                                    <option value="">&nbsp;</option>
<?php
                                    foreach ($airports as $airport) 
                                    {
?>
                                        <option value="<?php echo $airport['id']; ?>" data-lat="<?php echo $airport['lat']; ?>" data-lng="<?php echo $airport['lng']; ?>">
                                            <?php echo $airport['name']; ?>
                                        </option>
<?php
                                    }
?>
                                </select>
                            </div>
                            <button type="button" class="btn btn-success mb-2" id="get_route">Get Route</button>

                            
                        </form>

                    </div>
                    <nav class="tm-main-nav" style="display:none; color:black;" id="div_details">
                        <div class="card" style="width: 18rem;">
                            <div class="card-header" id="title">
                                <b>Travel Details</b>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item" id="text"></li>
                            </ul>
                        </div>
                    </nav>   
                            
                </div> <!-- Left column: logo and menu -->

                <!-- Right column: content -->
                <div class="tm-right-column">


                    <div class="tm-content-div">
                        <!-- Welcome section -->
                        <section id="welcome" class="tm-section">
                            <header>
                                <h2 class="tm-blue-text tm-welcome-title tm-margin-b-45">Welcome to my ShippyPRO test</h2>
                            </header>
                            <div id="map"></div>
                        </section>
                        <!-- About section -->

                    </div>

                </div> <!-- Right column: content -->
            </div>
        </div> <!-- row -->
    </div> <!-- container -->

    <!-- load JS files -->
    <script src="js/jquery-1.11.3.min.js"></script> <!-- jQuery (https://jquery.com/download/) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <script src="js/toastr.min.js"></script>
    <script>
        $(document).ready(function() {

            // Styling the selectbox with selext2 plugin
            $('#departure').select2({
                placeholder: "Select a departure",
                allowClear: true,
                width: '280px'
            });

            $('#arrival').select2({
                placeholder: "Select an arrival",
                allowClear: true,
                width: '280px'
            });

            // Leaflet setup
            var map = L.map('map').setView([51.505, -0.09], 7);

            main_layer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '<span style="color:#000;">&#169; OpenStreetMap contributors.</span>',
                minZoom: 5,
                maxZoom: 17,
                maxNativeZoom: 20,
                noWrap: true,
            }).addTo(map);


            // Move the map to the departure airports
            $( "#departure" ).change(function() 
            {
                let lat = $("#departure").find(":selected").data("lat");
                let lng = $("#departure").find(":selected").data("lng");

                if (typeof (lat)!="undefined")
                {
                    map.panTo(new L.LatLng(lat, lng));
                }

            });

            // Move the map to the arrival airports
            $( "#arrival" ).change(function() 
            {
                let lat = $("#arrival").find(":selected").data("lat");
                let lng = $("#arrival").find(":selected").data("lng");

                if (typeof (lat)!="undefined")
                {
                    map.panTo(new L.LatLng(lat, lng));
                }
            });

<?php 
            // Pre-populate the map with marker from the airports array
            foreach($airports as $airport)
            {
?>
                L.marker([<?php echo $airport['lat']; ?>, <?php echo $airport['lng']; ?>]).addTo(map);
<?php
            }
?>
            // Fired when user click on get route button
            var layerGroup = L.layerGroup().addTo(map);
            var poly = {};
            $( "#get_route" ).click(function() {

                let id_departure = $("#departure").val();
                let id_arrival = $("#arrival").val();

                if (id_departure=="")
                {
                    toastr.error("Departure can't be empty at least you're already in flight!", "Error!");
                }
                else if (id_arrival=="")
                {
                    toastr.error("Arrival can't be empty or you never land!", "Error!");
                }
                else
                {
                    $.ajax({
                        url: 'get_route.php',
                        data: {
                            id_departure: id_departure,
                            id_arrival: id_arrival,
                        },
                        type: 'post',
                        success: function (response) 
                        {
                            var resp = JSON.parse(response);

                            if (resp['status']=="ok") 
                            {
                                if(resp['final_path'])
                                {
                                    $("#div_details").hide();

                                    // Delete the previous draw route if found
                                    if (typeof layerGroup != "undefined")
                                    {
                                        layerGroup.clearLayers();
                                    }

                                    text = '';
                                    total_travel = 0;
                                    i = 0;
                                    for (var final_path of resp['final_path']) 
                                    {
                                        // Draw the routes with polylines
                                        var loc_from = final_path['from'].split(',');
                                        var lat_from = loc_from[0];
                                        var lng_from = loc_from[1];
                                        var p_from = new L.LatLng(lat_from,lng_from);
                                        
                                        var loc_to = final_path['to'].split(',');
                                        var lat_to= loc_to[0];
                                        var lng_to = loc_to[1];
                                        var p_to = new L.LatLng(lat_to,lng_to);

                                        var pointList = [p_from, p_to];

                                        poly[i] = new L.Polyline(pointList, {
                                            color: 'red',
                                            weight: 3,
                                            opacity: 0.5,
                                            smoothFactor: 1
                                        });

                                        layerGroup.addLayer(poly[i]);
                                        i++;

                                        // Try to zoom in/out to fit the entire route on map
                                        // anyway could be improved 
                                        map.fitBounds([
                                            [p_from],
                                            [p_to]
                                        ]);
                                        
                                        total_travel += parseFloat(final_path[2]);

                                        text += "From: " + final_path['from_city'] + "<br>To: " + final_path['to_city'] + "<br>Price is: £" + final_path[2] + "<br><hr>";
                                    }

                                    text += "<b>Total: £" + total_travel + "</b>";

                                    $("#div_details").show();
                                    $("#text").html(text);
                                }
                                else 
                                {
                                    $("#div_details").hide();
                                    if (typeof layerGroup != "undefined")
                                    {
                                        layerGroup.clearLayers();
                                    }

                                    toastr.error("No route found!");
                                }
                            }
                            else 
                            {
                                toastr.error(resp['msg']);
                            }
                            
                        }
                    });
                }
            });

        });
    </script>
</body>

</html>