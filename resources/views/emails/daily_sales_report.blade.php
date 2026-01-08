@component('mail::message')
    <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width:120px; margin-bottom:20px;">

    # Daily Sales Report

    Date: {{ $date }}

    @component('mail::table')
        | Product | Quantity Sold | Total Revenue |
        |---------|---------------|---------------|
        @foreach ($soldProducts as $product)
            | {{ $product['name'] }} | {{ $product['quantity'] }} | ${{ number_format($product['total'], 2) }} |
        @endforeach
    @endcomponent

    @component('mail::button', ['url' => url('/admin/orders')])
        View All Orders
    @endcomponent

    Thanks,<br>
    **Your Store Team**
@endcomponent
