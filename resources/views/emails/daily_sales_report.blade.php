<x-mail::message>
# Daily Sales Report

**Date:** {{ $date }}

<x-mail::panel>
**Total Products Sold:** {{ $soldProducts->sum('quantity') }}  
**Total Revenue:** ${{ number_format($soldProducts->sum('total'), 2) }}
</x-mail::panel>

<x-mail::table>
| Product | Price | Quantity | Revenue |
|:--------|-----:|--------:|-------:|
@foreach ($soldProducts as $product)
| {{ $product['name'] }} | ${{ number_format($product['price'] ?? ($product['total'] / max($product['quantity'],1)), 2) }} | {{ $product['quantity'] }} | ${{ number_format($product['total'], 2) }} |
@endforeach
</x-mail::table>

<x-mail::button :url="url('/admin/orders')" color="primary">
View Orders
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
