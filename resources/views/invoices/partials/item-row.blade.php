<tr>
    <td class="text-center align-middle">
        <span class="text-muted small">{{ $idx + 1 }}</span>
    </td>

    {{-- Category --}}
    <td>
        <select name="categories[{{ $idx }}]" class="form-control form-control-sm category-select">
            <option value="">Category...</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ isset($item) && $item->product->category_id == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </td>

    {{-- Product --}}
    <td>
        <select name="products[{{ $idx }}]" class="form-control form-control-sm product-select" required>
            <option value="">Select product...</option>
            @if (isset($item))
                <option value="{{ $item->product_id }}" selected data-mrp="{{ $item->product->price }}"
                    data-rate="{{ $item->price }}">
                    {{ $item->product->name }}
                </option>
            @endif
        </select>
    </td>

    {{-- MRP --}}
    <td>
        <input type="number" step="0.01" name="mrps[{{ $idx }}]"
            class="form-control form-control-sm mrp-input" value="{{ isset($item) ? $item->product->mrp : '' }}"
            placeholder="0.00" readonly>
    </td>

    {{-- Qty --}}
    <td>
        <input type="number" min="1" name="quantities[{{ $idx }}]"
            class="form-control form-control-sm qty-input" value="{{ isset($item) ? $item->quantity : 1 }}" required>
    </td>

    {{-- Discount % --}}
    <td>
        <input type="number" step="0.01" min="0" max="100" name="discounts[{{ $idx }}]"
            class="form-control form-control-sm discount-input"
            value="{{ isset($item) ? (isset($item->discount) ? $item->discount : 0) : 0 }}" placeholder="0">
    </td>

    {{-- Net Sale Price --}}
    <td>
        <input type="number" step="0.01" name="rates[{{ $idx }}]"
            class="form-control form-control-sm rate-input" value="{{ isset($item) ? $item->rate : '' }}"
            placeholder="0.00" required>
    </td>

    {{-- Total Amount --}}
    <td>
        <input type="hidden" name="amounts[{{ $idx }}]" class="amount-input"
            value="{{ isset($item) ? $item->amount : 0 }}">
        <span class="amount-display fw-bold">
            ₹{{ isset($item) ? number_format($item->amount, 2) : '0.00' }}
        </span>
    </td>

    {{-- Also need descriptions for DB --}}
    <input type="hidden" name="descriptions[{{ $idx }}]"
        value="{{ isset($item) ? $item->description : '' }}">
    <input type="hidden" name="units[{{ $idx }}]" value="{{ isset($item) ? $item->unit_id : 1 }}">

    {{-- Remove row --}}
    <td class="text-center align-middle">
        <button type="button" class="btn btn-sm btn-link text-danger remove-item-row p-0">
            <i class="feather icon-minus-circle"></i>
        </button>
    </td>
</tr>
