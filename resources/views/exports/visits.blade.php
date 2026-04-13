<table>
    <thead>
    	<tr>
            <th>Visit Date</th>
            <th>Executive Name</th>
            <th>Customer Name</th>
            <th>Segment</th>
            <th>Product</th>
            <th>Purpose</th>
       </tr>
    </thead>
    <tbody>
        @foreach($visits as $visit)
        <tr>
            <td>{{ $visit->visit_date->format('d M, Y') }}</td>
            <td>{{ $visit->user->username }}</td>
            <td>{{ $visit->customer->name }}</td>
            <td>{{ $visit->customer->segment->name }}</td>
            <td>{{ $visit->product->name }}</td>
            <td>{{ $visit->purpose->name }}</td>
        </tr>
        @endforeach
   </tbody>
</table>
