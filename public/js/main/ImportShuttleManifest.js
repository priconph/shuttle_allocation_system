const getRouteCode = (cboElement) => {
    $.ajax({
        type: "GET",
        url: "get_route_code",
        // data: "data",
        dataType: "json",
        beforeSend: function(){
        },
        success: function (response) {
            let options = "";
            options += `<option value='' selected>--All--</option>`;
            response.forEach(element => {
                options += `<option value='${element.routes_code}'>${element.routes_destination}</option>`;
            });

            cboElement.append(options);
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}