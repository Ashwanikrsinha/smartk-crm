<section class="table-responsive-lg rounded mt-3">
    <table class="table table-bordered rounded" id="item" style="min-width: 50rem;">
        <thead>
            <tr class="align-middle">
                <th colspan="5">
                    <input type="checkbox" id="check-items" class="form-check-input"
                    {{ isset($quotation) && $quotation->quotationItems->count() > 0 ? 'checked' : '' }}>
                    <span class="ms-2">Quotation {{ isset($quotation) ? "No. {$quotation->quotation_number}" : '' }} Items</span>
                </th>
            </tr>
            <tr>
                <th>Item Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Rate</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($quotation) && $quotation->quotationItems->count() > 0)
                @foreach ($quotation->quotationItems as $quotationItem)
                <tr>
                    <td style="min-width: 18rem;">
                        <select name="products[]" class="form-control" required>
                            <option value="" selected>Choose...</option>
                            @foreach ($products as $key => $product)
                            <option value="{{ $key }}" {{ $key==$quotationItem->product_id ? 'selected' : ''}}>
                                {{ $product }}
                            </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" name="descriptions[]" value="{{ $quotationItem->description }}" maxlength="150"
                            class="form-control">
                    </td>
                    <td>
                        <input type="number" name="quantities[]" value="{{ $quotationItem->quantity }}" step=".01"
                            class="form-control" required>
                    </td>
                    <td style="min-width: 12rem;">
                        <select name="units[]" class="form-control" required>
                            <option value="" selected>Choose...</option>
                            @foreach ($units as $key => $unit)
                            <option value="{{ $key }}" {{ $key==$quotationItem->unit_id ? 'selected' : ''}} >{{ $unit }}
                            </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="rates[]" value="{{ $quotationItem->rate }}" step=".0001" 
                        class="form-control" required>
                    </td>
                </tr>
                @endforeach
            @else
            <tr>
                <td style="min-width: 16rem;">
                    <select name="products[]" class="form-control" required disabled>
                        <option value="" selected>Choose...</option>
                        @foreach ($products as $key => $product)
                        <option value="{{ $key }}">{{ $product }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" name="descriptions[]" class="form-control" maxlength="150" disabled>
                </td>
                <td>
                    <input type="number" step="0.01" name="quantities[]" class="form-control" required disabled>
                </td>
                <td style="min-width: 12rem;">
                    <select name="units[]" class="form-control" required disabled>
                        <option value="" selected>Choose...</option>
                        @foreach ($units as $key => $unit)
                        <option value="{{ $key }}">{{ $unit }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="rates[]" step=".0001" class="form-control" required disabled>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</section>

<footer class="d-flex justify-content-between mt-3 mt-lg-0 mb-4">
    <button class="btn btn-primary btn-sm" id="add-row">
        <span class="feather icon-plus"></span>
    </button>
    <button class="btn btn-danger btn-sm" id="remove-row">
        <i class="feather icon-x"></i>
    </button>
</footer>
