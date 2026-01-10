@component('mail::message')
# Low Stock Alert

The following product is running low on stock:

**Product:** {{ $product->name }}
**Remaining Quantity:** {{ $product->stock_quantity }}

Please take action to restock.

Thanks,
{{ config('app.name') }}
@endcomponent
