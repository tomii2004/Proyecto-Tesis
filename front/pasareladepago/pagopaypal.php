<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://www.paypal.com/sdk/js?client-id=ARoAjaFuYO9Alev6wces5IYHIJEcwaBhu_F7EQrTukbzeGy-tYoJVc1f4faFz3KUjXvHKOYRqCIjstMM&buyer-country=US&currency=USD&components=buttons&enable-funding=venmo,paylater,card" data-sdk-integration-source="developer-studio"></script>
    <script src="app.js"></script>
</head>
<body>
    <div id="paypal-button-container"></div>

    <script>
        paypal.Buttons({
            style:{
                color: 'blue',
                shape: 'pill',
                label: 'pay'
            },
            createOrder: function(data,actions){
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: 200,
                        }
                    }]
                })
            },
            onApprove: function(data,actions){
                actions.order.capture().then(function(detalles){
                    window.location.href="completado.php"
                });
            },
            onCancel: function (data){
                alert("Pago Cancelado");
                console.log(data);
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>