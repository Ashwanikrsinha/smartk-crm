<section class="table-responsive-lg rounded mt-3">
    <table class="table table-bordered rounded" id="item" style="min-width: 50rem;">
        <thead class="small">
            <tr>
                <th>Product Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($invoice) && $invoice->invoiceItems->count() > 0)
                @foreach ($invoice->invoiceItems as $invoiceItem)
                <tr>
                    <td style="min-width: 18rem;">
                        <select name="products[]" class="form-control" required>
                            <option value="" selected>Choose...</option>
                            @foreach ($products as $key => $product)
                            <option value="{{ $key }}" {{ $key==$invoiceItem->product_id ? 'selected' : ''}}>
                                {{ $product }}
                            </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" name="descriptions[]" min="0" value="{{ $invoiceItem->description }}" maxlength="150"
                            class="form-control">
                    </td>
                    <td>
                        <input type="number" name="quantities[]" min="0" value="{{ $invoiceItem->quantity }}" step=".01"
                            class="form-control" required>
                    </td>
                    <td style="min-width: 10rem;">
                        <select name="units[]" class="form-control" required>
                            <option value="" selected>Choose...</option>
                            @foreach ($units as $key => $unit)
                            <option value="{{ $key }}" {{ $key==$invoiceItem->unit_id ? 'selected' : ''}} >{{ $unit }}
                            </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="rates[]" min="0" value="{{ $invoiceItem->rate }}" step=".0001" 
                        class="form-control" required>
                    </td>
                    <td>
                        <input type="number" name="amounts[]" min="0" value="{{ $invoiceItem->amount }}" step=".0001" 
                        class="form-control" required>
                    </td>
                </tr>
                @endforeach
            @else
            <tr>
                <td style="min-width: 16rem;">
                    <select name="products[]" class="form-control" required>
                        <option value="" selected>Choose...</option>
                        @foreach ($products as $key => $product)
                        <option value="{{ $key }}">{{ $product }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" name="descriptions[]" class="form-control" maxlength="150">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="quantities[]" class="form-control" required>
                </td>
                <td style="min-width: 10rem;">
                    <select name="units[]" class="form-control" required>
                        <option value="" selected>Choose...</option>
                        @foreach ($units as $key => $unit)
                        <option value="{{ $key }}">{{ $unit }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="rates[]" min="0" step=".0001" class="form-control" required>
                </td>
                <td>
                    <input type="number" name="amounts[]" min="0" step=".0001" class="form-control" required>
                </td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"></td>
                <td colspan="2">
                    <div class="input-group">
                        <span class="input-group-text small">Amount</span>
                        <input type="number" name="amount" step="0.0001" value="{{ $invoice->amount ?? 0 }}"  
                        class="form-control" readonly required>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>

</section>

