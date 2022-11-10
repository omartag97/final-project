<style>
.zoom {
    transition: transform .2s;
    width: 200px;
    height: 200px;
}
table, th, td {
  border:1px solid black;
}
.zoom:hover {
  -ms-transform: scale(1.1); /* IE 9 */
  -webkit-transform: scale(1.1); /* Safari 1-8 */
    transform: scale(1.1);
}
</style>
<x-mail::message>
<h1>welcome {{$userName}} in Talabat</h1>
<body class="bg-light">
    <div class="d-flex justify-content-center">
        <img class="ax-center zoom" src="https://www.order-catch.com/wp-content/uploads/2019/10/Talabat-logo.png" />
    </div>
    <div class="container">
    <div class="card p-6 p-lg-10 space-y-4">
        <h1 class="h3 fw-700">
        Your order information :
    </h1>
    <h1>Restaurant : {{$restaurantName}}</h1>
    <table style="width:100%">
        <tr>
        <th>Product Name</th>
        <th> Price</th>
        </tr>
        @for ($i = 0 ; $i < $numberOfProducts ; $i++)
            <tr>
                <td>{{$nameData[$i]}}</td>
                <td>{{$priceData[$i]}}</td>
            </tr>
            @endfor
    </table>
    <br>
        <table style="width:100%">
            <tr>
            <th>delivery fee </th>
            <th> Payment type</th>
            <th> addition request</th>
            </tr>
                <tr>
                    <td>{{$deliveryFee}}</td>
                    <td>{{$paymentType}}</td>
                    <td>{{$additionRequest}}</td>
                </tr>
        </table>
</body>

<x-mail::button :url="''">
Visit Wrbsite
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
